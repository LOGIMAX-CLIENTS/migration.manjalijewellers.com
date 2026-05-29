<link href="<?php echo base_url() ?>assets/css/pages/video_shopping.css" rel="stylesheet" type="text/css">
<div class="main-container">
<div  class="container-fluid " align="center">

<div class="vsRegForm" >
<div class="container-fluid header">
<div class="container">	
	<div class="row"> 
		<div class="col-md-12"> 
			<div align="center"><legend class="head">VIDEO SHOPPING APPOINTMENT</legend></div>
			<div class="">
				<div class="row"> 
	 				<div class="col-md-4"></div>
	 				<div class="col-md-4 box">
				<?php 
					$attributes 		=	array('id' => 'signupForm', 'name' => 'signupForm','autocomplete'=>'off');
					echo form_open_multipart('vs_appt_book/add',$attributes);  
				?> 
					<p class="sub-head">REGISTERED USER? <a href="<?php echo base_url();?>/index.php/user/login" rel="tooltip" title="Click here to sign in">Login</a></p>	
					<p>OR</p>
					<p class="sub-head">VERIFY MOBILE</p>	
						<div class="" align="center">
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
							<div class="cus_form">    
								<div class="form-group"> 
									<input type="number" id="vs_mobile" name="mobile" value="" class="form-control" placeholder="Mobile Number" pattern="\d{10}" required/>
									<span class="note"> Verify your mobile number and book your video shopping appointment.</span>
								</div> 
							</div>
							<span id="err"  class="error"></span>
							<div class="reg_form">
							<button type="button" id="generate_otp" class="reg_button" name="type" value="Verify Mobile">Verify</button>
						</div>
						</div> 
					</div> <!-- /col -->
				</div>  
			</div><!-- /member_login -->
		</div><!--col-->	
	</div><!--row-->	
</div><!--container-->	
</div><!--container-fluid header-->
<div id="otp_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header ">
		 	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel" align="center">Mobile Number Verification</h3>
		  </div>
		  <div class="modal-body">
		  	<p>Please enter the code sent to your mobile number</p>
		  	<div>
				<label style="display:inline; margin:5px" for="otp">Enter Code:</label>
				<input  style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required autocomplete="off" autofocus="true"/>
				<span id="otpErr"  class="error"></span>
				<a style="margin-right:1%;margin-left:1%" id="resendOTP" >Resend OTP</a>
				<span id="OTPloader"><img src="<?php echo base_url()?>assets/img/loader.gif" ></span>
			</div>
			<div class="modal-footer">
				<button style="margin-left:1%" type="button" id="verifyOTP" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />Verify</<button>
			</div>
		  </div>
		</div>
	</div>
</div>	
</div>
</div>
</div>

