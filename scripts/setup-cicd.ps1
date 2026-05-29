# =============================================================================
# LOGIMAX CI/CD - Client Setup Automation (PowerShell)
# Run from retail_v5/scripts directory
# =============================================================================

param(
    [string]$Client = "",
    [switch]$Batch,
    [switch]$Help
)

$ErrorActionPreference = "Stop"

# Colors
function Write-Color($text, $color) {
    Write-Host $text -ForegroundColor $color
}

# Header
Write-Host ""
Write-Color "╔═══════════════════════════════════════════════════════════╗" Cyan
Write-Color "║       LOGIMAX CI/CD - Client Setup Automation             ║" Cyan
Write-Color "╚═══════════════════════════════════════════════════════════╝" Cyan
Write-Host ""

# =============================================================================
# PREREQUISITES CHECK
# =============================================================================
function Test-Prerequisites {
    Write-Color "Checking prerequisites..." Yellow
    
    # Check GitHub CLI
    if (!(Get-Command "gh" -ErrorAction SilentlyContinue)) {
        Write-Color "❌ GitHub CLI (gh) not installed" Red
        Write-Host "   Install: winget install GitHub.cli"
        exit 1
    }
    
    # Check auth
    $authStatus = gh auth status 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Color "❌ Not logged into GitHub CLI" Red
        Write-Host "   Run: gh auth login"
        exit 1
    }
    
    Write-Color "✓ Prerequisites OK" Green
}

# =============================================================================
# SETUP SINGLE CLIENT
# =============================================================================
function Setup-Client {
    param([string]$RepoName, [string]$ServerPath = "/home/user/public_html")
    
    Write-Host ""
    Write-Color "═══════════════════════════════════════════════════════════" Cyan
    Write-Color "Setting up: $RepoName" Green
    Write-Color "═══════════════════════════════════════════════════════════" Cyan
    
    # Generate webhook secret
    $bytes = New-Object byte[] 32
    [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
    $webhookSecret = [BitConverter]::ToString($bytes).Replace("-", "").ToLower()
    
    Write-Color "Generated webhook secret" Yellow
    
    # Webhook URL
    $webhookUrl = "https://$RepoName/webhooks/deploy.php"
    
    # Add GitHub Secrets
    Write-Color "Adding GitHub secrets..." Yellow
    
    $secrets = @{
        "STAGING_WEBHOOK_URL" = $webhookUrl
        "STAGING_WEBHOOK_SECRET" = $webhookSecret
        "PRODUCTION_WEBHOOK_URL" = $webhookUrl
        "PRODUCTION_WEBHOOK_SECRET" = $webhookSecret
    }
    
    foreach ($secret in $secrets.GetEnumerator()) {
        try {
            echo $secret.Value | gh secret set $secret.Key --repo "LOGIMAX-CLIENTS/$RepoName" 2>$null
            Write-Color "  ✓ $($secret.Key)" Green
        } catch {
            Write-Color "  ✗ $($secret.Key)" Red
        }
    }
    
    # Add workflow file
    Write-Color "Adding workflow file..." Yellow
    
    $tempDir = New-TemporaryFile | % { Remove-Item $_; mkdir $_ }
    Push-Location $tempDir
    
    try {
        gh repo clone "LOGIMAX-CLIENTS/$RepoName" . -- --depth 1 2>$null
        
        if (Test-Path ".git") {
            New-Item -ItemType Directory -Path ".github/workflows" -Force | Out-Null
            Copy-Item "$PSScriptRoot\..\templates\client\deploy.yml" ".github\workflows\deploy.yml"
            
            git add .github/
            git commit -m "Add CI/CD workflow" 2>$null
            git push 2>$null
            Write-Color "  ✓ Workflow added" Green
        }
    } catch {
        Write-Color "  ⚠️ Could not add workflow" Yellow
    }
    
    Pop-Location
    Remove-Item $tempDir -Recurse -Force -ErrorAction SilentlyContinue
    
    # Save config
    $configDir = "$PSScriptRoot\..\clients\configs"
    New-Item -ItemType Directory -Path $configDir -Force | Out-Null
    
    $config = @"
# CI/CD Configuration for $RepoName
# Generated: $(Get-Date)

WEBHOOK_SECRET="$webhookSecret"
WEBHOOK_URL="$webhookUrl"
REPO_PATH="$ServerPath"
DEPLOY_SCRIPT="$ServerPath/deploy.sh"
"@
    
    $config | Out-File "$configDir\$RepoName.env" -Encoding UTF8
    
    Write-Host ""
    Write-Color "Server Configuration:" Yellow
    Write-Host ""
    Write-Host "# Add to webhooks/.htaccess on server:"
    Write-Host "SetEnv WEBHOOK_SECRET `"$webhookSecret`""
    Write-Host "SetEnv REPO_PATH `"$ServerPath`""
    Write-Host "SetEnv DEPLOY_SCRIPT `"$ServerPath/deploy.sh`""
    Write-Host ""
    Write-Color "✓ Config saved to: clients/configs/$RepoName.env" Green
}

# =============================================================================
# BATCH SETUP
# =============================================================================
function Setup-Batch {
    $registryFile = "$PSScriptRoot\..\clients\registry.json"
    
    if (!(Test-Path $registryFile)) {
        Write-Color "❌ Registry file not found: $registryFile" Red
        return
    }
    
    $registry = Get-Content $registryFile | ConvertFrom-Json
    $clients = $registry.clients
    
    Write-Color "Found $($clients.Count) clients in registry" Green
    Write-Host ""
    
    $confirm = Read-Host "Setup GitHub secrets for all clients? (y/N)"
    
    if ($confirm -match "^[Yy]$") {
        foreach ($client in $clients) {
            try {
                Setup-Client -RepoName $client.repo
            } catch {
                Write-Color "Failed: $($client.repo) - $_" Red
            }
        }
    }
}

# =============================================================================
# INTERACTIVE MENU
# =============================================================================
function Show-Menu {
    Write-Host ""
    Write-Color "Select an option:" Cyan
    Write-Host ""
    Write-Host "  1) Setup single client"
    Write-Host "  2) Batch setup all clients from registry"
    Write-Host "  3) Generate config file only"
    Write-Host "  4) Exit"
    Write-Host ""
    
    $choice = Read-Host "Enter choice [1-4]"
    
    switch ($choice) {
        "1" {
            $repo = Read-Host "Client repo name (e.g., client.example.com)"
            $path = Read-Host "Server path (default: /home/user/public_html)"
            if (!$path) { $path = "/home/user/public_html" }
            Setup-Client -RepoName $repo -ServerPath $path
        }
        "2" {
            Setup-Batch
        }
        "3" {
            $repo = Read-Host "Client repo name"
            $bytes = New-Object byte[] 32
            [System.Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($bytes)
            $secret = [BitConverter]::ToString($bytes).Replace("-", "").ToLower()
            
            Write-Host ""
            Write-Host "# .htaccess config:"
            Write-Host "SetEnv WEBHOOK_SECRET `"$secret`""
            Write-Host "SetEnv REPO_PATH `"/home/user/public_html`""
        }
        "4" {
            Write-Host "Goodbye!"
            exit 0
        }
    }
}

# =============================================================================
# MAIN
# =============================================================================
Test-Prerequisites

if ($Help) {
    Write-Host "Usage: .\setup-cicd.ps1 [-Client <repo>] [-Batch] [-Help]"
    Write-Host ""
    Write-Host "Options:"
    Write-Host "  -Client <repo>  Setup single client"
    Write-Host "  -Batch          Setup all clients from registry"
    Write-Host "  -Help           Show this help"
    exit 0
}

if ($Client) {
    Setup-Client -RepoName $Client
}
elseif ($Batch) {
    Setup-Batch
}
else {
    while ($true) {
        Show-Menu
    }
}
