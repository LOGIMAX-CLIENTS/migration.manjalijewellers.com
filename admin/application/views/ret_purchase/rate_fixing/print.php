<?php
function moneyFormatIndia($num)
{
	$nums = explode(".",$num);
	if(count($nums)>2){
	return "0";
	}else{
	if(count($nums)==1){
	$nums[1]="00";
	}
	$num = $nums[0];
	$explrestunits = "" ;
	if(strlen($num)>3){
	$lastthree = substr($num, strlen($num)-3, strlen($num));
	$restunits = substr($num, 0, strlen($num)-3); 
	$restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
	$expunit = str_split($restunits, 2);
	for($i=0; $i<sizeof($expunit); $i++)
	{
	if($i==0)
	{
	$explrestunits .= (int)$expunit[$i].","; 
	}
	else
	{
	$explrestunits .= $expunit[$i].",";
	}
	}
	$thecash = $explrestunits.$lastthree;
	} else {
	$thecash = $num;
	}
	return $thecash.".".$nums[1]; 
	}
}
?>
<html><head>
		<meta charset="utf-8">
		<title>Job Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/metalissue_ack.css">
		<style type="text/css">
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
    			vertical-align:top; 
				width: 40%; 
				text-align: right;
    		}
    
    		.addr_brch_values {
    			display: inline-block;
    			vertical-align:top; 
				width: 40%; 
				text-align: left;
    		}

			.n_item_dashed
			{
					border-top:1px dashed black;
					border-bottom:0px;
					margin-left:0px !important;
					width:125	0% !important;
					padding-top:-1px !important;

			}
		</style>
</head><body>
<div class="PDFReceipt">
			    
			<div class="heading">
				<div class="company_name"><h1><?php echo strtoupper($comp_details['company_name']); ?></h1></div>
				<div><?php echo strtoupper($comp_details['address1']) ?> , <?php echo strtoupper($comp_details['address2']) ?></div>
				<?php echo ($comp_details['email']!='' ? '<div>Email : '.$comp_details['email'].' </div>' :'') ?>
				<?php echo ($comp_details['gst_number']!='' ? '<div>GST : '.$comp_details['gst_number'].' </div>' :'') ?>
			</div><br>
			<hr class="borderline">
			<div style="width: 100%; text-transform:uppercase;height:150px;">
				<div style="display: inline-block; width: 55%;display: inline-block;height:30px;">
					    <label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.'Mr./Ms.'.$rate_fix['karigar_name']."</div>"; ?></label><br>
    					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$rate_fix['mobile']."</div>"; ?></label><br>
    					<label><?php echo ($rate_fix['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_fix['address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($rate_fix['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($rate_fix['address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($rate_fix['city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_fix['city_name']).($rate_fix['pincode']!='' ? ' - '.$rate_fix['pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($rate_fix['state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_fix['state_name']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($rate_fix['country_name']!='' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_fix['country_name'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($rate_fix['pan_no']) && $rate_fix['pan_no']!='' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_fix['pan_no'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($rate_fix['gst_number']) && $rate_fix['gst_number']!='' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_fix['gst_number'])."</div><br>" :''); ?></label>
                </div>
				
                
                <!-- <div style="display: inline-block; text-align: center !important; width: 15%; "></div> -->
                
               	<div style="width: 35%; display: inline-block;vertical-align: top;text-align: right">					 
					<label><?php echo ($rate_fix['date_add']!='' ? '<div class="addr_brch_labels"> Date &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_fix['date_add']."</div><br>" :''); ?></label><br>
				 	<label><?php echo ($rate_fix['po_ref_no']!='' ? '<div class="addr_brch_labels">Selected PO  &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_fix['po_ref_no']."</div><br>" :''); ?></label><br>
					 <label><?php echo ($rate_fix['po_ref_no']!='' ? '<div class="addr_brch_labels">Rate Fix No &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_fix['rate_fix_id']."</div><br>" :''); ?></label><br>
				</div>
			</div><br>
			<hr class="borderline">  
			<div style="width: 100%; text-transform:uppercase;margin-top:1px;">
			    <div style="text-align: center !important;">RATE FIX RECEIPT</div>
			</div> 
			<div  class="content-wrapper">
 				<div class="box">
  					<div class="box-body">
 						<div  class="container-fluid">
							<div id="printable">
								<div  class="row" style="margin-top:2%;">
									<div class="col-xs-12">
										<div class="table-responsive">
											<hr class="item_dashed" style="width:100% !important;">
											<table id="pp" class="table text-center">
											<!--	<thead> -->
											    <tr>
														<th class="alignLeft">S.No</th>
														<th class="alignLeft">Description</th>
														<th class="alignRight">Purity</th>
														<th class="alignRight">Pcs</th>
														<th class="alignRight">Gwt</th>

														<th class="alignRight">Nwt</th>
														<th class="alignRight">Touch</th>
														<th class="alignRight">Wast%</th>
														<th class="alignRight">Pure Wt</th>
														<th class="alignRight">M.C</th>

														<th class="alignRight">Rate</th>
														<th class="alignRight">Amount</th>
													</tr>
													<tr>
														<td><hr class="item_dashed" style="width:1270% !important;"></td>
													</tr>
												<!--</thead>
												<tbody>-->
											<?php 
											{ 
												$taxable_amount = ($rate_fix['amount'] - $rate_fix['tax_amount']); //-$rate_fix['tax_amount']
												$tax_rate = (($rate_fix['rate_fix_rate']*$rate_fix['tax_percentage']/100)+$rate_fix['rate_fix_rate']);
											?>
												<tr>
													<td class="alignLeft"><?php echo 1?></td>
													<td class="alignLeft"><?php echo $rate_fix['metal'].($rate_fix['id_metal']==1 ? ' - 24KT Gold' : '- 24KT Silver' );?></td>
													<td class="alignRight"><?php echo 100;?></td>
													<td class="alignRight"><?php echo 0;?></td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$rate_fix['rate_fix_wt'],3,'.',''))?></td>
													
													<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$rate_fix['rate_fix_wt'],3,'.',''))?></td>
													<td class="alignRight"><?php echo 100;?></td>
													<td class="alignRight"><?php echo 0.000;?></td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$rate_fix['rate_fix_wt'],3,'.',''))?></td>
													<td class="alignRight"><?php echo 0.00;?></td>	
													
													<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)(($rate_fix['rate_fix_rate'])),2,'.',''))?></td>
													<td class="alignRight"><?php echo moneyFormatIndia(number_format((float)$rate_fix['tax_amount'],2,'.',''))?></td>
												</tr>
											<?php } ?>
														
											
											<?php 
											if($rate_fix['remark']!='') { ?>
												<tr>
													<td></td>
													<td colspan="9">REMARKS :- <?php echo $rate_fix['remark'];?></td>
												</tr>	
												
											<?php } ?>
											
									<!--</tbody> -->
										<tfoot>	
											
										
											
										<?php
											if($rate_fix['cgst_cost']>0)
											{?>
											    <tr>
												  	<td class="alignRight"></td>
													<td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
    												<td class="alignRight"></td>


													<td class="alignRight"></td>
												   <td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
													<td class="alignRight"></td>

													<td class="alignRight"><?php echo "CGST(" . ($rate_fix['tax_percentage']/2) . "%)"; ?></b></td>
    												<td class="alignRight"><?php echo moneyFormatIndia(number_format($rate_fix['cgst_cost'],2,'.',''))?></b></td>
    											</tr>
											<?php }
											?>
											
											<?php
											if($rate_fix['sgst_cost']>0)
											{?>
											    <tr>
												    <td class="alignRight"></td>
													<td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
    												<td class="alignRight"></td>

													<td class="alignRight"></td>
												    <td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
    												<td class="alignRight"></td>

													<td class="alignRight"><?php echo "SGST(" . ($rate_fix['tax_percentage']/2) . "%)"; ?></b></td>
    												<td class="alignRight"><?php echo moneyFormatIndia(number_format($rate_fix['sgst_cost'],2,'.',''))?></b></td>
    											</tr>
											<?php }
											?>
											
											<?php
											if($rate_fix['igst_cost']>0)
											{?>
											    <tr>
												   	<td class="alignRight"></td>
												   	<td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
    												<td class="alignRight"></td>

													<td class="alignRight"></td>
												    <td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
    												<td class="alignRight"></td>

													<td class="alignRight"><?php echo "IGST(" . number_format(($rate_fix['tax_percentage']), 0, '.', '') . "%)"; ?></td>
    												<td class="alignRight"><?php echo moneyFormatIndia(number_format($rate_fix['igst_cost'],2,'.',''))?></td>
    											</tr>
											<?php }
											?>
											
											<tr>
												<td><hr class="item_dashed" style="width:1270% !important;"></td>
											</tr>

											<tr>
												<td class="alignLeft"><b>TOTAL</b></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
    											<td class="alignRight"></td>
												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format((float)$rate_fix['rate_fix_wt'],3,'.',''))?></b></td>

												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format((float)$rate_fix['rate_fix_wt'],3,'.',''))?></b></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format((float)$rate_fix['rate_fix_wt'],3,'.',''))?></b></td>
												<td class="alignRight"></td>
												
												
												<td class="alignRight"></td>
												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format((float)$rate_fix['amount'],2,'.',''))?></b></td>
												
											</tr>

											<tr>
												<td><hr class="item_dashed" style="width:1270% !important;"></td>
											</tr>

											<tr>
												<td class="alignLeft"></td>
												<td  colspan="2"><b>INCLUDING GST</b></td>
												<!--<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>-->
												<td colspan="8"></td>
											</tr>

											<tr>
												<td class="alignLeft"></td>
												<td colspan="2"><b>RATE : <?php echo moneyFormatIndia(number_format((float)$tax_rate,2,'.',''))?></b></td>
												<!--<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>-->
												<td colspan="7"></td>
												<td class="alignRight"><b><?php echo ($rate_fix['crdr_amt'] > 0 ? $rate_fix['crdr'] : '')?></b></td>
												<td class="alignRight"><b><?php echo ($rate_fix['crdr_amt'] > 0 ? moneyFormatIndia(number_format((float)$rate_fix['crdr_amt'],2,'.','')):'')?></b></td>
												
											</tr>

										</tfoot>
										<tr>
												<td><hr class="item_dashed" style="width:1270% !important;"></td>
											</tr>
									</table><br>	
								</div>	
							 </div>	
						</div><br><br><br>
						<div class="footer">
							<div class="footer_left" style="width:25%;">
									<label>Audited By</label>
							</div>
							<div class="footer_left" style="width:20%;">
									<label>Party Sign</label>
							</div>
							<div class="footer_left" style="width:23%;">
									<label>Manager Sign</label>
							</div>
							<div class="footer_left"style="width:25%;">
									<label>Operator </label></br>
									<?php echo $rate_fix['emp'] .'-'. date("d-m-y h:i:sa"); ?>
							</div>
						</div>
            				
            			</div>
				</div>
 </div>
 </div><!-- /.box-body --> 
</div>
</div>  
<script type="text/javascript">
			setTimeout(function(){
			
				window.print();
			
			}, 1000);
		</script>        
</body></html>