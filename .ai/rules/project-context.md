# Project Context — Madeena Company Profile

## Project Overview

**PT Madeena Karya Indonesia** — Company profile website for an Indonesian manufacturer of Digital Direct Radiography (DDR) equipment. The site serves as both a public-facing marketing website and a content-managed CMS via Filament admin panel.

### Key Features
- **Public Website**: Homepage with hero banners, product catalog, blog, about section, legal/certification info, and contact details with WhatsApp integration.
- **Admin Panel** (`/admin`): Filament v5-powered CMS for managing hero banners, products, blog posts, pages, site settings, users, and Inabuyer 2026 event messages.
- **Inabuyer 2026 Module**: Event feedback form with CSRF protection and a Livewire-based live display component.
- **Storage**: MinIO S3-compatible object storage for public media and backups (same endpoint for dev and prod).
- **Backup**: Automated database backup upload to MinIO S3 via custom Artisan command.

---

## Key Technologies

| Component        | Technology             | Version  |
|------------------|------------------------|----------|
| Backend          | Laravel (PHP)          | 13.x / 8.4 |
| Admin Panel      | Filament PHP           | 5.x      |
| Frontend         | Tailwind CSS + Alpine.js | 3.4 / 3.15 |
| Build            | Vite                   | 6.x      |
| Database         | MySQL                  | 8.4      |
| Dev Server       | Artisan Serve          | —        |
| Prod Server      | Nginx + PHP-FPM        | Alpine   |
| Container        | Docker + Docker Swarm  | Multi-stage |
| CI/CD            | GitHub Actions         | 8 workflows |
| Testing          | PHPUnit                | 11.x    |

---

## Setup Instructions

### Prerequisites
- PHP 8.4 (with extensions: bcmath, curl, gd, intl, mbstring, opcache, pcntl, pdo_mysql, zip)
- Composer 2.x
- Node.js 24.x + npm
- MySQL 8.4
- (Optional) Docker & Docker Compose for containerized development

### Step-by-Step Local Setup

```bash
# 1. Clone and enter the project
git clone <repo-url>
cd madeena-website-company-profile

# 2. (Optional) Setup PHP 8.4 runtime on WSL
./setup-environment-84.sh

# 3. Install PHP dependencies
composer install

# 4. Install Node dependencies
npm install

# 5. Configure environment
cp .env.example .env
php artisan key:generate
# Edit .env with your database credentials

# 6. Run database migrations and seeders
php artisan migrate
php artisan db:seed

# 7. Create storage symlink
php artisan storage:link

# 8. Create an admin user
php artisan make:filament-user
```

### Running the Application

```bash
# Full dev stack (Serve + Queue + Pail Logs + Vite HMR) — recommended
composer dev

# Individual services
php artisan serve --host=127.0.0.1 --port=8000  # App server
npm run dev                                                    # Vite HMR
php artisan queue:listen --tries=1                             # Queue worker
php artisan pail --timeout=0                                   # Log tail
```

### Building for Production

```bash
npm run build               # Compile frontend assets
php artisan optimize         # Cache config, routes, views
```

### Running Tests

```bash
php artisan test             # Run all tests (PHPUnit)
php artisan test --testsuite=Unit      # Unit tests only
php artisan test --testsuite=Feature   # Feature tests only
```

### Docker Development

```bash
# Local Docker (simple)
docker compose up -d

# Production simulation
./scripts/simulate-prod.sh
# or
docker compose -f docker-compose.simulation.yml up -d

# Production deployment (Swarm mode)
docker compose -f docker-compose.prod.yml config  # Validate
docker stack deploy -c docker-compose.prod.yml madeena_cp
```

---

## Repository Structure

```
├── app/
│   ├── Console/Commands/       # Custom Artisan Commands: CheckStorageHealth (storage:check), UploadDatabaseBackup (backup:upload)
│   ├── Filament/               # Admin panel resources, pages, auth
│   ├── Http/Controllers/       # Public-facing controllers
│   ├── Livewire/               # Livewire components (Inabuyer display)
│   ├── Models/                 # Eloquent models (7 models)
│   ├── Policies/               # Authorization policies
│   └── Providers/              # Service providers (App)
├── config/                     # Laravel configuration files
├── database/
│   ├── factories/              # Model factories
│   ├── migrations/             # Database migrations (14 files)
│   └── seeders/                # Database seeders (7 files)
├── docker/                     # Docker configs (nginx, php.ini, supervisord, entrypoint)
├── nginx/                      # Nginx production configs
├── public/                     # Public assets (entry point: index.php)
├── resources/
│   ├── css/                    # Tailwind CSS source
│   ├── js/                     # Alpine.js entry point
│   └── views/                  # Blade templates (layouts, pages, Livewire, Filament)
├── docs/                       # Documentation (PRD.md)
├── routes/                     # Route definitions (web.php, console.php)
├── scripts/                    # Deployment & simulation scripts
├── storage/                    # Laravel storage (logs, cache, uploads)
├── tests/                      # PHPUnit tests (Unit + Feature)
├── .github/workflows/          # CI/CD workflows (8 files)
├── Dockerfile                  # Multi-stage production build
├── docker-compose.prod.yml     # Swarm production compose
├── docker-compose.yml          # Basic dev compose
├── docker-compose.local.yml    # Local dev compose
└── docker-compose.simulation.yml # Production simulation
```

---

## Coding Conventions

### PHP / Laravel
- **PSR-4 autoloading** with `App\` namespace rooted in `app/`.
- **Laravel Pint** (`laravel/pint`) for code formatting — run `./vendor/bin/pint` before commits.
- **Type declarations**: Use PHP 8.4 typed properties, return types, and `void` on all methods.
- **Strict types**: Prefer `declare(strict_types=1)` in new files.
- **Eloquent conventions**: Use model route binding (`:slug`), `$fillable` arrays, and relationship methods.
- **Filament resources**: Follow Filament v5 conventions — each resource in `app/Filament/Resources/` with corresponding `Pages/` subdirectory.

### Frontend
- **Tailwind CSS v3** with custom `madeena` color palette (`blue: #1e3a5f`, `teal: #1a9b8a`, `light: #e8f4f8`).
- **Inter** as the default sans-serif font family.
- **Alpine.js** for client-side interactivity — no heavy JS frameworks.
- **Blade components** and layouts — the main layout is `resources/views/layouts/app.blade.php`.

### Database
- **Migrations**: Timestamped with `YYYY_MM_DD_NNNNNN_` prefix convention.
- **Foreign keys**: Use `constrained()->cascadeOnDelete()` where applicable.
- **Seeders**: Each model has its own seeder; `DatabaseSeeder` orchestrates all.

### Testing
- **PHPUnit 11.x** with `Unit` and `Feature` test suites.
- **Testing database**: SQLite `:memory:` (configured in `phpunit.xml`).
- **Faker**: Available via `fakerphp/faker` for test data generation.

### Version Control
- **`.gitignore`** excludes `vendor/`, `node_modules/`, `.env`, and build artifacts.
- **`.editorconfig`** enforces consistent whitespace (spaces, not tabs).
- **StyleCI** (`.styleci.yml`) for automated code style checks.
