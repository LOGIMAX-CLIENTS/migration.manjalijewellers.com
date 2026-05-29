  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Issue

        <small></small>

      </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li><a href="#">Issue</a></li>

        <li class="active">Branch Transfer</li>

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

          <h3 class="box-title">Add Issue</h3>

          <div class="box-tools pull-right">

            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

          </div>

        </div>

        <div class="box-body">

            <!-- Alert -->

			<?php

				if($this->session->flashdata('chit_alert'))

				 {

					$message = $this->session->flashdata('chit_alert');

			?>

				   <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

					<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

					<?php echo $message['message']; ?>

				  </div>

			<?php } ?>

             <!-- form -->

			<?php  echo form_open_multipart('admin_ret_billing/issue/save'); ?>

			<p class="help-block"></p>

			<div class="row">

				<div class="col-md-8">

					<div class="row">

						<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

								<div class="col-md-2">

									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_select" name="issue[id_branch]" class="form-control branch_filter" required><span class="error"> *</span></select>

										<input type="hidden" id="id_branch" value="">

									</div>

								</div>

								<?php }else{?>

									<input type="hidden" id="branch_filter" name="issue[id_branch]"  value="<?php echo $this->session->userdata('id_branch') ?>">


									<input type="hidden" id="id_branch" value="<?php echo $this->session->userdata('id_branch') ?>">

								<?php }?>

						<div class="col-md-3">

							<div class="form-group tagged">

			                  <label>Select Issue To <span class="error"> *</span></label>

			                      <select name="issue[issue_to]" id="issue_to" class="form-control select2">
									  <option value=""></option>
									  <option value="1">Employee</option>
									  <option value="2">Customer</option>
									  <option value="4">Karigar</option>
								  </select>
			                      <input type="hidden" id="id_ret_wallet" name="issue[id_ret_wallet]">
			                      <input type="hidden" id="actual_amt">
			                      <input id="is_eda" type="hidden"  name="issue[is_eda]" value="1" />
                                  <input type="hidden" id="allow_bill_type" value="<?php echo set_value('settings[allow_bill_type]',$settings['allow_bill_type']); ?>" />

			                </div>

						</div>

						<div class="col-md-3">

							<div class="form-group tagged">

			                  <label>Select Issue Type <span class="error"> *</span></label>

			                      <select name="issue[issue_type]" id="issue_type" class="form-control select2">
									  <option value=""></option>
									  <option value="1">Petty Cash</option>
									  <option value="2">Credit</option>
									  <option value="3">Refund</option>
									  <option value="5">Expenses</option>
									  <option value="6">Non Jewellery Expense</option>
								  </select>

			                </div>

						</div>

						<div class="col-md-2" class="acc_block">

							<div class="form-group">

			                  <label for="">Account Head <span class="error"> *</span></label>

			                  <div class="form-group">

			                      <select name="acc_head"  id="acc_head" class="form-control" disabled="">

			                      </select>

			                      <input type="hidden" id="id_acc_head" name="issue[id_acc_head]">

				              </div>

			                </div>

						</div>

					</div>

					<div class="row">

						<div class="col-md-4">

							<div class="form-group">

			                  <label for="">Name <span class="error"> *</span></label>

			                  <input type="text" id="name" name="issue[name]" class="form-control" autocomplete="off" placeholder="Enter Name/Mobile">

			                  <input type="hidden" class="cus_id" name="issue[id_customer]" id="id_customer">

			                  <input type="hidden" name="issue[id_employee]" id="id_employee">

							  <input type="hidden" name="issue[id_karigar]" id="id_karigar">

			                  <input type="hidden" name="issue[barrower_name]" id="barrower_name">

							  <input type="hidden" id="cus_state" name="issue[cus_state]">
								<input type="hidden" id="cus_country" name="issue[cus_country]">
								<input type="hidden" id="cus_del_state" name="issue[cus_state]">
								<input type="hidden" id="cus_del_country" name="issue[cus_country]">
								<input type="hidden" id="cmp_state" name="">
								<input type="hidden" id="cmp_country" name="" value="<?php echo $settings['cmp_country'];?>">

			                </div>

			                <span id="customerAlert"></span>

						</div>

						<div class="col-md-4">

							<div class="form-group">

			                  <label for="">Mobile <span class="error"> *</span></label>

			                  <input type="number" name="issue[mobile]" id="mobile" class="form-control" autocomplete="off" readonly required="true">

			                </div>

						</div>

						<div class="col-md-4">

							<div class="form-group">

			                  <label for="">Amount <span class="error"> *</span></label>

			                  <div class="form-group">

			                      <input type="number" name="issue[amount]" id="issue_amount" class="form-control" autocomplete="off" >

			                       <input type="hidden" id="multiple_receipt_id" name="issue[multiple_receipt_id]" value="">

								   <input id="max_return_amt" type="hidden" value="<?php echo $settings['max_return_amt'] ?>" />

				              </div>

			                </div>

						</div>

					</div>

					<div class="row">

						<div class="col-md-4">

							<div class="form-group">

			                  <label for="">Reference No</label>

			                  <input type="text" id="refno" name="issue[refno]" class="form-control" autocomplete="off" placeholder="Enter Reference no.">

			                </div>

			                <span id="customerAlert"></span>

						</div>

						<div class="col-md-4" id="petty_cash_block" style="display:none">
							<div class="form-group">
			                  <label for="">Petty cash Amount</label>
			                  <input type="text" id="pty_csh" name="issue[petty_cash_amt]" class="form-control" autocomplete="off" readonly>
			                </div>
			                <span id="customerAlert"></span>
						</div>




						<div class="col-md-2" class="acc_block">

							<div class="form-group">

			                  <label for="">Employee <span class="error"> *</span></label>

			                  <div class="form-group">

			                      <select id="emp_select" name="issue[emp_id]" class="form-control" required>

			                      </select>

			                      <!-- <input type="hidden" id="id_employee" > -->

				              </div>

			                </div>

						</div>



					</div>

					<div class="row">

						<div class="col-md-12">

							<div class="form-group">

			                  <label for="">Narration</label>

			                  <div class="form-group">

			                      <textarea name="issue[narration]" id="narration" class="form-control" rows="5" cols="100" required> </textarea>

				              </div>

			                </div>

						</div>

					</div>

				</div>

				<div class="col-sm-4">

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

										 <table id="payment_modes" class="table table-bordered table-striped">

											<thead>

											</thead>

											<tbody>

												<tr>

													<td class="text-right"><b class="custom-label">Pay</b></td>

													<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>

													<td>

														<input type="number" class="form-control pay_to_cus" name="payment[pay_to_cus]" value="" required readonly>

													</td>

												</tr>

												<tr style="display:none" id="pan">

													<td class="text-right"><b class="custom-label">PAN No</b></td>

													<th class="text-right"></th>

													<td>

													<input type="hidden" id="min_pan_amt" value="<?php echo $settings['min_pan_amt'];?>">

													<input type="hidden" id="is_pan_required" value="<?php echo $settings['is_pan_required'];?>">

													<input type="hidden" id="cus_payment_details" value="">

													<input type="text" style="width:300px" class="form-control pan_no" name="issue[pan_no]" id="pan_no" value="<?php echo set_value('payment[pan_no]',isset($payment['pan_no'])?$payment['pan_no']:NULL); ?>">

													</td>

												</tr>
												<tr style="display:none" id="aadhar">

													<td class="text-right"><b class="custom-label">Adhaar No</b></td>

													<th class="text-right"></th>

													<td>
														<input type="text" style="width:300px" class="form-control aadhar_no" name="issue[aadhar_no]" id="aadhar_no" maxLength="14" value="<?php echo set_value('payment[aadhar_no]', isset($payment['aadhar_no']) ? $payment['aadhar_no'] : NULL); ?>">
													</td>

												</tr>
												<tr style="display:none" id="passport">

													<td class="text-right"><b class="custom-label">Passport</b></td>

													<th class="text-right"></th>

													<td>
														<input type="text" style="width:300px" class="form-control pp_no" name="issue[pp_no]" id="pp_no" maxLength="15" value="<?php echo set_value('payment[pp_no]', isset($payment['pp_no']) ? $payment['pp_no'] : NULL); ?>">
													</td>

												</tr>
												<tr style="display:none" id="license">

													<td class="text-right"><b class="custom-label">Driving License</b></td>

													<th class="text-right"></th>

													<td>
														<input type="text" style="width:300px" class="form-control driving_license_no" name="issue[driving_license_no]" id="driving_license_no" maxLength="15" value="<?php echo set_value('payment[driving_license_no]', isset($payment['driving_license_no']) ? $payment['driving_license_no'] : NULL); ?>">
													</td>

												</tr>

												<!--<tr>

													<td class="text-right">Credit Due Date</td>

													<td></td>

													<td>

														<input class="form-control" id="credit_due_date" data-date-format="dd-mm-yyyy hh:mm:ss" name="payment[credit_due_date]" value="" type="text" placeholder="Credit Due Date" disabled/>

													</td>

												</tr>-->

												<?php

												$modes = $this->ret_billing_model->get_payModes();

												if(sizeof($modes)>0){

												foreach($modes as $mode){

													$cash = ($mode['short_code'] == "CSH" ? '<input class="form-control" id="cash_pay" name="payment[cash_payment]" type="text" placeholder="Enter Amount" value=""/>' : '');

													$card = ($mode['short_code'] == "CC" || $mode['short_code'] == "DC" ? '<a class="btn bg-olive btn-xs pull-right card disabled" id="card_detail_modal" href="javascript:void(0);" disabled><b>+</b><input type="hidden" id="card_payment" name="payment[card_pay]" value=""></a> ' : '');

													$cheque = ($mode['short_code'] == "CHQ"  ? '<a class="btn bg-olive btn-xs pull-right" id="cheque_modal" href="#" data-toggle="modal"  ><b>+</b><input type="hidden" id="chq_payment" name="payment[chq_pay]" value=""></a> ' : '');

													$net_banking = ($mode['short_code'] == "NB"  ? '<a class="btn bg-olive btn-xs pull-right net_bank"  href="#" data-toggle="modal" id="net_bank_modal" ><b>+</b><input type="hidden" id="nb_payment" name="payment[net_bank_pay]" value=""></a> ' : '');

												?>

												<tr>

													<td class="text-right"><?php echo $mode['mode_name']; ?>

													</td>

													<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

													<td class="mode_<?php echo $mode['short_code']; ?>">

														<span class="<?php echo $mode['short_code'];?>"></span>







													<?php echo $cash; ?>

													<?php echo $card; ?>

													<?php echo $cheque; ?>

													<?php echo $net_banking; ?>

													</td>

												</tr>

												<?php }}?>

											</tbody>

											<tfoot>

												<tr>

													<th class="text-right custom-label">Total</th>

													<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>

													<th class="total_issue_amt"></th>

												</tr>

												<tr>

													<th class="text-right custom-label">Balance</th>

													<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>

													<th class="issue_bal_amount"></th>

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

			<p class="help-block"></p>

			<!--End of row-->

		</div>

		<div class="box-footer clearfix">

			<div class="row">

				<div class="col-xs-offset-5">

					<button id="save_issue" type="submit" class="btn btn-primary btn-flat" disabled=""><i class="fa fa-plus" ></i> Save</button>

					<button type="button" class="btn btn-default btn-flat btn-cancel">Back</button>

				</div> <br/>

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

				<button type="button" style="display:none;" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Card Details</h4>

			</div>

			<div class="modal-body">

				<div class="box-body">

					<div class="row">

						<div class="col-sm-12 pull-right">


						<span style="color:red;">BALANCE AMOUNT:</span>

                           <span class="issue_balance_amount" style="color:red;" >0</span>

							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_card"><i class="fa fa-user-plus"></i>ADD</button>

							<p class="error "><span id="cardPayAlert"></span></p>

						</div>

					</div>

					<p></p>

				   <div class="table-responsive">

					 <table id="card_details" class="table table-bordered">

						<thead>

							<tr>

								<!-- <th>Card Name</th>
								<th>Type</th>
								<th>Card No</th>
								<th>Amount</th>
								<th>Approval No</th>
								<th>Action</th> -->
								<th width="12%">Card Name</th>
								<th width="5%">Type</th> 
								<th width="15%">Device<span class="error">*</span></th> 
								<th width="23%">Card No</th> 
								<th width="20%">Amount</th> 
								<th width="20%">Approval No</th> 
								<th width="5%">Action</th>
							</tr>

						</thead>

						<tbody>

							<?php if($this->uri->segment(3) == 'edit'){

								/*foreach($est_other_item['card_details'] as $ikey => $ival){

										echo '<tr><td><input class="card_name" type="number" name="card_details[card_name][]" value="'.$ival['card_name'].'" /></td><td><input class="card_type" type="number" name="card_details[card_type][]" value="'.$ival['card_type'].'" /></td><td><input type="number" class="card_no" style="width: 100px;"  name="card_details[card_no][]" value="'.$ival['card_no'].'"  /></td><td><input type="number" class="card_amt" style="width: 100px;"  name="card_details[card_amt][]" value="'.$ival['card_amt'].'"  /></td><td>-</td></tr>';

								}*/

							}else{ ?>

							<tr>

								<td><select name="card_details[card_name][]" class="form-control card_name"><option value=2>VISA</option><option value=2>RuPay</option><option value=3>Mastro</option><option value=4>Master</option></select></td>

								<td><select name="card_details[card_type][]" class="form-control card_type"><option value=1>CC</option><option value=2>DC</option></select></td>

								<td><input type="number" step="any" class="form-control card_no" name="card_details[card_no][]"/></td>

								<td><input type="number" step="any" class="form-control card_amt" name="card_details[card_amt][]"/></td>

								<td><input type="number" step="any" class="form-control ref_no" name="card_details[ref_no][]"/></td>



								<td><a href="#" onclick="removeCC_row($(this).closest('tr')) ;" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

							</tr>

							<?php } ?>

						</tbody>

						<tfoot>

							<tr>

								<th  colspan=3>Total</th>

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

			<a href="#" id="add_issue_card" class="btn btn-success">Save & Close</a>

			<button type="button" style="display:none;" class="btn btn-warning" data-dismiss="modal">Close</button>

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

                          <span class="issue_balance_amount" style="color:red;" >0</span>

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
							<!-- Kanaga Sundar -->
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

								<!-- <td>Total</td><td></td><td></td><td></td><td></td><td><span class="chq_total_amount"></span></td><td></td> -->
								<td colspan="2">Total</td>
								<td colspan="3"><span class="chq_total_amount"></span></td>

							</tr>

						</tfoot>

					 </table>

				  </div>

				</div>

			</div>

		  <div class="modal-footer">

			<a href="#" id="save_issue_chq" class="btn btn-success">Save & Close</a>

			<button type="button" class="btn btn-close btn-warning chq_close_btn" data-dismiss="modal">Close</button>
			<!-- <button type="button" style="display:none;" class="btn btn-warning" data-dismiss="modal">Close</button> -->

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
				<!-- <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> -->

				<h4 class="modal-title" id="myModalLabel">Net Banking Details</h4>

			</div>

			<div class="modal-body">

				<div class="box-body">

					<div class="row">

						<div class="col-sm-12 pull-right">

						<span style="color:red;">BALANCE AMOUNT:</span>

                           <span class="issue_balance_amount" style="color:red;" >0</span>

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

								<th class="upi_type"  >Bank</th>

								<th>Payment Date</th>

								<th class="device" style="display:none">Device</th>

								<th>Ref No</th>

								<th>Amount</th>

								<th>Action</th> -->
								<th width="15%">Type</th>
								<th width="15%">Bank/Device</th> 
								<th width="20%">Payment Date</th> 
								<th width="20%">Ref No</th>  
								<th width="20%">Amount</th>  
								<th width="10%">Action</th>

							</tr>

						</thead>

						<tbody>

						<!--	<tr>

								<td><select name="nb_details[nb_type][]" class="nb_type"><option value=1>RTGS</option><option value=2>IMPS</option></select></td>

								<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]"/></td>

								<td><input type="number" step="any" class="amount" name="nb_details[amount][]"/></td>

								<td><a href="#" onClick="removeChq_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

							</tr> -->

						</tbody>

						<tfoot>

							<tr>

								<th  colspan=3>Total</th>

								<th colspan=3>

									<span class="nb_total_amount"></span>

								</th>

							</tr>

						</tfoot>

					 </table>

				  </div>

				</div>

			</div>

		  <div class="modal-footer">

			<a href="#" id="save_issue_net_banking" class="btn btn-success">Save & Close</a>

			<button type="button" class="btn btn-close btn-warning NB_close_btn" data-dismiss="modal">Close</button>

			<!-- <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button> -->

		  </div>

		</div>

	</div>

</div>

<!-- Net Banking-->



 <!-- Receipt Refund modal -->

  <div class="modal fade" id="receipt_refund" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      <div class="modal-dialog">

          <div class="modal-content">

              <div class="modal-header">

                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span

                          class="sr-only">Close</span></button>

                  <h4 class="modal-title" id="myModalLabel">Receipt Refund</h4>

              </div>

              <div class="modal-body">

                  <div class="box-body chit_details">

                      <div class="row">

                          <div class="box-body">

                              <div class="table-responsive">

                                  <div class="col-md-8">



                                  </div>

                                  <table id="refund_list" class="table table-bordered text-center">

                                      <thead>

                                          <tr>

                                              <th width="5%;">Select</th>

                                              <th width="10%;">Bill No</th>

                                              <th width="10%;">Advance Amount</th>

                                              <th width="10%;">Issue Amount</th>

                                          </tr>

                                      </thead>

                                      <tbody>

                                      </tbody>

                                      <tfoot>

                                          <tr>

                                              <td colspan="3">Total</td>

                                              <td><span class="total_refund_amt"></span></td>

                                          </tr>

                                      </tfoot>



                                  </table>

                              </div>

                          </div>

                      </div>

                  </div>

              </div>

              <div class="modal-footer">

                  <a href="#" id="add_receipt_refund" class="btn btn-success">Save</a>

                  <button type="button" class="btn btn-warning" data-dismiss="modal"

                      id="close_receipt_refund">Close</button>

              </div>

          </div>

      </div>

  </div>

  <!-- / Receipt Refund Modal -->


  <!-- Account Head-->

<div class="modal fade" id="account_head_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog" style="width:40%;">

	<div class="modal-content">

		<div class="modal-header">

			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

			<h4 class="modal-title" id="myModalLabel">Account Head Details</h4>

		</div>

		<div class="modal-body">

			<div class="box-body">

				<div class="row">

					<div class="col-sm-12 pull-right">

						<button type="button" class="btn bg-olive btn-sm pull-right" id="new_account_head"><i class="fa fa-user-plus"></i>ADD</button>

						<p class="error "><span id="AccountAlert"></span></p>

					</div>

				</div>

				<p></p>

			   <div class="table-responsive">

				 <table id="account_head_details" class="table table-bordered">

					<thead>

						<tr>

							<th>Account Head</th>

							<!-- <th class="upi_type"  >Bank</th>

							<th>Payment Date</th>

							<th class="device" style="display:none">Device</th>

							<th>Ref No</th>   -->

							<th>Amount</th>

							<th>Action</th>

						</tr>

					</thead>

					<tbody>


					</tbody>

					<tfoot>

						<tr>

							<td>Total</td>
						   <td class="ah_total_amount"></td>
							<td></td>

						</tr>

					</tfoot>

				 </table>

			  </div>

			</div>

		</div>

	  <div class="modal-footer">

		<a href="#" id="save_issue_account" class="btn btn-success">Save</a>

		<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

	  </div>

	</div>

</div>

</div>

<!-- Account Head-->