<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\WebDavFilesystemServiceProvider;

return [
    AppServiceProvider::class,
    WebDavFilesystemServiceProvider::class,
    AdminPanelProvider::class,
];
