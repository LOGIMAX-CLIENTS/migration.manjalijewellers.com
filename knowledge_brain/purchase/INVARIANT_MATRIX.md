# PURCHASE MODULE — INVARIANT MATRIX
> **Module:** Purchase | **Version:** 1.0 | **Date:** 2026-02-23

---

## Dimension 1: Bill Type (`gst_bill_type`)

| Value | Label | When Used | Controls |
|---|---|---|---|
| `P` | GST Purchase | Registered vendor (has GST number) | Full GST calculation (CGST+SGST or IGST) |
| `PM` | Non-GST Purchase | Unregistered vendor | No GST, different ref number prefix |
| `PA` | Approval Stock Purchase | Items received on approval basis | Different workflow — may convert to P/PM later |

**Controlling fields:**
- DB: `ret_purchase_order.gst_bill_type`
- PHP: `$gst_bill_type` variable in `generatePurRefOrderNo()`
- JS: `$("input[name='billing[bill_type]']:radio")` radio buttons

---

## Dimension 2: Rate Type (`rate_type`)

| Value | Label | When Used | Formula |
|---|---|---|---|
| `1` | Per-Piece | Fixed cost items | `total = item_cost × pcs` |
| `2` | Per-Gram (default) | Weight-based pricing | `total = net_wt × rate + making_charge + wastage + stone_charge` |

**Controlling fields:**
- DB: `customerorder.rate_type`
- PHP: `$addData['rate_type']` (defaults to 2)
- JS: Rate type selector, controls `#item_cost` readonly state

---

## Dimension 3: Order Type (`order_type`)

| Value | Label | Item Source | Special Behavior |
|---|---|---|---|
| `1` | Stock Order | Free item entry | No customer order reference |
| `2` | Customer Order | From pending customer orders | `cus_ord_ref` required, loads customer order details |
| `3` | Stock Repair | From repair order list | Loads stock repair order details |

**Controlling fields:**
- DB: `customerorder.order_type`
- JS: `ctrl_page` + order type selector

---

## Dimension 4: Bill Status (`bill_status`)

| Value | Label | Allowed Operations | Transition From |
|---|---|---|---|
| `0` | Active/Open | Edit, Pay, Rate Fix, Cancel | Initial state |
| `1` | Completed | View only, Print | After full payment |
| `2` | Cancelled | View only | From Active (with reason) |

---

## Dimension 5: QC Status (per item)

| Value | Label | Next Step |
|---|---|---|
| `0` | Pending QC | QC Issue |
| `1` | QC Passed | HM Issue or Lot Generate |
| `2` | QC Failed/Rejected | Purchase Return |

---

## Behavior Grid: Bill Type × Rate Type

| | **Per-Piece (1)** | **Per-Gram (2)** |
|---|---|---|
| **P (GST)** | item_cost × pcs + GST | (net_wt × rate + MC + VA + stones) + GST |
| **PM (Non-GST)** | item_cost × pcs (no GST) | (net_wt × rate + MC + VA + stones) (no GST) |
| **PA (Approval)** | item_cost × pcs + GST (if converted) | (net_wt × rate + MC + VA + stones) + GST (if converted) |

> **⚠️ Edge Case:** PA bills start without full pricing. Final cost determined on conversion.

## Behavior Grid: Order Type × Rate Type

| | **Per-Piece (1)** | **Per-Gram (2)** |
|---|---|---|
| **Stock Order (1)** | Rare — fixed price stock items | Standard — gold/silver items |
| **Customer Order (2)** | Custom items with fixed price | Standard — customer order with weight specs |
| **Stock Repair (3)** | Repair job at fixed cost | Repair priced by weight |

---

## Dimension 6: Payment Mode (in `supplier_po_payment`)

| Value | Label | Fields Required | Ledger Impact |
|---|---|---|---|
| Cash | Cash payment | amount only | Debit cash, credit supplier |
| Bank | Bank transfer/cheque | amount, bank_id, cheque_no (optional) | Debit bank, credit supplier |
| Metal | Metal adjustment | metal_weight, metal_purity, metal_rate | Convert weight→value, credit supplier |

## Behavior Grid: Payment Mode × Bill Type

| | **P (GST)** | **PM (Non-GST)** | **PA (Approval)** |
|---|---|---|---|
| **Cash** | Standard cash + GST ledger | Cash only | Cash after conversion |
| **Bank** | Bank + TDS deduction possible | Bank, no TDS | Bank after conversion |
| **Metal** | Metal value at current rate + GST | Metal value, no GST | Metal after conversion |

> **⚠️ Key:** TDS is applicable only for registered vendors (Bill Type P) above threshold.