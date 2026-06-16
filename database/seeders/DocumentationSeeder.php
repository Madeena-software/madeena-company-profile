<?php

namespace Database\Seeders;

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
                            'type' => 'academic-equation',
                            'attrs' => [
                                'latex' => 'E = mc^2',
                                'ref_id' => 'eq-1',
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
    }
}
