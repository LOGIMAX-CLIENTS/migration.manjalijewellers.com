# Pool-to-Packet Migration: INWARD Pattern

> Replace legacy `checkNonTagItemExist()` + `updateNTData('+')` + manual INSERT blocks
> with a single `InventoryService::inward()` call for nontag stock creation.

## Metadata
- **Pattern ID**: MIG-PACKET-001
- **Severity**: HIGH
- **Modules Affected**: nontag, purchase, billing, tagging, metal_process, purchase_approval, branch_transfer
- **Auto-fixable**: No (each call site has different variable names and context)

## Client Scope
- **Applies to**: ALL (any client migrating from pool to packet inventory)
- **Reason**: All clients use the same legacy pool pattern — migration is universal

## Created By
- **Developer**: AI (Antigravity)
- **Client**: coswan (first migration)
- **Date**: 2026-05-12
- **Source Bug ID**: N/A (migration, not bug fix)

## Symptom
Legacy pool-based nontag stock tracking uses `ret_nontag_item` (aggregate pool) with 3 operations per inward:
1. `checkNonTagItemExist()` → does row exist?
2. `updateNTData('+')` or `insertData(ret_nontag_item)` → update/create pool row
3. `insertData(ret_nontag_item_log)` + `insertData(ret_section_nontag_item_log)` → audit log

This must be replaced with a single `InventoryService::inward()` call that creates a packet + logs automatically.

## Root Cause
Architecture migration — pool aggregation replaced by packet-based tracking for:
- Per-unit traceability (each receipt = separate packet)
- Support for length-based products
- Unified inventory API across all modules

## Detection
```bash
# Find all INWARD ('+') pool calls in active controllers
grep -rn "updateNTData.*'+'" admin/application/controllers/ | grep -v "_14_07_2025\|_18_07_2025\|\.bak"

# Find all checkNonTagItemExist calls
grep -rn "checkNonTagItemExist" admin/application/controllers/ | grep -v "_14_07_2025\|_18_07_2025\|\.bak"
```

## Files
Any controller that does `updateNTData($data, '+')` — typical files:
- `admin/application/controllers/admin_ret_purchase.php` (nontag_receipt, PO save, cancel reverse)
- `admin/application/controllers/admin_ret_purchase_approval.php` (approval ×3)
- `admin/application/controllers/admin_ret_tagging.php` (untag, retag)
- `admin/application/controllers/admin_ret_stock_issue.php` (stock return)
- `admin/application/controllers/admin_ret_metal_process.php` (metal return ×3)
- `admin/application/controllers/admin_ret_section_transfer.php` (transfer destination)
- `admin/application/controllers/admin_ret_brntransfer.php` (BT receive)
- `admin/application/controllers/admin_ret_billing.php` (bill cancel, bill delete)

## Fix

### Before (Legacy Pool — INWARD)
```php
// --- BLOCK START: Legacy pool inward ---
$existData = array(
    'id_section'    => $addData['id_section'],
    'id_product'    => $addData['id_product'],
    'design'        => $addData['id_design'],
    'id_branch'     => 1,
    'id_sub_design' => $addData['id_sub_design']
);
$isExist = $this->$model->checkNonTagItemExist($existData);
if ($isExist['status'] == TRUE) {
    $nt_data = array(
        'id_nontag_item' => $isExist['id_nontag_item'],
        'no_of_piece'    => ($addData['nt_pcs'] != '' ? $addData['nt_pcs'] : 0),
        'gross_wt'       => $addData['nt_grswt'],
        'net_wt'         => $addData['nt_netwt'],
        'less_wt'        => 0,
        'updated_by'     => $this->session->userdata('uid'),
        'updated_on'     => date('Y-m-d H:i:s'),
    );
    $update_nt = $this->$model->updateNTData($nt_data, '+');
} else {
    $non_tag_data_insert = array(
        'branch'        => 1,
        'id_section'    => $addData['id_section'],
        'product'       => $addData['id_product'],
        'design'        => $addData['id_design'],
        'id_sub_design' => $addData['id_sub_design'],
        'no_of_piece'   => ($addData['nt_pcs'] != '' ? $addData['nt_pcs'] : 0),
        'gross_wt'      => $addData['nt_grswt'],
        'net_wt'        => $addData['nt_netwt'],
        'created_on'    => date("Y-m-d H:i:s"),
        'created_by'    => $this->session->userdata('uid')
    );
    $ins_id = $this->$model->insertData($non_tag_data_insert, 'ret_nontag_item');
}

// Manual log inserts (also removed)
$non_tag_data = array(...);
$this->$model->insertData($non_tag_data, 'ret_nontag_item_log');

$item_non_tag_data = array(...);
$this->$model->insertData($item_non_tag_data, 'ret_section_nontag_item_log');
// --- BLOCK END ---
```

### After (Packet — INWARD)
```php
// [PACKET-MIGRATION] InventoryService creates packet + logs automatically
$this->load->library('inventory/InventoryService');
$this->inventoryservice->inward('nontag', [
    'product'       => $addData['id_product'],
    'design'        => $addData['id_design'],
    'id_sub_design' => $addData['id_sub_design'],
    'branch'        => $addData['id_branch'],
    'id_section'    => $addData['id_section'],
    'gwt'           => ($addData['nt_grswt'] != '' ? $addData['nt_grswt'] : 0),
    'nwt'           => ($addData['nt_netwt'] != '' ? $addData['nt_netwt'] : 0),
    'pcs'           => ($addData['nt_pcs'] != '' ? $addData['nt_pcs'] : 0),
    'less_wt'       => ($addData['nt_lesswt'] != '' ? $addData['nt_lesswt'] : 0),
    'length'        => isset($addData['length_value']) ? $addData['length_value'] : null,
    'source'        => 'NONTAG_RECEIPT',  // ← Change per screen
    'txn_ref_id'    => $insOrder,          // ← Change per screen (the transaction ID)
    'txn_ref_table' => 'ret_nontag_receipt', // ← Change per screen
    'txn_date'      => $bill_date,
    'user_id'       => $this->session->userdata('uid'),
]);
```

### Field Mapping (CRITICAL — use exact names)

| Legacy Key | InventoryService Key | Notes |
|------------|---------------------|-------|
| `id_product` / `product_id` | `product` | — |
| `id_design` / `design_id` | `design` | — |
| `id_sub_design` | `id_sub_design` | Same |
| `id_branch` / `branch` / hardcoded `1` | `branch` | **REQUIRED** |
| `id_section` | `id_section` | — |
| `nt_grswt` / `gross_wt` | `gwt` | **NOT** `gross_wt` |
| `nt_netwt` / `net_wt` | `nwt` | **NOT** `net_wt` |
| `nt_pcs` / `no_of_piece` / `piece` | `pcs` | **NOT** `no_of_piece` |
| `nt_lesswt` / `less_wt` | `less_wt` | Same |

### Source Labels (use per screen)

| Screen | source | txn_ref_table |
|--------|--------|---------------|
| Nontag Receipt | `NONTAG_RECEIPT` | `ret_nontag_receipt` |
| Purchase Approval | `PURCHASE_APPROVAL` | `ret_purchase_approval` |
| Untag/Retag | `TAGGING` | `ret_taging` |
| Stock Return | `STOCK_RETURN` | `ret_stock_issue` |
| Metal Return | `METAL_RETURN` | `ret_metal_process` |
| Section Transfer (dest) | `SECTION_TRANSFER_IN` | `ret_section_transfer` |
| BT Receive | `BT_RECEIVE` | `ret_branch_transfer` |
| Bill Cancel | `BILL_CANCEL` | `ret_billing` |
| Bill Delete | `BILL_DELETE` | `ret_billing` |
| PO Cancel Reverse | `PO_CANCEL_REVERSE` | `ret_purchase_order` |

## Verification
1. Save a transaction in the migrated screen
2. Check `ret_stock_packet` — new row with `orig_*` values matching input
3. Check `ret_stock_packet_log` — new log entry with `movement_type = 'IN'`
4. Check old `ret_nontag_item` — should NOT be modified (pool abandoned)
5. Verify JSON response returns success (no PHP Fatal errors)
6. Check browser console — no "Unexpected end of JSON input" errors

## Notes
- **`$this->load->library('inventory/InventoryService');`** can be called multiple times safely (CI caches it)
- Variable names differ per screen — you must map them manually (see Field Mapping table)
- The `length` key is optional — pass `null` for non-length products (safe to include always)
- If the screen has both a `checkNonTagItemExist + updateNTData('+')` block AND manual `insertData` calls to log tables, remove ALL of them — InventoryService handles everything
- The closing `}` brace count must match — verify the `if($insOrder)` or enclosing block structure after replacement
