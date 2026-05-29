      <div class="content-wrapper">
      	<!-- Content Header (Page header) -->
      	<section class="content-header">
      		<h1>
      			Profile
      			<small></small>
      		</h1>
      		<ol class="breadcrumb">
      			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      			<li><a href="<?php echo base_url('index.php/settings/profile/list'); ?>">Master</a></li>
      			<li class="active">Profile</li>
      		</ol>
      	</section>

      	<!-- Main content -->
      	<section class="content">

      		<!-- Default box -->
      		<div class="box">
      			<div class="box-header with-border">
      				<h3 class="box-title">Profile - <?php echo ($profile['id_profile'] != NULL ? 'Edit' : 'Add'); ?></h3>
      				<div class="box-tools pull-right">
      					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
      					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
      				</div>
      			</div>
      			<div class="box-body">
      				<div class="">
      					<?php echo form_open(($profile['id_profile'] != NULL &&  $profile['id_profile'] > 0 ? 'settings/profile/update/' . $profile['id_profile'] : 'settings/profile/save')) ?>
      					<div class="row">
      						<div class="form-group">
      							<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Profile Name</label>
      							<div class="col-md-4">
      								<input type="text" class="form-control" id="profile_name" name="profile[profile_name]" value="<?php echo set_value('$profile[profile_name]', $profile['profile_name']); ?>" placeholder="eg: Admin" required="true">
      								<p class="help-block"></p>

      							</div>
      						</div>
      					</div><br>
      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Scheme Close Rights</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_acc_closing]" value="1" <?php if ($profile['allow_acc_closing'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_acc_closing]" value="0" <?php if ($profile['allow_acc_closing'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div>
      					</br>



      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Is OTP Required for Login</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[req_otplogin]" value="1" <?php if ($profile['req_otplogin'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[req_otplogin]" value="0" <?php if ($profile['req_otplogin'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Show Pending Download</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[show_pending_download]" value="1" <?php if ($profile['show_pending_download'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[show_pending_download]" value="0" <?php if ($profile['show_pending_download'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Show Cart Items</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[show_cart]" value="1" <?php if ($profile['show_cart'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[show_cart]" value="0" <?php if ($profile['show_cart'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Allow Bill Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_bill_cancel]" value="1" <?php if ($profile['allow_bill_cancel'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_bill_cancel]" value="0" <?php if ($profile['allow_bill_cancel'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Allow Order Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_order_cancel]" value="1" <?php if ($profile['allow_order_cancel'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_order_cancel]" value="0" <?php if ($profile['allow_order_cancel'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Allow Lot Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_lot_cancel]" value="1" <?php if ($profile['allow_lot_cancel'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_lot_cancel]" value="0" <?php if ($profile['allow_lot_cancel'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>
      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Previous Bill Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[previous_bill_cancel]" value="1" <?php if ($profile['previous_bill_cancel'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[previous_bill_cancel]" value="0" <?php if ($profile['previous_bill_cancel'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>



      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">OTP Bill Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[bill_cancel_otp]" value="1" <?php if ($profile['bill_cancel_otp'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[bill_cancel_otp]" value="0" <?php if ($profile['bill_cancel_otp'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">OTP For Credit</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[credit_sales_otp_req]" value="1" <?php if ($profile['credit_sales_otp_req'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[credit_sales_otp_req]" value="0" <?php if ($profile['credit_sales_otp_req'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">OTP For Order Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[order_cancel_otp_req]" value="1" <?php if ($profile['order_cancel_otp'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[order_cancel_otp_req]" value="0" <?php if ($profile['order_cancel_otp'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">OTP For Counter Change</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[counter_change_otp]" value="1" <?php if ($profile['counter_change_otp'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[counter_change_otp]" value="0" <?php if ($profile['counter_change_otp'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">OTP For Order Unlink</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[order_unlink_otp]" value="1" <?php if ($profile['order_unlink_otp'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[order_unlink_otp]" value="0" <?php if ($profile['order_unlink_otp'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">OTP For Credit Collection Discount</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[credit_collection_disc_otp]" value="1" <?php if ($profile['credit_collection_disc_otp'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[credit_collection_disc_otp]" value="0" <?php if ($profile['credit_collection_disc_otp'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Allow Branch Trasnfer Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_branch_transfer_cancel]" value="1" <?php if ($profile['allow_branch_transfer_cancel'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_branch_transfer_cancel]" value="0" <?php if ($profile['allow_branch_transfer_cancel'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Allow Bill Type</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_bill_type]" value="1" <?php if ($profile['allow_bill_type'] == 1) { ?> checked="true" <?php } ?>> Normal
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_bill_type]" value="2" <?php if ($profile['allow_bill_type'] == 2) { ?> checked="true" <?php } ?>> EDA
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_bill_type]" value="3" <?php if ($profile['allow_bill_type'] == 3) { ?> checked="true" <?php } ?>> All
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Device Wise Login</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[device_wise_login]" value="1" <?php if ($profile['device_wise_login'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[device_wise_login]" value="0" <?php if ($profile['device_wise_login'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Branch Transfer Option</label>
      						<div class="col-md-8">
      							<div class="col-md-8">
      								<input type="checkbox" id="tag_transfer" name="profile[tag_transfer]" <?php if ($profile['tag_transfer'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>Tag &nbsp;<input type="checkbox" id="non_tag_transfer" name="profile[non_tag_transfer]" <?php if ($profile['non_tag_transfer'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>Non-Tag &nbsp;<input type="checkbox" name="profile[purchase_item_transfer]" id="purchase_item_transfer" <?php if ($profile['purchase_item_transfer'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>Purchase Item &nbsp;<input type="checkbox" name="profile[packaging_item_transfer]" id="packaging_item_transfer" <?php if ($profile['packaging_item_transfer'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>Packaging Items
      							</div>


      							<p class="help-block"></p>
      						</div>
      					</div></br>

	  					<!--Allow Other Issue-->
						<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Allow Other Issue</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_other_issue]" value="1" <?php if ($profile['allow_other_issue'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_other_issue]" value="0" <?php if ($profile['allow_other_issue'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<!-- <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">MC Edit(Estimation)</label>                            -->
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">MC Edit(Estimation & Billing)</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_mc_edit]" value="1" <?php if ($profile['allow_mc_edit'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_mc_edit]" value="0" <?php if ($profile['allow_mc_edit'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<!-- <label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">VA Edit(Estimation)</label>                            -->
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">VA Edit(Estimation & Billing)</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_va_edit]" value="1" <?php if ($profile['allow_va_edit'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_va_edit]" value="0" <?php if ($profile['allow_va_edit'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Purity Edit(Estimation)</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[est_purity_edit]" value="1" <?php if ($profile['est_purity_edit'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[est_purity_edit]" value="0" <?php if ($profile['est_purity_edit'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1">Tag History</label>
      						<div class="col-md-8">
      							<div class="col-md-8">
      								<!-- Add more checkboxes for other details here -->
      								<input type="checkbox" name="profile[tag_details]" id="tag_details" <?php if ($profile['tag_details'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>
      								Tag Details &nbsp;
      								<input type="checkbox" id="purchase_details" name="profile[purchase_details]" <?php if ($profile['purchase_details'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>
      								Purchase &nbsp;
      								<input type="checkbox" name="profile[stone_details]" id="stone_details" <?php if ($profile['stone_details'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>
      								Stone &nbsp;
      								<input type="checkbox" name="profile[estimation]" id="estimation" <?php if ($profile['estimation'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>
      								Estimation&nbsp;
      								<input type="checkbox" name="profile[branch_transfer_details]" id="branch_transfer_details" <?php if ($profile['branch_transfer_details'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>
      								Branch Transfer &nbsp;<br>
      								<input type="checkbox" name="profile[section_transfer_details]" id="section_transfer_details" <?php if ($profile['section_transfer_details'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>
      								Section Transfer &nbsp;
      								<input type="checkbox" name="profile[scan_details]" id="scan_details" <?php if ($profile['scan_details'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>
      								Scan Details&nbsp;
      								<input type="checkbox" name="profile[stock_issue_details]" id="stock_issue_details" <?php if ($profile['stock_issue_details'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>
      								Stock Issue
      							</div>
      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Estimation</label>
      						<div class="col-md-8">
      							<div class="col-md-8">
      								<input type="checkbox" id="est_tag" name="profile[est_tag]" <?php if ($profile['est_tag'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>Tag &nbsp;<input type="checkbox" id="est_non_tag" name="profile[est_non_tag]" <?php if ($profile['est_non_tag'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>Non-Tag &nbsp;<input type="checkbox" name="profile[est_home_bill]" id="est_home_bill" <?php if ($profile['est_home_bill'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>Home Bill &nbsp;<input type="checkbox" name="profile[est_old_metal]" id="est_old_metal" <?php if ($profile['est_old_metal'] == 1) { ?> checked="true" value="1" <?php } else { ?> value="0" <?php } ?>>Old Metal
      							</div>
      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">OTP for Stock Issue</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[stock_issue_otp_req]" value="1" <?php if ($profile['stock_issue_otp_req'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[stock_issue_otp_req]" value="0" <?php if ($profile['stock_issue_otp_req'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">OTP For Vendor Approval</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[vendor_approval_otp_req]" value="1" <?php if ($profile['vendor_approval_otp_req'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[vendor_approval_otp_req]" value="0" <?php if ($profile['vendor_approval_otp_req'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> OTP Approval Type For Bill Discount</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[bill_disc_approval_type]" value="1" <?php if ($profile['bill_disc_approval_type'] == 1) { ?> checked="true" <?php } ?>> OTP
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[bill_disc_approval_type]" value="2" <?php if ($profile['bill_disc_approval_type'] == 2) { ?> checked="true" <?php } ?>> Mobile App
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> OTP Approval Type For Credit Sales</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[credit_sales_approval_type]" value="1" <?php if ($profile['credit_sales_approval_type'] == 1) { ?> checked="true" <?php } ?>> OTP
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[credit_sales_approval_type]" value="2" <?php if ($profile['credit_sales_approval_type'] == 2) { ?> checked="true" <?php } ?>> Mobile App
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>



      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Branch Transfer Approval Type</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[BT_otp_approval_type]" value="1" <?php if ($profile['BT_otp_approval_type'] == 1) { ?> checked="true" <?php } ?>> OTP
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[BT_otp_approval_type]" value="2" <?php if ($profile['BT_otp_approval_type'] == 2) { ?> checked="true" <?php } ?>> Mobile App
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Previous Date Other Issue</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[pre_date_oi]" value="1" <?php if ($profile['pre_date_oi'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[pre_date_oi]" value="0" <?php if ($profile['pre_date_oi'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Order Delievery Otp</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[order_delievery_otp]" value="1" <?php if ($profile['order_delievery_otp'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[order_delievery_otp]" value="0" <?php if ($profile['order_delievery_otp'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>


      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Allow Stock Type</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_stock_type]" value="1" <?php if ($profile['allow_stock_type'] == 1) { ?> checked="true" <?php } ?>> Pcs
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_stock_type]" value="2" <?php if ($profile['allow_stock_type'] == 2) { ?> checked="true" <?php } ?>> Weight
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_stock_type]" value="3" <?php if ($profile['allow_stock_type'] == 3) { ?> checked="true" <?php } ?>> All
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

						<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Allow Wedding Purchase Discount</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[wed_wastage_slab]" value="1" <?php if ($profile['wedding_wastage_slab'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[wed_wastage_slab]" value="0" <?php if ($profile['wedding_wastage_slab'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Chit Metal Rate Edit</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[metalrate_edit]" value="1" <?php if ($profile['metalrate_edit'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[metalrate_edit]" value="0" <?php if ($profile['metalrate_edit'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<div class="form-group">
      							<label for="metal_rate_datelimit" class="col-md-2 col-md-offset-1 ">Chit Metal Rate Date Limit</label>
      							<div class="col-md-4">
      								<input type="number" class="form-control" id="metal_rate_datelimit" name="profile[metal_rate_datelimit]" value="<?php echo set_value('profile[metal_rate_datelimit]', $profile['metal_rate_datelimit']); ?>" placeholder="eg: 3">
      								<p class="help-block">Enter the number of months to allow backdate selection (e.g., 3 for 3 months).</p>
      							</div>
      						</div>
      					</div><br>



      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Allow Previous Receipt Date</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_previous_rcpt_date]" value="1" <?php if ($profile['allow_previous_rcpt_date'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_previous_rcpt_date]" value="0" <?php if ($profile['allow_previous_rcpt_date'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Allow Supplier Bill Approval</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_supplier_bill_approval]" value="1" <?php if ($profile['allow_supplier_bill_approval'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_supplier_bill_approval]" value="0" <?php if ($profile['allow_supplier_bill_approval'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Allow IR Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_ir_cancel]" value="1" <?php if (isset($profile['allow_ir_cancel']) && $profile['allow_ir_cancel'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[allow_ir_cancel]" value="0" <?php if (!isset($profile['allow_ir_cancel']) || $profile['allow_ir_cancel'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<div class="row">
      						<label for="chargeseme_name" class="col-md-2 col-md-offset-1 "> Previous IR Cancel</label>
      						<div class="col-md-6">
      							<div class="col-md-3">
      								<input type="radio" name="profile[previous_ir_cancel]" value="1" <?php if (isset($profile['previous_ir_cancel']) && $profile['previous_ir_cancel'] == 1) { ?> checked="true" <?php } ?>> Yes
      							</div>
      							<div class="col-md-3">
      								<input type="radio" name="profile[previous_ir_cancel]" value="0" <?php if (!isset($profile['previous_ir_cancel']) || $profile['previous_ir_cancel'] == 0) { ?> checked="true" <?php } ?>> No
      							</div>

      							<p class="help-block"></p>
      						</div>
      					</div></br>

      					<br />
      					<div class="row col-xs-12">
      						<div class="box box-default"><br />
      							<div class="col-xs-offset-5">
      								<button type="submit" class="btn btn-primary">Save</button>
      								<button type="button" class="btn btn-default btn-cancel">Cancel</button>

      							</div> <br />
      						</div>
      					</div>

      					</form>
      				</div>
      			</div><!-- /.box-body -->
      			<div class="box-footer">

      			</div><!-- /.box-footer-->
      		</div><!-- /.box -->


      	</section><!-- /.content -->
      </div><!-- /.content-wrapper -->