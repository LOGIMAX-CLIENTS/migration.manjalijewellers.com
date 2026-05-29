# Flow Checklist: Repair Order → Repair Delivery (Bill Type 11)

> **Last Updated:** 2026-03-27
> **Source:** Customer Order FLOW_RISK_MATRIX (repair = order type 4)
> **Covers:** Repair order creation → completion → delivery billing (bill_type=11)

---

## Modules Involved
- **Customer Order** — repair order (type=4), status tracking
- **Billing** — repair delivery bill (bill_type=11)
- **Tagging** — tag_status for repair items
- **Account** — journal entries

## Tables Touched
`customerorder` (order_type=4), `customerorderdetails`, `ret_billing` (type=11), `ret_bill_details`, `ret_billing_payment`, `ret_taging` (tag_status=8 on repair), `ret_journal`

---

## SAVE Checklist (Repair Order)

| # | Table | Expected | Status | Gap? |
|---|---|---|---|---|
| 1 | `customerorder` | INSERT (order_type=4) | ⬜ | — |
| 2 | `customerorderdetails` | INSERT repair items | ⬜ | — |
| 3 | `ret_taging.tag_status` | → 8 (under repair) | ⚠️ | **BUG**: Only set on UPDATE, NOT on initial SAVE |

## SAVE Checklist (Repair Delivery — Type 11)

| # | Table | Expected | Status |
|---|---|---|---|
| 1 | `ret_billing` | INSERT delivery bill (type=11) | ⬜ |
| 2 | `ret_bill_details` | INSERT repaired items | ⬜ |
| 3 | `ret_billing_payment` | INSERT payment | ⬜ |
| 4 | `customerorderdetails.orderstatus` | → 5 (delivered) | ⬜ |
| 5 | `ret_taging.tag_status` | → 2 (sold) or 0 (returned to customer) | ⬜ |
| 6 | `ret_journal` | INSERT delivery journal | ⬜ |

## CANCEL Checklist

| # | Item | Expected | Gap? |
|---|---|---|---|
| 1 | Cancel repair order | Status → 6, tag_status freed | ⬜ |
| 2 | Cancel delivery bill | Standard bill cancel + order status revert | ⬜ |
| 3 | Status guard on delivery | Must be status=4 (completed) before delivery | ❌ **BUG**: No guard |

## Known Bugs Found

| Bug ID | Missing Step | Severity |
|---|---|---|
| FR-CUSORD-010 | tag_status not set on repair SAVE (only UPDATE) | 🟡 MED |
| FR-CUSORD-011 | No guard: status=4 not required before set to delivered | 🟡 MED |
| FR-CUSORD-012 | No guard: status=5 (delivered) can skip completed | 🟡 MED |
