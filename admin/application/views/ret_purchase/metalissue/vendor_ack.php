<html><head>
		<meta charset="utf-8">
		<title>Metal Issue Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/metalissue_ack.css">
		<style type="text/css">
			 .addr_labels {
                display: inline-block;
                width: 30%;
				/* padding-bottom: 5px; */
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
			<div style="width: 100%; text-transform:uppercase;margin-top:1px;">
				<div style="text-align: center !important;">JOB WORK ISSUE VOUCHER </div>
			</div>
			<hr class="borderline">  
			<div style="width: 100%; text-transform:uppercase;height:120px;">
			<hr class="borderline">
			<div style="width: 100%; text-transform:uppercase;height:150px;">
				<div style="display: inline-block; width: 50%;display: inline-block;height:30px;">
				<br>
					    <label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.'Mr./Ms.'.$issue['karigar_name']."</div>"; ?></label><br>
    					<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$issue['mobile']."</div>"; ?></label><br>
    					<label><?php echo ($issue['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($issue['address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($issue['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($issue['address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($issue['city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($issue['city_name']).($issue['pincode']!='' ? ' - '.$issue['pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($issue['state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($issue['state_name']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($issue['country_name']!='' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($issue['country_name'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($issue['pan_no']) && $issue['pan_no']!='' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($issue['pan_no'])."</div><br>" :''); ?></label>
    					<label><?php echo (isset($issue['gst_number']) && $issue['gst_number']!='' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($issue['gst_number'])."</div><br>" :''); ?></label>
                </div>
				
                
                <div style="display: inline-block; text-align: center !important; width: 7%; "></div>
                
               	<div style="width: 43%; display: inline-block;vertical-align: top;text-align: right">	
				   <label></label>
					<label><?php echo ($issue['issue_date']!='' ? '<div class="addr_brch_labels">Issue Date &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$issue['issue_date']."</div><br>" :''); ?></label>
					<label><?php echo ($issue['met_issue_ref_id']!='' ? '<div class="addr_brch_labels">Ref No &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$issue['met_issue_ref_id']."</div><br>" :''); ?></label>
					<label><?php echo ($issue['issue_type']!='' ? '<div class="addr_brch_labels">Issue Type &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$issue['issue_type']."</div><br>" :''); ?></label>		
					<label><?php echo ($issue['order_no']!='' ? '<div class="addr_brch_labels">Order no &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$issue['order_no']."</div><br>" :''); ?></label>				
					<label><?php echo ($issue['pur_no']!='' ? '<div class="addr_brch_labels">Po no &nbsp;&nbsp;</div><div class="addr_brch_values">&nbsp;&nbsp;:&nbsp;&nbsp;'.$issue['pur_no']."</div>" :''); ?></label>				
				</div>
			</div><br>
			<!-- <hr class="borderline">  
			<div style="width: 100%; text-transform:uppercase;margin-top:1px;">
			    <div style="text-align: center !important;">JOB WORK ISSUE VOUCHER </div>
			</div> --> 
			
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
														<th class="alignLeft">S.No</th>
														<th class="alignLeft">Description</th>
														<th class="alignRight">Purity</th>
														<th class="alignRight">Pcs</th>
														<th class="alignRight">Gwt</th>
														<th class="alignRight">Nwt</th>
														<th class="alignRight">Dia(CT)</th>
														<th class="alignRight">Uom</th>
														<th class="alignRight">Touch</th>
														<th class="alignRight">Wast%</th>
														<th class="alignRight">Pure Wt</th>
														<th class="alignRight">M.C</th>
														<th class="alignRight">Amount</th>
													</tr>
													<tr>
														<td colspan="13"><hr class="item_dashed" style="width:100% !important;"></td>
													</tr>
												<!--</thead>
												<tbody>-->
											<?php 
											$i=1;
											$total_pcs=0;
											$total_grs_wt=0;
											$total_net_wt=0;
											$total_pur_wt=0;
											$total_dia_wt=0;
											foreach($issue_details as $val)
											{
												$total_pcs+=$val['issue_pcs'];
												$issue_wt = $val['issue_wt'];
												$net_wt = (isset($val['stone_type']) && $val['stone_type'] == 0) ? $val['net_wt'] : $val['issue_wt'];
												$dia_wt = $val['dia_wt'];

												if(isset($val['uom_name']) && strtoupper($val['uom_name']) == 'CARAT') {
													$issue_wt = $issue_wt * 0.2;
													$net_wt = $net_wt * 0.2;
												}

												$total_grs_wt += $issue_wt;
												$total_net_wt += $net_wt;
    											$total_pur_wt += $val['issue_pure_wt'];
												$total_dia_wt += ($dia_wt * 0.2);
												
											?>
													<tr>
														<td class="alignLeft"><?php echo $i;?></td>
														<td class="alignLeft"><?php echo $val['product_name']."-".$val['design_name'];?></td>
														<td class="alignRight"><?php echo number_format((float)$val['purity'],3,'.','');?></td>
														<td class="alignRight"><?php echo $val['issue_pcs'];?></td>
														<td class="alignRight"><?php echo $val['issue_wt'];?></td>
														<td class="alignRight"><?php echo $val['net_wt'];?></td>
														<td class="alignRight"><?php echo $val['dia_wt'];?></td>
														<td class="alignRight"><?php echo $val['uom_name'];?></td>
														<td class="alignRight"><?php echo number_format($val['touch'],3,'.','');?></td>
														<td class="alignRight"><?php echo $val['wastage'];?></td>
														<td class="alignRight"><?php echo $val['issue_pure_wt'];?></td>	
														<td class="alignRight"><?php echo $val['mc'];?></td>	
														<td class="alignRight"><?php echo 0.00;?></td>
														
													</tr>
													<?php 
													if($val['hsn_code']!='') { ?>
														<tr style="font-weight:bold;">
															<td></td>
															<td colspan="5">HSN CODE :- <?php echo $val['hsn_code'];?></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>														
													<?php } ?>
													<?php 
													if($val['tag_code']!='') { ?>
														<tr style="font-weight:bold;">
															<td></td>
															<td colspan="5">TAG CODE :- <?php echo $val['tag_code'];?></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>														
													<?php } ?>

													<?php 
													if($val['bt_code']!='') { ?>
														<tr style="font-weight:bold;">
															<td></td>
															<td colspan="5">BT CODE :- <?php echo $val['bt_code'];?></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>														
													<?php } ?>
													<?php 
													if($val['po_ref_no']!='') { ?>
														<tr style="font-weight:bold;">
															<td></td>
															<td colspan="5">PO REF NO :- <?php echo $val['po_ref_no'];?></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>														
													<?php } ?>
											<?php $i++;
											}?>
											<?php 
											if($issue['remark']!='') { ?>
												<tr style="font-weight:bold;line-height:200%">
													<td></td>
													<td colspan="7"><span style="font-size:12px !important">REMARKS :- <?php echo $issue['remark'];?></span></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
												</tr>													
											<?php } ?>
											<tr>
												<td colspan="13"><hr class="item_dashed" style="width:100% !important;"></td>
											</tr>
									<!--</tbody> -->
										<tfoot>
											<tr>
												<td class="alignLeft"><b>TOTAL</b></td>
												<td class="alignLeft"></td>
												<td class="alignRight"></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_pcs)?></b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_grs_wt,3,'.','')?></b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_net_wt,3,'.','')?></b></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_dia_wt,3,'.','')?></b></td>
												<td></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
												<td class="alignRight"><b><?php echo number_format((float)$total_pur_wt,3,'.','')?></b></td>
												<td class="alignRight"></td>
												<td class="alignRight"></td>
											</tr>
										</tfoot>
										<tr>
										<td colspan="13"><hr class="item_dashed" style="width:100% !important;"></td>
											</tr>
									</table><br>	
								</div>	
							 </div>	
						</div>
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
									<?php echo $issue['emp_name'] .'-'. date("d-m-y h:i:sa"); ?>
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