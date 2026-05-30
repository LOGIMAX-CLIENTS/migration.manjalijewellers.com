---
name: bug-fix-engine
description: Full AI bug remediation engine — 4 roles (Debugger, Architect, Developer, Tester), 7 modes, debug playbooks for reverse engineering, recipes for auto-fix, and fingerprint integration for cross-client scanning.
---

# Bug Fix Engine — SKILL.md

> **PURPOSE**: Make AI bug fixing FAST — from vague symptom to deployed fix.
> - **Layer 0 (Debugger)**: Reverse-engineer symptoms → root cause
> - **Layer 1-3 (Architect → Developer → Tester)**: Fix → verify → deploy
> This is the entry point — it routes to the right approach.

---

## 📦 Recipe Repository (Central Source of Truth)

All recipes, playbooks, and common patterns are stored in a **central repo**.
The repo's GitHub org, name, and directory structure are configured in:
**`{CENTRAL_REPO}/.agent/sync-config.json`**

> **CRITICAL**: The central repo is the SINGLE source of truth for ALL developers across ALL client repos.
> Local `.agent/skills/bug-fix-engine/recipes/` and `debug_playbooks/` are **GITIGNORED and DEPRECATED**. The AI reads directly from the central repo via MCP. Do NOT read from or write to local caches.

### How to Find the Central Repo

1. Look for a sibling directory of the current project that contains `.agent/sync-config.json`
2. Read `sync-config.json` to get `github_org` and `github_repo`
3. Use those values for all MCP and git operations below

```powershell
# Auto-detect: scan sibling directories for the central repo
$htdocsRoot = Split-Path $env:PROJECT_ROOT -Parent
$centralRepo = Get-ChildItem -Path $htdocsRoot -Directory | Where-Object {
    Test-Path (Join-Path $_.FullName ".agent\sync-config.json")
} | Select-Object -First 1 -ExpandProperty FullName
$config = Get-Content "$centralRepo\.agent\sync-config.json" | ConvertFrom-Json
```

### Dual-Mode Access

**Mode 1 — GitHub MCP (preferred)**:
```
# Read sync-config.json first to get {ORG} and {REPO}
Read:  mcp_github-mcp-server_get_file_contents(owner: "{ORG}", repo: "{REPO}", path: "recipes/<name>.md")
Write: mcp_github-mcp-server_push_files(owner: "{ORG}", repo: "{REPO}", ...)
List:  mcp_github-mcp-server_get_file_contents(owner: "{ORG}", repo: "{REPO}", path: "recipes/")
```

**Mode 2 — Git CLI fallback (when MCP unavailable)**:
```powershell
# Read: pull latest before searching
cd $centralRepo && git pull origin main

# Write: commit + push after creating recipe
cd $centralRepo && git add recipes/<name>.md && git commit -m "recipe: <name>" && git push
```

> **Detection**: Try MCP first. If MCP tool not available or errors → fall back to local git clone auto-detected from sibling directories.

### Repo Structure
```
<central-repo>/
├── .agent/
│   ├── sync-config.json  ← Config: GitHub org/repo, directory names
│   └── scripts/          ← Sync script
├── recipes/              ← Fix recipes (Before→After code, detection, verification)
├── patterns/             ← COMMON_BUG_PATTERNS.md
├── playbooks/            ← Debug playbooks (symptom → investigation)
└── roles/                ← AI role definitions
```

---

## 🔍 Role 0: Debugger (Layer 0 — Reverse Engineering)
**When**: Client reports a bug with NO bug ID. Vague symptom. "Something is broken."

> **Full methodology**: `roles/debugger.md`
> **Playbooks**: `debug_playbooks/{symptom}.md`

### Quick Route: Symptom → Playbook

| Symptom | Playbook | First Move |
|---|---|---|
| Wrong data on page | `debug_playbooks/wrong_data.md` | Trace query → JOINs/WHEREs |
| Button does nothing | `debug_playbooks/silent_failure.md` | AJAX response + error log |
| Error message shown | `debug_playbooks/action_error.md` | Parse error via `error_log_parser.md` |
| Used to work, now doesn't | `debug_playbooks/regression.md` | `git log` + recent changes |
| Dropdown data wrong | `debug_playbooks/dropdown_data.md` | AJAX endpoint → WHERE clause |
| Permission denied | `debug_playbooks/permission.md` | RBAC: profile → access table |
| Module A ≠ Module B | `debug_playbooks/data_mismatch.md` | Compare both SQL queries |
| Duplicate records | `debug_playbooks/duplicates.md` | Race condition? UNIQUE? |
| Corruption after cancel | `debug_playbooks/cancel_corruption.md` | Save-vs-cancel comparison |
| Slow page | `debug_playbooks/performance.md` | N+1? Missing index? |
| SMS/OTP not sent | `debug_playbooks/notification.md` | Gateway config + credits |
| Only in one client | `debug_playbooks/client_specific.md` | Fingerprint divergence |

### Debug Handshake (2-4 rounds)
```
Round 1: Symptom collection (AI asks, human answers)
Round 2: Static trace (AI reads code + generates SQL)
Round 3: Data analysis (human runs SQL, AI analyzes)
Round 4: Root cause → hand off to Architect
```

**Output**: Exact file, function, line, what's wrong → transitions to Role 1.

---

## AI Roles

Every fix activates **3 roles** in sequence. The AI must think as each role:

### 🏗️ Role 1: System Architect
**When**: Before ANY code change.

| Responsibility | What To Do |
|---|---|
| Impact analysis | Which modules read/write the affected tables? Check `SHARED_TABLES.md` |
| State machine integrity | Does this fix break any tag_status / bill_status transitions? |
| Cross-module ripple | Trace downstream: a billing fix may break reports, transfers, day close |
| Cancel flow coverage | Check `_cancel_master.md` — does cancel reverse what save touches? |
| Settings dependency | Does a settings key control this behavior? Check `general_settings.md` |
| Architecture traps | Purchase cancel uses SAME logic as sales cancel — opposite stock direction! |

**Output**: "Fix X in module Y. Downstream impact: Z. No ripple risk." OR "Wait — this affects 3 other modules. Here's the dependency chain."

### 💻 Role 2: Full Stack Developer
**When**: During code change.

| Responsibility | What To Do |
|---|---|
| Backend (PHP) | Trace Controller → Model → DB. Fix at correct layer. |
| Frontend (JS) | Check if validation is JS-only → add server-side mirror |
| Database (SQL) | Verify table schema matches expected columns. Check INDEXes. |
| Transaction safety | `trans_begin()` → logic → `trans_commit()`/`trans_rollback()`. Never `trans_complete()`. |
| Security | Parameterize SQL. Use `$this->input->post()` not `$_POST`. Escape output. |
| Coding conventions | Match existing code style. See CLAUDE.md § Coding Conventions. |

**Output**: Exact code change with file, line, before/after.

### 🧪 Role 3: Tester
**When**: After code change.

| Responsibility | What To Do |
|---|---|
| Syntax check | `php -l <file>` — MUST pass |
| Flow trace (5 actions) | Save ✓ Cancel ✓ Edit ✓ Print ✓ Report ✓ |
| Edge cases | Empty input, zero amount, duplicate submit, cancelled bill, multi-branch |
| Report accuracy | Does the report query include this fix's table/column? |
| Regression | Does fix break any flow listed in `_cancel_master.md`? |
| Cross-client | Will this fix work for all 70+ clients or is it client-specific? |

**Output**: Test result summary. Pass/fail with evidence.

---

## When To Use Which Mode

| Situation | Mode | Roles Active |
|---|---|---|
| Client calls with vague symptom | → **Mode 0: Debug** | Debugger → (then Architect) |
| Client calls with a specific bug | → **ADHOC Match** | Architect (quick) → Developer |
| Fixing a known bug from backlog | → **Mode A: Single Bug Fix** | All 4 roles |
| Fixing all instances of a pattern | → **Mode B: Batch Fix** | Developer → Tester |
| Auditing a flow for unknown bugs | → **Mode C: Flow Audit** | Architect → Tester |
| Report shows wrong data | → **Mode D: Report Debug** | Debugger → Developer → Tester |
| Settings change broke something | → **Mode E: Config Trace** | Debugger → Architect → Developer |

---

## Step 0: Context Load (2 minutes)

Before ANY fix, load in order:

0. **FIRST — Sync recipes from central repo** (read `sync-config.json` for org/repo):
   - MCP: `get_file_contents(owner: "{ORG}", repo: "{REPO}", path: "recipes/")`
   - Git fallback: `cd {CENTRAL_REPO} && git pull origin main`
   - This ensures you have ALL recipes from ALL developers across ALL repos.
1. **Always**: `CLAUDE.md` — product overview, 28 modules, bill types, conventions
2. **Always**: Central `bug-recipes/patterns/COMMON_BUG_PATTERNS.md` — known fix patterns
3. **If module known**: `knowledge_brain/<Module>/MODULE_BRAIN.md`
4. **If flow known**: `knowledge_brain/_SYSTEM/FLOW_CHECKLISTS/<flow>.md`
5. **If cancel bug**: `knowledge_brain/_SYSTEM/FLOW_CHECKLISTS/_cancel_master.md`
6. **If report bug**: `knowledge_brain/Ret_Reports/METHOD_INDEX.md`

---

## ADHOC Match Protocol (Client calls with a bug)

**Goal: Match to known bug in 2 minutes. Skip investigation.**

### Step 1: Search Known Bugs
```
1. Search central repo's patterns/COMMON_BUG_PATTERNS.md for symptom keywords
   - MCP: search_code in {ORG}/{REPO}
   - Git fallback: grep -rn "keyword" {CENTRAL_REPO}/
2. Search knowledge_brain/_SYSTEM/CROSS_MODULE_BUGS.md
3. Search central repo's recipes/ for matching symptom
```

### Step 2: Route Based on Match

| Result | Action | Time |
|---|---|---|
| **Recipe exists** | Apply recipe → `php -l` → done | 5-15 min |
| **Pattern match** | Use fix template → apply → save as recipe | 30 min |
| **Cross-module bug** | Read entry → check if fixed in source → apply | 15-30 min |
| **No match** | Fall to **Mode A** | 1-4 hours |

---

## Mode A: Single Bug Fix

### A1: Identify (Architect role)
1. Search patterns/recipes → match?
2. **Match found** → skip A2, go to A3
3. **No match** → A2

### A2: Debug (Architect + Developer roles)
1. Load flow checklist: `FLOW_CHECKLISTS/<flow>.md`
2. Load module brain + flow risk matrix
3. **Trace the deviation**: which step in Save/Cancel/Edit/Print/Report is broken?
4. **For cancel bugs**: load `_cancel_master.md` — check the 20-step trace + 8 known gaps
5. Output: **exact file, exact function, exact deviation**

### A3: Fix (Developer role)
- Apply recipe/pattern OR write minimal fix
- `php -l <file>` syntax check
- **Ripple check** (Architect hat): Does fix break downstream? Check `SHARED_TABLES.md`
- **⚠️ Cancel logic trap**: `cancel_bill()` L7773 uses SAME code for ALL bill types. Purchase cancel needs OPPOSITE stock logic (subtract, not add).

### A4: Verify (Tester role)
- Trace fix against flow checklist (5 actions: Save ✓ Cancel ✓ Edit ✓ Print ✓ Report ✓)
- Run edge cases

### A5: Learn
- Save recipe → push to central repo's `recipes/<pattern_name>.md`
  - Use `/push-recipe` workflow (handles MCP vs git fallback automatically)
  - ⛔ Do NOT save to local client repos — central repo is the sole destination
- Update flow checklist if gap found
- Update `CROSS_MODULE_BUGS.md` if cross-module

---

## Mode B: Batch Fix (Pattern-Level)

For fixing ALL instances of the same bug across ALL modules.

### B1: Select Pattern
From `COMMON_BUG_PATTERNS.md`. Top batch candidates:
- `PAT-SEC-001`: SQL injection (raw column interpolation)
- `PAT-SEC-002`: OTP bypass (`==` vs `===`)
- `PAT-TXN-001`: `trans_commit()` on failure branch
- `PAT-TXN-002`: `trans_complete()` instead of `trans_begin/commit`
- `PAT-VAL-001`: Client-side-only validation

### B2: Detect All
```powershell
# Example: OTP bypass
grep -rn "==" admin/application/controllers/ --include="*.php" | grep -i "otp"
# Example: trans_commit on failure
grep -rn "trans_commit" admin/application/models/ --include="*.php" -A5 -B5
```

### B3: Fix All
For each instance: confirm bug (not false positive) → apply fix → `php -l` → log

### B4: Summary + Recipe
Save batch detection + fix as recipe.

---

## Mode C: Flow Audit (Finding Unknown Bugs)

### C1: Pick a Flow
From `FLOW_CHECKLISTS/`. Priority order:
1. Highest client complaint frequency
2. Most tables touched (more reversal risk)
3. Not yet audited (⬜ items in checklist)

### C2-C6: Trace 5 Actions

| Step | Trace | Bug = |
|---|---|---|
| C2: Save | Find save function → list all INSERT/UPDATE tables → compare to checklist | Missing table write |
| C3: Cancel | Find cancel → list all reversals → compare to save | Missing reversal step |
| C4: Edit | Find edit → verify UPDATE not INSERT → verify old data cleaned | Duplicate insert |
| C5: Print | Find print template → verify same data source as save | Formula mismatch |
| C6: Report | Find report query → verify same tables as save + correct joins | Wrong aggregation |

### C7: Output
Update checklist with bugs found. Each gets: Bug ID, missing step, severity.

---

## Mode D: Report Debug (NEW)

**For when a report shows wrong numbers. Different from flow bugs — this is query correctness.**

### D1: Identify Report (Architect role)
1. Which report? → Map to model method via `Ret_Reports/METHOD_INDEX.md`
2. Which tables does it query? → Check Table→Method map in METHOD_INDEX § 7d
3. Load report checklist: `FLOW_CHECKLISTS/rpt_<name>.md`

### D2: Trace Data Flow (Developer role)
```
Browser → JS AJAX → Controller (list/ajax switch) → Model method → SQL → JSON → DataTable
```
1. Read the model method's SQL query
2. Check JOIN conditions — missing JOIN = missing rows
3. Check WHERE filters — wrong filter = phantom or missing rows
4. Check GROUP BY — missing GROUP = wrong totals
5. Check date/branch filters — timezone or day_close_date mismatch?

### D3: Common Report Bugs
| Bug Pattern | Detection |
|---|---|
| Cancelled bills included | Missing `bill_status != 2` in WHERE |
| In-transit tags double-counted | Missing `tag_status != 2` filter |
| Date-suffixed method | Old version called instead of current |
| Wrong bill type filter | Missing `bill_type IN (...)` |
| Export ≠ screen | `export_csv()` uses different query than AJAX |

### D4: Fix + Verify (Developer + Tester)
- Fix SQL query in model
- Verify with DB query: `SELECT ... FROM ... WHERE [same filters]` → compare count/total
- Check both screen AND export match

---

## Mode E: Config Trace (Settings Bug)

**For when a settings change causes system-wide side effects.**

### E1: Identify Setting (Architect role)
1. Which `ret_settings` key changed?
2. Grep for the key across ALL modules:
```powershell
grep -rn "KEY_NAME" admin/application/ --include="*.php"
```
3. List all modules that read this setting

### E2: Trace Impact (Architect role)
For each module that reads the setting:
- What behavior does it control?
- Is the setting cached or read per-request?
- Does the setting have a default fallback?

### E3: Fix (Developer role)
- Fix at the source (setting value) or at the consumer (module reading it)
- If setting is missing from DB → add with default value
- If `configDB()` is commented out (MST-BUG-022) → fix persistence

---

## Cancel Flow Quick Reference

**Read**: `FLOW_CHECKLISTS/_cancel_master.md` for the full 20-step trace.

### Known Architecture Traps
| Trap | Description |
|---|---|
| **Journal never reversed** | `cancel_bill()` does NOT touch `ret_journal` — ALL bill types |
| **Purchase stock inversion** | Cancel ADDS stock back. For type=4 (purchase), should SUBTRACT |
| **Other inventory loop bug** | L8185: `$other_inv` variable outside foreach — only deletes LAST item |
| **Sales return double-add** | Return adds stock, then cancel adds stock AGAIN |
| **Credit cancel hardcodes** | Sets `credit_status=2` instead of recalculating partial collections |

---

## Fingerprint Tool (Cross-Client Scanning)

**Tool**: `AI_Bug_Fix_System/fingerprint/fingerprint.php`

### Usage
After fixing a bug in source, scan all 70+ clients for the SAME bug:
```
1. Define the fingerprint (exact code pattern before fix)
2. Run fingerprint scan across client repos
3. Generate fix report: which clients have the bug, which don't
4. Apply recipe to affected clients
```

---

## Recipe Format

All recipes in **central repo**: `{ORG}/{REPO}/recipes/<name>.md` (see `sync-config.json` for actual values)
Template: `{CENTRAL_REPO}/recipes/_TEMPLATE.md`

```markdown
# Recipe: [Pattern/Bug Name]
## Metadata
- Pattern ID, Severity, Modules Affected, Auto-fixable
## Client Scope
- Applies to: ALL | [specific clients]
- Reason: [if not ALL, why]
## Created By
- Developer, Client, Date, Source Bug ID
## Symptom
What the client reports.
## Root Cause
What's wrong in the code.
## Detection
grep command to find this bug.
## Files
Which files to check.
## Fix
Exact code change (Before → After blocks).
## Verification
How to confirm the fix works.
## Notes
Additional context, caveats, related patterns.
```

---

## Priority Matrix (Which bug to fix first)

| Priority | Criteria | Examples |
|---|---|---|
| P0 — Fix NOW | Data corruption, security breach, money loss | Journal not reversed, SQLi, clear_database no auth |
| P1 — Fix this sprint | Wrong output, customer-facing error | Report totals wrong, credit balance incorrect |
| P2 — Fix next sprint | Cosmetic, non-blocking | DOMPDF typo, table name typo |
| P3 — Backlog | Dead code, tech debt | Date-suffixed methods, commented code |

---

## Speed Reference

| Scenario | Without This Skill | With This Skill |
|---|---|---|
| Context loading | 15-20 min | 2 min (CLAUDE.md) |
| Known bug (recipe) | 2-4 hours re-investigate | 5-15 min apply |
| Pattern bug (batch) | 2-4 hours per instance | 15 min per instance |
| Flow bug (novel) | 4-8 hours | 1-2 hours (checklist) |
| Report bug | 2-6 hours (guess SQL) | 30 min (METHOD_INDEX → query) |
| ADHOC from client | 2+ hours | 2 min match + 10 min apply |
| Settings bug | hours of tracing | 15 min (grep key → trace) |
