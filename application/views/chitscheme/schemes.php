<?php 
$scheme_group=$this->login_model->company_details();
$get_cusname=$this->scheme_modal->get_cusname();
?>
<link href="<?php echo base_url() ?>assets/css/pages/schemes.css" rel="stylesheet"/>
<style>
	.collapse{
		display:none;
	}
</style>
<div class="main-container">
	<!-- main -->
	<div class="main-container">		  
		<div class="main"  id="schemPayList">
			<!-- main-inner --> 
			<div class="main-inner">
				<!-- container --> 
				<div class="container">
					<!-- alert -->
					<div class="row">      
						<div class="col-md-12">   
							<div align="center"><legend class="head">PURCHASE PLAN</legend></div>
							<input type="text" hidden id="branch_set" name="branch_set" value="<?php echo $this->session->userdata('branch_settings'); ?>"/>
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
						
							<?php
							if($cusActive == 0) { ?>							 
								<div class="alert alert-danger" align="center">
								  <button type="button" class="close" data-dismiss="alert">&times;</button>
								  <strong>Your account is not yet activated. Please contact adminstrator.</strong>
								</div>
							<?php } else {?>
							<div class="alert_msg"></div>
							<div id="join_schemes">
								<div class="row">
									<!-- existing scheme table -->
									<?php if($reg_existing == 1){?>
									<div class="col-md-4">	 
									  <div class="widget">				
										<div class="widget-content">							
										  <div class="account-container " >
										  
											<p class="sub-head">REGISTER EXISTING PURCHASE PLAN </p>
											<p class="height"></p>
											<span><i>( Register your existing Purchase plan, if you already have saving Purchase plan account)</i></span> 
											<p class="height"></p>
											<div class="">
											<?php
												$attributes = array('autocomplete' => "off",'role'=>'form','id' => 'join_existing');
												echo form_open('chitscheme/join_existing',$attributes) ?>					     
												<div  style="padding-left: 10px" >
												
												<div class="row" align="center">
													<p class="sub-head">REGISTER BY ACCOUNT DETAILS </p>  
												</div>							        	
																					
												<div class="row paddingBottom10">
												   <div class="col-xs-5" ><b>Purchase plan Code</b></div>
													 <select id="secheme_select" class="form-control" style="width:42%;"  required="true"></select>

													 <input id="scheme_code" name="scheme_code" type="hidden" value="" required="true"/>
													<input id="id_scheme" name="id_scheme" type="hidden" value="" required="true"/>
													<input id="regExistingReqOtp" name="regExistingReqOtp" type="hidden" value="" required="true"/>
											   </div>
											   <?php if($scheme_group['has_lucky_draw']==1) {?>
											   
											   <div class="row paddingBottom10">
												 <img id="spinner" src="<?php echo base_url()?>assets/img/spinner.gif" style="display:none">
												   <div class="col-xs-5" ><b>Group Code</b></div>
													 <select id="group_select" class="form-control" style="width:42%;"  required="true"></select>
													 <input id="group_code" name="group_code" type="hidden" value="" required="true"/>
													<input id="id_scheme_group" name="id_scheme_group" type="hidden" value="" required="true"/>
													<!--<input id="regExistingReqOtp" name="regExistingReqOtp" type="hidden" value="" required="true"/>-->
											   </div>
										   <?php } ?>
												<div class="row paddingBottom10">
													<div class="col-xs-5" ><b>A/c No</b></div>
													<input type="email" class="col-xs-6 form-control"  id="scheme_acc_number" name="scheme_acc_number" placeholder="eg:FPL 47" style="width: 42%" required="true">					      					
												</div>									
												<div class="row paddingBottom10">
													<div class="col-xs-5" ><b>A/c Name</b></div>
													<input type="text" class="col-xs-6 form-control"  id="account_name" name="account_name" placeholder="eg: Name" style="width: 42%" required="true">	
												</div>
												<div class="row paddingBottom10"> 
													<div class="col-xs-5" id="pan" style="display:none;" ><b>PAN No.</b> <span id="panReq" style="color:red;">* <i rel="tooltip"  style="color:#000;" title="For given PAN, original should be produced at the time of Purchase.Copy of PAN Card should be submitted." class="icon-question-sign help-icon"></i></span></div> 
													<input type="text" class="col-xs-6 form-control"   id="exis_pan_no" name="exis_pan_no" required="true" placeholder="PAN Number" style="width: 42%;display:none;text-transform: uppercase;">
												</div>
												<?php if($this->session->userdata('branch_settings')==1){
													if(($this->session->userdata('is_branchwise_cus_reg')!=1)){?>	
													<div class="row paddingBottom10">
														<div class="col-xs-5" ><b>Branch</b></div>
														<select id="branch_select" class="form-control" style="width:42%;" ></select>
														<input id="id_branch" name="id_branch" type="hidden" value="" />	
													</div>
													 <?php }else{?>
														<input type="hidden" name="id_branch"  value="<?php echo$this->session->userdata('id_branch'); ?>" >
														<input type="hidden" name="is_branchwise_cus_reg" id="is_branchwise_cus_reg"  value="<?php echo$this->session->userdata('is_branchwise_cus_reg'); ?>" >
													<?php }?>
												<?php }?>
												</div>
												<?php echo form_close(); ?>		
											</div>				
											<p class="height"></p>
											<div class="control-group existing_control " align="center">
												<button class="button btn join-btn"  id="register_sub" type="submit" >Register</button> 								</div>						       
										  </div>							  
									   </div>
									 </div>
									</div>
									<?php }?>
															
									<!-- NEW SCHEME TABLE -->
									<div class="col-md-<?php echo ($reg_existing == 0? '12' : '8');?>"> 
										<div class="widget">			
										<!--<?php if($is_multi_commodity=='1'){		
											$idx = 0;
											foreach ($schCommodity as $c){
												echo "<button type='button' class='".($idx == 0 ? 'btn-warning':'')." metal_btm btn' id='metal_btm".$c['id_metal']."' value='".$c['id_metal']."'>".$c['metal']."</button>";
												$idx++;
											}
											} ?>-->
											<div class="widget-content">
												<p class="sub-head">JOIN NEW PURCHASE PLAN</p>
												<p class="height"></p>
												<span><i>( Join new Purchase plan, if you don't have saving Purchase plan account)</i></span>
												<p class="help-block"></p>
												<!-- Metal Tab
												<div class="row">
													<div class="col-md-12"  id="metal_filter"></div>
												</div>-->
												<!-- Branch Tab -->
												<div class="row">
													<div class="col-md-12"  id="branch_filter"></div>
												</div>
												<p class="help-block"></p>
												<p class="height"></p>
												<div class="tab-pane active" id="new_scheme">
													<img id="spinner" src="<?php echo base_url()?>assets/img/spinner.gif" style="display:none">
													<div id="schemeTable">
														<ul id="sch_clsfy_tabs" class="nav nav-pills nav-stacked col-md-<?php echo ($reg_existing == 0? '3' : '2');?>">
														<!--Content will be loaded from js for Classification Headers-->
														<input type="hidden" id="is_multi_commodity"  value="<?php echo $is_multi_commodity;?>">
														</ul>
														<div id="tab_sch_content" class="tab-content col-md-<?php echo ($reg_existing == 0? '9' : '10');?>">
															<div id="clsfy_tc_block"></div> <br/>
														</div>
													</div>
												</div>
											</div>
										<!--</NEW scheme table>-->
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>
				</div>
				<div class="overlayy" style="display:none;font-size: 20px; position: absolute;top: 0%; z-index: 60;width: 100%;height: 100%;left: 0%;background: rgba(255,255,255,0.7);">
					<i class="fa fa-refresh fa-spin" style="margin-left: 50%;margin-top: 40%;"></i>
				</div>
				<!-- /container --> 
			</div>
		</div>
	  <!-- /main-inner --> 
	</div>
</div>
<div class="modal fade" id="schemeJoin_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close clx" data-dismiss="modal">&times;</button>
				<h3 id="myModalLabel">JOIN PURCHASE PLAN</h3>
			</div>
			<!--customer name by default in account name while joining scheme hh -->
			<!--<input type="hidden" id="name" value="<?php echo ($this->session->userdata('acc_name')!='' ? $this->session->userdata('acc_name'):$this->session->userdata('cus_name'));?>">-->
			<?php if($scheme_group['cusName_edit']!=1) {?>
			<input type="hidden" id="name"  value="<?php echo $get_cusname['cus_name'];?>">
			<?php 
					//print_r($get_cusname);
					$attributes 		=	array('id' => 'schemeForm', 'name' => 'schemeForm');
					echo form_open_multipart('chitscheme/join_scheme',$attributes);  
			?>
			<?php }else{?>
			<?php 
				$attributes 		=	array('id' => 'schemeForm', 'name' => 'schemeForm');
				echo form_open_multipart('chitscheme/join_scheme',$attributes);  
			?>
			<?php }?>
			<div class="modal-body">
			</div>
			</form>
			<div class="modal-footer">
				<a href="#" class="btn join-button btn-info" id="confirm" >Join</a>
				<a href="#" class="btn join-button btn-danger" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>

<?php } ?>

<!-- /alert -->  
<div id="scheme_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">PURCHASE PLAN DETAILS</h3>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>

<div id="terms_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel">Purchase plan Terms & Conditions</h3>
			</div>
			<div class="modal-body">
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
			<?php 
				$attributes 		=	array('id' => 'regotpForm', 'name' => 'regotpForm');
				echo form_open_multipart('chitscheme/submit_schregOtp',$attributes);  
			?>
			<div class="modal-body">
				<p>Please enter the code sent to your mobile number</p>
				<div>
					<label style="display:inline; margin:5px" for="otp">Enter Code:</label>
					<input style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required />
					<a style="margin-right:1%;margin-left:1%" id="resendOTP" >Resend OTP</a>
					<span id="OTPloader"><img src="<?php echo base_url()?>assets/img/loader.gif" ></span>
				</div>
				<div class="modal-footer">
					<input style="margin-left:1%" type="submit" value="Register" id="otp_submit" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
	
<div id="otp_mob_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header ">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel">Mobile Number Verification</h3>
			</div>
			<?php 
			$attributes 		=	array('id' => 'regotpForm', 'name' => 'regotpForm');
			echo form_open_multipart('chitscheme/submit_schreg_bymobile_otp',$attributes);  
			?>
			<div class="modal-body">
				<p>Please enter the otp sent to your mobile number</p>
				<div>
					<label style="display:inline; margin:5px" for="otp">Enter Code:</label>
					<input style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required />
					<a style="margin-right:1%;margin-left:1%" id="resendOTP" >Resend OTP</a>
					<span id="OTPloader"><img src="<?php echo base_url()?>assets/img/loader.gif" ></span>
				</div>
				<div class="modal-footer">
					<input style="margin-left:1%" type="submit" value="Register" id="otp_submit" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />
				</div>
			</div>
		  </form>
		</div>
	</div>
</div>
<br />
<br />
<br />
   