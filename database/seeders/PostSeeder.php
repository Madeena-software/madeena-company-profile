<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        Post::updateOrCreate(
            ['slug' => 'madeena-luncurkan-ddr-hf100b-mdn'],
            [
                'title' => 'Madeena Luncurkan DDR HF100B-MDN',
                'excerpt' => 'PT Madeena Karya Indonesia meluncurkan solusi digital radiography buatan Indonesia dengan TKDN 57,62%.',
                'body' => '<p>PT Madeena Karya Indonesia resmi meluncurkan DDR HF100B-MDN sebagai solusi radiografi digital produksi dalam negeri. Produk ini mengusung teknologi Camera Coupled X-Ray Detector (CCXD) dengan performa tinggi untuk kebutuhan pelayanan kesehatan.</p><p>Perangkat mendukung format DICOM, memiliki resolusi 12MP, dan dirancang untuk implementasi klinis yang efisien.</p>',
                'category' => 'Produk',
                'is_published' => true,
                'published_at' => now()->subDays(7),
            ]
        );

        Post::updateOrCreate(
            ['slug' => 'penguatan-kemitraan-fasyankes'],
            [
                'title' => 'Penguatan Kemitraan dengan Fasilitas Kesehatan',
                'excerpt' => 'Program kemitraan Madeena hadir untuk mempercepat adopsi teknologi radiografi digital di berbagai daerah.',
                'body' => '<p>Madeena terus memperkuat kolaborasi dengan fasilitas pelayanan kesehatan melalui skema kemitraan yang fleksibel.</p><p>Program ini mencakup dukungan instalasi, pelatihan, dan pendampingan operasional agar proses implementasi berjalan optimal.</p>',
                'category' => 'Kemitraan',
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ]
        );

        Post::updateOrCreate(
            ['slug' => 'komitmen-hilirisasi-riset-ugm'],
            [
                'title' => 'Komitmen Hilirisasi Riset UGM untuk Industri Medis',
                'excerpt' => 'Madeena memperkuat hilirisasi inovasi perguruan tinggi menjadi produk alat kesehatan siap pakai.',
                'body' => '<p>Berangkat dari riset Universitas Gadjah Mada, Madeena berkomitmen menghadirkan teknologi kesehatan yang relevan untuk kebutuhan nasional.</p><p>Langkah ini mendorong kemandirian industri alat kesehatan Indonesia melalui inovasi berkelanjutan.</p>',
                'category' => 'Inovasi',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ]
        );
    }
}
