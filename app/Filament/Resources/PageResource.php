<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\RichEditorBlocks\EquationBlock;
use App\Filament\RichEditorBlocks\FigureBlock;
use App\Filament\RichEditorBlocks\ReferenceListBlock;
use App\Models\Page;
use App\Models\User;
use Filament\Forms\Components\Builder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Halaman';
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



    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Halaman')->schema([
                TextInput::make('title')
                    ->label('Judul Halaman')->required()->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->label('Slug')->required()->unique(ignoreRecord: true),
                Select::make('content_language')
                    ->label('Bahasa Konten')
                    ->options(['id' => 'Indonesia', 'en' => 'Inggris'])
                    ->default('id'),
                Toggle::make('enable_auto_numbering')
                    ->label('Aktifkan Penomoran Otomatis')
                    ->default(true)
                    ->helperText('Jika aktif, judul (H2, H3) akan otomatis diberi nomor urut.'),
                Toggle::make('show_in_header')
                    ->label('Tampilkan di Navigasi Header')
                    ->default(false),
                Toggle::make('show_in_footer')
                    ->label('Tampilkan di Footer')
                    ->default(false),
                Textarea::make('summary')
                    ->label('Ringkasan Singkat')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Ringkasan yang muncul di Halaman Utama atau kartu preview.'),
            ])->columns(2),

            Section::make('Konten Halaman')->schema([
                Builder::make('content_json')
                    ->label('Konten Halaman')
                    ->blocks(\App\Filament\BuilderBlocks::get())
                    ->reorderable()
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->live(debounce: 3000)
                    ->afterStateUpdated(function ($livewire) {
                        if (method_exists($livewire, 'save')) {
                            $livewire->save();
                        }
                    })
                    ->helperText('Tersimpan otomatis setiap 3 detik. ✓')
                    ->blockNumbers(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable(),
            Tables\Columns\IconColumn::make('show_in_header')->label('Header')->boolean(),
            Tables\Columns\IconColumn::make('show_in_footer')->label('Footer')->boolean(),
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
