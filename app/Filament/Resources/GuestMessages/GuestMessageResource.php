<?php

namespace App\Filament\Resources\GuestMessages;

use App\Filament\Resources\GuestMessages\Pages\CreateGuestMessage;
use App\Filament\Resources\GuestMessages\Pages\EditGuestMessage;
use App\Filament\Resources\GuestMessages\Pages\ListGuestMessages;
use App\Filament\Resources\GuestMessages\Schemas\GuestMessageForm;
use App\Filament\Resources\GuestMessages\Tables\GuestMessagesTable;
use App\Models\GuestMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GuestMessageResource extends Resource
{
    protected static ?string $model = GuestMessage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Kesan dan Pesan';
    
    protected static ?string $navigationLabel = 'Semua Pesan Tamu';
    
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GuestMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GuestMessagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuestMessages::route('/'),
            'create' => CreateGuestMessage::route('/create'),
            'edit' => EditGuestMessage::route('/{record}/edit'),
        ];
    }
}
