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
			<div align="center"><legend class="head">MY TRANSACTIONS</legend></div>	
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
								foreach ($schBranches as $c){
									echo "<button type='button' class='".($idx == 0 ? 'theme-btn-bg':'')." branch_btn btn btn-sm' id='branch_btn".$c['id_branch']."' value='".$c['id_branch']."'>".$c['name']."</button>";
									$idx++;
								}
								?>
							</div>
							
						</div>
						<br/>
						<br/> 	
						<div class="row" >
							<div class="col-md-12" > 
							  	<?php 
							  	
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
				
		</div>	
    </div>
    <!-- /container --> 
  </div>
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->	
	
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