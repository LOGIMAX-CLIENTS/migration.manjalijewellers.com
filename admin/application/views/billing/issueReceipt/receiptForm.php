  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

  	<!-- Content Header (Page header) -->

  	<section class="content-header">

  		<h1>

  			Receipt

  			<small></small>

  		</h1>

  		<ol class="breadcrumb">

  			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

  			<li><a href="#">Receipt</a></li>

  			<li class="active">Add Receipt</li>

  		</ol>

  	</section>

	  <style>
		.modal_btns {
			display: flex;
			justify-content: space-between;
		}

	</style>


  	<!-- Main content -->

  	<section class="content">



  		<!-- Default box -->

  		<div class="box box-primary">

  			<div class="box-header with-border">

  				<h3 class="box-title">Add Receipt</h3>

  				<div class="box-tools pull-right">

  					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

  					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

  				</div>

  			</div>

  			<div class="box-body">

  				<!-- Alert -->

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

  				<!-- form -->
				<?php
					$metal_rates = $this->admin_settings_model->metal_ratesDB("last");
				?>
  				<form id="receipt_billing">

  					<input id="validate_max_cash" type="hidden" value="<?php echo $settings['validate_cash_amt'] ?>" />

  					<input id="max_cash_amt" type="hidden" value="<?php echo $settings['max_cash_amt'] ?>" />

  					<input id="max_cash_type" type="hidden" value="<?php echo $settings['max_cash_type'] ?>" />

  					<input type="hidden" id="cus_payment_details" value="">

  					<p class="help-block"></p>

  					<div class="row">

  						<div class="col-md-7">

  							<div class="row">

  								<div class="col-md-4">

  									<div class="form-group">

  										<label for="">Select Branch <span class="error"> *</span></label>

  										<select id="branch_select" class="form-control"></select>

  										<input type="hidden" class="form-control " name="receipt[id_branch]" id="id_branch" value="<?php echo $this->session->userdata('id_branch') ?>">
										  <input type="hidden" id="branch_id_country" value="" required="">
											<input type="hidden" id="branch_id_state" value="" required="">
											<input type="hidden" id="branch_id_city" value="" required="">
											<input type="hidden" id="branch_pincode" value="" required="">
											<input type="hidden" id="branch_id_village" value="" required="">

  									</div>

  									<span id="branchAlert" class="error"></span>

  								</div>

  								<div class="col-md-6">

  									<label for="">Customer <span class="error"> *</span></label>
  									<div class="form-group">

  										<div class="input-group ">

  											<input type="text" name="name" id="name" class="form-control" autocomplete="off" placeholder="Enter Name/Mobile">

  											<input type="hidden" class="cus_id" name="receipt[id_customer]" id="id_customer">

  											<input type="hidden" name="receipt[id_karigar]" id="id_karigar">

  											<input type="hidden" name="receipt[id_employee]" id="id_employee">

											<input type="hidden" id="cus_state" name="receipt[cus_state]">
											<input type="hidden" id="cus_country" name="receipt[cus_country]">
											<input type="hidden" id="cus_del_state" name="receipt[cus_state]">
											<input type="hidden" id="cus_del_country" name="receipt[cus_country]">
											<input type="hidden" id="cmp_state" name="">
											<input type="hidden" id="cmp_country" name="" value="<?php echo $settings['cmp_country'];?>">

											<input id="is_eda" type="hidden"  name="receipt[is_eda]" value="1" />
											<input type="hidden" id="allow_previous_rcpt_date" value="<?php echo isset($settings['allow_previous_rcpt_date']) ? $settings['allow_previous_rcpt_date'] : 0; ?>" />
											<input type="hidden" id="allow_bill_type" value="<?php echo set_value('settings[allow_bill_type]',$settings['allow_bill_type']); ?>" />
											<input type="hidden" id="form_secret" name="form_secret"  value="<?php echo get_form_secret_key(); ?>">

  											<span class="input-group-btn">

  												<button type="button" id="add_new_customer" class="btn btn-success"><i class="fa fa-plus"></i></button>

  												<button type="button" data-bs-toggle="offcanvas" data-bs-target="#demo" id="edit_customer" class="btn btn-primary"><i class="fa fa-edit"></i></button>

  											</span>

  										</div>

  									</div>
  								</div>


  							</div>
  							<p></p>

  							<div class="row">
  								<div class="col-md-6">
  									<div class="form-group">
  										<label for="">Receipt Type <span class="error"> *</span></label>
  										<div class="form-group">
											<input type="radio" name="receipt[receipt_type]" id="receipt_type1"
												value="1" checked=""> Credit Collection &nbsp;&nbsp;
											<input type="radio" name="receipt[receipt_type]" id="receipt_type2"
												value="2"> Advance &nbsp;&nbsp;&nbsp;
											<input type="radio" name="receipt[receipt_type]" id="receipt_type6"
												value="6"> Chit Close &nbsp;&nbsp;&nbsp;
											<input type="radio" name="receipt[receipt_type]" id="receipt_type8" value="8" >  Petty Cash Receipt  &nbsp;&nbsp;&nbsp;
											<input type="radio" name="receipt[receipt_type]" id="receipt_type9" value="9" >  Amount Receipt  &nbsp;&nbsp;
  										</div>
  									</div>
  								</div>

  								<div class="col-md-2" id="receipt_to_block" style="display:none;">
  									<div class="form-group">
  										<label for="">Receipt To <span class="error"> *</span></label>
  										<div class="form-group">
  											<input type="radio" name="receipt[receipt_to]" id="receipt_to_emp" class="receipt_to" value="1" checked> Employee &nbsp;&nbsp;<br>
  											<input type="radio" name="receipt[receipt_to]" id="receipt_to_kar" class="receipt_to" value="2"> Karigar &nbsp;&nbsp;
  										</div>
  									</div>
  								</div>
  								<div class="col-md-2">
  									<div class="form-group">
  										<label for="">Against Est <span class="error"> *</span></label>
  										<div class="form-group">
  											<input type="radio" name="receipt[is_aganist_est]" id="is_aganist_est_yes" class="is_aganist_est" value="1" disabled> Yes &nbsp;&nbsp;
  											<input type="radio" name="receipt[is_aganist_est]" id="is_aganist_est_no" class="is_aganist_est" value="0" checked="" disabled> No &nbsp;&nbsp;
  										</div>
  									</div>
  								</div>
  								<div class="col-md-4">
  									<label for="">Esti No. </label>
  									<div class="input-group">
  										<input type="number" name="esti_no" id="esti_no" class="form-control" autocomplete="off" disabled />
  										<span class="input-group-btn">
  											<button type="button" id="est_search" class="btn btn-default btn-flat">Search</button>
  										</span>
  									</div>
  									<span id="searchEstiAlert" style="color: red;"></span>
  								</div>
  							</div><br>

  							<div class="row">

  								<div class="col-md-4">

  									<div class="form-group">

  										<label for="">Receipt As <span class="error"> *</span></label>

  										<div class="form-group">

  											<input type="radio" name="receipt[receipt_as]" id="receipt_as1" value="1" checked=""> Amount &nbsp;&nbsp;

  											<input type="radio" name="receipt[receipt_as]" id="receipt_as2" value="2"> Weight &nbsp;&nbsp;

  										</div>

  									</div>

  								</div>

  								<div class="col-md-4">

  									<div class="form-group">

  										<label for="">Store As <span class="error"> *</span></label>

  										<div class="form-group">

  											<input type="radio" name="receipt[store_receipt_as]" id="store_receipt_as_1" value="1" checked=""> Amount &nbsp;&nbsp;

  											<input type="radio" name="receipt[store_receipt_as]" id="store_receipt_as_2" value="2"> Weight &nbsp;&nbsp;

  										</div>

  									</div>

  								</div>

  								<div class="col-md-4">

  									<div class="form-group">

  										<label for="">Rate Calculation From <span class="error"> *</span></label>

  										<div class="form-group">

  											<input type="radio" name="receipt[rate_calc]" id="store_receipt_as_1" value="1" checked=""> Gold &nbsp;&nbsp;

  											<input type="radio" name="receipt[rate_calc]" id="store_receipt_as_2" value="2"> Silver &nbsp;&nbsp;

  										</div>

  									</div>

  								</div>

  							</div>

  							<div class="row">

  								<div class="col-md-4">

  									<div class="form-group">

  										<label for="">Amount <span class="error"> *</span></label>

  										<div class="form-group">

  											<input type="number" step="any" name="receipt[amount]" id="amount" class="form-control" autocomplete="off" readonly>

  											<input type="hidden" id="multiple_receipt_id" name="receipt[multiple_receipt_id]" value="">

  										</div>

  									</div>

  								</div>

  								<div class="col-md-4">

  									<div class="form-group">

  										<label for="">Weight </label>

  										<div class="form-group">

  											<input type="number" step="any" name="receipt[weight]" id="weight" class="form-control" readonly="">

  										</div>

  									</div>

  								</div>

  								<div class="col-md-4">

  									<div class="form-group">

  										<label for="">Date <span class="error"> *</span></label>

  										<div class="form-group">

  											<input class="form-control" id="receipt_date" data-date-format="dd-mm-yyyy" name="receipt[receipt_date]" value="<?php echo isset($day_close_date) ? $day_close_date : ''; ?>" type="text" placeholder="Choose Date" readonly <?php if (empty($settings['allow_previous_rcpt_date']) || $settings['allow_previous_rcpt_date'] == 0) { ?>style="pointer-events: none; background-color: #eee;"<?php } ?> />

											<input type="hidden" id="goldrate_22ct" name="receipt[goldrate_22ct]" value = "<?php echo $metal_rates['goldrate_22ct']; ?>">

											<input type="hidden" id="goldrate_18ct" name="receipt[goldrate_18ct]" value= "<?php echo $metal_rates['goldrate_18ct']; ?>">

											<input type="hidden" id="silverrate_1gm" name="receipt[silverrate_1gm]" value= "<?php echo $metal_rates['silverrate_1gm']; ?>">

  										</div>

  									</div>

  								</div>


  							</div>

  							<div class="row">

  								<div class="col-md-2">

  									<div class="form-group">

  										<label for="">Employee <span class="error"> *</span></label>

  										<div class="form-group">

  											<select id="emp_select" name="receipt[emp_id]" class="form-control">

  											</select>

  											<!-- <input type="hidden" id="id_employee" > -->

  										</div>

  									</div>

  								</div>

  							</div>

  							<div class="row">

  								<div class="col-md-12">

  									<div class="row">

  										<div class="box-body">

  											<div class="table-responsive">

  												<table id="sales_item_details" class="table table-bordered table-striped text-center">

  													<thead>

  														<tr>

  															<th>Tag</th>

  															<th>Product</th>

  															<th>G.Wt</th>

  															<th>L.Wt</th>

  															<th>N.Wt</th>

  															<th>Wastage(%)</th>

  															<th>Amount</th>

  															<th>Action</th>

  														</tr>

  													</thead>

  													<tbody>



  													</tbody>

  													<tfoot>

  														<tr>
  															<td></td>
  															<td></td>
  															<td></td>
  															<td></td>
  															<td></td>
  															<td></td>
  															<td><span class="total_est_amount"></span></td>
  															<td></td>
  														</tr>

  													</tfoot>

  												</table>

  											</div>

  										</div>

  									</div>

  								</div>

  							</div>



  							<div class="row">

  								<div class="col-md-12">

  									<div class="row">

  										<div class="box-body">

  											<div class="table-responsive" style="display:none;">

  												<table id="purchase_item_details" class="table table-bordered table-striped text-center">

  													<thead>

  														<tr>

  															<th>Purpose</th>

  															<th>Product</th>

  															<th>G.Wt</th>

  															<th>D.Wt</th>

  															<th>S.Wt</th>

  															<th>N.Wt</th>

  															<th>Wastage(%)</th>

  															<th>Wastage.Wt(%)</th>

  															<th>Rate Per Gram</th>

  															<th>Amount</th>

  															<th>Est No</th>

  															<th>Action</th>

  														</tr>

  													</thead>

  													<tbody>

  														<!--<?php if ($this->uri->segment(3) == 'edit') {

																	foreach ($est_other_item['old_matel_details'] as $ikey => $ival) {

																		$net_wt = 0;

																		$other_stone_price = 0;

																		$other_stone_wt = 0;

																		$stone_data = array();

																		$net_wt = $ival['gross_wt'] - ($ival['dust_wt'] + $ival['stone_wt']);

																		foreach ($ival['stone_details'] as $data) {

																			$other_stone_price	+=	$data['price'];

																			$other_stone_wt	+=	$data['wt'];

																			$stone_data[] = array(

																				'bill_item_stone_id' => $data['bill_item_stone_id'],

																				'stone_id'			=> $data['stone_id'],

																				'stone_pcs'			=> $data['pieces'],

																				'stone_wt'			=> $data['wt'],

																				'stone_price'		=> $data['price']

																			);
																		}

																		$stone_details = json_encode($stone_data);

																		echo '<tr id="' . $ikey . '">

									<td><span>' . ($ival['purpose'] == 1 ? 'Cash' : 'Exchange') . '</span></td>

									<td><span>' . ($ival['metal_type'] == 1 ? 'Gold' : 'Silver') . '</span>

										<input type="hidden" class="is_est_details" value="1" name="purchase[is_est_details][]" />

										<input type="hidden" class="item_type" name="purchase[itemtype][]" value="' . $ival['item_type'] . '" />

									    <input type="hidden" class="pur_metal_type"value="' . $ival['metal_type'] . '" name="purchase[metal_type][]"/>

									    <input type="hidden" class="old_metal_sale_id" value="' . $ival['old_metal_sale_id'] . '" name="purchase[old_metal_sale_id][]"/>

										</td>

									<td>-</td>

									<td><input type="number" class="pur_pcs" name="purchase[pcs][]" value="1" /></td>

									<td><span>' . $ival['gross_wt'] . '</span><input type="hidden" class="pur_gross_val" name="purchase[gross][]" value="' . $ival['gross_wt'] . '"/></td>

									<td><span>-</span><input type="hidden" class="pur_less_val" name="purchase[less][]" value="" /></td>

									<td>

										<span>' . $net_wt . '</span>

										<input type="hidden" class="pur_net_val" name="purchase[net][]" value="' . $net_wt . '" />

										<input type="hidden" class="est_old_dust_val" name="purchase[dust_wt][]" value="' . $ival['dust_wt'] . '" />

										<input type="hidden" name="purchase[stone_wt][]" class="est_old_stone_val" value="' . $ival['stone_wt'] . '"/>

									</td>

									<td><span>' . $ival['wastage_percent'] . '</span><input type="hidden" class="pur_wastage" name="purchase[wastage][]" value="' . $ival['wastage_percent'] . '" />

									</td>

									<td><input type="number" class="pur_discount" name="purchase[discount][]" value="' . $ival['bill_discount'] . '" />

									</td>

									<td>

									<a href="#" onClick="create_new_empty_bill_purchase_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details"  name="purchase[stone_details][]" value=' . $stone_details . '><input type="hidden" class="other_stone_price" value="' . $other_stone_price . '" /><input type="hidden" class="other_stone_wt" value="' . $other_stone_wt . '" /><input type="hidden" class="bill_material_price" value=""/>

									</td>

									<td><input type="number" class="bill_amount" name="purchase[billamount][]" value="' . $ival['amount'] . '" step="any" readonly /><input type="hidden" class="bill_rate_per_grm" name="purchase[rate_per_grm][]" value="' . $ival['rate_per_gram'] . '" step="any" readonly /></td>

									<td><span>' . $ival['est_id'] . '</span><input type="hidden" class="pur_est_id" name="purchase[estid][]" value="' . $ival['est_id'] . '" /></td>

									<td>-</td>

									</tr>';
																	}
																} ?>-->

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

  							<div class="row">

  								<div class="col-md-12">

  									<div class="form-group">

  										<label for="">Narration <span class="error"> *</span></label>

  										<div class="form-group">

  											<textarea name="receipt[narration]" id="narration"

  												class="form-control" rows="5" cols="100"> </textarea>

  										</div>

  									</div>

  								</div>

  							</div>

  						</div>

  						<div class="col-sm-5">

  							<div class="box box-info payment_blk">

  								<div class="box-header with-border">

  									<h3 class="box-title">Make Payment</h3>

  									<div class="box-tools pull-right">

  										<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

  									</div>

  								</div>

  								<div class="box-body">

  									<div class="row">

  										<div class="col-sm-12">

  											<div class="box-body">

  												<div class="table-responsive">

  													<table id="payment_modes" class="table table-bordered table-striped">

  														<thead>

  														</thead>

  														<tbody>

  															<tr>

  																<td class="text-right"><b class="custom-label">Receive</b></td>

  																<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>

  																<td>

  																	<input type="number" style="width:180px" class="form-control receive_amount" name="payment[tot_amt_received]" value="0" disabled>

  																</td>

  															</tr>

  															<tr  style="display:none" id="pan">

  																<td class="text-right"><b class="custom-label">PAN No</b></td>

  																<th class="text-right"></th>

  																<td>

  																	<input type="hidden" id="min_pan_amt" value="<?php echo $settings['min_pan_amt']; ?>">

  																	<input type="hidden" id="is_pan_required" value="<?php echo $settings['is_pan_required']; ?>">

  																	<input type="hidden" id="cus_payment_details" value="">

  																	<input type="text" style="width:180px" class="form-control pan_no" name="receipt[pan_no]" id="pan_no" value="<?php echo set_value('payment[pan_no]', isset($payment['pan_no']) ? $payment['pan_no'] : NULL); ?>">

  																</td>

  															</tr>
																<tr style="display:none" id="aadhar">

																	<td class="text-right"><b class="custom-label">Adhaar No</b></td>

																	<th class="text-right"></th>

																	<td>
																		<input type="text" style="width:180px" class="form-control aadhar_no" name="receipt[aadhar_no]" id="aadhar_no" maxLength="14" value="<?php echo set_value('payment[aadhar_no]', isset($payment['aadhar_no']) ? $payment['aadhar_no'] : NULL); ?>">
																	</td>

																</tr>
																<tr style="display:none" id="passport">

																	<td class="text-right"><b class="custom-label">Passport</b></td>

																	<th class="text-right"></th>

																	<td>
																		<input type="text" style="width:180px" class="form-control pp_no" name="receipt[pp_no]" id="pp_no" maxLength="15" value="<?php echo set_value('payment[pp_no]', isset($payment['pp_no']) ? $payment['pp_no'] : NULL); ?>">
																	</td>

																</tr>
																<tr style="display:none" id="license">

																	<td class="text-right"><b class="custom-label">Driving License</b></td>

																	<th class="text-right"></th>

																	<td>
																		<input type="text" style="width:180px" class="form-control driving_license_no" name="receipt[driving_license_no]" id="driving_license_no" maxLength="15" value="<?php echo set_value('payment[driving_license_no]', isset($payment['driving_license_no']) ? $payment['driving_license_no'] : NULL); ?>">
																	</td>

  															</tr>

  															<tr>

  																<td class="text-right"><b class="custom-label">Image</b></td>

  																<th class="text-right"></th>

  																<td>

  																	<input type="file" id="pan_images">

  																	<input type="hidden"

  																		id="panimg" name="receipt[pan_img]">

  																	<div id="pan_preview"></div>

  																</td>

  															</tr>

  															<?php

																$modes = $this->ret_billing_model->get_payModes();

																if (sizeof($modes) > 0) {

																	foreach ($modes as $mode) {

																		$cash = ($mode['short_code'] == "CSH" ? '<input class="form-control cash_pay" style="width:180px" id="make_pay_cash" name="payment[cash_payment]" type="text" placeholder="Enter Amount" value=""/>' : '');

																		$card = ($mode['short_code'] == "CC"  ? '<a class="btn bg-olive btn-xs pull-right" id="card_detail_modal" href="#" data-toggle="modal" ><b>+</b>	<input type="hidden" id="card_payment" name="payment[card_pay]" value=""></a> ' : '');

																		$cheque = ($mode['short_code'] == "CHQ"  ? '<a class="btn bg-olive btn-xs pull-right" id="cheque_modal" href="#" data-toggle="modal" ><b>+</b><input type="hidden" id="chq_payment" name="payment[chq_pay]" value=""></a> ' : '');

																		$net_banking = ($mode['short_code'] == "NB"  ? '<a class="btn bg-olive btn-xs pull-right net_bank"  href="#" data-toggle="modal" id="net_bank_modal"><b>+</b><input type="hidden" id="nb_payment" name="payment[net_bank_pay]" value=""></a> ' : '');

																?>

  																	<tr>

  																		<td class="text-right"><?php echo $mode['mode_name']; ?>

  																		</td>

  																		<td class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></td>

  																		<td class="mode_<?php echo $mode['short_code']; ?>">

  																			<span class="<?php echo $mode['short_code']; ?>"></span>

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

  																	<a class="btn bg-olive btn-xs pull-right advance_adj" id="adv_adj_modal" onclick="get_receipt_advance_details()" href="#" data-toggle="modal"><b>+</b></a>



  																	<input type='hidden' id='advance_muliple_receipt' name="receipt[advance_muliple_receipt]" value="">

  																	<input type="hidden" id="excess_adv_amt" name="adv[excess_adv_amt][]" value="">

  																</td>

  															</tr>







  														</tbody>

  														<tfoot>

  															<tr>

  																<th class="text-right custom-label">Total</th>

  																<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>

  																<th class="receipt_total_amount"></th>



  															</tr>

  															<tr>

  																<th class="text-right custom-label">Balance</th>

  																<th class="text-right"><?php echo $this->session->userdata('currency_symbol') ?></th>

  																<th class="receipt_bal_amount"></th>

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

  				</form>

  				<p class="help-block"></p>

  				<!--End of row-->

  			</div>

  			<div class="box-footer clearfix">

  				<div class="row">

  					<div class="col-xs-offset-5">

  						<button id="save_receipt" type="submit" class="btn btn-primary btn-flat" disabled=""><i class="fa fa-plus"></i> Save</button>

  						<button type="button" class="btn btn-default btn-flat btn-cancel">Back</button>

  					</div> <br />

  				</div>

  			</div>

  			<div class="overlay" style="display:none">

  				<i class="fa fa-refresh fa-spin"></i>

  			</div>

  		</div>

  	</section>

  </div>





  <!-- Card Details -->

  <div class="modal fade" id="card-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  	<div class="modal-dialog" style="width:60%;">

  		<div class="modal-content">

  			<div class="modal-header">

			  	<button type="button" class="card_close_btn close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
  				<!-- <button type="button" style="display:none;" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->

  				<h4 class="modal-title" id="myModalLabel">Card Details</h4>

  			</div>

  			<div class="modal-body">

  				<div class="box-body">

  					<div class="row">

  						<div class="col-sm-12 pull-right">

  							<span style="color:red;">BALANCE AMOUNT:</span>

  							<span class="receipt_balance_amount" style="color:red;">0</span>


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

								<th>Device</th>

								<th>Card No</th>

								<th>Amount</th>

								<th>Approval No</th>

								<th>Action</th> -->
  									<th width="12%">Card Name</th>
									<th width="10%">Type</th> 
									<th width="18%">Device<span class="error">*</span></th> 
									<th width="10%">Date</th>
  									<th width="20%">Card No</th>
									<th width="15%">Amount</th> 
									<th width="15%">Approval No</th> 
									<th width="10%">Action</th>

  								</tr>

  							</thead>

  							<tbody>

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

  				<a href="#" id="add_card" class="btn btn-success">Save & Close</a>

				<button type="button" class="btn btn-close btn-warning card_close_btn" data-dismiss="modal">Close</button>
  				<!-- <button type="button" style="display:none;" class="btn btn-close btn-warning" data-dismiss="modal">Close</button> -->

  			</div>

  		</div>

  	</div>

  </div>

  <!-- Card Details -->



  <div class="modal fade" id="cheque-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  	<div class="modal-dialog" style="width:60%;">

  		<div class="modal-content">

  			<div class="modal-header">

			  	<button type="button" class="chq_close_btn close chq_close_btn" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

  				<!-- <button type="button" style="display:none;" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->

  				<h4 class="modal-title" id="myModalLabel">Cheque Details</h4>

  			</div>

  			<div class="modal-body">

  				<div class="box-body">

  					<div class="row">

  						<div class="col-sm-12 pull-right">

  							<span style="color:red;">BALANCE AMOUNT:</span>

  							<span class="receipt_balance_amount" style="color:red;">0</span>


  							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_chq"><i class="fa fa-user-plus"></i>ADD</button>

  							<p class="error "><span id="chqPayAlert"></span></p>

  						</div>

  					</div>

  					<p></p>

  					<div class="table-responsive">

  						<table id="chq_details" class="table table-bordered">

  							<thead>

  								<tr>
									<!-- Kanaga Sundar -->
  									<!-- <th>Cheque Date</th>

								<th>Bank</th>

								<th>Branch</th>

								<th>Cheque No</th>

								<th>IFSC Code</th>

								<th>Amount</th>

								<th>Action</th> -->
  									<th width="20%">Cheque Date</th>
  									<th width="20%">Bank</th>
  									<th width="25%">Cheque No</th>
									<th width="25%">Amount</th>
									<th width="10%">Action</th>

  								</tr>

  							</thead>

  							<tbody>

  								<!-- <tr>

								<td><input id="cheque_datetime" data-date-format="dd-mm-yyyy hh:mm:ss" class="cheque_date" name="cheque_details[cheque_date][]" type="text"  placeholder="Cheque Date" /></td>

								<td><input name="cheque_details[bank_name][]" type="text"  class="bank_name"></td>

								<td><input name="cheque_details[bank_branch][]" type="text" class="bank_branch"></td>

								<td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]"/></td>

								<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]"/></td>

								<td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]"/></td>



								<td><a href="#" onclick="removeCC_row($(this).closest('tr')) ;" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

							</tr> -->

  							</tbody>

  							<tfoot>

  								<tr>

  									<!-- <td>Total</td>
  									<td></td>
  									<td></td>
  									<td></td>
  									<td></td>
  									<td><span class="chq_total_amount"></span></td>
  									<td></td> -->
  									<td colspan="2">Total</td>
  									<td colspan="3"><span class="chq_total_amount"></span></td>

  								</tr>

  							</tfoot>

  						</table>

  					</div>

  				</div>

  			</div>

  			<div class="modal-footer">

  				<a href="#" id="save_chq" class="btn btn-success">Save & Close</a>

				<button type="button" class="btn btn-close btn-warning chq_close_btn" data-dismiss="modal">Close</button>
  				<!-- <button type="button" style="display:none;" class="btn btn-close btn-warning" data-dismiss="modal">Close</button> -->

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

			<button type="button" class="NB_close_btn close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

			<!-- <button type="button" style="display:none;" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->

  				<h4 class="modal-title" id="myModalLabel">Net Banking Details</h4>

  			</div>

  			<div class="modal-body">

  				<div class="box-body">

  					<div class="row">

  						<div class="col-sm-12 pull-right">

  							<span style="color:red;">BALANCE AMOUNT:</span>

  							<span class="receipt_balance_amount" style="color:red;">0</span>


  							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_net_bank"><i class="fa fa-user-plus"></i>ADD</button>

  							<p class="error "><span id="NetBankAlert"></span></p>

  						</div>

  					</div>

  					<p></p>

  					<div class="table-responsive">
  						<table id="net_bank_details" class="table table-bordered">
  							<thead>
  								<tr>
							<!-- <th width="15%">Type</th>
  									<th width="20%">Bank/Device</th>
  									<th width="20%">Payment Date</th>
  									<th width="20%">Ref No</th>
  									<th width="20%">Amount</th>
							<th width="5%">Action</th> -->
							<th width="15%">Type</th>
							<th width="15%">Bank/Device</th> 
							<th width="20%">Payment Date</th> 
							<th width="20%">Ref No</th>  
							<th width="20%">Amount</th>  
							<th width="10%">Action</th>
  								</tr>

  							</thead>

  							<tbody>

  							</tbody>

  							<tfoot>

  								<tr>

							<th  colspan=2>Total</th>

  									<th colspan=2>

  										<span class="nb_total_amount"></span>

  									</th>

  								</tr>

  							</tfoot>

  						</table>

  					</div>

  				</div>

  			</div>

  			<div class="modal-footer">

		<a href="#" id="save_net_banking" class="btn btn-success" data-dismiss="modal">Save & Close</a>

		<button type="button" class="btn btn-close btn-warning NB_close_btn" data-dismiss="modal">Close</button>

		<!-- <button type="button" style="display:none;" class="btn btn-close btn-warning" data-dismiss="modal">Close</button> -->

  			</div>

  		</div>

  	</div>

  </div>

  <!-- Net Banking-->



  <!-- Receipt Refund modal -->

  <div class="modal fade" id="credit_collection" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  	<div class="modal-dialog">

  		<div class="modal-content">

  			<div class="modal-header">



  				<!-- <button type="button" style="display:none;" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span> -->
				
				<span class="sr-only">Close</span></button>

  				<h4 class="modal-title" id="myModalLabel">CREDIT DETAILS</h4>

  			</div>

  			<div class="modal-body">

  				<div class="box-body chit_details">

  					<div class="row">

  						<div class="box-body">

  							<div class="table-responsive">

  								<table id="issue_list" class="table table-bordered text-center">

  									<thead>

  										<tr>

  											<th width="5%;">#</th>

  											<th width="10%;">Bill No</th>

  											<th width="10%;">Issue</th>

  											<th width="10%;">Paid</th>

  											<th width="10%;">Balance</th>

  											<th class="credit_discount" width="10%;">Discount</th>

  											<th width="10%;">Received</th>

  										</tr>

  									</thead>

  									<tbody>

  									</tbody>



  								</table>

  							</div>

  						</div>

  					</div>

  				</div>

  			</div>

  			<div class="modal-footer">

  				<a href="#" id="save_credit_collection" class="btn btn-success">Save</a>

  				<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal" id="close_receipt_refund">Close</button>

  			</div>

  		</div>

  	</div>

  </div>

  <!-- / Receipt Refund Modal -->







  <!-- Advance Adj -->

  <div class="modal fade" id="adv-adj-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  	<div class="modal-dialog">

  		<div class="modal-content">

  			<div class="modal-header">

  				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

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

  				<button type="button" class="btn btn-close btn-warning" data-bs-dismiss="modal" id="close_add_adj">Close</button>

  			</div>

  		</div>

  	</div>

  </div>

  <!-- Advance Adj -->





  <!-- <div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

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
				<div class="tab-content"><br/>
					<div class="tab-pane active" id="tab_general">
						<div class="row">

							<div class="form-group">

							<label for="" class="col-md-3 col-md-offset-1 ">Customer Type<span class="error"></span></label>

								<div class="col-md-6">

								<input type="radio" id="cus_type1"  name="cus[cus_type]" value="1" class="minimal" checked/> Individual

								<input type="radio" id="cus_type2"  name="cus[cus_type]" value="2" class="minimal" /> Business

							</div>

							</div>

						</div></br>



						<div class="row">

							<div class="form-group">

							<label for="cus_first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error">*</span></label>

							<div class="col-md-6">

									<input type="text" class="form-control" id="cus_first_name" name="cus[first_name]" placeholder="Enter customer first name" required="true">



									<p class="help-block cus_first_name error"></p>

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

								<select class="form-control" id="city"  style="width:100%;"></select>

								<input type="hidden" name="cus[id_city]" id="id_city">

							</div>



							</div>

						</div></br>



						<div class="row">

							<div class="form-group">

							<label for="" class="col-md-3 col-md-offset-1 ">Select Area</label>

							<div class="col-md-6">

								<select class="form-control" id="sel_village" style="width:100%;"></select>

									<input type="hidden" name="cus[id_village]" id="id_village" name="">

									<p class="help-block sel_village error"></p>

							</div>

							</div>

						</div></br>



						<div class="row">

							<div class="form-group">

								<label for="address1" class="col-md-3 col-md-offset-1 ">Address1<span class="error">*</span></label>

								<div class="col-md-6">

										<input class="form-control" id="address1" name="customer[address1]" value=""  type="text" placeholder="Enter Address Here 1" required />

										<p class="help-block address1 error"></p>

									</div>

							</div>

						</div></br>

						<div class="row">

							<div class="form-group">

								<label for="address2" class="col-md-3 col-md-offset-1">Address2</label>

								<div class="col-md-6">

										<input class="form-control" id="address2" name="customer[address2]" placeholder="Enter Address Here 2" value=""  type="text" />

									</div>

							</div>

						</div></br>

						<div class="row">

							<div class="form-group">

								<label for="address3" class="col-md-3 col-md-offset-1">Address3</label>

								<div class="col-md-6">

										<input class="form-control titlecase" id="address3" name="customer[address3]" value=""  type="text" placeholder="Enter Address Here 3" />

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



						<div class="row gst" style="display:none;">

							<div class="form-group">

							<label for="" class="col-md-3 col-md-offset-1 ">GST No<span class="error"></span></label>

							<div class="col-md-6">

									<input type="text" class="form-control" id="gst_no" name="cus[gst_no]" placeholder="Enter GST No">

									<p class="help-block cus_mobile"></p>

							</div>

							</div>

						</div>

						<div class="row" >

							<div class="form-group">

							<label for="" class="col-md-3 col-md-offset-1 ">Upload Image<span class="error">*</span></label>

							<div class="col-md-6">

									<input id="cus_image" name="cus_img" accept="image/*" type="file" >

									<p class="help-block cus_mobile"></p>

									<img src="<?php echo base_url('assets/img/default.png') ?>" class="img-thumbnail" id="cus_img_preview" style="width:175px;height:100%;" alt="Customer image">

													<input type="hidden" name="customer[customer_img]" value="<?php echo set_value('customer[customer_img]', $customer['cus_img']) ?>" />

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