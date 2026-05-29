#!/bin/bash
# =============================================================================
# SETUP SOURCE SYMLINK — One-time migration for prod + sales
# Converts existing real directories to release-based symlink structure
# matching setup-mono-client.sh conventions.
#
# Usage:
#   sudo bash setup-source-symlink.sh [--dry-run]
#
# What this does:
#   1. Creates shared/releases/backups structure inside prod/ and sales/
#   2. Moves persistent data (logs, uploads, QR codes) to shared/
#   3. Copies config files to shared/config/
#   4. Moves current code to releases/release_initial/
#   5. Creates 22 symlinks (3 config + 14 data + 3 assets + 2 logs)
#   6. Creates current → releases/release_initial
#   7. Updates Apache vhost DocumentRoots to {env}/current
#
# ⚠️  CAUSES ~60s DOWNTIME PER ENVIRONMENT. Run during off-hours.
# =============================================================================

set -euo pipefail

# =============================================================================
# CONFIGURATION
# =============================================================================

BASE_PATH="/var/www/retail"
TARGETS=("prod" "sales")
WEB_USER="www-data"
APP_USER="ubuntu"

DRY_RUN=false
[[ "${1:-}" == "--dry-run" ]] && DRY_RUN=true

# Shared data directories (matched from setup-mono-client.sh)
DATA_DIRS=(
    "data/img"
    "data/kyc"
    "data/uploads"
    "data/vendor_ack"
    "data/estimation"
    "data/export"
    "data/adm_app_apk"
    "data/bill_qrcode"
    "data/esti_qrcode"
    "data/other_qrcode"
    "data/other_inventory_qrcode"
    "data/other_product_qrcode"
    "data/uploads_root"
    "data/assets_img"
    "data/assets_dist"
    "data/assets_plugins"
)

# =============================================================================
# LOGGING
# =============================================================================

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

run() {
    if [ "$DRY_RUN" = true ]; then
        log "[DRY-RUN] $*"
    else
        "$@"
    fi
}

# =============================================================================
# PRE-FLIGHT CHECKS
# =============================================================================

if [ "$(id -u)" -ne 0 ] && [ "$DRY_RUN" = false ]; then
    log "❌ Must run as root (sudo). Use --dry-run to preview."
    exit 1
fi

for target in "${TARGETS[@]}"; do
    target_dir="${BASE_PATH}/${target}"
    if [ ! -d "$target_dir" ]; then
        log "❌ Directory not found: $target_dir"
        exit 1
    fi
    if [ -L "${target_dir}/current" ]; then
        log "❌ ${target} already has current symlink — already migrated?"
        exit 1
    fi
done

log "═══════════════════════════════════════════════════════════"
log "🏗️  Source Symlink Migration"
log "═══════════════════════════════════════════════════════════"
log "Targets: ${TARGETS[*]}"
log "Dry run: ${DRY_RUN}"
log ""

# =============================================================================
# MIGRATE EACH TARGET
# =============================================================================

for TARGET in "${TARGETS[@]}"; do
    TARGET_DIR="${BASE_PATH}/${TARGET}"
    SHARED_DIR="${TARGET_DIR}/shared"
    RELEASES_DIR="${TARGET_DIR}/releases"
    RELEASE_NAME="release_initial"
    RELEASE_DIR="${RELEASES_DIR}/${RELEASE_NAME}"

    log "═══════════════════════════════════════════════════════════"
    log "🔄 Migrating: ${TARGET} (${TARGET_DIR})"
    log "═══════════════════════════════════════════════════════════"

    # -----------------------------------------------------------------
    # STEP 1: Create directory structure
    # -----------------------------------------------------------------
    log "📁 Step 1: Creating shared/releases/backups structure..."

    run mkdir -p "${SHARED_DIR}/config"
    run mkdir -p "${SHARED_DIR}/logs/app_log"
    run mkdir -p "${SHARED_DIR}/logs/tagging_log"
    run mkdir -p "${RELEASES_DIR}"
    run mkdir -p "${TARGET_DIR}/backups"

    for dir in "${DATA_DIRS[@]}"; do
        run mkdir -p "${SHARED_DIR}/${dir}"
    done

    log "✅ Structure created"

    # -----------------------------------------------------------------
    # STEP 2: Move persistent data to shared
    # -----------------------------------------------------------------
    log "📦 Step 2: Moving persistent data to shared/..."

    # Move log directories
    if [ -d "${TARGET_DIR}/admin/log" ] && [ ! -L "${TARGET_DIR}/admin/log" ]; then
        log "   Moving admin/log → shared/logs/app_log/"
        run cp -a "${TARGET_DIR}/admin/log/." "${SHARED_DIR}/logs/app_log/" 2>/dev/null || true
    fi
    if [ -d "${TARGET_DIR}/admin/tagging_log" ] && [ ! -L "${TARGET_DIR}/admin/tagging_log" ]; then
        log "   Moving admin/tagging_log → shared/logs/tagging_log/"
        run cp -a "${TARGET_DIR}/admin/tagging_log/." "${SHARED_DIR}/logs/tagging_log/" 2>/dev/null || true
    fi

    # Move data directories (admin-level)
    declare -A DIR_MAP=(
        ["admin/img"]="data/img"
        ["admin/assets/kyc"]="data/kyc"
        ["admin/uploads"]="data/uploads"
        ["admin/vendor_ack"]="data/vendor_ack"
        ["admin/estimation"]="data/estimation"
        ["admin/export"]="data/export"
        ["admin/adm_app_apk"]="data/adm_app_apk"
        ["admin/bill_qrcode"]="data/bill_qrcode"
        ["admin/esti_qrcode"]="data/esti_qrcode"
        ["admin/other_qrcode"]="data/other_qrcode"
        ["admin/other_inventory_qrcode"]="data/other_inventory_qrcode"
        ["admin/other_product_qrcode"]="data/other_product_qrcode"
        ["admin/uploads_root"]="data/uploads_root"
    )

    for src_rel in "${!DIR_MAP[@]}"; do
        dest_rel="${DIR_MAP[$src_rel]}"
        src_full="${TARGET_DIR}/${src_rel}"
        dest_full="${SHARED_DIR}/${dest_rel}"
        if [ -d "$src_full" ] && [ ! -L "$src_full" ]; then
            log "   Moving ${src_rel} → shared/${dest_rel}/"
            run cp -a "${src_full}/." "${dest_full}/" 2>/dev/null || true
        fi
    done

    # Move assets (copy tracked files first, these are partially gitignored)
    for asset_dir in "admin/assets/img" "admin/assets/dist" "admin/assets/plugins"; do
        asset_name=$(basename "$asset_dir")
        src_full="${TARGET_DIR}/${asset_dir}"
        dest_full="${SHARED_DIR}/data/assets_${asset_name}"
        if [ -d "$src_full" ] && [ ! -L "$src_full" ]; then
            log "   Moving ${asset_dir} → shared/data/assets_${asset_name}/"
            run cp -a "${src_full}/." "${dest_full}/" 2>/dev/null || true
        fi
    done

    # Move top-level persistent dirs
    for dir in log data; do
        if [ -d "${TARGET_DIR}/${dir}" ] && [ ! -L "${TARGET_DIR}/${dir}" ]; then
            log "   Moving ${dir}/ → shared/${dir}/"
            run cp -a "${TARGET_DIR}/${dir}/." "${SHARED_DIR}/${dir}/" 2>/dev/null || true
        fi
    done

    # rate.txt
    if [ -f "${TARGET_DIR}/rate.txt" ]; then
        run cp "${TARGET_DIR}/rate.txt" "${SHARED_DIR}/data/rate.txt"
    fi

    log "✅ Persistent data moved"

    # -----------------------------------------------------------------
    # STEP 3: Copy config files to shared/config
    # -----------------------------------------------------------------
    log "📝 Step 3: Copying config files to shared/config/..."

    if [ -f "${TARGET_DIR}/global_configs.php" ]; then
        run cp "${TARGET_DIR}/global_configs.php" "${SHARED_DIR}/config/global_configs.php"
        log "   ✅ global_configs.php"
    fi
    if [ -f "${TARGET_DIR}/admin/application/config/database.php" ]; then
        run cp "${TARGET_DIR}/admin/application/config/database.php" "${SHARED_DIR}/config/database.php"
        log "   ✅ database.php"
    fi
    if [ -f "${TARGET_DIR}/admin/application/config/config.php" ]; then
        run cp "${TARGET_DIR}/admin/application/config/config.php" "${SHARED_DIR}/config/config.php"
        log "   ✅ config.php"
    fi

    # Copy webhook handler to shared
    if [ -f "${TARGET_DIR}/webhooks/webhooks/deploy.php" ]; then
        run cp "${TARGET_DIR}/webhooks/webhooks/deploy.php" "${SHARED_DIR}/webhook-deploy.php"
        log "   ✅ webhook-deploy.php"
    elif [ -f "${TARGET_DIR}/cicd/server/webhook-deploy.php" ]; then
        run cp "${TARGET_DIR}/cicd/server/webhook-deploy.php" "${SHARED_DIR}/webhook-deploy.php"
        log "   ✅ webhook-deploy.php (from cicd)"
    fi

    # Maintenance page
    if [ -f "${TARGET_DIR}/maintenance.html" ]; then
        run cp "${TARGET_DIR}/maintenance.html" "${SHARED_DIR}/maintenance.html"
        log "   ✅ maintenance.html"
    fi

    log "✅ Config files copied"

    # -----------------------------------------------------------------
    # STEP 4: Move current code to first release
    # -----------------------------------------------------------------
    log "🚚 Step 4: Moving current code to releases/${RELEASE_NAME}/..."
    log "   ⚠️  DOWNTIME STARTS NOW for ${TARGET}"

    # Move everything EXCEPT shared/, releases/, backups/ to the release
    run mkdir -p "${RELEASE_DIR}"

    # Use a temp dir approach: move current content to release
    for item in "${TARGET_DIR}"/*; do
        item_name=$(basename "$item")
        # Skip the dirs we just created
        case "$item_name" in
            shared|releases|backups) continue ;;
        esac
        run mv "$item" "${RELEASE_DIR}/"
    done

    # Move hidden files too (like .git, .gitignore, .htaccess)
    for item in "${TARGET_DIR}"/.[!.]*; do
        [ -e "$item" ] || continue
        run mv "$item" "${RELEASE_DIR}/"
    done

    log "✅ Code moved to release"

    # -----------------------------------------------------------------
    # STEP 5: Create 22 symlinks in release → shared
    # -----------------------------------------------------------------
    log "🔗 Step 5: Creating symlinks (release → shared)..."

    # Config symlinks (3)
    run ln -sfn "${SHARED_DIR}/config/database.php"      "${RELEASE_DIR}/admin/application/config/database.php"
    run ln -sfn "${SHARED_DIR}/config/config.php"         "${RELEASE_DIR}/admin/application/config/config.php"
    run ln -sfn "${SHARED_DIR}/config/global_configs.php" "${RELEASE_DIR}/global_configs.php"
    log "   ✅ 3 config symlinks"

    # Data symlinks (14) — remove existing dirs first
    for src_rel in "${!DIR_MAP[@]}"; do
        dest_rel="${DIR_MAP[$src_rel]}"
        run rm -rf "${RELEASE_DIR}/${src_rel}"
        run ln -sfn "${SHARED_DIR}/${dest_rel}" "${RELEASE_DIR}/${src_rel}"
    done
    log "   ✅ 13 data symlinks"

    # rate.txt
    run ln -sfn "${SHARED_DIR}/data/rate.txt" "${RELEASE_DIR}/rate.txt"
    log "   ✅ rate.txt symlink"

    # Assets symlinks (3)
    for asset_dir in "admin/assets/img" "admin/assets/dist" "admin/assets/plugins"; do
        asset_name=$(basename "$asset_dir")
        run rm -rf "${RELEASE_DIR}/${asset_dir}"
        run ln -sfn "${SHARED_DIR}/data/assets_${asset_name}" "${RELEASE_DIR}/${asset_dir}"
    done
    log "   ✅ 3 asset symlinks"

    # Log symlinks (3)
    run rm -rf "${RELEASE_DIR}/admin/log"
    run ln -sfn "${SHARED_DIR}/logs/app_log" "${RELEASE_DIR}/admin/log"
    run rm -rf "${RELEASE_DIR}/admin/tagging_log"
    run ln -sfn "${SHARED_DIR}/logs/tagging_log" "${RELEASE_DIR}/admin/tagging_log"
    # Top-level log/ (CI/CodeIgniter logs)
    run rm -rf "${RELEASE_DIR}/log"
    run ln -sfn "${SHARED_DIR}/log" "${RELEASE_DIR}/log"
    log "   ✅ 3 log symlinks"

    # Webhook symlink
    if [ -f "${SHARED_DIR}/webhook-deploy.php" ]; then
        run mkdir -p "${RELEASE_DIR}/webhooks"
        run rm -f "${RELEASE_DIR}/webhooks/deploy.php"
        run ln -sfn "${SHARED_DIR}/webhook-deploy.php" "${RELEASE_DIR}/webhooks/deploy.php"
        log "   ✅ webhook symlink"
    fi

    log "✅ All symlinks created"

    # -----------------------------------------------------------------
    # STEP 6: Create current → release symlink
    # -----------------------------------------------------------------
    log "⚡ Step 6: Creating current symlink..."

    run ln -sfn "${RELEASE_DIR}" "${TARGET_DIR}/current_tmp"
    run mv -Tf "${TARGET_DIR}/current_tmp" "${TARGET_DIR}/current"

    log "✅ ${TARGET}/current → releases/${RELEASE_NAME}"
    log "   ⚠️  DOWNTIME ENDS for ${TARGET}"

    # -----------------------------------------------------------------
    # STEP 7: Create deploy.env for deploy-production.sh
    # -----------------------------------------------------------------
    log "📝 Step 7: Creating deploy.env..."

    if [ "$TARGET" = "prod" ]; then
        HEALTH_URL="https://retail.logimaxindia.com"
    elif [ "$TARGET" = "sales" ]; then
        HEALTH_URL="https://demo.retail.logimaxindia.com"
    else
        HEALTH_URL=""
    fi

    if [ "$DRY_RUN" = false ]; then
        cat > "${SHARED_DIR}/deploy.env" <<DEPLOYENV
# Auto-generated by setup-source-symlink.sh
CLIENT_ID=source-${TARGET}
BASE_DIR=${TARGET_DIR}
DEPLOY_ENV=production
HEALTH_CHECK_URL=${HEALTH_URL}
DEPLOYENV
    fi

    log "✅ deploy.env created"

    log ""
done

# =============================================================================
# STEP 8: Update Apache vhost
# =============================================================================

log "═══════════════════════════════════════════════════════════"
log "🌐 Step 8: Updating Apache vhost DocumentRoots..."
log "═══════════════════════════════════════════════════════════"

VHOST_000="/etc/apache2/sites-available/000-default.conf"
VHOST_SALES="/etc/apache2/sites-available/demo.retail.logimaxindia.com.conf"

# Backup originals
if [ "$DRY_RUN" = false ]; then
    cp "$VHOST_000" "${VHOST_000}.bak.$(date +%Y%m%d)"
    if [ -f "$VHOST_SALES" ]; then
        cp "$VHOST_SALES" "${VHOST_SALES}.bak.$(date +%Y%m%d)"
    fi
fi

# Update prod: /var/www/retail/prod → /var/www/retail/prod/current
# Also add +FollowSymLinks
run sed -i \
    -e 's|DocumentRoot /var/www/retail/prod$|DocumentRoot /var/www/retail/prod/current|' \
    -e 's|<Directory /var/www/retail/prod>|<Directory /var/www/retail/prod/current>|' \
    -e 's|Options -Indexes$|Options -Indexes +FollowSymLinks|' \
    "$VHOST_000"

# Update sales: /var/www/retail/sales → /var/www/retail/sales/current
run sed -i \
    -e 's|DocumentRoot /var/www/retail/sales$|DocumentRoot /var/www/retail/sales/current|' \
    -e 's|<Directory /var/www/retail/sales>|<Directory /var/www/retail/sales/current>|' \
    -e 's|Options -Indexes$|Options -Indexes +FollowSymLinks|' \
    "$VHOST_SALES"

log "✅ Vhost configs updated"
log "   Backups: ${VHOST_000}.bak.* and ${VHOST_SALES}.bak.*"

# Test Apache config
if [ "$DRY_RUN" = false ]; then
    log "🔍 Testing Apache config..."
    if apache2ctl configtest 2>&1 | grep -q "Syntax OK"; then
        log "✅ Apache config test passed"
        systemctl reload apache2
        log "✅ Apache reloaded"
    else
        log "❌ Apache config test FAILED — rolling back!"
        cp "${VHOST_000}.bak."* "$VHOST_000"
        [ -f "${VHOST_SALES}.bak."* ] && cp "${VHOST_SALES}.bak."* "$VHOST_SALES"
        log "   Restored original vhost configs"
        apache2ctl configtest
    fi
fi

# =============================================================================
# STEP 9: Fix permissions
# =============================================================================

log "🔒 Step 9: Fixing permissions..."

for TARGET in "${TARGETS[@]}"; do
    TARGET_DIR="${BASE_PATH}/${TARGET}"
    run chown -R ${APP_USER}:${WEB_USER} "${TARGET_DIR}"
    run find "${TARGET_DIR}" -type d -exec chmod 2775 {} \;
    run find "${TARGET_DIR}" -type f -exec chmod 644 {} \;
    run find "${TARGET_DIR}" -name "*.sh" -exec chmod 755 {} \;
done

log "✅ Permissions set"

# =============================================================================
# SUMMARY
# =============================================================================

log ""
log "═══════════════════════════════════════════════════════════"
log "✅ Migration Complete"
log "═══════════════════════════════════════════════════════════"
log ""
log "Structure:"
for TARGET in "${TARGETS[@]}"; do
    log "  ${BASE_PATH}/${TARGET}/"
    log "  ├── current → releases/release_initial/"
    log "  ├── releases/"
    log "  │   └── release_initial/"
    log "  ├── shared/"
    log "  │   ├── config/   (global_configs.php, database.php, config.php)"
    log "  │   ├── data/     (14 dirs: img, uploads, QR codes, etc.)"
    log "  │   └── logs/     (app_log, tagging_log)"
    log "  └── backups/"
    log ""
done
log "Verify:"
log "  curl -I https://retail.logimaxindia.com/admin/"
log "  curl -I https://demo.retail.logimaxindia.com/admin/"
log ""
log "Next: Copy deploy-production.sh to server and update webhook."
