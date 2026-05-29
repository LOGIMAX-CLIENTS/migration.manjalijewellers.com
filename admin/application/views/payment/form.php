<?php
$form_secret = get_form_secret_key();
?>
<style>
	#timer {
		font-size: 1em;
		color: #ff6347;
		/* Tomato Red */
		margin-bottom: 14px;
		font-weight: bold;
	}

	.h_amt {
		font-size: 23px;
	}

	.f_amt {
		font-size: 26px;
	}

	.highlight-enabled {
		background-color: #cde4ff !important;
		color: #000 !important;
		border-radius: 50% !important;
	}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Payment
			<small></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="<?php echo site_url('payment/list'); ?>">Payment</a></li>
			<li class="active">Scheme Payment</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
				<div class="row">
					<div class="col-md-3">
						<h3 class="box-title">Payment Form</h3>
					</div>
					<div class="col-md-3">
						<a class="btn btn-success pull-right" id="add_customer" href="<?php echo base_url('index.php/customer/add'); ?>"><i class="fa fa-plus-circle"></i> Add Customer</a>
					</div>
					<div class="col-md-3">
						<a class="btn btn-success pull-right" id="add_customer" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-plus-circle"></i> Add Account</a>
					</div>
					<?php if ($pay['metalrate_edit'] == 1) { ?>
						<div class="col-md-3">
							<div class="form-group">
								<label for="enable_editing">Edit Metal Rate</label>
								<input type="checkbox" name="generic[enable_editing]" id="enable_editing" value='1' />
								<input type="hidden" id="metal_rate_datelimit" value="<?php echo $pay['metal_rate_datelimit']; ?>">
							</div>
						</div>
					<?php } ?>
				</div>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<!-- <?php $attributes = array('autocomplete' => 'off', 'id' => 'pay_form');
						echo form_open(($pay['id_payment'] != NULL && $pay['id_payment'] > 0 ? 'payment/update/' . $pay['id_payment'] : 'payment/save_all'), $attributes) ?>-->
				<form id="pay_form">
					<input type="hidden" id="login_branch" value="<?php echo $this->session->userdata('id_branch'); ?>">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label for="">Customer Mobile<span class="error">*</span></label>
									<input type="text" class="form-control mobile_number" name="mobile_number" placeholder="Enter Mobile Number" required id="mobile_number" value="" style="width: 99%;">
									<!--<select class="form-control" id="customer"></select>-->
									<input type="hidden" name="generic[id_customer]" id="id_customer" value="<?php echo $this->session->userdata('cus_id'); ?>" />
									<input type="hidden" name="generic[enable_old_sch_acc_no]" id="enable_old_sch_acc_no" value="<?php echo $this->session->userdata('enable_old_sch_acc_no'); ?>" />
									<input type="hidden" id="session_cus_id" value="<?php echo $this->session->userdata('cus_id'); ?>" />
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
										<span>
											Search By Account No
											<span class="error" style="font-size: 10.5px;">(Type Only Acc.Number)</span>
										</span>
										<div id="oldAccWrapper" style="display: flex; align-items: center; gap: 5px;">
											<input type="checkbox" id="oldAccountCheck">
											<span>Old Acc No</span>
										</div>
									</label>
									<input type="text" class="form-control Scheme_account_no"
										name="Scheme_account_no"
										placeholder="Enter Scheme Account No"
										id="Scheme_account_no"
										style="width: 99%;">
								</div>
							</div>
							<!--	<div class="col-md-4">
							 <div class="form-group" >
								<label>Select Scheme &nbsp;</label>
								  <?php if ($this->session->userdata('id_scheme_account') == '') { ?>
										 <select id="scheme_select" class="form-control" style="width:100%;"></select>
										 <input id="id_scheme" name="generic[id_scheme]" type="hidden" value=""/>
									<?php } else { ?>
										 </br>
										  <label><?php echo $this->session->userdata('scheme_name'); ?></label>
										 <input type="hidden" name="generic[id_scheme]" class="form-control" id="id_scheme"  value="<?php echo $this->session->userdata('id_scheme'); ?>" />
									<?php } ?>
							 </div>
					 </div> -->
							<div class="col-md-3">
								<div class="form-group">
									<label for="">Scheme A/c No<span class="error">*</span></label>
									<select class="form-control" name="generic[id_scheme_account]" id="scheme_account" style="width:100%;" required></select>
									<?php if ($this->session->userdata('id_scheme_account') == '') { ?>
										<input type="hidden" class="form-control" id="id_scheme_account" value="<?php echo set_value('generic[id_scheme_account]', $pay['id_scheme_account']); ?>" />
									<?php } else { ?>
										<input type="hidden" class="form-control" id="id_scheme_account" value="<?php echo $this->session->userdata('id_scheme_account'); ?>" />
									<?php } ?>
									<?php
									$this->session->unset_userdata("cus_id");
									$this->session->unset_userdata("id_scheme_account");
									$this->session->unset_userdata("cus_mobile");
									$this->session->unset_userdata("cus_name");
									$this->session->unset_userdata("id_scheme");
									$this->session->unset_userdata("scheme_name");
									?>
								</div>
							</div>
							<!-- QR Code block-->
							<div class="col-md-3 ">
								<div class="form-group">
									<label for="">SCAN QR/ BAR CODE</label>
									<input id="qr_scan_scheme_account_id" type="text" class="form-control" />
								</div>
							</div>
							<!-- QR Code block ends-->
							<!-- <div class="col-sm-4 pull-right" id="enable_editing_blk" style="display<?php echo ($pay['edit_addpay_page'] == 1 ? ':block' : ':none') ?>" > 
				 <?php if (($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <= 2) || $this->config->item('showToAdminsOnly') == 0) { ?> 
				 <div class="form-group">
					 <label for="" >Enable editing</label>
					 <input type="checkbox" name="generic[enable_editing]" id="enable_editing" value='1' />
				 </div>	
				 <?php } else { ?> 
				 <input type="hidden" name="generic[enable_editing]" value='0' />
				  <?php } ?> 
			  </div> -->
						</div>
						<!-- setting based branch option hh -->
						<!-- 11-01-2023 #AB branch auto store based on emp log -->
						<!-- <div class="row">
		   <?php if (($this->session->userdata('branch_settings') == 1) && (($this->session->userdata('is_branchwise_cus_reg') == 0 || $this->config->item('payOtherBranch') == 1) || ($this->session->userdata('branchWiseLogin') != 1))) { ?>
			 <div class="col-md-4">
						  <?php if (($this->session->userdata('branch_settings') == 1) && (($this->session->userdata('is_branchwise_cus_reg') == 0 || $this->config->item('payOtherBranch') == 1)) || ($this->session->userdata('branchWiseLogin') != 1)) { ?>
							 <div class="form-group" >
								<label>Select Branch &nbsp;</label>
								 <select id="branch_select" class="form-control" style="width:100%;">
								 </select>
								 <input id="id_branch" name="generic[id_branch]" type="hidden" value=""/>
								 <input type="hidden"  id="branch_settings" value="<?php echo $this->session->userdata('branch_settings'); ?>" >
							 </div>
						<?php } else { ?>
							<input type="hidden" name="generic[id_branch]"  value="<?php echo $this->session->userdata('id_branch'); ?>" >
						<?php } ?>
					 </div>
			 <?php } ?>	
			   <div class="col-sm-4">
				   <div class="form-group"> <label>Today's Rate</label>
				   <div class="input-group ">
					   <span class="input-group-addon input-sm"><?php echo $this->session->userdata('currency_symbol') ?></span>
					   <input type="text" class="form-control input-sm" id="metal_rate" name="generic[metal_rate]" readonly="true" />
			  <input type="hidden" class="form-control"  name="generic[payment_type]" value="Manual"  /> 	
				   </div>
				   </div>
			   </div>
			   <div class="col-sm-4 ">
				   <div class="form-group">
					   <label for="">Payment Date</label>
						   <div  id="date_payment_block">
						 </div>
				   </div>	
			   </div>	
		   </div> -->
						<div class="row">
							<?php if ($this->session->userdata('branch_settings') == 1) { ?>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Select Branch<span class="error">*</span></label>
										<select id="branch_select" class="form-control" style="width:100%;" required>
										</select>
										<input id="id_branch" name="generic[id_branch]" type="hidden" value="<?php if ($this->session->userdata('id_branch') > 0) {
																													echo $this->session->userdata('id_branch');
																												} ?>" />
										<input type="hidden" id="branch_settings" value="<?php echo $this->session->userdata('branch_settings'); ?>">
									</div>
								</div>
							<?php } else { ?>
								<input type="hidden" id="id_branch" name="generic[id_branch]" value="<?php echo $this->session->userdata('id_branch'); ?>">
							<?php } ?>
							<div class="col-md-3">
								<div class="form-group">
									<label>Select employee<span class="error">*</span></label>
									<select id="employee_select" class="form-control" style="width:100%;" required>
									</select>
									<input id="id_employee" name="generic[id_employee]" type="hidden" />
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="">Payment Date</label>
									<div id="date_payment_block">
									</div>
									<input type="hidden" class="form-control" id="metal_rate" name="generic[metal_rate]" value="" />
								</div>
							</div>
							<div class="col-sm-3 current_date_rate">
								<div class="form-group"> <label>Today's Rate</label>
									<div class="input-group ">
										<span class="input-group-addon input-sm"><?php echo $this->session->userdata('currency_symbol') ?></span>
										<input type="text" class="form-control input-sm" id="current_metal_rate" name="generic[current_metal_rate]" readonly="true" />
										<input type="hidden" class="form-control" name="generic[payment_type]" value="Manual" />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3 editing_date_rate">
								<div class="form-group">
									<label for="">Metal Rate's Date<span class="error">*</span></label>
									<div id="metalrate_date_payment">
									</div>
								</div>
							</div>
							<div class="col-sm-3 editing_date_rate">
								<div class="form-group">
									<label>Metal Rate<span class="error">*</span></label>
									<select id="select_metal_rate" class="form-control" name="generic[select_metal_rate]" style="width:100%;"></select>
									<input type="hidden" id="metalrate_edit_date" name="generic[metalrate_edit_date]" value="">
									<input type="hidden" id="rate_edit_enable" name="generic[rate_edit_enable]" value="">
								</div>
							</div>
						</div>
						<!-- 11-01-2023 #AB branch auto store based on emp log -->
						<div class="row">
							<div class="col-xs-12 col-md-12 col-lg-12">
								<div id="scheme-detail-box" class="box box-solid box-default">
									<div class="box-header with-border">
										<h3 class="box-title">Scheme A/c Details</h3>
									</div>
									<div class="box-body">
										<div class="col-xs-12 col-md-4 col-lg-4 pull-left">
											<!-- DGS-DCNM -->
											<input type="hidden" id="daily_pay_limit" name="generic[daily_pay_limit]" />
											<input type="hidden" id="tot_amt_paid" name="generic[tot_amt_paid]" />
											<!-- DGS-DCNM -->
											<input type="hidden" id="reference_no" name="generic[reference_no]" value="" />
											<input type="hidden" id="sync_scheme_code" name="generic[sync_scheme_code]" value="" />
											<input type="hidden" id="nominee_name" name="generic[nominee_name]" value="" />
											<input type="hidden" id="nominee_relationship" name="generic[nominee_relationship]" value="" />
											<input type="hidden" id="nominee_address1" name="generic[nominee_address1]" value="" />
											<input type="hidden" id="nominee_address2" name="generic[nominee_address2]" value="" />
											<input type="hidden" id="nominee_mobile" name="generic[nominee_mobile]" value="" />
											<input type="hidden" id="emp_name" name="generic[emp_name]" value="" />
											<input type="hidden" id="FORM_SECRET" name="generic[form_secret]" value="<?php echo $form_secret; ?>">
											<input type="hidden" id="overallAmountSum" value="">
											<input type="hidden" id="schemeAmountSum" value="">

											<!--       <input type="hidden" id="referal_code" name="generic[referal_code]" value="" /> -->
											<input type="hidden" id="customer_mobile" value="">
											<table class="table table-condensed">
												<tr>
													<th>Joined on</th>
													<td><span id="start_date"></span></td>
												</tr>
												<tr>
													<th>A/c Name</th>
													<td><span id="acc_name"></span></td>
												</tr>
												<tr>
													<th>Scheme Code</th>
													<td><span id="scheme_code"></span></td>
												</tr>
												<tr>
													<th>Type</th>
													<td><span id="scheme_type"></span></td>
												</tr>
												<tr>
													<th>Amount to weight Conversion</th>
													<td>
														<input type="hidden" id="fix_weight" name="generic[fix_weight]" value="<?php echo set_value('generic[fix_weight]'); ?>" />
														<!-- esakki 19-09 -->
														<input type="hidden" id="sch_type" name="generic[sch_type]" />
														<input type="hidden" id="is_flexible_wgt" />
														<input type="hidden" name="generic[is_otp_scheme]" id="is_otp_scheme" />
														<input type="hidden" name="generic[wgt_store_as]" id="wgt_store_as" />
														<!-- weight conbvert daily or closing-->
														<input type="hidden" id="wgt_cvrt" name="generic[wgt_convert]" value="<?php echo set_value('generic[wgt_convert]'); ?>" />
														<input type="hidden" id="flexible_sch_type" name="generic[flexible_sch_type]" />
														<!-- weight conbvert daily or closing-->
														<span id="amt_to_wgt"></span>
													</td>
												</tr>
												<!-- <tr >
									<th >Assumed Weight</th>
									<td>
									<span id="amttowgt"></span></td>
								</tr> -->
											</table>
										</div>
										<div class="col-xs-12 col-md-4 col-lg-4">
											<table class="table table-condensed">
												<tr class="topup_sch_hide">
													<th>Payable</th>
													<td>
														<input type="hidden" id="sch_amt" name="generic[sch_amt]" value="<?php echo set_value('generic[sch_amt]'); ?>" />
														<span id="payable"></span>
													</td>
												</tr>
												<tr>
													<th>Paid Installments</th>
													<td>
														<span id="paid_installments"></span>
														<input type="hidden" id="paid_ins" name="generic[total_paid]" />
														<input type="hidden" id="paidinstall" name="generic[paid_installments]" />
														<input type="hidden" id="ref_benifit_ins" name="generic[ref_benifitadd_ins]" />
														<input type="hidden" id="referal_code" name="generic[referal_code]" />
														<input type="hidden" id="discount" name="generic[discount]" />
														<input type="hidden" id="discount_installment" name="generic[discount_installment]" />
														<input type="hidden" id="discount_type" name="generic[discount_type]" />
														<input type="hidden" id="firstPayDisc_value" name="generic[firstPayDisc_value]" />
														<input type="hidden" id="discountedAmt" name="generic[discountedAmt]" />
														<input type="hidden" id="ref_benifitadd_by" name="generic[ref_benifitadd_ins_type]" />
														<input type="hidden" id="max_dues" name="generic[max_dues]" />
														<input type="hidden" id="pay" name="generic[payable]" />
														<input type="hidden" id="min_pan_amt" value="<?php echo $pay['min_pan_amt']; ?>" />
														<input id="validate_max_cash" type="hidden" value="<?php echo $pay['validate_cash_amt'] ?>" />
														<input id="max_cash_amt" type="hidden" value="<?php echo $pay['max_cash_amt'] ?>" />
														<input id="chit_cash_paid" type="hidden" value="0" />
														<input id="chit_total_cash_paid" type="hidden" value="0" />
														<input id="adv_total_cash_amt" type="hidden" value="0" />
														<input type="hidden" id="agent_code" name="generic[agent_code]" />
														<input type="hidden" id="id_agent" name="generic[id_agent]" />
														<input type="hidden" name="generic[id_scheme]" id="id_scheme" />
														<input type="hidden" id="agent_refferal" name="generic[agent_refferal]" />
														<input type="hidden" id="emp_refferal" name="generic[emp_refferal]" />
														<input type="hidden" id="cus_refferal" name="generic[cus_refferal]" /> <!-- 22-10  -->
														<input type="hidden" id="firstPayamt_as_payamt" name="generic[firstPayamt_as_payamt]" />
														<input type="hidden" id="firstPayamt_maxpayable" name="generic[firstPayamt_maxpayable]" />
														<input type="hidden" id="firstPayment_as_wgt" name="generic[firstPayment_as_wgt]" />
														<input type="hidden" id="cash_paymts" name="generic[cash_paymts]" />
														<input type="hidden" id="disable_pay_amt" name="generic[disable_pay_amt]" />
													</td>
												</tr>
												<tr>
													<th>Amount Paid</th>
													<td><span id="total_amount_paid"></span></td>
												</tr>
												<tr class="is_weight">
													<th>Weight Paid</th>
													<td><span id="total_weight_paid"></span></td>
												</tr>
												<tr>
													<th>Last Paid Date</th>
													<td><span id="last_paid_date"></span></td>
												</tr>
												<!--	<tr style="display:none;">
									<th>Preclose</th>
									<td><input type="checkbox" id="is_preclose" />  Need to close</td>
								</tr>-->
											</table>
										</div>
										<div class="col-xs-12 col-md-4 col-lg-4">
											<table class="table table-condensed">
												<!--<tr>
													<th>PDC/ECS </th>
													<td><span id="total_pdc"></span></td>
												</tr> -->
												<tr>
													<th style="width:50%;">Can pay?</th>
													<td style="width:50%;"><span id="allow_pay"></span></td>
												</tr>
												<tr class="topup_sch_hide">
													<th style="width:50%;">Unpaid Dues</th>
													<td style="width:50%;"><span id="unpaid_dues"></span></td>
												</tr>
												<tr class="topup_sch_hide" id="is_preclose_blk" style="display<?php echo ($pay['allow_preclose'] == 1 ? ':block' : ':none') ?>">
													<th style="width:50%;"> Is Preclose </th>
													<td style="width:50%;"><input type="checkbox" name="generic[totalunpaid]" id="is_preclose" value='1' /></td>
													<input type="hidden" class="form-control" id="preclose" />
												</tr>
												<tr class="hidden_allow" style="display:none">
													<th style="width:50%;">Allowed Dues</th>
													<!-- <td><input type='hidden' class='form-control input_number' id="allowed_dues" name='generic[installments]' style="width:50%;float:right;" readonly="true" />
														<input type='hidden' id="payamt" />
														<input type='hidden' id="act_allowed_dues" />
														<input type='hidden' id="act_due_type" />
													</td> -->
													<td style="width:50%;">
														<input type='hidden' class='form-control input_number' id="allowed_dues" name='generic[installments]' style="width:50;" readonly="true" />
														<input type='hidden' id="payamt" />
														<input type='hidden' id="act_allowed_dues" />
														<input type='hidden' id="act_due_type" />
														<?php $i = 0; ?>
														<button type="button" value="<?php echo $i; ?>" class="dec_due">-</button>
														<input id="sel_due" class="sel_due" name="generic[installments]" Style="width:10%" value="1" readonly="true" />
														<button type="button" value="<?php echo $i; ?>" class="incre_due">+</button>
													</td>
												</tr>
												<tr class="hidden_allow" style="display:none">
													<th style="width:50%;">Due Type</th>
													<td style="width:50%;"><input type='text' class='form-control' name='generic[due_type]' id="due_type" style="width:50%;" readonly="true" /></td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row toup_sch" style="display:none;">
							<div class="col-xs-12 col-md-12 col-lg-12">
								<div id="topup_scheme-detail-box" class="box box-solid" style="border-top: 3px solid #06f698;">
									<div class="box-header with-border" style="background-color: #06f698;">
										<h3 class="box-title">Advance Booking Details</h3>
									</div>
									<div class="box-body">
										<div class="col-xs-12 col-md-4 col-lg-4 pull-left">
											<table class="table table-condensed">
												<tr>
													<th>Booking Amount</th>
													<td><span id="topup_amount"></span></td>
												</tr>
												<tr>
													<th>Booking Weight</th>
													<td><span id="topup_weight"></span></td>
												</tr>
												<tr>
													<th>Booking Metal Rate</th>
													<td><span id="topup_rate"></span></td>
												</tr>
												<tr>
													<th>Applicable Top-up slab value</th>
													<td><select id="topup_slab_value" style="display:none;"></select></td>
												</tr>
											</table>
										</div>
										<div class="col-xs-12 col-md-4 col-lg-4 pull-left">
											<table class="table table-condensed">
												<tr>
													<th>Total Paid Amount</th>
													<td><span id="topup_amount_paid"></span></td>
												</tr>
												<tr>
													<th>Outstanding Amount</th>
													<td><span id="topup_balance_pay"></span></td>
												</tr>
												<tr>
													<th>Est. Total slab Amount</th>
													<td><b style="color: #006;" id="topup_estimated_paid_amount"></b></td>
												</tr>
												<tr>
													<th>Due Date</th>
													<td><b style="color: #006;" id="due_date_to"></b></td>
												</tr>
											</table>
										</div>
										<div class="col-xs-12 col-md-4 col-lg-4 pull-left">
											<table class="table table-condensed">
												<tr>
													<th>Minimum Payable</th>
													<td><b style="color: #006;" id="topup_min_payable"></b></td>
												</tr>
												<tr>
													<th>Maximum Payable</th>
													<td><b style="color: #006;" id="topup_max_payable"></b></td>
												</tr>
												<tr>
													<th>Top-up slab (In Days)</th>
													<td><b style="color: #006;" id="topup_slab_endday"></b></td>
												</tr>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--<div class="row">
	   <div class="col-md-3">
		  <div class="form-group col-md-10 col-md-offset-1">
			<label for="chargeseme_name" >  <input type="checkbox"   id="adjust_unpaid" disabled="true" value="1"/> Adjust Unpaid</label>
		 </div>
	   </div>  
	   <div class="col-md-3">
		  <div class="form-group">
			<label for="chargeseme_name" class="col-md-6 col-md-offset-1 ">Unpaid Dues</label>
			<div class="col-md-4">
			<input type="text" class="form-control input_number"  id="no_of_unpaids" disabled="true" value=""/>
			 <p class="help-block"></p>
			</div>
		 </div>
	   </div>
	 </div>-->
					<div class="row">
						<div class="col-sm-7 col-sm-offset-3">
							<div id="error-msg"></div>
							<div id="payment_container">
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-12" id="old_metal" style="display:none;">
								<div class="row">
									<div class="col-sm-2">
										<div class="form-group">
											<label for="">Est No.</label>
											<div class="input-group">
												<input type="hidden" id="chit_deposit" name="generic[chit_deposit]" value="1">
												<input type="hidden" id="otp_price_fixing">
												<input class="form-control" id="filter_est_no" name="filter_est_no" type="text" placeholder="Esti No." value="" />
												<input type="hidden" id="estimation_id" name="generic[estimation_id]">
												<span class="input-group-btn">
													<button type="button" id="search_est_no" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>
												</span>
											</div>
										</div>
										<button type="button" id="cash_deposit" class="btn btn-success  btn-flat"><i class="fa fa-plus"></i><label>&nbsp;Add Payment</label></button>
									</div>
									<div class="col-md-6" style="margin-top: -10px;">
										<div class="box-body">
											<div class="table-responsive">
												<table id="est_list" class="table table-bordered table-striped text-center">
													<thead>
														<tr>
															<th>Est No</th>
															<th>Amount</th>
															<th>Weight</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody></tbody>
													<tfoot>
														<tr>
															<th class="">Total</th>
															<th class="tot_est_amt">0</th>
															<th class="tot_est_weight">0</th>
															<th></th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<?php if ($this->payment_model->get_gstsettings() == 1) { ?>
								<div class="col-sm-2">
									<div class="form-group">
										<label for="">GST Amount</label>
										<div class="input-group input-group-sm">
											<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol') ?></span>
											<input type="text" class="form-control" id="gst_amt" name="generic[gst_amount]" readonly="true" />
											<input type="hidden" class="form-control" id="gst_type" name="generic[gst_type]" />
											<input type="hidden" class="form-control" id="gst_percent" name="generic[gst]" />
										</div>
									</div>
								</div>
							<?php } ?>
							<div class="col-sm-3">
								<div class="form-group">
									<label for="">Received Amount<span class="error">*</span></label>
									<div class="input-group input-group-sm">
										<span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol') ?></span>
										<input type="number" class="form-control received_amt" id="total_amt" name="generic[payment_amount]" onkeypress="return /[0-9]/i.test(event.key)" required />
										<input type="hidden" class="form-control" id="total_est_amt" name="generic[est_amt]" />
										<input type="hidden" class="form-control" id="total_est_wgt" name="generic[est_wgt]" />
										<input type="hidden" id="allow_cash_limit" name="generic[allow_cash_limit]" />
										<input type="hidden" id="disable_acc_payments" name="generic[disable_acc_payments]" />
										<input type="hidden" id="chit_number" name="generic[chit_number]" />
									</div>
								</div>
							</div>
							<div class="col-sm-1" style=" margin-top: 25px;">
								<div class="form-group">
									<div class="input-group input-group-sm">
										<input type="button" class="form-control btn btn-primary" id="proced" value="Proceed" />
									</div>
								</div>
							</div>
							<!--	<div class="col-sm-3">
			  <div class="form-group">
			  <label for="">Payment Amount</label>
				   <div class="input-group input-group-sm">
					   <span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol') ?></span>
					   <input type="text" class="form-control"   id="payment_amt" readonly="true" />
				   </div>
			   </div>
			   </div> -->
							<!--flx scheme-->
							<div class="col-sm-3" id="payment_weight_block">
								<div class="form-group">
									<label for="">Payment Weight</label>
									<div class="input-group input-group-sm">
										<input type="text" class="form-control" id="payment_weight" name="generic[metal_weight]" readonly="true" />
										<span class="input-group-addon">Gram</span>
									</div>
								</div>
							</div>
							<div class="col-sm-4 digi_benefit_block box box-solid box-default box-success" Style="display:none;width: 30%;">
								<table class="table">
									<tbody>
										<tr>
											<th colspan=2><span id="digi_benefit_content"></span></th>
										</tr>
										<tr>
											<td>Benefit Amount</td>
											<td><span id="digi_benefit_amt" Style="font-size: 18px;"></span></td>
										</tr>
										<tr>
											<td>Benefit Weight</td>
											<td><span id="digi_benefit_wgt" Style="font-size: 18px;"></span></td>
										</tr>
											<tr class="other_benefit_block">
												<th colspan=2><span id="digi_other_benefit_content"></span></th>
											</tr>
											<tr class="other_benefit_block">
												<td>Other Benefit Amount</td>
												<td><span id="digi_other_benefit_amt" Style="font-size: 18px;"></span></td>
											</tr>
											<tr class="other_benefit_block">
												<td>Other Benefit Weight</td>
												<td><span id="digi_other_benefit_wgt" Style="font-size: 18px;"></span></td>
											</tr>

									</tbody>
								</table>
							</div>
						</div>
						<!--  <div class="row">	
				   <div class="col-sm-3">
					   <div class="form-group">
						   <label for="">Payment Mode</label>
						   <select class="form-control"   id="pay_mode" name="generic[payment_mode]" ></select>
													  <input type="hidden" class="form-control" id="paymode"  value="<?php echo set_value('generic[payment_mode]', $pay['payment_mode']); ?>" />
					   </div>
				   </div> 	
				   <div class="col-sm-3">
					   <div class="form-group">
						   <label for="">Ref No</label>
						   <input type="text" class="form-control input_number input-sm" id="payment_ref_number" name="generic[payment_ref_number]" value="<?php echo set_value('generic[payment_ref_number]', $pay['payment_ref_number']); ?>" />
					   </div>
				   </div>
				   <div class="col-sm-3">
					   <div class="form-group">	              
						   <label for="">Payment Status</label> <br/>
						 <select class="form-control" name="generic[payment_status]" id="payment_status"></select>
							 <input type="hidden" class="form-control" id="pay_status"  value="<?php echo set_value('generic[payment_status]', $pay['payment_status']); ?>" />
					  </div>	
				 </div>
	 </div></br> -->
					</div>
					<!--flx scheme-->
					<!-- <div class="row cheque-container" style="display: none;">
	  <div class="col-sm-2">
		   <div class="form-group">
			   <label for="">Cheque Date</label>
				   <div class='input-group date'>
						 <input type='text' class="form-control input-sm datemask myDatePicker" name="pdc[date_payment]"  id='pay_date' data-inputmask="'alias': 'dd/mm/yyyy'" data-mask  data-date-format="dd/mm/yyyy" value="<?php echo set_value('pdc[date_payment', $pay['pdc']['date_payment']); ?>" />
						 <span class="input-group-addon">
							 <span class="glyphicon glyphicon-calendar"></span>
						 </span>
					 </div>
		   </div>
	   </div> 
	   <div class="col-sm-3">
		   <div class="form-group">
			   <label for="">Cheque No</label>
			   <input type="text" class="form-control input_number input-sm" id="cheque_no" name="pdc[cheque_no]" value="<?php echo set_value('pdc[cheque_no]', $pay['pdc']['cheque_no']); ?>" />
		   </div>
	   </div>
	   <div class="col-sm-2">
		   <div class="form-group">
			   <label for="">Bank</label>
			   <select class="form-control" id="payee_bank" name="pdc[payee_bank]" style="width: 100%;"></select>
					   <input type="hidden" id="id_payee_bank" value="<?php echo set_value('pdc[payee_bank]', $pay['pdc']['payee_bank']); ?>" />
		   </div>
	   </div>
	   <div class="col-sm-3">
		   <div class="form-group">
			   <label for="">Branch</label>
			   <input type="text" class="form-control input_text input-sm" id="payee_branch" name="pdc[payee_branch]" value="<?php echo set_value('pdc[payee_branch]', $pay['pdc']['payee_branch']); ?>" />
		   </div>
	   </div>  
	 <div class="col-sm-2">
		   <div class="form-group">
			   <label for="">IFSC Code</label>
			   <input type="text" class="form-control" id="bank_IFSC" name="pdc[payee_ifsc]" value="<?php echo set_value('pdc[payee_ifsc]', $pay['pdc']['payee_ifsc']); ?>" />
		   </div>
	   </div></br>
	   <div class="col-sm-3">
		   <div class="form-group">
			   <label for="">Drawee A/c</label>
				   <select class="form-control" id="drawee_acc_no" name="pdc[id_drawee]" style="width: 100%;"></select>
		   </div>
	   </div>
		   <div class="col-md-3">			
			  <div class="form-group">
				<label for="">Drawee Bank</label>
				<input type="text" class="form-control input_number"  id="drawee_bank" readonly="true" />
			 </div>	
		 </div>	
		<div class="col-md-3">			
			  <div class="form-group">
				<label for="">Drawee Branch</label>
				<input type="text" class="form-control input_text"  id="drawee_bank_branch" readonly="true"  />
			 </div>	
		 </div>
		 <div class="col-md-3">			
			  <div class="form-group">
				<label for="">Drawee IFSC Code</label>
				<input type="text" class="form-control ucase"  id="drawee_ifsc" readonly="true"  />
			 </div>	
		 </div>	
  </div></br>-->
					<div class="row">
						<div class="col-md-6">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="">Ref No</label>
										<input type="text" class="form-control input_number input-sm" id="payment_ref_number" name="generic[payment_ref_number]" value="<?php echo set_value('generic[payment_ref_number]', $pay['payment_ref_number']); ?>" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="">Payment Status</label> <br />
										<input type="text" class="form-control" name="generic[paymt_sts]" id="payment_status" value="Success" readonly>
										<!-- <select class="form-control" name="generic[payment_status]" id="payment_status"></select> -->
										<input type="hidden" class="form-control" name="generic[payment_status]" id="pay_status" value="<?php echo set_value('generic[paymt_sts]', $pay['payment_status']); ?>" />
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<label>Remarks</label>
										<textarea class="form-control" name="generic[remark]" rows="6"><?php echo set_value('generic[remark]', $pay['remark']); ?></textarea>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="box box-info payment_blk">
								<div class="box-header with-border">
									<h3 class="box-title">Make Payment</h3>
									<div class="box-tools pull-right">
										<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
									</div>
								</div>
								<div class="box-body">
									<div class="row">
										<div class="col-sm-11">
											<div class="box-body">
												<div class="table-responsive">
													<table id="payment_modes" class="table table-bordered table-striped" style="pointer-events: none;opacity: 0.4;">
														<thead>
														</thead>
														<tbody>
															<tr>
																<td class="text-right"><b class="custom-label">Payment Amount</b><span class="error">*</span></td>
																<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>
																<td>
																	<input type="text" class="form-control" id="payment_amt" readonly="true" />
																</td>
															</tr>
															<!--<tr>
												 <td class="text-right"><b class="custom-label">Received</b></td>
												 <th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>
												 <td> 
													 <input type="number" class="form-control receive_amount" name="generic[tot_amt_received]" value="<?php echo set_value('generic[tot_amt_received]', isset($generic['tot_amt_received']) ? $generic['pan_no'] : 0); ?>" >
												 </td>
											 </tr>-->
															<?php
															$modes = $this->payment_model->get_payModes();
															if (sizeof($modes) > 0) {
																foreach ($modes as $mode) {
																	//$cash = ($mode['short_code'] == "CSH" ? '<input class="form-control" id="make_pay_cash" name="cus_pay_mode[cash_payment]" type="text" placeholder="Enter Amount" value=""/>' : '');
																	$cash = ($mode['short_code'] == "CSH" ? '<div id="cash_div"><input class="form-control" id="make_pay_cash" name="cus_pay_mode[cash_payment]" type="number" placeholder="Enter Amount" value=""/></div>' : '');
																	//$card = ($mode['short_code'] == "CC"? '<a class="btn bg-olive btn-xs pull-right" id="card_detail_modal" href="#" data-toggle="modal" data-target="#card-detail-modal" ><b>+</b></a> ' : '');
																	// $card = ($mode['short_code'] == "CC" || $mode['short_code'] == "DC" ? '<a class="btn bg-olive btn-xs pull-right" id="card_detail_modal" href="#" data-toggle="modal" data-target="#card-detail-modal" ><b>+</b></a> ' : '');
																	$card = ($mode['short_code'] == "CC" ? '<div id="card_div"><a class="btn bg-olive btn-xs pull-right" id="card_detail_modal" href="#" data-toggle="modal" data-target="#card-detail-modal" ><b>+</b></a></div> ' : '');
																	//$cheque = ($mode['short_code'] == "CHQ"  ? '<a class="btn bg-olive btn-xs pull-right" id="cheque_modal" href="#" data-toggle="modal" data-target="#cheque-detail-modal" ><b>+</b></a> ' : '');
																	$cheque = ($mode['short_code'] == "CHQ"  ? '<div id="cheque_div"><a class="btn bg-olive btn-xs pull-right" id="cheque_modal" href="#" data-toggle="modal" data-target="#cheque-detail-modal" ><b>+</b></a></div> ' : '');
																	//$net_banking = ($mode['short_code'] == "NB"  ? '<a id="netbankmodal" class="btn bg-olive btn-xs pull-right"  href="#" data-toggle="modal" data-target="#net_banking_modal" ><b>+</b></a> ' : '');
																	$net_banking = ($mode['short_code'] == "NB"  ? '<div id="nb_div"><a id="netbankmodal" class="btn bg-olive btn-xs pull-right"  href="#" data-toggle="modal" data-target="#net_banking_modal" ><b>+</b></a></div> ' : '');
																	//voucher starts here
																	$voucher = ($mode['short_code'] == "VCH"  ? '<div id="VCH_div"><a class="btn bg-olive btn-xs pull-right" id="vch_modal" href="#" data-toggle="modal" data-target="#vch-detail-modal" ><b>+</b></a></div> ' : '');
																	//voucher ends here
															?>
																	<tr>
																		<td class="text-right"><?php echo $mode['mode_name']; ?>
																		</td>
																		<td class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></td>
																		<td class="mode_<?php echo $mode['short_code']; ?>">
																			<span class="<?php echo $mode['short_code']; ?>"></span>
																			<input type="hidden" id="card_payment" name="cus_pay_mode[card_pay]" value="">
																			<input type="hidden" id="chq_payment" name="cus_pay_mode[chq_pay]" value="">
																			<input type="hidden" id="nb_payment" name="cus_pay_mode[net_bank_pay]" value="">
																			<!--//voucher starts here-->
																			<input type="hidden" id="vch_payment" name="cus_pay_mode[vch_pay]" value="">
																			<!--//voucher ends here-->
																			<?php echo $cash; ?>
																			<?php echo $card; ?>
																			<?php echo $cheque; ?>
																			<?php echo $net_banking; ?>
																			<!--//voucher starts here-->
																			<?php echo $voucher; ?>
																			<!--//voucher ends here-->
																		</td>
																	</tr>
															<?php }
															} ?>
															<tr>
																<td class="text-right">Advance Adj</td>
																<td class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></td>
																<td class="mode_ADV"> <!-- esakki 11-11 -->
																	<span id="tot_adv_adj"></span>
																	<!--<a class="btn bg-olive btn-xs pull-right" id="adv_adj_modal" onclick="get_advance_details()" href="#" data-toggle="modal"><b>+</b></a> -->
																	<div id="adv_adj_div">
																		<a class="btn bg-olive btn-xs pull-right" id="adv_adj_modal" onclick="get_advance_details()" href="#" data-toggle="modal"><b>+</b></a>
																	</div>
																	<input type="hidden" id="adv_adj_details" name="cus_pay_mode[adv_adj]" value="">
																	<input type="hidden" id="ord_adv_adj_details" name="cus_pay_mode[order_adv_adj]" value="">
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
																<th class="text-right custom-label">Balance</th>
																<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>
																<th class="bal_amount"></th>
															</tr>
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
					<div class="row">
						<div class="col-md-12">
							<div class="box box-default"><br />
								<?php if ($pay['useWalletForChit'] == 1 && $pay['allow_wallet'] == 1) { ?>
									<input name="generic[wallet]" type="hidden" class="wallet">
									<input type="hidden" name="generic[wallet_balance]" class="wallet_balance" />
									<input type="hidden" name="generic[redeem_percent]" class="redeem_percent" />
									<div class="col-md-12" style="margin-top: 40px;">
										<div class="col-md-6">
											<div class="eligible_walletamt" style="display:none">
												<input type="checkbox" name="generic[is_use_wallet]" class="ischk_wallet_pay" value="1" />
												Use Wallet <input name="generic[redeem_request]" step="1" type="number" class="redeem_request">
												<div style="color:red;font-size:15px;">
													<span id="wallet_msg"></span>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>
								<div class="col-sm-offset-4">
									<!--<input type="checkbox" name="generic[is_use_wallet]"  value='1' /> Use Wallet &nbsp; &nbsp; &nbsp; &nbsp;<br/>-->
									<!--<button type="submit" class="btn btn-primary" id="btn-payment" >Save</button> -->
									<input type="hidden" id="clear_form" name="type" value="">
									<div class="btn-group" id="btn-submit" data-toggle="buttons" style="pointer-events: all;opacity: 0.9;">
										<label class="btn btn-primary">
											<input type="checkbox" id="pay_print" name="type" value="1">Save and Print
										</label>
										<label class="btn btn-primary">
											<input type="checkbox" id="pay_save" name="type" value="2"> Save
											<input type="hidden" id="isOTPRegForPayment" name="isOTPRegForPayment" value="<?php echo $pay['isOTPRegForPayment'] ?>">
										</label>
									</div>
									<button type="button" class="btn btn-default btn-cancel">Cancel</button>
								</div> <br />
							</div>
						</div>
					</div>
				</form>
			</div><!-- /.box-body -->
			<div class="overlay" style="display:none">
				<i class="fa fa-refresh fa-spin"></i>
			</div>
			<div class="box-footer">
			</div><!-- /.box-footer-->
			<div class="overlay" style="display:none">
				<i class="fa fa-refresh fa-spin"></i>
			</div>
		</div><!-- /.box -->
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
<div id="otp_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header ">
				<button type="button" id="close_model" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 id="myModalLabel">Mobile Number Verification</h3>
			</div>
			<div class="modal-body">
				<p>Please enter the code sent to your mobile number</p>
				<div>
					<label style="display:inline; margin:5px" for="otp">Enter Code:</label>
					<input style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required />
					<input style="margin-left:1%" type="submit" value="Verify" id="verify_otp" style="background-color:#0079C0" class="button btn btn-primary" />
					<span id="OTPloader"><img src="<?php echo base_url() ?>assets/img/loader.gif"></span>
					<br />
					<p id="timer" style="display:none;">90</p>
				</div>
				<div class="modal-footer">
					<input type="submit" id="resendotp" value="Resend OTP" class="resendotp" disabled="true"> </input>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Card Details -->
<div class="modal fade" id="card-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Card Details</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<!-- esakki -->
							<span class="h_amt" style="color:red;">Balance Amount:</span>
							<!-- <span class="cc_balance_amount f_amt" style="color:red;">0</span> -->
							<span class="f_amt" style="color:red;"><?php echo $this->session->userdata('currency_symbol') ?> </span><span class="modal_balance_amount f_amt" style="color:red;">0</span>
							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_card"><i class="fa fa-user-plus"></i>ADD</button>
							<p class="error "><span id="cardPayAlert"></span></p>
						</div>
					</div>
					<p></p>
					<div class="table-responsive">
						<table id="card_details" class="table table-bordered">
							<thead>
								<tr>
									<th>Card Name</th>
									<th>Type</th>
									<th>Device</th>
									<th>Card No</th>
									<th>Amount</th>
									<th>Approval No</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<!--	<?php if ($this->uri->segment(3) == 'edit') {
											/*foreach($est_other_item['card_details'] as $ikey => $ival){
							 echo '<tr><td><input class="card_name" type="number" name="card_details[card_name][]" value="'.$ival['card_name'].'" /></td><td><input class="card_type" type="number" name="card_details[card_type][]" value="'.$ival['card_type'].'" /></td><td><input type="number" class="card_no" style="width: 100px;"  name="card_details[card_no][]" value="'.$ival['card_no'].'"  /></td><td><input type="number" class="card_amt" style="width: 100px;"  name="card_details[card_amt][]" value="'.$ival['card_amt'].'"  /></td><td>-</td></tr>';
					 }*/
										} else { ?>
				 <tr> 
					 <td><select name="card_details[card_name][]" class="card_name"><option value="1">RuPay</option><option value="2">VISA</option><option value="3">Mastro</option><option value="4">Master</option></select></td>
					 <td><select name="card_details[card_type][]" class="card_type"><option value="1">CC</option><option value="2">DC</option></select></td>
					 <td><select class="form-control id_device" name="card_details[id_device][]" class="id_device" style="width: 100px !important;">
						 <?php
											$devices = $this->payment_model->get_payment_device_details();
											foreach ($devices as $device) {
												echo '<option value="' . $device['id_device'] . '">' . $device['device_name'] . '</option>';
											}
							?>
						 </select>
					 </td>
					 <td><input type="number" step="any" class="card_no" name="card_details[card_no][]"/></td> 
					 <td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]"/></td> 
					 <td><input type="text" step="any" class="ref_no" name="card_details[ref_no][]"/></td> 
					 <td><a href="#" onclick="removeCC_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  
				 </tr> 
				 <?php } ?>-->
							</tbody>
							<tfoot>
								<tr>
									<th colspan=3>Total</th>
									<th colspan=2>
										<span class="cc_total_amount"></span>
										<span class="cc_total_amt" style="display: none;"></span>
										<span class="dc_total_amt" style="display: none;"></span>
									</th>
								</tr>
								<!-- esakki -->
								<!-- <tr>
					 <th  colspan=3>Balance Amount</th>
					 <th colspan=2>
						 <span class="cc_balance_amount"></span>
					 </th>
				 </tr> -->
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" id="add_newcc" class="btn btn-success">Save</a>
				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="cheque-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Cheque Details</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<!-- esakki -->
							<span class="h_amt" style="color:red;">Balance Amount:</span>
							<!-- <span class="chq_balance_amount f_amt" style="color:red;">0</span> -->
							<span class="f_amt" style="color:red;"><?php echo $this->session->userdata('currency_symbol') ?> </span><span class="modal_balance_amount f_amt" style="color:red;">0</span>
							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_chq"><i class="fa fa-user-plus"></i>ADD</button>
							<p class="error "><span id="chqPayAlert"></span></p>
						</div>
					</div>
					<p></p>
					<div class="table-responsive">
						<table id="chq_details" class="table table-bordered">
							<thead>
								<tr>
									<th>Cheque Date</th>
									<th>Bank</th>
									<th>Branch</th>
									<th>Cheque No</th>
									<th>IFSC Code</th>
									<th>Amount</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<!-- <td><input class="form-control datemask date nb_date" data-date-format="dd-mm-yyyy" name="cheque_details[cheque_date][]" type="text" placeholder="Cheque Date" style="width: 100px;" /></td> -->
								<!-- esakki -->
								<!-- <tr> 
					 <td><input id="cheque_datetime" data-date-format="dd-mm-yyyy" class="cheque_date" name="cheque_details[cheque_date][]" type="text" required="true" placeholder="Cheque Date" /></td>
					 <td><input name="cheque_details[bank_name][]" type="text" required="true" class="bank_name" onkeypress="return /[a-zA-Z]/i.test(event.key)"></td>
					 <td><input name="cheque_details[bank_branch][]" type="text" required="true" class="bank_branch" onkeypress="return /[a-zA-Z]/i.test(event.key)" ></td>
					 <td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]"/></td> 
					 <td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"  /></td> 
					 <td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]"/></td> 
					 <td><a href="#" onclick="removeChq_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  
				 </tr>  -->
							</tbody>
							<tfoot>
								<tr>
									<td>Total</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td><span class="chq_total_amount"></span></td>
									<td></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" id="add_newchq" class="btn btn-success">Save</a>
				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- cheque-->
<!-- Net Banking-->
<div class="modal fade" id="net_banking_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Net Banking Details</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<!-- esakki -->
							<span class="h_amt" style="color:red;">Balance Amount:</span>
							<!-- <span class="nb_balance_amount f_amt" style="color:red;">0</span> -->
							<span class="f_amt" style="color:red;"><?php echo $this->session->userdata('currency_symbol') ?> </span><span class="modal_balance_amount f_amt" style="color:red;">0</span>
							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_net_bank"><i class="fa fa-user-plus"></i>ADD</button>
							<p class="error "><span id="NetBankAlert"></span></p>
						</div>
					</div>
					<p></p>
					<div class="table-responsive">
						<table id="net_bank_details" class="table table-bordered" style="width:100%;border-collapse: collapse;">
							<thead>
								<tr>
									<!-- <th>Type</th> 
					 <th>Bank/Device</th> -->
									<!--<th class="upi_type">Bank</th>-->
									<!--	<th class="device" style="display:none;">Device</th>-->
									<!-- <th >Payment Date</th>
					 <th>Ref No</th>  
					 <th>Amount</th>  
					 <th>Action</th>  -->
									<th width="15%">Type</th>
									<th width="20%">Bank/Device</th>
									<th width="20%">Payment Date</th>
									<th width="20%">Ref No</th>
									<th width="20%">Amount</th>
									<th width="5%">Action</th>
								</tr>
							</thead>
							<tbody id="net_bankdetails">
								<!--<tr id='0'> 
					 <td><select name="nb_details[nb_type][]" class="nb_type0" id="nb_type_0"><option value="">Select Type</option><option value=1>RTGS</option><option value=2>IMPS</option><option value=3>UPI</option></select></td>
					 <td>
						 <select name="nb_details[nb_bank][]" class="nb_bank0" style="width:150px;"><option value="">Select Bank</option>
							 <?php
								$banks = $this->payment_model->get_bank_acc_details();
								foreach ($banks as $bank) {
									echo '<option value="' . $bank['id_bank'] . '">' . $bank['acc_number'] . '</option>';
								}
								?>
						 </select>
					 </td>
					 <td>
						 <select name="nb_details[nb_device][]" class="nb_device0" style="width:150px;"><option value="">Select Device</option>
							 <?php
								$devices = $this->payment_model->get_payment_device_details();
								foreach ($devices as $device) {
									echo '<option value="' . $device['id_device'] . '">' . $device['device_name'] . '</option>';
								}
								?>
						 </select>
					 </td>
					 <td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]"/></td> 
					 <td><input type="number" step="any" class="amount" name="nb_details[amount][]"/></td> 
					 <td><a href="#" onclick="removeNb_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  
				 </tr> -->
							</tbody>
							<tfoot>
								<tr>
									<th colspan=4>Total</th>
									<th colspan=4>
										<span class="nb_total_amount"></span>
									</th>
								</tr>
								<!-- esakki -->
								<!--  <tr>
					 <th  colspan=4>Balance Amount</th>
					 <th colspan=4>
						 <span class="nb_balance_amount"></span>
					 </th>
				 </tr> -->
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" id="add_newnb" class="btn btn-success">Save</a>
				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Advance Adj -->
<div class="modal fade" id="adv-adj-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Advance Adjustment</h4>
			</div>
			<div class="modal-body">
				<!--<?php echo !empty($est_other_item['chit_details']) ? '' : 'style="display:none;"'; ?>-->
				<div class="box-body chit_details">
					<div class="row">
						<div class="box-body">
							<div class="table-responsive">
								<div class="col-md-8">
									<div class="form-group">
										<!-- esakki -->
										<span class="h_amt" style="color:red;">Balance Amount:</span>
										<!-- <span class="adv_adj_balance_amount f_amt" style="color:red;">0</span>y -->
										<span class="f_amt" style="color:red;"><?php echo $this->session->userdata('currency_symbol') ?> </span><span class="modal_balance_amount f_amt" style="color:red;">0</span>
										<br>
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
											<th width="10%;">Adjusted Amount</th>
											<th width="10%;">Balance Amount</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="2">Total</td>
											<td><span class="total_adv_amt"></span></td>
											<td><span class="total_adj_adv_amt"></span></td>
											<td><span class="total_blc_amt"></span></td>
										</tr>
										<tr>
											<td colspan="3">Total Bill Amount</td>
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
				<a href="#" id="save_receipt_adv_adj" class="btn btn-success">Save</a>
				<button type="button" class="btn btn-warning" data-dismiss="modal" id="close_add_adj">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- / Advance Adj -->
<div id="pay_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background-color: orange;">
				<!--  <button type="button" id="close_model" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
				<h3 id="myModalLabel">Payment Alert</h3>
			</div>
			<div class="modal-body">
				<p id="show_msg" style="text-align:center;"></p>
				<div class="modal-footer">
					<a href="#" id="close_model" class="close" data-dismiss="modal" aria-hidden="true">OK </a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Voucher modal starts here -->
<div class="modal fade" id="vch-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Voucher Details</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<!-- esakki -->
							<span class="h_amt" style="color:red;">Balance Amount:</span>
							<!-- <span class="vch_balance_amount f_amt" style="color:red;">0</span> -->
							<span class="f_amt" style="color:red;"><?php echo $this->session->userdata('currency_symbol') ?> </span><span class="modal_balance_amount f_amt" style="color:red;">0</span>
							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_vch"><i class="fa fa-user-plus"></i>ADD</button>
							<p class="error "><span id="vchPayAlert"></span></p>
						</div>
					</div>
					<p></p>
					<div class="table-responsive">
						<table id="vch_details" class="table table-bordered">
							<thead>
								<tr>
									<th>Voucher Code</th>
									<th>Voucher Value</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<!-- <td><input id="cheque_datetime" data-date-format="dd-mm-yyyy" class="cheque_date" name="cheque_details[cheque_date][]" type="text" required="true" placeholder="Cheque Date" /></td> -->
									<td><input type="text" step="any" class="voucher_code" name="vch_details[voucher_code][]" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)" /></td>
									<td><input type="number" step="any" class="voucher_amount" name="vch_details[payment_amount][]" /></td>
									<td><a href="#" onclick="removevch_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td>Total</td>
									<td><span class="vch_total_amount"></span></td>
									<td></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" id="vch_newvch" class="btn btn-success">Save</a>
				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- Voucher modal ends here -->
<script type="text/javascript">
	var payOtherBranch = <?php echo $this->config->item('payOtherBranch'); ?>;
</script>