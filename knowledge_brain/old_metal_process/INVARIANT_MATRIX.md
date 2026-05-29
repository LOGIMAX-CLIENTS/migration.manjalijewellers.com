# Invariant Matrix — Old Metal Process Module

Invariants are conditions that MUST be true before and after every save operation. Any code path that violates these is a defect.

---

## I1: Process Number Uniqueness

**Invariant**: `ret_old_metal_process.process_no` must be unique per (process_for, id_metal_process).  
**Generator**: `generate_process_number()` → MAX + 1 with no transaction lock  
**Violation**: OMP-005 — Race condition allows duplicate `process_no` under concurrent saves  
**Verification Query**:
```sql
SELECT process_no, COUNT(*) FROM ret_old_metal_process 
GROUP BY process_no HAVING COUNT(*) > 1;
```

---

## I2: Pocket Issue Weight Cannot Exceed Available Weight

**Invariant**: For any pocket, `SUM(issue_gwt) <= gross_wt`  
**Enforcement**: JS-only (`blc_weight` cap on frontend)  
**Gap**: No server-side check in controller. A crafted POST can over-issue a pocket  
**Verification Query**:
```sql
SELECT p.pocket_no, p.gross_wt, SUM(md.issue_gwt) as total_issued
FROM ret_old_metal_pocket p
JOIN ret_old_metal_melting_details md ON md.id_pocket = p.id_metal_pocket
GROUP BY p.id_metal_pocket
HAVING total_issued > p.gross_wt;
```

---

## I3: All Category Rows Must Reference Valid id_ret_category

**Invariant**: `ret_old_metal_melting_recd_details.received_category` must exist in `ret_ret_category`  
**Gap**: Controller inserts category data from JSON without FK validation  
**Risk**: Orphaned category entries if JS clears category on client but old JSON is submitted  

---

## I4: Polishing Receipt `created_branch` Must Equal Session Branch

**Invariant**: `ret_lot_inwards.created_branch` = session `id_branch`  
**Violation**: **OMP-028** — Line 1315 uses `$id_branch` (undefined variable, defaults to `NULL` in PHP)  
**Effect**: All polishing receipt lot inward records have `created_branch = NULL`  
**Code**:
```php
'created_branch' => $id_branch,   // ❌ undefined — should be $branchDetails['id_branch']
```

---

## I5: Refining Receipt Non-Tag Stock Must Record Actual Piece Count

**Invariant**: `ret_old_metal_refining_details.no_of_piece` must equal actual pieces received  
**Violation**: **OMP-029** — Controller line 1024 hardcodes `'piece' => 1` for all category rows  
**Effect**: Refining non-tag stock always shows 1 piece per item category regardless of reality  
**Code**:
```php
$cateegoryData = array(
    'piece' => 1,   // ❌ hardcoded — should be $cat['recd_pcs']
    ...
);
```

---

## I6: Payment Amount Must Reference Correct Source Field

**Invariant**: For NB payment, `payment_amount` = `receipt_payment['net_banking_amount']`  
**Violation**: **OMP-003** — Controller line 1494 uses `$receipt_payment['cash_amount']` instead  
**Code**:
```php
'payment_amount' => $receipt_payment['cash_amount'],  // ❌ should be ['net_banking_amount']
```

---

## I7: Testing Receipt Against-Melting MUST Update `melting_status` 

**Invariant**: When testing receipt `against_melting == 2`, `melting_status` on the associated melting_recd row must be set to 3  
**Violation**: Code at lines 744-755 is ENTIRELY COMMENTED OUT — stock log and status update never happen  
**Effect**: Against-melting testing receipts leave all upstream status flags unchanged  

---

## I8: Pocket `trans_type=3` (Non-Tag) Must Store Actual `net_wt`, Not `gross_wt`

**Invariant**: `ret_old_metal_pocket_details.net_wt` = actual net weight (gross minus stone weight)  
**Violation**: Lines 326 & 339 both set `'net_wt' => $val['gross_wt']`  
**Effect**: Non-tag pockets show gross_wt = net_wt, suppressing stone weight deductions  
**Note**: This may be intentional for raw metal (no stone deduction). Needs business clarification (GAP-10)  

---

## I9: Refining Issue Must Lock `melting_status` to Prevent Dual-Issue

**Invariant**: A `melting_recd` row with `melting_status=4` (refining issued) cannot be re-issued  
**Enforcement**: `get_Active_Refining_details()` filters `WHERE melting_status=3` (testing completed)  
**Gap**: No DB-level lock. Between the SELECT and UPDATE, another session could issue the same row  
**Severity**: Concurrency risk, mitigated by low concurrent usage in practice  

---

## I10: GST Split Must Match Company-Karigar State Relationship

**Invariant**: If company and karigar are in same state → CGST+SGST; otherwise → IGST  
**Violation**: **OMP-002** — `get_chg_tax_type()` compares `id_company` to `id_state` directly instead of looking up `company.id_state`  
**Effect**: Tax split always uses wrong logic — could be 100% IGST or CGST+SGST for wrong cases  

---

## Save Operation Invariant Checklist

| Process | Invariant | Status |
|---|---|---|
| Pocket Save | I1 (unique pocket_no) | ⚠️ No DB UNIQUE constraint confirmed |
| Pocket Save | I2 (pcs > 0, wt > 0) | ✅ Controller lines 210-212 check this |
| Melting Issue | I2 (weight not exceeded) | ❌ JS-only, no server check |
| Melting Receipt | I3 (valid category) | ⚠️ No FK check |
| Testing Receipt (Against-Melting) | I7 (status update) | ❌ Commented out |
| Refining Issue | I9 (no dual-issue lock) | ⚠️ Concurrency gap |
| Refining Receipt | I5 (piece count) | ❌ Hardcoded to 1 (OMP-029) |
| Refining Receipt | I10 (correct GST) | ❌ Wrong field (OMP-002) |
| Polishing Receipt | I4 (created_branch) | ❌ Undefined variable (OMP-028) |
| All Save | I6 (NB payment source) | ❌ Uses cash_amount (OMP-003) |
