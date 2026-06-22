# Recipe: Scheme Payment Sticker Printing PRN Layout Config

## Metadata
- **Pattern ID**: PAT-PRN-001
- **Severity**: HIGH
- **Modules Affected**: Scheme Payments (Thermal/Sticker Printing)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: RTM, SRJ
- **Reason**: Older versions of the codebase do not have the TSPL PRN configuration enabled for scheme payments or have missing running total variables.

## Created By
- **Developer**: Antigravity
- **Client**: RTM
- **Date**: 2026-06-15
- **Source Bug ID**: N/A

## Symptom
Sticker printing does not trigger for scheme payment receipts, or the generated `.prn` files display blank values for total weight (`T.W:`) and total amount (`T.Amt:`).

## Root Cause
1. Controller-level route handling for `.prn` generation on payment invoices was commented out.
2. View template `receipt_thermal_prn.php` was missing or configured with incorrect label coordinates/dimensions.
3. Database model method `get_invoiceData` was missing the subqueries to calculate running weights and running amounts, resulting in empty values sent to the print view.

## Detection
Run the following search to check if the running totals are calculated in the invoice data fetch query:
```command
grep -rn "running_amount" admin/application/models/payment_model.php
```
Also check if the controller loads `receipt_thermal_prn` for scheme payments:
```command
grep -rn "receipt_thermal_prn" admin/application/controllers/admin_payment.php
```

## Files
- `admin/application/controllers/admin_payment.php`
- `admin/application/models/payment_model.php`
- `admin/application/views/include/receipt_thermal_prn.php`

## Fix

### Controller Fix (admin_payment.php)
#### Before
```php
        if ($type == 'Payment') {
            $data['records'] = $this->$model->get_invoiceData($id, "");
            $data['records_sch'] = $this->$model->get_paymentContent($data['records'][0]['id_scheme_account']);
            $data['gstSplitup'] = $this->$model->get_gstSplitupData($data['records'][0]['id_scheme'], $data['records'][0]['date_add']);
            $paidinstll = $this->$model->get_paidinstallmentcount($data['records'][0]['id_scheme_account']);
            $i = 1;
            foreach ($paidinstll as $x => $x_value) {
                if ($x_value['id_payment'] == $id) {
                    $data['records'][0]['installment'] = $i;
                }
                $i++;
            }
            if ($this->branch_settings == 1) {
                $data['comp_details'] = $this->$set->get_branchcompany($data['records'][0]['id_branch']);
            } else {
                $data['comp_details'] = $this->$set->get_company();
            }
            $data['records'][0]['amount_in_words'] = $this->no_to_words($data['records'][0]['payment_amount']);
            $html = $this->load->view('include/receipt_thermal', $data);
        }
```
#### After
```php
        if ($type == 'Payment') {
            $data['records'] = $this->$model->get_invoiceData($id, "");
            $data['records_sch'] = $this->$model->get_paymentContent($data['records'][0]['id_scheme_account']);
            $data['gstSplitup'] = $this->$model->get_gstSplitupData($data['records'][0]['id_scheme'], $data['records'][0]['date_add']);
            $paidinstll = $this->$model->get_paidinstallmentcount($data['records'][0]['id_scheme_account']);
            $i = 1;
            foreach ($paidinstll as $x => $x_value) {
                if ($x_value['id_payment'] == $id) {
                    $data['records'][0]['installment'] = $i;
                }
                $i++;
            }
            if ($this->branch_settings == 1) {
                $data['comp_details'] = $this->$set->get_branchcompany($data['records'][0]['id_branch']);
            } else {
                $data['comp_details'] = $this->$set->get_company();
            }
            $data['records'][0]['amount_in_words'] = $this->no_to_words($data['records'][0]['payment_amount']);
            $receipt_content = $this->load->view('include/receipt_thermal_prn', $data, true);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="receipt_' . $id . '_' . date("Ymd_His") . '_lmx.prn"');
            header('Content-Length: ' . strlen($receipt_content));
            echo $receipt_content;
            exit;
        }
```

### Model Fix (payment_model.php)
#### Before
In `get_invoiceData` SELECT query:
```sql
SELECT sch.id_purity,IFNULL(cus.nominee_mobile,'') as nominee_mobile,IFNULL(cus.nominee_name,'') as nominee_name,IFNULL(cus.nominee_relationship,'') as nominee_relationship,IFNULL(cus.nominee_address1,'') as nominee_address1,IFNULL(cus.nominee_address2,'') as nominee_address2,
                            sch_acc.topup_weight,sch_acc.topup_amount,sch_acc.topup_rate,IFNULL(DATE_FORMAT(sch_acc.start_date,'%d-%m-%Y'),'') as start_date,
                            pay.topup_slab_data,IFNULL(pay.remark,'-') as pay_remarks,IFNULL(DATE_FORMAT(pay.due_date_to,'%d-%m-%Y'),'') as due_date_to,IFNULL(s.state_code,'') as state_code,IFNULL(cus.pan,'') as cus_pan_no,IFNULL(m.metal,'Metal') as metal_name,pay.id_payment,sch_acc.id_scheme_account,IFNULL(sch_acc.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sch_acc.id_branch) as acc_branch,sch.code,c.schemeaccNo_displayFrmt,sch.is_lucky_draw,ifnull(sch_acc.scheme_acc_number,'Not Allocated') as scheme_acc_number,c.scheme_wise_acc_no,
		                    IFNULL(pay.receipt_year,'') as receipt_year, ...
```
In `get_invoiceData` record array construction:
```php
                    'saved_benefits' => $row->saved_benefits,
                    'saved_benefit_amt' => $row->saved_benefit_amt,
                );
```
#### After
In `get_invoiceData` SELECT query:
```sql
SELECT sch.id_purity,IFNULL(cus.nominee_mobile,'') as nominee_mobile,IFNULL(cus.nominee_name,'') as nominee_name,IFNULL(cus.nominee_relationship,'') as nominee_relationship,IFNULL(cus.nominee_address1,'') as nominee_address1,IFNULL(cus.nominee_address2,'') as nominee_address2,
                            sch_acc.topup_weight,sch_acc.topup_amount,sch_acc.topup_rate,IFNULL(DATE_FORMAT(sch_acc.start_date,'%d-%m-%Y'),'') as start_date,
                            pay.topup_slab_data,IFNULL(pay.remark,'-') as pay_remarks,IFNULL(DATE_FORMAT(pay.due_date_to,'%d-%m-%Y'),'') as due_date_to,IFNULL(s.state_code,'') as state_code,IFNULL(cus.pan,'') as cus_pan_no,IFNULL(m.metal,'Metal') as metal_name,pay.id_payment,sch_acc.id_scheme_account,IFNULL(sch_acc.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sch_acc.id_branch) as acc_branch,sch.code,c.schemeaccNo_displayFrmt,sch.is_lucky_draw,ifnull(sch_acc.scheme_acc_number,'Not Allocated') as scheme_acc_number,c.scheme_wise_acc_no,
                            (SELECT SUM(p2.payment_amount)FROM payment p2 WHERE p2.payment_status = 1 AND p2.id_scheme_account = (SELECT p1.id_scheme_account FROM payment p1 WHERE p1.id_payment = '" . $payment_no . "')AND p2.date_payment <= (SELECT p1.date_payment FROM payment p1 WHERE p1.id_payment = '" . $payment_no . "')) AS running_amount,
                            (SELECT SUM(p2.metal_weight)FROM payment p2 WHERE p2.payment_status = 1 AND p2.id_scheme_account = (SELECT p1.id_scheme_account FROM payment p1 WHERE p1.id_payment = '" . $payment_no . "')AND p2.date_payment <= (SELECT p1.date_payment FROM payment p1 WHERE p1.id_payment = '" . $payment_no . "')) AS running_weight,
		                    IFNULL(pay.receipt_year,'') as receipt_year, ...
```
In `get_invoiceData` record array construction:
```php
                    'saved_benefits' => $row->saved_benefits,
                    'saved_benefit_amt' => $row->saved_benefit_amt,
                    'running_weight' => $row->running_weight,
                    'running_amount' => $row->running_amount,
                );
```

### View Fix (receipt_thermal_prn.php)
Create/Overwrite the view with 50mm x 25mm label TSPL parameters:
```php
SIZE 50. mm, 25 mm
GAP 3 mm, 0 mm
SPEED 1
DENSITY 20
DIRECTION 0,0
REFERENCE 0,0
OFFSET 0 mm
SHIFT 0
SET PEEL OFF
SET CUTTER OFF
SET TEAR ON
CLS
CODEPAGE 850
TEXT 380,160,"ROMAN.TTF",180,3,9,"Rc : <?= $records[0]['receipt_no']?>"
TEXT 180,160,"ROMAN.TTF",180,3,9,"Ins: <?= intval($records[0]['installment']); ?>"
TEXT 380,130,"ROMAN.TTF",180,3,9,"Dt : <?= (date('d-m-Y', strtotime(str_replace("/", "-", $records[0]['date_payment'])))) ?>"
TEXT 380,100,"ROMAN.TTF",180,3,9,"<?= $records[0]['scheme_acc_number']?>"
<?php if ($records[0]['scheme_type'] != 0) : ?>
TEXT 380,68,"ROMAN.TTF",180,4,10,"Rs. <?= number_format($records[0]['payment_amount']) ?> Wt: <?= $records[0]['metal_weight']?>"
TEXT 380,30,"ROMAN.TTF",180,4,10,"Rt. <?= intval($records[0]['metal_rate']) ?> T.W:<?= ($records[0]['running_weight'])?> "
<?php else : ?>
TEXT 380,68,"ROMAN.TTF",180,4,10,"Rs. <?= number_format($records[0]['payment_amount'])?>"
TEXT 380,30,"ROMAN.TTF",180,4,10,"T.Amt:<?= $records[0]['running_amount']?> "
<?php endif; ?>
PRINT 1,1
E
```

## Verification
1. Open a customer scheme payment account in RTM.
2. Complete a scheme payment.
3. Click "Print Receipt" (which calls `payment/thermal_invoice`).
4. Ensure the `.prn` download begins and that the file opens with the proper coordinates and contains all values (`T.W:` for weight schemes or `T.Amt:` for amount schemes) matching the transaction.

## Notes
Layout coordinates are optimized for TSPL (TSC) thermal sticker printers using 50mm x 25mm labels.
