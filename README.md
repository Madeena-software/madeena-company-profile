# PT Madeena Karya Indonesia — Company Profile Website

Company profile website for **PT Madeena Karya Indonesia**, an Indonesian manufacturer of Digital Direct Radiography (DDR) equipment based on Camera Coupled X-Ray Detector (CCXD) technology.

> 📄 For full product requirements, data model, and architecture details, see [`docs/PRD.md`](docs/PRD.md). For CMS operator guidance in Indonesian, see [`docs/panduan-cms/panduan-cms.md`](docs/panduan-cms/panduan-cms.md).

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Backend | Laravel (PHP) | 13.x / 8.4 |
| Admin Panel | Filament PHP | 5.x |
| Frontend | Tailwind CSS + Alpine.js | 4.0 / 3.15 |
| Build Tool | Vite | 6.x |
| Database | MySQL | 8.4 |
| Production Server | Nginx + PHP-FPM | Alpine |
| Container | Docker + Docker Swarm | Multi-stage |
| Object Storage | MinIO (S3-compatible) | — |
| CI/CD | GitHub Actions | Workflows |

## Features

- **Public Website** — Homepage with dynamic sections, multilingual support via dynamic Language registry, product catalog (`/produk/{slug}`), academic research articles (`/artikel/{slug}`), custom pages (`/halaman/{slug}`), and contact section with WhatsApp integration.
- **Admin Panel** (`/admin`) — Filament v5 CMS for managing Homepage sections (draft/published & language duplication), Language registry & UI labels, Products, Articles (academic rich editor with KaTeX), Pages (draft/publication lifecycle & preview), Events, Guest Messages, Site Settings, and User accounts.
- **Event & Guest Message Module** — Public feedback form (`/events/{event:slug}/feedback`) with CSRF protection, rate limiting, anti-spam honeypot, duplicate suppression, and a Livewire real-time display (`/events/{event:slug}/display`) for exhibition screens (with legacy redirects from `/inabuyer2026/...`).
- **Object Storage** — MinIO S3-compatible storage for media uploads and database backups.
- **Automated Backups** — Database backup upload to S3 with integrity verification and retention pruning.

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

The `composer dev` command starts all services concurrently: app server (port 8011), queue worker, log tail, and Vite HMR.

## Admin Access

Navigate to `/admin` and log in. You can log in using Madeena IAM SSO or local credentials. The database seeder creates a default admin account using the `FILAMENT_ADMIN_EMAIL` and `FILAMENT_ADMIN_PASSWORD` values from `.env`.

## Docker

```bash
# Local development
docker compose up -d

# Production simulation
docker compose -f docker-compose.simulation.yml up -d
```

> ℹ️ Production deployments are managed via a repository-controlled GitHub Actions Swarm deployment workflow (`.github/workflows/deploy-swarm.yml`) dispatched manually.

## Testing

```bash
php artisan test                         # All tests
php artisan test --testsuite=Feature     # Feature tests only
php artisan test --testsuite=Unit        # Unit tests only
```

## Documentation

- [`docs/PRD.md`](docs/PRD.md) — Product Requirements Document
- [`docs/panduan-cms/panduan-cms.md`](docs/panduan-cms/panduan-cms.md) — Panduan Penggunaan CMS (Bahasa Indonesia)
- [`.agents/AGENTS.md`](.agents/AGENTS.md) — Repository AI delivery framework & agent guidance

## Contact

- **Email**: madeenajog@gmail.com
- **Phone**: +62 821 3811 4011
- **WhatsApp**: +62 857 2830 4141
- **Address**: Jl. Lowanu No. 68-72, Sorosutan, Umbulharjo, Kota Yogyakarta, DIY 55162
