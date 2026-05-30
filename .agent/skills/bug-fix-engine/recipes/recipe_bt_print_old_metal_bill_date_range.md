# Recipe: Add Old Metal Bill Date Range to Branch Transfer Print

## Metadata
- **Pattern ID**: PAT-BT-PRINT01
- **Severity**: LOW
- **Modules Affected**: Branch Transfer (Print)
- **Auto-fixable**: No (requires coordinated SQL + View edit)

## Client Scope
- **Applies to**: ALL clients using old metals branch transfer (s_type=3)
- **Reason**: Standard feature gap — the old metal bill date range was not displayed on the Branch Transfer Receipt print.

## Created By
- **Developer**: Antigravity AI
- **Client**: karpagamjewels.com
- **Date**: 2026-04-24
- **Source Bug ID**: N/A

## Symptom
In the Branch Transfer Receipt print (`/admin_ret_brntransfer/branch_transfer/print/{code}/3/{type}`), users cannot see the date range of the old metal bills being transferred. They must look up individual bills to determine the date span.

## Root Cause
The `getBTransData()` model function for `s_type==3` already JOINs `ret_billing bill` but only selects `bill.bill_no` — it never includes `MIN(bill.bill_date)` or `MAX(bill.bill_date)` in the result set. The view has no markup to display this data.

## Detection
```bash
# Check if bill date range fields exist in getBTransData s_type==3 query
grep -n "bill_from_date\|bill_to_date" admin/application/models/ret_brntransfer_model.php

# Check if Bill Date is displayed in print view
grep -n "bill_from_date\|bill_to_date" admin/application/views/branch_transfer/print.php
```

## Files
- `admin/application/models/ret_brntransfer_model.php` — `getBTransData()` function, s_type==3 branch
- `admin/application/views/branch_transfer/print.php` — print header layout

## Fix

### 1. Model — Add MIN/MAX bill dates to getBTransData s_type==3 query

**Before:**
```php
$sql=$this->db->query("SELECT m.metal_type,SUM(est.gross_wt) as grs_wt,SUM(est.net_wt) as net_wt,SUM(est.rate) as amount,date_format(b.created_time,'%d-%m-%Y') as created_time,
    b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,bill.bill_no
```

**After:**
```php
$sql=$this->db->query("SELECT m.metal_type,SUM(est.gross_wt) as grs_wt,SUM(est.net_wt) as net_wt,SUM(est.rate) as amount,date_format(b.created_time,'%d-%m-%Y') as created_time,
    b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,bill.bill_no,
    date_format(MIN(bill.bill_date),'%d-%m-%Y') as bill_from_date, date_format(MAX(bill.bill_date),'%d-%m-%Y') as bill_to_date
```

> **Note:** MIN/MAX are aggregate functions that work correctly with the existing GROUP BY clause. No additional JOIN needed — `ret_billing bill` is already joined.

### 2. View — Add Bill Date center column to the GSTIN/TRANS ID table

The print header has a 2-column table: GSTIN (left) and TRANS ID details (right). Add a center column for the bill date range.

**Before (2-column layout):**
```html
<table style="width:150%">
    <tr>
        <td style="font-size:11px !important;width:50%;">
            <span>GSTIN : <?php echo $comp_details['gst_number']; ?> &nbsp;</span>
        </td>
        <td style="font-size:11px !important;text-align:right">
            <!-- TRANS ID / CODE / DATE / FROM / TO spans -->
        </td>
    </tr>
</table>
```

**After (3-column layout):**
```html
<table style="width:150%">
    <tr>
        <td style="font-size:11px !important;width:20%;">
            <span>GSTIN : <?php echo $comp_details['gst_number']; ?> &nbsp;</span>
        </td>

        <td style="font-size:11px !important;text-align:center;vertical-align:middle;width:25%;">
        <?php if(isset($btrans[0]['bill_from_date']) && !empty($btrans[0]['bill_from_date'])) { ?>
            <span style="font-weight:bold;">Bill Date : <?php echo $btrans[0]['bill_from_date']; ?> To <?php echo $btrans[0]['bill_to_date']; ?></span>
        <?php } ?>
        </td>

        <td style="font-size:11px !important;text-align:right;width:45%;">
            <!-- TRANS ID / CODE / DATE / FROM / TO spans -->
        </td>
    </tr>
</table>
```

> **Important:** The outer table must keep `width:150%` — this is intentional for DomPDF A4 layout. Column widths must total ~90% (20+25+45) to prevent overflow collapse.

## Verification
1. Navigate to `/admin_ret_brntransfer/branch_transfer/print/{code}/3/1` (summary print)
2. Confirm "Bill Date : DD-MM-YYYY To DD-MM-YYYY" appears centered between GSTIN and TRANS ID
3. Navigate to `/admin_ret_brntransfer/branch_transfer/print/{code}/3/2` (detailed print)
4. Confirm bill date appears correctly on detailed print too
5. Navigate to a non-old-metal transfer print (s_type=1,2,4) — center column should be empty (no errors)
6. Verify "BRANCH TRANSFER RECEIPT" heading is on its own line below, not collapsed into the table

## Notes
- Do NOT create a separate model function for the bill date range — add it to the existing `getBTransData` query directly. The JOIN to `ret_billing` already exists.
- Do NOT use flexbox/grid CSS properties (`align-items`, `justify-content`) — DomPDF does not support them.
- The `isset($btrans[0]['bill_from_date'])` guard ensures the center column is empty for non-old-metal transfers where `bill_from_date` won't exist in the result set.
- Column width distribution (20/25/45) was calibrated for A4 DomPDF rendering. Adjust if client has different page sizes.
