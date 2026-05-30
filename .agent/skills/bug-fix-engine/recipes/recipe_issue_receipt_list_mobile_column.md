# Recipe: Missing Customer Mobile No Column in Issue & Receipt Listing

## Metadata
- **Pattern ID**: PAT-LIST-MOB01
- **Severity**: LOW
- **Modules Affected**: Billing (Issue & Receipt Listing)
- **Auto-fixable**: No (requires coordinated SQL + View + JS edit across 4 files)

## Client Scope
- **Applies to**: ALL
- **Reason**: Standard feature gap — mobile number is stored in the `customer` table but was never wired to the Issue/Receipt listing DataTables.

## Created By
- **Developer**: Antigravity AI
- **Client**: karpagamjewels.com
- **Date**: 2026-04-24
- **Source Bug ID**: N/A

## Symptom
In the Receipt listing (`/admin_ret_billing/receipt/list`) and Issue listing (`/admin_ret_billing/issue/list`), the Customer Mobile Number column is missing from the table. Users cannot see mobile numbers directly in the listing and must open individual records to find it.

## Root Cause
The model functions `ajax_getReceiptlist()` and `ajax_getIssuetist()` in `ret_billing_model.php` already JOIN the `customer` table but only SELECT `c.firstname` — they never include `c.mobile` in the result set. Consequently, the HTML `<thead>` has no `<th>` for mobile and the JS DataTable `aoColumns` has no `{ "mDataProp": "cus_mobile" }` entry.

Three-layer gap: SQL SELECT → HTML header → JS DataTable column definition.

## Detection
```bash
# Check if cus_mobile exists in Receipt list SQL
grep -n "cus_mobile" admin/application/models/ret_billing_model.php

# Check if Mobile No header exists in views
grep -n "Mobile No" admin/application/views/billing/issueReceipt/receiptList.php
grep -n "Mobile No" admin/application/views/billing/issueReceipt/issueList.php

# Check if cus_mobile DataTable column exists in JS
grep -n "cus_mobile" admin/assets/js/ret_billing.js
```

## Files
- `admin/application/models/ret_billing_model.php` — `ajax_getReceiptlist()` and `ajax_getIssuetist()` functions
- `admin/application/views/billing/issueReceipt/receiptList.php` — Receipt table `<thead>`
- `admin/application/views/billing/issueReceipt/issueList.php` — Issue table `<thead>`
- `admin/assets/js/ret_billing.js` — `set_receipt_list()` and `set_issue_list()` DataTable definitions

## Fix

### 1. Model — Receipt List SQL (`ret_billing_model.php`, `ajax_getReceiptlist()`)

**Before:**
```php
$sql = $this->db->query("SELECT r.bill_no,b.name,if(r.type=1,'Issue','Receipt') as type,IFNULL(e.firstname,'-') as emp_name,IFNULL(c.firstname,'-') as cus_name,IFNULL(r.amount,0) as amount,
```

**After:**
```php
$sql = $this->db->query("SELECT r.bill_no,b.name,if(r.type=1,'Issue','Receipt') as type,IFNULL(e.firstname,'-') as emp_name,IFNULL(c.firstname,'-') as cus_name,IFNULL(c.mobile,'-') as cus_mobile,IFNULL(r.amount,0) as amount,
```

### 2. Model — Issue List SQL (`ret_billing_model.php`, `ajax_getIssuetist()`)

**Before:**
```php
if(date(d.entry_date)=date(r.bill_date),'1','0') as allow_cancel,r.bill_status
```

**After:**
```php
if(date(d.entry_date)=date(r.bill_date),'1','0') as allow_cancel,r.bill_status,

			IFNULL(if(r.issue_to=1,e.mobile,if(r.issue_to=2,c.mobile,'')),'-') as cus_mobile
```

> **Note:** Issue list uses conditional mobile selection based on `issue_to` type: employee mobile (issue_to=1), customer mobile (issue_to=2), empty for account head entries.

### 3. View — Receipt List (`receiptList.php`, inside `<thead>`)

**Before:**
```html
<th>Customer</th>
<th>Tot.Amount</th>
```

**After:**
```html
<th>Customer</th>
<th>Mobile No</th>
<th>Tot.Amount</th>
```

### 4. View — Issue List (`issueList.php`, inside `<thead>`)

**Before:**
```html
<th>Barrower Name</th>
<th>Tot.Amount</th>
```

**After:**
```html
<th>Barrower Name</th>
<th>Mobile No</th>
<th>Tot.Amount</th>
```

### 5. JS — Receipt DataTable (`ret_billing.js`, inside `set_receipt_list()`)

**Before:**
```javascript
{ "mDataProp": "cus_name" },

{ "mDataProp": "amount" },
```

**After:**
```javascript
{ "mDataProp": "cus_name" },

{ "mDataProp": "cus_mobile" },

{ "mDataProp": "amount" },
```

### 6. JS — Issue DataTable (`ret_billing.js`, inside `set_issue_list()`)

**Before:**
```javascript
{ "mDataProp": "barrower_name" },

{ "mDataProp": "amount" },
```

**After:**
```javascript
{ "mDataProp": "barrower_name" },

{ "mDataProp": "cus_mobile" },

{ "mDataProp": "amount" },
```

## Verification
1. Navigate to `/admin_ret_billing/receipt/list` — confirm "Mobile No" header appears between "Customer" and "Tot.Amount"
2. Select a branch, set a date range with existing data, click Search — mobile numbers should appear in the new column
3. Navigate to `/admin_ret_billing/issue/list` — confirm "Mobile No" header appears between "Barrower Name" and "Tot.Amount"
4. Click Search with data — mobile numbers should populate (employee mobile for employee issues, customer mobile for customer issues)
5. No JS console errors (no DataTable "Requested unknown parameter" warnings)
6. DataTable search/filter should work across mobile numbers

## Notes
- The `customer` table JOIN already exists in both queries, so no additional JOIN is needed — only the SELECT clause changes.
- For the issue list, the mobile is conditional on `issue_to` type: 1=employee (`e.mobile`), 2=customer (`c.mobile`), other=empty. This matches the existing `barrower_name` conditional pattern.
- This is a standard "add column to DataTable listing" pattern requiring synchronized edits across 3 layers: Model SQL → View HTML header → JS aoColumns.
- No `<tfoot>` exists on these tables, so no footer adjustment needed.
- Related pattern: PAT-RPT-DIA01 (missing column in DataTable listing).
