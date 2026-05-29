<html>
<head>
	<meta charset="utf-8">
	<title>Physical Stock Inspect Report - Print</title>
	<style>
		@page { margin: 10mm 8mm; }
		body { font-family: Arial, sans-serif; font-size: 10pt; color: #000; margin: 0; padding: 0; }
		.header { text-align: center; margin-bottom: 2px; }
		.header h2 { margin: 0; font-size: 14pt; }
		.header h3 { margin: 2px 0 0; font-size: 11pt; font-weight: normal; }
		.header .meta { font-size: 9pt; color: #555; margin-top: 3px; }
		.sub-header { overflow: hidden; margin-bottom: 8px; font-size: 9pt; color: #333; }
		.sub-header-left { float: left; }
		.sub-header-right { float: right; }
		table { width: 100%; border-collapse: collapse; font-size: 9pt; }
		th, td { border: 1px solid #999; padding: 3px 6px; }
		th { background: #eee; text-align: center; font-weight: bold; }
		td { white-space: nowrap; }
		.text-right { text-align: right; }
		.text-left { text-align: left; }
		.text-center { text-align: center; }
		.section-header { background: #f0f0f0; font-weight: bold; }
		.section-header td { text-align: left; }
		.subtotal-row { background: #e8f4fd; font-weight: bold; border-top: 1px solid #999; }
		.grand-total { font-weight: bold; color: #E67E22; }
		.shortage { color: red; font-weight: bold; }
		.excess { color: green; font-weight: bold; }
		.product-indent { padding-left: 25px !important; color: #555; }
		.print-footer { margin-top: 30px; font-size: 9pt; color: #333; overflow: hidden; }
		.print-footer-left { float: left; text-align: left; }
		.print-footer-left div { margin-bottom: 4px; }
		.print-footer-right { float: right; text-align: center; margin-top: 10px; }
		.signature-line { border-top: 1px solid #000; width: 200px; margin-top: 50px; padding-top: 5px; }
		@media print {
			.no-print { display: none !important; }
		}
	</style>
</head>
<body>
	<div class="no-print" style="text-align:center;padding:8px;background:#f5f5f5;border-bottom:1px solid #ddd;">
		<button onclick="window.print();" style="padding:6px 20px;font-size:12pt;cursor:pointer;">🖨 Print</button>
		<button onclick="window.close();" style="padding:6px 20px;font-size:12pt;cursor:pointer;margin-left:10px;">✕ Close</button>
	</div>

	<div class="header">
		<h2><?php echo isset($company_name) ? htmlspecialchars($company_name) : ''; ?></h2>
		<h3><?php echo isset($branch_name) ? htmlspecialchars($branch_name) : ''; ?> — <?php echo isset($report_type) && $report_type == '2' ? 'SECTION STOCK REPORT' : 'PRODUCT STOCK REPORT'; ?></h3>
		<div class="meta">Date: <?php echo isset($report_date) ? htmlspecialchars($report_date) : date('d/m/Y'); ?></div>
	</div>

	<div class="sub-header">
		<div class="sub-header-left"><strong>Printed By:</strong> <?php echo isset($printed_by) ? htmlspecialchars($printed_by) : '-'; ?></div>
		<div class="sub-header-right"><strong>Print Date & Time:</strong> <?php echo date('d/m/Y h:i A'); ?></div>
	</div>

	<?php
	// Determine report type
	$is_product_wise = (!isset($report_type) || $report_type == '1');

	// Initialize grand totals
	$grand_sys_pcs = 0; $grand_sys_wt = 0;
	$grand_ent_pcs = 0; $grand_ent_wt = 0;
	$grand_diff_pcs = 0; $grand_diff_wt = 0;
	?>

	<table>
		<thead>
			<tr>
				<th class="text-left"><?php echo $is_product_wise ? 'Product' : 'Section'; ?></th>
				<th class="text-right">System Pcs</th>
				<th class="text-right">System Wt</th>
				<th class="text-right">Entered Pcs</th>
				<th class="text-right">Entered Wt</th>
				<th class="text-right">Difference Pcs</th>
				<th class="text-right">Difference Wt</th>
				<th class="text-left">Entered By</th>
			</tr>
		</thead>
		<tbody>
		<?php if (!empty($section_list)) : ?>
			<?php if ($is_product_wise) : ?>
				<?php foreach ($section_list as $prod) :
					$sys_pcs = floatval($prod['system_pcs']);
					$sys_wt = floatval($prod['system_weight']);
					$ent_pcs = floatval($prod['entered_pcs']);
					$ent_wt = floatval($prod['entered_weight']);
					$diff_pcs = $sys_pcs - $ent_pcs;
					$diff_wt = $sys_wt - $ent_wt;
					$has_entry = ($ent_pcs > 0 || $ent_wt > 0);

					$grand_sys_pcs += $sys_pcs;
					$grand_sys_wt += $sys_wt;
					$grand_ent_pcs += $ent_pcs;
					$grand_ent_wt += $ent_wt;
					if ($ent_pcs > 0) $grand_diff_pcs += $diff_pcs;
					if ($ent_wt > 0) $grand_diff_wt += $diff_wt;

					// Color classes
					$diff_pcs_class = '';
					$diff_wt_class = '';
					if ($ent_pcs > 0) {
						$diff_pcs_class = $diff_pcs > 0 ? 'shortage' : ($diff_pcs < 0 ? 'excess' : '');
					}
					if ($ent_wt > 0) {
						$diff_wt_class = $diff_wt > 0 ? 'shortage' : ($diff_wt < 0 ? 'excess' : '');
					}
				?>
				<tr>
					<td class="text-left" style="font-weight:bold;"><?php echo htmlspecialchars($prod['product_name'] ?: 'Unknown'); ?></td>
					<td class="text-right"><?php echo $sys_pcs != 0 ? $sys_pcs : ($has_entry ? '0' : ''); ?></td>
					<td class="text-right"><?php echo $sys_wt != 0 ? number_format($sys_wt, 3, '.', '') : ($has_entry ? '0.000' : ''); ?></td>
					<td class="text-right"><?php echo $ent_pcs != 0 ? $ent_pcs : ''; ?></td>
					<td class="text-right"><?php echo $ent_wt != 0 ? number_format($ent_wt, 3, '.', '') : ''; ?></td>
					<td class="text-right <?php echo $diff_pcs_class; ?>"><?php echo ($ent_pcs > 0 && $diff_pcs != 0) ? abs($diff_pcs) : ''; ?></td>
					<td class="text-right <?php echo $diff_wt_class; ?>"><?php echo ($ent_wt > 0 && $diff_wt != 0) ? number_format(abs($diff_wt), 3, '.', '') : ''; ?></td>
					<td class="text-left"><?php echo htmlspecialchars($prod['employee_names'] ?: '-'); ?></td>
				</tr>
				<?php endforeach; ?>

			<?php else : ?>
				<?php foreach ($section_list as $section) :
					$sec_sys_pcs = floatval($section['total_system_pcs']);
					$sec_sys_wt = floatval($section['total_system_weight']);
					$sec_ent_pcs = floatval($section['total_entered_pcs']);
					$sec_ent_wt = floatval($section['total_entered_weight']);
					$sec_has_entry = false;
					$sec_diff_pcs = 0; $sec_diff_wt = 0;
					$product_count = count($section['products']);
				?>
				<!-- Section Header -->
				<tr class="section-header">
					<td colspan="8"><?php echo htmlspecialchars($section['section_name'] ?: 'Unknown'); ?> (<?php echo $product_count; ?> products)</td>
				</tr>
				<?php if (!empty($section['products'])) : ?>
					<?php foreach ($section['products'] as $prod) :
						$p_sys_pcs = floatval($prod['system_pcs']);
						$p_sys_wt = floatval($prod['system_weight']);
						$p_ent_pcs = floatval($prod['entered_pcs']);
						$p_ent_wt = floatval($prod['entered_weight']);
						$p_diff_pcs = $p_sys_pcs - $p_ent_pcs;
						$p_diff_wt = $p_sys_wt - $p_ent_wt;
						$has_entry = ($p_ent_pcs > 0 || $p_ent_wt > 0);
						if ($has_entry) $sec_has_entry = true;
						if ($p_ent_pcs > 0) $sec_diff_pcs += $p_diff_pcs;
						if ($p_ent_wt > 0) $sec_diff_wt += $p_diff_wt;

						$diff_pcs_class = '';
						$diff_wt_class = '';
						if ($p_ent_pcs > 0) {
							$diff_pcs_class = $p_diff_pcs > 0 ? 'shortage' : ($p_diff_pcs < 0 ? 'excess' : '');
						}
						if ($p_ent_wt > 0) {
							$diff_wt_class = $p_diff_wt > 0 ? 'shortage' : ($p_diff_wt < 0 ? 'excess' : '');
						}
					?>
					<tr>
						<td class="text-left product-indent">&nbsp;&nbsp;<?php echo htmlspecialchars($prod['product_name'] ?: 'Unknown'); ?></td>
						<td class="text-right"><?php echo $p_sys_pcs != 0 ? $p_sys_pcs : ($has_entry ? '0' : ''); ?></td>
						<td class="text-right"><?php echo $p_sys_wt != 0 ? number_format($p_sys_wt, 3, '.', '') : ($has_entry ? '0.000' : ''); ?></td>
						<td class="text-right"><?php echo $p_ent_pcs != 0 ? $p_ent_pcs : ''; ?></td>
						<td class="text-right"><?php echo $p_ent_wt != 0 ? number_format($p_ent_wt, 3, '.', '') : ''; ?></td>
						<td class="text-right <?php echo $diff_pcs_class; ?>"><?php echo ($p_ent_pcs > 0 && $p_diff_pcs != 0) ? abs($p_diff_pcs) : ''; ?></td>
						<td class="text-right <?php echo $diff_wt_class; ?>"><?php echo ($p_ent_wt > 0 && $p_diff_wt != 0) ? number_format(abs($p_diff_wt), 3, '.', '') : ''; ?></td>
						<td class="text-left"><?php echo htmlspecialchars($prod['employee_names'] ?: '-'); ?></td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php
					// Section subtotal
					$sub_diff_pcs_class = $sec_has_entry ? ($sec_diff_pcs > 0 ? 'shortage' : ($sec_diff_pcs < 0 ? 'excess' : '')) : '';
					$sub_diff_wt_class = $sec_has_entry ? ($sec_diff_wt > 0 ? 'shortage' : ($sec_diff_wt < 0 ? 'excess' : '')) : '';
					$grand_sys_pcs += $sec_sys_pcs;
					$grand_sys_wt += $sec_sys_wt;
					$grand_ent_pcs += $sec_ent_pcs;
					$grand_ent_wt += $sec_ent_wt;
					if ($sec_has_entry) {
						$grand_diff_pcs += $sec_diff_pcs;
						$grand_diff_wt += $sec_diff_wt;
					}
				?>
				<tr class="subtotal-row">
					<td class="text-right" style="padding-right:10px;">Sub Total</td>
					<td class="text-right"><?php echo $sec_sys_pcs != 0 ? $sec_sys_pcs : ''; ?></td>
					<td class="text-right"><?php echo $sec_sys_wt != 0 ? number_format($sec_sys_wt, 3, '.', '') : ''; ?></td>
					<td class="text-right"><?php echo $sec_ent_pcs != 0 ? $sec_ent_pcs : ''; ?></td>
					<td class="text-right"><?php echo $sec_ent_wt != 0 ? number_format($sec_ent_wt, 3, '.', '') : ''; ?></td>
					<td class="text-right <?php echo $sub_diff_pcs_class; ?>"><?php echo ($sec_has_entry && $sec_diff_pcs != 0) ? abs($sec_diff_pcs) : ''; ?></td>
					<td class="text-right <?php echo $sub_diff_wt_class; ?>"><?php echo ($sec_has_entry && $sec_diff_wt != 0) ? number_format(abs($sec_diff_wt), 3, '.', '') : ''; ?></td>
					<td></td>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php
			// Grand total colors
			$gt_diff_pcs_class = $grand_diff_pcs > 0 ? 'shortage' : ($grand_diff_pcs < 0 ? 'excess' : '');
			$gt_diff_wt_class = $grand_diff_wt > 0 ? 'shortage' : ($grand_diff_wt < 0 ? 'excess' : '');
			?>
			<tr class="grand-total">
				<td class="text-left"><strong>GRAND TOTAL</strong></td>
				<td class="text-right"><?php echo $grand_sys_pcs != 0 ? $grand_sys_pcs : ''; ?></td>
				<td class="text-right"><?php echo $grand_sys_wt != 0 ? number_format($grand_sys_wt, 3, '.', '') : ''; ?></td>
				<td class="text-right"><?php echo $grand_ent_pcs != 0 ? $grand_ent_pcs : ''; ?></td>
				<td class="text-right"><?php echo $grand_ent_wt != 0 ? number_format($grand_ent_wt, 3, '.', '') : ''; ?></td>
				<td class="text-right <?php echo $gt_diff_pcs_class; ?>"><?php echo $grand_diff_pcs != 0 ? abs($grand_diff_pcs) : ''; ?></td>
				<td class="text-right <?php echo $gt_diff_wt_class; ?>"><?php echo $grand_diff_wt != 0 ? number_format(abs($grand_diff_wt), 3, '.', '') : ''; ?></td>
				<td></td>
			</tr>
		<?php else : ?>
			<tr><td colspan="8" style="text-align:center;color:#999;padding:20px;">No data found for the selected date.</td></tr>
		<?php endif; ?>
		</tbody>
	</table>

	<div class="print-footer">
		<div class="print-footer-right">
			<div class="signature-line">Authorised Signature</div>
		</div>
	</div>

	<script>
		window.onload = function() {
			<?php if (isset($auto_print) && $auto_print == '1') : ?>
			setTimeout(function() { window.print(); }, 300);
			<?php endif; ?>
		};
	</script>
</body>
</html>
