<link href="<?php echo base_url() ?>assets/css/pages/changeUser.css" rel="stylesheet">

<div class="main-container">

<!-- main -->		  

<div class="main"  id="resetPass">

  <!-- main-inner --> 

  <div class="main-inner">

     <!-- container --> 

    <div class="container">

	  <!-- alert -->

      <div class="row">

        <div class="col-md-12">

<div align="center"><legend class="head">RESET PASSWORD</legend></div>
			

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

			<div class="mainDiv">

								<p class="description">PLEASE ENTER YOUR DETAILS</p>



			<?php 

			$attributes = array('id' => 'reset_passwd', 'name' => 'reset_passwd');

			echo form_open('user/resetPass_submit',$attributes)  ?>

					<div class="col-md-12">

						<!--<div class="row marginBottom15">

							<div class="col-md-6"><label>Old password</label></div>

							<div class="col-md-6"><input type="password" class="form-control" id="old_passwd" name="old_passwd" required/></div>

						</div>-->

						<div class="row marginBottom15">

							<div class="col-md-6"><label>New Password</label></div>

							<div class="col-md-6"><input type="password" class="form-control" id="passwd" name="passwd" required/></div>

						</div>

						<div class="row marginBottom15">

							<div class="col-md-6"><label>Confirm Password</label></div>

							<div class="col-md-6"><input type="password" class="form-control" id="confirm_passwd" name="confirm_passwd" required/></div>

						</div>

			</div>

			<div class="update_submit"><button type="submit" id="IDSubmit"  class="button btn">Submit</button></div>

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

<script type="text/javascript">

page = "resetPass"

</script>