<?php

$storageBasePath = env('STORAGE_BASE_PATH');
$publicStorageDriver = env('PUBLIC_STORAGE_DRIVER', 'local');
$enterpriseBackupDriver = env('ENTERPRISE_BACKUP_DRIVER', 'local');

$privateRoot = $storageBasePath
    ? rtrim($storageBasePath, '/').'/app/private'
    : storage_path('app/private');

$publicRoot = $storageBasePath
    ? rtrim($storageBasePath, '/').'/app/public'
    : storage_path('app/public');

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3", "webdav"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => $privateRoot,
            'serve' => false,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => $publicStorageDriver,
            'root' => $publicStorageDriver === 'webdav'
                ? env('NEXTCLOUD_WEBDAV_PUBLIC_ROOT', 'madeena_cp_media')
                : $publicRoot,
            'base_uri' => env('NEXTCLOUD_WEBDAV_BASE_URI'),
            'username' => env('NEXTCLOUD_WEBDAV_USERNAME'),
            'password' => env('NEXTCLOUD_WEBDAV_PASSWORD'),
            'timeout' => env('NEXTCLOUD_WEBDAV_TIMEOUT', 30),
            'low_speed_limit' => env('NEXTCLOUD_WEBDAV_LOW_SPEED_LIMIT', 1),
            'low_speed_time' => env('NEXTCLOUD_WEBDAV_LOW_SPEED_TIME', 30),
            'tcp_keepalive' => filter_var(env('NEXTCLOUD_WEBDAV_TCP_KEEPALIVE', true), FILTER_VALIDATE_BOOL),
            'verify_ssl' => filter_var(env('NEXTCLOUD_WEBDAV_VERIFY_SSL', true), FILTER_VALIDATE_BOOL),
            'require_server_checksum' => filter_var(env('NEXTCLOUD_WEBDAV_REQUIRE_SERVER_CHECKSUM', false), FILTER_VALIDATE_BOOL),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => filter_var(env('PUBLIC_STORAGE_THROW', false), FILTER_VALIDATE_BOOL),
            'report' => filter_var(env('PUBLIC_STORAGE_REPORT', false), FILTER_VALIDATE_BOOL),
        ],

        'enterprise_backups' => [
            'driver' => $enterpriseBackupDriver,
            'root' => $enterpriseBackupDriver === 'webdav'
                ? env('NEXTCLOUD_WEBDAV_BACKUP_ROOT', 'madeena_cp_backups')
                : env('ENTERPRISE_BACKUP_LOCAL_ROOT', '/var/www/enterprise_backups'),
            'base_uri' => env('NEXTCLOUD_WEBDAV_BASE_URI'),
            'username' => env('NEXTCLOUD_WEBDAV_USERNAME'),
            'password' => env('NEXTCLOUD_WEBDAV_PASSWORD'),
            'timeout' => env('NEXTCLOUD_WEBDAV_TIMEOUT', 30),
            'low_speed_limit' => env('NEXTCLOUD_WEBDAV_LOW_SPEED_LIMIT', 1),
            'low_speed_time' => env('NEXTCLOUD_WEBDAV_LOW_SPEED_TIME', 30),
            'tcp_keepalive' => filter_var(env('NEXTCLOUD_WEBDAV_TCP_KEEPALIVE', true), FILTER_VALIDATE_BOOL),
            'verify_ssl' => filter_var(env('NEXTCLOUD_WEBDAV_VERIFY_SSL', true), FILTER_VALIDATE_BOOL),
            'require_server_checksum' => filter_var(env('NEXTCLOUD_WEBDAV_REQUIRE_SERVER_CHECKSUM', false), FILTER_VALIDATE_BOOL),
            'throw' => filter_var(env('ENTERPRISE_BACKUP_THROW', false), FILTER_VALIDATE_BOOL),
            'report' => filter_var(env('ENTERPRISE_BACKUP_REPORT', false), FILTER_VALIDATE_BOOL),
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => $publicRoot,
    ],

];
