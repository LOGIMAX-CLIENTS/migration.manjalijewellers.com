#!/bin/bash
# =============================================================================
# RUN-MIGRATIONS.SH — Schema Migration Runner for Retail ERP
#
# Applies pending SQL migrations from database/migrations/ to the client DB.
# Tracks applied migrations in `schema_migrations` table.
#
# Features:
#   - Auto-detects DB credentials from global_configs.php (no env vars needed)
#   - Separate staging/production DB support
#   - --dry-run mode (list pending without applying)
#   - JSON output for DevOps tool integration
#   - Auto-backup before production migrations
#   - Exit codes: 0=success, 1=error, 2=no pending migrations
#
# Usage:
#   bash run-migrations.sh [options]
#
# Options:
#   --config <path>       Path to global_configs.php (auto-detected if not set)
#   --migrations <path>   Path to migrations directory (auto-detected if not set)
#   --env <name>          Environment: staging|production (default: production)
#   --dry-run             List pending migrations without applying
#   --json                Output results as JSON (for DevOps callback)
#   --backup              Force DB backup before applying (default for production)
#   --no-backup           Skip DB backup
#
# Exit codes:
#   0 = migrations applied successfully
#   1 = error (parse failure, SQL error, etc.)
#   2 = no pending migrations (everything up to date)
# =============================================================================

set -o pipefail

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

# =============================================================================
# DEFAULTS
# =============================================================================

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
CONFIG_FILE=""
MIGRATIONS_DIR=""
ENVIRONMENT="production"
DRY_RUN=false
JSON_OUTPUT=false
DO_BACKUP=""  # auto-decide based on environment

# =============================================================================
# PARSE ARGUMENTS
# =============================================================================

while [[ $# -gt 0 ]]; do
    case "$1" in
        --config)      CONFIG_FILE="$2"; shift 2 ;;
        --migrations)  MIGRATIONS_DIR="$2"; shift 2 ;;
        --env)         ENVIRONMENT="$2"; shift 2 ;;
        --dry-run)     DRY_RUN=true; shift ;;
        --json)        JSON_OUTPUT=true; shift ;;
        --backup)      DO_BACKUP="true"; shift ;;
        --no-backup)   DO_BACKUP="false"; shift ;;
        *)             echo "Unknown option: $1"; exit 1 ;;
    esac
done

# =============================================================================
# AUTO-DETECT PATHS
# =============================================================================

# Auto-detect config file from shared directory
if [ -z "$CONFIG_FILE" ]; then
    # Walk up from script location to find shared/config/
    check_dir="$SCRIPT_DIR"
    for i in $(seq 1 6); do
        if [ "$ENVIRONMENT" = "staging" ]; then
            candidate="${check_dir}/shared/config/global_configs_staging.php"
            if [ -f "$candidate" ]; then
                CONFIG_FILE="$candidate"
                break
            fi
            # Fallback: try prod's shared config with staging suffix
            candidate="$(dirname "$check_dir")/prod/shared/config/global_configs_staging.php"
            if [ -f "$candidate" ]; then
                CONFIG_FILE="$candidate"
                break
            fi
        fi
        # Production or fallback
        candidate="${check_dir}/shared/config/global_configs.php"
        if [ -f "$candidate" ]; then
            CONFIG_FILE="$candidate"
            break
        fi
        check_dir="$(dirname "$check_dir")"
    done
fi

# Auto-detect migrations directory
if [ -z "$MIGRATIONS_DIR" ]; then
    # Try relative to script: ../../database/migrations/
    candidate="$(realpath "${SCRIPT_DIR}/../database/migrations" 2>/dev/null || echo "")"
    if [ -d "$candidate" ]; then
        MIGRATIONS_DIR="$candidate"
    fi

    # Try from repo root (if we're inside a release or staging clone)
    if [ -z "$MIGRATIONS_DIR" ]; then
        check_dir="$SCRIPT_DIR"
        for i in $(seq 1 6); do
            candidate="${check_dir}/database/migrations"
            if [ -d "$candidate" ]; then
                MIGRATIONS_DIR="$candidate"
                break
            fi
            check_dir="$(dirname "$check_dir")"
        done
    fi
fi

# =============================================================================
# VALIDATE
# =============================================================================

if [ -z "$CONFIG_FILE" ] || [ ! -f "$CONFIG_FILE" ]; then
    echo "❌ Config file not found: ${CONFIG_FILE:-<not set>}"
    echo "   Use --config <path> to specify global_configs.php location"
    exit 1
fi

if [ -z "$MIGRATIONS_DIR" ] || [ ! -d "$MIGRATIONS_DIR" ]; then
    echo "❌ Migrations directory not found: ${MIGRATIONS_DIR:-<not set>}"
    echo "   Use --migrations <path> to specify directory"
    exit 1
fi

# =============================================================================
# PARSE PHP CONFIG — Extract DB credentials from global_configs.php
# =============================================================================

parse_php_config() {
    local config_file="$1"

    # Use PHP itself to extract DB credentials — handles both quote styles
    # and any whitespace/formatting variations in global_configs.php
    if command -v php &>/dev/null; then
        DB_HOST=$(php -r "include '$config_file'; echo Globals::\$hostname;" 2>/dev/null)
        DB_USER=$(php -r "include '$config_file'; echo Globals::\$username;" 2>/dev/null)
        DB_PASS=$(php -r "include '$config_file'; echo Globals::\$password;" 2>/dev/null)
        DB_NAME=$(php -r "include '$config_file'; echo Globals::\$database;" 2>/dev/null)
    else
        # Fallback: grep with support for both single and double quotes
        DB_HOST=$(sed -n 's/.*\$hostname\s*=\s*["\x27]\([^"\x27]*\)["\x27].*/\1/p' "$config_file" | head -1)
        DB_USER=$(sed -n 's/.*\$username\s*=\s*["\x27]\([^"\x27]*\)["\x27].*/\1/p' "$config_file" | head -1)
        DB_PASS=$(sed -n 's/.*\$password\s*=\s*["\x27]\([^"\x27]*\)["\x27].*/\1/p' "$config_file" | head -1)
        DB_NAME=$(sed -n 's/.*\$database\s*=\s*["\x27]\([^"\x27]*\)["\x27].*/\1/p' "$config_file" | head -1)
    fi
}

parse_php_config "$CONFIG_FILE"

if [ -z "$DB_HOST" ] || [ -z "$DB_USER" ] || [ -z "$DB_NAME" ]; then
    echo "❌ Failed to parse DB credentials from: $CONFIG_FILE"
    echo "   Expected: public static \$hostname, \$username, \$password, \$database"
    exit 1
fi

# Resolve full paths for mysql/mysqldump (www-data may have limited PATH)
MYSQL_BIN=$(which mysql 2>/dev/null || echo "/usr/bin/mysql")
MYSQLDUMP_BIN=$(which mysqldump 2>/dev/null || echo "/usr/bin/mysqldump")

MYSQL="${MYSQL_BIN} -h${DB_HOST} -u${DB_USER} -p${DB_PASS} ${DB_NAME}"
MYSQLDUMP="${MYSQLDUMP_BIN} -h${DB_HOST} -u${DB_USER} -p${DB_PASS} ${DB_NAME}"

# =============================================================================
# LOGGING
# =============================================================================

log() {
    if [ "$JSON_OUTPUT" = false ]; then
        echo "$1"
    fi
}

# =============================================================================
# ENSURE TRACKING TABLE
# =============================================================================

log "🗄  Connecting to ${DB_NAME}@${DB_HOST} (${ENVIRONMENT})"

$MYSQL -e "
CREATE TABLE IF NOT EXISTS schema_migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL UNIQUE,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    environment VARCHAR(20) DEFAULT '${ENVIRONMENT}'
) ENGINE=InnoDB;
" 2>/dev/null

if [ $? -ne 0 ]; then
    echo "❌ Cannot connect to database or create tracking table"
    exit 1
fi

# =============================================================================
# FIND PENDING MIGRATIONS
# =============================================================================

# Get list of applied migrations
APPLIED=$($MYSQL -N -e "SELECT migration FROM schema_migrations ORDER BY migration;" 2>/dev/null)

# Get list of migration files
MIGRATION_FILES=$(find "$MIGRATIONS_DIR" -maxdepth 1 -name "*.sql" -type f | sort)

PENDING=()
for file in $MIGRATION_FILES; do
    fname=$(basename "$file")
    if ! echo "$APPLIED" | grep -qx "$fname"; then
        PENDING+=("$file")
    fi
done

PENDING_COUNT=${#PENDING[@]}

# =============================================================================
# NO PENDING MIGRATIONS
# =============================================================================

if [ "$PENDING_COUNT" -eq 0 ]; then
    if [ "$JSON_OUTPUT" = true ]; then
        echo "{\"status\":\"up_to_date\",\"applied\":0,\"pending\":0,\"environment\":\"${ENVIRONMENT}\"}"
    else
        log "✅ No pending migrations — database is up to date"
    fi
    exit 2
fi

# =============================================================================
# DRY RUN
# =============================================================================

if [ "$DRY_RUN" = true ]; then
    log "📋 Pending migrations (${PENDING_COUNT}):"
    for file in "${PENDING[@]}"; do
        log "  ➡  $(basename "$file")"
    done

    if [ "$JSON_OUTPUT" = true ]; then
        pending_list=$(printf '%s\n' "${PENDING[@]}" | while read f; do echo "\"$(basename "$f")\""; done | paste -sd,)
        echo "{\"status\":\"dry_run\",\"pending\":${PENDING_COUNT},\"migrations\":[${pending_list}],\"environment\":\"${ENVIRONMENT}\"}"
    fi
    exit 0
fi

# =============================================================================
# BACKUP (production only, unless overridden)
# =============================================================================

if [ "$DO_BACKUP" = "" ]; then
    # Auto-decide: backup for production, skip for staging
    if [ "$ENVIRONMENT" = "production" ]; then
        DO_BACKUP="true"
    else
        DO_BACKUP="false"
    fi
fi

if [ "$DO_BACKUP" = "true" ]; then
    BACKUP_DIR="$(dirname "$CONFIG_FILE")/../../backups"
    ensure_dir_writable "$BACKUP_DIR"
    if [ ! -d "$BACKUP_DIR" ] || [ ! -w "$BACKUP_DIR" ]; then
        if [ "$JSON_OUTPUT" = true ]; then
            echo "{\"status\":\"error\",\"error\":\"Backup directory not writable: ${BACKUP_DIR}\",\"environment\":\"${ENVIRONMENT}\"}"
        else
            echo "❌ Backup directory not writable: ${BACKUP_DIR}"
        fi
        exit 1
    fi
    BACKUP_FILE="${BACKUP_DIR}/pre_migration_$(date +%Y%m%d_%H%M%S).sql.gz"
    log "📦 Backing up ${DB_NAME} → ${BACKUP_FILE}"
    BACKUP_ERR=$($MYSQLDUMP --single-transaction --routines --triggers --skip-lock-tables --no-tablespaces --set-gtid-purged=OFF 2>&1 | gzip > "$BACKUP_FILE"; echo "${PIPESTATUS[0]}")
    if [ "$BACKUP_ERR" != "0" ] || [ ! -s "$BACKUP_FILE" ]; then
        if [ "$JSON_OUTPUT" = true ]; then
            echo "{\"status\":\"error\",\"error\":\"Backup failed — mysqldump exit code ${BACKUP_ERR}\",\"environment\":\"${ENVIRONMENT}\"}"
        else
            echo "❌ Backup failed — aborting migrations"
        fi
        rm -f "$BACKUP_FILE"
        exit 1
    fi
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log "✅ Backup complete (${BACKUP_SIZE})"
fi

# =============================================================================
# APPLY MIGRATIONS
# =============================================================================

log "🔄 Applying ${PENDING_COUNT} migration(s) to ${DB_NAME} (${ENVIRONMENT})..."

APPLIED_COUNT=0
FAILED_COUNT=0
FAILED_MIGRATION=""
RESULTS=()

for file in "${PENDING[@]}"; do
    fname=$(basename "$file")
    log "  ➡  Applying: ${fname}"

    START_MS=$(($(date +%s%N) / 1000000))

    # Extract only the -- UP section (everything between -- UP and -- DOWN, or everything after -- UP if no DOWN)
    # Strip CRLF (\r) — Windows line endings cause MySQL parse errors on Linux
    if grep -q '^-- DOWN' "$file"; then
        # Has both UP and DOWN markers — extract between them, strip both marker lines
        UP_SQL=$(sed -n '/^-- UP/,/^-- DOWN/p' "$file" | sed '1d;$d' | tr -d '\r')
    elif grep -q '^-- UP' "$file"; then
        # Has UP marker but no DOWN — everything after -- UP line
        UP_SQL=$(sed -n '/^-- UP/,$p' "$file" | sed '1d' | tr -d '\r')
    else
        # No markers at all — use entire file (strip comments and CRLF)
        UP_SQL=$(cat "$file" | tr -d '\r')
    fi

    # Apply the migration via temp file + redirect (echo pipe breaks multi-statement SQL)
    TMPFILE=$(mktemp /tmp/migration_XXXXXX.sql)
    echo "$UP_SQL" > "$TMPFILE"
    ERROR_MSG=$($MYSQL < "$TMPFILE" 2>&1)
    STATUS=$?
    rm -f "$TMPFILE"

    # Tolerate "Duplicate column name" (error 1060) — makes ADD COLUMN idempotent
    if [ $STATUS -ne 0 ] && echo "$ERROR_MSG" | grep -qi "duplicate column"; then
        # Check if ALL errors are duplicate-column; if so, treat as success
        NON_DUP_ERRORS=$(echo "$ERROR_MSG" | grep -i "^ERROR" | grep -vi "duplicate column")
        if [ -z "$NON_DUP_ERRORS" ]; then
            log "  ⚠️  Skipped duplicate columns (already exist)"
            STATUS=0
            ERROR_MSG=""
        fi
    fi

    END_MS=$(($(date +%s%N) / 1000000))
    DURATION_MS=$((END_MS - START_MS))

    if [ $STATUS -ne 0 ]; then
        FAILED_COUNT=$((FAILED_COUNT + 1))
        FAILED_MIGRATION="$fname"
        log "  ❌ FAILED: ${fname}"
        log "     Error: ${ERROR_MSG}"
        RESULTS+=("{\"migration\":\"${fname}\",\"status\":\"failed\",\"duration_ms\":${DURATION_MS},\"error\":\"$(echo "$ERROR_MSG" | tr '"' "'" | tr '\n' ' ')\"}")
        break  # Stop on first failure
    fi

    # Record in tracking table
    $MYSQL -e "INSERT INTO schema_migrations (migration, environment) VALUES ('${fname}', '${ENVIRONMENT}');" 2>/dev/null

    APPLIED_COUNT=$((APPLIED_COUNT + 1))
    log "  ✅ Applied: ${fname} (${DURATION_MS}ms)"
    RESULTS+=("{\"migration\":\"${fname}\",\"status\":\"applied\",\"duration_ms\":${DURATION_MS}}")
done

# =============================================================================
# RESULTS
# =============================================================================

if [ "$FAILED_COUNT" -gt 0 ]; then
    OVERALL_STATUS="failed"
    EXIT_CODE=1
else
    OVERALL_STATUS="success"
    EXIT_CODE=0
fi

if [ "$JSON_OUTPUT" = true ]; then
    results_json=$(printf '%s,' "${RESULTS[@]}" | sed 's/,$//')
    echo "{\"status\":\"${OVERALL_STATUS}\",\"applied\":${APPLIED_COUNT},\"failed\":${FAILED_COUNT},\"pending\":$((PENDING_COUNT - APPLIED_COUNT - FAILED_COUNT)),\"failed_migration\":\"${FAILED_MIGRATION}\",\"environment\":\"${ENVIRONMENT}\",\"database\":\"${DB_NAME}\",\"migrations\":[${results_json}]}"
else
    log ""
    if [ "$FAILED_COUNT" -gt 0 ]; then
        log "❌ Migration failed: ${FAILED_MIGRATION}"
        log "   Applied ${APPLIED_COUNT}/${PENDING_COUNT} migration(s) before failure"
        log "   Remaining: $((PENDING_COUNT - APPLIED_COUNT - FAILED_COUNT)) migration(s) skipped"
    else
        log "✅ All ${APPLIED_COUNT} migration(s) applied successfully"
    fi
fi

exit $EXIT_CODE
