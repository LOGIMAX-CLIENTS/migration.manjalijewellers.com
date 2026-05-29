<html><head>
		<meta charset="utf-8">
		<title>Repair Order Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/metalissue_ack.css">
			<style type="text/css">
		body, html {
		margin-bottom:0;
		font-size: 12px !important;
		}
		 span { display: inline-block; }
		 
		 .addr_headers_labels {
            display: inline-block;
            width: 20%;
        }
        
        .addr_rate_labels {
            display: inline-block;
            width: 10%;
        }
        
        .addr_labels {
            display: inline-block;
            width: 20%;
        }
		
        .addr_values {
            display: inline-block;
            padding-left: -5px;
        }

		.addr_delv_labels {
			display: inline-block;
			width: 25%;
		}

		.addr_delv_values {
			display: inline-block;
			padding-left: 5px;
		}

		.addr_brch_labels {
			display: inline-block;
			width: 20%;
		}

		.addr_brch_values {
			display: inline-block;
			padding-left: 5px;
		}
		
	</style>
</head><body>
<?php 
	    function moneyFormatIndia($num) {
        	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
        }
	    ?>
<span class="PDFReceipt" >
    
               <div style="width: 100%; text-transform:uppercase;font-size: 11px !important;" align="center;">
			        <img alt=""  src="<?php echo base_url();?>assets/img/receipt_logo.jpg" ><br>
			    </div><br>
			    
	
			<div style="width: 100%; text-transform:uppercase;font-size: 11px !important;">
    	        	<div style="display: inline-block; width: 48%; padding-left:0px;">
    					<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.'Mr./Ms.'.$repair[0]['firstname']."</div>"; ?></label><br>
    					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$repair[0]['mobile']."</div>"; ?></label><br>
    					<label><?php echo ($repair[0]['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($repair[0]['address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($repair[0]['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($repair[0]['address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($repair[0]['address3']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($repair[0]['address3']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($repair[0]['village_name']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($repair[0]['village_name']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($repair[0]['city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($repair[0]['city_name']).($repair[0]['pincode']!='' ? ' - '.$repair[0]['pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($repair[0]['state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($repair[0]['state_name'].($repair[0]['state_code']!='' ? '-'.$repair[0]['state_code'] :'')).'.'."</div><br>" :''); ?></label>
    				</div>
    				<div style="display: inline-block; width: 10%; padding-left:0px;"></div>
    				<div style="width: 50%; text-align: right !importan; display: inline-block; vertical-align: top;margin-top:-14px !important;">
    					<div style="text-align: left !important;width: 100%; display: inline-block;"> 
        						<label><?php echo ($comp_details['name']!='' ? '<div class="addr_brch_labels">Branch</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['name']).','."</div><br>" :''); ?></label>
        						<label><?php echo ($comp_details['address1']!='' ? '<div class="addr_brch_labels">Address</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['address1']).','."</div><br>" :''); ?></label>
        						<label><?php echo ($comp_details['address2']!='' ? '<div class="addr_brch_labels"></div><div class="addr_brch_values">&nbsp;&nbsp;&nbsp;'.strtoupper($comp_details['address2']).','."</div><br>" :''); ?></label>
        						<label><?php echo ($comp_details['city']!='' ? '<div class="addr_brch_labels">Place</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['city']).($comp_details['pincode']!='' ? ' - '.$comp_details['pincode'].'.' :'')."</div>" :''); ?><br></label>
        						<label><?php echo ($comp_details['state']!='' ? '<div class="addr_brch_labels">State</div><div class="addr_brch_values">:&nbsp;&nbsp;'.strtoupper($comp_details['state'].($comp_details['state_code']!='' ? '-'.$comp_details['state_code']  :'')).'.'."</div><br>" :''); ?></label>
						</div>
					</div>
			    </div><p></p>
			    
			    <div style="width: 100%; text-transform:uppercase;font-size: 11px !important;text-align:center;margin-top:-45px !important;">
			        <label><b>Repair Order</b></label>
			    </div><p></p>
			
			
			
		    
	    	<div style="width: 100%; text-align: right; text-transform:uppercase;display: inline-block;margin-top:-40px;font-size: 11px !important">
			    <div style="text-align: right; width:100%;height: 18px;">
					<div style="width: 80%; display: inline-block"> Order Date &nbsp; : &nbsp; </div>
					<div style="width: 20%; display: inline-block; margin-top: -2px;text-align: left;"><?php echo $repair[0]['order_date']; ?></div>
				</div>
		
				
					<div style="text-align: right; width:100%;height: 18px;">
					<div style="width: 80%; display: inline-block"> Order no &nbsp; : &nbsp; </div>
					<div style="width: 15%; display: inline-block; margin-top: -2px;text-align: left;"><?php echo $repair[0]['order_no']; ?></div>
				</div>
				
			</div>
			
			<br><br><br><br><br>
			
<div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
				<div id="printable">
						<div  class="row" style="margin-top:-8%;">
							<div class="col-xs-12">
								<div class="table-responsive">
								    <hr class="item_dashed" style="width:95% !important;">
									<table id="pp" class="table text-center" style="text-transform:uppercase;">
									<!--	<thead> -->
											<tr>
										     	<th width="15%;" style="text-align:left;">S.NO</th>
												<th  width="25%;" style="text-align:left;">Product</th>
												<th  width="25%;" style="text-align:left;">Design</th>
												<th  width="40%;" style="text-align:left;">Sub Design</th>
												<th  width="25%;" style="text-align:left;">Pcs</th>
												<th  width="25%;" style="text-align:left;">Gross Wt(Gms)</th>
												<th  width="25%;" style="text-align:left;">Less Wt</th>
												<th  width="25%;" style="text-align:left;">Net Wt</th>
												<th  width="25%;" style="text-align:left;">Type</th>
											</tr>
											<tr>
												<td><hr class="item_dashed" style="width:1600% !important;"></td>
											</tr>
										<!--</thead>
										<tbody>-->
											<?php 
											$i=1;
											$total_wt=0;
											$totalitems=0;
											$stone_charges=0;
											foreach($repair_details as $val)
											{
    											$totalitems+=$val['totalitems'];
    											$total_wt+=$val['weight'];
												$total_less_wt+=$val['less_wt'];
    											$total_net_wt+=$val['net_wt'];
												foreach($val['stone_details'] as $sval){
													$stone_charges+=$sval['amount'];
													}

											?>
												<tr>
												<td style="text-align:left;"><?php echo $i;?></td>
														<td style="text-align:left;"><?php echo $val['product_name'];?></td>
														<td style="text-align:left;"><?php echo $val['design_name'];?></td>
														<td style="text-align:left;"><?php echo $val['sub_design_name'];?></td>
														<td style="text-align:left;"><?php echo $val['totalitems'];?></td>
														<td style="text-align:left;"><?php echo $val['gross_wt'];?></td>
														<td style="text-align:left;"><?php echo $val['less_wt'];?></td>
														<td style="text-align:left;"><?php echo $val['net_wt'];?></td>
														<td style="text-align:left;"><?php echo $val['repair_type'];?></td>
													</tr>

													<?php
    								foreach($val['stone_details'] as $sval)
									
    							    {?>
    							        <tr>
									    	<td></td>
    							            <td></td>
											<td></td>
    							            <td><?php echo $sval['stone_name'];?></td>
    							            <td></td>
											<td style="text-align:left;"><?php echo $sval['wt'].'/'.$sval['uom_short_code'];?></td>
    							            <td style="text-align:left;">Rs.<?php echo $sval['rate_per_gram'].'/'.$sval['uom_short_code'];?></td>
    							            <td style="text-align:left;">Rs.<?php echo moneyFormatIndia(number_format($sval['amount'],2,'.',''))?></td>
											<td style="text-align:left;"></td>
    							        </tr>
    							    <?php }?>

    							    <?php if($stone_charges>0){?>
    							     <tr style="font-weight:bold;">
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td></td>
    							            <td style="text-align:left;"></td>
											<td style="text-align:left;">TOTAL</td>
    							            <td style="text-align:left;">Rs.<?php echo moneyFormatIndia(number_format($stone_charges,2,'.',''))?></td>
											<td style="text-align:left;"></td>
    							        </tr>
    							      <?php }?>	
									  <?php 
										if($val['description']!=''){?>
											<tr>
													<td></td>
													<td colspan="11">REMARKS :- <?php echo $val['description'];?></td>
												</tr>
										<?php }
										?>

											<?php $i++;
											}?>
											<tr>
												<td><hr class="item_dashed" style="width:1600% !important;"></td>
											</tr>
									<!--</tbody> -->
										<tfoot>
											<tr>
											<td colspan="4"><b>TOTAL</b></td>
											<td><b><?php echo number_format((float)$totalitems,0,'.','')?></b></td>
											<td><b><?php echo number_format((float)$total_wt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$total_less_wt,3,'.','')?></b></td>
											<td><b><?php echo number_format((float)$total_net_wt,3,'.','')?></b></td>
											</tr>
										</tfoot>
										<tr>
												<td><hr class="item_dashed" style="width:1600% !important;"></td>
											</tr>
									</table><br>	
								</div>	
							 </div>	
						</div>
				</div>
 </div>
 </div><!-- /.box-body --> 
</div>

 </span>          
</body></html>