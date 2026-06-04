# Session State — Madeena Company Profile

> Last updated: 2026-06-04 11:30 WIB (UTC+7)

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
| Server (Dev)   | FrankenPHP via Laravel Octane                    |
| Server (Prod)  | Nginx (Alpine) + PHP 8.4-FPM                    |
| Orchestration  | Docker Swarm (multi-stage Dockerfile)            |
| CI/CD          | GitHub Actions (8 workflows)                    |
| File Storage   | Nextcloud WebDAV (prod) / Local filesystem (dev)|
| Queue          | sync (dev) / database (prod)                    |
| Testing        | PHPUnit 11.x (Unit + Feature suites, SQLite :memory:) |

### Major Modules
- **Models**: User, HeroBanner, Product, Post, Setting, Page, InabuyerMessage
- **Filament Resources**: User, HeroBanner, Product, Post, Page, InabuyerMessage + ManageSettings page
- **Controllers**: HomeController, PublicStorageController, Inabuyer2026/FeedbackController
- **Livewire**: Inabuyer2026Display
- **Custom Artisan Commands**: CheckWebDavStorage, UploadDatabaseBackupToWebDav
- **Service Providers**: AppServiceProvider, WebDavFilesystemServiceProvider

---

## 2. Active Goal & Priorities

🎯 **Active Goal**: Awaiting user input

**Priority Queue**:
1. _(empty — awaiting user direction)_

---

## 3. Recent Milestones Completed

| Date       | Milestone                                        | Status |
|------------|--------------------------------------------------|--------|
| 2026-06-04 | `.ai/` control center bootstrapped               | ✅     |
| 2026-06-04 | Full repository audit completed                  | ✅     |

---

## 4. Environment & Health Status

| Check             | Status  | Notes                                    |
|-------------------|---------|------------------------------------------|
| PHP Version       | ✅ 8.4  | Matches `composer.json` platform config |
| Node/NPM          | ✅       | `node_modules/` present                |
| Composer Deps     | ✅       | `vendor/` present, `composer.lock` synced |
| Database          | ⚠️ Unknown | Requires `php artisan migrate:status` check |
| `.env`            | ✅       | `.env` and `.env.local` present         |
| Docker            | ✅       | Multi-stage Dockerfile + Swarm compose  |
| GitHub Workflows  | ✅       | 8 workflows configured                  |
| FrankenPHP Binary | ✅       | `frankenphp` binary present at repo root |

---

## 5. Known Issues

- None documented yet. Will be populated as issues are discovered.

---

## 6. Next Steps

1. **Receive primary goal** from the user to set the active objective.
2. **Run `php artisan migrate:status`** to verify database health.
3. **Run `php artisan test`** to verify test suite baseline.
4. Begin work on the user's requested feature or fix.
