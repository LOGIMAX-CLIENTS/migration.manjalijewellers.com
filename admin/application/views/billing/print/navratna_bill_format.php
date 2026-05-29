<html>

<head>
	<meta charset="utf-8">
	<title>Billing Receipt</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/billing_receipt_2.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
	<style>
		.head {
			color: black;
			font-size: 30px;
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
			/* text-overflow: ellipsis; */
		}

		.duplicate_copy * {
			font-size: 9px;
		}

		.duplicate_copy #pp th,
		.duplicate_copy #pp td {
			font-size: 9px !important;
		}


		.stones,
		.charges {
			font-style: italic;
		}

		.stones .stoneData,
		.charges .chargeData {
			font-size: 10px !important;
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

		.wrapper {
			display: flex;
			width: 100%;
			height: 100px;
		}

		#a1 {
			width: 40%;
		}

		#a3 {
			width: 35%;
			text-transform: uppercase;
			font-size: 11px !important;
			font-weight: bold;
		}

		#a1,
		#a3 {

			font-size: 10px !important;
			font-weight: bold;
		}

		#a2 {
			width: 20%;
			font-weight: bold;
			font-size: 18px;
			text-align: center;
			text-transform: uppercase;
		}

		#a2sub {
			width: 5%;
			font-size: 11px !important;
			font-weight: bold;
		}

		/* #a3{
            text-align:right;
        } */
		.footer {
			text-align: justify;

		}

		.new_tax {
			font-weight: bold;
			font-size: 18px;
			text-align: center;
			text-transform: uppercase;
		}

		.footer {

			width: 100%;

			height: 100px;

		}

		.sub_inv {
			text-transform: uppercase;
			font-weight: bold;
		}

		@page {

			size: A4;

			margin-top: 140px;
			margin-bottom: 20px;
			margin-left: 25px;
			margin-right: 25px;

		}

		@media print {

			table.paging tfoot td {

				height: 110px;

			}

			/* .footer {

				position: fixed;

				bottom: 0;
			} */
		}
	</style>

</head>

<body>

	<div class="PDFReceipt">

		<?php

		$login_emp = $billing['emp_name'];

		$esti_sales_emp = '';
		$esti_purchase_emp = '';
		$esti_return_emp = '';
		$first_rate = 0;

		$esti_sales_id = '';
		$esti_purchase_id = '';
		$esti_return_id = '';
		$is_eda = $billing['is_eda'];

		function moneyFormatIndia($num)
		{
			return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
		}
		function removeLastWordAs(&$string)
		{
			$substring = substr($string, 0, strrpos($string, "&"));
			$substring = trim($substring);
			$lastCharacter = strrchr($substring, "&");
			if ($lastCharacter !== false) {
				return substr($substring, 0, -strlen($lastCharacter));
			} else {
				return $substring;
			}
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
		foreach ($est_other_item['old_matel_details'] as $items) {
			$pur_total_amt += $items['amount'];
		}


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
		} ?>

<?php if (sizeof($receiptDetails) > 0) {
	$adj_amt = 0;

	foreach ($receiptDetails as $val) {
		$adj_amt += $val['adjuseted_amt'];
		$refund_amt += $val['refund_amt'];
	}
	$adj_amt = number_format($adj_amt + $refund_amt, 2, '.', '');

		} ?>


		<table class=paging>
			<thead>
				<tr>
					<td>
						<div class="content-block">
							<?php
							if (sizeof($est_other_item['item_details']) > 0) //SALES BILL 
							{
								if ($billing['bill_type'] == 15) {
									$invoice_no = $billing['branch_code'] . '-SA-' . $billing['approval_ref_no'];
								} else {
									$invoice_no = $billing['branch_code'] . '-SA-' . $billing['sales_ref_no'];
								}
							} else if (sizeof($est_other_item['old_matel_details']) > 0) // OLD METAL ITEMS
							{
								$invoice_no =  $billing['branch_code'] . '-PU-' . $billing['pur_ref_no'];
							} else if (sizeof($est_other_item['return_details']) > 0) //SALES RETURN
							{
								$invoice_no =  $billing['branch_code'] . '-SR-' . $billing['s_ret_refno'];
							} else if ($billing['bill_type'] == 5) //ORDER ADVANCE
							{
								$invoice_no =  $billing['branch_code'] . '-OR-' . $billing['order_adv_ref_no'];
							} else if ($billing['bill_type'] == 8)   //CREDIT COLLECTION
							{
								$invoice_no =  $billing['branch_code'] . '-CC-' . $billing['credit_coll_refno'];
							} else if ($billing['bill_type'] == 10)   //CHIT PRE CLOSE
							{
								$invoice_no =  $billing['branch_code'] . '-' . $billing['chit_preclose_refno'];
							} else {
								$invoice_no =  $billing['bill_no'];
							}

							$inv_no =  $this->ret_billing_model->get_bill_no_format_detail($billing['bill_id'], '');
							?>

							<div style="width:100%;" class="new_tax">
								<div style="text-decoration: underline; text-align:center;"> <?php echo ($billing['is_eda']==1 ? 'tax invoice' : 'Proforma' )?> </div>
								<span style="text-align:center;font-size: 12px;text-transform:none;"> <?= ($billing['is_today'] == 1 ? '' : '( Duplicate Copy )') ?> </span>
							</div>
							<div class="wrapper">
								<div id="a1">
									<?php if ($billing['billing_for'] == 1 || $billing['billing_for'] == 2) { ?>

										<div>
											<?php echo ($billing['bill_type'] == 11 ? 'BILL TO' . '<br><br>' : '') ?>
											<label><?php echo (!empty($billing['title']) ? $billing['title'] . '. ' : 'Mr/Mrs. ') . strtoupper($billing['customer_name']); ?></label><br>
											<label><?php echo ($billing['address1'] != '' ?  strtoupper($billing['address1']) . ',' : ''); ?></label>
											<label><?php echo ($billing['address2'] != '' ?  strtoupper($billing['address2']) . ',' : ''); ?></label>
											<label><?php echo ($billing['address3'] != '' ?  strtoupper($billing['address3']) . ',' : ''); ?></label>
											<label><?php echo ($billing['village_name'] != '' ? '<br>' . strtoupper($billing['village_name']) . ',' : ''); ?></label>
											<label><?php echo ($billing['city'] != '' ?  '<br>' . strtoupper($billing['city']) : '<br>'); ?></label>
											<label><?php echo ($billing['pincode'] != '' ?  '-' . $billing['pincode'] . '.' : '') ?></label>
											<!-- <label><?php echo ($billing['cus_state'] != '' ? 'State:&nbsp;&nbsp;' . strtoupper($billing['cus_state'] . '-' . $billing['state_code']) . ',' . "<br>" : ''); ?></label>
                                    	<label><?php echo ($billing['cus_country'] != '' ? 'Country:&nbsp;&nbsp;' . strtoupper($billing['cus_country']) : ''); ?></label><br> -->
											<label><?php echo (isset($billing['pan_no']) && $billing['pan_no'] != '' ? '<br>PAN NO:&nbsp;&nbsp;' . strtoupper($billing['pan_no']) . '' : ''); ?></label>
											<label><?php echo (isset($billing['adhar_no']) && $billing['adhar_no'] != '' ? '<br>AADHAR NO:&nbsp;&nbsp;' . strtoupper($billing['adhar_no']) . '' : ''); ?></label>
											<label><?php echo (isset($billing['passport_no']) && $billing['passport_no'] != '' ? '<br>PASSPORT NO:&nbsp;&nbsp;' . strtoupper($billing['passport_no']) . '' : ''); ?></label>
											<label><?php echo (isset($billing['driving_license_no']) && $billing['driving_license_no'] != '' ? '<br>DRIVING LICENSE NO:&nbsp;&nbsp;' . strtoupper($billing['driving_license_no']) . '' : ''); ?></label>
											<label><?php echo (isset($billing['gst_number']) && $billing['gst_number'] != '' ? '<br>GST IN:&nbsp;&nbsp;' . strtoupper($billing['gst_number'])  : ''); ?></label>
											<label><?php echo  '<br>MOBILE: ' . $billing['mobile']; ?></label><br>
											<?php
											if (!empty($billing['irnno'])) { ?>
												<label style="font-weight: bold;overflow-wrap: break-word;">
													IRN : <?php echo $billing['irnno']; ?>
												</label>
											<?php } ?>
										</div>


									<?php } else if ($billing['billing_for'] == 3 && $billing['bill_type'] != 13 && $billing['bill_type'] != 14) { ?>
										<div>

											<label><?php echo 'Name:&nbsp;&nbsp;' . 'Mr./Ms.' . $billing['karigar_name']; ?></label><br>
											<label><?php echo 'Mobile:&nbsp;&nbsp;' . $billing['mobile']; ?></label><br>
											<label><?php echo ($billing['karigar_address1'] != '' ? 'Address:&nbsp;&nbsp;' . strtoupper($billing['karigar_address1']) . ',' : ''); ?></label><br>
											<label><?php echo ($billing['karigar_address2'] != '' ? '&nbsp;&nbsp;&nbsp;' . strtoupper($billing['karigar_address2']) . ',' : ''); ?></label><br>
											<label><?php echo (isset($billing['pan_no']) && $billing['pan_no'] != '' ? 'PAN :&nbsp;&nbsp;' . strtoupper($billing['pan_no'])  : ''); ?></label><br>
											<label><?php echo (isset($billing['karigar_gst_number']) && $billing['karigar_gst_number'] != '' ? 'GST IN:&nbsp;&nbsp;' . strtoupper($billing['karigar_gst_number']) : ''); ?></label>
											<br>
											<label><?php echo 'place of supply:&nbsp;&nbsp;' . $comp_details['state'] . '-' . $comp_details['state_code']; ?></label><br>
											<label><?php echo 'reverse charge:&nbsp;&nbsp;'; ?></label><br>
										</div>
									<?php } else { ?>
										<div>

											<label><?php echo  $billing['transfer_details']['name']; ?></label><br>
											<label><?php echo  $billing['transfer_details']['address1']; ?>,</label><br>
											<label><?php echo  $billing['transfer_details']['address2']; ?>,</label><br>
											<label><?php echo  $billing['transfer_details']['city'] . '-' . $billing['transfer_details']['pincode']; ?></label><br>
											<label><?php echo  $billing['transfer_details']['state']; ?></label>
											<label><?php echo '<br>GST IN:&nbsp;&nbsp;' . $billing['transfer_details']['gst_number']; ?></label>
										</div>
									<?php } ?>
								</div>
								<div id="a2">
									<!-- <div style="text-decoration: underline"> tax invoice </div> -->
									<?php if($billing['is_eda']==1){

										if (!empty($billing['irnno'])) {
											$imageTag = '<img src="data:image/png;base64,' . $billing['bqrcodeimage'] . '" alt="QR Code"  style="height:125px; width:125px;">';

											// Output the image tag
											echo $imageTag;
									?>
											<!-- <img src="<?php echo base_url() . "einvqrcode/" . $billing['irnno'] . ".jpg"; ?>" alt="QRcode" style="height:125px; width:125px;"> -->
										<?php } else { ?>
											<img src="<?php echo base_url(); ?>bill_qrcode/<?php echo $qrfilename; ?>.png" alt="QRcode" style="height:65px; width:65px;">
									<?php } } ?>
								</div>
								<div id="a2sub"></div>
								<div id="a3">
									<div>
										<table style="font-weight:bold;">
											<tr>
												<td style="width:40%;"><label><?php echo ($billing['bill_type'] == 15 ? 'Ack No' : ($billing['is_eda']==1 ? 'Invoice No' : 'Proforma No')); ?></label></td>
												<td style="width:10%;">:</td>
												<td style="width:50%;"><?php echo $inv_no; ?></td>
											</tr>
											<tr>
												<td><label>Date</label></td>
												<td>:</td>
												<td><?php echo $billing['bill_date'] ?></td>
											</tr>
											<tr>
												<td><label>Time</label></td>
												<td>:</td>
												<td><?php echo $billing['bill_time']; ?></td>
											</tr>
											<tr>
												<td><label>Gold 22-KT</label></td>
												<td>:</td>
												<td><?php echo  number_format($gold_metal_rate, 2, '.', '') . '/Gm'; ?></td>
											</tr>
											<tr>
												<td><label>Gold 18-KT</label></td>
												<td>:</td>
												<td><?php echo  number_format($billing['goldrate_18ct'], 2, '.', '') . '/Gm'; ?></td>
											</tr>
											<tr>
												<td><label>SILVER</label></td>
												<td>:</td>
												<td><?php echo number_format($silver_metal_rate, 2, '.', '') . '/Gm'; ?></td>
											</tr>
											<tr>
												<?php if ($billing['is_eda'] == 1) { ?>
												<td><label>GSTIN</label></td>
												<td>:</td>
												<td><?php echo $comp_details['gst_number']; ?></td>
												<?php } ?>
											</tr>

											<!-- <tr>
												<td><label>Place Of Supply</label></td>
												<td>:</td>
												<td><?php //echo ($billing['delivered_at'] == 1 ? ( $billing['bill_type']==13 ? $billing['branch_name'] : 'Showroom') : 'Customer Address'); ?></td>
											</tr> -->

											<!-- <?php if ($billing['state_code'] != '') { ?>
												<tr>
													<td><label for="statecode">State Code</label></td>
													<td>:</td>
													<td><?php echo $billing['state_code']; ?></td>
												</tr>
											<?php  } ?> -->
										</table>

									</div>
								</div>
							</div> <br>
							<div>

								<?php
								$title = "";

								if ($billing['bill_type'] == 9) {
									$title = "ORDER DELIVERY";
								} elseif (sizeof($est_other_item['item_details']) > 0 && sizeof($est_other_item['old_matel_details']) > 0) {
									$title = "SALES AND PURCHASE BILL";
								} elseif (sizeof($est_other_item['item_details']) > 0 && sizeof($est_other_item['old_matel_details']) == 0) {
									$title = "SALES BILL";
									if ($billing['is_to_be'] == 1) {
										$title = 'TO BE ' . $title;
									}
								} elseif ($billing['bill_type'] == 5) {
									$title = "ORDER RECEIPT";
								} elseif ($billing['bill_type'] == 7) {
									$title = "SALES RETURN";
								} elseif ($billing['bill_type'] == 8) {
									$title = "CREDIT COLLECTION";
								}
								?>

								<div for="title" style="text-align:center;font-weight:bold;"><?php if($billing['bill_status']==2){echo $title." - CANCELLED";} else {echo $title;}?></div> <br>

							</div><br>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>




						<div class="content-wrapper" style="margin-top:-30px;">
							<div class="box">
								<div class="box-body">
									<div class="container-fluid">
										<div id="printable">
											<?php if (sizeof($est_other_item['item_details']) > 0) { ?>
												<?php
												if ($est_other_item['item_details'][0]['order_no'] != '') {


												?>
													<div align="center">
														<label><b>Order No.<?php echo $est_other_item['item_details'][0]['order_no']; ?></b></label>
													</div>
												<?php }
												?>



												<div class="col-xs-12">
													<div class="table-responsive">
														<table id="pp" class="table">
															<tr>
																<td colspan="9">
																	<hr class="item_dashed">
																</td>
															</tr>
														</table>
														<table id="pp" class="table">
															<thead style="text-transform:uppercase;font-size:12px;">

																<tr>
																	<th class="table_heading alignCenter" style="width: 7%; text-align:center;">S.No</th>
																	<th class="table_heading textOverflowHidden" style="width: 35%; text-align:left;">Description </th>
																	<th class="table_heading alignRight" style="width: 11%;">HSN</th>
																	<th class="table_heading alignRight" style="width: 5%;">PCS</th>
																	<th class="table_heading alignRight" style="width: 10%;">GRS.WT</th>
																	<th class="table_heading alignRight" style="width: 10%;">NET.WT</th>
																	<!-- <th class="table_heading alignRight" style="width: 6%;">V.A</th>
																	<th class="table_heading alignRight" style="width: 6%;">MC</th> -->
																	<td></td>
																	<td></td>
																	<th class="table_heading alignRight" style="width: 11%;">Rate</th>
																	<th class="table_heading alignRight" style="width: 11%;">Amount</th>
																</tr>
															</thead>

															<tbody>
																<tr>
																	<th colspan="10">
																		<hr class="item_dashed">
																	</th>
																</tr>
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
																$other_metal_amount =0;
																foreach ($est_other_item['chit_details'] as $chit) {
																	if ($chit['closing_weight'] > 0) {
																		$rate_per_gram = ($chit['rate_per_gram'] > 0 ? $chit['rate_per_gram'] :$billing['goldrate_22ct']);
																		$savings_in_making_charge += $chit['savings_in_making_charge'];
																		$savings_in_wastage += ($chit['savings_in_wastage'] * $rate_per_gram);
																		$closing_weight += number_format($chit['closing_weight'], 3, '.', '');
																		$closing_amount += ($chit['closing_amount']);
																		$scheme_benefit = number_format((($closing_weight * $rate_per_gram) - $closing_amount + $savings_in_making_charge + $savings_in_wastage+$chit['additional_benefits']), 2, '.', '');
																	}
																}

																$scheme_benefit = 0;


																foreach ($est_other_item['item_details'] as $items) {
																	$esti_sales_emp = $items['esti_emp_name'];
																	$esti_sales_id = $items['esti_emp_id'];

																	$mc = 0;
																	$wastge_wt = 0;

																	$stone_amount = 0;
																	if (count($items['stone_details']) > 0) {
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

																	$tot_item_discount += $items['bill_discount'];

																	foreach ($items['other_metal_details'] as $other_metal_details){
																		$other_metal_amount += $other_metal_details['tag_other_itm_amount'];
																	}

																	if($settings['bill_discount_type'] == 2){
																		$taxable_amt    += $items['item_cost'] - $items['item_total_tax'];
																		$item_taxable   = number_format((float)$items['item_cost'] - $items['item_total_tax'], 2, '.', '');
																	}else{																	
																		$taxable_amt    += $items['item_cost'] + $items['bill_discount'] - $items['item_total_tax'];
																		$item_taxable   = number_format((float)$items['item_cost'] + $items['bill_discount'] - $items['item_total_tax'], 2, '.', '');
																		// print_r($items['item_cost']);exit;
																	}
																	if ($billing['tot_bill_amount'] > 0) {
																		$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_bill_amount']);
																	} else {
																		$amt_in_words   = $this->ret_billing_model->no_to_words(-1 * $billing['tot_bill_amount']);
																	}
																	// $item_taxable   = number_format((float)$items['item_cost'] - $items['item_total_tax'], 2, '.', '');
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
																		$discount_weight = ($items['wastage_discount'] / $items['rate_per_grm']);
																		$item_wastge_wt = $wastge_wt - $discount_weight;
																		
																	} else {
																		$item_wastge_wt = $wastge_wt;
																	}
																	if ($items['mc_discount'] > 0) {
																		$mc        = $mc - $items['mc_discount'];
																	}
																	$total_mc        += $mc;
																	$tot_wastage_wt += $item_wastge_wt;
																	$wastage_value = round($item_wastge_wt * $items['rate_per_grm'], 2);

																?>


																	<tr>
																		<td style="text-align:center"><?php echo $i; ?></td>
																		<td style="text-align:left;" class="textOverflowHidden"><?php echo $items['product_name']; ?><?php echo ' - ' . $items['design_name']; ?></td>
																		<td class="alignRight"><?php echo $items['hsn_code']; ?></td>
																		<td class="alignRight"><?php echo $items['piece']; ?></td>
																		<td class="alignRight"><?php echo $items['gross_wt'] . ($items['pro_uom'] != '' ? '-' . $items['pro_uom'] : ''); ?></td>
																		<td class="alignRight"><?php echo $items['net_wt'] . ($items['pro_uom'] != '' ? '-' . $items['pro_uom'] : '') ?></td>
																		<!-- <td class="alignRight">
															<?php
																	if ($items['net_wt'] >= 1) {
																		$va_g = round(($wastage_value + $mc) / $items['net_wt'], 0);
																		echo $va_g > 0 ? $va_g . "/g " : '';
																	} else {
																		echo round($wastage_value + $mc, 0);
																	}
															?> 
														</td> -->
																		<!-- <td class="alignRight"><?php echo $item_wastge_wt > 0 ? moneyFormatIndia(number_format($item_wastge_wt, 3, '.', '')) : ''; ?></td>
																		<td class="alignRight"><?php echo $mc > 0 ? moneyFormatIndia(number_format($mc, 2, '.', '')) : ''; ?></td> -->
																		 <td></td>
																		 <td></td>
																		<td class="alignRight"><?php echo moneyFormatIndia($items['rate_per_grm']); ?></td>
																		<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($item_taxable > 0 ? ($item_taxable - $other_metal_amount) : 0), 2, '.', '')); ?></td>
																	</tr>
																	<!-- <tr>
															<td></td>
															<td colspan="2"><?php echo $items['huid'] ? 'huid: ' . $items['huid'] : ''; ?> </td>
															<td colspan="6"></td>
														</tr> -->

																	<?php if (sizeof($items['other_metal_details']) > 0) { ?>

																		<?php foreach ($items['other_metal_details'] as $other_metal_details) { ?>
																			<tr style>
																				<td></td>
																				<td><?php echo $other_metal_details['metal'] ?></td>

																				<td></td>
																				<td></td>
																				<!-- <td></td> -->
																				<td class="alignRight"><?php echo $other_metal_details['tag_other_itm_grs_weight'] ?></td>

																				<td class="alignRight"><?php echo $other_metal_details['tag_other_itm_grs_weight'] ?></td>

																				<!-- <td class="alignRight"><?php echo $other_metal_details['tag_other_itm_wastage'] . '%' ?></td>

																				<td class="alignRight"><?php echo moneyFormatIndia($other_metal_details['tag_other_itm_mc']) ?></td> -->
																				<td></td>
																				<td></td>
																				<td class="alignRight"><?php echo moneyFormatIndia($other_metal_details['tag_other_itm_rate']) ?></td>

																				<td class="alignRight"><?php echo moneyFormatIndia(number_format($other_metal_details['tag_other_itm_amount'], 2, '.', '')) ?></td>

																			</tr>

																	<?php }
																	} ?>
																	<?php
																	if (count($items['stone_details']) > 0) {
																		foreach ($items['stone_details'] as $stoneItems) {

																	?>
																			<tr class="stones">
																				<td></td>
																				<?php
																				$uom = ($stoneItems['stone_type'] == 1) ? 'CT' : 'ST';
																				?>
																				<?php if ($stoneItems['stone_cal_type'] == 1) { ?>
																					<td colspan="4" class='textOverflowHidden stoneData'><?php echo $stoneItems['pieces'] . ' Pcs ' . $stoneItems['stone_name'] . ($stoneItems['amount'] > 0 ? ' ' . number_format($stoneItems['wt'], 3, '.', '') . ' ' . $uom . 'x' . moneyFormatIndia(number_format($stoneItems['rate_per_gram'], 2, '.', '')) . '=' . moneyFormatIndia(number_format((float)($stoneItems['amount']), 2, '.', '')) : ''); ?></td>
																				<?php } else if ($stoneItems['stone_cal_type'] == 2) { ?>
																					<td colspan="4" class='textOverflowHidden stoneData'><?php echo number_format($stoneItems['wt'], 3, '.', '') . ' ' . $uom . ' ' . $stoneItems['stone_name'] . ($stoneItems['amount'] > 0 ? ' ' . $stoneItems['pieces'] . 'Pcs ' . 'x' . $stoneItems['rate_per_gram'] . '=' . moneyFormatIndia(number_format((float)($stoneItems['amount']), 2, '.', '')) : ''); ?></td>
																				<?php } ?>
																				<td class="alignRight stoneData"></td>
																				<td class="alignRight stoneData"></td>
																				<td class="alignRight stoneData"></td>
																				<td class="alignRight"></td>
																				<td class="alignRight stoneData"></td>
																			</tr>
																		<?php }
																	}

																	if (count($items['charges']) > 0) {
																		foreach ($items['charges'] as $chargeItems) { ?>
																			<tr class="charges" style="display:none;">
																				<td></td>
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
																		<td></td>
																		<td colspan="2"><?php echo $items['show_huid'] == 1 &&  $items['huid']   ? 'HUID: ' . $items['huid'] : ''; ?> </td>
																		<td colspan="7"></td>
																	</tr>
																<?php $i++;
																} ?>
															</tbody>
															<tr>
																<td colspan="10">
																	<hr class="item_dashed">
																</td>
															</tr>
															<tr class="total" style="font-weight: bold">
																<td class="alignCenter">Total</td>
																<td></td>
																<td></td>
																<td class="alignRight"><?php echo $pieces; ?></td>
																<td class="alignRight"><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
																<td class="alignRight"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
																<td></td>
																<td></td>
																<td></td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($taxable_amt), 2, '.', '')); ?></td>
															</tr>
															<tr>
																<td colspan="10">
																	<hr class="item_dashed">
																</td>
															</tr>

															<?php if($settings['bill_discount_type'] == 1){
															if ($tot_item_discount > 0) { ?>
																<tr>
																	<td colspan="5"></td>
																	<td class="alignRight">DISCOUNT</td>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($tot_item_discount), 2, '.', '')); ?></td>
																</tr>
															<?php }} ?>	


															<?php if ($taxable_amt > 0) { ?>
																<tr>
																	<td colspan="5"></td>
																	<td class="alignRight">SUB TOTAL</td>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo $settings['bill_discount_type'] == 1 ? moneyFormatIndia(number_format((float)($taxable_amt - $tot_item_discount), 2, '.', '')) : moneyFormatIndia(number_format((float)($taxable_amt), 2, '.', '')) ; ?></td>
																</tr>
															<?php } ?>

															<?php if ($total_sgst > 0) { ?>
																<tr>
																	<td colspan="5"></td>
																	<td class="alignRight">SGST<?php echo (sizeof($est_other_item['tax_details']) == 1 ? '(' . ($est_other_item['item_details'][0]['tax_percentage'] / 2) . '%)' : '') ?></td>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_sgst), 2, '.', '')); ?></td>
																</tr>
															<?php } ?>

															<?php if ($total_cgst > 0) { ?>
																<tr>
																	<td colspan="5"></td>
																	<td class="alignRight">CGST <?php echo (sizeof($est_other_item['tax_details']) == 1 ? '(' . ($est_other_item['item_details'][0]['tax_percentage'] / 2) . '%)' : '') ?></td>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_cgst), 2, '.', '')); ?></td>
																</tr>
															<?php } ?>

															<?php if ($total_igst > 0) { ?>
																<tr>
																	<td colspan="5"></td>
																	<td class="alignRight">IGST <?php echo (sizeof($est_other_item['tax_details']) == 1 ? '(' . ($est_other_item['item_details'][0]['tax_percentage']) . '%)' : '') ?></td>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_igst), 2, '.', '')); ?></td>
																</tr>
															<?php } ?>

															<?php if ($scheme_benefit > 0) { ?>
																<tr>
																	<td colspan="5"></td>
																	<td class="alignRight">CHIT BENEFIT</td>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($scheme_benefit), 2, '.', '')); ?></td>
																</tr>
															<?php } ?>

															<?php if ($billing['handling_charges'] > 0) { ?>
																<tr>
																	<td colspan="5"></td>
																	<td class="alignRight">H.C</td>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format($billing['handling_charges'], 2, '.', '')); ?></td>
																</tr>
															<?php } ?>

															<?php
															if ((sizeof($est_other_item['return_details']) == 0) && ((sizeof($est_other_item['old_matel_details']) == 0)) && ($billing['round_off_amt'] != 0)) { ?>
																<tr>
																	<td colspan="5"></td>
																	<td class="alignRight">Round Off</td>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format($billing['round_off_amt'], 2, '.', '')); ?></td>
																</tr>
																<tr>
																	<td colspan="9"></td>
																	<td>
																		<hr class="item_dashed">
																	</td>
																</tr>
																<tr>
																	<td colspan="5"></td>
																	<th class="alignRight" style="font-size: 12px;">TOTAL</th>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<th class="alignRight" style="font-size: 12px;"><?php echo moneyFormatIndia(number_format($tot_sales_amt + $billing['handling_charges'] + $billing['round_off_amt'] - $scheme_benefit, 2, '.', '')); ?></th>
																</tr>
																<tr>
																	<td colspan="9"></td>
																	<td>
																		<hr class="item_dashed">
																	</td>
																</tr>

															<?php } else { ?>
																<tr>
																	<td colspan="9"></td>
																	<td>
																		<hr class="item_dashed">
																	</td>
																</tr>
																<tr>
																	<td colspan="5"></td>
																	<th class="alignRight" style="font-size: 12px;">TOTAL</th>
																	<td colspan="2"></td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<th class="alignRight" style="font-size: 12px;"><?php echo moneyFormatIndia(number_format($tot_sales_amt + $billing['handling_charges'] + $billing['round_off_amt'] - $scheme_benefit, 2, '.', '')); ?></th>
																</tr>
																<tr>
																	<td colspan="9"></td>
																	<td>
																		<hr class="item_dashed">
																	</td>
																</tr>
															<?php } ?>

															<?php if ($billing['tcs_tax_amt'] > 0) { ?>
																<tr>
																	<td colspan="6"></td>
																	<td colspan="2" class="alignRight">TCS (<?php echo $billing['tcs_tax_per'] . '%'; ?>)</td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format($billing['tcs_tax_amt'], 2, '.', '')); ?></td>
																</tr>

																<tr>
																	<td colspan="9"></td>
																	<td colspan="1">
																		<hr class="item_dashed">
																	</td>
																</tr>
																<tr style="font-weight:bold">
																	<td colspan="6"></td>
																	<td colspan="2" class="alignRight">NET AMT</td>
																	<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format($tot_sales_amt + $billing['handling_charges'] + $billing['tcs_tax_amt'] - $scheme_benefit, 2, '.', '')); ?></td>
																</tr>
															<?php } ?>
														</table>
														<!-- <div style="margin-left:10px; font-size:14px !important;">
													<?php foreach ($est_other_item['item_details'] as $items) {

														if ($items['description'] != '') {
															$j = 1;
													?>
										<b>Order Details</b> <br>
								        <label><?php echo $j . '.' . $items['description'] . '.'; ?></label><br>

								    <?php

															$j++;
														}
													}; ?>
												</div> -->
													</div>
												</div>
										</div>
									<?php }
											//echo "<pre>"; print_r($stones); echo "</pre>";exit;
									?>


									<?php if (sizeof($est_other_item['return_details']) > 0) { ?>

										<div class="sub_inv">
											<span>Sales Return Invoice No : <?php
																			$ret_no = $this->ret_billing_model->get_bill_no_format_detail($billing['bill_id'], 'sr');
																			echo  $ret_no; 
																			if (!empty($billing['bill_date'])) {
																				echo ' / Date : ' . date('d-m-Y', strtotime($billing['bill_date']));
																			}
																			?></span>

										</div>
										<hr class="return_dashed">
										<table id="pp" class="table text-center">

											<thead style="text-transform:uppercase;font-size:12px;">
												<tr>
													<th class="table_heading alignCenter" style="width: 7%; text-align:center;">S.No</th>
													<th class="table_heading textOverflowHidden" style="width: 35%; text-align:left;">Description </th>
													<th class="table_heading alignRight" style="width: 11%;">HSN</th>
													<th class="table_heading alignRight" style="width: 5%;">PCS</th>
													<th class="table_heading alignRight" style="width: 10%;">GRS.WT</th>
													<th class="table_heading alignRight" style="width: 10%;">NET.WT</th>
													<!-- <th class="table_heading alignRight" style="width: 6%;">V.A</th>
													<th class="table_heading alignRight" style="width: 6%;">MC</th> -->
													<td></td>
													<td></td>
													<th class="table_heading alignRight" style="width: 11%;">Rate</th>
													<th class="table_heading alignRight" style="width: 11%;">Amount</th>
												</tr>
											</thead>
											<tr>
												<td colspan="10">
													<hr class="return_dashed">
												</td>
											</tr>


											<!--<tbody>-->
											<tr style="font-weight:bold; text-transform: uppercase;">
												<td colspan="9">Exchange (Refer sale bill no : <?php
																								$ref_no_sale = $this->ret_billing_model->get_bill_no_format_detail($est_other_item['return_details'][0]['ret_bill_id']);
																								echo $ref_no_sale; ?>)</td>
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

											if ($billing['tot_bill_amount'] > 0) {
												$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_bill_amount']);
											} else {

												$amt_in_words   = $this->ret_billing_model->no_to_words(-1 * $billing['tot_bill_amount']);
											}

											$wastge_wt = 0;
											foreach ($est_other_item['return_details'] as $items) {
												$esti_return_emp = $items['esti_emp_name'];
												$esti_return_id = $items['esti_emp_id'];

												$mc = 0;
												$wastge_wt = 0;
												$pieces             += $items['piece'];
												$gross_wt           += $items['gross_wt'];
												$net_wt             += $items['net_wt'];
												$discount           += $items['discount'];
												$total_sgst         += $items['total_sgst'];
												$total_igst         += $items['total_igst'];
												$total_cgst         += $items['total_cgst'];
												$item_cost   = $items['item_cost'] - $items['item_total_tax'];
												$return_item_cost   += $items['item_cost'] - $items['item_total_tax'];
												$tax_percentage     = $items['tax_percentage'] / 2;

												if ($items['calculation_based_on'] == 0) {
													$wastge_wt = ($items['gross_wt'] * ($items['wastage_percent'] / 100));
													$mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * $items['piece']));
												} else if ($items['calculation_based_on'] == 1) {
													$wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
													$mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['net_wt']) : ($items['mc_value'] * $items['piece']));
												} else if ($items['calculation_based_on'] == 2) {
													$wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
													$mc = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * $items['piece']));
												}
												if ($items['wastage_discount'] > 0) {
													$discount_weight = ($items['wastage_discount'] / $items['rate_per_grm']);

													$wastge_wt = $wastge_wt - $discount_weight;

												} else {
													$wastge_wt = $wastge_wt;
												}


												if ($items['mc_discount'] > 0) {
													$mc        = $mc - $items['mc_discount'];
												}
												$wastage_value = round($wastge_wt * $items['rate_per_grm'], 2);
											?>

												<tr>
													<td style="width: 5%" class="alignCenter"><?php echo $i; ?></td>
													<td style="width: 26%;" class="textOverflowHidden"><?php echo $items['product_name']; ?></td>
													<td style="width: 15%" class="alignRight"><?php echo $items['hsn_code']; ?></td>
													<td style="width: 9%" class="alignRight"><?php echo $items['piece']; ?></td>
													<td style="width: 15%" class="alignRight"><?php echo $items['gross_wt']; ?></td>
													<td style="width: 15%" class="alignRight"><?php echo $items['net_wt']; ?></td>
													<td colspan="2"></td>
													<!-- <td style="width: 15%" class="alignRight"><?php echo ($wastge_wt > 0 ? number_format($wastge_wt, 3, '.', '') : ''); ?></td>
													<td style="width: 15%" class="alignRight"><?php echo $mc > 0 ? moneyFormatIndia(number_format($mc, 2, '.', '')) : ''; ?></td> -->
													<!-- <td class="alignRight"><?php
																				if ($items['net_wt'] >= 1) {
																					$va_g = round(($wastage_value + $mc) / $items['net_wt'], 0);
																					echo $va_g > 0 ? $va_g . "/g" : '';
																				} else {
																					echo round($wastage_value + $mc, 0);
																				}
																				?> 
														</td> -->
													<td style="width: 15%" class="alignRight"><?php echo moneyFormatIndia($items['rate_per_grm']); ?></td>
													<td style="width: 15%" class="alignRight"><?php echo moneyFormatIndia(number_format($item_cost, 2, '.', '')); ?></td>
												</tr>
												<?php $i++;
												if (count($items['rtn_stone_details']) > 0) {
													foreach ($items['rtn_stone_details'] as $stoneItems) {

												?>
														<tr class="stones">
															<td></td>
															<?php
															$uom = ($stoneItems['stone_type'] == 1) ? 'CT' : 'ST';
															?>
															<?php if ($stoneItems['stone_cal_type'] == 1) { ?>
																<td colspan="4" class='textOverflowHidden stoneData'><?php echo $stoneItems['pieces'] . ' Pcs ' . $stoneItems['stone_name'] . ($stoneItems['amount'] > 0 ? ' ' . number_format($stoneItems['wt'], 3, '.', '') . ' ' . $uom . 'x' . moneyFormatIndia(number_format($stoneItems['rate_per_gram'], 2, '.', '')) . '=' . moneyFormatIndia(number_format((float)($stoneItems['amount']), 2, '.', '')) : ''); ?></td>
															<?php } else if ($stoneItems['stone_cal_type'] == 2) { ?>
																<td colspan="4" class='textOverflowHidden stoneData'><?php echo number_format($stoneItems['wt'], 3, '.', '') . ' ' . $uom . ' ' . $stoneItems['stone_name'] . ($stoneItems['amount'] > 0 ? ' ' . $stoneItems['pieces'] . 'Pcs ' . 'x' . $stoneItems['rate_per_gram'] . '=' . moneyFormatIndia(number_format((float)($stoneItems['amount']), 2, '.', '')) : ''); ?></td>
															<?php } ?>
															<td class="alignRight stoneData"></td>
															<td class="alignRight stoneData"></td>
															<td class="alignRight stoneData"></td>
															<td class="alignRight"></td>
															<td class="alignRight stoneData"></td>
														</tr>
											<?php }
												}
											} ?>
											<tr>
												<td colspan="10">
													<hr class="return_dashed">
												</td>
											</tr>
											<!--</tbody> -->
											<!--<tfoot>-->
											<tr style="font-weight: bold">
												<td>Total</td>
												<td></td>
												<td></td>
												<td class="alignRight"><?php echo $pieces ?></td>
												<td class="alignRight"><?php echo number_format($gross_wt, 3, '.', '') ?></td>
												<td class="alignRight"><?php echo number_format($net_wt, 3, '.', '') ?></td>
												<td></td>
												<td></td>
												<td></td>
												<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$return_item_cost, '2', '.', '')); ?></td>
											</tr>
											<tr>
												<td colspan="10">
													<hr class="return_dashed">
												</td>
											</tr>

											<?php
											if (sizeof($est_other_item['old_matel_details']) > 0) {
												if ($total_sgst > 0) { ?>
													<tr>
														<td colspan="5"></td>
														<td class="alignRight">SGST <?php echo  $tax_percentage . '%'; ?></td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_sgst, 2, '.', '')); ?></td>
													</tr>

												<?php } ?>
												<?php if ($total_cgst > 0) { ?>
													<tr>
														<td colspan="5"></td>
														<td class="alignRight">CGST <?php echo  $tax_percentage . '%'; ?></td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_cgst, 2, '.', '')); ?></td>
													</tr>

												<?php } ?>
												<?php $return_round_off_amt = moneyFormatIndia(number_format($billing['return_round_off_amt'], 2, '.', ''));
												if ($return_round_off_amt != 0) { ?>
													<tr>
														<td colspan="5"></td>
														<td class="alignRight">Round Off</td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($return_round_off_amt, 2, '.', '')); ?></td>
													</tr>

												<?php } ?>
												<?php if ($total_return > 0) { ?>
													<tr>
														<td colspan="9"></td>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="font-weight: bold">
														<td colspan="5"></td>
														<td class="alignRight">TOTAL</td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return - $billing['credit_due_amt'] + $return_round_off_amt, 2, '.', '')); ?></td>
													</tr>
													<tr>
														<td colspan="9"></td>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
											<?php }
											} ?>
											<?php
											if ($pur_total_amt == 0) {
												if ($billing['credit_due_amt'] != 0) { ?>

													<tr>
														<td colspan="5"></td>
														<td class="alignRight">Return Amount</td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return, 2, '.', '')); ?></td>
													</tr>

													<tr>
														<td colspan="5"></td>
														<td class="alignRight">Credit Due Amt</td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo $billing['credit_due_amt'] ?></td>
													</tr>
												<?php } ?>

												<?php if ($total_sgst > 0) { ?>
													<tr>
														<td colspan="5"></td>
														<td class="alignRight">SGST <?php echo  $tax_percentage . '%'; ?></td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo $total_sgst; ?></td>
													</tr>

												<?php } ?>
												<?php if ($total_cgst > 0) { ?>
													<tr>
														<td colspan="5"></td>
														<td class="alignRight">CGST <?php echo  $tax_percentage . '%'; ?></td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo $total_cgst; ?></td>
													</tr>

												<?php } ?>

												<?php $return_round_off_amt = moneyFormatIndia(number_format($billing['return_round_off_amt'], 2, '.', ''));
												if ($return_round_off_amt != 0) { ?>
													<tr>
														<td colspan="5"></td>
														<td class="alignRight">Round Off</td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($return_round_off_amt, 2, '.', '')); ?></td>
													</tr>

												<?php } ?>

												<?php if ($total_return > 0) { ?>
													<tr>
														<td colspan="9"></td>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="font-weight: bold">
														<td colspan="5"></td>
														<td class="alignRight">TOTAL</td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return - $billing['credit_due_amt'] + $return_round_off_amt, 2, '.', '')); ?></td>
													</tr>
													<tr>
														<td colspan="9"></td>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
												<?php } ?>

												<?php if ($tot_sales_amt + $billing['handling_charges'] > 0) { ?>
													<tr>
														<td colspan="5"></td>
														<td class="alignRight">Sales Amount</td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($tot_sales_amt + $billing['handling_charges'] - $scheme_benefit, 2, '.', '')); ?></td>
													</tr>
												<?php } ?>

												<?php if ($total_return > 0 && $billing['bill_type'] != 7) { ?>
													<tr>
														<td colspan="5"></td>
														<td colspan="3" class="alignRight">Exchange Amount</td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return - $billing['credit_due_amt'] + $return_round_off_amt, 2, '.', '')); ?></td>
													</tr>
												<?php } ?>

												<!-- <?php if ($total_return > 0) { ?>
													<tr>
														<td colspan="6"></td>
														<td colspan="2" class="alignRight">Return Charges</td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format($billing['return_charges'], 2, '.', '')); ?></td>
													</tr>
												<?php } ?> -->



												<?php if ($tot_sales_amt > 0 && $total_return > 0) { ?>
													<tr>
														<td colspan="9"></td>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr>
														<td colspan="5"></td>
														<td class="alignRight">Net Amount</td>
														<td colspan="2"></td>
														<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
														<td class="alignRight"><?php echo moneyFormatIndia(number_format(($tot_sales_amt + $billing['handling_charges']) - $total_return + $round_off, 2, '.', '')); ?></td>
													</tr>
													<tr>
														<td colspan="9"></td>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
											<?php }
											} ?>
										</table>
									<?php } ?>

									<?php if (sizeof($est_other_item['sales_ret_trans_details']) > 0) { ?>
										<hr class="header_dashed">
										<div class="col-xs-12">
											<div class="table-responsive">
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:10px;">
														<tr>
															<td class="alignCenter" width="5%;">S.No</td>
															<td width="10%;">HSN</td>
															<td width="15%;">Description</td>
															<td width="10%;">PCS</td>
															<td width="10%;">Gwt(g)</td>
															<td class="alignRight" width="10%;">Rate</td>
															<td class="alignRight" width="5%;" style="text-align:right;">Amount</td>
														</tr>
													</thead>
													<tr>
														<td>
															<hr class="item_dashed" style="width:1350% !important;">
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
															<td class="alignCenter"><?php echo $i; ?></td>
															<td><?php echo $items['hsn_code']; ?></td>
															<td style="font-size:10px !important;"><?php echo $items['category_name']; ?></td>
															<td><?php echo $items['piece']; ?></td>
															<td><?php echo $items['gross_wt']; ?></td>
															<td class="alignRight"><?php echo $items['rate_per_grm']; ?></td>
															<td class="alignRight"><?php echo number_format($item_taxable, 2, '.', ''); ?></td>
														</tr>
													<?php $i++;
													} ?>
													<!--</tbody> -->
													<tr>
														<td>
															<hr class="item_dashed" style="width:1350% !important;">
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
															<hr class="item_dashed" style="width:1350% !important;">
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
															<td width="">SUB TOTAL</td>
															<td width="1%;" style="font-size:15px !important;">&#8377;</td>
															<td style="text-align:right;"><?php echo number_format((float)($taxable_amt), 2, '.', ''); ?></td>
														</tr>
													<?php } ?>

													<?php if ($total_sgst > 0) { ?>
														<tr>
															<td colspan="4">
															</td>
															<td width="4">SGST</td>
															<td width="1%;"><?php echo ($est_other_item['sales_ret_trans_details'][0]['tax_percentage'] / 2) . '%' ?></td>
															<td style="text-align:right;"><?php echo $total_sgst; ?></td>
														</tr>
													<?php } ?>

													<?php if ($total_cgst > 0) { ?>
														<tr>
															<td colspan="4"></td>
															<td width="">CGST <?php echo ($est_other_item['sales_ret_trans_details'][0]['tax_percentage'] / 2) . '%' ?></td>
															<td width="1%;" style="font-size:15px !important;">&#8377;</td>
															<td style="text-align:right;"><?php echo $total_cgst; ?></td>
														</tr>
													<?php } ?>

													<?php if ($total_igst > 0) { ?>
														<tr>
															<td colspan="4"></td>
															<td width="">IGST <?php echo ($est_other_item['sales_ret_trans_details'][0]['tax_percentage']) . '%' ?></td>
															<td width="1%;" style="font-size:15px !important;">&#8377;</td>
															<td style="text-align:right;"><?php echo $total_igst; ?></td>
														</tr>
													<?php } ?>

													<?php if ($billing['round_off_amt'] > 0) { ?>
														<tr>
															<td colspan="4"></td>
															<td width="">Round Off</td>
															<td width="1%;" style="font-size:15px !important;">&#8377;</td>
															<td style="text-align:right;"><?php echo number_format($billing['round_off_amt'], 2, '.', ''); ?></td>

														</tr>

													<?php } ?>

													<tr>
														<td colspan="4"></td>
														<td>
															<hr class="total_dashed" style="width:250% !important;">
														</td>
														<td></td>
														<td></td>
													</tr>


													<tr>
														<td colspan="4"></td>
														<th width="" style="font-size: 12px;">Total</th>
														<td width="1%;" style="font-size:15px !important;" style="font-size:15px !important;">&#8377;</td>
														<th style="text-align:right;font-size: 12px;"><?php echo number_format(($billing['tot_bill_amount'] < 0 ? ($billing['tot_bill_amount'] * -1) : $billing['tot_bill_amount']), 2, '.', ''); ?></th>

													</tr>

													<tr>
														<td colspan="3"></td>
														<td></td>
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
										$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_amt_received']);
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
										<div align="center">
											<label><b>Order No.<?php echo $order_no; ?></b></label>
										</div>
										<div class="col-xs-12">
											<div class="table-responsive">
												<table id="pp" class="table text-center">
													<!--	<thead> -->
													<tr>
														<td colspan="9">
															<hr class="old_metal_dashed">
														</td>
													</tr>
												</table>
												<table id="pp" class="table text-center">
													<!--	<thead> -->

													<tr style="text-transform:uppercase;font-size:12px;">
														<th class="table_heading alignCenter" style="width: 5%; text-align:center;">S.No</th>
														<th class="table_heading " style="width: 35%; text-align:left;">Description </th>
														<th class="table_heading alignRight" style="width: 10%;">HSN</th>
														<th class="table_heading alignRight" style="width: 8%;">PCS</th>
														<th class="table_heading alignRight" style="width: 10%;">GRS.WT</th>
														<th class="table_heading alignRight" style="width: 10%;">NET.WT</th>
														<!-- <th class="table_heading alignRight" style="width: 10%;">V.A</th> -->
														<th></th>
														<th class="table_heading alignRight" style="width: 12%;">Rate</th>
														<th class="table_heading alignRight" style="width: 12%;">Amount</th>
													</tr>
													<tr>
														<td colspan="9">
															<hr class="old_metal_dashed">
														</td>
													</tr>
													<!--</thead>
													<tbody>-->
													<?php
													$i = 1;
													$weight = 0;
													$od_pcs = 0;
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
															<td class="alignCenter"><?php echo $i; ?></td>
															<td class='textOverflowHidden '><?php echo $items['product_name']; ?><?php echo " -" . $items['sub_design_name']; ?></td>
															<td class="alignRight"><?php echo $items['hsn_code']; ?></td>
															<!-- <td><?php echo $items['purname']; ?></td> -->
															<td class="alignRight"><?php echo $items['totalitems']; ?></td>
															<td class="alignRight"><?php echo $items['weight'] . ($items['pro_uom'] != '' ? '-' . $items['pro_uom'] : ''); ?></td>
															<td class="alignRight"><?php echo (!empty($items['net_wt']) ? $items['net_wt'] : '') . ($items['pro_uom'] != '' ? '-' . $items['pro_uom'] : ''); ?></td>
															<!-- <td class="alignRight"><?php
																					if ($items['net_wt'] >= 1) {
																						$va_g = round(($od_wastage_amt + $items['mc']) / $items['net_wt'], 0);
																						echo $va_g > 0 ? $va_g . "/g" : '';
																					} else {
																						echo round($od_wastage_amt + $items['mc'], 0);
																					}
																					?> </td> -->
															<td></td>
															<td class="alignRight"><?php echo $items['rate_per_gram']; ?></td>
															<!-- <td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($od_wastage_amt), 2, '.', '')); ?></td> -->
															<td class="alignRight"><?php echo moneyFormatIndia($rate_without_gst - $stone_amount, 2, '.', ''); ?></td>
														</tr>
														<?php

														if (count($items['stones']) > 0) {
															foreach ($items['stones'] as $stoneItems) {

														?>
																<tr class="stones">
																	<td></td>
																	<?php if ($stoneItems['stone_cal_type'] == 1) { ?>
																		<td colspan="4" class='textOverflowHidden stoneData'><?php echo $stoneItems['pieces'] . ' Pcs ' . $stoneItems['stone_name'] . ($stoneItems['amount'] > 0 ? ' ' . number_format($stoneItems['wt'], 3, '.', '') . ' ' . $stoneItems['uom_short_code'] . 'x' . moneyFormatIndia(($stoneItems['rate_per_gram']), 2, '.', '') . '=' . moneyFormatIndia(number_format((float)($stoneItems['amount']), 2, '.', '')) : ''); ?></td>
																	<?php } else if ($stoneItems['stone_cal_type'] == 2) { ?>
																		<td colspan="4" class='textOverflowHidden stoneData'><?php echo number_format($stoneItems['wt'], 3, '.', '') . ' ' . $stoneItems['uom_short_code'] . ' ' . $stoneItems['stone_name'] . ($stoneItems['amount'] > 0 ? ' ' . $stoneItems['pieces'] . 'Pcs ' . 'x' . $stoneItems['rate_per_gram'] . '=' . moneyFormatIndia(number_format((float)($stoneItems['amount']), 2, '.', '')) : ''); ?></td>
																	<?php } ?>
																	<td class="alignRight stoneData"></td>
																	<td class="alignRight stoneData"></td>
																	<td class="alignRight stoneData"></td>
																	<td class="alignRight stoneData"></td>

																</tr>
														<?php }
														}															?>
													<?php $i++;
													}
													?>

													<!--</tbody> -->
													<tr>
														<td colspan="9">
															<hr class="old_metal_dashed">
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
														<td colspan="9">
															<hr class="old_metal_dashed">
														</td>
													</tr>

													<?php if ($od_total_price_without_gst > 0) { ?>
														<tr>
															<td colspan="5"></td>
															<td colspan="2" class="alignRight">SUB TOTAL</td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($od_total_price_without_gst), 2, '.', '')); ?></td>
														</tr>
													<?php } ?>


													<?php if ($od_total_cgst > 0 || $od_total_sgst > 0 && $billing['is_eda'] == 1) { ?>

														<tr>
															<td colspan="5"></td>
															<td colspan="2" class="alignRight">CGST <?php echo ($items['tax_percentage'] / 2) . '%'; ?></td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td class="alignRight"><?php echo moneyFormatIndia($od_total_cgst, 2, '.', ''); ?></td>
														</tr>

														<tr>
															<td colspan="5"></td>
															<td colspan="2" class="alignRight">SGST <?php echo ($items['tax_percentage'] / 2) . '%'; ?></td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td class="alignRight"><?php echo moneyFormatIndia($od_total_sgst, 2, '.', ''); ?></td>
														</tr>

													<?php } ?>

													<?php if ($od_total_igst > 0 && $billing['is_eda'] == 1) { ?>

														<tr>
															<td colspan="5"></td>
															<td colspan="2" class="alignRight">IGST</td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td class="alignRight"><?php echo moneyFormatIndia($od_total_igst, 2, '.', ''); ?></td>
														</tr>

													<?php } ?>

													<?php if ($od_total_price > 0) { ?>
														<tr>
															<td colspan="8"></td>
															<td>
																<hr class="old_metal_dashed">
															</td>
														</tr>
														<tr class="table_heading">
															<td colspan="5"></td>
															<td colspan="2" class="alignRight">Approx Total Amount</td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td class="alignRight"><?php echo moneyFormatIndia($od_total_price, 2, '.', ''); ?></td>
														</tr>
														<tr>
															<td colspan="8"></td>
															<td>
																<hr class="old_metal_dashed">
															</td>
														</tr>

													<?php } ?>





												</table> <br>

											</div>

										</div>
									</div>
								</div><br>
							<?php } ?>
							<div style="margin-left:10px; font-size:14px !important;">

								<?php
								$hasNonEmptyDescription = false; // Initialize a flag variable

								foreach ($est_other_item['order_details'] as $items) {
									if ($items['description'] != '') {
										$hasNonEmptyDescription = true;
										break; // Break out of the loop if any description is not empty
									}
								}
								if ($hasNonEmptyDescription) {
								?>
									<b>Order Details</b> <br>
									<?php }
								$j = 1;

								foreach ($est_other_item['order_details'] as $items) {

									if ($items['description'] != '') {
									?>
										<label>
											<?php echo $j . '.' . $items['description'] . '.'; ?>
										</label><br>

								<?php


									}
									$j++;
								}; ?>
							</div>

							<?php if (sizeof($est_other_item['sales_trasnfer_details']) > 0) { ?>
								<hr class="header_dashed">
								<div class="col-xs-12">
									<div class="table-responsive">
										<table id="pp" class="table text-center">
											<thead style="text-transform:uppercase;font-size:10px; font-weight:bold;">
												<tr>
													<td width="5%;" class="alignRight">S.No</td>
													<td width="10%;" class="alignCenter">HSN</td>
													<td width="25%;">Description</td>
													<td width="10%;">PCS</td>
													<td width="10%;">Gwt(g)</td>
													<td class="alignRight" width="10%;">Rate</td>
													<td class="alignRight" width="25%;" style="text-align:right;">Amount</td>
												</tr>
											</thead>
											<tr>
												<td colspan="7">
													<hr class="item_dashed">
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
													<td style="text-align:right;"><?php echo $i; ?></td>
													<td><?php echo $items['hsn_code']; ?></td>
													<td style="font-size:10px !important;"><?php echo $items['category_name']; ?></td>
													<td><?php echo $items['piece']; ?></td>
													<td><?php echo $items['gross_wt']; ?></td>
													<td style="text-align:right;"><?php echo moneyFormatIndia($items['rate_per_grm']); ?></td>
													<td style="text-align:right;"><?php echo moneyFormatIndia(number_format($item_taxable, 2, '.', '')); ?></td>
												</tr>
											<?php $i++;
											} ?>
											<!--</tbody> -->
											<!-- <tr>
												<td>
													<hr class="item_dashed" style="width:1350% !important;">
												</td>
											</tr> -->
											<tr>
												<td colspan="7">
													<hr class="item_dashed">
												</td>
											</tr>
											<tr class="total">
												<td></td>
												<td></td>
												<td>Total</td>
												<td><?php echo $pieces; ?></td>
												<td><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
												<td></td>
												<td style="text-align:right;"><?php echo moneyFormatIndia(number_format((float)($taxable_amt + $bill_discount), 2, '.', '')); ?></td>
											</tr>
											<!-- <tr>
												<td>
													<hr class="item_dashed" style="width:1350% !important;">
												</td>
											</tr> -->
											<tr>
												<td colspan="7">
													<hr class="item_dashed">
												</td>
											</tr>
											<?php if ($bill_discount > 0) { ?>
												<tr>
													<td colspan="3"></td>
													<td width="">LESS DISC</td>
													<td style="text-align:right; font-size:15px !important;">&#8377;</td>
													<td style="text-align:right;"><?php echo moneyFormatIndia(number_format($bill_discount, 2, '.', '')); ?></td>
												</tr>
											<?php } ?>
											<?php if ($taxable_amt > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="">SUB TOTAL</td>
													<td style="font-size:15px !important; text-align:right;">&#8377;</td>
													<td style="text-align:right;"><?php echo moneyFormatIndia(number_format((float)($taxable_amt), 2, '.', '')); ?></td>
												</tr>
											<?php } ?>

											<?php if ($total_sgst > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="4">SGST (<?php echo ($est_other_item['sales_trasnfer_details'][0]['tax_percentage'] / 2) . '%' ?>)</td>
													<td style="text-align:right; font-size:15px !important;">&#8377;</td>
													<td style="text-align:right;"><?php echo moneyFormatIndia(number_format($total_sgst, 2, '.', '')); ?></td>
												</tr>
											<?php } ?>

											<?php if ($total_cgst > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="">CGST (<?php echo ($est_other_item['sales_trasnfer_details'][0]['tax_percentage'] / 2) . '%' ?>)</td>
													<td style="text-align:right; font-size:15px !important;">&#8377;</td>
													<td style="text-align:right;"><?php echo moneyFormatIndia(number_format($total_cgst, 2, '.', '')); ?></td>
												</tr>
											<?php } ?>

											<?php if ($total_igst > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="">IGST (<?php echo ($est_other_item['sales_trasnfer_details'][0]['tax_percentage']) . '%' ?>)</td>
													<td style="text-align:right; font-size:15px !important;">&#8377;</td>
													<td style="text-align:right;"><?php echo moneyFormatIndia(number_format($total_igst, 2, '.', '')); ?></td>
												</tr>
											<?php } ?>

											<?php if ($billing['round_off_amt'] > 0) { ?>
												<tr>
													<td colspan="4"></td>
													<td width="">Round Off</td>
													<td width="1%;" style="font-size:15px !important;">&#8377;</td>
													<td style="text-align:right;"><?php echo moneyFormatIndia(number_format($billing['round_off_amt'], 2, '.', '')); ?></td>

												</tr>

											<?php } ?>

											<tr>
												<td colspan="4"></td>
												<td colspan="3">
													<hr class="total_dashed">
												</td>
											</tr>






											<?php if ($billing['handling_charges'] > 0) { ?>
												<tr>
													<td colspan="3"></td>
													<td width="">H.C</td>
													<td width="1%;" style="font-size:15px !important;">&#8377;</td>
													<td style="text-align:right;"><?php echo moneyFormatIndia(number_format($billing['handling_charges'], 2, '.', '')); ?></td>
												</tr>
											<?php } ?>


											<tr style="font-weight:bold;">
												<td colspan="4"></td>
												<td>Total</td>
												<td style="font-size:15px !important; text-align:right;">&#8377;</td>
												<td style="text-align:right; font-size: 12px; "><?php echo moneyFormatIndia(number_format($billing['tot_bill_amount'], 2, '.', '')); ?></td>

											</tr>


										</table>
									</div>
								</div>
							</div><br>
						<?php } ?>

						<?php if (sizeof($est_other_item['old_matel_details']) > 0) { ?>

							<?php if (sizeof($est_other_item['old_matel_details']) > 0 && sizeof($est_other_item['item_details']) == 0 && $billing['bill_type'] != 8) { ?>
								<div for="title" style="text-align:center;font-weight:bold;">PURCHASE BILL</div> <br>

							<?php	} ?>
							<?php
							if (sizeof($est_other_item['item_details']) != 0 || $billing['bill_type'] == 5) {
								$pur_inv_no =  $this->ret_billing_model->get_bill_no_format_detail($billing['bill_id'], 'p');
							?>
								<span style="font-size:12px !important; font-weight:bold; text-transform: uppercase;">Purchase Invoice No : <?php echo $pur_inv_no; ?></span>
				

							<?php }
							?>

							<table id="pp" class="table text-center">

								<tr>
									<td colspan="9">
										<hr class="old_metal_dashed">
									</td>
								</tr>



								<tr style="text-transform:uppercase;font-size:12px;">
									<th class="table_heading alignCenter" style="width: 5%; text-align:center;">S.No</th>
									<th colspan="2" class="table_heading textOverflowHidden" style="width: 35%; text-align:left;">Description </th>
									<th class="table_heading alignRight" style="width: 8%;">GRSWT</th>
									<th class="table_heading alignRight" style="width: 10%;">STN LESS</th>
									<th class="table_heading alignRight" style="width: 10%;">MTL LESS</th>
									<th class="table_heading alignRight" style="width: 10%;">NETWT</th>
									<th class="table_heading alignRight" style="width: 12%;">RATE</th>
									<th class="table_heading alignRight" style="width: 12%;">Amount</th>
								</tr>


								<tr>
									<td colspan="9">
										<hr class="old_metal_dashed">
									</td>
								</tr>


								<!--<tbody>-->
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
								if ($billing['tot_bill_amount'] < 0) {
									$amt_in_words   = $this->ret_billing_model->no_to_words(-1 * $billing['tot_bill_amount']);
								} else {
									$amt_in_words   = $this->ret_billing_model->no_to_words($billing['tot_bill_amount']);
								}
								foreach ($est_other_item['old_matel_details'] as $items) {
									$esti_purchase_emp = $items['esti_emp_name'];
									$esti_purchase_id = $items['esti_emp_id'];

									$gross_wt += $items['gross_wt'];
									$net_wt += ($items['net_wt']);
								?>
									<tr>
										<td class="alignCenter" style="width: 5%"><?php echo $i; ?></td>
										<td style="font-size:10px; width:35%" colspan="2" class='textOverflowHidden'><?php echo $items['old_metal_type']; ?></td>
										<td style="width: 24%" style="width: 8%" class="alignRight"><?php echo number_format($items['gross_wt'], 3, '.', ''); ?></td>
										<td style="width: 10%" class="alignRight"><?php echo $items['stone_wt'] > 0 ? $items['stone_wt'] : ''; ?></td>
										<td style="width: 10%" class="alignRight"><?php echo ($items['wast_wt'] + $items['dust_wt'] + $items['less_wt'] > 0 ? number_format(($items['wast_wt'] + $items['dust_wt'] + $items['less_wt']), 3, '.', '') : ''); ?></td>
										<td style="width: 10%" class="alignRight"><?php echo number_format($items['net_wt'], 3, '.', ''); ?></td>
										<td style="width: 12%" class="alignRight"><?php echo moneyFormatIndia($items['rate_per_gram']); ?></td>
										<td style="width: 12%" class="alignRight"><?php echo moneyFormatIndia(number_format($items['amount'], 2, '.', '')); ?></td>
									</tr>
								<?php $i++;
								} ?>
								<!-- stone details-->
								<?php

								if (count($items['stone_details']) > 0) {
									foreach ($items['stone_details'] as $stoneItems) {

								?>
										<tr class="stones">
											<td></td>
											<?php
												$uom = ($stoneItems['stone_type'] == 1) ? 'CT' : 'ST';
											?>
											<?php if ($stoneItems['stone_cal_type'] == 1) { ?>
												<td colspan="4" class='textOverflowHidden stoneData'><?php echo $stoneItems['pieces'] . ' Pcs ' . $stoneItems['stone_name'] . ($stoneItems['price'] > 0 ? ' ' . number_format($stoneItems['wt'], 3, '.', '') . ' ' . $uom . ' x ' . moneyFormatIndia(($stoneItems['rate_per_gram']), 2, '.', '') . '=' . moneyFormatIndia(number_format((float)($stoneItems['price']), 2, '.', '')) : ''); ?></td>
											<?php } else if ($stoneItems['stone_cal_type'] == 2) { ?>
												<td colspan="4" class='textOverflowHidden stoneData'><?php echo number_format($stoneItems['wt'], 3, '.', '') . ' ' . $uom . ' ' . $stoneItems['stone_name'] . ($stoneItems['price'] > 0 ? ' ' . $stoneItems['pieces'] . 'Pcs ' . 'x' . $stoneItems['rate_per_gram'] . '=' . moneyFormatIndia(number_format((float)($stoneItems['price']), 2, '.', '')) : ''); ?></td>
											<?php } ?>
											<td class="alignRight stoneData"></td>
											<td class="alignRight stoneData"></td>
											<td class="alignRight"></td>
											<td class="alignRight stoneData"></td>
										</tr>
								<?php }
								} ?>
								<!--</tbody> -->
								<tr>
									<td colspan="9">
										<hr class="old_metal_dashed">
									</td>
								</tr>
								<tr style="font-weight: bold">
									<td class="alignCenter">Total</td>
									<td></td>
									<td></td>
									<td class="alignRight"><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
									<td></td>
									<td></td>
									<td class="alignRight"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
									<td></td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$pur_total_amt, 2, '.', '')); ?></td>
								</tr>
								<tr>
									<td colspan="9">
										<hr class="old_metal_dashed">
									</td>
								</tr>
								<?php if ($tot_sales_amt + $billing['handling_charges'] > 0) { ?>
									<tr style="font-weight: bold">
										<td colspan="5"></td>
										<td class="alignRight" colspan="2">Sales Amount</td>
										<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
										<td class="alignRight"><?php echo moneyFormatIndia((number_format($tot_sales_amt + $billing['handling_charges'], 2, '.', ''))); ?></td>
									</tr>
								<?php } ?>
								<?php if ($total_return > 0 && $billing['bill_type'] != 7) { ?>
									<tr style="font-weight: bold">
										<td colspan="5"></td>
										<td class="alignRight" colspan="2">Exchange Amount</td>
										<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
										<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_return + $return_round_off_amt, 2, '.', '')); ?></td>
									</tr>
								<?php } ?>
								<?php if ($pur_total_amt > 0) { ?>
									<tr style="font-weight:bold;">
										<td colspan="4">
											<?php
											$cgst_sgst_perc = ($est_other_item['item_details'][0]['tax_percentage'] / 2);
											$cgst_sgst_amt = ($pur_total_amt * $cgst_sgst_perc / 100);
											?>
											<!--RCM : (CGST <?php echo  $cgst_sgst_perc . "% - " . number_format($cgst_sgst_amt, 2, '.', '') ?>,  SGST <?php echo  $cgst_sgst_perc . "% - " . number_format($cgst_sgst_amt, 2, '.', '') ?>)-->
										</td>
										<td class="alignRight" colspan="3">Purchase Amount</td>
										<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
										<td class="alignRight"><?php echo moneyFormatIndia(number_format($pur_total_amt, 2, '.', '')); ?></td>
									</tr>
								<?php } ?>
								<?php $round_off = moneyFormatIndia(number_format($billing['round_off_amt'], 2, '.', '')); ?>
								<?php if ($round_off != 0) { ?>
									<tr style="font-weight: bold">
										<td colspan="5"></td>
										<td class="alignRight" colspan="2">Round Off</td>
										<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
										<td class="alignRight"><?php echo $round_off ?></td>
									</tr>
								<?php } ?>
								<tr>
									<td colspan="8"></td>
									<td>
										<hr class="item_dashed">
									</td>
								</tr>
								<?php if ($tot_sales_amt > 0) { ?>

									<tr style="font-weight: bold">
										<td colspan="5"></td>
										<td class="alignRight" colspan="2">Net Amount</td>
										<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
										<td class="alignRight"><?php echo moneyFormatIndia(number_format(($tot_sales_amt + $billing['handling_charges']) - $total_return - $pur_total_amt + $round_off - $return_round_off_amt, 2, '.', '')); ?></td>
									</tr>
									<tr>
										<td colspan="8"></td>
										<td>
											<hr class="item_dashed">
										</td>
									</tr>
								<?php } ?>
							</table>
						<?php } ?>

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
											<tr style="text-transform:uppercase;">
												<td>S.No</td>
												<td>Description</td>
												<td>Repair WT(g)</td>
												<td>Completed Wt(g)</td>
												<td class="alignRight">Amount</td>
											</tr>
											<tr>
												<td colspan="5">
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
													<td class="alignRight"><?php echo moneyFormatIndia(number_format($items['rate'] - $items['repair_tot_tax'], 2, '.', '')); ?>
													</td>
												</tr>

											<?php $i++;
											} ?>
											<!--</tbody> -->
											<tr>
												<td colspan="5">
													<hr class="old_metal_dashed">
												</td>
											</tr>
											<tr>
												<td></td>
												<th>Total</th>
												<td><?php echo number_format($tot_order_wt, 3, '.', ''); ?></td>
												<td><?php echo number_format($tot_completed_wt, 3, '.', ''); ?></td>
												<td class="alignRight"><?php echo moneyFormatIndia(number_format($taxable_amt, 2, '.', '')); ?></td>
											</tr>
											<tr>
												<td colspan="5">
													<hr class="old_metal_dashed">
												</td>
											</tr>

											<table class="table" style="width:100%">
												<thead>
													<tr>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>
														<th></th>


													</tr>
												</thead>
												<tbody> <?php if ($tot_repair_amt > 0) { ?>
														<tr>
															<td colspan="7"></td>
															<td class="alignRight" width="">SUB TOTAL</td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td class="alignRight"><?php echo number_format($taxable_amt, 2, '.', ''); ?></td>
														</tr>
													<?php } ?>

													<?php if ($cgst > 0 || $sgst > 0) { ?>
														<tr>
															<td colspan="7"></td>
															<td class="alignRight" width="">CGST<?= ' ( '.$items['repair_percent'] /2 . '% )'  ?></td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td class="alignRight"><?php echo number_format($cgst, 2, '.', ''); ?></td>
														</tr>
														<tr>
															<td colspan="7"></td>
															<td class="alignRight" width="">SGST<?= ' ( '.$items['repair_percent'] /2 . '% )'  ?></td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td class="alignRight"><?php echo number_format($sgst, 2, '.', ''); ?></td>
														</tr>
													<?php } ?>
													<?php if ($igst > 0) { ?>
														<tr>
															<td class="alignRight" colspan="7"></td>
															<td class="alignRight" width="">IGST<?= '( '.$items['repair_percent'].' )%' ?></td>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<td><?php echo number_format($igst, 2, '.', ''); ?></td>
														</tr>
													<?php } ?>

													<?php if ($tot_repair_amt > 0) { ?>

														<tr>
															<td colspan="7"></td>
															<td colspan="3">
																<hr class="total_dashed">
															</td>
														</tr>

														<tr>
															<td colspan="7"></td>
															<th class="alignRight" width="" style="font-size: 12px;">Total</th>
															<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
															<th class="alignRight" style="font-size: 12px;"> <?php echo number_format($tot_repair_amt, 2, '.', ''); ?></th>
														</tr>

														<tr>
															<td colspan="7"></td>
															<td colspan="3">
																<hr class="total_dashed">
															</td>
														</tr>

												<?php if($taxable_amt!='' && $tot_sales_amt!=''){?>
															<tr>
																<td colspan="6"></td>
																<td colspan="2" class="alignRight">TOTAL AMOUNT</td>
																<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																	<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)($taxable_amt +$sgst +$cgst + $tot_sales_amt), 2, '.', '')); ?></td>
															</tr>
														<?php } ?>
												</tbody>
											</table>

										<?php } ?>

										</table><br>
									</div>
								</div>
							</div><br>
						<?php } ?>

						<?php if ($billing['bill_type'] == 8) {
							$amt_in_words   = $this->ret_billing_model->no_to_words($billing['due_amount']);
						?>
							<div class="row">
								<!--<div align="center" style="text-transform:uppercase;">
												<label>Credit No : <?php echo $billing['ref_bill_id']; ?></label>
											</div>-->


								<div class="col-xs-12">
									<div class="table-responsive">
										<hr class="item_details">
										<table id="pp" class="table text-center">

											<tr>
												<td style="width: 70%; font-weight: bold">Description</td>
												<td style="width: 30%; font-weight: bold" class="alignRight">Amount</td>
											</tr>
											<tr>
												<td colspan="2">
													<hr class="item_details">
												</td>
											</tr>
											<tr>
												<td><?php echo 'Received with thanks from Mr./Ms.' . $billing['customer_name'] . ' Towards Credit Bill No : ' . $billing['ref_bill_no']; ?></td>
												<td class="alignRight"><?php echo moneyFormatIndia(number_format($billing['due_amount'], 2, '.', '')); ?></td>
											</tr>
											<tr>
												<td colspan="2">
													<hr class="item_details">
												</td>
											</tr>

											<tr>
												<td style="font-weight:bold">Opening credit value</td>
												<td style="font-weight:bold" class="alignRight"><?php echo moneyFormatIndia(number_format($billing['due_amount'], 2, '.', '')); ?></td>
											</tr>
											<tr>
												<td style="font-weight:bold">Amount Paid</td>
												<td style="font-weight:bold" class="alignRight"><?php echo moneyFormatIndia(number_format($billing['tot_amt_received'], 2, '.', '')); ?></td>
											</tr>
											<?php if ($pur_total_amt > 0) { ?>
												<tr>
													<td style="font-weight:bold">Purchase Amount</td>
													<td style="font-weight:bold" class="alignRight"><?php echo moneyFormatIndia(number_format($pur_total_amt, 2, '.', '')); ?></td>
												</tr>
											<?php } ?>
											<?php
											if ($due_amount >= 0) { ?>
												<tr>
													<td>Balance as on Today</td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format($due_amount, 2, '.', '')); ?></td>
												</tr>
											<?php } else { ?>
												<tr>
													<td>Balance as on Today</td>
													<td class="alignRight">0</td>
												</tr>
											<?php } ?>
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
												<td style="width: 30%; font-weight: bold" class="alignRight">Amount</td>
											</tr>
											<tr>
												<td colspan="3">
													<hr class="item_dashed">
												</td>
											</tr>
											<?php
											$i = 1;
											$chit_amt = 0;
											foreach ($est_other_item['chit_details'] as $chit)
												$chit_amt += $chit['utilized_amt']; { ?>
												<tr>
													<td><?php echo $i; ?></td>
													<td><?php echo $chit['scheme_acc_number']; ?></td>
													<td class="alignRight"><?php echo moneyFormatIndia($chit['utilized_amt']); ?></td>
												</tr>
											<?php $i++;
											}
											$amt_in_words   = $this->ret_billing_model->no_to_words($chit_amt);
											?>
											<tr>
												<td colspan="3">
													<hr class="item_dashed">
												</td>
											</tr>
										</table>
										<br>
									</div>
								</div>
							</div><br>
						<?php } ?>





						<table>
							<thead style="text-transform:uppercase; visibility:hidden;">
								<tr>
									<th class="table_heading alignCenter" style="width: 5%; text-align:center;"></th>
									<th colspan="2" class="table_heading textOverflowHidden" style="width:<?php echo sizeof($est_other_item['old_matel_details']) > 0 || sizeof($est_other_item['return_details']) > 0 || sizeof($est_other_item['order_details']) > 0 ? '44%' : '39%' ?>; text-align:left;"> </th>
									<!-- <th class="table_heading alignRight" style="width: 8%;"></th> -->
									<th class="table_heading alignRight" style="width: 5%;"></th>
									<th class="table_heading alignRight" style="width: 10%;"></th>
									<th class="table_heading alignRight" style="width: 10%;"></th>
									<th class="table_heading alignRight" style="width:10%;"></th>
									<th class="table_heading alignRight" style="width:13%;"></th>
									<th class="table_heading alignRight" style="width:13%;"></th>
								</tr>
							</thead>
							<?php
							$get_other_adv_details = $this->ret_billing_model->get_previous_order_details($id_customerorder, $billing['bill_id'], $billing['id_branch'], $billing['bill_created_time']);

							/*echo "<pre>";
														print_r($get_other_adv_details);
														echo "</pre>";
														exit; */

							$total_other_advance = 0;

							$total_metal_paid = 0;


							$i = 1;
							if (sizeof($get_other_adv_details) > 0) { ?>
								<tr style="font-weight: bold;">
									<td colspan="4"></td>
									<td colspan="3" class="alignRight">Order Amount</td>
									<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($od_total_price, 2, '.', '')); ?></td>
								</tr>
								<?php }
							$tot_adv_amt = 0;

							foreach ($get_other_adv_details as $oth_adv) {

								$amount_adv = 0;

								if ($oth_adv['advance_type'] == 1) {
									$amount_adv = $oth_adv['received_amount'];
									$adv_text 	= "By Advance Order.No: ";
									$total_other_advance = $total_other_advance + $amount_adv;
									$label = 'Advance';
									// $ref_no=$billing['branch_code'].'-OR-'.$oth_adv['order_adv_ref_no'];

									$ref_no = $this->ret_billing_model->get_bill_no_format_detail($oth_adv['bill_id'], '');
								} else if ($oth_adv['advance_type'] == 2) {
									$amount_adv = $oth_adv['advance_amount'];
									$adv_text 	= "By Purchase Invoice: ";
									$total_other_advance = $total_other_advance + $amount_adv;
									$label = 'Purchase';
									// $ref_no=$billing['branch_code'].'-PU-'.$oth_adv['pur_ref_no'];

									$ref_no = $this->ret_billing_model->get_bill_no_format_detail($oth_adv['bill_id'], 'p');
								}

								$rate = 0;

								$tot_adv_amt += $amount_adv;

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
								if ($amount_adv > 0) {
								?>

									<!-- <tr style="font-weight: bold;">
																<td colspan="5" ><?php echo $adv_text . $ref_no . " Dated : " . date("d-m-Y", strtotime($oth_adv['bill_date'])); ?></td>
																<td colspan="2" class="alignRight"><?php echo $label; ?> Amount</td>
																<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																<td class="alignRight"><?php echo moneyFormatIndia(number_format($amount_adv, 2, '.', '')); ?></td>
															</tr> -->

								<?php
								}

								$i++;
							}


							$balance_amt = round(($od_total_price - $total_other_advance), 2);

							$actual_weight = $first_rate > 0 ? round(($od_total_price / $first_rate), 3) : 0;

							$balance_metal = round(($actual_weight - $total_metal_paid), 3);


							if (sizeof($get_other_adv_details) > 0) { ?>
								<!-- <tr>
																<td colspan="8"></td>
																<td> <hr class="old_metal_dashed"> </td>
															</tr>
															<tr style="font-weight: bold;">
																<td colspan="3"></td>
																<td colspan="4" class="alignRight">Total</td>
																<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
																<td class="alignRight"><?php echo moneyFormatIndia($balance_amt, 2, '.', ''); ?></td>
															</tr> -->


							<?php } ?>
							<?php if ($tot_adv_amt > 0) { ?>
								<tr style="font-weight: bold;">
									<td colspan="5"></td>
									<td colspan="2" class="alignRight"> Advance Amount</td>
									<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
									<td class="alignRight"><?php echo moneyFormatIndia(number_format($tot_adv_amt, 2, '.', '')); ?></td>
								</tr>
							<?php } ?>
						</table>

						<?php if (sizeof($est_other_item['tax_details']) > 1) { ?>

							<br><br>
							<div class="col-xs-12">
								<div class="table-responsive">
									<hr class="item_dashed">
									<table id="pp" class="table text-center">
										<thead style="text-transform:uppercase;font-size:17px;">
											<tr>
												<td class="table_heading" style="width: 5%; ">S.No</td>
												<td class="table_heading" style="width: 5%;text-align:left;">HSN</td>
												<td class="table_heading" style="width: 12%; text-align: right;">Taxable Value</td>
												<td class="table_heading" style="width: 14%;text-align:center;" colspan="2">CGST </td>
												<td class="table_heading" style="width: 14% !important;text-align:center;" colspan="2">SGST </td>
												<td class="table_heading" style="width: 12%;text-align:center; " colspan="2">IGST </td>

												<td class="table_heading alignRight" style="width: 12%;">Amount</td>
											</tr>
											<tr>
												<td colspan="10">
													<hr class="item_dashed">
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
											<td colspan="10">
												<hr class="item_dashed">
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
										foreach ($est_other_item['tax_details'] as $items) {
											$item_taxable = number_format((float) $items['item_cost'] - $items['item_total_tax'], 2, '.', '');
											$total_item_taxable += number_format((float) $items['item_cost'] - $items['item_total_tax'], 2, '.', '');
											$total_cgst += $items['total_cgst'];
											$total_sgst += $items['total_sgst'];
											$total_igst += $items['total_igst'];
											$item_total_tax += $items['item_total_tax'];
										?>
											<tr>
												<td><?php echo $i; ?></td>
												<td style="text-align:left"><?php echo $items['hsn_code']; ?></td>
												<td class="alignRight"><?php echo moneyFormatIndia(number_format($item_taxable, 2, '.', '')); ?></td>
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
											<td colspan="10">
												<hr class="item_dashed">
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
						<br>
					<?php }
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
							$chq_amt = 0;


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
							$chit_adj += $chit['utilized_amt'];
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


					<?php
					if ($cash_amt > 0 || $ord_adj_amt > 0 || $chit_adj > 0 || $chq_amt > 0 || $card_amt > 0 || $upi_amt > 0 || $rtgs_amt > 0 || $imps_amt > 0 || $adv_adj > 0 || $gift_amount > 0 || $billing['advance_deposit'] > 0 || $neft_amt > 0 || $due_amount >0 || $adj_amt > 0 || $billing['adv_adj_amt'] > 0) { 


					if($billing['bill_type'] == 1 || $billing['bill_type'] == 9){
							$width1 = 17;
							$width2 = 12;
							$width3 = 12;
						}
						else if($billing['bill_type'] == 2 || $billing['bill_type'] == 4 ){
							$width1 = 10;
							$width2 = 12;
							$width3 = 12;
						}
						else if( $billing['bill_type'] == 3 || $billing['bill_type'] == 7){
							$width1 = 16;
							$width2 = 13;
							$width3 = 12;
						}
						else{
							$width1 = 10;
							$width2 = 13;
							$width3 = 13;
						} ?>
						<table id="pp" class="table" style="font-size: 12px;">

							<thead style="text-transform:uppercase; visibility:hidden;">
								<tr>
									<th class="table_heading alignCenter" style="width: 5%; text-align:center;"></th>
									<th colspan="2" class="table_heading textOverflowHidden" style="width:<?php echo sizeof($est_other_item['old_matel_details']) > 0 || sizeof($est_other_item['return_details']) > 0 || sizeof($est_other_item['order_details']) > 0 ? '44%' : '39%' ?>; text-align:left;"> </th>
									<!-- <th class="table_heading alignRight" style="width: 8%;"></th> -->
									<th class="table_heading alignRight" style="width: 5%;"></th>
									<th class="table_heading alignRight" style="width: 10%;"></th>
									<th class="table_heading alignRight" style="width: 10%;"></th>
									<th class="table_heading alignRight" style="width:<?=$width1?>%;"></th>
									<th class="table_heading alignRight" style="width:<?=$width2?>%;"></th>
									<th class="table_heading alignRight" style="width:<?=$width3?>%;"></th>
								</tr>
							</thead>


							<?php if ($chit_adj != 0 && ($billing['bill_type'] != 10)) { ?>
								<tr>
									<td colspan="5"> </td>
									<th colspan="2" class="alignRight">CHIT ADJ</th>
									<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
									<th class="alignRight"><?php echo moneyFormatIndia(number_format(round($chit_adj + $scheme_benefit), 2, '.', '')); ?></th>
								</tr>
								<tr>
									<td colspan="8"></td>
									<td>
										<hr class="item_dashed">
									</td>
								</tr>
								<tr>
									<td colspan="5"></td>
									<th colspan="2" class="alignRight">Balance Amount</th>
									<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
									<th class="alignRight"><?php echo moneyFormatIndia(number_format(round($tot_sales_amt + $billing['handling_charges'] - $pur_total_amt - $total_return + $round_off - ($chit_adj + $scheme_benefit)), 2, '.', '')); ?></th>
								</tr>
							<?php } ?>

							<!-- <?php if ($ord_adj_amt != 0 || $billing['adv_adj_amt'] != 0) { ?>
								<tr>
									<td colspan="4"></td>
									<th colspan="3" class="alignRight">ORDER ADVANCE ADJ</th>
									<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
									<th class="alignRight"><?php echo moneyFormatIndia(number_format($ord_adj_amt + $billing['adv_adj_amt'], 2, '.', '')); ?></th>
								</tr>
								<?php } ?> -->

							<!-- <?php if ($ord_adj_amt > 0) { ?>
								<tr>
									<td colspan="4"></td>
									<th colspan="3" class="alignRight">ORDER ADVANCE ADJ</th>
									<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
									<th class="alignRight"><?php echo moneyFormatIndia(number_format($ord_adj_amt, 2, '.', '')); ?></th>
								</tr>
							<?php } ?> -->
							<?php if ($billing['adv_adj_amt'] > 0) { ?>
								<tr>
									<td colspan="4"></td>
									<th colspan="3" class="alignRight">ADVANCE ADJ</th>
									<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
									<!-- <th class="alignRight"><?php echo moneyFormatIndia(number_format(($billing['adv_adj_amt'] > 0 && $adj_amt > 0 && $billing['adv_adj_amt'] != $adj_amt ) ? ($billing['adv_adj_amt'] + $adj_amt) : (($billing['adv_adj_amt'] > 0) ? $billing['adv_adj_amt'] : $adj_amt) , 2, '.', '')); ?></th> -->
									<th class="alignRight"><?php echo moneyFormatIndia(number_format($billing['adv_adj_amt'], 2, '.', '')); ?></th>
								</tr>
							<?php } ?>
							<?php if ($chq_amt > 0) { ?>
								<?php foreach ($payment['pay_details'] as $chq) {
									if ($chq['payment_mode'] == 'CHQ') { ?>
										<tr>
											<td colspan="6"><?php echo 'Chq No: <strong>' . $chq['cheque_no'] . '</strong> / <strong>' . $chq['payment_ref_number'] . '</strong> Date: <strong>' . $chq['cheque_date'] . '</strong>'; ?></td>
											<th colspan="1" class="alignRight">CHEQUE</th>
											<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
											<th class="alignRight"><?php echo moneyFormatIndia(number_format($chq['payment_amount'], 2, '.', '')); ?></th>
										</tr>
							<?php }
								}
							} ?>

							<?php if ($card_amt > 0) { ?>
								<?php foreach ($payment['pay_details'] as $cardItem) {
									if ($cardItem['payment_mode'] == 'DC' || $cardItem['payment_mode'] == 'CC') { ?>
										<tr>
											<td colspan="6">
												<?php
												$details = [
													!empty($cardItem['card_no']) ? 'Card No: <strong>' . $cardItem['card_no'] . '</strong>' : null,
													!empty($cardItem['payment_ref_number']) ? 'Appr code: <strong>' . $cardItem['payment_ref_number'] . '</strong>' : null,
													!empty($cardItem['payment_date']) ? 'Date: <strong>' . $cardItem['payment_date'] . '</strong>' : null
												];

												// Remove null values and join with ' / '
												echo implode(' / ', array_filter($details));
												?>
											</td>


											<th colspan="1" class="alignRight">CARD</th>
											<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
											<th class="alignRight"><?php echo moneyFormatIndia(number_format($cardItem['payment_amount'], 2, '.', '')); ?></th>
										</tr>
							<?php }
								}
							} ?>

							<?php if ($rtgs_amt > 0) { ?>
								<?php foreach ($payment['pay_details'] as $rtgs) {
									if ($rtgs['payment_mode'] == 'NB' && $rtgs['transfer_type'] == 'RTGS') { ?>
										<tr>
											<td colspan="6">
												<?php
												$details = [
													!empty($rtgs['device_name']) ? $rtgs['device_name'] : null,
													!empty($rtgs['payment_ref_number']) ? 'Appr code: <strong>' . $rtgs['payment_ref_number'] . '</strong>' : null,
													!empty($rtgs['net_banking_date']) ? 'Date: <strong>' . $rtgs['net_banking_date'] . '</strong>' : null
												];

												// Remove null values and join with ' / '
												echo implode(' / ', array_filter($details));
												?>
					</td>

											<th colspan="1" class="alignRight">RTGS</th>
					<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
					<th class="alignRight"><?php echo moneyFormatIndia(number_format($rtgs['payment_amount'], 2, '.', '')); ?></th>
				</tr>
	<?php }
								}
							} ?>

	<?php if ($imps_amt > 0) { ?>
		<?php foreach ($payment['pay_details'] as $imps) {
								if ($imps['payment_mode'] == 'NB' && $imps['transfer_type'] == 'IMPS') { ?>
				<tr>
										<td colspan="6">
 									   <?php 
 									       $details = [
 									           !empty($imps['device_name']) ? $imps['device_name'] : null,
 									           !empty($imps['payment_ref_number']) ? 'Appr code: <strong>' . $imps['payment_ref_number'] . '</strong>' : null,
 									           !empty($imps['net_banking_date']) ? 'Date: <strong>' . $imps['net_banking_date'] . '</strong>' : null
 									       ];
									   
 									       // Remove null values and join with ' / '
 									       echo implode(' / ', array_filter($details));
 									   ?>
									</td>

											<th colspan="1" class="alignRight">IMPS
					</th>
					<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
					<th class="alignRight"><?php echo moneyFormatIndia(number_format($imps['payment_amount'], 2, '.', '')); ?></th>
				</tr>
	<?php }
							}
						} ?>

	<?php if ($neft_amt > 0) { ?>
		<?php foreach ($payment['pay_details'] as $neft) {
								if ($neft['payment_mode'] == 'NB' && $neft['transfer_type'] == 'NEFT') { ?>
				<tr>
					<td colspan="6">
					    <?php 
					        $details = [
					            !empty($neft['device_name']) ? $neft['device_name'] : null,
					            !empty($neft['payment_ref_number']) ? 'Appr code: <strong>' . $neft['payment_ref_number'] . '</strong>' : null,
					            !empty($neft['net_banking_date']) ? 'Date: <strong>' . $neft['net_banking_date'] . '</strong>' : null
					        ];
						
					        // Remove null values and join with ' / '
					        echo implode(' / ', array_filter($details));
					    ?>
					</td>

					<th colspan="1" class="alignRight">NEFT</th>
					<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
					<th class="alignRight"><?php echo moneyFormatIndia(number_format($neft['payment_amount'], 2, '.', '')); ?></th>
				</tr>
	<?php }
							}
						} ?>


	<?php if ($upi_amt > 0) { ?>
		<?php foreach ($payment['pay_details'] as $upi) {
								if ($upi['payment_mode'] == 'NB' && $upi['transfer_type'] == 'UPI') { ?>
				<tr>
				<td colspan="6">
					    <?php 
					        $details = [
					            !empty($upi['device_name']) ? $upi['device_name'] : null,
					            !empty($upi['payment_ref_number']) ? 'Appr code: <strong>' . $upi['payment_ref_number'] . '</strong>' : null,
					            !empty($upi['net_banking_date']) ? 'Date: <strong>' . $upi['net_banking_date'] . '</strong>' : null
					        ];
					        // Remove empty values and join with ' / '
					        echo implode(' / ', array_filter($details));
					    ?>
					</td>

					<th colspan="1" class="alignRight">UPI</th>
					<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
					<th class="alignRight"><?php echo moneyFormatIndia(number_format($upi['payment_amount'], 2, '.', '')); ?></th>
				</tr>
	<?php }
							}
						} ?>




	<?php if ($due_amount > 0) { ?>
		<tr>
			<td colspan="4"></td>
			<th colspan="3" class="alignRight">DUE AMOUNT<?php echo " (".$billing['credit_due_date'].")"?></th>
			<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
			<th class="alignRight"><?php echo moneyFormatIndia(number_format($due_amount, 2, '.', '')); ?></th>
		</tr>
	<?php } ?>

	<?php if ($adv_adj > 0) { ?>
		<tr>
			<td colspan="5"></td>
			<th colspan="2" class="alignRight">ADVANCE ADJ</th>
			<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
			<th class="alignRight"><?php echo moneyFormatIndia(number_format($adv_adj, 2, '.', '')); ?></th>
		</tr>
	<?php } ?>


	<?php if ($bouns_amt != 0) { ?>
		<tr>
			<td colspan="5"></td>
			<th colspan="2" class="alignRight">BONUS</td>
			<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
			<th class="alignRight"><?php echo moneyFormatIndia(number_format($bouns_amt, 2, '.', '')); ?></th>
		</tr>
	<?php } ?>


	<?php if ($gift_amount != 0) { ?>
		<tr>
			<td colspan="5"></td>
			<th colspan="2" class="alignRight">GIFT UTILIZED</th>
			<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
			<th class="alignRight"><?php echo moneyFormatIndia(number_format($gift_amount, 2, '.', '')); ?></th>
		</tr>
	<?php } ?>

	<?php if ($billing['credit_disc_amt'] != 0) { ?>
		<tr>
			<td colspan="5"></td>
			<th colspan="2" class="alignRight">DISCOUNT</th>
			<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
			<th class="alignRight"><?php echo moneyFormatIndia(number_format($billing['credit_disc_amt'], 2, '.', '')); ?></th>
		</tr>
	<?php } ?>

	<?php if ($billing['advance_deposit'] != 0) { ?>
		<tr>
			<td colspan="5"></td>
			<th colspan="2" class="alignRight">Advance</th>
			<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
			<th class="alignRight"><?php echo moneyFormatIndia(number_format($billing['advance_deposit'], 2, '.', '')) ?></th>
		</tr>
	<?php } ?>

	<?php if ($cash_amt > 0) { ?>
		<tr>
			<td colspan="5"></td>
			<th colspan="2" class="alignRight">Cash Received</th>
			<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
			<th class="alignRight"><?php echo moneyFormatIndia(number_format($cash_amt, 2, '.', '')); ?></th>
		</tr>
	<?php } ?>
		</table>

	<?php }
	?>


	<?php
	if ($cash_amt < 0 || $chq_amt < 0  || $rtgs_amt < 0 || $imps_amt < 0 || $upi_amt < 0 || $card_amt < 0) {


if($billing['bill_type'] == 1 || $billing['bill_type'] == 9 ){
			$width1 = 17;
			$width2 = 12;
			$width3 = 11;
		}
		else if($billing['bill_type'] == 2 || $billing['bill_type'] == 4 ){
			$width1 = 16;
			$width2 = 13;
			$width3 = 13;
		}	
		else if( $billing['bill_type'] == 3){
			$width1 = 16;
			$width2 = 12;
			$width3 = 12;
		}
		else if( $billing['bill_type'] == 7){
			$width1 = 16;
			$width2 = 13;
			$width3 = 12;
		}
		else{
			$width1 = 10;
			$width2 = 13;
			$width3 = 13;
		} ?>


		<table id="pp" class="pay_mode_totals table text-center" style=" font-size: 12px;">

			<thead style="text-transform:uppercase; visibility:hidden;">
				<tr>
					<th class="table_heading alignCenter" style="width: 5%; text-align:center;"></th>
					<th colspan="2" class="table_heading textOverflowHidden" style="width:<?php echo (sizeof($est_other_item['old_matel_details']) > 0) || (sizeof($est_other_item['order_details']) > 0) || (sizeof($est_other_item['return_details']) > 0) ? '44%' : '39%' ?>; text-align:left;"> </th>
					<!-- <th class="table_heading alignRight" style="width: 8%;"></th> -->
					<th class="table_heading alignRight" style="width: 6%;"></th>
					<th class="table_heading alignRight" style="width: 10%;"></th>
					<th class="table_heading alignRight" style="width: 10%;"></th>
					<th class="table_heading alignRight" style="width:<?=$width1?>%;"></th>
					<th class="table_heading alignRight" style="width:<?=$width2?>%;"></th>
					<th class="table_heading alignRight" style="width:<?=$width3?>%;"></th>
				</tr>
			</thead>




			<?php if ($chq_amt < 0) { ?>
				<?php foreach ($payment['pay_details'] as $chq) {
					if ($chq['payment_mode'] == 'CHQ') { ?>
						<tr>
							<td colspan="6"> <?php echo 'Chq No: ' . $chq['cheque_no'] . '/  ' . $chq['payment_ref_number'] . 'Date: ' . $chq['cheque_date']; ?></td>
							<th colspan="1" class="alignRight">CHEQUE</th>
							<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
							<th class="alignRight"><?php echo moneyFormatIndia(number_format($chq['payment_amount'], 2, '.', '')); ?></th>
						</tr>
			<?php }
				}
			} ?>

			<?php if ($rtgs_amt < 0) { ?>
				<?php foreach ($payment['pay_details'] as $rtgs) {
					if ($rtgs['payment_mode'] == 'NB' && $rtgs['transfer_type'] == 'RTGS') { ?>
						<tr>
							<td colspan="6"> <?php echo $rtgs['device_name'] . '/ Appr code: ' . $rtgs['payment_ref_number'] . '/' . 'Date: ' . $rtgs['net_banking_date']; ?></td>
							<th colspan="1" class="alignRight">RTGS</th>
							<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
							<th class="alignRight"><?php echo moneyFormatIndia(number_format($rtgs['payment_amount'], 2, '.', '')); ?></th>
						</tr>
			<?php }
				}
			} ?>

			<?php if ($imps_amt < 0) { ?>
				<?php foreach ($payment['pay_details'] as $imps) {
					if ($imps['payment_mode'] == 'NB' && $imps['transfer_type'] == 'IMPS') { ?>
						<tr>
							<td colspan="6"> <?php echo $imps['device_name'] . '/ Appr code: ' . $imps['payment_ref_number'] . '/' . 'Date: ' . $imps['net_banking_date']; ?></td>
							<th colspan="1" class="alignRight">IMPS</th>
							<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
							<th class="alignRight"><?php echo moneyFormatIndia(number_format($imps['payment_amount'], 2, '.', '')); ?></th>
						</tr>
			<?php }
				}
			} ?>

			<?php if ($upi_amt < 0) { ?>
				<?php foreach ($payment['pay_details'] as $upi) {
					if ($upi['payment_mode'] == 'NB' && $upi['transfer_type'] == 'UPI') { ?>
						<tr>
							<td colspan="6"> <?php echo $upi['device_name'] . '/ Appr code: ' . $upi['payment_ref_number'] . '/' . 'Date: ' . $upi['net_banking_date']; ?></td>
							<th colspan="1" class="alignRight">UPI</th>
							<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
							<th class="alignRight"><?php echo moneyFormatIndia(number_format($upi['payment_amount'], 2, '.', '')); ?></th>
						</tr>
			<?php }
				}
			} ?>

			<?php if ($cash_amt < 0) { ?>
				<tr>
					<td colspan="5"></td>
					<th colspan="2" class="alignRight">Cash Paid</th>
					<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
					<th class="alignRight"><?php echo moneyFormatIndia(number_format(($cash_amt), 2, '.', '')); ?></th>
				</tr>
			<?php } ?>

			<?php if ($card_amt < 0) { ?>
				<?php foreach ($payment['pay_details'] as $cardItem) {
					if ($cardItem['payment_mode'] == 'DC' || $cardItem['payment_mode'] == 'CC') { ?>
						<tr>
							<td colspan="6">
								<?php
								$details = [
									!empty($cardItem['card_no']) ? 'Card No: <strong>' . $cardItem['card_no'] . '</strong>' : null,
									!empty($cardItem['payment_ref_number']) ? 'Appr code: <strong>' . $cardItem['payment_ref_number'] . '</strong>' : null,
									!empty($cardItem['payment_date']) ? 'Date: <strong>' . $cardItem['payment_date'] . '</strong>' : null
								];

								// Remove null values and join with ' / '
								echo implode(' / ', array_filter($details));
								?>
							</td>
			
			
							<th colspan="1" class="alignRight">CARD</th>
							<th class="alignRight" style="font-size:15px !important;">&#8377;</th>
							<th class="alignRight"><?php echo moneyFormatIndia(number_format($cardItem['payment_amount'], 2, '.', '')); ?></th>
						</tr>
					<?php }
				}
			} ?>

		</table>


	<?php }
	// echo 'hello';



	if (sizeof($get_other_adv_details) > 0 || $od_total_price > 0) {

		$rate_order = $this->ret_billing_model->get_order_rate($order_no, $billing['id_branch']);
		// print_r($rate_order);exit;

		foreach ($rate_order as $r) {
			$rate_new = 0;

			if ($r['rate_field'] == "goldrate_22ct") {
				$rate_new = $r['goldrate_22ct'];
			} else if ($r['rate_field'] == "goldrate_18ct") {
				$rate_new = $r['goldrate_18ct'];
			} else if ($r['rate_field'] == "silverrate_1gm") {
				$rate_new = $r['silverrate_1gm'];
			}
		}

		$first_rate = $rate_new;
		$od_balance = $balance_amt - ($total_amt + $billing['adv_adj_amt']); // Adjusted balance amount by adding general advance adjustment
		$od_metal_balance = round(($od_balance / $first_rate), 3);

	?>
		<table>
			<thead style="text-transform:uppercase; visibility:hidden;">
				<tr>
					<th class="table_heading alignCenter" style="width: 5%; text-align:center;"></th>
					<th colspan="2" class="table_heading textOverflowHidden" style="width:<?php echo sizeof($est_other_item['old_matel_details']) > 0 || sizeof($est_other_item['return_details']) > 0 || sizeof($est_other_item['order_details']) > 0 ? '44%' : '39%' ?>; text-align:left;"> </th>
					<!-- <th class="table_heading alignRight" style="width: 8%;"></th> -->
					<th class="table_heading alignRight" style="width: 5%;"></th>
					<th class="table_heading alignRight" style="width: 10%;"></th>
					<th class="table_heading alignRight" style="width: 10%;"></th>
					<th class="table_heading alignRight" style="width:10%;"></th>
					<th class="table_heading alignRight" style="width:13%;"></th>
					<th class="table_heading alignRight" style="width:13%;"></th>
				</tr>
			</thead>
			<tr>
				<td colspan="8"></td>
				<td>
					<hr class="old_metal_dashed">
				</td>
			</tr>
			<?php if ($balance_type == 1) { ?>
				<tr style="font-weight: bold;">
					<td colspan="3"></td>
					<td colspan="4" class="alignRight">Balance Wt (<?php echo moneyFormatIndia(number_format($od_balance, 2, '.', '')) . ' / ' . moneyFormatIndia($first_rate); ?>)</td>
					<td class="alignRight"></td>
					<td class="alignRight"><?php echo moneyFormatIndia(number_format($od_metal_balance, 3, '.', '')) . 'Gm'; ?></td>
				</tr>
			<?php } else { ?>
				<tr style="font-weight: bold;">
					<td colspan="3"></td>
					<td colspan="4" class="alignRight">Approx Balance Amount</td>
					<td class="alignRight" style="font-size:15px !important;">&#8377;</td>
					<td class="alignRight"><?php echo moneyFormatIndia(number_format($od_balance, 2, '.', '')); ?></td>
				</tr>
			<?php } ?>



		</table>
	<?php } ?>


	<?php if ((sizeof($est_other_item['chit_details']) > 0) && $billing['bill_type'] != 10) { ?>
		<div class="col-xs-6" style="width: 60%;">
			<div class="table-responsive">
				<table id="pp" class="table text-center" style="width: 100%; table-layout: fixed;">
					<colgroup>
						<col style="width: 10%;">
						<col style="width: 23%;">
						<col style="width: 21%;">
						<col style="width: 23%;">
						<col style="width: 23%;">
					</colgroup>
					<tr>
						<td colspan="5">
							<hr class="item_dashed" style="width: 100%;">
						</td>
					</tr>
					<tr style="text-transform:uppercase;font-size:12px;">
						<th style="text-align: left;">S.No</th>
						<th style="text-align: left;">Ref No</th>
						<th style="text-align: right;">Amount</th>
						<th style="text-align: right;">Scheme Disc</th>
						<th style="text-align: right;">Total</th>
					</tr>
					<tr>
						<td colspan="5">
							<hr class="item_dashed" style="width: 100%;">
						</td>
					</tr>
					<?php
					$i = 1;
					$sheme_close = 0;
					$tot_scheme_benefit = 0;
					$grand_total = 0;
					$total_apx_benefit = 0;

					$is_split_bill = 0;
					foreach ($est_other_item['item_details'] as $items) {
						if($items['isTagsplitted'] == 1){
							$is_split_bill = 1;
						}
					}

					foreach ($est_other_item['chit_details'] as $chit) { 
						// Calculate scheme benefit for each chit
						$scheme_benefit = 0;
						if ($chit['closing_weight'] > 0) {
							$rate_per_gram = ($chit['rate_per_gram'] > 0 ? $chit['rate_per_gram'] : $billing['goldrate_22ct']);
							$closing_weight = number_format($chit['closing_weight'], 3, '.', '');
							$closing_amount = $chit['closing_amount'];
							
							// Base calculation: (closing_weight * rate_per_gram) - closing_amount + additional_benefits
							$base_benefit = ($closing_weight * $rate_per_gram) - $closing_amount + $chit['additional_benefits'];
							
							// Only include savings_in_making_charge and savings_in_wastage if NOT a split bill
							if ($is_split_bill == 0) {
								$savings_in_making_charge = $chit['savings_in_making_charge'];
								$savings_in_wastage = ($chit['savings_in_wastage'] * $rate_per_gram);
								$base_benefit += $savings_in_making_charge + $savings_in_wastage;
							} else {
								// For split bill, accumulate savings separately for Apx Scheme Benefit
								$savings_in_making_charge = $chit['savings_in_making_charge'];
								$savings_in_wastage = ($chit['savings_in_wastage'] * $rate_per_gram);
								$total_apx_benefit += $savings_in_making_charge + $savings_in_wastage;
							}
							
							$scheme_benefit = number_format($base_benefit, 0, '.', '');
						}
						
						// Calculate row values
						$payable_amount = $chit['utilized_amt'] - $scheme_benefit;
						$row_total = $chit['utilized_amt'];
						?>
						<tr>
							<td style="text-align: left;"><?php echo $i; ?></td>
							<td style="text-align: left;"><?php echo $chit['scheme_acc_number']; ?></td>
							<td style="text-align: right;"><?php echo moneyFormatIndia(number_format($payable_amount, 2, '.', '')); ?></td>
							<td style="text-align: right;"><?php echo moneyFormatIndia(number_format($scheme_benefit, 2, '.', '')); ?></td>
							<td style="text-align: right;"><?php echo moneyFormatIndia(number_format($row_total, 2, '.', '')); ?></td>
						</tr>
					<?php 
						$i++;
						$sheme_close += $payable_amount;
						$tot_scheme_benefit += $scheme_benefit;
						$grand_total += $row_total;
					}
					?>
					<tr>
						<td colspan="5">
							<hr class="item_dashed" style="width: 100%;">
						</td>
					</tr>
					<tr style="font-weight: bold;">
						<td></td>
						<td></td>
						<td style="text-align: right;"><?php echo moneyFormatIndia(number_format($sheme_close, 2, '.', '')); ?></td>
						<td style="text-align: right;"><?php echo moneyFormatIndia(number_format($tot_scheme_benefit, 2, '.', '')); ?></td>
						<td style="text-align: right;"><?php echo moneyFormatIndia(number_format($grand_total, 2, '.', '')); ?></td>
					</tr>
					<tr>
						<td colspan="5">
							<hr class="item_dashed" style="width: 100%;">
						</td>
					</tr>
					<tr style="height: 20px;">
						<td colspan="5"></td>
					</tr>
					<?php if ($is_split_bill == 1 && $total_apx_benefit > 0) { ?>
					<tr>
						<td colspan="2" style="font-weight: bold; text-align: left;">Apx Scheme Discount</td>
						<td style="font-weight: bold; text-align: right;"><?php echo moneyFormatIndia(number_format($total_apx_benefit, 2, '.', '')); ?></td>
						<td colspan="2"></td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
	<?php } ?>


	<br>
	<?php if (sizeof($est_other_item['order_adj']) > 0) { ?>
		<div class="col-xs-6" style="width:100%;">
			<div class="table-responsive">
				<div style="text-align:left; font-weight: bold;">Order Advance Details</div>
				<table id="pp" class="table text-center">

					<tr style="visibility:hidden;">
						<th class="table_heading alignCenter" style="width: 5%; text-align:center;"></th>
						<th class="table_heading textOverflowHidden" style="width:6%; text-align:left;"> </th>
						<th class="table_heading alignRight" style="width: 10%;"></th>
						<th class="table_heading alignRight" style="width: 10%;"></th>
						<th class="table_heading alignRight" style="width: 10%;"></th>
						<th class="table_heading alignRight" style="width: 4%;"></th>
						<th class="table_heading alignRight" style="width: 10%;"></th>
						<th class="table_heading alignRight" style="width: 30%;"></th>
						<th class="table_heading alignRight" style="width: 12%;"></th>
					</tr>
					<tr>
						<td colspan="2">Date</td>
						<!-- <td class="alignRight">Rate</td>
															<td class="alignRight">Weight</td> -->
						<td colspan="2" class="alignRight">Amount</td>
						<td colspan="5"></td>
					</tr>
					<tr>
						<td colspan="4">
							<hr class="item_dashed">
						</td>
						<td colspan="5"></td>
					</tr>
					<?php
					$i = 1;
					$adv_paid_amount = 0;
					$adv_paid_weight = 0;
					foreach ($est_other_item['order_adj'] as $ord) {
						$adv_paid_amount += ($ord['store_as'] == 1 ? $ord['advance_amount'] : ($ord['received_weight'] * $ord['rate_per_gram']));
						$adv_paid_weight += ($ord['store_as'] == 1 ? ($ord['advance_amount'] / $ord['rate_per_gram']) : $ord['received_weight']);
						if ($adv_paid_amount > 0 || $adv_paid_weight > 0) {
					?>
							<tr>
								<td colspan="2"><?php echo $ord['bill_date']; ?></td>
								<!-- <td class="alignRight"><?php echo moneyFormatIndia($ord['rate_per_gram']); ?></td>
												<td class="alignRight"><?php echo number_format(($ord['store_as'] == 1 && $ord['advance_amount'] > 0 ? ($ord['advance_amount'] / $ord['rate_per_gram']) : $ord['received_weight']), 3, '.', ''); ?></td> -->
								<td colspan="2" class="alignRight"><?php echo moneyFormatIndia(number_format(($ord['store_as'] == 1 && $ord['advance_amount'] > 0 ? $ord['advance_amount'] : ($ord['received_weight'] * $ord['rate_per_gram'])), 2, '.', '')); ?></td>
								<td colspan="5"></td>
							</tr>

					<?php $i++;
						}
					} ?>
					<tr>
						<td colspan="4">
							<hr class="item_dashed">
						</td>
						<td colspan="5"></td>
					</tr>
					<tr>
						<th colspan="2">Total</th>
						<!-- <td class="alignRight"></td>
													<td class="alignRight" style="font-size: 12px;"><?php echo number_format($adv_paid_weight, 3, '.', '') ?></td> -->
						<td colspan="2" class="alignRight"><?php echo moneyFormatIndia(number_format($adv_paid_amount, 2, '.', '')) ?></td>
						<td colspan="5"></td>
					</tr>
				</table>
			</div>
		</div>
	<?php } ?>

	<?php if ($billing['gift_issue_amount'] > 0) { ?>
		<span>Gift Voucher Worth &#8377; <?php echo ' ' . $billing['gift_issue_amount'] . ' ' ?>Valid Till <?php echo $billing['valid_to'] . '. Voucher Code - ' . $billing['code'] . ''; ?></span>
	<?php } else if ($billing['gift_issue_weight'] > 0) { ?>
		<span>Gift Voucher Worth <?php echo ' ' . $billing['gift_issue_weight'] . ' ' . (($billing['utilize_for']) == 1 ? ' Gold ' : ' Silver ') . ' ' ?>Valid Till <?php echo $billing['valid_to'] . '. Voucher Code - ' . $billing['code'] . ''; ?></span>
	<?php } ?>
	<?php
	if ($billing['note'] != '') { ?>
		<label>Terms and Conditions</label>
		<?php echo $billing['note']; ?>
	<?php } ?>

	</div><br>

	<?php if (sizeof($receiptDetails) > 0) {
		$tot_adv = 0;
		$adj_amt = 0;

		foreach ($receiptDetails as $val) { ?>
			<div>
			<!-- <div style="margin-top: 3px; margin-bottom: 7px">
					<div style="font-size:12px;"><span>(<?php echo $amt_in_words; ?> Only)</span></div>
	</div> -->
				<br>

				<table id="pp" class="table text-center" style="width:80%;">
					<tr>
						<td><b>Receipt No</b></td>
						<td><b>Receipt Date</b></td>
						<td><b>Receipt Amount</b></td>
						<td><b>Adjusted Amount</b></td>
						<td><b>Utilized Amount</b></td>
						<td><b>Refund Amount</b></td>
						<td><b>Balance Amount </b></td>


					</tr>
					<tbody>
						<tr>
							<td><?php echo ($val['bill_no']); ?></td>
							<td><?php echo ($val['bill_date']); ?></td>
							<td><?php echo number_format($val['tot_receipt_amount'], 2, '.', ''); ?></td>
							<td><?php echo number_format($val['adjuseted_amt'], 2, '.', ''); ?></td>
							<td><?php echo number_format($val['tot_utilized_amt'], 2, '.', ''); ?></td>
							<td><?php echo number_format($val['refund_amt'], 2, '.', ''); ?></td>
							<td><?php echo number_format($val['bal_amt'], 2, '.', ''); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
	<?php }
	} ?>

	<?php if ($billing['remark'] != '' && strlen($billing['remark']) > 1) { ?>
		<div style="font-weight:bold;">
			REMARK &nbsp;:<?php echo $billing['remark']; ?>
			<br>
			<br>
		</div>
	<?php } ?>
	<?php
	$sum_va_percent_after_discount = 0;
	foreach($est_other_item['item_details'] as $items){
			if ($items['calculation_based_on'] == 0) {
				// Gross weight based
				$base_wt   = $items['gross_wt'];
				$wastge_wt = ($items['gross_wt'] * ($items['wastage_percent'] / 100));
				$mc        = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * 1));

			} else if ($items['calculation_based_on'] == 1) {
				// Net weight based
				$base_wt   = $items['net_wt'];
				$wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
				$mc        = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['net_wt']) : ($items['mc_value'] * 1));

			} else if ($items['calculation_based_on'] == 2) {
				// VA on net, MC on gross
				$base_wt   = $items['net_wt'];
				$wastge_wt = ($items['net_wt'] * ($items['wastage_percent'] / 100));
				$mc        = ($items['mc_type'] == 2 ? ($items['mc_value'] * $items['gross_wt']) : ($items['mc_value'] * 1));
			}

			/* ---------- Wastage Discount ---------- */
			if ($items['wastage_discount'] > 0 && $items['rate_per_grm'] > 0) {
				$discount_weight = ($items['wastage_discount'] / $items['rate_per_grm']);
				$item_wastge_wt  = $wastge_wt - $discount_weight;
			} else {
				$item_wastge_wt = $wastge_wt;
			}

			/* ---------- Prevent negative wastage ---------- */
			$item_wastge_wt = max(0, $item_wastge_wt);

			/* ---------- MC Discount ---------- */
			if ($items['mc_discount'] > 0) {
				$mc = max(0, $mc - $items['mc_discount']);
			}

			/* ---------- Totals ---------- */
			$total_mc        += $mc;
			$tot_wastage_wt  += $item_wastge_wt;

			/* ---------- Wastage Value (A.V) ---------- */
			$wastage_value = round($item_wastge_wt * $items['rate_per_grm'], 2);

			/* ---------- VA % AFTER DISCOUNT ---------- */
			$va_percent_after_discount = 0;
			if ($base_wt > 0) {
				if ($settings['bill_discount_type'] == 1 && $items['bill_discount'] > 0 && $items['rate_per_grm'] > 0) {
					// Discount type 1: item-wise discount stored in bill_discount field
					// Convert item discount amount to equivalent wastage weight reduction
					$item_disc_as_weight = ($items['bill_discount'] / $items['rate_per_grm']);
					$effective_wastge_wt = max(0, $item_wastge_wt - $item_disc_as_weight);
					$va_percent_after_discount = round(($effective_wastge_wt / $base_wt) * 100, 2);
				} else {
					// Discount type 2: discount already applied in wastage_discount (handled above)
					$va_percent_after_discount = round(($item_wastge_wt / $base_wt) * 100, 2);
				}
			}
		}
		$sum_va_percent_after_discount += $va_percent_after_discount;
		$avg_va_percent_after_discount = $sum_va_percent_after_discount / sizeof($est_other_item['item_details']);

		if ($avg_va_percent_after_discount > 0) {
			echo '<div style="font-weight:bold;">After discounted VA % ' . $avg_va_percent_after_discount . '%</div>';
		}
	?>
	<?php if ($is_eda == 1) { ?>
	<div style="margin-top: 3px; margin-bottom: 3px">
		<div style="font-size:12px;"><span>(<?php echo $amt_in_words; ?> Only)</span></div>
	</div>
	<?php } ?>
	<br>
	<?php if ($billing['bill_emp_name'] != '') { ?>
		<div style="font-weight:bold;">
			<br>
			<?php echo 'Billed Emp : ' . $billing['bill_emp_name'] . '/' . $billing['bill_emp_code'] ?>
		</div>
	<?php } ?>

	<!--<div style="font-weight: bold"> 
								EMP : <?php echo ($esti_sales_emp != '' ? $esti_sales_emp . "/" . $esti_sales_id : ($esti_purchase_emp != '' ? $esti_purchase_emp . "/" . $esti_purchase_id : ($esti_return_emp != '' ? $esti_return_emp . "/" . $esti_return_id : $login_emp))); ?> - REF NO : <?php echo $billing['bill_no']; ?> 
							</div>
							<div style="font-weight: bold"> 
								EMP-ID : <?php echo $billing['id_employee'] ?> / <?php echo $billing['emp_name']; ?>
							</div>-->
	</div>


	<?php if ($billing['delivered_at'] == 2) { ?>
		<div class="row">
			<label><b>Delivered safely at : </b><br>
				<label><?php echo ($billing['del_add_address1'] != '' ? strtoupper($billing['del_add_address1']) . ',' . "<br>" : ''); ?></label>
				<label><?php echo ($billing['del_add_address2'] != '' ? strtoupper($billing['del_add_address2']) . ',' . "<br>" : ''); ?></label>
				<label><?php echo ($billing['del_add_address3'] != '' ? strtoupper($billing['del_add_address3']) . ',' . "<br>" : ''); ?></label>
				<label><?php echo ($billing['del_city_name'] != '' ? strtoupper($billing['del_city_name']) . ($billing['del_pincode'] != '' ? ' - ' . $billing['del_pincode'] . '.' : '') : ''); ?><br></label>
				<label><?php echo ($billing['del_state_name'] != '' ? strtoupper($billing['del_state_name']) . ',' . "<br>" : ''); ?></label>
		</div>
	<?php } ?>
	</div>

	</td>
	</tr>
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</tfoot>
	</table>

	<div class="footer">
		<div style="height:25px;">

		</div>

		<div style="display:flex; font-weight:bold;">

			<div style="font-size:16px; text-align:left;width:50%">Customer Signature</div>
			<div style="font-size:16px; text-align:Right;width:48%;">For <?php echo $comp_details['company_name']; ?></div>

		</div>

		<div style="font-size:11px; width:98%;">
			<p>*Amount of tax (Invoice Inwards) subject to reverse charge. *Received ornaments in good condition. *The Hallmarking charge is included in the invoice.*The consumer can get the purity of the hallmarked jewellery/artefacts checked from any of the BIS recognized A&H center. The list of BIS recognized A&H centers along with address and contact details are available in the website:www.bis.gov.in
				E.&O.E</p>
		</div>

	</div>
	</div>
</body>
<script type="text/javascript">
	/*window.print();

		window.onafterprint = function() {
			
			window.close(); // For closing the tab

		};*/
</script>

</html>