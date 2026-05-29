<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ret_purchase_order_model extends CI_Model
{
	function __construct()
    {
        parent::__construct();
    }
    // General Functions

    public function insertData($data, $table)
    {
        $query = $this->db->query("SHOW COLUMNS FROM `$table`");
        $columns = $query->result_array();

        
        $default_values = [];
        foreach ($columns as $column) {
            if (!is_null($column['Default'])) {
                $default_values[$column['Field']] = $column['Default'];
            } else {
                // If no default value, use an empty string or null based on column nullability
                $default_values[$column['Field']] = $column['Null'] === 'YES' ? null : '';
            }
        }
        
        foreach ($data as $field => $value) {
            // If the value is empty, set it to the default value
            
            if ((empty($value) || $value == 'null')  ) {
                // $data[$field] = $default_values[$field];

                if($value === 0 || $value === '0'){
                    $data[$field] = 0;
                }else{
                    $data[$field] = $default_values[$field];
                }
            
            }
        }
    
        $insert_flag = $this->db->insert($table, $data);

        return ($insert_flag == 1 ? $this->db->insert_id() : 0);
    }

    public function updateData($data, $id_field, $id_value, $table)
    {    
        $query = $this->db->query("SHOW COLUMNS FROM `$table`");
        $columns = $query->result_array();

        
        $default_values = [];
        foreach ($columns as $column) {
            if (!is_null($column['Default'])) {
                $default_values[$column['Field']] = $column['Default'];
            } else {
                // If no default value, use an empty string or null based on column nullability
                $default_values[$column['Field']] = $column['Null'] === 'YES' ? null : '';
            }
        }
        foreach ($data as $field => $value) {
            // If the value is empty, set it to the default value
            
            if ((empty($value) || $value == 'null')) {
                if($value === 0 || $value === '0'){
                    $data[$field] = 0;
                }else{
                    $data[$field] = $default_values[$field];
                }
            
            }
        }

        $edit_flag = 0;

        $this->db->where($id_field, $id_value);

        $edit_flag = $this->db->update($table,$data);

        return ($edit_flag==1?$id_value:0);

    }
    
    // public function insertData($data,$table)
    // {
    // 	$insert_flag = 0;
	// 	$insert_flag = $this->db->insert($table, $data);
	// 	return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	// }
	public function insertBatchData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert_batch($table, $data);
		if ($this->db->affected_rows() > 0){
			return $this->db->insert_id();
		}else{
			return FALSE;
		}
	}
	public function updateBatchData($data,$table,$id_field,$id_value)
    {
    	$insert_flag = 0;
		$this->db->where($id_field,$id_value);
		$updat=$this->db->update_batch($table,$data);
		print_r($this->db->last_query());exit;
		if ($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	// public function updateData($data,$id_field,$id_value,$table)
    // {
	//     $edit_flag = 0;
	//     $this->db->where($id_field,$id_value);
	// 	$edit_flag = $this->db->update($table,$data);
	// 	return ($edit_flag==1?$id_value:0);
	// }
	public function updateMultipleWhereData($data,$where,$table)
    {
	    $edit_flag = 0;
	    $this->db->where($where);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?1:0);
	}
	public function deleteData($id_field,$id_value,$table)
    {
        $this->db->where($id_field, $id_value);
        $status= $this->db->delete($table);
		return $status;
	}
	function get_FinancialYear()
	{
		$sql=$this->db->query("SELECT fin_year_code From ret_financial_year where fin_status=1");
		return $sql->row_array();
	}
	function generatePurNo()
	{
	    $lastno = NULL;
	    $sql = "SELECT MAX(pur_no) as lastorder_no
					FROM customerorder o
					ORDER BY id_customerorder DESC
					LIMIT 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->lastorder_no;
			}
	    if($lastno != NULL)
		{
            $max_num = explode("-",$lastno);
            $number = (int) $max_num[1];
            //$number = (int) $lastno;
            $number++;
            $order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $order_number;
	}
	function generatePaymentNo()
	{
	    $lastno = NULL;
	    $sql = "SELECT MAX(pay_refno) as lastorder_no
					FROM ret_po_payment p
					ORDER BY pay_id DESC
					LIMIT 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->lastorder_no;
			}
	    if($lastno != NULL)
		{
		    //$max_num = explode("-",$lastno);
            $number = (int) $lastno;
            $number++;
            $order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $order_number;
	}
	function generatePurRefOrderNo($is_suspense_stock,$gst_bill_type="")
	{
	    $fin_year = $this->get_FinancialYear();
	    // PUR-INT01: Use FOR UPDATE to prevent concurrent reads getting the same max number
	    $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(po_ref_no, '-', -1) AS UNSIGNED)) as max_num
	            FROM ret_purchase_order
	            WHERE fin_year_code = ? AND is_suspense_stock = ?
	            FOR UPDATE";
	    $result = $this->db->query($sql, array($fin_year['fin_year_code'], $is_suspense_stock));
	    $max_num = (int) $result->row()->max_num;
	    return str_pad($max_num + 1, 5, '0', STR_PAD_LEFT);
	}
	function generate_approval_refno()
	{
	    $lastno = NULL;
	    $sql = "SELECT (ref_no) as lastno FROM ret_supplier_rate_cut ORDER BY id_supplier_rate_cut DESC LIMIT 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->lastno;
			}
	    if($lastno != NULL)
		{
            $max_num = explode("-",$lastno);
            $number = (int) $max_num[1];
            //$number = (int) $lastno;
            $number++;
            $refno = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $refno = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $refno;
	}
	function get_ActiveWeightRange($data)
	{
	    $sql=$this->db->query("SELECT w.id_weight,w.weight_description as name,w.from_weight,w.to_weight,w.value
        FROM ret_weight w
        LEFT JOIN ret_uom m ON m.uom_id=w.id_uom
        WHERE w.id_weight IS NOT NULL AND w.id_product=".$data['id_product']."");
        return $sql->result_array();
	}
	function get_purchase_order_Details($data)
	{
        $sql=$this->db->query("SELECT c.id_customerorder,IFNULL(c.order_pcs,0) as order_pcs,
        IFNULL(c.order_approx_wt,0) as order_approx_wt,IFNULL(c.delivered_wt,0) as delivered_wt,
        IFNULL(c.delivered_qty,0) as delivered_qty,k.firstname as karigar_name,
        date_format(c.order_date,'%d-%m-%Y') as order_date,c.pur_no,IFNULL(k.contactno1,'') as mobile,
        if(c.order_for=1,'Stock Order',if(c.order_for=2,'Customer Order',if(c.order_for=3,'Repair Order',''))) as order_for,
        cus.order_no,br.name as cus_order_branch,
        IF(el.status = 4, 'Rejected', m.order_status) as order_status_msg,
        IF(el.status = 4, 'red', m.color) as color,
        c.order_status,
        c.cus_ord_ref,
        IFNULL(DATE_FORMAT(d.smith_due_date, '%d-%m-%Y'), '') as smith_due_date,
        IFNULL(el.rejection_reason, '') as rejection_reason,
        IFNULL(DATE_FORMAT(el.rejected_at, '%d-%m-%Y'), '') as rejected_at
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder = c.id_customerorder
        LEFT JOIN ret_order_email_logs el ON el.id_customerorder = c.id_customerorder
        LEFT JOIN ret_karigar k ON k.id_karigar=c.id_karigar
        LEFT JOIN customerorder cus ON cus.id_customerorder=c.cus_ord_ref
        LEFT JOIN branch br ON br.id_branch=cus.order_from
        LEFT JOIN order_status_message m ON m.id_order_msg=c.order_status
        WHERE c.pur_no IS NOT NULL and c.order_type=1
        ".($data['from_date']!='' && $data['to_date']!=''  ? " and (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."
        ".($data['order_for']!='' && $data['order_for']>0 ? " and c.order_for=".$data['order_for']."" :'')."
        GROUP BY c.id_customerorder
        Order by c.id_customerorder DESC");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
     function get_karigar_order_details($id_customerorder)
	{
	    $returnData = array();
	    $sql=$this->db->query("SELECT (d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,k.firstname as karigar_name,IFNULL(k.email,'') as email,
	    c.company_name, p.product_name,des.design_name,concat(s.value,' ',s.name) as size,IFNULL(k.contactno1,'') as mobile,
	    cus.order_no,date_format(cus.order_date,'%d-%m-%Y') as order_date, emp.firstname as emp_name,
	    subDes.sub_design_name,cus.pur_no, date_format(d.smith_due_date, '%d-%m-%Y') as smith_due_date,IFNULL(d.description,'') as description,
	    IFNULL(w.value,0) as approx_wt,cus.id_karigar as id_vendor,
	    cus.id_karigar,cus.order_for,IFNULL(d.weight,'') as weight,d.id_orderdetails,IFNULL(d.cus_orderdet_ref,'') as cus_ord_detail_id,ord.orderno as cusorderno,
	    IFNULL(d.net_wt,0) as net_wt
        FROM customerorder cus
        JOIN company c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=cus.id_customerorder
        LEFT JOIN ret_karigar k ON k.id_karigar=cus.id_karigar
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=d.id_sub_design
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.weight
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        LEFT JOIN employee emp on emp.id_employee=cus.order_taken_by
        LEFT JOIN customerorderdetails ord ON ord.id_orderdetails = d.cus_orderdet_ref
        WHERE cus.pur_no IS NOT NULL  AND cus.id_customerorder=".$id_customerorder."
        GROUP by d.id_orderdetails
        Order by d.id_product,d.design_no,d.id_sub_design,w.value");
        //print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        foreach($result as $items)
        {
            if($items['cus_ord_detail_id']!='')
            {
                $items['images'] = $this->get_order_images($items['id_orderdetails']);
                $items['stone_details'] = $this->get_customer_order_stone_details($items['cus_ord_detail_id']);
            }
            $returnData[]=$items;
        }
        return $returnData;
	}
	function get_customer_order_stone_details($id_orderdetails)
	{
	    $sql = $this->db->query("SELECT st.stone_name,s.wt,s.pieces,m.uom_name,
        s.is_apply_in_lwt,s.stone_cal_type,s.rate_per_gram,s.price,
        s.stone_id,st.stone_type,s.uom_id
        FROM ret_order_item_stones s
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
        LEFT JOIN ret_uom m ON m.uom_id = s.uom_id
        WHERE s.order_item_id = ".$id_orderdetails." ");
        return $sql->result_array();
	}
	function get_order_images($id_orderdetails)
    {
        $sql = $this->db->query("SELECT  c.image,d.orderno
        FROM customer_order_image c
        LEFT JOIN customerorderdetails d ON d.id_orderdetails = c.id_orderdetails
        where c.id_orderdetails=".$id_orderdetails."");
        return $sql->result_array();
    }
	function get_karigar_order_products($id_customerorder)
	{
	    $responseData=array();
	    $sql=$this->db->query("SELECT p.product_name,des.design_name,subDes.sub_design_name,d.id_product,d.design_no,d.id_sub_design,
	    IFNULL(d.description,'') as description,IFNULL(c.cus_ord_ref,'') as cus_ord_ref
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=d.id_sub_design
        WHERE c.pur_no IS NOT NULL AND c.id_customerorder=".$id_customerorder."
        GROUP by d.id_product,d.design_no,d.id_sub_design");
        $result = $sql->result_array();
        foreach($result as $items)
        {
            $weight_details=[];
            $size_details=[];
            $tot_items=[];
            $item_details = $this->get_karigar_order_product_details($id_customerorder,$items['id_product'],$items['design_no'],$items['id_sub_design']);
            if($items['cus_ord_ref']=='')
            {
                $image_details = $this->get_karigar_order_detail_images($id_customerorder,$items['id_product'],$items['design_no'],$items['id_sub_design']);
            }else
            {
                $image_details = $this->get_customer_order_detail_images($items['cus_ord_ref'],$items['id_product'],$items['design_no'],$items['id_sub_design']);
            }
            foreach($item_details as $itemDet)
            {
                $weight_details[]=array('weight_range'=>$itemDet['weight_range'],'approx_wt'=>$itemDet['approx_wt'],'tot_items'=>$itemDet['tot_items']);
                $size_details[]=array('size'=>$itemDet['size']);
                $tot_items[]=array('tot_items'=>$itemDet['tot_items']);
            }
            $responseData[]=array(
                                 'product_name'     =>$items['product_name'],
                                 'design_name'      =>$items['design_name'],
                                 'sub_design_name'  =>$items['sub_design_name'],
                                 'description'      =>$items['description'],
                                 //'item_details'     =>$this->get_karigar_order_product_details($id_customerorder,$items['id_product'],$items['design_no'],$items['id_sub_design']),
                                 'weight_details'   =>$weight_details,
                                 'size_details'     =>$size_details,
                                 'pcs_details'      =>$tot_items,
                                 'img_details'      =>$image_details,
                                 );
        }
        return $responseData;
	}
	function get_karigar_order_product_details($id_customerorder,$id_product,$id_design,$id_sub_design)
	{
	    $sql=$this->db->query("SELECT p.product_name,des.design_name,subDes.sub_design_name,(d.totalitems) as tot_items,w.weight_description as weight_range,
	    concat(s.value,' ',s.name) as size,IFNULL(w.value,0) as approx_wt,IFNULL(d.description,'') as description
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=d.id_sub_design
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.id_weight_range
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        WHERE c.pur_no IS NOT NULL AND c.id_customerorder=".$id_customerorder."
        ".($id_product!='' ? " and d.id_product=".$id_product."" :'')."
        ".($id_design!='' ? " and d.design_no=".$id_design."" :'')."
        ".($id_sub_design!='' ? " and d.id_sub_design=".$id_sub_design."" :'')."
        Order by w.id_weight");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_karigar_order_detail_images($id_customerorder,$id_product,$id_design,$id_sub_design)
	{
	    $sql=$this->db->query("SELECT img.image as image_name,img.id_orderdetails
        FROM customer_order_image img
        LEFT JOIN customerorderdetails d ON d.id_orderdetails=img.id_orderdetails
        LEFT JOIN customerorder cus ON cus.id_customerorder=d.id_customerorder
        WHERE cus.pur_no IS NOT NULL  AND cus.id_customerorder=".$id_customerorder."
        ".($id_product!='' ? " and d.id_product=".$id_product."" :'')."
        ".($id_design!='' ? " and d.design_no=".$id_design."" :'')."
        ".($id_sub_design!='' ? " and d.id_sub_design=".$id_sub_design."" :'')."
        ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_customer_order_detail_images($id_customerorder,$id_product,$id_design,$id_sub_design)
	{
	    $sql=$this->db->query("SELECT img.image as image_name,img.id_orderdetails
        FROM customer_order_image img
        LEFT JOIN customerorderdetails d ON d.id_orderdetails=img.id_orderdetails
        LEFT JOIN customerorder cus ON cus.id_customerorder=d.id_customerorder
        WHERE cus.id_customerorder=".$id_customerorder."
        ".($id_product!='' ? " and d.id_product=".$id_product."" :'')."
        ".($id_design!='' ? " and d.design_no=".$id_design."" :'')."
        ".($id_sub_design!='' ? " and d.id_sub_design=".$id_sub_design."" :'')."
        ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_KarigarOrders($data)
	{
	    $sql=$this->db->query("SELECT c.pur_no,d.id_orderdetails,p.product_name,des.design_name,m.sub_design_name,d.totalitems
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master m ON m.id_sub_design=d.id_sub_design
        WHERE d.orderstatus=3
        ".($data['id_karigar']!='' ? " and j.id_vendor=".$data['id_karigar']."" :'')."");
        return $sql->result_array();
	}
	function get_karigar_details($id_karigar)
	{
	    $sql=$this->db->query("SELECT k.id_karigar,concat(k.firstname,' ',IFNULL(k.lastname,'')) as karigar_name,k.contactno1 as mobile,k.code_karigar,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,IFNULL(cy.name,'') as country_name,IFNULL(st.name,'') as state_name,IFNULL(ct.name,'') as city_name,
	    IFNULL(k.company,'') as company_name,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,
	    cy.name as country_name,st.name as state_name,ct.name as city_name, IFNULL(k.gst_number,'') as gst_number, k.karigar_type, is_tcs, tcs_tax, is_tds, tds_tax,
	    k.id_country,k.id_state,k.id_city,k.pan_no,k.pincode,k.karigar_calc_type, k.acc_holder_name
        FROM ret_karigar k
        LEFT JOIN country cy ON cy.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city ct ON ct.id_city=k.id_city
        where k.id_karigar=".$id_karigar."");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
	}
	function get_pur_order_details($data)
	{
	    $sql=$this->db->query("SELECT d.id_orderdetails,(d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,k.firstname as karigar_name,IFNULL(k.email,'') as email,
	    p.product_name,des.design_name,concat(s.value,' ',s.name) as size,IFNULL(k.contactno1,'') as mobile,cus.pur_no,date_format(cus.order_date,'%d-%m-%Y') as order_date,
	    emp.firstname as emp_name,subDes.sub_design_name,m.order_status as order_status_msg,d.orderstatus,m.color,IFNULL(d.delivered_qty,0) as delivered_qty,date_format(d.delivered_date,'%d-%m-%Y') as delivered_date,
	    IFNULL(d.delivered_wt,0) as delivered_wt,
	    if(cus.order_type=1,'Stock Order',if(cus.order_type=2,'Customer Order',if(cus.order_type=3,'Customer Repair',if(cus.order_type=4,'Stock Repair','')))) as order_type,
	    if(cus.order_for=1,'Branch',if(cus.order_for=2,'Customer','Repair')) as order_for,IFNULL(ordRef.order_no,'') as cus_order_no
        FROM customerorderdetails d
        LEFT JOIN customerorder cus ON cus.id_customerorder=d.id_customerorder
        LEFT JOIN customerorder ordRef on ordRef.id_customerorder=cus.cus_ord_ref
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=d.id_sub_design
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.id_weight_range
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=cus.id_karigar
        LEFT JOIN employee emp ON emp.id_employee=cus.order_taken_by
        LEFT JOIN order_status_message m ON m.id_order_msg=d.orderstatus
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        WHERE cus.pur_no is NOT NULL
        ".($data['from_date']!='' && $data['to_date']!='' && $data['date_group_by']==1 ? " and (date(cus.order_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."
        ".($data['from_date']!='' && $data['to_date']!='' && $data['date_group_by']==2 ? " and (date(d.delivered_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."
        ".($data['id_karigar']!='' ? " and j.id_vendor=".$data['id_karigar']."" :'')."
        ".($data['report_type']!='' ? " and cus.order_for=".$data['report_type']."" :'')."
        ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_customer_order_pending_details($data)
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT c.id_customerorder,c.order_no,c.order_type
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN customerorder cusOrd ON cusOrd.id_customerorder = c.cus_ord_ref
        LEFT JOIN customerorderdetails cusOrdet ON cusOrdet.id_customerorder=cusOrd.id_customerorder
        LEFT JOIN joborder j ON j.id=cusOrdet.id_orderdetails
        WHERE d.orderstatus<3 and c.order_type=2
        GROUP by d.id_customerorder");
        $result= $sql->result_array();
        foreach($result as $items)
        {
            $returnData[]=array(
                              'id_customerorder'=>$items['id_customerorder'],
                              'order_type'      =>$items['order_type'],
                              'order_no'        =>$items['order_no'],
                              'item_details'    =>$this->get_customer_order_item_details($items['id_customerorder']),
                              'order_details'   =>$this->get_customer_order_details($items['id_customerorder']),
                             );
        }
        return $returnData;
	}
	function get_stock_repair_order($data)
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT c.id_customerorder,c.order_no,c.order_type
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        WHERE d.orderstatus <= 3 and (c.order_type=4 or c.order_type=3)
        ".($data['id_branch']!='' ? " and d.current_branch=".$data['id_branch']."" :'')."
        GROUP by d.id_customerorder");
        $result= $sql->result_array();
        foreach($result as $items)
        {
            $order= $this->get_stock_repair_order_details($items['id_customerorder'],$data);
            if(!empty($order)){
                $returnData[]=array(
                                    'id_customerorder'=>$items['id_customerorder'],
                                    'order_type'      =>$items['order_type'],
                                    'order_no'        =>$items['order_no'],
                                    'item_details'    =>[],
                                    'order_details'   =>$order,
                                    );
            }
        }
        return $returnData;
	}
	function get_stock_repair_order_details($id_customerorder,$data)
	{
        $returnData =[];
        $sql=$this->db->query("SELECT d.id_orderdetails,d.id_product,IFNULL(d.design_no,'') as design_no,IFNULL(d.id_sub_design,'') as id_sub_design,IFNULL(SUM(d.weight),0) as weight,IFNULL(SUM(d.totalitems),0) as totalitems,
        p.product_name,IFNULL(des.design_name,'') as design_name,IFNULL(s.sub_design_name,'') as sub_design_name,IFNULL(SUM(d.pure_wt),0) as pure_wt,
        IFNULL(sz.id_size,'') as id_size,IFNULL(concat(sz.value,' ',sz.name),'') as size,
        IFNULL( d.less_wt,'') as less_wt, IFNULL(d.net_wt,'') as net_wt,IFNULL(d.description,'') as description,IFNULL(d.stn_amt,'') as stn_amt,
        IFNULL( d.mc,'') as mc_value,IFNULL( d.wast_percent,'') as wast_percent,IFNULL(tag.tag_code,'-') as tag_code
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_taging tag ON tag.id_orderdetails =d.id_orderdetails
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=d.id_sub_design
        LEFT JOIN ret_size sz ON sz.id_size=d.size
        WHERE c.id_customerorder=".$id_customerorder." and  d.id_orderdetails NOT IN ( Select cus.cus_orderdet_ref FROM customerorderdetails cus LEFT JOIN customerorder c ON c.id_customerorder = cus.id_customerorder  where c.order_status != 6 and  cus.cus_orderdet_ref IS NOT NULL and cus.cus_orderdet_ref = d.id_orderdetails  )
      ".($data['id_branch']!='' ? " and d.current_branch=".$data['id_branch']."" :'')."
      group by d.id_orderdetails");
   //   echo $this->db->last_query();exit;
        $result =  $sql->result_array();
        foreach($result as $items)
		{
		    $items['image_details']=$this->get_images($items['id_orderdetails']);
            $items['stone_details'] = $this->get_customer_order_stone_details($items['id_orderdetails']);
		    $returnData[]=$items;
		}
		return $returnData;
	}
	function get_customer_order_details($id_customerorder)
	{
        $returnData = array();
	    $sql_1=$this->db->query("SELECT d.id_orderdetails,d.id_product,IFNULL(d.design_no,'') as design_no,IFNULL(d.id_sub_design,'') as id_sub_design,IFNULL(SUM(d.weight),0) as weight,IFNULL(SUM(d.totalitems),0) as totalitems,
	    IFNULL(p.product_name,'') as product_name,IFNULL(des.design_name,'') as design_name,IFNULL(s.sub_design_name,'') as sub_design_name,IFNULL(sz.id_size,'') as id_size,IFNULL(concat(sz.value,' ',sz.name),'') as size,
        IFNULL( d.less_wt,'') as less_wt, IFNULL(d.net_wt,'') as net_wt,IFNULL(d.description,'') as description,IFNULL(d.stn_amt,'') as stn_amt,
        IFNULL( d.mc,'') as mc_value,IFNULL( d.wast_percent,'') as wast_percent,date_format(d.cus_due_date,'%d-%m-%Y') as cus_due_date
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=d.id_sub_design
        LEFT JOIN ret_size sz ON sz.id_size=d.size
        WHERE c.id_customerorder=".$id_customerorder." and d.orderstatus=0
        group by d.id_orderdetails");
        $result =  $sql_1->result_array();
		foreach($result as $items)
		{
		    $items['image_details']=$this->get_images($items['id_orderdetails']);
            $items['stone_details'] = $this->get_customer_order_stone_details($items['id_orderdetails']);
		    $returnData[]=$items;
		}
		return $returnData;
    }
    function get_images($id_orderdetails)
    {
        $returnData = array();
        $sql = $this->db->query("SELECT * FROM `customer_order_image`
        where id_orderdetails=".$id_orderdetails."");
        $result =  $sql->result_array();
        foreach($result as $val)
		{
            $path = 'assets/img/orders/'.$val['image'];
			$type = pathinfo($path, PATHINFO_EXTENSION);
			if(file_exists($path)) {
				$data = file_get_contents($path);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			} else {
				$base64 = '';
			}
            $returnData[]=array(
                'id_order_img'  => $val['id_order_img'],
                'id_orderdetails' => $val['id_orderdetails'],
                'image'           => $val['image'],
                'img_src'        => $base64,
            );
        }
        return $returnData;
    }
	function get_customer_order_item_details($id_customerorder)
	{
	    $sql=$this->db->query("SELECT d.id_orderdetails,(d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,k.firstname as karigar_name,IFNULL(k.email,'') as email,c.company_name,
	    p.product_name,des.design_name,concat(s.value,' ',s.name) as size,IFNULL(k.contactno1,'') as mobile,cus.order_no,date_format(cus.order_date,'%d-%m-%Y') as order_date,
	    emp.firstname as emp_name,subDes.sub_design_name,cus.pur_no, date_format(d.smith_due_date, '%d-%m-%Y') as smith_due_date,
	    (SELECT image_name from ret_sub_design_mapping_images as img where is_default=1 and img.id_sub_design_mapping=m.id_sub_design_mapping) as default_image,
	    IFNULL(d.description,'') as description,m.id_sub_design_mapping,IFNULL(w.value,0) as approx_wt,j.id_vendor,d.id_product,d.id_customerorder,IFNULL(s.id_size,'') as id_size,tag.tag_code
        FROM customerorder cus
        JOIN company c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=cus.id_customerorder
        LEFT JOIN ret_taging tag ON tag.id_orderdetails =d.id_orderdetails
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=j.id_vendor
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=d.id_sub_design
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.weight
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        LEFT JOIN employee emp on emp.id_employee=cus.order_taken_by
        LEFT JOIN ret_sub_design_mapping m ON m.id_product=(d.id_product AND d.design_no AND d.id_sub_design)
        WHERE cus.order_no IS NOT NULL  AND cus.id_customerorder=".$id_customerorder."
        group by d.id_orderdetails");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	/*function get_purchase_entry_details()
	{
	    $sql=$this->db->query("SELECT p.po_id,p.po_ref_no,date_format(p.po_date,'%d-%m-%Y') as po_date,k.firstname as karigar,p.ewaybillno,
	    if(p.po_type=1,'Oranments',if(p.po_type=2,'Bullion Purchase','Stone')) as po_type,ord.gross_wt,ord.tot_pcs,ord.tot_lwt,ord.tot_nwt,
	    c.name as category_name,pur.purity,(IFNULL(p.tot_purchase_amt,0)) as tot_purchase_amt,p.tot_purchase_wt
        FROM ret_purchase_order p
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        LEFT JOIN ret_category c on c.id_ret_category=p.id_category
        LEFT JOIN ret_purity pur on pur.id_purity=p.id_purity
        LEFT JOIN (SELECT d.po_item_po_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.no_of_pcs),0) as tot_pcs,IFNULL(SUM(d.less_wt),0) as tot_lwt,IFNULL(SUM(d.net_wt),0) as tot_nwt
        FROM ret_purchase_order_items d
        GROUP by d.po_item_po_id) as ord ON ord.po_item_po_id=p.po_id
        WHERE p.pur_approval_type = 0");
        return $sql->result_array();
	}*/
	function get_purchase_entry_details($from_date,$to_date)
	{
	    $sql=$this->db->query("SELECT ifnull(grn.grn_ref_no, '') as grn_ref_no, p.po_grn_id, p.po_id,p.po_ref_no,date_format(p.po_date,'%d-%m-%Y') as po_date,k.firstname as karigar,p.ewaybillno,
	    if(p.po_type=1,'Oranments',if(p.po_type=2,'Bullion Purchase','Stone')) as po_type,ord.gross_wt,ord.tot_pcs,ord.tot_lwt,ord.tot_nwt,
	    ord.catname as category_name,ord.purity,(IFNULL(p.tot_purchase_amt,0)) as tot_purchase_amt,p.tot_purchase_wt,p.purchase_type,
	    if(p.is_approved=1,'Approved','Yet to Approved') as is_approved,IFNULL((gre.grs_wt),0) as grs_wt,
        p.is_approved as approved_status,p.bill_status,IFNULL(emp.firstname,'') as emp_name, p.po_delivery_date
        FROM ret_purchase_order p
        LEFT JOIN ret_grn_entry as grn ON grn.grn_id = p.po_grn_id
        LEFT JOIN employee as emp ON emp.id_employee = p.created_by
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
         LEFT JOIN (SELECT IFNULL(SUM(i.grn_gross_wt),0) as grs_wt,p.po_id
        FROM ret_grn_entry g
        LEFT JOIN ret_grn_items i ON i.grn_item_grn_id = g.grn_id
        LEFT JOIN ret_purchase_order p ON p.po_grn_id = g.grn_id
        GROUP BY p.po_id) as gre ON gre.po_id = p.po_id
        LEFT JOIN (SELECT group_concat(DISTINCT(c.name)) as catname, group_concat(DISTINCT(pur.purity)) as purity,
            d.po_item_po_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.no_of_pcs),0) as tot_pcs,
            IFNULL(SUM(d.less_wt),0) as tot_lwt,IFNULL(SUM(d.net_wt),0) as tot_nwt
            FROM ret_purchase_order_items d
            LEFT JOIN ret_category c on c.id_ret_category=d.po_item_cat_id
            LEFT JOIN ret_purity pur on pur.id_purity=d.id_purity
            GROUP by d.po_item_po_id) as ord ON ord.po_item_po_id=p.po_id
        WHERE p.pur_approval_type = 0
        AND (date(p.po_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
        ORDER BY p.po_id DESC");
        return $sql->result_array();
	}
	function get_approval_purchase_entry_details()
	{
	    $sql=$this->db->query("SELECT p.po_id,p.po_ref_no,date_format(p.po_date,'%d-%m-%Y') as po_date,k.firstname as karigar,p.ewaybillno,
	    if(p.po_type=1,'Oranments',if(p.po_type=2,'Metal','Stone')) as po_type,ord.gross_wt,ord.tot_pcs,ord.tot_lwt,ord.tot_nwt,
	    c.name as category_name,pur.purity,p.total_payable_amt,p.total_payable_wt
        FROM ret_purchase_order p
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        LEFT JOIN ret_category c on c.id_ret_category=p.id_category
        LEFT JOIN ret_purity pur on pur.id_purity=p.id_purity
        LEFT JOIN (SELECT d.po_item_po_id,is_suspense_stock,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.no_of_pcs),0) as tot_pcs,IFNULL(SUM(d.less_wt),0) as tot_lwt,IFNULL(SUM(d.net_wt),0) as tot_nwt
        FROM ret_purchase_order_items d
        GROUP by d.po_item_po_id) as ord ON ord.po_item_po_id=p.po_id
        WHERE ord.is_suspense_stock = 1");
        //echo $this->db->last_query();exit;
        return $sql->result_array();
	}
	function get_qc_receipt_items($data)
	{
	    $returnData = array();
	    $sql = $this->db->query("SELECT d.id_qc_issue_details,p.qc_process_id,i.po_item_id,pro.product_name,des.design_name,subDes.sub_design_name,d.issue_pcs,d.issue_gwt,d.issue_lwt,d.issue_nwt
        FROM ret_po_qc_issue_process p
        LEFT JOIN ret_po_qc_issue_details d ON d.qc_process_id = p.qc_process_id
        LEFT JOIN ret_purchase_order_items i ON i.po_item_id = d.po_item_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = i.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no = i.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = i.po_item_sub_des_id
        WHERE p.qc_process_id = '".$data['qc_ref_no']."'
        GROUP BY d.id_qc_issue_details");
        $result = $sql->result_array();
        foreach($result as $items){
            $items['stone_details'] = $this->get_qc_issue_stone_details($items['id_qc_issue_details']);
            $returnData[]=$items;
        }
        return $returnData;
	}
	function get_qc_issue_stone_details($id_qc_issue_details)
	{
	    $sql = $this->db->query("SELECT s.ret_qc_issue_stn_id,t.id_stone_type,s.stone_pcs,s.stone_wt,po_stn.po_stone_uom,
	    stn.stone_name,'0' as po_stone_rejected_pcs,'0' as po_stone_rejected_wt,s.stone_pcs as po_stone_accepted_pcs,s.stone_wt as po_stone_accepted_wt
        FROM ret_po_qc_issue_stone_details s
        LEFT JOIN ret_po_stone_items po_stn ON po_stn.po_st_id = s.po_st_id
        LEFT JOIN ret_stone stn ON stn.stone_id = po_stn.po_stone_id
        LEFT JOIN ret_stone_type t ON t.id_stone_type = stn.stone_type
        LEFT JOIN ret_uom m ON m.uom_id = stn.uom_id
        WHERE s.id_qc_issue_details = ".$id_qc_issue_details."");
        return $sql->result_array();
	}
	function get_purchase_issue_entry_items($data)
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT i.po_item_id,p.po_id,cat.name as category_name,pro.product_name,d.design_name,s.sub_design_name,
        i.no_of_pcs,i.gross_wt,i.mc_type,i.net_wt,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage,IFNULL(i.less_wt,0) as less_wt
        FROM ret_purchase_order p
        LEFT JOIN ret_purchase_order_items i ON i.po_item_po_id=p.po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=i.po_item_pro_id
        LEFT JOIN ret_design_master d ON d.design_no=i.po_item_des_id
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=i.po_item_sub_des_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=pro.cat_id
        WHERE i.status=1
        and p.po_id='".$data['po_id']."'");
        //print_r($this->db->last_query());exit;
        $result=$sql->result_array();
        foreach($result as $items)
        {
           $stn_amount=0;
           $stoneDetails=$this->pourchase_entry_stone_details($items['po_item_id']);
           foreach($stoneDetails as $stones)
           {
               $stn_amount+=$stones['po_stone_amount'];
           }
           $returnData[]=array(
                                'po_item_id'        =>$items['po_item_id'],
                                'po_id'             =>$items['po_id'],
                                'category_name'     =>$items['category_name'],
                                'product_name'      =>$items['product_name'],
                                'design_name'       =>$items['design_name'],
                                'sub_design_name'   =>$items['sub_design_name'],
                                'no_of_pcs'         =>$items['no_of_pcs'],
                                'gross_wt'          =>$items['gross_wt'],
                                'less_wt'           =>$items['less_wt'],
                                'net_wt'            =>$items['net_wt'],
                                'mc_type'           =>$items['mc_type'],
                                'mc_value'          =>$items['mc_value'],
                                'item_wastage'      =>$items['item_wastage'],
                                'stn_amount'       =>$stn_amount,
                                'stone_details'     =>$stoneDetails
                              );
        }
        return $returnData;
	}
	function pourchase_entry_stone_details($po_item_id)
	{
	    $sql=$this->db->query("SELECT s.po_st_id,s.po_item_id,s.po_stone_pcs,s.po_stone_wt,s.po_stone_amount,st.stone_name,m.uom_name,s.po_stone_rate as stone_rate,
	    s.po_stone_rejected_pcs,s.po_stone_rejected_wt,s.po_stone_accepted_pcs,s.po_stone_accepted_wt
        FROM ret_po_stone_items s
        LEFT JOIN ret_stone st ON st.stone_id=s.po_stone_id
        LEFT JOIN ret_uom m ON m.uom_id=s.po_stone_uom
        WHERE s.po_item_id=".$po_item_id."");
	    return $sql->result_array();
	}
	function get_status_details()
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT i.po_item_id,p.po_id,cat.name as category_name,pro.product_name,d.design_name,s.sub_design_name,
        i.no_of_pcs,i.gross_wt,i.mc_type,i.net_wt,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage,IFNULL(i.less_wt,0) as less_wt,
        p.po_ref_no,IFNULL(i.qc_passed_pcs,0) as qc_passed_pcs,IFNULL(i.qc_passed_gwt,0) as qc_passed_gwt,IFNULL(i.qc_passed_nwt,0) as qc_passed_nwt,
        k.firstname as karigar,i.is_halmarked
        FROM ret_purchase_order p
        LEFT JOIN ret_purchase_order_items i ON i.po_item_po_id=p.po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=i.po_item_po_id
        LEFT JOIN ret_design_master d ON d.design_no=i.po_item_des_id
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=i.po_item_sub_des_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=pro.cat_id
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        WHERE i.status=2");
        $result=$sql->result_array();
        return $result;
	}
	function get_qc_ref_no()
	{
	    $lastno = NULL;
	    $sql = "SELECT (ref_no) as last_no FROM ret_po_qc_issue_process p ORDER BY qc_process_id DESC LIMIT 1";
		$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->last_no;
			}
	    if($lastno != NULL)
		{
		    //$max_num = explode("-",$lastno);
            $number = (int) $lastno;
            $number++;
            $ref_no = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $ref_no = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $ref_no;
	}
	function get_qc_issue_details()
	{
	    $sql = $this->db->query("SELECT p.qc_process_id,p.ref_no
        FROM ret_po_qc_issue_process p
        LEFT JOIN ret_po_qc_issue_details d ON d.qc_process_id = p.qc_process_id
        WHERE d.status = 0 AND p.ref_no!=''
        GROUP BY d.qc_process_id
        ORDER BY p.qc_process_id DESC");
        return $sql->result_array();
	}
	function purchase_issue($fin_year='')
	{
	    $returnData=array();
	    /*$sql=$this->db->query("SELECT p.po_ref_no,p.po_id
        FROM ret_purchase_order_items i
        LEFT JOIN ret_purchase_order p ON p.po_id=i.po_item_po_id
        WHERE p.po_id IS NOT NULL and i.status=0 GROUP by i.po_item_po_id");*/
         $sql = $this->db->query("SELECT 
                p.po_ref_no,p.fin_year_code,
                p.po_id,
                IFNULL(SUM(i.no_of_pcs), 0) AS no_of_pcs,
                q.qcissued_pcs,
                (IFNULL(SUM(i.no_of_pcs), 0) - IFNULL(q.qcissued_pcs, 0)) AS blc_pcs,
                (IFNULL(SUM(i.gross_wt), 0) - IFNULL(q.qcissued_gwt, 0)) AS blc_gwt,
                q.qc_status
        FROM ret_purchase_order_items i
            LEFT JOIN ret_purchase_order p ON p.po_id = i.po_item_po_id
            LEFT JOIN (
                SELECT 
                    IFNULL(SUM(qd.issue_pcs), 0) AS qcissued_pcs,
                    IFNULL(SUM(qd.issue_gwt), 0) AS qcissued_gwt,
                    IFNULL(SUM(qd.issue_nwt), 0) AS qcissued_nwt,
                    qd.po_item_id,
                    qc.qc_status,r.po_item_po_id
                FROM ret_po_qc_issue_details qd
                LEFT JOIN ret_po_qc_issue_process qc ON qc.qc_process_id = qd.qc_process_id
                LEFT JOIN ret_purchase_order_items r ON r.po_item_id = qd.po_item_id
                WHERE qc.qc_status = 1
                GROUP BY r.po_item_po_id
            ) AS q ON q.po_item_po_id = p.po_id
            WHERE p.po_id IS NOT NULL and p.bill_status = 1
            AND p.is_approved = 1"
            . ($fin_year != '' ? " AND p.fin_year_code = " . $fin_year : "") . "
            GROUP BY i.po_item_po_id
            HAVING blc_gwt > 0
        ");
        $result=$sql->result_array();
        // print_r($this->db->last_query());exit;
        foreach($result as $items)
        {
            $returnData[]=array(
                               'po_ref_no'  =>$items['po_ref_no'],
                               'po_id'      =>$items['po_id'],
                               'qc_status'   =>$items['qc_status'],
                               'fin_year_code'   =>$items['fin_year_code'],
                               //'item_details'=>$this->get_purchase_item_details($items['po_id']),
                               );
        }
        return $returnData;
	}
	function purchase_receipt_orders()
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT p.po_ref_no,p.po_id,IFNULL(SUM(i.no_of_pcs),0) as no_of_pcs,q.qcissued_pcs,(IFNULL(SUM(i.no_of_pcs),0)-IFNULL(q.qcissued_pcs,0)) as blc_pcs
        FROM ret_purchase_order_items i
        LEFT JOIN ret_purchase_order p ON p.po_id=i.po_item_po_id
        LEFT JOIN (SELECT IFNULL(SUM(d.issue_pcs),0) as qcissued_pcs,r.po_item_po_id
        FROM ret_po_qc_issue_details d
        LEFT JOIN ret_po_qc_issue_process qc ON qc.qc_process_id = d.qc_process_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.po_item_id
        GROUP BY d.po_item_id) as q ON q.po_item_po_id = i.po_item_po_id
        WHERE p.po_id IS NOT NULL
        GROUP by i.po_item_po_id
        HAVING qcissued_pcs > 0");
        $result=$sql->result_array();
        foreach($result as $items)
        {
            $returnData[]=array(
                               'po_ref_no'  =>$items['po_ref_no'],
                               'po_id'      =>$items['po_id'],
                               );
        }
        return $returnData;
	}
	function get_purchase_item_details($po_id,$fin_year='')
	{
	    $sql=$this->db->query("SELECT i.po_item_id,p.po_id,cat.name as category_name,pro.product_name,d.design_name,s.sub_design_name,
        (IFNULL(i.no_of_pcs,0)-IFNULL(q.qcissued_pcs,0)) as no_of_pcs,(IFNULL(i.gross_wt,0)-IFNULL(q.qcissued_gwt,0)) as gross_wt,i.mc_type,(i.net_wt-IFNULL(q.qcissued_nwt,0)) as net_wt,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage,
        IFNULL(i.less_wt,0) as less_wt,
        p.po_ref_no,k.firstname as karigar_name,IFNULL(q.qcissued_pcs,0) as qcissued_pcs,(IFNULL(i.no_of_pcs,0)-IFNULL(q.qcissued_pcs,0)) as blc_pcs
        FROM ret_purchase_order p
        LEFT JOIN ret_purchase_order_items i ON i.po_item_po_id=p.po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=i.po_item_pro_id
        LEFT JOIN ret_design_master d ON d.design_no=i.po_item_des_id
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=i.po_item_sub_des_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=pro.cat_id
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        LEFT JOIN (SELECT IFNULL(SUM(qd.issue_pcs),0) as qcissued_pcs,IFNULL(SUM(qd.issue_gwt),0) as qcissued_gwt,IFNULL(SUM(qd.issue_nwt),0) as qcissued_nwt,
        qd.po_item_id
        FROM ret_po_qc_issue_details qd
        LEFT JOIN ret_po_qc_issue_process qc ON qc.qc_process_id = qd.qc_process_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = qd.po_item_id
        where qc.qc_status = 1
        GROUP BY qd.po_item_id) as q ON q.po_item_id = i.po_item_id
         WHERE p.po_id='".$po_id."'
          ".($fin_year!='' ? "and p.fin_year_code=".$fin_year."" : '')."
        HAVING gross_wt > 0");
        //print_r($this->db->last_query());exit;
        $result =  $sql->result_array();
        foreach($result as $items){
            $items['stone_details'] = $this->get_qc_stone_details($items['po_item_id']);
            $items['other_metal_details'] = $this->get_qc_other_metal_details($items['po_item_id']);
            $returnData[]=$items;
        }
        return $returnData;
	}
	function get_qc_stone_details($po_item_id)
	{
	    $sql=$this->db->query("SELECT po_stn.po_st_id,po_stn.po_stone_id,po_stn.is_apply_in_lwt,po_stn.po_stone_wt,
        po_stn.po_stone_uom,po_stn.po_stone_calc_based_on,po_stn.po_stone_rate,po_stn.po_stone_amount,s.stone_type,s.stone_name,uom.uom_short_code,
        IFNULL(stndet.stone_pcs,0) as stone_pcs,IFNULL(stndet.stone_wt,0) as stone_wt,
        (po_stn.po_stone_pcs-IFNULL(stndet.stone_pcs,0)) as po_stone_pcs,(po_stone_wt-IFNULL(stndet.stone_wt,0)) as po_stone_wt,
        ifnull(po_stn.po_quality_id,'') as po_quality_id
        FROM ret_po_stone_items po_stn
        LEFT JOIN ret_stone s ON s.stone_id = po_stn.po_stone_id
        LEFT JOIN ret_uom uom on uom.uom_id=po_stn.po_stone_uom
        LEFT JOIN(SELECT IFNULL(SUM(qc_stn.stone_pcs),0) as stone_pcs,IFNULL(SUM(qc_stn.stone_wt),0) as stone_wt,qc_stn.po_st_id
        FROM ret_po_qc_issue_stone_details qc_stn
        GROUP BY qc_stn.po_st_id) as stndet ON stndet.po_st_id = po_stn.po_st_id
        WHERE po_stn.po_item_id=".$po_item_id."
        having po_stone_wt > 0");
        return $sql->result_array();
	}
    function get_qc_other_metal_details($po_item_id)
    {
       $sql=$this->db->query("SELECT ifnull(sum(po_othr.po_other_item_gross_weight),0) as po_other_mt_wt,po_othr.po_item_id
       FROM ret_po_other_item po_othr
       LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = po_othr.po_item_id
       WHERE po_othr.po_item_id = ".$po_item_id."
       GROUP BY po_othr.po_item_id");
       return $sql->result_array();
    }
	function get_purchase_qc_details($data)
	{
	    $returnData = [];
        $sql=$this->db->query("SELECT p.qc_process_id,p.ref_no,p.issue_pcs,p.issue_gross_wt,p.issue_less_wt,p.issue_net_wt,e.firstname as empname,
        date_format(p.created_at,'%d-%m-%Y') as issue_date,IFNULL(qc_det.passed_gwt,0) as recd_gross_wt,IFNULL(qc_det.passed_lwt,0) as recd_lwt,
        IFNULL(qc_det.passed_nwt,0) as recd_nwt,p.ref_no,qc_det.status,qc_det.is_lot_created,ifnull(qc_det.po_ref_no,'') as po_ref_no,ifnull(qc_det.karigar,'') as karigar,
        p.qc_status,qc_det.po_id
        FROM ret_po_qc_issue_process p
        LEFT JOIN employee e ON e.id_employee = p.qc_id_vendor
        LEFT JOIN (SELECT IFNULL(SUM(d.passed_pcs),0) as passed_pcs,IFNULL(SUM(d.passed_gwt),0) as passed_gwt,
            IFNULL(SUM(d.passed_nwt),0) as passed_nwt,d.qc_process_id,IFNULL(SUM(d.passed_lwt),0) as passed_lwt,
            d.status,d.is_lot_created,ifnull(po.po_ref_no,'') as po_ref_no,ifnull(kar.firstname,'') as karigar,
            po.po_id
            FROM ret_po_qc_issue_details d
            LEFT JOIN ret_purchase_order_items po_itm on  po_itm.po_item_id = d.po_item_id
            LEFT JOIN ret_purchase_order po on po.po_id  = po_itm.po_item_po_id
            LEFT JOIN ret_karigar kar on kar.id_karigar = po.po_karigar_id
        GROUP BY d.qc_process_id) as qc_det ON qc_det.qc_process_id = p.qc_process_id
        Where (date(p.created_at) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ORDER BY p.qc_process_id DESC");
        //print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        foreach($result as $val){
            $val['item_details'] = $this->getqc_item_details($val['qc_process_id']);
            $returnData[]=$val;
        }
        return $returnData;
	}
	function getqc_item_details($qc_process_id)
	{
	    $sql = $this->db->query("SELECT pro.product_name,des.design_name,subDes.sub_design_name,d.passed_pcs,d.passed_gwt,d.passed_lwt,d.passed_nwt,
	    pr.po_ref_no
        FROM ret_po_qc_issue_details d
        LEFT JOIN ret_po_qc_issue_process p ON p.qc_process_id = d.qc_process_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.po_item_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = r.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no = r.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = r.po_item_sub_des_id
        LEFT JOIN ret_purchase_order pr ON pr.po_id = r.po_item_po_id
        WHERE d.qc_process_id = ".$qc_process_id."");
        return $sql->result_array();
	}
	//Halmarking Issue / Receipt
	function generate_HalmarkingRefNo()
	{
	    $lastno = NULL;
	    $sql = "SELECT MAX(hm_ref_no) as lastorder_no
					FROM ret_po_halmark_process o
					ORDER BY hm_process_id DESC
					LIMIT 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->lastorder_no;
			}
	    if($lastno != NULL)
		{
		    //$max_num = explode("-",$lastno);
            $number = (int) $lastno;
            $number++;
            $order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $order_number;
	}
	function get_pending_halmarking_items()
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT p.po_ref_no,p.po_id
        FROM ret_purchase_order_items i
        LEFT JOIN ret_po_qc_issue_details  qc on qc.po_item_id  = i.po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id=i.po_item_po_id
        WHERE p.po_id IS NOT NULL and p.is_approved = 1 and p.is_po_halmarked = 0 and (i.is_halmarked=0 or i.is_halmarked IS NULL) and i.is_lot_created=0 
        and qc.status =1
        GROUP by i.po_item_po_id");
        $result=$sql->result_array();
        foreach($result as $items)
        {
            $returnData[]=array(
                               'po_ref_no'  =>$items['po_ref_no'],
                               'po_id'      =>$items['po_id'],
                               'item_details'=>$this->get_halmarking_items($items['po_id']),
                               );
        }
        return $returnData;
	}
    function get_halmarking_items($po_id)
    {
        $returnData = array();
        $sql = $this->db->query("SELECT qc_d.id_qc_issue_details,qc_d.po_item_id,po.po_id,c.name as category_name,pro.product_name,des.design_name,subDes.sub_design_name,
        (IFNULL(SUM(qc_d.passed_pcs),0) - IFNULL(hmIss.hm_pcs,0)) as no_of_pcs,(IFNULL(SUM(qc_d.passed_gwt),0)-IFNULL(hmIss.hm_gwt,0)) as gross_wt,(IFNULL(SUM(qc_d.passed_nwt),0)- IFNULL(hmIss.hm_nwt,0)) as net_wt,
        (IFNULL(SUM(qc_d.passed_lwt),0)-IFNULL(hmIss.hm_lwt,0)) as less_wt,
        po.po_ref_no,k.firstname as karigar_name
        
        FROM ret_po_qc_issue_details qc_d 
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = qc_d.po_item_id
        LEFT JOIN ret_purchase_order po on po.po_id = po_itm.po_item_po_id
        left join ret_product_master pro ON pro.pro_id = po_itm.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no = po_itm.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = po_itm.po_item_sub_des_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        LEFT JOIN ret_karigar k ON k.id_karigar=po.po_karigar_id

        LEFT JOIN(SELECT hm_det.hm_po_qc_issue_id,hm_det.hm_po_item_id,SUM(hm_det.hm_issue_pcs) as hm_pcs,SUM(hm_det.hm_issue_gwt) as hm_gwt,
        SUM(hm_det.hm_issue_nwt) as hm_nwt,SUM(hm_det.hm_issue_lwt) as hm_lwt
        FROM ret_po_hm_process_details hm_det
        LEFT JOIN ret_po_qc_issue_details qc_det on qc_det.id_qc_issue_details = hm_det.hm_po_qc_issue_id
        GROUP BY qc_det.id_qc_issue_details) as hmIss on hmIss.hm_po_qc_issue_id = qc_d.id_qc_issue_details
        
        WHERE (po_itm.is_halmarked=0 or po_itm.is_halmarked IS NULL) and po_itm.is_lot_created = 0 and po.is_approved = 1 and po.is_po_halmarked = 0
        and po.po_id = ".$po_id."
        group by qc_d.id_qc_issue_details
        HAVING gross_wt > 0");
        $res = $sql->result_array();
        foreach($res as $key=>$val)
        {
            $returnData[]=array(
                "po_item_id"       => $val['po_item_id'],
                "id_qc_issue_details" => $val['id_qc_issue_details'],
                "po_id"            => $val['po_id'],
                "category_name"    => $val['category_name'],
                "product_name"     => $val['product_name'],
                "design_name"      => $val['design_name'],
                "sub_design_name"  => $val['sub_design_name'],
                "no_of_pcs"        => $val['no_of_pcs'],
                "gross_wt"         => $val['gross_wt'],
                "net_wt"           => $val['net_wt'],
                "less_wt"          => $val['less_wt'],
                "po_ref_no"        => $val['po_ref_no'],
                "karigar_name"     => $val['karigar_name'],
                "stone_details"    => $this->getqcAcceptedStoneDetails($val['id_qc_issue_details']),
            );
        }

        return $returnData;
    }

    function getqcAcceptedStoneDetails($id_qc_issue_details)
    {
        $sql = $this->db->query("SELECT (s.qc_passed_pcs-IFNULL(stndet.stone_pcs,0)) as stone_pcs,(s.qc_passed_wt-IFNULL(stndet.stone_wt,0)) as stone_wt,
        st.stone_name,m.uom_name,m.uom_id,i.po_stone_id as stone_id,i.is_apply_in_lwt,i.po_st_id,st.stone_type,i.po_stone_uom,i.po_stone_calc_based_on,
        i.po_quality_id,i.po_stone_amount,i.po_stone_rate

        FROM ret_po_qc_issue_stone_details s
        LEFT JOIN ret_po_stone_items i ON i.po_st_id = s.po_st_id
        LEFT JOIN ret_stone st ON st.stone_id = i.po_stone_id
        LEFT JOIN ret_uom m ON m.uom_id = i.po_stone_uom

        LEFT JOIN(SELECT IFNULL(SUM(hm_stn.stone_pcs),0) as stone_pcs,IFNULL(SUM(hm_stn.stone_wt),0) as stone_wt,hm_stn.po_st_id
        FROM ret_po_hm_issue_stone_details hm_stn
        GROUP BY hm_stn.po_st_id) as stndet ON stndet.po_st_id = s.po_st_id

        WHERE s.id_qc_issue_details = ".$id_qc_issue_details."
        HAVING stone_wt > 0");

        return $sql->result_array();
    }
	/*function get_halmarking_items($po_id)
	{
	    $sql=$this->db->query("SELECT i.po_item_id,p.po_id,cat.name as category_name,pro.product_name,d.design_name,s.sub_design_name,
        i.no_of_pcs as no_of_pcs,i.gross_wt as gross_wt,i.mc_type,i.net_wt as net_wt,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage,IFNULL(i.less_wt,0) as less_wt,
        p.po_ref_no,k.firstname as karigar_name
        FROM ret_purchase_order p
        LEFT JOIN ret_purchase_order_items i ON i.po_item_po_id=p.po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=i.po_item_pro_id
        LEFT JOIN ret_design_master d ON d.design_no=i.po_item_des_id
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=i.po_item_sub_des_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=pro.cat_id
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        WHERE i.is_halmarked=0 and i.is_lot_created = 0 and p.is_approved = 1
        and p.po_id='".$po_id."'");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}*/
	function get_halmarking_details()
	{
	    $sql=$this->db->query("SELECT h.hm_ref_no,h.hm_process_pcs,h.hm_process_pcs,IFNULL(h.hm_process_gwt,0) as hm_process_gwt,IFNULL(h.hm_process_lwt,0) as hm_process_lwt,
	    IFNULL(h.hm_process_nwt,0) as hm_process_nwt,
	    k.firstname as karigar_name,date_format(h.hm_process_created_at,'%d-%m-%Y') as issue_date,
	    if(h.status=1,'Issued','Completed') as hm_status,h.status,IFNULL(h.total_hm_charges,0) as total_hm_charges
        FROM ret_po_halmark_process h
        LEFT JOIN ret_karigar k ON k.id_karigar=h.hm_vendor_id");
        return $sql->result_array();
	}
	function get_halmarking_issue_orders()
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT p.hm_process_id,p.hm_ref_no
        FROM ret_po_halmark_process p
        WHERE status=1");
	    $result=$sql->result_array();
	    foreach($result as $items)
	    {
	        $returnData[]=array(
	                            'hm_process_id' =>$items['hm_process_id'],
	                            'hm_ref_no'     =>$items['hm_ref_no'],
	                            'item_details'  =>$this->get_halmarking_issue_order_details($items['hm_process_id']),
	                            );
	    }
	    return $returnData;
	}
    function get_halmarking_issue_order_details($hm_process_id)
    {
        $sql=$this->db->query("SELECT hm_det.hm_receipt_id,p.po_item_id,pro.product_name,des.design_name,subDes.sub_design_name,
        hm_det.hm_issue_pcs as no_of_pcs,
        hm_det.hm_issue_gwt as gross_wt,hm_det.hm_issue_nwt as net_wt,hm_det.hm_issue_lwt as less_wt
        
        FROM ret_po_hm_process_details hm_det
        LEFT JOIN ret_po_halmark_process s ON s.hm_process_id=hm_det.hm_issue_id
        LEFT JOIN ret_purchase_order_items p on p.po_item_id = hm_det.hm_po_item_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no=p.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=p.po_item_sub_des_id
        where s.status = 1 and s.hm_process_id = ".$hm_process_id."");
        $result=$sql->result_array();
        foreach($result as $items)
        {
            $returnData[]=array(
                'design_name'       =>$items['design_name'],
                'gross_wt'          =>$items['gross_wt'],
                'hm_process_id'     =>$items['hm_process_id'],
                'hm_receipt_id'     =>$items['hm_receipt_id'],
                'less_wt'           =>$items['less_wt'],
                'net_wt'            =>$items['net_wt'],
                'pcs'               =>$items['no_of_pcs'],
                'po_item_id'        =>$items['po_item_id'],
                'product_name'      =>$items['product_name'],
                'sub_design_name'   =>$items['sub_design_name'],
                'stone_details'     =>$this->get_hm_issue_stone_details($items['hm_receipt_id']),
                );
        }
        return $returnData;

    }

    function get_hm_issue_stone_details($hm_receipt_id)
	{
	    $sql = $this->db->query("SELECT s.ret_hm_issue_stn_id ,t.id_stone_type,s.stone_pcs,s.stone_wt,po_stn.po_stone_uom,
	    stn.stone_name,'0' as po_stone_rejected_pcs,'0' as po_stone_rejected_wt,s.stone_pcs as po_stone_accepted_pcs,s.stone_wt as po_stone_accepted_wt
        FROM ret_po_hm_issue_stone_details s
        LEFT JOIN ret_po_stone_items po_stn ON po_stn.po_st_id = s.po_st_id
        LEFT JOIN ret_stone stn ON stn.stone_id = po_stn.po_stone_id
        LEFT JOIN ret_stone_type t ON t.id_stone_type = stn.stone_type
        LEFT JOIN ret_uom m ON m.uom_id = stn.uom_id
        WHERE s.ret_hm_receipt_id = ".$hm_receipt_id."");
        return $sql->result_array();
	}

	/*function get_halmarking_issue_order_details($hm_process_id)
	{
	    $sql=$this->db->query("SELECT pro.product_name,des.design_name,subDes.sub_design_name,p.no_of_pcs as pcs,p.gross_wt as gross_wt,p.less_wt as less_wt,p.net_wt as net_wt,
	    s.hm_process_id,p.po_item_id
        FROM ret_purchase_order_items p
        LEFT JOIN ret_po_hm_process_details d ON d.hm_po_item_id=p.po_item_id
        LEFT JOIN ret_po_halmark_process s ON s.hm_process_id=d.hm_issue_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no=p.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=p.po_item_sub_des_id
        WHERE s.status=1 and s.hm_process_id=".$hm_process_id."");
        $result=$sql->result_array();
        foreach($result as $items)
        {
            $stoneDetails=$this->pourchase_entry_stone_details($items['po_item_id']);
             $returnData[]=array(
                                'design_name'       =>$items['design_name'],
                                'gross_wt'          =>$items['gross_wt'],
                                'hm_process_id'     =>$items['hm_process_id'],
                                'less_wt'           =>$items['less_wt'],
                                'net_wt'            =>$items['net_wt'],
                                'pcs'               =>$items['pcs'],
                                'po_item_id'        =>$items['po_item_id'],
                                'product_name'      =>$items['product_name'],
                                'sub_design_name'   =>$items['sub_design_name'],
                                'stone_details'     =>$stoneDetails,
                                );
        }
        return $returnData;
	}*/
	//Halmarking Issue / Receipt
	//check qc status
	function get_purchase_order_qc_details($po_id)
	{
	    $sql=$this->db->query("SELECT d.status
        FROM ret_po_qc_issue_details d
        LEFT JOIN ret_purchase_order_items i ON i.po_item_id=d.po_item_id
        WHERE i.po_item_po_id=".$po_id." AND i.is_halmarked=1 ");
        return $sql->result_array();
	}
	//check qc status
	//Lot Generate
	function get_purchase_order($po_id)
	{
	    $sql=$this->db->query("SELECT p.po_id,p.po_karigar_id,p.id_category,p.id_purity
        FROM ret_purchase_order p
        WHERE p.po_id=".$po_id."");
        return $sql->row_array();
	}
	function get_purchase_by_category($po_id)
	{
	    $sql=$this->db->query("SELECT p.po_id,p.po_karigar_id,i.po_item_cat_id as id_category,i.id_purity,pr.stock_type
        FROM ret_purchase_order_items i
        LEFT JOIN ret_purchase_order p ON p.po_id = i.po_item_po_id
        LEFT JOIN ret_product_master pr ON pr.pro_id = i.po_item_pro_id
        WHERE p.po_id = ".$po_id."
        GROUP BY i.po_item_cat_id,i.id_purity,pr.stock_type");
        return $sql->result_array();
	}
	function get_halmarking_purchase_order($hm_process_id)
	{
	    $sql=$this->db->query("SELECT o.po_id,o.po_karigar_id,o.id_category,o.id_purity
        FROM ret_po_hm_process_details d
        LEFT JOIN ret_po_halmark_process p ON p.hm_process_id=d.hm_issue_id
        LEFT JOIN ret_purchase_order_items i ON i.po_item_id=d.hm_po_item_id
        LEFT JOIN ret_purchase_order o ON o.po_id=i.po_item_po_id
        WHERE p.hm_process_id=".$hm_process_id." GROUP by p.hm_process_id");
        return $sql->row_array();
	}
	function get_purOrders($po_ref_no)
	{
	    $sql=$this->db->query("SELECT p.po_id,p.po_karigar_id,p.id_category,p.id_purity
        FROM ret_purchase_order p
        WHERE p.po_ref_no='".$po_ref_no."'");
        return $sql->row_array();
	}
	function get_purchase_orders_by_product($po_id,$id_category,$id_purity,$stock_type)
	{
	    $sql=$this->db->query("SELECT i.po_item_pro_id as id_product,d.passed_pcs,d.passed_gwt,d.passed_lwt,d.passed_nwt
        FROM ret_purchase_order_items i
        LEFT JOIN ret_po_qc_issue_details d ON d.po_item_id=i.po_item_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = i.po_item_pro_id
        WHERE i.po_item_po_id=".$po_id." AND d.status=1 AND i.is_halmarked=1 and d.is_lot_created=0
        ".($id_category!='' ? " and i.po_item_cat_id=".$id_category."" :'')."
        ".($stock_type!='' ? " and pro.stock_type=".$stock_type."" :'')."
        ".($id_purity!='' ? " and i.id_purity=".$id_purity."" :'')."
        GROUP by i.po_item_pro_id,pro.stock_type,i.id_purity");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_po_purchase_items($po_id,$id_category)
	{
	    $sql=$this->db->query("SELECT i.po_item_id,IFNULL(SUM(i.no_of_pcs-IFNULL(i.qc_failed_pcs,0)-IFNULL(i.hm_rejected_pcs,0)),0) as total_pcs,
        IFNULL(SUM(IFNULL(i.gross_wt,0)-IFNULL(i.qc_failed_gwt,0)-IFNULL(i.hm_rejected_gwt,0)),0) as total_gwt,
        IFNULL(SUM(IFNULL(i.less_wt,0)-IFNULL(i.qc_failed_lwt,0)-IFNULL(i.hm_rejected_lwt,0)),0) as total_lwt,
        IFNULL(SUM(IFNULL(i.net_wt,0)-IFNULL(i.qc_failed_nwt,0)-IFNULL(i.hm_rejected_nwt,0)),0) as total_nwt,
        i.mc_type,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage, i.item_pure_wt as item_pure_wt,i.is_rate_fixed,IFNULL(i.fix_rate_per_grm,0) as fix_rate_per_grm,
        mt.tgrp_id,tax.tax_percentage
        FROM ret_purchase_order_items i
        LEFT JOIN ret_po_qc_issue_details d ON d.po_item_id=i.po_item_id
        LEFT JOIN ret_product_master p ON p.pro_id=i.po_item_pro_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
	    (m.tax_percentage) as tax_percentage,(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=mt.tgrp_id
        WHERE i.po_item_po_id=".$po_id." AND d.status=1 AND i.is_halmarked=1 and d.is_lot_created=0
        ".($id_category!='' ? " and i.po_item_cat_id=".$id_category."" :'')."
        GROUP by i.po_item_id,p.stock_type");
        return $sql->result_array();
	}
	function get_purchase_order_items($po_id,$id_product)
	{
	    $sql=$this->db->query("SELECT i.po_item_id,IFNULL(SUM(i.no_of_pcs-IFNULL(i.qc_failed_pcs,0)-IFNULL(i.hm_rejected_pcs,0)),0) as total_pcs,
        IFNULL(SUM(IFNULL(i.gross_wt,0)-IFNULL(i.qc_failed_gwt,0)-IFNULL(i.hm_rejected_gwt,0)),0) as total_gwt,
        IFNULL(SUM(IFNULL(i.less_wt,0)-IFNULL(i.qc_failed_lwt,0)-IFNULL(i.hm_rejected_lwt,0)),0) as total_lwt,
        IFNULL(SUM(IFNULL(i.net_wt,0)-IFNULL(i.qc_failed_nwt,0)-IFNULL(i.hm_rejected_nwt,0)),0) as total_nwt,
        i.mc_type,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage, i.item_pure_wt as item_pure_wt,i.is_rate_fixed,IFNULL(i.fix_rate_per_grm,0) as fix_rate_per_grm,
        mt.tgrp_id,i.po_item_des_id,i.po_item_sub_des_id,i.po_item_pro_id
        FROM ret_purchase_order_items i
        LEFT JOIN ret_po_qc_issue_details d ON d.po_item_id=i.po_item_id
        LEFT JOIN ret_product_master p ON p.pro_id=i.po_item_pro_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        WHERE i.po_item_po_id=".$po_id." AND d.status=1
        ".($id_product!='' ? " and i.po_item_pro_id=".$id_product."" :'')."
        GROUP by i.po_item_des_id,i.po_item_sub_des_id");
        return $sql->result_array();
	}
	function check_purchase_halmarking_details($po_id)
	{
	    $sql=$this->db->query("SELECT i.po_item_id,IFNULL(SUM(i.no_of_pcs-IFNULL(i.qc_failed_pcs,0)-IFNULL(i.hm_rejected_pcs,0)),0) as total_pcs,
        IFNULL(SUM(IFNULL(i.gross_wt,0)-IFNULL(i.qc_failed_gwt,0)-IFNULL(i.hm_rejected_gwt,0)),0) as total_gwt,
        IFNULL(SUM(IFNULL(i.less_wt,0)-IFNULL(i.qc_failed_lwt,0)-IFNULL(i.hm_rejected_lwt,0)),0) as total_lwt,
        IFNULL(SUM(IFNULL(i.net_wt,0)-IFNULL(i.qc_failed_nwt,0)-IFNULL(i.hm_rejected_nwt,0)),0) as total_nwt,
        i.mc_type,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage, i.item_pure_wt as item_pure_wt,i.is_rate_fixed,IFNULL(i.fix_rate_per_grm,0) as fix_rate_per_grm,
        mt.tgrp_id,tax.tax_percentage
        FROM ret_purchase_order_items i
        LEFT JOIN ret_po_qc_issue_details d ON d.po_item_id=i.po_item_id
        LEFT JOIN ret_product_master p ON p.pro_id=i.po_item_pro_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
	    (m.tax_percentage) as tax_percentage,(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=mt.tgrp_id
        WHERE i.po_item_po_id=".$po_id." and d.status=1 AND i.status=4 AND i.is_halmarked=1 and d.is_lot_created=0
        GROUP by i.po_item_id");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_purchase_order_item_search($po_item_id)
	{
	    $sql=$this->db->query("SELECT i.po_item_po_id,i.po_item_id,IFNULL(SUM(i.no_of_pcs-IFNULL(i.qc_failed_pcs,0)-IFNULL(i.hm_rejected_pcs,0)),0) as total_pcs,
        IFNULL(SUM(IFNULL(i.gross_wt,0)-IFNULL(i.qc_failed_gwt,0)-IFNULL(i.hm_rejected_gwt,0)),0) as total_gwt,
        IFNULL(SUM(IFNULL(i.less_wt,0)-IFNULL(i.qc_failed_lwt,0)-IFNULL(i.hm_rejected_lwt,0)),0) as total_lwt,
        IFNULL(SUM(IFNULL(i.net_wt,0)-IFNULL(i.qc_failed_nwt,0)-IFNULL(i.hm_rejected_nwt,0)),0) as total_nwt,
        i.mc_type,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage, i.item_pure_wt as item_pure_wt,i.is_rate_fixed,IFNULL(i.fix_rate_per_grm,0) as fix_rate_per_grm,
        mt.tgrp_id,tax.tax_percentage,i.purchase_touch
        FROM ret_purchase_order_items i
        LEFT JOIN ret_po_qc_issue_details d ON d.po_item_id=i.po_item_id
        LEFT JOIN ret_product_master p ON p.pro_id=i.po_item_pro_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
	    (m.tax_percentage) as tax_percentage,(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=mt.tgrp_id
        WHERE i.po_item_id=".$po_item_id." AND i.is_halmarked=1
        GROUP by i.po_item_id");
        return $sql->row_array();
	}
	function get_pur_order_item_details($po_item_id)
	{
	    $sql=$this->db->query("SELECT i.po_item_po_id,i.po_item_id,IFNULL(SUM(i.no_of_pcs-IFNULL(i.qc_failed_pcs,0)-IFNULL(i.hm_rejected_pcs,0)),0) as total_pcs,
        IFNULL(SUM(IFNULL(i.gross_wt,0)-IFNULL(i.qc_failed_gwt,0)-IFNULL(i.hm_rejected_gwt,0)),0) as total_gwt,
        IFNULL(SUM(IFNULL(i.less_wt,0)-IFNULL(i.qc_failed_lwt,0)-IFNULL(i.hm_rejected_lwt,0)),0) as total_lwt,
        IFNULL(SUM(IFNULL(i.net_wt,0)-IFNULL(i.qc_failed_nwt,0)-IFNULL(i.hm_rejected_nwt,0)),0) as total_nwt,
        i.mc_type,IFNULL(i.mc_value,0) as mc_value,IFNULL(i.item_wastage,0) as item_wastage, i.item_pure_wt as item_pure_wt,i.is_rate_fixed,IFNULL(i.fix_rate_per_grm,0) as fix_rate_per_grm,
        mt.tgrp_id,tax.tax_percentage,i.purchase_touch
        FROM ret_purchase_order_items i
        LEFT JOIN ret_po_qc_issue_details d ON d.po_item_id=i.po_item_id
        LEFT JOIN ret_product_master p ON p.pro_id=i.po_item_pro_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        LEFT JOIN (select i.tgi_taxcode,i.tgi_tgrpcode,
	    (m.tax_percentage) as tax_percentage,(i.tgi_calculation) as tgi_calculation
		FROM ret_taxgroupitems i
		LEFT JOIN ret_taxmaster m on m.tax_id=i.tgi_taxcode) as tax on tax.tgi_tgrpcode=mt.tgrp_id
        WHERE i.po_item_id=".$po_item_id."
        GROUP by i.po_item_id");
        return $sql->row_array();
	}
	function get_ret_settings($settings)
	{
		$data=$this->db->query("SELECT value FROM ret_settings where name='".$settings."'");
		return $data->row()->value;
	}
	function get_headOffice()
	{
		$data=$this->db->query("SELECT b.is_ho,b.id_branch,name FROM branch b where b.is_ho=1");
		return $data->row_array();
	}
	function update_po_paymentData($data,$arith){
		$sql = "UPDATE ret_purchase_order SET total_payable_amt=(total_payable_amt".$arith." ".$data['total_payable_amt']."),total_payable_wt=(total_payable_wt".$arith." ".$data['total_payable_wt'].")
		WHERE po_id=".$data['po_id'];
		//print_r($sql);exit;
		$status = $this->db->query($sql);
		return $status;
	}
	function get_purchase_order_hm_details($hm_process_id)
	{
	    $sql=$this->db->query("SELECT i.status
        FROM ret_po_hm_process_details d
        LEFT JOIN ret_po_halmark_process p ON p.hm_process_id=d.hm_issue_id
        LEFT JOIN ret_purchase_order_items i ON i.po_item_id=d.hm_po_item_id
        LEFT JOIN ret_po_qc_issue_details qc ON qc.po_item_id=i.po_item_id
        WHERE qc.status=1 AND i.is_lot_created=0 AND i.status=4
        and p.hm_process_id=".$hm_process_id."");
        return $sql->result_array();
	}
	//Lot Generate
	//Purchase Payment Details
	function get_supplier_pay_details($data)
	{
        if($data['bill_type']==2)   //For Receipt Payment
        {
            $sql = $this->db->query("SELECT
            ifnull(SUM(COALESCE(CASE WHEN (trans_type = 2 AND trans_screen_id != 6) THEN ifnull(trans_amount,0) END,0)) -
            SUM(COALESCE(CASE WHEN (trans_type = 1) THEN ifnull(trans_amount,0)
            WHEN (trans_type = 2 AND trans_screen_id = 6) THEN ifnull(trans_amount,0) END,0)),0) as balance_amount,
            ifnull(SUM(COALESCE(CASE WHEN (trans_rec_type = 1 AND trans_type = 2) THEN ifnull(purewt,0) END,0)) -
            SUM(COALESCE(CASE WHEN (trans_rec_type = 1 AND trans_type = 1) THEN ifnull(purewt,0) END,0)),0) balance_purewt
            FROM ret_view_smith_ledger l
            WHERE l.customer_id IS NOT NULL
            ".($data['id_karigar']!='' ? " and l.customer_id=".$data['id_karigar']."" :'')."");
        }
        else if($data['bill_type']==1) // For Bill Payment
        {
            $sql = $this->db->query("SELECT
            ifnull(SUM(COALESCE(CASE WHEN (trans_type = 2 AND trans_screen_id != 6) THEN ifnull(trans_amount,0) END,0)) -
            SUM(COALESCE(CASE WHEN (trans_type = 1) THEN ifnull(trans_amount,0)
            WHEN (trans_type = 2 AND trans_screen_id = 6) THEN ifnull(trans_amount,0) END,0)),0) as balance_amount,
            ifnull(SUM(COALESCE(CASE WHEN (trans_rec_type = 1 AND trans_type = 2) THEN ifnull(purewt,0) END,0)) -
            SUM(COALESCE(CASE WHEN (trans_rec_type = 1 AND trans_type = 1) THEN ifnull(purewt,0) END,0)),0) balance_purewt
            FROM ret_view_supplier_ledger l
            WHERE l.customer_id IS NOT NULL
            ".($data['id_karigar']!='' ? " and l.customer_id=".$data['id_karigar']."" :'')."");
        }
        else if($data['bill_type']==3) // For Approval Bill Payment
        {
            $sql = $this->db->query("SELECT
            ifnull(SUM(COALESCE(CASE WHEN (trans_type = 2 AND trans_screen_id != 6) THEN ifnull(trans_amount,0) END,0)) -
            SUM(COALESCE(CASE WHEN (trans_type = 1) THEN ifnull(trans_amount,0)
            WHEN (trans_type = 2 AND trans_screen_id = 6) THEN ifnull(trans_amount,0) END,0)),0) as balance_amount,
            ifnull(SUM(COALESCE(CASE WHEN (trans_rec_type = 1 AND trans_type = 2) THEN ifnull(purewt,0) END,0)) -
            SUM(COALESCE(CASE WHEN (trans_rec_type = 1 AND trans_type = 1) THEN ifnull(purewt,0) END,0)),0) balance_purewt
            FROM ret_view_supplier_approval_ledger l
            WHERE l.customer_id IS NOT NULL
            ".($data['id_karigar']!='' ? " and l.customer_id=".$data['id_karigar']."" :'')."");
        }
        return $sql->row_array();
	}
	function purchase_payment_details($data)
	{
	    $returnData=array('item_details'=>[]);
	    $sql=$this->db->query("SELECT i.po_item_id,IFNULL(SUM(i.no_of_pcs-IFNULL(i.qc_failed_pcs,0)-IFNULL(i.hm_rejected_pcs,0)),0) as total_pcs,
        IFNULL(SUM(IFNULL(i.gross_wt,0)-IFNULL(i.qc_failed_gwt,0)-IFNULL(i.hm_rejected_gwt,0)),0) as total_gwt,
        IFNULL(SUM(IFNULL(i.less_wt,0)-IFNULL(i.qc_failed_lwt,0)-IFNULL(i.hm_rejected_lwt,0)),0) as total_lwt,
        IFNULL(SUM(IFNULL(i.net_wt,0)-IFNULL(i.qc_failed_nwt,0)-IFNULL(i.hm_rejected_nwt,0)),0) as total_nwt,
        i.mc_type,IFNULL(i.mc_value,0) as mc_value,pro.product_name,des.design_name,subDes.sub_design_name,
        cat.name as category_name,pur.purity,i.item_pure_wt,i.item_wastage,p.total_payable_amt,k.firstname as karigar_name,IFNULL(k.contactno1,'') as mobile
        FROM ret_purchase_order_items i
        LEFT JOIN ret_purchase_order p ON p.po_id=i.po_item_po_id
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=i.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no=i.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON  subDes.id_sub_design=i.po_item_sub_des_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.id_category
        LEFT JOIN ret_purity pur ON pur.id_purity=p.id_purity
        WHERE p.po_ref_no='".$data['po_ref_no']."'
        GROUP by i.po_item_id");
        $itemDetails=$sql->result_array();
        foreach($itemDetails as $item)
        {
            $returnData['item_details'][]=array(
                    'total_payable_amt' =>$item['total_payable_amt'],
                    'category_name'     =>$item['category_name'],
                    'design_name'       =>$item['design_name'],
                    'item_pure_wt'      =>$item['item_pure_wt'],
                    'item_wastage'      =>$item['item_wastage'],
                    'mc_type'           =>$item['mc_type'],
                    'mc_value'          =>$item['mc_value'],
                    'po_item_id'        =>$item['po_item_id'],
                    'product_name'      =>$item['product_name'],
                    'purity'            =>$item['purity'],
                    'sub_design_name'   =>$item['sub_design_name'],
                    'total_gwt'         =>$item['total_gwt'],
                    'total_lwt'         =>$item['total_lwt'],
                    'total_nwt'         =>$item['total_nwt'],
                    'total_pcs'         =>$item['total_pcs'],
                    'karigar_name'      =>$item['karigar_name'],
                    'mobile'            =>$item['mobile'],
                    'rate_fixing_det'   =>$this->item_rate_fixing_details($item['po_item_id']),
                    );
        }
        $payQuery=$this->db->query("SELECT p.po_id,IFNULL(csh.tot_pay_amt,0) as tot_pay_amt,IFNULL(wt.tot_pay_wt,0) as tot_pay_wt
        FROM ret_purchase_order p
        LEFT JOIN(SELECT pay.pay_po_id,IFNULL(SUM(pay.pay_amt),0) as tot_pay_amt
                 FROM ret_po_payment pay
                 LEFT JOIN ret_purchase_order p on p.po_id=pay.pay_po_id
                 WHERE p.po_ref_no='".$data['po_ref_no']."' AND pay.type=1) as csh ON csh.pay_po_id=p.po_id
        LEFT JOIN(SELECT pay.pay_po_id,IFNULL(SUM(pay.pay_wt),0) as tot_pay_wt
                 FROM ret_po_payment pay
                 LEFT JOIN ret_purchase_order p on p.po_id=pay.pay_po_id
                 WHERE p.po_ref_no='".$data['po_ref_no']."' AND pay.type=2) as wt ON wt.pay_po_id=p.po_id
        WHERE p.po_ref_no='".$data['po_ref_no']."'");
        $returnData['pay_details']= $payQuery->row_array();
        $payHistory=$this->db->query("SELECT pay.pay_id,date_format(pay.pay_create_on,'%d-%m-%Y') as pay_date,pay.pay_refno,IFNULL(SUM(pay.pay_amt),0) as tot_cash_pay,IFNULL(SUM(pay.pay_wt),0) as tot_pay_wt,
        k.firstname as karigar_name,p.po_ref_no
        FROM ret_po_payment pay
        LEFT JOIN ret_purchase_order p ON p.po_id=pay.pay_po_id
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        WHERE p.po_ref_no='".$data['po_ref_no']."'
        GROUP by pay.pay_po_id,pay.pay_refno");
        $returnData['pay_history']= $payHistory->result_array();
        return $returnData;
	}
	function item_rate_fixing_details($po_item_id)
	{
	    $rateFixing=$this->db->query("SELECT IFNULL(r.rate_fix_wt,0) as rate_fix_wt,IFNULL(r.rate_fix_rate,0) as rate_fix_rate,r.rate_fix_id,IFNULL(r.rate_fix_amt,0) as rate_fix_amt,r.rate_fix_type,
	    r.total_tax_amount,r.total_amount,date_format(r.rate_fix_created_on,'%d-%m-%Y') as rate_fix_created_on
        FROM ret_po_rate_fix r
        LEFT JOIN ret_purchase_order_items i ON i.po_item_id=r.rate_fix_po_item_id
        LEFT JOIN ret_purchase_order pur ON pur.po_id=i.po_item_po_id
        LEFT JOIN ret_product_master p ON p.pro_id=i.po_item_pro_id
        LEFT JOIN employee emp ON emp.id_employee=r.rate_fix_create_by
        WHERE i.po_item_id=".$po_item_id."");
        //print_r($this->db->last_query());exit;
        return $rateFixing->result_array();
	}
	function get_PO_Ratefix_List($from_date,$to_date)
	{
	    $sql=$this->db->query("SELECT rate_fix_id, po_ref_no, date_format(rate_fix_created_on, '%d-%m-%Y %H:%i:%s') as ratefixon, rate_fix_wt,
	                            rate_fix_rate, total_amount, kr.firstname as karigar,rf.is_approved,
                                if(rf.is_approved=0,'Yet to Approve','Approved') as approve_status,
                                rf.bill_status,
                               if(rf.bill_status=1,'Success','Cancel') as billed_status
	                            FROM ret_po_rate_fix as rf
	                            LEFT JOIN ret_purchase_order as po ON po.po_id = rf.rate_fix_po_item_id
	                            LEFT JOIN ret_karigar as kr ON kr.id_karigar = po.po_karigar_id
	                            WHERE (date(rf.rate_fix_created_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
	                            and rf.rate_fix_type = 1
	                            order by rf.rate_fix_id DESC");
	   //echo $this->db->last_query();exit;
        return $sql->result_array();
	}
	function get_approval_rate_fix_list($from_date,$to_date)
	{
	    $sql=$this->db->query("SELECT rate_fix_id, po_ref_no, date_format(rate_fix_created_on, '%d-%m-%Y %H:%i:%s') as ratefixon, rate_fix_wt,
	                            rate_fix_rate, total_amount, kr.firstname as karigar,rf.is_approved,if(rf.bill_status=2,'Cancelled', if(rf.is_approved=0,'Yet to Approve','Approved')) as approve_status,rf.bill_status
	                            FROM ret_po_rate_fix as rf
	                            LEFT JOIN ret_purchase_order as po ON po.po_id = rf.rate_fix_po_item_id
	                            LEFT JOIN ret_karigar as kr ON kr.id_karigar = po.po_karigar_id
	                            WHERE (date(rf.rate_fix_created_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
	                            and rf.rate_fix_type = 2
	                            order by rf.rate_fix_id DESC");
	   //echo $this->db->last_query();exit;
        return $sql->result_array();
	}
	function get_PurchasePaymentList($from_date,$to_date)
	{
	    $sql=$this->db->query("SELECT pay.pay_id,date_format(pay.pay_create_on,'%d-%m-%Y') as pay_date,pay.pay_refno,IFNULL(SUM(pay.pay_amt),0) as tot_cash_pay,IFNULL(SUM(pay.pay_wt),0) as tot_pay_wt,
        k.firstname as karigar_name,p.po_ref_no
        FROM ret_po_payment pay
        LEFT JOIN ret_purchase_order p ON p.po_id=pay.pay_po_id
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        GROUP by pay.pay_po_id,pay.pay_refno");
        return $sql->result_array();
	}
	function get_PurchaseSupplierPaymentList($from_date,$to_date)
	{
	    // PUR-CR04: Added has_cheque subquery to detect cheque payment details (type=5 in ret_po_payment_detail)
	    // VERIFY: Added is_verified column
	    $sql= $this->db->query("SELECT pay.pay_id,pay.pay_sup_id,date_format(pay.pay_create_on,'%d-%m-%Y') as pay_date,pay.pay_refno,
                        		IFNULL(SUM(pay.pay_amt),0) as pay_amt,
                                k.firstname as karigar_name,if(bill_type = 1, 'Purchase', 'Advance') as bill_type,
                                if(pay.pay_status=1,'Success','Cancelled') as status,pay.pay_status,pay.is_verified,
                                (SELECT COUNT(*) FROM ret_po_payment_detail pd WHERE pd.pay_id = pay.pay_id AND pd.type = 5) as has_cheque,
                                (SELECT rdc.entry_date FROM ret_day_closing rdc
                                LEFT JOIN branch b ON b.id_branch = rdc.id_branch
                                 WHERE b.is_ho = 1) as day_close_date
                                FROM ret_po_payment pay
                                LEFT JOIN ret_karigar k ON k.id_karigar = pay.pay_sup_id
                                WHERE (date(pay.pay_create_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
                                GROUP by pay.pay_id ");
        return $sql->result_array();
	}

    function verify_po_payment($pay_id)
    {
        $this->db->where('pay_id', $pay_id);
        $this->db->update('ret_po_payment', array('is_verified' => 1));
        return $this->db->affected_rows() > 0;
    }
	//Purchase Payment Details
	function getAvailableOrders($SearchTxt, $supplierId){
		$data = $this->db->query("SELECT pur_no as label, id_customerorder as value FROM customerorder WHERE pur_no LIKE '%".$SearchTxt."%'");
		return $data->result_array();
	}
	//Rate Fixing
	function get_rate_fixing_items($data)
	{
	    $sql=$this->db->query("SELECT i.po_item_id,pro.product_name,des.design_name,subDes.sub_design_name,
        cat.name as category_name,pur.purity,i.item_pure_wt,i.item_wastage,p.po_ref_no,'0' as fixed_wt,(i.item_pure_wt-IFNULL(paid.paid_wt,0)) as balance_wt
        FROM ret_purchase_order_items i
        LEFT JOIN ret_purchase_order p ON p.po_id=i.po_item_po_id
        LEFT JOIN ret_karigar k ON k.id_karigar=p.po_karigar_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=i.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no=i.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON  subDes.id_sub_design=i.po_item_sub_des_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=i.po_item_cat_id
        LEFT JOIN ret_purity pur ON pur.id_purity=p.id_purity
        LEFT JOIN(SELECT IFNULL(SUM(f.rate_fix_wt),0) as paid_wt,f.rate_fix_po_item_id
        FROM ret_po_rate_fix f
        GROUP by f.rate_fix_po_item_id) as paid ON paid.rate_fix_po_item_id=i.po_item_id
        WHERE i.is_rate_fixed=0 AND p.po_ref_no='".$data['po_ref_no']."'
        Having balance_wt>0");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_bill_details($SearchTxt,$bill_cus_id){
		$data = $this->db->query("SELECT b.bill_id as value,b.bill_no as label,b.tot_bill_amount
        FROM ret_billing b
        WHERE b.bill_no LIKE '%".$SearchTxt."%' and b.bill_status=1 AND (b.bill_type=1 OR b.bill_type=2 OR b.bill_type=3)
        ".($bill_cus_id!='' ? " and b.bill_cus_id=".$bill_cus_id."" :'')."
        ");
        //print_r($this->db->last_query());exit;
		return $data->result_array();
	}
	function get_retWallet_details($id_karigar)
	{
		$data=$this->db->query("SELECT id_wallet,id_karigar FROM ret_karigar_wallet w where w.id_karigar=".$id_karigar."");
		if($data->num_rows()>0)
		{
			return array('status'=>TRUE,'id_wallet'=>$data->row('id_wallet'));
		}else{
			return array('status'=>FALSE,'id_wallet'=>'');
		}
	}
	function get_supplier_advance_details($data)
	{
	    $data=$this->db->query("SELECT * FROM `ret_karigar_wallet` where id_karigar=".$data['id_karigar']."");
	    return $data->row_array();
	}
	//Update Wallet Account
	function updateWalletData($data,$arith)
	{
		$sql = "UPDATE ret_karigar_wallet SET amount=(amount".$arith." ".$data['amount'].") WHERE id_wallet=".$data['id_wallet'];
		$status = $this->db->query($sql);
		return $status;
	}
	//Update Wallet Account
	//Rate Fixing
		//Purchase Item return
	function getRejectedPos($data)
	{
		/*$sql = $this->db->query("SELECT po.po_id as value, poitm.po_item_id, po.po_ref_no as label, poitm.po_returned_pcs,g.grn_type
                                FROM ret_purchase_order as po
                                LEFT JOIN ret_purchase_order_items poitm ON po.po_id = poitm.po_item_po_id
                                LEFT JOIN ret_po_qc_issue_details po_qc ON po_qc.po_item_id = poitm.po_item_id
                                LEFT JOIN ret_purchase_return_items r ON r.pur_ret_po_item_id = poitm.po_item_id
                                LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id AND ret.bill_status = 1
                                LEFT JOIN ret_grn_entry g ON g.grn_id = po.po_grn_id
                                WHERE
                                ".($data['purchase_type']==0 ? " po_qc.failed_pcs > 0 AND po_qc.failed_gwt > 0" :" po_qc.passed_pcs > 0 AND po_qc.passed_gwt > 0")."
                                ".($data['id_karigar']!='' && $data['purchase_type']==0 ? " and po.po_karigar_id=".$data['id_karigar']."" :'')."
                                AND ret.pur_return_id IS NULL
                                GROUP BY po.po_id order by  po.po_id asc ");*/
        $sql = $this->db->query("SELECT p.po_id as value,p.po_ref_no as label,g.grn_type
        FROM ret_po_qc_issue_details d
        LEFT JOIN ret_po_qc_issue_process q ON q.qc_process_id = d.qc_process_id
        LEFT JOIN ret_purchase_order_items i ON i.po_item_id = d.po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = i.po_item_po_id
        LEFT JOIN ret_grn_entry g ON g.grn_id = p.po_grn_id
        LEFT JOIN ret_purchase_return_items r ON r.id_qc_issue_details = d.id_qc_issue_details
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id and ret.bill_status = 1
        LEFT join ret_lot_inwards lt on lt.po_id = p.po_id
        where
        ".($data['purchase_type']==0 ? " (d.failed_pcs > 0 OR d.failed_gwt > 0)" :" (d.passed_pcs > 0 OR d.passed_gwt > 0)  AND d.lot_no is null")."
        ".($data['id_karigar']!='' && $data['id_karigar']!=0  && $data['purchase_type']==0 ? " and p.po_karigar_id=".$data['id_karigar']."" :'')."
        GROUP BY p.po_id order by p.po_id DESC");
       // print_r($this->db->last_query());exit;
		return $sql->result_array();
	}
	function getRejectedItemsByPoId($poid,$purchase_type,$fin_year)
	{
	    $purchaseaitems = array();
		$sql = $this->db->query("SELECT po_qc.id_qc_issue_details,po.po_id, poitm.po_item_id, po.po_ref_no, poitm.no_of_pcs as purchasedpcs,
						poitm.gross_wt as purchasedwt,
						".($purchase_type==0 ? "(ifnull(sum(po_qc.failed_pcs),0) - ifnull(purret.rt_pcs,0))" :"(ifnull(sum(po_qc.passed_pcs),0) - ifnull(kar_iss.issue_pcs,0) - ifnull(purret.rt_pcs,0)- ifnull(lot.lot_pcs,0))")." as qcfaildpcs,
                        ".($purchase_type==0 ? "(ifnull(sum(po_qc.failed_gwt),0) - ifnull(purret.rt_wt,0))" :"(ifnull(sum(po_qc.passed_gwt),0) - ifnull(kar_iss.issue_metal_wt,0) - ifnull(purret.rt_wt,0)- ifnull(lot.lot_wt,0))")." as qcfaildwt,
						cat.name as catname, pro.product_name as product_name, des.design_name as design_name,
						subDes.sub_design_name, sup.firstname as karigar, cat.id_ret_category as cat_id, po.po_karigar_id as karigar_idyy,
						poitm.qc_failed_pcs as piece, poitm.qc_failed_gwt as weight,sup.firstname as supplier, cat.tgrp_id,
                        pro.pro_id,des.design_no as des_id,subDes.id_sub_design,poitm.item_wastage as wast_per,
                        poitm.purchase_touch as pur_touch,poitm.mc_type,poitm.mc_value,sup.karigar_calc_type,g.grn_type,poitm.tax_group,poitm.tax_type,poitm.pure_wt_calc_type,
                        IFNULL(pur.purity,0) as purity_value,
                        CASE
                            WHEN g.purchase_type = 0 THEN 'unknown' 
                            WHEN g.purchase_type = 1 THEN 'Stock'
                            WHEN g.purchase_type = 2 THEN 'Walk-in'
                            WHEN g.purchase_type = 3 THEN 'Expo'
                            ELSE 'Unknown'
                        END AS purchase_type_name,
                        poitm.calculation_based_on,pro.purchase_mode
                        FROM ret_po_qc_issue_details po_qc
						LEFT JOIN ret_purchase_order_items poitm ON poitm.po_item_id = po_qc.po_item_id
						LEFT JOIN ret_purchase_order as po ON po.po_id = poitm.po_item_po_id
						LEFT JOIN ret_category as cat ON cat.id_ret_category = poitm.po_item_cat_id
						LEFT JOIN ret_product_master as pro ON pro.pro_id = poitm.po_item_pro_id
						LEFT JOIN ret_design_master as des ON des.design_no = poitm.po_item_des_id
						LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=poitm.po_item_sub_des_id
						LEFT JOIN ret_karigar as sup ON sup.id_karigar = po.po_karigar_id
						LEFT JOIN ret_grn_entry g on g.grn_id = po.po_grn_id
						LEFT JOIN ret_purity pur ON pur.id_purity = poitm.id_purity
                        LEFT JOIN(SELECT rti.pur_ret_po_item_id,Ifnull(SUM(rti.pur_ret_pcs),0) as rt_pcs,
                        IFNULL(sum(rti.pur_ret_gwt),0) as rt_wt
                        FROM ret_purchase_return_items rti
                        LEFT JOIN ret_purchase_return r on r.pur_return_id = rti.pur_ret_id and r.bill_status = 1
                        where r.purchase_type = ".$purchase_type."
                        GROUP BY rti.pur_ret_po_item_id) as purret on purret.pur_ret_po_item_id = po_qc.po_item_id
                        LEFT JOIN(SELECT d.po_item_id,Ifnull(SUM(d.no_of_piece),0) as lot_pcs,
                        IFNULL(sum(d.gross_wt),0) as lot_wt
                        FROM ret_lot_inwards_detail d
                        GROUP BY d.po_item_id) as lot on lot.po_item_id = po_qc.po_item_id
                        LEFT JOIN(SELECT km.po_id,kmd.po_item_id,
                        ifnull(sum(kmd.issue_pcs),0) as issue_pcs,ifnull(sum(kmd.issue_metal_wt),0) as issue_metal_wt
                        FROM ret_karigar_metal_issue_details kmd
                        LEFT JOIN ret_karigar_metal_issue km on km.met_issue_id = kmd.issue_met_parent_id
                        where km.issue_against_po = 1 and km.bill_status=1
                        GROUP BY kmd.po_item_id) as kar_iss on kar_iss.po_item_id = poitm.po_item_id
						WHERE poitm.po_item_po_id = '".$poid."'
                        ".($fin_year!='' ? "and po.fin_year_code=".$fin_year."" : "")."
                        GROUP BY poitm.po_item_id
                        HAVING (qcfaildpcs > 0 OR qcfaildwt > 0)");
		//print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        foreach($result as $val){
            $val['stone_details'] = $this->get_qc_stone_rejected_details($val['po_item_id'],$purchase_type);
            $purchaseaitems[]=$val;
        }
		return array('purchaseaitems' => $purchaseaitems, 'purchasestones' => $this->get_purchaseItemStones($poid), 'purchaseothermetals' => $this->get_purchaseItemOtherMetal($poid));
	}
	function get_qc_stone_rejected_details($po_item_id,$purchase_type){
	    $sql = $this->db->query("SELECT s.po_st_id,
	    ".($purchase_type==0 ? '(IFNULL(sum(s.qc_rejected_pcs),0) - IFNULL(purret_stn.stn_pcs,0))' :'(IFNULL(sum(s.qc_passed_pcs),0) - IFNULL(purret_stn.stn_pcs,0))')." as qc_rejected_pcs,
	    ".($purchase_type==0 ? '(IFNULL(sum(s.qc_rejected_wt),0) - IFNULL(purret_stn.stn_wt,0))' :'(IFNULL(sum(s.qc_passed_wt),0) - IFNULL(purret_stn.stn_wt,0))')." as qc_rejected_wt,
	    st.stone_name,m.uom_name,m.uom_id,
	    i.is_apply_in_lwt,i.po_stone_id as stone_id,i.po_stone_calc_based_on,st.stone_type,
        i.po_stone_rate,i.po_stone_amount,ifnull(i.po_quality_id,'')  as po_quality_id
        FROM ret_po_qc_issue_stone_details s
        LEFT JOIN ret_po_stone_items i ON i.po_st_id = s.po_st_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = i.po_item_id
        LEFT JOIN ret_stone st ON st.stone_id = i.po_stone_id
        LEFT JOIN ret_uom m ON m.uom_id = i.po_stone_uom
        LEFT join (SELECT stn.po_st_id,i.pur_ret_po_item_id,IFNULL(sum(stn.ret_stone_pcs),0) as stn_pcs,IFNULL(sum(stn.ret_stone_wt),0) as stn_wt
        FROM ret_purchase_return_stone_items stn
        LEFT join ret_purchase_return_items i ON i.pur_ret_itm_id =  stn.pur_ret_return_id
        LEFT JOIN ret_purchase_return r on r.pur_return_id = i.pur_ret_id
        WHERE i.pur_ret_po_item_id = ".$po_item_id." and r.purchase_type = ".$purchase_type."
        GROUP BY stn.po_st_id) as purret_stn on purret_stn.po_st_id = s.po_st_id
        WHERE r.po_item_id = ".$po_item_id."
        GROUP BY s.po_st_id
        HAVING (qc_rejected_pcs > 0 or qc_rejected_wt > 0)");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function get_purchaseItemStones($poId)
	{
	    $stonequery = $this->db->query("SELECT po_stone_id as stone_id, sum(po_stone_pcs) as pieces, sum(po_stone_wt) as stonewt
	                                    FROM ret_po_stone_items as rets
	                                    LEFT JOIN ret_stone as st ON st.stone_id = rets.po_stone_id
                                        WHERE st.stone_type = 1 AND rets.po_item_id = '".$poid."'
                                        GROUP BY rets.po_stone_id");
        return $stonequery->result_array();
	}
	function get_purchaseItemOtherMetal($poId)
	{
	    $stonequery = $this->db->query("SELECT po_item_metal as item_metal, sum(po_other_item_pcs) as pieces, sum(po_other_item_gross_weight) as grosswt
	                                    FROM ret_po_other_item as rets
                                        WHERE rets.po_item_id = '".$poid."'
                                        GROUP BY po_item_metal
                                        ");
        return $stonequery->result_array();
	}
	function getRejectedItemsBySupId($supid)
	{
	    $purchaseitems      = array();
	    $purchasestones     = array();
	    $purchaseothermetals= array();
		$sql = $this->db->query("SELECT po.po_id, poitm.po_item_id, po.po_ref_no, poitm.no_of_pcs as purchasedpcs,
						poitm.gross_wt as purchasedwt,
						poitm.qc_failed_pcs as qcfaildpcs, poitm.qc_failed_gwt as qcfaildwt,
						cat.name as catname, pro.product_name as product_name, des.design_name as design_name,
						subDes.sub_design_name, sup.firstname as karigar , cat.id_ret_category as cat_id, po.po_karigar_id as karigar_id ,
						poitm.qc_failed_pcs as piece , poitm.qc_failed_gwt as weight,sup.firstname as supplier, cat.tgrp_id
						FROM ret_purchase_order_items poitm
						LEFT JOIN ret_purchase_order as po ON po.po_id = poitm.po_item_po_id
						LEFT JOIN ret_category as cat ON cat.id_ret_category = poitm.po_item_cat_id
						LEFT JOIN ret_product_master as pro ON pro.pro_id = poitm.po_item_pro_id
						LEFT JOIN ret_design_master as des ON des.design_no = poitm.po_item_des_id
						LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=poitm.po_item_sub_des_id
						LEFT JOIN ret_karigar as sup ON sup.id_karigar = po.po_karigar_id
						WHERE (poitm.qc_failed_pcs - poitm.po_returned_pcs) > 0 AND (qc_failed_gwt - poitm.po_returned_wt) > 0
						AND po.po_karigar_id = '".$supid."'");
		//print_r($this->db->last_query());exit;
		//return $sql->result_array();
		$purchaseitems = $sql->result_array();
		foreach($purchaseitems as $pkey => $pval){
		    $purchasestones[] = $this->get_purchaseItemStones($pval['po_id']);
		    $purchaseothermetals[] = $this->get_purchaseItemOtherMetal($pval['po_id']);
		}
		return array('purchaseaitems' => $purchaseitems, 'purchasestones' => $purchasestones, 'purchaseothermetals' => $purchaseothermetals);
	}
	function getReturnedRequestList($data)
	{
            $sql=$this->db->query("SELECT r.pur_return_id,r.pur_ret_ref_no,date_format(r.pur_ret_created_on,'%d-%m-%Y') as date_add,IFNULL(r.pur_ret_remark,'') as reason,
            (IFNULL(po_item.ret_pcs,0)) as ret_pcs,IFNULL(po_item.ret_wt,0) as ret_wt,CASE r.purchase_type
    WHEN 0 THEN 'Purchase'
    WHEN 1 THEN 'Sales'
    WHEN 2 THEN 'Sales Return'
    ELSE '-'
END AS purchase_type
,
            k.firstname as karigar_name,r.bill_status,if(r.bill_status=1,'Success','Cancelled') as pur_ret_status,po_item.po_ref_no,po_item.po_id,
            IF(r.pur_ret_convert_to = 1, 'Supplier',IF(r.pur_ret_convert_to = 2, 'Manufacturers',IF(r.pur_ret_convert_to = 3, 'Approval Ledger',IF(r.pur_ret_convert_to = 4, 'Stone Supplier', 'Diamond Supplier')))) AS transcation_type,
            r.tag_issue_from,r.nontag_issue_from,grn.purchase_type AS grn_purchase_type, r.stock_type
            FROM ret_purchase_return r
            LEFT JOIN ret_karigar k ON k.id_karigar=r.pur_ret_supplier_id
            LEFT JOIN (SELECT po.po_ref_no,poitm.pur_ret_id,poitm.pur_ret_itm_id,IFNULL(SUM(poitm.pur_ret_pcs),0) as ret_pcs,IFNULL(SUM(poitm.pur_ret_nwt),0) as ret_wt,
            po.po_id,po.po_grn_id
            FROM ret_purchase_return_items poitm
            LEFT JOIN ret_purchase_order_items pi ON pi.po_item_id = poitm.pur_ret_po_item_id
            LEFT JOIN ret_purchase_order po ON po.po_id = pi.po_item_po_id
            GROUP BY poitm.pur_ret_id) as po_item ON po_item.pur_ret_id = r.pur_return_id
            LEFT JOIN ret_grn_entry grn ON grn.grn_id = po_item.po_grn_id
            where pur_return_id IS NOT NULL
            ".($data['from_date'] != '' && $data['to_date']!='' ? ' and date(r.bill_date) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'"' : '')."
            ".($data['po_ref_no']!='' && $data['po_ref_no']!=null ? " and po_item.po_id =".$data['po_ref_no']."" :'')."
            ".($data['karigar']!='' && $data['karigar']!=null ? " and  r.pur_ret_supplier_id =".$data['karigar']."" :'')."
            ".($data['purchase']!='' && $data['purchase']!=null ? " and  r.purchase_type =".$data['purchase']."" :'')."
            ".($data['stock_type']!='' && $data['stock_type']!=3 ? " and  r.purchase_type =".$data['stock_type']."" :'')."
            ".($data['purchase_type'] != '' && $data['purchase_type'] != 0 ? " and grn.purchase_type = ".$data['purchase_type']."" : '')."
            ".($data['transcation_type']!='' && $data['transcation_type']!=0 ? " and  r.pur_ret_convert_to =".$data['transcation_type']."" :'')."
            ");
        // print_r($this->db->last_query());exit;
            return $sql->result_array();
	}
    function getPurchasePos($data)
	{
        $sql = $this->db->query("SELECT p.po_id as value,p.po_ref_no as label FROM ret_purchase_order p");
		return $sql->result_array();
    }
	function get_return_non_tag_details($pur_ret_id)
	{
	    $sql = $this->db->query("SELECT * FROM `ret_purchase_return_items` WHERE pur_ret_id = ".$pur_ret_id." AND return_item_type =3");
	    return $sql->result_array();
	}
	function get_return_tag_details($pur_ret_id)
	{
	    $sql = $this->db->query("SELECT * FROM `ret_purchase_return_items` WHERE pur_ret_id = ".$pur_ret_id." and tag_id IS NOT NULL");
	    return $sql->result_array();
	}
	function getReturnReceipt($id)
	{
	    $sql=$this->db->query("SELECT r.pur_return_id,r.pur_ret_ref_no,date_format(r.pur_ret_created_on,'%d-%m-%Y') as date_add,IFNULL(r.pur_ret_remark,'') as reason,IFNULL(item.ret_pcs,0) as ret_pcs,IFNULL(item.ret_wt,0) as ret_wt,
        k.firstname as karigar_name,k.contactno1 as mobile,IFNULL(k.address1,'') as address1,
        IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,
        IFNULL(cy.name,'') as city_name,IFNULL(ct.name,'') as country_name,IFNULL(st.name,'') as state_name,
        IFNULL(k.pincode,'') as pincode,k.gst_number,k.pan_no,k.email,k.contactno1 as supplier_mobile,r.pur_ret_round_off,
        r.pur_ret_other_charges,r.pur_ret_tds_percent,r.pur_ret_tds_value,r.pur_ret_other_charges_tds_percent,r.pur_ret_other_charges_tds_value,
        r.pur_ret_tcs_percent,r.pur_ret_tcs_value,r.return_total_cost,r.pur_ret_discount,e.firstname as emp_name,if(r.purchase_type=0,'PURCHASE RETURN','JOB RECEIPT ISSUE') as purchase_type,r.pur_ret_convert_to
        FROM ret_purchase_return r
        LEFT JOIN ret_karigar k ON k.id_karigar=r.pur_ret_supplier_id
        LEFT JOIN country ct ON ct.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city cy ON cy.id_city=k.id_city
        LEFT JOIN employee e On e.id_employee = r.pur_ret_created_by
        LEFT JOIN (SELECT i.pur_ret_id,IFNULL(SUM(i.pur_ret_pcs),0) as ret_pcs,IFNULL(SUM(i.pur_ret_gwt),0) as ret_wt
        FROM ret_purchase_return_items i
        GROUP by i.pur_ret_id) as item ON item.pur_ret_id=r.pur_return_id
        where pur_return_id IS NOT NULL and pur_return_id=".$id."");
        return $sql->row_array();
	}
	function getReturnReceiptDetails($id)
    {
        $sql = $this->db->query("SELECT pureitm.pur_ret_itm_id,p.product_name as product_name, pureitm.pur_ret_pcs as pur_ret_pcs, pureitm.pur_ret_nwt as pur_ret_nwt,pureitm.pur_ret_gwt as pur_ret_gwt,cat.hsn_code,pureitm.pur_ret_debit_note_amt as pur_ret_item_cost,
        pureitm.total_cgst_cost as  pur_ret_cgst,pureitm.total_sgst_cost as pur_ret_sgst,pureitm.total_igst_cost as pur_ret_igst,
        pureitm.pur_ret_rate as pur_ret_rate,pureitm.pur_ret_wastage,pureitm.pur_ret_pur_wt,pureitm.pur_ret_purchase_touch,IFNULL(pureitm.total_total_tax,0) as pur_ret_tax_value
        FROM ret_purchase_return_items as pureitm
        LEFT JOIN ret_purchase_return as purret ON purret.pur_return_id = pureitm.pur_ret_id
        LEFT JOIN ret_product_master as p ON p.pro_id = pureitm.id_product
        LEFT JOIN ret_category as cat ON cat.id_ret_category = p.cat_id
        where purret.pur_return_id=" . $id . "");
            $result= $sql->result_array();
            foreach($result as $items)
            {
                $returnData[]=array(
                    'pur_ret_itm_id'      =>$items['pur_ret_itm_id'],
                    'product_name'        =>$items['product_name'],
                    'pur_ret_pcs'         =>$items['pur_ret_pcs'],
                    'pur_ret_nwt'         =>$items['pur_ret_nwt'],
                    'pur_ret_gwt'         =>$items['pur_ret_gwt'],
                    'hsn_code'            =>$items['hsn_code'],
                    'pur_ret_item_cost'   =>$items['pur_ret_item_cost'],
                    'pur_ret_cgst'        =>$items['pur_ret_cgst'],
                    'pur_ret_sgst'        =>$items['pur_ret_sgst'],
                    'pur_ret_igst'        =>$items['pur_ret_igst'],
                    'pur_ret_rate'        =>$items['pur_ret_rate'],
                    'pur_ret_wastage'     =>$items['pur_ret_wastage'],
                    'pur_ret_pur_wt'       =>$items['pur_ret_pur_wt'],
                    'pur_ret_tax_value'    =>$items['pur_ret_tax_value'],
                    'pur_ret_purchase_touch' =>$items['pur_ret_purchase_touch'],
                    'stn_details'            =>$this->getPurchase_return_StoneDetails($items['pur_ret_itm_id']),
                );
            }
            //print_r($this->db->last_query());exit;
        return $returnData;
    }
    function getPurchase_return_StoneDetails($pur_ret_itm_id)
	{
	    $sql=$this->db->query("SELECT po_stn.pur_ret_stone_id,IFNULL(po_stn.ret_stone_pcs,0) as stone_pcs,IFNULL(po_stn.ret_stone_wt,0) as stone_wt,
       IFNULL(po_stn.ret_stone_rate,0) as stone_rate,IFNULL(po_stn.ret_stone_amount,0) as stone_amount,s.stone_name,uom.uom_name,
       uom.uom_short_code
        FROM ret_purchase_return_stone_items po_stn
        LEFT JOIN ret_stone s ON s.stone_id = po_stn.ret_stone_id
        LEFT JOIN ret_uom uom on uom.uom_id=po_stn.ret_stone_uom
        WHERE po_stn.pur_ret_return_id=".$pur_ret_itm_id."");
        // print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function getReturnReceiptGSTDetails($id)
    {
        $sql = $this->db->query("SELECT pureitm.pur_ret_tax_percent,IFNULL(SUM(total_total_tax),0) as pur_ret_tax_value,
	                            IFNULL(SUM(total_cgst_cost),0) as cgst_cost,IFNULL(SUM(total_sgst_cost),0) as sgst_cost,IFNULL(SUM(total_igst_cost),0) as igst_cost
	                            FROM ret_purchase_return_items as pureitm
	                            LEFT JOIN ret_purchase_return as purret ON purret.pur_return_id = pureitm.pur_ret_id
	                            where purret.pur_return_id=" . $id . " GROUP BY pur_ret_tax_percent");
        return $sql->result_array();
    }
	function get_purchase_ret_charge_details($pur_ret_id)
	{
	    $sql = $this->db->query("SELECT ch.name_charge,c.pur_ret_charge_value,c.pur_ret_charge_tax_value
        FROM ret_purchase_return_other_charges c
        LEFT JOIN ret_charges ch ON ch.id_charge = c.pur_ret_charge_id
        WHERE c.pur_ret_id =".$pur_ret_id."");
        return $sql->result_array();
	}
	function get_purchase_ret_charge_gst_details($pur_ret_id)
	{
	    $sql = $this->db->query("SELECT IFNULL(SUM(c.cgst_cost),0) as cgst_cost,IFNULL(SUM(c.sgst_cost),0) as sgst_cost,
	    IFNULL(SUM(c.igst_cost),0) as igst_cost,c.pur_ret_charge_tax
        FROM ret_purchase_return_other_charges c
        LEFT JOIN ret_charges ch ON ch.id_charge = c.pur_ret_charge_id
        WHERE c.pur_ret_id =".$pur_ret_id."
        GROUP BY c.pur_ret_charge_tax");
        return $sql->result_array();
	}
	function pur_ret_refno($PurType)
	{
		$lastno = $this->get_max_pur_ret_refno($PurType);
		if($lastno!=NULL)
		{
			$max_num = explode("-",$lastno);
            $number = (int) $max_num[1];
            //$number = (int) $lastno;
			$number++;
			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);
			return $code_number;
		}
		else
		{
			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);
			return $code_number;
		}
	}
	function get_max_pur_ret_refno($PurType)
    {
		$sql = "SELECT pur_ret_ref_no FROM `ret_purchase_return` where purchase_type = ".$PurType." ORDER BY pur_return_id DESC";
		return $this->db->query($sql)->row()->pur_ret_ref_no;
	}
	function get_tag_details($tag_id)
	{
	    $sql=$this->db->query("SELECT * FROM `ret_taging` WHERE tag_id=".$tag_id."");
	    return $sql->row_array();
	}
	//Purchase Item return
	//order description
	function get_order_description()
	{
	    $sql=$this->db->query("SELECT * FROM `ret_purchase_order_description`");
	    return $sql->result_array();
	}
	function get_orderdescription($id="")
	{
	    $sql=$this->db->query("SELECT * FROM `ret_purchase_order_description` where id_order_des=".$id."");
	    return $sql->row_array();
	}
	//order description
	//mail function
	public function send_email($send_mail_from,$email_to,$email_subject,$email_message,$email_cc="",$email_bcc="",$attachment) {
	     $r = array();
	     $sql="SELECT * FROM company";
	     $r = $this->db->query($sql)->result_array();
		 $config = array();
                $config['useragent']     = "CodeIgniter";
                $config['mailpath']      = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
                $config['protocol']      = "smtp";
                $config['smtp_host']     = "localhost";
                $config['smtp_port']     = "25";
                $config['mailtype']		 = 'html';
                $config['charset'] 		 = 'utf-8';
                $config['newline'] 		 = "\r\n";
                $config['wordwrap']		 = TRUE;
                $this->load->library('email');
                $this->email->initialize($config);
                $this->email->from($send_mail_from, $r[0]['company_name']);
                $this->email->to($email_to);
				if($email_cc!="")
				{
					$this->email->cc($email_cc);
				}
     			if($email_bcc!="")
				{
                   $this->email->bcc($email_bcc);
			    }
			    if($attachment!="")
    			{
    			    $this->email->attach($attachment);
    			}
            $this->email->subject($email_subject);
            $this->email->message($email_message);
            //$this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
            $send= $this->email->send();
            if($send)
            {
                return true;
            }else
            {
                echo $this->email->print_debugger();
            }
	}
	//mail function
	function get_karigar_pending_orders($data)
	{
	    $sql=$this->db->query("SELECT c.id_customerorder,c.pur_no,k.firstname as karigar_name
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=j.id_vendor
        WHERE c.order_for=1 AND j.id_vendor IS NOT NULL AND d.orderstatus=3 AND c.pur_no IS NOT NULL
        ".($data['id_karigar']!='' && $data['id_karigar']!=null ? " and j.id_vendor=".$data['id_karigar']."" :'')."
        GROUP by c.id_customerorder");
        return $sql->result_array();
	}
		function get_karigar_pending_order_details($data)
	{
	    $sql=$this->db->query("SELECT (d.totalitems) as tot_items,concat(w.value,'',u.uom_name) as weight_range,k.firstname as karigar_name,IFNULL(k.email,'') as email,c.company_name,
	    p.product_name,des.design_name,IFNULL(concat(s.value,' ',s.name),'') as size,IFNULL(k.contactno1,'') as mobile,cus.order_no,date_format(cus.order_date,'%d-%m-%Y') as order_date,
	    emp.firstname as emp_name,subDes.sub_design_name,cus.pur_no, date_format(d.smith_due_date, '%d-%m-%Y') as smith_due_date,
	    IFNULL(d.description,'') as description,d.id_orderdetails,j.id as id_joborder,IFNULL(d.delivered_qty,0) as delivered_pcs
        FROM customerorder cus
        JOIN company c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=cus.id_customerorder
        LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
        LEFT JOIN ret_karigar k ON k.id_karigar=j.id_vendor
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=d.id_sub_design
        LEFT JOIN ret_size s ON s.id_size=d.size
        LEFT JOIN ret_weight w ON w.id_weight=d.weight
        LEFT JOIN ret_uom u ON u.uom_id=w.id_uom
        LEFT JOIN employee emp on emp.id_employee=cus.order_taken_by
        WHERE cus.order_for=1  AND cus.id_customerorder=".$data['id_customerorder']." and d.orderstatus=3
        ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function update_partial_order_delivery($data,$arith){
		$sql = "UPDATE customerorderdetails SET delivered_qty=(delivered_qty".$arith." ".$data['delivered_qty']."),delivered_wt=(delivered_wt".$arith." ".$data['delivered_wt']."),is_partial_delivery=".$data['is_partial_delivery']."
		WHERE id_orderdetails=".$data['id_orderdetails'];
		//print_r($sql);exit;
		$status = $this->db->query($sql);
		return $status;
	}
	function update_order_delivery($data,$arith){
		$sql = "UPDATE customerorderdetails SET delivered_qty=(delivered_qty".$arith." ".$data['delivered_qty']."),delivered_wt=(delivered_wt".$arith." ".$data['delivered_wt'].")
		WHERE id_orderdetails=".$data['id_orderdetails'];
		//print_r($sql);exit;
		$status = $this->db->query($sql);
		return $status;
	}
    //Metal Issue
    function get_metal_issue_ref_no()
	{
	    $lastno = NULL;
	    $sql = "SELECT MAX(met_issue_ref_id) as lastorder_no
					FROM ret_karigar_metal_issue i
					ORDER BY met_issue_id DESC 
					LIMIT 1";

			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->lastorder_no;
			}
	    if($lastno != NULL)
		{
            $max_num = explode("-",$lastno);
            $number = (int) $max_num[1];
            //$number = (int) $lastno;
            $number++;
            $order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $order_number;
	}
    function get_profile_settings($id_profile)
    {
       $sql=$this->db->query("SELECT * FROM `profile` WHERE id_profile=".$id_profile."");
       return $sql->row_array();
    }
	function getKarigarMetalIssueList()
	{
	    $sql=$this->db->query("SELECT i.met_issue_id,k.firstname as karigar_name,date_format(i.met_issue_date,'%d-%m-%Y') as issue_date,i.met_issue_ref_id,IFNULL(d.metal_wt,0) as metal_wt,IFNULL(d.issue_metal_pur_wt,0) as issue_metal_pur_wt,
	    IFNULL(c.pur_no,'-') as pur_no,if(i.bill_status=1,'Success','Cancelled') as bill_status,IFNULL(i.remark,'') as remark,ifnull(d.po_ref_no,'') as po_ref_no,
        IF(i.tag_issue_from =1 and i.nontag_issue_from =1 ,'Available Stock',if(i.tag_issue_from =2,'Sales Return',if(i.tag_issue_from = 3 ,'Partly Sales',if(i.tag_issue_from =4 ,'H.O Other Issue',if(i.nontag_issue_from = 2,'NonTag Sales Return','NonTag Other Issue'))))) as tag_issue
        FROM ret_karigar_metal_issue i
        LEFT JOIN ret_karigar k ON k.id_karigar=i.met_issue_karid
        LEFT JOIN customerorder c ON c.id_customerorder=i.id_order
        LEFT JOIN(SELECT SUM(d.issue_metal_wt) as metal_wt,d.issue_met_parent_id,IFNULL(SUM(d.issue_metal_pur_wt),0) as issue_metal_pur_wt,
        po.po_ref_no
        FROM ret_karigar_metal_issue_details d
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = d.po_item_id
        LEFT JOIN ret_purchase_order po on po.po_id = po_itm.po_item_po_id
        GROUP by d.issue_met_parent_id) as d ON d.issue_met_parent_id=i.met_issue_id
        GROUP by i.met_issue_id");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
    function get_available_stock_details($data)
	{
	    /*$sql=$this->db->query("SELECT p.cat_id,s.purity,s.type,s.id_product,IFNULL(SUM(s.pieces),0) as pieces,IFNULL(SUM(s.net_wt),0) as net_wt,IFNULL(SUM(s.gross_wt),0) as gross_wt,
        p.product_name
        FROM ret_purchase_item_stock_summary s
        LEFT JOIN ret_product_master p ON p.pro_id=s.id_product
        WHERE s.id_stock_summary IS NOT NULL
        GROUP by s.type=1,s.id_product,s.purity
        HAVING purity>0");*/
        $stock_data = array();
        $sql = $this->db->query("SELECT nt.no_of_piece,nt.gross_wt,nt.net_wt,nt.product as id_product,p.product_name,nt.design,nt.id_sub_design,
        c.name as category_name,c.id_ret_category as cat_id,c.tgrp_id,nt.id_section,c.id_metal,ifnull(sect.section_name,'') as section_name,br.id_branch
        FROM ret_nontag_item nt
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        LEFT JOIN branch br ON br.id_branch = nt.branch
        LEFT JOIN ret_section sect on sect.id_section=nt.id_section
        WHERE br.id_branch is not null and br.is_ho = 1
        ".($data['id_product']!='' ? "and nt.product = ".$data['id_product']."" :'')."
         HAVING nt.gross_wt > 0");
        $data1 =  $sql->result_array();
        foreach($data1 as $r)
        {
            $stock_data[] = array(
                'no_of_piece'    => $r['no_of_piece'],
                'gross_wt'       => $r['gross_wt'],
                'net_wt'         => $r['net_wt'],
                'id_branch'       => $r['id_branch'],
                'id_metal'       => $r['id_metal'],
                'cat_id'         => $r['cat_id'],
                'id_product'     => $r['id_product'],
                'id_section'     => $r['id_section'],
                'design'         => $r['design'],
                'id_sub_design'  => $r['id_sub_design'],
                'category_name'  => $r['category_name'],
                'product_name'   => $r['product_name'],
                'section_name'   => $r['section_name'],
                'purity'         => $this->getCatPurity($r['cat_id']),
            );
        }
        return $stock_data;
	}
    function getCatPurity($id_cat)
    {
        $data = $this->db->query("SELECT cp.id_purity,purity FROM `ret_metal_cat_purity` cp
        LEFT JOIN ret_purity p on p.id_purity = cp.id_purity
        WHERE id_category IS NOT NULL
        ".($id_cat!='' && $id_cat>0 ? " and id_category = ".$id_cat."":"")."
		group by p.id_purity");
        return $data->result_array();
	}
	function get_metal_issue_purity($purity)
	{
	    $sql=$this->db->query("SELECT * FROM ret_purity WHERE purity='".$purity."'");
	    return $sql->row_array();
	}
	function getMetalIssue($id)
	{
	    $sql=$this->db->query("SELECT m.met_issue_id,m.met_issue_ref_id,date_format(m.met_issue_date,'%d-%m-%Y') as issue_date,
        IFNULL(k.firstname,'') as karigar_name,k.contactno1 as mobile,IFNULL(k.address1,'') as address1,
        IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,
        IFNULL(cy.name,'') as city_name,IFNULL(ct.name,'') as country_name,IFNULL(st.name,'') as state_name,
        IFNULL(k.pincode,'') as pincode,IF(m.metalissue_type=1,'JOB WORK ISSUE ',IF(m.metalissue_type=2,'Approval ','')) as issue_type,
        IFNULL(m.remark,'') as remark,IFNULL(emp.firstname,'') as emp_name,IFNULL(o.pur_no,'') as pur_no,IFNULL(repair.order_no,'') as order_no

        FROM ret_karigar_metal_issue m
        LEFT join customerorder o on o.id_customerorder=m.id_order
        LEFT join customerorder repair on repair.id_customerorder=o.cus_ord_ref
        LEFT JOIN ret_karigar k ON k.id_karigar=m.met_issue_karid
        LEFT JOIN country ct ON ct.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city cy ON cy.id_city=k.id_city
        LEFT JOIN employee emp ON emp.id_employee =m.met_issue_created_by
        WHERE m.met_issue_id=".$id."");
        return $sql->row_array();
	}
	function getMetalIssueDetails($id)
	{
	    $sql=$this->db->query("SELECT IFNULL(d.issue_metal_wt,0) as issue_wt,IFNULL(d.issue_metal_pur_wt,0) as issue_pure_wt,
        c.name as category_name,c.hsn_code,p.purity,pro.product_name,mt.metal,ifnull(d.issue_pcs,0) as issue_pcs,t.tag_code,
        IFNULL(brch.branch_trans_code,'') as bt_code,IFNULL(po.po_ref_no,'') as po_ref_no,if(d.is_repair_item = 1,d.net_wt,d.issue_metal_wt) as net_wt,IFNULL(d.less_wt,0) as less_wt,
        uom.uom_name,IFNULL(des.design_name,'') as design_name,IFNULL(tag_stn.wt,0) as dia_wt,d.mc,d.wastage,IFNULL(d.touch,p.purity) as touch
        
        FROM ret_karigar_metal_issue_details d
        LEFT JOIN ret_category c ON c.id_ret_category=d.issue_cat_id
        LEFT JOIN ret_taging t ON t.tag_id=d.tag_id
        LEFT JOIN ret_design_master des ON des.design_no=d.issu_met_id_design	
        LEFT join ret_uom uom on uom.uom_id = d.issue_uom_id
        LEFT JOIN ret_purchase_order_items i ON i.po_item_id = d.po_item_id
        LEFT JOIN ret_purchase_order po ON po.po_id = i.po_item_po_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =d.branch_trans_id 
        LEFT JOIN ret_purity p ON p.id_purity=d.issue_pur_id
        LEFT JOIN ret_product_master pro ON pro.pro_id=d.issu_met_pro_id
        LEFT JOIN metal mt ON mt.id_metal=d.issue_metal
        LEFT JOIN ( SELECT st.tag_id,uom_id,SUM(wt) as wt  FROM ret_taging_stone st WHere uom_id = 6 GROUP BY st.tag_id ) as tag_stn ON tag_stn.tag_id = d.tag_id
        WHERE d.issue_met_parent_id=".$id."
        ORDER BY d.issu_met_pro_id");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	function checkNonTagItemExist($data){
		$r = array("status" => FALSE);
		$id_design = (isset($data['design']) ? ($data['design']!='' ? $data['design'] :'') :'');
		$id_sub_design = (isset($data['id_sub_design']) ? ($data['id_sub_design']!='' ? $data['id_sub_design'] :'') :'');
		$id_section = (isset($data['id_section']) ? ($data['id_section']!='' ? $data['id_section'] :'') :'');
        $sql = "SELECT id_nontag_item FROM ret_nontag_item WHERE
        product=".$data['id_product']."
        ".($id_design!='' ? " and design=".$id_design."" :'')."
        ".($id_sub_design!='' ? " and id_sub_design=".$id_sub_design."" :'')."
        ".($id_section!='' ? " and id_section=".$id_section."" :'')."
        AND branch=".$data['id_branch'];
        //print_r($sql);exit;
        $res = $this->db->query($sql);
		if($res->num_rows() > 0){
			$r = array("status" => TRUE, "id_nontag_item" => $res->row()->id_nontag_item);
		}else{
			$r = array("status" => FALSE, "id_nontag_item" => "");
		}
		return $r;
	}
	function updateNTData($data,$arith){
		$sql = "UPDATE ret_nontag_item SET no_of_piece=(IFNULL(no_of_piece,0)".$arith." ".$data['no_of_piece']."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_nontag_item=".$data['id_nontag_item'];
		$status = $this->db->query($sql);
		return $status;
	}
	function get_KarigarMetal_issue($metal_issue_id)
    {
        $sql = $this->db->query("SELECT isd.issu_met_pro_id as pro_id,isd.issue_metal_wt as weight,IFNULL(isd.tag_id,'') as tag_id,iss.id_branch
        FROM ret_karigar_metal_issue_details isd
        LEFT JOIN ret_karigar_metal_issue iss ON iss.met_issue_id = isd.issue_met_parent_id
        WHERE isd.issue_met_parent_id=".$metal_issue_id."");
        return $sql->result_array();
    }
    //Metal Issue
     //Purchase Item Stock Summary
    function get_purity_details($id_purity)
    {
        $sql=$this->db->query("SELECT * FROM ret_purity WHERE id_purity=".$id_purity."");
        return $sql->row_array();
    }
    function checkPurchaseItemStockExist($data)
    {
		$r = array("status" => FALSE);
        $sql = "SELECT * FROM `ret_purchase_item_stock_summary`  WHERE id_stock_summary IS NOT NULL
        ".($data['id_product']!='' ? " and id_product=".$data['id_product']."" :'')."
        ".($data['id_branch']!='' ?  " and id_branch=".$data['id_branch']."" :'')."
        ".($data['purity']!='' ?  " and purity=".$data['purity']."" :'')."
        ";
        $res = $this->db->query($sql);
		if($res->num_rows() > 0){
			$r = array("status" => TRUE,'id_stock_summary'=>$res->row()->id_stock_summary);
		}else{
			$r = array("status" => FALSE);
		}
		return $r;
	}
	function get_return_bill_details($bill_detail_id)
	{
	    $sql=$this->db->query("SELECT * FROM ret_bill_details d WHERE d.bill_det_id=".$bill_detail_id."");
	    return $sql->row_array();
	}
	function updatePurItemData($id_stock_summary,$data,$arith){
		$sql = "UPDATE ret_purchase_item_stock_summary SET pieces=(pieces".$arith." ".$data['pieces']."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),less_wt=(less_wt".$arith." ".$data['less_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."'
		WHERE id_stock_summary=".$id_stock_summary." ";
		$status = $this->db->query($sql);
		return $status;
	}
    //Purchase Item Stock Summary
    function get_purchase_order_pending_details($data)
    {
        $returnData=array();
        if($data['po_id']!='')
        {
            $sql=$this->db->query("SELECT c.id_customerorder,c.pur_no,c.order_type,IFNULL(cusRef.order_type,'') as ref_order_type,
            IFNULL(cusRef.order_no,'') as order_no, d.rate_per_gram as rate_per_gram, c.rate_type as rate_type
            FROM customerorderdetails d
            LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
            LEFT JOIN customerorder cusRef on cusRef.id_customerorder=c.cus_ord_ref
            WHERE ".($data['id_karigar']!='' ? " c.id_karigar=".$data['id_karigar']."" :'')."
            GROUP BY d.id_customerorder");

            // echo $this->db->last_query();exit;
            $result= $sql->result_array();
        }
        else
        {
            $sql=$this->db->query("SELECT c.id_customerorder,c.pur_no,c.order_type,IFNULL(cusRef.order_type,'') as ref_order_type,
            IFNULL(cusRef.order_no,'') as order_no, d.rate_per_gram as rate_per_gram, c.rate_type as rate_type
            FROM customerorderdetails d
            LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
            LEFT JOIN customerorder cusRef on cusRef.id_customerorder=c.cus_ord_ref
            WHERE c.pur_no is not null and c.order_status<=3 ".($data['id_karigar']!='' ? " and c.id_karigar=".$data['id_karigar']."" :'')."
            GROUP BY d.id_customerorder");

            // echo $this->db->last_query();exit;
            $result= $sql->result_array();
        }
        foreach($result as $items)
        {
            $order_details = $this->get_purchase_cus_order_details($items['id_customerorder']);

            $item_details = ($items['ref_order_type'] == 4 ? $this->get_tag_cus_order_details($items['id_customerorder']): []);
            
           if( ($items['ref_order_type'] == 4 && count($item_details) > 0 ) || $items['ref_order_type'] !=4  ) {

                $returnData[]=array(
                    'id_customerorder'  =>$items['id_customerorder'],
                    'pur_no'            =>$items['pur_no'],
                    'order_type'        =>$items['order_type'],
                    'ref_order_type'    =>$items['ref_order_type'],
                    'order_no'          =>$items['order_no'],
                    'rate_type'         =>$items['rate_type'],
                    'rate_per_gram'     =>$items['rate_per_gram'],
                    'order_details'     =>$order_details,
                    'item_details'      =>$item_details,
                );
            }


        }
        return $returnData;
    }
    function get_tag_cus_order_details($id_customerorder)
    {
        $response_data =[];
        $sql=$this->db->query("SELECT tag.tag_status,tag.tag_id,tag.tag_code,date_format(tag.tag_datetime,'%d-%m-%Y') as tag_date, k.firstname as supplier_name, cat.id_metal,cat.id_ret_category  as cat_id,cat.name  as catname,
        tag.id_section,IFNULL(sec.section_name,'') as section,p.pro_id,des.design_no as des_id,s.id_sub_design,p.product_name,pur.id_purity,pur.purity,
        tag.piece,tag.gross_wt as weight,tag.tag_status as tagging_status,tag.less_wt,IFNULL(prs.purchase_touch,0) as purchase_touch,IFNULL(prs.item_wastage,0) as purchase_wastage,
        IFNULL(prs.mc_value,0) as purchase_mc_value,IFNULL(prs.mc_type,0) as purchase_mc_type,IFNULL(prs.pure_wt_calc_type,0) as calc_type,tag.net_wt,p.calculation_based_on,d.cus_orderdet_ref as id_orderdetails
        FROM customerorderdetails d
        LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
        LEFT JOIN ret_taging tag ON tag.id_orderdetails  = d.cus_orderdet_ref
        LEFT JOIN ret_lot_inwards lot ON lot.lot_no  = tag.tag_lot_id
        LEFT JOIN ret_karigar k ON k.id_karigar=lot.gold_smith
        LEFT JOIN ret_purity pur ON pur.id_purity=tag.purity
        LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=d.id_sub_design
        LEFT JOIN ret_weight as wt ON wt.id_weight = d.id_weight_range
        LEFT JOIN ret_lot_inwards i ON i.lot_no = tag.tag_lot_id
        LEFT JOIN ret_purchase_order po ON po.po_id = i.po_id
        LEFT JOIN ret_purchase_order_items prs ON prs.po_item_po_id = po.po_id AND prs.po_item_pro_id = tag.product_id
        AND prs.po_item_des_id = tag.design_id AND prs.po_item_sub_des_id = tag.id_sub_design
        where  d.id_customerorder=".$id_customerorder."  and tag.tag_status = 0
        GROUP BY d.id_product, d.design_no, d.id_sub_design, id_weight_range,tag.tag_id");

        //print_r($this->db->last_query());exit;
        $response_data = $sql->result_array();

        foreach($response_data as &$row){

            $row['stone_details'] = $this->getorderstones($row['id_orderdetails']);
        }
        
        return $response_data;
    }

    function getorderstones($id_orderdetails)
    {
        $sql=$this->db->query("SELECT os.is_apply_in_lwt as show_in_lwt,os.stone_id,os.pieces as stone_pcs,os.wt as stone_wt,
        os.price as stone_price,os.stone_cal_type,os.rate_per_gram as stone_rate,st.stone_type as stones_type,
        st.stone_name,uom.uom_short_code,os.uom_id as stone_uom_id
        FROM ret_order_item_stones os
        LEFT JOIN ret_stone st ON st.stone_id = os.stone_id
        LEFT JOIN ret_uom uom on uom.uom_id=os.uom_id
        where os.order_item_id=".$id_orderdetails."");
        // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    /*function get_purchase_cus_order_details($id_customerorder)
    {
        $sql=$this->db->query("SELECT p.product_name,des.design_name,s.sub_design_name,d.id_customerorder,d.id_product,d.design_no,
        d.id_sub_design,d.weight,d.totalitems,d.pure_wt
        FROM customerorderdetails d
        LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=d.id_sub_design
        where d.id_customerorder=".$id_customerorder."");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }*/
    function get_purchase_cus_order_details($id_customerorder)
    {
        $sql=$this->db->query("SELECT p.product_name,des.design_name,s.sub_design_name,d.id_customerorder,d.id_product,d.design_no,
        d.id_sub_design,ifnull(sum(d.weight),0) as weight,ifnull(sum(d.totalitems),0) as totalitems,ifnull(sum(d.pure_wt),0) as pure_wt, p.cat_id as cat_id,
        ifnull(wt.weight_description, sum(d.weight)) as weight_description, cat.name as catname,
        ifnull((SELECT sum(no_of_pcs) FROM ret_purchase_order_items WHERE po_order_no = d.id_customerorder AND
                po_item_pro_id = d.id_product AND po_item_des_id = d.design_no AND  po_item_sub_des_id = d.id_sub_design),0) as receivedpcs ,
        ifnull((SELECT sum(gross_wt) FROM ret_purchase_order_items WHERE po_order_no = d.id_customerorder AND
                po_item_pro_id = d.id_product AND po_item_des_id = d.design_no AND  po_item_sub_des_id = d.id_sub_design),0) as receivedwt
        FROM customerorderdetails d
        LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=d.id_sub_design
        LEFT JOIN ret_weight as wt ON wt.id_weight = d.id_weight_range
        where d.id_customerorder=".$id_customerorder." GROUP BY d.id_product, d.design_no, d.id_sub_design, id_weight_range");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_OrderProducts($data)
    {
        $sql=$this->db->query("SELECT p.pro_id,p.product_name,p.purchase_mode,p.tax_type
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        WHERE c.id_customerorder=".$data['id_customerorder']."
        ".($data['id_ret_category']!='' ? " and p.cat_id=".$data['id_ret_category']."" :'')."
        GROUP BY d.id_product");
       // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_OrderProductsDesign($data)
    {
        $sql=$this->db->query("SELECT des.design_no,des.design_name
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_design_master des ON des.design_no=d.design_no
        WHERE c.id_customerorder=".$data['id_customerorder']."
        ".($data['id_product']!='' ? " and d.id_product=".$data['id_product']."" :'')."
        GROUP BY d.design_no");
        return $sql->result_array();
    }
    function get_OrderSubDesigns($data)
    {
        $sql=$this->db->query("SELECT des.id_sub_design,des.sub_design_name
        FROM customerorder c
        LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
        LEFT JOIN ret_sub_design_master des ON des.id_sub_design=d.id_sub_design
        WHERE c.id_customerorder=".$data['id_customerorder']."
        ".($data['id_product']!='' ? " and d.id_product=".$data['id_product']."" :'')."
        ".($data['design_no']!='' ? " and d.design_no=".$data['design_no']."" :'')."
        GROUP BY d.id_sub_design");
        return $sql->result_array();
    }
    function get_pur_order_det($id_customerorder)
    {
        $sql=$this->db->query("SELECT IFNULL(c.delivered_qty,0) as delivered_qty,IFNULL(c.order_pcs,0) as order_pcs, IFNULL(c.order_approx_wt,0) as order_approx_wt, order_type, IFNULL(cus_ord_ref,0) as cus_ord_ref FROM customerorder c  WHERE c.id_customerorder=".$id_customerorder."");
        return $sql->row_array();
    }
    /*function get_cus_order_details($id_cus_order, $id_product){
        $sql = $this->db->query("SELECT id_orderdetails FROM `customerorderdetails` WHERE id_customerorder = '".$id_cus_order."' AND id_product = '".$id_product."' ");
        return $sql->row_array();
    }*/
    function get_cus_order_details($id_cus_order, $id_product, $design_no = "", $id_sub_design = "" ){
        $sql = $this->db->query("SELECT id_orderdetails FROM `customerorderdetails`
                                    WHERE id_customerorder = '".$id_cus_order."' AND id_product = '".$id_product."'
                                    ".($design_no !='' ? " and design_no = ".$design_no."" :'')."
                                    ".($id_sub_design!='' ? " and id_sub_design=".$id_sub_design."" :'')."
                                    ");
        return $sql->row_array();
    }
    function updatePurOrderStatus($data,$arith){
		$sql = "UPDATE customerorder SET delivered_qty=(delivered_qty".$arith." ".$data['delivered_qty']."),delivered_wt=(delivered_wt".$arith." ".$data['delivered_wt'].") WHERE id_customerorder=".$data['id_customerorder'];
		//print_r($sql);exit;
		$status = $this->db->query($sql);
		return $status;
	}
	function updatePurOrderDetailStatus($data, $updateId, $arith){
		$sql = "UPDATE customerorderdetails SET delivered_qty=(delivered_qty".$arith." ".$data['delivered_qty']."),delivered_wt=(delivered_wt".$arith." ".$data['delivered_wt']."),
		      orderstatus = if(totalitems <= delivered_qty ".$arith." ".$data['delivered_qty'].")  WHERE id_orderdetails=".$updateId;
		//print_r($sql);exit;
		$status = $this->db->query($sql);
		return $status;
	}
	function get_karigarPendingPos($karigar){
        $sql    = $this->db->query("SELECT po.po_id, po.po_ref_no, k.firstname as karigar, podet.no_of_pcs as receivedpcs,
                            podet.gross_wt as received_gwt, ifnull(podet.qc_passed_gwt, 0) as qc_passed_gwt, ifnull(podet.qc_passed_pcs,0) as passedpcs,
                            ifnull(podet.item_cost,0) as item_cost ,
                            ifnull(payment.pay_amt, 0) paidamt, ifnull(podet.item_cost,0) - ifnull(payment.pay_amt, 0) as balanceamt
                            FROM ret_purchase_order as po
                            LEFT JOIN
                            (SELECT po_item_po_id as poitem_id, sum(no_of_pcs) as no_of_pcs, sum(gross_wt) as gross_wt,
                            sum(less_wt) as less_wt, sum(net_wt) as net_wt, sum(qc_failed_pcs) as qc_failed_pcs,
                            sum(qc_failed_gwt) as qc_failed_gwt, sum(qc_failed_nwt) as qc_failed_nwt,
                            sum(qc_failed_lwt) as qc_failed_lwt,
                            sum(qc_passed_gwt) as qc_passed_gwt,
                            sum(qc_passed_lwt) as qc_passed_lwt,
                            sum(qc_passed_pcs) as qc_passed_pcs,
                            sum(qc_passed_nwt) as qc_passed_nwt,
                            sum(po_returned_pcs) as po_returned_pcs,
                            sum(po_returned_wt) as po_returned_wt ,
                             sum(item_cost) as item_cost
                             FROM ret_purchase_order_items as puitm
                             LEFT JOIN ret_purchase_order as pu ON pu.po_id = puitm.po_item_po_id
                             WHERE pu.po_karigar_id ='".$karigar."'
                             GROUP BY po_item_po_id
                            ) as podet ON podet.poitem_id = po.po_id
                            LEFT JOIN (SELECT pay_po_id,pay_po_ref_id,
                            sum(payd.pay_po_adj_amount) as pay_amt
                            FROM ret_po_payment as paymet
                            LEFT JOIN ret_supplier_pay_po_details as payd ON payd.po_pay_id = paymet.pay_id
                            GROUP BY pay_po_ref_id) as payment ON payment.pay_po_ref_id = po.po_id
                            LEFT JOIN ret_karigar k ON k.id_karigar=po.po_karigar_id
                            WHERE po.po_karigar_id ='".$karigar."' AND (ifnull(podet.item_cost,0) - ifnull(payment.pay_amt, 0)) > 0 ORDER BY po.po_id ASC");
        //echo $this->db->last_query();exit;
        $response_data = $sql->result_array();
        return $response_data;
	}
	function get_karigarPosPaidHistory($karigar){
	    $sql    = $this->db->query("SELECT pay.pay_id, date_format(pay.pay_create_on,'%d-%m-%Y') as pay_date, pay.pay_refno,
	                            IFNULL(pay.pay_amt,0) as tot_cash_pay, IFNULL(pay.pay_wt,0) as tot_pay_wt,
	                            ifnull(paydet.po_ref_no, '-') as po_ref_no , if(bill_type = 1, 'Purchase', 'Advance') as billtype, payamt
	                            FROM ret_po_payment pay
	                            LEFT JOIN(SELECT po_pay_id, sum(pay_po_adj_amount) as payamt, group_concat(p.po_ref_no) as  po_ref_no
	                                        FROM ret_supplier_pay_po_details as payd
	                                        LEFT JOIN ret_purchase_order p ON p.po_id = payd.pay_po_ref_id GROUP BY po_pay_id) as paydet ON paydet.po_pay_id = pay.pay_id
	                            WHERE pay.pay_sup_id='".$karigar."' ORDER BY pay.pay_id DESC LIMIT 10");
        $response_data = $sql->result_array();
        //echo $this->db->last_query();exit;
        return $response_data;
	}
	function get_po_paid_detail($payid){

	    $return_data = array();

	    $payment_sql = $this->db->query("SELECT pay.pay_id, date_format(pay.pay_create_on,'%d-%m-%Y') as pay_date, pay.pay_refno, 

	                            IFNULL(pay.pay_amt,0) as tot_cash_pay,if(pay.bill_type = 1,'Supplier payment',if(pay.bill_type = 2,'Smith payment','')) as  bill_type,

	                             k.firstname as karigar,IFNULL(emp.firstname,'') as emp,

	                             pay.pay_sup_id,IFNULL(pay.pay_narration,'') as remarks,k.contactno1 as mobile,k.code_karigar,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,IFNULL(cy.name,'') as country_name,IFNULL(st.name,'') as state_name,IFNULL(ct.name,'') as city_name,

	                            IFNULL(k.company,'') as company_name,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,

	                            cy.name as country_name,st.name as state_name,ct.name as city_name, IFNULL(k.gst_number,'') as gst_number

	                             FROM ret_po_payment pay 

	                              LEFT JOIN ret_karigar k ON k.id_karigar=pay.pay_sup_id 

                                  LEFT JOIN employee emp ON emp.id_employee =pay.pay_created_by 

                                  LEFT JOIN country cy ON cy.id_country = k.id_country

                                LEFT JOIN state st ON st.id_state = k.id_state

                                LEFT JOIN city ct ON ct.id_city = k.id_city

	                            WHERE pay_id = '".$payid."'");

	   $return_data['paydetails'] = $payment_sql->row_array();

	   if($return_data['paydetails']['bill_type'] == 1){

	       $po_query = $this->db->query("SELECT po_pay_id, pay_po_adj_amount as payamt, p.po_ref_no as  po_ref_no

	                                        FROM ret_supplier_pay_po_details as payd

	                                        LEFT JOIN ret_purchase_order p ON p.po_id = payd.pay_po_ref_id 

	                                   WHERE po_pay_id = '".$payid."'");

	        $return_data['pay_po_details'] = $po_query->result_array();  

	   }

	   $paymode_query = $this->db->query("SELECT id_pay_details, pay_id, type, pay_mode, payment_amount, ref_no, bill_id FROM ret_po_payment_detail WHERE pay_id = '".$payid."'");

	   $return_data['pay_mode_details'] = $paymode_query->result_array();  

	   

	   return $return_data;

	   

	}

    function get_po_paid_payment($id)

	{


		$items_query = $this->db->query("SELECT p.payment_amount,p.pay_mode as payment_mode,p.ref_no,
        
        IFNULL(p.type,'') as transfer_type,date_format(p.ref_date,'%d-%m-%Y') as ref_date,

        date_format(p.cheque_date,'%d-%m-%Y') as cheque_date,p.cheque_no,p.id_bank,p.payee_name,p.bank_branch,
        kar.acc_number		

		FROM ret_po_payment_detail p
        LEFT JOIN ret_po_payment pay ON p.pay_id = pay.pay_id
        LEFT JOIN bank b ON b.id_bank = p.id_bank
        LEFT JOIN ret_karigar kar ON kar.id_karigar = pay.pay_sup_id

		where p.pay_id=".$id."");

		$data=$items_query->result_array();

		return $data;

	}
    
	function getPurchaseOrderDet($po_id)
	{
        $return_data=array();
	    $sql=$this->db->query("SELECT p.po_id,p.purchase_type,p.po_type,p.is_suspense_stock,p.po_karigar_id,p.purchase_order_no,
        p.po_supplier_ref_no,p.po_ref_date,p.ewaybillno,p.despatch_through,p.id_category,p.id_purity,p.po_irnno,
        p.po_grn_id,g.grn_ref_no,k.firstname as supplier_name,IFNULL(k.contactno1,'') as mobile,IFNULL(k.gst_number,'') as gst_number,
        IFNULL(k.pan_no,'') as pan_no,cy.name as supplier_country_name,st.name as supplier_state_name,ct.name as supplier_city_name,
        IFNULL(k.address1,'') as supplier_address1,IFNULL(k.address2,'') as supplier_address2,g.grn_type AS grn_type1,
        IFNULL(k.address3,'') as supplier_address3,IFNULL(k.pincode,'') as supplier_pincode,st.state_code as supplier_state_code,p.po_ref_no,date_format(po_date,'%d-%m-%Y') as po_date,
        e.firstname as emp_name,if(g.grn_type=1,'GST PURCHASE',if(g.grn_type=2,'JOB RECEIPT','CHARGES')) as grn_type,IFNULL(g.grn_pay_tds_percent,0) as grn_pay_tds_percent,IFNULL(g.grn_pay_tds_value,0) as grn_pay_tds_value,IFNULL(p.tds_tax_value,0) as tds_tax_value,IFNULL(p.tds_percent,0) as tds_percent,IFNULL(p.tcs_tax_value,0) as tcs_tax_value,IFNULL(p.tcs_percent,0) as tcs_percent,
        p.is_po_halmarked, g.purchase_type as grn_purchase_type,
        e.firstname as emp_name,if(g.grn_type=1,'GST PURCHASE',if(g.grn_type=2,'JOB RECEIPT',if(g.grn_type=4,'JB','CHARGES'))) as grn_type,IFNULL(g.grn_pay_tds_percent,0) as grn_pay_tds_percent,IFNULL(g.grn_pay_tds_value,0) as grn_pay_tds_value,IFNULL(p.tds_tax_value,0) as tds_tax_value,IFNULL(p.tds_percent,0) as tds_percent,IFNULL(p.tcs_tax_value,0) as tcs_tax_value,IFNULL(p.tcs_percent,0) as tcs_percent,
        p.is_po_halmarked, g.purchase_type
        FROM ret_purchase_order p
        LEFT JOIN ret_grn_entry g on g.grn_id = p.po_grn_id
        LEFT JOIN ret_karigar k on k.id_karigar = p.po_karigar_id
        LEFT JOIN country cy ON cy.id_country = k.id_country
        LEFT JOIN state st ON st.id_state = k.id_state
        LEFT JOIN city ct ON ct.id_city = k.id_city
        LEFT JOIN employee e ON e.id_employee = p.created_by
        WHERE p.po_id=".$po_id."");
        $result = $sql->result_array();
        foreach ($result as $r)
        {
            $supplier_bill_entry_calc_type = $this->get_ret_settings('supplier_bill_entry_calc');
            $r['supplier_bill_entry_calc'] = $supplier_bill_entry_calc_type;
            $return_data=$r;
        }
        return $return_data;
	}
	function getPurchaseOrderItemDet($po_id)
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT p.po_item_id,p.po_item_cat_id,p.po_item_pro_id,p.po_item_des_id,p.po_item_sub_des_id,
        cat.name as category_name,pro.product_name,des.design_name,subDes.sub_design_name,
        p.po_purchase_mode,p.no_of_pcs,p.gross_wt,p.net_wt,p.cal_type,p.less_wt,p.item_pure_wt,
        p.purchase_touch,p.item_wastage,p.mc_type,p.mc_value,IFNULL(stn.stn_amt,0) as stn_amt,
        IFNULL(other_mt.oth_metal_amt,0) as oth_metal_amt,p.item_cost,p.fix_rate_per_grm,p.is_suspense_stock,p.is_halmarked,p.is_rate_fixed,
        p.po_order_no,p.id_purity,pur.purity as purity_name,ifnull(dia_stn.stn_wt,0) as dia_wt,pro.purchase_mode,p.total_tax,p.calculation_based_on,p.pure_wt_calc_type,IFNULL(p.purity_type,999) as purity_type,IFNULL(p.remark,'') as remark,IFNULL(p.total_cgst,0) as total_cgst,IFNULL(p.total_sgst,0) as total_sgst,
        IFNULL(p.total_igst,0) as total_igst,IFNULL(p.tax_percentage,0) as tax_percentage,p.rate_calc_type
        FROM ret_purchase_order_items p
        LEFT JOIN ret_purchase_order ord ON ord.po_id=p.po_item_po_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.po_item_cat_id
        LEFT JOIN ret_purity pur ON pur.id_purity=p.id_purity
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no=p.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=p.po_item_sub_des_id
        LEFT JOIN(SELECT s.po_item_id,SUM(s.po_stone_amount) as stn_amt
                 FROM ret_po_stone_items s
                 GROUP by s.po_item_id) as stn ON stn.po_item_id=p.po_item_id
        LEFT JOIN(SELECT i.po_item_id,SUM(i.po_other_item_amount) as oth_metal_amt
                 FROM ret_po_other_item i
                 GROUP by i.po_item_id) as other_mt ON other_mt.po_item_id=p.po_item_id
        LEFT JOIN(SELECT po_stn.po_item_id,SUM(po_stn.po_stone_wt) as stn_wt
            FROM ret_po_stone_items po_stn
            LEFT JOIN ret_stone s ON s.stone_id=po_stn.po_stone_id
            LEFT JOIN ret_uom uom ON uom.uom_id=s.uom_id
            WHERE  uom.uom_short_code='CT'
            GROUP by po_stn.po_item_id) as dia_stn ON dia_stn.po_item_id=p.po_item_id
        WHERE p.po_item_po_id=".$po_id." and p.status=0 ");
        $result= $sql->result_array();
        foreach($result as $items)
        {
            $items['stn_details']=$this->getPurchaseStoneDetails($items['po_item_id']);
            $items['other_metal_details']=$this->getPurchaseOtherMetalDetails($items['po_item_id']);
            $items['other_charge_details']=$this->getPurchaseOtherChargeDetails($items['po_item_id']);
            $returnData[]=$items;
        }
        return $returnData;
	}
	function getPurchaseOtherChargeDetails($po_item_id)
	{
	    $sql = $this->db->query("SELECT c.pur_othr_charge_value,c.calc_type,ch.name_charge,c.total_charge_value
        FROM ret_purchase_other_charges c
        LEFT JOIN ret_charges ch ON ch.id_charge = c.pur_othr_charge_id
        WHERE c.pur_po_item_id = ".$po_item_id." ");
        return $sql->result_array();
	}
	function get_purchase_order_item_det($po_id)
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT p.po_item_id,p.po_item_cat_id,p.po_item_pro_id,p.po_item_des_id,p.po_item_sub_des_id,
        cat.name as category_name,pro.product_name,des.design_name,subDes.sub_design_name,cat.hsn_code,
        p.po_purchase_mode,p.no_of_pcs,p.gross_wt,p.net_wt,p.cal_type,p.less_wt,p.item_pure_wt,
        p.purchase_touch,p.item_wastage,p.mc_type,p.mc_value,IFNULL(stn.stn_amt,0) as stn_amt,
        IFNULL(other_mt.oth_metal_amt,0) as oth_metal_amt,p.item_cost,p.fix_rate_per_grm,p.is_suspense_stock,p.is_halmarked,p.is_rate_fixed,
        p.po_order_no,p.id_purity,pur.purity as purity_name,ifnull(dia_stn.stn_wt,0) as dia_wt,pro.purchase_mode,p.total_cgst,p.total_sgst,p.total_igst,p.tax_percentage,
        IFNULL(p.total_tax,0) as total_tax,IFNULL(p.remark,'') as remark,IFNULL(p.quality_id,'') as quality_id,IFNULL(qc.code,'') as quality_code,if(p.rate_calc_type=1,'Wt','PCS') as rate_calc_type,pro.stone_type,ifnull(u.uom_short_code,'') as uom,
        p.calculation_based_on,IFNULL(p.purity_type,999) as purity_type
        FROM ret_purchase_order_items p
        LEFT JOIN ret_purchase_order ord ON ord.po_id=p.po_item_po_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.po_item_cat_id
        LEFT JOIN ret_purity pur ON pur.id_purity=p.id_purity
        LEFT JOIN ret_product_master pro ON pro.pro_id=p.po_item_pro_id
        LEFT JOIN ret_design_master des ON des.design_no=p.po_item_des_id
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design=p.po_item_sub_des_id
        LEFT JOIN ret_quality_code qc on qc.quality_id = p.quality_id
        LEFT JOIN ret_uom u on u.uom_id = p.uom
        LEFT JOIN(SELECT s.po_item_id,SUM(s.po_stone_amount) as stn_amt
                 FROM ret_po_stone_items s
                 GROUP by s.po_item_id) as stn ON stn.po_item_id=p.po_item_id
        LEFT JOIN(SELECT i.po_item_id,SUM(i.po_other_item_amount) as oth_metal_amt
                 FROM ret_po_other_item i
                 GROUP by i.po_item_id) as other_mt ON other_mt.po_item_id=p.po_item_id
        LEFT JOIN(SELECT po_stn.po_item_id,SUM(po_stn.po_stone_wt) as stn_wt
            FROM ret_po_stone_items po_stn
            LEFT JOIN ret_stone s ON s.stone_id=po_stn.po_stone_id
            LEFT JOIN ret_uom uom ON uom.uom_id=s.uom_id
            WHERE  uom.uom_short_code='CT'
            GROUP by po_stn.po_item_id) as dia_stn ON dia_stn.po_item_id=p.po_item_id
        WHERE p.po_item_po_id=".$po_id."");
        $result= $sql->result_array();
        foreach($result as $items)
        {
            $items['stn_details']=$this->getPurchaseStoneDetails($items['po_item_id']);
            $items['other_metal_details']=$this->getPurchaseOtherMetalDetails($items['po_item_id']);
            $items['other_charge_details']=$this->getPurchaseOtherChargeDetails($items['po_item_id']);
            $returnData[]=$items;
        }
        return $returnData;
	}
	function getPurchaseStoneDetails($po_item_id)
	{
	    $sql=$this->db->query("SELECT po_stn.po_st_id,po_stn.po_stone_id,po_stn.is_apply_in_lwt,po_stn.po_stone_pcs,po_stn.po_stone_wt,
        po_stn.po_stone_uom,po_stn.po_stone_calc_based_on,po_stn.po_stone_rate,po_stn.po_stone_amount,s.stone_type,s.stone_name,uom.uom_short_code,
         po_stn.po_quality_id
        FROM ret_po_stone_items po_stn
        LEFT JOIN ret_stone s ON s.stone_id = po_stn.po_stone_id
        LEFT JOIN ret_uom uom on uom.uom_id=po_stn.po_stone_uom
        WHERE po_stn.po_item_id=".$po_item_id."");
        return $sql->result_array();
	}
	function getPurchaseOtherMetalDetails($po_item_id)
	{
	    $sql=$this->db->query("SELECT po_othr.po_other_item_id,ifnull(po_othr.po_other_item_gross_weight,0) as othr_metal_wt,
        IFNULL(po_othr.po_other_item_pcs,0) as othr_metal_pcs,ifnull(po_othr.po_other_item_rate,0) as othr_metal_rate,
        ifnull(po_othr.po_other_item_amount,0) as othr_metal_amt,ifnull(cat.name,'') as cat_name
        FROM ret_po_other_item po_othr 
        LEFT JOIN ret_category cat on cat.id_ret_category = po_othr.po_item_metal
        WHERE po_item_id=".$po_item_id."");
	    return $sql->result_array();
	}
	function get_empty_record()
	{
	    $ho = $this->get_headOffice();
	    $supplier_bill_entry_calc_type = $this->get_ret_settings('supplier_bill_entry_calc');
	    $return_data=array(
	           'purchase_type'      =>2,
	           'po_type'            =>1,
	           'po_date'            =>NULL,
	           'po_karigar_id'      =>NULL,
	           'po_ref_no'          =>NULL,
	           'is_suspense_stock'  =>0,
               'is_po_halmarked'    =>1,
	           'po_supplier_ref_no' =>NULL,
	           'po_ref_date'        =>NULL,
	           'ewaybillno'         =>NULL,
	           'po_irnno'           =>NULL,
	           'despatch_through'   =>2,
	           'id_category'        =>'',
	           'id_purity'          =>'',
	           'po_ref_date'        =>NULL,
	           'id_branch'          =>$ho['id_branch'],
	           'po_grn_id'          => NULL,
	           'supplier_bill_entry_calc' => $supplier_bill_entry_calc_type,
	          );
	    return $return_data;
	}
	function generate_grn_refno($grn_type)
	{
	    // Get active financial year code
	    $fin_year = $this->get_FinancialYear();
	    $fin_year_code = $fin_year['fin_year_code'];

	    // PUR-INT01 fix: Use MAX to get the true highest number, scoped to current FY.
	    $sql = "SELECT MAX(CAST(SUBSTRING_INDEX(grn_ref_no, '-', -1) AS UNSIGNED)) as max_num
	            FROM ret_grn_entry WHERE grn_type = ? AND grn_fin_year_code = ? FOR UPDATE";
	    $result = $this->db->query($sql, array($grn_type, $fin_year_code));
	    $max_num = (int) $result->row()->max_num;
	    $grn_ref_no = str_pad($max_num + 1, 5, '0', STR_PAD_LEFT);

		if((int)$grn_type == 1){
		    return "PU-".$grn_ref_no;
		}else if((int)$grn_type == 2){
		    return "PM-".$grn_ref_no;
		}else if((int)$grn_type == 3){
		    return "PC-".$grn_ref_no;
		}else if((int)$grn_type == 4){
		    return "PJ-".$grn_ref_no;
		}
	}
	function ajax_getGrnentrydetails($from_date,$to_date)
	{
	    $sql = $this->db->query("SELECT grn.grn_id,grn.grn_karigar_id,date_format(grn.grn_date,'%d-%m-%Y') as grn_date,grn.purchase_type,
        k.firstname as karigar_name,grn.grn_ref_no,grn.grn_irnno,grn.grn_ewaybillno,k.contactno1 as mobile,
        grn.grn_purchase_amt,if(grn.grn_bill_status=1,'Success','Cancelled') as billstatus,grn.grn_bill_status,
        IFNULL(grn.grn_supplier_ref_no,'') as grn_supplier_ref_no,IFNULL(date_format(grn.grn_ref_date,'%d-%m-%Y'),'-') as grn_ref_date,
        IFNULL(po.is_approved,0) as is_approved
        FROM ret_grn_entry grn
        LEFT JOIN ret_karigar k ON k.id_karigar = grn.grn_karigar_id
        LEFT JOIN ret_purchase_order po on po.po_grn_id = grn.grn_id
        WHERE (date(grn.grn_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')");
        $result =  $sql->result_array();
        foreach($result as $items)
            {
                $items['image_details']=$this->get_grn_images($items['grn_id']);
                $returnData[]=$items;
            }
        return $returnData;
	}
    function get_grn_images($grn_id)
    {
        $sql = $this->db->query("SELECT * FROM `ret_grn_images` where grn_id=".$grn_id."");
        return $sql->result_array();
    }
	function get_grn_details($id)
	{
	    $retrnData = array();
	    $sql = $this->db->query("SELECT grn.grn_id,grn.grn_karigar_id,date_format(grn.grn_date,'%d-%m-%Y') as grn_date, grn.grn_type,
        k.firstname as karigar_name,grn.grn_ref_no,grn.grn_irnno,grn.grn_ewaybillno,k.contactno1 as mobile,
        grn.grn_purchase_amt,if(grn.grn_bill_status=1,'Success','Cancelled') as billstatus,grn.grn_bill_status,
        cy.name as supplier_country_name,st.name as supplier_state_name,ct.name as supplier_city_name,
        IFNULL(k.address1,'') as supplier_address1,IFNULL(k.address2,'') as supplier_address2,
        IFNULL(k.address3,'') as supplier_address3,IFNULL(k.pincode,'') as supplier_pincode,k.gst_number,k.email,k.contactno1 as supplier_mobile,
        IFNULL(grn.grn_tcs_percent,0) as grn_tcs_percent,IFNULL(grn.grn_tcs_value,0) as grn_tcs_value,IFNULL(grn.grn_pay_tds_percent,0) as grn_pay_tds_percent,
        IFNULL(grn.grn_pay_tds_value,0) as grn_pay_tds_value,IFNULL(grn.grn_round_off,0) as grn_round_off,IFNULL(grn.grn_supplier_ref_no,'') as grn_supplier_ref_no,
        IFNULL(date_format(grn.grn_ref_date,'%Y-%m-%d'),'') as grn_ref_date,IFNULL(grn.grn_other_charges_tds_percent,0) as grn_other_charges_tds_percent,
        IFNULL(grn.grn_other_charges_tds_value,0) as grn_other_charges_tds_value,grn.grn_despatch_through,IFNULL(k.pan_no,'') as pan_no,grn.grn_discount, grn.purchase_type
        FROM ret_grn_entry grn
        LEFT JOIN ret_karigar k ON k.id_karigar = grn.grn_karigar_id
        LEFT JOIN country cy ON cy.id_country = k.id_country
        LEFT JOIN state st ON st.id_state = k.id_state
        LEFT JOIN city ct ON ct.id_city = k.id_city
        where grn.grn_id =".$id."");
        $retrnData = $sql->row_array();
        $item_gst_details = $this->get_grn_gst_details($retrnData['grn_id']);
        $charges_gst_details = $this->get_grn_charge_gst_details($retrnData['grn_id']);
        $retrnData['item_details'] = $this->get_grn_item_details($retrnData['grn_id']);
        $retrnData['charge_details'] = $this->get_grn_charge_details($retrnData['grn_id']);
        $retrnData['gst_details'] = $item_gst_details;
        $retrnData['charge_gst_details'] = $charges_gst_details;
        return $retrnData;
	}
	function get_grn_item_details($grn_id)
	{
	    $returnData = [];
	    $sql = $this->db->query("SELECT i.grn_item_id,i.grn_gross_wt,i.grn_no_of_pcs,i.grn_rate_per_grm,i.grn_net_wt,
	    i.grn_item_cost,i.grn_item_gst_rate,i.grn_item_cgst,i.grn_item_sgst,i.grn_item_igst,
        c.name as category_name,i.grn_item_grn_id,c.hsn_code,i.grn_item_gst_value, ifnull(i.grn_wastage , 0) as grn_wastage ,i.itemratecaltype,
        c.id_ret_category as cat_id,(i.grn_item_cost - i.grn_item_gst_rate) as grn_amount
        FROM ret_grn_items i
        LEFT JOIN ret_category c ON c.id_ret_category = i.grn_item_cat_id
        where i.grn_item_grn_id=".$grn_id."");
        $result = $sql->result_array();
        foreach($result as $items)
        {
            $items['stone_details'] = $this->get_grn_stone_details($items['grn_item_id']);
            $items['othermetal_details'] = $this->getGRNOthermetalDetails($items['grn_item_id']);
            $returnData[]=$items;
        }
        return $returnData;
	}
	function get_grn_stone_details($grn_item_id)
    {
    	    $sql = $this->db->query("SELECT st.is_apply_in_lwt,s.stone_type,st.stone_id,st.pieces,st.pieces,st.wt,IFNULL(st.uom_id,uom.uom_id) as uom_id,
            uom.uom_name,s.stone_name,st.amount, st.rate_per_gram,st.stone_cal_type
            FROM ret_grn_item_stone st
            LEFT JOIN ret_stone s ON s.stone_id = st.stone_id
            LEFT JOIN ret_uom uom on uom.uom_id=s.uom_id
            WHERE st.grn_item_id = ".$grn_item_id."");
            return $sql->result_array();
    }
	function get_grn_gst_details($grn_id)
    {
        $sql = $this->db->query("SELECT IFNULL(SUM(i.grn_item_cgst),0) as cgst_cost,IFNULL(SUM(i.grn_item_sgst),0) as sgst_cost,
        IFNULL(SUM(i.grn_item_igst),0) as igst_cost,i.grn_item_gst_value
        FROM ret_grn_items i
        where i.grn_item_grn_id=".$grn_id."
        GROUP BY i.grn_item_gst_value");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
	function get_grn_charge_details($grn_id)
	{
	    $sql = $this->db->query("SELECT ch.name_charge,c.grn_charge_value,c.char_with_tax,c.char_tax,c.cgst_cost,c.sgst_cost,c.igst_cost,
	    c.grn_charge_id
        FROM ret_grn_other_charges c
        LEFT JOIN ret_charges ch ON ch.id_charge = c.grn_charge_id
        WHERE c.grn_id =".$grn_id."");
        return $sql->result_array();
	}
	function get_grn_charge_gst_details($grn_id)
	{
	    $sql = $this->db->query("SELECT IFNULL((c.char_tax),0) as grn_item_gst_value,
	    IFNULL(SUM(c.cgst_cost),0) as cgst_cost,IFNULL(SUM(c.sgst_cost),0) as sgst_cost,IFNULL(SUM(c.igst_cost),0) as igst_cost,c.grn_charge_id
        FROM ret_grn_other_charges c
        LEFT JOIN ret_charges ch ON ch.id_charge = c.grn_charge_id
        WHERE c.grn_id =".$grn_id."
        GROUP BY c.char_tax");
        return $sql->result_array();
	}
	function getActiveGRNsDetails($data)
    {
        $grnentries = array();
        if($data['po_id']!='')
        {
            $sql = $this->db->query("SELECT grn.grn_id, grn_ref_no, grn_type, grn_karigar_id, grn_supplier_ref_no, grn_ref_date, grn_ewaybillno,
            grn_despatch_through, grn_irnno, grn_other_charges, grn_discount, grn_purchase_wt, grn_purchase_amt,
            grn_pay_tds_percent, grn_pay_tds_value, grn_other_charges_tds_percent, grn_other_charges_tds_value,
            grn_tcs_percent, grn_tcs_value, grn_round_off,IFNULL(ch.charges_amount,0) as charges_amount,IFNULL(ch.charges_tax,0) as charges_tax, grn.purchase_type
            FROM `ret_grn_entry` as grn
            LEFT JOIN (SELECT c.grn_id,IFNULL(SUM(c.grn_charge_value),0) as charges_amount,
                        IFNULL(SUM(c.total_tax),0) as charges_tax
                        FROM ret_grn_other_charges c
                        GROUP BY c.grn_id) as ch ON ch.grn_id = grn.grn_id
            WHERE grn_bill_status = 1 AND grn.grn_type != 3 ");
            //print_r($this->db->last_query());exit;
        }
        else
        {
            $sql = $this->db->query("SELECT grn.grn_id, grn_ref_no, grn_type, grn_karigar_id, grn_supplier_ref_no, grn_ref_date, grn_ewaybillno,
            grn_despatch_through, grn_irnno, grn_other_charges, grn_discount, grn_purchase_wt, grn_purchase_amt,
            grn_pay_tds_percent, grn_pay_tds_value, grn_other_charges_tds_percent, grn_other_charges_tds_value,
            grn_tcs_percent, grn_tcs_value, grn_round_off,IFNULL(ch.charges_amount,0) as charges_amount,IFNULL(ch.charges_tax,0) as charges_tax, grn.purchase_type
            FROM `ret_grn_entry` as grn
            LEFT JOIN (SELECT c.grn_id,IFNULL(SUM(c.grn_charge_value),0) as charges_amount,
                        IFNULL(SUM(c.total_tax),0) as charges_tax
                        FROM ret_grn_other_charges c
                        GROUP BY c.grn_id) as ch ON ch.grn_id = grn.grn_id
            WHERE grn_bill_status = 1 AND grn.grn_type != 3 AND grn.grn_id NOT IN (SELECT ifnull(po_grn_id, '') FROM ret_purchase_order)");
        }
        return $sql->result_array();
    }
	function getGRNsCatDetailsbyGRNId($postdata)
	{
	    $responsedata = array();
	    $grncatdetails = $this->db->query("SELECT grn.grn_id , grnitm.grn_item_id, grnitm.grn_item_grn_id, grnitm.grn_item_is_order, grnitm.grn_item_cat_id, grnitm.grn_gross_wt, grnitm.grn_less_wt, grnitm.grn_net_wt,
	                                        grnitm.grn_no_of_pcs, grnitm.grn_rate_per_grm, grnitm.itemratecaltype, grnitm.grn_item_cost, grnitm.grn_item_gst_rate, grnitm.grn_item_gst_value,
	                                        cat.name as catname, cat.cat_type,IFNULL(dia.dia_wt,0) as dia_wt,grn.grn_pay_tds_percent, grn.purchase_type
	                                        FROM ret_grn_entry as grn
	                                        LEFT JOIN `ret_grn_items` as grnitm ON grnitm.grn_item_grn_id =  grn.grn_id
	                                        LEFT JOIN (SELECT IFNULL(SUM(s.wt / COALESCE(u.divided_by_value, 1)),0) as dia_wt, s.grn_item_id
                                                       FROM ret_grn_item_stone s
                                                       LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
                                                       LEFT JOIN ret_uom u ON u.uom_id = COALESCE(s.uom_id, st.uom_id)
                                                       WHERE (s.uom_id = 6 OR st.uom_id = 6 OR st.stone_type = 1)
                                                       GROUP BY s.grn_item_id) as dia ON dia.grn_item_id = grnitm.grn_item_id
	                                        LEFT JOIN ret_category as cat ON cat.id_ret_category = grnitm.grn_item_cat_id
	                                        WHERE grn.grn_id = '".$postdata['grnId']."'
                                             ".($postdata['cat_type']!='' ? "and cat.cat_type=".$postdata['cat_type']."":"")."
                                            GROUP BY grnitm.grn_item_id");
	    foreach($grncatdetails->result() as $row){
	        $responsedata[] = array(
	                            "grn_item_id"   => $row->grn_item_id,
	                            "grn_id"        => $row->grn_item_grn_id,
	                            "is_order"      => $row->grn_item_is_order,
	                            "cat_id"        => $row->grn_item_cat_id,
	                            "gross_wt"      => $row->grn_gross_wt,
	                            "grn_less_wt"   => $row->grn_less_wt,
	                            "grn_net_wt"    => $row->grn_net_wt,
	                            "no_of_pcs"     => $row->grn_no_of_pcs,
	                            "cat_type"      => $row->cat_type,
	                            "rate_per_grm"  => $row->grn_rate_per_grm,
	                            "cal_type"      => $row->itemratecaltype,
	                            "item_cost"     => $row->grn_item_cost,
	                            "gst_rate"      => $row->grn_item_gst_rate,
	                            "item_value"    => $row->grn_item_gst_value,
                                "grn_tds_percent"    => $row->grn_pay_tds_percent,
	                            "catname"       => $row->catname,
	                            "dia_wt"        => $row->dia_wt,
	                            "stone_detail"  => $this->getGRNCategoryStonedetails($row->grn_item_id),
	                            "om_detail"     => $this->getGRNOthermetalDetails($row->grn_item_id),
	                            "charge_detail" => $this->getGRNOtherchargeDetails($row->grn_id),
                                "purchase_type" => $row->purchase_type
	                       );
	    }
	    return $responsedata;
	}
	function getGRNCategoryStonedetails($grncatId)
	{
	    $stonequery = $this->db->query("SELECT grnst.grn_item_id, grnst.stone_id, grnst.pieces, grnst.wt, st.uom_id, grnst.rate_per_gram, grnst.amount,
	                                    grnst.is_apply_in_lwt, grnst.stone_cal_type,
	                                    st.stone_type, st.stone_name
	                                    FROM `ret_grn_item_stone` as grnst
	                                    LEFT JOIN ret_stone as st ON st.stone_id = grnst.stone_id
	                                    WHERE grnst.grn_item_id = '".$grncatId."'");
	   return $stonequery->result_array();
	}
	function getGRNOthermetalDetails($grncatId)
	{
	    $othermetalquery = $this->db->query("SELECT grnom.grn_other_itm_id,  grnom.grn_itms_id, grnom.grn_other_itm_metal_id, grnom.grn_other_itm_pur_id,
	                                        grnom.grn_other_itm_grs_weight, grnom.grn_other_itm_wastage, grnom.grn_other_itm_mc, grnom.grn_other_itm_uom,
	                                        grnom.grn_other_itm_cal_type, grnom.grn_other_itm_pcs, grnom.grn_other_itm_rate, grnom.grn_other_itm_amount,
	                                        cat.name as omcatname, cat.id_ret_category as omcatid
	                                        FROM `ret_grn_other_metals` as grnom
	                                        LEFT JOIN ret_category as cat ON cat.id_ret_category = grnom.grn_other_itm_metal_id
	                                        WHERE grnom.grn_itms_id = '".$grncatId."'");
	    return $othermetalquery->result_array();
	}
	function getGRNOtherchargeDetails($grnId)
	{
	    $otherchargequery = $this->db->query("SELECT grncrg.grn_other_charge_id, grncrg.grn_charge_id, grncrg.grn_id,  grncrg.grn_charge_value,
	                                        grncrg.char_with_tax, grncrg.char_tax,
	                                        grncrg.total_tax, chr.code_charge, chr.name_charge
	                                        FROM `ret_grn_other_charges` as grncrg
	                                        LEFT JOIN ret_charges as chr ON chr.id_charge = grncrg.grn_charge_id
	                                        WHERE grncrg.grn_id = '".$grnId."'");
	    return $otherchargequery->result_array();
	}
	function get_approvl_ratefix_po($data)
	{
	    $sql = $this->db->query("SELECT c.id_supplier_rate_cut,c.ref_no,c.weight as pure_wieght,IFNULL(rtfix.rate_fix_wt,0) as rate_fix_wt,
	    IFNULL(c.weight-IFNULL(rtfix.rate_fix_wt,0),0) as blc_wt,c.po_id,p.po_ref_no,date_format(c.date_add,'%d%m-%Y') as dateadd
        FROM ret_supplier_rate_cut c
        LEFT JOIN ret_purchase_order p ON p.po_id = c.po_id
        LEFT JOIN (SELECT IFNULL(SUM(r.rate_fix_wt),0) as rate_fix_wt,r.id_approval_ratecut
                  FROM ret_po_rate_fix r
                  GROUP BY r.id_approval_ratecut) as rtfix ON rtfix.id_approval_ratecut = c.id_supplier_rate_cut
        WHERE c.ref_no IS NOT NULL and c.conversion_type = 2
        ".($data['id_karigar']!='' ? " and c.id_karigar=".$data['id_karigar']."" :'')."
        having blc_wt > 0");
        return $sql->result_array();
	}
	function get_rate_fixing_po_no($data)
	{
            $sql = $this->db->query("SELECT p.po_ref_no,p.po_id,p.po_karigar_id,p.tot_purchase_wt,
            IFNULL(ratefix.fixed_wt,0) as total_fixed_wt,
            date_format(p.po_date,'%d-%m-%Y') as podate,IFNULL(po_ret.pur_ret_pur_wt,0) as ret_pure_wt,p.fin_year_code
            FROM ret_purchase_order p
            LEFT JOIN(SELECT IFNULL(SUM(f.rate_fix_wt),0) as fixed_wt,f.rate_fix_po_item_id
            FROM ret_po_rate_fix f
            GROUP BY f.rate_fix_po_item_id) as ratefix ON ratefix.rate_fix_po_item_id = p.po_id
            LEFT JOIN(SELECT IFNULL(SUM(r.pur_ret_pur_wt),0) as pur_ret_pur_wt,p.po_id
                FROM ret_purchase_return_items r
                LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id
                LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = r.pur_ret_po_item_id
                LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
                WHERE ret.bill_status = 1
            GROUP BY p.po_id) as po_ret ON po_ret.po_id = p.po_id
            WHERE p.isratefixed = 0 AND p.tot_purchase_wt > 0 and p.is_suspense_stock = 0
            ".($data['id_karigar']!='' ? " AND p.po_karigar_id=".$data['id_karigar']."" :'')."
            HAVING tot_purchase_wt > total_fixed_wt ");
            $result1 = $sql->result_array();
            /*$approvalfix = $this->db->query("SELECT p.po_ref_no,p.po_id,date_format(p.po_date,'%d-%m-%Y') as podate,p.po_karigar_id,IFNULL(unfix.unfixweight,0) as tot_purchase_wt,
            IFNULL(ratefix.fixed_wt,0) as total_fixed_wt,IFNULL(po_ret.pur_ret_pur_wt,0) as ret_pure_wt
            FROM ret_purchase_order p
            LEFT JOIN(SELECT IFNULL(SUM(r.weight),0) as unfixweight,r.po_id
                     FROM ret_supplier_rate_cut r
                     WHERE r.status = 1 AND r.conversion_type = 2
                     GROUP BY r.po_id) as unfix ON unfix.po_id = p.po_id
            LEFT JOIN(SELECT IFNULL(SUM(f.rate_fix_wt),0) as fixed_wt,f.rate_fix_po_item_id
                    FROM ret_po_rate_fix f
                    GROUP BY f.rate_fix_po_item_id) as ratefix ON ratefix.rate_fix_po_item_id = p.po_id
            LEFT JOIN(SELECT IFNULL(SUM(r.pur_ret_pur_wt),0) as pur_ret_pur_wt,p.po_id
                FROM ret_purchase_return_items r
                LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id
                LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = r.pur_ret_po_item_id
                LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
                WHERE ret.bill_status = 1
            GROUP BY p.po_id) as po_ret ON po_ret.po_id = p.po_id
            WHERE p.is_approved = 1 AND p.is_suspense_stock = 1 AND p.isratefixed = 0
            ".($data['id_karigar']!='' ? " AND p.po_karigar_id=".$data['id_karigar']."" :'')."
            HAVING tot_purchase_wt > total_fixed_wt");*/
            //$result2 = $approvalfix->result_array();
        return $result1;
	}
	function get_purchase_order_status($data)
	{
	    $sql = $this->db->query("SELECT c.id_customerorder,c.pur_no,d.id_product,d.design_no,d.id_sub_design,IFNULL(SUM(d.totalitems),0) as orderpcs,
        p.product_name,des.design_name,subDes.sub_design_name,IFNULL(rcd.recd_pcs,0) as received_pcs,IFNULL(rcd.recd_gross_wt,0) as received_weight,IFNULL(rcd.po_ref_no,'') as po_ref_no,IFNULL(cusOrd.order_no,'') as cus_ord_ref,k.firstname as karigar_name,
        date_format(c.order_date,'%d-%m-%Y') as orderdate,rcd.po_item_po_id,cusOrd.id_customerorder as cusOrdid, date_format(d.smith_due_date,'%d-%m-%Y') as due_date,
        IF(el.status = 4, 'Rejected', m.order_status) as order_status,
        IF(el.status = 4, 'red', m.color) as color,
        el.status as email_status
        FROM customerorderdetails d
        LEFT JOIN customerorder c ON c.id_customerorder = d.id_customerorder
        LEFT JOIN ret_product_master p ON p.pro_id = d.id_product
        LEFT JOIN ret_design_master des ON des.design_no = d.design_no
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = d.id_sub_design
        LEFT JOIN customerorder cusOrd ON cusOrd.id_customerorder = c.cus_ord_ref
        LEFT JOIN ret_karigar k ON k.id_karigar = c.id_karigar
        LEFT JOIN order_status_message m ON m.id_order_msg=c.order_status
        LEFT JOIN ret_order_email_logs el ON el.id_customerorder = c.id_customerorder
        LEFT JOIN(SELECT i.po_item_po_id,GROUP_CONCAT(po.po_ref_no) as po_ref_no,i.po_order_no,IFNULL(SUM(i.no_of_pcs),0) as recd_pcs,IFNULL(SUM(i.gross_wt),0) as recd_gross_wt,i.po_item_pro_id,i.po_item_des_id,i.po_item_sub_des_id
        FROM ret_purchase_order_items i
        LEFT JOIN ret_purchase_order po ON po.po_id = i.po_item_po_id
        GROUP BY i.po_item_pro_id,i.po_item_des_id,i.po_item_sub_des_id,i.po_order_no) as rcd ON rcd.po_order_no = d.id_customerorder AND rcd.po_item_pro_id = d.id_product AND rcd.po_item_des_id = d.design_no AND rcd.po_item_sub_des_id = d.id_sub_design
        WHERE c.order_type = 1 AND c.pur_no IS NOT NULL AND d.id_product IS NOT NULL AND d.design_no IS NOT NULL and d.id_sub_design IS NOT NULL
        ".($data['id_karigar']!='' ? " AND c.id_karigar=".$data['id_karigar']."" :'')."
        ".($data['id_product']!='' ? " and d.id_product=".$data['id_product']."" :'')."
        ".($data['id_design']!='' ? " and d.design_no=".$data['id_design']."" :'')."
        ".($data['id_sub_design']!='' ? " and d.id_sub_design=".$data['id_sub_design']."" :'')."
        ".($data['from_date']!='' && $data['to_date']!='' ? " and (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."
         ".($data['order_for']!='' ? "and c.order_for=".$data['order_for']."":'')."
        GROUP BY d.id_customerorder,d.id_product,d.design_no,d.id_sub_design");
        return $sql->result_array();
	}
	function get_po_payment($id)
    {
        $po_data=array();
        $sql= $this->db->query("SELECT p.pay_id,p.pay_sup_id,
        p.pay_refno,p.pay_amt,p.pay_narration,p.pay_status,p.bill_type,
        kar.firstname as karigar,IFNULL(SUM(pl.debit),0)-IFNULL(SUM(pl.credit),0) as balance_amount
        FROM ret_po_payment p
        LEFT JOIN ret_karigar kar on kar.id_karigar=p.pay_sup_id
        LEFT JOIN ret_view_grn_pay_ledger pl on pl.sup_id=p.pay_sup_id
        WHERE p.pay_id=".$id."");
        //print_r($this->db->last_query());exit;
        $po_pay = $sql->result_array();
        foreach($po_pay as $val)
        {
            $po_data[]=array(
                'pay_id' => $val['pay_id'],
                'pay_sup_id' => $val['pay_sup_id'],
                'bill_type' => $val['bill_type'],
                'pay_amt' => $val['pay_amt'],
                'pay_refno' => $val['pay_refno'],
                'pay_narration'  => $val['pay_narration'],
                'kargiar_name'   => $val['karigar'],
                'balance_amount'  => $val['balance_amount'],
                'po_payment_details' => $this->get_po_paymentDetails($val['pay_id']),
            );
        }
        return $po_data;
    }
   function get_po_paymentDetails($pay_id)
    {
        $sql=$this->db->query("SELECT pd.id_pay_details,pd.pay_id,pd.type,
        pd.pay_mode,pd.payment_amount,
        pd.ref_no,pd.id_bank,date_format(pd.ref_date,'%Y-%m-%d') as payment_date
        FROM ret_po_payment_detail pd
        Where pd.pay_id=".$pay_id."");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_approval_stock_tags($data)
    {
        $sql = $this->db->query("SELECT r.po_id,r.po_ref_no,r.po_karigar_id,date_format(r.po_date,'%d-%m-%Y') as podate,
        t.tag_id,t.tag_code,t.piece,t.gross_wt,t.net_wt,IFNULL(cusor.pur_no,'') as order_no,IFNULL(cusor.order_status,'') as order_status,m.order_status as statusmessage,br.name as current_branchname,sup.firstname as suppliername,
        t.tag_status,t.is_approval_stock_converted,IFNULL(date_format(t.app_stk_converted_date,'%d-%m-%Y'),'') as converted_date
        FROM ret_purchase_order r
        LEFT JOIN ret_lot_inwards l ON l.po_id = r.po_id
        LEFT JOIN ret_taging t ON t.tag_lot_id = l.lot_no
        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        LEFT JOIN customerorderdetails as ordet ON ordet.approval_tagid = t.tag_id
        LEFT JOIN customerorder as cusor ON cusor.id_customerorder = ordet.id_customerorder
        LEFT JOIN order_status_message m ON m.id_order_msg=cusor.order_status
        LEFT JOIN ret_karigar as sup ON sup.id_karigar = r.po_karigar_id
        LEFT JOIN branch as br ON br.id_branch = t.current_branch
        WHERE r.is_suspense_stock =1 AND t.tag_type = 1 AND t.tag_status = 0
        ".($data['order_status']==0 ? " AND (cusor.order_status = ".$data['order_status']." OR cusor.order_status IS NULL)" :" AND cusor.order_status = ".$data['order_status']."")."
        ".($data['bill_no']!='' ? " and r.po_ref_no='".$data['bill_no']."'" :'')."
        ");
        return $sql->result_array();
    }
    function get_approval_tag_details($tag_id)
    {
        $sql = $this->db->query("SELECT * FROM ret_taging WHERE tag_id = '".$tag_id."' ");
        return $sql->row_array();
    }
    function  get_purchaseReturn_item_details($pur_ret_id)
    {
        $tag_details=$this->db->query("SELECT pur_itms.pur_ret_itm_id,pur_itms.pur_ret_pcs as pcs,pur_itms.return_item_type as type,
        pur_itms.pur_ret_gwt as grs_wt,tag.tag_code,tag.product_id,p.product_name,pty.purity
        FROM ret_taging tag
        LEFT JOIN ret_purchase_return_items pur_itms on pur_itms.tag_id=tag.tag_id
        LEFT JOIN ret_product_master p on p.pro_id=tag.product_id
        LEFT JOIN ret_purity pty ON pty.id_purity = tag.purity
        WHERE pur_itms.tag_id is not null
        and pur_itms.return_item_type=2
        and pur_itms.pur_ret_id=".$pur_ret_id."");
        $result['tag_details'] = $tag_details->result_array();
        $non_tag_details=$this->db->query("SELECT pur_itm.pur_ret_itm_id ,pur_itm.pur_ret_id,pur_itm.return_item_type as type,
        pur_itm.pur_ret_pcs as pcs,pur_itm.pur_ret_gwt as grs_wt,pro.product_name,'' as purity
        FROM ret_purchase_return_items pur_itm
        LEFT JOIN ret_product_master pro on pro.pro_id=pur_itm.id_product
        WHERE pur_itm.pur_ret_id=".$pur_ret_id." and pur_itm.return_item_type=3 ");
        $result['nt_details'] = $non_tag_details->result_array();
        $po_details=$this->db->query("SELECT pr_itms.pur_ret_itm_id,po_itms.po_item_pro_id,
        pr_itms.pur_ret_pcs as pcs,pr_itms.pur_ret_gwt as grs_wt,p.product_name,pr_itms.pur_ret_pcs,
        pr_itms.pur_ret_gwt as gross_wt,pr_itms.pur_ret_nwt as net_wt,IFNULL(pr_itms.pur_ret_lwt,0) as less_wt,IFNULL(pr_itms.pur_ret_pur_wt,0) as pure_wt,
        IFNULL(pr_itms.pur_ret_wastage,0) as wastage,IFNULL(pr_itms.pur_ret_mc_value,0) as mc_value,pty.purity
        FROM ret_purchase_order_items po_itms
        LEFT JOIN ret_purchase_return_items pr_itms on pr_itms.pur_ret_po_item_id=po_itms.po_item_id
        LEFT JOIN ret_product_master p on p.pro_id=po_itms.po_item_pro_id
        LEFT JOIN ret_purity pty ON pty.id_purity = po_itms.id_purity
        WHERE pr_itms.pur_ret_po_item_id is not null
        and pr_itms.return_item_type=1
        and pr_itms.pur_ret_id=".$pur_ret_id."");
        $result['po_details'] = $po_details->result_array();
        return $result;
    }
    //lot generate
    function get_qc_stone_accepted_details($po_item_id){
        /*$sql = $this->db->query("SELECT s.qc_passed_pcs,s.qc_passed_wt,st.stone_name,m.uom_name,m.uom_id,i.po_stone_id as stone_id
        FROM ret_po_qc_issue_stone_details s
        LEFT JOIN ret_po_stone_items i ON i.po_st_id = s.po_st_id
        LEFT JOIN ret_stone st ON st.stone_id = i.po_stone_id
        LEFT JOIN ret_uom m ON m.uom_id = i.po_stone_uom
        WHERE s.id_qc_issue_details = ".$id_qc_issue_details."
        HAVING s.qc_passed_wt > 0");*/
        $sql = $this->db->query("SELECT (IFNULL(SUM(s.qc_passed_pcs),0)-IFNULL(lot.stone_pcs,0)) as qc_passed_pcs,(IFNULL(SUM(s.qc_passed_wt),0)-IFNULL(lot.stone_wt,0)) as qc_passed_wt,stn.stone_name,stn.stone_type,m.uom_name,
        stn.uom_id,st.po_stone_id,sty.stone_type as stone_type_name,IFNULL(st.po_quality_id,'') as po_quality_id,st.is_apply_in_lwt,st.po_stone_rate,st.po_stone_amount,st.po_stone_calc_based_on,
        (IFNULL(SUM(s.qc_passed_pcs),0)-IFNULL(lot.stone_pcs,0)) as act_blc_stn_pcs,(IFNULL(SUM(s.qc_passed_wt),0)-IFNULL(lot.stone_wt,0)) as act_blc_stn_wt,
        IFNULL(qc.code,'') as quality_code,IFNULL(rc.clarity,'') as stone_clarity,IFNULL(ro.color,'') as stone_color,IFNULL(rt.cut,'') as stone_cut,IFNULL(rs.name,'') as stone_shape
        FROM ret_po_qc_issue_stone_details s
        LEFT JOIN ret_po_qc_issue_details d ON d.id_qc_issue_details = s.id_qc_issue_details
        LEFT JOIN ret_po_stone_items st ON st.po_st_id = s.po_st_id
        LEFT JOIN ret_stone stn ON stn.stone_id = st.po_stone_id
        LEFT JOIN ret_uom m ON m.uom_id = stn.uom_id
        LEFT JOIN ret_stone_type sty ON sty.id_stone_type = stn.stone_type
        LEFT JOIN ret_quality_code qc ON qc.quality_id = st.po_quality_id
        LEFT JOIN ret_clarity rc ON rc.id_clarity = qc.clarity_id
        LEFT JOIN ret_color ro ON ro.id_color = qc.color_id
        LEFT JOIN ret_cut rt ON rt.id_cut = qc.cut_id
        LEFT JOIN ret_shape rs ON rs.shape_id = qc.shape_id
        LEFT JOIN (SELECT IFNULL(SUM(s.stone_pcs),0) as stone_pcs,IFNULL(SUM(s.stone_wt),0) as stone_wt,s.stone_id
            FROM ret_lot_inwards_stone_detail s
            LEFT JOIN ret_lot_inwards_detail d ON d.id_lot_inward_detail = s.id_lot_inward_detail
            LEFT JOIN ret_purchase_order_items i ON i.po_item_id = d.po_item_id
            where i.po_item_id = ".$po_item_id."
        GROUP BY s.stone_id) as lot ON lot.stone_id = st.po_stone_id
        WHERE st.po_item_id = ".$po_item_id."
        GROUP BY st.po_stone_id
        having qc_passed_wt > 0");
        return $sql->result_array();
    }
    	function checkLotItemExist($data)
    	{
    		$r = array("status" => FALSE);
    		$sql = "SELECT id_lot_inward_detail FROM ret_lot_inwards_detail WHERE lot_product=".$data['id_product']." and lot_no=".$data['lot_no'];
            $res = $this->db->query($sql);
    		if($res->num_rows() > 0){
    			$r = array("status" => TRUE, "id_lot_inward_detail" => $res->row()->id_lot_inward_detail);
    		}else{
    			$r = array("status" => FALSE, "id_lot_inward_detail" => "");
    		}
    		return $r;
    	}
	function update_lot_data($data,$arith){
		$sql = "UPDATE ret_lot_inwards_detail SET no_of_piece=(IFNULL(no_of_piece,0)".$arith." ".$data['no_of_piece']."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),less_wt=(less_wt".$arith." ".$data['less_wt'].") WHERE id_lot_inward_detail=".$data['id_lot_inward_detail'];
		$status = $this->db->query($sql);
		return $status;
	}
    function get_po_details($po_id)
    {
        $sql = $this->db->query("SELECT * FROM ret_purchase_order WHERE po_id =".$po_id."");
        return $sql->row_array();
    }
    function get_po_item_details($po_id)
    {
        $sql = $this->db->query("SELECT * FROM ret_purchase_order_items WHERE po_item_id =".$po_id."");
        return $sql->row_array();
    }
    //lot generate
    function opening_balance_ratefixing($data)
    {
        $sql = $this->db->query("SELECT c.ref_no,(IFNULL(c.weight,0)-IFNULL(f.fixed_weight,0)) as weight,(IFNULL(c.amount,0)-IFNULL(f.amount,0)) as amount,c.id_smith_company_op_balance
        FROM smith_company_op_balance c
        LEFT JOIN(SELECT IFNULL(SUM(rc.weight),0) as fixed_weight,rc.op_blc_id,IFNULL(SUM(rc.charges_amount),0) as amount
            FROM ret_supplier_rate_cut rc
            WHERE rc.status = 1 and rc.is_opening_blc = 1
        GROUP BY rc.op_blc_id) as f ON f.op_blc_id = c.id_smith_company_op_balance
        LEFT JOIN ret_karigar k ON k.id_karigar = c.id_karigar
        WHERE (c.weight_type = 1 OR c.amount_type =1)
        ".($data['id_karigar']!='' ? " and c.id_karigar=".$data['id_karigar']."" :'')."");
        return $sql->result_array();
    }
    function get_approval_rate_fixing_po_no($data)
    {
        $sql = $this->db->query("SELECT p.po_id,p.po_ref_no,(IFNULL(SUM(r.item_pure_wt),0)-IFNULL(rc.fixed_weight,0)-IFNULL(po_ret.pur_ret_pur_wt,0)) as item_pure_wt,
        if(r.mc_type=2,(IFNULL((r.mc_value),0)*IFNULL(SUM(r.no_of_pcs),0)),if(r.calculation_based_on=0,(IFNULL(sum(r.gross_wt),0)*IFNULL((r.mc_value),0)),if(r.calculation_based_on=1,(IFNULL(sum(r.net_wt),0)*IFNULL((r.mc_value),0)),(IFNULL(sum(r.gross_wt),0)*IFNULL((r.mc_value),0))))) as total_mc_value,
        IFNULL(rc.amount,0) as fixed_amount,IFNULL(stn.po_stone_amount,0) as po_stone_amount,IFNULL(po_ret.ret_mc_value,0) as ret_mc_value,IFNULL(po_ret_stn.ret_stone_amount,0) as ret_stone_amount,IFNULL(chrg.total_charge_value,0) as purchase_charges,p.fin_year_code
        FROM ret_purchase_order p
        LEFT JOIN ret_purchase_order_items r ON r.po_item_po_id = p.po_id
        LEFT JOIN ret_grn_entry grn ON grn.grn_id = p.po_grn_id
        LEFT JOIN (SELECT IFNULL(SUM(c.total_charge_value),0) as total_charge_value,r.po_item_po_id
        FROM ret_purchase_other_charges c
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = c.pur_po_item_id
        GROUP BY r.po_item_po_id) as chrg ON chrg.po_item_po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(s.po_stone_amount),0) as po_stone_amount,r.po_item_po_id
            FROM ret_po_stone_items s
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = s.po_item_id
        GROUP BY r.po_item_po_id) as stn ON stn.po_item_po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(c.weight),0) as fixed_weight,c.po_id,IFNULL(SUM(c.charges_amount),0) as amount
            FROM ret_supplier_rate_cut c
            WHERE c.status = 1
        GROUP BY c.po_id) as rc ON rc.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(r.pur_ret_pur_wt),0) as pur_ret_pur_wt,p.po_id,
            if(r.calculation_based_on=0,(IFNULL(sum(r.pur_ret_gwt),0)*IFNULL(sum(r.pur_ret_mc_value),0)),if(r.calculation_based_on=1,(IFNULL(sum(r.pur_ret_nwt),0)*IFNULL(sum(r.pur_ret_mc_value),0)),(IFNULL(sum(r.pur_ret_gwt),0)*IFNULL(sum(r.pur_ret_mc_value),0)))) as ret_mc_value
            FROM ret_purchase_return_items r
            LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id
            LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = r.pur_ret_po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
            WHERE ret.bill_status = 1
        GROUP BY p.po_id) as po_ret ON po_ret.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(s.ret_stone_amount),0) as ret_stone_amount,p.po_id
            FROM ret_purchase_return_stone_items s
            LEFT JOIN ret_purchase_return_items r ON r.pur_ret_itm_id = s.pur_ret_return_id
            LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id
            LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = r.pur_ret_po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
            WHERE ret.bill_status = 1
        GROUP BY p.po_id) as po_ret_stn ON po_ret_stn.po_id = p.po_id
        WHERE p.is_approved = 1 AND p.isratefixed = 0 AND p.bill_status = 1 AND grn.grn_type IN (2, 4)
        ".($data['id_karigar']!='' ? " and p.po_karigar_id=".$data['id_karigar']."" :'')."
        GROUP BY p.po_id
        having item_pure_wt > 0");
        //print_r($this->db->last_query());exit;
        $result =  $sql->result_array();
        foreach($result as $val){
            $returnData[]=array(
                        'fixed_amount'      =>$val['fixed_amount'],
                        'po_stone_amount'   =>$val['po_stone_amount'],
                        'ret_stone_amount'  =>$val['ret_stone_amount'],
                        'total_mc_value'    =>$val['total_mc_value'],
                        'item_pure_wt'      =>$val['item_pure_wt'],
                        'po_id'             =>$val['po_id'],
                        'po_ref_no'         =>$val['po_ref_no'],
                        'ret_mc_value'      =>$val['ret_mc_value'],
                        'po_charges'        =>$val['purchase_charges'],
                        'total_amount'      =>number_format(($val['total_mc_value']+$val['purchase_charges']-$val['fixed_amount']+$val['po_stone_amount']-$val['ret_mc_value']-$val['ret_stone_amount']),2,'.',''),
            );
        }
        return $returnData;
    }
    function get_approval_conversion_details($id_supplier_rate_cut)
    {
        $sql = $this->db->query("SELECT date_format(c.date_add,'%d-%m-%Y') as date_add,cat.name as category_name,
        IFNULL(SUM(c.weight),0) as pure_wt,IFNULL(c.rate_per_gram,0) as rate_per_gram,if(c.convert_to=1,'Purchase Bill','JOb Work Receipt') as title,
        IFNULL(SUM(c.amount),0) as total_amount,IFNULL(SUM(c.tax_amount),0) as tax_amount,IFNULL(c.tax_percentage,'') as tax_percentage,
        IFNULL(k.firstname,'') as karigar_name,k.contactno1 as mobile,IFNULL(k.address1,'') as address1,
        IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,
        IFNULL(cy.name,'') as city_name,IFNULL(ct.name,'') as country_name,IFNULL(st.name,'') as state_name,
        IFNULL(k.pincode,'') as pincode,ifnull(c.narration,'') as remark,ifnull(p.product_name,'') as product_name,
        c.igst_cost,c.sgst_cost,c.cgst_cost,pro.po_ref_no,bill.bill_no,IFNULL(cat.name,'') as category_name,c.rate_cut_type,
        IF(c.convert_to = 1, 'Supplier', IF(c.convert_to = 2, 'Manufacturer', IF(c.convert_to = 3, 'Stone Supplier', IF(c.convert_to = 4, 'Diamond Supplier', '')))) AS transcation,
        IFNULL(k.gst_number,'') as gst_number,k.pan_no,IFNULL(c.charges_amount,'') as charges,IF(c.conversion_type=1,'Fix','UnFix') as conversion, c.is_tds, c.tds_percent, c.tds_tax_value
        
        FROM ret_supplier_rate_cut c
        LEFT JOIN ret_karigar k on k.id_karigar = c.id_karigar
        LEFT JOIN ret_purchase_order pro on pro.po_id = c.po_id
        LEFT JOIN ret_billing bill on bill.bill_no = c.ref_no
        LEFT JOIN ret_product_master p ON p.pro_id = c.id_product
        LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id
        LEFT JOIN country ct ON ct.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city cy ON cy.id_city=k.id_city
        LEFT JOIN employee emp on emp.id_employee=c.created_by 
        WHERE c.id_supplier_rate_cut = ".$id_supplier_rate_cut."
        GROUP BY p.cat_id");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
    }
    function get_supplier_rate_cut_details($data)
    {
        $sql= $this->db->query("SELECT date_format(src.date_add,'%d-%m-%Y') as date_add,src.id_supplier_rate_cut,b.name as branch_name,k.firstname as firstname,
            IF(src.rate_cut_type=1,'AMOUNT TO WEIGHT','WEIGHT TO AMOUNT') as rate_cut_type,grn.purchase_type,
            c.metal as cat_name,src.amount as amount,src.rate_per_gram,src.weight,src.status,IFNULL(src.ref_no,'') as ref_no,
            IFNULL(pay.is_verified, 0) as is_verified, IFNULL(pay.pay_id, 0) as pay_id,
            IFNULL((SELECT COUNT(*) FROM ret_po_payment_detail pd WHERE pd.pay_id = pay.pay_id AND pd.type = 5), 0) as has_cheque
        FROM ret_supplier_rate_cut src
        LEFT JOIN branch b ON b.id_branch = src.id_branch
        LEFT JOIN ret_karigar k ON k.id_karigar = src.id_karigar
        LEFT JOIN metal c ON c.id_metal =  src.id_metal
        LEFT JOIN ret_grn_entry grn ON grn.grn_karigar_id = k.id_karigar
        LEFT JOIN ret_po_payment pay ON pay.id_supplier_rate_cut = src.id_supplier_rate_cut
        WHERE src.id_supplier_rate_cut IS NOT NULL
        ".($data['id_branch']!='' ? " and src.id_branch=".$data['id_branch']."" :'')."
        ".($data['from_date']!='' && $data['to_date']!='' ? (function() use ($data) {
            $from = DateTime::createFromFormat('d-m-Y', $data['from_date']) ?: new DateTime($data['from_date']);
            $to   = DateTime::createFromFormat('d-m-Y', $data['to_date'])   ?: new DateTime($data['to_date']);
            return " and (date(src.date_add) BETWEEN '".$from->format('Y-m-d')."' AND '".$to->format('Y-m-d')."')";
        })() : '')."
        ".($data['purchase_type'] != '' && $data['purchase_type'] != 0 ? " and grn.purchase_type = ".$data['purchase_type']."" : '')."
         GROUP by src.id_supplier_rate_cut Order by src.id_supplier_rate_cut DESC");
        // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_headoffice_valut_report_old($data)
    {
        $multiple_id_metal = implode(' , ', $data['id_metal']);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $data['id_metal'];
		}
        $sql = $this->db->query("SELECT p.po_ref_no,date_format(p.po_date,'%d-%m-%Y') as podate,k.firstname as karigar_name,cat.name as category_name,pro.product_name,
        IFNULL(SUM(itm.gross_wt),0) as inw_gwt,IFNULL(SUM(itm.net_wt),0) as inw_nwt,IFNULL(SUM(itm.no_of_pcs),0) as inw_pcs,
        IFNULL((stndet.po_stone_wt),0) as inw_stn_wt,
        IFNULL(ret.pur_ret_pcs,0) as pur_ret_pcs,IFNULL(ret.pur_ret_gwt,0) as pur_ret_gwt,IFNULL(ret.pur_ret_nwt,0) as pur_ret_nwt,IFNULL(ret_stn.ret_stone_wt,0) as ret_stone_wt,IFNULL(ret_stn.ret_stone_pcs,0) as ret_stone_pcs,
        IFNULL(lot.lot_pcs,0) as lot_pcs,IFNULL(lot.gross_wt,0) as lot_gwt,IFNULL(lot.net_wt,0) as lot_nwt,IFNULL(lot_stn.stone_pcs,0) as lot_stn_pcs,IFNULL(lot_stn.stone_wt,0) as lot_stn_wt,
        IFNULL(tagdet.tag_pcs,0) as tag_pcs,IFNULL(tagdet.tag_gwt,0) as tag_gwt,IFNULL(tagdet.tag_lwt,0) as tag_lwt,IFNULL(tagdet.tag_nwt,0) as tag_nwt,IFNULL(tag_stn.stn_wt,0) as tag_stn_wt,
        (IFNULL(SUM(itm.gross_wt),0)-(IFNULL(ret.pur_ret_gwt,0)-(IFNULL(lot.gross_wt,0)))) as blc_gwt,
        (IFNULL(SUM(itm.net_wt),0)-(IFNULL(ret.pur_ret_nwt,0))-(IFNULL(lot.net_wt,0))) as blc_nwt,
        (IFNULL(SUM(itm.no_of_pcs),0)-IFNULL(ret.pur_ret_pcs,0)-IFNULL(lot.lot_pcs,0)) as blc_pcs
        FROM ret_purchase_order p
        LEFT JOIN ret_grn_entry g ON g.grn_id = p.po_grn_id
        LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_po_id = p.po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = itm.po_item_pro_id
        LEFT JOIN ret_category cat ON cat.id_ret_category = itm.po_item_cat_id
        LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_wt),0) as po_stone_wt,stn_itm.po_item_po_id,stn_itm.po_item_pro_id
            FROM ret_po_stone_items s
            LEFT JOIN ret_stone stn ON stn.stone_id = s.po_stone_id
            LEFT JOIN ret_purchase_order_items stn_itm ON stn_itm.po_item_id = s.po_item_id
            LEFT JOIN ret_product_master p ON p.pro_id = stn_itm.po_item_pro_id
            LEFT JOIN ret_category cat ON cat.id_ret_category = itm.po_item_cat_id
            LEFT JOIN ret_uom m on m.uom_id = s.po_stone_uom
            WHERE stn.stone_type = 1
            ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
            GROUP BY stn_itm.po_item_po_id,stn_itm.po_item_pro_id) as stndet ON stndet.po_item_po_id = p.po_id AND stndet.po_item_pro_id = itm.po_item_pro_id
        LEFT JOIN(SELECT itm.po_item_po_id,itm.po_item_pro_id,IFNULL(SUM(r.pur_ret_pcs),0) as pur_ret_pcs,
        IFNULL(SUM(r.pur_ret_gwt),0) as pur_ret_gwt,IFNULL(SUM(r.pur_ret_nwt),0) as pur_ret_nwt
        FROM ret_purchase_return_items r
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = r.pur_ret_po_item_id
        LEFT JOIN ret_purchase_order po ON po.po_id = itm.po_item_po_id
        LEFT JOIN ret_purchase_return rtm ON rtm.pur_return_id = r.pur_ret_id
        LEFT JOIN ret_category cat ON cat.id_ret_category = itm.po_item_cat_id
        WHERE rtm.bill_status = 1
        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
        GROUP BY itm.po_item_po_id,itm.po_item_pro_id) as ret ON ret.po_item_po_id = p.po_id AND ret.po_item_pro_id = itm.po_item_pro_id
        LEFT JOIN (SELECT IFNULL(SUM(s.ret_stone_wt),0) as ret_stone_wt,IFNULL(SUM(s.ret_stone_pcs),0) as ret_stone_pcs,r.id_product,p.po_id
        FROM ret_purchase_return_stone_items s
        LEFT JOIN ret_purchase_return_items r ON r.pur_ret_itm_id = s.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = r.pur_ret_po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_category cat ON cat.id_ret_category = itm.po_item_cat_id
        LEFT JOIN ret_stone st ON st.stone_id = s.ret_stone_id
        WHERE ret.bill_status = 1 AND st.stone_type = 1
        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
        GROUP BY r.id_product,p.po_id ) as ret_stn ON ret_stn.po_id = p.po_id AND ret_stn.id_product = itm.po_item_pro_id
        LEFT JOIN(SELECT IFNULL(SUM(d.no_of_piece),0) as lot_pcs,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt,d.lot_product,l.po_id
        FROM ret_lot_inwards l
        LEFT JOIN ret_lot_inwards_detail d ON d.lot_no = l.lot_no
        LEFT JOIN ret_product_master p ON p.pro_id = d.lot_product
        LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id
        where l.lot_no IS NOT NULL
        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
        GROUP BY d.lot_product,l.po_id) as lot ON lot.lot_product = itm.po_item_pro_id AND lot.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(d.stone_pcs),0) as stone_pcs,IFNULL(SUM(d.stone_wt),0) as stone_wt,det.lot_product,p.po_id
        FROM ret_lot_inwards_stone_detail d
        LEFT JOIN ret_lot_inwards_detail det ON det.id_lot_inward_detail = d.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards l ON l.lot_no = det.lot_no
        LEFT JOIN ret_purchase_order p ON p.po_id = l.po_id
        LEFT JOIN ret_stone st ON st.stone_id = d.stone_id
        LEFT JOIN ret_product_master pr ON pr.pro_id = det.lot_product
        LEFT JOIN ret_category cat ON cat.id_ret_category = pr.cat_id
        WHERE p.bill_status = 1 AND st.stone_type = 1
        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
        GROUP BY det.lot_product,p.po_id) as lot_stn ON lot_stn.lot_product = itm.po_item_pro_id AND lot_stn.po_id = p.po_id
        LEFT JOIN(SELECT tag.product_id,l.po_id,IFNULL(SUM(tag.piece),0) as tag_pcs,IFNULL(SUM(tag.gross_wt),0) as tag_gwt,
        IFNULL(SUM(tag.less_wt),0) as tag_lwt,IFNULL(SUM(tag.net_wt),0) as tag_nwt
        FROM ret_lot_inwards_detail d
        LEFT JOIN ret_lot_inwards l ON l.lot_no = d.lot_no
        LEFT JOIN ret_taging tag ON tag.tag_lot_id = l.lot_no
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id
        where l.lot_no IS NOT NULL
        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
        GROUP BY tag.product_id,l.po_id) as tagdet ON tagdet.product_id = itm.po_item_pro_id AND tagdet.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(ts.wt),0) as stn_wt,IFNULL(SUM(ts.pieces),0) as stn_pcs,t.product_id,p.po_id
        FROM ret_taging_stone ts
        LEFT JOIN ret_taging t ON t.tag_id = ts.tag_id
        LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
        LEFT JOIN ret_purchase_order p ON p.po_id = l.po_id
        LEFT JOIN ret_stone st ON st.stone_id = ts.stone_id
        LEFT JOIN ret_product_master pr ON pr.pro_id = t.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category = pr.cat_id
        WHERE p.bill_status = 1 AND st.stone_type = 1
        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.product_id,p.po_id) as tag_stn ON tag_stn.product_id = itm.po_item_pro_id AND tag_stn.po_id = p.po_id
        WHERE p.bill_status = 1
        ".($data['po_ref_no']!='' && $data['po_ref_no']!=0 ? " and p.po_id=".$data['po_ref_no']."" :'')."
        ".($data['from_date'] != '' && $data['to_date']!='' ? ' and date(p.po_date) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'"' : '')."
        ".($data['id_karigar']!='' && $data['id_karigar']!=0 ? " and p.po_karigar_id=".$data['id_karigar']."" :'')."
        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
        ".($data['grn_type']!=     0 ? " and g.grn_type=".$data['grn_type']."" :'')."
        GROUP BY p.po_id,itm.po_item_pro_id
        ORDER BY p.po_id DESC");
        //print_r($this->db->last_query());exit;
        $response_data = $sql->result_array();
    	foreach($response_data as $key => $val){
    	    $return_data[$val['po_ref_no']][] =   $val;
    	}
    	foreach($return_data as $key => $grndata){
    	    foreach($grndata as $gkey => $gval){
            	if($gkey > 0){
            	    $return_data[$key][$gkey]["po_ref_no"] = "";
            	}
        	}
    	}
    	//echo "<pre>";print_r($return_data);exit;
    	return $return_data;
    }
    function get_headoffice_valut_report($data)
    {
        $multiple_id_metal = implode(' , ', $data['id_metal']);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $data['id_metal'];
		}
		$multiple_category = implode(' , ', $data['id_category']);
        if($multiple_category != '')
		{
			$id_category = $multiple_category;
		}else{
			$id_category = $data['id_category'];
		}
        $report_type = $data['valut_report_type'];
        $op_date= date('Y-m-d',(strtotime('-1 day',strtotime($data['from_date']))));
        $sql = $this->db->query("SELECT p.po_id,p.po_ref_no,date_format(p.po_date,'%d-%m-%Y') as podate,k.firstname as karigar_name,
        round((IFNULL(blc_inw.inw_pcs,0)-IFNULL(blc_ret.pur_ret_pcs,0)-IFNULL(blc_lot.lot_pcs,0)-IFNULL(metal_issue_blc.issue_pcs,0)),0) as blc_pcs,
        round((IFNULL(blc_inw.inw_gwt,0)-IFNULL(blc_ret.pur_ret_gwt,0)-IFNULL(blc_lot.lot_gwt,0)-IFNULL(metal_issue_blc.issue_wt,0)),3) as blc_gwt,
        round((IFNULL(blc_inw.inw_nwt,0)-IFNULL(blc_ret.pur_ret_nwt,0)-IFNULL(blc_lot.lot_nwt,0)-IFNULL(metal_issue_blc.issue_wt,0)),3) as blc_nwt,
        round((IFNULL(blc_stn.po_stone_wt,0)-IFNULL(blc_ret_stn.ret_stone_wt,0)-IFNULL(blc_lot_stn.stone_wt,0)-IFNULL(metal_issue_dia_blc.issue_wt,0) + (IFNULL(blc_inw_lose.inw_gwt,0)) - IFNULL(loosestn_blc_lot.lot_gwt,0) - IFNULL(loosestn_blc_ret.pur_ret_gwt,0)),3) as blc_diawt,
        IFNULL(inw.inw_pcs,0) as inw_pcs,IFNULL(inw.inw_gwt,0) as inw_gwt,IFNULL(inw.inw_nwt,0) as inw_nwt,(IFNULL(inw_stn.po_stone_wt,0) + IFNULL(inwLose.inw_gwt,0)) as inw_diawt,
        IFNULL(ret.pur_ret_pcs,0) as pur_ret_pcs,IFNULL(ret.pur_ret_gwt,0) as pur_ret_gwt,IFNULL(ret.pur_ret_nwt,0) as pur_ret_nwt,(IFNULL(ret_stn.ret_stone_wt,0) + IFNULL(loosestn_ret.pur_ret_gwt,0)) as ret_diawt,
        (IFNULL(lot.lot_pcs,0)) as lot_pcs,(IFNULL(lot.lot_gwt,0)) as lot_gwt,(IFNULL(lot.lot_nwt,0)) as lot_nwt,(IFNULL(lot_stn.stone_wt,0) + IFNULL(loosestn_lot.lot_gwt,0)) as lot_dia_wt,
        IFNULL(tag.tag_pcs,0) as tag_pcs,IFNULL(tag.tag_gwt,0) as tag_gwt,IFNULL(tag.tag_nwt,0) as tag_nwt,IFNULL(tag.tag_nwt,0) as tag_nwt,(IFNULL(tag_stn.stn_wt,0) + IFNULL(loosestn_tag.tag_gwt,0)) as tag_dia_wt,
        IFNULL(metal_issue.issue_pcs,0) as issue_pcs,IFNULL(metal_issue.issue_wt,0) as issue_wt,IFNULL(metal_issue_dia.issue_wt,0) as issue_diawt
        FROM ret_purchase_order p
        LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
        LEFT JOIN ret_grn_entry g ON g.grn_id = p.po_grn_id
        LEFT JOIN (SELECT IFNULL(SUM(itm.gross_wt),0) as inw_gwt,IFNULL(SUM(itm.net_wt),0) as inw_nwt,
        IFNULL(SUM(itm.no_of_pcs),0) as inw_pcs,p.po_id
        FROM ret_purchase_order_items itm
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.bill_status = 1 and p.is_approved = 1 and uom!=6
        AND date(p.po_date)<='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as blc_inw ON blc_inw.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(itm.gross_wt),0) as inw_gwt,IFNULL(SUM(itm.net_wt),0) as inw_nwt,
        IFNULL(SUM(itm.no_of_pcs),0) as inw_pcs,p.po_id
        FROM ret_purchase_order_items itm
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.bill_status = 1 and p.is_approved = 1 and uom =6
        AND date(p.po_date)<='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as blc_inw_lose ON blc_inw_lose.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_wt),0) as po_stone_wt,stn_itm.po_item_po_id,stn_itm.po_item_pro_id
        FROM ret_po_stone_items s
        LEFT JOIN ret_stone stn ON stn.stone_id = s.po_stone_id
        LEFT JOIN ret_purchase_order_items stn_itm ON stn_itm.po_item_id = s.po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = stn_itm.po_item_po_id
        LEFT JOIN ret_uom m on m.uom_id = s.po_stone_uom
        LEFT JOIN ret_product_master pro ON pro.pro_id = stn_itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE stn.stone_type = 1
        AND date(p.po_date)<='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY stn_itm.po_item_po_id) as blc_stn ON blc_stn.po_item_po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(r.pur_ret_pcs),0) as pur_ret_pcs,IFNULL(SUM(r.pur_ret_gwt),0) as pur_ret_gwt,
        IFNULL(SUM(r.pur_ret_nwt),0) as pur_ret_nwt,p.po_id
        FROM ret_purchase_return ret
        LEFT JOIN ret_purchase_return_items r ON r.pur_ret_id = ret.pur_return_id
        LEFT JOIN ret_purchase_order_items ret_itm ON ret_itm.po_item_id = r.pur_ret_po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = ret_itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = ret_itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE ret.bill_status = 1
        AND date(ret.bill_date)<='".$op_date."' and ret_itm.uom!=6
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as blc_ret ON blc_ret.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(r.pur_ret_pcs),0) as pur_ret_pcs,IFNULL(SUM(r.pur_ret_gwt),0) as pur_ret_gwt,
        IFNULL(SUM(r.pur_ret_nwt),0) as pur_ret_nwt,p.po_id
        FROM ret_purchase_return ret
        LEFT JOIN ret_purchase_return_items r ON r.pur_ret_id = ret.pur_return_id
        LEFT JOIN ret_purchase_order_items ret_itm ON ret_itm.po_item_id = r.pur_ret_po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = ret_itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = ret_itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE ret.bill_status = 1
        AND date(ret.bill_date)<='".$op_date."' and ret_itm.uom = 6
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as loosestn_blc_ret ON loosestn_blc_ret.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(s.ret_stone_wt),0) as ret_stone_wt,IFNULL(SUM(s.ret_stone_pcs),0) as ret_stone_pcs,
        r.id_product,p.po_id
        FROM ret_purchase_return_stone_items s
        LEFT JOIN ret_purchase_return_items r ON r.pur_ret_itm_id = s.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = r.pur_ret_po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_stone st ON st.stone_id = s.ret_stone_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE ret.bill_status = 1 AND st.stone_type = 1 AND date(ret.bill_date)<='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id ) as blc_ret_stn ON blc_ret_stn.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(d.no_of_piece),0) as lot_pcs,IFNULL(SUM(d.gross_wt),0) as lot_gwt,
        IFNULL(SUM(d.net_wt),0) as lot_nwt,l.po_id
        FROM ret_lot_inwards l
        LEFT JOIN ret_lot_inwards_detail d ON d.lot_no = l.lot_no
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = d.po_item_id
        LEFT JOIN ret_category c ON c.id_ret_category = l.id_category
        LEFT JOIN ret_product_master p ON p.pro_id = d.lot_product
        where date(l.lot_date) <='".$op_date."'
        AND if(IFNULL(d.po_item_id,'')!='',po_itm.uom!=6,l.lot_no IS NOT NULL)
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as blc_lot ON blc_lot.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(d.no_of_piece),0) as lot_pcs,IFNULL(SUM(d.gross_wt),0) as lot_gwt,
        IFNULL(SUM(d.net_wt),0) as lot_nwt,l.po_id
        FROM ret_lot_inwards l
        LEFT JOIN ret_lot_inwards_detail d ON d.lot_no = l.lot_no
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = d.po_item_id
        LEFT JOIN ret_category c ON c.id_ret_category = l.id_category
        LEFT JOIN ret_product_master p ON p.pro_id = d.lot_product
        where  date(l.lot_date) <='".$op_date."'
        AND po_itm.uom=6
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as loosestn_blc_lot ON loosestn_blc_lot.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(nt.pcs),0) as lot_pcs,IFNULL(SUM(nt.grs_wt),0) as lot_gwt,
        IFNULL(SUM(nt.grs_wt),0) as lot_nwt,l.po_id
        FROM ret_nontag_receipt nt
        LEFT JOIN ret_lot_inwards l ON l.lot_no = nt.lot_id
        LEFT JOIN ret_product_master p ON p.pro_id = nt.id_product
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        where p.stock_type = 2 and date(l.lot_date) <='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as blc_nt_lot ON blc_nt_lot.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(d.stone_pcs),0) as stone_pcs,IFNULL(SUM(d.stone_wt),0) as stone_wt,det.lot_product,p.po_id
        FROM ret_lot_inwards_stone_detail d
        LEFT JOIN ret_lot_inwards_detail det ON det.id_lot_inward_detail = d.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards l ON l.lot_no = det.lot_no
        LEFT JOIN ret_purchase_order p ON p.po_id = l.po_id
        LEFT JOIN ret_stone st ON st.stone_id = d.stone_id
        LEFT JOIN ret_category c ON c.id_ret_category = l.id_category
        LEFT JOIN ret_product_master pro ON pro.pro_id = det.lot_product
        WHERE p.bill_status = 1 AND st.stone_type = 1 and date(l.lot_date) <='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as blc_lot_stn ON blc_lot_stn.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(t.piece),0) as tag_pcs,IFNULL(SUM(t.gross_wt),0) as tag_gwt,
        IFNULL(SUM(t.net_wt),0) as tag_nwt,l.po_id
        FROM ret_taging t
        LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        where date(t.tag_datetime) <='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as blc_tag ON blc_tag.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(ts.wt),0) as stn_wt,IFNULL(SUM(ts.pieces),0) as stn_pcs,t.product_id,p.po_id
        FROM ret_taging_stone ts
        LEFT JOIN ret_taging t ON t.tag_id = ts.tag_id
        LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
        LEFT JOIN ret_purchase_order p ON p.po_id = l.po_id
        LEFT JOIN ret_stone st ON st.stone_id = ts.stone_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.bill_status = 1 AND st.stone_type = 1 and date(t.tag_datetime) <='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as blc_tag_stn ON blc_tag_stn.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt,p.po_id
        FROM ret_karigar_metal_issue_details d
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = d.po_item_id
        LEFT JOIN ret_karigar_metal_issue i ON i.met_issue_id = d.issue_met_parent_id
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = d.issu_met_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE i.bill_status = 1 and date(i.met_issue_date)<='".$op_date."' and itm.uom!=6
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as metal_issue_blc ON metal_issue_blc.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt,p.po_id
        FROM ret_karigar_metal_issue_details d
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = d.po_item_id
        LEFT JOIN ret_karigar_metal_issue i ON i.met_issue_id = d.issue_met_parent_id
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = d.issu_met_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE i.bill_status = 1 and date(i.met_issue_date)<='".$op_date."' and itm.uom = 6
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as metal_issue_dia_blc ON metal_issue_dia_blc.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(itm.gross_wt),0) as inw_gwt,IFNULL(SUM(itm.net_wt),0) as inw_nwt,
        IFNULL(SUM(itm.no_of_pcs),0) as inw_pcs,p.po_id
        FROM ret_purchase_order_items itm
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.bill_status = 1 and p.is_approved = 1 and itm.uom!=6
        AND date(p.po_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as inw ON inw.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(itm.gross_wt),0) as inw_gwt,IFNULL(SUM(itm.net_wt),0) as inw_nwt,
        IFNULL(SUM(itm.no_of_pcs),0) as inw_pcs,p.po_id
        FROM ret_purchase_order_items itm
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.bill_status = 1 and p.is_approved = 1 and itm.uom =6
        AND date(p.po_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as inwLose ON inwLose.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_wt),0) as po_stone_wt,stn_itm.po_item_po_id,stn_itm.po_item_pro_id
        FROM ret_po_stone_items s
        LEFT JOIN ret_stone stn ON stn.stone_id = s.po_stone_id
        LEFT JOIN ret_purchase_order_items stn_itm ON stn_itm.po_item_id = s.po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = stn_itm.po_item_po_id
        LEFT JOIN ret_uom m on m.uom_id = s.po_stone_uom
        LEFT JOIN ret_product_master pro ON pro.pro_id = stn_itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE stn.stone_type = 1  and p.is_approved = 1
        AND date(p.po_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY stn_itm.po_item_po_id) as inw_stn ON inw_stn.po_item_po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(r.pur_ret_pcs),0) as pur_ret_pcs,IFNULL(SUM(r.pur_ret_gwt),0) as pur_ret_gwt,
        IFNULL(SUM(r.pur_ret_nwt),0) as pur_ret_nwt,p.po_id
        FROM ret_purchase_return ret
        LEFT JOIN ret_purchase_return_items r ON r.pur_ret_id = ret.pur_return_id
        LEFT JOIN ret_purchase_order_items ret_itm ON ret_itm.po_item_id = r.pur_ret_po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = ret_itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = ret_itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE ret.bill_status = 1 and ret_itm.uom!=6
        AND date(ret.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as ret ON ret.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(r.pur_ret_pcs),0) as pur_ret_pcs,IFNULL(SUM(r.pur_ret_gwt),0) as pur_ret_gwt,
        IFNULL(SUM(r.pur_ret_nwt),0) as pur_ret_nwt,p.po_id
        FROM ret_purchase_return ret
        LEFT JOIN ret_purchase_return_items r ON r.pur_ret_id = ret.pur_return_id
        LEFT JOIN ret_purchase_order_items ret_itm ON ret_itm.po_item_id = r.pur_ret_po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = ret_itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = ret_itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE ret.bill_status = 1 and ret_itm.uom = 6
        AND date(ret.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as loosestn_ret ON loosestn_ret.po_id = p.po_id
    LEFT JOIN(SELECT IFNULL(SUM(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt,itm.po_item_po_id as po_id
        FROM ret_karigar_metal_issue_details d
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = d.po_item_id
        LEFT JOIN ret_karigar_metal_issue i ON i.met_issue_id = d.issue_met_parent_id
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = d.issu_met_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE i.bill_status = 1 and itm.uom != 6
        AND date(i.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as metal_issue ON metal_issue.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt,p.po_id
        FROM ret_karigar_metal_issue_details d
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = d.po_item_id
        LEFT JOIN ret_karigar_metal_issue i ON i.met_issue_id = d.issue_met_parent_id
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = d.issu_met_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE i.bill_status = 1 and itm.uom = 6
        AND date(i.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as metal_issue_dia ON metal_issue_dia.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(s.ret_stone_wt),0) as ret_stone_wt,IFNULL(SUM(s.ret_stone_pcs),0) as ret_stone_pcs,r.id_product,p.po_id
        FROM ret_purchase_return_stone_items s
        LEFT JOIN ret_purchase_return_items r ON r.pur_ret_itm_id = s.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = r.pur_ret_id
        LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = r.pur_ret_po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
        LEFT JOIN ret_stone st ON st.stone_id = s.ret_stone_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = itm.po_item_pro_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE ret.bill_status = 1 AND st.stone_type = 1 AND date(ret.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id ) as ret_stn ON ret_stn.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(nt.pcs),0) as lot_pcs,IFNULL(SUM(nt.grs_wt),0) as lot_gwt,IFNULL(SUM(nt.grs_wt),0) as lot_nwt,l.po_id
        FROM ret_nontag_receipt nt
        LEFT JOIN ret_lot_inwards l ON l.lot_no = nt.lot_id
        LEFT JOIN ret_product_master p ON p.pro_id = nt.id_product
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        where p.stock_type = 2 and date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as nt_lot ON nt_lot.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(d.no_of_piece),0) as lot_pcs,IFNULL(SUM(d.gross_wt),0) as lot_gwt,
        IFNULL(SUM(d.net_wt),0) as lot_nwt,l.po_id
        FROM ret_lot_inwards l
        LEFT JOIN ret_lot_inwards_detail d ON d.lot_no = l.lot_no
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = d.po_item_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.lot_product
        LEFT JOIN ret_category c ON c.id_ret_category = l.id_category
        where  date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        AND if(IFNULL(d.po_item_id,'')!='',po_itm.uom!=6,l.lot_no IS NOT NULL)
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as lot ON lot.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(d.no_of_piece),0) as lot_pcs,IFNULL(SUM(d.gross_wt),0) as lot_gwt,
        IFNULL(SUM(d.net_wt),0) as lot_nwt,l.po_id
        FROM ret_lot_inwards l
        LEFT JOIN ret_lot_inwards_detail d ON d.lot_no = l.lot_no
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = d.po_item_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.lot_product
        LEFT JOIN ret_category c ON c.id_ret_category = l.id_category
        where  date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        and po_itm.uom=6
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as loosestn_lot ON loosestn_lot.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(d.stone_pcs),0) as stone_pcs,IFNULL(SUM(d.stone_wt),0) as stone_wt,det.lot_product,p.po_id
        FROM ret_lot_inwards_stone_detail d
        LEFT JOIN ret_lot_inwards_detail det ON det.id_lot_inward_detail = d.id_lot_inward_detail
        LEFT JOIN ret_lot_inwards l ON l.lot_no = det.lot_no
        LEFT JOIN ret_purchase_order p ON p.po_id = l.po_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = det.lot_product
        LEFT JOIN ret_stone st ON st.stone_id = d.stone_id
        LEFT JOIN ret_category c ON c.id_ret_category = l.id_category
        WHERE p.bill_status = 1 AND st.stone_type = 1 and date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as lot_stn ON lot_stn.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(t.piece),0) as tag_pcs,IFNULL(SUM(t.gross_wt),0) as tag_gwt,
        IFNULL(SUM(t.net_wt),0) as tag_nwt,l.po_id
        FROM ret_taging t
        LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
         where date(t.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'  and (t.uom_gross_wt!=6 OR t.uom_gross_wt IS NULL)
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as tag ON tag.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(t.piece),0) as tag_pcs,IFNULL(SUM(t.gross_wt),0) as tag_gwt,
        IFNULL(SUM(t.net_wt),0) as tag_nwt,l.po_id
        FROM ret_taging t
        LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        where date(t.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        and t.uom_gross_wt=6
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY l.po_id) as loosestn_tag ON loosestn_tag.po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(ts.wt),0) as stn_wt,IFNULL(SUM(ts.pieces),0) as stn_pcs,t.product_id,p.po_id
        FROM ret_taging_stone ts
        LEFT JOIN ret_taging t ON t.tag_id = ts.tag_id
        LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
        LEFT JOIN ret_purchase_order p ON p.po_id = l.po_id
        LEFT JOIN ret_stone st ON st.stone_id = ts.stone_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.bill_status = 1 AND st.stone_type = 1 and date(t.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        GROUP BY p.po_id) as tag_stn ON tag_stn.po_id = p.po_id
        WHERE p.bill_status = 1
        ".($data['po_ref_no']!='' && $data['po_ref_no']!=0 ? " and p.po_id=".$data['po_ref_no']."" :'')."
        ".($data['id_karigar']!='' && $data['id_karigar']!=0 ? " and p.po_karigar_id=".$data['id_karigar']."" :'')."
        ".($data['grn_type']!=     0 ? " and g.grn_type=".$data['grn_type']."" :'')."
        order by p.po_id DESC");
       //print_r($this->db->last_query());exit;
        $response_data = $sql->result_array();
    	foreach($response_data as $key => $val){
    	    $closing_pcs = number_format(($val['blc_pcs']+$val['inw_pcs']-$val['pur_ret_pcs']-$val['issue_pcs']-$val['lot_pcs']),0,'.','');
    	    $closing_gwt = number_format(($val['blc_gwt']+$val['inw_gwt']-$val['pur_ret_gwt']-$val['issue_wt']-$val['lot_gwt']),3,'.','');
    	    $closing_nwt = number_format(($val['blc_nwt']+$val['inw_nwt']-$val['pur_ret_nwt']-$val['issue_wt']-$val['lot_nwt']),3,'.','');
    	    $closing_diawt = number_format(($val['blc_diawt']+$val['inw_diawt']-$val['ret_diawt']-$val['lot_dia_wt']-$val['issue_diawt']),3,'.','');
    	    if($val['blc_gwt']!=0 || $val['inw_gwt']!=0 || $val['pur_ret_gwt']!=0 || $val['lot_gwt']!=0 || $val['tag_gwt']!=0 || $closing_gwt!=0 || $closing_diawt!=0)
    	    {
    	        $tag_details = [];
    	        if($report_type==2 && $val['tag_pcs']>0)
    	        {
    	            $tag_details = $this->get_po_tag_details($val['po_id'],$data['from_date'],$data['to_date'],$id_metal,$id_category,$data['po_ref_no']);
    	        }
    	        $return_data[] =   array(
    	                            'po_ref_no'     =>$val['po_ref_no'],
    	                            'podate'        =>$val['podate'],
    	                            'karigar_name'  =>$val['karigar_name'],
    	                            'blc_pcs'       =>($val['blc_pcs']),
    	                            'blc_gwt'       =>($val['blc_gwt']),
    	                            'blc_nwt'       =>($val['blc_nwt']),
    	                            'blc_diawt'     =>($val['blc_diawt']),
    	                            'inw_pcs'       =>($val['inw_pcs']),
    	                            'inw_gwt'       =>($val['inw_gwt']),
    	                            'inw_nwt'       =>($val['inw_nwt']),
    	                            'inw_diawt'     =>($val['inw_diawt']),
    	                            'pur_ret_pcs'   =>($val['pur_ret_pcs']),
    	                            'pur_ret_gwt'   =>($val['pur_ret_gwt']),
    	                            'pur_ret_nwt'   =>($val['pur_ret_nwt']),
    	                            'ret_diawt'     =>($val['ret_diawt']),
    	                            'lot_pcs'       =>($val['lot_pcs']),
    	                            'lot_gwt'       =>($val['lot_gwt']),
    	                            'lot_nwt'       =>($val['lot_nwt']),
    	                            'lot_dia_wt'    =>($val['lot_dia_wt']),
    	                            'tag_pcs'       =>($val['tag_pcs']),
    	                            'tag_gwt'       =>($val['tag_gwt']),
    	                            'tag_nwt'       =>($val['tag_nwt']),
    	                            'tag_dia_wt'    =>($val['tag_dia_wt']),
    	                            'issue_pcs'     =>($val['issue_pcs']),
    	                            'issue_wt'      =>($val['issue_wt']),
    	                            'issue_diawt'   =>($val['issue_diawt']),
    	                            'closing_pcs'   =>($closing_pcs),
    	                            'closing_gwt'   =>($closing_gwt),
    	                            'closing_nwt'   =>($closing_nwt),
    	                            'closing_diawt' =>($closing_diawt),
    	                            'tag_details'   =>$tag_details,
    	                            );
    	    }
    	}
    	//echo "<pre>";print_r($return_data);exit;
    	return $return_data;
    }
    function get_po_tag_details($po_id,$from_date,$to_date,$id_metal,$id_category,$po_ref_no)
    {
        $returnData = [];
        $sql = $this->db->query("SELECT t.tag_code,t.piece,t.gross_wt,t.less_wt,t.net_wt,IFNULL(stn.dia_wt,0) as dia_wt,l.lot_no
        FROM ret_taging t
        LEFT JOIN ret_lot_inwards l ON l.lot_no = t.tag_lot_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        LEFT JOIN (SELECT IFNULL(SUM(s.wt),0) as dia_wt,s.tag_id
                  FROM ret_taging_stone s
                  LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
                  WHERE st.stone_type = 1
                  GROUP BY s.tag_id) as stn ON stn.tag_id = t.tag_id
        WHERE l.po_id =".$po_id." and date(t.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and c.id_ret_category in (".$id_category.") " :'' )."
        ".($po_ref_no!='' && $po_ref_no !='0' ? " and l.po_id in (".$po_ref_no.") " :'' )."
        Order by l.lot_no ASC");
        $result = $sql->result_array();
        foreach($result as $val)
        {
            $returnData[$val['lot_no']][] = $val;
        }
        return $returnData;
    }
    function moneyFormatIndia($num)
	{
		return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
	}
    function get_lot_and_tag_wise_report($data)
    {
        $sql = $this->db->query("SELECT pr.product_name as product, kr.firstname as karigarname,  lotdet.lot_no as lotno, date_format(lot.lot_date, '%d-%m-%Y') as lotdate, lotdet.no_of_piece as lotpcs,
        lotdet.gross_wt as lotgrswt, lotdet.net_wt as lotnetwt, ifnull(podia.stwt,0) as lotdiawt, ifnull(podia.stpcs,0) as lotdiapcs,
        ifnull(postn.stwt,0) as lotstwt, ifnull(postn.stpcs,0) as lotstpcs, ifnull(lottag.taggrswt, 0) as taggrswt, ifnull(lottag.tagnetwt, 0) as tagnetwt,
        ifnull(lottag.tagdiawt,0) as tagdiawt, ifnull(lottag.tagdiapcs,0) as tagdiapcs, ifnull(lottag.tagstnwt,0) as tagstwt, ifnull(lottag.tagstpcs,0) as tagstpcs,
        (lotdet.no_of_piece-ifnull(lottag.tagstpcs,0)) as blc_pcs,(lotdet.gross_wt-ifnull(lottag.taggrswt, 0)) as blc_gwt,(lotdet.net_wt-ifnull(lottag.tagnetwt,0)) as blc_nwt
        FROM ret_lot_inwards_detail as lotdet
        LEFT JOIN ret_product_master as pr ON pr.pro_id = lotdet.lot_product
        LEFT JOIN ret_lot_inwards as lot ON lot.lot_no = lotdet.lot_no
        LEFT JOIN ret_karigar as kr ON kr.id_karigar = lot.gold_smith
        LEFT JOIN ret_purchase_order_items as poitm ON poitm.po_item_po_id = lot.po_id  AND poitm.po_item_cat_id = lot.id_category AND poitm.po_item_pro_id = lotdet.lot_product
        LEFT JOIN ret_purchase_order as po ON po.po_id = poitm.po_item_po_id
        LEFT JOIN (SELECT po_item_id, sum(po_stone_wt) as stwt, sum(po_stone_pcs) as stpcs, sum(po_stone_amount) as stamount
        FROM ret_po_stone_items as po
        LEFT JOIN ret_stone as st ON st.stone_id = po.po_stone_id
        WHERE st.stone_type = 1
        GROUP BY po.po_item_id) as podia ON podia.po_item_id = poitm.po_item_id
        LEFT JOIN (SELECT po_item_id, sum(po_stone_wt) as stwt, sum(po_stone_pcs) as stpcs, sum(po_stone_amount) as stamount
        FROM ret_po_stone_items as po
        LEFT JOIN ret_stone as st ON st.stone_id = po.po_stone_id
        WHERE st.stone_type != 1
        GROUP BY po.po_item_id) as postn ON postn.po_item_id = poitm.po_item_id
        LEFT JOIN(SELECT tag.id_lot_inward_detail as lotinwid, sum(tag.gross_wt) as taggrswt,
        sum(tag.net_wt) as tagnetwt,  ifnull(tagdia.stn_wt,0) as tagdiawt,
        ifnull(tagdia.stn_pcs,0) as tagdiapcs,
        ifnull(tagst.stn_wt,0) as tagstnwt, ifnull(tagst.stn_pcs,0) as tagstpcs
        FROM ret_taging as tag
        LEFT JOIN (SELECT ts.tag_id, ifnull(round(sum(ts.wt), 4),0) as stn_wt,
        ifnull(SUM(ts.pieces),0) as stn_pcs
        FROM ret_taging_stone as ts
        LEFT JOIN ret_stone as st ON st.stone_id = ts.stone_id
        WHERE st.stone_type = 1
        GROUP by ts.tag_id) tagdia on tagdia.tag_id = tag.tag_id
        LEFT JOIN (SELECT ts.tag_id, ifnull(round(sum(ts.wt), 4),0) as stn_wt,
        ifnull(SUM(ts.pieces),0) as stn_pcs
        FROM ret_taging_stone as ts
        LEFT JOIN ret_stone as st ON st.stone_id = ts.stone_id
        WHERE st.stone_type = 1
        GROUP by ts.tag_id) tagst on tagst.tag_id = tag.tag_id
        GROUP BY tag.id_lot_inward_detail) as lottag ON lottag.lotinwid = lotdet.id_lot_inward_detail
        WHERE lot.po_id IS NOT NULL AND lot.stock_type = 1
        GROUP BY lotdet.id_lot_inward_detail, lotdet.lot_product");
        $response_data = $sql->result_array();
    	foreach($response_data as $key => $val){
    	    $return_data[$val['lotno']][] =   $val;
    	}
    	foreach($return_data as $key => $grndata){
    	    foreach($grndata as $gkey => $gval){
            	if($gkey > 0){
            	    $return_data[$key][$gkey]["lotno"] = "";
            	}
        	}
    	}
    	echo "<pre>";print_r($return_data);exit;
    	return $return_data;
    }
    function get_qcIssuedDetails($data)
    {
        $qc_issue = array();
        $sql = $this->db->query("SELECT po_qc.qc_process_id,po.po_id,po.po_ref_no,qc_iss.id_qc_issue_details ,qc_iss.issue_pcs,qc_iss.issue_gwt,qc_iss.issue_lwt,qc_iss.issue_nwt,
        po_itm.po_item_id,pro.product_name,des.design_name,subdes.sub_design_name,
        kar.firstname as karigar,po_qc.qc_id_vendor
        FROM ret_po_qc_issue_details qc_iss
        LEFT JOIN ret_po_qc_issue_process po_qc on po_qc.qc_process_id = qc_iss.qc_process_id
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = qc_iss.po_item_id
        LEFT JOIN ret_purchase_order po on po.po_id = po_itm.po_item_po_id
        LEFT JOIN ret_karigar kar on kar.id_karigar = po.po_karigar_id
        LEFT JOIN ret_category cat on cat.id_ret_category = po_itm.po_item_cat_id
        LEFT JOIN ret_product_master pro on pro.pro_id = po_itm.po_item_pro_id
        LEFT JOIN ret_design_master des on des.design_no = po_itm.po_item_des_id
        LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = po_itm.po_item_sub_des_id
        WHERE qc_iss.qc_process_id=".$data['id_qc_issue']."");
       // print_r($this->db->last_query());exit;
        $data =  $sql->result_array();
        foreach($data as $r)
        {
            $qc_issue[]=array(
                'qc_process_id'        =>    $r['qc_process_id'],
                'po_id'                =>    $r['po_id'],
                'po_ref_no'            =>    $r['po_ref_no'],
                'id_emp'               =>    $r['qc_id_vendor'],
                'id_qc_issue_details'  =>    $r['id_qc_issue_details'],
                'issue_pcs'            =>    $r['issue_pcs'],
                'issue_gwt'            =>    $r['issue_gwt'],
                'issue_pcs'            =>    $r['issue_pcs'],
                'issue_lwt'            =>    $r['issue_lwt'],
                'issue_nwt'            =>    $r['issue_nwt'],
                'po_item_id'           =>    $r['po_item_id'],
                'product_name'         =>    $r['product_name'],
                'design_name'          =>    $r['design_name'],
                'sub_design_name'      =>    $r['sub_design_name'],
                'karigar'              =>    $r['karigar'],
                'stone_details'        =>    $this->get_qcIssueStoneDetails($r['id_qc_issue_details'],$r['po_item_id']),
                'blc_details'          =>    $this->getBalanceQcIssueDetails($r['po_item_id'],$r['qc_process_id']),
            );
        }
        return  $qc_issue;
    }
    function get_qcIssueStoneDetails($id_qc_issue_details,$po_item_id)
    {
        $sql = $this->db->query("SELECT iss_stn.ret_qc_issue_stn_id,po_stn.po_st_id,ifnull(iss_stn.stone_pcs,0) as stone_pcs,ifnull(iss_stn.stone_wt,0) as stone_wt,
        po_stn.po_stone_id,po_stn.is_apply_in_lwt,po_stn.po_stone_uom,ifnull(po_stn.po_stone_calc_based_on,'') as po_stone_calc_based_on,
        po_stn.po_stone_rate,po_stn.po_stone_amount,ifnull(po_stn.po_quality_id,'') as po_quality_id,
        (ifnull(po_stn.po_stone_pcs,0) - ifnull(qc_stn.stone_pcs,0)) as bal_stn_pcs,(ifnull(po_stn.po_stone_wt,0) - ifnull(qc_stn.stone_wt,0)) as bal_stn_wt
        from ret_po_stone_items po_stn
        LEFT JOIN ret_po_qc_issue_stone_details iss_stn on iss_stn.po_st_id = po_stn.po_st_id
        left join (select qc_iss.ret_qc_issue_stn_id,qc_iss.po_st_id,qc_iss.stone_pcs,qc_iss.stone_wt
        from ret_po_qc_issue_stone_details qc_iss
        where qc_iss.id_qc_issue_details !=".$id_qc_issue_details.") as qc_stn on qc_stn.po_st_id = po_stn.po_st_id
        where po_stn.po_item_id = ".$po_item_id." and iss_stn.id_qc_issue_details = ".$id_qc_issue_details."");
        return $sql->result_array();
    }
    function getBalanceQcIssueDetails($po_item_id,$qc_process_id)
    {
        $sql = $this->db->query("SELECT (IFNULL(r.no_of_pcs,0) - IFNULL(SUM(q.qcissued_pcs),0)) as blc_qcissued_pcs,(IFNULL(r.gross_wt,0) - IFNULL(SUM(q.qcissued_gwt),0)) as blc_qcissued_gwt,
        (IFNULL(r.net_wt,0) - IFNULL(SUM(q.qcissued_nwt),0)) as blc_qcissued_nwt
        FROM ret_purchase_order p
        LEFT JOIN ret_purchase_order_items r ON r.po_item_po_id=p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(qd.issue_pcs),0) as qcissued_pcs,
        IFNULL(SUM(qd.issue_gwt),0) as qcissued_gwt,IFNULL(SUM(qd.issue_nwt),0) as qcissued_nwt,
        qd.po_item_id
        FROM ret_po_qc_issue_details qd
        LEFT JOIN ret_po_qc_issue_process qc ON qc.qc_process_id = qd.qc_process_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = qd.po_item_id
        WHERE qd.qc_process_id!=".$qc_process_id."
        GROUP BY qd.po_item_id) as q ON q.po_item_id = r.po_item_id
        WHERE r.po_item_id = ".$po_item_id." ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_qcReceiptDetails($data)
    {
        $qc_receipt = array();
        $sql = $this->db->query("SELECT qc_rec.id_qc_issue_details,qc_rec.qc_process_id,qc_rec.po_item_id,
        qc_rec.failed_pcs,qc_rec.failed_gwt,qc_rec.failed_lwt,qc_rec.failed_nwt,
        qc_rec.passed_pcs,qc_rec.passed_gwt,qc_rec.passed_lwt,qc_rec.passed_nwt,
        qc_rec.issue_pcs,qc_rec.issue_gwt,qc_rec.issue_lwt,qc_rec.issue_nwt,
        pro.product_name,des.design_name,subdes.sub_design_name
        FROM ret_po_qc_issue_details qc_rec
        LEFT JOIN ret_po_qc_issue_process po_qc on po_qc.qc_process_id = qc_rec.qc_process_id
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = qc_rec.po_item_id
        LEFT JOIN ret_purchase_order po on po.po_id = po_itm.po_item_po_id
        LEFT JOIN ret_karigar kar on kar.id_karigar = po.po_karigar_id
        LEFT JOIN ret_category cat on cat.id_ret_category = po_itm.po_item_cat_id
        LEFT JOIN ret_product_master pro on pro.pro_id = po_itm.po_item_pro_id
        LEFT JOIN ret_design_master des on des.design_no = po_itm.po_item_des_id
        LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = po_itm.po_item_sub_des_id
        WHERE qc_rec.qc_process_id=".$data['id_qc_issue']." ");
        $data =  $sql->result_array();
        foreach($data as $r)
        {
            $qc_receipt[]=array(
                'id_qc_issue_details' =>     $r['id_qc_issue_details'],
                'qc_process_id'        =>    $r['qc_process_id'],
                'po_item_id'           =>    $r['po_item_id'],
                'failed_pcs'           =>    $r['failed_pcs'],
                'failed_gwt'           =>    $r['failed_gwt'],
                'failed_lwt'           =>    $r['failed_lwt'],
                'failed_nwt'           =>    $r['failed_nwt'],
                'passed_pcs'           =>    $r['passed_pcs'],
                'passed_gwt'           =>    $r['passed_gwt'],
                'passed_lwt'           =>    $r['passed_lwt'],
                'passed_nwt'           =>    $r['passed_nwt'],
                'issue_pcs'            =>    $r['issue_pcs'],
                'issue_gwt'            =>    $r['issue_gwt'],
                'issue_lwt'            =>    $r['issue_lwt'],
                'issue_nwt'            =>    $r['issue_nwt'],
                'po_item_id'           =>    $r['po_item_id'],
                'product_name'         =>    $r['product_name'],
                'design_name'          =>    $r['design_name'],
                'sub_design_name'      =>    $r['sub_design_name'],
                'stone_details'        =>    $this->get_qcReceiptStoneDetails($r['id_qc_issue_details']),
            );
        }
        return $qc_receipt;
    }
    function get_qcReceiptStoneDetails($id_qc_issue_details)
    {
        $sql = $this->db->query("SELECT s.ret_qc_issue_stn_id,t.id_stone_type,s.stone_pcs,s.stone_wt,po_stn.po_stone_uom,
	    stn.stone_name,qc_rejected_pcs as po_stone_rejected_pcs,qc_rejected_wt as po_stone_rejected_wt,s.stone_pcs as po_stone_accepted_pcs,s.stone_wt as po_stone_accepted_wt
        FROM ret_po_qc_issue_stone_details s
        LEFT JOIN ret_po_stone_items po_stn ON po_stn.po_st_id = s.po_st_id
        LEFT JOIN ret_stone stn ON stn.stone_id = po_stn.po_stone_id
        LEFT JOIN ret_stone_type t ON t.id_stone_type = stn.stone_type
        LEFT JOIN ret_uom m ON m.uom_id = stn.uom_id
        WHERE s.id_qc_issue_details = ".$id_qc_issue_details."");
        return $sql->result_array();
    }
    function get_weight_gain_loss_report($data)
	{
	    $returnData = [];
	    $op_date= date('Y-m-d',(strtotime('-1 day',strtotime($data['from_date']))));
        $sql = $this->db->query("SELECT l.lot_no,l.lot_date,
        (IFNULL(lot_blc.no_of_piece,0)-IFNULL(tag_blc.piece,0)) as blc_pcs,(IFNULL(lot_blc.gross_wt,0)-IFNULL(tag_blc.gross_wt,0)) as blc_gwt,
        (IFNULL(lot_blc_stn.stone_wt,0)-IFNULL(tag_blc_stn.stn_wt,0)) as blc_diawt,
        (IFNULL(lot_blc.net_wt,0)-IFNULL(tag_blc.net_wt,0)) as blc_nwt,
        IFNULL(lot.no_of_piece,0) as lot_pcs,IFNULL(lot.gross_wt,0) as lot_gwt,IFNULL(lot.net_wt,0) as lot_nwt,IFNULL(lot_stn.stone_wt,0) as lot_diawt,
        IFNULL(tag.piece,0) as tag_pcs,IFNULL(tag.gross_wt,0) as tag_gwt,IFNULL(tag.net_wt,0) as tag_nwt,IFNULL(tag_stn.stn_wt,0) as tag_diawt
        FROM ret_lot_inwards l
        LEFT JOIN (SELECT IFNULL(SUM(d.no_of_piece),0) as no_of_piece,IFNULL(SUM(d.gross_wt),0) as gross_wt,
            IFNULL(SUM(d.net_wt),0) as net_wt,d.lot_no
            FROM ret_lot_inwards_detail d
            LEFT JOIN ret_lot_inwards l ON l.lot_no = d.lot_no
            where date(l.lot_date) <= '".date('Y-m-d',strtotime($op_date))."'
            GROUP BY l.lot_no) as lot_blc ON lot_blc.lot_no = l.lot_no
        LEFT JOIN (SELECT IFNULL(SUM(d.stone_wt),0) as stone_wt,l.lot_no
            FROM ret_lot_inwards_stone_detail d
            LEFT JOIN ret_lot_inwards_detail ls ON ls.id_lot_inward_detail = d.id_lot_inward_detail
            LEFT JOIN ret_lot_inwards l ON l.lot_no = ls.lot_no
            LEFT JOIN ret_stone stn ON stn.stone_id = d.stone_id
            where date(l.lot_date) <= '".date('Y-m-d',strtotime($op_date))."' and stn.stone_type = 1
            GROUP BY l.lot_no) as lot_blc_stn ON lot_blc_stn.lot_no = l.lot_no
        LEFT JOIN (SELECT IFNULL(SUM(t.piece),0) as piece,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt,
            t.tag_lot_id
            FROM ret_taging t
            LEFT JOIN ret_lot_inwards lt on lt.lot_no = t.tag_lot_id
            where date(t.tag_datetime)<= '".date('Y-m-d',strtotime($op_date))."'
            GROUP BY t.tag_lot_id) as tag_blc ON tag_blc.tag_lot_id = l.lot_no
        LEFT JOIN (SELECT IFNULL(SUM(st.wt),0) as stn_wt,t.tag_lot_id
            FROM ret_taging_stone st
            LEFT JOIN ret_stone s ON s.stone_id = st.stone_id
            LEFT JOIN ret_taging t ON t.tag_id = st.tag_id
            LEFT JOIN ret_lot_inwards lt on lt.lot_no = t.tag_lot_id
            where date(t.tag_datetime)<= '".date('Y-m-d',strtotime($op_date))."' and s.stone_type = 1
            GROUP BY t.tag_lot_id) as tag_blc_stn ON tag_blc_stn.tag_lot_id = l.lot_no
        LEFT JOIN (SELECT IFNULL(SUM(d.no_of_piece),0) as no_of_piece,IFNULL(SUM(d.gross_wt),0) as gross_wt,
            IFNULL(SUM(d.net_wt),0) as net_wt,d.lot_no
            FROM ret_lot_inwards_detail d
            LEFT JOIN ret_lot_inwards l ON l.lot_no = d.lot_no
            where date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
            GROUP BY l.lot_no) as lot ON lot.lot_no = l.lot_no
        LEFT JOIN (SELECT IFNULL(SUM(d.stone_wt),0) as stone_wt,l.lot_no
            FROM ret_lot_inwards_stone_detail d
            LEFT JOIN ret_lot_inwards_detail ls ON ls.id_lot_inward_detail = d.id_lot_inward_detail
            LEFT JOIN ret_lot_inwards l ON l.lot_no = ls.lot_no
            LEFT JOIN ret_stone stn ON stn.stone_id = d.stone_id
            where date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."' and stn.stone_type = 1
            GROUP BY l.lot_no) as lot_stn ON lot_stn.lot_no = l.lot_no
        LEFT JOIN (SELECT IFNULL(SUM(t.piece),0) as piece,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt,
            t.tag_lot_id
            FROM ret_taging t
            LEFT JOIN ret_lot_inwards lt on lt.lot_no = t.tag_lot_id
            where date(t.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
            GROUP BY t.tag_lot_id) as tag ON tag.tag_lot_id = l.lot_no
        LEFT JOIN (SELECT IFNULL(SUM(st.wt),0) as stn_wt,t.tag_lot_id
            FROM ret_taging_stone st
            LEFT JOIN ret_stone s ON s.stone_id = st.stone_id
            LEFT JOIN ret_taging t ON t.tag_id = st.tag_id
            LEFT JOIN ret_lot_inwards lt on lt.lot_no = t.tag_lot_id
            where date(t.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."' and s.stone_type = 1
            GROUP BY t.tag_lot_id) as tag_stn ON tag_stn.tag_lot_id = l.lot_no
        where l.lot_no IS NOT NULL and l.is_closed = 1 ORDER BY l.lot_no DESC");
        //print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        foreach($result as $val){
            $gl_pcs = number_format(($val['blc_pcs']+$val['lot_pcs']-$val['tag_pcs']),0,'.','');
            $gl_gwt = number_format(($val['blc_gwt']+$val['lot_gwt']-$val['tag_gwt']),3,'.','');
            $gl_nwt = number_format(($val['blc_nwt']+$val['lot_nwt']-$val['tag_nwt']),3,'.','');
            $gl_diawt = number_format(($val['blc_diawt']+$val['lot_diawt']-$val['tag_diawt']),3,'.','');
            if($gl_pcs > 0 || $val['lot_pcs'] > 0 || $val['tag_pcs'] > 0)
            {
                $returnData[]=array(
                                'lot_no'        =>$val['lot_no'],
                                'lot_date'      =>$val['lot_date'],
                                'blc_pcs'       =>$val['blc_pcs'],
                                'blc_gwt'       =>$val['blc_gwt'],
                                'blc_nwt'       =>$val['blc_nwt'],
                                'blc_diawt'     =>$val['blc_diawt'],
                                'lot_pcs'       =>$val['lot_pcs'],
                                'lot_gwt'       =>$val['lot_gwt'],
                                'lot_nwt'       =>$val['lot_nwt'],
                                'lot_diawt'     =>$val['lot_diawt'],
                                'tag_pcs'       =>$val['tag_pcs'],
                                'tag_gwt'       =>$val['tag_gwt'],
                                'tag_nwt'       =>$val['tag_nwt'],
                                'tag_diawt'     =>$val['tag_diawt'],
                                'gl_pcs'        =>$gl_pcs,
                                'gl_gwt'        =>$gl_gwt,
                                'gl_nwt'        =>$gl_nwt,
                                'gl_diawt'      =>$gl_diawt,
                                );
            }
        }
        return $returnData;
	}
    function get_karigar_SupplierEntrys($data)
    {
        /*$sql = $this->db->query("SELECT pur_ord.po_id,pur_ord.po_ref_no,pur_ord.po_type,ifnull(kar_iss.issue_metal_wt,0) as metalissuewt,
        (ifnull(qc_iss.passed_pcs,0) - ifnull(kar_iss.issue_pcs,0)) as no_of_piece,
        (ifnull(qc_iss.passed_nwt,0) - ifnull(kar_iss.issue_metal_wt,0)) as net_wt,po_itm.po_item_id
        FROM ret_purchase_order_items po_itm
        LEFT JOIN ret_product_master pro on pro.pro_id = po_itm.po_item_pro_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        LEFT JOIN (SELECT po.po_type,po.is_approved,po.po_id,po.po_ref_no from  ret_purchase_order po
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_po_id = po.po_id
        GROUP BY po.po_id) as pur_ord on pur_ord.po_id = po_itm.po_item_po_id
        LEFT JOIN (SELECT qc.po_item_id,ifnull(sum(qc.passed_pcs),0) as passed_pcs,
        ifnull(sum(qc.passed_nwt),0) as passed_nwt ,po_itm.po_item_po_id
        FROM ret_po_qc_issue_details qc
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = qc.po_item_id
        WHERE qc.is_lot_created =0 and qc.status = 1
        GROUP BY po_itm.po_item_po_id) as qc_iss on qc_iss.po_item_po_id = po_itm.po_item_po_id
        LEFT JOIN (SELECT km.po_id,kmd.po_item_id,
        ifnull(sum(kmd.issue_pcs),0) as issue_pcs,ifnull(sum(kmd.issue_metal_wt),0) as issue_metal_wt,po_itm.po_item_po_id
        FROM ret_karigar_metal_issue_details kmd
        LEFT JOIN ret_karigar_metal_issue km on km.met_issue_id = kmd.issue_met_parent_id
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = kmd.po_item_id
        where km.issue_against_po = 1 and km.bill_status=1
        GROUP BY po_itm.po_item_po_id) as kar_iss on kar_iss.po_item_po_id = po_itm.po_item_po_id
        WHERE (pur_ord.po_type = 3 or pur_ord.po_type = 2) and pur_ord.is_approved=1
        ".($data['id_metal']!='' && $data['id_metal']>0 ? "and cat.id_metal=".$data['id_metal']."":"")."
        GROUP BY pur_ord.po_id
        HAVING net_wt > 0");*/
        $sql = $this->db->query("SELECT p.po_id,p.po_ref_no,
        (IFNULL(SUM(d.passed_pcs),0)-IFNULL(lot.no_of_piece,0)-IFNULL(metalIssue.issue_pcs,0)) as blc_pcs,
        (IFNULL(SUM(d.passed_nwt),0)-IFNULL(metalIssue.issue_wt,0)-IFNULL(lot.net_wt,0)) as net_wt
        FROM ret_po_qc_issue_details d
        LEFT JOIN ret_po_qc_issue_process q ON q.qc_process_id = d.qc_process_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id
        LEFT JOIN (SELECT IFNULL(SUM(ld.no_of_piece),0) as no_of_piece,IFNULL(SUM(ld.net_wt),0) as net_wt,l.po_id
                  FROM ret_lot_inwards l
                  LEFT JOIN ret_lot_inwards_detail ld ON ld.lot_no = l.lot_no
                  GROUP BY l.po_id) as lot ON lot.po_id = p.po_id
        LEFT JOIN (SELECT IFNULL(SUM(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt,p.po_id
            FROM ret_karigar_metal_issue_details d
            LEFT JOIN ret_karigar_metal_issue i ON i.met_issue_id = d.issue_met_parent_id
            LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = d.po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = itm.po_item_po_id
            WHERE i.bill_status = 1
        GROUP BY p.po_id) as metalIssue ON metalIssue.po_id = p.po_id
        WHERE d.status = 1 AND r.is_halmarked = 1 and p.is_approved=1 and (p.po_type = 3 or p.po_type = 2) and d.is_lot_created = 0
        and p.bill_status = 1
        GROUP BY r.po_item_po_id
        having net_wt > 0;");
        return $sql->result_array();
    }
    function get_Available_SupplierPo($data)
    {
        $po_data = array();
        $multiple_po_id = implode(' , ', $data['po_id']);
        if($multiple_po_id != '')
		{
			$po_id = $multiple_po_id;
		}else{
			$po_id = $data['po_id'];
		}
        $sql = $this->db->query("SELECT c.id_metal,po_itm.po_item_cat_id as cat_id,po_itm.po_item_pro_id as id_product,
        po_itm.po_item_des_id as design,po_itm.po_item_sub_des_id as id_sub_design,
        c.name as category_name,p.product_name,
        (ifnull(SUM(d.passed_pcs),0) - ifnull(kar_iss.issue_pcs,0)-ifnull(lot.lot_pcs,0)) as no_of_piece,
        (ifnull(SUM(d.passed_nwt),0) - ifnull(kar_iss.issue_metal_wt,0)-ifnull(lot.lot_nwt,0)) as net_wt,
        '' as section_name,'' as id_section,po_itm.po_item_id,po_itm.id_purity,
        ifnull(pur.purity,'') as purname,po_itm.uom,po_itm.po_item_id,ifnull(pur_ord.po_ref_no,'') as po_ref_no,ifnull(pur_ord.po_date,'') as po_date,
        ifnull(pur_ord.karigar,'') as karigar
        FROM ret_po_qc_issue_details d
        LEFT JOIN ret_purchase_order_items po_itm ON po_itm.po_item_id = d.po_item_id
        LEFT JOIN ret_category c on c.id_ret_category = po_itm.po_item_cat_id
        LEFT JOIN ret_product_master p on p.pro_id = po_itm.po_item_pro_id
        LEFT JOIN ret_purity pur on pur.id_purity = po_itm.id_purity
        LEFT JOIN (SELECT po.po_id,po.po_ref_no,ifnull(concat(kar.firstname,'-',kar.code_karigar),'') as karigar,
        date_format(po.po_date,'%d-%m-%Y') as po_date,po.fin_year_code
        from  ret_purchase_order po
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_po_id = po.po_id
        LEFT JOIN ret_karigar kar on kar.id_karigar = po.po_karigar_id
        GROUP BY po.po_id) as pur_ord on pur_ord.po_id = po_itm.po_item_po_id
        LEFT JOIN (SELECT IFNULL(SUM(d.no_of_piece),0) as lot_pcs,IFNULL(SUM(d.gross_wt),0) as lot_gwt,IFNULL(SUM(d.net_wt),0) as lot_nwt,
        r.po_item_id
        FROM ret_lot_inwards_detail d
        LEFT JOIN ret_lot_inwards l ON l.lot_no = d.lot_no
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.po_item_id
        group by r.po_item_id) as lot ON lot.po_item_id = po_itm.po_item_id
        LEFT JOIN (SELECT km.po_id,kmd.po_item_id,
        ifnull(sum(kmd.issue_pcs),0) as issue_pcs,ifnull(sum(kmd.issue_metal_wt),0) as issue_metal_wt
        FROM ret_karigar_metal_issue_details kmd
        LEFT JOIN ret_karigar_metal_issue km on km.met_issue_id = kmd.issue_met_parent_id
        where km.issue_against_po = 1 and km.bill_status=1
        GROUP BY kmd.po_item_id) as kar_iss on kar_iss.po_item_id = po_itm.po_item_id
        WHERE ".($po_id!='' ? "po_itm.po_item_po_id in (".$po_id.")":"")."
        ".($data['fin_year']!='' ? "and pur_ord.fin_year_code=".$data['fin_year']."" : "")."
        GROUP BY po_itm.po_item_id
        HAVING net_wt > 0");
        //print_r($this->db->last_query());exit;
        $data =  $sql->result_array();
        foreach($data as $r)
        {
            $po_data[] = array(
                'po_item_id'    =>  $r['po_item_id'],
                'no_of_piece'    => $r['no_of_piece'],
                'net_wt'         => $r['net_wt'],
                'id_metal'       => $r['id_metal'],
                'cat_id'         => $r['cat_id'],
                'id_product'     => $r['id_product'],
                'id_section'     => $r['id_section'],
                'design'         => $r['design'],
                'id_sub_design'  => $r['id_sub_design'],
                'category_name'  => $r['category_name'],
                'product_name'   => $r['product_name'],
                'section_name'   => $r['section_name'],
                'id_purity'      => $r['id_purity'],
                'purname'        => $r['purname'],
                'uom'            =>  $r['uom'],
                'po_ref_no'      =>  $r['po_ref_no'],
                'po_date'        =>  $r['po_date'],
                'karigar'        => $r['karigar'],
                'purity'         => $this->getCatPurity($r['cat_id']),
            );
        }
        return $po_data;
    }
    function getKarigarIssueRefNo($data)
    {
        $sql = $this->db->query("SELECT kmi.met_issue_id,kmi.met_issue_ref_id,k.firstname as suppliername
        FROM ret_karigar_metal_issue kmi
        LEFT JOIN ret_karigar k ON k.id_karigar = kmi.met_issue_karid
        WHERE kmi.issue_against_po=1 and kmi.bill_status=1
        ".($data['id_karigar']!="" ? "and kmi.met_issue_karid=".$data['id_karigar']."":"")."");
        return $sql->result_array();
    }
    function getKarigarMetalIssueLooseStones($data)
    {
        $sql = $this->db->query("SELECT km.met_issue_id,km.met_issue_ref_id,kmi.issue_met_id,
        (sum(kmi.issue_pcs) - ifnull(po_stone.po_stone_pcs,0) - ifnull(pur_itm.po_itm_pcs,0)) as stone_pcs,
        (sum(kmi.issue_metal_wt) - ifnull(po_stone.po_stone_wt,0) - ifnull(pur_itm.po_itm_wt,0)) as stone_wt,kmi.issue_cat_id as cat_id,kmi.issu_met_pro_id as id_product,kmi.issu_met_id_design as id_design,kmi.issu_met_id_sub_design as id_sub_design,
        cat.name as category,pro.product_name,des.design_name,subdes.sub_design_name,pur.purity,pur.id_purity,
        pro.purchase_mode,kmi.issue_uom_id as stone_uom_id
        FROM ret_karigar_metal_issue km
        LEFT JOIN ret_karigar_metal_issue_details kmi on kmi.issue_met_parent_id = km.met_issue_id
        LEFT JOIN ret_product_master pro on pro.pro_id = kmi.issu_met_pro_id
        LEFT JOIN ret_category cat on cat.id_ret_category = kmi.issue_cat_id
        LEFT JOIN ret_design_master des on des.design_no = kmi.issu_met_id_design
        LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = kmi.issu_met_id_sub_design
        LEFT JOIN ret_purity pur on pur.id_purity = kmi.issue_pur_id
        LEFT JOIN (select po_stn.issue_met_id,
        ifnull(sum(po_stn.po_stone_pcs),0) as po_stone_pcs,ifnull(sum(po_stn.po_stone_wt),0) as po_stone_wt
        FROM ret_po_stone_items po_stn
        GROUP BY po_stn.issue_met_id) as po_stone on po_stone.issue_met_id = kmi.issue_met_id
        LEFT JOIN (select po_itm.issue_met_id,
        ifnull(sum(po_itm.no_of_pcs),0) as po_itm_pcs,ifnull(sum(po_itm.net_wt),0) as po_itm_wt
        FROM ret_purchase_order_items po_itm
        GROUP BY po_itm.issue_met_id) as pur_itm on pur_itm.issue_met_id = kmi.issue_met_id
        WHERE km.met_issue_id=".$data['metal_issue_id']." and km.bill_status=1
        GROUP BY kmi.issue_met_id
        HAVING stone_wt > 0");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    //Approval Rate Unfxied details
    function get_approval_rate_unfixed_details($data)
    {
        $sql = $this->db->query("SELECT p.po_id,p.po_ref_no,IFNULL(SUM(r.gross_wt),0) as gross_wt,IFNULL(SUM(r.net_wt),0) as net_wt,IFNULL(SUM(r.item_pure_wt),0) as pure_wt,date_format(g.grn_date,'%d-%m-%Y') as grndate,g.grn_ref_no,
        c.name as category_name,k.firstname as supplier_name,IFNULL(stn.stone_wt,0) as stone_wt,IFNULL(r.item_cost,0) as grn_item_cost,IFNULL(r.total_tax,0) as grn_item_gst_rate,
        (IFNULL(r.item_cost,0) - IFNULL(r.total_tax,0)) as taxable_amount,IFNULL(stnd.stnamount,0) as stnamount,r.fix_rate_per_grm as unfixrate,
        0 as ratefixwt, p.tot_purchase_wt as balance_weight, p.tot_purchase_wt, UNIX_TIMESTAMP(g.grn_date) as createdon, 0 as fixrate, '' as crdr,IFNULL(ret.pur_ret_pur_wt,0) as pur_ret_pur_wt
        FROM ret_purchase_order p
        LEFT JOIN ret_grn_entry g ON g.grn_id = p.po_grn_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(grn.grn_item_cost),0) as grn_item_cost,IFNULL(SUM(grn.grn_item_gst_rate),0) as grn_item_gst_rate,grn.grn_item_grn_id,grn.grn_item_cat_id
                 FROM ret_grn_items grn
                 GROUP BY grn.grn_item_grn_id,grn.grn_item_cat_id) as grnitem ON grnitem.grn_item_grn_id = g.grn_id AND grnitem.grn_item_cat_id = r.po_item_cat_id
        LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
        LEFT JOIN ret_category c ON c.id_ret_category = r.po_item_cat_id
        LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_wt),0) as stone_wt,s.po_item_id
            FROM ret_po_stone_items s
            LEFT JOIN ret_purchase_order_items i ON i.po_item_id = s.po_item_id
            LEFT JOIN ret_stone st ON st.stone_id = s.po_stone_id
            LEFT JOIN ret_stone_type t ON t.id_stone_type = st.stone_type
            WHERE st.stone_type = 1
        GROUP BY s.po_item_id) as stn ON stn.po_item_id = r.po_item_id
        LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_amount),0) as stnamount,s.po_item_id
            FROM ret_po_stone_items s
            LEFT JOIN ret_purchase_order_items i ON i.po_item_id = s.po_item_id
            LEFT JOIN ret_stone st ON st.stone_id = s.po_stone_id
            LEFT JOIN ret_stone_type t ON t.id_stone_type = st.stone_type
            WHERE st.stone_type = 1
        GROUP BY s.po_item_id) as stnd ON stnd.po_item_id = r.po_item_id
        LEFT JOIN ret_po_rate_fix as rfp ON rfp.rate_fix_po_item_id = p.po_id
		LEFT JOIN(SELECT pitm.po_item_po_id,IFNULL(SUM(itm.pur_ret_gwt),0) as ret_gwt,IFNULL(SUM(itm.pur_ret_pur_wt),0) as pur_ret_pur_wt
    		FROM ret_purchase_return r
    		LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
    		LEFT JOIN ret_purchase_order_items pitm ON pitm.po_item_id = itm.pur_ret_po_item_id
    		WHERE r.bill_status = 1
		GROUP BY pitm.po_item_po_id) as ret ON ret.po_item_po_id = p.po_id
        WHERE p.isratefixed = 0 AND rfp.rate_fix_id IS NULL and p.is_suspense_stock = 1 and p.is_approved = 1
        ".($data['cat_id']!='' && $data['cat_id']>0 ? "AND r.po_item_cat_id=".$data['cat_id']."":'')."
        ".($data['id_karigar']!='' && $data['id_karigar']>0 ? "AND p.po_karigar_id=".$data['id_karigar']."":'')."
        GROUP BY p.po_id
        UNION
        SELECT p.po_id,p.po_ref_no,IFNULL(SUM(r.gross_wt),0) as gross_wt,IFNULL(SUM(r.net_wt),0) as net_wt,IFNULL(SUM(r.item_pure_wt),0) as pure_wt,date_format(g.grn_date,'%d-%m-%Y') as grndate,g.grn_ref_no,
        c.name as category_name,k.firstname as supplier_name,IFNULL(stn.stone_wt,0) as stone_wt,IFNULL(r.item_cost,0) as grn_item_cost,IFNULL(r.total_tax,0) as grn_item_gst_rate,
        (IFNULL(r.item_cost,0) - IFNULL(r.total_tax,0)) as taxable_amount,IFNULL(stnd.stnamount,0) as stnamount, r.fix_rate_per_grm as unfixrate,
        IFNULL(rfx.rate_fix_wt,0) as ratefixwt,(p.tot_purchase_wt - IFNULL(ratefix.ratefixwt,0)-IFNULL(ret.pur_ret_pur_wt,0)) as balance_weight,p.tot_purchase_wt,
        UNIX_TIMESTAMP(g.grn_date) as createdon, rfx.rate_fix_rate as fixrate, concat(round(abs(((r.fix_rate_per_grm - rfx.rate_fix_rate) * rfx.rate_fix_wt) * 1.03),2), if(r.fix_rate_per_grm > rfx.rate_fix_rate , 'Dr', 'Cr')) as crdr,
		IFNULL(ret.pur_ret_pur_wt,0) as pur_ret_pur_wt
        FROM ret_po_rate_fix as rfx
        LEFT JOIN ret_purchase_order p ON rfx.rate_fix_po_item_id = p.po_id
        LEFT JOIN ret_grn_entry g ON g.grn_id = p.po_grn_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_po_id = p.po_id
        LEFT JOIN(SELECT IFNULL(SUM(grn.grn_item_cost),0) as grn_item_cost,IFNULL(SUM(grn.grn_item_gst_rate),0) as grn_item_gst_rate,grn.grn_item_grn_id,grn.grn_item_cat_id
                 FROM ret_grn_items grn
                 GROUP BY grn.grn_item_grn_id,grn.grn_item_cat_id) as grnitem ON grnitem.grn_item_grn_id = g.grn_id AND grnitem.grn_item_cat_id = r.po_item_cat_id
        LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
        LEFT JOIN ret_category c ON c.id_ret_category = r.po_item_cat_id
        LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_wt),0) as stone_wt,s.po_item_id
            FROM ret_po_stone_items s
            LEFT JOIN ret_purchase_order_items i ON i.po_item_id = s.po_item_id
            LEFT JOIN ret_stone st ON st.stone_id = s.po_stone_id
            LEFT JOIN ret_stone_type t ON t.id_stone_type = st.stone_type
            WHERE st.stone_type = 1
        GROUP BY s.po_item_id) as stn ON stn.po_item_id = r.po_item_id
        LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_amount),0) as stnamount,s.po_item_id
            FROM ret_po_stone_items s
            LEFT JOIN ret_purchase_order_items i ON i.po_item_id = s.po_item_id
            LEFT JOIN ret_stone st ON st.stone_id = s.po_stone_id
            LEFT JOIN ret_stone_type t ON t.id_stone_type = st.stone_type
            WHERE st.stone_type = 1
        GROUP BY s.po_item_id) as stnd ON stnd.po_item_id = r.po_item_id
        LEFT JOIN(SELECT IFNULL(SUM(rfx.rate_fix_wt),0) as ratefixwt,rfx.rate_fix_po_item_id
                 FROM ret_po_rate_fix rfx
                 GROUP BY rfx.rate_fix_po_item_id) as ratefix ON ratefix.rate_fix_po_item_id = p.po_id
        LEFT JOIN(SELECT pitm.po_item_po_id,IFNULL(SUM(itm.pur_ret_gwt),0) as ret_gwt,IFNULL(SUM(itm.pur_ret_pur_wt),0) as pur_ret_pur_wt
    		FROM ret_purchase_return r
    		LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
    		LEFT JOIN ret_purchase_order_items pitm ON pitm.po_item_id = itm.pur_ret_po_item_id
    		WHERE r.bill_status = 1
		GROUP BY pitm.po_item_po_id) as ret ON ret.po_item_po_id = p.po_id
        WHERE p.isratefixed = 0 and p.is_suspense_stock = 1 and p.is_approved = 1 and rate_fix_type = 2
        ".($data['cat_id']!='' && $data['cat_id']>0 ? "and r.po_item_cat_id=".$data['cat_id']."":'')."
        ".($data['id_karigar']!='' && $data['id_karigar']>0 ? "and p.po_karigar_id=".$data['id_karigar']."":'')."
        GROUP BY rfx.rate_fix_id  HAVING balance_weight > 0
        ORDER BY po_id DESC
        ");
        //ORDER BY rate_fix_id ASC
        //ORDER BY po_id, createdon ASC
		//echo $this->db->last_query();exit;
        $result = $sql->result_array();
        //echo $this->db->last_query();exit;
        foreach($result as $key => $val){
            $return_data[$val['grn_ref_no']][] =   $val;
        }
        foreach($return_data as $key => $val){
            foreach($val as $gkey => $gval){
                if($gkey > 0){
                    $return_data[$key][$gkey]["po_ref_no"] = "";
                    $return_data[$key][$gkey]["grn_ref_no"] = "";
                    $return_data[$key][$gkey]["supplier_name"] = "";
                    $return_data[$key][$gkey]["grndate"] = "";
                    $return_data[$key][$gkey]["category_name"] = "";
                    $return_data[$key][$gkey]["supplier_name"] = "";
                    $return_data[$key][$gkey]["pure_wt"] = "";
                    $return_data[$key][$gkey]["gross_wt"] = "";
                    $return_data[$key][$gkey]["net_wt"] = "";
                    $return_data[$key][$gkey]["stone_wt"] = "";
                    $return_data[$key][$gkey]["grn_item_cost"] = "";
                    $return_data[$key][$gkey]["grn_item_gst_rate"] = "";
                    $return_data[$key][$gkey]["taxable_amount"] = "";
                }
            }
        }
        //echo "<pre>";print_r($return_data);exit;
        return $return_data;
    }
    //Approval Rate Unfxied details
    //Approval Rate fixed details
    function get_approval_rate_fixed_details($data)
	{
	    $return_data = [];
	    $sql = $this->db->query("SELECT p.po_id,p.po_ref_no,k.firstname as suppliername,r.rate_fix_wt,r.rate_fix_rate,r.total_amount as fixed_amount,date_format(r.rate_fix_created_on,'%d-%m-%Y') as ratefixeddate,
	    g.grn_ref_no,date_format(g.grn_date,'%d-%m-%Y') as grn_date,p.tot_purchase_wt,g.grn_purchase_amt
        FROM ret_po_rate_fix r
        LEFT JOIN ret_purchase_order p ON p.po_id = r.rate_fix_po_item_id
        LEFT JOIN ret_grn_entry g ON g.grn_id = p.po_grn_id
        LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
        Where g.grn_ref_no IS NOT NULL and p.is_suspense_stock = 1 and r.rate_fix_type = 2
        ".($data['from_date'] != '' && $data['to_date']!='' ? ' and date(g.grn_date) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'"' : '')."
        ".($data['id_karigar']!='' && $data['id_karigar']>0 ? "and p.po_karigar_id=".$data['id_karigar']." ":'')."
        Order by p.po_id DESC");
        $result = $sql->result_array();
        foreach($result as $key => $val){
            $return_data[$val['grn_ref_no']][] =   $val;
        }
         foreach($return_data as $key => $val){
                foreach($val as $gkey => $gval){
                    if($gkey > 0){
                        $return_data[$key][$gkey]["po_ref_no"] = "";
                        $return_data[$key][$gkey]["suppliername"] = "";
                        $return_data[$key][$gkey]["grn_ref_no"] = "";
                        $return_data[$key][$gkey]["grn_date"] = "";
                        $return_data[$key][$gkey]["grn_purchase_amt"] = "";
                    }
                }
            }
        //echo "<pre>";print_r($return_data);exit;
        return $return_data;
	}
    //Approval Rate fixed details
    function get_smith_cmpy_op_bal_details($data)
    {
        $sql = $this->db->query("SELECT scop.id_smith_company_op_balance,if(scop.stock_type=1,'Company stock',' Smith stock') as stock_tye,
        if(scop.balance_type=1,'Metal','Diamond/stone') as bal_type,mt.metal,cat.name as category,pro.product_name,ifnull(kar.firstname,'') as karigar,ifnull(scop.pieces,0) as op_pcs,ifnull(scop.weight,0) as op_wgt,ifnull(uom.uom_short_code,'gm') as uom_short_code,
        ifnull(scop.amount,0) as op_amt,ifnull(scop.weight_type,'') as weight_type,ifnull(scop.amount_type,'') as amount_type,
        if(scop.smith_type=1,'Supplier',if(scop.smith_type=2,'Smith',if(scop.smith_type=3,'Approval Supplier',if(scop.smith_type=4,'Stone Supplier','')))) as smith_type,
        ifnull(scop.remarks,'') as remarks,scop.ref_no
        from smith_company_op_balance scop
        LEFT JOIN metal mt on mt.id_metal = scop.id_metal
        LEFT JOIN ret_category cat on cat.id_ret_category = scop.id_category
        LEFT JOIN ret_product_master pro on pro.pro_id = scop.id_product
        LEFT JOIN ret_karigar kar on kar.id_karigar = scop.id_karigar
        LEFT JOIN ret_uom uom on uom.uom_id = scop.uom_id
        WHERE
        ".($data['from_date']!='' && $data['to_date']!='' ? " (date(scop.createdon) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."ORDER BY scop.id_smith_company_op_balance DESC");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }

    /**
     * Get a single smith/company opening balance by ID for editing
     * @param int $id
     * @return array|null
     */
    function get_op_bal_by_id($id)
    {
        $this->db->select('scop.*, mt.metal, cat.name as category_name, pro.product_name,
            kar.firstname as karigar_name, uom.uom_short_code,
            date_format(scop.createdon, "%d-%m-%Y") as formatted_date');
        $this->db->from('smith_company_op_balance scop');
        $this->db->join('metal mt', 'mt.id_metal = scop.id_metal', 'left');
        $this->db->join('ret_category cat', 'cat.id_ret_category = scop.id_category', 'left');
        $this->db->join('ret_product_master pro', 'pro.pro_id = scop.id_product', 'left');
        $this->db->join('ret_karigar kar', 'kar.id_karigar = scop.id_karigar', 'left');
        $this->db->join('ret_uom uom', 'uom.uom_id = scop.uom_id', 'left');
        $this->db->where('scop.id_smith_company_op_balance', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Update an existing smith/company opening balance record
     * @param int $id PK
     * @param array $data Fields to update
     * @return bool
     */
    function update_op_bal($id, $data)
    {
        $this->db->where('id_smith_company_op_balance', $id);
        return $this->db->update('smith_company_op_balance', $data);
    }

    /**
     * Soft delete a smith/company opening balance record
     * @param int $id PK
     * @return bool
     */
    function delete_op_bal($id)
    {
        // Hard delete since there's no status column on legacy table
        $this->db->where('id_smith_company_op_balance', $id);
        return $this->db->delete('smith_company_op_balance');
    }

    function get_crdr_detail($rate_fix_id)
    {

         $sql = $this->db->query("SELECT p.po_id,p.po_ref_no,IFNULL(SUM(r.gross_wt),0) as gross_wt,IFNULL(SUM(r.net_wt),0) as net_wt,IFNULL(SUM(r.item_pure_wt),0) as pure_wt,date_format(g.grn_date,'%d-%m-%Y') as grndate,g.grn_ref_no,
         c.name as category_name,k.firstname as supplier_name,IFNULL(stn.stone_wt,0) as stone_wt,IFNULL(r.item_cost,0) as grn_item_cost,IFNULL(r.total_tax,0) as grn_item_gst_rate,
         (IFNULL(r.item_cost,0) - IFNULL(r.total_tax,0)) as taxable_amount,IFNULL(stnd.stnamount,0) as stnamount, r.fix_rate_per_grm as unfixrate,
         IFNULL(rfx.rate_fix_wt,0) as ratefixwt,(p.tot_purchase_wt - IFNULL(ratefix.ratefixwt,0)) as balance_weight,p.tot_purchase_wt,
         UNIX_TIMESTAMP(g.grn_date) as createdon, rfx.rate_fix_rate as fixrate, concat(round(abs(((r.fix_rate_per_grm - rfx.rate_fix_rate) * rfx.rate_fix_wt) * 1.03),2), if(r.fix_rate_per_grm > rfx.rate_fix_rate , 'Dr', 'Cr')) as crdr,
         '0' as pur_ret_pur_wt,rfx.rate_fix_id, if(r.fix_rate_per_grm > rfx.rate_fix_rate , 'Dr', 'Cr') as crdrtype,
         k.contactno1 as mobile,IFNULL(k.address1,'') as address1,
        IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,
        IFNULL(cy.name,'') as city_name,IFNULL(ct.name,'') as country_name,IFNULL(st.name,'') as state_name,
        IFNULL(k.pincode,'') as pincode
         FROM ret_po_rate_fix as rfx
         LEFT JOIN ret_purchase_order p ON rfx.rate_fix_po_item_id = p.po_id
         LEFT JOIN ret_grn_entry g ON g.grn_id = p.po_grn_id
         LEFT JOIN ret_purchase_order_items r ON r.po_item_po_id = p.po_id
         LEFT JOIN(SELECT IFNULL(SUM(grn.grn_item_cost),0) as grn_item_cost,IFNULL(SUM(grn.grn_item_gst_rate),0) as grn_item_gst_rate,grn.grn_item_grn_id,grn.grn_item_cat_id
                 FROM ret_grn_items grn
                 GROUP BY grn.grn_item_grn_id,grn.grn_item_cat_id) as grnitem ON grnitem.grn_item_grn_id = g.grn_id AND grnitem.grn_item_cat_id = r.po_item_cat_id
         LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
         LEFT JOIN country ct ON ct.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city cy ON cy.id_city=k.id_city
         LEFT JOIN ret_category c ON c.id_ret_category = r.po_item_cat_id
         LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_wt),0) as stone_wt,s.po_item_id
         FROM ret_po_stone_items s
         LEFT JOIN ret_purchase_order_items i ON i.po_item_id = s.po_item_id
         LEFT JOIN ret_stone st ON st.stone_id = s.po_stone_id
         LEFT JOIN ret_stone_type t ON t.id_stone_type = st.stone_type
         WHERE st.stone_type = 1
         GROUP BY s.po_item_id) as stn ON stn.po_item_id = r.po_item_id
         LEFT JOIN(SELECT IFNULL(SUM(s.po_stone_amount),0) as stnamount,s.po_item_id
         FROM ret_po_stone_items s
         LEFT JOIN ret_purchase_order_items i ON i.po_item_id = s.po_item_id
         LEFT JOIN ret_stone st ON st.stone_id = s.po_stone_id
         LEFT JOIN ret_stone_type t ON t.id_stone_type = st.stone_type
         WHERE st.stone_type = 1
         GROUP BY s.po_item_id) as stnd ON stnd.po_item_id = r.po_item_id
         LEFT JOIN(SELECT IFNULL(SUM(rfx.rate_fix_wt),0) as ratefixwt,rfx.rate_fix_po_item_id
                 FROM ret_po_rate_fix rfx
                 GROUP BY rfx.rate_fix_po_item_id) as ratefix ON ratefix.rate_fix_po_item_id = p.po_id
         WHERE p.isratefixed = 0
         ".($rate_fix_id!='' && $rate_fix_id>0 ? "and rfx.rate_fix_id=".$rate_fix_id."":'')."
         GROUP BY rfx.rate_fix_id  HAVING balance_weight > 0
         ORDER BY po_id, createdon ASC");
         return $sql->result_array();
    }
    function generate_opblc_refno()
	{
	    $lastno = NULL;
	    $sql = "SELECT MAX(ref_no) as ref_no FROM smith_company_op_balance o ORDER BY id_smith_company_op_balance DESC LIMIT 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->ref_no;
			}
	    if($lastno != NULL)
		{
		    //$max_num = explode("-",$lastno);
            $number = (int) $lastno;
            $number++;
            $order_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $order_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $order_number;
	}
    function get_NontagLots()
    {
        $sql = $this->db->query("SELECT lt.lot_no,lt.stock_type,IFNULL(ltd.gross_wt,0) AS lot_grswt,
        IFNULL(ltd.no_of_piece,0) AS lot_pcs,IFNULL(ntl.nt_pcs,0) AS nt_pcs,IFNULL(ntl.nt_grs_wt,0) AS nt_grswt
        FROM ret_lot_inwards lt
        LEFT JOIN ret_lot_inwards_detail ltd ON ltd.lot_no = lt.lot_no
        LEFT JOIN (
                    SELECT nt.lot_id,SUM(nt.pcs) AS nt_pcs,SUM(nt.grs_wt) AS nt_grs_wt
                    FROM ret_nontag_receipt nt
                    LEFT JOIN ret_lot_inwards rlt ON rlt.lot_no = nt.lot_id
                    GROUP BY nt.lot_id
                ) AS ntl ON ntl.lot_id = lt.lot_no
        WHERE lt.stock_type = 2 AND lt.is_closed = 0 AND lt.lot_received_at = 1 AND lt.lot_from IN (2,6) AND ltd.tag_status = 0
        AND ( ltd.no_of_piece > IFNULL(ntl.nt_pcs,0) OR ntl.nt_pcs = 0 ) AND (ltd.gross_wt > IFNULL(ntl.nt_grs_wt,0) OR ntl.nt_grs_wt = 0)
        GROUP BY lt.lot_no ORDER BY lt.lot_no DESC");
        // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_lot_nontag_details($data)
    {
        $sql = $this->db->query("SELECT lt.lot_no,ltd.id_lot_inward_detail,ltd.lot_product as id_product,
        ltd.lot_id_design as id_design,ltd.id_sub_design as id_sub_design,
        ifnull(pro.product_name,'') as product_name,ifnull(des.design_name,'') as design_name,ifnull(sub_des.sub_design_name,'') as sub_design_name,
        ifnull(ltd.id_section,'') as id_section,ifnull(sect.section_name,'') as section_name
        FROM ret_lot_inwards lt
        LEFT JOIN ret_lot_inwards_detail ltd on ltd.lot_no = lt.lot_no
        LEFT JOIN ret_product_master pro on pro.pro_id = ltd.lot_product
        LEFT JOIN ret_design_master des on des.design_no = ltd.lot_id_design
        LEFT JOIN ret_sub_design_master sub_des on sub_des.id_sub_design = ltd.id_sub_design
        LEFT JOIN ret_section sect on sect.id_section = ltd.id_section
        WHERE lt.stock_type=2 and lt.is_closed = 0 and lt.lot_no=".$data['lot_no']."
        group by ltd.id_lot_inward_detail");
       // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function getNonTagLotItemDetails($data)
    {
        $ntdata = array();
        $sql = $this->db->query("SELECT lt.lot_no,lt.stock_type,ltd.id_lot_inward_detail,
        (ltd.no_of_piece - ifnull(ntag.nt_pcs,0)) as pcs,(ltd.gross_wt - ifnull(ntag.nt_grswt,0)) as grs_wt,
        (ltd.less_wt - ifnull(ntag.nt_less_wt,0)) as less_wt,
        (ltd.net_wt - ifnull(ntag.nt_net_wt,0)) as net_wt,ifnull(mt.metal,'') as metal,ifnull(cat.name,'') as category,
        po.po_ref_no,date_format(po.po_date,'%d-%m-%Y') as po_date,ifnull(kar.firstname,'') as karigar
        FROM ret_lot_inwards lt
        LEFT JOIN ret_lot_inwards_detail ltd on ltd.lot_no = lt.lot_no
        LEFT JOIN ret_category cat on cat.id_ret_category = ltd.lot_id_category
        LEFT JOIN metal mt on mt.id_metal = cat.id_metal
        LEFT JOIN ret_purchase_order po on po.po_id = lt.po_id
        LEFT JOIN ret_karigar kar on kar.id_karigar = po.po_karigar_id
        LEFT JOIN (SELECT nt.lot_id,nt.id_lot_inward_detail,ifnull(sum(nt.pcs),0) as nt_pcs,ifnull(sum(nt.grs_wt),0) as nt_grswt,
        ifnull(sum(nt.less_wt),0) as nt_less_wt,ifnull(sum(nt.net_wt),0) as nt_net_wt
        FROM ret_nontag_receipt nt
        WHERE nt.lot_id=".$data['lot_no']."  and nt.id_lot_inward_detail = ".$data['id_lot_inward_detail']." and nt.id_product=".$data['id_product'].") as ntag on ntag.id_lot_inward_detail = ltd.id_lot_inward_detail
        LEFT JOIN (SELECT lm.id_lot_inward_detail FROM ret_lot_merge lm
        LEFT JOIN ret_lot_inwards_detail lt on lt.id_lot_inward_detail = lm.id_lot_inward_detail) as lt_m on lt_m.id_lot_inward_detail = ltd.id_lot_inward_detail
        WHERE lt.stock_type =2 and lt.is_closed = 0 and lt.lot_no = ".$data['lot_no']." and ltd.id_lot_inward_detail = ".$data['id_lot_inward_detail']." and lt_m.id_lot_inward_detail is null
        and ltd.lot_product=".$data['id_product']."
        HAVING grs_wt > 0");
        //print_r($this->db->last_query());exit;
        $result =  $sql ->result_array();
        foreach($result as $r)
        {
            $ntdata[] = array(
                'lot_no'                => $r['lot_no'],
                'id_lot_inward_detail'  => $r['id_lot_inward_detail'],
                'pcs'                   => $r['pcs'],
                'grs_wt'                => $r['grs_wt'],
                'less_wt'               => $r['less_wt'],
                'net_wt'                => $r['net_wt'],
                'metal'                 => $r['metal'],
                'category'              => $r['category'],
                'po_ref_no'             => $r['po_ref_no'],
                'karigar'               => $r['karigar'],
                'po_date'               => $r['po_date'],
                'lotdet'                => $this->getLotNotTag($r['id_lot_inward_detail'],$data['id_product']),
                'completedLot'          => $this->getCompletedNonTag($r['id_lot_inward_detail'],$data['id_product']),
            );
        }
        return $ntdata;
    }
    function getLotNotTag($id_lot_inward_detail,$id_product)
    {
        $sql = $this->db->query("SELECT lt.lot_no,lt.stock_type,ltd.id_lot_inward_detail,
        ltd.no_of_piece,ltd.gross_wt,
        ltd.less_wt,
        ltd.net_wt
        FROM ret_lot_inwards lt
        LEFT JOIN ret_lot_inwards_detail ltd on ltd.lot_no = lt.lot_no
        WHERE lt.stock_type =2 and lt.is_closed = 0 and ltd.id_lot_inward_detail = ".$id_lot_inward_detail." and ltd.lot_product = ".$id_product."");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function getCompletedNonTag($id_lot_inward_detail,$id_product)
    {
        $sql = $this->db->query("SELECT nt.lot_id,ifnull(sum(nt.pcs),0) as nt_pcs,ifnull(sum(nt.grs_wt),0) as nt_grswt,
        ifnull(sum(nt.less_wt),0) as nt_less_wt,ifnull(sum(nt.net_wt),0) as nt_net_wt
        FROM ret_nontag_receipt nt
        WHERE nt.id_lot_inward_detail=".$id_lot_inward_detail." and nt.id_product=".$id_product."");
        return $sql->result_array();
    }
    function getNonTagReceiptNum()
	{
	    $lastno = NULL;
	    $sql = "SELECT MAX(nt_receipt_no) as last_receipt_no
					FROM ret_nontag_receipt nt
					ORDER BY id_nontag_receipt DESC
					LIMIT 1";
			$result = $this->db->query($sql);
			if( $result->num_rows() > 0){
				$lastno = $result->row()->last_receipt_no;
			}
	    if($lastno != NULL)
		{
		    //$max_num = explode("-",$lastno);
            $number = (int) $lastno;
            $number++;
            $receipt_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		}
		else
		{
           $receipt_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		return $receipt_number;
	}
    function get_nontag_receiptedList($data)
    {
        $sql = $this->db->query("SELECT nt.id_nontag_receipt,ifnull(nt.nt_receipt_no,'') as nt_receipt_no,nt.lot_id,
        br.name as branch_name,ifnull(sect.section_name,'') as section_name,ifnull(pro.product_name,'') as product_name,
        ifnull(des.design_name,'') as design_name,ifnull(subdes.sub_design_name,'') as sub_design_name,
        nt.pcs,nt.grs_wt,nt.less_wt,nt.net_wt,IFNULL(nt.remark,'') as remark,DATE_FORMAT(nt.created_on,'%d-%m-%Y') AS receipt_date
        from ret_nontag_receipt nt
        LEFT join branch br on br.id_branch = nt.id_branch
        LEft join ret_section sect on sect.id_section = nt.id_section
        Left join ret_product_master pro on pro.pro_id = nt.id_product
        Left join ret_design_master des on des.design_no = nt.id_design
        Left join ret_sub_design_master subdes on subdes.id_sub_design = nt.id_sub_design
        WHERE ".($data['from_date']!='' && $data['to_date']!='' ? " (date(nt.created_on) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."ORDER BY nt.id_nontag_receipt DESC");
        return $sql->result_array();
    }
    function getLottedNotTagWt($lot_no,$id_product,$id_design,$id_sub_design)
    {
        $sql = $this->db->query("SELECT lt.lot_no,lt.stock_type,ltd.id_lot_inward_detail,ltd.gross_wt
        FROM ret_lot_inwards lt
        LEFT JOIN ret_lot_inwards_detail ltd on ltd.lot_no = lt.lot_no
        LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_po_id = lt.po_id
        WHERE lt.stock_type =2 and lt.is_closed = 0 and lt.lot_no = ".$lot_no." and ltd.lot_product = ".$id_product."
        and po_itm.po_item_des_id = ".$id_design."  and po_itm.po_item_sub_des_id = ".$id_sub_design."");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
    }
    function getReceiptedNontagWt($lot_no,$id_product,$id_design,$id_sub_design)
    {
        $sql = $this->db->query("SELECT sum(ntr.grs_wt) as nt_grs_wt
        from ret_nontag_receipt ntr
        where ntr.lot_id =".$lot_no." and ntr.id_product=".$id_product." and ntr.id_design=".$id_design." and ntr.id_sub_design=".$id_sub_design."");
        //print_r($this->db->last_query());exit;
        return $sql->row()->nt_grs_wt;
    }
    function get_karigar_wise_tds_percent($id_karigar,$fin_year)
	{
	    $sql=$this->db->query("SELECT kar.id_karigar,
        kar.fin_year_code,
        (IFNULL(rp.item_cost,0)+IFNULL(kr.opening_balance_amount,0)) as balance
        FROM ret_karigar kar
        LEFT JOIN(SELECT IFNULL(SUM(po.item_cost),0) as item_cost,i.po_karigar_id
        FROM ret_purchase_order_items po
        LEFT JOIN ret_purchase_order i ON i.po_id = po.po_item_po_id
        Group by i.po_karigar_id ) as rp on rp.po_karigar_id = kar.id_karigar
        LEFT JOIN(SELECT IFNULL(rk.opening_balance_amount,0) as opening_balance_amount,
        rk.id_karigar
        FROM ret_karigar rk
        WHERE rk.fin_year_code =".$fin_year."
        Group by rk.id_karigar) as kr on kr.id_karigar = kar.id_karigar
        WHERE kar.id_karigar =".$id_karigar."
        Group by kar.id_karigar");
       return $sql->row_array();
    }
    function get_po_balance($data=[])
    {
        $sql1 = $this->db->query("SELECT p.po_id,p.po_ref_no,p.is_po_halmarked,
        (IFNULL(SUM(d.passed_pcs),0)-IFNULL(lot.no_of_piece,0)-IFNULL(metalIssue.issue_pcs,0)- IFNULL(ret.pcs,0)) as blc_pcs,
        (IFNULL(SUM(d.passed_gwt),0)-IFNULL(metalIssue.issue_wt,0)-IFNULL(lot.gross_wt,0)- IFNULL(ret.gwt,0)) as gross_wt

        FROM ret_po_qc_issue_details d 
        LEFT JOIN ret_po_qc_issue_process q ON q.qc_process_id = d.qc_process_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id

    
        LEFT JOIN (SELECT IFNULL(SUM(ld.no_of_piece),0) as no_of_piece,IFNULL(SUM(ld.gross_wt),0) as gross_wt,l.po_id
            FROM ret_lot_inwards l
            LEFT JOIN ret_lot_inwards_detail ld ON ld.lot_no = l.lot_no
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = ld.po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id
        GROUP BY p.po_id) as lot ON lot.po_id = p.po_id   

        LEFT JOIN (SELECT pri.pur_ret_po_item_id,p.po_id,
            ifnull(sum(pri.pur_ret_gwt),0) as gwt,ifnull(sum(pri.pur_ret_pcs),0) as pcs,ifnull(sum(pri.pur_ret_nwt),0) as nwt
            FROM ret_purchase_return_items pri
            LEFT JOIN ret_purchase_return pr on pr.pur_return_id = pri.pur_ret_id
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = pri.pur_ret_po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id
            WHERE pr.purchase_type =1 and pri.pur_ret_po_item_id is not null and pr.bill_status = 1
        group by p.po_id) as ret on ret.po_id = p.po_id       

        LEFT JOIN (SELECT IFNULL(SUM(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt,p.po_id
            FROM ret_karigar_metal_issue_details d 
            LEFT JOIN ret_karigar_metal_issue i ON i.met_issue_id = d.issue_met_parent_id
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id
            WHERE i.bill_status = 1
        GROUP BY p.po_id) as metalIssue ON metalIssue.po_id = p.po_id

    
        WHERE d.status = 1 and p.is_po_halmarked = 1
        ".(!empty($data['fin_year']) ? "and p.fin_year_code=".$data['fin_year']."" : "")."
        GROUP BY r.po_item_po_id
        having gross_wt > 0;");

        $result1 = $sql1->result_array();


        $sql2 = $this->db->query("SELECT p.po_id,p.po_ref_no,p.is_po_halmarked,
        (IFNULL(SUM(d.hm_passed_pcs),0)-IFNULL(lot.no_of_piece,0)-IFNULL(metalIssue.issue_pcs,0)- IFNULL(ret.pcs,0)) as blc_pcs,
        (IFNULL(SUM(d.hm_passed_gwt),0)-IFNULL(metalIssue.issue_wt,0)-IFNULL(lot.gross_wt,0)- IFNULL(ret.gwt,0)) as gross_wt
        
        FROM ret_po_hm_process_details d 
        LEFT JOIN ret_po_halmark_process hm on hm.hm_process_id = d.hm_issue_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.hm_po_item_id
        LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id
        
        LEFT JOIN (SELECT IFNULL(SUM(ld.no_of_piece),0) as no_of_piece,IFNULL(SUM(ld.gross_wt),0) as gross_wt,l.po_id
            FROM ret_lot_inwards l
            LEFT JOIN ret_lot_inwards_detail ld ON ld.lot_no = l.lot_no
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = ld.po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id
        GROUP BY p.po_id) as lot ON lot.po_id = p.po_id 

        LEFT JOIN (SELECT pri.pur_ret_po_item_id,p.po_id,
            ifnull(sum(pri.pur_ret_gwt),0) as gwt,ifnull(sum(pri.pur_ret_pcs),0) as pcs,ifnull(sum(pri.pur_ret_nwt),0) as nwt
            FROM ret_purchase_return_items pri
            LEFT JOIN ret_purchase_return pr on pr.pur_return_id = pri.pur_ret_id
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = pri.pur_ret_po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id
            WHERE pr.purchase_type =1 and pri.pur_ret_po_item_id is not null and pr.bill_status = 1
        group by p.po_id) as ret on ret.po_id = p.po_id       

        LEFT JOIN (SELECT IFNULL(SUM(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt,p.po_id
            FROM ret_karigar_metal_issue_details d 
            LEFT JOIN ret_karigar_metal_issue i ON i.met_issue_id = d.issue_met_parent_id
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.po_item_id
            LEFT JOIN ret_purchase_order p ON p.po_id = r.po_item_po_id
            WHERE i.bill_status = 1
        GROUP BY p.po_id) as metalIssue ON metalIssue.po_id = p.po_id  
        
        WHERE d.hm_status = 1 and p.is_po_halmarked = 0
        ".(!empty($data['fin_year']) ? "and p.fin_year_code=".$data['fin_year']."" : "")."
        GROUP BY r.po_item_po_id
        having gross_wt > 0;");

        $result2 = $sql2->result_array();

        $result = array_merge($result1,$result2);

        return $result;

    }
    function get_po_balance_details($data)
    {
        $returnData=[];
        if($data['ishallmarked']==0)   //  FROM ret_po_hm_process_details table
        {
            $sql = $this->db->query("SELECT r.po_item_id,d.hm_receipt_id,pr.po_ref_no,pr.po_id,pro.product_name,(IFNULL(SUM(d.hm_passed_pcs),0) - IFNULL(kar_iss.issue_pcs,0)-IFNULL(lot.lot_pcs,0)-IFNULL(ret.pcs,0)) as pcs,
            (IFNULL(SUM(d.hm_passed_gwt),0)-IFNULL(kar_iss.issue_metal_wt,0)-IFNULL(lot.lot_gwt,0)-IFNULL(ret.gwt,0)) as gross_wt,
            (IFNULL(SUM(d.hm_passed_nwt),0)-IFNULL(kar_iss.issue_metal_wt,0)-IFNULL(lot.lot_nwt,0)-IFNULL(ret.nwt,0)) as net_wt,pur.purity,pro.cat_id,c.name as cat_name,
            if(pro.stock_type=1,'Tagged Items','Non Tagged Items') as stock_type,(IFNULL(SUM(d.hm_passed_lwt),0)-IFNULL(lot.lot_lwt,0)) as less_wt,pro.stock_type as stktype,r.id_purity,des.design_name,subDes.sub_design_name,pro.pro_id,r.po_item_des_id,r.po_item_sub_des_id,
            e.firstname as emp_name
            
            FROM ret_po_hm_process_details d
            LEFT JOIN ret_po_halmark_process hm on hm.hm_process_id = d.hm_issue_id
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.hm_po_item_id
            LEFT JOIN ret_purchase_order pr ON pr.po_id = r.po_item_po_id
            left join ret_product_master pro ON pro.pro_id = r.po_item_pro_id
            LEFT JOIN ret_design_master des ON des.design_no = r.po_item_des_id
            LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = r.po_item_sub_des_id
            LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
            LEFT JOIN ret_purity pur ON pur.id_purity = r.id_purity
            LEFT JOIN employee e on e.id_employee = hm.hm_vendor_id

            LEFT JOIN (SELECT km.po_id,kmd.po_item_id,
            ifnull(sum(kmd.issue_pcs),0) as issue_pcs,ifnull(sum(kmd.issue_metal_wt),0) as issue_metal_wt
            FROM ret_karigar_metal_issue_details kmd
            LEFT JOIN ret_karigar_metal_issue km on km.met_issue_id = kmd.issue_met_parent_id
            where km.issue_against_po = 1 and km.bill_status=1
            GROUP BY kmd.po_item_id) as kar_iss on kar_iss.po_item_id = r.po_item_id


            LEFT JOIN (SELECT pri.pur_ret_po_item_id,
            ifnull(sum(pri.pur_ret_gwt),0) as gwt,ifnull(sum(pri.pur_ret_pcs),0) as pcs,ifnull(sum(pri.pur_ret_nwt),0) as nwt
            FROM ret_purchase_return_items pri
            LEFT JOIN ret_purchase_return pr on pr.pur_return_id = pri.pur_ret_id
            WHERE pr.purchase_type =1 and pri.pur_ret_po_item_id is not null and pr.bill_status = 1
            group by pri.pur_ret_po_item_id) as ret on ret.pur_ret_po_item_id = r.po_item_id

            LEFT JOIN (SELECT IFNULL(SUM(det.no_of_piece),0) as lot_pcs,IFNULL(SUM(det.gross_wt),0) as lot_gwt,IFNULL(SUM(det.net_wt),0) as lot_nwt,
            r.po_item_id,IFNULL(SUM(det.less_wt),0) as lot_lwt
            FROM ret_lot_inwards_detail det
            LEFT JOIN ret_lot_inwards l ON l.lot_no = det.lot_no
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = det.po_item_id
            group by r.po_item_id) as lot ON lot.po_item_id = r.po_item_id

            WHERE d.hm_status = 1
            ".($data['po_id']!='' ? " and pr.po_id=".$data['po_id']."" :'')."
            ".($data['fin_year']!='' ? "and pr.fin_year_code=".$data['fin_year']."" : "")."
            GROUP BY d.hm_po_item_id
            HAVING gross_wt > 0");

            $result = $sql->result_array();
            foreach($result as $val){
                $val['stone_details'] = $this->get_hm_stone_accepted_details($val['po_item_id']);
                $val['other_metal_details'] = $this->get_qc_other_metal_details($val['po_item_id']);
                $returnData[]=$val;
            }
            
        }else{
            $sql = $this->db->query("SELECT r.po_item_id,d.id_qc_issue_details,pr.po_ref_no,pr.po_id,pro.product_name,(IFNULL(SUM(d.passed_pcs),0) - IFNULL(kar_iss.issue_pcs,0)-IFNULL(lot.lot_pcs,0)-IFNULL(ret.pcs,0)) as pcs,(IFNULL(SUM(d.passed_gwt),0)-IFNULL(kar_iss.issue_metal_wt,0)-IFNULL(lot.lot_gwt,0)-IFNULL(ret.gwt,0)) as gross_wt,
            (IFNULL(SUM(d.passed_nwt),0)-IFNULL(kar_iss.issue_metal_wt,0)-IFNULL(lot.lot_nwt,0)-IFNULL(ret.nwt,0)) as net_wt,pur.purity,pro.cat_id,c.name as cat_name,
            if(pro.stock_type=1,'Tagged Items','Non Tagged Items') as stock_type,(IFNULL(SUM(d.passed_lwt),0)-IFNULL(lot.lot_lwt,0)) as less_wt,pro.stock_type as stktype,r.id_purity,des.design_name,subDes.sub_design_name,pro.pro_id,r.po_item_des_id,r.po_item_sub_des_id,
            e.firstname as emp_name
            
            FROM ret_po_qc_issue_details d
            LEFT JOIN ret_po_qc_issue_process p ON p.qc_process_id = d.qc_process_id
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = d.po_item_id
            LEFT JOIN ret_purchase_order pr ON pr.po_id = r.po_item_po_id
            left join ret_product_master pro ON pro.pro_id = r.po_item_pro_id
            LEFT JOIN ret_design_master des ON des.design_no = r.po_item_des_id
            LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = r.po_item_sub_des_id
            LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
            LEFT JOIN ret_purity pur ON pur.id_purity = r.id_purity
            LEFT JOIN employee e on e.id_employee = p.qc_id_vendor
            
            LEFT JOIN (SELECT km.po_id,kmd.po_item_id,
            ifnull(sum(kmd.issue_pcs),0) as issue_pcs,ifnull(sum(kmd.issue_metal_wt),0) as issue_metal_wt
            FROM ret_karigar_metal_issue_details kmd
            LEFT JOIN ret_karigar_metal_issue km on km.met_issue_id = kmd.issue_met_parent_id
            where km.issue_against_po = 1 and km.bill_status=1
            GROUP BY kmd.po_item_id) as kar_iss on kar_iss.po_item_id = r.po_item_id

            LEFT JOIN (SELECT pri.pur_ret_po_item_id,
            ifnull(sum(pri.pur_ret_gwt),0) as gwt,ifnull(sum(pri.pur_ret_pcs),0) as pcs,ifnull(sum(pri.pur_ret_nwt),0) as nwt
            FROM ret_purchase_return_items pri
            LEFT JOIN ret_purchase_return pr on pr.pur_return_id = pri.pur_ret_id
            WHERE pr.purchase_type =1 and pri.pur_ret_po_item_id is not null and pr.bill_status = 1
            group by pri.pur_ret_po_item_id) as ret on ret.pur_ret_po_item_id = r.po_item_id

            
            LEFT JOIN (SELECT IFNULL(SUM(det.no_of_piece),0) as lot_pcs,IFNULL(SUM(det.gross_wt),0) as lot_gwt,IFNULL(SUM(det.net_wt),0) as lot_nwt,
            r.po_item_id,IFNULL(SUM(det.less_wt),0) as lot_lwt
            FROM ret_lot_inwards_detail det
            LEFT JOIN ret_lot_inwards l ON l.lot_no = det.lot_no
            LEFT JOIN ret_purchase_order_items r ON r.po_item_id = det.po_item_id
            group by r.po_item_id) as lot ON lot.po_item_id = r.po_item_id
        
            WHERE d.status = 1
            ".($data['po_id']!='' ? " and pr.po_id=".$data['po_id']."" :'')."
            ".($data['fin_year']!='' ? "and pr.fin_year_code=".$data['fin_year']."" : "")."
            GROUP BY d.po_item_id
            HAVING gross_wt > 0");
            //print_r($this->db->last_query());exit;
            $result = $sql->result_array();
            foreach($result as $val){
                $val['stone_details'] = $this->get_qc_stone_accepted_details($val['po_item_id']);
                $val['other_metal_details'] = $this->get_qc_other_metal_details($val['po_item_id']);
                $returnData[]=$val;
            }
        }
        return $returnData;
    }
    function get_creditdebit_entry($data)
    {

        $sql=$this->db->query("SELECT IFNULL(ct.transbillno,'') as transbillno,IF(ct.accountto =1,'Supplier',IF(ct.accountto =2,'Smith','Approval')) as accounttype,

        If(ct.transtype = 1,'Credit','Debit') as transtype,IFNULL(ct.transamount,0) as amount,IFNULL(ct.weight,0) as weight,IFNULL(ct.naration,'') as naration,

        date_format(ct.transdate,'%d-%m-%Y') as transdate,IFNULL(k.firstname,'') as karigar,IFNULL(cl.ledger_name, '-') as ledger_name,

        ct.crdrid,ct.crdr_status,IF(ct.crdr_status =1,'Success','Cancelled') as pay_status,

        IF(ct.accountto = 1,'Supplier',IF(ct.accountto = 2,'Smith','Approvals')) as accountto

        FROM ret_crdr_note ct

        LEFT JOIN ret_karigar k ON k.id_karigar=ct.supid

        LEFT JOIN ret_crdr_ledger cl ON cl.id_crdr_ledger = ct.id_cr_dr_ledger

        WHERE ct.crdrid IS NOT NULL 

        ".($data['trans_type']!='' ? " and ct.accountto=".$data['trans_type']."" :'')."

        ".($data['status_type']!='' ? " and ct.crdr_status=".$data['status_type']."" :'')."

        ".($data['transcation_type']!='' ? " and ct.transtype=".$data['transcation_type']."" :'')."

        ".($data['from_date']!='' && $data['to_date']!=''  ? " and (date(ct.transdate) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')" :'')."

        ");

        return $sql->result_array();

    }
     function get_credit_debit($id)
	{
	    $sql=$this->db->query("SELECT IFNULL(ct.transbillno,'') as transbillno,ct.accountto  as accountto,
        ct.transtype  as transtype,IFNULL(ct.transamount,0) as transamount,IFNULL(ct.weight,0) as weight,
        IFNULL(ct.naration,'') as naration, ct.id_cr_dr_ledger, IFNULL(cl.ledger_name, '-') as ledger_name,
        date_format(ct.transdate,'%d-%m-%Y') as transdate,IFNULL(k.firstname,'') as karigar,
        ct.supid,ct.crdrid
        FROM ret_crdr_note ct
        LEFT JOIN ret_karigar k ON k.id_karigar=ct.supid
        LEFT JOIN ret_crdr_ledger cl ON cl.id_cr_dr_ledger = ct.id_cr_dr_ledger
        WHERE ct.crdrid = ".$id." ");
        return $sql->row_array();
	}
	function code_number_generator()
	{
		$lastno = $this->get_last_code_no();
		if($lastno!=NULL)
		{
			$number = (int) $lastno;
			$number++;
			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);
			return $code_number;
		}
		else
		{
			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);
			return $code_number;
		}
	}
	function get_last_code_no()
    {
		$sql = "SELECT (transbillno) as transbillno
		FROM ret_crdr_note
		where crdrid is not null
		ORDER BY crdrid DESC LIMIT 1";
        //print_r($sql);exit;
		return $this->db->query($sql)->row()->transbillno;
	}
    function get_retagging_details($data)
    {
        $returnData = [];
        $return_array = array();
        
        $op_date= date('Y-m-d',(strtotime('-1 day',strtotime($data['from_date']))));

        $multiple_id_metal = implode(' , ', $data['id_metal']);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $data['id_metal'];
		}

        $multiple_id_category = implode(' , ', $data['id_category']);
        if($multiple_id_category != '')
		{
			$id_category = $multiple_id_category;
		}else{
			$id_category = $data['id_category'];
		}

        $multiple_receipttype = implode(' , ', $data['receipt_type']);
		if(empty($data['receipt_type'])){
		    $data['receipt_type'] = array(0);
		}

        // Old metal query
        $receipt_type[1] = $sql = $this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,br.name as branch_name,date_format(b.dwnload_datetime,'%d-%m-%Y') as bt_date,
        (IFNULL(op_inw.gross_wt,0)-IFNULL(op_blc_retag.gross_wt,0) - IFNULL(op_blc_pocket.gross_wt,0)) as op_blc_gwt,
        (IFNULL(op_inw.net_wt,0)-IFNULL(op_blc_retag.net_wt,0) - IFNULL(op_blc_pocket.net_wt,0)) as op_blc_nwt,
        (IFNULL(op_blc_inw_dia.diawt,0)-IFNULL(op_blc_retagDia.dia_wt,0) - IFNULL(op_blc_pocketDia.dia_wt,0)) as op_blc_diawt,
        '0.000' as op_blc_grm_wt,'0.000' as op_blc_ct_wt,

        IFNULL(inw.gross_wt,0) as inw_gwt,IFNULL(inw.net_wt,0) as inw_nwt,
        IFNULL(inw_dia.diawt,0) as inw_diawt,'0.000' as inw_grm_wt,'0.000' as inw_ct_wt,
        
        '0.000' as issue_gwt,'0.000' as issue_nwt,'0.000' as issue_diawt,'0.000' as issue_grm_wt,'0.000' as issue_ct_wt,
        
        IFNULL(retag.gross_wt,0) as retag_gwt,IFNULL(retag.net_wt,0) as retag_nwt,
        IFNULL(retagDia.dia_wt,0) as retag_diawt,'0.000' as retag_grm_wt,'0.000' as retag_ct_wt,
        
        '0.000' as ret_gwt,'0.000' as ret_nwt,'0.000' as return_diawt,'0.000' as ret_grm_wt,'0.000' as ret_ct_wt,
        
        IFNULL(pocket.gross_wt,0) as pkt_gwt,IFNULL(pocket.net_wt,0) as pkt_nwt,IFNULL(pocketDia.dia_wt,0) as pkt_diawt,
        '0.000' as pkt_grm_wt,'0.000' as pkt_ct_wt
        
        FROM ret_branch_transfer b 
        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = t.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        WHERE t.item_type=1 AND date(brch.dwnload_datetime)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and m.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id)  as op_inw ON op_inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(s.wt),0) as diawt
        FROM ret_brch_transfer_old_metal t
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0 
        LEFT JOIN ret_bill_old_metal_sale_details osd on osd.old_metal_sale_id = t.old_metal_sale_id
        LEFT JOIN ret_billing_item_stones s on s.old_metal_sale_id = osd.old_metal_sale_id
        LEFT JOIN ret_billing bill ON bill.bill_id = osd.bill_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=osd.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
        WHERE bill.bill_status = 1  AND st.stone_type = 1 
        AND date(brch.dwnload_datetime)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_blc_inw_dia ON op_blc_inw_dia.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = t.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        WHERE t.item_type=1 
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and m.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id)  as inw ON inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(s.wt),0) as diawt
        FROM ret_brch_transfer_old_metal t
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0 
        LEFT JOIN ret_bill_old_metal_sale_details osd on osd.old_metal_sale_id = t.old_metal_sale_id
        LEFT JOIN ret_billing_item_stones s on s.old_metal_sale_id = osd.old_metal_sale_id
        LEFT JOIN ret_billing bill ON bill.bill_id = osd.bill_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=osd.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
        WHERE bill.bill_status = 1  AND st.stone_type = 1 
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
       ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id



        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.transfer_id,bt.old_metal_sale_id
        FROM ret_brch_transfer_old_metal bt
        WHERE bt.item_type=1
        GROUP BY bt.old_metal_sale_id) as bt on bt.old_metal_sale_id = accDet.ref_no
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = bt.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        WHERE acc.type = 4 and date(acc.date_add)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retag ON op_blc_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(accstn.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process_stone_details  accstn
        LEFT JOIN ret_acc_stock_process_details accdet on accdet.id_process_details = accstn.id_process_details
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accdet.id_process
        LEFT JOIN(SELECT bt.transfer_id,bt.old_metal_sale_id
        FROM ret_brch_transfer_old_metal bt
        WHERE bt.item_type=1
        GROUP by bt.old_metal_sale_id) as bt on bt.old_metal_sale_id = accdet.ref_no
        LEFT JOIN ret_branch_transfer brch on brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = bt.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        LEFT JOIN ret_stone st on st.stone_id = accstn.stone_id
        WHERE acc.type=4 and st.stone_type=1
        and date(acc.date_add)<='".$op_date."' 
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retagDia  ON op_blc_retagDia .branch_transfer_id = b.branch_transfer_id



        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.transfer_id,bt.old_metal_sale_id
        FROM ret_brch_transfer_old_metal bt
        WHERE bt.item_type=1
        GROUP BY bt.old_metal_sale_id) as bt on bt.old_metal_sale_id = accDet.ref_no
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = bt.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        WHERE acc.type = 4 and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retag ON retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(accstn.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process_stone_details  accstn
        LEFT JOIN ret_acc_stock_process_details accdet on accdet.id_process_details = accstn.id_process_details
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accdet.id_process
        LEFT JOIN(SELECT bt.transfer_id,bt.old_metal_sale_id
        FROM ret_brch_transfer_old_metal bt
        WHERE bt.item_type=1
        GROUP by bt.old_metal_sale_id) as bt on bt.old_metal_sale_id = accdet.ref_no
        LEFT JOIN ret_branch_transfer brch on brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = bt.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        LEFT JOIN ret_stone st on st.stone_id = accstn.stone_id
        WHERE acc.type=4 and st.stone_type=1
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retagDia  ON retagDia.branch_transfer_id = b.branch_transfer_id

        
        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN(SELECT bt.transfer_id,bt.old_metal_sale_id
        FROM ret_brch_transfer_old_metal bt
        WHERE bt.item_type=1
        GROUP by bt.old_metal_sale_id) as bt on bt.old_metal_sale_id = pkd.old_metal_sale_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = bt.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        WHERE pk.trans_type = 1 and pkd.type = 1 
        and date(pk.date)<='".$op_date."' 
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_pocket  ON op_blc_pocket.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pckstn.stone_wt),0) as dia_wt
        FROM ret_old_metal_pocket_stone_details pckstn
        LEFT JOIN ret_old_metal_pocket_details pkd on pkd.id_pocket_details = pckstn.id_pocket_details
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket 
         LEFT JOIN(SELECT bt.transfer_id,bt.old_metal_sale_id
        FROM ret_brch_transfer_old_metal bt
        WHERE bt.item_type=1
        GROUP by bt.old_metal_sale_id) as bt on bt.old_metal_sale_id = pkd.old_metal_sale_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = bt.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        LEFT JOIN ret_stone st on st.stone_id = pckstn.stone_id
        WHERE pk.trans_type = 1 and pkd.type = 1 and st.stone_type = 1
        and date(pk.date)<='".$op_date."' 
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_pocketDia  ON op_blc_pocketDia.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN(SELECT bt.transfer_id,bt.old_metal_sale_id
        FROM ret_brch_transfer_old_metal bt
        WHERE bt.item_type=1
        GROUP by bt.old_metal_sale_id) as bt on bt.old_metal_sale_id = pkd.old_metal_sale_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = bt.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        WHERE pk.trans_type = 1 and pkd.type = 1 
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as pocket  ON pocket.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pckstn.stone_wt),0) as dia_wt
        FROM ret_old_metal_pocket_stone_details pckstn
        LEFT JOIN ret_old_metal_pocket_details pkd on pkd.id_pocket_details = pckstn.id_pocket_details
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket 
         LEFT JOIN(SELECT bt.transfer_id,bt.old_metal_sale_id
        FROM ret_brch_transfer_old_metal bt
        WHERE bt.item_type=1
        GROUP by bt.old_metal_sale_id) as bt on bt.old_metal_sale_id = pkd.old_metal_sale_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id = bt.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_category c ON c.id_old_metal_cat=e.id_old_metal_category
		LEFT JOIN metal m on m.id_metal = c.id_metal
        LEFT JOIN ret_stone st on st.stone_id = pckstn.stone_id
        WHERE pk.trans_type = 1 and pkd.type = 1 and st.stone_type = 1
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and c.id_old_metal_cat in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as pocketDia  ON pocketDia.branch_transfer_id = b.branch_transfer_id


       
        WHERE b.transfer_item_type = 3 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
        ".($data['id_branch']!='' ? " and b.transfer_from_branch=".$data['id_branch']."" :'')."");
        
       // print_r($this->db->last_query());exit;




        // Sales Return query
        $receipt_type[2] =  $sql = $this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,br.name as branch_name,date_format(b.dwnload_datetime,'%d-%m-%Y') as bt_date,
        (IFNULL(op_inw.gross_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.gross_wt,0)-IFNULL(op_blc_ret.gross_wt,0)-IFNULL(op_blc_pocket.gross_wt,0)) as op_blc_gwt,
        (IFNULL(op_inw.net_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.net_wt,0)-IFNULL(op_blc_ret.net_wt,0)-IFNULL(op_blc_pocket.net_wt,0)) as op_blc_nwt,
        (IFNULL(op_blc_inw_dia.diawt,0) + IFNULL(op_inw_looseDia.gross_wt,0) - IFNULL(op_blc_looseDia_retag.gross_wt,0) - IFNULL(op_blc_retagDia.dia_wt,0) - IFNULL(op_blc_looseDia_ret.gross_wt,0) - IFNULL(op_blc_retDia.diawt,0) - IFNULL(op_blc_looseDia_pocket.gross_wt,0) -IFNULL(op_blc_pocketDia.diawt,0)) as op_blc_diawt,
        (IFNULL(op_inw_grm.gross_wt,0) - IFNULL(op_blc_grm_metalIssue.issue_wt,0) - IFNULL(op_blc_grm_retag.gross_wt,0) - IFNULL(op_blc_grm_ret.gross_wt,0) - IFNULL(op_blc_grm_pocket.gross_wt,0)) as op_blc_grm_wt,
        (IFNULL(op_inw_ct.gross_wt,0) - IFNULL(op_blc_ct_metalIssue.issue_wt,0) - IFNULL(op_blc_ct_retag.gross_wt,0) - IFNULL(op_blc_grm_ret.gross_wt,0) - IFNULL(op_blc_ct_pocket.gross_wt,0)) as op_blc_ct_wt,

        
        IFNULL(inw.gross_wt,0) as inw_gwt,IFNULL(inw.net_wt,0) as inw_nwt,
        (IFNULL(inw_dia.diawt,0) + IFNULL(looseDia_inw.gross_wt,0)) as inw_diawt,
        IFNULL(grm_inw.gross_wt,0) as inw_grm_wt,IFNULL(ct_inw.gross_wt,0) as inw_ct_wt,
       
        IFNULL(metalIssue.issue_wt,0) as issue_gwt,IFNULL(metalIssue.issue_wt,0) as issue_nwt,
        IFNULL(looseDia_mtissue.issue_wt,0) as issue_diawt,
        IFNULL(grm_mtissue.issue_wt,0) as issue_grm_wt,IFNULL(ct_mtissue.issue_wt,0) as issue_ct_wt,


        IFNULL(retag.gross_wt,0) as retag_gwt,IFNULL(retag.net_wt,0) as retag_nwt,
        (IFNULL(retagDia.dia_wt,0) + IFNULL(looseDia_retag.gross_wt,0)) as retag_diawt,
        IFNULL(grm_retag.gross_wt,0) as retag_grm_wt,IFNULL(ct_retag.gross_wt,0) as retag_ct_wt,
        
        IFNULL(ret.gross_wt,0) as ret_gwt,IFNULL(ret.net_wt,0) as ret_nwt,
        (IFNULL(retDia.diawt,0) + IFNULL(looseDia_ret.gross_wt,0)) as return_diawt,
        IFNULL(grm_ret.gross_wt,0) as ret_grm_wt,IFNULL(ct_ret.gross_wt,0) as ret_ct_wt,
        
        IFNULL(pocket.gross_wt,0) as pkt_gwt,IFNULL(pocket.net_wt,0) as pkt_nwt,
        (IFNULL(pocketDia.diawt,0) + IFNULL(looseDia_pocket.gross_wt,0)) as pkt_diawt,
        IFNULL(grm_pocket.gross_wt,0) as pkt_grm_wt,IFNULL(ct_pocket.gross_wt,0) as pkt_ct_wt

        FROM ret_branch_transfer b 

        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 AND date(brch.dwnload_datetime)<='".$op_date."' and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw ON op_inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 AND date(brch.dwnload_datetime)<='".$op_date."' and p.stone_type = 1 and tag.uom_gross_wt!=6
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw_grm ON op_inw_grm.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 AND date(brch.dwnload_datetime)<='".$op_date."' and p.stone_type = 1 and tag.uom_gross_wt = 6
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw_ct ON op_inw_ct.transfer_id = b.branch_transfer_id

        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 AND date(brch.dwnload_datetime)<='".$op_date."' and p.stone_type = 2 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw_looseDia ON op_inw_looseDia.transfer_id = b.branch_transfer_id

        
        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(s.wt),0) as diawt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_bill_details det ON det.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_billing_item_stones s ON s.bill_det_id = det.bill_det_id
        LEFT JOIN ret_billing bill ON bill.bill_id = det.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
        WHERE bill.bill_status = 1 AND det.status = 2 AND st.stone_type = 1 and det.is_non_tag = 0
        AND date(brch.dwnload_datetime)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_blc_inw_dia ON op_blc_inw_dia.transfer_id = b.branch_transfer_id
 

        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as inw ON inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 and p.stone_type = 1 and tag.uom_gross_wt!=6
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as grm_inw ON grm_inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 and p.stone_type = 1 and tag.uom_gross_wt=6
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as ct_inw ON ct_inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 and p.stone_type = 2
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as looseDia_inw ON looseDia_inw.transfer_id = b.branch_transfer_id





        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(s.wt),0) as diawt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_bill_details det ON det.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_billing_item_stones s ON s.bill_det_id = det.bill_det_id
        LEFT JOIN ret_billing bill ON bill.bill_id = det.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
        WHERE bill.bill_status = 1 AND det.status = 2 AND st.stone_type = 1 and det.is_non_tag = 0
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
       ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id

    
        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 0
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_metalIssue ON op_blc_metalIssue.transfer_id = b.branch_transfer_id

        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt != 6
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_grm_metalIssue ON op_blc_grm_metalIssue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt = 6
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_ct_metalIssue ON op_blc_ct_metalIssue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 2
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_looseDia_metalIssue ON op_blc_looseDia_metalIssue.transfer_id = b.branch_transfer_id

        
        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 0
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as metalIssue ON metalIssue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt != 6
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as grm_mtissue ON grm_mtissue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt = 6
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as ct_mtissue ON ct_mtissue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 2
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as looseDia_mtissue ON looseDia_mtissue.transfer_id = b.branch_transfer_id

    
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accDet.ref_no
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 1 AND date(acc.date_add)<='".$op_date."' and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retag ON op_blc_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accDet.ref_no
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 1 AND date(acc.date_add)<='".$op_date."' and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_retag ON op_blc_grm_retag.branch_transfer_id = b.branch_transfer_id

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accDet.ref_no
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 1 AND date(acc.date_add)<='".$op_date."' and p.stone_type = 1 and tag.uom_gross_wt =6
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_retag ON op_blc_ct_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accDet.ref_no
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 1 AND date(acc.date_add)<='".$op_date."' and p.stone_type = 2 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_retag ON op_blc_looseDia_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accstn.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process_stone_details accstn 
        LEFT JOIN ret_acc_stock_process_details accdet ON accdet.id_process_details = accstn.id_process_details
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accdet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accdet.ref_no
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone st ON st.stone_id = accstn.stone_id
        WHERE acc.type = 1 AND st.stone_type = 1 
        AND date(acc.date_add)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retagDia ON op_blc_retagDia.branch_transfer_id = b.branch_transfer_id


        
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accDet.ref_no
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 1 and p.stone_type = 0
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retag ON retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accDet.ref_no
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 1 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_retag ON grm_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accDet.ref_no
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 1 and p.stone_type = 1 and tag.uom_gross_wt = 6 
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_retag ON ct_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
            FROM ret_brch_transfer_old_metal bt 
            WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = accDet.ref_no
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 1 and p.stone_type = 2
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_retag ON looseDia_retag.branch_transfer_id = b.branch_transfer_id


        
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accstn.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process_stone_details accstn 
        LEFT JOIN ret_acc_stock_process_details accdet ON accdet.id_process_details = accstn.id_process_details
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accdet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accdet.ref_no AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_stone st ON st.stone_id = accstn.stone_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 1 AND st.stone_type = 1
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retagDia ON retagDia.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 2 and r.bill_status = 1 and p.stone_type = 0
        AND date(r.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ret ON op_blc_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 2 and r.bill_status = 1 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        AND date(r.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_ret ON op_blc_grm_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 2 and r.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt=6 
        AND date(r.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_ret ON op_blc_ct_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 2 and r.bill_status = 1 and p.stone_type = 2 
        AND date(r.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_ret ON op_blc_looseDia_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(ret_st.ret_stone_wt),0) as diawt
        FROM ret_purchase_return_stone_items ret_st
        LEFT JOIN ret_purchase_return_items ritm ON ritm.pur_ret_itm_id = ret_st.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = ritm.pur_ret_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = ritm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_stone stn ON stn.stone_id = ret_st.ret_stone_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE ret.tag_issue_from = 2 AND stn.stone_type = 1 and ret.bill_status = 1
        AND date(ret.bill_date)<='".$op_date."'
       ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
       ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retDia ON op_blc_retDia. branch_transfer_id = b.branch_transfer_id

        
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 2 and r.bill_status = 1 and p.stone_type = 0
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ret ON ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 2 and r.bill_status = 1 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_ret ON grm_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 2 and r.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_ret ON ct_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 2 and r.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_ret ON looseDia_ret.branch_transfer_id = b.branch_transfer_id

        

    
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(ret_st.ret_stone_wt),0) as diawt
        FROM ret_purchase_return_stone_items ret_st
        LEFT JOIN ret_purchase_return_items ritm ON ritm.pur_ret_itm_id = ret_st.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = ritm.pur_ret_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = ritm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_stone stn ON stn.stone_id = ret_st.ret_stone_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE ret.tag_issue_from = 2 AND stn.stone_type = 1 and ret.bill_status = 1
        and (date(ret.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retDia ON retDia. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and p.stone_type = 0
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_pocket ON op_blc_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_pocket ON op_blc_grm_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and p.stone_type = 1 and tag.uom_gross_wt = 6 
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_pocket ON op_blc_ct_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and p.stone_type = 2
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_pocket ON op_blc_looseDia_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pks.stone_wt),0) as diawt
        FROM ret_old_metal_pocket_stone_details pks
        LEFT JOIN ret_old_metal_pocket_details pkd on pkd.id_pocket_details = pks.id_pocket_details
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone stn ON stn.stone_id = pks.stone_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and stn.stone_type = 1
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_pocketDia ON op_blc_pocketDia. branch_transfer_id = b.branch_transfer_id
        


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and p.stone_type = 0
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as pocket ON pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_pocket ON grm_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_pocket ON ct_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and p.stone_type = 2  
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_pocket ON looseDia_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pks.stone_wt),0) as diawt
        FROM ret_old_metal_pocket_stone_details pks
        LEFT JOIN ret_old_metal_pocket_details pkd on pkd.id_pocket_details = pks.id_pocket_details
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone stn ON stn.stone_id = pks.stone_id
        WHERE pk.trans_type = 1 and pkd.type = 2 and stn.stone_type = 1
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as pocketDia ON pocketDia. branch_transfer_id = b.branch_transfer_id

    

        WHERE b.transfer_item_type = 3 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
        ".($data['id_branch']!='' ? " and b.transfer_from_branch=".$data['id_branch']."" :'')."");

        //print_r($this->db->last_query());exit;
        
        // PartlySales query
        $receipt_type[3] =  $sql = $this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,br.name as branch_name,date_format(b.dwnload_datetime,'%d-%m-%Y') as bt_date,
        (IFNULL(op_inw.gross_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.gross_wt,0)-IFNULL(op_blc_ret.gross_wt,0)-IFNULL(op_blc_pocket.gross_wt,0)) as op_blc_gwt,
        (IFNULL(op_inw.net_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.net_wt,0)-IFNULL(op_blc_ret.net_wt,0)-IFNULL(op_blc_pocket.net_wt,0)) as op_blc_nwt,
        (IFNULL(op_blc_inw_dia.diawt,0) + IFNULL(op_inw_looseDia.gross_wt,0) - IFNULL(op_blc_retagDia.dia_wt,0) - IFNULL(op_blc_looseDia_retag.gross_wt,0) -IFNULL(op_blc_retDia.diawt,0) -IFNULL(op_blc_looseDia_ret.gross_wt,0) -IFNULL(op_blc_pocketDia.diawt,0) - IFNULL(op_blc_looseDia_pocket.gross_wt,0)) as op_blc_diawt,
        (IFNULL(op_inw_grm.gross_wt,0) - IFNULL(op_blc_grm_metalIssue.issue_wt,0) - IFNULL(op_blc_grm_retag.gross_wt,0) - IFNULL(op_blc_grm_ret.gross_wt,0) - IFNULL(op_blc_grm_pocket.gross_wt,0)) as op_blc_grm_wt,
        (IFNULL(op_inw_ct.gross_wt,0) - IFNULL(op_blc_ct_metalIssue.issue_wt,0) - IFNULL(op_blc_ct_retag.gross_wt,0) - IFNULL(op_blc_ct_ret.gross_wt,0) - IFNULL(op_blc_ct_pocket.gross_wt,0)) as op_blc_ct_wt,
        
        IFNULL(inw.gross_wt,0) as inw_gwt,IFNULL(inw.net_wt,0) as inw_nwt,
        (IFNULL(inw_dia.diawt,0) + IFNULL(looseDia_inw.gross_wt,0)) as inw_diawt,
        IFNULL(grm_inw.gross_wt,0) as inw_grm_wt,IFNULL(ct_inw.gross_wt,0) as inw_ct_wt,
        
        IFNULL(metalIssue.issue_wt,0) as issue_gwt,IFNULL(metalIssue.issue_wt,0) as issue_nwt,
        IFNULL(looseDia_metalIssue.issue_wt,0) as issue_diawt,
        IFNULL(grm_metalIssue.issue_wt,0) as issue_grm_wt,IFNULL(ct_metalIssue.issue_wt,0) as issue_ct_wt,
        
        IFNULL(retag.gross_wt,0) as retag_gwt,IFNULL(retag.net_wt,0) as retag_nwt,
        (IFNULL(retagDia.dia_wt,0) + IFNULL(looseDia_retag.gross_wt,0)) as retag_diawt,
        IFNULL(grm_retag.gross_wt,0) as retag_grm_wt,IFNULL(ct_retag.gross_wt,0) as retag_ct_wt,

        IFNULL(ret.gross_wt,0) as ret_gwt,IFNULL(ret.net_wt,0) as ret_nwt,
        (IFNULL(retDia.diawt,0) + IFNULL(looseDia_ret.gross_wt,0)) as return_diawt,
        IFNULL(grm_ret.gross_wt,0) as ret_grm_wt,IFNULL(ct_ret.gross_wt,0) as ret_ct_wt,

        IFNULL(pocket.gross_wt,0) as pkt_gwt,IFNULL(pocket.net_wt,0) as pkt_nwt,
        (IFNULL(pocketDia.diawt,0) + IFNULL(looseDia_pocket.gross_wt,0)) as pkt_diawt,
        IFNULL(grm_pocket.gross_wt,0) as pkt_grm_wt,IFNULL(ct_pocket.gross_wt,0) as pkt_ct_wt

        FROM ret_branch_transfer b 
        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3 AND date(brch.dwnload_datetime)<='".$op_date."' and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw ON op_inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3 AND date(brch.dwnload_datetime)<='".$op_date."' and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw_grm ON op_inw_grm.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3 AND date(brch.dwnload_datetime)<='".$op_date."' and p.stone_type = 1 and tag.uom_gross_wt=6
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw_ct ON op_inw_ct.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3 AND date(brch.dwnload_datetime)<='".$op_date."' and p.stone_type = 2 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw_looseDia ON op_inw_looseDia.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,(IFNULL(tagst.tag_stwt,0) - IFNULL(SUM(s.wt),0)) as diawt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_bill_details det ON det.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_billing_item_stones s ON s.bill_det_id = det.bill_det_id
        LEFT JOIN ret_billing bill ON bill.bill_id = det.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id

        LEFT JOIN(SELECT SUM(ts.wt) as tag_stwt,t.transfer_id
        FROM ret_taging_stone ts
        LEFT JOIN ret_bill_details bd on bd.tag_id = ts.tag_id
        LEFT JOIN ret_stone st ON st.stone_id = ts.stone_id
        LEFT JOIN ret_billing b on b.bill_id = bd.bill_id
        LEFT JOIN ret_brch_transfer_old_metal t on t.sold_bill_det_id = bd.bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = bd.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE b.bill_status = 1 AND st.stone_type = 1 and bd.is_non_tag = 0 
        AND date(b.bill_date)<='".$op_date."'
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        GROUP BY t.transfer_id) as tagst on tagst.transfer_id = t.transfer_id

        WHERE bill.bill_status = 1  AND st.stone_type = 1 and det.is_non_tag = 0
        AND date(bill.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_blc_inw_dia ON op_blc_inw_dia.transfer_id = b.branch_transfer_id
 

        
        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3 and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as inw ON inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as grm_inw ON grm_inw.transfer_id = b.branch_transfer_id



        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3 and p.stone_type = 1 and tag.uom_gross_wt=6 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as ct_inw ON ct_inw.transfer_id = b.branch_transfer_id



        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3 and p.stone_type = 2  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as looseDia_inw ON looseDia_inw.transfer_id = b.branch_transfer_id



        LEFT JOIN(SELECT t.transfer_id,(IFNULL(tagst.tag_stwt,0) - IFNULL(SUM(s.wt),0)) as diawt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_bill_details det ON det.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_billing_item_stones s ON s.bill_det_id = det.bill_det_id
        LEFT JOIN ret_billing bill ON bill.bill_id = det.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id

        LEFT JOIN(SELECT SUM(ts.wt) as tag_stwt,t.transfer_id
        FROM ret_taging_stone ts
        LEFT JOIN ret_bill_details bd on bd.tag_id = ts.tag_id
        LEFT JOIN ret_stone st ON st.stone_id = ts.stone_id
        LEFT JOIN ret_billing b on b.bill_id = bd.bill_id
        LEFT JOIN ret_brch_transfer_old_metal t on t.sold_bill_det_id = bd.bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = bd.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE b.bill_status = 1 AND st.stone_type = 1 and bd.is_non_tag = 0 
        and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        GROUP BY t.transfer_id) as tagst on tagst.transfer_id = t.transfer_id

        WHERE bill.bill_status = 1  AND st.stone_type = 1 and det.is_non_tag = 0
        and (date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
       ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 0
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_metalIssue ON op_blc_metalIssue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_grm_metalIssue ON op_blc_grm_metalIssue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt=6 
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_ct_metalIssue ON op_blc_ct_metalIssue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type = 2  
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_looseDia_metalIssue ON op_blc_looseDia_metalIssue.transfer_id = b.branch_transfer_id

        
        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type =0
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as metalIssue ON metalIssue.transfer_id = b.branch_transfer_id


         
        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type =1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as grm_metalIssue ON grm_metalIssue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type =1 and tag.uom_gross_wt=6 
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as ct_metalIssue ON ct_metalIssue.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.issue_from = 1 AND k.tag_issue_from = 2 and k.bill_status = 1 and p.stone_type =2  
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as looseDia_metalIssue ON looseDia_metalIssue.transfer_id = b.branch_transfer_id


    
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accDet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 3 AND date(acc.date_add)<='".$op_date."' and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retag ON op_blc_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accDet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 3 AND date(acc.date_add)<='".$op_date."' and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_retag ON op_blc_grm_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accDet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 3 AND date(acc.date_add)<='".$op_date."' and p.stone_type = 1 and tag.uom_gross_wt=6 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_retag ON op_blc_ct_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accDet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 3 AND date(acc.date_add)<='".$op_date."' and p.stone_type = 2  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_retag ON op_blc_looseDia_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accstn.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process_stone_details accstn 
        LEFT JOIN ret_acc_stock_process_details accdet ON accdet.id_process_details = accstn.id_process_details
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accdet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accdet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone st ON st.stone_id = accstn.stone_id
        WHERE acc.type = 3 AND st.stone_type = 1 
        AND date(acc.date_add)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retagDia ON op_blc_retagDia.branch_transfer_id = b.branch_transfer_id


        
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accDet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 3 and p.stone_type = 0
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retag ON retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accDet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 3 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_retag ON grm_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accDet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 3 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_retag ON ct_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accDet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 3 and p.stone_type = 2 
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_retag ON looseDia_retag.branch_transfer_id = b.branch_transfer_id
       

        
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accstn.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process_stone_details accstn 
        LEFT JOIN ret_acc_stock_process_details accdet ON accdet.id_process_details = accstn.id_process_details
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accdet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = accdet.ref_no AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_stone st ON st.stone_id = accstn.stone_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 3 AND st.stone_type = 1
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retagDia ON retagDia.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 3 and r.bill_status = 1
        AND date(r.bill_date)<='".$op_date."' and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ret ON op_blc_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 3 and r.bill_status = 1
        AND date(r.bill_date)<='".$op_date."' and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_ret ON op_blc_grm_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 3 and r.bill_status = 1
        AND date(r.bill_date)<='".$op_date."' and p.stone_type = 1 and tag.uom_gross_wt=6 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_ret ON op_blc_ct_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 3 and r.bill_status = 1
        AND date(r.bill_date)<='".$op_date."' and p.stone_type = 2  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_ret ON op_blc_looseDia_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(ret_st.ret_stone_wt),0) as diawt
        FROM ret_purchase_return_stone_items ret_st
        LEFT JOIN ret_purchase_return_items ritm ON ritm.pur_ret_itm_id = ret_st.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = ritm.pur_ret_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = ritm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_stone stn ON stn.stone_id = ret_st.po_st_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE ret.tag_issue_from = 3 AND stn.stone_type = 1 and ret.bill_status = 1
        AND date(ret.bill_date)<='".$op_date."'
       ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
       ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retDia ON op_blc_retDia. branch_transfer_id = b.branch_transfer_id

        
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 3 and r.bill_status = 1 and p.stone_type = 0
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ret ON ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 3 and r.bill_status = 1 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_ret ON grm_ret.branch_transfer_id = b.branch_transfer_id



        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 3 and r.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_ret ON ct_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = itm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 3 and r.bill_status = 1 and p.stone_type = 2  
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_ret ON looseDia_ret.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(ret_st.ret_stone_wt),0) as diawt
        FROM ret_purchase_return_stone_items ret_st
        LEFT JOIN ret_purchase_return_items ritm ON ritm.pur_ret_itm_id = ret_st.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = ritm.pur_ret_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = ritm.tag_id AND bt.item_type = 3 and bt.is_non_tag = 0
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_stone stn ON stn.stone_id = ret_st.po_st_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE ret.tag_issue_from = 3 AND stn.stone_type = 1 and ret.bill_status = 1
        and (date(ret.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retDia ON retDia. branch_transfer_id = b.branch_transfer_id



        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and p.stone_type = 0
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_pocket ON op_blc_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_pocket ON op_blc_grm_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_pocket ON op_blc_ct_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and p.stone_type = 2  
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_pocket ON op_blc_looseDia_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pks.stone_wt),0) as diawt
        FROM ret_old_metal_pocket_stone_details pks
        LEFT JOIN ret_old_metal_pocket_details pkd on pkd.id_pocket_details = pks.id_pocket_details
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone stn ON stn.stone_id = pks.stone_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and stn.stone_type = 1
        and date(pk.date)<='".$op_date."'  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_pocketDia ON op_blc_pocketDia. branch_transfer_id = b.branch_transfer_id
        


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and p.stone_type = 0
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as pocket ON pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_pocket ON grm_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_pocket ON ct_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pkd.gross_wt),0) as gross_wt,IFNULL(SUM(pkd.net_wt),0) as net_wt
        FROM ret_old_metal_pocket_details pkd
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and p.stone_type = 2  
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_pocket ON looseDia_pocket. branch_transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT brch.branch_transfer_id,IFNULL(SUM(pks.stone_wt),0) as diawt
        FROM ret_old_metal_pocket_stone_details pks
        LEFT JOIN ret_old_metal_pocket_details pkd on pkd.id_pocket_details = pks.id_pocket_details
        LEFT JOIN ret_old_metal_pocket pk on pk.id_metal_pocket = pkd.id_metal_pocket
        LEFT JOIN (SELECT bt.tag_id,bt.transfer_id
        FROM ret_brch_transfer_old_metal bt 
        WHERE bt.item_type = 2 and bt.is_non_tag = 0
        GROUP BY bt.tag_id) as bt on bt.tag_id = pkd.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_taging tag ON tag.tag_id = bt.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone stn ON stn.stone_id = pks.stone_id
        WHERE pk.trans_type = 1 and pkd.type = 3 and stn.stone_type = 1
        and (date(pk.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as pocketDia ON pocketDia. branch_transfer_id = b.branch_transfer_id



        WHERE b.transfer_item_type = 3 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
        ".($data['id_branch']!='' ? " and b.transfer_from_branch=".$data['id_branch']."" :'')."");
        //print_r($this->db->last_query());exit;
        
        //NonTag return query
        $receipt_type[4] = $this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,br.name as branch_name,date_format(b.dwnload_datetime,'%d-%m-%Y') as bt_date,
        (IFNULL(op_inw.gross_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.gross_wt,0)-IFNULL(op_blc_ret.gross_wt,0)) as op_blc_gwt,
        (IFNULL(op_inw.net_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.net_wt,0)-IFNULL(op_blc_ret.net_wt,0)) as op_blc_nwt,
        (IFNULL(op_inw.net_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.net_wt,0)-IFNULL(op_blc_ret.net_wt,0)) as op_blc_nwt,
        (IFNULL(op_blc_inw_dia.diawt,0)-IFNULL(op_blc_retagDia.dia_wt,0)-IFNULL(op_blc_retDia.diawt,0)) as op_blc_diawt,
        '0.000' as op_blc_grm_wt,'0.000' as op_blc_ct_wt,

        IFNULL(inw.gross_wt,0) as inw_gwt,IFNULL(inw.net_wt,0) as inw_nwt,
        IFNULL(inw_dia.diawt,0) as inw_diawt,'0.000' as inw_grm_wt,'0.000' as inw_ct_wt,
        
        IFNULL(metalIssue.issue_wt,0) as issue_gwt,IFNULL(metalIssue.issue_wt,0) as issue_nwt,
        '0.000' as issue_diawt,'0.000' as issue_grm_wt,'0.000' as issue_ct_wt,
        
        IFNULL(retag.gross_wt,0) as retag_gwt,IFNULL(retag.net_wt,0) as retag_nwt,
        IFNULL(retagDia.dia_wt,0) as retag_diawt,'0.000' as retag_grm_wt,'0.000' as retag_ct_wt,
        
        IFNULL(ret.gross_wt,0) as ret_gwt,IFNULL(ret.net_wt,0) as ret_nwt,
        IFNULL(retDia.diawt,0) as return_diawt,'0.000' as ret_grm_wt,'0.000' as ret_ct_wt,

        '0.000' as pkt_gwt,'0.000' as pkt_nwt,'0.000' as pkt_diawt,'0.000' as pkt_grm_wt,'0.000' as pkt_ct_wt
       
        FROM ret_branch_transfer b 
        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch
        
        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 1
        LEFT JOIN ret_bill_details d ON d.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2 AND date(brch.dwnload_datetime)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_inw ON op_inw.transfer_id = b.branch_transfer_id

        

        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 1
        LEFT JOIN ret_bill_details d ON d.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY t.transfer_id) as inw ON inw.transfer_id = b.branch_transfer_id

        
        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(s.wt),0) as diawt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_bill_details det ON det.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_billing_item_stones s ON s.bill_det_id = det.bill_det_id
        LEFT JOIN ret_billing bill ON bill.bill_id = det.bill_id
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE bill.bill_status = 1 AND det.status = 2 AND st.stone_type = 1 and det.is_non_tag = 1
        AND date(bill.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as op_blc_inw_dia ON op_blc_inw_dia.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(s.wt),0) as diawt
        FROM ret_brch_transfer_old_metal t 
        LEFT JOIN ret_bill_details det ON det.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_billing_item_stones s ON s.bill_det_id = det.bill_det_id
        LEFT JOIN ret_billing bill ON bill.bill_id = det.bill_id
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE bill.bill_status = 1 AND det.status = 2 AND st.stone_type = 1 and det.is_non_tag = 1
        and (date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id

        
        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_bill_details det ON det.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.bill_status = 1 and k.nontag_issue_from = 2
        AND date(k.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as op_blc_metalIssue ON op_blc_metalIssue.transfer_id = b.branch_transfer_id

        
        LEFT JOIN (SELECT bt.transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = d.tag_id AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.status = 4
        LEFT JOIN ret_bill_details det ON det.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE k.nontag_issue_from = 2 and k.bill_status = 1
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.transfer_id) as metalIssue ON metalIssue.transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.sold_bill_det_id = accDet.ref_no AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_bill_details det ON det.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        WHERE acc.type = 5 AND date(acc.date_add)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retag ON op_blc_retag.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.sold_bill_det_id = accDet.ref_no AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id 
        LEFT JOIN ret_bill_details d ON d.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 5
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retag ON retag.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accstn.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process_stone_details accstn 
        LEFT JOIN ret_acc_stock_process_details accdet ON accdet.id_process_details = accstn.id_process_details
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accdet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.sold_bill_det_id = accdet.ref_no AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_stone st ON st.stone_id = accstn.stone_id
        LEFT JOIN ret_bill_details d ON d.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 5 AND st.stone_type = 1 
        AND date(acc.date_add)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retagDia ON op_blc_retagDia.branch_transfer_id = b.branch_transfer_id

        
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(accstn.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process_stone_details accstn 
        LEFT JOIN ret_acc_stock_process_details accdet ON accdet.id_process_details = accstn.id_process_details
        LEFT JOIN ret_acc_stock_process acc ON acc.id_process = accdet.id_process
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.sold_bill_det_id = accdet.ref_no AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_stone st ON st.stone_id = accstn.stone_id
        LEFT JOIN ret_bill_details d ON d.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE acc.type = 5 AND st.stone_type = 1
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retagDia ON retagDia.branch_transfer_id = b.branch_transfer_id

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.sold_bill_det_id = itm.bill_det_id AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_details d ON d.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE r.nontag_issue_from = 2 and r.bill_status = 1
        AND date(r.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ret ON op_blc_ret.branch_transfer_id = b.branch_transfer_id

         

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r 
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.sold_bill_det_id = itm.bill_det_id AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        WHERE r.nontag_issue_from = 2 and r.bill_status = 1
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        GROUP BY brch.branch_transfer_id) as ret ON ret.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(ret_st.ret_stone_wt),0) as diawt
        FROM ret_purchase_return_stone_items ret_st
        LEFT JOIN ret_purchase_return_items ritm ON ritm.pur_ret_itm_id = ret_st.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = ritm.pur_ret_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = ritm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_details d ON d.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone stn ON stn.stone_id = ret_st.po_st_id
        WHERE ret.nontag_issue_from = 2 AND stn.stone_type = 1 and ret.bill_status = 1
        AND date(ret.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retDia ON op_blc_retDia. branch_transfer_id = b.branch_transfer_id

        
        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(ret_st.ret_stone_wt),0) as diawt
        FROM ret_purchase_return_stone_items ret_st
        LEFT JOIN ret_purchase_return_items ritm ON ritm.pur_ret_itm_id = ret_st.pur_ret_return_id
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = ritm.pur_ret_id
        LEFT JOIN ret_brch_transfer_old_metal bt ON bt.tag_id = ritm.tag_id AND bt.item_type = 2 and bt.is_non_tag = 1
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id
        LEFT JOIN ret_bill_details d ON d.bill_det_id = bt.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone stn ON stn.stone_id = ret_st.po_st_id
        WHERE ret.nontag_issue_from = 2 AND stn.stone_type = 1 and ret.bill_status = 1
        and (date(ret.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retDia ON retDia. branch_transfer_id = b.branch_transfer_id
    
        WHERE b.transfer_item_type = 3 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
        ".($data['id_branch']!='' ? " and b.transfer_from_branch=".$data['id_branch']."" :'')."");


        //H.o Other Issue Query

        $receipt_type[5] = $this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,br.name as branch_name,date_format(b.dwnload_datetime,'%d-%m-%Y') as bt_date,

        (IFNULL(op_inw.gross_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.gross_wt,0)-IFNULL(op_blc_ret.gross_wt,0)) as op_blc_gwt,
        (IFNULL(op_inw.net_wt,0)-IFNULL(op_blc_metalIssue.issue_wt,0)-IFNULL(op_blc_retag.net_wt,0)-IFNULL(op_blc_ret.net_wt,0)) as op_blc_nwt,
        (IFNULL(op_blc_inw_dia.diawt,0)-IFNULL(op_blc_retagDia.dia_wt,0)-IFNULL(op_blc_retDia.diawt,0)) as op_blc_diawt,
        (IFNULL(op_inw_grm.gross_wt,0) - IFNULL(op_blc_grm_metalIssue.issue_wt,0) - IFNULL(op_blc_grm_retag.gross_wt,0)) as op_blc_grm_wt,
        (IFNULL(op_inw_ct.gross_wt,0) - IFNULL(op_blc_ct_metalIssue.issue_wt,0) - IFNULL(op_blc_ct_retag.gross_wt,0)) as op_blc_ct_wt,

        IFNULL(inw.gross_wt,0) as inw_gwt,IFNULL(inw.net_wt,0) as inw_nwt,
        (IFNULL(inw_dia.diawt,0) + IFNULL(looseDia_inw.gross_wt,0)) as inw_diawt,
        IFNULL(grm_inw.gross_wt,0) as inw_grm_wt,IFNULL(ct_inw.gross_wt,0) as inw_ct_wt,

        IFNULL(metalIssue.issue_wt,0) as issue_gwt,IFNULL(metalIssue.issue_wt,0) as issue_nwt,
        IFNULL(looseDia_metalIssue.issue_wt,0) as issue_diawt,
        IFNULL(grm_metalIssue.issue_wt,0) as issue_grm_wt,IFNULL(ct_metalIssue.issue_wt,0) as issue_ct_wt,

        IFNULL(retag.gross_wt,0) as retag_gwt,IFNULL(retag.net_wt,0) as retag_nwt,
        (IFNULL(retagDia.dia_wt,0) + IFNULL(looseDia_retag.gross_wt,0)) as retag_diawt,
        IFNULL(grm_retag.gross_wt,0) as retag_grm_wt,IFNULL(ct_retag.gross_wt,0) as retag_ct_wt,

        IFNULL(ret.gross_wt,0) as ret_gwt,IFNULL(ret.net_wt,0) as ret_nwt,
        (IFNULL(retDia.diawt,0)+IFNULL(looseDia_ret.gross_wt,0)) as return_diawt,
        IFNULL(grm_ret.gross_wt,0) as ret_grm_wt,IFNULL(ct_ret.gross_wt,0) as ret_ct_wt,

        '0.000' as pkt_gwt,'0.000' as pkt_nwt,'0.000' as pkt_diawt,'0.000' as pkt_grm_wt,'0.000' as pkt_ct_wt

        FROM ret_branch_transfer b 

        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

        

        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3  AND brch.is_other_issue = 1 AND brch.status = 4 and p.stone_type = 0
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as inw ON inw.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3  AND brch.is_other_issue = 1 AND brch.status = 4 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_inw ON grm_inw.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3  AND brch.is_other_issue = 1 AND brch.status = 4 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_inw ON ct_inw.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3  AND brch.is_other_issue = 1 AND brch.status = 4 and p.stone_type = 2  
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_inw ON looseDia_inw.transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(st.wt),0) as diawt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_taging_stone st ON st.tag_id = tag.tag_id
        LEFT JOIN ret_stone s ON s.stone_id = st.stone_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3 AND s.stone_type = 1 AND brch.is_other_issue = 1 AND brch.status = 4
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 and pro.stone_type = 0
        and (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retag ON retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 and pro.stone_type = 1 and (t.uom_gross_wt!=6 or t.uom_gross_wt is null)
        and (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_retag ON grm_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 and pro.stone_type = 1 and t.uom_gross_wt=6 
        and (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_retag ON ct_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 and pro.stone_type = 2  
        and (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_retag ON looseDia_retag.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(s.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_acc_stock_process_stone_details s ON s.id_process_details = d.id_process_details
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_stone sty ON sty.stone_id = s.stone_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND sty.stone_type = 1
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4
        and (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retagDia ON retagDia.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 and r.bill_status = 1 and pro.stone_type = 0
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ret on ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 and r.bill_status = 1 and pro.stone_type = 1 and (t.uom_gross_wt!=6 or t.uom_gross_wt is null)
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_ret on grm_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 and r.bill_status = 1 and pro.stone_type = 1 and t.uom_gross_wt=6 
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_ret on ct_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 and r.bill_status = 1 and pro.stone_type = 2  
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_ret on looseDia_ret.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(s.ret_stone_wt),0) as diawt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_purchase_return_stone_items s ON s.pur_ret_return_id = itm.pur_ret_itm_id
        LEFT JOIN ret_stone st ON st.stone_id = s.ret_stone_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 4 AND st.stone_type = 1 AND brch.is_other_issue = 1 AND brch.status = 4 and r.bill_status =1
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as retDia ON retDia.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id = d.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.status = 4 AND brch.is_other_issue = 1
        LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE k.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 and k.bill_status =1 and p.stone_type = 0
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as metalIssue ON metalIssue.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id = d.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.status = 4 AND brch.is_other_issue = 1
        LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE k.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 and k.bill_status =1 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is  null)
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as grm_metalIssue ON grm_metalIssue.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id = d.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.status = 4 AND brch.is_other_issue = 1
        LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE k.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 and k.bill_status =1 and p.stone_type = 1 and tag.uom_gross_wt=6 
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as ct_metalIssue ON ct_metalIssue.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id = d.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.status = 4 AND brch.is_other_issue = 1
        LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE k.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 and k.bill_status =1 and p.stone_type = 2  
        and (date(k.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as looseDia_metalIssue ON looseDia_metalIssue.branch_transfer_id = b.branch_transfer_id


        

        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4 
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(brch.dwnload_datetime)<='".$op_date."'
        and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_inw ON op_inw.transfer_id = b.branch_transfer_id

        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4 
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(brch.dwnload_datetime)<='".$op_date."'
        and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_inw_grm ON op_inw_grm.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4 
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(brch.dwnload_datetime)<='".$op_date."'
        and p.stone_type = 1 and tag.uom_gross_wt=6 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_inw_ct ON op_inw_ct.transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4 
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(brch.dwnload_datetime)<='".$op_date."'
        and p.stone_type = 2 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_inw_looseDia ON op_inw_looseDia.transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(st.wt),0) as diawt
        FROM ret_brch_transfer_tag_items t 
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_taging_stone st ON st.tag_id = tag.tag_id
        LEFT JOIN ret_stone s ON s.stone_id = st.stone_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4 
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE tag.tag_status = 3 AND brch.is_other_issue = 1 AND brch.status = 4 AND s.stone_type = 1 AND date(brch.dwnload_datetime)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_inw_dia ON op_blc_inw_dia.transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(p.date_add)<='".$op_date."'
        and pro.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retag ON op_blc_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(p.date_add)<='".$op_date."'
        and pro.stone_type = 1 and (t.uom_gross_wt!=6 or t.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_retag ON op_blc_grm_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(p.date_add)<='".$op_date."'
        and pro.stone_type = 1 and t.uom_gross_wt=6 
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_retag ON op_blc_ct_retag.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(p.date_add)<='".$op_date."'
        and pro.stone_type = 2  
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_retag ON op_blc_looseDia_retag.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(s.stone_wt),0) as dia_wt
        FROM ret_acc_stock_process p 
        LEFT JOIN ret_acc_stock_process_details d ON d.id_process = p.id_process
        LEFT JOIN ret_acc_stock_process_stone_details s ON s.id_process_details = d.id_process_details
        LEFT JOIN ret_taging t ON t.tag_id = d.ref_no
        LEFT JOIN ret_stone sty ON sty.stone_id = s.stone_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_product_master pro ON pro.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = pro.cat_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND sty.stone_type = 1
        WHERE p.type = 6 AND brch.is_other_issue = 1 AND brch.status = 4 and date(p.date_add)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and pro.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retagDia ON op_blc_retagDia.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(r.bill_date)<='".$op_date."'
        and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ret on op_blc_ret.branch_transfer_id = b.branch_transfer_id

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(r.bill_date)<='".$op_date."'
        and p.stone_type = 1 and (t.uom_gross_wt!=6 or t.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_ret on op_blc_grm_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(r.bill_date)<='".$op_date."'
        and p.stone_type = 1 and t.uom_gross_wt=6 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_ret on op_blc_ct_ret.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(itm.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(itm.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(r.bill_date)<='".$op_date."'
        and p.stone_type = 2
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_ret on op_blc_looseDia_ret.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(s.ret_stone_wt),0) as diawt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_purchase_return_stone_items s ON s.pur_ret_return_id = itm.pur_ret_itm_id
        LEFT JOIN ret_stone st ON st.stone_id = s.ret_stone_id
        LEFT JOIN ret_taging t ON t.tag_id = itm.tag_id
        LEFT JOIN ret_brch_transfer_tag_items bt ON bt.tag_id = t.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = bt.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4
        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE r.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND st.stone_type = 1 AND date(r.bill_date)<='".$op_date."' and r.bill_status =1
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_retDia ON op_blc_retDia.branch_transfer_id = b.branch_transfer_id

        

        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id = d.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.status = 4 AND brch.is_other_issue = 1
        LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE k.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(k.met_issue_date)<='".$op_date."' 
        and k.bill_status = 1 and p.stone_type = 0
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_metalIssue ON op_blc_metalIssue.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id = d.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.status = 4 AND brch.is_other_issue = 1
        LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE k.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(k.met_issue_date)<='".$op_date."' 
        and k.bill_status = 1 and p.stone_type = 1 and (tag.uom_gross_wt!=6 or tag.uom_gross_wt is null)
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_grm_metalIssue ON op_blc_grm_metalIssue.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id = d.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.status = 4 AND brch.is_other_issue = 1
        LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE k.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(k.met_issue_date)<='".$op_date."' 
        and k.bill_status = 1 and p.stone_type = 1 and tag.uom_gross_wt=6 
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_ct_metalIssue ON op_blc_ct_metalIssue.branch_transfer_id = b.branch_transfer_id


        LEFT JOIN (SELECT brch.branch_transfer_id,IFNULL(SUM(d.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue k 
        LEFT JOIN ret_karigar_metal_issue_details d ON d.issue_met_parent_id = k.met_issue_id
        LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id = d.tag_id
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.status = 4 AND brch.is_other_issue = 1
        LEFT JOIN ret_taging tag ON tag.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        WHERE k.tag_issue_from = 4 AND brch.is_other_issue = 1 AND brch.status = 4 AND date(k.met_issue_date)<='".$op_date."' 
        and k.bill_status = 1 and p.stone_type = 2  
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY brch.branch_transfer_id) as op_blc_looseDia_metalIssue ON op_blc_looseDia_metalIssue.branch_transfer_id = b.branch_transfer_id

        

        WHERE b.is_other_issue = 1 AND b.transfer_item_type = 1 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."

        ".($data['id_branch']!='' ? " and b.transfer_from_branch=".$data['id_branch']."" :'')."");


        //Nontag Other issue query
        $receipt_type[6] = $this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,br.name as branch_name,date_format(b.dwnload_datetime,'%d-%m-%Y') as bt_date,
        (IFNULL(op_inw.gross_wt,0) - IFNULL(op_blc_metalIssue.issue_wt,0) - IFNULL(op_blc_retag.gross_wt,0) - IFNULL(op_blc_ret.gross_wt,0)) as op_blc_gwt,
        (IFNULL(op_inw.net_wt,0) - IFNULL(op_blc_metalIssue.issue_wt,0) - IFNULL(op_blc_retag.net_wt,0) - IFNULL(op_blc_ret.net_wt,0)) as op_blc_nwt,
        '0.000' as op_blc_diawt,'0.000' as op_blc_grm_wt,'0.000' as op_blc_ct_wt,
        
        IFNULL(inw.gross_wt,0) as inw_gwt,IFNULL(inw.net_wt,0) as inw_nwt,
        '0' as inw_diawt,'0.000' as inw_grm_wt,'0.000' as inw_ct_wt,
        
        IFNULL(metalIssue.issue_wt,0) as issue_gwt,IFNULL(metalIssue.issue_wt,0) as issue_nwt,
        '0.000' as issue_diawt,'0.000' as issue_grm_wt,'0.000' as issue_ct_wt,
        
        IFNULL(retag.gross_wt,0) as retag_gwt,IFNULL(retag.net_wt,0) as retag_nwt,
        '0' as retag_diawt,'0.000' as retag_grm_wt,'0.000' as retag_ct_wt,
        
        IFNULL(ret.gross_wt,0) as ret_gwt,IFNULL(ret.net_wt,0) as ret_nwt,
        '0' as ret_diawt,'0.000' as ret_grm_wt,'0.000' as ret_ct_wt,

        '0.000' as pkt_gwt,'0.000' as pkt_nwt,'0.000' as pkt_diawt,'0.000' as pkt_grm_wt,'0.000' as pkt_ct_wt
        
        from ret_branch_transfer b
        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch
        
        LEFT JOIN(SELECT bt.branch_transfer_id,IFNULL(SUM(bnt.grs_wt),0) as gross_wt,IFNULL(SUM(bnt.net_wt),0) as net_wt
        FROM ret_branch_transfer bt 
        LEFT JOIN ret_brch_transfer_non_tag_items bnt on bnt.transfer_id = bt.branch_transfer_id
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id 
        WHERE bt.is_other_issue = 1 and bt.transfer_item_type = 2 and bt.status=4  
        AND date(bt.dwnload_datetime)<='".$op_date."'
       ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
       ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as op_inw on op_inw.branch_transfer_id = b.branch_transfer_id
        
        LEFT JOIN (SELECT bt.branch_transfer_id,IFNULL(SUM(bnt.grs_wt),0) as gross_wt,IFNULL(SUM(bnt.net_wt),0) as net_wt
        FROM ret_branch_transfer bt 
        LEFT JOIN ret_brch_transfer_non_tag_items bnt on bnt.transfer_id = bt.branch_transfer_id
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bnt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id 
        WHERE bt.is_other_issue = 1 and bt.transfer_item_type = 2 and bt.status=4  
        AND (date(bt.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
            ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as inw on inw.branch_transfer_id = b.branch_transfer_id
        
        
        LEFT JOIN(SELECT bt.branch_transfer_id,IFNULL(SUM(kmi.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue km
        LEFT JOIN ret_karigar_metal_issue_details kmi on kmi.issue_met_parent_id = km.met_issue_id
        LEFT JOIN ret_brch_transfer_non_tag_items bnt on bnt.nontag_transfer_id = kmi.branch_trans_id
        LEFT JOIN ret_branch_transfer bt on bt.branch_transfer_id = bnt.transfer_id
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bnt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id 
        WHERE km.nontag_issue_from = 3 and km.bill_status = 1 and kmi.branch_trans_id is not null
        and date(km.met_issue_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as op_blc_metalIssue ON op_blc_metalIssue.branch_transfer_id = b.branch_transfer_id
                  
        LEFT JOIN(SELECT bt.branch_transfer_id,IFNULL(SUM(kmi.issue_metal_wt),0) as issue_wt
        FROM ret_karigar_metal_issue km
        LEFT JOIN ret_karigar_metal_issue_details kmi on kmi.issue_met_parent_id = km.met_issue_id
        LEFT JOIN ret_brch_transfer_non_tag_items bnt on bnt.nontag_transfer_id = kmi.branch_trans_id
        LEFT JOIN ret_branch_transfer bt on bt.branch_transfer_id = bnt.transfer_id
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bnt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id 
        WHERE km.nontag_issue_from = 3 and km.bill_status = 1 and kmi.branch_trans_id is not null
        and (date(km.met_issue_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as metalIssue ON metalIssue.branch_transfer_id = b.branch_transfer_id
                  
        LEFT JOIN (SELECT bt.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc on acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_non_tag_items bnt on bnt.nontag_transfer_id = accDet.ref_no
        LEFT JOIN ret_branch_transfer bt on bt.branch_transfer_id = bnt.transfer_id
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bnt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id 
        WHERE acc.type = 7 AND date(acc.date_add)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as op_blc_retag ON op_blc_retag.branch_transfer_id = b.branch_transfer_id
                            
        LEFT JOIN (SELECT bt.branch_transfer_id,IFNULL(SUM(accDet.gross_wt),0) as gross_wt,IFNULL(SUM(accDet.net_wt),0) as net_wt
        FROM ret_acc_stock_process_details accDet
        LEFT JOIN ret_acc_stock_process acc on acc.id_process = accDet.id_process
        LEFT JOIN ret_brch_transfer_non_tag_items bnt on bnt.nontag_transfer_id = accDet.ref_no
        LEFT JOIN ret_branch_transfer bt on bt.branch_transfer_id = bnt.transfer_id
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bnt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id 
        WHERE acc.type = 7 
        and (date(acc.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')     
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as retag ON retag.branch_transfer_id = b.branch_transfer_id
        
        
        LEFT JOIN (SELECT bt.branch_transfer_id,IFNULL(sum(pri.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(pri.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return pr
        LEFT JOIN ret_purchase_return_items pri on pri.pur_ret_id = pr.pur_return_id
        LEFT JOIN ret_brch_transfer_non_tag_items bnt on bnt.nontag_transfer_id = pri.branch_trans_id
        LEFT JOIN ret_branch_transfer bt on bt.branch_transfer_id = bnt.transfer_id
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bnt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id 
        WHERE (pr.nontag_issue_from = 3 OR pr.nontag_issue_from = 2) and pr.bill_status = 1 and pri.branch_trans_id is not null
        and date(pr.bill_date)<='".$op_date."'
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as op_blc_ret on op_blc_ret.branch_transfer_id = b.branch_transfer_id
        
        LEFT JOIN (SELECT bt.branch_transfer_id,IFNULL(sum(pri.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(pri.pur_ret_nwt),0) as net_wt
        FROM ret_purchase_return pr 
        LEFT JOIN ret_purchase_return_items pri on pri.pur_ret_id = pr.pur_return_id
        LEFT JOIN ret_brch_transfer_non_tag_items bnt on bnt.nontag_transfer_id = pri.branch_trans_id
        LEFT JOIN ret_branch_transfer bt on bt.branch_transfer_id = bnt.transfer_id
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bnt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id 
        WHERE (pr.nontag_issue_from = 3 OR pr.nontag_issue_from = 2) and pr.bill_status = 1 and pri.branch_trans_id is not null
        and (date(pr.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as ret on ret.branch_transfer_id = b.branch_transfer_id
        
        WHERE b.is_other_issue = 1 and b.transfer_item_type = 2 and b.status=4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')." 
        ".($data['id_branch']!='' ? " and b.transfer_from_branch=".$data['id_branch']."" :'')."
        ");
        //echo $this->db->last_query();exit;


        foreach($data['receipt_type'] as $tkey => $tval){
            if($tval == 0){
                $return_array = array_merge( $receipt_type[1]->result_array(), $receipt_type[2]->result_array(),  $receipt_type[3]->result_array(), $receipt_type[4]->result_array(),$receipt_type[5]->result_array(),$receipt_type[6]->result_array());
            }else{
                $return_array = array_merge($return_array, $receipt_type[$tval]->result_array());
            }
        }

         //print_r($this->db->last_query());exit;
        foreach($return_array as $item)
        {
            $closing_gwt   = ($item['op_blc_gwt']+$item['inw_gwt']-$item['issue_gwt']-$item['ret_gwt']-$item['retag_gwt']);

            $closing_nwt   = ($item['op_blc_nwt']+$item['inw_nwt']-$item['issue_gwt']-$item['ret_nwt']-$item['retag_nwt']);

            $closing_diawt = ($item['op_blc_diawt']+$item['inw_diawt']- $item['issue_diawt'] - $item['return_diawt']-$item['retag_diawt']);

            $closing_grm_wt = ($item['op_blc_grm_wt']+$item['inw_grm_wt'] - $item['issue_grm_wt'] - $item['ret_grm_wt'] - $item['retag_grm_wt']);

            $closing_ct_wt = ($item['op_blc_ct_wt']+$item['inw_ct_wt'] - $item['issue_ct_wt'] - $item['ret_ct_wt'] - $item['retag_ct_wt']);

            if($item['op_blc_gwt']>0 || $item['op_blc_diawt']>0 || $item['op_blc_grm_wt']>0 || $item['op_blc_ct_wt']>0
            || $item['inw_gwt'] > 0 || $item['inw_diawt']>0 || $item['inw_grm_wt']>0 || $item['inw_ct_wt']>0 
            || $item['issue_gwt']>0 || $item['issue_diawt']>0 || $item['issue_grm_wt']>0 || $item['issue_ct_wt']>0 
            || $item['ret_gwt']>0 || $item['ret_diawt']>0 || $item['ret_grm_wt']>0 || $item['ret_ct_wt']>0 
            || $item['retag_gwt']>0 || $item['retag_diawt']>0 || $item['retag_grm_wt']>0 || $item['retag_ct_wt']>0)
            {
                $item['closing_gwt'] = number_format($closing_gwt,3,'.','');
                $item['closing_nwt'] = number_format($closing_nwt,3,'.','');
                $item['closing_diawt'] = number_format($closing_diawt,3,'.','');
                $item['closing_grm_wt'] = number_format($clsoing_grm_wt,3,'.','');
                $item['closing_ct_wt'] = number_format($closing_ct_wt,3,'.','');
                $returnData[]=$item;
            }
        }

        return $returnData;
    }
    function get_supplier_sale($data)
    {
        $returnData = [];
        $sql = $this->db->query("SELECT ret.pur_return_id,ret.pur_ret_ref_no,sales_ret.ref_pur_ret_itm_id
        FROM ret_purchase_return_items itm
        LEFT JOIN ret_purchase_return ret ON ret.pur_return_id = itm.pur_ret_id

        LEFT JOIN (select r.ref_pur_ret_itm_id,r.pur_ret_itm_id
        from ret_purchase_return_items r
        group by r.pur_ret_itm_id) as sales_ret ON sales_ret.pur_ret_itm_id = itm.pur_ret_itm_id

        LEFT JOIN ret_purchase_return_items r ON r.ref_pur_ret_itm_id = itm.pur_ret_itm_id
        WHERE ret.bill_status = 1 AND ret.purchase_type = 1 AND ret.pur_ret_supplier_id = ".$data['id_karigar']."
        and sales_ret.ref_pur_ret_itm_id IS NULL
        GROUP BY ret.pur_return_id");
        $result = $sql->result_array();
        foreach($result as $val){
            $returnData[]=array(
                'pur_return_id'=>$val['pur_return_id'],
                'pur_ret_ref_no'=>$val['pur_ret_ref_no'],
                'item_details'  =>$this->get_supplier_bill_details($val['pur_return_id'])
                );
        }
        return $returnData;
    }
    function get_supplier_bill_details($pur_return_id){
        $returnData = [];
        $sql = $this->db->query("SELECT r.pur_ret_id,r.pur_ret_itm_id,r.pur_ret_tax_type as tax_type,r.pur_ret_tax_id as tax_group,c.id_ret_category as cat_id,'' as po_item_id,'' as tag_id,r.calculation_based_on,'' as bill_det_id,'' as branch_trans_id,'' as tag_code,c.name as catname,p.product_name as pro_name,
        p.pro_id as pro_id,d.design_name as des_name,r.id_design as des_id,s.sub_design_name as subDes_name,r.id_sub_design,r.pur_ret_pcs as piece,
        r.pur_ret_gwt as gross_wt,r.pur_ret_lwt as less_wt,r.pur_ret_wastage as va_per,r.pur_ret_mc_type as mc_type,r.pur_ret_mc_value as mc_value,
        r.pur_ret_purchase_touch as pur_touch,'0' as tag_other_itm_grs_weight,r.pur_ret_rate
        FROM ret_purchase_return_items r
        LEFT JOIN ret_product_master p ON p.pro_id = r.id_product
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        LEFT JOIN ret_design_master d ON d.design_no = r.id_design
        LEFT JOIN ret_sub_design_master s ON s.id_sub_design = r.id_sub_design
        WHERE r.pur_ret_id = ".$pur_return_id."");
        $result = $sql->result_array();
        foreach($result as $val){
            $val['stone_details'] = $this->getSupplierstoneDetails($val['pur_ret_itm_id']);
            $val['other_metal_details'] = $this->getSupplierOtherMetalDetails($val['pur_ret_itm_id']);
            $returnData[]=$val;
        }
        return $returnData;
    }

    function getSupplierstoneDetails($pur_ret_itm_id)
    {
        $sql = $this->db->query("SELECT s.stone_type,st.ret_stone_wt as stone_wt,st.ret_stone_amount as stone_price,
        '1' as show_in_lwt,st.ret_stone_uom as stone_uom_id,st.ret_stone_rate as stone_rate,st.ret_stone_pcs as stone_pcs,
        st.ret_stone_calc_based_on as stone_cal_type,st.ret_stone_id as stone_id
        FROM ret_purchase_return_stone_items st
        LEFT JOIN ret_stone s ON s.stone_id = st.ret_stone_id
        WHERE st.pur_ret_return_id = ".$pur_ret_itm_id."");
        return $sql->result_array();
    }

    function getSupplierOtherMetalDetails($pur_ret_itm_id)
    {
        $sql = $this->db->query("SELECT m.ret_other_itm_grs_weight as gwt,m.ret_other_itm_amount as amount
        FROM ret_purchase_return_other_metal m
        WHERE m.pur_ret_return_id = ".$pur_ret_itm_id."");
        return $sql->result_array();
    }

    function get_metal_issue_details($data)
    {
        $sql = $this->db->query("SELECT issue_det.issue_met_id,p.product_name,des.design_name,sub.sub_design_name,
        issue_det.issu_met_pro_id as product_id,issue_det.issu_met_id_design as design_id,sub.id_sub_design,
        tag.piece, tag.less_wt,IFNULL(tag.net_wt,0) as net_wt,IFNULL(tag.gross_wt,0) as gross_wt,tag.tag_id
        FROM ret_karigar_metal_issue_details issue_det
        LEFT JOIN ret_karigar_metal_issue issue ON issue.met_issue_id = issue_det.issue_met_parent_id
        LEFT JOIN ret_product_master p ON p.pro_id= issue_det.issu_met_pro_id
        LEFT JOIN ret_metai_issue_receipt_details receipt ON receipt.issue_met_id = issue_det.issue_met_id 
        LEFT JOIN ret_design_master as des ON des.design_no = issue_det.issu_met_id_design
        LEFT JOIN ret_sub_design_master sub ON sub.id_sub_design= issue_det.issu_met_id_sub_design
        LEFT JOIN ret_taging tag ON tag.tag_id = issue_det.tag_id
        where issue.met_issue_id = ".$data['met_issue_id']." and receipt.issue_met_id  IS NULL
        GROUP BY issue_det.issue_met_id");
        return $sql->result_array();
    }

    function getKarigarIssueRef_No($data)
    {
        $sql = $this->db->query("SELECT kmi.met_issue_id,kmi.met_issue_ref_id,k.firstname as suppliername
        FROM ret_karigar_metal_issue kmi
        LEFT JOIN ret_karigar k ON k.id_karigar = kmi.met_issue_karid
        LEFT JOIN ret_karigar_metal_issue_details kmid on kmid.issue_met_parent_id = kmi.met_issue_id
        LEFT JOIN ret_metai_issue_receipt_details receipt ON receipt.issue_met_id = kmid.issue_met_id 
        LEFT JOIN customerorder po ON po.id_customerorder = kmi.id_order
        WHERE kmi.issue_aganist=1 and kmi.bill_status=1 and po.order_for = 3 and receipt.issue_met_id IS NULL
        GROUP BY kmi.met_issue_id");
        return $sql->result_array();
    }

    function GetFinancialYear()
	{
		$sql=$this->db->query("SELECT fin_year_code,fin_status,fin_year_name From ret_financial_year");
		return $sql->result_array();

	}

    function get_rate_fix_details($id)
    {
        $sql1 = $this->db->query("SELECT rf.rate_fix_id,rf.rate_fix_wt,po.po_ref_no,
        date_format(rf.rate_fix_created_on, '%d-%m-%Y') as date_add,
        IFNULL(rf.rate_fix_rate,0) as rate_fix_rate,IFNULL(rf.total_amount,0) as amount,
        IFNULL(k.firstname,'') as karigar_name,k.contactno1 as mobile,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,
        IFNULL(cy.name,'') as city_name,IFNULL(ct.name,'') as country_name,IFNULL(st.name,'') as state_name,
        IFNULL(emp.firstname,'') as emp,rf.remark,IFNULL(rf.cgst_cost,'') as cgst_cost,IFNULL(rf.sgst_cost,'') as sgst_cost,
        IFNULL(rf.igst_cost,'') as igst_cost,rf.tax_percentage,rf.tax_amount,
        concat(round(abs(((r.fix_rate_per_grm - rf.rate_fix_rate)*rf.rate_fix_wt)*1.03),2)) as crdr_amt,if(r.fix_rate_per_grm > rf.rate_fix_rate,'Debit Note','Credit Note') as crdr,IFNULL(mt.metal,'') as metal,IFNULL(cat.name,'') as category_name,mt.id_metal

        FROM ret_po_rate_fix rf
        LEFT JOIN ret_purchase_order po on po.po_id = rf.rate_fix_po_item_id
        LEFT JOIN ret_purchase_order_items r ON r.po_item_po_id = po.po_id
        LEFT JOIN ret_product_master pro on pro.pro_id = r.po_item_pro_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        LEFT JOIN metal mt on mt.id_metal = cat.id_metal
        LEFT JOIN ret_karigar k on k.id_karigar = po.po_karigar_id
        LEFT JOIN country ct ON ct.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city cy ON cy.id_city=k.id_city
        LEFT JOIN employee emp on emp.id_employee = rf.rate_fix_create_by
        WHERE rf.rate_fix_id = ".$id." and rf.rate_fix_type =1");
        // print_r($this->db->last_query());exit;
        $result1 = $sql1->row_array();

        $sql2 = $this->db->query("SELECT rf.rate_fix_id,rf.rate_fix_wt,IFNULL(src.ref_no,'') as po_ref_no,date_format(rf.rate_fix_created_on, '%d-%m-%Y') as date_add,
        IFNULL(rf.rate_fix_rate,0) as rate_fix_rate,IFNULL(rf.total_amount,0) as amount,
        IFNULL(k.firstname,'') as karigar_name,k.contactno1 as mobile,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,
        IFNULL(cy.name,'') as city_name,IFNULL(ct.name,'') as country_name,IFNULL(st.name,'') as state_name,
        IFNULL(emp.firstname,'') as emp,rf.remark,IFNULL(rf.cgst_cost,'') as cgst_cost,IFNULL(rf.sgst_cost,'') as sgst_cost,
        IFNULL(rf.igst_cost,'') as igst_cost,rf.tax_percentage,rf.tax_amount,
        concat(round(abs(((src.rate_per_gram - rf.rate_fix_rate)*rf.rate_fix_wt)*1.03),2)) as crdr_amt,if(src.rate_per_gram > rf.rate_fix_rate,'Debit Note','Credit Note') as crdr,IFNULL(mt.metal,'') as metal,IFNULL(cat.name,'') as category_name,mt.id_metal
        
        FROM ret_po_rate_fix rf
        LEFT JOIN ret_supplier_rate_cut src on src.id_supplier_rate_cut = rf.id_approval_ratecut
        LEFT JOIN smith_company_op_balance smop on smop.id_smith_company_op_balance = src.op_blc_id
        LEFT JOIN ret_karigar k on k.id_karigar = src.id_karigar
        LEFT JOIN ret_product_master pro on pro.pro_id = src.id_product
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        LEFT JOIN metal mt on mt.id_metal = cat.id_metal
        LEFT JOIN country ct ON ct.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city cy ON cy.id_city=k.id_city
        LEFT JOIN employee emp on emp.id_employee = rf.rate_fix_create_by
        
        Where rf.rate_fix_id=".$id." and  rf.rate_fix_type = 2");
        $result2 = $sql2->row_array();

        $result = array_merge($result1,$result2);
        return $result;
    }


     //Credit Debit Entry Starts

function get_credit_debit_detail($id)

{
    $return_data = array();

    $sql=$this->db->query("SELECT IFNULL(ct.transbillno,'') as transbillno,
    IFNULL(ct.transamount,0) as transamount,IFNULL(ct.naration,'') as remarks,

    date_format(ct.transdate,'%d-%m-%Y') as transdate,IFNULL(k.firstname,'') as karigar,

    ct.supid,ct.crdrid,IFNULL(emp.firstname,'') as emp,IF(ct.transtype = 1,'Credit','Debit') as trans_type,

    k.contactno1 as mobile,k.code_karigar,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,
                             
    IFNULL(k.address3,'') as address3,IFNULL(cy.name,'') as country_name,IFNULL(st.name,'') as state_name,IFNULL(cty.name,'') as city_name,

    IFNULL(k.company,'') as company_name,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,IFNULL(k.address3,'') as address3,

    cy.name as country_name,st.name as state_name,cty.name as city_name, IFNULL(k.gst_number,'') as gst_number,

    if(ct.accountto=1,'Supplier',if(ct.accountto=2,'Smith',if(ct.accountto=3,'Karigar',''))) as accountto

    FROM ret_crdr_note ct

    LEFT JOIN ret_karigar k ON k.id_karigar=ct.supid

    LEFT JOIN employee emp ON emp.id_employee =ct.createdby 

    LEFT JOIN country cy ON cy.id_country = k.id_country

    LEFT JOIN state st ON st.id_state = k.id_state

    LEFT JOIN city cty ON cty.id_city = k.id_city

    WHERE ct.crdrid = ".$id." ");

    // print_r($this->db->last_query());exit;

    return $sql->row_array();
 }

//Credit Debit Entry Ends

function get_issue_received_wt($issue_met_id)
{
    $sql = $this->db->query("SELECT IFNULL(SUM(weight),0) as weight  FROM ret_metai_issue_receipt_details kmi where kmi.issue_met_id =".$issue_met_id );
    return $sql->row_array()['weight'];
}

function get_order_details($issue_met_id)
{
    $sql = $this->db->query("SELECT issue_met_id,ki.id_order,od.id_orderdetails,IFNULL(od.weight,0) as weight
    FROM ret_karigar_metal_issue_details kmi 
    LEFT JOIN ret_karigar_metal_issue ki ON ki.met_issue_id =kmi.issue_met_parent_id
    LEFT JOIN customerorderdetails pod ON pod.id_customerorder = ki.id_order
    LEFT JOIN customerorderdetails od ON od.id_orderdetails = pod.cus_orderdet_ref and od.tag_id = kmi.tag_id
    Where kmi.issue_met_id= ".$issue_met_id ." GROUP BY kmi.issue_met_id");
    return $sql->row_array();
}

function get_tag_status($tag_id)

{

    $sql = $this->db->query("SELECT * from ret_taging where tag_id=" . $tag_id . "");

    return $sql->row_array();
}

function get_opening_metal_stock_list($data)
{

    $stock_data=[];

    $ho = $this->get_headOffice();

    $sql = $this->db->query("SELECT c.id_ret_category as cat_id,c.name as category_name,IFNULL(blc.weight,0) as balance_weight,

    (IFNULL(blc.weight,0)-IFNULL(met_issue.issue_metal_wt,0)-IFNULL(f.fixed_weight,0)) as gross_wt,'0' as no_of_piece,'0' as net_wt,c.tgrp_id,c.id_metal,

    blc.id_product as id_product,p.product_name,'' as design,'' as id_sub_design,'' as section_name,'' as id_section,blc.id_smith_company_op_balance,

    k.firstname as karigar_name 

    FROM smith_company_op_balance blc 

    LEFT JOIN ret_category c ON c.id_ret_category = blc.id_category

    LEFT JOIN ret_product_master p ON p.pro_id = blc.id_product

    LEFT JOIN ret_karigar k ON k.id_karigar = blc.id_karigar

    LEFT JOIN(SELECT IFNULL(SUM(rc.weight),0) as fixed_weight,rc.op_blc_id,IFNULL(SUM(rc.charges_amount),0) as amount

        FROM ret_supplier_rate_cut rc 

        WHERE rc.status = 1 and rc.is_opening_blc = 1

    GROUP BY rc.op_blc_id) as f ON f.op_blc_id = blc.id_smith_company_op_balance

    

    

    LEFT JOIN (SELECT IFNULL(SUM(d.issue_metal_wt),0) as issue_metal_wt,d.issue_cat_id,d.id_smith_company_op_balance

              FROM ret_karigar_metal_issue_details d 

              LEFT JOIN ret_karigar_metal_issue i ON i.met_issue_id = d.issue_met_parent_id

              WHERE i.is_against_opening = 1 and i.bill_status = 1

              GROUP BY d.id_smith_company_op_balance) as met_issue ON met_issue.id_smith_company_op_balance = blc.id_smith_company_op_balance

    WHERE blc.id_category IS NOT NULL AND blc.metal_type = 1

    ".($data['id_karigar']!='' ? " and blc.id_karigar=".$data['id_karigar']."" :'')."

    having gross_wt > 0

    order by blc.id_category ASC");

    $data1 = $sql->result_array();

    foreach($data1 as $r)

    {

        $stock_data[] = array(

            'no_of_piece'    => $r['no_of_piece'],

            'gross_wt'       => $r['gross_wt'],

            'net_wt'         => $r['gross_wt'],

            'id_metal'       => $r['id_metal'],

            'cat_id'         => $r['cat_id'],

            'id_product'     => $r['id_product'],

            'design'         => $r['design'],

            'id_sub_design'  => $r['id_sub_design'],

            'category_name'  => $r['category_name'],

            'product_name'   => $r['product_name'],

            'section_name'   => $r['section_name'],

            'id_section'     => $r['id_section'],

            'karigar_name'   => $r['karigar_name'],

            'id_smith_company_op_balance' => $r['id_smith_company_op_balance'],

            'id_branch'      => $ho['id_branch'],

            'purity'         => $this->getCatPurity($r['cat_id']),

                      

        );

    }

    return $stock_data;

}

function get_hm_stone_accepted_details($po_item_id){
    /*$sql = $this->db->query("SELECT s.qc_passed_pcs,s.qc_passed_wt,st.stone_name,m.uom_name,m.uom_id,i.po_stone_id as stone_id
    FROM ret_po_qc_issue_stone_details s
    LEFT JOIN ret_po_stone_items i ON i.po_st_id = s.po_st_id
    LEFT JOIN ret_stone st ON st.stone_id = i.po_stone_id
    LEFT JOIN ret_uom m ON m.uom_id = i.po_stone_uom
    WHERE s.id_qc_issue_details = ".$id_qc_issue_details."
    HAVING s.qc_passed_wt > 0");*/
    $sql = $this->db->query("SELECT (IFNULL(SUM(s.hm_passed_pcs),0)-IFNULL(lot.stone_pcs,0)) as qc_passed_pcs,
    (IFNULL(SUM(s.hm_passed_wt),0)-IFNULL(lot.stone_wt,0)) as qc_passed_wt,stn.stone_name,stn.stone_type,m.uom_name,
    stn.uom_id,st.po_stone_id,sty.stone_type as stone_type_name,
    IFNULL(st.po_quality_id,'') as po_quality_id,st.is_apply_in_lwt,st.po_stone_rate,st.po_stone_amount,st.po_stone_calc_based_on,
    (IFNULL(SUM(s.hm_passed_pcs),0)-IFNULL(lot.stone_pcs,0)) as act_blc_stn_pcs,
    (IFNULL(SUM(s.hm_passed_wt),0)-IFNULL(lot.stone_wt,0)) as act_blc_stn_wt
    
    FROM ret_po_hm_issue_stone_details s
    LEFT JOIN ret_po_hm_process_details d ON d.hm_receipt_id = s.ret_hm_receipt_id
    LEFT JOIN ret_po_stone_items st ON st.po_st_id = s.po_st_id
    LEFT JOIN ret_stone stn ON stn.stone_id = st.po_stone_id
    LEFT JOIN ret_uom m ON m.uom_id = stn.uom_id
    LEFT JOIN ret_stone_type sty ON sty.id_stone_type = stn.stone_type
    
    LEFT JOIN (SELECT IFNULL(SUM(s.stone_pcs),0) as stone_pcs,IFNULL(SUM(s.stone_wt),0) as stone_wt,s.stone_id
        FROM ret_lot_inwards_stone_detail s
        LEFT JOIN ret_lot_inwards_detail d ON d.id_lot_inward_detail = s.id_lot_inward_detail
        LEFT JOIN ret_purchase_order_items i ON i.po_item_id = d.po_item_id
        where i.po_item_id = ".$po_item_id."
    GROUP BY s.stone_id) as lot ON lot.stone_id = st.po_stone_id
   
    WHERE st.po_item_id = ".$po_item_id."
    GROUP BY st.po_stone_id
    having qc_passed_wt > 0");
    return $sql->result_array();
}
public function karigar_pan_available($pan_number, $id_karigar = null)
    {
        $query = "SELECT kyc_number 
        FROM ret_karigar AS karigar
        LEFT JOIN ret_karigar_kyc AS kar_kyc ON kar_kyc.id_karigar = karigar.id_karigar
        LEFT JOIN ret_kyc_master AS ky ON ky.id_kyc_master = kar_kyc.id_kyc
        WHERE kar_kyc.id_kyc = 1 AND
        kar_kyc.kyc_number = ?";

        if ($id_karigar) {
            $query .= " AND karigar.id_karigar != ?";
            $status = $this->db->query($query, [$pan_number, $id_karigar]);
        } else {
            $status = $this->db->query($query, [$pan_number]);
        }
        // print_r($this->db->last_query());

        if ($status->num_rows() > 0) {
            $return_data = ['status' => false, 'message' => 'PAN Number Already Exists'];
        } else {
            $return_data = ['status' => true, 'message' => ''];
        }

        return $return_data;

    }

    public function karigar_gst_available($gst_number, $id_karigar = null)
	{
        $query = "SELECT kyc_number 
        FROM ret_karigar AS karigar
        LEFT JOIN ret_karigar_kyc AS kar_kyc ON kar_kyc.id_karigar = karigar.id_karigar
        LEFT JOIN ret_kyc_master AS ky ON ky.id_kyc_master = kar_kyc.id_kyc
        WHERE kar_kyc.id_kyc = 5 AND
        kar_kyc.kyc_number = ?";

        if ($id_karigar) {
            $query .= " AND karigar.id_karigar != ?";
            $status = $this->db->query($query, [$gst_number, $id_karigar]);
        } else {
            $status = $this->db->query($query, [$gst_number]);
        }

        if($status->num_rows()>0)
		{
			$return_data=array('status'=>false,'message'=>'GST Number Already Exists');
		}else{
			$return_data=array('status'=>true,'message'=>'');
		}

        return $return_data;
	}

    public function karigar_aadhar_available($aadhar_no, $id_karigar = null)
	{
        $query = "SELECT kyc_number 
        FROM ret_karigar AS karigar
        LEFT JOIN ret_karigar_kyc AS kar_kyc ON kar_kyc.id_karigar = karigar.id_karigar
        LEFT JOIN ret_kyc_master AS ky ON ky.id_kyc_master = kar_kyc.id_kyc
        WHERE kar_kyc.id_kyc = 2 AND
        kar_kyc.kyc_number = ?";

        if ($id_karigar) {
            $query .= " AND karigar.id_karigar != ?";
            $status = $this->db->query($query, [$aadhar_no, $id_karigar]);
        } else {
            $status = $this->db->query($query, [$aadhar_no]);
        }

        if($status->num_rows()>0)
		{
			$return_data=array('status'=>false,'message'=>'AADHAR Number Already Exists');
		}else{
			$return_data=array('status'=>true,'message'=>'');
		}

        return $return_data;
	}

    function getTaggedRefNo()



	{



		$sql = $this->db->query("SELECT po.po_id,po.po_ref_no



		FROM ret_taging t



		LEFT JOIN ret_lot_inwards l on l.lot_no = t.tag_lot_id



		LEFT JOIN ret_purchase_order po on po.po_id = l.po_id



		WHERE t.tag_lot_id is not null and l.po_id is not null



		GROUP BY t.tag_lot_id



		ORDER BY l.lot_no DESC");



		return $sql->result_array();



	}

    function get_po_payment_pending_bills($data)
    {
        $transactions=array();
        if($data['id_karigar']!='')
        {
            $pql = $this->db->query("SELECT * FROM ret_view_supplier_ledger l WHERE l.customer_id IS NOT NULL and l.customer_id = ".$data['id_karigar']."");
            $transactions=$pql->result_array();
        }
    }

    function getPending_payment_po_bills_for_payment($post)
    {
        if($post['opening_checked'] == "0")               
            $sql=$this->db->query("SELECT 
                                    p.po_id,
                                    '' as ratecut,
                                    p.po_ref_no AS referenceno,
                                    DATE_FORMAT(p.po_date,'%d-%m-%Y') AS po_date,
                                    k.firstname AS karigar,
                                    (IFNULL(p.tot_purchase_amt,0) - IFNULL(crdr.dr_amt, 0) + IFNULL(crdr.cr_amt, 0) - IFNULL(paid.paid_amount,0) - IFNULL(pra.return_total, 0) - IFNULL(paid.crdr_adj_total, 0) - IFNULL(paid.ret_adj_total, 0)) AS trans_amount, 'bill' as type
                                FROM ret_purchase_order p
                                LEFT JOIN ret_grn_entry grn ON grn.grn_id = p.po_grn_id
                                LEFT JOIN (
                                    SELECT po_id, 
                                        SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) as dr_amt,
                                        SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) as cr_amt
                                    FROM ret_crdr_note WHERE crdr_status=1 and (po_id is not null and po_id>0)
                                    GROUP BY po_id
                                ) crdr ON crdr.po_id = p.po_id
                                LEFT JOIN (
                                    SELECT bill.po_id, 
                                        SUM(bill.bill_amount) as paid_amount,
                                        IFNULL(SUM(ca_agg.adj_total), 0) AS crdr_adj_total,
                                        IFNULL(SUM(ra_agg.adj_total), 0) AS ret_adj_total
                                    FROM ret_po_bill_payment_details bill
                                    INNER JOIN ret_po_payment pay ON pay.pay_id = bill.pay_id AND pay.pay_status = 1
                                    LEFT JOIN (
                                        SELECT pay_id, SUM(adjusted_amount) AS adj_total
                                        FROM ret_crdr_note_po_adj
                                        GROUP BY pay_id
                                    ) ca_agg ON ca_agg.pay_id = bill.pay_id
                                    LEFT JOIN (
                                        SELECT pay_id, SUM(adjusted_amount) AS adj_total
                                        FROM ret_purchase_return_po_adj
                                        GROUP BY pay_id
                                    ) ra_agg ON ra_agg.pay_id = bill.pay_id
                                    GROUP BY bill.po_id
                                ) paid ON paid.po_id = p.po_id
                                LEFT JOIN (
                                    SELECT po_id, SUM(IFNULL(return_total_cost, 0)) AS return_total
                                    FROM ret_purchase_return
                                    WHERE bill_status = 1 AND po_id IS NOT NULL AND po_id > 0
                                    GROUP BY po_id
                                ) pra ON pra.po_id = p.po_id
                                LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
                                WHERE p.pur_approval_type = 0 and p.bill_status=1 and p.is_approved=1 and p.isratefixed=1 and k.id_karigar IS NOT NULL ".($post['id_karigar']!='' ? " and k.id_karigar=".$post['id_karigar']."" :'')."
                                ".(isset($post['grn_type']) && $post['grn_type']!='' ? " and grn.grn_type=".$post['grn_type']."" :'')."
                                GROUP BY p.po_id HAVING trans_amount > 0
                                union
                                SELECT 
                                    p.po_id,
                                    src.id_supplier_rate_cut as ratecut,
                                    concat(p.po_ref_no,' - ',src.ref_no) AS referenceno,
                                    DATE_FORMAT(src.date_add,'%d-%m-%Y') AS po_date,
                                    k.firstname AS karigar,
                                    (IFNULL(src.amount,0) - IFNULL(crdr.dr_amt, 0) + IFNULL(crdr.cr_amt, 0) - IFNULL(paid.paid_amount,0) - IFNULL(paid.crdr_adj_total, 0)) AS trans_amount, 'ratecut' as type
                                FROM ret_supplier_rate_cut src
                                LEFT JOIN ret_purchase_order p on p.po_id = src.po_id
                                LEFT JOIN ret_grn_entry grn ON grn.grn_id = p.po_grn_id
                                LEFT JOIN (
                                    SELECT id_supplier_rate_cut, 
                                        SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) as dr_amt,
                                        SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) as cr_amt
                                    FROM ret_crdr_note WHERE crdr_status=1 and (id_supplier_rate_cut is not null and id_supplier_rate_cut>0)
                                    GROUP BY id_supplier_rate_cut
                                ) crdr ON crdr.id_supplier_rate_cut = src.id_supplier_rate_cut
                                LEFT JOIN (
                                    SELECT bill.id_supplier_rate_cut, 
                                        SUM(bill.bill_amount) as paid_amount,
                                        IFNULL(SUM(ca_agg.adj_total), 0) AS crdr_adj_total
                                    FROM ret_po_bill_payment_details bill
                                    INNER JOIN ret_po_payment pay ON pay.pay_id = bill.pay_id AND pay.pay_status = 1
                                    LEFT JOIN (
                                        SELECT pay_id, SUM(adjusted_amount) AS adj_total
                                        FROM ret_crdr_note_po_adj
                                        GROUP BY pay_id
                                    ) ca_agg ON ca_agg.pay_id = bill.pay_id
                                    GROUP BY bill.id_supplier_rate_cut
                                ) paid ON paid.id_supplier_rate_cut = src.id_supplier_rate_cut
                                LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
                                LEFT JOIN metal c ON c.id_metal =  src.id_metal
                                WHERE p.pur_approval_type = 0 and p.bill_status=1 and p.isratefixed=0 and p.is_approved=1 and src.status=1 and src.conversion_type = 1 and src.id_karigar IS NOT NULL ".($post['id_karigar']!='' ? " and src.id_karigar=".$post['id_karigar']."" :'')."
                                ".(isset($post['grn_type']) && $post['grn_type']!='' ? " and grn.grn_type=".$post['grn_type']."" :'')."
                                GROUP BY src.id_supplier_rate_cut HAVING trans_amount > 0
                                union
                                SELECT 
                                    rf.rate_fix_id as po_id,
                                    rf.rate_fix_id as ratecut,
                                    CONCAT(po.po_ref_no, ' - RF-', rf.rate_fix_id) AS referenceno,
                                    DATE_FORMAT(rf.rate_fix_created_on,'%d-%m-%Y') AS po_date,
                                    k.firstname AS karigar,
                                    (IFNULL(rf.total_amount,0) - IFNULL(paid.paid_amount,0)) AS trans_amount, 'rate_fix' as type
                                FROM ret_po_rate_fix rf
                                LEFT JOIN ret_purchase_order po ON po.po_id = rf.rate_fix_po_item_id
                                LEFT JOIN ret_karigar k ON k.id_karigar = po.po_karigar_id
                                LEFT JOIN (
                                    SELECT bill.rate_fix_id, SUM(bill.bill_amount) as paid_amount
                                    FROM ret_po_bill_payment_details bill
                                    INNER JOIN ret_po_payment pay ON pay.pay_id = bill.pay_id AND pay.pay_status = 1
                                    WHERE bill.rate_fix_id IS NOT NULL AND bill.rate_fix_id > 0
                                    GROUP BY bill.rate_fix_id
                                ) paid ON paid.rate_fix_id = rf.rate_fix_id
                                WHERE rf.is_approved = 1 AND rf.bill_status = 1 AND rf.rate_fix_type = 1 AND k.id_karigar IS NOT NULL ".($post['id_karigar']!='' ? " and po.po_karigar_id=".$post['id_karigar']."" :'')."
                                GROUP BY rf.rate_fix_id HAVING trans_amount > 0
                                union
                                SELECT 
                                    rf.rate_fix_id as po_id,
                                    rc.id_supplier_rate_cut as ratecut,
                                    CONCAT(IFNULL(po.po_ref_no,''), ' - ', rc.ref_no, ' - RF-', rf.rate_fix_id) AS referenceno,
                                    DATE_FORMAT(rf.rate_fix_created_on,'%d-%m-%Y') AS po_date,
                                    k.firstname AS karigar,
                                    (IFNULL(rf.total_amount,0) - IFNULL(paid.paid_amount,0)) AS trans_amount, 'rate_fix' as type
                                FROM ret_po_rate_fix rf
                                LEFT JOIN ret_supplier_rate_cut rc ON rc.id_supplier_rate_cut = rf.id_approval_ratecut
                                LEFT JOIN ret_purchase_order po ON po.po_id = rc.po_id
                                LEFT JOIN ret_karigar k ON k.id_karigar = rc.id_karigar
                                LEFT JOIN (
                                    SELECT bill.rate_fix_id, SUM(bill.bill_amount) as paid_amount
                                    FROM ret_po_bill_payment_details bill
                                    INNER JOIN ret_po_payment pay ON pay.pay_id = bill.pay_id AND pay.pay_status = 1
                                    WHERE bill.rate_fix_id IS NOT NULL AND bill.rate_fix_id > 0
                                    GROUP BY bill.rate_fix_id
                                ) paid ON paid.rate_fix_id = rf.rate_fix_id
                                WHERE rf.bill_status = 1 AND rf.rate_fix_type = 2 AND rc.conversion_type = 2 AND rc.status = 1 AND k.id_karigar IS NOT NULL ".($post['id_karigar']!='' ? " and rc.id_karigar=".$post['id_karigar']."" :'')."
                                GROUP BY rf.rate_fix_id HAVING trans_amount > 0
                                union
                                SELECT 
                                    cn.crdrid as po_id,
                                    '' as ratecut,
                                    cn.transbillno AS referenceno,
                                    DATE_FORMAT(cn.transdate,'%d-%m-%Y') AS po_date,
                                    k.firstname AS karigar,
                                    (IFNULL(cn.transamount, 0) - IFNULL(cr_paid.paid_amount, 0)) AS trans_amount, 'crdr_credit' as type
                                FROM ret_crdr_note cn
                                LEFT JOIN ret_karigar k ON k.id_karigar = cn.supid
                                LEFT JOIN (
                                    SELECT bpd.crdrid, SUM(bpd.bill_amount) as paid_amount
                                    FROM ret_po_bill_payment_details bpd
                                    INNER JOIN ret_po_payment pp ON pp.pay_id = bpd.pay_id AND pp.pay_status = 1
                                    WHERE bpd.crdrid IS NOT NULL AND bpd.crdrid > 0
                                    GROUP BY bpd.crdrid
                                ) cr_paid ON cr_paid.crdrid = cn.crdrid
                                WHERE cn.crdr_status = 1 AND cn.transtype = 1 AND (cn.po_id IS NULL OR cn.po_id = 0) AND (cn.id_supplier_rate_cut IS NULL OR cn.id_supplier_rate_cut = 0) AND k.id_karigar IS NOT NULL ".(isset($post['bill_type']) && $post['bill_type']=='2' ? " and cn.accountto = 2" : " and cn.accountto = 1").($post['id_karigar']!='' ? " and cn.supid=".$post['id_karigar']."" :'')."
                                HAVING trans_amount > 0");
        else
            $sql=$this->db->query("SELECT op.id_smith_company_op_balance as po_id,
                                        'OPENING' as ratecut,
                                        concat('OPENING',' - ',op.ref_no) as referenceno, 
                                        DATE_FORMAT(op.createdon,'%d-%m-%Y') AS po_date, 
                                        k.firstname AS karigar, 
                                        (IFNULL(op.amount,0) - IFNULL(crdr.total_transamount,0)) AS trans_amount, 
                                        'opening' as type 
                                        FROM `smith_company_op_balance` op 
                                        LEFT JOIN ret_karigar k on k.id_karigar = op.id_karigar
                                        LEFT JOIN (
                                            SELECT bill_det.opening_id, SUM(bill_det.bill_amount) AS total_transamount
                                            FROM ret_po_bill_payment_details bill_det
                                            LEFT JOIN ret_po_payment p on p.pay_id=bill_det.pay_id where p.pay_status=1
                                            GROUP BY opening_id
                                        ) crdr ON crdr.opening_id = op.id_smith_company_op_balance
                                WHERE k.id_karigar IS NOT NULL ".($post['id_karigar']!='' ? " and op.id_karigar=".$post['id_karigar']."" :'')."
                                ");
        return $sql->result_array();
    }

    function getPending_payment_po_bills($post)
    {
        $sql=$this->db->query("SELECT 
                                    p.po_id,
                                    '' as ratecut,
                                    p.po_ref_no AS referenceno,
                                    DATE_FORMAT(p.po_date,'%d-%m-%Y') AS po_date,
                                    k.firstname AS karigar,
                                    ord.catname AS category_name,
                                    (IFNULL(p.tot_purchase_amt,0) - (IFNULL(crdr.dr_amt, 0) + IFNULL(crdr.cr_amt, 0)) - IFNULL(paid.paid_amount,0)) AS trans_amount,
                                    p.tot_purchase_wt,
                                    p.is_approved AS approved_status,
                                    p.bill_status
                                FROM ret_purchase_order p
                                LEFT JOIN ret_grn_entry grn ON grn.grn_id = p.po_grn_id
                                LEFT JOIN (
                                    SELECT po_id, 
                                        SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) as dr_amt,
                                        SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) as cr_amt
                                    FROM ret_crdr_note where crdr_status=1
                                    GROUP BY po_id
                                ) crdr ON crdr.po_id = p.po_id
                                LEFT JOIN (
                                    SELECT bill.po_id, SUM(bill.bill_amount) as paid_amount
                                    FROM ret_po_bill_payment_details bill
                                    LEFT JOIN ret_po_payment pay ON
                                    pay.pay_id = bill.pay_id
                                    WHERE pay.pay_status = 1
                                    GROUP BY bill.po_id
                                ) paid ON paid.po_id = p.po_id
                                LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
                                LEFT JOIN (
                                    SELECT 
                                        d.po_item_po_id,
                                        GROUP_CONCAT(DISTINCT c.name) AS catname,
                                        GROUP_CONCAT(DISTINCT pur.purity) AS purity,
                                        IFNULL(SUM(d.gross_wt),0) AS gross_wt,
                                        IFNULL(SUM(d.no_of_pcs),0) AS tot_pcs,
                                        IFNULL(SUM(d.less_wt),0) AS tot_lwt,
                                        IFNULL(SUM(d.net_wt),0) AS tot_nwt
                                    FROM ret_purchase_order_items d
                                    LEFT JOIN ret_category c ON c.id_ret_category = d.po_item_cat_id
                                    LEFT JOIN ret_purity pur ON pur.id_purity = d.id_purity
                                    GROUP BY d.po_item_po_id
                                ) ord ON ord.po_item_po_id = p.po_id
                                WHERE p.pur_approval_type = 0 and p.bill_status=1 and p.isratefixed=1 and p.is_approved=1 and k.id_karigar IS NOT NULL ".($post['id_karigar']!='' ? " and k.id_karigar=".$post['id_karigar']."" :'')."
                                ".(isset($post['grn_type']) && $post['grn_type']!='' ? " and grn.grn_type=".$post['grn_type']."" :'')."
                                GROUP BY p.po_id HAVING trans_amount > 0
                                union
                                SELECT 
                                    p.po_id,
                                    src.id_supplier_rate_cut as ratecut,
                                    concat(p.po_ref_no,' - ',src.ref_no) AS referenceno,
                                    DATE_FORMAT(src.date_add,'%d-%m-%Y') AS po_date,
                                    k.firstname AS karigar,
                                    c.metal AS category_name,
                                    (IFNULL(src.amount,0) - (IFNULL(crdr.dr_amt, 0) + IFNULL(crdr.cr_amt, 0)) - IFNULL(paid.paid_amount,0)) AS trans_amount,
                                    p.tot_purchase_wt,
                                    p.is_approved AS approved_status,
                                    p.bill_status
                                FROM ret_supplier_rate_cut src
                                LEFT JOIN ret_purchase_order p on p.po_id = src.po_id
                                LEFT JOIN ret_grn_entry grn ON grn.grn_id = p.po_grn_id
                                LEFT JOIN (
                                    SELECT id_supplier_rate_cut, 
                                        SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) as dr_amt,
                                        SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) as cr_amt
                                    FROM ret_crdr_note where crdr_status=1
                                    GROUP BY id_supplier_rate_cut
                                ) crdr ON crdr.id_supplier_rate_cut = src.id_supplier_rate_cut
                                LEFT JOIN (
                                    SELECT bill.id_supplier_rate_cut, SUM(bill.bill_amount) as paid_amount
                                    FROM ret_po_bill_payment_details bill
                                    LEFT JOIN ret_po_payment pay ON
                                    pay.pay_id = bill.pay_id
                                    WHERE pay.pay_status = 1
                                    GROUP BY bill.id_supplier_rate_cut
                                ) paid ON paid.id_supplier_rate_cut = src.id_supplier_rate_cut
                                LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
                                LEFT JOIN metal c ON c.id_metal =  src.id_metal
                                WHERE p.pur_approval_type = 0 and p.bill_status=1 and p.isratefixed=0 and src.status=1 and p.is_approved=1 and src.conversion_type = 1 and src.id_karigar IS NOT NULL ".($post['id_karigar']!='' ? " and src.id_karigar=".$post['id_karigar']."" :'')."
                                ".(isset($post['grn_type']) && $post['grn_type']!='' ? " and grn.grn_type=".$post['grn_type']."" :'')."
                                GROUP BY src.id_supplier_rate_cut HAVING trans_amount > 0");
        return $sql->result_array();
    }

    function get_pending_po_bills_after_tagging_without_pcs_with_weight($post)
    {
        $sql = $this->db->query("SELECT
                                    SUM(lot.no_of_piece) AS tot_pcs,
                                    SUM(IFNULL(tag.piece, 0)) AS tagged_pcs,
                                    SUM(IFNULL(tag.gross_wt, 0)) AS tagged_wt,
                                    SUM(lot.gross_wt) AS gross_wt,
                                    ifnull(crdr.dr_wt, 0) as dr_wt,
                                    ifnull(crdr.cr_wt, 0) as cr_wt,
                                    '' AS ratecut,
                                    (
                                        SUM(lot.no_of_piece) - SUM(IFNULL(tag.piece, 0))
                                    ) AS remining_pcs,
                                    (
                                        SUM(lot.gross_wt) - IFNULL(tag.gross_wt, 0) - IFNULL(crdr.dr_wt, 0) + IFNULL(crdr.cr_wt, 0)
                                    ) AS remining_wt,
                                    SUM(lot.net_wt) AS net_wt,
                                    p.product_name,
                                    i.lot_no,
                                    k.firstname AS karigar_name,
                                    DATE_FORMAT(i.lot_date, '%d-%m-%Y') AS lot_date,
                                    IFNULL(i.po_id, '') AS po_id,
                                    IFNULL(pur.po_ref_no, '') AS referenceno,
                                    CONCAT(e.firstname, '-', e.emp_code) AS emp_name,
                                    i.is_closed,
                                    i.stock_type,
                                    i.lot_status,
                                    SUM(IFNULL(lot.pur_wt, 0)) AS pure_wt
                                FROM
                                    ret_lot_inwards i
                                LEFT JOIN ret_lot_inwards_detail lot ON
                                    lot.lot_no = i.lot_no
                                LEFT JOIN ret_product_master p ON
                                    p.pro_id = lot.lot_product
                                LEFT JOIN ret_category cat ON
                                    cat.id_ret_category = i.id_category
                                LEFT JOIN ret_karigar k ON
                                    k.id_karigar = i.gold_smith
                                LEFT JOIN ret_purchase_order pur ON
                                    pur.po_id = i.po_id
                                LEFT JOIN (
                                    SELECT po_id, 
                                    SUM(CASE WHEN transtype = 2 THEN weight ELSE 0 END) as dr_wt,
                                    SUM(CASE WHEN transtype = 1 THEN weight ELSE 0 END) as cr_wt
                                    FROM ret_crdr_note where crdr_status=1
                                    GROUP BY po_id
                                ) crdr ON crdr.po_id = pur.po_id
                                LEFT JOIN ret_grn_entry g ON
                                    g.grn_id = i.grn_id
                                LEFT JOIN ret_old_metal_process r ON
                                    r.id_old_metal_process = i.id_metal_process
                                LEFT JOIN employee e ON
                                    e.id_employee = i.created_by
                                LEFT JOIN(
                                    SELECT tag_lot_id,
                                        SUM(piece) AS piece,
                                        SUM(gross_wt) AS gross_wt
                                    FROM
                                        ret_taging
                                    GROUP BY
                                        tag_lot_id
                                ) tag
                                ON
                                    tag.tag_lot_id = lot.lot_no
                                WHERE
                                    k.id_karigar IS NOT NULL ".($post['id_karigar']!='' ? " and k.id_karigar=".$post['id_karigar']."" :'')." ".(isset($post['grn_type']) && $post['grn_type']!='' ? " and g.grn_type=".$post['grn_type']."" :'')." and i.lot_from = '2' and pur.is_approved=1 and pur.bill_status=1
                                GROUP BY
                                    pur.po_id HAVING tagged_pcs = tot_pcs AND (gross_wt - tagged_wt - dr_wt + cr_wt) <> 0;");
                        // print_r($this->db->last_query());exit;
        return $sql->result_array();
    } 
    function get_approval_po_bills($post)
    {
        $sql=$this->db->query("SELECT 
                p.po_id,
                p.po_ref_no as referenceno,
                IFNULL(p.tot_purchase_wt, 0) as tot_purchase_wt,
                IFNULL(p.tot_purchase_amt, 0) as tot_purchase_amt,
                m.id_metal,
                m.metal,
                (IFNULL(p.tot_purchase_wt, 0) - IFNULL(issued.tot_issued_wt, 0) - IFNULL(rc.tot_rc_wt, 0) - IFNULL(crdr.dr_wt, 0) + IFNULL(crdr.cr_wt, 0) - IFNULL(rtn_itms.pur_ret_pur_wt, 0)) as balance_wt,
                (IFNULL(p.tot_purchase_amt, 0) - IFNULL(crdr.dr_amt, 0) + IFNULL(crdr.cr_amt, 0)) as balance_amt
            FROM ret_purchase_order p
            JOIN ret_purchase_order_items pi ON pi.po_item_po_id = p.po_id
            LEFT JOIN (select IFNULL(sum(rtn_itm.pur_ret_pur_wt), 0) as pur_ret_pur_wt,rtn_itm.pur_ret_po_item_id FROM
                ret_purchase_return_items rtn_itm
                LEFT JOIN ret_purchase_return rtn
                    ON rtn.pur_return_id = rtn_itm.pur_ret_id
                WHERE rtn.bill_status = 1 group by rtn.pur_return_id) rtn_itms
                ON rtn_itms.pur_ret_po_item_id = pi.po_item_id
            JOIN ret_category cat ON cat.id_ret_category = pi.po_item_cat_id
            JOIN metal m ON m.id_metal = cat.id_metal
            LEFT JOIN (
                SELECT km.po_id, SUM(kmd.issue_metal_pur_wt) as tot_issued_wt
                FROM ret_karigar_metal_issue km
                JOIN ret_karigar_metal_issue_details kmd ON kmd.issue_met_parent_id = km.met_issue_id
                WHERE km.bill_status = 1 and km.met_issue_karid IS NOT NULL ".($post['karigar']!='' ? " and km.met_issue_karid = ".$post['karigar']." " :" ")."
                GROUP BY km.po_id
            ) issued ON issued.po_id = p.po_id
            LEFT JOIN (
                SELECT po_id, SUM(weight) as tot_rc_wt
                FROM ret_supplier_rate_cut
                WHERE status = 1 and id_karigar IS NOT NULL ".($post['karigar']!='' ? " and id_karigar = ".$post['karigar']." " :" ")."
                GROUP BY po_id
            ) rc ON rc.po_id = p.po_id
            LEFT JOIN (
                SELECT po_id, 
                    SUM(CASE WHEN transtype = 2 THEN weight ELSE 0 END) as dr_wt,
                    SUM(CASE WHEN transtype = 1 THEN weight ELSE 0 END) as cr_wt,
                    SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) as dr_amt,
                    SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) as cr_amt
                FROM ret_crdr_note
                WHERE ".($post['karigar']!='' ? "supid = ".$post['karigar']." " :" ")." AND crdr_status = 1
                GROUP BY po_id
            ) crdr ON crdr.po_id = p.po_id
            WHERE p.pur_approval_type = 0 and p.isratefixed=0 and p.bill_status=1 and p.is_approved=1 and p.po_karigar_id IS NOT NULL ".($post['karigar']!='' ? " and p.po_karigar_id=".$post['karigar']."" :'')." GROUP BY p.po_id HAVING balance_wt != 0 ");
                                // print_r($this->db->last_query());
        return $sql->result_array();
    }

    function get_po_details_by_ref_no($post)
    {
        $sql=$this->db->query("SELECT
            p.po_id,
            p.po_ref_no AS referenceno,
            IFNULL(p.tot_purchase_wt, 0) as purewt,
            IFNULL(tag_remin.remining_wt, 0) as remining_wt,
            IFNULL(crdr.dr, 0) as dr,
            IFNULL(crdr.cr, 0) as cr,
            IFNULL(sum(rate_cut.weight), 0) as ratecut,
            IFNULL(issued.issued_wt, 0) as issued_wt,
            (
                IFNULL(p.tot_purchase_wt, 0)
                - IFNULL(crdr.dr, 0)
                + IFNULL(crdr.cr, 0)
                - IFNULL(sum(rate_cut.weight), 0)
                - IFNULL(issued.issued_wt, 0)
                - IFNULL(rtn_itms.pur_ret_pur_wt, 0)
            ) AS balance,

            DATE_FORMAT(p.po_date, '%d-%m-%Y') AS date,
            k.firstname AS karigar,

            (
                IFNULL(p.tot_purchase_amt, 0)
                - IFNULL(crdr.total_dr_amount, 0)
                + IFNULL(crdr.total_cr_amount, 0)
            ) AS amount,

            'bill' AS TYPE

        FROM ret_purchase_order p

        LEFT JOIN ret_supplier_rate_cut rate_cut 
            ON rate_cut.po_id = p.po_id and rate_cut.status = 1
        LEFT JOIN ret_purchase_order_items po_itm 
            ON po_itm.po_item_po_id = p.po_id
        LEFT JOIN (select IFNULL(sum(rtn_itm.pur_ret_pur_wt), 0) as pur_ret_pur_wt,rtn_itm.pur_ret_po_item_id FROM
            ret_purchase_return_items rtn_itm
            LEFT JOIN ret_purchase_return rtn
                ON rtn.pur_return_id = rtn_itm.pur_ret_id
            WHERE rtn.bill_status = 1 group by rtn.pur_return_id) rtn_itms
            ON rtn_itms.pur_ret_po_item_id = po_itm.po_item_id
        LEFT JOIN ret_karigar k
            ON k.id_karigar = p.po_karigar_id
        LEFT JOIN (
            SELECT
                po_id,
                IFNULL(SUM(CASE 
                    WHEN transtype = 1 THEN weight 
                    ELSE 0 
                END), 0) AS cr,

                IFNULL(SUM(CASE 
                    WHEN transtype = 2 THEN weight 
                    ELSE 0 
                END), 0) AS dr,
                IFNULL(SUM(CASE 
                    WHEN transtype = 1 THEN transamount 
                    ELSE 0 
                END), 0) AS total_cr_amount,

                IFNULL(SUM(CASE 
                    WHEN transtype = 2 THEN transamount 
                    ELSE 0 
                END), 0) AS total_dr_amount
            FROM ret_crdr_note
            WHERE crdr_status = 1
            GROUP BY po_id
        ) crdr
            ON crdr.po_id = p.po_id
        LEFT JOIN (
            SELECT
                m.po_id,
                IFNULL(SUM(md.issue_metal_pur_wt), 0) AS issued_wt
            FROM ret_karigar_metal_issue m
            LEFT JOIN ret_karigar_metal_issue_details md ON md.issue_met_parent_id = m.met_issue_id
            WHERE m.bill_status = 1
            GROUP BY m.po_id
        ) issued ON issued.po_id = p.po_id
        LEFT JOIN (
            SELECT
                i.po_id,
                IFNULL(
                    SUM(lot.gross_wt) - SUM(tag.gross_wt),
                    0
                ) AS remining_wt
            FROM ret_lot_inwards i

            LEFT JOIN ret_lot_inwards_detail lot
                ON lot.lot_no = i.lot_no

            LEFT JOIN ret_karigar k2
                ON k2.id_karigar = i.gold_smith

            LEFT JOIN ret_taging tag
                ON tag.tag_lot_id = lot.lot_no

            WHERE
                i.lot_from = '2'
                AND i.po_id IS NOT NULL

                ".($post['karigar'] != '' ? " AND k2.id_karigar = ".$post['karigar'] : "")."

                ".($post['po_num'] != '' ? " AND i.po_id = ".$post['po_num'] : "")."

            GROUP BY i.po_id
            HAVING IFNULL(SUM(lot.gross_wt) - SUM(tag.gross_wt), 0) <> 0
        ) tag_remin
            ON tag_remin.po_id = p.po_id

        WHERE
            p.pur_approval_type = 0
            AND p.isratefixed = 0
            AND p.is_approved=1
            AND p.bill_status=1
            AND p.po_karigar_id IS NOT NULL

            ".($post['po_num'] != '' ? " AND p.po_id = ".$post['po_num'] : "")."
            
            ".($post['karigar'] != '' ? " AND p.po_karigar_id = ".$post['karigar'] : "")." GROUP BY p.po_id");
        // print_r($this->db->last_query());
        return $sql->result_array();
    }

    function get_gold_rate()
    {
        $this->db->select('goldrate_24ct');
        $this->db->from('metal_rates');
        $this->db->order_by('id_metalrates', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->goldrate_24ct;
        }
        return '';
    }

    function check_cheque_number_exist($cheque_no)
    {
        $this->db->where('cheque_no', $cheque_no);
        $query = $this->db->get('ret_po_payment_detail');
        if ($query->num_rows() > 0) {
            return true;
        }
        else
        {
             return false;
        }
    }

    function get_po_ratecut_balance_details($po_id)
    {
        $po_id = $this->db->escape_str($po_id);
        $sql = $this->db->query("SELECT 
                p.po_id,
                p.po_ref_no,
                IFNULL(p.tot_purchase_wt, 0) as tot_purchase_wt,
                IFNULL(p.tot_purchase_amt, 0) as tot_purchase_amt,
                m.id_metal,
                m.metal,
                (IFNULL(p.tot_purchase_wt, 0) - IFNULL(issued.tot_issued_wt, 0) - IFNULL(rc.tot_rc_wt, 0) - IFNULL(crdr.dr_wt, 0) + IFNULL(crdr.cr_wt, 0) - IFNULL(rtn_itms.pur_ret_pur_wt, 0)) as balance_wt,
                (IFNULL(p.tot_purchase_amt, 0) - IFNULL(crdr.dr_amt, 0) + IFNULL(crdr.cr_amt, 0)) as balance_amt
            FROM ret_purchase_order p
            JOIN ret_purchase_order_items pi ON pi.po_item_po_id = p.po_id
            LEFT JOIN (select IFNULL(sum(rtn_itm.pur_ret_pur_wt), 0) as pur_ret_pur_wt,rtn_itm.pur_ret_po_item_id FROM
                ret_purchase_return_items rtn_itm
                LEFT JOIN ret_purchase_return rtn
                    ON rtn.pur_return_id = rtn_itm.pur_ret_id
                WHERE rtn.bill_status = 1 group by rtn.pur_return_id) rtn_itms
                ON rtn_itms.pur_ret_po_item_id = pi.po_item_id
            JOIN ret_category cat ON cat.id_ret_category = pi.po_item_cat_id
            JOIN metal m ON m.id_metal = cat.id_metal
            LEFT JOIN (
                SELECT km.po_id, SUM(kmd.issue_metal_pur_wt) as tot_issued_wt
                FROM ret_karigar_metal_issue km
                JOIN ret_karigar_metal_issue_details kmd ON kmd.issue_met_parent_id = km.met_issue_id
                WHERE km.bill_status = 1 AND km.po_id = $po_id
                GROUP BY km.po_id
            ) issued ON issued.po_id = p.po_id
            LEFT JOIN (
                SELECT po_id, SUM(weight) as tot_rc_wt
                FROM ret_supplier_rate_cut
                WHERE po_id = $po_id AND status = 1
                GROUP BY po_id
            ) rc ON rc.po_id = p.po_id
            LEFT JOIN (
                SELECT po_id, 
                    SUM(CASE WHEN transtype = 2 THEN weight ELSE 0 END) as dr_wt,
                    SUM(CASE WHEN transtype = 1 THEN weight ELSE 0 END) as cr_wt,
                    SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) as dr_amt,
                    SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) as cr_amt
                FROM ret_crdr_note
                WHERE po_id = $po_id AND crdr_status = 1
                GROUP BY po_id
            ) crdr ON crdr.po_id = p.po_id
            WHERE p.po_id = $po_id and p.is_approved=1 and p.bill_status=1
            GROUP BY p.po_id");
            
        return $sql->row_array();
    }


    function save_email_log($data)
    {
        return $this->insertData($data, 'ret_order_email_logs');
    }

    function get_email_log_by_token($token)
    {
        $sql = $this->db->query("SELECT * FROM ret_order_email_logs WHERE token = '$token'");
        return $sql->row_array();
    }

    function update_email_log($id, $data)
    {
        return $this->updateData($data, 'id', $id, 'ret_order_email_logs');
    }
     // CR/DR entries without PO - Credit entries for payment screen left side
    function get_crdr_credit_entries_without_po($post)
    {
        $sql = $this->db->query("SELECT 
                ct.crdrid,
                ct.transbillno AS referenceno,
                ct.transamount,
                ct.naration,
                DATE_FORMAT(ct.createdon,'%d-%m-%Y') AS po_date,
                k.firstname AS karigar,
                ct.transamount AS trans_amount,
                ct.crdrid AS po_id,
                '' AS ratecut,
                'crdr_credit' AS type
            FROM ret_crdr_note ct
            LEFT JOIN ret_karigar k ON k.id_karigar = ct.supid
            WHERE (ct.po_id IS NULL OR ct.po_id = 0)
                AND ct.transtype = 1 
                AND ct.crdr_status = 1 
                AND ct.supid IS NOT NULL
                ".($post['id_karigar']!='' ? " AND ct.supid = ".$post['id_karigar']."" :'')."
            ORDER BY ct.createdon DESC");
        return $sql->result_array();
    }

    // Unified debit entries for payment screen — CR/DR debit notes + Purchase Returns
    // Shows: source, total_amount, spent_amount, balance_amount
    // Entries with balance_amount = 0 are excluded (fully cleared)
    function get_crdr_debit_entries_without_po($post)
    {
        $sql = $this->db->query("
            SELECT * FROM (
                SELECT 
                    ct.crdrid AS entry_id,
                    ct.transbillno AS referenceno,
                    ct.naration,
                    DATE_FORMAT(ct.createdon,'%d-%m-%Y') AS entry_date,
                    k.firstname AS karigar,
                    'Manual CR/DR' AS source,
                    'crdr' AS entry_type,
                    ct.transamount AS total_amount,
                    IFNULL(adj_sum.total_adjusted, 0) AS spent_amount,
                    (ct.transamount - IFNULL(adj_sum.total_adjusted, 0)) AS balance_amount
                FROM ret_crdr_note ct
                LEFT JOIN (
                    SELECT crdrid, SUM(adjusted_amount) AS total_adjusted
                    FROM ret_crdr_note_po_adj
                    GROUP BY crdrid
                ) adj_sum ON adj_sum.crdrid = ct.crdrid
                LEFT JOIN ret_karigar k ON k.id_karigar = ct.supid
                WHERE ct.transtype = 2 
                    AND ct.crdr_status = 1 
                    AND ct.supid IS NOT NULL
                    ".($post['id_karigar']!='' ? " AND ct.supid = ".$post['id_karigar']."" :'')."
                    ".(isset($post['bill_type']) && $post['bill_type']=='1' ? " AND ct.accountto = 1" : (isset($post['bill_type']) && $post['bill_type']=='2' ? " AND ct.accountto = 2" : ''))."

                UNION ALL

                SELECT 
                    r.pur_return_id AS entry_id,
                    r.pur_ret_ref_no AS referenceno,
                    r.pur_ret_remark AS naration,
                    DATE_FORMAT(r.bill_date,'%d-%m-%Y') AS entry_date,
                    k.firstname AS karigar,
                    'Purchase Return' AS source,
                    'purchase_return' AS entry_type,
                    IFNULL(r.return_total_cost, 0) AS total_amount,
                    IFNULL(adj_sum.total_adjusted, 0) AS spent_amount,
                    (IFNULL(r.return_total_cost, 0) - IFNULL(adj_sum.total_adjusted, 0)) AS balance_amount
                FROM ret_purchase_return r
                LEFT JOIN (
                    SELECT pur_return_id, SUM(adjusted_amount) AS total_adjusted
                    FROM ret_purchase_return_po_adj
                    GROUP BY pur_return_id
                ) adj_sum ON adj_sum.pur_return_id = r.pur_return_id
                LEFT JOIN ret_karigar k ON k.id_karigar = r.pur_ret_supplier_id
                LEFT JOIN (
                    SELECT p.po_id,
                        (IFNULL(p.tot_purchase_amt,0) 
                         - IFNULL(crdr_s.dr_amt, 0) + IFNULL(crdr_s.cr_amt, 0) 
                         - IFNULL(paid_s.paid_amount, 0) 
                         - IFNULL(ret_s.return_total, 0)) AS bill_balance
                    FROM ret_purchase_order p
                    LEFT JOIN (
                        SELECT po_id, 
                            SUM(CASE WHEN transtype = 2 THEN transamount ELSE 0 END) as dr_amt,
                            SUM(CASE WHEN transtype = 1 THEN transamount ELSE 0 END) as cr_amt
                        FROM ret_crdr_note WHERE crdr_status = 1 AND po_id IS NOT NULL AND po_id > 0
                        GROUP BY po_id
                    ) crdr_s ON crdr_s.po_id = p.po_id
                    LEFT JOIN (
                        SELECT bill.po_id, SUM(bill.bill_amount) as paid_amount
                        FROM ret_po_bill_payment_details bill
                        INNER JOIN ret_po_payment pay ON pay.pay_id = bill.pay_id AND pay.pay_status = 1
                        GROUP BY bill.po_id
                    ) paid_s ON paid_s.po_id = p.po_id
                    LEFT JOIN (
                        SELECT po_id, SUM(IFNULL(return_total_cost, 0)) AS return_total
                        FROM ret_purchase_return WHERE bill_status = 1 AND po_id IS NOT NULL AND po_id > 0
                        GROUP BY po_id
                    ) ret_s ON ret_s.po_id = p.po_id
                    WHERE p.bill_status = 1
                ) po_bal ON po_bal.po_id = r.po_id
                WHERE r.bill_status = 1
                    AND (
                        (r.po_id IS NULL OR r.po_id = 0)
                        OR (r.po_id IS NOT NULL AND r.po_id > 0 AND IFNULL(po_bal.bill_balance, 0) <= 0)
                    )
                    ".($post['id_karigar']!='' ? " AND r.pur_ret_supplier_id = ".$post['id_karigar']."" :'')."
            ) AS combined
            WHERE combined.balance_amount > 0
            ORDER BY entry_date DESC
        ");
        return $sql->result_array();
    }

    // Purchase Return entries for PO payment adjustment
    // Returns purchase returns with remaining balance > 0 for a given supplier
    function get_purchase_return_entries_for_payment($post)
    {
        $sql = $this->db->query("SELECT 
                r.pur_return_id,
                r.pur_ret_ref_no AS referenceno,
                DATE_FORMAT(r.bill_date,'%d-%m-%Y') AS return_date,
                IFNULL(r.return_total_cost, 0) AS return_total_cost,
                IFNULL(adj_sum.total_adjusted, 0) AS total_adjusted,
                (IFNULL(r.return_total_cost, 0) - IFNULL(adj_sum.total_adjusted, 0)) AS pending_amount,
                k.firstname AS karigar
            FROM ret_purchase_return r
            LEFT JOIN (
                SELECT pur_return_id, SUM(adjusted_amount) AS total_adjusted
                FROM ret_purchase_return_po_adj
                GROUP BY pur_return_id
            ) adj_sum ON adj_sum.pur_return_id = r.pur_return_id
            LEFT JOIN ret_karigar k ON k.id_karigar = r.pur_ret_supplier_id
            WHERE r.bill_status = 1
                AND (IFNULL(r.return_total_cost, 0) - IFNULL(adj_sum.total_adjusted, 0)) > 0
                ".($post['id_karigar']!='' ? " AND r.pur_ret_supplier_id = ".$post['id_karigar']."" :'')."
            ORDER BY r.bill_date DESC");
        return $sql->result_array();
    }

}
?>