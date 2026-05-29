---
description: "Fix a System-Level (Level 1) bug — fully automated by Antigravity. Human reviews diff only."
---

# /fix-system-bug — Level 1: System-Level Bug Fix

> **Source:** MASTER_BUG_REMEDIATION_PROCEDURE.md § 4, FINAL_SOP_BUG_REMEDIATION.md § 6
> **Automation Level:** 95% — Antigravity handles everything. Human reviews diff only.
> **Time Target:** < 30 minutes per bug
> **Applies to:** Security, Exception Handling, Variable/Copy-Paste, Debug Artifacts, Syntax/Runtime bugs

## Prerequisites
- Bug ID assigned (format: `{PREFIX}-{ROUND}{SEQ}`)
- Bug exists in the execution plan at `{BUG_REPORT_DIR}/{MODULE}_BUG_FIX_EXECUTION_PLAN.md`
- Module variables template filled at `SOP/modules/{MODULE_NAME}_variables.md`

## Core Principles
> **ONE bug at a time. No batch fixes. Stability > Elegance. Documentation first.**

1. **Isolation** — Each bug fixed in its own change
2. **Traceability** — Every change references the Bug ID
3. **Reversibility** — Documented rollback plan
4. **Surgical precision** — Minimal fix, no opportunistic refactoring
5. **Pattern reuse** — Check COMMON_BUG_PATTERNS.md before writing fix from scratch

---

## Step 1: REVIEW BUG CARD [🤖 AUTO]

1. Read bug details from the execution plan:
   - Use `view_file` on `{BUG_REPORT_DIR}/{MODULE}_BUG_FIX_EXECUTION_PLAN.md`
   - Locate the section for `{BUG_ID}`

2. Cross-reference COMMON_BUG_PATTERNS.md (if exists):
   - Use `view_file` on `bug_report_AI/COMMON_BUG_PATTERNS.md`
   - Check if this bug matches a known pattern
   - If match found → use the existing fix template

3. Confirm Level classification:
   - System 🟢 — This workflow is for system-level bugs only
   - If this is actually Architecture 🟡 → use `/fix-architecture-bug`
   - If this is actually Business 🔴 → use `/fix-business-bug`

## Step 2: VERIFY ROOT CAUSE [🤖 AUTO]

1. Read the exact source code at the identified location:
   - Use `view_file` with the exact file and line range from the execution plan
   - Confirm the code matches the bug description

2. If the code has already been modified (e.g., by a previous fix):
   - Check if the bug is already fixed → mark as ALREADY_FIXED and stop
   - If not → note the change and adjust the fix accordingly

## Step 3: ASSESS IMPACT [🤖 AUTO]

Evaluate using these dimensions:

| Dimension | Check | Result |
|-----------|-------|--------|
| Security? | Could this be exploited by malicious input? | YES/NO |
| Data? | Could this corrupt stored records? | YES/NO |
| Financial? | Does this affect money/weight calculations? | YES/NO |
| Cross-Module? | Check Brain Component 4 for dependencies | YES/NO |

Risk Level: **Low** (single-line fix) / **Medium** (function-level) / **High** (cross-function)

## Step 4: LOCATE EXACT CODE [🤖 AUTO]

1. Find exact file + line number
2. Use `grep_search` if needed to locate the precise code
3. Find all related occurrences if the bug pattern repeats (e.g., same SQL injection in multiple methods)

## Step 5: READ CURRENT CODE [🤖 AUTO]

1. Read the code verbatim using `view_file`
2. No assumptions — confirm that what's in the file matches the execution plan
3. If code has been changed since the audit, note the differences

## Step 6: APPLY MINIMAL FIX [🤖 AUTO]

1. Apply the fix using `replace_file_content` or `multi_replace_file_content`
2. Rules:
   - NO refactoring beyond the fix
   - NO renaming variables
   - NO restructuring logic
   - Use patterns already established in the same file
   - If a fix template exists in COMMON_BUG_PATTERNS.md, use it
3. Add a comment noting the Bug ID: `// Fix: {BUG_ID} — {brief description}`

### Fix Templates by Category:

**Security (SQL Injection):**
```php
// BEFORE: Raw variable in query
$this->db->where("column = '$value'");
// AFTER: Parameterized
$this->db->where('column', $value);
```

**Exception Handling (Missing Rollback):**
```php
// BEFORE: Commits even on failure
$this->db->trans_start();
// ... operations ...
$this->db->trans_complete();
// AFTER: Status check
$this->db->trans_start();
// ... operations ...
if ($this->db->trans_status() === FALSE) {
    $this->db->trans_rollback();
    // handle error
} else {
    $this->db->trans_commit();
}
```

**Variable/Copy-Paste:**
```
// Simply replace the wrong variable with the correct one
// e.g., $items.tag_id → items[i].tag_id
```

**Debug Artifacts:**
```
// Simply remove console.log(), print_r(), var_dump() lines
```

## Step 7: SYNTAX CHECK [🤖 AUTO]

For PHP files:
// turbo
```
& "C:\php8\php.exe" -l "{FIXED_FILE_PATH}"
```

For JS files:
- Note in the report that browser console check is required during smoke test

If syntax check FAILS:
- Review the fix for syntax errors
- Correct and re-run syntax check
- Do NOT proceed until syntax check passes

## Step 8: WRITE & RUN TESTS [🤖 AUTO]

1. Create a PHPUnit test file: `admin/tests/{BUG_ID}Test.php`
2. The test MUST:
   - Test the FIXED behavior (positive test)
   - Test the vulnerability is closed (negative test for security bugs)
   - Test edge cases relevant to the fix
3. Run the test:

// turbo
```
cd "c:\xampp 7.1\htdocs\etail_development_src\admin\tests" && & "C:\php8\php.exe" vendor/bin/phpunit --no-configuration {BUG_ID}Test.php --testdox
```

If tests FAIL:
- Diagnose the failure
- Fix the test or the code (if the fix was wrong)
- Re-run until all tests pass

## Step 9: REGRESSION CHECKLIST [🤖 + 👤]

Based on fix scope:
- **Single-line fix** → Smoke test affected action only
- **Function-level fix** → Test affected function + callers
- **Cross-function fix** → Test affected module end-to-end

Document what was checked and the result.

## Step 10: DOCUMENT ROLLBACK PLAN [🤖 AUTO]

Document exact steps to undo this specific fix:

```markdown
### Rollback Plan for {BUG_ID}
1. Revert file: {FILE_PATH}
2. Restore original code at line {LINE}:
   ```
   {ORIGINAL_CODE}
   ```
3. Run syntax check to confirm
4. Verify system returns to pre-fix state
```

## Step 11: PRESENT DIFF FOR APPROVAL [👤 HUMAN]

Present to the human:
1. Summary of what was changed
2. The diff (before/after)
3. Test results
4. Risk assessment
5. Rollback plan

Human actions:
- **APPROVE** → Proceed to update knowledge base (use `/update-knowledge-base`)
- **REJECT** → Undo changes, re-assess
- **MODIFY** → Apply modifications, re-run from Step 7

## Post-Fix: Update Tracking

1. Update the execution plan: mark bug as COMPLETED with date
2. Update the consolidated bug report: change status to FIXED
3. If this is a new pattern → update COMMON_BUG_PATTERNS.md
