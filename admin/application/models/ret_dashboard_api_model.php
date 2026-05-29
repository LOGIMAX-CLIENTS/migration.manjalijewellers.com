<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class ret_dashboard_api_model extends CI_Model

{

    const COLOUR_CODE = array(
    '#3366cc', '#109618', '#990099', '#ff9900', '#dc3912',
	'#673AB7', '#F44336', '#009688', '#FF9800', '#3F51B5',
	'#FFEB3B', '#795548', '#9C27B0', '#FF5722', '#607D8B',
	'#00BCD4', '#8BC34A', '#FFEB3B', '#FFC107', '#CDDC39',
    "#FF0000", "#00FF00", "#0000FF", "#FFFF00", "#00FFFF",
    "#FF00FF", "#800000", "#008000", "#000080", "#808000",
    "#800080", "#008080", "#808080", "#C0C0C0", "#FF9999",
    "#99FF99", "#9999FF", "#FFFF99", "#99FFFF", "#FF99FF",
    "#FF6666", "#66FF66", "#6666FF", "#FFFF66", "#66FFFF",
    "#FF66FF", "#FF3333", "#33FF33", "#3333FF", "#FFFF33",
    "#33FFFF", "#FF33FF", "#FF0000", "#00FF00", "#0000FF",
    "#FFFF00", "#00FFFF", "#FF00FF", "#800000", "#008000",
    "#000080", "#808000", "#800080", "#008080", "#808080",
    "#C0C0C0", "#FF9999", "#99FF99", "#9999FF", "#FFFF99",
    "#99FFFF", "#FF99FF", "#FF6666", "#66FF66", "#6666FF",
    "#FFFF66", "#66FFFF", "#FF66FF", "#FF3333", "#33FF33",
    "#3333FF", "#FFFF33", "#33FFFF", "#FF33FF", "#FF0000",
    "#00FF00", "#0000FF", "#FFFF00", "#00FFFF", "#FF00FF",
    "#800000", "#008000", "#000080", "#808000", "#800080",
    "#008080", "#808080", "#C0C0C0", "#FF9999", "#99FF99",
    "#9999FF", "#FFFF99", "#99FFFF", "#FF99FF", "#FF6666",
    "#66FF66", "#6666FF", "#FFFF66", "#66FFFF", "#FF66FF",
    "#FF3333", "#33FF33", "#3333FF", "#FFFF33", "#33FFFF",
    "#FF33FF"
     );

	function __construct()

    {

        parent::__construct();

    }


	function get_dashboard_sales_glance($from_date, $to_date,$id_branch,$id_metal)

    {

		$multiple_id_metal = implode(' , ', $id_metal);

        if($multiple_id_metal != '')

		{

			$id_metal = $multiple_id_metal;

		}else{

			$id_metal = $id_metal;

		}



		$multiple_id_branch = implode(' , ', $id_branch);

        if($multiple_id_branch != '')

		{

			$id_branch = $multiple_id_branch;

		}else{

			$id_branch = $id_branch;

		}

		//$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));


        $sql=$this->db->query("SELECT COUNT(DISTINCT b.bill_id) as sales_bill_count,IFNULL(sum(d.gross_wt),0.00) as sale_gwt,

        IFNULL(sum(d.net_wt),0.00) as sale_nwt, IFNULL(sum(d.item_cost),0.00) as sale_amount,IFNULL(sum(d.bill_discount),0.00) as sale_discount,

        IFNULL(SUM(ret.item_cost),0) as sales_return_amt,IFNULL(SUM(dia.diawt),0.00) as sale_diawt,

        IFNULL(SUM(ret.piece),0) as sales_return_count

        from ret_billing b

        Left JOIN ret_bill_details d on d.bill_id = b.bill_id

        LEFT JOIN ret_bill_return_details r ON r.bill_id = b.bill_id

        Left JOIN ret_bill_details ret on ret.bill_det_id = r.ret_bill_det_id

        LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id

        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id

        LEFT JOIN (SELECT IFNULL(SUM(s.wt),0) as diawt,IFNULL(SUM(s.price),0) as stn_amt,s.bill_det_id

			FROM ret_billing_item_stones s

			LEFT JOIN ret_bill_details d ON d.bill_det_id = s.bill_det_id

			LEFT JOIN ret_billing b on b.bill_id=d.bill_id

			LEFT JOIN ret_stone st ON st.stone_id = s.stone_id

			LEFT JOIN ret_uom m ON m.uom_id = s.uom_id

			WHERE st.stone_type = 1 GROUP By s.bill_det_id ) dia ON dia.bill_det_id = d.bill_det_id

        WHERE b.bill_id is not null and b.bill_status=1 AND b.is_eda=1
		-- ".($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") )."

		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."

        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch in (".$id_branch.")" :'' )."

         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."

         ");





	//	 print_r($this->db->last_query());exit;



	   $sales_return = $this->get_sales_return_details($from_date, $to_date,$id_branch,$id_metal);



	   $return_data = $sql->row_array();

	   $return_data['sale_diawt'] = round($return_data['sale_diawt'],3);

	   $return_data['sales_return_amt'] = $sales_return['cost'] != NULL ? $sales_return['cost'] : 0.00;



	   $return_data['sales_return_count'] = $sales_return['item_count'] != NULL ? $sales_return['item_count'] : 0.00;



	   $return_data['sales_return_pcs'] = $sales_return['piece'] != NULL ? $sales_return['piece'] : 0 ;





        return $return_data;



    }



    function get_sales_return_details($from_date, $to_date,$id_branch,$id_metal){



        $multiple_id_metal = implode(' , ', $id_metal);

        if($multiple_id_metal != '')

		{

			$id_metal = $multiple_id_metal;

		}else{

			$id_metal = $id_metal;

		}



		$multiple_id_branch = implode(' , ', $id_branch);

        if($multiple_id_branch != '')

		{

			$id_branch = $multiple_id_branch;

		}else{

			$id_branch = $id_branch;

		}



        $sql=$this->db->query("select SUM(d.piece) as piece,sum(d.item_cost) as cost,count(d.bill_det_id) as item_count

        from ret_billing b

        LEFT JOIN ret_bill_return_details r ON r.bill_id = b.bill_id

        Left JOIN ret_bill_details d on d.bill_det_id = r.ret_bill_det_id

        LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id

        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id

        WHERE r.bill_id is not null and b.bill_status=1 and b.is_eda=1

		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."

        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch in (".$id_branch.")" :'' )."

         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."

         ");



         return $sql->row_array();





    }

	function get_top_selling($from_date, $to_date,$id_branch,$id_metal)
    {
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		$multiple_id_branch = implode(' , ', $id_branch);
        if($multiple_id_branch != '')
		{
			$id_branch = $multiple_id_branch;
		}else{
			$id_branch = $id_branch;
		}

        $sql=$this->db->query("SELECT pro.product_name as product_name,COUNT(b.bill_id) as sales_bill_count,IFNULL(SUM(d.gross_wt),0) as wt,IFNULL(SUM(d.piece),0) as pcs
        from ret_billing b
        Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        RIGHT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        WHERE b.bill_id is not null and b.bill_status=1
		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch in (".$id_branch.")" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 GROUP by pro.pro_id ORDER BY sales_bill_count DESC LIMIT 5
         ");

         $data=$sql->result_array();

         foreach($data as $key => $val){

			$pro =$this->get_product_stock($id_branch,'',$val['product_id'])[0];

		   $val['stock_pcs'] =$pro['stock_pcs'];
		   $val['stock_wt'] =$pro['stock_wt'];

		   $return_data[]=$val;
	   	}

		return $return_data;

    }

	function get_top_sellers($from_date, $to_date,$id_branch,$id_metal)
    {
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		$multiple_id_branch = implode(' , ', $id_branch);
        if($multiple_id_branch != '')
		{
			$id_branch = $multiple_id_branch;
		}else{
			$id_branch = $id_branch;
		}

        $sql=$this->db->query("SELECT k.firstname as karigar_name,COUNT(b.bill_id) as sales_bill_count,IFNULL(SUM(d.gross_wt),0) as wt,IFNULL(SUM(d.piece),0) as pcs
        from ret_billing b
		Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        RIGHT JOIN ret_taging tag on tag.tag_id = d.tag_id
		LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        RIGHT JOIN ret_lot_inwards i ON i.lot_no=tag.tag_lot_id
        RIGHT JOIN ret_karigar k ON k.id_karigar = i.gold_smith
        WHERE b.bill_id is not null and b.bill_status=1
		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch in (".$id_branch.")" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 GROUP by k.id_karigar ORDER BY sales_bill_count DESC LIMIT 5
         ");

		 $data=$sql->result_array();

         foreach($data as $key => $val){

            $pro =$this->get_karigar_stock($id_branch,'',$val['id_karigar'])[0];


            $val['stock_pcs'] =$pro['stock_pcs'];
            $val['stock_wt'] =$pro['stock_wt'];

            $return_data[]=$val;
        }

        return $return_data;

    }

	function get_monthly_sales($fy_year,$id_branch,$id_metal)

    {

		$multiple_id_metal = implode(' , ', $id_metal);

        if($multiple_id_metal != '')

		{

			$id_metal = $multiple_id_metal;

		}else{

			$id_metal = $id_metal;

		}



        $multiple_id_branch = implode(' , ', $id_branch);

		if($multiple_id_branch != '')

		{

			$branch = $multiple_id_branch;

		}else{

			$branch = $id_branch;

		}



        $arrayBranch=$this->db->query("select * from branch where active = 1  ".($branch!='' && $branch !='0' ? " and id_branch in (".$branch.")" :'' )."")->result_array();



		$arrayMonth=['APR'=>4,'MAY'=>5,'JUN'=>6,'JUL'=>7,'AUG'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DEC'=>12,'JAN'=>1,'FEB'=>2,'MAR'=>3];



		// $arrayBranch=

       $array=[];

		foreach($arrayMonth as $key=>$month){

			$array_data=[];

			$array_data[]=$key;

			foreach($arrayBranch as $id=>$branch){



				$get=$this->db->query("SELECT IFNULL(SUM(d.net_wt),0.00 )as total_net_wt,date_format(b.bill_date,'%M') as bill_month,br.name as branch_name



				FROM ret_bill_details d



				LEFT JOIN ret_billing b ON b.bill_id=d.bill_id



				LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id



				LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id



				LEFT JOIN branch br ON br.id_branch=b.id_branch



				WHERE   b.bill_status=1 and



				b.id_branch = '".$branch['id_branch']."'  and b.is_eda = 1



                ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."



				and



               MONTH(b.bill_date) ='".$month."'



			   ".( $fy_year!='' && $fy_year !='0' ? " and b.fin_year_code = ".$fy_year."" :'' )."





		        GROUP by b.id_branch,MONTH(b.bill_date)")->row();



  	            //print_r($this->db->last_query());exit;



				$array_data[]= $get->total_net_wt != null ? (float) $get->total_net_wt:0.00;



		     }

			 $array[]=$array_data;





		}



		$return_data['branch'] =  $arrayBranch;



		$return_data['data'] =  $array;





        return $return_data;



    }

    function get_monthly_sales_mobile($fy_year,$id_branch,$id_metal)
    {
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

        $multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$id_branch = $multiple_id_branch;
		}else{
			$id_branch = $id_branch;
		}

        $arrayBranch=$this->db->query("select * from branch where active = 1  ".($id_branch!='' && $id_branch !='0' ? " and id_branch in (".$id_branch.")" :'' )."")->result_array();

		$arrayMonth=['APR'=>4,'MAY'=>5,'JUN'=>6,'JUL'=>7,'AUG'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DEC'=>12,'JAN'=>1,'FEB'=>2,'MAR'=>3];

		// $arrayBranch=
        $array=[];

		$branch_data = [];

         $array_month_lable=[];
		 $array_colour=[];

		foreach($arrayBranch as $id=>$branch){

		    $array_data=[];

		    $array_month_lable=[];

		    $branch_array[]=$branch['name'];

		    	foreach($arrayMonth as $key=>$month){

    				$get=$this->db->query("SELECT IFNULL(SUM(d.net_wt),0.00 )as total_net_wt,date_format(b.bill_date,'%M') as bill_month,br.name as branch_name

    				FROM ret_bill_details d

    				LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

					LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id

					LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id

    				LEFT JOIN branch br ON br.id_branch=b.id_branch

    				WHERE

					 b.bill_status=1 and

    				b.id_branch = '".$branch['id_branch']."'

					".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."

    				and

                   MONTH(b.bill_date) ='".$month."'

    			   ".( $fy_year!='' && $fy_year !='0' ? " and b.fin_year_code = ".$fy_year."" :'' )."


    		        GROUP by b.id_branch,MONTH(b.bill_date)")->row();



    				$array_data[]= $get->total_net_wt != null ? (float) $get->total_net_wt:0.00;

    				$array_month_lable[]=$key;

		    	}

				$branch_data[] = ['name' => $branch['name'] , 'value' => $array_data ];



		    	$array[]=$array_data;
				$array_colour[] = SELF::COLOUR_CODE[$id];



	     }





		// $return_data['branch'] =  $branch_array;

		// $return_data['value'] =  $array;

		$return_data['branch'] = $branch_data;

		$return_data['month'] =  $array_month_lable;

	    $return_data['colour_code'] =  $array_colour;
    //    $sql=$this->db->query()

        return $return_data;

    }

	function get_financial_year()
	{
		$data=$this->db->query("SELECT fin_id,fin_year_name,fin_year_code,fin_year_from,fin_year_to,fin_status as status
		FROM ret_financial_year ");
		return $data->result_array();
	}

	function get_store_sales($from_date, $to_date,$id_branch,$id_metal)
    {
        $multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}
        $return_data=[];
        $sql=$this->db->query("SELECT br.name as branch_name,IFNULL(SUM(IFNULL(d.item_cost,0.00)),0) as branch_sales,b.id_branch,br.short_name as branch_short_name
        FROM ret_billing b
		LEFT JOIN ret_bill_details d on d.bill_id = b.bill_id
		LEFT JOIN branch br ON br.id_branch=b.id_branch
		LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        WHERE b.bill_id is not null and b.bill_status=1
        ".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 GROUP by b.id_branch
		 ORDER BY branch_sales DESC
         ");
         $data= $sql->result_array();
	// print_r($data);exit;
         $sum=0;
         foreach($data as $key=>$value){
             $sum+=$value['branch_sales'];
         }

         foreach($data as $key=>$value){

            //  number_format((float)$number, $decimalPlaces, '.', '');
			if(($value['branch_sales']) != 0){
				$value['branch_sales_percentage']= number_format((float)($value['branch_sales']/$sum*100),2, '.', '');
			}else {
				$value['branch_sales_percentage']= 0;
			}

             $value['colour_code']=SELF::COLOUR_CODE[$key];
             $return_data[]=$value;
         }



        return $return_data;

    }

	function get_branch_sales($from_date, $to_date,$id_branch)
    {
        $sql=$this->db->query("SELECT br.name as branch_name,SUM(IFNULL(d.item_cost,0.00)) as branch_sales
        FROM ret_billing b
		LEFT JOIN ret_bill_details d on d.bill_id = b.bill_id
		LEFT JOIN branch br ON br.id_branch=b.id_branch
        WHERE b.bill_id is not null and b.bill_status=1
        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 GROUP by b.id_branch
         ");

        return $sql->result_array();

    }




	function get_branch_wastage($from_date, $to_date,$id_branch,$group_by)
    {
		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$grp_by = ($group_by == 1 ? "d.product_id" :($group_by == 2 ? "sec.id_section" :'b.id_branch' ));

        $sql=$this->db->query("SELECT br.name as branch_name,pro.product_name as product_name,sec.section_name,CAST( SUM(IFNULL(d.wastage_percent,0.00))/COUNT(d.bill_det_id) AS DECIMAL(16,2)) as branch_wastage_va
        FROM ret_billing b
		LEFT JOIN ret_bill_details d on d.bill_id = b.bill_id
		LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
		LEFT JOIN branch br ON br.id_branch=b.id_branch
	    LEFT JOIN ret_taging tag on tag.tag_id = d.tag_id
		LEFT JOIN ret_section sec on sec.id_section = IFNULL(tag.id_section,d.id_section)
        WHERE b.bill_id is not null and b.bill_status=1 and d.wastage_percent != 0
		".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 GROUP by ".$grp_by."
         ");

        return $sql->result_array();

    }

	function get_product_sales($from_date, $to_date, $id_branch,$id_metal)
	{

		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		$sql = $this->db->query("SELECT
				pro.product_name as product_name,
				SUM(IFNULL(d.item_cost, 0.00)) as product_sales
			FROM
				ret_billing b
			LEFT JOIN
				ret_bill_details d ON d.bill_id = b.bill_id
			LEFT JOIN
				ret_product_master pro ON pro.pro_id = d.product_id
			LEFT JOIN
			    ret_category cat on cat.id_ret_category = pro.cat_id

			WHERE
				b.bill_id IS NOT NULL AND b.bill_status = 1 and pro.pro_id IS NOT NULL
				".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
		        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
                ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
			GROUP BY
				pro.pro_id ORDER BY product_sales DESC

				");

		return $sql->result_array();

	}

	function get_employee_sales($from_date, $to_date, $id_branch, $id_metal)
	{
		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		//$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));

		$sql = $this->db->query("SELECT
					IFNULL(emp.firstname,'LDJ') as emp_name,
					SUM( IFNULL(d.item_cost, 0.00)) as emp_sales
				FROM
					ret_billing b
				LEFT JOIN
					ret_bill_details d ON d.bill_id = b.bill_id
				LEFT JOIN
				    ret_product_master pro ON pro.pro_id = d.product_id
			    LEFT JOIN
			        ret_category cat on cat.id_ret_category = pro.cat_id

				LEFT JOIN
					ret_estimation_items est_item ON est_item.est_item_id = d.esti_item_id
				LEFT JOIN
					ret_estimation est ON est.estimation_id = est_item.esti_id
				LEFT JOIN
				employee emp ON emp.id_employee = est.created_by

				WHere  b.bill_id IS NOT NULL AND b.bill_status = 1 and b.is_eda = 1
				".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
		        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
                ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
				
				GROUP BY
				emp.id_employee

				ORDER BY emp_sales DESC

				");

		return $sql->result_array();

	}


	function get_section_sales($from_date, $to_date, $id_branch, $id_metal)
	{

		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		//$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));

		$sql = $this->db->query("SELECT sec.section_name,SUM(IFNULL(d.item_cost,0.00)) as section_sales
								from ret_billing b
								Left JOIN ret_bill_details d on d.bill_id = b.bill_id
								LEFT JOIN ret_product_master pro ON pro.pro_id = d.product_id
			                    LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
								LEFT JOIN ret_taging tag on tag.tag_id = d.tag_id
								LEFT JOIN ret_section sec on sec.id_section = IFNULL(tag.id_section,d.id_section)
									WHERE
										b.bill_id IS NOT NULL AND b.bill_status = 1 and sec.id_section IS NOT NULL and b.is_eda = 1
									    ".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
		                                ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
										".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
										
										GROUP by sec.id_section  ORDER BY section_sales DESC
										");


		return $sql->result_array();

	}

	function get_karigar_sales($from_date, $to_date, $id_branch,$id_metal)
	{

		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		//$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));

		$sql = $this->db->query("SELECT IFNULL(kar.firstname,'') as karigar_name,SUM(IFNULL(d.item_cost,0.00)) as karigar_sales
		from ret_billing b
		Left JOIN ret_bill_details d on d.bill_id = b.bill_id
		LEFT JOIN ret_product_master pro ON pro.pro_id = d.product_id
	    LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
		LEFT JOIN ret_taging tag on tag.tag_id = d.tag_id
		LEFT JOIN ret_lot_inwards lot on lot.lot_no=tag.tag_lot_id
		LEFT JOIN ret_karigar kar on kar.id_karigar=lot.gold_smith
		where tag.tag_id is NOT NULL and b.bill_id IS NOT NULL AND b.bill_status = 1 and  kar.id_karigar IS NOT NULL and b.is_eda = 1
		".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
		".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		
		GROUP by lot.gold_smith ORDER BY karigar_sales DESC

				");

		return $sql->result_array();

	}

	function get_karigar_stock($id_branch, $id_metal, $id_karigar = "",$group_by = "1",$id_product = "")
	{
		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}
		$multiple_id_karigar = implode(' , ', $id_karigar);
		if($multiple_id_karigar != '')
		{
			$id_karigar = $multiple_id_karigar;
		}else{
			$id_karigar = $id_karigar;
		}
		$return_data = [];

		$sql = $this->db->query("SELECT k.firstname as karigar_name,pro.product_name as product_name,i.gold_smith as id_karigar,tag.current_branch as id_branch,tag.product_id as id_product,
		IFNULL(SUM(tag.piece),0) as stock_pcs,IFNULL(SUM(tag.gross_wt),0) as stock_wt,br.name as branch_name,
		SUM(IFNULL(tag.net_wt, 0.00)) as net_wt,
		SUM(IFNULL(tag.gross_wt, 0.00)) as gross_wt,
		IFNULL(stn.wt, 0.00) as dia_wt
		FROM ret_taging tag
		LEFT JOIN ret_lot_inwards i ON i.lot_no=tag.tag_lot_id
		LEFT JOIN ret_karigar k ON k.id_karigar = i.gold_smith
		LEFT JOIN ret_product_master pro ON pro.pro_id = tag.product_id
	    LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
		LEFT JOIN branch br ON br.id_branch = tag.current_branch
		LEFT JOIN
			    (SELECT SUM(wt) as wt,tag_st.tag_id,tag.product_id,i.gold_smith FROM  ret_taging_stone tag_st

						LEFT JOIN ret_stone st ON st.stone_id =tag_st.stone_id
						LEFT JOIN ret_taging tag ON tag.tag_id =tag_st.tag_id
						LEFT JOIN ret_lot_inwards i ON i.lot_no=tag.tag_lot_id
						LEFT JOIN ret_product_master pro ON pro.pro_id = tag.product_id
						LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
						Where st.stone_type = 1 and tag.tag_status = 0
									".($branch!='' && $branch !='0' ? " and tag.current_branch = ".$branch."" :'' )."
									".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
									".($id_product!='' && $id_product !='0' ? " and  pro.pro_id = ".$id_product."" :'' )."
									GROUP BY i.gold_smith ) as stn ON stn.gold_smith = i.gold_smith
		WHERE tag.tag_status = 0 and
		k.id_karigar is NOT NULL
		".($branch!='' && $branch !='0' ? " and tag.current_branch in ( ".$branch.")" :'' )."
		".($id_karigar!='' && $id_karigar !='0' ? " and k.id_karigar  in (".$id_karigar.")" :'' )."
		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
		GROUP by ".( $group_by =='1' ? " i.gold_smith " : ($group_by =='2' ? " i.gold_smith,tag.current_branch,tag.product_id " :'' ))." ORDER BY stock_wt DESC ");

		$data = $sql->result_array();



		return $data;

	}


	function get_section_stock($id_branch,$id_metal,$id_section ="")
	{
		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}
		$sql = $this->db->query("SELECT sec.section_name,SUM(IFNULL(tag.gross_wt,0.00)) as stock_wt,
		                        SUM(IFNULL(tag.piece, 0)) as stock_pcs,
								SUM(IFNULL(tag.net_wt, 0.00)) as net_wt,
								SUM(IFNULL(tag.gross_wt, 0.00)) as gross_wt,
								IFNULL(stn.wt, 0.00) as dia_wt
								FROM ret_taging tag
								LEFT JOIN ret_section sec on sec.id_section = tag.id_section
								LEFT JOIN ret_product_master pro ON pro.pro_id = tag.product_id
	    						LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
								LEFT JOIN
										(SELECT SUM(wt) as wt,tag_st.tag_id,tag.id_section FROM  ret_taging_stone tag_st
												LEFT JOIN ret_stone st ON st.stone_id =tag_st.stone_id
												LEFT JOIN ret_taging tag ON tag.tag_id =tag_st.tag_id
												LEFT JOIN ret_product_master pro ON pro.pro_id = tag.product_id
												LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
												Where st.stone_type = 1 and tag.tag_status = 0
															".($branch!='' && $branch !='0' ? " and tag.current_branch = ".$branch."" :'' )."
															".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
															".($id_section!='' && $id_section !='0' ? " and  tag.id_section = ".$id_section."" :'' )."
															GROUP BY tag.id_section ) as stn ON stn.id_section = tag.id_section
								WHERE
									 tag.tag_status = 0 and tag.id_section IS NOT NULL
									 ".($id_section!='' && $id_section !='0' ? " and  tag.id_section = ".$id_section."" :'' )."
									  ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
									".($branch!='' && $branch !='0' ? " and tag.current_branch in ( ".$branch.")" :'' )."
								GROUP by tag.id_section ORDER BY stock_wt DESC ");

		return $sql->result_array();

	}
	function get_product_stock($id_branch,$id_metal,$id_product = "")
	{

		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		$sql = $this->db->query("SELECT
				pro.product_name as product_name,
				SUM(IFNULL(tag.gross_wt, 0.00)) as stock_wt,
				SUM(IFNULL(tag.piece, 0)) as stock_pcs,
				SUM(IFNULL(tag.net_wt, 0.00)) as net_wt,
				SUM(IFNULL(tag.gross_wt, 0.00)) as gross_wt,
				IFNULL(stn.wt, 0.00) as dia_wt
			FROM
			    ret_taging tag
			LEFT JOIN
				ret_product_master pro ON pro.pro_id = tag.product_id
			LEFT JOIN
			    ret_category cat on cat.id_ret_category = pro.cat_id
			LEFT JOIN
			    (SELECT SUM(wt) as wt,tag_st.tag_id,tag.product_id FROM  ret_taging_stone tag_st
						LEFT JOIN ret_stone st ON st.stone_id =tag_st.stone_id
						LEFT JOIN ret_taging tag ON tag.tag_id =tag_st.tag_id
						LEFT JOIN ret_product_master pro ON pro.pro_id = tag.product_id
						LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
						Where st.stone_type = 1 and tag.tag_status = 0
									".($branch!='' && $branch !='0' ? " and tag.current_branch = ".$branch."" :'' )."
									".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
									".($id_product!='' && $id_product !='0' ? " and  pro.pro_id = ".$id_product."" :'' )."
									GROUP BY tag.product_id ) as stn ON stn.product_id = tag.product_id
			WHERE
			 pro.pro_id IS NOT NULL and tag.tag_status = 0
			 ".($branch!='' && $branch !='0' ? " and tag.current_branch = ".$branch."" :'' )."
			 ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
			 ".($id_product!='' && $id_product !='0' ? " and  pro.pro_id = ".$id_product."" :'' )."
			GROUP BY
				pro.pro_id
			ORDER BY
				stock_wt DESC
				");

		return $sql->result_array();

	}

	function get_custome_wise_sale($from_date, $to_date,$id_branch,$id_metal)
    {
		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		//$profile_settings = $this->get_profile_settings($this->session->userdata('profile'));
		//$eda_filter = ($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") );

        $chitsql=$this->db->query("SELECT  COUNT(DISTINCT b.bill_cus_id) as sales_bill_count
        from ret_billing b
        Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        RIGHT JOIN scheme_account sc on sc.id_customer =b.bill_cus_id
		LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        WHERE d.bill_id is not null and b.bill_status=1 and b.is_eda = 1
		".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 
		 GROUP BY b.bill_cus_id ");

         //	print_r($this->db->last_query());exit;

        $newCust = $this->db->query("SELECT  COUNT(DISTINCT b.bill_cus_id) as sales_bill_count
        from ret_billing b
        Left JOIN ret_bill_details d on d.bill_id = b.bill_id
		LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        LEFT JOIN customer as cus ON cus.id_customer = b.bill_cus_id
        WHERE d.bill_id is not null and b.bill_status=1 and date(cus.date_add) =date(b.bill_date) and b.is_eda=1
		".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		
		 GROUP BY b.bill_cus_id
         ");

		  //	print_r($this->db->last_query());exit;

        $old_cus=$this->db->query("SELECT COUNT(DISTINCT b.bill_cus_id) as sales_bill_count

                                    FROM ret_billing b
									Left JOIN ret_bill_details d on d.bill_id = b.bill_id
									LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
                                    LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id

                                    LEFT JOIN scheme_account sc on sc.id_customer =b.bill_cus_id

                                    WHERE sc.id_customer is null and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

									".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
									".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )." AND

									b.bill_cus_id in (SELECT b.bill_cus_id

                                    FROM ret_billing b

                                    LEFT JOIN scheme_account sc on sc.id_customer =b.bill_cus_id

                                    WHERE sc.id_customer is null and  date(b.bill_date)<'$from_date')

									GROUP BY b.bill_cus_id");


        $responce_data = array(
           ['REGULAR CUSTOMER',(int) $old_cus->row_array()['sales_bill_count']],['NEW CUSTOMER',(int) $newCust->row_array()['sales_bill_count']],['CHIT CUSTOMER',(int) $chitsql->row_array()['sales_bill_count']]
            );


        return $responce_data;

    }


	function get_dashboard_estimation($from_date,$to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT (SELECT count(estimation_id) AS totestimation

										FROM ret_estimation as est WHERE date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'  ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'').") as created,

										(select count(*) from (SELECT count(estimation_id) AS totestimation

										FROM ret_estimation as est

										LEFT JOIN ret_estimation_items AS estitm ON estitm.esti_id = est.estimation_id

										WHERE estitm.purchase_status = 1 AND date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'')." GROUP BY esti_id) as sold) as sold,

										(select count(*) from (SELECT count(estimation_id) AS totestimation

										FROM ret_estimation as est

										LEFT JOIN ret_estimation_items AS estitm ON estitm.esti_id = est.estimation_id

										WHERE estitm.purchase_status = 0 AND date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'')." GROUP BY esti_id) as unsold) as unsold,

										(select count(*) from (SELECT count(estitm.est_id) AS totestimation

										FROM ret_estimation as est

										RIGHT JOIN ret_estimation_old_metal_sale_details AS estitm ON estitm.est_id = est.estimation_id

										WHERE estitm.purchase_status = 0 and id_old_metal_type = 1  AND date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'')." GROUP BY est_id) as old_gold) as old_gold,

										(select count(*) from (SELECT count(estitm.est_id) AS totestimation

										FROM ret_estimation as est

										RIGHT JOIN ret_estimation_old_metal_sale_details AS estitm ON estitm.est_id = est.estimation_id

										WHERE estitm.purchase_status = 0 and id_old_metal_type = 2  AND date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'')." GROUP BY est_id) as old_silver) as old_silver
										");

		//print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_dashboard_virturaltag_details($from_date, $to_date,$id_branch)

    {

        $sql = $this->db->query("SELECT ifnull(SUM(IF(d.tag_id IS NULL and  e.item_type =2  and cat.id_metal = 1,d.gross_wt,0)),0) as homesale_gold_wt,ifnull(SUM(IF(d.is_partial_sale=1 and cat.id_metal = 1 and d.tag_id IS NOT null,d.gross_wt,0)),0) as partly_gold_wt,

		ifnull(SUM(IF(d.tag_id IS NULL and cat.id_metal = 1 and  e.item_type =2 ,d.piece,0)),0) as homesale_gold_pcs,ifnull(SUM(IF(d.is_partial_sale=1  and cat.id_metal = 1  and d.tag_id IS NOT null,d.piece,0)),0) as partly_gold_pcs,

		ifnull(SUM(IF(d.tag_id IS NULL and cat.id_metal = 2 and  e.item_type =2,d.gross_wt,0)),0) as homesale_silver_wt,ifnull(SUM(IF(d.is_partial_sale=1 and cat.id_metal = 2  and d.tag_id IS NOT null,d.gross_wt,0)),0) as partly_silver_wt,

		ifnull(SUM(IF(d.tag_id IS NULL and cat.id_metal = 2 and  e.item_type =2 ,d.piece,0)),0) as homesale_silver_pcs,ifnull(SUM(IF(d.is_partial_sale=1 and cat.id_metal = 2 and d.tag_id IS NOT null ,d.piece,0)),0) as partly_silver_pcs

		FROM ret_billing b

		LEFT JOIN ret_bill_details d ON d.bill_id = b.bill_id

		left join ret_product_master as pro on(pro.pro_id=d.product_id)

		left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

		LEFT JOIN ret_estimation_items e ON e.est_item_id=d.esti_item_id

		LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id

		LEFT JOIN ret_taging tag on tag.tag_id=d.tag_id

		WHERE   cat.id_metal in  (1,2)  and b.bill_status = 1 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

		".($id_branch!='' && $id_branch>0 ?  " and b.id_branch=".$id_branch."" :'')." ");

        //print_r($this->db->last_query());exit;

        return $sql->row_array();

    }

	function get_dashboard_salesreturn_det($from_date,$to_date,$id_branch)

	{

		$sql=$this->db->query("SELECT IFNULL(sum(IF(cat.id_metal = 1,d.gross_wt,0)),0) as gold_wt, IFNULL(sum(IF(cat.id_metal = 1,d.piece,0)),0) as gold_pcs ,

		IFNULL(sum(IF(cat.id_metal = 2,d.gross_wt,0)),0) as silver_wt,  IFNULL(sum(IF(cat.id_metal = 2,d.piece,0)),0) as silver_pcs

		from ret_bill_details as d

		left join ret_billing as b on(b.bill_id=d.bill_id)

		left join ret_product_master as pro on(pro.pro_id=d.product_id)

		left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

		where d.status=2 and b.bill_status=1

		and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

		".($id_branch!='' && $id_branch>0 ?  " and b.id_branch=".$id_branch."" :'')." ");

		// print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_dashboard_lot_tag_details($from_date, $to_date, $id_branch)

	{

		$sql = $this->db->query("SELECT

		(SELECT ifnull(SUM(d.no_of_piece),0) FROM ret_lot_inwards l left JOIN ret_lot_inwards_detail d on d.lot_no=l.lot_no WHERE date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and l.created_branch=".$id_branch."" :'').") as lot_pcs,

		(SELECT ifnull(SUM(d.gross_wt),0) FROM ret_lot_inwards l left JOIN ret_lot_inwards_detail d on d.lot_no=l.lot_no WHERE date(l.lot_date) BETWEEN  '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and l.created_branch=".$id_branch."" :'').") as lot_wt,

		(SELECT ifnull(SUM(tag.piece),0)  FROM ret_taging tag left join ret_product_master as pro on(pro.pro_id=tag.product_id) left join ret_category as cat on(cat.id_ret_category=pro.cat_id) WHERE cat.id_metal = 1 and tag.tag_status!=2 AND date(tag.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and tag.id_branch=".$id_branch."" :'').") as gold_tagged_pcs,

		(SELECT ifnull(SUM(tag.gross_wt),0)  FROM ret_taging tag left join ret_product_master as pro on(pro.pro_id=tag.product_id) left join ret_category as cat on(cat.id_ret_category=pro.cat_id)  WHERE cat.id_metal = 1 and tag.tag_status!=2 AND date(tag.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and tag.id_branch=".$id_branch."" :'').") as gold_tagged_wt,

		(SELECT ifnull(SUM(tag.piece),0)  FROM ret_taging tag left join ret_product_master as pro on(pro.pro_id=tag.product_id) left join ret_category as cat on(cat.id_ret_category=pro.cat_id) WHERE cat.id_metal = 2 and tag.tag_status!=2 AND date(tag.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and tag.id_branch=".$id_branch."" :'').") as silver_tagged_pcs,

		(SELECT ifnull(SUM(tag.gross_wt),0)  FROM ret_taging tag  left join ret_product_master as pro on(pro.pro_id=tag.product_id) left join ret_category as cat on(cat.id_ret_category=pro.cat_id) WHERE cat.id_metal = 2 and tag.tag_status!=2 AND date(tag.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and tag.id_branch=".$id_branch."" :'').") as silver_tagged_wt
		");

		// print_r($this->db->last_query());exit;

		return $sql->row_array();

	}


	function get_cover_up_report($from_date, $to_date, $id_branch,$id_metal)

	{
        $op_blc_to_date= date('Y-m-d',(strtotime('-1 day',strtotime($from_date))));
/*
		$openingwt = $this->db->query("SELECT tag.tag_code,tag.tag_id,tag.old_tag_id,ifnull(sum(tag.gross_wt),0) as gross_wt,m1.to_branch as id_branch,
		ifnull(SUM(tag.net_wt),0) as net_wt,ifnull(SUM(tag.piece),0) as piece
		FROM ret_taging_status_log m1
		LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date) <= '".$op_blc_to_date."')
		LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id
		WHERE m2.id_tag_status_log IS NULL  and m1.issuspensestock = 0
		".($id_branch!='' && $id_branch!=0  ? " and m1.to_branch =".$id_branch." " :'')."
		AND (m1.status = 0 OR m1.status = 6) AND date(m1.date) <= '".$op_blc_to_date."'
		")->row()->gross_wt;
		*/

		// print_r($this->db->last_query());exit;


		$sales_wt = $this->db->query("SELECT 'Sales' as label, ifnull(sum(b.gross_wt),0) as gross_wt,IFNULL(SUM(b.net_wt),0) as net_wt,
		SUM(b.piece) as piece,round(SUM(b.net_wt) * ifnull(pur.purity,pur2.purity) / 100,3) as pure_wt,round(IFNULL(AVG(ifnull(pur.purity,pur2.purity)),0),2) as purity
		FROM ret_bill_details b
		LEFT JOIN ret_billing bill on bill.bill_id = b.bill_id
		LEFT JOIN ret_purity pur ON pur.id_purity = b.purity
		LEFT JOIN ret_product_master p ON p.pro_id=b.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category=p.cat_id
		LEFT JOIN ret_taging tag ON tag.tag_id = b.tag_id
		LEFT JOIN ret_purity pur2 ON pur2.id_purity = tag.purity
		WHERE bill.bill_status = 1 and bill.is_eda = 1
		and date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'
		 ".($id_metal!='' && $id_metal !='0' ? "and cat.id_metal in (".$id_metal.") " :'' )."
		".($id_branch!='' && $id_branch!=0 ? " and bill.id_branch =".$id_branch."" :'')."
		")->row();



		$sales_return_wt = $this->db->query(" SELECT 'Sales Return' as label, ifnull(SUM(d.net_wt),0) as net_wt, ifnull(SUM(d.gross_wt),0) as gross_wt,
		round(IFNULL(SUM(d.net_wt * (pur.purity/100)),0),3) as pure_wt,round(IFNULL(AVG(pur.purity),''),2) as purity
		FROM ret_billing b
		LEFT JOIN ret_bill_return_details r ON r.bill_id=b.bill_id
		RIGHT JOIN ret_bill_details d ON d.bill_det_id=r.ret_bill_det_id
		LEFT JOIN ret_purity pur ON pur.id_purity = d.purity
		LEFT JOIN ret_product_master p ON p.pro_id=d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category=p.cat_id
		WHERE   b.bill_status=1 and b.is_eda = 1
    	".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."
		 ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."
		and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'
		")->row();

		//print_r($sales_return_wt);exit;








		$old_metal_wt = $this->db->query("SELECT 'Old Gold' as label,IFNULL(SUM(bill_old.gross_wt),0) as gross_wt,IFNULL(SUM(bill_old.net_wt),0) as net_wt,round(IFNULL(SUM(bill_old.net_wt * (bill_old.purity/100)),0),3) as pure_wt,round(IFNULL(AVG(bill_old.purity),0),2) as purity
		,date_format(bill.bill_date,'%d-%m-%Y') as bill_date,IFNULL(SUM(bill_old.rate),0) as total_amount
		FROM ret_bill_old_metal_sale_details as bill_old
		LEFT JOIN ret_billing as bill ON bill.bill_id = bill_old.bill_id
		WHERE bill.bill_id is not null and bill.bill_status=1 and bill.is_eda = 1
		and date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'
		 ".($id_metal!='' && $id_metal !='0' ? " and bill_old.metal_type in (".$id_metal.") " :'' )."
		".($id_branch != '' && $id_branch >0 ? ' and bill.id_branch='.$id_branch: '')."
		")->row();

		$chit_credit_collection = $this->db->query("SELECT 'Chit' as label,
		IFNULL(sum(IF(p.payment_mode='FP','0',pmd.payment_amount)),0) as net_wt, round(IFNULL(sum( (IF(p.payment_mode='FP','0',pmd.payment_amount) / p.metal_rate) * (91.6 /100) ),0),3) as pure_wt,'91.60' as purity
	   FROM payment_mode_details pmd
	   LEFT JOIN payment p ON (p.id_payment = pmd.id_payment)
	   LEFT JOIN branch b ON (b.id_branch = p.id_branch)
	   Left Join payment_mode pm on (pm.short_code = pmd.payment_mode)
	   JOIN chit_settings chit
	   Where pmd.payment_status = 1 and p.payment_status = 1 and pmd.is_active = 1 and pmd.payment_mode  = 'CSH'
		and (date(p.custom_entry_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
	   ".($id_branch!='' && $id_branch>0 ? " and p.id_branch=".$id_branch."" :'')."
	       ")->row();


		$covered_wt = $this->db->query("SELECT IFNULL(SUM(weight),0) as weight  FROM ret_cover_up where (date(created_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_metal!='' && $id_metal>0 ? " and id_metal=".$id_metal."" :'')." GROUP BY date(created_on) ORDER BY id_coverup DESC LIMIT 1 ")->row()->weight;



		//echo $this->db->last_query();exit;

		$grn = $this->db->query("SELECT IFNULL(SUM(t.item_pure_wt),0) as pure_wt,AVG(purchase_touch) as purity FROM ret_purchase_order_items t

		LEFT JOIN ret_purchase_order gr ON gr.po_id = t.po_item_po_id

		LEFT JOIN ret_category cat on cat.id_ret_category=t.po_item_cat_id

		where (date(gr.po_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_metal!='' && $id_metal>0 ? " and cat.id_metal=".$id_metal."" :'')." ")->row()->pure_wt;

         $cover=$covered_wt;

		$covered_wt = number_format( $covered_wt + $grn ,3,'.','');

		$wt =  number_format($sales_wt->pure_wt + ($id_metal == 1 ? $chit_credit_collection->pure_wt : 0 ) - $sales_return_wt->pure_wt -  $old_metal_wt->pure_wt,3,'.','');

		$pending =  number_format( (float) $covered_wt - $wt,3,'.','');

		if( $pending <=0 ){
			$label  = "Pending Positions";
		}else{
			$label  = "Excess Positions";
		}

		$returndata[] =$sales_wt;

		$returndata[] =$old_metal_wt;

		$returndata[] =$sales_return_wt;

		if($id_metal == 1){
			$returndata[] =$chit_credit_collection;
		}






		$return = array(

			"data" => $returndata,
			'coverup_wt' => $wt,
			'covered_wt' => $covered_wt,
			'pending' =>abs($pending),
			'pending_label' => $label,
			'grn_wt' => $grn,
			'cover_wt' => $cover

		);






		// print_r($this->db->last_query());exit;

		return $return;

	}



	function get_purchase_inwards($from_date,$to_date,$id_branch,$id_metal)
	{
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		$sql=$this->db->query("SELECT
		IFNULL(SUM(pitm.gross_wt),0) as gross_wt,  IFNULL(SUM(pitm.less_wt),0) as less_wt, IFNULL(SUM(pitm.net_wt),0) as net_wt, IFNULL(SUM(pitm.no_of_pcs),0) as no_of_pcs,IFNULL(SUM(pitm.item_pure_wt),0) as pure_wt,
		ifnull(SUM(posti.stwt), 0) as  dia_wt
		FROM ret_purchase_order_items as pitm
		LEFT JOIN  ret_purchase_order p ON p.po_id = pitm.po_item_po_id
		LEFT JOIN ret_category c on c.id_ret_category=pitm.po_item_cat_id
		LEFT JOIN (SELECT itm.po_item_po_id, sum(po_stone_wt) as stwt, sum(po_stone_amount) as stamount
			FROM ret_po_stone_items as po
			LEFT JOIN ret_purchase_order_items itm ON itm.po_item_id = po.po_item_id
			LEFT JOIN ret_stone as st ON st.stone_id = po.po_stone_id
			WHERE st.stone_type = 1
		GROUP BY itm.po_item_po_id) as posti ON posti.po_item_po_id = pitm.po_item_po_id
		WHERE p.is_approved = 1 and p.bill_status=1 and (date(p.po_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		".($id_metal != '' && $id_metal != 0 ? " and c.id_metal  in (".$id_metal.")" :'')." ");

		// print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_delayed_purchase_orders($from_date, $to_date, $id_branch, $id_metal)
	{
		$branch = is_array($id_branch) ? implode(',', $id_branch) : $id_branch;
		$id_metal = is_array($id_metal) ? implode(',', $id_metal) : $id_metal;

		$sql = $this->db->query("SELECT 
			c.pur_no as po_number,
			c.id_customerorder as orderid,
			DATE_FORMAT(c.order_date,'%d-%m-%Y') as po_date,
			DATE_FORMAT(d.smith_due_date,'%d-%m-%Y') as expected_delivery_date,
			DATEDIFF(CURRENT_DATE(), d.smith_due_date) as delayed_days,
			k.firstname as karigar_name,
			p.product_name as order_item,
			mt.metal as metal,
			d.totalitems as quantity_ordered,
			IFNULL(rcd.recd_pcs, 0) as received_pcs,
			d.id_customerorder,
			d.id_product,
			d.design_no,
			d.id_sub_design,
			DATE_FORMAT(rcd.last_recd_date,'%d-%m-%Y') as actual_received_date,
			DATEDIFF(d.smith_due_date, rcd.last_recd_date) as delivery_diff_days
		FROM customerorder c
		JOIN customerorderdetails d ON c.id_customerorder = d.id_customerorder
		LEFT JOIN ret_karigar k ON k.id_karigar = c.id_karigar
		LEFT JOIN ret_product_master p ON p.pro_id = d.id_product
		LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id
		LEFT JOIN metal mt ON mt.id_metal = cat.id_metal
		LEFT JOIN (
			SELECT pur.purchase_order_no as po_link_id, i.po_item_pro_id, i.po_item_des_id, i.po_item_sub_des_id, SUM(i.no_of_pcs) as recd_pcs, MAX(pur.po_date) as last_recd_date
			FROM ret_purchase_order_items i
			JOIN ret_purchase_order pur on i.po_item_po_id=pur.po_id
			WHERE pur.purchase_order_no IS NOT NULL AND pur.purchase_order_no != ''
			GROUP BY pur.purchase_order_no, i.po_item_pro_id, i.po_item_des_id, i.po_item_sub_des_id
		) as rcd ON rcd.po_link_id = c.id_customerorder 
		WHERE c.order_type = 1 AND c.pur_no IS NOT NULL AND (c.order_status != 6 and c.order_status != 7) AND (d.orderstatus != 6 and d.orderstatus!=7)
		".($branch!='' && $branch !='0' ? " AND c.order_from in (".$branch.")" :'' )."
		".($id_metal!='' && $id_metal !='0' ? " AND cat.id_metal in (".$id_metal.")" :'' )."
		ORDER BY delayed_days DESC");
		return $sql->result_array();
	}

	function get_delayed_po_payments($from_date, $to_date, $id_branch, $id_metal = "")
	{
		$branch = is_array($id_branch) ? implode(',', $id_branch) : $id_branch;
		
		$q1 = "SELECT 
			'Unpaid' as status,
			IFNULL(po.po_ref_no, '') as po_number,
			CONCAT('index.php/admin_ret_purchase/purchase/job_receipt/', po.po_id) as po_url,
			DATE_FORMAT(po.po_date, '%d-%m-%Y') as po_date,
			IFNULL(k.firstname, '') as supplier_name,
			DATE_FORMAT(po.po_delivery_date,'%d-%m-%Y') as due_date,
			(IFNULL(po.tot_purchase_amt,0) - IFNULL(crdr.total_transamount,0)) as bill_amount,
			IF(IFNULL(pay.total_paid_amt, 0) >= (IFNULL(po.tot_purchase_amt,0) - IFNULL(crdr.total_transamount,0)), 'Paid', 'Unpaid') as payment_status,
			po.po_id,
			IFNULL(pay.total_paid_amt, 0) as paid_amount,
			DATE_FORMAT(pay.last_payment_date, '%d-%m-%Y') as payment_date,
			IF(pay.last_payment_date IS NOT NULL, 
				DATEDIFF(DATE(pay.last_payment_date), po.po_delivery_date), 
				DATEDIFF(CURRENT_DATE(), po.po_delivery_date)) as delay_days,
			IFNULL(pay.pay_refno, '') as pay_refno,
			IFNULL(pay.pay_mode, '') as pay_mode,
			IFNULL(pay.bank_name, '') as bank_name,
			IFNULL(pay.ref_no, '') as ref_no,
			IFNULL(po.dashboard_comment, '') as dashboard_comment
		FROM ret_purchase_order po
		LEFT JOIN ret_karigar k ON k.id_karigar = po.po_karigar_id
        LEFT JOIN (
            SELECT po_id, SUM(transamount) AS total_transamount
            FROM ret_crdr_note where crdr_status=1
            GROUP BY po_id
        ) crdr ON crdr.po_id = po.po_id
        LEFT JOIN (
            SELECT po_id, SUM(weight) AS total_rate_cut_wt
            FROM ret_supplier_rate_cut
            GROUP BY po_id
        ) src ON src.po_id = po.po_id
        LEFT JOIN (
            SELECT iss.po_id, SUM(det.issue_metal_wt) AS total_issue_wt
            FROM ret_karigar_metal_issue iss
            JOIN ret_karigar_metal_issue_details det ON det.issue_met_parent_id = iss.met_issue_id
            WHERE iss.bill_status = 1
            GROUP BY iss.po_id
        ) kmi ON kmi.po_id = po.po_id
		LEFT JOIN (
			SELECT 
				bill.po_id, 
				SUM(bill.total_paid) as total_paid_amt,
				MAX(bill.pay_date) as last_payment_date,
				GROUP_CONCAT(DISTINCT bill.pay_refno) as pay_refno,
				GROUP_CONCAT(DISTINCT bill.pay_mode) as pay_mode,
				GROUP_CONCAT(DISTINCT bill.bank_name) as bank_name,
				GROUP_CONCAT(DISTINCT bill.ref_no) as ref_no
			FROM (
				SELECT 
					b.po_id, 
					p.pay_id,
					MAX(b.total_amount) as total_paid,
					p.pay_create_on as pay_date,
					p.pay_refno,
					GROUP_CONCAT(DISTINCT det.pay_mode) as pay_mode,
					GROUP_CONCAT(DISTINCT IFNULL(det.bank_name, bak.bank_name)) as bank_name,
					GROUP_CONCAT(DISTINCT det.ref_no) as ref_no
				FROM ret_po_bill_payment_details b
				JOIN ret_po_payment p ON p.pay_id = b.pay_id
				LEFT JOIN ret_po_payment_detail det ON det.pay_id = p.pay_id
				LEFT JOIN bank bak ON bak.id_bank = det.id_bank
				WHERE p.pay_status = 1
				GROUP BY b.po_id, p.pay_id
			) bill
			GROUP BY bill.po_id
		) pay ON pay.po_id = po.po_id
		WHERE po.po_id IS NOT NULL  and po.pur_approval_type = 1
		AND (
			(DATEDIFF(po.po_delivery_date, CURRENT_DATE()) < 0)
		)
        AND (IFNULL(po.tot_purchase_wt,0) - IFNULL(src.total_rate_cut_wt,0) - IFNULL(kmi.total_issue_wt,0)) > 0";

        $q2 = "SELECT 
			'Unpaid' as status,
			IFNULL(concat(po.po_ref_no,' - ',src.ref_no), '') as po_number,
			CONCAT('index.php/admin_ret_purchase/purchase/purchase_add/', po.po_id) as po_url,
			DATE_FORMAT(src.date_add, '%d-%m-%Y') as po_date,
			IFNULL(k.firstname, '') as supplier_name,
			DATE_FORMAT(po.po_delivery_date,'%d-%m-%Y') as due_date,
			(IFNULL(src.amount,0) - IFNULL(crdr.total_transamount,0)) as bill_amount,
			'Unpaid' as payment_status,
			po.po_id,
			0 as paid_amount,
			'' as payment_date,
			DATEDIFF(CURRENT_DATE(), po.po_delivery_date) as delay_days,
			'' as pay_refno,
			'' as pay_mode,
			'' as bank_name,
			'' as ref_no,
			'' as dashboard_comment
        FROM ret_supplier_rate_cut src
        JOIN ret_purchase_order po ON po.po_id = src.po_id
		LEFT JOIN ret_karigar k ON k.id_karigar = po.po_karigar_id
        LEFT JOIN (
            SELECT po_id, id_supplier_rate_cut, SUM(transamount) AS total_transamount
            FROM ret_crdr_note where crdr_status=1
            GROUP BY id_supplier_rate_cut
        ) crdr ON crdr.id_supplier_rate_cut = src.id_supplier_rate_cut
        WHERE po.pur_approval_type = 1
        AND po.po_id NOT IN (SELECT po_id FROM ret_po_bill_payment_details)
        AND DATEDIFF(po.po_delivery_date, CURRENT_DATE()) < 0";

		$sql = $this->db->query($q1 . " UNION " . $q2 . " ORDER BY delay_days DESC");
		return $sql->result_array();
	}

	function get_today_delivery_po_payments($from_date, $to_date, $id_branch, $id_metal = "")
	{
		$branch = is_array($id_branch) ? implode(',', $id_branch) : $id_branch;
		$id_metal = is_array($id_metal) ? implode(',', $id_metal) : $id_metal;
		
		$q1 = "SELECT 
			'Unpaid' as status,
			po.po_ref_no as po_number,
			DATE_FORMAT(po.po_date, '%d-%m-%Y') as po_date,
			k.firstname as supplier_name,
			DATE_FORMAT(po.po_delivery_date,'%d-%m-%Y') as due_date,
			DATEDIFF(po.po_delivery_date, CURRENT_DATE()) as delayed_days,
			(IFNULL(po.tot_purchase_amt,0) - IFNULL(crdr.total_transamount,0)) as bill_amount,
			'Unpaid' as payment_status,
			po.po_id,
			IFNULL(po.dashboard_comment, '') as dashboard_comment
		FROM ret_purchase_order po
		LEFT JOIN ret_karigar k ON k.id_karigar = po.po_karigar_id
        LEFT JOIN ret_category cat ON cat.id_ret_category = po.id_category
        LEFT JOIN (
            SELECT po_id, SUM(transamount) AS total_transamount
            FROM ret_crdr_note where crdr_status=1
            GROUP BY po_id
        ) crdr ON crdr.po_id = po.po_id
        LEFT JOIN (
            SELECT po_id, SUM(weight) AS total_rate_cut_wt
            FROM ret_supplier_rate_cut
            GROUP BY po_id
        ) src ON src.po_id = po.po_id
        LEFT JOIN (
            SELECT iss.po_id, SUM(det.issue_metal_wt) AS total_issue_wt
            FROM ret_karigar_metal_issue iss
            JOIN ret_karigar_metal_issue_details det ON det.issue_met_parent_id = iss.met_issue_id
            WHERE iss.bill_status = 1
            GROUP BY iss.po_id
        ) kmi ON kmi.po_id = po.po_id
		LEFT JOIN (
			SELECT 
				bill.po_id, 
				SUM(bill.total_paid) as total_paid_amt
			FROM (
				SELECT 
					b.po_id, 
					MAX(b.total_amount) as total_paid
				FROM ret_po_bill_payment_details b
				JOIN ret_po_payment p ON p.pay_id = b.pay_id
				WHERE p.pay_status = 1
				GROUP BY b.po_id, p.pay_id
			) bill
			GROUP BY bill.po_id
		) pay ON pay.po_id = po.po_id
		WHERE po.po_id IS NOT NULL and po.bill_status = 1 and po.is_approved = 1
		".($from_date != '' && $to_date !='' ? ' AND (date(po.po_delivery_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")' : ' AND po.po_delivery_date = CURRENT_DATE()')."
		AND IFNULL(pay.total_paid_amt, 0) < (IFNULL(po.tot_purchase_amt,0) - IFNULL(crdr.total_transamount,0))
		AND (IFNULL(po.tot_purchase_wt,0) - IFNULL(src.total_rate_cut_wt,0) - IFNULL(kmi.total_issue_wt,0)) > 0
        ".($id_metal != '' && $id_metal != '0' ? " AND cat.id_metal in (".$id_metal.")" : "")."";

        $q2 = "SELECT 
			'Unpaid' as status,
			concat(po.po_ref_no,' - ',src.ref_no) as po_number,
			DATE_FORMAT(src.date_add, '%d-%m-%Y') as po_date,
			k.firstname as supplier_name,
			DATE_FORMAT(po.po_delivery_date,'%d-%m-%Y') as due_date,
			DATEDIFF(po.po_delivery_date, CURRENT_DATE()) as delayed_days,
			(IFNULL(src.amount,0) - IFNULL(crdr.total_transamount,0)) as bill_amount,
			'Unpaid' as payment_status,
			po.po_id,
			'' as dashboard_comment
        FROM ret_supplier_rate_cut src
        JOIN ret_purchase_order po ON po.po_id = src.po_id
		LEFT JOIN ret_karigar k ON k.id_karigar = po.po_karigar_id
        LEFT JOIN (
            SELECT po_id, id_supplier_rate_cut, SUM(transamount) AS total_transamount
            FROM ret_crdr_note where crdr_status=1
            GROUP BY id_supplier_rate_cut
        ) crdr ON crdr.id_supplier_rate_cut = src.id_supplier_rate_cut
        WHERE po.bill_status = 1 and po.is_approved = 1
        AND po.po_id NOT IN (SELECT po_id FROM ret_po_bill_payment_details)
        ".($from_date != '' && $to_date !='' ? ' AND (date(po.po_delivery_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")' : ' AND po.po_delivery_date = CURRENT_DATE()')."
        ".($branch != '' && $branch != '0' ? " AND src.id_branch in (".$branch.")" : "")."
        ".($id_metal != '' && $id_metal != '0' ? " AND src.id_metal in (".$id_metal.")" : "")."";

		$sql = $this->db->query($q1 . " UNION " . $q2 . " ORDER BY po_id DESC");
		return $sql->result_array();
	}

	function get_vendor_payment($from_date,$to_date,$id_branch)
	{

		$sql = $this->db->query("SELECT k.firstname as suppliername,d.payment_amount,d.pay_mode,k.contactno1,
		SUM(IF(d.pay_mode = 'CSH',d.payment_amount,0.00)) as cash,
		SUM(IF(d.pay_mode = 'RTGS' OR d.pay_mode = 'NEFT' ,d.payment_amount,0.00)) as NB,
		SUM(d.payment_amount) as total
        FROM ret_po_payment_detail d
        LEFT JOIN ret_po_payment p ON p.pay_id = d.pay_id
        LEFT JOIN ret_karigar k ON k.id_karigar = p.pay_sup_id
        LEFT JOIN bank b ON b.id_bank = d.id_bank
        WHERE p.pay_refno IS NOT NULL AND p.pay_status = 1
        ".($from_date != '' && $to_date !='' ? ' and date(p.pay_create_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		group by p.pay_sup_id
        ORDER BY p.pay_id DESC");

		// print_r($this->db->last_query());exit;

		return $sql->result_array();

	}


	function get_outward_details($from_date,$to_date,$id_branch,$id_metal)
	{
		$return_data = [];

		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		$sql = $this->db->query("SELECT IF(r.purchase_type = 1 ,'B2B SALES','PURCHASE RETURN') as type,

				 IFNULL(SUM(c.pur_ret_gwt),0) as gross_wt,IFNULL(SUM(c.pur_ret_nwt),0) as net_wt,IFNULL(SUM(c.pur_ret_lwt),0) as less_wt
				 ,IFNULL(SUM(c.pur_ret_pur_wt),0) as purewt,
                 IFNULL(SUM(s.diawt),0) as diawt

				 FROM ret_purchase_return_items c

				 LEFT JOIN ret_purchase_return r ON r.pur_return_id = c.pur_ret_id

				 LEFT JOIN ret_purchase_order_items pitm ON pitm.po_item_id = c.pur_ret_po_item_id

				 LEFT JOIN ret_purchase_order po ON po.po_id = pitm.po_item_po_id

				 LEFT JOIN ret_category cat ON cat.id_ret_category = pitm.po_item_cat_id

				 LEFT JOIN(SELECT IFNULL(SUM(s.ret_stone_wt),0) as diawt,s.pur_ret_return_id,

						IFNULL(SUM(s.ret_stone_amount),0) as dia_wt_amount,c.pur_ret_id

						FROM ret_purchase_return_stone_items s

						LEFT JOIN ret_stone as stn ON stn.stone_id = s.ret_stone_id

						LEFT JOIN ret_purchase_return_items as c ON c.pur_ret_itm_id= s.pur_ret_return_id

						LEFT JOIN ret_purchase_return r ON r.pur_return_id = c.pur_ret_id

						WHERE stn.stone_type = 1

					GROUP BY c.pur_ret_id) as s ON s.pur_ret_id = r.pur_return_id

				 WHERE r.bill_status = 1

				 ".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.") " :'' )."

				 and date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

				 GROUP BY r.purchase_type");

		$purchase_return = $sql->result_array();

		// print_r($purchase_return);exit;

		if(! $purchase_return){
			$purchase_return = [[
				"type" => "PURCHASE RETURN",
            "gross_wt" => 0,
            "net_wt" => 0,
            "less_wt" => 0,
            "purewt" => 0,
            "diawt" => 0],[
			"type" => "B2B SALES",
            "gross_wt" => 0,
            "net_wt" => 0,
            "less_wt" => 0,
            "purewt" => 0,
            "diawt" => 0]];
		}



		$metalissue = $this->db->query("SELECT 'METAL ISSUE' as type,IFNULL(SUM(r.issue_metal_pur_wt),'0.000') as purewt,IFNULL(SUM(IF(p.stone_type != 2 ,r.issue_metal_wt,0)),'0.000') as gross_wt,IFNULL(SUM(IF(p.stone_type != 2 ,r.issue_metal_wt,0)),'0.000') as net_wt,

			IFNULL(SUM(IF(p.stone_type = 2 ,r.issue_metal_wt, 0)),'0.000') as diawt

			FROM  ret_karigar_metal_issue iss

			LEFT JOIN ret_karigar_metal_issue_details r on r.issue_met_parent_id=iss.met_issue_id

			LEFT JOIN ret_purchase_order_items po_itm on po_itm.po_item_id = r.po_item_id

			LEFT JOIN ret_purchase_order po on po.po_id = po_itm.po_item_po_id

			LEFT JOIN ret_product_master p on p.pro_id=r.issu_met_pro_id

			LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id

			WHERE iss.bill_status = 1
			".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
			 and (date(iss.met_issue_date) BETWEEN  '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'  ) ");

		$metal_issue = $metalissue->result_array();

        $return_data = array_merge($purchase_return,$metal_issue);

		// print_r($this->db->last_query());exit;

		return $return_data;

	}

	function getMetalwiseApprovalTransactionList($from_date,$to_date,$id_branch){

		$FromDt = date('Y-m-d',strtotime($from_date));
		$ToDt = date('Y-m-d',strtotime($to_date));

        $return_data = array();



        $sql = $this->db->query("SELECT s.customer_id, s.firstname,

                                SUM(if(ifnull(s.metal, '') = 'GOLD', (s.IssuePureWt - s.ReceiptPureWt), 0)) as goldwt,

                                SUM(if(ifnull(s.metal, '') = 'SILVER', (s.IssuePureWt - s.ReceiptPureWt), 0)) as silverwt,

                                SUM(if(ifnull(s.metal, '') = 'PLATINUM', (s.IssuePureWt - s.ReceiptPureWt), 0)) as platinumwt,

                                sum((round(s.Debit,2) - round(s.Credit,2))) as balanceamt

                                    from (SELECT customer_id, kr.firstname,

                                                sum(CAST(COALESCE(CASE WHEN (trans_type = 2 AND trans_screen_id != 6) THEN ifnull(trans_amount,0) END,0) AS DECIMAL(13,2))) as Debit,

                                                sum(round(COALESCE(CASE WHEN (trans_type = 1) THEN trans_amount

                                                                            WHEN (trans_type = 2 AND trans_screen_id = 6) THEN trans_amount

                                                                            END,0), 2)) as Credit,

                                                sum(round(COALESCE(CASE WHEN (trans_rec_type = 1 AND trans_type = 2) THEN purewt END,0), 3)) as IssuePureWt,



                                                sum(round(COALESCE(CASE WHEN (trans_rec_type = 1 AND trans_type = 1) THEN purewt END,0), 3)) as ReceiptPureWt,



                                                met.metal



                                from ret_view_supplier_approval_ledger as trans



                                LEFT JOIN ret_karigar as kr ON kr.id_karigar = trans.customer_id



                                LEFT JOIN metal as met ON met.id_metal = trans.id_metal



                                WHERE date(trans_date) <= '$ToDt' AND kr.id_karigar IS NOT NULL



                                GROUP BY customer_id, trans.id_metal  order by customer_id ASC) s GROUP BY s.customer_id");

        //echo $this->db->last_query();exit;



        $return_data = $sql->result_array();

        return $return_data;



    }


	function get_crdr_details($from_date,$to_date,$id_branch)
	{

		$sql = $this->db->query("SELECT

        If(ct.transtype = 1,'Credit','Debit') as transtype,IFNULL(SUM(If(ct.transtype = 1,ct.transamount,0)) ,0) as credit_amount,IFNULL(SUM(If(ct.transtype = 2,ct.transamount,0)) ,0) as debit_amount,0 as balance_amount,IFNULL(ct.naration,'') as naration,

        date_format(ct.transdate,'%d-%m-%Y') as transdate,IFNULL(k.firstname,'') as karigar,

        ct.crdrid

        FROM ret_crdr_note ct

        LEFT JOIN ret_karigar k ON k.id_karigar=ct.supid

        WHERE ct.crdrid IS NOT NULL

		AND (date(ct.transdate) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ");

		// print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_qc_details($from_date,$to_date,$id_branch,$id_metal)
	{

		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		$sql = $this->db->query("SELECT IFNULL(SUM(d.failed_pcs),0) as qc_failed_pcs,IFNULL(SUM(d.failed_gwt),'0.000') as qc_failed_gwt,IFNULL(SUM(d.failed_lwt),'0.000')  as qc_failed_lwt,IFNULL(SUM(d.failed_nwt),'0.000')  as qc_failed_nwt
        FROM ret_purchase_order_items p
        LEFT JOIN ret_po_qc_issue_details d ON d.po_item_id=p.po_item_id
        LEFT JOIN ret_po_qc_issue_process i ON i.qc_process_id=d.qc_process_id
		LEFT JOIN ret_category c ON c.id_ret_category = p.po_item_cat_id
        WHERE d.po_item_id IS NOT NULL
		".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
		AND (date(i.created_at) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ");

		// print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_dashboard_breakeven_details($from_date, $to_date, $id_branch, $id_metal, $rep_type = 1, $fin_year = 0)
	{
	    $return_data = array();
		// $multiple_id_metal = implode(' , ', $id_metal);
        // if($multiple_id_metal != '')
		// {
		// 	$id_metal = $multiple_id_metal;
		// }else{
		// 	$id_metal = $id_metal;
		// }
	    if($rep_type == 0){

	         $sql = $this->db->query("SELECT IFNULL(sum(d.gross_wt),0.00) as sale_gwt,
                            IFNULL(sum(d.net_wt),0.00) as sale_nwt, IFNULL(sum(d.item_cost),0.00) as sale_amount, IFNULL(SUM(dia.diawt),0.00) as sale_diawt
                            from ret_billing b
                            Left JOIN ret_bill_details d on d.bill_id = b.bill_id
                            LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
                            LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
                            LEFT JOIN (SELECT IFNULL(SUM(s.wt),0) as diawt,IFNULL(SUM(s.price),0) as stn_amt,s.bill_det_id
                    			FROM ret_billing_item_stones s
                    			LEFT JOIN ret_bill_details d ON d.bill_det_id = s.bill_det_id
                    			LEFT JOIN ret_billing b on b.bill_id=d.bill_id
                    			LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
                    			LEFT JOIN ret_uom m ON m.uom_id = s.uom_id
                    			WHERE st.stone_type = 1 GROUP By s.bill_det_id ) dia ON dia.bill_det_id = d.bill_det_id
                            WHERE b.bill_id is not null and b.bill_status = 1 and b.is_eda = 1
                    		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
                            ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch in (".$id_branch.")" :'' )."
                             ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
                             ");



	    }else{

	        if(empty($fin_year)){
	            $data = $this->db->query("SELECT date(fin_year_from) as fin_year_from, fin_year_to FROM ret_financial_year WHERE fin_status = 1 ORDER BY fin_id DESC LIMIT 1");
	        }else{
	            $data = $this->db->query("SELECT  date(fin_year_from) as fin_year_from, fin_year_to FROM ret_financial_year WHERE fin_id = $fin_year");
	        }

			//print_r($data->row()->fin_year_from);exit;

	        if(!empty($data->row()->fin_year_from) &&  !empty($data->row()->fin_year_to)){
	            $from_date = $data->row()->fin_year_from;
	            $to_date = $data->row()->fin_year_to;

	            $sql = $this->db->query("SELECT IFNULL(sum(d.gross_wt),0.00) as sale_gwt,
                            IFNULL(sum(d.net_wt),0.00) as sale_nwt, IFNULL(sum(d.item_cost),0.00) as sale_amount, IFNULL(sum(dia.diawt),0.00) as sale_diawt
                            from ret_billing b
                            Left JOIN ret_bill_details d on d.bill_id = b.bill_id
                            LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
                            LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
                            LEFT JOIN (SELECT IFNULL(sum(s.wt),0) as diawt,IFNULL(sum(s.price),0) as stn_amt,s.bill_det_id
                    			FROM ret_billing_item_stones s
                    			LEFT JOIN ret_bill_details d ON d.bill_det_id = s.bill_det_id
                    			LEFT JOIN ret_billing b on b.bill_id=d.bill_id
                    			LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
                    			LEFT JOIN ret_uom m ON m.uom_id = s.uom_id
                    			WHERE st.stone_type = 1 GROUP By s.bill_det_id ) dia ON dia.bill_det_id = d.bill_det_id
                            WHERE b.bill_id is not null and b.bill_status = 1 and b.is_eda = 1
                    		".($id_metal!='' && $id_metal !='0' ? " and cat.id_metal in (".$id_metal.")" :'' )."
                            ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch in (".$id_branch.")" :'' )."
                             ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
                             ");
	        }
	    }


        $br_even_log = $this->db->query("SELECT sum(IFNULL((brevn_log_gold_val),0)) as goldwt,

                                            sum(IFNULL((brevn_log_silver_val),0)) as silverwt,

                                            sum(IFNULL((brevn_log_dia_val),0)) as diawt

                                            FROM `ret_breakeven_logs` b

                               WHERE 1 ".($id_branch!='' && $id_branch !='0' ? " and b.brevn_log_branchid in (".$id_branch.")" :'' )."

                               ORDER BY brevn_log_id DESC LIMIT 1

                                ");

								//echo $this->db->last_query();exit;

								$gold_wt = $br_even_log->row()->goldwt;

								$silverwt = $br_even_log->row()->silverwt;

								$diawt = $br_even_log->row()->diawt;


								if($rep_type == 1){

									$wt = $br_even_log->row();

									$date1=date_create($data->row()->fin_year_from);
									$date2=date_create(date('Y-m-d'));
									$diff=date_diff($date1,$date2);



									$diff =$diff->format("%a");

									//var_dump($diff);exit;


									$gold_wt = $diff *  $gold_wt;

									$silverwt = $diff *  $silverwt;

									$diawt = $diff *  $diawt;

								}

       if($br_even_log->num_rows() > 0 && $sql->num_rows() > 0){
           if($id_metal == 1){
               $return_data = array("breakeven" => $gold_wt, "acchive" => $sql->row()->sale_nwt, "disppercent" => number_format((($sql->row()->sale_gwt / $gold_wt) * 100), 2, '.', ''));
           }else if($id_metal == 2){
               $return_data = array("breakeven" => $silverwt, "acchive" => $sql->row()->sale_nwt, "disppercent" => number_format((($sql->row()->sale_gwt / $silverwt) * 100), 2, '.', ''));
           }else{
               $return_data = array("breakeven" => $diawt, "acchive" => $sql->row()->sale_gwt, "disppercent" => number_format((($sql->row()->sale_gwt / $diawt) * 100), 2, '.', ''));

           }
       }else{
            $return_data = array("breakeven" => 0, "acchive" => 0, "disppercent" => 0);

       }

	   return $return_data;

	}

	function get_rate_fixed_details($from_date,$to_date,$id_branch)
	{

	    $return_data = [];

	    $sql = $this->db->query("SELECT k.firstname as suppliername,IFNULL(SUM(r.rate_fix_wt),0) as rate_fix_wt,IFNULL(AVG(r.rate_fix_rate),0) as rate_fix_rate,IFNULL(SUM(r.total_amount),0) as fixed_amount,p.tot_purchase_wt,IFNULL(SUM(g.grn_purchase_amt),0) as grn_purchase_amt

        FROM ret_po_rate_fix r

        LEFT JOIN ret_purchase_order p ON p.po_id = r.rate_fix_po_item_id

        LEFT JOIN ret_grn_entry g ON g.grn_id = p.po_grn_id

        LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id

        Where g.grn_ref_no IS NOT NULL and r.bill_status = 1

        ".($from_date != '' && $to_date !='' ? ' and date(g.grn_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."

        ");

        $return_data[] = $sql->row_array();

		$sql1= $this->db->query("SELECT COALESCE(SUM(src.amount),0) as fixed_amount,COALESCE(AVG(src.rate_per_gram),0) as rate_fix_rate ,COALESCE(SUM(src.weight),0) as rate_fix_wt

        FROM ret_supplier_rate_cut src

        LEFT JOIN metal c ON c.id_metal =  src.id_metal

        WHERE id_supplier_rate_cut IS NOT NULL and src.rate_cut_type= 2 and src.conversion_type = 1

		".($id_branch!='' ? " and id_branch=".$id_branch."" :'')."

        ".($id_metal!='' ? " and id_branch in (".$id_metal.")" :'')."

        ".($from_date!='' && $to_date !='' ? " and (date(src.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')" :'')."");

     //   print_r($this->db->last_query());exit;

	    $return_data[] = $sql1->row_array();

		$data['fixed_amount'] = 0;

		$data['rate_fix_rate'] = 0;

		$data['rate_fix_wt'] = 0;

		$data['grn_purchase_amt'] = 0;

		foreach( $return_data as $r ){

			$data['fixed_amount'] += $r['fixed_amount'];

			$data['rate_fix_rate']+= $r['rate_fix_rate'];

			$data['rate_fix_wt']+= $r['rate_fix_wt'];

			$data['grn_purchase_amt'] += $r['grn_purchase_amt'];
		}
		if(count($return_data)> 1){
			$data['rate_fix_rate']= $data['rate_fix_rate']/2;
		}
        //echo "<pre>";print_r($return_data);exit;

        return $data;

	}

	function get_rate_unfixing_details($from_date,$to_date,$id_branch,$id_metal)
	{

	    $return_data = [];

		$unfixing_data = [];

		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

	    $sql = $this->db->query("SELECT k.id_karigar,p.po_id,COALESCE(SUM(r.item_pure_wt),0) as pure_wt,k.firstname as supplier_name,
		COALESCE(SUM(ret.pur_ret_pur_wt),0) as pur_ret_pur_wt,
		COALESCE(SUM(rfp.ratefixwt),0) as ratefixwt,COALESCE( SUM(r.item_pure_wt) - SUM(ret.pur_ret_pur_wt) - SUM(rfp.ratefixwt),0) as balance_weight
        FROM ret_purchase_order p
        LEFT JOIN ret_purchase_order_items r ON r.po_item_po_id = p.po_id
	    LEFT JOIN ret_karigar k ON k.id_karigar = p.po_karigar_id
        LEFT JOIN ret_category c ON c.id_ret_category = r.po_item_cat_id
        left join metal m on m.id_metal=c.id_metal

        LEFT JOIN (SELECT IFNULL(SUM(rfx.rate_fix_wt),0) as ratefixwt,rfx.rate_fix_po_item_id
        FROM ret_po_rate_fix rfx
        GROUP BY rfx.rate_fix_po_item_id) as rfp ON rfp.rate_fix_po_item_id = p.po_id

		LEFT JOIN(SELECT pitm.po_item_po_id,IFNULL(SUM(itm.pur_ret_gwt),0) as ret_gwt,IFNULL(SUM(itm.pur_ret_pur_wt),0) as pur_ret_pur_wt
		FROM ret_purchase_return r
		LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
		LEFT JOIN ret_purchase_order_items pitm ON pitm.po_item_id = itm.pur_ret_po_item_id
		WHERE r.bill_status = 1
		GROUP BY pitm.po_item_po_id) as ret ON ret.po_item_po_id = p.po_id

        WHERE p.isratefixed = 0 AND rfp.rate_fix_po_item_id IS NULL

		".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."

		and p.is_suspense_stock = 0 and p.is_approved = 1 and p.bill_status=1

        GROUP BY k.id_karigar ");

		$sql2 = $this->db->query("SELECT rc.id_karigar,rc.weight as pure_wt,k.firstname as supplier_name,IFNULL(SUM(ratefix.ratefixwt),0) as ratefixwt,
        IFNULL(SUM(rc.weight - IFNULL(ratefix.ratefixwt,0)-IFNULL(ret.pur_ret_pur_wt,0)),0) as balance_weight,
        IFNULL(SUM(rc.weight),0) as tot_purchase_wt,'0' as pur_ret_pur_wt
        FROM ret_supplier_rate_cut rc
        LEFT JOIN ret_purchase_order po on po.po_id = rc.po_id
        LEFT JOIN ret_product_master p ON p.pro_id = rc.id_product
        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id
        LEFT JOIN ret_karigar k ON k.id_karigar = rc.id_karigar
        LEFT JOIN(SELECT IFNULL(SUM(rfx.rate_fix_wt),0) as ratefixwt,rfx.rate_fix_po_item_id
        FROM ret_po_rate_fix rfx
        GROUP BY rfx.rate_fix_po_item_id) as ratefix ON ratefix.rate_fix_po_item_id = po.po_id
        LEFT JOIN(SELECT pitm.po_item_po_id,IFNULL(SUM(itm.pur_ret_gwt),0) as ret_gwt,IFNULL(SUM(itm.pur_ret_pur_wt),0) as pur_ret_pur_wt
        FROM ret_purchase_return r
        LEFT JOIN ret_purchase_return_items itm ON itm.pur_ret_id = r.pur_return_id
        LEFT JOIN ret_purchase_order_items pitm ON pitm.po_item_id = itm.pur_ret_po_item_id
        WHERE r.bill_status = 1
        GROUP BY pitm.po_item_po_id) as ret ON ret.po_item_po_id = po.po_id
        WHERE rc.conversion_type = 2 and rc.status = 1
		".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY rc.id_karigar");

		$result1 = $sql->result_array();

		$result2 = $sql2->result_array();

		$return_data = array_merge($result1,$result2);

		foreach($return_data as $r){


			$r['pure_wt'] = $unfixing_data[$r['id_karigar']]['pure_wt'] + $r['pure_wt'];

			$r['pur_ret_pur_wt'] = $unfixing_data[$r['id_karigar']]['pur_ret_pur_wt'] + $r['pur_ret_pur_wt'];

			$r['ratefixwt'] = $unfixing_data[$r['id_karigar']]['ratefixwt'] + $r['ratefixwt'];

			$r['balance_weight'] = $unfixing_data[$r['id_karigar']]['balance_weight'] + $r['balance_weight'];

			//echo $amt." AMount ".$r['amount']." pament mode ".$r['payment_mode']." Final Amount ".$paymentwise_sales_records[$r['payment_mode']]['amount']."<br>";

			$unfixing_data[$r['id_karigar']] = $r;

		}


        return ($unfixing_data);

	}


	function  get_accountstock_inwards_details($from_date, $to_date,$id_branch,$id_metal = "")
    {
        $returnData = [];
        $return_array = array();

        // $op_date= date('Y-m-d',(strtotime('-1 day',strtotime($from_date))));

        $multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

		$multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}

        // Sales Return query
        $receipt_type[2] =  $sql = $this->db->query("SELECT 'Sales Return' as type,IFNULL(SUM(inw.gross_wt),0) as inw_gwt,IFNULL(SUM(inw.net_wt),0) as inw_nwt,IFNULL(SUM(inw_dia.diawt),0) as inw_diawt

        FROM ret_branch_transfer b

        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
        GROUP BY t.transfer_id) as inw ON inw.transfer_id = b.branch_transfer_id

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
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
       ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id

        WHERE b.transfer_item_type = 3 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
        ".($id_branch!='' ? " and b.transfer_from_branch=".$id_branch."" :'')."

		");

        //print_r($this->db->last_query());exit;

        // PartlySales query
        $receipt_type[3] =  $sql = $this->db->query("SELECT 'Partly Sales' as type, IFNULL(SUM(inw.gross_wt),0) as inw_gwt,IFNULL(SUM(inw.net_wt),0) as inw_nwt,IFNULL(SUM(inw_dia.diawt),0) as inw_diawt

        FROM ret_branch_transfer b
        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 0
        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id
        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 3
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
        GROUP BY t.transfer_id) as inw ON inw.transfer_id = b.branch_transfer_id


        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(s.wt),0) as diawt
        FROM ret_brch_transfer_old_metal t
        LEFT JOIN ret_bill_details det ON det.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_billing_item_stones s ON s.bill_det_id = det.bill_det_id
        LEFT JOIN ret_billing bill ON bill.bill_id = det.bill_id
        LEFT JOIN ret_product_master p ON p.pro_id = det.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
        WHERE bill.bill_status = 1 AND det.status = 2 AND st.stone_type = 1 and det.is_non_tag = 0
        and (date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
       ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id

        WHERE b.transfer_item_type = 3 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
        ".($id_branch!='' ? " and b.transfer_from_branch=".$id_branch."" :'')."");


        //NonTag return query
        $receipt_type[4] = $this->db->query("SELECT 'NonTag return' as type,
		IFNULL(SUM(inw.gross_wt),0) as inw_gwt,IFNULL(SUM(inw.net_wt),0) as inw_nwt,IFNULL(SUM(inw_dia.diawt),0) as inw_diawt
        FROM ret_branch_transfer b
        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch




        LEFT JOIN(SELECT t.transfer_id,IFNULL(SUM(t.gross_wt),0) as gross_wt,IFNULL(SUM(t.net_wt),0) as net_wt
        FROM ret_brch_transfer_old_metal t
        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id =t.transfer_id AND brch.status = 4 and t.is_non_tag = 1
        LEFT JOIN ret_bill_details d ON d.bill_det_id = t.sold_bill_det_id
        LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE t.item_type = 2
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
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
        and (date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY t.transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id


        WHERE b.transfer_item_type = 3 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
        ".($id_branch!='' ? " and b.transfer_from_branch=".$id_branch."" :'')."");


        //H.o Other Issue Query

        $receipt_type[5] = $this->db->query("SELECT 'Other Issue' as type,

		IFNULL(SUM(inw.gross_wt),0) as inw_gwt,IFNULL(SUM(inw.net_wt),0) as inw_nwt,IFNULL(SUM(inw_dia.diawt),0) as inw_diawt

        FROM ret_branch_transfer b

        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch



        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(tag.gross_wt),0) as gross_wt,IFNULL(SUM(tag.net_wt),0) as net_wt

        FROM ret_brch_transfer_tag_items t

        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id

        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4

        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id

        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id

        WHERE tag.tag_status = 3  AND brch.is_other_issue = 1 AND brch.status = 4

        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."

        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."

        GROUP BY brch.branch_transfer_id) as inw ON inw.transfer_id = b.branch_transfer_id



        LEFT JOIN (SELECT t.transfer_id,IFNULL(SUM(st.wt),0) as diawt

        FROM ret_brch_transfer_tag_items t

        LEFT JOIN ret_taging tag ON tag.tag_id = t.tag_id

        LEFT JOIN ret_taging_stone st ON st.tag_id = tag.tag_id

        LEFT JOIN ret_stone s ON s.stone_id = st.stone_id

        LEFT JOIN ret_branch_transfer brch ON brch.branch_transfer_id = t.transfer_id AND brch.is_other_issue = 1 AND brch.status = 4

        LEFT JOIN ret_product_master p ON p.pro_id = tag.product_id

        LEFT JOIN ret_category c ON c.id_ret_category = p.cat_id

        WHERE tag.tag_status = 3 AND s.stone_type = 1 AND brch.is_other_issue = 1 AND brch.status = 4

        and (date(brch.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."

        ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."

        GROUP BY brch.branch_transfer_id) as inw_dia ON inw_dia.transfer_id = b.branch_transfer_id


        WHERE b.is_other_issue = 1 AND b.transfer_item_type = 1 AND b.status = 4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."

        ".($id_branch!='' ? " and b.transfer_from_branch=".$id_branch."" :'')."");


        //Nontag Other issue query
        $receipt_type[6] = $this->db->query("SELECT 'Nontag Other issue' as type,

		IFNULL(SUM(inw.gross_wt),0) as inw_gwt,IFNULL(SUM(inw.net_wt),0) as inw_nwt,0 as inw_diawt

        from ret_branch_transfer b
        LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

        LEFT JOIN (SELECT bt.branch_transfer_id,IFNULL(SUM(bt.grs_wt),0) as gross_wt,IFNULL(SUM(bt.net_wt),0) as net_wt
        FROM ret_branch_transfer bt
        LEFT JOIN ret_nontag_item nt on nt.id_nontag_item = bt.id_nontag_item
        LEFT JOIN ret_product_master p ON p.pro_id = nt.product
        LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
        WHERE bt.is_other_issue = 1 and bt.transfer_item_type = 2 and bt.status=4
        AND (date(bt.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
        ".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
            ".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
        GROUP BY bt.branch_transfer_id) as inw on inw.branch_transfer_id = b.branch_transfer_id


        WHERE b.is_other_issue = 1 and b.transfer_item_type = 2 and b.status=4
        ".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
        ".($id_branch!='' ? " and b.transfer_from_branch=".$id_branch."" :'')."
        ");

		//OLD GOLD query
		$receipt_type[7] = $this->db->query("SELECT 'OLD GOLD' as type,

		IFNULL(SUM(inw.gross_wt),0) as inw_gwt,IFNULL(SUM(inw.net_wt),0) as inw_nwt,0 as inw_diawt

		from ret_branch_transfer b
		LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

		LEFT JOIN (SELECT bt.branch_transfer_id,IFNULL(SUM(old.gross_wt),0) as gross_wt,IFNULL(SUM(old.net_wt),0) as net_wt
		FROM ret_branch_transfer bt
		LEFT JOIN  ret_brch_transfer_old_metal old on old.transfer_id  = bt.branch_transfer_id
		WHERE old.transfer_id IS NOT NULL and bt.status=4
		AND (date(bt.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		GROUP BY bt.branch_transfer_id) as inw on inw.branch_transfer_id = b.branch_transfer_id


		WHERE  b.transfer_item_type = 3 and b.status=4
		".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
		".($id_branch!='' ? " and b.transfer_from_branch=".$id_branch."" :'')."
		");

		$receipt_type[8] = $this->db->query("SELECT 'REPAIR ORDER' as type,

		IFNULL(SUM(inw.gross_wt),0) as inw_gwt,IFNULL(SUM(inw.net_wt),0) as inw_nwt,0 as inw_diawt

		from ret_branch_transfer b
		LEFT JOIN branch br ON br.id_branch = b.transfer_from_branch

		LEFT JOIN (SELECT bt.branch_transfer_id,IFNULL(SUM(ordetails.weight),0) as gross_wt,IFNULL(SUM(ordetails.net_wt),0) as net_wt,0 as inw_diawt
		FROM ret_branch_transfer bt
		LEFT JOIN  ret_bt_order_log ord on ord.branch_transfer_id  = bt.branch_transfer_id
		LEFT JOIN customerorderdetails ordetails ON ordetails.id_orderdetails = ord.id_orderdetails
		LEFT JOIN customerorder cus ON cus.id_customerorder = ordetails.id_customerorder
		LEFT JOIN ret_product_master p ON p.pro_id = ordetails.id_product
		LEFT JOIN ret_category c on c.id_ret_category = p.cat_id
		WHERE ord.branch_transfer_id IS NOT NULL and bt.status=4
		AND (date(bt.dwnload_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		".($id_category!='' && $id_category !='0' ? " and p.cat_id in (".$id_category.") " :'' )."
			".($id_metal!='' && $id_metal !='0' ? " and c.id_metal in (".$id_metal.") " :'' )."
		GROUP BY bt.branch_transfer_id) as inw on inw.branch_transfer_id = b.branch_transfer_id


		WHERE  b.transfer_item_type = 3 and b.status=4
		".($data['bt_code']!='' ? " and b.branch_trans_code=".$data['bt_code']."" :'')."
		".($id_branch!='' ? " and b.transfer_from_branch=".$id_branch."" :'')."
		");
        //echo $this->db->last_query();exit;



        $return_array = array_merge( $receipt_type[2]->result_array(),  $receipt_type[3]->result_array(), $receipt_type[4]->result_array(),$receipt_type[5]->result_array(),$receipt_type[6]->result_array(),$receipt_type[7]->result_array(),$receipt_type[8]->result_array());


         //print_r($this->db->last_query());exit;


        return $return_array;
    }


	function get_rate_cut_details($from_date, $to_date,$id_branch,$id_metal)

    {
		$multiple_id_metal = implode(' , ', $id_metal);
        if($multiple_id_metal != '')
		{
			$id_metal = $multiple_id_metal;
		}else{
			$id_metal = $id_metal;
		}

        $sql= $this->db->query("SELECT COALESCE(SUM(src.amount),0) as amount,COALESCE(AVG(src.rate_per_gram),0) as rate_per_gram ,COALESCE(SUM(src.weight),0) as weight

        FROM ret_supplier_rate_cut src

        LEFT JOIN metal c ON c.id_metal =  src.id_metal

        WHERE id_supplier_rate_cut IS NOT NULL and src.rate_cut_type= 2 and src.conversion_type = 1

		".($id_branch!='' ? " and id_branch=".$id_branch."" :'')."

        ".($id_metal!='' ? " and id_metal in (".$id_metal.")" :'')."

        ".($from_date!='' && $to_date !='' ? " and (date(src.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')" :'')."");

     //   print_r($this->db->last_query());exit;

        return $sql->row_array();

    }







    function get_rate_cut_profit_loss($from_date, $to_date, $id_branch)
    {
        $branch = is_array($id_branch) ? implode(',', $id_branch) : $id_branch;
        
        $sql = $this->db->query("SELECT 
            DATE_FORMAT(src.date_add, '%d-%m-%Y') as rate_cut_date,
            (SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add)) as rate_cut_rate,
            IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0) as current_bullion_rate,
            (IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0) - (SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add))) as rate_deviation,
            CASE 
                WHEN ((SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add)) - IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0)) < 0 THEN 'profit'
                WHEN ((SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add)) - IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0)) > 0 THEN 'loss'
                ELSE 'neutral'
            END as profit_loss_status,
            CASE 
                WHEN IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0) > 0 THEN 
                    ROUND(((IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0) - (SELECT AVG(rate_per_gram) 
             FROM ret_supplier_rate_cut 
             WHERE status = 1 
             AND DATE(date_add) = DATE(src.date_add))) / IFNULL((SELECT goldrate_24ct 
             FROM metal_rates 
             WHERE DATE(updatetime) <= DATE(src.date_add) 
             AND goldrate_24ct > 0
             ORDER BY updatetime DESC 
             LIMIT 1), 0)) * 100, 2)
                ELSE 0
            END as profit_percentage
        FROM ret_supplier_rate_cut src
        LEFT JOIN ret_purchase_order po ON po.po_id = src.po_id
        WHERE src.status = 1
        AND src.ref_no IS NOT NULL
        ".($from_date != '' ? 
            " AND DATE(src.date_add) <= '".date('Y-m-d', strtotime($from_date))."'" : '')."
        group by DATE(src.date_add) ORDER BY DATE(src.date_add) DESC");
		// print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
	function get_profile_settings($id_profile)
	{
		$sql = $this->db->query("SELECT * FROM `profile` WHERE id_profile = " . $id_profile);
		return $sql->row_array();
	}

	// ========== MD Approval Dashboard Methods ==========

	function get_md_pending_orders()
	{
		$sql = $this->db->query("SELECT c.id_customerorder, c.pur_no, DATE_FORMAT(c.order_date,'%d-%m-%Y') as order_date,
			c.order_pcs, c.order_approx_wt, c.order_status, c.id_karigar,
			CONCAT(k.firstname,' ',IFNULL(k.lastname,'')) as karigar_name,
			IFNULL(k.contactno1,'') as karigar_mobile,
			IF(c.order_type=1,'Stock Order',IF(c.order_type=2,'Customer Order',IF(c.order_type=3,'Customer Repair',IF(c.order_type=4,'Stock Repair','')))) as order_type_label,
			IF(oc.id_cart_order IS NOT NULL, 'Reorder', 'Direct Order') as po_source,
			m.order_status as status_label, m.color as status_color,
			IFNULL(emp.firstname,'') as created_by_name,
			IFNULL(DATE_FORMAT(dd.smith_due_date,'%d-%m-%Y'),'') as due_date,
			IFNULL(prd.products,'') as products,
			IFNULL(prd.weight_range,'') as weight_range
			FROM customerorder c
			LEFT JOIN ret_karigar k ON k.id_karigar = c.id_karigar
			LEFT JOIN order_status_message m ON m.id_order_msg = c.order_status
			LEFT JOIN employee emp ON emp.id_employee = c.order_taken_by
			LEFT JOIN (SELECT id_customerorder, MIN(smith_due_date) as smith_due_date FROM customerorderdetails GROUP BY id_customerorder) dd ON dd.id_customerorder = c.id_customerorder
			LEFT JOIN (
				SELECT d.id_customerorder,
				GROUP_CONCAT(DISTINCT p.product_name SEPARATOR ', ') as products,
				GROUP_CONCAT(DISTINCT CONCAT(IFNULL(wr.wc_from_weight,''),'-',IFNULL(wr.wc_to_weight,'')) SEPARATOR ', ') as weight_range
				FROM customerorderdetails d
				LEFT JOIN ret_product_master p ON p.pro_id = d.id_product
				LEFT JOIN ret_design_weight_range_wc wr ON wr.id_wc = d.id_weight_range
				GROUP BY d.id_customerorder
			) prd ON prd.id_customerorder = c.id_customerorder
			LEFT JOIN (
				SELECT DISTINCT od.id_customerorder, oc2.id_cart_order
				FROM customerorderdetails od
				INNER JOIN order_cart oc2 ON oc2.id_orderdetails = od.id_orderdetails
			) oc ON oc.id_customerorder = c.id_customerorder
			WHERE c.pur_no IS NOT NULL AND c.order_status = 10
			ORDER BY c.order_date DESC");
		return $sql->result_array();
	}

	function md_approve_order($id_customerorder)
	{
		$this->db->where('id_customerorder', $id_customerorder);
		$this->db->update('customerorder', array('order_status' => 11));
		return $this->db->affected_rows() > 0;
	}

	function md_reject_order($id_customerorder, $reason)
	{
		$this->db->where('id_customerorder', $id_customerorder);
		$this->db->update('customerorder', array('order_status' => 8, 'reject_reason' => $reason));
		return $this->db->affected_rows() > 0;
	}

	function md_bulk_approve_orders($order_ids)
	{
		$this->db->where_in('id_customerorder', $order_ids);
		$this->db->update('customerorder', array('order_status' => 11));
		return $this->db->affected_rows();
	}

	function save_po_dashboard_comment($po_id, $comment)
	{
		$this->db->where('po_id', $po_id);
		$this->db->update('ret_purchase_order', array('dashboard_comment' => $comment));
		return $this->db->affected_rows() >= 0;
	}

}
?>