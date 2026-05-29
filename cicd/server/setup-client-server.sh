#!/bin/bash
# =============================================================================
# CLIENT SERVER SETUP SCRIPT
# Run this on client server to set up deployment infrastructure
# =============================================================================

set -e

echo "🚀 Logimax CI/CD Server Setup"
echo "=============================="

# Configuration
read -p "Enter client domain (e.g., client.example.com): " CLIENT_DOMAIN
read -p "Enter web root path (e.g., /var/www/html): " WEB_ROOT
read -p "Enter webhook secret (generate a random string): " WEBHOOK_SECRET
read -p "Enter deploy branch (e.g., main): " DEPLOY_BRANCH

# Validate
if [ -z "$CLIENT_DOMAIN" ] || [ -z "$WEB_ROOT" ] || [ -z "$WEBHOOK_SECRET" ]; then
    echo "❌ All fields are required"
    exit 1
fi

# Create directories
echo "📁 Creating directory structure..."
mkdir -p "$WEB_ROOT/webhooks"
mkdir -p "$WEB_ROOT/../releases"
mkdir -p "$WEB_ROOT/../shared/logs"
mkdir -p "$WEB_ROOT/../shared/uploads"

# Copy webhook handler
echo "📄 Setting up webhook handler..."
cat > "$WEB_ROOT/webhooks/deploy.php" << 'WEBHOOK_SCRIPT'
<?php
// Minimal webhook handler - Copy full version from retail_v5/scripts/webhook-handler.php
header('Content-Type: application/json');

$secret = getenv('WEBHOOK_SECRET') ?: '';
$input = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

if (empty($secret)) die(json_encode(['error' => 'Secret not configured']));
if (!hash_equals('sha256=' . hash_hmac('sha256', $input, $secret), $signature)) {
    http_response_code(403);
    die(json_encode(['error' => 'Invalid signature']));
}

$data = json_decode($input, true);
$mode = $data['deploy_mode'] ?? 'simple';
$maintenance = $data['maintenance_mode'] ?? false;

// Set environment and trigger deploy
putenv("DEPLOY_MODE=$mode");
putenv("MAINTENANCE_MODE=" . ($maintenance ? 'true' : 'false'));
putenv("DEPLOY_BRANCH=" . basename($data['ref'] ?? 'main'));
putenv("REPO_PATH=" . getenv('REPO_PATH'));

shell_exec("bash " . getenv('DEPLOY_SCRIPT') . " 2>&1 &");

echo json_encode(['status' => 'triggered', 'mode' => $mode]);
WEBHOOK_SCRIPT

# Set permissions
echo "🔐 Setting permissions..."
chmod 755 "$WEB_ROOT/webhooks"
chmod 644 "$WEB_ROOT/webhooks/deploy.php"

# Create environment file
echo "⚙️ Creating environment configuration..."
cat > "$WEB_ROOT/../.deploy-env" << ENV_FILE
# Logimax CI/CD Configuration
# Source this file or set these as system environment variables

export WEBHOOK_SECRET="$WEBHOOK_SECRET"
export REPO_PATH="$WEB_ROOT"
export DEPLOY_SCRIPT="$WEB_ROOT/deploy.sh"
export DEPLOY_BRANCH="$DEPLOY_BRANCH"
export LOG_FILE="$WEB_ROOT/../shared/logs/deploy.log"
ENV_FILE

chmod 600 "$WEB_ROOT/../.deploy-env"

# Summary
echo ""
echo "✅ Setup Complete!"
echo "=================="
echo ""
echo "📋 Next Steps:"
echo ""
echo "1. Copy the full deploy script to: $WEB_ROOT/deploy.sh"
echo "   From: retail_v5/scripts/unified-deploy.sh"
echo ""
echo "2. Copy maintenance page to: $WEB_ROOT/maintenance.html"
echo "   From: retail_v5/maintenance.html"
echo ""
echo "3. Add to PHP environment (php.ini or .htaccess):"
echo "   SetEnv WEBHOOK_SECRET \"$WEBHOOK_SECRET\""
echo "   SetEnv REPO_PATH \"$WEB_ROOT\""
echo "   SetEnv DEPLOY_SCRIPT \"$WEB_ROOT/deploy.sh\""
echo ""
echo "4. Add GitHub secrets in client repo:"
echo "   STAGING_WEBHOOK_URL: https://$CLIENT_DOMAIN/webhooks/deploy.php"
echo "   STAGING_WEBHOOK_SECRET: $WEBHOOK_SECRET"
echo "   PRODUCTION_WEBHOOK_URL: https://$CLIENT_DOMAIN/webhooks/deploy.php"
echo "   PRODUCTION_WEBHOOK_SECRET: $WEBHOOK_SECRET"
echo ""
echo "5. Copy workflow to client repo:"
echo "   From: retail_v5/templates/client/deploy.yml"
echo "   To: .github/workflows/deploy.yml"
echo ""
echo "🔒 Webhook Secret (save this): $WEBHOOK_SECRET"
