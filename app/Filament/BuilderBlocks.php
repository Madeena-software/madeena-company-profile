<?php

namespace App\Filament;

use App\Filament\RichEditorBlocks\EquationBlock;
use App\Filament\RichEditorBlocks\FigureBlock;
use App\Filament\RichEditorBlocks\ReferenceListBlock;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class BuilderBlocks
{
    public static function get(): array
    {
        return [
            self::heroBlock(),
            self::productsBlock(),
            self::featuresBlock(),
            self::aboutBlock(),
            self::legalitiesBlock(),
            self::contactBlock(),
            self::blogBlock(),
            self::galleryBlock(),
            self::testimonialBlock(),
            self::videoBlock(),
            self::freeTextBlock(),
        ];
    }

    private static function getIconOptions(): array
    {
        return [
            'fa-building' => '🏢 Gedung',
            'fa-certificate' => '📜 Sertifikat',
            'fa-award' => '🏆 Penghargaan',
            'fa-shield-alt' => '🛡️ Keamanan',
            'fa-university' => '🏛️ Universitas',
            'fa-file-contract' => '📋 Kontrak',
            'fa-brain' => '🧠 Otak / AI',
            'fa-network-wired' => '🌐 Jaringan',
            'fa-hospital' => '🏥 Rumah Sakit',
            'fa-x-ray' => '🩻 X-Ray',
            'fa-beaker' => '🧪 Laboratorium',
            'fa-microscope' => '🔬 Mikroskop',
            'fa-heartbeat' => '💓 Kesehatan',
            'fa-stethoscope' => '🩺 Stetoskop',
            'fa-handshake' => '🤝 Kemitraan',
            'fa-globe' => '🌍 Global',
            'fa-chart-line' => '📈 Grafik',
            'fa-cogs' => '⚙️ Teknologi',
            'fa-users' => '👥 Tim',
            'fa-phone' => '📞 Telepon',
            'fa-envelope' => '📧 Email',
            'fa-map-marker-alt' => '📍 Lokasi',
            'fa-star' => '⭐ Bintang',
            'fa-check-circle' => '✅ Centang',
            'fa-lightbulb' => '💡 Ide',
            'fa-rocket' => '🚀 Roket',
            'fa-wrench' => '🔧 Alat',
            'fa-camera' => '📷 Kamera',
            'fa-book' => '📖 Buku',
            'fa-graduation-cap' => '🎓 Pendidikan',
        ];
    }

    private static function getNavFields($defaultShow = false, $defaultLabel = ''): array
    {
        return [
            TextInput::make('section_id')
                ->label('ID Bagian (Anchor)')
                ->default(fn() => uniqid('sec-'))
                ->helperText('Otomatis dibuat, tapi bisa diganti untuk link navigasi (#id).'),
            Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default($defaultShow),
            TextInput::make('nav_label')->label('Label Navigasi')->default($defaultLabel),
        ];
    }

    private static function heroBlock(): Block
    {
        return Block::make('hero')
            ->label('🖼️ Hero Banner')
            ->icon('heroicon-o-photo')
            ->schema(array_merge(self::getNavFields(false, 'Beranda'), [
                Repeater::make('banners')
                    ->label('Slide Banner')
                    ->schema([
                        TextInput::make('title')->label('Judul')->required(),
                        TextInput::make('subtitle')->label('Subjudul'),
                        Textarea::make('description')->label('Deskripsi')->rows(3),
                        FileUpload::make('image_path')->label('Gambar Banner')->image()->disk('public')->directory('banners'),
                        TextInput::make('cta_text')->label('Teks Tombol'),
                        TextInput::make('cta_url')->label('URL Tombol'),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->addActionLabel('+ Tambah Slide')
                    ->helperText('Tambahkan beberapa slide untuk carousel banner.'),
            ]));
    }

    private static function productsBlock(): Block
    {
        return Block::make('products')
            ->label('📦 Produk')
            ->icon('heroicon-o-beaker')
            ->schema(array_merge(self::getNavFields(true, 'Produk'), [
                TextInput::make('section_title')->label('Judul Bagian')->default('Produk Inovasi Teknologi Kesehatan'),
                TextInput::make('section_subtitle')->label('Subjudul')->default('Berstandar Nasional, Izin Edar Kemenkes RI'),
            ]))
            ;
    }

    private static function aboutBlock(): Block
    {
        return Block::make('about')
            ->label('🏢 Tentang Kami')
            ->icon('heroicon-o-building-office')
            ->schema(array_merge(self::getNavFields(true, 'Tentang'), [
                Select::make('background_style')->label('Gaya Latar')->options([
                    'white' => 'Putih', 'light' => 'Abu-Abu Muda', 'dark' => 'Gelap', 'gradient' => 'Gradien',
                ])->default('white'),
                Textarea::make('company_profile')->label('Profil Perusahaan')->rows(5)
                    ->helperText('Ceritakan tentang perusahaan Anda.'),
                TextInput::make('vision')->label('Visi')
                    ->placeholder('Menjadi Duta Teknologi Indonesia...'),
                Repeater::make('mission')
                    ->label('Misi')
                    ->simple(TextInput::make('item')->label('Poin Misi')->required())
                    ->addActionLabel('+ Tambah Misi')
                    ->helperText('Setiap poin misi akan ditampilkan sebagai daftar bernomor.'),
                TextInput::make('motto')->label('Motto / Kredo')
                    ->placeholder('Know Sciences, Learn Engineering, Create Technology, Develop Business.'),
            ]));
    }

    private static function featuresBlock(): Block
    {
        return Block::make('features')
            ->label('⭐ Keunggulan')
            ->icon('heroicon-o-star')
            ->schema(array_merge(self::getNavFields(false, 'Keunggulan'), [
                TextInput::make('section_title')->label('Judul Bagian')->default('Keunggulan Teknologi'),
                Repeater::make('items')
                    ->label('Kartu Keunggulan')
                    ->schema([
                        Select::make('icon')
                            ->label('Ikon')
                            ->options(self::getIconOptions())
                            ->searchable()
                            ->helperText('Pilih ikon yang sesuai.'),
                        TextInput::make('title')->label('Judul')->required(),
                        Textarea::make('description')->label('Deskripsi')->rows(2),
                    ])
                    ->columns(1)
                    ->reorderable()
                    ->addActionLabel('+ Tambah Keunggulan'),
            ]));
    }

    private static function legalitiesBlock(): Block
    {
        return Block::make('legalities')
            ->label('📜 Sertifikasi')
            ->icon('heroicon-o-shield-check')
            ->schema(array_merge(self::getNavFields(true, 'Legalitas'), [
                TextInput::make('section_title')->label('Judul Bagian')->default('Legalitas Formal'),
                TextInput::make('section_subtitle')->label('Subjudul'),
                Select::make('background_style')->label('Gaya Latar')->options([
                    'white' => 'Putih', 'light' => 'Abu-Abu Muda', 'dark' => 'Gelap', 'gradient' => 'Gradien',
                ])->default('dark'),
                Repeater::make('certificates')
                    ->label('Daftar Sertifikasi')
                    ->schema([
                        Select::make('icon')
                            ->label('Ikon')
                            ->options(self::getIconOptions())
                            ->searchable(),
                        TextInput::make('title')->label('Nama Sertifikasi')->required(),
                        TextInput::make('detail')->label('Nomor / Detail')->required(),
                    ])
                    ->columns(1)
                    ->reorderable()
                    ->addActionLabel('+ Tambah Sertifikasi'),
            ]));
    }

    private static function contactBlock(): Block
    {
        return Block::make('contact')
            ->label('📞 Kontak')
            ->icon('heroicon-o-phone')
            ->schema(array_merge(self::getNavFields(true, 'Kontak'), [
                TextInput::make('section_title')->label('Judul Bagian')->default('Hubungi Kami'),
                TextInput::make('section_subtitle')->label('Subjudul'),
            ]))
            ;
    }

    private static function blogBlock(): Block
    {
        return Block::make('blog')
            ->label('📝 Blog Terbaru')
            ->icon('heroicon-o-newspaper')
            ->schema(array_merge(self::getNavFields(false, 'Blog'), [
                TextInput::make('section_title')->label('Judul Bagian')->default('Blog & Artikel Terbaru'),
                TextInput::make('posts_count')->label('Jumlah Artikel Ditampilkan')->numeric()->default(3)
                    ->helperText('Berapa artikel terbaru yang ditampilkan.'),
            ]))
            ;
    }

    private static function galleryBlock(): Block
    {
        return Block::make('gallery')
            ->label('🖼️ Galeri Foto')
            ->icon('heroicon-o-photo')
            ->schema(array_merge(self::getNavFields(false, 'Galeri'), [
                TextInput::make('section_title')->label('Judul Bagian')->default('Galeri'),
                Repeater::make('images')
                    ->label('Foto')
                    ->schema([
                        FileUpload::make('image')->label('Gambar')->image()->disk('public')->directory('gallery')->required(),
                        TextInput::make('caption')->label('Keterangan'),
                    ])
                    ->reorderable()
                    ->addActionLabel('+ Tambah Foto'),
            ]));
    }

    private static function testimonialBlock(): Block
    {
        return Block::make('testimonial')
            ->label('💬 Testimoni')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema(array_merge(self::getNavFields(false, 'Testimoni'), [
                TextInput::make('section_title')->label('Judul Bagian')->default('Testimoni'),
                Repeater::make('testimonials')
                    ->label('Daftar Testimoni')
                    ->schema([
                        FileUpload::make('photo')->label('Foto')->image()->disk('public')->directory('testimonials'),
                        TextInput::make('name')->label('Nama')->required(),
                        TextInput::make('role')->label('Jabatan / Instansi'),
                        Textarea::make('quote')->label('Testimoni')->rows(3)->required(),
                    ])
                    ->reorderable()
                    ->addActionLabel('+ Tambah Testimoni'),
            ]));
    }

    private static function videoBlock(): Block
    {
        return Block::make('video')
            ->label('📹 Video')
            ->icon('heroicon-o-video-camera')
            ->schema(array_merge(self::getNavFields(false, 'Video'), [
                TextInput::make('section_title')->label('Judul Bagian')->default('Video'),
                TextInput::make('video_url')->label('URL Video (YouTube/Vimeo)')
                    ->placeholder('https://www.youtube.com/watch?v=...')
                    ->helperText('Tempel URL video dari YouTube atau Vimeo.')
                    ->required(),
                TextInput::make('video_caption')->label('Keterangan Video'),
            ]));
    }

    private static function freeTextBlock(): Block
    {
        return Block::make('free_text')
            ->label('📄 Teks Bebas')
            ->icon('heroicon-o-document-text')
            ->schema(array_merge(self::getNavFields(false, 'Info'), [
                TextInput::make('section_title')->label('Judul Bagian'),
                Select::make('background_style')->label('Gaya Latar')->options([
                    'white' => 'Putih', 'light' => 'Abu-Abu Muda', 'dark' => 'Gelap', 'gradient' => 'Gradien',
                ])->default('white'),
                RichEditor::make('content')
                    ->label('Konten')
                    ->json()
                    ->customBlocks([
                        FigureBlock::class,
                        EquationBlock::class,
                        ReferenceListBlock::class,
                    ])
                    ->toolbarButtons([
                        'h2',
                        'h3',
                        'bold',
                        'italic',
                        'strike',
                        'bulletList',
                        'orderedList',
                        'link',
                        'blockquote',
                        'table',
                        'undo',
                        'redo',
                    ])
                    ->columnSpanFull(),
            ]));
    }
}
