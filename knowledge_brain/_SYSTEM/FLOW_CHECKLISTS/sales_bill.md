# Flow Checklist: Sales Bill (Bill Type 1)

> **Last Updated:** 2026-03-27
> **Source:** Billing FLOW_RISK_MATRIX.md + ret_billing_model.php + admin_ret_billing.php
> **Covers:** Pure sales bill (bill_type=1). For exchange variants see `sales_exchange.md`.

---

## Modules Involved
- **Billing** (primary) — `admin_ret_billing.php` / `ret_billing_model.php`
- **Tagging** — tag_status update (0→2 on save, 2→0 on cancel)
- **Estimation** — estbillid link, purchase_status update
- **Account** — journal entries (debit/credit)
- **Day Closing** — validates billing date

## Tables Touched
`ret_billing`, `ret_bill_details`, `ret_billing_payment`, `ret_billing_item_stones`, `ret_taging`, `ret_estimation`, `ret_estimation_items`, `ret_journal`, `ret_billing_advance`, `ret_bill_pay_device`, `ret_bill_other_metals`, `ret_section_nontag_item_log`

---

## SAVE Checklist

| # | Table | Expected Action | Code Location | Status |
|---|---|---|---|---|
| 1 | `ret_billing` | INSERT header (bill_no, bill_date, bill_type=1, bill_status=1, bill_cus_id, totals, GST, TCS) | `billing()` controller → model `insertData('ret_billing')` | ⬜ |
| 2 | `ret_bill_details` | INSERT one row per tag/item (tag_id, weight, rate, making, amount, esti_item_id) | `billing()` → loop → `insertData('ret_bill_details')` | ⬜ |
| 3 | `ret_billing_item_stones` | INSERT stone details per tag (stone_name, stone_wt, stone_amt) | `billing()` → stone loop | ⬜ |
| 4 | `ret_billing_payment` | INSERT payment rows (cash, card, UPI, cheque, wallet, advance, etc.) | `billing()` → payment loop | ⬜ |
| 5 | `ret_bill_pay_device` | INSERT if card payment (card_no, card_type, card_date, bank) | `billing()` → if card payment | ⬜ |
| 6 | `ret_taging` | UPDATE `tag_status = 2` (sold) for each billed tag | `billing()` → `update ret_taging set tag_status=2` | ⬜ |
| 7 | `ret_estimation` | UPDATE `estbillid = {bill_id}` to link estimation to bill | `billing()` → update estimation | ⬜ |
| 8 | `ret_estimation_items` | UPDATE `purchase_status = 1` for billed items | `billing()` → update estimation items | ⬜ |
| 9 | `ret_journal` | INSERT debit + credit entries (balanced double-entry) | `billing()` → account helper / journal insert | ⬜ |
| 10 | `ret_billing_advance` | INSERT if advance/order/chit adjustment applied | `billing()` → advance logic | ⬜ |
| 11 | `ret_section_nontag_item_log` | UPDATE stock for non-tagged items (if applicable) | `billing()` → nontag section | ⬜ |

---

## CANCEL Checklist

> Cancel function: `cancel_bill()` at ~L7581-8001 in controller

| # | Table | Expected Reversal | Code Location | Status | Gap? |
|---|---|---|---|---|---|
| 1 | `ret_billing` | UPDATE `bill_status = 0` (cancelled) | `cancel_bill()` ~L7700 | ✅ | — |
| 2 | `ret_bill_details` | Soft-delete / status update | `cancel_bill()` | ✅ | — |
| 3 | `ret_billing_payment` | Reverse / mark cancelled | `cancel_bill()` | ✅ | — |
| 4 | `ret_billing_item_stones` | Status update / soft-delete | `cancel_bill()` | ⚠️ | Stone count may not be zeroed |
| 5 | `ret_bill_pay_device` | DELETE or status update for card records | `cancel_bill()` | ⬜ | **VERIFY** |
| 6 | `ret_taging` | UPDATE `tag_status = 0` (available again) | `cancel_bill()` | ✅ | — |
| 7 | `ret_estimation` | UPDATE `estbillid = NULL` (unlink) | `cancel_bill()` | ✅ | — |
| 8 | `ret_estimation_items` | UPDATE `purchase_status = 0` (unsold) | `cancel_bill()` | ✅ | — |
| 9 | `ret_journal` | INSERT reverse journal entries | `cancel_bill()` + accounts | ✅ | — |
| 10 | `ret_billing_advance` | Restore advance to customer | `cancel_bill()` | ⚠️ | Not all advance types reversed |
| 11 | `ret_bill_other_metals` | Reverse old metal purchase records | `cancel_bill()` | ⚠️ | Verify old metal reversal |
| 12 | `ret_section_nontag_item_log` | Restore non-tag stock | `cancel_bill()` | ⬜ | **VERIFY** |
| 13 | POS transaction (if POS payment) | Void POS transaction via API | `cancel_bill()` | ❌ | **BUG: POS not auto-reversed** |
| 14 | Wallet balance (if wallet payment) | Restore wallet balance | `cancel_bill()` | ⚠️ | Verify wallet debit reversal |

---

## EDIT Checklist

| # | Table | Expected Update (not duplicate) | Code Location | Status | Gap? |
|---|---|---|---|---|---|
| 1 | `ret_billing` | UPDATE header totals — NOT insert new row | `update_bill()` or `billing()` edit mode | ⬜ | **VERIFY**: Does edit create new bill or update? |
| 2 | `ret_bill_details` | UPDATE or DELETE+RE-INSERT line items | Edit flow | ⬜ | **VERIFY**: Orphan detail rows if tags change |
| 3 | `ret_billing_payment` | UPDATE payment breakdown | Edit flow | ⬜ | **VERIFY** |
| 4 | `ret_taging` | If tag changed: old tag → status=0, new tag → status=2 | Edit flow | ⬜ | **VERIFY**: Old tag freed? |
| 5 | `ret_estimation` | If estimation changed: old estbillid cleared, new one set | Edit flow | ⬜ | **VERIFY** |
| 6 | `ret_journal` | Reverse old journal + create new journal | Edit flow | ⬜ | **VERIFY** |

---

## PRINT Checklist

> Print data: `receipt_helper.php → get_receipt_data()` → `bill_format_1.php` / `bill_format_2.php`

| # | Display Field | Source (must match save) | Code Location | Status | Gap? |
|---|---|---|---|---|---|
| 1 | Bill Number | `ret_billing.bill_no` via get_bill_no_format_detail() | receipt_helper.php | ⬜ | — |
| 2 | Customer Name | `ret_billing.bill_cus_id` → customer table join | receipt_helper.php | ⬜ | — |
| 3 | Item Details (tag, weight, rate) | `ret_bill_details` joined with `ret_taging` | receipt_helper.php | ⬜ | — |
| 4 | Stone Details | `ret_billing_item_stones` | receipt_helper.php | ⬜ | — |
| 5 | Making Charges | `ret_bill_details.making_charge` | receipt_helper.php | ⬜ | — |
| 6 | GST (CGST+SGST or IGST) | Calculated from `ret_bill_details` or `ret_billing` | receipt_helper.php/template | ⬜ | **VERIFY**: same formula as save? |
| 7 | Total Amount | `ret_billing.tot_bill_amount` or recalculated | template | ⬜ | **VERIFY**: source matches save |
| 8 | Payment Breakdown | `ret_billing_payment` grouped by type | receipt_helper.php | ⬜ | — |
| 9 | Advance/Chit Adjustment | `ret_billing_advance` or scheme tables | receipt_helper.php/template | ⚠️ | **KNOWN BUG**: Chit adjustment mismatch (theniNPR) |
| 10 | Old Metal Deduction | `ret_bill_other_metals` | receipt_helper.php | ⬜ | **VERIFY** |

---

## REPORT Checklist

> Reports module: `admin_ret_reports.php` / `ret_reports_model.php`

| # | Report | Source Tables | Matches Save? | Status |
|---|---|---|---|---|
| 1 | Daily Sales Summary | `ret_billing` aggregated by date + branch | ⬜ | ⬜ |
| 2 | Bill-wise Detail Report | `ret_billing` + `ret_bill_details` | ⬜ | ⬜ |
| 3 | Payment Mode Report | `ret_billing_payment` grouped by type | ⬜ | ⬜ |
| 4 | GST Report | `ret_billing` GST fields | ⬜ | **VERIFY**: uses same GST as save? |
| 5 | Tag Sales Report | `ret_bill_details` joined with `ret_taging` | ⬜ | ⬜ |
| 6 | Customer Sales Report | `ret_billing` grouped by customer | ⬜ | ⬜ |
| 7 | Employee Sales Report | `ret_billing` grouped by created_by | ⬜ | ⬜ |

---

## Known Bugs Found

| Bug ID | Missing Step | Severity | Source |
|---|---|---|---|
| FR-BIL-005 | Cancel does NOT reverse POS transaction | 🔴 HIGH | FLOW_RISK_MATRIX |
| FR-BIL-004 | Cancel: stone count may not be zeroed | 🟡 MED | FLOW_RISK_MATRIX |
| FR-BIL-006 | Cancel: wallet balance restoration partial | 🟡 MED | FLOW_RISK_MATRIX |
| — | Cancel: `ret_billing_advance` not all types reversed | 🟡 MED | FLOW_RISK_MATRIX |
| — | Cancel: `ret_bill_pay_device` (card records) — verify cleanup | 🟡 MED | New from this audit |
| — | Cancel: `ret_section_nontag_item_log` stock restoration — verify | 🟡 MED | New from this audit |
| — | Print: Chit adjustment amount mismatch (theniNPR bill_format_2) | 🟡 MED | Live trace in research |

---

## Audit Status

- [x] Save checklist mapped (11 tables)
- [x] Cancel checklist mapped (14 items, 3 gaps found)
- [ ] Edit checklist mapped (6 items — ALL need code verification)
- [x] Print checklist mapped (10 fields, 2 known gaps)
- [ ] Report checklist mapped (7 reports — need verification)
- [ ] Full code trace completed
