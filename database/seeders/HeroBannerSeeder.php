<?php

namespace Database\Seeders;

use App\Models\HeroBanner;
use Illuminate\Database\Seeder;

class HeroBannerSeeder extends Seeder
{
    public function run(): void
    {
        HeroBanner::updateOrCreate(
            ['title' => 'PT MADEENA Karya Indonesia'],
            [
                'subtitle' => 'Know Sciences, Learn Engineering, Create Technology, Develop Business.',
                'description' => 'Produsen alat Digital Direct Radiography (DDR) berbasis teknologi Camera Coupled X-Ray Detector (CCXD) buatan Indonesia. TKDN 57,62%, Izin Edar Kemenkes RI AKD 21501220581.',
                'cta_text' => 'Lihat Produk Kami',
                'cta_url' => '#produk',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );
    }
}
