# SYSTEM INSTRUCTION: WordPress-Like Company Profile CMS

> **Status**: Ready for execution
> **Created**: 2026-06-12
> **Depends on**: `academic-cms-editor.md` (Prompt 1 — must be executed first)
> **Decisions by**: Faliq + AI (Grill-Me Interview)

---

## CORE Framework

### C — Context

- **Project**: `madeena-company-profile` (Laravel 13 + Filament v5 + Tailwind CSS v3.4 + Alpine.js + Vite)
- **Admin Panel**: Filament v5 at `/admin`, SSO-only auth via `madeena-iam`
- **Current State**:
  - Homepage content is **hardcoded** in `resources/views/home.blade.php` (356 lines of static HTML)
  - 7 homepage sections exist but only 3 are editable (HeroBanner, Products, Contact settings)
  - Remaining sections (Riset & Inovasi, Keunggulan, Tentang Kami, Legalitas) are hardcoded
  - Admin sidebar has grouped navigation ("Konten Website", "Administrasi") — confusing for non-technical users
  - Dashboard is empty
- **End User**: Prof. Gede Bayu Suparta — 61 years old, Physics lecturer at UGM, **technologically illiterate**. The CMS must be as easy as WordPress.
- **Key Files to Modify/Remove**:
  - `app/Filament/Resources/HeroBannerResource.php` — **REMOVE** (replaced by Homepage Editor)
  - `app/Filament/Pages/ManageSettings.php` — **REBUILD** (simplified Settings page)
  - `resources/views/home.blade.php` — **REBUILD** (dynamic, data-driven from Builder JSON)
  - `resources/views/product.blade.php` — **REBUILD** (page builder powered)
  - `app/Http/Controllers/HomeController.php` — **UPDATE** (read from settings)
- **Key Files to Create**:
  - `app/Filament/Pages/HomepageEditor.php` — new unified homepage editor
  - `app/Filament/Pages/Dashboard.php` — custom welcome dashboard
  - `app/Filament/Pages/SiteSettings.php` — rebuilt settings page
  - Section Blade partials in `resources/views/sections/`
  - Builder block classes

### O — Objective

Rebuild the entire Filament admin panel into a **WordPress-like CMS** where a 61-year-old, technologically illiterate professor can:
1. **Build and customize the homepage** using a drag-and-drop section builder
2. **Manage products** with rich detail pages
3. **Write academic articles** (handled by Prompt 1)
4. **Manage site settings** (contact, social, SEO)
5. **Navigate easily** with a flat, icon-based sidebar

### R — Role

Senior Fullstack Laravel/Filament Engineer with expertise in:
- Filament v5 `Builder` field and custom page builders
- WordPress-like CMS architecture
- Elderly-friendly, accessible UX design
- Dynamic Blade template rendering from JSON
- Filament dashboard widgets

### E — Expectations

- **Clean slate** — remove old `HeroBannerResource`, old `ManageSettings`, old hardcoded Blade content
- **No third-party CMS packages** — use Filament v5 native Builder + forms only
- **PSR-12** compliant (run `./vendor/bin/pint`)
- **Tested** — PHPUnit tests for homepage rendering, settings, and builder output
- **Production-quality** — no TODOs, no placeholders
- **Follow `.ai/` session protocol** — update `history.md`, `state.md`, `memory.json` at end of session

---

## PHASE 1: Database & Model Changes

### 1.1 Settings Table — No Schema Changes Needed

The existing `settings` table (`id`, `key`, `value`, `timestamps`) is sufficient. We'll store:

| Key | Value Type | Purpose |
|---|---|---|
| `homepage_sections` | JSON array | Page builder section data |
| `nav_custom_links` | JSON array | Custom navigation links |
| `contact_info` | JSON object | Email, phone, WhatsApp, address |
| `social_media` | JSON object | Instagram, LinkedIn, YouTube URLs |
| `seo` | JSON object | Meta title, meta description |

### 1.2 Products Table — Add Page Builder Column

Create migration `add_content_json_to_products_table`:

```
products table changes:
├── content_json      JSON     NULLABLE  — Page builder JSON for product detail page
└── description       DROP     — Remove old RichEditor HTML column after data migration
```

**Data migration**: Convert existing `description` HTML → Tiptap JSON (same pattern as Prompt 1).

### 1.3 Model Updates

**Product model**:
- Replace `description` with `content_json` in `$fillable`
- Add cast: `content_json` → `array`

**Setting model** (if not already):
- Ensure `value` supports JSON retrieval. Add a helper:
  ```php
  public static function getJson(string $key, mixed $default = null): mixed
  {
      $setting = static::where('key', $key)->first();
      return $setting ? json_decode($setting->value, true) : $default;
  }

  public static function setJson(string $key, mixed $value): void
  {
      static::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);
  }
  ```

### 1.4 HeroBanner Model — Keep As-Is

Keep the `hero_banners` table and `HeroBanner` model. The Homepage Editor's Hero block will manage banners through this model (CRUD via embedded repeater/relation manager).

### 1.5 Clean Up

- **DELETE** `app/Filament/Resources/HeroBannerResource.php` and its `Pages/` directory
- **DELETE** `app/Filament/Pages/ManageSettings.php` and its Blade view `resources/views/filament/pages/manage-settings.blade.php`

---

## PHASE 2: Admin Sidebar Simplification

### 2.1 Navigation Structure

Replace the current grouped navigation with a flat, simple menu:

```
🏠 Halaman Utama          → HomepageEditor (Filament Page)
📦 Produk                  → ProductResource (upgraded)
📝 Artikel                 → PostResource (upgraded by Prompt 1)
📄 Halaman                 → PageResource (upgraded by Prompt 1)
⚙️ Pengaturan             → SiteSettings (Filament Page)
👥 Pengguna               → UserResource (existing, admin sees all)
📩 Pesan Inabuyer         → InabuyerMessageResource (existing)
```

### 2.2 Implementation

In `AdminPanelProvider` (or equivalent panel configuration):
- Remove navigation groups (`Konten Website`, `Administrasi`)
- Set `navigationSort` on each resource/page to control order
- Use Heroicon or emoji-style SVG icons for each menu item
- Remove `shouldRegisterNavigation()` guards that hide resources from non-admins (professor IS admin)

---

## PHASE 3: Custom Dashboard

### 3.1 Dashboard Page

Create `app/Filament/Pages/Dashboard.php` (or override the default Filament dashboard).

**Layout**:
```
┌─────────────────────────────────────────────────────┐
│  Selamat Datang, Prof. Suparta! 👋                   │
│  Website: madeena.co.id                              │
│  [🌐 Lihat Website]                                  │
├─────────────────────────────────────────────────────┤
│  Quick Actions (3 large cards in a row):             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐          │
│  │ 📝       │  │ 🏠       │  │ 📦       │          │
│  │ Tulis    │  │ Edit     │  │ Tambah   │          │
│  │ Artikel  │  │ Halaman  │  │ Produk   │          │
│  │ Baru     │  │ Utama    │  │ Baru     │          │
│  └──────────┘  └──────────┘  └──────────┘          │
├─────────────────────────────────────────────────────┤
│  Statistics:                                         │
│  📝 12 Artikel  │  📦 5 Produk  │  📄 3 Halaman    │
├─────────────────────────────────────────────────────┤
│  Aktivitas Terakhir:                                 │
│  • Anda mengedit "Artikel Penelitian X" — 2 jam lalu│
│  • Anda menambah produk "DDR Pro" — kemarin          │
│  • Anda mengubah Halaman Utama — 3 hari lalu        │
└─────────────────────────────────────────────────────┘
```

### 3.2 Widgets

Create Filament widgets:
1. **`WelcomeWidget`** — greeting + "Lihat Website" button
2. **`QuickActionsWidget`** — 3 large clickable cards linking to create/edit pages
3. **`StatsOverviewWidget`** — counts of articles, products, pages
4. **`RecentActivityWidget`** — last 5 edited items (posts, products, pages) with timestamps

---

## PHASE 4: Homepage Editor (Page Builder)

### 4.1 Filament Page: `HomepageEditor`

Create `app/Filament/Pages/HomepageEditor.php`:

```php
class HomepageEditor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Halaman Utama';
    protected static ?string $title = 'Edit Halaman Utama';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $sections = Setting::getJson('homepage_sections', $this->getDefaultSections());
        $this->form->fill(['sections' => $sections]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Builder::make('sections')
                ->label('Bagian Halaman')
                ->blocks([
                    $this->heroBlock(),
                    $this->researchBlock(),
                    $this->productsBlock(),
                    $this->featuresBlock(),
                    $this->aboutBlock(),
                    $this->legalitiesBlock(),
                    $this->contactBlock(),
                    $this->blogBlock(),
                    $this->galleryBlock(),
                    $this->testimonialBlock(),
                    $this->videoBlock(),
                    $this->freeTextBlock(),
                ])
                ->reorderable()
                ->collapsible()
                ->collapsed()
                ->addActionLabel('+ Tambah Bagian Baru')
                ->blockNumbers(false)
                ->columnSpanFull(),
        ])->statePath('data');
    }

    public function save(): void { ... }
    public function preview(): void { ... }
}
```

### 4.2 Section Block Definitions

Each block includes common fields:
- `section_id` (auto-generated or custom) — used for navigation anchors
- `show_in_nav` (Toggle) — whether to show this section in the top navigation
- `nav_label` (TextInput) — label for the navigation link
- `background_style` (Select) — 'light' | 'dark' | 'gradient' | 'white'

#### Block: Hero Banner (`hero`)

```php
Builder\Block::make('hero')
    ->label('🖼️ Hero Banner')
    ->icon('heroicon-o-photo')
    ->schema([
        // Common nav fields
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(false),
        TextInput::make('nav_label')->label('Label Navigasi')->placeholder('Beranda'),

        // Hero-specific
        // Uses HeroBanner model — managed via embedded repeater
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
    ]),
```

#### Block: Products Showcase (`products`)

```php
Builder\Block::make('products')
    ->label('📦 Produk')
    ->icon('heroicon-o-beaker')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(true),
        TextInput::make('nav_label')->label('Label Navigasi')->default('Produk'),
        TextInput::make('section_title')->label('Judul Bagian')->default('Produk Inovasi Teknologi Kesehatan'),
        TextInput::make('section_subtitle')->label('Subjudul')->default('Berstandar Nasional, Izin Edar Kemenkes RI'),
        // Products are auto-pulled from the products table
        // No need for manual product entry here
    ])
    ->helperText('Produk otomatis ditampilkan dari daftar Produk. Kelola produk di menu 📦 Produk.'),
```

#### Block: About Us (`about`)

```php
Builder\Block::make('about')
    ->label('🏢 Tentang Kami')
    ->icon('heroicon-o-building-office')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(true),
        TextInput::make('nav_label')->label('Label Navigasi')->default('Tentang'),
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
    ]),
```

#### Block: Features / Keunggulan (`features`)

```php
Builder\Block::make('features')
    ->label('⭐ Keunggulan')
    ->icon('heroicon-o-star')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(false),
        TextInput::make('nav_label')->label('Label Navigasi'),
        TextInput::make('section_title')->label('Judul Bagian')->default('Keunggulan Teknologi'),

        Repeater::make('items')
            ->label('Kartu Keunggulan')
            ->schema([
                Select::make('icon')
                    ->label('Ikon')
                    ->options($this->getIconOptions()) // Visual icon picker
                    ->searchable()
                    ->helperText('Pilih ikon yang sesuai.'),
                TextInput::make('title')->label('Judul')->required(),
                Textarea::make('description')->label('Deskripsi')->rows(2),
            ])
            ->columns(1)
            ->reorderable()
            ->addActionLabel('+ Tambah Keunggulan'),
    ]),
```

#### Block: Legalities / Sertifikasi (`legalities`)

```php
Builder\Block::make('legalities')
    ->label('📜 Sertifikasi')
    ->icon('heroicon-o-shield-check')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(true),
        TextInput::make('nav_label')->label('Label Navigasi')->default('Legalitas'),
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
                    ->options($this->getIconOptions())
                    ->searchable(),
                TextInput::make('title')->label('Nama Sertifikasi')->required(),
                TextInput::make('detail')->label('Nomor / Detail')->required(),
            ])
            ->columns(1)
            ->reorderable()
            ->addActionLabel('+ Tambah Sertifikasi'),
    ]),
```

#### Block: Contact (`contact`)

```php
Builder\Block::make('contact')
    ->label('📞 Kontak')
    ->icon('heroicon-o-phone')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(true),
        TextInput::make('nav_label')->label('Label Navigasi')->default('Kontak'),
        TextInput::make('section_title')->label('Judul Bagian')->default('Hubungi Kami'),
        TextInput::make('section_subtitle')->label('Subjudul'),
        // Contact data is auto-pulled from Settings (contact_info key)
    ])
    ->helperText('Data kontak otomatis diambil dari ⚙️ Pengaturan. Kelola di menu Pengaturan.'),
```

#### Block: Blog Terbaru (`blog`)

```php
Builder\Block::make('blog')
    ->label('📝 Blog Terbaru')
    ->icon('heroicon-o-newspaper')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(false),
        TextInput::make('nav_label')->label('Label Navigasi')->default('Blog'),
        TextInput::make('section_title')->label('Judul Bagian')->default('Blog & Artikel Terbaru'),
        TextInput::make('posts_count')->label('Jumlah Artikel Ditampilkan')->numeric()->default(3)
            ->helperText('Berapa artikel terbaru yang ditampilkan.'),
    ])
    ->helperText('Artikel otomatis diambil dari menu 📝 Artikel.'),
```

#### Block: Gallery (`gallery`)

```php
Builder\Block::make('gallery')
    ->label('🖼️ Galeri Foto')
    ->icon('heroicon-o-photo')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(false),
        TextInput::make('nav_label')->label('Label Navigasi'),
        TextInput::make('section_title')->label('Judul Bagian')->default('Galeri'),

        Repeater::make('images')
            ->label('Foto')
            ->schema([
                FileUpload::make('image')->label('Gambar')->image()->disk('public')->directory('gallery')->required(),
                TextInput::make('caption')->label('Keterangan'),
            ])
            ->reorderable()
            ->addActionLabel('+ Tambah Foto'),
    ]),
```

#### Block: Testimonial (`testimonial`)

```php
Builder\Block::make('testimonial')
    ->label('💬 Testimoni')
    ->icon('heroicon-o-chat-bubble-left-right')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(false),
        TextInput::make('nav_label')->label('Label Navigasi'),
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
    ]),
```

#### Block: Video Embed (`video`)

```php
Builder\Block::make('video')
    ->label('📹 Video')
    ->icon('heroicon-o-video-camera')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(false),
        TextInput::make('nav_label')->label('Label Navigasi'),
        TextInput::make('section_title')->label('Judul Bagian')->default('Video'),
        TextInput::make('video_url')->label('URL Video (YouTube/Vimeo)')
            ->placeholder('https://www.youtube.com/watch?v=...')
            ->helperText('Tempel URL video dari YouTube atau Vimeo.')
            ->required(),
        TextInput::make('video_caption')->label('Keterangan Video'),
    ]),
```

#### Block: Free Text (`free_text`)

```php
Builder\Block::make('free_text')
    ->label('📄 Teks Bebas')
    ->icon('heroicon-o-document-text')
    ->schema([
        Toggle::make('show_in_nav')->label('Tampilkan di Navigasi')->default(false),
        TextInput::make('nav_label')->label('Label Navigasi'),
        TextInput::make('section_title')->label('Judul Bagian'),
        Select::make('background_style')->label('Gaya Latar')->options([
            'white' => 'Putih', 'light' => 'Abu-Abu Muda', 'dark' => 'Gelap', 'gradient' => 'Gradien',
        ])->default('white'),

        RichEditor::make('content')
            ->label('Konten')
            ->json()
            ->customBlocks([
                FigureBlock::class,       // From Prompt 1
                TableBlock::class,        // From Prompt 1
                EquationBlock::class,     // From Prompt 1
                ReferenceListBlock::class, // From Prompt 1
            ])
            ->columnSpanFull(),
    ]),
```

### 4.3 Icon Picker Helper

```php
private function getIconOptions(): array
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
```

### 4.4 Navigation Builder

**Auto-generated navigation** from the Builder sections:

```php
// In a service class or HomepageEditor
public static function getNavigation(): array
{
    $sections = Setting::getJson('homepage_sections', []);
    $navItems = [];

    foreach ($sections as $index => $section) {
        if ($section['data']['show_in_nav'] ?? false) {
            $navItems[] = [
                'label' => $section['data']['nav_label'] ?? ucfirst($section['type']),
                'anchor' => '#section-' . $index,
            ];
        }
    }

    // Append custom links from settings
    $customLinks = Setting::getJson('nav_custom_links', []);
    foreach ($customLinks as $link) {
        $navItems[] = [
            'label' => $link['label'],
            'url' => $link['url'],
            'is_external' => true,
        ];
    }

    return $navItems;
}
```

### 4.5 Custom Navigation Links (Settings)

In the `SiteSettings` page, add a section for custom nav links:

```php
Section::make('Navigasi Tambahan')->schema([
    Repeater::make('nav_custom_links')
        ->label('Link Navigasi Kustom')
        ->schema([
            TextInput::make('label')->label('Label')->required()->placeholder('Blog'),
            TextInput::make('url')->label('URL')->required()->placeholder('/blog'),
        ])
        ->addActionLabel('+ Tambah Link')
        ->helperText('Link tambahan yang akan muncul di navigasi website.'),
]),
```

### 4.6 Preview Button

Add a header action on the `HomepageEditor` page:

```php
protected function getHeaderActions(): array
{
    return [
        Action::make('preview')
            ->label('👁️ Pratinjau')
            ->icon('heroicon-o-eye')
            ->url(route('home', ['preview' => true]))
            ->openUrlInNewTab()
            ->color('info'),

        Action::make('save')
            ->label('💾 Simpan')
            ->icon('heroicon-o-check')
            ->action('save')
            ->color('success'),
    ];
}
```

---

## PHASE 5: Site Settings Page

### 5.1 Rebuild ManageSettings → SiteSettings

Create `app/Filament/Pages/SiteSettings.php`:

```php
class SiteSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan';
    protected static ?string $title = 'Pengaturan Website';
    protected static ?int $navigationSort = 5;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('📞 Informasi Kontak')
                ->description('Informasi kontak yang ditampilkan di seluruh website.')
                ->collapsible()
                ->schema([
                    TextInput::make('email')->label('Email')->email()
                        ->placeholder('madeenajog@gmail.com'),
                    TextInput::make('phone')->label('Telepon')
                        ->placeholder('+62 821 3811 4011'),
                    TextInput::make('whatsapp')->label('WhatsApp')
                        ->placeholder('+62 857 2830 4141')
                        ->helperText('Nomor WhatsApp dengan kode negara.'),
                    Textarea::make('address')->label('Alamat')->rows(2)
                        ->placeholder('Jl. Lowanu No. 68-72, Yogyakarta'),
                ])->columns(2),

            Section::make('🌐 Media Sosial')
                ->description('Link media sosial perusahaan.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextInput::make('instagram')->label('Instagram URL')
                        ->placeholder('https://instagram.com/madeena'),
                    TextInput::make('linkedin')->label('LinkedIn URL'),
                    TextInput::make('youtube')->label('YouTube URL'),
                ])->columns(3),

            Section::make('🔍 SEO')
                ->description('Pengaturan mesin pencari (Google, dll).')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextInput::make('meta_title')->label('Judul Website (Meta Title)')
                        ->placeholder('PT Madeena Karya Indonesia')
                        ->helperText('Judul yang muncul di tab browser dan hasil pencarian Google.'),
                    Textarea::make('meta_description')->label('Deskripsi Website (Meta Description)')
                        ->rows(3)
                        ->placeholder('Produsen alat Digital Direct Radiography (DDR) buatan Indonesia.')
                        ->helperText('Deskripsi singkat yang muncul di hasil pencarian Google. Maks 160 karakter.'),
                ])->columns(1),

            Section::make('🔗 Navigasi Tambahan')
                ->description('Link navigasi tambahan di header website.')
                ->collapsible()
                ->collapsed()
                ->schema([
                    Repeater::make('nav_custom_links')
                        ->label('Link Kustom')
                        ->schema([
                            TextInput::make('label')->label('Label')->required(),
                            TextInput::make('url')->label('URL')->required(),
                        ])
                        ->addActionLabel('+ Tambah Link')
                        ->helperText('Akan muncul di navigasi atas website.'),
                ]),
        ]);
    }
}
```

---

## PHASE 6: Product Resource Upgrade

### 6.1 Upgrade `ProductResource` Form

Replace the current form with a tabbed layout:

```
Tab 1: "Info Produk"
├── TextInput: name (Nama Produk)
├── TextInput: slug (auto-generate)
├── TextInput: tagline
├── FileUpload: image_path (Gambar Utama)
├── KeyValue: specifications (Spesifikasi)
├── Toggle: is_featured (Unggulan)
├── Toggle: is_active (Aktif)
└── TextInput: sort_order (Urutan)

Tab 2: "Halaman Detail Produk" (Page Builder)
└── Builder::make('content_json')
        ->blocks([
            HeroBlock,        // Product hero section
            FeaturesBlock,    // Product features
            GalleryBlock,     // Product images
            VideoBlock,       // Product video
            FreeTextBlock,    // Rich text content (uses academic editor)
            TableBlock,       // Specs table
        ])
        ->reorderable()
        ->collapsible()
```

---

## PHASE 7: Frontend — Dynamic Homepage

### 7.1 Rebuild `home.blade.php`

Replace the 356-line hardcoded template with a dynamic renderer:

```blade
@extends('layouts.app')

@section('title', $seo['meta_title'] ?? 'PT Madeena Karya Indonesia')

@section('content')
    @foreach ($sections as $index => $section)
        <section id="section-{{ $index }}" class="homepage-section {{ $section['data']['background_style'] ?? 'white' }}">
            @include('sections.' . $section['type'], [
                'data' => $section['data'],
                'index' => $index,
                'language' => 'id',
            ])
        </section>
    @endforeach
@endsection
```

### 7.2 Section Blade Partials

Create `resources/views/sections/` directory with one partial per block type:

```
resources/views/sections/
├── hero.blade.php
├── products.blade.php
├── about.blade.php
├── features.blade.php
├── legalities.blade.php
├── contact.blade.php
├── blog.blade.php
├── gallery.blade.php
├── testimonial.blade.php
├── video.blade.php
└── free_text.blade.php
```

Each partial receives `$data` (the block's form data) and renders the appropriate HTML with Tailwind CSS styling matching the current design aesthetic.

### 7.3 Navigation Partial

Update `resources/views/layouts/app.blade.php` navigation to be dynamic:

```blade
<nav>
    @foreach ($navItems as $item)
        @if ($item['is_external'] ?? false)
            <a href="{{ $item['url'] }}" target="_blank">{{ $item['label'] }}</a>
        @else
            <a href="{{ $item['anchor'] }}">{{ $item['label'] }}</a>
        @endif
    @endforeach
</nav>
```

### 7.4 HomeController Update

```php
public function index()
{
    $sections = Setting::getJson('homepage_sections', []);
    $seo = Setting::getJson('seo', []);
    $contactInfo = Setting::getJson('contact_info', []);
    $socialMedia = Setting::getJson('social_media', []);
    $navItems = HomepageEditor::getNavigation();

    // Inject dynamic data into auto-pull sections
    foreach ($sections as &$section) {
        match ($section['type']) {
            'products' => $section['products'] = Product::active()->ordered()->get(),
            'blog' => $section['posts'] = Post::published()->latest()->take($section['data']['posts_count'] ?? 3)->get(),
            'contact' => $section['contact'] = $contactInfo,
            'hero' => $section['banners'] = HeroBanner::active()->ordered()->get(),
            default => null,
        };
    }

    return view('home', compact('sections', 'seo', 'contactInfo', 'socialMedia', 'navItems'));
}
```

### 7.5 Product Detail Page

Rebuild `resources/views/product.blade.php` to render the page builder JSON:

```blade
@extends('layouts.app')

@section('content')
    @if ($product->content_json)
        @foreach ($product->content_json as $index => $section)
            @include('sections.' . $section['type'], [
                'data' => $section['data'],
                'index' => $index,
            ])
        @endforeach
    @else
        {{-- Fallback: simple product display --}}
        <h1>{{ $product->name }}</h1>
        <img src="{{ $product->image_path }}" />
        <table><!-- specs --></table>
    @endif
@endsection
```

---

## PHASE 8: Seeder — Default Homepage

### 8.1 Update `HomepageSectionSeeder`

Create a seeder that populates `homepage_sections` with the current hardcoded content, so the website looks identical after the migration:

```php
Setting::setJson('homepage_sections', [
    ['type' => 'hero', 'data' => [
        'show_in_nav' => false,
        'nav_label' => 'Beranda',
        // Hero data matches current hardcoded content
    ]],
    ['type' => 'products', 'data' => [
        'show_in_nav' => true,
        'nav_label' => 'Produk',
        'section_title' => 'Produk Inovasi Teknologi Kesehatan',
        'section_subtitle' => 'Berstandar Nasional, Izin Edar Kemenkes RI',
    ]],
    ['type' => 'features', 'data' => [
        'show_in_nav' => false,
        'items' => [
            ['icon' => 'fa-network-wired', 'title' => 'Sistem Teleradiologi', 'description' => '...'],
            ['icon' => 'fa-brain', 'title' => 'Antarmuka AI Diagnostik', 'description' => '...'],
            ['icon' => 'fa-certificate', 'title' => 'Izin Edar Kemenkes RI', 'description' => '...'],
            ['icon' => 'fa-handshake', 'title' => 'Program Kemitraan', 'description' => '...'],
        ],
    ]],
    ['type' => 'about', 'data' => [
        'show_in_nav' => true,
        'nav_label' => 'Tentang',
        'company_profile' => 'PT Madeena Karya Indonesia didirikan...',
        'vision' => 'Menjadi Duta Teknologi Indonesia...',
        'mission' => ['Melakukan hilirisasi...', 'Mengkomersialisasikan...', 'Mengembangkan...'],
        'motto' => 'Know Sciences, Learn Engineering, Create Technology, Develop Business.',
    ]],
    ['type' => 'legalities', 'data' => [
        'show_in_nav' => true,
        'nav_label' => 'Legalitas',
        'background_style' => 'dark',
        'section_title' => 'Legalitas Formal',
        'certificates' => [
            ['icon' => 'fa-building', 'title' => 'Surat Izin Berusaha Berbasis Risiko', 'detail' => 'NIB 9120106900275'],
            // ... all 6 current certificates
        ],
    ]],
    ['type' => 'contact', 'data' => [
        'show_in_nav' => true,
        'nav_label' => 'Kontak',
        'section_title' => 'Hubungi Kami',
        'background_style' => 'gradient',
    ]],
]);

// Seed contact info and other settings
Setting::setJson('contact_info', [
    'email' => 'madeenajog@gmail.com',
    'phone' => '+62 821 3811 4011',
    'whatsapp' => '+62 857 2830 4141',
    'address' => 'Jl. Lowanu No. 68-72, Yogyakarta',
]);

Setting::setJson('social_media', [...]);
Setting::setJson('seo', [...]);
```

---

## PHASE 9: Verification Plan

### Automated Tests
1. **Unit Tests**:
   - `SettingModelTest` — test `getJson()` and `setJson()` helpers
   - `HomepageNavigationTest` — test auto-generated navigation from sections

2. **Feature Tests**:
   - `HomepageEditorTest` — test saving/loading Builder JSON
   - `HomepageRenderTest` — test dynamic homepage renders all section types
   - `ProductPageBuilderTest` — test product detail page rendering from JSON
   - `SiteSettingsTest` — test saving/loading settings
   - `DashboardTest` — test dashboard widgets render

3. **Run commands**:
   ```bash
   php artisan test
   ./vendor/bin/pint
   ```

### Manual Verification
- Seed the default homepage and verify it looks identical to the current hardcoded version
- Add/remove/reorder sections in the Homepage Editor
- Test the preview button
- Test product detail page with page builder content
- Test navigation updates when sections are toggled
- Test on mobile viewport

---

## CONSTRAINTS & GUARDRAILS

1. **No new Composer packages** — use Filament v5 native Builder + forms only
2. **Clean slate** — remove old HeroBannerResource, old ManageSettings, old hardcoded Blade content
3. **Visual parity** — after migration, the website must look identical to the current version
4. **Indonesian-first UX** — all admin labels in Bahasa Indonesia with helper text
5. **Professor-proof** — large buttons, helpful placeholders, emoji icons, collapsible sections
6. **Performance** — lazy-load images, optimize section rendering
7. **Security** — sanitize all HTML output, validate file uploads
8. **Follow `.ai/` protocol** — update `history.md`, `state.md`, `memory.json` at session end

---

## EXECUTION ORDER

1. Execute **Prompt 1** (`academic-cms-editor.md`) first — it creates the academic editor blocks reused by the Free Text section
2. Then execute **this prompt** (Prompt 2) in sequence:
   - Phase 1: Database changes
   - Phase 2: Admin sidebar
   - Phase 3: Dashboard
   - Phase 4: Homepage Editor
   - Phase 5: Site Settings
   - Phase 6: Product Resource
   - Phase 7: Frontend templates
   - Phase 8: Seeders
   - Phase 9: Verification

---

## START

Begin execution. Follow the CORE framework and 4-Phase Session Loop:
1. **Load Game** — read `.ai/memory/state.md` and confirm context
2. **Plan Before Code** — present the plan (this document) for approval
3. **Debugging Loop** — implement phases 1-9, test after each phase
4. **Save Game** — update `.ai/` files with progress

Execute Phase 1 (database changes) first, then proceed sequentially.
