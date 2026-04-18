# Docker Deployment Architecture for Madeena Company Profile

This document describes the complete Docker-based production and local development setup for the Madeena Company Profile Laravel 13 application.

## 📋 Overview

- **Production**: Docker Compose with Laravel app container, MySQL 8.4, Redis, and Nginx reverse proxy
- **Local Development**: Native PHP 8.4 (WSL) + Dockerized MySQL and Nginx
- **Subdirectory Routing**: Application served from `/company-profile` path
- **CI/CD**: GitHub Actions workflow with automated build, push, and deployment

---

## 🏗️ Architecture Overview

### Production Stack (docker-compose.prod.yml)
```
┌─────────────────────────────────────────────────────────┐
│                    Nginx (Port 80/443)                  │
│              (Reverse Proxy + SSL Termination)          │
│                    ↓                                      │
│         Routes to /company-profile path                 │
│                    ↓                                      │
├─────────────────────────────────────────────────────────┤
│  Laravel App Container (PHP 8.4-FPM + Supervisor)       │
│  - PHP-FPM listens on port 9000                         │
│  - Supervisor manages PHP-FPM + Nginx                   │
│                    ↓                                      │
├─────────────────────────────────────────────────────────┤
│  MySQL 8.4              │  Redis 7 (Alpine)             │
│  (Database)             │  (Cache + Session + Queue)    │
└─────────────────────────────────────────────────────────┘
```

### Local Development Stack (docker-compose.yml)
```
┌─────────────────────────────────────┐
│   Nginx (Docker, Port 80)            │
│   Routes to PHP-FPM on host          │
│            ↓                         │
├─────────────────────────────────────┤
│  PHP 8.4 (Native WSL, Port 9000)    │
│  Running: php artisan serve          │
│            ↓                         │
├─────────────────────────────────────┤
│  MySQL 8.4 (Docker, Port 3306)      │
│  (Database)                         │
└─────────────────────────────────────┘
```

---

## 🚀 Local Development Setup (WSL)

### Prerequisites
- PHP 8.4 installed natively on WSL
- Docker and Docker Compose installed on WSL
- Git, Composer, Node.js 20+

### Standardize WSL runtime to PHP 8.4

```bash
./setup-environment-84.sh
```

### Step 1: Clone and Setup

```bash
cd /var/www/madeena-website-company-profile

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Install PHP dependencies (using native PHP)
composer install

# Install Node dependencies
npm install && npm run build
```

### Step 2: Start Docker Services (MySQL + Nginx only)

```bash
# Start MySQL and Nginx containers for local development
docker-compose up -d

# Verify containers are running
docker-compose ps
```

### Step 3: Configure PHP 8.4 Locally

```bash
# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Create storage symlink
php artisan storage:link

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Run Development Server

```bash
# Option A: Using PHP's built-in server
php artisan serve

# Option B: Using PHP-FPM (recommended for production-like environment)
# Make sure Nginx is running via docker-compose
# Access via http://localhost
```

### Environment Variables for Local Development

The `.env` file should have:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=madeena_company_profile
DB_USERNAME=madeena
DB_PASSWORD=madeena_pass

CACHE_DRIVER=database
SESSION_DRIVER=cookie
QUEUE_CONNECTION=database
```

### Database Connection from WSL PHP to Docker MySQL

The local `docker-compose.yml` exposes MySQL on `127.0.0.1:3306`, allowing your native WSL PHP to connect directly:

```bash
# Test connection from WSL
mysql -h 127.0.0.1 -u madeena -pmadeena_pass -D madeena_company_profile -e "SELECT 1;"
```

---

## 🐳 Production Deployment (Docker)

### Prerequisites on VPS

- Linux (Ubuntu 22.04+ recommended)
- Docker and Docker Compose installed
- Git installed
- Nginx installed (for SSL certificate management with Certbot)
- Certbot installed (`apt install certbot python3-certbot-nginx`)
- SSH key access configured

### Step 1: Set Up GitHub Secrets

Create the following secrets in GitHub repository settings:

```
DOCKER_USERNAME          - Docker Hub username
DOCKER_PASSWORD          - Docker Hub access token
VPS_HOST                 - Public IP or hostname of VPS
VPS_USER                 - SSH user (e.g., ubuntu, root)
VPS_SSH_KEY              - Private SSH key (with newlines as \n)
VPS_PORT                 - SSH port (usually 22)
APP_DEPLOY_PATH          - Deployment path (e.g., /var/www/madeena-company-profile)
APP_DOMAIN               - Domain name (e.g., example.com)
APP_KEY                  - Laravel APP_KEY (generate via: php artisan key:generate --show)
DB_DATABASE              - MySQL database name
DB_USERNAME              - MySQL user
DB_PASSWORD              - MySQL password
DB_ROOT_PASSWORD         - MySQL root password
REDIS_PASSWORD           - Redis password
MAIL_MAILER              - smtp or sendgrid, etc.
MAIL_HOST                - Mail server host
MAIL_PORT                - Mail server port
MAIL_USERNAME            - Mail username
MAIL_PASSWORD            - Mail password
MAIL_FROM_ADDRESS        - Sender email address
```

### Step 2: Manual VPS Setup (First Time)

```bash
# 1. SSH into VPS
ssh -i your_key.pem user@your_vps_ip

# 2. Create deployment directory
sudo mkdir -p /var/www/madeena-company-profile
cd /var/www/madeena-company-profile

# 3. Clone repository
sudo git clone https://github.com/your-org/madeena-website-company-profile.git .

# 4. Create .env file with secrets (see Step 1)
sudo nano .env

# 5. Set permissions
sudo chown -R $(whoami):$(whoami) /var/www/madeena-company-profile

# 6. Build and start containers
docker-compose -f docker-compose.prod.yml up -d

# 7. Tail logs to verify startup
docker-compose -f docker-compose.prod.yml logs -f app
```

### Step 3: SSL Certificate Setup with Certbot

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain certificate
sudo certbot certonly --standalone -d example.com

# Verify certificate
sudo ls -la /etc/letsencrypt/live/example.com/
```

### Step 4: Nginx Configuration (VPS Host)

Create `/etc/nginx/sites-available/company-profile.conf`:

```nginx
server {
    listen 80;
    server_name example.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name example.com;

    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;

    location /company-profile {
        proxy_pass http://localhost/company-profile;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_redirect off;
    }
}
```

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/company-profile.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 5: GitHub Actions Workflow

The `.github/workflows/deploy-docker.yml` workflow automatically:

1. **Build Job**:
   - Checks out code
   - Builds Docker image
   - Pushes to Docker Hub

2. **Deploy Job** (triggered after build):
   - SSHs into VPS
   - Pulls latest code
   - Pulls Docker image
   - Runs `docker-compose -f docker-compose.prod.yml up -d`
   - Executes migrations: `php artisan migrate --force`
   - Shows container logs
   - Runs Certbot for SSL renewal
   - Performs health check against `/health` endpoint

Push to `main` or `master` branch to trigger deployment:

```bash
git push origin main
```

### Application URL

Once deployed, access the application at:

```
https://example.com/company-profile
```

---

## 📝 File Structure

```
madeena-website-company-profile/
├── Dockerfile                          # Production image definition
├── docker-compose.yml                  # Local dev (MySQL + Nginx)
├── docker-compose.prod.yml             # Production (App + MySQL + Redis + Nginx)
├── docker/
│   ├── nginx.conf                      # Main Nginx config
│   ├── app.conf                        # App routing config (production)
│   ├── nginx-dev.conf                  # Dev Nginx config (local)
│   ├── nginx-prod.conf                 # Production Nginx config (/company-profile subdirectory)
│   ├── php.ini                         # PHP configuration
│   ├── www.conf                        # PHP-FPM worker config
│   ├── supervisord.conf                # Supervisor (PHP-FPM + Nginx)
│   └── entrypoint.sh                   # Container startup script
├── .github/workflows/
│   └── deploy-docker.yml               # CI/CD pipeline
└── .env.example                        # Environment template
```

---

## 🔄 Continuity: Local Development → Production

### Development → Staging → Production

1. **Local Development** (Native PHP 8.3 + Docker MySQL + Nginx):
   - Code changes, testing, debugging
   - Full Docker Compose for isolated MySQL and Nginx

2. **Git Push** (to main/master):
   - GitHub Actions triggered
   - Build and push Docker image

3. **Production Deployment**:
   - SSH into VPS
   - Pull new image
   - Run migrations
   - Health check passes
   - Application live at `/company-profile`

---

## 🐛 Troubleshooting

### Local Development

**Issue**: PHP can't connect to MySQL
```bash
# Verify MySQL is running
docker-compose ps

# Test connection
mysql -h 127.0.0.1 -u madeena -pmadeena_pass

# Check .env DB_HOST is 127.0.0.1 (not localhost)
cat .env | grep DB_HOST
```

**Issue**: Nginx returns 502 Bad Gateway
```bash
# Verify PHP-FPM is listening
netstat -tulpn | grep 9000

# Restart containers
docker-compose restart nginx
```

### Production Deployment

**Issue**: Containers fail to start
```bash
# Check logs
docker-compose -f docker-compose.prod.yml logs app

# Verify .env settings
cat .env | grep DB_

# Check disk space
df -h
```

**Issue**: SSL certificate error
```bash
# Verify certificate exists
ls /etc/letsencrypt/live/example.com/

# Renew manually
sudo certbot renew --force-renewal

# Check Certbot logs
sudo cat /var/log/letsencrypt/letsencrypt.log
```

**Issue**: Health check fails after deployment
```bash
# Test endpoint manually
curl -v https://example.com/company-profile/health

# Check application logs
docker-compose -f docker-compose.prod.yml logs --tail 50 app
```

---

## 📚 Additional Resources

- [Laravel Docker Documentation](https://laravel.com/docs)
- [Docker Compose Reference](https://docs.docker.com/compose/compose-file/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)

---

## 🔐 Security Checklist

- [ ] All sensitive data in GitHub Secrets (never in .env committed to git)
- [ ] SSH key securely stored (use GitHub's SSH key management)
- [ ] VPS firewall configured (only necessary ports open)
- [ ] SSL certificate auto-renewed via Certbot
- [ ] Database backups configured
- [ ] Logs rotated and monitored
- [ ] Registry credentials secured (Docker Hub access token)

---

## 📞 Support

For issues or questions, refer to:
- Application logs: `docker-compose -f docker-compose.prod.yml logs app`
- Nginx logs: `docker-compose -f docker-compose.prod.yml logs nginx`
- MySQL logs: `docker-compose -f docker-compose.prod.yml logs mysql`
- GitHub Actions run summary: Check workflow run in Actions tab
