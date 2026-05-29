<?php $comp_details=$this->admin_settings_model->get_company();  ?>



<!DOCTYPE html>

<html>

  <head>

    <meta charset="UTF-8">

    <title><?php echo $comp_details['company_name']?> | Admin Forget Password</title>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Bootstrap 3.3.4 -->

    <link href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- Font Awesome Icons -->

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme style -->

    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

    <!-- iCheck -->

    <link href="<?php echo base_url(); ?>assets/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet" type="text/css">

  </head>

  <body class="login-page" >

    <div class="login-box">
         <div class="login-logo">
        	<img width="50%"  src="<?php echo base_url(); ?>assets/img/logo.png" "> 
    	</div>

     <div class="login-title">

		<b><?php echo $comp_details['company_name']?> </b><br>Forget Password?

      </div><!-- /.login-logo -->

      <div class="login-box-body">

        <!--<h2>Forget Password?</h2>-->
        
        

       <?php $attributes = array('id' => 'forgotForm', 'name' => 'forgotForm');

		     echo form_open('chit_admin/forgetUser',$attributes); 

		      if(isset($login_error) && $login_error){ ?>

            <div class="alert alert-danger alert-dismissable">

                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                    <h4><i class="icon fa fa-info"></i> Invalid Login!</h4>

                    The username and password you entered don't match. 

            </div>
            

           <?php } ?>

	
			<div class="login-fields" align="center">
				
				<p>Please provide your details:</p>
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
				
				<div class="form-group has-feedback">
					<!--<label for="password">Mobile No</label>-->
					<input type="number" id="mobile" name="mobile" value="" placeholder="Enter Employee Mobile Number" class="form-control"/>
				</div>
				
				<div class="form-group has-feedback">
					<!--<label for="username">Email ID</label>-->
					<input type="email" id="email" name="email" value="" placeholder="Enter Employee Email ID" class="form-control" />
				</div>
				
			</div>

          <div class="row">

            <div class="col-xs-8">    

				<div class="pull-left" style="margin-left: 25px;">
					<a  href="<?php echo base_url(); ?>index.php/chit_admin/login" style="color:#ff0000;" title="Click here for Login page" rel="tooltip">Return to Login Page</a>
					</div>
				<!--<div class="pull-left" style="margin-left: 25px;">
					<a  href="<?php echo base_url(); ?>index.php/user/forget" style="color:#717171;" title="Click here to reset password" rel="tooltip">Forgot Password?</a>
					</div>-->

              <div class="checkbox icheck">

                <label>

               <!--   <input type="checkbox"> Remember Me -->

                </label>

              </div>                        

            </div>

<!-- /.col -->
 
            <div class="col-xs-4">
            
            <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>

              <button id="generate_otp" type="submit" name="submit_login" class="btn btn-primary btn-block btn-flat">Submit</button>
              



<!-- modal -->      
<div class="modal fade" id="otp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="modal-title">Mobile Number Verification</h3>
		</div>
      <div class="modal-body">
              <p>Please enter the code sent to your mobile number</p>
			  	<div>
					<label style="display:inline; margin:5px" for="otp">Enter Code:</label>
					<input  style="display:inline; margin:5px" type="text" id="otp" name="otp" value="" class="login" required/>
					<a style="margin-right:1%;margin-left:1%" id="resendOTP" >Resend OTP</a>
					<span id="OTPloader"><!--<img src="<?php echo base_url()?>assets/img/loader.gif" >--></span>
				</div>
				<div>
					<input style="margin-left:1%" type="submit" value="Verify" id="submit" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />
				</div>
      </div>
      <!--<div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>-->
    </div>
  </div>
</div>
<!-- / modal -->  
            </div><!-- /.col -->

          </div>
          
          

        </form>

<!--

        <div class="social-auth-links text-center">

          <p>- OR -</p>

          <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>

          <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>

        </div>-->

        <!-- /.social-auth-links -->



      <!--  <a href="#">I forgot my password</a><br> -->

        <!--<a href="register.html" class="text-center">Register a new membership</a>-->



      </div><!-- /.login-box-body -->
      
      

    </div><!-- /.login-box -->



    <!-- jQuery 2.1.4 -->

    <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jQuery-2.1.4.min.js"></script>

    <!-- Bootstrap 3.3.2 JS -->

    <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

    <!-- iCheck -->

    <script src="<?php echo base_url(); ?>assets/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

    <script>

      $(function () {

        $('input').iCheck({

          checkboxClass: 'icheckbox_square-blue',

          radioClass: 'iradio_square-blue',

          increaseArea: '20%' // optional

        });

      });
var base_url="<?php echo base_url();  ?>";

    </script>
	<script src="<?php echo base_url(); ?>assets/js/forgotAdmin.js" type="text/javascript"></script>
	  <script src="<?php echo base_url(); ?>assets/dist/js/app.min.js" type="text/javascript"></script>   

  </body>

</html>