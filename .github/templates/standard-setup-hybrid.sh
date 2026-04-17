#!/bin/bash
# ═══════════════════════════════════════════════════════════════════════════════
# Madeena Standard Template — Hybrid Local Development Setup
# ═══════════════════════════════════════════════════════════════════════════════
# Hybrid architecture: PHP/Composer/NPM run natively on WSL.
#                      MySQL 8.4 runs in Docker (port-forwarded to 127.0.0.1).
#
# CUSTOMIZATION REQUIRED:
#   1. Replace {{APP_NAME}} with your app slug (lowercase, e.g., simama)
#   2. Replace {{DB_PORT}} with your MySQL port (e.g., 3306)
#   3. Replace {{COMPOSE_FILE}} with your local compose file name
#   4. Adjust PHP version and extensions as needed
#
# Usage:
#   ./standard-setup-hybrid.sh              # Standard setup
#   ./standard-setup-hybrid.sh --fresh      # Reset database
#   ./standard-setup-hybrid.sh --no-start   # Setup only
# ═══════════════════════════════════════════════════════════════════════════════
set -euo pipefail

# ─── CONFIGURATION ────────────────────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_NAME="{{APP_NAME}}"
COMPOSE_FILE="${SCRIPT_DIR}/{{COMPOSE_FILE}}"
ENV_TEMPLATE="${SCRIPT_DIR}/.env.local"
ENV_FILE="${SCRIPT_DIR}/.env"
REQUIRED_PHP="8.3"

DB_CONTAINER="${APP_NAME}-mysql-local"
DB_ROOT_PASS="secret"
DB_PORT={{DB_PORT}}

# ─── PARSE ARGUMENTS ─────────────────────────────────────────────────────────
FRESH=false
NO_START=false

for arg in "$@"; do
    case "$arg" in
        --fresh)    FRESH=true ;;
        --no-start) NO_START=true ;;
        -h|--help)  echo "Usage: $0 [--fresh|--no-start|--help]"; exit 0 ;;
    esac
done

# ─── COLOUR HELPERS ───────────────────────────────────────────────────────────
BOLD="\033[1m"
GREEN="\033[0;32m"
YELLOW="\033[1;33m"
RED="\033[0;31m"
CYAN="\033[0;36m"
RESET="\033[0m"

step()  { echo -e "\n${BOLD}${GREEN}══════════════════════════════════════════════${RESET}"; \
          echo -e "${BOLD}${GREEN}  $*${RESET}"; \
          echo -e "${BOLD}${GREEN}══════════════════════════════════════════════${RESET}"; }
info()  { echo -e "  ${GREEN}✔${RESET}  $*"; }
warn()  { echo -e "  ${YELLOW}⚠${RESET}  $*"; }
die()   { echo -e "\n${RED}${BOLD}✘  ERROR: ${*}${RESET}\n" >&2; exit 1; }

set_env() {
    local key="$1" value="$2" file="${3:-${ENV_FILE}}"
    if grep -q "^${key}=" "${file}" 2>/dev/null; then
        sed -i "s|^${key}=.*|${key}=${value}|" "${file}"
    else
        echo "${key}=${value}" >> "${file}"
    fi
}

# ─── STEP 1: PREFLIGHT ───────────────────────────────────────────────────────
step "1/8 · Preflight checks"

for cmd in php composer node npm docker; do
    command -v "$cmd" &>/dev/null || die "Required command not found: '$cmd'"
done

PHP_CURRENT=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
if [[ "${PHP_CURRENT}" != "${REQUIRED_PHP}" ]]; then
    warn "PHP ${PHP_CURRENT} detected. Production uses PHP ${REQUIRED_PHP}."
fi

# Check required PHP extensions
REQUIRED_EXTENSIONS=(pdo_mysql mbstring xml gd zip intl bcmath pcntl)
MISSING=()
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m 2>/dev/null | grep -qi "^${ext}$"; then
        MISSING+=("${ext}")
    fi
done
if [[ ${#MISSING[@]} -gt 0 ]]; then
    die "Missing PHP extensions: ${MISSING[*]}"
fi

if ! docker info &>/dev/null 2>&1; then
    die "Docker daemon is not running."
fi

info "All preflight checks passed."

# ─── STEP 2: DOCKER INFRASTRUCTURE ──────────────────────────────────────────
step "2/8 · Docker infrastructure (MySQL 8.4)"
cd "${SCRIPT_DIR}"
docker compose -f "${COMPOSE_FILE}" up -d
info "MySQL container started."

# ─── STEP 3: WAIT FOR MYSQL ─────────────────────────────────────────────────
step "3/8 · Waiting for MySQL readiness"
RETRIES=30
until docker exec -e MYSQL_PWD="${DB_ROOT_PASS}" "${DB_CONTAINER}" mysqladmin status -u root --silent 2>/dev/null | grep -q Uptime; do
    sleep 2
    RETRIES=$((RETRIES - 1))
    [[ ${RETRIES} -le 0 ]] && die "MySQL did not become healthy within 60s."
    echo -n "."
done
echo ""
info "MySQL is ready — 127.0.0.1:${DB_PORT}"

# ─── STEP 4: ENVIRONMENT ────────────────────────────────────────────────────
step "4/8 · Environment configuration"
if [[ ! -f "${ENV_FILE}" ]] || [[ "${FRESH}" == "true" ]]; then
    cp "${ENV_TEMPLATE}" "${ENV_FILE}"
    info "Generated .env from .env.local"
else
    info "Existing .env preserved (use --fresh to regenerate)"
fi

# ─── STEP 5: PHP DEPENDENCIES ───────────────────────────────────────────────
step "5/8 · PHP dependencies (Composer)"
composer install --no-interaction --prefer-dist --optimize-autoloader
info "Composer dependencies installed."

# ─── STEP 6: FRONTEND ASSETS ────────────────────────────────────────────────
step "6/8 · Frontend assets (NPM + Vite)"
npm ci 2>/dev/null || npm install
npm run build
info "Frontend assets compiled."

# ─── STEP 7: APPLICATION SETUP ───────────────────────────────────────────────
step "7/8 · Application setup"

# Generate app key if missing
if ! grep -q "^APP_KEY=base64:" "${ENV_FILE}" 2>/dev/null; then
    php artisan key:generate --force
    info "Application key generated."
fi

# Run migrations
if [[ "${FRESH}" == "true" ]]; then
    php artisan migrate:fresh --seed --force
    info "Database reset and seeded."
else
    php artisan migrate --force
    info "Migrations applied."
fi

php artisan storage:link --force 2>/dev/null || true

# ─── STEP 8: PERMISSIONS ────────────────────────────────────────────────────
step "8/8 · Permissions"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
info "Permissions set."

# ─── SUMMARY ─────────────────────────────────────────────────────────────────
echo ""
echo -e "${BOLD}${GREEN}══════════════════════════════════════════════════════${RESET}"
echo -e "${BOLD}${GREEN}  ✅  ${APP_NAME} local environment ready!${RESET}"
echo -e "${BOLD}${GREEN}══════════════════════════════════════════════════════${RESET}"
echo ""
echo -e "  ${BOLD}App URL:${RESET}       http://localhost:8000"
echo -e "  ${BOLD}MySQL:${RESET}         127.0.0.1:${DB_PORT} (container: ${DB_CONTAINER})"
echo ""

if [[ "${NO_START}" == "true" ]]; then
    info "Setup complete. Run 'composer dev' to start."
else
    echo -e "  ${BOLD}${CYAN}Starting development server…${RESET}"
    exec composer dev
fi
