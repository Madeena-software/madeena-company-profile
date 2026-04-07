# Deployment Guide — PT Madeena Karya Indonesia

Dokumen deployment ini ditulis agar bisa dipakai oleh:

- **Human operator** (langkah ringkas dan checklist)
- **AI agent** (langkah deterministik, berurutan, dan bisa dieksekusi otomatis)

## 1) Prasyarat Server Production

- OS Linux (Ubuntu/Debian)
- PHP 8.3+ beserta ekstensi Laravel umum (`mbstring`, `xml`, `curl`, `zip`, `bcmath`, `intl`, `pdo_mysql`)
- Composer
- Node.js 20+ dan npm
- Web server: Nginx
- Database MySQL/MariaDB

> Catatan repo: project ini juga mendukung storage path berbasis env `STORAGE_BASE_PATH`.

---

## 2) Setup Subdomain ke Public IP

Contoh target: `profile.example.com`

1. Dapatkan public IP server (misal `203.0.113.10`).
2. Di DNS provider, buat record:
    - Type: `A`
    - Host/Name: `profile` (atau sesuai kebutuhan)
    - Value: `203.0.113.10`
3. Tunggu propagasi DNS.
4. Verifikasi dari terminal:

```bash
dig +short profile.example.com
```

Output harus mengarah ke public IP server.

---

## 3) Deploy Manual (Human-Friendly)

```bash
# 1. Clone project ke server
git clone <repo-url> /var/www/madeena-website-company-profile
cd /var/www/madeena-website-company-profile

# 2. Install dependencies production
composer install --no-dev --optimize-autoloader
npm install
npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env (contoh minimal)
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://profile.example.com
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=madeena
# DB_USERNAME=...
# DB_PASSWORD=...
# STORAGE_BASE_PATH=/mnt/local_madeena_website_data/storage

# 5. Database
php artisan migrate --force
php artisan db:seed --force

# 6. Symlink storage
php artisan storage:link

# 7. Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Permission (sesuaikan user web server)
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

---

## 4) Nginx Virtual Host (Subdomain)

Contoh file: `/etc/nginx/sites-available/profile.example.com`

```nginx
server {
    listen 80;
    server_name profile.example.com;

    root /var/www/madeena-website-company-profile/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Aktifkan site dan reload Nginx:

```bash
ln -s /etc/nginx/sites-available/profile.example.com /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

Opsional SSL (disarankan):

```bash
apt install certbot python3-certbot-nginx -y
certbot --nginx -d profile.example.com
```

---

## 5) Deploy Steps (AI Agent Mode)

Gunakan urutan berikut secara **strict order**:

1. `cd /var/www/madeena-website-company-profile`
2. `docker pull <image>:latest`
3. `docker compose -f docker-compose.prod.yml down`
4. `docker compose -f docker-compose.prod.yml up -d`
5. `docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force`
6. `docker compose -f docker-compose.prod.yml exec -T app php artisan optimize:clear`
7. `docker compose -f docker-compose.prod.yml exec -T app php artisan config:cache`
8. `docker compose -f docker-compose.prod.yml exec -T app php artisan route:cache`
9. `docker compose -f docker-compose.prod.yml exec -T app php artisan view:cache`
10. `docker compose -f docker-compose.prod.yml exec -T app php artisan octane:reload`

Validasi setelah deploy:

- `docker compose -f docker-compose.prod.yml exec -T app php artisan about`
- `docker compose -f docker-compose.prod.yml exec -T app php artisan route:list | head`
- `curl -I https://profile.example.com`

Jika rollback diperlukan:

1. pull image tag stabil sebelumnya
2. jalankan kembali langkah 3-10

---

## 6) Checklist Verifikasi

- Subdomain membuka homepage tanpa error 500
- `/admin` bisa login
- Gambar/file upload muncul lewat `/storage/...`
- Data produk/blog tampil di frontend
- Cache sudah aktif untuk mode production
