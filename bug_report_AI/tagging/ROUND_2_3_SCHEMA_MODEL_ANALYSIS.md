# Round 2 — DB Schema Cross-Reference

> **Date**: 2026-02-24
> **Bugs Found**: 3 (code-level schema checks only; live DB analysis requires DB access)

---

## TAG-S01 — 19 SELECT \* Queries (P2)

**Category**: Performance/Schema | **Track**: A
**Description**: 19 queries use `SELECT *` instead of specifying required columns. Wastes bandwidth and risks breaking if schema changes.
**Lines**: L801, L997, L1017, L1037, L1349, L1477, L1533, L1553, L6765, L7417, L7587, L8795, L8843, L8987, L9039, L9157, L9535, L10095, L10227
**Fix**: Replace each `SELECT *` with explicit column list

---

## TAG-S02 — SHOW COLUMNS Dynamic Table (P1)

**Category**: Schema/Security | **Track**: A
**Description**: `SHOW COLUMNS FROM \`$table\`` at L37 and L73 — the `$table`parameter comes from method caller. If user-controlled, this is a table-name injection vector.
**Fix**: Validate`$table` against allowed table whitelist

---

## TAG-S03 — No is_deleted Filter Anywhere (P2)

**Category**: Schema | **Track**: B
**Description**: Zero `is_deleted` filters found in the model. If any tagged tables use soft-delete, queries may return deleted records. Need to verify if tables use `is_deleted` column.
**Impact**: Deleted tags could appear in reports and listings

---

# Round 3 — Model & Data-Flow Deep Dive

> **File**: `admin/application/models/ret_tag_model.php` (~10,230 lines)
> **Date**: 2026-02-24
> **Bugs Found**: 12

---

## TAG-R301 — 142 Raw SQL Queries (P1)

**Category**: Code Quality/Security | **Track**: A
**Description**: 142 uses of `$this->db->query(...)` instead of CI3 query builder. This violates the project rule: "Never use raw SQL unless genuinely impossible with active record." Most of these CAN be converted.
**Impact**: Raw SQL bypasses CI3's automatic escaping, increases injection risk
**Fix**: Convert to CI3 query builder (`$this->db->select()`, `->from()`, `->where()`, etc.)

---

## TAG-R302 — SQL Injection via String Concatenation (7 instances) (P0)

**Category**: Security | **Track**: A
**Description**: Direct string concatenation of variables into SQL strings without escaping.
**Instances**:
| Line | Code | Risk |
|---|---|---|
| L801 | `"SELECT * from ret_taging_stone WHERE tag_id='".$tag_id."'"` | `$tag_id` injected directly |
| L1349 | `"SELECT * FROM ret_design_master WHERE design_no ='".$design_id."'"` | `$design_id` injected |
| L4801 | `" and tag.design_id=".$data['des_select']` | `$data` from user input |
| L4823 | `" and tag.id_sub_design=".$data['sub_des_select']` | Same |
| L6737 | `" where e.login_branches=".$id_branch` | `$id_branch` not escaped |
| L7671 | `"WHERE id_tagging=".$tag_id.$where` | `$tag_id` + `$where` both raw |
| L8931 | `"UPDATE ret_nontag_item SET gross_wt=(gross_wt".$arith..."` | **UPDATE with injection** |
**Fix**: Use query bindings: `$this->db->query("SELECT * WHERE tag_id=?", array($tag_id))`

---

## TAG-R303 — Column Name Injection in Generic CRUD (5 instances) (P0)

**Category**: Security | **Track**: A
**Description**: Generic methods `getData()`, `updateData()`, `deleteData()` at L101, L185, L292, L8819 use `$id_field` as column name from caller. If caller passes user-controlled column, this is injection.
**Fix**: Add column whitelist in each method

---

## TAG-R304 — Raw $\_POST in Model (P1)

**Category**: Security | **Track**: A
**Location**: L3477

```php
$id_lot_inward_detail = $_POST['id_lot_inward_detail'];
```

**Description**: Model directly accesses `$_POST`. Models should never touch superglobals — input should come via parameters.
**Fix**: Pass value as method parameter from controller

---

## TAG-R305 — Zero Try-Catch Blocks (P1)

**Category**: Error Handling | **Track**: A
**Description**: Entire 10K+ line model has zero try-catch blocks. Any DB exception kills execution silently.
**Fix**: Wrap critical operations (INSERT/UPDATE/DELETE) in try-catch with logging

---

## TAG-R306 — 19 Loops with Potential N+1 Queries (P2)

**Category**: Performance | **Track**: A
**Description**: 19 `foreach` loops found. Some may contain DB queries inside the loop (N+1 pattern). Key suspects: L2412, L3692, L5053, L7807, L8069, L8206.
**Fix**: Audit each loop — if DB calls exist inside, refactor to batch query with `where_in()`

---

## TAG-R307 — Mixed $returnData / $return_data Naming (P1)

**Category**: Variable | **Track**: A
**Description**: 15 occurrences of `$returnData` and 5 of `$return_data` across models. Risk of assigning to one and returning the other.
**Lines**: `$returnData` at L5065, L5073, L5289, L5297, L8002, L8082, L8084, L8098, L8216, L8218, L8326, L8380, L8442, L8551 | `$return_data` at L7313, L7337, L7397, L7441, L7489, L7567
**Fix**: Standardize to one naming convention

---

## TAG-R308 — UPDATE with String Concat (P0)

**Category**: Security | **Track**: A
**Location**: L8931, L8955

```php
$sql = "UPDATE ret_nontag_item SET gross_wt=(gross_wt".$arith." ".$data['gross_wt'].")...";
```

**Description**: Raw UPDATE query built via string concatenation. `$arith` is `+` or `-`, `$data` fields come from user input. This is a blind SQL injection in an UPDATE statement.
**Fix**: Use query builder `$this->db->set()` with escaped values

---

## TAG-R309 — Aggregate Queries Missing GROUP BY (P1)

**Category**: Query Logic | **Track**: B
**Description**: 140 aggregate function calls vs 103 GROUP BY. ~37 instances may be missing GROUP BY. Key risk: subqueries with `SUM()` that return incorrect totals.
**Fix**: Audit each aggregate query for proper GROUP BY

---

## TAG-R310 — SHOW COLUMNS Table Name Injection (P1)

**Category**: Security | **Track**: A
**Location**: L37, L73

```php
$query = $this->db->query("SHOW COLUMNS FROM `$table`");
```

**Description**: `$table` is a method parameter. If any caller passes user input as table name, this allows table enumeration.
**Fix**: Validate `$table` against whitelist of known tables

---

## TAG-R311 — `ajax_getTaggingList` Raw SQL Mega-Query (P1)

**Category**: Security/Performance | **Track**: A
**Location**: L360-625 (~265 lines of raw SQL)
**Description**: The main tag listing function builds a massive raw SQL string with `$this->db->query($sql)`. This bypasses all CI3 escaping. At 265 lines, it's also unmaintainable.
**Fix**: Break into modular CI3 query builder calls

---

## TAG-R312 — SQL Injection in L8795 (P0)

**Category**: Security | **Track**: A
**Location**: L8795

```php
$sql = $this->db->query("SELECT * FROM `ret_taging` WHERE tag_id=".$tag_id);
```

**Description**: `$tag_id` concatenated directly into SQL without quotes or escaping.
**Fix**: Use query binding: `$this->db->query("SELECT * FROM ret_taging WHERE tag_id=?", array($tag_id))`

---

## Summary

| Round         | Severity            | Count                       |
| ------------- | ------------------- | --------------------------- |
| **R2 Schema** | P1: 1, P2: 2        | 3                           |
| **R3 Model**  | P0: 4, P1: 5, P2: 1 | 12 (note: TAG-R309 Track B) |
| **Total**     |                     | **15**                      |
