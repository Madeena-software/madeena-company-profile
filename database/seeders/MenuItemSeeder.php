<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        // Header links
        $headerLinks = [
            [
                'label' => 'Home',
                'url' => '/',
                'location' => 'header',
                'is_cta' => false,
                'sort_order' => 1,
            ],
            [
                'label' => 'Produk',
                'url' => '/#produk',
                'location' => 'header',
                'is_cta' => false,
                'sort_order' => 2,
            ],
            [
                'label' => 'Tentang Kami',
                'url' => '/#tentang',
                'location' => 'header',
                'is_cta' => false,
                'sort_order' => 3,
            ],
            [
                'label' => 'Blog',
                'url' => '/blog',
                'location' => 'header',
                'is_cta' => false,
                'sort_order' => 4,
            ],
            [
                'label' => 'Hubungi Kami',
                'url' => '/#kontak',
                'location' => 'header',
                'is_cta' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($headerLinks as $link) {
            MenuItem::updateOrCreate(
                ['label' => $link['label'], 'location' => $link['location']],
                $link
            );
        }

        // Footer links
        $footerLinks = [
            [
                'label' => 'Produk',
                'url' => '/#produk',
                'location' => 'footer',
                'is_cta' => false,
                'sort_order' => 1,
            ],
            [
                'label' => 'Tentang Kami',
                'url' => '/#tentang',
                'location' => 'footer',
                'is_cta' => false,
                'sort_order' => 2,
            ],
            [
                'label' => 'Blog',
                'url' => '/blog',
                'location' => 'footer',
                'is_cta' => false,
                'sort_order' => 3,
            ],
            [
                'label' => 'Legalitas',
                'url' => '/#legalitas',
                'location' => 'footer',
                'is_cta' => false,
                'sort_order' => 4,
            ],
            [
                'label' => 'Kontak',
                'url' => '/#kontak',
                'location' => 'footer',
                'is_cta' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($footerLinks as $link) {
            MenuItem::updateOrCreate(
                ['label' => $link['label'], 'location' => $link['location']],
                $link
            );
        }
    }
}
