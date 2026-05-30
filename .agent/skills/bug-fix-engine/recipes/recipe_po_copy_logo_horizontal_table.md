# Recipe: PO Copy — Logo Change & Horizontal Item Table

## Metadata
- **Pattern ID**: PAT-PUR-001
- **Severity**: LOW
- **Modules Affected**: Purchase Order (admin_ret_purchase)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: karpagamjewels.com
- **Reason**: Client-specific logo and layout preference for vendor acknowledgement PDF

## Created By
- **Developer**: Antigravity AI
- **Client**: Karpagam Jewels
- **Date**: 2026-04-29
- **Source Bug ID**: N/A (feature enhancement)

## Symptom
1. PO Copy (Vendor Acknowledgement PDF) shows old LOGIMAX logo instead of client's own logo
2. Item details displayed row-wise (Weight, Size, Pcs as separate rows per product) instead of column-wise
3. Weight cells contain redundant "gram" suffix when header already says "Weight(gram)"

## Root Cause
1. Logo hardcoded to `receipt_logo.jpg` (Logimax branding) instead of `logo.png` (client logo)
2. The `order_for==1` section used a per-product layout where each product was a heading followed by a 3-row table (Weight row, Size row, Pcs row). This wastes vertical space and is harder to scan.
3. The `weight_range` DB field stores values like "150.000gram" — the suffix was duplicated when header already shows the unit.

## Detection
```command
grep -n "receipt_logo" admin/application/views/ret_purchase/vendor_ack.php
grep -n "order_for==1" admin/application/views/ret_purchase/vendor_ack.php
```

## Files
- `admin/application/views/ret_purchase/vendor_ack.php`

## Fix

### Change 1: Logo — replace receipt_logo.jpg with logo.png + margin

#### Before
```html
<img alt=""  src="<?php echo base_url();?>assets/img/receipt_logo.jpg" ><br>
```

#### After
```html
<img alt=""  src="<?php echo base_url();?>assets/img/logo.png" style="margin-bottom:10px;" ><br>
```

### Change 2: Item table — row-wise to column-wise (order_for==1 block)

#### Before (per-product layout)
```php
foreach($order_details as $items)
{?>
 <lebel><b><?php echo $i.' . '.$items['product_name']...?></b></lebel><br><br>
 <table>
   <tr><td>Weight</td><td>150.000gram</td></tr>  <!-- row-wise -->
   <tr><td>Size</td><td>-</td></tr>
   <tr><td>Pcs</td><td>6</td></tr>
 </table>
 TOTAL PCS : 6 ; APPROX WT : 900.000
<?php } ?>
GRAND TOTAL PCS : 6 ; APPROX WT : 900.000
```

#### After (single table, horizontal columns)
```php
<table style="width:100%;">
  <tr>
    <td width="5%"><b>S.No</b></td>
    <td width="35%"><b>Product</b></td>
    <td width="20%"><b>Weight(gram)</b></td>
    <td width="20%"><b>Size</b></td>
    <td width="20%"><b>PCS</b></td>
  </tr>
  <?php foreach($order_details as $items) {
    // Collect weight_val, size_val, pcs_val from detail arrays
    // Strip "gram" from weight_range: str_replace('gram','',$weight['weight_range'])
  ?>
  <tr>
    <td><?php echo $i;?></td>
    <td><?php echo $items['product_name'].' - '.$items['design_name'].' - '.$items['sub_design_name'];?></td>
    <td><?php echo $weight_val;?></td>
    <td><?php echo $size_val;?></td>
    <td><?php echo $pcs_val;?></td>
  </tr>
  <?php if(!empty($items['description']) && trim($items['description'])!='') {?>
  <tr>
    <td></td>
    <td colspan="4"><b>Remarks:</b> <?php echo $items['description'];?></td>
  </tr>
  <?php } $i++; } ?>
  <tr>
    <td colspan="2"><b>GRAND TOTAL</b></td>
    <td><b><?php echo number_format($grand_total_wt,3,'.','');?></b></td>
    <td></td>
    <td><b><?php echo $grand_total_pcs;?></b></td>
  </tr>
</table>
```

### Change 3: Strip "gram" from weight cell values

#### Before
```php
$weight_val .= ($weight_val!='' ? ', ' : '').$weight['weight_range'];
```

#### After
```php
$weight_val .= ($weight_val!='' ? ', ' : '').str_replace('gram','',$weight['weight_range']);
```

### Change 4: Remarks condition hardened

#### Before
```php
if($items['description']!='')
```

#### After
```php
if(!empty($items['description']) && trim($items['description'])!='')
```

## Verification
1. Navigate to `admin_ret_purchase/get_karigar_acknowladgement/{po_id}` for a PO with `order_for=1`
2. Verify Karpagam Jewels logo appears (not Logimax) with spacing below
3. Verify item table is horizontal: S.No | Product | Weight(gram) | Size | PCS
4. Verify weight cells show numbers only (no "gram" suffix)
5. Verify GRAND TOTAL row shows correct sum of weights and pieces
6. Verify remarks only appear for items with non-empty descriptions
7. Test with PO having `order_for=2` or `order_for=3` — should be unaffected

## Notes
- Only the `order_for==1` section was changed. The `order_for==2/3` section (customer order format) was left untouched.
- The `weight_range` field in the DB stores values like "150.000gram" — the `str_replace` strips only the display, not the DB value.
- The `send_karigar_sms()` method at line ~2091 also uses the same `vendor_ack` view for PDF generation and WhatsApp/email sending — so this change affects SMS PDFs too.
- Logo file `logo.png` must exist at `admin/assets/img/logo.png` on the target server.
