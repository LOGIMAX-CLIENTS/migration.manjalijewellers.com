<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Physical Stock Entry</title>
	<style type="text/css">
		body, html { margin-bottom: 0; }
		body {
			font-family: sans-serif;
			font-size: 12px;
			margin-left: -25px;
			margin-top: -45px;
			margin-bottom: -40px;
		}
		@page { size: 78mm 297mm; margin-bottom: -40px !important; }
		span { display: inline-block; }
		.header { font-size: 15px; text-align: center; margin-bottom: 10px; }
		.header h3 { margin: 5px 0; }
		.tap_head { margin-top: -10px; }
		.alignRight { text-align: right; }
		.alignLeft { text-align: left; }
		.item_details { width: 100%; margin-left: -18px; }
		.item_dashed { border-top: 1px dashed black; border-bottom: 0; margin-left: 0; width: 300px; }
		.tot_dashed { border-top: 1px dashed black; border-bottom: 0; margin-left: 0; width: 300px; }
		table.estimation { margin-left: -2px; font-size: 11px; }
		table.estimation td { padding-top: 0; }
		.section-name { font-weight: bold; }
	</style>
</head>
<body>
	<span class="PDFReceipt">
		<div class="printable">
			<div class="header">
				<h3><?php echo $company_name; ?></h3>
				<label>Physical Stock Entry</label>
			</div>
			<div class="tap_head">
				<table style="width:118%; font-size:11px; margin-left:-2px; white-space:nowrap;">
					<tr>
						<td>Branch</td><td>:</td><td class="alignLeft"><?php echo $branch_name; ?></td>
						<td class="alignRight">Emp</td><td>:</td><td class="alignLeft"><?php echo $employee_name; ?></td>
					</tr>
					<tr>
						<td>Date</td><td>:</td><td class="alignLeft"><?php echo $stock_date; ?></td>
						<td class="alignRight">Type</td><td>:</td><td class="alignLeft"><?php echo $product_type_label; ?></td>
					</tr>
				</table>
			</div>
			<div class="item_details">
				<table class="estimation" style="width:118%;">
					<tr><td><hr class="item_dashed"></td></tr>
					<tr>
						<th width="10%">No</th>
						<th>PRODUCT</th>
						<th class="alignRight" width="25%"><?php echo $is_tagged ? 'PCS' : 'WT(gm)'; ?></th>
					</tr>
					<tr><td><hr class="item_dashed"></td></tr>
					<tbody>
						<?php
						$item_no = 1;
						foreach ($products as $row) {
							if (isset($row['is_section_header']) && $row['is_section_header']) {
						?>
							<tr>
								<td colspan="3" class="section-name"><?php echo $row['name']; ?></td>
							</tr>
							<tr><td><hr class="tot_dashed"></td></tr>
						<?php } else { ?>
							<tr>
								<td><?php echo $item_no; ?>)</td>
								<td><?php echo $row['name']; ?></td>
								<td class="alignRight"><?php echo (!empty($row['value']) ? $row['value'] : '-'); ?></td>
							</tr>
						<?php
							$item_no++;
							}
						}
						?>
					</tbody>
					<tr><td><hr class="item_dashed"></td></tr>
					<tr style="font-weight:bold;">
						<td></td>
						<td>TOTAL [<?php echo ($item_no - 1); ?>]</td>
						<td class="alignRight"><?php echo ($item_no - 1); ?></td>
					</tr>
					<tr><td><hr class="item_dashed"></td></tr>
				</table>
			</div>
			<div>
				<label>Printed : <?php echo date('d/m/Y h:i A'); ?></label><br><br>
				<label>Signature&nbsp;:</label><br><br>
			</div>
		</div>
	</span>
</body>
</html>
