# Recipe: DigiGold Same-Day Multiple Payment — Installment Number Not Stored

## Metadata
- **Pattern ID**: PAT-DG-001
- **Severity**: HIGH
- **Modules Affected**: Payment (admin_payment), Scheme Account, DigiGold
- **Auto-fixable**: No (requires context-aware else-block insertion)

## Client Scope
- **Applies to**: ALL clients using DigiGold (is_digi = 1) or any scheme with installment_cycle = 1 (daily) that allows multiple payments per day
- **Reason**: The NOT IN guard in get_due_date() is a shared function used by all schemes; the bug affects any scheme where multiple payments on the same day are permitted

## Created By
- **Developer**: Antigravity AI Agent
- **Client**: etail_development_src (source repo)
- **Date**: 2026-04-25
- **Source Bug ID**: N/A

## Symptom

For DigiGold scheme accounts, the **first payment of the day** stores an installment number correctly. Every **subsequent payment on the same day** stores `NULL` in the `installment` column.

This causes:
- Receipt print shows blank/wrong installment number for 2nd+ payments
- Passbook print has incorrect installment sequence
- Paid installment count (`total_paid_ins`) may be under-counted

## Root Cause

In `chit_transaction_model::updatedue_details()`, the `get_due_date()` function uses a `NOT IN` exclusion clause:

```sql
AND dt.due_date_from NOT IN (
  SELECT p.due_date FROM payment p
  WHERE p.payment_status = 1
    AND p.due_date IS NOT NULL
    AND p.id_scheme_account = sa.id_scheme_account
) LIMIT 1
```

**Payment 1 (same day):** `due_date_from = today` is not yet in `payment.due_date` → slot found → installment stored ✅

**Payment 2+ (same day):** Payment 1 already stored `due_date = today`. The `NOT IN` now excludes today's slot → query returns **zero rows** → `sizeof($ins_cycle[0]) > 0` is `false` → the entire `installment` update block is **skipped** → `NULL` stored in `payment.installment` ❌

The guard was designed for monthly schemes (prevent double-counting a paid month). For daily/digi schemes with same-day multi-payments, all payments on the same day legitimately share the **same installment number and due_date slot**.

## Detection

```bash
# Check for payments with NULL installment on the same day for a DigiGold account
grep -n "NOT IN (SELECT p.due_date" admin/application/models/chit_transaction_model.php
grep -n "NOT IN (SELECT p.due_date" admin/application/models/payment_model.php

# DB query to detect affected records
SELECT id_payment, id_scheme_account, date_payment, installment, due_date
FROM payment p
JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE s.is_digi = 1
  AND p.payment_status = 1
  AND p.installment IS NULL
  AND p.due_date IS NULL
ORDER BY p.id_scheme_account, p.date_payment;
```

## Files

- `admin/application/models/chit_transaction_model.php` — function `updatedue_details()` (~line 668)

## Fix

### Before

```php
function updatedue_details($pay)
{
    $dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));

    $ins_cycle = $this->payment_model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);

    if (sizeof($ins_cycle[0]) > 0) {

        $cycle_data = array(
            'due_date'       => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
            'due_date_to'    => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
            'grace_date'     => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
            'installment'    => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
            'is_limit_exceed'=> (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
        );

        $this->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
    }
    // ← no else branch — 2nd+ same-day payments silently get NULL installment
```

### After

```php
function updatedue_details($pay)
{
    $dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));

    $ins_cycle = $this->payment_model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);

    if (sizeof($ins_cycle[0]) > 0) {

        $cycle_data = array(
            'due_date'       => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
            'due_date_to'    => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
            'grace_date'     => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
            'installment'    => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
            'is_limit_exceed'=> (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
        );

        $this->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');

    } else {
        // [FIX] DigiGold same-day multiple payments:
        // get_due_date() returns empty when the due_date slot for today is already
        // claimed by an earlier payment (NOT IN clause blocks the 2nd+ payment).
        // Look up the earlier same-day payment's installment/due dates and reuse them.
        $sameday_pay = $this->db->query(
            "SELECT p.installment, p.due_date, p.due_date_to, p.grace_date
             FROM payment p
             WHERE p.id_scheme_account = " . (int)$pay['id_scheme_account'] . "
               AND p.payment_status = 1
               AND p.installment IS NOT NULL
               AND p.installment > 0
               AND DATE(p.due_date) = '" . $this->db->escape_str($dt_pay) . "'
               AND p.id_payment != " . (int)$pay['id_payment'] . "
             ORDER BY p.id_payment ASC
             LIMIT 1"
        )->row_array();

        if (!empty($sameday_pay) && !empty($sameday_pay['installment'])) {
            $cycle_data = array(
                'due_date'    => $sameday_pay['due_date'],
                'due_date_to' => $sameday_pay['due_date_to'],
                'grace_date'  => $sameday_pay['grace_date'],
                'installment' => $sameday_pay['installment'],
            );
            $this->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
        }
    }
```

## Verification

1. Open the payment form for a DigiGold scheme account
2. Make **Payment 1** for today → check DB: `SELECT installment, due_date FROM payment WHERE id_payment = <id>` — should show installment number (e.g. `7`) and a valid due_date
3. Make **Payment 2** for the same account on the same day → check DB for the new payment row — `installment` should match Payment 1's installment number (NOT NULL)
4. Make **Payment 3** if allowed — same check
5. Open receipt print for Payment 2 → installment number should display correctly
6. Open passbook print → all same-day payments should show the same installment number
7. Check a **normal monthly scheme** payment — installment should still assign sequentially (regression test)

## Notes

- The fix is placed in `updatedue_details()` as a consumer-level fallback. `get_due_date()` is NOT modified — its `NOT IN` logic remains correct for monthly schemes.
- The fallback query uses `(int)` cast on IDs and `escape_str()` on the date — safe from injection.
- This pattern will also fix any future scheme type that permits multiple payments per day (daily chit, daily savings, etc.) without any additional changes.
- **Rollback:** Remove the `else { ... }` block. The `if` path and all surrounding code are untouched.
- Related: check `payment_model::get_due_date()` (~line 8109) — identical `NOT IN` clause exists there. Same fix would apply if that path is also used for DigiGold saves. The `chit_transaction_model` version is the one called by `onPayTranStream` (the active admin save path).
