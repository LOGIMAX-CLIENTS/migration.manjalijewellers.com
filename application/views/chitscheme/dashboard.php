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
						<div align="center"><legend class="head">DASHBOARD</legend></div>
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
					<div class="col-md-4">
						<div class="widget">
							<div class="widget-header"> 
							<!--<i class="icon-user"></i>-->
							<!--  <h3> Profile</h3>-->
							My Profile
							</div>
						<div class="widget-content">
							<div class="">
								<div class="col-md-12">
									<div class="row">
										<div>
											<div class="col-xs-4">
												<?php 
													if (@getimagesize(base_url().'admin/assets/img/customer/'.$customer['id_customer'].'/customer.jpg')) {
													echo '<img class="img-thumbnail" src="'.base_url().'admin/assets/img/customer/'.$customer['id_customer'].'/customer.jpg"  width="70px" height="70px" >';
													}
													else {
													echo '<img class="img-thumbnail" src="'.base_url().'admin/assets/img/default.png"  width="70px" height="70px" >';
													}
												?>
											</div>
										<div class="col-xs-8">
											<div class="row">
												<p class="text-center"><?php echo $customer['firstname'].' '.$customer['lastname'];?></p>
												<p class="text-center">	 
													<!--<div class="progress">
													<div class="bar bar-success" style="width: <?php echo $profile_stat;?>%;"><span class="text-center"><?php echo $profile_stat;?>%</span></div>
													</div>-->
													<div class="progress skill-bar">
														<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $profile_stat;?>" aria-valuemin="0" aria-valuemax="100" >
														<span class="skill"><i class="val"><?php echo $profile_stat;?>%</i></span>
														</div>
													</div><a href="<?php echo base_url().'index.php/user/register_update'?>">Profile Update</a>
												</p>
											</div>	
											</div>	
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="row">
										<div class="col-xs-6 bold">Member since</div>
										<div class="col-xs-6"><?php echo isset($customer['date_add'])?$customer['date_add']:'-'; ?></div>
									</div>	  
									<div class="row">
										<div class="col-xs-6 bold">Mobile</div>
										<div class="col-xs-6"><?php echo isset($customer['mobile'])?$customer['mobile']:'-'; ?></div>
									</div> 
									<div class="row">
										<div class="col-xs-6 bold">Purchase plan</div>
										<div class="col-xs-6"><?php echo isset($customer['accounts'])?$customer['accounts']:'-'; ?></div>
									</div>
									<div class="row">					      
										<div class="col-xs-6 bold">Profile Status</div>
										<div class="col-xs-6"><?php if($customer['profile_complete'] == 0) { ?> <span class="label label-danger">Incomplete</span>  <a href="<?php echo base_url() ?>index.php/user/register_update"><i title="Click to complete your profile" style="font-size:15px; vertical-align:middle" class="icon-edit"></i></a><?php } else { ?> <span class="label label-success">Completed</span> <?php } ?></div>
									</div>
								</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="overview">
											<div class="mainHeading">Last Transaction</div>
										</div>
										<?php if(isset($payHistory['lastPaid']) && $payHistory['lastPaid']!=''){ ?>
										<div class="row">
											<div class="col-xs-6">Purchase plan Name</div>
											<div class="col-xs-6"><?php echo isset($payHistory['lastPaid'][0]['scheme_name'])?$payHistory['lastPaid'][0]['scheme_name']:'-'; ?></div>
										</div>	
										<?php  if($payHistory['lastPaid'][0]['has_lucky_draw']==0 && $this->config->item('showGCodeInAcNo') == 1) { ?>
										<div class="row">
											<div class="col-xs-6">Group Code</div>
											<div class="col-xs-6"><?php echo isset($payHistory['lastPaid'][0]['code'])?($payHistory['lastPaid'][0]['code'] === '' ? "" : $payHistory['lastPaid'][0]['code']):'-'; ?></div>
										</div>
										<?php } ?>
										<div class="row">
											<div class="col-xs-6">A/c No</div>
											<div class="col-xs-6"><?php echo isset($payHistory['lastPaid'][0]['scheme_acc_number'])?($payHistory['lastPaid'][0]['scheme_acc_number'] === '' ? "" : ($payHistory['lastPaid'][0]['scheme_acc_number']==' Not Allocated') ? $this->config->item('default_acno_label'):$payHistory['lastPaid'][0]['scheme_acc_number']):'Not Allocated'; ?></div>
										</div>
										<div class="row">
											<div class="col-xs-6">Paid Date</div>
											<div class="col-xs-6"><?php echo isset($payHistory['lastPaid'][0]['date_payment'])?$payHistory['lastPaid'][0]['date_payment']:'-'; ?></div>
										</div>
										<div class="row">
											<div class="col-xs-6">Paid Mode</div>
											<div class="col-xs-6"><?php echo isset($payHistory['lastPaid'][0]['payment_mode'])?$payHistory['lastPaid'][0]['payment_mode']:'-'; ?></div>
										</div>
										<?php if(isset($payHistory['lastPaid'][0]['metal_weight']) && $payHistory['lastPaid'][0]['metal_weight'] > 0) { ?>
										<div class="row">
											<div class="col-xs-6">Weight ( gm )</div>
											<div class="col-xs-6"><?php echo isset($payHistory['lastPaid'][0]['metal_weight'])?$payHistory['lastPaid'][0]['metal_weight']:'-'; ?></div>
										</div>    	
										<?php } ?>
										<div class="row">
											<div class="col-xs-6">Amount (<?php echo $currency;?>) </div>
											<div class="col-xs-6"><?php echo isset($payHistory['lastPaid'][0]['payment_amount'])?$payHistory['lastPaid'][0]['payment_amount']:'-'; ?></div>
										</div> 	
										<?php $payment_status = isset($payHistory['lastPaid'][0]['payment_status'])?($payHistory['lastPaid'][0]['payment_status'] === 'Awaiting'?"<span class='label label-warning'>".$payHistory['lastPaid'][0]['payment_status']."</span> <i rel='tooltip' title='Your payment not yet realised, status will be updated after credited from bank.' class='icon-question-sign help-icon'></i>":($payHistory['lastPaid'][0]['payment_status'] === 'Failed'?"<span class='label label-danger'>".$payHistory['lastPaid'][0]['payment_status']."</span>":($payHistory['lastPaid'][0]['payment_status'] === 'Rejected'?"<span class='label label-danger'>".$payHistory['lastPaid'][0]['payment_status']."</span> <i rel='tooltip' title='May be your payment realisation got failed. Please contact administrator for details' class='icon-question-sign help-icon'></i>":"<span class='label label-success'>".$payHistory['lastPaid'][0]['payment_status']."</span>"))):'-'; ?>
										<div class="row">
											<div class="col-xs-6">Status</div>
											<div class="col-xs-6"><?php echo $payment_status ?></div>
										</div>
										<?php } else{?> 
										<div class="row">
											<div class="col-xs-12 text-center"  align="center">
												<div class="alert alert-danger" align="center">
													<strong>You have not made any payment.</strong>
												</div>
												<?php   if($schemes=='0')	{?>
												<a class="button btn  btn-block" href="<?php echo base_url('index.php/chitscheme/schemes?type=new');?>">Click to Join Purchase plan</a>
												<?php       }else{ ?>
												<a class="button btn  btn-block" href="<?php echo base_url('index.php/paymt');?>">Click to Pay</a>
												<?php       } ?>	
											</div>					      		
										</div>
										<?php } ?> 
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="widget">
							<div class="widget-header"> 
								<!--<i class="icon-bar-chart"></i><h3> </h3>-->
								My Dashboard
							</div>
							<div class="widget-content">
								<div class="my-stat">
									<div class="row">
										<div class="shortcuts" > 
											<a href="<?php  echo ($schemes=='0'?base_url('index.php/chitscheme'):base_url('index.php/chitscheme/scheme_report'));?>" class="shortcut">
												<i class="shortcut-icon icon-list-alt"></i>
												<span class="shortcut-label"> My Plan</span> 
												<span class="shortcut-label"><?php echo (isset($schemes)?$schemes:0);?></span>
											</a>   
											<a href="<?php echo base_url('index.php/paymt');?>" class="shortcut">
												<i class="shortcut-icon icon-th"></i>
												<span class="shortcut-label">Pay EMA</span> 
												<span class="shortcut-label"><?php echo (isset($dues)?$dues:0);?></span>
											</a> 
											<?php  if($allow_wallet== 1) { ?>
											<a href="<?php echo base_url('index.php/paymt/wallet_transaction');?>" class="shortcut">
												<i class="shortcut-icon icon-briefcase"></i>
												<span class="shortcut-label">Wallet</span> 
												<span class="shortcut-label"><?php echo (isset($wallet_balance)? $wallet_balance:0);?></span>
											</a>
											<?php } ?>
											<a href="<?php echo base_url('index.php/paymt/payment_history');?>" class="shortcut">
												<i class="shortcut-icon icon-tasks"></i>
												<span class="shortcut-label">Paid Amount</span> 
												<span class="shortcut-label"><?php echo (isset($payments)?$payments:0);?></span>
											</a>   
											<a href="javascript:;" class="shortcut">
												<i class="shortcut-icon icon-money"></i>
												<span class="shortcut-label">Total Paid</span> 
												<span class="shortcut-label"><?php echo (isset($total_amount)?$currency." ".$total_amount:0);?></span>
											</a> 
											<!--<a href="<?php echo base_url('index.php/paymt/pdc_report');?>" class="shortcut">
											<i class="shortcut-icon icon-time"></i>
											<span class="shortcut-label">PDC/ECS</span> 
											<span class="shortcut-label"><?php echo (isset($pdc)?$pdc:0);?></span>
											</a> --> 
											<?php if($regExistingReqOtp==1){?>
											<a href="<?php echo base_url('index.php/chitscheme/exisRegReq');?>" class="shortcut">
												<i class="shortcut-icon icon-list-alt"></i>
												<span class="shortcut-label">Existing Reg Request</span> 
												<span class="shortcut-label"><?php echo (isset($exisRegReq)?$exisRegReq:0);?></span>
											</a>  
											<?php }?>
											<?php if($showClosed == 1){ ?>
											<a href="<?php echo base_url('index.php/chitscheme/closed_accounts');?>" class="shortcut">
												<i class="shortcut-icon  icon-ok-sign"></i>
												<span class="shortcut-label">Closed Accounts</span> 
												<span class="shortcut-label"><?php echo (isset($closedAcc)?$closedAcc:0);?></span>
											</a>  
											<?php }
											if($this->config->item('DTHshow') == 1){?>
											<a href="<?php echo base_url('index.php/user/custDth');?>" class="shortcut">
												<i class="shortcut-icon  fa fa-calendar"></i>
												<span class="shortcut-label">Booked Appointments</span> 
												<span class="shortcut-label"><?php echo (isset($custDth)?$custDth:0);?></span>
											</a> 
											<?php } 
											if($compare_plan_img != NULL){?>
											<a href="<?php echo base_url('index.php/chitscheme/compare_plan');?>" class="shortcut">
												<i class="shortcut-icon  fa fa-calendar"></i>
												<span class="shortcut-label">Compare Plans</span> 
											</a>
											<?php } ?>
											<!-- <a href="<?php echo base_url('index.php/user/custComplaints');?>" class="shortcut">
											<i class="shortcut-icon icon-ticket"></i>
											<span class="shortcut-label">Tickets</span> 
											<span class="shortcut-label"><?php echo (isset($custComplaints)?$custComplaints:0);?></span>
											</a> -->
											<?php if($this->config->item('sbi_virtual_pay_url') != ''){?>
											<a href="<?php echo $this->config->item('sbi_virtual_pay_url');?>" class="shortcut">
												<i class="shortcut-label fa fa-credit-card" style="font-size: 5.20em;"></i>
												<span class="shortcut-label">Pay Quick</span> 
												<span class="shortcut-label"> </span> 
											</a> 
											<?php } ?>

											<?php if($showCoinEnq == 1){ ?>   
											<a href="<?php echo base_url('index.php/user/coin_enq_details');?>" class="shortcut">
												<i class="shortcut-icon  fa fa-envelope-o"></i>
												<span class="shortcut-label">Coin Enquiry</span> 
												<span class="shortcut-label"><?php echo (isset($custDth)?$custDth:0);?></span>
											</a> 
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						</div>    	  
					</div>
				</div>
				<!-- /container --> 
			</div>
			<!-- /main-inner --> 
		</div>
	<!-- /main -->		  
	</div>
</div><br /><br />