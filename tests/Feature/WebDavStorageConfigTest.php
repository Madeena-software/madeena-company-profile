<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class WebDavStorageConfigTest extends TestCase
{
    public function test_storage_disks_keep_local_defaults(): void
    {
        $public = config('filesystems.disks.public');
        $backups = config('filesystems.disks.enterprise_backups');

        $this->assertSame('local', $public['driver']);
        $this->assertIsString($public['root']);
        $this->assertNotSame('', $public['root']);
        $this->assertSame('local', $backups['driver']);
        $this->assertIsString($backups['root']);
        $this->assertNotSame('', $backups['root']);
    }

    public function test_webdav_configuration_keys_are_present(): void
    {
        $public = config('filesystems.disks.public');
        $backups = config('filesystems.disks.enterprise_backups');

        foreach (['base_uri', 'username', 'password', 'timeout', 'verify_ssl', 'low_speed_limit', 'low_speed_time', 'tcp_keepalive', 'require_server_checksum'] as $key) {
            $this->assertArrayHasKey($key, $public);
            $this->assertArrayHasKey($key, $backups);
        }
    }
}
