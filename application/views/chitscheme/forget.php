<link href="<?php echo base_url() ?>assets/css/pages/signin.css" rel="stylesheet" type="text/css">
<div class="main-container">
	<div  class="container-fluid " align="center">
		<div class="loginForm" >
			<div class="container-fluid header">
				<div class="container">	
					<div class="member_login">
						<div class="">
							<?php 
							$attributes 		=	array('id' => 'forgotForm', 'name' => 'forgotForm');
							echo form_open_multipart('user/forgetUser',$attributes);  
							?>
							<!--<span class="title">MEMBER LOGIN</span>-->
							<h3 class="titleLogin">Forgot Password?</h3>	
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
								<div class="login_form"><p>Please provide your details:</p>
									<div class="form-group">
										<label for="password">Mobile No</label>
										<input type="number" id="mobile" name="mobile" class="form-control" value="" placeholder="Registered Mobile Number" required autofocus="true"/>
									</div>
									<!--<div class="form-group">
									<label for="username">Email ID</label>
									<input type="email" id="email" name="email" class="form-control" value="" placeholder="Registered Email ID" required />
									</div>	-->
								</div>
							</div>
							<div class="reg_form">
							<button type="button" id="generate_otp" class="reg_button" style="padding: 5px;">Submit</button>
							</div>
						</div> <!-- /content -->
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
								<input style="margin-left:1%" type="submit" value="Verify" id="submit" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />
							</div>
						</div>
					</div>
				</div>
			</div>	 
		</form>
		</div>
	</div>