<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\GuestMessage;
use App\Models\Post;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@madeena.local'],
            [
                'name' => 'Prof. Gede Bayu Suparta',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Make sure it's admin
        $admin->role = 'admin';
        $admin->save();

        Post::firstOrCreate(
            ['slug' => 'analisis-morfologi-permukaan-detektor-ddr'],
            [
                'title' => 'Analisis Morfologi Permukaan Detektor DDR menggunakan SEM',
                'user_id' => $admin->id,
                'category' => 'Penelitian',
                'placement' => 'Artikel',
                'is_published' => true,
                'published_at' => now(),
                'abstract' => 'Penelitian ini menganalisis morfologi permukaan detektor Digital Direct Radiography (DDR) menggunakan instrumen Scanning Electron Microscope (SEM) pasca pemanasan 500°C.',
                'keywords' => ['fisika', 'material', 'semikonduktor', 'DDR', 'radiografi'],
                'content_language' => 'id',
                'content_json' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'heading',
                            'attrs' => ['level' => 2],
                            'content' => [['type' => 'text', 'text' => 'Pendahuluan']]
                        ],
                        [
                            'type' => 'paragraph',
                            'content' => [
                                ['type' => 'text', 'text' => 'Seiring dengan perkembangan teknologi pencitraan medis, sistem radiografi digital menjadi standar utama dalam diagnostik non-destruktif. Berdasarkan penelitian sebelumnya [@1], detektor DDR memiliki efisiensi yang lebih tinggi. Pembahasan selengkapnya dapat dilihat pada ']
                            ]
                        ],
                        [
                            'type' => 'academic-equation',
                            'attrs' => [
                                'latex' => 'E = mc^2',
                                'ref_id' => 'eq-1',
                            ]
                        ],
                        [
                            'type' => 'academic-table',
                            'attrs' => [
                                'caption' => 'Hasil pengukuran suhu sampel',
                                'table_html' => '<table><thead><tr><th>Parameter</th><th>Nilai</th></tr></thead><tbody><tr><td>Suhu</td><td>500°C</td></tr></tbody></table>',
                                'ref_id' => 'tbl-1',
                            ]
                        ],
                        [
                            'type' => 'academic-figure',
                            'attrs' => [
                                'image' => ['posts/placeholder.png'], // Just a placeholder array for filament fileupload
                                'caption' => 'Ilustrasi SEM pada detektor',
                                'ref_id' => 'fig-1',
                                'size' => 'medium'
                            ]
                        ],
                        [
                            'type' => 'academic-references',
                            'attrs' => [
                                'references' => [
                                    [
                                        'authors' => 'Suparta, G.B. dkk.',
                                        'title' => 'Sistem Radiografi Digital',
                                        'journal' => 'UGM Press',
                                        'year' => '2020',
                                        'volume' => '1',
                                        'pages' => '1-50',
                                        'doi' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        Product::firstOrCreate(
            ['slug' => 'ddr-pro-series'],
            [
                'name' => 'DDR Pro Series',
                'tagline' => 'Digital Radiography System for NDT',
                'specifications' => [
                    'Resolusi' => '100 mikron',
                    'Tegangan Operasi' => '50-150 kV',
                    'Aplikasi' => 'NDT, Industri, Medis',
                ],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $event = Event::firstOrCreate(
            ['slug' => 'simposium-fisika-nasional-2026'],
            [
                'name' => 'Simposium Fisika Nasional 2026',
                'description' => 'Simposium tahunan inovasi fisika dan radiografi',
                'is_active' => true,
                'starts_at' => now()->addDays(5),
                'ends_at' => now()->addDays(7),
            ]
        );

        GuestMessage::firstOrCreate(
            ['email' => 'tamu@contoh.com'],
            [
                'event_id' => $event->id,
                'name' => 'Dr. Budi Santoso',
                'organization' => 'Universitas Gadjah Mada',
                'position' => 'Dosen Peneliti',
                'phone' => '081234567890',
                'kesan_dan_pesan' => 'Acara yang sangat bermanfaat untuk perkembangan radiografi digital di Indonesia.',
                'is_visible' => true,
            ]
        );
    }
}
