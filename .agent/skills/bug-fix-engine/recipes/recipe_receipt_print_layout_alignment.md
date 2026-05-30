# Recipe: Receipt Print Layout Alignment Fixes

> Pattern for fixing alignment, spacing, bold, and display issues in receipt print views.

## Metadata
- **Pattern ID**: PAT-BIL-PRINT01
- **Severity**: LOW
- **Modules Affected**: Billing (Receipt Print)
- **Auto-fixable**: Partially (Google Fonts removal and decimal fix are auto-fixable; table restructure requires manual edit)

## Client Scope
- **Applies to**: ALL
- **Reason**: All clients use the same `issueReceipt/print/issue.php` template with the same structural issues

## Created By
- **Developer**: augustineLogimax
- **Client**: erp.sparqlediamonds.com (Sparqle Diamonds)
- **Date**: 2026-04-23
- **Source Bug ID**: #60 (LOGIMAX-CLIENTS/erp.sparqlediamonds.com)

## Symptom
On the receipt print page (`/admin_ret_billing/receipt/receipt_print/{id}`):
1. Colons in the right header block (Invoice No, Date, Time, Gold rates, State Code) are not vertically aligned
2. Large horizontal gap between colon (`:`) and the value (e.g., `: _____ 00027`)
3. Right header text is not bold despite `#a3 { font-weight:bold }` being set
4. Page takes 30–60 seconds to load on local server (Google Fonts CDN timeout)
5. Amounts show `.00` decimals (`Rs 500.00`, `Cash 500.00`, `Total 500.00`)
6. With 7 rows in the right info block, content overflows `.wrapper { height:100px }`

## Root Cause

| # | Issue | Root Cause |
|---|-------|------------|
| 1 | Colon misalignment | `#a3` block used `<label>` inline elements — no column structure |
| 2 | Gap after colon | `#a3 { text-align:right }` was inherited by table cells, pushing content right |
| 3 | Bold not applying | `#a3 table td { ... }` has higher CSS specificity than `#a3 { font-weight:bold }` |
| 4 | Slow load | `fonts.googleapis.com` CDN request times out on local server with no internet |
| 5 | Decimal amounts | `number_format($amount, 2, ...)` hardcoded throughout the view |
| 6 | Wrapper overflow | `.wrapper { height:100px }` is too short for 7 info rows |

## Detection
```powershell
# Check for Google Fonts (causes slow load)
Select-String -Path "admin/application/views/billing/issueReceipt/print/issue.php" -Pattern "fonts.googleapis.com"

# Check for label-based right info block (misalignment)
Select-String -Path "admin/application/views/billing/issueReceipt/print/issue.php" -Pattern 'id="a3"' -Context 0,5

# Check for 2-decimal amount format
Select-String -Path "admin/application/views/billing/issueReceipt/print/issue.php" -Pattern 'number_format.*,2,'
```

## Files
- `admin/application/views/billing/issueReceipt/print/issue.php`

## Fix

### Fix 1: Remove Google Fonts (slow load)

#### Before
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
```

#### After
```html
<!-- Google Fonts removed — causes 30-60s load delay on local server -->
```

---

### Fix 2: Replace label-based #a3 block with table structure

#### Before
```html
<div id="a3">
   <label for="">Invoice No : <?php echo $issue['bill_no']; ?></label><br>
   <label for="">Date : <?php echo $issue['date_add']; ?></label>
   <label for="">Time : <?php echo $issue['time_add']; ?></label><br>
   <label><?php echo 'Gold 22-KT:&nbsp;&nbsp;'.number_format($metal_rate['goldrate_22ct'],2,'.','').'/Gm'.'&nbsp;&nbsp;18-KT:&nbsp;&nbsp;'.number_format($metal_rate['goldrate_18ct'],2,'.','').'/Gm'; ?></label><br>
   <label><?php echo 'SILVER:&nbsp;&nbsp;'.number_format($metal_rate['silverrate_1gm'],2,'.','').'/Gm'; ?></label><br>
   <label for="statecode"><?php echo 'State Code: '.$issue['state_code']; ?></label>
</div>
```

#### After
```html
<div id="a3">
    <table>
        <tr>
            <td>Invoice No</td><td>:</td><td><?php echo $issue['bill_no']; ?></td>
        </tr>
        <tr>
            <td>DateTime</td><td>:</td><td><?php echo $issue['date_add'].' / '.$issue['time_add']; ?></td>
        </tr>
        <tr>
            <td>Gold 22-KT</td><td>:</td><td><?php echo number_format($metal_rate['goldrate_22ct'],2,'.','').'/Gm'; ?></td>
        </tr>
        <tr>
            <td>Gold 18-KT</td><td>:</td><td><?php echo number_format($metal_rate['goldrate_18ct'],2,'.','').'/Gm'; ?></td>
        </tr>
        <tr>
            <td>SILVER</td><td>:</td><td><?php echo number_format($metal_rate['silverrate_1gm'],2,'.','').'/Gm'; ?></td>
        </tr>
        <tr>
            <td>State Code</td><td>:</td><td><?php echo $issue['state_code']; ?></td>
        </tr>
    </table>
</div>
```

---

### Fix 3: Add CSS rules for #a3 table

#### Before
```css
#a3 {
    text-align:right;
}
```

#### After
```css
#a3 {
    text-align:right;
}
#a3 table { border-collapse:collapse; border-spacing:0; margin-left:auto; }
#a3 table td { padding:0 !important; line-height:1.4; font-size:11px; text-align:left; white-space:nowrap; font-weight:bold; }
#a3 table td:first-child { padding-right:2px !important; }
#a3 table td:nth-child(2) { padding:0 3px !important; text-align:center; }
```

---

### Fix 4: Wrapper height auto

#### Before
```css
.wrapper {
    display:flex;
    width: 100%;
    height:100px;
}
```

#### After
```css
.wrapper {
    display:flex;
    width: 100%;
    height:auto;
}
```

---

### Fix 5: Remove decimals from amount fields

#### Before
```php
echo 'Rs '. moneyFormatIndia(number_format($issue['amount'],2,'.',''));
echo moneyFormatIndia(number_format($items['payment_amount'],2,'.',''));
echo moneyFormatIndia(number_format($adjusted_amt,2,'.',''));
echo moneyFormatIndia(number_format((float)($total_amt+...),2,'.',''));
```

#### After
```php
echo 'Rs '. moneyFormatIndia(number_format($issue['amount'],0,'.',''));
echo moneyFormatIndia(number_format($items['payment_amount'],0,'.',''));
echo moneyFormatIndia(number_format($adjusted_amt,0,'.',''));
echo moneyFormatIndia(number_format((float)($total_amt+...),0,'.',''));
```

## Verification
1. Navigate to `/admin/index.php/admin_ret_billing/receipt/receipt_print/{any_id}`
2. Page should load in under 2 seconds (Google Fonts removed)
3. Right header colons should be in a perfectly straight vertical column
4. No gap between `:` and value (e.g., `: 00027` not `:        00027`)
5. All right header text should be bold
6. Amounts show as `Rs 500`, `Cash 500`, `Total 500` (no `.00`)
7. PHP lint check must pass: `php -l admin/application/views/billing/issueReceipt/print/issue.php`

## Notes
- `#a3 table td { padding:0 !important }` is REQUIRED — Bootstrap or billing_receipt_2.css may apply td padding that overrides inline styles without `!important`
- `text-align:left` on each td is REQUIRED — `#a3 { text-align:right }` inherits into table cells and creates the large gap
- `font-weight:bold` must be on `#a3 table td` directly — higher specificity rule overrides the inherited value from `#a3`
- Google Fonts is not used by any other part of the billing receipt — safe to remove entirely
- Amount decimal removal is a display-only change — no database or calculation impact
