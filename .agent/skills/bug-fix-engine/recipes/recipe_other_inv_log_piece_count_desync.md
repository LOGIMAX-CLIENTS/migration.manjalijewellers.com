# Other Inventory Log Piece Count Desync

> Stock In/Out Report and Available Stock Report show different piece counts because log table records REQUESTED pieces instead of ACTUAL processed pieces.

## Metadata
- **Pattern ID**: PAT-INV-001
- **Severity**: CRITICAL
- **Modules Affected**: Other Inventory, Branch Transfer, Billing
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core inventory logic shared across all clients

## Created By
- **Developer**: Antigravity AI
- **Client**: karpagamjewels.com
- **Date**: 2026-05-06
- **Source Bug ID**: N/A

## Symptom
The "Gift Inventory Stock In/Out" report and the "Available Product wise Stock" report show different piece counts for the same item. Stock In/Out may show negative closing pcs while Available Stock shows 0 or a different positive number.

## Root Cause
Two reports read from different tables:
- **Stock In/Out** reads from `ret_other_inventory_purchase_items_log` (log entries with `no_of_pieces`)
- **Available Stock** reads from `ret_other_inventory_purchase_items_details` (actual piece records with `status=0`)

When outward operations (Branch Transfer approval, Issue Item, Billing Gift) execute:
1. They fetch piece records using `LIMIT N` (where N = requested count)
2. If fewer than N records exist, only the available records are processed
3. **BUG**: The log entry records the REQUESTED count (N) instead of the ACTUAL count processed (`count($itemDetails)`)

This creates cumulative drift between the two tables over time.

Additionally, the JS validation for packaging items in Branch Transfer:
- Uses `items.tot_pcs` instead of `items.tot_pcs - items.brch_pcs` (doesn't account for pending BTs)
- Can be bypassed by entering pcs before selecting the item

## Detection
```command
grep -rn "no_of_pieces.*no_of_pcs\|no_of_pieces.*total_pcs" admin/application/controllers/admin_ret_brntransfer.php admin/application/controllers/admin_ret_other_inventory.php admin/application/controllers/admin_ret_billing.php
```
```command
grep -n "var available_pcs = items.tot_pcs" admin/assets/js/ret_branch_transfer.js
```

## Files
- `admin/application/controllers/admin_ret_brntransfer.php` (Branch Transfer approval)
- `admin/application/controllers/admin_ret_other_inventory.php` (Issue Item)
- `admin/application/controllers/admin_ret_billing.php` (Billing Gift)
- `admin/assets/js/ret_branch_transfer.js` (Packaging Items validation)

## Fix

### Fix 1: Branch Transfer — Transit Approval Log (admin_ret_brntransfer.php)

#### Before
```php
$logData=array(
    'item_id'      =>$items['id_other_inv_item'],
    'no_of_pieces' =>$items['no_of_pcs'],
    'date'         =>$date,
    'status'       =>4,
```

#### After
```php
// PAT-INV-001: Validate actual available stock before processing
if(count($itemDetails) < $items['no_of_pcs']) {
    $this->db->trans_rollback();
    $result = array('message'=>'Insufficient stock for item. Available: '.count($itemDetails).', Requested: '.$items['no_of_pcs'],'class'=>'danger','title'=>'Branch Transfer Approval');
    echo json_encode($result);
    exit;
}

// ... (existing foreach loop) ...

$logData=array(
    'item_id'      =>$items['id_other_inv_item'],
    'no_of_pieces' =>count($itemDetails),
    'date'         =>$date,
    'status'       =>4,
```

### Fix 2: Branch Transfer — Stock Download Log (admin_ret_brntransfer.php)

#### Before
```php
$logData=array(
    'item_id'      =>$items['id_other_inv_item'],
    'no_of_pieces' =>$items['no_of_pcs'],
    'date'         =>$date,
    'status'       =>0,
```

#### After
```php
// PAT-INV-001: Validate actual in-transit stock before processing
if(count($itemDetails) < $items['no_of_pcs']) {
    $this->db->trans_rollback();
    $result = array('message'=>'Insufficient in-transit stock for item. Available: '.count($itemDetails).', Requested: '.$items['no_of_pcs'],'class'=>'danger','title'=>'Branch Transfer Approval');
    echo json_encode($result);
    exit;
}

// ... (existing foreach loop) ...

$logData=array(
    'item_id'      =>$items['id_other_inv_item'],
    'no_of_pieces' =>count($itemDetails),
    'date'         =>$date,
    'status'       =>0,
```

### Fix 3: Issue Item Log (admin_ret_other_inventory.php)

#### Before
```php
'no_of_pieces' =>$addData['total_pcs'],
```

#### After
```php
'no_of_pieces' =>count($itemDetails), // PAT-INV-001: use actual processed count
```

### Fix 4: Billing Gift Log (admin_ret_billing.php)

#### Before
```php
'no_of_pieces' =>$est_oth_inv['no_of_pcs'][$key],
```

#### After
```php
'no_of_pieces' =>count($itemDetails), // PAT-INV-001: use actual processed count
```

### Fix 5: JS Keyup Validation (ret_branch_transfer.js)

> NOTE: In source versions where this is already `tot_pcs - brch_pcs`, this fix is already applied. Verify before applying.

#### Before
```javascript
var available_pcs = items.tot_pcs;
if(parseFloat(available_pcs)<no_of_pcs)
{
    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Available Pieces is "+items.tot_pcs});
```

#### After
```javascript
var available_pcs = parseInt(items.tot_pcs) - parseInt(items.brch_pcs);
if(parseFloat(available_pcs)<parseFloat(no_of_pcs))
{
    $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Available Pieces is "+available_pcs});
```

### Fix 6: JS Add Button Re-validation (ret_branch_transfer.js)

Add before `if(allow_submmit) {` (the one that builds trHtml):

```javascript
// PAT-INV-001: Re-validate available stock before adding (prevents bypass when pcs entered before item selection)
if(allow_submmit) {
    var item_id = $("#select_item").val();
    var entered_pcs = parseFloat($("#packaging_no_of_pcs").val());
    $.each(other_inventory_item, function(key, items) {
        if(items.id_other_item == item_id) {
            var available_pcs = parseInt(items.tot_pcs) - parseInt(items.brch_pcs);
            if(entered_pcs > available_pcs) {
                $.toaster({ priority : 'danger', title : 'Warning!', message : ''+"</br>Entered pieces ("+entered_pcs+") exceeds available pieces ("+available_pcs+")"});
                allow_submmit = false;
            }
        }
    });
}
```

## Verification
1. Create a Branch Transfer with Packaging Items — enter pcs above available → should be blocked by JS
2. If JS bypassed, server should reject during approval with "Insufficient stock" message
3. After valid BT approval: check `ret_other_inventory_purchase_items_log` — `no_of_pieces` should match actual detail records processed
4. Compare Stock In/Out closing pcs with Available Stock pcs for the same item/branch — should match
5. Test Issue Item flow — same verification
6. Test Billing with gift items — same verification

## Notes
- Existing bad data in the log table from past transactions is NOT corrected by this fix. A separate data correction SQL may be needed to reconcile historical records.
- The `ret_other_inventory_purchase_items_log` table has status codes: 0=inward, 1=issued, 3=cancelled, 4=in-transit
- The `ret_other_inventory_purchase_items_details` table has status codes: 0=available, 1=issued, 4=in-transit
- Fix 5 (JS keyup) may already be applied in newer source versions — always verify before applying
