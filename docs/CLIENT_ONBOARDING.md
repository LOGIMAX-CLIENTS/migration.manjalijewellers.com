# Client Onboarding Guide

Quick guide to add CI/CD for new clients. Supports both **cPanel** and **AWS** environments.

## Prerequisites

- [ ] Client repo exists in `LOGIMAX-CLIENTS`
- [ ] SSH/terminal access to client server
- [ ] GitHub admin access to add secrets

---

## Step 1: Server Setup

### Option A: cPanel Servers

```bash
# Typical paths
WEB_ROOT=/home/{username}/public_html
WEBHOOKS_DIR=/home/{username}/public_html/webhooks
RELEASES_DIR=/home/{username}/releases
SHARED_DIR=/home/{username}/shared

# Create directories
mkdir -p $WEBHOOKS_DIR $RELEASES_DIR $SHARED_DIR/logs $SHARED_DIR/uploads
```

### Option B: AWS/EC2 Servers

```bash
# Typical paths
WEB_ROOT=/var/www/html
WEBHOOKS_DIR=/var/www/html/webhooks
RELEASES_DIR=/var/www/releases
SHARED_DIR=/var/www/shared

# Create directories
sudo mkdir -p $WEBHOOKS_DIR $RELEASES_DIR $SHARED_DIR/logs $SHARED_DIR/uploads
sudo chown -R www-data:www-data /var/www
```

---

## Step 2: Copy Files to Server

```bash
# Variables (adjust for your server)
SERVER=user@server
WEB_ROOT=/home/username/public_html  # or /var/www/html for AWS

# Copy files
scp scripts/unified-deploy.sh $SERVER:$WEB_ROOT/deploy.sh
scp scripts/webhook-handler.php $SERVER:$WEB_ROOT/webhooks/deploy.php
scp maintenance.html $SERVER:$WEB_ROOT/maintenance.html

# Make executable
ssh $SERVER "chmod +x $WEB_ROOT/deploy.sh"
```

---

## Step 3: Configure Environment

### cPanel (.htaccess method)

Add to `public_html/webhooks/.htaccess`:
```apache
SetEnv WEBHOOK_SECRET "your-secret-here"
SetEnv REPO_PATH "/home/username/public_html"
SetEnv DEPLOY_SCRIPT "/home/username/public_html/deploy.sh"
SetEnv LOG_FILE "/home/username/shared/logs/deploy.log"
```

### AWS (Environment file method)

Create `/var/www/.deploy-env`:
```bash
export WEBHOOK_SECRET="your-secret-here"
export REPO_PATH="/var/www/html"
export DEPLOY_SCRIPT="/var/www/html/deploy.sh"
export LOG_FILE="/var/www/shared/logs/deploy.log"
export RELEASES_DIR="/var/www/releases"
export SHARED_DIR="/var/www/shared"
```

---

## Step 4: Add GitHub Secrets

In client repo → Settings → Secrets → Actions:

| Secret | Value |
|--------|-------|
| `STAGING_WEBHOOK_URL` | `https://client.domain/webhooks/deploy.php` |
| `STAGING_WEBHOOK_SECRET` | (your secret) |
| `PRODUCTION_WEBHOOK_URL` | `https://client.domain/webhooks/deploy.php` |
| `PRODUCTION_WEBHOOK_SECRET` | (your secret) |

---

## Step 5: Add Workflow to Client Repo

Copy `templates/client/deploy.yml` → `.github/workflows/deploy.yml`

---

## Step 6: Configure GitHub Environment

For production approval:
1. Repo → Settings → Environments → New: `production`
2. Add required reviewers
3. Limit to `production` branch

---

## Step 7: Add Maintenance Check

Add to top of `index.php`:
```php
<?php
if (file_exists('.maintenance')) {
    header('HTTP/1.1 503 Service Unavailable');
    include 'maintenance.html';
    exit;
}
```

---

## Step 8: Test

```bash
# Staging (auto-deploy)
git checkout staging && git push

# Production (requires approval)
git checkout production && git merge staging && git push
```

---

## Rollback (Production)

If something goes wrong, instant rollback:

```bash
# SSH to server
ssh user@server

# List available releases
./rollback.sh list

# Rollback to previous release (instant)
./rollback.sh previous

# Or rollback to specific release
./rollback.sh 20260108_190000
```

**Rollback is instant** - it just swaps the symlink, no file copying.

---

## Directory Structure (Production)

```
/var/www/                    # or /home/user/
├── current → releases/latest/  # Symlink (what nginx/apache serves)
├── releases/
│   ├── 20260108_180000/     # Previous release (kept for rollback)
│   ├── 20260108_190000/     # Current release
│   └── ...
├── shared/
│   ├── logs/
│   ├── uploads/
│   ├── img/
│   ├── database.php
│   └── global_configs.php
└── .last_release            # For rollback tracking
```

---

## Path Reference

| Environment | Web Root | Releases | Shared |
|-------------|----------|----------|--------|
| **cPanel** | `/home/{user}/public_html` | `/home/{user}/releases` | `/home/{user}/shared` |
| **AWS** | `/var/www/html` | `/var/www/releases` | `/var/www/shared` |

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| 403 Invalid signature | Check WEBHOOK_SECRET matches |
| 500 Server error | Check PHP error logs |
| Permission denied | Fix ownership: `chown -R www-data:www-data /var/www` |
| Workflow not triggering | Verify branch names in workflow |
