<link href="<?php echo base_url() ?>assets/css/pages/changeUser.css" rel="stylesheet">
<div class="main-container">
	<!-- main -->		  
	<div class="main"  id="resetPass">
		<!-- main-inner --> 
		<div class="main-inner">
			<!-- container --> 
			<div class="container header">
			<!-- alert -->
				<div class="row">
					<div class="span12">
						<p style="height: 10px"></p>
						<div class="mainDiv">
							<div align="center"><strong style="font-size: 21px !important; font-family:  'Open Sans', sans-serif;">RESET PASSWORD</strong></div>
							<?php
							if($this->session->flashdata('successMsg')) { ?>
							<div class="alert alert-success" align="center">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
							</div>   
							<?php } else if($this->session->flashdata('errMsg')) { ?>	
							<div class="alert alert-danger" align="center">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong><?php echo $this->session->flashdata('errMsg'); ?>
							</div>
							<?php } ?>
							<p class="description">PLEASE FILL YOUR DETAILS</p>
							<?php 
							$attributes = array('id' => 'forgot_pswd', 'name' => 'forgot_pswd');
							echo form_open('user/forgot_pswd',$attributes)  ?>
							<div class="innerDiv">
								<div class="inputDiv" >
									<label>Mobile</label>
									<input type="text" id="rst_mobile" name="mobile" class="form-control" style="width: 50%" value="<?php echo $content['mble']?>" placeholder="Registered Mobile Number" readonly/>
								</div>
								<div class="inputDiv">
									<label>E-mail</label>
									<input type="email" id="rst_email" name="email" class="form-control" style="width: 50%" value="<?php echo $content['email']?>" placeholder="Registered Email ID"  readonly/>
								</div>
								<div class="inputDiv">
									<label>New Password</label>
									<input type="password" id="rst_passwd" name="passwd" class="form-control" style="width: 50%" placeholder="Enter New Password" required autofocus="true"/>
								</div>
								<div class="inputDiv">
									<label>Confirm Password</label>
									<input type="password" id="rst_confirm_passwd" name="confirm_passwd" class="form-control" style="width: 50%" placeholder="Re-Enter New Password"required/>
								</div>
							</div>
							<button type="submit" id="ID_pwd_Submit" style="margin-bottom: 5%" class="btn btn-primary">Submit</button><br />
							</form>
						</div>
					</div>
				<!-- /alert --> 
				</div>
				<!-- /container --> 
			</div>
			<!-- /main-inner --> 
		</div>
		<!-- /main -->
	</div>
</div>
<script type="text/javascript">
page = "forgot"
</script>