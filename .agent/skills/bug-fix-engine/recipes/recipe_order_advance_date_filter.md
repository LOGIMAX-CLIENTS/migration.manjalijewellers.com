# Recipe: Order Advance Missing After Day Close

## Metadata

| Field | Value |
|---|---|
| Pattern ID | PAT-BIL-ADV-001 |
| Severity | P1 |
| Modules Affected | Billing (`ret_billing_model.php` — `get_previous_order_details()`) |
| Auto-fixable | Yes — 2-line deletion per query |
| Bug ID | BIL-ADV-001 |

## Client Scope

**Applies to**: Any client where customers pay order advances (cash or old gold) across
multiple days with day-close operations between payments.

## Created By

| Field | Value |
|---|---|
| Developer | Antigravity |
| Client | AVSR |
| Date | 2026-04-20 |

---

## Symptom

On the bill print (`bill_format_2.php`, `receipt_billing.php`, `billing_soft_copy.php`),
advances (cash and/or old gold) made on a **later day** (after a day close) do not appear
in the **Advance Amount** summary. The **Approx Balance Amount** is therefore too high.

**Example:**
- Day 1: Cash ₹1,000 + Old Gold ₹9,000 → shows correctly
- Day close run
- Day 2: Cash ₹500 + Old Gold ₹5,000 → **missing from bill**
- Balance shows ₹40,000 instead of correct ₹34,500

---

## Root Cause

`get_previous_order_details()` in `ret_billing_model.php` contains two SQL queries
(one for `advance_type=1` cash, one for `advance_type=2` old gold). Both had:

```sql
AND date(bill.bill_date) <= '$bill_date'
```

Where `$bill_date = $billing['bill_created_time']` (the current bill's creation datetime,
e.g., `2026-04-20`).

After a day close, new advance bills get `bill_date = 2026-04-21`. The filter then evaluates:
```
2026-04-21 <= 2026-04-20 → FALSE → advance excluded
```

The date filter is unnecessary because `rba.id_customerorder` already scopes to the correct
order, and `bill.bill_status=1` already excludes cancelled bills.

---

## Detection

```bash
grep -n "bill_date.*bill_date\|bill_date.*advance" admin/application/models/ret_billing_model.php
```

Look for the pattern inside `get_previous_order_details()`:
```sql
date(bill.bill_date) <= '$bill_date'
```
If present in either Query 1 or Query 2 of this function → bug exists.

---

## Files

| File | Function | Lines |
|---|---|---|
| `admin/application/models/ret_billing_model.php` | `get_previous_order_details()` | ~9458–9525 |

---

## Fix

### Query 1 — Cash Advances (advance_type=1)

**Before:**
```sql
WHERE rba.id_customerorder = '{id}' AND rba.advance_type=1 and
      bill.id_branch = {branch} AND rba.advance_amount>0 And
      date(bill.bill_date) <= '{bill_date}' AND
      (bill.bill_status=1 and bill.bill_id != {bill_id})
```

**After:**
```sql
WHERE rba.id_customerorder = '{id}' AND rba.advance_type=1 AND
      bill.id_branch = {branch} AND rba.advance_amount>0 AND
      (bill.bill_status=1 and bill.bill_id != {bill_id})
```

---

### Query 2 — Old Gold Advances (advance_type=2)

**Before:**
```sql
WHERE rba.id_customerorder = '{id}' AND
      bill.id_branch = {branch} AND rba.advance_amount>0 And
      date(bill.bill_date) <= '{bill_date}' AND rba.advance_type=2 and
      (bill.bill_status=1 OR bill.bill_id = {bill_id})
```

**After:**
```sql
WHERE rba.id_customerorder = '{id}' AND
      bill.id_branch = {branch} AND rba.advance_amount>0 AND rba.advance_type=2 AND
      (bill.bill_status=1 OR bill.bill_id = {bill_id})
```

---

## Rollback

Re-add the removed line to each query:
```sql
AND date(bill.bill_date) <= '$bill_date'
```

No database changes — rollback is instant.

---

## Verification

### SQL Check
```sql
-- Shows ALL advances for an order regardless of date:
SELECT rba.advance_type, rba.advance_amount, DATE(bill.bill_date) as adv_date
FROM ret_billing_advance rba
LEFT JOIN ret_billing bill ON bill.bill_id = rba.bill_id
WHERE rba.id_customerorder = {ORDER_ID}
  AND bill.id_branch = {BRANCH_ID}
  AND rba.advance_amount > 0
ORDER BY bill.bill_date;
```

### Manual Test
1. Create order on Day 1 with cash + old gold advance
2. Run day close
3. Create another cash + old gold advance on Day 2 for same order
4. Print bill → verify ALL 4 advances appear in Advance Amount
5. Verify: Balance = Order Amount − all advances − cash at sale

### Balance Formula
```
Approx Balance = Order Amount
               − SUM(all advance_type=1 amounts)
               − SUM(all advance_type=2 amounts)
               − cash_received_at_time_of_sale
```
