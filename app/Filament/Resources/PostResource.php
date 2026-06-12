<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\RichEditorBlocks\EquationBlock;
use App\Filament\RichEditorBlocks\FigureBlock;
use App\Filament\RichEditorBlocks\ReferenceListBlock;
use App\Models\Post;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Blog';

    protected static string|\UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Post')->tabs([
                Tab::make('Metadata Artikel')->schema([
                    TextInput::make('title')
                        ->label('Judul')->required()->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->label('Slug')->required()->unique(ignoreRecord: true),
                    Select::make('user_id')
                        ->label('Penulis')
                        ->options(User::pluck('name', 'id'))
                        ->required()
                        ->default(Auth::id())
                        ->dehydrated(),
                    TextInput::make('category')->label('Kategori'),
                    FileUpload::make('cover_image')
                        ->label('Gambar Sampul')->image()->disk('public')->directory('posts'),
                    Toggle::make('is_published')->label('Publikasikan')
                        ->live()
                        ->afterStateUpdated(fn ($state, Set $set) => $set('published_at', $state ? now() : null)),
                    DateTimePicker::make('published_at')
                        ->label('Tanggal Publikasi'),
                    Select::make('content_language')
                        ->label('Bahasa Konten')
                        ->options(['id' => 'Indonesia', 'en' => 'Inggris'])
                        ->default('id'),
                ])->columns(2),

                Tab::make('Info Akademik')->schema([
                    Textarea::make('abstract')
                        ->label('Abstrak')
                        ->rows(4)
                        ->placeholder('Masukkan abstrak penelitian...'),
                    TagsInput::make('keywords')
                        ->label('Kata Kunci')
                        ->placeholder('Contoh: fisika, material, semikonduktor'),
                    Repeater::make('authors_info')
                        ->label('Penulis Tambahan / Afiliasi')
                        ->schema([
                            TextInput::make('name')->label('Nama Lengkap')->required(),
                            TextInput::make('affiliation')->label('Afiliasi / Instansi'),
                            TextInput::make('email')->label('Email')->email(),
                        ])
                        ->columns(3)
                        ->addActionLabel('+ Tambah Penulis'),
                ]),

                Tab::make('Konten Artikel')->schema([
                    Toggle::make('enable_auto_numbering')
                        ->label('Aktifkan Penomoran Otomatis')
                        ->default(true)
                        ->helperText('Jika aktif, judul (H2, H3) akan otomatis diberi nomor urut.'),
                    RichEditor::make('content_json')
                        ->label('Isi Artikel')
                        ->json()
                        ->columnSpanFull()
                        ->customBlocks([
                            FigureBlock::class,
                            EquationBlock::class,
                            ReferenceListBlock::class,
                        ])
                        ->toolbarButtons([
                            'heading',
                            'bold',
                            'italic',
                            'underline',
                            'superscript',
                            'subscript',
                            'bulletList',
                            'orderedList',
                            'link',
                            'blockquote',
                            'table',
                            'undo',
                            'redo',
                            'blocks',
                        ]),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image')->label('Cover'),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('author.name')->label('Penulis')->sortable(),
            Tables\Columns\TextColumn::make('category')->label('Kategori'),
            Tables\Columns\IconColumn::make('is_published')->label('Dipublikasikan')->boolean(),
            Tables\Columns\TextColumn::make('published_at')->label('Tanggal Publikasi')->date('d M Y')->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->since(),
        ])->defaultSort('published_at', 'desc')
            ->filters([])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([
                DeleteBulkAction::make(),
            ])])
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if (! $user instanceof User) {
                    return;
                }

                // Admin sees all posts, non-admin sees only their own
                if (! $user->isAdmin()) {
                    $query->where('user_id', $user->id);
                }
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
