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
					width:2550% !important;
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
				<div style="display: inline-block; width: 45%;display: inline-block;height:30px;">
					    <label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.'Mr./Ms.'.$rate_cut_details['karigar_name']."</div>"; ?></label><br>
    					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$rate_cut_details['mobile']."</div>"; ?></label><br>
    					<label><?php echo ($rate_cut_details['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_cut_details['address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($rate_cut_details['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($rate_cut_details['address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($rate_cut_details['city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_cut_details['city_name']).($rate_cut_details['pincode']!='' ? ' - '.$rate_cut_details['pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($rate_cut_details['state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_cut_details['state_name']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($rate_cut_details['country_name']!='' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_cut_details['country_name'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($rate_cut_details['pan_no']) && $rate_cut_details['pan_no']!='' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_cut_details['pan_no'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($rate_cut_details['gst_number']) && $rate_cut_details['gst_number']!='' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($rate_cut_details['gst_number'])."</div><br>" :''); ?></label>
                </div>
				
                
                <div style="display: inline-block; text-align: center !important; width: 19%; "></div>
                
               	<div style="width: 35%; display: inline-block;vertical-align: top;text-align: right">					 
					<label><?php echo ($rate_cut_details['date_add']!='' ? '<div class="addr_brch_labels">Issue Date &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_cut_details['date_add']."</div><br>" :''); ?></label><br>
    				<label><?php echo ($rate_cut_details['po_ref_no']!='' ? '<div class="addr_brch_labels">PO Ref No &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_cut_details['po_ref_no']."</div><br><br>" :''); ?></label>
					<label><?php echo ($rate_cut_details['ref_no']!='' ? '<div class="addr_brch_labels">Ref No &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_cut_details['ref_no']."</div><br>" :''); ?></label><br>
					<label><?php echo ($rate_cut_details['bill_no']!='' ? '<div class="addr_brch_labels">Bill No &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_cut_details['bill_no']."</div><br><br>" :''); ?></label>
					<label><?php echo ($rate_cut_details['transcation']!='' ? '<div class="addr_brch_labels">Trn Type &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_cut_details['transcation']."</div><br><br>" :''); ?></label>
					<label><?php echo ($rate_cut_details['conversion']!='' ? '<div class="addr_brch_labels">Convr Type &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$rate_cut_details['conversion']."</div><br><br>" :''); ?></label>   																
				</div>
			</div><br>
			<hr class="borderline">  
			<div style="width: 100%; text-transform:uppercase;margin-top:1px;">
			    <div style="text-align: center !important;"><?php echo $rate_cut_details['rate_cut_type'] == 1 ? "PAYMENT" : "GST PURCHASE"?></div>
			</div> 
			
			<div  class="content-wrapper">
 				<div class="box">
  					<div class="box-body">
 						<div  class="container-fluid">
							<div id="printable">
								<div  class="row" style="margin-top:2%;">
									<div class="col-xs-12">
										<div class="table-responsive">
											<hr class="n_item_dashed" style="width:100% !important;">
											<table id="pp" class="table text-center">
											<!--	<thead> -->
													<tr>
														<th class="alignLeft" style="width:5%">S.No</th>
														<th class="alignLeft" style="width:10%">Description</th>
														<th class="alignRight" style="width:10%">Purity</th>
														<th class="alignRight" style="width:5%">Pcs</th>
														<th class="alignRight" style="width:10%">Gwt</th>
														<th class="alignRight" style="width:10%">Nwt</th>
														<th class="alignRight" style="width:10%">Touch</th>
														<th class="alignRight" style="width:10%">Wast%</th>
														<th class="alignRight" style="width:10%">Pure Wt</th>
														<th class="alignRight" style="width:10%">Rate</th>
														<th class="alignRight" style="width:10%">M.C</th>
														<th class="alignRight" style="width:10%">Amount</th>
													</tr>
													<tr>
														<td><hr class="n_item_dashed" style=""></td>
													</tr>
												<!--</thead>
												<tbody>-->
											<?php 
											{ 
											$taxable_amount = ($rate_cut_details['total_amount']-$rate_cut_details['tax_amount']);
											?>
												<tr>
													<td class="alignLeft"><?php echo 1?></td>
													<td class="alignLeft"><?php echo $rate_cut_details['category_name'];?></td>
													<td class="alignRight"><?php echo 100;?></td>
													<td class="alignRight"><?php echo 0;?></td>
													<td class="alignRight"><?php echo moneyFormatIndia($rate_cut_details['pure_wt']);?></td>
													
													<td class="alignRight"><?php echo moneyFormatIndia($rate_cut_details['pure_wt']);?></td>
													<td class="alignRight"><?php echo 100;?></td>
													<td class="alignRight"><?php echo 0.000;?></td>
													<td class="alignRight"><?php echo moneyFormatIndia($rate_cut_details['pure_wt']);?></td>	
													<td class="alignRight"><?php echo moneyFormatIndia(number_format($rate_cut_details['rate_per_gram'],2,'.',''))?></td>	
													
													<td class="alignRight"><?php echo moneyFormatIndia(number_format($rate_cut_details['charges'],2,'.',''))?></td>	
													<td class="alignRight"><?php echo moneyFormatIndia(number_format($taxable_amount,2,'.',''));?></td>
												</tr>
											<?php } ?>
																					
											<?php 
											if($rate_cut_details['remark']!='') { ?>
												<tr>
													<td></td>
													<td colspan="11">REMARKS :- <?php echo $rate_cut_details['remark'];?></td>
												</tr>	
												
											<?php } ?>
											
									<!--</tbody> -->
										<tfoot>
											
											<?php
											if($rate_cut_details['cgst_cost']>0)
											{?>
											    <tr>
    												<td class="alignLeft"></td>
    												<td class="alignLeft"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>

    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
													<td class="alignRight"></td>

													<td class="alignRight"><?php echo "CGST(" . ($rate_cut_details['tax_percentage']/2) . "%)"; ?></b></td>
													<td class="alignRight"><b><?php echo moneyFormatIndia(number_format($rate_cut_details['cgst_cost'],2,'.',''))?></b></td>
    											</tr>
											<?php }
											?>
											
											<?php
											if($rate_cut_details['sgst_cost']>0)
											{?>
											    <tr>
    												<td class="alignLeft"></td>
    												<td class="alignLeft"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>

    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
													<td class="alignRight"></td>

													<td class="alignRight"><?php echo "SGST(" . ($rate_cut_details['tax_percentage']/2) . "%)"; ?></b></td>
    												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format($rate_cut_details['sgst_cost'],2,'.',''))?></b></td>
    											</tr>
											<?php }
											?>
											
											<?php
											if($rate_cut_details['igst_cost']>0)
											{?>
											   <tr>
    												<td class="alignLeft"></td>
    												<td class="alignLeft"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>

    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
													<td class="alignRight"></td>

													<td class="alignRight"><?php echo "IGST(" . number_format($rate_cut_details['tax_percentage'], 0, '.', '') . "%)"; ?></b></td>
    												<td class="alignRight"><?php echo moneyFormatIndia(number_format($rate_cut_details['igst_cost'],2,'.',''))?></td>
    											</tr>
											<?php }
											?>
											<?php
											if(isset($rate_cut_details['is_tds']) && $rate_cut_details['is_tds'] == 1 && $rate_cut_details['tds_tax_value'] > 0)
											{?>
											    <tr>
    												<td class="alignLeft"></td>
    												<td class="alignLeft"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
    												<td class="alignRight"></td>
													<td class="alignRight"></td>
													<td class="alignRight"></td>
													<td class="alignRight"><?php echo "TDS(" . $rate_cut_details['tds_percent'] . "%)"; ?></b></td>
													<td class="alignRight"><b><?php echo moneyFormatIndia(number_format($rate_cut_details['tds_tax_value'],2,'.',''))?></b></td>
    											</tr>
											<?php }
											?>
											
											<tr>
												<td><hr class="n_item_dashed"></td>
											</tr>
											<tr>
												<td class="alignLeft"><b>TOTAL</b></td>
												<td class="alignLeft"></td>
												<td class="alignRight"></td>
												<td class="alignRight"><b></b></td>
												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format((float)$rate_cut_details['pure_wt'],3,'.',''))?></b></td>
											
												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format((float)$rate_cut_details['pure_wt'],3,'.',''))?></b></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format((float)$rate_cut_details['pure_wt'],3,'.',''))?></b></td>
												<td class="alignRight"></td>
												
												<td class="alignRight"></td>
												<td class="alignRight"><b><?php echo moneyFormatIndia(number_format((float)$rate_cut_details['total_amount'],2,'.',''))?></b></td>
											</tr>
											<tr>
												<td><hr class="n_item_dashed" style=""></td>
											</tr>
										</tfoot>
										<tr>
												<td><hr class="n_item_dashed"></td>
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
									<?php echo $rate_cut_details['emp_name'] .'-'. date("d-m-y h:i:sa"); ?>
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