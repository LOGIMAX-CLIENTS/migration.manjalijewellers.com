<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class ret_tally_api_model extends CI_Model

{ 

    const EMP_IMG_PATH = 'assets/img/employee';

    const CUS_IMG_PATH = 'assets/img/customer';

    const RETCAT_IMG_PATH = 'assets/img/ret_category';

    const RETPRO_IMG_PATH = 'assets/img/ret_product';

    const RETDES_IMG_PATH = 'assets/img/designs';

	function __construct()

    {      

        parent::__construct(); 

		

	} 
	
	function getbranchtransferInitiatedList(){
		$return_data = array();
		$trans_query = $this->db->query("SELECT date(created_time) as VoucherDate, branch_trans_code as VoucherNumber, 
										fbr.name as FromBranchName, tbr.name as ToBranchName, branch_transfer_id 
										FROM `ret_branch_transfer` as bt 
										LEFT JOIN branch as fbr ON fbr.id_branch = bt.transfer_from_branch 
										LEFT JOIN branch as tbr ON tbr.id_branch = bt.transfer_to_branch 
										WHERE transfer_item_type = 1 AND transfer_from_branch = 1 AND status = 1");
		$return_data = $trans_query->result_array();
		foreach($return_data as $rkey => $rval){
			$return_data[$rkey]['TransferDetails'] = $this->getbranchtrasferDetails($rval['branch_transfer_id']);
		}
		return $return_data;
	}
	
	function getbranchtrasferDetails($tranid){
		$return_data = array();
		$detail_query = $this->db->query("SELECT pr.product_name as ItemName, 
										sum(tag.gross_wt) as GrossWeight, 
										sum(tag.net_wt) as NetWeight, sum(tag.piece) as Quantity, 
										sum(tag.less_wt) as LesWeight, 
										cat.hsn_code as HSNCode, cat.name as CategoryName 
										FROM `ret_brch_transfer_tag_items` trtag 
										LEFT JOIN ret_branch_transfer as bt ON bt.branch_transfer_id = trtag.transfer_id 
										LEFT JOIN ret_taging as tag ON tag.tag_id = trtag.tag_id 
										LEFT JOIN ret_product_master as pr ON pr.pro_id = tag.product_id 
										LEFT JOIN ret_category as cat ON cat.id_ret_category = pr.cat_id 
										WHERE transfer_id = '".$tranid."' 
										GROUP BY cat.id_ret_category, pr.pro_id");
										
										//fbr.name as frombranch, tbr.name as tobranch 
										//LEFT JOIN branch as fbr ON fbr.id_branch = bt.transfer_from_branch 
										//LEFT JOIN branch as tbr ON tbr.id_branch = bt.transfer_to_branch 
		return $detail_query->result_array();
	}
	
	function getsalesList(){
	    $return_data = array();
	    $sql=$this->db->query("SELECT d.bill_det_id, date(b.bill_date) as INVOICEDATE,b.sales_ref_no as INVOICENO,'Sales' as VOUCHERTYPE,
        '' as PARTYCODE,cus.firstname as PARTYNAME,'' as PARTYGROUP,IFNULL(cus.gst_number,'') as GSTNO,
        IFNULL(del.address1,'') as ADRESS1,IFNULL(del.address2,'') as ADRESS2,IFNULL(del.address3,'') as ADRESS3,
        cus.mobile as CONTACTNO,cat.cat_code as PRODUCTCODE,cat.name as PRODUCTNAME,
        d.piece as QTY,d.rate_per_grm as RATE,d.item_cost as VALUE,'' as SALESTAX,
        IFNULL(d.item_total_tax,0) as SALESTAXAMT,d.gross_wt as WEIGHT,d.item_cost as TOTAL,IFNULL(b.remark,'') as REMARKS,
        IFNULL(d.gross_wt,0) as GROSSWT,IFNULL(d.net_wt,0) as NETWT,
        '' as SERVICEAMT,'Trade Debtors' as LedgerParent, b.id_branch , b.bill_id, br.name as CostCentre, IFNULL(d.bill_discount,0) as bill_discount 
        FROM ret_bill_details d 
        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category=pro.cat_id
        LEFT JOIN customer cus ON cus.id_customer=b.bill_cus_id
        LEFT JOIN ret_bill_delivery del ON del.bill_id=b.bill_id 
        LEFT JOIN branch br ON br.id_branch = b.id_branch 
        WHERE b.bill_status=1 and b.sales_ref_no IS NOT NULL 
        and date(b.bill_date) = '".date('Y-m-d')."' and b.id_branch=2");
        
        foreach($sql->result() as $row){
            $return_data['VOUCHER'][] = array(
                    "Autoid"                =>   $row->bill_det_id,
                    "CompanyNumber"         =>   $row->id_branch,
                    "TallyMasterid"         =>   1,
                    "Voucherid"             =>   "",
                    "VoucherNumber"         =>   $row->INVOICENO,
                    "VoucherDate"           =>   $row->INVOICEDATE,
                    "VoucherType"           =>   $row->VOUCHERTYPE,
                    "VoucherTypeParent"     =>   $row->VOUCHERTYPE,
                    "LedgerName"            =>   $row->PARTYNAME,
                    "LedgerParent"          =>   $row->LedgerParent,
                    "BillName"              =>   $row->bill_id,
                    "BillDate"              =>   $row->INVOICEDATE,
                    "CrDr"                  =>   "Dr",
                    "CostCategory"          =>   "",
                    "CostCentre"            =>   $row->CostCentre, 
                    "Stockitem"             =>   $row->PRODUCTNAME,
                    "Godown"                =>   $row->CostCentre, 
                    "BatchNo"               =>   $row->INVOICENO,
                    "Quantity"              =>   $row->GROSSWT,
                    "Rate"                  =>   $row->RATE,
                    "Discount"              =>   $row->bill_discount,
                    "Amount"                =>   $row->TOTAL
                );
        }
        
        return $return_data;
	}
	
}