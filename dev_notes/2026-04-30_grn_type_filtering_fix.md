# GRN Type Filtering Fix — Credit/Debit Entry & Supplier Payment
**Date:** 30-04-2026  
**Developer:** Antigravity  
**Module:** Retail Purchase → Credit/Debit Entry, Supplier Payment  

---

## Problem Statement

GRN type filtering was completely absent from the Credit/Debit Entry and Supplier Payment bill loading logic. This caused:

1. **Smith radio button (Credit/Debit Entry):** Bills never loaded — no handler existed for value=2.
2. **Supplier radio button:** Loaded ALL bills including Smith (grn_type=4) entries that shouldn't appear.
3. **Approvals radio button:** Loaded ALL weight-based bills without restricting to grn_type=2.
4. **Supplier Payment page:** Bill/Receipt radio buttons showed all bills regardless of GRN type, and standalone CR/DR credit notes (created under Smith) appeared under Bill instead of Receipt.

## GRN Type Reference

| grn_type | Prefix | Description        | Used By              |
|----------|--------|--------------------|----------------------|
| 1        | PU-    | GST Purchase/Bill  | Supplier             |
| 2        | PM-    | Job Receipt        | Approvals            |
| 3        | PC-    | Charges            | (excluded from most) |
| 4        | PJ-    | Purchase Job Work  | Smith                |

---

## Files Changed

### 1. `admin/assets/js/ret_purchase_order.js`

#### A. Credit/Debit Entry — Radio Button Handler (~line 82826)
- **Before:** Only handled Supplier (value=1) and Approvals (value=3)
- **After:** Added Smith (value=2) — forces Amount mode, calls `load_smith_po_bills()`

#### B. Credit/Debit Entry — Karigar Dropdown Handler (~line 82848)
- **Before:** Only reloaded bills for Supplier and Approvals on karigar change
- **After:** Added Smith bill reload on karigar change

#### C. `load_supplier_approval_po_bills()` (~line 82982)
- **Before:** `data: { 'id_karigar': ..., 'bill_type': '3' }`
- **After:** `data: { 'id_karigar': ..., 'bill_type': '3', 'grn_type': '2' }`

#### D. `load_supplier_po_bills()` (~line 83009)
- **Before:** `data: { 'id_karigar': ..., 'bill_type': '1' }`
- **After:** `data: { 'id_karigar': ..., 'bill_type': '1', 'grn_type': '1' }`

#### E. NEW: `load_smith_po_bills()` (~line 83030)
- Calls same endpoint `getPending_payment_po_bills` with `grn_type: '4'`
- Uses amount-based bill format (same as supplier)

#### F. Supplier Payment — `get_all_payment_pending_po_bills()` (~line 83082)
- **Before:** No grn_type sent in AJAX
- **After:** Added `var grn_type_val = (bill_type == '2') ? '4' : '1';` and passes `'grn_type': grn_type_val`

### 2. `admin/application/models/ret_purchase_order_model.php`

#### A. `getPending_payment_po_bills()` — 1st UNION (line 8724)
- **Added:** `grn.grn_type` filter from `$post['grn_type']`
- **Impact:** Supplier bills now restricted to grn_type=1, Smith bills to grn_type=4

#### B. `getPending_payment_po_bills()` — 2nd UNION (line 8758)
- **Added:** Same `grn.grn_type` filter for rate-cut entries

#### C. `get_pending_po_bills_after_tagging_without_pcs_with_weight()` (line 8828)
- **Added:** `g.grn_type` filter from `$post['grn_type']`
- **Impact:** Approval bills now restricted to grn_type=2

#### D. `getPending_payment_po_bills_for_payment()` — 1st UNION (line 8602)
- **Added:** `grn.grn_type` filter from `$post['grn_type']`

#### E. `getPending_payment_po_bills_for_payment()` — 2nd UNION (line 8637)
- **Added:** Same `grn.grn_type` filter for rate-cut entries

#### F. `getPending_payment_po_bills_for_payment()` — 3rd UNION (line 8656)
- **Added:** `cn.accountto` filter based on `$post['bill_type']`
- bill_type=1 (Bill) → `cn.accountto = 1` (Supplier CR/DR entries only)
- bill_type=2 (Receipt) → `cn.accountto = 2` (Smith CR/DR entries only)

---

## Behavior Matrix After Fix

### Credit/Debit Entry Page
(`admin_ret_purchase/credit_debit_entry/add`)

| Radio Button | Value | Mode    | GRN Filter   | Function Called                                      |
|-------------|-------|---------|-------------|------------------------------------------------------|
| Supplier    | 1     | Amount  | grn_type=1  | `load_supplier_po_bills()`                           |
| Smith       | 2     | Amount  | grn_type=4  | `load_smith_po_bills()` ← NEW                       |
| Approvals   | 3     | Weight  | grn_type=2  | `load_supplier_approval_po_bills()`                  |

**Toggle behavior:**
- Supplier/Smith selected + toggle to Weight → auto-switches to Approvals
- Approvals selected + toggle to Amount → auto-switches to Supplier

### Supplier Payment Page
(`admin_ret_purchase/supplier_po_payment/add`)

| Radio Button | bill_type | grn_type sent | CR/DR accountto | What Shows                    |
|-------------|-----------|---------------|-----------------|-------------------------------|
| BILL        | 1         | 1             | 1 (Supplier)    | PU-* bills + Supplier CR/DR   |
| RECEIPT     | 2         | 4             | 2 (Smith)       | PJ-* bills + Smith CR/DR      |

---

## Testing Checklist

### Pre-requisites
- [ ] Database has bills with grn_type=1 (PU-*), grn_type=2 (PM-*), and grn_type=4 (PJ-*)
- [ ] At least one CR/DR credit note exists with accountto=1 (Supplier)
- [ ] At least one CR/DR credit note exists with accountto=2 (Smith)
- [ ] Clear browser cache / hard refresh to load updated JS

### Credit/Debit Entry Page Tests

**Test 1: Supplier Radio (value=1)**
- [ ] Select Supplier radio → mode switches to Amount
- [ ] Select a karigar with PU-* bills → dropdown shows ONLY grn_type=1 bills
- [ ] Verify NO PJ-* (grn_type=4) bills appear in dropdown
- [ ] Verify NO PM-* (grn_type=2) bills appear in dropdown

**Test 2: Smith Radio (value=2)**
- [ ] Select Smith radio → mode switches to Amount (or stays Amount)
- [ ] Select a karigar with PJ-* bills → dropdown shows ONLY grn_type=4 bills
- [ ] Verify NO PU-* (grn_type=1) bills appear in dropdown
- [ ] Verify bills load correctly (they didn't load at all before this fix)

**Test 3: Approvals Radio (value=3)**
- [ ] Select Approvals radio → mode switches to Weight
- [ ] Select a karigar with PM-* bills → dropdown shows ONLY grn_type=2 bills
- [ ] Verify NO PU-* or PJ-* bills appear in dropdown

**Test 4: Mode Toggle Behavior**
- [ ] Smith selected → toggle to Weight → radio auto-switches to Approvals
- [ ] Approvals selected → toggle to Amount → radio auto-switches to Supplier
- [ ] Supplier selected → toggle to Weight → radio auto-switches to Approvals

**Test 5: Karigar Change**
- [ ] With Smith radio selected, change karigar → bills reload with grn_type=4
- [ ] With Supplier radio selected, change karigar → bills reload with grn_type=1

### Supplier Payment Page Tests

**Test 6: Bill Radio**
- [ ] Select Bill radio → select karigar → pending bills table shows ONLY PU-* entries
- [ ] Verify NO PJ-* entries appear
- [ ] Verify CR/DR credit notes created under Supplier (accountto=1) appear
- [ ] Verify CR/DR credit notes created under Smith (accountto=2) do NOT appear

**Test 7: Receipt Radio**
- [ ] Select Receipt radio → select karigar → pending bills table shows ONLY PJ-* entries
- [ ] Verify NO PU-* entries appear
- [ ] Verify CR/DR credit notes created under Smith (accountto=2) appear
- [ ] Verify CR/DR credit notes created under Supplier (accountto=1) do NOT appear

**Test 8: Payment Save**
- [ ] Create a payment under Bill radio → verify it saves correctly
- [ ] Create a payment under Receipt radio → verify it saves correctly
- [ ] Verify saved payments reflect correct bill associations

### Regression Tests

**Test 9: Existing Functionality**
- [ ] Supplier Rate Cut page still loads bills correctly (bill_type=3 path)
- [ ] Karigar Metal Issue page still loads bills correctly (bill_type=2 path)
- [ ] Opening checkbox on payment page still loads opening balances correctly
- [ ] Payment edit/view still shows correct bill details

---

## Rollback Instructions

If issues arise, revert the `grn_type` filter additions:

1. **JS file:** Remove `grn_type` from all AJAX data objects, remove `load_smith_po_bills()` function, remove Smith handler from radio/karigar change events
2. **Model file:** Remove all `isset($post['grn_type'])` conditions from WHERE clauses, remove `cn.accountto` filter from 3rd UNION

---

## Related Changes (Same Day)

- **Supplier Ledger View Fix:** `ret_view_supplier_ledger` updated to exclude grn_type IN (2,4) — see `database/migrations/20260430_000001_fix_supplier_ledger_view_grn_type.sql`
