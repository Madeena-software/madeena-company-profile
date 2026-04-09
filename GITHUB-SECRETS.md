# GitHub Secrets Configuration

This file documents all required GitHub Secrets for the Docker deployment pipeline.

## How to Add Secrets

1. Go to repository **Settings** → **Secrets and variables** → **Actions**
2. Click **New repository secret**
3. Enter the secret name and value
4. Save

---

## Required Secrets

### Docker Registry (Docker Hub)

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `DOCKER_USERNAME` | Docker Hub username | `myusername` |
| `DOCKER_PASSWORD` | Docker Hub access token (NOT password) | `dckr_pat_xxxxx...` |

**To generate Docker access token**:
1. Go to https://hub.docker.com/
2. Login with your account
3. Click profile icon → **Account Settings** → **Security**
4. Click **New Access Token**
5. Copy token value

---

### VPS Connection Details

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `VPS_HOST` | VPS public IP or hostname | `203.0.113.45` or `api.example.com` |
| `VPS_USER` | SSH username | `ubuntu` or `root` |
| `VPS_SSH_KEY` | Private SSH key (with `\n` for newlines) | See below |
| `VPS_PORT` | SSH port | `22` |
| `APP_DEPLOY_PATH` | Deployment directory on VPS | `/var/www/madeena-company-profile` |

**To add SSH key as secret**:
1. Copy your private SSH key content
2. Replace actual newlines with `\n` literal string (use sed or similar)
3. Example:
   ```bash
   cat ~/.ssh/id_rsa | sed 's/$/\\n/' | tr -d '\n' | sed 's/\\n$/\n/'
   ```
4. Paste in GitHub Secrets

---

### Application Configuration

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `APP_DOMAIN` | Domain name for the application | `example.com` |
| `APP_KEY` | Laravel encryption key (generate: `php artisan key:generate --show`) | `base64:xxxxx...` |

---

### Database Configuration

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `DB_DATABASE` | MySQL database name | `madeena_company_profile` |
| `DB_USERNAME` | MySQL user (non-root) | `madeena` |
| `DB_PASSWORD` | MySQL user password | `secure_password_123` |
| `DB_ROOT_PASSWORD` | MySQL root password | `root_password_456` |

---

### Redis Configuration

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `REDIS_PASSWORD` | Redis password | `redis_secure_pass` |

---

### Email Configuration

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `MAIL_MAILER` | Mail driver | `smtp` or `sendgrid` |
| `MAIL_HOST` | SMTP host | `smtp.mailtrap.io` |
| `MAIL_PORT` | SMTP port | `587` or `465` |
| `MAIL_USERNAME` | SMTP username | `user@mailtrap.io` |
| `MAIL_PASSWORD` | SMTP password or API key | `xxx...` |
| `MAIL_FROM_ADDRESS` | Sender email address | `noreply@example.com` |

---

## Checklist for First-Time Setup

```bash
# 1. Generate APP_KEY locally
php artisan key:generate --show
# Copy output and add as APP_KEY secret

# 2. Generate or obtain SSH key
ssh-keygen -t rsa -b 4096 -f ~/.ssh/vps_deploy_key -N ""
# Add public key to VPS: ~/.ssh/authorized_keys

# 3. Create Docker Hub account and generate access token
# https://hub.docker.com/

# 4. Add all secrets to GitHub
# Use Settings → Secrets and variables → Actions

# 5. Test deployment
git push origin main
# Monitor: Actions tab in GitHub
```

---

## Environment Variables Used in Workflow

The `.github/workflows/deploy-docker.yml` uses these secrets to create `.env` on VPS:

```env
APP_NAME="Madeena Company Profile"
APP_ENV=production
APP_DEBUG=false
APP_KEY=${{ secrets.APP_KEY }}
APP_URL=http://${{ secrets.SSH_HOST }}:8011

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=${{ secrets.DB_DATABASE }}
DB_USERNAME=${{ secrets.DB_USERNAME }}
DB_PASSWORD=${{ secrets.DB_PASSWORD }}
DB_ROOT_PASSWORD=${{ secrets.DB_ROOT_PASSWORD }}

REDIS_PASSWORD=${{ secrets.REDIS_PASSWORD }}

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MAIL_MAILER=${{ secrets.MAIL_MAILER }}
MAIL_HOST=${{ secrets.MAIL_HOST }}
MAIL_PORT=${{ secrets.MAIL_PORT }}
MAIL_USERNAME=${{ secrets.MAIL_USERNAME }}
MAIL_PASSWORD=${{ secrets.MAIL_PASSWORD }}
MAIL_FROM_ADDRESS=${{ secrets.MAIL_FROM_ADDRESS }}
```

---

## Security Best Practices

✅ **DO:**
- Rotate secrets regularly
- Use strong, unique passwords for DB and Redis
- Store SSH keys securely (never commit to git)
- Use Docker access tokens instead of account passwords
- Regularly audit secret usage in Actions logs

❌ **DON'T:**
- Commit `.env` files with real secrets to git
- Use weak or simple passwords
- Share secret values in chat or email
- Store secrets in code comments
- Hardcode credentials in workflows

---

## Testing Secrets

To verify secrets are properly configured without revealing their values:

```bash
# In GitHub Actions workflow, you can test by echoing asterisks
- name: Test secrets
  run: |
    echo "Docker username: ${{ secrets.DOCKER_USERNAME }}"
    echo "VPS host: ${{ secrets.VPS_HOST }}"
    echo "App domain: ${{ secrets.APP_DOMAIN }}"
    # Secrets will display as **** in logs
```
