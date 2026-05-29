<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
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
						<div align="center"><legend class="head">REPORT</legend></div>
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
				<div class="row">
					<div class="col-md-12">
						<div class="widget">
							<div class="widget-header"> 
							<!--<i class="icon-list-alt"></i>-->
							<!--  <h3> Scheme Account Details</h3>-->
							Scheme Account Details
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
												echo ($account['customer']['scheme_ac']!=''?$account['customer']['scheme_ac']:"Not scheme_ac");
												?>
												</td>
											</div>
											<div class="col-md-2" id="report-details"><b>Payable</b></div>
											<div class="col-md-2" id="report-details"><?php echo ($account['customer']['scheme_type'] == 'Amount' || $account['customer']['scheme_type'] == 'Amount to Weight'?$account['customer']['currency_symbol']." ".$account['customer']['payable']: "max ".$account['customer']['max_weight']." g/month"); ?></div>
											<div class="col-md-2" id="report-details"><b>A/C Name</b></div>
											<div class="col-md-2" id="report-details"><?php echo ucfirst($account['customer']['account_name']); ?></div>
											<div class="col-md-2" id="report-details"><b>Scheme Type</b></div>
											<div class="col-md-2" id="report-details"><?php echo $account['customer']['scheme_type']; ?></div>
											<div class="col-md-2" id="report-details"><b>Paid Installments</b></div>
											<div class="col-md-2" id="report-details"><?php echo '<span class="badge bg-green">'.$account['customer']['paid_installments'].'/'.$account['customer']['total_installments'].'</span>'; ?></div> 
											<div class="col-md-2" id="report-details"><b>Start Date</b></div>
											<div class="col-md-2" id="report-details"><?php echo $account['customer']['start_date']; ?></div>
											<?php 	if($this->session->userdata('branch_settings')==1){?>
											<div class="col-md-2" id="report-details"><b>Branch</b></div>
											<div class="col-md-2" id="report-details"><?php echo $account['customer']['branch_name']; ?></div>
											<?php } ?>
											<div class="col-md-2" id="report-details"><b>Address</b></div>
											<div class="col-md-2" id="report-details"><?php echo $account['customer']['address1']; ?> <br/>
											<?php echo $account['customer']['address2']; ?><br/>
											<?php echo $account['customer']['address3']; ?><br/>
											</div>
											<?php }else{?>
											<div class="col-md-2" id="report-details"><b>Name</b></div>
											<div class="col-md-2" id="report-details"><?php echo  $account['customer']['name'];?></div>
											<div class="col-md-2" id="report-details"><b>Amount</b></div>
											<div class="col-md-2" id="report-details"><?php echo  $account['customer']['firstPayment_amt'];?></div>
											<div class="col-md-2" id="report-details"><b>Maturity Date</b></div>
											<div class="col-md-2" id="report-details"><?php echo  $account['customer']['maturity_date'];?></div>
											<div class="col-md-2" id="report-details"><b>Start Date</b></div>
											<div class="col-md-2" id="report-details"><?php echo  $account['customer']['start_date'];?></div>
											<div class="col-md-2" id="report-details"><b>Rate Fixed</b></div>
											<div class="col-md-2" id="report-details"><?php echo ($account['customer']['fixed_metal_rate']!='' ? 'YES' :'NO');?></div>
											<div class="col-md-2" id="report-details"><b>Fixed Rate</b></div>
											<div class="col-md-2" id="report-details"><?php echo ($account['customer']['fixed_metal_rate']!='' ? $account['customer']['fixed_metal_rate'] :'-');?></div>
											<div class="col-md-2"><button id="fix_rate">Fix Rate</button></div>
											<?php }?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>	
				<?php	$i =0;
				if ($account['customer']['type']=='0'){ ?>	
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-bordered table-striped ">
								<thead>
									<tr class="success">
										<th style="width:5%">Installment No</th>
										<th>Date</th>
										<?php 	if($this->session->userdata('branch_settings')==1){?>
										<!--	<th width="10%">Paid At</th>-->
										<?php } ?>
										<th>Mode</th>
										<!--	<th>Charge </th>	-->										
										<th>Scheme payment </th>											
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
										<td><?php echo ++$i;?></td>
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
						</div>	
					</div>	
				</div>
				<?php }?>
				<?php if ($account['customer']['type']=='1' || $account['customer']['type']=='2' || $account['customer']['type']=='3'  ){ ?>	
				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered table-striped table-responsive display">
							<thead>
								<tr class="info">
									<th style="width:5%">Installment No</th>
									<th>Date</th>
									<?php 	if($this->session->userdata('branch_settings')==1){?>
									<!--	<th>Paid At</th>-->
									<?php } ?>
									<th>Mode</th>
									<th>Rate</th>
									<!--<th>Charge</th>	-->												
									<th>Scheme Payment </th>											
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
									<td><?php echo ++$i;?></td>
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