# Recipe: Concurrent Payment Race Condition (Admin + Mobile)

## Metadata
- **Pattern ID**: PAT-PAY-002
- **Severity**: CRITICAL
- **Modules Affected**: Payment (Admin SaveAll)
- **Auto-fixable**: Yes
- **Related**: PAT-PAY-001 (UI-level allow_pay guard — this recipe adds server-side INSERT-time defense)

## Client Scope
- **Applies to**: ALL
- **Reason**: Universal flaw — admin SaveAll has no server-side re-validation before INSERT

## Created By
- **Developer**: Antigravity
- **Client**: lskjewellers.com
- **Date**: 2026-04-08
- **Source Bug ID**: N/A

## Symptom
Customer ends up with more payments than total_installments allows (e.g., 19 payments on an 18-month scheme). Happens when admin panel and mobile app process payments for the same account simultaneously. The admin form shows stale installment count from page-load time, and the backend does not recheck before INSERT.

Additionally, `date_payment` records the time the admin form was OPENED (JS `new Date()` at page load), not the time it was actually saved — causing misleading timestamps.

## Root Cause

### Race Condition:
1. Admin opens payment form → JS captures `new Date()` as `date_payment` → shows X/N installments
2. While form is open, mobile app payment completes → becomes the Nth installment
3. Admin clicks Save → `paymentDB("insert")` runs without re-validating installment count
4. Result: N+1 payment is inserted, bypassing scheme limit

### Stale date_payment:
`date_payment` is set by JavaScript `new Date()` at page load and sent as a readonly POST field. PHP reformats it via `strtotime()` but never replaces it with server time. So if the form is open for 4 minutes, `date_payment` is 4 minutes behind `date_add`.

## Detection
```command
grep -n "date_payment.*strtotime.*generic" admin/application/controllers/admin_payment.php
```
Check for absence of re-validation before paymentDB("insert") in SaveAll case:
```command
grep -n "paymentDB.*insert" admin/application/controllers/admin_payment.php
```
Check if `revalidate_installment_limit` function exists:
```command
grep -n "revalidate_installment_limit" admin/application/models/payment_model.php
```

## Files
- `admin/application/controllers/admin_payment.php` — SaveAll case (date_payment fix + guard logic)
- `admin/application/models/payment_model.php` — New `revalidate_installment_limit()` function

## Fix

### Fix 1: date_payment — Use server time instead of stale JS time

**File:** `admin/application/controllers/admin_payment.php` — inside SaveAll case

#### Before
```php
$date_payment = (isset($generic['date_payment'])? date('Y-m-d H:i:s',strtotime(str_replace("/","-",$generic['date_payment']))):NULL);
```

#### After
```php
//  FIX: Use server-side PHP datetime instead of stale JS page-load time [Race Condition Fix]
$date_payment = date('Y-m-d H:i:s');
```

### Fix 2: Race condition guard before INSERT

**File:** `admin/application/controllers/admin_payment.php` — inside SaveAll for-loop, just before `paymentDB("insert")`

#### Before
```php
//$this->db->trans_begin();
$status = $this->$model->paymentDB("insert","",$pay_array);
```

#### After
```php
//$this->db->trans_begin();

// === RACE CONDITION GUARD: Re-validate installment count at INSERT time ===
// Prevents concurrent payments (admin NB + mobile UPI) from exceeding total_installments
$__schData = $this->payment_model->revalidate_installment_limit($generic['id_scheme_account']);
$__currentPaid = isset($__schData['paid_installments']) ? intval($__schData['paid_installments']) : 0;
$__totalIns = isset($__schData['total_installments']) ? intval($__schData['total_installments']) : 0;
// Only block normal dues (ND/PD/PN/AN/AD/PC) — allow GA/GEN_ADV to pass through
if($__totalIns > 0 && $__currentPaid >= $__totalIns && !in_array($dueType, array('GA','GEN_ADV'))){
    $this->db->trans_rollback();
    $lg_data = "\n BLOCKED -- ".date('Y-m-d H:i:s')." -- Race condition guard: paid=".$__currentPaid." >= total=".$__totalIns." for scheme_account=".$generic['id_scheme_account'];
    file_put_contents($log_path, $lg_data, FILE_APPEND | LOCK_EX);
    echo json_encode(array('is_valid' => FALSE, 'success' => FALSE, 'msg' => 'This scheme has completed all '.$__totalIns.' installments. Further payments are not allowed.'));
    return;
}
// === END RACE CONDITION GUARD ===

$status = $this->$model->paymentDB("insert","",$pay_array);
```

### Fix 3: New model function

**File:** `admin/application/models/payment_model.php` — add near other scheme functions

**IMPORTANT:** Do NOT name this `get_paidInstallmentCount` — that function already exists (line ~4885) in the same model. PHP function names are case-insensitive so it will cause a fatal "Cannot redeclare" error.

```php
/**
 * Race Condition Guard: Get fresh paid installment count with payment_status filter
 * Used for server-side re-validation at INSERT time to prevent concurrent payments
 * from exceeding total_installments.
 */
function revalidate_installment_limit($id_scheme_account)
{
    $sql = "SELECT
        s.total_installments,
        s.scheme_type, s.min_weight, s.max_weight, s.payment_chances, s.flexible_sch_type,
        sa.is_opening, sa.paid_installments as opening_paid,
        IFNULL(IF(sa.is_opening=1,
            IFNULL(sa.paid_installments,0) + IFNULL(
                if(s.scheme_type = 1 and s.min_weight != s.max_weight,
                    COUNT(Distinct Date_Format(p.date_payment,'%Y%m')),
                    sum(p.no_of_dues)
                ),0),
            if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1),
                COUNT(Distinct Date_Format(p.date_payment,'%Y%m')),
                sum(p.no_of_dues))
        ),0) as paid_installments
        FROM scheme_account sa
        LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
        LEFT JOIN payment p ON (p.id_scheme_account = sa.id_scheme_account AND (p.payment_status = 1 OR p.payment_status = 2))
        WHERE sa.id_scheme_account = '$id_scheme_account'
        GROUP BY sa.id_scheme_account";
    $result = $this->db->query($sql);
    if($result->num_rows() > 0)
    {
        return $result->row_array();
    }
    return array('paid_installments' => 0, 'total_installments' => 0);
}
```

## Verification
1. Open admin payment form for an account with 17/18 installments paid
2. Simultaneously complete a mobile app payment for the same account
3. Then submit the admin form → should be BLOCKED with message "This scheme has completed all 18 installments..."
4. Check log file for "BLOCKED -- Race condition guard" entry
5. Verify `date_payment` in new payments matches `date_add` (no stale timestamps)
6. Verify General Advance (GA) payments are still allowed past total installments

## Notes
- PAT-PAY-001 (allow_pay guard) prevents the form from loading when paid >= total. This recipe (PAT-PAY-002) is the defense-in-depth layer for when concurrent payments slip through the UI guard.
- `$dueType` variable is available inside the SaveAll for-loop (set at ~line 1649)
- `$log_path` variable must be available in scope — it's typically set earlier in the SaveAll case
- The existing `get_paidinstallmentcount()` function (line ~4885) returns raw payment rows — it does NOT return `total_installments`, so it cannot be reused for this guard
- For reverse scenario (admin saves first, mobile completes later), consider auto-refund in mobile gateway callback — this is a proposed future enhancement
