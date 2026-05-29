<html>

<head>
	<meta charset="utf-8">
	<title>Order Booking Proforma invoice</title>
	<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/billing_receipt.css"> -->
	<style type="text/css">
		body,
		html {
			margin-bottom: 0;
			font-size: 12px !important;
		}
		.item_dashed{
			width: 100%;
		}

		span {
			display: inline-block;
		}

		.alignRight {

			text-align: right;
		}

		.alignLeft {

			text-align: left;
		}

		.addr_headers_labels {
			display: inline-block;
			width: 20%;
		}

		.addr_rate_labels {
			display: inline-block;
			width: 10%;
		}

		.addr_labels {
			display: inline-block;
			width: 20%;
		}

		.addr_values {
			display: inline-block;
			padding-left: -5px;
		}

		.addr_delv_labels {
			display: inline-block;
			width: 25%;
		}

		.addr_delv_values {
			display: inline-block;
			padding-left: 5px;
		}

		.addr_brch_labels {
			display: inline-block;
			width: 20%;
		}

		.addr_brch_values {
			display: inline-block;
			padding-left: 20px;
		}

		.stones,
		.charges {
			font-style: italic;
		}

		.stones .stoneData,
		.charges .chargeData {
			font-size: 12px !important;
		}

		.product-images-table {
			width: 100%;
			margin-top: 10mm;
			margin-bottom: 10mm;
			table-layout: fixed;
			border-collapse: collapse;
		}

		.product-image-cell {
			width: 20%;
			padding: 5px;
			text-align: center;
			vertical-align: top;
			text-align: center;
		}

		.product-image-item img {
			width: 30mm;
			height: 30mm;
			object-fit: cover;
			border: 1px solid #ccc;
		}

		.product-name {
			font-size: 10px;
			word-wrap: break-word;
			max-width: 30mm;
			margin: 5px auto 0;
			text-transform: uppercase;
			display: block;
		}

		#pp {
			border-collapse: collapse;
		}

		#pp thead {
			display: table-header-group;
		}

		#pp thead th {
			border-top: 2px solid #494343ff;
			border-bottom: 2px solid #494343ff;
			padding: 8px 0;
			margin: 5px 0;
		}


	</style>
</head>

<body>
	<span class="PDFReceipt">
		<!--<div class="header_top">
				<div class="header_top_left" style="mergin-top:30px !important;">
					<div>CIN : 394872094392030</div>
					<div>GSTIN : 98423792430923 </div>
				</div>
				<div class="header_top_right">
					<img width="30%" src="<?php echo dirname(base_url()) ?>/assets/img/receipt_logo.png" />
				</div>
			</div><br>-->

			<?php
			function moneyFormatIndia($num)
			{
				return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
			}
			?>
		<div style="width: 100%; text-transform:uppercase;font-size: 11px !important;">
			<div style="display: inline-block; width: 50%; padding-left:0px;margin-top:140px;">
				<!-- <label><?php echo '<div class="addr_headers_labels">CIN</div><div class="addr_values">:&nbsp;&nbsp;' . $comp_details['cin_number'] . "</div>"; ?></label><br>
				<label><?php echo '<div class="addr_headers_labels">GSTIN</div><div class="addr_values">:&nbsp;&nbsp;' . $comp_details['gst_number'] . "</div>"; ?></label><br> -->
			</div>

			<div style="width: 50%; text-align: right; display: inline-block; vertical-align: top;">
				<div style="text-align: right;width: 100%; display: inline-block;margin-top:-30px;display:none">
					<img width="50%" src="<?php echo dirname(base_url()) ?>/assets/img/receipt_logo.png" />
				</div>
			</div>
		</div>
		<p></p>

		<div style="width: 100%; text-transform:uppercase;font-size: 11px !important;">
			<div style="display: inline-block; width: 48%; padding-left:0px;">
				<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;' . 'Mr./Ms.' . $cus_details['cus_name'] . "</div>"; ?></label><br>
				<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;' . $cus_details['mobile'] . "</div>"; ?></label><br>
				<label><?php echo ($cus_details['address1'] != '' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($cus_details['address1']) . ',' . "</div><br>" : ''); ?></label>
				<label><?php echo ($cus_details['address2'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($cus_details['address2']) . ',' . "</div><br>" : ''); ?></label>
				<label><?php echo ($cus_details['address3'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($cus_details['address3']) . ',' . "</div><br>" : ''); ?></label>
				<label><?php echo ($cus_details['village_name'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($cus_details['village_name']) . ',' . "</div><br>" : ''); ?></label>
				<label><?php echo ($cus_details['city_name'] != '' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($cus_details['city_name']) . ($cus_details['pincode'] != '' ? ' - ' . $cus_details['pincode'] . '.' : '') . "</div><br>" : ''); ?></label>
				<label><?php echo ($cus_details['cus_state'] != '' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($cus_details['cus_state'] . ($cus_details['state_code'] != '' ? '-' . $cus_details['state_code'] : '')) . '.' . "</div><br>" : ''); ?></label>
				<!-- <label><?php echo (isset($cus_details['pan_no']) && $cus_details['pan_no'] != '' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($cus_details['pan_no']) . "</div><br>" : ''); ?></label> -->
				<label><?php echo (isset($cus_details['gst_number']) && $cus_details['gst_number'] != '' ? '<div class="addr_labels">GSTIN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($cus_details['gst_number']) . "</div><br>" : ''); ?></label>
			</div>



			<!-- <div style="display: inline-block; width: 10%; padding-left:0px;"></div>
			<div style="width: 50%; text-align: right !importan; display: inline-block; vertical-align: top;margin-top:-14px !important;">
				<div style="text-align: left !important;width: 100%; display: inline-block;">
					<label><?php echo ($comp_details['name'] != '' ? '<div class="addr_brch_labels">Branch</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($comp_details['name']) . ',' . "</div><br>" : ''); ?></label>
					<label><?php echo ($comp_details['address1'] != '' ? '<div class="addr_brch_labels">Address</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($comp_details['address1']) . ',' . "</div><br>" : ''); ?></label>
					<label><?php echo ($comp_details['address2'] != '' ? '<div class="addr_brch_labels"></div><div class="addr_brch_values">&nbsp;&nbsp;&nbsp;' . strtoupper($comp_details['address2']) . ',' . "</div><br>" : ''); ?></label>
					<label><?php echo ($comp_details['city'] != '' ? '<div class="addr_brch_labels">Place</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($comp_details['city']) . ($comp_details['pincode'] != '' ? ' - ' . $comp_details['pincode'] . '.' : '') . "</div>" : ''); ?><br></label>
					<label><?php echo ($comp_details['state'] != '' ? '<div class="addr_brch_labels">State</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($comp_details['state'] . ($comp_details['state_code'] != '' ? '-' . $comp_details['state_code']  : '')) . '.' . "</div><br>" : ''); ?></label>
				</div>
			</div>
		</div> -->

		<div style="display: inline-block; width: 10%; padding-left:0px;"></div>
			<div style="width: 50%; text-align: right !importan; display: inline-block; vertical-align: top;margin-top:-14px !important;">
				<div style="text-align: right !important;width: 210%; display: inline-block;">
					<label><?php echo ($cus_details['order_date'] != '' ? '<div class="addr_brch_labels">Order Date</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($cus_details['order_date']) . '' . "</div><br>" : ''); ?></label>
					<label><?php echo ($cus_details['orderno'] != '' ? '<div class="addr_brch_labels">Order No</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($cus_details['orderno']) . '' . "</div><br>" : ''); ?></label>				</div>
			</div>
		</div>

		<div style="width: 100%; text-transform:uppercase;font-size: 11px !important;text-align:center;margin-top:-45px !important;">
			<label><b>Order Booking Proforma invoice</b></label>
		</div>
		<p></p>


		<!-- <div style="text-align: left;width: 100%; text-transform:uppercase;margin-top:-8px;font-size: 11px !important">
			<div style="text-align: left; width:100%;height: 18px; ">
				<label><?php echo '<div class="addr_rate_labels">GOLD</div><div class="addr_values">:&nbsp;&nbsp;' . number_format($metal_rate['goldrate_22ct'], 2, '.', '') . '/' . 'Gm' . "</div>"; ?></label><br>
				<label><?php echo '<div class="addr_rate_labels">SILVER</div><div class="addr_values">:&nbsp;&nbsp;' . number_format($metal_rate['silverrate_1gm'], 2, '.', '') . '/' . 'Gm' . "</div>"; ?></label><br>
			</div>
		</div> -->

		<!-- <div style="width: 100%; text-align: right; text-transform:uppercase;display: inline-block;margin-top:-40px;font-size: 11px !important">
			<div style="text-align: right; width:100%;height: 18px;">
				<div style="width: 80%; display: inline-block"> Order Date &nbsp; : &nbsp; </div>
				<div style="width: 20%; display: inline-block; margin-top: -2px;text-align: left;"><?php echo $cus_details['order_date']; ?></div>
			</div> -->


			<!-- <div style="text-align: right; width:100%;height: 18px;">
				<div style="width: 80%; display: inline-block"> Order no &nbsp; : &nbsp; </div>
				<div style="width: 15%; display: inline-block; margin-top: -2px;text-align: left;"><?php echo $cus_details['orderno']; ?></div>
			</div>

		</div> -->


		<div class="content-wrapper">
			<div class="box">
				<div class="box-body">
					<div class="container-fluid">
						<div id="printable">
							<div class="row">
								<div class="col-xs-12">
									<div class="table-responsive">
										<table id="pp" class="table text-center" style="width:100%">
											<thead style="text-transform:uppercase;font-size:10px;">
												
												<tr>
													<th class="table_heading alignLeft" style="width: 5%">S.NO</th>
													<th class="table_heading aligncenter" style="width: 20%">Description</th>
													<th class="table_heading alignRight" style="width: 9%">Size</th>
													<th class="table_heading alignRight" style="width: 9%">Pcs</th>
													<th class="table_heading alignRight" style="width: 9%">Gwt</th>
													<th class="table_heading alignRight" style="width: 9%">Lwt</th>
													<th class="table_heading alignRight" style="width: 9%">Nwt</th>
													<th class="table_heading alignRight" style="width: 9%">V.A(%)</th>
													<th class="table_heading alignRight" style="width: 9%">MC/GM</th>
													<th class="table_heading alignRight" style="width: 9%">Rate</th>
													<th class="table_heading alignRight" style="width: 9%">Amount</th>
												</tr>
											</thead>
											<!--</thead>
										<tbody>-->
											<?php
											$i = 1;
											$total_wt = 0;
											$totalitems = 0;
											$total_cgst = 0;
											$total_sgst = 0;
											$total_igst = 0;

											$total_nwt = 0;
											$total_lwt = 0;
											$total_amt = 0;

											foreach ($order as $val) {
												// print_r($val);exit;
												$totalitems += $val['totalitems'];
												$total_wt += $val['weight'];
												$total_amt += $val['rate'];
												$total_lwt += $val['less_wt'];
												$total_nwt += $val['net_wt'];

												$total_cgst     += $val['total_cgst'];
												$total_sgst     += $val['total_sgst'];
												$total_igst     += $val['total_igst'];

											?>
												<tr>
													<td class="alignLeft"><?php echo $i; ?></td>
													<td class="alignLeft">
														<?php echo $val['product_name'] . '&nbsp;' 
														. '(' . $val['design_name'] . ')'
														. (!empty($val['pur_description']) ? ' - ' . $val['pur_description'] : ''); ?>
													</td>
													<td class="alignRight"><?php echo $val['size_name'] != '' ? $val['size_name'] : '-'; ?></td>
													<td class="alignRight"><?php echo $val['totalitems']; ?></td>
													<td class="alignRight"><?php echo $val['weight']; ?></td>
													<td class="alignRight"><?php echo $val['less_wt']; ?></td>
													<td class="alignRight"><?php echo $val['net_wt']; ?></td>
													<td class="alignRight"><?php echo $val['wast_percent']; ?></td>
													<td class="alignRight"><?php echo $val['mc_value']; ?></td>
													<td class="alignRight"><?php echo $val['rate_per_gram']; ?></td>
													<td class="alignRight"><?php echo $val['rate']; ?></td>
												</tr>


												<?php
												if (count($val['stone_details']) > 0) {
													foreach ($val['stone_details'] as $stoneItems) {	?>
													<tr class='stones'>
													<td></td>
													<td colspan="2" class='alignLeft stoneData'><?php echo $stoneItems['stone_name']; ?></td>
													<td></td>
													<td class="alignRight stoneData"><?php echo moneyFormatIndia(number_format((float)($stoneItems['wt']), 3, '.', '')) . '/' . $stoneItems['uom_short_code']; ?></td>
													<td colspan="2"class="alignRight stoneData"><?php echo $stoneItems['rate_per_gram'] . ' /  ' . $stoneItems['uom_short_code']; ?></td>
													<td colspan="2"class="alignRight stoneData"><?php echo moneyFormatIndia('Rs : ' . $stoneItems['amount']); ?></td>
													<td></td>
													<td></td>
													<td></td>
													</tr>
												<?php }
												}
												?>
												
												<?php
												if ($val['description'] != '') { ?>
													<tr>
														<td colspan="5">Remarks : <?php echo $val['description'] . ($val['cus_duedate'] != '' ? ' - Due Date' . $val['cus_duedate'] : ''); ?></td>
													</tr>
												<?php }
												?>
											<?php $i++;
											} ?>
											<tr>
												<td colspan="11">
													<hr class="item_dashed">
												</td>
											</tr>
											<!--</tbody> -->


											<tr>
												<td colspan="2"><b>TOTAL</b></td>
												<td></td>
												<td class="alignRight"><b><?php echo number_format((float)$totalitems, 0, '.', '') ?></b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_wt, 3, '.', '') ?></b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_lwt, 3, '.', '') ?></b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_nwt, 3, '.', '') ?></b></td>
												<td class="alignRight"><b></td>
												<td class="alignRight"><b></td>
												<td class="alignRight"><b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_amt, 3, '.', '') ?></b></td>

											</tr>
											<tr>
												<td colspan="11">
													<hr class="item_dashed">
												</td>
											</tr>
											
											<tr>
												<td colspan="5"><b><?php echo isset($val['rate_type']) ? $val['rate_type'] . ': ' . $val['rate_per_gram'] : ''; ?></b></td>
											</tr>

										</table><br>
									</div>


								</div>
							</div>

							<?php
							$product_images = array();
							$i = 1;
							foreach ($order as $item) {
								if (isset($item['image_details']) && count($item['image_details']) > 0) {
									$image_filename = isset($item['image_details'][0]['image']) ? $item['image_details'][0]['image'] : '';
									
									if ($image_filename != '') {
										$image_path = 'assets/img/orders/' . $image_filename;
										
										
										if (file_exists($image_path)) {
											$type = pathinfo($image_path, PATHINFO_EXTENSION);
											$data = @file_get_contents($image_path);
											if ($data !== false) {
												$base64_img = 'data:image/' . $type . ';base64,' . base64_encode($data);
												
												$product_images[] = array(
													'sno'          => $i,
													'product_name' => $item['product_name'],
													'img_src' => $base64_img
												);
											}
										}
									}
								}
								 $i++;
							}
							?>

							<?php if (count($product_images) > 0) { 
								$chunks = array_chunk($product_images, 5);
							?>
							<table class="product-images-table">
								<?php foreach ($chunks as $row) { ?>
									<tr>
										<?php foreach ($row as $prod_img) { ?>
											<td class="product-image-cell">
												<img src="<?php echo $prod_img['img_src']; ?>" 
													 style="width: 35mm; height: 35mm; object-fit: cover; border: 1px solid #ccc;" 
													 alt="<?php echo $prod_img['product_name']; ?>" />
												<div class="product-name">
													<?php echo $prod_img['sno'] . '. ' . $prod_img['product_name']; ?>
												</div>
											</td>
										<?php }
										$empty_cells = 5 - count($row);
										for($j=0; $j<$empty_cells; $j++) {
											echo '<td class="product-image-cell"></td>';
										}
										?>
									</tr>
								<?php } ?>
							</table>
							<?php } ?>

							<div class="row" style="text-transform:uppercase;">
								<label>Customer Signature</label>
								<label style="margin-left:20%;">Operator Signature</label>
								<label style="margin-left:20%;">Cashier Signature</label>

						  </div><br>

						  <div class="row" style="text-transform:uppercase;">
								<label></label>&nbsp;&nbsp;
								<label style="margin-left:35%;"><?php echo ($cus_details['emp_name'] != '' ? $cus_details['emp_name'] :"") . '&nbsp;' . ($dCData['is_day_closed'] == 0 ? ' - ' . date("d-m-y h:i:sa") : ''); ?></label>
								<label style="margin-left:20%;"></label>

						  </div>

							


						</div>
					</div>
				</div><!-- /.box-body -->
			</div>

	</span>

</body>

</html>