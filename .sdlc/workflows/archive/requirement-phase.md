# Workflow: REQUIREMENT Phase

> Execute when the active task is in **REQUIREMENT** phase.
> This workflow has a **HARD GATE** at Step 1 — you cannot proceed to code investigation until recipe search is complete.

---

## ⛔ DENY LIST (enforced for entire phase)

You **CANNOT** use these tools during REQUIREMENT:
- `write_to_file` → `admin/**` (source files)
- `write_to_file` → `.sdlc-v2/**/requirement.md` (use artifact review instead)
- `run_command` → anything creating/modifying source files
- `browser_subagent` → for testing fixes (observation of the bug is OK)

---

## Step 1: RECIPE SEARCH (Blocking Gate)

> ⛔ **HARD GATE**: You CANNOT proceed to Step 2 until this completes.
> Even if you believe the bug is novel, you MUST search first.

### 1a. Search GitHub MCP (primary)

```
call_mcp_tool github search_code
  query: "{symptom keyword} {module name}"
  (search in LOGIMAX-CLIENTS/bug-recipes repository)
```

### 1b. If MCP fails — git CLI fallback

```bash
cd ../bug-recipes && grep -r "{keyword}" recipes/
```

### 1c. Search common patterns

```
view_file: bug_report_AI/COMMON_BUG_PATTERNS.md
```

### 1d. Document the result

**You MUST paste the actual search output.** No summaries like "nothing found."

Record one of:
- `✅ Recipe found: {recipe_name}` — use it as starting point, skip to Step 7
- `✅ No recipe match` — proceed with investigation (paste the actual output below)
- `❌ Recipe search skipped` — **THIS IS A VIOLATION. Go back to 1a.**

---

## Step 2: Load Knowledge Brain

### 2a. Module Brain
```
view_file: knowledge_brain/{module}/MODULE_BRAIN.md
```

### 2b. System Brain (diagnostic)
```
view_file: knowledge_brain/_SYSTEM/DIAGNOSTIC_PLAYBOOK.md
view_file: knowledge_brain/_SYSTEM/DANGER_ZONES.md
```

### 2c. Task-specific brain docs

| Bug Type | Also Read |
|---|---|
| Data flow / report | `DATA_FLOW_CHAINS.md` |
| Cross-module | `SHARED_TABLES.md` + `MODULE_DEPENDENCIES.md` |
| Validation | `VALIDATION_GAPS.md` |
| Status/tag values | `TAG_STATUS_MAP.md` + `HARDCODED_VALUES.md` |

---

## Step 3: Generate Context

```bash
python .sdlc-v2/engine/cli.py context
```

This auto-generates `context.md` with brain references + RAG search results.

---

## Step 4: Code Investigation

**NOW** you may read source code:

### 4a. Find relevant code
```
grep_search: {function_name}, {table_name}, {keyword}
```

### 4b. Read functions
```
view_file: {model_file}, {controller_file}, {js_file}
```

### 4c. LCA analysis (if available)
```bash
lca analyze impact . {function_name}
lca analyze sql-map . -t {table_name}
lca analyze crossref .
lca analyze search . "{keyword}"
```

### 4d. Data verification
```sql
-- Use execute_sql SELECT only
SELECT * FROM {table} WHERE {conditions} LIMIT 10;
```

---

## Step 5: Identify Impact Areas

> This is critical — the task description shows ONE symptom. You must find ALL affected functions.

### 5a. Map the impact area

From your investigation, list ALL functions/methods that:
- Are in the same data flow chain as the reported bug
- Share the same database tables
- Are called by the same controller
- Feed data to the same view/report

### 5b. Format as impact area list

```
## Impact Areas
- `model::method_1()` — directly affected (reported bug)
- `model::method_2()` — shares table X, could be affected
- `model::method_3()` — called by same controller, uses related data
- `js_file.js::function_4()` — renders the output, could mask issues
```

This list feeds into TEST_DESIGN (discovery testing).

---

## Step 6: Form Root Cause Hypothesis

### 6a. State the hypothesis in plain English

> "The cashbook doesn't show opening balance because `get_cash_book_opening()` returns 0 
> for branch_id=1 due to a hardcoded fallback at line 33134."

### 6b. Tag the hypothesis

- 🔴 **HYPOTHESIS** — based on code reading only, not verified
- 🟡 **PARTIALLY VERIFIED** — some evidence from DB/observation
- 🟢 **CONFIRMED** — observed in browser/DB, matches symptom exactly

### 6c. Verify hypothesis (optional, read-only)

You MAY use these for verification:
- `browser_subagent` → to VIEW the bug (navigate, screenshot, check network)
- `execute_sql` → SELECT queries to verify data matches hypothesis

You CANNOT:
- Create test files
- Modify source code
- "Try a fix" to see if it works

If verification changes the hypothesis, update the tag.

---

## Step 7: Create Requirement Artifact

Create an artifact with ALL of these sections:

```
write_to_file:
  TargetFile: {artifact_dir}/requirement_review.md
  IsArtifact: true
  ArtifactMetadata:
    ArtifactType: "implementation_plan"
    RequestFeedback: true
    Summary: "Requirement for {TASK_ID}: {summary}"
```

### Required sections:

1. **Description** — from user input + your analysis
2. **Current Behavior** — what happens now (with evidence)
3. **Expected Behavior** — what should happen
4. **Root Cause** — hypothesis + status tag (🔴/🟡/🟢) + code citation
5. **Impact Areas** — the full list from Step 5 (feeds TEST_DESIGN)
6. **Acceptance Criteria** — ≥3 testable, specific criteria
7. **Technical Notes** — LCA impact, sql-map results, dependencies
8. **Recipe Match** — actual search output pasted (not summarized)

---

## Step 8: STOP — Wait for User Approval

> ⛔ Do NOT auto-transition. Do NOT start planning or coding.

Tell user: **"Review the requirement — comment on any line you want changed."**

When user says "approved":
1. Copy final content to `.sdlc-v2/active/{TASK_ID}/requirement.md`
2. Run: `python .sdlc-v2/engine/cli.py transition TEST_DESIGN`

---

## Evidence Standard

Every claim MUST include **pasted tool output**:

| Claim | Required Evidence |
|---|---|
| "No recipe found" | Paste the actual search_code or grep output |
| "Function X calls Y" | Paste grep_search result with line numbers |
| "Table has no data" | Paste execute_sql SELECT result |
| "Bug confirmed" | Paste browser screenshot path or network response |
| "Root cause is X" | Cite specific file:line with code snippet |
