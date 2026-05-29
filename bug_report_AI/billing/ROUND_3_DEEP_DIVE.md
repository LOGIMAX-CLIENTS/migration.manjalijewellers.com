# Billing Module Bug Audit — Round 3: Model & Data-Flow Deep Dive

> **Module**: Billing | **Date**: 2026-02-24 | **Auditor**: Antigravity
> **Target**: `admin/application/models/ret_billing_model.php` (11,697 lines)

---

## Summary

| Severity      | Count |
| ------------- | ----- |
| P0 (Critical) | 1     |
| P1 (High)     | 3     |
| P2 (Medium)   | 2     |
| **Total**     | **6** |

---

## Bugs Found

### BIL-R301 — RAW SQL with String Concatenation (241+ Instances) (P0)

**Location**: Throughout `ret_billing_model.php` (241+ `$this->db->query()` calls)
**Description**: The model uses `$this->db->query()` with string concatenation for the vast majority of its database operations — at least 241 instances. Many concatenate variables that originate from controller inputs:

```php
// L149 — variable directly from controller param:
$sql = $this->db->query("SELECT * from ret_taging where tag_id=" . $tag_id);

// L293 — user-controlled column name injection:
$sql = $this->db->query("SELECT " . $field . " From ret_billing where " . $field . " is not null");

// L5024 — direct string concatenation with search input:
WHERE tag.tag_code = '" . $SearchTxt . "'
```

**Impact**: SQL injection vectors (especially L293 where `$field` is used as column name). CI3 query bindings completely bypassed.
**Root Cause**: Entire model was written with raw SQL approach from the start. Not a gradual degradation — it's architectural.
**Track**: A — Security (systemic)

---

### BIL-R302 — $\_POST Direct Access in Model (19 Instances) (P1)

**Location**: L678, L680, L5024, L6309, L6311, L6412, L6414, L6494, L6498, L6612, L6616, L8272, L8675, L8677, L10141, L10143
**Description**: The model directly reads `$_POST` values instead of receiving them as parameters from the controller:

```php
// L678:
if ($_POST['dt_range'] != '') {
    $dateRange = explode('-', $_POST['dt_range']);

// L8272:
$billing_for = $_POST['billing_for'];
```

**Impact**: Violates MVC pattern (model should never access request data). Bypasses CI3 input sanitization. Makes methods untestable.
**Track**: A — Architecture

---

### BIL-R303 — SHOW COLUMNS Query Exposes Table Structure (P1)

**Location**: L19, L55
**Description**: Two methods run `SHOW COLUMNS FROM {table}` which returns full table schema:

```php
$query = $this->db->query("SHOW COLUMNS FROM `$table`");
```

If `$table` comes from user input (needs tracing), this could expose the entire DB schema.
**Track**: A — Security (needs trace verification)

---

### BIL-R304 — SELECT \* Usage in Multiple Queries (P2)

**Location**: L149, L160, L171, L192, L393, and several more
**Description**: Multiple raw SQL queries use `SELECT *` instead of specifying needed columns:

```php
$sql = $this->db->query("SELECT * from ret_taging where tag_id=" . $tag_id);
$sql = $this->db->query("SELECT * from ret_estimation_items where est_item_id=" . $est_item_id);
$sql = $this->db->query("SELECT * from branch where id_branch=" . $id_branch);
```

**Impact**: Performance (fetches unnecessary columns), fragility (column additions/renames silently change method behavior).
**Track**: A — Performance/Maintenance

---

### BIL-R305 — Missing Scope Filters in DataTable Queries (P1)

**Location**: Multiple DataTable/list methods (L563+, L695+, etc.)
**Description**: Several list/DataTable queries for billing records don't consistently apply `id_branch` or `is_cancelled` filters. The bill list query at L563+ joins many tables but doesn't always restrict by the current user's branch or exclude cancelled bills:

```php
// L563 — complex concatenated SQL for bill search
// Branch filter is applied conditionally — if missing, returns cross-branch data
```

**Impact**: Users may see bills from other branches (data leakage). Cancelled bills may appear in active lists.
**Track**: B — Business data scope

---

### BIL-R306 — Day Closing Check Uses Raw SQL Without Binding (P2)

**Location**: L659
**Description**: Day closing check query uses string concatenation:

```php
$dayClose = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=" . $data['id_branch']);
```

While `$data['id_branch']` is likely an integer from session, the pattern is unsafe and inconsistent with best practices.
**Track**: A — Code quality

---

## Aggregate Function Analysis

Checked all SUM/COUNT/AVG/MAX/MIN usage (90+ instances):

- **Most subqueries correctly include GROUP BY** — the subquery pattern is well-structured
- **Complex chit installment queries** (L1824, L4250-4260, L5323-5338) use nested aggregates with GROUP BY — correct but very complex and fragile
- **No obvious missing GROUP BY bugs found** — PAT-QRY-002 is clean for this module

---

## Round 3 Result

```
Round 3: Model Deep Dive Complete
├── Total bugs: 6
├── P0 (Critical): 1 — BIL-R301 (241+ raw SQL queries)
├── P1 (High): 3 — BIL-R302 ($_POST in model), BIL-R303 (SHOW COLUMNS), BIL-R305 (scope leaks)
├── P2 (Medium): 2 — BIL-R304 (SELECT *), BIL-R306 (day closing raw SQL)
├── Track A (System): 5
└── Track B (Business): 1
```
