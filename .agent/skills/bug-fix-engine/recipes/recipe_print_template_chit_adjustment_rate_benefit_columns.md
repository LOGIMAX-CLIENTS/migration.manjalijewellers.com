# Recipe: Print Template Chit Adjustment Rate Benefit Columns & Split Benefits

## Metadata
- **Pattern ID**: PAT-UI-004
- **Severity**: HIGH
- **Modules Affected**: Billing
- **Auto-fixable**: No (requires print template database adjustments)

## Client Scope
- **Applies to**: Kallarackals
- **Reason**: Client-specific requirement to align chit adjustments table columns and display approx discount benefits for split bills.

## Created By
- **Developer**: Antigravity
- **Client**: Kallarackals
- **Date**: 2026-05-27
- **Source Bug ID**: N/A

## Symptom
The chit adjustments table in the dynamic print template only displays a subset of columns (Ref No, Amount) and does not show rate benefits and total, nor the approx discount benefits for split bills.

## Root Cause
1. The print helper `template_receipt_helper.php` did not compute and map `rate_benefit`, `payable_amount` (row amount), `row_total`, and split-bill cumulative scheme discount values for template-based receipts.
2. The database table configuration in `print_templates` was missing mapping fields for these columns.
3. The local environment database driver threw a `Class 'Globals' not found` fatal error because of a missing validation check for the `Globals` class.

## Detection
Check if `apx_scheme_discount` or `chit_general_payable_total` are mapped in `admin/application/helpers/template_receipt_helper.php`:
```command
grep -rn "apx_scheme_discount" admin/application/helpers/template_receipt_helper.php
```

## Files
- `admin/application/helpers/template_receipt_helper.php`
- `admin/application/helpers/konva_receipt_helper.php`
- `admin/application/core/MY_DB_mysqli_driver.php`

## Fix

### template_receipt_helper.php
Add `rate_benefit`, `total` calculations and mapping fields for `chit_general_items`, `apx_scheme_discount`, and aggregate totals. Replicate the scheme benefit calculations from `bill_format_2.php`.

### konva_receipt_helper.php
Dynamically resolve condition variable `has_chit_items` to either `is_chit_preclose` or `has_chit_adj` based on the element ID or label contents to prevent double rendering of sections.

### MY_DB_mysqli_driver.php
Introduce a class existence check before calling `Globals::$ext_database` to prevent local development fatal crashes.

## Verification
1. Run static rendering test using a CI bootstrapper script on a standard chit bill (e.g. Bill 3259) and split-bill (e.g. Bill 1871).
2. Verify that:
   - S.No, Ref No, Amount (Payable Amount), Rate Benefit, and Total are present and mathematically correct in the rendered table.
   - For split bills, "Apx Scheme Discount" is displayed below the table.
   - For standard bills, the "Apx Scheme Discount" block is completely hidden.

## Notes
A custom database replication script was run to clone print template `478` into `480` and adjust columns config mapping to `{{ref_no}}`, `{{amount}}`, `{{rate_benefit}}`, and `{{total}}`, while adding a text node for approx discount benefits.
