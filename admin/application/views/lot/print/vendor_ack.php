<html><head>
		<meta charset="utf-8">
		<title>Vendor Report</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/lot_ack.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		<style type="text/css">
		body, html {
			margin-bottom: 0;
		}
		span { display: inline-block; }
		/* Fix dashed lines: force exactly 100% width on A4 print */
		hr.item_dashed {
			width: 100% !important;
		}
		/* Fix table layout and column collapsing/wrapping */
		table.table {
			width: 100% !important;
			table-layout: fixed !important;
			border-collapse: collapse !important;
		}
		table.table th, table.table td {
			white-space: nowrap !important;
		}
		/* Allow only ITEMS column to wrap naturally and allocate remaining space */
		table.table th:nth-child(2), table.table td:nth-child(2) {
			white-space: normal !important;
			word-wrap: break-word !important;
			word-break: break-word !important;
		}
	</style>
</head><body>
<span class="PDFReceipt">
			<div>
				<table class="meta" style="width:30%;margin-top:-30px !important;" align="left">
					<tr>
					<td><img alt=""    class="receipt-logo" src="<?php echo base_url();?>assets/img/logo_1.png"></td>
					</tr>
				</table>
				<table class="meta" style="width:30%;font-weight:bold;" align="right">
					<tr>
						<td style="font-size:11px !important;">
							<span>LOT NO &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp; <?php echo $lot_inwards_detail[0]['lot_no'];?></span>
						</td>
					</tr>
					<tr style="margin-top:25px !important;">
						<td style="font-size:11px !important;">
							<span>DATE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							:&nbsp;&nbsp;<?php echo $lot_inwards_detail[0]['lot_date'];?></span>
						</td>
					</tr></br>
					<tr style="margin-top:25px !important;">
						<td style="font-size:11px !important;">
							<span>VENDOR&nbsp;&nbsp;
							:&nbsp;&nbsp;<?php echo $lot_inwards_detail[0]['lt_gold_smith'];?> </span>
						</td>
					</tr></br>
					<tr style="margin-top:25px !important;">
						<td style="font-size:11px !important;">
							<span>EMP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							:&nbsp;&nbsp; <?php echo $lot_inwards_detail[0]['emp_name'];?> </span>
						</td>
					</tr></br>
				</table>
				<p></p>
			</div>
			<br><br><br><br><br>
			<div class="address" align="center">
					<h2>LOT RECEIPT - VENDOR </h2>
			</div><br>
<div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
 				<?php if($type==1){?>
				<div id="printable">
						<div  class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
							

									<table id="pp" class="table text-center">
									<!--	<thead> -->
											<tr>
												<th width="7%;" style="text-align:left;">S.NO</th>
												<th  width="25%;" style="text-align:left;">ITEMS</th>
												<th  width="20%;"style="text-align:left;">PCS</th>		
												<th  width="20%;" style="text-align:left;">GWT</th>
												<th  width="20%;" style="text-align:left;">PURITY</th>
											</tr>
											<tr>
												<td colspan="5"><hr class="item_dashed"></td>
											</tr>
										<!--</thead>
										<tbody>-->
											<?php 
											$i=1;
											$no_of_piece=0;
											$gross_wt=0;
											foreach($lot_det['design_wise'] as $lot)
											{
											$no_of_piece+=$lot['tot_pcs'];
											$gross_wt+=$lot['gross_wt'];
											?>
												<tr>
														<td style="text-align:left;"><?php echo $i;?></td>
														<td style="text-align:left;"><?php echo $lot['product_name'];?></td>
														<td style="text-align:left;"><?php echo $lot['tot_pcs'];?></td>
														<td style="text-align:left;"><?php echo $lot['gross_wt'];?></td>
														<td style="text-align:left;"><?php echo $lot['purity'];?></td>
													</tr>
											<?php $i++;
											}?>
											<tr>
												<td colspan="5"><hr class="item_dashed"></td>
											</tr>
									<!--</tbody> -->
										<tfoot>
											<tr>
											<td><b>TOTAL</b></td>
											<td></td>
											<td><b><?php echo $no_of_piece;?></b></td>
											<td><b><?php echo number_format((float)$gross_wt,3,'.','')?></b></td>
											<td></td>
											<td></td>
											</tr>
										</tfoot>
										<tr>
												<td colspan="5"><hr class="item_dashed"></td>
											</tr>
									</table><br>	
									
									<div class="grand_total" align="center">
										<div class="row">
											<label><b>TOTAL GWT :  <?php echo number_format((float)($gross_wt),3,'.','')?></b></label><br><br>
											<span style="margin-left:-20px;"><b>TOTAL PCS &nbsp;:  <?php echo $no_of_piece;?></b></span>
										</div>
									</div>
								</div>	
							 </div>	
						</div>
						<br><br><br><br><br><br><br><br><br><br><br><br>
						<div class="row" style="text-transform:uppercase;">
							<label>Received By</label>
							<label style="margin-left:30%;">Vendor Sign</label>
						</div>
				</div>
				<?php }?>
				
				
 </div>
 </div><!-- /.box-body --> 
</div>

 </span>          
</body></html>