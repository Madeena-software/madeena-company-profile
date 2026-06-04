<?php

declare(strict_types=1);

# ═══════════════════════════════════════════════════════════════════════════════
# Madeena CP — Laravel S3 Storage Integration Test
# ═══════════════════════════════════════════════════════════════════════════════
# Tests the full lifecycle: bucket auto-create, put, get, overwrite, delete
# for both 'public' and 'enterprise_backups' disks via Laravel Storage facade.
#
# Usage: php artisan tinker --execute="require 'scratch/test-s3-laravel.php';"
#    or: php scratch/test-s3-laravel.php  (standalone with .env.local bootstrap)
# ═══════════════════════════════════════════════════════════════════════════════

echo "================================================================\n";
echo "  MADEENA CP — LARAVEL S3 STORAGE INTEGRATION TEST              \n";
echo "================================================================\n\n";

# ── Bootstrap Laravel ────────────────────────────────────────────────────────
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (! file_exists($autoloadPath)) {
    echo "❌ Composer autoloader not found. Run 'composer install' first.\n";
    exit(1);
}
require $autoloadPath;

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;

# ── Resolve config ───────────────────────────────────────────────────────────
$publicConfig = config('filesystems.disks.public');
$backupsConfig = config('filesystems.disks.enterprise_backups');

echo "[*] Config loaded:\n";
echo "    public disk    → driver={$publicConfig['driver']}, bucket={$publicConfig['bucket']}, endpoint={$publicConfig['endpoint']}\n";
echo "    backups disk   → driver={$backupsConfig['driver']}, bucket={$backupsConfig['bucket']}, root={$backupsConfig['root']}\n\n";

if ($publicConfig['driver'] !== 's3') {
    echo "❌ Public disk is not using S3 driver. Got: {$publicConfig['driver']}\n";
    exit(1);
}

# ── Ensure bucket exists ─────────────────────────────────────────────────────
echo "[*] Phase 0: Ensuring bucket exists...\n";
try {
    $s3 = new S3Client([
        'version' => 'latest',
        'region' => $publicConfig['region'] ?? 'us-east-1',
        'endpoint' => $publicConfig['endpoint'],
        'use_path_style_endpoint' => true,
        'credentials' => [
            'key' => $publicConfig['key'],
            'secret' => $publicConfig['secret'],
        ],
        'http' => ['timeout' => 30, 'connect_timeout' => 10],
    ]);

    $bucket = $publicConfig['bucket'];

    if (! $s3->doesBucketExist($bucket)) {
        echo "    ℹ️  Bucket '{$bucket}' does not exist. Creating...\n";
        $s3->createBucket(['Bucket' => $bucket]);
        echo "    ✅ Bucket created.\n";
    } else {
        echo "    ✅ Bucket '{$bucket}' exists.\n";
    }
} catch (\Throwable $e) {
    echo "❌ Bucket check/create failed: {$e->getMessage()}\n";
    exit(1);
}

# ── Test disks ───────────────────────────────────────────────────────────────
$disks = ['public', 'enterprise_backups'];
$pass = 0;
$fail = 0;

foreach ($disks as $diskName) {
    echo "\n--- Testing disk: {$diskName} ---\n";
    $disk = Storage::disk($diskName);
    $probeKey = "integration-test-" . bin2hex(random_bytes(8)) . ".txt";
    $payload1 = "Madeena CP integration test payload v1 — " . now()->toIso8601String();
    $payload2 = "Madeena CP integration test payload v2 — OVERWRITE — " . now()->toIso8601String();

    try {
        # 1. PUT
        echo "[*] 1. Writing probe file '{$probeKey}'...\n";
        $disk->put($probeKey, $payload1);
        echo "    ✅ Write succeeded.\n";

        # 2. EXISTS
        echo "[*] 2. Checking file exists...\n";
        if (! $disk->exists($probeKey)) {
            throw new RuntimeException("File does not exist after write");
        }
        echo "    ✅ File exists.\n";

        # 3. GET (readback)
        echo "[*] 3. Reading back and verifying content...\n";
        $readBack = $disk->get($probeKey);
        if ($readBack !== $payload1) {
            throw new RuntimeException("Readback mismatch: expected " . strlen($payload1) . " bytes, got " . strlen($readBack ?? '') . " bytes");
        }
        echo "    ✅ Readback matches.\n";

        # 4. OVERWRITE
        echo "[*] 4. Overwriting with new content...\n";
        $disk->put($probeKey, $payload2);
        $readBack2 = $disk->get($probeKey);
        if ($readBack2 !== $payload2) {
            throw new RuntimeException("Overwrite readback mismatch");
        }
        echo "    ✅ Overwrite verified.\n";

        # 5. SIZE
        echo "[*] 5. Checking file size...\n";
        $size = $disk->size($probeKey);
        if ($size !== strlen($payload2)) {
            throw new RuntimeException("Size mismatch: expected " . strlen($payload2) . ", got {$size}");
        }
        echo "    ✅ Size correct: {$size} bytes.\n";

        # 6. DELETE
        echo "[*] 6. Deleting probe file...\n";
        $disk->delete($probeKey);
        if ($disk->exists($probeKey)) {
            throw new RuntimeException("File still exists after deletion");
        }
        echo "    ✅ Delete confirmed.\n";

        echo "  🎉 {$diskName}: ALL CHECKS PASSED\n";
        $pass++;

    } catch (\Throwable $e) {
        echo "  ❌ {$diskName}: FAILED — {$e->getMessage()}\n";
        $fail++;

        # Best-effort cleanup
        try { $disk->delete($probeKey); } catch (\Throwable) {}
    }
}

# ── Summary ──────────────────────────────────────────────────────────────────
echo "\n================================================================\n";
if ($fail === 0) {
    echo "  🎉 SUCCESS: All {$pass} disk(s) passed integration test.\n";
} else {
    echo "  ❌ FAILURE: {$fail} disk(s) failed, {$pass} passed.\n";
}
echo "================================================================\n";

exit($fail > 0 ? 1 : 0);
