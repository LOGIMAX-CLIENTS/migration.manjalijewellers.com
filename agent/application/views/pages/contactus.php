<?php 

$comp_details=$this->user_model->company_details(); 
//$company_details = $this->login_model->company_details();

?>
<link href="<?php echo base_url() ?>assets/css/pages/contact.css" rel="stylesheet" type="text/css">


    	
		<div class="col-md-12">							
			<div align="center"><legend class="head" >CONTACT US</legend></div>
		</div>
		<div class="contact">
		<div class="container" >
		<div class="row">
		
		<div class="col-md-8 contact">
             <h4 class="col-md-5 contact">Send an Enquiry</h4>
			 <div class="contact_form_holder form-group">
				 
					<?php  if($this->session->userdata('is_logged_in')==false){echo "<div class='errText col-md-8 contact' >Please login to send feedback</div>";}?>
					<div class="col-md-10">
						<label>Name</label>
						<input type="text" placeholder="Your Name" class="form-control " name="custName" id="cfName">
					</div>
					<div class="col-md-10">
						<label>Mobile</label>
						<input type="text" placeholder="10 digit Mobile Number" class="form-control " name="custMobile" id="cfMobile">
					</div>
					<div class="col-md-10">
						<label>Feedback Category</label>
						<select name="reg" id="cfreg" class="form-control " required="true">
							<option value="" selected>--Select your feedback category--</option>
							<option value="1" >Enquiry</option>
							<option value="2">Suggestion</option>
							<option value="3">Complaint</option>
							<option value="4">Others</option>
						</select>
					</div>
					<div class="col-md-10">
						<label>Message</label>
						<textarea placeholder="Enter your message here" class="form-control" name="custMessage" id="cfMessage"></textarea>
					</div>
					<div class="col-md-10">
						<img id="captcha_img" src="<?php echo base_url(); ?>captcha_contact.php?rand=<?php echo rand(); ?>">&nbsp;&nbsp;&nbsp;
						
						<a href="" id="cfrefreshCaptcha" > <i class="icon-repeat icon-large"></i> Refresh </a>
					</div>
					<div class="col-md-10">
						<label>Captcha</label>
						<input type="text" class="form-control " placeholder="Enter captcha here" name="answer" tabindex="6" id="cfcaptchaAns">
					</div>
					<div class="col-md-5">
					<a title="Click to leave feedback" class="pull_feedback" href="#"><span id="tab-text">Feedback</span></a>
					</div> 
					<div class="col-md-10">
						<button type="submit" id="submit" class="btn button btn-large contact-btn">SEND</button>
					</div>
				 
				 
			 </div>
							 
		</div>
	
         
					
					<div class="col-md-4 contact_us"> 
						<div class="contactus">
							<div class="getintouchTitle" style="">
								Shop Address
							</div>
							<div class="location">
								 <p><i class="fa fa-map-marker" aria-hidden="true" style="color: #020202;"></i> <?php echo $comp_details['address1'];  ?><br>&nbsp;&nbsp;&nbsp;<?php echo $comp_details['address2'];  ?><br>
								&nbsp;&nbsp;&nbsp;<?php echo $comp_details['state'];  ?>, <?php echo $comp_details['state'];  ?> </p>
							</div>
							
						
							<div class="getintouchTitle" style="">
								For Contact
							</div>
							<div class="contact1">
								<div class="noPadding"><p><i class="fa fa-phone" aria-hidden="true" style="color: #020202;"></i>  <a href="tel:<?php echo $comp_details['mobile'];  ?>" style="color:#000; text-decoration: none;"><?php echo $comp_details['mobile'];  ?></a></p>
								<p><i class="fa fa-phone" aria-hidden="true" style="color: #020202;"></i> <a href="tel:<?php echo $comp_details['mobile'];  ?>" style="color:#000; text-decoration: none;"><?php echo $comp_details['mobile'];  ?></a></p>
								<p><i class="fa fa-phone" aria-hidden="true" style="color: #020202;"></i> <a href="tel:<?php echo $comp_details['mobile'];  ?>" style="color:#000; text-decoration: none;"><?php echo $comp_details['mobile'];  ?></a></p></div>	
							</div>
							<div class="email">
								<p><a href="mailto:<?php echo $comp_details['email'];  ?>" style=""><i class="fa fa-envelope-o" aria-hidden="true" style="color: #020202;"></i>  <?php echo $comp_details['email'];  ?></a></p>
							</div>
						</div>
					
			
		
		</div>
		</div>
		</div>
		</div>
		
    
<script src="<?php echo base_url() ?>assets/js/pages/contactus.js"></script>