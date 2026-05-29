#!/bin/bash
# =============================================================================
# SOURCE VERSION SERVER SETUP
# Sets up symlink deployment for all source environments
# Run on server: bash setup-source-environments.sh
# =============================================================================

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}"
echo "╔═══════════════════════════════════════════════════════════════╗"
echo "║     LOGIMAX Source Version - Multi-Environment Setup          ║"
echo "╚═══════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Configuration
BASE_PATH="/home/retaillogimaxind/public_html"
REPO_URL="git@github.com:Logimax-Technologies/retail_v5.git"

# Environment definitions
declare -A ENVS
ENVS["develop"]="test_etail_v3:develop"
ENVS["qa"]="QA:qa"
ENVS["support"]="Support:support"
ENVS["production"]="etail:main"

# =============================================================================
# FUNCTIONS
# =============================================================================

generate_secret() {
    openssl rand -hex 32
}

setup_environment() {
    local env_name=$1
    local folder_branch=$2
    IFS=':' read -r folder branch <<< "$folder_branch"
    
    local env_path="$BASE_PATH/$folder"
    local releases_path="${env_path}_releases"
    local shared_path="${env_path}_shared"
    local webhooks_path="$BASE_PATH/webhooks"
    
    echo -e "\n${BLUE}━━━ Setting up $env_name ($folder) ━━━${NC}"
    
    # Create directories
    echo -e "${YELLOW}Creating directories...${NC}"
    mkdir -p "$releases_path"
    mkdir -p "$shared_path/uploads"
    mkdir -p "$shared_path/logs"
    mkdir -p "$webhooks_path"
    
    # Clone first release if not exists
    if [ ! -d "$releases_path/v1" ]; then
        echo -e "${YELLOW}Cloning initial release...${NC}"
        git clone -b "$branch" "$REPO_URL" "$releases_path/v1"
    fi
    
    # Create symlink if not exists
    if [ ! -L "$env_path/current" ] && [ ! -d "$env_path" ]; then
        echo -e "${YELLOW}Creating symlink...${NC}"
        mkdir -p "$env_path"
        ln -sfn "$releases_path/v1" "$env_path/current"
    fi
    
    # Generate webhook secret
    local secret=$(generate_secret)
    
    # Create .htaccess config
    local htaccess_content="# CI/CD Environment: $env_name
SetEnv WEBHOOK_SECRET \"$secret\"
SetEnv DEPLOY_BRANCH \"$branch\"
SetEnv REPO_PATH \"$env_path\"
SetEnv RELEASES_PATH \"$releases_path\"
SetEnv SHARED_PATH \"$shared_path\"
SetEnv DEPLOY_SCRIPT \"$env_path/unified-deploy.sh\"
SetEnv LOG_FILE \"$webhooks_path/${folder}-deploy.log\"
SetEnv DEPLOYMENT_SERVER \"$env_name\"
SetEnv REPO_URL \"$REPO_URL\""

    echo -e "${YELLOW}Creating .htaccess config...${NC}"
    echo "$htaccess_content" > "$env_path/.htaccess.cicd"
    
    # Copy deployment scripts
    echo -e "${YELLOW}Copying deployment scripts...${NC}"
    
    # Print summary
    echo -e "${GREEN}✓ $env_name setup complete${NC}"
    echo "  Folder: $folder"
    echo "  Branch: $branch"
    echo "  Path: $env_path"
    echo -e "  Secret: ${YELLOW}$secret${NC}"
    echo ""
    echo "  📋 Add to .htaccess:"
    echo "  $htaccess_content" | head -3
    echo "  ..."
    
    # Return secret for later use
    echo "$env_name:$secret" >> /tmp/source_secrets.txt
}

# =============================================================================
# MAIN
# =============================================================================

# Clear secrets file
> /tmp/source_secrets.txt

echo -e "${YELLOW}This script will set up symlink deployment for:${NC}"
for env in "${!ENVS[@]}"; do
    IFS=':' read -r folder branch <<< "${ENVS[$env]}"
    echo "  - $env → $folder (branch: $branch)"
done
echo ""
read -p "Continue? (y/N): " confirm
if [[ ! "$confirm" =~ ^[Yy]$ ]]; then
    echo "Aborted."
    exit 0
fi

# Setup each environment
for env in develop qa support production; do
    setup_environment "$env" "${ENVS[$env]}"
done

# Summary
echo -e "\n${GREEN}╔═══════════════════════════════════════════════════════════════╗"
echo "║                    SETUP COMPLETE!                              ║"
echo "╚═══════════════════════════════════════════════════════════════╝${NC}"

echo -e "\n${BLUE}Generated Secrets (save these):${NC}"
cat /tmp/source_secrets.txt

echo -e "\n${YELLOW}GitHub Secrets to Add:${NC}"
while IFS=: read -r env secret; do
    env_upper=$(echo "$env" | tr '[:lower:]' '[:upper:]')
    echo "  ${env_upper}_WEBHOOK_SECRET = $secret"
done < /tmp/source_secrets.txt

echo -e "\n${YELLOW}Webhook URLs:${NC}"
echo "  DEVELOP_WEBHOOK_URL = https://retail.logimaxindia.com/webhooks/develop-deploy.php"
echo "  QA_WEBHOOK_URL = https://retail.logimaxindia.com/webhooks/qa-deploy.php"
echo "  SUPPORT_WEBHOOK_URL = https://retail.logimaxindia.com/webhooks/support-deploy.php"
echo "  PROD_WEBHOOK_URL = https://retail.logimaxindia.com/webhooks/prod-deploy.php"

echo -e "\n${BLUE}Next Steps:${NC}"
echo "1. Add the generated secrets to GitHub repository settings"
echo "2. Copy webhook-handler.php to each environment's webhooks folder"
echo "3. Add .htaccess.cicd content to each environment's .htaccess"
echo "4. Copy unified-deploy.sh and rollback.sh to each environment"

rm /tmp/source_secrets.txt
