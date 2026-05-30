# Recipe: Excess Installment Payment Not Blocked

## Metadata
- **Pattern ID**: PAT-PAY-001
- **Severity**: CRITICAL
- **Modules Affected**: Payment (Admin + Mobile)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal allow_pay logic flaw — `<=` operator lets payment when paid == total

## Created By
- **Developer**: Antigravity
- **Client**: plans.sriaumjewellery.com
- **Date**: 2026-04-07
- **Source Bug ID**: N/A

## Symptom
Customers can make payments BEYOND the total number of installments defined in a scheme. Both Admin panel and Mobile App allow excess payments. Particularly visible in One-Time Deposit (scheme_type 3) but affects all scheme types.

## Root Cause
The `allow_pay` logic in both admin and mobile models uses `<=` (less-than-or-equal) to compare `paid_installments` against `total_installments`. When `paid == total`, the condition `paid <= total` is TRUE, and the complex nested ternary falls through to 'Y' in many code paths. There is no top-level guard that unconditionally blocks when installments are exhausted.

### Why `<=` is wrong:
- `paid_installments < total_installments` → payment needed (correct to allow)
- `paid_installments == total_installments` → scheme complete (should block)
- `paid_installments > total_installments` → over-paid (should block)

The `<=` operator treats `==` as allowed, which is the bug.

### Multi-chance complication:
If `max_chance > 1` (multiple payments allowed per installment), then `paid_installments == total_installments` should STILL allow payment if `current_chances_used < max_chance` (remaining chances in the last installment month).

## Detection
```command
grep -rn "paid_installments <= \$record->total_installments" admin/application/models/payment_model.php application/models/mobileapi_model.php application/models/payment_modal.php
```

Also check for missing top-level guard:
```command
grep -n "// Allow Pay\|// allow pay" admin/application/models/payment_model.php application/models/mobileapi_model.php
```

## Files
- `admin/application/models/payment_model.php` — Admin allow_pay logic (inside `get_paymentContent`)
- `application/models/mobileapi_model.php` — Mobile allow_pay logic (inside `get_payment_details`)
- `application/models/payment_modal.php` — Web payment model (has similar logic)

## Fix

### File 1: admin/application/models/payment_model.php

Find the `// Allow Pay` comment block inside `get_paymentContent()` and add a top-level guard BEFORE the scheme_type check.

#### Before
```php
// Allow Pay
if($record->scheme_type == 3){
```

#### After
```php
// Allow Pay
// Block if all installments are completed (universal guard for ALL scheme types)
// Exception: multi-chance schemes — allow remaining chances in last installment
if($record->paid_installments >= $record->total_installments){
    if($record->max_chance > 1 && $record->current_chances_used < $record->max_chance){
        // Allow - still has chances remaining in this installment
        $allow_pay = 'Y';
    } else {
        $allow_pay = 'N';
    }
}
else if($record->scheme_type == 3){
```

### File 2: application/models/mobileapi_model.php

Find the `// allow pay` comment block inside `get_payment_details()` and add the same top-level guard.

#### Before
```php
// allow pay
if($record->scheme_type == 3){
```

#### After
```php
// allow pay
// Block if all installments are completed (universal guard for ALL scheme types)
// Exception: multi-chance schemes — allow remaining chances in last installment
if($record->paid_installments >= $record->total_installments){
    if($record->max_chance > 1 && $record->current_chances_used < $record->max_chance){
        // Allow - still has chances remaining in this installment
        $allow_pay = 'Y';
    } else {
        $allow_pay = 'N';
    }
}
else if($record->scheme_type == 3){
```

### File 3: application/models/payment_modal.php (Web Model)

Same pattern — find `// allow pay` or `// Allow Pay` inside the function that builds the chit list and add the identical guard before `if($record->scheme_type == 3){`.

## Verification
1. Find an account with `paid_installments >= total_installments` in DB:
   ```sql
   SELECT sa.id_scheme_account, s.total_installments,
     IFNULL(IF(sa.is_opening=1, sa.paid_installments + COUNT(DISTINCT DATE_FORMAT(p.date_payment,'%Y%m')),
     COUNT(DISTINCT DATE_FORMAT(p.date_payment,'%Y%m'))),0) as paid
   FROM scheme_account sa
   LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
   LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account AND (p.payment_status=1 OR p.payment_status=2)
   WHERE sa.active=1 AND sa.is_closed=0
   GROUP BY sa.id_scheme_account
   HAVING paid >= s.total_installments
   ```
2. Load that account in Admin payment page → confirm `allow_pay` = 'N' in AJAX response
3. Load mobile API `payDuesData_get` for that customer → confirm scheme shows `allow_pay: 'N'`
4. Test edge case: scheme with `max_chance > 1` and last installment → confirm chances still work
5. Test edge case: scheme with `payment_chances = 1` → confirm distinct month counting doesn't break

## Notes
- The existing complex ternary operators ALSO have `<= total_installments` checks inside them — our top-level guard short-circuits before those are evaluated, making them irrelevant for the `==` case
- `payment_modal.php` (web model) has the same flaw but may not be actively used for payment processing — still should be fixed for consistency
- The `adminappapi_model.php` also has inline `allow_pay` ternaries with `<=` — should be audited if admin app is used
- `current_chances_used` comes from the `cp` subquery counting current month payments; `max_chance` comes from the scheme table
