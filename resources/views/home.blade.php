@extends('layouts.app')

@section('title', $settings['meta_title'] ?? 'PT Madeena Karya Indonesia - Digital Radiography Indonesia')

@section('content')

<section id="banner" class="relative min-h-screen flex items-center bg-gradient-to-br from-madeena-blue via-madeena-blue to-teal-800 pt-20">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style='background-image: url("data:image/svg+xml,%3Csvg width=%2760%27 height=%2760%27 viewBox=%270 0 60 60%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cg fill=%27none%27 fill-rule=%27evenodd%27%3E%3Cg fill=%27%23ffffff%27 fill-opacity=%270.4%27%3E%3Cpath d=%27M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%27/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")'></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                @if($banners->isNotEmpty())
                @php $hero = $banners->first(); @endphp
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    {{ $hero->title }}
                </h1>
                @if($hero->subtitle)
                <p class="text-xl md:text-2xl font-medium text-madeena-teal mb-6">{{ $hero->subtitle }}</p>
                @endif
                @if($hero->description)
                <p class="text-white/80 text-lg leading-relaxed mb-8">{{ $hero->description }}</p>
                @endif
                @if($hero->cta_text && $hero->cta_url)
                <a href="{{ $hero->cta_url }}" class="btn-primary text-lg">{{ $hero->cta_text }}</a>
                @endif
                @else
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                    PT MADEENA<br><span class="text-madeena-teal">Karya Indonesia</span>
                </h1>
                <p class="text-xl font-medium text-madeena-teal mb-6">Know Sciences, Learn Engineering, Create Technology, Develop Business.</p>
                <p class="text-white/80 text-lg leading-relaxed mb-8">
                    Produsen alat Digital Direct Radiography (DDR) berbasis teknologi Camera Coupled X-Ray Detector (CCXD) buatan Indonesia. TKDN 57,62%.
                </p>
                <a href="#produk" class="btn-primary text-lg">Lihat Produk Kami</a>
                @endif
            </div>
            <div class="flex justify-center lg:justify-end">
                <div class="relative">
                    <div class="absolute -inset-4 bg-madeena-teal/20 rounded-full blur-2xl"></div>
                    <img src="{{ asset('images/logo.png') }}"
                        alt="Logo PT Madeena Karya Indonesia"
                        class="relative w-64 h-64 md:w-80 md:h-80 object-contain drop-shadow-2xl">
                </div>
            </div>
        </div>
    </div>
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <a href="#riset" class="text-white/60 hover:text-white transition-colors">
            <i class="fas fa-chevron-down text-2xl"></i>
        </a>
    </div>
</section>

<section id="riset" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block bg-madeena-teal/10 text-madeena-teal font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Riset &amp; Inovasi</span>
                <h2 class="section-title">Teknologi Camera Coupled X-Ray Detector (CCXD)</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    PT Madeena Karya Indonesia didirikan untuk memfasilitasi hilirisasi inovasi radiografi digital yang dikembangkan oleh <strong>Prof. Dr. Gede Bayu Suparta</strong> bersama tim riset Universitas Gadjah Mada. Teknologi ini merupakan respons terhadap kebutuhan komersialisasi hasil riset perguruan tinggi menjadi produk inovasi yang siap dimanfaatkan masyarakat.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    DDR Madeena menggunakan detektor CCXD beresolusi tinggi dengan konsumsi daya rendah namun menghasilkan kualitas citra yang optimal. Diproduksi di Indonesia dengan Tingkat Komponen Dalam Negeri (TKDN) sebesar <strong class="text-madeena-blue">57,62%</strong>.
                </p>
                <div class="mt-8 grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-madeena-teal">57.62%</div>
                        <div class="text-sm text-gray-500 mt-1">TKDN</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-madeena-teal">12MP</div>
                        <div class="text-sm text-gray-500 mt-1">Resolusi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-madeena-teal">16-bit</div>
                        <div class="text-sm text-gray-500 mt-1">DICOM</div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="aspect-video bg-gray-100 rounded-2xl overflow-hidden shadow-xl">
                    <img src="{{ asset('images/banner.jpg') }}" alt="Riset &amp; Inovasi Madeena" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </div>
</section>

<section id="produk" class="py-20 bg-madeena-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-madeena-teal/10 text-madeena-teal font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Produk Kami</span>
            <h2 class="section-title">Produk Inovasi Teknologi Kesehatan</h2>
            <p class="section-subtitle">Berstandar Nasional, Izin Edar Kemenkes RI</p>
        </div>

        @if($products->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($products as $product)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                @if($product->image_path)
                <div class="aspect-video bg-gray-50 overflow-hidden">
                    <img src="{{ Storage::url($product->image_path) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-300">
                </div>
                @else
                <div class="aspect-video bg-gradient-to-br from-madeena-blue to-madeena-teal flex items-center justify-center">
                    <i class="fas fa-x-ray text-white text-5xl opacity-50"></i>
                </div>
                @endif
                <div class="p-6">
                    <h3 class="text-xl font-bold text-madeena-blue mb-2">{{ $product->name }}</h3>
                    @if($product->tagline)
                    <p class="text-madeena-teal font-medium text-sm mb-3">{{ $product->tagline }}</p>
                    @endif
                    @if($product->description)
                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">{!! strip_tags($product->description) !!}</p>
                    @endif
                    <a href="{{ route('product.show', $product->slug) }}"
                        class="mt-4 inline-block text-madeena-teal font-semibold hover:text-madeena-blue transition-colors text-sm">
                        Selengkapnya <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="aspect-video bg-gray-50 overflow-hidden flex items-center justify-center p-4">
                    <img src="{{ asset('images/product-ddr.png') }}" alt="DDR Madeena HF100B-MDN" class="w-full h-full object-contain">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-madeena-blue mb-2">DDR Madeena HF100B-MDN</h3>
                    <p class="text-madeena-teal font-medium text-sm mb-3">Direct Digital Radiography buatan Indonesia &mdash; TKDN 57,62%</p>
                    <p class="text-gray-600 text-sm leading-relaxed">Perangkat Direct Digital Radiography berbasis teknologi Camera Coupled X-Ray Detector (CCXD), produksi dalam negeri. Detektor 12MP 40&times;30 cm, format DICOM 16-bit. Izin Edar Kemenkes RI No. AKD 21501220581.</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="aspect-video bg-gradient-to-br from-madeena-blue to-madeena-teal flex items-center justify-center">
                    <i class="fas fa-hospital text-white text-5xl opacity-50"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-madeena-blue mb-2">Solusi Ruang Radiografi</h3>
                    <p class="text-madeena-teal font-medium text-sm mb-3">Paket instalasi ruang radiografi lengkap</p>
                    <p class="text-gray-600 text-sm leading-relaxed">Paket pengadaan dan instalasi ruang radiografi yang mencakup peralatan utama, aksesori pendukung, pemasangan, serta pelatihan operasional bagi tenaga teknis.</p>
                </div>
            </div>
        </div>
        @endif

        <div class="mt-16">
            <h3 class="text-2xl font-bold text-madeena-blue text-center mb-10">Keunggulan Teknologi</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach([
                ['icon' => 'fa-network-wired', 'title' => 'Sistem Teleradiologi', 'desc' => 'Transmisi dan konsultasi citra radiologi secara daring untuk mendukung efisiensi layanan diagnostik jarak jauh.'],
                ['icon' => 'fa-brain', 'title' => 'Antarmuka AI Diagnostik', 'desc' => 'Integrasi kecerdasan buatan untuk membantu analisis dan interpretasi citra radiologi secara otomatis.'],
                ['icon' => 'fa-certificate', 'title' => 'Izin Edar Kemenkes RI', 'desc' => 'Produk telah mendapatkan izin edar resmi dari Kementerian Kesehatan RI.'],
                ['icon' => 'fa-handshake', 'title' => 'Program Kemitraan', 'desc' => 'Skema kemitraan pengadaan yang fleksibel untuk berbagai jenis fasilitas pelayanan kesehatan.'],
                ] as $feat)
                <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 bg-madeena-teal/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas {{ $feat['icon'] }} text-madeena-teal text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-madeena-blue mb-2 text-sm">{{ $feat['title'] }}</h4>
                    <p class="text-gray-500 text-xs leading-relaxed">{{ $feat['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-10 text-center">
            <a href="#kontak" class="btn-primary">Konsultasi Produk</a>
        </div>
    </div>
</section>

<section id="tentang" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-madeena-teal/10 text-madeena-teal font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Tentang Kami</span>
            <h2 class="section-title">Profil, Visi &amp; Misi</h2>
            <p class="section-subtitle">PT Madeena Karya Indonesia &mdash; Inovasi Teknologi Alat Kesehatan Indonesia</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <div>
                <div class="bg-madeena-light rounded-2xl p-8 mb-8">
                    <h3 class="text-xl font-bold text-madeena-blue mb-4">Tentang Perusahaan</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        PT Madeena Karya Indonesia didirikan untuk memfasilitasi hilirisasi inovasi radiografi digital yang dikembangkan oleh <strong>Prof. Dr. Gede Bayu Suparta</strong> bersama tim riset Universitas Gadjah Mada. Perusahaan ini merupakan respons nyata terhadap tantangan hilirisasi dan komersialisasi teknologi hasil riset perguruan tinggi menjadi produk inovasi komersial yang siap dimanfaatkan masyarakat luas.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Dengan dukungan dana riset dari Kemendiknas, KNRT, dan Ristekdikti pada periode 2013&ndash;2019, PT Madeena berhasil mengembangkan Madeena X-Ray Medical Diagnostic Equipment yang telah memperoleh Izin Edar Kemenkes RI No. <strong>AKD 21501220581</strong>.
                    </p>
                </div>
                <div class="bg-madeena-blue rounded-2xl p-8 text-white">
                    <blockquote class="text-xl font-medium italic text-white/90 mb-4">
                        &ldquo;Know Sciences, Learn Engineering, Create Technology, Develop Business.&rdquo;
                    </blockquote>
                    <p class="text-white/60 text-sm">Kredo PT Madeena Karya Indonesia</p>
                </div>
            </div>
            <div>
                <div class="space-y-6">
                    <div class="border-l-4 border-madeena-teal pl-6">
                        <h3 class="text-xl font-bold text-madeena-blue mb-3">Visi</h3>
                        <p class="text-gray-600 italic">&ldquo;Menjadi Duta Teknologi Indonesia dengan menghasilkan teknologi dan produk kesehatan mutakhir untuk masyarakat global.&rdquo;</p>
                    </div>
                    <div class="border-l-4 border-madeena-blue pl-6">
                        <h3 class="text-xl font-bold text-madeena-blue mb-3">Misi</h3>
                        <ol class="list-decimal list-inside space-y-2 text-gray-600">
                            <li>Melakukan hilirisasi perkembangan dan hasil riset serta pengembangan teknologi.</li>
                            <li>Mengkomersialisasikan teknologi hasil riset &amp; pengembangan menjadi produk inovatif yang siap dimanfaatkan masyarakat.</li>
                            <li>Mengembangkan sistem pencitraan untuk memenuhi kebutuhan medis dan industri.</li>
                        </ol>
                    </div>
                    <div class="flex justify-center mt-8">
                        <img src="{{ asset('images/logo-no-bg.png') }}" alt="Logo Madeena" class="w-48 h-48 object-contain">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="legalitas" class="py-20 bg-madeena-blue text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-white/10 text-white font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Legalitas</span>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Legalitas Formal</h2>
            <p class="text-white/70 text-lg">Seluruh produk PT Madeena Karya Indonesia telah memenuhi persyaratan regulasi dan sertifikasi yang berlaku</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
            ['icon' => 'fa-building', 'title' => 'Surat Izin Berusaha Berbasis Risiko', 'detail' => 'NIB 9120106900275'],
            ['icon' => 'fa-file-contract', 'title' => 'Lisensi Paten UGM', 'detail' => 'No. 5204/UN 1.P/DIT-KAUI/HK/2020'],
            ['icon' => 'fa-certificate', 'title' => 'Sertifikat Izin Edar Kemenkes RI', 'detail' => 'AKD 21501220581'],
            ['icon' => 'fa-award', 'title' => 'Sertifikat Capaian TKDN 57,62%', 'detail' => 'No. 8110/SJ-IND.8/TKDN/9/2023'],
            ['icon' => 'fa-shield-alt', 'title' => 'Sertifikat CPAKB Kemenkes RI', 'detail' => 'PB-UMKU 91201069002750000001'],
            ['icon' => 'fa-university', 'title' => 'Surat Rekomendasi FK Undiksha', 'detail' => 'No. 1632/UN48.24/TU/2024'],
            ] as $cert)
            <div class="bg-white/10 backdrop-blur rounded-xl p-6 border border-white/20 hover:bg-white/20 transition-colors">
                <div class="w-12 h-12 bg-madeena-teal/30 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas {{ $cert['icon'] }} text-madeena-teal text-xl"></i>
                </div>
                <h3 class="font-bold text-white mb-2">{{ $cert['title'] }}</h3>
                <p class="text-white/70 text-sm">{{ $cert['detail'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

@if($posts->isNotEmpty())
<section id="berita" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-madeena-teal/10 text-madeena-teal font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Berita</span>
            <h2 class="section-title">Berita &amp; Artikel Terbaru</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($posts as $post)
            <article class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                @if($post->cover_image)
                <div class="aspect-video overflow-hidden">
                    <img src="{{ Storage::url($post->cover_image) }}"
                        alt="{{ $post->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                @endif
                <div class="p-6">
                    @if($post->category)
                    <span class="inline-block text-xs font-semibold text-madeena-teal bg-madeena-teal/10 px-2 py-1 rounded mb-3">{{ $post->category }}</span>
                    @endif
                    <h3 class="text-lg font-bold text-madeena-blue mb-2 group-hover:text-madeena-teal transition-colors">
                        <a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a>
                    </h3>
                    @if($post->excerpt)
                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between">
                        @if($post->published_at)
                        <span class="text-gray-400 text-xs">{{ $post->published_at->format('d M Y') }}</span>
                        @endif
                        <a href="{{ route('post.show', $post->slug) }}"
                            class="text-madeena-teal font-semibold text-sm hover:text-madeena-blue transition-colors">
                            Baca <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section id="kontak" class="py-20 bg-gradient-to-br from-madeena-blue to-teal-800 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block bg-white/10 text-white font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Kontak</span>
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Hubungi Kami</h2>
        <p class="text-white/80 text-lg mb-10">Untuk informasi lebih lanjut mengenai produk dan layanan PT Madeena Karya Indonesia, silakan menghubungi kami melalui saluran berikut</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <a href="mailto:{{ $settings['email'] ?? 'madeenajog@gmail.com' }}"
                class="bg-white/10 hover:bg-white/20 transition-colors rounded-xl p-5 text-center border border-white/20">
                <i class="fas fa-envelope text-2xl text-madeena-teal mb-3 block"></i>
                <div class="text-sm font-medium">Email</div>
                <div class="text-white/70 text-xs mt-1">{{ $settings['email'] ?? 'madeenajog@gmail.com' }}</div>
            </a>
            <a href="tel:{{ preg_replace('/\s/', '', $settings['phone'] ?? '+6282138114011') }}"
                class="bg-white/10 hover:bg-white/20 transition-colors rounded-xl p-5 text-center border border-white/20">
                <i class="fas fa-phone text-2xl text-madeena-teal mb-3 block"></i>
                <div class="text-sm font-medium">Telepon</div>
                <div class="text-white/70 text-xs mt-1">{{ $settings['phone'] ?? '+62 821 3811 4011' }}</div>
            </a>
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp'] ?? '6285728304141') }}" target="_blank"
                class="bg-white/10 hover:bg-white/20 transition-colors rounded-xl p-5 text-center border border-white/20">
                <i class="fab fa-whatsapp text-2xl text-madeena-teal mb-3 block"></i>
                <div class="text-sm font-medium">WhatsApp</div>
                <div class="text-white/70 text-xs mt-1">{{ $settings['whatsapp'] ?? '+62 857 2830 4141' }}</div>
            </a>
            <div class="bg-white/10 rounded-xl p-5 text-center border border-white/20">
                <i class="fas fa-map-marker-alt text-2xl text-madeena-teal mb-3 block"></i>
                <div class="text-sm font-medium">Alamat</div>
                <div class="text-white/70 text-xs mt-1">{{ $settings['address'] ?? 'Jl. Lowanu No. 68-72, Yogyakarta' }}</div>
            </div>
        </div>
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp'] ?? '6285728304141') }}" target="_blank"
            class="btn-primary text-lg inline-flex items-center gap-2">
            <i class="fab fa-whatsapp"></i> Chat via WhatsApp
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
@endpush