#!/bin/bash
# =============================================================================
# PORT_MAX DEPLOY SCRIPT
# Installs Composer & Node.js dependencies, builds SvelteKit frontend.
# Called after git pull on the server.
#
# Usage:
#   sudo bash port_max/deploy.sh          (from repo root)
#   sudo bash deploy.sh                   (from port_max/ directory)
# =============================================================================

# Auto-detect paths
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# If run from port_max/ directory
if [ -f "$SCRIPT_DIR/api/composer.json" ]; then
    PORTMAX_DIR="$SCRIPT_DIR"
# If run from repo root
elif [ -f "$SCRIPT_DIR/port_max/api/composer.json" ]; then
    PORTMAX_DIR="$SCRIPT_DIR/port_max"
else
    echo "❌ Cannot find port_max directory. Run from repo root or port_max/ folder."
    exit 1
fi

DEPLOY_LOG="$PORTMAX_DIR/deploy.log"

# =============================================================================
# HELPERS
# =============================================================================

log() {
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] $1" >> "$DEPLOY_LOG"
    echo "$1"
}

log "═══════════════════════════════════════════════════════════"
log "🔧 Port_Max Deployment Started"
log "═══════════════════════════════════════════════════════════"
log "📋 Directory: $PORTMAX_DIR"
log "👤 User: $(whoami)"
log "⏰ Time: $(date '+%Y-%m-%d %H:%M:%S')"

# Ensure required directories exist
mkdir -p "$PORTMAX_DIR/api/tmp" 2>/dev/null || true
mkdir -p "$PORTMAX_DIR/api/uploads" 2>/dev/null || true

# =============================================================================
# 1. AUTO-INSTALL COMPOSER (if missing)
# =============================================================================

if ! command -v composer &>/dev/null; then
    log "📥 Composer not found — auto-installing..."
    PHP_BIN=$(which php 2>/dev/null || which php8.1 2>/dev/null || which php8.2 2>/dev/null || which php7.4 2>/dev/null || echo "")
    if [ -n "$PHP_BIN" ]; then
        curl -sS https://getcomposer.org/installer | $PHP_BIN -- --install-dir=/usr/local/bin --filename=composer 2>&1
        if command -v composer &>/dev/null; then
            log "✅ Composer installed: $(composer -V 2>/dev/null | head -1)"
        else
            log "❌ Composer install failed"
        fi
    else
        log "❌ PHP not found — cannot install Composer"
    fi
fi

# =============================================================================
# 2. AUTO-INSTALL NODE.JS (if missing)
# =============================================================================

if ! command -v node &>/dev/null || ! command -v npm &>/dev/null; then
    log "📥 Node.js/npm not found — auto-installing..."
    if [ -f /etc/debian_version ]; then
        curl -fsSL https://deb.nodesource.com/setup_20.x 2>/dev/null | bash - 2>&1
        apt-get install -y nodejs 2>&1
    elif [ -f /etc/redhat-release ]; then
        curl -fsSL https://rpm.nodesource.com/setup_20.x 2>/dev/null | bash - 2>&1
        yum install -y nodejs 2>&1
    fi

    if command -v node &>/dev/null; then
        log "✅ Node.js installed: $(node -v)"
    else
        log "❌ Node.js install failed"
    fi
fi

# =============================================================================
# 3. COMPOSER INSTALL (Port_Max API)
# =============================================================================

if [ -f "$PORTMAX_DIR/api/composer.json" ]; then
    log "📦 API: Installing Composer dependencies..."
    if command -v composer &>/dev/null; then
        # Allow composer to run as root/sudo and set HOME for www-data
        export COMPOSER_ALLOW_SUPERUSER=1
        export HOME=${HOME:-/tmp}
        export COMPOSER_HOME=${COMPOSER_HOME:-/tmp/composer}

        # Fix git safe directory issue
        REPO_ROOT=$(dirname "$PORTMAX_DIR")
        git config --global --add safe.directory "$REPO_ROOT" 2>/dev/null || true

        # Auto-install php-zip if missing
        if ! php -m 2>/dev/null | grep -qi "^zip$"; then
            log "📥 PHP zip extension missing — installing..."
            PHP_VER=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;" 2>/dev/null || echo "7.4")

            # Unhold PHP packages (may be pinned to prevent auto-upgrades)
            apt-mark unhold php${PHP_VER} php${PHP_VER}-common php${PHP_VER}-cli php${PHP_VER}-fpm libapache2-mod-php${PHP_VER} 2>/dev/null || true

            export DEBIAN_FRONTEND=noninteractive
            timeout 120 apt-get update -qq 2>&1
            timeout 120 apt-get install -y -qq --fix-broken 2>&1
            timeout 120 apt-get install -y -qq php${PHP_VER}-common php${PHP_VER}-cli libapache2-mod-php${PHP_VER} php${PHP_VER}-zip 2>&1

            # Re-hold PHP packages to prevent future auto-upgrades
            apt-mark hold php${PHP_VER} php${PHP_VER}-common php${PHP_VER}-cli php${PHP_VER}-fpm libapache2-mod-php${PHP_VER} php${PHP_VER}-zip 2>/dev/null || true

            if php -m 2>/dev/null | grep -qi "^zip$"; then
                log "✅ php-zip installed"
                systemctl restart apache2 2>/dev/null && log "✅ Apache restarted" || true
            else
                log "⚠️  php-zip install failed"
            fi
        fi

        # Run composer install
        composer_output=$(cd "$PORTMAX_DIR/api" && composer install --no-dev --no-interaction --optimize-autoloader 2>&1)
        composer_status=$?

        # If platform reqs fail, retry with --ignore-platform-reqs
        if [ $composer_status -ne 0 ] && echo "$composer_output" | grep -q "ext-zip\|platform"; then
            log "⚠️  Platform check failed — retrying with --ignore-platform-reqs..."
            composer_output=$(cd "$PORTMAX_DIR/api" && composer install --no-dev --no-interaction --optimize-autoloader --ignore-platform-reqs 2>&1)
            composer_status=$?
        fi

        if [ $composer_status -eq 0 ]; then
            log "✅ API: Composer install completed"
        else
            log "❌ API: Composer install failed (exit code: $composer_status)"
            log "   Error: $composer_output"
        fi
    else
        log "❌ API: composer not available"
    fi
else
    log "⚠️  API: composer.json not found — skipping"
fi

# =============================================================================
# 4. NPM INSTALL & BUILD (Port_Max Migration Frontend)
# =============================================================================

if [ -f "$PORTMAX_DIR/migration/package.json" ]; then
    log "📦 Migration: Installing Node.js dependencies..."
    if command -v node &>/dev/null && command -v npm &>/dev/null; then
        log "📦 Node.js: $(node -v) | npm: $(npm -v)"

        # Install dependencies
        export HOME=${HOME:-/tmp}

        # Fix ownership if node_modules was created by root (from sudo runs)
        if [ -d "$PORTMAX_DIR/migration/node_modules" ]; then
            CURRENT_USER=$(whoami)
            chown -R "$CURRENT_USER" "$PORTMAX_DIR/migration/node_modules" 2>/dev/null || true
        fi

        npm_output=$(cd "$PORTMAX_DIR/migration" && npm install 2>&1)
        npm_status=$?
        if [ $npm_status -ne 0 ]; then
            log "❌ Migration: npm install failed (exit code: $npm_status)"
            log "   Error: $npm_output"
        else
            log "✅ Migration: npm dependencies installed"

            # Fix node_modules binary permissions (needed when running as www-data)
            chmod -R +x "$PORTMAX_DIR/migration/node_modules/.bin/" 2>/dev/null || true
            # Also fix native binaries (esbuild, rollup, etc.) deep in node_modules
            find "$PORTMAX_DIR/migration/node_modules" -type f \( -name "esbuild" -o -name "rollup" -o -name "*.node" \) -exec chmod +x {} \; 2>/dev/null || true

            # Build SvelteKit frontend (use node directly to bypass permission issues)
            log "🔨 Migration: Building SvelteKit frontend..."
            build_output=$(cd "$PORTMAX_DIR/migration" && node node_modules/vite/bin/vite.js build 2>&1)
            build_status=$?
            if [ $build_status -eq 0 ]; then
                log "✅ Migration: Frontend build completed"
                # Copy dist/ contents to port_max root for direct Apache serving
                if [ -d "$PORTMAX_DIR/migration/dist" ]; then
                    cp -rf "$PORTMAX_DIR/migration/dist/"* "$PORTMAX_DIR/" 2>/dev/null
                    cp -rf "$PORTMAX_DIR/migration/dist/".* "$PORTMAX_DIR/" 2>/dev/null
                    DIST_SIZE=$(du -sh "$PORTMAX_DIR/migration/dist" 2>/dev/null | cut -f1)
                    log "📁 Migration: dist/ size: $DIST_SIZE"
                    log "✅ Migration: Frontend files copied to port_max/"
                fi
            else
                log "❌ Migration: Frontend build failed (exit code: $build_status)"
                log "   Error: $build_output"
            fi
        fi
    else
        log "❌ Migration: node/npm not available"
    fi
else
    log "⚠️  Migration: package.json not found — skipping"
fi

# =============================================================================
# 5. FIX PERMISSIONS (when run as root/sudo, set ownership to www-data)
# =============================================================================

if [ "$(whoami)" = "root" ]; then
    log "🔒 Fixing permissions (setting www-data ownership)..."
    chown -R www-data:www-data "$PORTMAX_DIR" 2>/dev/null || true
    find "$PORTMAX_DIR" -type d -exec chmod 2775 {} \; 2>/dev/null || true
    find "$PORTMAX_DIR" -type f -exec chmod 664 {} \; 2>/dev/null || true
    find "$PORTMAX_DIR" -name "*.sh" -exec chmod 775 {} \; 2>/dev/null || true
    chmod -R +x "$PORTMAX_DIR/migration/node_modules/.bin/" 2>/dev/null || true
    log "✅ Permissions fixed (www-data:www-data)"
fi

# =============================================================================
# SUMMARY
# =============================================================================

log "═══════════════════════════════════════════════════════════"
log "✅ Port_Max Deployment Complete"
log "═══════════════════════════════════════════════════════════"
