<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class StorageConfigTest extends TestCase
{
    public function test_storage_disks_use_s3_driver(): void
    {
        $public = config('filesystems.disks.public');
        $backups = config('filesystems.disks.enterprise_backups');

        $this->assertSame('s3', $public['driver']);
        $this->assertSame('s3', $backups['driver']);
    }

    public function test_s3_configuration_keys_are_present(): void
    {
        $public = config('filesystems.disks.public');
        $backups = config('filesystems.disks.enterprise_backups');

        foreach (['key', 'secret', 'region', 'bucket', 'endpoint', 'use_path_style_endpoint'] as $key) {
            $this->assertArrayHasKey($key, $public, "Public disk missing key: {$key}");
            $this->assertArrayHasKey($key, $backups, "Backup disk missing key: {$key}");
        }
    }

    public function test_enterprise_backups_disk_has_backups_root(): void
    {
        $backups = config('filesystems.disks.enterprise_backups');

        $this->assertSame('backups', $backups['root']);
    }
}
