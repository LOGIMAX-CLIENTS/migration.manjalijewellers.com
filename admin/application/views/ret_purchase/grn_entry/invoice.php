<?php

function moneyFormatIndia($num)

{

	$nums = explode(".",$num);

	if(count($nums)>2){

	return "0";

	}else{

	if(count($nums)==1){

	$nums[1]="00";

	}

	$num = $nums[0];

	$explrestunits = "" ;

	if(strlen($num)>3){

	$lastthree = substr($num, strlen($num)-3, strlen($num));

	$restunits = substr($num, 0, strlen($num)-3); 

	$restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 

	$expunit = str_split($restunits, 2);

	for($i=0; $i<sizeof($expunit); $i++)

	{

	if($i==0)

	{

	$explrestunits .= (int)$expunit[$i].","; 

	}

	else

	{

	$explrestunits .= $expunit[$i].",";

	}

	}

	$thecash = $explrestunits.$lastthree;

	} else {

	$thecash = $num;

	}

	return $thecash.".".$nums[1]; 

	}

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<style type="text/css">

body{

	font-size: 12px;

}

table{

	width: 100%;

}

.rateborders{

	border-top: none;

	border-bottom: none;

	height: 20px;

	text-align: right;

	font-size: 14px;

}

.desc{

	text-align: right;

	font-size: 14px;

	height: 25px;

	border-bottom: none;

	border-top: none;

}

.descheading{

	text-align: center;

	font-weight: bold;

	height: 30px;

	font-size: 12px;

	border-top: none;

}

.nos_label{

	padding-right: 5px;

	font-weight: bold;

}

.nos_values{

	padding-left : 5px;

}

.iconDetails {

	 margin-left:2%;

	float:left; 

	width:40px;	

	height:40px;	

} 

.container2 {

	width:350px;

	height:auto;

	padding:1%;

    float:left;

}

.branchadd{

	padding-left: 8px !important;

    padding: 0px;

    margin-top: -91px;

    margin-left: 216px;

    border-left: 1px solid;

    margin-bottom: 24px;

}



        .addr_brch_labels {

			display: inline-block;

			width: 40%;

		}



		.addr_brch_values {

			display: inline-block;

			padding-left: 2px;

		}

		

		 .addr_labels {

            display: inline-block;

            width: 30%;

        }

		

        .addr_values {

            display: inline-block;

            padding-left: -5px;

        }

		

h4 {margin:0}

.left {float:left;width:45px;}

.right {float:left;margin:0 0 0 5px;width:400px;}

</style>



<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>GOODS RECEIVED NOTE</title>

</head>

<body>

<p style="text-align: center">

<div style="text-align: center">

    <span style="padding-left: 10px; font-weight: bold"><?php echo 'GOODS RECEIVED NOTE'?></span>

</div>

</p>



<?php



if (version_compare(phpversion(), '7.1', '>=')) {

    ini_set( 'precision', 14 );

    ini_set( 'serialize_precision', -1 );

}

function formatnumber($num){



	return floatval(number_format($num, 2, '.', ''));



}?>

<table width="921" height="766" border="1" cellpadding="3" cellspacing="0">

	<tr>

		<td height="77" colspan="2">

    		<div style="text-align: right !important; display: inline-block; vertical-align: top;">

				<div style="text-align: left !important;width: 100%; display: inline-block;"> 

						<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.$grn_details['karigar_name']."</div>"; ?></label><br>

    					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$grn_details['mobile']."</div>"; ?></label><br>

    					<label><?php echo ($grn_details['supplier_address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($grn_details['supplier_address1']).','."</div><br>" :''); ?></label>

    					<label><?php echo ($grn_details['supplier_address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($grn_details['supplier_address2']).','."</div><br>" :''); ?></label>

    					<label><?php echo ($grn_details['supplier_city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($grn_details['supplier_city_name']).($grn_details['supplier_pincode']!='' ? ' - '.$grn_details['supplier_pincode'].'.' :'')."</div><br>" :''); ?></label>

    					<label><?php echo ($grn_details['supplier_state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($grn_details['supplier_state_name']).','."</div><br>" :''); ?></label>

    					<label><?php echo (isset($grn_details['gst_number']) && $grn_details['gst_number']!='' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($grn_details['gst_number'])."</div><br>" :''); ?></label>

    					<label><?php echo (isset($grn_details['pan_no']) && $grn_details['gst_number']!='' ? '<div class="addr_labels">PAN </div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($grn_details['pan_no'])."</div><br>" :''); ?></label>

				</div>

			</div>

		</td>

		<td width="36%" >

			<div style="text-align: right !important; display: inline-block; vertical-align: top;">

				<div style="text-align: left !important;width: 100%; display: inline-block;"> 

						<label><?php echo '<div class="addr_brch_labels">GRN No</div><div class="addr_brch_values">:&nbsp;&nbsp;'.$grn_details['grn_ref_no']."</div><br>"; ?></label>

						<label><?php echo '<div class="addr_brch_labels">GRN Date</div><div class="addr_brch_values">:&nbsp;&nbsp;'.$grn_details['grn_date']."</div><br>"; ?></label>

						<label><?php echo '<div class="addr_brch_labels">Supplier Ref No</div><div class="addr_brch_values">:&nbsp;&nbsp;'.$grn_details['grn_supplier_ref_no']."</div><br>"; ?></label>

						<label><?php echo '<div class="addr_brch_labels">Supplier Ref Date</div><div class="addr_brch_values">:&nbsp;&nbsp;'.$grn_details['grn_ref_date']."</div>"; ?></label>

				</div>

			</div>

		</td>

	</tr>

	<tr>

		<td width="50%" valign="top" style="border-right: none;">

    	

    		<div style="text-align: right !important; display: inline-block; vertical-align: top;">

				<div style="text-align: left !important;width: 100%; display: inline-block;"> 

				        <label><b>Billed To</b></label><br>

						<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.$comp_details['company_name']."</div>"; ?></label><br>

    					<label><?php echo ($comp_details['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['address1']).','."</div><br>" :''); ?></label>

    					<label><?php echo ($comp_details['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($comp_details['address2']).','."</div><br>" :''); ?></label>

    					<label><?php echo ($comp_details['city']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['city']).($comp_details['pincode']!='' ? ' - '.$comp_details['pincode'].'.' :'')."</div><br>" :''); ?></label>

    					<label><?php echo ($comp_details['state']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['state']).','."</div><br>" :''); ?></label>

    					<label><?php echo ($comp_details['gst_number']!='' ? '<div class="addr_labels">GST</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['gst_number']).','."</div><br>" :''); ?></label>

				</div>

			</div><br>

			

			<div style="text-align: right !important; display: inline-block; vertical-align: top;">

				<div style="text-align: left !important;width: 100%; display: inline-block;"> 

				        <label><b>Shipped To</b></label><br>

						<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.$comp_details['company_name']."</div>"; ?></label><br>

    					<label><?php echo ($comp_details['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['address1']).','."</div><br>" :''); ?></label>

    					<label><?php echo ($comp_details['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($comp_details['address2']).','."</div><br>" :''); ?></label>

    					<label><?php echo ($comp_details['city']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['city']).($comp_details['pincode']!='' ? ' - '.$comp_details['pincode'].'.' :'')."</div><br>" :''); ?></label>

    					<label><?php echo ($comp_details['state']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['state']).','."</div><br>" :''); ?></label>

    					<label><?php echo ($comp_details['gst_number']!='' ? '<div class="addr_labels">GST</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['gst_number']).','."</div><br>" :''); ?></label>

				</div>

				
			</div>

			

		</td>

		<td width="14%" style="font-size:16px;border-left: none;height: 15px">

		  <!--<div style="text-align:center">

			<img src="<?php echo base_url(); ?>qrcode/<?php echo $inv['cusdel_irn']; ?>.jpg" style="height: 125px;width: 125px;" />	

			</div>-->

		</td>

		<td valign="top">
			<div style="text-align: center !important; width: 100%; font-size: 16px;"> 
				<label><b>Terms of Delivery</b></label><br>
			</div><br>
			<div style="text-align: left !important; width: 100%; font-size: 16px; display: inline-block; margin-top: 5px;"> 
				<label><?php echo '<div class="addr_brch_labels" style="font-size: 13px;">Delivery Type</div><div class="addr_brch_values" style="font-size: 13px;">:&nbsp;&nbsp;'.($grn_details['grn_despatch_through'] == 1 ? 'Courier' : 'Manual')."</div>"; ?></label>
			</div>
		</td>


	</tr>

	

	

	<tr>

		<td colspan="3" style="padding: 0px; border: none;">

		

				<table id="commodity_display" border="1" cellpadding="2" cellspacing="0">

					<tr>

						<td  class="descheading" style="border-left: none;">S.NO</td>

						<td  class="descheading">DESCRIPTION OF GOODS</td>

						<td  class="descheading">HSN</td>

						<td  class="descheading">QUANTITY<br /></td >

						<td  class="descheading">RATE </td>

						<td  class="descheading">AMOUNT(RS)<br/></td>

					</tr>

					<?php

					$i = 1;

					$total_taxable_amt = 0;

					$grn_item_cgst = 0;

					$grn_item_sgst = 0;

					$grn_item_igst = 0;

					$grn_item_total_cost = 0;

					$grn_net_wt = 0;

					$grn_wastage = 0;

					$total_charges = 0;

					foreach($grn_details['gst_details'] as $gst)

					{

					    $grn_item_cgst+=$gst['grn_item_cgst'];

			            $grn_item_sgst+=$gst['grn_item_sgst'];

			            $grn_item_igst+=$gst['grn_item_igst'];

					}

					foreach($grn_details['charge_details'] as $charge)

					{

					    $total_charges+=$charge['grn_charge_value'];

					}

					foreach($grn_details['item_details'] as $row) 

					{

					    $item_weight = 0;

					    $stone_cost = 0;

					    $stone_weight = 0;

			            $total_taxable_amt+=($row['itemratecaltype']==1 ? ($row['grn_net_wt'] * $row['grn_rate_per_grm']) : ($row['grn_no_of_pcs'] * $row['grn_rate_per_grm']));

			            $grn_item_total_cost+=$row['grn_item_cost'];

			            $grn_net_wt+=$row['grn_net_wt'];

			            $grn_wastage+=$row['grn_wastage'];

			            foreach($row['stone_details'] as $stn_details)

			            {

			                $stone_weight+=$stn_details['wt'];

			                $stone_cost+=$stn_details['amount'];

			            }

						$item_weight = $row['grn_amount'] - $stone_cost;

					?>

						<tr>

							<td class="desc" style="text-align: center;border-left: none;"><?php echo $i; ?></td>

							<td class="desc" style="text-align: left">&nbsp;<?php echo $row['category_name'] ?> </td>

							<td class="desc" style="text-align: left"><?php echo  $row['hsn_code'] ?></td>

							<td class="desc"><?php echo  number_format($row['grn_net_wt'],3,'.','') ?></td>

							<td class="desc"><?php echo  $row['grn_rate_per_grm'].($row['itemratecaltype']==1 ? ' / grm': ' / pcs') ?></td>

							<td  class="desc"><?php echo moneyFormatIndia(number_format($item_weight,2,'.','')) ?> </td>

						</tr>

						<?php if($row['grn_wastage'] > 0 ){ $i++; ?>

						    <tr>

							<td class="desc" style="text-align: center;border-left: none;"><?php echo $i; ?></td>

							<td class="desc" style="text-align: center;border-left: none;"></td>

							<td class="desc" style="text-align: left">&nbsp; Wastage </td>

							<td class="desc"><?php echo  number_format($row['grn_wastage'],3,'.','') ?></td>

							<td class="desc"><?php echo  $row['grn_rate_per_grm'] ?></td>

							<td  class="desc"><?php echo moneyFormatIndia(number_format($row['grn_wastage'] * $row['grn_rate_per_grm'],2,'.','')) ?> </td>

						</tr>

						<?php } ?>

					<?php $i++;

    					foreach($row['stone_details'] as $stn_details)

    					{?>

    					    <tr>

    							<td class="desc" style="text-align: center;border-left: none;"><?php echo $i; ?></td>

    							<td class="desc" style="text-align: center;border-left: none;"></td>

    							<td class="desc" style="text-align: left">&nbsp;<?php echo $stn_details['stone_name'] ?> </td>

    							<td class="desc"><?php echo  $stn_details['wt'] ?></td>

    							<td class="desc"><?php echo  $stn_details['rate_per_gram'] ?></td>

    							<td  class="desc"><?php echo moneyFormatIndia(number_format($stn_details['amount'],2,'.','')) ?> </td>

    						</tr>

    					<?php $i++; }

					}

						

					?>

					

					<?php 

					foreach($row['othermetal_details'] as $om)

					{?>

					    <tr>

    						<td class="desc" style="text-align: center;border-left: none;"><?php echo $i; ?></td>

    						<td class="desc" style="text-align: center;border-left: none;"></td>

							<td class="desc" style="text-align: left">&nbsp;<?php echo $om['omcatname'] ?></td>

							<td class="desc"><?php echo  $om['grn_other_itm_grs_weight'] ?></td>

							<td class="desc"><?php echo  $om['grn_other_itm_rate'] ?></td>

							<td  class="desc"><?php echo moneyFormatIndia(number_format($om['grn_other_itm_amount'],2,'.','')) ?> </td>

    					</tr>

					<?php $i++; }

					?>

					

					<?php 

					    if($grn_details['grn_type'] == 3) {

        					foreach($grn_details['charge_details'] as $charge)

        					{?>

        					    <tr>

            						<td class="rateborders" style="border-left: none"></td>

            						<td class="rateborders" style="border-left: none"></td>

        							<td class="rateborders">&nbsp;<?php echo $charge['name_charge'] ?></td>

        							<td class="rateborders"></td>

        							<td class="rateborders"></td>

        							<td  class="rateborders"><?php echo moneyFormatIndia(number_format($charge['grn_charge_value'],2,'.','')) ?> </td>

            					</tr>

        					<?php $i++; }

					    }

					?>

					



					<tr style="font-weight:bold;">

						<td class="rateborders" style="border-left: none"></td>

						<td class="rateborders"></td>

						<td class="rateborders"></td>

						<td class="rateborders"></td>

						<td class="rateborders"></td>

						<?php 

						if($grn_details['grn_type'] == 1 || $grn_details['grn_type'] == 2)

						{?>

						    <td class="rateborders" style="border: 1px solid;"><?php echo moneyFormatIndia(number_format($total_taxable_amt+$stone_cost,2,'.','')) ?></td>

						<?php }else{?>

						    <td class="rateborders" style="border: 1px solid;"><?php echo moneyFormatIndia(number_format($total_taxable_amt+$total_charges+$stone_cost,2,'.','')) ?></td>

						<?php }?>

					</tr>

					

					<?php 

					foreach($grn_details['gst_details'] as $gst)

					{?>

					    <?php 

					    if($gst['cgst_cost']>0)

					    {?>

					        <tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders">CGST(<?php echo ($gst['grn_item_gst_value']/2) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['cgst_cost'],2,".","")) ?></td>

        					</tr>

        					

        					<tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">SGST(<?php echo ($gst['grn_item_gst_value']/2) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['sgst_cost'],2,".","")) ?></td>

        					</tr>

        					

					    <?php }else if($gst['igst_cost']>0){ ?>

					        <tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">IGST(<?php echo ($gst['grn_item_gst_value']) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['igst_cost'],2,".","")) ?></td>

        					</tr>

					    <?php }?>

				<?php }?>

					    <?php 

					    if($grn_details['grn_tcs_percent']>0)

					    {?>

					        <tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">TCS(<?php echo ($grn_details['grn_tcs_percent']) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($grn_details['grn_tcs_value'],2,'.','')) ?></td>

        					</tr>

					    <?php }

					    ?>

					    

					    

					    <?php 

					    if($grn_details['grn_pay_tds_percent']>0)

					    {?>

					        <tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">TDS(<?php echo ($grn_details['grn_pay_tds_percent']) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($grn_details['grn_pay_tds_value'],2,'.','')) ?></td>

        					</tr>

					    <?php }

					    ?>

					    

					    <?php 

					    if($grn_details['grn_type'] != 3) {

        					foreach($grn_details['charge_details'] as $charge)

        					{?>

        					    <tr>

            						<td class="rateborders" style="border-left: none"></td>

            						<td class="rateborders" style="border-left: none"></td>

        							<td class="rateborders">&nbsp;<?php echo $charge['name_charge'] ?></td>

        							<td class="rateborders"></td>

        							<td class="rateborders"></td>

        							<td  class="rateborders"><?php echo moneyFormatIndia(number_format($charge['grn_charge_value'],2,'.','')) ?> </td>

            					</tr>

        					<?php $i++; }

					    }

					?>

					

					    <?php 

					foreach($grn_details['charge_gst_details'] as $gst)

					{?>

					    <?php 

					    if($gst['cgst_cost']>0)

					    {?>

					        <tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">CGST(<?php echo ($gst['grn_item_gst_value']/2) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['cgst_cost'],2,".","")) ?></td>

        					</tr>

        					

        					<tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">SGST(<?php echo ($gst['grn_item_gst_value']/2) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['sgst_cost'],2,".","")) ?></td>

        					</tr>

        					

					    <?php }else if($gst['igst_cost']>0){ ?>

					        <tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">IGST(<?php echo ($gst['grn_item_gst_value']) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['igst_cost'],2,".","")) ?></td>

        					</tr>

					    <?php }?>

				<?php }?>

					    

					    <?php 

					    if($grn_details['grn_other_charges_tds_percent']>0)

					    {?>

					        <tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">CHARGES TDS(<?php echo ($grn_details['grn_other_charges_tds_percent']) ?> %)</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($grn_details['grn_other_charges_tds_value'],2,'.','')) ?></td>

        					</tr>

					    <?php }

					    ?>

					    

					    <?php 

					    if($grn_details['grn_round_off']!=0)

					    {?>

					        <tr>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders" style="border-left: none"></td>

        						<td class="rateborders">ROUND OFF</td>

        						<td class="rateborders"></td>

        						<td class="rateborders"></td>

        						<td class="rateborders"><?php echo moneyFormatIndia(number_format($grn_details['grn_round_off'],2,'.','')) ?></td>

        					</tr>

					    <?php }

					    ?>

				

				

				

					<?php $height = "";

						if($i == 2)

							$height = "165px";

						else if($i == 3)

							$height = "140px";

						else if($i == 4)

							$height = "105px";

						else if($i == 5)

							$height = "80px";

						else if($i == 6)

							$height = "55px";

						else if($i == 7)

							$height = "30px";

					?>

					<?php if($i < 8) { ?>

					<tr style="height: <?php echo $height ?>">			

						<td class="rateborders" style="border-left: none"></td>

						<td class="rateborders"></td>

						<td class="rateborders"></td>

						<td class="rateborders"></td>

						<td class="rateborders"></td>

						<td class="rateborders"></td>

					</tr>

					<?php } ?>

					<tr>			

						<td class="rateborders" style="border-top: 1px solid #ccc; border-left: none"></td>

						<td class="rateborders" style="border-top: 1px solid #ccc; border-left: none"></td>

						<td class="rateborders" style="border-top: 1px solid #ccc;"><b>FINAL TOTAL</b></td>

						<td class="rateborders" style="border-top: 1px solid #ccc;"><b><?php echo moneyFormatIndia(number_format($grn_net_wt+$grn_wastage,3,'.','')) ?></b></td>

						<td class="rateborders" style="border-top: 1px solid #ccc;"></td>

						<td class="rateborders" style="border-top: 1px solid #ccc;"><b><?php echo moneyFormatIndia(number_format($grn_details['grn_purchase_amt'],2,'.','')) ?></b></td>

					</tr>

				</table>

			

		</td>

	</tr>

	

	



</table>

<?php 

if($grn_details['grn_type']==1 || $grn_details['grn_type']==3)

{?>

    <div style="text-align: center; margin-top: 10px;"> This is computer generated invoice </div>

    <div style="margin-top: 2px">DECLARATION</div>

    <div>

    "AS PER THE RULE 138 Of CGST ACT/ SGST ACT, PRECIOUS METALS / PRECIOUS STONES / JEWELLERY ARE EXEMPT FROM GENERATING E-WAY BILL AS NOTIFIED BY NOTIFICATION NO.3 OF 2018 DATED 23/01/2018"

    </div>

<?php } else {?>

    <br><br><br><br>

    <div class="row" style="text-transform:uppercase;">

		<label style="margin-left:20%;">Authorised Signatory</label>

		<label style="margin-left:20%;">Checked By</label>

		<label style="margin-left:30%;">Verified By</label>

	</div>

<?php }?>





</body>

</html>

<script type="text/javascript">

window.print();

</script>