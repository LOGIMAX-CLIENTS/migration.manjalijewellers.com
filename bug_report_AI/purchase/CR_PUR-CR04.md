## PUR-CR04 — Add Print Button for Duplicate Cheque Copy in Supplier Payment Listing

| Field         | Value                                                        |
| ------------- | ------------------------------------------------------------ |
| Severity      | P2 (Minor — UX enhancement / missing feature)                |
| Track         | A (System)                                                   |
| Category      | Integration (UI button + existing print endpoint)            |
| Sprint        | Sprint 2                                                     |
| Pattern Match | None                                                         |
| Module Brain  | ✅ Ready                                                     |
| Reporter      | Client / Internal                                            |
| Source        | Client                                                       |
| Type          | Change Request (New Feature)                                 |

### Description

In the Supplier Payment Listing Page, a Print button is required to generate a duplicate cheque print copy. Currently, there is no option available to print the cheque again if a duplicate copy is needed.

### User Story

As a user viewing the Supplier Payment Listing, I need a Print option so that I can easily print a duplicate cheque copy for any supplier payment record without having to re-navigate to the payment entry screen.

### Acceptance Criteria

1. A **Print button** should be available in the Supplier Payment Listing Page (action column per row).
2. Users should be able to **print a duplicate cheque copy** for the selected supplier payment.
3. The printed cheque copy should follow the **same format as the original cheque print**.
4. The feature should work **without affecting existing payment records** (read-only operation).

### Technical Context

- **Controller**: `admin_ret_purchase.php` → `supplier_po_payment()` method (825 lines, handles cheque print)
- **Controller Lines**: L5334–L6158 (approximate range for supplier payment sub-module)
- **Existing Cheque Print**: The cheque print functionality already exists within the payment add/edit flow — this CR adds a listing-level trigger to reuse the same print logic.
- **Module Brain**: `knowledge_brain/purchase/` — 17 documents available

### Implementation Notes

- This is a **read-only** operation — no data mutation required
- Reuse existing cheque print view/logic (no new print template needed)
- Add a Print action button to the DataTable listing rows
- Wire the button to invoke the existing cheque print endpoint with the payment ID

### Evidence

N/A — Feature request (no error/screenshot)

### Environment

Production / All browsers
