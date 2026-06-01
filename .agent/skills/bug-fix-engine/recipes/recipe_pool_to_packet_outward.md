# Pool-to-Packet Migration: OUTWARD Pattern

> Replace legacy `checkNonTagItemExist()` + `updateNTData('-')` + manual INSERT blocks
> with a single `InventoryService::outward()` call for nontag stock deduction.

## Metadata
- **Pattern ID**: MIG-PACKET-002
- **Severity**: HIGH
- **Modules Affected**: nontag, billing, eda, stock_issue, metal_process, section_transfer, branch_transfer, purchase
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
Legacy pool-based nontag stock deduction uses `ret_nontag_item` (aggregate pool) with:
1. `checkNonTagItemExist()` → find the pool row
2. `updateNTData('-')` → subtract pcs/weight from pool
3. `insertData(ret_nontag_item_log)` → audit log

This must be replaced with a single `InventoryService::outward()` call that deducts from a packet + logs automatically.

## Root Cause
Architecture migration — pool aggregation replaced by packet-based tracking.
Outward operations deduct from specific packets (`bal_pcs`, `bal_gross_wt`, `bal_net_wt`, `bal_length`).

## Detection
```bash
# Find all OUTWARD ('-') pool calls in active controllers
grep -rn "updateNTData.*'-'" admin/application/controllers/ | grep -v "_14_07_2025\|_18_07_2025\|\.bak"

# Count per controller
grep -c "updateNTData.*'-'" admin/application/controllers/admin_ret_billing.php
grep -c "updateNTData.*'-'" admin/application/controllers/admin_ret_eda.php
grep -c "updateNTData.*'-'" admin/application/controllers/admin_ret_stock_issue.php
grep -c "updateNTData.*'-'" admin/application/controllers/admin_ret_metal_process.php
grep -c "updateNTData.*'-'" admin/application/controllers/admin_ret_purchase.php
```

## Files
Any controller that does `updateNTData($data, '-')` — typical files:
- `admin/application/controllers/admin_ret_billing.php` (b2c_save, b2b_save, credit_save ×2)
- `admin/application/controllers/admin_ret_eda.php` (EDA approval)
- `admin/application/controllers/admin_ret_stock_issue.php` (stock issue)
- `admin/application/controllers/admin_ret_metal_process.php` (metal issue)
- `admin/application/controllers/admin_ret_section_transfer.php` (transfer source deduct)
- `admin/application/controllers/admin_ret_brntransfer.php` (BT send deduct)
- `admin/application/controllers/admin_ret_purchase.php` (purchase return, purchase cancel)

## Fix

### Before (Legacy Pool — OUTWARD)
```php
// --- BLOCK START: Legacy pool outward ---
$existData = array(
    'id_product' => $est['product_id'],
    'id_design'  => $est['design_id'],
    'id_branch'  => $est['id_branch']
);
$isExist = $this->$model->checkNonTagItemExist($existData);
if ($isExist['status'] == TRUE) {
    $nt_data = array(
        'id_nontag_item' => $isExist['id_nontag_item'],
        'no_of_piece'    => $est['piece'],
        'gross_wt'       => $est['gross_wt'],
        'net_wt'         => $est['net_wt'],
        'less_wt'        => $est['less_wt'],
        'updated_by'     => $this->session->userdata('uid'),
        'updated_on'     => date('Y-m-d H:i:s'),
    );
    $this->$model->updateNTData($nt_data, '-');

    // Manual log insert (also removed)
    $non_tag_data = array(
        'from_branch' => $est['id_branch'],
        'to_branch'   => NULL,
        'no_of_piece' => $est['piece'],
        'gross_wt'    => $est['gross_wt'],
        'net_wt'      => $est['net_wt'],
        'product'     => $est['product_id'],
        'design'      => $est['design_id'],
        'date'        => $est['est_date'],
        'created_on'  => date("Y-m-d H:i:s"),
        'created_by'  => $this->session->userdata('uid'),
        'status'      => 10,
        'bill_id'     => $esti_id
    );
    $this->$model->insertData($non_tag_data, 'ret_nontag_item_log');
}
// --- BLOCK END ---
```

### After (Packet — OUTWARD)
```php
// [PACKET-MIGRATION] InventoryService deducts from packet + logs automatically
$this->load->library('inventory/InventoryService');
$this->inventoryservice->outward('nontag', [
    'product'       => $est['product_id'],
    'design'        => $est['design_id'],
    'id_sub_design' => isset($est['id_sub_design']) ? $est['id_sub_design'] : null,
    'branch'        => $est['id_branch'],
    'id_section'    => isset($est['id_section']) ? $est['id_section'] : null,
    'gwt'           => ($est['gross_wt'] != '' ? $est['gross_wt'] : 0),
    'nwt'           => ($est['net_wt'] != '' ? $est['net_wt'] : 0),
    'pcs'           => ($est['piece'] != '' ? $est['piece'] : 0),
    'less_wt'       => isset($est['less_wt']) ? $est['less_wt'] : 0,
    'length'        => isset($est['length_value']) ? $est['length_value'] : null,
    'source'        => 'EDA_APPROVAL',     // ← Change per screen
    'txn_ref_id'    => $esti_id,            // ← Change per screen
    'txn_ref_table' => 'ret_estimation',    // ← Change per screen
    'txn_date'      => $est['est_date'],    // ← Change per screen
    'user_id'       => $this->session->userdata('uid'),
]);
```

### Field Mapping (CRITICAL — use exact names)

| Legacy Key | InventoryService Key | Notes |
|------------|---------------------|-------|
| `product_id` / `id_product` | `product` | — |
| `design_id` / `id_design` | `design` | — |
| `id_sub_design` | `id_sub_design` | May not exist in all screens |
| `id_branch` / `branch` | `branch` | **REQUIRED** |
| `id_section` | `id_section` | May not exist — OK to pass null |
| `gross_wt` / `nt_grswt` | `gwt` | **NOT** `gross_wt` |
| `net_wt` / `nt_netwt` | `nwt` | **NOT** `net_wt` |
| `piece` / `no_of_piece` / `nt_pcs` | `pcs` | **NOT** `no_of_piece` |
| `less_wt` / `nt_lesswt` | `less_wt` | Same |

### Source Labels (use per screen)

| Screen | source | txn_ref_table |
|--------|--------|---------------|
| Billing B2C | `BILLING_B2C` | `ret_billing` |
| Billing B2B | `BILLING_B2B` | `ret_billing` |
| Billing Credit B2C | `BILLING_CREDIT_B2C` | `ret_billing` |
| Billing Credit B2B | `BILLING_CREDIT_B2B` | `ret_billing` |
| EDA Approval | `EDA_APPROVAL` | `ret_estimation` |
| Stock Issue | `STOCK_ISSUE` | `ret_stock_issue` |
| Metal Issue | `METAL_ISSUE` | `ret_metal_process` |
| Section Transfer (source) | `SECTION_TRANSFER_OUT` | `ret_section_transfer` |
| BT Send | `BT_SEND` | `ret_branch_transfer` |
| Purchase Return | `PURCHASE_RETURN` | `ret_purchase_return` |
| Purchase Cancel | `PURCHASE_CANCEL` | `ret_purchase_order` |

## Verification
1. Save a transaction that deducts nontag stock
2. Check `ret_stock_packet` — `bal_*` values decreased by deducted amounts
3. Check `ret_stock_packet_log` — new log entry with `movement_type = 'OUT'`
4. Check old `ret_nontag_item` — should NOT be modified
5. Verify JSON response returns success
6. If no matching packet exists → verify error handling (handler throws Exception)

## Notes
- **Packet selection**: Outward automatically picks the packet to deduct from (FIFO by default in PacketStrategy). The user does NOT pick a packet in most screens — that's only for billing (Phase 5 Packet Picker).
- **Insufficient stock**: If `bal_pcs` or `bal_gross_wt` is less than requested, the handler will throw an Exception. Wrap in try/catch if the screen needs graceful degradation.
- **Multiple items in loop**: If the outward is inside a `foreach` loop (like billing line items), the `load->library()` call is safe to call repeatedly — CI caches it.
- **length key**: Always pass even as `null` — ready for future length-enabled products.

## Error Handling Pattern
```php
try {
    $this->load->library('inventory/InventoryService');
    $this->inventoryservice->outward('nontag', [...]);
} catch (Exception $e) {
    // Log error, rollback transaction, return error to UI
    log_message('error', 'Packet outward failed: ' . $e->getMessage());
    $this->db->trans_rollback();
    echo json_encode(['status' => false, 'message' => 'Insufficient stock']);
    return;
}
```

## Related Recipes
- **MIG-PACKET-001** — Pool-to-Packet INWARD pattern (the complement of this recipe)
- **inventoryservice-field-mapping KI** — Field name contract (gwt/nwt, NOT gross_wt/net_wt)
