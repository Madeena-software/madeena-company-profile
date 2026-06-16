<?php

namespace App\Filament\Resources\GuestMessages\Pages;

use App\Filament\Resources\GuestMessages\GuestMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGuestMessage extends EditRecord
{
    protected static string $resource = GuestMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
