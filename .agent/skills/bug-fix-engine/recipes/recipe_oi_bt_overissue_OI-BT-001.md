# Recipe: OI Branch Transfer Over-Issue Fix (OI-BT-001)

**Pattern**: PAT-INV-001  
**Module**: OtherInventory  
**Severity**: P1  
**Track**: A (System)  
**Date Fixed**: 2026-05-06  
**Status**: ✅ Verified

---

## Bug Summary

The `ret_other_inventory_purchase_items_log` table had entries where `no_of_pieces` reflected the **requested** transfer quantity rather than the **actual available stock** at the time of transit approval. This caused the **Stock In/Out** report to show higher totals than the **Available Stock** report permanently.

**Root cause**: No server-side stock validation before the BT save; transit/download log entries used `$items['no_of_pcs']` (from request payload) instead of the actual pieces processed from `ret_other_inventory_purchase_items_details`.

---

## Symptoms

- Available Stock ≠ Stock In/Out report at destination branches
- Difference grows over time as more BTs are approved
- No validation error shown to user when requesting more pcs than available

---

## Detection

### Code-Level
```powershell
# Check if BT handler writes log using request payload
Select-String -Path admin/application/controllers/admin_ret_brntransfer.php `
  -Pattern "no_of_pieces.*items\[.no_of_pcs"

# Check if server-side stock validation exists
Select-String -Path admin/application/controllers/admin_ret_brntransfer.php `
  -Pattern "available_pcs|SUM.*piece.*details"
```

### Data-Level
Deploy `admin/diagnose_oi_bt.php` (see `/fix-oi-bt-overissue` workflow) — it finds all BTs where logged pcs exceed stock at `approved_datetime`.

---

## Fix Applied

### Part 1 — Historical Data Correction
**File**: `ret_other_inventory_purchase_items_log` (database)

```sql
-- UPDATE 22 affected log rows (transit + download entries for 6 BTs)
UPDATE ret_other_inventory_purchase_items_log
SET no_of_pieces = CASE id_item_log
    WHEN 9392 THEN 9    -- BT 00275, FIVE THOUSAND GIFT     (Transit)
    WHEN 9428 THEN 9    -- BT 00275, FIVE THOUSAND GIFT     (Download)
    WHEN 9390 THEN 2    -- BT 00275, ONE THOUSAND GIFT      (Transit)
    WHEN 9426 THEN 2    -- BT 00275, ONE THOUSAND GIFT      (Download)
    WHEN 9393 THEN 11   -- BT 00275, TEN THOUSAND GIFT      (Transit)
    WHEN 9429 THEN 11   -- BT 00275, TEN THOUSAND GIFT      (Download)
    WHEN 9391 THEN 0    -- BT 00275, TWO THOUSAND GIFT      (Transit)
    WHEN 9427 THEN 0    -- BT 00275, TWO THOUSAND GIFT      (Download)
    WHEN 6419 THEN 0    -- BT 00291, FIFTEEN THOUSAND GIF   (Transit)
    WHEN 6423 THEN 0    -- BT 00291, FIFTEEN THOUSAND GIF   (Download)
    WHEN 6418 THEN 0    -- BT 00291, TEN THOUSAND GIFT      (Transit)
    WHEN 6422 THEN 0    -- BT 00291, TEN THOUSAND GIFT      (Download)
    WHEN 6420 THEN 1    -- BT 00291, TWENTY FIVE THOUSAND   (Transit)
    WHEN 6424 THEN 1    -- BT 00291, TWENTY FIVE THOUSAND   (Download)
    WHEN 9727 THEN 0    -- BT 00338, TEN THOUSAND GIFT      (Transit)
    WHEN 9728 THEN 0    -- BT 00338, TEN THOUSAND GIFT      (Download)
    WHEN 9948 THEN 7    -- BT 00347, FIFTEEN THOUSAND GIF   (Transit)
    WHEN 9949 THEN 7    -- BT 00347, FIFTEEN THOUSAND GIF   (Download)
    WHEN 9982 THEN 0    -- BT 00357, ONE THOUSAND GIFT      (Transit)
    WHEN 9984 THEN 0    -- BT 00357, ONE THOUSAND GIFT      (Download)
    WHEN 9983 THEN 0    -- BT 00357, TWENTY FIVE THOUSAND   (Transit)
    WHEN 9985 THEN 0    -- BT 00357, TWENTY FIVE THOUSAND   (Download)
END
WHERE id_item_log IN (
    9392,9428,9390,9426,9393,9429,9391,9427,
    6419,6423,6418,6422,6420,6424,
    9727,9728,9948,9949,9982,9984,9983,9985
);

-- INSERT 3 missing issue log entries at USILAMPATTI
INSERT INTO ret_other_inventory_purchase_items_log
    (item_id, no_of_pieces, date, status, from_branch, to_branch, amount, created_on)
VALUES
    (105, 19, NOW(), 1, (SELECT id_branch FROM branch WHERE name LIKE '%USILAMPATTI%' LIMIT 1), NULL, 0, NOW()),
    (106,  2, NOW(), 1, (SELECT id_branch FROM branch WHERE name LIKE '%USILAMPATTI%' LIMIT 1), NULL, 0, NOW()),
    (107,  1, NOW(), 1, (SELECT id_branch FROM branch WHERE name LIKE '%USILAMPATTI%' LIMIT 1), NULL, 0, NOW());
```

### Part 2 — Code Fix (Server-Side Validation)
**File**: `admin/application/controllers/admin_ret_brntransfer.php`

Added stock availability check before `trans_begin()` in the packaging item (type=4) BT save handler (~line 204):

```php
// SERVER-SIDE STOCK VALIDATION
$from_branch  = (int)$_POST['transfer_from'];
$stock_errors = [];
foreach ($_POST['trans_data'] as $pack_items) {
    $item_id   = (int)$pack_items['id_other_item'];
    $requested = (int)$pack_items['no_of_pcs'];
    $avail_q   = $this->db->query(
        "SELECT IFNULL(SUM(piece), 0) as available_pcs
         FROM ret_other_inventory_purchase_items_details
         WHERE other_invnetory_item_id={$item_id}
           AND current_branch={$from_branch} AND status=0"
    );
    $available = (int)$avail_q->row()->available_pcs;
    if ($requested > $available) {
        $stock_errors[] = "Item ID {$item_id}: requested {$requested}, available {$available}";
    }
}
if (!empty($stock_errors)) {
    echo json_encode(['status' => 0, 'message' => 'Insufficient stock. Transfer rejected.']);
    break;
}
```

---

## Key Gotcha

Use **`approved_datetime`** (not `created_time`) as the stock-check cutoff when diagnosing historical data. Items may be tagged at from-branch AFTER BT creation but BEFORE transit approval — those transfers are valid and should not be flagged.

---

## Verification

1. Run `admin/diagnose_oi_bt.php` → 0 over-issued entries
2. USILAMPATTI Available Stock = Stock In/Out ✅
3. THENI Available Stock = Stock In/Out ✅
4. HEAD OFFICE Available Stock = Stock In/Out ✅

---

## Workflow Reference

`/fix-oi-bt-overissue` — contains reusable diagnostic scripts for this exact bug pattern.
