<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Ret_brntransfer_model extends CI_Model
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
	
	// public function insertData($data, $table)
	// {
	// 	$insert_flag = 0;
	// 	$insert_flag = $this->db->insert($table, $data);
	// 	return ($insert_flag == 1 ? $this->db->insert_id() : 0);
	// }

	// public function updateData($data, $id_field, $id_value, $table)
	// {
	// 	$edit_flag = 0;
	// 	$this->db->where($id_field, $id_value);
	// 	$edit_flag = $this->db->update($table, $data);
	// 	return ($edit_flag == 1 ? $id_value : 0);
	// }
	public function updateDatamulti($data, $arr, $table)
	{
		$edit_flag = 0;
		$this->db->where($arr);
		$edit_flag = $this->db->update($table, $data);
		return $edit_flag;
	}
	public function deleteData($id_field, $id_value, $table)
	{
		$this->db->where($id_field, $id_value);
		$status = $this->db->delete($table);
		return $status;
	}

	function getLotsByFilter($postData)
	{
		if ($postData['page'] == 'add' || $postData['page'] == 'sales_transfer') {
			if ($postData['trans_type'] == 1) {
				$lot = $this->db->query("
SELECT 
    t.tag_lot_id AS lot_no
FROM ret_taging t
JOIN ret_lot_inwards l 
    ON l.lot_no = t.tag_lot_id
    AND l.stock_type = 1
WHERE 
    t.tag_status = 0
    AND (t.id_orderdetails IS NULL OR t.id_orderdetails = '')
    AND t.current_branch = ?
    AND NOT EXISTS (
        SELECT 1
        FROM ret_branch_transfer bt
        JOIN ret_brch_transfer_tag_items bti 
            ON bti.transfer_id = bt.branch_transfer_id
        JOIN ret_taging rt 
            ON rt.tag_id = bti.tag_id
        WHERE 
            rt.tag_lot_id = t.tag_lot_id
            AND bt.status IN (1,2)
            AND bt.transfer_item_type = 1
            AND bt.transfer_from_branch = 1
    )
GROUP BY 
    t.tag_lot_id
ORDER BY 
    t.tag_lot_id DESC
", [$postData['from_branch']]);

			} else {
				$lot = $this->db->query("SELECT l.lot_no,ld.gross_wt,ld.net_wt,ld.no_of_piece,date_format(l.lot_date,'%d-%m-%Y') as lot_date  
					FROM `ret_lot_inwards` l 
						Left join ret_lot_inwards_detail ld on l.lot_no=ld.lot_no
					WHERE l.stock_type = 2 and current_branch=" . $this->db->escape($postData['from_branch']) . " group by lot_no ORDER BY lot_no DESC");
			}
		} else if ($postData['page'] == 'approval_list') {
			if ($postData['trans_type'] == 1) {
				$lot = $this->db->query("SELECT l.`lot_no`
						FROM `ret_branch_transfer` bt
							Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id
							Left join ret_lot_inwards_detail ld on ld.id_lot_inward_detail=bti.id_lot_inward_detail
							Left join ret_lot_inwards l on ld.lot_no=l.lot_no 
						WHERE l.stock_type = 1 and bti.`id_lot_inward_detail` is not null and status=1 and transfer_item_type=1 and transfer_from_branch=" . $this->db->escape($postData['from_branch']) . " " . ($postData['to_branch'] != '' ? ' and transfer_to_branch = ' . $this->db->escape($postData['to_branch']) : '') . ' group by l.lot_no ORDER by l.lot_no DESC');
			} else {
				$lot = $this->db->query("SELECT l.lot_no
					FROM ret_branch_transfer bt
						Left join ret_lot_inwards_detail ld on ld.id_lot_inward_detail=bt.id_lot_inward_detail
						Left join ret_lot_inwards l on ld.lot_no=l.lot_no 
					WHERE l.stock_type = 2 and status=1 and transfer_item_type=2 and transfer_from_branch=" . $this->db->escape($postData['from_branch']) . " " . ($postData['to_branch'] != '' ? ' and transfer_to_branch = ' . $this->db->escape($postData['to_branch']) : '') . " group by l.lot_no ORDER by l.lot_no DESC");
			}
		}
		/*	  echo $this->db->last_query();
				  echo $this->db->_error_message();*/
		return $lot->result_array();
	}

	function zgetDesignByFilter($postData)
	{
		$search_clean = $this->db->escape_like_str($postData['searchTxt']);
		$data = $this->db->query("SELECT design_no as value, if(design_code = '' or design_code is null ,design_name ,CONCAT(design_code,' - ',design_name) ) as label FROM ret_design_master WHERE " .
			($postData['prodId'] != '' ? 'product_id =' . $this->db->escape($postData['prodId']) . ' and ' : '') . " 
		( design_code like '%" . $search_clean . "%' ESCAPE '!' or design_name like '%" . $search_clean . "%' ESCAPE '!' )");
		return $data->result_array();
	}

	function getDesignByFilter($postData)
	{
		$search_clean = $this->db->escape_like_str($postData['searchTxt']);
		$data = $this->db->query("SELECT design_no as value, if(design_code = '' or design_code is null ,design_name ,CONCAT(design_code,' - ',design_name) ) as label FROM  ret_design_master des left join ret_product_mapping p on des.design_no=p.id_design where ( design_code like '%" . $search_clean . "%' ESCAPE '!' or design_name like '%" . $search_clean . "%' ESCAPE '!' ) " .
			($postData['prodId'] != '' ? ' and p.pro_id =' . $this->db->escape($postData['prodId']) : '') . " 
		 GROUP by des.design_no");
		return $data->result_array();
	}

	function getProductsByFilter($postData)
	{
		$search_clean = $this->db->escape_like_str($postData['SearchTxt']);
		if ($postData['lot_no'] != '') {
			$result = $this->db->query("select pro_id as value,if(product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code,' - ',product_name) ) as label from ret_product_master where product_status=1 and (product_name like '%" . $search_clean  . "%' ESCAPE '!' or product_short_code like '%" . $search_clean . "%' ESCAPE '!')");
		} else {
			$result = $this->db->query("select pro_id as value,if(product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code,' - ',product_name) ) as label from ret_product_master where product_status=1 and (product_name like '%" . $search_clean . "%' ESCAPE '!' or product_short_code like '%" . $search_clean . "%' ESCAPE '!')");
		}
		return ($result) ? $result->result_array() : [];
	}

	function fetchTagsByFilter($data)
	{
		if ($data['tag_dt_rng'] != '') {
			$tagDateRange = explode('-', $data['tag_dt_rng']);
			$td1 = date_create($tagDateRange[0]);
			$td2 = date_create($tagDateRange[1]);
			$tagFromDt = date_format($td1, "d-m-Y");
			$tagToDt = date_format($td2, "d-m-Y");
		}

		if ($data['lot_dt_rng'] != '') {
			$lotDateRange = explode('-', $data['lot_dt_rng']);
			$ld1 = date_create($lotDateRange[0]);
			$ld2 = date_create($lotDateRange[1]);
			$lotFromDt = date_format($ld1, "d-m-Y");
			$lotToDt = date_format($ld2, "d-m-Y");
		}
		// print_r($data['from_brn']);exit;

		$sql = $this->db->query("SELECT tag_lot_id  as lot_no,t.tag_id,tag_code,
		t.gross_wt,t.net_wt,t.piece,date_format(t.tag_datetime,'%d-%m-%Y') as tag_datetime,
		if(design_code = '' or design_code is null ,ifnull(design_name,'-') ,CONCAT(design_code,' - ',design_name) ) as design,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product ,t.id_lot_inward_detail,c.id_metal,t.less_wt,
		pur.purity as purity_value,IFNULL(t.tag_purchase_cost,0) as tag_purchase_cost,ifnull(tag_dia_detail.stn_wt,0) as tag_dia_wt,IFNULL(sect.section_name,'') as section,ifnull(t.old_tag_id,'') as old_tag_code,IFNULL(t.sales_value,'') as sales_value,IFNULL(t.id_orderdetails,'') AS id_orderdetails,
		IFNULL(cus.orderno,'') AS orderno
				FROM `ret_taging` t
					Left join ret_lot_inwards l on t.tag_lot_id=l.lot_no
					Left join customerorderdetails cus on cus.id_orderdetails=t.id_orderdetails
					LEFT JOIN (SELECT tag_id,sum(wt) as stn_wt
					FROM `ret_taging_stone` as retst
					LEFT JOIN ret_uom as stuom ON stuom.uom_id = retst.uom_id 
					LEFT JOIN ret_stone as rtstn ON rtstn.stone_id = retst.stone_id
					where rtstn.stone_type = 1
					GROUP by tag_id) as tag_dia_detail ON tag_dia_detail.tag_id = t.tag_id
					Left join (SELECT bti.tag_id FROM `ret_branch_transfer` bt Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id WHERE (status = 1 or status = 2) and transfer_item_type=1 and bt.transfer_from_branch=" . $data['from_brn'] . " GROUP BY bti.tag_id) btrans on btrans.tag_id = t.tag_id
					Left join ret_product_master p on p.pro_id=t.product_id
					LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
					Left join ret_design_master d on d.design_no=t.design_id
					LEFT JOIN ret_purity pur ON pur.id_purity = t.purity
					LEFT JOIN ret_section sect on sect.id_section = t.id_section
			  	WHERE t.tag_status=0 and btrans.tag_id is null 
				and t.current_branch=" . $this->db->escape($data['from_brn']) . " 
			 	" . ($data['id_section'] != '' ? ' and t.id_section=' . $this->db->escape($data['id_section']) : '') . " 
			 	" . ($data['lotno'] != '' ? ' and tag_lot_id=' . $this->db->escape($data['lotno']) : '') . " 
				" . ($data['karigar'] != '' ? ' and l.gold_smith=' . $this->db->escape($data['karigar']) : '') . " 
				" . ($data['tagged_branch'] != '' ? 'and t.tagged_to_branch=' . $this->db->escape($data['tagged_branch']) : '') . "
			 	" . ($data['design_id'] != '' ? ' and design_id=' . $this->db->escape($data['design_id']) : '') . " 
			 	" . ($data['tag_no'] != '' ? ' and t.tag_code=' . $this->db->escape($data['tag_no']) : '') . " 
			 	" . ($data['old_tag_no'] != '' ? ' and t.old_tag_id=' . $this->db->escape($data['old_tag_no']) : '') . "
			 	" . ($data['prodId'] != '' ? ' and t.product_id=' . $this->db->escape($data['prodId']) : '') . " 
			 	" . ($data['lot_dt_rng'] != '' ? ' and date(lot_date) BETWEEN ' . $this->db->escape($lotFromDt) . ' AND ' . $this->db->escape($lotToDt) : '') . " 
			 	" . ($data['tag_dt_rng'] != '' ? ' and date(tag_datetime) BETWEEN ' . $this->db->escape($tagFromDt) . ' AND ' . $this->db->escape($tagToDt) : '') . " 
				".((empty($data['tag_no']) && empty($data['old_tag_no'])) 
				? " AND (t.id_orderdetails IS NULL OR t.id_orderdetails = '')" 
				: '')."
			 	group by tag_id");
		// echo $this->db->last_query();exit;	
		//Left join ret_lot_inwards_detail ld on ld.lot_no=l.lot_no
		return $sql->result_array();
	}

	function fetchEstiTagsByFilter($data)
	{
		$entry_date = date('Y-m-d');
		$sql = $this->db->query("SELECT entry_date FROM ret_day_closing WHERE id_branch=" . $this->db->escape($data['from_brn']));
		if ($sql->num_rows() > 0) {
			$entry_date = $sql->row('entry_date');
		}
		$sql->free_result();
		$esti_data = $this->db->query("
									SELECT 
										tag_lot_id  as lot_no,t.tag_id,tag_code,
										IFNULL(t.gross_wt,0) as gross_wt,IFNULL(t.net_wt,0) as net_wt,IFNULL(t.less_wt,0) as less_wt,
										t.piece,date_format(t.tag_datetime,'%d-%m-%Y') as tag_datetime,
										if(design_code = '' or design_code is null ,ifnull(design_name,'-'),
										CONCAT(design_code,' - ',design_name) ) as design,
										if( product_short_code = '' or product_short_code is null ,
										product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,
										t.id_lot_inward_detail,ifnull(tag_dia_detail.stn_wt,0) as tag_dia_wt,ifnull(s.section_name,'-') as section,ifnull(t.old_tag_id,'-') as old_tag
									FROM ret_estimation est
										LEFT JOIN ret_estimation_items est_itm ON est_itm.esti_id = est.estimation_id
										LEFT JOIN ret_taging t ON t.tag_id = est_itm.tag_id
										LEFT JOIN (SELECT tag_id,sum(wt) as stn_wt
										FROM `ret_taging_stone` as retst
										LEFT JOIN ret_uom as stuom ON stuom.uom_id = retst.uom_id 
										LEFT JOIN ret_stone as rtstn ON rtstn.stone_id = retst.stone_id
										where rtstn.stone_type = 1
										GROUP by tag_id) as tag_dia_detail ON tag_dia_detail.tag_id = t.tag_id
										Left join ret_lot_inwards l on t.tag_lot_id=l.lot_no
										Left join customerorderdetails co ON co.id_orderdetails = t.id_orderdetails
										Left join (SELECT bti.tag_id FROM `ret_branch_transfer` bt Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id WHERE (status = 1 or status = 2) AND transfer_item_type=1 GROUP BY bti.tag_id) btrans on btrans.tag_id = t.tag_id
										Left join ret_product_master p on p.pro_id=t.product_id
										Left join ret_design_master d on d.design_no=t.design_id 
										Left join ret_section s on s.id_section=t.id_section
									WHERE date(estimation_datetime)=" . $this->db->escape($entry_date) . " AND btrans.tag_id is null AND esti_for = 2 AND esti_no =" . $this->db->escape($data['esti_no']) . " AND t.current_branch=" . $this->db->escape($data['from_brn']) . " AND est.id_branch=" . $this->db->escape($data['from_brn']) . "
									GROUP BY est_itm.tag_id
									");
		// echo $this->db->last_query();
		return $esti_data->result_array();
	}

	function fetchNonTaggedItems($data)
	{
		$result = array();
		$type = 2; // 1 - Use Lot Table, 2 - Non Tag Table
		if ($this->session->userdata('branch_settings') == 1) {
			if ($this->isHeadOffice($data['from_brn']) == 1) {
				$type = 1;
			}
		} else {
			$type = 2;
		}


		$sql = ("SELECT CONCAT(design_code,' - ',design_name) as design_name,CONCAT(product_short_code ,' - ',product_name) as product_name,
	    (nt.gross_wt - ifnull(bt.grs_wt,0) ) as gross_wt,(nt.net_wt - ifnull(bt.net_wt,0) ) as net_wt,(nt.no_of_piece - ifnull(bt.pieces,0) ) as no_of_piece,nt.id_nontag_item,'' as id_lot_inward_detail,IFNULL(nt.id_section,'') as id_section,IFNULL(rs.section_name,'') as section_name,
	    nt.id_sub_design,subDes.sub_design_name,nt.product,nt.design,nt.id_sub_design,nt.id_section
        FROM  ret_nontag_item nt 
        Left join ret_product_master p on p.pro_id = nt.product
        Left join ret_design_master d on d.design_no = nt.design
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = nt.id_sub_design
        left join ret_section rs on rs.id_section = nt.id_section
        Left join (SELECT bt_nontag.id_nontag_item,sum(bt_nontag.grs_wt) as grs_wt,sum(bt_nontag.net_wt) as net_wt,sum(bt_nontag.pieces) as pieces,btrans.status FROM `ret_brch_transfer_non_tag_items` bt_nontag
		LEFT JOIN ret_branch_transfer btrans ON btrans.branch_transfer_id = bt_nontag.transfer_id
		WHERE btrans.status IN (1,2)  GROUP BY id_nontag_item) bt on bt.id_nontag_item=nt.id_nontag_item and bt.status IN (1,2)
        WHERE branch=" . $this->db->escape($data['from_brn']) . " " . ($data['prodId'] != '' ? ' and nt.product=' . $this->db->escape($data['prodId']) : '') . "
        " . ($data['id_section'] != '' ? ' and nt.id_section=' . $this->db->escape($data['id_section']) : '') . "
        group by id_nontag_item");
		// print_r($sql);exit;
		$res =  $this->db->query($sql)->result_array();
		foreach ($res as $r) {
			if ($r['gross_wt'] > 0) {
				$result[] = array(
					"design_name"           => ($r['design_name'] == NULL ? '-' : $r['design_name']),
					"section_name"          => $r['section_name'],
					"product_name"          => $r['product_name'],
					"sub_design_name"       => $r['sub_design_name'],
					"gross_wt"              => $r['gross_wt'],
					"net_wt"                => $r['net_wt'],
					"no_of_piece"           => $r['no_of_piece'],
					"id_nontag_item"        => $r['id_nontag_item'],
					"id_lot_inward_detail"  => $r['id_lot_inward_detail'],
					"id_sub_design"         => $r['id_sub_design'],
					"product"               => $r['product'],
					"design"                => $r['design'],
					"id_section"            => $r['id_section'],
				);
			}
		}
		// print_r($result);exit;
		return $result;
	}


	function isHeadOffice($branch)
	{
		$sql = $this->db->query("SELECT is_ho from branch where id_branch=" . $this->db->escape($branch));
		if ($sql->num_rows() == 1) {
			return $sql->row('is_ho');
		} else {
			return 0;
		}
	}

	/*function getApprovalListing($data){  
		if($data['dt_range'] != ''){
			$tagDateRange = explode('-',$data['dt_range']); 
			$td1 = date_create($tagDateRange[0]);
			$td2 = date_create($tagDateRange[1]);
			$tagFromDt = date_format($td1,"d-m-Y");  
			$tagToDt = date_format($td2,"d-m-Y"); 
		}
		//echo "<pre>";print_r($this->session->all_userdata());exit;
		$result = array();
		$from_branch = "";
		$to_branch = "";
		if($data['approval_type'] == 1){
		    $from_branch = ($data['from_branch'] != '' ? $data['from_branch'] : $this->session->userdata('id_branch') );
		    $to_branch = ($data['to_branch'] != '' ? $data['to_branch'] :  '' );
		}else{
		    $from_branch = ($data['from_branch'] != '' ? $data['from_branch'] : '' );
		    $to_branch = ($data['to_branch'] != '' ? $data['to_branch'] :   $this->session->userdata('id_branch') );
		}
		if($data['item_tag_type'] == 1){ 
			$sql = $this->db->query("SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,l.`lot_no`,t.gross_wt,t.net_wt,t.piece,date_format(bt.created_time,'%d-%m-%Y') as created_time,tag_transfer_id,tag_code,is_other_issue,
		if(design_code = '' or design_code is null ,ifnull(design_name,'-') ,CONCAT(design_code,' - ',design_name) ) as design,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,t.tag_id  ,t.product_id as id_prod,
		f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date
				FROM `ret_branch_transfer` bt
					Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id
					Left join ret_taging t on t.tag_id = bti.tag_id
					Left join ret_lot_inwards_detail ld on ld.lot_no=t.tag_lot_id
					Left join ret_lot_inwards l on ld.lot_no = l.lot_no
					Left join ret_product_master p on p.pro_id = t.product_id			
					Left join ret_design_master d on d.design_no = t.design_id			
					Left join branch fb on fb.id_branch = bt.transfer_from_branch			
					Left join branch tb on tb.id_branch = bt.transfer_to_branch		
					Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
					Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch		
			 	WHERE transfer_item_type =1 ".($from_branch != ''? ' and transfer_from_branch='.$this->db->escape($from_branch): '')." ".($data['branch_trans_code'] != '' ? ' and branch_trans_code='.$this->db->escape($data['branch_trans_code']): '')." ".($to_branch != '' ? ' and transfer_to_branch='.$this->db->escape($to_branch): '')." ".($data['lot_no'] != '' ? ' and l.lot_no='.$this->db->escape($data['lot_no']): '')." ".($data['id_design'] != '' ? ' and design_id='.$this->db->escape($data['id_design']): '')." ".($data['is_other_issue'] != '' ? ' and is_other_issue='.$this->db->escape($data['is_other_issue']): 'and is_other_issue=0')." ".($data['approval_type'] != '' ? ' and status='.$this->db->escape($data['approval_type']): 'and status=1')." ".($data['id_product'] != '' ? ' and lot_product='.$this->db->escape($data['id_product']): '')." ".($data['dt_range'] != '' ? ' and date(created_time) BETWEEN '.$this->db->escape($tagFromDt).' AND '.$this->db->escape($tagToDt): '')." group by bti.tag_id" ); 
			// echo $this->db->last_query();
			 $btData = array();
			 foreach($sql->result_array() as $r){  
			 	$btData[$r['branch_transfer_id']][] = $r;  
			 }  
			 $i = 0;
			 foreach($btData as $btrans){
			 	$j = 1;
			 	foreach($btrans as $tag){ 
					// Tag Detail 
					$tagData =  array( 
									"design" 	=> $tag['design'],
									"gross_wt"  => $tag['gross_wt'],
								    "net_wt" 	=> $tag['net_wt'],
								    "piece" 	=> $tag['piece'],
								    "id_prod" 	=> $tag['id_prod'],
								    "tag_code"  => $tag['tag_code']
								); 
			 		// Lot Data
			 		if(isset($result[$i])){
			 			$prev = $result[$i];
					    $result[$i]["gross_wt"] = $tag['gross_wt']+$prev['gross_wt'];
					    $result[$i]["net_wt"] 	= $tag['net_wt']+$prev['net_wt'];
					    $result[$i]["piece"] 	= $tag['piece']+$prev['piece'];
					}else{
						$result[$i] = $tag;  
						$result[$i]['no_of_prod'] = 0;  
					}	
					$id_prod = $result[$i]['id_prod']; 
					// Product Detail
					if(isset($result[$i]['prod'][$id_prod])){
			 			$prev_prod = $result[$i]['prod'][$id_prod];
					    $result[$i]['prod'][$id_prod]["gross_wt"]= $tag['gross_wt']+$prev_prod['gross_wt'];
						$result[$i]['prod'][$id_prod]["net_wt"]  = $tag['net_wt']+$prev_prod['net_wt'];
						$result[$i]['prod'][$id_prod]["piece"] 	 = $tag['piece']+$prev_prod['piece'];
						$result[$i]['prod'][$id_prod]["no_of_tags"]= $prev_prod['no_of_tags']+1 ; 
						$result[$i]['prod'][$id_prod]['tags'][$j]= $tagData;
					}else{
						$result[$i]['prod'][$id_prod] = array(
									"gross_wt"   => $tag['gross_wt'],
									"net_wt" 	 => $tag['net_wt'],
									"piece" 	 => $tag['piece'],
									"product" 	 => $tag['product'],
									"id_prod" 	 => $tag['id_prod'],
									"no_of_tags" => 1,
							    	);
						$result[$i]['no_of_prod']++;  // Product Count
						$result[$i]['prod'][$id_prod]['tags'][$j]= $tagData; // Add tag data in array
					} 
					 
					$j++;
			 	}
			 	$i++;
			 } 
			 //echo "<pre>";print_r($result);
		 } 	
		 elseif($data['item_tag_type'] == 2){
		 	$sql_nt = $this->db->query("SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,'-' as lot_no,bt.`pieces`,bt.`grs_wt`,bt.`net_wt`,`status`,date_format(bt.created_time,'%d-%m-%Y') as created_time,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,product as id_product,design as id_design,bt.id_nontag_item,is_other_issue,
			 f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date
				FROM `ret_branch_transfer` bt
					Left join ret_nontag_item nt on nt.id_nontag_item = bt.id_nontag_item
					Left join ret_product_master p on p.pro_id = nt.product		
					Left join branch fb on fb.id_branch = bt.transfer_from_branch			
					Left join branch tb on tb.id_branch = bt.transfer_to_branch	
					Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
					Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch	
			 	WHERE bt.id_nontag_item is not null AND bt.id_nontag_item > 0 and  transfer_item_type =2 ".($from_branch != ''? ' and transfer_from_branch='.$from_branch: '')." ".($data['branch_trans_code'] != '' ? ' and branch_trans_code='.$data['branch_trans_code']: '')." ".($to_branch != '' ? ' and transfer_to_branch='.$to_branch: '')." ".($data['id_product'] != '' ? ' and product='.$data['id_product']: '')." ".($data['is_other_issue'] != '' ? ' and is_other_issue='.$data['is_other_issue']: 'and is_other_issue=0')." ".($data['approval_type'] != '' ? ' and status='.$data['approval_type']: 'and status=1')." ".($data['dt_range'] != '' ? ' and date(created_time) BETWEEN '.$tagFromDt.' AND '.$tagToDt: '')." group by branch_transfer_id"); 
			 $result = $sql_nt->result_array();
		 }
		 else if($data['item_tag_type'] == 3)
		 {
			$sql=$this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,b.transfer_from_branch,b.transfer_to_branch,b.grs_wt as gross_wt,b.net_wt as net_wt,SUM(d.rate) as rate,m.old_metal_sale_id,
			f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch
			FROM ret_branch_transfer b 
			LEFT JOIN ret_brch_transfer_old_metal m ON m.transfer_id=b.branch_transfer_id
			LEFT JOIN ret_bill_old_metal_sale_details d ON d.old_metal_sale_id=m.old_metal_sale_id
			Left join branch fb on fb.id_branch = b.transfer_from_branch			
			Left join branch tb on tb.id_branch = b.transfer_to_branch	
			Left join ret_day_closing f_dc on f_dc.id_branch = b.transfer_from_branch			
			Left join ret_day_closing t_dc on t_dc.id_branch = b.transfer_to_branch
			WHERE b.transfer_item_type=3
			".($data['approval_type'] != '' ? ' and status='.$data['approval_type']: 'and status=1')."
			".($from_branch != ''? ' and b.transfer_from_branch='.$from_branch: '')."
			".($to_branch != '' ? ' and b.transfer_to_branch='.$to_branch: '')."
			group by b.branch_transfer_id");
		    //print_r($this->db->last_query());exit;
			$result=$sql->result_array();
		 }
		 
		 else if($data['item_tag_type'] == 4)
		 {
			$sql=$this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,b.transfer_from_branch,b.transfer_to_branch,b.pieces,
			f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch
			FROM ret_branch_transfer b 
			Left join branch fb on fb.id_branch = b.transfer_from_branch			
			Left join branch tb on tb.id_branch = b.transfer_to_branch	
			Left join ret_day_closing f_dc on f_dc.id_branch = b.transfer_from_branch			
			Left join ret_day_closing t_dc on t_dc.id_branch = b.transfer_to_branch
            WHERE b.transfer_item_type=4
			".($data['approval_type'] != '' ? ' and status='.$data['approval_type']: 'and status=1')."
			".($from_branch != ''? ' and b.transfer_from_branch='.$from_branch: '')."
			".($to_branch != '' ? ' and b.transfer_to_branch='.$to_branch: '')."
			group by b.branch_transfer_id");
		    //print_r($this->db->last_query());exit;
			$result=$sql->result_array();
		 }
		 else if($data['item_tag_type'] == 5)
		 {
		 	$sql=$this->db->query("SELECT p.product_name,d.totalitems,d.weight,date_format(l.date,'%d-%m-%Y') as date_add,b.branch_trans_code,
		 		f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,b.branch_transfer_id,
		 		fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,d.orderno,d.id_orderdetails
			FROM ret_branch_transfer b 
			LEFT JOIN ret_bt_order_log l ON l.branch_transfer_id=b.branch_transfer_id
			LEFT JOIN customerorderdetails d ON d.id_orderdetails=l.id_orderdetails
			LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
			Left join branch fb on fb.id_branch = b.transfer_from_branch			
			Left join branch tb on tb.id_branch = b.transfer_to_branch	
			Left join ret_day_closing f_dc on f_dc.id_branch = b.transfer_from_branch			
			Left join ret_day_closing t_dc on t_dc.id_branch = b.transfer_to_branch	
			WHERE b.transfer_item_type=5 
			".($data['branch_trans_code']!='' ? " and branch_trans_code=".$data['branch_trans_code']."" :'')."
			".($data['approval_type'] != '' ? ' and b.status='.$data['approval_type']: 'and status=1')." 
			 ".($from_branch != ''? ' and b.transfer_from_branch='.$from_branch: '')."
			 ".($to_branch != '' ? ' and b.transfer_to_branch='.$to_branch: '')."
			 group by l.id_orderdetails");
			$result=$sql->result_array();
		 }
		 
		return $result;
	}*/


	function getApprovalListing($data)
	{
		if ($data['dt_range'] != '') {
			$tagDateRange = explode('-', $data['dt_range']);
			$td1 = date_create($tagDateRange[0]);
			$td2 = date_create($tagDateRange[1]);
			$tagFromDt = date_format($td1, "Y-m-d");
			$tagToDt = date_format($td2, "Y-m-d");
		}
		//echo "<pre>";print_r($this->session->all_userdata());exit;
		$result = array();
		$from_branch = "";
		$to_branch = "";
		if ($data['approval_type'] == 1) {
			$from_branch = ($data['from_branch'] != '' ? $data['from_branch'] : $this->session->userdata('id_branch'));
			$to_branch = ($data['to_branch'] != '' ? $data['to_branch'] :  '');
		} else {
			$from_branch = ($data['from_branch'] != '' ? $data['from_branch'] : '');
			$to_branch = ($data['to_branch'] != '' ? $data['to_branch'] :   $this->session->userdata('id_branch'));
		}
		if ($data['item_tag_type'] == 1) {

			if ($data['approval_type'] == 1) {
				$sql = $this->db->query("SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,l.`lot_no`,t.gross_wt,t.net_wt,t.piece,date_format(bt.created_time,'%d-%m-%Y') as created_time,tag_transfer_id,tag_code,is_other_issue,
				if(design_code = '' or design_code is null ,ifnull(design_name,'-') ,CONCAT(design_code,' - ',design_name) ) as design,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,t.tag_id  ,t.product_id as id_prod,
				f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date
				FROM `ret_branch_transfer` bt
					Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id
					Left join ret_taging t on t.tag_id = bti.tag_id
					Left join ret_lot_inwards_detail ld on ld.lot_no=t.tag_lot_id
					Left join ret_lot_inwards l on ld.lot_no = l.lot_no
					Left join ret_product_master p on p.pro_id = t.product_id			
					Left join ret_design_master d on d.design_no = t.design_id			
					Left join branch fb on fb.id_branch = bt.transfer_from_branch			
					Left join branch tb on tb.id_branch = bt.transfer_to_branch		
					Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
					Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch		
			 	WHERE transfer_item_type =1 and t.tag_status=0 AND t.current_branch=" . $this->db->escape($from_branch) . "  
			 	    " . ($from_branch != '' ? ' and transfer_from_branch=' . $this->db->escape($from_branch) : '') . " 
			 	    " . ($data['branch_trans_code'] != '' ? ' and branch_trans_code=' . $this->db->escape($data['branch_trans_code']) : '') . " 
			 	    " . ($to_branch != '' ? ' and transfer_to_branch=' . $this->db->escape($to_branch) : '') . " 
			 	    " . ($data['lot_no'] != '' ? ' and l.lot_no=' . $this->db->escape($data['lot_no']) : '') . " 
			 	    " . ($data['id_design'] != '' ? ' and design_id=' . $this->db->escape($data['id_design']) : '') . " 
			 	    " . ($data['is_other_issue'] != '' ? ' and is_other_issue=' . $this->db->escape($data['is_other_issue']) : 'and is_other_issue=0') . " 
			 	    " . ($data['approval_type'] != '' ? ' and status=' . $this->db->escape($data['approval_type']) : 'and status=1') . " 
			 	    " . ($data['id_product'] != '' ? ' and lot_product=' . $this->db->escape($data['id_product']) : '') . " 
			 	    " . ($data['dt_range'] != '' ? ' and date(bt.created_time) BETWEEN ' . $this->db->escape($tagFromDt) . ' AND ' . $this->db->escape($tagToDt) : '') . " group by bti.tag_id");
				//echo $this->db->last_query();

			} else {
				$sql = $this->db->query("SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,l.`lot_no`,t.gross_wt,t.net_wt,t.piece,date_format(bt.created_time,'%d-%m-%Y') as created_time,tag_transfer_id,tag_code,is_other_issue,
				if(design_code = '' or design_code is null ,ifnull(design_name,'-') ,CONCAT(design_code,' - ',design_name) ) as design,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,t.tag_id  ,t.product_id as id_prod,
				f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date
				FROM `ret_branch_transfer` bt
					Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id
					Left join ret_taging t on t.tag_id = bti.tag_id
					Left join ret_lot_inwards_detail ld on ld.lot_no=t.tag_lot_id
					Left join ret_lot_inwards l on ld.lot_no = l.lot_no
					Left join ret_product_master p on p.pro_id = t.product_id			
					Left join ret_design_master d on d.design_no = t.design_id			
					Left join branch fb on fb.id_branch = bt.transfer_from_branch			
					Left join branch tb on tb.id_branch = bt.transfer_to_branch		
					Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
					Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch
					
			 	WHERE transfer_item_type =1 and t.tag_status=4  AND t.current_branch=" . $this->db->escape($to_branch) . " 
			 	" . ($from_branch != '' ? ' and transfer_from_branch=' . $this->db->escape($from_branch) : '') . " 
			 	" . ($data['branch_trans_code'] != '' ? ' and branch_trans_code=' . $this->db->escape($data['branch_trans_code']) : '') . " 
			 	" . ($to_branch != '' ? ' and transfer_to_branch=' . $this->db->escape($to_branch) : '') . " 
			 	" . ($data['lot_no'] != '' ? ' and l.lot_no=' . $this->db->escape($data['lot_no']) : '') . " 
			 	" . ($data['id_design'] != '' ? ' and design_id=' . $this->db->escape($data['id_design']) : '') . " 
			 	" . ($data['is_other_issue'] != '' ? ' and is_other_issue=' . $this->db->escape($data['is_other_issue']) : 'and is_other_issue=0') . " 
			 	" . ($data['approval_type'] != '' ? ' and status=' . $this->db->escape($data['approval_type']) : 'and status=1') . " 
			 	" . ($data['id_product'] != '' ? ' and lot_product=' . $this->db->escape($data['id_product']) : '') . " 
			 	" . ($data['dt_range'] != '' ? ' and date(bt.created_time) BETWEEN ' . $this->db->escape($tagFromDt) . ' AND ' . $this->db->escape($tagToDt) . '' : '') . " group by bti.tag_id");
				//echo $this->db->last_query();
			}

			// echo $this->db->last_query();
			$btData = array();
			foreach ($sql->result_array() as $r) {
				$btData[$r['branch_transfer_id']][] = $r;
			}
			$i = 0;
			foreach ($btData as $btrans) {
				$j = 1;
				foreach ($btrans as $tag) {
					// Tag Detail 
					$tagData =  array(
						"design" 	=> $tag['design'],
						"gross_wt"  => $tag['gross_wt'],
						"net_wt" 	=> $tag['net_wt'],
						"piece" 	=> $tag['piece'],
						"id_prod" 	=> $tag['id_prod'],
						"tag_code"  => $tag['tag_code']
					);
					// Lot Data
					if (isset($result[$i])) {
						$prev = $result[$i];
						$result[$i]["gross_wt"] = $tag['gross_wt'] + $prev['gross_wt'];
						$result[$i]["net_wt"] 	= $tag['net_wt'] + $prev['net_wt'];
						$result[$i]["piece"] 	= $tag['piece'] + $prev['piece'];
					} else {
						$result[$i] = $tag;
						$result[$i]['no_of_prod'] = 0;
					}
					$id_prod = $result[$i]['id_prod'];
					// Product Detail
					if (isset($result[$i]['prod'][$id_prod])) {
						$prev_prod = $result[$i]['prod'][$id_prod];
						$result[$i]['prod'][$id_prod]["gross_wt"] = $tag['gross_wt'] + $prev_prod['gross_wt'];
						$result[$i]['prod'][$id_prod]["net_wt"]  = $tag['net_wt'] + $prev_prod['net_wt'];
						$result[$i]['prod'][$id_prod]["piece"] 	 = $tag['piece'] + $prev_prod['piece'];
						$result[$i]['prod'][$id_prod]["no_of_tags"] = $prev_prod['no_of_tags'] + 1;
						$result[$i]['prod'][$id_prod]['tags'][$j] = $tagData;
					} else {
						$result[$i]['prod'][$id_prod] = array(
							"gross_wt"   => $tag['gross_wt'],
							"net_wt" 	 => $tag['net_wt'],
							"piece" 	 => $tag['piece'],
							"product" 	 => $tag['product'],
							"id_prod" 	 => $tag['id_prod'],
							"no_of_tags" => 1,
						);
						$result[$i]['no_of_prod']++;  // Product Count
						$result[$i]['prod'][$id_prod]['tags'][$j] = $tagData; // Add tag data in array
					}

					$j++;
				}
				$i++;
			}
			//echo "<pre>";print_r($result);
		}

		//  elseif($data['item_tag_type'] == 2){
		// 	$sql_nt = $this->db->query("SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,'-' as lot_no,bt.`pieces`,bt.`grs_wt`,bt.`net_wt`,bt.`status`,date_format(bt.created_time,'%d-%m-%Y') as created_time,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,product as id_product,design as id_design,bt.id_nontag_item,is_other_issue,
		// 	f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,rs.section_name,nt.id_section,nt.id_sub_design,ifnull(bt.id_nontag_receipt,'') as id_nontag_receipt
		// 	   FROM `ret_branch_transfer` bt
		// 		   Left join ret_nontag_item nt on nt.id_nontag_item = bt.id_nontag_item
		// 		   Left join ret_product_master p on p.pro_id = nt.product		
		// 		   Left join branch fb on fb.id_branch = bt.transfer_from_branch			
		// 		   Left join branch tb on tb.id_branch = bt.transfer_to_branch	
		// 		   Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
		// 		   Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch
		// 		   left join ret_section rs on rs.id_section = nt.id_section	
		// 		WHERE bt.id_nontag_item is not null AND bt.id_nontag_item > 0 and  transfer_item_type =2 ".($from_branch != ''? ' and transfer_from_branch='.$from_branch: '')." ".($data['branch_trans_code'] != '' ? ' and branch_trans_code='.$data['branch_trans_code']: '')." ".($to_branch != '' ? ' and transfer_to_branch='.$to_branch: '')." ".($data['id_product'] != '' ? ' and product='.$data['id_product']: '')." ".($data['is_other_issue'] != '' ? ' and is_other_issue='.$data['is_other_issue']: 'and is_other_issue=0')." ".($data['approval_type'] != '' ? ' and bt.status='.$data['approval_type']: 'and status=1')." ".($data['dt_range'] != '' ? ' and date(created_time) BETWEEN '.$tagFromDt.' AND '.$tagToDt: '')." group by branch_transfer_id
		// 	   UNION
		// 	   SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,'-' as lot_no,bt.`pieces`,bt.`grs_wt`,bt.`net_wt`,bt.`status`,date_format(bt.created_time,'%d-%m-%Y') as created_time,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,nt.id_product,nt.id_design ,ifnull(bt.id_nontag_item,'') as id_nontag_item,is_other_issue,
		// 	   f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,rs.section_name,nt.id_section,nt.id_sub_design,bt.id_nontag_receipt
		// 	   FROM `ret_branch_transfer` bt
		// 	   Left join ret_nontag_receipt nt on nt.id_nontag_receipt = bt.id_nontag_receipt
		// 	   Left join ret_product_master p on p.pro_id = nt.id_product		
		// 	   Left join branch fb on fb.id_branch = bt.transfer_from_branch			
		// 	   Left join branch tb on tb.id_branch = bt.transfer_to_branch	
		// 	   Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
		// 	   Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch
		// 	   left join ret_section rs on rs.id_section = nt.id_section	
		// 	   WHERE bt.id_nontag_receipt is not null AND bt.id_nontag_receipt > 0 and  transfer_item_type =2 ".($from_branch != ''? ' and transfer_from_branch='.$from_branch: '')." ".($data['branch_trans_code'] != '' ? ' and branch_trans_code='.$data['branch_trans_code']: '')." ".($to_branch != '' ? ' and transfer_to_branch='.$to_branch: '')." ".($data['id_product'] != '' ? ' and product='.$data['id_product']: '')." ".($data['is_other_issue'] != '' ? ' and is_other_issue='.$data['is_other_issue']: 'and is_other_issue=0')." ".($data['approval_type'] != '' ? ' and bt.status='.$data['approval_type']: 'and status=1')." ".($data['dt_range'] != '' ? ' and date(created_time) BETWEEN '.$tagFromDt.' AND '.$tagToDt: '')." group by branch_transfer_id");
		// 	 //print_r($this->db->last_query());exit;
		// 	 $result = $sql_nt->result_array();
		//  }
		else if ($data['item_tag_type'] == 2) {
			$sql_nt = $this->db->query("SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,'-' as lot_no,
		   SUM(rnt.pieces)  as  pieces,SUM(rnt.grs_wt) as grs_wt ,SUM(rnt.net_wt) as net_wt ,bt.`status`,date_format(bt.created_time,'%d-%m-%Y') as created_time,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,product as id_product,design as id_design,
		   rnt.id_nontag_item,is_other_issue,
			f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,rs.section_name,nt.id_section,nt.id_sub_design,ifnull(rnt.id_nontag_receipt,'') as id_nontag_receipt
			   FROM `ret_branch_transfer` bt
				   Left join ret_brch_transfer_non_tag_items rnt on rnt.transfer_id = bt.branch_transfer_id
				   Left join ret_nontag_item nt on nt.id_nontag_item = rnt.id_nontag_item
				   Left join ret_product_master p on p.pro_id = nt.product		
				   Left join branch fb on fb.id_branch = bt.transfer_from_branch			
				   Left join branch tb on tb.id_branch = bt.transfer_to_branch	
				   Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
				   Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch
				   left join ret_section rs on rs.id_section = nt.id_section	
				WHERE rnt.id_nontag_item is not null AND rnt.id_nontag_item > 0 and  transfer_item_type =2 " . ($from_branch != '' ? ' and transfer_from_branch=' . $from_branch : '') . " " . ($data['branch_trans_code'] != '' ? ' and branch_trans_code=' . $data['branch_trans_code'] : '') . " " . ($to_branch != '' ? ' and transfer_to_branch=' . $to_branch : '') . " " . ($data['id_product'] != '' ? ' and product=' . $data['id_product'] : '') . " " . ($data['is_other_issue'] != '' ? ' and is_other_issue=' . $data['is_other_issue'] : 'and is_other_issue=0') . " " . ($data['approval_type'] != '' ? ' and bt.status=' . $data['approval_type'] : 'and status=1') . " " . ($data['dt_range'] != '' ? ' and date(created_time) BETWEEN ' . $tagFromDt . ' AND ' . $tagToDt : '') . " group by branch_transfer_id
			   UNION
			   SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,'-' as lot_no,rnt.`pieces`,rnt.`grs_wt`,rnt.`net_wt`,bt.`status`,date_format(bt.created_time,'%d-%m-%Y') as created_time,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,nt.id_product,nt.id_design ,ifnull(rnt.id_nontag_item,'') as id_nontag_item,is_other_issue,
			   f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,rs.section_name,nt.id_section,nt.id_sub_design,rnt.id_nontag_receipt
			   FROM `ret_branch_transfer` bt
			   Left join ret_brch_transfer_non_tag_items rnt on rnt.transfer_id = bt.branch_transfer_id
			   Left join ret_nontag_receipt nt on nt.id_nontag_receipt = rnt.id_nontag_receipt
			   Left join ret_product_master p on p.pro_id = nt.id_product		
			   Left join branch fb on fb.id_branch = bt.transfer_from_branch			
			   Left join branch tb on tb.id_branch = bt.transfer_to_branch	
			   Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
			   Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch
			   left join ret_section rs on rs.id_section = nt.id_section	
			   WHERE rnt.id_nontag_receipt is not null AND rnt.id_nontag_receipt > 0 and  transfer_item_type =2 " . ($from_branch != '' ? ' and transfer_from_branch=' . $from_branch : '') . " " . ($data['branch_trans_code'] != '' ? ' and branch_trans_code=' . $data['branch_trans_code'] : '') . " " . ($to_branch != '' ? ' and transfer_to_branch=' . $to_branch : '') . " " . ($data['id_product'] != '' ? ' and product=' . $data['id_product'] : '') . " " . ($data['is_other_issue'] != '' ? ' and is_other_issue=' . $data['is_other_issue'] : 'and is_other_issue=0') . " " . ($data['approval_type'] != '' ? ' and bt.status=' . $data['approval_type'] : 'and status=1') . " " . ($data['dt_range'] != '' ? ' and date(created_time) BETWEEN ' . $tagFromDt . ' AND ' . $tagToDt : '') . " 
			   group by branch_transfer_id");
			//print_r($this->db->last_query());exit;
			$result = $sql_nt->result_array();
		} else if ($data['item_tag_type'] == 3) {
			$sql = $this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,b.transfer_from_branch,b.transfer_to_branch,b.grs_wt as gross_wt,b.net_wt as net_wt,SUM(d.rate) as rate,m.old_metal_sale_id,
			f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch
			FROM ret_branch_transfer b 
			LEFT JOIN ret_brch_transfer_old_metal m ON m.transfer_id=b.branch_transfer_id
			LEFT JOIN ret_bill_old_metal_sale_details d ON d.old_metal_sale_id=m.old_metal_sale_id
			Left join branch fb on fb.id_branch = b.transfer_from_branch			
			Left join branch tb on tb.id_branch = b.transfer_to_branch	
			Left join ret_day_closing f_dc on f_dc.id_branch = b.transfer_from_branch			
			Left join ret_day_closing t_dc on t_dc.id_branch = b.transfer_to_branch
			WHERE b.transfer_item_type=3
			" . ($data['approval_type'] != '' ? ' and status=' . $this->db->escape($data['approval_type']) : 'and status=1') . "
			" . ($from_branch != '' ? ' and b.transfer_from_branch=' . $this->db->escape($from_branch) : '') . "
			" . ($to_branch != '' ? ' and b.transfer_to_branch=' . $this->db->escape($to_branch) : '') . "
			" . ($data['branch_trans_code'] != '' ? ' and b.branch_trans_code=' . $this->db->escape($data['branch_trans_code']) : '') . " 
			and b.is_eda = " . $this->db->escape($data['is_eda_appr']) . "
			group by b.branch_transfer_id");
			//print_r($this->db->last_query());exit;
			$result = $sql->result_array();
		} else if ($data['item_tag_type'] == 4) {
			$sql = $this->db->query("SELECT b.branch_transfer_id,b.branch_trans_code,b.transfer_from_branch,b.transfer_to_branch,b.pieces,
			f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch
			FROM ret_branch_transfer b 
			Left join branch fb on fb.id_branch = b.transfer_from_branch			
			Left join branch tb on tb.id_branch = b.transfer_to_branch	
			Left join ret_day_closing f_dc on f_dc.id_branch = b.transfer_from_branch			
			Left join ret_day_closing t_dc on t_dc.id_branch = b.transfer_to_branch
            WHERE b.transfer_item_type=4
			" . ($data['approval_type'] != '' ? ' and status=' . $this->db->escape($data['approval_type']) : 'and status=1') . "
			" . ($from_branch != '' ? ' and b.transfer_from_branch=' . $this->db->escape($from_branch) : '') . "
			" . ($data['branch_trans_code'] != '' ? ' and b.branch_trans_code=' . $this->db->escape($data['branch_trans_code']) : '') . "
			" . ($to_branch != '' ? ' and b.transfer_to_branch=' . $this->db->escape($to_branch) : '') . "
			group by b.branch_transfer_id");
			// print_r($this->db->last_query());exit;
			$result = $sql->result_array();
		} else if ($data['item_tag_type'] == 5) {
			$sql = $this->db->query("SELECT p.product_name,d.totalitems,d.weight,date_format(l.date,'%d-%m-%Y') as date_add,b.branch_trans_code,
		 		f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date,b.branch_transfer_id,
		 		fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,d.orderno,d.id_orderdetails
			FROM ret_branch_transfer b 
			LEFT JOIN ret_bt_order_log l ON l.branch_transfer_id=b.branch_transfer_id
			LEFT JOIN customerorderdetails d ON d.id_orderdetails=l.id_orderdetails
			LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
			Left join branch fb on fb.id_branch = b.transfer_from_branch			
			Left join branch tb on tb.id_branch = b.transfer_to_branch	
			Left join ret_day_closing f_dc on f_dc.id_branch = b.transfer_from_branch			
			Left join ret_day_closing t_dc on t_dc.id_branch = b.transfer_to_branch	
			WHERE b.transfer_item_type=5 
			" . ($data['branch_trans_code'] != '' ? " and branch_trans_code=" . $this->db->escape($data['branch_trans_code']) . "" : '') . "
			" . ($data['approval_type'] != '' ? ' and b.status=' . $this->db->escape($data['approval_type']) : 'and status=1') . " 
			 " . ($from_branch != '' ? ' and b.transfer_from_branch=' . $this->db->escape($from_branch) : '') . "
			 " . ($to_branch != '' ? ' and b.transfer_to_branch=' . $this->db->escape($to_branch) : '') . "
			 group by l.id_orderdetails");
			$result = $sql->result_array();
		}

		/* echo $this->db->last_query();
		echo $this->db->_error_message();*/
		return $result;
	}


	function trans_code_generator($is_eda)
	{
		$lastno = $this->get_last_trans_code($is_eda);
		if ($lastno != NULL) {
			$number = (int) $lastno;
			$number++;
			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);;

			return $code_number;
		} else {
			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);;
			return $code_number;
		}
	}


	function getBTnontags($trans_id)
	{

		$sql = "SELECT bntag.id_nontag_item,nt.product,nt.product,nt.design,nt.id_sub_design,
		bntag.pieces as no_of_piece,bntag.grs_wt as gross_wt,bntag.net_wt,nt.id_section,nt.branch
		FROM ret_brch_transfer_non_tag_items  bntag
		LEFT JOIN  ret_nontag_item as nt on nt.id_nontag_item=bntag.id_nontag_item
		WHERE bntag.transfer_id=" . $this->db->escape($trans_id);
		//print_r($this->db->last_query());exit;
		return $this->db->query($sql)->result_array();
	}

	function get_last_trans_code($is_eda)
	{
		$sql = "SELECT max(branch_trans_code) as lastTrans_no FROM ret_branch_transfer 
		where is_eda =" . $this->db->escape($is_eda) . "
		ORDER BY branch_transfer_id DESC ";
		return $this->db->query($sql)->row()->lastTrans_no;
	}

	function getBTransData($transCode, $s_type, $print_type)
	{
		// print_r($s_type);exit;
		/*$sql_1 = "SELECT transfer_item_type,branch_trans_code,`branch_transfer_id`,fb.name as from_branch,tb.name as to_branch,ld.`lot_no`,bt.`pieces`,bt.`grs_wt`,bt.`net_wt`,`status`,date_format(bt.created_time,'%d-%m-%Y') as created_time,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,
		ld.lot_no as t_lot,t.gross_wt as t_gross,t.net_wt as t_net,t.piece as t_piece,t.tag_id
				FROM `ret_branch_transfer` bt
					Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id
					Left join ret_taging t on t.tag_id = bti.tag_id
					Left join ret_lot_inwards_detail ld on ld.id_lot_inward_detail=".($s_type==1?'bti.id_lot_inward_detail':'bt.id_lot_inward_detail')."
					Left join ret_product_master p on p.pro_id = ld.lot_product		
					Left join branch fb on fb.id_branch = bt.transfer_from_branch			
					Left join branch tb on tb.id_branch = bt.transfer_to_branch		
			 	WHERE branch_trans_code=".$transCode." GROUP BY ".($s_type==1?'bti.tag_id':'ld.id_lot_inward_detail')." "; */
		if ($s_type == 1) {
			$sql = $this->db->query("SELECT b.is_other_issue,b.transfer_item_type,tag.tag_code,b.branch_transfer_id,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,p.product_name,SUM(tag.piece) as piece,
    		    SUM(tag.gross_wt) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt,date_format(b.created_time,'%d-%m-%Y') as created_time,tag.id_section,sect.section_name,ifnull(tag_stn.stn_wt,0) as stn_wt,ifnull(tag_dia.dia_wt,0) as dia_wt,IFNULL(des.design_name,'') as design_name,ifnull(emp.firstname,'') as emp,ifnull(subdes.sub_design_name,'') as sub_design_name,
				IFNULL(b.remark,'') as remark,ifnull(t.tag_remark,'') as tag_remark,ifnull(tag.old_tag_id,'') as old_tag_code,IFNULL(uom.uom_name,'') as uom,  fb.address1 as from_branch_address1,fb.address2 as from_branch_address2,tb.address1 as to_branch_address1,tb.address2 as to_branch_address2,fb.gst_number as from_gst,tb.gst_number as to_gst,from_ci.name as from_city_name,to_ci.name as to_city_name,from_st.name as from_state_name,to_st.name as to_state_name,cat.hsn_code,fb.pincode as from_pincode,tb.pincode as to_pincode,IFNULL(tag.tag_purchase_cost,0) as buy_rate,cat.name,IFNULL(emp.emp_code,'') as emp_code
                FROM ret_branch_transfer b
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
                LEFT JOIN ret_brch_transfer_tag_items t ON t.transfer_id=b.branch_transfer_id
                LEFT JOIN ret_taging tag ON tag.tag_id=t.tag_id
				LEFT JOIN ret_uom uom ON uom.uom_id=tag.uom_gross_wt
				LEFT JOIN ret_section sect on sect.id_section = tag.id_section
                LEFT JOIN ret_product_master p ON p.pro_id=tag.product_id
				LEFT JOIN ret_design_master des on des.design_no = tag.design_id
				LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = tag.id_sub_design
				LEFT JOIN employee emp on emp.id_employee = b.create_by
				left join city from_ci on from_ci.id_city =fb.id_city
                left join city to_ci on to_ci.id_city =tb.id_city
                left join state from_st on from_st.id_state =fb.id_state
                left join state to_st on to_st.id_state=tb.id_state
				LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
				LEFT JOIN (SELECT tag_st.tag_id,tag.product_id,sum(if(tag_st.uom_id=6,round((tag_st.wt/5),3),tag_st.wt)) as stn_wt
				FROM ret_taging_stone tag_st
				LEFT JOIN ret_stone st on st.stone_id = tag_st.stone_id
				LEFT JOIN ret_taging tag on tag.tag_id = tag_st.tag_id
				
				WHERE st.stone_type!=1
				" . ($print_type == 1 ? 'GROUP by tag.product_id' : 'GROUP by tag_st.tag_id') . ") as tag_stn on " . ($print_type == 1 ? 'tag_stn.product_id = tag.product_id' : 'tag_stn.tag_id = tag.tag_id') . "
				LEFT JOIN (SELECT tag_st.tag_id,tag.product_id,sum(round((tag_st.wt),3)) as dia_wt
				FROM ret_taging_stone tag_st
				LEFT JOIN ret_stone st on st.stone_id = tag_st.stone_id
				LEFT JOIN ret_taging tag on tag.tag_id = tag_st.tag_id
				WHERE st.stone_type=1
				" . ($print_type == 1 ? 'GROUP by tag.product_id' : 'GROUP by tag_st.tag_id') . ") as tag_dia on " . ($print_type == 1 ? 'tag_dia.product_id = tag.product_id' : 'tag_dia.tag_id = tag.tag_id') . "
                WHERE b.branch_trans_code=" . $this->db->escape($transCode) . "
                " . ($print_type == 1 ? 'GROUP by tag.product_id' : ($print_type == 3 ? 'GROUP by cat.id_ret_category ' : 'GROUP by t.tag_id')) . "");
					// print_r($this->db->last_query());exit;
		}
	
		// else if($s_type==2)
		// {
		//     $sql=$this->db->query("SELECT b.is_other_issue,b.transfer_item_type,p.product_name,b.branch_transfer_id,b.branch_trans_code,
		//     SUM(b.pieces) as piece, SUM(b.grs_wt) as gross_wt, SUM(b.net_wt) as net_wt, date_format(b.created_time,'%d-%m-%Y') as created_time,
		//     fb.name as from_branch,tb.name as to_branch,ifnull(subdes.sub_design_name,'') as sub_design_name,
		// 	IFNULL(b.remark,'') as remark
		//     FROM ret_branch_transfer b 
		//     LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
		//     LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
		//     LEFT JOIN ret_nontag_item nt ON nt.id_nontag_item=b.id_nontag_item
		//     LEFT JOIN ret_lot_inwards_detail d ON d.id_lot_inward_detail=b.id_lot_inward_detail
		//     LEFT JOIN ret_product_master p ON p.pro_id=nt.product
		// 	LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = nt.id_sub_design
		//     WHERE b.transfer_item_type=2 AND b.branch_trans_code=".$transCode."");

		// }
		else if ($s_type == 2) {
			$sql = $this->db->query("SELECT b.is_other_issue,b.transfer_item_type,p.product_name,b.branch_transfer_id,b.branch_trans_code,
    		    SUM(rnt.pieces) as piece, SUM(rnt.grs_wt) as gross_wt, SUM(rnt.net_wt) as net_wt, date_format(b.created_time,'%d-%m-%Y') as created_time,
    		    fb.name as from_branch,tb.name as to_branch,ifnull(subdes.sub_design_name,'') as sub_design_name,
				IFNULL(b.remark,'') as remark,fb.address1 as from_branch_address1,fb.address2 as from_branch_address2,tb.address1 as to_branch_address1,tb.address2 as to_branch_address2,fb.gst_number as from_gst,tb.gst_number as to_gst,from_ci.name as from_city_name,to_ci.name as to_city_name,from_st.name as from_state_name,to_st.name as to_state_name,cat.hsn_code,fb.pincode as from_pincode,tb.pincode as to_pincode,cat.name,ifnull(emp.firstname,'') as emp,IFNULL(emp.emp_code,'') as emp_code
                FROM ret_branch_transfer b 
				LEFT JOIN ret_brch_transfer_non_tag_items rnt ON rnt.transfer_id=b.branch_transfer_id
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
                LEFT JOIN ret_nontag_item nt ON nt.id_nontag_item=rnt.id_nontag_item
                LEFT JOIN ret_lot_inwards_detail d ON d.id_lot_inward_detail=rnt.id_lot_inward_detail
                LEFT JOIN ret_product_master p ON p.pro_id=nt.product
				LEFT JOIN ret_sub_design_master subdes on subdes.id_sub_design = nt.id_sub_design
				left join city from_ci on from_ci.id_city =fb.id_city
                left join city to_ci on to_ci.id_city =tb.id_city
                left join state from_st on from_st.id_state =fb.id_state
                left join state to_st on to_st.id_state=tb.id_state
				LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
				LEFT JOIN employee emp on emp.id_employee = b.create_by
                WHERE b.transfer_item_type=2 AND b.branch_trans_code=" . $this->db->escape($transCode) . "
				group by rnt.id_nontag_item
				");
		} else if ($s_type == 3) {
			$sql = $this->db->query("SELECT m.metal_type,SUM(est.gross_wt) as grs_wt,SUM(est.net_wt) as net_wt,SUM(est.rate) as amount,date_format(b.created_time,'%d-%m-%Y') as created_time,
    		    b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,bill.bill_no,
				IFNULL(b.remark,'') as remark,tag.tag_code,tag.piece,cat.name,old_cat.old_metal_cat,est.gross_wt as old_gross_wt,est.piece as old_piece,CASE WHEN  s.item_type =1 THEN est.piece else tag.piece end as piece, CASE WHEN  s.item_type=1 THEN old_cat.old_metal_cat ELSE  cat.name END as name,old_cat.old_metal_cat,est.gross_wt as old_gross_wt,case when s.item_type=1 then  est.gross_wt else (tag.gross_wt) end as grs_wt,fb.address1 as from_branch_address1,fb.address2 as from_branch_address2,tb.address1 as to_branch_address1,tb.address2 as to_branch_address2,ci.name as from_city_name,tb_city.name as to_city_name,st.name as from_state_name,tb_state.name as to_state_name,fb.pincode as from_pincode,tb.pincode as to_pincode,fb.gst_number as from_gst,tb.gst_number as to_gst,ifnull(emp.firstname,'') as emp,IFNULL(emp.emp_code,'') as emp_code
                FROM ret_branch_transfer b 
                LEFT JOIN ret_brch_transfer_old_metal s ON s.transfer_id=b.branch_transfer_id
                LEFT JOIN ret_bill_old_metal_sale_details est ON est.old_metal_sale_id=s.old_metal_sale_id
                LEFT JOIN ret_estimation_old_metal_sale_details e on e.old_metal_sale_id=est.esti_old_metal_sale_id
                LEFT JOIN ret_billing bill on bill.bill_id=est.bill_id
                LEFT JOIN ret_old_metal_type m ON m.id_metal_type=e.id_old_metal_type
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
				left join ret_taging tag on tag.tag_id=s.tag_id
				LEFT JOIN ret_product_master p ON p.pro_id=tag.product_id
				LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
				left join ret_old_metal_category old_cat on old_cat.id_old_metal_cat=est.id_old_metal_category
				left join city ci on ci.id_city =fb.id_city
                left join city tb_city on tb_city.id_city =tb.id_city
                left join state st on st.id_state =fb.id_state
                left join state tb_state on tb_state.id_state=tb.id_state
				LEFT JOIN employee emp on emp.id_employee = b.create_by
                WHERE b.transfer_item_type=3 AND b.branch_trans_code=" . $this->db->escape($transCode) . "
                " . ($print_type == 1 ? 'GROUP by e.id_old_metal_type' : 'GROUP by est.old_metal_sale_id') . "");
			//print_r($this->db->last_query());exit;
		} else if ($s_type == 4) {
			$sql = $this->db->query("SELECT i.name as item_name,b.no_of_pcs,brch.branch_trans_code,fb.name as from_branch,tb.name as to_branch,date_format(brch.created_time,'%d-%m-%Y') as created_time,
    		    brch.branch_transfer_id,brch.transfer_item_type,s.size_name,IFNULL(brch.remark,'') as remark,ifnull(emp.firstname,'') as emp,IFNULL(emp.emp_code,'') as emp_code
                FROM ret_branch_transfer_other_inventory b 
                LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id=b.branch_transfer_id
                LEFT JOIN branch fb ON fb.id_branch=brch.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=brch.transfer_to_branch
                LEFT JOIN ret_other_inventory_item i ON i.id_other_item=b.id_other_inv_item
                LEFT JOIN ret_other_inventory_size s ON s.id_inv_size=i.id_inv_size
				LEFT JOIN employee emp on emp.id_employee = brch.create_by
                WHERE brch.transfer_item_type=4 AND brch.branch_trans_code=" . $this->db->escape($transCode) . "");
			// print_r($this->db->last_query());exit;
		} else if ($s_type == 5) {
			$sql = $this->db->query("SELECT p.pro_id as pro_id,p.product_name,b.transfer_item_type,b.branch_transfer_id,d.totalitems,d.weight,br.name as from_branch,date_format(l.date,'%d-%m-%Y') as created_time,b.branch_trans_code,tb.name as to_branch,
    		    cus.order_no,IFNULL(b.remark,'') as remark,t.tag_code,IFNULL(sect.section_name,'') as section,IFNULL(tag_dia_detail.stn_wt,0) as stn_wt,d.less_wt as less_wt,d.net_wt,ifnull(emp.firstname,'') as emp,IFNULL(emp.emp_code,'') as emp_code
				FROM ret_branch_transfer b 
				LEFT JOIN ret_bt_order_log l ON l.branch_transfer_id=b.branch_transfer_id
				LEFT JOIN customerorderdetails d ON d.id_orderdetails=l.id_orderdetails
				LEFT JOIN customerorder cus on cus.id_customerorder=d.id_customerorder
				LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
				LEFT JOIN ret_taging t ON t.id_orderdetails=d.id_orderdetails
				LEFT JOIN ret_section sect on sect.id_section = t.id_section
				LEFT JOIN branch br ON br.id_branch=b.transfer_from_branch
				LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
				LEFT JOIN employee emp on emp.id_employee = b.create_by
				LEFT JOIN (SELECT tag_id,sum(wt) as stn_wt
					FROM `ret_taging_stone` as retst
					LEFT JOIN ret_uom as stuom ON stuom.uom_id = retst.uom_id 
					LEFT JOIN ret_stone as rtstn ON rtstn.stone_id = retst.stone_id
					where rtstn.stone_type = 1
					GROUP by tag_id) as tag_dia_detail ON tag_dia_detail.tag_id = t.tag_id
				WHERE b.branch_trans_code=" . $this->db->escape($transCode) . " AND l.status=1
				");
			// echo "<pre>"; print_r($this->db->last_query());exit;
		}

		/*$sql  = " 
				SELECT 
				transfer_item_type,branch_trans_code,`branch_transfer_id`,fb.name as from_branch,tb.name as to_branch, bt.`pieces`,bt.`grs_wt`,bt.`net_wt`,`status`,date_format(bt.created_time,'%d-%m-%Y') as created_time,
				if( p.product_short_code = '' or p.product_short_code is null ,p.product_name ,CONCAT(p.product_short_code ,' - ',p.product_name) ) as product, 
				if(ld.id_lot_inward_detail is null, if( nt.product_short_code = '' or nt.product_short_code is null ,nt.product_name ,CONCAT(nt.product_short_code ,' - ',nt.product_name) ),if( ld.product_short_code = '' or ld.product_short_code is null ,ld.product_name ,CONCAT(ld.product_short_code ,' - ',ld.product_name) )) as nti_product,
				nt.id_nontag_item,ld.lot_no,t.gross_wt as t_gross,t.net_wt as t_net,t.piece as t_piece,t.tag_id,t.tag_code,
				t.product_id
				FROM `ret_branch_transfer` bt 
					Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id 
					Left join ret_taging t on t.tag_id = bti.tag_id 
					Left join ret_product_master p on p.pro_id = t.product_id
					Left join (
						SELECT lot_no,id_lot_inward_detail,lot_product,product_short_code,product_name
						FROM ret_lot_inwards_detail lot_det
						Left join ret_product_master p on p.pro_id = lot_det.lot_product
					) ld on ld.id_lot_inward_detail=".($s_type==1?'bti.id_lot_inward_detail AND ld.id_lot_inward_detail is not null AND ld.id_lot_inward_detail != 0':'bt.id_lot_inward_detail AND bt.id_lot_inward_detail is not null AND bt.id_lot_inward_detail != 0')."
					  
					Left join (
						SELECT nti.id_nontag_item,nti.product,product_short_code,product_name
						FROM ret_nontag_item nti
						Left join ret_product_master p on p.pro_id = nti.product
					) nt on nt.id_nontag_item = bt.id_nontag_item AND nt.id_nontag_item is not null AND nt.id_nontag_item != 0
					Left join branch fb on fb.id_branch = bt.transfer_from_branch 
					Left join branch tb on tb.id_branch = bt.transfer_to_branch 
				WHERE branch_trans_code='".$transCode."' GROUP BY ".($s_type==1? ($print_type==1 ? 't.product_id':'t.tag_id'):($print_type==1 ? 'nt.product':'nt.branch_transfer_id'))." "; */
		return $sql->result_array();
	}
	function getBTransDataSummary($transCode, $s_type, $print_type)
	{
		$sql = $this->db->query("SELECT b.is_other_issue,b.transfer_item_type,tag.tag_code,b.branch_transfer_id,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,p.product_name,SUM(tag.piece) as piece,
		SUM(tag.gross_wt) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt,date_format(b.created_time,'%d-%m-%Y') as created_time,
		tag.id_section,sect.section_name,ifnull(tag_stn.stn_wt,0) as stn_wt,ifnull(tag_dia.dia_wt,0) as dia_wt,IFNULL(des.design_name,'') as design_name,
		IFNULL(b.remark,'') as remark
		FROM ret_branch_transfer b
		LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
		LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
		LEFT JOIN ret_brch_transfer_tag_items t ON t.transfer_id=b.branch_transfer_id
		LEFT JOIN ret_taging tag ON tag.tag_id=t.tag_id
		LEFT JOIN ret_section sect on sect.id_section = tag.id_section
		LEFT JOIN ret_product_master p ON p.pro_id=tag.product_id
		LEFT JOIN ret_design_master des on des.design_no = tag.design_id
		LEFT JOIN (SELECT tag_st.tag_id,tag.product_id,sum(if(tag_st.uom_id=6,round((tag_st.wt/5),3),tag_st.wt)) as stn_wt 
		FROM ret_taging_stone tag_st
		LEFT JOIN ret_stone st on st.stone_id = tag_st.stone_id
		LEFT JOIN ret_taging tag on tag.tag_id = tag_st.tag_id
		LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id=tag.tag_id
		LEFT JOIN ret_branch_transfer b on b.branch_transfer_id = t.transfer_id
		WHERE st.stone_type!=1 and b.branch_trans_code=" . $this->db->escape($transCode) . "
		GROUP by tag.product_id) as tag_stn on tag_stn.product_id = tag.product_id
		LEFT JOIN (SELECT tag_st.tag_id,tag.product_id,sum(round((tag_st.wt),3)) as dia_wt
		FROM ret_taging_stone tag_st
		LEFT JOIN ret_stone st on st.stone_id = tag_st.stone_id
		LEFT JOIN ret_taging tag on tag.tag_id = tag_st.tag_id
		LEFT JOIN ret_brch_transfer_tag_items t ON t.tag_id=tag.tag_id
		LEFT JOIN ret_branch_transfer b on b.branch_transfer_id = t.transfer_id
		WHERE st.stone_type=1 and b.branch_trans_code=" . $this->db->escape($transCode) . "
		GROUP by tag.product_id) as tag_dia on tag_dia.product_id = tag.product_id
		WHERE b.branch_trans_code=" . $this->db->escape($transCode) . "
		GROUP by tag.product_id");
		// print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	function get_verifMobNo($branch)
	{
		$sql = $this->db->query("SELECT otp_verif_mobileno FROM `branch` WHERE id_branch=" . $this->db->escape($branch));
		if ($sql && $sql->num_rows() > 0) {
			return $sql->row()->otp_verif_mobileno;
		}
		return null;
	}

	function checkNonTagItemExist($data, $to_branch)
	{
		$r = array("status" => FALSE);
		$sql = "SELECT id_nontag_item FROM ret_nontag_item WHERE product=" . $data['product'] . " AND design=" . $data['design'] . "
		AND id_sub_design=" . $data['id_sub_design'] . "
		" . ($data['id_section'] != '' ? " and id_section=" . $data['id_section'] . "" : '') . "
		AND branch=" . $to_branch;
		$res = $this->db->query($sql);
		if ($res->num_rows() > 0) {
			$r = array("status" => TRUE, "id_nontag_item" => $res->row()->id_nontag_item);
		} else {
			$r = array("status" => FALSE, "id_nontag_item" => "");
		}
		return $r;
	}

	function updateNTData($data, $arith)
	{
		$sql = "UPDATE ret_nontag_item SET no_of_piece=(no_of_piece" . $arith . " " . $data['no_of_piece'] . "),gross_wt=(gross_wt" . $arith . " " . $data['gross_wt'] . "),net_wt=(net_wt" . $arith . " " . $data['net_wt'] . "),updated_by=" . $data['updated_by'] . ",updated_on='" . $data['updated_on'] . "' WHERE id_nontag_item=" . $data['id_nontag_item'];
		$status = $this->db->query($sql);
		return $status;
	}

	function getBTtags($trans_id, $approval_type)
	{
		if ($approval_type == 1) {
			$sql = "SELECT btag.tag_id, ifnull(tag.tag_type, 0) as tag_type,IFNULL(tag.id_section,'') as id_section
    		FROM ret_brch_transfer_tag_items btag 
    		LEFT JOIN ret_taging tag on tag.tag_id=btag.tag_id 
    		WHERE tag.tag_status=0 and transfer_id=" . $trans_id;
		} else {
			$sql = "SELECT btag.tag_id, ifnull(tag.tag_type, 0) as tag_type,IFNULL(tag.id_section,'') as id_section
    		FROM ret_brch_transfer_tag_items btag
    		LEFT JOIN ret_taging tag on tag.tag_id=btag.tag_id
    		WHERE tag.tag_status=4 and transfer_id=" . $trans_id;
		}
		//print_r($this->db->last_query());exit;
		return $this->db->query($sql)->result_array();
	}

	function get_tag_details($tag_id)
	{
		$sql = $this->db->query("SELECT IFNULL(t.id_section,'') as id_section FROM ret_taging t WHERE t.tag_id = " . $tag_id . "");
		return $sql->row_array();
	}

	function getBTBranches()
	{
		$branch = $this->db->query("SELECT b.is_ho,b.name,b.id_branch,b.gst_number FROM branch b Where active=1 and b.gst_number IS NOT NULL");
		return $branch->result_array();
	}

	function getSettigsByName($name)
	{
		$branch = $this->db->query("SELECT value FROM ret_settings b Where name='" . $name . "'");
		return $branch->row('value');
	}

	function get_profile_settings($id_profile)
	{
		$sql = $this->db->query("SELECT * FROM `profile` WHERE id_profile=" . $id_profile . "");
		return $sql->row_array();
	}


	function get_ajaxBranchTransferlist($from_date, $to_date)
	{
		$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));
		$login_branch = $this->session->userdata('id_branch');
		$sql = $this->db->query("SELECT b.status,b.transfer_item_type,b.branch_transfer_id,b.branch_trans_code,date_format(b.created_time,'%d-%m-%Y') as created_date,
	    if(b.status=1,'Yet to Approve',if(b.status=2,'Intransit',if(b.status=3,'Rejected','Stock Updated'))) as bt_status,if(b.transfer_item_type=1,'Tagged Items',if(b.transfer_item_type=2,'Non Tagged Items',if(b.transfer_item_type=3,'Partly / SR',if(b.transfer_item_type=4,'Packaging Items','Repair Items')))) as item_type,
	    fb.name as from_branch,tb.name as to_branch,b.pieces,b.grs_wt,IFNULL(repair.weight,0) as repair_weight,IFNULL(repair.totalitems,0) as repair_totalitems
        FROM ret_branch_transfer b 
        LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
        LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
        LEFT JOIN(SELECT b.branch_transfer_id,SUM(d.totalitems) as totalitems,SUM(d.weight) as weight
        FROM ret_branch_transfer b 
        LEFT JOIN ret_bt_order_log l ON l.branch_transfer_id=b.branch_transfer_id
        LEFT JOIN customerorderdetails d ON d.id_orderdetails=l.id_orderdetails
        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
        LEFT JOIN branch br ON br.id_branch=b.transfer_from_branch
        LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
        group by b.branch_transfer_id) as repair on repair.branch_transfer_id=b.branch_transfer_id
				
        Where date(b.created_time) BETWEEN '" . $from_date . "' and '" . $to_date . "'
        " . ($login_branch != 0 ? " and (b.transfer_from_branch=" . $login_branch . " or b.transfer_to_branch=" . $login_branch . ")" : '') . "
        " . ($profile_settings['allow_bill_type'] == 3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type'] == 1 ? " and b.is_eda=1" : " and b.is_eda=2")) . "
        ");
		//==print_r($this->db->last_query());exit;
		$return_data = $sql->result_array();
		if ($login_branch != '' && $login_branch != 0) {
			$dayClose = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=" . $login_branch);
			$cur_entry_date = $dayClose->row()->entry_date;
			if ($profile_settings['allow_bill_type'] == 2) {
				if ($from_date != $cur_entry_date) {
					$return_data = [];
				}
			}
		}
		return $return_data;
	}
	function get_profile_details($id_profile)
	{
		$sql = $this->db->query("SELECT * from profile where id_profile=" . $id_profile . "");
		return $sql->row_array();
	}


	/*function get_purchase_items($data)
	{
		$return_Data=[];
		
		$partlySale=$this->db->query("
        SELECT '0' as tot_item_cost,cat.id_metal,mt.metal as metal_name,IFNULL((SUM(tag.net_wt)-SUM(d.net_wt)),0) as net_wt,IFNULL((SUM(tag.gross_wt)-SUM(d.gross_wt)),0) as gross_wt
        FROM ret_taging tag 
        LEFT JOIN ret_bill_details d ON d.tag_id=tag.tag_id
        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        
        Left join (
        SELECT bti.tag_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and transfer_item_type=3 and bt.transfer_from_branch=".$data['from_branch']."
        GROUP BY bti.tag_id) btrans on btrans.tag_id = tag.tag_id
        
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        WHERE tag.is_partial=1 AND btrans.tag_id iS NULL and b.bill_status=1 and tag.trans_to_acc_stock=0
        ".($data['from_branch']!='' ?  " and b.id_branch=".$data['from_branch']."" :'')."
		 and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."') 
        GROUP by cat.id_metal");
        //print_r($this->db->last_query());exit;
        $partlySale_Result=$partlySale->result_array();
        foreach($partlySale_Result as $items)
        {
                if($items['id_metal']==1)
                {
                    $type='partly_sale_gold';
                }else if($items['id_metal']==2)
                {
                    $type='partly_sale_silver';
                }
                $return_Data[]=array(
        			'type'              =>$type,
        			'metal_type'		=>'PARTLY SALE'.'-'.$items['metal_name'],
        			'metal_name'		=>$items['metal_name'],
        			'id_metal'		    =>$items['id_metal'],
        			'gross_wt'			=>$items['gross_wt'],
        			'rate'	            =>$items['tot_item_cost'],
        			'net_wt'			=>$items['net_wt'],
        			'bill_det'			=>$this->get_partly_sale_details($data['from_date'],$data['to_date'],$data['from_branch'],$items['id_metal']),
    		    );
        }
        
        
        $sales_ret=$this->db->query("
		SELECT IFNULL(SUM(d.gross_wt),0) as gross_wt,(sum(d.net_wt)) as net_wt,mt.metal as metal_name,mt.id_metal,IFNULL(SUM(d.item_cost),0) as tot_item_cost
        FROM ret_bill_return_details r 
        LEFT JOIN ret_billing b ON b.bill_id=r.bill_id
        LEFT JOIN ret_bill_details d ON d.bill_det_id=r.ret_bill_det_id
        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id
        LEFT JOIN ret_product_master prod ON prod.pro_id=d.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=prod.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        
        Left join (
        SELECT bti.tag_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and transfer_item_type=3 and bt.transfer_from_branch=".$data['from_branch']."
        GROUP BY bti.tag_id) btrans on btrans.tag_id = tag.tag_id 
        WHERE d.status=2 AND  btrans.tag_id iS NULL And tag.tag_status=6 and tag.trans_to_acc_stock=0 and b.bill_status=1 
        ".($data['from_branch']!='' ?  " and b.id_branch=".$data['from_branch']."" :'')."
		 and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."') 
		GROUP by cat.id_metal");
		//print_r($this->db->last_query());exit;
        $sales_ret_result=$sales_ret->result_array();
        foreach($sales_ret_result as $items)
        {
           
                if($items['id_metal']==1)
                {
                    $type='sales_ret_items_gold';
                }else if($items['id_metal']==2)
                {
                    $type='sales_ret_items_silver';
                }
                $return_Data[]=array(
        			'type'              =>$type,
        			'metal_type'		=>'SALES RETURN'.'-'.$items['metal_name'],
        			'metal_name'		=>$items['metal_name'],
        			'id_metal'		    =>$items['id_metal'],
        			'gross_wt'			=>$items['gross_wt'],
        			'rate'	            =>$items['tot_item_cost'],
        			'net_wt'			=>$items['net_wt'],
        			'bill_det'			=>$this->get_sales_ret_details($data['from_date'],$data['to_date'],$data['from_branch'],$items['id_metal']),
    		    );
           
             
        }
        
        $sql=$this->db->query("SELECT s.old_metal_sale_id,IFNULL(SUM(s.gross_wt),0) as gross_wt,IFNULL(SUM(s.net_wt),0) as net_wt,IFNULL(SUM(s.rate),0) as rate,
        s.metal_type,est.id_old_metal_type,btrans.old_metal_sale_id
        FROM ret_billing b 
        LEFT JOIN ret_bill_old_metal_sale_details s ON s.bill_id=b.bill_id
        LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type=est.id_old_metal_type
        
        Left join (
        SELECT bti.old_metal_sale_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and transfer_item_type=3 and bt.transfer_from_branch=".$data['from_branch']."
        GROUP BY bti.old_metal_sale_id) btrans on btrans.old_metal_sale_id = s.old_metal_sale_id 
        
        WHERE b.bill_status=1 AND s.old_metal_sale_id IS NOT null AND  btrans.old_metal_sale_id iS NULL
        ".($data['from_branch']!='' ?  " and s.current_branch=".$data['from_branch']."" :'')."
        and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."') 
        and s.is_transferred=0
        group by s.metal_type");
        
		//print_r($this->db->last_query());exit;
		
		$result=$sql->result_array();
	    foreach($result as $items)
	    {
	        if($items['metal_type']==1)
	        {
	            $type='old_metal_gold';
	        }else
	        {
	            $type='old_metal_silver';
	        }
            $return_Data[]=array(
            'type'              =>'old_metal_items',
            'metal_type'		=>'OLD METAL'.' '.($items['metal_type']==1 ? 'GOLD' :'SILVER'),
            'gross_wt'			=>$items['gross_wt'],
            'net_wt'			=>$items['net_wt'],
            'rate'				=>$items['rate'],
            'type'              =>$type,
            'bill_det'			=>$this->old_metal_bill_details($data['from_date'],$data['to_date'],$data['from_branch'],$items['metal_type']),
            );
	    }
	
        
		return $return_Data;
	}
    
    function get_partly_sale_details($from_date,$to_date,$id_branch,$id_metal)
    {
        $sql=$this->db->query("SELECT (IFNULL(tag.gross_wt,0)-IFNULL(t.sold_gross_wt,0)) as gross_wt,'0' as amount,cat.id_metal,mt.metal as metal_name,
        DATE_FORMAT(bill.bill_date,'%d-%m-%Y') as bill_date,bill.bill_no,bill.bill_id,'0' as is_checked,d.tag_id as trans_id,
        (IFNULL(tag.net_wt,0)-IFNULL(t.sold_net_wt,0)) as net_wt,
        if(mt.id_metal=1,'partly_sale_gold','partly_sale_silver') as item_type,'3' as transfer_items,d.bill_det_id
        FROM ret_bill_details d 
        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id
        LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        
        Left join (
        SELECT bti.tag_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and transfer_item_type=3 and bt.transfer_from_branch=".$id_branch."
        GROUP BY bti.tag_id) btrans on btrans.tag_id = tag.tag_id
        
        LEFT JOIN (SELECT IFNULL(s.sold_gross_wt,0) as sold_gross_wt,IFNULL(s.sold_net_wt,0) as sold_net_wt,s.tag_id
                  FROM ret_partlysold s 
                  LEFT JOIN ret_taging tag ON tag.tag_id=s.tag_id
                  LEFT JOIN ret_bill_details d ON d.bill_det_id=s.sold_bill_det_id
                  LEFT JOIN ret_billing b ON b.bill_id=d.bill_id
                  LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        		  LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
                  WHERE b.bill_status=1
                  ".($id_branch!='' ?  " and b.id_branch=".$id_branch."" :'')."
		          and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') 
                  ) as t ON t.tag_id=d.tag_id
        WHERE bill.bill_status=1 AND d.is_partial_sale=1 AND btrans.tag_id iS NULL AND tag.tag_status=1 and tag.trans_to_acc_stock=0 and mt.id_metal=".$id_metal."
        ".($id_branch!='' ?  " and bill.id_branch=".$id_branch."" :'')."
		and (date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') 
        ");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
    }
    
    
    function get_sales_ret_details($from_date,$to_date,$id_branch,$id_metal)
    {
        $sql=$this->db->query("SELECT IFNULL((t.gross_wt),0) as gross_wt,IFNULL((t.net_wt),0) as net_wt,
        DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.bill_no,b.bill_id,'0' as is_checked,'0' as amount,
        t.tag_id as trans_id,if(mt.id_metal=1,'sales_ret_items_gold','sales_ret_items_silver') as type,'2' as transfer_items,IFNULL(d.item_cost,0) as amount,
        if(mt.id_metal=1,'sales_ret_items_gold','sales_ret_items_silver') as item_type
        FROM ret_billing b 
        LEFT JOIN ret_bill_return_details r ON r.bill_id=b.bill_id
        LEFT JOIN ret_bill_details d ON d.bill_det_id=r.ret_bill_det_id
        LEFT JOIN ret_taging t ON t.tag_id=d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id=t.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        
        Left join (
        SELECT bti.tag_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and transfer_item_type=3 and bt.transfer_from_branch=".$id_branch."
        GROUP BY bti.tag_id) btrans on btrans.tag_id = t.tag_id 
        
        WHERE t.tag_status=6 AND  btrans.tag_id iS NULL AND b.bill_status=1 AND t.trans_to_acc_stock=0 and mt.id_metal=".$id_metal."
        ".($id_branch!='' ?  " and b.id_branch=".$id_branch."" :'')."
		and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
    }
    
	function old_metal_bill_details($from_date,$to_date,$id_branch,$metal_type)
	{
		$sql=$this->db->query("SELECT s.old_metal_sale_id as trans_id,s.gross_wt as gross_wt,s.net_wt as net_wt,s.rate as amount,est.id_old_metal_type,
		t.metal_type,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.bill_no,b.bill_id,'0' as is_checked,'old_metal_items' as type,s.old_metal_sale_id,
		if(s.metal_type=1,'old_metal_gold','old_metal_silver') as item_type,'1' as transfer_items
		FROM ret_billing b 
		LEFT JOIN ret_bill_old_metal_sale_details s ON s.bill_id=b.bill_id
		LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
		LEFT JOIN ret_old_metal_type t ON t.id_metal_type=est.id_old_metal_type
		
		Left join (
        SELECT bti.old_metal_sale_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and transfer_item_type=3 and bt.transfer_from_branch=".$id_branch."
        GROUP BY bti.old_metal_sale_id) btrans on btrans.old_metal_sale_id = s.old_metal_sale_id 
        
		WHERE b.bill_status=1 AND s.old_metal_sale_id IS NOT null AND  btrans.old_metal_sale_id iS NULL
		".($id_branch!='' ?  " and s.current_branch=".$id_branch."" :'')."
		".($metal_type!='' ?  " and s.metal_type=".$metal_type."" :'')."
		and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') 
		 and s.is_transferred=0");
		 return $sql->result_array();
	}
    
    
    function get_purchase_items_details($transCode,$s_type,$print_type)
	{
	           $oldMetal=$this->db->query("SELECT IFNULL(SUM(s.gross_wt),0) as grs_wt,IFNULL(SUM(s.net_wt),0) as net_wt,IFNULL(SUM(s.rate),0) as amount,
                date_format(b.created_time,'%d-%m-%Y') as created_time,
                b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,
                if(s.metal_type=1,'OLD GOLD','OLD SILVER') as item_type,bill.bill_no,p.item_type as transfer_item,s.metal_type,if(s.metal_type=1,'GOLD','SILVER') as metal_name
                FROM ret_branch_transfer  b 
                LEFT JOIN ret_brch_transfer_old_metal p ON p.transfer_id=b.branch_transfer_id
                LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id=p.old_metal_sale_id
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
                LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
                LEFT JOIN ret_billing bill ON bill.bill_id=s.bill_id
                WHERE b.transfer_item_type=3 AND b.branch_trans_code=".$transCode."
                and p.item_type=1
                ".($print_type==2 ? " GROUP by s.old_metal_sale_id" :'GROUP by s.metal_type')."");
                $result['old_metal_details']=$oldMetal->result_array();
                
                $salesReturn=$this->db->query("SELECT IFNULL(SUM(d.gross_wt),0) as grs_wt,IFNULL(SUM(d.net_wt),0) as net_wt,IFNULL(SUM(d.item_cost),0) as amount,
                date_format(b.created_time,'%d-%m-%Y') as created_time,
                b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,
                if(cat.id_metal=1,'SALES RETURN - GOLD','SALES RETURN - SILVER') as item_type,p.item_type as transfer_item,bill.bill_no,cat.id_metal as metal_type,
                if(cat.id_metal=1,'GOLD','SILVER') as metal_name,pro.product_name
                FROM ret_branch_transfer b 
                LEFT JOIN ret_brch_transfer_old_metal p ON p.transfer_id=b.branch_transfer_id
                LEFT JOIN ret_bill_details d ON d.tag_id=p.tag_id
                LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
                LEFT JOIN ret_product_master pro ON pro.pro_id=d.product_id
                LEFT JOIN ret_category cat ON cat.id_ret_category=pro.cat_id
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
                WHERE b.transfer_item_type=3 and d.status=2 and bill.bill_status=1 and p.item_type=2 AND b.branch_trans_code=".$transCode."
                GROUP by d.product_id
                Having net_wt>0");
                $result['sales_return_details']=$salesReturn->result_array();
                
                $partlySale=$this->db->query("SELECT  date_format(b.created_time,'%d-%m-%Y') as created_time,
                b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,IFNULL(SUM(tag.gross_wt),0) as tag_gwt,IFNULL(sold.tot_sold_gwt,0) as tot_sold_gwt,
                (IFNULL(SUM(tag.gross_wt),0)-IFNULL(sold.tot_sold_gwt,0)) as grs_wt,(IFNULL(SUM(tag.net_wt),0)-IFNULL(sold.tot_sold_nwt,0)) as net_wt,'0' as amount,
                if(cat.id_metal=1,'GOLD','SILVER') as metal_name,if(cat.id_metal=1,'PARTLY SALE - GOLD','PARTLY SALE - SILVER') as item_type,
                p.product_name
                FROM ret_branch_transfer b 
                LEFT JOIN ret_brch_transfer_old_metal s on s.transfer_id=b.branch_transfer_id
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
                LEFT JOIN ret_taging tag ON tag.tag_id=s.tag_id
                LEFT JOIN ret_bill_details d ON d.tag_id=tag.tag_id
                LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
                LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
                LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
                LEFT JOIN (
                            SELECT IFNULL(SUM(d.gross_wt),0) as tot_sold_gwt,IFNULL(SUM(d.net_wt),0) as tot_sold_nwt,d.tag_id,cat.id_metal
                            FROM ret_bill_details d 
                            LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
                            LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
                            LEFT JOIN ret_billing b on b.bill_id=d.bill_id
                            LEFT JOIN ret_brch_transfer_old_metal t ON t.tag_id=d.tag_id
                            LEFT JOIN ret_branch_transfer br ON br.branch_transfer_id=t.transfer_id
                            WHERE b.bill_Status=1 AND d.is_partial_sale=1 AND t.tag_id is NOT NULL AND br.branch_trans_code=".$transCode."
                            GROUP by d.product_id 
                           ) as sold ON sold.id_metal=cat.id_metal
                WHERE b.branch_trans_code=".$transCode." AND s.item_type=3 AND bill.bill_status=1
                GROUP by d.product_id");
                //print_r($this->db->last_query());exit;
                $result['partly_sales_details']=$partlySale->result_array();
            
                
            return $result;
	}*/



	function get_purchase_items($data)
	{
		$return_Data = [];

		$partlySale = $this->db->query("SELECT * FROM metal WHERE metal_status = 1");
		$partlySale_Result = $partlySale->result_array();
		foreach ($partlySale_Result as $items) {
			/*if($items['id_metal']==1)
                {
                    $type='partly_sale_gold';
                }else if($items['id_metal']==2)
                {
                    $type='partly_sale_silver';
                }*/
			$type = 'partly_sale_' . strtolower($items['metal']);
			$itemDetails = $this->get_partly_sale_details($data['from_date'], $data['to_date'], $data['from_branch'], $items['id_metal'], $data['bill_type']);
			$gross_wt = 0;
			$net_wt   = 0;
			$tot_item_cost   = 0;
			$dia_wt   = 0;
			foreach ($itemDetails as $val) {
				$dia_wt += $val['dia_wt'];
				$gross_wt += $val['gross_wt'];
				$net_wt += $val['net_wt'];
				$tot_item_cost += $val['tot_item_cost'];
			}
			if ($gross_wt > 0) {
				$return_Data[] = array(
					'type'              => $type,
					'metal_type'		=> 'PARTLY SALE' . '-' . $items['metal'],
					'metal_name'		=> $items['metal'],
					'id_metal'		    => $items['id_metal'],
					'dia_wt'			=> number_format($dia_wt, 3, '.', ''),
					'gross_wt'			=> number_format($gross_wt, 3, '.', ''),
					'rate'	            => number_format($tot_item_cost, 2, '.', ''),
					'net_wt'			=> number_format($net_wt, 3, '.', ''),
					'bill_det'			=> $this->get_partly_sale_details($data['from_date'], $data['to_date'], $data['from_branch'], $items['id_metal'], $data['bill_type']),
				);
			}
		}


		$sales_ret = $this->db->query("SELECT * FROM metal WHERE metal_status = 1");
		//print_r($this->db->last_query());exit;
		$sales_ret_result = $sales_ret->result_array();
		foreach ($sales_ret_result as $items) {
			/*if($items['id_metal']==1)
                {
                    $type='sales_ret_items_gold';
                }else if($items['id_metal']==2)
                {
                    $type='sales_ret_items_silver';
                }*/

			$type = 'sales_ret_items_' . strtolower($items['metal']);

			$sales_return_det = $this->get_sales_ret_details($data['from_date'], $data['to_date'], $data['from_branch'], $items['id_metal'], $data['bill_type']);
			$gross_wt = 0;
			$net_wt   = 0;
			$dia_wt   = 0;
			$tot_item_cost   = 0;
			foreach ($sales_return_det as $val) {
				$gross_wt += $val['gross_wt'];
				$net_wt += $val['net_wt'];
				$dia_wt += $val['dia_wt'];
				$tot_item_cost += $val['amount'];
			}
			if ($gross_wt > 0 || $tot_item_cost > 0) {
				$return_Data[] = array(
					'type'              => $type,
					'metal_type'		=> 'SALES RETURN' . '-' . $items['metal'],
					'metal_name'		=> $items['metal'],
					'id_metal'		    => $items['id_metal'],
					'dia_wt'			=> number_format($dia_wt, 3, '.', ''),
					'gross_wt'			=> number_format($gross_wt, 3, '.', ''),
					'rate'	            => number_format($tot_item_cost, 3, '.', ''),
					'net_wt'			=> number_format($net_wt, 3, '.', ''),  // BRN-D11 fix: was $gross_wt (copy-paste swap)
					'bill_det'			=> $sales_return_det,
				);
			}
		}

		$sql = $this->db->query("SELECT s.old_metal_sale_id,IFNULL(SUM(s.gross_wt),0) as gross_wt,IFNULL(SUM(s.net_wt),0) as net_wt,IFNULL(SUM(s.rate),0) as rate,
        s.metal_type,s.id_old_metal_type, met.metal,
		FORMAT(IFNULL(SUM(bill_st.dia_wt), 0), 3) AS dia_wt
        FROM ret_billing b 
        LEFT JOIN ret_bill_old_metal_sale_details s ON s.bill_id=b.bill_id
        LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type=s.id_old_metal_type 
        LEFT JOIN metal as met ON met.id_metal = t.id_metal 
		LEFT JOIN (SELECT IFNULL(SUM(bill_st.wt),0) as dia_wt,bill_st.old_metal_sale_id
		FROM ret_billing_item_stones bill_st
		LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=bill_st.old_metal_sale_id
		LEFT JOIN ret_stone st ON st.stone_id = bill_st.stone_id
		LEFT JOIN ret_billing bill ON bill.bill_id = bill_st.bill_id
		WHERE bill.bill_status=1 and st.stone_type = 1	 
		GROUP BY bill_st.old_metal_sale_id) as bill_st ON bill_st.old_metal_sale_id = s.old_metal_sale_id
        
        Left join (
        SELECT bti.old_metal_sale_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and transfer_item_type=3 and bt.transfer_from_branch=" . $data['from_branch'] . "
        GROUP BY bti.old_metal_sale_id) btrans on btrans.old_metal_sale_id = s.old_metal_sale_id 
        
        WHERE b.bill_status=1 AND s.old_metal_sale_id IS NOT null AND  btrans.old_metal_sale_id iS NULL
        " . ($data['from_branch'] != '' ?  " and s.current_branch=" . $data['from_branch'] . "" : '') . "
        and (date(b.bill_date) BETWEEN '" . date('Y-m-d', strtotime($data['from_date'])) . "' AND '" . date('Y-m-d', strtotime($data['to_date'])) . "') 
		and b.is_eda=" . $data['bill_type'] . "
        and s.is_transferred=0 
        group by s.metal_type");

		//print_r($this->db->last_query());exit;

		$result = $sql->result_array();
		foreach ($result as $items) {
			/*if($items['metal_type']==1)
	        {
	            $type='old_metal_gold';
	        }else
	        {
	            $type='old_metal_silver';
	        }*/
			$type = 'old_metal_' . strtolower($items['metal']);
			$return_Data[] = array(
				'type'              => $type,
				'metal_type'		=> 'OLD METAL' . ' ' . strtoupper($items['metal']),
				'dia_wt'			=> $items['dia_wt'],
				'gross_wt'			=> $items['gross_wt'],
				'net_wt'			=> $items['net_wt'],
				'rate'				=> $items['rate'],
				//'type'              =>$type,
				'bill_det'			=> $this->old_metal_bill_details($data['from_date'], $data['to_date'], $data['from_branch'], $items['metal_type'], $data['bill_type']),
			);
		}


		return $return_Data;
	}

	function get_partly_sale_details($from_date, $to_date, $id_branch, $id_metal, $bill_type)
	{
		$sql = $this->db->query("SELECT (IFNULL(tag.gross_wt,0)-IFNULL(t.sold_gross_wt,0)) as gross_wt,'0' as amount,cat.id_metal,mt.metal as metal_name,
        DATE_FORMAT(bill.bill_date,'%d-%m-%Y') as bill_date,bill.sales_ref_no as bill_no,bill.bill_id,'0' as is_checked,d.tag_id,d.bill_det_id,
        (IFNULL(tag.net_wt,0)-IFNULL(t.sold_net_wt,0)) as net_wt,
		FORMAT(IFNULL(stn.dia_wt,0)-IFNULL(bill_st.sold_wt,0),3) as dia_wt,
        if(mt.id_metal=1,'partly_sale_gold','partly_sale_silver') as item_type,'3' as transfer_items,d.bill_det_id as trans_id,tag.gross_wt as tagged_gwt,
        IFNULL(brch.gross_wt,0) as transfered_wt,'' as is_non_tag
        FROM ret_bill_details d 
        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id
        LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        
        Left join (
        SELECT bti.tag_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and bti.item_type=3 and bt.transfer_from_branch=" . $id_branch . "
        GROUP BY bti.tag_id) btrans on btrans.tag_id = tag.tag_id
        
        Left join (
        SELECT bti.tag_id,IFNULL(SUM(bti.gross_wt),0) as gross_wt
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE bti.item_type=3 and bt.transfer_from_branch=" . $id_branch . "
        GROUP BY bti.tag_id) brch on brch.tag_id = tag.tag_id
		LEFT join(SELECT IFNULL(SUM(st.wt),0) as dia_wt,st.tag_id
        FROM ret_taging_stone st
		LEFT JOIN ret_bill_details d ON d.tag_id=st.tag_id
		LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
        LEFT JOIN ret_stone s ON s.stone_id=st.stone_id
        LEFT JOIN ret_uom uom ON uom.uom_id=s.uom_id
        WHERE bill.bill_status=1  AND d.tag_id IS NOT NULL AND s.stone_type=1
		" . ($id_branch != '' ?  " and bill.id_branch=" . $id_branch . "" : '') . "
		and (date(bill.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
		and bill.is_eda=" . $bill_type . " 
        group by st.tag_id) as stn ON stn.tag_id = tag.tag_id
		LEFT JOIN (SELECT IFNULL(SUM(bill_st.wt),0) as sold_wt,d.tag_id
		FROM ret_billing_item_stones bill_st
		LEFT JOIN ret_stone st ON st.stone_id = bill_st.stone_id
		LEFT JOIN ret_bill_details d ON d.bill_det_id = bill_st.bill_det_id
		LEFT JOIN ret_billing bill ON bill.bill_id = d.bill_id
		WHERE  bill.bill_status=1  AND d.tag_id IS NOT NULL AND st.stone_type=1
		" . ($id_branch != '' ?  " and bill.id_branch=" . $id_branch . "" : '') . "
		and (date(bill.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
		and bill.is_eda=" . $bill_type . " 
		GROUP BY d.tag_id) as bill_st ON bill_st.tag_id = tag.tag_id
        
        LEFT JOIN (SELECT IFNULL(sum(d.gross_wt),0) as sold_gross_wt,IFNULL(sum(d.net_wt),0) as sold_net_wt,d.tag_id
                  FROM ret_taging tag
                  LEFT JOIN ret_bill_details d ON d.tag_id=tag.tag_id
                  LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
                  LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        		  LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
                  WHERE bill.bill_status=1  AND d.tag_id IS NOT NULL
                  " . ($id_branch != '' ?  " and bill.id_branch=" . $id_branch . "" : '') . "
		          and (date(bill.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
                  GROUP BY d.tag_id) as t ON t.tag_id=d.tag_id
				  
        WHERE bill.bill_status=1 AND d.is_partial_sale=1 AND btrans.tag_id iS NULL AND mt.id_metal=" . $id_metal . "
        " . ($id_branch != '' ?  " and bill.id_branch=" . $id_branch . "" : '') . "
		and (date(bill.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') 
		and bill.is_eda=" . $bill_type . " 
        HAVING gross_wt > transfered_wt");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}


	function get_sales_ret_details($from_date, $to_date, $id_branch, $id_metal, $bill_type)
	{
		$returnData = array();
		$sql = $this->db->query("SELECT IFNULL((d.gross_wt),0) as gross_wt,IFNULL((d.net_wt),0) as net_wt,
        DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.s_ret_refno as bill_no,b.bill_id,'0' as is_checked,'0' as amount,
        if(d.is_non_tag=0,IFNULL(t.tag_id,d.bill_det_id),d.bill_det_id) as trans_id,if(mt.id_metal=1,'sales_ret_items_gold','sales_ret_items_silver') as type,'2' as transfer_items,IFNULL(d.item_cost,0) as amount,
        if(mt.id_metal=1,'sales_ret_items_gold','sales_ret_items_silver') as item_type,IFNULL(brch.gross_wt,0) as transfered_wt,t.gross_wt as tagged_gwt,t.tag_id,
        p.sales_mode,t.trans_to_acc_stock,d.is_non_tag,t.tag_status,IFNULL(btrans.tag_id,'') as btrans_tag_id,d.transferred_to_acc_stock,d.bill_det_id,
        IFNULL(non_tag_brch.sold_bill_det_id,'') as non_tag_brch_det_id,FORMAT(IFNULL(bill_st.dia_wt,0),3) as dia_wt,IFNULL(d.item_type,0) as det_item_type
        FROM ret_billing b 
        LEFT JOIN ret_bill_return_details r ON r.bill_id=b.bill_id
        LEFT JOIN ret_bill_details d ON d.bill_det_id=r.ret_bill_det_id
        LEFT JOIN ret_taging t ON t.tag_id=d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        
        Left join (
        SELECT bti.tag_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and bti.item_type = 2 and bt.transfer_from_branch=" . $id_branch . "
        GROUP BY bti.tag_id) btrans on btrans.tag_id = t.tag_id 
        
        Left join (
        SELECT bti.tag_id,IFNULL(SUM(bti.gross_wt),0) as gross_wt
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE transfer_item_type=3 and bti.item_type = 2 and bt.transfer_from_branch=" . $id_branch . "
        GROUP BY bti.tag_id) brch on brch.tag_id = t.tag_id
		LEFT JOIN (SELECT IFNULL(bill_st.wt,0) as dia_wt,d.bill_det_id
		FROM ret_billing_item_stones bill_st
		LEFT JOIN ret_stone st ON st.stone_id = bill_st.stone_id
		LEFT JOIN ret_bill_return_details r ON r.ret_bill_det_id= bill_st.bill_det_id
		LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id
		LEFT JOIN ret_billing bill ON bill.bill_id = r.ret_bill_id
		LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
		WHERE  bill.bill_status=1  and st.stone_type = 1 and mt.id_metal=" . $id_metal . "
		" . ($id_branch != '' ?  " and bill.id_branch=" . $id_branch . "" : '') . "
		and (date(bill.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
		and bill.is_eda=" . $bill_type . "
		GROUP BY bill_st.bill_det_id) as bill_st ON bill_st.bill_det_id = r.ret_bill_det_id
        
        Left join (
        SELECT bti.sold_bill_det_id,IFNULL(SUM(bti.gross_wt),0) as gross_wt
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE transfer_item_type=3 and bti.item_type = 2 and bti.is_non_tag = 1 and bt.transfer_from_branch=" . $id_branch . "
        GROUP BY bti.sold_bill_det_id) non_tag_brch on non_tag_brch.sold_bill_det_id = d.bill_det_id
        
        WHERE b.bill_status=1  and mt.id_metal=" . $id_metal . "
        " . ($id_branch != '' ?  " and b.id_branch=" . $id_branch . "" : '') . "
		and (date(b.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
		and b.is_eda=" . $bill_type . "");
		//print_r($this->db->last_query());exit;
		$result =  $sql->result_array();
		foreach ($result as $items) {
			if ($items['is_non_tag'] == 0 && $items['tag_status'] == 6 && $items['btrans_tag_id'] == '') {
				if (($items['sales_mode'] == 1) && ($items['trans_to_acc_stock'] == 0)) {
					$returnData[] = $items;
				} else if (($items['sales_mode'] == 2) && ($items['gross_wt'] > $items['transfered_wt'])) {
					$returnData[] = $items;
				}
			} else if ($items['is_non_tag'] == 0 && $items['det_item_type'] == 2 && ($items['tag_id'] == '' || $items['tag_id'] == NULL) && $items['transferred_to_acc_stock'] == 0) {
				// Home bill sales return items (item_type=2) — no tag record exists
				$returnData[] = $items;
			} else if ($items['is_non_tag'] == 1 && $items['non_tag_brch_det_id'] == '') {
				if ($items['transferred_to_acc_stock'] == 0) {
					$returnData[] = $items;
				}
			}
		}
		return $returnData;
	}

	function old_metal_bill_details($from_date, $to_date, $id_branch, $metal_type, $bill_type)
	{
		$sql = $this->db->query("SELECT s.old_metal_sale_id as trans_id,s.gross_wt as gross_wt,s.net_wt as net_wt,s.rate as amount,est.id_old_metal_type,
		t.metal_type,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.pur_ref_no as bill_no,b.bill_id,'0' as is_checked,'old_metal_items' as type,s.old_metal_sale_id,
		concat('old_metal_',LOWER(met.metal)) as item_type,'1' as transfer_items,'' as tag_id,'' as is_non_tag,
		FORMAT(IFNULL(bill_st.dia_wt,0),3) as dia_wt
		FROM ret_billing b 
		LEFT JOIN ret_bill_old_metal_sale_details s ON s.bill_id=b.bill_id
		LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
		LEFT JOIN ret_old_metal_type t ON t.id_metal_type=est.id_old_metal_type 
		LEFT JOIN metal as met ON met.id_metal = s.metal_type
		
		Left join (
        SELECT bti.old_metal_sale_id 
        FROM `ret_branch_transfer` bt 
        Left join ret_brch_transfer_old_metal bti on bti.transfer_id = bt.branch_transfer_id 
        WHERE (status = 1 or status = 2) and transfer_item_type=3 and bt.transfer_from_branch=" . $id_branch . "
        GROUP BY bti.old_metal_sale_id) btrans on btrans.old_metal_sale_id = s.old_metal_sale_id 
		LEFT JOIN (SELECT IFNULL(SUM(bill_st.wt),0) as dia_wt,bill_st.old_metal_sale_id
		FROM ret_billing_item_stones bill_st
		LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=bill_st.old_metal_sale_id
		LEFT JOIN ret_stone st ON st.stone_id = bill_st.stone_id
		LEFT JOIN ret_billing bill ON bill.bill_id = bill_st.bill_id
		WHERE bill.bill_status=1 and st.stone_type = 1	 
		" . ($id_branch != '' ?  " and s.current_branch=" . $id_branch . "" : '') . "
		" . ($metal_type != '' ?  " and s.metal_type=" . $metal_type . "" : '') . "
		and (date(bill.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') 
		and bill.is_eda=" . $bill_type . " and s.is_transferred=0
		GROUP BY bill_st.old_metal_sale_id) as bill_st ON bill_st.old_metal_sale_id = s.old_metal_sale_id
        
		WHERE b.bill_status=1 AND s.old_metal_sale_id IS NOT null AND  btrans.old_metal_sale_id iS NULL
		" . ($id_branch != '' ?  " and s.current_branch=" . $id_branch . "" : '') . "
		" . ($metal_type != '' ?  " and s.metal_type=" . $metal_type . "" : '') . "
		and (date(b.bill_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') 
		and b.is_eda=" . $bill_type . "
		 and s.is_transferred=0");
		return $sql->result_array();
	}


	function get_purchase_items_details($transCode, $s_type, $print_type)
	{
		$oldMetal = $this->db->query("SELECT IFNULL(SUM(s.gross_wt),0) as grs_wt,IFNULL(SUM(s.net_wt),0) as net_wt,IFNULL(SUM(s.rate),0) as amount,
                date_format(b.created_time,'%d-%m-%Y') as created_time,IFNULL((bill_st.dia_wt),0) as dia_wt,
                b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,
                concat('OLD  ',met.metal) as item_type,
                bill.bill_no,p.item_type as transfer_item,
                s.metal_type,met.metal as metal_name,ifnull(s.piece,0) as piece
                FROM ret_branch_transfer  b 
                LEFT JOIN ret_brch_transfer_old_metal p ON p.transfer_id=b.branch_transfer_id
                LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id=p.old_metal_sale_id 
                LEFT JOIN metal as met ON met.id_metal = s.metal_type 
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
                LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
                LEFT JOIN ret_billing bill ON bill.bill_id=s.bill_id
				LEFT JOIN (SELECT IFNULL(SUM(bill_st.wt),0) as dia_wt,bill_st.old_metal_sale_id
				FROM ret_billing_item_stones bill_st
				LEFT JOIN ret_bill_old_metal_sale_details s on s.old_metal_sale_id=bill_st.old_metal_sale_id 
				LEFT JOIN ret_stone st ON st.stone_id = bill_st.stone_id
				LEFT JOIN ret_billing bill ON bill.bill_id = bill_st.bill_id
				WHERE bill.bill_status=1 and st.stone_type = 1
				GROUP BY bill_st.old_metal_sale_id) as bill_st ON bill_st.old_metal_sale_id = s.old_metal_sale_id
                WHERE b.transfer_item_type=3 AND b.branch_trans_code=" . $transCode . "
                and p.item_type=1
                " . ($print_type == 2 ? " GROUP by s.old_metal_sale_id" : 'GROUP by s.metal_type') . "");
		$result['old_metal_details'] = $oldMetal->result_array();

		$salesReturn = $this->db->query("SELECT IFNULL(SUM(d.gross_wt),0) as grs_wt,IFNULL(SUM(d.net_wt),0) as net_wt,IFNULL(SUM(d.item_cost),0) as amount,
                date_format(b.created_time,'%d-%m-%Y') as created_time,IFNULL(bill_st.dia_wt,0) as dia_wt,
                b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,
                concat('SALES RETURN - ',met.metal) as item_type,
                p.item_type as transfer_item,bill.bill_no,cat.id_metal as metal_type,
                met.metal as metal_name,pro.product_name,tag.piece,cat.name
                FROM ret_branch_transfer b 
                LEFT JOIN ret_brch_transfer_old_metal p ON p.transfer_id=b.branch_transfer_id
                LEFT JOIN ret_bill_details d ON (IF(p.is_non_tag=1,d.bill_det_id,d.tag_id)) = IF(p.is_non_tag=1,p.sold_bill_det_id,p.tag_id)
				left join ret_taging tag on tag.tag_id=d.tag_id 
                LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
                LEFT JOIN ret_product_master pro ON pro.pro_id=d.product_id
                LEFT JOIN ret_category cat ON cat.id_ret_category=pro.cat_id 
                LEFT JOIN metal as met ON met.id_metal = cat.id_metal  
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
				LEFT JOIN (SELECT IFNULL(SUM(bill_st.wt),0) as dia_wt,d.bill_det_id
				FROM ret_billing_item_stones bill_st
				LEFT JOIN ret_bill_return_details r ON r.ret_bill_det_id= bill_st.bill_det_id
				LEFT JOIN ret_stone st ON st.stone_id = bill_st.stone_id
				LEFT JOIN ret_bill_details d ON d.bill_det_id = r.ret_bill_det_id
				LEFT JOIN ret_billing bill ON bill.bill_id = r.bill_id
				WHERE  bill.bill_status=1  and st.stone_type = 1
				GROUP BY d.bill_det_id) as bill_st ON bill_st.bill_det_id = p.sold_bill_det_id
                WHERE b.transfer_item_type=3 and d.status=2 and bill.bill_status=1 and p.item_type=2 AND b.branch_trans_code=" . $transCode . "
                GROUP by d.product_id");
		$result['sales_return_details'] = $salesReturn->result_array();

		$partlySale = $this->db->query("SELECT  date_format(b.created_time,'%d-%m-%Y') as created_time,
                b.is_other_issue,b.transfer_item_type,b.branch_trans_code,fb.name as from_branch,tb.name as to_branch,b.branch_transfer_id,IFNULL(SUM(tag.gross_wt),0) as tag_gwt,
                (IFNULL(SUM(s.gross_wt),0)) as grs_wt,(IFNULL(SUM(s.net_wt),0)) as net_wt,'0' as amount,
                met.metal as metal_name,IFNULL(stn.dia_wt,0) as tag_dia_wt,IFNULL((IFNULL(stn.dia_wt,0)-IFNULL(bill_st.sold_wt,0)),0) as dia_wt, 
                concat('PARTLY SALE - ',met.metal) as item_type,
                p.product_name
                FROM ret_branch_transfer b 
                LEFT JOIN ret_brch_transfer_old_metal s on s.transfer_id=b.branch_transfer_id
                LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch
                LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch
                LEFT JOIN ret_taging tag ON tag.tag_id=s.tag_id
                LEFT JOIN ret_bill_details d ON d.tag_id = s.tag_id AND s.sold_bill_det_id =d.bill_det_id
                LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
                LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
                LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id 
                LEFT JOIN metal as met ON met.id_metal = cat.id_metal  
				LEFT join(SELECT IFNULL(SUM(st.wt),0) as dia_wt,st.tag_id
				FROM ret_taging_stone st
				LEFT JOIN ret_stone s ON s.stone_id=st.stone_id
				LEFT JOIN ret_uom uom ON uom.uom_id=s.uom_id
				WHERE st.tag_id IS NOT NULL AND s.stone_type=1
				group by st.tag_id) as stn ON stn.tag_id = s.tag_id
				LEFT JOIN (SELECT IFNULL(SUM(bill_st.wt),0) as sold_wt,d.tag_id,d.bill_det_id
				FROM ret_billing_item_stones bill_st
				LEFT JOIN ret_stone st ON st.stone_id = bill_st.stone_id
				LEFT JOIN ret_bill_details d ON d.bill_det_id = bill_st.bill_det_id
				LEFT JOIN ret_billing bill ON bill.bill_id = d.bill_id
				WHERE  bill.bill_status=1 AND d.tag_id IS NOT NULL AND st.stone_type=1
				GROUP BY d.bill_det_id) as bill_st ON bill_st.bill_det_id = s.sold_bill_det_id
                WHERE b.branch_trans_code=" . $transCode . " AND s.item_type=3
                GROUP by d.product_id");
		//print_r($this->db->last_query());exit;
		$result['partly_sales_details'] = $partlySale->result_array();


		return $result;
	}




	function getBTOldMetalDetails($transfer_id)
	{
		$sql = $this->db->query("SELECT * FROM `ret_brch_transfer_old_metal` where transfer_id=" . $transfer_id . " and item_type=1");
		return $sql->result_array();
	}


	function get_salesreturn_items($transfer_id)
	{
		$sql = $this->db->query("SELECT * FROM `ret_brch_transfer_old_metal` where transfer_id=" . $transfer_id . " and item_type=2");
		return $sql->result_array();
	}

	function get_partlysale_items($transfer_id)
	{
		$sql = $this->db->query("SELECT * FROM `ret_brch_transfer_old_metal` where transfer_id=" . $transfer_id . " and item_type=3");
		return $sql->result_array();
	}




	function get_headoffice_branch()
	{
		$sql = $this->db->query("SELECT * FROM branch WHERE is_ho=1");
		if ($sql && $sql->num_rows() > 0) {
			return $sql->row()->id_branch;
		}
		return 0;
	}

	//Packaging Items
	function get_packaging_items($trans_id)
	{
		$sql = $this->db->query("SELECT m.id_other_inv_item,m.no_of_pcs FROM ret_branch_transfer_other_inventory m  WHERE m.branch_transfer_id=" . $trans_id . "");
		return $sql->result_array();
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

	function get_other_inventory_purchase_items_details($id_other_item, $id_branch, $issue_preference, $total_pcs)
	{
		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_purchase_items_details` 
        WHERE other_invnetory_item_id=" . $id_other_item . " AND current_branch=" . $id_branch . " AND status=0
        " . ($issue_preference == 1 ? 'order by pur_item_detail_id ASC' : 'order by pur_item_detail_id DESC') . "
        LIMIT " . $total_pcs . "");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	function get_other_inventory_download_pending_details($id_other_item, $id_branch, $issue_preference, $total_pcs)
	{
		$sql = $this->db->query("SELECT * FROM `ret_other_inventory_purchase_items_details` 
        WHERE other_invnetory_item_id=" . $id_other_item . " AND current_branch=" . $id_branch . " AND status=4
        " . ($issue_preference == 1 ? 'order by pur_item_detail_id ASC' : 'order by pur_item_detail_id DESC') . "
        LIMIT " . $total_pcs . "");
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}

	//Packaging Items

	//Repair Orders
	function getRepairOrderDetails($data)
	{
		$order_query = $this->db->query("SELECT d.id_customerorder,c.order_no,d.id_product,d.design_no,d.wast_percent,d.mc,d.stn_amt,d.net_wt as net_wt,IFNULL(IF(c.order_type=3,stn.wt,tag_stn.wt),0) as dia_wt,d.less_wt as less_wt,d.weight as gross_wt,d.totalitems,d.rate,d.id_purity,
		IFNULL(prod.hsn_code,'-') as hsn_code,prod.product_name,IFNULL(des.design_name,'') as design_name,p.purity as purname,
		m.tgrp_id as tax_group_id , tgrp_name, ifnull(cat.id_metal,'') as metal_type,prod.calculation_based_on,d.size,des.design_code,prod.gift_applicable,cat.id_ret_category,IFNULL(d.completed_weight,0) as completed_weight,IFNULL(d.rate,0) as amount,d.id_orderdetails,concat(cus.firstname,' ',cus.mobile) as cus_name,c.order_to,d.orderno,
		d.orderstatus,IFNULL(tag.tag_code,'') as tag_code,IFNULL(sec.section_name,'') as section_name
		FROM customerorder c
		LEFT JOIN customerorderdetails d on d.id_customerorder=c.id_customerorder
		LEFT JOIN ( SELECT id_other_details,uom_id,SUM(wt) as wt  FROM customer_order_stone_details WHere uom_id = 6 GROUP BY id_other_details ) as stn ON stn.id_other_details = d.id_orderdetails
		LEFT JOIN ret_product_master prod on prod.pro_id=d.id_product
		LEFT JOIN ret_design_master des on des.design_no=d.design_no
		LEFT JOIN ret_category cat on cat.id_ret_category=prod.cat_id
		LEFT JOIN metal m ON m.id_metal=cat.id_metal
		LEFT JOIN ret_purity p on p.id_purity=d.id_purity
		LEFT JOIN ret_taxgroupmaster as txgrp ON txgrp.tgrp_id = m.tgrp_id
		LEFT JOIN customer cus on cus.id_customer=c.order_to
		LEFT join ret_taging tag on tag.tag_id = d.tag_id
		LEFT JOIN ret_section sec ON sec.id_section = tag.id_section
		LEFT JOIN ( SELECT st.tag_id,uom_id,SUM(wt) as wt  FROM ret_taging_stone st WHere uom_id = 6 GROUP BY st.tag_id ) as tag_stn ON tag_stn.tag_id = d.tag_id
		where c.id_customerorder is not null and c.order_status!=6  and d.orderstatus!=6  and d.orderstatus<=4 and (c.order_type=3 OR c.order_type=4)
		" . ($data['order_no'] != '' ? " and c.order_no='" . $data['order_no'] . "'" : '') . " 
		" . ($data['from_brn'] != '' ? " and d.current_branch=" . $data['from_brn'] . "" : '') . "");
		// print_r($this->db->last_query());exit;
		return $order_query->result_array();
	}
	function getBTOrders($trans_id)
	{
		$sql = $this->db->query("SELECT * FROM ret_bt_order_log l 
			WHERE l.branch_transfer_id=" . $trans_id . " group by l.id_orderdetails");
		return $sql->result_array();
	}
	function get_repair_order_tag_details($id_orderdetails, $id_branch, $to_branch, $approval_type)
	{
		$sql = $this->db->query("select IFNULL(tag_id,'') as tag_id,IFNULL(id_section,'') as id_section 
		from ret_taging 
		where id_orderdetails='" . $id_orderdetails . "' 
		" . ($approval_type == 1 ? ' and tag_status = 0 and current_branch = ' . $id_branch . ' ' : ' and tag_status = 4 and current_branch = ' . $to_branch . ' ') . "
		");
		return $sql->row_array();
	}
	//Repair Orders


	function get_download_data($data)
	{
		if ($data['dt_range'] != '') {
			$tagDateRange = explode('-', $data['dt_range']);
			$td1 = date_create($tagDateRange[0]);
			$td2 = date_create($tagDateRange[1]);
			$tagFromDt = date_format($td1, "d-m-Y");
			$tagToDt = date_format($td2, "d-m-Y");
		}
		//echo "<pre>";print_r($this->session->all_userdata());exit;
		$result = array();
		$from_branch = "";
		$to_branch = "";
		if ($data['approval_type'] == 1) {
			$from_branch = ($data['from_branch'] != '' ? $data['from_branch'] : $this->session->userdata('id_branch'));
			$to_branch = ($data['to_branch'] != '' ? $data['to_branch'] :  '');
		} else {
			$from_branch = ($data['from_branch'] != '' ? $data['from_branch'] : '');
			$to_branch = ($data['to_branch'] != '' ? $data['to_branch'] :   $this->session->userdata('id_branch'));
		}
		$sql = $this->db->query("SELECT branch_trans_code,`branch_transfer_id`,fb.id_branch as fb_id_branch,tb.id_branch as tb_id_branch,fb.name as from_branch,tb.name as to_branch,l.`lot_no`,t.gross_wt,t.net_wt,t.piece,date_format(bt.created_time,'%d-%m-%Y') as created_time,tag_transfer_id,tag_code,is_other_issue,
		if(design_code = '' or design_code is null ,ifnull(design_name,'-') ,CONCAT(design_code,' - ',design_name) ) as design,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product,t.tag_id  ,t.product_id as id_prod,
		f_dc.entry_date as f_entry_date, t_dc.entry_date as t_entry_date, bt.pieces as actual_pieces,bt.grs_wt as actual_weights
				FROM `ret_branch_transfer` bt
					Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id
					Left join ret_taging t on t.tag_id = bti.tag_id 
					Left join ret_lot_inwards_detail ld on ld.id_lot_inward_detail = t.id_lot_inward_detail
					Left join ret_lot_inwards l on ld.lot_no = l.lot_no
					Left join ret_product_master p on p.pro_id = t.product_id			
					Left join ret_design_master d on d.design_no = t.design_id			
					Left join branch fb on fb.id_branch = bt.transfer_from_branch			
					Left join branch tb on tb.id_branch = bt.transfer_to_branch		
					Left join ret_day_closing f_dc on f_dc.id_branch = bt.transfer_from_branch			
					Left join ret_day_closing t_dc on t_dc.id_branch = bt.transfer_to_branch		
			 	WHERE transfer_item_type =1  
			 	" . ($from_branch != '' ? ' and transfer_from_branch=' . $from_branch : '') . " 
			 	" . ($data['branch_trans_code'] != '' ? ' and branch_trans_code=' . $data['branch_trans_code'] : '') . " 
			 	" . ($to_branch != '' ? ' and transfer_to_branch=' . $to_branch : '') . " 
			 	" . ($data['is_other_issue'] != '' ? ' and is_other_issue=' . $data['is_other_issue'] : 'and is_other_issue=0') . " 
			 	" . ($data['approval_type'] != '' ? ' and status=' . $data['approval_type'] : 'and status=1') . " 
			 	" . ($data['approval_type'] == 1 ? " AND t.tag_status = 0 AND t.current_branch  = " . $from_branch . "" : "AND t.tag_status = 4 AND t.current_branch  = " . $to_branch . " ") . " 
			 	group by bti.tag_id");
		//echo $this->db->last_query();exit;
		//Left join ret_lot_inwards_detail ld on ld.id_lot_inward_detail=t.id_lot_inward_detail 
		$btData = array();
		foreach ($sql->result_array() as $r) {
			$btData[$r['branch_transfer_id']][] = $r;
		}
		$i = 0;
		foreach ($btData as $btrans) {
			$j = 1;
			foreach ($btrans as $tag) {
				// Tag Detail 
				$tagData =  array(
					"design" 	=> $tag['design'],
					"product" 	=> $tag['product'],
					"gross_wt"  => $tag['gross_wt'],
					"net_wt" 	=> $tag['net_wt'],
					"piece" 	=> $tag['piece'],
					"id_prod" 	=> $tag['id_prod'],
					"tag_code"  => $tag['tag_code']
				);
				// Lot Data
				if (isset($result[$i])) {
					$prev = $result[$i];
					$result[$i]["gross_wt"] = $tag['gross_wt'] + $prev['gross_wt'];
					$result[$i]["net_wt"] 	= $tag['net_wt'] + $prev['net_wt'];
					$result[$i]["piece"] 	= $tag['piece'] + $prev['piece'];
				} else {
					$result[$i] = $tag;
					$result[$i]['no_of_prod'] = 0;
				}
				$id_prod = $result[$i]['id_prod'];
				// Product Detail
				if (isset($result[$i]['prod'][$id_prod])) {
					$prev_prod = $result[$i]['prod'][$id_prod];
					$result[$i]['prod'][$id_prod]["gross_wt"] = $tag['gross_wt'] + $prev_prod['gross_wt'];
					$result[$i]['prod'][$id_prod]["net_wt"]  = $tag['net_wt'] + $prev_prod['net_wt'];
					$result[$i]['prod'][$id_prod]["piece"] 	 = $tag['piece'] + $prev_prod['piece'];
					$result[$i]['prod'][$id_prod]["no_of_tags"] = $prev_prod['no_of_tags'] + 1;
					$result[$i]['prod'][$id_prod]['tags'][$j] = $tagData;
				} else {
					$result[$i]['prod'][$id_prod] = array(
						"gross_wt"   => $tag['gross_wt'],
						"net_wt" 	 => $tag['net_wt'],
						"piece" 	 => $tag['piece'],
						"product" 	 => $tag['product'],
						"id_prod" 	 => $tag['id_prod'],
						"no_of_tags" => 1,
					);
					$result[$i]['no_of_prod']++;  // Product Count
					$result[$i]['prod'][$id_prod]['tags'][$j] = $tagData; // Add tag data in array
				}

				$j++;
			}
			$i++;
		}
		//  echo "<pre>";print_r($result);
		return $result;
	}

	function fetchTagsByFilter_scan($data)
	{
		if ($data['tag_dt_rng'] != '') {
			$tagDateRange = explode('-', $data['tag_dt_rng']);
			$td1 = date_create($tagDateRange[0]);
			$td2 = date_create($tagDateRange[1]);
			$tagFromDt = date_format($td1, "d-m-Y");
			$tagToDt = date_format($td2, "d-m-Y");
		}

		if ($data['lot_dt_rng'] != '') {
			$lotDateRange = explode('-', $data['lot_dt_rng']);
			$ld1 = date_create($lotDateRange[0]);
			$ld2 = date_create($lotDateRange[1]);
			$lotFromDt = date_format($ld1, "d-m-Y");
			$lotToDt = date_format($ld2, "d-m-Y");
		}
		$sql = $this->db->query("SELECT tag_lot_id  as lot_no,t.tag_id,tag_code,
				t.gross_wt,t.net_wt,t.piece,date_format(t.tag_datetime,'%d-%m-%Y') as tag_datetime,
				if(design_code = '' or design_code is null ,ifnull(design_name,'-') ,CONCAT(design_code,' - ',design_name) ) as design,if( product_short_code = '' or product_short_code is null ,product_name ,CONCAT(product_short_code ,' - ',product_name) ) as product ,t.id_lot_inward_detail,c.id_metal
						FROM `ret_taging` t
							Left join ret_lot_inwards l on t.tag_lot_id=l.lot_no
							Left join (SELECT bti.tag_id FROM `ret_branch_transfer` bt Left join ret_brch_transfer_tag_items bti on bti.transfer_id = bt.branch_transfer_id WHERE (status = 1 or status = 2) and transfer_item_type=1 and bt.transfer_from_branch=" . $data['from_brn'] . " GROUP BY bti.tag_id) btrans on btrans.tag_id = t.tag_id
							Left join ret_product_master p on p.pro_id=t.product_id	
							LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
							Left join ret_design_master d on d.design_no=t.design_id			
						 WHERE t.tag_status=4 and t.current_branch=" . $data['from_brn'] . "
						" . ($data['lotno'] != '' ? ' and tag_lot_id=' . $data['lotno'] : '') . "
						" . ($data['design_id'] != '' ? ' and design_id=' . $data['design_id'] : '') . "
						" . ($data['tag_no'] != '' ? ' and t.tag_code="' . $data['tag_no'] . '"' : '') . "
						" . ($data['old_tag_no'] != '' ? ' and old_tag_id="' . $data['old_tag_no'] . '"' : '') . "
						" . ($data['id_karigar'] == '' ? ' ' : 'AND l.gold_smith =' . $data['id_karigar']) . "
						" . ($data['lot_dt_rng'] != '' ? ' and date(lot_date) BETWEEN "' . $lotFromDt . '" AND "' . $lotToDt . '"' : '') . "
						" . ($data['from_date'] != '' ? ' and date(tag_datetime) BETWEEN "' . $data['from_date'] . '" AND "' . $data['to_date'] . '"' : '') . "
						group by tag_id");


		// echo $this->db->last_query();exit;	
		//Left join ret_lot_inwards_detail ld on ld.lot_no=l.lot_no
		return $sql->result_array();
	}

	function getBTDetail($branch_transfer_id)
	{
		$sql = $this->db->query("SELECT b.pieces,IFNULL(bti.downd_pcs,0) as downd_pcs
            FROM ret_branch_transfer b 
            LEFT JOIN(SELECT SUM(tag.piece) as downd_pcs,bt.branch_transfer_id
            FROM ret_brch_transfer_tag_items t 
            LEFT JOIN ret_branch_transfer bt ON bt.branch_transfer_id = t.transfer_id
            LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
            where t.download_date IS NOT NULL
            GROUP BY t.transfer_id) as bti ON bti.branch_transfer_id = b.branch_transfer_id
            WHERE b.branch_transfer_id = " . $branch_transfer_id . " ");
		return $sql->row_array();
	}

	function get_scan_tag_status($branch_trans_code, $tag_code)
	{
		$sql = $this->db->query("SELECT t.tag_status,t.piece,IFNULL(bt.download_date,'') as download_date,t.tag_status,bt.transfer_id as trans_id,
		   t.tag_id,p.product_name,t.gross_wt,t.net_wt,IFNULL(t.less_wt,0) as less_wt,t.tag_code,IFNULL(t.id_section,'') as id_section
		   FROM ret_brch_transfer_tag_items bt
		   LEFT JOIN ret_branch_transfer b ON b.branch_transfer_id = bt.transfer_id
		   LEFT JOIN ret_taging t on t.tag_id=bt.tag_id
		   LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
		   where b.branch_trans_code='" . $branch_trans_code . "' and t.tag_code ='" . $tag_code . "'");
		return $sql->row_array();
	}
	function getNonTagReceiptedLots($data)
	{
		$sql = $this->db->query("SELECT nt.id_nontag_receipt,nt.nt_receipt_no,nt.grs_wt AS nt_grs_wt, nt.net_wt AS nt_net_wt,
	       bt.grs_wt AS bt_grs_wt, bt.net_wt AS bt_net_wt
			FROM ret_nontag_receipt nt 
			LEFT JOIN (
			SELECT SUM(grs_wt) AS grs_wt,SUM(net_wt) AS net_wt,status,id_nontag_receipt
			FROM ret_branch_transfer 
			WHERE status != 4 GROUP BY id_nontag_receipt ) bt on bt.id_nontag_receipt = nt.id_nontag_receipt 
			WHERE nt.id_branch = " . $data['id_branch'] . " AND
			nt.grs_wt != IFNULL(bt.grs_wt, 0) AND
			nt.net_wt != IFNULL(bt.net_wt, 0) ");
			// print_r($this->db->last_query());exit;	
		return $sql->result_array();
	}
	function fetchNonTaggedReceiptedItems($data)
	{
		$result = array();
		$type = 2; // 1 - Use Lot Table, 2 - Non Tag Table
		if ($this->session->userdata('branch_settings') == 1) {
			if ($this->isHeadOffice($data['id_branch']) == 1) {
				$type = 1;
			}
		} else {
			$type = 2;
		}


		$sql = ("SELECT CONCAT(design_code,' - ',design_name) as design_name,CONCAT(product_short_code ,' - ',product_name) as product_name,
			(nt.grs_wt - ifnull(bt.grs_wt,0) ) as gross_wt,(nt.net_wt - ifnull(bt.net_wt,0) ) as net_wt,(nt.pcs - ifnull(bt.pieces,0) ) as no_of_piece,'' as id_lot_inward_detail,IFNULL(nt.id_section,'') as id_section,IFNULL(rs.section_name,'') as section_name,
			nt.id_sub_design,subDes.sub_design_name,nt.id_product,nt.id_design,nt.id_sub_design,nt.id_nontag_receipt
			
			FROM  ret_nontag_receipt nt 
			Left join ret_product_master p on p.pro_id = nt.id_product
			Left join ret_design_master d on d.design_no = nt.id_design
			LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = nt.id_sub_design
			left join ret_section rs on rs.id_section = nt.id_section
			
			Left join (SELECT bt_nontag.id_nontag_receipt,sum(bt_nontag.grs_wt) as grs_wt,sum(bt_nontag.net_wt) as net_wt,sum(bt_nontag.pieces) as pieces,btrans.status FROM `ret_brch_transfer_non_tag_items` bt_nontag
			LEFT JOIN ret_branch_transfer btrans ON btrans.branch_transfer_id = bt_nontag.transfer_id
			WHERE btrans.status != 3  GROUP BY bt_nontag.id_nontag_receipt) bt on bt.id_nontag_receipt=nt.id_nontag_receipt and bt.status != 3 
			
			WHERE nt.id_branch=" . $data['id_branch'] . " 
			" . ($data['id_nontag_receipt'] != '' ? ' and nt.id_nontag_receipt=' . $data['id_nontag_receipt'] : '') . "
			group by nt.id_nontag_receipt ");
		// print_r($sql);exit;
		$res =  $this->db->query($sql)->result_array();
		foreach ($res as $r) {
			if ($r['gross_wt'] > 0) {
				$result[] = array(
					"design_name"           => ($r['design_name'] == NULL ? '-' : $r['design_name']),
					"section_name"          => $r['section_name'],
					"product_name"          => $r['product_name'],
					"sub_design_name"       => $r['sub_design_name'],
					"gross_wt"              => $r['gross_wt'],
					"net_wt"                => $r['net_wt'],
					"no_of_piece"           => $r['no_of_piece'],
					"id_nontag_receipt"     => $r['id_nontag_receipt'],
					"id_lot_inward_detail"  => $r['id_lot_inward_detail'],
					"id_sub_design"         => $r['id_sub_design'],
					"product"               => $r['product'],
					"design"                => $r['design'],
					"id_section"            => $r['id_section'],
					"id_nontag_item"        => $this->getNontagItemId(1, $r['id_section'], $r['id_product'], $r['id_design'], $r['id_sub_design']),
				);
			}
		}

		return $result;
	}
	function getNontagItemId($id_branch, $id_section, $id_product, $id_design, $id_sub_design)
	{
		$sql = $this->db->query("SELECT id_nontag_item 
			from ret_nontag_item
			where branch =" . $id_branch . " and id_section =" . $id_section . " 
			and product=" . $id_product . " and design=" . $id_design . " and id_sub_design=" . $id_sub_design . "");
		if ($sql && $sql->num_rows() > 0) {
			return $sql->row()->id_nontag_item;
		}
		return 0;
	}
}
