<?php

namespace App\Filament\RichEditorBlocks;

use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;

class EquationBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'academic-equation';
    }

    public static function getLabel(): string
    {
        return 'Persamaan / Equation';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->icon('heroicon-o-calculator')
            ->form([
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
