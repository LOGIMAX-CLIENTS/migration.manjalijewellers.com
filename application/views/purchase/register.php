<?php $data=$this->login_model->company_details(); ?>
<link href="<?php echo base_url() ?>assets/css/pages/purchase.css" rel="stylesheet" type="text/css">
<div class="main-container">
<div  class="container-fluid " align="center">
<div class="purchaseRegForm" >
<div class="container-fluid header">
<div class="container">	
	<div class="row"> 
		<div class="col-md-12"> 
			<div align="center"><legend class="head">AKSHAYA TRITIYA OFFER</legend></div>
			<p>You can now block your gold purchase at today’s price and take delivery of your jewellery when stores open after lockdown ! 
</p>
			<p> Verify your mobile number and block your gold purchase.</p>
			<div class="">
				<div class="row"> 
	 				<div class="col-md-4"></div>
	 				<div class="col-md-4 box">
				<?php 
					$attributes 		=	array('id' => 'signupForm', 'name' => 'signupForm','autocomplete'=>'off');
					echo form_open_multipart('purchase/DB_controller/add',$attributes);  
				?> 
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
			                    <!--<div class="input-group">
			                        <span class="input-group-addon">
			                           <select name="title"> 
			                                <option value="Mr" selected>Mr</option>
			                                <option value="Ms">Ms</option>
			                                <option value="Mrs">Mrs</option>
			                                <option value="Dr">Dr</option>
			                                <option value="Prof">Prof</option>
			                            </select>
			                        </span>
			                        <input type="text" id="firstname" name="firstname" class="form-control" placeholder="Name"  required autofocus="true"/>
			                        <input type="hidden"  name="referal_code" class="form-control"  value="<?php echo $this->uri->segment(3); ?>"/>  
			                     </div>--> 
								 <br/>
									<div class="form-group">
										<!--<label for="mobile" class="pull-left">Mobile</label>-->
										<input type="number" id="mobile" name="mobile" value="" class="form-control" placeholder="Mobile Number" pattern="\d{10}" required/>
									</div>
									<!--<div class="form-group">
										<label for="email">E-Mail</label>
					<input type="email" id="email" name="email" value="" class="form-control" placeholder="E-mail" required />
									</div>-->  
							</div>
							<div class="reg_form">
							<button type="button" id="generate_otp" class="reg_button" name="type" value="Verify Mobile">Verify</button>
						</div>
						</div>
						<!--<div class="login-actions" align="center"> 
							<label class="remember_me" for="terms"><input id="terms" name="terms" type="checkbox" class="checkbox-inline" value="Terms" required />&nbsp;&nbsp;Agree with <a class="terms theme-txt" href="https://www.jeweloneretail.in/jwlone_plan/index.php/user/terms" >Terms & Conditions</a></label> 
						</div>-->
						
					</div> <!-- /col -->
				</div> <!-- /row -->
						<!--<div class="cmp_details theme-txt">
				<div>Customer Care | <?php echo (!empty($data['phone1'])?$data['mob_code'].'  '.$data['phone1']:'');?></div><div>E-mail | <?php echo $data['email'];?></div>
					</div>-->
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
			<h3 id="myModalLabel">Mobile Number Verification</h3>
		  </div>
			  <div class="modal-body">
			  	<p>Please enter the code sent to your mobile number</p>
			  	<div>
					<label style="display:inline; margin:5px" for="otp">Enter Code:</label>
					<input  style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required autofocus="true"/>
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
<script type="text/javascript">
    var mob_no_len ="<?php echo $header_data['mob_no_len'];?>";   
  </script>