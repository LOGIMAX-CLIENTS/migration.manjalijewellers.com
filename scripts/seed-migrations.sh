#!/bin/bash
# =============================================================================
# SEED EXISTING MIGRATIONS — Dynamic version
# =============================================================================
# Marks ALL current migration files as "already applied" in a client's database.
# Use this when onboarding a new client whose database was cloned/imported from
# an existing client (so migrations don't try to re-apply existing schema changes).
#
# For FRESH databases (built from DDL), do NOT run this — let run-migrations.sh
# apply all migrations from scratch.
#
# USAGE:
#   bash seed-migrations.sh --config <global_configs.php> [--env <environment>]
#
# EXAMPLES:
#   # Onboarding a new client (cloned DB)
#   bash seed-migrations.sh --config /var/www/newclient/prod/shared/config/global_configs.php --env production
#
#   # Seeding staging
#   bash seed-migrations.sh --config /var/www/newclient/staging/global_configs.php --env staging
#
# OPTIONS:
#   --config <path>     Path to global_configs.php (REQUIRED)
#   --env <name>        Environment name to record (default: production)
#   --migrations <dir>  Path to migrations directory (auto-detected if not set)
#   --dry-run           Show what would be seeded without applying
# =============================================================================

set -o pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
CONFIG_FILE=""
ENVIRONMENT="production"
MIGRATIONS_DIR=""
DRY_RUN=false

# Parse arguments
while [[ $# -gt 0 ]]; do
    case "$1" in
        --config)      CONFIG_FILE="$2"; shift 2 ;;
        --env)         ENVIRONMENT="$2"; shift 2 ;;
        --migrations)  MIGRATIONS_DIR="$2"; shift 2 ;;
        --dry-run)     DRY_RUN=true; shift ;;
        *)             echo "Unknown option: $1"; exit 1 ;;
    esac
done

# Auto-detect migrations directory
if [ -z "$MIGRATIONS_DIR" ]; then
    candidate="$(realpath "${SCRIPT_DIR}/../database/migrations" 2>/dev/null || echo "")"
    if [ -d "$candidate" ]; then
        MIGRATIONS_DIR="$candidate"
    fi
fi

# Validate
if [ -z "$CONFIG_FILE" ] || [ ! -f "$CONFIG_FILE" ]; then
    echo "❌ Config file not found: ${CONFIG_FILE:-<not set>}"
    echo "   Usage: bash seed-migrations.sh --config <global_configs.php> [--env <name>]"
    exit 1
fi

if [ -z "$MIGRATIONS_DIR" ] || [ ! -d "$MIGRATIONS_DIR" ]; then
    echo "❌ Migrations directory not found: ${MIGRATIONS_DIR:-<not set>}"
    exit 1
fi

# Parse DB credentials from PHP config
DB_HOST=$(grep -oP "\\\$hostname\s*=\s*'[^']*'" "$CONFIG_FILE" | grep -oP "'[^']*'" | tr -d "'")
DB_USER=$(grep -oP "\\\$username\s*=\s*'[^']*'" "$CONFIG_FILE" | grep -oP "'[^']*'" | tr -d "'")
DB_PASS=$(grep -oP "\\\$password\s*=\s*'[^']*'" "$CONFIG_FILE" | grep -oP "'[^']*'" | tr -d "'")
DB_NAME=$(grep -oP "\\\$database\s*=\s*'[^']*'" "$CONFIG_FILE" | grep -oP "'[^']*'" | tr -d "'")

if [ -z "$DB_HOST" ] || [ -z "$DB_USER" ] || [ -z "$DB_NAME" ]; then
    echo "❌ Failed to parse DB credentials from: $CONFIG_FILE"
    exit 1
fi

MYSQL="mysql -h${DB_HOST} -u${DB_USER} -p${DB_PASS} ${DB_NAME}"

# Get all migration files (sorted)
MIGRATION_FILES=$(find "$MIGRATIONS_DIR" -maxdepth 1 -name "*.sql" -type f -printf "%f\n" | sort)
FILE_COUNT=$(echo "$MIGRATION_FILES" | wc -l)

if [ -z "$MIGRATION_FILES" ]; then
    echo "⚠️ No migration files found in: $MIGRATIONS_DIR"
    exit 0
fi

echo "🗄  Seeding $FILE_COUNT migration(s) for ${DB_NAME}@${DB_HOST} (${ENVIRONMENT})"

if [ "$DRY_RUN" = true ]; then
    echo ""
    echo "📋 Would seed:"
    echo "$MIGRATION_FILES" | while read -r fname; do
        echo "  ➡  $fname"
    done
    echo ""
    echo "🔍 Dry run — no changes made"
    exit 0
fi

# Create tracking table
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

# Build INSERT IGNORE statement dynamically
VALUES=""
while read -r fname; do
    [ -z "$fname" ] && continue
    if [ -n "$VALUES" ]; then
        VALUES="${VALUES},"
    fi
    VALUES="${VALUES}\n('${fname}', '${ENVIRONMENT}')"
done <<< "$MIGRATION_FILES"

SQL="INSERT IGNORE INTO schema_migrations (migration, environment) VALUES ${VALUES};"

echo -e "$SQL" | $MYSQL 2>/dev/null

if [ $? -ne 0 ]; then
    echo "❌ Failed to seed migrations"
    exit 1
fi

# Count what was actually inserted
SEEDED=$($MYSQL -N -e "SELECT COUNT(*) FROM schema_migrations WHERE environment='${ENVIRONMENT}';" 2>/dev/null)

echo "✅ Seeded ${SEEDED} migration(s) for environment: ${ENVIRONMENT}"
echo ""
echo "📋 Latest migrations in database:"
$MYSQL -e "SELECT migration, applied_at FROM schema_migrations WHERE environment='${ENVIRONMENT}' ORDER BY migration DESC LIMIT 5;" 2>/dev/null
