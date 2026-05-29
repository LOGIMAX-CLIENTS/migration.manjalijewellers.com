      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
      	<!-- Content Header (Page header) -->
      	<section class="content-header">
      		<h1>
      			Settings
      			<small>General Settings</small>
      		</h1>
      		<ol class="breadcrumb">
      			<li><a href="#"><i class="fa fa-dashboard"></i> settings</a></li>
      			<li class="active">General settings</li>
      		</ol>
      	</section>
      	<!-- Main content -->
      	<section class="content">
      		<!-- Default box -->
      		<div class="box">
      			<div class="box-header with-border">
      				<h3 class="box-title">General Settings</h3>
      				<div class="box-tools pull-right">
      					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
      					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
      				</div>
      			</div>
      			<div class="box-body">
      				<?php
						if ($this->session->flashdata('chit_alert')) {
							$message = $this->session->flashdata('chit_alert');
						?>
      					<div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
      						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      						<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
      						<?php echo $message['message']; ?>
      					</div>
      				<?php } ?>
      				<div class="col-md-12">
      					<!-- General Settings content -->
      					<ul class="nav nav-pills nav-stacked col-md-2">
      						<li class="active"><a href="#tab_m" data-toggle="pill">Maintenance</a></li>
      						<!-- branch settings-->
      						<?php if ($userType <= 2) { ?>
      							<li><a class="branch_setting" href="#tab_branch" data-toggle="pill">Branch</a></li>
      							<!-- <li><a href="#tab_gst_settings" data-toggle="pill">Gst Settings</a></li> -->
      						<?php } ?>
      						<!-- branch settings-->
      						<li><a href="#tab_1" data-toggle="pill">Scheme & Payment</a></li>
      						<li><a href="#tab_2" data-toggle="pill">Country</a></li>
      						<!-- <li><a href="#tab_otpsettings" data-toggle="pill">Otp Settings</a></li> -->
      						<!-- <li><a href="#tab_Schemeaccno" data-toggle="pill">Scheme a/c no Generate </a></li> -->
      						<!-- <li><a href="#tab_Schemegroup" data-toggle="pill">Scheme Grouping</a></li>  -->
      						<!-- <li><a href="#tab_receipt" data-toggle="pill">Receipt</a></li> -->
      						<li><a href="#tab_3" data-toggle="pill">Metal Rate</a></li>
      						<li><a id="db_backup" href="#tab_4" data-toggle="pill">Backup Database</a></li>
      						<?php if ($userType <= 2) { ?>
      							<li><a href="#tab_5" data-toggle="pill">Clear Database</a></li>
      							<li><a href="#tab_6" data-toggle="pill">Gateway API</a></li>
      							<li><a href="#tab_7" data-toggle="pill"> Otp & Promotion API</a></li>
      							<li><a href="#otpcredit" data-toggle="pill"> Otp & Promotion Credit</a></li>
      							<li><a href="#tab_8" data-toggle="pill">Mail Settings</a></li>
      							<li><a href="#tab_9" data-toggle="pill">Limit Settings</a></li>
      							<li><a href="#tab_discount" data-toggle="pill">Discount Settings</a></li>
      							<!-- <li><a href="#payment" data-toggle="pill">Payment Settings</a></li> -->
      							<li><a href="#tab_Wallet" data-toggle="pill">Wallet Settings</a></li>
      							<li><a href="#tab_Referral" data-toggle="pill">Referral Settings</a></li>
      							<li><a href="#tab_kyc" data-toggle="pill">KYC Configuration</a></li>
      							<li><a href="#tab_feature_flags" data-toggle="pill">Feature Flags</a></li>
      							
      							<li><a href="#tab_10" data-toggle="pill">Other settings</a></li>
      						<?php } ?>
      					</ul>
      					<!-- Tab content -->
      					<div class="tab-content col-md-10">
      						<!-- maintenance tab -->
      						<div class="tab-pane active" id="tab_m">
      							<h4 class="page-header">Maintenance</h4>
      							<?php
									$attributes = array('autocomplete' => "off", 'role' => 'form', 'id' => 'gen_settings');
									echo form_open(($general['id_chit_settings'] == NULL ? 'settings/general/save' : 'settings/general/update/' . $general['id_chit_settings']), $attributes);
									?>
      							<div class="row">
      								<div class="form-group">
      									<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Mode</label>
      									<div class="col-md-3">
      										<input type="checkbox" id="m_active" class="switch" data-on-text="ON" data-off-text="OFF" name="general[maintenance_mode]" value="1" <?php if ($general['maintenance_mode'] == 1) { ?>checked="true" <?php } ?> />
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="form-group">
      									<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Maintenance text</label>
      									<div class="col-md-6">
      										<textarea class="form-control" rows="5" cols="75" id="m_text" name="general[maintenance_text]"><?php echo set_value('general[maintenance_text]', $general['maintenance_text']); ?></textarea>
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div>
      							<div class="box-footer clearfix">
      								<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      								<button class="btn btn-sm btn-app pull-right" id="submit_maintn_tab" name="general[submit_maintn_tab]" value="Maintenance Tab"><i class="fa fa-save"></i> Save</button>
      							</div>
      						</div>
      						<!--/ maintenance tab -->
      						<!-- payment setting tab -->
      						<!--  <div class="tab-pane " id="payment">
			            <h4 class="page-header">Payment Settings</h4>
			              <div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											  <label class="checkbox-inline">
												<input type="checkbox" id="edit_addpay_page" name="general[edit_addpay_page]" value="1"<?php if ($general['edit_addpay_page'] == 1) { ?>checked="true" <?php } ?> />                          
												Allow customers to edit payment page.
											  </label>
										</div>
									</div>
						 </div>	
						 <div class="box-footer clearfix">
		                  <button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
		                  <button type="submit" class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
		                </div> 
					</div>	 -->
      						<!-- payment setting tab -->
      						<!-- scheme setting tab -->
      						<div class="tab-pane " id="tab_1">
      							<legend> <a data-toggle="tooltip" title=" ">Scheme & Payment Settings</a></legend>
      							<!-- tab content -->
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="scheme_delete" name="general[allow_join_multiple]" value="1" <?php if ($general['allow_join_multiple'] == 1) { ?>checked="true" <?php } ?> />
      											Allow customers to join multiple schemes.
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="scheme_delete" name="general[allow_join_unpaid]" value="1" <?php if ($general['allow_join_unpaid'] == 1) { ?>checked="true" <?php } ?> />
      											Allow customers to join new scheme if any unpaid scheme exists.
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="scheme_delete" name="general[delete_unpaid]" value="1" <?php if ($general['delete_unpaid'] == 1) { ?>checked="true" <?php } ?> />
      											Allow customers to delete unpaid scheme.
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" name="general[reg_existing]" value="1" <?php if ($general['reg_existing'] == 1) { ?>checked="true" <?php } ?> />
      											Allow customers to register existing scheme.
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" name="general[otp_scheme_join]" value="1" <?php if ($general['otp_scheme_join'] == 1) { ?>checked="true" <?php } ?> />
      											Otp validation required for while joining scheme.
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" name="general[newSchjoinonline]" value="1" <?php if ($general['newSchjoinonline'] == 1) { ?>checked="true" <?php } ?> />
      											Allow customers to New Scheme join</b>
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" name="general[branchwise_scheme]" value="1" <?php if ($general['branchwise_scheme'] == 1) { ?>checked="true" <?php } ?> />
      											Branchwise scheme</b>
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" name="general[sch_limit]" value="1" <?php if ($general['sch_limit'] == 1) { ?>checked="true" <?php } ?> />
      											Set limit for schemes</b>
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="scheme_delete" name="general[regExistingReqOtp]" value="1" <?php if ($general['regExistingReqOtp'] == 1) { ?>checked="true" <?php } ?> />
      											OTP Validation required for <b>Existing scheme Registration. </b>
      											<br /><small>NOTE : If checked admin approval not needed.</small>
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="scheme_delete" name="general[show_closed_list]" value="1" <?php if ($general['show_closed_list'] == 1) { ?>checked="true" <?php } ?> />
      											Show closed accounts list to cutomers.
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="scheme_delete" name="general[gst_setting]" value="1" <?php if ($general['gst_setting'] == 1) { ?>checked="true" <?php } ?> />
      											Allow GST for schemes
      										</label>
      									</div>
      								</div>
      							</div>
      							<!--Restrict Last Installment in online gateway if any pending available(In hours count) Dt: 7/8/2023,By:Abi-->
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<b>Restrict Last Installment in online gateway if any pending available(In Hours Count)</b>
      										<input type="number" id="restrict_lastPayment" name="general[restrict_lastPayment_days]" value="<?php echo $general['restrict_lastPayment_days']; ?>">
      									</div>
      								</div>
      							</div>
      							<!-- Restrict Last Installment in online gateway if any pending available(In hours count) -->
      							<!-- Added by Durga Starts here 17-07-2023 -->
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label for="new_ios_version" class="col-md-3">Enable Device Count Limit</label>
      										<div class="col-md-4">
      											<input type="text" class="form-control" name="general[chitCollectionEmpCount]" onkeypress="return /^[0-9]$/i.test(event.key)" id="chitCollectionEmpCount" value="<?php echo set_value($general['chitCollectionEmpCount'], $general['chitCollectionEmpCount'])  ?>" placeholder="eg:" />
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      							</div>
      							<!-- Added by Durga ends here 17-07-2023 -->
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<legend> <a data-toggle="tooltip" title=" ">OTP setting</a></legend>
      										<label class="checkbox-inline">
      											<input type="checkbox" id="scheme_delete" name="general[enable_closing_otp]" value="1" <?php if ($general['enable_closing_otp'] == 1) { ?>checked="true" <?php } ?> />
      											Enable OTP verification for scheme account closing
      										</label>
      									</div>
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="isOTPRegForPayment" name="general[isOTPRegForPayment]" value="1" <?php if ($general['isOTPRegForPayment'] == 1) { ?>checked="true" <?php } ?> />
      											Enable OTP verification for payment
      											&nbsp;&nbsp;<label>Expiry Time in seconds</label>
      											<input type="number" id="payOTP_exp" name="general[payOTP_exp]" style="width: 20%;" disabled="true" value='<?php echo $general['payOTP_exp']; ?>'>
      										</label>
      									</div>
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="isOTPReqToLogin" name="general[isOTPReqToLogin]" value="1" <?php if ($general['isOTPReqToLogin'] == 1) { ?>checked="true" <?php } ?> />
      											Enable OTP verification for login
      											&nbsp;&nbsp;<label>Expiry Time in seconds</label>
      											<input type="number" id="loginOTP_exp" name="general[loginOTP_exp]" style="width: 20%;" disabled="true" value='<?php echo $general['loginOTP_exp']; ?>'>
      										</label>
      									</div>
      									<!-- Created by RK - 12/12/2022-->
      									<!-- Adding checkbox for otp for gift issue starts here-->
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="isOTPReqToGift" name="general[isOTPReqToGift]" value="1" <?php if ($general['isOTPReqToGift'] == 1) { ?>checked="true" <?php } ?> />
      											Enable OTP verification for Gift Issue
      											&nbsp;&nbsp;<label>Expiry Time in seconds</label>
      											<input type="number" id="giftOTP_exp" name="general[giftOTP_exp]" style="width: 20%;" disabled="true" value='<?php echo $general['giftOTP_exp']; ?>'>
      										</label>
      									</div>
      									<!-- Adding checkbox for otp for gift issue Ends here-->
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<legend> <a data-toggle="tooltip" title=" ">Scheme Group</a></legend>
      										<label class="checkbox-inline">
      											<input type="checkbox" id="scheme_delete" name="general[has_lucky_draw]" value="1" <?php if ($general['has_lucky_draw'] == 1) { ?>checked="true" <?php } ?> />
      											Enable Lucky draw scheme grouping
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<legend> <a data-toggle="tooltip" title=" ">Payment</a></legend>
      										<div class="col-md-12">
      											<div class="col-md-3">
      												<label>Cost Center Settings</label>
      											</div>
      											<div class="col-md-3">
      												<input type="radio" name="general[cost_center]" value="1" <?php if ($general['cost_center'] == 1) { ?> checked="true" <?php } ?>> single gateway
      											</div>
      											<div class="col-md-3">
      												<input type="radio" name="general[cost_center]" value="2" <?php if ($general['cost_center'] == 2) { ?> checked="true" <?php } ?>> Multi-Cost Center [Branch-wise Customer]
      											</div>
      											<div class="col-md-3">
      												<input type="radio" name="general[cost_center]" value="3" <?php if ($general['cost_center'] == 3) { ?> checked="true" <?php } ?>> Multi-Cost Center [Single Customer Ac for multi branch]
      											</div>
      											<div class="col-md-3">
      												<input type="radio" name="general[cost_center]" value="4" <?php if ($general['cost_center'] == 4) { ?> checked="true" <?php } ?>> Multi-Cost Center [Commodity Wise Accounts]
      											</div>
      										</div><br><br>
      										<label class="checkbox-inline">
      											<input type="checkbox" id="edit_addpay_page" name="general[edit_addpay_page]" value="1" <?php if ($general['edit_addpay_page'] == 1) { ?>checked="true" <?php } ?> />
      											Allow employee to edit payment page.
      										</label>
      									</div>
      									<!-- HH <div class="form-group">
											  <label class="checkbox-inline">
												<input type="checkbox" id="scheme_wise_receipt" name="general[scheme_wise_receipt]" value="1"<?php if ($general['scheme_wise_receipt'] == 1) { ?>checked="true" <?php } ?> />                          
												 Generate Scheme Wise Receipt No
											  </label>
										</div>
										<div class="form-group">
											  <label class="checkbox-inline">
												<input type="checkbox" id="branch_wise_receipt" name="general[branch_wise_receipt]" value="1"<?php if ($general['branch_wise_receipt'] == 1) { ?>checked="true" <?php } ?> />                          
												 Generate Branch Wise Receipt No
											  </label>
										</div> -->
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" id="edit_custom_entry_date" name="general[edit_custom_entry_date]" value="1" <?php if ($general['edit_custom_entry_date'] == 1) { ?>checked="true" <?php } ?> />
      											<input type="hidden" id="edit_custom_entry_date" value="<?php echo $general['edit_custom_entry_date']; ?>">
      											Show custom entry date
      										</label>
      									</div>
      									<!--<input type="text" class="form-group" id="entry_date" name="general[custom_entry_date]" value="<?php echo $general['custom_entry_date']; ?>" > -->
      								</div>
      							</div>
      							<!--Generate receipt  number  based on one more settings Integ Auto HH -->
      							<div class="row">
      								<div class="form-group">
      									<div class="col-md-12">
      										<legend> <a data-toggle="tooltip" title=" ">Receipt No. Generation</a></legend>
      										<div class="col-md-3">
      											<input type="radio" name="general[receipt_no_set]" value="0" <?php if ($general['receipt_no_set'] == 0) { ?> checked="true" <?php } ?>> Manual
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[receipt_no_set]" value="1" <?php if ($general['receipt_no_set'] == 1) { ?> checked="true" <?php } ?>> Automatic
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[receipt_no_set]" value="2" <?php if ($general['receipt_no_set'] == 2) { ?> checked="true" <?php } ?>> Integration (Jilaba,...)
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[receipt_no_set]" value="3" <?php if ($general['receipt_no_set'] == 3) { ?> checked="true" <?php } ?>> Integration (Auto Sync)
      										</div>
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div><br>
      							<div class="row">
      								<div class="col-md-12">
      									<div class="form-group">
      										<div class="col-md-3">
      											Receipt size :
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[receipt]" value="0" <?php if ($general['receipt'] == 0) { ?> checked="true" <?php } ?>> Default(A4 Size)
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[receipt]" value="1" <?php if ($general['receipt'] == 1) { ?> checked="true" <?php } ?>> Customized
      										</div>
      										<p class="help-block"></p>
      									</div>
      									<br><br>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-md-12">
      									<div class="form-group">
      										<div class="col-md-3">
      											<label>Generate Receipt No. based on</label>
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[scheme_wise_receipt]" value="1" <?php if ($general['scheme_wise_receipt'] == 1) { ?> checked="true" <?php } ?>> common
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[scheme_wise_receipt]" value="2" <?php if ($general['scheme_wise_receipt'] == 2) { ?> checked="true" <?php } ?>> Branch-wise
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[scheme_wise_receipt]" value="3" <?php if ($general['scheme_wise_receipt'] == 3) { ?> checked="true" <?php } ?>> scheme-wise
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[scheme_wise_receipt]" value="4" <?php if ($general['scheme_wise_receipt'] == 4) { ?> checked="true" <?php } ?>> scheme with Branch-wise
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[scheme_wise_receipt]" value="5" <?php if ($general['scheme_wise_receipt'] == 5) { ?> checked="true" <?php } ?>> Financial Year-wise
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[scheme_wise_receipt]" value="6" <?php if ($general['scheme_wise_receipt'] == 6) { ?> checked="true" <?php } ?>> Financial Year with Scheme & Branch wise
      										</div>
      										<div class="col-md-3">
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[scheme_wise_receipt]" value="7" <?php if ($general['scheme_wise_receipt'] == 7) { ?> checked="true" <?php } ?>> Financial Year with Branch wise
      										</div>
      									</div>
      									<br><br>
      								</div>
      								<div class="row">
      									<div class="col-md-3">
      										<label>Generate Group Wise Receipt No.</label>
      									</div>
      									<div class="col-md-3">
      										<input type="radio" name="general[group_wise_receipt]" value="1" <?php if ($general['group_wise_receipt'] == 1) { ?> checked="true" <?php } ?>> Yes
      									</div>
      									<div class="col-md-3">
      										<input type="radio" name="general[group_wise_receipt]" value="0" <?php if ($general['group_wise_receipt'] == 0) { ?> checked="true" <?php } ?>> No
      									</div>
      								</div>
      							</div>
      							<!--	<div class="row">
                					<div class="form-group">
                					   <div class="col-md-12">
                					       <legend> <a  data-toggle="tooltip" title=" ">Receipt No. Display Format</a></legend> 
                					   <div class="col-md-3">
                							<input type="radio" name="general[receiptNo_displayFrmt]" value="0" <?php if ($general['receiptNo_displayFrmt'] == 0) { ?> checked="true" <?php } ?> > Only Receipt Number
                						</div>
                						<div class="col-md-3">
                							<input type="radio" name="general[receiptNo_displayFrmt]" value="1" <?php if ($general['receiptNo_displayFrmt'] == 1) { ?> checked="true" <?php } ?> > Based on Rcpt No Settings
                						</div>
                						<div class="col-md-3">
                							<input type="radio" name="general[receiptNo_displayFrmt]" value="2" <?php if ($general['receiptNo_displayFrmt'] == 2) { ?> checked="true" <?php } ?> > Customized
                						</div>	
                						<p class="help-block"></p>
                					   </div>
                					</div><br><br>
                				 </div>		-->
      							<br><br>
      							<!--Generate account  number  based on one more settings Integ Auto HH -->
      							<div class="row">
      								<div class="form-group">
      									<div class="col-md-12">
      										<legend> <a data-toggle="tooltip" title=" ">Scheme A/C No. Generation</a></legend>
      										<div class="col-md-3">
      											<input type="radio" name="general[schemeacc_no_set]" value="0" <?php if ($general['schemeacc_no_set'] == 0) { ?> checked="true" <?php } ?>> Yes (Automatic)
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[schemeacc_no_set]" value="1" <?php if ($general['schemeacc_no_set'] == 1) { ?> checked="true" <?php } ?>> No (Manual)
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[schemeacc_no_set]" value="2" <?php if ($general['schemeacc_no_set'] == 2) { ?> checked="true" <?php } ?>> Integration (Jilaba,...)
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[schemeacc_no_set]" value="3" <?php if ($general['schemeacc_no_set'] == 3) { ?> checked="true" <?php } ?>> Integration (Auto Sync.)
      										</div>
      										<p class="help-block"></p>
      									</div>
      								</div><br><br>
      							</div>
      							<br>
      							<div class="row">
      								<div class="col-md-4">
      									<label>Generate Scheme Account No. based on </label>
      								</div>
      								<div class="col-md-3">
      									<select id="3" name="general[scheme_wise_acc_no]" class="form-control">
      										<option value="0" <?php if ($general['scheme_wise_acc_no'] == 0) { ?> selected="true" <?php } ?>>Common</option>
      										<option value="1" <?php if ($general['scheme_wise_acc_no'] == 1) { ?> selected="true" <?php } ?>>Common with branch wise</option>
      										<option value="2" <?php if ($general['scheme_wise_acc_no'] == 2) { ?> selected="true" <?php } ?>>Scheme Wise</option>
      										<option value="3" <?php if ($general['scheme_wise_acc_no'] == 3) { ?> selected="true" <?php } ?>>Scheme wise With Branch Wise</option>
      										<option value="4" <?php if ($general['scheme_wise_acc_no'] == 4) { ?> selected="true" <?php } ?>>Financial Year Wise</option>
      										<option value="5" <?php if ($general['scheme_wise_acc_no'] == 5) { ?> selected="true" <?php } ?>>Financial Year with Scheme Wise</option>
      										<option value="6" <?php if ($general['scheme_wise_acc_no'] == 6) { ?> selected="true" <?php } ?>>Financial Year with Scheme & Branch Wise</option>
      									</select>
      									<br><br>
      								</div>
      							</div>
      							<br>
      							<!--	<div class="row">
						<div class="form-group">
							   <div class="col-md-12">
							       <legend> <a  data-toggle="tooltip" title=" ">Scheme A/C No. Display Format</a></legend> 
							   <div class="col-md-3">
									<input type="radio" name="general[schemeaccNo_displayFrmt]" value="0" <?php if ($general['schemeaccNo_displayFrmt'] == 0) { ?> checked="true" <?php } ?> > Only Account Number
								</div>
								<div class="col-md-3">
									<input type="radio" name="general[schemeaccNo_displayFrmt]" value="1" <?php if ($general['schemeaccNo_displayFrmt'] == 1) { ?> checked="true" <?php } ?> > Based on Acc No Settings
								</div>
								<div class="col-md-3">
									<input type="radio" name="general[schemeaccNo_displayFrmt]" value="2" <?php if ($general['schemeaccNo_displayFrmt'] == 2) { ?> checked="true" <?php } ?> > Customized
								</div>	
								<p class="help-block"></p>
							   </div>
						</div><br><br>
						</div>	-->
      							<div class="row">
      								<div class="form-group">
      									<div class="col-md-12">
      										<legend> <a data-toggle="tooltip" title=" ">Client ID Generation</a></legend>
      										<div class="col-md-3">
      											<input type="radio" name="general[gent_clientid]" value="0" <?php if ($general['gent_clientid'] == 0) { ?> checked="true" <?php } ?>> No
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[gent_clientid]" value="1" <?php if ($general['gent_clientid'] == 1) { ?> checked="true" <?php } ?>> Yes
      										</div>
      										<p class="help-block"></p>
      									</div>
      								</div><br><br>
      							</div>
      							<div class="row">
      								<div class="form-group">
      									<div class="col-md-12">
      										<legend> <a data-toggle="tooltip" title=" ">Edit Account Name</a></legend>
      										<div class="col-md-3">
      											<input type="radio" name="general[cusName_edit]" value="0" <?php if ($general['cusName_edit'] == 0) { ?> checked="true" <?php } ?>> No [Customer Name]
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[cusName_edit]" value="1" <?php if ($general['cusName_edit'] == 1) { ?> checked="true" <?php } ?>> Yes
      										</div>
      										<p class="help-block"></p>
      									</div>
      								</div><br><br>
      							</div>
      							<br><br>
      							<div class="row"> <!--Metal Wgt Decimal & RoundOff settings  For scheme master HH -->
      								<div class="form-group">
      									<legend><a data-toggle="tooltip" title="Metal Wgt Decimal & RoundOff settings "> Metal Wgt Decimal & RoundOff settings </a></legend>
      									<div class="col-md-12" style="margin-top: 29px;">
      										<div class="col-md-6">
      											<input type="checkbox" id="metal_wgt_roundoff" name="general[metal_wgt_roundoff]" value="1" <?php if ($general['metal_wgt_roundoff'] == 1) { ?>checked="true" <?php } ?> />
      											Enable Metal Wgt Roundoff
      										</div>
      										<div id="decimal_block">
      											<div class="col-md-3">
      												<label>Metal Wgt Decimal</label>
      												<input type="number" class="form-control decimal_block" id="metal_wgt_decimal" name="general[metal_wgt_decimal]" value="<?php echo set_value('general[metal_wgt_decimal]', $general['metal_wgt_decimal']); ?>" placeholder="Enter Decimal value" />
      											</div>
      										</div>
      									</div>
      									<p class="help-block"></p>
      								</div><br><br>
      							</div> <!--Metal Wgt Decimal & RoundOff settings For scheme master -->
      							<br>
      							<legend> <a data-toggle="tooltip" title=" ">Auto Debit Settings</a></legend>
      							<div class="row">
      								<div class="col-md-6">
      									<div class="form-group">
      										<label>Auto Debit &nbsp;&nbsp;</label>
      										<input type="radio" name="general[auto_debit]" value="1" <?php if ($general['auto_debit'] == 1) { ?> checked="true" <?php } ?>> Active &nbsp;&nbsp;
      										<input type="radio" name="general[auto_debit]" value="0" <?php if ($general['auto_debit'] == 0) { ?> checked="true" <?php } ?>> Inactive
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-md-12">
      									<label>Web/mobile app Payment option </label>
      									<div class="form-group">
      										<div class="col-md-3">
      											<input type="radio" name="general[auto_debit_allow_app_pay]" value="0" <?php if ($general['auto_debit_allow_app_pay'] == 0) { ?> checked="true" <?php } ?>> Block app payment
      										</div>
      										<div class="col-md-3">
      											<input type="radio" name="general[auto_debit_allow_app_pay]" value="1" <?php if ($general['auto_debit_allow_app_pay'] == 1) { ?> checked="true" <?php } ?>> Allow app payment
      										</div>
      										<div class="col-md-6">
      											<input type="radio" name="general[auto_debit_allow_app_pay]" value="2" <?php if ($general['auto_debit_allow_app_pay'] == 2) { ?> checked="true" <?php } ?>> Allow app payment only when subscription status is not ACTIVE
      										</div>
      									</div>
      								</div>
      								<p class="help-block"></p>
      							</div>
      							<!-- Added by Durga for customized account and receipt number settings starts here 26-06-2023 -->
      							<br><br>
      							<div class="row" style="display:none;">
      								<div class="form-group">
      									<div class="col-md-12">
      										<legend> <a data-toggle="tooltip" title="">Receipt No. Display Format</a></legend>
      										<input type="hidden" id="display_receipt_format" value="<?php echo $general['receiptNo_displayFrmt'] ?>" />
      										<div class="col-md-3">
      											<input type="radio" name="general[receiptNo_displayFrmt]" value="0" <?php if ($general['receiptNo_displayFrmt'] == 0) { ?> checked="true" <?php } ?>> Only Receipt Number
      										</div>
      										<!-- <div class="col-md-3">
													<input type="radio" name="general[receiptNo_displayFrmt]" value="1" <?php if ($general['receiptNo_displayFrmt'] == 1) { ?> checked="true" <?php } ?> > Based on Rcpt No Settings
												</div> -->
      										<div class="col-md-3">
      											<input type="radio" name="general[receiptNo_displayFrmt]" value="2" <?php if ($general['receiptNo_displayFrmt'] == 2) { ?> checked="true" <?php } ?>> Customized
      										</div>
      										<p class="help-block"></p>
      									</div>
      								</div><br><br>
      							</div>
      							<br><br>
      							<input type="hidden" id="rcpt_format_hidden" name="general[receiptNo_format]" />
      							<div class="row" id="rcpt_num_block" style="display:none;">
      								<div class="form-group">
      									<label class="control-label col-sm-2 ">Receipt Number Format Customization</label>
      									<div class="col-sm-4 ">
      										<select style="height: 150px;" class="form-control" size="11" id="selected_receipt_field_list" name="selected_receipt_field_list">
      											<?php if (count($selected_receipt_num_fields) > 0) { ?>
      												<?php foreach ($selected_receipt_num_fields as $field) { ?>
      													<option value="@@<?php echo $field['value'] ?>@@"><?php echo $field['text'] ?></option>
      											<?php }
													} ?>
      										</select>
      										<span class="help-block">choose/Remove the Receipt Number Fields.</span>
      										<span style="font-weight:bold;">Format </span><span id="display_rcpt_span" style="font-weight:bold;color:red;"></span>
      									</div>
      									<div class="col-sm-1">
      										<div class="btn-group" data-toggle="buttons">
      											<label onclick="revertReceiptFormat()" style="margin-top: 30px;" class="btn btn-primary">
      												<a style="color:#FFFFFF; text-decoration:none" href="" id="revert_receipt_button" name="revert_receipt_button" title="click"> >> </a>
      											</label>
      											<label onclick="insertReceiptFormat()" style="margin-top: 30px;" class="btn btn-primary">
      												<a style="color:#FFFFFF; text-decoration:none" href="" id="move_receipt_button" name="move_receipt_button" title="click">
      													<< </a>
      											</label>
      										</div>
      									</div>
      									<div class="col-sm-3">
      										<select style="height: 150px;" class="form-control" size="11" id="field_receipt_list" name="field_receipt_list">
      											<?php foreach ($receipt_num_fields as $field) { ?>
      												<option value="@@<?php echo $field['value'] ?>@@"><?php echo $field['text'] ?></option>
      											<?php } ?>
      										</select>
      										<span class="help-block">Select the value.</span>
      									</div>
      								</div>
      							</div>
      							<div class="row" style="display:none;">
      								<div class="form-group">
      									<div class="col-md-12">
      										<legend> <a data-toggle="tooltip" title="">Scheme A/C No. Display Format</a></legend>
      										<input type="hidden" id="display_format" value="<?php echo $general['schemeaccNo_displayFrmt'] ?>" />
      										<div class="col-md-3">
      											<input type="radio" name="general[schemeaccNo_displayFrmt]" value="0" <?php if ($general['schemeaccNo_displayFrmt'] == 0) { ?> checked="true" <?php } ?>> Only Account Number
      										</div>
      										<!-- <div class="col-md-3">
											<input type="radio" name="general[schemeaccNo_displayFrmt]" value="1" <?php if ($general['schemeaccNo_displayFrmt'] == 1) { ?> checked="true" <?php } ?> > Based on Acc No Settings
										</div> -->
      										<div class="col-md-3">
      											<input type="radio" name="general[schemeaccNo_displayFrmt]" value="2" <?php if ($general['schemeaccNo_displayFrmt'] == 2) { ?> checked="true" <?php } ?>> Customized
      										</div>
      										<p class="help-block"></p>
      									</div><br><br>
      								</div>
      								<br><br>
      								<input type="hidden" id="acc_format_hidden" name="general[schemeaccNo_format]" />
      								<div class="row" id="ac_num_block" style="display:none;">
      									<div class="form-group">
      										<label class="control-label col-sm-2 ">Account Number Format Customization</label>
      										<div class="col-sm-4 ">
      											<!-- <textarea class="form-control" name="general[custom_AccDisplayFrmt]" id="custom_AccDisplayFrmt" cols="35" rows="5" tabindex="4" >
										</textarea> -->
      											<select style="height: 150px;" class="form-control" size="11" id="selected_field_list" name="selected_field_list">
      												<?php foreach ($selected_acc_num_fields as $field) { ?>
      													<option value="@@<?php echo $field['value'] ?>@@"><?php echo $field['text'] ?></option>
      												<?php } ?>
      											</select>
      											<span class="help-block">choose/Remove the A/c Number Fields.</span>
      											<span style="font-weight:bold;">Format </span><span id="display_acc_span" style="font-weight:bold;color:red;"></span>
      										</div>
      										<div class="col-sm-1">
      											<div class="btn-group" data-toggle="buttons">
      												<label onclick="revertFormat()" style="margin-top: 30px;" class="btn btn-primary">
      													<a style="color:#FFFFFF; text-decoration:none" href="" id="revert_button" name="revert_button" title="click"> >> </a>
      												</label>
      												<label onclick="insertFormat()" style="margin-top: 30px;" class="btn btn-primary">
      													<a style="color:#FFFFFF; text-decoration:none" href="" id="move_button" name="move_button" title="click">
      														<< </a>
      												</label>
      											</div>
      										</div>
      										<div class="col-sm-3">
      											<select style="height: 150px;" class="form-control" size="11" id="field_list" name="field_list">
      												<?php foreach ($acc_num_fields as $field) { ?>
      													<option value="@@<?php echo $field['value'] ?>@@"><?php echo $field['text'] ?></option>
      												<?php } ?>
      											</select>
      											<span class="help-block">Select the value.</span>
      										</div>
      									</div>
      								</div>
      							</div>
      							<!-- Added by Durga for customized account and receipt number settings ends here 26-06-2023 -->
      							<!-- /tab content -->
      							<div class="box-footer clearfix">
      								<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      								<button class="btn btn-sm btn-app pull-right" id="submit_sch_pay_tab" name="general[submit_sch_pay_tab]" value="Scheme Tab"><i class="fa fa-save"></i> Save</button>
      							</div>
      						</div>
      						<!-- /scheme setting tab -->
      						<!-- Metal rate tab -->
      						<div class="tab-pane" id="tab_3">
      							<h4 class="page-header">Metal rate</h4>
      							<!-- tab content -->
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" name="general[rate_update]" value="1" <?php if ($general['rate_update'] == 1) { ?>checked="true" <?php } ?> />
      											Allow metal rate to update from api.
      										</label>
      									</div>
      								</div>
      							</div>
      							<h4 class="page-header">Gold rate Discount</h4>
      							<div class="row">
      								<div class="col-md-12">
      									<div class="col-md-4">
      										<div class="form-group">
      											<label class="checkbox-inline">
      												<input type="checkbox" name="general[enableGoldrateDisc]" value="1" <?php if ($general['enableGoldrateDisc'] == 1) { ?>checked="true" <?php } ?> />
      												Allow Gold rate Discount 22K.
      											</label>
      										</div>
      									</div>
      									<div class="col-md-8">
      										<label class="col-md-6 "> Gold rate 22k Discount Amount.</label>
      										<div class="col-md-2">
      											<div class="form-group">
      												<input type="number" style="width:100%" name="general[goldDiscAmt]" id="goldDiscAmt" value="<?php echo set_value('general[goldDiscAmt]', $general['goldDiscAmt']); ?>" />
      											</div>
      										</div>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-md-12">
      									<div class="col-md-4">
      										<div class="form-group">
      											<label class="checkbox-inline">
      												<input type="checkbox" name="general[enableGoldrateDisc_18k]" value="1" <?php if ($general['enableGoldrateDisc_18k'] == 1) { ?>checked="true" <?php } ?> />
      												Allow Gold rate Discount 18K.
      											</label>
      										</div>
      									</div>
      									<div class="col-md-8">
      										<label class="col-md-6 "> Gold rate 18k Discount Amount.</label>
      										<div class="col-md-2">
      											<div class="form-group">
      												<input type="number" style="width:100%" name="general[goldDiscAmt_18k]" id="goldDiscAmt_18k" value="<?php echo set_value('general[goldDiscAmt_18k]', $general['goldDiscAmt_18k']); ?>" />
      											</div>
      										</div>
      									</div>
      								</div>
      							</div>
      							<h4 class="page-header">Silver rate Discount</h4>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="col-sm-3">
      										<div class="form-group">
      											<label class="checkbox-inline">
      												<input type="checkbox" name="general[enableSilver_rateDisc]" value="1" <?php if ($general['enableSilver_rateDisc'] == 1) { ?>checked="true" <?php } ?> />
      												Allow Silver rate Discount.
      											</label>
      										</div>
      									</div>
      									<div class="col-sm-9">
      										<label class="col-sm-5 col-sm-offset-1 "> Silver rate Discount Amount.</label>
      										<div class="col-sm-2">
      											<div class="form-group">
      												<input type="number" step=".01" style="width:100%" name="general[silverDiscAmt]" id="silverDiscAmt" value="<?php echo set_value('general[silverDiscAmt]', $general['silverDiscAmt']); ?>" />
      											</div>
      										</div>
      									</div>
      								</div>
      							</div>
      							<!-- /tab content -->
      							<div class="box-footer clearfix">
      								<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      								<button class="btn btn-sm btn-app pull-right" id="submit_metal_tab" name="general[submit_metal_tab]" value="Metal Tab"><i class="fa fa-save"></i> Save</button>
      							</div>
      						</div>
      						<div class="tab-pane" id="tab_10">
      							<h4 class="page-header">Others</h4>
      							<div class="row">
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" name="general[allow_savecard]" value="1" <?php if ($general['allow_savecard'] == 1) { ?>checked="true" <?php } ?> />
      											Allow savecard for app
      										</label>
      									</div>
      								</div>
      								<div class="col-sm-12">
      									<div class="form-group">
      										<label class="checkbox-inline">
      											<input type="checkbox" name="general[allow_catlog]" value="1" <?php if ($general['allow_catlog'] == 1) { ?>checked="true" <?php } ?> />
      											Allow catlog
      										</label>
      									</div>
      								</div>
      								<!-- Req Oto Settings Options For User HH-->
      								<h4 class="page-header">Req Otp Login Settings For User</h4>
      								<div class="row">
      									<div class="col-sm-12">
      										<div class="form-group">
      											<h5 for="chargeseme_name" class="col-md-4">Req Otp Required </h5>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													<input type="radio" id="req_otp_login" name="general[req_otp_login]" value="0" <?php if ($general['req_otp_login'] == 0) { ?> checked="true" <?php } ?>> No
      												</div>
      												<div class="col-md-5">
      													<input type="radio" id="req_otp_login" name="general[req_otp_login]" value="1" <?php if ($general['req_otp_login'] == 1) { ?> checked="true" <?php } ?>> Yes
      												</div>
      												<p class="help-block"></p>
      											</div>
      										</div><br><br>
      									</div>
      								</div>
      								<!-- Req Oto Settings Options For User -->
      								<!-- DIrect To home settings Required HH-->
      								<h4 class="page-header">DIrect To home form Menu Enable and DIsable based on the settings.</h4>
      								<div class="row">
      									<div class="col-sm-12">
      										<div class="form-group">
      											<h5 for="chargeseme_name" class="col-md-4">DIrect To home Settings Required</h5>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													<input type="radio" name="general[enable_dth]" value="0" <?php if ($general['enable_dth'] == 0) { ?> checked="true" <?php } ?>> Enable
      												</div>
      												<div class="col-md-5">
      													<input type="radio" name="general[enable_dth]" value="1" <?php if ($general['enable_dth'] == 1) { ?> checked="true" <?php } ?>> Disable
      												</div>
      												<p class="help-block"></p>
      											</div>
      										</div><br><br>
      									</div>
      								</div>
      								<!-- DIrect To home settings Required -->
      								<!-- Coin Enquiry settings Required HH-->
      								<h4 class="page-header">Coin Enquiry form Enable and Disable based on the settings.</h4>
      								<div class="row">
      									<div class="col-sm-12">
      										<div class="form-group">
      											<h5 for="chargeseme_name" class="col-md-4">Coin Enquiry Settings Required</h5>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													<input type="radio" name="general[enable_coin_enq]" value="1" <?php if ($general['enable_coin_enq'] == 1) { ?> checked="true" <?php } ?>> Enable
      												</div>
      												<div class="col-md-5">
      													<input type="radio" name="general[enable_coin_enq]" value="0" <?php if ($general['enable_coin_enq'] == 0) { ?> checked="true" <?php } ?>> Disable
      												</div>
      												<p class="help-block"></p>
      											</div>
      										</div><br><br>
      									</div>
      								</div>
      								<!-- Coin Enquiry settings Required -->
      								<!-- Issue gift/Prize Otp settings Required HH-->
      								<h4 class="page-header">Issue gift/Prize OTP Requireds based on the settings.</h4>
      								<div class="row">
      									<div class="col-sm-12">
      										<div class="form-group">
      											<h5 for="chargeseme_name" class="col-md-4">Issue gift OTP Required</h5>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													<input type="radio" name="general[req_gift_issue_otp]" value="1" <?php if ($general['req_gift_issue_otp'] == 1) { ?> checked="true" <?php } ?>> Enable
      												</div>
      												<div class="col-md-5">
      													<input type="radio" name="general[req_gift_issue_otp]" value="0" <?php if ($general['req_gift_issue_otp'] == 0) { ?> checked="true" <?php } ?>> Disable
      												</div>
      												<p class="help-block"></p>
      											</div>
      										</div><br><br>
      									</div>
      									<div class="col-sm-12">
      										<div class="form-group">
      											<h5 for="chargeseme_name" class="col-md-4">Issue Prize OTP Required</h5>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													<input type="radio" name="general[req_prize_issue_otp]" value="1" <?php if ($general['req_prize_issue_otp'] == 1) { ?> checked="true" <?php } ?>> Enable
      												</div>
      												<div class="col-md-5">
      													<input type="radio" name="general[req_prize_issue_otp]" value="0" <?php if ($general['req_prize_issue_otp'] == 0) { ?> checked="true" <?php } ?>> Disable
      												</div>
      												<p class="help-block"></p>
      											</div>
      										</div><br><br>
      									</div>
      								</div>
      								<!-- VS_ settings Required HH-->
      								<h4 class="page-header">video shopping form Enable and Disable based on the settings.</h4>
      								<div class="row">
      									<div class="col-sm-12">
      										<div class="form-group">
      											<h5 for="chargeseme_name" class="col-md-4">VS Settings Required</h5>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													<input type="radio" name="general[vs_enable]" value="1" <?php if ($general['vs_enable'] == 1) { ?> checked="true" <?php } ?>> Enable
      												</div>
      												<div class="col-md-5">
      													<input type="radio" name="general[vs_enable]" value="0" <?php if ($general['vs_enable'] == 0) { ?> checked="true" <?php } ?>> Disable
      												</div>
      												<p class="help-block"></p>
      											</div>
      										</div><br><br>
      									</div>
      								</div>
      								<!-- VS_ settings Required -->
      								<!-- Coin Booking settings Required HH-->
      								<h4 class="page-header">Coin Booking form Enable and Disable based on the settings.</h4>
      								<div class="row">
      									<div class="col-sm-12">
      										<div class="form-group">
      											<h5 for="chargeseme_name" class="col-md-4">Akshaya Tritiya Coin Booking Settings Required</h5>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													<input type="radio" name="general[enable_coin_book]" value="1" <?php if ($general['enable_coin_book'] == 1) { ?> checked="true" <?php } ?>> Enable
      												</div>
      												<div class="col-md-5">
      													<input type="radio" name="general[enable_coin_book]" value="0" <?php if ($general['enable_coin_book'] == 0) { ?> checked="true" <?php } ?>> Disable
      												</div>
      												<p class="help-block"></p>
      											</div>
      										</div><br><br>
      									</div>
      								</div>
      								<!-- Coin Booking settings Required -->
      								<!-- Video Booking settings Required -->
      								<h4 class="page-header">Video Shopping Booking Time</h4>
      								<div class="row">
      									<div class="col-sm-12">
      										<div class="form-group">
      											<h5 for="chargeseme_name" class="col-md-4">Video Shop Booking Time</h5>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													From (AM)
      													<input class="form-control" value="<?php echo $general['fn_from']  ?>" name="general[fn_from]">
      												</div>
      												<div class="col-md-5">
      													To(PM)
      													<input class="form-control" value="<?php echo $general['an_to'] ?>" name="general[an_to]">
      												</div>
      											</div>
      										</div><br><br>
      									</div>
      								</div>
      								<!-- Video Booking settings Required -->
      								<h4 class="page-header">Default Country</h4>
      								<div class="row">
      									<div class="form-group">
      										<label for="country" class="col-md-3 col-md-offset-1">Country</label>
      										<div class="col-md-4">
      											<input type="hidden" id="countryval" name="co_default[id_country]" value="<?php echo set_value('co_default[id_country]', $co_default['id_country']); ?>" />
      											<select class="form-control" id="country" name="default[id_country]" style="width: 150px;"></select>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="country" class="col-md-3 col-md-offset-1">State</label>
      										<div class="col-md-4">
      											<input type="hidden" id="stateval" name="st_default[id_state]" value="<?php echo set_value('st_default[id_state]', $st_default['id_state']); ?>" />
      											<select class="form-control" id="state" name="default[id_state]" style="width: 150px;"></select>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="country" class="col-md-3 col-md-offset-1">City</label>
      										<div class="col-md-4">
      											<input type="hidden" id="cityval" name="ci_default[id_city]" value="<?php echo set_value('ci_default[id_city]', $ci_default['id_city']); ?>" />
      											<select class="form-control" id="city" name="default[id_city]" style="width: 150px;"></select>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      							</div>
      							<!-- /tab content -->
      							<div class="box-footer clearfix">
      								<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      								<button class="btn btn-sm btn-app pull-right" id="submit_others_tab" name="general[submit_others_tab]" value="Others Tab"><i class="fa fa-save"></i> Save</button>
      								<input id="tab_name" type="hidden" name="general[tab_name]" value="" />
      							</div>
      						</div>
      						

							<!--/ Metal rate tab -->
      						<!-- otp Settings -->
      						<!--   <div class="tab-pane" id="tab_otpsettings">
			             <h4 class="page-header">OTP Settings</h4>  
			              <div class="">
	                                       <?php
											$attributes = array('autocomplete' => "off", 'role' => 'form');
											$id = 1;
											echo form_open('otp_settings/settings/edit/' . $id, $attributes);
											?> 					    	
											<div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-4 col-md-offset-1 ">Enable OTP verification for scheme account closing</label>
												   <div class="col-md-6">
												   <div class="col-md-5">
														<input type="radio" name="general[enable_closing_otp]" value="0" <?php if ($general['enable_closing_otp'] == 0) { ?> checked="true" <?php } ?> > Yes
													</div>
													<div class="col-md-5">
														<input type="radio" name="general[enable_closing_otp]" value="1" <?php if ($general['enable_closing_otp'] == 1) { ?> checked="true" <?php } ?> > No
													</div>			   
													<p class="help-block"></p>
												   </div>
												</div><br><br>
											 </div>
									      <div class="box-footer clearfix">
							                  <button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
							                  <button class="btn btn-sm btn-app pull-right" ><i class="fa fa-save"></i> Save</button>
							              </div> 
											 <?php echo form_close();  ?>
										 </div>	   
			        </div>  -->
      						<!--  <div class="tab-pane" id="tab_gst_settings">
			             <h4 class="page-header">GST Settings</h4>  
			              <div class="">
	                                      <?php
											$attributes = array('autocomplete' => "off", 'role' => 'form');
											$id = 1;
											echo form_open('gst_setting/settings/edit/' . $id, $attributes);
											?> 				    	
											<div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-4 col-md-offset-1 ">Enable GST Settings for Schemes</label>
												   <div class="col-md-6">
												   <div class="col-md-5">
														<input type="radio" name="general[gst_setting]" value="1" <?php if ($general['gst_setting'] == 1) { ?> checked="true" <?php } ?> > Yes
													</div>
													<div class="col-md-5">
														<input type="radio" name="general[gst_setting]" value="0" <?php if ($general['gst_setting'] == 0) { ?> checked="true" <?php } ?> > No
													</div>			   
													<p class="help-block"></p>
												   </div>
												</div><br><br>
											 </div>
									      <div class="box-footer clearfix">
							                  <button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
							                  <button class="btn btn-sm btn-app pull-right" ><i class="fa fa-save"></i> Save</button>
							              </div> 
											 <?php echo form_close();  ?>
										 </div>	   
			        </div>  -->
      						<!-- receipt Settings -->
      						<!-- <div class="tab-pane" id="tab_receipt">
			             <h4 class="page-header">Receipt Settings</h4>  
			              <div class="">
	                                       <?php
											$attributes = array('autocomplete' => "off", 'role' => 'form');
											$id = 1;
											echo form_open('receipt/settings/edit/' . $id, $attributes);
											?> 					    	
											<div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Printing Type</label>
												   <div class="col-md-6">
												   <div class="col-md-5">
														<input type="radio" name="general[receipt]" value="0" <?php if ($general['receipt'] == 0) { ?> checked="true" <?php } ?> > Default(A4 Size)
													</div>
													<div class="col-md-5">
														<input type="radio" name="general[receipt]" value="1" <?php if ($general['receipt'] == 1) { ?> checked="true" <?php } ?> > Customized
													</div>			   
													<p class="help-block"></p>
												   </div>
												</div>
												<br><br>
											 </div>
											 <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Enable for Receipt Number Generate</label>
												   <div class="col-md-6">
												   <div class="col-md-5">
														<input type="radio" name="general[receipt_no_set]" value="0" <?php if ($general['receipt_no_set'] == 0) { ?> checked="true" <?php } ?> > Yes
													</div>
													<div class="col-md-5">
														<input type="radio" name="general[receipt_no_set]" value="1" <?php if ($general['receipt_no_set'] == 1) { ?> checked="true" <?php } ?> > No
													</div>			   
													<p class="help-block"></p>
												   </div>
												</div><br><br>
											 </div>
									      <div class="box-footer clearfix">
							                  <button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
							                  <button class="btn btn-sm btn-app pull-right" ><i class="fa fa-save"></i> Save</button>
							              </div> 
											 <?php echo form_close();  ?>
										 </div>	   
			        </div> -->
      						<!-- /receipt Settings -->
      						<!-- Scheme account number generate-->
      						<!-- <div class="tab-pane" id="tab_Schemeaccno">
			             <h4 class="page-header">Scheme Account Generate Settings</h4>  
			              <div class="">
	                                       <?php
											$attributes = array('autocomplete' => "off", 'role' => 'form');
											$id = 1;
											echo form_open('schemeacc_no/settings/edit/' . $id, $attributes);
											?> 					    	
											<div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Scheme A/c No Generate</label>
												   <div class="col-md-9">
												   <div class="col-md-3">
														<input type="radio" name="general[schemeacc_no_set]" value="0" <?php if ($general['schemeacc_no_set'] == 0) { ?> checked="true" <?php } ?> > Yes (Automatic)
													</div>
													<div class="col-md-3">
														<input type="radio" name="general[schemeacc_no_set]" value="1" <?php if ($general['schemeacc_no_set'] == 1) { ?> checked="true" <?php } ?> > No (Manual)
													</div>
													<div class="col-md-4">
														<input type="radio" name="general[schemeacc_no_set]" value="2" <?php if ($general['schemeacc_no_set'] == 2) { ?> checked="true" <?php } ?> > Integration (Jillaba or Etc..)
													</div>			   
													<p class="help-block"></p>
												   </div>
												</div><br><br>
											 </div>
									      <div class="box-footer clearfix">
							                  <button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
							                  <button class="btn btn-sm btn-app pull-right" ><i class="fa fa-save"></i> Save</button>
							              </div> 
											 <?php echo form_close();  ?>
										 </div>	   
			        </div>  -->
      						<!-- Scheme account number generate-->
      						<!-- branch Settings -->
      						<div class="tab-pane" id="tab_branch">
      							<h4 class="page-header">Branch Settings</h4>
      							<div class="">
      								<?php
										$attributes = array('autocomplete' => "off", 'role' => 'form');
										$id = 1;
										echo form_open('branch/settings/edit/' . $id, $attributes);
										?>
      								<div class="form-group">
      									<div class="row">
      										<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Branch Settings</label>
      										<div class="col-md-8">
      											<div class="col-md-4">
      												<input type="radio" name="general[branch_settings]" value="0" <?php if ($general['branch_settings'] == 0) { ?> checked="true" <?php } ?>> Default
      											</div>
      											<div class="col-md-4">
      												<input type="radio" name="general[branch_settings]" value="1" <?php if ($general['branch_settings'] == 1) { ?> checked="true" <?php } ?>> Branches
      											</div>
      											<!--	<div class="col-md-4">
														<a class="btn btn-primary" id="branch_enable" ><i class="fa fa-user-plus"></i> Proceed</a>
													</div>			   -->
      											<p class="help-block"></p>
      										</div>
      									</div><br>
      									<div class="row">
      										<label for="chargeseme_name" class="col-md-2 col-md-offset-1">Branch Wise login</label>
      										<div class="col-md-8">
      											<div class="col-md-4">
      												<input type="radio" name="general[branchWiseLogin]" value="0" <?php if ($general['branchWiseLogin'] == 0) { ?> checked="true" <?php } ?>> No
      											</div>
      											<div class="col-md-4">
      												<input type="radio" name="general[branchWiseLogin]" value="1" <?php if ($general['branchWiseLogin'] == 1) { ?> checked="true" <?php } ?>> Yes
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br>
      									<div class="row">
      										<label for="chargeseme_name" class="col-md-2 col-md-offset-1">Branchwise customer registration</label>
      										<div class="col-md-8">
      											<div class="col-md-4">
      												<input type="radio" name="general[is_branchwise_cus_reg]" value="0" <?php if ($general['is_branchwise_cus_reg'] == 0) { ?> checked="true" <?php } ?>> No
      											</div>
      											<div class="col-md-4">
      												<input type="radio" name="general[is_branchwise_cus_reg]" value="1" <?php if ($general['is_branchwise_cus_reg'] == 1) { ?> checked="true" <?php } ?>> Yes
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div></br>
      									<div class="row">
      										<label for="chargeseme_name" class="col-md-2 col-md-offset-1">Branchwise rate</label>
      										<div class="col-md-8">
      											<div class="col-md-4">
      												<input type="radio" name="general[is_branchwise_rate]" value="0" <?php if ($general['is_branchwise_rate'] == 0) { ?> checked="true" <?php } ?>> No
      											</div>
      											<div class="col-md-4">
      												<input type="radio" name="general[is_branchwise_rate]" value="1" <?php if ($general['is_branchwise_rate'] == 1) { ?> checked="true" <?php } ?>> Yes
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div><br><br>
      								<div class="box-footer clearfix">
      									<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      									<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      								</div>
      								<?php echo form_close();  ?>
      							</div>
      						</div>
      						<!-- branch Settings-->
      						<!-- currency setting tab -->
      						<div class="tab-pane" id="tab_2">
      							<?php
									$attributes = array('autocomplete' => "on", 'role' => 'form');
									echo form_open('settings/country/update/', $attributes);
									?>
      							<h4 class="page-header">Country</h4>
      							<!-- tab content -->
      							<div class="row">
      								<div class="form-group">
      									<label for="country" class="col-md-3 col-md-offset-1">Country</label>
      									<div class="col-md-4">
      										<input type="hidden" id="countryCurrval" name="comp[id_country]" value="<?php echo set_value('comp[id_country]', $comp['id_country']); ?>" />
      										<select class="form-control" id="countryCurr" name="general[countryCurr]"></select>
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="form-group">
      									<label for="currency_name" class="col-md-3 col-md-offset-1 ">Currency Name</label>
      									<div class="col-md-4">
      										<input type="text" class="form-control" id="currency_name" name="comp[currency_name]" value="<?php echo set_value('comp[currency_name]', $comp['currency_name']); ?>" placeholder="eg: Rupees" />
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="form-group">
      									<label for="currency_code" class="col-md-3 col-md-offset-1 ">Currency Code</label>
      									<div class="col-md-4">
      										<input type="text" class="form-control" id="currency_code" name="comp[currency_code]" value="<?php echo set_value('comp[currency_code]', $comp['currency_code']); ?>" maxlength="3" placeholder="eg: INR" />
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="form-group">
      									<label for="mob_code" class="col-md-3 col-md-offset-1 ">Mobile Code</label>
      									<div class="col-md-4">
      										<input type="text" class="form-control" id="mob_code" name="comp[mob_code]" value="<?php echo set_value('comp[mob_code]', $comp['mob_code']); ?>" placeholder="eg: +91" />
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="form-group">
      									<label for="mob_no_len" class="col-md-3 col-md-offset-1 ">Mobile.No Length</label>
      									<div class="col-md-4">
      										<input type="text" class="form-control" id="mob_no_len" name="comp[mob_no_len]" value="<?php echo set_value('comp[mob_no_len]', $comp['mob_no_len']); ?>" placeholder="eg: 10, 8" />
      										<p class="help-block"></p>
      									</div>
      								</div>
      							</div>
      							<!-- /tab content -->
      							<div class="box-footer clearfix">
      								<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      								<button type="submit" class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      							</div>
      							<?php echo form_close(); ?>
      						</div>
      						<!--/ currency setting tab -->
      						<!-- Database tab -->
      						<div class="tab-pane" id="tab_4">
      							<h4 class="page-header">Backup Database</h4>
      							<!-- tabs -->
      							<!-- backup tab content -->
      							<?php if ($userType <= 3) { ?>
      								<br /> <br />
      								<h5 class="page-header">Wallet Points Backup <button class="btn btn-default pull-right" id="btn-walletbackup"><i class="fa fa-save"></i> Download </button></h5>
      								<br /> <br />
      							<?php } ?>
      							<h4 class="page-header">Complete Backup</h4>
      							<div class="callout callout-danger">
      								<h4><i class="icon fa fa-warning"></i> Disclaimer before taking backup</h4>
      								<ol>
      									<li>You should back up your data on a regular basis.</li>
      									<li>This function only backs up your database, not your files.</li>
      									<li>Always verify that your backup files are complete, up-to-date and valid, even if you had a success message appear during the backup process.</li>
      									<li>Always check your data.</li>
      									<li>Never restore a backup on a live site.</li>
      								</ol>
      								<button class="btn btn-default" id="btn-backup"><i class="fa fa-save"></i> I have read the disclaimer. Please create a new backup</button>
      							</div>
      							<div class="row">
      								<div class="col-md-10 col-md-offset-1">
      									<div class="tab4le-responsive">
      										<table id="db_list" class="table table-bordered table-striped text-center">
      											<thead>
      												<th>ID</th>
      												<th>Date</th>
      												<th>Employee</th>
      												<th>File</th>
      											</thead>
      											<tbody>
      											</tbody>
      										</table>
      									</div>
      								</div>
      							</div>
      							<!--/. backup tab content -->
      							<!-- tabs -->
      						</div>
      						<!-- Database tab -->
      						<!-- Discount setting tab -->
      						<div class="tab-pane " id="tab_discount">
      							<h4 class="page-header">Discount Settings</h4>
      							<!-- tab content -->
      							<div class="">
      								<?php
										$attributes = array('autocomplete' => "off", 'role' => 'form');
										$id = 1;
										echo form_open('discount/settings/edit/' . $id, $attributes);
										?>
      								<div class="row">
      									<div class="col-sm-12">
      										<div class="form-group">
      											<label class="checkbox-inline">
      												<input type="checkbox" id="free_first_payment" name="discount[free_first_payment]" value="1" <?php if ($discount['free_first_payment'] == 1) { ?>checked="true" <?php } ?> />
      												First free payment
      											</label>
      										</div>
      									</div>
      								</div>
      								<!-- /tab content -->
      								<div class="box-footer clearfix">
      									<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      									<button type="submit" class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      								</div>
      								<?php echo form_close(); ?>
      							</div>
      						</div>
      						<!-- /Discount setting tab -->
      						<!-- clear Database -->
      						<div class="tab-pane" id="tab_5">
      							<h4 class="page-header">Clear Database</h4>
      							<!-- clear tab content -->
      							<div class="alert alert-danger alert-dismissable" id="clr_alert" style="display:none;">
      								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      								<span></span>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<div class="btn-group" data-toggle="buttons">
      										<label class="btn btn-default">
      											<input type="radio" name="clear_data" value="0"><i class="icon fa fa-check"></i> Selected
      										</label>
      										<label class="btn btn-default active">
      											<input type="radio" name="clear_data" value="1" checked="checked"><i class="icon fa fa-remove"></i> Clear All
      										</label>
      									</div>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" class="sel_block" name="clr_db" value="scheme" /> Schemes</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" class="sel_block" name="clr_db" value="customer" /> Customers</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" class="sel_block" name="clr_db" value="account" /> Scheme Accounts & Payments</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" name="clr_db" class="sel_block" value="wallet" /> Wallet</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" name="clr_db" class="sel_block" value="promotions" /> Promotions</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" name="clr_db" class="sel_block" value="log" /> Log</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" name="clr_db" class="sel_block text-red" value="metal_rates" /> Metal Rates</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" name="clr_db" class="sel_block text-red" value="daily_collection" /> Daily Collection</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<label><input type="checkbox" name="clr_db" class="sel_block text-red" value="access" /> Access</label>
      								</div>
      							</div>
      							<div class="row">
      								<div class="col-sm-6 col-sm-offset-1">
      									<a class="btn btn-primary" id="clr_proceed"><i class="fa fa-remove"></i> Proceed</a>
      								</div>
      							</div>
      							<!--/ clear tab content -->
      						</div>
      						<!--/ clear Database -->
      						<!-- Gateway API -->
      						<div class="tab-pane" id="tab_6">
      							<div class="row">
      								<?php if ($this->session->userdata('is_branchwise_cus_reg') == 1) { ?>
      									<div class="col-md-5">
      										<div class="form-group" style="    margin-left: 50px;">
      											<label>Select Branch &nbsp;&nbsp;</label>
      											<select id="branch_select" class="form-control" style="width:150px;"></select>
      											<input id="id_branch" name="scheme[id_branch]" type="hidden" value="" />
      											<input id="is_branchwise_cus_reg" name="scheme[id_branch]" type="hidden" value="<?php echo $this->session->userdata('is_branchwise_cus_reg'); ?>" />
      										</div>
      									</div>
      								<?php } ?>
      							</div>
      							<h4 class="page-header">Gateway Settings</h4>
      							<ul class="nav nav-tabs" id="gateway">
      								<li><a href="#PayU" class="gateway_list" value="1" data-toggle="tab">PayU</a></li>
      								<li><a href="#hdfc" class="gateway_list" value="2" data-toggle="tab">HDFC</a></li>
      								<li><a href="#techprocess" class="gateway_list" value="3" data-toggle="tab">Tech Process</a></li>
      							</ul>
      							<form id="gateway_submit">
      								<div id="gateway_type" style="display:none">
      									<ul class="nav nav-tabs" id="gateway_type">
      										<li><a href="#" value="0" class="gateway_type" data-toggle="tab">Demo Gateway</a></li>
      										<li><a href="#" value="1" class="gateway_type" data-toggle="tab">Pro Gateway</a></li>
      									</ul>
      								</div>
      								<div class="tab-pane" id="PayU">
      									<!-- Gateway content -->
      									<!-- Custom Tabs -->
      									<input type="hidden" id="pg_code" name="">
      									<input type="hidden" id="type" name="">
      									<input type="hidden" id="id_pg" name="">
      									<div class="nav-tabs-custom">
      										<div class="tab-content">
      											<div class="tab-pane" id="gateway_content">
      												<div class="">
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Key</label>
      															<div class="col-md-4">
      																<input type="text" class="form-control" name="demo[param_1]" id="param_1" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Salt</label>
      															<div class="col-md-4">
      																<input type="text" class="form-control" name="demo[param_2]" id="param_2" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Merchant Code</label>
      															<div class="col-md-4">
      																<input type="text" class="form-control" name="demo[param_3]" id="param_3" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 "> IV</label>
      															<div class="col-md-4">
      																<input type="text" class="form-control" name="demo[param_4]" id="param_4" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Verify URL</label>
      															<div class="col-md-6">
      																<input type="text" class="form-control" name="demo[api_url]" id="api_url" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Gateway Name</label>
      															<div class="col-md-4">
      																<input type="text" class="form-control" name="demo[pg_name]" id="pg_name" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Default</label>
      															<div class="col-md-4">
      																<input type="checkbox" id="is_default" name="demo[is_default]" value="1" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      												</div>
      											</div>
      											<div class="tab-pane" id="gateway_content">
      												<div class="">
      													<!--   <?php
																	$attributes = array('autocomplete' => "off", 'role' => 'form');
																	echo form_open('gateway/settings/edit_pro/' . $pro['id_pg'], $attributes);
																	?> -->
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Key</label>
      															<div class="col-md-4">
      																<input type="text" class="form-control" name="pro[param_1]" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Salt</label>
      															<div class="col-md-4">
      																<input type="text" class="form-control" name="pro[param_2]" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Verify URL</label>
      															<div class="col-md-6">
      																<input type="text" class="form-control" name="pro[api_url]" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Gateway Name</label>
      															<div class="col-md-4">
      																<input type="text" class="form-control" name="demo[pg_name]" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="form-group">
      															<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Default</label>
      															<div class="col-md-4">
      																<input type="checkbox" id="is_default" name="pro[is_default]" />
      																<p class="help-block"></p>
      															</div>
      														</div>
      													</div>
      													<!-- <?php echo form_close(); ?>-->
      												</div>
      											</div>
      										</div><!-- /.tab-content -->
      									</div><!-- nav-tabs-custom -->
      								</div>
      								<div class="overlay" style="display:none;">
      									<i class="fa fa-refresh fa-spin"></i>
      								</div>
      								<div class="box-footer clearfix">
      									<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      									<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save" id="submit"></i> Save</button>
      								</div>
      							</form>
      						</div>
      						<!--/ Gateway API -->
      						<!-- SMS API -->
      						<!--  <div class="tab-pane" id="tab_7">
			             <h4 class="page-header">SMS API Settings</h4>  
			              <div class="">
	                                       <?php
											$attributes = array('autocomplete' => "off", 'role' => 'form');
											echo form_open('sms/settings/edit/' . $sms['id_sms_api'], $attributes);
											?> 
											 <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Sender ID</label>
												   <div class="col-md-4">
													 <input type="text" class="form-control" name="sms[sms_sender_id]" value="<?php echo set_value('sms[sms_sender_id]', $sms['sms_sender_id']); ?>" maxlength="6" />
													<p class="help-block"></p>
												   </div>
												</div>
											 </div>
									    	 <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">SMS URL</label>
												   <div class="col-md-6">
													 <input type="text" class="form-control" name="sms[sms_url]" value="<?php echo set_value('sms[sms_url]', $sms['sms_url']); ?>" />
													<p class="help-block"></p>
												   </div>
												</div>
											 </div>	 
									      <div class="box-footer clearfix">
							                  <button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
							                  <button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
							              </div> 
											 <?php echo form_close(); ?>
										 </div>	   
			        </div>  -->
      						<div class="tab-pane" id="tab_7">
      							<div class="nav-tabs-custom">
      								<ul class="nav nav-tabs">
      									<li class="active"><a href="#pro_api" data-toggle="tab">Promotion API </a></li>
      									<li><a href="#sms_api" data-toggle="tab">OTP API </a></li>
      								</ul>
      								<div class="tab-content">
      									<div class="tab-pane active" id="pro_api">
      										<h2 class="page-header">Promotion API Settings</h2> </br></br>
      										<div class="">
      											<?php
													$attributes = array('autocomplete' => "off", 'role' => 'form');
													echo form_open('promotion/settings/edit/' . $promotion['id_promotion_api'], $attributes);
													?>
      											<div class="row">
      												<div class="form-group">
      													<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Sender ID</label>
      													<div class="col-md-4">
      														<input type="text" class="form-control" name="promotion[promotion_sender_id]" value="<?php echo set_value('promotion[promotion_sender_id]', $promotion['promotion_sender_id']); ?>" maxlength="6" />
      														<p class="help-block"></p>
      													</div>
      												</div>
      											</div>
      											<div class="row">
      												<div class="form-group">
      													<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Promotion URL</label>
      													<div class="col-md-6">
      														<input type="text" class="form-control" name="promotion[promotion_url]" value="<?php echo set_value('promotion[promotion_url]', $promotion['promotion_url']); ?>" />
      														<p class="help-block"></p>
      													</div>
      												</div>
      											</div>
      											<div class="box-footer clearfix">
      												<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      												<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      											</div>
      											<?php echo form_close(); ?>
      										</div>
      									</div>
      									<div class="tab-pane" id="sms_api">
      										<h2 class="page-header">Sms API Settings</h2> </br></br>
      										<div class="">
      											<?php
													$attributes = array('autocomplete' => "off", 'role' => 'form');
													echo form_open('sms/settings/edit/' . $sms['id_sms_api'], $attributes);
													?>
      											<div class="row">
      												<div class="form-group">
      													<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Sender ID</label>
      													<div class="col-md-4">
      														<input type="text" class="form-control" name="sms[sms_sender_id]" value="<?php echo set_value('sms[sms_sender_id]', $sms['sms_sender_id']); ?>" maxlength="6" />
      														<p class="help-block"></p>
      													</div>
      												</div>
      											</div>
      											<div class="row">
      												<div class="form-group">
      													<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">SMS URL</label>
      													<div class="col-md-6">
      														<input type="text" class="form-control" name="sms[sms_url]" value="<?php echo set_value('sms[sms_url]', $sms['sms_url']); ?>" />
      														<p class="help-block"></p>
      													</div>
      												</div>
      											</div>
      											<div class="box-footer clearfix">
      												<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      												<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      											</div>
      											<?php echo form_close(); ?>
      										</div>
      									</div>
      								</div><!-- /.tab-content -->
      							</div><!-- nav-tabs-custom -->
      						</div>
      						<!-- /SMS API -->
      						<!-- otp and sms credit -->
      						<div class="tab-pane" id="otpcredit">
      							<div class="nav-tabs-custom">
      								<ul class="nav nav-tabs">
      									<li class="active"><a href="#crt_prom" data-toggle="tab">Promotion Credit</a></li>
      									<li><a href="#crt_sms" data-toggle="tab">OTP Credit </a></li>
      								</ul>
      								<div class="tab-content">
      									<div class="tab-pane active" id="crt_prom">
      										<h2 class="page-header">Promotion Credit Settings</h2> </br></br>
      										<div class="">
      											<?php
													$attributes = array('autocomplete' => "off", 'role' => 'form');
													echo form_open('promotion_credit/settings/edit/' . $promotion_crt['id_promotion_api'], $attributes);
													?>
      											<div class="row">
      												<div class="form-group">
      													<label for="chargeseme_name" class="col-md-2 ">Credit Promotion </label>
      													<div class="col-md-1">
      														<span> <input type="checkbox" id="enable_promot" class="enable_promotion" />
      															<input type="hidden" id="enable_promot1" class="enable_promotion" name="promotion_crt[enable_promotion]" value="" /> </span>
      													</div>
      													<div class="col-md-6">
      														<input type="text" class="form-control" id="create_promotion" name="promotion_crt[credit_promotion]" style="width:64%;" value="<?php echo set_value('promotion_crt[credit_promotion]', $promotion_crt['credit_promotion']); ?>" />
      														<p class="help-block"></p>
      													</div>
      												</div>
      											</div>
      											<div class="row">
      												<div class="form-group">
      													<label for="chargeseme_name" class="col-md-2 ">Available Promotion</label>
      													<div class="col-md-1">
      													</div>
      													<div class="col-md-6">
      														<input type="text" class="form-control" name="promotion_crt[debit_promotion]" style="width:64%;" value="<?php echo set_value('promotion_crt[debit_promotion]', $promotion_crt['debit_promotion']); ?>" readonly="true" />
      														<p class="help-block"></p>
      													</div>
      												</div>
      											</div>
      											<div class="box-footer clearfix">
      												<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      												<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      											</div>
      											<?php echo form_close(); ?>
      										</div>
      									</div>
      									<div class="tab-pane" id="crt_sms">
      										<h2 class="page-header">Sms Credit Settings</h2> </br></br>
      										<div class="">
      											<?php
													$attributes = array('autocomplete' => "off", 'role' => 'form');
													echo form_open('otp_credit/settings/edit/' . $otp_crt['id_sms_api'], $attributes);
													?>
      											<div class="row">
      												<div class="form-group">
      													<label for="chargeseme_name" class="col-md-2">Credit Otp</label>
      													<div class="col-md-1">
      														<span> <input type="checkbox" id="enable_otpsms" class="enable_otp" />
      															<input type="hidden" id="enable_otp1" name="otp_crt[enable_otp]" value="" /> </span>
      													</div>
      													<div class="col-md-6">
      														<input type="text" id="credit_sms" class="form-control" name="otp_crt[credit_sms]" style="width:64%;" value="<?php echo set_value('otp_crt[credit_sms]', $otp_crt['credit_sms']); ?>" />
      														<p class="help-block"></p>
      													</div>
      												</div>
      											</div>
      											<div class="row">
      												<div class="form-group">
      													<label for="chargeseme_name" class="col-md-2">Available Otp</label>
      													<div class="col-md-1">
      													</div>
      													<div class="col-md-6">
      														<input type="text" class="form-control" name="otp_crt[debit_sms]" style="width:64%;" value="<?php echo set_value('otp_crt[debit_sms]', $otp_crt['debit_sms']); ?>" readonly="true" />
      														<p class="help-block"></p>
      													</div>
      												</div>
      											</div>
      											<div class="box-footer clearfix">
      												<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      												<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      											</div>
      											<?php echo form_close(); ?>
      										</div>
      									</div>
      								</div><!-- /.tab-content -->
      							</div><!-- nav-tabs-custom -->
      						</div><!-- nav-tabs-custom -->
      						<!-- otp and sms credit -->
      						<!-- Wallet settings -->
      						<div class="tab-pane" id="tab_Wallet">
      							<h4 class="page-header">Wallet Settings</h4>
      							<div class="">
      								<?php
										$attributes = array('autocomplete' => "off", 'role' => 'form');
										$id = 1;
										echo form_open('wallet/settings/edit/' . $id, $attributes);
										?>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 "> Is wallet Required</label>
      										<div class="col-md-6">
      											<div class="col-md-5">
      												<input type="radio" name="general[allow_wallet]" value="0" <?php if ($general['allow_wallet'] == 0) { ?> checked="true" <?php } ?>> No
      											</div>
      											<div class="col-md-5">
      												<input type="radio" name="general[allow_wallet]" value="1" <?php if ($general['allow_wallet'] == 1) { ?> checked="true" <?php } ?>> Yes
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br><br>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 "> Account Creation (Customer)</label>
      										<div class="col-md-6">
      											<div class="col-md-5">
      												<input type="radio" name="general[wallet_account_type]" value="0" <?php if ($general['wallet_account_type'] == 0) { ?> checked="true" <?php } ?>> Manual
      											</div>
      											<div class="col-md-5">
      												<input type="radio" name="general[wallet_account_type]" value="1" <?php if ($general['wallet_account_type'] == 1) { ?> checked="true" <?php } ?>> Automatic
      											</div>
      											<p class="help-block"></p>
      										</div><br><br>
      										<div class="form-group">
      											<label for="chargeseme_name" class="col-md-3 col-md-offset-1 "> Account Creation (Employee)</label>
      											<div class="col-md-6">
      												<div class="col-md-5">
      													<input type="radio" name="general[emp_wallet_account_type]" value="0" <?php if ($general['emp_wallet_account_type'] == 0) { ?> checked="true" <?php } ?>> Manual
      												</div>
      												<div class="col-md-5">
      													<input type="radio" name="general[emp_wallet_account_type]" value="1" <?php if ($general['emp_wallet_account_type'] == 1) { ?> checked="true" <?php } ?>> Automatic
      												</div>
      												<p class="help-block"></p>
      											</div><br><br>
      										</div>
      										<!-- Use Wallet Amount Setting -->
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Use Wallet Amount For Chit </label>
      										<div class="col-md-6">
      											<div class="col-md-5">
      												<input type="radio" name="general[useWalletForChit]" value="0" <?php if ($general['useWalletForChit'] == 0) { ?> checked="true" <?php } ?>> No
      											</div>
      											<div class="col-md-5">
      												<input type="radio" name="general[useWalletForChit]" value="1" <?php if ($general['useWalletForChit'] == 1) { ?> checked="true" <?php } ?>> Yes
      											</div>
      										</div><br><br>
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Wallet Integration</label>
      										<div class="col-md-6">
      											<div class="col-md-5">
      												<input type="radio" name="general[walletIntegration]" value="0" <?php if ($general['walletIntegration'] == 0) { ?> checked="true" <?php } ?>> No
      											</div>
      											<div class="col-md-5">
      												<input type="radio" name="general[walletIntegration]" value="1" <?php if ($general['walletIntegration'] == 1) { ?> checked="true" <?php } ?>> Yes(Like SSS)
      											</div>
      										</div>
      										<!-- Use Wallet Amount Setting -->
      									</div><br><br>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Wallet Balance Type</label>
      										<div class="col-md-6">
      											<div class="col-md-5">
      												<input type="radio" id="wallet_balance_type" name="general[wallet_balance_type]" value="0" <?php if ($general['wallet_balance_type'] == 0) { ?> checked="true" <?php } ?>> Amount
      											</div>
      											<div class="col-md-5">
      												<input type="radio" id="wallet_balance_type" name="general[wallet_balance_type]" value="1" <?php if ($general['wallet_balance_type'] == 1) { ?> checked="true" <?php } ?>> Points
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br><br>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 "></label>
      										<label>Points</label>
      										<input type="text" id="wallet_points" name="general[wallet_points]" style="width: 10%;" disabled="true" value='<?php echo $general['wallet_points']; ?>'>
      										<label>Amount</label>
      										<input type="text" id="wallet_amt_per_points" name="general[wallet_amt_per_points]" style="width: 10%;" disabled="true" value='<?php echo $general['wallet_amt_per_points']; ?>'>
      									</div>
      								</div>
      								<p class="help-block">NOTE : For employees wallet account has to be created manually.</p>
      								<div class="box-footer clearfix">
      									<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      									<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      								</div>
      								<?php echo form_close();  ?>
      							</div>
      						</div>
      						<!-- Wallet settings -->
      						<!-- Referral setting -->
      						<div class="tab-pane" id="tab_Referral">
      							<h4 class="page-header">Referral Settings</h4>
      							<div class="">
      								<?php
										$attributes = array('autocomplete' => "off", 'role' => 'form');
										$id = 1;
										echo form_open('refferbenifits/settings/edit/' . $id, $attributes);
										?>
      								<div class="row">
      									<div class="form-group">
      										<h5 for="chargeseme_name" class="col-md-4"> Referal required </h5>
      										<div class="col-md-6">
      											<div class="col-md-5">
      												<input type="radio" id="allow_referral" name="general[allow_referral]" value="0" <?php if ($general['allow_referral'] == 0) { ?> checked="true" <?php } ?>> No
      											</div>
      											<div class="col-md-5">
      												<input type="radio" id="allow_referral" name="general[allow_referral]" value="1" <?php if ($general['allow_referral'] == 1) { ?> checked="true" <?php } ?>> Yes
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br><br>
      								</div>
      								<div class="row">
      									<h4 class="col-md-3">Customer Settings </h4></br>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<h5 for="chargeseme_name" class="col-md-4"> Benefits Plan </h5>
      										<div class="col-md-6">
      											<div class="col-md-5">
      												<input type="radio" id="sch_benefit" name="general[cusplan_type]" value="0" <?php if ($general['cusplan_type'] == 0) { ?> checked="true" <?php } ?>> Scheme Plan
      											</div>
      											<div class="col-md-5">
      												<input type="radio" id="walllet_benefit" name="general[cusplan_type]" value="1" <?php if ($general['cusplan_type'] == 1) { ?> checked="true" <?php } ?>> Wallet Plan
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br><br>
      								</div>
      								<div class="row">
      									<h4 class="col-md-3">Employee Settings </h4></br>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<h5 for="chargeseme_name" class="col-md-4"> Benefits Plan </h5>
      										<div class="col-md-6">
      											<div class="col-md-5">
      												<input type="radio" name="general[empplan_type]" value="0" <?php if ($general['empplan_type'] == 0) { ?> checked="true" <?php } ?>> Scheme Plan
      											</div>
      											<div class="col-md-5">
      												<input type="radio" name="general[empplan_type]" value="1" <?php if ($general['empplan_type'] == 1) { ?> checked="true" <?php } ?>> Wallet Plan
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br><br>
      								</div>
      								<div class="row">
      									<h4 class="col-md-5">Referral Benefits Credit Settings </h4>
      								</div>
      								<h5></h5>
      								<div class="row">
      									<div class="form-group">
      										<h5 for="chargeseme_name" class="col-md-4">Customer Referral<br /> </h5>
      										<div class="col-md-8">
      											<div class="col-md-6">
      												<input type="radio" name="general[cusbenefitscrt_type]" value="0" <?php if ($general['cusbenefitscrt_type'] == 0) { ?> checked="true" <?php } ?>> Get referral code only once<br /> (In scheme joining)
      											</div>
      											<div class="col-md-6">
      												<input type="radio" name="general[cusbenefitscrt_type]" value="1" <?php if ($general['cusbenefitscrt_type'] == 1) { ?> checked="true" <?php } ?>> Get referral code multiple times<br /> (In scheme joining)
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br><br>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<h5 for="chargeseme_name" class="col-md-4">Employee Referral <br /> </h5>
      										<div class="col-md-8">
      											<div class="col-md-6">
      												<input type="radio" name="general[empbenefitscrt_type]" value="0" <?php if ($general['empbenefitscrt_type'] == 0) { ?> checked="true" <?php } ?>> Get referal code only once<br /> (In scheme joining)
      											</div>
      											<div class="col-md-6">
      												<input type="radio" name="general[empbenefitscrt_type]" value="1" <?php if ($general['empbenefitscrt_type'] == 1) { ?> checked="true" <?php } ?>> Get referral code multiple times<br /> (In scheme joining)
      											</div>
      											<p class="help-block"></p>
      										</div><br><br><br><br>
      										<h5 for="chargeseme_name" class="col-md-4"><br /> </h5>
      										<div class="col-md-8">
      											<div class="col-md-6">
      												<input type="radio" name="general[emp_ref_by]" value="0" <?php if ($general['emp_ref_by'] == 0) { ?> checked="true" <?php } ?>> Employee code
      											</div>
      											<div class="col-md-6">
      												<input type="radio" name="general[emp_ref_by]" value="1" <?php if ($general['emp_ref_by'] == 1) { ?> checked="true" <?php } ?>> Mobile
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br><br>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<h5 for="chargeseme_name" class="col-md-4 "> Credit on successive scheme joining </h5>
      										<div class="col-md-8">
      											<div class="col-md-6">
      												<input type="radio" name="general[schrefbenifit_secadd]" value="0" <?php if ($general['schrefbenifit_secadd'] == 0) { ?> checked="true" <?php } ?>> Yes
      											</div>
      											<div class="col-md-6">
      												<input type="radio" name="general[schrefbenifit_secadd]" value="1" <?php if ($general['schrefbenifit_secadd'] == 1) { ?> checked="true" <?php } ?>> No
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div><br><br>
      								</div>
      								<div class="box-footer clearfix">
      									<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      									<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      								</div>
      								<?php echo form_close();  ?>
      							</div>
      						</div>
      						<!-- Referral settings -->
      						<!-- Scheme Grouping setting-->
      						<!--  <div class="tab-pane" id="tab_Schemegroup">
			             <h4 class="page-header">Scheme Group Settings</h4>  
			              <div class="">
	                                       <?php
											$attributes = array('autocomplete' => "off", 'role' => 'form');
											$id = 1;
											echo form_open('scheme_group/settings/edit/' . $id, $attributes);
											?> 					    	
											<div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-4 col-md-offset-1 ">Enable for Scheme Group Setting</label>
												   <div class="col-md-6">
												   <div class="col-md-5">
												   <input type="radio" name="general[scheme_group_set]" value="0" <?php if ($general['scheme_group_set'] == 0) { ?> checked="true" <?php } ?> >  YES
													</div>
													<div class="col-md-5">
														  <input type="radio" name="general[scheme_group_set]" value="1" <?php if ($general['scheme_group_set'] == 1) { ?> checked="true" <?php } ?> >  NO
													</div>			   
													<p class="help-block"></p>
												   </div>
												</div><br><br>
											 </div>
								           <div class="box-footer clearfix">
							                  <button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
							                  <button class="btn btn-sm btn-app pull-right" ><i class="fa fa-save"></i> Save</button>
							              </div> 
											 <?php echo form_close();  ?>
										 </div>	   
			        </div>  -->
      						<!-- Scheme Grouping setting-->
      						<!-- Mail Settings -->
      						<div class="tab-pane" id="tab_8">
      							<h4 class="page-header">Mail Settings</h4>
      							<div class="">
      								<?php
										$attributes = array('autocomplete' => "off", 'role' => 'form');
										echo form_open('mail/settings/edit/' . $mail['id_company'], $attributes);
										?>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Mail Server</label>
      										<div class="col-md-4">
      											<input type="text" class="form-control" name="mail[mail_server]" value="<?php echo set_value('mail[mail_server]', $mail['mail_server']); ?>" />
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Mail Password</label>
      										<div class="col-md-6">
      											<input type="password" class="form-control" name="mail[mail_password]" value="<?php echo set_value('mail[mail_password]', $mail['mail_password']); ?>" />
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Send Mail Through</label>
      										<div class="col-md-6">
      											<div class="col-md-4">
      												<input type="radio" id="send_through" name="mail[send_through]" value="0" <?php if ($mail['send_through'] == 0) { ?> checked="true" <?php } ?>> PHP Mail
      											</div>
      											<div class="col-md-4">
      												<input type="radio" id="send_through" name="mail[send_through]" value="1" <?php if ($mail['send_through'] == 1) { ?> checked="true" <?php } ?>> Gmail SMTP
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div><br>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Server Type</label>
      										<div class="col-md-8">
      											<div class="col-md-4">
      												<input type="radio" id="server_type" name="mail[server_type]" value="1" <?php if ($mail['server_type'] == 1) { ?> checked="true" <?php } ?>> Shared Hosting
      											</div>
      											<div class="col-md-4">
      												<input type="radio" id="server_type" name="mail[server_type]" value="2" <?php if ($mail['server_type'] == 2) { ?> checked="true" <?php } ?>> Dedicated Server
      											</div>
      										</div>
      										<p class="help-block"></p>
      									</div>
      								</div><br>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">SMTP User</label>
      										<div class="col-md-6">
      											<div class="col-md-6">
      												<input type="text"
      													class="mail_settings" style="width: 100%;
  text-align: left;" id="smtp_user" name="mail[smtp_user]" value='<?php echo $mail['smtp_user'] ?>'>
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div><br>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">SMTP Password</label>
      										<div class="col-md-6">
      											<div class="col-md-6">
      												<input type="text" class="mail_settings" style="width: 100%;
  text-align: left;" id="smtp_user" id="smtp_pass" name="mail[smtp_pass]" value='<?php echo $mail['smtp_pass'] ?>'>
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div><br>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">SMTP Host</label>
      										<div class="col-md-6">
      											<div class="col-md-6">
      												<select id="select_host" name="mail[smtp_host]" class="form-control">
      													<option id="type_1" <?php if ($mail['server_type'] == 2) { ?> selected="true" <?php } ?> value="ssl://smtp.googlemail.com">ssl://smtp.googlemail.com </option>
      													<option id="type_2" <?php if ($mail['server_type'] == 1) { ?> selected="true" <?php } ?> value="ssl://mi3-lr3.supercp.com">ssl://mi3-lr3.supercp.com</option>
      												</select>
      											</div>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div><br>
      								<div class="box-footer clearfix">
      									<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      									<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      								</div>
      								<?php echo form_close(); ?>
      							</div>
      						</div>
      						<!-- /Mail Settings -->
      						<!-- Limit Settings -->
      						<div class="tab-pane" id="tab_9">
      							<h4 class="page-header">Limit Settings</h4>
      							<div class="">
      								<?php
										$attributes = array('autocomplete' => "off", 'role' => 'form');
										$id = 1;
										echo form_open('limit/settings/edit/' . $id, $attributes);
										?>
      								<div class="row">
      									<div class="form-group">
      										<label class="col-md-2 col-md-offset-1 ">Limit</label>
      										<div class="col-md-3" style="text-align: center">
      											<label style="align-content: center">Enable / Disable</label>
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2">
      											<label style="align-content: center">Limit Count</label>
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-3">
      											<label style="align-content: center">Available Count</label>
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Customer</label>
      										<div class="col-md-3" style="text-align: center">
      											<input type="checkbox" id="limit_cust" name="limit[limit_cust]" <?php if ($limit['limit_cust'] == 1) { ?>checked="checked" <?php } ?> value="1" />
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2" style="text-align: center">
      											<input type="number" align="middle" class="form-control" name="limit[cust_max_count]" id="cust_max_count" value="<?php echo set_value('limit[cust_max_count]', $limit['cust_max_count']); ?>" />
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2" style="text-align: center">
      											<input type="number" class="form-control" name="limit[cust_count]" value="<?php echo set_value('cust_count', $cus_count); ?>" disabled="true" />
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Scheme</label>
      										<div class="col-md-3" style="text-align: center">
      											<input type="checkbox" id="limit_sch" name="limit[limit_sch]" <?php if ($limit['limit_sch'] == 1) { ?>checked="checked" <?php } ?> value="1" />
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2" style="text-align: center">
      											<input type="number" class="form-control" id="sch_max_count" name="limit[sch_max_count]" value="<?php echo set_value('limit[sch_max_count]', $limit['sch_max_count']); ?>" maxlength="6" />
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2" style="text-align: center">
      											<input type="number" class="form-control" name="limit[sch_count]" value="<?php echo set_value('scheme_count', $scheme_count); ?>" disabled="true" />
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Branch</label>
      										<div class="col-md-3" style="text-align: center">
      											<input type="checkbox" id="limit_branch" name="limit[limit_branch]" <?php if ($limit['limit_branch'] == 1) { ?>checked="checked" <?php } ?> value="1" />
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2" style="text-align: center">
      											<input type="number" class="form-control" id="branch_max_count" name="limit[branch_max_count]" value="<?php echo set_value('limit[branch_max_count]', $limit['branch_max_count']); ?>" maxlength="6" />
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2" style="text-align: center">
      											<input type="number" class="form-control" name="limit[branch_count]" value="<?php echo set_value('scheme_count', $scheme_count); ?>" disabled="true" />
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="row">
      									<div class="form-group">
      										<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Scheme Account</label>
      										<div class="col-md-3" style="text-align: center">
      											<input type="checkbox" id="limit_sch_acc" name="limit[limit_sch_acc]" <?php if ($limit['limit_sch_acc'] == 1) { ?>checked="checked" <?php } ?> value="1" />
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2" style="text-align: center">
      											<input type="number" class="form-control" id="sch_acc_max_count" name="limit[sch_acc_max_count]" value="<?php echo set_value('limit[sch_acc_max_count]', $limit['sch_acc_max_count']); ?>" maxlength="6" />
      											<p class="help-block"></p>
      										</div>
      										<div class="col-md-2" style="text-align: center">
      											<input type="number" class="form-control" name="limit[sch_acc_count]" value="<?php echo set_value('sch_acc_count', $sch_acc_count); ?>" disabled="true" />
      											<p class="help-block"></p>
      										</div>
      									</div>
      								</div>
      								<div class="box-footer clearfix">
      									<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
      									<button class="btn btn-sm btn-app pull-right"><i class="fa fa-save"></i> Save</button>
      								</div>
      								<?php echo form_close(); ?>
      							</div>
      						</div>
      						<!-- /Limit Settings -->
      						<!-- / coded by ARVK-->
      					<!-- New KYC Settings Tab -->
							<div class="tab-pane" id="tab_kyc">
							<?php
							$id = 1;
							 echo form_open('kyc/settings/update', ['class' => 'form-horizontal']);
							  ?>
      							<div class="row">
									<div class="col-md-12">
										<h4 class="page-header text-primary" style="margin-top:0; border-bottom:2px solid #3c8dbc; padding-bottom:10px;"><i class="fa fa-id-card-o"></i> KYC Configuration Dashboard</h4>
									</div>
								</div>

								<div class="row">
									<!-- Left Column: Global Policies -->
									<div class="col-md-5">
										
										<!-- Master Switch -->
										<div class="box box-primary">
											<div class="box-header with-border">
												<h3 class="box-title"><i class="fa fa-cogs"></i> System Policy</h3>
											</div>
											<div class="box-body">
												<div class="form-group" style="margin-bottom:0px;">
													<label class="col-md-5 control-label">KYC Enforced</label>
													<div class="col-md-7">
														<div class="radio inline" style="display:inline-block;">
															<label><input type="radio" name="kyc[kyc_required]" id="kyc_required_yes" value="1" <?php if (isset($kyc['kyc_required']) && $kyc['kyc_required'] == 1) { echo 'checked="true"'; } ?>> Yes</label>
														</div>
														<div class="radio inline" style="display:inline-block; margin-left:15px;">
															<label><input type="radio" name="kyc[kyc_required]" id="kyc_required_no" value="0" <?php if (!isset($kyc['kyc_required']) || $kyc['kyc_required'] == 0) { echo 'checked="true"'; } ?>> No</label>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div id="kyc_settings_block" <?php if (!isset($kyc['kyc_required']) || $kyc['kyc_required'] == 0) { echo 'style="display:none;"'; } ?>>
											<!-- Operational Mode -->
											<div class="box box-solid box-default" style="border:1px solid #d2d6de;">
												<div class="box-header with-border" style="background-color:#f4f4f4;">
													<h3 class="box-title"><i class="fa fa-sitemap"></i> Operational Mode</h3>
												</div>
												<div class="box-body">
													<div class="form-group" style="margin-bottom:0px;">
														<div class="col-md-12">
															<div class="radio" style="margin-top:5px;">
																<label><input type="radio" name="kyc[kyc_mode]" id="kyc_mode_scheme" value="0" <?php if (!isset($kyc['kyc_mode']) || $kyc['kyc_mode'] == 0) { echo 'checked="true"'; } ?>> <strong class="text-primary">Scheme Wise</strong> - Rules bound to specific schemes.</label>
															</div>
															<div class="radio" style="margin-bottom:5px;">
																<label><input type="radio" name="kyc[kyc_mode]" id="kyc_mode_customer" value="1" <?php if (isset($kyc['kyc_mode']) && $kyc['kyc_mode'] == 1) { echo 'checked="true"'; } ?>> <strong class="text-primary">Customer Wise</strong> - Rules bound to customer profile.</label>
															</div>
														</div>
													</div>
												</div>
											</div>

											<!-- Approval Policy -->
											<div class="box box-solid box-danger" style="border:1px solid #dd4b39;">
												<div class="box-header with-border" style="background-color:#dd4b39; color:#fff;">
													<h3 class="box-title"><i class="fa fa-ban"></i> Payment Restriction</h3>
												</div>
												<div class="box-body">
													<div class="form-group" style="margin-bottom:0;">
														<label class="col-md-12 text-muted" style="margin-bottom:10px; font-weight:normal;">If a customer's KYC is incomplete or pending review:</label>
														<div class="col-md-12">
															<div class="radio inline" style="display:inline-block;">
																<label><input type="radio" name="kyc[kyc_allow_type]" value="0" <?php if (!isset($kyc['kyc_allow_type']) || $kyc['kyc_allow_type'] == 0) { echo 'checked="true"'; } ?>> Allow Payment</label>
															</div>
															<div class="radio inline" style="display:inline-block; margin-left:15px;">
																<label><input type="radio" name="kyc[kyc_allow_type]" value="1" <?php if (isset($kyc['kyc_allow_type']) && $kyc['kyc_allow_type'] == 1) { echo 'checked="true"'; } ?>> Block Payment</label>
															</div>
														</div>
													</div>
												</div>
											</div>

											<!-- Integration Details -->
											<div class="box box-solid box-success" style="border:1px solid #00a65a;">
												<div class="box-header with-border" style="background-color:#00a65a; color:#fff;">
													<h3 class="box-title"><i class="fa fa-link"></i> Processing Engine</h3>
												</div>
												<div class="box-body">
													<div class="form-group" style="margin-bottom:0px;">
														<div class="col-md-12">
															<div class="radio inline" style="display:inline-block;">
																<label><input type="radio" name="kyc[kyc_integration_type]" id="kyc_integ_self" value="0" <?php if (!isset($kyc['kyc_integration_type']) || $kyc['kyc_integration_type'] == 0) { echo 'checked="true"'; } ?>> Self Managed</label>
															</div>
															<div class="radio inline" style="display:inline-block; margin-left:15px;">
																<label><input type="radio" name="kyc[kyc_integration_type]" id="kyc_integ_third" value="1" <?php if (isset($kyc['kyc_integration_type']) && $kyc['kyc_integration_type'] == 1) { echo 'checked="true"'; } ?>> Third Party API</label>
															</div>
														</div>
													</div>

													<hr style="margin:10px 0;">

													<!-- Manual vs Auto -->
													<div id="kyc_verification_type_block" <?php if (isset($kyc['kyc_integration_type']) && $kyc['kyc_integration_type'] == 1) { echo 'style="display:none;"'; } ?> class="form-group" style="margin-bottom:0px;">
														<label class="col-md-4 control-label" style="font-weight:normal; padding-top:10px;">Verification</label>
														<div class="col-md-8" style="padding-top:3px;">
															<div class="radio inline" style="display:inline-block;">
																<label><input type="radio" name="kyc[kyc_verification_type]" id="kyc_verify_manual" value="0" <?php if (!isset($kyc['kyc_verification_type']) || $kyc['kyc_verification_type'] == 0) { echo 'checked="true"'; } ?>> Manual</label>
															</div>
															<div class="radio inline" style="display:inline-block; margin-left:10px;">
																<label><input type="radio" name="kyc[kyc_verification_type]" id="kyc_verify_auto" value="1" <?php if (isset($kyc['kyc_verification_type']) && $kyc['kyc_verification_type'] == 1) { echo 'checked="true"'; } ?>> Auto</label>
															</div>
														</div>
													</div>

													<div id="kyc_auto_api_block" <?php if (!isset($kyc['kyc_integration_type']) || $kyc['kyc_integration_type'] == 0) { echo 'style="display:none;"'; } ?>>
														<div class="form-group">
															<label class="col-md-12 control-label" style="text-align:left; font-weight:normal;">API Endpoint</label>
															<div class="col-md-12">
																<input type="text" class="form-control input-sm" name="kyc[kyc_api_url]" id="kyc_api_url" value="<?php echo isset($kyc['kyc_api_url']) ? set_value('kyc[kyc_api_url]', $kyc['kyc_api_url']) : ''; ?>" placeholder="https://api.provider.com/verify">
															</div>
														</div>
														<div class="form-group" style="margin-bottom:0;">
															<label class="col-md-12 control-label" style="text-align:left; font-weight:normal;">Secret Key</label>
															<div class="col-md-12">
																<input type="text" class="form-control input-sm" name="kyc[kyc_api_key]" id="kyc_api_key" value="<?php echo isset($kyc['kyc_api_key']) ? set_value('kyc[kyc_api_key]', $kyc['kyc_api_key']) : ''; ?>" placeholder="Secret Token">
															</div>
														</div>
													</div>

												</div>
											</div>
										</div>
									</div>

									<!-- Right Column: Rule Thresholds -->
									<div class="col-md-7">
										
										<!-- Customer Rules Container -->
										<div id="kyc_customer_rules_block" <?php if (!isset($kyc['kyc_mode']) || $kyc['kyc_mode'] == 0) { echo 'style="display:none;"'; } ?>>
											<div class="box box-warning" style="border-top-color:#f39c12;">
												<div class="box-header with-border">
													<h3 class="box-title"><i class="fa fa-users text-yellow"></i> Customer Profiles Matrix</h3>
												</div>
												<div class="box-body" style="background:#fcfcfc; padding:15px;">
													


													<table class="table table-bordered table-striped" id="kyc_customer_rules_table" style="background:#fff; margin-bottom:0;">
														<thead>
															<tr class="bg-gray-light">
																<th style="width:40%;">Requested Documents</th>
																<th style="width:15%;">Trigger Event</th>
																<th style="width:15%;">Amount Limit</th>
																<th style="width:20%;">Condition Logic</th>
																<th style="width:10%; text-align:center;">
																	<button type="button" class="btn btn-xs btn-primary" id="kyc_add_customer_row" title="Add Rule"><i class="fa fa-plus"></i></button>
																</th>
															</tr>
														</thead>
														<tbody id="kyc_customer_rows_container">
															<?php
															$kyc_cust_rules = isset($kyc['kyc_customer_rules']) ? json_decode($kyc['kyc_customer_rules'], true) : array();
															if (is_array($kyc_cust_rules) && count($kyc_cust_rules) > 0) {
																foreach ($kyc_cust_rules as $cidx => $crule) { ?>
																	<tr class="kyc_customer_row">
																		<td>
																			<?php $attr_arr = isset($crule['attribute']) ? $crule['attribute'] : array(); if (!is_array($attr_arr)) $attr_arr = array($attr_arr); ?>
																			<select name="kyc_cust_rule_attr_<?php echo $cidx; ?>[]" class="form-control kyc_cust_rule_attr_select input-sm" multiple="multiple" style="width:100%;" data-selected="<?php echo htmlspecialchars(json_encode($attr_arr)); ?>">
																			</select>
																		</td>
																		<td>
																			<select name="kyc_cust_rule_when[]" class="form-control kyc_cust_rule_when_select input-sm">
																				<option value="0" <?php if (isset($crule['when']) && $crule['when'] == 0) { echo 'selected'; } ?>>On Creation</option>
																				<option value="1" <?php if (isset($crule['when']) && $crule['when'] == 1) { echo 'selected'; } ?>>Exceeds Amount</option>
																			</select>
																		</td>
																		<td class="kyc_cust_amount_cell" <?php if (isset($crule['when']) && $crule['when'] != 1) { echo 'style="display:none;"'; } ?>>
																			<input type="text" name="kyc_cust_rule_amount[]" class="form-control input-sm" value="<?php echo isset($crule['amount']) ? $crule['amount'] : ''; ?>" placeholder="0.00">
																		</td>
																		<td class="kyc_cust_logic_cell">
																			<select name="kyc_cust_rule_logic[]" class="form-control kyc_cust_rule_logic_select input-sm">
																				<option value="0" <?php if (isset($crule['logic']) && $crule['logic'] == 0) { echo 'selected'; } ?>>All Required</option>
																				<option value="1" <?php if (isset($crule['logic']) && $crule['logic'] == 1) { echo 'selected'; } ?>>At Least One</option>
																			</select>
																		</td>
																		<td style="text-align:center;">
																			<button type="button" class="btn btn-xs btn-danger kyc_remove_customer_row"><i class="fa fa-trash"></i></button>
																		</td>
																	</tr>
															<?php } } ?>
														</tbody>
													</table>
													<input type="hidden" name="kyc[kyc_customer_rules]" id="kyc_customer_rules_hidden" value="<?php echo isset($kyc['kyc_customer_rules']) ? htmlspecialchars($kyc['kyc_customer_rules']) : ''; ?>">
												</div>
											</div>
										</div>

										<!-- Scheme Rules Container -->
										<div id="kyc_scheme_rules_block" <?php if (isset($kyc['kyc_mode']) && $kyc['kyc_mode'] == 1) { echo 'style="display:none;"'; } ?>>
											<div class="box box-info" style="border-top-color:#00c0ef;">
												<div class="box-header with-border">
													<h3 class="box-title"><i class="fa fa-list-alt text-aqua"></i> Scheme Mappings Matrix</h3>
												</div>
												<div class="box-body" style="background:#fcfcfc; padding:15px;">



													<table class="table table-bordered table-striped" id="kyc_scheme_rules_table" style="background:#fff; margin-bottom:0;">
														<thead>
															<tr class="bg-gray-light">
																<th style="width:20%;">Target Scheme</th>
																<th style="width:25%;">Required Documents</th>
																<th style="width:15%;">Trigger Event</th>
																<th style="width:15%;">Condition Logic</th>
																<th style="width:10%;">Limit Type</th>
																<th style="width:10%;">Threshold</th>
																<th style="width:5%; text-align:center;">
																	<button type="button" class="btn btn-xs btn-primary" id="kyc_add_scheme_row" title="Add Rule"><i class="fa fa-plus"></i></button>
																</th>
															</tr>
														</thead>
														<tbody id="kyc_scheme_rows_container">
															<?php
															$kyc_rules = isset($kyc['kyc_scheme_rules']) ? json_decode($kyc['kyc_scheme_rules'], true) : array();
															if (is_array($kyc_rules) && count($kyc_rules) > 0) {
																foreach ($kyc_rules as $idx => $rule) {
																	$rule_docs = isset($rule['kyc_docs']) ? $rule['kyc_docs'] : array();
																	if (!is_array($rule_docs)) $rule_docs = array();
																?>
																	<tr class="kyc_scheme_row">
																		<td>
																			<?php $scheme_arr = isset($rule['scheme']) ? $rule['scheme'] : array(); if (!is_array($scheme_arr)) $scheme_arr = array($scheme_arr); ?>
																			<select name="kyc_rule_scheme_<?php echo $idx; ?>[]" class="form-control kyc_rule_scheme_select input-sm" multiple="multiple" style="width:100%;" data-selected="<?php echo htmlspecialchars(json_encode($scheme_arr)); ?>">
																			</select>
																		</td>
																		<td>
																			<select name="kyc_rule_docs_<?php echo $idx; ?>[]" class="form-control kyc_rule_docs_select input-sm" multiple="multiple" style="width:100%;" data-selected="<?php echo htmlspecialchars(json_encode($rule_docs)); ?>">
																			</select>
																		</td>
																		<td>
																			<select name="kyc_rule_when[]" class="form-control kyc_rule_when_select input-sm">
																				<option value="0" <?php if ($rule['when'] == 0) { echo 'selected'; } ?>>Account Opening</option>
																				<option value="1" <?php if ($rule['when'] == 1) { echo 'selected'; } ?>>During Payment</option>
																				<option value="2" <?php if ($rule['when'] == 2) { echo 'selected'; } ?>>Account Closing</option>
																			</select>
																		</td>
																		<td class="kyc_logic_cell">
																			<select name="kyc_rule_logic[]" class="form-control kyc_rule_logic_select input-sm">
																				<option value="0" <?php if (isset($rule['logic']) && $rule['logic'] == 0) { echo 'selected'; } ?>>All Required</option>
																				<option value="1" <?php if (isset($rule['logic']) && $rule['logic'] == 1) { echo 'selected'; } ?>>At Least One</option>
																			</select>
																		</td>
																		<td class="kyc_type_cell" <?php if ($rule['when'] != 1) { echo 'style="display:none;"'; } ?>>
																			<select name="kyc_rule_type[]" class="form-control kyc_rule_type_select input-sm">
																				<option value="0" <?php if (isset($rule['type']) && $rule['type'] == 0) { echo 'selected'; } ?>>Total Amount</option>
																				<option value="1" <?php if (isset($rule['type']) && $rule['type'] == 1) { echo 'selected'; } ?>>Installment Count</option>
																			</select>
																		</td>
																		<td class="kyc_amount_cell" <?php if ($rule['when'] != 1) { echo 'style="display:none;"'; } ?>>
																			<input type="text" name="kyc_rule_amount[]" class="form-control input-sm" value="<?php echo $rule['amount']; ?>" placeholder="...">
																		</td>
																		<td style="text-align:center;">
																			<button type="button" class="btn btn-xs btn-danger kyc_remove_row"><i class="fa fa-trash"></i></button>
																		</td>
																	</tr>
															<?php } } ?>
														</tbody>
													</table>
													<input type="hidden" name="kyc[kyc_scheme_rules]" id="kyc_scheme_rules_hidden" value="<?php echo isset($kyc['kyc_scheme_rules']) ? htmlspecialchars($kyc['kyc_scheme_rules']) : ''; ?>">
												</div>
											</div>
										</div>

									</div>
								</div>

								<div class="row">
									<div class="col-md-12">
										<hr style="margin-top:10px; margin-bottom:15px; border-top:1px solid #ddd;">
										<div class="pull-right">
											<button class="btn btn-primary" id="submit_new_kyc_tab" name="kyc[submit_new_kyc_tab]" value="New KYC Tab"><i class="fa fa-save"></i> Save Settings</button>
										</div>
									</div>
								</div>

								<?php echo form_close(); ?>
							</div>

						<!-- Feature Flags Tab -->
						<div class="tab-pane" id="tab_feature_flags">
							<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
								<div>
									<h4 style="margin:0; display:flex; align-items:center; gap:8px;">
										<input type="checkbox" id="ff_master_toggle" class="switch" data-on-text="ON" data-off-text="OFF" checked="checked" style="display:none;" />
										Feature Flags
									</h4>
									<small style="color:#888;">Enable or disable optional ERP features per client. Changes take effect immediately after saving.</small>
								</div>
								<button type="button" class="btn btn-warning btn-sm" id="ff_add_new_row" style="white-space:nowrap;">
									<i class="fa fa-plus"></i> Add New Flag
								</button>
							</div>
							<div id="ff_alert" class="alert" style="display:none; margin-bottom:10px;">
								<span id="ff_alert_msg"></span>
							</div>
							<table class="table table-bordered table-condensed" id="ff_table" style="margin-bottom:0;">
								<thead>
									<tr style="background:#f5f5f5;">
										<th style="width:40px; text-align:center;">#</th>
										<th style="width:260px;">Feature Code <small style="color:#888; font-weight:normal;">(UPPERCASE_KEY)</small></th>
										<th>Description</th>
										<th style="width:80px; text-align:center;">Active</th>
										<th style="width:70px; text-align:center;">Remove</th>
									</tr>
								</thead>
								<tbody id="ff_rows_body">
									<tr id="ff_empty_row">
										<td colspan="5" style="text-align:center; color:#aaa; padding:20px;">Loading feature flags...</td>
									</tr>
								</tbody>
							</table>
							<div class="box-footer clearfix" style="margin-top:15px; padding:0;">
								<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
								<button type="button" class="btn btn-sm btn-app pull-right" id="ff_save_btn"><i class="fa fa-save"></i> Save Feature Flags</button>
							</div>
						</div>
						<!-- /Feature Flags Tab -->

</div> <!-- /Tab content -->
      				</div>
      			</div><!-- /.box-body -->
      			<div class="box-footer">
      			</div><!-- /.box-footer-->
      		</div><!-- /.box -->
      	</section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      <div class="modal fade" id="confirm-truncate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      	<div class="modal-dialog">
      		<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      				<h4 class="modal-title" id="myModalLabel">Confirm Clear Database</h4>
      			</div>
      			<div class="modal-body">
      				<strong>Are you sure! You want to clear the database?</strong>
      			</div>
      			<div class="modal-footer">
      				<button id="confirm_clear" type="button" class="btn btn-danger" data-dismiss="alert">Clear</button>
      				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
      <!--branch settings -->
      <div class="modal fade" id="confirm-changebranch" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      	<div class="modal-dialog">
      		<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      				<h4 class="modal-title" id="myModalLabel">Confirm Change Your Settings</h4>
      			</div>
      			<div class="modal-body">
      				<strong>Are you sure! You want to Change the Branch Settings...?</strong>
      			</div>
      			<div class="modal-footer">
      				<button id="confirm_change" type="button" class="btn btn-danger" data-dismiss="alert">Yes</button>
      				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
      <!--branch settings -->