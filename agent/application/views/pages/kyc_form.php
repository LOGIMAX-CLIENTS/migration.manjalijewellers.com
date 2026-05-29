<?php
$kyc_status = $this->session->userdata('kyc_status');
$kyc_count = $this->session->userdata('kyc_count');
?>
<link href="<?php echo base_url() ?>assets/css/pages/payment.css" rel="stylesheet">

<div class="main-container">
	<div class="main-container">
		<!-- main -->		  
		<div class="main" >
		  <!-- main-inner --> 
			<div class="main-inner">
			 <!-- container --> 
				<div class="row">
				 </br>
					<?php  if($kyc_status != 1 && $kyc_count < 3) { ?>
						<h4 style="text-align:center;">Alert! Please update all the kyc details.....</h4>
					<?php } ?>
						<div align="center"><legend class="head" style="margin-left: 16px;">KYC Form</legend></div>
					<!--<?php echo "<pre>" ; print_r($kyc);?>-->
			  <!-- alert -->
					<div class="container">
						<div class="widget-content">
							<?php ?>
							<div id="success" value=''>									
								<div class=""  align="center">
									<button type="button" class="close" data-dismiss="alert">&times;</button>
									<strong id="suc_mgs"> </strong>
								</div> 
							</div>
								<!--	<div id="failed" value=''>
										<div class="alert alert-danger" align="center">
										<div class="alert alert-success"  align="center">
										<button type="button" class="close" data-dismiss="alert">&times;</button>
										<strong id="err_mgs"> </strong>
										</div> 
									</div>-->
							<div class="col-md-12 col-xs-12 kyc_form1">
								<div class="tab-pane active">					
									<ul id="tabs" class="nav nav-pills nav-stacked tabs2 col-md-2 col-xs-12">
										<li class="active" id="1" value="1"><a href="#tab_bank" data-toggle="pill">Bank Account Details</a></li>
										<li class="" id="2" value="2"><a href="#tab_pan" data-toggle="pill">PAN Card</a></li>
										<li class="" id="3" value="3"><a href="#tab_aadhar" data-toggle="pill">Aadhaar Card</a></li>
									<!--	<li class="" id="4" value="4"><a href="#tab_dl" data-toggle="pill">Driving Licence</a></li> -->
									</ul>						
									<div id="tab_kyc_content" class="tab-content col-md-10 col-xs-12 kyc_form">
										<div class="tab-pane overflow active" id="tab_bank">
											<h4 class="page-header">Bank Account <span color="red" style="float:right;    margin-right:70px;" id="b_txt" > <span id="bank_rmsat"> Status  <span id="bank_clrrm" > : <?php echo $kyc['bank']['status']; ?> </span> </span></span></h4>
											<input type="hidden" id="bank_color" value="<?php echo $kyc['bank']['status'];?>" >	
												<?php 
													$attributes = array('autocomplete' => "off",'role'=>'form');
													echo form_open( 'user/kyc_details', $attributes);
											   ?> 
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>Account Number<span class="error"> * </span> 		</b></label>
												<div class="col-md-6 col-xs-7 kyc_form">
													<input type="number" id="bank_acc_no" name="bank[bank_acc_no]" value="<?php echo set_value('bank[bank_acc_no]',isset($kyc['bank']['number'])?$kyc['bank']['number']:NULL); ?>" placeholder="Account Number" class="form-control" required />
												</div> 	
											</div><!---->
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">					
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>Confirm Account Number<span class="error"> * </span> 		</b></label>
												<div class="col-md-6 col-xs-7 kyc_form">
													<input type="number" id="con_acc_no" name="bank[con_acc_no]" value="<?php echo set_value('bank[con_acc_no]',isset($kyc['bank']['number'])?$kyc['bank']['number']:NULL); ?>" placeholder="Confirm Account Number" class="form-control" required />
												</div> 		
											</div><!---->								
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>IFSC Code<span class="error"> * </span> 		</b></label>
												<div class="col-md-6 col-xs-7 kyc_form">
													<input type="text" id="ifsc" name="bank[ifsc]" value="<?php echo set_value('bank[ifsc]',isset($kyc['bank']['bank_ifsc']) ? $kyc['bank']['bank_ifsc'] :NULL); ?>" placeholder="IFSC Code" style='text-transform:uppercase' class="form-control" required />
												</div> 	
											</div><!---->
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>A/c Holder Name<span class="error"> * </span> 		</b></label>
												<div class="col-md-6 col-xs-7 kyc_form">
													<input type="text" id="bank_name" name="bank[name]" value="<?php echo set_value('bank[name]',$kyc['bank']['name']); ?>" placeholder="A/c Holder Name" class="form-control" required />
												</div> 	
											</div> 
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>Bank Name<span class="error"> * </span> 		</b></label>
												<div class="col-md-6 col-xs-7 kyc_form">
													<input type="text" id="bank_branch" name="bank[bank_branch]" value="<?php echo set_value('bank[bank_branch]',$kyc['bank']['bank_branch']); ?>" placeholder="Bank Name" class="form-control" required />
												</div> 		
											</div>
											 <?php if($kyc['bank']['status']=='Pending'|| $kyc['bank']['status']=='Rejected' ){ ?> 
											<div class="col-md-12 col-xs-12 kyc_form" align="center">
												<input type="checkbox" name="bnk_chk" id="bnk_chk" required/> I agree to submit Bank Account for KYC verification. <br/> <br/>
												<input type="button" value="Submit" id="bank_btn" name="bank" onclick="submit_kyc('bank','1','<?php echo ($kyc['bank']['type']);?>')" class="button btn btn-primary btn-large"/>
												<input type="hidden"  value="1">
											</div>
											<?php } ?>
											<?php echo form_close();  ?>
										</div>							
										<div class="tab-pane overflow" id="tab_pan">
											<h4 class="page-header">PAN Card Details <span  style="float:right; margin-right:70px;" id="p_txt"> <span id="pan_rmsat" >  Status   <span id="pan_clrrm" > : <?php echo $kyc['pan']['status'];?></span></span></span></h4>
											<input type="hidden" id="pan_color" value="<?php echo $kyc['pan']['status'];?>" >
											<?php /* 
														$attributes = array('autocomplete' => "off",'role'=>'form');
															 echo form_open( 'user/kyc_details', $attributes);*/									  
													   ?> 
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>PAN Number<span class="error"> * </span> 		</b></label>
													<div class="col-md-6 col-xs-7 kyc_form">
														<input type="text" id="pan_no" name="pan[pan_no]" value="<?php echo set_value('pan[pan_no]',isset($kyc['pan']['number']) ? $kyc['pan']['number'] : NULL); ?>" placeholder="10 Digit PAN number" style='text-transform:uppercase' class="form-control" required />
													</div> 		
											</div><!---->
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>PAN Card Holder Name<span class="error"> * </span> 		</b></label>
													<div class="col-md-6 col-xs-7 kyc_form">
														<input type="text" id="pan_card_name" name="pan[pan_card_name]" value="<?php echo set_value('pan[pan_card_name]',$kyc['pan']['name']); ?>" placeholder="Card Holder Name" class="form-control" required />
													</div> 		
											</div><!---->
											<?php if($kyc['pan']['status']=='Pending' || $kyc['pan']['status']=='Rejected'  ){ ?>
											<div class="col-md-12 col-xs-12 kyc_form" align="center">
												<input type="checkbox" name="pan_chk" id="pan_chk" required/> I agree to submit PAN Card details for KYC verification. <br/> <br/>
												<input type="button" value="Submit" onclick="submit_kyc('pan','2','<?php echo ($kyc['pan']['type']);?>')" id="pan_btn" class="button btn btn-primary btn-large"/>
												<input type="hidden" id="submit_pan" value="2">
											</div>
											<?php } ?>
											<?php echo form_close();  ?>
										</div>
										<div class="tab-pane overflow" id="tab_aadhar">
											<h4 class="page-header">Aadhaar Card Details <span  style="float:right; margin-right:70px;"id="a_txt"><span id="a_rmsat" > Status   <span id="aadhar_clrrm" > : <?php echo  $kyc['aadhar']['status']; ?> </span>  </span> </span></h4>
											<input type="hidden" id="aadhar_color" value="<?php echo $kyc['aadhar']['status'];?>" >
											<?php 
												$attributes = array('autocomplete' => "off", 'id'=>"adhar_form" , 'role'=>'form');
												 echo form_open( 'user/kyc_details', $attributes);
											?> 
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>Aadhaar Number<span class="error"> * </span> 		</b></label>
													<div class="col-md-6 col-xs-7 kyc_form">
														<input type="text" id="aadhar_number" name="aadhar_number" value="<?php echo set_value('aadhar[aadhar_number]',isset($kyc['aadhar']['number'])?$kyc['aadhar']['number']:NULL ); ?>" maxlength="12" placeholder="12 Digit UID Number" class="form-control"  required/>
													</div> 		
											</div><!---->
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15"> 
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>Aadhaar Holder Name<span class="error"> * </span> 		</b></label>
													<div class="col-md-6 col-xs-7 kyc_form">
														<input type="text" id="aadhar_cardname" name="aadhar_cardname" value="<?php echo set_value('aadhar[aadhar_cardname]',isset($kyc['aadhar']['name'])? $kyc['aadhar']['name'] : NULL); ?>" placeholder="Card Holder Name" class="form-control"  required/>
													</div> 		
											</div><!---->
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>Date of Birth<span class="error"> * </span> 		</b></label>
													<div class="col-md-6 col-xs-7 kyc_form">
														<input type="date" id="dob" name="dob" value="<?php echo set_value('aadhar[dob]',isset($kyc['aadhar']['dob'])?$kyc['aadhar']['dob']:NULL); ?>" placeholder="D.O.B" class="form-control"  required/>
													</div> 		
											</div>
											<?php if($kyc['aadhar']['status'] != 'Verified') { ?>
											<!--<i class="icon-info" title="Download Aadhaar PDF File from uidai website " rel="tooltip"></i>-->
										<!--	<p>Get Aadhaar PDF File from <a href="https://eaadhaar.uidai.gov.in/#/" targer="_blank">uidai</a> website and upload pdf along with pdf password to verify your Aadhaar details.</p><br/>
											<div class="col-md-12 row marginBottom15">	
												<label class="col-md-6" for="firstname"> <b>Upload Aadhaar PDF<span class="error"> * </span> 		</b></label>
													<div class="col-md-3">
															<input type="file" id="pdf" name="adhar_file">
															<input type="hidden" id="file" >
													</div> 	 
											</div>
											<div class="col-md-12 row marginBottom15">											
												<label class="col-md-6" for="firstname"> <b>PDF Password<span class="error"> * </span> 		</b></label>
													<div class="col-md-6">
														<input type="text" id="aadhar_password" name="aadhar_password" value="" placeholder="PDF Password" class="form-control" required />
													</div> 	
											</div>  -->
											<?php } ?>  
											<!--<div class="col-md-2" style="margin-left: 300px;" >-->
													<?php if($kyc['aadhar']['status']=='Pending' || $kyc['aadhar']['status']=='Rejected'){ ?>
											<div class="col-md-12 col-xs-12 kyc_form" align="center">
											  <!--   <span>Note : This will support only masked aadhar. Please download the masked aather in download page</span> <br/> <br/> -->
												<input type="checkbox" name="adhar_chk"  id="adhar_chk" required/> I agree to submit Aadhaar details for KYC verification. <br/> <br/>
												<input type="button" id="aadhar_btn" value="Submit" onclick="submit_kyc('aadhar','3','<?php echo ($kyc['aadhar']['type']);?>')" class="button btn btn-primary btn-large"/>
												<input type="hidden" id="submit_aadhar" value="3">
											</div>
												<?php } ?>
												<?php echo form_close();  ?>
											<!--</div>-->
										</div>
								<!-- Driving Licence Tab -->
										<div class="tab-pane overflow" id="tab_dl">
											<h4 class="page-header">Driving Licence <span  style="float:right; margin-right:70px;"id="a_txt"><span id="a_rmsat" > Status   <span id="dl_clrrm" > : <?php echo  $kyc['dl']['status']; ?> </span>  </span> </span></h4>
											<input type="hidden" id="dl_color" value="<?php echo $kyc['dl']['status'];?>" >
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>Driving Licence Number<span class="error"> * </span> 		</b></label>
													<div class="col-md-6 col-xs-7 kyc_form">
														<input type="text" id="dl_number" name="dl_number" value="<?php echo set_value('dl[dl_number]',isset($kyc['dl']['number'])?$kyc['dl']['number']:NULL ); ?>"  style='text-transform:uppercase' min="14" max="15" placeholder="Driving Licence" class="form-control" required />
													</div> 		
											</div><!---->
											<div class="col-md-12 col-xs-12 kyc_form marginBottom15">
												<label class="col-md-6 col-xs-5 kyc_form" for="firstname"> <b>Date of Birth<span class="error"> * </span> 		</b></label>
													<div class="col-md-6 col-xs-7 kyc_form">
														<input type="date" id="dl_dob" name="dl_dob" value="<?php echo set_value('dl[dl_dob]',isset($kyc['dl']['dob'])?$kyc['dl']['dob']:NULL); ?>" placeholder="D.O.B" class="form-control" required />
													</div> 	
											</div><!---->
											<!--<div class="col-md-2" style="margin-left: 300px;" >-->

													<?php if($kyc['dl']['status']=='Pending' || $kyc['dl']['status']=='Rejected'){ ?>
											<div class="col-md-12 col-xs-12 kyc_form" align="center">
												<input type="checkbox" name="dl_chk" id="dl_chk" required/> I agree to submit Driving Licence for KYC verification. <br/> <br/>
												<input type="button" id="dl_btn" value="Submit" onclick="submit_kyc('dl','4','<?php echo ($kyc['dl']['type']);?>')" class="button btn btn-primary btn-large"/>
												<input type="hidden" id="submit_dl" value="3">
											</div>
											<?php } ?>
									<!--</div>-->
										</div>
									</div>
								</div>
								<br/>
								<br/>
								<br/>
								<br/>
							</div>
						</div>
					</div>		
				<!-- /alert -->  
				</div><!-- /.box-body -->
				<div class="overlayy" style="display:none;font-size: 20px; position: absolute;top: 0%; z-index: 60;width: 100%;height: 100%;left: 0%;background: rgba(255,255,255,0.7);">
					<i class="fa fa-refresh fa-spin" style="margin-left: 50%;margin-top: 40%;"></i>
				</div>
			</div>
			<!-- /container --> 
		</div>
	  <!-- /main-inner --> 
	</div>
</div>
</div>
<!-- /main -->		  
<br />
<br />
<br />