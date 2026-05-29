#!/bin/bash
# =============================================================================
# VALIDATE MONO-REPO CLIENT — Pre-flight check for deployment readiness
# Checks all common issues that cause deploy failures.
#
# Usage:
#   bash validate-client.sh <client_id> [--fix]
#
# Options:
#   --fix    Attempt to auto-fix issues (requires sudo)
# =============================================================================

CLIENT_ID="${1:?Usage: validate-client.sh <client_id> [--fix]}"
AUTO_FIX=false
[ "$2" = "--fix" ] && AUTO_FIX=true

BASE_PATH="/var/www"
BASE_DIR="${BASE_PATH}/${CLIENT_ID}"
PROD_DIR="${BASE_DIR}/prod"
STAGING_DIR="${BASE_DIR}/staging"
SHARED_DIR="${PROD_DIR}/shared"
CURRENT_LINK="${PROD_DIR}/current"

PASS=0
FAIL=0
WARN=0

check() {
    local status="$1" label="$2" detail="$3"
    if [ "$status" = "ok" ]; then
        echo "  ✅ $label"
        PASS=$((PASS + 1))
    elif [ "$status" = "warn" ]; then
        echo "  ⚠️  $label — $detail"
        WARN=$((WARN + 1))
    else
        echo "  ❌ $label — $detail"
        FAIL=$((FAIL + 1))
    fi
}

echo "═══════════════════════════════════════════════════════════"
echo "🔍 Validating client: ${CLIENT_ID}"
echo "═══════════════════════════════════════════════════════════"
echo ""

# ── 1. Directory Structure ──
echo "📁 Directory Structure"
[ -d "$PROD_DIR" ] && check "ok" "prod/ exists" || check "fail" "prod/ missing" "Run setup-mono-client.sh"
[ -d "$STAGING_DIR" ] && check "ok" "staging/ exists" || check "fail" "staging/ missing" "Run setup-mono-client.sh"
[ -d "$SHARED_DIR" ] && check "ok" "shared/ exists" || check "fail" "shared/ missing" "Run setup-mono-client.sh"
[ -d "${PROD_DIR}/releases" ] && check "ok" "releases/ exists" || check "fail" "releases/ missing" "mkdir -p ${PROD_DIR}/releases"
[ -L "$CURRENT_LINK" ] && check "ok" "current symlink exists" || check "fail" "current symlink missing" "No active release"

if [ -d "${PROD_DIR}/backups" ]; then
    check "ok" "backups/ exists"
else
    check "fail" "backups/ missing" "Migrations will fail on backup step"
    if [ "$AUTO_FIX" = true ]; then
        mkdir -p "${PROD_DIR}/backups"
        chown ubuntu:www-data "${PROD_DIR}/backups"
        echo "       → Fixed: created ${PROD_DIR}/backups"
    fi
fi
echo ""

# ── 2. SSH Key Access ──
echo "🔑 SSH Key Access"
SSH_DIR="/var/www/.ssh"
if [ -d "$SSH_DIR" ]; then
    # Check directory permissions (www-data needs execute)
    dir_perms=$(stat -c '%a' "$SSH_DIR")
    if [ "$((dir_perms % 100 / 10))" -ge 1 ]; then
        check "ok" "SSH dir has group execute ($dir_perms)"
    else
        check "fail" "SSH dir lacks group execute ($dir_perms)" "www-data can't enter"
        if [ "$AUTO_FIX" = true ]; then
            chmod 710 "$SSH_DIR"
            echo "       → Fixed: chmod 710 $SSH_DIR"
        fi
    fi

    # Check key readability
    KEY_FILE=""
    if [ -f "${SSH_DIR}/id_ed25519_${CLIENT_ID}" ]; then
        KEY_FILE="${SSH_DIR}/id_ed25519_${CLIENT_ID}"
    elif [ -f "${SSH_DIR}/id_ed25519" ]; then
        KEY_FILE="${SSH_DIR}/id_ed25519"
    fi

    if [ -n "$KEY_FILE" ]; then
        key_perms=$(stat -c '%a' "$KEY_FILE")
        key_group=$(stat -c '%G' "$KEY_FILE")
        if [ "$key_group" = "www-data" ] && [ "$((key_perms % 100 / 10))" -ge 4 ]; then
            check "ok" "SSH key readable by www-data ($KEY_FILE)"
        else
            check "fail" "SSH key not readable by www-data" "group=$key_group perms=$key_perms"
            if [ "$AUTO_FIX" = true ]; then
                chown ubuntu:www-data "$KEY_FILE"
                chmod 640 "$KEY_FILE"
                echo "       → Fixed: chown/chmod on $KEY_FILE"
            fi
        fi
    else
        check "fail" "No SSH key found" "Expected id_ed25519_${CLIENT_ID} or id_ed25519 in $SSH_DIR"
    fi

    # Check known_hosts
    if [ -f "${SSH_DIR}/known_hosts" ] && grep -q github.com "${SSH_DIR}/known_hosts" 2>/dev/null; then
        check "ok" "known_hosts has github.com"
    else
        check "warn" "known_hosts missing github.com" "Will show warning on first fetch"
        if [ "$AUTO_FIX" = true ]; then
            ssh-keyscan github.com 2>/dev/null >> "${SSH_DIR}/known_hosts"
            chown ubuntu:www-data "${SSH_DIR}/known_hosts"
            chmod 664 "${SSH_DIR}/known_hosts"
            echo "       → Fixed: added github.com to known_hosts"
        fi
    fi
else
    check "fail" "SSH directory not found" "$SSH_DIR does not exist"
fi
echo ""

# ── 3. Webhook Handler ──
echo "🌐 Webhook Handler"
WEBHOOK_FILE="${CURRENT_LINK}/webhooks/deploy.php"
if [ -f "$WEBHOOK_FILE" ] || [ -L "$WEBHOOK_FILE" ]; then
    # Check if it's the mono-repo handler (not source version)
    handler_type=$(head -5 "$WEBHOOK_FILE" 2>/dev/null | grep -o "Mono-Repo\|Source Version")
    if [ "$handler_type" = "Mono-Repo" ]; then
        check "ok" "Webhook handler is Mono-Repo type"
    elif [ "$handler_type" = "Source Version" ]; then
        check "fail" "Webhook handler is SOURCE VERSION type" "Should be webhook-deploy.php"
    else
        check "warn" "Cannot determine webhook handler type" "Check manually"
    fi
else
    check "fail" "Webhook handler not found" "$WEBHOOK_FILE"
fi

# Check shared webhook-deploy.php
if [ -f "${SHARED_DIR}/webhook-deploy.php" ]; then
    check "ok" "shared/webhook-deploy.php exists"
else
    check "warn" "shared/webhook-deploy.php missing" "Will use current/cicd/server/ (chicken-and-egg)"
    if [ "$AUTO_FIX" = true ] && [ -f "${CURRENT_LINK}/cicd/server/webhook-deploy.php" ]; then
        cp "${CURRENT_LINK}/cicd/server/webhook-deploy.php" "${SHARED_DIR}/webhook-deploy.php"
        chown ubuntu:www-data "${SHARED_DIR}/webhook-deploy.php"
        echo "       → Fixed: copied webhook-deploy.php to shared/"
    fi
fi

# Check shared deploy-mono.sh
if [ -f "${SHARED_DIR}/deploy-mono.sh" ]; then
    check "ok" "shared/deploy-mono.sh exists"
else
    check "warn" "shared/deploy-mono.sh missing" "Will use current/cicd/server/ (chicken-and-egg)"
    if [ "$AUTO_FIX" = true ] && [ -f "${CURRENT_LINK}/cicd/server/deploy-mono.sh" ]; then
        cp "${CURRENT_LINK}/cicd/server/deploy-mono.sh" "${SHARED_DIR}/deploy-mono.sh"
        chown ubuntu:www-data "${SHARED_DIR}/deploy-mono.sh"
        chmod 755 "${SHARED_DIR}/deploy-mono.sh"
        echo "       → Fixed: copied deploy-mono.sh to shared/"
    fi
fi
echo ""

# ── 4. Git Permissions ──
echo "📂 Git Permissions (www-data write access)"
for env_dir in "$STAGING_DIR" "$CURRENT_LINK"; do
    env_name=$(basename "$env_dir")
    if [ -d "${env_dir}/.git" ]; then
        git_group=$(stat -c '%G' "${env_dir}/.git")
        git_perms=$(stat -c '%a' "${env_dir}/.git")
        if [ "$git_group" = "www-data" ] && [ "$((git_perms % 100 / 10))" -ge 7 ]; then
            check "ok" "${env_name}/.git group-writable by www-data"
        else
            check "fail" "${env_name}/.git not writable by www-data" "group=$git_group perms=$git_perms"
            if [ "$AUTO_FIX" = true ]; then
                chown -R ubuntu:www-data "${env_dir}/.git"
                chmod -R g+w "${env_dir}/.git"
                echo "       → Fixed: chown/chmod on ${env_dir}/.git"
            fi
        fi
    fi
done
echo ""

# ── 5. Apache Vhost ──
echo "🌍 Apache Configuration"
# Check by filename first (sites-enabled contains symlinks — grep -rl may not follow them)
VHOST_FILE=""
if [ -f "/etc/apache2/sites-enabled/${CLIENT_ID}.conf" ] || [ -L "/etc/apache2/sites-enabled/${CLIENT_ID}.conf" ]; then
    VHOST_FILE="/etc/apache2/sites-enabled/${CLIENT_ID}.conf"
else
    # Fallback: grep content (works if file is directly in sites-enabled, not symlink)
    VHOST_FILE=$(grep -rl "${CLIENT_ID}" /etc/apache2/sites-enabled/ 2>/dev/null | head -1)
fi

if [ -n "$VHOST_FILE" ]; then
    check "ok" "Apache vhost found: $(basename "$VHOST_FILE")"

    # Resolve symlink for content checks
    VHOST_REAL=$(readlink -f "$VHOST_FILE" 2>/dev/null || echo "$VHOST_FILE")

    # Check for maintenance Alias
    if grep -q "Alias /maintenance.html" "$VHOST_REAL" 2>/dev/null; then
        check "ok" "Maintenance Alias configured"
    else
        check "fail" "Maintenance Alias MISSING" "Maintenance page won't show during deploys"
    fi

    # Check webhook alias points to correct handler
    if grep -q "webhook-deploy.php" "$VHOST_REAL" 2>/dev/null; then
        check "ok" "Webhook Alias points to webhook-deploy.php"
    else
        check "warn" "No webhook Alias in vhost" "Webhook served from webroot"
    fi
else
    check "warn" "No Apache vhost found for ${CLIENT_ID}" "Check /etc/apache2/sites-enabled/"
fi
echo ""

# ── 6. Maintenance Page ──
echo "📄 Maintenance Page"
if [ -f "${SHARED_DIR}/maintenance.html" ]; then
    check "ok" "shared/maintenance.html exists"
else
    check "fail" "shared/maintenance.html missing" "No maintenance page during deploys"
    if [ "$AUTO_FIX" = true ] && [ -f "${CURRENT_LINK}/maintenance.html" ]; then
        cp "${CURRENT_LINK}/maintenance.html" "${SHARED_DIR}/maintenance.html"
        chown ubuntu:www-data "${SHARED_DIR}/maintenance.html"
        echo "       → Fixed: copied maintenance.html to shared/"
    fi
fi
echo ""

# ── 7. Config Files ──
echo "⚙️  Configuration"
[ -f "${SHARED_DIR}/config/global_configs.php" ] && check "ok" "global_configs.php exists" || check "fail" "global_configs.php missing" "DB connection will fail"
[ -f "${SHARED_DIR}/config/database.php" ] && check "ok" "database.php exists" || check "fail" "database.php missing" "CI database config missing"
echo ""

# ── Summary ──
echo "═══════════════════════════════════════════════════════════"
TOTAL=$((PASS + FAIL + WARN))
echo "📊 Results: $PASS passed, $FAIL failed, $WARN warnings (${TOTAL} checks)"
if [ $FAIL -eq 0 ]; then
    echo "✅ Client ${CLIENT_ID} is deployment-ready!"
else
    echo "❌ Client ${CLIENT_ID} has $FAIL issue(s) to fix"
    if [ "$AUTO_FIX" = false ]; then
        echo "   Run with --fix to auto-repair: bash validate-client.sh ${CLIENT_ID} --fix"
    fi
fi
echo "═══════════════════════════════════════════════════════════"

exit $FAIL
