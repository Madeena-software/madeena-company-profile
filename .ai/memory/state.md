# Session State — Madeena Company Profile

> Last updated: 2026-06-10 19:00 WIB (UTC+7)

---

## 1. System Technology Stack & Major Modules

| Layer          | Technology                                      |
|----------------|-------------------------------------------------|
| Language       | PHP 8.4                                         |
| Framework      | Laravel 13.x                                    |
| Admin Panel    | Filament PHP v5                                 |
| Frontend CSS   | Tailwind CSS v3.4 (with `@tailwindcss/forms`, `@tailwindcss/typography`) |
| Frontend JS    | Alpine.js v3.15                                 |
| Build Tool     | Vite v6 (`laravel-vite-plugin`)                 |
| Template       | Blade                                           |
| Database       | MySQL 8.4                                       |
| Server (Dev)   | Artisan Serve                                   |
| Server (Prod)  | Nginx (Alpine) + PHP 8.4-FPM                    |
| Orchestration  | Docker Swarm (multi-stage Dockerfile)            |
| CI/CD          | GitHub Actions (8 workflows)                    |
| File Storage   | MinIO S3-compatible (bucket: `mmcp-storage`)     |
| Queue          | sync (dev) / database (prod)                    |
| Testing        | PHPUnit 11.x (Unit + Feature suites, SQLite :memory:) |

### Major Modules
- **Models**: User, HeroBanner, Product, Post, Setting, Page, InabuyerMessage
- **Filament Resources**: User, HeroBanner, Product, Post, Page, InabuyerMessage + ManageSettings page
- **Controllers**: HomeController, PublicStorageController, Inabuyer2026/FeedbackController
- **Livewire**: Inabuyer2026Display
- **Custom Artisan Commands**: CheckStorageHealth (`storage:check`), UploadDatabaseBackup (`backup:upload`)
- **Service Providers**: AppServiceProvider

---

## 2. Active Goal & Priorities

🎯 **Active Goal**: Documentation alignment — PRD generated, README and `.ai/` files being aligned. Awaiting user's next primary goal.

**Priority Queue**:
1. ~~Refactor `config/filesystems.php` to S3 driver~~ ✅
2. ~~Replace Artisan commands (WebDAV → generic S3-compatible)~~ ✅
3. ~~Update CI/CD deploy workflow~~ ✅
4. ~~Verification: `composer update`, `php artisan test`, S3 integration test~~ ✅
5. ~~Remove FrankenPHP references from codebase~~ ✅
6. ~~Generate comprehensive PRD (`docs/PRD.md`)~~ ✅
7. ~~Align README and `.ai/` files with PRD~~ ✅

---

## 3. Recent Milestones Completed

| Date       | Milestone                                        | Status |
|------------|--------------------------------------------------|--------|
| 2026-06-04 | `.ai/` control center bootstrapped               | ✅     |
| 2026-06-04 | Full repository audit completed                  | ✅     |
| 2026-06-04 | Storage refactored: WebDAV → MinIO S3            | ✅     |
| 2026-06-04 | S3 Integration and PHPUnit verification passed   | ✅     |
| 2026-06-10 | FrankenPHP references removed from codebase      | ✅     |
| 2026-06-10 | Comprehensive PRD generated (`docs/PRD.md`)      | ✅     |
| 2026-06-10 | README and `.ai/` files aligned with PRD         | ✅     |

---

## 4. Environment & Health Status

| Check             | Status  | Notes                                    |
|-------------------|---------|------------------------------------------|
| PHP Version       | ✅ 8.4  | Matches `composer.json` platform config |
| Node/NPM          | ✅       | `node_modules/` present                |
| Composer Deps     | ✅       | `vendor/` present, `composer.lock` synced |
| Database          | ✅       | Connection healthy, migrations passed   |
| `.env`            | ✅       | `.env` and `.env.local` present         |
| Docker            | ✅       | Multi-stage Dockerfile + Swarm compose  |
| GitHub Workflows  | ✅       | 8 workflows configured                  |
| MinIO S3          | ✅       | Verified with `storage:check` command   |
| PRD               | ✅       | `docs/PRD.md` generated and validated   |

---

## 5. Known Issues

- `.env.example` stale `OCTANE_SERVER`/`OCTANE_HTTPS` references — **fixed 2026-06-10**.
- `config/octane.php` and `laravel/octane` dependency still present in codebase (not actively used).

---

## 6. Next Steps

1. Await user's next instructions.
