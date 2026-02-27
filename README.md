# Madeena X-Ray — Company Profile Website

Website company profile **PT Madeena Karya Indonesia** — manufacturer alat radiografi digital di Jogja.

**Produk Unggulan:** DDR MADEENA HF100B-MDN  
**Sertifikasi:** Ijin Edar KEMENKES RI AKD21501220581  
**Status:** Static site (HTML5 UP Landed) → akan dimigrasi ke Laravel + Filament CMS

---

## Homework — Konten yang Harus Disediakan

Agar website static ini bisa selesai dan siap launch, berikut yang perlu kamu siapkan.
Centang `[x]` kalau sudah selesai.

---

### 1. FOTO / GAMBAR

> Format: JPG/WebP, max 200KB, min 1200x800px.  
> Taruh di folder `images/` lalu kasih tahu nama filenya.

| # | Kebutuhan | Keterangan | Status |
|---|-----------|------------|--------|
| 1 | **Foto hero banner** | Foto utama DDR Madeena atau lab radiologi. Untuk background banner paling atas | `[ ]` |
| 2 | **Foto riset/kolaborasi** | Foto tim riset, lab UGM, atau kegiatan R&D — untuk section "Riset & Inovasi" | `[ ]` |
| 3 | **Foto produk DDR** | Foto alat DDR Madeena HF100B-MDN (min 1, ideal 3 angle: depan, samping, detail) | `[ ]` |
| 4 | **Foto ruang radiografi** | Foto instalasi lengkap di RS/klinik — untuk section "Ruang Radiografi" | `[ ]` |
| 5 | **Foto sertifikat KEMENKES** | Scan/foto ijin edar — untuk bukti kredibilitas | `[ ]` |
| 6 | **Logo partner** (UGM, dll) | PNG transparent, untuk section kolaborasi | `[ ]` |
| 7 | **Favicon** | Versi kecil logo Madeena (32x32 dan 180x180 px, format PNG) | `[ ]` |
| 8 | **OG Image** (social share) | 1200x630px — gambar yang muncul saat link di-share di WA/FB/LinkedIn | `[ ]` |

**Opsional (nice to have):**
- [ ] Foto tim / founder (400x400, square) — untuk "Tentang Kami"
- [ ] Foto testimonial / client yang sudah pakai alat
- [ ] Video produk (link YouTube)

---

### 2. TEKS / COPYWRITING

> Tulis di mana saja (Google Docs, WA, langsung di file). Nanti saya masukkan ke HTML.

| # | Kebutuhan | Catatan | Status |
|---|-----------|---------|--------|
| 1 | **Tagline banner** | Sekarang cuma "DDR MADEENA". Tulis 1 kalimat jualan, misal: "Solusi Radiografi Digital #1 dari Indonesia" | `[ ]` |
| 2 | **Deskripsi riset UGM** | Detail kolaborasi: tahun berapa, nama peneliti/lab, hasilnya apa | `[ ]` |
| 3 | **Link berita UGM** | URL asli artikel berita UGM (sekarang linknya ke diri sendiri) | `[ ]` |
| 4 | **Spesifikasi DDR** | Spek teknis ringkas: tegangan, arus, ukuran, berat, fitur utama | `[ ]` |
| 5 | **Keunggulan produk** | 3-5 poin kenapa DDR Madeena lebih baik dari kompetitor | `[ ]` |
| 6 | **Isi paket Ruang Radiografi** | Apa saja yang termasuk: alat, instalasi, pelatihan, garansi, dll | `[ ]` |
| 7 | **Daftar RS/klinik client** | Nama rumah sakit/klinik yang sudah pakai (untuk testimoni/trust) | `[ ]` |
| 8 | **Profil perusahaan** | Visi, misi, sejarah singkat, tahun berdiri, jumlah karyawan | `[ ]` |
| 9 | **Profil tim/founder** | Nama, jabatan, foto (opsional) — untuk halaman "Tentang Kami" | `[ ]` |
| 10 | **Jam operasional** | Senin-Jumat jam berapa? Sabtu buka? | `[ ]` |

---

### 3. URL / LINK EKSTERNAL

| # | Kebutuhan | Catatan | Status |
|---|-----------|---------|--------|
| 1 | **Facebook** | URL page Facebook Madeena. Kalau belum ada → bilang "belum ada" | `[ ]` |
| 2 | **Instagram** | URL IG Madeena | `[ ]` |
| 3 | **LinkedIn** | URL company page LinkedIn | `[ ]` |
| 4 | **Twitter/X** | URL akun X. Kalau tidak ada → akan dihapus dari footer | `[ ]` |
| 5 | **YouTube** | URL channel (kalau ada video produk/demo) | `[ ]` |
| 6 | **WhatsApp** | Nomor WA untuk tombol chat (sekarang pakai +62 878 6048 4899 — benar?) | `[ ]` |
| 7 | **Google Maps** | Alamat yang bisa di-pin di Google Maps / link Google Maps | `[ ]` |
| 8 | **Katalog produk** | PDF katalog produk (kalau ada) — untuk tombol "Lihat Semua Produk" | `[ ]` |

---

### 4. KEPUTUSAN YANG PERLU DIAMBIL

Ini bukan konten, tapi keputusan desain yang perlu kamu tentukan:

| # | Pertanyaan | Opsi | Jawaban |
|---|-----------|------|---------|
| 1 | Halaman `left-sidebar.html` mau jadi apa? | A) Tentang Kami  B) Produk Detail  C) Hapus | `____` |
| 2 | Halaman `right-sidebar.html` mau jadi apa? | A) Berita/Artikel  B) FAQ  C) Hapus | `____` |
| 3 | Halaman `no-sidebar.html` mau jadi apa? | A) Kontak Lengkap+Map  B) Daftar Distributor  C) Hapus | `____` |
| 4 | Contact form mau pakai apa untuk sementara? | A) [Formspree](https://formspree.io) (gratis)  B) Hanya link WA/email  C) Nanti di Laravel | `____` |
| 5 | Mau pasang Google Analytics? | A) Ya (kasih GA4 ID)  B) Nanti saja | `____` |
| 6 | Bahasa website? | A) Indonesia saja  B) Indonesia + English | `____` |

---

## Current Status

### Yang Sudah Selesai
- [x] Landing page (index.html) — layout & struktur selesai
- [x] Navigasi & menu dropdown
- [x] Section: Banner, Riset, DDR, Ruang Radiografi, Produk, Filosofi Logo, Kontak
- [x] Footer dengan social icons & copyright
- [x] Responsive design (mobile/tablet/desktop)
- [x] SEO dasar: meta description, keywords, structured data, Open Graph
- [x] robots.txt & sitemap.xml
- [x] HTML semantik & accessible

### Yang Menunggu Konten dari Kamu
- [ ] Foto-foto asli (lihat Section 1)
- [ ] Teks/copywriting (lihat Section 2)
- [ ] URL social media (lihat Section 3)
- [ ] Keputusan halaman (lihat Section 4)

### Yang Akan Dikerjakan Setelah Konten Masuk
- [ ] Replace semua placeholder images
- [ ] Isi teks yang masih generic
- [ ] Aktifkan social media links / hapus yang tidak ada
- [ ] WhatsApp floating button
- [ ] Contact form fungsional
- [ ] Halaman subpages (sesuai keputusan)
- [ ] Final QA & performance check

---

## Project Structure

```
├── index.html              # Landing page utama (✅ selesai)
├── left-sidebar.html       # Subpage — menunggu keputusan
├── right-sidebar.html      # Subpage — menunggu keputusan
├── no-sidebar.html         # Subpage — menunggu keputusan
├── elements.html           # UI reference (internal only)
├── robots.txt              # SEO crawl rules
├── sitemap.xml             # SEO sitemap
├── images/                 # Gambar (perlu diganti foto asli)
└── assets/
    ├── css/                # Compiled CSS
    ├── sass/               # SCSS source files
    ├── js/                 # JavaScript files
    └── webfonts/           # Font files
```

## Tech Stack

- HTML5 / CSS3 / JavaScript
- SCSS (Sass) + jQuery
- [HTML5 UP — Landed](https://html5up.net/landed) template (CCA 3.0 license)
- **Roadmap:** Laravel 11 + Filament 3 CMS → workspace + ecommerce modules

## Contact

- **Email:** info@madeena-xray.com
- **Phone:** +62 878 6048 4899
- **Address:** Jalan Candi Sambisari, Jogja, 55571, Indonesia

## License

Template: [HTML5 UP](https://html5up.net) — [CCA 3.0 license](https://html5up.net/license)  
Content: © 2026 PT Madeena Karya Indonesia
