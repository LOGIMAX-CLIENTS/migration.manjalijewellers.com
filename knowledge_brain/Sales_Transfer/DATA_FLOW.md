# Sales Transfer Module — Data Flow

> **Module**: Sales Transfer
> **Last Updated**: 2026-03-20 — Round 1

---

## Flow 1: Sales Transfer REQUEST (CREATE — bill_type=13)

### Trigger
User clicks **Save** button on `sales_trasnfer.php` with `sales_transfer_item_type = 1` (Sales Transfer Request)

### JS Flow
1. `$('#sales_trans_submit').on('click')` — L2265
2. Validates: `validateSalesRequestRow()` — L2233 (checks purchase cost not empty)
3. Validates: metal selection if `is_metal_for_billing=1` — L2278
4. Collects checked rows from `#bt_search_list` — L2300-2350
5. Builds `req_data[]` array with: `tag_id, piece, gross_wt, less_wt, net_wt, metal_code, product_id, design_id, calculation_based_on, purity, id_metal, item_cost, calc_type, rate_per_grm`
6. Calls `create_sales_transfer(req_data, item_cost)` — L2356

### AJAX Call
```
POST admin_ret_sales_transfer/create_sales_transfer
Params: from_brn, to_brn, req_data[], tot_bill_amount, form_secret, id_metal, remark
```

### Controller Flow — `create_sales_transfer()` L84-214
1. Read POST inputs: `from_brn`, `to_brn`, `tot_bill_amount`, `req_data`, `form_secret`, `id_metal`, `remark`
2. Fetch metal rates: `ret_billing_model->get_branchwise_rate($from_branch)` — L100
3. Fetch branch details (from + to): `get_branch_details()` — L101-102
4. Check `is_metal_for_billing` setting → fetch metal details if needed — L103-106
5. **Force `$tot_bill_amount = 0`** — L109 (⚠️ overrides POST value)
6. Generate bill number: `code_number_generator($from_branch, $id_metal, 1)` — L110
7. Get day-closing date: `getBranchDayClosingData($from_branch)` — L111
8. Get financial year: `get_FinancialYear()` — L112
9. Determine bill_date: if day-closing date = today → use datetime, else use day-closing date — L113
10. Generate reference number: `generateRefNo($from_branch, 'sales_ref_no', $id_metal, 1)` — L114

### DB Writes (inside `trans_begin/trans_commit`)
```
1. INSERT ret_billing (bill_type=13, billing_for=3, is_credit=1, credit_status=2)
   → Returns $insId

2. FOREACH req_data:
   a. Calculate taxable_amt:
      - calc_type=1: gross_wt × rate_per_grm
      - calc_type=2: piece × rate_per_grm
   b. Calculate tax: (taxable_amt × 3) / 100
   c. GST split: same country + same state → SGST/CGST (50/50), else → IGST
   d. INSERT ret_bill_details (bill_id=$insId, bill_type=2)
   e. UPDATE ret_taging SET tag_status=4 WHERE tag_id=X
   f. INSERT ret_taging_status_log (status=11)
   g. Accumulate $tot_bill_amount

3. UPDATE ret_billing SET tot_bill_amount WHERE bill_id=$insId
```

### Response
```json
{"status": true, "id": "{insId}", "message": "Sales Trasnfer Added Successfully.."}
```

### JS Post-Success
1. Show toaster success — L2474
2. Open invoice in new tab: `admin_ret_billing/billing_invoice/{id}` — L2476
3. Reload page — L2480

---

## Flow 2: Sales Transfer DOWNLOAD (APPROVE)

### Two Sub-Modes
- **Batch mode** (`sales_trans_dnload=1`): Select bills → approve all at once
- **Scan mode** (`sales_trans_dnload=2`): Scan individual tags one-by-one

### Flow 2a: Batch Download

#### Trigger
User selects `sales_transfer_item_type = 2`, searches bills, selects checkboxes, clicks Save

#### JS Flow
1. `$('#sales_trans_submit').on('click')` — L2265 with `trans_type=2`
2. Collects checked `bill_id[]` from `#bt_search_download_list`
3. Calls `update_sales_transfer_request(req_data)` — L2422

#### AJAX Call
```
POST admin_ret_sales_transfer/update_sales_transfer_request
Params: from_brn, to_brn, req_data[{bill_id}], bill_no, form_secret
```

#### Controller Flow — `update_sales_transfer_request()` L217-283
1. Get all branch day-closing data — L226
2. Find from_branch and to_branch entry dates — L230-240
3. **Validate**: to_branch entry_date >= from_branch entry_date → error if not — L242-247
4. FOREACH bill_id:
   a. `trans_begin()`
   b. UPDATE `ret_billing` SET `download_date`, `download_by`
   c. Get bill tag details: `getSalesTrans_Tag($bill_id)` (tags with `tag_status=4`)
   d. FOREACH tag:
      - UPDATE `ret_taging` SET `tag_status=0`, `current_branch=$to_brn`
      - INSERT `ret_taging_status_log` (status=0, from_branch, to_branch)

### Flow 2b: Scan Download

#### Trigger
Tag is scanned in `#scan_tag_no` input field (Enter key)

#### JS Flow
1. `$(document).on('keyup', '#scan_tag_no')` — L3462
2. Checks localStorage for already-scanned tags
3. Calls `getscan_TagSearchList(tag_code)` — L3510
4. AJAX to `admin_ret_sales_transfer/sales_transfer/getTagsByFilter` → find matching tag
5. **Immediately** fires another AJAX to `admin_ret_sales_transfer/update_TagScan` — L3700
6. Shows scanned tag in `#bill_dwnload_list`
7. If `actual_pcs == tagDet` (all tags scanned) → shows "completed", reloads page

#### Controller Flow — `update_TagScan()` L450-516
1. Validate day-closing dates (same as batch mode)
2. UPDATE `ret_taging` SET `tag_status=0`, `current_branch=$to_brn`
3. INSERT `ret_taging_status_log` (status=0)
4. Check if all tags downloaded: `get_TagBilledPcs($bill_id)` vs `$actual_pcs`
5. If all done → UPDATE `ret_billing` SET `download_date`, `download_by`
6. Return `"completed"` or `"Tag Updated Successfully"`

---

## Flow 3: Sales Return Transfer REQUEST (CREATE — bill_type=14)

### Trigger
User clicks **Save** on `sales_ret_transfer.php` with `sales_ret_transfer_item_type = 1`

### Two Sub-Variants
- **Against Bill** (`aganist_bill=1`): Return against specific sales transfer bill → sends `cat_id` array
- **Not Against Bill** (`aganist_bill=0`): Return any tags with `tag_status=6` → sends `bill_det_id, bill_id, tag_id` array

### JS Flow (Against Bill)
1. `$('#sales_ret_trans_submit').on('click')` — L3042
2. Collects checked `cat_id[]` from `#bt_search_list` — L3070-3094
3. Calls `create_sales_ret_transfer(req_data, item_cost)` — L3098

### AJAX Call
```
POST admin_ret_sales_transfer/create_sales_ret_transfer
Params: from_brn, to_brn, req_data[], tot_bill_amount, bill_no, fin_year_code, form_secret
```

### Controller Flow — `create_sales_ret_transfer()` L287-380
1. FOREACH category in req_data:
   a. Get category details (to find metal code): `get_category_details()` — L305
   b. Generate bill number with category's metal: `code_number_generator()` — L306
   c. Get original bill_id: `getBillId($to_brn, $sales_bill_no, $fin_year_code)` — L309
   d. Generate ref number: `generateRefNo($from_branch, 's_ret_refno', metal, 1)` — L311

### DB Writes (per category)
```
1. trans_begin()
2. INSERT ret_billing (bill_type=14, ref_bill_id=original_bill_id, remark='SALES RETURN TRASNFER')
   → Returns $insId

3. Get tag details: get_sales_return_req_tag_details($cat_id, $to_brn, $bill_id, $from_branch)
   - Finds tags with tag_status=0 in the original transfer bill

4. FOREACH tag detail:
   a. Accumulate $tot_bill_amount from item_cost
   b. INSERT ret_bill_return_details (bill_id=$insId, ret_bill_id, ret_bill_det_id)
   c. IF tag_status=0:
      - UPDATE ret_taging SET tag_status=4
      - INSERT ret_taging_status_log (status=12)

5. UPDATE ret_billing SET tot_bill_amount = -$tot_bill_amount (NEGATIVE!)
```

---

## Flow 4: Sales Return Transfer DOWNLOAD (APPROVE)

### Same two sub-modes as Flow 2 (Batch vs Scan)

### Flow 4a: Batch Download

#### Controller Flow — `update_sales_ret_transfer()` L383-447
1. Get `bill_id` from `getBillId()` — L395
2. **⚠️ BUG**: UPDATE `ret_billing` using undefined `$tb_entry_date` — L397
3. FOREACH req_data items:
   a. Get return tag details: `get_sales_return_tag_details()` — L401
   b. FOREACH tag:
      - IF `tag_status=4`: UPDATE `ret_taging` SET `tag_status=0`, `current_branch=$to_brn` + log
      - IF `tag_status=6`: UPDATE `ret_taging` SET `current_branch=$to_brn` (no status change)

### Flow 4b: Scan Download — `update_ret_TagScan()` L522-589
Same pattern as Flow 2b but uses `ref_bill_id` for piece count verification.

---

## JS Function Map (Key Functions)

| Function | Lines | Purpose |
|---|---|---|
| `getBTBranches()` | L287-509 | Load branches respecting GST filtering + logged-in branch |
| `get_metal_rates_by_branch()` | L2518-2554 | Fetch metal rates (cross-module) |
| `get_ActiveCategory()` | L2964-3016 | Fetch categories (cross-module) |
| `get_ActiveMetals()` | L2902-2946 | Fetch metals (cross-module) |
| `get_received_lots()` | L217-281 | Fetch lots by branch (cross-module) |
| `getSearchProd()` | L93-153 | Autocomplete product search (cross-module) |
| `getSearchDesign()` | L155-205 | Autocomplete design search (cross-module) |
| `get_sales_transfer_tag_list()` | L1885-2083 | Fetch available tags for transfer request |
| `get_sales_transfer_approval_list()` | L1515-1667 | Fetch pending bills for download |
| `get_sales_return_transfer_tag_list()` | L1673-1881 | Fetch tags for return request |
| `get_sales_return_approval_list()` | L1311-1467 | Fetch pending return bills for download |
| `calculateSaleBillRowTotal()` | L2105-2165 | Per-row calculation (taxable + 3% tax) |
| `calculate_sales_trans_details()` | L2183-2227 | Aggregate totals across all rows |
| `validateSalesRequestRow()` | L2233-2259 | Validate purchase cost on checked rows |
| `create_sales_transfer()` | L2444-2512 | POST: create sales transfer request |
| `update_sales_transfer_request()` | L2564-2630 | POST: approve/download sales transfer |
| `create_sales_ret_transfer()` | L3238-3318 | POST: create return transfer request |
| `update_sales_ret_transfer_request()` | L3324-3382 | POST: approve/download return transfer |
| `bill_download_by_scan()` | L3394-3458 | Initialize scan-based download UI |
| `getscan_TagSearchList()` | L3588-3836 | Search + auto-download tags by scan |
| `ret_bill_download_by_scan()` | L3850-3924 | Initialize scan-based return download UI |
| `get_retscan_TagSearchList()` | L4012-4250 | Search + auto-download return tags by scan |
| `remove_sales_trans_row()` | L2171-2179 | Remove a tag row from the table |
