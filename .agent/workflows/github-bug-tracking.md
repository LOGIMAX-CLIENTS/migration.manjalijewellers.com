---
description: Set up project issue tracking (labels, milestones) and manage bug issues via Gitea or GitHub
version: 2.0
last_updated: 2026-03-07
---

# Bug Tracking Workflow (Platform-Aware)

> **Purpose**: Automates bug lifecycle tracking via Gitea Issues or GitHub Issues.
> **Platform**: Determined by `{ISSUE_PLATFORM}` in `.agent/config.md` (`gitea` or `github`)

## Pre-Flight: Auto-Setup (Run Once Per Developer)

> [!IMPORTANT]
> This runs automatically the **first time** any developer uses a bug workflow. No manual setup needed.

### Step 0a: Detect Platform
// turbo
1. Read `{ISSUE_PLATFORM}` from `.agent/config.md`
2. If not set, auto-detect from git remote:
   ```powershell
   git remote -v
   ```
   - If URL contains `13.127.29.211` or known Gitea host → `{ISSUE_PLATFORM}` = `gitea`
   - If URL contains `github.com` → `{ISSUE_PLATFORM}` = `github`
3. Set `{ISSUE_OWNER}` and `{ISSUE_REPO}` based on platform:
   - Gitea: use `{GITEA_OWNER}` / `{GITEA_REPO}` from config
   - GitHub: use `{GITHUB_OWNER}` / `{GITHUB_REPO}` from config

### Step 0b: Check Token
// turbo
1. If platform = `gitea`:
   - Check if `.agent/.env` exists
   - If **NO** → Ask the developer:
     ```
     ⚠️ First-time setup: Gitea API token needed.
     1. Go to http://13.127.29.211/user/settings/applications
     2. Create a token named "antigravity-bot" with issue read/write permissions
     3. Paste the token here:
     ```
   - Save to `.agent/.env` as `GITEA_TOKEN={pasted_token}`
   - Verify: `GET {GITEA_API}/user` → should return username
2. If platform = `github`:
   - GitHub MCP server handles auth automatically (no token needed)

## Platform Dispatch Reference

> All workflows that say "create/update/close issue" should follow this table.

### API Mapping

| Operation | Platform = `github` | Platform = `gitea` |
|-----------|--------------------|--------------------|
| **Create issue** | `github-mcp-server` → `create_issue(owner, repo, title, body, labels)` | `POST {GITEA_API}/repos/{owner}/{repo}/issues` body: `{title, body, labels:[id,...]}` |
| **Update issue** | `github-mcp-server` → `update_issue(owner, repo, issue_number, state, labels)` | `PATCH {GITEA_API}/repos/{owner}/{repo}/issues/{N}` body: `{state, labels:[id,...]}` |
| **Close issue** | `update_issue(state:"closed")` | `PATCH .../issues/{N}` body: `{"state":"closed"}` |
| **Add comment** | `github-mcp-server` → `add_issue_comment(owner, repo, issue_number, body)` | `POST {GITEA_API}/repos/{owner}/{repo}/issues/{N}/comments` body: `{"body":"..."}` |
| **List issues** | `github-mcp-server` → `list_issues(owner, repo, state)` | `GET {GITEA_API}/repos/{owner}/{repo}/issues?state={state}&limit=100` |
| **Search issues** | `github-mcp-server` → `search_issues(q)` | `GET {GITEA_API}/repos/{owner}/{repo}/issues?q={query}` |
| **Get user** | `github-mcp-server` → `get_me` (MCP built-in) | `GET {GITEA_API}/user` |
| **Create label** | `github-mcp-server` → auto-created on issue | `POST {GITEA_API}/repos/{owner}/{repo}/labels` body: `{name, color}` |

### Gitea REST Helper (PowerShell)

When platform = `gitea`, read the token and make API calls like this:
```powershell
$token = (Get-Content ".agent/.env" | Select-String "GITEA_TOKEN=" | ForEach-Object { $_.Line.Split("=",2)[1] })
$headers = @{"Authorization"="token $token"; "Content-Type"="application/json"}
$base = "{GITEA_API}/repos/{GITEA_OWNER}/{GITEA_REPO}"

# Example: Create issue
$body = @{title="[BUG_ID] Title"; body="Description"; labels=@(1,2)} | ConvertTo-Json
Invoke-RestMethod -Uri "$base/issues" -Headers $headers -Method Post -Body $body
```

### Offline Fallback
If GitHub is unreachable (network issues, MCP server down, auth expired):
1. Log the issue locally to `bug_report_AI/PENDING_GITHUB_ISSUES.md`:
   ```markdown
   ## Pending Issue: {BUG_ID}
   - Title: [{BUG_ID}] {Bug Title}
   - Labels: {severity}, {track}, {category}, module:{MODULE}
   - Body: {full issue body}
   - Action: CREATE / UPDATE / CLOSE
   - Queued: {TIMESTAMP}
   ```
2. Continue the fix workflow — don't block on GitHub
3. On next successful GitHub connection, process all pending entries from the file
4. After syncing, move processed entries to `PENDING_GITHUB_ISSUES_ARCHIVE.md`

### Offline Fallback
If GitHub is unreachable (network issues, MCP server down, auth expired):
1. Log the issue locally to `bug_report_AI/PENDING_GITHUB_ISSUES.md`:
   ```markdown
   ## Pending Issue: {BUG_ID}
   - Title: [{BUG_ID}] {Bug Title}
   - Labels: {severity}, {track}, {category}, module:{MODULE}
   - Body: {full issue body}
   - Action: CREATE / UPDATE / CLOSE
   - Queued: {TIMESTAMP}
   ```
2. Continue the fix workflow — don't block on GitHub
3. On next successful GitHub connection, process all pending entries from the file
4. After syncing, move processed entries to `PENDING_GITHUB_ISSUES_ARCHIVE.md`

---

## Part 1: Project Setup (One-Time)

> Run this once per repo to create the label and milestone structure.

### Step 1: Create Labels

Use `github-mcp-server` → `issue_write` with labels. The first time any of these labels are used, GitHub creates them automatically. However, to pre-create with colors, use the GitHub web UI or run this setup.

**Severity Labels:**
| Label | Color | Description |
|---|---|---|
| `P0-critical` | `#d73a4a` (red) | Critical — data loss, security, financial |
| `P1-high` | `#e99695` (light red) | High — major functionality broken |
| `P2-medium` | `#fbca04` (yellow) | Medium — incorrect behavior, workaround exists |
| `P3-low` | `#0e8a16` (green) | Low — cosmetic, minor |

**Track Labels:**
| Label | Color | Description |
|---|---|---|
| `Track-A-system` | `#1d76db` (blue) | System/Architecture bug — Antigravity fixes |
| `Track-B-business` | `#5319e7` (purple) | Business logic bug — human validates |

**Category Labels:**
| Label | Color | Description |
|---|---|---|
| `cat:security` | `#d73a4a` | Security vulnerability |
| `cat:transaction` | `#b60205` | Transaction safety |
| `cat:query` | `#0075ca` | Query logic |
| `cat:variable` | `#bfd4f2` | Variable/typo |
| `cat:validation` | `#d4c5f9` | Validation logic |
| `cat:schema` | `#006b75` | Database schema |
| `cat:calculation` | `#e99695` | Business calculation |
| `cat:business-rule` | `#5319e7` | Missing/wrong business rule |
| `cat:performance` | `#fbca04` | Performance |
| `cat:integration` | `#c5def5` | AJAX/Integration |

**Module Labels:**
| Label | Color | Description |
|---|---|---|
| `module:Estimation` | `#0e8a16` | Estimation module |
| `module:Billing` | `#1d76db` | Billing module |
| `module:Payment` | `#5319e7` | Payment CRM module |
| _(add per module as needed)_ | | |

**Status Labels:**
| Label | Color | Description |
|---|---|---|
| `status:triaged` | `#c2e0c6` | Bug triaged, awaiting fix |
| `status:in-progress` | `#fbca04` | Fix in progress |
| `status:testing` | `#0e8a16` | Fix applied, under test |
| `status:blocked` | `#d73a4a` | Blocked — needs human input |

> **Tip**: Labels are auto-created by GitHub when first used in an issue. Pre-creating via UI just lets you set colors.

### Step 2: Create Milestones

Create Sprint milestones using the GitHub MCP server:

```
Milestone: Sprint 1 — P0 Critical Fixes
Milestone: Sprint 2 — P1 High Priority
Milestone: Sprint 3 — P2 Medium Priority
Milestone: Sprint 4 — P3 Low Priority + Cleanup
```

> [!NOTE]
> The GitHub MCP server does not currently have a `create_milestone` tool. Create these 4 milestones manually at:
> `https://github.com/{REPO_OWNER}/{REPO_NAME}/milestones/new`

### Step 3: Verify Setup
After setup, confirm:
- [ ] All severity labels exist (P0–P3)
- [ ] Track labels exist (Track-A, Track-B)
- [ ] At least 1 module label exists
- [ ] Sprint milestones 1–4 created

---

## Part 2: Create Bug Issue (called from `/bug-intake-triage`)

> Antigravity calls this automatically after triaging a bug.

### Inputs Required
| Input | Source |
|---|---|
| Bug ID | Generated by `/bug-intake-triage` |
| Title | Bug title from triage |
| Severity | P0/P1/P2/P3 from triage |
| Track | A or B from triage |
| Category | From triage classification |
| Module | Current module being audited |
| Pattern match | From COMMON_BUG_PATTERNS.md check |
| Sprint | Determined by severity mapping |

### Issue Creation Steps

#### Step 1: Get Current User [Antigravity]
Use `github-mcp-server` → `get_me` to get the logged-in developer's GitHub username.
This becomes the assignee.

#### Step 2: Build Issue Body [Antigravity]
Construct the issue body from triage data:

```markdown
## Bug Report: {BUG_ID}

**Module**: {MODULE_NAME}
**Track**: {A (System) / B (Business)}
**Category**: {category}
**Severity**: {P0/P1/P2/P3}
**Pattern Match**: {PAT-XXX / Novel}

### Description
{Bug description from triage or execution plan}

### Location
- **File**: `{file_path}`
- **Method**: `{method_name}`
- **Line**: {line_number}

### Current Behavior
{What currently happens}

### Expected Behavior
{What should happen}

### Evidence
{DB query results, code snippets, screenshots}

### Reproduction Steps
1. {step 1}
2. {step 2}
3. {step 3}

---
*Auto-generated by Antigravity Bug Remediation System*
*Triage Date: {TODAY}*
*Local Bug ID: {BUG_ID}*
```

#### Step 3: Create the Issue [Antigravity]
Use `github-mcp-server` → `issue_write` with method `create`:

```
Owner: {REPO_OWNER}      # from .agent/config.md
Repo: {REPO_NAME}         # from .agent/config.md
Title: [{BUG_ID}] {Bug Title}
Body: {constructed body from Step 2}
Labels: ["{severity_label}", "{track_label}", "{category_label}", "{module_label}", "status:triaged"]
Assignees: [{username from get_me}]
```

#### Step 4: Record Issue Number [Antigravity]
After creation, GitHub returns the issue number (e.g., `#42`).
- Append `GitHub: #42` to the bug entry in the local execution plan
- This cross-references local files ↔ GitHub

### Output
```
✅ GitHub Issue created: #{ISSUE_NUMBER}
   Title: [EST-R301] SQL Injection via Column Name
   Labels: P0-critical, Track-A-system, cat:security, module:Estimation, status:triaged
   Assignee: {username}
   Milestone: Sprint 1
   URL: https://github.com/{REPO_OWNER}/{REPO_NAME}/issues/{N}
```

---

## Part 3: Update Bug Issue Status (called during `/fix-single-bug`)

> When Antigravity starts working on a bug, update the issue status.

### On Fix Start
Use `github-mcp-server` → `issue_write` with method `update`:
- Remove label: `status:triaged`
- Add label: `status:in-progress`
- Add comment: `🔧 Fix in progress — {developer} working on this via /fix-{architecture|business}-bug`

### On Testing
Use `github-mcp-server` → `issue_write` with method `update`:
- Remove label: `status:in-progress`
- Add label: `status:testing`
- Add comment: `🧪 Fix applied, running /test-and-verify`

### On Blocked
If human approval is needed and developer is waiting:
- Add label: `status:blocked`
- Add comment: `⏸️ Awaiting human approval at Step {N}`

---

## Part 4: Close Bug Issue (called from `/learn-and-improve`)

> After fix is verified and brain is updated, close the GitHub Issue.

### Close Steps

#### Step 1: Build Close Comment [Antigravity]
```markdown
## ✅ Bug Fixed

**Fix Summary**: {1-line description of fix}
**Files Changed**: {list of files}
**Root Cause**: {brief root cause}

### Fix Details
- **Track**: {A (System) / B (Business)}
- **Category**: {category}
- **Pattern**: {PAT-XXX matched / NEW pattern added}
- **Tests**: {N tests, M assertions — PASS}
- **Human Validated**: {Yes/No — if Track B}

### Verification
```
{test output summary}
```

---
*Fixed by: {developer username}*
*Fix Date: {TODAY}*
*Local Bug ID: {BUG_ID}*
*Closed by Antigravity Bug Remediation System*
```

#### Step 2: Close the Issue [Antigravity]
Use `github-mcp-server` → `issue_write` with method `update`:

```
Owner: {REPO_OWNER}      # from .agent/config.md
Repo: {REPO_NAME}         # from .agent/config.md
Issue Number: {ISSUE_NUMBER}
State: closed
State Reason: completed
Labels: [remove "status:testing", keep severity/track/module/category]
```

Use `github-mcp-server` → `add_issue_comment`:
```
Body: {close comment from Step 1}
```

### Output
```
✅ GitHub Issue #{ISSUE_NUMBER} closed
   Bug: {BUG_ID} — {Title}
   Fixed by: {username}
   Resolution: completed
```

---

## Quick Reference

| Action | When | MCP Tool |
|---|---|---|
| Create issue | After `/bug-intake-triage` | `issue_write` (create) |
| Assign developer | At creation | `get_me` → assignee |
| Update status → in-progress | Start of `/fix-single-bug` | `issue_write` (update) |
| Update status → testing | After fix, before verify | `issue_write` (update) |
| Add blocked label | Waiting for human gate | `issue_write` (update) |
| Close issue | After `/learn-and-improve` | `issue_write` (update) + `add_issue_comment` |
| Deploy fix to client | After fix verified | Git cherry-pick (see Part 6) |
| Track client deployment | After deploy | Update `CLIENT_DEPLOYMENT_MATRIX.md` |

---

## Example: Full Lifecycle of EST-R301

```
1. /bug-intake-triage EST-R301
   → GitHub Issue #42 created
   → Labels: P0-critical, Track-A-system, cat:security, module:Estimation, status:triaged
   → Milestone: Sprint 1
   → Assignee: kanaga-sundar

2. /fix-single-bug EST-R301
   → Issue #42: status:triaged → status:in-progress
   → Comment: "🔧 Fix in progress"

3. /fix-architecture-bug EST-R301 (Step 7-8)
   → Issue #42: status:in-progress → status:testing
   → Comment: "🧪 Running tests"

4. /learn-and-improve EST-R301
   → Issue #42: CLOSED
   → Comment: "✅ Bug Fixed — column whitelist added to 10 methods"

5. Deploy to clients
   → Cherry-pick commit abc123 to AI_theni_NPR → ✅ deployed
   → Cherry-pick commit abc123 to AI_madurai_XYZ → ✅ deployed
   → Update CLIENT_DEPLOYMENT_MATRIX.md
```

---

## Part 5: Multi-Client Deployment Tracking

> **Problem**: Bug is fixed once in the source repo, but must be deployed to N client instances. Need to track which clients have received which fixes.

### Step 1: Create Client Registry (One-Time)

Create `bug_report_AI/CLIENT_REGISTRY.md` in the source repo:

```markdown
# Client Registry

| # | Client ID | Client Name | Repo / Path | Branch | Environment | Status |
|---|---|---|---|---|---|---|
| 1 | THENI-NPR | Theni NPR | AI_theni_NPR | main | Production | Active |
| 2 | MADURAI-XYZ | Madurai XYZ | AI_madurai_XYZ | main | Production | Active |
| 3 | SALEM-ABC | Salem ABC | AI_salem_ABC | main | Production | Active |

> Update this table when a new client is onboarded or decommissioned.
```

### Step 2: Create Deployment Matrix (Per Sprint)

After each sprint's bugs are fixed, create or update `bug_report_AI/CLIENT_DEPLOYMENT_MATRIX.md`:

```markdown
# Client Deployment Matrix — Sprint 1

| Bug ID | Title | Source Fix | Commit | THENI-NPR | MADURAI-XYZ | SALEM-ABC |
|---|---|---|---|---|---|---|
| EST-001 | Gift voucher wrong variable | ✅ Fixed | abc123 | ✅ 2026-02-21 | ❌ Pending | ❌ Pending |
| EST-003 | market_rate_tax wrong source | ✅ Fixed | def456 | ✅ 2026-02-21 | ❌ Pending | ❌ Pending |
| EST-052 | SQL injection column name | ✅ Fixed | ghi789 | ✅ 2026-02-21 | ✅ 2026-02-22 | ❌ Pending |
```

### Step 3: Update Matrix After Each Deployment

After deploying to a client:
1. Update the client's column: `❌ Pending` → `✅ {DATE}`
2. If deployment failed: `❌ Pending` → `⚠️ Failed — {reason}`
3. If client doesn't need this fix (divergent code): `❌ Pending` → `➖ N/A`

### Deployment Priority Order

| Priority | Rule |
|---|---|
| 1st | Client that reported the bug (if client-reported) |
| 2nd | Highest-traffic / highest-revenue clients |
| 3rd | All remaining active clients |

> [!IMPORTANT]
> A bug is NOT considered "fully resolved" until ALL active clients show `✅` in the deployment matrix. The GitHub Issue should remain open with label `status:deploying` until all clients are deployed.

### Additional Status Label

Add one more status label to Part 1 setup:

| Label | Color | Description |
|---|---|---|
| `status:deploying` | `#0e8a16` (green) | Fix verified, deploying to client instances |

---

## Part 6: Selective Bug Fix Deployment (Cherry-Pick)

> **Problem**: You fixed 5 bugs in the source repo, but want to deploy only specific fixes to a client — not the entire branch. This is critical for production safety.

### Method 1: Cherry-Pick Specific Commits (Recommended)

Use this when you want to deploy individual bug fixes without pulling other changes.

#### Prerequisites
- Each bug fix should be in its **own commit** with a clear message format:
  ```
  fix({MODULE}): [{BUG_ID}] {short description}
  ```
  Example: `fix(estimation): [EST-056] discount subtracts instead of adds`

#### Step-by-Step

**1. Identify the commit hash for the fix:**
```bash
# On the source repo
git log --oneline --grep="EST-056" -n 5
# Output: abc123f fix(estimation): [EST-056] discount subtracts instead of adds
```

**2. Navigate to the client repo:**
```bash
cd /path/to/AI_theni_NPR
```

**3. Add source repo as a remote (one-time per client):**
```bash
git remote add source https://github.com/{REPO_OWNER}/{REPO_NAME}.git
git fetch source
```

**4. Cherry-pick the specific fix:**
```bash
git cherry-pick abc123f
```

**5. If conflicts arise:**
```bash
# View conflicts
git status

# Resolve conflicts manually, then:
git add .
git cherry-pick --continue

# If the conflict is too messy, abort and apply manually:
git cherry-pick --abort
# Then manually apply the changes from the fix
```

**6. Verify the fix works on the client instance:**
```bash
# Run the client's local environment and test the specific fix
# Follow /test-and-verify for the specific bug ID
```

**7. Push to the client's remote:**
```bash
git push origin main
```

**8. Update the deployment matrix.**

### Method 2: Patch File (For Clients Without Git Remote Access)

Use this when the client instance doesn't have direct access to the source repo.

**1. Generate a patch from the source repo:**
```bash
cd /path/to/{REPO_NAME}
git format-patch -1 abc123f --stdout > EST-056.patch
```

**2. Copy patch to client and apply:**
```bash
cd /path/to/AI_theni_NPR
git apply EST-056.patch
git add .
git commit -m "fix(estimation): [EST-056] cherry-picked from source"
```

### Method 3: Sprint Bundle (Deploy All Sprint Fixes Together)

Use this when deploying an entire sprint's worth of fixes to a client at once.

**1. Create a branch with all sprint fixes in the source repo:**
```bash
cd /path/to/{REPO_NAME}
git log --oneline --grep="fix(estimation)" --since="2026-02-20" 
# Lists all fix commits for the sprint
```

**2. Cherry-pick all sprint fixes as a batch:**
```bash
cd /path/to/AI_theni_NPR
git fetch source
git cherry-pick abc123f def456a ghi789b  # All sprint commits in order
```

**3. Or generate a combined patch:**
```bash
# On source repo — generate patches for all sprint 1 fixes
git format-patch --grep="Sprint-1" --stdout > sprint_1_fixes.patch

# On client repo — apply all at once
git apply sprint_1_fixes.patch
```

### Commit Message Convention for Bug Fixes

To make cherry-picking reliable, **always** use this commit format:

```
fix({module}): [{BUG_ID}] {description}

Sprint: {sprint_number}
Track: {A|B}
Severity: {P0|P1|P2|P3}
GitHub: #{issue_number}
```

Example:
```
fix(estimation): [EST-056] percentage discount subtracts instead of adds

Sprint: 1
Track: B
Severity: P0
GitHub: #42
```

This makes it trivial to:
- `git log --grep="EST-056"` → find the fix commit
- `git log --grep="Sprint: 1"` → find all Sprint 1 fixes
- `git log --grep="fix(estimation)"` → find all Estimation fixes

### Pre-Deploy Checklist (Per Client)

Before deploying any fix to a client:

- [ ] Fix is verified in source repo (tests pass, `/test-and-verify` complete)
- [ ] Client is on a compatible code version (check for divergence in the affected file)
- [ ] Database migration scripts included (if fix involves ALTER TABLE / schema changes)
- [ ] Backup client DB before deploying schema changes
- [ ] Cherry-pick applies cleanly (no conflicts, or conflicts resolved correctly)
- [ ] Post-deploy smoke test on client instance
- [ ] Deployment matrix updated

> [!CAUTION]
> **Schema changes** (ALTER TABLE, ENGINE change, charset migration) require extra care. Always:
> 1. Back up the client's database first
> 2. Test the migration script on a staging copy
> 3. Apply during low-traffic hours
> 4. Verify data integrity after migration

