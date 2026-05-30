# Recipe: Porting Credit/Debit Ledger & PO Adjustments

> This recipe provides instructions to port the complete Credit/Debit Ledger management module, including the Ledger Master, Credit/Debit Entry pages, and the PO Payment Debit Note Adjustments flow to a new client environment.

## Metadata
- **Pattern ID**: PAT-CDL-001
- **Severity**: HIGH
- **Modules Affected**: Purchase Orders, Purchase Order Payments, Masters (CR/DR Ledger Master), Credit/Debit Entry
- **Auto-fixable**: No (requires structural schema additions, file copies, and code ports)

## Client Scope
- **Applies to**: ALL 
- **Reason**: Any ERP client missing the new "Debit Note Adjustments" module in PO Payments and the standalone Credit/Debit Entry module.

## Created By
- **Developer**: Antigravity
- **Client**: LOGIMAX-CLIENTS/shop.gupthagem.com
- **Date**: 2026-05-05
- **Source Bug ID**: N/A (Feature Port)

## Symptom
Clients do not have the ability to create Credit/Debit Notes for Suppliers/Smiths independently of Purchase Returns. The PO Payment form lacks the "Debit Note Adjustment" modal. When inspecting `admin_ret_purchase`, the `credit_debit_entry` endpoints are missing. The `ret_crdr_ledger` master is completely absent.

## Root Cause
The Credit/Debit ledger tables, Master UI, Entry UI, and PO Adjustment logic (developed in `etail_development_src`) have not been pushed to the client repository. 

## Files
- `db_queries.txt` (DB changes)
- `admin/application/config/routes.php`
- `admin/application/controllers/admin_ret_catalog.php`
- `admin/application/controllers/admin_ret_purchase.php`
- `admin/application/models/ret_catalog_model.php`
- `admin/application/models/ret_dashboard_api_model.php`
- `admin/application/models/ret_purchase_order_model.php`
- `admin/application/views/master/ret_crdr_ledger/list.php` [NEW]
- `admin/application/views/ret_purchase/credit_entry/form.php` [NEW]
- `admin/application/views/ret_purchase/credit_entry/list.php` [NEW]
- `admin/application/views/ret_purchase/credit_entry/print.php` [NEW]
- `admin/application/views/ret_purchase/popayment/form.php`
- `admin/assets/js/catalog_master.js`
- `admin/assets/js/ret_purchase_order.js`

## Fix

### Step 1: Database Schema Additions
Execute the following table definitions to create the CR/DR ledger masters and PO adjustments linkage tables:
```sql
CREATE TABLE `ret_crdr_ledger` ...
CREATE TABLE `ret_crdr_note` ...
CREATE TABLE `ret_crdr_note_po_adj` ...
CREATE TABLE `ret_purchase_return_po_adj` ...
```
**(Important)**: You must also update the `ret_view_supplier_ledger_grn` (and smith ledger view) to include `UNION ALL` for `ret_crdr_note` entries to ensure the unified ledger lists these manually created notes correctly.

### Step 2: Routing Configuration
In `admin/application/config/routes.php`, add endpoints for the new CR/DR ledger master.
```php
$route['admin_ret_catalog/ret_crdr_ledger/list'] = "admin_ret_catalog/ret_crdr_ledger/list";
$route['admin_ret_catalog/ret_crdr_ledger/ajax'] = "admin_ret_catalog/ret_crdr_ledger/ajax";
// ... (Add all 8 routes for add, edit, delete, status_change, unique validation)
```

### Step 3: CR/DR Ledger Master (Backend & Frontend)
- **Controller**: In `admin_ret_catalog.php`, add the `ret_crdr_ledger` function block handling `list`, `ajax`, `add`, `edit`, `update`, `status_change`, and `delete_ledger`.
- **Model**: In `ret_catalog_model.php`, add methods `ajax_get_crdr_ledger()`, `get_crdr_ledger()`, `check_unique_crdr_ledger()`, and `delete_crdr_ledger()`.
- **JS**: In `catalog_master.js`, inject the data table and form handlers for `#ret_crdr_ledger_form` and register the switch-case `ret_crdr_ledger` on document ready.
- **Views**: Copy `master/ret_crdr_ledger/list.php` from source into the client environment.

### Step 4: Credit/Debit Entry Module (Backend & Frontend)
- **Controller**: In `admin_ret_purchase.php`, add the `credit_debit_entry` controller function handling `list`, `ajax`, `add`, `edit`, `delete`, and `credit_debit_acknolodgement` actions. Also add `get_crdr_debit_entries_for_payment()`.
- **Model**: In `ret_purchase_order_model.php`, add methods for saving notes: `get_creditdebit_entry()`, `get_credit_debit()`, `code_number_generator()`, `get_credit_debit_detail()`, and `get_crdr_debit_entries_without_po()`.
- **Model**: In `ret_dashboard_api_model.php`, add `get_crdr_details()` to prevent "Call to undefined method" when pulling dashboard API metrics.
- **Views**: Copy `ret_purchase/credit_entry/form.php`, `list.php`, and `print.php` from source.
- **JS**: In `ret_purchase_order.js`, port the `credit_debit_entry` form logic, print formatting, delete confirmation handlers, and add the missing `getTaggedRefNo()` helper.

### Step 5: PO Payment Debit Note Adjustments (UI)
In `admin/application/views/ret_purchase/popayment/form.php`, insert the Debit Note Adj row just above the Total Amount:
```php
<tr>
    <td class="text-right">Debit Note Adj.</td>
    <td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
    <td>
        <a class="btn bg-olive btn-xs" id="debit_note_modal_btn"><b>+</b></a> 
        <span id="tot_debit_adj_amt" class="pull-right" style="padding-right: 6px;"></span>
        <input type="hidden" id="debit_adjustments_data" name="billing[debit_adjustments]" value="">
        <input type="hidden" id="purchase_return_adjustments_data" name="billing[purchase_return_adjustments]" value="">
    </td>
</tr>
```
Append the `#debit_note_modal` HTML template to the bottom of the form file.

### Step 6: PO Payment Backend Calculations
In `admin/application/models/ret_purchase_order_model.php` -> `get_supplier_pay_details()` modify the `balance_amount` SQL block. Use a correlated subquery for CR/DR Notes (do NOT use a direct `LEFT JOIN`, as it causes Cartesian products across ledgers):

```php
(ifnull(SUM(COALESCE(CASE WHEN (trans_type = 2 AND trans_screen_id != 6) THEN ifnull(trans_amount,0) END,0)) - 
SUM(COALESCE(CASE WHEN (trans_type = 1) THEN ifnull(trans_amount,0) 
WHEN (trans_type = 2 AND trans_screen_id = 6) THEN ifnull(trans_amount,0) END,0)),0)
+
ifnull((SELECT SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) - SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) 
        FROM ret_crdr_note 
        WHERE crdr_status = 1 AND supid IS NOT NULL ".($data['id_karigar']!='' ? " and supid=".$data['id_karigar']."" : '')."), 0)) as balance_amount
```

### Step 7: Form Constraints (Business Logic Lockdowns)
- **Print Lock**: Hide the Print button action in `ret_purchase_order.js` under `credit_debit_entry` datatables column rendering (`<i class="fa fa-print"></i>` string replacement).
- **Entry Edit Lock**: In `credit_entry/form.php`, add `disabled` to `#toggle_transaction` and `#accountto3` (Approvals) to enforce strict Debit/Credit logic bound to Purchases only.

## Verification
1. **Master Test**: Go to Masters -> CR/DR Ledger. Create a new ledger.
2. **Note Test**: Go to Purchase -> Credit/Debit Entry. Ensure the `Transaction In` toggle and `Approvals` account type are disabled. Create a Note. Ensure the "Print" action in the list view is hidden.
3. **Ledger Reconcile**: Verify the Supplier Balance drops appropriately in the Ledger for a Debit Note.
4. **PO Apply Test**: Go to PO Payment and click the `+` icon for Debit Note Adj. The modal should open seamlessly. Validate you cannot adjust more than the available debit balance.
