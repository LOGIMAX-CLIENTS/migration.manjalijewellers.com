#!/bin/bash
# =============================================================================
# UNIFIED DEPLOYMENT SCRIPT
# Supports: Simple (git pull) | Symlink (zero-downtime) | Rollback
# =============================================================================

set -e

# =============================================================================
# CONFIGURATION (Environment Variables)
# =============================================================================
: "${REPO_PATH:?REPO_PATH environment variable is required}"
: "${DEPLOY_BRANCH:?DEPLOY_BRANCH environment variable is required}"

# Deployment mode
DEPLOY_MODE="${DEPLOY_MODE:-simple}"          # simple | symlink
MAINTENANCE_MODE="${MAINTENANCE_MODE:-false}"
ENVIRONMENT="${ENVIRONMENT:-staging}"

# Paths and settings
DEPLOY_LOG="${DEPLOY_LOG:-$REPO_PATH/deploy.log}"
SSH_KEY="${SSH_KEY:-$HOME/.ssh/id_rsa_deploy}"
LOCK_FILE="${LOCK_FILE:-/tmp/deploy_$DEPLOY_BRANCH.lock}"
GIT_REMOTE="${GIT_REMOTE:-origin}"
DEPLOY_SOURCE="${DEPLOY_SOURCE:-Webhook-Auto}"
CLEAR_CACHE="${CLEAR_CACHE:-}"
POST_DEPLOY_SCRIPT="${POST_DEPLOY_SCRIPT:-}"

# Symlink mode paths
BASE_DIR="${BASE_DIR:-$(dirname $REPO_PATH)}"
RELEASES_DIR="${RELEASES_DIR:-$BASE_DIR/releases}"
SHARED_DIR="${SHARED_DIR:-$BASE_DIR/shared}"
CURRENT_LINK="${CURRENT_LINK:-$BASE_DIR/current}"
RELEASES_KEEP="${RELEASES_KEEP:-5}"
ROLLBACK_FILE="$BASE_DIR/.last_release"

# =============================================================================
# SYSTEM INFO
# =============================================================================
SYSTEM_USER=$(whoami)
SSH_IP=$(echo $SSH_CONNECTION | awk '{print $1}')
[ -z "$SSH_IP" ] && SSH_IP="Local/Console"

# =============================================================================
# LOGGING
# =============================================================================
log() {
    local level="$1"; shift
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    local msg="[$timestamp] [$level] $*"
    echo "$msg" | tee -a "$DEPLOY_LOG"
}

log_info() { log "INFO" "$@"; }
log_error() { log "ERROR" "$@"; }
log_success() { log "SUCCESS" "$@"; }

# =============================================================================
# LOCK MANAGEMENT (Prevent Concurrent Deploys)
# =============================================================================
acquire_lock() {
    if [ -f "$LOCK_FILE" ]; then
        local pid=$(cat "$LOCK_FILE" 2>/dev/null)
        if [ -n "$pid" ] && kill -0 "$pid" 2>/dev/null; then
            log_error "⚠️  Deployment for $DEPLOY_BRANCH already in progress (PID: $pid). Exiting."
            exit 1
        fi
        log_info "Removing stale lock file"
        rm -f "$LOCK_FILE"
    fi
    echo $$ > "$LOCK_FILE"
    trap 'rm -f "$LOCK_FILE"' EXIT
}

# =============================================================================
# SSH KEY SETUP
# =============================================================================
setup_git_ssh() {
    if [ -f "$SSH_KEY" ]; then
        export GIT_SSH_COMMAND="ssh -i $SSH_KEY -o IdentitiesOnly=yes -o StrictHostKeyChecking=no"
        log_info "🔑 Using SSH key: $SSH_KEY"
    elif [ -f "$HOME/.ssh/id_rsa" ]; then
        export GIT_SSH_COMMAND="ssh -i $HOME/.ssh/id_rsa"
        log_info "🔑 Using default SSH key"
    fi
}

# =============================================================================
# MAINTENANCE MODE
# =============================================================================
maintenance_on() {
    if [ "$MAINTENANCE_MODE" = "true" ]; then
        log_info "🔧 Enabling maintenance mode..."
        local maint_path="$REPO_PATH/.maintenance"
        [ -L "$CURRENT_LINK" ] && maint_path="$CURRENT_LINK/.maintenance"
        touch "$maint_path"
    fi
}

maintenance_off() {
    if [ "$MAINTENANCE_MODE" = "true" ]; then
        log_info "🔧 Disabling maintenance mode..."
        rm -f "$REPO_PATH/.maintenance" "$CURRENT_LINK/.maintenance" 2>/dev/null || true
    fi
}

# =============================================================================
# CACHE CLEARING
# =============================================================================
clear_cache() {
    if [ -n "$CLEAR_CACHE" ]; then
        log_info "🧹 Clearing cache directories..."
        find "$REPO_PATH" -name "cache" -type d -exec rm -rf {} + 2>/dev/null || true
        log_info "🧹 Cache cleared"
    fi
}

# =============================================================================
# POST-DEPLOY SCRIPT
# =============================================================================
run_post_deploy() {
    if [ -n "$POST_DEPLOY_SCRIPT" ] && [ -f "$POST_DEPLOY_SCRIPT" ]; then
        log_info "🔧 Running post-deploy script: $POST_DEPLOY_SCRIPT"
        bash "$POST_DEPLOY_SCRIPT" >> "$DEPLOY_LOG" 2>&1
    fi
}

# =============================================================================
# SIMPLE DEPLOYMENT (Git Pull - for Staging)
# =============================================================================
deploy_simple() {
    log_info "════════════════════════════════════════"
    log_info "📦 SIMPLE Deployment (git pull)"
    log_info "👤 User: $SYSTEM_USER"
    log_info "🌐 SSH IP: $SSH_IP"
    log_info "════════════════════════════════════════"
    
    cd "$REPO_PATH" || { 
        log_error "❌ Failed to enter repo path $REPO_PATH"
        exit 1 
    }
    
    setup_git_ssh
    
    # Fetch all branches and tags
    log_info "📥 Fetching all branches and tags..."
    git fetch --all
    
    # Checkout target branch
    log_info "📍 Switching to branch: $DEPLOY_BRANCH"
    git checkout "$DEPLOY_BRANCH"
    
    # Get commit before pull
    before_commit=$(git rev-parse HEAD)
    before_commit_short=$(git rev-parse --short HEAD)
    
    # Check for local changes and stash if needed
    stash_applied=false
    if ! git diff --quiet || ! git diff --cached --quiet || [ -n "$(git ls-files --others --exclude-standard)" ]; then
        log_info "⚠️  Local changes detected, stashing..."
        git stash --include-untracked || true
        stash_applied=true
    fi
    
    # Reset to remote and pull with --no-rebase
    log_info "🔄 Resetting to $GIT_REMOTE/$DEPLOY_BRANCH..."
    git reset --hard "$GIT_REMOTE/$DEPLOY_BRANCH"
    git clean -fd -q
    
    log_info "🚀 Pulling latest changes from $GIT_REMOTE/$DEPLOY_BRANCH..."
    git pull --no-rebase "$GIT_REMOTE" "$DEPLOY_BRANCH"
    
    # Reapply stashed changes if any
    if [ "$stash_applied" = true ]; then
        log_info "🔄 Reapplying stashed changes..."
        git stash pop || log_info "⚠️  No stash to apply or conflict occurred"
    fi
    
    # Get commit after pull
    after_commit=$(git rev-parse HEAD)
    after_commit_short=$(git rev-parse --short HEAD)
    commit_msg=$(git log -1 --pretty=%B | head -n1)
    commit_author=$(git log -1 --pretty=format:'%an <%ae>')
    
    # Log deployment details
    log_entry="Deployed by: $SYSTEM_USER ($SSH_IP) | Branch: $DEPLOY_BRANCH | Before: $before_commit_short | After: $after_commit_short | Commit by: $commit_author | Message: $commit_msg"
    log_success "$log_entry"
    
    # Clear cache if requested
    clear_cache
}

# =============================================================================
# SYMLINK DEPLOYMENT (Zero-Downtime - for Production)
# =============================================================================
deploy_symlink() {
    log_info "════════════════════════════════════════"
    log_info "📦 SYMLINK Deployment (zero-downtime)"
    log_info "👤 User: $SYSTEM_USER"
    log_info "🌐 SSH IP: $SSH_IP"
    log_info "════════════════════════════════════════"
    
    setup_git_ssh
    
    # Get git remote URL
    local git_url="${GIT_REMOTE_URL:-$(cd $REPO_PATH && git remote get-url origin 2>/dev/null)}"
    if [ -z "$git_url" ]; then
        log_error "Could not determine git remote URL"
        exit 1
    fi
    
    # 1. Create timestamped release directory
    local release_name=$(date '+%Y%m%d_%H%M%S')
    local release_path="$RELEASES_DIR/$release_name"
    
    mkdir -p "$RELEASES_DIR"
    log_info "📁 Creating release: $release_name"
    
    # 2. Clone fresh copy (shallow for speed)
    log_info "� Cloning repository..."
    git clone --branch "$DEPLOY_BRANCH" --depth 1 --single-branch "$git_url" "$release_path"
    
    # 3. Link shared directories
    if [ -d "$SHARED_DIR" ]; then
        log_info "🔗 Linking shared directories..."
        
        # Link top-level shared directories
        for dir in logs uploads img bill_qrcode esti_qrcode log data; do
            if [ -e "$SHARED_DIR/$dir" ]; then
                rm -rf "$release_path/$dir"
                ln -sfn "$SHARED_DIR/$dir" "$release_path/$dir"
                log_info "  ✓ $dir"
            fi
        done
        
        # Link admin subdirectories
        if [ -d "$SHARED_DIR/admin" ]; then
            mkdir -p "$release_path/admin"
            for dir in log kyc tagging_log vendor_ack other_inventory_qrcode other_product_qrcode; do
                if [ -e "$SHARED_DIR/admin/$dir" ]; then
                    rm -rf "$release_path/admin/$dir"
                    ln -sfn "$SHARED_DIR/admin/$dir" "$release_path/admin/$dir"
                    log_info "  ✓ admin/$dir"
                fi
            done
        fi
    fi
    
    # 4. Copy config files from shared
    for config in database.php global_configs.php; do
        if [ -f "$SHARED_DIR/$config" ]; then
            if [ "$config" = "database.php" ]; then
                mkdir -p "$release_path/admin/application/config"
                cp "$SHARED_DIR/$config" "$release_path/admin/application/config/$config"
            else
                cp "$SHARED_DIR/$config" "$release_path/$config"
            fi
            log_info "  ✓ $config (copied)"
        fi
    done
    
    # Config.php if exists
    if [ -f "$SHARED_DIR/config.php" ]; then
        mkdir -p "$release_path/admin/application/config"
        cp "$SHARED_DIR/config.php" "$release_path/admin/application/config/config.php"
        log_info "  ✓ config.php (copied)"
    fi
    
    # 5. Save current release for rollback
    if [ -L "$CURRENT_LINK" ]; then
        local previous=$(readlink -f "$CURRENT_LINK")
        echo "$previous" > "$ROLLBACK_FILE"
        log_info "📦 Saved rollback point: $(basename $previous)"
    fi
    
    # 6. ATOMIC SYMLINK SWAP
    log_info "⚡ Atomic symlink swap..."
    local temp_link="${CURRENT_LINK}.new.$$"
    ln -sfn "$release_path" "$temp_link"
    mv -Tf "$temp_link" "$CURRENT_LINK"
    
    # Get commit info
    after_commit_short=$(cd "$release_path" && git rev-parse --short HEAD)
    commit_msg=$(cd "$release_path" && git log -1 --pretty=%B | head -n1)
    commit_author=$(cd "$release_path" && git log -1 --pretty=format:'%an <%ae>')
    
    log_success "✅ Deployed: $release_name ($after_commit_short)"
    log_success "   By: $commit_author"
    log_success "   Message: $commit_msg"
    
    # 7. Cleanup old releases
    cleanup_old_releases
}

# =============================================================================
# CLEANUP OLD RELEASES
# =============================================================================
cleanup_old_releases() {
    log_info "🧹 Cleaning old releases (keeping $RELEASES_KEEP)..."
    
    cd "$RELEASES_DIR" || return
    
    local current_release=""
    [ -L "$CURRENT_LINK" ] && current_release=$(readlink -f "$CURRENT_LINK")
    
    local count=0
    for dir in $(ls -dt */); do
        dir="${dir%/}"
        local full_path="$RELEASES_DIR/$dir"
        
        # Never delete current release
        [ "$full_path" = "$current_release" ] && continue
        
        count=$((count + 1))
        if [ $count -ge $RELEASES_KEEP ]; then
            log_info "  Removing: $dir"
            rm -rf "$full_path"
        fi
    done
}

# =============================================================================
# MAIN EXECUTION
# =============================================================================
main() {
    local start_time=$(date +%s)
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    local STATUS="success"
    
    log_info "════════════════════════════════════════"
    log_info "🚀 Automated Deployment Started at $timestamp"
    log_info "📋 Branch: $DEPLOY_BRANCH"
    log_info "🚀 Source: $DEPLOY_SOURCE"
    log_info "📁 Repo Path: $REPO_PATH"
    log_info "🔧 Mode: $DEPLOY_MODE"
    log_info "🌍 Environment: $ENVIRONMENT"
    log_info "════════════════════════════════════════"
    
    acquire_lock
    
    case "$DEPLOY_MODE" in
        simple)
            deploy_simple
            ;;
        symlink)
            maintenance_on
            deploy_symlink
            maintenance_off
            ;;
        *)
            log_error "Unknown deploy mode: $DEPLOY_MODE"
            STATUS="failed"
            ;;
    esac
    
    # Post-deploy tasks
    run_post_deploy

    # =========================================================================
    # SQL Migrations (run after deploy, before final status)
    # =========================================================================
    if [ "$STATUS" = "success" ]; then
        local deploy_root="$REPO_PATH"
        [ "$DEPLOY_MODE" = "symlink" ] && [ -L "$CURRENT_LINK" ] && deploy_root=$(readlink -f "$CURRENT_LINK")

        local migrate_script="$deploy_root/scripts/run-migrations.sh"
        local migrate_dir="$deploy_root/database/migrations"

        # Try environment-specific config, fall back to shared/default
        local config_file="$SHARED_DIR/config/global_configs_${ENVIRONMENT}.php"
        [ ! -f "$config_file" ] && config_file="$SHARED_DIR/global_configs.php"
        [ ! -f "$config_file" ] && config_file="$deploy_root/global_configs.php"

        if [ -f "$migrate_script" ] && [ -d "$migrate_dir" ]; then
            local sql_count=$(find "$migrate_dir" -maxdepth 1 -name '*.sql' -type f 2>/dev/null | wc -l)
            if [ "$sql_count" -gt 0 ]; then
                log_info "🗄 Running $sql_count SQL migration(s)..."
                local migrate_result
                migrate_result=$(bash "$migrate_script" --config "$config_file" --migrations "$migrate_dir" --env "$ENVIRONMENT" --json 2>&1) || true

                if echo "$migrate_result" | grep -q '"status":"error"\|FAILED'; then
                    log_error "❌ SQL migration FAILED: $migrate_result"
                    # Don't change STATUS — code is deployed, just log the migration issue
                else
                    log_success "✅ SQL migrations completed: $migrate_result"
                fi
            else
                log_info "🗄 No pending SQL migrations"
            fi
        fi
    fi
    
    local duration=$(($(date +%s) - start_time))
    
    if [ "$STATUS" = "success" ]; then
        log_info "════════════════════════════════════════"
        log_success "✅ Automated deployment completed at $(date)"
        log_success "📊 Duration: ${duration}s"
        log_info "════════════════════════════════════════"
    else
        log_error "❌ Automated deployment failed at $(date)"
    fi

    # =========================================================================
    # DevOps Tool Callback (send log_tail)
    # =========================================================================
    if [ -n "${DEVOPS_API_URL:-}" ]; then
        local log_tail=""
        if [ -f "$DEPLOY_LOG" ]; then
            log_tail=$(tail -30 "$DEPLOY_LOG" 2>/dev/null | sed 's/"/\\"/g' | tr '\n' '|')
        fi

        local commit_sha=""
        local commit_message=""
        local deploy_root="$REPO_PATH"
        [ "$DEPLOY_MODE" = "symlink" ] && [ -L "$CURRENT_LINK" ] && deploy_root=$(readlink -f "$CURRENT_LINK")
        if [ -d "$deploy_root/.git" ]; then
            commit_sha=$(cd "$deploy_root" && git rev-parse --short HEAD 2>/dev/null || echo "")
            commit_message=$(cd "$deploy_root" && git log -1 --pretty=%B 2>/dev/null | head -n1 | sed 's/"/\\"/g' || echo "")
        fi

        curl -s -X POST "${DEVOPS_API_URL}/api/webhooks/deploy-log" \
            -H "Content-Type: application/json" \
            -H "X-Webhook-Secret: ${DEVOPS_WEBHOOK_SECRET:-}" \
            -d "{
                \"client_id\": \"source\",
                \"client_name\": \"Logimax-Technologies/etail_development_src\",
                \"environment\": \"${ENVIRONMENT}\",
                \"status\": \"${STATUS}\",
                \"branch\": \"${DEPLOY_BRANCH}\",
                \"commit_sha\": \"${commit_sha}\",
                \"commit_message\": \"${commit_message}\",
                \"triggered_by\": \"${SYSTEM_USER}\",
                \"trigger_type\": \"webhook\",
                \"deploy_type\": \"source\",
                \"duration_seconds\": \"${duration}\",
                \"log_tail\": \"${log_tail}\"
            }" --max-time 10 2>/dev/null || log_info "⚠️ Failed to notify DevOps Tool"
    fi
    
    # Output JSON for webhook response
    echo "{\"status\":\"$STATUS\",\"duration\":\"${duration}s\",\"environment\":\"$ENVIRONMENT\",\"branch\":\"$DEPLOY_BRANCH\"}"
}

main "$@"
