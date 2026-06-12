<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::updateOrCreate(
            ['slug' => 'tentang'],
            [
                'title' => 'Tentang Kami',
                'content_json' => [
                    [
                        'type' => 'free_text',
                        'data' => [
                            'content' => [
                                [
                                    'type' => 'academic-paragraph',
                                    'data' => [],
                                    'content' => [
                                        ['type' => 'text', 'text' => 'PT Madeena Karya Indonesia didirikan untuk memfasilitasi hilirisasi inovasi radiografi digital yang dikembangkan oleh Prof. Dr. Gede Bayu Suparta bersama tim riset Universitas Gadjah Mada.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        );
    }
}
