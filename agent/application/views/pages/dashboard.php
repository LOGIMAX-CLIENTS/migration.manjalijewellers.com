<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<?php 
$agent = $this->user_model->agent_details();
?>
<div class="main-container">
	<!-- main -->		  
	<div class="main">
	  <!-- main-inner --> 
		<div class="main-inner">
			<!-- container --> 
			<div class="container dashboard">
				<br/>
					<!-- alert -->
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<div align="center" class="theme-txt">
							<?php  if($agent['bank_account_number'] == '' || $agent['bank_name'] == '') { ?>
								 <marquee> <h4>Alert! Please update the bank details in profile page to proceed settlements.....</h4></marquee> 
							<?php } ?>
							<p style="font-size: 18px;">Welcome To <?php echo $header_data['company_name']?></p>
							<p style="font-size: 18px;">Your Current Balance is </p>
							<b id="tot_cash_point" style="font-size: 30px;"></b>
						</div>
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
					<!-- /alert -->  
					</div>
				</div>
				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-10 col-xs-12">
						<div class="my-stat">
							<ul class="nav nav-pills nav-pills1">
								<li class="active col-md-4 col-xs-12"><a href="#my_transactions" data-toggle="tab">MY TRANSACTION</a></li>
								<li class="col-md-4 col-xs-12"><a href="#my_referals" id="ref" data-toggle="tab">MY REFERAL LIST</a></li>
								<li class="col-md-4 col-xs-12"><a href="#my_settlement" id="settle" data-toggle="tab">MY SETTLEMENT</a></li>
							</ul>
						</div> 
						<div class="row agent_dashboard" style="">
							<div class="col-md-4 col-xs-5">
								<div class="form-group">
									<label></label>
									<input type="date" class="form-control" style="border: 1px solid #8c1f48;border-radius: 15px;" id="ranges_from" name="range_from" value=""  onfocus="this.type='date'" onblur="this.type='text'" data-date-format="yyyy-mm-dd">
									<p class="help-block"></p>
								</div> 	
							</div> 
							<div class="col-md-4 col-xs-5">
								<div class="form-group">
									<label for="classi" ></label>
									<input type="date" class="form-control"  style="border: 1px solid #8c1f48;border-radius: 15px;" id="ranges_to" name="range_to" value=""  onfocus="this.type='date'" onblur="this.type='text'"  data-date-format="yyyy-mm-dd">
									<p class="help-block"></p>                       	
								</div>
							</div> 
							<div class="col-sm-1 col-xs-2">
								<div class="form-group">
									<label for="classi" ></label>
									<button id="send_date" style="border: 1px solid #8c1f48;border-radius: 15px;" class="form-control"><i class="fa fa-search"></i></button>
									 <p class="help-block"></p>                       	
								</div>
							</div> 
							<div class="col-sm-3 col-xs-12">
								<div class="overlay" style="display:none">
									<i class="fa fa-refresh fa-spin" id="spin" style="line-height: 2.85;font-size: 25px;"></i>
								</div>							</div>
						</div>
						<div id="dashboard" align="center"></div>
						</br>
							<!-- <table class="bold" style="width:100%; border: 1px solid #d6d6d6; font-size: 15px;">
											  <tr>
												  <td>0</td>
												  <td>1</td>
												  <td>0%</td>
												  <td>₹ 29.13</td>
												 </tr>
												 <tr style="border: 1px solid #d6d6d6;">
												  <td rowspan="3">Referrals</td>
												  <td rowspan="3">Orders</td>
												  <td rowspan="3">Conversions</td>
												  <td rowspan="3">Earnings</td>
												 </tr>
							</table>--> 
							<!---/my transactions -->
						<div class="tab-content">
							<div class="tab-pane active" id="my_transactions">
								<div class="widget-header">MY TRANSACTION</div>
								<div class="widget-content">
									<div class="row">
									   <div>
											<div class="form-group">
												<div id="bill"></div>
											</div> 	
									   </div>
									</div>
								</div>
								 <div id="view_trans_data"></div>
							</div>
							<!---/my transactions -->
							 <!---/my referals -->
							<div class="tab-pane" id="my_referals">
								<div class="widget-header"> 
								  MY REFERAL LIST
								</div>
								<div class="widget-content">
									<div class="row">
										<div>
											<div class="form-group">
												 <div id="referals"></div>
											</div> 	
										</div>  
									</div>
								</div>
								<div id="view_ref_data"></div>
							</div>
							<!--/my referals -->
							<!---/my settlements -->
							<div class="tab-pane" id="my_settlement">
									 <!---/summary-->
								<div class="widget-header"> 
								SUMMARY
								</div>
								<div class="widget-content">
									<div class="row">
										<div>
											<div class="form-group">
												 <div id="settlement_summary"></div>
											</div> 	
									   </div>   
									</div>
								</div>
								<!---/summary-->
								<div class="widget-header"> 
								MY SETTLEMENT HISTORY
								</div>
								<div class="widget-content">
									<div class="row">
										<div>
											<div class="form-group">
												 <div id="settlements"></div>
											</div> 	
									   </div>   
									</div>
								</div>
							   <div id="view_settle_data"></div>
							</div>
							<!---/my settlements -->
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