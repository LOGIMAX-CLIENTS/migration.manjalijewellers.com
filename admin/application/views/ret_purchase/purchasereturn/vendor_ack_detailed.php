<html>

<head>
	<meta charset="utf-8">

	<title>Purchase Return - <?php echo $order['pur_no']; ?> </title>

	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/purchase_return.css">

	<style>
		.addr_labels {
			display: inline-block;
			width: 30%;
			padding-bottom: 5px;
		}

		.addr_values {
			display: inline-block;
			padding-left: -5px;
		}

		.addr_brch_labels {
			display: inline-block;
			vertical-align: top;
			width: 40%;
			text-align: right;
		}

		.addr_brch_values {
			display: inline-block;
			vertical-align: top;
			width: 40%;
			text-align: left;
		}
	</style>
</head>

<body>
	<?php
	function moneyFormatIndia($num)
	{
		return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
	}
	?>
	<div class="PDFReceipt">
		<div class="heading">
			<div class="company_name"><?php echo strtoupper($comp_details['company_name']); ?></div>
			<div><?php echo strtoupper($comp_details['address1']) ?> , <?php echo strtoupper($comp_details['city']) ?></div>
			<div><?php echo strtoupper($comp_details['address2']) ?></div>
			<?php ($comp_details['email'] != '' ? '<div>Email : ' . $comp_details['email'] . ' :</div>' : '') ?>
			<?php ($comp_details['gst_number'] != '' ? '<div>GST No ' . $comp_details['gst_number'] . ' :</div>' : '') ?>
			<div><?php echo $comp_details['state'] ?></div>
		</div><br>

		<div style="width: 100%; text-transform:uppercase;height:140px;">
			<div style="display: inline-block; width: 35%;">
				<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;' . 'Mr./Ms.' . $issue['karigar_name'] . "</div>"; ?></label><br>
				<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;' . $issue['supplier_mobile'] . "</div>"; ?></label><br>
				<label><?php echo ($issue['address1'] != '' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($issue['address1']) . ',' . "</div><br>" : ''); ?></label>
				<label><?php echo ($issue['address2'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($issue['address2']) . ',' . "</div><br>" : ''); ?></label>
				<label><?php echo ($issue['city_name'] != '' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($issue['city_name']) . ($issue['pincode'] != '' ? ' - ' . $issue['pincode'] . '.' : '') . "</div><br>" : ''); ?></label>
				<label><?php echo ($issue['state_name'] != '' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($issue['state_name'] . '-' . $issue['state_name']) . ',' . "</div><br>" : ''); ?></label>
				<label><?php echo ($issue['country_name'] != '' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($issue['country_name']) . "</div><br>" : ''); ?></label>
				<label><?php echo (isset($issue['pan_no']) && $issue['pan_no'] != '' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($issue['pan_no']) . "</div><br>" : ''); ?></label>
				<label><?php echo (isset($issue['gst_number']) && $issue['gst_number'] != '' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($issue['gst_number']) . "</div><br>" : ''); ?></label>
			</div>


			<div style="display: inline-block; text-align: center !important; width: 29%; ">PURCHASE RETURN RECEIPT</div>

			<div style="width: 35%; display: inline-block;vertical-align: top;text-align: right">

				<label><?php echo ($issue['date_add'] != '' ? '<div class="addr_brch_labels">Receipt Date &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;' . $issue['date_add'] . "</div><br>" : ''); ?></label><br>
				<label><?php echo ($issue['pur_ret_ref_no'] != '' ? '<div class="addr_brch_labels">Ref No &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;' . $issue['pur_ret_ref_no'] . "</div><br>" : ''); ?></label>


			</div>
		</div><br>
		<div class="receipt_details">
			<table class="job_receipt_table">
				<thead>
					<tr>
						<th class="alignRight">S.No</th>
						<th style="text-align: center">DESCRIPTION OF GOODS</th>
						<th class="alignRight">HSN</th>
						<th class="alignRight">QUANTITY (GM)</th>
						<th class="alignRight">RATE PER GRAM</th>
						<th class="alignRight">AMOUNT(RS)</th>

					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$total_taxable_amt = 0;
					$cgst_cost = 0;
					$sgst_cost = 0;
					$cgst_iost = 0;
					$total_item_cost = 0;
					$total_charges = 0;
					foreach ($grn_details['gst_details'] as $gst) {
						$cgst_cost += $gst['cgst_cost'];
						$sgst_cost += $gst['sgst_cost'];
						$igst_cost += $gst['igst_cost'];
					}
					foreach ($grn_details['charge_details'] as $charge) {
						$total_charges += $charge['grn_charge_value'];
					}
					foreach ($receipt_details as $row) {
						$total_taxable_amt += $row['pur_ret_item_cost'] - $row['pur_ret_tax_value'];
						$total_item_cost += $row['pur_ret_item_cost'];
						foreach ($row['stone_details'] as $stn_details) {
							$stone_weight += $stn_details['wt'];
							$stone_cost += $stn_details['amount'];
						}

					?>
						<tr>
							<td class="alignRight" style="text-align: center;border-left: none;"><?php echo $i; ?></td>
							<td class="alignRight" style="text-align: center">&nbsp;<?php echo $row['product_name'] ?></td>
							<td class="alignRight"><?php echo ($row['hsn_code']); ?></td>
							<td class="alignRight"><?php echo  number_format($row['pur_ret_gwt'], 3, '.', '') ?></td>
							<td class="alignRight"><?php echo  $row['pur_ret_rate'] ?></td>
							<td class="alignRight"><?php echo moneyFormatIndia(number_format($row['pur_ret_item_cost'] - $row['pur_ret_tax_value'], 2, '.', '')) ?> </td>
						</tr>
					<?php $i++;
					}

					?>

					<tr>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
					</tr>




					<tr>
						<td class="rateborders" style="border-left: none"></td>
						<td class="rateborders"></td>
						<td class="rateborders"><strong>SUB TOTAL</strong></td>
						<td class="rateborders"></td>
						<td class="border-top: 1px solid #ccc;"></td>
						<td class="alignRight border-top: 1px solid #ccc;" style="font-weight:bold"><?php echo moneyFormatIndia($total_taxable_amt + $total_charges, 2, ".", "") ?></td>

					<tr>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
					</tr>

					</tr>
					<?php
					foreach ($gst_details as $gst) { ?>
						<?php
						if ($gst['cgst_cost'] > 0) { ?>
							<tr>
								<td class="rateborders" style="border-left: none"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="alignRight">CGST(<?php echo ($gst['pur_ret_tax_value'] / 2) ?> %)</td>
								<td class="alignRight"><?php echo moneyFormatIndia(number_format($gst['cgst_cost'], 2, ".", "")) ?></td>
							</tr>

							<tr>
								<td class="rateborders" style="border-left: none"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="alignRight">SGST(<?php echo ($gst['pur_ret_tax_value'] / 2) ?> %)</td>
								<td class="alignRight"><?php echo moneyFormatIndia(number_format($gst['sgst_cost'], 2, ".", "")) ?></td>
							</tr>

						<?php } else if ($gst['igst_cost'] > 0) { ?>
							<tr>
								<td class="rateborders" style="border-left: none"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="alignRight">IGST(<?php echo ($gst['pur_ret_tax_value']) ?> %)</td>
								<td class="alignRight"><?php echo moneyFormatIndia(number_format($gst['igst_cost'], 2, ".", "")) ?></td>
							</tr>
						<?php } ?>
					<?php } ?>

					<?php
					if ($issue['pur_ret_tcs_percent'] > 0) { ?>
						<tr>
							<td class="rateborders" style="border-left: none"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders">TCS(<?php echo ($issue['pur_ret_tcs_percent']) ?> %)</td>
							<td class="rateborders"><?php echo moneyFormatIndia($issue['pur_ret_tcs_value'], 2, ".", "") ?></td>
						</tr>
					<?php }
					?>

					<?php
					if ($issue['pur_ret_tds_percent'] > 0) { ?>
						<tr>
							<td class="rateborders" style="border-left: none"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders">TDS(<?php echo ($issue['pur_ret_tds_percent']) ?> %)</td>
							<td class="rateborders"><?php echo moneyFormatIndia($issue['pur_ret_tds_value'], 2, ".", "") ?></td>
						</tr>
					<?php }
					?>


					<?php foreach ($charge_details as $charge) { ?>
						<tr>
							<td class="desc" style="text-align: center;border-left: none;"></td>
							<td class="rateborders"></td>
							<td class="desc" style=""></td>
							<td class="desc"></td>
							<td class="desc"><?php echo $charge['name_charge'] ?></td>
							<td class="desc"><?php echo moneyFormatIndia(number_format($charge['pur_ret_charge_value'])) ?> </td>
						</tr>
					<?php $i++;
					}
					?>


					<?php
					foreach ($charge_gst_details as $gst) { ?>
						<?php
						if ($gst['cgst_cost'] > 0) { ?>
							<tr>
								<td class="rateborders" style="border-left: none"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders">CGST(<?php echo ($gst['pur_ret_charge_tax'] / 2) ?> %)</td>
								<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['cgst_cost'], 2, ".", "")) ?></td>
							</tr>

							<tr>
								<td class="rateborders" style="border-left: none"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders">SGST(<?php echo ($gst['pur_ret_charge_tax'] / 2) ?> %)</td>
								<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['sgst_cost'], 2, ".", "")) ?></td>
							</tr>

						<?php } else if ($gst['igst_cost'] > 0) { ?>
							<tr>
								<td class="rateborders" style="border-left: none"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders"></td>
								<td class="rateborders">IGST(<?php echo ($gst['pur_ret_charge_tax']) ?> %)</td>
								<td class="rateborders"><?php echo moneyFormatIndia(number_format($gst['igst_cost'], 2, ".", "")) ?></td>
							</tr>
						<?php } ?>
					<?php } ?>


					<?php
					if ($issue['pur_ret_other_charges_tds_percent'] > 0) { ?>
						<tr>
							<td class="rateborders" style="border-left: none"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders">CHARGES TDS(<?php echo ($issue['pur_ret_other_charges_tds_percent']) ?> %)</td>
							<td class="rateborders"><?php echo moneyFormatIndia(round($issue['pur_ret_other_charges_tds_value'], 2), 2, ".", "") ?></td>
						</tr>
					<?php }
					?>


					<?php
					if ($issue['pur_ret_discount'] != 0) { ?>
						<tr>
							<td class="rateborders" style="border-left: none"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders">DISCOUNT</td>
							<td class="rateborders"><?php echo moneyFormatIndia(round($issue['pur_ret_discount'], 2), 2, ".", "") ?></td>
						</tr>
					<?php }
					?>


					<?php
					if ($issue['pur_ret_round_off'] != 0) { ?>
						<tr>
							<td class="rateborders" style="border-left: none"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders"></td>
							<td class="rateborders">ROUND OFF</td>
							<td class="rateborders"><?php echo moneyFormatIndia(round($issue['pur_ret_round_off'], 2), 2, ".", "") ?></td>
						</tr>
					<?php }
					?>


					<?php $height = "";
					if ($i == 2)
						$height = "165px";
					else if ($i == 3)
						$height = "140px";
					else if ($i == 4)
						$height = "105px";
					else if ($i == 5)
						$height = "80px";
					else if ($i == 6)
						$height = "55px";
					else if ($i == 7)
						$height = "30px";
					?>


					<?php if ($i < 8) { ?>
						<!-- <tr style="height: <?php echo $height ?>">			
						<td class="rateborders" style="border-left: none"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
						<td class="rateborders"></td>
					</tr> -->
					<?php } ?>

					<?php $grandtotal = $gst['cgst_cost'] + $gst['sgst_cost'] + $gst['igst_cost'] + $issue['return_total_cost'] + $issue['pur_ret_tcs_value'] + $issue['pur_ret_tds_value'] + $charge['pur_ret_charge_value'] + $issue['pur_ret_other_charges_tds_value'] ?>


					<tr>
						<td class="rateborders" style="border-top: 1px solid #ccc; border-left: none"></td>
						<td class="rateborders" style="border-top: 1px solid #ccc; border-left: none"></td>
						<td class="rateborders" style="border-top: 1px solid #ccc;"><b>FINAL TOTAL</b></td>
						<td class="rateborders" style="border-top: 1px solid #ccc;"><b><?php echo $total_qty; ?></b></td>
						<td class="rateborders" style="border-top: 1px solid #ccc;"></td>
						<td class="alignRight" style="border-top: 1px solid #ccc;"><b><?php echo moneyFormatIndia(round($grandtotal, 3), 2, ".", "") ?></b></td>
					</tr>
					<tr>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
						<td style="border-top: 1px solid #ccc;"></td>
					</tr>
				</tbody>
				<tfoot>


				</tfoot>
			</table>
		</div>

		<div class="footer">
			<div class="footer_left">
				Checked By
			</div>
			<div class="footer_center">
				Verified By
			</div>
			<div class="footer_right">
				Authorised Signatory
			</div>
		</div>

	</div>

	<script type="text/javascript">
		setTimeout(function() {

			window.print();

		}, 1000);
	</script>
</body>

</html>
