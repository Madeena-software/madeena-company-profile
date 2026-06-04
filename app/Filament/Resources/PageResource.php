<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Halaman';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?int $navigationSort = 5;

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
            Section::make('Informasi Halaman')->schema([
                TextInput::make('title')
                    ->label('Judul Halaman')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->notIn(['admin', 'blog', 'login', 'logout', 'register', 'storage', 'api', 'inabuyer2026', 'health'])
                    ->validationMessages([
                        'not_in' => 'Slug ini merupakan rute bawaan sistem dan tidak dapat digunakan.',
                    ]),
                Builder::make('content')
                    ->label('Konten Halaman (Page Builder)')
                    ->columnSpanFull()
                    ->blocks([
                        Block::make('paragraph')
                            ->label('Paragraf Teks')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                RichEditor::make('content')
                                    ->label('Konten')
                                    ->required(),
                            ]),
                        Block::make('hero')
                            ->label('Banner Hero')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Utama')
                                    ->required(),
                                TextInput::make('subtitle')
                                    ->label('Subjudul'),
                                TextInput::make('cta_text')
                                    ->label('Teks Tombol CTA'),
                                TextInput::make('cta_url')
                                    ->label('Link Tombol CTA'),
                                Select::make('bg_style')
                                    ->label('Gaya Background')
                                    ->options([
                                        'blue' => 'Biru Madeena',
                                        'teal' => 'Teal Madeena',
                                        'gray' => 'Abu-abu Terang',
                                        'white' => 'Putih Bersih',
                                    ])
                                    ->default('blue'),
                            ]),
                        Block::make('features')
                            ->label('Kotak Fitur/Keunggulan')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Section'),
                                TextInput::make('subtitle')
                                    ->label('Subjudul Section'),
                                Repeater::make('items')
                                    ->label('Daftar Fitur')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul Fitur')
                                            ->required(),
                                        Textarea::make('description')
                                            ->label('Deskripsi Fitur')
                                            ->required(),
                                        TextInput::make('icon')
                                            ->label('Icon FontAwesome (contoh: fa-shield-alt)')
                                            ->default('fa-star'),
                                    ])
                                    ->columns(1)
                                    ->createItemButtonLabel('Tambah Fitur'),
                            ]),
                        Block::make('image_text')
                            ->label('Gambar & Teks')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Gambar')
                                    ->image()
                                    ->disk('public')
                                    ->directory('pages')
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Judul Teks'),
                                RichEditor::make('content')
                                    ->label('Konten Teks')
                                    ->required(),
                                Select::make('image_position')
                                    ->label('Posisi Gambar')
                                    ->options([
                                        'left' => 'Kiri',
                                        'right' => 'Kanan',
                                    ])
                                    ->default('left'),
                            ]),
                        Block::make('cta')
                            ->label('Call to Action (CTA)')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul CTA')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Deskripsi CTA'),
                                TextInput::make('button_text')
                                    ->label('Teks Tombol')
                                    ->required(),
                                TextInput::make('button_url')
                                    ->label('Link Tombol')
                                    ->required(),
                            ]),
                        Block::make('embed')
                            ->label('Embed Code (HTML/Iframe)')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Textarea::make('code')
                                    ->label('Kode HTML/Iframe (contoh: Google Maps/YouTube)')
                                    ->required()
                                    ->rows(5),
                            ]),
                    ]),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable(),
            Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->date('d M Y')->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->since(),
        ])->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([
                DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
