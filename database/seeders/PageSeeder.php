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
                'content' => '<h2>Profil, Visi &amp; Misi</h2><p>PT Madeena Karya Indonesia didirikan untuk memfasilitasi hilirisasi inovasi radiografi digital yang dikembangkan oleh <strong>Prof. Dr. Gede Bayu Suparta</strong> bersama tim riset Universitas Gadjah Mada. Perusahaan ini merupakan respons nyata terhadap tantangan hilirisasi dan komersialisasi teknologi hasil riset perguruan tinggi menjadi produk inovasi komersial yang siap dimanfaatkan masyarakat luas.</p><p>Dengan dukungan dana riset dari Kemendiknas, KNRT, dan Ristekdikti pada periode 2013–2019, PT Madeena berhasil mengembangkan Madeena X-Ray Medical Diagnostic Equipment yang telah memperoleh Izin Edar Kemenkes RI No. <strong>AKD 21501220581</strong>.</p><h3>Visi</h3><p><em>&ldquo;Menjadi Duta Teknologi Indonesia dengan menghasilkan teknologi dan produk kesehatan mutakhir untuk masyarakat global.&rdquo;</em></p><h3>Misi</h3><ol><li>Melakukan hilirisasi perkembangan dan hasil riset serta pengembangan teknologi.</li><li>Mengkomersialisasikan teknologi hasil riset &amp; pengembangan menjadi produk inovatif yang siap dimanfaatkan masyarakat.</li><li>Mengembangkan sistem pencitraan untuk memenuhi kebutuhan medis dan industri.</li></ol>',
            ]
        );
    }
}
