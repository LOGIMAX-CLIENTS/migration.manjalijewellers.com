---
description: "Phase 6 — Post-fix knowledge update. Updates Module Brain, Pattern Library, and documentation."
---

# /update-knowledge-base — Phase 6: Knowledge Update & Closure

> **Source:** MASTER_BUG_REMEDIATION_PROCEDURE.md § 13, FINAL_SOP_BUG_REMEDIATION.md § 12
> **Automation Level:** 80% — Antigravity generates documentation. Human reviews.

## Prerequisites
- Bug fix completed and approved
- Bug ID known

---

## Step 1: Update Module Brain [🤖 AUTO]

Read the Module Brain at `{BRAIN_DIR}/MODULE_BRAIN.md` and update:

### Anti-Pattern Note
Add to the relevant section:
```markdown
### Anti-Pattern: {BUG_ID}
- **What was broken:** {description}
- **Root Cause:** {why it was coded wrong}
- **How it was fixed:** {the fix applied}
- **Lesson:** {what to avoid in future}
```

### "Why It Was Done This Way" Note
If the original code had a reason for being the way it was, document it:
```markdown
### Developer Intent Note: {BUG_ID}
- **Original approach:** {what the code did}
- **Likely intent:** {why the developer wrote it this way}
- **Problem:** {why that approach failed}
- **Corrected approach:** {what we changed it to}
```

### Data Flow Update
If the fix changed how data flows through the module:
- Update `{BRAIN_DIR}/DATA_FLOW.md` with the corrected flow
- Note which step changed and why

### Business Rules Update
If the fix revealed a new or corrected business rule:
- Update `{BRAIN_DIR}/BUSINESS_RULES.md`
- Note the canonical rule number and the correction

## Step 2: Update Pattern Library [🤖 AUTO]

Check `bug_report_AI/COMMON_BUG_PATTERNS.md`:

### If this bug matches an EXISTING pattern:
Add this module to the "Found In" list for that pattern:
```markdown
- **Found In:** Estimation, Billing, **{MODULE_NAME}** ← NEW
```

### If this is a NEW pattern:
Add a new entry:
```markdown
## Pattern: {PATTERN_NAME}

- **First Found In:** {MODULE_NAME} ({BUG_ID})
- **Category:** {1-8}
- **Level:** System 🟢 / Architecture 🟡 / Business 🔴
- **Detection Rule:** {how to find this pattern}
  - Grep: `{search pattern}`
- **Description:** {what the bug is}
- **Root Cause:** {why it happens}
- **Fix Template:**
  ```
  // BEFORE (buggy):
  {buggy code pattern}
  
  // AFTER (fixed):
  {fixed code pattern}
  ```
- **Found In:** {MODULE_NAME}
```

If COMMON_BUG_PATTERNS.md doesn't exist, create it with the header:
```markdown
# Common Bug Patterns Library
> Auto-maintained by Antigravity. Updated after every audit and fix.
> Last Updated: {TODAY}

## How to Use
- Before any audit: Read this file first
- Before any fix: Check if a fix template already exists
- After any fix: Add new patterns or update "Found In" lists
```

## Step 3: Update Bug Tracking [🤖 AUTO]

Update the execution plan (`{BUG_REPORT_DIR}/{MODULE}_BUG_FIX_EXECUTION_PLAN.md`):
- Status: `COMPLETED ✅`
- Date Fixed: `{TODAY}`
- Tests: `{PASS/FAIL}`
- Fixed By: `Antigravity + {DEVELOPER}`

Update the consolidated report (`{BUG_REPORT_DIR}/CONSOLIDATED_BUG_REPORT.md`):
- Change status column to `FIXED`

## Step 4: Generate Fix Report Entry [🤖 AUTO]

Append to `{BUG_REPORT_DIR}/{MODULE}_FIX_REPORT.md`:

```markdown
---
### Fix: {BUG_ID} — {BUG_TITLE}
- **Date:** {TODAY}
- **Level:** System 🟢 / Architecture 🟡 / Business 🔴
- **Category:** {category}
- **Severity:** {P0/P1/P2/P3}
- **Files Changed:** {list}
- **Root Cause:** {brief}
- **Fix Applied:** {brief}
- **Tests:** {PASS/FAIL — N tests, M assertions}
- **Rollback:** {reference to rollback plan}
---
```

## Step 5: Post-Fix Checklist [🤖 + 👤]

```
[ ] Module Brain updated with anti-pattern
[ ] COMMON_BUG_PATTERNS.md updated (if applicable)
[ ] Bug card status → COMPLETED
[ ] Fix report entry generated
[ ] If new business rule discovered → added to Canonical Rules
[ ] Execution plan updated
[ ] Consolidated report updated
```

## Completion
Once all items are checked, this bug is officially CLOSED.
Proceed to the next bug: use `/fix-single-bug {NEXT_BUG_ID}`
