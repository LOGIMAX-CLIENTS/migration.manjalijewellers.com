# Recipe: Tagging Pur MC Type — Reversed Option Values

## Metadata
- **Pattern ID**: PAT-UI-002
- **Severity**: MEDIUM (P2) — wrong label shown, display mismatch; value saved to DB is correct but display misleads user
- **Modules Affected**: Tagging
- **Auto-fixable**: Yes (exact string swap in one file)

## Client Scope
- **Applies to**: ALL
- **Reason**: Source is the same `form.php` view shared across all clients

## Created By
- **Developer**: Antigravity
- **Client**: (all)
- **Date**: 2026-04-02
- **Source Bug ID**: N/A

## Symptom
In the Tagging Add/Edit form, the **Pur MC Type** dropdown (`#tag_pur_id_mc_type`) always displays **"Per Piece"** when the lot was created with **"Per Gram"** MC type in Lot Inward or Supplier Bill Entry. The label is reversed — value=1 shows "Per Piece" in tagging but means "Per Gram" in both source modules.

## Root Cause
The HTML `<select>` options for `#tag_pur_id_mc_type` in `admin/application/views/tagging/form.php` have reversed labels compared to the system-wide convention:

| System-wide (Lot Inward, Purchase) | Tagging form (BEFORE fix) |
|------------------------------------|--------------------------|
| `value="1"` → Per Gram             | `value="1"` → Per Piece ← **WRONG** |
| `value="2"` → Per Piece            | `value="2"` → Per Gram  ← **WRONG** |

The JS sets `$('#tag_pur_id_mc_type').val(item.pur_mc_type)` where `item.pur_mc_type` comes directly from the `mc_type` DB column (1=Per Gram). With the reversed labels, value=1 (Per Gram) would select the "Per Piece" option — showing the wrong label.

## Detection
```powershell
# Search for the reversed options in the tagging form
Select-String -Path "admin\application\views\tagging\form.php" -Pattern "tag_pur_id_mc_type" -Context 3,5
```

Look for the pattern where value="1" maps to "Per Piece" (wrong) vs value="1" → "Per Gram" (correct).

```powershell
# Cross-check vs lot_inward convention
Select-String -Path "admin\application\views\lot\form.php" -Pattern "Per Gram|Per Piece" | Select-Object -First 5
```

## Files
- `admin/application/views/tagging/form.php` (lines ~1072-1073)

## Fix

### Before
```html
<select class="form-control" id="tag_pur_id_mc_type" tabindex="10">
    <option value="">--N/A--</option>
    <option value="1">Per Piece</option>  <!-- WRONG — value=1 is Per Gram system-wide -->
    <option value="2">Per Gram</option>   <!-- WRONG — value=2 is Per Piece system-wide -->
    <option value="3">% On Price</option>
</select>
```

### After
```html
<select class="form-control" id="tag_pur_id_mc_type" tabindex="10">
    <option value="">--N/A--</option>
    <option value="1">Per Gram</option>   <!-- CORRECT — matches lot_inward/purchase -->
    <option value="2">Per Piece</option>  <!-- CORRECT — matches lot_inward/purchase -->
    <option value="3">% On Price</option>
</select>
```

## Verification
1. Open `/admin_ret_tagging/tagging/add`
2. Select a Lot No that was created with **Per Gram** MC type in Lot Inward
3. Confirm **Pur MC Type** field now shows **"Per Gram"** (not "Per Piece")
4. Add a tag item — confirm the MC type label matches Lot Inward
5. Edit an existing tag row — confirm `#tag_pur_id_mc_type` correctly repopulates from saved value

## Notes
- The system-wide DB convention is: `mc_type 1 = Per Gram`, `mc_type 2 = Per Piece`
- This is consistent across: `lot/form.php`, `ret_purchase/pur_entry_form.php`, `tagging/bulk_edit.php`
- Only the `#tag_pur_id_mc_type` in `tagging/form.php` had the labels reversed
- The `#tag_id_mc_type` (sell MC type, same file ~line 1126) uses a different JS calculation path (mc_type==2→Per Gram in that specific calc) — do NOT apply this swap there without verifying the JS math
- No backend impact: DB value stored was already correct (1 or 2 as-is from lot_inward); only the displayed label was wrong
