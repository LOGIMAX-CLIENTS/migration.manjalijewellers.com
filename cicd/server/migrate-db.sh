#!/bin/bash
# =============================================================================
# MIGRATE-DB.SH — Initial Client Database Migration
#
# One-time script to clone a source database to a new client.
# Used during client onboarding to create production and staging databases.
#
# Usage:
#   bash migrate-db.sh [options]
#
# Required:
#   --source-host <host>     Source RDS endpoint
#   --source-db <db>         Source database name
#   --source-user <user>     Source DB user
#   --source-pass <pass>     Source DB password
#   --target-host <host>     Target RDS endpoint
#   --target-db-prod <db>    Production database name
#   --target-db-staging <db> Staging database name
#   --target-user <user>     Target DB user
#   --target-pass <pass>     Target DB password
#
# Optional:
#   --dry-run                Show commands without executing
#   --skip-staging           Only create production database
#   --dump-file <path>       Use existing dump file instead of exporting
#   --migrations-dir <path>  Path to database/migrations/ (to seed tracking)
#
# Example:
#   bash migrate-db.sh \
#     --source-host source-rds.example.com \
#     --source-db etail_v3_source \
#     --source-user admin \
#     --source-pass 'secret' \
#     --target-host target-rds.example.com \
#     --target-db-prod etail_newclient \
#     --target-db-staging etail_newclient_staging \
#     --target-user admin \
#     --target-pass 'secret'
# =============================================================================

set -o pipefail

# =============================================================================
# ARGUMENTS
# =============================================================================

SOURCE_HOST=""
SOURCE_DB=""
SOURCE_USER=""
SOURCE_PASS=""
TARGET_HOST=""
TARGET_DB_PROD=""
TARGET_DB_STAGING=""
TARGET_USER=""
TARGET_PASS=""
DRY_RUN=false
SKIP_STAGING=false
DUMP_FILE=""
MIGRATIONS_DIR=""

while [[ $# -gt 0 ]]; do
    case "$1" in
        --source-host)       SOURCE_HOST="$2"; shift 2 ;;
        --source-db)         SOURCE_DB="$2"; shift 2 ;;
        --source-user)       SOURCE_USER="$2"; shift 2 ;;
        --source-pass)       SOURCE_PASS="$2"; shift 2 ;;
        --target-host)       TARGET_HOST="$2"; shift 2 ;;
        --target-db-prod)    TARGET_DB_PROD="$2"; shift 2 ;;
        --target-db-staging) TARGET_DB_STAGING="$2"; shift 2 ;;
        --target-user)       TARGET_USER="$2"; shift 2 ;;
        --target-pass)       TARGET_PASS="$2"; shift 2 ;;
        --dry-run)           DRY_RUN=true; shift ;;
        --skip-staging)      SKIP_STAGING=true; shift ;;
        --dump-file)         DUMP_FILE="$2"; shift 2 ;;
        --migrations-dir)    MIGRATIONS_DIR="$2"; shift 2 ;;
        *)                   echo "Unknown option: $1"; exit 1 ;;
    esac
done

# =============================================================================
# VALIDATE
# =============================================================================

ERRORS=()
[ -z "$SOURCE_HOST" ] && [ -z "$DUMP_FILE" ] && ERRORS+=("--source-host is required (or use --dump-file)")
[ -z "$SOURCE_DB" ] && [ -z "$DUMP_FILE" ] && ERRORS+=("--source-db is required (or use --dump-file)")
[ -z "$SOURCE_USER" ] && [ -z "$DUMP_FILE" ] && ERRORS+=("--source-user is required (or use --dump-file)")
[ -z "$SOURCE_PASS" ] && [ -z "$DUMP_FILE" ] && ERRORS+=("--source-pass is required (or use --dump-file)")
[ -z "$TARGET_HOST" ] && ERRORS+=("--target-host is required")
[ -z "$TARGET_DB_PROD" ] && ERRORS+=("--target-db-prod is required")
[ -z "$TARGET_USER" ] && ERRORS+=("--target-user is required")
[ -z "$TARGET_PASS" ] && ERRORS+=("--target-pass is required")
[ "$SKIP_STAGING" = false ] && [ -z "$TARGET_DB_STAGING" ] && ERRORS+=("--target-db-staging is required (or use --skip-staging)")

if [ ${#ERRORS[@]} -gt 0 ]; then
    echo "❌ Validation errors:"
    for err in "${ERRORS[@]}"; do
        echo "   - $err"
    done
    exit 1
fi

# =============================================================================
# LOGGING
# =============================================================================

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

run_or_dry() {
    if [ "$DRY_RUN" = true ]; then
        log "  [DRY-RUN] $1"
    else
        eval "$1"
    fi
}

# =============================================================================
# MAIN
# =============================================================================

START_TIME=$(date +%s)

log "═══════════════════════════════════════════════════════════"
log "🗄  Database Migration: Initial Client Setup"
log "═══════════════════════════════════════════════════════════"
if [ -n "$DUMP_FILE" ]; then
    log "📦 Source: dump file ($DUMP_FILE)"
else
    log "📦 Source: ${SOURCE_DB}@${SOURCE_HOST}"
fi
log "🎯 Target Prod: ${TARGET_DB_PROD}@${TARGET_HOST}"
[ "$SKIP_STAGING" = false ] && log "🎯 Target Staging: ${TARGET_DB_STAGING}@${TARGET_HOST}"
[ "$DRY_RUN" = true ] && log "⚠️  DRY RUN — no changes will be made"

SOURCE_MYSQL="mysql -h${SOURCE_HOST} -u${SOURCE_USER} -p${SOURCE_PASS}"
TARGET_MYSQL="mysql -h${TARGET_HOST} -u${TARGET_USER} -p${TARGET_PASS}"

# =============================================================================
# STEP 1: Export source database
# =============================================================================

if [ -n "$DUMP_FILE" ]; then
    if [ ! -f "$DUMP_FILE" ]; then
        log "❌ Dump file not found: $DUMP_FILE"
        exit 1
    fi
    log "📦 Using existing dump: $DUMP_FILE"
    CLEANED_DUMP="$DUMP_FILE"

    # Check if it needs DEFINER stripping
    if grep -q "DEFINER=" "$DUMP_FILE" 2>/dev/null; then
        log "🔧 Stripping DEFINER clauses from dump..."
        CLEANED_DUMP="/tmp/migrate_db_cleaned_$(date +%s).sql"
        sed 's/\sDEFINER=[^ ]* / /g' "$DUMP_FILE" > "$CLEANED_DUMP"
        log "✅ Cleaned dump: $CLEANED_DUMP"
    fi
else
    log ""
    log "━━━ Step 1: Export source database ━━━"

    DUMP_FILE="/tmp/migrate_db_${SOURCE_DB}_$(date +%Y%m%d_%H%M%S).sql"

    log "📤 Exporting ${SOURCE_DB}..."
    DUMP_CMD="mysqldump -h${SOURCE_HOST} -u${SOURCE_USER} -p${SOURCE_PASS} ${SOURCE_DB} \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --set-gtid-purged=OFF \
        --column-statistics=0 \
        2>/dev/null > ${DUMP_FILE}"

    run_or_dry "$DUMP_CMD"

    if [ "$DRY_RUN" = false ]; then
        if [ ! -s "$DUMP_FILE" ]; then
            log "❌ Export failed — dump file is empty"
            exit 1
        fi
        DUMP_SIZE=$(du -h "$DUMP_FILE" | cut -f1)
        log "✅ Export complete: ${DUMP_SIZE}"
    fi

    # Strip DEFINER clauses
    log "🔧 Stripping DEFINER clauses..."
    CLEANED_DUMP="/tmp/migrate_db_cleaned_$(date +%s).sql"

    if [ "$DRY_RUN" = false ]; then
        sed 's/\sDEFINER=[^ ]* / /g' "$DUMP_FILE" > "$CLEANED_DUMP"
        log "✅ DEFINER clauses stripped"
    else
        CLEANED_DUMP="$DUMP_FILE"
        log "  [DRY-RUN] sed 's/DEFINER=.../ /g' $DUMP_FILE > $CLEANED_DUMP"
    fi
fi

# =============================================================================
# STEP 2: Create target databases
# =============================================================================

log ""
log "━━━ Step 2: Create target databases ━━━"

# Production DB
log "📁 Creating production database: ${TARGET_DB_PROD}"
run_or_dry "$TARGET_MYSQL -e \"CREATE DATABASE IF NOT EXISTS \\\`${TARGET_DB_PROD}\\\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\" 2>/dev/null"

# Staging DB
if [ "$SKIP_STAGING" = false ]; then
    log "📁 Creating staging database: ${TARGET_DB_STAGING}"
    run_or_dry "$TARGET_MYSQL -e \"CREATE DATABASE IF NOT EXISTS \\\`${TARGET_DB_STAGING}\\\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\" 2>/dev/null"
fi

# =============================================================================
# STEP 3: Import into production
# =============================================================================

log ""
log "━━━ Step 3: Import into production database ━━━"

log "📥 Importing into ${TARGET_DB_PROD}..."
run_or_dry "$TARGET_MYSQL ${TARGET_DB_PROD} < ${CLEANED_DUMP} 2>/dev/null"

if [ "$DRY_RUN" = false ]; then
    # Verify table count
    SOURCE_TABLES=$($SOURCE_MYSQL -N -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='${SOURCE_DB}';" 2>/dev/null)
    TARGET_TABLES=$($TARGET_MYSQL -N -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='${TARGET_DB_PROD}';" 2>/dev/null)

    if [ "$SOURCE_TABLES" = "$TARGET_TABLES" ]; then
        log "✅ Import verified: ${TARGET_TABLES} tables (matches source)"
    else
        log "⚠️  Table count mismatch: source=${SOURCE_TABLES:-?} target=${TARGET_TABLES:-?}"
        log "   This may be expected if source has temporary tables"
    fi
fi

# =============================================================================
# STEP 4: Import into staging
# =============================================================================

if [ "$SKIP_STAGING" = false ]; then
    log ""
    log "━━━ Step 4: Import into staging database ━━━"

    log "📥 Importing into ${TARGET_DB_STAGING}..."
    run_or_dry "$TARGET_MYSQL ${TARGET_DB_STAGING} < ${CLEANED_DUMP} 2>/dev/null"

    if [ "$DRY_RUN" = false ]; then
        STAGING_TABLES=$($TARGET_MYSQL -N -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='${TARGET_DB_STAGING}';" 2>/dev/null)
        log "✅ Staging import: ${STAGING_TABLES:-?} tables"
    fi
fi

# =============================================================================
# STEP 5: Create schema_migrations tracking table
# =============================================================================

log ""
log "━━━ Step 5: Create migration tracking ━━━"

CREATE_TRACKING="CREATE TABLE IF NOT EXISTS schema_migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL UNIQUE,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    environment VARCHAR(20) DEFAULT 'production'
) ENGINE=InnoDB;"

run_or_dry "$TARGET_MYSQL ${TARGET_DB_PROD} -e \"${CREATE_TRACKING}\" 2>/dev/null"

if [ "$SKIP_STAGING" = false ]; then
    run_or_dry "$TARGET_MYSQL ${TARGET_DB_STAGING} -e \"${CREATE_TRACKING}\" 2>/dev/null"
fi

# Seed with all existing migration files (dump already has these applied)
if [ -n "$MIGRATIONS_DIR" ] && [ -d "$MIGRATIONS_DIR" ]; then
    MIGRATION_FILES=$(find "$MIGRATIONS_DIR" -maxdepth 1 -name "*.sql" -type f | sort)
    SEED_COUNT=0

    for file in $MIGRATION_FILES; do
        fname=$(basename "$file")
        SEED_SQL="INSERT IGNORE INTO schema_migrations (migration, environment) VALUES ('${fname}', 'production');"
        run_or_dry "$TARGET_MYSQL ${TARGET_DB_PROD} -e \"${SEED_SQL}\" 2>/dev/null"

        if [ "$SKIP_STAGING" = false ]; then
            STAGING_SEED="INSERT IGNORE INTO schema_migrations (migration, environment) VALUES ('${fname}', 'staging');"
            run_or_dry "$TARGET_MYSQL ${TARGET_DB_STAGING} -e \"${STAGING_SEED}\" 2>/dev/null"
        fi
        SEED_COUNT=$((SEED_COUNT + 1))
    done

    log "✅ Seeded ${SEED_COUNT} migration(s) as already-applied"
else
    log "ℹ️  No migrations-dir provided — skipping seed"
    log "   Tip: use --migrations-dir path/to/database/migrations/ to seed tracking"
fi

# =============================================================================
# STEP 6: Cleanup
# =============================================================================

log ""
log "━━━ Step 6: Cleanup ━━━"

if [ "$DRY_RUN" = false ] && [ -f "$CLEANED_DUMP" ] && [ "$CLEANED_DUMP" != "$DUMP_FILE" ]; then
    rm -f "$CLEANED_DUMP"
    log "✅ Cleaned up temporary files"
fi

# Keep original dump for reference
if [ -f "$DUMP_FILE" ] && [ -z "${1:-}" ]; then
    log "📦 Original dump retained: $DUMP_FILE"
fi

# =============================================================================
# SUMMARY
# =============================================================================

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

log ""
log "═══════════════════════════════════════════════════════════"
log "✅ Database migration complete"
log "═══════════════════════════════════════════════════════════"
log "⏱️  Duration: ${DURATION}s"
log ""
log "Remaining steps:"
log "  1. Update shared/config/global_configs.php with target DB credentials"
if [ "$SKIP_STAGING" = false ]; then
    log "  2. Create shared/config/global_configs_staging.php for staging DB"
fi
log "  3. Verify application loads correctly on the new client"
