<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 2;

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
            \Filament\Schemas\Components\Tabs::make('Tabs')
                ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('Info Produk')->schema([
                        TextInput::make('name')
                            ->label('Nama Produk')->required()->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug')->required()->unique(ignoreRecord: true),
                        TextInput::make('tagline')
                            ->label('Tagline')->maxLength(255),
                        FileUpload::make('image_path')
                            ->label('Gambar Produk')->image()->disk('public')->directory('products'),
                        KeyValue::make('specifications')
                            ->label('Spesifikasi')->columnSpanFull(),
                        Toggle::make('is_featured')->label('Unggulan'),
                        Toggle::make('is_active')->label('Aktif')->default(true),
                        TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                    ])->columns(2),
                    \Filament\Schemas\Components\Tabs\Tab::make('Halaman Detail Produk')->schema([
                        \Filament\Forms\Components\Builder::make('content_json')
                            ->label('Konten')
                            ->blocks(\App\Filament\BuilderBlocks::get())
                            ->reorderable()
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull()
                            ->blockNumbers(false),
                    ]),
                ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('image_path')->label('Gambar'),
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('tagline')->label('Tagline'),
            Tables\Columns\IconColumn::make('is_featured')->label('Unggulan')->boolean(),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->since(),
        ])->defaultSort('sort_order')
            ->filters([])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([
                DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
