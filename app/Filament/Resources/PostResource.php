<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Artikel')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('category')->label('Kategori'),
                Forms\Components\Textarea::make('excerpt')->label('Ringkasan')->rows(2),
                Forms\Components\FileUpload::make('cover_image')
                    ->label('Gambar Sampul')->image()->directory('posts'),
                Forms\Components\RichEditor::make('body')
                    ->label('Isi Artikel')->columnSpanFull(),
                Forms\Components\Toggle::make('is_published')->label('Publikasikan')
                    ->live()
                    ->afterStateUpdated(fn ($state, Forms\Set $set) =>
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
            Tables\Columns\TextColumn::make('category')->label('Kategori'),
            Tables\Columns\IconColumn::make('is_published')->label('Dipublikasikan')->boolean(),
            Tables\Columns\TextColumn::make('published_at')->label('Tanggal Publikasi')->date('d M Y')->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->since(),
        ])->defaultSort('published_at', 'desc')
          ->filters([])
          ->actions([Tables\Actions\EditAction::make()])
          ->bulkActions([Tables\Actions\BulkActionGroup::make([
              Tables\Actions\DeleteBulkAction::make(),
          ])]);
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
