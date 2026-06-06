# Recipe: Konva Receipt Amount Decimal Formatting, Round-off Mathematical Correction, and Payment Label Uppercasing

## Metadata
- **Pattern ID**: PAT-PRT-001
- **Severity**: HIGH
- **Modules Affected**: Print / Receipts
- **Auto-fixable**: No

## Client Scope
- **Applies to**: ALL
- **Reason**: All template-based Konva print layouts consume shared helpers.

## Created By
- **Developer**: Antigravity
- **Client**: Mane Pally
- **Date**: 2026-06-04
- **Source Bug ID**: PRT-CLT01

## Symptom
1. Amount/money fields in the receipt print view show varying decimal lengths (e.g. `4,936.9` instead of `4,936.90`), or lose decimals completely.
2. The print layout shows a mathematical error like `3,29,126.55 (Subtotal) + 4,936.90 (CGST) + 4,936.90 (SGST) = 3,39,000.00 (Total)` because the `TOTAL` field displays the already rounded amount before `Round Off` is listed.
3. Payment method labels like CASH and UPI display in mixed casing (e.g. `Cash` or `Upi`) instead of all uppercase (`CASH` or `UPI`).

## Root Cause
1. `_konva_clean_number` strips trailing zeroes on all numeric fields (including money/amount variables).
2. `$grand_total` includes `$round_off` in its definition too early, causing `TOTAL` to show already rounded values.
3. `ucfirst(strtolower($raw_mode))` is applied as a default fallback label builder for payment modes not matching standard conditions (e.g. `CASH` becomes `Cash`).

## Detection
Ensure print layout has Konva helpers:
```command
grep -rn "_konva_clean_number" admin/application/helpers/
```

## Files
- `admin/application/helpers/konva_receipt_helper.php`
- `admin/application/helpers/template_receipt_helper.php`
- `admin/application/helpers/receipt_helper.php`

## Fix

### 1. `konva_receipt_helper.php` (Formatting amounts and table last columns)
Inside `konva_receipt_helper.php`, define `_konva_is_money_field` and modify `_konva_clean_number`, `_konva_resolve_field`, and `_konva_substitute` to formatting money fields with 2 decimal places:

#### Before:
```php
function _konva_clean_number($value)
{
    if (is_array($value)) return '';
    $s = trim((string)$value);
    if ($s === '') return '';

    if (!preg_match('/^-?[\d,]+\.?\d*$/', $s)) return $value;

    if (strpos($s, '.') !== false) {
        $s = rtrim($s, '0');
        $s = rtrim($s, '.');
    }

    $raw = str_replace(',', '', $s);
    if ($raw === '0' || $raw === '-0' || $raw === '' || $raw === '-') return '';

    return $s;
}
```

#### After:
```php
function _konva_is_money_field($key)
{
    $key = trim(strtolower($key));
    if ($key === '') return false;
    $money_keys = [
        'sub_total', 'subtotal', 'sgst_amount', 'cgst_amount', 'igst_amount',
        'sgst', 'cgst', 'igst', 'round_off', 'handling_charges',
        'grand_total', 'net_amount', 'net_payable', 'net_total', 'exchange_amount',
        'paid_amount', 'total_paid', 'balance_amount', 'due_amount', 'cash_received',
        'cash_amount', 'card_amount', 'upi_amount', 'cheque_amount',
        'pay_cash', 'pay_cheque', 'pay_card', 'pay_credit_card', 'pay_debit_card',
        'pay_rtgs', 'pay_imps', 'pay_neft', 'pay_upi', 'pay_due', 'pay_advance_adj', 'pay_advance_adjustment',
        'total_mc_amount', 'total_taxable', 'discount_amount', 'tcs_amount', 'tds_amount',
        'sales_sub_total', 'sales_total_amount', 'total_sales_amount',
        'total_old_amount', 'total_old_metal_amount', 'purchase_total_amount', 'purchase_cash_paid',
        'total_return_amount', 'total_return_amount_with_gst', 'return_sub_total',
        'total_return_cgst', 'total_return_sgst', 'total_return_igst', 'total_return_tax',
        'sales_return_total', 'total_transfer_amount',
        'advance_adj', 'advance_adjustment', 'advance_deposit', 'order_advance_adj', 'chit_adj', 'receipt_adj',
        'chit_general_total', 'chit_pre_close_total', 'repair_order_total',
        'receipt_adj_total_amount', 'receipt_adj_total_utilized', 'receipt_adj_total_refund', 'receipt_adj_total_balance',
        'credit_balance_amount', 'credit_collection_total',
        'order_amount', 'od_total_amount', 'od_approx_total',
        'rate_benefit', 'chit_benefit', 'chit_benefit_before_gst',
        'apx_scheme_discount', 'chit_general_payable_total', 'chit_general_rate_benefit_total',
        'item_total', 'total_va_content', 'sales_total_item_total', 'total_stone_amount',
        'amount', 'old_metal_amount', 'return_amount', 'stone_amount', 'return_stone_amount',
        'old_metal_stone_amount', 'cgst_amt', 'sgst_amt', 'igst_amt', 'mc_amt', 'taxable_amount',
        'discount', 'va_discount_amount', 'bill_discount', 'tot_amount', 'net_amt', 'sales_amount',
        'cash', 'card', 'cheque', 'upi', 'pay_credit', 'outstanding_amount',
        'total_gst_amount', 'cgst_total', 'sgst_total', 'igst_total', 'total_tax_amount',
        'total_taxable_amount', 'total_gst', 'tax_amount', 'vat_amount', 'vat_amt', 'tax_amt',
        'gross_amount', 'total_mc', 'total_va', 'total_stone', 'other_charges_amount',
        'handling_charges_amount', 'total_pure_wt_amount', 'pure_wt_amount', 'pure_amount',
        'rate_benefit_amount', 'discount_amt', 'va_discount_amt', 'bill_discount_amt',
        'mc_amount', 'va_amount', 'net_amount_with_tax', 'rate', 'old_metal_rate'
    ];
    if (in_array($key, $money_keys, true)) return true;
    if (preg_match('/(amount|amt|total|rate|price|cgst|sgst|igst|gst|tax|discount|charges|mc)/', $key)) {
        if (!preg_match('/(weight|wt|qty|pcs|pieces|count|percent|%)/', $key)) return true;
    }
    return false;
}

function _konva_clean_number($value, $is_money = false)
{
    if (is_array($value)) return '';
    $s = trim((string)$value);
    if ($s === '') return '';
    if (!preg_match('/^-?[\d,]+\.?\d*$/', $s)) return $value;

    $raw = str_replace(',', '', $s);
    $val_float = floatval($raw);
    if ($val_float == 0.0) return '';

    if ($is_money) {
        if (function_exists('moneyFormatIndia')) {
            $formatted = sprintf("%.2f", $val_float);
            return moneyFormatIndia($formatted);
        } else {
            return number_format($val_float, 2);
        }
    }

    if (strpos($s, '.') !== false) {
        $s = rtrim($s, '0');
        $s = rtrim($s, '.');
    }
    return $s;
}
```

Also pass `is_last_col` in `_konva_render_table` cell resolution to ensure the last column of the table (and its total in footer) formats to exactly 2 decimals:
```php
$is_last_col = ($ci + $bcs >= count($cols));
$cellVal = _konva_resolve_field($field, $item, $bill_data, $is_last_col);
```

### 2. `template_receipt_helper.php` (Mathematical correction for totals and uppercase labels)
Exclude `$round_off` from `$grand_total` calculation and use the lookup in `$mode_map` for uppercase formatting:

#### Before:
```php
$grand_total = $total_taxable_amt + $total_sgst + $total_cgst + $total_igst + $round_off + $handling_charges;
...
        } else {
            $label = ucfirst(strtolower($raw_mode));
            $ref   = '';
        }
```

#### After:
```php
$grand_total = $total_taxable_amt + $total_sgst + $total_cgst + $total_igst + $handling_charges;
// Add $round_off later in net_amount and due_amount mapping:
$net_amount_val = $grand_total + $round_off - $total_purchase_amt;
...
        } else {
            $label = isset($mode_map[$raw_mode]) ? $mode_map[$raw_mode]['label'] : ucfirst(strtolower($raw_mode));
            $ref   = '';
        }
```

### 3. `receipt_helper.php` (Uppercase labels for fallback)
Apply the same changes as `template_receipt_helper.php` for `mode_map` definitions and loop translations.

## Verification
1. Load a print view for a bill with round-off values (e.g. bill_id 3267).
2. Check that amount values show 2 decimal places (e.g. `4,936.90` instead of `4,936.9`).
3. Check that `SUB TOTAL + CGST + SGST = TOTAL` mathematically adds up, and the `Round Off` is applied to obtain the `Net Amount`.
4. Verify that the payment methods list renders as `CASH` or `UPI` (fully capitalized).

## Notes
None
