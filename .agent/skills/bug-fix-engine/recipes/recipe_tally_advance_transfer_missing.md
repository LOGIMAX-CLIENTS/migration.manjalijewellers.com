# Tally API — Customer-to-Customer Advance Transfer Missing from importreceipts

> `ret_advance_transfer` records (receipt_type=7) were never queried in `getAllreceiptsList()`,
> causing advance transfer vouchers to be silently absent from the Tally `importreceipts` API response.

## Metadata
- **Pattern ID**: PAT-TALLY-016
- **Severity**: HIGH
- **Modules Affected**: Tally API (`tally_app_api`, `ret_tally_api_model`)
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: Core `getAllreceiptsList()` function is shared across all clients. Any client using
  customer-to-customer advance transfer will have this gap.

## Created By
- **Developer**: Antigravity
- **Client**: karpagamjewels.com
- **Date**: 2026-05-11
- **Source Bug ID**: N/A

## Symptom
When hitting `/index.php/tally_app_api/importreceipts?from_date=YYYY-MM-DD&to_date=YYYY-MM-DD`,
advance transfer receipts (where one customer's advance is moved to another customer) do not appear
in the `VOUCHER` array. Regular advance receipts (receipt added directly) DO appear correctly.

Receipts created via the "Transfer Advance" flow have `receipt_type = 7` on `ret_issue_receipt`
and a corresponding row in `ret_advance_transfer`. These were never queried.

## Root Cause
`getAllreceiptsList()` had no SQL block for `ret_advance_transfer`. The table stores:
- `id_issue_receipt` — the destination receipt (new advance owner)
- `transfer_receipt_id` — the source receipt (original advance owner)
- `transfer_amount` — the amount moved

The function only queried `ret_issue_rcpt_payment`, `ret_advance_utilized`, and issue receipts
via `ret_issue_receipt` join — none of which contain transfer records.

## Detection
```bash
# Confirm the block is absent — should return no results if bug present
grep -n "ret_advance_transfer" application/models/ret_tally_api_model.php

# Confirm receipt_type=7 records exist in the target date
# (run in MySQL)
# SELECT * FROM ret_issue_receipt WHERE receipt_type = 7 AND DATE(bill_date) = 'YYYY-MM-DD';
```

## Files
- `application/models/ret_tally_api_model.php`

## Fix

### Before
```php
// No block existed for ret_advance_transfer in getAllreceiptsList()
// The function jumped directly from the ret_advance_utilized foreach to return $return_data;
        }

        return $return_data;
    }
```

### After
Insert the following block directly before `return $return_data;` at the end of `getAllreceiptsList()`,
after the closing `}` of the Advance Adjustment (`ret_advance_utilized`) foreach loop:

```php
        //Advance Transfer payment (Customer to Customer) - PAT-TALLY-016
        $adv_transfer_sql = "SELECT 
            rat.id_advance_transfer,
            rat.transfer_amount,
            date_format(sir_to.bill_date, '%Y%m%d') as INVOICEDATE,
            sir_to.bill_no as to_bill_no,
            sir_from.bill_no as from_bill_no,
            cus_from.firstname as from_customer_name,
            cus_from.mobile as from_mobile,
            cus_to.firstname as to_customer_name,
            cus_to.mobile as to_mobile,
            concat(IFNULL(adr_from.address1,''),' ',IFNULL(adr_from.address2,''),' ',IFNULL(adr_from.address3,'')) as from_address,
            IFNULL(st_from.name,'') as from_cusstate,
            IFNULL(cn_from.name,'') as from_cuscountry,
            IFNULL(adr_from.pincode,'') as from_cuspincode,
            concat(IFNULL(adr_to.address1,''),' ',IFNULL(adr_to.address2,''),' ',IFNULL(adr_to.address3,'')) as to_address,
            IFNULL(st_to.name,'') as to_cusstate,
            IFNULL(cn_to.name,'') as to_cuscountry,
            IFNULL(adr_to.pincode,'') as to_cuspincode,
            br.id_branch, br.short_name as brcode, br.name as CostCentre, br.address1 as frmaddress1, br.sort,
            IFNULL(bst.name,'') as brstate
        FROM ret_advance_transfer rat
        LEFT JOIN ret_issue_receipt sir_to   ON sir_to.id_issue_receipt   = rat.id_issue_receipt
        LEFT JOIN ret_issue_receipt sir_from ON sir_from.id_issue_receipt = rat.transfer_receipt_id
        LEFT JOIN customer cus_from ON cus_from.id_customer = sir_from.id_customer
        LEFT JOIN customer cus_to   ON cus_to.id_customer   = sir_to.id_customer
        LEFT JOIN address adr_from ON adr_from.id_customer = cus_from.id_customer
        LEFT JOIN address adr_to   ON adr_to.id_customer   = cus_to.id_customer
        LEFT JOIN country cn_from ON cn_from.id_country = adr_from.id_country
        LEFT JOIN state   st_from ON st_from.id_state   = adr_from.id_state
        LEFT JOIN country cn_to   ON cn_to.id_country   = adr_to.id_country
        LEFT JOIN state   st_to   ON st_to.id_state     = adr_to.id_state
        LEFT JOIN branch br ON br.id_branch = sir_to.id_branch
        LEFT JOIN state  bst ON bst.id_state = br.id_state
        WHERE sir_to.istransfered = 0 AND rat.transfer_amount > 0
        ".($from_date !='' && $to_date !='' ? " AND DATE(sir_to.bill_date) BETWEEN '".$from_date."' AND '".$to_date."'" :'')."
        ".(isset($_GET['id_branch']) && $_GET['id_branch'] !='' ? " AND sir_to.id_branch = '".$_GET['id_branch']."'" :'')."
        LIMIT 2000;";

        $adv_transfer_query = $this->db->query($adv_transfer_sql);

        foreach($adv_transfer_query->result() as $trow){
            $vouchertype    = "GR-Journal";
            $voucher_number = "ADVT-".$trow->to_bill_no;

            // Dr: Source customer (advance deducted)
            $return_data['VOUCHER'][] = array(
                "Autoid"            => $trow->id_advance_transfer,
                "CompanyNumber"     => $trow->sort,
                "TallyMasterid"     => 1,
                "Voucherid"         => "",
                "VoucherNumber"     => $voucher_number,
                "VoucherDate"       => $trow->INVOICEDATE,
                "VoucherType"       => $vouchertype,
                "VoucherTypeParent" => "Sundry Debtors",
                "LedgerName"        => $trow->from_customer_name."-".$trow->from_mobile,
                "LedgerParent"      => "Sundry Debtors",
                "LedgerAddress"     => $trow->from_address,
                "LedgerState"       => $trow->from_cusstate,
                "LedgerCountry"     => $trow->from_cuscountry,
                "LedgerPincode"     => $trow->from_cuspincode,
                "LedgerMobile"      => "+91".$trow->from_mobile,
                "LedgerGstReg"      => "Unregistered/Consumer",
                "LedgerGstin"       => "",
                "BillName"          => "",
                "BillDate"          => $trow->INVOICEDATE,
                "PlaceOfSupply"     => "",
                "TransactionDate"   => $trow->INVOICEDATE,
                "CrDr"              => "Dr",
                "Amount"            => number_format($trow->transfer_amount, 2, '.', ''),
                "CostCategory"      => "",
                "CostCentre"        => $trow->CostCentre,
                "BranchCode"        => $trow->brcode,
                "Location"          => $trow->frmaddress1,
                "State"             => $trow->brstate
            );

            // Cr: Destination customer (advance credited)
            $return_data['VOUCHER'][] = array(
                "Autoid"            => $trow->id_advance_transfer,
                "CompanyNumber"     => $trow->sort,
                "TallyMasterid"     => 1,
                "Voucherid"         => "",
                "VoucherNumber"     => $voucher_number,
                "VoucherDate"       => $trow->INVOICEDATE,
                "VoucherType"       => $vouchertype,
                "VoucherTypeParent" => "Sundry Debtors",
                "LedgerName"        => $trow->to_customer_name."-".$trow->to_mobile,
                "LedgerParent"      => "Sundry Debtors",
                "LedgerAddress"     => $trow->to_address,
                "LedgerState"       => $trow->to_cusstate,
                "LedgerCountry"     => $trow->to_cuscountry,
                "LedgerPincode"     => $trow->to_cuspincode,
                "LedgerMobile"      => "+91".$trow->to_mobile,
                "LedgerGstReg"      => "Unregistered/Consumer",
                "LedgerGstin"       => "",
                "BillName"          => "",
                "BillDate"          => $trow->INVOICEDATE,
                "PlaceOfSupply"     => "",
                "TransactionDate"   => $trow->INVOICEDATE,
                "CrDr"              => "Cr",
                "Amount"            => number_format($trow->transfer_amount, 2, '.', ''),
                "CostCategory"      => "",
                "CostCentre"        => $trow->CostCentre,
                "BranchCode"        => $trow->brcode,
                "Location"          => $trow->frmaddress1,
                "State"             => $trow->brstate
            );
        }

        return $return_data;
    }
```

## Verification
1. Create an advance receipt for Customer A (this is `transfer_receipt_id`)
2. Create an advance transfer receipt from Customer A to Customer B (this is `id_issue_receipt`, `receipt_type = 7`)
3. Hit `/index.php/tally_app_api/importreceipts?from_date=YYYY-MM-DD&to_date=YYYY-MM-DD`
4. Confirm `ADVT-{to_bill_no}` appears in the `VOUCHER` array with exactly **2 entries**:
   - Entry 1: `LedgerName = "CustomerA-mobile"`, `CrDr = "Dr"`, correct `Amount`
   - Entry 2: `LedgerName = "CustomerB-mobile"`, `CrDr = "Cr"`, same `Amount`
5. Confirm Dr amount = Cr amount (balanced journal)
6. Confirm the original advance-add receipt still appears separately (GR- prefix, unaffected)
7. Mark `sir_to.istransfered = 1` and re-query — the ADVT- entries should disappear (sync gate works)

## Notes
- **Do NOT place this block inside `getChitUtlizationPayments()`** — that function is not called
  by the `importreceipts` endpoint. Must be inside `getAllreceiptsList()`.
- **Sync gate uses `sir_to.istransfered`** on `ret_issue_receipt` (the destination row), NOT on
  `ret_advance_transfer` itself (which has no `istransfered` column).
- **VoucherNumber prefix is `ADVT-`** (Advance Transfer), distinct from:
  - `GR-` — regular advance receipts
  - `ADVC-` — advance adjustment (Chit utilization, PAT-TALLY-015)
- The `ret_advance_transfer` table schema:
  ```
  id_advance_transfer  INT PK
  id_issue_receipt     INT  → destination ret_issue_receipt.id_issue_receipt
  transfer_receipt_id  INT  → source ret_issue_receipt.id_issue_receipt
  transfer_amount      DECIMAL(10,0)
  otp                  VARCHAR(20)
  ```
- Related recipe: PAT-TALLY-015 (`recipe_advance_adj_chit_missing.md`) — handles `ret_advance_utilized`
  with `adjusted_for = 2` (Chit advance adjustments), a different table/flow.
