<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Estimation</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/estimation.css">
	<style type="text/css">
		body,
		html {
			margin-bottom: 0;
		}
		.alignCenter {
			text-align: center;
		}
		.alignLeft {
			text-align: left;
		}
		.item_details {
			width: 100%;
			margin-left: -18px;
		}
		.alignRight {
			text-align: right;
		}
		.item_name {
			font-weight: bold;
		}
		.finalTotal {
			width: 109.6%;
			font-size: 18px !important;
		}
		@page {
			size: 78mm 297mm;
			margin-bottom: -40px !important;
		}
		.ebill img {
			width: 5%;
			margin-left: 0px;
			display: inline-block;
		}
		/* output size */
		span {
			display: inline-block;
		}
	</style>
</head>
<?php
// function ($num)
// {
// 	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
// }
 ?>
<body class="plugin">
	<span class="PDFReceipt">
		<div class="printable">
			<div class="header">
				<h3> Branch - <?php echo $estimation['short_name']; ?> <br> Estimation - <?php echo $estimation['esti_no']; ?></h3>
			</div>
			<div class="tap_head">
				<div class="alignRight" style="margin-right:-30px">
				   <!-- <div class="alignLeft" style="margin-right:-30px"> -->
				   <div class="bill">
				  <!-- <php  print_r($filename); ?> -->
                     <img src="<?php echo $filename; ?>" alt="">
                   </div>
				   <!-- </div> -->
					<label> Date: <?php echo $estimation['estimation_datetime']; ?></label><br>
					<?php
					$gold = '';
					$silver = '';
					$platinum = '';
					foreach ($est_other_item['item_details'] as $m_rate) {
						$m_type = $m_rate['metal_type'];
						if ($m_type == 1) {
							$gold = $m_rate['metal_name'];
						}
						if ($m_type == 2) {
							$silver = $m_rate['metal_name'];
						}
						if ($m_type == 3) {
							$platinum = $m_rate['metal_name'];
						}
					}
					?>
					<?php if (strlen($gold) > 0) { ?>
						<label><?php echo 'gold rate	: ' . number_format($metal_rates['goldrate_22ct'], 2, '.', '') . '/GM'; ?></label><br>
					<?php }
					if (strlen($silver) > 0) { ?>
						<label><?php echo 'silver rate  : ' . number_format($metal_rates['silverrate_1gm'], 2, '.', '') . '/GM'; ?></label><br>
					<?php }
					if (strlen($platinum) > 0) { ?>
						<label><?php echo 'platinum rate: ' . number_format($metal_rates['platinum_1g'], 2, '.', '') . '/GM'; ?></label><br>
					<?php } ?>
				</div>
			</div>
			<p></p>
			<div style="text-align:center; width:110%">
				<label>For Office use only</label>
			</div>
			<div class="item_details">
				<?php if (sizeof($est_other_item['item_details'])) { ?>
					<table class="estimation" style="width:118%;">
						<tr>
							<td>
								<hr class="item_dashed">
							</td>
						</tr>
						<!--</thead>-->
						<tbody>
							<?php
							$tot_payable = 0;
							$tot_purchase = 0;
							$market_rate_cost = 0;
							$total_wt = 0;
							$total_gwt = 0;
							$net_wt = 0;
							$total_piece = 0;
							$total_net_wt = 0;
							$tag_net_wt = 0;
							$making_charge = 0;
							$total_tax = 0;
							$sub_total = 0;
							$paid_advance   = 0;
							$paid_weight    = 0;
							$wt_amt         = 0;
							$tot_adv_paid   = 0;
							$chit_amount    = 0;
							$total_chit_amount    = 0;
							if (sizeof($est_other_item['advance_details']) > 0) {
								foreach ($est_other_item['advance_details'] as $advance) {
									$paid_advance += $advance['paid_advance'];
									$paid_weight += $advance['paid_weight'];
									$wt_amt += ($advance['paid_weight']);
								}
								$tot_adv_paid = number_format(($paid_advance + $wt_amt), 2, '.', '');
							}
							if (sizeof($est_other_item['chit_details']) > 0) {
								foreach ($est_other_item['chit_details'] as $advance) {
									$chit_amount += $advance['utl_amount'];
								}
							}
							$item_no = 1;
							foreach ($est_other_item['item_details'] as $items) {
								$making_charge = 0;
								$stone_price = 0;
								$stone_weight = 0;
								$stone_piece = 0;
								$charge_price = 0;
								$tag_other_itm_amount = 0;
								$certification_cost = 0;
								$tag_other_itm_grs_weight = 0;
								$total_piece += $items['piece'];
								$total_gwt += $items['gross_wt'];
								$total_wt += $items['net_wt'];
								$tot_payable += $items['item_cost'];
								$market_rate_cost += $items['market_rate_cost'];
								$payable_without_tax = $items['item_cost'] - $items['item_total_tax'];
								$sub_total += $payable_without_tax;
								$total_tax += $items['item_total_tax'];
								if ($items['is_partial'] == 1 && $items['isTagsplitted']==0) {
									$net_wt += $items['net_wt'];
									$tag_net_wt += $items['tag_net_wt'];
								}
								if ($items['calculation_based_on'] == 0) {
									$wast_wgt = number_format((($items['gross_wt']) * ($items['wastage_percent'] / 100)), 3, '.', '');
									$making_charge = ($items['mc_type'] == 2 ? $items['gross_wt'] * $items['mc_value'] : $items['mc_value'] * 1);
								} else if ($items['calculation_based_on'] == 1) {
									$wast_wgt = number_format((($items['net_wt']) * ($items['wastage_percent'] / 100)), 3, '.', '');
									$making_charge = ($items['mc_type'] == 2 ? $items['net_wt'] * $items['mc_value'] : $items['mc_value'] * 1);
								} else if ($items['calculation_based_on'] == 2) {
									$wast_wgt = number_format((($items['net_wt']) * ($items['wastage_percent'] / 100)), 3, '.', '');
									$making_charge = ($items['mc_type'] == 2 ? $items['gross_wt'] * $items['mc_value'] : $items['mc_value'] * 1);
								}
								foreach ($items['stone_details'] as $stone) {
									//$stone+=$stone['stone_name'];
									$stone_price += $stone['price'];
									$stone_weight += $stone['wt'];
									$stone_piece += $stone['pieces'];
									$certification_cost += $stone['certification_cost'];
									$cost = $stone['price'] / $stone['wt'];
								}
								foreach ($items['charges'] as $charge) {
									$charge_price += $charge['amount'];
								}
								foreach ($items['other_metal_details'] as $other_metal_details) {
									$tag_other_itm_grs_weight += $other_metal_details['tag_other_itm_grs_weight'];
									$tag_other_itm_amount += $other_metal_details['tag_other_itm_amount'];
								}
							?>
								<tr>
									<td colspan="4" style="font-weight: bold;">
										<?php if ($items['item_type'] != 2) {
											echo $items['section_name'] != '' ? 'Counter : ' . $items['section_name'] : '';
										} else {
											echo $items['est_sec_code'] != '' ? 'Counter : ' . $items['est_sec_code'] : '';
										} ?>
									</td>
								</tr>
								<tr style="margin-top:2px !important;">
									<td colspan="5" class="item_name"><?php echo $item_no; ?>) <?php echo substr($items['design_name'], 0, 15) . ($items['est_rate_per_grm'] != '' ? ' @ Rate : ' . $items['est_rate_per_grm'] : ''); ?> </td>
								</tr>
								<tr>
									<td colspan="4"><?php echo $items['old_tag_id'] != '' ? 'Tag No: ' . $items['old_tag_id'] : $items['tag_code'] ?></td>
								</tr>
								<tr>
									<th></th>
									<th class="alignCenter" width="25%"> G.WT</th>
									<th class="alignCenter" width="25%">Net.WT</th>
									<th class="alignCenter" width="30%">VA(%)</th>
									<th class="alignCenter" width="30%">MC</th>
									<th class="alignCenter" width="30%">VALUE</th>
								</tr>
								<tr style="margin-top:2px !important;">
									<td></td>
									<td class="alignRight"><?php echo number_format($items['gross_wt'], 3, '.', ''); ?></td>
									<td class="alignRight"><?php echo number_format($items['net_wt'], 3, '.', ''); ?></td>
									<td class="alignRight"><?php echo number_format($wast_wgt * $items['est_rate_per_grm'], 2, '.', ''); ?></td>
									<td class="alignRight"><?php echo (number_format($making_charge, 2, '.', '')) ?> </td>
									<td class="alignRight"><?php echo (number_format($items['item_cost'] - $charge_price - $items['item_total_tax'] - $stone_price - $tag_other_itm_amount, 2, '.', '')); ?></td>
									<br>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td class="alignRight">(<?php echo ($items['wastage_percent']) . '%)'; ?></td>
									<td class="alignRight">(<?php echo number_format($items['mc_value']) . ($items['mc_type'] == 2 ? '/gm' : '/Pcs')  ?>)</td>
									<td></td>
								</tr>
								<?php if (sizeof($items['other_metal_details']) > 0) { ?>
									<?php foreach ($items['other_metal_details'] as $other_metal_details) {
									?>
										<tr>
											<td colspan="4"><?php echo $other_metal_details['metal'] . ' - (Rs. ' . (($other_metal_details['tag_other_itm_rate'])) . '/GM)' ?></td>
										</tr>
										<tr>
											<td></td>
											<td><?php echo $other_metal_details['tag_other_itm_grs_weight'] ?></td>
											<td class="alignRight"><?php echo $other_metal_details['tag_other_itm_grs_weight'] ?></td>
											<td class="alignRight"><?php echo $other_metal_details['tag_other_itm_wastage'] ?></td>
											<td class="alignRight"><?php echo $other_metal_details['tag_other_itm_mc'] ?></td>
											<td class="alignRight"><?php echo (number_format($other_metal_details['tag_other_itm_amount'], 2, '.', '')) ?></td>
											<td class="alignRight"><br><br></td>
										</tr>
								<?php }
								} ?>
								<?php if (sizeof($items['stone_details']) > 0) { ?>
									<?php foreach ($items['stone_details'] as $stone) {
									?>
										<tr>
											<td colspan="3"><?php echo $stone['stone_name'] ?></td>
											<td colspan="2"><?php echo number_format($stone['wt'], 3, '.', '') . $stone['uom_short_code'] . ' @ ' . $stone['rate_per_gram'] . '/-' ?></td>
											<td class="alignRight"><?php echo ($stone['price']) ?></td>
										</tr>
								<?php }
								} ?>
								<?php if (sizeof($items['charges']) > 0) { ?>
									<?php foreach ($items['charges'] as $charge) {
									?>
										<tr>
											<td colspan="3"><?php echo $charge['code_charge'] . ' Charges' ?></td>
											<td></td>
											<td></td>
											<td class="alignRight"><?php echo (number_format($charge['amount'], 2, '.', '')); ?></td>
										</tr>
								<?php }
								} ?>
								<?php if ($certification_cost > 0) { ?>
									<tr>
										<td>CERTIFICATION CHARGE</td>
										<td></td>
										<td></td>
										<td></td>
										<td class="alignRight"><?php echo (number_format($certification_cost, 2, '.', '')); ?></td>
									</tr>
								<?php } ?>
								<tr style="display:none">
									<td><?php echo $items['tgrp_name']; ?></td>
									<td></td>
									<td></td>
									<td class="alignRight"><?php echo (number_format($items['item_total_tax'], 2, '.', '')); ?></td>
								</tr>
								<tr>
									<td>
										<hr class="tot_dashed">
									</td>
								</tr>
							<?php $item_no++;
							} ?>
							<tr>
								<td colspan="4">SUB TOTAL</td>
								<td></td>
								<td class="alignRight"><?php echo (number_format($sub_total, 2, '.', '')); ?></td>
							</tr>
							<?php if ($estimation['is_eda'] != 1) { ?>
								<tr>
									<td colspan="4">CGST <?php echo $items['tax_percentage'] / 2 ?>%</td>
									<td></td>
									<td class="alignRight"><?php echo (number_format($total_tax / 2, 2, '.', '')); ?></td>
								</tr>
							<?php } ?>
							<?php if ($estimation['is_eda'] != 1) { ?>
								<tr>
									<td colspan="4">SGST <?php echo $items['tax_percentage'] / 2 ?>%</td>
									<td></td>
									<td class="alignRight"><?php echo (number_format($total_tax / 2, 2, '.', '')); ?></td>
								</tr>
							<?php } ?>
							<tr>
								<td>
									<hr class="tot_dashed">
								</td>
							</tr>
							<?php if ($tot_adv_paid > 0) { ?>
								<tr style="font-weight:bold;">
									<td class="">SUB TOTAL</td>
									<td class=""></td>
									<td class=""></td>
									<td></td>
									<td class="alignRight">
										<?php echo (number_format($tot_payable, 2, '.', '')); ?></td>
								</tr>
								<tr>
									<td>
										<hr class="tot_dashed">
									</td>
								</tr>
								<tr>
									<td>Adv Paid</td>
									<td></td>
									<td></td>
									<td></td>
									<td class="alignRight"><?php echo (number_format($tot_adv_paid, 2, '.', '')); ?></td>
								</tr>
								<tr>
									<td>
										<hr class="tot_dashed">
									</td>
								</tr>
							<?php } ?>
							<tr style="font-weight:bold;">
								<td></td>
								<td class="alignRight"><?php echo number_format($total_gwt, 3, '.', ''); ?></td>
								<td class="alignRight"><?php echo number_format($total_wt, 3, '.', ''); ?></td>
								<td class="alignRight" style="padding-right:14px"><?php echo $total_piece; ?></td>
								<td></td>
								<td class="alignRight">
									<?php echo (number_format($tot_payable, 2, '.', '')); ?></td>
							</tr>
							
							<?php if ($chit_amount > 0) { ?>
								<tr>
									<td>
										<hr class="tot_dashed">
									</td>
								</tr>
								<?php
								$i = 1;
								$total_weight_amount        = 0;
								$total_wastage_amount       = 0;
								$total_making_charge_amount = 0;
								$total_chit_amount_scheme   = 0;
								$closing_add_chgs   = 0;
								$additional_benefits   = 0;
								foreach ($est_other_item['chit_details'] as $chit) {
									if ($chit['scheme_type'] == 0) {
										$total_chit_amount_scheme += $chit['utl_amount'];
									}
									$weight_amount = 0;
									$wastage_amount = 0;
									$mc_amount = 0;
									if ($chit['scheme_type'] == 2 || $chit['scheme_type'] == 3 || $chit['scheme_type'] == 1) { ?>
										<?php
										if ($chit['closing_weight'] > 0) {
											$total_weight_amount += number_format($chit['closing_weight'] * $estimation['goldrate_22ct'], 2, '.', '');
											$weight_amount = number_format($chit['closing_weight'] * $estimation['goldrate_22ct'], 2, '.', '');
										?>
											
											<tr>
												<td colspan="3"><b><?php echo $i . '. ACC NO - ' . $chit['scheme_acc_number']; ?></b></td>
											</tr>
											<tr>
												<td colspan="2">Saved Weight</td>
												<td class="alignRight"><?php echo $chit['closing_weight']; ?></td>
												<td></td>
												<td class="alignRight"><?php echo ($weight_amount); ?></td>
											</tr>
										<?php } else {
											$total_chit_amount_scheme += $chit['utl_amount'];
										?>
											<tr>
												<td colspan="3"><?php echo $i . '. ACC NO - ' . $chit['scheme_acc_number']; ?></b></td>
												<td class="alignCenter"></td>
												<td></td>
												<td class="alignRight"><?php echo ($chit['utl_amount']); ?></td>
											</tr>
										<?php } ?>
										<?php
										if ($chit['wastage_per'] > 0) {
											$total_wastage_amount += number_format($chit['savings_in_wastage'] * $estimation['goldrate_22ct'], 2, '.', '');
											$wastage_amount = number_format($chit['savings_in_wastage'] * $estimation['goldrate_22ct'], 2, '.', '');
										?>
											<tr>
												<td colspan="2">Saved V.A(<?php echo $chit['wastage_per'] . '%' ?>)</td>
												<td class="alignRight"><?php echo $chit['savings_in_wastage']; ?></td>
												<td></td>
												<td class="alignRight"><?php echo ($wastage_amount); ?></td>
											</tr>
										<?php }
										?>
										<?php
										if ($chit['savings_in_making_charge'] > 0) {
											$mc_amount = number_format($chit['savings_in_making_charge'], 2, '.', '');
											$total_making_charge_amount += number_format($chit['savings_in_making_charge'], 2, '.', '');
										?>
											<tr>
												<td colspan="3">Total Saved MC</td>
												<td></td>
												<td class="alignRight"><?php echo ($mc_amount); ?></td>
											</tr>
										<?php }
										?>
										<?php
										if ($chit['additional_benefits'] > 0) {
											$additional_benefits = $chit['additional_benefits'];
										?>
											<tr>
												<td colspan="2">Benefits</td>
												<td class="alignRight"></td>
												<td></td>
												<td class="alignRight"><?php echo ($chit['additional_benefits']); ?></td>
											</tr>
										<?php }
										?>
										<?php
										if ($chit['closing_add_chgs'] > 0) {
											$closing_add_chgs = $chit['closing_add_chgs'];
										?>
											<tr>
												<td colspan="2">Deduction</td>
												<td class="alignRight"></td>
												<td></td>
												<td class="alignRight"><?php echo ($chit['closing_add_chgs']); ?></td>
											</tr>
										<?php }
										?>
									<?php $i++;
									} else if ($chit['scheme_type'] == 0) { ?>
										<tr>
											<td><?php echo $i . '. ACC NO - ' . $chit['scheme_acc_number']; ?></td>
											<td></td>
											<td></td>
											<td></td>
											<td class="alignRight"><?php echo (number_format($chit['utl_amount'], 2, '.', '')); ?></td>
										</tr>
									<?php $i++;
									} ?>
									<tr>
										<td>
											<hr class="tot_dashed">
										</td>
									</tr>
								<?php } ?>
								<?php
								$total_chit_amount = number_format($total_weight_amount + $total_making_charge_amount + $total_wastage_amount + $total_chit_amount_scheme - $closing_add_chgs + $additional_benefits, 2, '.', '')
								?>
								<tr style="font-weight:bold;">
									<td class="">SUB TOTAL</td>
									<td class="alignRight"></td>
									<td class="alignRight" style="padding-right:14px"></td>
									<td></td>
									<td></td>
									<td class="alignRight"><?php echo (number_format($total_chit_amount, 2, '.', '')); ?></td>
								</tr>
							<?php } ?>
							<tr>
								<td>
									<hr class="tot_dashed">
								</td>
							</tr>
						</tbody>
					</table>
					<?php if ($tag_net_wt != 0) { ?>
						<div>
							<label>PARTLY (<?php echo number_format($tag_net_wt, 3, '.', '') . '-' . number_format($net_wt, 3, '.', '') ?>): <?php echo number_format(($tag_net_wt - $net_wt), 3, '.', ''); ?></label>
						</div>
				<?php }
				} ?>
				<?php
				if (sizeof($est_other_item['old_matel_details']) > 0) { ?>
					<hr class="item_dashed">
					<div style="height:-10px;">
						<p style="text-transform:uppercase;text-align:left; font-weight: bold; line-height:2px;">purchase items</p>
					</div>
					<table class="purchase" width="120%">
						<tr>
							<td>
								<hr class="item_dashed">
							</td>
						</tr>
						<tr>
							<th width="20%">METAL</th>
							<th width="10%" class="alignRight">G.WT</th>
							<th width="10%" class="alignRight">N.WT</th>
							<th width="15%" class="alignRight">V.A</th>
							<th width="20%" class="alignRight">RATE</th>
							<th width="25%" class="alignRight">VALUE</th>
						</tr>
						<tr>
							<td>
								<hr class="item_dashed">
							</td>
						</tr>
						<tbody>
							<?php
							$gross_wt = 0;
							$total_va = 0;
							$amount = 0;
							foreach ($est_other_item['old_matel_details'] as $data) {
								$gross_wt += $data['gross_wt'];
								$net_wt += $data['net_wt'];
								$total_va += $data['wastage_wt'];
								$amount += $data['amount'];
								foreach ($data['stone_details'] as $stn) {
									$tot_stone_amt += $stn['price'];
								}
							?>
								<tr>
									<td><?php echo $data['old_metal_type']; ?></td>
									<td class="alignRight"><?php echo $data['gross_wt']; ?></td>
									<td class="alignRight"><?php echo $data['net_wt']; ?></td>
									<td class="alignRight"><?php echo $data['wastage_wt']; ?></td>
									<td class="alignRight"><?php echo $data['rate_per_gram']; ?></td>
									<td class="alignRight"><?php echo ($data['amount'] - $tot_stone_amt); ?></td>
								</tr>
								<?php if (sizeof($data['stone_details']) > 0 && $data['old_stone_set'] == 1) { ?>
									<?php foreach ($data['stone_details'] as $stone) {
										$stone_rate = $stone['price'] / $stone['wt'];
									?>
										<tr>
											<td><?php echo $stone['stone_name']; ?></td>
											<td colspan="3"><?php echo number_format($stone['wt'], 3, '.', '') . $stone['uom_short_code'] . '*' . number_format($stone_rate) ?></td>
											<td class="alignRight"></td>
											<td class="alignRight"><?php echo (number_format($stone['price'], 2, '.', '')) ?></td>
										</tr>
								<?php }
								} ?>
								<tr>
									<td>
										<hr class="item_dashed">
									</td>
								</tr>
							<?php } ?>
						</tbody>
						<tr>
							<td></td>
							<td class="alignRight"><?php echo number_format($gross_wt, 3, '.', ''); ?></td>
							<td class="alignRight"><?php echo number_format($net_wt, 3, '.', ''); ?></td>
							<td class="alignRight"><?php echo number_format($total_va, 3, '.', ''); ?></td>
							<td class="alignRight"></td>
							<td class="alignRight"><?php
													$tot_purchase = number_format($amount, 2, '.', '');
													echo ($tot_purchase);
													?>
							</td>
						</tr>
						<tr>
							<td>
								<hr class="item_dashed">
							</td>
						</tr>
					</table>
				<?php } ?>
				<table class="" width="115%">
					<tbody>
						<?php if ($tot_payable != 0) { ?>
							<tr>
								<td>
								<td>
								<td class="alignRight">Sales :</td>
								<td class="alignRight"><?php echo (number_format(($tot_payable), 2, '.', '')); ?></td>
							</tr>
						<?php } ?>
						<?php if ($tot_purchase != 0) { ?>
							<tr>
								<td>
								<td colspan="2" class="alignRight">Purchase :</td>
								<td class="alignRight"> <?php echo (number_format(($tot_purchase), 2, '.', '')); ?></td>
							</tr>
						<?php } ?>
						<?php if ($total_chit_amount != 0) { ?>
							<tr>
								<td>
								<td colspan="2" class="alignRight">CHIT :</td>
								<td class="alignRight"> <?php echo (number_format(($total_chit_amount), 2, '.', '')); ?></td>
							</tr>
						<?php } ?>
						<?php if ($tot_adv_paid != 0) { ?>
							<tr>
								<td>
								<td class="alignRight">ADVANCE :</td>
								<td class="alignRight"> <?php echo (number_format(($tot_adv_paid), 2, '.', '')); ?></td>
							</tr>
						<?php } ?>
						<tr style="font-weight:bold;">
							<td>
							<td colspan="2" class="alignRight">Total :</td>
							<td class="alignRight"> <?php echo (number_format(($tot_payable - $tot_purchase - $total_chit_amount), 2, '.', '')); ?></td>
						</tr>
					</tbody>
				</table>
				<p class="alignRight finalTotal">Rs. <?php echo (number_format(round($tot_payable - $tot_purchase - $chit_amount - $tot_adv_paid), 2, '.', '')); ?></p>
				<div>
					<label style="text-transform: capitalize;">The estimate value may changes as per the gold rate</label><br><br>
					<label>EMP Code&nbsp;&nbsp;&nbsp;: <?php echo $estimation['emp_code'] . '(' . $estimation['emp_name'] . ')'; ?></label><br>
					<label>Signature&nbsp;:</label><br><br>
					<label>Customer Name : <?php echo $estimation['customer_name']; ?> </label><br>
					<label>Mobile : <?php echo $estimation['mobile']; ?> </label><br>
				</div>
			</div>
		</div>
	</span>
</body>
</html>