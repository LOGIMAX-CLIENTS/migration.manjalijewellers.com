#!/bin/bash
# =============================================================================
# INSTANT ROLLBACK SCRIPT
# Rollback to previous release in under 1 second
# =============================================================================

set -e

# =============================================================================
# CONFIGURATION
# =============================================================================
BASE_DIR="${BASE_DIR:-/var/www}"
RELEASES_DIR="${RELEASES_DIR:-$BASE_DIR/releases}"
CURRENT_LINK="${CURRENT_LINK:-$BASE_DIR/current}"
ROLLBACK_FILE="${ROLLBACK_FILE:-$BASE_DIR/.last_release}"
LOG_FILE="${LOG_FILE:-$BASE_DIR/shared/logs/rollback.log}"

# =============================================================================
# PERMISSION SELF-HEALING
# =============================================================================

ensure_dir_writable() {
    local dir="$1"
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir" 2>/dev/null || sudo mkdir -p "$dir" 2>/dev/null
    fi
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

# Fix base directory permissions before anything else
ensure_dir_writable "$BASE_DIR"
ensure_dir_writable "$(dirname "$LOG_FILE")"
if [ -f "$LOG_FILE" ]; then
    ensure_file_writable "$LOG_FILE"
fi

# =============================================================================
# LOGGING
# =============================================================================
log() {
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] $*" | tee -a "$LOG_FILE"
}

# =============================================================================
# SHOW AVAILABLE RELEASES
# =============================================================================
list_releases() {
    echo ""
    echo "Available Releases:"
    echo "==================="
    
    local current=""
    if [ -L "$CURRENT_LINK" ]; then
        current=$(readlink -f "$CURRENT_LINK")
    fi
    
    local i=1
    for dir in $(ls -dt "$RELEASES_DIR"/*/); do
        local release=$(basename "$dir")
        local commit=$(cd "$dir" && git rev-parse --short HEAD 2>/dev/null || echo "unknown")
        local date=$(echo "$release" | sed 's/_/ /' | sed 's/\([0-9]\{4\}\)\([0-9]\{2\}\)\([0-9]\{2\}\)/\1-\2-\3/')
        
        if [ "$dir" = "$current/" ]; then
            echo "  $i) $release ($commit) ← CURRENT"
        else
            echo "  $i) $release ($commit)"
        fi
        i=$((i + 1))
    done
    echo ""
}

# =============================================================================
# ROLLBACK TO PREVIOUS
# =============================================================================
rollback_previous() {
    if [ ! -f "$ROLLBACK_FILE" ]; then
        echo "❌ No previous release found"
        echo "   Rollback file not found: $ROLLBACK_FILE"
        exit 1
    fi
    
    local previous=$(cat "$ROLLBACK_FILE")
    
    if [ ! -d "$previous" ]; then
        echo "❌ Previous release directory not found: $previous"
        exit 1
    fi
    
    rollback_to "$previous"
}

# =============================================================================
# ROLLBACK TO SPECIFIC RELEASE
# =============================================================================
rollback_to() {
    local target="$1"
    
    # If just a name, prepend releases dir
    if [[ "$target" != /* ]]; then
        target="$RELEASES_DIR/$target"
    fi
    
    if [ ! -d "$target" ]; then
        echo "❌ Release not found: $target"
        exit 1
    fi
    
    local release_name=$(basename "$target")
    local commit=$(cd "$target" && git rev-parse --short HEAD 2>/dev/null || echo "unknown")
    
    echo ""
    echo "🔄 Rolling back to: $release_name (commit: $commit)"
    echo ""
    
    # Save current for potential re-rollback
    if [ -L "$CURRENT_LINK" ]; then
        local current=$(readlink -f "$CURRENT_LINK")
        echo "$current" > "$ROLLBACK_FILE"
    fi
    
    # Atomic symlink swap with sudo fallback
    local temp_link="${CURRENT_LINK}.rollback.$$"
    if ln -sfn "$target" "$temp_link" 2>/dev/null; then
        mv -Tf "$temp_link" "$CURRENT_LINK" 2>/dev/null || \
            sudo mv -Tf "$temp_link" "$CURRENT_LINK" 2>/dev/null
    else
        sudo ln -sfn "$target" "$temp_link" 2>/dev/null && \
            sudo mv -Tf "$temp_link" "$CURRENT_LINK" 2>/dev/null || \
            sudo ln -sfn "$target" "$CURRENT_LINK" 2>/dev/null
    fi
    
    log "ROLLBACK: Switched to $release_name ($commit)"
    
    echo "✅ Rollback complete!"
    echo ""
    echo "   Current: $(readlink -f $CURRENT_LINK)"
    echo ""
}

# =============================================================================
# MAIN
# =============================================================================
case "${1:-}" in
    list|ls|-l)
        list_releases
        ;;
    previous|prev|-p)
        rollback_previous
        ;;
    "")
        echo ""
        echo "Usage: rollback.sh [command]"
        echo ""
        echo "Commands:"
        echo "  list, ls, -l     List available releases"
        echo "  previous, -p     Rollback to previous release"
        echo "  <release_name>   Rollback to specific release"
        echo ""
        echo "Examples:"
        echo "  ./rollback.sh list"
        echo "  ./rollback.sh previous"
        echo "  ./rollback.sh 20260108_190000"
        echo ""
        list_releases
        ;;
    *)
        rollback_to "$1"
        ;;
esac
