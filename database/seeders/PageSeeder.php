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
                'summary' => 'PT Madeena Karya Indonesia didirikan untuk memfasilitasi hilirisasi inovasi radiografi digital yang dikembangkan oleh Prof. Dr. Gede Bayu Suparta bersama tim riset UGM.',
                'show_in_header' => true,
                'show_in_footer' => true,
                'enable_auto_numbering' => false,
                'content_json' => [
                    [
                        'type' => 'free_text',
                        'data' => [
                            'content' => [
                                'type' => 'doc',
                                'content' => [
                                    [
                                        'type' => 'heading',
                                        'attrs' => ['level' => 2],
                                        'content' => [['type' => 'text', 'text' => 'Profil, Visi & Misi']]
                                    ],
                                    [
                                        'type' => 'paragraph',
                                        'content' => [['type' => 'text', 'text' => 'PT Madeena Karya Indonesia didirikan untuk memfasilitasi hilirisasi inovasi radiografi digital yang dikembangkan oleh Prof. Dr. Gede Bayu Suparta bersama tim riset Universitas Gadjah Mada. Perusahaan ini merupakan respons nyata terhadap tantangan hilirisasi dan komersialisasi teknologi hasil riset perguruan tinggi menjadi produk inovasi komersial yang siap dimanfaatkan masyarakat luas.']]
                                    ],
                                    [
                                        'type' => 'paragraph',
                                        'content' => [['type' => 'text', 'text' => 'Dengan dukungan dana riset dari Kemendiknas, KNRT, dan Ristekdikti pada periode 2013-2019, PT Madeena berhasil mengembangkan Madeena X-Ray Medical Diagnostic Equipment yang telah memperoleh Izin Edar Kemenkes RI No. AKD 21501220581.']]
                                    ],
                                    [
                                        'type' => 'heading',
                                        'attrs' => ['level' => 2],
                                        'content' => [['type' => 'text', 'text' => 'Visi']]
                                    ],
                                    [
                                        'type' => 'paragraph',
                                        'content' => [['type' => 'text', 'text' => '"Menjadi Duta Teknologi Indonesia dengan menghasilkan teknologi dan produk kesehatan mutakhir untuk masyarakat global."']]
                                    ],
                                    [
                                        'type' => 'heading',
                                        'attrs' => ['level' => 2],
                                        'content' => [['type' => 'text', 'text' => 'Misi']]
                                    ],
                                    [
                                        'type' => 'bulletList',
                                        'content' => [
                                            [
                                                'type' => 'listItem',
                                                'content' => [
                                                    [
                                                        'type' => 'paragraph',
                                                        'content' => [['type' => 'text', 'text' => 'Melakukan hilirisasi perkembangan dan hasil riset serta pengembangan teknologi.']]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'type' => 'listItem',
                                                'content' => [
                                                    [
                                                        'type' => 'paragraph',
                                                        'content' => [['type' => 'text', 'text' => 'Mengkomersialisasikan teknologi hasil riset & pengembangan menjadi produk inovatif yang siap dimanfaatkan masyarakat.']]
                                                    ]
                                                ]
                                            ],
                                            [
                                                'type' => 'listItem',
                                                'content' => [
                                                    [
                                                        'type' => 'paragraph',
                                                        'content' => [['type' => 'text', 'text' => 'Mengembangkan sistem pencitraan untuk memenuhi kebutuhan medis dan industri.']]
                                                    ]
                                                ]
                                            ],
                                        ]
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        );
    }
}
