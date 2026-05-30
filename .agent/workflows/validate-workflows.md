---
description: Validate all workflows are internally consistent — auto-setup developer environment, check cross-references, file references, step numbering
version: 2.0
last_updated: 2026-03-07
---

# Validate Workflows

> **Purpose**: (1) Auto-setup a developer's local environment on first run, and (2) self-test the workflow system for internal consistency.
> **When to use**: First time on a new machine, after modifying any workflow, before onboarding a new developer, or monthly health check.

## Steps

### Step 0: Pre-Flight Auto-Setup [Antigravity]

> [!IMPORTANT]
> This step auto-detects all machine-specific values and saves them to `.agent/.env`.
> If `.agent/.env` already exists with all values populated, **skip to Step 1**.

#### 0a. Detect PROJECT_ROOT
// turbo
```powershell
git rev-parse --show-toplevel
```
Save result as `PROJECT_ROOT` in `.agent/.env`.

#### 0b. Detect PHP_PATH
// turbo
```powershell
# Windows
(Get-Command php -ErrorAction SilentlyContinue).Source

# Linux/Mac
# which php
```
- If found → save as `PHP_PATH`
- If not found → WARN: "PHP not found in PATH. Set PHP_PATH manually in .agent/.env"

#### 0c. Detect ISSUE_PLATFORM from Git Remote
// turbo
```powershell
git remote -v
```
Parse the remote URL:
- Contains `github.com` → `ISSUE_PLATFORM=github`
  - Parse owner/repo: `github.com/{OWNER}/{REPO}.git` → save `REPO_OWNER`, `REPO_NAME`
- Contains `13.127.29.211` or other non-GitHub host → `ISSUE_PLATFORM=gitea`
  - Parse: `http://{HOST}/{OWNER}/{REPO}.git` → save `GITEA_URL=http://{HOST}`, `GITEA_API=http://{HOST}/api/v1`, `REPO_OWNER`, `REPO_NAME`
- Multiple remotes → use `origin`

#### 0d. Derive LOCALHOST_URL
// turbo
From `PROJECT_ROOT`, extract the path after the web server root:
```
PROJECT_ROOT = c:\xampp\htdocs\AMS\AMS-RetailAdmin
Web root      = c:\xampp\htdocs\
Relative      = AMS/AMS-RetailAdmin
LOCALHOST_URL = http://localhost/{relative}/index.php
```

#### 0e. Check/Request Issue Tracker Token
If `ISSUE_PLATFORM=gitea`:
1. Check if `GITEA_TOKEN` exists in `.agent/.env`
2. If missing → Ask developer:
   ```
   ⚠️ First-time setup: Gitea API token needed.
   1. Go to {GITEA_URL}/user/settings/applications
   2. Create token "antigravity-bot" with issue read/write
   3. Paste token here:
   ```
3. Save to `.agent/.env` as `GITEA_TOKEN={pasted}`
4. Verify: `GET {GITEA_API}/user` → should return username

If `ISSUE_PLATFORM=github`:
- No token needed — GitHub MCP server handles auth via IDE settings
- Verify: call `github-mcp-server` → `get_me` → if fails, WARN developer to set up GitHub MCP

#### 0f. Write .env File
// turbo
Write all detected values to `.agent/.env`:
```
PROJECT_ROOT={detected}
PHP_PATH={detected}
LOCALHOST_URL={derived}
ISSUE_PLATFORM={detected}
REPO_OWNER={parsed}
REPO_NAME={parsed}
GITEA_URL={parsed}       # only if gitea
GITEA_API={parsed}       # only if gitea
GITEA_TOKEN={provided}   # only if gitea
```

#### 0g. Display Setup Summary
```
✅ Environment auto-configured:
   Project root:  {PROJECT_ROOT}
   PHP:           {PHP_PATH}
   Platform:      {ISSUE_PLATFORM} ({REPO_OWNER}/{REPO_NAME})
   Localhost:     {LOCALHOST_URL}
   Token:         {GITEA_TOKEN status or "GitHub MCP"}
   
   Saved to: .agent/.env (gitignored — your copy only)
```

### Step 1: Check All Workflow Files Exist [Antigravity]

// turbo
List all `.md` files in `.agent/workflows/`. Verify these core workflows exist:

| #   | Workflow                 | File                          |
| --- | ------------------------ | ----------------------------- |
| 1   | Build Module Brain       | `build-module-brain.md`       |
| 2   | Module Bug Audit         | `module-bug-audit.md`         |
| 3   | Bug Intake & Triage      | `bug-intake-triage.md`        |
| 4   | Fix Single Bug           | `fix-single-bug.md`           |
| 5   | Fix Architecture Bug     | `fix-architecture-bug.md`     |
| 6   | Fix Business Bug         | `fix-business-bug.md`         |
| 7   | Test & Verify            | `test-and-verify.md`          |
| 8   | Learn & Improve          | `learn-and-improve.md`        |
| 9   | Manage Bug Patterns      | `manage-bug-patterns.md`      |
| 10  | GitHub Bug Tracking      | `github-bug-tracking.md`      |
| 11  | Setup New Client         | `setup-new-client.md`         |
| 12  | Setup Existing Client    | `setup-existing-client.md`    |
| 13  | Sprint Status            | `sprint-status.md`            |
| 14  | System Health            | `system-health.md`            |
| 15  | Overall Bug Dashboard    | `overall-bug-dashboard.md`    |
| 16  | Validate Workflows       | `validate-workflows.md`       |
| 17  | Build System Brain       | `build-system-brain.md`       |

### Step 2: Check Frontmatter [Antigravity]

// turbo
For each workflow file, verify:

- [ ] Has `description` field
- [ ] Has `version` field
- [ ] Has `last_updated` field
- [ ] `last_updated` is not more than 90 days old (warn if stale)

### Step 3: Check Cross-References [Antigravity]

// turbo
Scan all workflow files for `/workflow-name` references. Verify each referenced workflow exists:

Common references to check:

- `/fix-single-bug` → should reference `/bug-intake-triage`, `/fix-architecture-bug`, `/fix-business-bug`, `/test-and-verify`, `/learn-and-improve`
- `/learn-and-improve` → should reference `/fix-single-bug`
- `/module-bug-audit` → should reference `/build-module-brain`, `/fix-single-bug`
- `/bug-intake-triage` → should reference `/github-bug-tracking`

### Step 4: Check File Path References [Antigravity]

// turbo
Scan for hardcoded file paths in workflows. Flag any that don't use config variables:

| Pattern                 | Should Be                                     |
| ----------------------- | --------------------------------------------- |
| `C:\php8\php.exe`       | `{PHP_PATH}` — reference `.agent/config.md`   |
| `admin/tests/`          | `{TEST_DIR}` — reference `.agent/config.md`   |
| `Logimax-Technologies`  | `{REPO_OWNER}` — reference `.agent/config.md` |
| `etail_development_src` | `{REPO_NAME}` — reference `.agent/config.md`  |

### Step 5: Check Supporting Files [Antigravity]

// turbo
Verify all referenced files/templates exist:

| File                                             | Purpose                          | Required |
| ------------------------------------------------ | -------------------------------- | -------- |
| `.agent/config.md`                               | Central config                   | ✅       |
| `.agent/GEMINI.md`                               | Project rules                    | ✅       |
| `bug_report_AI/COMMON_BUG_PATTERNS.md`           | Pattern library                  | ✅       |
| `bug_report_AI/ACTIVE_BUGS.md`                   | Live bug tracker                 | ✅       |
| `bug_report_AI/ROLLBACK_REGISTRY.md`             | Rollback index                   | ✅       |
| `knowledge_brain/_TEMPLATE/COVERAGE_TRACKER.md`  | Brain coverage tracking template | ✅       |
| `knowledge_brain/_TEMPLATE/FORENSIC_TEMPLATE.md` | Investigation template           | ✅       |
| `knowledge_brain/_TEMPLATE/INVARIANT_MATRIX.md`  | Variant grid template            | ✅       |
| `bug_report_AI/SOP/FINAL_SOP_BUG_REMEDIATION.md` | Master SOP                       | ✅       |

### Step 6: Generate Validation Report [Antigravity]

```markdown
# Workflow Validation Report — {DATE}

## Files

- Workflow files found: {N}/17
- Missing: {list or "None"}

## Frontmatter

- All have description: {✅/❌}
- All have version: {✅/❌}
- Stale workflows (>90 days): {list or "None"}

## Cross-References

- Total references checked: {N}
- Broken references: {list or "None"}

## Hardcoded Paths

- Hardcoded paths found: {N}
- {list of files with hardcoded paths}

## Supporting Files

- Required files present: {N}/9
- Missing: {list or "None"}

## Overall: {✅ PASS / ⚠️ WARNINGS / ❌ FAIL}
```
