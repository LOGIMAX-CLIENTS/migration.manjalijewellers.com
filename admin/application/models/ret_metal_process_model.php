<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ret_metal_process_model extends CI_Model
{
	
	function __construct()
    {
        parent::__construct();
    }

        // General Functions

        public function insertData($data,$table)

        {
    
            $insert_flag = 0;
    
            $insert_flag = $this->db->insert($table, $data);
    
            return ($insert_flag == 1 ? $this->db->insert_id(): 0);
    
        }
    
        public function insertBatchData($data,$table)
    
        {
    
            $insert_flag = 0;
    
            $insert_flag = $this->db->insert_batch($table, $data);
    
            if ($this->db->affected_rows() > 0){
    
                return TRUE;
    
            }else{
    
                return FALSE;
    
            }
    
        }
    
        public function updateData($data, $id_field, $id_value, $table)
    
        {    
    
            $edit_flag = 0;
    
            $this->db->where($id_field, $id_value);
    
            $edit_flag = $this->db->update($table,$data);
    
            return ($edit_flag==1?$id_value:0);
    
        }	 
    
        public function deleteData($id_field,$id_value,$table)
    
        {
    
            $this->db->where($id_field, $id_value);
    
            $status= $this->db->delete($table); 
    
            return $status;
    
        }
        function get_melting_issue_process_details()
        {
            $returnData = array();
            
            $sql=$this->db->query("SELECT recd.id_melting_recd,m.id_old_metal_process,recd.received_wt,c.name as category_name,IFNULL(testing_det.testing_loss_weight,0) as testing_loss_weight,(recd.received_wt-IFNULL(testing_det.testing_loss_weight,0)) as actual_weight,concat(p.process_no,' ',k.firstname) as process_no,
            k.firstname as karigar_name,recd.id_product,recd.id_design,recd.id_sub_design,recd.received_category,recd.recd_pcs,IFNULL(recd.id_section,'') as id_section
            FROM ret_old_metal_melting m 
            LEFT JOIN ret_old_metal_melting_details d ON d.id_melting = m.id_melting
            LEFT JOIN ret_old_metal_melting_recd_details recd ON recd.id_melting = m.id_melting
            LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = m.id_old_metal_process
            LEFT JOIN ret_category c ON c.id_ret_category = recd.received_category
            LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
            LEFT JOIN (SELECT IFNULL(SUM(t.production_loss),0) as testing_loss_weight,t.id_melting_recd
                      FROM ret_old_metal_testing t 
                      where t.testing_status = 1
                      GROUP BY t.id_melting_recd) as testing_det ON testing_det.id_melting_recd = recd.id_melting_recd
            WHERE p.id_metal_process = 1 AND recd.id_melting_recd IS NOT NULL and recd.melting_status  = 2
            group by recd.id_melting_recd");
            
           $result = $sql->result_array();
           foreach($result as $items)
           {
               $items['testing_details'] = $this->get_process_testing_details($items['id_melting_recd']);
               $returnData[]=$items;
           }
           
           return $returnData;
       }
    
    function get_process_testing_details($id_melting_recd)
    {
        $sql = $this->db->query("SELECT p.id_old_metal_process,k.firstname as karigar_name,p.process_no,t.id_melting_recd,mp.process_no,
        t.net_wt as issue_wt,t.purity,t.received_wt,IFNULL(t.received_purity,0) as received_purity,t.production_loss,c.name as category_name
        FROM ret_old_metal_testing t 
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = t.id_old_metal_process
        LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
        LEFT JOIN ret_old_metal_melting_recd_details r ON r.id_melting_recd = t.id_melting_recd
        LEFT JOIN ret_old_metal_melting_details d ON d.id_melting = r.id_melting
        LEFT JOIN ret_old_metal_melting m ON m.id_melting = d.id_melting
        LEFT JOIN ret_category c ON c.id_ret_category = r.received_category
        LEFT JOIN ret_old_metal_process mp ON mp.id_old_metal_process = m.id_old_metal_process
        where t.id_melting_recd=".$id_melting_recd." and t.testing_status = 1 group by t.id_melting_recd");
        return $sql->result_array();
    }
    
    function get_process_melting_details($id)
    {
        $sql=$this->db->query("SELECT r.id_melting_recd,c.name as category_name,r.received_wt,(r.received_wt-IFNULL(testing.testing_loss_wt,0)) as actual_weight,
        p.process_no,p.id_old_metal_process,k.firstname as karigar_name
        FROM ret_old_metal_melting_recd_details r 
        LEFT JOIN ret_old_metal_melting_details d ON d.id_melting = r.id_melting
        LEFT JOIN ret_old_metal_melting m ON m.id_melting = d.id_melting
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = m.id_old_metal_process_receipt
        LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
        LEFT JOIN ret_category c ON c.id_ret_category = r.received_category
        LEFT JOIN (SELECT IFNULL(SUM(t.production_loss),0) as testing_loss_wt,t.id_melting_recd
                  FROM ret_old_metal_testing t 
                  GROUP BY t.id_melting_recd) as testing on testing.id_melting_recd = r.id_melting_recd
        WHERE p.process_for = 2 AND p.id_metal_process = 1  and r.tested_purity = 0 and p.id_old_metal_process = ".$id."
        GROUP BY r.id_melting_recd"); 
        return $sql->result_array();
    }

    function get_Active_Refining_details()
        {
            $sql=$this->db->query("SELECT p.id_old_metal_process,p.process_no
            FROM ret_old_metal_process p
            LEFT JOIN ret_old_metal_refining r on r.id_old_metal_process = p.id_old_metal_process
            LEFT JOIN ret_old_metal_testing t ON t.id_metal_testing=r.id_metal_testing
            LEFT JOIN ret_old_metal_melting_recd_details d ON d.id_melting_recd=t.id_melting_recd
            LEFT JOIN ret_old_metal_melting m ON m.id_melting=d.id_melting
            WHERE p.process_for=1 AND d.melting_status =4 
            ");
           $result = $sql->result_array();
           foreach($result as $items)
           {
               $returnData[]=array(
                                   'id_old_metal_process'       =>$items['id_old_metal_process'],
                                   'process_no'                 =>$items['process_no'],
                                   'refining_process_details'   =>$this->get_process_refiining_details($items['id_old_metal_process']),
                                  );
           }
           
           return $returnData;
       }

       function get_process_refiining_details($id)
       {
           $sql=$this->db->query("SELECT p.process_no,c.name as category_name,t.received_wt as net_wt,t.received_purity as purity,p.id_old_metal_process,t.amount,
           m.id_melting,r.id_metal_refining,d.id_melting_recd
           FROM ret_old_metal_refining r 
           LEFT JOIN ret_old_metal_testing t ON t.id_metal_testing=r.id_metal_testing
           LEFT JOIN ret_old_metal_melting_recd_details d ON d.id_melting_recd=t.id_melting_recd
           LEFT JOIN ret_old_metal_melting m ON m.id_melting=d.id_melting
           LEFT JOIN ret_category c ON c.id_ret_category=d.received_category
           LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=r.id_old_metal_process
           WHERE p.process_for=1 AND d.melting_status=4 AND p.id_old_metal_process=".$id."");
            return $sql->result_array();
       }
    //Process Master
    function ajax_getMetalProcess()
    {
        $sql=$this->db->query("SELECT * FROM `ret_old_metal_process_master`");
        return $sql->result_array();
    }
    
    function getMetalProcess($id_metal_process)
    {
        $sql=$this->db->query("SELECT * FROM `ret_old_metal_process_master` where id_metal_process=".$id_metal_process."");
        return $sql->row_array();
    }
    
    //Process Master
        
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
		$sql = "SELECT max(pocket_no) as pocket_no FROM ret_old_metal_pocket ORDER by id_metal_pocket DESC";
		return $this->db->query($sql)->row()->pocket_no;	
	}
    
    function get_old_metal_type()
    {
        $sql=$this->db->query("SELECT * FROM ret_old_metal_type");
        return $sql->result_array();
    }

    /*function get_metal_stock_list($data)
    {
        $return_data=array();
        
        $sql=$this->db->query("SELECT est.id_old_metal_type,SUM(s.gross_wt) as gross_wt,SUM(s.wast_wt) as wast_wt,t.metal_type,SUM(IFNULL(s.gross_wt,0)-IFNULL(s.dust_wt,0)-IFNULL(s.stone_wt,0)) as pure_wt, SUM(s.old_metal_rate) as old_metal_rate,SUM(s.rate) as rate,COUNT(DISTINCT s.old_metal_sale_id) as tot_count,IFNULL(SUM(s.dust_wt),0) as dust_wt,IFNULL(SUM(s.stone_wt),0) as stone_wt,IFNULL(SUM(s.net_wt),0) as net_wt 
        FROM ret_bill_old_metal_sale_details s
        LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_estimation e ON e.estimation_id=est.est_id
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type=est.id_old_metal_type
        LEFT JOIN ret_billing b ON b.bill_id=s.bill_id 
        WHERE b.bill_status=1 and s.is_pocketed=0 and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')  and s.current_branch=".$data['id_branch']."
        ".($data['id_metal']!='' ? " and s.metal_type=".$data['id_metal']."" :'')."");
        //print_r($this->db->last_query());exit;
        $result=$sql->result_array();
        foreach($result as $items)
        {   
            $purity_per=0;
            $bill_details=$this->get_old_metal_details($data['from_date'],$data['to_date'],$data['id_branch'],$data['id_metal']);
            foreach($bill_details as $bill)
            {   
                $purity_per+=$bill['purity_per'];
            }
            $return_data[]=array(
                'gross_wt'          =>$items['gross_wt'],
                'stone_wt'          =>$items['stone_wt'],
                'dust_wt'           =>$items['dust_wt'],
                'net_wt'            =>$items['net_wt'],
                'metal_type'        =>'Old Metal',
                'wast_wt'           =>$items['wast_wt'],
                'pure_wt'           =>$items['pure_wt'],
                'old_metal_rate'    =>$items['old_metal_rate'],
                'rate'              =>$items['rate'],
                'total_count'       =>$items['tot_count'],
                'type'              =>'old_metal',
                'purity_per'        =>number_format(($purity_per/sizeof($bill_details)),2,'.',''),
                'bill_det'          =>$bill_details,
            );
        }

        return $return_data;
    }*/
    
    
    function get_headOffice()
	{
		$data=$this->db->query("SELECT b.is_ho,b.id_branch,name FROM branch b where b.is_ho=1");
		return $data->row_array();
	}
	
    function get_metal_stock_list($data)
	{
		$return_Data=[];

        
            $sql=$this->db->query("SELECT t.metal_type,s.metal_type
            FROM ret_billing b 
            LEFT JOIN ret_bill_old_metal_sale_details s ON s.bill_id=b.bill_id
            LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
            LEFT JOIN ret_old_metal_type t ON t.id_metal_type=est.id_old_metal_type
            WHERE b.bill_status=1  AND s.is_transferred=1
            and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
            ".($data['id_metal']!='' ? " and s.metal_type=".$data['id_metal']."" :'')."
            ".($data['id_branch']!='' ? " and s.current_branch=".$data['id_branch']."" :'')."
            ".($data['from_branch']!='' ? " and b.id_branch=".$data['from_branch']."" :'')."
            GROUP by s.metal_type");
            //print_r($this->db->last_query());exit;
            $result=$sql->result_array();
            foreach($result as $items)
            {
                $billDetails=$this->old_metal_bill_details($data['from_date'],$data['to_date'],$data['id_branch'],$items['metal_type'],$data['from_branch']);
                $purity_per=0;
                $gross_wt=0;
                $nwt=0;
                $rate=0;
                $piece=0;
                $total_purity=0;
                $diawt=0;
                foreach($billDetails as $bill)
                {
                    $piece+=$bill['piece'];
                    $gross_wt+=$bill['gross_wt'];
                    $nwt+=$bill['net_wt'];
                    $rate+=$bill['rate'];
                    $diawt+=$bill['diawt'];
                    $total_purity+=number_format(($bill['touch']*$bill['piece']),2,'.','');
                    $purity_per+=number_format(($bill['touch']*$bill['piece']),2,'.','');
                }
                
                $return_Data[]=array(
                'metal_type'        =>'OLD METAL ITEMS',
                'type'              =>'old_metal',
                'piece'			    =>number_format($piece,0,'.',''),
                'gross_wt'			=>number_format($gross_wt,3,'.',''),
                'net_wt'			=>number_format($nwt,3,'.',''),
                'dia_wt'            =>number_format($diawt,3,'.',''),
                'rate'				=>number_format($rate,2,'.',''),
                'total_purity'		=>number_format($total_purity,3,'.',''),
                'purity_per'        =>number_format($purity_per/($piece),2,'.',''),
                'bill_det'			=>$billDetails,
                );
            }
       
            $sales_ret=$this->db->query("SELECT mt.id_metal
            FROM ret_taging tag 
            LEFT JOIN ret_bill_details d ON d.tag_id=tag.tag_id
            LEFT JOIN ret_billing b ON b.bill_id=d.bill_id
            LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
            LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
            LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
            WHERE b.bill_status=1 AND tag.tag_status=6 and (tag.tag_process=0 or tag.tag_process=2)
            and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
            ".($data['id_metal']!='' ? " and mt.id_metal=".$data['id_metal']."" :'')."
            ".($data['id_branch']!='' ? " and tag.current_branch=".$data['id_branch']."" :'')."
            ".($data['from_branch']!='' ? " and b.id_branch=".$data['from_branch']."" :'')."
            GROUP by mt.id_metal");
            //print_r($this->db->last_query());exit;
            $sales_ret_result=$sales_ret->result_array();
            foreach($sales_ret_result as $val)
            {
                $SalesRetDetails=$this->get_sales_ret_details($data['from_date'],$data['to_date'],$data['id_branch'],$val['id_metal'],$data['from_branch']);
                $piece=0;
                $purity_per=0;
                $gross_wt=0;
                $nwt=0;
                $diawt=0;
                $rate=0;
                foreach($SalesRetDetails as $sales)
                {
                    $piece+=$sales['piece'];
                    $gross_wt+=$sales['gross_wt'];
                    $nwt+=$sales['net_wt'];
                    $diawt+=$sales['diawt'];
                    $rate+=$sales['item_cost'];
                    $purity_per+=$sales['purity_per'];
                }

                if($gross_wt>0)
                {
                    $return_Data[]=array(
                        'metal_type'        =>'SALES RETURN ITEMS',
                        'type'              =>'sales_return',
                        'piece'			    =>number_format($piece,0,'.',''),
                        'gross_wt'			=>number_format($gross_wt,3,'.',''),
                        'net_wt'			=>number_format($nwt,3,'.',''),
                        'dia_wt'            =>number_format($diawt,3,'.',''),
                        'rate'				=>number_format($rate,2,'.',''),
                        'purity_per'        =>number_format($purity_per/sizeof($SalesRetDetails),2,'.',''),
                        'bill_det'			=>$SalesRetDetails,
                    );
                }
                
                
            }

        
            $partly_sales = $this->db->query("SELECT mt.id_metal
            FROM ret_partlysold s
            LEFT JOIN ret_taging tag on tag.tag_id = s.tag_id
            LEFT JOIN ret_bill_details d on d.bill_det_id=s.sold_bill_det_id
            LEFT JOIN ret_billing b on b.bill_id=d.bill_id
            LEFT JOIN ret_product_master p on p.pro_id=d.product_id
            LEFT JOIN ret_category cat on cat.id_ret_category=p.cat_id
            LEFT JOIN metal mt on mt.id_metal=cat.id_metal
            WHERE b.bill_status=1 and tag.is_partial=1 and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
            ".($data['id_metal']!='' ? " and mt.id_metal=".$data['id_metal']."" :'')."
            ".($data['id_branch']!='' ? " and tag.current_branch=".$data['id_branch']."" :'')."
            ".($data['from_branch']!='' ? " and b.id_branch=".$data['from_branch']."" :'')."
            GROUP by mt.id_metal");
            //print_r($this->db->last_query());exit;
            $partly_sale_result = $partly_sales->result_array();
            foreach($partly_sale_result as $val)
            {
                $PartlyDetails = $this->get_partly_sale_details($data['from_date'],$data['to_date'],$data['id_branch'],$val['id_metal'],$data['from_branch']);
                $piece = 0;
                $gross_wt = 0;
                $nwt=0;
                $diawt=0;
                $rate=0;
                $purity_per=0;
                foreach($PartlyDetails as $sales)
                {
                    $gross_wt+=$sales['gross_wt'];
                    $nwt+=$sales['net_wt'];
                    $diawt+=$sales['dia_wt'];
                    $rate+=$sales['item_cost'];
                    $purity_per+=$sales['purity_per'];
                }

                if($gross_wt>0)
                {
                    $return_Data[]=array(
                        'metal_type'        =>'PARTLY SALES ITEMS',
                        'type'              =>'partly_sales',
                        'piece'			    =>number_format($piece,0,'.',''),
                        'gross_wt'			=>number_format($gross_wt,3,'.',''),
                        'net_wt'			=>number_format($nwt,3,'.',''),
                        'dia_wt'            =>number_format($diawt,3,'.',''),
                        'rate'				=>number_format($rate,2,'.',''),
                        'purity_per'        =>number_format($purity_per/sizeof($PartlyDetails),2,'.',''),
                        'bill_det'			=>$PartlyDetails,
                    );
                }

            }
       

        
        
	    
		return $return_Data;
	}
    
    function get_partly_sale_details($from_date,$to_date,$id_branch,$id_metal,$from_branch)
    {
        $returnData=[];
        $sql=$this->db->query("SELECT (IFNULL(tag.gross_wt,0)-IFNULL(t.sold_gross_wt,0)-IFNULL(retag.gross_wt,0)) as gross_wt,
        (IFNULL(tag.net_wt,0)-IFNULL(t.sold_net_wt,0)-IFNULL(retag.net_wt,0)) as net_wt,
        (IFNULL(tagdia.wt,0) - IFNULL(t.sold_diawt,0) - IFNULL(retag.dia_wt,0)) as diawt,
        '0' as amount,cat.id_metal,mt.metal as metal_name,
        DATE_FORMAT(bill.bill_date,'%d-%m-%Y') as bill_date,bill.bill_no,bill.bill_id,'0' as is_checked,d.tag_id as trans_id,
        d.tag_id,
        if(mt.id_metal=1,'partly_sale_gold','partly_sale_silver') as item_type,'3' as transfer_items,
        (d.item_cost-d.item_total_tax) as item_cost,pur.purity,cat.name as category,'1' as piece,cat.id_ret_category as cat_id,
        pur.purity as touch,d.rate_per_grm as rate_per_gram
        FROM ret_bill_details d 
        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id
        LEFT JOIN ret_billing bill ON bill.bill_id=d.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        LEFT JOIN ret_purity pur ON pur.id_purity=tag.purity

        LEFT JOIN(SELECT IFNULL(SUM(ts.wt),0) as wt,ts.tag_id
        FROM ret_taging_stone ts 
        LEFT JOIN ret_stone s on s.stone_id = ts.stone_id
        WHERE s.stone_type = 1
        GROUP BY ts.tag_id) as tagdia on tagdia.tag_id = tag.tag_id 
        
        LEFT JOIN(SELECT r.ref_no,IFNULL(SUM(r.gross_wt),0) as gross_wt,IFNULL(SUM(r.net_wt),0) as net_wt,IFNULL(stn.wt,0) as dia_wt
        FROM ret_acc_stock_process_details r
        LEFT JOIN ret_acc_stock_process a ON a.id_process = r.id_process
        LEFT JOIN(SELECT IFNULL(SUM(acc_stn.stone_wt),0) as wt,acc_stn.id_process_details
         	FROM ret_acc_stock_process_stone_details acc_stn
            LEFT JOIN ret_stone s on s.stone_id = acc_stn.stone_id
           	LEFT JOIN ret_acc_stock_process_details ad on ad.id_process_details = acc_stn.id_process_details
            LEFT JOIN ret_acc_stock_process ac on ac.id_process = ad.id_process
            WHERE s.stone_type = 1 and ac.type = 3
        GROUP BY ad.ref_no) as stn on stn.id_process_details = r.id_process_details
        Where a.type = 3
        GROUP by r.ref_no) as retag ON retag.ref_no = tag.tag_id
        
        LEFT JOIN (SELECT pkdet.tag_id
        FROM ret_old_metal_pocket_details pkdet
        WHERE pkdet.type = 3 group by pkdet.tag_id) as pocket ON pocket.tag_id = d.tag_id
        
        LEFT JOIN (SELECT IFNULL(SUM(d.gross_wt),0) as sold_gross_wt,IFNULL(SUM(d.net_wt),0) as sold_net_wt,d.tag_id,IFNULL(stn.wt,0) as sold_diawt
        FROM ret_bill_details d
        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id
        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN(SELECT IFNULL(SUM(bs.wt),0) as wt,d.tag_id
             	FROM ret_billing_item_stones bs
                LEFT JOIN ret_bill_details d on d.bill_det_id = bs.bill_det_id
                LEFT JOIN ret_stone st on st.stone_id = bs.stone_id
                WHERE st.stone_type = 1
        GROUP BY d.tag_id) as stn on stn.tag_id = d.tag_id
        WHERE b.bill_status=1
        ".($from_branch!='' ?  " and b.id_branch=".$from_branch."" :'')."
        and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') 
        group by d.tag_id) as t ON t.tag_id=d.tag_id

        WHERE bill.bill_status=1 AND d.is_partial_sale=1 and pocket.tag_id IS NULL and mt.id_metal=".$id_metal."
        
        ".($id_branch!='' ?  " and tag.current_branch=".$id_branch."" :'')."
        ".($from_branch!='' ?  " and bill.id_branch=".$from_branch."" :'')."
		and (date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') 
        having gross_wt > 0");
		//print_r($this->db->last_query());exit;
		$result =  $sql->result_array();
        foreach($result as $items)
        {
            $returnData[]=array(
                'piece'          =>$items['piece'],
                'old_metal_cat'  =>$items['category'],
                'gross_wt'       =>$items['gross_wt'],
                'net_wt'         =>$items['net_wt'],
                'is_checked'     =>$items['is_checked'],
                'trans_id'       =>$items['trans_id'],
                'purity_per'     =>number_format((($items['purity'])),2,'.',''),
                'purity'         =>$items['purity'],
                'item_cost'      =>$items['item_cost'],
                'rate_per_grm'   =>$items['rate_per_grm'],
                'type'           =>'partly_sales_items',
                'bill_date'      =>$items['bill_date'],
                'bill_no'        =>$items['bill_no'],
                'bill_id'        =>$items['bill_id'],
                'tag_id'         =>$items['tag_id'],
                'diawt'         =>$items['diawt'],
                'cat_id'        =>$items['cat_id'],
                'touch'         =>$items['purity'],
                'pocket_type'    =>3,
            );  
        }

        return $returnData;
    }
    
    
    function get_sales_ret_details($from_date,$to_date,$id_branch,$id_metal,$from_branch)
    {
        $returnData=[];
        $sql=$this->db->query("SELECT (IFNULL((d.gross_wt),0)-IFNULL(retag.gross_wt,0)) as gross_wt,(IFNULL((d.net_wt),0)-IFNULL(retag.net_wt,0)) as net_wt,'0' as is_checked,t.tag_id,
        t.tag_id as trans_id,pur.purity,(d.item_cost-d.item_total_tax) as item_cost,d.rate_per_grm,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.bill_no,b.bill_id,IFNULL(t.piece,0) as piece,cat.name as category,cat.id_ret_category as cat_id,d.rate_per_grm,
        (IFNULL(dia.stn_wt,0) - IFNULL(retag.dia_wt,0)) as diawt
        FROM ret_billing b 
        LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id
        LEFT JOIN ret_taging t ON t.tag_id=d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id=t.product_id
        LEFT JOIN ret_category cat ON cat.id_ret_category=p.cat_id
        LEFT JOIN metal mt ON mt.id_metal=cat.id_metal
        LEFT JOIN ret_purity pur ON pur.id_purity=t.purity
        
        LEFT JOIN(SELECT r.ref_no,IFNULL(SUM(r.gross_wt),0) as gross_wt,IFNULL(SUM(r.net_wt),0) as net_wt,IFNULL(stn.wt,0) as dia_wt
        FROM ret_acc_stock_process_details r
        LEFT JOIN ret_acc_stock_process a ON a.id_process = r.id_process
            LEFT JOIN(SELECT IFNULL(SUM(acc_stn.stone_wt),0) as wt,acc_stn.id_process_details
                FROM ret_acc_stock_process_stone_details acc_stn
                LEFT JOIN ret_stone s on s.stone_id = acc_stn.stone_id
                LEFT JOIN ret_acc_stock_process_details ad on ad.id_process_details = acc_stn.id_process_details
                LEFT JOIN ret_acc_stock_process ac on ac.id_process = ad.id_process
                WHERE s.stone_type = 1 and ac.type = 1
            GROUP BY ad.ref_no) as stn on stn.id_process_details = r.id_process_details
        Where a.type = 1
        GROUP by r.ref_no) as retag ON retag.ref_no = t.tag_id
        
        LEFT JOIN (SELECT pkdet.tag_id
        FROM ret_old_metal_pocket_details pkdet
        WHERE pkdet.type = 2 group by pkdet.tag_id) as pocket ON pocket.tag_id = d.tag_id

        LEFT JOIN(SELECT IFNULL(SUM(ts.wt),0) as stn_wt,ts.tag_id
        FROM ret_taging_stone ts
        LEFT JOIN ret_stone s on s.stone_id = ts.stone_id
        WHERE s.stone_type = 1
        GROUP BY ts.tag_id) as dia on dia.tag_id = d.tag_id
        
        WHERE t.tag_status=6 AND b.bill_status=1 and pocket.tag_id IS NULL
        and mt.id_metal=".$id_metal."
        ".($id_branch!='' ?  " and t.current_branch=".$id_branch."" :'')."
        ".($from_branch!='' ?  " and b.id_branch=".$from_branch."" :'')."
		and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		having gross_wt > 0");
	    //print_r($this->db->last_query());exit;
		$result= $sql->result_array();
		foreach($result as $items)
		{
		    $returnData[]=array(
		                       'gross_wt'       =>$items['gross_wt'],
		                       'net_wt'         =>$items['net_wt'],
		                       'is_checked'     =>$items['is_checked'],
		                       'trans_id'       =>$items['trans_id'],
		                       'purity_per'     =>$items['purity'],
                               'touch'          =>$items['purity'],
		                       'item_cost'      =>$items['item_cost'],
		                       'rate_per_gram'   =>$items['rate_per_grm'],
		                       'type'           =>'sales_return_items',
		                       'bill_date'      =>$items['bill_date'],
                               'bill_no'        =>$items['bill_no'],
                               'bill_id'        =>$items['bill_id'],
                               'tag_id'         =>$items['tag_id'],
                               'purity'         =>$items['purity'],
                               'diawt'          =>$items['diawt'],
                               'cat_id'         =>$items['cat_id'],
                               'old_metal_cat'  =>$items['category'],
                               'pocket_type'    =>2,
                               'piece'          =>1,
		                       );
		}
		return $returnData;
    }
    
	function old_metal_bill_details($from_date,$to_date,$id_branch,$metal_type,$from_branch)
	{
	    $returnData=array();
        $sql=$this->db->query("SELECT s.old_metal_sale_id as trans_id,(s.gross_wt-IFNULL(retag.gross_wt,0)) as gross_wt,(s.net_wt-IFNULL(retag.net_wt,0)) as net_wt,s.rate as amount,est.id_old_metal_type,
        t.metal_type,DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.bill_no,b.bill_id,'0' as is_checked,s.old_metal_sale_id,
        if(s.metal_type=1,'old_metal_gold','old_metal_silver') as item_type,'1' as transfer_items,(IFNULL(s.gross_wt,0)-IFNULL(s.dust_wt,0)-IFNULL(s.stone_wt,0)) as pure_wt,s.old_metal_rate,
        s.rate as rate,IFNULL(s.dust_wt,0) as dust_wt,IFNULL(s.stone_wt,0) as stone_wt,IFNULL(s.wast_wt,0) as wast_wt,IFNULL(s.purity,0) as purity,IFNULL(s.piece,0) as piece,
        cat.old_metal_cat,(IFNULL(stn_dt.diawt,0) - IFNULL(retag.dia_wt,0)) as diawt,s.touch,est.rate_per_gram
        FROM ret_billing b 
        LEFT JOIN ret_bill_old_metal_sale_details s ON s.bill_id=b.bill_id
        
        LEFT JOIN(SELECT r.ref_no,IFNULL(SUM(r.gross_wt),0) as gross_wt,IFNULL(SUM(r.net_wt),0) as net_wt,IFNULL(stn.wt,0) as dia_wt
        FROM ret_acc_stock_process_details r
        LEFT JOIN ret_acc_stock_process a ON a.id_process = r.id_process
       
        LEFT JOIN(SELECT IFNULL(SUM(acc_stn.stone_wt),0) as wt,acc_stn.id_process_details
         	FROM ret_acc_stock_process_stone_details acc_stn
            LEFT JOIN ret_stone s on s.stone_id = acc_stn.stone_id
           	LEFT JOIN ret_acc_stock_process_details ad on ad.id_process_details = acc_stn.id_process_details
            LEFT JOIN ret_acc_stock_process ac on ac.id_process = ad.id_process
            WHERE s.stone_type = 1 and ac.type = 4
        GROUP BY ad.ref_no) as stn on stn.id_process_details = r.id_process_details
       
        Where a.type = 4
        GROUP by r.ref_no) as retag ON retag.ref_no = s.old_metal_sale_id



        LEFT JOIN (SELECT od.id_pocket_details,od.old_metal_sale_id
        FROM ret_old_metal_pocket_details od 
        LEFT JOIN ret_old_metal_pocket old_pc on old_pc.id_metal_pocket = od.id_metal_pocket
        where old_pc.trans_type = 1
        group by od.id_pocket_details) as old_pocket on old_pocket.old_metal_sale_id = s.old_metal_sale_id
        
        LEFT JOIN(SELECT IFNULL(SUM(st.wt),0) as diawt,st.old_metal_sale_id,stn.stone_code
        FROM ret_billing_item_stones st 
        LEFT JOIN ret_stone stn ON stn.stone_id = st.stone_id
        WHERE stn.stone_type = 1
        GROUP BY st.old_metal_sale_id) as stn_dt ON stn_dt.old_metal_sale_id = s.old_metal_sale_id

        LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type=est.id_old_metal_type
        LEFT JOIN ret_old_metal_category cat on cat.id_old_metal_cat = est.id_old_metal_category
        WHERE b.bill_status=1 AND s.old_metal_sale_id IS NOT null  AND s.is_transferred=1  and old_pocket.id_pocket_details is null
        ".($id_branch!='' ?  " and s.current_branch=".$id_branch."" :'')."
        ".($from_branch!='' ?  " and b.id_branch=".$from_branch."" :'')."
        ".($metal_type!='' ?  " and s.metal_type=".$metal_type."" :'')."
        and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
        having gross_wt > 0");
        //print_r($this->db->last_query());exit;
        $result=$sql->result_array();
        foreach($result as $items)
        {
            $returnData[]=array(
                               'trans_id'           =>$items['trans_id'],
                               'gross_wt'           =>$items['gross_wt'],
                               'net_wt'             =>$items['net_wt'],
                               'diawt'              =>$items['diawt'],
                               'amount'             =>$items['amount'],
                               'id_old_metal_type'  =>$items['id_old_metal_type'],
                               'metal_type'         =>$items['metal_type'],
                               'bill_date'          =>$items['bill_date'],
                               'bill_no'            =>$items['bill_no'],
                               'bill_id'            =>$items['bill_id'],
                               'is_checked'         =>$items['is_checked'],
                               'old_metal_sale_id'  =>$items['old_metal_sale_id'],
                               'item_type'          =>$items['item_type'],
                               'transfer_items'     =>$items['transfer_items'],
                               'pure_wt'            =>$items['pure_wt'],
                               'old_metal_rate'     =>$items['old_metal_rate'],
                               'rate'               =>$items['rate'],
                               'item_cost'          =>$items['rate'],
                               'dust_wt'            =>$items['dust_wt'],
                               'stone_wt'           =>$items['stone_wt'],
                               'wast_wt'            =>$items['wast_wt'],
                               'purity'             =>$items['purity'],
                               'piece'              =>$items['piece'],
                               'old_metal_cat'      =>$items['old_metal_cat'],
                               'diawt'              =>$items['diawt'],
                               'type'               =>'old_metal_items',
                               'pocket_type'        =>1,
                               'purity_per'         =>number_format((($items['purity'])),2,'.',''),
                               'touch'              =>number_format((($items['touch'])),2,'.',''),
                               'rate_per_gram'      =>$items['rate_per_gram']
                               );
        }
        return $returnData;
	}
    
    

    function get_old_metal_details($from_date,$to_date,$id_branch,$id_metal)
    {
        $return_data=array();
        
        $sql=$this->db->query("SELECT s.old_metal_sale_id,est.id_old_metal_type,IFNULL(s.gross_wt,0) as gross_wt,IFNULL(s.wast_wt,0) as wast_wt,t.metal_type,(IFNULL(s.gross_wt,0)-IFNULL(s.dust_wt,0)-IFNULL(s.stone_wt,0)) as pure_wt,
       s.old_metal_rate,s.rate as rate,IFNULL(s.dust_wt,0) as dust_wt,IFNULL(s.stone_wt,0) as stone_wt,IFNULL(s.net_wt,0) as net_wt,
       DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.bill_no,b.bill_id,s.esti_old_metal_sale_id
        FROM ret_purchase_items_log l 
        LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=l.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_estimation e ON e.estimation_id=est.est_id
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type=est.id_old_metal_type
        LEFT JOIN ret_billing b ON b.bill_id=s.bill_id
        WHERE b.bill_status=1 and s.is_pocketed=0 and (date(l.date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')  and l.to_branch=".$id_branch." AND l.item_type=1
         ".($id_metal!='' ? " and s.metal_type=".$id_metal."" :'')."
        GROUP by s.esti_old_metal_sale_id");
        //print_r($this->db->last_query());exit;
        
        /*$sql=$this->db->query("SELECT s.old_metal_sale_id,est.id_old_metal_type,IFNULL(s.gross_wt,0) as gross_wt,IFNULL(s.wast_wt,0) as wast_wt,t.metal_type,(IFNULL(s.gross_wt,0)-IFNULL(s.dust_wt,0)-IFNULL(s.stone_wt,0)) as pure_wt,
       s.old_metal_rate,s.rate as rate,IFNULL(s.dust_wt,0) as dust_wt,IFNULL(s.stone_wt,0) as stone_wt,IFNULL(s.net_wt,0) as net_wt,
       DATE_FORMAT(b.bill_date,'%d-%m-%Y') as bill_date,b.bill_no,b.bill_id,s.esti_old_metal_sale_id
        FROM ret_bill_old_metal_sale_details s
        LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id=s.esti_old_metal_sale_id
        LEFT JOIN ret_estimation e ON e.estimation_id=est.est_id
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type=est.id_old_metal_type
        LEFT JOIN ret_billing b ON b.bill_id=s.bill_id
        WHERE s.is_transferred=1 and b.bill_status=1 and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') and s.is_pocketed=0
        ".($id_old_metal_type!='' ? " and est.id_old_metal_type=".$id_old_metal_type."" :'')."
        ".($id_branch!='' && $id_branch>0 ? " and s.current_branch=".$id_branch."" :'')."");*/
        
        $result=$sql->result_array();
        foreach($result as $items)
        {
            $return_data[]=array(
                'id_old_metal_type' =>$items['id_old_metal_type'],
                'old_metal_sale_id' =>$items['old_metal_sale_id'],
                'gross_wt'          =>$items['gross_wt'],
                'stone_wt'          =>$items['stone_wt'],
                'dust_wt'           =>$items['dust_wt'],
                'net_wt'            =>$items['net_wt'],
                'metal_type'        =>$items['metal_type'],
                'wast_wt'           =>$items['wast_wt'],
                'pure_wt'           =>$items['pure_wt'],
                'old_metal_rate'    =>$items['old_metal_rate'],
                'rate'              =>$items['rate'],
                'bill_date'         =>$items['bill_date'],
                'bill_no'           =>$items['bill_no'],
                'bill_id'           =>$items['bill_id'],
                'is_checked'        =>0,
                'purity_per'        =>number_format((($items['rate']/$items['pure_wt'])/($items['old_metal_rate'])*100),2,'.',''),
            );
        }
        return $return_data;
        
    }
    
    function get_pocket_list($data)
    {
        $sql=$this->db->query("SELECT * FROM `ret_old_metal_pocket` WHERE (date(date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."') ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    
    function get_melting_pocket_details($id_metal_pocket)
    {
        $sql=$this->db->query("
        SELECT IFNULL(SUM(d.piece),0) as piece,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt,IFNULL(SUM(d.diawt),0) as dia_wt,d.id_metal_type,t.metal_type,p.id_metal_pocket,p.pocket_no,p.id_branch,IFNULL(pl_iss.issue_gwt,0) as polishing_issue_gwt,IFNULL(pl_iss.issue_nwt,0) as polishing_issue_nwt,IFNULL(pl_iss.issue_pcs,0) as polishing_issue_pcs,IFNULL(m.issue_pcs,0) as melting_issue_pcs,IFNULL(m.issue_gwt,0) as melting_issue_gwt,
        IFNULL(m.issue_nwt,0) as melting_issue_nwt,round(IFNULL(SUM(d.purity*d.piece)/SUM(d.piece),0),2) as tot_purity,round(IFNULL(SUM(d.rate_per_gram*d.piece)/SUM(d.piece),0),2) as avg_rate,
        (IFNULL(pl_iss.issue_gwt,0)+IFNULL(m.issue_gwt,0)) as issue_gwt,(IFNULL(pl_iss.issue_nwt,0)+IFNULL(m.issue_nwt,0)) as issue_nwt,
        (IFNULL(pl_iss.issue_dia_wt,0) + IFNULL(m.issue_dia_wt,0)) as issue_dia_wt,
        (IFNULL(pl_iss.issue_pcs,0)+IFNULL(m.issue_pcs,0)) as issue_pcs,(IFNULL(pl_iss.issue_purity,0)+IFNULL(m.issue_purity,0)) as issue_purity,
        IFNULL(SUM(d.item_cost),0) as amount,p.trans_type
        FROM ret_old_metal_pocket_details d 
        LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_metal_pocket
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_metal_type
      
        LEFT JOIN (SELECT IFNULL(SUM(pd.issue_pcs),0) as issue_pcs,IFNULL(SUM(pd.issue_gwt),0) as issue_gwt,
        IFNULL(SUM(pd.issue_nwt),0) as issue_nwt,IFNULL(SUM(pd.issue_diawt),0) as issue_dia_wt,pd.id_old_metal_type,
        IFNULL(SUM(pd.issue_purity),0) as issue_purity,pd.id_pocket
        FROM ret_old_metal_polishing_details pd
        GROUP BY pd.id_old_metal_type,pd.id_pocket) as pl_iss ON pl_iss.id_old_metal_type = d.id_metal_type AND pl_iss.id_pocket = d.id_metal_pocket
        
        LEFT JOIN(SELECT IFNULL(SUM(mdt.issue_pcs),0) as issue_pcs,IFNULL(SUM(mdt.issue_gwt),0) as issue_gwt,
        IFNULL(SUM(mdt.issue_nwt),0) as issue_nwt,IFNULL(SUM(mdt.issue_diawt),0) as issue_dia_wt,mdt.id_metal_type,
        IFNULL(SUM(mdt.issue_purity),0) as issue_purity,mdt.id_pocket
        FROM ret_old_metal_melting_details mdt
        GROUP BY mdt.id_metal_type,mdt.id_pocket) as m ON m.id_metal_type = d.id_metal_type AND m.id_pocket = d.id_metal_pocket

        where p.piece>0 AND p.status = 0 and d.id_metal_pocket = ".$id_metal_pocket." and d.type=1
        GROUP by d.id_metal_type
        having issue_nwt<net_wt");
        return $sql->result_array();
    }

    
    function get_melting_SalesPocket_details($id_metal_pocket)
    {
        $sql=$this->db->query("SELECT IFNULL(SUM(d.piece),0) as piece,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt,IFNULL(SUM(d.diawt),0) as dia_wt,d.id_metal_type,p.id_metal_pocket,p.pocket_no,p.id_branch,IFNULL(pl_iss.issue_gwt,0) as polishing_issue_gwt,IFNULL(pl_iss.issue_nwt,0) as polishing_issue_nwt,IFNULL(pl_iss.issue_pcs,0) as polishing_issue_pcs,IFNULL(m.issue_pcs,0) as melting_issue_pcs,IFNULL(m.issue_gwt,0) as melting_issue_gwt,
        IFNULL(m.issue_nwt,0) as melting_issue_nwt,round(IFNULL(SUM(d.purity*d.piece)/SUM(d.piece),0),2) as tot_purity,round(IFNULL(SUM(d.rate_per_gram*d.piece)/SUM(d.piece),0),2) as avg_rate,
        (IFNULL(pl_iss.issue_gwt,0)+IFNULL(m.issue_gwt,0)) as issue_gwt,(IFNULL(pl_iss.issue_nwt,0)+IFNULL(m.issue_nwt,0)) as issue_nwt,
        (IFNULL(pl_iss.issue_pcs,0)+IFNULL(m.issue_pcs,0)) as issue_pcs,(IFNULL(pl_iss.issue_purity,0)+IFNULL(m.issue_purity,0)) as issue_purity,
        (IFNULL(pl_iss.issue_dia_wt,0) + IFNULL(m.issue_dia_wt,0)) as issue_dia_wt,
        IFNULL(SUM(d.item_cost),0) as amount,p.trans_type,cat.id_ret_category as cat_id,cat.name as category, t.metal_type
        FROM ret_old_metal_pocket_details d 
        LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_metal_pocket
        LEFT JOIN ret_category cat on cat.id_ret_category = d.id_category
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_metal_type

        LEFT JOIN (SELECT IFNULL(SUM(pd.issue_pcs),0) as issue_pcs,IFNULL(SUM(pd.issue_gwt),0) as issue_gwt,
        IFNULL(SUM(pd.issue_nwt),0) as issue_nwt,IFNULL(SUM(pd.issue_diawt),0) as issue_dia_wt,pd.id_category,
        IFNULL(SUM(pd.issue_purity),0) as issue_purity,pd.id_pocket
        FROM ret_old_metal_polishing_details pd
        GROUP BY pd.id_category,pd.id_pocket) as pl_iss ON pl_iss.id_category = d.id_category AND pl_iss.id_pocket = d.id_metal_pocket

        LEFT JOIN(SELECT IFNULL(SUM(mdt.issue_pcs),0) as issue_pcs,IFNULL(SUM(mdt.issue_gwt),0) as issue_gwt,
        IFNULL(SUM(mdt.issue_nwt),0) as issue_nwt,IFNULL(SUM(mdt.issue_diawt),0) as issue_dia_wt,mdt.id_category,
        IFNULL(SUM(mdt.issue_purity),0) as issue_purity,mdt.id_pocket
        FROM ret_old_metal_melting_details mdt
        GROUP BY mdt.id_category,mdt.id_pocket) as m ON m.id_category = d.id_category AND m.id_pocket = d.id_metal_pocket
        
        
        where p.gross_wt>0 AND p.status = 0 and d.id_metal_pocket = ".$id_metal_pocket." and d.type!=1
        GROUP by d.id_category
        having issue_nwt<net_wt");
        //print_r($this->db->last_query());exit;

        return $sql->result_array();
    }
    
    function get_pocket_details($data)
    {
        $returnData = [];
        $sql=$this->db->query("SELECT p.id_metal_pocket,p.pocket_no,p.id_branch,p.piece,p.gross_wt,p.net_wt,p.dia_wt,p.avg_purity,p.amount,
        p.status,p.issue_gwt,p.issue_nwt,p.issue_pcs,IFNULL(p.total_purity,0) as tot_purity,IFNULL(p.issue_purity,0) as issue_purity,p.trans_type
        FROM ret_old_metal_pocket p 
        where gross_wt>0 AND p.status = 0
        ".($data['trans_type']!='' ? " and p.trans_type=".$data['trans_type']."" :'')."");
        $result = $sql->result_array();
        foreach($result as $items)
        {
            if($items['trans_type']==1)
            {
                $item_details = $this->get_melting_pocket_details($items['id_metal_pocket']);

                $sales_item_details = $this->get_melting_SalesPocket_details($items['id_metal_pocket']);

            }else if($items['trans_type']==2){

                $item_details = $this->get_melting_tag_details($items['id_metal_pocket']);

            }
            else if($items['trans_type']==3){
                $item_details = $this->get_melting_non_tag_details($items['id_metal_pocket']);
            }
            
            if(sizeof($item_details) > 0 || sizeof($sales_item_details) > 0)
            {
                $items['item_details'] = $item_details;
                $items['sales_item_details'] = $sales_item_details;
                $returnData[] = $items;
            }
            
        }
        
        return $returnData;
    }
    
    function get_melting_tag_details($id_metal_pocket)
    {
        $sql = $this->db->query("SELECT pk.trans_type,t.tag_id,d.id_pocket_details,t.tag_code,p.product_name,
        d.gross_wt,d.piece,d.net_wt,d.id_metal_pocket,pk.trans_type,pk.pocket_no,
        IFNULL(d.item_cost,0) as item_cost,IFNULL(d.purity,0) as purity
        FROM ret_old_metal_pocket_details d 
        LEFT JOIN ret_old_metal_pocket pk ON pk.id_metal_pocket = d.id_metal_pocket
        LEFT JOIN ret_taging t ON t.tag_id = d.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = t.product_id
        LEFT JOIN ret_old_metal_melting_details md ON md.id_pocket_details = d.id_pocket_details
        WHERE d.id_metal_pocket =".$id_metal_pocket." and md.id_pocket_details IS NULL");
        return $sql->result_array();
    }

    function get_melting_non_tag_details($id_metal_pocket)
    {
        $sql = $this->db->query("SELECT d.id_pocket_details,d.id_metal_pocket
        ,d.piece,d.gross_wt,d.net_wt,p.trans_type,p.id_metal_pocket,p.pocket_no,pro.product_name
        FROM ret_old_metal_pocket_details d
        LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_metal_pocket
        LEFT JOIN ret_product_master pro ON pro.pro_id = d.id_product
        LEFT JOIN ret_design_master des ON des.design_no = d.id_design
        LEFT JOIN ret_sub_design_master subDes ON subDes.id_sub_design = d.id_sub_design
        LEFT JOIN ret_old_metal_melting_details md ON md.id_pocket_details = d.id_pocket_details
        WHERE p.trans_type = 3 AND p.id_metal_pocket = ".$id_metal_pocket." and md.id_pocket_details IS NULL");
        return $sql->result_array();
    }
    
    function get_polish_pocket()
    {
        $returnData = array();
        $sql=$this->db->query("SELECT p.id_metal_pocket,p.pocket_no,p.id_branch,p.piece,p.gross_wt,p.net_wt,p.avg_purity,p.amount,
        p.status,p.issue_gwt,p.issue_nwt,p.issue_pcs,IFNULL(p.total_purity,0) as tot_purity,IFNULL(p.issue_purity,0) as issue_purity
        FROM ret_old_metal_pocket p 
        LEFT JOIN (SELECT IFNULL(SUM(s.purity*s.piece),0) as purity,d.id_metal_pocket
        FROM ret_old_metal_pocket_details d 
        LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id=d.old_metal_sale_id
        LEFT JOIN ret_billing b ON b.bill_id=s.bill_id
        WHERE b.bill_status=1 AND d.old_metal_sale_id IS NOT NULL
        GROUP by d.id_metal_pocket) as det ON det.id_metal_pocket=p.id_metal_pocket
        where p.status = 0 and p.trans_type = 1
        having issue_nwt<net_wt");
        //print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        foreach($result as $items)
        {
            $returnData[]=array(
                                'id_metal_pocket'=>$items['id_metal_pocket'],
                                'pocket_no'=>$items['pocket_no'],
                                'pocket_details' =>$this->get_polishing_pocket_details($items['id_metal_pocket']),
                               );
        }
        
        return $returnData;
    }
    
    function get_polishing_pocket_details($id_metal_pocket)
    {
        $sql=$this->db->query("SELECT p.id_metal_pocket,p.pocket_no,IFNULL(SUM(d.gross_wt),0) as gross_wt,IFNULL(SUM(d.net_wt),0) as net_wt,IFNULL(SUM(d.diawt),0) as diawt,IFNULL(SUM(d.piece),0) as piece,IFNULL(SUM(d.item_cost),0) as amount,IFNULL(SUM(d.piece*d.purity),0) as tot_purity,IFNULL((IFNULL(SUM(d.piece*d.purity),0)/IFNULL(SUM(d.piece),0)),0) as avg_purity,
        IFNULL(issue_det.issue_pcs,0) as issue_pcs,IFNULL(issue_det.issue_gwt,0) as issue_gwt,IFNULL(issue_det.issue_nwt,0) as issue_nwt,IFNULL(issue_det.total_issue_purity,0) as total_issue_purity,
        IFNULL(t.metal_type,'') as old_metal_type,t.id_metal_type
        FROM ret_old_metal_pocket_details d 
        LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_metal_pocket
        LEFT JOIN ret_bill_old_metal_sale_details b ON b.old_metal_sale_id = d.old_metal_sale_id
        LEFT JOIN ret_estimation_old_metal_sale_details est ON est.old_metal_sale_id = b.esti_old_metal_sale_id
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_metal_type
        LEFT JOIN (SELECT det.id_pocket,det.id_old_metal_type,IFNULL(SUM(det.issue_pcs),0) as issue_pcs,IFNULL(SUM(det.issue_gwt),0) as issue_gwt,
                  IFNULL(SUM(det.issue_nwt),0) as issue_nwt,
                  IFNULL(SUM(det.issue_purity*det.issue_pcs),0) as total_issue_purity
                  FROM ret_old_metal_polishing l
                  LEFT JOIN ret_old_metal_polishing_details det ON det.id_polishing = l.id_polishing
                  WHERE det.id_polishing_details IS NOT NULL and det.id_pocket= ".$id_metal_pocket."
                  GROUP BY det.id_old_metal_type) as issue_det ON issue_det.id_old_metal_type = d.id_metal_type
        WHERE d.old_metal_sale_id IS NOT NULL  and p.id_metal_pocket= ".$id_metal_pocket."
        GROUP by d.id_metal_type
        Having gross_wt >issue_gwt");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    
    
    function generate_process_number($process_for,$id_metal_process)
	{
		$lastno = $this->get_last_process_number($process_for,$id_metal_process);
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

	function get_last_process_number($process_for,$id_metal_process)
    {
		$sql = "SELECT process_no FROM ret_old_metal_process where process_for=".$process_for." and id_metal_process=".$id_metal_process." ORDER BY id_old_metal_process DESC LIMIT 1";
		return $this->db->query($sql)->row()->process_no;	
	}
	
	function ajax_get_metal_process()
	{
	    $sql=$this->db->query("SELECT p.id_old_metal_process,p.process_no,if(p.process_for=1,'ISSUE','RECEIPT') as process_for,m.process_name,k.firstname as karigar_name,date_format(p.created_on,'%d-%m-%Y') as date_add,emp.firstname as emp_name
        FROM ret_old_metal_process p 
        LEFT JOIN ret_old_metal_process_master m ON m.id_metal_process=p.id_metal_process
        LEFT JOIN ret_karigar k ON k.id_karigar=p.id_karigar
        LEFT JOIN employee emp ON emp.id_employee=p.created_by
        order by p.id_old_metal_process DESC");
        return $sql->result_array();
	}
	
	
	function get_metal_process($id_old_metal_process)
	{
	    $sql=$this->db->query("SELECT p.id_old_metal_process,p.id_metal_process,p.process_no,p.process_for,if(p.process_for=1,'ISSUE','RECEIPT') as process,m.process_name,k.firstname as karigar_name,date_format(p.created_on,'%d-%m-%Y') as date_add,emp.firstname as emp_name,
        cy.name as country_name,st.name as state_name,ct.name as city_name,k.pincode,IFNULL(k.address1,'') as address1,IFNULL(k.address2,'') as address2,
        IFNULL(k.address3,'') as address3,p.remark
        FROM ret_old_metal_process p
        LEFT JOIN ret_old_metal_process_master m ON m.id_metal_process=p.id_metal_process 
        LEFT JOIN ret_karigar k ON k.id_karigar=p.id_karigar 
        LEFT JOIN country cy ON cy.id_country=k.id_country
        LEFT JOIN state st ON st.id_state=k.id_state
        LEFT JOIN city ct ON ct.id_city=k.id_city
        LEFT JOIN employee emp ON emp.id_employee=p.created_by 
        where id_old_metal_process=".$id_old_metal_process."");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
	}
	
	function get_melting_issue_details($id_old_metal_process)
	{
	    $sql=$this->db->query("SELECT p.pocket_no,d.issue_gwt as gross_wt,d.issue_nwt as net_wt,d.issue_purity as avg_purity,
        (d.issue_nwt*d.issue_rate) as amount,mt.metal_type as old_metal_cat
        FROM ret_old_metal_melting_details d 
        LEFT JOIN ret_old_metal_melting m ON m.id_melting=d.id_melting
        LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket=d.id_pocket
        LEFT JOIN ret_old_metal_type mt on mt.id_metal_type = d.id_metal_type
        WHERE m.id_old_metal_process=".$id_old_metal_process."");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	
	
	function get_melting_receipt_details($id_old_metal_process)
	{
	    $sql=$this->db->query("SELECT p.process_no,m.gross_wt,m.net_wt,d.received_wt,m.received_less_wt,m.receipt_charges,d.product_name
        FROM ret_old_metal_melting m 
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=m.id_old_metal_process
        LEFT JOIN (SELECT IFNULL(SUM(d.received_wt),0) as received_wt,d.id_melting,prod.product_name
        FROM ret_old_metal_melting_recd_details d
        LEFT JOIN ret_product_master prod ON prod.pro_id = d.id_product
        GROUP by d.id_melting) as d ON d.id_melting=m.id_melting
        WHERE m.id_old_metal_process_receipt=".$id_old_metal_process."");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	
	function get_old_metal_process_payment($id_old_metal_process)
	{
	    $sql=$this->db->query("SELECT * FROM `ret_old_metal_process_payment` WHERE id_old_metal_process=".$id_old_metal_process."");
	    return $sql->result_array();
	}
	
	function getPocketingDetails($id_metal_pocket)
	{
	    $sql=$this->db->query("SELECT * FROM `ret_old_metal_pocket_details` WHERE id_metal_pocket=".$id_metal_pocket."");
	    return $sql->result_array();
	}
	
	function get_branch_details()
	{
	    $sql=$this->db->query("SELECT b.id_branch,d.entry_date
        FROM ret_day_closing d 
        LEFT JOIN branch b ON b.id_branch=d.id_branch
        WHERE b.is_ho=1");
	    return $sql->row_array();
	}
    
    
    function get_KarigarMeltingIssueDetilas($data)
    {
   
        $sql=$this->db->query("SELECT p.process_no,IFNULL(det.issue_gwt,0) as gross_wt,IFNULL(det.issue_nwt,0) as net_wt,IFNULL(m.amount,0) as amount,IFNULL(det.issue_purity,0) as purity,p.id_old_metal_process,m.id_melting
        FROM ret_old_metal_melting m 
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=m.id_old_metal_process
        LEFT JOIN(SELECT d.id_melting,IFNULL(SUM(d.issue_pcs),0) as issue_pcs,
                  IFNULL(SUM(d.issue_gwt),0) as issue_gwt,IFNULL(SUM(d.issue_nwt),0) as issue_nwt,IFNULL(SUM(d.issue_purity),0) as issue_purity
                 FROM ret_old_metal_melting_details d 
                 GROUP by d.id_melting) as det ON det.id_melting = m.id_melting
        WHERE p.id_karigar=".$data['id_karigar']." AND m.melting_status=0");
        return $sql->result_array();
    }
    

    //Testing Issue
    function get_melting_details()
    {
        $sql=$this->db->query("SELECT m.id_melting,(IFNULL(m.receipt_charges,0)+m.amount) as amount,m.purity,m.rate,m.gross_wt as net_wt,recd.net_wt as rec_weight,
        (IFNULL(det.recevied_wt,0) - IFNULL(recd.net_wt,0)) as received_wt,
        c.name as category_name,p.process_no,p.id_old_metal_process,det.id_melting_recd
        FROM ret_old_metal_melting m 
        
        LEFT JOIN(SELECT d.id_melting_recd,IFNULL(SUM(d.received_wt),0) as recevied_wt,
            d.melting_status,d.id_melting,d.received_category
            FROM ret_old_metal_melting_recd_details d 
         GROUP by d.id_melting_recd) as det ON  det.id_melting=m.id_melting
         
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=m.id_old_metal_process
        LEFT JOIN ret_category c ON c.id_ret_category=det.received_category
        
        LEFT JOIN(SELECT IFNULL(SUM(ot.net_wt),0) as net_wt,ot.id_melting_recd
            FROM ret_old_metal_testing ot
            GROUP by ot.id_melting_recd ) as recd ON recd.id_melting_recd = det.id_melting_recd
        WHERE m.id_melting IS NOT NULL
        GROUP BY det.id_melting_recd
        HAVING received_wt > 0 
        ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }

    function get_testing_issue_details($id_old_metal_process)
    {
        $sql=$this->db->query("SELECT t.net_wt,t.purity,t.amount,p.process_no,d.received_category,
        cat.name as category_name
        FROM ret_old_metal_process p 
        LEFT JOIN ret_old_metal_testing t ON t.id_old_metal_process=p.id_old_metal_process 
        LEFT JOIN ret_old_metal_melting_recd_details d ON d.id_melting_recd=t.id_melting_recd
        LEFT JOIN ret_category cat ON cat.id_ret_category=d.received_category
        WHERE p.id_old_metal_process=".$id_old_metal_process."");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    
    function get_TestingReceiptAcknowladgement($id_old_metal_process)
    {
        $sql=$this->db->query("SELECT p.process_no,t.net_wt,t.amount,t.purity,t.received_wt,t.production_loss,t.received_purity,t.receipt_charges
        FROM ret_old_metal_testing t 
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=t.id_old_metal_process_receipt
        WHERE t.id_old_metal_process_receipt=".$id_old_metal_process."");
        return $sql->result_array();
    }
    
    function get_testing_receipt_details($data)
    {
        $sql=$this->db->query("SELECT t.id_melting_recd,t.net_wt,t.amount,t.purity,c.name as category_name,p.process_no,m.id_melting,t.id_metal_testing,IFNULL(d.received_category,'') as received_category,
        IFNULL(d.id_product,'') as id_product,IFNULL(d.id_design,'') as id_design,IFNULL(d.id_sub_design,'') as id_sub_design,IFNULL(d.recd_pcs,'') as recd_pcs,
        IFNULL(d.id_section,'') as id_section
        FROM ret_old_metal_testing t
        LEFT JOIN ret_old_metal_melting_recd_details d on d.id_melting_recd=t.id_melting_recd
        LEFT JOIN ret_old_metal_melting m on m.id_melting=d.id_melting
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=t.id_old_metal_process
        LEFT JOIN ret_category c ON c.id_ret_category=d.received_category
        WHERE t.testing_status = 0 AND p.id_karigar=".$data['id_karigar']."");
        return $sql->result_array();
    }
    
    function check_purity_stock_details($purity,$id_ret_category,$id_product,$id_branch)
    {
        $sql=$this->db->query("SELECT * FROM `ret_purchase_item_stock_summary` WHERE id_branch=".$id_branch." 
        AND id_ret_category=".$id_ret_category." 
        AND id_product=".$id_product." 
        AND purity=".$purity."");
        
        $res = $this->db->query($sql);
        
		if($res->num_rows() > 0){
			$r = array("status" => TRUE,'id_stock_summary'=>$res->row()->id_stock_summary); 
		}else{
			$r = array("status" => FALSE); 
		} 
		return $r;
		
    }
    
    
    
    function updateStockItemData($id_stock_summary,$data,$arith){ 
		$sql = "UPDATE ret_purchase_item_stock_summary SET gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['gross_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' 
		WHERE id_stock_summary=".$id_stock_summary."  ";  
		$status = $this->db->query($sql);
		return $status;
	}
    
    //Testing Issue
    
    
    //refining issue

    function get_RefiningIssueDetails()
    {
        $sql=$this->db->query("SELECT p.process_no,r.id_melting_recd,c.name as category_name,(r.testing_completed_wt-IFNULL(ref_iss.issue_weight,0)) as received_wt,r.tested_purity as purity,(IFNULL(t.receipt_charges,0)+IFNULL(m.receipt_charges,0)+m.amount) as amount
        FROM ret_old_metal_melting_recd_details r 
        LEFT JOIN ret_category c ON c.id_ret_category = r.received_category
        LEFT JOIN ret_old_metal_melting m ON m.id_melting = r.id_melting
        LEFT JOIN ret_old_metal_testing t ON t.id_melting_recd = r.id_melting_recd
        LEFT JOIN (select r.id_melting_recd,IFNULL(SUM(r.issue_weight),0) as issue_weight
                from ret_old_metal_refining r 
                group by r.id_melting_recd) as ref_iss ON ref_iss.id_melting_recd = r.id_melting_recd
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = m.id_old_metal_process
        WHERE r.melting_status = 3
        having received_wt > 0");
        return $sql->result_array();
    }

    function get_RefiningReceiptDetails($data)
    {
        $sql=$this->db->query("SELECT p.process_no,c.name as category_name,r.issue_weight as net_wt,d.tested_purity as purity,p.id_old_metal_process,(IFNULL(t.receipt_charges,0)+IFNULL(m.receipt_charges,0)+m.amount) as amount,
        m.id_melting,r.id_metal_refining,d.id_melting_recd
        FROM ret_old_metal_refining r 
        LEFT JOIN ret_old_metal_melting_recd_details d ON d.id_melting_recd=r.id_melting_recd
        LEFT JOIN ret_old_metal_melting m ON m.id_melting=d.id_melting
        LEFT JOIN ret_old_metal_testing t ON t.id_melting_recd = r.id_melting_recd
        LEFT JOIN ret_category c ON c.id_ret_category=d.received_category
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=r.id_old_metal_process
        WHERE p.process_for=1 AND r.refining_status =0 AND p.id_karigar=".$data['id_karigar']."");
        //print_r($this->db->last_query());exit;
         return $sql->result_array();
    }

    function get_RefiningIssueAcknowladgement($id_old_metal_process)
    {
        $sql=$this->db->query("SELECT p.process_no,c.name as category_name,t.received_wt,t.received_purity,p.id_old_metal_process,t.received_purity as purity,t.amount
        FROM ret_old_metal_process p 
        LEFT JOIN ret_old_metal_refining r ON r.id_old_metal_process=p.id_old_metal_process
        LEFT JOIN ret_old_metal_testing t ON t.id_melting_recd = r.id_melting_recd
        LEFT JOIN ret_old_metal_melting m ON m.id_melting=r.id_melting
        LEFT JOIN ret_category c ON c.id_ret_category=m.received_category
        WHERE p.process_for=1 AND p.id_metal_process=3
        and p.id_old_metal_process=".$id_old_metal_process."");
       // print_r($this->db->last_query());exit;
        return $sql->result_array();
        
    }
    
    function get_refiningReceiptAcknowladgement($id_old_metal_process)
    {
        $sql=$this->db->query("SELECT p.process_no,d.received_wt,d.purity,cat.name as category_name,r.receipt_charges,r.receipt_charges_tax_amt,r.receipt_charges_tax_igst,r.receipt_charges_tax_cgst,r.receipt_charges_tax_sgst,r.receipt_charges_tax_perc
        FROM ret_old_metal_process p 
        LEFT JOIN ret_old_metal_refining r ON r.id_old_metal_process_receipt=p.id_old_metal_process
        LEFT JOIN ret_old_metal_refining_details d ON d.id_metal_refining = r.id_metal_refining
        LEFT JOIN ret_category cat ON cat.id_ret_category=d.received_category
      WHERE p.id_old_metal_process=".$id_old_metal_process."");
      //print_r($this->db->last_query());exit;
      return $sql->result_array();
    }
    
    
    function get_PolishingReceiptDetails($data)
    {
        $sql=$this->db->query("SELECT r.id_polishing,r.id_old_metal_process,p.process_no,d.issue_gwt as gross_wt,d.issue_nwt as net_wt,IFNULL(d.issue_pcs,0) as no_of_piece,t.metal_type as old_metal_type,
        d.id_polishing_details,id_old_metal_type,d.issue_diawt
		FROM ret_old_metal_polishing_details d 
        LEFT JOIN ret_old_metal_polishing r ON r.id_polishing = d.id_polishing
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process=r.id_old_metal_process
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_old_metal_type
        WHERE d.status=0 AND p.id_karigar=".$data['id_karigar']."");
        //print_r($this->db->last_query());exit;
         return $sql->result_array();
    }
    
    function get_PolishingIssueAcknowladgement($id_old_metal_process)
    {
        $sql=$this->db->query("SELECT d.issue_pcs,d.issue_gwt,d.issue_nwt,d.issue_purity,r.process_no,pok.pocket_no
        FROM ret_old_metal_polishing_details d 
        LEFT JOIN ret_old_metal_polishing p on p.id_polishing=d.id_polishing
        LEFT JOIN ret_old_metal_process r ON r.id_old_metal_process=p.id_old_metal_process
        LEFT JOIN ret_old_metal_pocket pok ON pok.id_metal_pocket=d.id_pocket
        WHERE r.id_old_metal_process=".$id_old_metal_process."");
       // print_r($this->db->last_query());exit;
        return $sql->result_array();
        
    }
    
    function get_PolishingReceiptAcknowladgement($id_old_metal_process)
    {
        $sql=$this->db->query("
        SELECT SUM(d.gross_wt) as received_gwt,SUM(d.net_wt) as received_nwt,c.name as category_name,
        p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,r.process_name
        FROM ret_lot_inwards_detail d 
        LEFT JOIN ret_lot_inwards l ON l.lot_no =d.lot_no
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = l.id_metal_process
        LEFT JOIN ret_category c ON c.id_ret_category = l.id_category
        LEFT JOIN ret_old_metal_process_master r ON r.id_metal_process = p.id_metal_process
        WHERE l.id_metal_process = ".$id_old_metal_process."
        GROUP BY l.id_category");
       // print_r($this->db->last_query());exit;
        return $sql->result_array();
        
    }
    
    
    function checkPurchaseItemStockExist($id_product,$id_branch,$purity)
    {
        $sql=$this->db->query("SELECT * FROM `ret_purchase_item_stock_summary` WHERE id_branch=".$id_branch." AND id_product=".$id_product." AND purity=".$purity ."");
        if($sql->num_rows() > 0)
        {
            return TRUE;
        }else
        {
            return FALSE;
        }
    }
    
    function updatePurItemData($id_stock_summary,$data,$arith){ 
		$sql = "UPDATE ret_purchase_item_stock_summary SET gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['gross_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_stock_summary=".$id_stock_summary." ";  
		$status = $this->db->query($sql);
		return $status;
	}
    
    //refining issue
    
    
    
    //Polishing
    
    
    function updatePocketItem($id_metal_pocket,$data,$arith){ 
		$sql = "UPDATE ret_old_metal_pocket SET issue_gwt=(issue_gwt".$arith." ".$data['issue_gwt']."),issue_nwt=(issue_nwt".$arith." ".$data['issue_nwt']."),issue_purity=(issue_purity".$arith." ".$data['issue_purity']."),issue_pcs=(issue_pcs".$arith." ".$data['issue_pcs']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_metal_pocket=".$id_metal_pocket." ";  
		$status = $this->db->query($sql);
		return $status;
	}

    //Polishing
    
    function get_ActiveCategoryPurity()
    {
        $sql=$this->db->query("SELECT p.purity,p.id_purity,c.id_category
        FROM ret_metal_cat_purity c 
        LEFT JOIN ret_purity p ON p.id_purity=c.id_purity
        WHERE c.id_category IS NOT NULL AND p.id_purity IS NOT NULL");
        return $sql->result_array();
    }
    
    function getCategoryDetails($id_ret_category)
    {
        $sql=$this->db->query("SELECT * FROM ret_category WHERE id_ret_category=".$id_ret_category."");
        return $sql->row_array();
    }
    
    
    
    function checkStoneItemStockExist($id_category,$id_product,$id_branch)
    {
        $sql=$this->db->query("SELECT * FROM `ret_purchase_item_stock_summary` WHERE id_branch=".$id_branch." AND id_product=".$id_product." and id_ret_category=".$id_category." ");
        if($sql->num_rows() > 0)
        {
            return TRUE;
        }else
        {
            return FALSE;
        }
    }
    
    function updateStoneItemData($data,$arith){ 
		$sql = "UPDATE ret_purchase_item_stock_summary SET gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['gross_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_ret_category=".$data['id_ret_category']." and id_branch=".$data['id_product']." and id_product=".$data['id_product']."  ";  
		$status = $this->db->query($sql);
		return $status;
	}
	
	
	//Reports
	function get_metal_process_reports($data)
	{
	    //Issue
	    if($data['id_metal_process']==1) //Melting Issue
	    {
	        if($data['process_for']==1)
	        {
	            $sql = $this->db->query("SELECT p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,
                k.firstname as karigar_name,d.issue_pcs,d.issue_gwt as gross_wt,d.issue_nwt as net_wt,t.metal_type as item_name,r.process_name,
                '0' as diawt
                FROM ret_old_metal_melting_details d 
                LEFT JOIN ret_old_metal_melting m ON m.id_melting = d.id_melting
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = m.id_old_metal_process
                LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_metal_type
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                LEFT JOIN ret_old_metal_process_master r ON r.id_metal_process = p.id_metal_process
                Where p.process_for = 1 AND p.id_metal_process =".$data['id_metal_process']."
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                ".($data['id_metal_type']!='' ? " and d.id_metal_type=".$data['id_metal_type']."" :'')."
                
                ");
	        }else
	        {
	            $sql = $this->db->query("SELECT p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,
                k.firstname as karigar_name,c.name as item_name,d.received_wt as gross_wt,d.received_wt as net_wt,r.process_name,
                '0' as diawt
                FROM ret_old_metal_melting_recd_details d 
                LEFT JOIN ret_old_metal_melting m ON m.id_melting = d.id_melting
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = m.id_old_metal_process_receipt
                LEFT JOIN ret_category c ON c.id_ret_category = d.received_category
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                LEFT JOIN ret_old_metal_process_master r ON r.id_metal_process = p.id_metal_process
                Where p.process_for = 2 AND p.id_metal_process =".$data['id_metal_process']."
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                ".($data['id_category']!='' ? " and d.received_category=".$data['id_category']."" :'')."
                ");
	        }
	        
	    }
	    else if($data['id_metal_process']==2) //Testing
	    {
	        if($data['process_for']==1)
	        {
	            $sql = $this->db->query("SELECT p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,
                k.firstname as karigar_name,c.name as item_name,r.process_name,t.net_wt as gross_wt,t.net_wt as net_wt,'0' as diawt
                FROM ret_old_metal_testing t 
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = t.id_old_metal_process
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                LEFT JOIN ret_old_metal_melting_recd_details d ON d.id_melting_recd = t.id_melting_recd
                LEFT JOIN ret_category c ON c.id_ret_category = d.received_category
                LEFT JOIN ret_old_metal_process_master r ON r.id_metal_process = p.id_metal_process
                Where p.process_for = 1 AND p.id_metal_process =".$data['id_metal_process']."
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                ");
	        }else
	        {
	            $sql = $this->db->query("SELECT p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,
                k.firstname as karigar_name,c.name as item_name,r.process_name,t.received_wt as gross_wt,t.received_wt as net_wt,'0' as diawt
                FROM ret_old_metal_testing t 
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = t.id_old_metal_process_receipt
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                LEFT JOIN ret_old_metal_melting_recd_details d ON d.id_melting_recd = t.id_melting_recd
                LEFT JOIN ret_category c ON c.id_ret_category = d.received_category
                LEFT JOIN ret_old_metal_process_master r ON r.id_metal_process = p.id_metal_process
                Where p.process_for = 2 AND p.id_metal_process =".$data['id_metal_process']."
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                ".($data['id_category']!='' ? " and d.received_category=".$data['id_category']."" :'')."
                ");
	        }
            
	    }
	    else if($data['id_metal_process']==3) //Refining
	    {
	        if($data['process_for']==1)
	        {
	            $sql = $this->db->query("SELECT p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,
                k.firstname as karigar_name,c.name as item_name,m.process_name,r.issue_weight as gross_wt,r.issue_weight as net_wt,'0' as diawt
                FROM ret_old_metal_refining r 
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = r.id_old_metal_process
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                LEFT JOIN ret_old_metal_melting_recd_details d ON d.id_melting_recd = r.id_melting_recd
                LEFT JOIN ret_category c ON c.id_ret_category = d.received_category
                LEFT JOIN ret_old_metal_process_master m ON m.id_metal_process = p.id_metal_process
                Where p.process_for = 1 AND p.id_metal_process =".$data['id_metal_process']."
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                ");
	        }else
	        {
	            $sql = $this->db->query("SELECT p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,
                k.firstname as karigar_name,c.name as item_name,m.process_name,r.received_wt as gross_wt,r.received_wt as net_wt,'0' as diawt
                FROM ret_old_metal_refining_details r 
                LEFT JOIN ret_old_metal_refining rf ON rf.id_metal_refining = r.id_metal_refining
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = rf.id_old_metal_process_receipt
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                LEFT JOIN ret_category c ON c.id_ret_category = r.received_category
                LEFT JOIN ret_old_metal_process_master m ON m.id_metal_process = p.id_metal_process
                Where p.process_for = 2 AND p.id_metal_process =".$data['id_metal_process']."
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                ".($data['id_category']!='' ? " and d.received_category=".$data['id_category']."" :'')."
                ");
                
	        }
            
	    }
	    else if($data['id_metal_process']==4) //Polishing
	    {
	         if($data['process_for']==1)
	         {
                $sql = $this->db->query("SELECT p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,
                k.firstname as karigar_name,d.id_old_metal_type,t.metal_type as item_name,d.issue_pcs as pcs,d.issue_gwt as gross_wt,d.issue_nwt as net_wt,
                r.process_name,d.issue_diawt as diawt
                FROM ret_old_metal_polishing_details d 
                LEFT JOIN ret_old_metal_polishing pl ON pl.id_polishing = d.id_polishing
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = pl.id_old_metal_process
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_old_metal_type
                LEFT JOIN ret_old_metal_process_master r ON r.id_metal_process = p.id_metal_process
                Where p.process_for = 1 AND p.id_metal_process =".$data['id_metal_process']." 
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                ".($data['id_metal_type']!='' ? " and d.id_old_metal_type=".$data['id_metal_type']."" :'')."
                ");
	        }
	        else
	        {
	            /*$sql = $this->db->query("
                SELECT SUM(d.gross_wt) as gross_wt,SUM(d.net_wt) as net_wt,k.firstname as karigar_name,c.name as item_name,
                p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,r.process_name,'0' as diawt
                FROM ret_lot_inwards_detail d 
                LEFT JOIN ret_lot_inwards l ON l.lot_no =d.lot_no
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = l.id_metal_process
                LEFT JOIN ret_category c ON c.id_ret_category = l.id_category
                LEFT JOIN ret_old_metal_process_master r ON r.id_metal_process = p.id_metal_process
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                WHERE p.id_metal_process =  ".$data['id_metal_process']."
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                GROUP BY l.id_category,l.id_metal_process");*/
                
                $sql = $this->db->query("
                SELECT SUM(d.gross_wt) as gross_wt,SUM(d.net_wt) as net_wt,k.firstname as karigar_name,c.name as item_name,
                p.id_old_metal_process,p.process_no,date_format(p.date_add,'%d-%m-%Y') as date_add,r.process_name,IFNULL(SUM(d.dia_wt),0) as diawt
                FROM ret_old_metal_polishing_recd_details d
                LEFT JOIN ret_old_metal_polishing pl ON pl.id_polishing = d.id_polishing
                LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = pl.id_old_metal_process_receipt
                LEFT JOIN ret_category c ON c.id_ret_category = d.id_category
                LEFT JOIN ret_old_metal_process_master r ON r.id_metal_process = p.id_metal_process
                LEFT JOIN ret_karigar k ON k.id_karigar = p.id_karigar
                WHERE p.id_metal_process =  ".$data['id_metal_process']."
                AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
                ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
                ".($data['id_category']!='' ? " and d.id_category=".$data['id_category']."" :'')."
                GROUP BY pl.id_old_metal_process_receipt,d.id_category");
                //print_r($this->db->last_query());exit;
	        }
	        //print_r($this->db->last_query());exit;
	    }
	    
	    return $sql->result_array();
	    
	}
	
	/*function get_metal_process_reports($data)
	{
	    $returnData=array();
	    $sql=$this->db->query("SELECT p.process_no,m.process_name,p.id_old_metal_process,p.process_for,m.process_code,k.firstname as karigar_name,date_format(p.date_add,'%d-%m-%Y') as issue_date
        FROM ret_old_metal_process p 
        LEFT JOIN ret_karigar k ON k.id_karigar=p.id_karigar
        LEFT JOIN ret_old_metal_process_master m ON m.id_metal_process=p.id_metal_process
        WHERE p.process_for=".$data['process_for']."
        AND (date(p.date_add) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."')
        ".($data['id_karigar']!='' ? " and p.id_karigar=".$data['id_karigar']."" :'')."
        ".($data['id_metal_process']!='' ? " and p.id_metal_process=".$data['id_metal_process']."" :'')."
        ");
        
        $result=$sql->result_array();
        foreach($result as $items)
        {
            $process_details=[];
            if($items['process_code']=='MELTING')
            {
                $process_details = $this->get_melting_process_details($items['id_old_metal_process']);
            }
            else if($items['process_code']=='TESTING')
            {
                $process_details = $this->get_testing_process_details($items['id_old_metal_process']);
            }
            else if($items['process_code']=='REFINING')
            {
                $process_details = $this->get_refining_process_details($items['id_old_metal_process']);
            }
            else if($items['process_code']=='POLISHING')
            {
                $process_details = $this->get_polishing_process_details($items['id_old_metal_process']);
            }
            $returnData[]=array(
                               'id_old_metal_process'   =>$items['id_old_metal_process'],
                               'issue_date'             =>$items['issue_date'],
                               'karigar_name'           =>$items['karigar_name'],
                               'process_no'             =>$items['process_no'],
                               'process_name'           =>$items['process_name'],
                               'process_for'            =>$items['process_for'],
                               'process_code'           =>$items['process_code'],
                               'issue_wt'               =>$process_details['issue_nwt'],
                               'received_wt'            =>$process_details['received_wt'],
                               'process_status'         =>$process_details['process_status'],
                               );
        }
        
        return $returnData;
	}*/
	
	function get_refining_process_details($id_old_metal_process)
	{
	    $sql=$this->db->query("SELECT t.received_wt as issue_nwt,if(r.refining_status=0,'Refining Issue','Refining Completed') as process_status,IFNULL(recd.received_wt,0) as received_wt
        FROM ret_old_metal_process p 
        LEFT JOIN ret_old_metal_refining r ON r.id_old_metal_process=p.id_old_metal_process
        LEFT JOIN ret_old_metal_testing t ON t.id_metal_testing=r.id_metal_testing
        LEFT JOIN(SELECT d.id_metal_refining,IFNULL(SUM(d.received_wt),0) as received_wt
        FROM ret_old_metal_refining_details d 
        GROUP by d.id_metal_refining) as recd ON recd.id_metal_refining=r.id_metal_refining");
        return $sql->row_array();
	}
	
	function get_polishing_process_details($id_old_metal_process)
	{
	    $sql=$this->db->query("SELECT d.issue_nwt as issue_nwt,d.received_nwt as received_wt,if(d.status=0,'Issue','Completed') as process_status 
	    FROM ret_old_metal_polishing_details d  
        LEFT JOIN ret_old_metal_polishing p ON p.id_polishing =d.id_polishing 
	    WHERE id_old_metal_process = ".$id_old_metal_process."");
        return $sql->row_array();
	}
	
	
	function get_testing_process_details($id_old_metal_process)
	{
	    $sql=$this->db->query("SELECT t.net_wt as issue_nwt,t.received_wt,IF(t.testing_status=0,'Testing Issue','Testing Completed') as process_status
        FROM ret_old_metal_process p
        LEFT JOIN ret_old_metal_testing t ON t.id_old_metal_process=p.id_old_metal_process 
        WHERE t.id_old_metal_process=".$id_old_metal_process."");
        return $sql->row_array();
	}
	
	
	function get_melting_process_details($id_old_metal_process)
    {
        $sql=$this->db->query("SELECT m.id_old_metal_process,m.gross_wt as issue_nwt,IFNULL(recd.received_wt,0) as received_wt,if(m.melting_status=0,'Melting Issue','Melting Completed') as process_status
        FROM ret_old_metal_melting m 
        LEFT JOIN(SELECT d.id_melting,IFNULL(SUM(d.received_wt),0) as received_wt,
        if(d.melting_status=0,'Melting Issue',if(d.melting_status=1,'Melting Completed',if(d.melting_status=2,'Testing Issue',if(d.melting_status=3,'Testing Completed',if(d.melting_status=4,'Refifing Issue',if(d.melting_status=5,'Refining Completed','')))))) as process_status
        FROM ret_old_metal_melting_recd_details d 
        GROUP by d.id_melting) as recd ON recd.id_melting=m.id_melting
        WHERE m.id_old_metal_process=".$id_old_metal_process."
        ");
        return $sql->row_array();
    }
	
	//Reports
	
	function checkNonTagItemExist($data){
		$r = array("status" => FALSE);
		
		$id_design = (isset($data['design']) ? ($data['design']!='' ? $data['design'] :'') :'');
		$id_sub_design = (isset($data['id_sub_design']) ? ($data['id_sub_design']!='' ? $data['id_sub_design'] :'') :'');
		$id_section = (isset($data['id_section']) ? ($data['id_section']!='' ? $data['id_section'] :'') :'');
        $sql = "SELECT id_nontag_item FROM ret_nontag_item WHERE 
        product=".$data['id_product']." 
        ".($id_section!='' ? " and id_section=".$id_section."" :'')."
        ".($id_design!='' ? " and design=".$id_design."" :'')."
        ".($id_sub_design!='' ? " and id_sub_design=".$id_sub_design."" :'')."
        AND branch=".$data['id_branch']; 
        //print_r($sql);exit;
        $res = $this->db->query($sql);
		if($res->num_rows() > 0){
			$r = array("status" => true, "id_nontag_item" => $res->row()->id_nontag_item); 
		}else{
			$r = array("status" => false, "id_nontag_item" => ""); 
		} 
		return $r;
	}

	function updateNTData($data,$arith){ 
		$sql = "UPDATE ret_nontag_item SET no_of_piece=(no_of_piece".$arith." ".(isset($data['no_of_piece']) ? $data['no_of_piece']:0)."),gross_wt=(gross_wt".$arith." ".$data['gross_wt']."),net_wt=(net_wt".$arith." ".$data['net_wt']."),updated_by=".$data['updated_by'].",updated_on='".$data['updated_on']."' WHERE id_nontag_item=".$data['id_nontag_item'];  
		$status = $this->db->query($sql);
		return $status;
	}
	
	
	function get_detail_porcess_report($data)
	{
	     $returnData = [];
	    $sql=$this->db->query("SELECT p.id_metal_pocket,p.pocket_no,IFNULL(p.piece,0) as piece,IFNULL(p.gross_wt,0) as gross_wt,IFNULL(p.net_wt,0) as net_wt,
        date_format(p.date,'%d-%m-%Y') as date_add,p.trans_type
        FROM ret_old_metal_pocket p 
        LEFT JOIN ret_old_metal_pocket_details d ON d.id_metal_pocket = p.id_metal_pocket
        LEFT JOIN ret_bill_old_metal_sale_details s ON s.old_metal_sale_id = d.old_metal_sale_id
        LEFT JOIN ret_billing b ON b.bill_id = s.bill_id
        WHERE date(p.date) BETWEEN '".date('Y-m-d',strtotime($data['from_date']))."' AND '".date('Y-m-d',strtotime($data['to_date']))."'
        group by p.id_metal_pocket");
        //print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        foreach($result as $items)
        { 
            $items['melting_details'] = $this->get_pocket_melting_details($items['id_metal_pocket'],$items['trans_type']);
            $items['polish_details'] = $this->get_pocket_polish_details($items['id_metal_pocket']);
            $items['testing_details'] = $this->get_pocket_testing_details($items['id_metal_pocket']);
            $returnData[]= $items;
        }
        
        return $returnData;
	}
	
	function get_pocket_melting_details($id_metal_pocket,$trans_type)
	{
	    $returnData = array();
	    if($trans_type==1)
	    {
	        $sql=$this->db->query("SELECT d.id_pocket,d.issue_pcs,d.issue_gwt,d.issue_nwt,p.process_no,IFNULL(rcd.received_wt,0) as received_wt,
    	    pm.process_name,date_format(p.date_add,'%d-%m-%Y') as date_add,p.id_old_metal_process,t.metal_type,m.id_melting
            FROM ret_old_metal_melting m 
            LEFT JOIN ret_old_metal_melting_details d ON d.id_melting = m.id_melting
            LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = m.id_old_metal_process
            LEFT JOIN ret_old_metal_process_master pm ON pm.id_metal_process = p.id_metal_process
            LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_metal_type
            LEFT JOIN(SELECT IFNULL(SUM(rcd.received_wt),0) as received_wt,dt.id_pocket
                     FROM ret_old_metal_melting_recd_details rcd 
                     LEFT JOIN ret_old_metal_melting_details dt ON dt.id_melting = rcd.id_melting
                     GROUP by dt.id_pocket) as rcd ON rcd.id_pocket = d.id_pocket
            WHERE d.id_pocket =".$id_metal_pocket."");
	    }
	    else if($trans_type==2){
	        $sql=$this->db->query("SELECT d.id_pocket,IFNULL(sum(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_gwt),0) as issue_gwt,
	        d.issue_nwt,p.process_no,IFNULL(rcd.received_wt,0) as received_wt,
    	    pm.process_name,date_format(p.date_add,'%d-%m-%Y') as date_add,p.id_old_metal_process,'Tagged Items' as metal_type,m.id_melting
            FROM ret_old_metal_melting m 
            LEFT JOIN ret_old_metal_melting_details d ON d.id_melting = m.id_melting
            LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = m.id_old_metal_process
            LEFT JOIN ret_old_metal_process_master pm ON pm.id_metal_process = p.id_metal_process
            LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_metal_type
            LEFT JOIN(SELECT IFNULL(SUM(rcd.received_wt),0) as received_wt,dt.id_pocket
                     FROM ret_old_metal_melting_recd_details rcd 
                     LEFT JOIN ret_old_metal_melting_details dt ON dt.id_melting = rcd.id_melting
                     GROUP by dt.id_pocket) as rcd ON rcd.id_pocket = d.id_pocket
            WHERE d.id_pocket =".$id_metal_pocket."");
            //print_r($this->db->last_query());exit;
	    }
	    else if($trans_type==3){
	        $sql=$this->db->query("SELECT d.id_pocket,IFNULL(sum(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_gwt),0) as issue_gwt,
	        d.issue_nwt,p.process_no,IFNULL(rcd.received_wt,0) as received_wt,
    	    pm.process_name,date_format(p.date_add,'%d-%m-%Y') as date_add,p.id_old_metal_process,'Non Tagged Items' as metal_type,m.id_melting
            FROM ret_old_metal_melting m 
            LEFT JOIN ret_old_metal_melting_details d ON d.id_melting = m.id_melting
            LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = m.id_old_metal_process
            LEFT JOIN ret_old_metal_process_master pm ON pm.id_metal_process = p.id_metal_process
            LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_metal_type
            LEFT JOIN(SELECT IFNULL(SUM(rcd.received_wt),0) as received_wt,dt.id_pocket
                     FROM ret_old_metal_melting_recd_details rcd 
                     LEFT JOIN ret_old_metal_melting_details dt ON dt.id_melting = rcd.id_melting
                     GROUP by dt.id_pocket) as rcd ON rcd.id_pocket = d.id_pocket
            WHERE d.id_pocket =".$id_metal_pocket."");
            //print_r($this->db->last_query());exit;
	    }
	    
        $result = $sql->result_array();
        foreach($result as $items)
        {
            $items['recd_details'] = ($items['id_melting']!='' && $items['id_melting']!=null ? $this->get_melting_recd_details($items['id_melting']):[]);
            $returnData[]=$items;
        }
        return $returnData;
	}
	
	function get_melting_recd_details($id_melting)
	{
	    $sql = $this->db->query("SELECT IFNULL(SUM(rcd.received_wt),0) as received_wt,c.name as category_name
                 FROM ret_old_metal_melting_recd_details rcd
                 LEFT JOIN ret_category c on c.id_ret_category = rcd.received_category
                 where rcd.id_melting = ".$id_melting."
                 GROUP by rcd.received_category");
                 
        return $sql->result_array();
	}
	
	function get_pocket_polish_details($id_metal_pocket)
	{
	    $returnData = [];
	    /*$sql=$this->db->query("SELECT p.pocket_no,p.id_metal_pocket,IFNULL(pl.process_no,'') as process_no,IFNULL(pl.issue_pcs,0) as issue_pcs,
        IFNULL(pl.issue_gwt,0) as issue_gwt,IFNULL(pl.issue_nwt,0) as issue_nwt,pl.process_name,IFNULL(pl.received_gwt,0) as received_gwt,
        IFNULL(pl.received_nwt,0) as received_nwt,IFNULL(pl.received_pcs,0) as received_pcs,pl.date_add,pl.id_old_metal_process
        FROM ret_old_metal_pocket p 
        LEFT JOIN(SELECT IFNULL(SUM(d.issue_pcs),0) as issue_pcs,IFNULL(SUM(d.issue_gwt),0) as issue_gwt,IFNULL(SUM(d.issue_nwt),0) as issue_nwt,d.id_pocket,
        pr.process_no,m.process_name,IFNULL(SUM(d.received_gwt),0) as received_gwt,IFNULL(SUM(d.received_nwt),0) as received_nwt,
        IFNULL(SUM(d.received_pcs),0) as received_pcs,date_format(pr.date_add,'%d-%m-%Y') as date_add,pr.id_old_metal_process
        FROM ret_old_metal_polishing_details d 
        LEFT JOIN ret_old_metal_polishing l ON l.id_polishing = d.id_polishing
        LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_pocket
        LEFT JOIN ret_old_metal_process pr ON pr.id_old_metal_process = l.id_old_metal_process
        LEFT JOIN ret_old_metal_process_master m ON m.id_metal_process = pr.id_metal_process
        GROUP by d.id_pocket) as pl ON pl.id_pocket = p.id_metal_pocket
        where p.id_metal_pocket= ".$id_metal_pocket." ");*/
        
        $sql=$this->db->query("
        SELECT p.pocket_no,IFNULL((d.issue_pcs),0) as issue_pcs,IFNULL((d.issue_gwt),0) as issue_gwt,IFNULL((d.issue_nwt),0) as issue_nwt,d.id_pocket,
        pr.process_no,m.process_name,IFNULL((d.received_gwt),0) as received_gwt,IFNULL((d.received_nwt),0) as received_nwt,
        IFNULL((d.received_pcs),0) as received_pcs,date_format(pr.date_add,'%d-%m-%Y') as date_add,pr.id_old_metal_process,t.metal_type
        FROM ret_old_metal_polishing_details d 
        LEFT JOIN ret_old_metal_polishing l ON l.id_polishing = d.id_polishing
        LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_pocket
        LEFT JOIN ret_old_metal_process pr ON pr.id_old_metal_process = l.id_old_metal_process
        LEFT JOIN ret_old_metal_process_master m ON m.id_metal_process = pr.id_metal_process
        LEFT JOIN ret_old_metal_type t ON t.id_metal_type = d.id_old_metal_type
        where p.id_metal_pocket= ".$id_metal_pocket."");
        
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
        
	}
	
	
	function get_pocket_testing_details($id_metal_pocket)
	{
	    $sql=$this->db->query("SELECT p.id_old_metal_process,p.process_no,p.id_old_metal_process,t.net_wt as issue_nwt,t.received_wt,pm.process_name,date_format(p.date_add,'%d-%m-%Y') as date_add,dt.id_pocket
        FROM ret_old_metal_testing t 
        LEFT JOIN ret_old_metal_melting_recd_details d ON d.id_melting_recd = t.id_melting_recd
        LEFT JOIN ret_old_metal_melting_details dt ON dt.id_melting = d.id_melting
        LEFT JOIN ret_old_metal_melting m ON m.id_melting = d.id_melting
        LEFT JOIN ret_old_metal_process p ON p.id_old_metal_process = t.id_old_metal_process
        LEFT JOIN ret_old_metal_process_master pm ON pm.id_metal_process = p.id_metal_process
        WHERE t.id_metal_testing IS NOT NULL and dt.id_pocket = ".$id_metal_pocket." ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
	}
	
	function get_active_design_products()
	{
	    $sql = $this->db->query("SELECT des.design_no,des.design_name,p.pro_id
        FROM ret_product_mapping p 
        LEFT JOIN ret_design_master des ON des.design_no = p.id_design
        WHERE p.id_design IS NOT NULL");
        return $sql->result_array();
	}
	
	function get_active_sub_design_products()
	{
	    $sql = $this->db->query("SELECT m.id_sub_design,m.sub_design_name,s.id_sub_design_mapping,s.id_product,s.id_design
        FROM ret_sub_design_mapping s 
        LEFT JOIN ret_sub_design_master m ON m.id_sub_design = s.id_sub_design
        LEFT JOIN ret_product_master p ON p.pro_id = s.id_product
        LEFT JOIN ret_design_master d ON d.design_no = s.id_design
        WHERE s.id_sub_design IS NOT NULL
        GROUP BY s.id_product,s.id_design,s.id_sub_design");
        return $sql->result_array();
	}
	
    function get_testing($id_melting_recd)
	{
	    $sql=$this->db->query("SELECT * FROM `ret_old_metal_testing` WHERE id_melting_recd=".$id_melting_recd."");
	    return $sql->result_array();
	}
    function get_refining($id_metal_testing)
	{
	    $sql=$this->db->query("SELECT * FROM `ret_old_metal_refining` WHERE id_metal_testing=".$id_metal_testing."");
	    return $sql->result_array();
	}
    

    function update_test_metalItem($data,$arith){ 
		$sql = "UPDATE ret_old_metal_testing SET 
         net_wt=(net_wt".$arith." ".$data['net_wt']."),
        updated_by=".$data['updated_by'].",
        updated_on='".$data['updated_on']."'
        WHERE id_metal_testing=".$data['id_metal_testing'];  
		$status = $this->db->query($sql);
        // print_r($this->db->last_query());exit;
		return $status;
	}

    function update_refining($data,$arith){ 
		$sql = "UPDATE ret_old_metal_refining SET 
         weight=(weight".$arith." ".$data['weight'].")
        WHERE id_metal_refining=".$data['id_metal_refining'];  
		$status = $this->db->query($sql);
        // print_r($this->db->last_query());exit;
		return $status;
	}

    function get_opening_metal_stock_list($data)
	{

	    $sql = $this->db->query("SELECT blc.id_category as id_metal_type,t.metal_type,IFNULL(SUM(blc.weight),0) as actual_weight,IFNULL(pckt.gross_wt,0) as pocket_weight,(IFNULL(SUM(blc.weight),0)-IFNULL(pckt.gross_wt,0)) as blc_weight

        FROM smith_company_op_balance blc

        LEFT JOIN ret_old_metal_type t ON t.id_metal_type = blc.id_category

        LEFT JOIN (SELECT IFNULL(SUM(d.gross_wt),0) as gross_wt,d.id_metal_type

                  FROM ret_old_metal_pocket_details d 

                  LEFT JOIN ret_old_metal_pocket p ON p.id_metal_pocket = d.id_metal_pocket

                  WHERE p.is_against_opening=1

                  GROUP BY d.id_metal_type) as pckt ON pckt.id_metal_type = blc.id_category

        WHERE blc.stock_type = 1 AND blc.balance_type =1 AND blc.metal_type = 2

        ".($data['id_metal']!='' && $data['id_metal']!=0 ? " and blc.id_metal=".$data['id_metal']."" :'')."

        GROUP BY blc.id_category

        HAVING blc_weight > 0");

        return $sql->result_array();

	}

    function get_karigar_state(){

        $sql = $this->db->query(
            "Select id_state from ret_karigar where id_karigar =".$_POST['karigar'].""
        );

        return $sql->row_array()->id_state;
    }
    function get_company_state($id){

        $sql = $this->db->query(
            "Select id_state from company where id_company =".$id.""
        );

        return $sql->row_array()->id_state;
    }

    function get_tag_details($tag_id)
    {
        $sql = $this->db->query("SELECT IFNULL(id_section,'') as id_section FROM ret_taging where tag_id = ".$tag_id." ");
        return $sql->row()->id_section;
    }

    function get_repair_pending_order(){
        $sql=$this->db->query("SELECT c.id_customerorder,c.pur_no,c.order_type,IFNULL(cusRef.order_type,'') as ref_order_type,
            IFNULL(d.orderno,'') as order_no,d.tag_id,IFNULL(t.tag_code,'') as tag_code
            FROM customerorderdetails d
            LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
            LEFT JOIN customerorder cusRef on cusRef.id_customerorder=c.cus_ord_ref
            LEFT JOIN ret_taging t on t.tag_id=d.tag_id
            WHERE  c.order_status<=3 AND (c.order_type=4) and t.tag_id is not null and t.tag_code is not null and t.tag_status=0
            GROUP BY t.tag_id");

            return $sql->result_array();

        //    $result =  $sql->result_array();

        //    foreach($result as $items)

        //    {
        //        $returnData[]=array(
        //                            'id_customerorder'  =>$items['id_customerorder'],
        //                            'pur_no'            =>$items['pur_no'],
        //                            'order_type'        =>$items['order_type'],
        //                            'ref_order_type'    =>$items['ref_order_type'],
        //                            'order_no'          =>$items['order_no'],
        //                            'order_details'     =>$this->get_purchase_cus_order_details($items['id_customerorder']),
        //                            'item_details'     => ( $this->get_tag_cus_order_details($items['id_customerorder'])),
        //                        );
        //    }
        //    return $returnData;

    }



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


    function get_tag_cus_order_details($id_customerorder)

    {

        $sql=$this->db->query("SELECT tag.tag_id,tag.tag_code,date_format(tag.tag_datetime,'%d-%m-%Y') as tag_date, k.firstname as supplier_name, cat.id_metal,cat.id_ret_category  as cat_id,cat.name  as catname,

        tag.id_section,IFNULL(sec.section_name,'') as section,p.pro_id,des.design_no as des_id,s.id_sub_design,p.product_name,pur.id_purity,pur.purity,

        tag.piece,tag.net_wt as weight,tag.tag_status as tagging_status

        FROM ret_taging tag

        LEFT JOIN customerorderdetails d ON d.id_orderdetails  = tag.id_orderdetails

        LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder

        LEFT JOIN ret_lot_inwards lot ON lot.lot_no  = tag.tag_lot_id

        LEFT JOIN ret_karigar k ON k.id_karigar=lot.gold_smith

        LEFT JOIN ret_purity pur ON pur.id_purity=tag.purity

        LEFT JOIN ret_section sec ON sec.id_section = tag.id_section

        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product

        LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id

        LEFT JOIN ret_design_master des ON des.design_no=d.design_no

        LEFT JOIN ret_sub_design_master s ON s.id_sub_design=d.id_sub_design

        LEFT JOIN ret_weight as wt ON wt.id_weight = d.id_weight_range

        where tag.tag_status = 0 and d.ortertype=4 and tag.tag_id is not null and   d.id_customerorder=".$id_customerorder." GROUP BY d.id_product, d.design_no, d.id_sub_design, id_weight_range,tag.tag_id");

        // print_r($this->db->last_query());exit;

        return $sql->result_array();

    }


    // Old Metal PnL Report - Pocket-level profit/loss from refining
    function get_old_metal_pnl_data($data)
    {
        $from_date = date('Y-m-d', strtotime($data['from_date']));
        $to_date   = date('Y-m-d', strtotime($data['to_date']));
        
        $sql = $this->db->query("
            SELECT 
                p.id_metal_pocket,
                p.pocket_no,
                DATE_FORMAT(p.date,'%d-%m-%Y') as pocket_date,
                IFNULL(p.gross_wt, 0) as total_old_gold_wt,
                IFNULL(p.avg_purity, 0) as avg_purity_pct,
                ROUND(IFNULL(p.gross_wt, 0) * IFNULL(p.avg_purity, 0) / 100, 3) as expected_pure_wt,
                IFNULL(ref_agg.actual_refined_wt, 0) as actual_refined_pure_wt,
                ROUND(IFNULL(ref_agg.actual_refined_wt, 0) - (IFNULL(p.gross_wt, 0) * IFNULL(p.avg_purity, 0) / 100), 3) as difference,
                CASE 
                    WHEN ROUND(IFNULL(ref_agg.actual_refined_wt, 0) - (IFNULL(p.gross_wt, 0) * IFNULL(p.avg_purity, 0) / 100), 3) > 0 THEN 'PROFIT'
                    WHEN ROUND(IFNULL(ref_agg.actual_refined_wt, 0) - (IFNULL(p.gross_wt, 0) * IFNULL(p.avg_purity, 0) / 100), 3) < 0 THEN 'LOSS'
                    ELSE 'BREAK-EVEN'
                END as status
            FROM ret_old_metal_pocket p
            INNER JOIN (
                SELECT 
                    md.id_pocket,
                    SUM(IFNULL(rd.received_wt, 0)) as actual_refined_wt
                FROM ret_old_metal_melting_details md
                LEFT JOIN ret_old_metal_melting_recd_details mrd ON mrd.id_melting = md.id_melting
                LEFT JOIN ret_old_metal_testing t ON t.id_melting_recd = mrd.id_melting_recd AND t.testing_status = 1
                LEFT JOIN ret_old_metal_refining r ON r.id_metal_testing = t.id_metal_testing AND r.refining_status = 1
                LEFT JOIN ret_old_metal_refining_details rd ON rd.id_metal_refining = r.id_metal_refining
                WHERE rd.received_wt IS NOT NULL
                GROUP BY md.id_pocket
            ) as ref_agg ON ref_agg.id_pocket = p.id_metal_pocket
            WHERE p.trans_type = 1
            AND (DATE(p.date) BETWEEN '".$from_date."' AND '".$to_date."')
            GROUP BY p.id_metal_pocket
            ORDER BY p.date DESC
        ");
        
        return $sql->result_array();
    }

}
?>