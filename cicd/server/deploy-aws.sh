#!/bin/bash
# =============================================================================
# AWS EC2 DEPLOYMENT SCRIPT
# Supports: Simple (git pull) | Symlink (zero-downtime) | Rollback
# Designed for: Amazon Linux 2, Ubuntu on EC2
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

# AWS-specific paths (different from cPanel)
WEB_USER="${WEB_USER:-www-data}"              # Ubuntu: www-data, Amazon Linux: apache
APP_USER="${APP_USER:-ec2-user}"              # The user running deployments
DEPLOY_LOG="${DEPLOY_LOG:-/var/log/deploy/deploy.log}"
LOCK_FILE="${LOCK_FILE:-/tmp/deploy_$DEPLOY_BRANCH.lock}"
GIT_REMOTE="${GIT_REMOTE:-origin}"

# SSH key location (EC2 typically uses deploy keys)
SSH_KEY="${SSH_KEY:-/home/$APP_USER/.ssh/deploy_key}"

# Symlink mode paths
BASE_DIR="${BASE_DIR:-$(dirname $REPO_PATH)}"
RELEASES_DIR="${RELEASES_DIR:-$BASE_DIR/releases}"
SHARED_DIR="${SHARED_DIR:-$BASE_DIR/shared}"
CURRENT_LINK="${CURRENT_LINK:-$BASE_DIR/current}"
RELEASES_KEEP="${RELEASES_KEEP:-5}"
ROLLBACK_FILE="$BASE_DIR/.last_release"

# AWS-specific options
S3_BACKUP_BUCKET="${S3_BACKUP_BUCKET:-}"      # Optional: s3://bucket-name/backups
CLOUDWATCH_LOG_GROUP="${CLOUDWATCH_LOG_GROUP:-/logimax/deployments}"
SNS_TOPIC_ARN="${SNS_TOPIC_ARN:-}"            # Optional: SNS for notifications
ENABLE_OPCACHE_RESET="${ENABLE_OPCACHE_RESET:-true}"

# =============================================================================
# AWS HELPER FUNCTIONS
# =============================================================================

# Check if running on EC2
is_ec2() {
    if [ -f /sys/hypervisor/uuid ] && grep -qi ec2 /sys/hypervisor/uuid 2>/dev/null; then
        return 0
    fi
    if curl -s --max-time 1 http://169.254.169.254/latest/meta-data/ >/dev/null 2>&1; then
        return 0
    fi
    return 1
}

# Get EC2 instance metadata
get_instance_id() {
    if is_ec2; then
        curl -s --max-time 2 http://169.254.169.254/latest/meta-data/instance-id 2>/dev/null || echo "unknown"
    else
        hostname
    fi
}

# Log to CloudWatch (if configured)
log_to_cloudwatch() {
    local message="$1"
    local level="${2:-INFO}"
    
    if [ -n "$CLOUDWATCH_LOG_GROUP" ] && command -v aws &>/dev/null; then
        local timestamp=$(date +%s000)
        local log_stream="deployments/$(date +%Y/%m/%d)"
        
        # Create log stream if needed (ignore errors)
        aws logs create-log-stream \
            --log-group-name "$CLOUDWATCH_LOG_GROUP" \
            --log-stream-name "$log_stream" 2>/dev/null || true
        
        # Send log event
        aws logs put-log-events \
            --log-group-name "$CLOUDWATCH_LOG_GROUP" \
            --log-stream-name "$log_stream" \
            --log-events "timestamp=$timestamp,message=[$level] $message" 2>/dev/null || true
    fi
}

# Send SNS notification (if configured)
send_sns_notification() {
    local subject="$1"
    local message="$2"
    
    if [ -n "$SNS_TOPIC_ARN" ] && command -v aws &>/dev/null; then
        aws sns publish \
            --topic-arn "$SNS_TOPIC_ARN" \
            --subject "$subject" \
            --message "$message" 2>/dev/null || true
    fi
}

# Backup to S3 (if configured)
backup_to_s3() {
    local source_path="$1"
    local backup_name="$2"
    
    if [ -n "$S3_BACKUP_BUCKET" ] && command -v aws &>/dev/null; then
        log_info "📦 Backing up to S3: $backup_name"
        aws s3 cp "$source_path" "$S3_BACKUP_BUCKET/$backup_name" --recursive 2>/dev/null || {
            log_info "⚠️ S3 backup failed (non-critical)"
        }
    fi
}

# =============================================================================
# LOGGING
# =============================================================================
ensure_log_dir() {
    local log_dir=$(dirname "$DEPLOY_LOG")
    if [ ! -d "$log_dir" ]; then
        sudo mkdir -p "$log_dir"
        sudo chown "$APP_USER:$WEB_USER" "$log_dir"
        sudo chmod 775 "$log_dir"
    fi
}

log() {
    local level="$1"; shift
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    local msg="[$timestamp] [$level] $*"
    echo "$msg" | tee -a "$DEPLOY_LOG"
    log_to_cloudwatch "$*" "$level"
}

log_info() { log "INFO" "$@"; }
log_error() { log "ERROR" "$@"; }
log_success() { log "SUCCESS" "$@"; }

# =============================================================================
# LOCK MANAGEMENT
# =============================================================================
acquire_lock() {
    if [ -f "$LOCK_FILE" ]; then
        local pid=$(cat "$LOCK_FILE" 2>/dev/null)
        if [ -n "$pid" ] && kill -0 "$pid" 2>/dev/null; then
            log_error "⚠️ Deployment already in progress (PID: $pid)"
            exit 1
        fi
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
    elif [ -f "/home/$APP_USER/.ssh/id_rsa" ]; then
        export GIT_SSH_COMMAND="ssh -i /home/$APP_USER/.ssh/id_rsa"
        log_info "🔑 Using default SSH key"
    fi
}

# =============================================================================
# PHP OPCACHE RESET (for PHP-FPM)
# =============================================================================
reset_opcache() {
    if [ "$ENABLE_OPCACHE_RESET" = "true" ]; then
        log_info "🔄 Resetting PHP OPcache..."
        
        # Method 1: PHP-FPM reload
        if systemctl is-active --quiet php-fpm 2>/dev/null; then
            sudo systemctl reload php-fpm || true
        elif systemctl is-active --quiet php7.4-fpm 2>/dev/null; then
            sudo systemctl reload php7.4-fpm || true
        elif systemctl is-active --quiet php8.1-fpm 2>/dev/null; then
            sudo systemctl reload php8.1-fpm || true
        fi
        
        # Method 2: Create opcache reset script
        local reset_script="$REPO_PATH/opcache_reset.php"
        if [ -L "$CURRENT_LINK" ]; then
            reset_script="$CURRENT_LINK/opcache_reset.php"
        fi
        
        echo '<?php opcache_reset(); echo "OK";' > "$reset_script"
        curl -s "http://localhost/opcache_reset.php" 2>/dev/null || true
        rm -f "$reset_script"
    fi
}

# =============================================================================
# FIX PERMISSIONS (AWS-specific)
# =============================================================================
fix_permissions() {
    local target_path="$1"
    log_info "🔐 Fixing permissions..."
    
    # Set ownership
    sudo chown -R "$APP_USER:$WEB_USER" "$target_path"
    
    # Directories: 755, Files: 644
    find "$target_path" -type d -exec chmod 755 {} \;
    find "$target_path" -type f -exec chmod 644 {} \;
    
    # Make scripts executable
    find "$target_path" -name "*.sh" -exec chmod 755 {} \;
    
    # Writable directories
    for dir in logs uploads cache temp; do
        if [ -d "$target_path/$dir" ]; then
            chmod -R 775 "$target_path/$dir"
        fi
    done
}

# =============================================================================
# SIMPLE DEPLOYMENT (Git Pull)
# =============================================================================
deploy_simple() {
    log_info "════════════════════════════════════════"
    log_info "📦 SIMPLE Deployment (git pull)"
    log_info "🖥️ Instance: $(get_instance_id)"
    log_info "════════════════════════════════════════"
    
    cd "$REPO_PATH" || {
        log_error "❌ Failed to enter repo path: $REPO_PATH"
        exit 1
    }
    
    setup_git_ssh
    
    # Fetch and reset
    log_info "📥 Fetching latest changes..."
    git fetch --all
    git checkout "$DEPLOY_BRANCH"
    
    local before_commit=$(git rev-parse --short HEAD)
    
    git reset --hard "$GIT_REMOTE/$DEPLOY_BRANCH"
    git clean -fd -q
    git pull --no-rebase "$GIT_REMOTE" "$DEPLOY_BRANCH"
    
    local after_commit=$(git rev-parse --short HEAD)
    local commit_msg=$(git log -1 --pretty=%B | head -n1)
    
    # Fix permissions
    fix_permissions "$REPO_PATH"
    
    # Reset OPcache
    reset_opcache
    
    log_success "✅ Deployed: $before_commit → $after_commit"
    log_success "   Message: $commit_msg"
}

# =============================================================================
# SYMLINK DEPLOYMENT (Zero-Downtime)
# =============================================================================
deploy_symlink() {
    log_info "════════════════════════════════════════"
    log_info "📦 SYMLINK Deployment (zero-downtime)"
    log_info "🖥️ Instance: $(get_instance_id)"
    log_info "════════════════════════════════════════"
    
    setup_git_ssh
    
    local git_url="${GIT_REMOTE_URL:-$(cd $REPO_PATH 2>/dev/null && git remote get-url origin)}"
    if [ -z "$git_url" ]; then
        log_error "Could not determine git remote URL"
        exit 1
    fi
    
    # Create release
    local release_name=$(date '+%Y%m%d_%H%M%S')
    local release_path="$RELEASES_DIR/$release_name"
    
    mkdir -p "$RELEASES_DIR"
    log_info "📁 Creating release: $release_name"
    
    # Clone
    git clone --branch "$DEPLOY_BRANCH" --depth 1 --single-branch "$git_url" "$release_path"
    
    # Link shared directories
    if [ -d "$SHARED_DIR" ]; then
        log_info "🔗 Linking shared directories..."
        for dir in logs uploads cache storage; do
            if [ -e "$SHARED_DIR/$dir" ]; then
                rm -rf "$release_path/$dir"
                ln -sfn "$SHARED_DIR/$dir" "$release_path/$dir"
                log_info "  ✓ $dir"
            fi
        done
        
        # Copy config files
        for config in .env database.php config.php; do
            if [ -f "$SHARED_DIR/$config" ]; then
                cp "$SHARED_DIR/$config" "$release_path/"
                log_info "  ✓ $config (copied)"
            fi
        done
    fi
    
    # Fix permissions
    fix_permissions "$release_path"
    
    # Save rollback point
    if [ -L "$CURRENT_LINK" ]; then
        local previous=$(readlink -f "$CURRENT_LINK")
        echo "$previous" > "$ROLLBACK_FILE"
        log_info "📦 Rollback point: $(basename $previous)"
        
        # Optional S3 backup
        backup_to_s3 "$previous" "backups/$(basename $previous).tar.gz"
    fi
    
    # Atomic symlink swap
    log_info "⚡ Atomic symlink swap..."
    local temp_link="${CURRENT_LINK}.new.$$"
    ln -sfn "$release_path" "$temp_link"
    mv -Tf "$temp_link" "$CURRENT_LINK"
    
    # Reset OPcache
    reset_opcache
    
    # Cleanup old releases
    cleanup_old_releases
    
    local after_commit=$(cd "$release_path" && git rev-parse --short HEAD)
    log_success "✅ Deployed: $release_name ($after_commit)"
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
        
        [ "$full_path" = "$current_release" ] && continue
        
        count=$((count + 1))
        if [ $count -ge $RELEASES_KEEP ]; then
            log_info "  Removing: $dir"
            rm -rf "$full_path"
        fi
    done
}

# =============================================================================
# MAIN
# =============================================================================
main() {
    local start_time=$(date +%s)
    local STATUS="success"
    
    ensure_log_dir
    
    log_info "════════════════════════════════════════"
    log_info "🚀 AWS EC2 Deployment Started"
    log_info "📋 Branch: $DEPLOY_BRANCH"
    log_info "🔧 Mode: $DEPLOY_MODE"
    log_info "🌍 Environment: $ENVIRONMENT"
    log_info "🖥️ Instance: $(get_instance_id)"
    log_info "════════════════════════════════════════"
    
    acquire_lock
    
    case "$DEPLOY_MODE" in
        simple)
            deploy_simple
            ;;
        symlink)
            deploy_symlink
            ;;
        *)
            log_error "Unknown deploy mode: $DEPLOY_MODE"
            STATUS="failed"
            ;;
    esac
    
    local duration=$(($(date +%s) - start_time))
    
    if [ "$STATUS" = "success" ]; then
        log_success "✅ Deployment completed in ${duration}s"
        send_sns_notification "✅ Deployment Success" "Branch: $DEPLOY_BRANCH, Duration: ${duration}s"
    else
        log_error "❌ Deployment failed"
        send_sns_notification "❌ Deployment Failed" "Branch: $DEPLOY_BRANCH"
    fi
    
    # Output JSON for webhook response
    echo "{\"status\":\"$STATUS\",\"duration\":\"${duration}s\",\"environment\":\"$ENVIRONMENT\",\"branch\":\"$DEPLOY_BRANCH\",\"instance\":\"$(get_instance_id)\"}"
}

main "$@"
