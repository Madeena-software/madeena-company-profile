<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use Filament\Widgets\Widget;

class RecentActivityWidget extends Widget
{
    protected string $view = 'filament.widgets.recent-activity-widget';

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function getActivities(): array
    {
        $activities = collect();

        $posts = Post::latest('updated_at')->take(5)->get()->map(function ($item) {
            return [
                'icon' => 'heroicon-o-document-text',
                'color' => 'text-primary-500',
                'message' => 'Anda menyimpan Artikel "' . $item->title . '"',
                'time' => $item->updated_at,
            ];
        });

        $products = Product::latest('updated_at')->take(5)->get()->map(function ($item) {
            return [
                'icon' => 'heroicon-o-beaker',
                'color' => 'text-success-500',
                'message' => 'Anda menyimpan Produk "' . $item->name . '"',
                'time' => $item->updated_at,
            ];
        });

        $pages = Page::latest('updated_at')->take(5)->get()->map(function ($item) {
            return [
                'icon' => 'heroicon-o-document',
                'color' => 'text-info-500',
                'message' => 'Anda menyimpan Halaman "' . $item->title . '"',
                'time' => $item->updated_at,
            ];
        });

        $activities = $activities->concat($posts)->concat($products)->concat($pages);

        return $activities->sortByDesc('time')->take(5)->values()->all();
    }
}
