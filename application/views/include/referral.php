<?php

	$company_details = $this->login_model->company_details();
	$data = $this->login_model->customer_data($this->session->userdata('username'));

	//echo "<pre>";print_r($data);echo "</pre>";exit;

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/pages/referral.css">


<div id="referral" class="referral-box" style="float:right;">



	<div id="referral-form">



		<div id="content">


			<div align="center"><i class="fa fa-users fa-3x"></i></br><label style="color:#2dbe60">	
			INVITE YOUR FRIENDS</label></div>
			
			
			<div align="center">
			
			<ul>
				<li>Invite your friends to join saving </li>				
				<li>scheme with  <?php echo $company_details['company_name'];?>.</li> 
			    <li>Get credits in your wallet.</li>
				<h5><strong> Referral Code &nbsp;&nbsp;<?php echo ($this->session->userdata('username'));?></strong></h5>
			</div>
			
			<div class="errText"></div>
			<div class="success_Text"></div>
			
			<div><input type="text" placeholder="Enter the Mobile no or mail id " class="form-control marginBottom10" name="referral_val" id="referral_value">
			<input type="hidden" class="form-control marginBottom10" name="referral_by" id="referral_by">
			<input type="hidden" class="form-control marginBottom10"  id="senduser_mobi" value="<?php echo $this->session->userdata('username'); ?>">
			<input type="hidden" class="form-control marginBottom10" id="senduser_mail" value="<?php echo $data['email'];?>">
			
			</div>
			

			<div style="text-align: -webkit-center;">	
				<label><button id="ref_mbi"><i class="fa fa-mobile fa fa-lg"></i>
				</button ></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			
				<label><button id="ref_mail"><i class="fa fa-envelope-o fa-lg"></i></button></label>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			
				<!--<label><button id="ref_mbi"><i class="fa fa-whatsapp fa-lg"></i></button></label>			
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->			
			</div>
			<div></div>
			<div></div>
		</div>



	</div>



	<a title="Click to leave feedback" class="refpull_feedback"  href="#"><span id="tab-text">Invite</span></a>



	

</div>