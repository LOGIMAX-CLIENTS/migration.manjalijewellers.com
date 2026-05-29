<style type="text/css">
	.ui-autocomplete {
		max-height: 200px;
		overflow-y: scroll;
		overflow-x: hidden;
	}

	#myCheck:checked+#area {
		display: block !important;
	}

	.col-sm-3 {
		width: 33.333333%;
	}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Scheme
			<small>Manage scheme</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Masters</a></li>
			<li class="active">Scheme</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
				<h3 class="box-title">Scheme Master</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<div class="">
					<?php echo form_open_multipart(($sch['id_scheme'] != NULL && $sch['id_scheme'] > 0 ? 'scheme/update/' . $sch['id_scheme'] : 'scheme/save'), array('id' => 'scheme_create')) ?>
					<div class="col-md-11 col-md-offset-1">
						<div class="row">
							<div class="col-md-5">
								<div <?php if ($discount['free_first_payment'] == 1) { ?> class="form-group" <?php } else { ?> class="form-group" <?php } ?>>
									<label>Visible to Customer</label>
									<!--<input type="checkbox" id="visible" data-on-text="YES" data-off-text="NO" name="sch[visible]" value="1" <?php if ($sch['visible'] == 1) { ?> checked="true" <?php } ?>/>-->
									<input type="radio" id="" name="sch[visible]" value="0" class="minimal" <?php if ($sch['visible'] == 0) { ?> checked <?php } ?> /> Restrict to Join</label>
									<input type="radio" id="" name="sch[visible]" value="1" class="minimal" <?php if ($sch['visible'] == 1) { ?> checked <?php } ?> /> Show to All</label>
									<input type="radio" id="" name="sch[visible]" value="2" class="minimal" <?php if ($sch['visible'] == 2) { ?> checked <?php } ?> /> Show in Admin</label>
								</div>
							</div>
							<div class="col-md-3">
								<div <?php if ($discount['free_first_payment'] == 1) { ?> class="form-group" <?php } else { ?> class="form-group" <?php } ?>>
									<label>Active to Customer</label>
									<input type="checkbox" id="active" data-on-text="YES"
										data-off-text="NO" name="sch[active]" value="1" <?php if ($sch['active'] == 1) { ?> checked="true" <?php } ?> />
								</div>
							</div>
							<div class="col-md-3">
								<div <?php if ($discount['free_first_payment'] == 1) { ?> class="form-group" <?php } else { ?> class="form-group" <?php } ?>>
									<label>Free Installments</label>
									<input type="checkbox" id="has_free_ins" name="sch[has_free_ins]" value="1" <?php if ($sch['has_free_ins'] == 1) { ?> checked="true" <?php } ?> />
									<!--	<input type="text" id="free_payInstallments" 
					name="sch[free_payInstallments]" value=" <?php echo set_value('sch[free_payInstallments]', $sch['free_payInstallments']); ?>"/>-->
									<div class="form-group">
										<select class="form-control select2 cls" id='free_instalments' multiple="multiple" data-placeholder="Select a installment no"
											style="width: 100%;" disabled="true">
										</select>
										<input type="hidden" class="free_payInstallments" name='sch[free_payInstallments]' />
									</div>
								</div>
							</div>
							<!-- auto debit plan type  select option HH-->
							<div class="col-md-3">
								<label>Select Auto Debit Plan Type</label>
								<div class="form-group">
									<select id="2" name="sch[auto_debit_plan_type]" class="form-control">
										<option value="0" <?php if ($sch['auto_debit_plan_type'] == 0) { ?> selected="true" <?php } ?>>Not Applicable</option>
										<option value="1" <?php if ($sch['auto_debit_plan_type'] == 1) { ?> selected="true" <?php } ?>>Periodic</option>
										<option value="2" <?php if ($sch['auto_debit_plan_type'] == 2) { ?> selected="true" <?php } ?>>OnDemand</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<input type="checkbox" id="has_gift" data-on-text="YES" data-off-text="NO" name="sch[has_gift]" value="1" <?php if ($sch['has_gift'] == 1) { ?> checked="true" <?php } ?> />
											<label>Has Gift</label>
										</div>
										<div class="form-group">
											<input type="checkbox" id="is_enquiry" name="sch[is_enquiry]" value="1" <?php if ($sch['is_enquiry'] == 1) { ?> checked="true" <?php } ?> />
											<label>Enquiry only</label>
										</div>
										<!-- #DGS DCNM -->
								<!-- <div class="form-group" style="display: <?= $enableDigi['is_digi_gold'] ? 'none' : 'block' ?>;"> -->
								<div class="form-group" id="digiBox">
											<input type="checkbox" id="is_digi" name="sch[is_digi]" value="1" <?php if ($sch['is_digi'] == 1) { ?> checked="true" <?php } ?> />
											<label>Is Digi ?</label>
										</div>
										<div id="digiWarning" style="display:none; color:red; font-weight:bold;"></div>
									</div>
									<!--Has voucher settings if this setting is enabled , voucher can be added to that account -->
									<div class="col-md-6">
										<div class="form-group">
											<input type="checkbox" id="has_voucher" name="sch[has_voucher]" value="1" <?php if ($sch['has_voucher'] == 1) { ?> checked="true" <?php } ?> />
											<label>Has Voucher?</label>
										</div>
										<div class="form-group">
											<input type="checkbox" onchange="enableWeightslab(this)" id="is_lumpSum" name="sch[is_lumpSum]" value="1" <?php if ($sch['is_lumpSum'] == 1) { ?> checked="true" <?php } ?> />
											<label>Is LumpSum</label>
										</div>
										<div class="form-group">
											<input type="checkbox" onchange="enableTopupScheme(this)" id="is_topup_scheme" name="sch[is_topup_scheme]" value="1" <?php if ($sch['is_topup_scheme'] == 1) { ?> checked="true" <?php } ?> />
											<label>Is Topup scheme</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<p class="help-block"></p>
						<legend> <a data-toggle="tooltip" title="Enter Your Scheme Details">Scheme Detail</a></legend>
						<div class="row">
							<div class="col-sm-8">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label>Scheme Name</label>
											<input type="text" class="form-control" id="name" name="sch[scheme_name]" value="<?php echo set_value('sch[scheme_name]', $sch['scheme_name']); ?>" placeholder="Your Scheme name" required="true">
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="classi">Classification</label>
											<input type="hidden" id="classify_val" name="id_classification" value="<?php echo set_value('sch[id_classification]', $sch['id_classification']); ?>" />
											<select id="classify" name="sch[id_classification]" class="form-control" required="true"></select>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="scheme_code" class="">Scheme Code</label>
											<input type="text" class="form-control" id="code" name="sch[code]" value="<?php echo set_value('sch[code]', $sch['code']); ?>" placeholder="Scheme code">
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="scheme_code" class="">Sync Scheme Code *</label>
											<input type="text" class="form-control" required name="sch[sync_scheme_code]" value="<?php echo set_value('sch[sync_scheme_code]', $sch['sync_scheme_code']); ?>" placeholder="Sync Scheme code">
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="metal">Commodity </label>
											<input type="hidden" id="metal_val" name="id_metal" value="<?php echo set_value('sch[id_metal]', $sch['id_metal']); ?>" />
											<select id="metal" name="sch[id_metal]" class="form-control" required="true">
												<option value="" disabled selected hidden>Please Select Commodity</option>
												<?php if (!empty($chit_metals)) { foreach ($chit_metals as $cm) { ?>
												<option value="<?php echo $cm['id_metal']; ?>" <?php echo ($sch['id_metal'] == $cm['id_metal']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cm['metal']); ?></option>
												<?php } } ?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label for="metal">Purity </label>
											<input type="hidden" id="purity_val" name="id_purity" value="<?php echo set_value('sch[id_purity]', $sch['id_purity']); ?>" />
											<select id="purity" name="sch[id_purity]" class="form-control" required="true">
												<!--<option value="" selected>Please Select Purity</option>-->
											</select>
											<p class="help-block"></p>
										</div>
									</div>
									<?php if ($gst['sch_limit'] == 1) { ?>
										<div class="col-sm-6">
											<div class="form-group">
												<label>Scheme limit value</label>
												<input type="text" class="form-control input_number" required="true" id="sch_limit_value" name="sch[sch_limit_value]" value="<?php echo set_value('sch[sch_limit_value]', $sch['sch_limit_value']); ?>" placeholder="Scheme limit" required="true" />
												<p class="help-block"></p>
											</div>
										</div>
									<?php } ?>
									<?php if ($this->session->userdata('branch_settings') == 1 && $sch['branchwise_scheme'] == 1) {
										if ($sch['id_scheme'] == NULL) { ?>
											<div class="col-sm-6">
												<div class="form-group">
													<label for="Branch" class=""> <a data-toggle="tooltip" title="Select scheme applicable branches"> Branch</a></label>
													<select class="form-control select2 cls" id='branches' name='branch_data[id_branch]"' multiple="multiple" data-placeholder="Select Your Baranch"
														style="width: 100%;">
													</select>
													<input type="hidden" id="id_branch" class="id_branch" name='branch_data[id_branch]' value="" />
												</div>
											</div>
										<?php  } else {
										?>
											<div class="col-sm-6">
												<div class="form-group">
													<label for="Branch" class=""> <a data-toggle="tooltip" title="Select scheme applicable branches"> Branch</a></label>
													<select class="form-control select2 cls" id='branches' name='branch_data[id_branch]"' multiple="multiple" data-placeholder="Select Your Baranch"
														style="width: 100%;">
													</select>
													<div id="sel_br" data-sel_br='<?php echo $branch_data; ?>'></div>
													<input type="hidden" id="id_branch" class="id_branch" name='branch_data[id_branch]' />
												</div>
											</div>
									<?php  }
									} ?>
									<div class="col-sm-6">
										<label>Scheme Type</label>
										<div class="row">
											<div class="col-md-12">
												<label>
													<input type="radio" id="opt_amount" name="sch[scheme_type]" value="0" class="minimal" <?php if ($sch['scheme_type'] == 0) { ?> checked <?php } ?> /> Amount</label>
											</div>
											<div class="col-md-12">
												<label>
													<input type="radio" id="opt_weight" name="sch[scheme_type]" value="1" class="minimal" <?php if ($sch['scheme_type'] == 1) { ?> checked <?php } ?> /> Weight</label>
											</div>
											<div class="col-md-12">
												<label>
													<input type="radio" id="opt_amtToWgt" name="sch[scheme_type]" value="2" class="minimal" <?php if ($sch['scheme_type'] == 2) { ?> checked <?php } ?> /> Amount to weight</label>
											</div>
											<div class="col-md-12">
												<label>
													<input type="radio" id="opt_flexible" onChange="enableWeightslab(this)" name="sch[scheme_type]" value="3" class="minimal" <?php if ($sch['scheme_type'] == 3) { ?> checked <?php } ?> /> Flexible</label>
											</div>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label for="scheme_code" class="">Amount</label>
												<div class="input-group ">
													<span class="input-group-addon input-sm"><?php echo $this->session->userdata('currency_symbol') ?></span>
													<input type="text" class="form-control input_currency" id="amount" name="sch[amount]" value="<?php echo set_value('sch[amount]', $sch['amount']); ?>" <?php if ($sch['scheme_type'] == 0) { ?>
														placeholder="Scheme amount" required="true" <?php } ?> disabled="ture" required="false" />
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Min Installments</label>
												<input type="text" class="form-control input_number" required="true" id="min_installments" name="sch[min_installments]" value="<?php echo set_value('sch[min_installments]', $sch['min_installments']); ?>" placeholder="Minimum installments" required="true" />
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>Installments</label>
												<input type="text" class="form-control input_number" required="true" id="total_installments" name="sch[total_installments]" value="<?php echo set_value('sch[total_installments]', $sch['total_installments']); ?>" placeholder="Total installments" required="true" />
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label><a data-toggle="tooltip" title="Average of selected installment will be set as max payable">Average calc. ins.</a></label>
												<select id="avg_calc_ins" class="form-control" data-placeholder="Select installment"></select>
												<input type="hidden" class="avg_calc_select" name='sch[avg_calc_ins]' />
												<p class="help-block"></p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="chargeseme_name">Upload Scheme image</label>
									<input id="edit_sch_img" name="sch[edit_sch_img]" accept="image/*" type="file">
									<img src="<?php echo (isset($sch['logo']) ? base_url() . 'assets/img/sch_image/' . $sch['logo'] : base_url() . ('assets/img/no_image.png')); ?>" class="img-thumbnail" id="edit_sch_img_preview" style="width:304px;height:100%;" alt="scheme image">
									<p class="help-block"></p>
								</div>
								<!--	<div class="form-group">
                   	   <label>
                   		<input type="checkbox" name="sch[is_pan_required]" value="1" class="minimal"  <?php if ($sch['is_pan_required'] == 1) {  ?> checked <?php } ?> />
                   		 &nbsp;Required PAN Number
                   	   </label>	
                   	</div> 
					<div class="form-group">
                   	   <label><a  data-toggle="tooltip" title="PAN Number is made mandatory on scheme joining, when scheme amount is greater than or equal to PAN Required Amount"> PAN Required Amount</a></label>	
                   	   <input  class="form-control" type="number" placeholder="Amount"  value="<?php echo set_value('sch[pan_req_amt]', $sch['pan_req_amt']); ?>" id="pan_req_amt"name="sch[pan_req_amt]"/>
                   	</div> -->
							</div>
						</div>
						<div class="row">
							<div class="col-md-3" id="avg_settings">
								<div class="form-group">
									<label>Average Based On</label>
									<div class="row">
										<div class="col-sm-4">
											<input type="radio" id="avg_calc_by" class="minimal" name="sch[avg_calc_by]" <?php if ($sch['avg_calc_by'] == 0) {  ?>checked <?php } ?> value="0" /> By Installments
										</div>
										<div class="col-sm-4">
											<input type="radio" id="avg_calc_by" class="minimal" name="sch[avg_calc_by]" <?php if ($sch['avg_calc_by'] == 1) {  ?>checked <?php } ?> value="1" />
											By Months
										</div>
									</div>
									<p class="help-block"></p>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Scheme Approval Required</label>
									<div class="row">
										<div class="col-sm-6">
											<input type="radio" class="minimal" name="sch[sch_approval]" <?php if ($sch['sch_approval'] == 1) {  ?>checked <?php } ?> value="1" /> Yes
										</div>
										<div class="col-sm-6">
											<input type="radio" class="minimal" name="sch[sch_approval]" <?php if ($sch['sch_approval'] == 0) {  ?>checked <?php } ?> value="0" />
											No
										</div>
									</div>
									<p class="help-block"></p>
								</div>
							</div>
							<?php //print_r($sch);exit; 
							?>
							<div class="col-md-3">
								<div class="form-group" width="100%">
									<label>Store closing balance </label>
									<div class="row">
										<div class="col-sm-6">
											<input type="radio" class="minimal" name="sch[store_closing_balance]" <?php if ($sch['store_closing_balance'] == 0) {  ?>checked <?php } ?> value="0" /> Auto
										</div>
										<div class="col-sm-6">
											<input type="radio" class="minimal" name="sch[store_closing_balance]" <?php if ($sch['store_closing_balance'] == 1) {  ?>checked <?php } ?> value="1" /> Customize
										</div>
									</div>
									<p class="help-block"></p>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>Maturity Type</label>
									<select id="maturity_type" class="form-control" data-placeholder="Type" name='sch[maturity_type]'>
										<option value=1 <?php if ($sch['maturity_type'] == 1) {  ?> selected <?php } ?>>Flexible [Can pay installments and close]</option>
										<option value=2 <?php if ($sch['maturity_type'] == 2) {  ?> selected <?php } ?>>Fixed</option>
										<option value=3 <?php if ($sch['maturity_type'] == 3) {  ?> selected <?php } ?>>Fixed Flexible [Increase maturity if has Default]</option>
										<option value=4 <?php if ($sch['maturity_type'] == 4) {  ?> selected <?php } ?>>Fixed Flexible [By Lapse]</option>
									</select>
									<!--<input type="hidden" id="maturity_type" name="sch[maturity_type]" value="<?php echo set_value('sch[maturity_type]', $sch['maturity_type']); ?>"  />-->
									<p class="help-block"></p>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Maturity Installment</label>
									<input type="text" class="form-control input_number" required="true" id="maturity_installment" name="sch[maturity_installment]" value="<?php echo set_value('sch[maturity_installment]', $sch['maturity_installment']); ?>" placeholder="Maturity installment" required="true" />
									<p class="help-block"></p>
								</div>
							</div>
							<!-- <div class="col-md-3"  id="maturity_setting" style="<?php echo $sch['maturity_type'] == 2 ? "" : "display: none;"; ?>"> -->
							<div class="col-md-3" id="maturity_setting">
								<div class="form-group">
									<label> <a data-toggle="tooltip" title="Enter Maturity Days only fix Maturity Date is fixed"> Maturity Days</a></label>
									<input class="form-control" type="nnumber" placeholder="Maturity Days" value="<?php echo set_value('sch[maturity_days]', $sch['maturity_days']); ?>" id="maturity_days" name="sch[maturity_days]" />
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label> <a data-toggle="tooltip" title="Enter Close Maturity Days only fix Maturity Date is fixed"> Closing Maturity Days</a></label>
									<input class="form-control" type="number" placeholder="Close Maturity Days" value="<?php echo set_value('sch[closing_maturity_days]', $sch['closing_maturity_days']); ?>" id="closing_maturity_days" name="sch[closing_maturity_days]" />
								</div>
							</div>
							<!--#DSG-DCNM START-->
							<div class="col-md-3">
								<div class="form-group">
									<label>Show Chit Detail</label>
									<input type="text" class="form-control input_number" id="chit_detail_days" name="sch[chit_detail_days]" value="<?php echo set_value('sch[chit_detail_days]', $sch['chit_detail_days']); ?>" placeholder="Days count to show chit detail" />
									<p class="help-block"></p>
								</div>
							</div>
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-3">
										<label>Show Installment</label>
										<div class="form-group">
											<input type="radio" id="dis_paid" name="sch[show_ins_type]" value="0" class="minimal" <?php if ($sch['show_ins_type'] == 0) { ?> checked <?php } ?> /> Display paid installment<br>
											<input type="radio" id="dis_paidtot" name="sch[show_ins_type]" value="1" class="minimal" <?php if ($sch['show_ins_type'] == 1) { ?> checked <?php } ?> /> Display paid / total installments
										</div>
									</div>
									<div class="col-md-3">
										<label>Restrict Payment</label>
										<div class="form-group">
											<input type="radio" id="ap_installment" name="sch[restrict_payment]" value="0" class="minimal" disabled <?php if ($sch['restrict_payment'] == 0) { ?> checked <?php } ?> /> By Installment Count<br>
											<input type="radio" id="ap_maturity" name="sch[restrict_payment]" value="1" class="minimal" disabled <?php if ($sch['restrict_payment'] == 1) { ?> checked <?php } ?> /> By Days Count
										</div>
									</div>
									<div class="col-md-3" style="display: <?= $enableDigi['is_digi_gold'] ? 'none' : 'block' ?>;">
										<label>Total no of days to pay</label>
										<div class="form-group">
											<input type="text" id='total_days_to_pay' name="sch[total_days_to_pay]" value="<?= isset($sch['total_days_to_pay']) ? $sch['total_days_to_pay'] : '' ?>" placeholder="Total No of Days" />
										</div>
									</div>
									<!-- RAHUL 02_09_25 -->
									<div class="col-md-3">
										<label>Payable Duration(For Mobile App)</label>
										<div class="form-group">
											<select id="display_payable" class="form-control" data-placeholder="Type" name='sch[display_payable]'>
												<option value=0 <?php if ($sch['display_payable'] == 0) {  ?> selected <?php } ?>>Days</option>
												<option value=1 <?php if ($sch['display_payable'] == 1) {  ?> selected <?php } ?>>Weeks</option>
												<option value=2 <?php if ($sch['display_payable'] == 2) {  ?> selected <?php } ?>>Months</option>
												<option value=3 <?php if ($sch['display_payable'] == 3) {  ?> selected <?php } ?>>Month</option>
											</select>
										</div>
									</div>
									<div class="col-md-3" id="tot_payday_div" style="display:none;">
										<label>Total Pay Days</label>
										<div class="form-group">
											<input type="text" class="form-control input_number" id="total_days_to_pay" name="sch[total_days_to_pay]" disabled value="<?php echo set_value('sch[total_days_to_pay]', $sch['total_days_to_pay']); ?>" placeholder="Days count to allow pay" />
										</div>
									</div>
								</div>
								<!--RHR starts -->
								<legend><a data-toggle="tooltip"></a></legend>
								<div class="row">
									<div class="col-md-12">
										<div class="col-md-4">
											<label>Installment Cycle</label>
											<div class="form-group">
												<select id="installment_cycle" class="form-control" data-placeholder="Type" name='sch[installment_cycle]'>
													<option value=0 <?php if ($sch['installment_cycle'] == 0) {  ?> selected <?php } ?>>Monthly Pay</option>
													<option value=1 <?php if ($sch['installment_cycle'] == 1) {  ?> selected <?php } ?>>Daily Pay</option>
													<option value=2 <?php if ($sch['installment_cycle'] == 2) {  ?> selected <?php } ?>>Days Duration Pay</option>
													<option value=3 <?php if ($sch['installment_cycle'] == 3) {  ?> selected <?php } ?>>One Time Pay</option>
													<option value=4 <?php if ($sch['installment_cycle'] == 4) {  ?> selected <?php } ?>>Custom payable cycle</option>
												</select>
											</div>
										</div>
										<div class="col-md-4" id="ins_days_duration" <?php echo ($sch['installment_cycle'] == 2 ? 'style="display:block;"' : 'style="display:none;"'); ?>>
											<label>Days(Installment Cycle)</label>
											<div class="form-group">
												<input type="number" class="form-control input_number" id="ins_days_duration" name="sch[ins_days_duration]" value="<?php echo set_value('sch[ins_days_duration]', $sch['ins_days_duration']); ?>" placeholder="Days Duration count for installment cycle" />
											</div>
										</div>
										<div class="col-md-4" id="grace_days">
											<label>Grace Days</label>
											<div class="form-group">
												<input type="number" class="form-control input_number" id="grace_days" name="sch[grace_days]" value="<?php echo set_value('sch[grace_days]', $sch['grace_days']); ?>" placeholder="Grace Days count to allow pay" />
											</div>
										</div>
										<!--Restrict Payment -->
										<div class="col-md-4">
											<div class="form-group">
												<label>
													<input type="checkbox" id="disable_pay" class="amtsch_block" name="sch[disable_pay]" <?php if ($sch['disable_pay'] == 1) { ?> checked="checked" <?php } ?> value="1" /><span data-toggle="tooltip" title="Disable Payments"> Disable CASH payment </span>
												</label>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group ">
												<label><span data-toggle="tooltip" title="do not allow payment if amount reached this limit">CASH Payment Limit</span></label>
												<input type="number" class="form-control disable_pay_amt" id="disable_pay_amt" name="sch[disable_pay_amt]" value="<?php echo set_value('sch[disable_pay_amt]', $sch['disable_pay_amt']); ?>" <?php if ($sch['disable_pay_amt'] != 1) { ?> disabled="true" <?php } ?> />
											</div>
										</div>
									</div>
								</div>
								<!--RHR ends -->
							</div>
							<!--#DSG-DCNM END-->
						</div>
						<div id="payment_type" style="display:none;">
							<legend><a data-toggle="tooltip" title="Flexible Scheme Settings ">Flexible Scheme Settings</a></legend>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label> Flexible Scheme Type </label>
										<select class='form-control' id="pay_type">
											<option value="" <?php if ($sch['flexible_sch_type'] == '') {  ?>selected <?php } ?>> -- Select --</option>
											<option value="1" <?php if ($sch['flexible_sch_type'] == 1) {  ?>selected <?php } ?>>Amount</option>
											<option value="2" <?php if ($sch['flexible_sch_type'] == 2) {  ?>selected <?php } ?>>Amount to Weight(based on Amount)</option>
											<option value="3" <?php if ($sch['flexible_sch_type'] == 3) {  ?>selected <?php } ?>>Amount to Weight(based on Weight)</option>
											<option value="4" onChange="enableWeightslab(this)" <?php if ($sch['flexible_sch_type'] == 4) {  ?>selected <?php } ?>>Weight(based on Weight)</option>
											<option value="5" <?php if ($sch['flexible_sch_type'] == 5) {  ?>selected <?php } ?>>Weight(based on Amount)</option>
											<option value="6" <?php if ($sch['flexible_sch_type'] == 6) {  ?>selected <?php } ?>>Amount (Partly Flexible)</option>
											<option value="7" <?php if ($sch['flexible_sch_type'] == 7) {  ?>selected <?php } ?>>Weight (Partly Flexible)</option>
											<option value="8" <?php if ($sch['flexible_sch_type'] == 8) {  ?>selected <?php } ?>>Weight</option>
											<option value="9" <?php if ($sch['flexible_sch_type'] == 9) {  ?>selected <?php } ?>>Custom payable by topup value</option>
										</select>
										<input type="hidden" id="flexible_sch_type" name="sch[flexible_sch_type]" value="<?php echo set_value('sch[flexible_sch_type]', $sch['flexible_sch_type']); ?>" />
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-sm-3" id="premium_settings" style="display:none;">
									<div class="form-group">
										<label>One Time Premium</label>
										<div class="row">
											<div class="col-sm-3">
												<input type="radio" id="one_time_premium" class="minimal" name="sch[one_time_premium]" <?php if ($sch['one_time_premium'] == 1) {  ?>checked <?php } ?> value="1" /> Yes
											</div>
											<div class="col-sm-3">
												<input type="radio" id="one_time_premium" class="minimal" name="sch[one_time_premium]" <?php if ($sch['one_time_premium'] == 0) {  ?>checked <?php } ?> value="0" />
												No
											</div>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-sm-3" id="rate_selection" style="display:none;">
									<div class="form-group">
										<label>Rate Selection</label>
										<div class="row">
											<div class="col-sm-3">
												<input type="radio" id="current_rate" class="minimal" name="sch[rate_select]" <?php if ($sch['rate_select'] == 1) {  ?>checked <?php } ?> value="1" /> Current Rate
											</div>
											<div class="col-sm-3">
												<input type="radio" id="rate_history" class="minimal" name="sch[rate_select]" <?php if ($sch['rate_select'] == 0) {  ?>checked <?php } ?> value="0" />
												Rate History
											</div>
											<div class="col-sm-3">
												<input type="radio" id="lowest_rate" class="minimal" name="sch[rate_select]" <?php if ($sch['rate_select'] == 2) {  ?>checked <?php } ?> value="2" />
												Fix Lowest Rate from scheme join
											</div>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-sm-3" id="enquiry_settings" style="display:none;"> <!-- Is Enquiry settings HH-->
									<div class="form-group">
										<label>Is Enquiry</label>
										<div class="row">
											<div class="col-sm-3">
												<input type="radio" id="is_enquiry" name="sch[is_enquiry]" class="minimal" <?php if ($sch['is_enquiry'] == 1) {  ?> checked<?php } ?> value="1">
												Enquiry Only
											</div>
											<div class="col-sm-3">
												<input type="radio" id="is_enquiry" name="sch[is_enquiry]" class="minimal" <?php if ($sch['is_enquiry'] == 0) {  ?> checked<?php } ?> value="0">
												Can join scheme
											</div>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-sm-4" id="weight_convert" style="display:none;">
									<label><a data-toggle="tooltip" title="Select sheme payment Weight Convertion">Weight Convertion</a></label>
									<div class="form-group">
										<div class="col-md-3">
											<label>
												<input type="radio" id="wgt_convert_daily" name="sch[wgt_convert]" class="minimal" <?php if ($sch['wgt_convert'] == 0) {  ?> checked<?php } ?> value="0" />
												Daily
											</label>
										</div>
										<div class="col-md-6">
											<label>
												<input type="radio" id="wgt_convert_month" name="sch[wgt_convert]" class="minimal" <?php if ($sch['wgt_convert'] == 1) {  ?> checked<?php } ?> value="1" />
												End Of Scheme
											</label>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-sm-4" id="weight_store" style="display:none;">
									<label><a data-toggle="tooltip" title="Select sheme payment Weight Convertion">Weight Store AS</a></label>
									<div class="form-group">
										<div class="col-md-6">
											<label>
												<input type="radio" id="store_as_amt" name="sch[wgt_store_as]" class="minimal" <?php if ($sch['wgt_store_as'] == 0) {  ?> checked<?php } ?> value="0" />Amount</label>
										</div>
										<div class="col-md-6">
											<label>
												<input type="radio" id="store_as_wgt" name="sch[wgt_store_as]" class="minimal" <?php if ($sch['wgt_store_as'] == 1) {  ?> checked<?php } ?> value="1" />Weight
											</label>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							<div id="price_settings" style="display:none;">
								<div class="row">
									<div class="form-group">
										<div class="col-md-2">
											<label>Price Fixing</label>
											<br />
										</div>
										<div class="col-sm-8">
											<div class="col-sm-4">
												<input type="radio" id="otp_price_fixing" name="sch[otp_price_fixing]" class="minimal" <?php if ($sch['otp_price_fixing'] == 1) {  ?> checked<?php } ?> value="1">
												Yes
											</div>
											<div class="col-sm-4">
												<input type="radio" id="otp_price_fixing" name="sch[otp_price_fixing]" class="minimal" <?php if ($sch['otp_price_fixing'] == 0) {  ?> checked<?php } ?> value="0">
												No
											</div>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-md-2">
											<br />
										</div>
										<div class="col-sm-8">
											<div class="col-sm-4">
												<input type="radio" id="otp_price_fix_single" name="sch[otp_price_fix_type]" class="minimal" <?php if ($sch['otp_price_fix_type'] == 1) {  ?> checked<?php } ?> value="1">
												Single
											</div>
											<div class="col-sm-4">
												<input type="radio" id="otp_price_fix_multiple" name="sch[otp_price_fix_type]" class="minimal" <?php if ($sch['otp_price_fix_type'] == 2) {  ?> checked<?php } ?>value="2">
												Multiple
											</div>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-md-2">
											<br />
										</div>
										<div class="col-sm-8">
											<div class="col-sm-4">
												<input type="radio" id="rate_fix_sch_join" name="sch[rate_fix_by]" class="minimal" <?php if ($sch['rate_fix_by'] == 0) {  ?> checked<?php } ?> value="0">
												At the time of scheme join
											</div>
											<div class="col-sm-4">
												<input type="radio" id="rate_fix_sch_close" name="sch[rate_fix_by]" class="minimal" <?php if ($sch['rate_fix_by'] == 1) {  ?> checked<?php } ?> value="1">
												At the time of Scheme Close
											</div>
											<div class="col-sm-4">
												<input type="radio" id="rate_fix_anytime" name="sch[rate_fix_by]" class="minimal" <?php if ($sch['rate_fix_by'] == 2) {  ?> checked<?php } ?> value="2">
												Anytime
											</div>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
							</div>
						</div>
						<br />
						<div id="paymenttype_settings" style="display:none">
							<div class="row">
								<div class="col-sm-offset-1 col-sm-3" id="paymentmethod_settings" style="display:none">
									<legend><a data-toggle="tooltip" title="Set Payment Allowed per month ">Payment Type</a></legend>
									<div class="form-group">
										<div class="col-sm-12">
											<label>
												<input type="radio" id="payment_single" name="sch[pay_duration]" class="minimal" <?php if ($sch['pay_duration'] == 0) {  ?> checked<?php } ?> value="0" />
												Daily Payment
											</label>
										</div>
										<div class="col-sm-12">
											<label>
												<input type="radio" id="payment_multiple" name="sch[pay_duration]" class="minimal" <?php if ($sch['pay_duration'] == 1) {  ?> checked<?php } ?> value="1" />
												Monthly Payment
											</label>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-sm-3">
									<legend><a data-toggle="tooltip" title="Set Payment Allowed per month ">Payment Allowed per month</a></legend>
									<div class="form-group">
										<div class="col-md-12">
											<label>
												<input type="radio" id="pay_single" name="sch[payment_chances]" class="minimal" <?php if ($sch['payment_chances'] == 0) {  ?> checked<?php } ?> value="0" /> Single Payment</label>
										</div>
										<div class="col-md-12">
											<label>
												<input type="radio" id="pay_multiple" name="sch[payment_chances]" class="minimal" <?php if ($sch['payment_chances'] == 1) {  ?> checked<?php } ?> value="1" /> Multiple Payment</label>
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<legend><a data-toggle="tooltip" title="Set your scheme payment Chances limit ">Payment Chances</a></legend>
									<div class="col-md-4">
										<div class="form-group">
											<label for="units">Minimum Limit</label>
											<input type="text" class="form-control" id="min_chance" required="true" name="sch[min_chance]" value="<?php echo set_value('sch[min_chance]', $sch['min_chance']); ?>" />
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label for="units">Maximum Limit</label>
											<input type="text" class="form-control" id="max_chance" required="true" name="sch[max_chance]" value="<?php echo set_value('sch[max_chance]', $sch['max_chance']); ?>" />
										</div>
									</div>
									<!--DGS-DCNM -->
									<div class="col-md-4">
										<div class="form-group">
											<label>Daily Payment Limit</label>
											<input type="text" class="form-control input_number" id="daily_pay_limit" name="sch[daily_pay_limit]" disabled value="<?php echo set_value('sch[daily_pay_limit]', $sch['daily_pay_limit']); ?>" placeholder="Daily Payment Limit" />
											<p class="help-block"></p>
										</div>
									</div>
									<!--DGS-DCNM -->
									<p class="help-block"></p>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-sm-offset-1 col-sm-5" id="weighttye_settings" style="display:none;">
									<legend><a data-toggle="tooltip" title="Enter your scheme max_weight and min_weight Limit"> Payment Weight Limit</a></legend>
									<div class="form-group">
										<div class="col-sm-6">
											<label for="units">Minimum Limit</label>
											<input type="text" class="form-control input_weight" id="min_weight" name="sch[min_weight]" value="<?php echo set_value('sch[min_weight]', $sch['min_weight']); ?>" <?php if ($sch['scheme_type'] == 0) { ?> disabled="true" <?php } ?> />
										</div>
										<div class="col-sm-6">
											<label for="units">Maximum Limit</label>
											<input type="text" class="form-control input_weight" id="max_weight" name="sch[max_weight]" value="<?php echo set_value('sch[max_weight]', $sch['max_weight']); ?>" <?php if ($sch['scheme_type'] == 0) { ?> disabled="true" <?php } ?> />
										</div>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12" id="flexi_installments" style="display:none;">
										<div class="form-group">
											<label> Flexible Scheme Installment Settings </label>
											<div class="col-md-2 answer">
												<button type="button" id="add_sch" class="btn btn-success">ADD+</button>
											</div>
											<div class="col-sm-10 answer" style="">
												<div class="table-responsive">
													<table id="scheme_setting_tbl" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
														<thead>
															<tr>
																<th>Installment</th>
																<th>Min Value</th>
																<th>Max Value</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<?php foreach ($flex_sch_data as $key => $flx_sc) {
																echo "<tr rowID='" . $key . "'><td>From<input type='number' name='scheme_flexible[" . $key . "][ins_from]' class='ins_from' value='" . $flx_sc['ins_from'] . "' style='width: 50px;'>To<input type='number' name='scheme_flexible[" . $key . "][ins_to]' class='ins_to' value='" . $flx_sc['ins_to'] . "'  style='width: 50px;'></td><td><input type='number' name='scheme_flexible[" . $key . "][min_value]' value='" . $flx_sc['min_value'] . "' class='form-control min_value' style='width: 100px;'   style='width: 100px;'></td><td><input type='number' name='scheme_flexible[" . $key . "][max_value]' value='" . $flx_sc['max_value'] . "' class='form-control max_value' style='width: 100px;'   style='width: 100px;'></td><td><div><button id='" . $key . "' class='delete btn btn-danger'  name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
															} ?>
														</tbody>
													</table>
												</div>
											</div><!-- /.col -->
										</div>
									</div>
									<div class="col-sm-3" id="paymentamount_limit" style="display:none;">
										<legend><a data-toggle="tooltip" title="Enter your scheme max_amount and mini_amount Limit"> Payment Amount Limit</a></legend>
										<div class="col-sm-6">
											<div class="form-group">
												<label>Restrict Payment by </label>
												<select class='form-control' id="amt_restrict_by" name="sch[amt_restrict_by]">
													<option value="" <?php if ($sch['amt_restrict_by'] == '') {  ?>selected <?php } ?>> -- Select --</option>
													<option value="1" <?php if ($sch['amt_restrict_by'] == 1) {  ?>selected <?php } ?>>Total Amount Wise</option>
													<option value="2" <?php if ($sch['amt_restrict_by'] == 2) {  ?>selected <?php } ?>>Month Wise</option>
												</select>
												<p class="help-block"></p>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-6">
												<label for="units">Minimum</label>
												<input type="text" class="form-control input_amount" id="min_amount" name="sch[min_amount]" value="<?php echo set_value('sch[min_amount]', $sch['min_amount']); ?>" <?php if ($sch['scheme_type'] == 0) { ?> <?php } ?> />
											</div>
											<div class="col-sm-6">
												<label for="units">Maximum</label>
												<input type="text" class="form-control input_amount" id="max_amount" name="sch[max_amount]" value="<?php echo set_value('sch[max_amount]', $sch['max_amount']); ?>" <?php if ($sch['scheme_type'] == 0) { ?> <?php } ?> />
											</div>
											<p class="help-block"></p>
										</div>
										<br />
										<div id="flx_denomintions" style="display:block;"> <!-- enabled based on the schem type- flx sch type 1,2 H-->
											<div class="col-sm-12">
												<label for="units">Denomination</label>
												<input type="text" class="form-control input_amount" id="flx_denomintion" name="sch[flx_denomintion]" value="<?php echo set_value('sch[flx_denomintion]', $sch['flx_denomintion']); ?>" <?php if ($sch['scheme_type'] == 3 && $sch['flexible_sch_type'] == 1 && $sch['flexible_sch_type'] == 2) { ?> disabled="true" <?php } ?> />
											</div>
										</div>
									</div>
									<div class="col-sm-6" id="paymentamount_limit" style="display:none;">
										<legend><a data-toggle="tooltip" title="Scheme Payable Settings"> Payable Settings</a></legend>
										<div class="form-group">
											<input type="checkbox" value=1 id="get_amt_in_schjoin" name="sch[get_amt_in_schjoin]" <?php if ($sch['get_amt_in_schjoin'] == 1) { ?> checked="checked" <?php } ?> />
											<span for="get_amt_in_schjoin">Get payment amount in scheme join & apply as </span>
										</div>
										<div class="form-group" style="margin-left:30px;">
											<!--<input type="checkbox" value=1 id="firstPayamt_as_payamt"  name="sch[firstPayamt_as_payamt]" <?php if ($sch['firstPayamt_as_payamt'] == 1) { ?> checked="checked" <?php } ?> /> -->
											<input type="checkbox" value=1 id="firstPayamt_as_payamt" name="sch[firstPayamt_as_payamt]" <?php if ($sch['get_amt_in_schjoin'] == 1) {
																																			if ($sch['firstPayamt_as_payamt'] == 1) { ?> checked="checked" <?php }
																																																	} else { ?> disabled="true" <?php } ?> />
											<span for="firstPayamt_as_payamt">Fix First payment as Payable </span>
										</div>
										<div class="form-group" style="margin-left:30px;">
											<!-- <input type="checkbox" value=1 id="firstPayamt_maxpayable"  name="sch[firstPayamt_maxpayable]" <?php if ($sch['firstPayamt_maxpayable'] == 1) { ?> checked="checked" <?php } ?> /> -->
											<input type="checkbox" value=1 id="firstPayamt_maxpayable" name="sch[firstPayamt_maxpayable]" <?php if ($sch['get_amt_in_schjoin'] == 1) {
																																				if ($sch['firstPayamt_maxpayable'] == 1) { ?> checked="checked" <?php }
																																																		} else { ?> disabled="true" <?php } ?> />
											<span for="firstPayamt_maxpayable">Fix First payment as Max Amt </span>
										</div>
										<div class="form-group" style="margin-left:30px;">
											<input type="checkbox" value=1 id="firstPayment_as_wgt" name="sch[firstPayment_as_wgt]" <?php if ($sch['firstPayment_as_wgt'] == 1) { ?> checked="checked" <?php } ?> />
											<span for="firstPayment_as_wgt">Fix First payment as Max Weight </span>
										</div>
										<br />
										<div class="form-group" style="margin-left:30px;">
											<label>Amount based on</label></br>
											<label>
												<input type="radio" id="amt_based_on" name="sch[amt_based_on]" class="minimal" <?php if ($sch['amt_based_on'] == 0) {  ?> checked<?php } ?> value="0" />
												Avg Amount
											</label>
											<label>
												<input type="radio" id="amt_based_on" name="sch[amt_based_on]" class="minimal" <?php if ($sch['amt_based_on'] == 1) {  ?> checked<?php } ?> value="1" />
												First Pay Amount
											</label>
										</div>
										<br />
										<div class="form-group">
											<div class="row col-md-12">
												<div class="col-md-3">
													<div class="form-group">
														<span data-toggle="tooltip" title="Set No of dues">No of Dues</span>
														<input type="number" id="no_of_dues" name="sch[no_of_dues]" class="form-control" placeholder="Enter dues" value="<?php echo set_value('sch[no_of_dues]', $sch['no_of_dues']); ?>">
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<span data-toggle="tooltip" title="Ins. No. from which received payment amount will be applied as min. amount">Min. Amount from</span>
														<select id="set_as_min_from" name="sch[set_as_min_from]" class="form-control" data-placeholder="Select installment"></select>
														<input type="hidden" class="min_select" name="set_as_min_from" value="<?php echo set_value('sch[set_as_min_from]', $sch['set_as_min_from']); ?>" />
													</div>
												</div>
												<div class="col-md-3">
													<div class="form-group">
														<span data-toggle="tooltip" title="Ins. No. from which received payment amount will be applied as max. amount">Max. amount from</span>
														<select id="set_as_max_from" name="sch[set_as_max_from]" class="form-control" data-placeholder="Select installment"></select>
														<input type="hidden" class="max_select" name='set_as_max_from' value="<?php echo set_value('sch[set_as_max_from]', $sch['set_as_max_from']); ?>" />
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<br />
						<div class="row" id="settlement_settings" style="display:none">
							<div class="col-sm-12">
								<div class="box">
									<div class="box-header with-border">
										<h3 class="box-title">Settlement Setting</h3>
									</div>
									<div class="box-body">
										<div class="row">
											<div class="form-group">
												<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Type</label>
												<div class="col-md-6">
													<label class="radio-inline">
														<input type="radio" name="sch[type]" <?php if ($sch['type'] == 1) { ?> checked="true" <?php } ?> value="1" data-toggle="tooltip" title="Settlement will be done by considering Gold rates of the whole month">Monthly
													</label>
													<label class="radio-inline">
														<input type="radio" name="sch[type]" <?php if ($sch['type'] == 2) { ?> checked="true" <?php } ?> value="2" data-toggle="tooltip" title="Settlement will be done by considering Gold rates of scheme payment dates">Purchase
													</label>
													<label class="radio-inline">
														<input type="radio" name="sch[type]" <?php if ($sch['type'] == 3) { ?> checked="true" <?php } ?> value="3">No Settlement
													</label>
													<!--<label class="radio-inline">
						      <input type="radio" name="set[type]"  <?php if ($set['type'] == 4) { ?> checked="true" <?php } ?> value="4">Manual
						    </label>-->
													<p class="help-block"></p>
												</div>
											</div>
										</div>
										<div class="row adjust_block">
											<div class="form-group">
												<label for="chargeseme_name" class="col-md-2 col-md-offset-1 ">Adjust By</label>
												<div class="col-md-6">
													<label class="radio-inline">
														<input type="radio" name="sch[adjust_by]" <?php if ($sch['adjust_by'] == 1) { ?> checked="true" <?php } ?> value="1">Highest
													</label>
													<label class="radio-inline">
														<input type="radio" name="sch[adjust_by]" <?php if ($sch['adjust_by'] == 2) { ?> checked="true" <?php } ?> value="2">Lowest
													</label>
													<label class="radio-inline">
														<input type="radio" name="sch[adjust_by]" <?php if ($sch['adjust_by'] == 3) { ?> checked="true" <?php } ?> value="3">Average
													</label>
													<label class="radio-inline">
														<input type="radio" name="sch[adjust_by]" <?php if ($sch['adjust_by'] == 4) { ?> checked="true" <?php } ?> value="4">Manual
													</label>
													<p class="help-block"></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Top up scheme settings starts here -->
						<div id="topUpSchemeDiv" style="display: none;">
							<div class="row">
								<div class="col-md-12">
									<legend><a data-toggle="tooltip" title="PAN & Nominee Settings">Top up scheme Settings</a> </legend>
									<div class="col-md-2">
										<label>Top up Booking Type</label>
										<div class="form-group">
											<input type="radio" id="topup_booking_type_amt" name="sch[topup_booking_type]" value="1" class="minimal" checked <?php if ($sch['topup_booking_type'] == 1) { ?> checked <?php } ?> />&nbsp;&nbsp;<label for="topup_booking_type_amt"> Amount</label><br>
											<input type="radio" id="topup_booking_type_wgt" name="sch[topup_booking_type]" value="2" class="minimal" <?php if ($sch['topup_booking_type'] == 2) { ?> checked <?php } ?> /> &nbsp;&nbsp;<label for="topup_booking_type_wgt">Weight</label>
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Top up min value</label>
											<input type="text" class="form-control" id="topUpMinvalue" name="sch[topup_min_value]" value="<?php echo set_value('sch[topup_min_value]', $sch['topup_min_value']); ?>">
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Top up max value</label>
											<input type="text" class="form-control" id="topUpMaxvalue" name="sch[topup_max_value]" value="<?php echo set_value('sch[topup_max_value]', $sch['topup_max_value']); ?>">
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label>Top up denomination</label>
											<input type="text" class="form-control" id="topupDenomination" name="sch[topup_denomination]" value="<?php echo set_value('sch[topup_denomination]', $sch['topup_denomination']); ?>">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Top up Store Slab</label>
											<select id="topupStoreSlab" name="sch[topup_store_slab]" class="form-control">
												<option value="1" <?php if ($sch['topup_store_slab'] == 1) { ?> selected="true" <?php } ?>>Store fully completed slab</option>
												<option value="2" <?php if ($sch['topup_store_slab'] == 2) { ?> selected="true" <?php } ?>>Store current slab</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-10">
									<button type="button" id="topUpSchemeChartAddBtn" class="btn btn-success">ADD+</button>
									<div class="table-responsive">
										<table id="topUpSchemeChartTable" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
											<thead>
												<tr>
													<th>Payable Based On</th>
													<th>Slab Date</th>
													<th>Payable Type</th>
													<th>Payable Values</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php
												foreach ($topUpSchemeChart as $key => $chart) {
												?>
													<tr id="topUpChartRow_<?= $key ?>">
														<td><select name="topUpSchemeChart[<?= $key ?>][payable_by]" class='locked'>
																<option value='1' selected>Days</option>
															</select></select></td>
														<td>From &nbsp;&nbsp;<input type='number' name="topUpSchemeChart[<?= $key ?>][range_from]" class='range_from' value='<?= $chart['range_from']  ?>' style='width: 50px;'>&nbsp;To&nbsp;<input type='number' name="topUpSchemeChart[<?= $key ?>][range_to]" class='range_to' value='<?= $chart['range_to']  ?>' style='width: 50px;'></td>
														<td><select name="topUpSchemeChart[<?= $key ?>][payable_type]">
																<option value='0' <?= $chart['payable_type'] == 0 ? 'selected' : '' ?>>Amount</option>
																<option value='1' <?= $chart['payable_type'] == 1 ? 'selected' : '' ?>>Percentage</option>
															</select></td>
														<td><input type='text' name="topUpSchemeChart[<?= $key ?>][payable_value]" value="<?= $chart['payable_value']  ?>"></td>
														<td><button id="topUpSchemeRowDeleteBtn_<?= $key ?>" class='delete btn btn-danger' name='delete' type='button'><i class='fa fa-trash'></i> REMOVE</button>
									</div>
									</td>
									</tr>
									</tr>
								<?php
												}
								?>
								</tbody>
								</table>
								</div>
							</div>
						</div>
					</div>
					<!-- Top up scheme ends here -->
					<!-- PAN settings -->
					<div class="row">
						<div class="col-md-12">
							<legend><a data-toggle="tooltip" title="PAN & Nominee Settings">PAN / Aadhar No./Nominee Settings</a> </legend>
							<div class="col-md-2">
								<label>PAN</label>
								<select id="3" name="sch[is_pan_required]" class="form-control">
									<option value="0" <?php if ($sch['is_pan_required'] == 0) { ?> selected="true" <?php } ?>>Not required</option>
									<option value="1" <?php if ($sch['is_pan_required'] == 1) { ?> selected="true" <?php } ?>>Required Account-wise</option>
									<option value="2" <?php if ($sch['is_pan_required'] == 2) { ?> selected="true" <?php } ?>>Required Customer-wise</option>
								</select>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label><a data-toggle="tooltip" title="PAN Number is made mandatory on scheme joining, when scheme amount is greater than or equal to PAN Required Amount"> PAN Required Amount</a></label>
									<input class="form-control" type="number" placeholder="Amount" value="<?php echo set_value('sch[pan_req_amt]', $sch['pan_req_amt']); ?>" id="pan_req_amt" name="sch[pan_req_amt]" />
								</div>
							</div>
							<div class="col-md-2">
								<label>Nominee</label>
								<select id="3" name="sch[is_nominee_required]" class="form-control">
									<option value="0" <?php if ($sch['is_nominee_required'] == 0) { ?> selected="true" <?php } ?>>Not required</option>
									<option value="1" <?php if ($sch['is_nominee_required'] == 1) { ?> selected="true" <?php } ?>>Required Account-wise</option>
									<option value="2" <?php if ($sch['is_nominee_required'] == 2) { ?> selected="true" <?php } ?>>Required Customer-wise</option>
								</select>
							</div>
							<div class="col-md-2">
								<label>Aadhar No.</label>
								<select id="3" name="sch[is_aadhaar_required]" class="form-control">
									<option value="0" <?php if ($sch['is_aadhaar_required'] == 0) { ?> selected="true" <?php } ?>>Not required</option>
									<option value="1" <?php if ($sch['is_aadhaar_required'] == 1) { ?> selected="true" <?php } ?>>Required Account-wise</option>
									<option value="2" <?php if ($sch['is_aadhaar_required'] == 2) { ?> selected="true" <?php } ?>>Required Customer-wise</option>
								</select>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label><a data-toggle="tooltip" title="Aadhar Number is made mandatory on scheme joining, when scheme amount is greater than or equal to Aadhar Required Amount"> Aadhar Required Amount</a></label>
									<input class="form-control" type="number" placeholder="Amount" value="<?php echo set_value('sch[aadhaar_required_amt]', $sch['aadhaar_required_amt']); ?>" id="aadhaar_required_amt" name="sch[aadhaar_required_amt]" />
								</div>
							</div>
						</div>
					</div><br />
					<div class="row"> <!--lucky draw settings HH -->
						<div class="col-sm-12">
							<legend><a data-toggle="tooltip" title="enable  Lucky Draw settings"> Lucky Draw</a></legend>
							<div class="form-group">
								<div class="col-sm-8" style="margin-top: 29px;">
									<div class="col-sm-4">
										<input type="checkbox" id="isLuckyDraw" name="sch[is_lucky_draw]" value="1" <?php if ($sch['is_lucky_draw'] == 1) { ?>checked="true" <?php } ?> />
										Enable Lucky Draw
										<br />
									</div>
									<div id="isLuckyDraw_block">
										<div class="col-sm-4">
											<input type="checkbox" class="minimal isLuckyDraw_block" name="sch[has_prize]" value="1" <?php if ($sch['has_prize'] == 1) { ?>checked="true" <?php } ?> />
											Has Prize
										</div>
										<div class="col-sm-4">
											<label>Maximum Members</label>
											<input type="number" class="form-control isLuckyDraw_block" id="max_members" name="sch[max_members]" value="<?php echo set_value('sch[max_members]', $sch['max_members']); ?>" placeholder="Enter Members" />
											<br />
										</div>
									</div>
								</div>
								<p class="help-block"></p>
							</div>
						</div>
					</div><br />
					<!--// Weight slabs--Start>-->
					<div class="row">
						<div class="col-sm-12">
							<label><a data-toggle="tooltip">Weight Slabs</a></label>
							<div class="form-group">
								<div class="col-sm-3">
									<select id="joinTime_weight_slabs" name="sch[joinTime_weight_slabs][]" multiple class="form-control"></select>
									<input id="selected_wgt" type="hidden" value="<?php echo $sch['joinTime_weight_slabs'] ?>">
									<br />
								</div>
							</div>
						</div>
					</div>
					<!--Weight slabs End  -->
					<!--Add price fixing settings option hh-->
					<div class="row">
						<div class="col-sm-12">
							<legend><a data-toggle="tooltip" title="Add Your Payment Charges"> Payment Charges</a></legend>
							<div class="form-group">
								<div class="col-sm-4">
									<label>Label for Charge</label>
									<input type="text" class="form-control" required="true" name="sch[charge_head]" value="<?php echo set_value('sch[charge_head]', $sch['charge_head']); ?>" />
									<br />
								</div>
								<div class="col-sm-8" style="margin-top: 29px;">
									<div class="col-sm-4">
										<input type="radio" name="sch[charge_type]" class="minimal" <?php if ($sch['charge_type'] == 0) {  ?>checked <?php } ?> value="0" /> Percentage
									</div>
									<div class="col-sm-4">
										<input type="radio" name="sch[charge_type]" class="minimal" <?php if ($sch['charge_type'] == 1) {  ?>checked <?php } ?> value="1" />
										Amount
									</div>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="sch[charge]" value="<?php echo set_value('sch[charge]', $sch['charge']); ?>" />
									</div>
								</div>
								<p class="help-block"></p>
							</div>
						</div>
					</div><br />
					<div id="payment_setting">
						<div class="row">
							<div class="col-sm-12">
								<legend><a data-toggle="tooltip" title="Select single or multiple payment"> Free Payment settings</a></legend>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-group">
											<div class="col-sm-3">
												<label>
													<input type="checkbox" id="free_payment" name="sch[free_payment]" <?php if ($sch['free_payment'] == 1) { ?> checked="checked" <?php } ?> value="1" /><span data-toggle="tooltip" title="Free payment will be credited on a/c joining"> First payment free </span>
												</label>
											</div>
											<div class="col-sm-4">
												<div class="form-group ">
													<label>
														<input type="checkbox" id="allowSecondPay" name="sch[allowSecondPay]" value="1" <?php if ($sch['allowSecondPay'] == 1) { ?> checked="true" <?php } ?> /><span data-toggle="tooltip" title="Allow 2nd ins payment after 1st free payment"> Allow Second Pay </span>
													</label>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group ">
													<label>
														<input type="checkbox" id="approvalReqForFP" name="sch[approvalReqForFP]" value="1" <?php if ($sch['approvalReqForFP'] == 1) { ?> checked="true" <?php } ?> /><span data-toggle="tooltip" title="Admin approval required"> Approval required for Free Payment </span>
													</label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Pending payment -->
						<div class="row">
							<div class="col-sm-12">
								<legend><a data-toggle="tooltip" title="Select single or multiple payment"> Payment Settings</a></legend>
								<div class="form-group">
									<div class="col-sm-4">
										<label>
											<input type="checkbox" id="allow_unpaid" class="amtsch_block" name="sch[allow_unpaid]" <?php if ($sch['allow_unpaid'] == 1) { ?> checked="checked" <?php } ?> value="1" /> <span data-toggle="tooltip" title="If selected,customers are allowed to pay pending dues.">Allow pending due</span>
										</label>
									</div>
									<div class="col-sm-4">
										<div class="form-group ">
											<label class="col-sm-6"><span data-toggle="tooltip" title="Number of dues allowed.">No.of dues</span></label>
											<div class="col-sm-6">
												<input type="text" class="form-control unpaid_block" id="unpaid_months" name="sch[unpaid_months]" value="<?php echo set_value('sch[unpaid_months]', $sch['unpaid_months']); ?>" <?php if ($sch['allow_unpaid'] != 1) { ?> disabled="true" <?php } ?> />
											</div>
										</div>
									</div>
									<div class="col-sm-4">
										<label>Allow Unpaid In</label>
										<div class="form-group">
											<select multiple="multiple" id="allow_unpaid_in" name="sch[allow_unpaid_in][]">
												<option value="1" <?= in_array('1', $sch['allow_unpaid_in']) ? 'selected' : '' ?>>Admin</option>
												<option value="0" <?= in_array('0', $sch['allow_unpaid_in']) ? 'selected' : '' ?>>Web App</option>
												<option value="2" <?= in_array('2', $sch['allow_unpaid_in']) ? 'selected' : '' ?>>Mobile App</option>
												<option value="3" <?= in_array('3', $sch['allow_unpaid_in']) ? 'selected' : '' ?>>Admin App</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- /Pending payment -->
						<br />
						<!-- Advance payment -->
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<div class="col-sm-4">
										<label>
											<input type="checkbox" id="allow_advance" class="amtsch_block" name="sch[allow_advance]" <?php if ($sch['allow_advance'] == 1) { ?> checked="checked" <?php } ?> value="1" /><span data-toggle="tooltip" title="If selected,customers are allowed to pay advance payment."> Allow advance payment </span>
										</label>
									</div>
									<div class="col-sm-4">
										<div class="form-group ">
											<label class="col-sm-6"><span data-toggle="tooltip" title="Number of dues allowed.">No.of dues</span></label>
											<div class="col-sm-6">
												<input type="text" class="form-control advance_block" id="advance_months" name="sch[advance_months]" value="<?php echo set_value('sch[advance_months]', $sch['advance_months']); ?>" <?php if ($sch['allow_advance'] != 1) { ?> disabled="true" <?php } ?> />
											</div>
										</div>
									</div>
									<div class="col-sm-4">
										<label>Allow Advance In</label>
										<div class="form-group">
											<select multiple="multiple" id="allow_advance_in" name="sch[allow_advance_in][]">
												<option value="1" <?= in_array('1', $sch['allow_advance_in']) ? 'selected' : '' ?>>Admin</option>
												<option value="0" <?= in_array('0', $sch['allow_advance_in']) ? 'selected' : '' ?>>Web App</option>
												<option value="2" <?= in_array('2', $sch['allow_advance_in']) ? 'selected' : '' ?>>Mobile App</option>
												<option value="3" <?= in_array('3', $sch['allow_advance_in']) ? 'selected' : '' ?>>Admin App</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--/ Advance payment -->
						<br />
						<!-- Pre-close -->
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<!-- //RAHUL 24_11_25 -->
									<div class="col-sm-3">
										<label>
											<input type="checkbox" id="allow_preclose" class="amtsch_block" name="sch[allow_preclose]" <?php if ($sch['allow_preclose'] == 1) { ?> checked="checked" <?php } ?> value="1" /><span data-toggle="tooltip" title="If selected,customers can pre-close the scheme account."> Allow pre-close </span>
										</label>
									</div>
									<div class="col-sm-2">
										<div class="form-group ">
											<label class="col-sm-6 "><span data-toggle="tooltip" title="Number of dues pending.">No.of dues</span></label>
											<div class="col-sm-6">
												<input type="text" class="form-control preclose_block" id="preclose_months" name="sch[preclose_months]" value="<?php echo set_value('sch[preclose_months]', $sch['preclose_months']); ?>" <?php if ($sch['allow_preclose'] != 1) { ?> disabled="true" <?php } ?> />
											</div>
										</div>
									</div>
									<div class="col-sm-2">
										<div class="form-group " id="preclose_benefits_box">
											<label class="col-sm-6"><span data-toggle="tooltip" title="Select 'YES' if benefits are applicable for pre-closer. ">Avail Benefits</span></label>
											<div class="col-sm-6">
												<input type="checkbox" id="preclose_benefits" class="mySwitch preclose_block" data-on-text="YES" data-off-text="NO" name="sch[preclose_benefits]" value="1" <?php if ($sch['preclose_benefits'] == 1) { ?> checked="true" <?php } ?> />
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group" id="preclose_type_box">
											<label class="">Pre-close by</label>
											<select id="preclose_type" class="form-control" data-placeholder="Type" name='sch[preclose_type]'>
												<option value=0 <?php if ($sch['preclose_type'] == 0) {  ?> selected <?php } ?>>Days</option>
												<option value=1 <?php if (($sch['preclose_type'] == 1) || empty($sch['preclose_type'])) {  ?> selected <?php } ?>>Installments</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div></br>
					<!--/ Pre-close -->
					<!-- Chit General Advance settings (GA) block with separate benefit settings .... Dt Added : 06-11-2023, By: #AB -->
					<div class="row">
						<div class="col-md-12">
							<legend><a data-toggle="tooltip" title="Add Your Advance settings"> General Advance Settings </a></legend>
							<div class="row">
								<div class="form-group">
									<div class="col-md-3">
										<label>
											<input type="checkbox" id="allow_general_advance" class="amtsch_block" name="sch[allow_general_advance]" <?php if ($sch['allow_general_advance'] == 1) { ?> checked="checked" <?php } ?> value="1" /> <span data-toggle="tooltip" title="If selected,customers are allowed to pay extra general payments in advance."> General advance</span>
										</label>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label for="units">Minimum Amount</label>
											<input type="text" class="form-control" id="adv_min_amt" name="sch[adv_min_amt]" value="<?php echo set_value('sch[adv_min_amt]', $sch['adv_min_amt']); ?>" />
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label for="units">Maximum Amount</label>
											<input type="text" class="form-control" id="adv_max_amt" name="sch[adv_max_amt]" value="<?php echo set_value('sch[adv_max_amt]', $sch['adv_max_amt']); ?>" />
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label for="units">Denomination</label>
											<input type="text" class="form-control" id="adv_denomination" name="sch[adv_denomination]" value="<?php echo set_value('sch[adv_denomination]', $sch['adv_denomination']); ?>" />
										</div>
									</div>
								</div>
								<div class="row" id="adv_benefit_block">
									<div class="col-sm-12">
										<div class="form-group">
											<label for="payment_limit" class="col-md-2 black" style="<?php echo $sch['apply_adv_benefit'] == 1 ? "" : "display: block;"; ?>">
												<input type="checkbox" id="apply_adv_benefit" name="sch[apply_adv_benefit]" value="1" <?php if ($sch['apply_adv_benefit'] == 1) { ?>checked="true" <?php } ?> />Apply advance benefits
											</label>
											<div class="col-md-2 adv_answer" style="<?php echo $sch['apply_adv_benefit'] == 1 ? "" : "display: none;"; ?>">
												<button type="button" id="adv_proceed" class="btn btn-success">ADD+</button>
											</div>
											<div class="col-sm-10 answer" style="<?php echo $sch['apply_adv_benefit'] == 1 ? "" : "display: block;"; ?>">
												<div class="table-responsive">
													<table id="adv_benefit_chart" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
														<thead>
															<tr>
																<th>Interest By</th>
																<th>Interest For</th>
																<th>Type</th>
																<th>Value</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<?php foreach ($adv_benefit_data as $key => $adv) {
																$amtType = $adv['interest_type'] == 1 ? "checked='checked'" : "";
																$perType = $adv['interest_type'] == 0 ? "checked='checked'" : "";
																$interest_by_months = $adv['interest_by'] == 0 ? "selected" : "";    //DGS-DCNM
																echo "<tr rowID='" . $key . "'>
									<td>
										<select class='interest_by' name='adv_chart[" . $key . "][interest_by]' value='" . $adv['interest_by'] . "'>
											<option value='0' " . $interest_by_months . ">Month wise</option>
										</select>
									</td>   
									<td>From<input type='number' name='adv_chart[" . $key . "][installment_from]' class='installment_from' value='" . $adv['installment_from'] . "' style='width: 50px;'>To<input type='number' name='adv_chart[" . $key . "][installment_to]' class='installment_to' value='" . $adv['installment_to'] . "'  style='width: 50px;'></td>
									<td><input type='radio' name='adv_chart[" . $key . "][interest_type]' class='interest_type' " . $amtType . " value=1  style='width: 50px;'>Amt<input type='radio' name='adv_chart[" . $key . "][interest_type]' class='interest_type'  " . $perType . "  value=0  style='width: 50px;'>%</td>
									<td><input type='text' name='adv_chart[" . $key . "][interest_value]' value='" . $adv['interest_value'] . "' class='form-control interest_value' onkeypress=\"return /[0-9.]/i.test(event.key)\" style='width: 100px;'   style='width: 100px;'></td>
									<td><div><button id='" . $key . "' class='delete btn btn-danger'  name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>
								</tr>";
															}  ?>
														</tbody>
													</table>
												</div>
											</div><!-- /.col -->
											<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Chit GA ends-->
					<div class="row">
						<div class="col-sm-12">
							<legend><a data-toggle="tooltip" title="Add Your Scheme Payment Benefits"> Benefits</a></legend>
							<!-- RHR Calculation type row starts here -->
							<div class="row">
								<div class="col-sm-12">
									<label for="" class="col-sm-2">Calculation Type</label>
									<div class=" col-sm-2">
										<label>
											<input type="radio" name="sch[calculation_type]" class="minimal" <?php if ($sch['calculation_type'] == 1) { ?>checked <?php } ?> value="1" /> Installment Wise
										</label>
									</div>
									<div class="col-sm-2">
										<label>
											<input type="radio" name="sch[calculation_type]" class="minimal" <?php if ($sch['calculation_type'] == 2) { ?>checked <?php } ?> value="2" />
											Maturity Wise
										</label>
									</div>
								</div>
							</div><br />
							<!-- RHR Calculation type row ends here -->
							<div class="form-group">
								<label for="payment_limit" class="col-md-2">
									<input type="checkbox" id="isInterest" name="sch[interest]" class="minimal" value="1" <?php if ($sch['interest'] == 1) {  ?>checked <?php } ?> />
									Interest
								</label>
								<div id="interest_amtblock">
									<div class="col-md-2">
										<label>
											<input type="radio" name="sch[interest_by]" class="minimal interest_block" <?php if ($sch['interest_by'] == 0) {  ?>checked <?php } ?> value="0" /> Percentage
										</label>
									</div>
									<div class="col-md-2">
										<label>
											<input type="radio" name="sch[interest_by]" class="minimal interest_block" <?php if ($sch['interest_by'] == 1) {  ?>checked <?php } ?> value="1" />
											Amount
										</label>
									</div>
									<div class="col-md-2">
										<input type="text" class="form-control interest_block" id="interest_value" name="sch[interest_value]" value="<?php echo set_value('sch[interest_value]', $sch['interest_value']); ?>" />
									</div>
									<div class="col-md-4">
										<label class="col-md-3">Total</label>
										<div class="col-md-8">
											<div class="input-group">
												<span class="input-group-addon input-sm">
													<?php echo $this->session->userdata('currency_symbol') ?>
												</span>
												<input type="text" class="form-control interest_block" id="total_interest" name="sch[total_interest]" value="<?php echo set_value('sch[total_interest]', $sch['total_interest']); ?>" readonly="true" />
											</div>
										</div>
									</div>
								</div>
								<div id="interest_wgtblock" style="display: none;">
									<div class="col-sm-offset-6 col-sm-4">
										<label class="col-sm-4">Weight</label>
										<div class="col-sm-8">
											<div class="input-group">
												<input type="text" class="form-control interest_block" id="interest_weight" name="sch[interest_weight]" value="<?php echo set_value('sch[interest_weight]', $sch['interest_weight']); ?>" />
												<span class="input-group-addon input-sm">
													g
												</span>
											</div>
										</div>
									</div>
								</div>
								<p class="help-block"></p>
							</div>
						</div>
					</div></br>
					<!-- RHR benefits modifiction done in interest_ins_block div -->
					<div class="row" id="interest_ins_block">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="payment_limit" class="col-md-2 black" style="<?php echo $sch['apply_benefit_by_chart'] == 1 ? "" : "display: block;"; ?>">
									<input type="checkbox" id="isapply_benefit_by_chart" name="sch[apply_benefit_by_chart]" value="1" <?php if ($sch['apply_benefit_by_chart'] == 1) { ?>checked="true" <?php } ?> />
									Apply interest by chart
								</label>
								<div class="col-md-2 answer" style="<?php echo $sch['apply_benefit_by_chart'] == 1 ? "" : "display: none;"; ?>">
									<button type="button" id="proced" class="btn btn-success">ADD+</button>
									</span>
								</div>
								<div class="col-sm-10 answer" style="<?php echo $sch['apply_benefit_by_chart'] == 1 ? "" : "display: none;"; ?>">
									<div class="table-responsive">
										<table id="chart_creation_tbl" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
											<thead>
												<tr>
													<!--RHR -->
													<th>Interest Calc On</th>
													<!--RHR -->
													<!--DGS-DCNM -->
													<th>Interest By</th>
													<th>Interest For</th>
													<!--DGS-DCNM -->
													<th>Type</th>
													<th>Value</th>
													<!--RHR -->
													<th>Installment No</th>
													<th>Commodity</th>
													<!--RHR -->
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($chartData as $key => $chart) {
													//echo '<pre>';print_r($chartData);exit;
													$amtType = $chart['interest_type'] == 1 ? "checked='checked'" : "";
													$perType = $chart['interest_type'] == 0 ? "checked='checked'" : "";
													$interest_by_ins = $chart['interest_by'] == 0 ? "selected" : "";    //DGS-DCNM
													$interest_by_day = $chart['interest_by'] == 1 ? "selected" : "";    //DGS-DCNM
													$purchaseVA_MCdiscount = $chart['interest_by'] == 2 ? "selected" : "";   //retail purpose just store not to include in any of closing calculation
													//DGS-DCNM ---> add <td> interest_by...
													if ($chart['int_calc_on'] == '2') {
														echo "<tr rowID='" . $key . "'>
									<td><b>By Installment(For Bonus)</b><input type='hidden' name='installmentchart[" . $key . "][int_calc_on]'  value='2'></td>
									<td>-</td>   
									<td>-</td>
									<td>-</td> 
									<td>-</td> 
									<td>
									<select id='installment_no' class='installment_no' name='installmentchart[" . $key . "][installment_no]'></select>
									<input type='hidden' id='bonus_ins' value=" . $chart['installment_no'] . ">
									</td>
									<td>-</td>
									<td>-</td>
									</tr>";
													} else {
														echo "<tr rowID='" . $key . "'>
									<td><b>By Value</b><input type='hidden' name='installmentchart[" . $key . "][int_calc_on]'  value='1'></td>
									<td><select class='interest_by' name='installmentchart[" . $key . "][interest_by]' value='" . $chart['interest_by'] . "'>
										<option value='0' " . $interest_by_ins . ">By Installments</option>
										<option value='1' " . $interest_by_day . ">By Days</option>
										<option value='2' " . $purchaseVA_MCdiscount . ">Purchase VA&MC Discount</option>
									</select></td>   
									<td>From<input type='number' name='installmentchart[" . $key . "][installment_from]' class='installment_from' value='" . $chart['installment_from'] . "' style='width: 50px;'>To<input type='number' name='installmentchart[" . $key . "][installment_to]' class='installment_to' value='" . $chart['installment_to'] . "'  style='width: 50px;'></td>
									<td><input type='radio' name='installmentchart[" . $key . "][interest_type]' class='interest_type' " . $amtType . " value=1  style='width: 50px;'>Amt<input type='radio' name='installmentchart[" . $key . "][interest_type]' class='interest_type'  " . $perType . "  value=0  style='width: 50px;'>%</td>
									<td><input type='number' name='installmentchart[" . $key . "][interest_value]' value='" . $chart['interest_value'] . "' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td>
									<td>-</td>
									<td>
									<select name='installmentchart[" . $key . "][commodityIdForChart]' class='form-control' required>
										<option value='' disabled hidden " . (empty($chart['commodity']) ? 'selected' : '') . ">Please Select Commodity</option>
										<option value='1' " . (($chart['commodity'] == '1') ? 'selected' : '') . ">GOLD</option>
										<option value='2' " . (($chart['commodity'] == '2') ? 'selected' : '') . ">SILVER</option>
									</select>
									</td>
									<td><div><button id='" . $key . "' class='delete btn btn-danger'  name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>
								</tr>";
													}
												} ?>
											</tbody>
										</table>
									</div>
								</div><!-- /.col -->
								<p class="help-block"></p>
							</div>
						</div>
					</div>
					</br>
					<!--	<div class="row" id="interest_ins_block">
			   <div class="col-sm-12">
				<div class="form-group">
				    <div class="col-md-3">
			 	     <label>Interest Type</label>
							   	 <select id="interest_type" class="form-control"  name='sch[interest_type]'>
							   	     <option value=0>--Select Interest type--</option>
							   	     <option value=1 <?php if ($sch['interest_type'] == 1) {  ?> selected <?php } ?>>All Installments</option>
							   	     <option value=2 <?php if ($sch['interest_type'] == 2) {  ?> selected <?php } ?>>All Installment SpecificPayment</option>
							   	     <option value=3 <?php if ($sch['interest_type'] == 3) {  ?> selected <?php } ?>>Specific Installment</option>
							   	     <option value=4 <?php if ($sch['interest_type'] == 3) {  ?> selected <?php } ?>>Specific Installment Specific Payment</option>
							   	 </select> 
					   </div>
					   <div class="col-md-3">
					       <label>Interest Installment</label>
					  <div id="interest_amtblock"> 
				     <select id="interest_ins_sel" class="form-control" data-placeholder="Select Your installments" ></select>
	                <input type="hidden" class="interest_ins" name='sch[interest_ins]' />
				    </div> 
				   </div> 
			    	</div>
			   </div>
	  	  </div> -->
					</br>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="payment_limit" class="col-sm-2">
									<input type="checkbox" id="isTaxable" class="minimal" name="sch[tax]" <?php if ($sch['tax'] == 1) {  ?>checked <?php } ?> value="1" />
									Tax
								</label>
								<div class="col-sm-2">
									<label>
										<input type="radio" name="sch[tax_by]" class="minimal tax_block" <?php if ($sch['tax_by'] == 0) {  ?>checked <?php } ?> value="0" /> Percentage
									</label>
								</div>
								<div class="col-sm-2">
									<label>
										<input type="radio" name="sch[tax_by]" class="minimal tax_block" <?php if ($sch['tax_by'] == 1) {  ?>checked <?php } ?> value="1" />
										Amount
									</label>
								</div>
								<div class="col-sm-2">
									<input type="text" class="form-control tax_block" id="tax_value" name="sch[tax_value]" value="<?php echo set_value('sch[tax_value]', $sch['tax_value']); ?>" />
								</div>
								<div class="col-sm-4">
									<label class="col-sm-3">Total</label>
									<div class="col-sm-8">
										<div class="input-group">
											<span class="input-group-addon input-sm">
												<?php echo $this->session->userdata('currency_symbol') ?>
											</span>
											<input type="text" id="total_tax" name="sch[total_tax]" class="form-control tax_block" value="<?php echo set_value('sch[total_tax]', $sch['total_tax']); ?>" readonly="true" />
										</div>
									</div>
								</div>
							</div>
						</div>
					</div><br>
					<!--	<div class="row">
			   <div class="col-sm-offset-1 col-sm-11">
			 	<div class="form-group">
                   <label for="payment_limit" class="col-sm-2">
					 <input type="checkbox" id="isFirstPayDisc" class="minimal" name="sch[firstPayDisc]" <?php if ($sch['firstPayDisc'] == 1) {  ?>checked <?php } ?> value="1"  />
                  	First Pay Disc
               	 </label>
                  <div class="col-sm-6">
					  <div class="col-sm-4">
						<label>
						   <input type="radio" name="sch[firstPayDisc_by]" class="minimal" <?php if ($sch['firstPayDisc_by'] == 0) {  ?>checked <?php } ?> value="0" /> Percentage
						</label>
					   </div> 
					   <div class="col-sm-4">
						<label>
						  <input type="radio" name="sch[firstPayDisc_by]" class="minimal" <?php if ($sch['firstPayDisc_by'] == 1) {  ?>checked <?php } ?> value="1"  />
						  Amount 
						</label>
					   </div> 
					   <div class="col-sm-4">
						<input type="text" class="form-control" id="firstPayDisc_value" name="sch[firstPayDisc_value]" value="<?php echo set_value('sch[firstPayDisc_value]', $sch['firstPayDisc_value']); ?>" /> 
					   </div>
				  </div> 
                   <p class="help-block"></p> 
			 </div> 
			 </div> 
			</div></br>
				<div class="row">
			   <div class="col-sm-offset-1 col-sm-11">
			 	<div class="form-group">
                   <label for="payment_limit" class="col-sm-2">
					 <input type="checkbox" id="all_pay_disc" class="minimal" name="sch[all_pay_disc]" <?php if ($sch['all_pay_disc'] == 1) {  ?>checked <?php } ?> value="1"  />
                  	All  Pay Disc
               	 </label>
                  <div class="col-sm-6">
					  <div class="col-sm-4">
						<label>
						   <input type="radio" name="sch[allpay_disc_by]" class="minimal" <?php if ($sch['allpay_disc_by'] == 0) {  ?>checked <?php } ?> value="0" /> Percentage
						</label>
					   </div> 
					   <div class="col-sm-4">
						<label>
						  <input type="radio" name="sch[allpay_disc_by]" class="minimal" <?php if ($sch['allpay_disc_by'] == 1) {  ?>checked <?php } ?> value="1"  />
						  Amount 
						</label>
					   </div> 
					   <div class="col-sm-4">
						<input type="text" class="form-control" id="allpay_disc_value" name="sch[allpay_disc_value]" value="<?php echo set_value('sch[allpay_disc_value]', $sch['allpay_disc_value']); ?>" /> 
					   </div>
				  </div> 
                   <p class="help-block"></p> 
			 </div> 
			 </div> 
			</div></br>-->
					<!--<div class="row">
			   <div class="col-sm-offset-1 col-sm-11">
			    <legend><a  data-toggle="tooltip" title="Select Scheme Refferal Payment Benefits"> Refferal Benifits</a></legend>
			 	<div class="form-group">
                   <label for="payment_limit" class="col-sm-3">
						Referral By 
               	 </label>
                  <div class="col-sm-9" style="margin-left: -51px;">
					  <div class="col-sm-4">
						<label for="payment_limit">
							 <input type="checkbox" id="emp_refferal" class="minimal" name="sch[emp_refferal]" <?php if ($sch['emp_refferal'] == 1) {  ?>checked <?php } ?> value="1"  />
							Employee
						</label>
					   </div> 
					   <div class="col-sm-4">
						 <label for="payment_limit">
							 <input type="checkbox" id="cus_refferal" class="minimal" name="sch[cus_refferal]" <?php if ($sch['cus_refferal'] == 1) {  ?>checked <?php } ?> value="1"  />
							Customer 
						 </label>
					   </div> 
				  </div> 
                   <p class="help-block"></p> 
			 </div>			
			 </div> 
			</div></br>-->
					<br>
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="payment_limit" class="col-sm-2">
									<input type="checkbox" id="discount" class="minimal" name="sch[discount]" <?php if ($sch['discount'] == 1) {  ?>checked <?php } ?> value="1" />
									Discount
								</label>
								<div class="col-sm-2">
									<label>
										<input type="radio" id="discount_type" name="sch[discount_type]" class="minimal" <?php if ($sch['discount_type'] == 0) {  ?>checked <?php } ?> value="0" /> All Installments
									</label>
								</div>
								<div class="col-sm-2">
									<label>
										<input type="radio" id="discount_type" name="sch[discount_type]" class="minimal" <?php if ($sch['discount_type'] == 1) {  ?>checked <?php } ?> value="1" /> Specific Installments
									</label>
								</div>
								<div class="col-sm-2">
									<select id="disc_select" class="form-control" data-placeholder="Select Your installments"></select>
									<input type="hidden" class="disc_select" name='sch[discount_installment]' />
								</div>
							</div>
							<p class="help-block"></p>
						</div>
					</div></br>
					<div class="row">
						<div class="col-sm-12">
							<label for="" class="col-sm-2">Discount Type</label>
							<div class=" col-sm-2">
								<label>
									<input type="radio" name="sch[firstPayDisc_by]" class="minimal" <?php if ($sch['firstPayDisc_by'] == 0) {  ?>checked <?php } ?> value="0" /> Percentage
								</label>
							</div>
							<div class="col-sm-2">
								<label>
									<input type="radio" name="sch[firstPayDisc_by]" class="minimal" <?php if ($sch['firstPayDisc_by'] == 1) {  ?>checked <?php } ?> value="1" />
									Amount
								</label>
							</div>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="firstPayDisc_value" name="sch[firstPayDisc_value]" value="<?php echo set_value('sch[firstPayDisc_value]', $sch['firstPayDisc_value']); ?>" />
							</div>
						</div>
					</div><br />
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="payment_limit" class="col-md-2 precloseblock_open" style="<?php echo $sch['apply_debit_on_preclose'] == 1 ? "" : "display: black;"; ?>">
									<input type="checkbox" id="apply_debit_on_preclose" name="sch[apply_debit_on_preclose]" value="1" <?php if ($sch['apply_debit_on_preclose'] == 1) { ?>checked="true" <?php } ?> />
									Deduct closing balance on pre-close
								</label>
								<div class="col-md-2 precloseblock" style="<?php echo $sch['apply_debit_on_preclose'] == 1 ? "" : "display: none;"; ?>">
									<button type="button" id="proceds" class="btn btn-success">ADD+</button>
									</span>
								</div>
								<div class="col-sm-10 precloseblock" style="<?php echo $sch['apply_debit_on_preclose'] == 1 ? "" : "display: none;"; ?>">
									<div class="table-responsive">
										<table id="preclose_chart_creation_tbl" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
											<thead>
												<tr>
													<th>Installment</th>
													<th>Duduction Type</th>
													<th>Duduction Value</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($preclosechartdata as $key => $chart) {
													$amtType = $chart['deduction_type'] == 1 ? "checked='checked'" : "";
													$perType = $chart['deduction_type'] == 0 ? "checked='checked'" : "";
													echo "<tr rowID='" . $key . "'><td>From<input type='number' name='installmentpreclosechart[" . $key . "][installment_from]' class='installment_from' value='" . $chart['installment_from'] . "' style='width: 50px;'>To<input type='number' name='installmentpreclosechart[" . $key . "][installment_to]' class='installment_to' value='" . $chart['installment_to'] . "'  style='width: 50px;'></td><td><input type='radio' name='installmentpreclosechart[" . $key . "][deduction_type]' class='deduction_type' " . $amtType . " value=1  style='width: 50px;'>Amt<input type='radio' name='installmentpreclosechart[" . $key . "][deduction_type]' class='deduction_type'  " . $perType . "  value=0  style='width: 50px;'>%</td><td><input type='number' name='installmentpreclosechart[" . $key . "][deduction_value]' value='" . $chart['deduction_value'] . "' class='form-control deduction_value' style='width: 100px;'   style='width: 100px;'></td><td><div><button id='" . $key . "' class='delete btn btn-danger'  name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
												} ?>
											</tbody>
										</table>
									</div>
								</div><!-- /.col -->
								<p class="help-block"></p>
							</div>
						</div>
						<div class="col-sm-4">
							<label class="col-sm-3"><a data-toggle="tooltip" title="Minimun installment for preclose with discount/benefit available."> Preclose Min Ins</a></label>
							<div class="col-sm-8">
								<div class="form-group">
									<select id="apply_benefit_min_ins" class="form-control" data-placeholder="Select installment" name='sch[apply_benefit_min_ins]'></select>
								</div>
							</div>
						</div>
					</div></br>
					<br>
					<!--	<div class="row">
			   <div class="col-sm-12">
				    <legend><a  data-toggle="tooltip" title="Select Scheme Refferal Payment Benefits"> Refferal Benifits</a></legend>
				 	<div class="form-group">
	                   <label for="payment_limit" class="col-sm-2">
					   Referrals Applicable
	               	 </label> 
					  <div class="col-sm-2">
						<label>
						   <input type="radio" name="sch[ref_benifitadd_ins_type]" class="minimal" <?php if ($sch['ref_benifitadd_ins_type'] == 0) {  ?>checked <?php } ?> value="0" /> All Installments
						</label>
					   </div> 
					   <div class="col-sm-2">
						<label>
						  <input type="radio" name="sch[ref_benifitadd_ins_type]" class="minimal" <?php if ($sch['ref_benifitadd_ins_type'] == 1) {  ?>checked <?php } ?> value="1"  /> Specific Installments
						 </label>
					   </div> 
					   <div class="col-sm-2">
					     <select id="install_select" class="form-control" data-placeholder="Select Your installments" ></select>
		                 <input type="hidden" class="installs_select" name='sch[ref_benifitadd_ins]' />
					   </div> 
	                   <p class="help-block"></p> 
				 	</div>			
			 	</div> 
			</div></br>			
			<div class="row">
			   <div class="col-sm-12">
			 	<div class="form-group">
                   <label for="payment_limit" class="col-sm-2">
					 <input type="checkbox" id="emp_refferal" class="minimal" name="sch[emp_refferal]" <?php if ($sch['emp_refferal'] == 1) {  ?>checked <?php } ?> value="1"  />
                  	Employee
               	 </label> 
				  <div class="col-sm-2">
					<label>
					   <input type="radio" name="sch[emp_refferal_by]" class="minimal" <?php if ($sch['emp_refferal_by'] == 0) {  ?>checked <?php } ?> value="0" /> Percentage
					</label>
				   </div> 
				   <div class="col-sm-2">
					<label>
					  <input type="radio" name="sch[emp_refferal_by]" class="minimal" <?php if ($sch['emp_refferal_by'] == 1) {  ?>checked <?php } ?> value="1"  />
					  Amount 
					</label>
				   </div> 
				   <div class="col-sm-2">
					<input type="any" class="form-control" id="emp_refferal_value" name="sch[Emp_ref_values]" value="<?php echo set_value('sch[Emp_ref_values]', $sch['Emp_ref_values']); ?>" /> 
				   </div> 
				   <div class="col-sm-2">
				       <label>Installment to deduct referal amount if customer preclose before this installment</label>
				       </div>
				   <div class="col-sm-2">
					<input type="any" class="form-control" id="emp_deduct_ins" name="sch[emp_deduct_ins]" value="<?php echo set_value('sch[emp_deduct_ins]', $sch['emp_deduct_ins']); ?>" /> 
				   </div> 
                   <p class="help-block"></p> 
			 </div>			
			 </div> 
			</div></br>
			<div class="row">
    			   <div class="col-sm-12">
    			 	<div class="form-group"> 
                       <label for="payment_limit" class="col-sm-2">
    					 <input type="checkbox" id="cus_refferal" class="minimal" name="sch[cus_refferal]" <?php if ($sch['cus_refferal'] == 1) {  ?>checked <?php } ?> value="1"  />
                      	Customer 
    	               </label> 
    				   <div class="col-sm-2">
    						<label>
    						   <input type="radio" name="sch[cus_refferal_by]" class="minimal" <?php if ($sch['cus_refferal_by'] == 0) {  ?>checked <?php } ?> value="0" /> Percentage
    						</label>
    					   </div>  
    					   <div class="col-sm-2">
    						<label>
    						  <input type="radio" name="sch[cus_refferal_by]" class="minimal" <?php if ($sch['cus_refferal_by'] == 1) {  ?>checked <?php } ?> value="1"  />
    						  Amount 
    						</label>
    					   </div> 
    					   <div class="col-sm-2">
    						<input type="text" class="form-control" id="cus_refferal_value" name="sch[cus_ref_values]" value="<?php echo set_value('sch[cus_ref_values]', $sch['cus_ref_values']); ?>" /> 
    					   </div> 
    					   <div class="col-sm-2">
				       <label>Installment to deduct Customer</label>
				       </div>
				   <div class="col-sm-2">
					<input type="any" class="form-control" id="cus_deduct_ins" name="sch[cus_deduct_ins]" value="<?php echo set_value('sch[cus_deduct_ins]', $sch['cus_deduct_ins']); ?>" /> 
				   </div> 
    	                   <p class="help-block"></p>                                          	 	
    				 	</div> 
    			    </div> 
			</div>
			<div class="row">
			   <div class="col-sm-12">
				<div class="form-group">
                   <label for="payment_limit" class="col-md-2 agentblock_open" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: black;"; ?>">
					<input type="checkbox" id="apply_agent_benefit" name="sch[agent_refferal]" value="1"<?php if ($sch['agent_refferal'] == 1) { ?>checked="true" <?php } ?> />
                  	Agent Benefits
               	 </label>
				 <div class="col-sm-4">
					   <label class="col-sm-3"><a  data-toggle="tooltip" title=""> Agent Credit Type</a></label>
					   <div class="col-sm-8">
					     <div class="form-group">
							<select id="agent_credit" class="form-control" data-placeholder="Select Credit Type" name='sch[agent_credit_type]'>
								<option value=0>--Select Credit type--</option>
							   	     <option value=1 <?php if ($sch['agent_credit_type'] == 1) {  ?> selected <?php } ?>>Credit based on scheme joined distributor</option>
							   	     <option value=2 <?php if ($sch['agent_credit_type'] == 2) {  ?> selected <?php } ?>>Based on payment collected distributor</option>
							</select> 
						 </div>
					   </div> 
				   </div>
               	  <div class="col-md-2 agentblock" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: none;"; ?>" >
					<button type="button" id="agent_add_row" class="btn btn-success">ADD+</button>
	                    </span>
				  </div> 
					<div class="col-sm-10 agentblock" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: none;"; ?>"> 
                	<div class="table-responsive"> 
                         <table id="agent_chart_creation_tbl" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid" >
                        <thead>
                             <tr>  
                        <th>Installment</th>
                        <th>Benefit Type</th>
                        <th>Benefit Value</th>
                        <th>Action</th>
                      </tr>
                        </thead>
                          <tbody>
                              <?php foreach ($agentbenefitchart as $key => $chart) {
									$amtType = $chart['benefit_type'] == 1 ? "checked='checked'" : "";
									$perType = $chart['benefit_type'] == 0 ? "checked='checked'" : "";
									echo "<tr rowID='" . $key . "'><td>From<input type='number' name='agent_benefit_chart[" . $key . "][installment_from]' class='installment_from' value='" . $chart['installment_from'] . "' style='width: 50px;'>To<input type='number' name='agent_benefit_chart[" . $key . "][installment_to]' class='installment_to' value='" . $chart['installment_to'] . "'  style='width: 50px;'></td><td><input type='radio' name='agent_benefit_chart[" . $key . "][benefit_type]' class='benefit_type' " . $amtType . " value='" . $chart['benefit_type'] . "'  style='width: 50px;'>Amt<input type='radio' name='agent_benefit_chart[" . $key . "][benefit_type]' class='benefit_type'  " . $perType . "  value='" . $chart['benefit_type'] . "'  style='width: 50px;'>%</td><td><input type='number' name='agent_benefit_chart[" . $key . "][benefit_value]' value='" . $chart['benefit_value'] . "' class='form-control benefit_value' style='width: 100px;'   style='width: 100px;'></td><td><div><button id='" . $key . "' class='delete btn btn-danger'  name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
								} ?>
                          </tbody>
                         </table> 
                    </div>
                </div>
			    <div class="col-sm-2">
				       </div>
			   <div class="col-sm-2 agentblock" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: none;"; ?>" >
				       <label>Installment to deduct Agent</label>
				       </div>
				   <div class="col-sm-2 agentblock" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: none;"; ?>" >
					<input type="any" class="form-control" id="agent_deduct_ins" name="sch[agent_deduct_ins]" value="<?php echo set_value('sch[agent_deduct_ins]', $sch['agent_deduct_ins']); ?>" /> 
				   </div> 
				<p class="help-block"></p>                       	 	
			 </div> 
			</div></div></br></br> -->
					<!--Agent benefit end -->
					<!-- Employee & Agent Incentive Benefit settings worked for CJ. DB Tables: scheme , scheme_incentive_settings (START)-->
					<div class="row">
						<div class="col-md-12">
							<legend><a data-toggle="tooltip" title="Select Employee & Agent Incentive Benefits">Employee & Agent Incentive Benefits</a></legend>
							<div class="form-group">
								<div class="col-md-2"> <!-- employee div -->
									<label for="credit_incentive">
										<input type="checkbox" id="emp_credit_incentive" name="sch[emp_refferal]" value="1" <?php if ($sch['emp_refferal'] == 1) { ?>checked="true" <?php } ?> /> Enable Employee Incentive
									</label>
									<input type="hidden" name="sch[cus_refferal]" id="cus_refferal">
								</div>
								<div class="col-md-2">
									<label for="payment_limit">
										<input type="checkbox" id="cus_refferal" class="minimal" name="sch[cus_refferal]" <?php if ($sch['cus_refferal'] == 1) {  ?>checked <?php } ?> value="1" />
										Customer Referral
									</label>
								</div>
								<div class="col-md-2 emp_incentive_block" style="<?php echo $sch['emp_refferal'] == 1 ? "" : "display: none;"; ?>">
									<button type="button" id="add_emp_incentive_row" class="btn btn-success">ADD+</button>
								</div>
								<div class="col-md-2 emp_incentive_block" style="<?php echo $sch['emp_refferal'] == 1 ? "" : "display: none;"; ?>">
									<label>Employee Preclose deduct Ins</label>
								</div>
								<div class="col-md-2 emp_incentive_block" style="<?php echo $sch['emp_refferal'] == 1 ? "" : "display: none;"; ?>">
									<input type="any" class="form-control" id="emp_deduct_ins" name="sch[emp_deduct_ins]" value="<?php echo set_value('sch[emp_deduct_ins]', $sch['emp_deduct_ins']); ?>" />
								</div>
								<div class="col-md-2 emp_incentive_block" style="<?php echo $sch['emp_refferal'] == 1 ? "" : "display: none;"; ?>">
									<label>Customer Preclose deduct Ins</label>
								</div>
								<div class="col-md-2 emp_incentive_block" style="<?php echo $sch['emp_refferal'] == 1 ? "" : "display: none;"; ?>">
									<input type="any" class="form-control" id="cus_deduct_ins" name="sch[cus_deduct_ins]" value="<?php echo set_value('sch[cus_deduct_ins]', $sch['cus_deduct_ins']); ?>" />
								</div>
								<div class="col-md-3 emp_incentive_block" style="<?php echo $sch['emp_refferal'] == 1 ? "" : "display: none;"; ?> color:red;">Note : Installment to deduct referal amount if employee preclose before this installment</div>
								<div class="col-md-10 emp_incentive_block" style="<?php echo $sch['emp_refferal'] == 1 ? "" : "display: none;"; ?>">
									<div class="table-responsive">
										<table id="emp_incentive_chart_table" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
											<thead>
												<tr>
													<th>Credit To</th>
													<th>Credit For</th>
													<th>From Range</th>
													<th>To Range</th>
													<th>Credit Type</th>
													<th>Credit Value</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($incentive_chart as $key => $chart) {
													if ($chart['credit_to'] == 1) {
														$amtType = $chart['credit_type'] == 0 ? "checked='checked'" : "";
														$perType = $chart['credit_type'] == 1 ? "checked='checked'" : "";
														$credit_to = $chart['credit_to'] == 1 ? "Employee" : "Agent";
														$credit_for_0 = $chart['credit_for'] == 0 ? "selected='selected'" : "";
														$credit_for_1 = $chart['credit_for'] == 1 ? "selected='selected'" : "";
														$credit_for_2 = $chart['credit_for'] == 2 ? "selected='selected'" : "";
														$ins = $sch['total_installments'];
														echo "<tr rowID='" . $key . "'>
								  <td><input type='hidden' name='incentive_chart[emp][" . $key . "][credit_to]' value='" . $chart['credit_to'] . "'><span><b>" . $credit_to . "</b></span></td>
								  <td><select class='credit_for' name='incentive_chart[emp][" . $key . "][credit_for]' value='" . $chart['credit_for'] . "'>
									<option value='0' " . $credit_for_0 . ">New scheme joining</option>
									<option value='1' " . $credit_for_1 . ">customer intro scheme join</option>
									<option value='2' " . $credit_for_2 . ">Payment based on day</option>
								  </select></td>
								  <td><select class='credit_range' name='incentive_chart[emp][" . $key . "][credit_from_range]'>" ?>
														<?php
														if ($chart['credit_for'] == 0 || $chart['credit_for'] == 1) {
															$x = 1;
															while ($x <= $ins) {
																echo "<option value='" . $x . "' " . ($chart['from_range'] == $x ? "selected" : "") . "> " . $x . " </option>";
																$x++;
															}
														} else if ($chart['credit_for'] == 3) {
															$x = 1;
															while ($x <= 31) {
																echo "<option value='" . $x . "' " . ($chart['from_range'] == $x ? "selected" : "") . "> " . $x . " </option>";
																$x++;
															}
														} else if ($chart['credit_for'] == 2) {
															$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
															$x = 0;
															while ($x <= 6) {
																echo "<option value='" . $days[$x] . "' " . ($chart['from_range'] == $days[$x] ? "selected" : "") . "> " . $days[$x] . " </option>";
																$x++;
															}
														}
														?>
														<?php
														echo "</select></td>
								  <td><select class='credit_range' name='incentive_chart[emp][" . $key . "][credit_to_range]'>" ?>
														<?php
														if ($chart['credit_for'] == 0 || $chart['credit_for'] == 1) {
															$x = 1;
															while ($x <= $ins) {
																echo "<option value='" . $x . "' " . ($chart['to_range'] == $x ? "selected" : "") . "> " . $x . " </option>";
																$x++;
															}
														} else if ($chart['credit_for'] == 3) {
															$x = 1;
															while ($x <= 31) {
																echo "<option value='" . $x . "' " . ($chart['to_range'] == $x ? "selected" : "") . "> " . $x . " </option>";
																$x++;
															}
														} else if ($chart['credit_for'] == 2) {
															$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
															$x = 0;
															while ($x <= 6) {
																echo "<option value='" . $days[$x] . "' " . ($chart['to_range'] == $days[$x] ? "selected" : "") . "> " . $days[$x] . " </option>";
																$x++;
															}
														}
														?>
												<?php
														echo "</select></td>
								  <td><input type='radio' name='incentive_chart[emp][" . $key . "][credit_type]' class='credit_type' " . $amtType . " value='0'  style='width: 50px;'>Amt<input type='radio' name='incentive_chart[emp][" . $key . "][credit_type]' class='credit_type'  " . $perType . "  value='1'  style='width: 50px;'>%</td>
								  <td><input type='number' step='any' name='incentive_chart[emp][" . $key . "][credit_value]' value='" . $chart['credit_value'] . "' class='form-control credit_value' style='width: 100px;'   style='width: 100px;'></td>
								  <td><div><button id='" . $key . "' class='delete btn btn-danger'  name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>
								  </tr>";
													}
												} ?>
											</tbody>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="form-group"> <!-- agent div -->
								<div class="col-md-2">
									<label for="agent_credit_incentive">
										<input type="checkbox" id="agent_credit_incentive" name="sch[agent_refferal]" value="1" <?php if ($sch['agent_refferal'] == 1) { ?>checked="true" <?php } ?> /> Enable Agent Incentive
									</label>
								</div>
								<div class="col-md-2 agent_incentive_block" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: none;"; ?>">
									<button type="button" id="add_agent_incentive_row" class="btn btn-success">ADD+</button>
								</div>
								<div class="col-md-2 agent_incentive_block" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: none;"; ?>">
									<label>Preclose deduct Ins</label>
								</div>
								<div class="col-sm-2 agent_incentive_block" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: none;"; ?>">
									<input type="any" class="form-control" id="agent_deduct_ins" name="sch[agent_deduct_ins]" value="<?php echo set_value('sch[agent_deduct_ins]', $sch['agent_deduct_ins']); ?>" />
								</div>
								<div class="col-md-10 agent_incentive_block" style="<?php echo $sch['agent_refferal'] == 1 ? "" : "display: none;"; ?>">
									<div class="table-responsive">
										<table id="agent_incentive_chart_table" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
											<thead>
												<tr>
													<th>Credit To</th>
													<th>Credit For</th>
													<th>From Range</th>
													<th>To Range</th>
													<th>Credit Type</th>
													<th>Credit Value</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($incentive_chart as $key => $chart) {
													if ($chart['credit_to'] == 2) {
														$amtType = $chart['credit_type'] == 0 ? "checked='checked'" : "";
														$perType = $chart['credit_type'] == 1 ? "checked='checked'" : "";
														$credit_to = $chart['credit_to'] == 1 ? "Employee" : "Agent";
														$credit_for_0 = $chart['credit_for'] == 0 ? "selected='selected'" : "";
														$credit_for_3 = $chart['credit_for'] == 3 ? "selected='selected'" : "";
														$ins = $sch['total_installments'];
														echo "<tr rowID='" . $key . "'>
								  <td><input type='hidden' name='incentive_chart[age][" . $key . "][credit_to]' value='" . $chart['credit_to'] . "'><span><b>" . $credit_to . "</b></span></td>
								  <td><select class='credit_for' name='incentive_chart[age][" . $key . "][credit_for]' value='" . $chart['credit_for'] . "'>
									<option value='0' " . $credit_for_0 . ">New scheme joining</option>
									<option value='3' " . $credit_for_3 . ">Payment based on date</option>
								  </select></td>
								  <td><select class='credit_range' name='incentive_chart[age][" . $key . "][credit_from_range]'>" ?>
														<?php
														if ($chart['credit_for'] == 0 || $chart['credit_for'] == 1) {
															$x = 1;
															while ($x <= $ins) {
																echo "<option value='" . $x . "' " . ($chart['from_range'] == $x ? "selected" : "") . "> " . $x . " </option>";
																$x++;
															}
														} else if ($chart['credit_for'] == 3) {
															$x = 1;
															while ($x <= 31) {
																echo "<option value='" . $x . "' " . ($chart['from_range'] == $x ? "selected" : "") . "> " . $x . " </option>";
																$x++;
															}
														}
														?>
														<?php
														echo "</select></td>
								  <td><select class='credit_range' name='incentive_chart[age][" . $key . "][credit_to_range]'>" ?>
														<?php
														if ($chart['credit_for'] == 0 || $chart['credit_for'] == 1) {
															$x = 1;
															while ($x <= $ins) {
																echo "<option value='" . $x . "' " . ($chart['to_range'] == $x ? "selected" : "") . "> " . $x . " </option>";
																$x++;
															}
														} else if ($chart['credit_for'] == 3) {
															$x = 1;
															while ($x <= 31) {
																echo "<option value='" . $x . "' " . ($chart['to_range'] == $x ? "selected" : "") . "> " . $x . " </option>";
																$x++;
															}
														}
														?>
												<?php
														echo "</select></td>
								  <td><input type='radio' name='incentive_chart[age][" . $key . "][credit_type]' class='credit_type' " . $amtType . " value='0'  style='width: 50px;'>Amt<input type='radio' name='incentive_chart[age][" . $key . "][credit_type]' class='credit_type'  " . $perType . "  value='1'  style='width: 50px;'>%</td>
								  <td><input type='number' step='any' name='incentive_chart[age][" . $key . "][credit_value]' value='" . $chart['credit_value'] . "' class='form-control credit_value' style='width: 100px;'   style='width: 100px;'></td>
								  <td><div><button id='" . $key . "' class='delete btn btn-danger'  name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td>
								  </tr>";
													}
												} ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					</br></br></br>
					<!-- Employee & Agent Incentive Benefit settings worked for CJ. DB Tables: scheme , scheme_incentive_settings (ENDS)-->
					<div class="row">
						<div class="col-sm-12">
							<legend><a data-toggle="tooltip" title="Add Your Employee Referral Incentive"> Employee Referral Incentive</a></legend>
							<div class="form-group">
								<label for="during_sch_closing" class="col-md-2">
									<input type="checkbox" id="incentive_scheme_close" class="minimal" name="sch[emp_incentive_closing]" <?php if ($sch['emp_incentive_closing'] == 1) {  ?>checked value="1" <?php } else { ?> value="0" <?php } ?> />
									Credit Incentive During Scheme Closing
								</label>
								<div id="select_options_check">
									<div class="col-md-2">
										<input type="radio" class="recurring_data_gst" id="click_label_yes_gst" name="sch[closing_incentive_based_on]" <?php if ($sch['closing_incentive_based_on'] == 1) {  ?>checked <?php } ?> value="1" disabled><label for="click_label_yes_gst">&nbsp;&nbsp;Closing Installment</label>
									</div>
									<div class="col-md-2">
										<input type="radio" class="recurring_data_gst" id="click_label_no_gst" name="sch[closing_incentive_based_on]" <?php if ($sch['closing_incentive_based_on'] == 2) {  ?>checked <?php } ?> value="2" disabled><label for="click_label_no_gst">&nbsp;&nbsp;Closing Weight</label>
									</div>
								</div>
								<p class="help-block"></p>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="col-md-2 answer" style="<?php echo $sch['emp_incentive_closing'] == 1 || $sch['emp_incentive_closing'] == 0 ? "" : "display: none;"; ?>">
							<button type="button" id="proced_closing" class="btn btn-success" disabled>ADD+</button>
							</span>
						</div>
						<div class="col-sm-10 answer" style="<?php echo $sch['emp_incentive_closing'] == 1 || $sch['emp_incentive_closing'] == 0 ? "" : "display: none;"; ?>">
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-12">
						<div class="table-responsive">
							<table id="chart_creation_tbl_closing" width="50%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid">
								<thead>
									<tr>
										<th>Installment</th>
										<th>Type</th>
										<th>Value</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($closing_data as $key => $chart) {
										$amtType = $chart['type'] == 1 ? "checked='checked'" : "";
										$perType = $chart['type'] == 2 ? "checked='checked'" : "";
										$selected = "selected='selected'";
										echo "<tr rowID='" . $key . "'>"
											. "<td>From<input type='number' name='installmentchart_closing[" . $key . "][incentive_from]' class='installment_from' value='" . $chart['incentive_from'] . "' style='width: 50px;' step='any'>To<input type='number' name='installmentchart_closing[" . $key . "][incentive_to]' class='installment_to' value='" . $chart['incentive_to'] . "'  style='width: 50px;' step='any'></td>"
											. "<td><input type='radio' name='installmentchart_closing[" . $key . "][type]' class='interest_type' " . $amtType . " value='1'  style='width: 50px;'>Amt<input type='radio' name='installmentchart_closing[" . $key . "][type]' class='interest_type'  " . $perType . " value='2'  style='width: 50px;'>%</td>"
											. "<td><input type='number' step='any' name='installmentchart_closing[" . $key . "][value]' value='" . $chart['value'] . "' class='form-control interest_value' style='width: 100px;'   style='width: 100px;'></td>"
											. "<td><div><button id='" . $key . "' class='delete btn btn-danger'  name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button></div></td></tr>";
									} ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<input type="hidden" name="gst_setting" value="<?php echo set_value('gst[gst_setting]', $gst['gst_setting']) ?>" />
				<?php if ($gst['gst_setting'] == 1) { ?>
					<!-- START OF GST Settings -->
					<legend>GST Settings</legend>
					<div class="row">
						<div class="col-md-12">
							<div class="col-sm-12">
								<label>GST type</label>
								<div class="row">
									<div class="col-sm-3">
										<input type="radio" name="sch[gst_type]" value="0" <?php if ($sch['gst_type'] == 0) { ?> checked="true" <?php } ?>> Inclusive
									</div>
									<div class="col-sm-3">
										<input type="radio" name="sch[gst_type]" value="1" <?php if ($sch['gst_type'] == 1) { ?> checked="true" <?php } ?>> Exclusive
									</div>
									<div class="col-sm-3">
										<input type="text" class="form-control" id="hsn_code" name="sch[hsn_code]" value="<?php echo set_value('sch[hsn_code]', $sch['hsn_code']); ?>" placeholder="HSN Code">
										HSN Code
									</div>
								</div>
								<p class="help-block"></p>
							</div>
							<p class="help-block"></p>
						</div>
						<div class="row">
							<div class="col-sm-offset-1 col-sm-10">
								<div class="box box-primary">
									<div class="box-header with-border">
										<h3 class="box-title">GST Split up</h3>
									</div>
									<div class="box-body">
										<?php if ($sch['id_scheme'] == NULL) { ?>
											<div class="row">
												<div class="form-group">
													<div class="col-sm-2">
														<input type="text" readonly="true" value="GST" class="form-control " name="gst_data[0][splitup_name]" />
													</div>
													<div class="col-sm-2">
														<div class="input-group ">
															<input type="text" class="form-control " name="gst_data[0][percentage]" value='3' />
															<input type="hidden" class="form-control " name="gst_data[0][type]" value='' />
															<span class="input-group-addon input-sm"><?php echo '%'; ?></span>
														</div>
													</div>
												</div>
											</div>
											<p class="help-block"></p>
											<div class="row">
												<div class="form-group">
													<div class="col-sm-2">
														<input type="text" class="form-control " name="gst_data[1][splitup_name]" value="CGST" />
													</div>
													<div class="col-sm-2">
														<div class="input-group ">
															<input type="text" class="form-control " name="gst_data[1][percentage]" value="1.5" />
															<span class="input-group-addon input-sm">%</span>
														</div>
													</div>
													<div class="col-md-6">
														<div class="row">
															<div class="col-md-5">
																<input type="radio" name="gst_data[1][type]" value="0" checked="true"> Same State GST
															</div>
															<div class="col-md-5">
																<input type="radio" name="gst_data[1][type]" value="1"> Other State GST
															</div>
														</div>
													</div>
												</div>
											</div>
											<p class="help-block"></p>
											<div class="row">
												<div class="form-group">
													<div class="col-sm-2">
														<input type="text" class="form-control " name="gst_data[3][splitup_name]" value="SGST" />
													</div>
													<div class="col-sm-2">
														<div class="input-group ">
															<input type="text" class="form-control " name="gst_data[3][percentage]" value="1.5" />
															<span class="input-group-addon input-sm">%</span>
														</div>
													</div>
													<div class="col-md-6">
														<div class="row">
															<div class="col-md-5">
																<input type="radio" name="gst_data[3][type]" value="0" checked="true"> Same State GST
															</div>
															<div class="col-md-5">
																<input type="radio" name="gst_data[3][type]" value="1"> Other State GST
															</div>
														</div>
													</div>
												</div>
											</div>
											<p class="help-block"></p>
											<div class="row">
												<div class="form-group">
													<div class="col-sm-2">
														<input type="text" class="form-control " name="gst_data[4][splitup_name]" value="IGST" />
													</div>
													<div class="col-sm-2">
														<div class="input-group ">
															<input type="text" class="form-control " name="gst_data[4][percentage]" value="3.00" />
															<span class="input-group-addon input-sm">%</span>
														</div>
													</div>
													<div class="col-md-6">
														<div class="row">
															<div class="col-md-5">
																<input type="radio" name="gst_data[4][type]" value="0"> Same State GST
															</div>
															<div class="col-md-5">
																<input type="radio" name="gst_data[4][type]" value="1" checked="true"> Other State GST
															</div>
														</div>
													</div>
												</div>
											</div>
											<p class="help-block"></p>
											<?php
										} else {
											$i = 0;
											foreach ($gst_data as $gst) { ?>
												<div class="row">
													<div class="form-group">
														<div class="col-sm-2">
															<input type="text" class="form-control " name="gst_data[<?php echo $i ?>][splitup_name]" value="<?php echo set_value('gst_data[splitup_name]', $gst['splitup_name']); ?>" <?php if ($gst['type'] == NULL) { ?> readonly="true" <?php } ?> />
														</div>
														<div class="col-sm-2">
															<div class="input-group ">
																<input type="text" class="form-control " name="gst_data[<?php echo $i ?>][percentage]" value="<?php echo set_value('gst_data[percentage]', $gst['percentage']); ?>" />
																<span class="input-group-addon input-sm"><?php echo '%'; ?></span>
															</div>
														</div>
														<?php if ($gst['type'] != NULL) { ?>
															<div class="col-md-6">
																<div class="row">
																	<div class="col-md-5">
																		<input type="radio" name="gst_data[<?php echo $i ?>][type]" value="0" <?php if ($gst['type'] == 0) { ?> checked="true" <?php } ?>> Same State GST
																	</div>
																	<div class="col-md-5">
																		<input type="radio" name="gst_data[<?php echo $i ?>][type]" value="1" <?php if ($gst['type'] == 1) { ?> checked="true" <?php } ?>> Other State GST
																	</div>
																</div>
															</div>
														<?php } ?>
													</div>
												</div>
												<p class="help-block"></p>
											<?php
												$i++;
											}
											if (sizeof($gst_data) > 0) { ?>
												<input type="checkbox" name="sch[update_gst]" value="1" class="minimal" /> <span style="color:red;">Check to update GST</span>
										<?php }
										} ?>
									</div>
								</div>
							</div>
						</div> <?php  } ?>
					<!-- END OF GST Settings -->
					</div>
					<div class="row">
						<div class="col-sm-offset-1 col-sm-11">
							<legend><a data-toggle="tooltip" title="Notification Content"> Notification Content</a></legend>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class='form-group'>
								<label for="user_lastname"></label>
								<textarea class="form-control" name="sch[noti_msg]" id="noti_msg" cols="35" rows="5" tabindex="4"><?php echo set_value('sch[noti_msg]', $sch['noti_msg']); ?></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-offset-1 col-sm-11">
							<legend><a data-toggle="tooltip" title="Scheme Description"> Description</a></legend>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class='form-group'>
								<label for="user_lastname"></label>
								<textarea id="description" name="sch[description]"><?php echo set_value('sch[description]', $sch['description']); ?></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-offset-1 col-sm-11">
							<legend><a data-toggle="tooltip" title="Key Benefit Description"> Key Benefit Description</a></legend>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class='form-group'>
								<label for="key_benifits_description"></label>
								<textarea id="key_benifits_description" class="form-control" name="sch[key_benifits_description]" cols="35" rows="5" tabindex="4"><?php echo set_value('sch[key_benifits_description]', $sch['key_benifits_description']); ?></textarea>
							</div>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-12">
							<div class="box box-default"><br />
								<div class="col-xs-offset-5">
									<button type="submit" id="submit" class="btn btn-primary">Save</button>
									<button type="button" class="btn btn-default btn-cancel">Cancel</button>
								</div> <br />
							</div>
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