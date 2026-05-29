<html><head>
		<meta charset="utf-8">
		<title>Branch Copy - <?php echo $lot_inwards_detail[0]['lot_no'];?></title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/lot_ack.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		<style type="text/css">
		body, html {
			margin-bottom: 0;
		}		
		span { display: inline-block; }
		/* Fix dashed lines: force exactly 100% width on A4 print */
		hr.item_dashed, hr.sumamry_dashed {
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
		/* For Table 1: ITEMS is 2nd column. For Table 2: ITEMS is 3rd column */
		table.table th:nth-child(2), table.table td:nth-child(2),
		table.table th:nth-child(3), table.table td:nth-child(3) {
			white-space: normal !important;
			word-wrap: break-word !important;
			word-break: break-word !important;
		}
	</style>
</head><body>
<span class="PDFReceipt">
			<div>
				<!--<table class="meta" style="width:30%;margin-top:-30px !important;" align="left">
					<tr>
					<td><img alt=""  src="<?php echo base_url();?>assets/img/receipt_logo.png"></td>
					</tr>
				</table>-->
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
			</div>
			<div class="" align="center">
			    <h2><?php echo strtoupper($comp_details['company_name']);?></h2>
			</div>
			<div class="" align="center">
			    
			    <?php 
			        $branch_name='';
			    	foreach($tag_details['design_wise'] as $key => $branches)
			    	{
			    	     foreach($branches as $branch)
			    	     {
			    	         $branch_name=$branch['branch_name'];
			    	     }
			    	}
			    ?>
				<h2>LOT RECEIPT - <?php echo strtoupper($branch_name)?></h2>
			</div><br>
<div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
						<div  class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
								    <table id="pp" class="table text-center">
									<!--	<thead> -->
											<tr>
												<th width="7%;" style="text-align:left;">S.NO</th>
												<th  width="25%;" style="text-align:left;">ITEMS</th>
												<!--<th  width="20%;"style="text-align:left;">Design</th>-->		
												<th  width="20%;" style="text-align:left;">PCS</th>
												<th  width="20%;" style="text-align:left;">GWT</th>
											</tr>
											<tr>
												<td colspan="4"><hr class="sumamry_dashed"></td>
											</tr>
									<!--<tbody>-->
										    <?php 
										    $i=1;
										    $pcs=0;$gross_wt=0;$tot_sales_value=0;
										    foreach($summary as $lot)
										    {
										        $pcs+=$lot['piece'];
										        $gross_wt+=$lot['gross_wt'];
										        $tot_sales_value+=$lot['tot_sales_value'];
										    ?>
                                                <tr>
                                                    <td style="text-align:left;"><?php echo $i;?></td>
                                                    <td style="text-align:left;"><?php echo $lot['product_name'];?></td>
                                                    <!--<td style="text-align:left;"><?php echo $lot['design_name'];?></td>-->
                                                    <td style="text-align:left;"><?php echo $lot['piece'];?></td>
                                                    <td style="text-align:left;"><?php echo $lot['gross_wt'].($lot['tot_sales_value']>0 ?'/'.$lot['tot_sales_value']:'');?></td>
                                                </tr>
										    <?php 
										    $i++;
										    }
										    ?>
									<!--<tbody>-->
									<tr>
										<td colspan="4"><hr class="sumamry_dashed"></td>
									</tr>
									<tfoot>
                                        <tr style="font-weight:bold;">
                                            <td style="text-align:left;"></td>
                                            <!--<td style="text-align:left;"></td>-->
                                            <td style="text-align:left;">TOTAL</td>
                                            <td style="text-align:left;"><?php echo $pcs;?></td>
                                            <td style="text-align:left;"><?php echo $gross_wt.($tot_sales_value>0 ? '/'.$tot_sales_value :'');?></td>
                                        </tr>
										</tfoot>
										<tr>
											<td colspan="4"><hr class="sumamry_dashed"></td>
										</tr>
									</table><br>
								<?php if($type==2){?>	
								<table id="pp" class="table text-center">
									<!--	<thead> -->
											<tr>
												<th width="10%;" style="text-align:left;">S.NO</th>
												<th width="25%;" style="text-align:left;">TAG CODE</th>
												<th width="25%;" style="text-align:left;">ITEMS</th>
												<th width="25%;" style="text-align:left;">DESIGN</th>
												<th width="35%;" style="text-align:left;">SUB DESIGN</th>
												<th width="20%;" style="text-align:left;">PCS</th>		
												<th width="20%;" style="text-align:left;">GWT</th>
												<th width="20%;" style="text-align:left;">RATE</th>
											</tr>
											<tr>
												<td colspan="8"><hr class="item_dashed"></td>
											</tr>
										<!--</thead>
										<tbody>-->
												<?php 
											$i=1;
											
											$tot_tagged_pcs=0;
											$tot_tagged_gross_wt=0;
											$tot_sales_value=0;
											foreach($tag_details['design_wise'] as $key => $branches)
											{
											    $tagged_pcs=0;
        										$tagged_gross_wt=0;
        										$sales_value=0;
											    foreach($branches as $branch)
											    {
											       
											        if($key==$branch['product_name'])
											        {
											            $tagged_pcs+=$branch['piece'];
											            $tagged_gross_wt+=$branch['gross_wt'];
											            $sales_value+=$branch['sales_value'];
											        ?>
											            <tr>
            													<td style="text-align:left;"><?php echo $i;?></td>
            													<td style="text-align:left;"><?php echo $branch['tag_code'];?></td>
            													<td style="text-align:left;"><?php echo $branch['product_name'];?></td>
            													<td style="text-align:left;"><?php echo $branch['design_name'];?></td>
            													<td style="text-align:left;"><?php echo $branch['sub_design_name'];?></td>
            													<td style="text-align:left;"><?php echo $branch['piece'];?></td>
            													<td style="text-align:left;"><?php echo $branch['gross_wt'];?></td>
            													<td style="text-align:left;"><?php echo $branch['sales_value'];?></td>					
            											</tr>
											        <?php }
											    $i++;}?>
											    <tr>
        												<td colspan="8"><hr class="item_dashed"></td>
        										</tr>
											    <tr style="font-weight:bold;">
            													<td style="text-align:left;"></td>
            													<td style="text-align:left;"></td>
            													<td style="text-align:left;"></td>
            													<td style="text-align:left;"></td>
            													<td style="text-align:left;">SUB TOTAL</td>
            													<td style="text-align:left;"><?php echo $tagged_pcs;?></td>
            													<td style="text-align:left;"><?php echo $tagged_gross_wt;?></td>
            													<td style="text-align:left;"><?php echo $sales_value;?></td>
            																					
            									</tr>
            									<tr>
    												<td colspan="8"><hr class="item_dashed"></td>
    											</tr>
											    <?php 
											    $i=1;
											    $tot_tagged_pcs+=$tagged_pcs;$tot_tagged_gross_wt+=$tagged_gross_wt;$tot_sales_value+=$sales_value;
											    $tagged_pcs=0;$sales_value=0;$tagged_gross_wt=0;
											    }?>
									<!--</tbody> -->
											<tfoot>
											<tr>
											<td><b>TOTAL</b></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td><b><?php echo $tot_tagged_pcs;?></b></td>
											<td><b><?php echo number_format((float)$tot_tagged_gross_wt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$tot_sales_value,2,'.','')?></b></td>
											<td></td>
											<td></td>
											</tr>
										</tfoot>
										
											<tr>
												<td colspan="8"><hr class="item_dashed"></td>
											</tr>
									</table>
							    <?php }?>
								</div>
								
							 </div>	
						</div>
						
						<div class="row" style="text-transform:uppercase;">
						    <br><br>
							<label>individual wt verified by</label>
							<label style="margin-left:20%;">received by</label>
							<label style="margin-left:30%;">approved By</label>
						</div>
 </div>
			</div>
 </div>
 </div><!-- /.box-body --> 
</div>
 </span>          
</body></html>
