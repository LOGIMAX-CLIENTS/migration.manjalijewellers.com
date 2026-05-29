<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ret_other_inventory_model extends CI_Model
{
	const TABLE_NAME 	= "ret_other_inventory_item";
	function __construct()
	{
		parent::__construct();
	}
	// General Functions
	public function insertData($data, $table)
	{
		$insert_flag = 0;
		$insert_flag = $this->db->insert($table, $data);
		return ($insert_flag == 1 ? $this->db->insert_id() : 0);
	}
	public function insertBatchData($data, $table)
	{
		$insert_flag = 0;
		$insert_flag = $this->db->insert_batch($table, $data);
		if ($this->db->affected_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function updateBatchData($data, $table, $id_field, $id_value)
	{
		$insert_flag = 0;
		$this->db->where($id_field, $id_value);
		$updat = $this->db->update_batch($table, $data);
		print_r($this->db->last_query());
		exit;
		if ($this->db->affected_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function updateData($data, $id_field, $id_value, $table)
	{
		$edit_flag = 0;
		$this->db->where($id_field, $id_value);
		$edit_flag = $this->db->update($table, $data);
		return ($edit_flag == 1 ? $id_value : 0);
	}
	public function deleteData($id_field, $id_value, $table)
	{
		$this->db->where($id_field, $id_value);
		$status = $this->db->delete($table);
		return $status;
	}

	function get_currentBranchName($branch_id)
	{
		$branch_name = "";
		$branch_query = $this->db->query("SELECT id_branch, name FROM branch WHERE id_branch = $branch_id");
		if ($branch_query->num_rows() > 0) {
			$branch_name = $branch_query->row()->name;
		}
		return $branch_name;
	}

	function get_headOffice()
	{
		$data = $this->db->query("SELECT b.is_ho,b.id_branch,name FROM branch b where b.is_ho=1");
		return $data->row_array();
	}

	function getBranchDayClosingData($id_branch)
	{
		$sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=" . $id_branch);
		return $sql->row_array();
	}

	function generatePurNo()
	{
		$lastno = NULL;
		$sql = "SELECT MAX(otr_inven_pur_order_ref) as lastorder_no
					FROM ret_other_inventory_purchase o
					ORDER BY otr_inven_pur_id DESC 
					LIMIT 1";
		$result = $this->db->query($sql);
		if ($result->num_rows() > 0) {
			$lastno = $result->row()->lastorder_no;
		}

		if ($lastno != NULL) {
			//$max_num = explode("-",$lastno);
			$number = (int) $lastno;
			$number++;
			$order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		} else {
			$order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}

		return $order_number;
	}

	// function generateItemRefNo()
	// {
	//     $lastno = NULL;
	//     $sql = "SELECT MAX(item_ref_no) as item_ref_no
	// 				FROM ret_other_inventory_purchase_items_details o
	// 				ORDER BY pur_item_detail_id DESC 
	// 				LIMIT 1"; 
	// 		$result = $this->db->query($sql);
	// 		if( $result->num_rows() > 0){
	// 			$lastno = $result->row()->item_ref_no;				
	// 		} 

	//     if($lastno != NULL)
	// 	{ 
	// 	    //$max_num = explode("-",$lastno);
	//         $number = (int) $lastno;
	//         $number++;
	//         $order_number = str_pad($number, 5, '0', STR_PAD_LEFT);	
	// 	}
	// 	else
	// 	{
	//        $order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
	// 	}

	// 	return $order_number;
	// }
	function generateItemRefNo()
	{
		$lastno = NULL;
		$sql = "SELECT MAX(item_ref_no) as item_ref_no
            FROM ret_other_inventory_purchase_items_details o
            ORDER BY pur_item_detail_id DESC 
            LIMIT 1";
		$result = $this->db->query($sql);
		if ($result->num_rows() > 0) {
			$lastno = $result->row()->item_ref_no;
		}
		if ($lastno != NULL) {
			// Extract numeric part and increment
			$matches = [];
			preg_match('/(\d+)$/', $lastno, $matches);
			$number = (int) $matches[0] + 1;
			// Pad with zeros
			$order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		} else {
			$order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $order_number;
	}
	function getlastrefno()
	{
		$sql = $this->db->query("SELECT d.pur_item_detail_id,d.item_ref_no FROM ret_other_inventory_purchase_items_details d where d.ref_no is not null  ORDER by pur_item_detail_id DESC LIMIT 1");
		return $sql->row()->item_ref_no;
	}
	function ajax_getProductlist()
	{
		$id_other_item = $this->db->query("SELECT p.otr_inven_pur_id,IFNULL(op.inv_pur_itm_qty,0) as quantity,
        op.inv_pur_itm_id,p.otr_inven_pur_order_ref as ref_no,IFNULL(SUM(od.piece),0) as no_of_pcs,
		date_format(p.entry_date,'%d-%m-%Y') as entry_date
        FROM ret_other_inventory_purchase p
	    Left join  ret_other_inventory_purchase_items op On op.otr_inven_pur_id = p.otr_inven_pur_id
	    Left join  ret_other_inventory_purchase_items_details od On od.inv_pur_itm_id = op.inv_pur_itm_id
        Where op.inv_pur_itm_qty > 0 and p.purchase_bill_status =1 
        Group by p.otr_inven_pur_order_ref ");
		//print_r($this->db->last_query());exit;
		return $id_other_item->result_array();
	}
	function get_other_inventory_product($id_other_item)
	{
		$id_other_item = $this->db->query("select pro.name as item_name,od.item_ref_no,od.piece,od.amount,
		
		op.otr_inven_pur_id,od.pur_item_detail_id
		from ret_other_inventory_purchase_items_details od
		Left join  ret_other_inventory_purchase_items tm On tm.inv_pur_itm_id = od.inv_pur_itm_id
		Left join ret_other_inventory_purchase op On op.otr_inven_pur_id = tm.otr_inven_pur_id
		
		Left join ret_other_inventory_item pro On pro.id_other_item = od.other_invnetory_item_id
		where op.otr_inven_pur_id =" . $id_other_item);
		return $id_other_item->result_array();
	}
	function get_other_inventory_records($id_other_item)
	{
		$id_other_item = $this->db->query("select id_other_item,name,short_code,sku_id,purchase_id_uom,item_for,item_image,issue_preference,id_inv_size,unit_price, issue_to
		from ret_other_inventory_item  
		where  id_other_item=" . $id_other_item);
		return $id_other_item->row_array();
	}

	function get_inv_item_reorder_details($id_other_item)
	{
		$sql = $this->db->query("SELECT s.id_branch,s.id_other_item,s.min_pcs,s.max_pcs,b.name as branch_name
        FROM ret_other_inventory_reorder_settings s 
        LEFT JOIN branch b ON b.id_branch=s.id_branch
        where s.id_other_item=" . $id_other_item . "");
		return $sql->result_array();
	}
	public function update_other_inventory($data, $id, $id_field, $table)
	{
		$edit_flag = 0;
		$this->db->where($id_field, $id);
		$edit_flag = $this->db->update($table, $data);
		return ($edit_flag == 1 ? $id : 0);
	}
	function getActiveskuid($SearchTxt, $searchField)
	{
		$data = $this->db->query("SELECT ot.id_other_item as value,ot.sku_id as label,name,ot.purchase_id_uom , ot.item_hsn_code,ot.issue_preference,
		IF(ot.purchase_id_uom ='1','GM','CARAT') as uom_name FROM ret_other_inventory_item ot
		WHERE ot.item_hsn_code is NULL AND  ot." . $searchField . " LIKE '%" . $SearchTxt . "%'");
		//print_r($this->db->last_query());exit;
		return $data->result_array();
	}



	// function ajax_getotheritem()
	// {
	// 	$id_other_item_type = $this->db->query("SELECT * FROM ret_other_inventory_item_type ORDER BY id_other_item_type desc");
	// 	return $id_other_item_type->result_array();
	// }

	function ajax_getotheritem()
	{
		$id_other_item_type = $this->db->query("
        SELECT 
            item_type.*, 
            (
                SELECT COUNT(*) 
                FROM ret_other_inventory_item i 
                WHERE i.item_for = item_type.id_other_item_type
            ) as item_type_used_count 
        FROM ret_other_inventory_item_type item_type 
        ORDER BY item_type.id_other_item_type DESC
    ");

		return $id_other_item_type->result_array();
	}

	function get_inventory_category()
	{
		$id_other_item_type = $this->db->query("SELECT * FROM ret_other_inventory_item_type ORDER BY id_other_item_type desc");
		return $id_other_item_type->result_array();
	}

	function get_InventoryCategory($id_other_item_type)
	{
		$id_other_item_type = $this->db->query("SELECT t.id_other_item_type,t.qrcode,i.issue_preference
        FROM ret_other_inventory_item i 
        LEFT JOIN ret_other_inventory_item_type t ON t.id_other_item_type=i.item_for
        WHERE i.id_other_item=" . $id_other_item_type . "");
		//print_r($this->db->last_query());exit;
		return $id_other_item_type->row_array();
	}

	function get_other_item_records($id_other_item_type)
	{
		$id_other_item_type = $this->db->query("select id_other_item_type,name,outward_type,asbillable,expirydatevalidate,reorderlevel 
		from ret_other_inventory_item_type  
		where  id_other_item_type=" . $id_other_item_type);
		//print_r($this->db->last_query());exit;
		return $id_other_item_type->row_array();
	}

	public function update_otheritem($data, $id)
	{
		$edit_flag = 0;
		$this->db->where('id_other_item_type', $id);
		$edit_flag = $this->db->update('ret_other_inventory_item_type', $data);
		//print_r($this->db->last_query());exit;
		return ($edit_flag == 1 ? $id : 0);
	}

	function getActiveItemname()
	{
		$data = $this->db->query("SELECT id_other_item_type,name FROM `ret_other_inventory_item_type` WHERE status = 1");
		return $data->result_array();
	}


	//purchase entry
	function get_other_inventory_item()
	{
		$sql = $this->db->query("SELECT i.id_other_item,i.name
        FROM ret_other_inventory_item i");
		return $sql->result_array();
	}

	function get_supplier()
	{
		$sql = $this->db->query("SELECT id_karigar,id_state,firstname as karigar_name FROM `ret_karigar` WHERE karigar_for=4");
		return $sql->result_array();
	}

	function ajax_getPurchaseEntrylist($data)
	{
		$returnData = array();
		$sql = $this->db->query("SELECT p.otr_inven_pur_id, k.firstname AS supplier_name, DATE_FORMAT(p.entry_date, '%d-%m-%Y') AS entry_date, DATE_FORMAT(p.supplier_bill_date, '%d-%m-%Y') AS supplier_bill_date, IFNULL(p.supplier_order_ref_no, '') AS supplier_order_ref_no, IFNULL(p.otr_inven_pur_order_ref, '') AS pur_order_ref_no, IFNULL(pur.tot_pcs, 0) AS tot_pcs, IFNULL(pur.tot_amount, 0) AS tot_amount, IFNULL(pur_det.no_of_pcs, 0) AS no_of_pcs, IFNULL((pur.tot_pcs - IFNULL(pur_det.no_of_pcs, 0)), 0) AS balance, p.purchase_bill_status, IF(p.purchase_bill_status = 1, 'Success', 'Cancelled') AS bill_status FROM ret_other_inventory_purchase p LEFT JOIN ret_karigar k ON k.id_karigar = p.otr_inven_pur_supplier LEFT JOIN ( SELECT i.otr_inven_pur_id, IFNULL(SUM(i.inv_pur_itm_qty), 0) AS tot_pcs, IFNULL(SUM(i.inv_pur_itm_total), 0) AS tot_amount FROM ret_other_inventory_purchase_items i GROUP BY i.otr_inven_pur_id ) AS pur ON pur.otr_inven_pur_id = p.otr_inven_pur_id LEFT JOIN ( SELECT i.otr_inven_pur_id, IFNULL(SUM(od.piece), 0) AS no_of_pcs FROM ret_other_inventory_purchase_items_details od LEFT JOIN ret_other_inventory_purchase_items i ON i.inv_pur_itm_id = od.inv_pur_itm_id GROUP BY i.otr_inven_pur_id ) AS pur_det ON pur_det.otr_inven_pur_id = pur.otr_inven_pur_id WHERE p.otr_inven_pur_id IS NOT NULL
		" . ($data['id_karigar'] != '' ? " and p.otr_inven_pur_supplier=" . $data['id_karigar'] . "" : '') . "
        " . ($data['from_date'] != '' && $data['to_date'] != '' ? ' and date(p.entry_date) BETWEEN "' . date('Y-m-d', strtotime($data['from_date'])) . '" AND "' . date('Y-m-d', strtotime($data['to_date'])) . '"' : '') . " ");
		// print_r($this->db->last_query());exit;
		$result = $sql->result_array();

		foreach ($result as $items) {

			$items['image_details'] = $this->get_inv_purchase_images($items['otr_inven_pur_id']);
			$items['pur_details'] = $this->get_purchase_item_det($items['otr_inven_pur_id']);
			$returnData[] = $items;
		}


		return $returnData;
	}
	//purchase entry
	function get_inv_purchase_images($pur_id)
	{
		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_purchase_images` where otr_inven_pur_id=" . $pur_id . "");
		return $sql->result_array();
	}

	//stock details
	function other_inventory_stock($data)
	{
		$day_closing = $this->getBranchDayClosingData($data['id_branch']);
		$stock_detail = array();
		$date = ($day_closing['is_day_closed'] == 1 ? $day_closing['entry_date'] : date("Y-m-d"));
		// if(( date('Y-m-d',strtotime($data['from_date'])) !=$date) && (date('Y-m-d',strtotime($data['from_date']))!=$date))
		// {
		// 	$sql = $this->db->query("SELECT s.id_other_item,i.name,
		//     IFNULL(s.op_blc_pcs,0) as op_blc_pcs,IFNULL(s.op_blc_amt,0) as op_blc_amt,
		//     IFNULL(s.inw_pcs,0) as inw_pcs,IFNULL(s.inw_amt,0) as inw_amount,
		//     IFNULL(s.out_pcs,0) as out_pcs,IFNULL(s.out_amt,0) as out_amount
		//     FROM ret_other_inventory_stock s
		//     LEFT JOIN ret_other_inventory_item i ON i.id_other_item=s.id_other_item
		//     where date(s.date) BETWEEN  '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
		//     ".($data['id_branch']!='' ? " and s.id_branch=".$data['id_branch']."" :'')."
		//     ".($data['id_other_item']!='' ? " and s.id_other_item=".$data['id_other_item']."" :'')."
		//     order by s.id_other_item ASC");
		//     $result = $sql->result_array();
		// }
		// else{
		$op_date = date('Y-m-d', (strtotime('-1 day', strtotime($data['from_date']))));
		$sql = $this->db->query("SELECT i.id_other_item,i.name,
		IFNULL(blc.piece,0) as op_blc_pcs,IFNULL(blc.closing_amt,0) as op_blc_amt,
		IFNULL(inw.inw_pcs,0) as inw_pcs,IFNULL(inw.inw_amount,0) as inw_amount,IFNULL(tp.name,'') as type_name,
		IFNULL(br_out.out_pcs,0) as out_pcs,IFNULL(br_out.out_amount,0) as out_amount
        FROM ret_other_inventory_item i
		Left join ret_other_inventory_item_type tp on tp.id_other_item_type = i.item_for
		LEFT JOIN( SELECT l1.item_id,IFNULL(SUM(IF( l1.status=0,l1.no_of_pieces,-l1.no_of_pieces)),0) as piece,IFNULL(SUM(l1.amount),0) as closing_amt
                                    FROM ret_other_inventory_purchase_items_log l1
                                    LEFT JOIN ret_other_inventory_item as item ON item.id_other_item = l1.item_id
                                    WHERE l1.id_item_log  IS NOT NULL
                                    and ( l1.to_branch= " . $data['id_branch'] . " or  l1.from_branch=" . $data['id_branch'] . " )
                                    AND date(l1.date) <= '" . $op_date . "'
                                    GROUP BY item.id_other_item ) as blc on blc.item_id=i.id_other_item
		LEFT JOIN (
		SELECT l.item_id,IFNULL(SUM(l.no_of_pieces),0) as inw_pcs,IFNULL(SUM(l.amount),0) as inw_amount
        FROM ret_other_inventory_purchase_items_log l
		WHERE (date(l.date) BETWEEN '" . date('Y-m-d', strtotime($data['from_date'])) . "' AND '" . date('Y-m-d', strtotime($data['to_date'])) . "') AND l.status=0
		" . ($data['id_branch'] != '' ? " and l.to_branch=" . $data['id_branch'] . "" : '') . "
		GROUP by l.item_id) inw ON inw.item_id=i.id_other_item
		LEFT JOIN (
		SELECT l.item_id,IFNULL(SUM(l.no_of_pieces),0) as out_pcs,IFNULL(SUM(l.amount),0) as out_amount
        FROM ret_other_inventory_purchase_items_log l
		WHERE (date(l.date) BETWEEN '" . date('Y-m-d', strtotime($data['from_date'])) . "' AND '" . date('Y-m-d', strtotime($data['to_date'])) . "') AND (l.status=1 or l.status=4 or l.status=3)
		" . ($data['id_branch'] != '' ? " and l.from_branch=" . $data['id_branch'] . "" : '') . "
		GROUP by l.item_id) br_out ON br_out.item_id=i.id_other_item
		where i.id_other_item is not null
		" . ($data['id_other_item'] != '' ? " and i.id_other_item=" . $data['id_other_item'] . "" : '') . "
		" . ($data['id_other_item_type'] != '' ? " and i.item_for=" . $data['id_other_item_type'] . "" : '') . "
		GROUP by i.id_other_item");
		// }
		//    print_r($this->db->last_query());exit;
		$result = $sql->result_array();
		foreach ($result as $items) {
			$stock_detail[] = array(
				'id_other_item'   => $items['id_other_item'],
				'item_name'       => $items['name'],
				'op_blc_pcs'      => $items['op_blc_pcs'],
				'op_blc_amt'      => $items['op_blc_amt'],
				'inw_pcs'         => $items['inw_pcs'],
				'inw_amount'      => $items['inw_amount'],
				'out_pcs'         => $items['out_pcs'],
				'out_amount'      => $items['out_amount'],
				'type_name'       => $items['type_name'],
				'closing_pcs'     => ($items['op_blc_pcs'] + $items['inw_pcs'] - $items['out_pcs']),
				'closing_amt'     => number_format($items['op_blc_amt'] + $items['inw_amount'] - $items['out_amount'], 3, '.', ''),
			);
		}
		return $stock_detail;
	}
	//stock details
	//stock details

	function get_invnetory_item($data)
	{
		$responseData = array();
		$sql = $this->db->query("SELECT i.name as item_name,IFNULL(d.tot_pcs,0) as tot_pcs,i.id_other_item,IFNULL(i.item_image,'') as item_image,i.sku_id,
        IFNULL(bt.brch_pcs,0) as brch_pcs
        FROM ret_other_inventory_item_type t 
        LEFT JOIN ret_other_inventory_item i ON i.item_for=t.id_other_item_type
		LEFT JOIN (SELECT IFNULL(SUM(p.no_of_pcs),0) as brch_pcs,p.id_other_inv_item
		FROM ret_branch_transfer_other_inventory p
		LEFT JOIN ret_branch_transfer b ON b.branch_transfer_id = p.branch_transfer_id
		WHERE (b.status = 1) 
		GROUP BY p.id_other_inv_item) as bt ON bt.id_other_inv_item = i.id_other_item
        LEFT JOIN (SELECT d.other_invnetory_item_id,IFNULL(SUM(d.piece),0) as tot_pcs
        FROM ret_other_inventory_purchase_items_details d
        WHERE d.status=0 
        " . ($data['id_branch'] != '' ? " and d.current_branch=" . $data['id_branch'] . "" : '') . " 
        GROUP by d.other_invnetory_item_id) as d on d.other_invnetory_item_id=i.id_other_item
        having tot_pcs>0");
		//print_r($this->db->last_query());exit;
		$result = $sql->result_array();
		foreach ($result as $items) {
			$responseData[] = array(
				'item_name'    => $items['item_name'],
				'tot_pcs'      => $items['tot_pcs'],
				'brch_pcs'      => $items['brch_pcs'],
				'id_other_item' => $items['id_other_item'],
				'item_image'   => $items['item_image'],
				'sku_id'       => $items['sku_id'],
			);
		}
		return $responseData;
	}



	function get_customer()
	{
		$sql = $this->db->query("SELECT cus.id_customer,concat(cus.firstname,'-',cus.mobile) as cus_name
        FROM customer cus 
        WHERE cus.active=1");
		return $sql->result_array();
	}


	function get_other_inventory_purchase_items_details($id_other_item, $id_branch, $issue_preference, $total_pcs)
	{
		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_purchase_items_details` 
        WHERE other_invnetory_item_id=" . $id_other_item . " AND current_branch=" . $id_branch . " AND status=0
        " . ($issue_preference == 1 ? 'order by pur_item_detail_id ASC' : 'order by pur_item_detail_id DESC') . "
        LIMIT " . $total_pcs . "");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	function get_OtherInventoryIssueDetails($data)
	{
		$sql = $this->db->query("SELECT i.id_inventory_issue,date_format(i.issue_date,'%d-%m-%Y') as issue_date,br.name as branch_name,i.no_of_pieces,IFNULL(s.tot_amount,0) as approx_amt,IFNULL(i.remarks,'') as remarks,
        cus.firstname as cus_name,emp.firstname as given_by,t.name as item_name,IFNULL(bill.bill_no,'') as bill_no,IFNULL(i.bill_id,'') as bill_id
        FROM ret_other_invnetory_issue i 
        LEFT JOIN branch br ON br.id_branch=i.id_branch
        LEFT JOIN employee emp ON emp.id_employee=i.created_by
        LEFT JOIN ret_other_inventory_item t ON t.id_other_item=i.id_other_item
        LEFT JOIN ret_billing bill ON bill.bill_id=i.bill_id
        LEFT JOIN customer cus ON cus.id_customer=bill.bill_cus_id
        LEFT JOIN(SELECT d.id_inventory_issue,IFNULL(SUM(d.amount),0) as tot_amount
        FROM ret_other_inventory_purchase_items_details d
        GROUP by d.id_inventory_issue) as s ON s.id_inventory_issue=i.id_inventory_issue
        where " . ($data['from_date'] != '' && $data['to_date'] != '' ? ' date(i.issue_date) BETWEEN "' . date('Y-m-d', strtotime($data['from_date'])) . '" AND "' . date('Y-m-d', strtotime($data['to_date'])) . '"' : '') . "
        " . ($data['id_branch'] != '' && $data['id_branch'] != 0 ? " and i.id_branch=" . $data['id_branch'] . "" : '') . "");
		return $sql->result_array();
	}

	public function skuid_available($sku_id, $id_other_item = "")
	{
		$this->db->select('sku_id');
		$this->db->where('sku_id', $sku_id);
		$status = $this->db->get('ret_other_inventory_item');
		if ($status->num_rows() > 0) {
			return TRUE;
		}
	}



	//Size Master
	function ajax_getOtherInventorySizeList($data)
	{
		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_size`");
		return $sql->result_array();
	}

	function get_packaging_size($id)
	{
		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_size` where id_inv_size=" . $id . "");
		//print_r($this->db->last_query());exit;
		return $sql->row_array();
	}

	function get_ActivePackagingItemSize()
	{
		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_size` where status=1");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}
	//Size Master


	function get_bill_details($data)
	{
		$dcData = $this->getBranchDayClosingData($data['id_branch']);
		$sql = $this->db->query("SELECT b.bill_id,concat(b.bill_no,'-',cus.mobile) as cus_bill_no
        FROM ret_billing b 
        LEFT JOIN customer cus ON cus.id_customer=b.bill_cus_id
        WHERE b.bill_status=1 and b.id_branch=" . $data['id_branch'] . " and date(b.bill_date)='" . $dcData['entry_date'] . "'");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}


	//Available Stock Details
	function get_AvailableStockDetails($data)
	{
		$sql = $this->db->query("SELECT i.name as item_name,i.sku_id,s.size_name,SUM(d.piece) as tot_pcs,SUM(d.amount) as tot_amount,br.name as branch_name,d.other_invnetory_item_id,
        IFNULL(tp.name,'') as type_name
        FROM ret_other_inventory_purchase_items_details d 
        LEFT JOIN ret_other_inventory_item i ON i.id_other_item=d.other_invnetory_item_id
		Left join ret_other_inventory_item_type tp on tp.id_other_item_type = i.item_for
        LEFT JOIN ret_other_inventory_size s ON s.id_inv_size=i.id_inv_size
        LEFT JOIN branch br ON br.id_branch=d.current_branch
        WHERE d.status=0 
        " . ($data['id_branch'] != '' && $data['id_branch'] != 0 ? " and d.current_branch=" . $data['id_branch'] . "" : '') . "
        " . ($data['id_inv_size'] != '' ? " and s.id_inv_size=" . $data['id_size'] . "" : '') . "
        " . ($data['id_other_item'] != '' ? " and d.other_invnetory_item_id=" . $data['id_other_item'] . "" : '') . "
		" . ($data['id_other_item_type'] != '' ? " and i.item_for=" . $data['id_other_item_type'] . "" : '') . "
        GROUP by d.current_branch,d.other_invnetory_item_id");
		return $sql->result_array();
	}
	function get_ActiveCategory()
	{
		$sql = $this->db->query("SELECT * FROM ret_other_inventory_item_type tp WHERE tp.status=1");
		return $sql->result_array();
	}
	//Available Stock Details

	//Product Mapping
	function get_ActiveProduct()
	{
		$sql = $this->db->query("SELECT p.pro_id FROM ret_product_master p WHERE p.product_status=1");
		return $sql->result_array();
	}

	function check_other_inv_products_maping($id_product, $inv_des_otheritemid)
	{
		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_product_link` WHERE inv_pro_id=" . $id_product . " AND inv_des_otheritemid=" . $inv_des_otheritemid . " ");
		//print_r($this->db->last_query());exit;
		if ($sql->num_rows() == 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function get_item_mapping_details($data)
	{
		$sql = $this->db->query("SELECT p.inv_des_id,pro.pro_id,pro.product_name,i.id_other_item,i.name as item_name,s.size_name
        FROM ret_other_inventory_product_link p 
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.inv_pro_id
        LEFT JOIN ret_other_inventory_item i ON i.id_other_item=p.inv_des_otheritemid
        LEFT JOIN ret_other_inventory_size s ON s.id_inv_size=i.id_inv_size
        where p.inv_des_id IS NOT NULL 
        " . ($data['id_product'] != '' ? " and p.inv_pro_id=" . $data['id_product'] . "" : '') . "
        " . ($data['id_other_item'] != '' ? " and p.inv_des_otheritemid=" . $data['id_other_item'] . "" : '') . "
        ");
		return $sql->result_array();
	}


	function get_productMappedDetails($id_branch)
	{
		$responseData = array();
		$sql = $this->db->query("SELECT p.pro_id FROM ret_product_master p WHERE p.product_status=1");
		$result = $sql->result_array();
		foreach ($result as $items) {
			$responseData[] = array(
				'pro_id' => $items['pro_id'],
				'item_details' => $this->get_product_linked_items($items['pro_id'], $id_branch),
			);
		}
		return $responseData;
	}


	function get_product_linked_items($pro_id, $id_branch)
	{
		$sql = $this->db->query("SELECT i.id_other_item,i.name as item_name,IFNULL(i.item_image,'') as item_image,IFNULL(d.tot_pcs,0) as tot_pcs,i.sku_id
        FROM ret_other_inventory_product_link l 
        LEFT JOIN ret_other_inventory_item i ON i.id_other_item=l.inv_des_otheritemid
        LEFT JOIN (
            SELECT d.other_invnetory_item_id,IFNULL(SUM(d.piece),0) as tot_pcs
            FROM ret_other_inventory_purchase_items_details d
            WHERE d.status=0 AND d.current_branch=" . $id_branch . "
            GROUP by d.other_invnetory_item_id
        ) as d on d.other_invnetory_item_id=i.id_other_item
        where inv_pro_id=" . $pro_id . " GROUP by i.id_other_item");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	//Product Mapping


	//Reorder Report
	function get_reorder_report($data)
	{
		$id_branch=$this->session->userdata('id_branch');
		if($data['id_branch']!='' && $data['id_branch']!=0)
    	{
    	    $id_branch=$data['id_branch'];
    	}
		$sql = $this->db->query("SELECT s.id_branch,s.id_other_item,s.min_pcs,s.max_pcs,br.name as branch_name,i.name as item_name,IFNULL(d.tot_pcs,0) as available_pcs
        FROM ret_other_inventory_reorder_settings s 
        LEFT JOIN ret_other_inventory_item i ON i.id_other_item=s.id_other_item
        LEFT JOIN branch br ON br.id_branch=s.id_branch
        LEFT JOIN(SELECT d.other_invnetory_item_id,SUM(d.piece) as tot_pcs,d.current_branch
        FROM ret_other_inventory_purchase_items_details d 
        WHERE d.status=0 " . ($id_branch != '' ? " and d.current_branch=" . $id_branch . "" : '') . "
        GROUP by d.current_branch,d.other_invnetory_item_id) as d ON d.other_invnetory_item_id=s.id_other_item AND d.current_branch=s.id_branch
        WHERE s.id_inv_reorder_settings IS NOT NULL
        " . ($id_branch != '' ? " and s.id_branch=" . $id_branch . "" : '') . "");
		return $sql->result_array();
	}
	//Reorder Report

	function get_other_inventory_print($ref_no)
	{
		$return_data = array();

		$sql = $this->db->query(
			"SELECT pd.item_ref_no,pi.inv_pur_itm_id,itm.id_other_item,itm.name as pro_name, op.otr_inven_pur_supplier
			from ret_other_inventory_purchase op 
			left join ret_other_inventory_purchase_items pi on pi.otr_inven_pur_id =op.otr_inven_pur_id
			left join ret_other_inventory_purchase_items_details pd on pd.inv_pur_itm_id = pi.inv_pur_itm_id 
			left join ret_other_inventory_item itm on itm.id_other_item =pd.other_invnetory_item_id
			where pd.item_ref_no ='" . $ref_no . "' "
		);

		return $sql->result_array();
	}

	function get_product_other_inventory_print($ref_no)
	{
		$return_data = array();

		$sql = $this->db->query(
			"SELECT pd.item_ref_no,pi.inv_pur_itm_id,itm.id_other_item,itm.name as pro_name, op.otr_inven_pur_supplier
			from ret_other_inventory_purchase op 
			left join ret_other_inventory_purchase_items pi on pi.otr_inven_pur_id =op.otr_inven_pur_id
			left join ret_other_inventory_purchase_items_details pd on pd.inv_pur_itm_id = pi.inv_pur_itm_id 
			left join ret_other_inventory_item itm on itm.id_other_item =pd.other_invnetory_item_id
			where pd.ref_no  ='" . $ref_no . "' "
		);
		return $sql->result_array();
	}

	function ajax_get_other_inventory()
	{
		$id_other_item = $this->db->query("SELECT i.id_other_item,i.name,i.short_code,i.purchase_id_uom,stock_id_uom,i.sku_id,IFNULL(i.item_image,'') as image,u.uom_name,
		
		i.name as item_name,IF(i.issue_preference = 1,'FIFO','FILO') as issue_preference,i.unit_price,
		IFNULL(s.size_name,'') as size,IFNULL(tp.name,'') as type_name,i.qr_image, 
		(SELECT COUNT(*) 
         FROM ret_other_inventory_purchase_items roipi 
         WHERE roipi.inv_pur_itm_itemid = i.id_other_item) as item_used_count
		FROM ret_other_inventory_item i
		Left join ret_uom u on u.uom_id= i.purchase_id_uom
		Left join ret_other_inventory_item_type tp on tp.id_other_item_type = i.item_for
		Left join ret_other_inventory_size s on s.id_inv_size = i.id_inv_size
		ORDER BY id_other_item desc");
		return $id_other_item->result_array();
	}
	//Adding scheme map for gift code starts
	public function get_inv_chit_gift($id)
	{
		$sql = "SELECT * FROM gift_mapping where id_other_item=" . $id;
		$result = $this->db->query($sql);
		return $result->result_array();
	}
	public function delete_gift_map_data($id)
	{
		$edit_flag = 0;
		$sql = "DELETE from gift_mapping where id_other_item=" . $id;
		$edit_flag = $this->db->query($sql);
		return ($edit_flag == 1 ? $id : 0);
	}
	//Adding scheme map for gift code ends
	function get_other_inventory_product_det($data)
	{
		$sql = $this->db->query("SELECT i.id_other_item,i.name,IFNULL(op.inv_pur_itm_qty,0) as no_of_pcs,
        IFNULL(od.piece,0) as tag,p.otr_inven_pur_id,op.inv_pur_itm_itemid,op.inv_pur_itm_id,
		IFNULL((op.inv_pur_itm_qty - IFNULL(od.piece,0)), 0) as balance,IFNULL(op.inv_pur_itm_rate,0) as rate
        FROM ret_other_inventory_item i
	    Left join  ret_other_inventory_purchase_items op On op.inv_pur_itm_itemid = i.id_other_item
		Left join  ret_other_inventory_purchase p On p.otr_inven_pur_id = op.otr_inven_pur_id 
	    Left join  ret_other_inventory_purchase_items_details od On od.inv_pur_itm_id = op.inv_pur_itm_id
		 Where i.id_other_item is not null
		 " . ($data['id_other_item'] != '' ? " and op.otr_inven_pur_id =" . $data['id_other_item'] . "" : '') . "
		");

		// print_r($this->db->last_query());exit;
		return $sql->result_array();
	}
	function get_other_inventory_details($data)
	{
		$sql = $this->db->query("SELECT i.id_other_item,IFNULL(i.name,'') as product_name,IFNULL(op.inv_pur_itm_qty,0) as no_of_pcs,
        IFNULL((tag.tag_pcs),0) as tag,p.otr_inven_pur_id,op.inv_pur_itm_itemid,op.inv_pur_itm_id,
		IFNULL((sum(op.inv_pur_itm_qty) - IFNULL((tag.tag_pcs),0)), 0) as balance,IFNULL(op.inv_pur_itm_rate,0) as rate
        FROM ret_other_inventory_item i
	    Left join  ret_other_inventory_purchase_items op On op.inv_pur_itm_itemid = i.id_other_item
		Left join  ret_other_inventory_purchase p On p.otr_inven_pur_id = op.otr_inven_pur_id 
	    LEFT JOIN (SELECT IFNULL(SUM(d.piece),0) as tag_pcs,d.other_invnetory_item_id
                  FROM ret_other_inventory_purchase_items_details d 
                  LEFT JOIN ret_other_inventory_purchase_items r ON r.inv_pur_itm_id = d.inv_pur_itm_id
                  LEFT JOIN ret_other_inventory_purchase pur ON pur.otr_inven_pur_id = r.otr_inven_pur_id
				  where d.other_invnetory_item_id IS NOT NULL
				" . ($data['id_other_item'] != '' ? " and r.otr_inven_pur_id =" . $data['id_other_item'] . "" : '') . "
                  GROUP BY d.other_invnetory_item_id) 
				  as tag ON tag.other_invnetory_item_id = op.inv_pur_itm_itemid
				  
		 Where i.id_other_item is not null 
		 " . ($data['id_other_item'] != '' ? " and op.otr_inven_pur_id =" . $data['id_other_item'] . "" : '') . "
         " . ($data['item_id'] != '' ? " and i.id_other_item =" . $data['item_id'] . "" : '') . "
        
		 group by i.id_other_item
		 Having balance > 0
		 
		 order by i.id_other_item ASC");

		// print_r($this->db->last_query());exit;
		return $sql->result_array();
	}
	function get_other_inventory_ref_no()
	{
		/*$sql=$this->db->query("SELECT p.otr_inven_pur_order_ref,p.otr_inven_pur_id,op.inv_pur_itm_itemid,
		
		IFNULL((op.inv_pur_itm_qty - IFNULL(SUM(od.piece),0)),0) as balance
        FROM ret_other_inventory_purchase p
	    Left join  ret_other_inventory_purchase_items op On op.otr_inven_pur_id = p.otr_inven_pur_id
		Left join  ret_other_inventory_item i On i.id_other_item = op.inv_pur_itm_itemid
		Left join  ret_other_inventory_purchase_items_details od On od.inv_pur_itm_id = op.inv_pur_itm_id
        Where i.id_other_item is not null
	    GROUP BY p.otr_inven_pur_order_ref HAVING balance > 0 
		
		");*/


		$sql = $this->db->query("SELECT p.otr_inven_pur_order_ref,p.otr_inven_pur_id,op.inv_pur_itm_itemid,
		(IFNULL(SUM(op.inv_pur_itm_qty),0)-IFNULL(tag.tag_pcs,0)) as balance
		
        FROM ret_other_inventory_purchase p
        
	    Left join  ret_other_inventory_purchase_items op On op.otr_inven_pur_id = p.otr_inven_pur_id
	    
		Left join  ret_other_inventory_item i On i.id_other_item = op.inv_pur_itm_itemid
		
        LEFT JOIN (SELECT IFNULL(SUM(d.piece),0) as tag_pcs,pur.otr_inven_pur_id
                  FROM ret_other_inventory_purchase_items_details d 
                  LEFT JOIN ret_other_inventory_purchase_items r ON r.inv_pur_itm_id = d.inv_pur_itm_id
                  LEFT JOIN ret_other_inventory_purchase pur ON pur.otr_inven_pur_id = r.otr_inven_pur_id
                  GROUP BY pur.otr_inven_pur_id) as tag ON tag.otr_inven_pur_id = p.otr_inven_pur_id
        Where i.id_other_item is not null
	    GROUP BY p.otr_inven_pur_id");
		return $sql->result_array();
	}
	//Purchase Entry Print Copy
	function getPurchaseDet($id)
	{

		$return_data = array();
		$sql = $this->db->query("SELECT p.otr_inven_pur_id,k.firstname as supplier_name,date_format(p.entry_date,'%d-%m-%Y') as entry_date,date_format(p.supplier_bill_date,'%d-%m-%Y') as supplier_bill_date,
	
	IFNULL(p.supplier_order_ref_no,'') as supplier_order_ref_no,IFNULL(p.otr_inven_pur_order_ref,'') as pur_order_ref_no,
	IFNULL(pur.tot_pcs,0) as tot_pcs,IFNULL(pur.tot_amount,0) as tot_amount,st.state_code as supplier_state_code,
	k.contactno1 as mobile,cy.name as supplier_country_name,st.name as supplier_state_name,ct.name as supplier_city_name,
	IFNULL(k.address1,'') as supplier_address1,IFNULL(k.address2,'') as supplier_address2,st.id_state,
    IFNULL(k.address3,'') as supplier_address3,IFNULL(k.pincode,'') as supplier_pincode,k.gst_number,k.email,
	
	k.contactno1 as supplier_mobile,IFNULL(k.pan_no,'') as pan_no,emp.firstname as emp_name
	FROM ret_other_inventory_purchase p 
	LEFT JOIN ret_karigar k ON k.id_karigar=p.otr_inven_pur_supplier
	LEFT JOIN country cy ON cy.id_country = k.id_country
	LEFT JOIN state st ON st.id_state = k.id_state
	LEFT JOIN city ct ON ct.id_city = k.id_city
	LEFT JOIN employee emp ON emp.id_employee=p.otr_inven_pur_created_by
	LEFT JOIN(SELECT i.otr_inven_pur_id,IFNULL(i.inv_pur_itm_qty,0) as tot_pcs,IFNULL(i.inv_pur_itm_total,0) as tot_amount
	FROM ret_other_inventory_purchase_items i 
	GROUP by i.otr_inven_pur_id) as pur ON pur.otr_inven_pur_id=p.otr_inven_pur_id
	where p.otr_inven_pur_id IS NOT NULL and
	p.otr_inven_pur_id =" . $id . "");
		$result = $sql->result_array();

		foreach ($result as $r) {
			$return_data = $r;
		}
		return $return_data;
	}
	//Purchase Entry Print Copy
	function get_purchase_item_det($id)
	{

		$returnData = array();
		$sql = $this->db->query("SELECT p.otr_inven_pur_id,date_format(p.entry_date,'%d-%m-%Y') as entry_date,
	
	date_format(p.supplier_bill_date,'%d-%m-%Y') as supplier_bill_date,IFNULL(inv.name,'') as product_name,
	
	IFNULL(p.supplier_order_ref_no,'') as supplier_order_ref_no,IFNULL(p.otr_inven_pur_order_ref,'') as pur_order_ref_no,
	IFNULL(pur.inv_pur_itm_qty,0) as tot_pcs,IFNULL(pur.inv_pur_itm_total,0) as tot_amount,
	IFNULL(pur.inv_pur_itm_rate,0) as tot_rate,IFNULL(pur.inv_pur_itm_gst,0) as pur_gst,
    IFNULL(pur.gst_amount,0) as gst_amount
	FROM ret_other_inventory_purchase_items pur
	LEFT JOIN ret_other_inventory_purchase as p ON p.otr_inven_pur_id=pur.otr_inven_pur_id
	LEFT JOIN ret_other_inventory_item as inv ON inv.id_other_item = pur.inv_pur_itm_itemid
	where p.otr_inven_pur_id IS NOT NULL and
	pur.otr_inven_pur_id =" . $id . "
	
    ");

		//   print_r($this->db->last_query());exit;
		return  $sql->result_array();
	}
	function get_purchase_gst_det($id)
	{

		$returnData = array();
		$sql = $this->db->query("SELECT pur.otr_inven_pur_id,IFNULL(pur.inv_pur_itm_rate,0) as tot_rate,
	
	IFNULL(pur.inv_pur_itm_gst,0) as pur_gst,IFNULL(SUM(pur.gst_amount),0) as gst_amount
	FROM ret_other_inventory_purchase_items pur
	where pur.otr_inven_pur_id IS NOT NULL and
	pur.otr_inven_pur_id =" . $id . "
	 
	Group by pur.inv_pur_itm_gst");

		return  $sql->result_array();
	}
	//Purchase Entry Print Copy
	function get_ref_no_details($id)
	{
		$sql = $this->db->query("SELECT ref_no from ret_other_inventory_purchase_items_details 
		
		where pur_item_detail_id =" . $id);
		return $sql->row()->ref_no;
	}

	// Method to fetch all size names
	public function get_all_sizes()
	{
		$sql = $this->db->query("SELECT size_name from ret_other_inventory_size");
		return  $sql->result_array();
	}

	public function CheckIsNameDuplicate($name){

		$sql = $this->db->query("SELECT name as cat_name FROM ret_other_inventory_item_type
		WHERE name ='".$name."' ");

		if($sql->num_rows() > 0){
			$return_data = array('status' =>false,'message' => 'Name already exists.');
			
		}
		else{
			$return_data = array('status' =>true, 'message' => '');
		}

		// print_r($sql->num_rows());exit;
		return $return_data;

	}
}
