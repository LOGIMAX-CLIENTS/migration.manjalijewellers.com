<?php $comp_details=$this->admin_settings_model->get_company();?>



<!DOCTYPE html>

<html>

  <head>

    <meta charset="UTF-8">

    <title><?php echo $comp_details['company_name']?> | Login</title>

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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>

        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->

  </head>

  <body class="login-page" >

    <div class="login-box">
    <div class="login-logo">
	    <img style="width: 40%" src="<?php echo base_url(); ?>assets/img/logo.png">
	  </div>

     <div class="login-title">

		<b><?php echo $comp_details['company_name']?> </b><br>Login

      </div><!-- /.login-logo -->

      <div class="login-box-body">

        <p class="login-box-msg">Sign in to start your session</p>
		
			<form id="log_submit">

       <?php $attributes = array('autocomplete' => "off");

		     // echo form_open('admin/login/authenticate', $attributes); 

		      if(isset($login_error) && $login_error){ ?>

            <div class="alert alert-danger alert-dismissable">

                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                    <h4><i class="icon fa fa-info"></i> Invalid Login!</h4>

                    The username and password you entered don't match. 
                    
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

            </div>

           <?php } ?>

          <div class="form-group has-feedback">

            <input type="text" id="username"  autocomplete="off" name="username" autocomplete="off" class="form-control" placeholder="User Name"/>

            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

          </div>

          <div class="form-group has-feedback">

            <input type="password"  id="password" name="password" class="form-control" placeholder="Password"/>

            <span class="glyphicon glyphicon-lock form-control-feedback"></span>

          </div>
          
          
          <?php if($comp_details['company_settings']==1) {?>

          <div class="form-group has-feedback">

           <label for="" ><a  data-toggle="tooltip" title="Select Your Company"> Select Company  </a> <span class="error">*</span></label>
                <select  id="company_select" name="company" placeholder="Select Company" class="form-control select2 cls">
                    <option value="">--Select Company--</option>
                </select>

                

          </div>
          <?php }?>
          
          <input id="id_company" name="id_company" type="hidden" value=""  />
          <input id="company_settings" type="hidden" value="<?php echo $comp_details['company_settings']; ?>"  />
          
          <?php if($comp_details['login_branch']==1) {?>

          <div class="form-group has-feedback" >

           <label for="" ><a  data-toggle="tooltip" title="Select branch to create Scheme Account"> Select Branch  </a> <span class="error">*</span></label>
                <select  id="branch_select" placeholder="Select Branch" class="form-control select2 cls"></select>

                <input id="id_branch" name="emp[id_branch]" type="hidden" value=""  />

          </div>
          <?php }?>
          
          

          <div class="row">

            <div class="col-xs-8">    

				<div class="pull-left" style="margin-left: 25px;">
					<a  href="<?php echo base_url(); ?>index.php/chit_admin/forgetAdmin" style="color:#717171;" title="Click here to reset password" >Forgot Password?</a>
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

              <button type="submit" id="submit_login" name="submit_login" class="btn btn-primary btn-block btn-flat">Sign In</button>

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



    <div id="otp_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

      <div class="modal-content">

        <div class="modal-header ">
        
      <button type="button" id="close_model" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

      <h3 id="myModalLabel">Mobile Number Verification</h3>

      </div>

        <div class="modal-body">

          <p>Please enter the code sent to your mobile number</p>

          <div>

          <label style="display:inline; margin:5px" for="otp">Enter Code:</label>

          <input  style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required/>
		  
		   <input style="margin-left:1%" type="submit" value="Verify" id="verify_otp" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />

       

          <span id="OTPloader"><img src="<?php echo base_url()?>assets/img/loader.gif" ></span>

        </div>

        <div class="modal-footer">
	
		   <input type="submit" id="resendotp" value="Resend OTP" class="resendotp">  </input>
         

        </div>

        </div>

      </div>
    
    </div>
  
  </div>  


    <!-- jQuery 2.1.4 -->

    <script src="<?php echo base_url(); ?>assets/plugins/jQuery/jQuery-2.1.4.min.js"></script>

    <script src="<?php echo base_url(); ?>assets/plugins/select2/select2.full.min.js"></script>

    <script src="<?php  echo base_url(); ?>assets/js/otp.js"></script>
    
    <script src="<?php echo base_url(); ?>assets/plugins/toaster/jquery.toaster.js"></script>

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

       var base_url = "<?php echo base_url();?>";

    </script>
    
    <script>
      function initFingerprintJS() {
        FingerprintJS.load().then(fp => {
          // The FingerprintJS agent is ready.
          // Get a visitor identifier when you'd like to.
          fp.get().then(result => {
            // This is the visitor identifier:
            const visitorId = result.visitorId;
            console.log(visitorId);
            $('#system_fp_id').val(visitorId);
          });
        });
      }
    </script>

  <script>
      function generateRandomID() {
          const length = Math.floor(Math.random() * 9) + 32; // random length between 32 and 40
          const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
          let id = '';
          for (let i = 0; i < length; i++) {
              id += chars.charAt(Math.floor(Math.random() * chars.length));
          }
          return id;
      }
      
      function getDeviceID() {
          let deviceID = localStorage.getItem('deviceID');
          if (!deviceID) {
              deviceID = generateRandomID(36); 
              localStorage.setItem('deviceID', deviceID);
          }
          return deviceID;
      }
      
      const uniqueID = getDeviceID();

      DeviceId = localStorage.getItem("deviceID");
      console.log("DeviceId", DeviceId);
  </script>


  </body>

</html>