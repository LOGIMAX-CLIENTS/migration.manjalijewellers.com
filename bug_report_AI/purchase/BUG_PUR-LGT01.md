# BUG_PUR-LGT01 — Lot Generate Stone Popup Missing Fields & UI Mismatch

| Field         | Value                                                   |
| ------------- | ------------------------------------------------------- |
| Severity      | P2 (UX/Display — no financial data affected)            |
| Track         | A (System)                                              |
| Category      | Integration / UI Synchronization                        |
| Sprint        | Sprint 2                                                |
| Pattern Match | None (Novel)                                            |
| Module Brain  | ✅ Ready (`knowledge_brain/Lot/MODULE_BRAIN.md`)         |
| Reporter      | Developer (Internal)                                    |
| Source        | Internal                                                |
| GitHub Issue  | TBD                                                     |

---

## Summary

The Lot Generate stone details popup (`lgt_stoneModal`) was missing several key columns and using a simplified/hardcoded LWT calculation, causing it to be inconsistent with the `qc_issue_receipt` / `order_form` "Add Stone" popup which is the system-wide standard.

---

## Steps to Reproduce

1. Navigate to `lot_generate/add`
2. Select a PO with stone items that have been QC-issued
3. Click the stone `+` button in the Action column
4. Observe the popup

---

## Expected Behavior

The stone popup should display the same 14 columns as `qc_issue_receipt`:
**LWT | Type | Name | Code | Pcs | Wt | Cal.Type | Cut | Color | Clarity | Shape | Rate | Amount | Action**

- **Code** = Quality code from `ret_quality_code` table
- **Cut / Color / Clarity / Shape** = Diamond grading attributes from joined quality tables
- All fields readonly (display only)
- Footer totals for Pcs, Wt, Amount
- Font size readable (14px)
- Modal width 85%, centered

---

## Actual Behavior (Before Fix)

- Popup had only 9 columns: LWT | Type | Name | Pcs | Wt | Cal.Type | Rate | Amount | Action
- **Code**, **Cut**, **Color**, **Clarity**, **Shape** columns were completely missing
- SQL query in `get_qc_stone_accepted_details()` did not JOIN `ret_quality_code`, `ret_clarity`, `ret_color`, `ret_cut`, `ret_shape`
- LWT calculation was hardcoded (`/ 5` for diamonds) instead of dynamic UOM-based
- Modal width set to 72%, not centered
- Font size too small (12px), inputs cramped (28px height)
- Cal.Type radios stacked vertically (had `<br>` between them)
- Wt input-group wrapping (UOM dropdown below weight input)

---

## Evidence

- Screenshot 1: Before — 9-column popup (missing Code, Cut, Color, Clarity, Shape)
- Screenshot 2: After — Full 14-column popup with all fields populated correctly
- Test: PO P-00002 → Diamond row shows VVS/PEAR BRILIANT/E-F/ROUND correctly

---

## Files Changed

| File | Change |
|---|---|
| `admin/application/models/ret_purchase_order_model.php` | Added LEFT JOINs to `ret_quality_code`, `ret_clarity`, `ret_color`, `ret_cut`, `ret_shape` in `get_qc_stone_accepted_details()` |
| `admin/application/views/ret_purchase/generate_lot/form.php` | Widened to 85%+centered, 14-column thead/tfoot, scoped CSS for compact layout, added `<style>` block |
| `admin/assets\js/ret_purchase_order.js` | Rebuilt `create_lgt_item()` row builder for 14 columns, fixed `total_stone_amount()`, made all fields readonly/disabled |

---

## Rollback Plan

Revert 3 files to their pre-fix state:
1. `ret_purchase_order_model.php` — Remove the 5 LEFT JOIN lines and `quality_code`, `stone_clarity`, `stone_color`, `stone_cut`, `stone_shape` from SELECT
2. `form.php` — Restore to 9-column thead, 72% width, remove `<style>` block
3. `ret_purchase_order.js` — Restore original `create_lgt_item()` and `total_stone_amount()` functions

---

## Resolution

**Status**: ✅ Fixed — 2026-04-10  
**Fixed By**: Antigravity  
**Verified By**: Developer (browser test — popup shows correct data for PO P-00002)

### Fix Summary

- SQL: Added 5 LEFT JOINs to pull quality attributes dynamically
- JS: Rebuilt row builder to render all 14 columns with readonly attributes
- View: 14-column table, 85% centered modal, 14px readable font, compact CSS layout
- Footer: 14-cell tfoot with correct column alignment
