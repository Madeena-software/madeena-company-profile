<?php

namespace App\Filament\RichEditorBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TableBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'academic-table';
    }

    public static function getLabel(): string
    {
        return 'Tabel / Table';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->icon('heroicon-o-table-cells')
            ->form([
                TextInput::make('caption')
                    ->label('Judul Tabel')
                    ->required()
                    ->placeholder('Contoh: Hasil pengukuran suhu sampel pada berbagai tekanan')
                    ->helperText('Akan ditampilkan sebagai "Tabel X: [judul Anda]"'),

                Textarea::make('table_html')
                    ->label('Konten Tabel (HTML)')
                    ->required()
                    ->rows(10)
                    ->placeholder('<table><thead><tr><th>Parameter</th><th>Nilai</th></tr></thead><tbody><tr><td>Suhu</td><td>500°C</td></tr></tbody></table>')
                    ->helperText('Masukkan tabel dalam format HTML. Gunakan <thead> untuk header dan <tbody> untuk isi.'),

                TextInput::make('ref_id')
                    ->label('ID Referensi')
                    ->placeholder('tbl-temperature')
                    ->helperText('ID unik untuk rujukan silang. Contoh: tbl-temperature')
                    ->rules(['nullable', 'regex:/^[a-z0-9-]+$/']),
            ]);
    }
}
