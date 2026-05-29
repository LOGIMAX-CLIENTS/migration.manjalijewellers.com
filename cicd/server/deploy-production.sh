#!/bin/bash
# =============================================================================
# DEPLOY PRODUCTION — Dual-target symlink deployment
# Deploys PRODUCTION branch to both prod AND demo environments
# Called by webhooks/deploy.php on PRODUCTION branch push
#
# Aligned with deploy-mono.sh patterns:
#   - Direct clone to release dir (no /tmp/ intermediate)
#   - Permission self-healing (www-data/ubuntu coexistence)
#   - PHP syntax check before swap
#   - Health check + auto-rollback after swap
#   - DevOps API callback (start + end)
#   - flock-based concurrent deploy lock
#   - Config bootstrap from release if missing in shared
#   - Assets merge (tracked files → shared before symlink)
#
# Usage:
#   bash deploy-production.sh                    # Normal deployment
#   bash deploy-production.sh --rollback         # Rollback both envs
#   bash deploy-production.sh --rollback prod    # Rollback prod only
#
# Environment variables (set by deploy.php or deploy.env):
#   BASE_PATH          - Server base path (default: /var/www/retail)
#   DEPLOY_BRANCH      - Branch to deploy (default: PRODUCTION)
#   GIT_REMOTE_URL     - Git SSH URL (auto-detected from deploy.env or current clone)
#   RELEASES_KEEP      - Number of releases to keep (default: 5)
#   DEVOPS_API_URL     - DevOps dashboard URL for callbacks
#   DEVOPS_WEBHOOK_SECRET - Secret for DevOps API auth
# =============================================================================

# NOTE: No 'set -e' — we use explicit error checks so the webhook handler
# always gets a status response, even on failure.

# =============================================================================
# CONFIGURATION
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# Source persistent deploy config (like deploy-mono.sh)
if [ -f "${SCRIPT_DIR}/deploy.env" ]; then
    set -a
    source "${SCRIPT_DIR}/deploy.env"
    set +a
fi

BASE_PATH="${BASE_PATH:-/var/www/retail}"
DEPLOY_BRANCH="${DEPLOY_BRANCH:-PRODUCTION}"
GIT_REMOTE_URL="${GIT_REMOTE_URL:-}"
RELEASES_KEEP="${RELEASES_KEEP:-5}"
WEB_USER="${WEB_USER:-www-data}"
APP_USER="${APP_USER:-ubuntu}"
ENABLE_OPCACHE_RESET="${ENABLE_OPCACHE_RESET:-true}"

# DevOps Tool callback credentials (passed by source-webhook.php)
DEVOPS_API_URL="${DEVOPS_API_URL:-}"
DEVOPS_WEBHOOK_SECRET="${DEVOPS_WEBHOOK_SECRET:-}"

# Targets — both prod and sales
TARGETS=("prod" "sales")

# Timestamp for this release
TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
DEPLOY_LOG="${BASE_PATH}/prod/deploy-production.log"

# DevOps Tool integration (for status callbacks)
DEVOPS_API_URL="${DEVOPS_API_URL:-}"
DEVOPS_WEBHOOK_SECRET="${DEVOPS_WEBHOOK_SECRET:-}"
DEPLOYMENT_ID=""

# Shared data directories (must match setup-source-symlink.sh / setup-mono-client.sh)
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

# =============================================================================
# PERMISSION SELF-HEALING (aligned with deploy-mono.sh)
# Webhooks run as www-data, but dirs may be owned by ubuntu.
# These helpers auto-fix ownership issues using sudo fallback.
# =============================================================================

ensure_dir_writable() {
    local dir="$1"
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir" 2>/dev/null || sudo mkdir -p "$dir" 2>/dev/null
    fi
    if [ -d "$dir" ] && [ ! -w "$dir" ]; then
        sudo chown "${WEB_USER}:${WEB_USER}" "$dir" 2>/dev/null
        sudo chmod 2775 "$dir" 2>/dev/null
    fi
}

ensure_file_writable() {
    local file="$1"
    if [ -f "$file" ] && [ ! -w "$file" ]; then
        sudo chown "${WEB_USER}:${WEB_USER}" "$file" 2>/dev/null
        sudo chmod 664 "$file" 2>/dev/null
    fi
}

ensure_target_permissions() {
    local target_dir="$1"
    local shared_dir="${target_dir}/shared"
    local releases_dir="${target_dir}/releases"

    ensure_dir_writable "${target_dir}"
    ensure_dir_writable "${shared_dir}"
    ensure_dir_writable "${shared_dir}/logs"
    ensure_dir_writable "${shared_dir}/config"
    ensure_dir_writable "${releases_dir}"
    ensure_dir_writable "${target_dir}/backups"

    # Fix shared data directory ownership
    if [ -d "${shared_dir}/data" ]; then
        sudo chown -R "${WEB_USER}:${WEB_USER}" "${shared_dir}/data" 2>/dev/null || true
    fi
}

# =============================================================================
# SSH KEY DETECTION
# =============================================================================

resolve_ssh_key() {
    local user_home="$1"
    for key_path in \
        "${user_home}/.ssh/id_ed25519" \
        "${user_home}/.ssh/deploy_key" \
        "${user_home}/.ssh/id_rsa_deploy" \
        "${user_home}/.ssh/id_rsa"; do
        if [ -f "$key_path" ]; then
            echo "$key_path"
            return
        fi
    done
    echo ""
}

if [ "$(whoami)" == "www-data" ]; then
    SSH_KEY=$(resolve_ssh_key "/var/www")
else
    SSH_KEY="${SSH_KEY:-$(resolve_ssh_key "/home/ubuntu")}"
fi

if [ -n "$SSH_KEY" ]; then
    export GIT_SSH_COMMAND="ssh -i $SSH_KEY -o IdentitiesOnly=yes -o StrictHostKeyChecking=no"
fi

# Auto-detect repo URL if not set
if [ -z "$GIT_REMOTE_URL" ]; then
    GIT_REMOTE_URL=$(cd "${BASE_PATH}/prod/current" 2>/dev/null && git remote get-url origin 2>/dev/null || echo "")
    if [ -z "$GIT_REMOTE_URL" ]; then
        GIT_REMOTE_URL="git@github.com:Logimax-Technologies/etail_development_src.git"
    fi
fi

# =============================================================================
# LOGGING
# =============================================================================

log() {
    local msg="[$(date '+%Y-%m-%d %H:%M:%S')] $1"
    echo "$msg"
    echo "$msg" >> "$DEPLOY_LOG" 2>/dev/null || true
}

log_error() { log "❌ $1"; }
log_success() { log "✅ $1"; }

# =============================================================================
# DEVOPS TOOL CALLBACK
# Reports deployment status to DevOps Tool via /api/webhooks/mono-deploy
# Matches the pattern in deploy-mono.sh for consistency
# =============================================================================

devops_callback() {
    local action="$1"
    local status="${2:-}"
    local extra="${3:-}"

    if [ -z "$DEVOPS_API_URL" ]; then
        log "   [DEBUG] devops_callback($action) SKIPPED — DEVOPS_API_URL is empty"
        return 0
    fi

    local payload="{\"client_id\":\"source\",\"environment\":\"production\",\"action\":\"${action}\""

    if [ "$action" = "start" ]; then
        payload="${payload},\"branch\":\"${DEPLOY_BRANCH}\",\"triggered_by\":\"$(whoami)\"}"
    else
        # Include deploy log tail for end actions
        local log_tail=""
        if [ -f "$DEPLOY_LOG" ]; then
            log_tail=$(tail -20 "$DEPLOY_LOG" 2>/dev/null | sed 's/"/\\"/g' | tr '\n' '|')
        fi
        payload="${payload},\"deployment_id\":\"${DEPLOYMENT_ID}\",\"status\":\"${status}\",\"commit_sha\":\"${commit_sha:-}\",\"commit_message\":\"${commit_msg:-}\",\"duration_seconds\":\"${duration:-0}\",\"log_tail\":\"${log_tail}\"${extra}}"
    fi

    log "   [DEBUG] devops_callback($action): Sending to ${DEVOPS_API_URL}/api/webhooks/mono-deploy"
    local response
    response=$(curl -sk -X POST "${DEVOPS_API_URL}/api/webhooks/mono-deploy" \
        -H "Content-Type: application/json" \
        -H "X-Webhook-Secret: ${DEVOPS_WEBHOOK_SECRET}" \
        -d "$payload" --max-time 10 2>&1)
    local curl_exit=$?

    if [ $curl_exit -ne 0 ]; then
        log "   [DEBUG] devops_callback($action) FAILED — curl exit $curl_exit: $response"
    else
        log "   [DEBUG] devops_callback($action) response: $response"
    fi

    if [ "$action" = "start" ]; then
        DEPLOYMENT_ID=$(echo "$response" | jq -r '.deploymentId // empty' 2>/dev/null || echo "")
        [ -n "$DEPLOYMENT_ID" ] && log "   [DEBUG] DevOps deployment ID: $DEPLOYMENT_ID"
    fi
}


# =============================================================================
# MAINTENANCE PAGE
# =============================================================================

activate_maintenance() {
    for TARGET in "${TARGETS[@]}"; do
        local target_dir="${BASE_PATH}/${TARGET}"
        local maint_flag="${target_dir}/.maintenance_active"
        touch "$maint_flag" 2>/dev/null || sudo touch "$maint_flag" 2>/dev/null
        log "🔧 Maintenance page activated for ${TARGET}"
    done
}

deactivate_maintenance() {
    for TARGET in "${TARGETS[@]}"; do
        local maint_flag="${BASE_PATH}/${TARGET}/.maintenance_active"
        rm -f "$maint_flag" 2>/dev/null || sudo rm -f "$maint_flag" 2>/dev/null
    done
    log "✅ Maintenance pages deactivated"
}

cleanup() {
    deactivate_maintenance
    # Apply deferred self-updates
    local pending_dir="${BASE_PATH}/prod/shared/.pending_updates"
    if [ -d "$pending_dir" ]; then
        for f in "${pending_dir}/"*; do
            [ -f "$f" ] && mv -f "$f" "${BASE_PATH}/prod/shared/$(basename "$f")" 2>/dev/null
        done
        rmdir "$pending_dir" 2>/dev/null
    fi
}

# Report failure to DevOps Tool on unexpected exit
on_error() {
    local exit_code=$?
    local duration=$(( $(date +%s) - ${DEPLOY_START_TIME:-$(date +%s)} ))
    log_error "Deployment failed with exit code $exit_code"
    devops_callback "end" "failed" ""
}

trap cleanup EXIT
trap on_error ERR

# =============================================================================
# SYMLINK CREATION (per release) — aligned with deploy-mono.sh
# =============================================================================

create_symlinks() {
    local release_dir="$1"
    local shared_dir="$2"
    local link_count=0

    # Config symlinks with bootstrap (3)
    local REQUIRED_CONFIGS=(
        "config/database.php:admin/application/config/database.php"
        "config/config.php:admin/application/config/config.php"
        "config/global_configs.php:global_configs.php"
    )

    for config_pair in "${REQUIRED_CONFIGS[@]}"; do
        local shared_file="${shared_dir}/${config_pair%%:*}"
        local release_target="${release_dir}/${config_pair##*:}"

        if [ -f "$shared_file" ]; then
            ln -sfn "$shared_file" "$release_target"
            link_count=$((link_count + 1))
        else
            # Auto-bootstrap: copy from release if available
            local release_source="${release_dir}/${config_pair##*:}"
            if [ -f "$release_source" ] && [ ! -L "$release_source" ]; then
                cp "$release_source" "$shared_file"
                ln -sfn "$shared_file" "$release_target"
                log "   ⚠️ ${config_pair%%:*} bootstrapped from release (review & customize!)"
                link_count=$((link_count + 1))
            else
                log "   ❌ ${config_pair%%:*} MISSING in shared/ and no source in release!"
            fi
        fi
    done

    # Data directory symlinks (13 + rate.txt)
    for src_rel in "${!DIR_MAP[@]}"; do
        dest_rel="${DIR_MAP[$src_rel]}"
        rm -rf "${release_dir}/${src_rel}"
        ln -sfn "${shared_dir}/${dest_rel}" "${release_dir}/${src_rel}"
        link_count=$((link_count + 1))
    done
    ln -sfn "${shared_dir}/data/rate.txt" "${release_dir}/rate.txt" 2>/dev/null && link_count=$((link_count + 1))

    # Assets symlinks with merge (copy tracked files → shared, then symlink)
    for asset_name in dist plugins img; do
        local shared_asset="${shared_dir}/data/assets_${asset_name}"
        local release_asset="${release_dir}/admin/assets/${asset_name}"
        if [ -d "$shared_asset" ]; then
            # Merge tracked files from release into shared (preserve existing)
            if [ -d "$release_asset" ] && [ ! -L "$release_asset" ]; then
                cp -rp "${release_asset}/"* "${shared_asset}/" 2>/dev/null || true
            fi
            rm -rf "$release_asset"
            ln -sfn "$shared_asset" "$release_asset"
            link_count=$((link_count + 1))
        fi
    done

    # Log symlinks (3)
    rm -rf "${release_dir}/admin/log"
    ln -sfn "${shared_dir}/logs/app_log" "${release_dir}/admin/log" && link_count=$((link_count + 1))
    rm -rf "${release_dir}/admin/tagging_log"
    ln -sfn "${shared_dir}/logs/tagging_log" "${release_dir}/admin/tagging_log" && link_count=$((link_count + 1))
    # Top-level log/
    rm -rf "${release_dir}/log"
    ln -sfn "${shared_dir}/log" "${release_dir}/log" && link_count=$((link_count + 1))

    # Webhook handler symlink
    if [ -f "${shared_dir}/webhook-deploy.php" ]; then
        mkdir -p "${release_dir}/webhooks"
        rm -f "${release_dir}/webhooks/deploy.php"
        ln -sfn "${shared_dir}/webhook-deploy.php" "${release_dir}/webhooks/deploy.php"
        link_count=$((link_count + 1))
    fi

    log "   ${link_count} symlinks created"
}

# =============================================================================
# PHP SYNTAX CHECK (aligned with deploy-mono.sh)
# =============================================================================

php_syntax_check() {
    local release_dir="$1"

    log "🔍 PHP syntax check..."

    local syntax_errors=""
    local syntax_count=0

    while IFS= read -r php_file; do
        result=$(php -d short_open_tag=Off -l "$php_file" 2>&1)
        if [ $? -ne 0 ]; then
            syntax_errors="${syntax_errors}\n  ❌ ${php_file}: ${result}"
            syntax_count=$((syntax_count + 1))
        fi
    done < <(find "${release_dir}" -name "*.php" \
        -not -path "*/vendor/*" -not -path "*/node_modules/*" \
        -not -name "*_[0-9][0-9]_[0-9][0-9]_[0-9][0-9].php" \
        -not -path "*/admin/application/controllers/reconstruct.php")

    if [ "$syntax_count" -gt 0 ]; then
        log "❌ PHP syntax check FAILED — ${syntax_count} file(s) with errors:"
        log "$syntax_errors"
        return 1
    fi

    log "✅ PHP syntax check passed"
    return 0
}

# =============================================================================
# HEALTH CHECK + AUTO-ROLLBACK (aligned with deploy-mono.sh)
# =============================================================================

health_check() {
    local target="$1"
    local health_url="$2"
    local prev_release="$3"
    local target_dir="${BASE_PATH}/${target}"

    log "🏥 Health check for ${target}: ${health_url}..."
    sleep 2  # Give Apache a moment to pick up the new symlink

    local health_status
    health_status=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$health_url" 2>/dev/null || echo "000")

    if [ "$health_status" -ge 200 ] && [ "$health_status" -lt 400 ]; then
        log "✅ Health check passed for ${target} (HTTP ${health_status})"
        return 0
    else
        log "❌ Health check FAILED for ${target} (HTTP ${health_status})"

        # Auto-rollback
        if [ -n "$prev_release" ] && [ -d "$prev_release" ]; then
            log "🔄 Rolling back ${target} to $(basename $prev_release)..."
            local temp_link="${target_dir}/current.rollback.$$"
            ln -sfn "$prev_release" "$temp_link" 2>/dev/null || sudo ln -sfn "$prev_release" "$temp_link" 2>/dev/null
            mv -Tf "$temp_link" "${target_dir}/current" 2>/dev/null || sudo mv -Tf "$temp_link" "${target_dir}/current" 2>/dev/null
            log "✅ Rolled back ${target}"
        else
            log "⚠️ No previous release to roll back to for ${target}"
        fi
        return 1
    fi
}

# =============================================================================
# CLEANUP OLD RELEASES
# =============================================================================

cleanup_releases() {
    local releases_dir="$1"
    local current_link="$2"
    local current_release=""

    [ -L "$current_link" ] && current_release=$(readlink -f "$current_link")

    local count=0
    for dir in $(ls -dt "${releases_dir}"/*/ 2>/dev/null); do
        dir="${dir%/}"
        [ "$dir" = "$current_release" ] && continue
        [ "$(basename "$dir")" = "release_initial" ] && continue  # Never delete initial release
        count=$((count + 1))
        if [ $count -ge $RELEASES_KEEP ]; then
            log "  🧹 Removing old release: $(basename $dir)"
            rm -rf "$dir"
        fi
    done
}

# =============================================================================
# PHP OPCACHE RESET
# =============================================================================

reset_opcache() {
    if [ "$ENABLE_OPCACHE_RESET" = "true" ]; then
        log "🔄 Resetting PHP OPcache..."
        for phpver in php-fpm php7.4-fpm php8.0-fpm php8.1-fpm php8.2-fpm php8.3-fpm; do
            if systemctl is-active --quiet "$phpver" 2>/dev/null; then
                systemctl reload "$phpver" || true
                log "  Reloaded $phpver"
                return
            fi
        done
        if systemctl is-active --quiet apache2 2>/dev/null; then
            systemctl reload apache2 || true
            log "  Reloaded Apache (mod_php opcache reset)"
        fi
    fi
}

# =============================================================================
# ROLLBACK
# =============================================================================

do_rollback() {
    local rollback_target="${1:-all}"

    for TARGET in "${TARGETS[@]}"; do
        if [ "$rollback_target" != "all" ] && [ "$rollback_target" != "$TARGET" ]; then
            continue
        fi

        local target_dir="${BASE_PATH}/${TARGET}"
        local current_link="${target_dir}/current"
        local rollback_file="${target_dir}/.last_release"

        if [ ! -f "$rollback_file" ]; then
            log_error "No rollback point for ${TARGET}"
            continue
        fi

        local previous
        previous=$(cat "$rollback_file")
        if [ ! -d "$previous" ]; then
            log_error "Rollback release not found: $previous"
            continue
        fi

        log "🔄 Rolling back ${TARGET} to $(basename $previous)..."
        local temp_link="${current_link}.rollback.$$"
        ln -sfn "$previous" "$temp_link"
        mv -Tf "$temp_link" "$current_link" 2>/dev/null || sudo mv -Tf "$temp_link" "$current_link" 2>/dev/null
        log_success "Rolled back ${TARGET} to $(basename $previous)"
    done

    reset_opcache
    exit 0
}

# =============================================================================
# MAIN DEPLOYMENT
# =============================================================================

main() {
    local start_time
    start_time=$(date +%s)
    DEPLOY_START_TIME=$start_time  # global for ERR trap

    # Handle rollback
    if [ "${1:-}" = "--rollback" ]; then
        do_rollback "${2:-all}"
    fi

    # ── flock-based concurrent deploy lock (aligned with deploy-mono.sh) ──
    LOCK_FILE="/tmp/deploy_production_source.lock"
    echo $$ > "$LOCK_FILE"
    trap 'rm -f "$LOCK_FILE"; cleanup' EXIT
    exec 200>"$LOCK_FILE"
    if ! flock -n 200; then
        log "⚠️ Another production deploy is already running. Waiting..."
        flock 200  # blocks until lock released
    fi

    # ── Validate prerequisites ──
    if [ -z "$GIT_REMOTE_URL" ]; then
        log_error "Cannot determine repo URL"
        exit 1
    fi

    log "═══════════════════════════════════════════════════════════"
    log "🚀 Production Deployment Started (Dual Target)"
    log "═══════════════════════════════════════════════════════════"
    log "📋 Branch: ${DEPLOY_BRANCH}"
    log "🎯 Targets: ${TARGETS[*]}"
    log "📦 Release: ${TIMESTAMP}"
    log "📋 Repo: ${GIT_REMOTE_URL}"
    log "👤 User: $(whoami)"
    log "⏰ Time: $(date '+%Y-%m-%d %H:%M:%S')"

    # ── DevOps callback: start ──
    devops_callback "start"

    # ── Fix permissions on all targets BEFORE clone ──
    for TARGET in "${TARGETS[@]}"; do
        ensure_target_permissions "${BASE_PATH}/${TARGET}"
    done

    # ── Activate maintenance page on both targets ──
    activate_maintenance

    # -----------------------------------------------------------------
    # STEP 1: Clone directly to first target's release dir
    #         Then copy to other targets (avoids /tmp/ permission issues)
    # -----------------------------------------------------------------
    local first_target="${TARGETS[0]}"
    local first_release_dir="${BASE_PATH}/${first_target}/releases/${TIMESTAMP}"

    # Ensure releases dir is writable
    ensure_dir_writable "${BASE_PATH}/${first_target}/releases"

    log "📥 Cloning ${DEPLOY_BRANCH} → ${first_release_dir}..."
    clone_output=$(git clone --depth 1 --branch "$DEPLOY_BRANCH" "$GIT_REMOTE_URL" "$first_release_dir" 2>&1)
    clone_status=$?

    if [ $clone_status -ne 0 ]; then
        log_error "Git clone failed (exit code: ${clone_status})"
        log_error "Error: ${clone_output}"
        DURATION=$(($(date +%s) - start_time))
        devops_callback "end" "failed"
        exit 1
    fi

    COMMIT_SHA=$(cd "$first_release_dir" && git rev-parse --short HEAD)
    COMMIT_MSG=$(cd "$first_release_dir" && git log -1 --pretty=%B | head -n1)
    COMMIT_AUTHOR=$(cd "$first_release_dir" && git log -1 --pretty=format:'%an')

    log "✅ Clone complete: ${COMMIT_SHA} — ${COMMIT_MSG}"

    # ── Stage self-updates (before any swap) ──
    mkdir -p "${BASE_PATH}/prod/shared/.pending_updates"
    if [ -f "${first_release_dir}/cicd/server/deploy-production.sh" ]; then
        cp "${first_release_dir}/cicd/server/deploy-production.sh" "${BASE_PATH}/prod/shared/.pending_updates/deploy-production.sh" 2>/dev/null
        log "   📋 Staged deploy-production.sh self-update"
    fi
    if [ -f "${first_release_dir}/cicd/server/deploy.php" ]; then
        cp "${first_release_dir}/cicd/server/deploy.php" "${BASE_PATH}/prod/shared/.pending_updates/webhook-deploy.php" 2>/dev/null
        log "   📋 Staged webhook-deploy.php update"
    fi

    # -----------------------------------------------------------------
    # STEP 2: PHP Syntax Check on first clone (applies to all targets)
    # -----------------------------------------------------------------
    if ! php_syntax_check "$first_release_dir"; then
        log_error "Aborting deployment due to PHP syntax errors"
        rm -rf "$first_release_dir"
        DURATION=$(($(date +%s) - start_time))
        devops_callback "end" "failed"
        exit 1
    fi

    # Update globals for DevOps callback
    DEPLOY_COMMIT_SHA="$commit_sha"
    DEPLOY_COMMIT_MSG="$commit_msg"

    # -----------------------------------------------------------------
    # STEP 3: Deploy to each target
    # -----------------------------------------------------------------
    local deploy_success=true
    local health_urls=()

    # Health check URLs per target
    declare -A TARGET_HEALTH_URLS=(
        ["prod"]="${PROD_HEALTH_URL:-https://retail.logimaxindia.com/}"
        ["sales"]="${SALES_HEALTH_URL:-https://demo.retail.logimaxindia.com/}"
    )

    for TARGET in "${TARGETS[@]}"; do
        local target_dir="${BASE_PATH}/${TARGET}"
        local shared_dir="${target_dir}/shared"
        local releases_dir="${target_dir}/releases"
        local current_link="${target_dir}/current"
        local release_dir="${releases_dir}/${TIMESTAMP}"
        local rollback_file="${target_dir}/.last_release"

        log ""
        log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        log "🎯 Deploying to: ${TARGET}"
        log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

        # Verify structure exists
        if [ ! -d "$shared_dir" ] || [ ! -d "$releases_dir" ]; then
            log_error "${TARGET} not set up for symlink deploy (missing shared/ or releases/)"
            deploy_success=false
            continue
        fi

        # Ensure releases dir is writable
        ensure_dir_writable "$releases_dir"

        # For the first target, release_dir already exists (from clone)
        # For subsequent targets, copy from first clone
        if [ "$TARGET" != "$first_target" ]; then
            log "📂 Copying release to ${TARGET}..."
            local copy_ok=false
            local copy_err=""

            # Try 1: cp -r without sudo (works if releases dir is writable)
            if copy_err=$(cp -r "$first_release_dir" "$release_dir" 2>&1); then
                copy_ok=true
                log "   ✅ Copy succeeded (direct)"
            else
                log "   ⚠️ Direct cp failed: ${copy_err}"
                # Try 2: sudo cp -r (fallback for permission issues)
                if copy_err=$(sudo cp -r "$first_release_dir" "$release_dir" 2>&1); then
                    copy_ok=true
                    log "   ✅ Copy succeeded (sudo cp)"
                else
                    log "   ⚠️ sudo cp failed: ${copy_err}"
                    # Try 3: rsync (handles symlinks and permissions gracefully)
                    if command -v rsync &>/dev/null; then
                        mkdir -p "$release_dir" 2>/dev/null || sudo mkdir -p "$release_dir" 2>/dev/null
                        if copy_err=$(sudo rsync -a "$first_release_dir/" "$release_dir/" 2>&1); then
                            copy_ok=true
                            log "   ✅ Copy succeeded (rsync)"
                        else
                            log "   ⚠️ rsync failed: ${copy_err}"
                        fi
                    fi
                fi
            fi

            if [ "$copy_ok" = false ] || [ ! -d "$release_dir" ]; then
                log_error "Failed to copy release to ${TARGET} — all copy methods exhausted"
                deploy_success=false
                continue
            fi
        fi

        # Create symlinks
        log "🔗 Creating symlinks..."
        create_symlinks "$release_dir" "$shared_dir"

        # Fix permissions
        log "🔐 Fixing permissions..."
        if [ "$(whoami)" = "www-data" ]; then
            sudo chown -R "${WEB_USER}:${WEB_USER}" "$release_dir" 2>/dev/null || true
        else
            sudo chown -R "${APP_USER}:${WEB_USER}" "$release_dir" 2>/dev/null || true
        fi
        sudo find "$release_dir" -type d -exec chmod 2775 {} \; 2>/dev/null || true
        sudo find "$release_dir" -type f -exec chmod 644 {} \; 2>/dev/null || true
        sudo find "$release_dir" -name "*.sh" -exec chmod 755 {} \; 2>/dev/null || true

        # Save rollback point
        local prev_release=""
        if [ -L "$current_link" ]; then
            prev_release=$(readlink -f "$current_link")
            echo "$prev_release" > "$rollback_file" 2>/dev/null || \
                sudo bash -c "echo '$prev_release' > '$rollback_file'" 2>/dev/null
            log "📦 Rollback point: $(basename $prev_release)"
        fi

        # Atomic symlink swap (with sudo fallback, like deploy-mono.sh)
        log "⚡ Atomic symlink swap..."
        local temp_link="${current_link}.new.$$"
        if ln -sfn "$release_dir" "$temp_link" 2>/dev/null; then
            mv -Tf "$temp_link" "$current_link" 2>/dev/null || \
                sudo mv -Tf "$temp_link" "$current_link" 2>/dev/null
        else
            sudo ln -sfn "$release_dir" "$temp_link" 2>/dev/null && \
                sudo mv -Tf "$temp_link" "$current_link" 2>/dev/null || \
                sudo ln -sfn "$release_dir" "$current_link" 2>/dev/null
        fi

        log_success "${TARGET}/current → releases/${TIMESTAMP} (${COMMIT_SHA})"

        # Deactivate maintenance for this target
        rm -f "${target_dir}/.maintenance_active" 2>/dev/null || \
            sudo rm -f "${target_dir}/.maintenance_active" 2>/dev/null
        log_success "Maintenance page deactivated for ${TARGET}"

        # Health check
        local health_url="${TARGET_HEALTH_URLS[$TARGET]:-http://localhost}"
        if ! health_check "$TARGET" "$health_url" "$prev_release"; then
            deploy_success=false
            log_error "Health check failed for ${TARGET} — rolled back"
        fi

        # Cleanup old releases
        cleanup_releases "$releases_dir" "$current_link"
    done

    # -----------------------------------------------------------------
    # STEP 4: Database migrations (both prod and demo)
    # -----------------------------------------------------------------
    log ""
    log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    log "🗄️  Running database migrations..."
    log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

    local migration_success=true
    local migration_script="${BASE_PATH}/prod/current/scripts/run-migrations.sh"

    if [ -f "$migration_script" ]; then
        for TARGET in "${TARGETS[@]}"; do
            local target_dir="${BASE_PATH}/${TARGET}"
            local config_file="${target_dir}/shared/config/global_configs.php"

            if [ ! -f "$config_file" ]; then
                log_error "Config not found for ${TARGET}: ${config_file}"
                migration_success=false
                continue
            fi

            log "🔄 Migrating ${TARGET} database..."

            local migration_output
            migration_output=$(bash "$migration_script" \
                --config "$config_file" \
                --migrations "${BASE_PATH}/prod/current/database/migrations" \
                --env production \
                --json 2>&1)
            local migration_exit=$?

            if [ $migration_exit -eq 0 ]; then
                local applied_count
                applied_count=$(echo "$migration_output" | grep -o '"applied":[0-9]*' | grep -o '[0-9]*' || echo "?")
                log_success "${TARGET}: ${applied_count} migration(s) applied"
            elif [ $migration_exit -eq 2 ]; then
                log_success "${TARGET}: database up to date"
            else
                log_error "${TARGET}: migration failed!"
                log "  Output: ${migration_output}"
                migration_success=false
            fi
        done
    else
        log "⚠️  Migration script not found: ${migration_script}"
        log "  Skipping auto-migrations"
    fi

    # -----------------------------------------------------------------
    # STEP 5: Cleanup and finalize
    # -----------------------------------------------------------------

    # Reset OPcache
    reset_opcache

    DURATION=$(($(date +%s) - start_time))

    log ""
    log "═══════════════════════════════════════════════════════════"
    if [ "$deploy_success" = true ] && [ "$migration_success" = true ]; then
        log_success "Deployment + migrations completed in ${DURATION}s"
        log_success "Release: ${TIMESTAMP} (${COMMIT_SHA})"
    elif [ "$deploy_success" = true ]; then
        log "⚠️  Deployment OK but migrations had errors (${DURATION}s)"
        log_success "Release: ${TIMESTAMP} (${COMMIT_SHA})"
    else
        log_error "Deployment completed with errors in ${DURATION}s"
    fi
    log "═══════════════════════════════════════════════════════════"

    # ── DevOps callback: end (with migration results) ──
    local status
    if [ "$deploy_success" = true ] && [ "$migration_success" = true ]; then
        status="success"
    elif [ "$deploy_success" = true ]; then
        status="partial"
    else
        status="failed"
    fi
    local migration_extra=""
    if [ "$migration_success" = true ]; then
        migration_extra=",\"migrations\":{\"applied\":0,\"failed\":0,\"status\":\"success\"}"
    else
        migration_extra=",\"migrations\":{\"applied\":0,\"failed\":1,\"status\":\"failed\"}"
    fi
    devops_callback "end" "$status" "$migration_extra"

    # ── Structured log entry ──
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] | source | production | ${DEPLOY_BRANCH} | ${COMMIT_SHA} | ${COMMIT_AUTHOR} | ${DURATION}s | ${COMMIT_MSG}" >> "${BASE_PATH}/prod/shared/logs/deploy-history.log" 2>/dev/null

    # Output JSON for webhook response
    echo "{\"status\":\"${status}\",\"duration\":\"${DURATION}s\",\"release\":\"${TIMESTAMP}\",\"commit\":\"${COMMIT_SHA}\",\"targets\":\"${TARGETS[*]}\",\"migrations\":\"${migration_success}\"}"
}

main "$@"
