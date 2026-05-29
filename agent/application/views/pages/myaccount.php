

<style>
.account-settings .user-profile {
    margin: 0 0 1rem 0;
    padding-bottom: 1rem;
    text-align: center;
}
.account-settings .user-profile .user-avatar {
    margin: 0 0 1rem 0;
}
.account-settings .user-profile .user-avatar img {
    width: 90px;
    height: 90px;
    -webkit-border-radius: 100px;
    -moz-border-radius: 100px;
    border-radius: 100px;
}
.account-settings .user-profile h5.user-name {
    margin: 0 0 0.5rem 0;
}
.account-settings .user-profile h6.user-email {
    margin: 0;
    font-size: 0.8rem;
    font-weight: 400;
  /*  color: #9fa8b9;   */
}
.account-settings .about {
    margin: 2rem 0 0 0;
    text-align: center;
	margin-top: -28px;
}
.account-settings .about h5 {
    margin: 0 0 15px 0;
  /*  color: #007ae1;   */
  margin: 0 0 15px 0;
    color: #8C1F48;
    font-size: 18px;
    font-weight: bold;
}
.account-settings .about p {
    font-size: 0.825rem;
}
.form-control {
    border: 1px solid #cfd1d8;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    font-size: .825rem;
    background: #ffffff;
  /*  color: #2e323c;   */
}

.card {
    background: #ffffff;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    border: 0;
    margin-bottom: 1rem;
}
.col-xl-3 .col-lg-3 .col-md-12 .col-sm-12 .col-12{
    margin-top:50px;
}


.user-avatar{
        padding-top: 50px;
   /* border-radius: 50px; */
}
.user-contact h6{font-size: 12px;}
.refhead{background-color: #8C1F48;
    border: 1px;
    color: #fff;
    font-size: 15px;
    font-weight: bold;}
.refcode{height: 50px;
    font-size: 18px;
    color: #8C1F48;
    font-weight:600;
}
.text-primary{color: #8C1F48;
    font-size: 15px;font-weight: bold;}	
.form-control {font-size: 12px;
    box-shadow: none !important;
    height: auto;
    border-color: #aaaaaa;}
.form-control .box input{height:27px;box-shadow:none;}
.form-control .box select{width:100% !important;}
.initial{height: 27px;border-color: #aaaaaa;border-radius: 3px;margin-left: 10;margin-left: 2px;    width: 45px;}   


/* tab css */
 #carrier_wizard ul.anchor.nbr_steps_4 li,  #carrier_wizard ul.anchor.nbr_steps_5 li {
    float: left;
    width: 25%;
}
 #carrier_wizard li {
    display: block;
    overflow: hidden;
    position: relative;
}
 *,  :after,  :before {
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
li {
    display: list-item;
    text-align: -webkit-match-parent;
}
 #carrier_wizard ul.anchor {
    float: left;
    list-style: none;
    margin: 0 0 10px;
    padding: 0;
    width: 100%;
}
 #carrier_wizard ul.anchor {
    float: left;
    list-style: none;
    margin: 0 0 10px;
    padding: 0;
    width: 100%;
}
#carrier_wizard ul.anchor {
    clear: both;
    display: block;
    position: relative;
}
 #carrier_wizard li a.unselected {
   /* background-color: #dfabbf;*/
    border-top: 1px solid #8C1F48;
    border-bottom: 1px solid #8C1F48;
    border-left: 1px solid #8C1F48;
    border-right: 1px solid #8C1F48;
    color: #8C1F48;
    cursor: text;
    border-radius: 5px 0px 0px 5px;
	line-height:2.5;
}

 #carrier_wizard li a.selected {
    background-color: #8C1F48;
    color: #ffffff;
    cursor: text;
    border-radius: 5px 0px 0px 5px;
	line-height:2.5;
}
 #carrier_wizard li a {
    color: #ccc;
    display: block;
    height: 32px;
    margin: 0 16px 0 0;
    outline-style: none;
    position: relative;
    text-decoration: none;
}
 #carrier_wizard li a .stepDesc {
    display: table-cell;
    font-size: 13px;
    height: 32px;
    line-height: 13px;
    position: relative;
    text-align: left;
    vertical-align: middle;
}

 #carrier_wizard li a .chevron {
    border: 16px solid transparent;
    border-left: 14px solid #fff;
    border-right: 0;
    position: absolute;
    right: -16px;
    top: 0;
}
 #carrier_wizard li a.unselected .chevron:after {
    border-left: 14px solid #8C1F48;
}
 #carrier_wizard li a.selected .chevron:after {
    border-left: 14px solid #8C1F48;
}
 #carrier_wizard li a .chevron:after {
    border: 16px solid transparent;
    border-left: 14px solid #ccc;
    border-right: 0;
    content: "";
    position: absolute;
    right: 2px;
    top: -16px;
}
.stepNumber{
    text-align: center;
    font-size: 13px;
    font-weight:600;
}
.select2-container{width:100% !important}
</style>
<div class="col-md-12">							
	<div align="center"><legend class="head" >MY ACCOUNT</legend></div>
			 <!-- alert -->
	<div class="row">
        <div class="col-md-12 col-xs-12">
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
<div class="container">
	<div class="row gutters">
		<form method="post" action="updateMyAccount" enctype="multipart/form-data">
			<input type="hidden" class="form-control box" id="rf-agentid" name="agentid" value="<?php echo $agent['id_agent']; ?>">
			<div class="col-xs-12 col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
				<div class="card h-100">
					<div class="card-body">
						<div class="account-settings">
							<div class="user-profile">
								<div class="user-avatar">
									<div class="image-upload">
										<label for="file-input">
											<img id="previewImg"  src="<?php echo (isset($agent['image'])?base_url('assets/img/agent/'.$agent['image']):('assets/img/default.png')); ?>" style="width: 130px; height: 130px;margin-top: -50px;" />
										</label> 
										<div class="filebtn">
											<input id="file-input" type="file" name="agentimage" onchange="previewFile(this);" style="display:none;"/>
										</div>	
									</div>
								</div>
							</div>
							<div class="about">
								<h5 class="user-name"><?php echo $agent['firstname']." ".$agent['lastname']; ?></h5>
								<h6 class="user-contact"><?php echo $agent['email']; ?></h6>
								<!--<h6 class="user-contact"><?php echo $agent['mobile']; ?></h6>
								<div class="refhead">Refferal Code</div>-->
								<div class="refcode">Refferal Code : <?php echo $agent['agent_code']; ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
				<div class="card h-100">
					<div class="card-body">
						<div id="carrier_wizard" class="panel swMain text-center">
							<ul class="steps nbr_steps_4 anchor">
								<li id="pro_1">
									<a href="#" id="pro1" class = "selected">
										<span class="stepNumber">Personal Details</span>
										<span class="chevron"></span>
									</a>
								</li>
								<li id="pro_2">
									<a href="#" id="pro2"  class = "unselected">
										<span class="stepNumber">Contact & Address</span>
										<span class="chevron"></span>
									</a>
								</li>
								<li id="pro_3">
									<a href="#" id="pro3"  class = "unselected">
										<span class="stepNumber">Bank Details</span>
										<span class="chevron"></span>
									</a>
								</li>
								<li id="pro_4">
									<a href="#" id="pro4"  class = "unselected">
										<span class="stepNumber">Social Media</span>
										<span class="chevron"></span>
									</a>
								</li>
							</ul>
						</div>
						<!-- Personal Details  --->
						<div class="row gutters" id="personal" style="display:block;">
							<div class="col-xs-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mb-2 text-primary">Personal Details</h6>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="fullName"  style="display: inline-block;max-width: 100%;margin-bottom: 5px;font-weight: 700;width: 450px;">Initials & First Name</label>
									<select name="title" class="initial" value="<?php echo $agent['title']; ?>" style ="height: 27px;border-color: #aaaaaa;border-radius: 3px;margin-left: 10;margin-left: 2px;width: 45px;">
										<option value="Mr" <?php if($agent['title'] == 'Mr'){ ?> selected="selected" <?php }?> >Mr</option>
										<option value="Ms" <?php if($agent['title'] == 'Ms'){ ?> selected="selected" <?php }?> >Ms</option>
										<option value="Mrs" <?php if($agent['title'] == 'Mrs'){ ?> selected="selected" <?php }?> >Mrs</option>
										<option value="Dr" <?php if($agent['title'] == 'Dr'){ ?> selected="selected" <?php }?> >Dr</option>
										<option value="Prof" <?php if($agent['title'] == 'Prof'){ ?> selected="selected" <?php }?> >Prof</option>
									</select>
									<input type="text" class="form-control box fullname" name="firstname" id="firstname" placeholder="Enter first name" value="<?php echo $agent['firstname']; ?>" required>
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="eMail">Last Name</label>
									<input type="text" class="form-control box" name="lastname" id="lastname" placeholder="Enter last name" value="<?php echo $agent['lastname']; ?>" >
								</div>
							</div>
							<!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
							<div class="form-group">
							<label for="phone">Gender</label>
							<input type="radio" id="gender-M" name="gender" value="0" class="minimal" checked="">Male
							<input type="radio" id="gender-F" name="gender" value="1"  class="minimal" checked="">Female
							<input type="radio" id="gender-F" name="gender" value="3"  class="minimal" checked="">Others
							</div>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
							<div class="form-group">
							<label for="email">Preferred Payment Mode</label>
							<input type="email" class="form-control box" name="mail" id="mail" placeholder="Enter e-mail id" value="<?php echo $agent['email']; ?>" disabled>
							</div>
							</div> -->
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="dob">Date of Birth</label>
									<input type="date" class="form-control box" name="bday" id="bday" placeholder="Enter date of birth" value="<?php echo $agent['date_of_birth']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="dow">Date of Wedding</label>
									<input type="date" class="form-control box" name="wed" id="wed" placeholder="Enter date of wedding" value="<?php echo $agent['date_of_wed']; ?>">
								</div>
							</div>
						</div>
						<!-- Personal Details  --->
						<!-- address Line  -->
						<div class="row gutters" id="address" style="display:none;">
							<div class="col-xs-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mt-3 mb-2 text-primary">Contact & Address</h6>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="phone">Phone</label>
									<input type="number" class="form-control box" name="phone" id="phone" placeholder="Enter phone number" value="<?php echo $agent['mobile']; ?>" disabled>
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="email">Email</label>
									<input type="email" class="form-control box" name="mail" id="mail" placeholder="Enter e-mail id" value="<?php echo $agent['email']; ?>" disabled>
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="Street">Address Line 1</label>
									<input type="text" class="form-control box" id="address1" name="address1" placeholder="Enter Street" value="<?php echo $agent['address1']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="Street">Address Line 2</label>
									<input type="text" class="form-control box" id="address2" name="address2" placeholder="Enter Street" value="<?php echo $agent['address2']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="ciTy">Country</label>
									<input  type="hidden" id="countryval" name="countryval" value="<?php echo set_value('countryval', $agent['id_country']); ?>"/>
									<select  class="form-control box" id="id_country" name="id_country" ></select>
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="sTate">State</label>
									<input  type="hidden" id="stateval" name="stateval" value="<?php echo set_value('stateval',$agent['id_state']); ?>"/>
									<select id="id_state" name="id_state" class="form-control box"></select>
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="ciTy">City</label>
									<input  type="hidden" id="cityval" name="cityval" value="<?php echo set_value('cityval',$agent['id_city']); ?>"/>
									<select  id="id_city" name="id_city" class="form-control box"></select>
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="zIp">Zip Code</label>
									<input type="text" class="form-control box" id="pincode"  name="pincode" placeholder="Zip Code" value="<?php echo $agent['pincode']; ?>" >
								</div>
							</div>
						</div>
						<!-- address Line  -->
						<!-- Social media -->
						<div class="row gutters" id="bank" style="display:none;">
							<div class="col-xs-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mt-3 mb-2 text-primary">Bank Account Details</h6>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="AccNo">Bank Account No</label>
									<input type="number" class="form-control box" id="bankAccNo" name="bankAccNo" placeholder="Enter Bank Account Number " value="<?php echo $agent['bank_account_number']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="Ifsc">IFSC Code</label>
									<input type="text" class="form-control box" id="ifsc"  name="ifsc" placeholder="Enter IFSC Code" value="<?php echo $agent['ifsc_code']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="ciTy">Bank Name</label>
									<input type="text" class="form-control box" id="bankName" name="bankName" placeholder="Enter Bank Name" value="<?php echo $agent['bank_name']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="sTate">Account Name</label>
									<input type="text" class="form-control box" id="bankAccName" name="bankAccName" placeholder="Enter Account Holder's Name" value="<?php echo $agent['bank_acc_holder_name']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="mode">Preferred Mode</label>
									<select id="preferred_mode" name="preferred_mode" class="form-control box" value="<?php echo $agent['preferred_mode']; ?>" >
									<option value="1" <?php if($agent['preferred_mode'] == '1'){ ?> selected="selected" <?php }?> >CASH</option>
									<option value="2" <?php if($agent['preferred_mode'] == '2'){ ?> selected="selected" <?php }?> >ONLINE</option>
									</select>
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label>Upload Bank Statement / Cancelled Cheque</label>
									<label for="bankFile">
										<input id="bank-file-input" type="file" name="bankFile" onchange="" style=""/>
										<input type="button" id="viewBankFile" value="View File" class="file-button" onchange="previewFileBank(this);">
										<i class="fa fa-close" style="margin-left: 270px;display:none;margin-top: -20px;" id="bank_img_close"></i>
										<img id="bank-file" src="<?php echo (isset($agent['bank_image'])?base_url('assets/img/agent/bank/'.$agent['bank_image']):base_url('assets/img/agentBankDefault.jpeg')); ?>"  style="width: 300px; height: 300px;margin-top: 10px; display:none;" />
									</label> 
								</div>
							</div>
						</div>
						<!-- Social media -->
						<!-- Social media -->
						<div class="row gutters" id="media" style="display:none;">
							<div class="col-xs-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mt-3 mb-2 text-primary">Social Media Links</h6>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="Street">Website URL</label>
									<input type="name" class="form-control box" id="webLink" name="webLink" placeholder="Enter Website URL" value="<?php echo $agent['website_url']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="zIp">Facebook</label>
									<input type="text" class="form-control box" id="fbLink"  name="fbLink" placeholder="Enter Facebook URL" value="<?php echo $agent['facebook_url']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="ciTy">Instagram</label>
									<input type="name" class="form-control box" id="igLink" name="igLink" placeholder="Enter Instagram URL" value="<?php echo $agent['instagram_url']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="sTate">Twitter</label>
									<input type="text" class="form-control box" id="twLink" name="twLink" placeholder="Enter Twitter URL" value="<?php echo $agent['twitter_url']; ?>">
								</div>
							</div>
							<div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="zIp">Youtube</label>
									<input type="text" class="form-control box" id="ytLink" name="ytLink" placeholder="Enter Youtube URL" value="<?php echo $agent['youtube_url']; ?>">
								</div>
							</div>
						</div>
						<!-- Social media -->
						</br>
						<!--- form buttons -->
						<div class="row gutters">
							<div class="col-xs-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<div class="text-center">
									<button type="button" id="submit" name="submit" class="btn btn-secondary">Cancel</button>
									<button type="submit" id="submit" name="submit" class="btn" style="background-color: #8C1F48;color:#ffffff">Update</button>
								</div>
							</div>
						</div>
					<!--- form buttons -->
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>

<script>


$("#pro_1").on('click',function(event) {
    $("#personal").css('display','block');
    $("#address,#bank,#media").css('display','none');
    $("#pro1").removeClass('unselected');
    $("#pro1").addClass('selected');
    $("#pro2,#pro3,#pro4").removeClass('selected');
    $("#pro2,#pro3,#pro4").addClass('unselected');

});
$("#pro_2").on('click',function(event) {
    $("#address").css('display','block');
    $("#personal,#bank,#media").css('display','none');
    $("#pro2").removeClass('unselected');
    $("#pro2").addClass('selected');
    $("#pro1,#pro3,#pro4").removeClass('selected');
    $("#pro1,#pro3,#pro4").addClass('unselected');

});
$("#pro_3").on('click',function(event) {
    $("#bank").css('display','block');
    $("#address,#personal,#media").css('display','none');
    $("#pro3").removeClass('unselected');
    $("#pro3").addClass('selected');
    $("#pro1,#pro2,#pro4").removeClass('selected');
    $("#pro1,#pro2,#pro4").addClass('unselected');

});
$("#pro_4").on('click',function(event) {
    $("#media").css('display','block');
    $("#address,#bank,#personal").css('display','none');
    $("#pro4").removeClass('unselected');
    $("#pro4").addClass('selected');
    $("#pro1,#pro2,#pro3").removeClass('selected');
    $("#pro1,#pro2,#pro3").addClass('unselected');
});
	
	
        function previewFile(input){
            var file = $("input[type=file]").get(0).files[0];

            if(file){
              var reader = new FileReader();

              reader.onload = function(){
                  $("#previewImg").attr("src", reader.result);
              }

              reader.readAsDataURL(file);
            }
        }
        
        function previewFileBank(input){
            var file = $("input[type=file]").get(0).files[0];

            if(file){
              var reader = new FileReader();

              reader.onload = function(){
                  $("#bank-file").attr("src", reader.result);
              }

              reader.readAsDataURL(file);
            }
        }

    $("#viewBankFile").on('click',function(event) {

		$("#bank-file").css('display','block');
		$("#bank_img_close").css('display','block');
		$("#viewBankFile").css('display','none');

	});
	 $("#bank_img_close").on('click',function(event) {

		$("#bank-file").css('display','none');
		$("#bank_img_close").css('display','none');
		$("#viewBankFile").css('display','block');

	});
    </script>
<script src="<?php echo base_url() ?>assets/js/pages/profile.js"></script>