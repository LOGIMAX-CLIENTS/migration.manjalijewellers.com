<html><head>
		<meta charset="utf-8">
		<title>Vendor Report</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/order_ack.css">
	
		<style type="text/css">
		body, html {
		margin-bottom:0
		}
		 span { display: inline-block; }
	</style>
</head><body>
<span class="PDFReceipt">
			<div>
				<table class="meta" style="width:30%;margin-top:-30px !important;" align="left">
					<tr>
					<td><img alt=""  src="<?php echo base_url();?>assets/img/receipt_logo.png"></td>
					</tr>
				</table>
				<table class="meta" style="width:30%;font-weight:bold;" align="right">
					<tr>
						<td style="font-size:11px !important;">
							<span>PUR NO &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp; <?php echo $order[0]['pur_no'];?></span>
						</td>
					</tr>
					<tr style="margin-top:25px !important;">
						<td style="font-size:11px !important;">
							<span>DATE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							:&nbsp;&nbsp;<?php echo $order[0]['order_date'];?></span>
						</td>
					</tr></br>
					<tr style="margin-top:25px !important;">
						<td style="font-size:11px !important;">
							<span>VENDOR&nbsp;&nbsp;
							:&nbsp;&nbsp;<?php echo $order[0]['karigar_name'];?> </span>
						</td>
					</tr></br>
					<tr style="margin-top:25px !important;">
						<td style="font-size:11px !important;">
							<span>EMP &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							:&nbsp;&nbsp; <?php echo $order[0]['emp_name'];?> </span>
						</td>
					</tr></br>
				</table>
				<p></p>
			</div>
			<br><br><br><br><br>
			<div class="address" align="center">
					<h2>ORDER DETAILS </h2>
			</div><br>
<div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
				<div id="printable">
						<div  class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
							

									<table id="pp" class="table text-center">
									<!--	<thead> -->
										<tr>
											<th width="7%;" style="text-align:left;">S.NO</th>
											<th  width="25%;" style="text-align:left;">PRODUCT</th>
											<th  width="25%;" style="text-align:left;">DESIGN</th>
											<th  width="25%;" style="text-align:left;">WEIGHT</th>
											<th  width="25%;" style="text-align:left;">SIZE</th>
											<th  width="20%;"style="text-align:left;">PCS</th>
										</tr>
											<tr>
												<td><hr class="item_dashed"></td>
											</tr>
										<!--</thead>
										<tbody>-->
											<?php 
											$i=1;
											$no_of_piece=0;
											foreach($order as $items)
											{
												$no_of_piece+=$items['tot_items'];
												$total_weight+=$items['value']*$items['tot_items'];
											?>
											<tr>
											<td style="text-align:left;"><?php echo $i;?></td>
											<td style="text-align:left;"><?php echo $items['product_name'];?></td>
											<td style="text-align:left;"><?php echo $items['design_name'];?></td>
											<td style="text-align:left;"><?php echo $items['value'];?></td>
											<td style="text-align:left;"><?php echo $items['size'];?></td>
											<td style="text-align:left;"><?php echo $items['tot_items'];?></td>
											</tr>
											<?php $i++;
											}?>
											<tr>
												<td><hr class="item_dashed"></td>
											</tr>
									<!--</tbody> -->
										<tfoot>
											<tr>
											<td><b>TOTAL</b></td>
											<td></td>
											<td></td>
											<td><b><?php echo number_format($total_weight,3,'.','');?></b></td>
											<td></td>
											<td><b><?php echo $no_of_piece;?></b></td>
										
											</tr>
										</tfoot>
										<tr>
												<td><hr class="item_dashed"></td>
											</tr>
									</table><br>
								    <br>	
									
									<?php 

									foreach($order as $items){		
										$order_img=explode('#', $items['order_img']);
										if($items['order_img']!='')
										{?>
											 <br><label><?php echo $items['design_name'];?></label>
										 <br><br>	
										<?php 
										  foreach($order_img as $image)
    										{
    										    if($image!=''){?>
    										    <img alt="" width="20%;" width="20%;"  src="<?php echo base_url();?>assets/img/stock_order/<?php echo $image;?>">&nbsp;&nbsp;&nbsp;&nbsp;
    										    <?php 
    										}}
    										if($items['Description']!='')
    										{
    										  echo $items['Description'];
    										}
    										?>
    										<br>
										<?php }else if($items['Description']!='')
										{?>
										 <br><b><label><?php echo $items['design_name'];?></b></label>
										<?php
										    echo $items['Description'];
										}
									?>
									<?php }?>

									<?php 
									if(sizeof($order_des)>0)
									{?>
									<br><label>General Description</label>
										 <br><br>	
										<?php foreach($order_des as $des)
										{?>
										<?php echo $des['content'];?>
										<?php }
									}
									?>
								</div>	
							 </div>	
						</div>
						<br><br><br><br><br><br><br><br><br><br><br><br>
						<div class="row" style="text-transform:uppercase;">
							<label>Received By</label>
							<label style="margin-left:30%;">Vendor Sign</label>
						</div>
				</div>				
				
 </div>
 </div><!-- /.box-body --> 
</div>

 </span>          
</body></html>