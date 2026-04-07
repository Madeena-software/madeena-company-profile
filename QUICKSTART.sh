#!/bin/bash

# Quick Start Guide for Madeena Company Profile Docker Setup
# This file contains helpful commands for common tasks

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Madeena Company Profile - Docker Setup${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Function to print section headers
section() {
    echo -e "${YELLOW}→ $1${NC}"
}

# ============================================================================
# LOCAL DEVELOPMENT SETUP
# ============================================================================

section "LOCAL DEVELOPMENT SETUP (WSL)"
echo ""
echo "Step 1: Clone and setup"
echo "  mkdir -p /var/www && cd /var/www"
echo "  git clone https://github.com/your-org/madeena-website-company-profile.git"
echo "  cd madeena-website-company-profile"
echo ""

echo "Step 2: Copy environment and generate key"
echo "  cp .env.example .env"
echo "  php artisan key:generate"
echo ""

echo "Step 3: Install dependencies (native PHP)"
echo "  composer install"
echo "  npm install && npm run build"
echo ""

echo "Step 4: Start Docker services (MySQL + Nginx)"
echo "  docker-compose up -d"
echo ""

echo "Step 5: Setup database"
echo "  php artisan migrate"
echo "  php artisan db:seed          # Optional"
echo "  php artisan storage:link"
echo ""

echo "Step 6: Run development server"
echo "  php artisan serve"
echo "  # Access: http://localhost:8000"
echo ""

# ============================================================================
# USEFUL COMMANDS
# ============================================================================

echo -e "${YELLOW}USEFUL COMMANDS${NC}"
echo ""

section "Local Development Commands"
echo "  docker-compose up -d               # Start MySQL and Nginx"
echo "  docker-compose down                # Stop all services"
echo "  docker-compose ps                  # Show container status"
echo "  docker-compose logs -f mysql       # Stream MySQL logs"
echo "  docker-compose logs -f nginx       # Stream Nginx logs"
echo ""

section "Database Commands"
echo "  php artisan migrate                # Run migrations"
echo "  php artisan migrate:rollback       # Rollback migrations"
echo "  php artisan db:seed                # Seed database"
echo "  php artisan tinker                 # Interactive shell"
echo ""

section "Cache & Optimization"
echo "  php artisan config:cache           # Cache config"
echo "  php artisan route:cache            # Cache routes"
echo "  php artisan view:cache             # Cache views"
echo "  php artisan cache:clear            # Clear all caches"
echo ""

section "Asset Building"
echo "  npm run dev                        # Development build (watch)"
echo "  npm run build                      # Production build"
echo ""

section "Testing"
echo "  php artisan test"
echo "  vendor/bin/phpunit"
echo ""

# ============================================================================
# PRODUCTION DEPLOYMENT
# ============================================================================

echo -e "${YELLOW}PRODUCTION DEPLOYMENT${NC}"
echo ""

section "One-time VPS Setup"
echo "  ssh -i your_key.pem user@your_vps_ip"
echo "  cd /var/www/madeena-company-profile"
echo "  docker-compose -f docker-compose.prod.yml up -d"
echo "  docker-compose -f docker-compose.prod.yml logs -f app"
echo ""

section "Automatic Deployment"
echo "  1. Configure GitHub Secrets (see GITHUB-SECRETS.md)"
echo "  2. Commit and push to main/master branch"
echo "  3. Monitor: GitHub → Actions tab"
echo "  4. Verify: https://your-domain.com/company-profile"
echo ""

section "Manual Deployment to VPS"
echo "  git push origin main"
echo "  # Wait for GitHub Actions workflow to complete"
echo "  # Workflow handles: build, push, deploy, migrate, health-check"
echo ""

# ============================================================================
# TROUBLESHOOTING
# ============================================================================

echo -e "${YELLOW}TROUBLESHOOTING${NC}"
echo ""

section "PHP can't connect to MySQL"
echo "  # Verify MySQL is running:"
echo "  docker-compose ps"
echo "  # Test connection:"
echo "  mysql -h 127.0.0.1 -u madeena -pmadeena_pass"
echo ""

section "Nginx returns 502 error"
echo "  # Check PHP-FPM is running:"
echo "  netstat -tulpn | grep 9000"
echo "  # Restart containers:"
echo "  docker-compose restart nginx"
echo ""

section "Container startup errors"
echo "  # Check logs:"
echo "  docker-compose logs app"
echo "  docker-compose logs mysql"
echo "  docker-compose logs nginx"
echo ""

section "Database migration errors"
echo "  # Check .env DB settings:"
echo "  grep DB_ .env"
echo "  # Run migration with verbose output:"
echo "  php artisan migrate --verbose"
echo ""

# ============================================================================
# DOCUMENTATION
# ============================================================================

echo -e "${YELLOW}DOCUMENTATION${NC}"
echo ""
echo "  📖 DOCKER-SETUP.md           - Complete Docker architecture guide"
echo "  🔐 GITHUB-SECRETS.md         - GitHub Secrets configuration"
echo "  📋 README-DEPLOYMENT.md      - Manual deployment steps"
echo "  🚀 .github/workflows/deploy-docker.yml - CI/CD workflow"
echo ""

# ============================================================================
# QUICK START CHECKLIST
# ============================================================================

echo -e "${GREEN}✓ QUICK START CHECKLIST${NC}"
echo ""
echo "For LOCAL development:"
echo "  ☐ Copy .env.example → .env"
echo "  ☐ Run: php artisan key:generate"
echo "  ☐ Run: composer install && npm install"
echo "  ☐ Run: docker-compose up -d"
echo "  ☐ Run: php artisan migrate"
echo "  ☐ Run: php artisan serve"
echo ""

echo "For PRODUCTION deployment:"
echo "  ☐ Read GITHUB-SECRETS.md"
echo "  ☐ Add all secrets to GitHub repository"
echo "  ☐ Push code to main/master branch"
echo "  ☐ Monitor workflow in GitHub Actions"
echo "  ☐ Test application: https://your-domain.com/company-profile"
echo ""

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Setup complete! Happy coding! 🚀${NC}"
echo -e "${GREEN}========================================${NC}"
