<?php

namespace App\Filament\Resources\MenuItems;

use App\Filament\Resources\MenuItems\Pages\ManageMenuItems;
use App\Models\MenuItem;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Menu Navigasi';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'label';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Menu')->schema([
                TextInput::make('label')
                    ->label('Label Link')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('URL Tujuan')
                    ->required()
                    ->placeholder('Contoh: /, /#produk, /blog, /about-us')
                    ->maxLength(255),
                Select::make('location')
                    ->label('Lokasi Menu')
                    ->options([
                        'header' => 'Header (Navigasi Atas)',
                        'footer' => 'Footer (Navigasi Bawah)',
                        'both' => 'Keduanya (Header & Footer)',
                    ])
                    ->default('header')
                    ->required(),
                Select::make('target')
                    ->label('Target Link')
                    ->options([
                        '_self' => 'Tab yang Sama (_self)',
                        '_blank' => 'Tab Baru (_blank)',
                    ])
                    ->default('_self')
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_cta')
                    ->label('Style sebagai Tombol CTA (Khusus Header)'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL Tujuan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'header' => 'info',
                        'footer' => 'success',
                        'both' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_cta')
                    ->label('CTA')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMenuItems::route('/'),
        ];
    }
}
