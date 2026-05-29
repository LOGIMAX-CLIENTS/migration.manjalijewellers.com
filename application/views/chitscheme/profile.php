<html>
<style>
#nominee{
    visibility: hidden;
    min-width: 250px;
    margin-left: -125px;
    background-color: yellow;
    color: #000;
    text-align: center;
    border-radius: 10px;
    padding: 16px;
     position:absolute;
    z-index: 1;
    left: 50%;
    bottom: 30px;
    font-size: 17px;
}
#nominee.show {
    visibility: visible;
    -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
    animation: fadein 0.10s, fadeout 0.10s 5.5s;
}
#n_Relationship {
    visibility: hidden;
    min-width: 250px;
    margin-left: -125px;
    background-color: yellow;
    color: #000;
    text-align: center;
    border-radius: 10px;
    padding: 16px;
     position:absolute;
    z-index: 1;
    left: 50%;
    bottom: 30px;
    font-size: 17px;
}
#n_Relationship.show {
    visibility: visible;
    -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
    animation: fadein 0.10s, fadeout 0.10s 5.5s;
}
</style>
<body>
<link href="<?php echo base_url() ?>assets/css/pages/profile.css" rel="stylesheet" type="text/css">
<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<div class="main-container">
	<!-- main -->		  
	<div class="main">
		<!-- main-inner --> 
		<div class="main-inner" id="edit">
			<!-- container --> 
			<div class="container dashboard">
				<!-- alert -->
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<div align="center"><legend class="head">PROFILE</legend></div>
						<div align="right">
							<a class="formHead" href="<?php echo base_url() ?>index.php/user/reset_passwd"><span>Click to RESET PASSWORD</span> </a>
						</div>
						<?php
						if($this->session->flashdata('successMsg')) { ?>
						<div class="alert alert-success" align="center">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
						</div>      
						<?php } if($this->session->flashdata('errMsg')) { ?>							 
						<div class="alert alert-danger" align="center">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
						</div>
						<?php } ?>
					</div>		
					<!-- /alert -->  
				</div>
				<?php 
				$attributes 		=	array('id' => 'registerForm', 'name' => 'registerForm');
				echo form_open_multipart('user/DB_controller/update/'.$profile[0]['id_customer'],$attributes);  
				?>
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<form id="edit_profile">
							<div class="widget">
								<img id="spinner" src="<?php echo base_url()?>assets/img/spinner.gif" style="display:none">
								<div class="widget-content">
									<div class="row" >
										<div class="col-md-12 col-xs-12">
											<div class="row">
												<legend class="formHead">Basic Information</legend>    
												<div class="col-md-3" align="center">
													<?php 
													if (@getimagesize(base_url().'admin/assets/img/customer/'.$profile[0]['id_customer'].'/customer.jpg')) 							{
													echo '<span style="display:none" id="cusImg_text">UPLOAD YOUR IMAGE</span><img class="img-thumbnail marginBottom15" id="cusImg_preview" src="'.base_url().'admin/assets/img/customer/'.$profile[0]['id_customer'].'/customer.jpg"><button class="btn btn-small " title="Remove Image" id="customer">&times;</button>';
													}else {
													echo '<img class="img-thumbnail marginBottom15" id="cusImg_preview" src="'.base_url().'admin/assets/img/default.png">';
													}
													?>
													<input type="hidden" name="cus_img_edit" id="cus_img_edit"  style="width:304px" />
													<label for="cus_img"></label>
													<input type="file" id="cus_img" name="cus_img"  placeholder="Upload proof" class="profile marginBottom15" />	
												</div>
												<div class="col-md-4 col-xs-12">
													<div class="col-md-12 col-xs-12 profile_col row marginBottom15">		
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>First Name<span class="error"> * </span> </b></label>
														<div class="col-md-2 col-xs-2">
															<?php if($profile[0]['is_cus_synced']!=1) {	?>
															<select name="title"  style="height: 33px; border-radius: 3px;">
																<option value="none" selected disabled hidden> 
																<?php echo $profile[0]['title']; ?> </option>

																<option value="Mr">Mr</option>
																<option value="Ms">Ms</option>
																<option value="Mrs">Mrs</option>
																<option value="Dr">Dr</option>
																<option value="Prof">Prof</option>
															</select>
															<?php }else{?>
															<select name="title" disabled="disabled" style="">
																<option value="none" selected disabled hidden> 
																<?php echo $profile[0]['title']; ?> </option>

																<option value="Mr">Mr</option>
																<option value="Ms">Ms</option>
																<option value="Mrs">Mrs</option>
																<option value="Dr">Dr</option>
																<option value="Prof">Prof</option>
															</select>
															<?php } ?>
														</div>
														<div class="col-md-6 col-xs-6 " style="">
														<?php if($profile[0]['is_cus_synced']!=1) {	?>
														<input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $profile[0]['firstname']; ?>" placeholder="First Name" required="true">
														<?php }else{?>
														<input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $profile[0]['firstname']; ?>" placeholder="First Name"  readonly="true" required="true">
														<?php } ?>
														<!--<input type="hidden" class="form-control" id="profile_edit"  value="<?php echo set_value('profile[0][profile_edit]',$profile[0]['profile_edit']); ?>" />-->

														</div> <!-- /controls -->		
													</div> 
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="lasttname"><b>Last Name <span class="error">  *</span></b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<?php if($profile[0]['is_cus_synced']!=1) {	?>
															<input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $profile[0]['lastname']; ?>" placeholder="Last Name" required/>
															<?php }else{?>	
															<input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $profile[0]['lastname']; ?>" placeholder="Last Name" readonly="true" required/>
															<?php } ?> 
														</div> <!-- /controls -->	
													</div> 
													<div class="col-md-12 col-xs-12 row marginBottom15">					
														<label class="col-md-3 col-xs-3 profile_col"><b>Gender</b></label>
														<div class="col-md-9 col-xs-9 profile_col">
															<label class="marginLeft15">
																<input type="radio" id="gender-M" name="gender" value="0" class="minimal" <?php if($profile[0]['gender'] == 0){ ?> checked <?php } ?> > Male
															</label>
															<label class="marginLeft15">
																<input type="radio"  id="gender-F" name="gender" value="1" class="minimal" <?php if($profile[0]['gender'] == 1){ ?> checked <?php } ?>> Female
															</label>
															<label class="marginLeft15">
																<input type="radio" id="gender-o" name="gender" value="3" class="minimal" <?php if($profile[0]['gender'] == 3){ ?> checked <?php } ?>> Others 
															</label>
														</div>	<!-- /controls -->		
													</div> <!-- /control-group -->
												</div>

												<div class="col-md-4 col-xs-12" >
													<div class="col-md-12 col-xs-12 row marginBottom15">				
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"><b>Date of Birth </b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<input data-provide="datepicker"  type="text" id="date_of_birth" name="date_of_birth" value="<?php echo $profile[0]['date_of_birth']; ?>" placeholder="Date of Birth" class="form-control"  />
														</div> <!-- /controls -->				
													</div>
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"><b>Wedding Day</b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<input data-provide="datepicker"  type="text" id="date_of_wed" name="date_of_wed" value="<?php echo $profile[0]['date_of_wed']; ?>" placeholder="Wedding day" class="form-control"  />
														</div> <!-- /controls -->				
													</div> 
												</div>
											</div>
											<!-- Referal link  send     -->	

											<?php  $data = $this->services_modal->get_data();
											if($data['allow_referral']==1) {?>		
											<legend class="formHead">Invite Referrals </legend>
											<div class="row">
												<div class="col-sm-4 col-xs-12 col-sm-offset-2">
												<div id="error-msg"></div>
												</div>      		
											</div>
											<div class="row">
												<div class="col-md-offset-1 col-md-4 col-xs-12" >
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>Mobile<span class="error"> * </span> </b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<input type="number" id="mobileno" name="mobileno" placeholder="Mobile No" class="form-control"/>
														</div> 			
													</div>
												</div>
												<div class="col-md-5 col-xs-12" >
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-3 col-xs-4 profile_col"><b>Email<span class="error"> * </span> </b></label>
														<div class="col-md-9 col-xs-8 profile_col">
															<input type="email" id="emailid" name="emailid"   placeholder="E-mail Address" class="form-control" />
														</div> <!-- /controls -->				
													</div>
												</div>
												<div class="col-md-2 col-xs-12" >
													<div class="col-md-12 col-xs-12 row marginBottom15">					
														<div class="col-md-8 col-xs-12ssss">
															<input type="button" value="Send" id="referallinksend" class="button btn btn-primary btn-large"/>
														</div> 
													</div>
												</div> 
											</div> 
											<?php }?> 	 
											<!-- Referal link send     -->	
											<div class="row">
												<legend class="formHead">Contact & Address</legend> 
												<div class="col-md-4 col-xs-12" >
													<div class="col-md-12 col-xs-12 row marginBottom15">	
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>Mobile<span class="error"> * </span> </b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<input disabled="disabled" type="number" id="mobile" name="mobile" value="<?php echo $profile[0]['mobile']; ?>" placeholder="Mobile No" class="form-control" readonly="true" required />
														</div> <!-- /controls -->				
													</div> 
													<div class="col-md-12 col-xs-12 row marginBottom15">		
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>Email<span class="error"> * </span> </b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<input type="email" id="email" name="email"  value="<?php echo $profile[0]['email']; ?>" placeholder="E-mail Address" class="form-control" required />
														</div> <!-- /controls -->				
													</div> 
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>Pincode</b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<input id="pincode" name="pincode" value="<?php echo $profile[0]['pincode']; ?>" placeholder="Pincode" class="form-control" />
														</div> <!-- /controls -->				
													</div>
												</div>  
												<div class="col-md-4 col-xs-12" >
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>Address 1 </b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<?php if($profile[0]['is_cus_synced']!=1) {	?>
															<input id="address1" name="address1" placeholder="Enter Address" class="form-control" value="<?php echo $profile[0]['address1']; ?>"  / >
															<?php }else{?>
															<input id="address1" name="address1" placeholder="Enter Address" class="form-control" readonly="true" value="<?php echo $profile[0]['address1']; ?>"  / >
															<?php } ?>
														</div> <!-- /controls -->				
													</div> 
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>Address 2</b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<?php if($profile[0]['is_cus_synced']!=1) {	?>
															<input id="address2" name="address2" placeholder="Enter Address" class="form-control" value="<?php echo $profile[0]['address2']; ?>"  / >
															<?php }else{?>
															<input id="address2" name="address2" placeholder="Enter Address" class="form-control" readonly="true" value="<?php echo $profile[0]['address2']; ?>"  / >
															<?php } ?>
														</div> <!-- /controls -->				
													</div>
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>Address 3</b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<?php if($profile[0]['is_cus_synced']!=1) {	?>
															<input id="address3" name="address3" placeholder="Enter Address" class="form-control" value="<?php echo $profile[0]['address3']; ?>"  / >
															<?php }else{?>
															<input id="address3" name="address3" placeholder="Enter Address" class="form-control" readonly="true" value="<?php echo $profile[0]['address3']; ?>"  / >
															<?php } ?>
														</div> <!-- /controls -->				
													</div>
												</div>
												<div class="col-md-4 col-xs-12" >	
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>Country </b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<?php if($profile[0]['is_cus_synced']!=1) {?>
															<input  type="hidden" id="countryval" name="countryval" value="<?php echo set_value('countryval', $profile[0]['id_country']); ?>"/>
															<select  class="form-control" id="id_country" name="id_country" >
															</select>
															<?php }else{?>
															<input  type="hidden" id="countryval" name="countryval"  value="<?php echo set_value('countryval', $profile[0]['id_country']); ?>"/>
															<select  class="form-control" id="id_country" name="id_country" disabled="disabled" >
															</select>
															<?php } ?>
														</div> <!-- /controls -->				
													</div> 
													<div class="col-md-12 col-xs-12 row marginBottom15">		
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>State</b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<?php if($profile[0]['is_cus_synced']!=1) {?>
															<input  type="hidden" id="stateval" name="stateval" value="<?php echo set_value('stateval',$profile[0]['id_state']); ?>"/>
															<select id="id_state" name="id_state" class="form-control">
															</select>
															<?php }else{?>
															<input  type="hidden" id="stateval" name="stateval" value="<?php echo set_value('stateval',$profile[0]['id_state']); ?>"/>
															<select id="id_state" name="id_state" class="form-control" disabled="disabled">
															</select>
															<?php } ?>
														</div> <!-- /controls -->				
													</div> 
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-4 col-xs-4 profile_col" for="firstname"> <b>City</b></label>
														<div class="col-md-8 col-xs-8 profile_col">
															<?php if($profile[0]['is_cus_synced']!=1) {?>
															<input  type="hidden" id="cityval" name="cityval" value="<?php echo set_value('id_cityval',$profile[0]['id_city']); ?>"/>
															<select  id="id_city" name="id_city" class="form-control">
															</select>
															<?php }else{?>
															<input  type="hidden" id="cityval" name="cityval" value="<?php echo set_value('id_cityval',$profile[0]['id_city']); ?>"/>
															<select  id="id_city" name="id_city" class="form-control" disabled="disabled">
															</select>
															<?php } ?>
														</div> <!-- /controls -->				
													</div> 
												</div> 
											</div>
											<div class="row">
												<legend class="formHead">Nominee Details</legend> 
												<div class="col-md-4 col-xs-12" >	
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<div class="col-md-5 col-xs-4 profile_col">
															<label> <b>Nominee</b></label>&nbsp;&nbsp;
															<i class="icon-info" title=" Nominee is the person nominated by the account holder, who can purchase on scheme maturity in the case of absence of account holder" rel="tooltip"></i>
														</div>
														<div class="col-md-7 col-xs-8 profile_col">
															<input type="text" id="nominee_name"  name="nominee_name" value="<?php echo $profile[0]['nominee_name']; ?>" placeholder="Nominee Name" class="form-control" />
														<!--<div id="n_Relationship">haiii..</div>-->
														</div> <!-- /controls -->				
													</div> 
												</div>
												<div class="col-md-4 col-xs-12" >	
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<div class="col-md-5 col-xs-4 profile_col">
															<label> <b>Nominee Mobile</b></label>&nbsp;&nbsp;
															<i class="" title=" Nominee is the person nominated by the account holder, who can purchase on scheme maturity in the case of absence of account holder" rel="tooltip"></i>
														</div>
														<div class="col-md-7 col-xs-8 profile_col">
															<input type="text" id="nominee_mobile"  name="nominee_mobile" value="<?php echo $profile[0]['nominee_mobile']; ?>" placeholder="Nominee Mobile" class="form-control" />
														<!--<div id="n_Relationship">haiii hari..</div>-->
														</div> <!-- /controls -->	
													</div> 
												</div>
												<div class="col-md-4 col-xs-12" >	 
													<div class="col-md-12 col-xs-12 row marginBottom15">		
														<div class="col-md-5 col-xs-4 profile_col">
															<label> <b>Relationship</b></label>&nbsp;&nbsp;
															<i class="icon-info" title="Relationship between account holder and the nominee person" rel="tooltip"></i>
														</div>
														<div class="col-md-7 col-xs-8 profile_col">
															<input type="text" id="nominee_relationship" name="nominee_relationship" value="<?php echo $profile[0]['nominee_relationship']; ?>" placeholder="Nominee Relationship" class="form-control" />
														<!--<div id="n_Relationship">haiii..</div>-->
														</div> <!-- /controls -->				
													</div> 
												</div>
											</div> 
											<div class="row">
												<legend class="formHead">Proof Details</legend> 
												<div class="col-md-4 col-xs-12" >	 
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-5 col-xs-4 profile_col" for="firstname"> <b>Pan No.</b></label>
														<div class="col-md-7 col-xs-8 profile_col">
															<input type="text" id="pan_no" name="pan" value="<?php echo $profile[0]['pan']; ?>" placeholder="Pan Number" class="form-control"/>
														</div> <!-- /controls -->				
													</div> 
												</div>
												<div class="col-md-4 col-xs-12" >	 
													<div class="col-md-12 col-xs-12 row marginBottom15">
														<label class="col-md-5 col-xs-4 profile_col" for="firstname"> <b>Upload Proof</b></label>
														<div class="col-md-7 col-xs-8 profile_col">
															<select id="proof_list" class="form-control">
															<option>--Select Proof to upload--</option>
															<option value="1">Pan Card</option>
															<option value="2">VoterId Card</option>
															<option value="3">Ration Card</option>
															</select>
														</div> <!-- /controls -->
													</div> 
												</div> 
												<div class="col-md-4 col-xs-12" >	 
													<div class="col-md-12 col-xs-12">	
														<div class="controls">
														<div id="uploadArea"></div>
														</div> <!-- /controls -->				
													</div> 
												</div>
											</div>
											<div class="row">	
												<?php 
												if (@getimagesize(base_url().'admin/assets/img/customer/'.$profile[0]['id_customer'].'/pan.jpg')) {

												echo '  <div class="col-md-4" ><div class="control-group">	';

												echo '<span id="Img_text" class="control-label">PAN PROOF</span><img class="img-thumbnail" id="panImg_preview"  src="'.base_url().'admin/assets/img/customer/'.$profile[0]['id_customer'].'/pan.jpg" ><button class="btn btn-small " title="Remove" id="pan">&times;</button></div></div>';
												}
												else {
												echo '  <div class="col-md-4" ><div class="control-group">	';

												echo '<span id="pan_Img_text" style="display:none">PANPROOF</span><img class="img-thumbnail" style="display:none" id="panImg_preview" src="" ></div></div>';
												}
												if (@getimagesize(base_url().'admin/assets/img/customer/'.$profile[0]['id_customer'].'/voterid.jpg')) {	
												echo '  <div class="col-md-4" ><div class="control-group">';

												echo '<span id="Img_text">VOTER ID PROOF</span><img class="img-thumbnail" id="VIImg_preview"  src="'.base_url().'admin/assets/img/customer/'.$profile[0]['id_customer'].'/voterid.jpg"><button class="btn btn-small " title="Remove" id="voterid">&times;</button></div></div>';
												}
												else {

												echo ' <div class="col-md-4" >	 <div class="control-group">';

												echo '<span id="VI_Img_text" style="display:none">VOTERID PROOF</span><img class="img-thumbnail" style="display:none" id="VIImg_preview" src="" ></div></div>';
												}
												if (@getimagesize(base_url().'admin/assets/img/customer/'.$profile[0]['id_customer'].'/rationcard.jpg')) {
												echo '  <div class="col-md-4" ><div class="control-group">';
												echo '<span id="Img_text" >RATION CARD PROOF</span><img class="img-thumbnail" id="RCImg_preview"  src="'.base_url().'admin/assets/img/customer/'.$profile[0]['id_customer'].'/rationcard.jpg" ><button class="btn btn-small " title="Remove" id="rationcard">&times;</button></div></div>';
												}
												else {
												echo '  <div class="col-md-4" ><div class="control-group">';
												echo '<span id="RC_Img_text" style="display:none">RATIONCARD PROOF</span><img class="img-thumbnail" style="display:none" id="RCImg_preview" src=""></div></div>';
												}
												?>
											</div>
										</div>
									</div>
									<div align="center">
										<legend style="padding-top:15px"></legend>
										<input type="submit" value="Update" id="registerSubmit" class="button btn btn-primary btn-large"/>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>	
			</div>
			<!-- /container --> 
		</div>
		<!-- /main-inner --> 
	</div>
	<!-- /main -->		  
</div>
<br />
<br />
<br />
<script type="text/javascript">
	IDEmail   = "<?php echo $profile[0]['email']; ?>";
	ISpanreq   = "<?php echo $profile[0]['ispan_req']; ?>";
	function myFunction() {
    var x = document.getElementById("nominee")
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); },5000);
} 
 function myFunction1() {
    var x = document.getElementById("n_Relationship")
    x.className = "show";
    setTimeout(function(){ x.className = x.className.replace("show", ""); },5000);
} 

</script>

</body>
</html>

