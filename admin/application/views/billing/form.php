	<!-- Content Wrapper. Contains page content -->
	<style>
		.remove-btn {
			margin-top: -168px;
			margin-left: -38px;
			background-color: #e51712 !important;
			border: none;
			color: white !important;
		}

		.hint_class > p{
			color:red;
			font-size: medium;
			font-weight: 600;
		}

		.purchase_add {
			cursor: pointer;
			color: blue;
			text-decoration: underline;
		}

		.summary_lbl {
			font-weight: bold;
		}

		.h_amt {
			font-size: 23px;
		}

		.f_amt {
			font-size: 26px;
		}

		.stickyBlk {
			margin: 0 auto;
			top: 0;
			width: 100%;
			z-index: 999;
			background: #fff;
		}

		.custom-label {
			font-weight: 600 !important;
			letter-spacing: 0.5px !important;
			text-transform: uppercase !important;
		}

		.extra-bold {
			font-weight: bold;
			font-weight: 800 !important;
			/* Adjust the font weight as needed */
			letter-spacing: 0.5px !important;
			text-transform: uppercase !important;
			color: red;
		}

		.payment_blk input[type=text],
		.payment_blk input[type=number] {
			width: 250px;
		}

		.bold-black-text {
			color: red;
			font-weight: bold;
			margin-top: 10px;
			margin-left: 10px;
			font-size: 16px;
		}

		.gift_details {
			color: #FF0000;
		}

		.billType {
			padding: 3px !important;
			margin: 0px !important;
			height: auto;
		}

		.form-group {
			margin-bottom: 1px;
		}

		#payment_modes td,
		#total_summary_details td {
			padding: 1px 5px !important;
		}

		#payment_modes input[type=text],
		#payment_modes input[type=number],
		#payment_modes button,
		#total_summary_details input[type=text],
		#total_summary_details input[type=number],
		#total_summary_details button {
			height: 25px !important;
			padding: 1px 5px !important;
		}

		*[tabindex]:focus {
			outline: 1px black solid;
		}

		.loader {
			color: #666;
			width: 12px;
			height: 12px;
			border-radius: 50%;
			display: block;
			/* margin:15px auto; */
			position: relative;
			/* color: #000; */
			box-sizing: border-box;
			animation: animloader 2s linear infinite;
		}
		.modal_btns {
			display: flex;
			justify-content: space-between;
		}

		/* .loader {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  display: inline-block;
  margin-top:4px;
  position: relative;
  background: #FFF;
  box-shadow: -24px 0 #FFF, 24px 0 #FFF;
  box-sizing: border-box;
  animation: shadowPulse 2s linear infinite;
}

@keyframes shadowPulse {
  33% {
    background: #FFF;
    box-shadow: -24px 0 #FF3D00, 24px 0 #FFF;
  }
  66% {
    background: #FF3D00;
    box-shadow: -24px 0 #FFF, 24px 0 #FFF;
  }
  100% {
    background: #FFF;
    box-shadow: -24px 0 #FFF, 24px 0 #FF3D00;
  }
} */
	</style>

	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<!--<section class="content-header">
          <h1>
        	Billing
            <small>Customer Billing</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Billings</a></li>
            <li class="active">Billing</li>
          </ol>
        </section>-->
		<!-- Main content -->
		<section class="content product">
			<!-- Default box -->
			<div class="box box-primary">
				<!-- <div class="box-header with-border">
              <h3 class="box-title">Add Billing</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div> -->
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
					<!-- form container -->
					<div class="row">
						<!-- form -->
						<?php
						$metal_rates = $this->admin_settings_model->metal_ratesDB("last");
						?>
						<form id="bill_pay">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-12" style="padding: 0px;">
										<!--Cost Center-->
										<div class="form-group">
											<div class="col-md-2">
												<label>Cost Centre<span class="error">*</span></label><br>
												<?php if ($this->session->userdata('id_branch') == "") { ?>
													<select name="billing[id_branch]" id="id_branch" class="form-control" required tabindex=1 autofocus>
														<?php echo $this->ret_billing_model->get_currentBranches($billing['id_branch']); ?>
														<input type="hidden" id="branch_id_country" value="" required="">
														<input type="hidden" id="branch_id_state" value="" required="">
														<input type="hidden" id="branch_id_city" value="" required="">
														<input type="hidden" id="branch_pincode" value="" required="">
														<input type="hidden" id="branch_id_village" value="" required="">
													</select>
												<?php } else { ?>
													<label><?php echo $this->ret_billing_model->get_currentBranchName($type == 'add' ? $this->session->userdata('id_branch') : $billing['id_branch']); ?> </label>
													<input type="hidden" id="id_branch" name="billing[id_branch]" value="<?php echo $type == 'add' ? $this->session->userdata('id_branch') : $billing['id_branch']; ?>" />
													<input type="hidden" id="branch_id_country" value="" required="">
													<input type="hidden" id="branch_id_state" value="" required="">
													<input type="hidden" id="branch_id_city" value="" required="">
													<input type="hidden" id="branch_pincode" value="" required="">
													<input type="hidden" id="branch_id_village" value="" required="">
												<?php } ?>
											</div>
											<input type="hidden" id="enable_gift_voucher" value="">
											<input type="hidden" id="enable_pos_payment" value="<?php echo $this->ret_billing_model->getPOSMachineRequired();?>">
											<input type="hidden" id="max_return_amt" value="<?php echo $billing['max_return_amt']; ?>">
											<input type="hidden" id="allow_mc_edit" value="<?php echo $profile_setting['allow_mc_edit'] ?>" />
											<input type="hidden" id="allow_manual_rate" value="<?php echo $emp_setting['allow_manual_rate'] ?>" />

											<input type="hidden" id="min_gold_tol" value="<?php echo $emp_setting['min_gold_tol'] ?>" />
											<input type="hidden" id="max_gold_tol" value="<?php echo $emp_setting['max_gold_tol'] ?>" />
      											<input type="hidden" id="allow_va_edit" value="<?php echo $profile_setting['allow_va_edit'] ?>" />
											<label style="display:none;" class="per-grm-sale-value"> <?php echo $metal_rates['goldrate_22ct']; ?> </label>
											<label style="display:none;" class="silver_per-grm-sale-value"> <?php echo $metal_rates['silverrate_1gm']; ?> </label>
											<label style="display:none;" class="mjdmagoldrate_22ct"> <?php echo $metal_rates['mjdmagoldrate_22ct']; ?> </label>
											<label style="display:none;" class="mjdmasilverrate_1gm"> <?php echo $metal_rates['mjdmasilverrate_1gm']; ?> </label>
											<label style="display:none;" class="market_gold_20ct"> <?php echo $metal_rates['market_gold_20ct']; ?> </label>
											<label style="display:none;" class="goldrate_22ct"> <?php echo $metal_rates['goldrate_22ct']; ?> </label>
											<label style="display:none;" class="silverrate_1gm"> <?php echo $metal_rates['silverrate_1gm']; ?> </label>
											<label style="display:none;" class="goldrate_24ct"> <?php echo $metal_rates['goldrate_24ct']; ?> </label>
											<label style="display:none;" class="silverrate_1gm"> <?php echo $metal_rates['silverrate_1gm']; ?> </label>
											<label style="display:none;" class="goldrate_18ct"> <?php echo $metal_rates['goldrate_18ct']; ?> </label>
											<label style="display:none;" class="goldrate_14ct"> <?php echo $metal_rates['goldrate_14ct']; ?> </label>
											<label style="display:none;" class="goldrate_9ct"> <?php echo $metal_rates['goldrate_9ct']; ?> </label>
											<label style="display:none;" class="platinum_1g"> <?php echo $metal_rates['platinum_1g']; ?> </label>



											<p id="branchAlert" class="error" align="left"></p>
										</div>
										<!-- Billing To -->
										<div class="form-group">
											<div class="col-md-3">
												<label>Billing To</label><br>
												<input type="radio" id="billing_for1" name="billing[billing_for]" value="1" checked tabindex=2> Customer </label>
												<input type="radio" id="billing_for2" name="billing[billing_for]" value="2" tabindex=3> Company </label>
												<input type="radio" id="billing_for3" name="billing[billing_for]" value="3" tabindex=4> Supplier </label>
											</div>
											<div class="col-md-2 .md-offset-5">
												<label>Employee</label></br>
												<select id="emp_select" name="billing[id_employee]" style="width:100%"></select>
											</div>
											<div class="col-md-2" style="<?php echo $billing['IsMetalForBilling'] == 1 ? 'display:block' : 'display:none' ?>">
												<label>Select Metal<span class="error">*</span></label>
												<div class="form-group">
													<?php if ($billing['IsMetalForBilling'] == 1) { ?>
														<select class="form-control" id="select_metal_type" name="billing[metal_type]"></select>
													<?php } else { ?>
														<input type="hidden" id="select_metal_type" name="billing[metal_type]">
													<?php } ?>
												</div>
											</div>
										</div>
										<!-- Customer -->
										<div class="form-group">
											<div class="col-md-3">
												<label>Customer<span class="error">*</span></label><br>
												<div class="input-group">
													<input class="form-control" id="bill_cus_name" name="billing[cus_name]" type="text" placeholder="Name / Mobile" value="<?php echo set_value('billing[cus_name]', isset($billing['cus_name']) ? $billing['cus_name'] : NULL); ?>" required autocomplete="off" tabindex=4 />
													<input class="form-control cus_id" id="bill_cus_id" name="billing[bill_cus_id]" type="hidden" value="<?php echo set_value('billing[bill_cus_id]', $billing['bill_cus_id']); ?>" />
													<input type="hidden" id="customer_sales_limit" name="settings[customer_sales_limit]" value="">
													<input type="hidden" id="bt_tcs_tax_per" name="tax_per[tcs_tax_per]" value="">
													<input type="hidden" id="opening_balance" name="tcs_details[opening_balance]" value="">
													<input type="hidden" id="fin_year_code" name="tcs_details[fin_year_code]" value="">
													<input type="hidden" id="cus_total_amt_for_tcs" value="">
													<input type="hidden" id="form_secret" name="billing[form_secret]" value="<?php echo get_form_secret_key(); ?>">
													<input type="hidden" id="validity_days" name="billing[validity_days]" value="">
													<input type="hidden" id="validate_date" name="billing[validate_date]" value="">
													<input type="hidden" id="id_set_gift_voucher" name="billing[id_set_gift_voucher]" value="">
													<input type="hidden" id="gift_type" name="billing[gift_type]" value="">
													<input type="hidden" id="utilize_for" name="billing[utilize_for]" value="">
													<input type="hidden" id="issue_for" name="billing[issue_for]" value="">
													<input type="hidden" id="bill_value" value="">
													<input type="hidden" id="credit_value" value="">
													<input type="hidden" id="calc_type" value="">
													<input type="hidden" id="goldrate_22ct" name="billing[goldrate_22ct]" value="">
													<input type="hidden" id="silverrate_1gm" name="billing[silverrate_1gm]" value="">
													<input type="hidden" id="goldrate_14ct" name="billing[goldrate_14ct]" value="">
													<input type="hidden" id="goldrate_9ct" name="billing[goldrate_9ct]" value="">
													<input type="hidden" id="goldrate_18ct" name="billing[goldrate_18ct]" value="">
													<input type="hidden" id="goldrate_24ct" name="billing[goldrate_24ct]" value="">
													<input type="hidden" id="platinum_1g" name="billing[platinum_1g]" value="">
													<input id="disc_limit_type" type="hidden" value="<?php echo set_value('billing[disc_limit_type]', $billing['disc_limit_type']); ?>" />
													<input id="disc_limit" type="hidden" value="<?php echo set_value('billing[disc_limit]', $billing['disc_limit']); ?>" />
													<input id="otp_dis_approval" type="hidden" value="<?php echo set_value('billing[otp_dis_approval]', $billing['otp_dis_approval']); ?>" />
													<input id="otp_mcva_dis_approval" type="hidden" value="<?php echo set_value('billing[otp_mcva_dis_approval]', $billing['otp_mcva_dis_approval']); ?>" />
													<input id="allow_bill_type" type="hidden" value="<?php echo set_value('billing[allow_bill_type]', $billing['allow_bill_type']); ?>" />
													<input id="is_eda" type="hidden" name="billing[is_eda]" value="1" />
													<input id="credit_collection_disc_otp" type="hidden" value="<?php echo set_value('billing[credit_collection_disc_otp]', $billing['credit_collection_disc_otp']); ?>" />
													<input id="is_counter_req" type="hidden" value="<?php echo set_value('billing[is_counter_req]', $billing['is_counter_req']); ?>" />
													<input id="counter_id" type="hidden" value="<?php echo $this->session->userdata('counter_id'); ?>" />
													<input id="is_tcs_required" type="hidden" value="<?php echo set_value('billing[is_tcs_required]', $billing['is_tcs_required']); ?>" />
													<input id="tcs_tax_per" name="billing[tcs_tax_per]" type="hidden" value="<?php echo set_value('billing[tcs_tax_per]', $billing['tcs_tax_per']); ?>" />
													<input id="tcs_min_bill_amt" type="hidden" value="<?php echo set_value('billing[tcs_min_bill_amt]', $billing['tcs_min_bill_amt']); ?>" />
													<input id="repair_order_per" type="hidden" value="<?php echo set_value('billing[repair_order_per]', $billing['repair_percentage']); ?>" />
													<input id="credit_sales_approval_type" type="hidden" value="<?php echo set_value('billing[credit_sales_approval_type]', $billing['credit_sales_approval_type']); ?>" />
													<input id="credit_sales_otp_req" type="hidden" value="<?php echo set_value('billing[credit_sales_otp_req]', $billing['credit_sales_otp_req']); ?>" />
													<input id="bill_discount_type" name="billing[bill_discount_type]" type="hidden" value="<?php echo set_value('billing[bill_discount_type]', $billing['bill_discount_type']); ?>" />
													<input id="bill_discount_apply_on" name="billing[bill_discount_apply_on]" type="hidden" value="<?php echo set_value('billing[bill_discount_apply_on]', $billing['bill_discount_apply_on']); ?>" />
													<input type="hidden" id="weightschemecaltype" value="<?php echo set_value('billing[weightschemecaltype]', $billing['weightschemecaltype']); ?>" />
													<input type="hidden" id="weight_scheme_closure_type" value="<?php echo set_value('billing[weight_scheme_closure_type]', $billing['weight_scheme_closure_type']); ?>" />
													<input type="hidden" id="billing_emp_select_req" value="<?php echo set_value('billing[billing_emp_select_req]', $billing['billing_emp_select_req']); ?>" />
													<input id="tot_purchase_amt" type="hidden" value="" />
													<input type="hidden" id="tcs_total_tax_amount" name="billing[tcs_tax_amt]" value="">
													<label style="display:none;" class="per-grm-sale-value"> </label>
													<label style="display:none;" class="silver_per-grm-sale-value"> </label>
													<input type="hidden" id="cus_state" name="billing[cus_state]">
													<input type="hidden" id="cus_country" name="billing[cus_country]">
													<input type="hidden" id="cus_del_state" name="billing[cus_state]">
													<input type="hidden" id="cus_del_country" name="billing[cus_country]">
													<input type="hidden" id="cmp_state" name="" value="<?php echo set_value('billing[cmp_state]', $billing['cmp_state']); ?>">
													<input type="hidden" id="cmp_country" name="" value="<?php echo set_value('billing[cmp_country]', $billing['cmp_country']); ?>">
													<input type="hidden" id="isMetal" name="billing[isMetal]" value="<?php echo set_value('billing[IsMetalForBilling]', $billing['IsMetalForBilling']); ?>" />
													<input type="hidden" id="allow_bill_type" value="<?php echo $this->session->userdata('allow_bill_type'); ?>" />
													<input type="hidden" id="supplier_country" value="">
													<input type="hidden" id="supplier_state" value="">
													<input type="hidden" id="emp_name" value="<?php echo set_value('billing[emp_name]', $billing['emp_name']); ?>">
													<input type="hidden" id="is_direct_bill_required" value="<?php echo set_value('billing[is_direct_bill_required]', $billing['is_direct_bill_required']); ?>">
													<!-- BIL-CLT02: Granular discount limit toggle settings (1=enforce, 0=bypass) -->
								<input type="hidden" id="enable_emp_disc_limit"      value="<?php echo set_value('billing[enable_emp_disc_limit]',      $billing['enable_emp_disc_limit']);      ?>">
								<input type="hidden" id="enable_mc_va_disc_limit"    value="<?php echo set_value('billing[enable_mc_va_disc_limit]',    $billing['enable_mc_va_disc_limit']);    ?>">
								<input type="hidden" id="enable_disc_blw_metal_rate" value="<?php echo set_value('billing[enable_disc_blw_metal_rate]', $billing['enable_disc_blw_metal_rate']); ?>">

													<input type="hidden" id="chit_rate_cal_type" value="<?php echo set_value ('billing[chit_rate_calculation_type]', $billing['chit_rate_calculation_type']); ?>" />



													<!-- OTP Reqired fields -->
													<input id="from_cus_mobile" type="hidden" name="billing[from_cus_mobile]" value="" />
													<input id="is_otp_verfied" type="hidden" name="billing[is_otp_verfied]" value="0" />
													<input id="adv_trans_otp" type="hidden" name="billing[adv_trans_otp]" />
													<input id="send_resend" type="hidden" name="billing[send_resend]" value="0" />
													<input id="otp_required" type="hidden" name="billing[otp_required]" value=<?php echo $otp_settings ?> />
													<input id="bill_id" name="billing[bill_id]" type="hidden" value="<?php echo set_value('billing[bill_id]', $billing['bill_id']); ?>" />
													<input id="validate_max_cash" type="hidden" value="<?php echo set_value('settings[validate_cash_amt]', $settings['validate_cash_amt']); ?>" />
													<input id="max_cash_amt" type="hidden" value="<?php echo set_value('settings[max_cash_amt]', $settings['max_cash_amt']); ?>" />
													<input id="chit_total_cash_amt" type="hidden" value="0" />
													<input id="adv_total_cash_amt" type="hidden" value="0" />
													<input id="return_cash_paid_amount" type="hidden" value="0" />
													<input id="sales_return_cash_amt" type="hidden" value="0" />
													<input id="order_adv_cash" type="hidden" value="0" />
													<input id="otp_item_details" type="hidden" value="0" />
													<input id="is_approval_success" type="hidden" value="0" />
													<input id="approval_id" type="hidden" value="" name="billing[approval_id]" />
													<input id="approval_type" type="hidden" value="" name="billing[approval_type]" />
													<input id="apprl_approved_by" type="hidden" value="" name="billing[apprl_approved_by]" />
													<input id="apprl_status" type="hidden" value="" name="billing[apprl_status]" />
													<input id="disc_approved_id" type="hidden" value="" name="billing[disc_approved_id]" />
													<input id="disc_approved_by" type="hidden" value="" name="billing[disc_approved_by]" />
													<input id="credit_approved_id" type="hidden" value="" name="billing[credit_approved_id]" />
													<input id="credit_approved_by" type="hidden" value="" name="billing[credit_approved_by]" />
													<input id="disc_amt_modified" type="hidden" value="" name="billing[disc_amt_modified]" />





													<input id="is_otp_approved"  name="billing[is_otp_approved]"  type="hidden" value="0" />

													<input id="otp_approved_by"  name="billing[otp_approved_by]"  type="hidden" value="" />


													<input id="cliIDcode" type="hidden" value="<?php echo $this->config->item('cliIDcode') ?>" />
													<input id="bill_disc_approval_type" type="hidden" value="<?php echo set_value('billing[bill_disc_approval_type]', $billing['bill_disc_approval_type']) ?>" />
													<input id="old_metal_rate_type" type="hidden" value="<?php echo set_value('billing[old_metal_rate_type]', $billing['old_metal_rate_type']) ?>" />


													<span id="customerAlert"></span>
													<span class="input-group-btn">
														<a class="btn btn-default" id="add_new_customer" href="#" data-toggle="tooltip" title="Add Customer" tabindex=5><i class="fa fa-user-plus"></i></a>
														<a class="btn btn-warning" data-bs-toggle="offcanvas" data-bs-target="#demo" id="edit_estimation_detalis" href="#" tabindex=5 data-toggle="tooltip" title="Edit Customer"><i class="fa fa-user-plus"></i></a>
													</span>
												</div>
											</div>
											<p id=cus_info></p>
											<p id="cusAlert" class="error" align="left"></p>
										</div>
										<div class="form-group">
											<div class="col-md-2">
												<label for="finalname">Customer Name <span class="error">*</span></label>
												<input type="text" autocomplete="off" class="form-control" name="billing[customer_name]" id="finalname" value="">
											</div>
										</div>
										<!-- Billing To -->
										<div class="form-group" id="emp_user" style="display:none;">
											<div class="col-md-2">
												<label>Company Employee<br>
													<div class="input-group">
														<input type="text" class="form-control" id="bill_emp_name" placeholder="Name / Mobile" tabindex=6>
														<input type="hidden" id="id_cmp_emp" name="billing[id_cmp_emp]">
														<span class="input-group-btn">
															<button type="button" id="add_cmp_emp" class="btn btn-default btn-flat" tabindex=7><i class="fa fa-plus"></i></button>
														</span>
													</div>
											</div>
										</div>
									</div>
								</div>
								<p></p>
								<p></p>
								<p></p>
								<div class="row">
									<div class="form-group">
										<div class="col-md-2">
											<label><a data-toggle="tooltip" title="Delivery Location">Delivery Location</a><span class="error">*</span></label>
											<div class="form-group">
												<select class="form-control" id="delivered_at" name="billing[delivered_at]">
													<option value="1" selected>Show Room</option>
													<option value="2">Customer Address</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								<p></p>
								<div class="row"> <!-- Row 2 -->
									<div class="col-sm-12">
										<label class="col-sm-1 control-label">Bill Type <span class="error">*</span></label>
										<a class="btn btn-app billType  btn-flat bg-green">
											<!--<span class="badge bg-yellow">3</span>-->
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_typesales" name="billing[bill_type]" value="1" <?php echo $billing['bill_type'] == 1 ? 'checked' : '' ?> tabindex=9> <label for="bill_typesales" class="custom-label"> SALES </label>
										</a>
										<a class="btn btn-app billType btn-flat bg-teal">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_type_salesPurch" name="billing[bill_type]" value="2" <?php echo $billing['bill_type'] == 2 ? 'checked' : '' ?> tabindex=10> <label for="bill_type_salesPurch" class="custom-label"> Sales & Purchase</label>
										</a>
										<a class="btn btn-app billType btn-flat bg-olive">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_type_saleRet" name="billing[bill_type]" value="3" <?php echo $billing['bill_type'] == 3 ? 'checked' : '' ?> tabindex=11> <label for="bill_type_saleRet" class="custom-label"> Sales,Purchase & Return</label>
										</a>
										<a class="btn btn-app billType btn-flat bg-yellow">
											<!--<i class="fa fa-barcode"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_type_purchase" name="billing[bill_type]" value="4" <?php echo $billing['bill_type'] == 4 ? 'checked' : '' ?> tabindex=12> <label for="bill_type_purchase" class="custom-label"> Purchase </label>
										</a>
										<a class="btn btn-app billType btn-flat bg-purple">
											<!--<i class="fa fa-users"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_type_order_advance" name="billing[bill_type]" value="5" <?php echo $billing['bill_type'] == 5 ? 'checked' : '' ?> tabindex=13 /> <label for="bill_type_order_advance" class="custom-label"> Order Advance </label>
										</a>
										<!--<a class="btn btn-app billType btn-flat bg-orange">
			                <input type="radio" class="bill_type_sales" name="billing[bill_type]" value="6"  <?php echo $billing['bill_type'] == 6 ? 'checked' : '' ?>> <label for="bill_type_advance" class="custom-label"> Advance </label>
			              </a>-->
										<a class="btn btn-app billType btn-flat bg-red">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_type_sales_return" name="billing[bill_type]" value="7" <?php echo $billing['bill_type'] == 7 ? 'checked' : '' ?> tabindex=14> <label for="bill_type_sales_return" class="custom-label"> Sales Return </label>
										</a>
										<a class="btn btn-app billType btn-flat bg-maroon">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_type_credit_bill" name="billing[bill_type]" value="8" <?php echo $billing['bill_type'] == 8 ? 'checked' : '' ?> tabindex=15> <label for="bill_type_credit_bill" class="custom-label"> Credit Collection </label>
										</a>
										<a class="btn btn-app billType btn-flat bg-fuchsia">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_type_order_del" name="billing[bill_type]" value="9" <?php echo $billing['bill_type'] == 9 ? 'checked' : '' ?> tabindex=16> <label for="bill_type_order_del" class="custom-label">Order Delivery</label>
										</a>
										<a class="btn btn-app billType btn-flat bg-aqua">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" class="bill_type_sales" id="bill_type_chitPre" name="billing[bill_type]" value="10" <?php echo $billing['bill_type'] == 10 ? 'checked' : '' ?> tabindex=17> <label for="bill_type_chitPre" class="custom-label">Chit Pre Close</label>
										</a>
										<a class="btn btn-app billType btn-flat bg-maroon">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" id="repair_order_delivery" class="bill_type_sales" name="billing[bill_type]" value="11" <?php echo $billing['bill_type'] == 11 ? 'checked' : '' ?> tabindex=18> <label for="repair_order_delivery" class="custom-label">Repair Order Delivery</label>
										</a>
										<a class="btn btn-app billType btn-flat bg-maroon">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" id="supplier_sales_bill" class="bill_type_sales" name="billing[bill_type]" value="12" <?php echo $billing['bill_type'] == 12 ? 'checked' : '' ?> tabindex=19 disabled> <label for="supplier_sales_bill" class="custom-label">Supplier Sales Bill</label>
										</a>
										<a class="btn btn-app billType btn-flat bg-red">
											<!--<i class="fa fa-bullhorn"></i> -->
											<input type="radio" id="approval_sales_bill" class="bill_type_sales" name="billing[bill_type]" value="15" <?php echo $billing['bill_type'] == 15 ? 'checked' : '' ?> tabindex=20> <label for="approval_sales_bill" class="custom-label">Approval Sales Bill</label>
										</a>
									</div>
								</div>
								<p></p>
								<div align="left" style="background: #f5f5f5">
									<ul class="nav nav-tabs" id="billing-tab">
										<li id="item_summary" class="active"><a id="tab_items" href="#pay_items" data-toggle="tab">Item</a></li>
										<li id="tab_tot_summary"><a href="#tot_summary" data-toggle="tab">Total Summary</a></li>
										<li id="tab_make_pay"><a href="#make_pay" data-toggle="tab">Make Payment</a></li>
										<li id="tab_inventory_issu"><a href="#inventory_issue" data-toggle="tab">Packaging Item</a></li>
										<li id="tab_delivery_address"><a href="#customer_delivery_address" data-toggle="tab">Customer Delivery Address</a></li>
										&nbsp;&nbsp;
										<span class="bold-black-text" style="display: inline-block;">BILL AMOUNT: </span>
										<span class="billing_amount bold-black-text">0</span>
										&nbsp;&nbsp;
										<span class="bold-black-text" style="display: inline-block;">DUE AMOUNT: </span>
										<span class="due_bill_amount bold-black-text">0</span>
									</ul>



								</div>
								<div class="tab-content">
									<div class="tab-pane active" id="pay_items">
										<div class="box box-default ">
											<div class="box-body">
												<!-- Search Block	 -->
												<div class="row">
													<div class="col-sm-3 search_esti">
														<div class="row">
															<div class="col-sm-4">
																<label>EstNo. </label>
															</div>
															<div class="col-sm-8">
																<div class="form-group">
																	<div class="input-group">
																		<input class="form-control" id="filter_est_no" tabindex=-1 name="filter_est_no" type="text" placeholder="Esti No." value="" autocomplete='off' tabindex=18 />
																		<span class="input-group-btn">
																			<button type="button" id="search_est_no" class="btn btn-default btn-flat" tabindex=19><i class="fa fa-search"></i></button>
																		</span>
																	</div>
																	<p id="searchEstiAlert" class="error" align="left"></p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3 search_tag">
														<div class="row" style="display:<?php echo  ($billing['is_direct_bill_required']==1 ? 'block': 'none')?>">
															<div class="col-sm-4">
																<label>TagNo. </label>
															</div>
															<div class="col-sm-8">
																<div class="form-group">
																	<div class="input-group">
																		<input class="form-control" id="filter_tag_no" name="filter_tag_no" type="text" placeholder="Tag No." value="" tabindex=20 />
																		<span class="input-group-btn">
																			<button type="button" id="search_tag_no" class="btn btn-default btn-flat" tabindex=21><i class="fa fa-search"></i></button>
																		</span>
																	</div>
																	<p id="searchTagAlert" class="error" align="left"></p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3 search_oldtag" style="display: none;">
														<div class="row" style="display:<?php echo  ($billing['is_direct_bill_required']==1 ? 'block': 'none')?>">
															<div class="col-sm-4">
																<label>Old TagNo. </label>
															</div>
															<div class="col-sm-8">
																<div class="form-group">
																	<div class="input-group">
																		<input class="form-control" id="filter_oldtag_no" name="filter_oldtag_no" type="text" placeholder="Old Tag No." value="" tabindex=21 />
																		<span class="input-group-btn">
																			<button type="button" id="search_oldtag_no" class="btn btn-default btn-flat" tabindex=22><i class="fa fa-search"></i></button>
																		</span>
																	</div>
																	<p id="searchOldTagAlert" class="error" align="left"></p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-4 search_order" id="repair_order">
														<div class="row">
															<div class="col-sm-4">
																<label>Against Order</label>
															</div>
															<div class="col-sm-8">
																<div class="form-group">
																	<input type="radio" name="billing[is_against_order]" value="1" checked>Yes&nbsp;&nbsp;
																	<input type="radio" name="billing[is_against_order]" value="2">No
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3 search_order order_no_search">
														<div class="row">
															<div class="col-sm-4">
																<label>OrderNo. </label>
															</div>
															<div class="col-sm-8">
																<div class="form-group">
																	<div class="input-group">
																		<span class="input-group-btn">
																			<select class="form-control" id="order_fin_year_select" style="width:100px;">
																				<?php
																				foreach ($billing['financial_year'] as $fin_year) { ?>
																					<option value=<?php echo $fin_year['fin_year_code']; ?> <?php echo ($fin_year['fin_status'] == 1 ? 'selected' : '')  ?>><?php echo $fin_year['fin_year_name']; ?></option>
																				<?php }
																				?>
																			</select>
																		</span>
																		<input class="form-control" id="filter_order_no" name="billing[filter_order_no]" type="text" placeholder="Order No." value="" style="width:100px;" tabindex=15 />
																		<span class="input-group-btn">
																			<button type="button" id="search_order_no" class="btn btn-default btn-flat" tabindex=16><i class="fa fa-search"></i></button>
																		</span>
																	</div>
																	<p id="searchOrderNoAlert" class="error" align="left"></p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3 search_bill">
														<div class="row">
															<div class="col-sm-2">
																<label>BillNo. </label>
															</div>
															<div class="col-sm-8">
																<div class="form-group">
																	<div class="input-group">
																		<span class="input-group-btn">
																			<select class="form-control" id="bill_fin_year_select" tabindex="22" style="width:100px;">
																				<?php
																				foreach ($billing['financial_year'] as $fin_year) { ?>
																					<option value=<?php echo $fin_year['fin_year_code']; ?> <?php echo ($fin_year['fin_status'] == 1 ? 'selected' : '')  ?>><?php echo $fin_year['fin_year_name']; ?></option>
																				<?php }
																				?>
																			</select>
																		</span>
																		<select class="form-control" id="filter_bill_no" name="filter_bill_no" type="text" placeholder="Bill No." value="" style="width:100px;" tabindex="23"></select>
																		<input type="hidden" id="ret_bill_id" name="billing[ret_bill_id]" value="<?php echo set_value('billing[cus_name]', isset($billing['ref_bill_id']) ? $billing['ref_bill_id'] : NULL); ?>" />
																		<span class="input-group-btn">
																			<button type="button" id="search_bill_no" class="btn btn-default btn-flat" tabindex="24"><i class="fa fa-search"></i></button>
																		</span>
																	</div>
																	<p id="searchBillAlert" class="error" align="left"></p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3 credit_col" style="padding-left:100px;">
														<div class="row">
															<div class="col-sm-4">
																<label>Collection Type. </label>
															</div>
															<div class="col-sm-8">
																<div class="form-group">
																	<div class="input-group">
																		<input type="radio" name="billing[collection_type]" value="1" checked tabindex=2> Credit</label>
																		<input type="radio" name="billing[collection_type]" value="2" tabindex=3> Tobe </label>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-3 credit_col" style="padding-left:100px;">
														<div class="row">
															<div class="col-sm-2">
																<button type="button" id="credit_history" class="btn btn-info" title="Credit History Details"><i class="fa fa-plus"></i></button>
															</div>
														</div>
													</div>
													<div class="col-sm-3 eda_tax_calc" style="padding-left:100px;display:none;white-space:nowrap">
														<div class="row">
															<div class="col-sm-4">
																<label for="eda_tax_calc">EDA TAX</label>
															</div>
															<div class="col-sm-8">
																<div class="form-group">
																	<div class="input-group">
																		<input id="eda_tax_calc" type="checkbox" value="" />
																		<input type="hidden" name=billing[is_eda_tax_calc] id="is_eda_tax_calc" value="0">
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!--	<div class="col-sm-3 date_filter">
                						 	<div class="row">
                					    		<div class="col-sm-4">
                					    			<label>Choose Bill Date</label>
                						 		</div>
                						 		<div class="col-sm-8">
                						 			<div class="form-group">
                										   <a class="btn btn-default btn_date_range" id="payment-dt-btn">
                											<span  style="display:none;" id="payment_list1"></span>
                											<span  style="display:none;" id="payment_list2"></span>
                											<i class="fa fa-calendar"></i> Date range picker
                											<i class="fa fa-caret-down"></i>
                											</a>
                									</div>
                						 		</div>
                						 	</div>
                						</div>-->
												</div>
												<form id="sale_item_details">
													<div class="row sale_details">
														<div class="col-md-12">
														<p class="text-light-blue">Sales Items <?php echo  ($billing['is_direct_bill_required']==1 ? '(<a href="#"style="font-size: 20px;" style="text-decoration:none" class="sales_add" tabindex="30"><i class="icon fa fa-plus"></i></a>)': '')?></p>

															<div class="table-responsive">
																<input type="hidden" id="custom_active_id" class="custom_active_id" name="" value="" />
																<table id="billing_sale_details" class="table table-bordered table-striped text-center">
																	<thead>
																		<tr>
																			<!-- <th>#</th>
																			<th>Show HUID</th>
																			<th>HUID</th>
																			<th>Item Type</th>
																			<th>Tag Code</th>
																			<th>Employee</th>
																			<th>Is Partly</th>
																			<th>Image</th>
																			<th>HSN Code</th>
																			<th>Section</th>
																			<th>Product</th>
																			<th>Design</th>
																			<th>Sub Design</th>
																			<th>Pcs</th>
																			<th>Purity</th>
																			<th>Size</th>
																			<th>Est Rate</th>
																			<th>G.Wt</th>
																			<th>L.Wt</th>
																			<th>N.Wt</th>
																			<th>Wast(%)</th>
																			<th>Wast Wt(g)</th>
																			<th>MC Type</th>
																			<th>MC</th>
																			<th>Rate</th>
																			<th>Discount</th>
																			<th>Taxable Amt</th>
																			<th>Tax(%)</th>
																			<th>Tax</th>
																			<th>Charges</th>
																			<th>Other Metal Amt</th>
																			<th>Amount</th>
																			<th>Partly</th>
																			<th>Tag No</th>
																			<th>Employee</th>
																			<th>Order No</th>
																			<th>Est No</th>
																			<th>Action</th> -->
																			<th style="width: 100px;">#</th>
																			<th style="width: 100px;">Show HUID</th>
																			<th style="width: 100px;">HUID</th>
																			<th style="width: 100px;">Item Type</th>
																			<th style="width: 100px;">Tag Code</th>
																			<th style="width: 170px;">Employee</th>
																			<th style="width: 100px;">Is Partly</th>
																			<th style="width: 100px;">Image</th>
																			<th style="width: 100px;">HSN Code</th>
																			<th style="width: 100px;">Section</th>
																			<th style="width: 100px;">Product</th>
																			<th style="width: 100px;">Design</th>
																			<th style="width: 100px;">Sub Design</th>
																			<th style="width: 100px;">Pcs</th>
																			<th style="width: 100px;">Purity</th>
																			<th style="width: 100px;">Size</th>
																			<th style="width: 100px;">Est Rate</th>
																			<th style="width: 100px;">G.Wt</th>
																			<th style="width: 100px;">L.Wt</th>
																			<th style="width: 100px;">N.Wt</th>
																			<th style="width: 100px;">Wast(%)</th>
																			<th style="width: 100px;">Wast Wt(g)</th>
																			<th style="width: 100px;">MC Type</th>
																			<th style="width: 100px;">MC</th>
																			<th style="width: 100px;">Rate</th>
																			<th style="width: 100px;">Discount</th>
																			<th style="width: 100px;">Taxable Amt</th>
																			<th style="width: 100px;">Tax(%)</th>
																			<th style="width: 100px;">Tax</th>
																			<th style="width: 100px;">Charges</th>
																			<th style="width: 100px;">Other Metal Amt</th>
																			<th style="width: 100px;">Amount</th>
																			<th style="width: 100px;">Order No</th>
																			<th style="width: 100px;">Est No</th>
																			<th>Action</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php if ($this->uri->segment(3) == 'edit') {
																			foreach ($est_other_item['item_details'] as $ikey => $ival) {
																				$other_stone_price = 0;
																				$other_stone_wt = 0;
																				$stone_data = array();
																				foreach ($ival['stone_details'] as $data) {
																					$other_stone_price	+=	$data['price'];
																					$other_stone_wt	+=	$data['wt'];
																					$stone_data[] = array(
																						'bill_item_stone_id' => $data['bill_item_stone_id'],
																						'stone_id'			=> $data['stone_id'],
																						'stone_pcs'			=> $data['pieces'],
																						'stone_wt'			=> $data['wt'],
																						'stone_price'		=> $data['price'],
																						'rate_per_gram'      => isset($data['rate_per_gram']) ? $data['rate_per_gram'] : 0,
																					);
																				}
																				$stone_details = json_encode($stone_data);
																				echo '<tr id="' . $ikey . '">
																				<td>
																				<span>' . $ival['hsn_code'] . '</span><input type="hidden" class="bill_det_id" name="sale[bill_det_id][]" value="' . $ival['bill_det_id'] . '" /><input type="hidden" class="sale_pro_hsn" name="sale[hsn][]" value="' . $ival['hsn_code'] . '" /><input type="hidden" class="sale_type" name="sale[sourcetype][]" value="1" /><input type="hidden" class="sale_item_type" name="sale[itemtype][]" value="' . $ival['item_type'] . '" /><input type="hidden" class="is_est_details" value="1" name="sale[is_est_details][]" /><input type="hidden" class="est_itm_id" name="sale[est_itm_id][]" value="' . $ival['est_item_id'] . '" /><input type="hidden" class="sale_cal_type" name="sale[calltype][]" value="' . $ival['calculation_based_on'] . '" /><input type="hidden" class="sale_metal_type" value="' . $ival['metal_type'] . '" /><input type="hidden" class="sale_purity" value="' . $ival['purid'] . '"  name="sale[purity][]" /><input type="hidden" class="sale_size" value="' . $ival['size'] . '"  name="sale[size][]" /><input type="hidden" class="sale_uom" value="' . $ival['uom'] . '"  name="sale[uom][]" /><input type="hidden" class="total_tax" name="sale[total_tax][]"><input type="hidden" class="is_partial"  name="sale[is_partial][]" value="' . $ival['is_partial'] . '"/>
																				<input type="hidden" class="total_tax" name="sale[item_total_tax][]">
																				</td>
																				<td>
																					<span>' . $ival['product_name'] . '</span><input class="sale_product_id" type="hidden" name="sale[product][]" value="' . $ival['product_id'] . '"/>
																				</td>
																				<td><span>' . $ival['design_code'] . '</span><input type="hidden" class="sale_design_id" name="sale[design][]" value="' . $ival['design_id'] . '" />
																				</td>
																				<td><span>' . $ival['piece'] . '</span><input type="hidden" class="sale_pcs" name="sale[pcs][]" value="' . $ival['piece'] . '"/>
																				<td><span>' . $ival['gross_wt'] . '</span><input type="hidden" class="bill_gross_val" name="sale[gross][]" value="' . $ival['gross_wt'] . '" /></td>
																				</td>
																				<td><span>' . $ival['less_wt'] . '</span><input type="hidden" class="bill_less_val" name="sale[less][]" value="' . $ival['less_wt'] . '" /></td>
																				<td><span>' . $ival['net_wt'] . '</span><input type="hidden" class="bill_net_val" name="sale[net][]" value="' . $ival['net_wt'] . '" /></td>
																				<td><span>' . $ival['wastage_percent'] . '</span><input type="hidden" class="bill_wastage" name="sale[wastage][]" value="' . $ival['wastage_percent'] . '" />
																				</td>
																				<td><span>' . ($ival['mc_type'] == 1 ? 'Per Gram' : 'Per Piece') . '</span><input type="hidden" class="bill_mctype" name="sale[bill_mctype][]" value="' . $ival['mc_type'] . '" /><input type="hidden" class="bill_mc" name="sale[mc][]" value="' . $ival['mc_value'] . '" />
																				</td>
																				<td><input type="number" class="bill_discount" name="sale[discount][]" value="' . $ival['bill_discount'] . '" step="any" />
																				</td>
																				<td></td>
																				<td><span>' . $ival['tgrp_name'] . '</span>
																					<input type="hidden" class="sale_tax_group" name="sale[taxgroup][]" value="' . $ival['tax_group_id'] . '" />
																				</td>
																				<td></td>
																				<td>' . ((sizeof($ival['stone_details']) > 0) ? '<a href="#" onClick="create_new_empty_bill_sales_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a>' : '-') . '
																				<input type="hidden" class="stone_details" value="' . $stone_details . '" name="sale[stone_details][]"/>
																			<input type="hidden" class="bill_stone_price" value="' . $other_stone_price . '" />
																			<input type="hidden" class="est_old_stone_val" value="" />
																			<input type="hidden" class="est_old_dust_val" value="" />
																			<input type="hidden" class="bill_material_price" value=""/>
																				</td>
																			<td><input type="number" class="bill_amount" name="sale[billamount][]" value="' . $ival['item_cost'] . '" step="any" readonly /><input type="hidden" class="per_grm_amount" name="sale[per_grm][]" value="" step="any" />
																			</td>
																			<td>' . ($ival['is_partial'] == 0 ? 'No' : 'Yes') . '</td>
																			<td>
																			<span>' . $ival['tag_id'] . '</span><input type="hidden" class="sale_tag_id" name="sale[tag][]" value="' . $ival['tag_id'] . '" />
																			</td>
																			<td>-</td>
																			<td>
																				<span>' . $ival['est_item_id'] . '</span><input type="hidden" class="sale_est_itm_id" name="sale[estid][]" value="' . $ival['est_item_id'] . '" />
																			</td>
																			<td>
																				<a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a>
																			</td>
																				</tr>';
																											}
																										} ?>
																									</tbody>
																			<tfoot>
																				<tr></tr>
																			</tfoot>
																		</table>
																	</div>
																</div>
															</div>
												</form>
												<div class="row order_adv_details">
													<p class="text-light-blue">Order Details</p>
													<div class="box-body">
														<div class="table-responsive">
															<table id="billing_order_adv_details" class="table table-bordered table-striped text-center">
																<thead>
																	<tr>
																		<th>HSN Code</th>
																		<th>Product</th>
																		<th>Design</th>
																		<th>Pcs</th>
																		<th>G.Wt</th>
																		<th>L.Wt</th>
																		<th>N.Wt</th>
																		<th>Wast(%)</th>
																		<th>MC</th>
																		<th>Tax(%)</th>
																		<th>Order No</th>
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																</tbody>
																<tfoot>
																	<tr></tr>
																</tfoot>
															</table>
														</div>
													</div>
												</div>
												<div class="row purchase_details">
													<div class="col-md-12">
													<p class="text-light-blue">Purchase Items <?php echo  ( $billing['is_direct_bill_required']==1? '<span style="font-size: 20px;" class="purchase_add">(<i class="icon fa fa-plus"></i>)</span>': '')?></p>


														<input type="hidden" id="oldMRow" value="0" name="">
														<input type="hidden" id="old_metal_active_id" name="">
														<div class="table-responsive">
															<table id="purchase_item_details" class="table table-bordered table-striped text-center">
																<thead>
																	<tr>
																		<th>Metal</th>
																		<th>Metal Type</th>
																		<th>Category</th>
																		<th>Purity</th>
																		<th>Pcs</th>
																		<th>G.Wt</th>
																		<th>Dust.Wt</th>
																		<th>Stone.Wt</th>
																		<th>Wastage(%)</th>
																		<th>Wastage Wt</th>
																		<th>N.Wt</th>
																		<th>Rate</th>
																		<th>Exchange Value(%)</th>
																		<!-- <th>Discount</th> -->
																		<th>Stone</th>
																		<th>Employee</th>
																		<th>Purpose</th>
																		<th>Remark</th>
																		<th>Amount</th>
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																</tbody>
																<tfoot>
																	<tr></tr>
																</tfoot>
															</table>
														</div>
													</div>
												</div>

												<div class="box box-default repair_order_details" style="display:none;">
													<div class="box-body">
														<div class="row">
															<div class="box-body">
																<div class="table-responsive">
																	<h4>Repair Item Details</h4>
																	<table id="billing_repair_order_details" class="table table-bordered table-striped text-center repair_with_orderno">
																		<thead>
																			<tr>
																				<th>HSN Code</th>
																				<th>Product</th>
																				<th>Design</th>
																				<th>Pcs</th>
																				<th>G.Wt</th>
																				<th>L.Wt</th>
																				<th>N.Wt</th>
																				<th>Wast(%)</th>
																				<th>MC</th>
																				<th>Tax(%)</th>
																				<th>Order No</th>
																				<th>Completed Weight</th>
																				<th>Amount</th>
																				<th>Action</th>
																			</tr>
																		</thead>
																		<tbody>
																		</tbody>
																		<tfoot>
																			<tr></tr>
																		</tfoot>
																	</table>
																	<table id="billing_repair_order_other_details" class="table table-bordered table-striped text-center repair_without_orderno">
																		<thead>
																			<tr>
																				<th>Type</th>
																				<th>Product</th>
																				<th>Pcs</th>
																				<th>Completed Weight</th>
																				<th>TAX(%)</th>
																				<th>Amount</th>
																				<th>Action</th>
																			</tr>
																		</thead>
																		<tbody>
																		</tbody>
																		<tfoot>
																			<tr></tr>
																		</tfoot>
																	</table>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row return_details">
													<div class="col-md-12">
														<p class="text-light-blue">Return Items</p>
														<div class="table-responsive">
															<table id="sale_return_details" class="table table-bordered table-striped text-center">
																<thead>
																	<tr>
																		<th>HSN Code</th>
																		<th>Product</th>
																		<th>Design</th>
																		<th>Pcs</th>
																		<th>G.Wt</th>
																		<th>L.Wt</th>
																		<th>N.Wt</th>
																		<th>Wast(%)</th>
																		<th>MC</th>
																		<th>Sale Discount</th>
																		<th>Taxable Amt</th>
																		<th>Tax(%)</th>
																		<th>Tax</th>
																		<th>Amount</th>
																		<th>Discount</th>
																		<th>Sale Return Amt</th>
																		<th>Partly</th>
																		<th>Tag No</th>
																		<th>Order No</th>
																		<th>Est No</th>
																		<th>Action</th>
																	</tr>
																</thead>
																<tbody>
																	<?php if ($this->uri->segment(3) == 'edit') {
																		foreach ($est_other_item['return_details'] as $ikey => $ival) {
																			echo '<tr>
        												<td><span>' . $ival['hsn_code'] . '</span
        												>
        												<input type="hidden" class="bill_det_id" name="sales_return[bill_det_id][]" value="' . $ival['bill_det_id'] . '" />
        												<input type="hidden" class="sale_pro_hsn" name="sales_return[hsn][]" value="' . $ival['hsn_code'] . '"/>
        												<input type="hidden" class="sale_type" name="sales_return[sourcetype][]" value="1" /><input type="hidden" class="sale_item_type" name="sales_return[itemtype][]" value="' . $ival['item_type'] . '" /><input type="hidden" class="is_est_details" value="1" name="sales_return[is_est_details][]" /><input type="hidden" class="est_itm_id" name="sales_return[est_itm_id][]" value="' . $ival['esti_item_id'] . '" /><input type="hidden" class="sale_cal_type" name="sales_return[calltype][]" value="' . $ival['calculation_based_on'] . '" /><input type="hidden" class="sale_metal_type" value="" /><input type="hidden" class="sale_purity" value="' . $ival['purname'] . '"  name="sales_return[purity][]" /><input type="hidden" class="sale_size" value="' . $ival['size'] . '"  name="sales_return[size][]" /><input type="hidden" class="sale_uom" value="' . $ival['uom'] . '"  name="sales_return[uom][]" /></td>
        												<td><span>' . $ival['product_name'] . '</span><input class="sale_product_id" type="hidden" name="sales_return[product]" value="' . $ival['product_id'] . '" /></td>
        												<td><span>' . $ival['design_code'] . '</span><input type="hidden" class="sale_design_id" name="sales_return[design][]" value="' . $ival['design_id'] . '" /></td><td><input type="number" class="sale_pcs" name="sales_return[pcs][]" value="' . $ival['piece'] . '"  /></td>
        												<td><span>' . $ival['gross_wt'] . '</span><input type="hidden" class="bill_gross_val" name="sales_return[gross][]" value="' . $ival['gross_wt'] . '" /></td>
        												<td><span>' . $ival['less_wt'] . '</span><input type="hidden" class="bill_less_val" name="sales_return[less][]" value="' . $ival['less_wt'] . '" /></td>
        												<td><span>' . $ival['net_wt'] . '</span><input type="hidden" class="bill_net_val" name="sales_return[net][]" value="' . $ival['net_wt'] . '" /></td>
        												<td><span>' . $ival['wastage_percent'] . '</span><input type="hidden" class="bill_wastage" name="sales_return[wastage][]" value="' . $ival['wastage_percent'] . '" /></td>
        												<td><span>' . $ival['mc_type'] . '</span><input type="hidden" class="bill_mctype" name="sales_return[bill_mctype][]" value="' . $ival['mc_type'] . '" /><input type="hidden" class="bill_mc" name="sales_return[mc][]" value="' . $ival['mc_value'] . '" /></td>
        												<td><input type="hidden" class="bill_discount" name="sales_return[discount][]" value="' . $ival['discount'] . '"  />' . $ival['discount'] . '</td><td></td>
        												<td><span>' . $ival['tgrp_name'] . '</span><input type="hidden" class="sale_tax_group" name="sales_return[taxgroup][]" value="' . $ival['tax_group_id'] . '" /></td>
        												<td><span>' . $ival['item_total_tax'] . '</span></td>
        												<td><input type="hidden" class="bill_stone_price" value="" /><input type="hidden" class="bill_material_price" value=""/><input type="number" class="bill_amount" name="sales_return[billamount][]" value="' . $ival['item_cost'] . '" step="any" readonly style="width: 100px;"/><input type="hidden" class="per_grm_amount" name="sales_return[per_grm][]" value="" step="any" /></td>
        												<td><input type="number" class="sale_ret_disc_amt" name="sales_return[sale_ret_disc_amt][]" value="" step="any" style="width: 100px;"/></td>
        												<td><input type="number" class="sale_ret_amt" name="sales_return[sale_ret_amt][]" value="' . $ival['return_item_cost'] . '" step="any" readonly style="width: 100px;" readonly/></td>
        												<td>Yes</td>
        												<td><span>' . $ival['tag_id'] . '</span><input type="hidden" class="sale_tag_id" name="sales_return[tag][]" value="' . $ival['tag_id'] . '" /></td>
        												<td>-</td>
        												<td><span>' . $ival['esti_id'] . '</span><input type="hidden" class="sale_est_itm_id" name="sales_return[estid][]" value="' . $ival['esti_item_id'] . '" /></td>
        												<td><a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
        												</tr>';
																		}
																	} ?>
																</tbody>
																<tfoot>
																	<tr> </tr>
																</tfoot>
															</table>
														</div>
													</div>
												</div>
												<div class="box box-default supplier_sales_bill_details" style="display:none;">
													<div class="row">
														<div class="box-header with-border">
															<h3 class="box-title">Item Details</h3>
														</div>
														<p></p>
														<div class="col-md-12">
															<div class="row">
																<div class="col-md-3">
																	<label>Tag Code</label>
																	<div class="form-group">
																		<input type="text" class="form-control" id="tag_number" placeholder="Enter Tag Code">
																		<!--<input type="hidden" id="tag_id">-->
																	</div>
																</div>
																<div class="col-md-3">
																	<label>Old Tag</label>
																	<div class="form-group">
																		<input type="text" class="form-control" id="old_tag_number" placeholder="Enter Tag Code">
																	</div>
																</div>
																<div class="col-md-3">
																	<label></label>
																	<div class="form-group">
																		<button type="button" id="tag_history_search" class="btn btn-info">Search</button>
																	</div>
																</div>
															</div>
														</div>
														<div class="col-md-12">
															<br>
															<div class="row">
																<div class="col-md-2">
																	<div class="form-group">
																		<label>Non Tag Product</label>
																		<select class="form-control" id="select_product"></select>
																	</div>
																</div>
																<div class="col-md-2">
																	<div class="form-group">
																		<label>Design<span class="error">*</span></label>
																		<select class="form-control" id="select_design" style="width:100%;"></select>
																	</div>
																</div>
																<div class="col-md-2">
																	<div class="form-group">
																		<label>Sub Design<span class="error">*</span></label>
																		<select class="form-control" id="select_sub_design" style="width:100%;"></select>
																	</div>
																</div>
																<div class="col-md-2">
																	<div class="form-group">
																		<label>Piece<span class="error">*</span></label>
																		<input class="form-control" type="number" id="issue_pcs" placeholder="Pcs">
																		<b>Avail Pcs :<span class="available_pcs"></span></b>
																	</div>
																</div>
																<div class="col-md-2">
																	<div class="form-group">
																		<label>Weight<span class="error">*</span></label>
																		<input class="form-control" type="number" id="issue_weight" placeholder="Weight">
																		<b>Avail Wt :<span class="available_weight"></span></b>
																	</div>
																</div>
																<div class="col-md-2">
																	<label></label>
																	<div class="form-group">
																		<button type="button" id="set_non_tag_stock_list" class="btn btn-info">Add</button>
																	</div>
																</div>
															</div>
														</div>
														<p></p>
														<div class="col-md-12"><br>
															<div class="row">
																<div class="table-responsive">
																	<table id="return_item_detail" class="table table-bordered table-striped">
																		<input type="hidden" id="nontagreturnitemlist" name="billing[nontagreturnitemlist]">
																		<input type="hidden" id="returntaggeditemlist" name="billing[returntaggeditemlist]">
																		<thead style="text-transform:uppercase;">
																			<tr>
																				<th width="5%;"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all" />All</label></th>
																				<th width="10%;">Category</th>
																				<th width="10%;">Pcs</th>
																				<th width="10%;">GWt</th>
																				<th width="10%;">LWt</th>
																				<th width="10%;">Other MetalWt</th>
																				<th width="10%;">NWt</th>
																				<th width="15%;">Rate</th>
																				<th width="10%;">Taxable Amt</th>
																				<th width="10%;">Amount</th>
																				<th width="10%;">Action</th>
																			</tr>
																		</thead>
																		<tbody></tbody>
																		<tfoot>
																			<tr style="font-weight:bold;">
																				<td colspan="2" style="text-align: center;">TOTAL</td>
																				<td class="return_pcs"></td>
																				<td class="return_gwt"></td>
																				<td class="return_lwt"></td>
																				<td class="return_omwt"></td>
																				<td class="return_nwt"></td>
																				<td class=""></td>
																				<td></td>
																				<td></td>
																				<td></td>
																			</tr>
																		</tfoot>
																	</table>
																</div>
															</div>
															<p></p>
														</div>
													</div>
												</div>
												<!--<div class="box box-default custom_details" >
                						<div class="box-header with-border">
                						  <h3 class="box-title">Custom Details</h3>
                						  <div class="box-tools pull-right">
                							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                						  </div>
                						</div>
                						<div class="box-body">
                							<div class="row">
                							  <div class="box-tools pull-right">
                								<button type="button" id="create_custom_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
                							  </div>
                							</div>
                							<div class="row">
                								<div class="box-body">
                								   <div class="table-responsive">
                									 <table id="estimation_custom_details" class="table table-bordered table-striped text-center">
                										<thead>
                										  <tr>
                											<th>Product</th>
                											<th>Qty</th>
                											<th>Purity</th>
                											<th>Size</th>
                											<th>Pcs</th>
                											<th>G.Wt</th>
                											<th>L.Wt</th>
                											<th>N.Wt</th>
                											<th>Wastage(%)</th>
                											<th>MC/grm</th>
                											<th>Amount</th>
                											<th>Action</th>
                										  </tr>
                										</thead>
                										<tbody>
                											<?php if ($this->uri->segment(3) == 'edit') {
																foreach ($est_other_item['item_details'] as $ikey => $ival) {
																	if ($ival['item_type'] == 1) {
																		echo '<tr><td><input type="text" name="est_custom[product][]" value="' . $ival['product_name'] . '" class="cus_product" required /><input class="cus_product_id" type="hidden" name="est_custom[pro_id][]" value="' . $ival['product_id'] . '" /></td><td><input class="cus_qty" type="number" name="est_custom[qty][]" value="' . $ival['quantity'] . '" /></td><td><div>' . $ival['purname'] . '</div><input class="cus_purity" name="est_custom[purity][]" value="' . $ival['purid'] . '" /></td><td><input type="number" class="cus_size" name="est_custom[size][]" value="' . $ival['size'] . '" /></td><td><input class="cus_pcs" type="number" name="est_custom[pcs][]" value="' . $ival['piece'] . '" /></td><td><input type="number" class="cus_gwt" name="est_custom[gwt][]" value="' . $ival['gross_wt'] . '" /></td><td><input class="cus_lwt" type="number" name="est_custom[lwt][]" value="' . $ival['less_wt'] . '" /></td><td><input type="number" class="cus_nwt" name="est_custom[nwt][]" value="' . $ival['net_wt'] . '" readonly /></td><td><input class="cus_wastage" type="number" name="est_custom[wastage][]" value="' . $ival['wastage_percent'] . '" /></td><td><input type="number" class="cus_mc" name="est_custom[mc][]" value="' . $ival['mc_per_grm'] . '" /></td><td><input class="cus_amount" type="number" name="est_custom[amount][]" value="' . $ival['item_cost'] . '" readonly /><input type="hidden" class="cus_calculation_based_on" name="est_custom[calculation_based_on][]" value="' . $ival['calculation_based_on'] . '" /></td></tr>';
																	}
																}
															} ?>
                										</tbody>
                										<tfoot>
                											<tr></tr>
                										</tfoot>
                									 </table>
                								  </div>
                								</div>
                							</div>
                						</div>
                					</div>-->
												<!--<div class="box box-default old_matel_details" <?php echo !empty($est_other_item['old_matel_details']) ? '' : 'style="display:none;"'; ?>>
                						<div class="box-header with-border">
                						  <h3 class="box-title">Old Metal Details</h3>
                						  <div class="box-tools pull-right">
                							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                						  </div>
                						</div>
                						<div class="box-body">
                							<div class="row">
                							  <div class="box-tools pull-right">
                								<button type="button" id="create_old_matel_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
                							  </div>
                							</div>
                							<div class="row">
                								<div class="box-body">
                								   <div class="table-responsive">
                									 <table id="estimation_old_matel_details" class="table table-bordered table-striped text-center">
                										<thead>
                										  <tr>
                											<th>Type</th>
                											<th>Category</th>
                											<th>Purity</th>
                											<th>G.Wt</th>
                											<th>L.Wt</th>
                											<th>N.Wt</th>
                											<th>Wastage(%)</th>
                											<th>Rate</th>
                											<th>Amount</th>
                											<th>Send To</th>
                											<th>Action</th>
                										  </tr>
                										</thead>
                										<tbody>
                											<?php if ($this->uri->segment(3) == 'edit') {
																foreach ($est_other_item['old_matel_details'] as $ikey => $ival) {
																	echo '<tr><td><div>' . $ival['receiveditem'] . '</div><input type="hidden" class="old_item_type" name="est_oldmatel[item_type][]" value="' . $ival['item_type'] . '"  /></td><td><div>' . $ival['metal'] . '</div><input type="hidden" class="old_id_category"  name="est_oldmatel[id_category][]"value="' . $ival['id_category'] . '"  /></td><td><div>' . $ival['purname'] . '</div><input type="hidden" class="old_purity"  name="est_oldmatel[purity][]" value="' . $ival['purid'] . '" /></td><td><input type="number" class="old_gwt" name="est_oldmatel[gwt][]" value="' . $ival['gross_wt'] . '" /></td><td><input class="old_lwt" type="number" name="est_oldmatel[lwt][]" value="' . $ival['dust_wt'] . '" /></td><td><input type="number" class="old_nwt" name="est_oldmatel[nwt][]" value="' . $ival['ls_wt'] . '" readonly /></td><td><input class="old_wastage" type="number" name="est_oldmatel[wastage][]" value="' . $ival['wastage_percent'] . '" /></td><td><input type="number" class="old_rate" name="est_oldmatel[rate][]" value="' . $ival['rate_per_gram'] . '" /></td><td><input class="old_amount" type="number" name="est_oldmatel[amount][]" value="' . $ival['amount'] . '" /></td><td><input type="hidden" class="old_use_type" name="est_oldmatel[use_type][]" value="' . $ival['type'] . '" /><div>' . $ival['reusetype'] . '</div></td></tr>';
																}
															} ?>
                										</tbody>
                										<tfoot>
                											<tr></tr>
                										</tfoot>
                									 </table>
                								  </div>
                								</div>
                							</div>
                						</div>
                					</div>-->
												<div class="row">
													<div class="col-md-6">
														<div class="box box-default stone_details" <?php echo !empty($est_other_item['stone_details']) ? '' : 'style="display:none;"'; ?>>
															<div class="box-body">
																<p class="text-light-blue">Stone Details</p>
																<div class="row">
																	<div class="box-tools pull-right">
																		<button type="button" id="create_stone_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
																	</div>
																</div>
																<div class="row">
																	<div class="box-body">
																		<div class="table-responsive">
																			<table id="estimation_stone_details" class="table table-bordered table-striped text-center">
																				<thead>
																					<tr>
																						<th>Stone</th>
																						<th>Pcs</th>
																						<th>Wt</th>
																						<th>Price</th>
																						<th>Action</th>
																					</tr>
																				</thead>
																				<tbody>
																					<?php if ($this->uri->segment(3) == 'edit') {
																						foreach ($est_other_item['stone_details'] as $ikey => $ival) {
																							echo '<tr><td><div>' . $ival['stone_name'] . '</div><input type="hidden" class="stone_id" name="est_stones[stone_id][]" value="' . $ival['stone_id'] . '" /></td><td><input type="number" class="stone_pcs" name="est_stones[stone_pcs][]" value="' . $ival['pieces'] . '" /></td><td><input class="stone_wt" type="number" name="est_stones[stone_wt][]" value="' . $ival['wt'] . '" /></td><td><input type="number" class="stone_price" name="est_stones[stone_price][]" value="' . $ival['price'] . '" /></td></tr>';
																						}
																					} ?>
																				</tbody>
																				<tfoot>
																					<tr></tr>
																				</tfoot>
																			</table>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div align="right" style="display:none;">
													<button type="button" class="btn btn-warning next-tab">Next</button>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="tot_summary">
										<div class="row">
											<div class="col-sm-12">
												<div class="box box-default total_summary_details" style="display: none;">
													<div class="box-body">
														<div class="row">
															<div class="col-md-offset-1 col-md-10">
																<div class="table-responsive">
																	<table id="total_summary_details" class="table table-bordered table-striped">
																		<thead>
																			<tr>
																				<th>Weight</th>
																				<th>(Grms)</th>
																				<th>Amount</th>
																				<th>INR</th>
																			</tr>
																		</thead>
																		<tbody>
																			<tr>
																				<td>Sale Weight</td>
																				<td><span class="summary_lbl summary_sale_weight"></span></td>
																				<td>Taxable Sale Amount</td>
																				<td><span class="summary_lbl summary_sale_amt"></span></td>
																			</tr>
																			<tr style="display:none;">
																				<td></td>
																				<td><span class=""></span></td>
																				<td>TDS</td>
																				<td>
																					<div class="input-group" style="width: 40%;">
																						<input class="form-control tds_percent summary_lbl" type="number" name="billing[tds_percent]" id="tds_percent" tabindex="21" />
																						<span class="input-group-btn"><input type="number" class="form-control tds_tax_value summary_lbl" name="billing[tds_tax_value]" id="tds_tax_value" tabindex="22" style="width:100px;" readonly /></span>
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td class="text-right">CGST</td>
																				<td><span class="summary_lbl sales_cgst"></span>
																					<input type="hidden" id="cgst" class="cgst" name="billing[cgst]">
																				</td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td class="text-right">SGST</td>
																				<td><span class="summary_lbl sales_sgst"></span>
																					<input type="hidden" id="sgst" class="sgst" name="billing[sgst]">
																				</td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td class="text-right">IGST</td>
																				<td><span class="summary_lbl sales_igst"></span>
																					<input type="hidden" id="igst" class="igst" name="billing[igst]">
																				</td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td>Sale Amount</td>
																				<td><span class="summary_lbl sale_amt_with_tax"></span></td>
																			</tr>
																			<tr class="tcs_value">
																				<td></td>
																				<td></td>
																				<td class="text-right">TCS</td>
																				<td>
																					<div class="input-group" style="width: 50%;">
																						<input class="form-control tcs_percent summary_lbl" type="number" name="billing[tcs_percent]" id="tcs_percent" tabindex="21" value="" />
																						<span class="input-group-btn"><input type="number" class="form-control tcs_tax_amt summary_lbl" name="billing[tcs_tax_value]" id="tds_tax_value" tabindex="22" style="width:100px;" readonly /></span>
																					</div>
																			</tr>
																			<tr style="display:none;">
																				<td></td>
																				<td></td>
																				<td class="text-right">Other Charges</td>
																				<td>
																					<div class="input-group" style="width: 40%;">
																						<input id="other_charges_taxable_amount" name="billing[other_charges_amount]" class="form-control summary_lbl custom-inp add_other_charges" type="number" step="any" readonly tabindex="19" />
																						<span class="input-group-addon input-sm add_other_charges">+</span>
																						<input type="hidden" id="other_charges_details" name="billing[other_charges_details]" />
																					</div>
																				</td>
																			</tr>
																			<tr style="display:none;">
																				<td></td>
																				<td></td>
																				<td class="text-right">CHARGES TDS</td>
																				<td>
																					<div class="input-group" style="width: 40%;">
																						<input class="form-control tds_percent summary_lbl" type="number" name="billing[tds_percent]" id="tds_percent" tabindex="21" />
																						<span class="input-group-btn"><input type="number" class="form-control charges_tds_tax_value summary_lbl" name="billing[tds_tax_value]" id="tds_tax_value" tabindex="22" style="width:100px;" readonly /></span>
																					</div>
																				</td>
																			</tr>
																			<tr style="display:none;">
																				<td></td>
																				<td></td>
																				<td>Other Charges CGST</td>
																				<td><span class="summary_lbl other_charges_cgst"></span></td>
																			</tr>
																			<tr style="display:none;">
																				<td></td>
																				<td></td>
																				<td>Other Charges SGST</td>
																				<td><span class="summary_lbl other_charges_sgst"></span></td>
																			</tr>
																			<tr style="display:none;">
																				<td></td>
																				<td></td>
																				<td>Other Charges IGST</td>
																				<td><span class="summary_lbl other_charges_igst"></span></td>
																			</tr>
																			<tr>
																				<td>Purchase Weight</td>
																				<td><span class="summary_lbl summary_pur_weight"></span></td>
																				<td>Purchase Amount</td>
																				<td><span class="summary_lbl summary_pur_amt"></span></td>
																			</tr>
																			<tr>
																				<td>Advance Paid Weight</td>
																				<td><span class="summary_lbl summary_adv_paid_wt"></span></td>
																				<td>Advance Paid Amount</td>
																				<td><span class="summary_lbl summary_adv_paid_amt"></span><a style="display:none;" class ="advance_btn btn bg-olive btn-xs "><b>+</b></a></td>
																			</tr>
																			<tr>
																				<td>Chit Paid Weight</td>
																				<td><span class="summary_lbl summary_chit_paid_wt"></td>
																				<td>Chit Paid Amount</td>
																				<td><span class="summary_lbl summary_chit_paid_amt"></span></td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td>Repair Amount</td>
																				<td><span class="summary_lbl summary_repair_amt"></span></td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td>Credit Due Amount
																					<input type="hidden" name="billing[credit_due_amt]" id="summary_credit_due_amt">
																					<input type="hidden" name="billing[credit_ret_amt]" id="summary_credit_due_ret_amt">
																					<input type="hidden" name="billing[return_paid_amount]" id="return_paid_amount">
																				</td>
																				<td><span class="summary_lbl summary_credit_due_ret_amt"></span></td>
																			</tr>
																			<tr>
																				<td>Return Weight</td>
																				<td><span class="summary_lbl summary_sale_ret_weight"></span></td>
																				<td>Return Amount</td>
																				<td>
																					<span class="summary_lbl summary_sale_ret_amt"></span>
																					<input type="hidden" class="ret_round_off_val" name="sales_return[0][return_round_off_amt]">
																				</td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td>Credit Amount</td>
																				<td><span class="form-control summary_credit_amt" style="width: 200px;"></td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td class="text-right">Discount</td>
																				<td>
																					<div class="form-group">
																						<div class="input-group" id="sale_discount">
																							<input type="number" class="form-control summary_discount_amt summary_lbl" id="summary_discount_amt" name="billing[discount]" style="width: inherit;"><input type="hidden" id="total_discount">
																							<span class="input-group-btn">
																								<button type="button" id="disc_apply" class="btn btn-default btn-flat" style="display:none;">Apply</button>
																							</span>
																							<span class="input-group-btn" style="display: none;"> <!-- Bill Discount Reset Button-->
																								<button type="button" id="get_approval" class="btn btn-success btn-flat" title=">Get Mobile App Approval">Get Approval Status<span id="timer"></span></button>
																							</span>
																							<span class="input-group-btn"> <!-- Bill Discount Reset Button-->
																								<button type="button" id="disc_reset" class="btn btn-primary btn-flat" title="Discount Reset">Reset</button>
																							</span>
																						</div>
																					</div>
																					<div class="input-group" id="credit_discount">
																						<input type="number" class="form-control credit_discount_amt summary_lbl" id="credit_discount_amt" name="billing[credit_discount_amt]" style="width: inherit;">
																						<input type="hidden" id="credit_discount_amt_value">
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td class="text-right">Handling Charges</td>
																				<td>
																					<div class="input-group">
																						<input type="number" class="form-control handling_charges summary_lbl" id="handling_charges" name="billing[handling_charges]">
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td class="text-right">Return Charges</td>
																				<td>
																					<div class="input-group">
																						<input type="number" class="form-control return_charges summary_lbl" id="return_charges" name="billing[return_charges]">
																					</div>
																				</td>
																			</tr>

																			<tr>
																				<td></td>
																				<td></td>
																				<td class="text-right">Final Price</td>
																				<td><input type="number" class="form-control total_cost summary_lbl" id="total_cost" name="billing[total_cost]" value="" required style="width: fit-content;"><input type="hidden" id="total_payment_amount"></td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td class="text-right">Round Off</td>
																				<td><span class="summary_round_off"></span>
																					<input type="hidden" name="billing[round_off]" id="round_off">
																				</td>
																			</tr>
																			<tr>
																				<td></td>
																				<td></td>
																				<td></td>
																				<td>
																					<p class="error "><span id="paymentAlert"></span></p>
																				</td>
																			</tr>
																			<tr style="color: #FF0000;font-weight:bold;">
																				<td class="" style="font-size:14px;"></td>
																				<td></td>
																				<td class="text-right gift_row">Gift Voucher Worth</td>
																				<td><span class="summary_gift_voucher"></span>
																					<input class="form-control" id="gift_voucher_amt" name="billing[gift_voucher_amt]" type="hidden" />
																				</td>
																			</tr>
																		</tbody>
																		<tfoot>
																			<tr></tr>
																		</tfoot>
																	</table>
																	<div align="right">
																		<button type="button" class="btn btn-warning next-tab">Next</button>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="box box-info summary_adv_details" style="display:none;">
													<div class="box-header with-border">
														<h4 class="box-title">Total Summary</h4>
														<div class="box-tools pull-right">
															<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
														</div>
													</div>
													<div class="box-body">
														<div class="row">
															<div class="box-body">
																<div class="table-responsive">
																	<table id="total_summary_details" class="table table-bordered table-striped">
																		<thead>
																			<tr>
																				<th>Balance</th>
																				<th></th>
																				<th>Received Advance</th>
																				<th></th>
																				<th>Store As</th>
																			</tr>
																		</thead>
																		<tbody>
																			<tr>
																				<td>Weight (Grams)</td>
																				<td><span class="adv_blc_wt"></span></td>
																				<td>Weight (Grams)</td>
																				<td><span class="adv_rcd_wt"></span>
																					<input type="hidden" class="max_wt" name="">
																				</td>
																				<td>
																					<input type="hidden" name="billing[id_customerorder]" id="id_customerorder">
																					<input type="radio" name="billing[pur_store_as]" value="1" checked>Amount&nbsp;&nbsp;
																					<input type="radio" name="billing[pur_store_as]" value="2">Weight
																				</td>
																			</tr>
																			<tr>
																				<td>Amount(INR)</td>
																				<td><span class="adv_blc_amt"></span></td>
																				<td>Amount(INR)</td>
																				<td>
																					<input type="text" class="adv_amt" name="billing[bill_amount]">
																				</td>
																				<td>
																					<input type="radio" value="1" name="billing[sale_store_as]" checked>Amount
																					&nbsp;&nbsp;
																					<input type="radio" value="2" name="billing[sale_store_as]">Weight
																				</td>
																			</tr>
																			<tr style="font-weight: bold;">
																				<td>Advance Paid</td>
																				<td></td>
																				<td></td>
																				<td></td>
																				<td style="display:none;"></td>
																			</tr>
																			<tr>
																				<td>Weight (Grmas)</td>
																				<td><span class="adv_paid_wt"></span></td>
																				<td></td>
																				<td></td>
																				<td style="display:none;"></td>
																			</tr>
																			<tr>
																				<td>Amount (INR)</td>
																				<td><span class="adv_paid_amt"></span></td>
																				<td></td>
																				<td></td>
																				<td style="display:none;"></td>
																			</tr>
																		</tbody>
																		<tfoot>
																			<tr></tr>
																		</tfoot>
																	</table>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="make_pay">
										<div class="row">
											<div class="col-sm-12">
												<div class="box box-default payment_blk">
													<div class="box-body">
														<div class="row">
															<div class="col-sm-5">
																<div class="table-responsive">
																	<table id="payment_modes" class="table table-bordered">
																		<thead>
																		</thead>
																		<tbody>
																			<tr>
																				<td class="text-right"><b class="custom-label">Pay</b></td>
																				<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>
																				<td>
																					<input type="number" class="form-control pay_to_cus" name="billing[pay_to_cus]" value="" required readonly>
																				</td>
																			</tr>
																			<tr>
																				<td class="text-right"><b class="extra-bold">Received</b></td>
																				<th class="text-right"><b class="extra-bold"><?php echo $this->session->userdata('currency_symbol') ?></b></th>
																				<td>
																					<input type="number" class="form-control receive_amount extra-bold" name="billing[tot_amt_received]" value="<?php echo set_value('billing[tot_amt_received]', isset($billing['tot_amt_received']) ? $billing['pan_no'] : 0); ?>">
																				</td>
																			</tr>
																			<tr>
																				<td class="text-right"><b class="custom-label">Returns</b></td>
																				<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>
																				<td>
																					<input type="number" class="form-control benifit_amount" name="billing[benifits]" value="">
																				</td>
																			</tr>
																			<tr style="display:none">
																				<td class="text-right"><b class="custom-label">Calculate From</b></td>
																				<th class="text-right"></th>
																				<td>
																					<input type="radio" id="rate_calc1" value="1" name="billing[rate_calc]" checked>Gold
																					&nbsp;
																					<input type="radio" id="rate_calc2" value="2" name="billing[rate_calc]">Silver
																				</td>
																			</tr>
																			<tr>
																				<td class="text-right gst_number" style="display:none"><b class="custom-label">GST No</b></td>
																				<th class="text-right"></th>
																				<td>
																					<input type="text" class="form-control gst_number" name="billing[gst_number]" id="gst_number" value="<?php echo set_value('billing[gst_number]', isset($billing['gst_number']) ? $billing['gst_number'] : NULL); ?>" style="display:none">
																				</td>
																			</tr>
																			<tr>
																				<td class="text-right"><b class="custom-label">PAN No</b></td>
																				<th class="text-right"></th>
																				<td>
																					<input type="hidden" id="min_pan_amt" value="<?php echo $billing['min_pan_amt']; ?>">
																					<input type="hidden" id="is_pan_required" value="<?php echo $billing['is_pan_required']; ?>">
																					<input type="hidden" id="pan_verification_billing" value="<?php echo (isset($billing['pan_verification']) ? $billing['pan_verification'] : 0); ?>">
																					<div class="input-group">
														<input type="text" class="form-control pan_no" name="billing[pan_no]" id="pan_no" value="<?php echo set_value('billing[pan_no]', isset($billing['pan_no']) ? $billing['pan_no'] : NULL); ?>">
														<?php if (isset($billing['pan_verification']) && $billing['pan_verification'] == 1) { ?>
														<span class="input-group-btn">
															<button type="button" class="btn btn-primary btn-flat" id="btn_verify_pan" disabled><i class="fa fa-check-circle"></i> Verify</button>
														</span>
														<?php } ?>
													</div>
													<input type="hidden" id="pan_verified_flag" value="0">
													<span id="pan_verify_status" style="font-size:12px; display:block; margin-top:3px;"></span>
																				</td>
																			</tr>
																			<?php if ((int)$billing['insurance_amount'] > 0) { ?>
																			<tr class='insured_row1'>
																				<input type="hidden" id="insurance_amount" value="<?php echo $billing['insurance_amount']; ?>">
																				<td class="text-right"><b class="custom-label">Insurance</b></td>
																				<td class="text-right"><input type="checkbox" id="is_insured" name="billing[is_insured]" value="1" <?php echo ''; ?> style="margin-right: 10px;width: 20px;"></td>
																				<?php
																					$from_date = date('d-m-Y');
																					$to_date = date('d-m-Y', strtotime('+1 year'));
																				?>
																				<td style="display: flex;gap: 2px;">
																					<input class="form-control" id="insurance_from_date" style="width: 27%;" data-date-format="dd-mm-yyyy hh:mm:ss" name="billing[insurance_from_date]" value="" dates="<?php echo $from_date;?>" type="text" placeholder="From Date" readonly/>
																					<input class="form-control" id="insurance_to_date" style="width: 27%;" data-date-format="dd-mm-yyyy hh:mm:ss" name="billing[insurance_to_date]" value="" dates="<?php echo $to_date;?>" type="text" placeholder="To Date" readonly/>
																				</td>
																			</tr>

																			<?php } ?>
																			<tr>
																				<td class="text-right"><b class="custom-label">Adhaar No</b></td>
																				<th class="text-right"></th>
																				<td>
																					<input type="text" class="form-control aadhar_no" name="billing[aadhar_no]" id="aadhar_no" maxLength="14" value="<?php echo set_value('billing[aadhar_no]', isset($billing['aadhar_no']) ? $billing['aadhar_no'] : NULL); ?>">
																				</td>
																			</tr>
																			<tr>
																				<td class="text-right"><b class="custom-label">Driving License</b></td>
																				<th class="text-right"></th>
																				<td>
																					<input type="text" class="form-control dl_no" name="billing[dl_no]" id="dl_no" maxLength="15" value="<?php echo set_value('billing[dl_no]', isset($billing['dl_no']) ? $billing['dl_no'] : NULL); ?>">
																				</td>
																			</tr>
																			<tr>
																				<td class="text-right"><b class="custom-label">Passport</b></td>
																				<th class="text-right"></th>
																				<td>
																					<input type="text" class="form-control pp_no" name="billing[pp_no]" id="pp_no" maxLength="15" value="<?php echo set_value('billing[pp_no]', isset($billing['pp_no']) ? $billing['pp_no'] : NULL); ?>">
																				</td>
																			</tr>
																			<tr>
																				<td class="text-right"><b class="custom-label">Image</b></td>
																				<th class="text-right"></th>
																				<td>
																					<input type="file" id="pan_images">
																					<input type="hidden" id="panimg" name="billing[pan_img]">
																					<div id="pan_preview"></div>
																				</td>
																			</tr>
																		</tbody>
																	</table>
																</div>
																<label>Remark</label>
																<textarea class="form-control" id="remark" name="billing[remark]" <?php echo set_value('billing[remark]', isset($billing['remark']) ? $billing['remark'] : NULL); ?> rows="5" cols="500"> </textarea>
															</div>
															<div class="col-sm-5">
																<div class="table-responsive">
																	<table id="payment_modes" class="table table-bordered table-striped">
																		<tbody>
																			<?php if ($billing['is_credit_enable'] == 1) { ?>
																				<tr>
																					<td class="text-right">Is Credit</td>
																					<td></td>
																					<td>
																						<input type="radio" id="is_credit_no" class="is_credit" name="billing[is_credit]" value="0" <?php echo $billing['is_credit'] == 0 ? 'checked' : '' ?>> <label for="is_credit_no">&nbsp;No</label> &nbsp;&nbsp;
																						<input type="radio" id="is_credit_yes" class="is_credit" name="billing[is_credit]" value="1" <?php echo $billing['is_credit'] == 1 ? 'checked' : '' ?>><label for="is_credit_yes">&nbsp;Yes</label>
																					</td>
																				</tr>
																				<tr>
																					<td class="text-right">Is Tobe</td>
																					<td></td>
																					<td>
																						<input type="radio" id="is_to_be_no" class="is_to_be" name="billing[is_to_be]" value="0" <?php echo $billing['is_to_be'] == 0 ? 'checked' : '' ?>> <label for="is_to_be_no">&nbsp;No</label> &nbsp;&nbsp;
																						<input type="radio" id="is_to_be_yes" class="is_to_be" name="billing[is_to_be]" value="1" <?php echo $billing['is_to_be'] == 1 ? 'checked' : '' ?>><label for="is_to_be_yes">&nbsp;Yes</label>
																					</td>
																				</tr>
																				<tr>
																					<td class="text-right">Credit Due Date</td>
																					<td></td>
																					<td>
																						<input class="form-control" id="credit_due_date" data-date-format="dd-mm-yyyy hh:mm:ss" name="billing[credit_due_date]" value="" type="text" placeholder="Credit Due Date" disabled />
																					</td>
																				</tr>


																				<tr>

																					<td class="text-right">Reference</td>

																					<td></td>

																					<td>

																						<input class="form-control" id="credit_reference" name="billing[credit_reference]" value="" type="text" placeholder="Credit Reference" disabled />

																					</td>

																				</tr>

																			<?php } ?>
																			<tr>
																				<td class="text-right">Chit Utilization</td>
																				<td class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></td>
																				<td>
																					<span id="tot_chit_amt"></span>
																					<a class="btn bg-olive btn-xs pull-right" id="chit_util_modal" href="#" data-toggle="modal"><b>+</b></a>
																					<input type="hidden" id="chit_details" name="billing[chit_uti]" value="">
																				</td>
																			</tr>
																			<tr>
																				<td class="text-right">Gift Voucher</td>
																				<td class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></td>
																				<td>
																					<span id="tot_voucher_amt"></span>
																					<a class="btn bg-olive btn-xs pull-right" id="gift_voucher_modal" href="#" data-toggle="modal" data-target="#gv-confirm-add" disabled><b>+</b></a>
																					<input type="hidden" id="giftVoucher_details" name="billing[vocuher]" value="">
																				</td>
																			</tr>
																			<?php
																			$modes = $this->ret_billing_model->get_payModes();
																			if (sizeof($modes) > 0) {
																				foreach ($modes as $mode) {
																					$cash = ($mode['short_code'] == "CSH" ? '<input class="form-control" id="make_pay_cash" name="billing[cash_payment]"  type="text" placeholder="Enter Amount" autocomplete="off" value=""/>' : '');
																					$card = ($mode['short_code'] == "CC" ? '<a class="btn bg-olive btn-xs pull-right card-detail" id="card_detail_modal" href="#" data-toggle="modal" ><b>+</b></a> ' : '');
																					// data-target="#card-detail-modal"
																					$cheque = ($mode['short_code'] == "CHQ"  ? '<a class="btn bg-olive btn-xs pull-right cheque-detail" id="cheque_modal" href="#" data-toggle="modal" ><b>+</b></a> ' : '');
																					// data-target="#cheque-detail-modal"
																					$net_banking = ($mode['short_code'] == "NB"  ? '<a class="btn bg-olive btn-xs pull-right net_bank"  href="#" data-toggle="modal" id="net_bank_modal" ><b>+</b></a> ' : '');
																			?>
																					<tr>
																						<td class="text-right"><?php echo $mode['mode_name']; ?>
																						</td>
																						<td class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></td>
																						<td class="mode_<?php echo $mode['short_code']; ?>">
																							<span class="<?php echo $mode['short_code']; ?>"></span>
																							<input type="hidden" id="card_payment" name="billing[card_pay]" value="">
																							<input type="hidden" id="cus_payment_details" name="billing[cus_pay]" value="">
																							<input type="hidden" id="chq_payment" name="billing[chq_pay]" value="">
																							<input type="hidden" id="nb_payment" name="billing[net_bank_pay]" value="">
																							<?php echo $cash; ?>
																							<?php echo $card; ?>
																							<?php echo $cheque; ?>
																							<?php echo $net_banking; ?>
																						</td>
																					</tr>
																			<?php }
																			} ?>
																			<tr>
																				<td class="text-right">Advance Adj</td>
																				<td class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></td>
																				<td>
																					<span id="tot_adv_adj"></span>
																					<a class="btn bg-olive btn-xs pull-right" id="adv_adj_modal" onclick="get_advance_details()" href="#" data-toggle="modal"><b>+</b></a>
																					<input type="hidden" id="adv_adj_details" name="billing[adv_adj]" value="">
																					<input type="hidden" id="ord_adv_adj_details" name="billing[order_adv_adj]" value="">
																					<input type='hidden' id='advance_muliple_receipt' name="adv[advance_muliple_receipt][]" value="">
																					<input type="hidden" id="excess_adv_amt" name="adv[excess_adv_amt][]" value="">
																				</td>
																			</tr>
																		</tbody>
																		<tfoot>
																			<tr>
																				<th class="text-right custom-label">Total</th>
																				<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>
																				<th class="sum_of_amt"></th>
																			</tr>
																			<tr>
																				<th class="text-right extra-bold">Balance</th>
																				<th class="text-right extra-bold"><?php echo $this->session->userdata('currency_symbol') ?></th>
																				<th class="bal_amount extra-bold "></th>
																			</tr>
																			<tr style="display:none;">
																				<th class="text-right custom-label">Refund</th>
																				<th class="text-right"><input type="checkbox" id="chit_refund" name="billing[chit_refund]"></th>
																				<th class="chit_refund_amt"><input type="hidden" id="chit_refund_amt" name="billing[chit_refund_amt]" value=""></th>
																			</tr>
																			<tr>
																				<th class="text-right">Keep it As</th>
																				<!-- <th class="text-right"><input type="checkbox" id="chit_refund" name="billing[chit_refund]"></th> -->
																				<th></th>
																				<th>
																					<input type="radio" id="advance_no" name="billing[make_as_advance]" value="0" checked> <label for="advance_no" disabled>&nbsp;Refund</label>
																					<input type="radio" id="advance_yes" name="billing[make_as_advance]" value="1" disabled> <label for="advance_yes">&nbsp;Advance</label> &nbsp;&nbsp;
																					<input type="radio" id="chit_deposit" name="billing[make_as_advance]" value="2" disabled> <label for="chit_deposit">&nbsp;Chit Deposit</label> &nbsp;&nbsp;
																					<input type="hidden" id="chit_deposit_acc_details" name="billing[chit_deposit_details]" value="">
																				</th>
																				<!-- <th class="chit_refund_amt"><input type="hidden" id="chit_refund_amt" name="billing[chit_refund_amt]" value=""></th> -->
																			</tr>
																			<tr>
																				<th class="text-right">Store As</th>
																				<th></th>
																				<th>
																					<input type="radio" id="store_as_amt" name="billing[store_as]" value="0" checked disabled> <label for="store_as_amt">&nbsp;Amount</label>
																					<input type="radio" id="store_as_wt" name="billing[store_as]" value="1" disabled> <label for="store_as_wt">&nbsp;Weight</label> &nbsp;&nbsp;
																				</th>
																			</tr>
																			<tr>
																				<th class="text-right">Advance Amount</th>
																				<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>
																				<th> <input type="text" class="form-control" id="advance_amount" name="billing[advance_deposit]" placeholder="Enter Advance Amount" disabled>
																				</th>
																			</tr>
																		</tfoot>
																	</table>
																</div>
															</div>
															<div class="col-md-2 hint_class">
																<p>F1 - Cash</p>
																<p>F2 - Card</p>
																<p>F3 - NB</p>
																<p>Ctrl+S - Save</p>
															</div>
														</div>
														<div class="row">
															<div class="col-sm-12" align="center">
																<?php if ($this->uri->segment(3) != 'edit') { ?>
																	<button type="button" id="pay_submit" class="btn btn-primary" disabled>Save</button>
																<?php } ?>
																<button type="button" class="btn btn-default btn-cancel">Cancel</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row" style="display:none">
											<div class="col-sm-2">
												<label>Select Delivery</label>
											</div>
											<div class="col-sm-10">
												<div class="form-group">
													<div class="input-group">
														<select class="form-control" id="select_delivery" name="billing[id_delivery]" style="width:100%;"></select>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="inventory_issue">
										<div class="row">
											<div class="col-sm-6">
												<div class="box box-default payment_blk">
													<div class="box-body">
														<div class="row">
															<div class="col-md-12">
																<div class="box-header with-border">
																	<h3 class="box-title">Packaging Box</h3>
																	<div class="box-tools pull-right">
																		<button type="button" class="btn btn-primary" id="add_new_inv">Add</button>
																	</div>
																</div>
																<div class="row">
																	<div class="box-body">
																		<div class="table-responsive">
																			<table id="estimation_other_inv_details" class="table table-bordered table-striped text-center">
																				<thead>
																					<tr>
																						<th>Item Name</th>
																						<th>No of Pcs</th>
																						<th>Image</th>
																						<th>Action</th>
																					</tr>
																				</thead>
																				<tbody>
																				</tbody>
																				<tfoot>
																					<tr></tr>
																				</tfoot>
																			</table>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane" id="customer_delivery_address">
										<div class="row">
											<div class="col-sm-6">
												<div class="box box-default">
													<div class="box-body">
														<div class="row">
															<div class="col-md-12">
																<div class="row">
																	<div class="col-md-12">
																		<div class="col-md-6">
																			<label for="">BILLING ADDRESS</label></br>
																			<span id="cus_reg_address"></span>
																		</div>
																		<div class="col-md-6">
																			<label for="">DELIVERY ADDRESS<span class="error"></span></label></br>
																			<span id="biling_address_as_delivery_addr"></span>
																		</div>
																	</div>
																</div>
																</br>
																<div class="row add_type" style="display:none;">
																	<div class="col-md-offset-1">
																		<input type="checkbox" id="make_delivery_address" value="1" checked><span>&nbsp;Use this Billing Address as Delivery Address</span>
																		<input type="hidden" name="billing[delivery_address_type]" id="delivery_address_type" value="1">
																	</div>
																</div></br>
																<div class="row" id="my_addr" style="display: none;">
																	<div class="form-group">
																		<label class="col-md-3 col-md-offset-1 ">My Address</label>
																		<div class="col-md-12">
																			<div class="col-md-6">
																				<div class="form-group">
																					<select class="form-control" id="select_myaddr" name="billing[id_delivery_address]" style="width:100%;"></select>
																				</div>
																			</div>
																			<div class="col-md-6">
																				<button type="button" class="btn btn-primary" data-toggle="tooltip" title="Add New Delivery Address" id="add_new_delivery_address"><i class="fa fa-plus-circle"></i></button>
																			</div>
																		</div>
																	</div>
																</div></br>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="box box-default">
													<div class="box-body">
														<div class="row">
															<div class="col-md-12">
																<div class="row new_address" style="display: none;">
																	<div class="form-group">
																		<div class="row">
																			<div class="col-md-4">
																				<div class="form-group">
																					<label>Address Name<span class="error">*</span></label>
																					<input type="text" class="form-control" name="billing[del_address_name]" id="del_address_name" placeholder="Enter Address Name">
																				</div>
																			</div>
																			<div class="col-md-4">
																				<div class="form-group">
																					<label>Select Country<span class="error">*</span></label>
																					<select class="form-control" id="del_country" name="billing[del_country]" style="width:100%;"></select>
																					<input type="hidden" name="cus[id_country]" id="delivery_country">
																				</div>
																			</div>
																			<div class="col-md-4">
																				<div class="form-group">
																					<label>Select State<span class="error">*</span></label>
																					<select class="form-control" id="del_state" name="billing[del_state]" style="width:100%;"></select>
																					<input type="hidden" name="cus[id_state]" id="delivery_state">
																				</div>
																			</div>
																		</div>
																		<div class="row">
																			<div class="col-md-4">
																				<div class="form-group">
																					<label>Select City<span class="error">*</span></label>
																					<select class="form-control" id="del_city" name="billing[del_city]" style="width:100%;"></select>
																					<input type="hidden" name="cus[id_city]" id="delivery_city">
																				</div>
																			</div>
																			<div class="col-md-4">
																				<div class="form-group">
																					<label>Address1<span class="error">*</span></label>
																					<input class="form-control" id="del_address1" name="billing[del_address1]" name="customer[address1]" type="text" placeholder="Enter Address Here " />
																				</div>
																			</div>
																			<div class="col-md-4">
																				<div class="form-group">
																					<label>Address2<span class="error">*</span></label>
																					<input class="form-control" id="del_address2" name="billing[del_address2]" name="customer[address2]" type="text" placeholder="Enter Address Here " />
																				</div>
																			</div>
																		</div>
																		<div class="row">
																			<div class="col-md-4">
																				<div class="form-group">
																					<label>Address3</label>
																					<input class="form-control" id="del_address3" name="billing[del_address3]" name="customer[address3]" type="text" placeholder="Enter Address Here " />
																				</div>
																			</div>
																			<div class="col-md-4">
																				<div class="form-group">
																					<label>Pincode</label>
																					<input class="form-control" id="del_pincode" name="billing[del_pincode]" name="customer[pincode]" type="number" placeholder="Enter Pincode " />
																				</div>
																			</div>
																		</div>
																	</div></br>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<button type="button" class="btn btn-primary" id="save_new_delivery_addr">Proceed</button>
												</div>
											</div>
										</div>
									</div>
									<!--<div class="form-group">
								<div class="col-md-10">
									<label for="Offer">Bill Type :</label>
									<div class="row">
										<div class="col-md-6">
											<input type="checkbox" class="bill_type_sales" name="sales" value="1" > <label for="bill_type_sales">Sales </label>
										</div>
										<div class="col-md-6">
											<input type="checkbox" id="bill_type_purchase" name="purchase" value="1"> <label for="bill_type_purchase">Purchase </label>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<input type="checkbox" id="select_bill_type_order_advanceamt_details" name="orderadvanceamt" value="1" <?php echo !empty($est_other_item['stone_details']) ? 'checked' : ''; ?>> <label for="select_bill_type_order_advanceamt_details">Order  Advance Amount </label>
										</div>
										<div class="col-md-6">
											<input type="checkbox" id="select_advance_oldmatel_details" name="oldmateladvance" value="1" > <label for="select_advance_oldmatel_details">Order Advance Metal </label>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<input type="checkbox" id="bill_type_advance" name="advancebill" value="1" > <label for="bill_type_advance">Advance Bill </label>
										</div>
										<div class="col-md-6">
											<input type="checkbox" id="bill_type_sales_return" name="salesreturn" value="1"> <label for="bill_type_sales_return">Sales Return </label>
										</div>
									</div>
							   </div>
							</div>-->
									<p></p>
								</div> <!--/ Col -->
							</div> <!--/ row -->
							<p class="help-block"> </p>
					</div>

					<?php echo form_close(); ?>
					<!-- /form -->
				</div>
				<div class="overlay" style="display:none">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
		</section>
	</div>
	<!-- modal for fetch estimation details -->
	<div class="modal fade" id="estimation-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:75%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Select to add billing</h4>
				</div>
				<div class="modal-body">
					<div class="row" id="est_items_to_sale_convertion_tbl" style="display:none;">
						<div class="box-body">
							<p class="lead">Estimation</p>
							<div class="row">
								<div class="col-md-6">
									<label>Non Tag Available Pieces : <span id="blc_pcs"></span>,</label>
									<label>Available Weight : <span id="blc_gwt"></span></label>
								</div>
								<div class="col-md-6">
									<label>Total Pieces : <span id="tot_pcs"></span>,</label>
									<label>Total Weight : <span id="tot_wt"></span></label>
								</div>
							</div>
							<div class="table-responsive">
								<table id="est_items_to_sale_convertion" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th><label class="checkbox-inline"><input type="checkbox" id="select_Allsale" name="select_all" value="all" />All</label></th>
											<th>Product</th>
											<th>Design</th>
											<th>Pcs</th>
											<th>Purity</th>
											<th>Size</th>
											<th>G.Wt</th>
											<th>L.Wt</th>
											<th>N.Wt</th>
											<th>Wast(%)</th>
											<th>Wast Wt(g)</th>
											<th>MC</th>
											<th>Discount</th>
											<th>Tax Group</th>
											<th>Tax</th>
											<th>Amount</th>
											<th>Partly</th>
											<th>Tag No</th>
											<!--<th>Advance Paid</th> -->
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr></tr>
									</tfoot>
								</table>
								<p></p>
							</div>
						</div>
					</div>
					<div class="row" id="est_olditems_to_sale_convertion_tbl" style="display:none;">
						<div class="box-body">
							<p class="lead">Purchase</p>
							<div class="table-responsive">
								<table id="est_olditems_to_sale_convertion" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th><label class="checkbox-inline"><input type="checkbox" id="select_Allpur" name="select_all" value="all" />All</label></th>
											<th>Purpose</th>
											<th>Category</th>
											<th>G.Wt</th>
											<th>Dust.Wt</th>
											<th>Stn.Wt</th>
											<th>N.Wt</th>
											<th>Wastage(%)</th>
											<th>Wastage.Wt(%)</th>
											<th>Rate Per grm</th>
											<th>Discount</th>
											<th>Amount</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr></tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="update_estimation_to_bill" class="btn btn-success">Add</a>
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- / esti to billing modal -->
	<!--Gift Voucher-->
	<div class="modal fade" id="gv-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Gift Voucher</h4>
				</div>
				<div class="modal-body">
					<!--Gift Voucher-->
					<span class="h_amt" style="color:red;">Balance Amount:</span>

					<span class="balance_amount f_amt" style="color:red;">0</span>
					<!--<?php echo !empty($est_other_item['voucher_details']) ? '' : 'style="display:none;"'; ?>-->
					<div class="box-body gift_voucher_details">
						<div class="row">
							<!--<div class="col-sm-12 pull-right">
							<button type="button" id="create_gift_voucher_details" class="btn bg-olive  btn-sm pull-right"><i class="fa fa-plus"></i> Add</button>
							<p class="error "><span id="voucherAlert"></span></p>
						</div>-->
						</div>
						<div class="row">
							<div class="box-body">
								<div class="table-responsive">
									<table id="gift_voucher_details" class="table table-bordered text-center">
										<thead>
											<tr>
												<th>Voucher No</th>
												<th>Amount</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<?php if ($this->uri->segment(3) == 'edit') {
												foreach ($est_other_item['voucher_details'] as $ikey => $ival) {
													echo '<tr><td><input class="voucher_no" type="number" name="gift_voucher[voucher_no][]" style="width: 100px;" value="' . $ival['voucher_no'] . '" /></td><td><input type="number" class="gift_voucher_amt" style="width: 100px;"  name="gift_voucher[gift_voucher_amt][]" value=""' . $ival['gift_voucher_amt'] . '  /></td></tr>';
												}
											} else { ?>
												<tr>
													<td><input class="voucher_no" type="text" name="gift_voucher[voucher_no][]" style="width: 100px;" /><input type="hidden" class="id_gift_card"></td>
													<td><input type="number" class="gift_voucher_amt" style="width: 100px;" name="gift_voucher[gift_voucher_amt][]" readonly /></td>
													<td><a href="#" onclick="removeGift_voucher($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
												</tr>
											<?php } ?>
										</tbody>
										<tfoot>
											<tr>
												<th>Total</th>
												<th colspan=2><span class="gift_total_amount"></span></th>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!--./Gift Voucher-->
				</div>
				<div class="modal-footer">
					<a href="#" id="add_newvoucher" class="btn btn-success">Save</a>
					<button type="button" class="btn btn-close btn-warning" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- / Gift Voucher Modal -->
	<!-- Chit Utilization -->
	<div class="modal fade" id="chit-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Chit Utilization</h4>
				</div>
				<div class="modal-body">
					<!--<?php echo !empty($est_other_item['chit_details']) ? '' : 'style="display:none;"'; ?>-->
					<div class="box-body chit_details">
						<div class="row">
							<div class="col-sm-12 pull-right">
								<span class="h_amt" style="color:red;">Balance Amount:</span>

								<span class="balance_amount f_amt" style="color:red;">0</span>
								<button type="button" id="create_chit_details" class="btn bg-olive btn-sm pull-right"><i class="fa fa-plus"></i> Add</button>
								<p class="error "><span id="chitUtilAlert"></span></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">
									<label>V.A(%) - <span class="applied_wast_per"></span></label>
									<input type="number" class="form-control" id="wastage_per" placeholder="Enter V.A(%)" readonly>
								</div>
								<div class="col-md-6">
									<label>MC - <span class="applied_mc"></span></label>
									<input type="number" class="form-control" id="mc_value" placeholder="Enter MC" readonly>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="box-body">
								<div class="table-responsive">
									<table id="estimation_chit_details" class="table table-bordered text-center">
										<thead>
											<tr>
												<th>A/c Id</th>
												<th>Amount</th>
												<th>Weight</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><input class="scheme_account" type="text" style="width: 100px;" />
													<input type="hidden" class="scheme_account_id" name="chit_uti[scheme_account_id][]">
												</td>
												<td><span class="chit_amount"></span><input type="hidden" class="chit_amt" name="chit_uti[chit_amt][]" /><input type="hidden" class="chit_cash_pay" name="chit_uti[chit_cash_pay][]" /><input type="hidden" name="chit_uti[scheme_type][]" class="form-control scheme_type" id="scheme_type" value="'+items.scheme_type+'"></td>
												<td><span class="saved_weight"></span><input type="hidden" class="form-control closing_weight"><input type="hidden" name="chit_uti[wastage_per][]" class="form-control wastage_per"><input type="hidden" name="chit_uti[mc_value][]" class="form-control mc_value"><input type="hidden" name="chit_uti[savings_in_wastage][]" class="form-control savings_in_wastage"><input type="hidden" name="chit_uti[savings_in_mcvalue][]" class="form-control savings_in_mcvalue"><input type="hidden" name="chit_uti[closing_weight][]" class="form-control closing_weight"><input type="hidden" name="chit_uti[paid_installments][]" class="form-control paid_installments"><input type="hidden" name="chit_uti[total_installments][]" class="form-control total_installments"></td>
												<td><a href="#" onclick="removeChit_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
											</tr>
										</tbody>
										<tfoot>
											<tr>
												<th colspan=2>Total</th>
												<th colspan=2><input type="hidden" class="chit_cash_amt" /><span class="cht_total_amount"></span></th>
											</tr>
										</tfoot>
									</table>
									<!-- <div class="col-md-12">
        							<div class="row">
        							   <div class="col-md-4">
        							      <input type="hidden" id="mobile" value="">
        							      <input type="hidden" id="send_resend" value="0">
                                         <button class="btn btn-primary" id="send_otp" value="Send OTP">Send OTP</button>
        							   </div>
        							   <div class="col-md-4">
                                         <input type="number" class="form-control" id="user_otp" disabled>
        							   </div>
        							 </div>
        							 <span id="otp_alert"></span>
							    </div>-->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a id="add_newchit_util" class="btn btn-success" data-bs-dismiss="modal">Save</a>
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- / Chit Utilisation -->
	<!-- Advance Adj -->
	<div class="modal fade" id="adv-adj-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:75%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Advance Adjustment</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<span class="h_amt" style="color:red; font-size: 17px;">Balance Amount:</span>
							<span class="balance_amount f_amt" style="color:red; font-size: 17px;">0</span>
						</div>
					</div>
					<!--<?php echo !empty($est_other_item['chit_details']) ? '' : 'style="display:none;"'; ?>-->
					<div class="box-body chit_details">
						<div class="row">
							<div class="box-body">
								<div class="table-responsive">
									<div class="col-md-8">
										<div class="form-group">
											<label for="">Store As <span class="error"> *</span></label>&nbsp;&nbsp;
											<input type="radio" name="store_receipt_as" id="store_receipt_as_1" value="1" checked=""> Amount &nbsp;&nbsp;
											<input type="radio" name="store_receipt_as" id="store_receipt_as_2" value="2"> Weight &nbsp;&nbsp;
											<input type="hidden" id="id_ret_wallet" name="">
										</div>
									</div>
									<table id="bill_adv_adj" class="table table-bordered text-center">
										<thead>
											<tr>
												<th width="5%;">Select</th>
												<th width="10%;">Receipt No</th>
												<th width="10%;">Total Amount</th>
												<th width="10%;">Total Weight</th>
												<th width="10%;">Adjusted Amount</th>
												<th width="10%;">Balance Amount</th>
												<th width="10%;">Refund Amount</th>
												<th width="10%;">Refund Mode</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
										<tfoot>
											<tr>
												<td colspan="2">Total</td>
												<td><span class="total_adv_amt"></span></td>
												<td><span class="total_adv_wt"></span></td>
												<td><span class="total_adj_adv_amt"></span></td>
												<td><span class="total_blc_amt"></span></td>
											</tr>
											<tr>
												<td colspan="5">Total Bill Amount</td>
												<td><span class="total_bill_amt"></span></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="add_adv_adj" class="btn btn-success">Save</a>
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal" id="close_add_adj">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- / Advance Adj -->
	<!-- Card Details -->
	<div class="modal fade" id="card-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:90%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="card_close_btn close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Card Details</h4>
				</div>
				<div class="modal-body">
					<div class="box-body">
						<div class="row">
							<div class="col-sm-12 pull-right">
								<span class="h_amt" style="color:red;">Balance Amount:</span>

								<span class="balance_amount f_amt" style="color:red;">0</span>
								<button type="button" class="btn bg-olive btn-sm pull-right" id="new_card"><i class="fa fa-user-plus"></i>ADD</button>
								<p class="error "><span id="cardPayAlert"></span></p>
							</div>
						</div>
						<p></p>
						<div class="table-responsive">
							<table id="card_details" class="table table-bordered">
								<thead>
									<tr>
										<!-- Kanaga Sundar -->
										<!-- <th>Card Name</th>
										<th>Type</th>
										<th>Device<span class="error">*</span></th>
										<th>Card No</th>
										<th>Amount</th>
										<th>Approval No</th>
										<th>Action</th> -->
										 <?php $pos_req = $this->ret_billing_model->getPOSMachineRequired(); ?>
											<th width="12%">Card Name</th>
								<th width="8%">Type</th> 
								<th width="15%">Device<span class="error">*</span></th> 
								<th width="10%">Date</th>
								<th width="15%">Card No</th> 
								<th width="12%">Amount</th> 
								<th width="10%">Approval No</th> 
								<?php if($pos_req == 1) { ?><th width="13%">POS Device</th><?php } ?>
								<?php if($pos_req == 1) { ?><th width="10%">POS</th><?php } ?>
								<th width="5%">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if ($this->uri->segment(3) == 'edit') {
										/*foreach($est_other_item['card_details'] as $ikey => $ival){
										echo '<tr><td><input class="card_name" type="number" name="card_details[card_name][]" value="'.$ival['card_name'].'" /></td><td><input class="card_type" type="number" name="card_details[card_type][]" value="'.$ival['card_type'].'" /></td><td><input type="number" class="card_no" style="width: 100px;"  name="card_details[card_no][]" value="'.$ival['card_no'].'"  /></td><td><input type="number" class="card_amt" style="width: 100px;"  name="card_details[card_amt][]" value="'.$ival['card_amt'].'"  /></td><td>-</td></tr>';
								}*/
									} else { ?>
										<!--<tr>
								<td><select name="card_details[card_name][]" class="card_name"><option value="1">RuPay</option><option value="2">VISA</option><option value="3">Mastro</option><option value="4">Master</option></select></td>
								<td><select name="card_details[card_type][]" class="card_type"><option value="1">CC</option><option value="2">DC</option></select></td>
								<td><input type="number" step="any" class="card_no" name="card_details[card_no][]"/></td>
								<td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]"/></td>
								<td><input type="text" step="any" class="ref_no" name="card_details[ref_no][]"/></td>
								<td><a href="#" onclick="removeCC_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
							</tr> -->
									<?php } ?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan=4>Total</th>
										<th colspan=3>
											<span class="cc_total_amount"></span>
											<span class="cc_total_amt" style="display: none;"></span>
											<span class="dc_total_amt" style="display: none;"></span>
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="add_newcc" class="btn btn-success">Save & Close</a>
					<button type="button" class="btn btn-close btn-warning card_close_btn" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- / Advance Adj -->
	<!-- cheque-->
	<!-- Card Details -->
	<div class="modal fade" id="cheque-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:60%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="chq_close_btn close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Cheque Details</h4>
				</div>
				<div class="modal-body">
					<div class="box-body">
						<div class="row">
							<div class="col-sm-12 pull-right">
								<span class="h_amt" style="color:red;">Balance Amount:</span>

								<span class="balance_amount f_amt" style="color:red;">0</span>
								<button type="button" class="btn bg-olive btn-sm pull-right" id="new_chq"><i class="fa fa-user-plus"></i>ADD</button>
								<p class="error "><span id="chqPayAlert"></span></p>
							</div>
						</div>
						<p></p>
						<div class="table-responsive">
							<table id="chq_details" class="table table-bordered">
								<thead>
									<tr>
										<!-- <th>Cheque Date</th>
										<th>Bank</th>
										<th>Cheque No</th>
										<th>Amount</th>
										<th>Action</th> -->
										<th width="20%">Cheque Date</th>
										<th width="25%">Bank</th>
										<th width="20%">Cheque No</th>
										<th width="25%">Amount</th>
										<th width="10%">Action</th>
									</tr>
								</thead>
								<tbody>
									<!--<tr>
								<td><input id="cheque_datetime" data-date-format="dd-mm-yyyy hh:mm:ss" class="cheque_date" name="cheque_details[cheque_date][]" type="text" required="true" placeholder="Cheque Date" /></td>
								<td><input name="cheque_details[bank_name][]" type="text" required="true" class="bank_name"></td>
								<td><input name="cheque_details[bank_branch][]" type="text" required="true" class="bank_branch"></td>
								<td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]"/></td>
								<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]"/></td>
								<td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]"/></td>
								<td><a href="#" onclick="removeChq_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
							</tr> -->
								</tbody>
								<tfoot>
									<tr>
										<td>Total</td>
										<td></td>
										<td></td>
										<!-- <td></td> -->
										<!-- <td></td> -->
										<td><span class="chq_total_amount"></span></td>
										<td></td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="add_newchq" class="btn btn-success">Save & Close</a>
					<button type="button" class="btn btn-close btn-warning chq_close_btn" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- cheque-->
	<!-- Net Banking-->
	<div class="modal fade" id="net_banking_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:90%;">
			<div class="modal-content">
				<div class="modal-header">
					<!-- <button type="button" class="NB_close_btn pull-right" data-dismiss="modal"><span>&times;</span></button> -->
					<button type="button" class="NB_close_btn close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Net Banking Details</h4>
				</div>
				<div class="modal-body">
					<div class="box-body">
						<div class="row">
							<div class="col-sm-12 pull-right">
								<span class="h_amt" style="color:red;">Balance Amount:</span>

								<span class="balance_amount f_amt" style="color:red;">0</span>
								<button type="button" class="btn bg-olive btn-sm pull-right" id="new_net_bank"><i class="fa fa-user-plus"></i>ADD</button>
								<p class="error "><span id="NetBankAlert"></span></p>
							</div>
						</div>
						<p></p>
						<div class="table-responsive">
							<table id="net_bank_details" class="table table-bordered">
								<thead>
									<tr>
										<!-- <th>Type</th>
										<th class="upi_type">Bank</th>
										<th>Payment Date</th>
										<th class="device" style="display:none">Device</th>
										<th>Ref No</th>
										<th>Amount</th>
										<th>Action</th> -->
										<th width="12%">Type</th>
										<th width="13%">Bank</th>
										<?php if($pos_req == 1) { ?><th width="13%">POS Device</th><?php } ?>
										<th width="15%">Payment Date</th>
										<th width="15%">Ref No</th>
										<th width="15%">Amount</th>
										<?php if($pos_req == 1) { ?><th width="12%">POS</th><?php } ?>
										<th width="8%">Action</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th colspan=4>Total</th>
										<th>
											<span class="nb_total_amount"></span>
										</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" id="add_newnb" class="btn btn-success">Save &amp; Close</a>
				<button type="button" class="btn btn-close btn-warning NB_close_btn" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
	<!-- Net Banking-->
	<!-- Return Bill Modal -->
	<div class="modal fade" id="bill-detail-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:75%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Select to Return</h4>
				</div>
				<div class="modal-body">
					<div class="row" id="bill_items_for_return" style="display:none;">
						<div class="box-body">
							<p class="lead">Bill Item Details</p>
							<div class="table-responsive">
								<table id="bill_items_tbl_for_return" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th>Select</th>
											<th>Product</th>
											<th>Design</th>
											<th>Pcs</th>
											<th>Purity</th>
											<th>Size</th>
											<th>G.Wt</th>
											<th>L.Wt</th>
											<th>N.Wt</th>
											<th>Wast(%)</th>
											<th>MC</th>
											<th>Discount</th>
											<th>Tax Group</th>
											<th>Tax</th>
											<th>Amount</th>
											<th>Partly</th>
											<th>Tag No</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr></tr>
									</tfoot>
								</table>
								<p></p>
							</div>
						</div>
					</div>
					<div class="row" id="bill_old_items_purchased" style="display:none;">
						<div class="box-body">
							<p class="lead">Purchased Items</p>
							<div class="table-responsive">
								<table id="bill_old_items_purchased_tbl" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th>Select</th>
											<th>Purpose</th>
											<th>Category</th>
											<!--<th>Purity</th>  -->
											<th>G.Wt</th>
											<th>Dust.Wt</th>
											<th>Stn.Wt</th>
											<th>N.Wt</th>
											<th>Wastage(%)</th>
											<th>Rate Per grm</th>
											<th>Discount</th>
											<th>Amount</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr></tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="update_bill_return" class="btn btn-success">Add</a>
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- / Return Bill Modal -->
	<!-- sale stone details-->
	<div class="modal fade" id="stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static">
		<div class="modal-dialog" style="width:90%;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Add Stone</h4>
				</div>
				<div class="modal-body">

					<div class="row">
						<input type="hidden" id="active_id" name="">
						<table id="estimation_stone_item_details" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
									<th width="5%">LWT</th>
									<th width="10%">Type</th>
									<th width="13%">Name</th>
									<th width="10%">Code</th>
									<th width="7%">Pcs</th>
									<th width="15%">Wt</th>
									<th width="10%">Cal.Type</th>
									<th width="10%">Rate</th>
									<th width="12%">Amount</th>

								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="5">Total</th>
									<th colspan="3" style="text-align: left;">
										<span class="stone_total_weight"></span>
									</th>
									<th colspan="2" style="text-align: left;">
										<span class="stone_total_amount"></span>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="update_stone_details" class="btn btn-success">Save</button>
					<button type="button" id="close_stone_details" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	</div>
	<!-- sale stone details-->
	<!--Purchase stone-->
	<div class="modal fade" id="PurstoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static">
		<div class="modal-dialog" style="width:60%;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Add Stone</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-tools pull-right">
						</div>
					</div>
					<div class="row">
						<input type="hidden" id="pur_active_id" name="">
						<table id="estimation_pur_stone_item_details" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
									<th>Stone</th>
									<th>Pcs</th>
									<th>Wt</th>
									<th>Price</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr></tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="update_pur_stone_details" class="btn btn-success">Save</button>
					<button type="button" id="close_pur_stone_details" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	</div>
	<!--Purchase stone-->
	<!--Return Bill No-->
	<div class="modal fade" id="billno-detail-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:75%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Select to Return</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Select Bill No</label>
								<select id="billno_select" name="billno_select" class="form-control" style="width:100%;" multiple></select>
								<input type="hidden" id="filter_Billno" name="">
							</div>
						</div>
					</div>
					<div class="row" id="bill_items_return" style="display:none;">
						<div class="box-body">
							<p class="lead">Bill Item Details</p>
							<div class="table-responsive">
								<table id="bill_items_for_return" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th>Select</th>
											<th>Product</th>
											<th>Design</th>
											<th>Pcs</th>
											<th>Purity</th>
											<th>Size</th>
											<th>G.Wt</th>
											<th>L.Wt</th>
											<th>N.Wt</th>
											<th>Wast(%)</th>
											<th>MC</th>
											<th>Discount</th>
											<th>Tax Group</th>
											<th>Tax</th>
											<th>Amount</th>
											<th>Partly</th>
											<th>Tag No</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr></tr>
									</tfoot>
								</table>
								<p></p>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="update_billreturn" class="btn btn-success">Add</a>
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- / Return Bill Modal -->
	<!--Return Bill No-->
	<!-- emp modal -->
	<div class="modal fade" id="emp_add" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Add Employee</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="form-group">
							<label for="cus_first_name" class="col-md-3 col-md-offset-1 ">Employee Name<span class="error">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" id="emp_firstname" name="emp[firstname]" placeholder="Enter customer first name" required="true">
								<p class="help-block cus_first_name"></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label for="cus_mobile" class="col-md-3 col-md-offset-1 ">Mobile<span class="error">*</span></label>
							<div class="col-md-6">
								<input type="number" class="form-control" id="emp_mobile" name="emp[mobile]" placeholder="Enter customer mobile">
								<p class="help-block cus_mobile"></p>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="add_newemployee" class="btn btn-success">Add</a>
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="cus_other_charges_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:60%;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Add Charges</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-tools pull-right">
							<button type="button" id="add_new_charge" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
						</div>
					</div>
					<div class="row">
						<table id="estimation_other_charge_cus_item_details" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
									<th>Charge Name</th>
									<th>Value</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr></tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="update_charge_details" class="btn btn-success">Save</button>
					<button type="button" id="close_charge_details" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	</div>
	<!-- / emp modal -->
	<!-- <div class="modal fade" id="charge_items_popup" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Charges</h4>
				</div>
				<div class="modal-body">
					<div>
						<table id="billing_charges_details" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
									<th>Charge Code</th>
									<th>Value</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div> -->
	<!-- CHIT DEPOSIT -->
	<div class="modal fade" id="chit_deposit_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Account Details</h4>
				</div>
				<div class="modal-body">
					<div>
						<table id="chit_deposit_details" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
									<th>Select Scheme</th>
									<th>Select Account</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="save_chit_deposit">Save</button>
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- CHIT DEPOSIT -->
	<!-- OTP MODAL POPUP -->

	<div class="modal fade" id="otp_validation" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">OTP Validation </h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-md-12" id="discountApprovalMessage">
							</div>
							<div class="col-md-6" id="otp_form">
								<div class="form-group">
									<label>OTP Verification</label>
									<div class="input-group margin">
										<input type="text" id="otp_by_emp" class="form-control" required placeholder="6 Digit OTP" />
										<span class="input-group-btn">
											<button type="button" class="btn btn-warning btn-flat" id="resend_otp">Resend OTP</button>
										</span>
									</div>
								</div>
							</div>
							<div class="col-md-6 otp_form">


								<div class="form-group">

									<label>OTP Verified By</label>

									<div class="col-md-8">

										<select style="width:200px" class="form-control" name="approved_by_emp" id="emp_select_otp"></select>

									</div>

								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="send_discount_otp" class="btn btn-success">Yes</a>
					<a href="#" id="otp_submit_validation" class="btn btn-success">Submit</a>
					<button type="button" class="btn btn-close btn-danger" id="discount_close_modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="otp_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Verify OTP and Update Status</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-12" id="CreditApprovalMessage">
							</div>
						</div>
					</div>
					<p></p>
					<div class="row otp_block">
						<div class="col-md-2">
							<div class='form-group'>
								<label for="">OTP</label>
							</div>
						</div>
						<div class="col-md-5">
							<div class='form-group'>
								<div class='input-group'>
									<input type="text" id="credit_otp" name="otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required />
									<span class="input-group-btn">
										<button type="button" id="verify_credit_otp" class="btn btn-primary btn-flat" disabled>Verify</button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class='form-group'>
								<input type="button" id="resend_credit_otp" class="btn btn-warning btn-flat" disabled value="Resend OTP" /> <span id="timer"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<span class="otp_alert"></span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="send_credit_otp" class="btn btn-success">Yes</a>
					<a href="#" id="send_credit_otp_no" class="btn btn-danger btn-flat">No</a>
					<button type="button" id="approve" class="btn btn-success btn-flat" disabled>Approve</button>
					<button type="button" id="cancel_credit_otp" class="btn btn-danger btn-flat" id="close">Close</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="customer-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Customer Details</h4>
				</div>
				<div class="modal-body">
					<form id="cus_pop"></form>
					<div id="cus_bill_details"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!--Credit History Details-->
	<div class="modal fade" id="bill-credit-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog" style="width:75%;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Select Pending Bill</h4>
				</div>
				<div class="modal-body">
					<div class="row" id="bill_items_for_pending" style="display:none;">
						<div class="box-body">
							<p class="lead">Credit Pending Details</p>
							<div class="table-responsive">
								<table id="bill_items_tbl_for_pending" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th>Select</th>
											<th>Bill No</th>
											<th>Bill Date</th>
											<th>Tot Bill Amount</th>
											<th>Due Amount</th>
											<th>Paid Amount</th>
											<th>Bal Amount</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr></tr>
									</tfoot>
								</table>
								<p></p>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="update_bill_credit" class="btn btn-success">Add</a>
					<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!--Credit History Details-->
	<div class="modal fade" id="imageModal_tag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" style="width:60%;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Image Preview</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div id="order_images" style="margin-top: 2%;"></div>
					</div>
				</div>
				<div class="modal-footer">
					</br>
					<button type="button" id="close_stone_details" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- <div class="modal fade" id="confirm-add" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Add Customer</h4>
				</div>
				<div class="modal-body">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_general" data-toggle="tab">GENERAL</a></li>
						<li><a href="#tab_kyc" data-toggle="tab">KYC</a></li>
					</ul>
					<div class="tab-content"><br />
						<div class="tab-pane active" id="tab_general">
							<div class="row" style="display:none;">
								<div class="form-group">
									<label for="" class="col-md-3 col-md-offset-1 ">Customer Type<span class="error"></span></label>
									<div class="col-md-6">
										<input type="radio" id="cus_type1" name="cus[cus_type]" value="1" class="minimal" checked /> Individual
										<input type="radio" id="cus_type2" name="cus[cus_type]" value="2" class="minimal" /> Business
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="cus_first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error">*</span></label>
									<div class="input-group">
										<span class="input-group-addon">
											<select name="cus[title]" id="title">
												<option value="" disabled hidden></option>
												<option value="Mr" selected>Mr</option>
												<option value="Ms">Ms</option>
												<option value="Mrs">Mrs</option>
												<option value="Dr">Dr</option>
												<option value="Prof">Prof</option>
											</select>
										</span>
										<input type="text" class="form-control" style="width:65%;" id="cus_first_name" name="cus[first_name]" placeholder="Enter customer first name" required="true">
									</div><br>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="cus_gender" class="col-md-3 col-md-offset-1 ">Gender<span class="error">*</span></label>
									<div class="col-md-6">
										<input type="radio" name="customer[gender]" value="0" id="gender0" class="minimal" <?php if ($customer['gender'] == 0) { ?> checked <?php } ?> required />Male
										<input type="radio" name="customer[gender]" value="1" id="gender1" class="minimal" <?php if ($customer['gender'] == 1) { ?> checked <?php } ?> />Female
										<input type="radio" name="customer[gender]" value="3" id="gender2" class="minimal" <?php if ($customer['gender'] == 3) { ?> checked <?php } ?> />Others
										<p class="help-block cus_gender error"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="cus_mobile" class="col-md-3 col-md-offset-1 ">Mobile<span class="error">*</span></label>
									<div class="col-md-6">
										<input type="number" class="form-control" id="cus_mobile" name="cus[mobile]" placeholder="Enter customer mobile">
										<p class="help-block cus_mobile error"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="cus_email" class="col-md-3 col-md-offset-1 ">Email</label>
									<div class="col-md-6">
										<input type="text" class="form-control" id="cus_email" name="cus[cus_email]" placeholder="Enter Email ID">
										<p class="help-block cus_email error"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="" class="col-md-3 col-md-offset-1 ">Select Country<span class="error">*</span></label>
									<div class="col-md-6">
										<select class="form-control" id="country" style="width:100%;"></select>
										<input type="hidden" name="cus[id_country]" id="id_country">
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="" class="col-md-3 col-md-offset-1 ">Select State<span class="error">*</span></label>
									<div class="col-md-6">
										<select class="form-control" id="state" style="width:100%;"></select>
										<input type="hidden" name="cus[id_state]" id="id_state">
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="" class="col-md-3 col-md-offset-1 ">Select City<span class="error">*</span></label>
									<div class="col-md-6">
										<select class="form-control" id="city" style="width:100%;"></select>
										<input type="hidden" name="cus[id_city]" id="id_city">
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="pincode" class="col-md-3 col-md-offset-1">Pin Code<span class="error"></span></label>
									<div class="col-md-6">
										<input class="form-control titlecase" id="pin_code_add" type="number" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' required />
										<p class="help-block pincode error"></p>
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="" class="col-md-3 col-md-offset-1 ">Select Area</label>
									<div class="col-md-5">
										<select class="form-control" id="sel_village" style="width:100%;"></select>
										<input type="hidden" name="cus[id_village]" id="id_village" name="">
										<p class="help-block sel_village error"></p>
									</div>
									<span class="input-group-btn">
										<button type="button" class="btn btn-success add_new_village"><i class="fa fa-plus"></i></button>
									</span>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="address1" class="col-md-3 col-md-offset-1 ">Address1<span class="error">*</span></label>
									<div class="col-md-6">
										<input class="form-control" id="address1" name="customer[address1]" value="" type="text" placeholder="Enter Address Here 1" required />
										<p class="help-block address1 error"></p>
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="address2" class="col-md-3 col-md-offset-1">Address2</label>
									<div class="col-md-6">
										<input class="form-control" id="address2" name="customer[address2]" placeholder="Enter Address Here 2" value="" type="text" />
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="address3" class="col-md-3 col-md-offset-1">Address3</label>
									<div class="col-md-6">
										<input class="form-control titlecase" id="address3" name="customer[address3]" value="" type="text" placeholder="Enter Address Here 3" />
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="" class="col-md-3 col-md-offset-1 ">Select Profession</label>
									<div class="col-md-6">
										<select class="form-control" id="profession" style="width:100%;"></select>
										<input type="hidden" name="cus[profession]" id="professionval">
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="pincode" class="col-md-3 col-md-offset-1">Date of Birth</label>
									<div class="col-md-6">
										<input class="form-control date_of_birth" id="date_of_birth" name="customer[date_of_birth]" value="<?php echo set_value('customer[date_of_birth]', $customer['date_of_birth']); ?>" type="text" />
										<p class="help-block pincode error"></p>
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="pincode" class="col-md-3 col-md-offset-1">Wedding Date</label>
									<div class="col-md-6">
										<input class="form-control date_of_wed" id="date_of_wed" name="customer[date_of_wed]" value="<?php echo set_value('customer[date_of_wed]', $customer['date_of_wed']); ?>" type="text" />
										<p class="help-block pincode error"></p>
									</div>
								</div>
							</div></br>
							<div class="row">
								<div class="form-group">
									<label for="" class="col-md-3 col-md-offset-1 ">Upload Image<span class="error">*</span></label>
									<div class="col-md-6">
										<input id="cus_image" name="cus_img" accept="image/*" type="file">
										<p class="help-block cus_mobile"></p>
										<img src="<?php echo base_url('assets/img/default.png') ?>" class="img-thumbnail" id="cus_img_preview" style="width:175px;height:100%;" alt="Customer image">
										<input type="hidden" name="customer[customer_img]" value="<?php echo set_value('customer[customer_img]', $customer['cus_img']) ?>" />
									</div>
								</div>
							</div>
							<div class="row gst" style="display:none;">
								<div class="form-group">
									<label for="" class="col-md-3 col-md-offset-1 ">GST No<span class="error"></span></label>
									<div class="col-md-6">
										<input type="text" class="form-control" id="gst_no" name="cus[gst_no]" placeholder="Enter GST No">
										<p class="help-block cus_mobile"></p>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab_kyc">
							<div class="row">
								<div class="form-group">
									<label for="cus_pan" class="col-md-3 col-md-offset-1 ">Pan</label>
									<div class="col-md-6">
										<input type="text" class="form-control pan_no" id="pan" name="cus[pan]" placeholder="Enter Pan ID">
										<p class="help-block cus_email error"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="cus_aadhar" class="col-md-3 col-md-offset-1 ">Aadhar</label>
									<div class="col-md-6">
										<input type="text" class="form-control" id="aadharid" name="cus[cus_aadhar]" maxlength="14" placeholder="Enter aadhar ID">
										<p class="help-block cus_email error"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="cus_dl" class="col-md-3 col-md-offset-1 ">Driving License</label>
									<div class="col-md-6">
										<input type="text" class="form-control dl_no" id="dl" name="cus[cus_dl]" maxlength="15" placeholder="Enter Driving License No">
										<p class="help-block cus_email error"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for="cus_dl" class="col-md-3 col-md-offset-1 ">PassPort</label>
									<div class="col-md-6">
										<input type="text" class="form-control pp_no" id="pp" name="cus[cus_pp]" maxlength="15" placeholder="Enter Passport No">
										<p class="help-block cus_email error"></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="cus[id_customer]" id="id_customer" value="">
					<a href="#" id="add_newcutomer" class="btn btn-success">Add</a>
					<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div> -->
	<!--<div class="modal fade" id="confirm-area" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Add Village</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="form-group">
							<label for="pincode" class="col-md-3 col-md-offset-1">Pincode</label>
							<div class="col-md-6">
								<input class="form-control titlecase" id="new_pincode" type="text" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' readonly required />
							</div>
						</div>
					</div></br>
					<div class="row">
						<div class="form-group">
							<label for="area" class="col-md-3 col-md-offset-1 ">Area<span class="error">*</span></label>
							<div class="col-md-6">
								<input class="form-control" id="village" value="" type="text" placeholder="Enter Area Here " required />
								<p class="help-block address1 error"></p>
							</div>
						</div>
					</div></br>
				</div></br>
				<div class="modal-footer">
					<input type="hidden" name="cus[id_customer]" id="id_customer" value="">
					<a href="#" id="add_new_area" class="btn btn-success">Add</a>
					<button type="button" class="btn btn-warning new_village_close" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>-->
	<div class="modal fade" data-backdrop="static" data-keyboard="false" id="order_otp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Verify OTP and Update Status</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<h5>We have sent OTP to autorized mobile number. Kindly verify OTP to proceed further.</h5>
						</div>
					</div>
					<p></p>
					<div class="row otp_block">
						<div class="col-md-2">
							<div class='form-group'>
								<label for="">OTP</label>
							</div>
						</div>
						<div class="col-md-5">
							<div class='form-group'>
								<div class='input-group'>
									<input type="text" id="order_trns_otp" name="order_trns_otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required />
									<span class="input-group-btn">
										<button type="button" id="verify_order_otp" class="btn btn-primary btn-flat" disabled>Verify</button>
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class='form-group'>
								<input type="button" id="resend_order_otp" class="btn btn-warning btn-flat" value="Resend OTP" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<span class="otp_alert"></span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="submit_order_delievery btn btn-success btn-flat" disabled>Save And Submit</button>
					<button type="button" class="btn btn-danger btn-flat" data-dismiss="modal" id="close">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- old metals -->
	<div class="modal fade" id="old_stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static">
		<div class="modal-dialog" style="width:80%;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Add Stone</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="box-tools pull-right">
							<button type="button" id="create_stone_old" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
						</div>
					</div>
					<div class="row">
						<table id="estimation_stone_old_metal_details" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
									<th>LWT</th>
									<th>Type</th>
									<th>Name</th>
									<th>Pcs</th>
									<th>Wt</th>
									<th>Cal.Type</th>
									<th>Rate</th>
									<th>Amount</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody></tbody>
							<tfoot>
								<tr></tr>
							</tfoot>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="update_stone_details" class="btn btn-success">Save</button>
					<button type="button" id="close_stone_details" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	</div>
	<!-- old metals -->
	<div class="modal fade" id="credit_disc_otp_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header disc_otp">
					<button type="button" class="close" id='close_modl'><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">OTP Validation </h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
					</div>
					<div class="row disc_otp">
						<div class="form-group">
							<div class="col-md-8">
								<div class="form-group">
									<label>OTP Verification</label>
									<div class="input-group margin">
										<input type="text" id="cc_disc_otp" class="form-control" required placeholder="6 Digit OTP" />
										<span class="input-group-btn">
											<button type="button" class="btn btn-warning btn-flat" id="cc_resend_otp">Resend OTP</button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Confirmation -->
					<div class="row otp_confirm">
						<div class="form-group">
							<div class="col-md-8">
								<div class="form-group">
									<label>OTP Confirmation</label>
									<div class="input-group margin">
										OTP will be sent to the administrator for approval. Do you wish to proceed?
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer disc_otp">
					<a href="#" id="verfiy_cc_otp" class="btn btn-success">Verify</a>
					<button type="button" class="btn btn-danger" id="cc_discount_close_modal">Close</button>
				</div>
				<div class="modal-footer otp_confirm">
					<button type="button" class="btn btn-success" id="send_cc_disc_otp_yes">Yes</button>
					<button type="button" class="btn btn-danger" id="send_cc_disc_otp_no">No</button>
				</div>
			</div>
		</div>
	</div>


	<!-- Discount App Approval -->
	<div class="modal fade" id="app_aproval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<!-- <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->
					<h4 class="modal-title" id="myModalLabel">Mobile App Approval</h4>
				</div>
				<div class="modal-body">
					Please wait, Till This Transaction Get Approval From Management ...<span style="color:red" class="show_timer"></span>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success btn-flat get_approval" title=">Get Mobile App Approval">Get Approval Status</button>
					<button type="button" class="cancel_bill_disc_aprvl btn btn-close btn-warning" data-bs-dismiss="modal">Cancel Approval</button>
				</div>
			</div>
		</div>
	</div>
	<!--End of Discount App Approval -->
	<!-- Credit Collection app Approval -->
	<div class="modal fade" id="cc_app_aproval_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<!-- <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->
					<h4 class="modal-title" id="myModalLabel">Mobile App Approval</h4>
				</div>
				<div class="modal-body">
				Please wait, Till This Transaction Get Approval From Management ...<span style="color:red" class="show_timer"></span>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success btn-flat get_approval" title=">Get Mobile App Approval">Get Approval Status</button>
					<button type="button" class="cancel_cc_aprvl btn btn-close btn-warning" data-bs-dismiss="modal">Cancel Approval</button>
				</div>
			</div>
		</div>
	</div>
	<!-- End of Credit Collection app Approval -->


	<!-- Other Metal Details -->

<div class="modal fade" id="other_metalmodal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog" style="width:72%;">

<div class="modal-content">

<div class="modal-header">

<h4 class="modal-title" id="myModalLabel">Other Metals</h4>

</div>

<div class="modal-body">

<div class="row">

	<div class="col pull-right">

		<!-- <button type="button" id="create_other_metal_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button> -->

	</div>

</div>

<div class="row">

	<input type="hidden" id="charge_active_row" value="0">

	<table id="other_metal_table" class="table table-bordered table-striped text-center">

		<thead>

			<tr>

				<th width="15%">Metal</th>

				<th width="10%">Purity</th>

				<th width="10%">Pcs</th>

				<th width="10%">Gwt</th>

				<th width="10%">V.A(%)</th>

				<th width="10%">Mc Type</th>

				<th width="10%">Mc</th>

				<th width="10%">Rate</th>

				<th width="10%">Amount</th>

			</tr>

		</thead>

		<tbody></tbody>

		<tfoot><tr style="font-weight:bold;"><td>Total</td>
		<td></td><td class="total_pcs"></td>
		<td class="total_wt"></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td class="total_amount"></td>
	</tr></tfoot>

	</table>

</div>

</div>

<div class="modal-footer">
						<button type="button" id="save_other_metal_details" class="btn btn-primary">Save</button>
						<button type="button" id="tag_other_metal_details" class="btn btn-close btn-warning " data-bs-dismiss="modal">Close</button>
					</div>

</div>

</div>

</div>


 <!-- Other Metal Details -->


 <!-- Order Advance Adj -->

<div class="modal fade" id="ord_adv_adj" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog" style="width:75%;">

	<div class="modal-content">

		<div class="modal-header">

			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

			<h4 class="modal-title" id="myModalLabel">Order Advance Adjustment</h4>

		</div>

		<div class="modal-body">

			<div class="box-body chit_details">

				<div class="row">

					<div class="box-body">

						<div class="table-responsive">

							<table id="bill_ord_adv_adj" class="table table-bordered text-center">

								<thead>

									<tr>

										<th width="5%;">Select</th>

										<th width="10%;">Bill No</th>

										<th width="10%;">Total Amount</th>

										<!-- <th width="10%;">Total Weight</th> -->

										<th width="10%;">Adjusted Amount</th>

										<th width="10%;">Balance Amount</th>

										<!-- <th width="10%;">Refund Amount</th>

										<th width="10%;">Refund Mode</th> -->

									</tr>

								</thead>

								<tbody>

								</tbody>

								<tfoot>

									<tr>

										<td colspan="2" >Total</td>

										<td><span class="total_ord_adv_amt"></span></td>

										<!-- <td><span class="total_adv_wt"></span></td> -->

										<td><span class="total_ord_adj_adv_amt"></span></td>

										<td><span class="total_ord_blc_amt"></span></td>

									</tr>

									<!-- <tr>

										<td colspan="4">Total Bill Amount</td>

										<td><span class="total_bill_amt"></span></td>

									</tr> -->

								</tfoot>

							</table>

						</div>

					</div>

				</div>

			</div>


		</div>

		<div class="modal-footer">

			<a href="#" id="add_ord_adv_adj" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-bs-dismiss="modal" id="close_add_ord_adj">Close</button>

		</div>

	</div>

</div>

</div>

<!-- / Advance Adj -->