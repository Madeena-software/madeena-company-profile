# PT Madeena Karya Indonesia — Website Company Profile

Website company profile untuk PT Madeena Karya Indonesia, produsen alat Digital Direct Radiography (DDR) buatan Indonesia.

## Tech Stack

- **Framework**: Laravel 13
- **Admin Panel**: Filament PHP v5
- **PHP Engine**: PHP 8.4
- **CSS Framework**: Tailwind CSS v3
- **JS**: Alpine.js
- **Build Tool**: Vite

## Fitur

- Halaman beranda dengan hero section, produk, tentang kami, legalitas, blog, dan kontak
- Admin panel Filament di `/admin` untuk mengelola:
    - Hero Banner
    - Produk
    - Blog
    - Pengaturan website
- Responsive mobile-friendly design
- WhatsApp floating button
- SEO meta tags dari database

## Instalasi

```bash
# 1. Clone repository
git clone <repo-url>
cd website-company-profile-madeena

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Copy environment file
cp .env.example .env
php artisan key:generate

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_DATABASE=madeena
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations & seeders
php artisan migrate
php artisan db:seed

# 7. Build frontend assets
npm run build

# 8. Create storage link
php artisan storage:link

# 9. Create admin user
php artisan make:filament-user

# 10. Serve
php artisan serve
```

## Akses Admin

Buka `/admin` di browser dan login dengan akun yang dibuat via `php artisan make:filament-user`.

## Deployment

Dokumentasi deployment dipisah di [README-DEPLOYMENT.md](README-DEPLOYMENT.md).

## Kontak

- **Email**: madeenajog@gmail.com
- **Telepon**: +62 821 3811 4011
- **WhatsApp**: +62 857 2830 4141
- **Alamat**: Jl. Lowanu No. 68-72, Sorosutan, Umbulharjo, Kota Yogyakarta, DIY 55162
