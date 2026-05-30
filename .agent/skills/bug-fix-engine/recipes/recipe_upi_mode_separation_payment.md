# Recipe: UPI Payment Mode Separation from Net Banking

## Metadata
- **Pattern ID**: PAT-PAY-007
- **Severity**: HIGH
- **Modules Affected**: Payment, Reports (Net Banking Report)
- **Auto-fixable**: No (requires multi-file coordination + SQL query changes)

## Client Scope
- **Applies to**: ALL
- **Reason**: Any client that accepts UPI payments may have UPI transactions filed under NB (Net Banking) mode with `NB_type = 3`. The UPI mode was later separated into its own `payment_mode = 'UPI'` value, leaving the old logic inconsistent.

## Created By
- **Developer**: Abinaya
- **Client**: erp.lakshmanaacharison.in
- **Date**: 2026-04-23
- **Source Bug ID**: N/A (fix commits: 5c1e036, 203ff600, 72901c5, a92fd15, 0da0248)

## Symptom
1. **Net Banking report is missing UPI transactions** — UPI payments made via the standalone `payment_mode = 'UPI'` mode do not appear in the Net Banking report at all.
2. **"Edit Payment" screen shows wrong mode label for UPI** — payments stored as UPI mode display incorrectly (e.g., shown as NB or blank) because display logic only handles `NB_type = 3` under the NB umbrella.
3. **NB type dropdown still shows "UPI" as a sub-type** — the `create_new_empty_net_banking_row()` function in `payment.js` still lists UPI as option `value=3` inside the NB type select, even after UPI was split into its own mode.
4. **Bank/device suffix not appended to UPI mode name** — `mode_name` SQL alias in `payment_model.php` skips UPI because the CONCAT branch only triggers for `NB`, `CC`, `DC`.

## Root Cause

UPI payments were originally implemented as a sub-type of Net Banking (`payment_mode = 'NB'`, `NB_type = 3`). The system was later rearchitected to store UPI as its own top-level `payment_mode = 'UPI'`, but **four separate areas of the codebase were not updated in sync**:

1. **Report SQL WHERE clause** — only filtered `payment_mode = 'NB'`, missing the new `'UPI'` mode entirely.
2. **mode_name SQL alias** — the CONCAT branch (which adds bank/device suffix) only triggered for `NB || CC || DC`, excluding `UPI`.
3. **UPI-label condition in mode_name** — checked `pmd.payment_mode = 'NB' && NB_type = 3`, but new UPI records have `payment_mode = 'UPI'`, not `NB`.
4. **JS payment row builder** — `create_new_empty_net_banking_row()` still listed UPI as a dropdown option in the NB type select.

Additionally, the report query was missing `pmd.is_active = 1`, causing soft-deleted payment mode rows to appear in results.

## Detection

```powershell
# 1. Check if NB report query includes UPI mode
grep -n "payment_mode = 'NB'" admin/application/models/ret_reports_model.php

# 2. Check mode_name alias for UPI handling in payment_model.php
grep -n "as mode_name" admin/application/models/payment_model.php

# 3. Check if UPI is still listed as NB sub-type in JS dropdown
grep -n "UPI" admin/assets/js/payment.js | grep -i "nb_type\|nb-type\|option"

# 4. Check if is_active filter exists in NB/UPI report query
grep -n "is_active" admin/application/models/ret_reports_model.php
```

**Bug confirmed if:**
- Query 1 returns `= 'NB'` (not `IN ('UPI','NB')`)
- Query 2 shows the `mode_name` CONCAT condition does NOT include `|| pmd.payment_mode = 'UPI'`
- Query 3 shows `<option value=3>UPI</option>` inside the NB type select
- Query 4 returns no result for the NB/UPI report block

## Files

- `admin/application/models/ret_reports_model.php` — Report SQL WHERE filter
- `admin/application/models/payment_model.php` — `mode_name` SQL alias (display label logic)
- `admin/assets/js/payment.js` — NB type dropdown builder + `nb_type` map
- `admin/application/views/payment/list.php` — Any duplicate UPI modal HTML block (cleanup)

## Fix

### Fix 1 — `ret_reports_model.php`: Include UPI in NB report query + add is_active filter

#### Before
```php
WHERE pmd.payment_mode = 'NB' AND bp.payment_status = 1 AND pmd.payment_status =1 AND bp.receipt_no IS NOT NULL AND ifnull(bp.id_payGateway,0) = 0
```

#### After
```php
WHERE pmd.payment_mode IN ('UPI','NB')  AND bp.payment_status = 1 AND pmd.payment_status =1 AND pmd.is_active =1 AND bp.receipt_no IS NOT NULL AND ifnull(bp.id_payGateway,0) = 0
```

---

### Fix 2 — `payment_model.php`: Broaden `mode_name` CONCAT trigger to include UPI mode

#### Before
```php
IFNULL( if(pmd.payment_mode='FP','Free payment', if(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' ,CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-', IFNULL(IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)')),if(pmd.NB_type = 1,'RTGS',if(pmd.NB_type = 2,'IMPS',if(pmd.NB_type = 3,'UPI',if(pmd.NB_type = 4,'NEFT','')))))),pm.mode_name ) ) ,p.payment_mode) as mode_name,
```

#### After
```php
IFNULL( if(pmd.payment_mode='FP','Free payment', if(pmd.payment_mode = 'NB' || pmd.payment_mode = 'UPI' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' ,CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-', IFNULL(IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)')),if(pmd.NB_type = 1,'RTGS',if(pmd.NB_type = 2,'IMPS',if(pmd.NB_type = 3,'UPI',if(pmd.NB_type = 4,'NEFT','')))))),pm.mode_name ) ) ,p.payment_mode) as mode_name,
```

> **Note**: The intermediate commit `a92fd15` also included `pmd.payment_mode = 'UPI'` in the UPI-label condition. This was later corrected in `0da0248` — the final state above is the correct one. UPI records stored as `payment_mode = 'UPI'` will display using `pm.mode_name` (which already says "UPI"), so the `NB_type = 3` branch only needs to handle the legacy NB records.

---

### Fix 3 — `payment.js`: Remove UPI from NB type dropdown + nb_type map

#### Before — `create_new_empty_net_banking_row()`
```javascript
+ '<td><select name="nb_details[nb_type][]" class="nb_type" ><option value="">Select Type</option><option value=1>RTGS</option><option value=2>IMPS</option><option value=3>UPI</option></select></td>'
```

#### After
```javascript
+ '<td><select name="nb_details[nb_type][]" class="nb_type" ><option value="">Select Type</option><option value=1>RTGS</option><option value=2>IMPS</option></td>'
```

#### Before — `get_payMode_Data()` nb_type map
```javascript
let nb_type = { RTGS: '1', IMPS: '2', UPI: '3' };
```

#### After
```javascript
let nb_type = { RTGS: '1', IMPS: '2'};
```

---

### Fix 4 — `payment/list.php`: Remove duplicate UPI modal block (if present)

If a duplicate `<!-- UPI Details Modal -->` block exists (added by a stale copy during development), remove the extra one. There should be only **one** `<div class="modal fade" id="upi_detail_modal">` in the file.

```bash
# Detection: count occurrences
grep -c "id=\"upi_detail_modal\"" admin/application/views/payment/list.php
# Should be 1. If 2+, remove the duplicate.
```

## Verification

1. **Net Banking Report**: Open the Net Banking report → filter by date range → confirm UPI transactions appear alongside RTGS/IMPS entries. Also verify cancelled/inactive payment mode rows are NOT included.
2. **Edit Payment (UPI mode)**: Open an existing UPI payment → click Edit → confirm the mode name displays correctly (e.g., `UPI - SBI(B)` or `UPI - MyDevice(D)`) without falling back to blank or `NB`.
3. **New NB Payment row**: Open a payment form → add a Net Banking row → confirm the "Type" dropdown shows only `RTGS` and `IMPS` (no `UPI` option).
4. **`php -l` syntax check**:
   ```powershell
   php -l admin/application/models/ret_reports_model.php
   php -l admin/application/models/payment_model.php
   ```
5. **DB sanity check** — confirm UPI records exist in `payment_mode_details` with `payment_mode = 'UPI'` (not NB + NB_type=3):
   ```sql
   SELECT payment_mode, NB_type, COUNT(*) FROM payment_mode_details
   WHERE payment_mode IN ('NB','UPI') GROUP BY payment_mode, NB_type;
   ```

## Notes

- This bug arises when a client upgrades from the old UPI-as-NB-subtype model to the new standalone UPI mode. **Clients that have never had UPI payments will not be affected**.
- Legacy UPI payments (stored as `NB` + `NB_type = 3`) were intentionally kept inclusive in the report by using `IN ('UPI','NB')` — do not narrow this back to `= 'NB'`.
- The `is_active = 1` filter added to the report query is a safe addition — `pmd.is_active` flags logically deleted payment mode rows and should always be filtered in reports.
- Related pattern: **PAT-PAY-004** (`recipe_payment_due_sync_bounds.md`) — multi-mode payment save logic.
- If the client's `payment_mode_details` table does not have an `is_active` column, check the schema before applying Fix 1.
