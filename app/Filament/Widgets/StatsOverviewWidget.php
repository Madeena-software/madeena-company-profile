<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        return [
            Stat::make('Artikel Penelitian', Post::count())
                ->description('Total artikel yang dipublikasikan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Produk Inovasi', Product::count())
                ->description('Total produk yang terdaftar')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('success'),
            Stat::make('Halaman Statis', Page::count())
                ->description('Total halaman informasi')
                ->descriptionIcon('heroicon-m-document')
                ->color('info'),
        ];
    }
}
