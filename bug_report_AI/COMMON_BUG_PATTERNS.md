# 🔍 Common Bug Patterns Library

> **Purpose**: Cross-module reuse — every bug pattern found anywhere is cataloged here so Antigravity can instantly detect it in new modules.
> **Last Updated**: 2026-03-07
> **Seed Source**: Estimation Module Audit (65 bugs → 17 patterns extracted) + Billing Module Scan (+2 new patterns) + Scheme Module Scan (+0 new patterns, 3 existing patterns confirmed) + chit_settings Module Scan (+0 new patterns, 4 existing patterns confirmed — PAT-QRY-001×5, PAT-SEC-002×8, PAT-SEC-003×7, PAT-RAW-001×5)

---

## How to Use This Library

1. **Before any audit**: Antigravity reads this file and greps for each detection rule in the target module
2. **After any fix**: Check if the bug matches an existing pattern. If new → add it. If existing → add module to "Found In"
3. **When fixing**: Use the fix template as a starting point — adapt to the specific context

---

## Pattern Index

| ID          | Pattern Name                      | Category     | Default Severity | Modules Found In    |
| ----------- | --------------------------------- | ------------ | ---------------- | ------------------- |
| PAT-SEC-001 | Column Name SQL Injection         | Security     | P0               | Estimation, Billing, Branch Transfer |
| PAT-SEC-002 | Raw $\_POST Bypass                | Security     | P2               | Estimation, Billing, Branch Transfer |
| PAT-SEC-003 | mkdir 0777 Permissions            | Security     | P2               | Estimation, Billing |
| PAT-SEC-004 | XSS via Unescaped Output          | Security     | P1               | Estimation, Branch Transfer |
| PAT-SEC-005 | Live DB Error / SQL Query Leaks   | Security     | P1               | Branch Transfer |
| PAT-TXN-001 | trans_commit on Failure Branch    | Transaction  | P0               | Estimation, Billing, Branch Transfer |
| PAT-TXN-002 | MyISAM on Transactional Table     | Transaction  | P1               | Estimation          |
| PAT-TXN-003 | trans_commit Without trans_status Check | Transaction | P1         | Billing, Branch Transfer |
| PAT-TXN-004 | Orphaned trans_begin in Read-Only Function | Transaction | P0       | Branch Transfer |
| PAT-QRY-001 | Cartesian JOIN (Self-Reference)   | Query Logic  | P1               | Estimation          |
| PAT-SEC-001 | Column Name SQL Injection         | Security     | P0               | Estimation, Billing |
| PAT-SEC-002 | Raw $\_POST Bypass                | Security     | P2               | Estimation, Billing, Scheme, Settings |
| PAT-SEC-003 | mkdir 0777 Permissions            | Security     | P2               | Estimation, Billing, Settings |
| PAT-SEC-004 | XSS via Unescaped Output          | Security     | P1               | Estimation, Scheme, Settings |
| PAT-TXN-001 | trans_commit on Failure Branch    | Transaction  | P0               | Estimation, Billing |
| PAT-TXN-002 | MyISAM on Transactional Table     | Transaction  | P1               | Estimation          |
| PAT-QRY-001 | Cartesian JOIN (Self-Reference)   | Query Logic  | P1               | Estimation, Settings |
| PAT-QRY-002 | Missing GROUP BY with Aggregate   | Query Logic  | P1               | Estimation          |
| PAT-QRY-003 | Missing WHERE Scope Filter        | Query Logic  | P2               | Estimation          |
| PAT-VAR-001 | Copy-Paste Variable Mismatch      | Variable     | P0–P1            | Estimation, Branch Transfer |
| PAT-VAR-002 | Undefined Variable Assignment     | Variable     | P1               | Estimation          |
| PAT-VAR-003 | $returndata vs $return_data Typo  | Variable     | P1               | Estimation          |
| PAT-VAL-001 | Always-True Condition (>= 0)      | Validation   | P0               | Estimation          |
| PAT-VAL-002 | Single Flag Overwrite in Loop     | Validation   | P1               | Estimation          |
| PAT-VAL-003 | Validation Mutates Form Data      | Validation   | P1               | Estimation          |
| PAT-SCH-001 | Missing AUTO_INCREMENT on PK      | Schema       | P0               | Estimation          |
| PAT-SCH-002 | Integer Column for Decimal Data   | Schema       | P2               | Estimation          |
| PAT-RAW-001 | Raw SQL with String Concatenation | Query Safety | P1               | Billing, Branch Transfer |
| PAT-CON-001 | TOCTOU Sequence Generation        | Concurrency  | P1               | Purchase, Branch Transfer |
| PAT-LOGIC-001 | Blind Ratio Distribution to Ineligible Items | Logic | P1 | Billing |
| PAT-LOGIC-002 | Return-Omission in Debt Calculation | Logic | P0 | Billing, Reports |
| PAT-LOGIC-003 | Round-Off Omission in Balance Calculation | Logic | P1 | Billing |
| PAT-LOGIC-004 | Zero Value Loss During Fallback | Logic | P1 | Billing, Branch Transfer |
| PAT-JS-001 | Un-scoped Global Checkbox «Select All» | JavaScript | P1 | Branch Transfer |
| PAT-JS-002 | Hardcoded System/Branch ID in Business Logic | JavaScript | P0 | Branch Transfer |
| PAT-JS-003 | Global Scope Leak (Undeclared Variables) | JavaScript | P2 | Branch Transfer |
| PAT-JS-004 | Deprecated select2("val", ...) API | JavaScript | P2 | Branch Transfer |
| PAT-JS-005 | Event Listener Accumulation inside Change Handler | JavaScript | P1 | Branch Transfer |
| PAT-DOM-001 | Duplicate DOM IDs in Same Form | UI/DOM | P1 | Branch Transfer |
| PAT-NULL-001 | Null Object Reference (Row-Access Without Guard) | Variable | P0–P1 | Branch Transfer |
| PAT-LOGIC-004 | Zero Value Loss During Fallback | Logic | P1 | Billing |
| PAT-RAW-001 | Raw SQL with String Concatenation | Query Safety | P1               | Billing, Scheme, Settings |
| PAT-TXN-003 | trans_commit Without trans_status | Transaction  | P1               | Billing             |
| PAT-LOGIC-005 | Using Transaction Date for Historical Event | Logic | P1 | Billing |
| --- | --- | --- | --- | --- |
| PAT-SEC-001 | Column Name SQL Injection | Security | P0 | Estimation |
| PAT-SEC-002 | Raw $_POST Bypass | Security | P2 | Estimation, Billing, Branch Transfer |
| PAT-SEC-003 | mkdir 0777 Permissions | Security | P2 | Estimation |
| PAT-SEC-004 | XSS via Unescaped Output | Security | P1 | Estimation |
| PAT-SEC-005 | Live DB Error / SQL Query Leaks | Security | P1 | Branch Transfer |
| PAT-TXN-001 | trans_commit on Failure Branch | Transaction | P0 | Estimation |
| PAT-TXN-002 | MyISAM on Transactional Table | Transaction | P1 | Estimation |
| PAT-QRY-001 | Cartesian JOIN (Self-Reference) | Query Logic | P1 | Estimation, Purchase |
| PAT-QRY-002 | Missing GROUP BY with Aggregate | Query Logic | P1 | Estimation |
| PAT-QRY-003 | Missing WHERE Scope Filter | Query Logic | P2 | Estimation |
| PAT-VAR-001 | Copy-Paste Variable Mismatch | Variable | P0–P1 | Estimation, Branch Transfer |
| PAT-VAR-002 | Undefined Variable Assignment | Variable | P1 | Estimation |
| PAT-VAR-003 | $returndata vs $return_data Typo | Variable | P1 | Estimation |
| PAT-VAL-001 | Always-True Condition (>= 0) | Validation | P0 | Estimation |
| PAT-VAL-002 | Single Flag Overwrite in Loop | Validation | P1 | Estimation |
| PAT-VAL-003 | Validation Mutates Form Data | Validation | P1 | Estimation |
| PAT-SCH-001 | Missing AUTO_INCREMENT on PK | Schema | P0 | Estimation |
| PAT-SCH-002 | Integer Column for Decimal Data | Schema | P2 | Estimation |
| PAT-INT-001 | Implicit Event Object + Async Timing | Integration | P2 | Purchase |
| PAT-QRY-004 | Missing FOR UPDATE Lock on Sequence | Query Logic | P1 | Purchase |
| PAT-QRY-004 | Missing IFNULL on LEFT JOIN Column | Query Logic | P2 | Purchase |
| PAT-INT-001 | Unchecked file_get_contents Corrupting JSON | Integration | P1 | Purchase |
| PAT-CON-001 | TOCTOU Sequence Generation | Concurrency | P1 | Purchase |
| PAT-INT-001 | Missing Balance Nature (CR/DR) Indicator | Integration/UI | P2 | Purchase |
| PAT-CALC-001 | Cascading Tax in Client-Side Calculation | Logic | P0 | Purchase |
| PAT-UI-001 | UI Label Mismatch / Wrong Field Position | UI/Display | P1 | Purchase |
| PAT-QRY-005 | NULL/Empty Fallback in SQL Display Column | Query Logic | P1 | Reports |
| PAT-UI-001 | Async Balance Overwrite in Bill-wise Selection | UI/Logic | P1 | Purchase |
| PAT-QRY-006 | UNION Scope Leak — Data in Wrong Row Type | Query Logic | P2 | Reports |
| PAT-AUD-001 | Destructive Operation Without Audit Trail | Audit/Safety | P1 | Services (Scheme Account) |

---

## Detailed Patterns

### PAT-DOM-001 — Duplicate DOM IDs

**Category**: UI/DOM | **Severity**: P1

**Description**: Multiple HTML elements on the same page share the same `id` attribute. This causes jQuery selectors like `$("#elem_id")` to predictably fetch only the *first* occurring element in the DOM tree, silently ignoring others and breaking subsequent logic expecting unique bindings.

**Detection Rule**:
Visual inspection of view files containing duplicated rendering blocks (like "Non-Tagged" vs "Tagged" forms on single views).

**Fix Template**:
Rename the IDs to guarantee uniqueness (e.g. `id="elem_id"` vs `id="elem_id_nt"`). Map Javascript initializations specifically to the element context (using `$(e.target)`) instead of sweeping `#elem_id` assignments.

**Bug Occurrences**: BRN-R401 (Branch Transfer)

---

### PAT-SEC-001 — Column Name SQL Injection

**Category**: Security | **Severity**: P0

**Description**: User-controlled value (from `$_POST` or `$this->input->post()`) used to construct column names, table names, or ORDER BY clauses. Query bindings (`?` placeholders) cannot protect column names — only VALUES.

**Detection Rule**:

```
grep -n '$searchField\|$sortField\|$orderField\|$columnName' {MODEL_FILE}
```

Then trace if the variable originates from user input.

**Fix Template**:

```php
// BEFORE (vulnerable):
$this->db->like($searchField, $searchValue);

// AFTER (safe):
$allowed_columns = ['column_a', 'column_b', 'column_c'];
if (!in_array($searchField, $allowed_columns, true)) {
    $searchField = 'column_a'; // safe default
}
$this->db->like($searchField, $searchValue);
```

**Estimation Bugs**: EST-R301 (10+ methods)
**Billing Match**: Model `ret_billing_model.php` L293, L5024 — `$field` and `$SearchTxt` used in raw SQL from POST

---

### PAT-SEC-002 — Raw $\_POST Bypass

**Category**: Security | **Severity**: P2

**Description**: Using `$_POST['field']` instead of `$this->input->post('field')`. Raw `$_POST` bypasses CodeIgniter's XSS filtering.

**Detection Rule**:

```
grep -n '\$_POST\[' {CONTROLLER_FILE}
```

**Fix Template**:

```php
// BEFORE:
$value = $_POST['field_name'];

// AFTER:
$value = $this->input->post('field_name');
```

**Estimation Bugs**: EST-R605 (12 endpoints)
**Billing Match**: Controller has 50+ `$_POST[]` uses (L307, L1119, L5196, L5198, L5253, etc.) + Model has 19 `$_POST[]` uses (L678, L5024, L8272, etc.)
**Scheme Hits**: Controller L428, L469, L489, L505, L520, L521, L537, L794, L813, L868, L909, L926, L944, L1213, L1220, L1228 — 16 confirmed `$_POST` uses in `admin_scheme.php`
**Settings Hits**: `admin_settings.php` L2151 — `$clear_by = $_POST;` in `clear_database()` uses raw `$_POST` directly (this is particularly dangerous as the function truncates DB tables based on mode/selected keys)

---

### PAT-SEC-003 — mkdir 0777 Permissions

**Category**: Security | **Severity**: P2

**Description**: Creating directories with world-writable permissions.

**Detection Rule**:

```
grep -n 'mkdir.*0777\|mkdir.*777' {CONTROLLER_FILE} {MODEL_FILE}
```

**Fix Template**:

```php
// BEFORE:
mkdir($path, 0777, true);

// AFTER:
mkdir($path, 0755, true);
```

**Estimation Bugs**: EST-R607
**Billing Match**: Controller L101, L1638, L1643, L4560, L6879, L6884 (6 instances of `mkdir($path, 0777, TRUE)`)
**Settings Hits**: Controller `admin_settings.php` L2256, L2623, L2646, L2662, L3391, L3499, L3524 — **7 instances** of `mkdir($path, 0777, TRUE)` across import/upload handlers

---

### PAT-SEC-004 — XSS via Unescaped Output

**Category**: Security | **Severity**: P1

**Description**: User-supplied or flash data rendered in HTML without escaping.

**Detection Rule**:

```
grep -n 'flashdata\|echo \$\|<?= \$' {VIEW_DIR}/*.php
```

**Fix Template**:

```php
// BEFORE:
<?= $this->session->flashdata('message') ?>

// AFTER:
<?= htmlspecialchars($this->session->flashdata('message'), ENT_QUOTES, 'UTF-8') ?>
```

**Estimation Bugs**: EST-R409
**Tagging Hits**: 60 instances across view files (flashdata/unescaped output)
**Scheme Hits**: 11 view files render `$message['message']` (from flashdata) via `echo $message['message']` without `htmlspecialchars()` — files: `scheme_reg.php`, `scheme_group.php`, `approval.php`, `detail_list.php`, `list.php` (settlement), `sales_trasnfer.php`, `sales_ret_transfer.php` (sales_transfer), `customerschemelist.php`, `acc_list.php`, `closing/list.php`, `opening/list.php`
**Settings Hits**: 14+ view files under `views/settings/` render `$message['message']` and `$message['title']` unescaped — files: `card_list.php`, `comp_list.php`, `export/export_data.php`, `export/export_account.php`, `retail_setting/list.php`, `module/list.php`, `import/import_customer.php`, `menu/list.php`, `unreg_cus_list.php`, `import/import_account.php`, `setting_list.php`, `terms/list.php`, `import/import_list.php`, `import/import_data.php`, `gateway_list.php`, `general/form.php`, `general/list.php`, `profile/list.php`

---

### PAT-SEC-005 — Live Database Error / SQL Query Leaks

**Category**: Security | **Severity**: P1

**Description**: The application exposes internal database error messages (`_error_message()`) and/or raw SQL queries (`last_query()`) to the client during a failure state. This provides attackers with database schema intelligence and facilitates SQL injection payload crafting. This can occur either as a raw string `echo` or structured within a JSON response array.

**Detection Rule**:

```
grep -n 'last_query\|_error_message' {CONTROLLER_FILE} {MODEL_FILE}
```

**Fix Template**:

```php
// BEFORE:
echo $this->db->last_query();
echo $this->db->_error_message();
$result['err'] = $this->db->_error_message();

// AFTER (Log server-side instead):
log_message('error', 'Module Name - DB error: ' . $this->db->_error_message() . ' | Query: ' . $this->db->last_query());
// Send generic safe message to client
$result['message'] = 'An internal error occurred.';
```

**Branch Transfer Match**: BRN-103 (Controllers `L272`, `L273`, `L932` — naked `echo` injected into JSON response output)

---

### PAT-TXN-001 — trans_commit on Failure Branch

**Category**: Transaction | **Severity**: P0

**Description**: After `trans_begin()`, the failure/error branch calls `trans_commit()` instead of `trans_rollback()`, persisting corrupt partial state.

**Detection Rule**:

```
Search for trans_commit() calls, then check if they are inside if(false)/catch/error blocks.
```

**Fix Template**:

```php
// BEFORE:
if ($error) {
    $this->db->trans_commit(); // BUG: should rollback
}

// AFTER:
if ($error) {
    $this->db->trans_rollback();
}
```

**Estimation Bugs**: EST-R601
**Billing Match**: 37 `trans_commit()` calls vs 23 `trans_status()` checks — 14+ transaction blocks commit without verifying status first

---

### PAT-TXN-002 — MyISAM on Transactional Table

**Category**: Transaction | **Severity**: P1

**Description**: Table uses MyISAM engine but is accessed within `trans_begin()`/`trans_commit()` blocks. MyISAM ignores transactions silently — data is committed immediately regardless of rollback.

**Detection Rule**:

```sql
SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES
WHERE TABLE_SCHEMA = '{database}' AND ENGINE = 'MyISAM'
AND TABLE_NAME LIKE '%{module}%';
```

Cross-reference with code: does the controller use `trans_begin()` when writing to this table?

**Fix Template**:

```sql
ALTER TABLE `{table_name}` ENGINE=InnoDB;
```

> [!CAUTION]
> Large tables may lock during conversion. Run during maintenance window.

**Estimation Bugs**: EST-S02

---

### PAT-QRY-001 — Cartesian JOIN (Self-Reference)

**Category**: Query Logic | **Severity**: P1

**Description**: JOIN condition references the same table alias on both sides (e.g., `b.col = b.col`), producing a Cartesian product — every row joins with every other row.

**Detection Rule**:

```
Look for JOIN ... ON conditions where both sides reference the same table alias.
```

**Fix Template**:

```php
// BEFORE:
->join('table_b b', 'b.type = b.type')  // self-reference!

// AFTER:
->join('table_b b', 'a.type = b.type')  // correct cross-table reference
```

**Estimation Bugs**: EST-R302, EST-R308
**Settings Hits**: `admin_settings_model.php` L896–907 — `get_company()` has `join chit_settings cs` with **no ON clause** — Cartesian JOIN! Also L1048: `metal_ratesDB()` has `join chit_settings cs` with no ON clause. Both produce row count = `metal_rates` × `chit_settings` rows (expected to be 1, but fragile by design). Risk is low when `chit_settings` has exactly 1 row, but becomes a silent multiplier bug if a second row ever appears.

---

### PAT-QRY-002 — Missing GROUP BY with Aggregate

**Category**: Query Logic | **Severity**: P1

**Description**: Subquery uses `SUM()`, `COUNT()`, or other aggregate functions but lacks `GROUP BY`. Returns arbitrary results or only the first row.

**Detection Rule**:

```
Look for SUM(, COUNT(, AVG(, MAX(, MIN( in SQL strings, then check for GROUP BY.
```

**Fix Template**:

```php
// BEFORE:
$this->db->select('item_id, SUM(tax_amount) as total_tax');

// AFTER:
$this->db->select('item_id, SUM(tax_amount) as total_tax');
$this->db->group_by('item_id');
```

**Estimation Bugs**: EST-R303 (3 methods)
**Tagging Hits**: 140 aggregate functions vs 103 GROUP BY — ~37 potential missing GROUP BY instances

---

### PAT-QRY-003 — Missing WHERE Scope Filter

**Category**: Query Logic | **Severity**: P2

**Description**: Query retrieves data without filtering by branch, company, or status — returns data from other tenants/contexts.

**Detection Rule**: Check queries for missing `branch_id`, `company_id`, or `status` filters when the table has those columns.

**Estimation Bugs**: EST-R309
**Tagging Hits**: 80 queries from main tables but only 4 branch/company filters — most queries unscoped

---

### PAT-VAR-001 — Copy-Paste Variable Mismatch

**Category**: Variable | **Severity**: P0–P1

**Description**: Copy-pasted code block uses a variable name from the original context instead of the new context. Most common: similar functions use similar but not identical variable names.

**Detection Rule**: Compare parallel functions (e.g., `get_tag_data` vs `get_tag_barcode_data`) and look for variables that should differ but don't.

**Fix Template**: Identify the correct variable for the context and replace. Always verify by checking both the source and destination functions.

**Estimation Bugs**: EST-R501 (market rate tax), EST-R502 (items.tag_id), EST-001 ($arrayMaterials)

---

### PAT-VAR-002 — Undefined Variable Assignment

**Category**: Variable | **Severity**: P1

**Description**: Code assigns a value to a field using a variable that was never defined in the current scope. Result: `undefined` (JS) or `null` (PHP) stored in the field.

**Detection Rule**: Look for variable names used on the right side of an assignment that have no prior assignment or declaration in the same scope.

**Estimation Bugs**: EST-R503 (rate_per_grm)

---

### PAT-VAR-003 — $returndata vs $return_data Typo

**Category**: Variable | **Severity**: P1

**Description**: Inconsistent naming convention causes a typo — data is assigned to one variable but the return statement uses a different name.

**Detection Rule**:

```
grep -n '\$return' {MODEL_FILE} | sort
```

Look for inconsistent naming patterns.

**Estimation Bugs**: EST-R304\r\n**Tagging Hits**: Mixed `$returnData` (15 occurrences) and `$return_data` (5 occurrences) across model methods

---

### PAT-VAL-001 — Always-True Condition (>= 0)

**Category**: Validation | **Severity**: P0

**Description**: Condition like `$('#table tbody tr').length >= 0` is always true because `.length` is never negative. The intent was to check `> 0` (has rows).

**Detection Rule**:

```
grep -n '\.length >= 0\|\.length > -1' {JS_FILE}
```

**Fix Template**:

```javascript
// BEFORE:
if ($('#est_tag_table tbody tr').length >= 0) { ... }  // always true

// AFTER:
if ($('#est_tag_table tbody tr').length > 0) { ... }    // checks for rows
```

**Estimation Bugs**: EST-R402

---

### PAT-VAL-002 — Single Flag Overwrite in Loop

**Category**: Validation | **Severity**: P1

**Description**: A single `form_validate` boolean is set by multiple section checks sequentially. The last section's result overwrites all prior failures. If section 1 fails but section 3 passes, the form submits.

**Detection Rule**: Look for a single validation flag variable being reassigned in sequence without &&-chaining or early return.

**Fix Template**:

```javascript
// BEFORE:
form_validate = validateSection1();
form_validate = validateSection2(); // overwrites section 1 result
form_validate = validateSection3();

// AFTER:
form_validate = true;
form_validate = validateSection1() && form_validate;
form_validate = validateSection2() && form_validate;
form_validate = validateSection3() && form_validate;
```

**Estimation Bugs**: EST-R403

---

### PAT-VAL-003 — Validation Mutates Form Data

**Category**: Validation | **Severity**: P1

**Description**: Validation function copies/modifies form field values as a side effect. If user edits the form and re-validates, the old copied values persist.

**Detection Rule**: Look for `.val(...)` assignments inside validate functions.

**Estimation Bugs**: EST-R604

---

### PAT-SCH-001 — Missing AUTO_INCREMENT on PK

**Category**: Schema | **Severity**: P0

**Description**: ID column is `NOT NULL` but has no `AUTO_INCREMENT`, and the application never explicitly sets the value. Result: INSERT fails or always uses 0.

**Detection Rule**:

```sql
SELECT TABLE_NAME, COLUMN_NAME, EXTRA
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = '{database}'
AND COLUMN_KEY = 'PRI'
AND EXTRA NOT LIKE '%auto_increment%';
```

**Fix Template**:

```sql
ALTER TABLE `{table}` MODIFY `{column}` int NOT NULL AUTO_INCREMENT;
```

**Estimation Bugs**: EST-S01, EST-S07

---

### PAT-SCH-002 — Integer Column for Decimal Data

**Category**: Schema | **Severity**: P2

**Description**: Column is `int` or `tinyint` but stores percentage or decimal data. Values like `2.5%` are silently truncated to `2`.

**Detection Rule**:

```sql
SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = '{database}'
AND DATA_TYPE IN ('int', 'tinyint', 'smallint')
AND (COLUMN_NAME LIKE '%per%' OR COLUMN_NAME LIKE '%rate%' OR COLUMN_NAME LIKE '%disc%');
```

**Fix Template**:

```sql
ALTER TABLE `{table}` MODIFY `{column}` decimal(10,2) DEFAULT NULL;
```

**Estimation Bugs**: EST-S03

---

### PAT-INT-001 — Implicit Event Object + Async Timing in File Upload

**Category**: Integration | **Severity**: P2

**Description**: JavaScript function called from a jQuery event handler uses the implicit global `event` object (Chrome-only) instead of receiving the event as a parameter. Combined with a hardcoded `setTimeout` to handle async operations (like image compression), this creates a double failure: the function may crash in non-Chrome browsers, and even if it works, the timing is unreliable.

**Detection Rule**:
```
grep -n "event\.target\.files" admin/assets/js/*.js
grep -n "setTimeout.*function.*[0-9]\{4\}" admin/assets/js/*.js
```
Look for functions that use `event.target` without `event` being a function parameter, AND `setTimeout` with hardcoded delays inside async processing flows.

**Fix Template**:
```javascript
// BEFORE — broken:
$('#file_input').on('change', function() { processFiles(); });
function processFiles() {
    var files = event.target.files; // ← implicit global event (Chrome-only!)
    // ... async compression ...
    setTimeout(function() { /* render previews */ }, 3000); // ← fragile timing
}

// AFTER — fixed:
$('#file_input').on('change', function(e) { processFiles(e); });
function processFiles(e) {
    var files = $('#file_input')[0].files; // ← direct DOM access (all browsers)
    var promises = [];
    // ... collect compression promises ...
    Promise.all(promises).then(function() { /* render previews */ }); // ← reliable
}
```

**Modules Found In**: Purchase
### PAT-QRY-004 — Missing IFNULL on LEFT JOIN Column

**Category**: Query Logic | **Severity**: P2

**Description**: When a SELECT uses LEFT JOIN, non-aggregated columns from the joined table can be NULL if no match exists. If the column is sent via JSON to JavaScript without an IFNULL wrapper, it becomes `null` in JS which displays as "null" or "undefined" depending on how it's accessed.

**Detection Rule**:
```
Look for LEFT JOIN queries where selected columns from the joined table are NOT wrapped in IFNULL().
Focus on columns used for display (names, descriptions, codes).
```

**Fix Template**:
```php
// BEFORE (generates NULL when no product match):
p.product_name,

// AFTER (returns empty string on no match):
IFNULL(p.product_name,'') as product_name,
```

**Also add JS-side defensive fallback**:
```javascript
// BEFORE:
items.product_name

// AFTER:
(items.product_name || '')
```

**Purchase Bugs**: PUR-UI01

---

### PAT-INT-001 — Unchecked file_get_contents Corrupting JSON

**Category**: Integration | **Severity**: P1

**Description**: PHP's `file_get_contents()` on a non-existent file generates a warning. When this happens inside a method whose result is `json_encode()`d, the warning output is prepended to the JSON string, making it unparseable. JavaScript then gets `undefined` for all expected properties.

**Detection Rule**:
```
grep -n 'file_get_contents' {MODEL_FILE} {CONTROLLER_FILE}
```
Then check: is there a `file_exists()` check before the call?

**Fix Template**:
```php
// BEFORE (generates PHP warning on missing file):
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

// AFTER (safe — returns empty on missing file):
if(file_exists($path)) {
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
} else {
    $base64 = '';
}
```

**Purchase Bugs**: PUR-UI01
### PAT-CON-001 — TOCTOU Sequence Generation

**Category**: Concurrency | **Severity**: P1

**Description**: Using a `SELECT MAX()` or `SELECT ... ORDER BY DESC LIMIT 1` to find the last reference number, incrementing it in PHP, and then inserting a new row. This creates a Time-of-Check to Time-of-Use race condition where concurrent requests produce duplicate reference numbers unless the row reading is protected by a pessimistic lock (`FOR UPDATE`) inside a transaction context.

**Detection Rule**:
```
grep -rn 'MAX(\|ORDER BY.*DESC' {MODEL_FILE}
```
Then trace if the retrieved number is incremented in PHP and saved back to the database without `FOR UPDATE` or `trans_begin()`.

**Fix Template**:
```php
// BEFORE (Vulnerable to Race Condition):
$query = $this->db->query("SELECT MAX(ref_no) as max_ref FROM table");
$next_ref = $query->row()->max_ref + 1;
$this->db->insert('table', ['ref_no' => $next_ref]);

// AFTER (Protected):
$this->db->trans_begin();
// Lock the rows to prevent concurrent reads of the max value
$query = $this->db->query("SELECT MAX(ref_no) as max_ref FROM table FOR UPDATE");
$next_ref = ($query->num_rows() > 0 && $query->row()->max_ref) ? $query->row()->max_ref + 1 : 1;
$this->db->insert('table', ['ref_no' => $next_ref]);

if ($this->db->trans_status() === FALSE) {
    $this->db->trans_rollback();
} else {
    $this->db->trans_commit();
}
```

**Purchase Bugs**: PUR-INT01

---

### PAT-INT-001 — Missing Balance Nature (CR/DR) Indicator

**Category**: Integration/UI | **Severity**: P2

**Description**: Financial balance values displayed on forms as raw numbers without indicating whether the amount is a Credit (negative) or Debit (positive). Users cannot determine the nature of the balance, leading to incorrect data entry decisions. Additionally, the balance nature is not persisted to the database, preventing downstream reporting.

**Detection Rule**:
```
Look for balance/amount display fields in forms that show numeric values from AJAX responses without any CR/DR or positive/negative indicator. Check if the form captures the balance nature in hidden inputs for DB storage.
### PAT-CALC-001 — Cascading Tax in Client-Side Calculation

**Category**: Logic | **Severity**: P0

**Description**: In client-side calculation functions, "other charges" (e.g., Hallmark charges) include their own tax (e.g., ₹300 + 18% = ₹354). This combined amount is then added to the base `item_cost` before the product's GST is calculated. Result: GST is applied to a value that already includes another tax — cascading tax (tax on tax). This creates a mismatch between GRN calculations (which handle charges separately) and bill entry calculations.

**Detection Rule**:
```
In JS calculation functions, look for:
1. A variable that accumulates charge + charge_tax (e.g., item_charges_amount = charge + tax)
2. That variable being added to item_cost BEFORE the product GST calculation
3. Then GST being calculated on the combined amount
```

**Fix Template**:
```javascript
// BEFORE (no indicator):
$('#balance_field').val(data.balance_amount);

// AFTER (with CR/DR indicator + hidden input):
if (data.balance_amount < 0) {
    $('#balance_type_hidden').val(1); // 1 = CR
    $('.balance_badge').html('(Cr)').addClass('badge-cr');
} else {
    $('#balance_type_hidden').val(2); // 2 = DR
    $('.balance_badge').html('(Dr)').addClass('badge-dr');
}
$('#balance_field').val(Math.abs(data.balance_amount));
```

**Purchase Bugs**: PUR-INT03
// BEFORE (cascading tax):
item_cost = parseFloat((purewt * rate) + mc + metal + other_charges_amount + stone).toFixed(2);
tax = calculate_base_value_tax(item_cost, tax_group); // GST on inflated base
item_cost = parseFloat(parseFloat(item_cost) + parseFloat(tax)).toFixed(2);

// AFTER (correct — charges added AFTER tax):
item_cost = parseFloat((purewt * rate) + mc + metal + stone).toFixed(2); // NO charges
tax = calculate_base_value_tax(item_cost, tax_group); // GST on base only
item_cost = parseFloat(parseFloat(item_cost) + parseFloat(tax)).toFixed(2);
item_cost = parseFloat(parseFloat(item_cost) + parseFloat(other_charges_amount)).toFixed(2); // charges AFTER
```

**Key Invariant**: Product GST must NEVER include other charges' tax in its base. Other charges are separate taxable items with their own tax rate.

**Purchase Bugs**: PUR-CLT02

---

### PAT-LOGIC-004 — Zero Value Loss During Fallback Checks

**Category**: Logic | **Severity**: P1

**Description**: Using PHP's `empty()` or similar soft-truthiness checks to determine if an input should fall back to a database default. Because `empty("0")` and `empty(0)` both evaluate to `true` in PHP, explicit zero values entered by users (e.g. 0% wastage, 0 weight) are discarded and overwritten by schema defaults.

**Detection Rule**:
```
grep -n 'empty(' {MODEL_FILE} {CONTROLLER_FILE}
```
Look for `empty()` checks being used on optional numeric or percentage fields where `0` is a valid business value.

**Fix Template**:
```php
// BEFORE (Vulnerable):
$data['wastage_percent'] = $input['wastage']; // "0" passes to model
// Inside model:
if (empty($value)) { 
    $data[$field] = $default_values[$field]; // "0" is treated as empty!
}

// AFTER (Safe Controller Check):
$data['wastage_percent'] = ($input['wastage'] !== '' && $input['wastage'] !== null) ? $input['wastage'] : 0;
```

**Key Principle**: Use strict type/null checks (`!== ''` and `!== null`) for numeric form inputs instead of `empty()` to guarantee that literal `0` integers and `"0"` strings are preserved.

**Billing Bugs**: BIL-CLT03

---

### PAT-QRY-005 — NULL/Empty Fallback in SQL Display Column

**Category**: Query Logic | **Severity**: P1

**Description**: SQL query uses `CONCAT(IFNULL(column, ''), ...)` to build a display value from a descriptive column (e.g., `weight_description`). When the column is NULL or empty, the CONCAT produces only whitespace — rendering the column blank in the UI. The root data exists in other columns (e.g., `from_weight`, `to_weight`) but is not used as a fallback.

**Detection Rule**:
```
grep -n 'CONCAT.*IFNULL.*description\|CONCAT.*IFNULL.*name' {MODEL_FILE}
```
Look for CONCAT expressions building display values from nullable description/name columns without an IF fallback.

**Fix Template**:
```sql
-- BEFORE (blank when description is NULL):
CONCAT(IFNULL(wt.weight_description, ''), ' ', IFNULL(m.uom_name, '')) AS weight_name

-- AFTER (falls back to range values):
IF(IFNULL(wt.weight_description, '') != '',
   CONCAT(wt.weight_description, ' ', IFNULL(m.uom_name, '')),
   CONCAT(IFNULL(wt.from_weight, ''), ' - ', IFNULL(wt.to_weight, ''))
) AS weight_name
```

---

### PAT-LOGIC-005 — Using Transaction Date for Historical Event

**Category**: Logic | **Severity**: P1

**Description**: A query retrieving historical or linked records (e.g., advances, installments, previous returns) incorrectly uses the current transaction's date (e.g., delivery date, current bill date) as the date for those linked records. This misleads users about the actual payment/event history.

**Detection Rule**:
Look for SQL JOINs where a date from the parent/main table (e.g., `ret_billing`) is aliased as the date for a linked record from a joined table (e.g., `ret_billing_advance`).

**Fix Template**:
```sql
-- BEFORE (Incorrect):
SELECT b.bill_date as item_date FROM ret_billing b JOIN linked_table l ...

-- AFTER (Correct):
SELECT l.actual_event_date as item_date FROM ret_billing b JOIN linked_table l ...
```

**Key Principle**: Historical records displayed on documents must always preserve their original event dates. Never override historical dates with the current transaction date.

**Billing Bugs**: BIL-CLT04

**Reports Bugs**: RPT-CLT01
**First Found**: 2026-03-03
### PAT-UI-001 — Async Balance Overwrite in Bill-wise Selection

**Category**: UI/Logic | **Severity**: P1

**Description**: In forms where users select individual items (bills, orders, etc.) via checkboxes, a `calculateSelectedTotal()` function correctly sums the checked items. However, the parent function that called the AJAX to load the items also sets the balance/total field to the **overall outstanding** amount synchronously — either before or after the async AJAX call. Due to JS async timing, the synchronous set runs AFTER the AJAX success callback's correct calculation, overwriting the selected total with the overall total.

**Detection Rule**:
```
In JS files, look for:
1. A function that calls AJAX to load selectable items (bills, orders)
2. Code OUTSIDE the AJAX success callback that sets a balance/total field
3. Code INSIDE the AJAX success callback that also sets the same field via a calculation function
4. The outer code runs AFTER the inner function returns (async timing)
```

**Fix Template**:
```javascript
// BEFORE (balance overwritten by total outstanding):
function loadItems(data) {
    $.ajax({ success: function() {
        calculateSelectedTotal(); // ← sets correct value
    }});
    $("#balance").val(data.total_outstanding); // ← overwrites after async!
}

// AFTER (let calculateSelectedTotal be sole authority):
function loadItems(data) {
    $.ajax({ success: function() {
        calculateSelectedTotal(); // ← sole authority for balance
    }});
    // Removed: synchronous total outstanding set
}

// Also guard parent function:
if (ctrl_page[1] != 'target_page') {
    // original total-outstanding logic for other pages
} else {
    // let calculateSelectedTotal() handle it
}
```

**Modules Found In**: Purchase

**Purchase Bugs**: PUR-CLT03

---

### PAT-QRY-006 — UNION Scope Leak — Data in Wrong Row Type

**Category**: Query Logic | **Severity**: P2

**Description**: In a UNION query that combines different row types (e.g., payments + returns), a LEFT JOIN in one SELECT populates columns that should only have data in the other SELECT. Result: data "leaks" into rows where it doesn't belong — e.g., rejected weight appearing on payment rows when it should only be on return rows.

**Detection Rule**:
```
In UNION queries, check if any LEFT JOIN in SELECT A populates columns
that are also populated by SELECT B (the other UNION part).
If those columns are semantically scoped to only one row type,
the JOIN is a scope leak.
```

**Fix Template**:
```sql
-- BEFORE (scope leak — rejected weight appears on payment rows):
SELECT ..., IFNULL(ret.rejected_gwt, 0) as rejected_gwt
FROM payments
LEFT JOIN (SELECT SUM(gwt) as rejected_gwt FROM returns ...) as ret ON ...
UNION
SELECT ..., IFNULL(SUM(pri.gwt),0) as rejected_gwt
FROM returns ...

-- AFTER (scoped — payment rows get 0, only return rows show data):
SELECT ..., 0 as rejected_gwt
FROM payments
-- LEFT JOIN removed
UNION
SELECT ..., IFNULL(SUM(pri.gwt),0) as rejected_gwt
FROM returns ...
```

**Key Principle**: In UNION queries, each SELECT should only populate columns relevant to its row type. Use hardcoded `0` or `''` for columns that belong to the other row type.

**Modules Found In**: Reports
**Reports Bugs**: RPT-CLT02
**First Found**: 2026-03-06

---

## Adding New Patterns

### PAT-RAW-001 — Raw SQL with String Concatenation

**Category**: Query Safety | **Severity**: P1

**Description**: Uses `$this->db->query()` with string concatenation instead of query builder or parameterized bindings. User-supplied values concatenated into SQL strings are vulnerable to SQL injection.

**Detection Rule**:

```
grep -n 'db->query("' {MODEL_FILE}
```

Then check if any concatenated variable originates from user input.

**Fix Template**:

```php
// BEFORE (vulnerable):
$sql = $this->db->query("SELECT * FROM ret_taging WHERE tag_id=" . $tag_id);

// AFTER (safe):
$this->db->where('tag_id', $tag_id);
$sql = $this->db->get('ret_taging');

// OR with query bindings:
$sql = $this->db->query("SELECT * FROM ret_taging WHERE tag_id = ?", array($tag_id));
```

**Billing Bugs**: 241+ instances in `ret_billing_model.php` (L149, L160, L171, L192, L293, L5024, etc.)
**Scheme Hits**: 40+ raw `db->query($sql)` calls in `scheme_model.php` — several use interpolated `$id` directly (e.g. L184: `WHERE s.id_scheme =$id`, L196, L216, L539, L576, L581, L597, L661, L667, L673, L718, L729, L741, L749, L761, L784, L785, L795). All `$id` values arrive via controller routing — risk is low but pattern is confirmed.
**Settings Hits**: `admin_settings_model.php` — L1158: `setting_data()` concatenates `$settings` param directly into SQL (`Where settings='.$settings.'`); L1183: `settingsDB()` concatenates `$id` into WHERE clause; L2546: raw UPDATE for `gifts` table with `$status` and `$id`; L2724: raw UPDATE for `notification` table with `$status` and `$id`. Controller `admin_settings.php` L822: raw UPDATE on `promotion_api_settings` with arithmetic expression.
**First Found**: 2026-02-24 (Billing Module Pattern Scan)

---

### PAT-TXN-003 — trans_commit Without trans_status Check

**Category**: Transaction | **Severity**: P1

**Description**: After `trans_begin()`, the code calls `trans_commit()` directly without first checking `trans_status()`. If any query failed silently, the partial/corrupt data is committed permanently.

**Detection Rule**:
Count `trans_begin()` calls vs `trans_status()` calls in the same file. If `trans_begin` count > `trans_status` count, some transaction blocks lack safety checks.

**Fix Template**:

```php
// BEFORE (unsafe):
$this->db->trans_begin();
// ... operations ...
$this->db->trans_commit(); // Always commits, even if queries failed

// AFTER (safe):
$this->db->trans_begin();
// ... operations ...
if ($this->db->trans_status() === FALSE) {
    $this->db->trans_rollback();
    // handle error
} else {
    $this->db->trans_commit();
}
```

> [!NOTE]
> This differs from PAT-TXN-001 (which is about committing in the _error branch_). PAT-TXN-003 is about _missing the check entirely_ — no status check at all before commit.

**Billing Bugs**: 14+ transaction blocks in controller (37 `trans_commit` vs 23 `trans_status` checks)
**First Found**: 2026-02-24 (Billing Module Pattern Scan)

---

### PAT-TXN-004 — Orphaned trans_begin in Read-Only Function

**Category**: Transaction | **Severity**: P0

**Description**: A function that performs **zero DB writes** (only reads from DB or session) has a `trans_begin()` call. The transaction is opened but never committed or rolled back — the handle dangles until the MySQL connection closes. This is a copy-paste artifact from sibling functions that do have DB writes. It wastes a connection resource on every invocation and can interfere with MySQL `autocommit` state.

**Detection Rule**:
```powershell
# Find trans_begin() calls in functions lacking any insert/update/delete nearby:
grep -n "trans_begin" admin/application/controllers/admin_ret_brntransfer.php
# For each match, check if insertData/updateData/deleteData/trans_commit/trans_rollback exists in the same function
```

**Distinguishing from PAT-TXN-003**: PAT-TXN-003 has a valid DB write but is missing `trans_status()` check. PAT-TXN-004 has no DB writes at all — the entire transaction block is dead code.

**Fix Template**:
```php
// BEFORE (BRN-101 pattern — no DB writes, transaction is pointless):
function verify_otp() {
    $this->db->trans_begin(); // ← orphaned, serves no purpose
    $session_otp = $this->session->userdata('bt_approval_otp');
    $post_otp = $this->input->post('otp');
    // ... only session comparisons, no DB writes ...
    echo json_encode($status); // trans handle left dangling
}

// AFTER (correct — just remove the trans_begin):
function verify_otp() {
    // No transaction needed — function only reads session data
    $session_otp = $this->session->userdata('bt_approval_otp');
    $post_otp = $this->input->post('otp');
    // ... only session comparisons, no DB writes ...
    echo json_encode($status);
}
```

**Prevention**: Before adding `trans_begin()` to any function, verify at least one DB write (`insertData`, `updateData`, `deleteData`, or raw `$this->db->insert/update/delete`) exists in the function. Read-only functions (DB selects, session reads, config reads) must never open a transaction.

**First Found**: 2026-03-11 (Branch Transfer Module — BRN-101)
**Modules Found In**: Branch Transfer

---

## PAT-LOGIC-001: Blind Ratio Distribution to Ineligible Items

**Category**: Logic
**Default Severity**: P1

**Description**: A financial value (discount, tax, surcharge) is distributed across all line items using a simple ratio (`item_share = total × item_amount / grand_total`) without checking whether each item is actually eligible to receive that value. Items with zero capacity (e.g., MC=0, VA=0) receive an unearned share, and the excess overflows into a catch-all field (e.g., `item_blc_discount`) instead of being redistributed to eligible items.

**Detection Rule**:

```bash
# Look for ratio-based distribution without eligibility checks
grep -n "disc_per\|disc_amt.*total_sales_amt\|ratio.*discount" admin/assets/js/*.js
# Check for unconditional application to all rows
grep -n "each.*row.*disc\|forEach.*item.*discount" admin/assets/js/*.js
```

**Fix Template**:

```javascript
// BEFORE (anti-pattern): blind ratio to all items
var disc_per = (disc_amt / total_sales_amt) * 100;
// Applied to every row regardless of eligibility

// AFTER: pre-allocate with eligibility check
var allocations = calculateAllocations(); // checks item.mc > 0 || item.va > 0
// Each row reads its pre-allocated share; ineligible items get 0
```

**Modules Found In**: Billing (BIL-CLT01 — type 2 bill discount)
**First Found**: 2026-02-25

---

## PAT-LOGIC-002: Return-Omission in Debt Calculation

**Category**: Logic | **Severity**: P0

**Description**: Calculating an outstanding balance by only subtracting payments from the total, ignoring sales returns. Variant A: Ignoring returns entirely. Variant B: Using an unpopulated column. Variant C: Subtracting cash refunds (money already returned to customer) which does NOT reduce the debt. Only advance-kept returns (`make_as_advance=1`) should be subtracted.

**Detection Rule**:
Search for calculations of "due_amount", "balance", or "outstanding" and verify if they subtract both payment tables AND return tables.
```bash
grep -n "due_amount\|balance.*=\|blc_amt" {MODEL_FILE} {JS_FILE}
```

**Fix Template**:
```php
// BEFORE (anti-pattern — uses unpopulated column or omits returns entirely):
$due = $total - $paid;
// OR: SUM(credit_ret_amt) — column always 0

// AFTER (correct — join through ret_bill_return_details):
$returned_sql = $this->db->query("SELECT IFNULL(SUM(d.item_cost),0) as total_returned
    FROM ret_bill_return_details r
    LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id
    LEFT JOIN ret_billing rb ON rb.bill_id = r.bill_id
    WHERE d.bill_det_id IS NOT NULL
    AND rb.bill_status = 1
    AND rb.make_as_advance = 1  -- CRITICAL: Exclude cash refunds
    AND r.ret_bill_id = " . $bill_id);
$due = $total - $paid - $returned;
```

**Modules Found In**: Billing (BIL-INT01, BIL-INT02, BIL-INT04)
**First Found**: 2026-03-04

---

## PAT-LOGIC-003: Round-Off Omission in Balance Calculation

**Category**: Logic | **Severity**: P1

**Description**: A balance formula calculates outstanding amount as `tot_bill_amount - payments - returns` but omits `round_off_amt`. Because `tot_bill_amount` in `ret_billing` is stored *after* rounding (e.g., ₹10000.50 where round_off = 0.50), subtracting only item-level amounts leaves a residual exactly equal to `round_off_amt`. This non-zero balance prevents the credit status from flipping to Paid (1), and report-level queries similarly show a phantom outstanding balance.

**Detection Rule**:
```bash
# Find balance calculations that reference tot_bill_amount but miss round_off_amt
grep -n "tot_bill_amount.*tot_amt_received\|bal_amt.*tot_bill_amount" \
  admin/application/controllers/admin_ret_billing.php \
  admin/application/models/ret_reports_model.php
# Then check: does each formula also subtract round_off_amt?
```

**Fix Template**:
```php
// BEFORE (omits round_off_amt — leaves phantom residual):
$balance = $bill_details['tot_bill_amount']
         - $bill_details['tot_amt_received']
         - $total_collections
         - $total_returned;

// AFTER (correct — subtracts round_off_amt from the total):
$balance = $bill_details['tot_bill_amount']
         - $bill_details['tot_amt_received']
         - $total_collections
         - $total_returned
         - floatval($bill_details['round_off_amt']);  // PAT-LOGIC-003 fix
```

**Also fix the SQL report query**:
```sql
-- BEFORE:
(b.tot_bill_amount - b.tot_amt_received - IFNULL(p.paid_amt, 0)) AS bal_amt

-- AFTER:
(b.tot_bill_amount - b.tot_amt_received - IFNULL(p.paid_amt, 0)
 - IFNULL(b.round_off_amt, 0)) AS bal_amt
```

**Prerequisite**: Ensure `get_BillAmount()` (or equivalent) SELECTs `round_off_amt`:
```php
// In ret_billing_model.php get_BillAmount():
$sql = "SELECT b.tot_bill_amount, b.tot_amt_received, b.is_credit,
               b.round_off_amt             -- required for PAT-LOGIC-003 fix
        FROM ret_billing b WHERE b.bill_id = " . $bill_id;
```

**Key Invariant**: `round_off_amt` is set once at bill creation and never changes. It is part of `tot_bill_amount` but is NOT collected through payments or returns — it must always be explicitly excluded from any outstanding-balance formula.

1. **Pattern ID**: `PAT-{CATEGORY}-{SEQ}` (e.g., `PAT-SEC-005`)
2. **Category**: Security / Transaction / Query Logic / Variable / Validation / Schema / Performance / Integration
3. **Default Severity**: P0 / P1 / P2 / P3
4. **Description**: What the bug is and why it's dangerous
5. **Detection Rule**: Grep/SQL command to find it
6. **Fix Template**: Before/after code with explanation
7. **Modules Found In**: List of modules where this pattern has been found

---

### PAT-UI-001: UI Label Mismatch / Wrong Field Position

**Category**: UI/Display
**Default Severity**: P1

**Description**:
View file labels don't match the data being displayed in the corresponding fields. This can occur when:
- Labels are hardcoded with incorrect text
- Fields are positioned in wrong sections of the form
- Data is populated correctly by JS but the HTML label next to it is wrong
- Sections are duplicated or placed in wrong panels (left vs right)

This is dangerous because users make financial decisions based on displayed labels. A mislabeled "Pure Balance" showing as "Bill Pure WT" can lead to incorrect invoice entries.

**Detection Rule**:
```bash
# Look for labels/headers in view files and cross-reference with JS population logic
grep -n "<label>" views/{module}/*.php | grep -i "balance\|outstanding\|pure\|amount"
grep -n "<h4>" views/{module}/*.php | grep -i "balance\|outstanding\|pure\|amount"
```

**Fix Template**:
```html
<!-- BEFORE: Labels don't match actual data being displayed -->
<label>Pure Balance</label>
<h4>Outstanding Pure Wt(Grms)</h4>

<!-- AFTER: Labels match the data context -->
<label>Bill Pure WT</label>
<h4>Overall Pure Weight</h4>
```

**Prevention**: Always cross-reference view labels with the JS function that populates the field values. When creating new forms, use a label→data mapping document.

**Modules Found In**: Purchase (PUR-CLT03)
**First Found**: 2026-03-04
**Modules Found In**: Billing (BIL-INT03)
**First Found**: 2026-03-06

---

### PAT-JS-001 — Un-scoped Global Checkbox «Select All»

**Category**: JavaScript | **Severity**: P1

**Description**: A «Select All» checkbox uses an unscoped jQuery selector like `$("tbody tr td input[type='checkbox']")` which matches ALL checkbox inputs across ALL tables on the page, not just the intended table. Checking one «Select All» incorrectly checks rows in unrelated tables.

**Detection Rule**:
```
grep -n "select_all\|sel_all\|select-all" {JS_FILE}
```
Then check if the selector inside the handler is scoped to a specific table ID (`$("#table_id tbody ..."`). If it uses a bare class/tbody selector, it's unscoped.

**Fix Template**:
```javascript
// BEFORE (unscoped — affects ALL tables):
$('#select_all_btn').click(function() {
    $("tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
});

// AFTER (scoped — affects only its own table):
$('#select_all_btn').click(function() {
    $("#specific_table_id tbody tr td input[type='checkbox']").prop('checked', $(this).prop('checked'));
});
// Or via closest parent:
$(this).closest('table').find("tbody tr td input[type='checkbox']")
```

**Branch Transfer Bugs**: BRN-D40, BRN-D49
**First Found**: 2026-03-11

---

### PAT-JS-002 — Hardcoded System/Branch ID in Business Logic

**Category**: JavaScript | **Severity**: P0

**Description**: A numeric system ID (branch ID, company ID, user ID) is hardcoded as a literal integer in an AJAX payload or business logic condition instead of being read from a form field or data attribute. When the system grows (new branches, new companies), the hardcoded value silently misroutes data.

**Detection Rule**:
```
grep -n "transfer_to:\s*[0-9]\+\|id_branch:\s*[0-9]\+\|branch_id:\s*[0-9]" {JS_FILE}
```
Also search for `if(item.id_branch != 1)` or similar numeric comparisons on entity IDs.

**Fix Template**:
```javascript
// BEFORE (hardcoded):
data: { transfer_to: 1, ... }

// AFTER (dynamic):
data: { transfer_to: $('#to_brn').val(), ... }

// For conditions:
// BEFORE:
if(item.id_branch != 1) { ... }
// AFTER:
if(item.id_branch != current_branch_id) { ... } // where current_branch_id is set from a PHP-injected JS var
```

**Branch Transfer Bugs**: BRN-D37 (P0 — all old metal transfers misrouted to branch 1)
**First Found**: 2026-03-11

---

### PAT-JS-003 — Global Scope Leak (Undeclared Variables)

**Category**: JavaScript | **Severity**: P2

**Description**: Variables used in JS functions are not declared with `var`, `let`, or `const`. They silently become properties of the `window` object. In multi-function code this causes race conditions and data sharing between unrelated code paths. Common in large legacy jQuery files written before strict linting.

**Detection Rule**:
```javascript
// Look for assignments that lack a preceding var/let/const in the same function:
// e.g., trans_type = $("...").val(); without prior var trans_type;
// Running JSHint or ESLint with "esversion: 5" on the file will catch these.
```

**Fix Template**:
```javascript
// BEFORE (global leak):
function add_to_trans() {
    grs_wt = parseFloat(row.find('.gross_wt').val()); // global!
}

// AFTER:
function add_to_trans() {
    var grs_wt = parseFloat(row.find('.gross_wt').val()); // local
}
```

**Branch Transfer Bugs**: BRN-D44, BRN-D45, BRN-D46, BRN-D52
### PAT-VAR-001 — Copy-Paste Variable Name Mismatch

**Category**: Variable | **Severity**: P1

**Description**: Utilizing undefined variables in methods or functions, typically due to copy-pasting code from another function and neglecting to update the variable names or return parameters. This results in fatal undefined variable crashes or silent data failures.

**Detection Rule**:
```
# N/A: Static analysis required. Use php -l and visual inspection of method scope.
```

**Fix Template**:
```php
// BEFORE (example referencing undefined $data):
$result = $this->db->get('stock');
return $data->result_array();

// AFTER:
$result = $this->db->get('stock');
return ($result) ? $result->result_array() : [];
```

**Branch Transfer Bugs**: BRN-D01, BRN-D02, BRN-D09, BRN-D16, BRN-D17
**First Found**: 2026-03-12

---

### PAT-SEC-005: Information Leak (Server Diagnostics)
**Category**: Security
**Default Severity**: P1 (High - Structural Exposure)

**Description**:
Occurs when database exceptions or raw query traces (`last_query()`, `_error_message()`) are assigned directly inside dynamic controller response strings or arrays bound directly to client views, exposing internal architecture to potential exploitation organically.

**Detection Rule**:
Search for diagnostic function assignments within result structures:
`grep -E "echo.*last_query|\bresult.*last_query" admin/application/controllers/`

**Fix Template**:
**Before:**
```php
$result['err'] = $this->db->_error_message();
echo $this->db->last_query();
```
**After:**
```php
log_message('error', "DB error: " . $this->db->_error_message() . " | Query: " . $this->db->last_query());
$result['status'] = 0; // Return generic error
```

**Modules Found In**: Branch Transfer (BRN-103)
**First Found**: 2026-03-11

---

### PAT-JS-004 — Deprecated `.select2("val", ...)` API

**Category**: JavaScript | **Severity**: P2

**Description**: Using the deprecated `.select2("val", value)` method to programmatically set a Select2 dropdown's value. This API was removed in Select2 v4+. The correct method is `.val(value).trigger('change')`.

**Detection Rule**:
```
grep -n 'select2("val"\|select2("'val'"' {JS_FILE}
```

**Fix Template**:
```javascript
// BEFORE (deprecated, throws error in Select2 v4+):
$("#my_select").select2("val", selected_id);

// AFTER (correct for Select2 v4+):
$("#my_select").val(selected_id).trigger('change');
```

**Branch Transfer Bugs**: BRN-D38 area; also confirmed in `get_invnetory_item()` L7116, `get_ActiveSections()` L8073, `getNonTagReceiptedLots()` L8131
**First Found**: 2026-03-11

---

### PAT-JS-005 — Event Listener Accumulation inside Change Handler

**Category**: JavaScript | **Severity**: P1

**Description**: An event listener (e.g., `keypress`, `click`) is bound inside another event handler that fires multiple times (e.g., `change` on a radio button). Each time the outer handler fires, a new instance of the inner listener is added without removing the old one. After N interactions, pressing a key triggers N duplicate handler calls.

**Detection Rule**:
```
// Look for .on('keypress')/keyup/click calls INSIDE a .on('change') handler.
grep -n "on('change'" {JS_FILE} | head -30
// Then check if the change handler body contains another .on() call.
```

**Fix Template**:
```javascript
// BEFORE (accumulates listeners):
$("input[name='transfer_type']").on('change', function() {
    $("#my_input").on('keypress', function(e) { // ← NEW listener added every time radio changes!
        // ...
    });
});

// AFTER (unbind first, or move listener outside):
// Option A: unbind before re-binding
$("input[name='transfer_type']").on('change', function() {
    $("#my_input").off('keypress').on('keypress', function(e) { // clean
        // ...
    });
});

// Option B (better): use event delegation outside the change handler
$(document).on('keypress', '#my_input', function(e) {
    // context-sensitive logic here
});
```

**Branch Transfer Bugs**: BRN-D39 (keypress accumulation in radio change handler)
**First Found**: 2026-03-11
### PAT-VAR-002: Hardcoded Routing Values
**Category**: Variable / Hardcoding
**Default Severity**: P0 (Critical - Data Corruption)

**Description**:
Occurs when frontend code statically assigns a literal identifier (like `1`) to a data mapping property (like `transfer_to`) instead of capturing the dynamically intended value evaluated and selected by the user.

**Detection Rule**:
Search for literals where variables are expected inside AJAX data objects:
`grep -n "transfer_to' : 1" admin/assets/js/`

**Fix Template**:
**Before:**
```javascript
postData = { 'transfer_to' : 1 };
```
**After:**
```javascript
postData = { 'transfer_to' : to_brn };
```

**Modules Found In**: Branch Transfer (BRN-D37)
**First Found**: 2026-03-12
```

---

### PAT-NULL-001: Null Object Reference (Row-Access Without Guard)
**Category**: Variable / Null Safety
**Default Severity**: P0–P1 (fatal crash — OTP flow, lookup functions)

**Description**:
Occurs when a query result is accessed via `->row()->property` without first checking `->num_rows() > 0`. If the query returns no rows, `->row()` returns `null`, and PHP fatally crashes trying to access a property on that `null`. The crash propagates up and kills the entire request — even if the calling controller has a null-handling else branch that *would* handle it gracefully.

This pattern is especially dangerous in lookup functions (mobile number, head office, product ID lookups) where the query returning zero rows is a legitimate runtime condition, not just a bug trigger.

**Affected Bug Examples**: BRN-D10, BRN-D16, BRN-D17, BRN-D09

**Detection Rule**:
```bash
# Find all ->query() calls in models NOT followed by a num_rows check
grep -n "->query(" admin/application/models/ret_brntransfer_model.php
# Then check surrounding lines for missing ->num_rows() guard
```
Or a targeted pattern: any function where `->row()->` appears without `num_rows()` in the same function body.

**Fix Template**:
**Before (crash):**
```php
function getSomeValue($id)
{
    $sql = "SELECT column FROM table WHERE id=" . $id;
    return $this->db->query($sql)->row()->column;  // CRASH if no rows
}
```
**After (safe):**
```php
function getSomeValue($id)
{
    $sql = $this->db->query(
        "SELECT column FROM table WHERE id=" . $this->db->escape($id)
    );
    if ($sql && $sql->num_rows() > 0) {
        return $sql->row()->column;
    }
    return null;  // or return 0; — depends on caller's expectation
}
```

**Key considerations**:
- The safe fallback value (`null` vs `0`) depends on what the **caller** expects — check the controller first
- Apply `$this->db->escape()` to parameters when adding the guard (secondary improvement)
- The calling code's null-handling `if ($value)` check only works if the model doesn't crash first

**Modules Found In**: Branch Transfer (BRN-D10, BRN-D16, BRN-D17, BRN-D09)
**First Found**: 2026-03-12

---

### PAT-JS-001 — Client-Side Filter Contradicts Backend Parameter

**Category**: Logic / JS Rendering | **Severity**: P2

**Description**: Backend API already supports conditional data via a query parameter (e.g., `include_closed=1`), and the JS Ajax call sends it correctly. However, the JS `$.each()` rendering loop still applies a hardcoded client-side filter (e.g., `if(item.is_closed==0)`) that discards the extra data before it reaches the DOM. The backend correctly returns the data, but the UI never shows it.

**Detection Rule**:
```
# Find JS loops that filter data after an AJAX call that already has a conditional parameter
grep -n 'if(item\.\w*\s*==\s*0)' {JS_FILE}
# Then cross-reference: does the AJAX call for this data include a conditional param?
```

**Fix Template**:
```javascript
// BEFORE (filter always applied):
if(item.is_closed==0) {
    // append to dropdown
}

// AFTER (filter respects page context):
if(item.is_closed==0 || (typeof ctrl_page !== 'undefined' && ctrl_page[2] == 'target_page')) {
    // append to dropdown
}
```

**Key Principle**: If the backend is parameterized to return extra data conditionally, the client-side rendering must also be aware of that condition. Don't apply blanket client-side filters that undo what the backend was asked to provide.

**Modules Found In**: Tagging (TAG-CLT02)
**First Found**: 2026-03-24

---

### PAT-AUD-001 — Destructive Operation Without Audit Trail

**Category**: Audit/Safety | **Severity**: P1

**Description**: A service/cron endpoint permanently deletes database records (hard DELETE) without: (1) transaction wrapping, (2) per-record logging via `log_detail`, (3) structured JSON response listing affected IDs. The caller has no way to know which records were deleted, and a mid-loop failure results in partial deletes with no rollback.

**Detection Rule**:
```
grep -rn '$this->db->delete' admin/application/models/ --include="*.php" -B5 -A5
```
Then check: (1) Is there a `trans_begin()` before the delete loop? (2) Is there a `log_detail()` call per record? (3) Does the return value include the list of deleted IDs?

**Fix Template**:
```php
// BEFORE (fire-and-forget):
foreach($records as $record) {
    $this->db->delete('table', array('id' => $record['id']));
    $count++;
}
return "Has ".$count." Records Deleted";

// AFTER (auditable + transactional):
// Controller: trans_begin() before calling model
$deleted_ids = array();
foreach($records as $record) {
    $status = $this->db->delete('table', array('id' => $record['id']));
    if($status) {
        $count++;
        $deleted_ids[] = $record['id'];
    }
}
return array('status' => 1, 'deletedCount' => $count, 'deleted_ids' => $deleted_ids, 'message' => $count.' Records Deleted');
// Controller: trans_commit/rollback + log_detail per ID + json_encode response
```

**Key Principle**: Every destructive operation MUST have: (a) transaction wrapping, (b) per-record audit log, (c) structured response with affected IDs. Follow the `admin_manage->account_post` Delete case as reference implementation.

**Modules Found In**: Services (deleteNoPayments_Acc_days, deleteNoPayments_Acc)
**First Found**: 2026-04-22

