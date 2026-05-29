<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet"/>
<div class="main-container">
	<!-- main -->		  
	<div class="main">
	  <!-- main-inner --> 
		<div class="main-inner">
		 <!-- container --> 
			<div class="container dashboard">
			<!-- alert -->
				<div class="row">
					<div class="col-md-12">
						<div align="center"><legend class="head">PURCHASE PLAN DETAIL</legend></div>
						<?php
						if($this->session->flashdata('successMsg')) { ?>
							<div class="alert alert-success" align="center">
							  <button type="button" class="close" data-dismiss="alert">&times;</button>
							  <strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
							</div>      
						<?php } if($this->session->flashdata('errMsg')) { ?>							 
							<div class="alert alert-danger" align="center">
							  <button type="button" class="close" data-dismiss="alert">&times;</button>
							  <strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
							</div>
						<?php } ?>
					</div>		
					<!-- /alert -->  
				</div>
				<?php
				if($account['customer']['auto_debit_plan_type'] == 1){ ?>
				<div class="row">
					<div class="card">
						<div class="card_content">
							<h4 class="card_title">
								<?php 
								if(($account['customer']['auto_debit_status'] == 0 || $account['customer']['auto_debit_status'] == 5) && $account['customer']['paid_installments'] < $account['customer']['total_installments']){
									echo "Cashfree Subscription automates your monthly payments after the initial checkout is completed. Subscribe Now!!";
									echo '<a href="#confirm-subscribe" data-href="'.base_url().'index.php/chitscheme/cf_subscription/1/'.$account['customer']['id_scheme_account'].'" class="btn btn-xs btn-success open-modal cf_ab_subscribe pull-right" data-toggle="modal">Subscribe</a>';
								} 
								else if($account['customer']['auto_debit_status'] == 1 && $account['customer']['auth_link'] != NULL){
									echo 'Subscription has been created and is ready to be authorized.<a class="btn btn-success pull-right" href="'.$account['customer']['auth_link'].'">Click to authorize</a>';
								}
								else if($account['customer']['auto_debit_status'] == 3){
									echo "<i class='fa fa-check-square bg-green'></i> Cashfree Auto-Debit Subscribed for this plan.Subscription expires on ".$account['customer']['sub_expires_on'];
									echo '<a href="#confirm-unsubscribe" data-href="'.base_url().'index.php/chitscheme/cf_subscription/2/'.$account['customer']['id_scheme_account'].'" class="btn btn-xs btn-danger open-modal cf_ab_unsubscribe pull-right" title="You can cancel Auto-Debit, by clicking Unsubscribe." rel="tooltip" data-toggle="modal">Unsubscribe</a>';
								}
								else {
									if($account['customer']['auto_debit_status'] == 2){
										echo "Subscription Status : <span style='color:red'>BANK AUTHORIZATION PENDING</span>";
									}
									else if($account['customer']['auto_debit_status'] == 4){
										echo "Subscription Status : <span style='color:yellow'>ON HOLD</span>";
									}
									else if($account['customer']['auto_debit_status'] == 6){
										echo "Subscription Status : <span style='color:yellow'>COMPLETED</span>";
									}
								}
								?>
							</h4>
						</div>
					</div>
				</div>
				<?php } ?>
				<br/>
				<div class="row">
					<div class="col-md-12">
						<div class="widget">
							<div class="widget-header"> 
								<!--<i class="icon-list-alt"></i>-->
								<!--  <h3> Scheme Account Details</h3>-->
								Purchase plan Account Details
							</div>	
							<div class="widget-content">
								<div class="row">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-3" align="center" >
											  <?php 
												if (@getimagesize(base_url().'admin/assets/img/customer/'.$customer['id_customer'].'/customer.jpg')) {
													echo '<img  class="img-thumbnail" src="'.base_url().'admin/assets/img/customer/'.$customer['id_customer'].'/customer.jpg"  width="140px" height="140px" >';
													}
													else {
													echo '<img  class="img-thumbnail" src="'.base_url().'admin/assets/img/default.png"  width="140px" height="140px" >';
												}
												?>
											</div>
											<?php if($account['customer']['one_time_premium']==0){?> 
											<div class="col-md-2" id="report-details"><b>Customer Name</b></div>
											<div class="col-md-2" id="report-details"><?php echo  $account['customer']['name'];?></div>
											<div class="col-md-2" id="report-details"><b>Mobile</b></div>
											<div class="col-md-2" id="report-details"><i class="icon-phone-sign"></i>   <?php echo $account['customer']['mobile']; ?></div>
											<div class="col-md-2"  id="report-details"><b>A/c No</b></div>
											<div class="col-md-2" id="report-details">
												<td><?php 
												echo ($account['customer']['scheme_ac']!='' && $account['customer']['scheme_ac']!=' Not Allocated'? $account['customer']['scheme_ac']:$this->config->item('default_acno_label'));
												?>
												</td>
											</div>
											<div class="col-md-2" id="report-details"><b>Payable</b></div>
											   <?php 
											   if($account['customer']['scheme_type']=='Flexible'){ ?>
											  <div class="col-md-2" id="report-details"><?php echo ($account['customer']['currency_symbol']." Min ".$account['customer']['min_amount']." Max ".$account['customer']['max_amount']); ?></div>
												<?php } else{?>
												<div class="col-md-2" id="report-details"><?php echo ($account['customer']['scheme_type'] == 'Amount' || $account['customer']['scheme_type'] == 'Amount to Weight'?$account['customer']['currency_symbol']." ".$account['customer']['payable']: "Min ".$account['customer']['min_weight']." g"." Max ".$account['customer']['max_weight']." g/month"); ?></div>
												<?php }?>
												<div class="col-md-2" id="report-details"><b>A/C Name</b></div>
												<div class="col-md-2" id="report-details"><?php echo ucfirst($account['customer']['account_name']); ?></div>
												<div class="col-md-2" id="report-details"><b>Scheme Type</b></div>
												<div class="col-md-2" id="report-details"><?php echo $account['customer']['scheme_type']; ?></div>
												<div class="col-md-2" id="report-details"><b>Paid Installments</b></div>
												<div class="col-md-2" id="report-details"><?php echo '<span class="badge bg-green">'.($account['customer']['paid_installments']!='' ? $account['customer']['paid_installments']:'0').'/'.$account['customer']['total_installments'].'</span>'; ?></div> 
												<div class="col-md-2" id="report-details"><b>Start Date</b></div>
												<div class="col-md-2" id="report-details"><?php echo $account['customer']['start_date']; ?></div>
												<?php 	if($this->session->userdata('branch_settings')==1){?>
												<div class="col-md-2" id="report-details"><b>Branch</b></div>
												<div class="col-md-2" id="report-details"><?php echo $account['customer']['branch_name']; ?></div>
												<?php } ?>
												<?php if ($account['customer']['is_multi_commodity']=='1'){ ?>  <!-- metal type showed based on  is_multi_commodity sett HH-->
												<div class="col-md-2" id="report-details"><b>Metal</b></div>
												<div class="col-md-2" id="report-details"><?php echo ($account['customer']['id_metal']==1 ? '<span class="badge bg-yellow">'. $account['customer']['metal'].'</span>':'<span class="badge bg-gray">'. $account['customer']['metal'].'</span>'); ?></div>
												<?php } ?>
												<div class="col-md-2" id="report-details"><b>Address</b></div>
												<div class="col-md-2" id="report-details"><?php echo $account['customer']['address1']; ?> <br/>
												   <?php echo $account['customer']['address2']; ?><br/>
												   <?php echo $account['customer']['address3']; ?><br/>
												</div>
											   <!--	<?php 	if($account['gift']['has_gift']==1 ){?>
												<div class="col-md-2" id="report-details"><b>Issued Gift Description</b></div>
												<div class="col-md-2" id="report-details"><?php echo ($account['gift']['type']==1 ? $account['gift']['gift_desc'] :'-');?></div></br>
												<div class="col-md-2" id="report-details"><b>Issued Gift Date</b></div>
												<div class="col-md-2" id="report-details"><?php echo ($account['gift']['type']==1 ? $account['gift']['date_issued'] :'-');?></div>
												<?php } ?>-->
												<!--<?php 	if($account['prize']['has_prize']==1 ){?>
												<div class="col-md-2" id="report-details"  class="btn btn-primary" style="font-size: 12px;"><b>Issued Prize Description</b></div>
												<div class="col-md-1" id="report-details"  class="btn btn-primary" style="font-size: 12px;"><?php echo ($account['prize']['type']==2 ? $account['prize']['gift_desc'] :'-');?></div></br>
												<div class="col-md-2" id="report-details"  class="btn btn-primary" style="font-size: 12px;"><b>Issued Prize Date</b></div>
												<div class="col-md-1" id="report-details"  class="btn btn-primary" style="font-size: 12px;"><?php echo ($account['prize']['type']==2 ? $account['prize']['date_issued'] :'-');?></div>
												<?php } ?>-->
												<?php }else{?>
												<div class="col-md-2" id="report-details"><b>Account Name</b></div>
												<div class="col-md-2" id="report-details"><?php echo  $account['customer']['account_name'];?></div>
												<div class="col-md-2" id="report-details"><b>Amount</b></div>
												<div class="col-md-2" id="report-details"><?php echo  $account['customer']['currency_symbol'].' '.$account['customer']['firstPayment_amt'];?></div>
												<div class="col-md-2" id="report-details"><b>Maturity Date</b></div>
												<div class="col-md-2" id="report-details"><?php echo  $account['customer']['maturity_date'];?></div>
												<div class="col-md-2" id="report-details"><b>Start Date</b></div>
												<div class="col-md-2" id="report-details"><?php echo  $account['customer']['start_date'];?></div>
												<?php if($account['customer']['flexible_sch_type']==1){ ?>
												<div class="col-md-2" id="report-details"><b>Rate Fixed on</b></div>
												<div class="col-md-2" id="report-details"><?php echo  $account['customer']['fixed_rate_on'];?></div>
												<div class="col-md-2" id="report-details"><b>Rate Fixed</b></div>
												<div class="col-md-2" id="report-details"><?php echo ($account['customer']['fixed_metal_rate']!=0.00  ? 'YES' :'NO');?></div>
												<div class="col-md-2" id="report-details"><b>Rate Fixed Through</b></div>   
												<div class="col-md-2" id="report-details"><?php echo  $account['customer']['rate_fixed_in'];?></div>
												<div class="col-md-2" id="report-details"><b>Fixed Gold Rate</b></div>
												<div class="col-md-2" id="report-details"><?php echo ($account['customer']['fixed_metal_rate']!='' ? $account['customer']['currency_symbol'].' '.$account['customer']['fixed_metal_rate'] :'-');?></div>
												<?php } ?>
												<div class="col-md-2" id="report-details"><b>Weight</b></div>
												<div class="col-md-2" id="report-details"><?php echo $account['customer']['fixed_wgt'].' '.g;?></div>
												<?php if($account['customer']['firstPayment_amt'] > 0 && $account['customer']['fixed_metal_rate']==0.00){ ?>
												<input type="hidden" id="rate_select" name="rate_select" value="<?php echo $account['details'][0]['rate_select']; ?>">
												<input type="hidden" id="start_date" name="start_date" value="<?php echo $account['details'][0]['start_date']; ?>">
												<br><div class="col-md-2"><button id="fix_rate" class="btn btn-primary" style="font-size: 12px;">Fix Rate</button></div>
												<?php }?>
											<?php }?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>	    
		  <!-- showed gift/price details to the users based on the type HH-->
				<?php if (($account['customer']['has_gift']=='1')){ ?>	
				<div class="row">
					<div class="col-md-6">
						<div class="table-responsive">
							<table class="table table-bordered table-striped ">
								<thead>
									<tr class="success">
										<th>Issued Gift Description</th>
										<th>Issued Gift Date</th>
									</tr>
								</thead>
								<tbody>
									<?php  foreach($account['gift'] as $gift) { ?>
									<tr>
										<td> <?php echo $gift['gift_desc']; ?> </td>
										<td>  <?php echo $gift['date_issued']; ?> </td>
									</tr>	
										<?php  } ?>	
								</tbody>
							</table>	
						  </br>
						</div>				
					</div>
					<?php }?>
					<?php if (($account['customer']['has_prize']=='1')){ ?>
					<div class="col-md-6">
						<div class="table-responsive">
							<table class="table table-bordered table-striped ">
								<thead>
									<tr class="success">
										<th>Issued Prize Description</th>
										<th>Issued Prize Date</th>
									</tr>
								</thead>
								<tbody>
								  <?php  foreach($account['prize'] as $prize) {	?>
									<tr>
										<td> <?php echo $prize['gift_desc']; ?> </td>
										<td>  <?php echo $prize['date_issued']; ?> </td>
									</tr>
									<?php  } ?>	
								</tbody>
							</table>	
							</br>
						</div>				
					</div>	
				</div>
				<?php }?>		
			<!-- showed gift/price details to the users based on the type -->     
			  <?php	$i =0;
					if (($account['customer']['type']=='0')){ ?>	
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-bordered table-striped ">
								<thead>
									<tr class="success">
										<th style="width:5%">Due Month</th>
										<th>Date</th>
										<?php 	if($this->session->userdata('branch_settings')==1){?>
											<!--	<th width="10%">Paid At</th>-->
										  <?php } ?>
										<th>Mode</th>
									<!--	<th>Charge </th>	-->										
										<th>Purchase plan payment </th>											
										<th>GST </th>	
										<th>Status </th>										
										<th>Balance Amount </th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$prev_amt = number_format($account['customer']['balance_amount'],"2",".","");  
									?>
									<tr class="warning" >
										<td colspan="6" style="background-color:#d8e5f0;text-align:center;font-weight:bold">PREVIOUS BALANCE</td>
										<td style="background-color:#d8e5f0;"> <?php echo  $account['customer']['currency_symbol']." ".number_format(( $prev_amt),"2",".",""); ?> </td>
									</tr>
									<?php 
										$bal_amt = $prev_amt;	
										$i = ($prev_amt=='0'?0:$account['customer']['ins']);														
										foreach($account['payment'] as $pay)
											{
											$gst_amt = ($pay['gst_type']==1 ?$pay['payment_amount']*($pay['gst']/100):$pay['payment_amount']-($pay['payment_amount']*(100/(100+$pay['gst']))));
											$b_amt = number_format(($bal_amt + ($pay['payment_amount'] != ""? $pay['payment_amount']:0)),"2",".","");
											$bal_amt = ($pay['gst'] > 0 ? ($pay['gst_type'] == 0 ?$b_amt - $gst_amt : $b_amt):$b_amt);
										?>
										<tr>
											<td><?php echo $pay['due_month'];?></td>
											<td><?php echo $pay['date_payment']; ?> </td>  
											<!--<td><?php echo $pay['name']; ?> </td>	-->
											<td><?php echo ($pay['mode']== 'FP' ? 'Free' :$pay['mode'] ); ?> </td>
											<!--<td><?php echo  $pay['currency_symbol']." ".$pay['add_charges']; ?> </td>-->		
											<td><?php echo  $pay['currency_symbol']." ".$pay['payment_amount']; ?> <br/><?php echo ($pay['gst'] > 0 ? ($pay['gst_type'] == 0?"<span style='color:#7ea0bd;'>Inclusive of GST</span>":""):"")?></td>
											<td><?php echo ($pay['gst'] > 0 ? number_format( $gst_amt,"2",".",""):'-');?></td>		
											<td><?php echo $pay['payment_status'] ; ?> </td>														
											<td><?php echo $pay['currency_symbol']." ".number_format(($bal_amt),"2",".","") ; ?> </td>															
										</tr>	
									<?php  } ?>	
								</tbody>
							</table>	
							</br>
						</div>				
					</div>	
				</div>
				<?php }?>
				<?php if (($account['customer']['type']=='1' || $account['customer']['type']=='2' || $account['customer']['type']=='3')&&($account['customer']['one_time_premium']==0)){ ?>	
				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered table-striped table-responsive display">
							<thead>
								<tr class="info">
									<th style="width:10%">Due Month</th>
									<th>Date</th>
									<?php 	if($this->session->userdata('branch_settings')==1){?>
									<!--	<th>Paid At</th>-->
								  <?php } ?>
									<th>Mode</th>
									<th>Rate</th>
									<!--<th>Charge</th>	-->												
									<th>Purchase plan Payment </th>											
									<th>GST</th>											
									<th>Status</th>											
									<th>Weight (g)</th>
									<th>Balance Amount </th>
									<th>Balance Weight (g)</</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									$prev_wgt = number_format($account['customer']['balance_weight'],"3",".","");  
									$prev_amt = number_format($account['customer']['balance_amount'],"2",".","");  
								?>
								<tr class="warning" >
									<td colspan="8" style="background-color:#d8e5f0;text-align:center;font-weight:bold">PREVIOUS BALANCE</td>
									<td style="background-color:#d8e5f0;"> <?php echo $account['customer']['currency_symbol']." ".number_format(( $prev_amt),"2",".",""); ?> </td>
									<td style="background-color:#d8e5f0;"> <?php echo number_format(( $prev_wgt),"3",".","").' g'; ?> </td>
								</tr>
								<?php 
									$bal_amt = $prev_amt;
									$bal_wgt =$prev_wgt ;	
									foreach($account['details'] as $detail)
									{
									  $gst_amt = ($detail['gst_type']==1 ?$detail['payment_amount']*($detail['gst']/100):$detail['payment_amount']-($detail['payment_amount']*(100/(100+$detail['gst']))));
									  $b_amt = number_format(($bal_amt + ($detail['payment_amount'] != ""? $detail['payment_amount']:0)),"2",".","");
									  $bal_amt = ($detail['gst'] > 0 ? ($detail['gst_type'] == 0 ?$b_amt - $gst_amt : $b_amt):$b_amt);
									  $bal_wgt = number_format(($bal_wgt + ($detail['metal_weight'] != ""? $detail['metal_weight']:0)),"3",".","");
								?>
								<tr>
									<td><?php echo $detail['due_month'];?></td>
									<td><?php echo $detail['date_payment']; ?> </td>
									<td><?php echo $detail['mode']; ?> </td>
									<!--<td><?php echo $detail['name']; ?> </td>	-->
									<td><?php echo $detail['currency_symbol']." ". $detail['metal_rate']; ?> </td>						
									<!--<td><?php echo  $detail['currency_symbol']." ".$detail['add_charges']; ?> </td>		-->
									<td><?php echo $detail['currency_symbol']." ". $detail['payment_amount']; ?><br /> <?php echo ($detail['gst'] > 0 ? ($detail['gst_type'] == 0?"<span style='color:#7ea0bd;'>Inclusive of GST</span>":""):"")?></td>		
									<td><?php echo ($detail['gst'] > 0 ? number_format( $gst_amt,"2",".",""):'-');?></td>		
									<td><?php echo $detail['payment_status'] ?> </td>						
									<td><?php echo $detail['metal_weight'].' g'; ?> </td>	
									<td><?php echo $detail['currency_symbol']." ".number_format($bal_amt,"2",".","") ; ?> </td>		
									<td><?php echo number_format($bal_wgt,"3",".","").' g'; ?> </td>																					
								</tr>	
							<?php  } ?>										
							</tbody>
						</table>
						</br>
					</div>	
				</div>
				<?php }  ?>
			</div>
		</div>
	</div>
</div>
<div id="otp_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header ">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel">Mobile Number Verification</h3>
			</div>
			<div class="modal-body">
				<p>Please enter the code sent to your mobile number</p>
				<div>
					<input type="hidden" id="metal_rate" value="<?php echo $account['customer']['metal_rate']['goldrate_22ct'];?>">
					<input type="hidden" id="scheme_acc_number" value="<?php echo $account['customer']['scheme_acc_number'];?>">
					  <input type="hidden" id="id_scheme_account" value="<?php echo $account['customer']['id_scheme_account'];?>">
					 <input type="hidden" id="id_branch" value="<?php echo $account['customer']['id_branch'];?>">
					<label style="display:inline; margin:5px" for="otp">Enter Code:</label>
					<input  style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required/>
					<a style="margin-right:1%;margin-left:1%;cursor: pointer;" id="resendOTP" >Resend OTP</a>
					<span id="OTPloader"><img src="<?php echo base_url()?>assets/img/loader.gif" ></span>
				</div>
				<div class="modal-footer">
					<input style="margin-left:1%" type="submit" value="Submit" id="submit" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />
				</div>
			</div>
		</div>
	</div>
</div>	
<div class="modal fade" id="confirm-subscribe">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>CONFIRM SUBSCRIPTION</h4>
			</div>
			<div class="modal-body">
				<p>Cashfree Subscription automates your monthly payments after the initial checkout is completed. You can unsubscribe from Auto-Debit anytime.</p>
				<p>Are you sure? You want to Subscribe cashfree Auto-Debit option for this Purchase plan?</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn join-button btn-default"  data-dismiss="modal">Cancel</a>
				<a href="#" class="btn join-button btn-success btn-confirm">Subscribe</a>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="confirm-unsubscribe">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>CONFIRM UNSUBSCRIBE</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure? You want to Unsubscribe cashfree Auto-Debit option for this Purchase plan?</p>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn join-button btn-default"  data-dismiss="modal">Cancel</a>
				<a href="#" class="btn join-button btn-danger btn-confirm">Unsubscribe</a>
			</div>
		</div>
	</div>
</div>
	
	
<!-- modal-->
<div class="modal fade" id="rateFixModal" tabindex="-1" role="dialog"  aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel">Metal Rate</h4>
				<input type="hidden" id="id_scheme_account" value="<?php echo $account['customer']['id_scheme_account'];?>">
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" id="ratefixByHistory" class="btn btn-default" data-dismiss="modal">Proceed</button>
			</div>
		</div><!-- /.modal-content-->
	</div><!-- /.modal-dialog -->
</div>
<br />
<br />
<br />