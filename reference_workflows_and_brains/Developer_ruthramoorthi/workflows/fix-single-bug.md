---
description: Fix a single bug using the 8-step Antigravity workflow with task planning
---

# Phase 4 — Fix Single Bug (Antigravity-Driven)

**SOP Reference**: [FINAL_SOP_BUG_REMEDIATION.md](file:///c:/xampp/htdocs/etail_development_src/SOP/FINAL_SOP_BUG_REMEDIATION.md) — Section 6 (Phase 3 — Fix Execution)

## When to Use
- Fix one specific bug from the consolidated bug report
- Fix an ad-hoc bug reported by a client or tester
- User says "fix bug {BUG_ID}" or "fix this bug"

## Core Principles
> **ONE bug at a time. No batch fixes. Stability > Elegance. Documentation first.**

1. **Isolation** — Each bug fixed in its own change, tagged with Bug ID
2. **Traceability** — Every line change references the Bug ID
3. **Reversibility** — Every fix has a documented rollback plan
4. **Surgical precision** — Minimal fix footprint, no opportunistic refactoring
5. **Pattern reuse** — Check `bug_report_AI/COMMON_BUG_PATTERNS.md` before writing from scratch

## Required Input
| Field | Source |
|---|---|
| `{BUG_ID}` | From bug report or user |
| `{MODULE_NAME}` | From bug report |

If the user doesn't provide a Bug ID, ask: "Which bug would you like to fix? Provide the Bug ID or describe the issue."

## Steps

### STEP 0: CREATE ANTIGRAVITY TASK PLAN

Before touching any code, Antigravity must create its own task plan:

1. Create a `task.md` in your brain directory with a checklist:
```markdown
# Task: Fix {BUG_ID} — {Bug Title}

- [ ] Step 1: Diagnose — read bug details, cross-reference brain
- [ ] Step 2: Locate — find exact file + line, read current code
- [ ] Step 3: Assess — impact, risk, track classification
- [ ] Step 4: Plan — create implementation plan, document rollback
- [ ] Step 5: Apply fix — make the minimal code change
- [ ] Step 6: Syntax check — run php -l or browser check
- [ ] Step 7: Test — generate and run tests
- [ ] Step 8: Approval gate — show diff to human
- [ ] Step 9: Close — update brain, patterns, tracking
```

2. Create an `implementation_plan.md` in your brain directory:
```markdown
# Fix Plan: {BUG_ID} — {Bug Title}

## Bug Details
- **ID**: {BUG_ID}
- **Module**: {MODULE_NAME}
- **Track**: A (System) / B (Business)
- **Severity**: P{N}
- **Category**: {CATEGORY}

## Root Cause
{What is actually wrong and why}

## Proposed Fix
### File: {filename}
- **Line(s)**: {line range}
- **Current code**: {paste exact current code}
- **Proposed change**: {paste exact replacement}
- **Why this fix**: {explain why this is the correct minimal fix}

## Impact Assessment
- **Risk**: Low / Medium / High
- **Cross-module impact**: {does this fix affect other modules?}
- **Data impact**: {could this change existing stored data?}

## Rollback Plan
{Exact steps to undo this fix if it causes a regression}

## Tests Required
{List the test cases to run after applying the fix}
```

---

### STEP 1: DIAGNOSE

// turbo
1. Read the bug details from `bug_report_AI/{module}/CONSOLIDATED_BUG_REPORT.md`
   - If ad-hoc bug (no report), use the user's description
2. Read the Module Brain: `knowledge/{MODULE_NAME}/MODULE_BRAIN.md`
   - Check §10 (Bug Root Cause Register) — has this bug or similar been seen before?
   - Check §11 (Code Anti-Patterns) — is this a known anti-pattern?
3. Read the common patterns library: `bug_report_AI/COMMON_BUG_PATTERNS.md`
   - Does a fix template exist for this pattern?

Output: Root cause identified. Update the implementation plan with the diagnosis.

---

### STEP 2: LOCATE

// turbo
1. Find the exact file and line number
   - Use the bug report's file:line reference
   - OR search the codebase for the problematic code pattern
2. Read the current code verbatim
   - Show at least 10 lines of context around the bug
3. Confirm the bug still exists (code matches what's described in the report)

If the bug has already been fixed or code has changed:
- Tell the user: "Code at {file}:{line} has changed since the bug report. The current code is: {paste}. Does this still need fixing?"

Output: Exact file + line confirmed. Current code shown.

---

### STEP 3: ASSESS

Determine:

1. **Impact Type**: Security / Data Integrity / Financial / UX / Performance
2. **Risk Level**: Low / Medium / High
   - Low: cosmetic, isolated function, no data impact
   - Medium: affects logic but limited scope, easy rollback
   - High: financial calculation, cross-module, schema change, data migration

3. **Track Classification**:
   - **Track A (System/Architecture)** — Antigravity applies fix, human just approves diff:
     - Security fixes (SQL injection, XSS, CSRF)
     - Transaction fixes (missing rollback, wrong commit)
     - Query fixes (N+1, Cartesian JOIN, missing index)
     - Schema fixes (column type, missing PK)
     - Variable/typo fixes
     - Exception handling (try-catch, error response)
     - Performance fixes (index, pagination)

   - **Track B (Business)** — Antigravity proposes, human must validate the logic:
     - Calculation formula fixes
     - Business rule additions/corrections
     - Status workflow changes
     - Cross-module data contract fixes

4. **Cross-Module Check**: From brain §16 (Change Impact Map):
   - Does this file/function affect other modules?
   - If yes, list them as "also verify after fix"

Output: Update implementation plan with assessment. Mark task step 3 complete.

---

### STEP 4: PLAN & APPLY FIX

1. **Check for pattern template**: If `COMMON_BUG_PATTERNS.md` has a fix template for this type, use it
2. **Write the minimal fix**: Only change the lines that fix the bug. No refactoring.
3. **Document the rollback**:
   - For file changes: "Revert {file} to backup or git checkout"
   - For schema changes: "Run ALTER TABLE ... to revert column"
4. **Apply the code change** using the edit tool:
   - Add a comment on the changed line(s): `// BUG-FIX: {BUG_ID} — {brief description} — {date}`

**Fix Procedures by Code Layer:**

**PHP Controller/Model (System/Architecture):**
- Locate exact file and line from plan
- Apply minimal fix — no additional refactoring
- Verify change doesn't break adjacent logic

**DB Schema (System):**
> IMPORTANT: Require database backup before any schema change.
- Generate `ALTER TABLE` statement
- Document the reverse `ALTER TABLE` for rollback
- Flag for staging test before production

**JavaScript (Architecture/Business):**
- Locate exact function and line
- Check if fix needs to apply to counterpart functions (e.g., `getActiveDesigns` and `get_ActivelotSubDesigns`)
- Verify fix doesn't affect BOTH Add and Edit paths (JS is shared)
- JS files are 30K+ lines — be surgically precise

**View/Template (Architecture):**
- Apply fix (e.g., `htmlspecialchars()` for XSS, fix HTML structure)
- Test both Add and Edit modes
- Check print/PDF output if applicable

Output: Fix applied. Show before/after diff.

---

### STEP 5: SYNTAX CHECK

// turbo
For PHP files:
```bash
& "C:\php8\php.exe" -l {filename}
```

For JS files:
- Check for syntax errors by scanning the modified function
- Look for unclosed brackets, missing semicolons, string literal issues

If syntax check fails → revert the change, diagnose, and re-apply.

---

### STEP 6: TEST

Run relevant tests. Invoke `/test-and-verify {BUG_ID}` or follow these steps:

1. **Generate test cases** for this specific bug fix
2. **Run PHPUnit** (if PHP fix):
// turbo
```bash
& "C:\php8\php.exe" vendor/bin/phpunit --no-configuration admin/tests/{MODULE}/{BUG_ID}Test.php --testdox
```
3. **Smoke test checklist** — verify primary module operations still work

If tests fail → analyze failure, adjust fix, re-apply from Step 4.

---

### STEP 7: APPROVAL GATE (Human)

Present to the human for approval:

```
═══════════════════════════════════════════
BUG FIX REVIEW — {BUG_ID}
═══════════════════════════════════════════

Bug: {title}
Track: A (System) / B (Business)
Severity: P{N}
Risk: Low / Medium / High

File: {filename}
Lines: {line range}

BEFORE:
{exact old code}

AFTER:
{exact new code}

WHY:
{explanation of why this fixes the bug}

TESTS:
{test results — PASS/FAIL}

ROLLBACK:
{how to undo this fix}

═══════════════════════════════════════════
Approve? (yes / no / need changes)
═══════════════════════════════════════════
```

**For Track A (System)**: Human reviews the diff → Approve/Reject
**For Track B (Business)**: Human validates the business logic is correct → Approve
**For P0 Hotfix**: Verbal approval is sufficient, backfill documentation within 24h

If rejected → go back to Step 4 with feedback.

---

### STEP 8: CLOSE & UPDATE

After human approval:

1. **Update the CONSOLIDATED_BUG_REPORT.md**:
   - Change bug status from `New` / `In Progress` → `Fixed`
   - Add fix date and applied-by

2. **Update the Module Brain** — invoke `/brain-update {BUG_ID}`:
   - Add entry to §10 (Bug Root Cause Register)
   - Update §11 (Code Anti-Patterns Map) if applicable
   - Update §13 (Field-Level Trace) if data flow changed
   - Update §15 (Unit Test Map) with new test cases
   - Update §21 (Gaps/Risks) — mark debt as fixed if applicable

3. **Update COMMON_BUG_PATTERNS.md**:
   - If this was a new pattern → add it
   - If pattern exists → add module to "Modules Found In"

4. **Generate walkthrough**:
   Create a walkthrough artifact documenting:
   - What was broken
   - Root cause
   - Fix applied (before/after)
   - Test results
   - Files changed

5. **Mark task as complete** in task.md

6. **Tell the user**:
   - Bug {BUG_ID} fixed ✅
   - Brain updated
   - Next bug to fix: {next Sprint 1 bug from consolidated report}
