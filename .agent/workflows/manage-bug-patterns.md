---
description: Initialize, search, or manage the Common Bug Patterns Library used across all modules
version: 1.1
last_updated: 2026-02-19
---

# Manage Bug Patterns Library Workflow

> **SOP Reference**: FINAL_SOP_BUG_REMEDIATION.md § 12 (Knowledge & Pattern Library)
> **SOP Location**: `bug_report_AI/SOP/FINAL_SOP_BUG_REMEDIATION.md` (if available)

## When to Use
- **First time**: To initialize the patterns library before the first audit
- **Before audit**: To load all patterns for Round 0 pre-scan
- **After fix**: To add a new pattern or update "Modules Found In"
- **On demand**: To scan a specific module against all known patterns
- User says "check common patterns", "initialize pattern library", or "scan {MODULE} for patterns"

## Pattern Library Location
`bug_report_AI/COMMON_BUG_PATTERNS.md`

## Steps

### Step 1: Check if Library Exists
// turbo
Check if `bug_report_AI/COMMON_BUG_PATTERNS.md` exists.

- **Exists** → read it, proceed to Step 3 or Step 4
- **Not found** → create it in Step 2

### Step 2: Initialize the Library (First Time Only)

Create `bug_report_AI/COMMON_BUG_PATTERNS.md` with the following seed content:

> [!IMPORTANT]
> **Pattern ID Formats**: The seed patterns below use `SYS-`, `ARCH-`, `BIZ-` prefixes. The live library (`COMMON_BUG_PATTERNS.md`) may use `PAT-{CATEGORY}-{SEQ}` format (e.g., `PAT-SEC-001`). When the live library already exists with patterns from a module audit, **the live library is authoritative**. Only use these seeds if initializing from scratch. Never overwrite an existing library with seeds.

```markdown
# Common Bug Patterns Library

> Auto-maintained by Antigravity. Updated after every audit and every fix.
> Last Updated: {TODAY}
>
> **Rules:**
> 1. Before any audit → Antigravity reads this file first
> 2. Before any fix → Check if a fix template already exists
> 3. After any fix → Check if pattern exists. If new, add it. If exists, add module to "Found In" list.
> 4. When generating execution plans → Reference fix templates from this library

---

## SYSTEM LEVEL PATTERNS

### SYS-001: Raw $_POST/$_GET Without Sanitization
**Detection**: Grep for `$_POST[` or `$_GET[` without `$this->input->post()`
**Risk**: SQL Injection, XSS
**Severity**: P0
**Fix Template**: Replace with `$this->input->post('field', TRUE)` (CodeIgniter sanitized input)
**Modules Found In**: _{update after each audit}_

### SYS-002: trans_commit() Without trans_status() Check
**Detection**: Grep for `trans_commit()` without `trans_status()` check nearby
**Risk**: Committing failed transactions → data corruption
**Severity**: P0
**Fix Template**:
```php
if ($this->db->trans_status() === FALSE) {
    $this->db->trans_rollback();
} else {
    $this->db->trans_commit();
}
```
**Modules Found In**: _{update after each audit}_

### SYS-003: Missing Try-Catch Around DB Operations
**Detection**: Functions with INSERT/UPDATE/DELETE but no try-catch
**Risk**: Unhandled exceptions → silent data loss
**Severity**: P1
**Fix Template**: Wrap in try-catch with rollback on exception
**Modules Found In**: _{update after each audit}_

### SYS-004: mkdir with 0777 Permissions
**Detection**: Grep for `mkdir(.*, 0777`
**Risk**: Security — world-writable directories
**Severity**: P1
**Fix Template**: Use `0755` for directories, `0644` for files
**Modules Found In**: _{update after each audit}_

### SYS-005: File Upload Without Type Validation
**Detection**: `$_FILES` used without MIME type or extension check
**Risk**: Malicious file upload
**Severity**: P0
**Fix Template**: Validate extension against whitelist + check MIME type
**Modules Found In**: _{update after each audit}_

---

## ARCHITECTURE LEVEL PATTERNS

### ARCH-001: Panel Selector vs Row Selector Mismatch
**Detection**: `$('#panel_element_id').val()` used inside per-row event handler
**Risk**: All rows read/write to same global element instead of their own
**Severity**: P1
**Fix Template**: Replace `$('#id')` with `curRow.find('.class')`
**Modules Found In**: LOT

### ARCH-002: Options Appended to Panel Only, Not Row
**Detection**: AJAX success writes `.append()` to `$('#select_xyz')` but NOT `curRow.find('.xyz')`
**Risk**: Row dropdown stays empty, only panel dropdown populated
**Severity**: P1
**Fix Template**: Append to BOTH `curRow.find('.class')` AND `$('#panel_select')`
**Modules Found In**: LOT

### ARCH-003: N+1 Query Pattern
**Detection**: SQL query inside a `foreach` or `for` loop
**Risk**: Performance — 100 items = 101 queries instead of 2
**Severity**: P2
**Fix Template**: Batch query outside loop, build lookup array
**Modules Found In**: _{update after each audit}_

### ARCH-004: Select2 Not Initialized on Dynamic Elements
**Detection**: `.select2()` called for static elements but not for dynamically added rows
**Risk**: Dropdown has no search/typeahead UI
**Severity**: P2
**Fix Template**: Initialize Select2 AFTER appending the row to DOM
**Modules Found In**: LOT

### ARCH-005: Event Handler Fires Multiple Times
**Detection**: `.on('change', ...)` bound inside a function that runs multiple times
**Risk**: Event fires N times instead of once → duplicate AJAX calls
**Severity**: P1
**Fix Template**: Use `.off('change').on('change', ...)` or delegate from parent
**Modules Found In**: _{update after each audit}_

### ARCH-006: Cartesian JOIN (Missing ON Condition)
**Detection**: JOIN with no ON clause or ON clause that matches all rows
**Risk**: Result set explodes — row count = table1 × table2
**Severity**: P1
**Fix Template**: Add proper ON condition with FK relationship
**Modules Found In**: _{update after each audit}_

---

## BUSINESS LEVEL PATTERNS

### BIZ-001: JS Calculation Not Matching PHP Calculation
**Detection**: Compare formulas in JS file vs PHP model for same field
**Risk**: User sees one value on screen, different value saved to DB
**Severity**: P0
**Fix Template**: Ensure identical formula in both layers, or remove JS calc and use server-only
**Modules Found In**: LOT

### BIZ-002: Missing Weight Boundary Validation
**Detection**: Weight subtraction without boundary check (less_wt > gross_wt)
**Risk**: Negative net weight → wrong stock values
**Severity**: P1
**Fix Template**: Add `if (less_wt > gross_wt) { reject; }` in both JS and PHP
**Modules Found In**: LOT

### BIZ-003: Hardcoded Default Values Instead of Config-Driven
**Detection**: Grep for magic numbers like `value="2"`, `type=1` in HTML/JS
**Risk**: Wrong default for some clients/configurations
**Severity**: P2
**Fix Template**: Read default from DB settings or master data
**Modules Found In**: LOT

### BIZ-004: Cancel Operation Doesn't Update Child Tables
**Detection**: Cancel/status-change updates only header table
**Risk**: Orphan records in detail/child tables still affect reports
**Severity**: P1
**Fix Template**: Update all related tables in same transaction
**Modules Found In**: LOT

### BIZ-005: Edit Allowed for System-Generated Records
**Detection**: Edit route has no check for `record_source` or `created_by`
**Risk**: User modifies system-generated data (imports, auto-calcs)
**Severity**: P2
**Fix Template**: Add gate check at edit entry point
**Modules Found In**: LOT
```

### Step 3: Search Patterns in a Specific Module

If user wants to scan a module against the patterns library:

// turbo
For **each** pattern in the library:
1. Execute the detection rule (grep/search) against the module's files:
   - `{CONTROLLER_FILE}` for SYS and ARCH patterns
   - `{MODEL_FILE}` for SYS and ARCH patterns
   - `{JS_FILE}` for ARCH and BIZ patterns
   - `{VIEW_DIR}` for SYS patterns (XSS)
2. Report matches:
```
Pattern {PATTERN_ID}: {PATTERN_NAME}
  → Found at: {file}:{line}
  → Status: Confirmed Bug / False Positive / Already Fixed
```
3. Summarize: `{N} pattern matches found in {MODULE_NAME} out of {TOTAL} patterns scanned`

### Step 4: Add a New Pattern

When a new pattern is discovered (from audit or fix):

Add to the appropriate section (System / Architecture / Business):
```markdown
### {LEVEL}-{NNN}: {Pattern Name}
**Detection**: {how to find this pattern via grep/search}
**Risk**: {what goes wrong}
**Severity**: {P0/P1/P2/P3}
**Fix Template**: {standard fix — before/after code}
**Modules Found In**: {MODULE_NAME}
**First Found**: {date}
```

### Step 5: Update Existing Pattern

When a known pattern is found in a new module:
1. Find the pattern entry by ID
2. Add `{MODULE_NAME}` to "Modules Found In" if not already listed
3. If the fix deviated from the template, update the fix template with the improved version

### Step 6: Report
```
✅ Pattern Library Status
   Total patterns: {N}
   System patterns: {N}
   Architecture patterns: {N}
   Business patterns: {N}
   If scan was done: {N} matches found in {MODULE_NAME}
   Last updated: {date}
```
