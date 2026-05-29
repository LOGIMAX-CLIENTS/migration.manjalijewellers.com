#!/bin/bash
# =============================================================================
# UNIFIED DEPLOY SCRIPT - Logimax CI/CD
# Works for ALL environments (auto-detects from current directory)
# Supports both webhook automation and manual CLI usage
# =============================================================================

# NOTE: DO NOT use 'set -e' here — it silently kills the script on any
# non-zero exit code (e.g. git fetch network hiccup), and the PHP webhook
# handler never sees why. We use explicit error checks instead.

# =============================================================================
# AUTO-DETECT CONFIGURATION
# =============================================================================

# Get script's actual directory (where it's running from)
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_PATH="$SCRIPT_DIR"

# Auto-detect environment name from folder
ENV_NAME=$(basename "$REPO_PATH")

# Auto-detect client name from path: /var/www/{client}/{env}
CLIENT_NAME=$(basename "$(dirname "$REPO_PATH")")

# SSH Key for GitHub — prefer client-specific key, fall back to generic
resolve_ssh_key() {
    local user_home="$1"
    local client="$2"
    # 1. Client-specific key (e.g., id_ed25519_sarangapani)
    if [ -f "${user_home}/.ssh/id_ed25519_${client}" ]; then
        echo "${user_home}/.ssh/id_ed25519_${client}"
    # 2. Generic key
    elif [ -f "${user_home}/.ssh/id_ed25519" ]; then
        echo "${user_home}/.ssh/id_ed25519"
    else
        echo ""
    fi
}

if [ "$(whoami)" == "www-data" ]; then
    SSH_KEY=$(resolve_ssh_key "/var/www" "$CLIENT_NAME")
else
    SSH_KEY="${SSH_KEY:-$(resolve_ssh_key "/home/ubuntu" "$CLIENT_NAME")}"
fi

if [ -z "$SSH_KEY" ]; then
    echo "❌ No SSH key found for client '$CLIENT_NAME'"
    exit 1
fi

# Log file
DEPLOY_LOG="$REPO_PATH/deploy.log"

# Lock file (prevent concurrent deployments)
LOCK_FILE="/tmp/deploy_${ENV_NAME}.lock"

# =============================================================================
# BRANCH MAPPING (default branch per environment)
# =============================================================================

get_default_branch() {
    case "$ENV_NAME" in
        dev)           echo "Retail_1.1.1.0001" ;;
        qa)            echo "QA" ;;
        staging)       echo "support" ;;
        prod)          echo "Production" ;;
        *)             echo "Retail_1.1.1.0001" ;;
    esac
}

# =============================================================================
# HELPER FUNCTIONS
# =============================================================================

log() {
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] $1" >> "$DEPLOY_LOG"
    echo "$1"
}

cleanup() {
    rm -f "$LOCK_FILE"
}

# =============================================================================
# MAIN DEPLOYMENT
# =============================================================================

# Trap for cleanup
trap cleanup EXIT

# Check for concurrent deployments — also clean up stale locks (older than 10 min)
if [ -f "$LOCK_FILE" ]; then
    lock_age=$(( $(date +%s) - $(stat -c %Y "$LOCK_FILE" 2>/dev/null || echo 0) ))
    if [ "$lock_age" -gt 600 ]; then
        log "⚠️  Stale lock file detected (${lock_age}s old). Removing..."
        rm -f "$LOCK_FILE"
    else
        log "⚠️  Deployment for $ENV_NAME already in progress. Exiting."
        exit 1
    fi
fi

# Create lock file
touch "$LOCK_FILE"

# Change to repo directory
cd "$REPO_PATH" || {
    log "❌ Failed to enter repo path: $REPO_PATH"
    exit 1
}

# Bypass git safe.directory check (repo may be owned by different user)
export GIT_CONFIG_COUNT=1
export GIT_CONFIG_KEY_0=safe.directory
export GIT_CONFIG_VALUE_0="$REPO_PATH"

# Set SSH command for GitHub
export GIT_SSH_COMMAND="ssh -i $SSH_KEY -o IdentitiesOnly=yes -o StrictHostKeyChecking=no"

# Get branch (from argument or default for environment)
if [ -z "$1" ]; then
    branch=$(get_default_branch)
else
    branch="$1"
fi

# Timestamp and user info
timestamp=$(date '+%Y-%m-%d %H:%M:%S')
system_user=$(whoami)
ssh_ip="${SSH_CONNECTION%% *}"
[ -z "$ssh_ip" ] && ssh_ip="Webhook/Local"

# Determine if running interactively or via webhook
is_interactive=false
[ -t 0 ] && is_interactive=true

log "═══════════════════════════════════════════════════════════"
log "🚀 Deployment Started: $ENV_NAME"
log "═══════════════════════════════════════════════════════════"
log "📋 Environment: $ENV_NAME"
log "📋 Branch: $branch"
log "👤 User: $system_user"
log "🌐 Source: $ssh_ip"
log "⏰ Time: $timestamp"
log "🔑 SSH Key: $SSH_KEY (exists: $([ -f "$SSH_KEY" ] && echo 'yes' || echo 'NO'))"

# Get commit before pull
before_commit=$(git rev-parse --short HEAD 2>/dev/null || echo "initial")

# Check for local changes (interactive only)
if [ "$is_interactive" = true ]; then
    if ! git diff --quiet || ! git diff --cached --quiet || [ -n "$(git ls-files --others --exclude-standard)" ]; then
        log "⚠️  Local changes detected:"
        git status --short
        echo ""
        read -p "❓ Overwrite local changes with remote? (y/N): " confirm
        if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
            log "🚫 Deployment cancelled by user."
            exit 1
        fi
    fi
fi

# Remove any stale git lock files (refs + index)
find .git/refs -name "*.lock" -delete 2>/dev/null || true

# Bypass known_hosts issues for GitHub
export GIT_SSH_COMMAND="ssh -i $SSH_KEY -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"

# Fetch all remotes
log "📥 Fetching latest changes..."
fetch_output=$(git fetch --all --prune 2>&1)
fetch_status=$?
if [ $fetch_status -ne 0 ]; then
    log "⚠️  Full fetch failed, trying target branch only..."
    fetch_output=$(git fetch origin "$branch" 2>&1)
    fetch_status=$?
    if [ $fetch_status -ne 0 ]; then
        log "❌ git fetch failed (exit code: $fetch_status)"
        log "❌ Error: $fetch_output"
        echo "❌ Deployment FAILED for $ENV_NAME — git fetch error"
        exit 1
    fi
fi
log "📥 Fetch: $fetch_output"

# Switch to branch if needed
current_branch=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "unknown")
if [ "$current_branch" != "$branch" ]; then
    log "📍 Switching to branch: $branch (currently on: $current_branch)"
    checkout_output=$(git checkout "$branch" 2>&1 || git checkout -b "$branch" "origin/$branch" 2>&1)
    checkout_status=$?
    if [ $checkout_status -ne 0 ]; then
        log "❌ git checkout failed (exit code: $checkout_status)"
        log "❌ Error: $checkout_output"
        echo "❌ Deployment FAILED for $ENV_NAME — git checkout error"
        exit 1
    fi
    log "📍 Checkout: $checkout_output"
fi

# Reset to origin (force update)
log "🔄 Applying updates from origin/$branch..."
reset_output=$(git reset --hard "origin/$branch" 2>&1)
reset_status=$?
if [ $reset_status -ne 0 ]; then
    log "❌ git reset failed (exit code: $reset_status)"
    log "❌ Error: $reset_output"
    echo "❌ Deployment FAILED for $ENV_NAME — git reset error"
    exit 1
fi
log "🔄 Reset: $reset_output"

git clean -fd -q -e "port_max/_app" -e "port_max/index.html" -e "port_max/favicon*" -e "port_max/migration/dist" -e "port_max/migration/node_modules" -e "port_max/api/vendor" 2>/dev/null || true

# Get commit after pull
after_commit=$(git rev-parse --short HEAD)
commit_msg=$(git log -1 --pretty=%B | head -n1)
commit_author=$(git log -1 --pretty=format:'%an')

# Run database migrations (always, unless explicitly disabled with SKIP_MIGRATIONS=true)
if [ "$SKIP_MIGRATIONS" != "true" ] && [ -f "./scripts/run-migrations.sh" ]; then
    log "🔧 Running database migrations..."
    bash ./scripts/run-migrations.sh --config "$REPO_PATH/global_configs.php" --env "$ENV_NAME" >> "$DEPLOY_LOG" 2>&1
    migrate_exit=$?
    if [ $migrate_exit -eq 0 ]; then
        log "✅ Migrations applied successfully"
    elif [ $migrate_exit -eq 2 ]; then
        log "ℹ️  No pending migrations"
    else
        log "⚠️  Migration script failed (exit code: $migrate_exit)"
    fi
fi

# Clear cache (CodeIgniter)
if [ -d "./admin/application/cache" ]; then
    find ./admin/application/cache -type f ! -name 'index.html' -delete 2>/dev/null || true
    log "🧹 Cache cleared"
fi

# Fix file ownership to www-data (when run manually as ubuntu/root)
if [ "$(whoami)" != "www-data" ]; then
    log "🔒 Fixing permissions (setting www-data ownership)..."
    chown -R www-data:www-data "$REPO_PATH" 2>/dev/null || true
    find "$REPO_PATH" -type d -exec chmod 2775 {} \; 2>/dev/null || true
    find "$REPO_PATH" -type f -exec chmod 664 {} \; 2>/dev/null || true
    find "$REPO_PATH" -name "*.sh" -exec chmod 775 {} \; 2>/dev/null || true
    log "✅ Permissions fixed (www-data:www-data)"
fi

# Run Port_Max deploy if port_max files changed
if [ -f "$REPO_PATH/port_max/deploy.sh" ]; then
    # Check if port_max files changed in this deploy
    portmax_changes=$(git diff --name-only "$before_commit" "$after_commit" -- port_max/ 2>/dev/null | head -1)
    if [ -n "$portmax_changes" ]; then
        log "🔧 Port_Max changes detected — running port_max/deploy.sh..."
        if [ "$(whoami)" != "www-data" ]; then
            sudo -u www-data bash "$REPO_PATH/port_max/deploy.sh" 2>&1 | tee -a "$DEPLOY_LOG"
        else
            bash "$REPO_PATH/port_max/deploy.sh" 2>&1 | tee -a "$DEPLOY_LOG"
        fi
    fi
fi

# Log summary
log "═══════════════════════════════════════════════════════════"
log "✅ Deployment Complete"
log "═══════════════════════════════════════════════════════════"
log "📊 Changes: $before_commit → $after_commit"
log "💬 Message: $commit_msg"
log "👤 Author: $commit_author"
log ""

# Save structured log entry
echo "[$timestamp] | $ENV_NAME | $branch | $before_commit→$after_commit | $commit_author | $ssh_ip | $commit_msg" >> "$REPO_PATH/deploy-history.log"

echo "✅ Deployment complete for $ENV_NAME"