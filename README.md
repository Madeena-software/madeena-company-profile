# PT Madeena Karya Indonesia — Company Profile Website

Company profile website for **PT Madeena Karya Indonesia**, an Indonesian manufacturer of Digital Direct Radiography (DDR) equipment based on Camera Coupled X-Ray Detector (CCXD) technology.

> 📄 For full product requirements, data model, and architecture details, see [`docs/PRD.md`](docs/PRD.md).

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Backend | Laravel (PHP) | 13.x / 8.4 |
| Admin Panel | Filament PHP | 5.x |
| Frontend | Tailwind CSS + Alpine.js | 3.4 / 3.15 |
| Build Tool | Vite | 6.x |
| Database | MySQL | 8.4 |
| Production Server | Nginx + PHP-FPM | Alpine |
| Container | Docker + Docker Swarm | Multi-stage |
| Object Storage | MinIO (S3-compatible) | — |
| CI/CD | GitHub Actions | 8 workflows |

## Features

- **Public Website** — Homepage with hero banners, product catalog, blog, company profile, certifications, and contact section with WhatsApp integration
- **Admin Panel** (`/admin`) — Filament v5 CMS for managing hero banners, products, blog posts, pages, site settings, and user accounts
- **Inabuyer 2026 Module** — Event feedback form with CSRF protection and a Livewire-based real-time display for exhibition screens
- **Object Storage** — MinIO S3-compatible storage for media uploads and database backups
- **Automated Backups** — Database backup upload to S3 with integrity verification and retention pruning

## Quick Start

```bash
# 1. Clone and enter the project
git clone <repo-url>
cd madeena-company-profile

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate
# Edit .env with your database and MinIO credentials

# 4. Run database migrations and seeders
php artisan migrate
php artisan db:seed

# 5. Create storage symlink
php artisan storage:link

# 6. Start the dev server
composer dev
```

The `composer dev` command starts all services concurrently: app server (port 8000), queue worker, log tail, and Vite HMR.

## Admin Access

Navigate to `/admin` and log in. You can log in using Madeena IAM SSO or traditional credentials. The database seeder creates a default admin account using the `FILAMENT_ADMIN_EMAIL` and `FILAMENT_ADMIN_PASSWORD` values from `.env`.

## Docker

```bash
# Local development
docker compose up -d

# Production simulation
docker compose -f docker-compose.simulation.yml up -d

# Production deployment (Swarm)
docker stack deploy -c docker-compose.prod.yml madeena_cp
```

## Testing

```bash
php artisan test                         # All tests
php artisan test --testsuite=Feature     # Feature tests only
php artisan test --testsuite=Unit        # Unit tests only
```

## Documentation

- [`docs/PRD.md`](docs/PRD.md) — Full Product Requirements Document
- [`README-DEPLOYMENT.md`](README-DEPLOYMENT.md) — Deployment guide
- [`.ai/`](.ai/README.md) — AI agent control center

## Contact

- **Email**: madeenajog@gmail.com
- **Phone**: +62 821 3811 4011
- **WhatsApp**: +62 857 2830 4141
- **Address**: Jl. Lowanu No. 68-72, Sorosutan, Umbulharjo, Kota Yogyakarta, DIY 55162
