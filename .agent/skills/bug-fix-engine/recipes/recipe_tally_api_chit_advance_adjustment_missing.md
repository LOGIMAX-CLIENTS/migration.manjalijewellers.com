# Recipe: Tally API Chit Advance Adjustment Missing

## Metadata
- **Pattern ID**: PAT-TALLY-015
- **Severity**: HIGH
- **Modules Affected**: Tally API (`tally_app_api`)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Common discrepancy across older versions of the codebase where Chit advance utilizations were not synced correctly to Tally receipts.

## Created By
- **Developer**: Antigravity
- **Client**: karpagamjewels.com
- **Date**: 2026-05-09
- **Source Bug ID**: N/A

## Symptom
Advance Adjustment payments mapped against Chit schemes (where `adjusted_for = 2` in the `ret_advance_utilized` table) are missing from the Tally API output when hitting the `importreceipts` endpoint for a given date range.

## Root Cause
The block of SQL code responsible for mapping `ret_advance_utilized` payments (`adjusted_for = 2`) into the `getAllreceiptsList` JSON payload was either entirely missing, or injected inside the wrong function (`getChitUtlizationPayments()`) which is not executed by the `importreceipts` endpoint.
Additionally, when queried, the SQL logic threw errors by referencing `pay.istransfered = 0` despite the `ret_advance_utilized` table not having an `istransfered` column (the correct reference is `p.istransfered = 0` on the `payment` table).

## Detection
```command
grep -rn "pay.adjusted_for = 2" application/models/ret_tally_api_model.php
```

## Files
- `application/models/ret_tally_api_model.php`

## Fix

### Before
```php
// Block missing from getAllreceiptsList() entirely or misplaced inside getChitUtlizationPayments()
// And using incorrect istransfered column:
// WHERE p.payment_status = 1 AND pay.adjusted_for = 2 
// AND pay.istransfered = 0
```

### After
```php
// Ensure this block is placed at the very end of getAllreceiptsList() BEFORE 'return $return_data;'
        //Advance Adjustment payment for Chit utilized_amt
        $sql = "SELECT 
        t.*, 
        (@row_number:=CASE WHEN @prev_id_payment = t.id_payment THEN @row_number + 1 ELSE 1 END) AS currownumber,
        @prev_id_payment := t.id_payment AS prev_id_payment
    FROM (SELECT date_format(p.date_payment, '%Y%m%d') as INVOICEDATE, p.receipt_no, `id_adv_utilized`, 
                                        pay.utilized_amt, pay.id_payment, 
                                        'Payment' as VOUCHERTYPE, 
                                        '' as PARTYCODE,cus.firstname as PARTYNAME,'' as PARTYGROUP,IFNULL(cus.gst_number,'') as GSTNO,
                                        concat(IFNULL(adr.address1,''),' ', IFNULL(adr.address2,''), ' ', IFNULL(adr.address3,'')) as cuaddress, 
                                        IFNULL(adr.pincode,'') as cuspincode, IFNULL(st.name,'') as cusstate, IFNULL(cn.name,'') as cuscountry, 
                                        cus.mobile as CONTACTNO, 
                                        '' as REMARKS,
                                        br.address1 as frmaddress1, br.address2 as frmaddress2, 
                                        IFNULL(bst.name,'') as brstate, IFNULL(bcn.name,'') as brcountry,  
                                        'Sundry Debtors' as LedgerParent, p.id_branch, br.short_name as brcode, 
                                        br.name as CostCentre, br.sort
                                        FROM ret_advance_utilized as pay 
                                        LEFT JOIN payment p ON p.id_payment = pay.id_payment 
                                        LEFT JOIN scheme_account as sca ON sca.id_scheme_account = p.id_scheme_account 
                                        LEFT JOIN customer cus ON cus.id_customer = sca.id_customer 
                                        LEFT JOIN address as adr ON adr.id_customer = cus.id_customer  
                                        LEFT JOIN country as cn ON cn.id_country = adr.id_country 
                                        LEFT JOIN state as st ON st.id_state = adr.id_state 
                                        LEFT JOIN branch br ON br.id_branch = p.id_branch 
                                        LEFT JOIN state as bst ON bst.id_state = br.id_state 
                                        LEFT JOIN country as bcn ON bcn.id_country = br.id_country 
                                        WHERE p.payment_status = 1 AND pay.adjusted_for = 2 
                                         ".($from_date !='' && $to_date !='' ? " AND DATE(p.date_payment) BETWEEN '".$from_date."' AND '".$to_date."'" :'')." 
                                        ".(isset($_GET['id_branch']) && $_GET['id_branch'] !='' ? " AND p.id_branch = '".$_GET['id_branch']."'" :'')."  
                                        AND p.istransfered = 0 AND pay.utilized_amt > 0 LIMIT 2000 ) AS t, (SELECT @row_number:=0, @prev_id_payment:=0) AS vars;";
        $adv_query = $this->db->query($sql);

        foreach($adv_query->result() as $row){
                    $ledgername = "Advance Adj";
                    $vouchertype = "GR-Journal";
                    $partyvoucherparent = "Sundry Debtors";

                    if(!empty($row->receipt_no)){
                        $pay_invoice_string = "ADVC-".$row->receipt_no."-".$row->currownumber;
                    }else{
                        $pay_invoice_string = "ADVC-".$row->id_payment."-".$row->currownumber;
                    }

                    $return_data['VOUCHER'][] = array(
                                            "Autoid"                => $row->id_adv_utilized,
                                            "CompanyNumber"         => $row->sort,
                                            "TallyMasterid"         => 1,
                                            "Voucherid"             => "",
                                            "VoucherNumber"         => $pay_invoice_string,
                                            "VoucherDate"           => $row->INVOICEDATE,
                                            "VoucherType"           => $vouchertype,
                                            "VoucherTypeParent"     => $partyvoucherparent,
                                            "LedgerName"            => $row->PARTYNAME. "-".$row->CONTACTNO,
                                            "LedgerParent"          => $row->LedgerParent,
                                            "LedgerAddress"         => $row->cuaddress,
                                            "LedgerState"           => $row->cusstate,
                                            "LedgerCountry"         => $row->cuscountry,
                                            "LedgerPincode"         => $row->cuspincode,
                                            "LedgerMobile"          => "+91".$row->CONTACTNO,
                                            "LedgerGstReg"          => "Unregistered/Consumer",
                                            "LedgerGstin"           => "",
                                            "BillName"              => "",
                                            "BillDate"              => $row->INVOICEDATE,
                                            "PlaceOfSupply"         => "",
                                            "TransactionDate"       => $row->INVOICEDATE,
                                            "CrDr"                  => "Cr",
                                            "Amount"                => number_format($row->utilized_amt, 2,'.',''),
                                            "CostCategory"          => "",
                                            "CostCentre"            => $row->CostCentre,
                                            "BranchCode"            => $row->brcode,
                                            "Location"              => $row->frmaddress1,
                                            "State"                 => $row->brstate
                                        );
                    
                    $return_data['VOUCHER'][] = array(
                                            "Autoid"                => $row->id_adv_utilized ,
                                            "CompanyNumber"         => $row->sort,
                                            "TallyMasterid"         => 1,
                                            "Voucherid"             => "",
                                            "VoucherNumber"         => $pay_invoice_string,
                                            "VoucherDate"           => $row->INVOICEDATE,
                                            "VoucherType"           => $vouchertype,
                                            "VoucherTypeParent"     => "Sundry Debtors",
                                            "LedgerName"            => $ledgername,
                                            "LedgerParent"          => "Current Liabilities",
                                            "LedgerAddress"         => "",
                                            "LedgerState"           => "",
                                            "LedgerCountry"         => "",
                                            "LedgerPincode"         => "",
                                            "LedgerMobile"          => "",
                                            "LedgerGstReg"          => "Unregistered/Consumer",
                                            "LedgerGstin"           => "",
                                            "BillName"              => "",
                                            "BillDate"              => $row->INVOICEDATE,
                                            "PlaceOfSupply"         => "",
                                            "TransactionDate"       => $row->INVOICEDATE,
                                            "CrDr"                  => "Dr",
                                            "Amount"                => number_format($row->utilized_amt, 2,'.',''),
                                            "CostCategory"          => "",
                                            "CostCentre"            => $row->CostCentre,
                                            "BranchCode"            => $row->brcode,
                                            "Location"              => $row->frmaddress1,
                                            "State"                 => $row->brstate
                                        );
        }
```

## Verification
1. Open the API endpoint `/index.php/tally_app_api/importreceipts?from_date=YYYY-MM-DD&to_date=YYYY-MM-DD` and check if vouchers with `ADVC-` prefix exist.
2. Confirm the JSON output includes pairs of Cr/Dr entries mapping `Advance Adj` to the customer's ledger.
3. Validate that `p.istransfered = 0` accurately prevents syncing previously synced vouchers.

## Notes
Make sure not to insert this logic inside `getChitUtlizationPayments()` by mistake. Ensure it is placed directly before `return $return_data;` at the end of `getAllreceiptsList()`.
