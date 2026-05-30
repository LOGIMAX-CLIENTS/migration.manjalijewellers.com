# Recipe: Wrong Branch Selector in Stone Rate Auto-Fill (set_minmaxStone_rates)

## Metadata
- **Pattern ID**: PAT-UI-009
- **Severity**: HIGH
- **Modules Affected**: Tagging (Add), Bulk Edit
- **Auto-fixable**: Yes (simple string replacement)

## Client Scope
- **Applies to**: ALL
- **Reason**: The bug is in the shared `ret_tagging.js` file used by all clients. Any client using the stone rate min/max settings feature will be silently affected.

## Created By
- **Developer**: Amman (manual fix)
- **Client**: erp.sriammanjewellers.in (amman)
- **Date**: 2026-04-27
- **Source Bug ID**: N/A

## Symptom
When the user changes `stone_wt` (or `stone_pcs`) in the stone detail modal during tagging/bulk-edit, the `stone_rate` field is **never auto-populated** from the stone rate settings — it always stays at 0. The configured min/max stone rate slab lookup silently does nothing.

## Root Cause
In `set_minmaxStone_rates()` inside `ret_tagging.js`, the branch match condition uses the wrong jQuery selector:

```js
if ($('#tag_branch_va_mc').val() == items.id_branch && ...)
```

`#tag_branch_va_mc` does not exist on the tagging form (or holds a different / empty value). The correct selector is `#branch_select`, which is the actual branch dropdown used on both the tagging add page and bulk edit page.

Because the branch condition is **always false**, no rate slab ever matches, so `stone_rate` is never set — even when the stone type, stone ID, quality, and UOM all match perfectly.

The companion function `check_min_max_stone_rate()` (called on manual `stone_rate` change) already uses `#branch_select` correctly at line ~9775, making this an inconsistency between the two functions.

## Detection

```powershell
# Find all occurrences of the wrong selector in JS files
Select-String -Path "admin\assets\js\ret_tagging.js" -Pattern "tag_branch_va_mc" -CaseSensitive
```

```bash
grep -n "tag_branch_va_mc" admin/assets/js/ret_tagging.js
```

Expected: one match inside `set_minmaxStone_rates()`.

## Files
- `admin/assets/js/ret_tagging.js`

## Fix

### Before
```js
if ($('#tag_branch_va_mc').val() == items.id_branch && curRow.find('.stones_type').val() == items.stone_type && curRow.find('.stone_id').val() == items.stone_id && curRow.find('.quality_id').val() == items.quality_id && curRow.find('.stone_uom_id').val() == items.uom_id) {
```

### After
```js
if ($('#branch_select').val() == items.id_branch && curRow.find('.stones_type').val() == items.stone_type && curRow.find('.stone_id').val() == items.stone_id && curRow.find('.quality_id').val() == items.quality_id && curRow.find('.stone_uom_id').val() == items.uom_id) {
```

**One-liner replacement:**
```powershell
(Get-Content "admin\assets\js\ret_tagging.js") -replace "\$\('#tag_branch_va_mc'\)\.val\(\)", "`$('#branch_select').val()" | Set-Content "admin\assets\js\ret_tagging.js"
```

## Verification
1. Go to **Tagging → Add** page
2. Select a lot, product, and open the stone detail modal
3. Enter a stone type, stone ID, quality, UOM, pieces, and weight that match a configured stone rate slab
4. Verify `stone_rate` field auto-fills with the slab's `max_rate` when you tab/leave `stone_wt`
5. Manually enter a rate **outside** the min/max range → verify the toaster warning fires and rate is clamped to `max_rate`
6. Repeat in **Bulk Edit** mode

## Notes
- `check_min_max_stone_rate()` (the validation function called on manual `stone_rate` change) correctly uses `#branch_select` — only `set_minmaxStone_rates()` had the wrong selector.
- `#tag_branch_va_mc` may have been a legacy selector from an older form layout that was later renamed to `#branch_select`.
- Related: if a client customised the branch selector to a different ID, both functions must be updated consistently.
