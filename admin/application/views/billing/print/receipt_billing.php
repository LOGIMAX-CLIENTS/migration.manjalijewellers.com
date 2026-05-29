<html>

<head>
	<meta charset="utf-8">
	<title>Billing Receipt</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/billing_receipt.css">
	<style>
		@page {

			margin-top: 40mm;
			margin-left: 0mm;
			margin-right: 0mm;
			margin-bottom: 30mm;
			font-size: 14px !important;

			size: 207mm 297mm;

			/* margin-bottom: 33mm; */

			border: 1px solid red;

		}

		.head {
			color: black;
			font-size: 35px;
		}

		.alignCenter {
			text-align: center;
		}

		.alignRight {
			text-align: right;
		}

		.table_heading {
			font-weight: bold;
		}

		.textOverflowHidden {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.duplicate_copy * {
			font-size: 9px;
		}

		.duplicate_copy #pp th,
		.duplicate_copy #pp td {
			font-size: 10px !important;
		}

		.return_dashed {
			width: 2400%;
		}

		.old_metal_dashed {
			width: 2550%;
		}

		.stones,
		.charges {
			font-style: italic;
		}

		.stones .stoneData,
		.charges .chargeData {
			font-size: 14px !important;
		}

		.addr_labels {
			display: inline-block;
			width: 30%;
		}

		.addr_values {
			display: inline-block;
			padding-left: -5px;
		}

		.rate_labels {
			display: inline-block;
			width: 30%;
		}

		.addr_brch_labels {
			display: inline-block;
			width: 30%;
		}

		.addr_brch_values {
			display: inline-block;
			padding-left: 2px;
		}
		.payment_print,
		.amt_word {
			<?php if (sizeof($est_other_item['item_details']) == 0 && ($type == 'p' || $type == 'sr' || $type == 'od')) { ?>display: block;
			<?php } else if ($type == '') {; ?>display: block;
			<?php } else {; ?>display: none;
			<?php }; ?>
		}
		.pur_word {
			<?php if (sizeof($est_other_item['item_details']) > 0 && ($type == 'p' || $type == 'sr' || $type == 'od')) { ?>display: block;
			<?php } else if ($type == '') {; ?>display: none;
			<?php } else {; ?>display: none;
			<?php }; ?>
		}

		/* Sales return */
		<?php if ($type == 'sr') { ?>.sales_div {
			display: none !important;
		}

		.sales_return_div {
			display: block;
		}

		<?php } else { ?>.sales_return_div {
			display: none;
		}

		<?php } ?>
		/* order advance */

		<?php if ($type == 'od') { ?>.ord_adv {
			display: block;
		}

		<?php } else { ?>.ord_adv {
			display: none;
		}

		<?php } ?>
		/* purchase  */

		<?php if ($type == 'p') { ?>.purchase_div {
			display: block;
		}

		.sales_div {
			display: none;
		}

		<?php } else { ?>.purchase_div {
			display: none;
		}

		.sales_div {
			display: block;
		}

		<?php } ?>.bill_title {
			padding-top: 10px;
			display: inline-block;
			font-weight: bold;
			text-align: center !important;
			width: 100%;
			text-transform: uppercase;
		}
	</style>
</head>

<body>
	<div>
		<span class="PDFReceipt">
			<!--<?php echo $billing['adv_adj_amt'] ?>-->
			<?php
			if ($billing['bill_type'] == 5) {
				$ord_adv = 1;
			}
			if (sizeof($est_other_item['return_details']) > 0) //SALES RETURN
			{
				$sal_ret = 1;
			} ?>
			<?php
			$login_emp = $billing['emp_name'];
			$esti_sales_emp = '';
			$esti_purchase_emp = '';
			$esti_return_emp = '';
			$esti_sales_id = '';
			$esti_purchase_id = '';
			$esti_return_id = '';
			function moneyFormatIndia($num)
			{
				return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
			}
			$gold_metal_rate = ($billing['goldrate_22ct'] > 0 ? $billing['goldrate_22ct'] : $metal_rate['goldrate_22ct']);
			$silver_metal_rate = ($billing['silverrate_1gm'] > 0 ? $billing['silverrate_1gm'] : $metal_rate['silverrate_1gm']);
			$metal_type = 0;
			$tot_sales_amt = 0;
			$sales_cost = 0;
			foreach ($est_other_item['item_details'] as $items) {
				$sales_cost += $items['item_cost'];
			}
			$tot_sales_amt  =  $sales_cost;
			$total_return = 0;
			foreach ($est_other_item['return_details'] as $items) {
				$total_return  += $items['item_cost'];
			}
			$pur_total_amt = 0;
			$metal_name = '';
			foreach ($est_other_item['old_matel_details'] as $items) {
				$pur_total_amt += $items['amount'];
				$metal_name = $items['metal_name'];
			}
			?>
			<div>
				<div class="header_top">
				</div><br>

				<div class="flex" style="display: flex;">
					<div style="width: 40%; text-transform:uppercase; font-weight:bold;">
						<?php if ($billing['billing_for'] == 1 || $billing['billing_for'] == 2) { ?>
							<!-- <div style="display: inline-block; width: 50%; padding-left:0px;">
							<?php echo ($billing['bill_type'] == 11 ? 'BILL TO' . '<br><br>' : '') ?>
							<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;' . 'Mr./Ms.' . $billing['customer_name'] . "</div>"; ?></label><br>
							<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;' . $billing['mobile'] . "</div>"; ?></label><br>
							<label><?php echo ($billing['address1'] != '' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['address1']) . ',' . "</div><br>" : ''); ?></label>
							<label><?php echo ($billing['address2'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($billing['address2']) . ',' . "</div><br>" : ''); ?></label>
							<label><?php echo ($billing['village_name'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($billing['village_name']) . ',' . "</div><br>" : ''); ?></label>
							<label><?php echo ($billing['city'] != '' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['city']) . ($billing['pincode'] != '' ? ' - ' . $billing['pincode'] . '.' : '') . "</div><br>" : ''); ?></label>
							<label><?php echo ($billing['cus_state'] != '' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['cus_state'] . '-' . $billing['state_code']) . ',' . "</div><br>" : ''); ?></label>
							<label><?php echo ($billing['cus_country'] != '' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['cus_country']) . "</div><br>" : ''); ?></label>
							<label><?php echo (isset($billing['pan_no']) && $billing['pan_no'] != '' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['pan_no']) . "</div><br>" : ''); ?></label>
							<label><?php echo (isset($billing['gst_number']) && $billing['gst_number'] != '' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['gst_number']) . "</div><br>" : ''); ?></label>
						</div> -->
							<div id="headertable" style="display: inline-block;vertical-align:top;padding-left:0px;">
								<?php echo ($billing['bill_type'] == 11 ? 'BILL TO' . '<br><br>' : '') ?>
								<table style="width:100%;line-height:-6px !important;font-weight:bold;">
									<?php if ($billing['customer_name'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%"><?php echo ($billing['customer_name'] != '' ? 'NAME' : ''); ?> </td>
											<td>:</td>
											<td class="line_height" style="width:85%"><?php echo  $billing['customer_name']; ?></td>
										</tr>
									<?php } ?>
									<?php if ($billing['mobile'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%"><?php echo ($billing['mobile'] != '' ? 'MOBILE' : ''); ?> </td>
											<td>:</td>
											<td class="line_height" style="width:85%"><?php echo $billing['mobile']; ?></td>
										</tr>
									<?php } ?>
									<?php if ($billing['address1'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%;vertical-align:top"><?php echo ($billing['address1'] != '' ? 'ADDRESS' : ''); ?> </td>
											<td>:</td>
											<td class="line_height" style="width:85%"><?php echo ($billing['address1'] != '' ? strtoupper($billing['address1']) . ',' : ''); ?></td>
										</tr>
									<?php } ?>
									<?php $billing['address2'] != '' ?>
									<tr>
										<td class="line_height" style="width:15%"></td>
										<td></td>
										<td class="line_height" style="width:85%"><?php echo ($billing['address2'] != '' ? strtoupper($billing['address2']) . ',' : ''); ?></td>
									</tr>
									<?php ?>
									<?php $billing['address3'] != '' ?>
									<tr>
										<td class="line_height" style="width:15%"></td>
										<td></td>
										<td class="line_height" style="width:85%"><?php echo ($billing['address3'] != '' ? strtoupper($billing['address3']) . ',' : ''); ?></td>
									</tr>
									<?php ?>
									<?php if ($billing['village_name'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%"> </td>
											<td></td>
											<td class="line_height" style="width:85%"><?php echo ($billing['village_name'] != '' ? strtoupper($billing['village_name']) . ',' : ''); ?></td>
										</tr>
									<?php } ?>
									<?php if ($billing['city'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%"><?php echo ($billing['city'] != '' ? 'CITY' : ''); ?> </td>
											<td>:</td>
											<td class="line_height" style="width:85%"><?php echo ($billing['city'] != '' ? strtoupper($billing['city']) : ''); ?></td>
										</tr>
									<?php } ?>
									<?php if ($billing['cus_state'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%"><?php echo ($billing['cus_state'] != '' ? 'STATE' : ''); ?> </td>
											<td>:</td>
											<td class="line_height" style="width:85%"><?php echo ($billing['cus_state'] != '' ? strtoupper($billing['cus_state']) : ''); ?></td>
										</tr>
									<?php } ?>
									<?php if ($billing['cus_country'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%">COUNTRY </td>
											<td>:</td>
											<td class="line_height" style="width:85%"><?php echo ($billing['cus_country'] != '' ? strtoupper($billing['cus_country']) . ',' : ''); ?></td>
										</tr>
									<?php } ?>
									<?php if ($billing['pan_no'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%">PAN </td>
											<td>:</td>
											<td class="line_height" style="width:85%"><?php echo ($billing['pan_no'] != '' ? strtoupper($billing['pan_no']) . ',' : ''); ?></td>
										</tr>
									<?php } ?>
									<?php if ($billing['gst_number'] != '') { ?>
										<tr>
											<td class="line_height" style="width:15%">GST NO </td>
											<td>:</td>
											<td class="line_height" style="width:85%"><?php echo ($billing['gst_number'] != '' ? strtoupper($billing['gst_number']) . ',' : ''); ?></td>
										</tr>
									<?php } ?>
									<?php if ($billing['driving_license_no'] != '' && $billing['driving_license_no'] != 'UNDEFINED' && $billing['driving_license_no'] != 'undefined') { ?>

									<tr>
										<td class="line_height" style="width:15%">DL NO </td>
										<td>:</td>
										<td class="line_height" style="width:85%"><?php echo ($billing['driving_license_no'] != '' ? strtoupper($billing['driving_license_no']) . ',' : ''); ?></td>
									</tr>
								<?php } ?>
								<?php if ($billing['passport_no'] != '' && $billing['passport_no'] != 'UNDEFINED' && $billing['passport_no'] != 'undefined') { ?>

									<tr>
										<td class="line_height" style="width:15%">PP NO</td>
										<td>:</td>
										<td class="line_height" style="width:85%"><?php echo ($billing['passport_no'] != '' ? strtoupper($billing['passport_no']) . ',' : ''); ?></td>
									</tr>
								<?php } ?>
								</table>
							</div>

						<?php } else if ($billing['billing_for'] == 3 && $billing['bill_type'] != 13 && $billing['bill_type'] != 14) { ?>
							<div style="display: inline-block; width: 50%;font-weight: bold">
								<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;' . 'Mr./Ms.' . $billing['karigar_name'] . "</div>"; ?></label><br>
								<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;' . $billing['mobile'] . "</div>"; ?></label><br>
								<label><?php echo ($billing['karigar_address1'] != '' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['karigar_address1']) . ',' . "</div><br>" : ''); ?></label>
								<label><?php echo ($billing['karigar_address2'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($billing['karigar_address2']) . ',' . "</div><br>" : ''); ?></label>
								<label><?php echo (isset($billing['pan_no']) && $billing['pan_no'] != '' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['pan_no']) . "</div><br>" : ''); ?></label>
								<label><?php echo (isset($billing['karigar_gst_number']) && $billing['karigar_gst_number'] != '' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($billing['karigar_gst_number']) . "</div><br>" : ''); ?></label>
								<br>
								<label><?php echo '<div class="addr_labels">place of supply</div><div class="addr_values">:&nbsp;&nbsp;' . $comp_details['state'] . '-' . $comp_details['state_code'] . "</div>"; ?></label><br>
								<label><?php echo '<div class="addr_labels">reverse charge</div><div class="addr_values">:&nbsp;&nbsp;No</div>'; ?></label><br>
							</div>
						<?php } else { ?>
							<div style="display: inline-block; width: 50%; font-weight: bold">
								<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;' . $billing['transfer_details']['name'] . "</div>"; ?></label><br>
								<label><?php echo '<div class="addr_labels">Address1</div><div class="addr_values">:&nbsp;&nbsp;' . $billing['transfer_details']['address1'] . "</div>"; ?></label><br>
								<label><?php echo '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;' . $billing['transfer_details']['address2'] . "</div>"; ?></label><br>
								<label><?php echo '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;' . $billing['transfer_details']['city'] . '' . $billing['transfer_details']['pincode'] . "</div>"; ?></label><br>
								<label><?php echo '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;' . $billing['transfer_details']['state'] . "</div>"; ?></label><br>
								<label><?php echo '<div class="addr_labels">GST</div><div class="addr_values">:&nbsp;&nbsp;' . $billing['transfer_details']['gst_number'] . "</div>"; ?></label><br>
							</div>
						<?php } ?>

					</div>
					<div style="width: 15%;">
						<div>
							<div style="text-align:center; font-size:9px;">E-Bill</div>
							<div align="center"><img src="<?php echo base_url(); ?>bill_qrcode/<?php echo $qrfilename; ?>.png" style="margin-top:0px" width="130" height="130" src="F:\xampp\htdocs\etail_v5\admin\assets\img\billing\02090.jpg"></div>
						</div>
					</div>
					<div align="right" style="width:45%;font-weight:bold">
						<?php
						if ($type == '') //SALES BILL 
						{
							$invoice_no = $billing['branch_code']  . $billing['fin_year_code'] . '-' . $billing['sales_ref_no'];

							if ($billing['bill_type'] == 8)   //CREDIT COLLECTION
							{
								$invoice_no =  $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['credit_coll_refno'];
							} else if ($billing['bill_type'] == 10)   //CHIT PRE CLOSE
							{
								$invoice_no =  $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['chit_preclose_refno'];
							}
						} else if ($type == 'p') // OLD METAL ITEMS
						{
							$invoice_no =  $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['pur_ref_no'];
						} else if ($type == 'sr') //SALES RETURN
						{
							$invoice_no =  $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['s_ret_refno'];
						} else if ($type == 'od') //ORDER ADVANCE
						{
							$invoice_no =  $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['order_adv_ref_no'];
						} else {
							$invoice_no =  $billing['bill_no'];
						}

						// if($type=='sr'){

						// 	$bill_format_invoice_number = 'sr ';
						// echo $billing['bill_type'].'-'.$billing['bill_id'].'-'.$billing['invoice_no'];

						// }else if($type=='p'){

						// 	$bill_format_invoice_number =  'p ';


						// }else{
						// 	$bill_format_invoice_number = $billing['invoice_no'];
						// }
						?>

						<label><?php echo '<div class="addr_brch_labels" style="height: 1px;">INVOICE DATE</div><div class="addr_brch_values" style="height: 18px;font-weight: bold;">:&nbsp;&nbsp;' . $billing['bill_date'] . "</div><br>"; ?></label>
						<label><?php echo '<div class="addr_brch_labels" style="height: 18px;">INVOICE NO</div><div class="addr_brch_values" style="height: 18px;">:&nbsp;&nbsp;' . $invoice_no . "</div><br>"; ?>
						</label>



					</div>
				</div>





				<!-- <div style="padding-top:10px;text-align:center;width: 100%; text-transform:uppercase;"> -->

				<div class="bill_title">
					<label style="white-space:nowrap;">
						<?php $purchase_old_metal_type = 0; ?>
						<?php foreach ($est_other_item['old_matel_details'] as $purchase_old_metal_type) {
							$purchase_old_metal_type = $purchase_old_metal_type['metal_type'];
						} ?>
						<!--<?php echo $billing['bill_type'] != 15 ? "  " . ($billing['bill_type'] == 1 ? 'Sales Bill' : ($billing['bill_type'] == 2   ? ($purchase_old_metal_type == 1 ? 'Sales & Old Gold  Purchase ' : ($purchase_old_metal_type == 2 ? 'Sales & Old Silver Purchase' : 'Sales & Purchase')) : ($billing['bill_type'] == 14 ? "Sales Return Transfer" : ($billing['bill_type'] == 3 ?   'Sales & Credit Sales ' : ($billing['bill_type'] == 4 ? ($purchase_old_metal_type == 1 ? 'Old Gold Purchase Bill' : $purchase_old_metal_type == 2 ? 'Old Silver Purchase Bill' : 'Purchase Bill') : ($billing['bill_type'] == 5 ? 'Order Advance' : ($billing['bill_type'] == 6 ? 'Advance' : ($billing['bill_type'] == 7 ? 'Credit Note' : ($billing['bill_type'] == 9 ? 'Order Delivery' : ($billing['bill_type'] == 10 ? 'Chit PreClose' : ($billing['bill_type'] == 11 ? 'Repair Order' : ($billing['bill_type'] == 13 ? 'Sales Transfer' : ($billing['bill_type'] == 15 ? 'Approval Bill Acknowledgement' : 'Credit Collection'))))))))))))) . "" : ($billing['bill_status'] == 2 ? '- Cancelled' : '') ?>
						-->
						<?php

						$title = "Tax-Invoice";
						if ($billing['bill_type'] != '') {
							
							if($billing['bill_status'] == 1){
								
									if ($billing['bill_type'] == 1) {
								$title = ($billing['is_to_be'] == 1 ? 'To Be Sales Bill' : ($billing['is_credit'] == 1 ? 'Credit Sales Bill' : 'Sales Bill'));
							}  else if ($billing['bill_type'] == 2) {
								if ($type == '') {
									$title = 'Sales';
								} else if ($type == 'p') {
									$title = ($metal_name != '' ? 'Old ' . $metal_name . ' Purchase Bill' : 'Old Metal Purchase Bill');
								} else {
									$title = ($metal_name != '' ? 'Old ' . $metal_name . ' Purchase Bill' : 'Old Metal Purchase Bill');
								}
							} else if ($billing['bill_type'] == 3) {

								if ($type == '') {
									$title = 'Sales';
								} else if ($type == 'p') {
									$title = ($metal_name != '' ? 'Old ' . $metal_name . ' Purchase Bill' : 'Old Metal Purchase Bill');
								} else if ($type = 'sr') {
									$title = 'Credit Note';
								} else {
									$title = 'Credit Note';
								}
							} else if ($billing['bill_type'] == 4) {
								$title = ($metal_name != '' ? 'Old ' . $metal_name . ' Purchase Bill' : 'Purchase Bill');
							} else if ($billing['bill_type'] == 5) {
								$title = 'Order Advance';
							} else if ($billing['bill_type'] == 6) {
								$title = 'Advance';
							} else if ($billing['bill_type'] == 7) {
								$title = 'Credit Note';
							} else if ($billing['bill_type'] == 9) {
								$title = 'Order Delivery';
							} else if ($billing['bill_type'] == 10) {
								$title = 'Chit PreClose';
							} else if ($billing['bill_type'] == 11) {
								$title = 'Repair Order';
							} else if ($billing['bill_type'] == 13) {
								$title = 'Sales Transfer';
							} else if ($billing['bill_type'] == 14) {
								$title = "Sales Return Transfer";
							} else {
								$title = 'Credit Collection';
							}
								
							}else if ($billing['bill_status'] == 2) {
								$title = 'Cancelled';
							}
						
						} else { ?>
							<div><?php echo $title = 'Tax -Invoice' ?> </div>;
						<?php }
						echo $title;
						?>

					</label>
					<!-- DONE -->
				</div>
				<!-- </div> -->
				<div class="content-wrapper" style="margin-top:20px;">
					<div class="box">
						<div class="box-body">
							<div class="container-fluid">
								<div id="printable">
									<div class="sales_div">





















										<?php if (sizeof($est_other_item['item_details']) > 0) { ?>
											<?php
											if ($est_other_item['item_details'][0]['order_no'] != '') { ?>
												<div align="center">
													<label><b>Order No.<?php echo $est_other_item['item_details'][0]['order_no']; ?></b></label>
												</div>
												<p></p>
											<?php }
											?>
											<hr class="header_dashed">
											<div class="col-xs-12">
												<div class="table-responsive">
													<table id="pp" class="table text-center">
														<thead style="text-transform:uppercase;font-size:19px;">
															<tr>
																<td class="table_heading" style="width: 5%; ">S.No</td>
																<td class="table_heading" style="width: 9%;text-align:left;">HSN</td>
																<td class="table_heading" style="width: 20%;">Description</td>
																<!-- <td class="table_heading" style="width: 9%">PURITY</td> -->
																<td class="table_heading alignRight" style="width: 6%;">Qty</td>
																<td class="table_heading alignRight" style="width: 14% !important;">Gwt</td>
																<td class="table_heading alignRight" style="width: 12%; ">Calc.Wt</td>
																<td class="table_heading alignRight" style="width: 11%;">Rate</td>
																<td class="table_heading alignRight" style="width:8%;">V.A</td>
																<td class="table_heading alignRight" style="width: 12%;">Amount</td>
															</tr>
														</thead>
														<tr>
															<td>
																<hr class="item_dashed" style="width:2260% !important;">
															</td>
														</tr>
														<!--<tbody>-->
														<?php
														$i = 1;
														$pieces = 0;
														$gross_wt = 0;
														$net_wt = 0;
														$discount = 0;
														$taxable_amt = 0;
														$tot_tax = 0;
														$sales_cost = 0;
														$tot_tax_per = 0;
														$total_cgst = 0;
														$total_sgst = 0;
														$total_igst = 0;
														$bill_discount = 0;
														$mc = 0;
														$wastge_wt = 0;
														$total_stone_amount = 0;
														$total_mc = 0;
														$tot_wastage_wt = 0;
														$savings_in_making_charge = 0;
														$savings_in_wastage = 0;
														$closing_weight = 0;
														$closing_amount = 0;
														$scheme_benefit = 0;
														foreach ($est_other_item['chit_details'] as $chit) {
															if ($chit['closing_weight'] > 0) {
																$savings_in_making_charge += $chit['savings_in_making_charge'];
																$savings_in_wastage += ($chit['savings_in_wastage'] * $billing['goldrate_22ct']);
																$closing_weight += number_format($chit['closing_weight'], 3, '.', '');
																$closing_amount += ($chit['closing_amount']);
																$scheme_benefit = number_format((($closing_weight * $billing['goldrate_22ct']) - $closing_amount + $savings_in_making_charge + $savings_in_wastage), 2, '.', '');
															}
														}
														foreach ($est_other_item['item_details'] as $items) {
															$esti_sales_emp = $items['esti_emp_name'];
															$esti_sales_id = $items['esti_emp_id'];
															$mc = 0;
															$wastge_wt = 0;
															$stone_amount = 0;
															if (sizeof($items['stone_details']) > 0) {
																foreach ($items['stone_details'] as $stoneItems) {
																	$stone_amount += $stoneItems['amount'];
																}
															}
															$total_stone_amount += $stone_amount;

															$metal_type = $items['metal_type'];
															$pieces         += $items['piece'];
															$gross_wt       += $items['gross_wt'];
															$net_wt         += $items['net_wt'];
															$discount       += $items['discount'];
															$tot_tax        += $items['item_total_tax'];
															$sales_cost     += $items['item_cost'];
															$total_cgst     += $items['total_cgst'];
															$total_sgst     += $items['total_sgst'];
															$total_igst     += $items['total_igst'];
															$bill_discount  += ($items['bill_discount'] - $items['wastage_discount'] - $items['mc_discount'] - $items['item_blc_discount']);
															$item_discount  = ($items['bill_discount'] - $items['wastage_discount'] - $items['mc_discount'] - $items['item_blc_discount']);
															$item_blc_discount += $items['item_blc_discount'];
															$taxable_amt    += $items['item_cost'] - $items['item_total_tax'];
															$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_bill_amount']);
															$item_taxable   = number_format((float)$items['item_cost'] - $items['item_total_tax'], 2, '.', '');
															$tax_percentage = number_format(($items['item_total_tax'] * 100) / $item_taxable, '2', '.', '');
															$tot_tax_per    += $tax_percentage;
															if ($items['calculation_based_on'] == 0) {
																$wastge_wt = ($items['gross_wt'] * ($items['wastage_percent'] / 100));
																$mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * 1));
															} else if ($items['calculation_based_on'] == 1) {
																$wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
																$mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['net_wt']) : ($items['mc_value'] * 1));
															} else if ($items['calculation_based_on'] == 2) {
																$wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
																$mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * 1));
															}
															if ($items['wastage_discount'] > 0) {
																$discount_weight = number_format(($items['wastage_discount'] / $items['rate_per_grm']), 3, '.', '');
																$item_wastge_wt = number_format(($wastge_wt - $discount_weight), 3, '.', '');
															} else {
																$item_wastge_wt = $wastge_wt;
															}
															if ($items['mc_discount'] > 0) {
																$mc        = $mc - $items['mc_discount'];
															}
															$total_mc        += $mc;
															$tot_wastage_wt += $item_wastge_wt;
															if ($item_wastge_wt > 0 && $items['rate_per_grm'] > 0) {
																$wastge_amt = ($item_wastge_wt * $items['rate_per_grm']);
															}
															if ($item_wastge_wt > 0) {
																if ($items['calculation_based_on'] == 0) {
																	$wastage_percent = (($item_wastge_wt * 100) / $items['gross_wt']);
																} else if ($items['calculation_based_on'] == 1 || $items['calculation_based_on'] == 2) {
																	$wastage_percent = (($item_wastge_wt * 100) / $items['net_wt']);
																}
															} else {
																$wastage_percent = $items['wastage_percent'];
															}
															// $total_mc        += $mc;
															// echo 'totalmx'.$total_mc;
															// $tot_wastage_wt += $item_wastge_wt;
														?>
															<tr>
																<td><?php echo $i; ?></td>
																<td style="text-align:left"><?php echo $items['hsn_code']; ?></td>
																<td class='textOverflowHidden'><?php echo $items['product_name']; ?><?php echo $items['size'] > 0 ? "-" . $items['size_name'] : ''; ?></td>
																<!-- <td><?php echo $items['purname']; ?></td> -->
																<td class="alignRight"><?php echo $items['piece']; ?></td>
																<td class="alignRight"><?php echo $items['gross_wt']; ?></td>
																<td class="alignRight"><?php echo $items['net_wt']; ?></td>
																<td class="alignRight"><?php echo $items['rate_per_grm']; ?></td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($wastge_amt), 2, '.', '')); ?></td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($item_taxable > 0 ? ($item_taxable + $item_discount) : 0), 2, '.', '')); ?></td>
															</tr>
															<tr>
																<!--<td></td>-->
																<td><?php echo  $items['cat_code'] . '-' . number_format($items['purname'], 2) ?></td>
																<td><?php echo ($items['hu_id'] != '' ? 'HUID:' . $items['hu_id'] : '') ?></td>
																<td class="alignRight"></td>
																<td class="alignRight"></td>
																<td class="alignRight"></td>
																<td class="alignRight"></td>
																<td class="alignRight"><?php echo ($wastage_percent != '' ? '(' . number_format($wastage_percent, 2, '.', '') . '%' : '') . ')'; ?></td>
																<td class="alignRight"></td>
															</tr>
															<?php
															if ($total_mc > 0) { ?>

																<tr>
																	<td colspan="2" style="white-space: no-wrap">Designer Charge:</td>
																	<td class="alignLeft"><?php echo $total_mc ?></td>
																	<td class="alignRight"></td>
																	<td class="alignRight"></td>
																	<td class="alignRight"></td>
																	<td class="alignRight"></td>
																	<td class="alignRight"></td>
																	<td class="alignRight"></td>

																</tr>
															<?php }	 ?>
															<?php
															if (count($items['stone_details']) > 0) {
																foreach ($items['stone_details'] as $stoneItems) {	?>
																	<!-- <tr class="stones">
																		<td></td>
																		<td class='textOverflowHidden stoneData'><?php echo $stoneItems['stone_name']; ?></td>
																		<td></td>
																		<td></td>
																		<td class="alignRight stoneData"><?php echo $stoneItems['pieces']; ?></td>
																		<td class="alignRight stoneData"><?php echo $stoneItems['wt']; ?></td>
																		<td class="alignRight"></td>
																		<td class="alignRight stoneData"><?php echo moneyFormatIndia(number_format((float)($stoneItems['amount']), 2, '.', '')); ?></td>
																	</tr> -->
																	<tr class="stones">
																		<td></td>
																		<td class='textOverflowHidden stoneData'><?php echo $stoneItems['stone_name']; ?></td>
																		<td class="alignRight stoneData"><?php echo moneyFormatIndia(number_format((float)($stoneItems['wt']), 3, '.', '')) . '/' . $stoneItems['uom_short_code']; ?></td>
																		<td></td>
																		<td class="alignRight stoneData"><?php echo $stoneItems['rate_per_gram'] . ' /  ' . $stoneItems['uom_short_code']; ?></td>
																		<td class="alignRight"></td>
																		<td class="alignRight stoneData"><?php echo moneyFormatIndia('Rs : ' . $stoneItems['amount']); ?></td>
																		<td class="alignRight"></td>
																		<td></td>
																	</tr>
																<?php
																}
															}
															if (count($items['charges']) > 0) {
																foreach ($items['charges'] as $chargeItems) { ?>
																	<tr class="charges" style="display:none;">
																		<td></td>
																		<td></td>
																		<td></td>
																		<td class="chargeData"><?php echo $chargeItems['code_charge']; ?></td>
																		<td></td>
																		<td></td>
																		<td></td>
																		<td></td>
																	</tr>
															<?php }
															}
															?>
															<tr>
																<td>
																	<hr class="item_dashed" style="width:2260% !important;">
																</td>
															</tr>
														<?php $i++;
														} ?>
														<!--</tbody> -->
														<tr class="total" style="font-weight: bold">
															<td> </td>
															<td></td>
															<td class="alignRight">Total</td>
															<td class="alignRight"><?php echo $pieces; ?></td>
															<td class="alignRight"><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
															<td class="alignRight"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
															<td class="alignRight"></td>
															<td class="alignRight"></td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($taxable_amt), 2, '.', '')); ?></td>
														</tr>
														<tr>
															<td style="margin-left:150px">
																<hr class="item_dashed" style="width:2260% !important;">
															</td>
														</tr>
														<?php if ($taxable_amt > 0) { ?>
															<tr>
																<td colspan="6"></td>
																<td class="alignLeft" style="width:15%;font-weight:bold;">GST DETAILS</td>
																<td></td>
																<td></td>
															</tr>
															<tr>
																<td colspan="6"></td>
																<td colspan="7">
																	<hr class=" item_dashed">
																</td>
															</tr>
															<!-- <tr>
																<td colspan="6"></td>
																<td class="alignLeft">SUB TOTAL</td>
																<td class="alignLeft">Rs.</td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($taxable_amt), 2, '.', '')); ?></td>
															</tr> -->
														<?php } ?>
														<?php if ($total_sgst > 0) { ?>
															<tr>
																<td colspan="6"></td>
																<td class="alignLeft">SGST<?php echo (sizeof($est_other_item['tax_details']) == 1 ? '(' . ($est_other_item['item_details'][0]['tax_percentage'] / 2) . '%)'  : '') ?></td>
																<td class="alignLeft">Rs.</td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_sgst), 2, '.', '')); ?></td>
															</tr>
														<?php } ?>
														<?php if ($total_cgst > 0) { ?>
															<tr>
																<td colspan="6"></td>
																<td class="alignLeft">CGST<?php echo (sizeof($est_other_item['tax_details']) == 1 ? '(' . ($est_other_item['item_details'][0]['tax_percentage'] / 2) . '%)'  : '') ?></td>
																<td class="alignLeft">Rs.</td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_cgst), 2, '.', '')); ?></td>
															</tr>
														<?php } ?>
														<?php if ($total_igst > 0) { ?>
															<tr>
																<td colspan="6"></td>
																<td class="alignLeft">IGST <?php echo (sizeof($est_other_item['tax_details']) == 1 ? '(' . ($est_other_item['item_details'][0]['tax_percentage']) . '%)'  : '') ?></td>
																<td class="alignLeft">Rs.</td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_igst), 2, '.', '')); ?></td>
															</tr>
														<?php } ?>
														<?php if ($scheme_benefit > 0) { ?>
															<tr>
																<td colspan="6"></td>
																<td class="alignLeft">CHIT BENEFIT </td>
																<td class="alignLeft">RS</td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($scheme_benefit), 2, '.', '')); ?></td>
															</tr>
														<?php } ?>
														<?php if ($billing['handling_charges'] > 0) { ?>
															<tr>
																<td colspan="6"></td>
																<td class="alignLeft">H.C</td>
																<td class="alignLeft">Rs.</td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format($billing['handling_charges'], 2, '.', '')); ?></td>
															</tr>
														<?php } ?>
														<?php
														if ((sizeof($est_other_item['return_details']) == 0) && ((sizeof($est_other_item['old_matel_details']) == 0)) && ($billing['round_off_amt'] != 0)) {
														?>
															<?php if ($type == 'p') { ?>

																<tr>
																	<td colspan="6"></td>
																	<td class="alignLeft">Round Off </td>
																	<td class="alignLeft">Rs.</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format($billing['round_off_amt'], 2, '.', '')); ?></td>
																</tr>
																<tr>
																	<td colspan="6"></td>
																	<td colspan="7">
																		<hr class="item_dashed">
																	</td>
																</tr>

																<tr>
																	<td colspan="6"></td>
																	<td class="alignLeft">TOTAL </td>
																	<td class="alignLeft">Rs.</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format(round($tot_sales_amt + $billing['handling_charges'] + $billing['round_off_amt'] - $scheme_benefit), 2, '.', ''));
																							$amount_in_words = (number_format(round($tot_sales_amt + $billing['handling_charges'] + $billing['round_off_amt'] - $scheme_benefit), 2, '.', '')); ?></td>
																</tr>
																<tr>
																	<td colspan="6"></td>
																	<td colspan="7">
																		<hr class=" item_dashed">
																	</td>
																</tr>
															<?php } ?>
														<?php  } else { ?>
															<?php if ($type == 'p') { ?>

																<tr>
																	<td colspan="6"></td>
																	<td colspan="7">
																		<hr class=" item_dashed">
																	</td>
																</tr>
																<tr style="font-weight: bold">
																	<td colspan="6"></td>
																	<td class="alignLeft">TOTAL </td>
																	<td class="alignLeft">Rs.</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format(($tot_sales_amt + $billing['handling_charges'] - $scheme_benefit), 2, '.', ''));
																							$amount_in_words = (number_format(round($tot_sales_amt + $billing['handling_charges'] + $billing['round_off_amt'] - $scheme_benefit), 2, '.', '')); ?></td>
																</tr>
																<tr>
																	<td colspan="6"></td>
																	<td colspan="7">
																		<hr class=" item_dashed">
																	</td>
																</tr>
															<?php } ?>
														<?php } ?>
														<?php if ($billing['tcs_tax_amt'] > 0) { ?>
															<tr>
																<td colspan="6"></td>
																<td class="alignLeft">TCS % </td>
																<td class="alignRight"><?php echo $billing['tcs_tax_per']; ?></td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format($billing['tcs_tax_amt'], 2, '.', '')); ?></td>
															</tr>
															<tr>
																<td colspan="6"></td>
																<td class="alignLeft">Net Amt </td>
																<td class="alignLeft">Rs.</td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format($tot_sales_amt + $billing['handling_charges'] + $billing['tcs_tax_amt'] - $scheme_benefit, 2, '.', '')); ?></td>
															</tr>
														<?php  } ?>
														<?php if (sizeof($est_other_item['order_adj']) > 0) { ?>
															<table id="pp" class="table text-center">
																<?php foreach ($est_other_item['order_adj'] as $ord) {

																	$tot_adv_amount += $ord['advance_amount']; ?>
																	<tr style="display: none;">
																		<td colspan="6" style="font-weight: bold; width: 66%;">
																			<?php if ($ord['advance_type'] == 1) { ?>
																				By Advance Rec.No:
																			<?php } else if ($ord['advance_type'] == 2) { ?>
																				By Purchase Rec.No:
																			<?php } ?>
																			<?php echo $order_no ?> Dated <?php echo $ord['bill_date'] . ' Bill no : ' . $ord['order_adv_ref_no'] ?>
																		</td>
																		<td class="alignLeft">Amount</td>
																		<td class="alignLeft">Rs.</td>
																		<td class="alignRight" style="width: 12%;"><?php echo number_format(round($ord['advance_amount']), 2, '.', ''); ?></td>
																	</tr>
																<?php } ?>
																<?php if ($pur_total_amt > 0) { ?>
																	<?php foreach ($est_other_item['old_matel_details'] as $items) { ?>
																		<tr>
																			<td colspan="6" style="font-weight: bold; width: 66%;">By Puq Rec.No:<?php echo $items['pur_refId'] . ($billing['fin_year_code'] != '' ? " (" . $billing['fin_year_code'] . ")" : ''); ?></td>
																			<td class="alignLeft" style="width: 10%;">Purchase Amount</td>
																			<td class="alignLeft" style="width: 12%;">Rs.</td>
																			<td class="alignRight" style="width: 12%;"><?php echo number_format($items['amount'], 2, '.', ''); ?></td>
																		</tr>
																	<?php } ?>
																<?php } ?>


																<!-- </table><br> -->
															<?php } ?>

															<?php if ($pur_total_amt == 0 || $total_return > 0 || $pur_total_amt > 0) { ?>
																<!-- <?php if ($tot_sales_amt + $billing['handling_charges'] > 0) { ?>
														<tr>
															<td colspan="6"></td>
															<td class="alignLeft" >Sales Amount </td>
															<td class="alignLeft">Rs.</td>
															<td class="alignRight"><?php echo moneyFormatIndia((number_format($tot_sales_amt + $billing['handling_charges'], 2, '.', ''))); ?></td>
														</tr>
													<?php } ?> -->
																<!-- <?php if ($total_return > 0) { ?>
														<tr>
														<td colspan="6"></td>
														<td class="alignLeft">Credit Amount</td>
														<td class="alignLeft">Rs.</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return, 2, '.', '')); ?></td>
														</tr>
													<?php } ?> -->
																<!-- <?php if ($pur_total_amt > 0) { ?>
														<tr style="font-weight:bold;">
															<td colspan="6">
																<?php
																			$cgst_sgst_perc = ($est_other_item['item_details'][0]['tax_percentage'] / 2);
																			$cgst_sgst_amt = ($pur_total_amt * $cgst_sgst_perc / 100);
																?>
																RCM : (CGST <?php echo  $cgst_sgst_perc . "% - " . number_format($cgst_sgst_amt, 2, '.', '') ?>,  SGST <?php echo  $cgst_sgst_perc . "% - " . number_format($cgst_sgst_amt, 2, '.', '') ?>)
															</td>
															<td class="alignLeft">Purchase Amount</td>
															<td class="alignLeft">Rs.</td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format($pur_total_amt, 2, '.', ''));
																					$amount_in_words = number_format($pur_total_amt, 2, '.', ''); ?></td>
														</tr>
													<?php } ?> -->

																<?php $round_off = moneyFormatIndia(number_format($billing['round_off_amt'], 2, '.', '')); ?>
																<?php if ($round_off != 0) { ?>
																	<tr>
																		<td colspan="6" style="width: 66%;"></td>
																		<td class="alignLeft" style="width: 15%;">Round Off</td>
																		<td class="alignLeft">Rs.</td>
																		<td class="alignRight" style="width: 12%;"><?php echo $round_off ?></td>
																	</tr>
																	<tr>
																		<td colspan="6"></td>
																		<td colspan="3">
																			<hr class="item_dashed">
																		</td>
																	</tr>
																<?php } ?>
																<?php if ($tot_sales_amt > 0) { ?>
																	<tr style="font-weight:bold">
																		<td colspan="6"></td>
																		<td class="alignLeft">TOTAL </td>
																		<td class="alignLeft">Rs.</td>
																		<td class="alignRight"><?php echo moneyFormatIndia(number_format(round(($tot_sales_amt + $billing['handling_charges'])  + $round_off), 2, '.', ''));
																								$amount_in_words = (number_format(round(($tot_sales_amt + $billing['handling_charges']) - $total_return - $pur_total_amt + $round_off), 2, '.', ''));  ?></td>
																	</tr>
																	<tr>
																		<td colspan="6"></td>
																		<td colspan="3">
																			<hr class="item_dashed">
																		</td>
																	</tr>
																<?php } ?>



															<?php } ?>
															</table>
												</div>
											</div>
										<?php }
										//echo "<pre>"; print_r($stones); echo "</pre>";exit;
										?>
									</div><!-- End of Sales Div -->


































									<?php if (sizeof($est_other_item['tax_details']) > 1) { ?>

										<br><br>
										<div class="col-xs-12">
											<div class="table-responsive">
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:19px;">
														<tr>
															<td class="table_heading" style="width: 5%; ">S.No</td>
															<td class="table_heading" style="width: 5%;text-align:left;">HSN</td>
															<td class="table_heading" style="width: 10%;">Taxable Value</td>
															<td class="table_heading" style="width: 6%;text-align:center;" colspan="2">CGST </td>
															<td class="table_heading" style="width: 14% !important;text-align:center;" colspan="2">SGST </td>
															<td class="table_heading" style="width: 12%;text-align:center; " colspan="2">IGST </td>

															<td class="table_heading alignRight" style="width: 12%;">Amount</td>
														</tr>
														<tr>
															<td>
																<hr class="header_dashed" style="width:1500%;">
															</td>
														</tr>
														<tr>
															<td class="table_heading" style="width: 5%;" colspan="3"></td>
															<td class="table_heading alignRight" style="width: 6%;">Rate</td>
															<td class="table_heading alignRight" style="width: 6%;">Amount</td>
															<td class="table_heading alignRight" style="width: 6%;">Rate</td>
															<td class="table_heading alignRight" style="width: 6%;">Amount</td>
															<td class="table_heading alignRight" style="width: 6%;">Rate</td>
															<td class="table_heading alignRight" style="width: 6%;">Amount</td>
															<td class="table_heading alignRight" style="width: 12%;"></td>
														</tr>

													</thead>
													<tr>
														<td>
															<hr class="item_dashed" style="width:2260% !important;">
														</td>
													</tr>
													<!--<tbody>-->
													<?php
													$i = 1;
													$total_cgst = 0;
													$total_sgst = 0;
													$total_igst = 0;
													$total_item_taxable = 0;
													$item_total_tax = 0;
													foreach ($est_other_item['tax_details'] as  $items) {
														$item_taxable   = number_format((float)$items['item_cost'] - $items['item_total_tax'], 2, '.', '');
														$total_item_taxable += number_format((float)$items['item_cost'] - $items['item_total_tax'], 2, '.', '');
														$total_cgst += $items['total_cgst'];
														$total_sgst += $items['total_sgst'];
														$total_igst += $items['total_igst'];
														$item_total_tax += $items['item_total_tax'];
													?>
														<tr>
															<td><?php echo $i; ?></td>
															<td style="text-align:left"><?php echo $items['hsn_code']; ?></td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format($item_taxable)); ?></td>
															<td class="alignRight"><?php echo ($items['total_cgst'] > 0 ? ($items['tax_percentage'] / 2) . '%' : 0); ?></td>
															<td class="alignRight"><?php echo ($items['total_cgst'] > 0 ? ($items['total_cgst']) : 0); ?></td>
															<td class="alignRight"><?php echo ($items['total_sgst'] > 0 ? ($items['tax_percentage'] / 2) . '%' : 0); ?></td>
															<td class="alignRight"><?php echo ($items['total_sgst'] > 0 ? ($items['total_sgst']) : 0); ?></td>
															<td class="alignRight"><?php echo ($items['total_igst'] > 0 ? ($items['tax_percentage']) . '%' : 0); ?></td>
															<td class="alignRight"><?php echo ($items['total_igst'] > 0 ? ($items['total_igst']) : 0); ?></td>
															<td class="alignRight"><?php echo $items['item_total_tax']; ?></td>

														</tr>
													<?php $i++;
													}
													?>
													<tr>
														<td>
															<hr class="item_dashed" style="width:2260% !important;">
														</td>
													</tr>
													<tr>
														<td>TOTAL</td>
														<td style="text-align:left"></td>
														<td class="alignRight"><?php echo moneyFormatIndia(($total_item_taxable)); ?></td>
														<td class="alignRight"></td>
														<td class="alignRight"><?php echo moneyFormatIndia(($total_cgst)); ?></td>
														<td class="alignRight"></td>
														<td class="alignRight"><?php echo moneyFormatIndia(($total_sgst)); ?></td>
														<td class="alignRight"></td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_igst)); ?></td>
														<td class="alignRight"><?php echo moneyFormatIndia($item_total_tax); ?></td>

													</tr>
													<!--</tbody> -->


												</table>
											</div>
										</div>
								</div>
							<?php }
									//echo "<pre>"; print_r($stones); echo "</pre>";exit;
							?>
							<div class="sales_return_div">

								<?php if (sizeof($est_other_item['return_details']) > 0) { ?>
									<span style="font-weight: bold">CREDIT NOTE INVOICE NO : <?php echo  $billing['invoice_no'] ?></span>
									<table id="pp" class="table text-center">
										<!--	<thead> -->
										<tr>
											<td colspan="8">
												<hr class="item_dashed">
											</td>
										</tr>
										<?php if (sizeof($est_other_item['item_details']) == 0 && sizeof($est_other_item['old_matel_details']) == 0 || sizeof($est_other_item['return_details']) > 0) { ?>
											<tr style="text-transform:uppercase;">
												<td style="width: 6%" class="table_heading">S.No</td>
												<td style="width: 18%" class="table_heading">Description</td>
												<td style="width: 8%" class="table_heading alignRight">HSN Code</td>
												<td style="width: 8%" class="table_heading alignRight">PURITY</td>
												<td style="width: 5%" class="table_heading alignRight">PCS</td>
												<td style="width: 13%" class="table_heading alignRight">Gwt</td>
												<td style="width: 7%" class="table_heading alignRight">Calc.Wt</td>
												<td style="width: 10%" class="table_heading alignRight">Amount</td>
											</tr>
											<tr>
												<td colspan="8">
													<hr class="item_dashed">
												</td>
											</tr>
										<?php } ?>
										<!--</thead>
													<tbody>-->
										<tr style="font-weight:bold; text-transform: uppercase;">
											<td colspan="8">Exchange</td>
										</tr>
										<?php
										$i = 1;
										$pieces = 0;
										$gross_wt = 0;
										$net_wt = 0;
										$return_item_cost = 0;
										$discount = 0;
										$tax_percentage = 0;
										$total_cgst = 0;
										$total_igst = 0;
										$total_sgst = 0;
										$mc = 0;
										$wastge_wt = 0;
										foreach ($est_other_item['return_details'] as $items) {
											$esti_return_emp = $items['esti_emp_name'];
											$esti_return_id = $items['esti_emp_id'];
											$ret_stone_price = 0;
											$mc = 0;
											$wastge_wt = 0;
											$pieces             += $items['piece'];
											$gross_wt           += $items['gross_wt'];
											$net_wt             += $items['net_wt'];
											$discount           += $items['discount'];
											$total_sgst         += $items['total_sgst'];
											$total_igst         += $items['total_igst'];
											$total_cgst         += $items['total_cgst'];
											$return_item_cost   += $items['item_cost'] - $items['item_total_tax'];
											$item_cost   += $items['item_cost'] - $items['item_total_tax'];
											$tax_percentage     = $items['tax_percentage'] / 2;
											if ($items['calculation_based_on'] == 0) {
												$wastge_wt = ($items['gross_wt'] * ($items['wastage_percent'] / 100));
												$mc = ($items['mc_type'] == 1 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * $items['piece']));
											} else if ($items['calculation_based_on'] == 1) {
												$wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
												$mc = ($items['mc_type'] == 1 ? ($items['mc_value'] * $items['net_wt']) : ($items['mc_value'] * $items['piece']));
											} else if ($items['calculation_based_on'] == 2) {
												$wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
												$mc = ($items['mc_type'] == 1 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * $items['piece']));
											}
											if (sizeof($items['rtn_stone_details']) > 0) {
												foreach ($items['rtn_stone_details'] as $stoneItems) {
													$ret_stone_price += $stoneItems['price'];
												}
											}
										?>

											<tr>
												<td style="width: 6%"><?php echo $i; ?></td>
												<td style="width: 18%; font-size:10px;" class='textOverflowHidden'><?php echo $items['product_name']; ?></td>
												<td style="width: 8%" class="alignRight"><?php echo $items['hsn_code']; ?></td>
												<td style="width: 8%" class="alignRight"><?php echo $items['purname']; ?></td>
												<td style="width: 5%" class="alignRight"><?php echo $items['piece']; ?></td>
												<td style="width: 13%" class="alignRight"><?php echo $items['gross_wt']; ?></td>
												<td style="width: 7%" class="alignRight"><?php echo $items['net_wt']; ?></td>
												<!-- <td style="width: 10%" class="alignRight"><?php echo moneyFormatIndia(number_format($items['item_cost'], 2, '.', '')); ?></td> -->
												<td style="width: 10%" class="alignRight"><?php echo moneyFormatIndia(number_format($item_cost,2,'.',''));?></td>
											</tr>

											<?php
											if (sizeof($items['rtn_stone_details']) > 0) {
												foreach ($items['rtn_stone_details'] as $stoneItems) { ?>
													<tr class="stones">
														<td style="width: 6%"></td>
														<td style="width: 18%" class='textOverflowHidden stoneData'><?php echo $stoneItems['stone_name']; ?></td>
														<td style="width:8%;" class="alignRight stoneData"><?php echo moneyFormatIndia(number_format((float)($stoneItems['wt']), 3, '.', '')) . '/' . $stoneItems['uom_short_code']; ?></td>
														<td style="width:8%" class="alignRight stoneData"><?php echo $stoneItems['rate_per_gram'] . ' /  ' . $stoneItems['uom_short_code']; ?></td>
														<td style="width: 5%" class="alignRight"></td>
														<td style="width:13%" class="alignRight stoneData"><?php echo moneyFormatIndia('Rs : ' . $stoneItems['price']); ?></td>
														<td style="width: 7%" class="alignRight"></td>
														<td style="width: 10%"></td>
													</tr>
											<?php
												}
											} ?>
											<tr>
												<td colspan="8" style="text-transform:uppercase; font-weight: bold">Refer sale bill no : <?php echo $items['sales_ref_no'] . ' Dt ' . $items['ref_bill_date'] ?> </td>
											</tr>
										<?php $i++;
										} ?>
										<tr>
											<td colspan="8">
												<hr class="item_dashed">
											</td>
										</tr>
										<!--</tbody> -->
										<!--<tfoot>-->
										<!-- </tfoot> -->
										<tr style="font-weight: bold">
											<td>Total</td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<!-- <td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$total_return, '2', '.', '')); ?></td> -->
											<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$return_item_cost, '2', '.', '')); ?></td>
										</tr>
										<tr>
											<td colspan="8">
												<hr class="item_dashed">
											</td>
										</tr>
										<?php if($total_sgst > 0) { ?>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td class="alignRight">SGST <?php echo  $tax_percentage.'%';?></td>
                                                <td class="alignRight" style="font-size:15px !important;">&#8377;</td>
                                                <td class="alignRight"><?php echo moneyFormatIndia($total_sgst); ?></td>
                                            </tr>

                                            <?php } ?>
                                            <?php if($total_cgst > 0) { ?>
                                            <tr>
                                                <td colspan="5"></td>
                                                <td class="alignRight">CGST <?php echo  $tax_percentage.'%';?></td>
                                                <td class="alignRight" style="font-size:15px !important;">&#8377;</td>
                                                <td class="alignRight"><?php echo moneyFormatIndia($total_cgst); ?></td>
                                            </tr>
										<?php } ?>

										<?php if($total_return > 0){?>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td>
                                                    <hr class="item_dashed">
                                                </td>
                                            </tr>
                                            <tr style="font-weight: bold">
                                                <td colspan="4"></td>
                                                <td colspan="2" class="alignRight">TOTAL</td>
                                                <td class="alignRight" style="font-size:15px !important;">&#8377;</td>
                                                <td class="alignRight">
                                                    <?php echo moneyFormatIndia(number_format($total_return-$billing['credit_due_amt']-$round_off,2,'.',''));?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7"></td>
                                                <td>
                                                    <hr class="item_dashed">
                                                </td>
                                            </tr>
                                            <?php } ?>


										<?php 
										if ($pur_total_amt == 0 && $type != 'sr' && $type != 'p') {
											if ($tot_sales_amt + $billing['handling_charges'] > 0) { ?>
												<tr>
													<td colspan="5"></td>
													<td class="alignRight">Sales Amount</td>
													<td class="alignRight">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format($tot_sales_amt + $billing['handling_charges'] - $scheme_benefit, 2, '.', '')); ?></td>
												</tr>
											<?php } ?>
											<?php if ($billing['credit_due_amt'] != 0) { ?>
												<tr>
													<td colspan="5"></td>
													<td class="alignRight">Return Amount</td>
													<td class="alignRight">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return, 2, '.', '')); ?></td>
												</tr>
												<tr>
													<td colspan="5"></td>
													<td class="alignRight">Credit Due Amt</td>
													<td class="alignRight">Rs.</td>
													<td class="alignRight"><?php echo $billing['credit_due_amt'] ?></td>
												</tr>
											<?php } ?>
											<?php if ($total_return > 0) { ?>
												<tr>
													<td colspan="5"></td>
													<td class="alignRight">Credit Amount</td>
													<td class="alignRight">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return - $billing['credit_due_amt'], 2, '.', '')); ?></td>
												</tr>
											<?php } ?>
											<?php $round_off = moneyFormatIndia(number_format($billing['round_off_amt'], 2, '.', '')); ?>
											<?php if ($round_off != 0) { ?>
												<tr>
													<td colspan="5"></td>
													<td class="alignRight">Round Off</td>
													<td class="alignRight">Rs.</td>
													<td class="alignRight"><?php echo $round_off ?></td>
												</tr>
											<?php } ?>
											<?php if ($tot_sales_amt > 0 && $total_return > 0) { ?>
												<tr>
													<td colspan="5"></td>
													<td colspan="7">
														<hr class="item_dashed">
													</td>
												</tr>
												<tr>
													<td colspan="5"></td>
													<td class="alignRight">Net Amount</td>
													<td class="alignRight">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format(round(($tot_sales_amt + $billing['handling_charges']) - $total_return + $round_off), 2, '.', ''));
																			$amount_in_words = number_format(round(($tot_sales_amt + $billing['handling_charges']) - $total_return + $round_off), 2, '.', ''); ?></td>
												</tr>
												<tr>
													<td colspan="5"></td>
													<td colspan="7">
														<hr class="item_dashed">
													</td>
												</tr>
										<?php }
										} ?>
									</table>
								<?php } ?>

							</div><!-- End of Sales return Div -->


							<?php if (sizeof($est_other_item['sales_ret_trans_details']) > 0) { ?>
								<hr class="header_dashed">
								<div class="col-xs-12">
									<div class="table-responsive">
										<table id="pp" class="table text-center">
											<thead style="text-transform:uppercase;font-size:10px;">
												<tr>
													<td width="5%;">S.No</td>
													<td width="10%;">HSN</td>
													<td width="15%;">Description</td>
													<td width="10%;">PCS</td>
													<td width="10%;">Gwt</td>
													<td width="10%;">Rate</td>
													<td width="5%;" style="text-align:right;">Amount(Rs)</td>
												</tr>
											</thead>
											<tr>
												<td>
													<hr class="item_dashed" style="width:1400% !important;">
												</td>
											</tr>
											<!--<tbody>-->
											<?php
											$i = 1;
											$pieces = 0;
											$gross_wt = 0;
											$net_wt = 0;
											$discount = 0;
											$taxable_amt = 0;
											$tot_tax = 0;
											$sales_cost = 0;
											$tot_sales_amt = 0;
											$tot_tax_per = 0;
											$total_cgst = 0;
											$total_sgst = 0;
											$total_igst = 0;
											$bill_discount = 0;
											foreach ($est_other_item['sales_ret_trans_details'] as $items) {
												$mc = 0;
												$pieces         += $items['piece'];
												$gross_wt       += $items['gross_wt'];
												$net_wt         += $items['net_wt'];
												$tot_tax        += $items['item_total_tax'];
												$sales_cost     += $items['item_cost'];
												$total_cgst     += $items['total_cgst'];
												$total_sgst     += $items['total_sgst'];
												$total_igst     += $items['total_igst'];
												$taxable_amt    += $items['item_cost'] - $items['item_total_tax'];
												$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_bill_amount']);
												$tot_sales_amt  = number_format($sales_cost, 2, '.', '');
												$item_taxable   = number_format((float)$items['item_cost'] - $items['item_total_tax'], 2, '.', '');
												$tax_percentage = number_format(($items['item_total_tax'] * 100) / $item_taxable, '2', '.', '');
												$tot_tax_per    += $tax_percentage;
											?>
												<tr>
													<td><?php echo $i; ?></td>
													<td><?php echo $items['hsn_code']; ?></td>
													<td style="font-size:10px !important;"><?php echo $items['category_name']; ?></td>
													<td><?php echo $items['piece']; ?></td>
													<td><?php echo $items['gross_wt']; ?></td>
													<td><?php echo $items['rate_per_grm']; ?></td>
													<td style="text-align:right;"><?php echo number_format($item_taxable, 2, '.', ''); ?></td>
												</tr>
											<?php $i++;
											} ?>
											<!--</tbody> -->
											<tr>
												<td>
													<hr class="item_dashed" style="width:1400% !important;">
												</td>
											</tr>
											<tr class="total">
												<td></td>
												<td></td>
												<td>Total</td>
												<td><?php echo $pieces; ?></td>
												<td><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
												<td></td>
												<td style="text-align:right;"><?php echo number_format((float)($taxable_amt + $bill_discount), 2, '.', ''); ?></td>
											</tr>
											<tr>
												<td>
													<hr class="item_dashed" style="width:1400% !important;">
												</td>
											</tr>
											<?php if ($bill_discount > 0) { ?>
												<tr>
													<td colspan="3"></td>
													<td width="">LESS DISC</td>
													<td width="1%;"></td>
													<td style="text-align:right;"><?php echo number_format($bill_discount, 2, '.', ''); ?></td>
												</tr>
											<?php } ?>
											<?php if ($taxable_amt > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="">SUB TOTAL1</td>
													<td width="1%;">Rs.</td>
													<td style="text-align:right;"><?php echo number_format((float)($taxable_amt), 2, '.', ''); ?></td>
												</tr>
											<?php } ?>
											<?php if ($total_sgst > 0) { ?>
												<tr>
													<td colspan="4"><?php if ($billing['remark'] != '' && strlen($billing['remark']) > 1) { ?>
															REMARK &nbsp;:<?php echo $billing['remark']; ?>
														<?php } else { ?>
															REMARK &nbsp;:<?php echo '&nbsp;' . '-';
																		}
																			?>
													</td>
													<td width="4">SGST</td>
													<td width="1%;"><?php echo ($est_other_item['sales_ret_trans_details'][0]['tax_percentage'] / 2) . '%' ?></td>
													<td style="text-align:right;"><?php echo $total_sgst; ?></td>
												</tr>
											<?php } ?>
											<?php if ($total_cgst > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="">CGST</td>
													<td width="1%;"><?php echo ($est_other_item['sales_ret_trans_details'][0]['tax_percentage'] / 2) . '%' ?></td>
													<td style="text-align:right;"><?php echo $total_cgst; ?></td>
												</tr>
											<?php } ?>
											<?php if ($total_igst > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="">IGST</td>
													<td width="1%;"><?php echo ($est_other_item['sales_ret_trans_details'][0]['tax_percentage']) . '%' ?></td>
													<td style="text-align:right;"><?php echo $total_igst; ?></td>
												</tr>
											<?php } ?>
											<?php if ($billing['round_off_amt'] > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="">Round Off</td>
													<td width="1%;">Rs.</td>
													<td style="text-align:right;"><?php echo number_format($billing['round_off_amt'], 2, '.', ''); ?></td>
												</tr>
											<?php } ?>
											<tr>
												<td colspan="4"></td>
												<td>
													<hr class="item_dashed" style="width:400%">
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td colspan="4"></td>
												<td width="">Total</td>
												<td width="1%;">Rs.</td>
												<td style="text-align:right;"><?php echo number_format(($billing['tot_bill_amount'] < 0 ? ($billing['tot_bill_amount'] * -1) : $billing['tot_bill_amount']), 2, '.', ''); ?></td>
											</tr>
											<tr>
												<td colspan="4"></td>
												<td>
													<hr class="item_dashed" style="width:400%">
												</td>
												<td></td>
												<td></td>
											</tr>
										</table>
									</div>
								</div>
							</div><br>
						<?php } ?>


						<?php if ($billing['bill_type'] == 5) { ?>
							<?php if (sizeof($est_other_item['advance_details']) > 0) {
								$amount = 0;
								$order_no = '';
								$order_adv_pur = 0;
								$balance_type = 1;
								foreach ($est_other_item['advance_details'] as $item) {
									$order_no = $item['order_no'];
									$id_customerorder = $item['id_customerorder'];

									if ($item['advance_type'] == 1) {
										if ($item['store_as'] == 1) {
											$amount += $item['advance_amount'];
										} else {
											$amount += $item['advance_weight'] * $item['rate_per_gram'];
										}
									}
								}
							}
							?>
							<?php ?>
							<div class="row">
								<!-- <div align="left">
									<label><b>Order No.<?php echo $order_no; ?></b></label>
								</div> -->
								<hr class="header_dashed">
								<div class="col-xs-12">
									<div class="table-responsive">
										<table id="pp" class="table text-center" style="width:100% !important">
											<thead style="text-transform:uppercase;font-size:19px;">
												<!--	<thead> -->
												<tr>
													<td class="table_heading" style="width: 5%; ">S.No</td>
													<td class="table_heading" style="width: 9%;text-align:left;">HSN</td>
													<td class="table_heading" style="width: 20%;">Description</td>
													<!-- <td class="table_heading" style="width: 9%">PURITY</td> -->
													<td class="table_heading alignRight" style="width: 6%;">Qty</td>
													<td class="table_heading alignRight" style="width: 14% !important;">Gwt</td>
													<td class="table_heading alignRight" style="width: 12%; ">Calc.Wt</td>
													<td class="table_heading alignRight" style="width: 11%;">Rate</td>
													<td class="table_heading alignRight" style="width:8%;">V.A</td>
													<td class="table_heading alignRight" style="width: 12%;">Amount</td>
												</tr>
											</thead>
											<tr>
												<td>
													<hr class="item_dashed" style="width:2260% !important;">
												</td>
											</tr>
											<!--</thead>
													<tbody>-->
											<?php
											$i = 1;
											$od_pcs = 0;
											$weight = 0;
											$od_net_wt = 0;
											$od_total_cgst = 0;
											$od_total_sgst = 0;
											$od_total_igst = 0;
											$od_total_price = 0;
											$od_total_price_without_gst = 0;
											$od_wastage_wt = 0;
											$od_wastge_amt = 0;
											foreach ($est_other_item['order_details'] as $items) {
												$od_pcs += $items['totalitems'];
												$weight += $items['weight'];
												$od_net_wt += $items['net_wt'];
												$od_total_cgst 	+= $items['total_cgst'];
												$od_total_sgst 	+= $items['total_sgst'];
												$od_total_igst 	+= $items['total_igst'];
												$od_total_price += $items['rate'];
												$rate_without_gst = $items['rate'] - ($items['total_cgst'] + $items['total_sgst'] + $items['total_igst']);
												$od_total_price_without_gst += $rate_without_gst;
												$od_wastage_wt = ($items['net_wt'] * ($items['wast_percent'] / 100));
												$od_wastage_amt = ($od_wastage_wt * $items['rate_per_gram']);
												$stone_amount = 0;
												if (count($items['stones']) > 0) {
													foreach ($items['stones'] as $ordStones) {
														$stone_amount += $ordStones['st_price'];
													}
												}
												$balance_type = $items['balance_type'];
											?>
												<tr>
													<td><?php echo $i; ?></td>
													<td style="text-align:left"><?php echo $items['hsn_code']; ?></td>
													<td class='textOverflowHidden'><?php echo $items['product_name']; ?><?php echo $items['size'] > 0 ? "-" . $items['size_name'] : ''; ?></td>
													<!-- <td><?php echo $items['purname']; ?></td> -->
													<td class="alignRight"><?php echo $items['totalitems']; ?></td>
													<td class="alignRight"><?php echo $items['weight']; ?></td>
													<td class="alignRight"><?php echo $items['net_wt']; ?></td>
													<td class="alignRight"><?php echo $items['rate_per_gram']; ?></td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($od_wastage_amt), 2, '.', '')); ?></td>
													<td class="alignRight"><?php echo moneyFormatIndia($rate_without_gst - $stone_amount, 2, '.', ''); ?></td>
												</tr>
												<tr>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td class="alignRight"><?php echo $items['wast_percent'] != '' ? '(' . $items['wast_percent'] . '% )' : ''; ?></td>
													<td></td>

												</tr>
												<?php
												if (count($items['stones']) > 0) {
													foreach ($items['stones'] as $stoneItems) {
														if ($stoneItems['uom_short_code'] == 'CT' || $stoneItems['uom_short_code'] == 'gm') {
												?>
															<tr class="stones">
																<td></td>
																<td class='textOverflowHidden stoneData'><?php echo $stoneItems['stone_name']; ?></td>
																<td class="alignRight stoneData"><?php echo moneyFormatIndia(number_format((float)($stoneItems['wt']), 3, '.', '')) . '/' . $stoneItems['uom_short_code']; ?></td>
																<td></td>
																<td class="alignRight stoneData"><?php echo $stoneItems['rate_per_gram'] . ' /  ' . $stoneItems['uom_short_code']; ?></td>
																<td class="alignRight"></td>
																<td class="alignRight stoneData"><?php echo moneyFormatIndia('Rs : ' . $stoneItems['st_price']); ?></td>
																<td class="alignRight"></td>
																<td></td>
															</tr>
												<?php }
													}
												}
												?>
												<!-- <tr>
													<td>
														<hr class="item_dashed" style="width:1400% !important;">
													</td>
												</tr> -->
											<?php $i++;
											}
											?>
											<!--</tbody> -->
											<tr>
												<td>
													<hr class="item_dashed" style="width:2260% !important;">
												</td>
											</tr>
											<tr class="total" style="font-weight: bold">
												<td>Total</td>
												<td></td>
												<td></td>
												<td class="alignRight"><?php echo $od_pcs; ?></td>
												<td class="alignRight"><?php echo number_format($weight, 3, '.', ''); ?></td>
												<td class="alignRight"><?php echo number_format($od_net_wt, 3, '.', ''); ?></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($od_total_price_without_gst), 2, '.', '')); ?></td>
											</tr>
											<tr>
												<td style="margin-left:150px">
													<hr class="item_dashed" style="width:2260% !important;">
												</td>
											</tr>
											<?php if ($od_total_price_without_gst > 0) { ?>
												<tr>
													<td colspan="6"></td>
													<td class="alignLeft" style="width:15%;font-weight:bold;">GST DETAILS</td>
													<td></td>
													<td></td>
												</tr>
												<tr style="height:5px"></tr>
												<tr>
													<td colspan="6"></td>
													<td class="alignLeft">SUB TOTAL2</td>
													<td class="alignLeft">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($od_total_price_without_gst), 2, '.', '')); ?></td>
												</tr>
											<?php } ?>
											<?php if ($od_total_cgst > 0 || $od_total_sgst > 0) { ?>
												<tr>
													<td colspan="6"></td>
													<td class="alignLeft">CGST<?php echo $items['tax_percentage'] != '' ? '(' . ($items['tax_percentage'] / 2) . '%)' : '' ?></td>
													<td class="alignLeft">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia($od_total_cgst, 2, '.', ''); ?></td>
												</tr>
												<tr>
													<td colspan="6"></td>
													<td class="alignLeft">SGST<?php echo $items['tax_percentage'] != '' ? '(' . ($items['tax_percentage'] / 2) . '%)' : '' ?></td>
													<td class="alignLeft">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia($od_total_sgst, 2, '.', ''); ?></td>
												</tr>
											<?php } ?>
											<?php if ($od_total_igst > 0) { ?>
												<tr>
													<td colspan="6"></td>
													<td class="alignLeft">IGST<?php echo $items['tax_percentage'] != '' ? '(' . ($items['tax_percentage']) . '%)' : '' ?></td>
													<td class="alignLeft">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia($od_total_igst, 2, '.', ''); ?></td>
												</tr>
											<?php } ?>
											<tr>
												<td colspan="6"></td>
												<td colspan="7">
													<hr class="item_dashed">
												</td>
											</tr>
											<tr>
												<td colspan="6"></td>
												<td class="alignLeft">Total</td>
												<td class="alignLeft">Rs.</td>
												<td class="alignRight"><?php echo moneyFormatIndia($od_total_price, 2, '.', ''); ?></td>
											</tr>
											<tr>
												<td colspan="6"></td>
												<td colspan="7">
													<hr class=" item_dashed">
												</td>
											</tr>
											<?php
											$get_other_adv_details = $this->ret_billing_model->get_previous_order_details($id_customerorder, $billing['bill_id'], $billing['id_branch'], $billing['bill_created_time']);
											/*echo "<pre>";
												print_r($get_other_adv_details);
												echo "</pre>";
												exit;*/
											$total_other_advance = 0;
											$total_metal_paid = 0;
											$first_rate = 0;
											$i = 1;
											foreach ($get_other_adv_details as $oth_adv) {
												$amount_adv = 0;
												if ($oth_adv['advance_type'] == 1) {
													$amount_adv = $oth_adv['received_amount'];
													$adv_text 	= "By Advance Order.No: ";
													$total_other_advance = $total_other_advance + $amount_adv;
												} else if ($oth_adv['advance_type'] == 2) {
													$amount_adv = $oth_adv['advance_amount'];
													$adv_text 	= "By Purchase Order.No: ";
													$total_other_advance = $total_other_advance + $amount_adv;
												}
												$rate = 0;
												if ($oth_adv['rate_field'] == "goldrate_22ct") {
													$rate = $oth_adv['goldrate_22ct'];
												} else if ($oth_adv['rate_field'] == "goldrate_18ct") {
													$rate = $oth_adv['goldrate_18ct'];
												} else if ($oth_adv['rate_field'] == "silverrate_1gm") {
													$rate = $oth_adv['silverrate_1gm'];
												}
												if ($i == 1) {
													$first_rate = $rate;
												}
												$metal_paid = $rate > 0 ? (round($amount_adv / $rate, 3)) : 0;
												$total_metal_paid = $total_metal_paid + $metal_paid;
											?>
												<?php if ($amount_adv > 0) { ?>
													<tr>
														<td colspan="6" style="font-weight: bold;"><?php echo $adv_text . '<span style="font-size:18px !important;font-weight:bold">' . $order_no . '</span>' . " Dated : " . date("d-m-Y", strtotime($oth_adv['bill_date'])) . ($oth_adv['rate_type'] == 2 ? ' ( Rate Not Fixed ) ' : '( Rate Fixed : ' . ($rate) . ' )'); ?></td>
														<td class="alignLeft">Amount</td>
														<td class="alignLeft">Rs.</td>
														<td class="alignRight"><?php echo moneyFormatIndia($amount_adv, 2, '.', ''); ?></td>
													</tr>
												<?php } ?>

											<?php
												$i++;
											}
											$balance_amt = $od_total_price - $total_other_advance;
											$actual_weight = $first_rate > 0 ? round(($od_total_price / $first_rate), 3) : 0;
											$balance_metal = $actual_weight - $total_metal_paid;
											?>
											<?php if ($balance_type == 2) { ?>
												<tr style="font-weight: bold;">
													<td colspan="6"></td>
													<td class="alignLeft">Aprx. Bal Amt</td>
													<td class="alignLeft">Rs.</td>
													<td class="alignRight"><?php echo moneyFormatIndia(round($balance_amt), 2, '.', ''); ?></td>
												</tr>
											<?php } else if ($balance_type == 1) { ?>
												<tr style="font-weight: bold;">
													<td colspan="6"></td>
													<td class="alignLeft">Aprx. Bal Metal</td>
													<td class="alignLeft">:</td>
													<td class="alignRight"><?php echo moneyFormatIndia($balance_metal, 3, '.', '') . 'Gm'; ?></td>
												</tr>
											<?php } ?>
										</table><br>
									</div>
								</div>
							</div><br>
						<?php } ?>


						<?php if (sizeof($est_other_item['sales_trasnfer_details']) > 0) { ?>
							<hr class="header_dashed">
							<div class="col-xs-12">
								<div class="table-responsive">
									<table id="pp" class="table text-center">
										<thead style="text-transform:uppercase;font-size:10px;">
											<tr>
												<td width="5%;">S.No</td>
												<td width="10%;">HSN</td>
												<td width="15%;">Description</td>
												<td width="10%;">PCS</td>
												<td width="10%;">Gwt</td>
												<td width="10%;">Rate</td>
												<td width="5%;" style="text-align:right;">Amount(Rs)</td>
											</tr>
										</thead>
										<tr>
											<td>
												<hr class="item_dashed" style="width:1400% !important;">
											</td>
										</tr>
										<!--<tbody>-->
										<?php
										$i = 1;
										$pieces = 0;
										$gross_wt = 0;
										$net_wt = 0;
										$discount = 0;
										$taxable_amt = 0;
										$tot_tax = 0;
										$sales_cost = 0;
										$tot_sales_amt = 0;
										$tot_tax_per = 0;
										$total_cgst = 0;
										$total_sgst = 0;
										$total_igst = 0;
										$bill_discount = 0;
										foreach ($est_other_item['sales_trasnfer_details'] as $items) {
											$mc = 0;
											$pieces         += $items['piece'];
											$gross_wt       += $items['gross_wt'];
											$net_wt         += $items['net_wt'];
											$tot_tax        += $items['item_total_tax'];
											$sales_cost     += $items['item_cost'];
											$total_cgst     += $items['total_cgst'];
											$total_sgst     += $items['total_sgst'];
											$total_igst     += $items['total_igst'];
											$taxable_amt    += $items['item_cost'] - $items['item_total_tax'];
											$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_bill_amount']);
											$tot_sales_amt  = number_format($sales_cost, 2, '.', '');
											$item_taxable   = number_format((float)$items['item_cost'] - $items['item_total_tax'], 2, '.', '');
											$tax_percentage = number_format(($items['item_total_tax'] * 100) / $item_taxable, '2', '.', '');
											$tot_tax_per    += $tax_percentage;
										?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $items['hsn_code']; ?></td>
												<td style="font-size:10px !important;"><?php echo $items['category_name']; ?></td>
												<td><?php echo $items['piece']; ?></td>
												<td><?php echo $items['gross_wt']; ?></td>
												<td><?php echo $items['rate_per_grm']; ?></td>
												<td style="text-align:right;"><?php echo number_format($item_taxable, 2, '.', ''); ?></td>
											</tr>
										<?php $i++;
										} ?>
										<!--</tbody> -->
										<tr>
											<td>
												<hr class="item_dashed" style="width:1400% !important;">
											</td>
										</tr>
										<tr class="total">
											<td></td>
											<td></td>
											<td>Total</td>
											<td><?php echo $pieces; ?></td>
											<td><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
											<td></td>
											<td style="text-align:right;"><?php echo number_format((float)($taxable_amt + $bill_discount), 2, '.', ''); ?></td>
										</tr>
										<tr>
											<td>
												<hr class="item_dashed" style="width:1400% !important;">
											</td>
										</tr>
										<?php if ($bill_discount > 0) { ?>
											<tr>
												<td colspan="3"></td>
												<td width="">LESS DISC</td>
												<td width="1%;"></td>
												<td style="text-align:right;"><?php echo number_format($bill_discount, 2, '.', ''); ?></td>
											</tr>
										<?php } ?>
										<?php if ($taxable_amt > 0) { ?>
											<tr>
												<td colspan="4"></td>
												<td width="">SUB TOTAL4</td>
												<td width="1%;">Rs.</td>
												<td style="text-align:right;"><?php echo number_format((float)($taxable_amt), 2, '.', ''); ?></td>
											</tr>
										<?php } ?>
										<?php if ($total_sgst > 0) { ?>
											<tr>
												<td colspan="4"><?php if ($billing['remark'] != '' && strlen($billing['remark']) > 1) { ?>
														REMARK &nbsp;:<?php echo $billing['remark']; ?>
													<?php } else { ?>
														REMARK &nbsp;:<?php echo '&nbsp;' . '-';
																	}
																		?>
												</td>
												<td width="4">SGST</td>
												<td width="1%;"><?php echo ($est_other_item['sales_trasnfer_details'][0]['tax_percentage'] / 2) . '%' ?></td>
												<td style="text-align:right;"><?php echo $total_sgst; ?></td>
											</tr>
										<?php } ?>
										<?php if ($total_cgst > 0) { ?>
											<tr>
												<td colspan="4"></td>
												<td width="">CGST</td>
												<td width="1%;"><?php echo ($est_other_item['sales_trasnfer_details'][0]['tax_percentage'] / 2) . '%' ?></td>
												<td style="text-align:right;"><?php echo $total_cgst; ?></td>
											</tr>
										<?php } ?>
										<?php if ($total_igst > 0) { ?>
											<tr>
												<td colspan="4"></td>
												<td width="">IGST</td>
												<td width="1%;"><?php echo ($est_other_item['sales_trasnfer_details'][0]['tax_percentage']) . '%' ?></td>
												<td style="text-align:right;"><?php echo $total_igst; ?></td>
											</tr>
										<?php } ?>
										<?php if ($billing['round_off_amt'] > 0) { ?>
											<tr>
												<td colspan="4"></td>
												<td width="">Round Off</td>
												<td width="1%;">Rs.</td>
												<td style="text-align:right;"><?php echo number_format($billing['round_off_amt'], 2, '.', ''); ?></td>
											</tr>
										<?php } ?>
										<tr>
											<td colspan="4"></td>
											<td>
												<hr class="item_dashed" style="width:350% !important;">
											</td>
											<td></td>
											<td></td>
										</tr>
										<?php if ($billing['handling_charges'] > 0) { ?>
											<tr>
												<td colspan="3"></td>
												<td width="">H.C</td>
												<td width="1%;">Rs.</td>
												<td style="text-align:right;"><?php echo $billing['handling_charges']; ?></td>
											</tr>
										<?php } ?>
										<tr>
											<td colspan="4"></td>
											<td width="">Total</td>
											<td width="1%;">Rs.</td>
											<td style="text-align:right;"><?php echo number_format($billing['tot_bill_amount'], 2, '.', ''); ?></td>
										</tr>
										<tr>
											<td colspan="4"></td>
											<td>
												<hr class="item_dashed" style="width:350% !important;">
											</td>
											<td></td>
											<td></td>
										</tr>
									</table>
								</div>
							</div>
						</div><br>
					<?php } ?>

					<div class="<?php echo (sizeof($est_other_item['old_matel_details']) != 0) ? 'purchase_div' : ''; ?>">

						<?php if (sizeof($est_other_item['old_matel_details']) > 0 && $billing['bill_type'] != 5) { ?>
							<div id="purchase_print_view">
								<?php
								if (sizeof($est_other_item['item_details']) != 0) { ?>
									<span style="font-weight: bold">PURCHASE INVOICE NO : <?php echo $billing['branch_code'] . $billing['fin_year_code'] . $billing['metal_code'] . '-' . $billing['pur_ref_no']; ?></span>
								<?php }
								?>
								<table id="pp" class="table text-center">
									<!--	<thead> -->
									<tr style="">
										<td>
											<hr class="old_metal_dashed">
										</td>
									</tr>
									<?php if (sizeof($est_other_item['item_details']) == 0 || sizeof($est_other_item['old_matel_details']) > 0) { ?>
										<tr style="text-transform:uppercase;">
											<td style="width: 5%;" class="table_heading">S.No</td>
											<td style="width: 25%;" class="table_heading">DESCRIPTION</td>
											<td style="width: 10%;" class="table_heading">Ex.Value%</td>
											<td style="width: 10%;" class="table_heading">HSN</td>
											<td style="width: 9%;" class="table_heading alignRight">GROSS WT</td>
											<td style="width: 9%;" class="table_heading alignRight">LESS WT</td>
											<td style="width: 15%;" class="table_heading alignRight">Calc.Wt</td>
											<td style="width: 9%;" class="table_heading alignRight">Rate</td>
											<td style="width: 15%;" class="table_heading alignRight">Amount</td>
										</tr>
										<tr>
											<td>
												<hr class="old_metal_dashed">
											</td>
										</tr>
									<?php } ?>
									<!--</thead>
													<tbody>-->
									<?php
									/*$old_metal_details = array();
													foreach ($est_other_item['old_matel_details'] as $olditem) {
														$key = $olditem['metal_type'];
														if (!array_key_exists($key, $old_metal_details)) {
															$old_metal_details[$key] = array(
																'metal_type'=> $olditem['metal_type'],
																'gross_wt' => $olditem['gross_wt'],
																'net_wt' => $olditem['net_wt'],
																'amount' => $olditem['amount'],
															);
														} else {
															$old_metal_details[$key]['gross_wt'] = $old_metal_details[$key]['gross_wt'] + $olditem['gross_wt'];
															$old_metal_details[$key]['net_wt'] = $old_metal_details[$key]['net_wt'] + $olditem['net_wt'];
															$old_metal_details[$key]['amount'] = $old_metal_details[$key]['amount'] + $olditem['amount'];
														}
													}*/
									$i = 1;
									$total_amt = 0;
									$pieces = 0;
									$gross_wt = 0;
									$net_wt = 0;
									foreach ($est_other_item['old_matel_details'] as $items) {
										$esti_purchase_emp = $items['esti_emp_name'];
										$esti_purchase_id = $items['esti_emp_id'];
										$gross_wt += $items['gross_wt'];
										$net_wt += ($items['net_wt']);
										$stone_amount = 0;


										if (sizeof($items['stone_details']) > 0) {
											foreach ($items['stone_details'] as $stoneItems) {
												$stone_amount += $stoneItems['price'];
											}
										}
									?>
										<tr>
											<td style="width: 5%"><?php echo $i; ?></td>
											<td style="font-size:10px; width: 25%" class='textOverflowHidden'><?php echo $items['old_metal_type']; ?></td>
											<td style="width: 10%"><?php echo $items['touch']; ?></td>
											<td style="width: 10%">71080000</td>
											<td style="width: 9%" class="alignRight"><?php echo number_format($items['gross_wt'], 3, '.', ''); ?></td>
											<td style="width: 9%" class="alignRight"><?php echo number_format($items['dust_wt']+$items['stone_wt'], 3, '.', ''); ?></td>
											<td style="width: 15%" class="alignRight"><?php echo number_format($items['net_wt'], 3, '.', ''); ?></td>
											<td style="width: 9%" class="alignRight"><?php echo number_format($items['rate_per_gram'], 3, '.', ''); ?></td>
											<td style="width: 15%" class="alignRight"><?php echo moneyFormatIndia(number_format(($items['amount']), 2, '.', '')); ?></td>
										</tr>

										<?php if (count($items['stone_details']) > 0) {
											foreach ($items['stone_details'] as $stoneItems) {	?>

												<tr class="stones">
													<td></td>
													<td class='textOverflowHidden stoneData'><?php echo $stoneItems['stone_name']; ?></td>
													<td class="alignRight stoneData"><?php echo moneyFormatIndia(number_format((float)($stoneItems['wt']), 3, '.', '')) . '/' . $stoneItems['uom_short_code']; ?></td>
													<td></td>
													<td class="alignRight stoneData"><?php echo $stoneItems['rate_per_gram'] . ' /  ' . $stoneItems['uom_short_code']; ?></td>
													<td class="alignRight"></td>
													<td class="alignRight"></td>
													<td class="alignRight stoneData"><?php echo moneyFormatIndia('Rs : ' . $stoneItems['price']); ?></td>
													<td class="alignRight"></td>
													<td></td>
												</tr>
										<?php
											}
										} ?>
									<?php $i++;
									} ?>
									<!--</tbody> -->
									<tr>
										<td>
											<hr class="old_metal_dashed">
										</td>
									</tr>
									<tr style="font-weight: bold">
										<td>Total</td>
										<td></td>
										<td></td>
										<td></td>
										<td class="alignRight"><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
										<td></td>
										<td class="alignRight"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
										<td></td>
										<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$pur_total_amt, 2, '.', '')); ?></td>
									</tr>
									<tr style="display: none;">
										<td>
											<hr class="old_metal_dashed">
										</td>
									</tr>


									<?php if ($pur_total_amt != '' && $type != 'sr' && $type != 'p') { ?>
										<?php if (($tot_sales_amt + $billing['handling_charges'] > 0)) { ?>
											<tr>
												<td colspan="3"></td>
												<td class="alignRight" colspan="3">Sales Amount</td>
												<td class="alignRight">Rs.</td>
												<td class="alignRight"><?php echo moneyFormatIndia((number_format($tot_sales_amt + $billing['handling_charges'], 2, '.', ''))); ?></td>
											</tr>
										<?php } ?>
										<?php if ($total_return > 0) { ?>
											<tr>
												<td colspan="3"></td>
												<td class="alignRight" colspan="3">Credit Amount</td>
												<td class="alignRight">Rs.</td>
												<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return, 2, '.', '')); ?></td>
											</tr>
										<?php } ?>
										<?php if ($pur_total_amt > 0) { ?>
											<tr style="font-weight:bold;">
												<td colspan="3">
													<?php
													$cgst_sgst_perc = ($est_other_item['item_details'][0]['tax_percentage'] / 2);
													$cgst_sgst_amt = ($pur_total_amt * $cgst_sgst_perc / 100);
													?>
													<!--RCM : (CGST <?php echo  $cgst_sgst_perc . "% - " . number_format($cgst_sgst_amt, 2, '.', '') ?>,  SGST <?php echo  $cgst_sgst_perc . "% - " . number_format($cgst_sgst_amt, 2, '.', '') ?>)-->
												</td>
												<td class="alignRight" colspan="3">Purchase Amount </td>
												<td class="alignRight">Rs.</td>
												<td class="alignRight"><?php echo moneyFormatIndia(number_format($pur_total_amt, 2, '.', ''));
																		$amount_in_words = number_format($pur_total_amt, 2, '.', ''); ?></td>
											</tr>
										<?php } ?>
										<?php $round_off = moneyFormatIndia(number_format($billing['round_off_amt'], 2, '.', '')); ?>
										<?php if ($round_off != 0) { ?>
											<tr>
												<td colspan="3"></td>
												<td class="alignRight" colspan="3">Round Off</td>
												<td class="alignRight">Rs.</td>
												<td class="alignRight"><?php echo $round_off ?></td>
											</tr>
											<tr>
												<td colspan="3"></td>
												<td colspan="7">
													<hr class="item_dashed">
												</td>
											</tr>
										<?php } ?>
										<?php if ($tot_sales_amt > 0) { ?>
											<tr style="font-weight:bold">
												<td colspan="3"></td>
												<td class="alignRight" colspan="2">TOTAL </td>
												<td class="alignRight">Rs.</td>
												<td class="alignRight"><?php echo moneyFormatIndia(number_format(round(($tot_sales_amt + $billing['handling_charges']) - $total_return - $pur_total_amt + $round_off), 2, '.', ''));
																		$amount_in_words = (number_format(round(($tot_sales_amt + $billing['handling_charges']) - $total_return - $pur_total_amt + $round_off), 2, '.', ''));  ?></td>
											</tr>
											<tr>
												<td colspan="3"></td>
												<td colspan="7">
													<hr class="item_dashed">
												</td>
											</tr>
										<?php } ?>

									<?php } ?>
								</table>
							</div>
						<?php } ?>
					</div><!-- End of Purchase Div -->


					<?php if (sizeof($est_other_item['repair_order_details']) > 0) { ?>
						<div class="row">
							<div align="center">
								<label><b>ORDER NO : <?php echo $est_other_item['repair_order_details'][0]['order_no']; ?></b></label>
							</div>
							<hr class="old_metal_header_dashed">
							<div class="col-xs-12">
								<div class="table-responsive">
									<table id="pp" class="table text-center">
										<!--	<thead> -->
										<tr style="text-transform:uppercase;font-weight:bold">
											<td>S.No</td>
											<td>Description</td>
											<td>Repair WT(g)</td>
											<td>Completed Wt(g)</td>
											<td>Amount</td>
										</tr>
										<tr>
											<td>
												<hr class="old_metal_dashed">
											</td>
										</tr>
										<!--</thead>
										<tbody>-->
										<?php
										$i = 1;
										$tot_repair_amt = 0;
										$gst = 0;
										$tot_gst = 0;
										$igst = 0;
										$tot_igst = 0;
										$taxable_amt = 0;
										$tot_order_wt = 0;
										$tot_completed_wt = 0;
										$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_bill_amount']);
										foreach ($est_other_item['repair_order_details'] as $items) {
											$tot_order_wt += $items['weight'];
											$tot_completed_wt += $items['completed_weight'];
											$tot_repair_amt += $items['rate'];
											$tot_gst += $items['repair_tot_tax'];
											$igst += $items['igst'];
											$cgst += $items['cgst'];
											$sgst += $items['sgst'];
											$taxable_amt += $items['rate'] - $items['repair_tot_tax'];
										?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $items['product_name']; ?></td>
												<td><?php echo ($items['weight']); ?></td>
												<td><?php echo $items['completed_weight']; ?></td>
												<td><?php echo number_format($items['rate'] - $items['repair_tot_tax'], 2, '.', ''); ?>
												</td>
											</tr>
										<?php $i++;
										} ?>
										<!--</tbody> -->
										<tr>
											<td>
												<hr class="old_metal_dashed">
											</td>
										</tr>
										<tr>
											<td></td>
											<td>Total</td>
											<td><?php echo number_format($tot_order_wt, 3, '.', ''); ?></td>
											<td><?php echo number_format($tot_completed_wt, 3, '.', ''); ?></td>
											<td><?php echo number_format($taxable_amt, 2, '.', ''); ?></td>
										</tr>
										<tr>
											<td>
												<hr class="old_metal_dashed">
											</td>
										</tr>
										<?php if ($tot_repair_amt > 0) { ?>
											<tr>
												<td colspan="2"></td>
												<td width="">SUB TOTAL5</td>
												<td>Rs.</td>
												<td><?php echo number_format($taxable_amt, 2, '.', ''); ?></td>
											</tr>
										<?php } ?>
										<?php if ($cgst > 0 || $sgst > 0) { ?>
											<tr>
												<td colspan="2"></td>
												<td width="">CGST</td>
												<td><?php echo ($items['repair_percent'] / 2) . '%' ?></td>
												<td><?php echo number_format($cgst, 2, '.', ''); ?></td>
											</tr>
											<tr>
												<td colspan="2"></td>
												<td width="">SGST</td>
												<td><?php echo ($items['repair_percent'] / 2) . '%' ?></td>
												<td><?php echo number_format($sgst, 2, '.', ''); ?></td>
											</tr>
										<?php } ?>
										<?php if ($igst > 0) { ?>
											<tr>
												<td colspan="2"></td>
												<td width="">IGST</td>
												<td><?php echo ($items['repair_percent']) . '%' ?></td>
												<td><?php echo number_format($igst, 2, '.', ''); ?></td>
											</tr>
										<?php } ?>
										<?php if ($tot_repair_amt > 0) { ?>
											<tr>
												<td colspan="2"></td>
												<td>
													<hr class="total_dashed" style="width:300% !important;">
												</td>
											</tr>
											<tr>
												<td colspan="2"></td>
												<td width="">Total</td>
												<td>Rs.</td>
												<td><?php echo number_format($tot_repair_amt, 2, '.', ''); ?></td>
											</tr>
											<tr>
												<td colspan="2"></td>
												<td>
													<hr class="total_dashed" style="width:300% !important;">
												</td>
											</tr>
										<?php } ?>
									</table><br>
								</div>
							</div>
						</div><br>
					<?php } ?>

					<?php if ($billing['bill_type'] == 8) { ?>
						<div class="row">
							<!--<div align="center" style="text-transform:uppercase;">
												<label>Credit No : <?php echo $billing['ref_bill_id']; ?></label>
											</div>-->
							<div class="col-xs-12">
								<div class="table-responsive">
									<table id="pp" class="table text-center">
										<tr>
											<td>
												<hr class="old_metal_dashed">
											</td>
										</tr>
										<tr>
											<td style="width: 70%; font-weight: bold">Description</td>
											<td style="width: 30%; font-weight: bold">Amount</td>
										</tr>
										<tr>
											<td><?php echo 'Received with thanks from Mr./Ms.' . $billing['customer_name'] . ' Towards Credit Bill No : ' . $billing['ref_bill_no']; ?></td>
											<td><?php echo $billing['due_amount']; ?></td>
										</tr>
										<tr>
											<td>
												<hr class="old_metal_dashed">
											</td>
										</tr>
									</table>
									<br>
								</div>
							</div>
						</div><br>
					<?php } ?>

					<?php if ($billing['bill_type'] == 10) { ?>
						<div class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
									<table id="pp" class="table text-center">
										<tr>
											<td style="width: 70%; font-weight: bold">S.No</td>
											<td style="width: 70%; font-weight: bold">Ref No</td>
											<td style="width: 30%; font-weight: bold">Amount</td>
										</tr>
										<tr>
											<td>
												<hr class="old_metal_dashed">
											</td>
										</tr>
										<?php
										$i = 1;
										foreach ($est_other_item['chit_details'] as $chit) { ?>
											<tr>
												<td><?php echo $i; ?></td>
												<td><?php echo $chit['scheme_acc_number']; ?></td>
												<td><?php echo moneyFormatIndia($chit['utilized_amt']); ?></td>
											</tr>
										<?php $i++;
										} ?>
										<tr>
											<td>
												<hr class="old_metal_dashed">
											</td>
										</tr>
									</table>
									<br>
								</div>
							</div>
						</div><br>
					<?php } ?>

					<?php
					$due_amount = 0;
					if ($billing['bill_type'] == 8) {
						if ($billing['is_credit'] == 1) {
							$due_amount = number_format(($billing['tot_bill_amount'] - $billing['tot_amt_received']), 2, '.', '');
						} else {
							/*if($billing['due_amount']!=$billing['tot_paid_amt'])
														    {
														        $due_amount=number_format(($billing['due_amount']-($billing['tot_amt_received']-$billing['tot_paid_amt']+$pur_total_amt)),2,'.','');
														    }*/
							$due_amount = number_format(($billing['tot_bill_amount'] - $billing['tot_amt_received']), 2, '.', '');
						}
					} else {
						if ($billing['is_credit'] == 1) {
							$due_amount = number_format(($billing['tot_bill_amount'] - $billing['tot_amt_received']), 2, '.', '');
						}
					}

					if (sizeof($payment['pay_details']) > 0) {
						$cash_amt = 0;
						$card_amt = 0;
						$net_banking_amt = 0;
						$gift_amount = 0;
						$chit_amount = 0;
						$total_amt = 0;
						$adv_adj = 0;
						$imps_amt = 0;
						$rtgs_amt = 0;
						$upi_amt = 0;
						$chq_amt = 0;
						$neft_amt =0;
						foreach ($payment['pay_details'] as $items) {
							$total_amt += $items['payment_amount'];
							if ($items['payment_mode'] == 'Cash') {
								$cash_amt += $items['payment_amount'];
							}
							if ($items['payment_mode'] == 'CHQ') {
								$chq_amt += $items['payment_amount'];
							}
							if ($items['payment_mode'] == 'DC' || $items['payment_mode'] == 'CC') {
								$card_amt += $items['payment_amount'];
							}
							if ($items['payment_mode'] == 'NB' && $items['transfer_type'] == 'RTGS') {
								$net_banking_amt += $items['payment_amount'];
								$rtgs_amt += $items['payment_amount'];
							}
							if ($items['payment_mode'] == 'NB' && $items['transfer_type'] == 'IMPS') {
								$net_banking_amt += $items['payment_amount'];
								$imps_amt += $items['payment_amount'];
							}
							if ($items['payment_mode'] == 'NB' && $items['transfer_type'] == 'UPI') {
								$net_banking_amt += $items['payment_amount'];
								$upi_amt += $items['payment_amount'];
							}
							if ($items['payment_mode'] == 'NB' && $items['transfer_type'] == 'NEFT') {
								$net_banking_amt += $items['payment_amount'];
								$neft_amt += $items['payment_amount'];
							}
						}
					} ?>

					<?php if (sizeof($est_other_item['chit_details']) > 0 && (($billing['bill_type'] != 10))) {
						$chit_adj = 0;
						$bouns_amt = 0;
						foreach ($est_other_item['chit_details'] as $chit) {
							if (($chit['paid_installments'] == $chit['total_installments']) && ($chit['scheme_type'] == 0) && $chit['firstPayDisc_value'] > 0) {
								$bouns_amt += ($chit['paid_installments'] * $chit['firstPayDisc_value']);
							}
							$chit_adj += $chit['closing_amount'];
						}
						$chit_adj = $chit_adj;
					?>

					<?php } ?>
					<?php if (sizeof($est_other_item['voucher_details']) > 0) {
						foreach ($est_other_item['voucher_details'] as $voc) {
							$gift_amount += $voc['gift_voucher_amt'];
						}
					?>
					<?php } ?>
					<?php if (sizeof($est_other_item['order_adj']) > 0) {
						$ord_adj_amt = 0;
						$chit_adj_wt = 0;
						foreach ($est_other_item['order_adj'] as $ord) {
							/*	if($ord['received_weight']>0)
											{
												$chit_adj_wt +=($ord['received_weight']*($ord['rate_per_gram']));
											}*/
							$ord_adj_amt += $ord['advance_amount'];
						}
						$ord_adj_amt = $ord_adj_amt + $chit_adj_wt;
					?>

					<?php } ?>
					<div class="pur_word" style="font-size: 16px !important;font-weight:bold">
						<?php echo !empty($billing['sales_ref_no']) ? 'REFER BILL NO : ' . $billing['branch_code'] . $billing['fin_year_code'] . '-' . $billing['sales_ref_no'] : ''; ?>
					</div>



					<div class="payment_print">
						<?php
						if ($pur_total_amt > 0 ||  $cash_amt > 0 || $chq_amt > 0 || $card_amt > 0 || $rtgs_amt > 0 || $imps_amt > 0 || $upi_amt > 0 || $neft_amt > 0 || $adv_adj > 0 || $gift_amount > 0 || $billing['advance_deposit'] > 0 || $due_amount != 0) { ?>
							<!--<table>
							<tr>
								<?php if ($due_amount != 0) { ?>
									<td>DUE AMOUNT</td>
								<?php } ?>
								<?php if ($adv_adj > 0) { ?>
									<td>ADVANCE ADJ</td>
								<?php } ?>
								<?php if ($chit_adj != 0 && ($billing['bill_type'] != 10)) { ?>
									<td>CHIT ADJ</td>
								<?php } ?>
								<?php if ($bouns_amt != 0) { ?>
									<td>BONUS</td>
								<?php } ?>
								<?php if ($ord_adj_amt != 0 || $billing['adv_adj_amt'] != 0) { ?>
									<td>ADV ADJ</td>
								<?php } ?>
								<?php if ($gift_amount != 0) { ?>
									<td>GIFT UTILIZED</td>
								<?php } ?>
								<?php if ($billing['credit_disc_amt'] != 0) { ?>
									<td>DISCOUNT</td>
								<?php } ?>
								<?php if ($billing['advance_deposit'] != 0) { ?>
									<td>Advance</td>
								<?php } ?>
							</tr>
							<tbody>
								<tr>
									<?php if ($due_amount != 0) { ?>
										<td><?php echo moneyFormatIndia(number_format($due_amount, 2, '.', '')); ?></td>
									<?php } ?>
									<?php if ($adv_adj != 0) { ?>
										<td><?php echo moneyFormatIndia(number_format($adv_adj, 2, '.', '')); ?></td>
									<?php } ?>
									<?php if ($chit_adj != 0  && ($billing['bill_type'] != 10)) { ?>
										<td><?php echo moneyFormatIndia(number_format($chit_adj, 2, '.', '')); ?></td>
									<?php } ?>
									<?php if ($bouns_amt > 0) { ?>
										<td><?php echo moneyFormatIndia(number_format($bouns_amt, 2, '.', '')); ?></td>
									<?php } ?>
									<?php if ($ord_adj_amt != 0 || $billing['adv_adj_amt'] != 0) { ?>
										<td><?php echo moneyFormatIndia(number_format($ord_adj_amt + $billing['adv_adj_amt'], 2, '.', '')); ?></td>
									<?php } ?>
									<?php if ($gift_amount != 0) { ?>
										<td><?php echo moneyFormatIndia(number_format($gift_amount, 2, '.', '')); ?></td>
									<?php } ?>
									<?php if ($billing['credit_disc_amt'] != 0) { ?>
										<td><?php echo moneyFormatIndia(number_format($billing['credit_disc_amt'], 2, '.', '')); ?></td>
									<?php } ?>
									<?php if ($billing['advance_deposit'] != 0) { ?>
										<td><?php echo number_format($billing['advance_deposit'], 2, '.', '') ?></td>
									<?php } ?>
								</tr>
							</tbody>
						</table>-->
							<?php $deposit_bill_id = 0; ?>
							<?php foreach ($billing['adjusted_in_sales'] as $items) {
								$deposit_bill_id = $items['deposit_bill_id'];
							} ?>

							<table id="pp" class="pay_mode_totals table text-left" style="width:100%;">
								<div align="left">
									<label><b>PAYMENT DETAILS : </b></label>
								</div>
								<tbody>
									<?php if ($cash_amt > 0) { ?>
										<tr>
											<td style="width:24%;">CASH</td>
											<td style="width:10%;" class="alignRight"><?php echo moneyFormatIndia(number_format($cash_amt, 2, '.', '')); ?></td>
											<td style="width:66%;"></td>

										</tr>
									<?php } ?>
									<?php if ($chq_amt > 0) { ?>
										<?php foreach ($payment['pay_details'] as $chq) {
											if ($chq['payment_mode'] == 'CHQ') { ?>
												<tr>
													<td style="width:24%">CHEQUE</td>
													<td style="width:10%" class="alignRight"><?php echo moneyFormatIndia(number_format($chq['payment_amount'], 2, '.', '')); ?></td>
													<td style="width:66%"><?php echo ($chq['cheque_no'] != '' ? 'Chq\ Ref.No - ' . $chq['cheque_no'] : '') . ($chq['cheque_date'] != '' ? ' .Dtd - ' . $chq['cheque_date'] : '') . ($chq['bank_name'] != '' ? '- ' . $chq['bank_name'] : '') ?></td>

												</tr>
									<?php }
										}
									} ?>
									<?php if ($card_amt > 0) { ?>
										<?php foreach ($payment['pay_details'] as $cardItem) {
											if ($cardItem['payment_mode'] == 'DC' || $cardItem['payment_mode'] == 'CC') { ?>
												<tr>
													<td style="width:24%"><?php echo $cardItem['payment_mode'] == 'DC' ? 'DEBIT CARD' : ($cardItem['payment_mode'] == 'CC' ? 'CREDIT CARD' : '') ?></td>
													<td style="width:10%" class="alignRight"><?php echo moneyFormatIndia(number_format($cardItem['payment_amount'], 2, '.', '')); ?></td>
													<td style="width:66%"><?php echo $cardItem['card_no'] != '' ? ' Ref.No - ' . $cardItem['card_no'] : '' ?></td>

												</tr>
									<?php  }
										}
									} ?>

									<?php if ($rtgs_amt > 0) {
										foreach ($payment['pay_details'] as $rtgs) {
											if ($rtgs['transfer_type'] == 'RTGS') {
												$rtgs_ref_no = $rtgs['payment_ref_number'];
												$rtgs_amount = $rtgs['payment_amount'] ?>
												<tr>
													<td style="width:24%">RTGS</td>
													<td class="alignRight" style="width:10%;"><?php echo moneyFormatIndia(number_format($rtgs_amount, 2, '.', '')); ?></td>
													<td style="width:66%"><?php echo ($rtgs_ref_no != '' ? ' Ref.No - ' . $rtgs_ref_no : '') . ($rtgs['net_banking_date'] != '' ? ' .Dtd - ' . $rtgs['net_banking_date'] : '') ?> </td>
												</tr>
											<?php }
											?>
									<?php }
									}  ?>

									<?php if ($imps_amt > 0) {
										foreach ($payment['pay_details'] as $imps) {
											if ($imps['payment_mode'] == 'NB' && $imps['transfer_type'] == 'IMPS') {
												$imps_ref_no = $imps['payment_ref_number'];
												$imps_amount = $imps['payment_amount']; ?>
												<tr>
													<td style="width:24%">IMPS</td>
													<td class="alignRight" style="width:10%;"><?php echo moneyFormatIndia(number_format($imps_amount, 2, '.', '')); ?></td>
													<td style="width:66%;"><?php echo ($imps_ref_no != '' ? ' Ref.No - ' . $imps_ref_no : '') . ($imps['net_banking_date'] != '' ? ' .Dtd - ' . $imps['net_banking_date'] : '') ?></td>
												</tr>
											<?php	}
											?>
									<?php }
									} ?>

									<?php if ($upi_amt > 0) {

										foreach ($payment['pay_details'] as $nb) {
											if ($nb['transfer_type'] == 'UPI') {
												$upi_amount = 	$nb['payment_amount'];
												$ref_no = $nb['payment_ref_number']
									?>
												<tr>
													<!-- <td colspan="7"></td> -->
													<td style="width:24%">UPI</td>
													<td class="alignRight" style="width:10%;"><?php echo moneyFormatIndia(number_format($upi_amount, 2, '.', '')); ?></td>
													<td style="width:66%;"><?php echo ($ref_no != '' ? ' Ref.No - ' . $ref_no : '') . ($nb['net_banking_date'] != '' ? ' .Dtd - ' . $nb['net_banking_date'] : '') ?></td>

												</tr>
									<?php }
										}
									} ?>
									
									<?php if ($neft_amt > 0) {

										foreach ($payment['pay_details'] as $nb) {
											if ($nb['transfer_type'] == 'NEFT') {
												$NEFT_amount = 	$nb['payment_amount'];
												$ref_no = $nb['payment_ref_number']
									?>
												<tr>
													<!-- <td colspan="7"></td> -->
													<td style="width:24%">NEFT</td>
													<td class="alignRight" style="width:10%;"><?php echo moneyFormatIndia(number_format($NEFT_amount, 2, '.', '')); ?></td>
													<td style="width:66%;"><?php echo ($ref_no != '' ? ' Ref.No - ' . $ref_no : '') . ($nb['net_banking_date'] != '' ? ' .Dtd - ' . $nb['net_banking_date'] : '') ?></td>

												</tr>
									<?php }
										}
									} ?>

									<?php if ($due_amount != 0) { ?>
										<tr>
											<td style="width:24%">DUE AMOUNT</td>
											<td style="width:10%" class="alignRight"><?php echo moneyFormatIndia(number_format($due_amount, 2, '.', '')); ?></td>
											<td style="width:66%"><?= $billing['credit_due_date'] != '' ? 'Due Date : ' . $billing['credit_due_date'] : '' ?></td>

										</tr>
									<?php } ?>
									<?php if (($ord_adj_amt != 0 || $billing['adv_adj_amt'] != 0) && $deposit_bill_id == '' && $billing['ord_adv_adj'] == '' && $receiptDetails == '') { ?>
										<tr>
											<td style="width:24%">ADV ADJ</td>
											<td style="width:10%" class="alignRight"><?php echo moneyFormatIndia(number_format($ord_adj_amt + $billing['adv_adj_amt'], 2, '.', '')); ?></td>
											<td style="width:66%"></td>

										</tr>
									<?php } ?>

									<?php if (sizeof($receiptDetails) > 0) {

										foreach ($receiptDetails as $val) { ?>
											<tr>
												<td style="width:24%"><?= $val['adjuseted_amt'] != '' ? 'ADV ADJ AMOUNT ' : '' ?></td>
												<td style="width:10%" class="alignRight"><?php echo number_format($val['adjuseted_amt'], 2, '.', '') . ' '; ?></td>
												<td style="width:66%"><?= $val['bill_no'] != '' ? ('Ref Receipt No : ' . $val['bill_no']) : '0' ?></td>
											</tr>
									<?php }
									} ?>

									<?php if ($chit_adj != 0  && ($billing['bill_type'] != 10)) { ?>
										<tr>
											<td style="width:24%">CHIT ADJ</td>
											<td style="width:10%"><?php echo moneyFormatIndia(number_format($chit_adj, 2, '.', '')); ?></td>
											<td style="width:66%"></td>

										</tr>
									<?php } ?>


									<?php if ($total_return > 0 && $billing['bill_type'] != 7) { ?>
										<tr>
											<td style="width:24%;" class="alignLeft">CREDIT NOTE</td>
											<td style="width:10%;" class="alignRight"><?php echo moneyFormatIndia(number_format($total_return, 2, '.', '')) ?></td>
											<td style="width:66%;" class="alignLeft"><?php echo $billing['sales_refno'] != '' ? 'Ref Credit Note Bill No: ' . $billing['sales_refno'] : '' ?></td>
										</tr>
									<?php } ?>

									<?php if ($pur_total_amt > 0 && $billing['bill_type'] != 4) { ?>
										<tr>
											<td style="width:24%;" class="alignLeft"><?php echo $old['metal_name'] != '' ? 'BY CUST OWN ' . $old['metal_name'] . ' EX ' : 'BY CUST OWN EX' ?></td>
											<td style="width:10%;" class="alignRight"><?php echo moneyFormatIndia(number_format($pur_total_amt, 2, '.', '')) ?></td>
											<td style="width:66%;" class="alignLeft"><?php echo $billing['purchase_ref_no'] != '' ? '&nbsp;&nbsp;&nbsp;Ref Pur No : ' . $billing['purchase_ref_no'] : '' ?></td>
										</tr>
									<?php } ?>

									<?php if ($bouns_amt > 0) { ?>
										<tr>
											<td style="width:24%">BONUS</td>
											<td style="width:10%" class="alignRight"><?php echo moneyFormatIndia(number_format($bouns_amt, 2, '.', '')); ?></td>
											<td style="width:66%"></td>

										</tr>
									<?php } ?>
									<?php if ($gift_amount != 0) { ?>
										<tr>
											<td style="width:24%">GIFT UTILIZED</td>
											<td style="width:10%" class="alignRight"><?php echo moneyFormatIndia(number_format($gift_amount, 2, '.', '')); ?></td>
											<td style="width:66%"></td>

										</tr>
									<?php } ?>
									<?php if ($billing['credit_disc_amt'] != 0) { ?>
										<tr>
											<td style="width:24%">DISCOUNT</td>
											<td style="width:10%" class="alignRight"><?php echo moneyFormatIndia(number_format($billing['credit_disc_amt'], 2, '.', '')); ?></td>
											<td style="width:66%"></td>
										</tr>
									<?php } ?>
									<?php if ($billing['advance_deposit'] != 0 && sizeof($billing['sales_rtn_bill_no']) == '') { ?>
										<tr>
											<td style="width:24%">ADVANCE</td>
											<td style="width:10%" class="alignRight"><?php echo number_format($billing['advance_deposit'], 2, '.', '') ?></td>
											<td style="width:66%"><?php echo $billing['adv_recpt_no'] != '' ? '&nbsp;&nbsp;&nbsp;Receipt No: ' . $billing['adv_recpt_no'] : '' ?></td>
										</tr>
									<?php } ?>
									<!--<?php if (sizeof($billing['sales_rtn_bill_no']) > 0) {
											foreach ($billing['sales_rtn_bill_no'] as $adj) { ?>
										<tr>
											<td style="width:24%"><?php echo $adj['bill_no'] != '' ? 'REFER SALES BILL : ' : ''  ?></td>
											<td style="width:10%" class="alignRight"><?php echo $adj['bill_no']; ?></td>
											<td style="width:66%"></td>
										</tr>
									<?php	}
										} ?>-->

									<!-- <?php if (sizeof($billing['sales_rtn_bill_no']) > 0) {
												foreach ($billing['sales_rtn_bill_no'] as $adj) { ?>
										<tr>
											<td style="width:24%"><?php echo $adj['sales_ref_bill_no'] != '' ? 'REFER SALES BILL : ' : ($adj['order_adv_ref_no'] != '' ? 'REFER ORDER ADVANCE NO : ' : '') ?></td>
											<td style="width:10%" class="alignRight"><?php echo $adj['sales_ref_bill_no'] != '' ? $adj['sales_ref_bill_no'] : ($adj['order_adv_ref_no'] != '' ? $adj['order_adv_ref_no'] : ''); ?></td>
											<td style="width:66%"></td>
										</tr>
									<?php	}
											} ?> -->

									<?php if (sizeof($billing['sales_rtn_bill_no']) > 0) {
										foreach ($billing['sales_rtn_bill_no'] as $adj) { ?>
											<tr>
												<td style="width:24%;display:none"><?php echo $adj['sales_ref_no'] != '' ? 'REFER SALES BILL : ' : ($adj['order_adv_ref_no'] != '' ? 'REFER ORDER ADVANCE NO : ' : '') ?></td>
												<td style="width:10%;display:none" class="alignRight"><?php echo $adj['sales_ref_no'] != '' ? $adj['sales_ref_no'] : ($adj['order_adv_ref_no'] != '' ? $adj['order_adv_ref_no'] : ''); ?></td>
												<td style="width:66%;display:none"></td>
											</tr>
									<?php	}
									} ?>
									<?php if (sizeof($billing['adv_rcpt_no']) > 0) {
									?>
										<tr>
											<td style="width:24%"><?php echo $billing['adv_rcpt_no'] != '' ? 'ADVANCE  RECEIPT NO : ' : '' ?></td>
											<td style="width:10%" class="alignRight"><?php echo $billing['adv_rcpt_no'] != '' ? $billing['adv_rcpt_no'] : '' ?></td>
											<td style="width:66%"></td>
										</tr>
									<?php
									} ?>


									<?php if (sizeof($est_other_item['order_adj']) > 0) {

										foreach ($est_other_item['order_adj'] as $ord) { ?>

											<tr>
												<td style="width:24%"><?php echo  'ADVANCE  ADJ ' ?></td>
												<td style="width:10%" class="alignRight"><?php echo number_format(round($ord['advance_amount']), 2, '.', ''); ?></td>
												<td style="width:66%"><?php echo 'Dtd- '.$ord['bill_date'] . ' Bill no : ' . $ord['order_adv_ref_no'] ?></td>
											</tr>


									<?php }
									} ?>

								</tbody>
							</table>












										<?php if($emp_id['employee_nmae']!=''){ ?> 

											<p>sales employee name - <?php echo $emp_id['employee_nmae']?></p>

											<p>bill employee name - <?php echo $emp_id['employee_nmae']?></p>




											<?php }?>




















							<?php /* if ($amount_in_words != "") {
							$words_amt = $amount_in_words < 0 ? $amount_in_words * -1 : $amount_in_words;
						?>
							<div class="amount_in_words">
								Amount in Words
								: <span><?php echo $this->ret_billing_model->no_to_words($words_amt); ?> Only </span>
							</div>
						<?php } */ ?>
						<?php }
						?>
						<?php
						if ($cash_amt < 0 || $chq_amt < 0  || $rtgs_amt < 0 || $imps_amt < 0 || $billing['adjusted_in_sales']) { ?>
							<div align="left">
								<label><b>PAYMENT DETAILS: </b></label>
							</div>
							<table id="pp" class="pay_mode_totals table text-center" style="width:30%;font-weight: bold;">
								<tr>
									<?php if ($cash_amt < 0) { ?>
								<tr>
									<td>CASH</td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($cash_amt, 2, '.', '')); ?></td>
								</tr>
							<?php } ?>
							<?php if ($chq_amt > 0) { ?>
								<?php foreach ($payment['pay_details'] as $chq) {
									if ($chq['payment_mode'] == 'CHQ') { ?>
										<tr>
											<td style="width:24%"><?php echo $chq['payment_mode'] == 'CHQ' ? 'CHEQUE ' :  '' ?>(<?php echo $chq['cheque_no'] . ($chq['bank_name'] != '' ? '-' . $chq['bank_name'] : '') ?>)</td>
											<td style="width:10%" class="alignRight"><?php echo moneyFormatIndia(number_format($chq_amt, 2, '.', '')); ?></td>
											<td style="width:66%"></td>

										</tr></br>
							<?php  }
								}
							} ?>
							<?php if ($card_amt < 0) { ?>
								<?php foreach ($payment['pay_details'] as $cardItem) {
									if ($cardItem['payment_mode'] == 'DC' || $cardItem['payment_mode'] == 'CC') { ?>
										<tr>
											<td><?php echo $cardItem['payment_mode'] == 'DC' ? 'DEBIT CARD' : ($cardItem['payment_mode'] == 'CC' ? 'CREDIT CARD' : '') ?>(<?php echo $cardItem['card_no'] ?>)</td>
											<td class="alignRight"><?php echo moneyFormatIndia(number_format($cardItem['payment_amount'], 2, '.', '')); ?></td>
										</tr></br>
							<?php  }
								}
							} ?>
							<?php if ($rtgs_amt < 0) { ?>
								<tr>
									<td>RTGS</td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($rtgs_amt, 2, '.', '')); ?></td>
								</tr>
							<?php } ?>
							<?php if ($imps_amt < 0) { ?>
								<tr>
									<td>IMPS</td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($imps_amt, 2, '.', '')); ?></td>
								</tr>
							<?php }   ?>
							<?php if ($upi_amt < 0) { ?>
								<tr>
									<td>UPI</td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($upi_amt, 2, '.', '')); ?></td>
								</tr>
							<?php } ?>
							
							<?php if ($neft_amt < 0) { ?>
								<tr>
									<td>NEFT</td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($neft_amt, 2, '.', '')); ?></td>
								</tr>
							<?php } ?>
							</tr>
							<tbody>
							</tbody>
							</table>
						<?php }
						?>
					</div><!-- end of payment print  -->
					<!-- End of Payment Details -->

					<?php if ((sizeof($est_other_item['chit_details']) > 0) && $billing['bill_type'] != 10) { ?>
						<div class="col-xs-6">
							<div class="table-responsive">
								<table id="pp" class="table text-center">
									<tr>
										<td width="2%;">S.No</td>
										<td width="5%;">Ref No</td>
										<td width="25%;">Amount</td>
									</tr>
									<tr>
										<td>
											<hr class="item_dashed" style="width:450% !important;">
										</td>
									</tr>
									<?php
									$i = 1;
									foreach ($est_other_item['chit_details'] as $chit) { ?>
										<tr>
											<td><?php echo $i; ?></td>
											<td><?php echo $chit['scheme_acc_number']; ?></td>
											<td><?php echo moneyFormatIndia($chit['closing_amount']); ?></td>
										</tr>
									<?php $i++;
									} ?>
								</table>
							</div>
						</div>
					<?php } ?>
					<br>

					<!--	<?php if (sizeof($est_other_item['order_adj']) > 0) { ?>
						<div class="col-xs-6" style="width:50%;">
							<div class="table-responsive">
								<table id="pp" class="table text-center">
									<tr>
										<td>Date</td>
										<td class="alignRight">Amount</td>
										<td class="alignRight">Rate</td>
										<td class="alignRight">Weight</td>
									</tr>
									<tr>
										<td>
											<hr class="item_dashed" style="width:400% !important;">
										</td>
									</tr>
									<?php
									$i = 1;
									$adv_paid_amount = 0;
									$adv_paid_weight = 0;
									foreach ($est_other_item['order_adj'] as $ord) {
										$adv_paid_amount += ($ord['store_as'] == 1 ? $ord['advance_amount'] : ($ord['received_weight'] * $ord['rate_per_gram']));
										$adv_paid_weight += ($ord['store_as'] == 1 ? ($ord['advance_amount'] / $ord['rate_per_gram']) : $ord['received_weight']);
									?>
										<tr>
											<td><?php echo $ord['bill_date']; ?></td>
											<td class="alignRight"><?php echo number_format(($ord['store_as'] == 1 ? $ord['advance_amount'] : ($ord['received_weight'] * $ord['rate_per_gram'])), 2, '.', ''); ?></td>
											<td class="alignRight"><?php echo $ord['rate_per_gram']; ?></td>
											<td class="alignRight"><?php echo number_format(($ord['store_as'] == 1 ? ($ord['advance_amount'] / $ord['rate_per_gram']) : $ord['received_weight']), 3, '.', ''); ?></td>
										</tr>
									<?php $i++;
									} ?>
									<tr>
										<td>
											<hr class="item_dashed" style="width:400% !important;">
										</td>
									</tr>
									<tr>
										<td>Total</td>
										<td class="alignRight"><?php echo moneyFormatIndia(number_format($adv_paid_amount, 2, '.', '')) ?></td>
										<td class="alignRight"></td>
										<td class="alignRight"><?php echo number_format($adv_paid_weight, 3, '.', '') ?></td>
									</tr>
								</table>
							</div>
						</div>
					<?php } ?>-->


					<!-- <?php if ($billing['gift_issue_amount'] > 0) { ?>
						<span>Gift Voucher Worth Rs. <?php echo ' ' . $billing['gift_issue_amount'] . ' ' ?>Valid Till <?php echo $billing['valid_to'] . '. Voucher Code - ' . $billing['code'] . ''; ?></span>
					<?php } else if ($billing['gift_issue_weight'] > 0) { ?>
						<span>Gift Voucher Worth <?php echo ' ' . $billing['gift_issue_weight'] . ' ' . (($billing['utilize_for']) == 1 ? ' Gold ' : ' Silver ') . ' ' ?>Valid Till <?php echo $billing['valid_to'] . '. Voucher Code - ' . $billing['code'] . ''; ?></span>
					<?php } ?> -->

					<?php
					if ($billing['note'] != '') { ?>
						<label>Terms and Conditions</label>
						<?php echo $billing['note']; ?>
					<?php } ?>
					</div><br>

					<?php if (sizeof($receiptDetails) > 0 && $billing['bill_type'] == 1) {
						$tot_adv = 0;
						$adj_amt = 0;
						foreach ($receiptDetails as $val) { ?>
							<div>
								<table id="pp" class="table text-center" style="width:85%">
									<tr>
										<td><b>Receipt No</b></td>
										<td><b>Receipt Date</b></td>
										<td><b>Receipt Amount</b></td>
										<td><b>Adjusted Amount</b></td>
										<td><b>Utilized Amount</b></td>
										<td><b>Balance Amount</b></td>
									</tr>
									<tbody>
										<tr>
											<td><?php echo ($val['bill_no']); ?></td>
											<td><?php echo ($val['bill_date']); ?></td>
											<td><?php echo number_format($val['tot_receipt_amount'], 2, '.', ''); ?></td>
											<td><?php echo number_format($val['adjuseted_amt'], 2, '.', ''); ?></td>
											<td><?php echo number_format($val['tot_utilized_amt'], 2, '.', ''); ?></td>
											<td><?php echo number_format($val['bal_amt'], 2, '.', ''); ?></td>
										</tr>
									</tbody>
								</table>
							</div><br>
					<?php }
					} ?>

					<?php if ($amount_total > 0) { ?>
						<div style="margin-top: 3px; margin-bottom: 3px">
							<div><span style="font-weight: bold;">Amount in Words</span> : <span>Rupees <?php echo $this->ret_billing_model->no_to_words($amount_in_words); ?> Only</span></div>
						</div>
					<?php } ?>
					<?php if ($billing['delivered_at'] == 2) { ?>
						<div class="row">
							<label><b>Delivered safely at : </b></label><br>
							<label><?php echo ($billing['del_add_address1'] != '' ? strtoupper($billing['del_add_address1']) . ',' . "<br>" : ''); ?></label>
							<label><?php echo ($billing['del_add_address2'] != '' ? strtoupper($billing['del_add_address2']) . ',' . "<br>" : ''); ?></label>
							<label><?php echo ($billing['del_add_address3'] != '' ? strtoupper($billing['del_add_address3']) . ',' . "<br>" : ''); ?></label>
							<label><?php echo ($billing['del_city_name'] != '' ? strtoupper($billing['del_city_name']) . ($billing['del_pincode'] != '' ? ' - ' . $billing['del_pincode'] . '.' : '') : ''); ?><br></label>
							<label><?php echo ($billing['del_state_name'] != '' ? strtoupper($billing['del_state_name']) . ',' . "<br>" : ''); ?></label>
						</div>
					<?php } ?>
				</div>
			</div><!-- /.box-body -->
			<div></div>
		</span>

		<body>
		<div style="height:50px">
		</div>
		<div class="signature">
			<table>
				<td style="font-weight: bold;" class="cus_sign">
					Customer Signature
				</td>
				<td class="auth_sign">
					<div style="font-weight: bold;"> <?php
														$emp_name = $billing['emp_name'] != '' ? $billing['emp_name'] . ' ' : '';
														$system_id = $billing['counter_short_code'] != '' ? (' - System ID : ' . $billing['counter_short_code'] . ' - ') : '';
														$current_date = date("d-m-y h:i:sa");
														?>
						<label>Operator Signature</label></br>
						<?php echo $emp_name . $system_id . $current_date; ?>
					</div>
				</td>
				<td style="font-weight: bold;" class="cashier_sign">
					Cashier Signature
				</td>
			</table>
			<div style="padding-top:40px; height:20px">
				*This is System generated Issue *E & O.E.
			</div>
		</div>
	</div>
</body>

</html>

<script type="text/javascript">
	<?php
	$pur = $type;
	if ($pur_total_amt > 0 && $type == '') { ?>
		<?php $pur = 'p'; ?>
		let url = "<?php echo base_url() ?>index.php/admin_ret_billing/billing_invoice/<?php echo $billing['bill_id'] . "/" . $pur ?>";
		window.open(url, '_blank');
	<?php } else if ($sal_ret == 1 && ($type == '' || $type == 'p')) { ?>
		<?php $pur = 'sr'; ?>
		let url = "<?php echo base_url() ?>index.php/admin_ret_billing/billing_invoice/<?php echo $billing['bill_id'] . "/" . $pur ?>";
		window.open(url, '_blank');
	<?php }  ?>
	//    window.print();
</script>