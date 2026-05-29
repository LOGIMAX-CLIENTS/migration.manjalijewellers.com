# FORENSIC TEMPLATE — Other Inventory
> **Module:** Other Inventory | Use this as an investigation cheat sheet when debugging.

---

## Layer 1 — Symptom Collection

### Symptom Checklist
- [ ] Item not showing in dropdown / list
- [ ] Stock count wrong (over/under)
- [ ] Purchase qty doesn't match tagged pieces
- [ ] Issue not reflecting in stock report
- [ ] Image not uploading / not showing
- [ ] QR code not generating or printing blank
- [ ] Gift scheme not saving
- [ ] SKU ID duplicate error on save
- [ ] Category delete doesn't work (item in use)
- [ ] Product mapping not appearing after save
- [ ] Reorder report showing wrong values
- [ ] Purchase cancel not working
- [ ] Size not appearing in dropdown

---

## Layer 2 — Reproduce & Isolate

### Step-by-step Checklist
1. Identify which sub-module is affected: Item Master / Category / Purchase / Tagging / Issue / Stock / Size / Mapping?
2. Which operation: Create / Read / Update / Delete?
3. Is the error visible (flash message / JS alert) or silent (data saved wrong)?
4. Which branch is the user on? (Check `id_branch` session)
5. Is day closing active? → Check `ret_day_closing.is_day_closed`

### Isolation Questions
- Is the issue only on specific branch or all branches?
- Does it happen only after day closing?
- Does it happen for specific item types (billable vs free)?
- What is the `purchase_bill_status` of affected purchase? (1=active, 2=cancelled)

---

## Layer 3 — Client-Side Trace

### Console Checks
```javascript
// Check which page/sub-module is active
console.log(ctrl_page);  // e.g., ["index.php", "other_inventory", "list"]

// Check loaded data
console.log(other_inventory_ref_no);  // purchase ref list
console.log(inventory_item);          // items loaded for product tagging
console.log(img_resource);            // base64 images for purchase

// Check form hidden fields
console.log($('#table_length').val());  // scheme rows count
console.log($('#id_branch').val());     // current branch
```

### Network Tab Checks
- Check POST payload of AJAX calls: are `id_other_item`, `id_branch`, `from_date`, `to_date` populated?
- Check response from `/other_inventory/ajax` — look for `list` and `access` keys
- For stock issues: check POST payload to `/stock_details/ajax` — `id_branch` must be set

---

## Layer 4 — Server-Side Trace

| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Item not saved | Controller | `other_inventory('save')` | L129–242 | Check `trans_status()` result; check if `insertData` returned 0 |
| Stock count wrong | Model | `other_inventory_stock()` | L319–388 | Check log status codes; verify `id_branch` filter |
| Issue didn't update stock | Controller | `issue_item('save')` | L909–990 | Check `updateData` on piece records; log insertion |
| Purchase ref not generating | Model | `generatePurNo()` | L76–98 | Check MAX query returns correct value |
| Tag print blank | Controller | `other_inventory_print()` | L1295–1335 | Verify `get_other_inventory_print()` returns data |
| Image not saving | Controller | `set_image_other()` | L36–54 | Check `upload_img()` return; verify path created |
| Gift not saving | Controller | `other_inventory('save')` | L165–208 | Check `table_length` POST value is set; verify loop bounds |

### Add Diagnostic Prints
```php
// After any model call
print_r($this->db->last_query()); exit;  // See the actual SQL

// Check transaction status
var_dump($this->db->trans_status()); exit;

// Check POST data
print_r($_POST); exit;
print_r($_FILES); exit;
```

---

## Layer 5 — Database Verification

### Check Stock Consistency
```sql
-- Compare log balance vs piece-level balance
SELECT 
    i.id_other_item, i.name,
    IFNULL(SUM(IF(l.status=0, l.no_of_pieces, -l.no_of_pieces)),0) AS log_balance,
    IFNULL(SUM(IF(d.status=0, d.piece, 0)),0) AS piece_balance
FROM ret_other_inventory_item i
LEFT JOIN ret_other_inventory_purchase_items_log l ON l.item_id = i.id_other_item
LEFT JOIN ret_other_inventory_purchase_items_details d ON d.other_invnetory_item_id = i.id_other_item
GROUP BY i.id_other_item
HAVING log_balance != piece_balance;
```

### Check Purchase vs Tagged Balance
```sql
SELECT 
    pi.otr_inven_pur_id, pi.inv_pur_itm_itemid,
    pi.inv_pur_itm_qty AS purchased,
    COUNT(pd.pur_item_detail_id) AS tagged,
    (pi.inv_pur_itm_qty - COUNT(pd.pur_item_detail_id)) AS balance
FROM ret_other_inventory_purchase_items pi
LEFT JOIN ret_other_inventory_purchase_items_details pd ON pd.inv_pur_itm_id = pi.inv_pur_itm_id
WHERE pi.otr_inven_pur_id = {PURCHASE_ID}
GROUP BY pi.inv_pur_itm_id;
```

### Check Issue History for Item
```sql
SELECT i.id_inventory_issue, i.issue_date, i.no_of_pieces, 
       i.id_branch, i.bill_id, i.remarks,
       COUNT(d.pur_item_detail_id) AS actual_pieces_issued
FROM ret_other_invnetory_issue i
LEFT JOIN ret_other_inventory_purchase_items_details d ON d.id_inventory_issue = i.id_inventory_issue
WHERE i.id_other_item = {ITEM_ID}
GROUP BY i.id_inventory_issue;
```

### Check Orphan Pieces (no parent item)
```sql
SELECT pd.pur_item_detail_id, pd.inv_pur_itm_id, pd.other_invnetory_item_id
FROM ret_other_inventory_purchase_items_details pd
LEFT JOIN ret_other_inventory_purchase_items pi ON pi.inv_pur_itm_id = pd.inv_pur_itm_id
WHERE pi.inv_pur_itm_id IS NULL;
```

### Check Available Stock by Branch
```sql
SELECT i.name, d.current_branch, b.name as branch_name,
       SUM(d.piece) as available_pcs, SUM(d.amount) as available_amt
FROM ret_other_inventory_purchase_items_details d
LEFT JOIN ret_other_inventory_item i ON i.id_other_item = d.other_invnetory_item_id
LEFT JOIN branch b ON b.id_branch = d.current_branch
WHERE d.status = 0
GROUP BY d.other_invnetory_item_id, d.current_branch;
```

---

## Layer 6 — Root Cause Classification

| Category | Examples | Risk | Fix Pattern |
|---|---|---|---|
| SQL Injection | Any raw query with `$data[...]` interpolated | 🔴 CRITICAL | Use CI query builder or prepared statements |
| Transaction Missing | `product_details/save` has no trans_begin | 🔴 HIGH | Add trans_begin before first insert |
| Data Loss on Edit | `stock_id_uom`, `issue_to` not in UPDATE | 🟠 MEDIUM | Add missing fields to update $data array |
| CSRF — GET Deletes | DELETE via GET request | 🔴 HIGH | Convert to POST with CSRF token |
| Race Condition — Ref No | MAX() based sequential generation | 🟠 MEDIUM | Use AUTO_INCREMENT or table-level locking |
| Orphan Records | DELETE item doesn't clean related tables | 🟠 MEDIUM | Add cascaded deletes |
| Dead Code | `updateBatchData` has print_r+exit | 🔴 CRITICAL | Remove the debug lines |
| Logic Bug | gift_mapping writes before trans_begin in issue | 🔴 HIGH | Move writes inside transaction |

---

## Layer 7 — Stock Integrity Trace (Specialized)

For any "stock not matching" bug:

1. **Get all log entries for the item + branch:**
   ```sql
   SELECT * FROM ret_other_inventory_purchase_items_log 
   WHERE item_id = {ITEM_ID} AND (to_branch = {BRANCH} OR from_branch = {BRANCH})
   ORDER BY id_item_log;
   ```

2. **Get all piece-level records:**
   ```sql
   SELECT pur_item_detail_id, status, current_branch, id_inventory_issue 
   FROM ret_other_inventory_purchase_items_details
   WHERE other_invnetory_item_id = {ITEM_ID}
   ORDER BY pur_item_detail_id;
   ```

3. **Cross-check:** Log balance (status-based SUM) should equal piece count (status=0 for available).
4. **Common mismatch causes:** 
   - Issue committed but log insert failed (outside transaction)
   - Cancelled purchase pieces still counted as tagged
   - Pieces transferred via Branch Transfer not reflected in log (separate table)

---

## Layer 8 — Print/ZPL Trace (Specialized)

For QR/tag print issues:

1. Check `item_ref_no` exists in `ret_other_inventory_purchase_items_details`
2. Verify `other_inventory_print($ref_no)` → `get_other_inventory_print($ref_no)` returns rows
3. Check `get_tag_code($d, $no)` — position slots are 1, 2, 3 (3 tags per row at x=170, 590, 1000)
4. ZPL generated: Content-Type=text/plain, Content-Disposition=attachment (.prn file)
5. **If blank output:** Check if data array is empty — ref_no not matching any piece
6. **If print fails:** Check that `downloadFile()` headers are not corrupted by any prior output
