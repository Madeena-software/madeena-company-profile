<?php

namespace App\Filament\RichEditorBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;

class FigureBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'academic-figure';
    }

    public static function getLabel(): string
    {
        return 'Gambar / Figure';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->icon('heroicon-o-photo')
            ->form([
                FileUpload::make('image')
                    ->label('Unggah Gambar')
                    ->image()
                    ->disk('public')
                    ->directory('academic-figures')
                    ->imageResizeMode('contain')
                    ->imageResizeTargetWidth('1920')
                    ->required()
                    ->helperText('Unggah gambar (JPG, PNG, WebP). Akan dioptimasi otomatis.'),

                TextInput::make('caption')
                    ->label('Keterangan Gambar')
                    ->required()
                    ->placeholder('Contoh: Morfologi permukaan sampel setelah pemanasan 500°C')
                    ->helperText('Akan ditampilkan sebagai "Gambar X: [keterangan Anda]"'),

                TextInput::make('ref_id')
                    ->label('ID Referensi')
                    ->placeholder('fig-sem-surface')
                    ->helperText('ID unik untuk rujukan silang. Gunakan huruf kecil dan strip. Contoh: fig-sem-surface')
                    ->rules(['nullable', 'regex:/^[a-z0-9-]+$/']),

                Select::make('size')
                    ->label('Ukuran Gambar')
                    ->options([
                        'small' => 'Kecil (50%)',
                        'medium' => 'Sedang (75%)',
                        'full' => 'Penuh (100%)',
                    ])
                    ->default('full'),
            ]);
    }
}
