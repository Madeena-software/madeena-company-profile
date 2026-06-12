<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.widgets.welcome-widget';

    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';
}
