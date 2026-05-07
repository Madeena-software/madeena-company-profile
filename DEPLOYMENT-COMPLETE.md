# 🚀 Complete Docker Deployment Documentation

## Summary of What Has Been Created

This repository now has a **complete, production-ready Docker deployment architecture** for both local development and production deployment. Below is a comprehensive overview of all components.

---

## 📦 Files Created

### Core Docker Configuration

| File | Purpose |
|------|---------|
| **Dockerfile** | Multi-stage Docker image for PHP 8.3, Nginx, and Laravel app |
| **docker-compose.yml** | Local development: MySQL 8.4 + Nginx (PHP runs natively on WSL) |
| **docker-compose.prod.yml** | Production: App container + MySQL + Redis + Nginx |

### Docker Configuration Files (in `docker/` directory)

| File | Purpose |
|------|---------|
| **nginx.conf** | Main Nginx configuration with gzip, caching, and security headers |
| **app.conf** | Nginx app-to-PHP routing (not used for prod, kept for reference) |
| **nginx-dev.conf** | Development Nginx config (routes to host PHP-FPM on `host.docker.internal:9000`) |
| **nginx-prod.conf** | **Production Nginx config with `/company-profile` subdirectory routing** |
| **php.ini** | PHP configuration (memory, timeouts, opcache, timezone) |
| **www.conf** | PHP-FPM worker pool configuration |
| **supervisord.conf** | Supervisor config to manage PHP-FPM + Nginx in container |
| **entrypoint.sh** | Container startup script (runs migrations, caching, optimization) |

### CI/CD Pipeline

| File | Purpose |
|------|---------|
| **.github/workflows/deploy-docker.yml** | **Complete GitHub Actions workflow** (build → push → SSH deploy → migrate → health check) |

### Documentation & Setup Guides

| File | Purpose |
|------|---------|
| **DOCKER-SETUP.md** | Comprehensive architecture guide with detailed setup instructions |
| **GITHUB-SECRETS.md** | GitHub Secrets configuration reference |
| **QUICKSTART.sh** | Quick reference script with common commands |
| **.env.example** | Updated with Docker-aware configuration and inline comments |

---

## 🎯 Key Features

### ✅ Production Setup (`docker-compose.prod.yml`)

```
┌─────────────────────────────────┐
│  Nginx (SSL via Certbot)        │
│  Routes /company-profile → App  │
└──────────┬──────────────────────┘
           │ (port 9000)
     ┌─────▼────────────┐
     │  Laravel App     │
     │  (PHP 8.3-FPM    │
     │   + Supervisor)  │
     └────┬────────┬────┘
          │        │
    ┌─────▼─┐  ┌──▼────┐
    │ MySQL │  │ Redis │
    │ 8.4   │  │ 7     │
    └───────┘  └───────┘
```

**Production Features:**
- Dockerfile builds optimized image with all dependencies
- Docker Compose orchestrates app, MySQL, Redis, Nginx
- Redis for cache, session, and queue
- Supervisor manages PHP-FPM and Nginx in single container
- Health checks on all services
- Entrypoint script auto-runs migrations and caching

---

### ✅ Local Development Setup (`docker-compose.yml`)

```
┌─────────────────────────┐
│  Nginx (Docker)         │
│  Port: 80               │
└──────────┬──────────────┘
           │ (host.docker.internal:9000)
     ┌─────▼────────────┐
     │  PHP 8.3 (Native)│
     │  Running on WSL  │
     │  php artisan     │
     │  serve           │
     └────────┬─────────┘
              │ (3306)
         ┌────▼────┐
         │  MySQL  │
         │  8.4    │
         │(Docker) │
         └─────────┘
```

**Local Development Features:**
- PHP 8.3 runs natively on WSL (no containerization overhead)
- MySQL 8.4 runs in Docker
- Nginx runs in Docker (reverse proxy)
- MySQL exposed on `127.0.0.1:3306` for WSL PHP to connect
- Perfect for debugging and development

---

### ✅ GitHub Actions CI/CD Workflow

```
┌─────────────────────────────────────────────────────┐
│  Job 1: Build & Push                                │
│  - build Dockerfile                                 │
│  - push to Docker Hub                               │
└────────────────────┬────────────────────────────────┘
                     │ (needs: build-and-push)
┌────────────────────▼────────────────────────────────┐
│  Job 2: Deploy (SSH to VPS)                         │
│  - docker pull latest image                         │
│  - docker-compose -f docker-compose.prod.yml up -d  │
│  - php artisan migrate --force                      │
│  - show docker logs --tail 100                      │
│  - certbot renew (SSL)                              │
│  - curl health check /company-profile/health        │
└─────────────────────────────────────────────────────┘
```

**Workflow Features:**
- Triggers on push to `main` or `master` branch
- Builds and pushes image to Docker Hub
- SSHes into VPS with GitHub Secrets
- Auto-deploys with docker-compose using the freshly pulled image
- Runs database migrations
- **Shows 100 lines of application logs in Actions console** (you don't need to SSH)
- Renews SSL certificates
- Health checks the application
- Handles rollback if anything fails

---

## 🔑 Subdirectory Routing (`/company-profile`)

The Nginx production configuration (`docker/nginx-prod.conf`) is specially configured to:

1. **Accept requests** to `/company-profile/*` path
2. **Rewrite internally** to `/index.php` (Laravel routing)
3. **Preserve the path** for Laravel to handle routing
4. **Forward to PHP-FPM** on port 9000

Example URLs:
```
https://example.com/company-profile          → Homepage
https://example.com/company-profile/products → Products page
https://example.com/company-profile/blog     → Blog page
https://example.com/company-profile/admin    → Admin panel
```

---

## 🛠️ Setup Instructions

### LOCAL DEVELOPMENT (WSL)

```bash
# 1. Clone repo
cd /var/www
git clone https://github.com/your-org/madeena-website-company-profile.git
cd madeena-website-company-profile

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Install dependencies (native PHP 8.3)
composer install
npm install && npm run build

# 4. Start Docker services (MySQL + Nginx)
docker-compose up -d

# 5. Setup database (native PHP 8.3)
php artisan migrate
php artisan db:seed        # Optional
php artisan storage:link

# 6. Run development server
php artisan serve
# Access: http://localhost:8000
```

**Verify local setup:**
```bash
docker-compose ps                    # Should see MySQL and Nginx running
mysql -h 127.0.0.1 -u madeena -p   # Test MySQL connection
curl http://localhost                # Test Nginx is up
```

---

### PRODUCTION DEPLOYMENT

#### Step 1: Configure GitHub Secrets

See `GITHUB-SECRETS.md` for complete list. Minimum required:

```
DOCKER_USERNAME          (Docker Hub username)
DOCKER_PASSWORD          (Docker Hub access token)
VPS_HOST                 (VPS IP or domain)
VPS_USER                 (SSH user, e.g., ubuntu)
VPS_SSH_KEY              (Private SSH key)
APP_DOMAIN               (Production domain)
APP_KEY                  (Laravel APP_KEY)
DB_DATABASE, DB_USERNAME, DB_PASSWORD, DB_ROOT_PASSWORD
REDIS_PASSWORD
MAIL_* (email configuration)
```

#### Step 2: First-Time VPS Setup

```bash
# SSH into VPS
ssh -i your_key.pem user@your_vps_ip

# Create deployment directory
sudo mkdir -p /var/www/madeena-company-profile
cd /var/www/madeena-company-profile

# Clone the repository
sudo git clone https://github.com/your-org/madeena-website-company-profile.git .

# Set permissions
sudo chown -R $(whoami):$(whoami) .
```

#### Step 3: Automated Deployment

```bash
# Simply push to main/master
git push origin main

# Monitor in GitHub:
# - Go to Actions tab
# - Watch build → push → deploy
# - View logs directly in Actions console
# - See application deployment logs (100 lines)
# - See health check results
```

#### Step 4: SSL Certificate (One-time)

```bash
# SSH into VPS
sudo certbot certonly --standalone -d example.com

# Update Nginx to use certificates
# (Already configured in docker/nginx-prod.conf and deploy workflow)
```

---

## 🔐 Environment Variables

### Local Development (native PHP)

The `docker-compose.yml` exposes MySQL on port 3306:

```env
DB_HOST=127.0.0.1        # Not 'localhost' - must be IP
DB_PORT=3306
DB_DATABASE=madeena_company_profile
DB_USERNAME=madeena
DB_PASSWORD=madeena_pass
CACHE_DRIVER=database
SESSION_DRIVER=database
```

### Production (Docker)

The `docker-compose.prod.yml` provides internal service names:

```env
DB_HOST=mysql            # Internal service name
DB_PORT=3306
CACHE_DRIVER=redis
SESSION_DRIVER=database
REDIS_HOST=redis
QUEUE_CONNECTION=redis
```

---

## 📊 Architecture Comparison

| Aspect | Local Dev | Production |
|--------|-----------|-----------|
| **PHP** | Native WSL 8.3 | Docker Container |
| **MySQL** | Docker | Docker |
| **Nginx** | Docker | Docker |
| **Redis** | None (database caching) | Docker |
| **Build** | Manual compose up | GitHub Actions |
| **Deployment** | Local testing | SSH + docker-compose |
| **SSL** | None (http://localhost) | Certbot (https) |
| **Logging** | Console | docker logs --tail 100 |
| **Health Check** | Visual testing | curl endpoint |

---

## 🐛 Troubleshooting

### Local Development Issues

**MySQL connection refused**
```bash
docker-compose ps                    # Check MySQL is running
docker-compose logs mysql            # View MySQL logs
mysql -h 127.0.0.1 -u madeena -p   # Test connection manually
```

**Nginx 502 Bad Gateway**
```bash
netstat -tulpn | grep 9000          # Check PHP-FPM listening
docker-compose logs nginx            # View Nginx logs
php artisan serve                    # Ensure you ran serve command
```

**Database migration fails**
```bash
grep DB_ .env                        # Verify DB_HOST is 127.0.0.1
php artisan migrate --verbose        # Run with verbose output
```

### Production Deployment Issues

**Docker build fails**
- Check GitHub Actions logs
- Verify Dockerfile syntax
- Ensure all required files exist

**SSH deployment fails**
- Verify SSH key added to VPS user's `~/.ssh/authorized_keys`
- Check VPS_HOST, VPS_USER, VPS_SSH_KEY secrets
- Ensure deploy path exists on VPS

**Application doesn't start**
- SSH to VPS: `docker-compose -f docker-compose.prod.yml logs app`
- Check .env file exists and has correct values
- Verify MySQL and Redis are healthy

**Health check fails**
- Check application is responding: `curl https://example.com/company-profile/health`
- View Nginx logs: `docker-compose logs nginx`
- View app logs: `docker-compose logs app`

---

## 📚 Quick References

### Common Commands

**Local Development:**
```bash
docker-compose up -d                 # Start services
docker-compose down                  # Stop services
docker-compose logs -f app           # Stream logs
php artisan migrate                  # Run migrations
php artisan serve                    # Start dev server
npm run dev                          # Watch assets
```

**Production (VPS):**
```bash
docker-compose -f docker-compose.prod.yml up -d     # Start
docker-compose -f docker-compose.prod.yml down       # Stop
docker-compose -f docker-compose.prod.yml logs app   # View logs
docker-compose -f docker-compose.prod.yml ps         # Check status
```

### File Locations

**Local Development:**
- Code: `/var/www/madeena-website-company-profile`
- MySQL data: Docker volume `mysql_dev_data`

**Production (VPS):**
- Code: `/var/www/madeena-company-profile` (from secret)
- MySQL data: Docker volume `mysql_data`
- SSL certs: `/etc/letsencrypt/live/example.com/`
- Nginx config: Inside container

---

## 📝 Configuration Checklist

- [ ] GitHub Secrets configured (`GITHUB-SECRETS.md`)
- [ ] Docker Hub account created with access token
- [ ] VPS provisioned with Docker and Docker Compose
- [ ] SSH key generated and added to VPS
- [ ] Domain DNS pointing to VPS public IP
- [ ] `.env.example` updated (✓ done)
- [ ] Dockerfile created (✓ done)
- [ ] docker-compose files created (✓ done)
- [ ] Nginx configs created (✓ done)
- [ ] GitHub Actions workflow created (✓ done)
- [ ] Documentation complete (✓ done)

---

## 🚀 Next Steps

1. **Configure GitHub Secrets** (See `GITHUB-SECRETS.md`)
2. **Test Locally** (See Local Dev setup above)
3. **Push to Repository** 
4. **Monitor GitHub Actions** (Actions tab)
5. **Verify Production Deployment** (Visit https://your-domain.com/company-profile)

---

## 📖 Additional Documentation

- **DOCKER-SETUP.md** - Detailed architecture and setup guide
- **GITHUB-SECRETS.md** - GitHub Secrets configuration reference
- **QUICKSTART.sh** - Quick command reference
- **README-DEPLOYMENT.md** - Manual deployment steps (legacy)

---

## 🤝 Support

For issues:
1. Check troubleshooting section above
2. Review GitHub Actions logs (for deployment issues)
3. Check container logs: `docker-compose logs <service>`
4. Consult documentation files
5. Review Laravel logging: `storage/logs/`

---

**Deployment Architecture Created: ✅**
- Production Docker infrastructure ✅
- Local WSL development setup ✅
- GitHub Actions CI/CD pipeline ✅
- Comprehensive documentation ✅
- `/company-profile` subdirectory routing ✅
- Health checks and logging ✅
