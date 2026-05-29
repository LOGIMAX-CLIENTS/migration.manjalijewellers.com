<html><head>
		<meta charset="utf-8">
		<title>Office Copy - <?php echo $lot_inwards_detail[0]['lot_no'];?></title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/lot_ack.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		<style type="text/css">
		body, html {
			margin-bottom: 0;
		}
		span { display: inline-block; }
		/* Fix dashed lines: force exactly 100% width on A4 print */
		hr.item_dashed1, hr.item_dashed2 {
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
            <div class="" align="center">
			    <h2><?php echo strtoupper($comp_details['company_name']);?></h2>
			</div>
			<div class="" align="center">
					<h2>LOT RECEIPT - <?php echo $lot_inwards_detail[0]['rcvd_branch_name']; ?> </h2>
			</div>
			<table class="meta" style="width:30%;font-weight:bold;margin-top:-10%;" align="right">
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
					</tr>
				</table><br>
<div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
				<div id="printable" >
						<div  class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
								<table id="pp" class="table text-center" style="margin-top:65px !important;">
									<!--	<thead> -->
											<tr>
												<th width="5%" style="text-align:left;">S.NO</th>
												<th  width="25%" style="text-align:left;">ITEMS</th>

												<th  width="10%" style="text-align:left;">PCS</th>		
												<th  width="15%" style="text-align:left;">GWT</th>
												<th  width="15%" style="text-align:left;">LWT</th>
												<th  width="15%" style="text-align:left;">NWT</th>
												<th  width="15%" style="text-align:left;">PURITY</th>
											</tr>
											<tr>
												<td colspan="7"><hr class="item_dashed1"></td>
											</tr>
										<!--</thead>
										<tbody>-->
											<?php 
											$i=1;
											$no_of_piece=0;
											$gross_wt=0;
											$net_wt=0;
											$less_wt=0;
											foreach($lot_det['design_wise'] as $lot)
											{
											$no_of_piece+=$lot['tot_pcs'];
											$gross_wt+=$lot['gross_wt'];
											$net_wt+=$lot['net_wt'];
											$less_wt+=$lot['less_wt'];
											?>
												<tr>
														<td style="text-align:left;"><?php echo $i;?></td>
														<td style="text-align:left;"><?php echo $lot['product_name'];?></td>
														<td style="text-align:left;"><?php echo $lot['tot_pcs'];?></td>
														<td style="text-align:left;"><?php echo $lot['gross_wt'];?></td>
														<td style="text-align:left;"><?php echo $lot['less_wt'];?></td>
														<td style="text-align:left;"><?php echo $lot['net_wt'];?></td>
														<td style="text-align:left;"><?php echo $lot['purity'];?></td>
													</tr>
											<?php $i++;
											}?>
											<tr>
												<td colspan="7"><hr class="item_dashed1"></td>
											</tr>
									<!--</tbody> -->
										<tfoot>
											<tr>
											<td><b>TOTAL</b></td>
											<td></td>
											<td><b><?php echo $no_of_piece;?></b></td>
											<td><b><?php echo number_format((float)$gross_wt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$less_wt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$net_wt,3,'.','')?></b></td>
											<td></td>
											<td></td>
											</tr>
										</tfoot>
										<tr>
												<td colspan="7"><hr class="item_dashed1"></td>
											</tr>
									</table><br>
							<?php if(sizeof($tag_det['design_wise'])>0){?>
									<span style="text-align:center;">BRANCH SUMMARY</span><br>
									<p></p>
									<table id="pp" class="table text-center">
									<!--	<thead> -->
											<tr>
												<th width="5%" style="text-align:left;">S.NO</th>
												<th  width="20%" style="text-align:left;">ITEMS</th>
												<th  width="8%" style="text-align:left;">PCS</th>		
												<th  width="10%" style="text-align:left;">GWT</th>
												<th  width="10%" style="text-align:left;">LWT</th>
												<th  width="10%" style="text-align:left;">DIA WT</th>
												<th  width="10%" style="text-align:left;">NWT</th>
												<th  width="13%" style="text-align:left;">Amount</th>
												<th  width="14%" style="text-align:left;">BRANCH</th>
											</tr>
											<tr>
												<td colspan="9"><hr class="item_dashed2"></td>
											</tr>
										<!--</thead>
										<tbody>-->
											<?php 
											$i=1;
											
											$tot_tagged_pcs=0;
											$tot_tagged_gross_wt=0;
											$tot_tagged_nwt=0;
											$tot_sales_value=0;
											$tot_less_wt=0;
											$tot_dia_wt=0;
											foreach($tag_det['design_wise'] as $key => $branches)
											{
											    $tagged_pcs=0;
        										$tagged_gross_wt=0;
        										$tagged_tot_less_wt=0;
        										$tagged_net_wt=0;
        										$sales_value=0;
        										$dia_wt=0;
											    foreach($branches as $branch)
											    {
											       
											        if($key==$branch['branch_name'])
											        {
											            $tagged_pcs+=$branch['tot_pcs'];
											            $tagged_gross_wt+=$branch['gross_wt'];
											            $tagged_net_wt+=$branch['tot_nwt'];
											            $tagged_tot_less_wt+=$branch['tot_less_wt'];
											            $sales_value+=$branch['sales_value'];
											            $dia_wt+=$branch['dia_wt'];
											        ?>
											            <tr>
            													<td style="text-align:left;"><?php echo $i;?></td>
            													<td style="text-align:left;"><?php echo $branch['product_name'];?></td>
            													<td style="text-align:left;"><?php echo $branch['tot_pcs'];?></td>
            													<td style="text-align:left;"><?php echo $branch['gross_wt'];?></td>
            													<td style="text-align:left;"><?php echo $branch['tot_less_wt'];?></td>
            													<td style="text-align:left;"><?php echo $branch['dia_wt'];?></td>
            													<td style="text-align:left;"><?php echo $branch['tot_nwt'];?></td>
            													<td style="text-align:left;"><?php echo $branch['sales_value'];?></td>
            													<td style="text-align:left;"><?php echo $branch['branch_name'];?></td>									
            											</tr>
											        <?php }
											    $i++;}?>
											    <tr>
        												<td colspan="9"><hr class="item_dashed2"></td>
        										</tr>
											    <tr style="font-weight:bold;">
            													<td style="text-align:left;"></td>
            													<td style="text-align:left;">Sub Total</td>
            													<td style="text-align:left;"><?php echo $tagged_pcs;?></td>
            													<td style="text-align:left;"><?php echo $tagged_gross_wt;?></td>
            													<td style="text-align:left;"><?php echo $tagged_tot_less_wt;?></td>
            													<td style="text-align:left;"><?php echo $dia_wt;?></td>
            													<td style="text-align:left;"><?php echo $tagged_net_wt;?></td>
            													<td style="text-align:left;"><?php echo $sales_value;?></td>
            																					
            									</tr>
            									<tr>
    												<td colspan="9"><hr class="item_dashed2"></td>
    											</tr>
											    <?php 
											    $i=1;
											    $tot_tagged_pcs+=$tagged_pcs;
											    $tot_tagged_gross_wt+=$tagged_gross_wt;
											    $tot_tagged_nwt+=$tagged_net_wt;
											    $tot_less_wt+=$tagged_tot_less_wt;
											    $tot_sales_value+=$sales_value;
											    $tot_dia_wt+=$dia_wt;
											    $tagged_pcs=0;$sales_value=0;$tagged_gross_wt=0;$tagged_net_wt=0;$tagged_tot_less_wt=0;$dia_wt=0;
											    }?>

									<!--</tbody> -->
										<tfoot>
											<tr>
											<td><b>TOTAL</b></td>
											<td></td>
											<td><b><?php echo $tot_tagged_pcs;?></b></td>
											<td><b><?php echo number_format((float)$tot_tagged_gross_wt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$tagged_tot_less_wt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$tot_dia_wt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$tot_tagged_nwt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$tot_sales_value,2,'.','')?></b></td>
											<td></td>
											<td></td>
											</tr>
										</tfoot>
										<tr>
												<td colspan="9"><hr class="item_dashed2"></td>
											</tr>
									</table><br>	
							<?php }?>
								</div>	
							 </div>	
						</div>
						<br>
				</div>
				<div class="grand_total" align="center">
					<div class="row">
							<label><b>Diff.Gwt :  <?php echo number_format((float)($tot_tagged_gross_wt-$gross_wt),3,'.','')?></b></label>
					</div><br>
					<div class="row" style="margin-left:-30px;">
							<label><b>Diff.Pcs :  <?php echo ($tot_tagged_pcs-$no_of_piece);?></b></label>
					</div>
				</div>			
			</div><br><br><br><br>
			<div class="row" style="text-transform:uppercase;">
							<label>individual wt verified by</label>
							<label style="margin-left:20%;">received by</label>
							<label style="margin-left:30%;">approved By</label>
						</div>
 </div>
 </div><!-- /.box-body --> 
</div>

 </span>          
</body></html>