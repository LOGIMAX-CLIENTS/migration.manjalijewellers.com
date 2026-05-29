#!/bin/bash
# =============================================================================
# DEPLOY MONO-REPO CLIENT — Per-deploy script (called by webhook handler)
# Releases-based deployment with atomic symlink swap.
#
# Usage: bash deploy-mono.sh [branch]
# Default branch: auto-detected from environment name (staging→support, prod→Production)
#
# Features:
#   - Deploy locking (prevents concurrent deploys)
#   - git clone --depth 1 into releases/{timestamp}/
#   - Maintenance page activation during deploy
#   - All symlinks (3 configs + 14 data dirs + 2 log dirs + rate.txt)
#   - Atomic symlink swap (ln -sfn + mv -Tf)
#   - Health check (curl post-swap)
#   - Auto-rollback on health failure
#   - Persistent logging to shared/logs/deploy.log
#   - DevOps tool API callback (start + end)
#   - Old release cleanup (keeps last 3)
# =============================================================================

# NOTE: No 'set -e' — we use explicit error checks so the webhook handler
# always gets a status response, even on failure.

# =============================================================================
# AUTO-DETECT CONFIGURATION
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# Source persistent deploy config FIRST (before auto-detection).
# When running from shared/, the walk-up auto-detection fails.
# deploy.env lives alongside deploy-mono.sh in shared/, so use SCRIPT_DIR.
if [ -f "${SCRIPT_DIR}/deploy.env" ]; then
    set -a
    source "${SCRIPT_DIR}/deploy.env"
    set +a
fi

# Auto-detect the deployment base directory.
# Supports both layouts:
#   Flat:   /var/www/{client}/current/cicd/server/  → BASE_DIR = /var/www/{client}
#   Hybrid: /var/www/{client}/prod/current/cicd/server/ → BASE_DIR = /var/www/{client}/prod
#
# Override with env vars: BASE_DIR, CLIENT_ID (or deploy.env)
if [ -z "${BASE_DIR:-}" ]; then
    # Walk up from script dir to find the directory containing shared/
    check_dir=$(realpath "${SCRIPT_DIR}/../../..")
    if [ -d "${check_dir}/shared" ]; then
        BASE_DIR="$check_dir"
    elif [ -d "$(dirname "$check_dir")/shared" ]; then
        # One more level up (hybrid layout)
        BASE_DIR="$(dirname "$check_dir")"
    else
        BASE_DIR="$check_dir"
    fi
fi

CLIENT_ID="${CLIENT_ID:-$(basename "$(dirname "$BASE_DIR")")}"
# If BASE_DIR is /var/www/ashoka/prod, CLIENT_ID = ashoka
# If BASE_DIR is /var/www/ashoka, CLIENT_ID = ashoka (flat layout)
# Validate: if CLIENT_ID looks like a system dir, fall back to parent
if [ "$CLIENT_ID" = "www" ] || [ "$CLIENT_ID" = "var" ]; then
    CLIENT_ID=$(basename "$BASE_DIR")
fi
SHARED_DIR="${BASE_DIR}/shared"

RELEASES_DIR="${BASE_DIR}/releases"
CURRENT_LINK="${BASE_DIR}/current"
DEPLOY_LOG="${SHARED_DIR}/logs/deploy.log"
LOCK_FILE="/tmp/deploy_mono_${CLIENT_ID}.lock"

# Maintenance page
MAINTENANCE_PAGE="${SHARED_DIR}/maintenance.html"
MAINTENANCE_FLAG="${BASE_DIR}/.maintenance_active"

# =============================================================================
# PERMISSION SELF-HEALING
# Webhooks run as www-data, but dirs may be owned by ubuntu.
# These helpers auto-fix ownership issues using sudo fallback.
# =============================================================================

ensure_dir_writable() {
    local dir="$1"
    # Create if missing
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir" 2>/dev/null || sudo mkdir -p "$dir" 2>/dev/null
    fi
    # Fix ownership if not writable
    if [ -d "$dir" ] && [ ! -w "$dir" ]; then
        sudo chown www-data:www-data "$dir" 2>/dev/null
        sudo chmod 2775 "$dir" 2>/dev/null
    fi
}

ensure_file_writable() {
    local file="$1"
    if [ -f "$file" ] && [ ! -w "$file" ]; then
        sudo chown www-data:www-data "$file" 2>/dev/null
        sudo chmod 664 "$file" 2>/dev/null
    fi
}

ensure_base_permissions() {
    # Fix critical directories that www-data needs write access to
    ensure_dir_writable "${BASE_DIR}"
    ensure_dir_writable "${SHARED_DIR}"
    ensure_dir_writable "${SHARED_DIR}/logs"
    ensure_dir_writable "${SHARED_DIR}/config"
    ensure_dir_writable "${RELEASES_DIR}"
    ensure_dir_writable "${BASE_DIR}/backups"
    # Fix log files
    if [ -f "$DEPLOY_LOG" ]; then
        ensure_file_writable "$DEPLOY_LOG"
    fi

    # Fix shared data directory ownership (prevents "Permission denied" /
    # "unable to unlink" errors when files are owned by ubuntu but deploy
    # runs as www-data, or vice versa)
    if [ -d "${SHARED_DIR}/data" ]; then
        sudo chown -R www-data:www-data "${SHARED_DIR}/data" 2>/dev/null || true
    fi
}

# SSH Key — prefer client-specific key, fall back to generic
resolve_ssh_key() {
    local user_home="$1"
    local client="$2"
    if [ -f "${user_home}/.ssh/id_ed25519_${client}" ]; then
        echo "${user_home}/.ssh/id_ed25519_${client}"
    elif [ -f "${user_home}/.ssh/id_ed25519" ]; then
        echo "${user_home}/.ssh/id_ed25519"
    else
        echo ""
    fi
}

if [ "$(whoami)" == "www-data" ]; then
    SSH_KEY=$(resolve_ssh_key "/var/www" "$CLIENT_ID")
else
    SSH_KEY="${SSH_KEY:-$(resolve_ssh_key "/home/ubuntu" "$CLIENT_ID")}"
fi

if [ -z "$SSH_KEY" ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ❌ No SSH key found for client '$CLIENT_ID'"
    exit 1
fi

export GIT_SSH_COMMAND="ssh -i $SSH_KEY -o IdentitiesOnly=yes -o StrictHostKeyChecking=no"

# Repo URL: prefer env var (from webhook handler), then auto-detect from current clone
if [ -z "${REPO_URL:-}" ]; then
    REPO_URL=$(cd "${CURRENT_LINK}" 2>/dev/null && git remote get-url origin 2>/dev/null || echo "")
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] REPO_URL auto-detected from current clone: ${REPO_URL:-'(empty)'}"
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] REPO_URL from env var: ${REPO_URL}"
fi
echo "[$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] SSH_KEY: ${SSH_KEY} (exists: $([ -f "$SSH_KEY" ] && echo 'YES' || echo 'NO'))"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] CURRENT_LINK: ${CURRENT_LINK}"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] BASE_DIR: ${BASE_DIR}"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] CLIENT_ID: ${CLIENT_ID}"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] [DEBUG] DEPLOY_ENV: ${DEPLOY_ENV:-'(not set)'}"

# =============================================================================
# ENVIRONMENT + BRANCH DETECTION
# =============================================================================

# Detect environment from directory or argument
detect_environment() {
    # Check Apache env var first
    if [ -n "${DEPLOY_ENV:-}" ]; then
        echo "$DEPLOY_ENV"
        return
    fi
    # Fallback: detect from symlink name or caller
    echo "staging"
}

# Branch mapping
get_default_branch() {
    local env="$1"
    case "$env" in
        staging)    echo "support" ;;
        production) echo "Production" ;;
        *)          echo "support" ;;
    esac
}

DEPLOY_ENV=$(detect_environment)
BRANCH="${1:-$(get_default_branch $DEPLOY_ENV)}"

# =============================================================================
# DEVOPS TOOL API
# =============================================================================

DEVOPS_API_URL="${DEVOPS_API_URL:-}"
DEVOPS_WEBHOOK_SECRET="${DEVOPS_WEBHOOK_SECRET:-}"
DEPLOYMENT_ID=""

devops_callback() {
    local action="$1"
    local status="${2:-}"
    local extra="${3:-}"

    if [ -z "$DEVOPS_API_URL" ]; then
        log "   [DEBUG] devops_callback($action) SKIPPED — DEVOPS_API_URL is empty"
        return 0
    fi

    local payload="{\"client_id\":\"${CLIENT_ID}\",\"environment\":\"${DEPLOY_ENV}\",\"action\":\"${action}\""

    if [ "$action" = "start" ]; then
        # Include status=running so mono-deploy endpoint can find/update the approval record
        payload="${payload},\"status\":\"running\",\"branch\":\"${BRANCH}\",\"triggered_by\":\"$(whoami)\",\"deploy_type\":\"source\"}"
    else
        # Phase 2.5c: Include deploy log tail for end actions
        local log_tail=""
        if [ -f "$DEPLOY_LOG" ]; then
            log_tail=$(tail -20 "$DEPLOY_LOG" 2>/dev/null | sed 's/"/\\"/g' | tr '\n' '|')
        fi
        payload="${payload},\"deployment_id\":\"${DEPLOYMENT_ID}\",\"status\":\"${status}\",\"commit_sha\":\"${AFTER_COMMIT:-}\",\"commit_message\":\"${COMMIT_MSG:-}\",\"duration_seconds\":\"${DURATION:-0}\",\"log_tail\":\"${log_tail}\"${extra}}"
    fi

    local response
    response=$(curl -s -X POST "${DEVOPS_API_URL}/api/webhooks/mono-deploy" \
        -H "Content-Type: application/json" \
        -H "X-Webhook-Secret: ${DEVOPS_WEBHOOK_SECRET}" \
        -d "$payload" 2>/dev/null || echo "{}")

    if [ "$action" = "start" ]; then
        DEPLOYMENT_ID=$(echo "$response" | jq -r '.deploymentId // empty' 2>/dev/null || echo "")
    fi
}

# =============================================================================
# LOGGING
# =============================================================================

log() {
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] $1" | tee -a "$DEPLOY_LOG"
}

# =============================================================================
# CLEANUP & MAINTENANCE
# =============================================================================

cleanup() {
    # flock auto-releases when fd 200 closes on script exit
    deactivate_maintenance
    # Apply deferred self-updates (staged during deploy to avoid overwriting running script)
    if [ -d "${SHARED_DIR}/.pending_updates" ]; then
        for f in "${SHARED_DIR}/.pending_updates/"*; do
            [ -f "$f" ] && mv -f "$f" "${SHARED_DIR}/$(basename "$f")" 2>/dev/null
        done
        rmdir "${SHARED_DIR}/.pending_updates" 2>/dev/null
    fi
}

activate_maintenance() {
    if [ -f "$MAINTENANCE_PAGE" ]; then
        touch "$MAINTENANCE_FLAG"
        log "🔧 Maintenance page activated"
    fi
}

deactivate_maintenance() {
    rm -f "$MAINTENANCE_FLAG"
    log "✅ Maintenance page deactivated"
}

# =============================================================================
# MAIN DEPLOYMENT
# =============================================================================

trap cleanup EXIT

# Fix base directory permissions before anything else
ensure_base_permissions

START_TIME=$(date +%s)

# ── Phase 2.5b: Concurrent Deploy Lock (flock-based) ──
# Using flock instead of file-based lock to prevent race conditions
# between two near-simultaneous deploys. Lock auto-releases on script exit.
LOCK_FILE="/tmp/deploy-${CLIENT_ID}.lock"
exec 200>"$LOCK_FILE"

if ! flock -n 200; then
    log "⚠️ Another deploy for ${CLIENT_ID} is already running. Waiting..."
    flock 200  # blocks until lock released
fi

# ── Validate prerequisites ──
if [ -z "$REPO_URL" ]; then
    log "❌ Cannot determine repo URL from current deployment"
    exit 1
fi

if [ ! -d "$SHARED_DIR" ]; then
    log "❌ Shared directory not found: ${SHARED_DIR}"
    log "   Run setup-mono-client.sh first"
    exit 1
fi

# ── Previous release (for rollback) ──
PREV_RELEASE=$(readlink -f "$CURRENT_LINK" 2>/dev/null || echo "")

log "═══════════════════════════════════════════════════════════"
log "🚀 Deployment Started: ${CLIENT_ID} (${DEPLOY_ENV})"
log "═══════════════════════════════════════════════════════════"
log "📋 Client: ${CLIENT_ID}"
log "📋 Environment: ${DEPLOY_ENV}"
log "📋 Branch: ${BRANCH}"
log "📋 Repo: ${REPO_URL}"
log "👤 User: $(whoami)"
log "⏰ Time: $(date '+%Y-%m-%d %H:%M:%S')"

# ── DevOps callback: start ──
devops_callback "start"

# ── Activate maintenance page ──
activate_maintenance

# ── Clone new release ──
TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
RELEASE_DIR="${RELEASES_DIR}/${TIMESTAMP}"

log "📥 Cloning branch: ${BRANCH}..."
clone_output=$(git clone --depth 1 --branch "${BRANCH}" "${REPO_URL}" "${RELEASE_DIR}" 2>&1)
clone_status=$?

if [ $clone_status -ne 0 ]; then
    log "❌ Git clone failed (exit code: ${clone_status})"
    log "❌ Error: ${clone_output}"
    END_TIME=$(date +%s)
    DURATION=$((END_TIME - START_TIME))
    devops_callback "end" "failed"
    exit 1
fi

AFTER_COMMIT=$(cd "${RELEASE_DIR}" && git rev-parse --short HEAD)
COMMIT_MSG=$(cd "${RELEASE_DIR}" && git log -1 --pretty=%B | head -n1)
COMMIT_AUTHOR=$(cd "${RELEASE_DIR}" && git log -1 --pretty=format:'%an')

log "✅ Clone complete: ${AFTER_COMMIT} — ${COMMIT_MSG}"

# ── Self-update: stage deploy infrastructure for deferred copy ──
# IMPORTANT: Do NOT overwrite deploy-mono.sh while it's running!
# Bash reads scripts incrementally from disk — overwriting mid-execution
# corrupts the byte offset and causes syntax errors.
# Stage to .pending_updates/, cleanup() trap moves them into place on exit.
mkdir -p "${SHARED_DIR}/.pending_updates"
if [ -f "${RELEASE_DIR}/cicd/server/deploy-mono.sh" ]; then
    cp "${RELEASE_DIR}/cicd/server/deploy-mono.sh" "${SHARED_DIR}/.pending_updates/deploy-mono.sh" 2>/dev/null
    log "   📋 Staged shared/deploy-mono.sh (applied on exit)"
fi
if [ -f "${RELEASE_DIR}/cicd/server/webhook-deploy.php" ]; then
    cp "${RELEASE_DIR}/cicd/server/webhook-deploy.php" "${SHARED_DIR}/.pending_updates/webhook-deploy.php" 2>/dev/null
    log "   📋 Staged shared/webhook-deploy.php (applied on exit)"
fi
if [ -f "${RELEASE_DIR}/scripts/run-migrations.sh" ]; then
    cp "${RELEASE_DIR}/scripts/run-migrations.sh" "${SHARED_DIR}/.pending_updates/run-migrations.sh" 2>/dev/null
    log "   📋 Staged shared/run-migrations.sh (applied on exit)"
fi
if [ -f "${RELEASE_DIR}/maintenance.html" ]; then
    cp "${RELEASE_DIR}/maintenance.html" "${SHARED_DIR}/.pending_updates/maintenance.html" 2>/dev/null
    log "   📋 Staged shared/maintenance.html (applied on exit)"
fi

# ── Phase 2.5: PHP Syntax Check (before symlink swap) ──
log "🔍 PHP syntax check..."

SYNTAX_ERRORS=""
SYNTAX_COUNT=0

while IFS= read -r php_file; do
    result=$(php -d short_open_tag=Off -l "$php_file" 2>&1)
    if [ $? -ne 0 ]; then
        SYNTAX_ERRORS="${SYNTAX_ERRORS}\n  ❌ ${php_file}: ${result}"
        SYNTAX_COUNT=$((SYNTAX_COUNT + 1))
    fi
done < <(find "${RELEASE_DIR}" -name "*.php" \
    -not -path "*/vendor/*" -not -path "*/node_modules/*" \
    -not -name "*_[0-9][0-9]_[0-9][0-9]_[0-9][0-9].php" \
    -not -path "*/admin/application/controllers/reconstruct.php")

# Also check client overrides if present
if [ -d "${RELEASE_DIR}/clients/${CLIENT_ID}" ]; then
    while IFS= read -r php_file; do
        result=$(php -l "$php_file" 2>&1)
        if [ $? -ne 0 ]; then
            SYNTAX_ERRORS="${SYNTAX_ERRORS}\n  ❌ ${php_file}: ${result}"
            SYNTAX_COUNT=$((SYNTAX_COUNT + 1))
        fi
    done < <(find "${RELEASE_DIR}/clients/${CLIENT_ID}" -name "*.php" 2>/dev/null)
fi

if [ "$SYNTAX_COUNT" -gt 0 ]; then
    log "❌ PHP syntax check FAILED — ${SYNTAX_COUNT} file(s) with errors:"
    log "$SYNTAX_ERRORS"
    rm -rf "${RELEASE_DIR}"
    END_TIME=$(date +%s)
    DURATION=$((END_TIME - START_TIME))
    devops_callback "end" "failed" ",\"error\":\"PHP syntax: ${SYNTAX_COUNT} files\""
    exit 1
fi

log "✅ PHP syntax check passed"

# ── Create symlinks ──
log "🔗 Creating symlinks..."

# Config symlinks (validate shared config files exist before symlinking)
REQUIRED_CONFIGS=(
    "config/database.php:admin/application/config/database.php"
    "config/config.php:admin/application/config/config.php"
    "config/global_configs.php:global_configs.php"
)

CONFIG_MISSING=0
for config_pair in "${REQUIRED_CONFIGS[@]}"; do
    shared_file="${SHARED_DIR}/${config_pair%%:*}"
    release_target="${RELEASE_DIR}/${config_pair##*:}"

    if [ -f "$shared_file" ]; then
        ln -sf "$shared_file" "$release_target"
        log "   ✓ ${config_pair%%:*} (symlinked)"
    else
        # Auto-bootstrap: copy from release if available in shared
        release_source="${RELEASE_DIR}/${config_pair##*:}"
        if [ -f "$release_source" ] && [ ! -L "$release_source" ]; then
            cp "$release_source" "$shared_file"
            ln -sf "$shared_file" "$release_target"
            log "   ⚠️ ${config_pair%%:*} was MISSING — bootstrapped from release (review & customize!)"
        else
            log "   ❌ ${config_pair%%:*} MISSING in shared/ and no source in release!"
            CONFIG_MISSING=$((CONFIG_MISSING + 1))
        fi
    fi
done

if [ "$CONFIG_MISSING" -gt 0 ]; then
    log "❌ ${CONFIG_MISSING} required config file(s) missing. Deploy cannot continue safely."
    log "   Run setup-mono-client.sh or manually create the missing files in ${SHARED_DIR}/config/"
    rm -rf "${RELEASE_DIR}"
    END_TIME=$(date +%s)
    DURATION=$((END_TIME - START_TIME))
    devops_callback "end" "failed" ",\"error\":\"Missing ${CONFIG_MISSING} config file(s)\""
    exit 1
fi

# Data directory symlinks
ln -sf "${SHARED_DIR}/data/img"                        "${RELEASE_DIR}/admin/img"
ln -sf "${SHARED_DIR}/data/kyc"                        "${RELEASE_DIR}/admin/assets/kyc"
ln -sf "${SHARED_DIR}/data/uploads"                    "${RELEASE_DIR}/admin/uploads"
ln -sf "${SHARED_DIR}/data/vendor_ack"                 "${RELEASE_DIR}/admin/vendor_ack"
ln -sf "${SHARED_DIR}/data/estimation"                 "${RELEASE_DIR}/admin/estimation"
ln -sf "${SHARED_DIR}/data/export"                     "${RELEASE_DIR}/admin/export"
ln -sf "${SHARED_DIR}/data/adm_app_apk"                "${RELEASE_DIR}/admin/adm_app_apk"
ln -sf "${SHARED_DIR}/data/bill_qrcode"                "${RELEASE_DIR}/admin/bill_qrcode"
ln -sf "${SHARED_DIR}/data/esti_qrcode"                "${RELEASE_DIR}/admin/esti_qrcode"
ln -sf "${SHARED_DIR}/data/other_qrcode"               "${RELEASE_DIR}/admin/other_qrcode"
ln -sf "${SHARED_DIR}/data/other_inventory_qrcode"     "${RELEASE_DIR}/admin/other_inventory_qrcode"
ln -sf "${SHARED_DIR}/data/other_product_qrcode"       "${RELEASE_DIR}/admin/other_product_qrcode"
ln -sf "${SHARED_DIR}/data/uploads_root"               "${RELEASE_DIR}/admin/uploads_root"
ln -sf "${SHARED_DIR}/data/rate.txt"                   "${RELEASE_DIR}/rate.txt"

# Log symlinks
ln -sf "${SHARED_DIR}/logs/app_log"                    "${RELEASE_DIR}/admin/log"
ln -sf "${SHARED_DIR}/logs/tagging_log"                "${RELEASE_DIR}/admin/tagging_log"

# Assets symlinks (partially gitignored — some files missing from clones)
# Copy tracked files first, then replace with symlink to shared
if [ -d "${SHARED_DIR}/data/assets_dist" ]; then
    cp -rp "${RELEASE_DIR}/admin/assets/dist/"* "${SHARED_DIR}/data/assets_dist/" 2>/dev/null
    rm -rf "${RELEASE_DIR}/admin/assets/dist"
    ln -sf "${SHARED_DIR}/data/assets_dist"            "${RELEASE_DIR}/admin/assets/dist"
fi
if [ -d "${SHARED_DIR}/data/assets_plugins" ]; then
    cp -rp "${RELEASE_DIR}/admin/assets/plugins/"* "${SHARED_DIR}/data/assets_plugins/" 2>/dev/null
    rm -rf "${RELEASE_DIR}/admin/assets/plugins"
    ln -sf "${SHARED_DIR}/data/assets_plugins"         "${RELEASE_DIR}/admin/assets/plugins"
fi
if [ -d "${SHARED_DIR}/data/assets_img" ]; then
    rm -rf "${RELEASE_DIR}/admin/assets/img"
    ln -sf "${SHARED_DIR}/data/assets_img"             "${RELEASE_DIR}/admin/assets/img"
fi

log "✅ Symlinks created (3 config + 14 data + 3 assets + 2 log)"

# Webhook handler symlink (shared → release)
if [ -f "${SHARED_DIR}/webhook-deploy.php" ]; then
    mkdir -p "${RELEASE_DIR}/webhooks"
    rm -f "${RELEASE_DIR}/webhooks/deploy.php"
    ln -sf "${SHARED_DIR}/webhook-deploy.php" "${RELEASE_DIR}/webhooks/deploy.php"
    log "🔗 Webhook handler symlinked"
fi

# ── Set permissions on release (comprehensive) ──
log "🔒 Setting permissions on release..."

# Ownership: prefer ubuntu:www-data for SFTP+Apache, but www-data can't chown to ubuntu
# So use www-data:www-data when running as www-data (webhook deploys)
if [ "$(whoami)" = "www-data" ]; then
    chown -R www-data:www-data "${RELEASE_DIR}" 2>/dev/null || true
else
    chown -R ubuntu:www-data "${RELEASE_DIR}"
fi

# Default directory permissions → 755
find "${RELEASE_DIR}" -type d -exec chmod 755 {} \;

# Default file permissions → 644
find "${RELEASE_DIR}" -type f -exec chmod 644 {} \;

# Preserve executable scripts → 755
find "${RELEASE_DIR}" -type f \( -name "*.sh" -o -name "*.bash" \) -exec chmod 755 {} \;

# Writable shared directories (setgid + group writable)
WRITABLE_DIRS=(
    "data/img" "data/kyc" "data/uploads" "data/vendor_ack"
    "data/estimation" "data/export" "data/bill_qrcode" "data/esti_qrcode"
    "data/other_qrcode" "data/other_inventory_qrcode" "data/other_product_qrcode"
    "data/uploads_root" "logs/app_log" "logs/tagging_log"
)
for DIR in "${WRITABLE_DIRS[@]}"; do
    FULL="${SHARED_DIR}/${DIR}"
    if [ -d "$FULL" ]; then
        find "$FULL" -type d -exec chmod 2775 {} \;
        find "$FULL" -type f -exec chmod 644 {} \;
        find "$FULL" -type d -exec chmod g+s {} \;
    fi
done

log "✅ Permissions set"

# ── Phase 2.2: Run Database Migrations (before symlink swap) ──
MIGRATION_SCRIPT="${RELEASE_DIR}/scripts/run-migrations.sh"
MIGRATION_DIR="${RELEASE_DIR}/database/migrations"
MIGRATION_STATUS="skipped"
MIGRATION_APPLIED=0
MIGRATION_FAILED=0
MIGRATION_JSON=""

if [ -x "$MIGRATION_SCRIPT" ] || [ -f "$MIGRATION_SCRIPT" ]; then
    if [ -d "$MIGRATION_DIR" ] && [ "$(find "$MIGRATION_DIR" -maxdepth 1 -name '*.sql' -type f 2>/dev/null | wc -l)" -gt 0 ]; then
        log "🗄  Running database migrations..."

        # Determine config file based on environment
        if [ "$DEPLOY_ENV" = "staging" ]; then
            MIGRATE_CONFIG="${SHARED_DIR}/config/global_configs_staging.php"
            if [ ! -f "$MIGRATE_CONFIG" ]; then
                MIGRATE_CONFIG="${SHARED_DIR}/config/global_configs.php"
            fi
        else
            MIGRATE_CONFIG="${SHARED_DIR}/config/global_configs.php"
        fi

        if [ ! -f "$MIGRATE_CONFIG" ]; then
            log "⚠️ Config file not found for migrations: ${MIGRATE_CONFIG}"
            log "   Skipping migrations — deploy continues"
            MIGRATION_STATUS="config_missing"
        else
            # Capture stderr separately to prevent PHP notices / MySQL warnings
            # from contaminating the JSON output that jq needs to parse
            MIGRATE_STDERR_FILE=$(mktemp /tmp/migrate_stderr_XXXXXX.log)
            # Ensure backups directory exists and is writable
            ensure_dir_writable "${BASE_DIR}/backups"
            if [ ! -d "${BASE_DIR}/backups" ] || [ ! -w "${BASE_DIR}/backups" ]; then
                log "⚠️  Backups directory not writable: ${BASE_DIR}/backups — skipping backup"
                EXTRA_MIGRATE_FLAGS="--no-backup"
            else
                EXTRA_MIGRATE_FLAGS=""
            fi

            # Debug: log migration invocation details
            log "   [DEBUG] MIGRATION_SCRIPT: ${MIGRATION_SCRIPT} (exists: $([ -f "$MIGRATION_SCRIPT" ] && echo YES || echo NO))"
            log "   [DEBUG] MIGRATE_CONFIG: ${MIGRATE_CONFIG} (exists: $([ -f "$MIGRATE_CONFIG" ] && echo YES || echo NO))"
            log "   [DEBUG] MIGRATION_DIR: ${MIGRATION_DIR} (exists: $([ -d "$MIGRATION_DIR" ] && echo YES || echo NO))"
            log "   [DEBUG] BACKUP_DIR: ${BACKUP_DIR} (exists: $([ -d "$BACKUP_DIR" ] && echo YES || echo NO), writable: $([ -w "$BACKUP_DIR" ] && echo YES || echo NO))"
            log "   [DEBUG] EXTRA_FLAGS: '${EXTRA_MIGRATE_FLAGS}'"
            log "   [DEBUG] Running as: $(whoami)"

            MIGRATION_JSON=$(bash "$MIGRATION_SCRIPT" \
                --config "$MIGRATE_CONFIG" \
                --migrations "$MIGRATION_DIR" \
                --env "$DEPLOY_ENV" \
                $EXTRA_MIGRATE_FLAGS \
                --json 2>"$MIGRATE_STDERR_FILE")
            MIGRATE_EXIT=$?

            log "   [DEBUG] MIGRATE_EXIT: ${MIGRATE_EXIT}"
            log "   [DEBUG] MIGRATION_JSON length: ${#MIGRATION_JSON}"

            if [ $MIGRATE_EXIT -eq 0 ]; then
                # Migrations applied successfully
                MIGRATION_STATUS="success"
                MIGRATION_APPLIED=$(echo "$MIGRATION_JSON" | jq -r '.applied // 0' 2>/dev/null || echo "0")
                log "✅ Migrations complete: ${MIGRATION_APPLIED} applied"
            elif [ $MIGRATE_EXIT -eq 2 ]; then
                # No pending migrations
                MIGRATION_STATUS="up_to_date"
                log "✅ Database schema is up to date"
            else
                # Migration failed — abort deploy, do NOT swap symlink
                MIGRATION_STATUS="failed"
                MIGRATION_FAILED=$(echo "$MIGRATION_JSON" | jq -r '.failed // 1' 2>/dev/null || echo "1")
                FAILED_NAME=$(echo "$MIGRATION_JSON" | jq -r '.failed_migration // "unknown"' 2>/dev/null || echo "unknown")
                MIGRATION_APPLIED=$(echo "$MIGRATION_JSON" | jq -r '.applied // 0' 2>/dev/null || echo "0")

                log "❌ Migration FAILED: ${FAILED_NAME}"
                log "   Applied ${MIGRATION_APPLIED} migration(s) before failure"
                # Log stderr for debugging (PHP notices, MySQL warnings, etc.)
                if [ -f "$MIGRATE_STDERR_FILE" ] && [ -s "$MIGRATE_STDERR_FILE" ]; then
                    log "   stderr: $(cat "$MIGRATE_STDERR_FILE")"
                fi
                log "   JSON output: ${MIGRATION_JSON}"
                log "   ⚠️  Aborting deploy — old code stays live"
                log "   ⚠️  Fix the migration and redeploy, or apply manually"

                rm -rf "${RELEASE_DIR}"
                END_TIME=$(date +%s)
                DURATION=$((END_TIME - START_TIME))
                # Extract migration details for tracking even on failure
                local FAIL_DETAILS=$(echo "$MIGRATION_JSON" | jq -c '.migrations // []' 2>/dev/null || echo "[]")
                devops_callback "end" "failed" ",\"error\":\"Migration failed: ${FAILED_NAME}\",\"migrations\":{\"applied\":${MIGRATION_APPLIED},\"failed\":${MIGRATION_FAILED},\"status\":\"failed\",\"details\":${FAIL_DETAILS}}"
                rm -f "$MIGRATE_STDERR_FILE"
                exit 1
            fi
            rm -f "$MIGRATE_STDERR_FILE"
        fi
    else
        log "ℹ️  No migration files found — skipping"
        MIGRATION_STATUS="no_files"
    fi
else
    log "ℹ️  Migration runner not found — skipping"
    MIGRATION_STATUS="no_runner"
fi

# ── Atomic symlink swap ──
log "🔄 Atomic symlink swap..."
# Atomic swap: ln + mv. Use sudo fallback if www-data can't write to BASE_DIR.
if ln -sfn "${RELEASE_DIR}" "${BASE_DIR}/current_tmp" 2>/dev/null; then
    mv -Tf "${BASE_DIR}/current_tmp" "${BASE_DIR}/current" 2>/dev/null || \
        sudo mv -Tf "${BASE_DIR}/current_tmp" "${BASE_DIR}/current" 2>/dev/null
else
    # Fallback: sudo for both operations
    sudo ln -sfn "${RELEASE_DIR}" "${BASE_DIR}/current_tmp" 2>/dev/null && \
        sudo mv -Tf "${BASE_DIR}/current_tmp" "${BASE_DIR}/current" 2>/dev/null || \
        sudo ln -sfn "${RELEASE_DIR}" "${BASE_DIR}/current" 2>/dev/null
fi
log "✅ current → ${RELEASE_DIR}"

# ── Deactivate maintenance (before health check — Apache returns 503 while active) ──
deactivate_maintenance

# ── Health check ──
HEALTH_URL="${HEALTH_CHECK_URL:-http://localhost}"
log "🏥 Health check: ${HEALTH_URL}..."

sleep 2  # Give Apache a moment to pick up the new symlink

health_status=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "${HEALTH_URL}" 2>/dev/null || echo "000")

if [ "$health_status" -ge 200 ] && [ "$health_status" -lt 400 ]; then
    log "✅ Health check passed (HTTP ${health_status})"
else
    log "❌ Health check FAILED (HTTP ${health_status})"

    # ── Auto-rollback ──
    if [ -n "$PREV_RELEASE" ] && [ -d "$PREV_RELEASE" ]; then
        log "🔄 Rolling back to previous release: ${PREV_RELEASE}"
        ln -sf "${PREV_RELEASE}" "${BASE_DIR}/current_tmp"
        mv -Tf "${BASE_DIR}/current_tmp" "${BASE_DIR}/current"
        log "✅ Rollback complete"

        END_TIME=$(date +%s)
        DURATION=$((END_TIME - START_TIME))
        devops_callback "end" "rolled_back"
        exit 1
    else
        log "⚠️ No previous release to roll back to"
        END_TIME=$(date +%s)
        DURATION=$((END_TIME - START_TIME))
        devops_callback "end" "failed"
        exit 1
    fi
fi

# ── Cleanup old releases (keep last 3) ──
log "🧹 Cleaning old releases..."
cd "${RELEASES_DIR}"
RELEASE_COUNT=$(ls -1d */ 2>/dev/null | wc -l)
if [ "$RELEASE_COUNT" -gt 3 ]; then
    REMOVE_COUNT=$((RELEASE_COUNT - 3))
    ls -1d */ | head -n "$REMOVE_COUNT" | while read -r old_release; do
        log "   🗑️  Removing: ${old_release}"
        rm -rf "${RELEASES_DIR}/${old_release}"
    done
    log "✅ Cleaned ${REMOVE_COUNT} old release(s)"
else
    log "✅ ${RELEASE_COUNT} release(s) — no cleanup needed"
fi

# ── Final summary ──
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

log ""
log "═══════════════════════════════════════════════════════════"
log "✅ Deployment Complete: ${CLIENT_ID}"
log "═══════════════════════════════════════════════════════════"
log "📊 Commit: ${AFTER_COMMIT}"
log "💬 Message: ${COMMIT_MSG}"
log "👤 Author: ${COMMIT_AUTHOR}"
log "⏱️  Duration: ${DURATION}s"
log ""

# ── DevOps callback: end (with migration results) ──
MIGRATION_DETAILS=""
if [ -n "$MIGRATION_JSON" ] && echo "$MIGRATION_JSON" | jq -e '.migrations' > /dev/null 2>&1; then
    MIGRATION_DETAILS=$(echo "$MIGRATION_JSON" | jq -c '.migrations' 2>/dev/null || echo "[]")
fi
devops_callback "end" "success" ",\"migrations\":{\"applied\":${MIGRATION_APPLIED},\"failed\":${MIGRATION_FAILED},\"status\":\"${MIGRATION_STATUS}\",\"details\":${MIGRATION_DETAILS:-[]}}"

# ── Structured log entry ──
echo "[$(date '+%Y-%m-%d %H:%M:%S')] | ${CLIENT_ID} | ${DEPLOY_ENV} | ${BRANCH} | ${AFTER_COMMIT} | ${COMMIT_AUTHOR} | ${DURATION}s | ${COMMIT_MSG}" >> "${SHARED_DIR}/logs/deploy-history.log"

echo "✅ Deployment complete for ${CLIENT_ID} (${DEPLOY_ENV}) in ${DURATION}s"
