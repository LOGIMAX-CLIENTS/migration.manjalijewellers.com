# Recipe: #tag_pcs Reverts to 1 After First Tag Save

## Metadata
- **Recipe ID**: RCP-TAG-001
- **Module**: ret_tagging
- **Symptom Category**: Stale variable clobbers fresh AJAX result
- **Severity**: P2 — Wrong value displayed after save, affects every subsequent tag
- **Created**: 2026-05-21
- **Author**: Antigravity

---

## Symptom

After successfully saving the **first tag** in the tagging screen (`/admin_ret_tagging/tagging/add`), the `#tag_pcs` field resets to `1` instead of showing the product master value (e.g. 2).

**First tag**: `#tag_pcs` shows correct value (e.g. 2) ✅  
**After save**: `#tag_pcs` shows `1` ❌

---

## Root Cause

In `createTag()`, the variable `lt_tag_pcs` is computed **before** the AJAX save call:

```js
// Line ~29297 — computed BEFORE save, stale by the time it's used
var lt_tag_pcs = ((parseFloat($("#tag_blc_pcs").val())) > 0 ? 1 : '');
```

This hardcodes `1` whenever any lot balance exists (it never reads the product master `pieces` value).

After the save succeeds, the success callback:
1. Calls `get_lot_inwards_detail()` (sync: `async:false`) — which **correctly** sets `$('#tag_pcs').val(item.pieces)` from the product master
2. Then immediately overwrites it: `$('#tag_pcs').val(lt_tag_pcs)` — forcing it back to `1`

The stale variable wins because it runs last.

---

## Detection

Search for this pattern in `createTag()` success callback:

```
$('#tag_pcs').val(lt_tag_pcs);
```

If this line exists **after** a call to `get_lot_inwards_detail()` in the same success callback, the bug is present.

---

## Fix

**File**: `admin/assets/js/ret_tagging.js`  
**Function**: `createTag()` → AJAX success callback

### Before
```js
get_lot_inwards_detail($('#tag_lot_received_id').val(),$('#tag_lt_prod').val(),'');
// ...
$('#tag_gwt').focus();

$('#tag_pcs').val(lt_tag_pcs);   // ← REMOVE THIS LINE
```

### After
```js
get_lot_inwards_detail($('#tag_lot_received_id').val(),$('#tag_lt_prod').val(),'');
// ...
$('#tag_gwt').focus();

// lt_tag_pcs line removed — get_lot_inwards_detail() already sets #tag_pcs
// correctly via item.pieces from the product master
```

---

## Why This Works

`get_lot_inwards_detail()` uses `async: false`, so it completes synchronously before the next line. Inside it, `$('#tag_pcs').val(item.pieces)` is set from the server response (product master value). Removing the stale overwrite preserves that correct value.

---

## Verification Steps

1. Go to `/admin_ret_tagging/tagging/add`
2. Select a lot for a product where **Pieces = 2** in the product master
3. **First tag**: Confirm `#tag_pcs` shows `2`
4. Save the tag
5. **After save**: Confirm `#tag_pcs` still shows `2` (not `1`)
6. Create a second tag and confirm `#tag_pcs` remains `2`

---

## Rollback

Re-add the removed line inside the `createTag()` success callback, after `$('#tag_gwt').focus();`:

```js
$('#tag_pcs').val(lt_tag_pcs);
```
