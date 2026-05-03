<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\StorageAttributes;
use Sabre\DAV\Client as SabreClient;

class UploadDatabaseBackupToWebDav extends Command
{
    protected $signature = 'backup:webdav-upload
        {path : Local gzipped SQL backup file to upload}
        {--disk=enterprise_backups : Destination storage disk}
        {--remote-name= : Remote object name}
        {--max-attempts=3 : Upload attempts before failing}
        {--retry-delay-ms=750 : Base delay between retries in milliseconds}
        {--retention-days=14 : Delete matching remote backups older than this many days}
        {--prefix=madeena_cp- : Remote backup filename prefix used for retention}';

    protected $description = 'Upload a verified database backup to a WebDAV-backed Laravel disk.';

    public function handle(): int
    {
        $localPath = (string) $this->argument('path');

        if (! is_file($localPath) || ! is_readable($localPath)) {
            $this->error("Backup file is not readable: {$localPath}");

            return self::FAILURE;
        }

        if (filesize($localPath) === 0) {
            $this->error("Backup file is empty: {$localPath}");

            return self::FAILURE;
        }

        if (! $this->assertGzipSqlBackup($localPath)) {
            return self::FAILURE;
        }

        $diskName = (string) $this->option('disk');
        $remoteName = (string) ($this->option('remote-name') ?: basename($localPath));
        $disk = Storage::disk($diskName);

        try {
            $localMetadata = $this->buildLocalMetadata($localPath);
        } catch (\Throwable $exception) {
            $this->error("Unable to prepare local backup metadata: {$exception->getMessage()}");

            return self::FAILURE;
        }

        if (! $this->uploadWithRetries(
            disk: $disk,
            localPath: $localPath,
            remoteName: $remoteName,
            expectedSize: $localMetadata['size'],
            maxAttempts: max(1, (int) $this->option('max-attempts')),
            baseRetryDelayMs: max(0, (int) $this->option('retry-delay-ms')),
        )) {
            return self::FAILURE;
        }

        if (! $this->verifyRemoteIntegrity($diskName, $remoteName, $localMetadata)) {
            return self::FAILURE;
        }

        if (! $this->writeAndVerifyIntegrityManifest($disk, $remoteName, $localMetadata)) {
            return self::FAILURE;
        }

        $deleted = $this->pruneOldBackups(
            diskName: $diskName,
            prefix: (string) $this->option('prefix'),
            retentionDays: max(0, (int) $this->option('retention-days')),
        );

        $this->info(sprintf(
            'Uploaded %s to %s:%s (%s, pruned %d old backup%s).',
            basename($localPath),
            $diskName,
            $remoteName,
            $this->formatBytes((int) filesize($localPath)),
            $deleted,
            $deleted === 1 ? '' : 's',
        ));

        return self::SUCCESS;
    }

    /**
     * @return array{size:int,sha256:string,sha1:string}
     */
    private function buildLocalMetadata(string $path): array
    {
        $stream = fopen($path, 'rb');

        if ($stream === false) {
            throw new \RuntimeException("Unable to open backup file for hashing: {$path}");
        }

        $sha256 = hash_init('sha256');
        $sha1 = hash_init('sha1');

        while (! feof($stream)) {
            $chunk = fread($stream, 1024 * 1024);

            if ($chunk === false) {
                fclose($stream);

                throw new \RuntimeException("Unable to read backup file while hashing: {$path}");
            }

            if ($chunk !== '') {
                hash_update($sha256, $chunk);
                hash_update($sha1, $chunk);
            }
        }

        fclose($stream);

        $this->info('Local backup digest computed with bounded memory usage.');

        return [
            'size' => (int) filesize($path),
            'sha256' => hash_final($sha256),
            'sha1' => hash_final($sha1),
        ];
    }

    private function assertGzipSqlBackup(string $path): bool
    {
        $handle = gzopen($path, 'rb');

        if ($handle === false) {
            $this->error('Backup is not a readable gzip file.');

            return false;
        }

        $createTableCount = 0;

        while (! gzeof($handle)) {
            $line = gzgets($handle);

            if ($line === false) {
                break;
            }

            if (str_starts_with($line, 'CREATE TABLE')) {
                $createTableCount++;
            }
        }

        gzclose($handle);

        if ($createTableCount === 0) {
            $this->error('Backup gzip is readable, but no CREATE TABLE statements were found.');

            return false;
        }

        $this->info("Local gzip and SQL signature checks passed ({$createTableCount} tables).");

        return true;
    }

    private function uploadWithRetries(
        Filesystem $disk,
        string $localPath,
        string $remoteName,
        int $expectedSize,
        int $maxAttempts,
        int $baseRetryDelayMs,
    ): bool {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $stream = fopen($localPath, 'rb');

            if ($stream === false) {
                $this->error("Unable to open backup file: {$localPath}");

                return false;
            }

            try {
                if (! $disk->put($remoteName, $stream)) {
                    throw new \RuntimeException('upload returned false');
                }

                $remoteSize = (int) $disk->size($remoteName);

                if ($remoteSize !== $expectedSize) {
                    throw new \RuntimeException(sprintf(
                        'remote size mismatch after upload (expected %d, got %d)',
                        $expectedSize,
                        $remoteSize,
                    ));
                }

                $this->info(sprintf('Upload attempt %d/%d succeeded.', $attempt, $maxAttempts));

                return true;
            } catch (\Throwable $exception) {
                $this->warn(sprintf('Upload attempt %d/%d failed: %s', $attempt, $maxAttempts, $exception->getMessage()));

                try {
                    $disk->delete($remoteName);
                } catch (\Throwable) {
                    // Best-effort cleanup so retries do not keep partial uploads.
                }

                if ($attempt === $maxAttempts) {
                    $this->error('Upload failed after all retry attempts.');

                    return false;
                }

                $delayMs = $baseRetryDelayMs * $attempt;
                if ($delayMs > 0) {
                    usleep($delayMs * 1000);
                }
            } finally {
                fclose($stream);
            }
        }

        $this->error('Upload failed unexpectedly before retry loop completion.');

        return false;
    }

    /**
     * @param  array{size:int,sha256:string,sha1:string}  $localMetadata
     */
    private function verifyRemoteIntegrity(string $diskName, string $remoteName, array $localMetadata): bool
    {
        $disk = Storage::disk($diskName);

        try {
            $remoteSize = (int) $disk->size($remoteName);
        } catch (\Throwable $exception) {
            $this->error("Unable to retrieve remote size: {$exception->getMessage()}");

            return false;
        }

        if ($remoteSize !== $localMetadata['size']) {
            $this->error(sprintf(
                'Remote size mismatch: expected %d bytes, got %d bytes.',
                $localMetadata['size'],
                $remoteSize,
            ));

            return false;
        }

        $webDavChecksums = $this->readWebDavChecksums($diskName, $remoteName);

        if ($webDavChecksums !== null && isset($webDavChecksums['sha1'])) {
            $serverSha1 = strtolower($webDavChecksums['sha1']);

            if ($serverSha1 !== strtolower($localMetadata['sha1'])) {
                $this->error('Server-side WebDAV checksum mismatch (SHA1).');

                return false;
            }

            $this->info('Remote metadata and server checksum verified (no full-file readback required).');

            return true;
        }

        $requireServerChecksum = (bool) config("filesystems.disks.{$diskName}.require_server_checksum", false);

        if ($requireServerChecksum) {
            $this->error('Server-side checksum is required but not available from WebDAV metadata.');

            return false;
        }

        try {
            $remoteSha256 = $this->hashRemoteStream($disk, $remoteName);
        } catch (\Throwable $exception) {
            $this->error("Unable to verify remote backup readback: {$exception->getMessage()}");

            return false;
        }

        if (! hash_equals(strtolower($localMetadata['sha256']), strtolower($remoteSha256))) {
            $this->error('Remote backup readback checksum mismatch (SHA256).');

            return false;
        }

        $this->info('Remote full-stream SHA256 readback verified with bounded memory usage.');

        return true;
    }

    private function hashRemoteStream(Filesystem $disk, string $remoteName): string
    {
        $stream = $disk->readStream($remoteName);

        if (! is_resource($stream)) {
            throw new \RuntimeException("Unable to open remote backup stream: {$remoteName}");
        }

        $sha256 = hash_init('sha256');

        try {
            while (! feof($stream)) {
                $chunk = fread($stream, 1024 * 1024);

                if ($chunk === false) {
                    throw new \RuntimeException("Unable to read remote backup stream: {$remoteName}");
                }

                if ($chunk !== '') {
                    hash_update($sha256, $chunk);
                }
            }
        } finally {
            fclose($stream);
        }

        return hash_final($sha256);
    }

    /**
     * @param  array{size:int,sha256:string,sha1:string}  $localMetadata
     */
    private function writeAndVerifyIntegrityManifest(Filesystem $disk, string $remoteName, array $localMetadata): bool
    {
        $manifestPath = $remoteName.'.sha256';

        try {
            $manifest = json_encode([
                'algorithm' => 'sha256',
                'sha256' => $localMetadata['sha256'],
                'sha1' => $localMetadata['sha1'],
                'size' => $localMetadata['size'],
                'generated_at_utc' => now('UTC')->toIso8601String(),
                'file' => $remoteName,
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)."\n";
        } catch (\Throwable $exception) {
            $this->error("Unable to encode integrity manifest: {$exception->getMessage()}");

            return false;
        }

        try {
            if (! $disk->put($manifestPath, $manifest)) {
                throw new \RuntimeException('manifest upload returned false');
            }

            $readBack = $disk->get($manifestPath);

            if ($readBack !== $manifest) {
                throw new \RuntimeException('manifest readback mismatch');
            }
        } catch (\Throwable $exception) {
            $this->error("Integrity manifest write/verify failed: {$exception->getMessage()}");

            return false;
        }

        $this->info('Integrity manifest uploaded and verified.');

        return true;
    }

    /**
     * @return array<string, string>|null
     */
    private function readWebDavChecksums(string $diskName, string $remoteName): ?array
    {
        $diskConfig = (array) config("filesystems.disks.{$diskName}", []);

        if (($diskConfig['driver'] ?? null) !== 'webdav') {
            return null;
        }

        $baseUri = rtrim((string) ($diskConfig['base_uri'] ?? ''), '/').'/';
        $username = (string) ($diskConfig['username'] ?? '');
        $password = (string) ($diskConfig['password'] ?? '');
        $root = trim((string) ($diskConfig['root'] ?? ''), '/');

        if ($baseUri === '/' || $username === '' || $password === '') {
            return null;
        }

        $client = new SabreClient([
            'baseUri' => $baseUri,
            'userName' => $username,
            'password' => $password,
            'authType' => SabreClient::AUTH_BASIC,
        ]);

        $timeout = (int) ($diskConfig['timeout'] ?? 30);

        if ($timeout > 0) {
            $client->addCurlSetting(CURLOPT_CONNECTTIMEOUT, min($timeout, 10));
            $client->addCurlSetting(CURLOPT_TIMEOUT, $timeout);
        }

        if (($diskConfig['verify_ssl'] ?? true) === false) {
            $client->addCurlSetting(CURLOPT_SSL_VERIFYHOST, 0);
            $client->addCurlSetting(CURLOPT_SSL_VERIFYPEER, false);
        }

        $path = ltrim($remoteName, '/');
        $webDavPath = trim($root.'/'.$path, '/');
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $webDavPath)));

        try {
            $properties = $client->propFind($encodedPath, ['{http://owncloud.org/ns}checksums']);
        } catch (\Throwable) {
            return null;
        }

        $rawChecksums = (string) ($properties['{http://owncloud.org/ns}checksums'] ?? '');

        if ($rawChecksums === '') {
            return null;
        }

        $checksums = [];

        foreach (preg_split('/\s+/', trim($rawChecksums)) ?: [] as $token) {
            if (! str_contains($token, ':')) {
                continue;
            }

            [$algorithm, $value] = explode(':', $token, 2);
            $algorithm = strtolower(trim($algorithm));
            $value = trim($value);

            if ($algorithm === '' || $value === '') {
                continue;
            }

            $checksums[$algorithm] = $value;
        }

        return $checksums === [] ? null : $checksums;
    }

    private function deleteRelatedIntegrityManifest(Filesystem $disk, string $path): void
    {
        try {
            $disk->delete($path.'.sha256');
        } catch (\Throwable) {
            // Best-effort cleanup during retention pruning.
        }
    }

    private function pruneOldBackups(string $diskName, string $prefix, int $retentionDays): int
    {
        if ($retentionDays === 0) {
            return 0;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($diskName);
        $cutoff = now()->subDays($retentionDays)->getTimestamp();
        $deleted = 0;

        /** @var StorageAttributes $file */
        foreach ($disk->listContents('', false) as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $path = $file->path();
            $name = basename($path);

            if (! str_starts_with($name, $prefix) || ! str_ends_with($name, '.sql.gz')) {
                continue;
            }

            $lastModified = $file->lastModified();
            if ($lastModified === null || $lastModified >= $cutoff) {
                continue;
            }

            if ($disk->delete($path)) {
                $this->deleteRelatedIntegrityManifest($disk, $path);
                $deleted++;
            }
        }

        return $deleted;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = max($bytes, 0);

        foreach ($units as $unit) {
            if ($size < 1024 || $unit === 'GB') {
                return round($size, 2).' '.$unit;
            }

            $size /= 1024;
        }

        return $bytes.' B';
    }
}
