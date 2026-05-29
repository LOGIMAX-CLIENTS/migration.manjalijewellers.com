#!/bin/bash
# =============================================================================
# MASTER CI/CD SETUP SCRIPT
# Automates CI/CD setup for existing LOGIMAX-CLIENTS repositories
# =============================================================================

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}"
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║       LOGIMAX CI/CD - Client Setup Automation             ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# =============================================================================
# CONFIGURATION
# =============================================================================
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
TEMPLATE_REPO="Logimax-Technologies/retail_v5"
CLIENT_ORG="LOGIMAX-CLIENTS"
REGISTRY_FILE="$SCRIPT_DIR/../clients/registry.json"

# Check prerequisites
check_prerequisites() {
    echo -e "${YELLOW}Checking prerequisites...${NC}"
    
    if ! command -v gh &> /dev/null; then
        echo -e "${RED}❌ GitHub CLI (gh) not installed${NC}"
        echo "   Install: https://cli.github.com/"
        exit 1
    fi
    
    if ! gh auth status &> /dev/null; then
        echo -e "${RED}❌ Not logged into GitHub CLI${NC}"
        echo "   Run: gh auth login"
        exit 1
    fi
    
    if ! command -v jq &> /dev/null; then
        echo -e "${YELLOW}⚠️  jq not installed (optional for registry parsing)${NC}"
    fi
    
    echo -e "${GREEN}✓ Prerequisites OK${NC}"
}

# =============================================================================
# SETUP SINGLE CLIENT
# =============================================================================
setup_client() {
    local client_repo="$1"
    local server_host="$2"
    local server_user="$3"
    local server_path="$4"
    
    echo ""
    echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}Setting up: ${GREEN}$client_repo${NC}"
    echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
    
    # Generate webhook secret
    local webhook_secret=$(openssl rand -hex 32)
    echo -e "${YELLOW}Generated webhook secret${NC}"
    
    # Construct webhook URL
    local webhook_url="https://${client_repo}/webhooks/deploy.php"
    
    # Step 1: Add GitHub Secrets
    echo -e "${YELLOW}Adding GitHub secrets...${NC}"
    
    gh secret set STAGING_WEBHOOK_URL \
        --repo "$CLIENT_ORG/$client_repo" \
        --body "$webhook_url" 2>/dev/null && echo -e "  ${GREEN}✓ STAGING_WEBHOOK_URL${NC}" || echo -e "  ${RED}✗ STAGING_WEBHOOK_URL${NC}"
    
    gh secret set STAGING_WEBHOOK_SECRET \
        --repo "$CLIENT_ORG/$client_repo" \
        --body "$webhook_secret" 2>/dev/null && echo -e "  ${GREEN}✓ STAGING_WEBHOOK_SECRET${NC}" || echo -e "  ${RED}✗ STAGING_WEBHOOK_SECRET${NC}"
    
    gh secret set PRODUCTION_WEBHOOK_URL \
        --repo "$CLIENT_ORG/$client_repo" \
        --body "$webhook_url" 2>/dev/null && echo -e "  ${GREEN}✓ PRODUCTION_WEBHOOK_URL${NC}" || echo -e "  ${RED}✗ PRODUCTION_WEBHOOK_URL${NC}"
    
    gh secret set PRODUCTION_WEBHOOK_SECRET \
        --repo "$CLIENT_ORG/$client_repo" \
        --body "$webhook_secret" 2>/dev/null && echo -e "  ${GREEN}✓ PRODUCTION_WEBHOOK_SECRET${NC}" || echo -e "  ${RED}✗ PRODUCTION_WEBHOOK_SECRET${NC}"
    
    # Step 2: Add workflow file to repo
    echo -e "${YELLOW}Adding workflow file...${NC}"
    
    # Clone, add workflow, push
    local temp_dir=$(mktemp -d)
    cd "$temp_dir"
    
    if gh repo clone "$CLIENT_ORG/$client_repo" . -- --depth 1 2>/dev/null; then
        mkdir -p .github/workflows
        cp "$SCRIPT_DIR/../templates/client/deploy.yml" .github/workflows/deploy.yml
        
        git add .github/
        git commit -m "Add CI/CD workflow" 2>/dev/null || echo "  (workflow already exists)"
        git push 2>/dev/null && echo -e "  ${GREEN}✓ Workflow added${NC}" || echo -e "  ${YELLOW}⚠️ Could not push (check permissions)${NC}"
    else
        echo -e "  ${RED}✗ Could not clone repo${NC}"
    fi
    
    cd - > /dev/null
    rm -rf "$temp_dir"
    
    # Step 3: Generate server config
    echo -e "${YELLOW}Server configuration:${NC}"
    echo ""
    echo -e "${GREEN}# Add to webhooks/.htaccess on server:${NC}"
    echo "SetEnv WEBHOOK_SECRET \"$webhook_secret\""
    echo "SetEnv REPO_PATH \"$server_path\""
    echo "SetEnv DEPLOY_SCRIPT \"$server_path/deploy.sh\""
    echo ""
    
    # Save to config file
    local config_file="$SCRIPT_DIR/../clients/configs/${client_repo}.env"
    mkdir -p "$SCRIPT_DIR/../clients/configs"
    cat > "$config_file" << EOF
# CI/CD Configuration for $client_repo
# Generated: $(date)

WEBHOOK_SECRET="$webhook_secret"
WEBHOOK_URL="$webhook_url"
REPO_PATH="$server_path"
DEPLOY_SCRIPT="$server_path/deploy.sh"
EOF
    echo -e "${GREEN}✓ Config saved to: clients/configs/${client_repo}.env${NC}"
}

# =============================================================================
# SETUP SERVER (via SSH)
# =============================================================================
setup_server() {
    local server_host="$1"
    local server_user="$2"
    local server_path="$3"
    local webhook_secret="$4"
    
    echo -e "${YELLOW}Setting up server via SSH...${NC}"
    
    ssh "${server_user}@${server_host}" << REMOTE_SCRIPT
        # Create directories
        mkdir -p "$server_path/webhooks"
        mkdir -p "$(dirname $server_path)/releases"
        mkdir -p "$(dirname $server_path)/shared/{logs,uploads,img}"
        
        # Create .htaccess
        cat > "$server_path/webhooks/.htaccess" << 'HTACCESS'
SetEnv WEBHOOK_SECRET "$webhook_secret"
SetEnv REPO_PATH "$server_path"
SetEnv DEPLOY_SCRIPT "$server_path/deploy.sh"
HTACCESS
        
        chmod +x "$server_path/deploy.sh" 2>/dev/null || true
        echo "✓ Server setup complete"
REMOTE_SCRIPT
}

# =============================================================================
# COPY FILES TO SERVER
# =============================================================================
copy_files_to_server() {
    local server_host="$1"
    local server_user="$2"
    local server_path="$3"
    
    echo -e "${YELLOW}Copying files to server...${NC}"
    
    scp "$SCRIPT_DIR/unified-deploy.sh" "${server_user}@${server_host}:${server_path}/deploy.sh"
    scp "$SCRIPT_DIR/rollback.sh" "${server_user}@${server_host}:${server_path}/rollback.sh"
    scp "$SCRIPT_DIR/webhook-handler.php" "${server_user}@${server_host}:${server_path}/webhooks/deploy.php"
    scp "$SCRIPT_DIR/../maintenance.html" "${server_user}@${server_host}:${server_path}/maintenance.html"
    
    ssh "${server_user}@${server_host}" "chmod +x ${server_path}/deploy.sh ${server_path}/rollback.sh"
    
    echo -e "${GREEN}✓ Files copied${NC}"
}

# =============================================================================
# BATCH SETUP FROM REGISTRY
# =============================================================================
batch_setup() {
    echo -e "${YELLOW}Batch setup from registry...${NC}"
    
    if [ ! -f "$REGISTRY_FILE" ]; then
        echo -e "${RED}Registry file not found: $REGISTRY_FILE${NC}"
        exit 1
    fi
    
    # Parse registry and setup each client
    if command -v jq &> /dev/null; then
        local clients=$(jq -r '.clients[].repo' "$REGISTRY_FILE")
        local count=$(echo "$clients" | wc -l)
        
        echo -e "Found ${GREEN}$count${NC} clients in registry"
        echo ""
        read -p "Setup GitHub secrets for all clients? (y/N): " confirm
        
        if [[ "$confirm" =~ ^[Yy]$ ]]; then
            for repo in $clients; do
                setup_client "$repo" "" "" "/home/user/public_html"
            done
        fi
    else
        echo -e "${RED}jq required for batch setup${NC}"
    fi
}

# =============================================================================
# INTERACTIVE MENU
# =============================================================================
show_menu() {
    echo ""
    echo -e "${BLUE}Select an option:${NC}"
    echo ""
    echo "  1) Setup single client (GitHub secrets + workflow)"
    echo "  2) Setup single client + copy files to server"
    echo "  3) Batch setup all clients from registry (secrets only)"
    echo "  4) Copy files to server only"
    echo "  5) Generate server config file"
    echo "  6) Exit"
    echo ""
    read -p "Enter choice [1-6]: " choice
    
    case $choice in
        1)
            read -p "Client repo name (e.g., client.example.com): " repo
            setup_client "$repo" "" "" "/home/user/public_html"
            ;;
        2)
            read -p "Client repo name: " repo
            read -p "Server host (e.g., client.example.com): " host
            read -p "Server user (e.g., root): " user
            read -p "Server path (e.g., /home/user/public_html): " path
            setup_client "$repo" "$host" "$user" "$path"
            copy_files_to_server "$host" "$user" "$path"
            ;;
        3)
            batch_setup
            ;;
        4)
            read -p "Server host: " host
            read -p "Server user: " user
            read -p "Server path: " path
            copy_files_to_server "$host" "$user" "$path"
            ;;
        5)
            read -p "Client repo name: " repo
            read -p "Server path: " path
            local secret=$(openssl rand -hex 32)
            echo ""
            echo "# .htaccess config:"
            echo "SetEnv WEBHOOK_SECRET \"$secret\""
            echo "SetEnv REPO_PATH \"$path\""
            echo "SetEnv DEPLOY_SCRIPT \"$path/deploy.sh\""
            ;;
        6)
            echo "Goodbye!"
            exit 0
            ;;
        *)
            echo -e "${RED}Invalid option${NC}"
            ;;
    esac
}

# =============================================================================
# MAIN
# =============================================================================
main() {
    check_prerequisites
    
    if [ -n "$1" ]; then
        # Direct command mode
        case "$1" in
            --client)
                setup_client "$2" "" "" "${3:-/home/user/public_html}"
                ;;
            --batch)
                batch_setup
                ;;
            --help)
                echo "Usage: $0 [options]"
                echo ""
                echo "Options:"
                echo "  --client <repo> [path]  Setup single client"
                echo "  --batch                 Setup all clients from registry"
                echo "  --help                  Show this help"
                echo ""
                echo "Without options, runs interactive menu."
                ;;
            *)
                echo "Unknown option: $1"
                echo "Run '$0 --help' for usage"
                ;;
        esac
    else
        # Interactive menu
        while true; do
            show_menu
        done
    fi
}

main "$@"
