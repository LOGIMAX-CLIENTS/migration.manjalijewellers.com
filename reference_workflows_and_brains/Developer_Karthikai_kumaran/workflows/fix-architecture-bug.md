---
description: "Fix an Architecture-Level (Level 2) bug — semi-automated. Human validates approach before fix."
---

# /fix-architecture-bug — Level 2: Architecture-Level Bug Fix

> **Source:** MASTER_BUG_REMEDIATION_PROCEDURE.md § 5, FINAL_SOP_BUG_REMEDIATION.md § 6
> **Automation Level:** 75% — Antigravity diagnoses + proposes. Human validates approach.
> **Time Target:** < 2 hours per bug
> **Applies to:** Transaction Safety, Query Architecture, Schema Defects, Concurrency, Performance, Integration/AJAX

## Prerequisites
- Bug ID assigned
- Module Brain exists (especially Components 2, 4, 5)
- Bug exists in execution plan

## Core Principles
> **ONE bug at a time. Propose BEFORE applying. Document the architectural rationale.**

---

## Step 1: DIAGNOSE ROOT CAUSE [🤖 AUTO]

1. Read bug from execution plan
2. Read relevant Module Brain components:
   - Component 2 (Data Flow) — to trace the affected flow
   - Component 4 (Cross-Module Map) — to check dependencies
   - Component 5 (Invariant Matrix) — to check variant-specific behavior
3. Cross-reference COMMON_BUG_PATTERNS.md for known architectural anti-patterns

## Step 2: TRACE DATA FLOW [🤖 AUTO]

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

## Step 3: CHECK CROSS-MODULE IMPACT [🤖 AUTO]

Using the Cross-Module Map (Brain Component 4):
1. List all modules that depend on the affected data
2. For each dependent module, assess:
   - Will the fix change the data contract?
   - Will the fix change query results?
   - Will the fix change timing/ordering?
3. Document the cross-module impact assessment

## Step 4: PROPOSE FIX [🤖 AUTO]

Present to the human with:

```markdown
### Fix Proposal for {BUG_ID}

**Root Cause:** {what's architecturally wrong}

**Proposed Fix:**
- What will change: {description}
- Files affected: {list}
- Risk Assessment: Low / Medium / High
- Cross-Module Impact: {assessment from Step 3}

**Rollback Plan:**
- {exact steps to undo}

**Alternative Approaches Considered:**
1. {approach 1} — rejected because {reason}
2. {approach 2} — rejected because {reason}
```

## ⏸️ Step 5: HUMAN APPROVAL GATE [👤 HUMAN]

> **STOP HERE. Do NOT apply any fix until the human approves the approach.**

Human evaluates:
- [ ] Is this the right architectural solution?
- [ ] Is the risk level acceptable?
- [ ] Is the cross-module impact manageable?
- [ ] Are there better alternatives?

Human actions:
- **APPROVE** → Proceed to Step 6
- **MODIFY** → Adjust the approach per human guidance, return to Step 4
- **REJECT** → Document reason, move bug to DEFERRED
- **ESCALATE** → Flag for senior review

## Step 6: APPLY FIX [🤖 AUTO, after approval]

Apply the approved fix using `replace_file_content` or `multi_replace_file_content`.

### Architecture-Level Sub-Procedures:

#### A. Transaction Safety Fixes
```
1. Identify all trans_start()/trans_complete()/trans_status() blocks
2. Verify: trans_commit() only when trans_status() === TRUE
3. Verify: trans_rollback() path exists for every failure
4. Verify: external side-effects (email, SMS) are OUTSIDE the transaction
5. Apply fix: add missing rollback/status checks
```

#### B. Schema Defect Fixes
```
⚠️ MANDATORY: Full database backup BEFORE any schema change

1. Generate ALTER TABLE statement
2. Present the migration SQL for human review
3. DO NOT EXECUTE — save to {BUG_REPORT_DIR}/{BUG_ID}_migration.sql
4. Human will execute during maintenance window
```

#### C. Query Architecture Fixes
```
1. Rewrite using CodeIgniter 3 Query Builder where possible
2. Verify results match original query (compare row counts, sums)
3. Add index if needed (generate CREATE INDEX statement)
```

#### D. Concurrency Fixes
```
1. Propose locking strategy (SELECT FOR UPDATE, application-level lock)
2. Human validates: will locking cause deadlocks?
3. Apply fix
```

#### E. Performance Fixes
```
1. Add pagination / limits
2. Add missing indexes
3. Optimize queries (remove N+1, replace subqueries with JOINs)
```

#### F. Integration/AJAX Fixes
```
1. Add error handlers to AJAX calls
2. Add timeout configuration
3. Add proper error responses
```

## Step 7: SYNTAX CHECK [🤖 AUTO]

// turbo
```
& "C:\php8\php.exe" -l "{FIXED_FILE_PATH}"
```

## Step 8: WRITE & RUN TESTS [🤖 AUTO]

Tests for architecture-level bugs must include:
- **Unit test** proving the fix works
- **Integration test** covering the architectural change
- For transaction fixes: test success commits AND failure rollbacks
- For query fixes: test correct results AND performance

// turbo
```
cd "c:\xampp 7.1\htdocs\etail_development_src\admin\tests" && & "C:\php8\php.exe" vendor/bin/phpunit --no-configuration {BUG_ID}Test.php --testdox
```

## Step 9: DOCUMENT [🤖 AUTO]

Create a fix walkthrough:
```markdown
## Fix Walkthrough: {BUG_ID}

### Architectural Problem
{description of the structural flaw}

### Root Cause
{why the code was written this way / what was missed}

### Fix Applied
{what was changed and why}

### Cross-Module Impact
{assessment results}

### Rollback Plan
{exact steps to undo}

### Test Results
{test output}
```

## Step 10: FINAL HUMAN REVIEW [👤 HUMAN]

Human performs:
- [ ] Diff review — is the fix correct?
- [ ] Smoke test — does the module still work?
- [ ] Cross-module check — do dependent features still work?
- **APPROVE** → Update tracking, proceed to knowledge update
- **REJECT** → Revert, re-assess
