<html>

<head>
	<meta charset="utf-8">
	<title>CASH</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/metalissue_ack.css">
	<style type="text/css">
		.addr_labels {
			display: block;
			width: 30%;
			padding-bottom: 5px;
			text-align: center;
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

		table {
			border-collapse: collapse;
			width: 100%;
		}

		.table-responsive table,
		th,
		td {
			border: 1px solid;
		}

		.table-responsive table,
		th,
		tr,
		td {
			height: 35px;
		}

		@page {

			margin-top: 30mm;
			margin-left: 10mm;
			margin-right: 10mm;
			margin-bottom: 30mm;
			font-size: 30px !important;
			size: 207mm 297mm;
			border: 1px solid red;

		}

		td {
			text-align: center;
		}

		.mb-2 {
			margin-bottom: 5px;
			font-weight: 500;
		}

		.PDFReceipt * {
			font-size: 16px;
		}

		.PDFReceipt table tr td {
			font-size: 16px !important;
		}

		
	</style>
</head>

<body>
	<div class="PDFReceipt">

		<div class="heading">
			<div class="company_name">
				<h1><?php echo strtoupper($comp_details['company_name']); ?></h1>
			</div>
			<div><?php echo strtoupper($comp_details['address1']) ?> , <?php echo strtoupper($comp_details['address2']) ?></div>
			<?php echo ($comp_details['email'] != '' ? '<div>Email : ' . $comp_details['email'] . ' </div>' : '') ?>
			<?php echo ($comp_details['gst_number'] != '' ? '<div>GST : ' . $comp_details['gst_number'] . ' </div>' : '') ?>
			<?php echo ($denomination['branch_name'] != '' ? '<div>BRANCH : ' . $denomination['branch_name'] . ' </div>' : '') ?>
			<?php echo ($denomination['date'] != '' ? '<div>Denomonation Report Date : ' . date_format(date_create($denomination['date']), "d-m-Y") . ' </div>' : '') ?>

			<?php echo ($denomination['cash_type'] != '' ? '<div>Cash Type : ' . ($denomination['cash_type']==1?'CRM':($denomination['cash_type']==2?'Retail':'ALL')) . ' </div>' : '') ?>
			
		</div><br>




		<!-- <div style="width: 100%; text-transform:uppercase;margin-top:1px;">
			    <div style="text-align: center !important;">CASH COLLECTION DENOMINATION</div>
			</div>  -->

		<div class="content-wrapper">
			<div class="box">
				<div class="box-body">
					<div class="container-fluid">
						<div id="printable">
							<div class="row" style="margin-top:2%;">
								<div class="col-xs-12">
									<div class="table-responsive">

										<table id="pp" class="table text-center">

											<tr>
												<th class="" width="33%">Denomination</th>
												<th class="" width="33%">Count</th>
												<th class="" width="33%">Amount</th>
											</tr>


											<?php
											foreach ($denomination_details as $dd) {
											?>
												<tr>
													<td class=""><?php echo $dd['note']; ?></td>
													<td class="alignRight"><?php echo $dd['value']; ?></td>
													<td class="alignRight"><?php echo number_format($dd['amount'],2); ?></td>


												</tr>
											<?php } ?>

											<tr>
												<td class="alignLeft"></td>
												<td class="alignRight"><b>TOTAL</b> </td>
												<td class="alignRight"><b><?php echo number_format($denomination['cash_on_hand'],2); ?></b></td>


											</tr>

										</table><br>
									</div>
								</div>
							</div><br><br><br>


							<div class="no-style">
								<table class="no-style" width="75%">
								<tr>
									<td class="alignLeft">BILL AMOUNT </td>
									<td class="alignRight"><?=  $denomination['sales_amount']  ?></td>
								</tr>
								<tr>
									<td class="alignLeft">CASH IN HAND </td>
									<td class="alignRight"><?=  $denomination['cash_on_hand']  ?></td>
								</tr>
								<tr>
									<td class="alignLeft">OPENING BALNACE </td>
									<td class="alignRight"><?=  $denomination['opening_balance']  ?></td>
								</tr>
								<tr>
									<td class="alignLeft">DIFFERENCE </td>
									<td class="alignRight"><?=  number_format($denomination['total_amount'] - $denomination['cash_on_hand'],2)  ?></td>
								</tr>
								<tr>
									<td class="alignLeft"><b>TOTAL<b> </td>
									<td class="alignRight"><b><?=  $denomination['total_amount']  ?></b></td>
								</tr>
									
								</table>

							</div>
						</div>
					</div>
				</div>
			</div><!-- /.box-body -->
		</div>
	</div>
	<script type="text/javascript">
		setTimeout(function() {

			window.print();

		}, 1000);

		window.onafterprint = function() {
			
			window.close(); // For closing the tab

		};
	</script>
</body>

</html>