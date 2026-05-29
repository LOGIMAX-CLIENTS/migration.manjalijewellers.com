---
description: Generate and run tests for a bug fix, then produce sign-off report
---

# Phase 5 — Testing & Verification

**SOP Reference**: [FINAL_SOP_BUG_REMEDIATION.md](file:///c:/xampp/htdocs/etail_development_src/SOP/FINAL_SOP_BUG_REMEDIATION.md) — Section 7 (Phase 4 — Testing Standards)

## When to Use
- After applying a bug fix via `/fix-single-bug`
- To generate tests for any specific change
- To run the full verification sign-off for a bug

## Required Input
| Field | Source |
|---|---|
| `{BUG_ID}` | From fix workflow |
| `{MODULE_NAME}` | From fix workflow |
| `{FILE_CHANGED}` | The file that was modified |
| `{FIX_DESCRIPTION}` | What was fixed |

## Steps

### 1. Generate Unit Tests

Based on the bug category, generate appropriate tests:

**For Security bugs (SQLi, XSS, CSRF):**
- Test: whitelist enforcement accepts valid input
- Test: malicious payload (SQL injection string) is blocked/sanitized
- Test: parameterized binding active (no string concatenation in query)
- Test: XSS payload in output is escaped

**For Transaction bugs:**
- Test: success path → `trans_commit()` called
- Test: failure path → `trans_rollback()` called
- Test: partial failure → no orphan records left

**For Logic/Calculation bugs:**
- Test: correct result for normal input (happy path)
- Test: edge case — input = 0
- Test: edge case — input = null or empty
- Test: edge case — input = negative
- Test: edge case — very large input (overflow check)
- Test: boundary values (min valid, max valid)

**For Data Validation bugs:**
- Test: valid input passes validation
- Test: invalid input blocked with error message
- Test: boundary condition handled correctly
- Test: null / empty handled gracefully

**For Schema bugs:**
- Test: data preserved after migration
- Test: column type correct
- Test: constraint enforced (NOT NULL, UNIQUE, FK)

**For Integration bugs:**
- Test: Mock external module returns valid data → correct handling
- Test: Mock external module returns error → graceful failure
- Test: Mock external module returns null → no crash

Save test file to: `admin/tests/{MODULE_NAME}/{BUG_ID}Test.php`

### 2. Run PHP Syntax Check

// turbo
```bash
& "C:\php8\php.exe" -l {FILE_CHANGED}
```

Expected: `No syntax errors detected in {FILE_CHANGED}`

If FAILED: Report the error. Do NOT proceed until syntax is clean.

### 3. Run Unit Tests

// turbo
```bash
cd admin/tests && & "C:\php8\php.exe" vendor/bin/phpunit --no-configuration {MODULE_NAME}/{BUG_ID}Test.php --testdox
```

Document results:
```
Tests: {X} / {Y} passed
Failures: {list any failures with reason}
```

If tests fail: Analyze failure, report to user, suggest fix adjustment.

### 4. Module Smoke Test Checklist

Run through the module-specific checklist. Read the Module Brain §22 (Operational Support Guide) for module-specific checks, plus these universal checks:

```markdown
# Smoke Test — {MODULE_NAME} — Bug {BUG_ID}
Date: {TODAY}

## Core Operations
- [ ] Primary CREATE/SAVE action works correctly
- [ ] Primary EDIT/UPDATE action preserves all data
- [ ] Primary DELETE action cleans up child records
- [ ] SEARCH/FILTER returns correct results
- [ ] Primary CALCULATION produces correct output

## Security
- [ ] Malicious input in text fields → rejected or sanitized
- [ ] Direct API call without session → rejected

## Cross-Module
- [ ] Linked module data is not corrupted
- [ ] Reports that use this module's data still work

## Regression
- [ ] The specific bug scenario from {BUG_ID} is now fixed
- [ ] No new errors in PHP error log
- [ ] No new JS console errors
```

### 5. Sign-Off Report

Generate the verification sign-off:

```markdown
═══════════════════════════════════════════
VERIFICATION SIGN-OFF — {BUG_ID}
═══════════════════════════════════════════

Bug ID:               {BUG_ID}
Module:               {MODULE_NAME}
Fix Applied:          ✅ YES
File Changed:         {FILE_CHANGED}

Syntax Check:         ✅ PASS / ❌ FAIL
Unit Tests:           {X}/{Y} passed — ✅ PASS / ❌ FAIL
Smoke Tests:          ✅ PASS / ❌ FAIL

Bug Comment Present:  ✅ YES  — // BUG-FIX: {BUG_ID}
Unintended Changes:   NONE / {list any}

Rollback Plan:        {documented location}

Status:               ✅ APPROVED FOR DEPLOYMENT
                      ❌ NEEDS REVIEW — {reason}
═══════════════════════════════════════════
```

### 6. Report to User

Tell the user:
- All test results (pass/fail counts)
- Sign-off status (approved / needs review)
- If approved: "Bug {BUG_ID} is ready for deployment. Run `/brain-update {BUG_ID}` to update the knowledge base."
- If failed: which tests failed and what to do next
