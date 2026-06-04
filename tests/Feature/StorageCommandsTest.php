<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageCommandsTest extends TestCase
{
    public function test_storage_check_command_verifies_configured_disks(): void
    {
        Storage::fake('public');
        Storage::fake('enterprise_backups');

        $this->artisan('storage:check')
            ->expectsOutputToContain('public: ok')
            ->expectsOutputToContain('enterprise_backups: ok')
            ->assertSuccessful();
    }

    public function test_backup_upload_command_uploads_and_verifies_gzipped_sql(): void
    {
        Storage::fake('enterprise_backups');

        $path = tempnam(sys_get_temp_dir(), 'madeena-cp-test-backup-');
        $this->assertIsString($path);

        file_put_contents($path, gzencode("CREATE TABLE `users` (`id` bigint unsigned not null);\n"));

        try {
            $this->artisan('backup:upload', [
                'path' => $path,
                '--remote-name' => 'madeena_cp-test.sql.gz',
                '--retention-days' => '14',
            ])
                ->expectsOutputToContain('Local gzip and SQL signature checks passed')
                ->expectsOutputToContain('Local backup digest computed with bounded memory usage')
                ->expectsOutputToContain('Integrity manifest uploaded and verified')
                ->assertSuccessful();

            $this->assertTrue(Storage::disk('enterprise_backups')->exists('madeena_cp-test.sql.gz'));
            $this->assertTrue(Storage::disk('enterprise_backups')->exists('madeena_cp-test.sql.gz.sha256'));
        } finally {
            @unlink($path);
        }
    }
}
