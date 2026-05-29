<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Estimation</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/estimation_konika.css">
	<style type="text/css">
		@page {
			size: 75mm 600mm;
		}
		
		body {
			margin-left: 1% !important;
		}

		.table_width {
			width: 98% !important;
		}
	</style>
</head>
<?php
function moneyFormatIndia($num)
{
	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
}
$tax_perc = "";

?>

<body>

	<div class="PDFReceipt">
		<div class="printable">

		<?php $is_eda = $estimation['is_eda'];  ?> 

			<div class="header heading">

				<h3>Estimate : <?php echo $estimation['esti_no']; ?></h3>

			</div>

			<div class="top_head">

				<div class="cus_name heading">

					<span><?php echo $estimation['customer_name'] . ($estimation['village_name'] != '' ? ' / ' . $estimation['village_name'] : '') . ' / ' . $estimation['mobile']; ?> </span>

				</div>

				<div class="top_info">

					<div class="rates">

						<div class="rows">
							<div class="left_col">Gold 22K : </div>
							<div class="right_col"> <?php echo number_format($estimation['goldrate_22ct'], 2, '.', '') ?></div>
						</div>

						<div class="rows">
							<div class="left_col">Gold 18K : </div>
							<div class="right_col"> <?php echo number_format($metal_rates['goldrate_18ct'], 2, '.', '') ?></div>
						</div>

						<div class="rows">
							<!-- <div class="left_col">Silver &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : </div> <div class="right_col"> <?php echo number_format($estimation['silverrate_1gm'], 2, '.', '') ?></div> -->
						</div>

					</div>

					<div class="estimation_datetime">

						<div class="rows">
							<div class="left_col">Est No : </div>
							<div class="right_col"> <?php echo $estimation['esti_no'] ?></div>
						</div>

						<div class="rows">
							<div class="left_col">Date &nbsp;&nbsp; : </div>
							<div class="right_col"> <?php echo date('d-m-Y', strtotime($estimation['estimation_datetime'])); ?></div>
						</div>

						<div class="rows">
							<div class="left_col">Time &nbsp;&nbsp; : </div>
							<div class="right_col"> <?php echo date('h:i:s A', strtotime($estimation['estimation_datetime'])); ?></div>
						</div>

					</div>

				</div>
			</div>
			<div style="width: 100%;">


				<?php if (sizeof($est_other_item['item_details'])) { ?>
					<div class="table_width">
						<table style="width: 100%">


							<tr>
								<td colspan="5" class="_dashed"></td>
							</tr>
							<tr class="heading">

								<!-- <th  class="alignLeft" style="width:10%"> DESC</th> -->

								<th class="alignCenter" style="width:20%">GWT</th>

								<th class="alignRight" style="width:25%"><?php echo($is_eda == 1 ?'PURE':'NWT'); ?></th>

								<th class="alignRight" style="width:20%">VA</th>

								<th colspan="2" class="alignRight" style="width:35%">AMOUNT</th>

							</tr>

							<tr>
								<td colspan="5"></td>
							</tr>

							<tr>
								<td colspan="5" class="_dashed"></td>
							</tr>


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

								$pure_wt += $items['pure_wt'];


								$tot_payable += $items['item_cost'];


								$market_rate_cost += $items['market_rate_cost'];


								$payable_without_tax = $items['item_cost'] - $items['item_total_tax'];


								$sub_total += $payable_without_tax;


								$total_tax += $items['item_total_tax'];


								if ($items['is_partial'] == 1 && $items['isTagsplitted'] == 0) {

									$net_wt += $items['net_wt'];

									$tag_net_wt += $items['tag_net_wt'];
								}

								if ($items['calculation_based_on'] == 0) {

									$wast_wgt = number_format((($items['gross_wt']) * ($items['wastage_percent'] / 100)), 2, '.', '');



									$making_charge = ($items['mc_type'] == 2 ? $items['gross_wt'] * $items['mc_value'] : $items['mc_value'] * $items['piece']);
								} else if ($items['calculation_based_on'] == 1) {

									$wast_wgt = number_format((($items['net_wt']) * ($items['wastage_percent'] / 100)), 2, '.', '');



									$making_charge = ($items['mc_type'] == 2 ? $items['net_wt'] * $items['mc_value'] : $items['mc_value'] * $items['piece']);
								} else if ($items['calculation_based_on'] == 2) {

									$wast_wgt = number_format((($items['net_wt']) * ($items['wastage_percent'] / 100)), 3, '.', '');



									$making_charge = ($items['mc_type'] == 2 ? $items['gross_wt'] * $items['mc_value'] : $items['mc_value'] * 1);
								}

								$wastage_value = round($wast_wgt * $items['est_rate_per_grm'], 2);

								foreach ($items['stone_details'] as $stone) {

									//$stone+=$stone['stone_name'];

									$stone_price += $stone['price'];

									$stone_weight += $stone['wt'];

									$stone_piece += $stone['pieces'];

									$certification_cost += $stone['certification_cost'];
								}



								foreach ($items['charges'] as $charge) {

									$charge_price += $charge['amount'];
								}



								foreach ($items['other_metal_details'] as $other_metal_details) {

									$tag_other_itm_grs_weight += $other_metal_details['tag_other_itm_grs_weight'];

									$tag_other_itm_amount += $other_metal_details['tag_other_itm_amount'];
								}

								$huid = "";

								if ($items['hu_id'] != "-" && $items['hu_id'] != "") {

									$huid = $huid . $items['hu_id'];
								}

								if ($items['hu_id'] != "-" && $items['hu_id'] != "" && $items['hu_id2'] != "-" && $items['hu_id2'] != "") {

									$huid = $huid . ",";
								}

								if ($items['hu_id2'] != "-" && $items['hu_id2'] != "") {

									$huid = $huid . $items['hu_id2'];
								}




							?>

								<tr>
									<td colspan="5" class="item_name heading">

										<?php echo $item_no.')'; ?>

										<?php //echo ($items['purname'] != "" ? "(". $items['purname'].")" : ""); 
										?>

										<?php echo substr($items['product_name'], 0, 10) . " " . substr($items['sub_design_name'], 0, 10);
										if ($items['calculation_based_on'] != 3) { ?> @ <?php echo $is_eda == 1 
											? ($items['pure_rate'] ?? '') 
											: ($items['est_rate_per_grm'] ?? ''); } ?>
																					
									</td>

								</tr>

								<tr>

									<!-- <td class="alignLeft"><?php echo $items['wastage_percent'] > 0 && $items['wastage_percent'] <= $wastage_limit_in_estimation['value'] ? ($items['wastage_percent'] + 0) . "%" : ($items['sales_mode'] == 1 ? "PCS : " . $items['piece'] : "") ?></td> -->

									<td class="alignRight"><?php echo $items['gross_wt'] != 0 ? number_format($items['gross_wt'], 3, '.', '') . ($items['pro_uom'] != '' ? '-' . $items['pro_uom'] : '') : ""; ?></td>

									<td class="alignRight"><?php echo ($is_eda?($items['pure_wt']):($items['net_wt'] != 0 ? number_format($items['net_wt'], 3, '.', '') . ($items['pro_uom'] != '' ? '-' . $items['pro_uom'] : '') : "")); ?></td>

									<!-- <td class="alignRight"></td> -->
										<?php


										// if ($items['net_wt'] >= 1) {
											// $va_g = round(($wastage_value + $making_charge)/$items['net_wt'],0);
											$va_g = number_format(($wastage_value + $making_charge) / $items['est_rate_per_grm'], 3);
											$va_p = round((($va_g / $items['net_wt']) * 100), 0);
											// echo $va_p > 0 ? $va_p . "%" : "";
											$purity = round($items['purity']);
											$va_total = $va_p + $purity;
											// echo ($va_p > 0 ? $va_total  : "");

										// } else {
										// 	echo round($wastage_value + $making_charge, 0);
										// }
										?>
									
									<td class="alignRight">
										    <?php 
											
										        echo $is_eda == 1 
										            ? number_format($va_total, 2) 
										            : ($va_p > 0 ? number_format($va_p, 2) . '%' : '');
											
										    ?>
									</td>
									<td colspan="2" class="alignRight"><?php echo moneyFormatIndia(number_format($items['item_cost'] - $charge_price - $items['item_total_tax'] - $tag_other_itm_amount - $stone_price, 2, '.', '')); ?></td>

								</tr>

								<?php if (sizeof($items['other_metal_details']) > 0) { ?>

									<?php
									$oth_va_amt = 0;
									$oth_mc_va = 0;
									$oth_mc_va_pg = 0;
									foreach ($items['other_metal_details'] as $other_metal_details) {

										// other metal calc
										$oth_va_amt = number_format(($other_metal_details['tag_other_itm_grs_weight'] * $other_metal_details['tag_other_itm_wastage']) / 100, 2, '.', '');
										$oth_mc_va = number_format(($other_metal_details['tag_other_itm_mc'] + $oth_va_amt), 2, '.', '');

										$oth_mc_va_pg = number_format($oth_mc_va / $other_metal_details['tag_other_itm_grs_weight'], 2, '.', ''); ?>

										<tr>

											<td colspan="5"><?php echo $other_metal_details['metal'] . ' - (Rs. ' . (moneyFormatIndia($other_metal_details['tag_other_itm_rate'])) . '/GM)' ?></td>

										</tr>

										<tr>
											<td></td>

											<td><?php echo $other_metal_details['tag_other_itm_grs_weight'] ?></td>

											<td class="alignLeft"><?php echo $other_metal_details['tag_other_itm_grs_weight'] ?></td>

											<td class="alignRight"><?php echo number_format($oth_mc_va_pg, 2, '.', '') ?></td>

											<td class="alignRight"><?php echo moneyFormatIndia(number_format($other_metal_details['tag_other_itm_amount'], 2, '.', '')) ?></td>

										</tr>

								<?php }
								} ?>

								<?php if (sizeof($items['stone_details']) > 0) { ?>

									<?php foreach ($items['stone_details'] as $stone) {

									?>

										<tr>

											<td colspan="1" style="text-transform:uppercase;"><?php echo $stone['stone_name'] ?></td>

											<td colspan="3" style="padding-left:2rem;"><?php echo number_format($stone['wt'], 3, '.', '') . $stone['uom_short_code'] . ' @ ' . $stone['rate_per_gram'] . '/-' ?></td>

											<td class="alignRight"><?php echo moneyFormatIndia($stone['price']) ?></td>

										</tr>


								<?php }
								} ?>

								<?php if (($items['est_stone_wt'] != $items['tag_stone_wt']) && $items['tag_id'] && ($items['isTagsplitted'] == 0)) { ?>

									<tr>
										<td colspan="5">PARTLY STONE(<?php echo number_format($items['tag_stone_wt'], 3, '.', '') . '-' . number_format($items['est_stone_wt'], 3, '.', '') ?>): <?php echo number_format(($items['tag_stone_wt'] - $items['est_stone_wt']), 3, '.', '') ?></td>
									</tr>
								<?php } ?>


								<?php if (sizeof($items['charges']) > 0) { ?>

									<?php foreach ($items['charges'] as $charge) {

									?>

										<tr>

											<td colspan="2" style="text-transform:uppercase;"><?php echo $charge['code_charge'] . ' Charges' ?></td>

											<td></td>

											<td class="alignRight" colspan="2"><?php echo moneyFormatIndia(number_format($charge['amount'], 2, '.', '')); ?></td>

										</tr>

								<?php }
								} ?>

								<?php if ($certification_cost > 0) { ?>

									<tr>

										<td colspan="2">CERTIFICATION CHARGE</td>

										<td></td>

										<td class="alignRight" colspan="2"><?php echo moneyFormatIndia(number_format($certification_cost, 2, '.', '')); ?></td>

									</tr>

								<?php } ?>

								<tr style="display:none">

									<td colspan="2"><?php echo $items['tgrp_name']; ?></td>

									<td></td>

									<td></td>

									<td class="alignRight"><?php echo moneyFormatIndia(number_format($items['item_total_tax'], 2, '.', '')); ?></td>

								</tr>

								<?php if ($items['tag_net_wt'] != 0 && ($items['tag_net_wt'] - $items['net_wt'] != 0) && $items['isTagsplitted'] == 0) { ?>

									<tr>

										<td colspan="5">PARTLY (<?php echo number_format($items['tag_net_wt'], 3, '.', '') . '-' . number_format($items['net_wt'], 3, '.', '') ?>): <?php echo number_format(($items['tag_net_wt'] - $items['net_wt']), 3, '.', ''); ?></td>

									</tr>

								<?php }

								if ($huid != "") { ?>

									<tr>

										<td colspan="5"><?php echo "HUID: " . $huid ?></td>

									</tr>

								<?php } ?>

								<tr>
									<td colspan="5"></td>
								</tr>

								<tr>
									<td colspan="5"></td>
								</tr>

							<?php $item_no++;
							} ?>

							<tr>
								<td colspan="5" class="_dashed"></td>
							</tr>

							<tr>
								<td colspan="5"></td>
							</tr>

							<tr>

								<td class="alignLeft" colspan="2">SUB TOTAL</td>

								<td></td>

								<td class="alignRight" colspan="2"><?php echo moneyFormatIndia(number_format($sub_total, 2, '.', '')); ?></td>

							</tr>
							<?php if(!$is_eda){ ?>
							<tr>

								<td colspan="2">CGST <?php echo $items['tax_percentage'] / 2 ?>%</td>

								<td></td>

								<td class="alignRight" colspan="2"><?php echo moneyFormatIndia(number_format($total_tax / 2, 2, '.', '')); ?></td>

							</tr>

							<tr>

								<td colspan="2">SGST <?php echo $items['tax_percentage'] / 2 ?>%</td>

								<td></td>

								<td class="alignRight" colspan="2"><?php echo moneyFormatIndia(number_format($total_tax / 2, 2, '.', '')); ?></td>

							</tr>
							<?php } ?>
							<tr>
								<td colspan="5"></td>
							</tr>

							<tr>

								<td colspan="5" class="_dashed"></td>

							</tr>

							<tr>
								<td colspan="5"></td>
							</tr>

							<tr class="rowClass heading">

								<!-- <td class="alignLeft">TOTAL[<?php echo $total_piece; ?>]</td> -->

								<td class="alignRight"><?php echo $total_gwt != 0 ? number_format($total_gwt, 3, '.', '') : ""; ?></td>

								<!-- <td class="alignRight"><?php echo $total_wt != 0 ? number_format($total_wt, 3, '.', '') : ""; ?></td> -->

								<td class="alignRight">
										<?= 
												isset($is_eda) && $is_eda ? 
														(isset($pure_wt) && $pure_wt != 0 ? number_format($pure_wt, 3, '.', '') : "") : 
														(isset($total_wt) && $total_wt != 0 ? number_format($total_wt, 3, '.', '') : "")
										; ?>
								</td>


								<td class="alignRight"></td>


								<td class="alignRight" colspan="2"><?php echo moneyFormatIndia(number_format($tot_payable, 2, '.', '')); ?></td>

							</tr>

							<tr>
								<td colspan="5"></td>
							</tr>

							<tr>

								<td colspan="5" class="_dashed"></td>

							</tr>

							<?php /* if($tot_adv_paid>0){?>

							<tr class="heading" style="font-weight:bold;">

							<td colspan="2">SUB TOTAL</td>

							<td></td>

							<td class="alignRight" colspan="2">

							<?php echo moneyFormatIndia(number_format($tot_payable,2,'.','')); ?></td>

							</tr>

							<tr>

							<td colspan="5" class="_dashed"></td>

							</tr>

							<tr>

							<td >Adv Paid</td>

							<td></td>

							<td></td>

							<td class="alignRight" colspan="2"><?php echo moneyFormatIndia(number_format($tot_adv_paid,2,'.',''));?></td>

							</tr>

							<tr>

							<td colspan="5" class="_dashed"></td>

							</tr>

							<tr class="heading" style="font-weight:bold;">

							<td class="alignLeft"><?php echo number_format($total_gwt,3,'.','');?></td>

							<td class="alignRight"><?php echo number_format($total_wt,3,'.','');?></td>

							<td class="alignRight"><?php echo $total_piece;?></td>

							<td class="alignRight" colspan="2">

							<?php echo moneyFormatIndia(number_format($tot_payable-$tot_adv_paid,2,'.','')); ?></td>

							</tr>

							<tr>

							<td colspan="5" class="_dashed"></td>

							</tr>


							<?php } */ ?>


							<?php if ($chit_amount > 0) { ?>

								<tr>

									<td colspan="5" class="_dashed"></td>

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

										<tr>

											<td colspan="5"><b><?php echo $i . '. ACC NO - ' . $chit['scheme_acc_number']; ?></b></td>

										</tr>

										<?php

										if ($chit['closing_weight'] > 0 || $chit['utl_amount'] > 0) {

											if ($chit['closing_weight'] > 0) {

												$total_weight_amount += number_format($chit['closing_weight'] * ($chit['rate_per_gram'] > 0 ? $chit['rate_per_gram'] : $estimation['goldrate_22ct']), 2, '.', '');

												$weight_amount = number_format($chit['closing_weight'] * ($chit['rate_per_gram'] > 0 ? $chit['rate_per_gram'] : $estimation['goldrate_22ct']), 2, '.', '');

										?>
												<tr>

													<td colspan="2">Saved Weight</td>

													<td class="alignCenter"><?php echo $chit['closing_weight']; ?></td>

													<td></td>

													<td class="alignRight"><?php echo moneyFormatIndia($weight_amount); ?></td>

												</tr>
											<?php   } else {
												$weight_amount = $chit['utl_amount'];
												$total_weight_amount += $chit['utl_amount'];
											?>
												<tr>

													<td colspan="2"><?php echo $chit['scheme_acc_number']; ?></td>

													<td class="alignCenter"></td>

													<td></td>

													<td class="alignRight"><?php echo moneyFormatIndia($weight_amount); ?></td>

												</tr>
											<?php }	?>



										<?php } ?>



										<?php

										if ($chit['wastage_per'] > 0) {

											$total_wastage_amount += number_format($chit['savings_in_wastage'] * ($chit['rate_per_gram'] > 0 ? $chit['rate_per_gram'] : $estimation['goldrate_22ct']), 2, '.', '');

											$wastage_amount = number_format($chit['savings_in_wastage'] * ($chit['rate_per_gram'] > 0 ? $chit['rate_per_gram'] : $estimation['goldrate_22ct']), 2, '.', '');

										?>

											<tr>

												<td colspan="2">Saved V.A(<?php echo $chit['wastage_per'] . '%' ?>)</td>

												<td class="alignCenter"><?php echo $chit['savings_in_wastage']; ?></td>

												<td></td>

												<td class="alignRight"><?php echo moneyFormatIndia($wastage_amount); ?></td>

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

												<td class="alignRight"><?php echo moneyFormatIndia($mc_amount); ?></td>

											</tr>

										<?php }

										?>



										<?php

										if ($chit['additional_benefits'] > 0) {

											$additional_benefits = $chit['additional_benefits'];

										?>

											<tr>

												<td colspan="2">Benefits</td>

												<td class="alignCenter"></td>

												<td></td>

												<td class="alignRight"><?php echo moneyFormatIndia($chit['additional_benefits']); ?></td>

											</tr>



										<?php }

										?>



										<?php

										if ($chit['closing_add_chgs'] > 0) {

											$closing_add_chgs = $chit['closing_add_chgs'];

										?>

											<tr>

												<td colspan="2">Deduction</td>

												<td class="alignCenter"></td>

												<td></td>

												<td class="alignRight"><?php echo moneyFormatIndia($chit['closing_add_chgs']); ?></td>

											</tr>



										<?php }

										?>



									<?php $i++;
									} else if ($chit['scheme_type'] == 0) { ?>



										<tr>

											<td colspan="2"><?php echo $i . '. ACC NO - ' . $chit['scheme_acc_number']; ?></td>

											<td></td>

											<td></td>

											<td class="alignRight"><?php echo moneyFormatIndia(number_format($chit['utl_amount'], 2, '.', '')); ?></td>

										</tr>

									<?php $i++;
									} ?>

									<tr>

										<td colspan="5" class="_dashed"></td>

									</tr>

								<?php } ?>

								<?php

								$total_chit_amount = number_format($total_weight_amount + $total_making_charge_amount + $total_wastage_amount + $total_chit_amount_scheme - $closing_add_chgs + $additional_benefits, 2, '.', '')

								?>

								<tr class="heading" style="font-weight:bold;">

									<td class="">SUB TOTAL</td>

									<td class="alignRight"></td>

									<td class="alignRight"></td>

									<td></td>

									<td class="alignRight"><?php echo moneyFormatIndia(number_format($total_chit_amount, 2, '.', '')); ?></td>

								</tr>

							<?php } ?>



						</table>
					</div>
				<?php } ?>
			</div>
			<?php

			if (sizeof($est_other_item['old_matel_details']) > 0) { ?>

				<div class="old_estimate heading">

					OLD ESTIMATE

				</div>
				<div class="table_width">
					<table style="width: 100%">

						<tr>

							<td colspan="5" class="_dashed"></td>

						</tr>

						<tr>
							<td colspan="5"></td>
						</tr>

						<tr class="heading">

							<th width="20%" class="alignLeft">DESC</th>

							<th width="17%" class="alignRight">GWT</th>

							<!-- <th width="17%" class="alignRight">LWT</th> -->

							<th width="25%" class="alignRight">NT.WT</th>

							<!-- <th width="17%" class="alignRight"></th> -->

							<th width="20%" class="alignRight"></th>

							<th class="alignRight">AMOUNT</th>

						</tr>

						<tr>
							<td colspan="5"></td>
						</tr>

						<tr>

							<td colspan="5" class="_dashed"></td>

						</tr>

						<tbody>

							<?php

							$gross_wt = 0;
							
							$net_wt = 0;

							$total_va = 0;

							$amount = 0;

							$old_item = 1;

							foreach ($est_other_item['old_matel_details'] as $data) {

								$gross_wt += $data['gross_wt'];
								
								$net_wt += $data['net_wt'];

								$total_va += $data['wastage_wt'];

								$amount += $data['amount'];

								$stone_total = 0;

								if (sizeof($data['stone_details']) > 0) {

									foreach ($data['stone_details'] as $oldstn) {

										$stone_total += $oldstn['price'];
									}
								}

							?>

								<tr>

									<td colspan="5"></td>

								</tr>

								<tr class="item_name heading">

									<td colspan="5"><?php echo $old_item . ") " . $data['old_metal_type'] . " @ " . $data['rate_per_gram']; ?></td>

								</tr>

								<tr>

									<td></td>

									<td class="alignRight"><?php echo $data['gross_wt']; ?></td>

									<!-- <td class="alignRight">
										<?php
												if ($data['id_category'] == 2 && $data['wast_type'] == 1) {

													echo $data['wastage_percent'];
												} else {

													echo number_format($data['wastage_wt'] + $data['dust_wt'] + $data['stone_wt'], 3, '.', '');
												}
												?>

									</td> -->

									
									<!-- <td></td> -->

									<td class="alignRight"><?php echo $data['net_wt']; ?></td>

									<td></td>

									<td class="alignRight"><?php echo moneyFormatIndia(number_format($data['amount'] - $stone_total, 2, '.', '')); ?></td>

								</tr>

								<?php if ($data['dust_wt'] != 0 || $data['stone_wt'] != 0) { ?>

									<!-- <tr>

										<td colspan="2">DUST / STN WT</td>

										<td colspan="3"><?php echo $data['dust_wt'] . " / " . $data['stone_wt']; ?></td>

									</tr> -->

								<?php } ?>

								<?php if (sizeof($data['stone_details']) > 0) { ?>

									<?php foreach ($data['stone_details'] as $stone) {

									?>

										<tr>

											<td colspan="2" style="text-transform:uppercase;"><?php echo $stone['stone_name'] ?></td>

											<td colspan="2"><?php echo number_format($stone['wt'], 3, '.', '') . $stone['uom_short_code'] . ' @ ' . $stone['rate_per_gram'] . '/-' ?></td>

											<td class="alignRight"><?php echo moneyFormatIndia($stone['price']) ?></td>

										</tr>

								<?php }
								} ?>

							<?php $old_item++;
							} ?>

							<tr>
								<td colspan="5" class="_dashed"></td>

							</tr>

							<tr>
								<td colspan="5"></td>
							</tr>

						</tbody>

						<tr class="rowClass heading">

							<td></td>

							<td class="alignRight"><?php echo number_format($gross_wt, 3, '.', ''); ?></td>

							<td class="alignRight"><?php echo number_format($net_wt, 3, '.', ''); ?></td>

							<td></td>

							<td class="alignRight" colspan="2"><?php

																$tot_purchase = number_format($amount, 2, '.', '');

																echo moneyFormatIndia($tot_purchase);

																?>

							</td>

						</tr>

						<tr>
							<td colspan="5"></td>
						</tr>

						<tr>

							<td colspan="5" class="_dashed"></td>

						</tr>

					</table>
				</div>
			<?php } ?>

			<?php
			$count = 0;
			if ($tot_payable != 0) {
				$count++;
			}
			if ($tot_purchase != 0) {
				$count++;
			}
			if ($total_chit_amount != 0) {
				$count++;
			}
			if ($tot_adv_paid != 0) {
				$count++;
			}
			?>

			<?php if ($count >= 2) { ?>
				<div class="table_width">
					<table style="width: 100%">

						<tbody>

							<?php if ($tot_payable != 0) { ?>

								<tr>

									<td class="alignLeft">Sales</td>

									<td class="alignRight"><?php echo moneyFormatIndia(number_format(($tot_payable), 2, '.', '')); ?></td>

								</tr>

							<?php } ?>

							<?php if ($tot_purchase != 0) { ?>

								<tr>

									<td class="alignLeft">Purchase</td>

									<td class="alignRight"> <?php echo moneyFormatIndia(number_format(($tot_purchase), 2, '.', '')); ?></td>

								</tr>

							<?php } ?>

							<?php if ($total_chit_amount != 0) { ?>

								<tr>

									<td class="alignLeft">CHIT</td>

									<td class="alignRight"> <?php echo moneyFormatIndia(number_format(($total_chit_amount), 2, '.', '')); ?></td>

								</tr>

							<?php } ?>

							<?php if ($tot_adv_paid != 0) { ?>

								<tr>

									<td class="alignLeft">ADVANCE</td>

									<td class="alignRight"> <?php echo moneyFormatIndia(number_format(($tot_adv_paid), 2, '.', '')); ?></td>

								</tr>

							<?php } ?>

							<tr>

								<td colspan="2" class="_dashed"></td>

							</tr>

							<tr>
								<td colspan="5"></td>
							</tr>

							<tr class="heading" style="font-weight:bold;">

								<td class="alignLeft">Grand Total</td>

								<td class="alignRight"> <?php echo moneyFormatIndia(number_format(($tot_payable - $tot_purchase - $total_chit_amount - $tot_adv_paid), 2, '.', '')); ?></td>

							</tr>

							<tr>
								<td colspan="5"></td>
							</tr>

							<tr>

								<td colspan="2" class="_dashed"></td>

							</tr>

						</tbody>

					</table>
				</div>
			<?php } ?>

			<div class="emp_details" style="text-transform:uppercase;">

				<label>EMP : <?php echo $estimation['emp_code'] . ' / ' . $estimation['emp_name'] . ' / ' . date('h:i A', strtotime(date('d-m-Y H:i:s'))); ?></label>

			</div>

			<p class="alignCenter finalTotal heading">Total: Rs. <?php echo moneyFormatIndia(number_format((round($tot_payable - $tot_purchase - $chit_amount - $tot_adv_paid)), 2, '.', '')); ?></p>

			<p>&nbsp;</p>

			<p>&nbsp;</p>

			<p>&nbsp;</p>

			<p>&nbsp;</p>

			<p>.&nbsp;</p>

		</div>
	</div>

</body>

</html>