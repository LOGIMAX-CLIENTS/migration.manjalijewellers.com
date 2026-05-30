---
description: Set up the Bug Remediation System for a brand-new client project (no existing brain or audit data)
version: 2.0
last_updated: 2026-03-16
---

# Setup New Client Project

> **When to use**: Client project has NO existing brain, NO audit data, and may use a different framework or structure.
> **Time**: ~30 minutes for setup + brain build time per module
> **Source**: Copy workflows from `retail_v5` (source version)

---

## Prerequisites
- [ ] Client project codebase is accessible locally (e.g., `c:\xampp\htdocs\{client_project}\`)
- [ ] GitHub repo exists for the client project (or will be created)
- [ ] Developer has Antigravity + GitHub MCP server configured

---

## Steps

### Step 1: Copy Workflow Files [Developer]

Copy the entire `.agent/workflows/` directory from the source project:

// turbo
```powershell
# Create target directories
New-Item -ItemType Directory -Force -Path "{CLIENT_PROJECT_PATH}\.agent\workflows"
New-Item -ItemType Directory -Force -Path "{CLIENT_PROJECT_PATH}\knowledge_brain\_TEMPLATE"
New-Item -ItemType Directory -Force -Path "{CLIENT_PROJECT_PATH}\knowledge_brain\_SYSTEM"
New-Item -ItemType Directory -Force -Path "{CLIENT_PROJECT_PATH}\bug_report_AI\SOP"

# Copy workflows
Copy-Item -Recurse -Force "c:\xampp\htdocs\retail_v5\.agent\workflows\*" "{CLIENT_PROJECT_PATH}\.agent\workflows\"

# Copy brain templates
Copy-Item -Recurse -Force "c:\xampp\htdocs\retail_v5\knowledge_brain\_TEMPLATE\*" "{CLIENT_PROJECT_PATH}\knowledge_brain\_TEMPLATE\"

# Copy SOP
Copy-Item -Force "c:\xampp\htdocs\retail_v5\bug_report_AI\SOP\FINAL_SOP_BUG_REMEDIATION.md" "{CLIENT_PROJECT_PATH}\bug_report_AI\SOP\"

# Copy Senior Developer Knowledge Files (CRITICAL — these prevent wrong AI diagnoses)
Copy-Item -Force "c:\xampp\htdocs\retail_v5\knowledge_brain\_SYSTEM\DIAGNOSTIC_PLAYBOOK.md" "{CLIENT_PROJECT_PATH}\knowledge_brain\_SYSTEM\"
Copy-Item -Force "c:\xampp\htdocs\retail_v5\knowledge_brain\_SYSTEM\DANGER_ZONES.md" "{CLIENT_PROJECT_PATH}\knowledge_brain\_SYSTEM\"
Copy-Item -Force "c:\xampp\htdocs\retail_v5\bug_report_AI\POSTMORTEM_LOG.md" "{CLIENT_PROJECT_PATH}\bug_report_AI\"
```

**Do NOT copy**:
- `knowledge_brain/{MODULE}/` — these are source-version specific (build fresh per client)
- `bug_report_AI/{MODULE}/` — audit data is source-version specific
- `WORKFLOW_WALKTHROUGH_ESTIMATION.md` — example doc, not a workflow

**MUST copy** (new devs need these on day 1):
- `DIAGNOSTIC_PLAYBOOK.md` — symptom→suspect rules that prevent wrong diagnoses
- `DANGER_ZONES.md` — NEVER rules that prevent catastrophic mistakes
- `POSTMORTEM_LOG.md` — past AI failures so the same mistakes don't repeat

### Step 2: Update Project-Specific Config [Developer]

Edit `.agent/config.md` (central config) — update ALL values for the client project:
- `{PROJECT_ROOT}`, `{PROJECT_NAME}`, `{REPO_OWNER}`, `{REPO_NAME}`
- `{PHP_PATH}`, `{PHPUNIT_PATH}`, `{TEST_DIR}`
- `{CONTROLLER_DIR}`, `{MODEL_DIR}`, `{VIEW_DIR}`, `{JS_DIR}`

Also update `github-bug-tracking.md` — verify the `{REPO_OWNER}` and `{REPO_NAME}` values in `.agent/config.md` match the client's GitHub repo.

Check and update if different:
| Setting | Source Value | Update If Different |
|---|---|---|
| PHP path | `{PHP_PATH}` | Client's PHP location |
| PHPUnit path | `{PHPUNIT_PATH}` | Client's test runner |
| Controller dir | `admin/controllers/` | Client's controller path |
| Model dir | `admin/models/` | Client's model path |
| View dir | `admin/views/` | Client's view path |
| JS dir | `admin/js/` | Client's JS path |

### Step 3: Adapt Pattern Library (If Different Framework) [Antigravity]

If same framework (CodeIgniter 3):
```
/manage-bug-patterns
Action: Initialize
```
→ Creates `COMMON_BUG_PATTERNS.md` with all 17 seed patterns. Done.

If **different framework**:
1. Initialize the pattern library
2. Remove CI3-specific detection rules
3. Add framework-equivalent detection rules:

| CI3 Pattern | Laravel Equivalent | Node/Express Equivalent |
|---|---|---|
| `$_POST['field']` | `$request->field` without validation | `req.body.field` without sanitization |
| `$this->db->like($var)` | `DB::whereRaw($var)` | Raw SQL with string concatenation |
| `trans_commit()` without check | `DB::commit()` without try-catch | Missing transaction rollback |

### Step 4: Set Up GitHub Tracking [Developer]

Run `/github-bug-tracking` Part 1 (one-time setup):
1. Create labels (severity, track, category, module) on the client's GitHub repo
2. Create Sprint 1–4 milestones at `https://github.com/{ORG}/{REPO}/milestones/new`

### Step 5: Build First Module Brain [Antigravity]

```
/build-module-brain
Module: {CLIENT_FIRST_MODULE}
```

This runs from scratch — no copying. Antigravity reads the client's actual code and builds a fresh brain.

**Start with the module that has the most reported bugs** — the brain pays off immediately.

### Step 6: Run First Audit [Antigravity]

```
/module-bug-audit
Module: {CLIENT_FIRST_MODULE}
```

This finds all bugs, creates the execution plan, and you're live.

### Step 7: Verify Setup [Developer]
- [ ] `.agent/workflows/` copied and paths updated
- [ ] `knowledge_brain/_TEMPLATE/` copied
- [ ] `knowledge_brain/_SYSTEM/DIAGNOSTIC_PLAYBOOK.md` copied ⚡
- [ ] `knowledge_brain/_SYSTEM/DANGER_ZONES.md` copied ⚡
- [ ] `bug_report_AI/POSTMORTEM_LOG.md` copied ⚡
- [ ] GitHub repo labels + milestones created
- [ ] `COMMON_BUG_PATTERNS.md` initialized (adapted if different framework)
- [ ] First module brain built
- [ ] First audit run
- [ ] First GitHub Issue created via `/bug-intake-triage`

---

## Completion Report
```
✅ New client project set up
   Project: {CLIENT_PROJECT_NAME}
   Repo: {ORG}/{REPO}
   Framework: {CodeIgniter 3 / Laravel / Other}
   Patterns: {N} patterns initialized
   First module: {MODULE_NAME} — brain built, audit complete
   Bugs found: {N}
   Ready for: /fix-single-bug
```
