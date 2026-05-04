<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InabuyerMessageResource\Pages;
use App\Models\InabuyerMessage;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema as ResourceSchema;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InabuyerMessageResource extends Resource
{
    protected static ?string $model = InabuyerMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Kesan dan Pesan';

    protected static string|\UnitEnum|null $navigationGroup = 'Inabuyer 2026';

    protected static ?int $navigationSort = 1;

    protected static function isAdminUser(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isAdminUser();
    }

    public static function canViewAny(): bool
    {
        return static::isAdminUser();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return static::isAdminUser();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(ResourceSchema $schema): ResourceSchema
    {
        return $schema->components([
            Section::make('Data Feedback')->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('organization')
                    ->label('Organisasi')
                    ->maxLength(255),
                Textarea::make('kesan_dan_pesan')
                    ->label('Kesan dan Pesan')
                    ->required()
                    ->rows(8)
                    ->columnSpanFull()
                    ->maxLength(5000),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ToggleColumn::make('is_visible')
                ->label('Tampil')
                ->onColor('success')
                ->offColor('gray')
                ->onIcon('heroicon-o-eye')
                ->offIcon('heroicon-o-eye-slash'),
            Tables\Columns\TextColumn::make('name')
                ->label('Nama')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('is_visible')
                ->label('Status')
                ->badge()
                ->formatStateUsing(fn (bool $state): string => $state ? 'Visible' : 'Hidden')
                ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                ->toggleable(),
            Tables\Columns\TextColumn::make('organization')
                ->label('Organisasi')
                ->searchable()
                ->toggleable(),
            Tables\Columns\TextColumn::make('kesan_dan_pesan')
                ->label('Kesan dan Pesan')
                ->limit(80)
                ->wrap()
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Diterima')
                ->since()
                ->sortable(),
        ])->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInabuyerMessages::route('/'),
            'edit' => Pages\EditInabuyerMessage::route('/{record}/edit'),
        ];
    }
}
