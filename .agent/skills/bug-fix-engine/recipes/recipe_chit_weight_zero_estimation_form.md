# Chit Weight Shows 0.000 in Estimation Form (Weight-Based Flexible Scheme)

## Metadata
- **Pattern ID**: PAT-EST-061
- **Severity**: HIGH
- **Modules Affected**: Estimation (ret_estimation)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core estimation JS shared across all clients with weight-based scheme chit utilization

## Created By
- **Developer**: Antigravity AI
- **Client**: pondythangamaaligai.com
- **Date**: 2026-04-21
- **Source Bug ID**: N/A

## Symptom
In the Estimation Form → Chit Details section, after entering a closed Chit ID (scheme_account_id), the **Weight column shows 0.000** even though:
- The chit bond print correctly shows the weight
- `scheme_account.closing_weight` in DB has the correct value
- The Amount column populates correctly

Affected scheme types: **Flexible Weight-based** (`scheme_type=3, flexible_sch_type=2`) where the scheme's `min_weight == max_weight` (e.g. 0.000 == 0.000).

## Root Cause

A 3-failure chain in `ret_estimation.js` → `get_scheme_acc_number()`:

**Failure 1 — `paid_installments` over-counted by the billing model:**
`ret_billing_model::get_closed_accounts()` computes `paid_installments` via:
```sql
IF((scheme_type=1 OR scheme_type=3) AND min_weight != max_weight,
    COUNT(DISTINCT date_month),  -- variable weight path
    SUM(no_of_dues))             -- falls here when min_weight == max_weight
```
For GHS schemes where `min_weight = max_weight = 0`, it falls to `SUM(no_of_dues)`. If the customer made 2 payments of `no_of_dues=1`, the result is **2**, even though `total_installments=1`.

**Failure 2 — JS guard blocks closing_weight assignment:**
```javascript
// BROKEN: paid_installments(2) != total_installments(1) → guard fails, weight never set
if(i.item.scheme_type!=0 && (i.item.paid_installments==i.item.total_installments)) {
    curRow.find('.closing_weight').val(i.item.closing_balance);
}
```

**Failure 3 — `calculate_chit_closing_balance()` defaults to 0:**
Since `.closing_weight` was never populated, `saving_weight` defaults to 0.

## Detection
```bash
grep -n "paid_installments==i.item.total_installments" admin/assets/js/ret_estimation.js
```
If the above line exists inside `get_scheme_acc_number()`, the bug is present.

Also verify in DB:
```sql
SELECT sa.id_scheme_account, s.scheme_type, s.flexible_sch_type,
       s.total_installments, s.min_weight, s.max_weight,
       sa.paid_installments, sa.closing_weight, sa.is_closed
FROM scheme_account sa
JOIN scheme s ON s.id_scheme = sa.id_scheme
WHERE sa.id_scheme_account = <CHIT_ID>;
```
Bug triggers when: `scheme_type=3`, `flexible_sch_type=2`, `min_weight = max_weight`.

## Files
- `admin/assets/js/ret_estimation.js`

## Fix

### Before
```javascript
if(i.item.scheme_type!=0 &&(i.item.paid_installments==i.item.total_installments))
{
    if(i.item.scheme_type == 3 && i.item.flexible_sch_type == 1) { // 3 - Flexible, 1 - Amount

        curRow.find('.closing_weight').val(0);

    } else {

        curRow.find('.closing_weight').val(i.item.closing_balance);

    }

}
```

### After
```javascript
if(i.item.scheme_type != 0) { // closing_weight: no installment-count guard needed; API already enforces is_closed=1

    if(i.item.scheme_type == 3 && i.item.flexible_sch_type == 1) { // 3 - Flexible, 1 - Amount

        curRow.find('.closing_weight').val(0);

    } else {

        curRow.find('.closing_weight').val(i.item.closing_balance);

    }

}
```

**Rationale:** The `paid_installments == total_installments` guard is redundant because `get_closed_accounts()` already enforces `sa.is_closed = 1` in its WHERE clause. Every returned record is closed. The installment equality check breaks for weight-based flexible schemes where `SUM(no_of_dues)` > `total_installments`. Source version (`etail_development_src`) never had this guard.

## Verification
1. Open Estimation Form → Add Chit Details row
2. Type the closed Chit ID (scheme_type=3, flexible_sch_type=2)
3. Select from autocomplete
4. **Weight column must show the correct gold weight (e.g. 22.346)** — not 0.000
5. Verify Amount also populates correctly
6. Cross-check with scheme_account.closing_weight in DB

## Notes
- Only affects **Flexible Weight-based** schemes (`scheme_type=3, flexible_sch_type=2`) where `min_weight == max_weight`
- Amount-based flexible schemes (`flexible_sch_type=1`) are unaffected (they correctly set weight=0)
- Regular monthly-installment weight schemes (`scheme_type=1`) are typically unaffected since their `min_weight != max_weight` triggers the `COUNT(DISTINCT month)` path
- Fix is source-version-aligned (confirmed against `etail_development_src`)
