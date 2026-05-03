<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Sabre\DAV\Client;

class CheckWebDavStorage extends Command
{
    protected $signature = 'storage:webdav-check {--disk=* : Disk name to check}';

    protected $description = 'Write, read, verify, and delete checksum probes on WebDAV-backed storage disks.';

    public function handle(): int
    {
        $disks = $this->option('disk') ?: ['public', 'enterprise_backups'];
        $failed = false;

        foreach ($disks as $diskName) {
            $diskName = (string) $diskName;
            $disk = Storage::disk($diskName);
            $path = sprintf('webdav-check-%s.txt', bin2hex(random_bytes(8)));
            $payload = sprintf(
                "madeena-cp-webdav-check\n%s\n%s\n",
                $diskName,
                hash('sha256', random_bytes(32)),
            );

            $this->line("Checking {$diskName}...");

            if (! $this->verifyWebDavConnectivity($diskName)) {
                $failed = true;

                continue;
            }

            try {
                if (! $disk->put($path, $payload)) {
                    throw new \RuntimeException('write returned false');
                }

                $readBack = $disk->get($path);

                if ($readBack !== $payload) {
                    throw new \RuntimeException('readback checksum mismatch');
                }

                if (! $disk->delete($path)) {
                    throw new \RuntimeException('delete returned false');
                }

                $this->info("{$diskName}: ok");
            } catch (\Throwable $exception) {
                $failed = true;
                $errorMessage = $exception->getMessage();

                if ($exception->getPrevious()) {
                    $errorMessage .= ' (Caused by: '.$exception->getPrevious()->getMessage().')';
                }

                $this->error("{$diskName} failure: {$errorMessage}");

                if ($this->getOutput()->isVerbose()) {
                    $this->error('Exception: '.get_class($exception));

                    if ($exception->getPrevious()) {
                        $this->error('Previous Exception: '.get_class($exception->getPrevious()));
                    }

                    $this->line($exception->getTraceAsString());
                }

                try {
                    $disk->delete($path);
                } catch (\Throwable) {
                    //
                }
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function verifyWebDavConnectivity(string $diskName): bool
    {
        try {
            $config = config("filesystems.disks.{$diskName}");

            if (($config['driver'] ?? '') !== 'webdav') {
                return true;
            }

            $baseUri = rtrim((string) ($config['base_uri'] ?? ''), '/').'/';
            $this->line("  Verifying connectivity to {$baseUri}...");

            $client = new Client([
                'baseUri' => $baseUri,
                'userName' => (string) ($config['username'] ?? ''),
                'password' => (string) ($config['password'] ?? ''),
            ]);

            $timeout = (int) ($config['timeout'] ?? 10);
            $client->addCurlSetting(CURLOPT_CONNECTTIMEOUT, min($timeout, 5));
            $client->addCurlSetting(CURLOPT_TIMEOUT, $timeout);

            if (($config['verify_ssl'] ?? true) === false) {
                $client->addCurlSetting(CURLOPT_SSL_VERIFYHOST, 0);
                $client->addCurlSetting(CURLOPT_SSL_VERIFYPEER, false);
            }

            $client->propFind('', ['{DAV:}resourcetype'], 0);
            $this->line('  Connectivity: ok');

            return true;
        } catch (\Throwable $exception) {
            $this->error('  Connectivity FAILED: '.$exception->getMessage());

            if ($this->getOutput()->isVerbose()) {
                $this->line($exception->getTraceAsString());
            }

            return false;
        }
    }
}
