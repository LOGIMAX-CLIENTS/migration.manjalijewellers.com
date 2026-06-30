# Workflow: PLANNING Phase

> Execute when the active task is in **PLANNING** phase.
> By this point, REQUIREMENT is approved AND discovery testing (TEST_DESIGN) has run.
> You have the complete picture: what's reported + what's actually broken.

---

## ⛔ DENY LIST (enforced for entire phase)

- `write_to_file` → `admin/**` (source files)
- `write_to_file` → `.sdlc-v2/**/design.md` or `tasks.md` (use artifact review)
- `browser_subagent` → any usage
- `run_command` → anything creating/modifying source files
- `execute_sql` → INSERT/UPDATE/DELETE (SELECT only)

---

## Step 1: Read Inputs

### 1a. Requirement
```
view_file: .sdlc-v2/active/{TASK_ID}/requirement.md
```
Note: Impact Areas section — these are the functions that need attention.

### 1b. Discovery Test Results
```
view_file: .sdlc-v2/active/{TASK_ID}/discovery_results.md
```
Note: Which functions PASS (leave alone) and which FAIL (must fix).

### 1c. Context
```
view_file: .sdlc-v2/active/{TASK_ID}/context.md
```

---

## Step 2: Impact Analysis (LCA)

```bash
lca analyze impact . {function_name} --visual    # Dependency tree
lca analyze modules .                             # Module dependency graph
lca risk score . --top 10                         # Risky files
lca quality smells . -m {module}                  # Existing issues
```

If LCA unavailable, use `grep_search` + `view_file` and paste why LCA failed.

---

## Step 3: Cross-Module Check

```
view_file: knowledge_brain/_SYSTEM/SHARED_TABLES.md
view_file: knowledge_brain/_SYSTEM/DANGER_ZONES.md
```

If fix touches >3 files or multiple modules → flag cross-module risk.

---

## Step 4: Design Document

Prepare design content with these sections:

### Approach
High-level solution. State what you're fixing and why this approach.

### Files to Modify
FROM LCA impact output (not guessing). For each file:
- File path
- Function(s) to change
- What changes and why

### Discovery Test Findings
Summarize what the tests found:
- Functions that PASSED → no changes needed
- Functions that FAILED → changes designed below
- Functions not tested → risk acknowledged

### Risks
FROM LCA risk scores + code smells. What could break.

### Decisions
Key technical choices with rationale (e.g., "extend existing method vs create new one").

---

## Step 5: Task Breakdown

Break the design into checkbox items:

```markdown
## Code Changes
- [ ] {file}: {function} — {what to change}
- [ ] {file}: {function} — {what to change}

## Test Verification  
- [ ] Run discovery tests again — all previously-passing tests still pass
- [ ] Run discovery tests — previously-failing tests now pass
- [ ] php -l on all modified files

## Post-Fix
- [ ] Update MODULE_BRAIN.md if behavior changed
```

Each task must be specific: file + function + what to change.

---

## Step 6: Create Design Artifact

```
write_to_file:
  TargetFile: {artifact_dir}/design_review.md
  IsArtifact: true
  ArtifactMetadata:
    ArtifactType: "implementation_plan"
    RequestFeedback: true
    Summary: "Design for {TASK_ID}: {summary}"
```

Include BOTH the design section AND the tasks section in one artifact.

---

## Step 7: STOP — Wait for User Approval

> ⛔ Do NOT auto-transition. Do NOT start coding.

Tell user: **"Review the design and tasks — comment on any line you want changed."**

When user says "approved":
1. Save design section to `.sdlc-v2/active/{TASK_ID}/design.md`
2. Save tasks section to `.sdlc-v2/active/{TASK_ID}/tasks.md`
3. Run: `python .sdlc-v2/engine/cli.py transition CODING`
