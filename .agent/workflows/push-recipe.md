---
description: Push a new or updated bug fix recipe to the central LOGIMAX-CLIENTS/bug-recipes GitHub repo
---

# /push-recipe — Push Recipe to Central Repo

## Purpose

After fixing a bug and creating a recipe, push it to the central recipe repo so ALL developers across ALL repos can use it immediately.

> [!NOTE]
> The central repo's GitHub org/repo are read from `.agent/sync-config.json` in the central repo.
> Auto-detect it by scanning sibling directories for that file.

## Prerequisites

- Recipe file exists locally (just created during a fix)
- Recipe follows `_TEMPLATE.md` format (has all required sections)

## Input

- `{RECIPE_FILE}` — Path to the local recipe `.md` file to push
- `{RECIPE_NAME}` — Filename for the recipe (e.g., `recipe_journal_reversal.md`)

If user doesn't provide these, the recipe should have been created in-memory during the fix session — prompt the user to provide the recipe content.

## Steps

### Step 1: Validate Recipe Format [Antigravity]

// turbo

Read the recipe file and verify it has ALL required sections:

| Section | Required | Check |
|---------|----------|-------|
| `## Metadata` | ✅ | Has Pattern ID, Severity, Modules Affected, Auto-fixable |
| `## Client Scope` | ✅ | Has "Applies to" field |
| `## Created By` | ✅ | Has Developer, Client, Date |
| `## Symptom` | ✅ | Non-empty |
| `## Root Cause` | ✅ | Non-empty |
| `## Detection` | ✅ | Has grep/search command |
| `## Files` | ✅ | Lists at least one file |
| `## Fix` | ✅ | Has both `### Before` and `### After` blocks |
| `## Verification` | ✅ | Has at least one step |

If any required section is missing → **add placeholder and warn developer** before proceeding.

### Step 2: Check for Duplicates [Antigravity]

#### Mode 1 — GitHub MCP:
```
# Read sync-config.json first to get {ORG} and {REPO}
mcp_github-mcp-server_get_file_contents(
    owner: "{ORG}",
    repo: "{REPO}",
    path: "recipes/"
)
```
Check if a file with the same name already exists.

#### Mode 2 — Git CLI fallback:
```powershell
# Auto-detect central repo
$htdocsRoot = Split-Path $env:PROJECT_ROOT -Parent
$bugRecipesPath = Get-ChildItem -Path $htdocsRoot -Directory | Where-Object {
    Test-Path (Join-Path $_.FullName ".agent\sync-config.json")
} | Select-Object -First 1 -ExpandProperty FullName

if (-not $bugRecipesPath) {
    Write-Error "Central recipe repo not found."
}
Push-Location $bugRecipesPath
git pull origin main
Test-Path "recipes/{RECIPE_NAME}"
Pop-Location
```

| Result | Action |
|--------|--------|
| File doesn't exist | Proceed to Step 3 (new recipe) |
| File exists, content SAME | Skip — already pushed |
| File exists, content DIFFERENT | Show diff to developer, ask: update or rename? |

### Step 3: Push to Central Repo [Antigravity]

#### Mode 1 — GitHub MCP:
```
# Read sync-config.json for {ORG} and {REPO}
mcp_github-mcp-server_create_or_update_file(
    owner: "{ORG}",
    repo: "{REPO}",
    path: "recipes/{RECIPE_NAME}",
    content: "{BASE64_ENCODED_CONTENT}",
    message: "recipe: {RECIPE_NAME} - {PATTERN_ID}",
    branch: "main"
)
```

#### Mode 2 — Git CLI fallback:
```powershell
# $bugRecipesPath already set from Step 2
Copy-Item "{RECIPE_FILE}" "$bugRecipesPath\recipes\{RECIPE_NAME}" -Force
Push-Location $bugRecipesPath
git add "recipes/{RECIPE_NAME}"
git commit -m "recipe: {RECIPE_NAME} - {PATTERN_ID}"
git push origin main
Pop-Location
```

### Step 4: Confirm [Antigravity]

```
✅ Recipe pushed to central repo
   Name: {RECIPE_NAME}
   Pattern: {PATTERN_ID}
   Repo: {ORG}/{REPO} (from sync-config.json)
   Method: {MCP | Git CLI}
   
   This recipe is now available to ALL developers immediately.
   No local cache created — central repo is the sole source of truth.
```

## Important Rules

1. **Always validate format before pushing** — incomplete recipes waste everyone's time
2. **Never overwrite without asking** — existing recipes may have been improved by other developers
3. **One recipe per push** — don't bundle multiple recipes
4. **Use consistent naming**: `recipe_<descriptive_name>.md` (lowercase, underscores)
5. **Never write recipes to local client repos** — the central `LOGIMAX-CLIENTS/bug-recipes` repo is the sole destination. Local `.agent/skills/bug-fix-engine/recipes/` is gitignored and deprecated.
