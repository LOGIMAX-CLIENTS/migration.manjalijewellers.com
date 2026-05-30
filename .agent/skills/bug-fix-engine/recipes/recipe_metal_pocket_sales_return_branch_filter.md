# Recipe: Metal Pocket — Sales Return Weight Not Branch-Filtered

## Metadata

| Field | Value |
|---|---|
| Pattern ID | PAT-DATA-007 |
| Severity | P2 — High (incorrect stock visibility, affects reconciliation) |
| Modules Affected | `old_metal_process` (Metal Pocket) |
| Auto-fixable | Yes — 4-line surgical fix in model |
| Applies to | Any client where dcnmjwl-era (older) model is deployed |

## Client Scope

| Field | Value |
|---|---|
| Applies to | Clients running older Metal Process model (pre-source-sync) |
| Fixed in source | ✅ Yes — `etail_development_src` already has the fix |
| First found in | `dcnmjwl` client |

## Created By

| Field | Value |
|---|---|
| Developer | Antigravity AI |
| Client | dcnmjwl (Dhamu Chettiar Nagai Maaligai) |
| Date | 2026-05-07 |
| Conversation | de7d3df1-f948-4a1f-9500-fa3e0716cb6d |

---

## Symptom

In the **Metal Pocket → Add** form, when the user selects a specific **From Branch** and clicks Search, the **SALES RETURN ITEMS** row shows the same gross weight regardless of which "From Branch" is selected. Each branch sees all other branches' sales returns pooled together.

**Screenshot evidence**: Switching From Branch from ANTHIYUR → SATHYAMANGALAM showed 2428g vs 1414g — demonstrating the outer grouping query was filtering, but the detail query was not branch-scoped.

---

## Root Cause

The older version of `get_metal_stock_list()` in `ret_metal_process_model.php` has a **4-parameter** `get_sales_ret_details()` function that is missing the `$from_branch` parameter entirely.

Three places are broken:

1. **Outer `$sales_ret` grouping query** — no `b.id_branch` (from_branch) filter in WHERE
2. **Caller** — passes only 4 args, omits `$data['from_branch']`
3. **Function definition** — only 4 params, no `$from_branch` variable, no WHERE clause for it

The `$id_branch` filter on `tag.current_branch` was correctly present — it filters by where the tag *currently sits*. But `from_branch` filters the *originating bill's branch* (`b.id_branch`), and that was entirely absent.

---

## Detection

Run this grep to check if the client has the old (broken) 4-param version:

```powershell
Select-String -Path ".\admin\application\models\ret_metal_process_model.php" `
  -Pattern "function get_sales_ret_details"
```

**Broken** (old version):
```
function get_sales_ret_details($from_date,$to_date,$id_branch,$id_metal)
```

**Fixed** (source version):
```
function get_sales_ret_details($from_date,$to_date,$id_branch,$id_metal,$from_branch)
```

Also check the caller:
```powershell
Select-String -Path ".\admin\application\models\ret_metal_process_model.php" `
  -Pattern "get_sales_ret_details\("
```

If the call only has 4 arguments → broken.

---

## Files

| File | Change |
|---|---|
| `admin/application/models/ret_metal_process_model.php` | 4 targeted line edits |

---

## Fix

### Change 1 — Outer grouping query: add `from_branch` filter

**Before:**
```php
".($data['id_branch']!='' ? " and tag.current_branch=".$data['id_branch']."" :'')."

GROUP by mt.id_metal");
```

**After:**
```php
".($data['id_branch']!='' ? " and tag.current_branch=".$data['id_branch']."" :'')."

".($data['from_branch']!='' ? " and b.id_branch=".$data['from_branch']."" :'')."

GROUP by mt.id_metal");
```

---

### Change 2 — Caller: pass `from_branch` as 5th argument

**Before:**
```php
$SalesRetDetails=$this->get_sales_ret_details($data['from_date'],$data['to_date'],$data['id_branch'],$val['id_metal']);
```

**After:**
```php
$SalesRetDetails=$this->get_sales_ret_details($data['from_date'],$data['to_date'],$data['id_branch'],$val['id_metal'],$data['from_branch']);
```

---

### Change 3 — Function signature: add `$from_branch` parameter

**Before:**
```php
function get_sales_ret_details($from_date,$to_date,$id_branch,$id_metal)
```

**After:**
```php
function get_sales_ret_details($from_date,$to_date,$id_branch,$id_metal,$from_branch='')
```

---

### Change 4 — WHERE clause: apply `from_branch` filter

**Before:**
```php
".($id_branch!='' ?  " and t.current_branch=".$id_branch."" :'')."

		and (date(b.bill_date) BETWEEN '...
```

**After:**
```php
".($id_branch!='' ?  " and t.current_branch=".$id_branch."" :'')."

        ".($from_branch!='' ?  " and b.id_branch=".$from_branch."" :'')."

		and (date(b.bill_date) BETWEEN '...
```

> **Note**: Use `$from_branch=''` default in function signature so existing callers without the 5th arg continue to work (backward-safe).

---

## Verification

1. Open **Metal Pocket → Add**
2. Set **Select Branch = HEAD OFFICE**, **From Branch = BRANCH_A**, pick a date range → Search
3. Note the **SALES RETURN ITEMS** gross weight (call it W1)
4. Change **From Branch = BRANCH_B** → Search
5. Note the new **SALES RETURN ITEMS** gross weight (call it W2)
6. ✅ **W1 ≠ W2** = fix working (each branch shows its own returns)
7. ✅ **W1 = W2** still after fix = no sales returns exist for those branches in that date range (not a bug)

---

## Notes

- The source (`etail_development_src`) already has all 4 of these fixes — this is a **client-version drift** bug
- The `id_branch` filter on `tag.current_branch` was always correct — it controls *where the tag sits now*
- The `from_branch` filter on `b.id_branch` controls *which branch's bill originated the sale* — this is what was missing
- This is a **read-only bug** — no data corruption, only incorrect display/selection in the pocket form
