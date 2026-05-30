---
description: "⚠️ DEPRECATED — Recipes are now read directly from the central GitHub repo via MCP. Local caches are gitignored. This workflow is kept for historical reference only."
---

# /pull-recipes — ⚠️ DEPRECATED

> [!CAUTION]
> **This workflow is DEPRECATED as of 2026-05-07.**
> Recipes are now read directly from the central `LOGIMAX-CLIENTS/bug-recipes` GitHub repo via MCP during every fix session (Step 0 of SKILL.md).
> Local `.agent/skills/bug-fix-engine/recipes/` directories are **gitignored** and no longer used.
> There is nothing to "pull to" — the AI reads from GitHub in real-time.
>
> **Do NOT run this workflow.** If you need offline access, use the local git clone of `bug-recipes` (sibling directory) directly.

## Original Purpose (Historical)

Pull ALL recipes from the central bug-recipes repo to the local `.agent/skills/bug-fix-engine/recipes/` directory. This was for:
- **Offline access** — work without GitHub API
- **Bulk sync** — catch up on all recipes created by other developers
- **New machine setup** — populate local cache from scratch

> [!NOTE]
> The central repo is auto-detected by scanning sibling directories for `.agent/sync-config.json`.
> Read that file to get `github_org` and `github_repo` for MCP calls.

> [!IMPORTANT]
> **This workflow is NO LONGER NEEDED.** The AI reads recipes from GitHub API in real-time during Step 0 of every fix session. Local caches are gitignored and deprecated.

## Steps

### Step 1: Detect Access Mode [Antigravity]

// turbo

Try GitHub MCP first. If unavailable, use git CLI.

### Step 2: Fetch Recipe List [Antigravity]

#### Mode 1 — GitHub MCP:
```
# First read sync-config.json to get {ORG} and {REPO}
mcp_github-mcp-server_get_file_contents(
    owner: "{ORG}",
    repo: "{REPO}",
    path: "recipes/"
)
```

#### Mode 2 — Git CLI fallback:
```powershell
# Auto-detect central repo location
$htdocsRoot = Split-Path $env:PROJECT_ROOT -Parent
$bugRecipesPath = Get-ChildItem -Path $htdocsRoot -Directory | Where-Object {
    Test-Path (Join-Path $_.FullName ".agent\sync-config.json")
} | Select-Object -First 1 -ExpandProperty FullName

if (-not $bugRecipesPath) {
    Write-Error "Central recipe repo not found. Clone it as a sibling of this project."
} else {
    Push-Location $bugRecipesPath
    git pull origin main
    Pop-Location
}
Get-ChildItem "$bugRecipesPath\recipes\*.md" | Select-Object Name, LastWriteTime
```

### Step 3: Sync to Local Cache [Antigravity]

// turbo

For each recipe file in the central repo:

1. Read content (MCP: `get_file_contents` for each file | Git: already local after pull)
2. Compare with local `.agent/skills/bug-fix-engine/recipes/` version
3. If central is newer or local doesn't exist → copy/overwrite local

```powershell
# $bugRecipesPath already set from Step 2
Copy-Item "$bugRecipesPath\recipes\*.md" ".agent\skills\bug-fix-engine\recipes\" -Force
```

### Step 4: Sync Playbooks + Patterns [Antigravity]

// turbo

Also sync playbooks and patterns:

```powershell
# $bugRecipesPath already set from Step 2
Copy-Item "$bugRecipesPath\playbooks\*.md" ".agent\skills\bug-fix-engine\debug_playbooks\" -Force
Copy-Item "$bugRecipesPath\patterns\COMMON_BUG_PATTERNS.md" "bug_report_AI\COMMON_BUG_PATTERNS.md" -Force
```

### Step 4a: Sync Roles [Antigravity]

// turbo

Sync role definitions (e.g., `debugger.md`) from central to local:

```powershell
# $bugRecipesPath already set from Step 2
$rolesDest = ".agent\skills\bug-fix-engine\roles"
if (-not (Test-Path $rolesDest)) { New-Item -ItemType Directory -Path $rolesDest -Force | Out-Null }
Copy-Item "$bugRecipesPath\roles\*.md" "$rolesDest\" -Force
```

### Step 5: Report [Antigravity]

```
✅ Recipe Sync Complete
   Source: {ORG}/{REPO} (from sync-config.json)
   Method: {MCP | Git CLI}
   
   Recipes:   {N} total, {M} new, {K} updated, {J} unchanged
   Playbooks: {N} synced
   Patterns:  COMMON_BUG_PATTERNS.md synced
   Roles:     {N} synced
   
   Local cache: .agent/skills/bug-fix-engine/recipes/
```

## When to Use

| Scenario | Use this? |
|----------|-----------|
| Starting a bug fix session | ❌ No — Step 0 reads from GitHub directly |
| New developer machine setup | ✅ Yes — populate local cache |
| Working offline / no internet | ✅ Yes — ensure local cache is current |
| Monthly maintenance | ✅ Yes — keep local in sync |
| After someone else reported pushing a recipe | ❌ No — Step 0 will pick it up automatically |
