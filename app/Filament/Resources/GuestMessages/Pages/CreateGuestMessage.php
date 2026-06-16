<?php

namespace App\Filament\Resources\GuestMessages\Pages;

use App\Filament\Resources\GuestMessages\GuestMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuestMessage extends CreateRecord
{
    protected static string $resource = GuestMessageResource::class;
}
