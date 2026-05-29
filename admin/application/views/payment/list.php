<style>
	.h_amt {
		font-size: 23px;
	}

	.f_amt {
		font-size: 26px;
	}
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Payment
			<small>Manage your offline payments</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo site_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="<?php echo site_url('payment/list'); ?>">Payment</a></li>
			<li class="active">Payment List</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Payment List</h3> <span id="total_payments" class="badge bg-green"></span>
						<div class="pull-right">
							<div class="col-md-2">
								<!-- esakki -->
								<!-- <a class="btn bg-aqua pull-right"  id="trans_submit" href="<?php echo base_url('index.php/payment/pay_list'); ?>" ><i class="fa fa fa-search"></i> Search transactions</a> -->
							</div>
							<!-- <a class="btn btn-success" id="add_post_payment" href="<?php echo base_url('index.php/payment/insertTrans'); ?>" ><i class="fa fa-plus-circle"></i> Insert Offline Payment</a> -->
							<?php if (($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <= 2) || $this->config->item('showToAdminsOnly') == 0) { ?>
								<!--<a class="btn btn-primary" href="<?php echo base_url('index.php/admin_payment/insertTransInPayment'); ?>"><i class="fa fa-retweet"></i> Sync Offline Payments</a> -->
								<!-- esakki -->
								<!-- &nbsp;&nbsp;<button type="button"  id="revert_approval" class="btn btn-warning"><i class="fa fa-user-plus" ></i>   Revert Approval</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
							<?php } ?>
							<a class="btn btn-success" id="add_post_payment" href="<?php echo base_url('index.php/payment/add'); ?>"><i class="fa fa-plus-circle"></i> Add</a>
							<!--<a class="btn btn-success"  id="trans_submit" href="<?php echo base_url('index.php/payment/pay_list'); ?>" ><i class="fa fa-plus-circle"></i> Payment List</a> -->
						</div>
					</div>
					<input type="hidden" name="" id="old_sch_acc_num" value="<?php echo $this->session->userdata('enable_old_sch_acc_no'); ?>">
					<div class="box-body">
						<!-- Alert -->
						<div class="row">
							<div class="col-sm-8 col-sm-offset-2">
								<div id="error-msg"></div>
								<div id="payment_container"></div>
							</div>
						</div>
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
						</br>
						<div class="row">
							<div class="col-md-12">
								<?php if ($this->payment_model->entry_date_settings() == 1) { ?>
									<div class="col-md-2">
										<div class="form-group">
											<label>Filter Date By</label>
											<select id="date_Select" class="form-control">
												<option value=1 selected>Payment Date</option>
												<option value=2>Entry Date</option>
											</select>
											<input id="id_type" name="scheme[id_type]" type="hidden" value="" />
										</div>
									</div>
								<?php } else { ?>
									<input id="id_type" name="scheme[id_type]" type="hidden" value="" />
								<?php } ?>
								<div class="col-md-2">
									<div class="form-group">
										<!-- esakki -->
										<label><span id="payment_date_range"></span></label>
										<button class="btn btn-default btn_date_range" id="payment-dt-btn">
											<span style="display:none;" id="payment_list1"></span>
											<span style="display:none;" id="payment_list2"></span>
											<i class="fa fa-calendar"></i> Date range picker
											<i class="fa fa-caret-down"></i>
										</button>
									</div>
								</div>
								<div class="col-md-2">
									<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
										<div class="form-group">
											<label>Filter By Branch</label>
											<select id="branch_select" class="form-control"></select>
											<input id="id_branch" name="scheme[id_branch]" type="hidden" value="" />
										</div>
									<?php } else { ?>
										<input id="id_branch" name="scheme[id_branch]" type="hidden" value="<?php echo $this->session->userdata('id_branch'); ?>" />
									<?php } ?>
								</div>
								<div class="col-md-2">
									<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
										<div class="form-group">
											<label>Filter By Employee</label>
											<select id="employee_select" class="form-control"></select>
											<input id="id_employee" name="scheme[id_employee]" type="hidden" value="" />
										</div>
									<?php } ?>
								</div>
								<!--status_filder start-14-12-23-santhosh-->
								<div class="col-md-2">
									<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
										<div class="form-group">
											<label>Filter By Status</label>
											<select id="status_select" class="form-control"></select>
											<input id="id_status" name="scheme[id_status]" type="hidden" value="" />
										</div>
									<?php } ?>
								</div>
								<!--status_filder end 14-12-23-santhosh-->
								<!--serach by mobile filter --start -14-12-23-->
								<div class="col-md-2">
									<div class="form-group">
										<div class="form-group">
											<label>Search by mobile</label>
											<input type="text" placeholder="Mobile Number" class="form-control" name="" id="mobilenumber">
											<input type="hidden" name="customer_id" id="customer_id" />
										</div>
									</div>
								</div>
								<!--serach by mobile filter --end -14-12-23-->
								<?php if ($this->payment_model->get_rptnosettings() == 0) { ?>
									<br />
									<div class="col-md-2">
										<div class="form-group">
											<button type="button" id="conform_save" class="btn btn-primary pull-right conform_recpt"><i class="fa fa-user-plus"></i>Update Receipt</button>
										</div>
									</div>
								<?php } ?>
							</div>
						</div></br>
						<?php if ($this->payment_model->get_rptnosettings() == 1 || $this->payment_model->get_rptnosettings() == 2) { ?>
							<div class="table-responsive">
								<table id="payment_list" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th>ID</th>
											<!--  <?php if ($this->payment_model->entry_date_settings() == 1) { ?>	
					<th>Entry Date</th>
					<?php } else { ?>
					<th>Payment Date</th>
					<?php } ?>  -->
											<th>Payment Date</th>
											<th>Custom Entry Date</th>
											<?php if ($this->session->userdata('enable_old_sch_acc_no') == 1) { ?>
												<th>Old A/c No</th>
											<?php } ?>
											<th>Customer</th>
											<th>A/c Name</th>
											<th>Scheme code</th>
											<th>Group code</th>
											<th>A/c No</th>
											<th>Mobile</th>
											<th>Total Paid Instal.</th>
											<th>Installment No</th>
											<th>Type</th>
											<th>Mode</th>
											<th>Transaction Id</th>
											<th>Metal Rate (<?php echo $this->session->userdata('currency_symbol'); ?>)</th>
											<th>Saved Weight(g)</th>
											<th>Fixed Weight(g)</th>
											<th>Paid Amount (<?php echo $this->session->userdata('currency_symbol'); ?>)</th>
											<th>Benefit Weight(g)</th>
											<th>Benefit Amount (<?php echo $this->session->userdata('currency_symbol'); ?>)</th>
											<th>Other benefit Wt(g)</th>
											<th>Ref No</th>
											<th>Status</th>
											<th>Action</th>
											<th>Receipt No.</th>
											<th>Remark</th>
											<th>Employee</th>
					   <th>Agent</th>

											<th>Cost Center</th>
											<th>Paid Through</th>
										</tr>
									</thead>
								</table>
							</div>
						<?php } else { ?>
							<div class="table-responsive">
								<table id="payment_list" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th><label class="checkbox-inline"><input type="checkbox" id="select_recpt" name="select_all" value="all" />All</label></th>
											<th>ID</th>
											<!--<?php if ($this->payment_model->entry_date_settings() == 1) { ?>	
					<th>Entry Date</th>
					<?php } else { ?>
					<th>Payment Date</th>
					<?php } ?>  -->
											<th>Payment Date</th>
											<th>Custom Entry Date</th>
											<?php if ($this->session->userdata('enable_old_sch_acc_no') == 1) { ?>
												<th>Old A/c No</th>
											<?php } ?>
											<th>Customer</th>
											<th>A/c Name</th>
											<th>Scheme code</th>
											<th>Group code</th>
											<th>A/c No</th>
											<th>Mobile</th>
											<th>Total Paid Instal.</th>
											<th>Installment No</th>
											<th>Type</th>
											<th>Mode</th>
											<th>Transaction Id</th>
											<th>Metal Rate (<?php echo $this->session->userdata('currency_symbol'); ?>)</th>
											<th>Metal Weight(g)</th>
											<th>Amount (<?php echo $this->session->userdata('currency_symbol'); ?>)</th>
									<th>Benefit Weight(g)</th>
									<th>Benefit Amount (<?php echo $this->session->userdata('currency_symbol'); ?>)</th>
									<th>Ref No</th>
											<th>Receipt No</th>
											<th>Status</th>
											<th>Action</th>
											<!-- <th>Receipt No.</th>   -->
											<th>Remark</th>
											<th>Employee</th>
					  <th>Agent</th>

											<th>Cost Center</th>
											<th>Paid Through</th>
										</tr>
									</thead>
								</table>
							</div>
						<?php } ?>
						<!-- esakki -->
						<!-- <label>Note:&nbsp;Last 7 days Payment List</label> -->
					</div><!-- /.box-body -->
					<div class="overlay" style="display:none">
						<i class="fa fa-refresh fa-spin"></i>
					</div>
				</div><!-- /.box -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- modal -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Delete Payment</h4>
			</div>
			<div class="modal-body">
				<strong>Are you sure! You want to delete this payment?</strong>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-danger btn-confirm">Delete</a>
				<button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- / modal -->
<!-- modal -->
<div class="modal fade" id="pay_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-yellow">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel" align="center">Transaction Detail</h4>
			</div>
			<div class="modal-body">
				<div class="trans-det"></div>
			</div>
			<!--<div class="modal-footer">
  <div class="col-sm-6 col-sm-offset-3">
  <button type="button" class="btn btn-block btn-warning" data-dismiss="modal">Close</button>
</div>
</div>-->
		</div>
	</div>
</div>
<!-- / modal -->
<!--Model-->
<div class="modal fade" id="edit_payment" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 50%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Edit Payment</h4>
			</div>
			<form id="update_pay_form" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
					</div>
					<div class="edit_payment">
						<div class='row' id="" style="margin-left:1%;">
							<input type="hidden" name="id_customer" id="id_customer" value="" />
							<input type='hidden' name='id_payment' id='id_payment' value="">
							<input type='hidden' name="added_by" id='addedby' value="">
							<input type='hidden' id='scheme_type' value="">
							<input type='hidden' id='flexible_sch_type' value="">
							<input type='hidden' id='prev_pay_mode' name="prev_pay_mode" value="">
							<input type="hidden" id="prev_pay_status" name="prev_pay_status" />
							<input type='hidden' id='id_pay_mode_details' value="">
							<!-- esakki 18-09 -->
							<input type="hidden" id="allow_cash_limit" name="allow_cash_limit" />
							<input type="hidden" id="disable_acc_payments" name="disable_acc_payments" />
							<input type="hidden" id="chit_number" name="chit_number" />
							<div class="col-sm-4">
								<div class="form-group">
									<label for="paymentstatus">Payment Status</label>
									<select class="col-md-12 form-control" id="paymentstatus" name="payment_status">
										<option value="1">Success</option>
										<option value="2">Awaiting</option>
										<option value="3">Failed</option>
										<option value="4">Canceled</option>
										<option value="5">Returned</option>
										<option value="6">Refund</option>
										<option value="7">Pending</option>
									</select>
								</div>
							</div>
							<div class='col-sm-4'>
								<div class="form-group" style="margin-left: -8px;padding-right:15px;">
									<label for="payment_date">Payment Date:</label>
									<input data-date-format="dd-mm-yyyy" class="form-control payment_date" id="payment_date" value="" name="payment_date" type="text" placeholder="Payment Date" readonly>
								</div>
							</div>
							<div class="col-sm-4">
								<div class='form-group'>
									<label for="metal_rate">Metal Rate</label>
									<!--<input class="form-control metal_rate" id="metal_rate" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="metal_rate" value=""  size="30" type="text" placeholder="Metal Rate" />-->
									<input class="form-control metal_rate" id="metal_rate" onchange="update_metal_weight()" name="metal_rate" value="" size="30" type="number" placeholder="Metal Rate" readonly="false" />
								</div>
							</div>
							<div class="col-sm-4">
								<div class='form-group'>
									<label for="metal_weight">Metal Weight</label>
									<input class="form-control metal_weight" id="metal_weight" name="metal_weight" value="" size="30" type="number" placeholder="Metal Weight" readonly="false" />
								</div>
							</div>
							<!--	<div class='col-sm-4'>
			 <div class='form-group'>
				  <label for="payment_ref_no">Payment Ref No</label>
				 <input class="form-control" id="payment_ref_no" name="payment_ref_no" value=""  size="30" type="text" placeholder="Payment Ref No" />
			 </div>
		   </div> -->
							<div class="col-sm-4">
								<div class="form-group">
									<label for="payment_mode">Payment Mode</label>
									<!--	<select class="col-md-12 form-control" id="payment_mode" name="payment_mode">
				</select> -->
									<input type="" class="col-md-12 form-control" id="payment_mode" name="payment_mode" readonly>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group" align="">
									<label for="">Select Employee * </label>
									<select required id="select_employee" class="form-control" required='true'></select>
									<input type="hidden" id="session_uid" value="<?= $this->session->userdata('uid'); ?>">
									<input id="id_employee1" name="generic[id_employee1]" type="hidden" value="" />
								</div>
							</div>
							<!-- <div class='col-sm-4'>
			 <div class='form-group'>
				  <label for="payment_mode">Payment Mode</label>
				 <input class="form-control" id="payment_mode" name="payment_mode" value=""  size="30" type="text" placeholder="Payment Mode" />
			 </div>
		   </div> -->
							<div class="col-md-12">
								<div class="box box-info payment_blk">
									<div class="box-body">
										<div class="row">
											<div class="col-sm-11">
												<div class="box-body">
													<div class="table-responsive">
														<table id="payment_modes" class="table table-bordered table-striped">
															<thead>
															</thead>
															<tbody>
																<tr>
																	<td class="text-right"><b class="custom-label">Payment Amount</b></td>
																	<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>
																	<td>
																		<input type="text" class="form-control" name="payment_amount" id="payment_amt" readonly="true" />
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
																		$cash = ($mode['short_code'] == "CSH" ? '<input class="form-control" id="make_pay_cash" name="cus_pay_mode[cash_payment]" type="number" placeholder="Enter Amount" value=""/>' : '');
																		$card = ($mode['short_code'] == "CC"  ? '<a class="btn bg-olive btn-xs pull-right" id="card_detail_modal" href="#" data-toggle="modal" data-target="#card-detail-modal" ><b>+</b></a> ' : '');
																		$cheque = ($mode['short_code'] == "CHQ"  ? '<a class="btn bg-olive btn-xs pull-right" id="cheque_modal" href="#" data-toggle="modal" data-target="#cheque-detail-modal" ><b>+</b></a> ' : '');
																		$net_banking = ($mode['short_code'] == "NB"  ? '<a id="netbankmodal" class="btn bg-olive btn-xs pull-right"  href="#" data-toggle="modal" data-target="#net_banking_modal" ><b>+</b></a> ' : '');
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
																				<!-- voucher starts here -->
																				<input type="hidden" id="vch_payment" name="cus_pay_mode[vch_pay]" value="">
																				<!-- voucher ends here -->
																				<?php echo $cash; ?>
																				<?php echo $card; ?>
																				<?php echo $cheque; ?>
																				<?php echo $net_banking; ?>
																				<!-- voucher starts here -->
																				<?php echo $voucher; ?>
																				<!-- voucher ends here -->
																			</td>
																		</tr>
																<?php }
																} ?>
																<tr>
																	<td class="text-right">Advance Adj</td>
																	<td class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></td>
																	<td>
																		<span id="tot_adv_adj"></span> <!-- get_edit_advance_details() -->
																		<a class="btn bg-olive btn-xs pull-right" id="adv_adj_modal" onclick="get_edit_advance_details()" href="#" data-toggle="modal" data-target="#adv-adj-confirm-add"><b>+</b></a>
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
							<div class="col-md-12">
								<div class='form-group'>
									<label for="remark">Remark<span class="error">*</span></label>
									<textarea style="width: 100%;height:63px;" class="remark" id="remark" name="remark" placeholder="Remark" value=""></textarea>
								</div>
							</div>
						</div>
					</div><br>
				</div>
				<div class="modal-footer">
					<a href="#" id="update_payment" class="btn btn-success" onclick="" style="padding: 10px 25px;" type='submit' name='update_payment'>Update</a>
					<button type="button" class="btn btn-danger" style="padding: 10px 25px;" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!--Model End-->
<!-- Card Details -->
<div class="modal fade" id="card-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Card Details</h4>
				<input type="hidden" id="cardTypeMode" value="Card">
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<!-- esakki 17-09-->
							<span class="h_amt" style="color:red;">Balance Amount:</span>
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
				<input type="hidden" id="chequeMode" value="CHQ">
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<!-- esakki 17-09-->
							<span class="h_amt" style="color:red;">Balance Amount:</span>
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
								<!-- esakki -->
								<!-- <tr> 
						<td><input id="cheque_datetime" data-date-format="dd-mm-yyyy" class="cheque_date" name="cheque_details[cheque_date][]" type="text" required="true" placeholder="Cheque Date" /></td>
						<td><input name="cheque_details[bank_name][]" type="text" required="true" class="bank_name"></td>
						<td><input name="cheque_details[bank_branch][]" type="text" required="true" class="bank_branch"></td>
						<td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]"/></td> 
						<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]"/></td> 
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
				<h4 class="modal-title" id="myModalLabel">Card Details</h4>
				<input type="hidden" id="netBankMode" value="NB">
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<!-- esakki 17-09-->
							<span class="h_amt" style="color:red;">Balance Amount:</span>
							<span class="f_amt" style="color:red;"><?php echo $this->session->userdata('currency_symbol') ?> </span><span class="modal_balance_amount f_amt" style="color:red;">0</span>
							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_net_bank"><i class="fa fa-user-plus"></i>ADD</button>
							<p class="error "><span id="NetBankAlert"></span></p>
						</div>
					</div>
					<p></p>
					<div class="table-responsive">
						<table id="net_bank_details" class="table table-bordered">
							<thead>
								<tr>
									<!--
						<th>Type</th> 
						<th class="upi_type">Bank</th>
						<th >Payment Date</th>
						<th class="device" style="display:none;">Device</th>
						<th>Ref No</th>  
						<th>Amount</th>  
						<th>Action</th> 
						-->
									<th>Type</th>
									<th>Bank/Device</th>
									<!--<th class="upi_type" style="display:none;">Bank</th>-->
									<!--<th class="device" style="display:none;">Device</th>-->
									<th>Payment Date</th>
									<th>Ref No</th>
									<th>Amount</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<!--	<tr> 
						<td><select name="nb_details[nb_type][]" class="nb_type"><option value=1>RTGS</option><option value=2>IMPS</option><option value=3>UPI</option></select></td>
						<td><select name="nb_details[nb_bank][]" class="id_bank"><option value=1>RTGS</option><option value=2>IMPS</option><option value=3>UPI</option></select></td>
						<td><input type="date" step="any" class="nb_date" name="nb_details[nb_date][]"/></td>
						<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]"/></td> 
						<td><input type="number" step="any" class="amount" name="nb_details[amount][]"/></td> 
						<td><a href="#" oonclick="removeNb_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  
					</tr> -->
							</tbody>
							<tfoot>
								<tr>
									<th colspan=4>Total</th>
									<th colspan=4>
										<span class="nb_total_amount"></span>
									</th>
								</tr>
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
										<!-- esakki 17-09-->
										<span class="h_amt" style="color:red;">Balance Amount:</span>
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
<!-- Voucher modal starts here -->
<div class="modal fade" id="vch-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Voucher Details</h4>
				<input type="hidden" id="vchMode" value="VCH">
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<!-- esakki 17-09-->
							<span class="h_amt" style="color:red;">Balance Amount:</span>
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
<!--modal---remark --start-14-12-23--santhosh-->
<div class="modal fade" id="edit_payment1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 50%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Edit Remark</h4>
			</div>
			<form id="update_pay_form" method="post">
				<div class="modal-body">
					<div class="col-md-12">
						<div class='form-group'>
							<label for="remark">Remark</label>
							<textarea style="width: 100%;height:63px;" class="remark" id="remark1" name="remark" placeholder="Remark" value=""></textarea>
						</div>
					</div><br>
				</div>
				<div class="modal-footer">
					<a href="#" id="update_payment1" class="btn btn-success" onclick="" style="padding: 10px 25px;" type='submit' name='update_payment'>Update</a>
					<button type="button" class="btn btn-danger" style="padding: 10px 25px;" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
</div>
<!--modal---remark --end-14-12-23--santhosh -->
<!-- Voucher modal ends here -->
<script type="text/javascript">
	var showExport = "<?php echo ((($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <= 2) || $this->config->item('showToAdminsOnly') == 0) ? 1 : 0); ?>";
</script>