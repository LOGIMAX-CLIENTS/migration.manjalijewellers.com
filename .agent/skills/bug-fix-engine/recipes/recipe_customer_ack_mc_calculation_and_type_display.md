# Recipe: Customer Acknowledgement MC Calculation and Type Display

## Metadata
- **Pattern ID**: PAT-LOT-001
- **Severity**: HIGH (P1) — customer print layout displays incorrect prices/totals and wrong MC types
- **Modules Affected**: Lot (print view)
- **Auto-fixable**: Yes (exact code replacement in customer print template)

## Client Scope
- **Applies to**: ALL
- **Reason**: All customers share the same print view template `customer_ack.php`

## Created By
- **Developer**: Antigravity
- **Client**: (all)
- **Date**: 2026-06-02
- **Source Bug ID**: N/A

## Symptom
On the Customer Acknowledgement print view (`/admin_ret_lot/customer_acknowladgement/2/<lot_id>`), the MC (Making Charge) values are displayed incorrectly:
1. The MC rate (e.g. `20.00`) is printed directly as the calculated total making charge instead of multiplying it by the gross weight or piece count, showing `20.00` total MC instead of `2,000.00` (for a `100.000` gross weight item).
2. The MC type suffix shows `/PCS` instead of `/GRM` for a "Per Gram" item.

## Root Cause
In `admin/application/views/lot/print/customer_ack.php`:
1. The making charge calculation logic was using:
   ```php
   $making_charge = round($po_detail['making_charge'], 3);
   $tot_making_charge += round($po_detail['making_charge'], 3);
   ```
   This treats the making charge rate as the total calculated amount, ignoring the weight/pcs multiplier.
2. The type suffix display checks `mc_type == 2` to print `'Grm'`, which is the reverse of the system-wide convention (where `mc_type = 1` is Per Gram, `mc_type = 2` is Per Piece):
   ```php
   ($po_detail['mc_type'] == 2 ? 'Grm' : 'Pcs')
   ```

## Detection
Check `admin/application/views/lot/print/customer_ack.php` for:
```php
$making_charge = round($po_detail['making_charge'],3);
```
and
```php
$po_detail['mc_type'] == 2 ? 'Grm' : 'Pcs'
```

## Files
- `admin/application/views/lot/print/customer_ack.php`

## Fix

### Before
```php
								// $making_charge = round($po_detail['mc_type'] == 2 ? $po_detail['mc_value'] * $po_detail['gross_wt'] : $po_detail['mc_value'] * $po_detail['no_of_pcs'],3);
								// $tot_making_charge += round($po_detail['mc_type'] == 2 ? $po_detail['mc_value'] * $po_detail['gross_wt'] : $po_detail['mc_value'] * 1,3);

                                $making_charge = round($po_detail['making_charge'],3);

                                $tot_making_charge += round($po_detail['making_charge'],3);
```
and:
```php
									<td class="alignRight"><?php echo !empty($making_charge) ? moneyFormatIndia($making_charge+0).'/'.($po_detail['mc_type'] == 2 ? 'Grm' : 'Pcs'):'';?></td>
```

### After
```php
								$making_charge = round($po_detail['mc_type'] == 1 ? $po_detail['making_charge'] * $po_detail['gross_wt'] : $po_detail['making_charge'] * $po_detail['tot_pcs'], 3);
								$tot_making_charge += $making_charge;
```
and:
```php
									<td class="alignRight"><?php echo !empty($po_detail['making_charge']) ? moneyFormatIndia($po_detail['making_charge']+0).'/'.($po_detail['mc_type'] == 1 ? 'Grm' : 'Pcs'):'';?></td>
```

## Verification
1. Load `http://localhost/coswan/admin/index.php/admin_ret_lot/customer_acknowladgement/2/3945`.
2. Confirm the MC column displays the calculated total making charge (`2,000` for `100.000` gross weight at `20.00` rate).
3. Confirm the suffix displays `20/Grm`.
4. Confirm the MC total in the summary row sums up to `2,000.00`.

## Notes
The system-wide database convention is `mc_type 1 = Per Gram`, `mc_type 2 = Per Piece`.
Only the Customer Acknowledgement print view template was using the raw rate directly and had the suffix check inverted.
