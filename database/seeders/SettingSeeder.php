<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // Legacy flat keys (kept for backward compatibility with HomeController)
        $flat = [
            ['key' => 'company_name', 'value' => 'PT Madeena Karya Indonesia', 'group' => 'general'],
            ['key' => 'tagline', 'value' => 'Know Sciences, Learn Engineering, Create Technology, Develop Business.', 'group' => 'general'],
        ];

        foreach ($flat as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // New JSON group format used by SiteSettings page and HomepageEditor
        Setting::setJson('contact_info', [
            'email'     => 'madeenajog@gmail.com',
            'phone'     => '+62 821 3811 4011',
            'whatsapp'  => '+62 857 2830 4141',
            'address'   => 'Jl. Lowanu No. 68-72, Sorosutan, Umbulharjo, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55162',
        ]);

        Setting::setJson('social_media', [
            'instagram' => '',
            'linkedin'  => '',
            'youtube'   => '',
        ]);

        Setting::setJson('seo', [
            'meta_title'       => 'PT Madeena Karya Indonesia - Digital Radiography Indonesia',
            'meta_description' => 'PT Madeena Karya Indonesia — produsen alat Digital Direct Radiography (DDR) berbasis teknologi Camera Coupled X-Ray Detector (CCXD) buatan Indonesia. TKDN 57,62%, Izin Edar Kemenkes RI AKD 21501220581.',
        ]);

        Setting::setJson('branding', [
            'logo'            => null,
            'primary_color'   => '#1a365d',
            'secondary_color' => '#2dd4bf',
            'font_family'     => 'Inter',
        ]);

        Setting::setJson('whatsapp_button', [
            'enabled' => true,
            'number'  => '+62 857 2830 4141',
        ]);

        Setting::setJson('nav_custom_links', []);

        // Seed a minimal starter homepage if none exists yet
        if (! Setting::where('key', 'homepage_sections')->exists()) {
            Setting::setJson('homepage_sections', [
                ['type' => 'hero', 'data' => [
                    'section_id'  => 'sec-hero',
                    'show_in_nav' => false,
                    'nav_label'   => 'Beranda',
                    'banners'     => [
                        [
                            'title'       => 'PT Madeena Karya Indonesia',
                            'subtitle'    => 'Know Sciences, Learn Engineering, Create Technology, Develop Business.',
                            'description' => 'Produsen alat Digital Direct Radiography (DDR) berbasis teknologi Camera Coupled X-Ray Detector (CCXD) buatan Indonesia. TKDN 57,62%.',
                            'cta_text'    => 'Lihat Produk Kami',
                            'cta_url'     => '#produk',
                            'image_path'  => null,
                        ],
                    ],
                ]],
                ['type' => 'stats', 'data' => [
                    'section_id'      => 'statistik',
                    'show_in_nav'     => false,
                    'background_style' => 'dark',
                    'stats' => [
                        ['number' => '57.6%', 'label' => 'Nilai TKDN', 'icon' => 'fa-award'],
                        ['number' => '10+', 'label' => 'Tahun Riset UGM', 'icon' => 'fa-microscope'],
                        ['number' => '12', 'label' => 'Megapixel Resolusi', 'icon' => 'fa-camera'],
                        ['number' => '100%', 'label' => 'Dukungan Lokal', 'icon' => 'fa-handshake'],
                    ],
                ]],
                ['type' => 'partners', 'data' => [
                    'section_id'      => 'mitra',
                    'show_in_nav'     => false,
                    'section_title'   => 'Didukung Oleh',
                    'background_style' => 'white',
                    'partners' => [
                        ['logo' => null, 'name' => 'Universitas Gadjah Mada', 'url' => 'https://ugm.ac.id'],
                        ['logo' => null, 'name' => 'Kementerian Kesehatan RI', 'url' => 'https://kemkes.go.id'],
                    ],
                ]],
                ['type' => 'about', 'data' => [
                    'section_id'      => 'tentang',
                    'show_in_nav'     => true,
                    'nav_label'       => 'Tentang Kami',
                    'background_style' => 'light',
                    'company_profile' => 'PT Madeena Karya Indonesia didirikan untuk memfasilitasi hilirisasi inovasi radiografi digital yang dikembangkan oleh Prof. Dr. Gede Bayu Suparta bersama tim riset Universitas Gadjah Mada.',
                    'vision'          => 'Menjadi Duta Teknologi Indonesia dengan menghasilkan teknologi dan produk kesehatan mutakhir untuk masyarakat global.',
                    'mission'         => [
                        ['item' => 'Melakukan hilirisasi perkembangan dan hasil riset serta pengembangan teknologi.'],
                        ['item' => 'Mengkomersialisasikan teknologi hasil riset & pengembangan menjadi produk inovatif yang siap dimanfaatkan masyarakat.'],
                        ['item' => 'Mengembangkan sistem pencitraan untuk memenuhi kebutuhan medis dan industri.'],
                    ],
                    'motto' => 'Know Sciences, Learn Engineering, Create Technology, Develop Business.',
                ]],
                ['type' => 'timeline', 'data' => [
                    'section_id'      => 'sejarah',
                    'show_in_nav'     => false,
                    'section_title'   => 'Jejak Langkah Kami',
                    'background_style' => 'white',
                    'milestones' => [
                        ['year' => '2013-2019', 'title' => 'Riset & Pengembangan', 'description' => 'Mendapat dukungan dana riset dari Kemendiknas, KNRT, dan Ristekdikti.'],
                        ['year' => '2020', 'title' => 'Lisensi Paten UGM', 'description' => 'Mendapatkan lisensi paten No. 5204/UN 1.P/DIT-KAUI/HK/2020.'],
                        ['year' => '2022', 'title' => 'Izin Edar Kemenkes', 'description' => 'Resmi mendapatkan Izin Edar Alat Kesehatan AKD 21501220581.'],
                        ['year' => '2023', 'title' => 'Sertifikat TKDN', 'description' => 'Berhasil mencapai Tingkat Komponen Dalam Negeri sebesar 57,62%.'],
                    ],
                ]],
                ['type' => 'team', 'data' => [
                    'section_id'      => 'tim',
                    'show_in_nav'     => false,
                    'section_title'   => 'Tim Kami',
                    'background_style' => 'light',
                    'members' => [
                        ['photo' => null, 'name' => 'Prof. Dr. Gede Bayu Suparta', 'role' => 'Founder & Komisaris Utama', 'bio' => 'Penemu teknologi CCXD dari Universitas Gadjah Mada.'],
                    ],
                ]],
                ['type' => 'products', 'data' => [
                    'section_id'      => 'produk',
                    'show_in_nav'     => true,
                    'nav_label'       => 'Produk',
                    'section_title'   => 'Produk Inovasi Teknologi Kesehatan',
                    'section_subtitle' => 'Berstandar Nasional, Izin Edar Kemenkes RI',
                ]],
                ['type' => 'current_projects', 'data' => [
                    'section_id'      => 'proyek',
                    'show_in_nav'     => true,
                    'nav_label'       => 'Proyek',
                    'section_title'   => 'Proyek Sedang Berjalan',
                    'section_subtitle' => 'Pengembangan teknologi radiografi terbaru yang sedang dalam tahap riset dan hilirisasi',
                    'background_style' => 'light',
                    'projects' => [
                        ['title' => 'DDR Dental (Panoramic)', 'description' => 'Pengembangan mesin radiografi gigi panoramik dengan dosis radiasi rendah untuk klinik gigi lokal.', 'progress' => 65],
                        ['title' => 'Mobile X-Ray AI Analysis', 'description' => 'Integrasi kecerdasan buatan untuk deteksi dini tuberkulosis secara otomatis pada hasil rontgen dada.', 'progress' => 40],
                        ['title' => 'DDR Veterinary', 'description' => 'Sistem radiografi digital khusus hewan yang disesuaikan untuk kebutuhan klinik hewan kecil dan menengah.', 'progress' => 85],
                    ],
                ]],
                ['type' => 'project_investment', 'data' => [
                    'section_id'      => 'investasi',
                    'show_in_nav'     => true,
                    'nav_label'       => 'Investasi',
                    'section_title'   => 'Peluang Kemitraan & Investasi Proyek Alkes',
                    'description'     => 'PT Madeena Karya Indonesia membuka peluang kemitraan bagi investor yang ingin berkontribusi dalam kemandirian alat kesehatan nasional. Proyek prioritas saat ini adalah pengembangan lini produksi massal untuk varian DDR Mobile Bedside.',
                    'background_style' => 'dark',
                    'target_funding'  => 'Rp 5.000.000.000',
                    'roi'             => '12-15% p.a (Estimasi)',
                    'highlights'      => [
                        ['item' => 'Pasar terjamin (E-Katalog Kemenkes)'],
                        ['item' => 'Kebijakan pemerintah mewajibkan alat kesehatan ber-TKDN'],
                        ['item' => 'Didukung penuh oleh LPPT UGM'],
                    ],
                    'button_text'     => 'Unduh Pitch Deck',
                    'button_url'      => '#kontak',
                ]],
                ['type' => 'pricing', 'data' => [
                    'section_id'      => 'harga',
                    'show_in_nav'     => false,
                    'section_title'   => 'Paket Instalasi Radiografi',
                    'background_style' => 'light',
                    'plans' => [
                        ['name' => 'Paket Bedside', 'price' => 'Hubungi Kami', 'is_featured' => false, 'button_text' => 'Konsultasi', 'button_url' => '#kontak', 'features' => [['item' => 'DDR Madeena HF100B-MDN'], ['item' => 'Mobile Bedside Stand'], ['item' => 'Instalasi & Pelatihan']]],
                        ['name' => 'Paket Thorax', 'price' => 'Hubungi Kami', 'is_featured' => true, 'button_text' => 'Konsultasi', 'button_url' => '#kontak', 'features' => [['item' => 'DDR Madeena HF100B-MDN'], ['item' => 'Thorax Vertical Stand'], ['item' => 'PC + Monitor 32" 4K'], ['item' => 'Instalasi & Pelatihan']]],
                    ],
                ]],
                ['type' => 'legalities', 'data' => [
                    'section_id'      => 'legalitas',
                    'show_in_nav'     => true,
                    'nav_label'       => 'Legalitas',
                    'section_title'   => 'Legalitas Formal',
                    'section_subtitle' => 'Seluruh produk PT Madeena Karya Indonesia telah memenuhi persyaratan regulasi',
                    'background_style' => 'dark',
                    'certificates'    => [
                        ['icon' => 'fa-building', 'title' => 'Surat Izin Berusaha Berbasis Risiko', 'detail' => 'NIB 9120106900275'],
                        ['icon' => 'fa-file-contract', 'title' => 'Lisensi Paten UGM', 'detail' => 'No. 5204/UN 1.P/DIT-KAUI/HK/2020'],
                        ['icon' => 'fa-certificate', 'title' => 'Sertifikat Izin Edar Kemenkes RI', 'detail' => 'AKD 21501220581'],
                        ['icon' => 'fa-award', 'title' => 'Sertifikat Capaian TKDN 57,62%', 'detail' => 'No. 8110/SJ-IND.8/TKDN/9/2023'],
                        ['icon' => 'fa-shield-alt', 'title' => 'Sertifikat CPAKB Kemenkes RI', 'detail' => 'PB-UMKU 91201069002750000001'],
                    ],
                ]],
                ['type' => 'faq', 'data' => [
                    'section_id'      => 'faq',
                    'show_in_nav'     => false,
                    'section_title'   => 'Pertanyaan Umum',
                    'background_style' => 'light',
                    'faqs' => [
                        ['question' => 'Apakah DDR Madeena sudah memiliki Izin Edar?', 'answer' => 'Ya, DDR Madeena telah mendapatkan Izin Edar dari Kemenkes RI dengan nomor AKD 21501220581.'],
                        ['question' => 'Berapa nilai TKDN produk ini?', 'answer' => 'Produk DDR kami memiliki sertifikasi Nilai Tingkat Komponen Dalam Negeri (TKDN) sebesar 57,62%.'],
                        ['question' => 'Apakah PT Madeena menyediakan layanan purna jual?', 'answer' => 'Tentu. Kami memberikan garansi sparepart 1 tahun, dukungan sparepart 3 tahun, dan garansi bodi 5 tahun, ditambah dengan teknisi lokal yang siap membantu.'],
                    ],
                ]],
                ['type' => 'blog', 'data' => [
                    'section_id'    => 'blog',
                    'show_in_nav'   => false,
                    'nav_label'     => 'Blog',
                    'section_title' => 'Blog & Artikel Terbaru',
                    'posts_count'   => 3,
                ]],
                ['type' => 'cta', 'data' => [
                    'section_id'      => 'cta',
                    'show_in_nav'     => false,
                    'background_style' => 'gradient',
                    'title'           => 'Siap Mengupgrade Fasilitas Radiologi Anda?',
                    'subtitle'        => 'Dukung kemandirian alat kesehatan nasional dengan menggunakan produk buatan Indonesia.',
                    'button_text'     => 'Hubungi Kami Sekarang',
                    'button_url'      => '#kontak',
                ]],
                ['type' => 'map', 'data' => [
                    'section_id'      => 'lokasi',
                    'show_in_nav'     => false,
                    'section_title'   => 'Temukan Kami',
                    'address'         => 'Jl. Lowanu No. 68-72, Sorosutan, Umbulharjo, Kota Yogyakarta',
                    'background_style' => 'white',
                    'embed_url'       => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.7533036814234!2d110.3756209!3d-7.8158913!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a57a159f8ea73%3A0xc3af7a5e8248880a!2sPT.%20Madeena%20Karya%20Indonesia!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid',
                ]],
                ['type' => 'contact', 'data' => [
                    'section_id'     => 'kontak',
                    'show_in_nav'    => true,
                    'nav_label'      => 'Kontak',
                    'section_title'  => 'Hubungi Kami',
                    'section_subtitle' => 'Untuk informasi lebih lanjut mengenai produk dan layanan PT Madeena Karya Indonesia, silakan menghubungi kami',
                ]],
            ]);
        }
    }
}
