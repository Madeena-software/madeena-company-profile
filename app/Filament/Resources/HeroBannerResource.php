<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroBannerResource\Pages;
use App\Models\HeroBanner;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class HeroBannerResource extends Resource
{
    protected static ?string $model = HeroBanner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?int $navigationSort = 1;

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
            Section::make('Informasi Banner')->schema([
                TextInput::make('title')
                    ->label('Judul')->required()->maxLength(255),
                TextInput::make('subtitle')
                    ->label('Subjudul')->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')->rows(3),
                FileUpload::make('image_path')
                    ->label('Gambar Banner')->image()->disk('public')->directory('banners'),
                TextInput::make('cta_text')
                    ->label('Teks Tombol CTA'),
                TextInput::make('cta_url')
                    ->label('URL Tombol CTA'),
                TextInput::make('sort_order')
                    ->label('Urutan')->numeric()->default(0),
                Toggle::make('is_active')
                    ->label('Aktif')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image_path')->label('Gambar'),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
            Tables\Columns\TextColumn::make('subtitle')->label('Subjudul'),
            Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->since(),
        ])->defaultSort('sort_order')->reorderable('sort_order')
            ->filters([])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([
                DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroBanners::route('/'),
            'create' => Pages\CreateHeroBanner::route('/create'),
            'edit' => Pages\EditHeroBanner::route('/{record}/edit'),
        ];
    }
}
