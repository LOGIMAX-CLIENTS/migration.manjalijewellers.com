<html>

<head>
	<meta charset="utf-8">
	<title>Vendor Report</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/process_ack.css">
	<!--	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/receipt_temp.css">-->
	<style type="text/css">
		body,
		html {
			margin-bottom: 0
		}

		span {
			display: inline-block;
		}

		.alignMe {
			list-style: none;
		}

		.alignMe b {
			display: inline-block;
			width: 20%;
			position: relative;
			padding-right: 10px;
			/* Ensures colon does not overlay the text */
		}

		.alignMe b::after {
			content: ":";
			position: absolute;
			right: 2px;
		}

		.alignRight {
			text-align: right;
		}
	</style>
</head>

<body>
	<span class="PDFReceipt">
		<br><br>

		<table class="meta" style="align=center;width:100%">
			<tr>
				<td style="text-align:center !important;"><img width="30%" style="color:red" src="<?php echo base_url(); ?>assets/img/receipt_logo.png"></td>
			</tr>
		</table>


		<div style="width: 100%; text-transform:uppercase;font-weight:bold;">

			<div style="display: inline-block; width: 50%; padding-left: 40px">
				<label><?php echo $process['karigar_name']; ?></label><br>
				<label><?php echo (isset($process['address1']) && $process['address1'] != '' ? $process['address1'] . ',' . "<br>" : ''); ?></label>
				<label><?php echo (isset($process['address2']) && $process['address2'] != '' ? $process['address2'] . ',' . "<br>" : ''); ?></label>
				<label><?php echo (isset($process['address3']) && $process['address3'] != '' ? $process['address3'] . ',' . "<br>" : ''); ?></label>
				<label><?php echo ($process['city_name'] != '' ? $process['city_name'] . ($process['city_name'] != '' ? ' - ' . $process['pincode'] . '.' : '') : ''); ?><br></label>
				<label><?php echo ($process['state_name'] != '' ? $process['state_name'] . ',' . "<br>" : ''); ?></label>
				<label><?php echo ($process['country_name'] != '' ? $process['country_name'] . '.' : ''); ?></label>
			</div>

			<div style="width: 100%; text-align: right; display: inline-block; vertical-align: top; margin-top:-12px;">

				<div style="text-align: right; width:100%; height: 18px;margin-top:1px;">
					<div style="width: 30%; display: inline-block"> Date &nbsp; : &nbsp; </div>
					<div style="text-align: left;width: 20%; display: inline-block;"> <?php echo $process['date_add']; ?></div>
				</div>
				<div style="text-align: right; width:100%; height: 20px;margin-top:-3px;">
					<div style="width: 30%; display: inline-block;"> Process No &nbsp; : &nbsp; </div>
					<div style="text-align: left;width: 20%; display: inline-block;"> <?php echo $process['process_no']; ?> </div>
				</div>
				<div style="text-align: right; width:100%; height: 20px;margin-top:-3px;">
					<div style="width: 30%; display: inline-block;"> Emp Name &nbsp; : &nbsp; </div>
					<div style="text-align: left;width: 20%; display: inline-block;"> <?php echo $process['emp_name']; ?> </div>
				</div>


			</div>
		</div>

		<div style="width: 100%; text-align: center; margin-top:-67px; font-weight: bold; text-transform:uppercase;">
			<label>
				<?php echo $process['process_name'] . ' - ' . $process['process']; ?>
			</label>
		</div>-


		<hr class="header_dashed" style="margin-top:-20px !important;">
		</hr>
		<div class="content-wrapper">
			<div class="box">
				<div class="box-body">
					<div class="container-fluid">
						<div id="printable">
							<?php

function moneyFormatIndia($num) {
	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
}
							if ($process['id_metal_process'] == 1) // Melting
							{
							?>
								<div class="row">
									<div class="col-xs-12">
										<div class="table-responsive">
											<?php if ($process['process_for'] == 1) { ?>
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<tr>
															<td class="" style="width: 5%">S.No</td>
															<td class="" style="width: 10%">Category</td>
															<td class="alignRight" style="width: 10%">pocket No</td>
															<td class="alignRight" style="width: 10%">Gwt(g)</td>
															<td class="alignRight" style="width: 10%">Nwt(g)</td>
															<td class="alignRight" style="width: 10%">PUR(%)</td>
															<td class="alignRight" style="width: 12%">Value(Rs)</td>
														</tr>
													</thead>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<?php
													$i = 1;
													$gross_wt = 0;
													$net_wt = 0;
													$avg_purity = 0;
													$amount = 0;
													foreach ($melting_details as $items) {
														$gross_wt += $items['gross_wt'];
														$net_wt += $items['net_wt'];
														$avg_purity += $items['avg_purity'];
														$amount += $items['amount'];
													?>
														<tr>
															<td class="" style="width: 5%"><?php echo $i; ?></td>
															<td class="" style="width: 10%"><?php echo $items['old_metal_cat'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['pocket_no'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['gross_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['net_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['avg_purity'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['amount'] ?></td>
														</tr>
													<?php $i++;
													}
													?>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 10%">TOTAL</td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format(($avg_purity / sizeof($melting_details)), 2, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($amount, 3, '.', ''); ?></td>
													</tr>
												</table>
											<?php } else { ?>
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<tr>
															<td class="alignRight" style="width: 5%">S.No</td>
															<td class="" style="width: 10%">Process No</td>
															<td class="" style="width: 10%">Product</td>
															<td class="alignRight" style="width: 10%">Gwt(g)</td>
															<td class="alignRight" style="width: 10%">Nwt(g)</td>
															<td class="alignRight" style="width: 10%">Recd Wt(g)</td>
															<td class="alignRight" style="width: 10%">Prod Loss(g)</td>
															<td class="alignRight" style="width: 10%">Charges(Rs)</td>
														</tr>
													</thead>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<?php
													$i = 1;
													$gross_wt = 0;
													$net_wt = 0;
													$received_wt = 0;
													$prod_loss = 0;
													$receipt_charges = 0;
													foreach ($melting_details as $items) {
														$gross_wt += $items['gross_wt'];
														$net_wt += $items['net_wt'];
														$received_wt += $items['received_wt'];
														$prod_loss += $items['received_less_wt'];
														$receipt_charges += $items['receipt_charges'];
													?>
														<tr>
															<td class="alignRight" style="width: 5%"><?php echo $i; ?></td>
															<td class="" style="width: 10%"><?php echo $items['process_no'] ?></td>
															<td class="" style="width: 10%"><?php echo $items['product_name'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['gross_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['net_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['received_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['received_less_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['receipt_charges'] ?></td>
														</tr>
													<?php $i++;
													}
													?>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 10%">TOTAL</td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($received_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($prod_loss, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($receipt_charges, 2, '.', ''); ?></td>
													</tr>
												</table>
											<?php } ?>
											<br>
										</div>
									</div>
								</div>
							<?php } ?>
							<?php
							if ($process['id_metal_process'] == 2) //testing
							{ ?>
								<div class="row">
									<div class="col-xs-12">
										<div class="table-responsive">
											<?php if ($process['process_for'] == 1) { ?>
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<tr>
															<td class="alignRight" style="width: 5%">S.No</td>
															<td class="alignRight" style="width: 10%">Process No</td>
															<td class="alignRight" style="width: 10%">Category</td>
															<td class="alignRight" style="width: 10%">Nwt(g)</td>
															<td class="alignRight" style="width: 10%">PUR(%)</td>
															<td class="alignRight" style="width: 12%">Value(Rs)</td>
														</tr>
													</thead>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<?php
													$i = 1;
													$received_wt = 0;
													$avg_purity = 0;
													$amount = 0;
													foreach ($melting_details as $items) {
														$received_wt += $items['net_wt'];
														$avg_purity += $items['purity'];
														$amount += $items['amount'];
													?>
														<tr>
															<td class="alignRight" style="width: 5%"><?php echo $i; ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['process_no'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['category_name'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['net_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['purity'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['amount'] ?></td>
														</tr>
													<?php $i++;
													}
													?>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 10%">TOTAL</td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($received_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format(($avg_purity / sizeof($melting_details)), 2, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($amount, 3, '.', ''); ?></td>
													</tr>
												</table>
											<?php } else { ?>
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<tr>
															<td class="alignRight" style="width: 5%">S.No</td>
															<td class="alignRight" style="width: 10%">Process No</td>
															<td class="alignRight" style="width: 10%">Nwt(g)</td>
															<td class="alignRight" style="width: 10%">Recd Wt(g)</td>
															<td class="alignRight" style="width: 10%">Prod Loss(g)</td>
															<td class="alignRight" style="width: 10%">Purity(%)</td>
															<td class="alignRight" style="width: 10%">Charges(Rs)</td>
														</tr>
													</thead>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<?php
													$i = 1;
													$net_wt = 0;
													$received_wt = 0;
													$prod_loss = 0;
													$receipt_charges = 0;
													foreach ($melting_details as $items) {
														$gross_wt += $items['gross_wt'];
														$net_wt += $items['net_wt'];
														$received_wt += $items['received_wt'];
														$prod_loss += $items['production_loss'];
														$receipt_charges += $items['receipt_charges'];
													?>
														<tr>
															<td class="alignRight" style="width: 5%"><?php echo $i; ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['process_no'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['net_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['received_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['production_loss'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['received_purity'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['receipt_charges'] ?></td>
														</tr>
													<?php $i++;
													}
													?>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 10%">TOTAL</td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($received_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($prod_loss, 3, '.', ''); ?></td>
														<td></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($receipt_charges, 3, '.', ''); ?></td>
													</tr>
												</table>
											<?php } ?>
											<br>
										</div>
									</div>
								</div>
							<?php }
							?>

							<?php
							if ($process['id_metal_process'] == 3) //Refining
							{ ?>
								<div class="row">
									<div class="col-xs-12">
										<div class="table-responsive">
											<?php if ($process['process_for'] == 1) { ?>
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<tr>
															<td class="alignRight" style="width: 5%">S.No</td>
															<td class="alignRight" style="width: 10%">Process No</td>
															<td class="alignRight" style="width: 10%">Nwt(g)</td>
															<td class="alignRight" style="width: 10%">PUR(%)</td>
															<td class="alignRight" style="width: 12%">Value(Rs)</td>
														</tr>
													</thead>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<?php
													$i = 1;
													$received_wt = 0;
													$avg_purity = 0;
													$amount = 0;
													foreach ($melting_details as $items) {
														$received_wt += $items['received_wt'];
														$avg_purity += $items['purity'];
														$amount += $items['amount'];
													?>
														<tr>
															<td class="alignRight" style="width: 5%"><?php echo $i; ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['process_no'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['received_wt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['purity'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['amount'] ?></td>
														</tr>
													<?php $i++;
													}
													?>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 10%">TOTAL</td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($received_wt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format(($avg_purity / sizeof($melting_details)), 2, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($amount, 3, '.', ''); ?></td>
													</tr>
												</table>
											<?php } else { ?>
												<table id="pp" class="table text-center">
																		<thead style="text-transform:uppercase;font-size:10px;font-weight: bold;">
																			<tr>
																				<td class="alignCenter" style="width: 20%">S.No</td>
																				<!-- <td class="alignRight" style="width: 20%">Process No</td> -->
																				<td class="alignLeft" style="width: 50%">Category</td>
																				<td class="alignRight" style="width: 30%">Recd Wt(g)</td>
																				<!-- <td class="alignRight" style="width: 20%">Receipt Charges</td> -->
																			</tr>
																		</thead>
																		<tr>
																				<!-- <td><hr class="item_dashed"></td> -->
																				<td colspan="3"><hr class="item_dashed"></td>

																		</tr>
																		<?php 
																		$i=1;
																		$net_wt=0;
																		$received_wt=0;
																		$receipt_charges=0;
																		$prod_loss=0;
																		$chg_cgst=0;
																		$chg_sgst=0;
																		$chg_igst=0;
																		$tot_tax_amt=0;
																		$rcpt_charge_bf_tax=0;
																		$tax_perc=0;
																		foreach($melting_details as $items)
																		{
																		$gross_wt+=$items['gross_wt'];
																		$net_wt+=$items['net_wt'];
																		$received_wt+=$items['received_wt'];
																		$receipt_charges=$items['receipt_charges'];
																		$chg_cgst=$items['receipt_charges_tax_cgst'];
																		$chg_sgst=$items['receipt_charges_tax_sgst'];
																		$chg_igst=$items['receipt_charges_tax_igst'];
																		$tot_tax_amt=$items['receipt_charges_tax_amt'];
																		$tax_perc = $items['receipt_charges_tax_perc'];
																		$rcpt_charge_bf_tax = $items['receipt_charges']-$items['receipt_charges_tax_cgst']-$items['receipt_charges_tax_sgst']-$items['receipt_charges_tax_igst'];
																		?>
																			<tr>
																				<td class="alignCenter" style="width: 5%"><?php echo $i;?></td>
																				<!-- <td class="alignRight" style="width: 10%"><?php echo $items['process_no']?></td> -->
																				<td class="alignLeft" style="width: 15%"><?php echo $items['category_name']?></td>
																				<td class="alignRight" style="width: 10%"><?php echo $items['received_wt']?></td>
																				<!-- <td class="alignRight" style="width: 10%"><?php echo $items['receipt_charges']-$items['receipt_charges_tax_cgst']-$items['receipt_charges_tax_sgst']-$items['receipt_charges_tax_igst']?></td> -->
																			</tr>
																		<?php $i++; }
																		?>
																		
																		

																		<tr>
																				<!-- <td><hr class="item_dashed"></td> -->
																				<td colspan="3"><hr class="item_dashed"></td>

																		</tr>
																		<tr style="text-transform:uppercase;font-size:10px;font-weight: bold;">
																			<td class="alignRight" style="width: 5%"></td>
																			<td class="alignRight" style="width: 10%">TOTAL</td>
																			<!-- <td class="alignRight" style="width: 10%"><?php echo number_format($net_wt,3,'.','');?></td> -->
																			<td class="alignRight" style="width: 10%"><?php echo number_format($received_wt,3,'.','');?></td>
																			
																			
																		</tr>
																		<tr>
																				<!-- <td><hr class="item_dashed"></td> -->
																				<td colspan="3"><hr class="item_dashed"></td>

																		</tr>
																		<?php if($rcpt_charge_bf_tax > 0){ ?>
																			<tr style="font-weight:bold;">
																				
																				<td></td>
																				<td class="alignRight">Receipt Charges</td>
																				<td class="alignRight"><?php echo moneyFormatIndia(number_format($rcpt_charge_bf_tax,2,'.',''))?></td>
																			</tr>
																		<?php } ?>
																		<?php if($chg_cgst > 0){ ?>
																			<tr style="font-weight:bold;">
																				
																				<td></td>
																				<td class="alignRight">CGST <?php echo '('.($tax_perc/2).'%)' ?></td>
																				<td class="alignRight"><?php echo $chg_cgst?></td>
																			</tr>
																		<?php } ?>
																		<?php if($chg_sgst > 0){ ?>
																			<tr style="font-weight:bold;">
																				
																				<td></td>
																				<td class="alignRight">SGST <?php echo '('.($tax_perc/2).'%)' ?></td>
																				<td class="alignRight"><?php echo $chg_sgst?></td>
																			</tr>
																		<?php } ?>
																		<?php if($chg_igst > 0){ ?>
																			<tr style="font-weight:bold;">
																				
																				<td></td>
																				<td class="alignRight">IGST <?php echo '('.($tax_perc).'%)' ?></td>
																				<td class="alignRight"><?php echo $chg_igst?></td>
																			</tr>
																		<?php } ?>
                                                                        <tr>
																				<!-- <td><hr class="item_dashed"></td> -->
																				<td colspan="3"><hr class="item_dashed"></td>

																		</tr>

																		<tr style="font-weight:bold">
																			
																			<td colspan="2" class="alignRight">Total</td>
																			<td  class="alignRight"><?php echo moneyFormatIndia($receipt_charges)?></td>
																		</tr>
																		<tr>
																				<!-- <td><hr class="item_dashed"></td> -->
																				<td colspan="3"><hr class="item_dashed"></td>

																		</tr>
																	</table>
											<?php } ?>
											<br>
										</div>
									</div>
								</div>
							<?php }  ?>


							<?php
							if ($process['id_metal_process'] == 4) //Polishing
							{ ?>
								<div class="row">
									<div class="col-xs-12">
										<div class="table-responsive">
											<?php if ($process['process_for'] == 1) { ?>
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<tr>
															<td class="alignRight" style="width: 5%">S.No</td>
															<td class="alignRight" style="width: 10%">Pocket No</td>
															<td class="alignRight" style="width: 10%">Pcs</td>
															<td class="alignRight" style="width: 10%">Gwt(g)</td>
															<td class="alignRight" style="width: 10%">Nwt(g)</td>
														</tr>
													</thead>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<?php
													$i = 1;
													$toal_pcs = 0;
													$total_gwt = 0;
													$total_nwt = 0;
													foreach ($polishing_details as $items) {
														$toal_pcs += $items['issue_pcs'];
														$total_gwt += $items['issue_gwt'];
														$total_nwt += $items['issue_nwt'];
													?>
														<tr>
															<td class="alignRight" style="width: 5%"><?php echo $i; ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['pocket_no'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['issue_pcs'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['issue_gwt'] ?></td>
															<td class="alignRight" style="width: 10%"><?php echo $items['issue_nwt'] ?></td>
														</tr>
													<?php $i++;
													}
													?>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 10%">TOTAL</td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($toal_pcs, 0, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($total_gwt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 10%"><?php echo number_format($total_nwt, 3, '.', ''); ?></td>
													</tr>
												</table>
											<?php } else { ?>
												<table id="pp" class="table text-center">
													<thead style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<tr>
															<td class="alignRight" style="width: 5%">S.No</td>
															<td class="alignRight" style="width: 10%">Process No</td>
															<td class="alignRight" style="width: 10%">Category</td>
															<td class="alignRight" style="width: 10%">Recd GWt(g)</td>
															<td class="alignRight" style="width: 10%">Recd NWt(g)</td>
														</tr>
													</thead>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<?php
													$i = 1;
													$net_wt = 0;
													$received_gwt = 0;
													$received_nwt = 0;
													foreach ($polishing_details as $items) {
														$gross_wt += $items['gross_wt'];
														$net_wt += $items['net_wt'];
														$received_gwt += $items['received_gwt'];
														$received_nwt += $items['received_nwt'];
													?>
														<tr>
															<td class="alignRight" style="width: 5%"><?php echo $i; ?></td>
															<td class="alignRight" style="width: 20%"><?php echo $items['process_no'] ?></td>
															<td class="alignRight" style="width: 25%"><?php echo $items['category_name'] ?></td>
															<td class="alignRight" style="width: 20%"><?php echo $items['received_gwt'] ?></td>
															<td class="alignRight" style="width: 20%"><?php echo $items['received_nwt'] ?></td>
														</tr>
													<?php $i++;
													}
													?>
													<tr>
														<td>
															<hr class="item_dashed">
														</td>
													</tr>
													<tr style="text-transform:uppercase;font-size:10px;font-weight: bold;">
														<td class="alignRight" style="width: 5%"></td>
														<td class="alignRight" style="width: 15%">TOTAL</td>
														<td class="alignRight" style="width: 15%"></td>
														<td class="alignRight" style="width: 15%"><?php echo number_format($received_gwt, 3, '.', ''); ?></td>
														<td class="alignRight" style="width: 15%"><?php echo number_format($received_nwt, 3, '.', ''); ?></td>
														<td></td>
													</tr>
												</table>
											<?php } ?>
											<br>
										</div>
									</div>
								</div>
							<?php }  ?>

							<?php
							if (sizeof($process_payment) > 0) {
								$cash_amt = 0;
								$net_amt = 0;
								$total_amt = 0;
								function moneyFormatIndia($num)
								{
									return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
								}
								foreach ($process_payment as $items) {
									$total_amt += $items['payment_amount'];
									if ($items['payment_mode'] == 'Cash') {
										$cash_amt += $items['payment_amount'];
									}
									if ($items['payment_mode'] == 'NB') {
										$net_amt += $items['payment_amount'];
									}
								}
							?>
								<table id="pp" class="table text-center" style="width:65%; font-weight: bold">
									<tr>
										<?php if ($cash_amt > 0) { ?>
											<td>Cash</td>
										<?php } ?>
										<?php if ($net_amt > 0) { ?>
											<td>NB</td>
										<?php } ?>
										<td>Total</td>
									</tr>
									<tbody>
										<tr>
											<?php if ($cash_amt > 0) { ?>
												<td><?php echo moneyFormatIndia(number_format($cash_amt, 2, '.', '')); ?></td>
											<?php } ?>

											<?php if ($net_amt > 0) { ?>
												<td><?php echo moneyFormatIndia(number_format($net_amt, 2, '.', '')); ?></td>
											<?php } ?>
											<td><?php echo moneyFormatIndia(number_format($total_amt, 2, '.', '')); ?></td>
										</tr>
									</tbody>
									</tale>
								<?php }
								?>

								<?php if ($process['remark']) { ?>
									<p><b>REMARKS : <?= $process['remark'] ?></b></p>
								<?php } ?>

						</div>
					</div>
				</div><!-- /.box-body -->
			</div>
		</div>

	</span>
</body>

</html>