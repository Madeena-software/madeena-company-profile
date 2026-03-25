<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Blog';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Blog')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Select::make('user_id')
                    ->label('Penulis')
                    ->options(User::pluck('name', 'id'))
                    ->required()
                    ->default(Auth::id())
                    ->disabled(function () {
                        $user = Auth::user();

                        if (! $user instanceof User) {
                            return true;
                        }

                        return ! ($user->is_admin || $user->email === config('auth.filament_admin_email', 'admin@madeena.local'));
                    })
                    ->dehydrated(),
                Forms\Components\TextInput::make('category')->label('Kategori'),
                Forms\Components\Textarea::make('excerpt')->label('Ringkasan')->rows(2),
                Forms\Components\FileUpload::make('cover_image')
                    ->label('Gambar Sampul')->image()->disk('public')->directory('posts'),
                Forms\Components\RichEditor::make('body')
                    ->label('Isi Blog')->columnSpanFull(),
                Forms\Components\Toggle::make('is_published')->label('Publikasikan')
                    ->live()
                    ->afterStateUpdated(fn($state, Forms\Set $set) =>
                    $set('published_at', $state ? now() : null)),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Tanggal Publikasi'),
            ])->columns(2),
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
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])])
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();

                if (! $user instanceof User) {
                    return;
                }

                // Admin sees all posts, non-admin sees only their own
                if (! ($user->is_admin || $user->email === config('auth.filament_admin_email', 'admin@madeena.local'))) {
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
