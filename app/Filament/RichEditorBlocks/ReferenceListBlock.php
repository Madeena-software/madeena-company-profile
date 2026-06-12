<?php

namespace App\Filament\RichEditorBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;

class ReferenceListBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'academic-references';
    }

    public static function getLabel(): string
    {
        return 'Daftar Pustaka / References';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->icon('heroicon-o-book-open')
            ->form([
                Repeater::make('references')
                    ->label('Daftar Referensi')
                    ->schema([
                        TextInput::make('authors')
                            ->label('Penulis')
                            ->required()
                            ->placeholder('Suparta, G.B., Smith, J.'),

                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->placeholder('Analysis of ionospheric perturbations'),

                        TextInput::make('journal')
                            ->label('Jurnal / Sumber')
                            ->placeholder('Journal of Geophysical Research'),

                        TextInput::make('year')
                            ->label('Tahun')
                            ->placeholder('2024'),

                        TextInput::make('volume')
                            ->label('Volume')
                            ->placeholder('129(3)'),

                        TextInput::make('pages')
                            ->label('Halaman')
                            ->placeholder('pp. 1234-1250'),

                        TextInput::make('doi')
                            ->label('DOI')
                            ->placeholder('10.1029/2024JA032xxx')
                            ->helperText('Opsional. Akan menjadi tautan klik.'),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->addActionLabel('+ Tambah Referensi')
                    ->helperText('Referensi akan otomatis diberi nomor [1], [2], [3], dst.'),
            ]);
    }
}
