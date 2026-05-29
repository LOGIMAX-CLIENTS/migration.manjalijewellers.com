# Forensic Investigation Template
# Module: Sales Transfer

> **Purpose**: Layer-by-layer investigation cheat sheet for diagnosing bugs in this module.
> **Last Updated**: 2026-03-20 — Round 2 (Refresh)

---

## Layer 1: Symptom Collection

### Common Symptoms in Sales Transfer

| # | Symptom | Likely Layer | Severity |
|---|---|---|---|
| 1 | Wrong total amount on transfer bill | JS calc / Controller calc | P0 |
| 2 | Tag stuck in transit (tag_status=4 permanently) | Controller download flow | P0 |
| 3 | Tag shows in wrong branch after transfer | Controller — `current_branch` not updated | P0 |
| 4 | "Check day closing" error on download | Controller — day-closing date mismatch | P1 |
| 5 | Transfer bill created with 0 amount | Controller L109 — `$tot_bill_amount` reset to 0 | P1 |
| 6 | Duplicate tags in transfer list | Model query — missing GROUP BY or filter | P1 |
| 7 | GST split incorrect (IGST vs SGST/CGST) | Controller — branch country/state comparison | P1 |
| 8 | Scan download doesn't complete | Controller — `actual_pcs` vs `get_TagBilledPcs()` mismatch | P1 |
| 9 | Branch dropdown not loading | JS — `getBTBranches()` AJAX failure | P2 |
| 10 | "No Records Found" when tags exist | Model query — wrong filter conditions | P1 |
| 11 | Page freezes during transfer | JS — `async:false` blocking UI thread | P2 |
| 12 | Return transfer links to wrong original bill | Controller — `getBillId()` returns wrong bill_id | P0 |

### Data to Collect Immediately
- [ ] Screenshot / exact error message
- [ ] URL and route (`sales_transfer/add` or `sales_transfer/ret_add` or approval)
- [ ] From Branch + To Branch
- [ ] Transfer type (request=1 or download=2)
- [ ] Tag codes involved
- [ ] Bill number (if download/approval)
- [ ] Browser console errors (F12 → Console)
- [ ] Network tab response for the AJAX call
- [ ] Financial year selected

---

## Layer 2: Reproduce & Isolate

### Reproduction Checklist
1. [ ] Can you reproduce with the SAME branches + tags?
2. [ ] Can you reproduce with DIFFERENT branches?
3. [ ] Does it happen for all users or specific branch login?
4. [ ] Is `is_metal_for_billing` setting = 1 or 0?
5. [ ] Is `sales_transfer_download` setting = 1 (batch) or 2 (scan)?
6. [ ] Check `loggedInBranch` — is user logged into a specific branch or admin (0)?

### Isolation Questions
- **When did it start?** → Check recent commits to controller/model/JS
- **Branch-specific?** → Check `branch.id_country` and `branch.id_state` for GST logic
- **Financial year?** → Incorrect `fin_year_code` can cause "No Records" on download
- **Day-closing up to date?** → Stale day-closing dates cause date validation errors

---

## Layer 3: Client-Side Trace (JavaScript)

### JS File: `ret_sales_transfer.js`

### Key Console Log Points
```javascript
// 1. On sales transfer save
console.log('ST — Save clicked', {
    trans_type: $("input[name='sales_transfer_item_type']:checked").val(),
    from_brn: $('#from_brn').val(),
    to_brn: $('#to_brn').val(),
    checked_tags: $("input[name='tag_id[]']:checked").length
});

// 2. Before create_sales_transfer AJAX
console.log('ST — AJAX payload:', {
    from_brn: $('#from_brn').val(),
    to_brn: $('#to_brn').val(),
    req_data: req_data,
    tot_bill_amount: item_cost,
    id_metal: $('#select_metal').val()
});

// 3. On calculateSaleBillRowTotal
console.log('ST — Row calc:', {
    piece, gross_wt, calc_type, pur_cost,
    taxable_amt, tax_amount, tot_amount
});
```

### Key Variables to Inspect
| Variable | Where | Expected | How to Check |
|---|---|---|---|
| `branchArr` | `getBTBranches()` | Array of branch objects with `gst_number` | `console.log(branchArr)` |
| `loggedInBranch` | Global | Branch ID or 0 (admin) | `console.log(loggedInBranch)` |
| `ctrl_page` | Global L3 | `['', 'sales_transfer', 'add'/'ret_add']` | `console.log(ctrl_page)` |
| `rate_details` | `get_metal_rates_by_branch()` | Object with `silverrate_1gm`, `goldrate_22ct` | `console.log(rate_details)` |
| `scan_dwload_data` | `bill_download_by_scan()` | Array of tag objects | `console.log(scan_dwload_data)` |

### Network Tab Checks
| Endpoint | Method | Expected Status | Key Params |
|---|---|---|---|
| `sales_transfer/sales_trans_tag` | POST | 200 | `from_brn, tag_code/cat_id` |
| `create_sales_transfer` | POST | 200 | `from_brn, to_brn, req_data[]` |
| `update_sales_transfer_request` | POST | 200 | `from_brn, to_brn, req_data[{bill_id}]` |
| `update_TagScan` | POST | 200 | `bill_id, tag_code, tag_id, from_brn, to_brn` |

---

## Layer 4: Server-Side Trace (PHP)

### Controller: `admin_ret_sales_transfer.php`

### Trace Points Table
| Symptom | File | Method | Line | What to Check |
|---|---|---|---|---|
| Wrong total | Controller | `create_sales_transfer()` | L109 | `$tot_bill_amount` is forcefully set to 0 here — check if it accumulates correctly via L164 |
| Tag not transitioning | Controller | `create_sales_transfer()` | L189 | Check `$tagInsert` is truthy before tag_status update |
| GST split wrong | Controller | `create_sales_transfer()` | L154-163 | Check `$from_branch_details['id_state']` vs `$to_branch_details['id_state']` |
| Download fails silently | Controller | `update_sales_transfer_request()` | L242-246 | Day-closing check returns error but with `trans_rollback()` before `trans_begin()` |
| Undefined variable error | Controller | `update_sales_ret_transfer()` | L397 | `$tb_entry_date` not defined before use |
| Undefined variable error | Controller | `update_sales_ret_transfer()` | L440 | `$insId` not defined in this method |
| **[R2]** Undefined variable error | Controller | `update_sales_transfer_request()` | L277 | `$insId` not defined in this method either — same bug as L440 |
| **[R2]** `$bill_date` is NULL | Controller | `update_sales_transfer_request()` | L227 | `getAllBranchDCData()` returns array-of-arrays, not flat array. `$dCData['entry_date']` resolves to NULL |
| **[R2]** `$bill_date` is NULL | Controller | `update_TagScan()` | L464 | Same `getAllBranchDCData()` array misuse as L227 |
| **[R2]** `$bill_date` is NULL | Controller | `update_ret_TagScan()` | L537 | Same `getAllBranchDCData()` array misuse |
| **[R2]** Return bill has wrong total | Controller | `create_sales_ret_transfer()` | L295,L367 | `$tot_bill_amount` not reset per category — accumulates across category iterations |
| **[R2]** No transaction on return download | Controller | `update_sales_ret_transfer()` | L438 | `trans_status()` called without `trans_begin()` — always returns TRUE |
| Scan not completing | Controller | `update_TagScan()` | L505 | `$actual_pcs == $tagDet` comparison — check types (string vs int) |

### Model: `ret_sales_transfer_model.php`

### Query Investigation Points
| Method | Query Type | Table(s) | Common Issue |
|---|---|---|---|
| `get_sales_transfer_tag_details()` | SELECT | `ret_taging` + 5 JOINs | Missing filters → too many rows returned |
| `get_sales_trans_approval_tag()` | SELECT | `ret_billing` + 2 JOINs | `tag_status=4` filter may miss tags that were partially downloaded |
| `getSalesTrans_Tag()` | SELECT | `ret_billing` + 2 JOINs | Only finds `tag_status=4` — if already downloaded (0), returns empty |
| `getBillId()` | SELECT | `ret_billing` | Returns `$sql->row()->bill_id` — **will fatal error if no row found** |
| `get_TagBilledPcs()` | SELECT | `ret_bill_details` + `ret_taging` | SUM aggregate may return unexpected results with NULL pieces |

---

## Layer 5: Database Verification

### 5a. Pull Complete Transfer Record
```sql
SELECT b.bill_id, b.bill_no, b.bill_type, b.bill_date, b.tot_bill_amount,
       b.from_branch, b.to_branch, b.download_date, b.download_by,
       d.bill_det_id, d.tag_id, d.item_cost, d.total_igst, d.total_sgst, d.total_cgst,
       t.tag_code, t.tag_status, t.current_branch
FROM ret_billing b
LEFT JOIN ret_bill_details d ON d.bill_id = b.bill_id
LEFT JOIN ret_taging t ON t.tag_id = d.tag_id
WHERE b.bill_id = '{BILL_ID}';
```

### 5b. Verify Tag Status Trail
```sql
SELECT tsl.*, t.tag_code, t.tag_status, t.current_branch
FROM ret_taging_status_log tsl
JOIN ret_taging t ON t.tag_id = tsl.tag_id
WHERE tsl.tag_id = '{TAG_ID}'
ORDER BY tsl.created_on DESC;
```

### 5c. Find Tags Stuck in Transit (>7 days)
```sql
SELECT t.tag_id, t.tag_code, t.tag_status, t.current_branch,
       b.bill_id, b.bill_no, b.bill_date, b.from_branch, b.to_branch, b.download_date
FROM ret_taging t
JOIN ret_bill_details d ON d.tag_id = t.tag_id
JOIN ret_billing b ON b.bill_id = d.bill_id
WHERE t.tag_status = 4
AND b.bill_type IN (13, 14)
AND b.download_date IS NULL
AND b.bill_date < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

### 5d. Verify Bill Total vs Detail Sum
```sql
SELECT b.bill_id, b.bill_no, b.tot_bill_amount,
       SUM(d.item_cost) AS calculated_total,
       ABS(b.tot_bill_amount) - SUM(d.item_cost) AS delta
FROM ret_billing b
JOIN ret_bill_details d ON d.bill_id = b.bill_id
WHERE b.bill_type IN (13, 14)
GROUP BY b.bill_id
HAVING ABS(ABS(b.tot_bill_amount) - SUM(d.item_cost)) > 0.01;
```

---

## Layer 6: Root Cause Classification

| Category | Risk | Example |
|---|---|---|
| **JS Calculation Error** | P0 — Financial | Tax amount calculated differently than PHP (rounding, calc_type mismatch) |
| **PHP Undefined Variable** | P0 — Runtime Error | `$insId`, `$tb_entry_date` used before definition in `update_sales_ret_transfer()` |
| **SQL Injection** | P0 — Security | All 17 model methods concatenate user input |
| **Transaction Scope Bug** | P1 — Data Integrity | `trans_begin()` inside loop, `trans_status()` outside loop |
| **Missing Validation** | P1 — Data | Day-closing not checked on request creation |
| **Tag Status Mismatch** | P0 — Inventory | Tag stuck at status=4 due to partial download failure |
| **GST Calculation Error** | P1 — Financial | Wrong country/state comparison → wrong IGST/SGST split |
| **Hardcoded Values** | P2 — Business | 3% tax rate hardcoded instead of settings-driven |

---

## Layer 7: Transaction Integrity (Specialized)

Since this module does financial INSERT transactions into `ret_billing` + `ret_bill_details` + `ret_taging`:

### Transaction Wrapping Check
| Method | `trans_begin()` | `trans_commit()/rollback()` | Issue | Round |
|---|---|---|---|---|
| `create_sales_transfer()` | L137 ✅ | L207/210 ✅ | OK — proper wrapping | R1 |
| `update_sales_transfer_request()` | L250 (inside loop) | L276/279 | ⚠️ Each bill is its own transaction — partial success possible | R1 |
| `create_sales_ret_transfer()` | L333 (inside loop) | L371/375 (outside loop) | ⚠️ Only last iteration's transaction checked | R1 |
| `update_sales_ret_transfer()` | **Missing!** | L438/442 | ⚠️ **[R2]** No `trans_begin()` — `trans_status()` always TRUE, `trans_commit()` commits whatever CI auto-started | R1+R2 |
| `update_TagScan()` | **Missing!** | No explicit trans | ⚠️ No transaction wrapping at all | R1 |
| `update_ret_TagScan()` | **Missing!** | No explicit trans | ⚠️ No transaction wrapping at all | R1 |

### Verification Query: Check for Partial Commits
```sql
-- Bills with tags partially downloaded (some tag_status=0, some tag_status=4)
SELECT b.bill_id, b.bill_no,
       SUM(CASE WHEN t.tag_status = 0 THEN 1 ELSE 0 END) AS downloaded,
       SUM(CASE WHEN t.tag_status = 4 THEN 1 ELSE 0 END) AS pending,
       b.download_date
FROM ret_billing b
JOIN ret_bill_details d ON d.bill_id = b.bill_id
JOIN ret_taging t ON t.tag_id = d.tag_id
WHERE b.bill_type IN (13, 14)
GROUP BY b.bill_id
HAVING downloaded > 0 AND pending > 0;
```

---

## Layer 8: Inventory/Stock Integrity (Specialized)

### Tag Movement Audit
```sql
-- For a specific tag, trace all branch movements
SELECT tsl.tag_id, t.tag_code,
       tsl.status, tsl.from_branch, tsl.to_branch,
       tsl.date, tsl.created_on,
       fb.name AS from_name, tb.name AS to_name
FROM ret_taging_status_log tsl
JOIN ret_taging t ON t.tag_id = tsl.tag_id
LEFT JOIN branch fb ON fb.id_branch = tsl.from_branch
LEFT JOIN branch tb ON tb.id_branch = tsl.to_branch
WHERE tsl.tag_id = '{TAG_ID}'
ORDER BY tsl.created_on ASC;
```

### Branch Stock Consistency Check
```sql
-- Tags marked as current_branch=X but last log shows different branch
SELECT t.tag_id, t.tag_code, t.current_branch,
       (SELECT tsl2.to_branch FROM ret_taging_status_log tsl2
        WHERE tsl2.tag_id = t.tag_id AND tsl2.to_branch IS NOT NULL
        ORDER BY tsl2.created_on DESC LIMIT 1) AS last_logged_branch
FROM ret_taging t
WHERE t.tag_status = 0
HAVING t.current_branch != last_logged_branch;
```
