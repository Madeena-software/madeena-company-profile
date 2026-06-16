# SYSTEM INSTRUCTION: Generate CMS User Guide for Prof. Gede Bayu Suparta

> **Status**: Ready for execution
> **Created**: 2026-06-16
> **Decisions by**: Faliq + AI (Grill-Me Interview)

---

## CORE Framework

### C — Context

- **Project**: `madeena-company-profile` (Laravel 13 + Filament v5 + Tailwind CSS v3.4 + Alpine.js + Vite)
- **Admin Panel**: Filament v5 at `/admin`, SSO-only auth via `madeena-iam`
- **CMS Features**: Homepage Editor (Page Builder), PostResource (Academic Articles), PageResource (Static Pages), ProductResource, SiteSettings, InabuyerMessage
- **Rich Text Editor**: Filament v5 native Tiptap-based `RichEditor` with custom academic blocks (FigureBlock, TableBlock, EquationBlock, ReferenceListBlock)
- **End User**: Prof. Drs. Gede Bayu Suparta, M.S., Ph.D. — 61 years old, Guru Besar Departemen Fisika FMIPA UGM. Expert in Imaging Physics (CT-Scan, Digital Radiography, NDT). Founder/key researcher behind PT Madeena Karya Indonesia (DDR medical devices). **Technologically illiterate** — needs extremely clear, patient, step-by-step instructions with screenshots.
- **CMS Prompt References** (for understanding the CMS structure):
  - `.ai/prompt/academic-cms-editor.md` — Academic editor with custom blocks
  - `.ai/prompt/wordpress-like-cms.md` — WordPress-like page builder CMS

### O — Objective

Generate a **single comprehensive PDF-ready Markdown document** (`docs/panduan-cms/panduan-cms.md`) that serves as a complete user guide for Prof. Suparta to independently operate the CMS. The guide must include **actual screenshots** captured via Playwright from the running development CMS. Every feature must be explained in step-by-step tutorial style with numbered instructions.

### R — Role

Technical Writer specializing in:
- Documentation for non-technical / elderly users
- Bilingual (Indonesian primary) instructional content
- Filament v5 admin panel UX
- Screenshot-based tutorial creation via Playwright

### E — Expectations

- **Language**: Bilingual — Indonesian primary, English technical terms in parentheses. Example: "Klik tombol **Simpan** (*Save*)"
- **Tone**: Formal but warm — use "Anda" (formal you). Like a patient teacher explaining to a respected colleague. Avoid jargon; explain every technical term on first use.
- **Format**: Single `.md` file at `docs/panduan-cms/panduan-cms.md`, screenshots in `docs/panduan-cms/screenshots/`
- **Screenshots**: Captured via **Playwright** (NOT browser subagent). Install Playwright, start the dev server, navigate to each CMS page, and take screenshots. Save as `.png` in `docs/panduan-cms/screenshots/`.
- **No placeholders** — every screenshot must be actual, every instruction must be accurate to the real CMS UI
- **Follow `.ai/` session protocol** — update `history.md`, `state.md`, `memory.json` at end of session

---

## PHASE 0: Setup & Screenshot Capture

### 0.1 Environment Preparation

Before writing the guide, prepare the environment:

1. **Install Playwright** in the project:
   ```bash
   npm install --save-dev @playwright/test
   npx playwright install chromium
   ```

2. **Start the development server**:
   ```bash
   php artisan serve --port=8000 &
   npm run dev &
   ```

3. **Create screenshot script** at `docs/panduan-cms/capture-screenshots.js`:
   A Playwright script that:
   - Logs into the CMS via SSO (or seeds a test user and uses direct login)
   - Navigates to each major CMS page
   - Captures full-page and element-specific screenshots
   - Saves them to `docs/panduan-cms/screenshots/` with descriptive names

### 0.2 Required Screenshots

Capture the following screenshots (minimum). Annotate filenames clearly:

```
screenshots/
├── 01-login/
│   ├── login-page.png              — SSO login page
│   ├── login-form-filled.png       — Login form with example credentials
│   └── login-success.png           — After successful login
│
├── 02-dashboard/
│   ├── dashboard-overview.png      — Full dashboard view
│   ├── dashboard-quick-actions.png — Quick action cards
│   └── sidebar-menu.png           — Sidebar navigation menu
│
├── 03-homepage/
│   ├── homepage-editor.png         — Homepage editor full view
│   ├── homepage-sections.png       — Section list (collapsed)
│   ├── homepage-hero-edit.png      — Hero banner editing
│   ├── homepage-about-edit.png     — About section editing
│   ├── homepage-products-edit.png  — Products section
│   ├── homepage-add-section.png    — Adding a new section
│   ├── homepage-reorder.png        — Reordering sections (drag handle)
│   └── homepage-preview.png        — Preview result
│
├── 04-artikel/
│   ├── artikel-list.png            — Article listing page
│   ├── artikel-create.png          — Create new article form
│   ├── artikel-metadata-tab.png    — Metadata tab filled
│   ├── artikel-akademik-tab.png    — Academic info tab
│   ├── artikel-konten-tab.png      — Content editor tab
│   ├── rich-editor-toolbar.png     — Rich text editor toolbar closeup
│   ├── rich-editor-heading.png     — Heading selection
│   ├── rich-editor-bold-italic.png — Bold/italic formatting
│   ├── block-menu.png              — Custom blocks dropdown menu
│   ├── block-figure-form.png       — Figure block form
│   ├── block-table-form.png        — Table block form
│   ├── block-equation-form.png     — Equation block form
│   ├── block-references-form.png   — References block form
│   ├── artikel-preview.png         — Article preview
│   └── artikel-published.png       — Published article on frontend
│
├── 05-halaman/
│   ├── halaman-list.png            — Pages listing
│   ├── halaman-create.png          — Create new page
│   └── halaman-builder.png         — Page builder in action
│
├── 06-produk/
│   ├── produk-list.png             — Products listing
│   ├── produk-create.png           — Create new product form
│   └── produk-detail-builder.png   — Product detail page builder
│
├── 07-pengaturan/
│   ├── settings-kontak.png         — Contact settings
│   ├── settings-sosial.png         — Social media settings
│   ├── settings-seo.png            — SEO settings
│   ├── settings-tampilan.png       — Appearance settings
│   └── settings-whatsapp.png       — WhatsApp button settings
│
└── 08-pesan/
    ├── pesan-list.png              — Messages listing
    └── pesan-detail.png            — Message detail view
```

> **Important**: If a page or feature doesn't exist yet (hasn't been built), skip that screenshot and note it in the guide as "Fitur ini sedang dalam pengembangan." Use existing pages/features only.

---

## PHASE 1: Write the Guide Document

### 1.0 Document Structure

Create `docs/panduan-cms/panduan-cms.md` with the following structure:

```markdown
# 📖 Panduan Lengkap CMS Website Madeena
## Untuk Prof. Drs. Gede Bayu Suparta, M.S., Ph.D.

**Versi**: 1.0
**Tanggal**: [current date]
**Disusun oleh**: Tim Teknis Madeena

---

## 📋 Daftar Isi

1. [Ringkasan Cepat (Cheat Sheet)](#1-ringkasan-cepat)
2. [Masuk ke CMS (Login)](#2-masuk-ke-cms)
3. [Mengenal Beranda (Dashboard)](#3-mengenal-dashboard)
4. [Mengelola Halaman Utama Website (Homepage)](#4-homepage-editor)
5. [Menulis & Mengelola Artikel](#5-menulis-artikel)
6. [Menggunakan Editor Teks (Rich Text Editor)](#6-editor-teks)
7. [Menyisipkan Gambar, Tabel & Persamaan](#7-blok-akademik)
8. [Mengelola Halaman Statis](#8-halaman-statis)
9. [Mengelola Produk](#9-produk)
10. [Pengaturan Website](#10-pengaturan)
11. [Melihat Pesan Masuk (Inabuyer)](#11-pesan)
12. [Tanya Jawab & Pemecahan Masalah (FAQ)](#12-faq)

---
```

### 1.1 Chapter: Ringkasan Cepat (Cheat Sheet)

Write a **1-page quick-reference** summary at the very beginning. Format as a table:

| Saya ingin... | Langkah singkat |
|---|---|
| Menulis artikel baru | Klik 📝 Artikel → Buat Baru → Isi judul & konten → Simpan |
| Menambah gambar di artikel | Di editor, klik ⊕ Blok → 📷 Tambah Gambar → Unggah file |
| Edit halaman utama | Klik 🏠 Halaman Utama → Edit bagian → Simpan |
| Tambah produk | Klik 📦 Produk → Buat Baru → Isi detail → Simpan |
| Ubah nomor WhatsApp | Klik ⚙️ Pengaturan → Kontak → Ubah nomor → Simpan |
| Lihat pesan masuk | Klik 📩 Pesan Inabuyer |

> **Design note**: Use large text, bold key actions, emoji icons. This page should be printable and pin-able next to the professor's monitor.

### 1.2 Chapter: Masuk ke CMS (Login)

Step-by-step login flow:

1. Explain what a CMS is (in 1-2 simple sentences): "CMS adalah 'ruang kontrol' website Anda. Dari sini Anda bisa mengubah isi website tanpa harus mengerti pemrograman."
2. Open browser, navigate to the admin URL
3. SSO login flow explanation with screenshots
4. What to do if login fails (forgot password, account locked, etc.)

**Key instructions**:
- Explain what a "browser" is (Chrome/Firefox) — don't assume knowledge
- Explain what a URL/address bar is
- Show exactly where to type the admin URL
- Show the SSO login page with labeled arrows
- Show the dashboard after successful login

### 1.3 Chapter: Mengenal Beranda / Dashboard

1. Explain the dashboard layout with annotated screenshot
2. Explain the sidebar menu — what each icon means:
   - 🏠 Halaman Utama → editor halaman depan website
   - 📦 Produk → kelola daftar produk
   - 📝 Artikel → tulis dan kelola artikel
   - 📄 Halaman → buat halaman statis
   - ⚙️ Pengaturan → ubah pengaturan website
   - 👥 Pengguna → kelola akun pengguna
   - 📩 Pesan Inabuyer → lihat pesan masuk
3. Explain the quick action cards
4. Explain the statistics overview
5. Explain the recent activity feed
6. **Transition to Homepage Editor** — end the chapter with a natural bridge:
   > "Sekarang Anda sudah mengenal Beranda (*Dashboard*). Mari kita mulai dengan hal yang paling penting: **mengelola tampilan halaman utama website Anda**. Klik menu 🏠 **Halaman Utama** di sebelah kiri."

**Key instructions**:
- Number and label every section of the dashboard in the screenshot
- Use callout boxes: "💡 **Tips**: Anda selalu bisa kembali ke halaman ini dengan mengklik logo Madeena di pojok kiri atas."
- The sidebar menu explanation serves as a "map" so the professor knows what each menu item does before diving into individual chapters
- The transition text at the end should feel natural, like a teacher guiding the student to the next lesson

### 1.4 Chapter: Mengelola Halaman Utama Website (Homepage Editor)

> **Note to AI Agent**: This chapter comes right after Dashboard because it's the first thing the professor will want to manage. The Dashboard chapter ends with a transition bridge leading here.

1. **Membuka Homepage Editor** — click "🏠 Halaman Utama" in sidebar
2. **Memahami bagian-bagian (sections)** — explain what each section type is:
   - Hero Banner = spanduk besar di atas website
   - Produk = etalase produk
   - Tentang Kami = profil perusahaan
   - Keunggulan = kartu-kartu keunggulan
   - Sertifikasi = daftar izin dan sertifikat
   - Kontak = informasi kontak
   - Blog Terbaru = artikel terbaru
   - Galeri = kumpulan foto
   - Testimoni = testimoni pelanggan
   - Video = video YouTube
   - Teks Bebas = konten tulisan bebas
3. **Mengedit bagian yang sudah ada** — click to expand, edit fields, save
4. **Menambah bagian baru** — click "+ Tambah Bagian Baru", select type
5. **Mengubah urutan bagian** — drag the ≡ handle to reorder
6. **Menghapus bagian** — click delete icon (with confirmation warning)
7. **Pratinjau (Preview)** — click "👁️ Pratinjau" button to see changes before publishing
8. **Menyimpan perubahan** — click "💾 Simpan"

### 1.5 Chapter: Menulis & Mengelola Artikel

Full tutorial flow:

1. **Melihat daftar artikel** — navigate to article list, explain the table columns
2. **Membuat artikel baru**:
   - Step-by-step: Click "Buat Baru", fill in each tab
   - Tab 1 (Metadata): Title, slug (explain auto-generate), category, cover image, publish toggle
   - Tab 2 (Info Akademik): Abstract, keywords, authors — explain each field with examples from Prof. Suparta's actual research domain (Physics, CT-Scan, Digital Radiography)
   - Tab 3 (Konten): Using the rich text editor (reference Chapter 6)
3. **Mengedit artikel** — find article in list, click edit, make changes, save
4. **Menghapus artikel** — with warning about permanent deletion
5. **Mempublikasikan artikel** — toggle `is_published` and set `published_at`

**Key instructions**:
- Use Prof. Suparta's research domain for examples: "Judul: *Analisis Morfologi Permukaan Sampel DDR menggunakan SEM*"
- Every field must have an example value shown in the screenshot or described
- Explain what "slug" means: "Slug adalah versi singkat dari judul yang digunakan di alamat website. Contoh: judul 'Analisis Morfologi' menjadi slug 'analisis-morfologi'."
- Explain what "publish" means vs "draft"

### 1.6 Chapter: Menggunakan Editor Teks (Rich Text Editor)

Detailed, toolbar-button-by-button guide:

1. **Overview** — what the editor looks like, annotated screenshot of the toolbar
2. **Menulis teks biasa** — just click and type
3. **Membuat judul bagian (Heading)** — H1, H2, H3 with examples:
   - "Gunakan Heading 2 untuk judul bab utama seperti '1. Pendahuluan', 'Metode', 'Hasil'"
   - "Gunakan Heading 3 untuk sub-bab seperti '1.1 Latar Belakang'"
4. **Menebalkan teks (Bold)** — select text, click B or Ctrl+B
5. **Memiringkan teks (Italic)** — select text, click I or Ctrl+I
6. **Menggarisbawahi teks (Underline)** — select text, click U
7. **Superscript & Subscript** — explain for scientific notation: "Untuk menulis H₂O, ketik H, lalu pilih teks '2', dan klik tombol subscript (x₂)"
8. **Daftar bernomor dan bullet** — ordered and unordered lists
9. **Menyisipkan tautan (Link)** — how to add a hyperlink
10. **Kutipan (Blockquote)** — for quotes
11. **Undo dan Redo** — "Batal" and "Ulangi" — Ctrl+Z, Ctrl+Y

**Key instructions**:
- Explain keyboard shortcuts alongside button clicks
- Use visual indicators: "Tombol **B** (huruf B tebal) di toolbar untuk menebalkan teks"
- Show before/after of each formatting action
- Add a warning: "⚠️ **Penting**: Jangan lupa menyimpan artikel Anda secara berkala dengan mengklik tombol **Simpan** di pojok kanan atas."

### 1.7 Chapter: Menyisipkan Gambar, Tabel & Persamaan (Blok Akademik)

Step-by-step for each custom block:

1. **Cara membuka menu blok** — "Klik tombol ⊕ atau pilih 'Blok' di toolbar"
2. **📷 Menyisipkan Gambar (Figure Block)**:
   - Step-by-step: select block → upload image → write caption → set ref ID (explain what ref ID is) → choose size
   - Example caption: "Morfologi permukaan sampel setelah pemanasan 500°C"
   - Explain image size options with visual comparison
3. **📊 Menyisipkan Tabel (Table Block)**:
   - Step-by-step: select block → write caption → enter HTML table → set ref ID
   - **Important**: Explain that tables use HTML format, provide a template they can copy-paste
   - Provide a ready-made template:
     ```
     <table>
       <thead>
         <tr><th>Parameter</th><th>Nilai</th><th>Satuan</th></tr>
       </thead>
       <tbody>
         <tr><td>Suhu</td><td>500</td><td>°C</td></tr>
         <tr><td>Tekanan</td><td>1.0</td><td>atm</td></tr>
       </tbody>
     </table>
     ```
   - Explain: "Salin (*copy*) template di atas, lalu ganti isinya dengan data Anda."
4. **∑ Menyisipkan Persamaan (Equation Block)**:
   - Step-by-step: select block → write LaTeX → preview → set ref ID
   - Provide common LaTeX examples from Physics:
     - `E = mc^2`
     - `F = ma`
     - `\frac{\partial^2 u}{\partial t^2} = c^2 \nabla^2 u` (wave equation)
     - `\int_0^\infty e^{-x^2} dx = \frac{\sqrt{\pi}}{2}`
   - Explain: "LaTeX adalah bahasa khusus untuk menulis rumus matematika. Anda tidak perlu menghafal semuanya — gunakan contoh di atas sebagai acuan."
5. **📚 Menyisipkan Daftar Pustaka (Reference List Block)**:
   - Step-by-step: select block → add references one by one → fill in authors, title, journal, year, DOI
   - Example using a real-style reference: "Suparta, G.B., et al. (2024). Digital Radiography System for NDT. *J. Phys.*"
6. **Rujukan Silang (Cross-References)**:
   - Explain how to type `[Gambar 1]`, `[Tabel 1]`, `[Persamaan 1]`, `[1]` in the text to create clickable references
   - Example: "Seperti ditunjukkan pada [Gambar 1], morfologi permukaan..."

### 1.8 Chapter: Mengelola Halaman Statis

1. View list of static pages
2. Create a new page (title, slug)
3. Use the page builder to add sections (same blocks as Homepage Editor)
4. Edit existing pages
5. Delete pages (with warning)

### 1.9 Chapter: Mengelola Produk

1. View product list
2. Create new product (name, slug, tagline, image, specifications, featured toggle)
3. Build product detail page using the page builder
4. Edit/delete products

### 1.10 Chapter: Pengaturan Website

Step-by-step for each settings section:

1. **📞 Informasi Kontak** — email, phone, WhatsApp, address
2. **🌐 Media Sosial** — Instagram, LinkedIn, YouTube URLs
3. **🔍 SEO** — meta title, meta description (explain what SEO means in simple terms)
4. **🔗 Navigasi Tambahan** — custom navigation links
5. **🎨 Pengaturan Tampilan** — logo, colors, font
6. **💬 Tombol WhatsApp** — enable/disable floating WhatsApp button

### 1.11 Chapter: Melihat Pesan Masuk (Inabuyer)

1. Navigate to messages
2. View message list
3. Read individual messages
4. Explain that messages come from the website's contact form

### 1.12 Chapter: Tanya Jawab & Pemecahan Masalah (FAQ)

Write at least 10 common questions and answers:

| # | Masalah | Solusi |
|---|---|---|
| 1 | Artikel tidak muncul di website | Pastikan toggle "Publikasikan" sudah aktif (hijau) dan tanggal publikasi sudah diisi |
| 2 | Gambar tidak bisa diunggah | Periksa ukuran file (maksimal 5MB). Format yang didukung: JPG, PNG, WebP |
| 3 | Lupa password | Hubungi tim teknis Madeena di [contact info] untuk reset password SSO |
| 4 | Persamaan LaTeX tidak muncul dengan benar | Pastikan format LaTeX benar. Coba salin dari contoh yang tersedia di panduan ini |
| 5 | Perubahan di halaman utama tidak terlihat | Pastikan Anda sudah mengklik tombol "💾 Simpan" setelah mengedit |
| 6 | Tabel berantakan tampilannya | Periksa format HTML tabel. Gunakan template yang disediakan di Bab 7 |
| 7 | Website lambat saat mengunggah gambar | Kompres gambar terlebih dahulu menggunakan [tool online gratis]. Idealnya di bawah 2MB |
| 8 | Tidak bisa mengakses halaman admin | Pastikan Anda menggunakan alamat yang benar: [admin URL]. Jika masih gagal, hubungi tim teknis |
| 9 | Artikel terhapus secara tidak sengaja | Segera hubungi tim teknis. Data mungkin masih bisa dipulihkan dari backup |
| 10 | Cara menambah penulis di artikel | Buka tab "Info Akademik" → klik "+ Tambah Penulis" → isi nama, afiliasi, dan email |

Add a final section:

```markdown
### 📞 Butuh Bantuan?

Jika Anda mengalami masalah yang tidak tercantum di atas, silakan hubungi:

- **Tim Teknis Madeena**: [email/WhatsApp]
- **Jam Operasional**: Senin–Jumat, 08.00–17.00 WIB

> 💡 **Tips**: Saat menghubungi tim teknis, jelaskan langkah-langkah yang sudah Anda lakukan
> dan kirimkan tangkapan layar (*screenshot*) jika memungkinkan.
> Untuk mengambil tangkapan layar, tekan tombol **PrtSc** (Print Screen) di keyboard Anda.
```

---

## PHASE 2: Screenshot Strategy

### 2.1 Playwright Script Guidelines

Create a Playwright script that:

1. **Sets viewport** to 1280x800 (standard laptop size)
2. **Uses a clean state** — seed test data if needed (sample articles, products, pages)
3. **Captures full-page screenshots** for overview pages
4. **Captures element-specific screenshots** for UI details (toolbar, buttons, forms)
5. **Adds red circles/arrows** for annotation where pointing at specific UI elements (use Playwright's page.evaluate to inject CSS overlays before capturing)
6. **Names files descriptively** following the naming convention in Phase 0.2
7. **Handles auth** — either bypass SSO for dev or use a seeded test user

### 2.2 Screenshot Annotations

For key screenshots, add visual annotations:

- **Red circles** (⭕) around buttons being referenced
- **Numbered callouts** (①②③) for multi-step screenshots
- **Arrow pointers** (➡️) pointing to specific fields

Implementation: Use Playwright's `page.evaluate()` to inject temporary CSS/HTML overlay elements before taking the screenshot.

### 2.3 Seed Data

Before capturing screenshots, seed realistic sample data:

```php
// Example seed data for screenshots
Post::create([
    'title' => 'Analisis Morfologi Permukaan Detektor DDR menggunakan SEM',
    'slug' => 'analisis-morfologi-permukaan-detektor-ddr',
    'content_json' => [...], // Sample Tiptap JSON with figures, equations
    'abstract' => 'Penelitian ini menganalisis morfologi permukaan...',
    'keywords' => ['DDR', 'SEM', 'Morfologi', 'Radiografi Digital'],
    'authors_info' => [
        ['name' => 'Gede Bayu Suparta', 'affiliation' => 'Departemen Fisika, FMIPA UGM', 'email' => 'gede.bayu@ugm.ac.id'],
        ['name' => 'Ahmad Researcher', 'affiliation' => 'PT Madeena Karya Indonesia', 'email' => 'ahmad@madeena.co.id'],
    ],
    'is_published' => true,
]);

Product::create([
    'name' => 'DDR Pro Series',
    'slug' => 'ddr-pro-series',
    'tagline' => 'Digital Direct Radiography Buatan Indonesia',
    'is_featured' => true,
    'is_active' => true,
]);
```

---

## PHASE 3: Writing Guidelines

### 3.1 Formatting Conventions

Use these consistently throughout the document:

- **Bold** for UI elements: "Klik tombol **Simpan**"
- *Italic* for English translations: "tangkapan layar (*screenshot*)"
- `Monospace` for URLs, file names, and technical values
- > Blockquote for tips and notes
- ⚠️ for warnings
- 💡 for tips
- 📌 for important notes
- ✅ for success indicators
- ❌ for error indicators

### 3.2 Screenshot Embedding

Embed screenshots using standard Markdown:

```markdown
![Halaman login CMS Madeena](./screenshots/01-login/login-page.png)
*Gambar 1: Halaman login CMS. Masukkan email dan password Anda di kolom yang ditandai.*
```

### 3.3 Step Format

Every tutorial step follows this pattern:

```markdown
**Langkah 3**: Klik tombol **Simpan** (*Save*) di pojok kanan atas halaman.

![Tombol Simpan](./screenshots/03-artikel/artikel-save-button.png)
*Setelah mengklik Simpan, akan muncul notifikasi hijau "Berhasil disimpan" di pojok kanan atas.*
```

### 3.4 Jargon Glossary

On first use of any technical term, explain it in parentheses:

- CMS: "CMS (*Content Management System*) adalah sistem untuk mengelola isi website"
- URL: "URL (*Uniform Resource Locator*) adalah alamat website, contoh: `madeena.co.id`"
- Slug: "Slug adalah versi singkat judul untuk alamat web"
- Dashboard: "Dashboard (*Halaman Utama Panel Admin*) adalah halaman pertama setelah Anda masuk"
- SSO: "SSO (*Single Sign-On*) adalah sistem login terpusat"
- Toggle: "Toggle (*tombol geser*) adalah sakelar hidup/mati"
- Publish: "Publish (*Publikasi*) berarti menampilkan artikel di website untuk umum"
- Draft: "Draft (*Draf*) berarti artikel disimpan tapi belum ditampilkan di website"
- LaTeX: "LaTeX (dibaca 'la-tek') adalah bahasa khusus untuk menulis rumus matematika dan sains"

### 3.5 Accessibility Guidelines

- Use **large, readable fonts** in examples (suggest 12pt+ when printed)
- Use **high-contrast** screenshots (avoid dark themes)
- Number EVERY step — never use "then", "next", "after that" without a number
- Each step = ONE action only. Never combine actions.
- Repeat important information — don't assume the reader remembers from 3 pages ago

---

## CONSTRAINTS & GUARDRAILS

1. **Accuracy** — every screenshot must match the actual CMS UI. If a feature doesn't exist yet, note it as "Dalam pengembangan" and skip.
2. **No assumptions** — never assume the reader knows what a button, icon, or technical term means. Explain everything.
3. **Physics domain examples** — use examples from Prof. Suparta's research domain (CT-Scan, DDR, Digital Radiography, Imaging Physics) for all sample content.
4. **Playwright only** — use Playwright for screenshots, NOT the browser subagent tool. Write and run a Node.js Playwright script.
5. **Single file** — output must be one `panduan-cms.md` file with embedded screenshot references.
6. **Follow `.ai/` protocol** — update `history.md`, `state.md`, `memory.json` at session end.

---

## VERIFICATION

After generating the guide:

1. **Visual check** — open the `.md` file in a Markdown viewer and verify all screenshots render correctly
2. **Link check** — verify all internal links (`#section`) work
3. **Completeness check** — verify every CMS feature is documented
4. **Tone check** — read through and ensure the language is warm, respectful, and jargon-free
5. **Print test** — verify the document looks good when exported to PDF (reasonable page breaks, no overflow)

---

## START

Begin execution:
1. **Load Game** — read `.ai/memory/state.md` and confirm context
2. **Setup** — install Playwright, start dev server, seed sample data
3. **Capture** — run Playwright script to capture all required screenshots
4. **Write** — create `docs/panduan-cms/panduan-cms.md` following the structure above
5. **Verify** — check completeness, accuracy, and rendering
6. **Save Game** — update `.ai/` files with progress
