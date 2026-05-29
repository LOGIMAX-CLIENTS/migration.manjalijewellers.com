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
						<div align="center"><legend class="head">Send Gift Card </legend></div>
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
							<?php 
							$attributes = array('id' => 'gifts_form', 'name' => 'gifts_form');
							echo form_open('user/gifts',$attributes)  ?>
							<div class="col-md-12">
								<!--<div class="row marginBottom15">
								<div class="col-md-6"><label>Old password</label></div>
								<div class="col-md-6"><input type="password" class="form-control" id="old_passwd" name="old_passwd" required/></div>
								</div>-->
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="name"> <b>SENDER NAME<span class="error"></span> </b></label>
									<div class="col-md-8">
										<input type="name" class="form-control" id="name" name="gifts_form[name]" value="<?php echo $content['name']; ?>"   placeholder=" Name" required="true">
										<input type="hidden" name="gifts_form[id_gift_card]" value="<?php echo $this->uri->segment(3); ?>"  id="id_gift_card" >	
									</div> 				
								</div> 
								<!--  <div class="col-md-12 row marginBottom15">									
								<label class="col-md-4" for="name"> <b>RECIPIENT NAME </b></label>
								<div class="col-md-8">
								<input type="text" class="form-control" id="name" name="gifts_form[namedth]" value="<?php echo $content['name']; ?>"   placeholder=" Name"/>	
								</div> <!-- /controls -->				
								<!--</div>-->
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="
									"><b>RECIPIENT MOBILE <span class="error"></span></b></label>     
									<div class="col-md-8">
										<input id="mobile" name="gifts_form[trans_to_mobile]" placeholder="Enter 10 digit Mobile Number" value="<?php echo $content['trans_to_mobile']; ?>" class="form-control" maxlength="10" required/ >
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">								
									<label class="col-md-4" for="trans_to_email"> <b>RECIPIENT EMAIL</b></label>
									<div class="col-md-8">
										<input type="email" class="form-control" id="email" name="gifts_form[trans_to_email]"  value="<?php echo $content['trans_to_email']; ?>"  placeholder="Enter Email"/>	
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="message"><b>PERSONAL MESSAGE <span class="error">  *</span></b></label>
									<div class="col-md-8">
										<textarea rows="4" id="message" name="gifts_form[message]" placeholder="Enter message" class="form-control"  required/ ></textarea>
									</div> <!-- /controls -->				
								</div> 
							</div>
							<div class="update_submit"><button type="submit" id="dth_submit"  onClick = "valthisform();" class="button btn">Send</button></div>
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