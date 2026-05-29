---
description: Initialize or manage the Common Bug Patterns Library across all modules
---

# Common Bug Patterns Library Management

**SOP Reference**: [FINAL_SOP_BUG_REMEDIATION.md](file:///c:/xampp/htdocs/etail_development_src/SOP/FINAL_SOP_BUG_REMEDIATION.md) — Section 12 (Knowledge & Pattern Library)

## When to Use
- To initialize the patterns library for the first time
- To review and clean up existing patterns
- To search for known patterns in a specific module
- User says "check common patterns" or "initialize pattern library"

## Pattern Library Location
`bug_report_AI/COMMON_BUG_PATTERNS.md`

## Steps

### 1. Check if Library Exists

// turbo
Check if `bug_report_AI/COMMON_BUG_PATTERNS.md` exists.

- If exists → read it, proceed to Step 3
- If not → create it in Step 2

### 2. Initialize the Library (First Time Only)

Create `bug_report_AI/COMMON_BUG_PATTERNS.md` with the known patterns from all previous audits and the finalized SOP.

**Seed patterns** (from SOP and past LOT module work):

```markdown
# Common Bug Patterns Library
> Updated after every audit and every fix.
> Before any audit → read this first.
> After any fix → check if pattern exists. If new, add it.

---

## SYSTEM LEVEL PATTERNS

### SYS-001: Raw $_POST/$_GET Without Sanitization
**Detection**: Grep for `$_POST[` or `$_GET[` without `$this->input->post()`
**Risk**: SQL Injection, XSS
**Fix Template**: Replace with `$this->input->post('field', TRUE)` (CodeIgniter sanitized input)
**Modules Found In**: {list}

### SYS-002: trans_commit() Without trans_status() Check
**Detection**: Grep for `trans_commit()` without `trans_status()` check nearby
**Risk**: Committing failed transactions → data corruption
**Fix Template**:
  if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
  } else {
      $this->db->trans_commit();
  }
**Modules Found In**: {list}

### SYS-003: Missing Try-Catch Around DB Operations
**Detection**: Functions with INSERT/UPDATE/DELETE but no try-catch
**Risk**: Unhandled exceptions → silent data loss
**Fix Template**: Wrap in try-catch with rollback
**Modules Found In**: {list}

### SYS-004: mkdir with 0777 Permissions
**Detection**: Grep for `mkdir(.*, 0777`
**Risk**: Security — world-writable directories
**Fix Template**: Use `0755` for directories, `0644` for files
**Modules Found In**: {list}

### SYS-005: File Upload Without Type Validation
**Detection**: `$_FILES` used without MIME type or extension check
**Risk**: Malicious file upload
**Fix Template**: Validate extension against whitelist + check MIME type
**Modules Found In**: {list}

---

## ARCHITECTURE LEVEL PATTERNS

### ARCH-001: Panel Selector vs Row Selector Mismatch
**Detection**: `$('#panel_element_id').val()` used inside per-row event handler
**Risk**: All rows read/write to same global element instead of their own
**Fix Template**: Replace `$('#id')` with `curRow.find('.class')`
**Modules Found In**: LOT

### ARCH-002: Options Appended to Panel Only, Not Row
**Detection**: AJAX success writes `.append()` to `$('#select_xyz')` but NOT `curRow.find('.xyz')`
**Risk**: Row dropdown stays empty, only panel dropdown populated
**Fix Template**: Append to BOTH `curRow.find('.class')` AND `$('#panel_select')`
**Modules Found In**: LOT

### ARCH-003: N+1 Query Pattern
**Detection**: SQL query inside a `foreach` or `for` loop
**Risk**: Performance — 100 items = 101 queries instead of 2
**Fix Template**: Batch query outside loop, build lookup array
**Modules Found In**: {list}

### ARCH-004: Select2 Not Initialized on Dynamically Created Elements
**Detection**: `.select2()` called for static elements but not for dynamically added rows
**Risk**: Dropdown has no search/typeahead UI
**Fix Template**: Initialize Select2 AFTER appending the row to DOM
**Modules Found In**: LOT

### ARCH-005: Event Handler Fires Multiple Times
**Detection**: `.on('change', ...)` bound inside a function that runs multiple times
**Risk**: Event fires N times instead of once → duplicate AJAX calls
**Fix Template**: Use `.off('change').on('change', ...)` or delegate from parent
**Modules Found In**: {list}

### ARCH-006: Cartesian JOIN (Missing ON Condition)
**Detection**: JOIN with no ON clause or ON clause that matches all rows
**Risk**: Result set explodes — row count = table1 × table2
**Fix Template**: Add proper ON condition with FK relationship
**Modules Found In**: {list}

---

## BUSINESS LEVEL PATTERNS

### BIZ-001: JS Calculation Not Matching PHP Calculation
**Detection**: Compare formulas in JS file vs PHP model for same field
**Risk**: User sees one value on screen, different value saved to DB
**Fix Template**: Ensure identical formula in both layers, or remove JS calc and use server-only
**Modules Found In**: LOT

### BIZ-002: Missing less_wt > gross_wt Validation
**Detection**: Weight subtraction without boundary check
**Risk**: Negative net weight → wrong stock values
**Fix Template**: Add `if (less_wt > gross_wt) { reject; }` in both JS and PHP
**Modules Found In**: LOT

### BIZ-003: Hardcoded Default Values Instead of Config-Driven
**Detection**: Grep for magic numbers like `value="2"`, `type=1` in HTML/JS
**Risk**: Wrong default for some clients/configurations
**Fix Template**: Read default from DB settings or master data
**Modules Found In**: LOT

### BIZ-004: Cancel Operation Doesn't Update Child Tables
**Detection**: Cancel/status-change updates only header table
**Risk**: Orphan records in detail/child tables still affect reports
**Fix Template**: Update all related tables in same transaction
**Modules Found In**: LOT

### BIZ-005: Edit Allowed for System-Generated Records
**Detection**: Edit route has no check for `record_source` or `created_by`
**Risk**: User modifies system-generated data (imports, auto-calcs)
**Fix Template**: Add gate check at edit entry point
**Modules Found In**: LOT
```

### 3. Search Patterns in a Specific Module

If user wants to scan a module against the patterns library:

// turbo
For each pattern in the library:
1. Run the detection rule (grep/search) against the module's files
2. Report matches:
```
Pattern {PATTERN_ID}: {PATTERN_NAME}
  → Found at: {file}:{line}
  → Status: Confirmed Bug / False Positive / Already Fixed
```

### 4. Add a New Pattern

When a new pattern is discovered (from audit or fix):

Add to the appropriate section (System / Architecture / Business):
```markdown
### {LEVEL}-{NNN}: {Pattern Name}
**Detection**: {how to find this pattern via grep/search}
**Risk**: {what goes wrong}
**Fix Template**: {standard fix}
**Modules Found In**: {MODULE_NAME}
**First Found**: {date}
```

### 5. Report to User

Tell the user:
- Total patterns in library: {N}
- System patterns: {N}
- Architecture patterns: {N}
- Business patterns: {N}
- If scanning was done: {N} matches found in {MODULE_NAME}
