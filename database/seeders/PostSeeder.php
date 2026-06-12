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
                'content_json' => [
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'PT Madeena Karya Indonesia resmi meluncurkan DDR HF100B-MDN sebagai solusi radiografi digital produksi dalam negeri. Produk ini mengusung teknologi Camera Coupled X-Ray Detector (CCXD) dengan performa tinggi untuk kebutuhan pelayanan kesehatan.']
                        ]
                    ],
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'Perangkat mendukung format DICOM, memiliki resolusi 12MP, dan dirancang untuk implementasi klinis yang efisien.']
                        ]
                    ]
                ],
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
                'content_json' => [
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'Madeena terus memperkuat kolaborasi dengan fasilitas pelayanan kesehatan melalui skema kemitraan yang fleksibel.']
                        ]
                    ],
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'Program ini mencakup dukungan instalasi, pelatihan, dan pendampingan operasional agar proses implementasi berjalan optimal.']
                        ]
                    ]
                ],
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
                'content_json' => [
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'Berangkat dari riset Universitas Gadjah Mada, Madeena berkomitmen menghadirkan teknologi kesehatan yang relevan untuk kebutuhan nasional.']
                        ]
                    ],
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'Langkah ini mendorong kemandirian industri alat kesehatan Indonesia melalui inovasi berkelanjutan.']
                        ]
                    ]
                ],
                'category' => 'Inovasi',
                'is_published' => true,
                'published_at' => now()->subDay(),
            ]
        );
        Post::updateOrCreate(
            ['slug' => 'e2e-test-post'],
            [
                'title' => 'E2E Test Post',
                'excerpt' => 'This is an E2E test post.',
                'content_json' => [
                    [
                        'type' => 'heading',
                        'attrs' => ['level' => 2],
                        'content' => [['type' => 'text', 'text' => 'Introduction']]
                    ],
                    [
                        'type' => 'academic-equation',
                        'attrs' => [
                            'data' => [
                                'latex' => 'E = mc^2',
                            ]
                        ]
                    ],
                    [
                        'type' => 'paragraph',
                        'content' => [['type' => 'text', 'text' => 'As seen in [@Fig. 1].']]
                    ]
                ],
                'enable_auto_numbering' => true,
                'category' => 'E2E',
                'is_published' => true,
                'published_at' => now(),
            ]
        );
    }
}
