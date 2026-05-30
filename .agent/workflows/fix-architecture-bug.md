---
description: Fix a Track A (System/Architecture-Level) bug — Antigravity-led with human diff approval
version: 1.3
last_updated: 2026-03-04
---

# Fix Architecture Bug Workflow

> **SOP Reference**: FINAL_SOP_BUG_REMEDIATION.md § 6 — Track A
> **Automation Level**: 75% — Antigravity diagnoses + proposes + applies. Human validates approach.
> **Time Target**: < 2 hours per bug
> **Applies to**: Security, Transaction Safety, Query Architecture, Schema Defects, Concurrency, Performance, Variable/Typo, Exception Handling, Integration/AJAX

## When to Use

- Called from `/fix-single-bug` Step 3 when ASSESS determines **Track A (System)**
- The code has a defect independent of business rules
- Antigravity can fix this almost autonomously — human just approves the diff

## Prerequisites

- Bug ID assigned (e.g., `EST-R301`, `BIL-S02`)
- Bug triaged via `/bug-intake-triage` (severity, track=A, category assigned)
- Execution plan exists in `bug_report_AI/{module}/`
- `bug_report_AI/COMMON_BUG_PATTERNS.md` loaded

### Step 0: MODULE BRAIN CHECK [Antigravity]

Check if `knowledge_brain/{MODULE_NAME}/MODULE_BRAIN.md` exists:

- **Exists** → Load brain context, proceed to Step 1
- **Missing** → Display warning:
  ```
  ⚠️ MODULE BRAIN NOT FOUND for {MODULE_NAME}
  Diagnosis accuracy will be reduced without brain context.
  Options:
    1. Continue without brain (faster, less accurate)
    2. Run /build-module-brain first (recommended for complex bugs)
  ```
  If continuing without brain → skip all brain-reference steps, rely on execution plan + pattern library only

### Step 0b: LOAD CENTRAL CONFIG [Antigravity]

Read `.agent/config.md` → load module paths and shared variables.
Read `.agent/.env` → load `{PHP_PATH}`, `{PROJECT_ROOT}`, `{ISSUE_PLATFORM}`, `{REPO_OWNER}`, `{REPO_NAME}`.
For issue operations → use platform dispatch (see `/github-bug-tracking`).

> [!NOTE]
> After applying a fix in Step 6, append the rollback plan to `bug_report_AI/ROLLBACK_REGISTRY.md`.
> This ensures all rollback plans are centrally accessible during emergencies.

## Steps

### Step 1: DIAGNOSE ROOT CAUSE [Antigravity]

1. Read bug from execution plan (`*_BUG_FIX_EXECUTION_PLAN.md`)
2. Read relevant Module Brain components:
   - `MODULE_BRAIN.md` — architecture context
   - `METHOD_INDEX.md` — method→table mappings
   - `DATA_FLOW.md` — to trace the affected flow
   - `CROSS_MODULE_MAP.md` — to check dependencies
   - `INVARIANT_MATRIX.md` — to check variant-specific behavior (if exists)
3. Cross-reference `COMMON_BUG_PATTERNS.md` for known patterns:
   - **Match found** → note Pattern ID, use fix template as starting point
   - **No match** → novel bug, use execution plan's proposed fix

### Step 2: TRACE DATA FLOW [Antigravity]

Layer-by-layer trace:

```
JS Layer      → What client-side code is involved?
Controller    → What processing happens?
Model Layer   → What queries are executed?
DB Layer      → What's the schema structure?
```

For each layer, document:

- Input data
- Processing/transformation
- Output data
- Where the architectural flaw occurs

### Step 3: CHECK CROSS-MODULE IMPACT [Antigravity]

Using `CROSS_MODULE_MAP.md`:

1. List all modules that depend on the affected data
2. For each dependent module, assess:
   - Will the fix change the data contract?
   - Will the fix change query results?
   - Will the fix change timing/ordering?
3. Document the cross-module impact assessment
4. If cross-module impact detected → **alert user before proceeding**

### Step 4: PROPOSE FIX [Antigravity]

Present to the human:

```markdown
### Fix Proposal for {BUG_ID}

**Root Cause:** {what's architecturally wrong}

**Proposed Fix:**

- What will change: {description}
- Files affected: {list with line numbers}
- Risk Assessment: Low / Medium / High
- Cross-Module Impact: {assessment from Step 3}
- Pattern Match: {PAT-XXX / Novel}

**Rollback Plan:**

- {exact steps to undo}

**Alternative Approaches Considered:**

1. {approach 1} — rejected because {reason}
2. {approach 2} — rejected because {reason}
```

### ⏸️ Step 5: HUMAN APPROVAL GATE (Pre-Fix) [Human]

> **STOP HERE. Do NOT apply any fix until the human approves the approach.**

Human evaluates:

- [ ] Is this the right architectural solution?
- [ ] Is the risk level acceptable?
- [ ] Is the cross-module impact manageable?
- [ ] Are there better alternatives?

Human actions:

- **APPROVE** → Proceed to Step 6
- **MODIFY** → Adjust approach per human guidance, return to Step 4
- **REJECT** → Document reason, mark bug "Deferred"
- **ESCALATE** → Flag for senior review

### Step 6: APPLY FIX [Antigravity — after approval]

Apply the approved fix using `replace_file_content` or `multi_replace_file_content`.

**Sub-Procedures by Category:**

#### A. Transaction Safety Fixes

1. Identify all `trans_start()`/`trans_complete()`/`trans_status()` blocks
2. Verify: `trans_commit()` only when `trans_status() === TRUE`
3. Verify: `trans_rollback()` path exists for every failure
4. Verify: external side-effects (email, SMS) are OUTSIDE the transaction
5. Apply fix: add missing rollback/status checks

#### B. Schema Defect Fixes

> [!CAUTION]
> **MANDATORY: Full database backup BEFORE any schema change.**

1. Generate `ALTER TABLE` statement
2. Generate matching rollback SQL
3. Save to `bug_report_AI/{module}/{BUG_ID}_migration.sql`
4. **DO NOT EXECUTE** — human executes during maintenance window

#### C. Query Architecture Fixes

1. Rewrite using CodeIgniter 3 Query Builder where possible
2. Verify results match original query (compare row counts, sums)
3. Add index if needed (generate `CREATE INDEX` statement)
4. For N+1 queries: batch query outside loop, build lookup array

#### D. Concurrency Fixes

1. Propose locking strategy (`SELECT FOR UPDATE`, application-level lock)
2. Human validates: will locking cause deadlocks?
3. Apply fix after confirmation

#### E. Performance Fixes

1. Add pagination / limits
2. Add missing indexes
3. Optimize queries (remove N+1, replace subqueries with JOINs)

#### F. Integration/AJAX Fixes

1. Add error handlers to AJAX calls
2. Add timeout configuration
3. Add proper error responses (JSON with status code)

#### G. Security Fixes

1. Replace `$_POST`/`$_GET` with `$this->input->post('field', TRUE)`
2. Add `htmlspecialchars()` for XSS in views
3. Verify CSRF token validation on forms
4. Fix `mkdir` permissions (0777 → 0755)

#### H. Variable/Typo Fixes

1. Locate the exact variable name mismatch
2. Apply minimal rename — do NOT refactor surrounding code
3. Verify counterpart functions use the correct variable too

### Step 7: SYNTAX CHECK [Antigravity]

// turbo
For PHP files:

```powershell
& "{PHP_PATH}" -l {affected_php_file}
```

For JS files → flag: "Manual browser console check required"
For SQL → validate syntax, do NOT execute

### Step 8: WRITE & RUN TESTS [Antigravity]

Run via `/test-and-verify`:

- **Unit test** proving the fix works
- For transaction fixes: test success commits AND failure rollbacks
- For query fixes: test correct results AND performance
- For security fixes: test malicious payload rejection

// turbo

```powershell
cd "{PROJECT_ROOT}\{TEST_DIR}" && & "{PHP_PATH}" vendor/bin/phpunit --no-configuration {TestFile}.php --testdox
```

### Step 9: DOCUMENT [Antigravity]

Generate fix walkthrough:

```markdown
## Fix Walkthrough: {BUG_ID}

### Architectural Problem

{description of the structural flaw}

### Root Cause

{why the code was written this way / what was missed}

### Fix Applied

{what was changed and why — before/after diff}

### Cross-Module Impact

{assessment results}

### Rollback Plan

{exact steps to undo}

### Test Results

{test output}
```

### ⏸️ Step 10: FINAL HUMAN REVIEW [Human]

Human performs:

- [ ] Diff review — is the fix correct?
- [ ] Smoke test — does the module still work?
- [ ] Cross-module check — do dependent features still work?

**APPROVE** → Proceed to `/learn-and-improve`
**REJECT** → Revert, re-assess

## Completion Report

```
✅ Bug {BUG_ID} — {TITLE} (Track A: Architecture)
   Category: {category}
   Fix: {1-line summary of change}
   File: {path}:{lines}
   Tests: {N} tests, {M} assertions — all passed
   Risk: {Low/Medium/High}
   Cross-Module Impact: {None / {list}}
   Pattern: {PAT-XXX matched / NEW pattern added / N/A}
   Rollback: {1-line rollback instruction}
   Next: /learn-and-improve → /fix-single-bug {NEXT_BUG_ID}
```
