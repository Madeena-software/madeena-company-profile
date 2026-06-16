<?php

namespace App\Filament\Resources\GuestMessages\Pages;

use App\Filament\Resources\GuestMessages\GuestMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGuestMessages extends ListRecords
{
    protected static string $resource = GuestMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
