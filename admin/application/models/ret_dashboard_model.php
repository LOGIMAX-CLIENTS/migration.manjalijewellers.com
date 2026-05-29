<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class ret_dashboard_model extends CI_Model

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



    function get_estimation($from_date,$to_date)

    {

	    		$sql=$this->db->query("select count(est.estimation_id) as estimation

	    							 from ret_estimation est

	    					          where(date(est.created_time) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')");

	    		 return $sql->row_array();

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

										WHERE estitm.purchase_status = 0 AND date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'')." GROUP BY esti_id) as unsold) as unsold");

		//print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_dashboard_estimation_details($from_date,$to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT estimation_id, esti_no, br.name as branchname, est.id_branch as branchid,

		if(esti.purchase_status = 1, 'Sold', if(esti.purchase_status = 0, 'Unbilled', if(esti.purchase_status = 2, 'Returned', 'In Process'))) as purchase_status,

		cus.firstname as cusname,

		cus.mobile as cusmobile, total_cost as estamount

		FROM ret_estimation as est

		LEFT JOIN ret_estimation_items as esti ON esti.esti_id = est.estimation_id

		LEFT JOIN branch as br ON br.id_branch = est.id_branch

		LEFT JOIN customer as cus ON cus.id_customer = est.cus_id

		WHERE est.esti_for=1 and date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

		".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'')."

		GROUP BY estimation_id");

		return $sql->result_array();

	}

	/*function get_dashboard_billings($from_date,$to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT count(*) as bills, ifnull(sum(tot_bill_amount),0) as billamount FROM ret_billing as bill WHERE date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' and bill.bill_status=1 ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')." ");

		return $sql->row_array();

	}*/



	function get_dashboard_billings($from_date,$to_date,$id_branch)

	{

		   /*$sql = $this->db->query("SELECT IFNULL(sum(bill_det.net_wt),0) as gold_wt,ifnull(count(bill.bill_id),0) as gold_count,

		   ifnull(sum(bill_det.item_cost),0) as gold_amt,ifnull(sum(bill_det.piece),0) as gold_piece

		   from ret_billing as bill

           left JOIN ret_bill_details  as bill_det on(bill_det.bill_id=bill.bill_id)

           left join ret_product_master as pro on(pro.pro_id=bill_det.product_id)

           left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

           left join metal as m on(m.id_metal=cat.id_metal)

           left join branch b on (b.id_branch=bill.id_branch)

           where bill.bill_status=1 and m.id_metal=1  ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')."

           and date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'");*/

            $sql = $this->db->query("SELECT m.metal_code,m.id_metal,m.metal,IFNULL(sum(bill_det.net_wt),0) as wt,ifnull(count(bill.bill_id),0) as count,

		   ifnull(sum(bill_det.item_cost),0) as amt,ifnull(sum(bill_det.piece),0) as piece

		   from ret_billing as bill

           left JOIN ret_bill_details  as bill_det on(bill_det.bill_id=bill.bill_id)

           left join ret_product_master as pro on(pro.pro_id=bill_det.product_id)

           left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

           left join metal as m on(m.id_metal=cat.id_metal)

           left join branch b on (b.id_branch=bill.id_branch)

           where bill.bill_status=1  ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')."

           and date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' GROUP BY  m.metal;");
			// print_r($this->db->last_query());exit;

		   $result =$sql->result_array();
		   foreach( $result as $key=>$val){
                $data[$val['metal']]=$val;
		   }
           return $data;

	}

	function get_dashboard_billings_sl_wt($from_date,$to_date,$id_branch)

	{

		   $sql = $this->db->query("SELECT IFNULL(sum(bill_det.net_wt),0) as silver_wt,ifnull(count(bill.bill_id),0) as silver_count,

		   ifnull(sum(bill_det.item_cost),0) as silver_amt,ifnull(sum(bill_det.piece),0) as silver_piece

		   from ret_billing as bill

           left JOIN ret_bill_details  as bill_det on(bill_det.bill_id=bill.bill_id)

           left join ret_product_master as pro on(pro.pro_id=bill_det.product_id)

           left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

           left join metal as m on(m.id_metal=cat.id_metal)

           left join branch b on (b.id_branch=bill.id_branch)

           where bill.bill_status=1 and m.id_metal=2  ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')."

           and date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'");

			// print_r($this->db->last_query());exit;

		   return $sql->row_array();

	}

	function get_dashboard_billings_mrp($from_date,$to_date,$id_branch)

	{

		 $sql = $this->db->query("SELECT IFNULL(sum(bill_det.item_cost),0) as mrp,ifnull(count(bill.bill_id),0) as mrp_count,ifnull(sum(bill_det.piece),0) as mrp_piece ,

		 ifnull(sum(bill_det.net_wt),0) as mrp_wt

		 from ret_billing as bill

         left JOIN ret_bill_details  as bill_det on(bill_det.bill_id=bill.bill_id)

         left join ret_product_master as pro on(pro.pro_id=bill_det.product_id)

         left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

         left join metal as m on(m.id_metal=cat.id_metal)

         left join branch b on (b.id_branch=bill.id_branch)

         where bill.bill_status=1 and pro.sales_mode=1  ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')."

         and date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'");

		   return $sql->row_array();

	}



	function get_dashboard_billings_dia($from_date,$to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT ifnull(count(bill.bill_id),0) as count,bill_stn.stone_id,ifnull(sum(bill_stn.pieces),0) as stone_pieces,

		ifnull(sum(bill_stn.wt),0) as stone_wt,ifnull(sum(bill_stn.price),0) as stone_amt,stn.stone_name

		from ret_billing_item_stones bill_stn

		Left Join ret_billing bill on bill.bill_id=bill_stn.bill_id

		left join ret_stone stn on stn.stone_id=bill_stn.stone_id

		where bill.bill_status=1 AND bill_stn.old_metal_sale_id is null and stn.stone_type=1   ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')."

		and date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'");

		return $sql->row_array();

	}



	function get_dashboard_greentag_det($from_date,$to_date,$id_branch)

    {

        $sql=$this->db->query("SELECT IFNULL(sum(tag.gross_wt),0) as tot_sales_wt,IFNULL(count(d.quantity),0) as tot_piece,IFNULL(sum(b.tot_bill_amount),0) as tot_bill_amt,((IFNULL(gold_wt.g_inct,0)*(SELECT value FROM ret_settings WHERE name = 'emp_sales_incentive_gold_perg'))+(IFNULL(silver_wt.s_inct,0)*((SELECT value from ret_settings where name = 'emp_sales_incentive_silver_perg')))) as incentive

        FROM ret_bill_details d

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

		left join ret_product_master as pro on(pro.pro_id=d.product_id)

        left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

        left join metal as m on(m.id_metal=cat.id_metal)

		left join (SELECT IFNULL(sum(tag.net_wt),0) as g_inct,b.bill_id

                    FROM ret_bill_details d

                    LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

                    LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

                    left join ret_product_master as pro on(pro.pro_id=d.product_id)

                    left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

                    left join metal as m on(m.id_metal=cat.id_metal)

                    WHERE tag.tag_status=1 AND tag.tag_mark=1 and m.id_metal=1

					and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

        			".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'').") as gold_wt on (gold_wt.bill_id=b.bill_id)

		LEFT JOIN (SELECT IFNULL(sum(tag.net_wt),0) as s_inct,b.bill_id

                    FROM ret_bill_details d

                    LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

                    LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

                    left join ret_product_master as pro on(pro.pro_id=d.product_id)

                    left join ret_category as cat on(cat.id_ret_category=pro.cat_id)

                    left join metal as m on(m.id_metal=cat.id_metal)

                    WHERE tag.tag_status=1 AND tag.tag_mark=1 and m.id_metal=2

					and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

        			".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'').") as silver_wt on (silver_wt.bill_id=b.bill_id)



        WHERE tag.tag_status=1 AND tag.tag_mark=1

        and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

        ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."");

        // print_r($this->db->last_query());exit;

          return $sql->row_array();

    }



    function get_dashboard_old_metal_purchase($from_date, $to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT oldmp.metal_type, if(oldmp.metal_type = 1, 'Gold', if(oldmp.metal_type = 2, 'Silver', '')) as type,

		ifnull(sum(net_wt),0) as weight,ifnull(sum(gross_wt),0) as gross_wt, ifnull(sum(rate),0) totalpaid FROM ret_bill_old_metal_sale_details oldmp

		LEFT JOIN ret_billing as bill ON bill.bill_id = oldmp.bill_id

		WHERE date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' and bill.bill_status=1 ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')." GROUP BY oldmp.metal_type");

		return $sql->result_array();

	}



	function get_dashboard_credit_sales($from_date, $to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT count(*) as tot_credit_bill, ifnull(sum(tot_bill_amount-tot_amt_received),0) as tot_due_amount,

								(SELECT ifnull(sum(b.tot_amt_received),0) as creditreceived from ret_billing as b

								  WHERE b.ref_bill_id is not null and b.bill_status=1 and b.bill_type=8 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

								  ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."

								) + (SELECT IFNULL(sum(d.received_amount),0)

								FROM ret_issue_credit_collection_details d
								LEFT JOIN ret_issue_receipt r on r.id_issue_receipt= d.id_issue_receipt
								LEFT JOIN customer cus ON cus.id_customer=r.id_customer
								LEFT JOIN branch br ON br.id_branch=r.id_branch
								WHERE r.type=2 AND r.receipt_type=1 and r.bill_status = 1 AND
								date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'
								".($id_branch!='' && $id_branch>0 ? " and r.id_branch=".$id_branch."" :'')." ) as creditreceived

								FROM ret_billing as bill

								WHERE bill.bill_type != 8 and bill.is_credit=1 AND date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

								and bill.bill_status=1 ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')." ");

		// print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_dashboard_gift_vouchers($from_date, $to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT (SELECT ifnull(sum(amount),0) FROM gift_card as gc WHERE status = 0 AND date(gc.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and gc.id_branch=".$id_branch."" :'').")as tot_issued,

										(SELECT ifnull(sum(amount),0) FROM gift_card as gc WHERE status = 2 AND date(gc.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and gc.id_branch=".$id_branch."" :'').") as tot_utlized,

										(SELECT ifnull(sum(amount),0) FROM gift_card as gc WHERE free_card = 2 AND date(gc.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and gc.id_branch=".$id_branch."" :'').") as tot_sold");

		return $sql->row_array();

	}



	function get_dashboard_bills_clasfications($from_date, $to_date,$id_branch)

	{

		$bills_clasification = array();

		$sql = $this->db->query("SELECT count(newcus.bills) as totalnewcusbill, ifnull(sum(newcus.total_sale_amt),0) as newcusbillsale, ifnull(sum(newcus.total_sale_wt),0) as newcisbillsalewt FROM (SELECT bill_cus_id, count(*) as bills, sum(tot_bill_amount) as total_sale_amt, sum(gross_wt) as total_sale_wt

		FROM ret_billing bil

		LEFT JOIN ret_bill_details as bill_det ON bill_det.bill_id = bil.bill_id

		WHERE date(bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' AND NOT EXISTS(SELECT bill_cus_id FROM ret_billing WHERE date(bill_date) < '".date('Y-m-d',strtotime($from_date))."' GROUP by bill_cus_id)

		GROUP by bill_cus_id) as newcus");



		$newcusbills = $sql->row_array();



		$oldcussql = $this->db->query("SELECT count(oldcus.bills) as totaloldcusbill, ifnull(sum(oldcus.total_sale_amt),0) as oldcusbillsale, ifnull(sum(oldcus.total_sale_wt),0) as oldcusbillsalewt FROM (SELECT bill_cus_id, count(*) as bills, sum(tot_bill_amount) as total_sale_amt, sum(gross_wt) as total_sale_wt

		FROM ret_billing bil

		LEFT JOIN ret_bill_details as bill_det ON bill_det.bill_id = bil.bill_id

		WHERE date(bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' AND NOT EXISTS(SELECT bill_cus_id FROM ret_billing WHERE date(bill_date) < '".date('Y-m-d',strtotime($from_date))."' GROUP by bill_cus_id)

		GROUP by bill_cus_id) as oldcus");

		$oldcusbills = $oldcussql->row_array();

		$bills_clasification = array('totalnewcusbill' => $newcusbills['totalnewcusbill'], 'newcusbillsale' => $newcusbills['newcusbillsale'], 'newcisbillsalewt' => $newcusbills['newcisbillsalewt'], 'totaloldcusbill' => $oldcusbills['totaloldcusbill'], 'oldcusbillsale' => $oldcusbills['oldcusbillsale'], 'oldcusbillsalewt' => $oldcusbills['oldcusbillsalewt']);

		return $bills_clasification;



	}

	function get_dashboard_approval_pendings($from_date, $to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT

		                        (SELECT ifnull(sum(pieces),0)from ret_branch_transfer where status=2 and status!=4 ".($id_branch!='' && $id_branch>0 ?  " and transfer_to_branch=".$id_branch."" :'').") as downloadpending,

		                        (SELECT ifnull(sum(pieces),0)from ret_branch_transfer where status=1 and status!=4 ".($id_branch!='' && $id_branch>0 ?  " and transfer_to_branch=".$id_branch."" :'')." ) as approvalpending

								");

								//print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_dashboard_lot_tag_details($from_date, $to_date, $id_branch)

	{

		$sql = $this->db->query("SELECT

		(SELECT ifnull(SUM(d.no_of_piece),0) FROM ret_lot_inwards l left JOIN ret_lot_inwards_detail d on d.lot_no=l.lot_no WHERE date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and l.created_branch=".$id_branch."" :'').") as lot_pcs,

		(SELECT ifnull(SUM(d.gross_wt),0) FROM ret_lot_inwards l left JOIN ret_lot_inwards_detail d on d.lot_no=l.lot_no WHERE date(l.lot_date) BETWEEN  '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and l.created_branch=".$id_branch."" :'').") as lot_wt,

		(SELECT ifnull(SUM(tag.piece),0)  FROM ret_taging tag WHERE tag.tag_status!=2 AND date(tag.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and tag.id_branch=".$id_branch."" :'').") as tagged_pcs,

		(SELECT ifnull(SUM(tag.gross_wt),0)  FROM ret_taging tag WHERE tag.tag_status!=2 AND date(tag.tag_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and tag.id_branch=".$id_branch."" :'').") as tagged_wt ,

		(SELECT ifnull(SUM(d.no_of_piece),0) FROM ret_lot_inwards l left JOIN ret_lot_inwards_detail d on d.lot_no=l.lot_no WHERE l.stock_type = 2 and date(l.lot_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and l.created_branch=".$id_branch."" :'').") as non_tag_pcs,

		(SELECT ifnull(SUM(d.gross_wt),0) FROM ret_lot_inwards l left JOIN ret_lot_inwards_detail d on d.lot_no=l.lot_no WHERE l.stock_type = 2 and  date(l.lot_date) BETWEEN  '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and l.created_branch=".$id_branch."" :'').") as non_tag_wt


		");

	//	 print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_dashboard_orders_details($from_date, $to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT

								(SELECT count(*) as orderplaced from order_cart as oc WHERE orderstatus = 1 AND date(created_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and oc.id_branch=".$id_branch."" :'')." ) as orderplaced,

								(SELECT count(*) as orderreceived from order_cart as oc WHERE  orderstatus = 0 AND date(created_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and oc.id_branch=".$id_branch."" :'').") as orderreceived,

								(SELECT count(*) as cart from order_cart as oc WHERE orderstatus = 0 AND date(created_on) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ?  " and oc.id_branch=".$id_branch."" :'').") as ordersincart");

		//print_r($this->db->last_query());exit;

		return $sql->row_array();

	}

	function get_dashboard_customer_details($from_date, $to_date)

	{

		$sql = $this->db->query("SELECT id_customer, firstname, cus.mobile as mobile, ifnull(cus_img, '') as cus_img, br.name as branchname, cus.id_branch as branchid,

		profile_complete, if(added_by = 1, 'Admin', if(added_by = 2, 'Mobile' , 'Web')) as jointhrough

		FROM customer as cus LEFT JOIN branch as br ON br.id_branch = cus.id_branch ORDER BY id_customer DESC LIMIT 10");

		return $sql->result_array();

	}

	function get_dashboard_bills_details($from_date, $to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT br.name as branchname, bill.id_branch as branchid, bill_no, cus.firstname as cusname,

		cus.mobile as cusmobile, tot_bill_amount as billamount,

		(CASE

			WHEN bill_type = 1 THEN 'Sales'

			WHEN bill_type = 2 THEN 'Sales & Purchase'

			WHEN bill_type = 3 THEN 'Sales & Return'

			WHEN bill_type = 4 THEN 'Purchase'

			WHEN bill_type = 5 THEN 'Order Advance'

			WHEN bill_type = 6 THEN 'Advance'

			WHEN bill_type = 7 THEN 'Sales Return'

			WHEN bill_type = 8 THEN 'Credit Bill Payment'

			WHEN bill_type = 9 THEN 'Order Delivery'

			WHEN bill_type = 10 THEN 'Chit Pre Close'

			WHEN bill_type = 11 THEN 'Repair Order Delivery'

			WHEN bill_type = 12 THEN 'Supplier Sales Bill'

			WHEN bill_type = 13 THEN 'Sales Transfer'

			WHEN bill_type = 14 THEN 'Sales Ret Transfer'

			ELSE 'Approval stock bill delivery'

		END) as billtype

		FROM ret_billing as bill

		LEFT JOIN branch as br ON br.id_branch = bill.id_branch

		LEFT JOIN customer as cus ON cus.id_customer = bill.bill_cus_id

		WHERE date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

		".($id_branch!='' && $id_branch>0 ?  " and bill.id_branch=".$id_branch."" :'')." ");

		return $sql->result_array();

	}

	function get_dashboard_virturaltag_details($from_date, $to_date,$id_branch)

    {

        $sql = $this->db->query("SELECT

                                        homesale.gross_wt as homesale_wt,

                                        homesale.item_cost as homesale_cost,

                                        homesale.pcs as homesale_pcs,

                                        tagsplit.gross_wt as tagsplit_wt,

                                        tagsplit.item_cost as tagsplit_cost,

                                        tagsplit.pcs as tagsplit_pcs,tagsplit.tag_wt as tagsplit_tag_wt

                                        FROM (SELECT ifnull(SUM(d.gross_wt),0) as gross_wt,

                                              ifnull(SUM(d.item_cost),0) as item_cost,

                                              ifnull(SUM(d.piece),0) as pcs

                                              FROM ret_billing b

                                              LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id

                                              LEFT JOIN ret_estimation_items e ON e.est_item_id=d.esti_item_id

                                              WHERE d.esti_item_id IS NOT null AND d.tag_id is null

                                              AND e.item_type =2 and b.bill_status =1 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

                                              ".($id_branch!='' && $id_branch>0 ?  " and b.id_branch=".$id_branch."" :'').") as homesale,

                                            (SELECT ifnull(SUM(d.gross_wt),0) as gross_wt,

                                                ifnull(SUM(d.item_cost),0) as item_cost,

                                                ifnull(SUM(d.piece),0) as pcs,ifnull(SUM(tag.gross_wt),0) as tag_wt

                                                FROM ret_billing b

                                                LEFT JOIN ret_bill_details d ON d.bill_id = b.bill_id

                                                LEFT JOIN ret_estimation_items e ON e.est_item_id=d.esti_item_id

                                                LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id

                                                LEFT JOIN ret_taging tag on tag.tag_id=d.tag_id

                                                WHERE d.tag_id IS NOT null AND d.is_partial_sale=1 and

                                                b.bill_status = 1 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

                                                ".($id_branch!='' && $id_branch>0 ?  " and b.id_branch=".$id_branch."" :'').") as tagsplit");

                                                // print_r($this->db->last_query());exit;

        return $sql->row_array();

    }



	function get_dashboard_salesreturn_det($from_date,$to_date,$id_branch)

	{

		$sql=$this->db->query("SELECT IFNULL(sum(d.gross_wt),0) as tot_wt, IFNULL(sum(d.piece),0) as tot_pcs from ret_bill_details as d

		left join ret_billing as b on(b.bill_id=d.bill_id)

		where d.status=2 and b.bill_status=1

		and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

		".($id_branch!='' && $id_branch>0 ?  " and b.id_branch=".$id_branch."" :'')." ");

		return $sql->row_array();

	}



	function get_dashboard_cash_abstarct_details($from_date, $to_date,$id_branch){



		$from 	= date('Y-m-d',strtotime($from_date));

		$to 	= date('Y-m-d',strtotime($to_date));

		$d1 	= date_create($from);

		$d2 	= date_create($to);

		$FromDt = date_format($d1,"Y-m-d");

		$ToDt 	= date_format($d2,"Y-m-d");





		$return_data = array("item_details" => array(), "voucher_details" => array(), "chit_details" => array(),"return_details"=>array(),'payment_details'=>array(),'advance_detals'=>array(),'branch_transfer_details'=>array(),'due_details'=>array(),'credit_details'=>array(),'metal_rates'=>array(),"old_matel_details"=>array(),"advance_adjusted"=>array(),"wallet_adjusted"=>array(),"general_adv_details"=>array(),"general_pay"=>array(),"order_adj"=>array(),"home_bill"=>array(),"partly_sale"=>array(),"bill_det"=>array(),"other_expenses"=>array(),"chit_payment_details"=>array());



		//SALES DETAILS

		$items_query = $this->db->query("SELECT ifnull(sum(d.piece),0) as piece,

											ifnull(sum(d.net_wt),0) as net_wt,

											ifnull(sum(d.item_cost),0) as item_cost,

											ifnull(sum(d.item_total_tax),0) as item_total_tax,

											ifnull(sum(d.bill_discount),0) as bill_discount

											From ret_billing b

											Left JOIN ret_bill_details d on d.bill_id=b.bill_id

											WHERE  b.bill_id is not null and b.bill_status=1

											".($FromDt!= '' && $ToDt!='' ? 'and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

											and  d.bill_det_id !='' and (b.bill_type=1 OR b.bill_type=2 OR b.bill_type=3 OR b.bill_type=4 OR b.bill_type=9)

											".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."

										");



		$return_data['item_details'] = $items_query->row_array();



		//PURCHASE DETAILS

		$old_metal_query=$this->db->query("SELECT ifnull(sum(s.gross_wt),0) as gross_wt, ifnull(sum(s.dust_wt),0) as dust_wt,

											ifnull(sum(s.stone_wt),0) as stone_wt, ifnull(sum(s.rate),0) as amount,

											ifnull(sum(s.net_wt),0) as net_wt, count(s.old_metal_sale_id) as tot_pur

											FROM ret_bill_old_metal_sale_details s

											LEFT JOIN ret_billing b on b.bill_id=s.bill_id

											WHERE b.bill_id is not null and b.bill_status = 1

											".($FromDt!= '' && $ToDt!='' ? 'and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

											".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."

										");

		$return_data['old_matel_details'] = $old_metal_query->row_array();





		//SALES RETURN

		$return_details=$this->db->query("SELECT ifnull(sum(d.net_wt),0) as net_wt, ifnull(sum(d.item_cost),0) as item_cost,

										  ifnull(sum(d.item_total_tax),0) as item_total_tax,

										  ifnull(sum(d.bill_discount),0) as bill_discount,

										  ifnull(sum(d.piece),0) as piece

										  FROM ret_billing b

										  LEFT JOIN ret_bill_return_details r ON r.bill_id=b.bill_id

										  LEFT JOIN ret_bill_details d ON d.bill_det_id=r.ret_bill_det_id

										  WHERE d.bill_det_id IS NOT null and b.bill_status = 1

										  ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."

										  ".($FromDt!= '' && $ToDt!='' ? 'and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

										");

		$return_data['return_details'] = $return_details->row_array();



	//PAYMENT DETAILS

	$payment_details = $this->db->query("SELECT ifnull(sum(p.payment_amount),0) as payment_amount, p.payment_mode

	FROM ret_billing_payment p

	LEFT JOIN ret_billing b on b.bill_id=p.bill_id

	where b.bill_id is not null and b.bill_status=1 ".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

	and b.bill_type!=6  ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')." GROUP BY payment_mode");

	$return_data['payment_details'] = $payment_details->result_array();

	//PAYMENT DETAILS



	//CHIT ADJ

	$chit_details=$this->db->query("SELECT ifnull(sum(utilized_amt),0) as utilized_amt

		from ret_billing_chit_utilization chit

		left JOIN ret_billing b on b.bill_id = chit.bill_id

		where b.bill_id is not null and b.bill_status=1 ".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')." ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."");

	$return_data['chit_details'] = $chit_details->row_array();

	//CHIT ADJ



	//ORDER ADVANCE DETAILS

	$advance_detals = $this->db->query("SELECT ifnull(sum(a.received_amount),0) as orderamount, ifnull(sum(a.received_weight * a.rate_per_gram),0) as orderweight

										FROM ret_billing_advance a

										LEFT JOIN ret_billing b ON b.bill_id=a.bill_id

										WHERE b.bill_id is not null and b.bill_status = 1

										".($FromDt!= '' && $ToDt!='' ? 'and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

										".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."

									");

	$return_data['advance_detals'] = $advance_detals->row_array();

	//ORDER ADVANCE DETAILS



	//VOUCHER UTILIZED

	$voucher_details=$this->db->query("SELECT ifnull(sum(g.gift_voucher_amt),0) as gift_voucher_amt

		From ret_billing_gift_voucher_details g

		LEFT JOIN ret_billing b on b.bill_id=g.bill_id

		where  b.bill_id is not null and b.bill_status = 1 ".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')." ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."");

	$return_data['voucher_details'] = $voucher_details->row_array();

	//VOUCHER UTILIZED



	//BT DETAILS

	$branch_transfer_details=$this->db->query("SELECT IFNULL(sum(b.pieces),0) as pieces,IFNULL(sum(b.grs_wt),0)as gross_wt,IFNULL(sum(b.net_wt),0)as net_wt

		From ret_branch_transfer b

		where ".($FromDt!= '' && $ToDt!='' ? '(date(b.created_time) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')." and  b.status=2 ".($id_branch!='' && $id_branch!=0 ? " and b.transfer_to_branch=".$id_branch."" :'')."");

	$return_data['branch_transfer_details'] = $branch_transfer_details->row_array();

	//BT DETAILS



	//CREDIT ISSUED

	$due_details=$this->db->query("SELECT ifnull(sum(b.tot_bill_amount-b.tot_amt_received),0) as due_amt

		From ret_billing b

		WHERE b.bill_id is not null and b.bill_status=1 and b.is_credit=1 ".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')." ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."");

	$return_data['due_details'] = $due_details->row_array();

	//CREDIT ISSUED



   //CREDIT RECEIVED

	$credit_details=$this->db->query("SELECT ifnull(sum(b.tot_amt_received),0) as tot_amt_received

	 From ret_billing b

	 where  b.bill_id is not null and b.bill_status=1 and b.bill_type=8 ".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

	 ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')." ");

	$return_data['credit_details']=$credit_details->row_array();

	//CREDIT RECEIVED



	//GENERAL ADV ADJ

	$advance_adjusted=$this->db->query("SELECT IFNULL(sum(r.amount),0) as adj_amt

		FROM ret_billing bill

		LEFT JOIN ret_wallet_transcation r on r.bill_no=bill.bill_id

		where r.bill_no is not null

		and bill.bill_status=1  and r.transaction_type =1

		".($FromDt!= '' && $ToDt!='' ? ' and (date(bill.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

		".($id_branch!='' && $id_branch!=0 ? " and bill.id_branch=".$id_branch."" :'')."

		");

	$return_data['advance_adjusted'] = $advance_adjusted->row_array();

	//GENERAL ADV ADJ





	//ORDER ADV ADJ

	$order_adj=$this->db->query("SELECT ifnull(sum(a.received_amount),0) as received_amount,ifnull(sum(a.received_weight * a.rate_per_gram),0) received_weight

	FROM ret_billing b

	LEFT JOIN ret_billing_advance a ON a.adjusted_bill_id=b.bill_id

	WHERE a.is_adavnce_adjusted=1 and b.bill_status = 1

	".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."

	 ".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

	");

	$return_data['order_adj'] = $order_adj->row_array();

	//ORDER ADV ADJ



	//GENERAL ADV RECEIVED

	$general_adv_details=$this->db->query("SELECT ifnull(sum(r.amount),0) as amount, ifnull(sum((r.weight*r.rate_per_gram)),0) as weight_amt

	FROM ret_issue_receipt r

	WHERE r.type=2

	".($id_branch!='' && $id_branch!=0 ? " and r.id_branch=".$id_branch."" :'')."

	".($FromDt!= '' && $ToDt!='' ? ' and (date(r.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

	");

	$return_data['general_adv_details'] = $general_adv_details->row_array();

	//GENERAL ADV RECEIVED





	$general_pay=$this->db->query("SELECT IFNULL(sum(IF(r.type = 1, (p.payment_amount * -1), p.payment_amount)),0) as payment_amount,

									p.payment_mode

									FROM ret_issue_receipt r

									LEFT JOIN ret_issue_rcpt_payment p ON p.id_issue_rcpt=r.id_issue_receipt

									WHERE r.type IN (1,2) AND r.bill_status = 1

									".($id_branch!='' && $id_branch!=0 ? " and r.id_branch=".$id_branch."" :'')."

									".($FromDt!= '' && $ToDt!='' ? ' and (date(r.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

									group by payment_mode

								");

	$return_data['general_pay'] = $general_pay->result_array();







	//HOME BILL DETAILS

	$home_bill=$this->db->query("SELECT ifnull(SUM(d.net_wt),0) as net_wt,ifnull(SUM(d.gross_wt),0) as gross_wt,ifnull(SUM(d.item_cost),0) as item_cost,ifnull(SUM(d.piece),0) as pcs

									FROM ret_billing b

									LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id

									LEFT JOIN ret_estimation_items e ON e.est_item_id=d.esti_item_id

									LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id

									LEFT JOIN ret_product_master p ON p.pro_id=d.product_id

									WHERE d.esti_item_id IS NOT null AND d.tag_id is null

									AND e.item_type=2 and b.bill_status=1

									".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."

									".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date)

									BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

								");

	$return_data['home_bill']=$home_bill->row_array();

	//HOME BILL DETAILS





	//PARTLY SALE

	$partly_sale=$this->db->query("SELECT ifnull(SUM(d.net_wt),0) as net_wt,ifnull(SUM(tag.gross_wt-d.gross_wt),0) as gross_wt,ifnull(SUM(d.item_cost),0) as item_cost,ifnull(SUM(d.piece),0) as pcs

									FROM ret_billing b

									LEFT JOIN ret_bill_details d ON d.bill_id=b.bill_id

									LEFT JOIN ret_estimation_items e ON e.est_item_id=d.esti_item_id

									LEFT JOIN ret_estimation est ON est.estimation_id=e.esti_id

									LEFT JOIN ret_product_master p ON p.pro_id=d.product_id

									LEFT JOIN ret_taging tag on tag.tag_id=d.tag_id

									WHERE d.tag_id IS NOT null AND d.is_partial_sale=1 and b.bill_status=1

									".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."

									".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

								");

	$return_data['partly_sale']=$partly_sale->row_array();

	//PARTLY SALE

	//PAYMENT DETAILS

	$chq_details = $this->db->query("SELECT ifnull(sum(p.payment_amount),0) as payment_amount, p.payment_mode,p.type

	FROM ret_billing_payment p

	LEFT JOIN ret_billing b on b.bill_id=p.bill_id

	where b.bill_id is not null and b.bill_status=1 and payment_mode = 'CHQ' ".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

	and b.bill_type!=6  ".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')." GROUP BY p.type");
   // print_r($this->db->last_query());exit;
	$return_data['cheque_details'] = $chq_details->result_array();

	//PAYMENT DETAILS


    $general_pay_chq=$this->db->query("SELECT ifnull(sum(p.payment_amount),0) as payment_amount,

									p.payment_mode,p.type

									FROM ret_issue_receipt r

									LEFT JOIN ret_issue_rcpt_payment p ON p.id_issue_rcpt=r.id_issue_receipt

									WHERE r.bill_status = 1 and p.payment_mode = 'CHQ'

									".($id_branch!='' && $id_branch!=0 ? " and r.id_branch=".$id_branch."" :'')."

									".($FromDt!= '' && $ToDt!='' ? ' and (date(r.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')."

									group by p.type

								");
	 //print_r($this->db->last_query());exit;
    $return_data['general_pay_chq'] = $general_pay_chq->result_array();


	//general_bill_details

	$bill_det=$this->db->query("SELECT IFNULL(SUM(b.round_off_amt),0) as round_off_amt,IFNULL(SUM(b.handling_charges),0) as handling_charges

	FROM ret_billing b

	WHERE b.bill_status = 1

	".($id_branch!='' && $id_branch!=0 ? " and b.id_branch=".$id_branch."" :'')."

	".($FromDt!= '' && $ToDt!='' ? ' and (date(b.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')." ");

	$return_data['bill_det'] = $bill_det->row_array();

	//general_bill_details



	//Other Expenses
	$otherExpenses=$this->db->query("SELECT IFNULL(SUM(r.amount),0) as tot_amount

	FROM ret_issue_receipt r

	WHERE r.type=1 AND r.issue_type=1 AND r.bill_status=1

	".($id_branch!='' && $id_branch!=0 ? " and r.id_branch=".$id_branch."" :'')."

	".($FromDt!= '' && $ToDt!='' ? ' and (date(r.bill_date) BETWEEN "'.$FromDt.'" AND "'.$ToDt.'")' : '')." ");

	$return_data['other_expenses'] = $otherExpenses->row_array();


	$chit_credit_collection = $this->db->query("SELECT
	sum(IF(p.payment_mode='FP','0',pmd.payment_amount)) as payment_amount,pm.mode_name as payment_mode

	FROM payment_mode_details pmd
	LEFT JOIN payment p ON (p.id_payment = pmd.id_payment)
	LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
	LEFT JOIN customer c ON (c.id_customer = sa.id_customer)
	LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
	LEFT JOIN branch b ON (b.id_branch = p.id_branch)
	Left Join payment_mode pm on (pm.short_code = pmd.payment_mode)
	left join ret_bill_pay_device dev on(dev.id_device = pmd.id_pay_device)
	JOIN chit_settings chit
	Where pmd.payment_status = 1 and p.payment_status = 1 and (date(p.date_payment) BETWEEN '".date('Y-m-d',strtotime($FromDt))."' AND '".date('Y-m-d',strtotime($ToDt))."')
	".($id_branch!='' && $id_branch>0 ? " and p.id_branch=".$id_branch."" :'')."
	GROUP BY pmd.payment_mode");

	$return_data['chit_payment_details']= $chit_credit_collection->result_array();



	$metal_rates=$this->db->query("SELECT *

	FROM metal_rates m

	WHERE date(m.updatetime)=".$FromDt."");

	$return_data['metal_rates']	= $metal_rates->row_array();





	$cachabstaract_data = array();

	$cachabstaract_data['sales_amount'] 					= (float)number_format($return_data['item_details']['item_cost']-$return_data['item_details']['item_total_tax'],2,'.','');

	$cachabstaract_data['sales_total_tax_amount'] 			= $return_data['item_details']['item_total_tax'];

	$cachabstaract_data['sales_return'] 					= number_format($return_data['return_details']['item_cost']-$return_data['return_details']['item_total_tax'],2,'.','');

	$cachabstaract_data['sales_return_total_tax_amount'] 	= number_format($return_data['return_details']['item_total_tax'],2,'.','');

	$cachabstaract_data['purchase_amount'] 					= number_format($return_data['old_matel_details']['amount'],2,'.','');

	$cachabstaract_data['advance_receipt'] 					= number_format($return_data['general_adv_details']['amount'] + $return_data['general_adv_details']['weight_amt'] + $return_data['advance_detals']['orderamount']+ $return_data['advance_detals']['orderweight'],2,'.','');

	$cachabstaract_data['credit_sale'] 						= number_format($return_data['credit_details']['tot_amt_received'],2,'.','');

	$cachabstaract_data['credit_receipt'] 					= number_format($return_data['due_details']['due_amt'],2,'.','');

	$cachabstaract_data['handling_charge'] 					= number_format($return_data['bill_det']['handling_charges'],2,'.','');

	$cachabstaract_data['other_expenses'] 					= number_format($return_data['other_expenses']['tot_amount'],2,'.','');

	$cachabstaract_data['chit_payment_details'] 			= $return_data['chit_payment_details'];

	$cachabstaract_data['trans_total'] 						= number_format($cachabstaract_data['sales_amount'] + $cachabstaract_data['sales_total_tax_amount'] - $cachabstaract_data['sales_return'] - $cachabstaract_data['sales_return_total_tax_amount'] - $cachabstaract_data['purchase_amount'] + $cachabstaract_data['advance_receipt'] + $cachabstaract_data['credit_sale'] - $cachabstaract_data['credit_receipt'] + $cachabstaract_data['handling_charge'] - $cachabstaract_data['other_expenses'],2,'.','');



	$received_pay_mode = array("cash" => 0, "cc" => 0, "nb" => 0, "dc" => 0, "chq" => 0,"chq_issued" => 0,"chq_received" => 0);

	foreach($return_data['payment_details'] as $payrow => $payval){

		if(strtolower($payval['payment_mode']) === 'cash' ){

			$received_pay_mode['cash'] = $payval['payment_amount'];

		}else if(strtolower($payval['payment_mode']) === 'cc' ){

			$received_pay_mode['cc'] = $payval['payment_amount'];

		}else if(strtolower($payval['payment_mode']) === 'dc' ){

			$received_pay_mode['dc'] = $payval['payment_amount'];

		}else if(strtolower($payval['payment_mode']) === 'nb' ){

			$received_pay_mode['nb'] = $payval['payment_amount'];

		}else if(strtolower($payval['payment_mode']) === 'chq' ){

			$received_pay_mode['chq'] = $payval['payment_amount'];

		}

	}

	foreach($return_data['cheque_details'] as $payrow => $chqpayval)
	{
		if($chqpayval['type']==2)
		{
			$received_pay_mode['chq_issued'] = ($chqpayval['payment_amount']*-1);
		}
		else if($chqpayval['type']==1)
		{
			$received_pay_mode['chq_received'] = $chqpayval['payment_amount'];
		}
	}

	foreach($return_data['general_pay_chq'] as $gpaychq)
		{
		    if($gpaychq['type']==2)
		    {
		        $received_pay_mode['chq_issued'] = $gpaychq['payment_amount'];
		    }
		    else if($gpaychq['type']==1)
		    {
		        $received_pay_mode['chq_received'] = $gpaychq['payment_amount'];
		    }
		}


	foreach($return_data['general_pay'] as $gpayrow => $gpayval){

		if(strtolower($gpayval['payment_mode']) === 'cash' ){

			$received_pay_mode['cash'] = $received_pay_mode['cash'] + $gpayval['payment_amount'];

		}else if(strtolower($gpayval['payment_mode']) === 'cc' ){

			$received_pay_mode['cc'] = $received_pay_mode['cc'] + $gpayval['payment_amount'];

		}else if(strtolower($gpayval['payment_mode']) === 'dc' ){

			$received_pay_mode['dc'] = $received_pay_mode['dc'] + $gpayval['payment_amount'];

		}else if(strtolower($gpayval['payment_mode']) === 'nb' ){

			$received_pay_mode['nb'] = $received_pay_mode['nb'] + $gpayval['payment_amount'];

		}else if(strtolower($gpayval['payment_mode']) === 'chq' ){

			$received_pay_mode['chq'] = $received_pay_mode['chq'] + $gpayval['payment_amount'];

		}

	}

	$cachabstaract_data['cash'] 					= $received_pay_mode['cash'];

	$cachabstaract_data['chq'] 						= $received_pay_mode['chq'];

	$cachabstaract_data['chq_issue'] 				= $received_pay_mode['chq_issued'];

	$cachabstaract_data['chq_recd'] 				= $received_pay_mode['chq_received'];

	$cachabstaract_data['card'] 					= $received_pay_mode['cc'] + $received_pay_mode['dc'];

	$cachabstaract_data['nb'] 						= $received_pay_mode['nb'];

	$cachabstaract_data['advadj'] 					= $return_data['advance_adjusted']['adj_amt'];

	$cachabstaract_data['chituti'] 					= $return_data['chit_details']['utilized_amt'];

	$cachabstaract_data['handlingcharge'] 			= 0;

	$cachabstaract_data['orderadj'] 				= $return_data['order_adj']['received_amount'] + $return_data['order_adj']['received_weight'];

	$cachabstaract_data['giftvoucher'] 				= $return_data['voucher_details']['gift_voucher_amt'];

	$cachabstaract_data['roundoff'] 				= (float)number_format($return_data['bill_det']['round_off_amt'],2,'.','');

	$cachabstaract_data['paymodes_total']			= number_format(($cachabstaract_data['cash'] + $cachabstaract_data['chq'] + $cachabstaract_data['card'] + $cachabstaract_data['nb'] + $cachabstaract_data['advadj'] + $cachabstaract_data['chituti'] + $cachabstaract_data['handlingcharge'] + $cachabstaract_data['orderadj'] + $cachabstaract_data['giftvoucher'] + $cachabstaract_data['roundoff'] ),2,'.','');



	return $cachabstaract_data;

}







    function get_estimation_details($from_date,$to_date,$id_branch)

    {



		$sql=$this->db->query("SELECT est.estimation_id,est.discount,est.total_cost,

			est.gift_voucher_amt,est.cus_id,st.stone_price,mat.mat_price,item.item_cost,sales.sales_amt,est.cus_id,concat(c.firstname,' ',c.lastname) as cus_name,c.chit_amt,

			sales.sales_wt,item.pur_wt,item.item_type

		FROM ret_estimation est

		left JOIN (select if(i.item_type=0,'Tag',if(i.item_type=1,'Catalog','Custom')) as item_type,i.esti_id,SUM(i.item_cost) as item_cost,SUM(i.gross_wt)as pur_wt FROM ret_estimation_items i GROUP by i.esti_id) as item on item.esti_id=est.estimation_id

		LEFT JOIN (select s.est_id,SUM(s.price) as stone_price FROM ret_estimation_item_stones s GROUP by s.est_id)as st ON st.est_id=est.estimation_id

		LEFT JOIN (SELECT m.est_id,SUM(m.price) as mat_price FROM ret_estimation_item_other_materials m GROUP by m.est_id) as mat on mat.est_id=est.estimation_id

		LEFT JOIN (SELECT sa.est_id,SUM(sa.amount) as sales_amt,SUM(sa.gross_wt) as sales_wt FROM ret_estimation_old_metal_sale_details sa GROUP BY sa.est_id) as sales on sales.est_id=est.estimation_id

		LEFT JOIN(SELECT chit.est_id,SUM(chit.utl_amount) as chit_amt FROM ret_est_chit_utilization chit GROUP by chit.est_id) as c on c.est_id=est.estimation_id

		LEFT JOIN (SELECT v.est_id,SUM(v.gift_voucher_amt) as gift_voucher_amt FROM ret_est_gift_voucher_details v GROUP by v.est_id) as vo on vo.est_id=est.estimation_id

		left join customer c on c.id_customer=est.cus_id

		where  (date(est.created_time) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' && $id_branch!=0 ? " and est.id_branch=".$id_branch."" :'')." ");

    	//print_r($this->db->last_query());exit;

    	return $sql->result_array();



    }



    function lot_branchwise_data($id_branch,$from_date,$to_date)

	{

					$sql=("SELECT l.lot_no,COUNT(l.lot_received_at) as lots,SUM(l.net_wt) as net_weight,SUM(l.gross_wt) as grs_wt,b.name as name,

					b.id_branch,l.created_on from ret_lot_inwards l

					left join branch b on b.id_branch=l.lot_received_at

					where l.lot_received_at=".$id_branch);



					if($from_date!='')

						{

						$sql = $sql.( ' and (date(l.created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

					$data = $this->db->query($sql);

					$res['count']=$data->row_array();

					return $res;

	}

    function get_lot_data($type,$from_date,$to_date)

	{

		switch($type)

		{

			case 'get_total_gross_wt':

					$sql = "SELECT SUM(gross_wt) as total_gt,created_on FROM ret_lot_inwards";

					if($from_date!='')

						{

						$sql = $sql.( ' where (date(created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

					$res = $this->db->query($sql);

					return $res->row('total_gt');

				break;

			case 'get_total_net_wt':

					$sql = "SELECT SUM(net_wt) as total_net_wt,created_on FROM ret_lot_inwards";

					if($from_date!='')

						{

						$sql = $sql.( ' where (date(created_on) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

					$total = $this->db->query($sql);

					return $total->row('total_net_wt');

				break;

		}

	}

	function tag_branchwise_data($id_branch,$from_date,$to_date)

	{

					$sql=("SELECT t.tag_id,SUM(t.net_wt) as nt,SUM(t.gross_wt) as gt,COUNT(t.id_branch) as tags,b.name as branch,t.created_time from ret_taging t

					left join branch b on b.id_branch=t.id_branch

					where t.id_branch=".$id_branch);

					if($from_date!='')

						{

						$sql = $sql.( ' and (date(t.created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

					$data = $this->db->query($sql);

					$tag['total'] = $data->row_array();

					return $tag;

	}

	function get_tag_data($type,$from_date,$to_date)

	{

		switch($type)

		{

			case 'get_total_gross_wt':

					$total_gt = "SELECT SUM(gross_wt) as total_gt,created_time FROM ret_taging";

					if($from_date!='')

						{

						$total_gt = $total_gt.( ' where (date(created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

					$result = $this->db->query($total_gt);

					return $result->row('total_gt');

				break;

			case 'get_total_net_wt':

					$total_nt = "SELECT SUM(net_wt) as total_net_wt,created_time FROM ret_taging";

					if($from_date!='')

						{

						$total_nt = $total_nt.( ' where (date(created_time) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

					$res = $this->db->query($total_nt);

					return $res->row('total_net_wt');

				break;

		}

	}



	function get_billing($from_date,$to_date)

	{

		$sql=$this->db->query("select count(b.bill_id) as billing

	    							 from ret_billing b

	    					          where(date(b.created_time) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')");

	    		 return $sql->row_array();

	}

	function allBranches(){

	    $sql="Select b.id_branch as id_branch, b.name as branch_name from branch b";

		$r=$this->db->query($sql);

		return $r->result_array();

	}

	function get_order_data($id_branch,$from_date,$to_date)

	{

		$catalog="SELECT COUNT(c.ortertype) as catalog,c.branch_id,b.id_branch as id_branch,b.name as branch,c.order_date from customerorderdetails  c

		left join branch b on b.id_branch=c.branch_id

		where c.ortertype=1 and c.branch_id=".$id_branch;

		if($from_date!='')

						{

						$catalog = $catalog.( ' and (date(c.order_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

		$ct =$this->db->query($catalog);

		$res['catalog'] = $ct->row_array();

		$ct->free_result();





		$cus="SELECT c.id_orderdetails,COUNT(c.ortertype) as custom,c.branch_id,b.name as branch,b.id_branch as id_branch,c.order_date from customerorderdetails  c

		left join branch b on b.id_branch=c.branch_id

		where c.ortertype=2 and c.branch_id=".$id_branch;

		if($from_date!='')

						{

						$cus = $cus.( ' and (date(c.order_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

		$cus =$this->db->query($cus);

		$res['cus'] = $cus->row_array();

		$cus->free_result();



		$sql= "SELECT c.id_orderdetails,COUNT(c.ortertype) as repair,c.branch_id,b.name as branch,b.id_branch as id_branch,c.order_date from customerorderdetails  c

		left join branch b on b.id_branch=c.branch_id

		where c.ortertype=3 and c.branch_id=".$id_branch;

		if($from_date!='')

						{

						$sql = $sql.( ' and (date(c.order_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'")');

						}

		$data =$this->db->query($sql);

		$res['repair'] = $data->row_array();

		$data->free_result();

		return $res;



	}

	function total_order($type="")

	{

		switch($type)

		{

		case 'tot_catalog':

		$sql = $this->db->query("SELECT COUNT(ortertype) as tot_catalog FROM customerorderdetails where ortertype=1");

		return $sql->row('tot_catalog');

		break;



	    case 'tot_custom':

		$sql = $this->db->query("SELECT COUNT(ortertype) as tot_custom FROM customerorderdetails where ortertype=2");

		return $sql->row('tot_custom');

		break;



	    case 'tot_repair':

		$sql = $this->db->query("SELECT COUNT(ortertype) as tot_repair FROM customerorderdetails where ortertype=3");

		return $sql->row('tot_repair');

		break;

		}

	}



	function get_saleBill($id_branch,$from_date,$to_date)

	{

		$sql=$this->db->query("select count(IFNULL(b.bill_id,0)) as billing,br.name as branch_name

		from ret_billing b

		LEFT join branch br on br.id_branch=b.id_branch

		where (b.bill_type=1 or b.bill_type=2 or b.bill_type=3 or b.bill_type=7) and (date(b.created_time) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

		".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."");

		return $sql->row_array();

	}



	function metal_bill_details()

	{

		$data=$this->db->query("SELECT m.metal,m.metal_code,count(d.bill_id) as billing,sum(d.item_cost) as sale_amount,b.id_branch,br.name as branch_name

			FROM ret_billing b

			LEFT JOIN ret_bill_details d on d.bill_id=b.bill_id

			LEFT JOIN ret_product_master p on p.pro_id=d.product_id

			LEFT JOIN ret_category c on c.id_ret_category=p.cat_id

			LEFT JOIN metal m on m.id_metal=c.id_metal

			LEFT JOIN branch br on br.id_branch=b.id_branch

			GROUP by br.id_branch,m.id_metal");

			return $data->result_array();

	}







   function karigar_orders($filterBy)

   {

       $result=0;

       $id_branch=$this->input->post('id_branch');

       $sql="Select count(o.id_customerorder) as total_orders

       From customerorderdetails o

       LEFT JOIN customerorder c on c.id_customerorder=o.id_customerorder

       WHERE c.order_for=1 ".($id_branch!='' ?  " and c.order_from=".$id_branch."":'')."";

       switch($filterBy)

       {

           case 'TM': //Tomorrow Delivery

           $sql=$sql." AND date(o.smith_due_date) = CURDATE() + INTERVAL 1 DAY";

           break;

           case 'T': //Today Delivered

           $sql=$sql." AND date(o.delivered_date) = CURDATE() AND o.orderstatus=5";

           break;

case 'TODDY_PENDING': //Today Yet To Delivery

           $sql=$sql." AND date(o.smith_due_date) = CURDATE() AND o.orderstatus<=3";

           break;

           case 'OVER_DUE': //Over Due Ordes

           $sql=$sql." AND date(o.smith_due_date) < CURDATE() AND o.orderstatus=3";

           break;

           case 'WIP': //Work in Progress Ordes

           $sql=$sql." AND o.orderstatus=3";

           break;

       }

       $r=$this->db->query($sql);

       if($r->num_rows()>0)

       {

           $result= $r->row('total_orders');

       }

       return $result;

   }





  /* function customer_orders($filterBy)

   {

       $result=0;

       $id_branch=$this->input->post('id_branch');

       $sql="Select count(o.id_customerorder) as total_orders

       From customerorderdetails o

       LEFT JOIN customerorder c on c.id_customerorder=o.id_customerorder

       WHERE c.order_for=2 ".($id_branch!='' ?  " and c.order_from=".$id_branch."":'')." ";

       switch($filterBy)

       {

           case 'TM': //Tomorrow Ready for Delivery

           $sql=$sql." AND date(o.cus_due_date) = CURDATE() + INTERVAL 1 DAY AND o.orderstatus=4";

           break;

		   case 'TMRW_PENDING': //Tomorrow Delivery Pending

           $sql=$sql." AND date(o.cus_due_date) = CURDATE() + INTERVAL 1 DAY AND o.orderstatus<4";

           break;

           case 'T': //Today Delivered

           $sql=$sql." AND date(o.delivered_date) = CURDATE() AND o.orderstatus=5 ";

           break;

		   case 'TODDY_PENDING': //Today Yet To Delivery

           $sql=$sql." AND date(o.cus_due_date) = CURDATE() AND o.orderstatus<=3";

           break;

           case 'OVER_DUE': //Over Due Ordes

           $sql=$sql." AND date(o.cus_due_date) < CURDATE() AND o.orderstatus=3 ";

           break;

           case 'WIP': //Work in Progress Ordes

           $sql=$sql." AND o.orderstatus=3";

           break;

       }

       $r=$this->db->query($sql);

       if($r->num_rows()>0)

       {

           $result= $r->row('total_orders');

       }

       return $result;

   }*/





   function customer_orders($from_date,$to_date,$id_branch)

    {

        $sql=$this->db->query("SELECT



        IFNULL((recd_piece.recd_pcs),0) as received_piece,IFNULL((allocated.allo_pcs),0) as allocated_piece,

        IFNULL((pending.pen_pcs),0) as pending_piece,IFNULL((ready.rdy_pcs),0) as ready_piece,

        IFNULL((delivery.dely_pcs),0) as delivery_piece



        FROM customerorder c



        LEFT JOIN branch b ON b.id_branch=c.order_from



        LEFT JOIN customerorderdetails d on d.id_customerorder=c.id_customerorder



        LEFT JOIN(SELECT SUM(d.totalitems) as recd_pcs,c.order_from

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' && $id_branch!=0 ? " and c.order_from=".$id_branch."" :'')."

                 and c.order_for='2' and d.orderstatus = 0) as recd_piece ON recd_piece.order_from=c.order_from



        LEFT JOIN(SELECT SUM(d.totalitems) as allo_pcs,c.order_from

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' && $id_branch!=0 ? " and c.order_from=".$id_branch."" :'')."

                 and c.order_for='2' and d.orderstatus = 3 ) as allocated ON allocated.order_from=c.order_from



        LEFT JOIN(SELECT SUM(d.totalitems) as pen_pcs,c.order_from

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' && $id_branch!=0 ? " and c.order_from=".$id_branch."" :'')."

                 and c.order_for='2' and d.orderstatus <= 2 ) as pending ON pending.order_from=c.order_from



        LEFT JOIN(SELECT SUM(d.totalitems) as rdy_pcs,c.order_from

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' && $id_branch!=0 ? " and c.order_from=".$id_branch."" :'')."

                 and c.order_for='2' and d.orderstatus = 4 ) as ready ON ready.order_from=c.order_from





        LEFT JOIN(SELECT SUM(d.totalitems) as dely_pcs,c.order_from

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' && $id_branch!=0 ? " and c.order_from=".$id_branch."" :'')."

                 and c.order_for='2' and d.orderstatus = 5 ) as delivery ON delivery.order_from=c.order_from





        WHERE c.order_from is not null

		and (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' && $id_branch!=0 ? " and c.order_from=".$id_branch."" :'')."

		".($id_branch!='' && $id_branch!=0 ? " and c.order_from=".$id_branch."" :'')."");

        // print_r($this->db->last_query());exit;

        return $sql->row_array();

    }



	function TagStockMetalWise($id_branch)

	{

		$sql=$this->db->query("SELECT SUM(tag.piece) as available_pcs,SUM(tag.gross_wt) as total_gwt,SUM(tag.net_wt) as total_nwt,m.metal

		FROM ret_taging_status_log m1
        
		LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log)

		LEFT JOIN ret_taging tag ON tag.tag_id = m1.tag_id

		LEFT JOIN ret_product_master p ON p.pro_id=tag.product_id

		LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id

		LEFT JOIN metal m ON m.id_metal=c.id_metal

		WHERE m2.id_tag_status_log IS NULL

        AND (m1.status = 0 OR m1.status = 6)

		".($id_branch!='' ? " and tag.current_branch=".$id_branch."" :'')."

		GROUP by m.id_metal");



		return $sql->result_array();

	}

	function NonTagStockMetalWise($id_branch)

	{
		// print_r($id_branch);exit;
		$return_data =[];

		$sql=$this->db->query("SELECT IFNULL(SUM(nt.no_of_piece),0) as nt_tot_pieces, IFNULL(SUM(nt.gross_wt),0) as nt_total_gwt,IFNULL(SUM(nt.net_wt),0) as nt_total_nwt,

		rt_m.metal
		
		FROM ret_nontag_item nt
		
		LEFT JOIN ret_product_master rt_p ON rt_p.pro_id = nt.product
		
		LEFT JOIN ret_category rt_c ON rt_c.id_ret_category = rt_p.cat_id
		
		LEFT JOIN metal rt_m ON rt_m.id_metal = rt_c.id_metal
		
		WHERE ".($id_branch != '' ? " nt.branch = ".$id_branch."" : '')."
		
		GROUP BY rt_m.id_metal");

		
		$return_data = $sql->result_array();

		// return $sql->result_array();

		// print_r($this->db->last_query());exit;

		// echo"<pre>";print_r($return_data);exit;

		return $return_data;

	}

	function AvailableStockDetails($from_date,$to_date,$id_branch)

	{

		$id_metal = 1;

		$FromDt = $from_date;

        $ToDt = $to_date;

		$op_blc_to_date= date('Y-m-d',(strtotime('-1 day',strtotime($from_date))));


		$sql = $this->db->query("SELECT t.product_id,p.product_name,b.name as branch_name,c.name as category_name,

								(IFNULL(blc.gross_wt,0)) as op_blc_gwt,

								(IFNULL(blc.net_wt,0)) as op_blc_nwt,

								(IFNULL(blc.piece,0)) as op_blc_pcs,

								IFNULL(INW.gross_wt,0) as inw_gwt,

								IFNULL(INW.net_wt,0) as inw_nwt,

								IFNULL(INW.piece,0) as inw_pcs,

								IFNULL(s.gross_wt,0) as sold_gwt,

								IFNULL(s.net_wt,0) as sold_nwt,

								IFNULL(s.piece,0) as sold_pcs,

								IFNULL(br_out.gross_wt,0) as br_out_gwt,

								IFNULL(br_out.net_wt,0) as br_out_nwt,

								IFNULL(br_out.piece,0) as br_out_pcs,

								IFNULL(in_trans.gross_wt,0) as in_trans_gwt,

								IFNULL(in_trans.net_wt,0) as in_trans_nwt,

								IFNULL(in_trans.piece,0) as in_trans_pcs,

								IFNULL(current.piece,0) as closing_pcs,

								IFNULL(current.gross_wt,0) as closing_gwt,

								IFNULL(current.net_wt,0) as closing_nwt,

								Date_Format(current_date(),'%d-%m-%Y') as date_add,m.metal as metal_name

								FROM ret_taging t

								LEFT JOIN ret_product_master p on p.pro_id=t.product_id

								LEFT JOIN branch b on b.id_branch=t.current_branch

								LEFT JOIN ret_category c on c.id_ret_category=p.cat_id

								LEFT JOIN metal m on m.id_metal=c.id_metal



								LEFT JOIN

								(SELECT tag.tag_code,tag.tag_id,tag.old_tag_id,p.product_name, ifnull(sum(tag.gross_wt),0) as gross_wt,

								ifnull(SUM(tag.net_wt),0) as net_wt,

								ifnull(SUM(tag.piece),0) as piece,

								tag.product_id as id_product,p.cat_id,

								p.metal_type

								FROM ret_taging_status_log m1

								LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date) <= '".$op_blc_to_date."')

								LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								LEFT JOIN ret_category c on c.id_ret_category=p.cat_id

								WHERE m2.id_tag_status_log IS NULL

								".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."

								AND (m1.status = 0 OR m1.status = 6) AND date(m1.date) <= '".$op_blc_to_date."'

								GROUP BY p.metal_type) as blc ON blc.metal_type = p.metal_type



								LEFT JOIN

								(SELECT tag.tag_id,tag.product_id,p.cat_id,ifnull(sum(tag.gross_wt),0) as gross_wt,

								ifnull(SUM(tag.net_wt),0) as net_wt, ifnull(SUM(tag.piece),0) as piece,

								p.metal_type

								FROM ret_taging_status_log m1

								LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date)  BETWEEN '$FromDt' AND '$ToDt' ".($id_branch!='' ? " and m2.to_branch=".$id_branch."" :'')." and m2.status = 0)

								LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								WHERE tag.tag_id IS NOT NULL

								".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."

								AND (m1.status = 0) AND date(m1.date) BETWEEN '$FromDt' AND '$ToDt' GROUP by p.metal_type) INW on INW.metal_type = p.metal_type



								LEFT JOIN (SELECT sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,

								SUM(b.piece) as piece,b.product_id,p.cat_id,

								p.metal_type

								FROM ret_taging tag

								LEFT JOIN ret_bill_details b on b.tag_id = tag.tag_id

								LEFT JOIN ret_billing bill on bill.bill_id = b.bill_id

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								WHERE bill.bill_status = 1 and (b.item_type = 0 OR b.item_type IS NULL) AND

								(date(bill.bill_date) BETWEEN '$FromDt' AND '$ToDt') AND b.product_id = p.pro_id

								".($id_branch!='' ? " and bill.id_branch=".$id_branch." " :'')."

								GROUP by p.metal_type) s ON s.metal_type = p.metal_type



								LEFT JOIN (SELECT tag.product_id,p.cat_id,sum(tag.gross_wt) as gross_wt,

								SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece,

								p.metal_type

								FROM ret_taging tag

								LEFT JOIN ret_taging_status_log l on l.tag_id = tag.tag_id and l.from_branch = ".$id_branch." and (l.status=2 or l.status=3 or l.status=5 or l.status=4 or l.status=9 or l.status=12 or l.status=10 or l.status=7)

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt') and

								(l.status=2 or l.status=3 or l.status=5 or l.status=4 or l.status=9 or l.status=12 or l.status=10 or l.status=7)

								".($id_branch!='' ? " and l.from_branch=".$id_branch."" :'')."

								GROUP by p.metal_type) as br_out  on br_out.metal_type = p.metal_type


								LEFT JOIN (SELECT tag.product_id, ifnull(sum(tag.gross_wt),0) as gross_wt,

								ifnull(SUM(tag.net_wt),0) as net_wt,

								ifnull(SUM(tag.piece),0) as piece,p.cat_id, p.metal_type

								FROM ret_taging_status_log m1

								LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date) <= '$ToDt')

								LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								WHERE m2.id_tag_status_log IS NULL

								".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."
								AND m1.status = 0 AND date(m1.date) <= '$ToDt' GROUP by p.metal_type) as current ON  current.metal_type = p.metal_type



								LEFT JOIN (SELECT t.tag_id,t.product_id,sum(t.piece) as piece,SUM(t.gross_wt) as gross_wt,sum(t.net_wt) as net_wt,p.product_name,

								p.cat_id, p.metal_type

								FROM ret_taging_status_log l

								LEFT JOIN ret_taging t ON t.tag_id=l.tag_id

								LEFT JOIN ret_product_master p ON p.pro_id=t.product_id

								WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt') and t.tag_status=4

								".($id_branch !='' ? " and l.from_branch=".$id_branch."" :'')."

								GROUP by p.metal_type) in_trans on in_trans.metal_type = p.metal_type


								where t.tag_id is not null

								".($id_metal !='' && $id_metal !=0 ? " and m.id_metal=".$id_metal."" :'')."

								GROUP by p.metal_type");


		// print_r($this->db->last_query());exit;

		$data=$sql->row_array();

		$return_data['g_opening_pcs']=$data['op_blc_pcs'];

		$return_data['g_opening_gwt']=$data['op_blc_gwt'];

		$return_data['g_opening_nwt']=$data['op_blc_nwt'];


		$return_data['g_tot_sales_pcs']=$data['sold_pcs'];

		$return_data['g_tot_sales_gwt']=$data['sold_gwt'];

		$return_data['g_tot_sales_nwt']=$data['sold_nwt'];


		$return_data['g_inward_pcs']=$data['inw_pcs'];

		$return_data['g_inward_gwt']=$data['inw_gwt'];


		$return_data['g_br_out_pcs']=$data['br_out_pcs'];

		$return_data['g_br_out_gwt']=$data['br_out_gwt'];


		$return_data['g_available_pcs']=$data['op_blc_pcs']+$data['inw_pcs']-$data['sold_pcs']-$data['br_out_pcs'];

		$return_data['g_available_gwt']=$data['op_blc_gwt']+$data['inw_gwt']-$data['sold_gwt']-$data['br_out_gwt'];

		$return_data['g_available_nwt']=$data['op_blc_nwt']+$data['inw_nwt']-$data['sold_nwt']-$data['br_out_nwt'];



		return $return_data;

	}

	function Available_SilverStockDetails($from_date,$to_date,$id_branch)

	{

		$id_metal = 2;

		$FromDt = $from_date;

        $ToDt = $to_date;

		$op_blc_to_date= date('Y-m-d',(strtotime('-1 day',strtotime($from_date))));


		$sql = $this->db->query("SELECT t.product_id,p.product_name,b.name as branch_name,c.name as category_name,

								(IFNULL(blc.gross_wt,0)) as op_blc_gwt,

								(IFNULL(blc.net_wt,0)) as op_blc_nwt,

								(IFNULL(blc.piece,0)) as op_blc_pcs,

								IFNULL(INW.gross_wt,0) as inw_gwt,

								IFNULL(INW.net_wt,0) as inw_nwt,

								IFNULL(INW.piece,0) as inw_pcs,

								IFNULL(s.gross_wt,0) as sold_gwt,

								IFNULL(s.net_wt,0) as sold_nwt,

								IFNULL(s.piece,0) as sold_pcs,

								IFNULL(br_out.gross_wt,0) as br_out_gwt,

								IFNULL(br_out.net_wt,0) as br_out_nwt,

								IFNULL(br_out.piece,0) as br_out_pcs,

								IFNULL(in_trans.gross_wt,0) as in_trans_gwt,

								IFNULL(in_trans.net_wt,0) as in_trans_nwt,

								IFNULL(in_trans.piece,0) as in_trans_pcs,

								IFNULL(current.piece,0) as closing_pcs,

								IFNULL(current.gross_wt,0) as closing_gwt,

								IFNULL(current.net_wt,0) as closing_nwt,

								Date_Format(current_date(),'%d-%m-%Y') as date_add,m.metal as metal_name

								FROM ret_taging t

								LEFT JOIN ret_product_master p on p.pro_id=t.product_id

								LEFT JOIN branch b on b.id_branch=t.current_branch

								LEFT JOIN ret_category c on c.id_ret_category=p.cat_id

								LEFT JOIN metal m on m.id_metal=c.id_metal



								LEFT JOIN

								(SELECT tag.tag_code,tag.tag_id,tag.old_tag_id,p.product_name, ifnull(sum(tag.gross_wt),0) as gross_wt,

								ifnull(SUM(tag.net_wt),0) as net_wt,

								ifnull(SUM(tag.piece),0) as piece,

								tag.product_id as id_product,p.cat_id,

								p.metal_type

								FROM ret_taging_status_log m1

								LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date) <= '".$op_blc_to_date."')

								LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								LEFT JOIN ret_category c on c.id_ret_category=p.cat_id

								WHERE m2.id_tag_status_log IS NULL

								".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."

								AND (m1.status = 0 OR m1.status = 6) AND date(m1.date) <= '".$op_blc_to_date."'

								GROUP BY p.metal_type) as blc ON blc.metal_type = p.metal_type



								LEFT JOIN

								(SELECT tag.tag_id,tag.product_id,p.cat_id,ifnull(sum(tag.gross_wt),0) as gross_wt,

								ifnull(SUM(tag.net_wt),0) as net_wt, ifnull(SUM(tag.piece),0) as piece,

								p.metal_type

								FROM ret_taging_status_log m1

								LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date)  BETWEEN '$FromDt' AND '$ToDt' ".($id_branch!='' ? " and m2.to_branch=".$id_branch."" :'')." and m2.status = 0)

								LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								WHERE tag.tag_id IS NOT NULL

								".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."

								AND (m1.status = 0) AND date(m1.date) BETWEEN '$FromDt' AND '$ToDt' GROUP by p.metal_type) INW on INW.metal_type = p.metal_type



								LEFT JOIN (SELECT sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,

								SUM(b.piece) as piece,b.product_id,p.cat_id,

								p.metal_type

								FROM ret_taging tag

								LEFT JOIN ret_bill_details b on b.tag_id = tag.tag_id

								LEFT JOIN ret_billing bill on bill.bill_id = b.bill_id

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								WHERE bill.bill_status = 1 and (b.item_type = 0 OR b.item_type IS NULL) AND

								(date(bill.bill_date) BETWEEN '$FromDt' AND '$ToDt') AND b.product_id = p.pro_id

								".($id_branch!='' ? " and bill.id_branch=".$id_branch." " :'')."

								GROUP by p.metal_type) s ON s.metal_type = p.metal_type



								LEFT JOIN (SELECT tag.product_id,p.cat_id,sum(tag.gross_wt) as gross_wt,

								SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece,

								p.metal_type

								FROM ret_taging tag

								LEFT JOIN ret_taging_status_log l on l.tag_id = tag.tag_id and l.from_branch = ".$id_branch." and (l.status=2 or l.status=3 or l.status=5 or l.status=4 or l.status=9 or l.status=12 or l.status=10 or l.status=7)

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt') and

								(l.status=2 or l.status=3 or l.status=5 or l.status=4 or l.status=9 or l.status=12 or l.status=10 or l.status=7)

								".($id_branch!='' ? " and l.from_branch=".$id_branch."" :'')."

								GROUP by p.metal_type) as br_out  on br_out.metal_type = p.metal_type


								LEFT JOIN (SELECT tag.product_id, ifnull(sum(tag.gross_wt),0) as gross_wt,

								ifnull(SUM(tag.net_wt),0) as net_wt,

								ifnull(SUM(tag.piece),0) as piece,p.cat_id, p.metal_type

								FROM ret_taging_status_log m1

								LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date) <= '$ToDt')

								LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id

								LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id

								WHERE m2.id_tag_status_log IS NULL

								".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."
								AND m1.status = 0 AND date(m1.date) <= '$ToDt' GROUP by p.metal_type) as current ON  current.metal_type = p.metal_type



								LEFT JOIN (SELECT t.tag_id,t.product_id,sum(t.piece) as piece,SUM(t.gross_wt) as gross_wt,sum(t.net_wt) as net_wt,p.product_name,

								p.cat_id, p.metal_type

								FROM ret_taging_status_log l

								LEFT JOIN ret_taging t ON t.tag_id=l.tag_id

								LEFT JOIN ret_product_master p ON p.pro_id=t.product_id

								WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt') and t.tag_status=4

								".($id_branch !='' ? " and l.from_branch=".$id_branch."" :'')."

								GROUP by p.metal_type) in_trans on in_trans.metal_type = p.metal_type


								where t.tag_id is not null

								".($id_metal !='' && $id_metal !=0 ? " and m.id_metal=".$id_metal."" :'')."

								GROUP by p.metal_type");


		// print_r($this->db->last_query());exit;

		$data=$sql->row_array();

		$return_data['s_opening_pcs']=$data['op_blc_pcs'];

		$return_data['s_opening_gwt']=$data['op_blc_gwt'];

		$return_data['s_opening_nwt']=$data['op_blc_nwt'];


		$return_data['s_tot_sales_pcs']=$data['sold_pcs'];

		$return_data['s_tot_sales_gwt']=$data['sold_gwt'];

		$return_data['s_tot_sales_nwt']=$data['sold_nwt'];


		$return_data['s_inward_pcs']=$data['inw_pcs'];

		$return_data['s_inward_gwt']=$data['inw_gwt'];


		$return_data['s_br_out_pcs']=$data['br_out_pcs'];

		$return_data['s_br_out_gwt']=$data['br_out_gwt'];


		$return_data['s_available_pcs']=$data['op_blc_pcs']+$data['inw_pcs']-$data['sold_pcs']-$data['br_out_pcs'];

		$return_data['s_available_gwt']=$data['op_blc_gwt']+$data['inw_gwt']-$data['sold_gwt']-$data['br_out_gwt'];

		$return_data['s_available_nwt']=$data['op_blc_nwt']+$data['inw_nwt']-$data['sold_nwt']-$data['br_out_nwt'];

		return $return_data;

	}


	/*function AvailableStockDetails($from_date,$to_date,$id_branch)

	{

		$op_date= date('Y-m-d',(strtotime('-1 day',strtotime($from_date))));



		$sql=$this->db->query("SELECT (SELECT IFNULL(SUM(s.closing_pcs),0) FROM ret_stock_balance s

		left join ret_product_master as pro on pro.pro_id=s.id_product

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(s.date)='".$op_date."' ".($id_branch!='' && $id_branch>0 ? " and s.id_branch=".$id_branch."" :'')." and m.id_metal=1) as g_opening_pcs,



		(SELECT IFNULL(SUM(s.closing_gwt),0) FROM ret_stock_balance s

		left join ret_product_master as pro on pro.pro_id=s.id_product

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(s.date)='".$op_date."' ".($id_branch!='' && $id_branch>0 ? " and s.id_branch=".$id_branch."" :'')." and m.id_metal=1) as g_opening_gwt,



		(SELECT IFNULL(SUM(s.closing_nwt),0) FROM ret_stock_balance s

		left join ret_product_master as pro on pro.pro_id=s.id_product

    	left join ret_category as cat on cat.id_ret_category=pro.cat_id

   		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(s.date)='".$op_date."' ".($id_branch!='' && $id_branch>0 ? " and s.id_branch=".$id_branch."" :'')." and m.id_metal=1) as g_opening_nwt,



		(SELECT IFNULL(SUM(d.piece),0) FROM ret_bill_details d

		LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

		left join ret_product_master as pro on pro.pro_id=d.product_id

    	left join ret_category as cat on cat.id_ret_category=pro.cat_id

    	left join metal as m on m.id_metal=cat.id_metal

		WHERE b.bill_status=1 and m.id_metal=1 and date(b.bill_date)='".$from_date."' ".($id_branch!='' && $id_branch>0  ? " and b.id_branch=".$id_branch."" :'').") as g_tot_sales_pcs,



		( SELECT IFNULL(SUM(d.gross_wt),0) FROM ret_bill_details d

    	LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

    	left join ret_product_master as pro on pro.pro_id=d.product_id

    	left join ret_category as cat on cat.id_ret_category=pro.cat_id

    	left join metal as m on m.id_metal=cat.id_metal

    	WHERE d.bill_det_id is NOT null and m.id_metal=1 and b.bill_status=1 and date(b.bill_date)='".$from_date."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'').") as g_tot_sales_gwt,



		(SELECT IFNULL(SUM(d.net_wt),0) FROM ret_bill_details d

		LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

		left join ret_product_master as pro on pro.pro_id=d.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE b.bill_status=1 and m.id_metal=1 and date(b.bill_date)='".$from_date."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'').") as g_tot_sales_nwt,



		(SELECT IFNULL(SUM(tag.piece),0) FROM ret_taging_status_log l

		LEFT JOIN ret_taging tag on tag.tag_id=l.tag_id

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(l.date)='".$from_date."' and l.status=0 ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')." and m.id_metal=1 ) as g_inward_pcs,



		(SELECT IFNULL(SUM(tag.net_wt),0) FROM ret_taging tag

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(tag.tag_datetime)='".$from_date."' ".($id_branch!='' && $id_branch>0 ? " and tag.current_branch=".$id_branch."" :'')." and m.id_metal=1 ) as g_inward_nwt,



		(SELECT IFNULL(SUM(tag.gross_wt),0) FROM ret_taging tag

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(tag.tag_datetime)='".$from_date."' ".($id_branch!='' && $id_branch>0 ? " and tag.current_branch=".$id_branch."" :'')." and m.id_metal=1) as g_inward_gwt ,



		(SELECT IFNULL(SUM(tag.piece),0) FROM ret_taging_status_log l

		LEFT JOIN ret_taging tag ON tag.tag_id=l.tag_id

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(l.date)='".$from_date."' and (l.status=2 or l.status=3 or l.status=5 or l.status=4) ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')." and m.id_metal=1) as g_br_out_pcs,



		(SELECT IFNULL(SUM(tag.gross_wt),0) FROM ret_taging_status_log l

		LEFT JOIN ret_taging tag ON tag.tag_id=l.tag_id

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(l.date)='".$from_date."' and (l.status=2 or l.status=3 or l.status=5 or l.status=4) ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')." and m.id_metal=1) as g_br_out_gwt,



		(SELECT IFNULL(SUM(tag.net_wt),0) FROM ret_taging_status_log l

		LEFT JOIN ret_taging tag ON tag.tag_id=l.tag_id

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(l.date)='".$from_date."' and (l.status=2 or l.status=3 or l.status=5 or l.status=4) ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')." and m.id_metal=1) as g_br_out_nwt ");



		// print_r($this->db->last_query());exit;

		$data=$sql->row_array();

		$return_data['g_opening_pcs']=$data['g_opening_pcs'];

		$return_data['g_opening_gwt']=$data['g_opening_gwt'];

		$return_data['g_opening_nwt']=$data['g_opening_nwt'];

		$return_data['g_tot_sales_pcs']=$data['g_tot_sales_pcs'];

		$return_data['g_tot_sales_gwt']=$data['g_tot_sales_gwt'];

		$return_data['g_tot_sales_nwt']=$data['g_tot_sales_nwt'];

		$return_data['g_inward_pcs']=$data['g_inward_pcs'];

		$return_data['g_inward_gwt']=$data['g_inward_gwt'];

		$return_data['g_br_out_pcs']=$data['g_br_out_pcs'];

		$return_data['g_br_out_gwt']=$data['g_br_out_gwt'];

		$return_data['g_available_pcs']=$data['g_opening_pcs']+$data['g_inward_pcs']-$data['g_tot_sales_pcs']-$data['g_br_out_pcs'];

		$return_data['g_available_gwt']=$data['g_opening_gwt']+$data['g_inward_gwt']-$data['g_tot_sales_gwt']-$data['g_br_out_gwt'];

		$return_data['g_available_nwt']=$data['g_opening_nwt']+$data['g_inward_nwt']-$data['g_tot_sales_nwt']-$data['g_br_out_nwt'];



		return $return_data;

	}





	function Available_SilverStockDetails($from_date,$to_date,$id_branch){



		$op_date= date('Y-m-d',(strtotime('-1 day',strtotime($from_date))));



		$sql=$this->db->query("SELECT (SELECT IFNULL(SUM(s.closing_pcs),0) FROM ret_stock_balance s

		left join ret_product_master as pro on pro.pro_id=s.id_product

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(s.date)='".$op_date."' ".($id_branch!='' && $id_branch>0 ? " and s.id_branch=".$id_branch."" :'')." and m.id_metal=2) as s_opening_pcs,



		(SELECT IFNULL(SUM(s.closing_gwt),0) FROM ret_stock_balance s

		left join ret_product_master as pro on pro.pro_id=s.id_product

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(s.date)='".$op_date."' ".($id_branch!='' && $id_branch>0 ? " and s.id_branch=".$id_branch."" :'')." and m.id_metal=2) as s_opening_gwt,



		(SELECT IFNULL(SUM(s.closing_nwt),0) FROM ret_stock_balance s

		left join ret_product_master as pro on pro.pro_id=s.id_product

    	left join ret_category as cat on cat.id_ret_category=pro.cat_id

   		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(s.date)='".$op_date."' ".($id_branch!='' && $id_branch>0 ? " and s.id_branch=".$id_branch."" :'')." and m.id_metal=2) as s_opening_nwt,



		(SELECT IFNULL(SUM(d.piece),0) FROM ret_bill_details d

		LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

		left join ret_product_master as pro on pro.pro_id=d.product_id

    	left join ret_category as cat on cat.id_ret_category=pro.cat_id

    	left join metal as m on m.id_metal=cat.id_metal

		WHERE b.bill_status=1 and m.id_metal=2 and date(b.bill_date)='".$from_date."' ".($id_branch!='' && $id_branch>0  ? " and b.id_branch=".$id_branch."" :'').") as s_tot_sales_pcs,



		( SELECT IFNULL(SUM(d.gross_wt),0) FROM ret_bill_details d

    	LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

    	left join ret_product_master as pro on pro.pro_id=d.product_id

    	left join ret_category as cat on cat.id_ret_category=pro.cat_id

    	left join metal as m on m.id_metal=cat.id_metal

    	WHERE d.bill_det_id is NOT null and m.id_metal=2 and b.bill_status=1 and date(b.bill_date)='".$from_date."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'').") as s_tot_sales_gwt,



		(SELECT IFNULL(SUM(d.net_wt),0) FROM ret_bill_details d

		LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

		left join ret_product_master as pro on pro.pro_id=d.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE b.bill_status=1 and m.id_metal=2 and date(b.bill_date)='".$from_date."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'').") as s_tot_sales_nwt,



		(SELECT IFNULL(SUM(tag.piece),0) FROM ret_taging_status_log l

		LEFT JOIN ret_taging tag on tag.tag_id=l.tag_id

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(l.date)='".$from_date."' and l.status=0 ".($id_branch!='' && $id_branch>0 ? " and l.to_branch=".$id_branch."" :'')." and m.id_metal=2 ) as s_inward_pcs,



		(SELECT IFNULL(SUM(tag.net_wt),0) FROM ret_taging tag

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(tag.tag_datetime)='".$from_date."' ".($id_branch!='' && $id_branch>0 ? " and tag.current_branch=".$id_branch."" :'')." and m.id_metal=2 ) as s_inward_nwt,



		(SELECT IFNULL(SUM(tag.gross_wt),0) FROM ret_taging tag

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(tag.tag_datetime)='".$from_date."' ".($id_branch!='' && $id_branch>0 ? " and tag.current_branch=".$id_branch."" :'')." and m.id_metal=2 ) as s_inward_gwt ,



		(SELECT IFNULL(SUM(tag.piece),0) FROM ret_taging_status_log l

		LEFT JOIN ret_taging tag ON tag.tag_id=l.tag_id

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(l.date)='".$from_date."' and (l.status=2 or l.status=3 or l.status=5 or l.status=4) ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')." and m.id_metal=2 ) as s_br_out_pcs,



		(SELECT IFNULL(SUM(tag.gross_wt),0) FROM ret_taging_status_log l

		LEFT JOIN ret_taging tag ON tag.tag_id=l.tag_id

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(l.date)='".$from_date."' and (l.status=2 or l.status=3 or l.status=5 or l.status=4) ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')." and m.id_metal= 2) as s_br_out_gwt,



		(SELECT IFNULL(SUM(tag.net_wt),0) FROM ret_taging_status_log l

		LEFT JOIN ret_taging tag ON tag.tag_id=l.tag_id

		left join ret_product_master as pro on pro.pro_id=tag.product_id

		left join ret_category as cat on cat.id_ret_category=pro.cat_id

		left join metal as m on m.id_metal=cat.id_metal

		WHERE date(l.date)='".$from_date."' and (l.status=2 or l.status=3 or l.status=5 or l.status=4) ".($id_branch!='' && $id_branch>0 ? " and l.from_branch=".$id_branch."" :'')." and m.id_metal=2 ) as s_br_out_nwt ");



		// print_r($this->db->last_query());exit;

		$data=$sql->row_array();

		$return_data['s_opening_gwt']=$data['s_opening_gwt'];

		$return_data['s_opening_pcs']=$data['s_opening_pcs'];

		$return_data['s_opening_nwt']=$data['s_opening_nwt'];

		$return_data['s_tot_sales_pcs']=$data['s_tot_sales_pcs'];

		$return_data['s_tot_sales_gwt']=$data['s_tot_sales_gwt'];

		$return_data['s_tot_sales_nwt']=$data['s_tot_sales_nwt'];

		$return_data['s_inward_pcs']=$data['s_inward_pcs'];

		$return_data['s_inward_gwt']=$data['s_inward_gwt'];

		$return_data['s_br_out_pcs']=$data['s_br_out_pcs'];

		$return_data['s_br_out_gwt']=$data['s_br_out_gwt'];

		$return_data['s_available_pcs']=$data['s_opening_pcs']+$data['s_inward_pcs']-$data['s_tot_sales_pcs']-$data['s_br_out_pcs'];

		$return_data['s_available_gwt']=$data['s_opening_gwt']+$data['s_inward_gwt']-$data['s_tot_sales_gwt']-$data['s_br_out_gwt'];

		$return_data['s_available_nwt']=$data['s_opening_nwt']+$data['s_inward_nwt']-$data['s_tot_sales_nwt']-$data['s_br_out_nwt'];



		return $return_data;

	}*/



	function getReorderItems($id_branch)

	{

		$result=array();



		$data=$this->db->query("SELECT s.min_pcs,s.max_pcs,concat(IFNULL(sz.value,''),'',sz.name) as size,p.product_name,d.design_name,s.id_product as product_id,s.id_design as design_id,

		b.name as branch_name,s.id_branch,concat(IFNULL(wt.value,''),IFNULL(m.uom_name,'')) as weight_name,wt.from_weight,wt.to_weight,s.id_wt_range,p.cat_id as id_category

		FROM ret_reorder_settings s

		LEFT JOIN ret_product_master p ON p.pro_id=s.id_product

		LEFT  JOIN ret_design_master d ON d.design_no=s.id_design

		LEFT JOIN ret_size sz on sz.id_size=d.id_size

		LEFT JOIN branch b on b.id_branch=s.id_branch

		LEFT JOIN ret_weight wt on wt.id_weight=s.id_wt_range

		LEFT JOIN ret_uom m on m.uom_id=wt.id_uom

		where s.id_product is not null and s.id_design is not null and s.id_wt_range is not null

		".($id_branch!='' && $id_branch>0 ? " and s.id_branch=".$id_branch."":'')."

		order by p.pro_id ASC");



		$items = $data->result_array();

		foreach($items as $row)

		{

			$available_pcs=$this->getTagging($row['product_id'],$row['design_id'],$row['id_branch'],$row['from_weight'],$row['to_weight']);

			$isAlreadyinCart=$this->get_cart_items($row['product_id'],$row['design_id'],$row['id_wt_range'],$row['id_branch']);



			if($row['min_pcs']>$available_pcs && $isAlreadyinCart==0)

			{

					$result[]=array(

					'product_name'  =>$row['product_name'],

					'design_name'   =>$row['design_name'],

					'product_id'    =>$row['product_id'],

					'design_id'     =>$row['design_id'],

					'branch_name'   =>$row['branch_name'],

					'id_branch'     =>$row['id_branch'],

					'weight_name'   =>$row['weight_name'],

					'min_pcs'       =>$row['min_pcs'],

					'max_pcs'       =>$row['max_pcs'],

					'from_weight'   =>$row['from_weight'],

					'to_weight'     =>$row['to_weight'],

					'id_wt_range'   =>$row['id_wt_range'],

					'size'          =>$row['size'],

					'id_category'   =>$row['id_category'],

					'is_cart'		=>$isAlreadyinCart,

					'available_pcs' =>$available_pcs

				);

			}

		}

		return $result;

	}





	function getTagging($id_product,$design_id,$id_branch,$from_weight,$to_weight)

	{

			$sql=$this->db->query("SELECT IFNULL(SUM(t.piece),0) as tot_pcs,sum(t.net_wt) as net_wt,SUM(t.gross_wt) as gross_wt

			FROM ret_taging t

			WHERE t.tag_status=0 ".($id_branch!='' ? " AND t.current_branch=".$id_branch." " :'')."  AND t.product_id=".$id_product." AND t.design_id=".$design_id." and net_wt BETWEEN '".$from_weight."' AND '".$to_weight."'");

			return $sql->row()->tot_pcs;

	}



	function get_cart_items($id_product,$design_no,$id_wt_range,$id_branch)

	{

		$sql=$this->db->query("SELECT * FROM order_cart o WHERE o.orderstatus=0 AND o.id_product=".$id_product." AND o.design_no=".$design_no." AND o.id_wt_range=".$id_wt_range." ".($id_branch!='' ? " AND o.id_branch=".$id_branch." " :'')." ");

		return $sql->num_rows();

	}



	function get_saleBillRecords($id_branch,$from_date,$to_date)
	{

		$sql=$this->db->query("select category.name as category_name,b.name as branch_name,sum(bill_detalis.net_wt) as sold_weight,
		sum(bill_detalis.gross_wt) as sold_gross_wt
		from ret_bill_details as bill_detalis
		Left Join ret_billing as billing on billing.bill_id = bill_detalis.bill_id
		Left Join ret_taging as tag on tag.tag_id = bill_detalis.tag_id
		Left Join ret_product_master as product on product.pro_id = bill_detalis.product_id
		Left Join ret_design_master as design on design.design_no = bill_detalis.design_id
		Left Join ret_category as category on category.id_ret_category = product.cat_id
		Left Join branch as b on b.id_branch = billing.id_branch
		where billing.bill_status ='1' and (date(billing.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		".($id_branch!='' ? " and billing.id_branch=".$id_branch."" :'')." Group By product.cat_id,billing.id_branch order by bill_detalis.bill_det_id desc");
		$result                      = $sql->result_array();
		$categorywise_sales_records  = array();
		 foreach($result as $r){
			   $categorywise_sales_records[] = $r;
			}
		return $categorywise_sales_records;
	}
/*
	function get_paymentBillRecords($id_branch,$from_date,$to_date)
	{
		$sql=$this->db->query("SELECT b.name as branch_name,payment.payment_mode as payment_mode,round(if(payment.payment_amount>0,IFNULL(SUM(payment.payment_amount),0),'0'),2) as amount
		FROM ret_billing as billing
		Left Join ret_billing_payment as payment on payment.bill_id = billing.bill_id
		Left Join branch as b on b.id_branch = billing.id_branch
		where payment_status='1' and billing.bill_status = 1 and (date(billing.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($id_branch!='' ? " and billing.id_branch=".$id_branch."" :'')." Group By b.id_branch,payment.payment_mode order by billing.bill_id desc");
		$result1                      = $sql->result_array();


		$sql1= $this->db->query("SELECT rp.payment_mode,sum(rp.payment_amount) as amount,r.id_issue_receipt,(r.weight*r.rate_per_gram) as weight_amt
        FROM ret_issue_rcpt_payment rp
        LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt=rp.id_issue_rcpt
        WHERE r.type=2 and r.bill_status=1 and r.receipt_type!=4 and r.receipt_type!=5 and r.receipt_type!=3 and r.receipt_type!=7
        and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
        ".($id_branch!='' ? " and r.id_branch=".$id_branch."" :'')."
		AND rp.payment_amount>0 GROUP BY rp.payment_mode,r.id_branch");

		$result2 = $sql1->result_array();

		$result = array_merge($result1,$result2);

		$paymentwise_sales_records  = array();
		 foreach($result as $r){
			   $paymentwise_sales_records[] = $r;
			}

		return $paymentwise_sales_records;
	}

	function get_metalBillRecords($id_branch,$from_date,$to_date)
	{
		$data  = $this->db->query("SELECT m.metal,m.metal_code,count(d.bill_id) as billing,sum(d.item_cost) as sale_amount,b.id_branch,br.name as branch_name,
		    IFNULL(sum(d.net_wt),0) as sold_weight
			FROM ret_billing b
			LEFT JOIN ret_bill_details d on d.bill_id=b.bill_id
			LEFT JOIN ret_product_master p on p.pro_id=d.product_id
			LEFT JOIN ret_category c on c.id_ret_category=p.cat_id
			LEFT JOIN metal m on m.id_metal=c.id_metal
			LEFT JOIN branch br on br.id_branch=b.id_branch
			where b.bill_status='1' and c.id_metal IS NOT NULL and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		    ".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."
			GROUP by br.id_branch,m.id_metal");

			$result                      = $data->result_array();
			$metalwise_sales_records     = array();
			 foreach($result as $r){
				   $metalwise_sales_records[] = $r;
				}
		     return $metalwise_sales_records;

	}*/

	function get_paymentBillRecords($id_branch,$from_date,$to_date,$allBranch="")
	{

		$sql=$this->db->query("SELECT b.name as branch_name,payment.payment_mode as payment_mode,
		round(if(payment.payment_amount!=0,SUM(payment.payment_amount),'0'),2) as amount

		FROM ret_billing as billing

		Left Join ret_billing_payment as payment on payment.bill_id = billing.bill_id

		Left Join branch as b on b.id_branch = billing.id_branch

		where payment_status='1' and billing.bill_status=1 and billing.bill_type!=6  and (date(billing.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		".($id_branch!='' && $id_branch>0  ? " and billing.id_branch=".$id_branch."" :'')."
		".($allBranch==1 ? "Group By payment.payment_mode order by billing.bill_id desc": "Group By b.id_branch,payment.payment_mode order by billing.bill_id desc" )."
		");


		$result1 = $sql->result_array();


		$sql1= $this->db->query("SELECT rp.payment_mode,sum(IF(rp.type = 1,rp.payment_amount,-rp.payment_amount)) as amount,r.id_issue_receipt,b.name as branch_name
		FROM ret_issue_rcpt_payment rp
		LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt=rp.id_issue_rcpt
		left join branch b on b.id_branch=r.id_branch
		WHERE  rp.payment_status=1 and r.bill_status=1 and (r.receipt_type!=4 and r.receipt_type!=5 OR r.type = 1)
		and (date(r.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		".($id_branch!='' && $id_branch>0  ? " and r.id_branch=".$id_branch."" :'')."
		AND rp.payment_amount>0 ".($allBranch==1 ? " GROUP BY rp.payment_mode":" GROUP BY rp.payment_mode,r.id_branch")."");

		// echo $this->db->last_query();exit;


		$result2 = $sql1->result_array();

		$result = array_merge($result1,$result2);


		$paymentwise_sales_records  = array();

		foreach($result as $r){




				$amt=$r['amount'];

				$r['amount'] = $paymentwise_sales_records[$r['payment_mode']]['amount'] + $r['amount'];

				//echo $amt." AMount ".$r['amount']." pament mode ".$r['payment_mode']." Final Amount ".$paymentwise_sales_records[$r['payment_mode']]['amount']."<br>";

				$paymentwise_sales_records[$r['payment_mode']] = $r;





			}

			//  print_r($paymentwise_sales_records);exit;

		return array_values($paymentwise_sales_records);

	}
	function get_metalBillRecords($id_branch,$from_date,$to_date)
	{
		$data  = $this->db->query("SELECT m.metal,m.metal_code,count(d.bill_id) as billing,sum(d.item_cost) as sale_amount,b.id_branch,br.name as branch_name,
		    IFNULL(sum(d.net_wt),0) as sold_weight
			FROM ret_billing b
			LEFT JOIN ret_bill_details d on d.bill_id=b.bill_id
			LEFT JOIN ret_product_master p on p.pro_id=d.product_id
			LEFT JOIN ret_category c on c.id_ret_category=p.cat_id
			LEFT JOIN metal m on m.id_metal=c.id_metal
			LEFT JOIN branch br on br.id_branch=b.id_branch
			where b.bill_status='1' and c.id_metal IS NOT NULL and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
		    ".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."
			GROUP by m.id_metal  ".($id_branch!='' ? ",br.id_branch" :'')."");

			$result                      = $data->result_array();
			$metalwise_sales_records     = array();
			 foreach($result as $r){
				   $metalwise_sales_records[] = $r;
				}
		     return $metalwise_sales_records;

	}




	//Credit Tab



	function get_CreditDetils($from_date,$to_date,$id_branch)

	{

            $sql=$this->db->query("SELECT IFNULL(c.credit_amt,0) as due_amt,IFNULL(cc.collection_amt,0) as collection_amt,br.name as branch_name

            FROM ret_billing b

            LEFT JOIN branch br ON br.id_branch=b.id_branch

            LEFT JOIN( SELECT b.id_branch,IFNULL(SUM(b.tot_bill_amount-b.tot_amt_received),0) as credit_amt

                        FROM ret_billing b

                        LEFT JOIN branch br ON br.id_branch=b.id_branch

                        where (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') AND b.bill_status=1 and b.is_credit=1

                        ".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."

                        GROUP by b.id_branch) as c on c.id_branch=b.id_branch

            LEFT JOIN(SELECT b.id_branch,IFNULL(b.tot_amt_received,0) as collection_amt

                        FROM ret_billing b

                        LEFT JOIN branch br ON br.id_branch=b.id_branch

                        where (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') AND b.bill_status=1 and b.bill_type=8

                        ".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."

                        GROUP by b.id_branch) as cc on cc.id_branch=b.id_branch

                        where (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

                        ".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."

            GROUP by b.id_branch");

        return $sql->result_array();

	}





	function get_due_pending_details()

    {

	    $reurn_data=array();

		$sql=$this->db->query("SELECT sum(b.tot_bill_amount-b.tot_amt_received-ifnull(c.tot_amt_received,0)) as pending_amt,br.name as branch_name,b.id_branch

        FROM ret_billing b

        LEFT JOIN branch br on br.id_branch=b.id_branch

        LEFT JOIN(SELECT b.tot_amt_received,b.ref_bill_id

                 FROM ret_billing b

                 WHERE b.bill_type=8) as c on c.ref_bill_id=b.bill_id

        WHERE b.bill_status=1 AND b.credit_status=2 and b.bill_type!=8 AND b.is_credit=1

        GROUP by b.id_branch");

        $credit_details=$sql->result_array();

        foreach($credit_details as $credit)

        {

            $reurn_data[]=array(

                                'pending_amt'=>$credit['pending_amt'],

                                'branch_name'=>$credit['branch_name'],

                                'over_due_amt'=>$this->over_due_pending($credit['id_branch']),

                               );

        }

        return $reurn_data;

    }



    function over_due_pending($id_branch)

    {

        $pending_amt=0;

        $sql=$this->db->query("SELECT sum(b.tot_bill_amount-b.tot_amt_received-ifnull(c.tot_amt_received,0)) as pending_amt,TIMESTAMPDIFF(month,(b.bill_date), current_date()),b.id_branch,b.bill_id

        FROM ret_billing b

        LEFT JOIN branch br on br.id_branch=b.id_branch

        LEFT JOIN(SELECT b.tot_amt_received,b.ref_bill_id

                 FROM ret_billing b

                 WHERE b.bill_type=8) as c on c.ref_bill_id=b.bill_id

        WHERE b.bill_status=1 AND b.credit_status=2 and b.bill_type!=8 AND b.is_credit=1 AND TIMESTAMPDIFF(month,(b.bill_date), current_date())>=3

        ".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."");

        $pending_amt=($sql->row('pending_amt')!=null ? $sql->row('pending_amt'):0);

        return $pending_amt;

    }



	//Credit Tab





	//Stock and Branch Trasnfer



	function get_stock_category_details($FromDt,$ToDt,$id_branch)

	{

	    $return_data=array();

	    $op_date= date('Y-m-d',(strtotime('-1 day',strtotime($FromDt))));

		//print_r($op_date);exit;

		$sql = $this->db->query("SELECT IFNULL(b.name,'') as branch_name,c.name as category_name,

		IFNULL(blc.gross_wt,0) as op_blc_gwt,IFNULL(blc.net_wt,0) as op_blc_nwt,IFNULL(blc.piece,0) as op_blc_pcs,

		IFNULL(INW.gross_wt,0) as inw_gwt,IFNULL(INW.net_wt,0) as inw_nwt,IFNULL(INW.piece,0) as inw_pcs,

		IFNULL(s.gross_wt,0) as sold_gwt,IFNULL(s.net_wt,0) as sold_nwt,IFNULL(s.piece,0) as sold_pcs,

		IFNULL(br_out.gross_wt,0) as br_out_gwt,IFNULL(br_out.net_wt,0) as br_out_nwt,IFNULL(br_out.piece,0) as br_out_pcs



		FROM ret_taging t

		LEFT JOIN ret_product_master p on p.pro_id=t.product_id

		LEFT JOIN branch b on b.id_branch=t.current_branch

		left join ret_category c on c.id_ret_category=p.cat_id

		left join metal m on m.id_metal=c.id_metal



		LEFT JOIN (SELECT  p.cat_id,SUM(s.closing_gwt) as gross_wt,SUM(s.closing_nwt) as net_wt,SUM(s.closing_pcs) as piece,s.date,s.id_branch,b.name as branch_name

        FROM ret_stock_balance s

        LEFT JOIN branch b ON b.id_branch=s.id_branch

        LEFT JOIN ret_product_master p ON p.pro_id=s.id_product

        WHERE s.id_product is NOT null AND date(s.date)='$op_date'

         ".($id_branch!='' ? " and s.id_branch=".$id_branch."" :'')."

        GROUP by p.cat_id,s.id_branch) blc on blc.cat_id=p.cat_id



		LEFT JOIN (SELECT prod.cat_id,tag.tag_id,tag.product_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece

        FROM ret_taging tag

        LEFT JOIN ret_taging_status_log l on l.tag_id=tag.tag_id and l.status=0

        LEFT JOIN ret_product_master prod on prod.pro_id=tag.product_id

        WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt') And l.status=0

        ".($id_branch!='' ? " and l.to_branch=".$id_branch."" :'')."

        GROUP by prod.cat_id,tag.current_branch) INW on INW.cat_id=p.cat_id



		LEFT JOIN (SELECT b.tag_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece,b.product_id,prod.cat_id

		FROM ret_taging tag

		LEFT JOIN ret_bill_details b on b.tag_id=tag.tag_id

		lEFT JOIN ret_billing bill on bill.bill_id=b.bill_id

		LEFT JOIN ret_product_master prod on prod.pro_id=b.product_id

		WHERE  bill.bill_status=1 and date(bill.bill_date) BETWEEN '$FromDt' AND '$ToDt'  AND b.product_id=prod.pro_id

		".($id_branch!='' ? " and bill.id_branch=".$id_branch." " :'')."

		GROUP by prod.cat_id,bill.id_branch) s ON s.cat_id=p.cat_id



		LEFT JOIN (

		SELECT tag.tag_id,tag.product_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece,prod.cat_id

        FROM ret_taging tag

        LEFT JOIN ret_taging_status_log l on l.tag_id=tag.tag_id and (l.status=2 or l.status=3 or l.status=5 or l.status=4)

        LEFT JOIN ret_product_master prod on prod.pro_id=tag.product_id

        WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt')  and (l.status=2 or l.status=3 or l.status=5 or l.status=4)

        ".($id_branch!='' ? " and l.from_branch=".$id_branch."" :'')."

        GROUP by prod.cat_id,tag.current_branch) br_out on br_out.cat_id=p.cat_id

		where t.tag_id is not null  ".($id_branch!='' ? " and t.current_branch=".$id_branch."" :'')."

	    GROUP by p.cat_id,t.current_branch");

	    $data=$sql->result_array();

	    foreach($data as $items)

	    {

	        $return_data[]=array(

	                             'branch_name'      =>$items['branch_name'],

	                             'category_name'    =>$items['category_name'],

	                             'opening_pcs'      =>$items['op_blc_pcs'],

	                             'opening_gwt'      =>$items['op_blc_gwt'],

	                             'opening_nwt'      =>$items['op_blc_nwt'],

	                             'tot_sales_pcs'    =>$items['sold_pcs'],

	                             'tot_sales_gwt'    =>$items['sold_gwt'],

	                             'tot_sales_nwt'    =>$items['sold_nwt'],

	                             'inw_pcs'          =>$items['inw_pcs'],

	                             'inw_gwt'          =>$items['inw_gwt'],

	                             'inw_nwt'          =>$items['inw_nwt'],

	                             'available_pcs'    =>$items['op_blc_pcs']+$items['inw_pcs']-$items['sold_pcs']-$items['br_out_pcs'],

	                             'available_gwt'    =>number_format($items['op_blc_gwt']+$items['inw_gwt']-$items['sold_gwt']-$items['br_out_gwt'],3,'.',''),

	                             'available_gwt'    =>number_format($items['op_blc_nwt']+$items['inw_nwt']-$items['sold_nwt']-$items['br_out_nwt'],3,'.',''),

	                            );

	    }

	   return $return_data;

	}





	function get_branch_transfer_details()

	{

		$sql1=$this->db->query("SELECT pro.product_name,fb.name as from_branch_name,tb.name as to_branch_name,IFNULL(SUM(tag.piece),0) as tot_pcs,IFNULL(SUM(tag.gross_wt),0) as tot_gwt,IFNULL(SUM(tag.gross_wt),0) as tot_nwt,tag.product_id

        FROM ret_branch_transfer b

        LEFT JOIN ret_brch_transfer_tag_items t ON t.transfer_id=b.branch_transfer_id

        LEFT JOIN ret_taging tag ON tag.tag_id=t.tag_id

        LEFT JOIN ret_product_master pro ON pro.pro_id=tag.product_id

        LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch

        LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch

        WHERE b.status=2 AND tag.product_id IS NOT NULL GROUP by tag.product_id");

        $return_data['download_pending']=$sql1->result_array();



        $sql2=$this->db->query("SELECT pro.product_name,fb.name as from_branch_name,tb.name as to_branch_name,IFNULL(SUM(tag.piece),0) as tot_pcs,IFNULL(SUM(tag.gross_wt),0) as tot_gwt,IFNULL(SUM(tag.gross_wt),0) as tot_nwt,tag.product_id

        FROM ret_branch_transfer b

        LEFT JOIN ret_brch_transfer_tag_items t ON t.transfer_id=b.branch_transfer_id

        LEFT JOIN ret_taging tag ON tag.tag_id=t.tag_id

        LEFT JOIN ret_product_master pro ON pro.pro_id=tag.product_id

        LEFT JOIN branch fb ON fb.id_branch=b.transfer_from_branch

        LEFT JOIN branch tb ON tb.id_branch=b.transfer_to_branch

        WHERE b.status=1 AND tag.product_id IS NOT NULL GROUP by tag.product_id");

        $return_data['approved_pending']=$sql2->result_array();



        return $return_data;

	}



	//Stock and Branch Trasnfer





	function get_new_customer($from_date,$to_date,$id_branch)

	{

	    $sql=$this->db->query("SELECT COUNT(c.id_customer) as customer,if(c.added_by=0,'Web',if(c.added_by=2,'Admin','Mobile App')) as added_by,IFNULL(v.village_name,'') as village_zone,

        IFNULL(b.name,'') as branch_name

        FROM customer c

        LEFT JOIN village v on v.id_village=c.id_village

        LEFT JOIN branch b ON b.id_branch=c.id_branch

        WHERE (date(c.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

        GROUP by c.id_village,c.added_by");

        return $sql->result_array();

	}







	//Order Management



	function get_customer_order_details($from_date,$to_date)

	{

	    $sql=$this->db->query("SELECT concat(cus.firstname,'-',cus.mobile) as cus_name,p.product_name,d.totalitems,d.weight,b.name as branch_name,m.order_status

        FROM customerorder c

        LEFT JOIN customerorderdetails d on d.id_customerorder=c.id_customerorder

        LEFT JOIN ret_product_master p ON p.pro_id=d.id_product

        LEFT JOIN customer cus ON cus.id_customer=c.order_to

        LEFT JOIN branch b ON b.id_branch=c.order_from

        LEFT JOIN order_status_message m on m.id_order_msg=d.orderstatus

        WHERE c.order_for=2 and (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ");

        //print_r($this->db->last_query());exit;

         return $sql->result_array();

	}



function get_order_details($from_date,$to_date,$order_for,$id_branch)

	{

	    $sql=$this->db->query("SELECT b.name as branch_name,c.order_from,





	    IFNULL(today_recd.received_pcs,0) as today_received_pcs,IFNULL(today_recd.received_wt,0) as today_received_wt,



	    IFNULL(a.allocation_pending_pcs,0) as allocation_pending_pcs,IFNULL(a.allocation_pending_wt,0) as allocation_pending_wt,



	    IFNULL(alloc_done.allocation_done_pcs,0) as allocation_done_pcs,IFNULL(alloc_done.allocation_done_wt,0) as allocation_done_wt,



	    IFNULL(kar_pend.karigar_pending_pcs,0) as karigar_pending_pcs,IFNULL(kar_pend.karigar_pending_wt,0) as karigar_pending_wt,



	    IFNULL(karigar_over_due.over_due_pcs,0) as karigar_over_due_pcs,IFNULL(karigar_over_due.over_due_wt,0) as karigar_over_due_wt,



	    IFNULL(karigar_delivered.delivered_pcs,0) as karigar_delivered_pcs,IFNULL(karigar_delivered.delivered_wt,0) as karigar_delivered_wt,



	    IFNULL(cus_del.delivery_ready_pcs,0) as cus_delivery_ready_pcs,IFNULL(cus_del.delivery_ready_wt,0) as cus_delivery_ready_wt,



	    IFNULL(del.delivered_pcs,0) as cus_delivered_pcs,IFNULL(del.delivered_wt,0) as cus_delivered_wt,



	    IFNULL(cus_over_due.over_due_pcs,0) as cus_over_due_pcs,IFNULL(cus_over_due.over_due_wt,0) as cus_over_due_wt,



	    IFNULL(cus_pending.pending_pcs,0) as cus_pending_pcs,IFNULL(cus_pending.pending_wt,0) as cus_pending_wt





        FROM customerorder c



        LEFT JOIN branch b ON b.id_branch=c.order_from



        LEFT JOIN customerorderdetails d on d.id_customerorder=c.id_customerorder



        LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as received_pcs,SUM(d.weight) as received_wt,c.order_from

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE  (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

                 and c.order_for='$order_for'  GROUP by c.order_from) as today_recd ON today_recd.order_from=c.order_from



         LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as allocation_pending_pcs,SUM(d.weight) as allocation_pending_wt,c.order_from

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE  (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

                 and c.order_for='$order_for' and d.orderstatus<=0 GROUP by c.order_from) as a ON a.order_from=c.order_from



        LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as allocation_done_pcs,SUM(d.weight) as allocation_done_wt,c.order_from

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE  (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

                 and c.order_for='$order_for' and d.orderstatus=3 GROUP by c.order_from) as alloc_done ON alloc_done.order_from=c.order_from



        LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as karigar_pending_pcs,c.order_from,SUM(d.weight) as karigar_pending_wt

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 LEFT JOIN joborder j on j.id_order=d.id_orderdetails

                 WHERE (date(j.deliverydate) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

                 and c.order_for='$order_for' and j.id is not null GROUP by c.order_from) as kar_pend ON kar_pend.order_from=c.order_from



        LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as over_due_pcs,c.order_from,SUM(d.weight) as over_due_wt

             FROM customerorder c

             LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

              LEFT JOIN joborder j on j.id_order=d.id_orderdetails

             WHERE date(d.smith_due_date)<CURRENT_DATE() and d.orderstatus=3 and c.order_for='$order_for' GROUP by c.order_from) as karigar_over_due ON karigar_over_due.order_from=c.order_from



        LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as delivered_pcs,c.order_from,SUM(d.weight) as delivered_wt

             FROM customerorder c

             LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

              LEFT JOIN joborder j on j.id_order=d.id_orderdetails

             WHERE (date(j.deliveredon) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') and j.orderstatus=4 and c.order_for='$order_for' GROUP by c.order_from) as karigar_delivered ON karigar_delivered.order_from=c.order_from





        LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as order_plced_pcs,c.order_from,SUM(d.weight) as order_placed_wt

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 LEFT JOIN joborder j on j.id_order=d.id_orderdetails

                 WHERE (date(c.order_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

                 and c.order_for='$order_for' and j.id is not null and (d.orderstatus=1 or d.orderstatus=2) GROUP by c.order_from) as p ON p.order_from=c.order_from



         LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as delivery_ready_pcs,c.order_from,SUM(d.weight) as delivery_ready_wt

                 FROM customerorder c

                 LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

                 WHERE (date(d.deliverydate) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') and

                 c.order_for='$order_for' and d.orderstatus=4 GROUP by c.order_from) as cus_del ON cus_del.order_from=c.order_from





        LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as delivered_pcs,c.order_from,SUM(d.weight) as delivered_wt

             FROM customerorder c

             LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

             WHERE (date(d.delivered_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

             and c.order_for='$order_for' and d.orderstatus=5 GROUP by c.order_from) as del ON del.order_from=c.order_from



        LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as pending_pcs,c.order_from,SUM(d.weight) as pending_wt

             FROM customerorder c

             LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

              LEFT JOIN joborder j on j.id_order=d.id_orderdetails

             WHERE date(d.cus_due_date)=CURRENT_DATE() and d.orderstatus<=3 and c.order_for='$order_for' GROUP by c.order_from) as cus_pending ON cus_pending.order_from=c.order_from





         LEFT JOIN(SELECT c.id_customerorder,SUM(d.totalitems) as over_due_pcs,c.order_from,SUM(d.weight) as over_due_wt

             FROM customerorder c

             LEFT JOIN customerorderdetails d ON d.id_customerorder=c.id_customerorder

              LEFT JOIN joborder j on j.id_order=d.id_orderdetails

             WHERE date(d.cus_due_date)<CURRENT_DATE() and d.orderstatus<=3 and c.order_for='$order_for' GROUP by c.order_from) as cus_over_due ON cus_over_due.order_from=c.order_from





        where c.order_from is not null

        GROUP by c.order_from");





        //print_r($this->db->last_query());exit;



        return $sql->result_array();

	}



	//Order Management



		//Sales Chart Details

	function get_sales_summary($from_date,$to_date,$id_branch)

	{

	    $sql=$this->db->query("SELECT IFNULL(sum(d.item_cost),0) as total_sales_amt

        FROM ret_bill_details d

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        LEFT JOIN ret_taging tag on tag.tag_id=d.tag_id

        WHERE b.bill_status=1 AND d.bill_det_id is NOT null and tag.tag_status=1

        and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

        ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."");



        return $sql->row()->total_sales_amt;

	}



	function get_green_tag_sales($from_date,$to_date,$id_branch)

	{

	    $sql=$this->db->query("SELECT COUNT(d.tag_id) as green_tag_sales,IFNULL(sum(d.item_cost),0) as tot_green_tag_amt

                            FROM ret_bill_details d

                            LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

                            LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

                            WHERE b.bill_status=1 AND tag.tag_status=1 and tag.tag_mark=1

                            and (date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')

                            ".($id_branch!='' ? " and b.id_branch=".$id_branch."" :'')."");



        return $sql->row_array();

	}



	function get_creditDetilas($from_date,$to_date,$id_branch)

	{

        $sql = $this->db->query("SELECT count(*) as tot_credit_bill, ifnull(sum(tot_bill_amount-tot_amt_received),0) as tot_due_amount,

        (SELECT ifnull(sum(b.tot_bill_amount),0) as creditreceived from ret_billing as b

        WHERE b.ref_bill_id is not null and b.bill_status=1 and b.bill_type=8 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

        ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."

        ) as creditreceived

        FROM ret_billing as bill

        WHERE bill.bill_type != 8 and bill.is_credit=1 AND date(bill.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

        and bill.bill_status=1 ".($id_branch!='' && $id_branch>0 ? " and bill.id_branch=".$id_branch."" :'')." ");

		return $sql->row_array();

	}



	function get_customer_visit($from_date,$to_date,$id_branch)

	{

	    $data=[];

	    $new_cus=$this->db->query("SELECT COUNT(b.bill_cus_id) as tot_cus

                                    FROM ret_billing b

                                    WHERE date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."  AND b.bill_cus_id not in (SELECT b.bill_cus_id

                                    FROM ret_billing b

                                    WHERE date(b.bill_date)<='$from_date')

                                ");



        $old_cus=$this->db->query("SELECT COUNT(b.bill_cus_id) as tot_cus

                                    FROM ret_billing b

                                    WHERE date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."  AND b.bill_cus_id in (SELECT b.bill_cus_id

                                    FROM ret_billing b

                                    WHERE date(b.bill_date)<'$from_date')

                                ");



        //print_r($this->db->last_query());exit;

        $data['new_cus']=$new_cus->row()->tot_cus;

        $data['old_cus']=$old_cus->row()->tot_cus;



        return $data;

	}





	function get_profit_details($from_date,$to_date,$id_branch)

	{

	    $sql=$this->db->query("SELECT IFNULL((tag.net_wt*tag.buy_rate),0) as purchase_cost,d.item_cost,d.tag_id

        FROM ret_taging tag

        LEFT JOIN ret_bill_details d on d.tag_id=tag.tag_id

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        WHERE tag.tag_status=1 AND b.bill_status=1

        AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'

        ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."");

        return $sql->result_array();

	}



	function getEstimationDetails($from_date,$to_date,$id_branch)

	{

		$sql = $this->db->query("SELECT (SELECT count(estimation_id) AS totestimation

										FROM ret_estimation as est WHERE date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'  ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'').") as created,



										(select count(*) from (SELECT count(estimation_id) AS totestimation

										FROM ret_estimation as est

										LEFT JOIN ret_estimation_items AS estitm ON estitm.esti_id = est.estimation_id

										WHERE estitm.purchase_status = 1 AND date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'')." GROUP BY esti_id) as sold) as sold,



									    (select count(*) from (SELECT COUNT(d.tag_id) as tot_tag_sales

                                        FROM ret_bill_details d

                                        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

                                        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

                                        WHERE b.bill_status=1 AND tag.tag_status=1 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')." GROUP BY d.tag_id) as tot_tag_sales) as tot_tag_sales,





									    (select count(*) from (SELECT COUNT(d.tag_id) as green_tag_sales

                                        FROM ret_bill_details d

                                        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

                                        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

                                        WHERE b.bill_status=1 AND tag.tag_status=1 and tag.tag_mark=1 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')." GROUP BY d.tag_id) as green_tag_sales) as green_tag_sales,



									    (select count(*) from (SELECT COUNT(d.tag_id) as tot_tag_ret

                                        FROM ret_bill_details d

                                        LEFT JOIN ret_taging tag ON tag.tag_id=d.tag_id

                                        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

                                        WHERE b.bill_status=1 AND tag.tag_status=6 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')." GROUP BY d.tag_id) as tot_tag_ret) as tot_tag_ret,







										(select count(*) from (SELECT count(estimation_id) AS totestimation

										FROM ret_estimation as est

										LEFT JOIN ret_estimation_items AS estitm ON estitm.esti_id = est.estimation_id

										WHERE estitm.purchase_status = 0 AND date(est.estimation_datetime) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and est.id_branch=".$id_branch."" :'')." GROUP BY esti_id) as unsold) as unsold");

		return $sql->row_array();

	}





	function get_branchwise_sales($from_date,$to_date,$id_branch)

	{

	    $sql=$this->db->query("SELECT SUM(d.item_cost) as amount ,br.name as branch_name

        FROM ret_bill_details d

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        LEFT JOIN branch br ON br.id_branch=b.id_branch

        WHERE b.bill_status=1 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."  group by b.id_branch");

        return $sql->result_array();

	}



	function get_modewise_sales($from_date,$to_date,$id_branch)

	{

	    $sql=$this->db->query("SELECT SUM(d.item_cost) as amount,pay.payment_mode

        FROM ret_bill_details d

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        LEFT JOIN branch br ON br.id_branch=b.id_branch

        LEFT JOIN ret_billing_payment pay ON pay.bill_id=b.bill_id

        WHERE b.bill_status=1 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."  group by pay.payment_mode");

        return $sql->result_array();

	}







	function get_product_wise_sales($from_date,$to_date,$id_branch)

	{

	    $sql=$this->db->query("SELECT SUM(d.item_cost) as amount,pro.product_name as pro_name,LEFT(UPPER(pro.product_name),4)  as pro_short_name

        FROM ret_bill_details d

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        LEFT JOIN branch br ON br.id_branch=b.id_branch

      	LEFT JOIN ret_product_master p ON p.pro_id=d.product_id

        LEFT JOIN ret_product_master pro ON pro.pro_id=p.parent_id

        WHERE b.bill_status=1 AND pro.parent_id=0 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')." GROUP by pro.product_name");

        return $sql->result_array();

	}





	function get_sales_growth_details($id_branch)

	{

	    $sql=$this->db->query("SELECT IFNULL(sum(d.item_cost),0) as value,date_format(b.bill_date,'%d-%m-%Y') as date

        FROM ret_bill_details d

        LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

        WHERE b.bill_status=1 AND d.bill_det_id is NOT null AND date(b.bill_date) >= DATE(NOW()) - INTERVAL 10 DAY

        ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."

        GROUP by b.bill_date");

         return $sql->result_array();

	}



	//Sales Chart Details





	 //stock chart details

   	function get_branch_stock_details($FromDt,$ToDt,$id_branch)

	{

	    $return_data=array();

	    $op_date= date('Y-m-d',(strtotime('-1 day',strtotime($FromDt))));

		//print_r($op_date);exit;

		$sql = $this->db->query("SELECT IFNULL(b.name,'') as branch_name,c.name as category_name,

		IFNULL(blc.gross_wt,0) as op_blc_gwt,IFNULL(blc.net_wt,0) as op_blc_nwt,IFNULL(blc.piece,0) as op_blc_pcs,

		IFNULL(INW.gross_wt,0) as inw_gwt,IFNULL(INW.net_wt,0) as inw_nwt,IFNULL(INW.piece,0) as inw_pcs,

		IFNULL(s.gross_wt,0) as sold_gwt,IFNULL(s.net_wt,0) as sold_nwt,IFNULL(s.piece,0) as sold_pcs,

		IFNULL(br_out.gross_wt,0) as br_out_gwt,IFNULL(br_out.net_wt,0) as br_out_nwt,IFNULL(br_out.piece,0) as br_out_pcs



		FROM ret_taging t

		LEFT JOIN ret_product_master p on p.pro_id=t.product_id

		LEFT JOIN branch b on b.id_branch=t.current_branch

		left join ret_category c on c.id_ret_category=p.cat_id

		left join metal m on m.id_metal=c.id_metal



		LEFT JOIN (SELECT  p.cat_id,SUM(s.closing_gwt) as gross_wt,SUM(s.closing_nwt) as net_wt,SUM(s.closing_pcs) as piece,s.date,s.id_branch,b.name as branch_name

        FROM ret_stock_balance s

        LEFT JOIN branch b ON b.id_branch=s.id_branch

        LEFT JOIN ret_product_master p ON p.pro_id=s.id_product

        WHERE s.id_product is NOT null AND date(s.date)='$op_date'

         ".($id_branch!='' ? " and s.id_branch=".$id_branch."" :'')."

        GROUP by s.id_branch) blc on blc.id_branch=t.current_branch



		LEFT JOIN (SELECT tag.current_branch,prod.cat_id,tag.tag_id,tag.product_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece

        FROM ret_taging tag

        LEFT JOIN ret_taging_status_log l on l.tag_id=tag.tag_id and l.status=0

        LEFT JOIN ret_product_master prod on prod.pro_id=tag.product_id

        WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt') And l.status=0

        ".($id_branch!='' ? " and l.to_branch=".$id_branch."" :'')."

        GROUP by tag.current_branch) INW on INW.current_branch=t.current_branch



		LEFT JOIN (SELECT b.tag_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece,b.product_id,prod.cat_id,tag.current_branch

		FROM ret_taging tag

		LEFT JOIN ret_bill_details b on b.tag_id=tag.tag_id

		lEFT JOIN ret_billing bill on bill.bill_id=b.bill_id

		LEFT JOIN ret_product_master prod on prod.pro_id=b.product_id

		WHERE  bill.bill_status=1 and date(bill.bill_date) BETWEEN '$FromDt' AND '$ToDt'  AND b.product_id=prod.pro_id

		".($id_branch!='' ? " and bill.id_branch=".$id_branch." " :'')."

		GROUP by tag.current_branch) s ON s.current_branch=t.current_branch



		LEFT JOIN (

		SELECT tag.tag_id,tag.product_id,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece,prod.cat_id,tag.current_branch

        FROM ret_taging tag

        LEFT JOIN ret_taging_status_log l on l.tag_id=tag.tag_id and (l.status=2 or l.status=3 or l.status=5 or l.status=4)

        LEFT JOIN ret_product_master prod on prod.pro_id=tag.product_id

        WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt')  and (l.status=2 or l.status=3 or l.status=5 or l.status=4)

        ".($id_branch!='' ? " and l.from_branch=".$id_branch."" :'')."

        GROUP by tag.current_branch) br_out on br_out.current_branch=t.current_branch



		where t.tag_id is not null  ".($id_branch!='' ? " and t.current_branch=".$id_branch."" :'')."

	    GROUP by t.current_branch");

	    $data=$sql->result_array();

	    //print_r($this->db->last_query());exit;

	    foreach($data as $items)

	    {

	        $return_data=array(

	                             'branch_name'      =>$items['branch_name'],

	                             'opening_pcs'      =>$items['op_blc_pcs'],

	                             'opening_gwt'      =>$items['op_blc_gwt'],

	                             'opening_nwt'      =>$items['op_blc_nwt'],

	                             'tot_sales_pcs'    =>$items['sold_pcs'],

	                             'tot_sales_gwt'    =>$items['sold_gwt'],

	                             'tot_sales_nwt'    =>$items['sold_nwt'],

	                             'inw_pcs'          =>$items['inw_pcs'],

	                             'inw_gwt'          =>$items['inw_gwt'],

	                             'inw_nwt'          =>$items['inw_nwt'],

	                             'available_pcs'    =>$items['op_blc_pcs']+$items['inw_pcs']-$items['sold_pcs']-$items['br_out_pcs'],

	                             'available_gwt'    =>number_format($items['op_blc_gwt']+$items['inw_gwt']-$items['sold_gwt']-$items['br_out_gwt'],3,'.',''),

	                             'available_gwt'    =>number_format($items['op_blc_nwt']+$items['inw_nwt']-$items['sold_nwt']-$items['br_out_nwt'],3,'.',''),

	                            );

	    }

	   return $return_data;

	}









    function get_product_stock_details()

    {

        $sql=$this->db->query("SELECT IFNULL(SUM(t.piece),0) as pcs,mp.product_name as pro_name,LEFT(UPPER(mp.product_name),4)  as pro_short_name

        FROM ret_taging t

        LEFT JOIN ret_product_master p ON p.pro_id=t.product_id

        LEFT JOIN ret_product_master mp on mp.pro_id = p.parent_id

        WHERE t.tag_status=0

        GROUP BY mp.product_name");

         return $sql->result_array();

    }



    //stock chart details


	function get_all_branch_store()
	{
		$sql = $this->db->query("SELECT * FROM branch WHERE active=1 AND is_ho=0;");

		// print_r($this->db->last_query());exit;

		return $sql->result_array();
	}

	function get_deposit_details_branchwise($from_date, $to_date, $id_branch)
	{
		$date_restriction = "2024-07-31";

		$cur_date = date('Y-m-d');

		$from_date = date('Y-m-d', strtotime($from_date));
		$to_date = date('Y-m-d', strtotime($to_date));

		$sql = $this->db->query("SELECT IFNULL((SELECT SUM(bill_pay.payment_amount) as bill_pay FROM ret_billing_payment AS bill_pay
		LEFT JOIN ret_billing AS bill ON bill.bill_id = bill_pay.bill_id
		WHERE payment_mode = 'Cash' AND bill_status = 1
		AND DATE(bill.bill_date) >= '$date_restriction'
		AND bill.id_branch in ('$id_branch') AND date(bill.bill_date) <= '$to_date'),0) as tot_rec,

			IFNULL((SELECT SUM( IF(ir_pay.type = 1, ir_pay.payment_amount, (ir_pay.payment_amount * -1)) ) AS cash_amt
			FROM  ret_issue_rcpt_payment AS ir_pay
			LEFT JOIN ret_issue_receipt AS ir ON ir.id_issue_receipt = ir_pay.id_issue_rcpt
			
			WHERE payment_mode = 'Cash' AND bill_status = 1
            AND DATE(ir_pay.payment_date) >= '$date_restriction'
			AND ir.id_branch in ('$id_branch') 
			AND date(ir.bill_date) <= '$to_date'),0) as tot_bill,
            
            
            IFNULL((SELECT sum(dep_amount) pre_tot_dep_amount FROM ret_bank_deposit WHERE date(created_on) < '$to_date' AND dep_branch in ('$id_branch') ),0) as tot_dep_amount,
            
            (SELECT IFNULL((SELECT SUM(bill_pay.payment_amount) as bill_pay FROM ret_billing_payment AS bill_pay
			LEFT JOIN ret_billing AS bill ON bill.bill_id = bill_pay.bill_id
			WHERE payment_mode = 'Cash' AND bill_status = 1
			AND DATE(bill.bill_date) >= '$date_restriction'
			AND bill.id_branch in ('$id_branch')  AND date(bill.bill_date) BETWEEN '$from_date' AND '$to_date'),0) +

			IFNULL((SELECT SUM( IF(ir_pay.type = 1, ir_pay.payment_amount, (ir_pay.payment_amount * -1)) ) AS cash_amt
			FROM  ret_issue_rcpt_payment AS ir_pay
			LEFT JOIN ret_issue_receipt AS ir ON ir.id_issue_receipt = ir_pay.id_issue_rcpt
			
			WHERE payment_mode = 'Cash' AND bill_status = 1
            AND DATE(ir_pay.payment_date) >= '$date_restriction'
			AND ir.id_branch in ('$id_branch') 
			AND date(ir.bill_date) BETWEEN '$from_date' AND '$to_date'),0) ) as inward,
            
            IFNULL((SELECT sum(dep_amount) pre_tot_dep_amount FROM ret_bank_deposit WHERE date(created_on) BETWEEN '$from_date' AND '$to_date' AND dep_branch in ('$id_branch') ),0) as deposit");

		// echo $this->db->last_query();exit;

		return $sql->result_array();
	}

	function get_previous_date_closing($dep_branch = 0, $from_date)
	{

		$date_restriction = "2024-07-31";

		$sql = "SELECT (dep_det.dep_pay) dep_amount,(issrec.pay) iss_pay,(bill_det.pay) rec_pay FROM ret_bank_deposit b
     JOIN (SELECT IFNULL(SUM(if(irp.type=1,(irp.payment_amount),(irp.payment_amount*-1))),0) pay,irp.payment_date,ir.id_branch FROM ret_issue_receipt ir
                  LEFT JOIN ret_issue_rcpt_payment irp ON irp.id_issue_rcpt=ir.id_issue_receipt
                  WHERE irp.payment_mode='Cash' AND DATE(irp.payment_date) >='$date_restriction' AND date(ir.bill_date)<'$from_date' AND ir.id_branch='$dep_branch'
                  AND ir.bill_status=1) issrec ON 1=1
                  
		JOIN(SELECT IFNULL(SUM(p.payment_amount),0) pay,date(b.bill_date) bill_date,b.id_branch FROM ret_billing b
            LEFT JOIN ret_billing_payment p ON p.bill_id=b.bill_id
            WHERE b.bill_status=1 AND b.id_branch='$dep_branch' AND p.payment_mode='Cash' AND date(b.bill_date) >='$date_restriction' AND date(b.bill_date)<'$from_date'
            ) bill_det ON 1=1
            
            JOIN (SELECT IFNULL(sum(b.dep_amount),0) as dep_pay FROM ret_bank_deposit b 
                 WHERE b.dep_branch='$dep_branch' AND date(b.created_on)<'$from_date' ) dep_det ON 1=1
                 LIMIT 1";

		//  echo $sql;exit;

		$sql = $this->db->query($sql);

		// echo "<pre>";print_r($this->db->last_query());exit;

		// echo "<pre>"; print_r($sql->row_array());exit;

		$closing_det_array = $sql->row_array();

		$closing_amt = $closing_det_array['iss_pay'] + $closing_det_array['rec_pay'] - $closing_det_array['dep_amount'];

		return $closing_amt;
	}

	//cash deposit branchwise

    function get_stock_details_dashboard($from,$to,$id_branch)
    {
       $stock_detail = [];
       if($from != '' && $to != ''){

           $d1 = date_create($from);
           $d2 = date_create($to);
           $FromDt = date_format($d1,"Y-m-d");
           $ToDt = date_format($d2,"Y-m-d");
           }
        $op_blc_to_date= date('Y-m-d',(strtotime('-1 day',strtotime($FromDt))));
	$sql= $this->db->query("SELECT m.id_metal,c.name as category_name,
			(IFNULL(blc.gross_wt,0)) as op_blc_gwt,
			(IFNULL(blc.net_wt,0)) as op_blc_nwt,
			(IFNULL(blc.piece,0)) as op_blc_pcs,
			IFNULL(INW.gross_wt,0) as inw_gwt,IFNULL(INW.net_wt,0) as inw_nwt,IFNULL(INW.piece,0) as inw_pcs,
			IFNULL(s.gross_wt,0) as sold_gwt,IFNULL(s.net_wt,0) as sold_nwt,IFNULL(s.piece,0) as sold_pcs,
			IFNULL(br_out.gross_wt,0) as br_out_gwt,IFNULL(br_out.net_wt,0) as br_out_nwt,IFNULL(br_out.piece,0) as br_out_pcs,
			IFNULL(in_trans.gross_wt,0) as in_trans_gwt,IFNULL(in_trans.net_wt,0) as in_trans_nwt,IFNULL(in_trans.piece,0) as in_trans_pcs,
			IFNULL(current.piece,0) as closing_pcs, IFNULL(current.gross_wt,0) as closing_gwt, IFNULL(current.net_wt,0) as closing_nwt,
			Date_Format(current_date(),'%d-%m-%Y') as date_add,m.metal as metal_name
			FROM ret_taging t
			LEFT JOIN ret_product_master p on p.pro_id=t.product_id
			LEFT JOIN branch b on b.id_branch=t.current_branch
			left join ret_category c on c.id_ret_category=p.cat_id
			left join metal m on m.id_metal=c.id_metal
			LEFT JOIN(SELECT m.id_metal,tag.tag_code,tag.tag_id,tag.old_tag_id,p.product_name, ifnull(sum(tag.gross_wt),0) as gross_wt,
			ifnull(SUM(tag.net_wt),0) as net_wt,
			ifnull(SUM(tag.piece),0) as piece,tag.product_id as id_product,p.cat_id
			FROM ret_taging_status_log m1
			LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date) <= '".$op_blc_to_date."')
			LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id
			LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id
			left join ret_category c on c.id_ret_category=p.cat_id
			left join metal m on m.id_metal=c.id_metal
			WHERE m2.id_tag_status_log IS NULL  and m1.issuspensestock = 0
			".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."
			AND (m1.status = 0 OR m1.status = 6) AND date(m1.date) <= '".$op_blc_to_date."'
			GROUP BY m.id_metal) as blc ON  blc.id_metal = m.id_metal
			LEFT JOIN(SELECT m.id_metal,tag.tag_id,tag.product_id,p.cat_id,ifnull(sum(tag.gross_wt),0) as gross_wt,
			ifnull(SUM(tag.net_wt),0) as net_wt, ifnull(SUM(tag.piece),0) as piece
			FROM ret_taging_status_log m1
			LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date)  BETWEEN '$FromDt' AND '$ToDt' ".($id_branch!='' ? " and m2.to_branch=".$id_branch."" :'')." and m2.status = 0)
			LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id
			LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id
			left join ret_category c on c.id_ret_category=p.cat_id
            left join metal m on m.id_metal=c.id_metal
			WHERE tag.tag_id IS NOT NULL  and m1.issuspensestock = 0
			".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."
			AND (m1.status = 0) AND date(m1.date) BETWEEN '$FromDt' AND '$ToDt' GROUP by m.id_metal) INW on  INW.id_metal = m.id_metal
			LEFT JOIN (SELECT m.id_metal,sum(tag.gross_wt) as gross_wt,SUM(tag.net_wt) as net_wt,
			SUM(b.piece) as piece,b.product_id,p.cat_id
			FROM ret_taging tag
			LEFT JOIN ret_bill_details b on b.tag_id = tag.tag_id
			LEFT JOIN ret_billing bill on bill.bill_id = b.bill_id
			LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id
			left join ret_category c on c.id_ret_category=p.cat_id
			left join metal m on m.id_metal=c.id_metal
			WHERE bill.bill_status = 1 and
			(date(bill.bill_date) BETWEEN '$FromDt' AND '$ToDt') AND b.product_id = p.pro_id
			".($id_branch!='' ? " and bill.id_branch=".$id_branch." " :'')."
			GROUP by id_metal ) s ON  s.id_metal = m.id_metal
			LEFT JOIN (SELECT m.id_metal,tag.product_id,p.cat_id,sum(tag.gross_wt) as gross_wt,
				SUM(tag.net_wt) as net_wt,SUM(tag.piece) as piece
				FROM ret_taging tag
				LEFT JOIN ret_taging_status_log l on l.tag_id = tag.tag_id and l.from_branch = ".$id_branch." and (l.status=2 or l.status=3 or l.status=5 or l.status=4 or l.status=9 or l.status=12 or l.status=10 or l.status=7)
				LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id
				left join ret_category c on c.id_ret_category=p.cat_id
				left join metal m on m.id_metal=c.id_metal
			WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt') and
			(l.status=2 or l.status=3 or l.status=5 or l.status=4 or l.status=9 or l.status=12 or l.status=10 or l.status=7)
			".($id_branch!='' ? " and l.from_branch=".$id_branch."" :'')."
			 GROUP by m.id_metal) br_out on br_out.id_metal = m.id_metal
			LEFT JOIN (SELECT  m.id_metal,tag.product_id, ifnull(sum(tag.gross_wt),0) as gross_wt,
			ifnull(SUM(tag.net_wt),0) as net_wt,
			ifnull(SUM(tag.piece),0) as piece,p.cat_id
			FROM ret_taging_status_log m1
			LEFT JOIN ret_taging_status_log m2 ON (m1.tag_id = m2.tag_id AND m1.id_tag_status_log < m2.id_tag_status_log AND date(m2.date) <= '$ToDt')
			LEFT JOIN ret_taging as tag ON tag.tag_id = m1.tag_id
			LEFT JOIN ret_product_master as p ON p.pro_id = tag.product_id
			left join ret_category c on c.id_ret_category=p.cat_id
			left join metal m on m.id_metal=c.id_metal
			WHERE m2.id_tag_status_log IS NULL
			".($id_branch!='' ? " and m1.to_branch=".$id_branch."" :'')."
			AND m1.status = 0 AND date(m1.date) <= '$ToDt' GROUP by  m.id_metal) as current ON  current.id_metal =  m.id_metal
			LEFT JOIN (SELECT  m.id_metal,t.tag_id,t.product_id,sum(t.piece) as piece,SUM(t.gross_wt) as gross_wt,sum(t.net_wt) as net_wt,p.product_name,
			p.cat_id
			FROM ret_taging_status_log l
			LEFT JOIN ret_taging t ON t.tag_id=l.tag_id
			LEFT JOIN ret_product_master p ON p.pro_id=t.product_id
			left join ret_category c on c.id_ret_category=p.cat_id
            left join metal m on m.id_metal=c.id_metal
			WHERE (date(l.date) BETWEEN '$FromDt' AND '$ToDt') and t.tag_status=4
			".($id_branch !='' ? " and l.from_branch=".$id_branch."" :'')."
			GROUP by m.id_metal ) in_trans on  in_trans.id_metal = m.id_metal
			where t.tag_id is not null

			GROUP by  m.id_metal
			order by c.sort,p.pro_id ASC ");
            $result = $sql->result_array();
            foreach($result as $r){
				$stock_detail[$r['metal_name']][] = $r;
			}
    	    return $stock_detail;
    }

    function approve_contract_price()
    {
    	$sql=$this->db->query("SELECT IFNULL(count(rk.status),0) as contract_price_count
    	From ret_karikar_items_wastage rk
    	where rk.status=0");
    	return $sql->row_array();
    }
    function approve_branch_transfer()
    {
    	$sql=$this->db->query("SELECT IFNULL(SUM(rb.pieces),0) as pcs,IFNULL(count(rb.status),0) as branch_transfer_count
    	From ret_branch_transfer rb
    	where rb.status=1 ");
    	return $sql->row_array();
    }
    function approve_branch_download()
    {
    	$sql=$this->db->query("SELECT IFNULL(SUM(rt.pieces),0) as pcs, IFNULL(count(rt.status),0) as branch_download_count
    	From ret_branch_transfer rt
    	where rt.status=4");
    	return $sql->row_array();
    }

    function approval_contract_price()
	{
			$sql=$this->db->query(" SELECT rb.id_karikar,rk.firstname as supplier,
			COUNT(CASE WHEN IFNULL(rb.status,0) =0 THEN 1 END) as yet_to_approve,
			COUNT(CASE WHEN IFNULL(rb.status,0) =1 THEN 1 END) as approved,
			COUNT(CASE WHEN IFNULL(rb.status,0) =2 THEN 1 END) as rejected,
			COUNT(CASE WHEN IFNULL(rb.status,0) =3 THEN 1 END) as hold
			From ret_karikar_items_wastage rb
			LEFT JOIN ret_karigar rk ON rb.id_karikar = rk.id_karigar
			where rb.status is NOT NULL
			");
		return $sql->result_array();
	}
	function get_gross_profit_details($data)
    {
        $id_profile = $this->session->userdata('profile_id');
        $profile_settings = $this->get_profile_settings($id_profile);

        $sql=$this->db->query("SELECT IFNULL(sum(d.gross_wt),0.00) as sale_wt, IFNULL(sum(d.item_cost),0.00) as sale_amount,IFNULL(sum(b.tot_discount),0.00) as discount_amount
        from ret_billing b
        Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        WHERE b.bill_id is not null and b.bill_status=1
        ".($data['id_branch']!='' && $data['id_branch'] !='0' ? " and b.id_branch = ".$data['id_branch']."" :'' )."
         ".($data['from_date'] != '' && $data['to_date']!='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'"' : '')."
         ".($data['id_metal'] != '' && $data['id_metal']>0 ? ' and pro.metal_type ='.$data['id_metal']: '')."
         ".($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") )."
         ");

        return $sql->row_array();

    }


	function get_dashboard_sales_glance($from_date, $to_date,$id_branch)
    {
        $sql=$this->db->query("SELECT COUNT(DISTINCT b.bill_id) as sales_bill_count,IFNULL(sum(d.gross_wt),0.00) as sale_gwt,
        IFNULL(sum(d.net_wt),0.00) as sale_nwt, IFNULL(sum(d.item_cost),0.00) as sale_amount,IFNULL(sum(b.tot_discount),0.00) as sale_discount,
        IFNULL(SUM(IF(d.status = 2,d.return_item_cost,0)),0) as sales_return_amt,IFNULL(SUM(dia.diawt),0.00) as sale_diawt,
        IFNULL(count(DISTINCT IF(d.status = 2, d.bill_det_id,0)),0) as sales_return_count
        from ret_billing b
        Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        LEFT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        LEFT JOIN (SELECT IFNULL(SUM(  s.wt),0) as diawt,IFNULL(SUM(s.price),0) as stn_amt,s.bill_det_id
			FROM ret_billing_item_stones s
			LEFT JOIN ret_bill_details d ON d.bill_det_id = s.bill_det_id
			LEFT JOIN ret_billing b on b.bill_id=d.bill_id
			LEFT JOIN ret_stone st ON st.stone_id = s.stone_id
			LEFT JOIN ret_uom m ON m.uom_id = s.uom_id
			WHERE st.stone_type = 1 GROUP By s.bill_det_id ) dia ON dia.bill_det_id = d.bill_det_id
        WHERE b.bill_id is not null and b.bill_status=1 and b.is_eda=1
        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
         ");



        return $sql->row_array();

    }

	function get_top_selling($from_date, $to_date,$id_branch)
    {
        $sql=$this->db->query("SELECT pro.product_name as product_name,COUNT(b.bill_id) as sales_bill_count
        from ret_billing b
        Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        RIGHT JOIN ret_product_master pro on pro.pro_id = d.product_id
        LEFT JOIN ret_category cat on cat.id_ret_category = pro.cat_id
        WHERE b.bill_id is not null and b.bill_status=1
        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 GROUP by pro.pro_id ORDER BY sales_bill_count DESC LIMIT 5
         ");

        return $sql->result_array();

    }

	function get_top_sellers($from_date, $to_date,$id_branch)
    {
        $sql=$this->db->query("SELECT k.firstname as karigar_name,COUNT(b.bill_id) as sales_bill_count
        from ret_billing b
		Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        RIGHT JOIN ret_taging tag on tag.tag_id = d.tag_id
        RIGHT JOIN ret_lot_inwards i ON i.lot_no=tag.tag_lot_id
        RIGHT JOIN ret_karigar k ON k.id_karigar = i.gold_smith
        WHERE b.bill_id is not null and b.bill_status=1
        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 GROUP by k.id_karigar ORDER BY sales_bill_count DESC LIMIT 5
         ");

        return $sql->result_array();

    }

	function get_monthly_sales($fy_year,$id_branch)
    {

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

				LEFT JOIN branch br ON br.id_branch=b.id_branch

				WHERE

				b.id_branch = '".$branch['id_branch']."'

				and

               MONTH(b.bill_date) ='".$month."'

			   ".( $fy_year!='' && $fy_year !='0' ? " and b.fin_year_code = ".$fy_year."" :'' )."


		        GROUP by b.id_branch,MONTH(b.bill_date)")->row();



				$array_data[]= $get->total_net_wt != null ? (float) $get->total_net_wt:0.00;

		     }
			 $array[]=$array_data;


		}

		$return_data['branch'] =  $arrayBranch;

		$return_data['data'] =  $array;
    //    $sql=$this->db->query()

        return $return_data;

    }

    function get_monthly_sales_mobile($fy_year,$id_branch)
    {
        $arrayBranch=$this->db->query("select * from branch where active = 1  ".($id_branch!='' && $id_branch !='0' ? " and id_branch = ".$id_branch."" :'' )."")->result_array();

		$arrayMonth=['APR'=>4,'MAY'=>5,'JUN'=>6,'JUL'=>7,'AUG'=>8,'SEP'=>9,'OCT'=>10,'NOV'=>11,'DEC'=>12,'JAN'=>1,'FEB'=>2,'MAR'=>3];

		// $arrayBranch=
        $array=[];

         $array_month_lable=[];

		foreach($arrayBranch as $id=>$branch){

		    $array_data=[];

		    $array_month_lable=[];

		    $branch_array[]=$branch['name'];

		    	foreach($arrayMonth as $key=>$month){

    				$get=$this->db->query("SELECT IFNULL(SUM(d.net_wt),0.00 )as total_net_wt,date_format(b.bill_date,'%M') as bill_month,br.name as branch_name

    				FROM ret_bill_details d

    				LEFT JOIN ret_billing b ON b.bill_id=d.bill_id

    				LEFT JOIN branch br ON br.id_branch=b.id_branch

    				WHERE

    				b.id_branch = '".$branch['id_branch']."'

    				and

                   MONTH(b.bill_date) ='".$month."'

    			   ".( $fy_year!='' && $fy_year !='0' ? " and b.fin_year_code = ".$fy_year."" :'' )."


    		        GROUP by b.id_branch,MONTH(b.bill_date)")->row();



    				$array_data[]= $get->total_net_wt != null ? (float) $get->total_net_wt:0.00;

    				$array_month_lable[]=$key;

		    	}

		    	$array[]=$array_data;

	     }





		$return_data['branch'] =  $branch_array;

		$return_data['value'] =  $array;

		$return_data['month'] =  $array_month_lable;

	    $return_data['colour_code'] =  SELF::COLOUR_CODE;
    //    $sql=$this->db->query()

        return $return_data;

    }

	function get_financial_year()
	{
		$data=$this->db->query("SELECT fin_id,fin_year_name,fin_year_code,fin_year_from,fin_year_to,fin_status as status
		FROM ret_financial_year ");
		return $data->result_array();
	}

	function get_store_sales($from_date, $to_date,$id_branch)
    {
        $multiple_id_branch = implode(' , ', $id_branch);
		if($multiple_id_branch != '')
		{
			$branch = $multiple_id_branch;
		}else{
			$branch = $id_branch;
		}
        $return_data=[];
        $sql=$this->db->query("SELECT br.name as branch_name,SUM(IFNULL(d.item_cost,0.00)) as branch_sales
        FROM ret_billing b
		LEFT JOIN ret_bill_details d on d.bill_id = b.bill_id
		LEFT JOIN branch br ON br.id_branch=b.id_branch
        WHERE b.bill_id is not null and b.bill_status=1
        ".($branch!='' && $branch !='0' ? " and b.id_branch in ( ".$branch.")" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		 GROUP by b.id_branch
         ");
         $data= $sql->result_array();
         $sum=0;
         foreach($data as $key=>$value){
             $sum+=$value['branch_sales'];
         }

         foreach($data as $key=>$value){

             number_format((float)$number, $decimalPlaces, '.', '');
             $value['branch_sales_percentage']= number_format((float)($value['branch_sales']/$sum*100),2, '.', '');
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




	function get_branch_wastage($from_date, $to_date,$id_branch)
    {
        $sql=$this->db->query("SELECT br.name as branch_name,SUM(IFNULL(d.wastage_percent,0.00))/COUNT(d.bill_det_id) as branch_wastage_va
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

	function get_product_sales($from_date, $to_date, $id_branch)
	{
		$sql = $this->db->query("SELECT
				pro.product_name as product_name,
				SUM(IFNULL(d.item_cost, 0.00)) as product_sales
			FROM
				ret_billing b
			LEFT JOIN
				ret_bill_details d ON d.bill_id = b.bill_id
			LEFT JOIN
				ret_product_master pro ON pro.pro_id = d.product_id
			WHERE
				b.bill_id IS NOT NULL AND b.bill_status = 1 and pro.pro_id IS NOT NULL
				".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
                ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
			GROUP BY
				pro.pro_id

				");

		return $sql->result_array();

	}

	function get_employee_sales($from_date, $to_date, $id_branch)
	{
		$id_profile = $this->session->userdata('profile_id');
		$profile_settings = $this->get_profile_settings($id_profile);

		$sql = $this->db->query("SELECT
					IFNULL(emp.firstname,'LDJ') as emp_name,
					SUM( IFNULL(d.item_cost, 0.00)) as emp_sales
				FROM
					ret_billing b
				LEFT JOIN
					ret_bill_details d ON d.bill_id = b.bill_id
				LEFT JOIN
					ret_estimation_items est_item ON est_item.est_item_id = d.esti_item_id
				LEFT JOIN
					ret_estimation est ON est.estimation_id = est_item.esti_id
				LEFT JOIN
				employee emp ON emp.id_employee = est.created_by

				WHere  b.bill_id IS NOT NULL AND b.bill_status = 1
				".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
                ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
				".($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") )."
				GROUP BY
				emp.id_employee;

				");

		return $sql->result_array();

	}


	function get_section_sales($from_date, $to_date, $id_branch)
	{
		$id_profile = $this->session->userdata('profile_id');
		$profile_settings = $this->get_profile_settings($id_profile);

		$sql = $this->db->query("SELECT sec.section_name,SUM(IFNULL(d.item_cost,0.00)) as section_sales
								from ret_billing b
								Left JOIN ret_bill_details d on d.bill_id = b.bill_id
								LEFT JOIN ret_taging tag on tag.tag_id = d.tag_id
								LEFT JOIN ret_section sec on sec.id_section = tag.id_section
									WHERE
										b.bill_id IS NOT NULL AND b.bill_status = 1 and tag.id_section IS NOT NULL
										".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
										".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
										".($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") )."
										GROUP by tag.id_section");

		return $sql->result_array();

	}

	function get_karigar_sales($from_date, $to_date, $id_branch)
	{
		$id_profile = $this->session->userdata('profile_id');
		$profile_settings = $this->get_profile_settings($id_profile);

		$sql = $this->db->query("SELECT IFNULL(kar.firstname,'') as karigar_name,SUM(IFNULL(d.item_cost,0.00)) as karigar_sales
		from ret_billing b
		Left JOIN ret_bill_details d on d.bill_id = b.bill_id
		LEFT JOIN ret_taging tag on tag.tag_id = d.tag_id
		LEFT JOIN ret_lot_inwards lot on lot.lot_no=tag.tag_lot_id
		LEFT JOIN ret_karigar kar on kar.id_karigar=lot.gold_smith
		where tag.tag_id is NOT NULL and b.bill_id IS NOT NULL AND b.bill_status = 1 and  kar.id_karigar IS NOT NULL
		".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
		".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
		".($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") )."
		GROUP by lot.gold_smith

				");

		return $sql->result_array();

	}

	function get_karigar_stock($from_date, $to_date, $id_branch)
	{
		$sql = $this->db->query("SELECT k.firstname as karigar_name,
		IFNULL(SUM(tag.piece),0) as tot_pcs,IFNULL(SUM(tag.gross_wt),0) as tot_gwt
		FROM ret_taging tag
		LEFT JOIN ret_lot_inwards i ON i.lot_no=tag.tag_lot_id
		LEFT JOIN ret_karigar k ON k.id_karigar = i.gold_smith
		WHERE tag.tag_status = 1 and
		k.id_karigar is NOT NULL
		".($id_branch!='' && $id_branch !='0' ? " and tag.current_branch = ".$id_branch."" :'' )."
		GROUP by i.gold_smith

				");

		return $sql->result_array();

	}


	function get_section_stock($from_date, $to_date, $id_branch)
	{
		$sql = $this->db->query("SELECT sec.section_name,SUM(IFNULL(tag.gross_wt,0.00)) as section_stock
								FROM ret_taging tag
								LEFT JOIN ret_section sec on sec.id_section = tag.id_section
								WHERE
									 tag.id_section IS NOT NULL
									".($id_branch!='' && $id_branch !='0' ? " and tag.current_branch = ".$id_branch."" :'' )."
								GROUP by tag.id_section ");

		return $sql->result_array();

	}
	function get_product_stock($from_date, $to_date, $id_branch)
	{
		$sql = $this->db->query("SELECT
				pro.product_name as product_name,
				SUM(IFNULL(tag.gross_wt, 0.00)) as product_sales
			FROM
			    ret_taging tag
			LEFT JOIN
				ret_product_master pro ON pro.pro_id = tag.product_id
			WHERE
			 pro.pro_id IS NOT NULL
			 ".($id_branch!='' && $id_branch !='0' ? " and tag.current_branch = ".$id_branch."" :'' )."
			GROUP BY
				pro.pro_id
				");

		return $sql->result_array();

	}

	function get_custome_wise_sale($from_date, $to_date,$id_branch)
    {
        $id_profile = $this->session->userdata('profile_id');
        $profile_settings = $this->get_profile_settings($id_profile);
        $eda_filter = ($profile_settings['allow_bill_type']==3 ? " and (b.is_eda=1 OR b.is_eda=2)" : ($profile_settings['allow_bill_type']==1 ? " and b.is_eda=1" :" and b.is_eda=2") );

        $chitsql=$this->db->query("SELECT  COUNT(DISTINCT b.bill_cus_id) as sales_bill_count
        from ret_billing b
        Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        RIGHT JOIN scheme_account sc on sc.id_customer =b.bill_cus_id
        WHERE d.bill_id is not null and b.bill_status=1
        ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
         ".$eda_filter."
         ");

         //	print_r($this->db->last_query());exit;

        $newCust = $this->db->query("SELECT  COUNT(DISTINCT b.bill_cus_id) as sales_bill_count
        from ret_billing b
        Left JOIN ret_bill_details d on d.bill_id = b.bill_id
        LEFT JOIN customer as cus ON cus.id_customer = b.bill_cus_id
        WHERE d.bill_id is not null and b.bill_status=1 and date(cus.date_add) =date(b.bill_date) ".($id_branch!='' && $id_branch !='0' ? " and b.id_branch = ".$id_branch."" :'' )."
         ".($from_date != '' && $to_date !='' ? ' and date(b.bill_date) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"' : '')."
         ".$eda_filter."
         ");

        $old_cus=$this->db->query("SELECT COUNT(DISTINCT b.bill_cus_id) as sales_bill_count

                                    FROM ret_billing b

                                    LEFT JOIN scheme_account sc on sc.id_customer =b.bill_cus_id

                                    WHERE sc.id_customer is null and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."' ".($id_branch!='' && $id_branch>0 ? " and b.id_branch=".$id_branch."" :'')."  AND b.bill_cus_id in (SELECT b.bill_cus_id

                                    FROM ret_billing b

                                    LEFT JOIN scheme_account sc on sc.id_customer =b.bill_cus_id

                                    WHERE sc.id_customer is null and  date(b.bill_date)<'$from_date')");


        $responce_data = array(
           ['OLD CUSTOMER',(int) $old_cus->row_array()['sales_bill_count']],['NEW CUSTOMER',(int) $newCust->row_array()['sales_bill_count']],['CHIT CUSTOMER',(int) $chitsql->row_array()['sales_bill_count']]
            );


        return $responce_data;

    }



	function get_pendingorderdetails($from_date,$to_date,$id_branch)
	{
		$data = $this->db->query(
		"SELECT (SELECT count(id_orderdetails) AS pending_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE co.orderstatus = 0 and co.ortertype=2
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'').") as pending_orders,

		(SELECT count(*) from
		(SELECT count(id_orderdetails) AS tot_stock_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE co.orderstatus = 0 and co.ortertype=1
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'')." GROUP BY id_orderdetails) as stock_orders) as stock_orders
");

// print_r($this->db->last_query());exit;


		return $data->row_array();
	}


	function get_wiporderDetails($from_date,$to_date,$id_branch)
	{
		$data = $this->db->query(
		"SELECT (SELECT count(id_orderdetails) AS pending_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE co.orderstatus = 3 and co.ortertype=2
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'').") as wipcusorders,

		(SELECT count(*) from
		(SELECT count(id_orderdetails) AS tot_stock_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE co.orderstatus = 3 and co.ortertype=1
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'')." GROUP BY id_orderdetails) as wipstockorders) as wipstockorders
		");

		return $data->row_array();
	}





	function get_dreadyorderDetails($from_date,$to_date,$id_branch)
	{
		$data = $this->db->query(
		"SELECT (SELECT count(id_orderdetails) AS pending_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE co.orderstatus = 4 and co.ortertype=2
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'').") as drcusorders,

		(SELECT count(*) from
		(SELECT count(id_orderdetails) AS tot_stock_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE co.orderstatus = 4 and co.ortertype=1
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'')." GROUP BY id_orderdetails) as drstockorders) as drstockorders
		");

		return $data->row_array();
	}

	function get_deliveredorderDetails($from_date,$to_date,$id_branch)
	{
		$data = $this->db->query(
			"SELECT (SELECT count(DISTINCT(b.bill_id)) AS pending_orders
			FROM ret_billing as b
			left join ret_bill_details d on d.bill_id = b.bill_id
			left join customerorderdetails co on co.id_orderdetails = d.id_orderdetails
			left join customerorder c on c.id_customerorder = co.id_customerorder
			WHERE b.bill_type = 9 and b.bill_status=1 and co.ortertype=2 and date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'
			".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'').") as deliveredcusorders,

			(SELECT count(*) from
			(SELECT count(b.bill_id) AS tot_stock_orders
			FROM ret_billing as b
			left join ret_bill_details d on d.bill_id = b.bill_id
			left join customerorderdetails co on co.id_orderdetails = d.id_orderdetails
			left join customerorder c on c.id_customerorder = co.id_customerorder
			WHERE b.bill_type = 9 and b.bill_status=1 and co.ortertype=1 AND date(b.bill_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."'
			".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'')." GROUP BY d.id_orderdetails) as deliveredstockorders) as deliveredstockorders
			");
			// echo "<pre>";print_r($this->db->last_query());exit;



			return $data->row_array();
	}


	function get_karigarreminderDetails($from_date,$to_date,$id_branch)
	{


		$data = $this->db->query(
		"SELECT (SELECT count(id_orderdetails) AS pending_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE  co.orderstatus = 3 and  co.ortertype=2 and date(co.smith_due_date) = '".date('Y-m-d')." '
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'').") as karigarremindcusorders,

		(SELECT count(*) from
		(SELECT count(id_orderdetails) AS tot_stock_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE  co.orderstatus = 3 and co.ortertype=1 AND date(co.smith_due_date) = '".date('Y-m-d')." '
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'')." GROUP BY id_orderdetails) as karigarremindstockorders) as karigarremindstockorders
		");

		// print_r($this->db->last_query());exit;

		return $data->row_array();
	}


	function get_karigaroverdueDetails($from_date,$to_date,$id_branch)
	{

		$data = $this->db->query(
		"SELECT (SELECT count(id_orderdetails) AS pending_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE  co.id_orderdetails is not null and (co.orderstatus = 3) and co.ortertype=2 and
		date(co.smith_due_date) < '".date('Y-m-d')." '
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'').") as karoverduecusorders,

		(SELECT count(*) from
		(SELECT count(id_orderdetails) AS tot_stock_orders
		FROM customerorderdetails as co
		LEFT JOIN customerorder c ON c.id_customerorder = co.id_customerorder
		WHERE  co.id_orderdetails is not null and co.orderstatus = 3 and co.ortertype=1 AND date(co.smith_due_date) < '".date('Y-m-d')." '
		".($id_branch!='' && $id_branch>0 ? " and c.order_from=".$id_branch."" :'')." GROUP BY id_orderdetails) as kar_overdue_stockorders) as kar_overdue_stockorders
		");

		return $data->row_array();
	}

	/**
	 * Get all ledgers with minimum balance for alert checking
	 * Returns ledger ID, ledger name, and minimum balance
	 */
	function getLedgerBalanceAlertData()
	{
		$sql = "SELECT id_ledger, ledger_name, min_balance, opening_balance, opening_date 
		        FROM ledger_master 
		        WHERE status = 1 AND min_balance > 0
		        ORDER BY ledger_name ASC";
		
		return $this->db->query($sql)->result_array();
	}



	function get_profile_settings($id_profile)
	{
		$sql = $this->db->query("SELECT * FROM `profile` WHERE id_profile = " . $id_profile);
		return $sql->row_array();
	}

}

?>