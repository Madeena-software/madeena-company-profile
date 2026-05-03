<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter as IlluminateFilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;

class WebDavFilesystemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('webdav', function (Application $app, array $config): IlluminateFilesystemAdapter {
            $baseUri = rtrim((string) ($config['base_uri'] ?? ''), '/').'/';
            $username = (string) ($config['username'] ?? '');
            $password = (string) ($config['password'] ?? '');

            if ($baseUri === '/' || $username === '' || $password === '') {
                throw new InvalidArgumentException('WebDAV disks require base_uri, username, and password configuration.');
            }

            $client = new Client([
                'baseUri' => $baseUri,
                'userName' => $username,
                'password' => $password,
                'authType' => Client::AUTH_BASIC,
            ]);

            $timeout = (int) ($config['timeout'] ?? 30);
            if ($timeout > 0) {
                $client->addCurlSetting(CURLOPT_CONNECTTIMEOUT, min($timeout, 10));
                $client->addCurlSetting(CURLOPT_TIMEOUT, $timeout);
            }

            $lowSpeedLimit = (int) ($config['low_speed_limit'] ?? 1);
            $lowSpeedTime = (int) ($config['low_speed_time'] ?? 30);
            if ($lowSpeedLimit > 0 && $lowSpeedTime > 0) {
                $client->addCurlSetting(CURLOPT_LOW_SPEED_LIMIT, $lowSpeedLimit);
                $client->addCurlSetting(CURLOPT_LOW_SPEED_TIME, $lowSpeedTime);
            }

            if ((bool) ($config['tcp_keepalive'] ?? true) && defined('CURLOPT_TCP_KEEPALIVE')) {
                $client->addCurlSetting(CURLOPT_TCP_KEEPALIVE, 1);
            }

            if (defined('CURLOPT_NOSIGNAL')) {
                $client->addCurlSetting(CURLOPT_NOSIGNAL, 1);
            }

            $client->addCurlSetting(CURLOPT_FOLLOWLOCATION, true);
            $client->addCurlSetting(CURLOPT_MAXREDIRS, 5);

            if (($config['verify_ssl'] ?? true) === false) {
                $client->addCurlSetting(CURLOPT_SSL_VERIFYHOST, 0);
                $client->addCurlSetting(CURLOPT_SSL_VERIFYPEER, false);
            }

            $adapter = new WebDAVAdapter(
                client: $client,
                prefix: trim((string) ($config['root'] ?? ''), '/'),
                visibilityHandling: WebDAVAdapter::ON_VISIBILITY_IGNORE,
            );

            return new IlluminateFilesystemAdapter(
                driver: new Filesystem($adapter, $config),
                adapter: $adapter,
                config: $config,
            );
        });
    }
}
