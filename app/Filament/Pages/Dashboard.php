<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Beranda';

    public static function shouldRegisterNavigation(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user instanceof \App\Models\User && $user->isAdmin();
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\WelcomeWidget::class,
            \App\Filament\Widgets\QuickActionsWidget::class,
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\RecentActivityWidget::class,
        ];
    }
}
