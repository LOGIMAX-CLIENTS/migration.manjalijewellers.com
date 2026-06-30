# Workflow: COMMIT Phase

> Execute when the active task reaches **COMMIT** phase.
> Every step marked MANDATORY is non-negotiable — skipping = violation.

---

## Step 0: Load Config

```
view_file: .sdlc-v2/config.json
```

Extract these values for use in later steps:
- `repo.owner` — GitHub repo owner for issues/PRs
- `repo.name` — GitHub repo name
- `repo.recipes_owner` — Bug recipes repo owner
- `repo.recipes_repo` — Bug recipes repo name
- `git.dev_branch` — PR target branch

---

## Step 1: Pre-Commit Verification

### 1a. Check review verdict
```
view_file: .sdlc-v2/active/{TASK_ID}/review.md
```
Verify: `Verdict: PASS` or `Verdict: PASS_WITH_ADVISORIES`
If FAIL → do NOT proceed. Go back to CODING.

### 1b. Check task completion
```
view_file: .sdlc-v2/active/{TASK_ID}/tasks.md
```
Verify: ALL checkboxes are `[x]`. No unchecked items.

### 1c. Syntax check
```bash
C:\xampp\php\php.exe -l {each modified file}
```

---

## Step 2: Auto-Baseline (if model changed)

If the fix modified a model method:

```bash
# Check if config exists
ls admin/tests/config/{method}.json

# If exists — capture baseline
cd admin && C:\xampp\php\php.exe index.php perf_test baseline {method}

# If NOT exists — generate config first, then baseline
cd admin && C:\xampp\php\php.exe index.php perf_test generate {model_name}
cd admin && C:\xampp\php\php.exe index.php perf_test baseline {method}
```

This ensures every fix automatically grows the regression baseline library.

---

## Step 3: Git Operations

```bash
# Verify branch
git branch --show-current
# Should be: {type}/{TASK_ID}-short-desc

# Stage source changes (files from design.md)
git add {files}

# Stage test files (if added)
git add admin/tests/

# Stage spec files
git add .sdlc-v2/active/{TASK_ID}/

# Commit with standard message format
git commit -m "{type}({module}): {summary} [{TASK_ID}]

- {change 1}
- {change 2}
Refs: {TASK_ID}"

# Push
git push -u origin {branch}
```

---

## Step 4: PR Creation

```
call_mcp_tool github create_pull_request:
  owner: {from config.json → repo.owner}
  repo: {from config.json → repo.name}
  title: "{type}({module}): {summary} [{TASK_ID}]"
  head: "{branch_name}"
  base: {from config.json → git.dev_branch}
  body: |
    ## Task: {TASK_ID}
    **Type**: {type} | **Module**: {module} | **Priority**: {priority}
    
    ### Changes
    {list files changed with summary}
    
    ### Test Results
    {from review.md}
    
    ### Acceptance Criteria
    {from requirement.md — all PASS}
```

---

## Step 5: MANDATORY — Recipe Creation

> ⛔ This step is NOT optional. Skipping = rule violation.

### 5a. Check: was a recipe found during REQUIREMENT?

If recipe was found and used → record: "Applied existing recipe: {name}" → skip to Step 6.

### 5b. If no recipe existed — create one NOW

```
view_file: bug-recipes/recipes/_TEMPLATE.md
```

Create recipe following the template:
- **Symptom**: What the user reported
- **Root Cause**: What was actually wrong (from requirement.md)
- **Fix Pattern**: What was changed (from design.md)
- **Affected Tables**: Database tables involved
- **Module**: Which module
- **Keywords**: Search terms for future discovery

### 5c. Push recipe to central repo

```
call_mcp_tool github create_or_update_file:
  owner: {from config.json → repo.recipes_owner}
  repo: {from config.json → repo.recipes_repo}
  path: "recipes/{module}/{TASK_ID}.md"
  content: {recipe content}
  message: "recipe: {TASK_ID} — {summary}"
  branch: "main"
```

Record: "Recipe pushed: recipes/{module}/{TASK_ID}.md"

---

## Step 6: MANDATORY — Brain Update

> ⛔ This step is NOT optional.

### 6a. Did the fix change documented behavior?

- **If yes** → update `knowledge_brain/{module}/MODULE_BRAIN.md`
  - Add/modify the section describing the changed behavior
  - Include the fix reference: `[{TASK_ID}]`

- **If cross-module impact** → update relevant `knowledge_brain/_SYSTEM/` docs:
  - `SHARED_TABLES.md` if table usage changed
  - `DATA_FLOW_CHAINS.md` if data flow changed
  - `DANGER_ZONES.md` if a new trap was discovered

- **If no behavior change** → record: "Brain unchanged: fix doesn't alter documented behavior"

---

## Step 7: MANDATORY — GitHub Issue Close

> ⛔ This step is NOT optional.

### 7a. Read issue number
```bash
cat .sdlc-v2/active/{TASK_ID}/metadata.txt
# Look for: github_issue: #N
```

### 7b. Add closing comment
```
call_mcp_tool github add_issue_comment:
  owner: {from config.json → repo.owner}
  repo: {from config.json → repo.name}
  issue_number: {N}
  body: |
    ## ✅ Fixed
    **Task**: {TASK_ID}
    **Fix**: {1-line summary}
    **Files**: {list from design.md}
    **Tests**: {test results from review.md}
    **PR**: #{pr_number}
    **Branch**: {branch_name}
    ---
    *Closed by SDLC Pipeline v2*
```

### 7c. Close the issue
```
call_mcp_tool github update_issue:
  owner: {from config.json → repo.owner}
  repo: {from config.json → repo.name}
  issue_number: {N}
  state: "closed"
```

### 7d. Update local tracking
If bug is tracked in `ACTIVE_BUGS.md`, mark as FIXED.

---

## Step 8: Archive Task

```bash
python .sdlc-v2/engine/cli.py done
```

This moves specs to `done/{TASK_ID}/` and records cycle time.

---

## Step 9: Self-Audit Checklist

Before declaring done, verify ALL of these:

- [ ] Commit pushed to feature branch
- [ ] PR created targeting Dev branch (Retail_1.1.1.0001)
- [ ] Recipe created OR existing recipe noted
- [ ] Brain updated OR "unchanged" documented
- [ ] GitHub issue closed with fix summary
- [ ] `sdlc done` executed
- [ ] Cycle time reported to user

**Any unchecked item → go back and complete it.**
