<?php

namespace App\Filament\Resources\GuestMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GuestMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('organization')
                    ->maxLength(255),
                TextInput::make('position')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Textarea::make('kesan_dan_pesan')
                    ->required()
                    ->maxLength(5000)
                    ->columnSpanFull(),
                Toggle::make('is_visible')
                    ->required()
                    ->default(true),
            ]);
    }
}
