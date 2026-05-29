<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ret_services_model extends CI_Model
{
	
	function __construct()
    {
        parent::__construct();
    }
    
    public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table,$data);
		return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	}
	
	public function updateData($data,$id_field,$id_value,$table)
    {    
	    $edit_flag = 0;
	    $this->db->where($id_field,$id_value);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}
    
	function isDayClosed($id_branch){
		$res = $this->db->query("SELECT is_day_closed FROM `ret_day_closing` WHERE  id_branch=".$id_branch);
		return ($res->row('is_day_closed') == 0 ? FALSE : TRUE);
	}
	
	function getPartlySold($date,$id_branch){
		$res = $this->db->query("
					SELECT bill_no,bil.bill_id,bil.bill_type,bil.id_branch,
						bil_det.tag_id,bil_det.product_id,bil_det.design_id,
					    tag.less_wt as act_less_wt,tag.net_wt as act_net_wt,tag.gross_wt as act_gross_wt,
					    bil_det.less_wt as sold_less_wt,bil_det.net_wt as sold_net_wt,bil_det.gross_wt as sold_gross_wt
					FROM `ret_billing` bil
						LEFT JOIN ret_bill_details bil_det ON bil_det.bill_id=bil.bill_id 
					    LEFT JOIN ret_taging tag ON tag.tag_id=bil_det.tag_id
					WHERE bil.bill_type<=3  and bil_det.is_partial_sale = 1 and bil_det.item_type = 0 and tag.tag_status = 1 and bil.id_branch=".$id_branch." and bil.bill_date='".$date."'
					GROUP BY tag.tag_id
					");
		//echo $this->db->last_query();			exit;		
		return $res->result_array();
	}
	
	function activeBranches(){
	    $sql="Select b.id_branch, b.name as branch_name from branch b where active =1";
		$r=$this->db->query($sql);
		return $r->result_array();
	}
	
	function stock_balance($id_branch,$stock_date)
    {
    	$stock_detail = array(); 
    	$op_date = date('Y-m-d',(strtotime('-1 day')));
        $stock_date = date('Y-m-d');
        
        /*$op_date = '2022-06-12';
    	$stock_date = '2022-06-13';*/
    	
    	$op_date = date('Y-m-d', strtotime('-1 day', strtotime($stock_date)));
    	
		$data = $this->db->query("SELECT t.current_branch,t.product_id,p.product_name,b.name as branch_name,IFNULL(blc.gross_wt,0) as op_blc_gwt,IFNULL(blc.net_wt,0) as op_blc_nwt,IFNULL(blc.piece,0) as op_blc_pcs,
		IFNULL(INW.gross_wt,0) as inw_gwt,IFNULL(INW.net_wt,0) as inw_nwt,IFNULL(INW.piece,0) as inw_pcs,
		IFNULL(s.gross_wt,0) as sold_gwt,IFNULL(s.net_wt,0) as sold_nwt,IFNULL(s.piece,0) as sold_pcs,
		IFNULL(br_ot.gross_wt,0) as br_out_gwt,IFNULL(br_ot.net_wt,0) as br_out_nwt,IFNULL(br_ot.piece,0) as br_out_pcs,
		Date_Format(current_date(),'%d-%m-%Y') as date_add
		FROM ret_taging t
		LEFT JOIN ret_product_master p on p.pro_id=t.product_id
		LEFT JOIN branch b on b.id_branch=t.current_branch
		
		LEFT JOIN (SELECT s.id_product as product_id,s.closing_gwt as gross_wt,s.closing_nwt as net_wt,s.closing_pcs as piece,s.date
        FROM ret_stock_balance s
        LEFT JOIN ret_product_master p ON p.pro_id=s.id_product
        WHERE s.type=1 and s.id_product is NOT null AND date(s.date)='$op_date'
        ".($id_branch!='' ? " and s.id_branch=".$id_branch."" :'')."
        GROUP by s.id_product) blc on blc.product_id=t.product_id
        
		
		lEFT JOIN (SELECT tag.tag_id,tag.product_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece
		FROM ret_taging tag
		LEFT JOIN ret_taging_status_log l on l.tag_id=tag.tag_id and l.to_branch=".$id_branch." and l.status=0
		LEFT JOIN ret_product_master prod on prod.pro_id=tag.product_id
		WHERE  date(l.date) BETWEEN '$stock_date' AND '$stock_date' AND tag.product_id=prod.pro_id And l.status=0
		".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
		GROUP by tag.product_id) INW on INW.product_id=t.product_id
		
		LEFT JOIN (SELECT b.tag_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece,b.product_id
		FROM ret_taging tag
		LEFT JOIN ret_bill_details b on b.tag_id=tag.tag_id
		lEFT JOIN ret_billing bill on bill.bill_id=b.bill_id
		LEFT JOIN ret_product_master prod on prod.pro_id=b.product_id
		WHERE  bill.bill_status=1 and date(bill.bill_date) BETWEEN '$stock_date' AND '$stock_date'  AND b.product_id=prod.pro_id
		".($id_branch!='' ? " and bill.id_branch=".$id_branch." and tag.current_branch=".$id_branch."" :'')." 
		GROUP by b.product_id) s ON s.product_id=t.product_id
		
		LEFT JOIN (
		SELECT tag.tag_id,tag.product_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece
        FROM ret_taging tag
        LEFT JOIN ret_taging_status_log l on l.tag_id=tag.tag_id and l.from_branch=".$id_branch." and (l.status=2 or l.status=3  or l.status=5 or l.status=4 or l.status=7 or l.status=8 or l.status=9 or l.status=10)
        LEFT JOIN ret_product_master prod on prod.pro_id=tag.product_id
        WHERE (date(l.date) BETWEEN '$stock_date' AND '$stock_date') and (l.status=2 or l.status=3  or l.status=5 or l.status=4 or l.status=7 or l.status=8 or l.status=9 or l.status=10) 
        ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')."
        GROUP by tag.product_id
		) br_ot on br_ot.product_id=t.product_id
		
		where t.tag_id is not null 
		".($id_branch!='' && $id_branch>0 ? " and t.current_branch=".$id_branch."" :'')."
		GROUP by t.product_id"); 
		//echo "<pre>"; print_r($this->db->last_query());exit;
    	$stock_detail = $data->result_array();
    	return $stock_detail;
    }
    
    function stock_balance_nontag($id_branch,$stock_date)
    {
        $stock_detail = array(); 
    	/*$op_date = date('Y-m-d',(strtotime('-1 day')));
    	$stock_date = date('Y-m-d');*/
    	
    	$op_date = date('Y-m-d', strtotime('-1 day', strtotime($stock_date)));
    	
    	/*$op_date = '2022-05-22';
    	$stock_date = '2022-05-23';*/
    	
            $op_bal_sql = $this->db->query("
            SELECT 
            p.pro_id as product_id,p.product_name, b.name as branch_name,b.id_branch,
            IFNULL(stk.closing_gwt,0) as op_blc_gwt,IFNULL(stk.closing_nwt,0) as op_blc_nwt,IFNULL(stk.closing_pcs,0) as op_blc_pcs
            FROM ret_product_master p
            LEFT JOIN branch b on b.id_branch=".$id_branch."
            LEFT JOIN ret_stock_balance stk  on p.pro_id=stk.id_product  and  date(stk.date)='".$op_date."' ".($id_branch!='' && $id_branch>0 ? " and stk.id_branch=".$id_branch."" :'')."
            WHERE stock_type = 2
            
            GROUP by p.pro_id
            ");		    
			
            $inward = $this->db->query(" 
            SELECT
            INW.product,
            IFNULL(sum(INW.gross_wt),0) as inw_gwt,IFNULL(sum(INW.net_wt),0) as inw_nwt,IFNULL(sum(INW.no_of_piece),0) as inw_pcs
            FROM ret_nontag_item_log INW
            WHERE to_branch=".$id_branch." and INW.status=0 AND (date(INW.date) BETWEEN '".$stock_date."' AND '".$stock_date."')
            GROUP by INW.product
            ");
			
            $outward = $this->db->query("
            SELECT 
            ot.product,
            IFNULL(sum(ot.gross_wt),0) as out_gwt,IFNULL(sum(ot.net_wt),0) as out_nwt,IFNULL(sum(ot.no_of_piece),0) as out_pcs 
            FROM ret_nontag_item_log ot
            WHERE ot.from_branch=".$id_branch." and (ot.status=3 or ot.status=4 or ot.status=7) AND (date(ot.date) BETWEEN '".$stock_date."' AND '".$stock_date."')  
            GROUP by ot.product
            "); 
        			
            $salesqr=$this->db->query("SELECT IFNULL(SUM(d.gross_wt),0) as sales_gwt,IFNULL(SUM(d.net_wt),0) as sales_nwt,IFNULL(SUM(d.piece),0) as sales_pcs,
            d.product_id
            FROM ret_bill_details d 
            LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
            LEFT JOIN ret_billing b ON b.bill_id=d.bill_id
            WHERE b.bill_status=1 AND p.stock_type=2 AND d.is_non_tag = 1 and b.id_branch=".$id_branch." and date(b.bill_date) BETWEEN '$stock_date' AND '$stock_date' 
            group by d.product_id");
            
            //print_r($this->db->last_query());exit;
       
        
        foreach($op_bal_sql->result_array() as $op_bal){
            $stock_detail[$op_bal['product_id']] = $op_bal;
            $stock_detail[$op_bal['product_id']]['inw_gwt'] = 0;
            $stock_detail[$op_bal['product_id']]['inw_nwt'] = 0;
            $stock_detail[$op_bal['product_id']]['inw_pcs'] = 0;
            $stock_detail[$op_bal['product_id']]['out_gwt'] = 0;
            $stock_detail[$op_bal['product_id']]['out_nwt'] = 0;
            $stock_detail[$op_bal['product_id']]['out_pcs'] = 0;
            $stock_detail[$op_bal['product_id']]['sales_pcs'] = 0;
            $stock_detail[$op_bal['product_id']]['sales_gwt'] = 0;
            $stock_detail[$op_bal['product_id']]['sales_nwt'] = 0;
            foreach($inward->result_array() as $inw){
                if($op_bal['product_id'] == $inw['product']){
                    $stock_detail[$op_bal['product_id']]['inw_gwt'] = $inw['inw_gwt'];
                    $stock_detail[$op_bal['product_id']]['inw_nwt'] = $inw['inw_nwt'];
                    $stock_detail[$op_bal['product_id']]['inw_pcs'] = $inw['inw_pcs'];
                }
            } 
            foreach($outward->result_array() as $out){
                if($op_bal['product_id'] == $out['product']){ 
                    $stock_detail[$op_bal['product_id']]['out_gwt'] = $out['out_gwt'];
                    $stock_detail[$op_bal['product_id']]['out_nwt'] = $out['out_nwt'];
                    $stock_detail[$op_bal['product_id']]['out_pcs'] = $out['out_pcs'];
                }
            }
            
            foreach($salesqr->result_array() as $sales){
                if($op_bal['product_id'] == $sales['product_id']){ 
                    $stock_detail[$op_bal['product_id']]['sales_gwt'] = $sales['sales_gwt'];
                    $stock_detail[$op_bal['product_id']]['sales_nwt'] = $sales['sales_nwt'];
                    $stock_detail[$op_bal['product_id']]['sales_pcs'] = $sales['sales_pcs'];
                }
            }
            
        }
    	return $stock_detail;
    }
    
    function stock_balance_old($id_branch,$date)
    {
    	$stock_detail = array(); 
    	$data = $this->db->query("
                        	SELECT 
                            	t.current_branch,t.product_id, 
                            	SUM(IFNULL(t.gross_wt,0)) as op_blc_gwt,SUM(IFNULL(t.net_wt,0)) as op_blc_nwt,SUM(IFNULL(t.piece,0)) as op_blc_pcs,
                            	0 as inw_gwt,0 as inw_nwt,0 as inw_pcs,
                            	0 as sold_gwt,0 as sold_nwt,0 as sold_pcs,
                            	Date_Format(current_date(),'%d-%m-%Y') as date_add 
                            FROM ret_taging t 
                            WHERE tag_status=0 and t.current_branch=".$id_branch." and DATE_FORMAT(t.tag_datetime,'%Y-%m-%d')<='".$date."'
                            GROUP by t.product_id"); 
                            //print_r($this->db->last_query());exit;
    	$stock_detail = $data->result_array();
    	return $stock_detail;
    }
    
    function stock_balance_details($id_branch,$date)
    {
        $stock_detail = array(); 
    	$op_date = '2020-11-18';
        $stock_date ='2020-11-19';
    		
		$data = $this->db->query("SELECT t.current_branch,t.product_id,p.product_name,b.name as branch_name,IFNULL(blc.gross_wt,0) as op_blc_gwt,IFNULL(blc.net_wt,0) as op_blc_nwt,IFNULL(blc.piece,0) as op_blc_pcs,
		IFNULL(INW.gross_wt,0) as inw_gwt,IFNULL(INW.net_wt,0) as inw_nwt,IFNULL(INW.piece,0) as inw_pcs,
		IFNULL(s.gross_wt,0) as sold_gwt,IFNULL(s.net_wt,0) as sold_nwt,IFNULL(s.piece,0) as sold_pcs,
		IFNULL(br_ot.gross_wt,0) as br_out_gwt,IFNULL(br_ot.net_wt,0) as br_out_nwt,IFNULL(br_ot.piece,0) as br_out_pcs,
		Date_Format(current_date(),'%d-%m-%Y') as date_add
		FROM ret_taging t
		LEFT JOIN ret_product_master p on p.pro_id=t.product_id
		LEFT JOIN branch b on b.id_branch=t.current_branch
		
		LEFT JOIN (SELECT s.id_product as product_id,s.closing_gwt as gross_wt,s.closing_nwt as net_wt,s.closing_pcs as piece,s.date
        FROM ret_stock_balance_new s
        LEFT JOIN ret_product_master p ON p.pro_id=s.id_product
        WHERE s.type=1 and s.id_product is NOT null AND date(s.date)='$op_date'
        ".($id_branch!='' ? " and s.id_branch=".$id_branch."" :'')."
        GROUP by s.id_product) blc on blc.product_id=t.product_id
        
		
		lEFT JOIN (SELECT tag.tag_id,tag.product_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece
		FROM ret_taging tag
		LEFT JOIN ret_product_master prod on prod.pro_id=tag.product_id
		WHERE  date(tag.tag_datetime) BETWEEN '$stock_date' AND '$stock_date' AND tag.product_id=prod.pro_id
		".($id_branch!='' && $id_branch>0 ? " and tag.current_branch=".$id_branch."" :'')."
		GROUP by tag.product_id) INW on INW.product_id=t.product_id
		
		LEFT JOIN (SELECT b.tag_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece,b.product_id
		FROM ret_taging tag
		LEFT JOIN ret_bill_details b on b.tag_id=tag.tag_id
		lEFT JOIN ret_billing bill on bill.bill_id=b.bill_id
		LEFT JOIN ret_product_master prod on prod.pro_id=b.product_id
		WHERE  bill.bill_status=1 and date(bill.bill_date) BETWEEN '$stock_date' AND '$stock_date'  AND b.product_id=prod.pro_id
		".($id_branch!='' ? " and bill.id_branch=".$id_branch." and tag.current_branch=".$id_branch."" :'')." 
		GROUP by b.product_id) s ON s.product_id=t.product_id
		
		LEFT JOIN (
		SELECT tag.tag_id,tag.product_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece
        FROM ret_taging tag
        LEFT JOIN ret_taging_status_log l on l.tag_id=tag.tag_id and l.from_branch=".$id_branch." and (l.status=2 or l.status=3  or l.status=5 or l.status=4)
        LEFT JOIN ret_product_master prod on prod.pro_id=tag.product_id
        WHERE (date(l.date) BETWEEN '$stock_date' AND '$stock_date') and (l.status=2 or l.status=3  or l.status=5 or l.status=4) 
        ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')."
        GROUP by tag.product_id
		) br_ot on br_ot.product_id=t.product_id
		
		where t.tag_id is not null 
		".($id_branch!='' && $id_branch>0 ? " and t.current_branch=".$id_branch."" :'')."
		GROUP by t.product_id"); 
		//echo "<pre>"; print_r($this->db->last_query());exit;
    	$stock_detail = $data->result_array();
    	return $stock_detail;
    }
    
    //partly sale stock balance
    function partly_sale_stock_balance($id_branch,$date)
    {
        $op_date = date('Y-m-d',(strtotime('-1 day')));
        $stock_date = date('Y-m-d');
        
        /*$op_date = '2022-04-02';
        $stock_date = '2022-04-03';*/
        
         $sql=$this->db->query("SELECT p.product_name,p.pro_id,
            
                    IFNULL(op_blc.closing_gwt,0) as op_blc_gwt,IFNULL(op_blc.closing_nwt,0) as op_blc_nwt,IFNULL(op_blc.closing_pcs,0) as op_blc_pcs,
                    
                    IFNULL(inw.inw_pcs,0) as inw_pcs,IFNULL(inw.inw_gwt,0) as inw_gwt,IFNULL(inw.inw_nwt,0) as inw_nwt,
                    
                    IFNULL(out_ward.br_out_pcs,0) as br_out_pcs,IFNULL(out_ward.br_out_gwt,0) as br_out_gwt,IFNULL(out_ward.br_out_nwt,0) as br_out_nwt
                    
                    FROM ret_product_master p 
                    
                    LEFT JOIN(SELECT IFNULL(SUM(m.closing_gwt),0) as closing_gwt,IFNULL(SUM(m.closing_pcs),0) as closing_pcs,IFNULL(SUM(m.closing_nwt),0) as closing_nwt,p.pro_id
                    FROM ret_purchase_item_stock m 
                    LEFT JOIN ret_product_master p ON p.pro_id=m.id_product
                    where (date(m.date) BETWEEN '".date('Y-m-d',strtotime($op_date))."' AND '".date('Y-m-d',strtotime($op_date))."') and (m.stock_type=3)
                    ".($id_branch!='' && $id_branch>0 ? " and m.id_branch=".$id_branch."" :'')."
                    GROUP by p.pro_id) as op_blc ON op_blc.pro_id=p.pro_id
                    
                    LEFT JOIN(
                    SELECT IFNULL(SUM(l.gross_wt),0) as inw_gwt,'0' as inw_nwt,p.pro_id,'0' as inw_pcs
                    FROM ret_purchase_items_log l 
                    LEFT JOIN ret_bill_details d ON d.bill_det_id=l.sold_bill_det_id
                    LEFT JOIN ret_partlysold tag ON tag.sold_bill_det_id=d.bill_det_id
                    LEFT JOIN ret_billing b ON b.bill_id=d.bill_id
                    LEFT JOIN ret_taging t ON t.tag_id=d.tag_id
                    LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
                    WHERE d.is_partial_sale=1 and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."') 
                    AND b.bill_status=1 and l.item_type=3 and (l.status=1)
                    ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
                    GROUP by p.pro_id
                    ) AS inw ON inw.pro_id=p.pro_id
                                    
                    LEFT JOIN(SELECT SUM(l.gross_wt) as  br_out_gwt,'0' as br_out_pcs,'0' as br_out_nwt,p.pro_id
                    FROM ret_purchase_items_log l
                    LEFT JOIN ret_bill_details d ON d.tag_id=l.tag_id
                    LEFT JOIN ret_billing b ON b.bill_id=d.bill_id
                    LEFT JOIN ret_taging t ON t.tag_id=l.tag_id
                    LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
                    WHERE l.item_type=3 and (l.status=2 or l.status=3) and b.bill_status=1 and d.item_type=0
                    and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                   ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')."
                    GROUP by p.pro_id) as out_ward ON out_ward.pro_id=p.pro_id

                    WHERE p.pro_id is NOT NULL order by p.pro_id ASC");
                    
                     //print_r($this->db->last_query());exit;
                    $result=$sql->result_array();
                    return $result;
        
    }
    //partly sale stock balance
    
    
    //Bullion Purchase Stock Balance
    function bullion_purchase_stock_balance($id_branch,$date)
    {
        $op_date = date('Y-m-d',(strtotime('-1 day')));
        $stock_date = date('Y-m-d');
        
        /*$op_date = '2022-04-03';
        $stock_date = '2022-04-04';*/
        
         $sql=$this->db->query("SELECT p.product_name,p.pro_id,
            
                    IFNULL(op_blc.closing_gwt,0) as op_blc_gwt,IFNULL(op_blc.closing_nwt,0) as op_blc_nwt,IFNULL(op_blc.closing_pcs,0) as op_blc_pcs,
                    
                    IFNULL(inw.inw_pcs,0) as inw_pcs,IFNULL(inw.inw_gwt,0) as inw_gwt,IFNULL(inw.inw_nwt,0) as inw_nwt,
                    
                    outward.out_pcs as br_out_pcs,IFNULL(outward.out_gwt,0) as br_out_gwt,IFNULL(outward.out_nwt,0) as br_out_nwt
                    
                    FROM ret_product_master p
                    LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id
                    
                    LEFT JOIN(SELECT IFNULL(SUM(m.closing_gwt),0) as closing_gwt,IFNULL(SUM(m.closing_pcs),0) as closing_pcs,IFNULL(SUM(m.closing_nwt),0) as closing_nwt,p.pro_id
                    FROM ret_purchase_item_stock m 
                    LEFT JOIN ret_product_master p ON p.pro_id=m.id_product
                    where (date(m.date) BETWEEN '".date('Y-m-d',strtotime($op_date))."' AND '".date('Y-m-d',strtotime($op_date))."') and (m.stock_type=1)
                    ".($id_branch!='' && $id_branch>0 ? " and m.id_branch=".$id_branch."" :'')."
                    GROUP by p.pro_id) as op_blc ON op_blc.pro_id=p.pro_id
                    
                    LEFT JOIN(
                    SELECT '0' as inw_pcs,IFNULL(SUM(l.gross_wt),0) as inw_gwt,IFNULL(SUM(l.net_wt),0) as inw_nwt,l.id_product as pro_id
                    FROM ret_purchase_items_log l 
                    LEFT JOIN ret_product_master p ON p.pro_id=l.id_product
                    WHERE l.item_type=6 and l.status=1
                    ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
                    and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."') 
                    GROUP by p.pro_id
                    ) AS inw ON inw.pro_id=p.pro_id
                    
                    LEFT JOIN(
                    SELECT '0' as out_pcs,IFNULL(SUM(l.gross_wt),0) as out_gwt,IFNULL(SUM(l.net_wt),0) as out_nwt,l.id_product as pro_id
                    FROM ret_purchase_items_log l 
                    LEFT JOIN ret_product_master p ON p.pro_id=l.id_product
                    WHERE l.item_type=6 and l.status=3
                    ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')."
                    and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."') 
                    GROUP by p.pro_id
                    ) AS outward ON outward.pro_id=p.pro_id
                                    
                    WHERE p.pro_id is NOT NULL AND (c.cat_type=2 OR c.cat_type=3 OR c.cat_type=4) order by p.pro_id ASC");
                    
                    //print_r($this->db->last_query());exit;
                    $result=$sql->result_array();
                    return $result;
        
    }
    //Bullion Purchase Stock Balance
    
    
       
    //old metal stock balance
    function old_metal_stock_balance($id_branch,$date)
    {
        $op_date = date('Y-m-d',(strtotime('-1 day')));
        $stock_date = date('Y-m-d');
        
        /*$op_date = '2022-04-02';
        $stock_date = '2022-04-03';*/
        
         $sql=$this->db->query("SELECT t.id_old_metal_cat as id_metal_type,t.old_metal_cat as metal_type,
                    (IFNULL(inw.inw_gwt,0)+IFNULL(adv_inw.inw_gwt,0)) as inw_gwt,(IFNULL(inw.inw_nwt,0)+IFNULL(adv_inw.inw_nwt,0)) as inw_nwt,
                    IFNULL(op_blc.closing_nwt,0) as op_blc_nwt,IFNULL(op_blc.closing_gwt,0) as op_blc_gwt,
                    IFNULL(br_out.br_out_gwt,0) as br_out_gwt,IFNULL(br_out.br_out_nwt,0) as br_out_nwt
                    FROM ret_old_metal_category t
                    
                    LEFT JOIN(SELECT IFNULL((m.closing_gwt),0) as closing_gwt,IFNULL((m.closing_nwt),0) as closing_nwt,m.id_old_metal_type
                    FROM ret_purchase_item_stock m 
                    where (date(m.date) BETWEEN '".date('Y-m-d',strtotime($op_date))."' AND '".date('Y-m-d',strtotime($op_date))."') and m.stock_type=0
                     ".($id_branch!='' && $id_branch>0 ? " and m.id_branch=".$id_branch."" :'')."
                    GROUP by m.id_old_metal_type) as op_blc ON op_blc.id_old_metal_type=t.id_old_metal_cat
                    
                    LEFT JOIN(SELECT IFNULL(SUM(s.gross_wt),0) as inw_gwt,IFNULL(SUM(s.net_wt),0) as inw_nwt,e.id_old_metal_category
                    FROM ret_purchase_items_log l
                    LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=l.old_metal_sale_id
                    LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
                    LEFT JOIN ret_billing b ON b.bill_id=s.bill_id
                    WHERE b.bill_status=1 and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                    ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
                    GROUP by e.id_old_metal_category) as inw ON inw.id_old_metal_category=t.id_old_metal_cat
                    
                    LEFT JOIN (SELECT IFNULL(SUM(s.gross_wt),0) as inw_gwt,IFNULL(SUM(s.net_wt),0) as inw_nwt,s.id_old_metal_category
                    FROM ret_purchase_items_log l 
                    LEFT JOIN ret_issue_receipt i ON i.id_issue_receipt=l.id_issue_receipt
                    LEFT JOIN ret_adv_receipt_weight w ON w.id_issue_receipt=i.id_issue_receipt
                    LEFT JOIN ret_estimation_old_metal_sale_details s ON s.old_metal_sale_id=w.est_old_metal_sale_id
                    WHERE s.purchase_status=3 AND i.bill_status=1 and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                    ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
                    GROUP by s.id_old_metal_category) as adv_inw ON adv_inw.id_old_metal_category=t.id_old_metal_cat
                    
                    LEFT JOIN(SELECT IFNULL(SUM(s.gross_wt),0) as br_out_gwt,IFNULL(SUM(s.net_wt),0) as br_out_nwt,e.id_old_metal_category
                    FROM ret_purchase_items_log l
                    LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=l.old_metal_sale_id
                    LEFT JOIN ret_estimation_old_metal_sale_details e ON e.old_metal_sale_id=s.esti_old_metal_sale_id
                    LEFT JOIN ret_billing b ON b.bill_id=s.bill_id
                    WHERE b.bill_status=1 and (l.status=2 or l.status=3) and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                    ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')."
                    GROUP by e.id_old_metal_category) as br_out ON br_out.id_old_metal_category=t.id_old_metal_cat

                    GROUP by t.id_old_metal_cat");
                    //print_r($this->db->last_query());exit;
                    $result=$sql->result_array();
                    
                    return $result;
        
    }
    //old metal stock balance
    
    
    //old metal process stock balance
    function old_metal_process_stock_balance($id_branch,$date)
    {
        $op_date = date('Y-m-d',(strtotime('-1 day')));
        $stock_date = date('Y-m-d');
        
        /*$op_date = '2022-03-10';
        $stock_date = '2022-03-11';*/
        
         $sql=$this->db->query("SELECT c.id_ret_category,c.name as category_name,
                    IFNULL(op_blc.closing_gwt,0) as op_blc_gwt,IFNULL(op_blc.closing_nwt,0) as op_blc_nwt,
                    (IFNULL(process_inw.inw_nwt,0)+IFNULL(testing_process_inw.inw_nwt,0)+IFNULL(refining_process_inw.inw_nwt,0)) as inw_nwt,
                    (IFNULL(process_out.process_out_wt,0)+IFNULL(refining_out.process_out_wt,0)) as out_ward_nwt
                    
                    FROM ret_category c 
                    
                    LEFT JOIN(SELECT IFNULL(SUM(m.closing_gwt),0) as closing_gwt,IFNULL(SUM(m.closing_nwt),0) as closing_nwt,m.id_ret_category
                    FROM ret_purchase_item_stock m 
                    where (date(m.date) BETWEEN '".date('Y-m-d',strtotime($op_date))."' AND '".date('Y-m-d',strtotime($op_date))."') 
                    and m.stock_type=4
                    ".($id_branch!='' && $id_branch>0 ? " and m.id_branch=".$id_branch."" :'')."
                    ) as op_blc ON op_blc.id_ret_category=c.id_ret_category
                   
                    
                    LEFT JOIN (SELECT IFNULL((d.received_wt),0) as inw_nwt,d.received_category as id_category
                    FROM ret_purchase_items_log l 
                    LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=l.id_old_metal_process
                    LEFT JOIN ret_old_metal_melting m ON m.id_old_metal_process=p.id_old_metal_process
                    LEFT JOIN ret_old_metal_melting_recd_details d on d.id_melting=m.id_melting
                    WHERE l.id_old_metal_process IS NOT NULL AND l.item_type=4 and d.received_category IS NOT NULL
                    and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                    ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
                    GROUP by d.received_category) as process_inw ON process_inw.id_category=c.id_ret_category
                    
                    LEFT JOIN (SELECT IFNULL(SUM(t.received_wt),0) as inw_nwt,d.received_category as id_category
                    FROM ret_purchase_items_log l 
                    LEFT JOIN ret_old_metal_testing t ON t.id_old_metal_process_receipt=l.id_old_metal_process
                    LEFT JOIN ret_old_metal_melting_recd_details d on d.id_melting_recd=t.id_melting_recd
                    LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=l.id_old_metal_process
                    WHERE l.id_old_metal_process IS NOT NULL AND l.item_type=4
                    and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                    ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
                    AND l.status=1 AND p.process_for=2
                    GROUP by d.received_category) as testing_process_inw ON testing_process_inw.id_category=c.id_ret_category
                    
                    
                    LEFT JOIN(SELECT IFNULL(SUM(d.received_wt),0) as inw_nwt,d.received_category as id_category
                    FROM ret_purchase_items_log l 
                    LEFT JOIN ret_old_metal_refining ref ON ref.id_old_metal_process_receipt=l.id_old_metal_process
                    LEFT JOIN ret_old_metal_refining_details d ON d.id_metal_refining=ref.id_metal_refining
                    WHERE l.id_old_metal_process IS NOT NULL AND l.item_type=4 and d.received_category IS NOT NULL
                    and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                    ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
                    GROUP by d.received_category) as refining_process_inw on refining_process_inw.id_category=c.id_ret_category
                    
                    
                    LEFT JOIN (SELECT p.id_old_metal_process,IFNULL(SUM(t.net_wt),0) as process_out_wt,d.received_category as id_category
                    FROM ret_purchase_items_log l
                    LEFT JOIN ret_old_metal_testing t ON t.id_old_metal_process=l.id_old_metal_process
                    LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=t.id_old_metal_process 
                    LEFT JOIN ret_old_metal_melting_recd_details d on d.id_melting_recd=t.id_melting_recd
                    
                    WHERE l.item_type=5 AND p.process_for=1 and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                    ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')."
                    GROUP by d.received_category) as process_out ON process_out.id_category=c.id_ret_category
                    
                    LEFT JOIN (SELECT p.id_old_metal_process,IFNULL(SUM(t.received_wt),0) as process_out_wt,d.received_category as id_category
                    FROM ret_purchase_items_log l
                    LEFT JOIN ret_old_metal_refining r ON r.id_old_metal_process=l.id_old_metal_process
                    LEFT JOIN ret_old_metal_testing t ON t.id_metal_testing=r.id_metal_testing
                    LEFT JOIN ret_old_metal_melting_recd_details d on d.id_melting_recd=t.id_melting_recd
                    LEFT JOIN ret_old_metal_melting m ON m.id_melting=d.id_melting
                    LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=l.id_old_metal_process
                    WHERE l.item_type=5 AND d.melting_status=4 AND p.process_for=1 and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
                    ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')."
                    GROUP by d.received_category) as refining_out ON refining_out.id_category=c.id_ret_category
                    ");
                    //print_r($this->db->last_query());exit;
                    $result=$sql->result_array();
                    
                    return $result;
        
    }
    //old metal process stock balance
    
    
    //Sales Return stock balance
    
    function sales_return_stock_balance($id_branch,$date)
    {
        $op_date = date('Y-m-d',(strtotime('-1 day')));
        $stock_date = date('Y-m-d');
        
         $sql=$this->db->query("SELECT p.product_name,p.pro_id,
            
        IFNULL(op_blc.closing_gwt,0) as op_blc_gwt,IFNULL(op_blc.closing_nwt,0) as op_blc_nwt,IFNULL(op_blc.closing_pcs,0) as op_blc_pcs,
        
        IFNULL(inw.inw_pcs,0) as inw_pcs,IFNULL(inw.inw_gwt,0) as inw_gwt,IFNULL(inw.inw_nwt,0) as inw_nwt,
        
        IFNULL(out_ward.br_out_pcs,0) as br_out_pcs,IFNULL(out_ward.br_out_gwt,0) as br_out_gwt,IFNULL(out_ward.br_out_nwt,0) as br_out_nwt
        
        FROM ret_product_master p 
        
        LEFT JOIN(SELECT IFNULL(SUM(m.closing_gwt),0) as closing_gwt,IFNULL(SUM(m.closing_pcs),0) as closing_pcs,IFNULL(SUM(m.closing_nwt),0) as closing_nwt,p.pro_id
        FROM ret_purchase_item_stock m 
        LEFT JOIN ret_product_master p ON p.pro_id=m.id_product
        where (date(m.date) BETWEEN '".date('Y-m-d',strtotime($op_date))."' AND '".date('Y-m-d',strtotime($op_date))."') and (m.stock_type=2)
        ".($id_branch!='' && $id_branch>0 ? " and m.id_branch=".$id_branch."" :'')."
        GROUP by p.pro_id) as op_blc ON op_blc.pro_id=p.pro_id
        
        LEFT JOIN(SELECT IFNULL(SUM(tag.piece),0) as inw_pcs,IFNULL(SUM(tag.gross_wt),0) as inw_gwt,IFNULL(SUM(tag.net_wt),0) as inw_nwt,p.pro_id
        FROM ret_purchase_items_log l 
        LEFT JOIN ret_taging tag ON tag.tag_id=l.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id=tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id
        LEFT JOIN ret_bill_details d on d.tag_id=tag.tag_id
        LEFT JOIN ret_billing b on b.bill_id=l.bill_id
        WHERE tag.tag_status=6  and (l.item_type=2) and b.bill_status=1
        and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
        ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')."
        GROUP by p.pro_id) AS inw ON inw.pro_id=p.pro_id
        
        LEFT JOIN(SELECT IFNULL(SUM(tag.piece),0) as br_out_pcs,IFNULL(SUM(tag.gross_wt),0) as br_out_gwt,IFNULL(SUM(tag.net_wt),0) as br_out_nwt,p.pro_id
        FROM ret_purchase_items_log l 
        LEFT JOIN ret_taging tag ON tag.tag_id=l.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id=tag.product_id
        LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id
        WHERE (l.item_type=2) and (l.status=2 or l.status=3)
        and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($stock_date))."' AND '".date('Y-m-d',strtotime($stock_date))."')
        ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')."
        GROUP by p.pro_id) AS out_ward ON out_ward.pro_id=p.pro_id
                        
        WHERE p.pro_id is NOT NULL order by p.pro_id ASC");
        //print_r($this->db->last_query());exit;
        
        return $sql->result_array();
    }
    
    //Sales Return stock balance
    
    
   function get_service_by_code($serv_code)
   {
   	 $this->db->select('id_services,serv_name,serv_email,serv_sms,serv_whatsapp,dlt_te_id');
   	 $this->db->where('serv_code',$serv_code);
   	 $service=$this->db->get('services');	 
   	 return $service->row_array();
   }
   
  
    function Get_service_code_sms($service_code)
    {
        //Declaration of variables
        $message ="";
        $sms_msg = "";
        $sms_footer = "";
        $customer_data = array();
        if($service_code=='KAR_REM')
        {
            $resultset = $this->db->query("SELECT c.order_no,c.id_customerorder,c.order_no,k.contactno1,cmp.company_name as cmp_name,date_format(j.assigndate,'%d-%m-%Y') as order_date,date_format(d.smith_due_date,'%d-%m-%Y') as delivery_date
            FROM customerorder c
            LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder
            LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
            LEFT JOIN ret_karigar k ON k.id_karigar=j.id_vendor
            JOIN company cmp
            WHERE d.orderstatus=3 AND d.smith_due_date=CURRENT_DATE()
            group by c.id_customerorder");
            //print_r($this->db->last_query());exit;
        }
        
       
        foreach($resultset->result() as $row)
        {
             if($attachement_url!='')
            {
                $row->attachement_url = $attachement_url;
            }
            $customer_data = $row;
            $mobile=$row->mobile;
        }
        $resultset = $this->db->query("SELECT serv_sms,serv_email,sms_msg, sms_footer from services where serv_code = '".$service_code."'");
        foreach($resultset->result() as $row)
        {
            $serv_sms = $row->serv_sms;
            $serv_email = $row->serv_email;
            $sms_msg = $row->sms_msg;
            $sms_footer = $row->sms_footer;
        }
        $resultset->free_result();
        //Generating Message content
        $field_name = explode('@@', $sms_msg);
        for($i=1; $i < count($field_name); $i+=2) 
        {
            $field =  $field_name[$i];
            if(isset($customer_data->$field))
            { 
                $content = strtolower($customer_data->$field);
                $content = ucwords($content);
                $sms_msg = str_replace("@@".$field."@@",$content,$sms_msg);
            }	
        }
        $field_name_footer = explode('@@', $sms_footer);	
        for($i=1; $i < count($field_name_footer); $i+=2)
        {
            if(isset($customer_data->$field_name_footer[$i]))
            { 
                $sms_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data->$field_name_footer[$i],$sms_footer);					
            }	
        }
        $sms_msg .= " ".$sms_footer;
        return (array('message'=>$sms_msg,'mobile'=>$mobile,'serv_sms'=>$serv_sms,'serv_email'=>$serv_email));
    }
    
    function send_whatsApp_message($mobile,$message) 
    {
    	$whatsappdata = array("phone" => "91".$mobile, "body" => $message);
    	//print_r($whatsappdata);exit;
    	$whatsappurl = $this->config->item("whatsappurl");
    
    	$curl = curl_init();
    	curl_setopt_array($curl, array(
    	 CURLOPT_URL => $this->config->item('whatsappurl'),
    	 CURLOPT_RETURNTRANSFER => true,
    	 CURLOPT_ENCODING => "",
    	 CURLOPT_MAXREDIRS => 10,
    	 CURLOPT_TIMEOUT => 30,
    	 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    	 CURLOPT_CUSTOMREQUEST => "POST",
    	 CURLOPT_POSTFIELDS => json_encode($whatsappdata),
    	 CURLOPT_SSL_VERIFYPEER => FALSE,
    	 CURLOPT_HTTPHEADER => array(
    	"authorization: Basic cHJlY2lzZXRyYTpIaXJoTmwxMA==",
    	"cache-control: no-cache",
    	"content-type: application/json"
    	 ),
    	));
    
    	$response = curl_exec($curl);
    	$err = curl_error($curl);
    	curl_close($curl);
    	if($err){
    		return false;
    	}else{
    		$res = json_decode($response);
    		return $res->message;
    	}  
    }
    
    function get_required_otp_approval()
	{
		$return_data = array("otp_required" => 0);
		$result = $this->db->query("SELECT id_ret_settings, value from ret_settings where name = 'is_otp_required_for_approval'");
		if($result->num_rows() > 0){
			$return_data = array("otp_required" => $result->row('value'));
		}
		return $return_data;
	}
	
	
	
	//Packaging Item Stock Balance
	function stock_balance_packaging_items($id_branch,$date)
    {
    	$stock_detail = array(); 
    	$op_date = date('Y-m-d',(strtotime('-1 day')));
        $stock_date = date('Y-m-d');
        
        /*$op_date = '2021-10-09';
        $stock_date = '2021-10-10';*/
    		
		$sql = $this->db->query("SELECT i.id_other_item,i.name,
		IFNULL(blc.piece,0) as op_blc_pcs,IFNULL(blc.closing_amt,0) as op_blc_amt,
		IFNULL(inw.inw_pcs,0) as inw_pcs,IFNULL(inw.inw_amount,0) as inw_amount,
		IFNULL(br_out.out_pcs,0) as out_pcs,IFNULL(br_out.out_amount,0) as out_amount
        FROM ret_other_inventory_item i
        
        LEFT JOIN (SELECT s.id_other_item as id_other_item,s.closing_pcs as piece,s.date,s.closing_amt
        FROM ret_other_inventory_stock s
        WHERE s.id_other_item is NOT null AND date(s.date)='$op_date'
        ".($id_branch!='' ? " and s.id_branch=".$id_branch."" :'')."
        GROUP by s.id_other_item) blc on blc.id_other_item=i.id_other_item
        
		LEFT JOIN (
		SELECT l.item_id,IFNULL(SUM(l.no_of_pieces),0) as inw_pcs,IFNULL(SUM(l.amount),0) as inw_amount
        FROM ret_other_inventory_purchase_items_log l 
		WHERE (date(l.date) BETWEEN '".date('Y-m-d',strtotime($date))."' AND '".date('Y-m-d',strtotime($date))."') AND l.status=0
		".($id_branch!='' ? " and l.to_branch=".$id_branch."" :'')."
		GROUP by l.item_id) inw ON inw.item_id=i.id_other_item
		
		LEFT JOIN (
		SELECT l.item_id,IFNULL(SUM(l.no_of_pieces),0) as out_pcs,IFNULL(SUM(l.amount),0) as out_amount
        FROM ret_other_inventory_purchase_items_log l 
		WHERE (date(l.date) BETWEEN '".date('Y-m-d',strtotime($date))."' AND '".date('Y-m-d',strtotime($date))."') AND (l.status=1 or l.status=4 or l.status=3)
		".($id_branch!='' ? " and l.from_branch=".$id_branch."" :'')."
		GROUP by l.item_id) br_out ON br_out.item_id=i.id_other_item
		
		where i.id_other_item is not null  
		".($data['id_other_item']!='' ? " and i.id_other_item=".$data['id_other_item']."" :'')." 
		GROUP by i.id_other_item");
		
	    //print_r($this->db->last_query());exit;
    	$result = $sql->result_array();
    	
    	return $result;
    }
	//Packaging Item Stock Balance
   
	
}
?>