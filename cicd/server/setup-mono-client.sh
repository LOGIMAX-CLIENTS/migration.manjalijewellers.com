#!/bin/bash
# =============================================================================
# SETUP MONO-REPO CLIENT — One-time server setup (Hybrid Layout)
# Creates staging (git pull) + production (releases/symlink) directory structure.
#
# Usage:
#   bash setup-mono-client.sh <client_id> <repo_ssh_url> [options]
#
# Options:
#   --domain <domain>             Production domain
#   --staging-domain <domain>     Staging domain
#   --base-path <path>            Server base path (default: /var/www)
#   --branch <branch>             Default branch (default: support)
#   --prod-branch <branch>        Production branch (default: Production)
#   --db-host <rds_endpoint>      RDS hostname → auto-generates global_configs.php
#   --db-user <username>          DB username (default: admin)
#   --db-pass <password>          DB password
#   --db-name <prod_db>           Production database name
#   --db-name-staging <staging_db> Staging database name
#   --webhook-secret <secret>     Webhook secret → sets Apache envvars + /etc/environment
#   --setup-php                   Configure php.ini (memory, uploads, timezone)
#   --setup-cron                  Add standard cron jobs (autoDayClose, resetDayClose)
#   --activate-vhost              Auto-activate Apache vhost (a2ensite + reload)
#   --skip-staging                Skip staging setup
#   --skip-vhost                  Skip Apache vhost generation
#   --skip-server-config          Skip timezone + php.ini (if already done for this server)
#
# Example (one-command setup):
#   sudo -E bash setup-mono-client.sh ashoka \
#     git@github.com:Logimax-Technologies/etail_development_src.git \
#     --domain erp.ashokajewellery.in --staging-domain staging.ashokajewellery.in \
#     --db-host rds.amazonaws.com --db-user admin --db-pass 'secret' \
#     --db-name ashoka_prod --db-name-staging ashoka_staging \
#     --webhook-secret '921c...' --setup-php --setup-cron --activate-vhost
# =============================================================================

set -euo pipefail

# =============================================================================
# ARGUMENTS
# =============================================================================

CLIENT_ID="${1:?Usage: setup-mono-client.sh <client_id> <repo_ssh_url> [options]}"
REPO_SSH_URL="${2:?Usage: setup-mono-client.sh <client_id> <repo_ssh_url> [options]}"
shift 2

# Defaults
DEFAULT_BRANCH="support"
PROD_BRANCH="PRODUCTION"
DOMAIN=""
STAGING_DOMAIN=""
BASE_PATH="/var/www"
SKIP_STAGING=false
SKIP_VHOST=false

# New automation params
DB_HOST=""
DB_USER="admin"
DB_PASS=""
DB_NAME=""
DB_NAME_STAGING=""
WEBHOOK_SECRET="921c493d946559a9a20565068a5f18ffbc6f899cb34b14f07f939e2e60157709"
SETUP_PHP=false
SETUP_CRON=false
ACTIVATE_VHOST=false
SKIP_SERVER_CONFIG=false

# Parse options
while [[ $# -gt 0 ]]; do
    case "$1" in
        --domain) DOMAIN="$2"; shift 2 ;;
        --staging-domain) STAGING_DOMAIN="$2"; shift 2 ;;
        --base-path) BASE_PATH="$2"; shift 2 ;;
        --branch) DEFAULT_BRANCH="$2"; shift 2 ;;
        --prod-branch) PROD_BRANCH="$2"; shift 2 ;;
        --db-host) DB_HOST="$2"; shift 2 ;;
        --db-user) DB_USER="$2"; shift 2 ;;
        --db-pass) DB_PASS="$2"; shift 2 ;;
        --db-name) DB_NAME="$2"; shift 2 ;;
        --db-name-staging) DB_NAME_STAGING="$2"; shift 2 ;;
        --webhook-secret) WEBHOOK_SECRET="$2"; shift 2 ;;
        --setup-php) SETUP_PHP=true; shift ;;
        --setup-cron) SETUP_CRON=true; shift ;;
        --activate-vhost) ACTIVATE_VHOST=true; shift ;;
        --skip-staging) SKIP_STAGING=true; shift ;;
        --skip-vhost) SKIP_VHOST=true; shift ;;
        --skip-server-config) SKIP_SERVER_CONFIG=true; shift ;;
        *) echo "Unknown option: $1"; exit 1 ;;
    esac
done

# Paths
BASE_DIR="${BASE_PATH}/${CLIENT_ID}"
PROD_DIR="${BASE_DIR}/prod"
STAGING_DIR="${BASE_DIR}/staging"
SHARED_DIR="${PROD_DIR}/shared"
RELEASES_DIR="${PROD_DIR}/releases"

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
    log "❌ No SSH key found for client '$CLIENT_ID'"
    exit 1
fi

export GIT_SSH_COMMAND="ssh -i $SSH_KEY -o IdentitiesOnly=yes -o StrictHostKeyChecking=no"

# =============================================================================
# LOGGING
# =============================================================================

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# =============================================================================
# PRE-FLIGHT CHECKS
# =============================================================================

if [ -d "$BASE_DIR" ]; then
    log "❌ Directory ${BASE_DIR} already exists. Aborting to prevent overwrite."
    log "   If you want to re-setup, manually remove ${BASE_DIR} first."
    exit 1
fi

if [ ! -f "$SSH_KEY" ]; then
    log "❌ SSH key not found at ${SSH_KEY}"
    exit 1
fi

# Ensure mod_rewrite is enabled (prevents Apache syntax errors with vhost)
if command -v a2enmod &>/dev/null; then
    if ! apache2ctl -M 2>/dev/null | grep -q rewrite; then
        log "⚠️  mod_rewrite not enabled — enabling now..."
        a2enmod rewrite 2>/dev/null
        log "✅ mod_rewrite enabled"
    fi
fi

log "═══════════════════════════════════════════════════════════"
log "🏗️  Setting up mono-repo client: ${CLIENT_ID}"
log "═══════════════════════════════════════════════════════════"
log "📂 Base: ${BASE_DIR}"
log "🔗 Repo: ${REPO_SSH_URL}"
log "🌿 Staging branch: ${DEFAULT_BRANCH}"
log "🌿 Production branch: ${PROD_BRANCH}"
[ -n "$DOMAIN" ] && log "🌐 Domain: ${DOMAIN}"
[ -n "$STAGING_DOMAIN" ] && log "🌐 Staging: ${STAGING_DOMAIN}"

# =============================================================================
# CREATE DIRECTORY STRUCTURE
# =============================================================================

log "📁 Creating directory structure..."

# Shared config
mkdir -p "${SHARED_DIR}/config"

# Shared data directories (14 data + 3 assets + rate.txt)
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

for dir in "${DATA_DIRS[@]}"; do
    mkdir -p "${SHARED_DIR}/${dir}"
done

# rate.txt placeholder
touch "${SHARED_DIR}/data/rate.txt"

# Log directories
mkdir -p "${SHARED_DIR}/logs/app_log"
mkdir -p "${SHARED_DIR}/logs/tagging_log"

# Releases directory
mkdir -p "${RELEASES_DIR}"

# Backup directory (for migration pre-deploy backups)
mkdir -p "${PROD_DIR}/backups"

log "✅ Directory structure created"

# =============================================================================
# CLONE PRODUCTION (first release)
# =============================================================================

TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
RELEASE_DIR="${RELEASES_DIR}/${TIMESTAMP}"

log "📥 Cloning production (branch: ${PROD_BRANCH})..."
git clone --depth 1 --branch "${PROD_BRANCH}" "${REPO_SSH_URL}" "${RELEASE_DIR}"

if [ $? -ne 0 ]; then
    log "❌ Git clone failed"
    exit 1
fi

log "✅ Production clone: ${RELEASE_DIR}"

# =============================================================================
# CLONE STAGING (full clone for git pull)
# =============================================================================

if [ "$SKIP_STAGING" = false ]; then
    log "📥 Cloning staging (branch: ${DEFAULT_BRANCH})..."
    git clone --branch "${DEFAULT_BRANCH}" "${REPO_SSH_URL}" "${STAGING_DIR}"

    if [ $? -ne 0 ]; then
        log "❌ Staging clone failed"
        exit 1
    fi

    # Create gitignored dirs in staging
    log "📁 Creating gitignored directories in staging..."
    STAGING_DIRS=(
        "admin/img" "admin/log" "admin/tagging_log"
        "admin/uploads" "admin/uploads_root"
        "admin/assets/kyc" "admin/assets/img"
        "admin/vendor_ack" "admin/estimation" "admin/export"
        "admin/adm_app_apk" "admin/bill_qrcode" "admin/esti_qrcode"
        "admin/other_qrcode" "admin/other_inventory_qrcode"
        "admin/other_product_qrcode"
    )
    for dir in "${STAGING_DIRS[@]}"; do
        mkdir -p "${STAGING_DIR}/${dir}"
    done

    # Create staging webhook handler (symlink to shared copy)
    mkdir -p "${STAGING_DIR}/webhooks"
    if [ -f "${STAGING_DIR}/cicd/server/webhook-deploy.php" ]; then
        cp "${STAGING_DIR}/cicd/server/webhook-deploy.php" "${SHARED_DIR}/webhook-deploy.php" 2>/dev/null || true
    fi
    if [ -f "${SHARED_DIR}/webhook-deploy.php" ]; then
        ln -sf "${SHARED_DIR}/webhook-deploy.php" "${STAGING_DIR}/webhooks/deploy.php"
        log "   ✅ Staging webhook handler symlinked"
    fi

    log "✅ Staging clone: ${STAGING_DIR}"
fi

# =============================================================================
# CREATE CONFIG TEMPLATES
# =============================================================================

log "📝 Creating config files..."

# Determine DB values (real or placeholder)
CFG_HOSTNAME="${DB_HOST:-REPLACE_WITH_RDS_ENDPOINT}"
CFG_USERNAME="${DB_USER:-REPLACE_WITH_DB_USER}"
CFG_PASSWORD="${DB_PASS:-REPLACE_WITH_DB_PASSWORD}"
CFG_DATABASE="${DB_NAME:-REPLACE_WITH_DB_NAME}"
CFG_DATABASE_STAGING="${DB_NAME_STAGING:-REPLACE_WITH_DB_NAME_STAGING}"

# Production global_configs.php (full format matching cicd/server/global_configs.php)
cat > "${SHARED_DIR}/config/global_configs.php" << GLOBALCFG
<?php
/**
 *
 * Global config file
 *
 * @author	Logimax Team
 */

 //Check direct script access
if ( \$_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( \$_SERVER['SCRIPT_FILENAME'] ) ) {
	header("HTTP/1.0 404 Not Found");
	echo "<h1>Not Found</h1>";
	echo "The requested URL was not found on this server.";
	exit();
}

/**
 * All global constants
 *
 */
if (!class_exists('Globals')) {
class Globals {

	/**
	 * Purchase Plan API version 
	 */
	public static \$pp_api_version = "1.0.0";

	/*
	 * DB configuation. host name, username, password, database name
	 */

	public static \$hostname = "${CFG_HOSTNAME}";
	public static \$username = "${CFG_USERNAME}";
	public static \$password = "${CFG_PASSWORD}";
	public static \$database = "${CFG_DATABASE}";
    
    
    /**
	* Purchase Plan acclount 
	*/
    public static \$default_acno_label = "Transaction Pending";
    
	/**
	 * Timezone for this website
	 */
	public static \$timezone = "Asia/Kolkata";
}
}

//Global declarations 
date_default_timezone_set(Globals::\$timezone);

//Check cURL exists
if (! function_exists ( 'curl_version' )) {
    exit ( "Enable cURL in PHP to proceed..." );
}
GLOBALCFG

# Staging global_configs.php (same but with staging DB name)
cat > "${STAGING_DIR}/global_configs.php" << STAGINGCFG
<?php
/**
 *
 * Global config file (Staging)
 *
 * @author	Logimax Team
 */

 //Check direct script access
if ( \$_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( \$_SERVER['SCRIPT_FILENAME'] ) ) {
	header("HTTP/1.0 404 Not Found");
	echo "<h1>Not Found</h1>";
	echo "The requested URL was not found on this server.";
	exit();
}

if (!class_exists('Globals')) {
class Globals {

	public static \$pp_api_version = "1.0.0";

	public static \$hostname = "${CFG_HOSTNAME}";
	public static \$username = "${CFG_USERNAME}";
	public static \$password = "${CFG_PASSWORD}";
	public static \$database = "${CFG_DATABASE_STAGING}";
    
    public static \$default_acno_label = "Transaction Pending";
    
	public static \$timezone = "Asia/Kolkata";
}
}

date_default_timezone_set(Globals::\$timezone);

if (! function_exists ( 'curl_version' )) {
    exit ( "Enable cURL in PHP to proceed..." );
}
STAGINGCFG

# database.php (CI database config — uses Globals::)
cat > "${SHARED_DIR}/config/database.php" << 'DBCONFIG'
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn'       => '',
    'hostname'  => Globals::$hostname,
    'username'  => Globals::$username,
    'password'  => Globals::$password,
    'database'  => Globals::$database,
    'dbdriver'  => 'mysqli',
    'dbprefix'  => '',
    'pconnect'  => FALSE,
    'db_debug'  => FALSE,
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8mb4',
    'dbcollat'  => 'utf8mb4_general_ci',
    'swap_pre'  => '',
    'encrypt'   => FALSE,
    'compress'  => FALSE,
    'stricton'  => FALSE,
    'failover'  => array(),
    'save_queries' => FALSE
);
DBCONFIG

# config.php (CI main config — copy from release and customize base_url)
if [ -f "${RELEASE_DIR}/admin/application/config/config.php" ]; then
    cp "${RELEASE_DIR}/admin/application/config/config.php" "${SHARED_DIR}/config/config.php"
    # Update base_url if domain is provided
    if [ -n "$DOMAIN" ]; then
        sed -i "s|\$config\['base_url'\].*|\$config['base_url'] = 'https://${DOMAIN}/admin/';|" "${SHARED_DIR}/config/config.php"
        log "   ✅ config.php: base_url set to https://${DOMAIN}/admin/"
    else
        log "   ⚠️  config.php copied — UPDATE base_url manually!"
    fi
else
    log "   ⚠️  config.php not found in release — create manually in ${SHARED_DIR}/config/"
fi

if [ -n "$DB_HOST" ]; then
    log "✅ Config files created with RDS credentials"
else
    log "✅ Config templates created"
    log "⚠️  Edit global_configs.php with your RDS credentials!"
fi

# =============================================================================
# SYMLINKS IN PRODUCTION RELEASE
# =============================================================================

log "🔗 Creating production symlinks..."

# Config symlinks
ln -sf "${SHARED_DIR}/config/database.php"       "${RELEASE_DIR}/admin/application/config/database.php"
ln -sf "${SHARED_DIR}/config/config.php"          "${RELEASE_DIR}/admin/application/config/config.php"
ln -sf "${SHARED_DIR}/config/global_configs.php"  "${RELEASE_DIR}/global_configs.php"

# Data directory symlinks (gitignored — won't exist in clones)
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

# Assets symlinks (partially gitignored — copy tracked files first, then symlink)
if [ -d "${RELEASE_DIR}/admin/assets/img" ]; then
    cp -rp "${RELEASE_DIR}/admin/assets/img/"* "${SHARED_DIR}/data/assets_img/" 2>/dev/null || true
    rm -rf "${RELEASE_DIR}/admin/assets/img"
fi
ln -sf "${SHARED_DIR}/data/assets_img"                 "${RELEASE_DIR}/admin/assets/img"

if [ -d "${RELEASE_DIR}/admin/assets/dist" ]; then
    cp -rp "${RELEASE_DIR}/admin/assets/dist/"* "${SHARED_DIR}/data/assets_dist/" 2>/dev/null || true
    rm -rf "${RELEASE_DIR}/admin/assets/dist"
fi
ln -sf "${SHARED_DIR}/data/assets_dist"                "${RELEASE_DIR}/admin/assets/dist"

if [ -d "${RELEASE_DIR}/admin/assets/plugins" ]; then
    cp -rp "${RELEASE_DIR}/admin/assets/plugins/"* "${SHARED_DIR}/data/assets_plugins/" 2>/dev/null || true
    rm -rf "${RELEASE_DIR}/admin/assets/plugins"
fi
ln -sf "${SHARED_DIR}/data/assets_plugins"             "${RELEASE_DIR}/admin/assets/plugins"

# Log symlinks
ln -sf "${SHARED_DIR}/logs/app_log"                    "${RELEASE_DIR}/admin/log"
ln -sf "${SHARED_DIR}/logs/tagging_log"                "${RELEASE_DIR}/admin/tagging_log"

log "✅ Symlinks created (3 config + 14 data + 3 assets + 2 log)"

# NOTE: global_configs.php auto-detection is built into admin/index.php (no uncommenting needed).
# The code uses file_exists() to conditionally load it — works on servers (symlink present)
# and locally (file absent) without any modification.

# =============================================================================
# WEBHOOK HANDLER (copy to shared + symlink)
# =============================================================================

log "📝 Setting up webhook handler..."

# Copy the full webhook-deploy.php from the cloned release to shared/
cp "${RELEASE_DIR}/cicd/server/webhook-deploy.php" "${SHARED_DIR}/webhook-deploy.php"

# Copy deploy-mono.sh to shared/ (avoids chicken-and-egg: webhook handler needs
# the deploy script to exist independently of the release being replaced)
if [ -f "${RELEASE_DIR}/cicd/server/deploy-mono.sh" ]; then
    cp "${RELEASE_DIR}/cicd/server/deploy-mono.sh" "${SHARED_DIR}/deploy-mono.sh"
    chown ubuntu:www-data "${SHARED_DIR}/deploy-mono.sh"
    chmod 755 "${SHARED_DIR}/deploy-mono.sh"
    log "   ✅ deploy-mono.sh copied to shared/"
else
    log "   ⚠️  deploy-mono.sh not found in release — webhook deploys may use fallback path"
fi

# Symlink in production release: webhooks/deploy.php → shared copy
mkdir -p "${RELEASE_DIR}/webhooks"
rm -f "${RELEASE_DIR}/webhooks/deploy.php"
ln -sf "${SHARED_DIR}/webhook-deploy.php" "${RELEASE_DIR}/webhooks/deploy.php"

log "✅ Webhook handler: shared/webhook-deploy.php → webhooks/deploy.php (symlinked)"

# =============================================================================
# ATOMIC SYMLINK: current → release
# =============================================================================

log "🔗 Creating 'current' symlink..."
ln -sf "${RELEASE_DIR}" "${PROD_DIR}/current_tmp"
mv -Tf "${PROD_DIR}/current_tmp" "${PROD_DIR}/current"
log "✅ current → ${RELEASE_DIR}"

# =============================================================================
# MAINTENANCE PAGE
# =============================================================================

log "📄 Copying maintenance page..."
if [ -f "${RELEASE_DIR}/maintenance.html" ]; then
    cp "${RELEASE_DIR}/maintenance.html" "${SHARED_DIR}/maintenance.html"
    log "✅ maintenance.html copied to shared/"
else
    log "⚠️  maintenance.html not found — create manually in ${SHARED_DIR}/"
fi

# =============================================================================
# DEPLOY ENVIRONMENT CONFIG (shared/deploy.env)
# =============================================================================

log "📝 Creating deploy.env..."

cat > "${SHARED_DIR}/deploy.env" << DEPLOYENV
# Auto-generated by setup-mono-client.sh — sourced by deploy-mono.sh
# Persists deploy configuration so manual env vars are not needed.
CLIENT_ID=${CLIENT_ID}
BASE_DIR=${PROD_DIR}
DEPLOY_ENV=production
HEALTH_CHECK_URL=${DOMAIN:+https://${DOMAIN}}
DEVOPS_API_URL=https://devops.logimaxindia.com
DEPLOYENV

if [ -n "$DOMAIN" ]; then
    log "✅ deploy.env created (HEALTH_CHECK_URL=https://${DOMAIN})"
else
    log "⚠️  deploy.env created without HEALTH_CHECK_URL — add --domain or edit manually"
fi

# =============================================================================
# APACHE VHOST GENERATION
# =============================================================================

if [ "$SKIP_VHOST" = false ] && [ -n "$DOMAIN" ]; then
    VHOST_FILE="/tmp/${CLIENT_ID}-vhost.conf"
    log "📝 Generating Apache vhost → ${VHOST_FILE}"

    cat > "${VHOST_FILE}" << VHOST
# ── STAGING ───────────────────────────────────────────────
<VirtualHost *:80>
    ServerName ${STAGING_DOMAIN:-staging.${DOMAIN}}
    ServerAlias www.${STAGING_DOMAIN:-staging.${DOMAIN}}
    DocumentRoot ${STAGING_DIR}

    SetEnv CLIENT_ID ${CLIENT_ID}

    <Directory ${STAGING_DIR}>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
        DirectoryIndex index.php index.html
    </Directory>

    # Maintenance page (served from shared/ via Alias — outside DocumentRoot)
    Alias /maintenance.html ${SHARED_DIR}/maintenance.html
    <Location /maintenance.html>
        Require all granted
    </Location>

    RewriteEngine On
    RewriteCond ${STAGING_DIR}/.maintenance_active -f
    RewriteCond %{REQUEST_URI} !^/maintenance\.html\$
    RewriteCond %{REQUEST_URI} !^/webhooks/
    RewriteRule ^(.*)\$ - [R=503,L]
    ErrorDocument 503 /maintenance.html

    ErrorLog \${APACHE_LOG_DIR}/${CLIENT_ID}_staging_error.log
    CustomLog \${APACHE_LOG_DIR}/${CLIENT_ID}_staging_access.log combined
</VirtualHost>

# ── PRODUCTION ────────────────────────────────────────────
<VirtualHost *:80>
    ServerName ${DOMAIN}
    ServerAlias www.${DOMAIN}
    DocumentRoot ${PROD_DIR}/current

    SetEnv CLIENT_ID ${CLIENT_ID}

    <Directory ${PROD_DIR}/current>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
        DirectoryIndex index.php index.html
    </Directory>

    # Maintenance page (served from shared/ via Alias — outside DocumentRoot)
    Alias /maintenance.html ${SHARED_DIR}/maintenance.html
    <Location /maintenance.html>
        Require all granted
    </Location>

    # Webhook handler (served from shared/ — always up to date)
    Alias /webhooks/deploy.php ${SHARED_DIR}/webhook-deploy.php

    RewriteEngine On
    RewriteCond ${PROD_DIR}/.maintenance_active -f
    RewriteCond %{REQUEST_URI} !^/maintenance\.html\$
    RewriteCond %{REQUEST_URI} !^/webhooks/
    RewriteRule ^(.*)\$ - [R=503,L]
    ErrorDocument 503 /maintenance.html

    ErrorLog \${APACHE_LOG_DIR}/${CLIENT_ID}_prod_error.log
    CustomLog \${APACHE_LOG_DIR}/${CLIENT_ID}_prod_access.log combined
</VirtualHost>
VHOST

    log "✅ Vhost config generated: ${VHOST_FILE}"
    log "   To activate:"
    log "   sudo cp ${VHOST_FILE} /etc/apache2/sites-available/${CLIENT_ID}.conf"
    log "   sudo a2ensite ${CLIENT_ID}.conf"
    log "   sudo apache2ctl configtest && sudo systemctl reload apache2"
fi

# =============================================================================
# PERMISSIONS (comprehensive — based on set_perm.sh)
# =============================================================================

log "🔒 Setting permissions..."

WRITABLE_DIRS=(
    "data/img"
    "data/kyc"
    "data/uploads"
    "data/vendor_ack"
    "data/estimation"
    "data/export"
    "data/bill_qrcode"
    "data/esti_qrcode"
    "data/other_qrcode"
    "data/other_inventory_qrcode"
    "data/other_product_qrcode"
    "data/uploads_root"
    "logs"
    "logs/app_log"
    "logs/tagging_log"
)

# Add ubuntu to www-data group (allows SFTP/SCP uploads)
usermod -aG www-data ubuntu 2>/dev/null || true

# Ownership: ubuntu owns, www-data group (allows both SFTP + Apache access)
chown -R ubuntu:www-data "${BASE_DIR}"

# Default directory permissions → 755
find "${BASE_DIR}" -type d -exec chmod 755 {} \;

# Default file permissions → 644
find "${BASE_DIR}" -type f -exec chmod 644 {} \;

# Preserve executable scripts → 755
find "${BASE_DIR}" -type f \( -name "*.sh" -o -name "*.bash" \) -exec chmod 755 {} \;

# Group-writable on staging (for SFTP uploads + webhook deploys as www-data)
if [ -d "${STAGING_DIR}" ]; then
    chmod -R g+w "${STAGING_DIR}"
    find "${STAGING_DIR}" -type d -exec chmod g+s {} \;
fi

# SSH keys: ensure www-data (group) can read for webhook-triggered deploys
# Create /var/www/.ssh and copy the key if it doesn't exist yet
WWW_SSH_DIR="/var/www/.ssh"
if [ ! -d "$WWW_SSH_DIR" ]; then
    log "📁 Creating $WWW_SSH_DIR for www-data SSH access..."
    mkdir -p "$WWW_SSH_DIR"
fi

# Copy SSH key to /var/www/.ssh if not already there
WWW_KEY_FILE=""
if [ -f "${WWW_SSH_DIR}/id_ed25519_${CLIENT_ID}" ]; then
    WWW_KEY_FILE="${WWW_SSH_DIR}/id_ed25519_${CLIENT_ID}"
elif [ -f "${WWW_SSH_DIR}/id_ed25519" ]; then
    WWW_KEY_FILE="${WWW_SSH_DIR}/id_ed25519"
else
    # Copy from the key used for cloning (SSH_KEY resolved earlier)
    if [ -n "$SSH_KEY" ] && [ -f "$SSH_KEY" ]; then
        DEST_KEY="${WWW_SSH_DIR}/$(basename "$SSH_KEY")"
        cp "$SSH_KEY" "$DEST_KEY"
        WWW_KEY_FILE="$DEST_KEY"
        log "   ✅ Copied $(basename "$SSH_KEY") → $WWW_SSH_DIR/"
    else
        log "   ⚠️  No SSH key to copy — webhook deploys may fail"
    fi
fi

# Set permissions: ubuntu owns, www-data group can read
chmod 710 "$WWW_SSH_DIR"
chown ubuntu:www-data "$WWW_SSH_DIR"
if [ -n "$WWW_KEY_FILE" ]; then
    chown ubuntu:www-data "$WWW_KEY_FILE"
    chmod 640 "$WWW_KEY_FILE"
fi

# Pre-populate known_hosts for GitHub
if ! grep -q github.com "${WWW_SSH_DIR}/known_hosts" 2>/dev/null; then
    ssh-keyscan github.com 2>/dev/null >> "${WWW_SSH_DIR}/known_hosts"
    chown ubuntu:www-data "${WWW_SSH_DIR}/known_hosts"
    chmod 664 "${WWW_SSH_DIR}/known_hosts"
fi
log "✅ SSH key configured in $WWW_SSH_DIR for www-data"

# Writable directories (setgid + group writable)
for DIR in "${WRITABLE_DIRS[@]}"; do
    FULL="${SHARED_DIR}/${DIR}"
    if [ -d "$FULL" ]; then
        find "$FULL" -type d -exec chmod 2775 {} \;
        find "$FULL" -type f -exec chmod 644 {} \;
        find "$FULL" -type d -exec chmod g+s {} \;
    fi
done

log "✅ Permissions set (ubuntu:www-data ownership, 755/644 defaults, 2775 writable dirs)"

# =============================================================================
# SERVER CONFIGURATION (timezone, php.ini)
# =============================================================================

if [ "$SETUP_PHP" = true ] && [ "$SKIP_SERVER_CONFIG" = false ]; then
    log "⚙️  Configuring server (timezone + php.ini)..."

    # Timezone — set both timedatectl AND legacy /etc/timezone
    # (timedatectl alone leaves /etc/timezone stale on some Ubuntu versions)
    timedatectl set-timezone Asia/Kolkata 2>/dev/null || true
    echo "Asia/Kolkata" > /etc/timezone 2>/dev/null || true
    dpkg-reconfigure -f noninteractive tzdata 2>/dev/null || true
    log "   ✅ Timezone: Asia/Kolkata (timedatectl + /etc/timezone)"

    # PHP ini settings (both Apache + CLI)
    PHP_SETTINGS=(
        "memory_limit = 512M"
        "post_max_size = 256M"
        "upload_max_filesize = 128M"
        "max_input_vars = 20000"
        "max_execution_time = 600"
        "max_input_time = 600"
    )

    for ini_file in /etc/php/*/apache2/php.ini /etc/php/*/cli/php.ini; do
        if [ -f "$ini_file" ]; then
            for setting in "${PHP_SETTINGS[@]}"; do
                key=$(echo "$setting" | cut -d= -f1 | xargs)
                value=$(echo "$setting" | cut -d= -f2 | xargs)
                # Only update if not already set to target value
                if ! grep -q "^${key} = ${value}$" "$ini_file" 2>/dev/null; then
                    sed -i "s/^${key} = .*/${key} = ${value}/" "$ini_file" 2>/dev/null || true
                fi
            done
        fi
    done
    log "   ✅ PHP ini configured (512M memory, 128M uploads, 600s timeout)"
elif [ "$SETUP_PHP" = true ]; then
    log "⏭  Skipping server config (--skip-server-config)"
fi

# =============================================================================
# WEBHOOK SECRET (Apache envvars + /etc/environment)
# =============================================================================

if [ -n "$WEBHOOK_SECRET" ]; then
    log "🔑 Setting webhook secret..."

    # Apache envvars (for PHP / webhook-deploy.php)
    for var_name in WEBHOOK_SECRET DEVOPS_WEBHOOK_SECRET MONO_WEBHOOK_SECRET; do
        if ! grep -q "^export ${var_name}=" /etc/apache2/envvars 2>/dev/null; then
            echo "export ${var_name}='${WEBHOOK_SECRET}'" >> /etc/apache2/envvars
        fi
    done

    # /etc/environment (for CLI / deploy-mono.sh)
    for var_name in WEBHOOK_SECRET DEVOPS_WEBHOOK_SECRET; do
        if ! grep -q "^${var_name}=" /etc/environment 2>/dev/null; then
            echo "${var_name}='${WEBHOOK_SECRET}'" >> /etc/environment
        fi
    done

    log "✅ Webhook secret set in Apache envvars + /etc/environment"
fi

# =============================================================================
# DEVOPS API URL (Apache envvars + /etc/environment)
# Ensures deploy-mono.sh can report status to DevOps Tool
# =============================================================================

DEVOPS_API_URL_DEFAULT="https://devops.logimaxindia.com"
log "🔗 Setting DevOps API URL (${DEVOPS_API_URL_DEFAULT})..."

# Apache envvars (for PHP / webhook-deploy.php)
if ! grep -q "^export DEVOPS_API_URL=" /etc/apache2/envvars 2>/dev/null; then
    echo "export DEVOPS_API_URL='${DEVOPS_API_URL_DEFAULT}'" >> /etc/apache2/envvars
fi

# /etc/environment (for CLI / deploy-mono.sh)
if ! grep -q "^DEVOPS_API_URL=" /etc/environment 2>/dev/null; then
    echo "DEVOPS_API_URL='${DEVOPS_API_URL_DEFAULT}'" >> /etc/environment
fi

log "✅ DEVOPS_API_URL set in Apache envvars + /etc/environment"

# =============================================================================
# CRON JOBS (autoDayClose + resetDayClose)
# =============================================================================

if [ "$SETUP_CRON" = true ] && [ -n "$DOMAIN" ]; then
    log "⏱  Setting up cron jobs..."

    PROD_DOMAIN="${DOMAIN}"
    STG_DOMAIN="${STAGING_DOMAIN:-staging.${DOMAIN}}"

    # Check if cron entries already exist for this domain
    EXISTING_CRON=$(crontab -u www-data -l 2>/dev/null || echo "")
    if echo "$EXISTING_CRON" | grep -q "${PROD_DOMAIN}"; then
        log "   ⏭  Cron already configured for ${PROD_DOMAIN}"
    else
        CRON_ENTRIES="
        # ${CLIENT_ID} — auto day close + reset (prod + staging)
        30 23 * * * curl -s https://${PROD_DOMAIN}/admin/index.php/admin_ret_services/autoDayClose
        30 23 * * * curl -s https://${STG_DOMAIN}/admin/index.php/admin_ret_services/autoDayClose
        59 23 * * * curl -s https://${PROD_DOMAIN}/admin/index.php/admin_ret_services/resetDayClose
        59 23 * * * curl -s https://${STG_DOMAIN}/admin/index.php/admin_ret_services/resetDayClose"

        (echo "$EXISTING_CRON"; echo "$CRON_ENTRIES") | crontab -u www-data -
        log "✅ Cron jobs added for ${PROD_DOMAIN} + ${STG_DOMAIN}"
    fi
fi

# =============================================================================
# APACHE VHOST ACTIVATION
# =============================================================================

if [ "$ACTIVATE_VHOST" = true ] && [ "$SKIP_VHOST" = false ] && [ -n "$DOMAIN" ]; then
    log "🌐 Activating Apache vhost..."

    VHOST_FILE="/tmp/${CLIENT_ID}-vhost.conf"
    SITES_FILE="/etc/apache2/sites-available/${CLIENT_ID}.conf"

    if [ -f "$VHOST_FILE" ]; then
        # Enable mod_rewrite if not already enabled
        a2enmod rewrite 2>/dev/null || true

        cp "$VHOST_FILE" "$SITES_FILE"
        a2ensite "${CLIENT_ID}.conf" 2>/dev/null || true

        # Test config before reload
        if apache2ctl configtest 2>&1 | grep -q "Syntax OK"; then
            systemctl reload apache2
            log "✅ Apache vhost activated and reloaded"
        else
            log "❌ Apache config test FAILED — vhost NOT activated"
            log "   Fix: sudo nano $SITES_FILE"
            apache2ctl configtest
        fi
    else
        log "❌ Vhost file not found: $VHOST_FILE"
    fi
fi

# =============================================================================
# PHP-ZIP EXTENSION (required by port_max for XLSX processing)
# =============================================================================

PHP_VER=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;" 2>/dev/null || echo "7.4")

if ! php -m 2>/dev/null | grep -qi "^zip$"; then
    log "📦 Installing PHP zip extension..."

    # Unhold PHP packages (may be pinned to prevent auto-upgrades)
    apt-mark unhold php${PHP_VER} php${PHP_VER}-common php${PHP_VER}-cli php${PHP_VER}-fpm libapache2-mod-php${PHP_VER} 2>/dev/null || true

    apt-get update -qq 2>&1
    apt-get install -y --fix-broken 2>&1
    apt-get install -y php${PHP_VER}-common php${PHP_VER}-cli libapache2-mod-php${PHP_VER} php${PHP_VER}-zip 2>&1

    # Re-hold PHP packages to prevent future auto-upgrades
    apt-mark hold php${PHP_VER} php${PHP_VER}-common php${PHP_VER}-cli php${PHP_VER}-fpm libapache2-mod-php${PHP_VER} php${PHP_VER}-zip 2>/dev/null || true

    systemctl restart apache2 2>/dev/null || true

    if php -m 2>/dev/null | grep -qi "^zip$"; then
        log "✅ PHP zip extension installed"
    else
        log "⚠️  PHP zip install failed — install manually: sudo apt install php${PHP_VER}-zip"
    fi
else
    log "✅ PHP zip extension already installed"
fi

# =============================================================================
# COMPOSER (required by port_max API)
# =============================================================================

if ! command -v composer &>/dev/null; then
    log "📥 Installing Composer..."
    PHP_BIN=$(which php 2>/dev/null || echo "")
    if [ -n "$PHP_BIN" ]; then
        curl -sS https://getcomposer.org/installer | $PHP_BIN -- --install-dir=/usr/local/bin --filename=composer 2>&1
        if command -v composer &>/dev/null; then
            COMPOSER_VER=$(COMPOSER_NO_INTERACTION=1 timeout 10 composer -V 2>/dev/null | head -1 || echo "installed")
            log "✅ Composer installed: ${COMPOSER_VER}"
        else
            log "⚠️  Composer install failed"
        fi
    fi
else
    COMPOSER_VER=$(COMPOSER_NO_INTERACTION=1 timeout 10 composer -V 2>/dev/null | head -1 || echo "installed")
    log "✅ Composer already installed: ${COMPOSER_VER}"
fi

# =============================================================================
# SUMMARY
# =============================================================================

log ""
log "═══════════════════════════════════════════════════════════"
log "✅ Setup Complete: ${CLIENT_ID}"
log "═══════════════════════════════════════════════════════════"
log ""
log "Structure:"
log "  ${BASE_DIR}/"
log "  ├── staging/          (git pull deploys)"
log "  └── prod/"
log "      ├── current → releases/${TIMESTAMP}/"
log "      ├── releases/"
log "      └── shared/       (config, data, logs)"
log ""

# Build dynamic "remaining steps" list
REMAINING=()

if [ -z "$DB_HOST" ]; then
    REMAINING+=("Edit ${SHARED_DIR}/config/global_configs.php (RDS credentials)")
    REMAINING+=("Edit ${STAGING_DIR}/global_configs.php (staging DB name)")
fi

REMAINING+=("Check assets: ls ${SHARED_DIR}/data/assets_img/ — if empty, upload via WinSCP")

if [ "$ACTIVATE_VHOST" = false ] && [ "$SKIP_VHOST" = false ] && [ -n "$DOMAIN" ]; then
    REMAINING+=("Activate vhost: sudo cp /tmp/${CLIENT_ID}-vhost.conf /etc/apache2/sites-available/${CLIENT_ID}.conf && sudo a2ensite ${CLIENT_ID}.conf && sudo systemctl reload apache2")
fi

if [ -z "$WEBHOOK_SECRET" ]; then
    REMAINING+=("Set DEVOPS_WEBHOOK_SECRET in /etc/apache2/envvars and /etc/environment")
fi

if [ "$SETUP_CRON" = false ]; then
    REMAINING+=("Add cron jobs for autoDayClose + resetDayClose")
fi

REMAINING+=("DNS: Point ${DOMAIN} and ${STAGING_DOMAIN:-staging.${DOMAIN}} to this server (Senior)")
REMAINING+=("SSL: Add domains to ACM certificate (Senior)")

if [ ${#REMAINING[@]} -gt 0 ]; then
    log "Remaining manual steps:"
    for i in "${!REMAINING[@]}"; do
        log "  $((i+1)). ${REMAINING[$i]}"
    done
else
    log "🎉 All automated steps complete! Only DNS + SSL remain (Senior)."
fi
log ""
log "Run validate-client.sh to verify:"
log "  sudo bash ${STAGING_DIR}/cicd/server/validate-client.sh ${CLIENT_ID}"
log ""

