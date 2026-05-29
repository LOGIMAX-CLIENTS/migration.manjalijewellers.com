<?php

	$company_details = $this->login_model->company_details();

	//echo "<pre>";print_r($company_details);echo "</pre>";exit;

?>

<!-- <a class="store-locator-btn" href="<?php echo base_url(); ?>index.php/user/store_locatore" target=""/><span class="store-locator-btn-txt">Store&nbsp;&nbsp;Locator </span></a>-->
                               

<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pages/contact.css">



<div id="contact" class="contact-box">



	<form autocomplete="off" action="#" id="contact-form">



		<div id="content">



			



			<div class="errText"></div>
			<?php  if($this->session->userdata('is_logged_in')==false){echo "<div class='errText'>Please login to send feedback</div>";}?>



			<div class="contactDts"><i class="icon-envelope icon-large"></i> &nbsp;<?php echo  $company_details['email']; ?> </div>

			
			<?php if($company_details['tollfree1']!='') { ?>
			
			<div class="contactDts"><i class="icon-phone-sign icon-large"></i> &nbsp;<?php echo ($company_details['tollfree1']); ?> (Toll-Free Number) </div>
			<?php } ?>

			<div class="contactDts">

				<i class="icon-phone-sign icon-large"></i>

				&nbsp; <?php echo (!empty($company_details['phone1'])?$company_details['mob_code'].'  '.$company_details['phone1']:''); echo (!empty($company_details['phone'])?',  '.$company_details['mob_code'].'  '.$company_details['phone']:'');?><br/>

				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo  $company_details['mobile'];echo (!empty($company_details['mobile1'])?', '.$company_details['mobile1']:''); ?>

			</div><br/>



			<div><input type="text" placeholder="Your Name" class="form-control marginBottom10" name="custName" id="custName"></div>



			<div><input type="text" placeholder="10 digit Mobile Number" class="form-control marginBottom10" name="custMobile" id="custMobile"></div>

            <div>
                <select name="reg" id="reg" class="form-control marginBottom10" required="true">
					<option value="" selected>--Select your feedback category--</option>
					<option value="1" >Enquiry</option>
					<option value="2">Suggestion</option>
					<option value="3">Complaint</option>
					<option value="4">Others</option>
				</select>
			</div>

			<div><textarea placeholder="Enter your message here" class="form-control" name="custMessage" id="custMessage"></textarea></div>



			<div class="captchaDts">



			<img id="captcha_img" src="<?php echo base_url() ?>captcha_contact.php?rand=<?php echo rand(); ?>">&nbsp;&nbsp;&nbsp;



			<a href="" id="refreshCaptcha" > <i class="icon-repeat icon-large"></i> Refresh </a>



			</div>



			<div><input type="text" class="form-control marginBottom10" placeholder="Enter captcha here" name="answer" tabindex="6" id="captchaAns"></div>



			<div><input type="button" value="Send" id="contact_submit" class="btn button btn-large login-btn" <?php  if($this->session->userdata('is_logged_in')==false){echo "disabled='true'";}?> ></div>



		</div>



	</form>



	<a title="Click to leave feedback" class="pull_feedback" href="#"><span id="tab-text">Feedback</span></a>



	

</div>