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
						<div align="center"><legend class="head">Book an Appointment</legend></div>
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
							<p class="description">PLEASE FILL THE FORM TO MAKE APPOINTMENT</p>
							<?php 
								$attributes = array('id' => 'dth_form', 'name' => 'dth_form');
								echo form_open('user/dth',$attributes)  ?>
							<div class="col-md-12">
								<!--<div class="row marginBottom15">
								<div class="col-md-6"><label>Old password</label></div>
								<div class="col-md-6"><input type="password" class="form-control" id="old_passwd" name="old_passwd" required/></div>
								</div>-->
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="mobile"><b>Phone <span class="error">  *</span></b></label>  
									<div class="col-md-8">
										<input id="mobile" name="dth_form[mobiledth]" placeholder="Enter Phone" value="<?php echo $content['mobile']; ?>" class="form-control"  required/ >
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="name"> <b>Name<span class="error"> * </span> </b></label>
									<div class="col-md-8">
										<input type="name" class="form-control" id="name" name="dth_form[namedth]" value="<?php echo $content['name']; ?>"   placeholder=" Name" required="true">
									</div> 				
								</div>  
								<div class="col-md-12 row marginBottom15">
									<label class="col-md-4" for="email"> <b>Email Id </b></label>
									<div class="col-md-8">
										<input type="email" class="form-control" id="email" name="dth_form[emaildth]"  value="<?php echo $content['email']; ?>"  placeholder="Enter Email"/>
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">
									<label class="col-md-4" id="idtype" ><b>Type <span class="error">  *</span></b></label>          
									<div class="col-md-3">
										<label style="margin-left:-24px">
											<input type="checkbox" id="type_DTH" name="dth_form[type_DTH]" value="5" readonly="readonly" class="minimal" > DTH	
											<span class="inline-block"> Direct To Home </span>
										</label>
									</div>	
									<div class="col-md-5">
										<label style="margin-left:-56px">
											<input type="checkbox" id="type_EC" name="dth_form[type_EC]" value="6" readonly="readonly" class="minimal" < > Experience Center	
										</label>
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="address"><b>Address <span class="error">  *</span></b></label>
									<div class="col-md-8">
										<textarea rows="4" id="address" name="dth_form[addressdth]" placeholder="Enter Address" class="form-control"  required/ ></textarea>
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">
									<label class="col-md-4" for="comments"><b>Message <span class="error">  *</span></b></label>          
									<div class="col-md-8">
										<textarea rows="4" id="comments" name="dth_form[commentsdth]" placeholder="Enter Your Message" class="form-control"  required/ ></textarea>
									</div> <!-- /controls -->				
								</div> 
								<div class="col-md-12 row marginBottom15">											
									<label class="col-md-4" for="pincode"><b>Pin Code <span class="error">  *</span></b></label>     
									<div class="col-md-8">
										<input id="pincode" name="dth_form[pincodedth]" placeholder="Enter Pin Code" value="<?php echo $content['pincode']; ?>" class="form-control" maxlength="7" required/ >
									</div> <!-- /controls -->				
								</div> 
								<!--<div class="row marginBottom15">
								<div class="col-md-6"><label>Name</label></div>
								<div class="col-md-6"><input type="name" class="form-control" id="name" name="dth_form[namedth]" required/></div>
								</div>
								<div class="row marginBottom15">
								<div class="col-md-6"><label>Mobile</label></div>
								<div class="col-md-6"><input type="mobile" class="form-control" id="mobile" name="dth_form[mobiledth]" required/></div>
								</div>
								<div class="row marginBottom15">
								<div class="col-md-6"><label>Email</label></div>
								<div class="col-md-6"><input type="email" class="form-control" id="email" name="dth_form[emaildth]" required/></div>
								</div>-->
							</div>
							<div class="update_submit"><button type="submit" id="dth_submit"  onClick = "valthisform();" class="button btn">Submit</button></div>
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