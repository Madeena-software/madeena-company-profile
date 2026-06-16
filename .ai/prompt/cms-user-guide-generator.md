# SYSTEM INSTRUCTION: Generate CMS User Guide for Prof. Gede Bayu Suparta

> **Status**: Ready for execution
> **Created**: 2026-06-16
> **Approach**: Discovery-first — the AI agent explores the codebase to discover features dynamically
> **Decisions by**: Faliq + AI (Grill-Me Interview)

---

## CORE Framework

### C — Context

- **Project**: `madeena-company-profile` (Laravel + Filament + Tailwind CSS + Alpine.js + Vite)
- **Admin Panel**: Filament at `/admin`, SSO-only auth via `madeena-iam`
- **CMS Features**: **Discovered at runtime** — the AI agent must scan the codebase to determine what features exist. Do NOT rely on this prompt for feature lists.
- **End User**: Prof. Drs. Gede Bayu Suparta, M.S., Ph.D. — 61 years old, Guru Besar Departemen Fisika FMIPA UGM. Expert in Imaging Physics (CT-Scan, Digital Radiography, NDT). Founder/key researcher behind PT Madeena Karya Indonesia (DDR medical devices). **Technologically illiterate** — needs extremely clear, patient, step-by-step instructions with screenshots.

### O — Objective

Generate a **single comprehensive PDF-ready Markdown document** (`docs/panduan-cms/panduan-cms.md`) that serves as a complete user guide for Prof. Suparta to independently operate the CMS. The guide must include **actual screenshots** captured via Playwright from the running development CMS. Every feature must be explained in step-by-step tutorial style with numbered instructions.

**The guide content is NOT hardcoded in this prompt.** The AI agent must discover all CMS features by scanning the codebase and generate documentation dynamically.

### R — Role

Technical Writer specializing in:
- Documentation for non-technical / elderly users
- Bilingual (Indonesian primary) instructional content
- Filament admin panel UX
- Screenshot-based tutorial creation via Playwright

### E — Expectations

- **Language**: Bilingual — Indonesian primary, English technical terms in parentheses. Example: "Klik tombol **Simpan** (*Save*)"
- **Tone**: Formal but warm — use "Anda" (formal you). Like a patient teacher explaining to a respected colleague. Avoid jargon; explain every technical term on first use.
- **Format**: Single `.md` file at `docs/panduan-cms/panduan-cms.md`, screenshots in `docs/panduan-cms/screenshots/`
- **Screenshots**: Captured via **Playwright** (NOT browser subagent). Install Playwright, start the dev server, navigate to each CMS page, and take screenshots. Save as `.png` in `docs/panduan-cms/screenshots/`.
- **No placeholders** — every screenshot must be actual, every instruction must be accurate to the real CMS UI
- **Follow `.ai/` session protocol** — update `history.md`, `state.md`, `memory.json` at end of session

---

## PHASE 0: Feature Discovery (MANDATORY FIRST STEP)

> **This is the most important phase.** Before writing ANY documentation, you MUST discover what features exist in the CMS by scanning the codebase. This makes the prompt future-proof — if features change, the guide automatically adapts.

### 0.1 Discovery Scan

Scan the following directories and files to build a complete feature inventory:

#### 1. Filament Pages (`app/Filament/Pages/`)
Read every `.php` file. For each Page, extract:
- Class name and `$navigationLabel` / `$title`
- `$navigationIcon` and `$navigationSort`
- `form()` method → all form fields, sections, tabs, and their labels
- Any action buttons (header actions, footer actions)
- Special behaviors (preview, save, etc.)

#### 2. Filament Resources (`app/Filament/Resources/`)
Read every `*Resource.php` file. For each Resource, extract:
- Class name, model, `$navigationLabel`, `$navigationIcon`
- `form()` method → all form fields, tabs, sections
- `table()` method → columns displayed in the list view
- Related pages (Create, Edit, List, View)

#### 3. Rich Editor Custom Blocks (`app/Filament/RichEditorBlocks/`)
Read every `.php` file. For each Block, extract:
- Block ID, label, icon
- Form schema → all fields with labels, placeholders, helper texts
- Purpose (what content type does this block create?)

#### 4. Builder Blocks (`app/Filament/BuilderBlocks.php` or similar)
Read the page builder block definitions. For each Builder block, extract:
- Block type/key, label, icon
- Form fields with labels and helper texts
- What section type does this represent on the homepage/pages?

#### 5. Dashboard Widgets (`app/Filament/Widgets/`)
Read every widget file. For each widget, extract:
- Widget type (stats, chart, table, custom)
- What data it displays
- Any action links/buttons

#### 6. Admin Panel Provider
Find the Filament panel provider (e.g. `AdminPanelProvider.php`) to understand:
- Navigation structure and groups
- Registered pages, resources, and widgets
- Auth configuration

### 0.2 Discovery Output

After scanning, compile a **Feature Inventory** — an internal checklist (not included in the final guide) that lists:

```
FEATURE INVENTORY
=================

SIDEBAR MENU (in navigation order):
1. [icon] [label] → [Page/Resource class] — [brief description]
2. [icon] [label] → [Page/Resource class] — [brief description]
...

RESOURCES (CRUD features):
- [ResourceName]: [model], [field count] fields, [column count] columns
  Fields: [field1], [field2], ...
...

CUSTOM PAGES:
- [PageName]: [purpose], [field count] fields
...

RICH EDITOR BLOCKS:
- [BlockName]: [label], [field count] fields
...

BUILDER BLOCKS (Page Builder):
- [BlockName]: [label], [field count] fields
...

DASHBOARD WIDGETS:
- [WidgetName]: [type], [purpose]
...
```

This inventory drives ALL subsequent phases. Every feature in the inventory gets a chapter (or sub-chapter) in the guide.

---

## PHASE 1: Guide Structure

### 1.0 Fixed Chapters (Always Present)

These chapters are always included regardless of what features are discovered:

```markdown
# 📖 Panduan Lengkap CMS Website Madeena
## Untuk Prof. Drs. Gede Bayu Suparta, M.S., Ph.D.

**Versi**: 1.0
**Tanggal**: [current date]
**Disusun oleh**: Tim Teknis Madeena

---

## 📋 Daftar Isi

1. Ringkasan Cepat (Cheat Sheet)
2. Masuk ke CMS (Login)
3. Mengenal Beranda (Dashboard)
4. Mengelola Halaman Utama Website (Homepage)
   ↓
   [DYNAMICALLY GENERATED CHAPTERS FROM DISCOVERY]
   ↓
N-1. Pengaturan Website
N.   Tanya Jawab & Pemecahan Masalah (FAQ)
```

### 1.1 Chapter: Ringkasan Cepat (Cheat Sheet) — ALWAYS FIRST

Generate a quick-reference table based on the Feature Inventory. For EVERY discovered feature, add a row:

| Saya ingin... | Langkah singkat |
|---|---|
| [action based on feature] | [2-3 step summary] |

> **Design note**: Use large text, bold key actions, emoji icons. This page should be printable and pin-able next to the professor's monitor.

### 1.2 Chapter: Masuk ke CMS (Login) — ALWAYS SECOND

Step-by-step login flow:

1. Explain what a CMS is: "CMS adalah 'ruang kontrol' website Anda. Dari sini Anda bisa mengubah isi website tanpa harus mengerti pemrograman."
2. Explain what a browser is, what a URL/address bar is
3. SSO login flow with screenshots
4. What to do if login fails

### 1.3 Chapter: Mengenal Beranda / Dashboard — ALWAYS THIRD

1. Dashboard layout with annotated screenshot
2. Sidebar menu explanation — list EVERY menu item discovered in Phase 0, with icon and brief description:
   - For each sidebar item: "[icon] **[label]** → [1-sentence explanation of what it does]"
3. Quick action cards (if dashboard widgets include them)
4. Statistics overview (if stats widget exists)
5. Recent activity (if activity widget exists)
6. **Transition bridge** — end with: "Sekarang Anda sudah mengenal Beranda (*Dashboard*). Mari kita mulai dengan hal yang paling penting: **mengelola tampilan halaman utama website Anda**. Klik menu 🏠 **Halaman Utama** di sebelah kiri."

### 1.4 Chapter: Mengelola Halaman Utama Website (Homepage) — ALWAYS FOURTH

Document the Homepage Editor page (if it exists). For each Builder block discovered in Phase 0:
1. Explain what the section type is (in simple terms)
2. How to edit it
3. How to add a new one
4. How to reorder
5. How to delete
6. Preview and Save

### 1.5+ Chapters: DYNAMICALLY GENERATED

For each **remaining** Resource and Page discovered in Phase 0 (excluding Dashboard, Homepage, and Settings), generate a chapter following this pattern:

**For Resources (CRUD features like Posts, Products, Pages, etc.):**
1. Navigating to the list page
2. Understanding the list table (what each column means)
3. Creating a new record — step-by-step for every form field
4. Editing a record
5. Deleting a record (with safety warning)
6. Any special features (tabs, toggles, rich editor, page builder)

**For Resources with Rich Text Editor:**
Additionally document:
- Every toolbar button (read from form schema)
- Every custom block (from `RichEditorBlocks/`)
- Keyboard shortcuts
- Before/after examples of formatting

**For Custom Pages (like Settings):**
1. Navigating to the page
2. Each section/tab — step-by-step for every field
3. Saving changes

### 1.N-1 Chapter: Pengaturan Website — ALWAYS SECOND-TO-LAST

Document the Settings page. For each section discovered in Phase 0, explain every field.

### 1.N Chapter: Tanya Jawab & Pemecahan Masalah (FAQ) — ALWAYS LAST

Generate FAQ entries based on discovered features. For each feature, create at least 1 common problem/solution entry. Include a "Butuh Bantuan?" section with:

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

### 2.1 Dynamic Screenshot Planning

Based on the Feature Inventory from Phase 0, determine what screenshots are needed. **Do NOT use a hardcoded list.** Instead, follow these rules:

**For EVERY discovered Page/Resource, capture at minimum:**
- One **full-page overview** screenshot (list view for resources, main view for pages)
- One **form/editor** screenshot (create or edit form)
- One **annotated detail** screenshot for complex UI elements (toolbar, custom blocks, etc.)

**Screenshot Naming Convention:**
```
screenshots/
├── [NN]-[feature-slug]/
│   ├── [feature-slug]-[view-type].png
│   ├── [feature-slug]-[detail-type].png
│   └── ...
```

Where:
- `[NN]` = chapter number (2 digits, matching guide chapter order)
- `[feature-slug]` = kebab-case feature name (e.g. `login`, `dashboard`, `artikel`, `homepage`)
- `[view-type]` = `overview`, `create`, `edit`, `list`, `form`, `toolbar`, `preview`, etc.

### 2.2 Playwright Script

Create a Playwright script (`docs/panduan-cms/capture-screenshots.js`) that:

1. **Sets viewport** to 1280x800 (standard laptop size)
2. **Seeds test data** — create sample records using models discovered in Phase 0, with realistic Physics/DDR domain content
3. **Handles auth** — bypass SSO for dev or use a seeded test user
4. **Iterates through the Feature Inventory** — for each feature, navigate to its admin page and capture screenshots
5. **Captures annotations** — use `page.evaluate()` to inject red circles, numbered callouts, and arrow pointers before capturing

### 2.3 Screenshot Annotations

For key screenshots, add visual annotations via injected CSS overlays:

- **Red circles** (⭕) around buttons being referenced
- **Numbered callouts** (①②③) for multi-step screenshots
- **Arrow pointers** (➡️) pointing to specific fields

### 2.4 Seed Data Guidelines

Seed realistic sample data using Prof. Suparta's research domain:
- **Article titles**: Physics/DDR/Radiography topics
- **Author names**: Prof. Gede Bayu Suparta, with UGM affiliation
- **Product names**: DDR-related medical devices
- **Keywords**: CT-Scan, Radiografi Digital, Imaging Physics, NDT
- **LaTeX equations**: Physics equations (E=mc², wave equation, etc.)

> **Important**: Read the model `$fillable` arrays and database migrations to understand what fields each model has. Don't hardcode seed data — adapt to the actual model structure.

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
![Halaman login CMS Madeena](./screenshots/01-login/login-overview.png)
*Gambar 1: Halaman login CMS. Masukkan email dan password Anda di kolom yang ditandai.*
```

### 3.3 Step Format

Every tutorial step follows this pattern:

```markdown
**Langkah 3**: Klik tombol **Simpan** (*Save*) di pojok kanan atas halaman.

![Tombol Simpan](./screenshots/04-artikel/artikel-save-button.png)
*Setelah mengklik Simpan, akan muncul notifikasi hijau "Berhasil disimpan" di pojok kanan atas.*
```

### 3.4 Jargon Glossary

On first use of any technical term, explain it in parentheses. Common terms include (but discover more from the actual UI labels):

- CMS: "CMS (*Content Management System*) adalah sistem untuk mengelola isi website"
- URL: "URL (*Uniform Resource Locator*) adalah alamat website, contoh: `madeena.co.id`"
- Slug: "Slug adalah versi singkat judul untuk alamat web"
- Dashboard: "Dashboard (*Beranda Panel Admin*) adalah halaman pertama setelah Anda masuk"
- SSO: "SSO (*Single Sign-On*) adalah sistem login terpusat"
- Toggle: "Toggle (*tombol geser*) adalah sakelar hidup/mati"
- Publish: "Publish (*Publikasi*) berarti menampilkan di website untuk umum"
- Draft: "Draft (*Draf*) berarti disimpan tapi belum ditampilkan di website"

> **Note**: Add glossary entries for ANY technical term you encounter in the CMS that the professor wouldn't understand. Don't limit yourself to this list.

### 3.5 Accessibility Guidelines

- Use **large, readable fonts** in examples (suggest 12pt+ when printed)
- Use **high-contrast** screenshots (avoid dark themes)
- Number EVERY step — never use "then", "next", "after that" without a number
- Each step = ONE action only. Never combine actions.
- Repeat important information — don't assume the reader remembers from 3 pages ago

### 3.6 Physics Domain Examples

When demonstrating any CMS feature, use examples from Prof. Suparta's research domain:
- Article titles: e.g. "Analisis Morfologi Permukaan Detektor DDR menggunakan SEM"
- Figure captions: e.g. "Morfologi permukaan sampel setelah pemanasan 500°C"
- Table data: e.g. temperature/pressure measurement data
- Equations: e.g. `E = mc^2`, wave equations, Gaussian integrals
- References: e.g. "Suparta, G.B., et al. (2024). Digital Radiography System for NDT. *J. Phys.*"
- Product names: e.g. "DDR Pro Series", "RSFD (Radiografi Sinar-X Fluoresensi Digital)"

---

## EXAMPLE CHAPTER TEMPLATES

> **Purpose**: These are example chapters showing the expected quality and depth. Use them as a TEMPLATE for consistency, but generate actual content from codebase discovery. Do NOT copy these verbatim.

### EXAMPLE A: Resource Chapter (e.g. "Menulis & Mengelola Artikel")

```markdown
## 5. Menulis & Mengelola Artikel

Artikel adalah tulisan yang akan ditampilkan di halaman blog website Anda.
Anda bisa menulis artikel tentang penelitian, berita, atau pengumuman.

### 5.1 Melihat Daftar Artikel

**Langkah 1**: Klik menu 📝 **Artikel** di panel sebelah kiri.

![Daftar Artikel](./screenshots/05-artikel/artikel-list.png)
*Anda akan melihat daftar semua artikel yang sudah pernah dibuat.*

Pada halaman ini, Anda bisa melihat:
- **Judul** — nama artikel
- **Status** — Draf (*belum dipublikasikan*) atau Publikasi (*sudah tampil di website*)
- **Tanggal** — kapan artikel dibuat

### 5.2 Membuat Artikel Baru

**Langkah 1**: Klik tombol **Buat Baru** (*Create*) di pojok kanan atas.

![Tombol Buat Baru](./screenshots/05-artikel/artikel-create-button.png)

**Langkah 2**: Isi **Judul** artikel.
Contoh: *Analisis Morfologi Permukaan Detektor DDR menggunakan SEM*

**Langkah 3**: Kolom **Slug** akan terisi otomatis. Anda tidak perlu mengubahnya.

> 💡 **Apa itu Slug?** Slug adalah versi singkat dari judul yang digunakan
> di alamat website. Contoh: judul "Analisis Morfologi" menjadi slug
> "analisis-morfologi". Kolom ini terisi otomatis.

**Langkah 4**: [continue for every field discovered in the form schema...]

**Langkah N**: Klik tombol **Simpan** (*Save*) di pojok kanan atas.

✅ Artikel Anda berhasil disimpan!
```

### EXAMPLE B: Rich Text Editor Toolbar Explanation

```markdown
## 6. Menggunakan Editor Teks (*Rich Text Editor*)

Editor teks adalah area tempat Anda menulis isi artikel. Editor ini mirip
dengan Microsoft Word — Anda bisa menebalkan teks, membuat judul, menyisipkan
gambar, dan lain-lain.

![Editor Teks](./screenshots/06-editor/editor-toolbar.png)
*Tombol-tombol di atas area tulisan disebut "toolbar" (*bilah alat*).*

### 6.1 Menulis Teks Biasa

Cukup klik di area putih yang luas, lalu mulai mengetik.

### 6.2 Menebalkan Teks (*Bold*)

**Langkah 1**: Pilih (*sorot*) teks yang ingin ditebalkan dengan cara:
klik dan tahan di awal teks, lalu seret (*drag*) ke akhir teks.

**Langkah 2**: Klik tombol **B** (huruf B tebal) di toolbar.

> 💡 **Cara cepat**: Anda juga bisa menekan **Ctrl+B** di keyboard
> setelah menyorot teks.

[continue for every toolbar button discovered in the RichEditor config...]
```

---

## CONSTRAINTS & GUARDRAILS

1. **Discovery-driven** — NEVER assume a feature exists. Always verify by reading the source code first. If a feature doesn't exist in the codebase, don't document it.
2. **Accuracy** — every screenshot must match the actual CMS UI. If a page errors or isn't built yet, note it as "Dalam pengembangan" and skip.
3. **No assumptions about user** — never assume the reader knows what a button, icon, or technical term means. Explain everything.
4. **Physics domain examples** — use examples from Prof. Suparta's research domain for all sample content.
5. **Playwright only** — use Playwright for screenshots, NOT the browser subagent tool. Write and run a Node.js Playwright script.
6. **Single file** — output must be one `panduan-cms.md` file with embedded screenshot references.
7. **Adaptive** — if you discover features not mentioned in this prompt, document them. If features mentioned as examples don't exist, skip them. The codebase is the source of truth.
8. **Follow `.ai/` protocol** — update `history.md`, `state.md`, `memory.json` at session end.

---

## VERIFICATION

After generating the guide:

1. **Completeness check** — cross-reference the Feature Inventory against the guide. Every feature should have documentation.
2. **Visual check** — open the `.md` file and verify all screenshot references point to existing files
3. **Link check** — verify all internal links (`#section`) work
4. **Tone check** — read through and ensure the language is warm, respectful, and jargon-free
5. **Print test** — verify the document looks good when exported to PDF (reasonable page breaks, no overflow)
6. **Accuracy check** — verify every instruction matches the actual CMS behavior

---

## START

Begin execution:
1. **Load Game** — read `.ai/memory/state.md` and confirm context
2. **Discover** — execute Phase 0: scan codebase, build Feature Inventory
3. **Setup** — install Playwright, start dev server, seed sample data
4. **Capture** — run Playwright script to capture screenshots for all discovered features
5. **Write** — create `docs/panduan-cms/panduan-cms.md` with dynamically generated chapters
6. **Verify** — check completeness, accuracy, and rendering
7. **Save Game** — update `.ai/` files with progress
