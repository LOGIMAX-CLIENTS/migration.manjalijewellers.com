# Branch Transfer — Deep Analysis Round (R7): Line-by-Line Source Code Audit

> **Date**: 2026-03-11
> **Focus**: Methods that pattern scanning missed — reading model and controller code line by line
> **Files**: Model L1-900, Controller L80-480

---

## Bugs Found: 8

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-D01 | **P0** | `getProductsByFilter()` returns undefined `$data` | Model L199 | A |
| BRN-D02 | **P1** | `updateDatamulti()` returns undefined `$id_value` | Model L106 | A |
| BRN-D03 | **P0** | Massive SQL Injection Surface (10+ methods, 50+ concat points) | Model L150-777 | A |
| BRN-D04 | **P2** | `SHOW COLUMNS` on Every Insert/Update | Model L15, L51 | A |
| BRN-D05 | **P3** | Dead Function `zgetDesignByFilter()` | Model L176-182 | A |
| BRN-D06 | **P2** | Trans Code Generated Before Form Secret Check | Controller L83 vs L87 | A |
| BRN-D07 | **P2** | Loop Overwrites Total with Same POST Values | Controller L128-130 | B |
| BRN-D08 | **P2** | `get_last_trans_code()` Missing Null Guard | Model L819 | A |

---

### BRN-D01 — `getProductsByFilter()` Returns Undefined Variable [P0]
```php
// Model L192-200:
function getProductsByFilter($postData) {
    if ($postData['lot_no'] != '') {
        $result = $this->db->query("select ...");  // ← stored in $result
    } else {
        $result = $this->db->query("select ...");  // ← stored in $result
    }
    return $data->result_array();  // ← BUG! $data is undefined, should be $result
}
```
**Root Cause**: Variable name mismatch — query stored in `$result` but return uses `$data`.
**Impact**: **This method will CRASH every time it's called** with a PHP fatal error. Either this method is never used (dead code) or it's a latent landmine.
**Pattern**: PAT-VAR-001 (copy-paste mismatch)

### BRN-D02 — `updateDatamulti()` Returns Undefined `$id_value` [P1]
```php
// Model L101-107:
public function updateDatamulti($data, $arr, $table) {
    $edit_flag = 0;
    $this->db->where($arr);
    $edit_flag = $this->db->update($table, $data);
    return ($edit_flag == 1 ? $id_value : 0);  // ← BUG! $id_value is not a parameter
}
```
**Root Cause**: Copied from `updateData()` which has `$id_value` as parameter. This function doesn't.
**Impact**: Always returns `NULL` (PHP notice + null). Called at Controller L360 during stock download approval — the return value is not checked, so the approval continues silently.
**Pattern**: PAT-VAR-001

### BRN-D03 — Massive SQL Injection Surface [P0]
The model has **50+ string concatenation points** where `$data`/`$postData` values from `$_POST` are directly concatenated into raw SQL queries. Critical methods include:

| Method | Lines | Concat Points | User-Controlled Values |
|---|---|---|---|
| `getLotsByFilter()` | L150-168 | 6 | `from_branch`, `to_branch` |
| `zgetDesignByFilter()` | L178-180 | 2 | `searchTxt` (LIKE injection) |
| `getDesignByFilter()` | L186-188 | 2 | `searchTxt`, `prodId` |
| `getProductsByFilter()` | L195-197 | 2 | `SearchTxt` |
| `fetchTagsByFilter()` | L241-254 | 12 | `from_brn`, `id_section`, `lotno`, `karigar`, `design_id`, `tag_no`, `old_tag_no`, `prodId`, dates |
| `fetchEstiTagsByFilter()` | L264-296 | 4 | `from_brn`, `esti_no`, dates |
| `fetchNonTaggedItems()` | L325-327 | 3 | `from_brn`, `prodId`, `id_section` |
| `isHeadOffice()` | L356 | 1 | `branch` |
| `getApprovalListing()` | L568-777 | 20+ | All filter params |
| `getBTnontags()` | L809 | 1 | `trans_id` |
| `get_last_trans_code()` | L817 | 1 | `is_eda` |
| `getBTransData()` | L868 | 2 | `transCode`, `print_type` |

**Note**: This supersedes BRN-S01 (which only counted 5 methods). The real count is **12+ methods with 50+ injection points**.
**Severity upgraded to P0** — `searchTxt` fields are directly from user text input and contain LIKE wildcards.

### BRN-D04 — `SHOW COLUMNS` on Every Insert/Update [P2]
```php
// Model L15 (insertData) and L51 (updateData):
$query = $this->db->query("SHOW COLUMNS FROM `$table`");
```
Called on **every** insert and update operation. `SHOW COLUMNS` is not cached and hits the information_schema on each call. For a save with 20 tag items, this runs 20+ times.
**Impact**: Performance degradation under load. Also, `$table` is not sanitized — SQL injection if table name is user-controlled (low risk since table names come from controller constants).

### BRN-D05 — Dead Function `zgetDesignByFilter()` [P3]
```php
// Model L176-182:
function zgetDesignByFilter($postData) { ... }
```
Prefixed with `z` indicating it was replaced by `getDesignByFilter()` at L184. Dead code that also has SQL injection in it.

### BRN-D06 — Trans Code Generated Before Form Secret Check [P2]
```php
// Controller L83:
$trans_code = $this->$model->trans_code_generator($is_eda);
// Controller L87-91:
if ($this->session->userdata('FORM_SECRET')) {
    if (strcasecmp($form_secret, ...) === 0) { $allow_submit = TRUE; }
}
```
**Root Cause**: Trans code sequence is incremented before verifying form_secret. If a user double-clicks submit, the first submission generates code 00001, the second generates 00002 but fails the secret check — sequence 00002 is wasted.
**Impact**: Gaps in trans code sequence.

### BRN-D07 — Loop Overwrites Total with Same POST Values [P2]
```php
// Controller L128-130 (inside foreach loop):
$pieces = (isset($_POST['nt_pieces']) ? $_POST['nt_pieces'] : 0);
$grs_wt = (isset($_POST['nt_grs_wt']) ? $_POST['nt_grs_wt'] : 0);
$net_wt = (isset($_POST['nt_net_wt']) ? $_POST['nt_net_wt'] : 0);
```
**Root Cause**: These values are read inside the loop iterating `$_POST['trans_data']` but they come from top-level POST fields (not per-item). They're the same on every iteration. The final `updateData()` at L134 just overwrites the master record with the same values repeatedly.
**Impact**: Redundant DB writes (perf) but functionally correct by accident since values don't change.

### BRN-D08 — `get_last_trans_code()` Missing Null Guard [P2]
```php
// Model L819:
return $this->db->query($sql)->row()->lastTrans_no;
```
**Root Cause**: If the query returns 0 rows (e.g., first-ever transfer for a given `is_eda` value), `->row()` returns NULL and `->lastTrans_no` throws a fatal error. The caller `trans_code_generator` L789 checks for NULL but the crash happens before it reaches that check.
**Impact**: First transfer after enabling EDA mode will crash.
