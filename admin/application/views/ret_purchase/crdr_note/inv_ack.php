<html><head>
		<meta charset="utf-8">
		<title>Credit or Debit Note</title>
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
					    <label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.'Mr./Ms.'.$crdrdetails[0]['supplier_name']."</div>"; ?></label><br>
    					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$crdrdetails[0]['mobile']."</div>"; ?></label><br>
    					<label><?php echo ($crdrdetails[0]['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($crdrdetails[0]['address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($crdrdetails[0]['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($crdrdetails[0]['address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($crdrdetails[0]['city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($crdrdetails[0]['city_name']).($crdrdetails[0]['pincode']!='' ? ' - '.$crdrdetails[0]['pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($crdrdetails[0]['state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($crdrdetails[0]['state_name']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($crdrdetails[0]['country_name']!='' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($crdrdetails[0]['country_name'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($crdrdetails[0]['pan_no']) && $crdrdetails[0]['pan_no']!='' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($crdrdetails[0]['pan_no'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($crdrdetails[0]['gst_number']) && $crdrdetails[0]['gst_number']!='' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($crdrdetails[0]['gst_number'])."</div><br>" :''); ?></label>
                </div>
				
                
                <div style="display: inline-block; text-align: center !important; width: 19%; "></div>
                
               	<div style="width: 35%; display: inline-block;vertical-align: top;text-align: right">					 
					<label><?php echo ($crdrdetails[0]['grndate']!='' ? '<div class="addr_brch_labels">Date &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$crdrdetails[0]['grndate']."</div><br>" :''); ?></label><br>
					<label><?php echo ($crdrdetails[0]['po_ref_no']!='' ? '<div class="addr_brch_labels">Po refno &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$crdrdetails[0]['po_ref_no']."</div><br>" :''); ?></label>
    											
				</div>
			</div><br>
			<hr class="borderline">  
			<div style="width: 100%; text-transform:uppercase;margin-top:1px;">
			    <div style="text-align: center !important;"><b><?php echo($crdrdetails[0]['crdrtype']=='Cr' ? "CREDIT NOTE" : "DEBIT NOTE")?></b></div>
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
											<table id="pp" class="table text-center" style="width:100% !important;">
											<!--	<thead> -->
													<tr>
														<th class="alignLeft" style="width:20%">S.No</th>
														<th class="alignLeft" style="width:20%">Description</th>
														<th class="alignRight" style="width:20%">Fixed Wt</th>
														<th class="alignRight" style="width:20%">Fixed Rate</th>
														<th class="alignRight" style="width:20%">Amount</th>
													</tr>
													<tr>
														<td><hr class="item_dashed" style="width:520% !important;"></td>
													</tr>
												<!--</thead>
												<tbody>-->
											<?php 
											$i=1;
											$total_fixed_wt=0;
											$total_fixed_rate=0;
											$total_crdr_amt=0;
											foreach($crdrdetails as $val)
											{	
												$total_fixed_wt+=$val['ratefixwt'];
												$total_fixed_rate+=$val['fixrate'];
												$total_crdr_amt+=number_format($val['crdr'],2,'.','');							
											?>
													<tr>
														<td class="alignLeft"><?php echo $i;?></td>
														<td class="alignLeft"><?php echo $val['category_name'];?></td>
														<td class="alignRight"><?php echo number_format((float)$val['ratefixwt'],3,'.','');?></td>
														<td class="alignRight"><?php echo number_format($val['fixrate'],2,'.','');?></td>
														<td class="alignRight"><?php echo number_format($val['crdr'],2,'.','');?></td>															
													</tr>
													
											<?php $i++;
											}?>
											<tr>
												<td><hr class="item_dashed" style="width:520% !important;"></td>
											</tr>
									<!--</tbody> -->
										<tfoot>
											<tr>
												<td class="alignLeft"><b>TOTAL</b></td>
												<td class="alignLeft"></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_fixed_wt,3,'.','')?></b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_fixed_rate,2,'.','')?></b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_crdr_amt,2,'.','')?></b></td>
											</tr>
										</tfoot>
										<tr>
												<td><hr class="item_dashed" style="width:520% !important;"></td>
											</tr>
									</table><br>	
								</div>	
							 </div>	
						</div><br><br><br>
            				
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