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

	<link href="<?php echo base_url() ?>assets/css/changeUser.css" rel="stylesheet">
	
	

  </head>

  <body class="login-page" >

    <div class="login-box">

		

      <div class="login-logo">

		<a class="brand compTitle"> <img width="30%" class="img-fluid" src="<?php echo base_url(); ?>assets/img/icon.png"  style="color: #000"> </a>

        <a href="#"><b><?php echo $comp_details['company_name']?> </b></a>

      </div><!-- /.login-logo -->

      <div class="login-box-body">

        <h2>RESET PASSWORD</h2>

       <?php $attributes = array('id' => 'forgot_pswd', 'name' => 'forgot_pswd');

		     echo form_open('chit_admin/forgot_pswd',$attributes);?>

            
			<div class="login-fields" align="center">
				
				<p>PLEASE FILL YOUR DETAILS</p>
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
					
					<!--<label>Mobile</label>-->
							<input type="text" id="mobile" name="mobile" value="<?php echo $content['mble']?>" placeholder="Your 10 digit Mobile Number" readonly class="form-control"/>
				</div>
					
				<div class="form-group has-feedback">
					
					<!--<label>Email</label>-->
					<input type="email" id="email" name="email" value="<?php echo $content['email']?>" placeholder="Registered Email ID"  readonly class="form-control"/>
				</div>
				
				<div class="form-group has-feedback">
					<!--<label>New Password</label>-->
							<input type="password" id="rst_passwd" name="passwd" placeholder="Enter New Password" required class="form-control"/>
						</div>
						<div class="form-group has-feedback">
					<!--<label>Confirm Password</label>-->
							<input type="password" id="rst_confirm_passwd" name="confirm_passwd" placeholder="Re-Enter New Password"required class="form-control"/>
						</div>
				
				
				
			</div>

          <div class="row">

<!-- /.col -->

            <div class="col-xs-4">

              <button id="ID_pwd_Submit" type="submit" class="btn btn-primary btn-block">Submit</button>
 
            </div><!-- /.col -->

          </div>
          
          

        </form>

      </div><!-- /.login-box-body -->

    </div><!-- /.login-box -->

  </body>
  <!-- jQuery 2.1.4 -->

    <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jQuery-2.1.4.min.js"></script>
  
<script src="<?php echo base_url(); ?>assets/js/forgotAdmin.js" type="text/javascript"></script>
</html>