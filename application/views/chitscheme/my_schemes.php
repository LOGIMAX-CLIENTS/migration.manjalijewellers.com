<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<style>
.card {
  box-shadow: 0 5px 23px -14px rgb(0 0 0 / 25%);
}
.card_content {
  background: none;
}
.collapse{
	display:none;
}
</style>
<div class="main-container">
	<!-- main -->		  
	<div class="main"  id="schemPayList">
		<!-- main-inner --> 
		<div class="main-inner">
			<!-- container --> 
			<div class="container">
				<div class="row">
				<div class="col-md-12">
					<div align="center"><legend class="head">MY PURCHASE PLANS</legend></div>	
					<?php
					if($this->session->flashdata('successMsg')) { ?>
					<div class="alert alert-success" align="center">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
					</div>      
					<?php } else if($this->session->flashdata('errMsg')) { ?>							 
					<div class="alert alert-danger" align="center">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
					</div>
					<?php } ?>
					<div class="widget" >
						<div class="widget-content"> 
							<div class="row">
								<div class="col-md-9">	
									<?php 
									$idx = 0;
									/*foreach ($schCommodity as $c){
									echo "<button type='button' class='".($idx == 0 ? 'theme-btn-bg':'')." metal_btn btn btn-sm' id='metal_btn".$c['id_metal']."' value='".$c['id_metal']."'>".$c['metal']."</button>";
									$idx++;
									}*/
									foreach ($schBranches as $c){
									echo "<button type='button' class='".($idx == 0 ? 'theme-btn-bg':'')." branch_btn btn btn-sm' id='branch_btn".$c['id_branch']."' value='".$c['id_branch']."'>".$c['name']."</button>";
									$idx++;
									}
									?>
								</div>
								<div class="col-md-3">	
									<a href="<?php echo base_url('index.php/chitscheme/exisRegReq'); ?>" class="btn btn-mini btn-default theme-txt" data-toggle="modal">Existing Reg Requests</a>
								</div>
							</div>
							<br/>
							<br/> 	
							<div class="row" >
								<div class="col-md-12" > 
									<?php 
									/*foreach($schCommodity as $com){ 
									foreach($content['schemes'] as $sch_ac){  
										if($com['id_metal'] == $sch_ac['id_metal']){
											$com['cus_sch_ac'] = $com['cus_sch_ac']+1;
										}
									}
									if($com['cus_sch_ac'] == 0){
										$display = $schCommodity[0]['id_metal'] == $com['id_metal'] ?'revert':'none';
										echo "<p class='theme-txt sch_card sch_ac_".$com['id_metal']."' style='display:".$display."'>You don't have any purchase plan in ".$com['metal']."....</p>";
									}
									}*/
									foreach($schBranches as $brn){ 
									foreach($content['schemes'] as $sch_ac){  
										if($brn['id_branch'] == $sch_ac['id_branch']){
											$brn['cus_sch_ac'] = $brn['cus_sch_ac']+1;
										}
									}
									if($brn['cus_sch_ac'] == 0){
										$display = $schBranches[0]['id_branch'] == $brn['id_branch'] ?'revert':'none';
										echo "<p class='theme-txt sch_card sch_ac_".$brn['id_branch']."' style='display:".$display."'>You don't have any purchase plan in ".$brn['name']."....</p>";
									}
									}
									?>
									<div class="cards">
										<?php foreach($content['schemes'] as $ac){?>
										<div class="col-md-12 col-sm-12 col-xs-12 sch_card sch_ac_<?php echo $ac['id_branch']?>" style="display:<?php echo  $content['branch_settings'] == 1 && $content['is_branchwise_cus_reg'] == 0 ? ($schBranches[0]['id_branch'] == $ac['id_branch'] ?'revert':'none') : 'revert'?> ">
										  <div class="card">
												<div class="card_content">
													<h2 class="card_title theme-txt">
														<?php 
														echo $ac['scheme_acc_number'] ;
														if($ac['is_multi_commodity'] == 1){
															echo "&nbsp;&nbsp;<small><span class='badge bg-".($ac['metal']=='Gold'?'yellow':'silver')."'>".$ac['metal']."</span></small>";
														}
														echo "<span class='hidden-xs'>";
														echo '<a href="'.base_url().'index.php/chitscheme/scheme_account_report/'.$ac['id_scheme_account'].'" class="btn btn-info btn-xs pull-right" title="View Plan Details" rel="tooltip"><i class="fa fa-eye"></i></a>';
														echo $ac['paid_installments']>0 ?'': '<a href="#confirm-delete" data-href="'.base_url().'index.php/chitscheme/delete_account/'.$ac['id_scheme_account'].'" class="btn btn-xs btn-del btn-danger open-modal pull-right" data-toggle="modal" title="Delete Plan" rel="tooltip"><i class="fa fa-trash"></i></a>';
														if($ac['auto_debit_plan_type'] == 1){
															if(($ac['auto_debit_status'] == 0 || $ac['auto_debit_status'] == 5)&& $ac['paid_installments'] < $ac['total_installments']){
																echo '<a href="#confirm-subscribe" data-href="'.base_url().'index.php/chitscheme/cf_subscription/1/'.$ac['id_scheme_account'].'" class="btn btn-xs btn-success open-modal cf_ab_subscribe pull-right" title="Cashfree Subscription automates your monthly payments after the initial checkout is completed." rel="tooltip" data-toggle="modal">Subscribe</a>';
															}
															else if($ac['auto_debit_status'] == 1){
																echo '<a class="btn btn-xs btn-success pull-right" href="'.$ac['auth_link'].'" title="Subscription has been created and is ready to be authorized.Click Authorize to complete subscription." rel="tooltip">Authorize</a>';
															}
															else if($ac['auto_debit_status'] == 3){
																echo '<a href="#confirm-unsubscribe" data-href="'.base_url().'index.php/chitscheme/cf_subscription/2/'.$ac['id_scheme_account'].'" class="btn btn-xs btn-danger open-modal cf_ab_unsubscribe pull-right" title="You can cancel Auto-Debit, by clicking Unsubscribe." rel="tooltip" data-toggle="modal">Unsubscribe</a>';
															}
															else if($ac['auto_debit_status'] == 4){
																echo '<a href="#confirm-retryPay" data-href="'.base_url().'index.php/cf_autodebit/cf_retry/1/'.$ac['id_scheme_account'].'" class="btn btn-xs btn-warning open-modal cf_ab_retryPay pull-right" title="Retry failed payment if bank account issues rectified." rel="tooltip" data-toggle="modal">Retry Payment</a>';
															}
														}  
														echo "</span>";
														?>
													</h2>
													<div class="row card_text" >
														<div class="col-md-2 col-sm-4 col-xs-4" >
															<small style="color:#a59e9e;">Branch:</small><br/><?php echo $ac['branch_name'] ?>
														</div>
														<div class="col-md-2 col-sm-4 col-xs-4" >
															<small style="color:#a59e9e;">A/C Name:</small><br/> <?php echo $ac['account_name'] ?>
														</div>
														<div class="col-md-2 col-sm-4 col-xs-4" >
															<small style="color:#a59e9e;">Payable:</small><br/><?php echo $ac['payable'] ?>
														</div> 
														<span class="hidden-xs">
															<div class="col-md-2 col-sm-2 col-xs-3">
																<small style="color:#a59e9e;">Joined On:</small><br/> <?php echo $ac['start_date'] ?>
															</div>
															<div class="col-md-4 col-sm-4 col-xs-4">
																<small style="color:#a59e9e;">Total Paid:</small><br/>
																<?php 
																echo $ac['one_time_premium']==1?'<span class="badge bg-grey">'.$ac['total_installments'].'</span>':'<span class="badge bg-grey">'.$ac['paid_installments'].'/'.$ac['total_installments'].'</span>';
																// Amount
																if($ac['total_paid_amount'] > 0){
																	if($ac['one_time_premium']==1 && $ac['is_enquiry']==1){		        						
																		echo	"&nbsp;&nbsp;".($ac['scheme_type']=='Flexible' || $ac['scheme_type']=='Amount to Weight' ? $ac['currency_symbol']. ' ' .number_format($ac['payable'],'2','.','') :  $ac['payable'] .'g/month');		        							
																				 }
																	else if($ac['scheme_type']=='Flexible'){ 		        				
																		echo "&nbsp;&nbsp;".($ac['currency_symbol']. ' ' .$ac['total_paid_amount'] - $ac['paid_gst']);
																	}else{
																		 echo "&nbsp;&nbsp;".($ac['currency_symbol']. ' ' .$ac['total_paid_amount']);
																	}
																}
																// Weight
																if($ac['total_paid_weight']>0){ 
																	echo "&nbsp;&nbsp;  |  &nbsp;&nbsp;".$ac['total_paid_weight'].' g';
																}
																?>
															</div>
														</span>
														<!--Mobile View-->
														<span class="visible-xs"> 
															<div class="col-md-12 col-sm-12 col-xs-12">
																<div class="col-md-6 col-sm-6 col-xs-6">
																	<?php 
																	echo '<a href="'.base_url().'index.php/chitscheme/scheme_account_report/'.$ac['id_scheme_account'].'" class="btn btn-info btn-xs" title="View Plan Details" rel="tooltip"><i class="fa fa-eye"></i></a>'; 
																	echo "&nbsp;&nbsp;".$ac['paid_installments']>0 ?'': '<a href="#confirm-delete" data-href="'.base_url().'index.php/chitscheme/delete_account/'.$ac['id_scheme_account'].'" class="btn btn-xs btn-del btn-danger" data-toggle="modal" title="Delete Plan." rel="tooltip"><i class="fa fa-trash"></i></a>';
																	?>
																	<button class="btn btn-xs" data-toggle="collapse" data-target=".collapse_<?php echo $ac['id_scheme_account'] ?>" value="<?php echo $ac['id_scheme_account'] ?>"><i class="fa fa-caret-down"></i></button>
																</div>	
																<?php if($ac['auto_debit_plan_type'] == 1 && $ac['auto_debit_status'] == 0 && $ac['paid_installments'] < $ac['total_installments']){ ?>
																<div class="col-md-4 col-sm-4 col-xs-4">
																	<a href="#confirm-subscribe" data-href="<?php echo base_url()?>'index.php/chitscheme/cf_subscription/1/'<?php echo $ac['id_scheme_account']?>'" class="btn btn-xs btn-success cf_ab_subscribe" title="Cashfree Subscription automates your monthly payments after the initial checkout is completed." rel="tooltip" data-toggle="modal">Subscribe</a>
																</div>	
																<?php } ?>
																<?php if($ac['auto_debit_plan_type'] == 1 && $ac['auto_debit_status'] == 3){ ?>
																<div class="col-md-4 col-sm-4 col-xs-4">
																	<a href="#confirm-unsubscribe" data-href="<?php echo base_url()?>'index.php/chitscheme/cf_subscription/2/'<?php echo $ac['id_scheme_account']?>'" class="btn btn-xs btn-danger cf_ab_unsubscribe" title="Unsubscribe Cashfree Auto-Debit." rel="tooltip" data-toggle="modal">Unsubscribe></a>
																</div>	
																<?php } ?>	
																<?php if($ac['auto_debit_status'] == 4){ ?>
																<div class="col-md-4 col-sm-4 col-xs-4">
																	echo '<a href="#confirm-retryPay" data-href="'.base_url().'index.php/cf_autodebit/cf_retry/1/'.$ac['id_scheme_account'].'" class="btn btn-xs btn-warning open-modal cf_ab_retryPay pull-right" title="Retry failed payment if bank account issues rectified." rel="tooltip" data-toggle="modal">Retry Payment</a>';
																</div>
																<?php } ?>
															</div>	
															<div class="col-md-2 col-sm-4 col-xs-4 collapse collapse_<?php echo $ac['id_scheme_account'] ?>" style='margin-left: 15px;'>
																<small style="color:#a59e9e;">Total Paid:</small><br/>
																<?php 
																echo $ac['one_time_premium']==1?'<span class="badge bg-grey">'.$ac['total_installments'].'</span>':'<span class="badge bg-grey">'.$ac['paid_installments'].'/'.$ac['total_installments'].'</span>';
																// Amount
																if($ac['total_paid_amount'] > 0){
																	if($ac['one_time_premium']==1 && $ac['is_enquiry']==1){		        						
																		echo	"&nbsp;&nbsp;".($ac['scheme_type']=='Flexible' || $ac['scheme_type']=='Amount to Weight' ? $ac['currency_symbol']. ' ' .number_format($ac['payable'],'2','.','') :  $ac['payable'] .'g/month');		        							
																				 }
																	else if($ac['scheme_type']=='Flexible'){ 		        				
																		echo "&nbsp;&nbsp;".($ac['currency_symbol']. ' ' .$ac['total_paid_amount'] - $ac['paid_gst']);
																	}else{
																		 echo "&nbsp;&nbsp;".($ac['currency_symbol']. ' ' .$ac['total_paid_amount']);
																	}
																}
																// Weight
																if($ac['total_paid_weight']>0){ 
																	echo "&nbsp;&nbsp;  |  &nbsp;&nbsp;".$ac['total_paid_weight'].' g';
																}
																?>
															</div>
															<div class="col-md-2 col-sm-4 col-xs-4 collapse collapse_<?php echo $ac['id_scheme_account'] ?>" >
																<small style="color:#a59e9e;">Joined On:</small><br/> <?php echo $ac['start_date'] ?>
															</div>										
														</span> 
													</div> 
												</div>
											</div>
										</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<br/>
					<br/>
					<br/>
					<div class="row" style="display: none" >
					<div class="schemeTable col-md-12" >
					<div class="table-responsive">
					<table  id="myschemes_list"  class="table table-bordered table-striped table-responsive display">
					<thead>
					<tr >
					<th width="5%">S.No</th>
					<?php if($this->session->userdata('branch_settings')==1){?>
					<th width="10%">Branch</th>
					<?php } ?> 
					<th width="11%">A/c Name</th>
					<th width="11%">A/c No.</th>
					<th width="10%">Joined On</th>
					<th width="13%"  style="text-align:center">Payable</th>
					<th width="12%">Paid Installments</th>
					<th width="13%">Total Paid Amount </th>
					<th width="13%">Total Weight in gms </th>
					<th>Rate Fixed Through</th>   <!--Rate Fixed Flag show in user side hh -->
					<th width="7%">Status</th>
					<?php if($content['auto_debit'] == 1){?>
					<th width="7%">AutoDebit</th>
					<?php } ?>
					<th width="12%">View Details</th>
					</tr>
					</thead>
					<tbody>
					</tbody> 
					</table>
					</div>			 
					</div>
					</div>	
				</div>	
				</div>
				<!-- /container --> 
				</div>
				</div>
				<!-- /main-inner --> 
				</div>
				<!-- /main -->	
				<div class="modal fade" id="confirm-delete">
				<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>CONFIRM DELETE</h4>
				</div>
				<div class="modal-body">
				<p>Are you sure? You want to delete this Purchase plan?</p>
				</div>
				<div class="modal-footer">
				<a href="#" class="btn join-button btn-default"  data-dismiss="modal">Cancel</a>
				<a href="#" class="btn join-button btn-danger btn-confirm">Delete</a>
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
				<div class="modal fade" id="confirm-retryPay">
				<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>RETRY PAYMENT</h4>
				</div>
				<div class="modal-body">
				<p>Are you sure? You want to retry payment?</p>
				</div>
				<div class="modal-footer">
				<a href="#" class="btn join-button btn-default"  data-dismiss="modal">Cancel</a>
				<a href="#" class="btn join-button btn-danger btn-confirm">Retry Payment</a>
				</div>
			</div>
		</div>
	</div>
</div>
<br />
<br />
<br />