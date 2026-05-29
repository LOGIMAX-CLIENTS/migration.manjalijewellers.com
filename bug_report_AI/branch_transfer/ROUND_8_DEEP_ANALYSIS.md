# Branch Transfer — Deep Analysis Round 8: Model L900+ & Controller Approval Flow

> **Date**: 2026-03-11
> **Focus**: Model methods L900-1700, Controller approval flow L480-700
> **Continuation of**: R7 deep analysis

---

## Bugs Found: 7

| Bug ID | Severity | Title | Lines | Track |
|---|---|---|---|---|
| BRN-D09 | **P0** | Undefined `$FromDt` in `get_ajaxBranchTransferlist()` | Model L1134 | A |
| BRN-D10 | **P1** | `get_verifMobNo()` no null guard — crash | Model L1037 | A |
| BRN-D11 | **P1** | `net_wt` shows `gross_wt` in sales return summary | Model L1531 | B |
| BRN-D12 | **P2** | `SELECT *` in `get_profile_settings()` | Model L1100 | A |
| BRN-D13 | **P2** | `getSettigsByName()` function name misspelling | Model L1092 | A |
| BRN-D14 | **P2** | 300+ lines of dead commented code | Model L1148-1451 | A |
| BRN-D15 | **P2** | `updateNTData()` raw SQL concat for stock arithmetic | Model L1058 | A |

---

### BRN-D09 — Undefined `$FromDt` in Branch Transfer List [P0]
```php
// Model L1134:
if ($FromDt != $cur_entry_date) {  // ← $FromDt is UNDEFINED
    $return_data = [];
}
```
**Root Cause**: The function receives `$from_date` and `$to_date` as parameters, but L1134 references `$FromDt` (capital F, capital D, capital t). This is a different variable name — PHP won't throw an error, it will silently evaluate `NULL != $cur_entry_date` which is always `TRUE`, clearing the entire listing.
**Impact**: When `allow_bill_type == 2` and `login_branch != 0`, the DataTable listing will **always return empty** because `$FromDt` is undefined (NULL). The condition `$FromDt != $cur_entry_date` evaluates to true (NULL != 'date').
**Fix**: Change `$FromDt` to `$from_date`

### BRN-D10 — `get_verifMobNo()` Missing Null Guard [P1]
```php
// Model L1036-1038:
function get_verifMobNo($branch) {
    $sql = "SELECT otp_verif_mobileno FROM `branch` WHERE id_branch=" . $branch;
    return $this->db->query($sql)->row()->otp_verif_mobileno;  // ← crash if branch has no OTP number
}
```
**Root Cause**: Same pattern as BRN-D08 — `->row()` returns NULL if 0 rows. Dereferencing `->otp_verif_mobileno` on NULL crashes.
**Impact**: If a branch doesn't have OTP configured, the OTP send flow crashes.

### BRN-D11 — Sales Return Net Weight Shows Gross Weight [P1] ⚠️ BUSINESS BUG
```php
// Model L1531:
'net_wt' => number_format($gross_wt, 3, '.', ''),  // ← BUG! Should be $net_wt
```
**Root Cause**: Copy-paste error. L1530 correctly shows `rate` using `$tot_item_cost`, but L1531 displays `gross_wt` instead of `net_wt` for the net weight field. The `$net_wt` variable is correctly calculated at L1513 but never used.
**Impact**: Sales return items in the purchase items (PS/SR) tab show **gross weight where net weight should be**. This means the printed branch transfer challan and the approval screen both display wrong net weight values.
**Business Rule Violated**: Weight accuracy in transfer documentation.

### BRN-D12 — `SELECT *` in `get_profile_settings()` [P2]
```php
// Model L1100:
$sql = $this->db->query("SELECT * FROM `profile` WHERE id_profile=" . $id_profile);
```
Violates "Never use SELECT *" rule. Profile table may contain sensitive columns.

### BRN-D13 — Function Name Misspelling [P2]
```php
// Model L1092:
function getSettigsByName($name)  // ← "Settigs" should be "Settings"
```
Not a bug per se (the typo is consistent), but reduces code discoverability and intellisense.

### BRN-D14 — 300+ Lines of Dead Commented Code [P2]
Model L1148-1451 contains the old `get_purchase_items()` function, fully commented out. 303 lines of dead code reduce readability.

### BRN-D15 — `updateNTData()` Raw SQL Arithmetic Concat [P2]
```php
// Model L1058:
$sql = "UPDATE ret_nontag_item SET no_of_piece=(no_of_piece" . $arith . " " . $data['no_of_piece'] . "),
    gross_wt=(gross_wt" . $arith . " " . $data['gross_wt'] . "),
    net_wt=(net_wt" . $arith . " " . $data['net_wt'] . "),
    updated_by=" . $data['updated_by'] . ",...";
```
**Root Cause**: Raw SQL UPDATE with concatenated arithmetic operator (`+` or `-`) and values. Called at controller L576/581/601 during stock download approval with data from `getBTnontags()` — which itself reads from DB, so injection risk is low but the pattern is dangerous if ever extended.
