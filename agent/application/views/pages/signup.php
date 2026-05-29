<?php $data=$this->user_model->company_details();  //$data=$this->login_model->company_details(); ?>
<link href="<?php echo base_url() ?>assets/css/pages/signin.css" rel="stylesheet" type="text/css">
<div class="main-container">
<div  class="container-fluid " align="center">
<div class="loginForm" >
<div class="container-fluid header">
<div class="container">	
<div class="member_login">
	<div class="">
	<?php 
		$attributes 		=	array('id' => 'signupForm', 'name' => 'signupForm','autocomplete'=>'off');
		echo form_open_multipart('user/DB_controller/add',$attributes);  
	?>
		<!--<span class="title">MEMBER LOGIN</span>-->
		<h3 class="titleLogin theme-txt">MEMBER REGISTER</h3>	
			<div class="login-fields" align="center">
				<?php if($this->session->flashdata('successMsg')) { ?>
						<div class="alert alert-success" align="center">
						  <button type="button" class="close" data-dismiss="alert">&times;</button>
						  <strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
						</div>      
				<?php } else if($this->session->flashdata('errMsg')) {  ?>							 
						<div class="alert alert-danger" align="center">
						  <button type="button" class="close" data-dismiss="alert">&times;</button>
						  <strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
						</div>
				<?php } ?>
				<div class="login_register">
								<a href="<?php echo base_url() ?>index.php/user/login" title="Click here to sign in" rel="tooltip">Login</a> | <a href="<?php echo base_url(); ?>index.php/user/register_add" title="Click here to sign-up" rel="tooltip">New User?</a>
							</div>
		<div class="login_form">
		   
                        <!--<div class="col-md-12 row marginBottom10">	    										
                          <div class="row">
                                <div class="col-md-4">
                                        <select name="title"  style="height: 33px; border-radius: 3px;">
                                        <option value="none" selected disabled hidden> 
                                        <?php echo $profile[0]['title']; ?> </option>
                                        <option value="Mr">Mr</option>
                                        <option value="Ms">Ms</option>
                                        <option value="Mrs">Mrs</option>
                                        <option value="Dr">Dr</option>
                                        <option value="Prof">Prof</option>
                                        </select>
                                  </div>
                                   <div class="col-md-8" style="width: 66.666667%;">
                                        <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Name"  required autofocus="true"/>
                                        <input type="hidden"  name="referal_code" class="form-control"  value="<?php echo $this->uri->segment(3); ?>"/>
                                    </div> 
                          </div>
                        </div>-->
                        
                        
                         <div class="input-group">
                                    <span class="input-group-addon">
                                       <select name="title">
                                            <option value="none" selected disabled hidden></option>
                                            <option value="Mr">Mr</option>
                                            <option value="Ms">Ms</option>
                                            <option value="Mrs">Mrs</option>
                                            <option value="Dr">Dr</option>
                                            <option value="Prof">Prof</option>
                                        </select>
                                    </span>
                                            <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Name"  required autofocus="true"/>
                                            <input type="hidden"  name="referal_code" class="form-control"  value="<?php echo $this->uri->segment(3); ?>"/>  
                                    </div><!-- /input-group -->
								    </br>
								
									<div class="form-group">
										<label for="mobile">Mobile</label>
										<input type="number" id="mobile" name="mobile" value="" class="form-control" placeholder="Mobile Number" pattern="\d{10}" maxlength="10" required />
									</div>
									<div class="form-group">
										<label for="email">E-Mail</label>
					<input type="email" id="email" name="email" value="" class="form-control" placeholder="E-mail" required />
									</div>
									<div class="form-group">
										<label for="address1">Address1</label>
					<input type="text" id="address1" name="address1" value="" class="form-control" placeholder="Street"/>
									</div>
									<div class="form-group">
										<label for="address2">Address2</label>
					<input type="text" id="address2" name="address2" value="" class="form-control" placeholder="Area"/>
									</div>
									
								<?php if($this->config->item('custom_fields')['city']==1 || $this->config->item('custom_fields')['city']==2){?>
											<div class="form-group">
                                   <label for="City">City</label>
                                   <input  type="hidden" id="cityval" name="cityval" value="" />
                                <input  type="hidden" id="custom_fields_city"  name="custom_fields_city" value="<?php echo $this->config->item('custom_fields')['city']; ?>" />
                    <select   style="width:100%;" class="form-control" id="id_city" name="id_city" />
						   </select>
                                 </div>
                                  <?php } ?>
                                 
                                 
						<?php	if($this->config->item('custom_fields')['state']==1 || $this->config->item('custom_fields')['state']==2){?>
						               <div class="form-group">
                             <label for="State">State</label>
                               <input  type="hidden" id="stateval" name="stateval" value="" />
                            <input  type="hidden" id="custom_fields_state"  name="custom_fields_state" value="<?php echo $this->config->item('custom_fields')['state']; ?>" />
                              <select style="width:100%;" id="id_state" name="id_state" class="form-control" />
							</select>
                                        </div>
                                      <?php } ?>
									
						              
                               	<?php if($this->config->item('custom_fields')['country']==1 || $this->config->item('custom_fields')['country']==2){?>         
                                        	<div class="form-group">
                                   <label for="Country">Country</label>
                                   <input  type="hidden" id="custom_fields_country"  name="custom_fields_country" value="<?php echo $this->config->item('custom_fields')['country']; ?>" />
                                   <input  type="hidden" id="countryval" name="countryval" value="" readonly/>
                    <select   style="width:100%;" class="form-control" id="id_country" name="id_country" />
						   </select>
                                 </div>
                                 
                                 <?php } else if($this->config->item('custom_fields')['country']==0 && $this->config->item('custom_fields')['state']==2) {  ?>
                                <input  type="hidden" id="custom_fields_country"  name="custom_fields_country" value="<?php echo $this->config->item('custom_fields')['country']; ?>" />
                                
                                 <input  type="hidden" id="countryval" name="countryval" value="101" readonly/>
                                 <input  type="hidden" id="id_country" name="id_country" value="101" readonly/>
                                  
								<?php } ?>
								
								
									<div class="form-group">
										<label for="password">Password:</label>
					<input type="password" id="passwd" name="passwd" value="" placeholder="Password" class="form-control" required/>
									</div>
									<div class="form-group">
										<label for="confirm_password">Confirm Password:</label>
					<input type="password" id="confirm_password" name="confirm_password" value="" placeholder="Confirm Password" class="form-control" required/>
									</div>
									<?php if($data['branch_settings']==1 && $data['is_branchwise_cus_reg']==1){?>				
										<div class="form-group" >
										   <label>Select Branch &nbsp;</label>
											<select id="branch_select" class="form-control" required>
											</select>
											<input id="id_branch" name="id_branch" type="hidden" />
										</div>
							       <?php } ?>
							</div>
		</div>
		<div class="login-actions">
							<span class="pull-left">
					<label class="remember_me" for="terms"><input id="terms" name="terms" type="checkbox" class="checkbox-inline" value="Terms" required />&nbsp;Agree with the <a class="terms theme-txt" href="#" >Terms & Conditions</a></label>
							</span>
						</div>
						<div class="reg_form">
								<button type="button" id="generate_otp" class="reg_button"  value="SIGNUP">SIGN UP</button>
							</div>
	</div> <!-- /content -->
			<div class="cmp_details theme-txt">
	<div>Customer Care | <?php echo (!empty($data['phone1'])?$data['mob_code'].'  '.$data['phone1']:'');?></div><div>E-mail | <?php echo $data['email'];?></div>
		</div>
</div><!-- /member_login -->
</div><!--container-->	
</div><!--container-fluid header-->
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
					<label style="display:inline; margin:5px" for="otp">Enter Code:</label>
					<input  style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required/>
					<a style="margin-right:1%;margin-left:1%" id="resendOTP" >Resend OTP</a>
					<span id="OTPloader"><img src="<?php echo base_url()?>assets/img/loader.gif" ></span>
				</div>
				<div class="modal-footer">
					<input style="margin-left:1%" type="submit" value="Submit" id="submit" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />
				</div>
			  </div>
			</div>
		</div>
	</div>	
</form>
</div>
</div>
</div>
<script type="text/javascript">
    var mob_no_len ="<?php echo $header_data['mob_no_len'];?>";   
    var  is_branchwise_cus_reg="<?php echo $data['is_branchwise_cus_reg'];?>"  // Branch wise Cus Reg In User //HH
  </script>