# SYSTEM INSTRUCTION: Academic Paper-Style CMS Editor (Elsevier/Nature)

> **Status**: Ready for execution
> **Created**: 2026-06-12
> **Decisions by**: Faliq + AI (Grill-Me Interview)

---

## CORE Framework

### C — Context

- **Project**: `madeena-company-profile` (Laravel 13 + Filament v5 + Tailwind CSS v3.4 + Alpine.js + Vite)
- **Admin Panel**: Filament v5 at `/admin`, SSO-only auth via `madeena-iam`
- **Current State**: `PostResource` and `PageResource` use Filament's basic `RichEditor` (HTML output). No structured content, no academic formatting.
- **End User**: Prof. Gede Bayu Suparta — 60 years old, Physics lecturer at UGM, **technologically illiterate**. The CMS must be as easy as WordPress.
- **Key Files**:
  - `app/Filament/Resources/PostResource.php` — blog editor (to be upgraded)
  - `app/Filament/Resources/PageResource.php` — page editor (to be upgraded)
  - `app/Models/Post.php` — blog model
  - `app/Models/Page.php` — page model
  - `database/migrations/` — existing migrations
  - `resources/views/` — frontend Blade views

### O — Objective

Upgrade the existing `PostResource` and `PageResource` editors to support **Elsevier/Nature-style academic paper content**, using Filament v5's **native Tiptap-based `RichEditor`** with custom blocks (`RichContentCustomBlock`). The professor should be able to write structured academic articles (with figures, tables, equations, references, cross-references) as easily as writing a WordPress blog post.

### R — Role

Senior Fullstack Laravel/Filament Engineer with expertise in:
- Filament v5 custom blocks (`RichContentCustomBlock`)
- Tiptap editor extensions and JSON storage
- KaTeX math rendering
- Academic paper typesetting and layout
- Accessible, elderly-friendly UX design

### E — Expectations

- **No third-party editor packages** — use Filament v5 native `RichEditor` only
- **Hard replace** — drop old `body`/`content` HTML columns, migrate all existing data to `content_json`. No legacy fallbacks.
- **PSR-12** compliant (run `./vendor/bin/pint`)
- **Tested** — PHPUnit tests for new models, blocks, and rendering
- **Production-quality** — no TODOs, no placeholders
- **Follow `.ai/` session protocol** — update `history.md`, `state.md`, `memory.json` at end of session

---

## PHASE 1: Database Schema Changes

### 1.1 Migration: Refactor Posts Table to Academic Format

Create migration `refactor_posts_to_academic_format`:

**Step 1**: Add new columns
```
posts table changes:
├── content_json      JSON     NOT NULL  — Structured Tiptap JSON content (replaces `body`)
├── abstract          TEXT     NULLABLE  — Paper abstract (optional)
├── keywords          JSON     NULLABLE  — Array of keywords (optional)
├── authors_info      JSON     NULLABLE  — Array of {name, affiliation, email} (optional)
├── content_language  VARCHAR  DEFAULT 'id'  — 'id' or 'en' for label rendering
└── body              DROP     — Remove old HTML column after data migration
```

**Step 2**: Data migration — convert existing `body` HTML to minimal Tiptap JSON:
```php
// In the migration's up() method:
Post::whereNotNull('body')->each(function ($post) {
    $post->content_json = [
        'type' => 'doc',
        'content' => [
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => strip_tags($post->body)]
                ]
            ]
        ]
    ];
    $post->saveQuietly();
});
```

**Step 3**: Drop `body` column after migration.

> **Note**: For the `down()` method, reverse the process: convert `content_json` back to HTML `body`.

### 1.2 Migration: Refactor Pages Table to Academic Format

Create migration `refactor_pages_to_academic_format`:

**Step 1**: Add new columns, migrate data, then drop old column:
```
pages table changes:
├── content_json      JSON     NOT NULL  — Structured Tiptap JSON content (replaces `content`)
├── content_language  VARCHAR  DEFAULT 'id'
└── content           DROP     — Remove old HTML column after data migration
```

**Step 2**: Same data migration pattern as posts — convert `content` HTML → Tiptap JSON, then drop `content` column.

### 1.3 Model Updates

**Post model**:
- Replace `body` with `content_json` in `$fillable`
- Add `abstract`, `keywords`, `authors_info`, `content_language` to `$fillable`
- Add casts: `content_json` → `array`, `keywords` → `array`, `authors_info` → `array`
- Remove any references to old `body` field

**Page model**:
- Replace `content` with `content_json` in `$fillable`
- Add `content_language` to `$fillable`
- Add casts: `content_json` → `array`
- Remove any references to old `content` field

---

## PHASE 2: Custom Blocks (RichContentCustomBlock)

Create all blocks in `app/Filament/RichEditorBlocks/` namespace.

### 2.1 FigureBlock

**ID**: `academic-figure`
**Label**: `Gambar / Figure`
**Purpose**: Image with auto-numbered caption and cross-reference anchor

**Form Schema**:
```php
FileUpload::make('image')
    ->label('Unggah Gambar')
    ->image()
    ->disk('public')
    ->directory('academic-figures')
    ->required()
    ->helperText('Unggah gambar (JPG, PNG, WebP). Maks 5MB.'),

TextInput::make('caption')
    ->label('Keterangan Gambar')
    ->required()
    ->placeholder('Contoh: Morfologi permukaan sampel setelah pemanasan 500°C')
    ->helperText('Akan ditampilkan sebagai "Gambar X: [keterangan Anda]"'),

TextInput::make('ref_id')
    ->label('ID Referensi')
    ->placeholder('fig-sem-surface')
    ->helperText('ID unik untuk rujukan silang. Gunakan huruf kecil dan strip. Contoh: fig-sem-surface')
    ->rules(['nullable', 'regex:/^[a-z0-9-]+$/']),

Select::make('size')
    ->label('Ukuran Gambar')
    ->options([
        'small' => 'Kecil (50%)',
        'medium' => 'Sedang (75%)',
        'full' => 'Penuh (100%)',
    ])
    ->default('full'),
```

### 2.2 TableBlock

**ID**: `academic-table`
**Label**: `Tabel / Table`
**Purpose**: Data table with auto-numbered caption

**Form Schema**:
```php
TextInput::make('caption')
    ->label('Judul Tabel')
    ->required()
    ->placeholder('Contoh: Hasil pengukuran suhu sampel pada berbagai tekanan'),

Textarea::make('table_html')
    ->label('Konten Tabel (HTML)')
    ->required()
    ->rows(10)
    ->placeholder('<table><thead><tr><th>Parameter</th><th>Nilai</th></tr></thead><tbody><tr><td>Suhu</td><td>500°C</td></tr></tbody></table>')
    ->helperText('Masukkan tabel dalam format HTML. Gunakan <thead> untuk header dan <tbody> untuk isi.'),

TextInput::make('ref_id')
    ->label('ID Referensi')
    ->placeholder('tbl-temperature')
    ->helperText('ID unik untuk rujukan silang.')
    ->rules(['nullable', 'regex:/^[a-z0-9-]+$/']),
```

### 2.3 EquationBlock

**ID**: `academic-equation`
**Label**: `Persamaan / Equation`
**Purpose**: LaTeX math equation rendered via KaTeX

**Form Schema**:
```php
Textarea::make('latex')
    ->label('Persamaan LaTeX')
    ->required()
    ->rows(3)
    ->placeholder('E = mc^2')
    ->helperText('Tulis persamaan dalam format LaTeX. Contoh: E = mc^2, \\frac{\\partial^2 u}{\\partial t^2} = c^2 \\nabla^2 u'),

// Note: Live KaTeX preview is rendered via Alpine.js in the block's Blade view

TextInput::make('ref_id')
    ->label('ID Referensi')
    ->placeholder('eq-wave')
    ->helperText('ID unik untuk rujukan silang. Contoh: eq-wave')
    ->rules(['nullable', 'regex:/^[a-z0-9-]+$/']),
```

### 2.4 ReferenceListBlock

**ID**: `academic-references`
**Label**: `Daftar Pustaka / References`
**Purpose**: Numbered bibliography/reference list

**Form Schema**:
```php
Repeater::make('references')
    ->label('Daftar Referensi')
    ->schema([
        TextInput::make('authors')
            ->label('Penulis')
            ->required()
            ->placeholder('Suparta, G.B., Smith, J.'),

        TextInput::make('title')
            ->label('Judul')
            ->required()
            ->placeholder('Analysis of ionospheric perturbations'),

        TextInput::make('journal')
            ->label('Jurnal / Sumber')
            ->placeholder('Journal of Geophysical Research'),

        TextInput::make('year')
            ->label('Tahun')
            ->placeholder('2024'),

        TextInput::make('volume')
            ->label('Volume')
            ->placeholder('129(3)'),

        TextInput::make('pages')
            ->label('Halaman')
            ->placeholder('pp. 1234-1250'),

        TextInput::make('doi')
            ->label('DOI')
            ->placeholder('10.1029/2024JA032xxx')
            ->helperText('Opsional. Akan menjadi tautan klik.'),
    ])
    ->columns(2)
    ->reorderable()
    ->addActionLabel('+ Tambah Referensi')
    ->helperText('Referensi akan otomatis diberi nomor [1], [2], [3], dst.'),
```

### 2.5 CitationMark (Inline — Not a Block)

This is NOT a custom block but an **inline mark/link convention** in the RichEditor. When the professor types `[1]` or `[Fig. 1]` in the text, the frontend renderer converts these into clickable cross-reference links.

**Implementation**: Use a post-processing step during rendering:
- Pattern `[1]`, `[2]`, `[1-3]` → link to `#ref-1`, `#ref-2`, etc.
- Pattern `[Fig. 1]`, `[Gambar 1]` → link to the corresponding figure's `ref_id` anchor
- Pattern `[Table 1]`, `[Tabel 1]` → link to the corresponding table's `ref_id` anchor
- Pattern `[Eq. 1]`, `[Persamaan 1]` → link to the corresponding equation's `ref_id` anchor

---

## PHASE 3: Upgrade PostResource Editor

### 3.1 Form Schema Restructure

Replace the current `PostResource::form()` with a tabbed/sectioned layout:

```
Tab 1: "Metadata Artikel"
├── TextInput: title (Judul)
├── TextInput: slug (auto-generate)
├── Select: user_id (Penulis)
├── TextInput: category (Kategori)
├── FileUpload: cover_image (Gambar Sampul)
├── Toggle: is_published (Publikasikan)
├── DateTimePicker: published_at
└── Select: content_language ('id' | 'en')

Tab 2: "Info Akademik" (optional section, collapsible)
├── Textarea: abstract (Abstrak) — with placeholder
├── TagsInput: keywords (Kata Kunci) — comma separated
└── Repeater: authors_info [{name, affiliation, email}]

Tab 3: "Konten Artikel" (main editor — full width)
└── RichEditor::make('content_json')
        ->json()
        ->label('Isi Artikel')
        ->columnSpanFull()
        ->customBlocks([
            FigureBlock::class,
            TableBlock::class,
            EquationBlock::class,
            ReferenceListBlock::class,
        ])
        ->toolbarButtons([
            'heading',       // H1, H2, H3 for section headings
            'bold',
            'italic',
            'underline',
            'superscript',   // For citations like ²
            'subscript',     // For chemical formulas like H₂O
            'bulletList',
            'orderedList',
            'link',
            'blockquote',
            'undo',
            'redo',
            'blocks',        // Custom blocks menu
        ])
```

### 3.2 No Legacy Fallback

- All posts use `content_json` exclusively — the old `body` column no longer exists
- The editor always reads/writes `content_json` (Tiptap JSON)
- Existing posts were migrated during the database migration phase

---

## PHASE 4: Upgrade PageResource Editor

Apply the same approach but simpler (Pages don't need abstract/keywords/authors):

```
Section: "Informasi Halaman"
├── TextInput: title
├── TextInput: slug
└── Select: content_language

Section: "Konten Halaman"
└── RichEditor::make('content_json')
        ->json()
        ->customBlocks([
            FigureBlock::class,
            TableBlock::class,
            EquationBlock::class,
            ReferenceListBlock::class,
        ])
```

---

## PHASE 5: Frontend Academic Renderer

### 5.1 Blade Component: `<x-academic-content>`

Create `resources/views/components/academic-content.blade.php`

This component takes `content_json` (array) and `language` ('id'|'en') and renders the Tiptap JSON into Elsevier/Nature-style HTML.

**Rendering Rules**:

1. **Section Headings**: Render with auto-numbering
   - H1 → Title (not numbered)
   - H2 → `1. Introduction`, `2. Methods`, etc.
   - H3 → `1.1`, `1.2`, `2.1`, etc.
   - H4 → `1.1.1`, `1.1.2`, etc.

2. **Figures**: Render as centered block with caption below
   ```html
   <figure id="fig-sem-surface" class="academic-figure">
     <img src="..." alt="..." />
     <figcaption>Gambar 1: Morfologi permukaan sampel...</figcaption>
   </figure>
   ```

3. **Tables**: Render with caption above (academic convention)
   ```html
   <div id="tbl-temperature" class="academic-table">
     <p class="table-caption">Tabel 1: Hasil pengukuran...</p>
     <table>...</table>
   </div>
   ```

4. **Equations**: Render centered with number on right
   ```html
   <div id="eq-wave" class="academic-equation">
     <span class="equation-content" data-latex="..."><!-- KaTeX rendered --></span>
     <span class="equation-number">(1)</span>
   </div>
   ```

5. **References**: Render as numbered list at bottom
   ```html
   <section class="academic-references">
     <h2>Referensi / References</h2>
     <ol>
       <li id="ref-1">Suparta, G.B., Smith, J. (2024). Analysis of... <a href="https://doi.org/...">[DOI]</a></li>
       ...
     </ol>
   </section>
   ```

6. **Cross-References**: Post-process text to convert `[1]`, `[Fig. 1]`, etc. into clickable anchor links

### 5.2 CSS Styling: Nature/Science Style

Create `resources/css/academic-article.css` (loaded only on article pages):

```css
/* Nature/Science inspired — single column, clean, modern */
.academic-article {
  font-family: 'Inter', 'Noto Sans', sans-serif;
  font-size: 1.05rem;
  line-height: 1.75;
  color: #1a1a2e;
  max-width: 800px;
  margin: 0 auto;
}

/* Section headings */
.academic-article h2 {
  font-size: 1.4rem;
  font-weight: 700;
  margin-top: 2.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e5e7eb;
}

/* Abstract block */
.academic-abstract {
  background: #f8f9fa;
  border-left: 4px solid #3b82f6;
  padding: 1.5rem;
  margin: 1.5rem 0;
  font-style: italic;
}

/* Figure styling */
.academic-figure {
  margin: 2rem auto;
  text-align: center;
}
.academic-figure img {
  max-width: 100%;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.academic-figure figcaption {
  margin-top: 0.75rem;
  font-size: 0.9rem;
  color: #4b5563;
  font-style: italic;
}

/* Equation styling */
.academic-equation {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 1.5rem 0;
  gap: 2rem;
}
.equation-number {
  color: #6b7280;
  font-size: 0.95rem;
}

/* Table styling */
.academic-table {
  margin: 2rem 0;
  overflow-x: auto;
}
.table-caption {
  font-weight: 600;
  font-size: 0.95rem;
  margin-bottom: 0.5rem;
  color: #374151;
}
.academic-table table {
  width: 100%;
  border-collapse: collapse;
}
.academic-table th, .academic-table td {
  border: 1px solid #d1d5db;
  padding: 0.5rem 0.75rem;
  text-align: left;
}
.academic-table th {
  background: #f3f4f6;
  font-weight: 600;
}

/* References */
.academic-references {
  margin-top: 3rem;
  padding-top: 1.5rem;
  border-top: 2px solid #e5e7eb;
}
.academic-references ol {
  padding-left: 2rem;
  font-size: 0.9rem;
  line-height: 1.6;
}
.academic-references li {
  margin-bottom: 0.5rem;
}

/* Cross-reference links */
a.xref {
  color: #2563eb;
  text-decoration: none;
  cursor: pointer;
}
a.xref:hover {
  text-decoration: underline;
}

/* Keywords */
.academic-keywords {
  margin: 1rem 0;
}
.academic-keywords .keyword {
  display: inline-block;
  background: #eff6ff;
  color: #1d4ed8;
  padding: 0.2rem 0.6rem;
  border-radius: 1rem;
  font-size: 0.85rem;
  margin: 0.2rem;
}

/* Author info */
.academic-authors {
  color: #4b5563;
  font-size: 0.95rem;
  margin: 0.5rem 0 1.5rem;
}
```

### 5.3 KaTeX Integration

Install KaTeX via npm:
```bash
npm install katex
```

Add to `vite.config.js`:
- Import KaTeX CSS in the article page layout
- Create Alpine.js component that auto-renders `[data-latex]` elements using KaTeX

```javascript
// resources/js/katex-render.js
import katex from 'katex';
import 'katex/dist/katex.min.css';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-latex]').forEach(el => {
        const latex = el.getAttribute('data-latex');
        try {
            katex.render(latex, el, {
                displayMode: true,
                throwOnError: false,
            });
        } catch (e) {
            el.textContent = latex; // Fallback to raw LaTeX
        }
    });
});
```

---

## PHASE 6: UX Enhancements (WordPress-like Simplicity)

### 6.1 Large Toolbar Buttons
Override the RichEditor's block picker to show large, clearly labeled buttons:
- 📷 **Tambah Gambar** (Add Figure)
- 📊 **Tambah Tabel** (Add Table)
- ∑ **Tambah Persamaan** (Add Equation)
- 📚 **Tambah Daftar Pustaka** (Add References)

### 6.2 Helpful Placeholders
Every block form field has Indonesian placeholder text with examples:
- Equation: `"Contoh: E = mc^2 atau \frac{d}{dx} f(x)"`
- Figure caption: `"Contoh: Morfologi permukaan sampel SEM"`
- Reference author: `"Contoh: Suparta, G.B., Smith, J."`

### 6.3 Inline Help Tooltips
Add `->helperText()` to every form field in every block with clear Indonesian instructions.

### 6.4 Auto-Save
Leverage Filament's built-in auto-save or add Livewire `wire:model.live.debounce.3000ms` with a "✓ Tersimpan" indicator.

### 6.5 Full-Screen Editing
Use Filament's `->fullWidth()` on the RichEditor and consider adding a full-screen toggle button.

### 6.6 Preview Button
Add a custom Filament Action button "👁️ Pratinjau" (Preview) that opens a modal showing the rendered academic article as it would appear on the public site.

---

## PHASE 7: Frontend View Updates

### 7.1 Post Show View (`resources/views/post/show.blade.php` or equivalent)

All posts now use the academic renderer — no legacy HTML fallback needed:

```blade
{{-- Academic renderer — all posts use content_json --}}
@if ($post->authors_info)
    <div class="academic-authors">
        @foreach ($post->authors_info as $author)
            <span>{{ $author['name'] }}</span>
            @if ($author['affiliation'] ?? null)
                <span class="affiliation">{{ $author['affiliation'] }}</span>
            @endif
        @endforeach
    </div>
@endif

@if ($post->abstract)
    <div class="academic-abstract">
        <strong>{{ $post->content_language === 'en' ? 'Abstract' : 'Abstrak' }}:</strong>
        {{ $post->abstract }}
    </div>
@endif

@if ($post->keywords)
    <div class="academic-keywords">
        <strong>{{ $post->content_language === 'en' ? 'Keywords' : 'Kata Kunci' }}:</strong>
        @foreach ($post->keywords as $keyword)
            <span class="keyword">{{ $keyword }}</span>
        @endforeach
    </div>
@endif

<x-academic-content
    :content="$post->content_json"
    :language="$post->content_language"
/>
```

### 7.2 Load Academic Assets

Always include academic CSS and KaTeX on post/page views:

```blade
@push('styles')
    @vite('resources/css/academic-article.css')
@endpush
@push('scripts')
    @vite('resources/js/katex-render.js')
@endpush
```

---

## PHASE 8: Auto-Numbering Engine

Create a PHP service class `App\Services\AcademicContentRenderer`:

```php
class AcademicContentRenderer
{
    private int $figureCount = 0;
    private int $tableCount = 0;
    private int $equationCount = 0;
    private array $sectionCounters = [0, 0, 0]; // H2, H3, H4
    private array $refIdMap = []; // Maps ref_id to number
    private string $language;

    public function __construct(string $language = 'id') { ... }

    public function render(array $tiptapJson): string { ... }

    // Processes each node in the Tiptap JSON tree
    private function renderNode(array $node): string { ... }

    // Auto-number sections based on heading level
    private function numberSection(int $level): string { ... }

    // Post-process HTML to convert [1], [Fig. 1] into anchor links
    private function processReferences(string $html): string { ... }
}
```

---

## PHASE 9: Verification Plan

### Automated Tests
1. **Unit Tests**:
   - `AcademicContentRendererTest` — test section numbering, figure/table/equation counting, cross-reference linking
   - `PostModelTest` — test `hasStructuredContent()` accessor
   - `PageModelTest` — test `hasStructuredContent()` accessor

2. **Feature Tests**:
   - `PostResourceTest` — test creating a post with structured JSON content
   - `PageResourceTest` — test creating a page with structured JSON content
   - `AcademicArticleDisplayTest` — test that the frontend renders academic content correctly
   - Data migration test — verify existing HTML posts were converted to valid Tiptap JSON

3. **Run commands**:
   ```bash
   php artisan test
   ./vendor/bin/pint
   ```

### Manual Verification
- Create a sample academic article in Filament with all block types
- Verify the public-facing page renders correctly
- Test cross-reference links (clicking [Fig. 1] scrolls to the figure)
- Test KaTeX equation rendering
- Test on mobile viewport

---

## CONSTRAINTS & GUARDRAILS

1. **No new Composer packages** — use Filament v5 native only
2. **One new npm package** — `katex` only
3. **Hard replace** — drop old `body`/`content` columns, all content uses `content_json` exclusively. Existing data migrated in the migration.
4. **Indonesian-first UX** — all admin labels in Bahasa Indonesia
5. **Language toggle** — output labels (Gambar/Figure, Tabel/Table) switch based on `content_language` field
6. **Performance** — KaTeX loaded only on pages with equations, academic CSS loaded only on structured content pages
7. **Security** — sanitize HTML in table blocks, validate LaTeX input
8. **Follow `.ai/` protocol** — update `history.md`, `state.md`, `memory.json` at session end

---

## START

Begin execution. Follow the CORE framework and 4-Phase Session Loop:
1. **Load Game** — read `.ai/memory/state.md` and confirm context
2. **Plan Before Code** — present the plan (this document) for approval
3. **Debugging Loop** — implement phases 1-8, test after each phase
4. **Save Game** — update `.ai/` files with progress

Execute Phase 1 (database migrations) first, then proceed sequentially.
