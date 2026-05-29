# Report Accuracy Checklist: GST Reports

> **Last Updated:** 2026-03-27
> **Model:** `get_gst_abstract_details_v1()`, `getGroupWiseBilling()`, `get_gstr1/2_*_details()`

---

## Reports Covered

| Report | Model Method | Purpose |
|---|---|---|
| GST Abstract | `get_gst_abstract_details_v1()` | Total GST collected by slab |
| GSTR1 B2B | `getGroupWiseBilling()` → `b2b` | Sales to registered dealers |
| GSTR1 B2C | `getGroupWiseBilling()` → `b2cs_others` | Sales to unregistered persons |
| GSTR1 HSN | `getGroupWiseBilling()` → `hsn_summary` | HSN-wise summary |
| GSTR2 Purchase | `get_gstr2_purchase_details()` | Purchase from registered dealers |
| GST Return Abstract | `get_gst_abstract_with_return_details()` | GST on returns |

## Accuracy Checks

| # | Check | Status |
|---|---|---|
| 1 | Total CGST + SGST + IGST = total GST | ⬜ |
| 2 | GST slab rates correct (3%, 5%, etc.) for jewelry | ⬜ |
| 3 | B2B + B2C totals = total sales GST | ⬜ |
| 4 | Returns GST deducted properly | ⬜ |
| 5 | Purchase GST matches supplier invoices | ⬜ |
| 6 | GSTIN validation on B2B | ⬜ |
| 7 | Cancelled bills excluded from GST | ⬜ |
| 8 | Export format matches GSTIN portal requirements | ⬜ |

## Known Risks

| Risk | Severity |
|---|---|
| Date-suffixed method copies may calculate differently | 🟡 MED |
| Making charges GST vs jewelry GST separation | 🟡 MED |
