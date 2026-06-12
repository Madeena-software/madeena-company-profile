<?php

namespace App\Filament\RichEditorBlocks;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class EquationBlock
{
    public static function make(): Block
    {
        return Block::make('academic-equation')
            ->label('Persamaan / Equation')
            ->icon('heroicon-o-calculator')
            ->schema([
                Textarea::make('latex')
                    ->label('Persamaan LaTeX')
                    ->required()
                    ->rows(3)
                    ->placeholder('E = mc^2')
                    ->helperText('Tulis persamaan dalam format LaTeX. Contoh: E = mc^2, \frac{\partial^2 u}{\partial t^2} = c^2 \nabla^2 u'),

                TextInput::make('ref_id')
                    ->label('ID Referensi')
                    ->placeholder('eq-wave')
                    ->helperText('ID unik untuk rujukan silang. Contoh: eq-wave')
                    ->rules(['nullable', 'regex:/^[a-z0-9-]+$/']),
            ]);
    }
}
