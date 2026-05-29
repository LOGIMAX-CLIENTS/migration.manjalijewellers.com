<!-- Content Wrapper. Contains page content -->

<style>

	.remove-btn {

		margin-top: -168px;

		margin-left: -38px;

		background-color: #e51712 !important;

		border: none;

		color: white !important;

	}

	.sm {

		font-weight: normal;

	}

	.err{

		color:red;

		font-size: 10px;

	}

</style>

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<section class="content-header">

		<h1>

			Inventory

			<small>Stock Issue</small>

		</h1>

		<ol class="breadcrumb">

			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

			<li><a href="#">Inventory</a></li>

			<li class="active">Stock Issue</li>

		</ol>

	</section>



	<!-- Main content -->

	<section class="content order">



		<!-- Default box -->

		<div class="box box-primary">



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

				<!-- form -->

				<form id="stock_issue_form">

					<div class="row">

						<div class="col-sm-12">

							<div class="row">

								<input type="hidden" id="form_secret" name="form_secret" value="<?php echo get_form_secret_key(); ?>">

								<div class="col-md-2">

									<div class="form-group">

										<label>Type</label>

										<div class="form-group">

											<input type="radio" id="type_issue" name="order[issue_receipt_type]" value="1" checked><label for="type_issue"> Issue </label>

											&nbsp;&nbsp;&nbsp;

											<input type="radio" id="type_receipt" name="order[issue_receipt_type]" value="2"><label for="type_receipt"> Receipt </label>

										</div>

									</div>

								</div>



								



								<div class="col-md-2 branch">

									<label>Issue From <span class="error">*</span> </label>

									<div class="form-group">

										<?php if ($this->session->userdata('id_branch') == '') { ?>

											<select id="branch_select" class="form-control order_from" required style="width:100%;"></select>

											<input type="hidden" name="order[order_from]" id="id_branch" value="1" required="">

										<?php } else { ?>

											<select id="branch_select" class="form-control order_from" disabled style="width:100%;"></select>

											<input id="id_branch" name="order[order_from]" type="hidden" value="<?php echo $this->session->userdata('id_branch'); ?>" />

										<?php } ?>

										<input type="hidden" id="branch_id_country" value="" required="">

										<input type="hidden" id="branch_id_state" value="" required="">

										<input type="hidden" id="branch_id_city" value="" required="">

										<input type="hidden" id="branch_pincode" value="" required="">
										
										<input type="hidden" id="branch_id_village" value="" required="">

									</div>

								</div>

								<div class="col-sm-2">
									<div class="form-group">
										<label>Select Employee</label>
										<div class="form-group">
										<select class="form-control" id="sel_emp" name="order[sel_emp]"></select>

										</div>
									</div>
								</div>

								<div class="col-md-2 type_issue">

									<div class="form-group">

										<label>Issue Type<span class="error">*</span></label>

										<div class="form-group">

											<select class="form-control" id="issue_type" name="order[issue_type]">

												<input class="form-control" id="issue_to_cus" name="order[issue_to_cus]" type="hidden" value="">

											</select>

										</div>

									</div>

								</div>



								<div class="col-md-2 ">

									<div class="form-group stock_type">

										<label> Stock Type <span class="error">*</span></label>

										<select id="stock_type" class="form-control " name="stock_type" style="width:100%;" tabindex="5">

											<option value="1">Taged</option>

											<option value="2">Non Taged</option>

										</select>

									</div>

								</div>



								<div class="col-md-2 type_receipt" style="display:none;">

									<label>Select Issue No <span class="error">*</span> </label>

									<div class="form-group">

										<select id="select_issue_no" name="order[issue_id]" class="form-control" style="width:100%;"></select>

									</div>

								</div>



								<div class="col-md-2 type_issue tagelement">

									<label class="metal">Select Metal <span class="error">*</span></label>

									<div class="form-group">

										<select id="metal" name="order[id_metal]" class="metal_select" style="width:100%;"></select>

									</div>

								</div>



								<div class="col-md-2 ">



									<div class="form-group issued_to">



										<label>Issue to <span class="error">*</span></label>



										<select id="issued_to" class="form-control " name="order[issued_to]" style="width:100%;" tabindex="5">



											<option value="1">Customer</option>



											<option value="2">Employee</option>



											<option value="3">karigar</option>



										</select>



									</div>



								</div>



								<div class="col-sm-3 customer">

									<label>Customer<span class="error" id="cus_req"> *</span></label>

									<div class="form-group">

										<div class="input-group " style="width: 100%;">

											<input class="form-control" id="est_cus_name" name="order[cus_name]" type="text" placeholder="Customer Name / Mobile" value="<?php echo set_value('order[cus_name]', isset($order['cus_name']) ? $order['cus_name'] : NULL); ?>" required autocomplete="off" />

											<input class="form-control" id="cus_id" name="order[cus_id]" type="hidden" value="<?php echo set_value('order[cus_id]', $order['cus_id']); ?>" />

											<input class="form-control" id="selected_cus_mobile" name="order[cus_mobile]" type="hidden" value="" />

											<input id="is_otp_verfied" type="hidden" name="order[is_otp_verfied]" value="0" />

											<input id="send_resend" type="hidden" name="order[send_resend]" value="0" />

											<input id="otp_required" type="hidden" name="order[otp_required]" value="<?php echo $otp_settings_stock_issue; ?>" />


											<span class="input-group-btn">

												<button type="button" id="add_new_customer" class="btn btn-success"><i class="fa fa-plus"></i></button>

											</span>

											<span class="input-group-btn">

												<button type="button" id="edit_customer" class="btn btn-primary"><i class="fa fa-edit"></i></button>

											</span>

											<input class="form-control" id="goldrate_22ct" name="metal_rates[goldrate_22ct]" type="hidden" value="" />

											<input class="form-control" id="silverrate_1gm" name="metal_rates[silverrate_1gm]" type="hidden" value="" />

											<span id="customerAlert"></span>

										</div>

									</div>

									<p id=cus_info></p>

								</div>



								<div class="col-md-2 employee" style="display:none">

									<label class="emp_select">Select Employee <span class="error">*</span></label>

									<div class="form-group">

										<select id="issue_employee" name="order[id_employee]" class="emp_select" style="width:100%;"></select>

									</div>

								</div>



								<div class="col-md-2 karigar" style="display:none">

									<label class="kar_select">Select karigar <span class="error">*</span></label>

									<div class="form-group">

										<select id="karigar" name="order[id_karigar]" class="kar_select" style="width:100%;"></select>

									</div>

								</div>



								<div class="col-md-2 nontagelement" style="display:none;">

									<br>

									<input type="button" class="btn btn-warning" value="Search Non Tag" id="search_non_tag">

								</div>



							</div>



						</div>



					</div>

					<div class="row issueothers type_issue">

						<div class="col-sm-12">

							<div class="row" class="issue_tag_items">

								<div class="col-md-12">

									<legend><i>Issue Items</i>

										<p class="help-block"></p>

									</legend>



									<div class="row">

									
									<div class="col-md-2">

										<!-- <label class="section">Select Section <span class="error">*</span></label> -->

										<div class="form-group">

											<select id="section_select" name="order[id_section]" class="sec_select" style="width:100%;"></select>

										</div>

										</div>

										<div class="col-sm-2 tagelement">

											<div class="box-tools pull-left">

												<div class="form-group">

													<div class="input-group">

														<input type="text" id="issue_tag_code" class="form-control" placeholder="Tag Scan Code">

														<span class="input-group-btn">

															<button type="button" id="issue_tag_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>

														</span>

													</div>

													<p id="searchEstiAlert" class="error" align="left"></p>

												</div>

											</div>

										</div>



										<div class="col-sm-2 tagelement">

											<div class="input-group">

												<input type="text" id="issue_old_tag_code" class="form-control" placeholder="OLD Tag Scan">

												<span class="input-group-btn">

													<button type="button" id="issue_old_tag_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>

												</span>

											</div>

										</div>



										<div class="col-sm-2">

											<div class="form-group">

												<div class="input-group">

													<input type="text" id="rate_per_gram" class="form-control" placeholder="Rate per Gram">

												</div>

											</div>

										</div>





									</div>



									<input type="hidden" value="0" id="sto_i_increment" />

									<table id="tagissue_item_detail" class="table table-bordered table-striped tagelement">

										<thead>

											<tr>

												<th width="10%;">Tag Code</th>

												<th width="10%;">Category</th>

												<th width="10%;">Purity</th>

												<th width="10%;">Section</th>

												<th width="10%;">Product</th>

												<th width="10%;">Design</th>

												<th width="10%;">Sub Design</th>

												<th width="10%;">Pcs</th>

												<th width="10%;">GWgt</th>

												<th width="10%;">NWgt</th>

												<th width="10%;">Rate</th>

												<th width="10%;">Stone</th>

												<th width="10%;">Other Metal</th>

												<th width="10%;" style="text-align:right;">Taxable Amount</th>

												<th width="10%;" style="text-align:right;">Tax</th>

												<th width="10%;" style="text-align:right;">Tax Amount</th>

												<th width="10%;" style="text-align:right;">Net Amount</th>

												<th width="10%;">Action</th>

											</tr>

										</thead>

										<tbody>

										</tbody>

										<tfoot>

											<tr style="font-weight:bold;">

												<td colspan="7" style="text-align: center;">Total</td>

												<td class="total_pieces"></td>

												<td class="total_gross_wt"></td>

												<td class="total_nwt"></td>

												<td></td>

												<td class="total_stone_amount" style="text-align:right;"></td>

												<td class="total_othermetal_amount" style="text-align:right;"></td>

												<td class="total_taxable_amount" style="text-align:right;"></td>

												<td style="text-align:right;"></td>

												<td class="total_tax_amount" style="text-align:right;"></td>

												<td class="total_amount" style="text-align:right;"></td>

												<td></td>

											</tr>

										</tfoot>

									</table>



									<table id="nontagissue_item_detail" class="table table-bordered table-striped nontagelement" style="display:none;">

										<thead>

											<tr>

												<th width="10%;">

													<label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all">All</label>

												</th>

												<th width="10%;">Section</th>

												<th width="10%;">Product</th>

												<th width="10%;">Design</th>

												<th width="15%;">Pcs</th>

												<th width="20%;">GWgt</th>

												<th width="20%;">NWgt</th>

												<th width="10%;" style="text-align:right;">Taxable Amount</th>

												<th width="10%;" style="text-align:right;">Tax</th>

												<th width="10%;" style="text-align:right;">Tax Amount</th>

												<th width="10%;" style="text-align:right;">Net Amount</th>

												<th width="10%;">Action</th>

											</tr>

										</thead>

										<tbody>

										</tbody>

										<tfoot>

											<tr style="font-weight:bold;">

												<td colspan="4" style="text-align: center;">Total</td>

												<td class="nttotal_pieces"></td>

												<td class="nttotal_gross_wt"></td>

												<td class="nttotal_nwt"></td>

												<td class="nttotal_taxable_amount" style="text-align:right;"></td>

												<td style="text-align:right;"></td>

												<td class="nttotal_tax_amount" style="text-align:right;"></td>

												<td class="nttotal_amount" style="text-align:right;"></td>

												<td></td>

											</tr>

										</tfoot>

									</table>



									

								</div>

							</div>



						</div>





						

					</div>





					<div class="row type_receipt" style="display:none;">

						<div class="col-sm-12">

							<div class="row" class="issue_tag_items">

								<div class="col-md-12">

									<legend><i>Receipt Items</i>

										<p class="help-block"></p>

									</legend>





									<div class="row">

										<div class="col-sm-2 receipttag">

											<div class="box-tools pull-left">

												<div class="form-group">

													<div class="input-group">

														<input type="text" id="receipt_tag_code" class="form-control" placeholder="Tag Scan Code">

														<span class="input-group-btn">

															<button type="button" id="receipt_tag_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>

														</span>

													</div>

													<p id="searchEstiAlert" class="error" align="left"></p>

												</div>

											</div>

										</div>



										<div class="col-sm-2 receipttag">

											<div class="input-group">

												<input type="text" id="receipt_old_tag_code" class="form-control" placeholder="OLD Tag Scan">

												<span class="input-group-btn">

													<button type="button" id="receipt_old_tag_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>

												</span>

											</div>

										</div>





									</div>



									<input type="hidden" value="0" id="sto_i_increment" />

									<table id="tag_receipt_item_detail" class="table table-bordered table-striped">

										<thead>

											<tr>



												<th width="10%;">Tag Code</th>

												<th width="10%;">Category</th>

												<th width="10%;">Purity</th>

												<th width="10%;">Product</th>

												<th width="10%;">Design</th>

												<th width="10%;">Sub Design</th>

												<th width="10%;">Pcs</th>

												<th width="10%;">GWgt</th>

												<th width="10%;">NWgt</th>

												<th width="10%;">Rate</th>

												<th width="10%;">Stone</th>

												<th width="10%;">Other Metal</th>

												<th width="10%;" style="text-align:right;">Taxable Amount</th>

												<th width="10%;" style="text-align:right;">Tax</th>

												<th width="10%;" style="text-align:right;">Tax Amount</th>

												<th width="10%;" style="text-align:right;">Net Amount</th>

												<th width="10%;">Action</th>

											</tr>

										</thead>

										<tbody>

										</tbody>

										<tfoot>

											<tr style="font-weight:bold;">

												<td colspan="6" style="text-align: center;">Total</td>

												<td class="receipt_total_pieces"></td>

												<td class="receipt_total_gross_wt"></td>

												<td class="receipt_total_nwt"></td>

												<td></td>

												<td class="receipt_total_stone_amount" style="text-align:right;"></td>

												<td class="receipt_total_othermetal_amount" style="text-align:right;"></td>

												<td class="receipt_total_taxable_amount" style="text-align:right;"></td>

												<td></td>

												<td class="receipt_total_tax_amount" style="text-align:right;"></td>

												<td class="receipt_total_amount" style="text-align:right;"></td>

												<td></td>

											</tr>

											</tr>

										</tfoot>

									</table>

										

									<input type="hidden" name="issued_branch" class="issued_branch" id="issued_branch" value="">

									<input type="hidden" name="issued_type" class="issued_type" id="issued_type" value="">

									

									<table id="nontag_receipt_item_detail" class="table table-bordered table-striped" style="display:none;">

										<thead>

											<tr>

												<th width="10%;">

													<label class="checkbox-inline"><input type="checkbox" id="rec_select_all" name="select_all" value="all">All</label>

												</th>

												<th width="10%;">Section</th>

												<th width="10%;">Product</th>

												<th width="10%;">Design</th>

												<th width="15%;">Pcs</th>

												<th width="20%;">GWgt</th>

												<th width="20%;">NWgt</th>

												<th width="10%;" style="text-align:right;">Taxable Amount</th>

												<th width="10%;" style="text-align:right;">Tax</th>

												<th width="10%;" style="text-align:right;">Tax Amount</th>

												<th width="10%;" style="text-align:right;">Net Amount</th>

												<th width="10%;">Action</th>

											</tr>

										</thead>

										<tbody>

										</tbody>

										<tfoot>

											<tr style="font-weight:bold;">

												<td colspan="4" style="text-align: center;">Total</td>

												<td class="nttotal_pieces"></td>

												<td class="nttotal_gross_wt"></td>

												<td class="nttotal_nwt"></td>

												<td class="nttotal_taxable_amount" style="text-align:right;"></td>

												<td style="text-align:right;"></td>

												<td class="nttotal_tax_amount" style="text-align:right;"></td>

												<td class="nttotal_amount" style="text-align:right;"></td>

												<td></td>

											</tr>

											</tr>

										</tfoot>

									</table>



								</div>

							</div>

						</div>

					</div>



			</div>

		</div>



		<div class="row type_issue">

			<div class="col-md-12">

				<div class="col-sm-10">

					<div class="form-group">

						<div class="input-group">

							<label>Remarks</label>

							<textarea class="form-control" name="order[remark]" id="remark" rows="5" cols="100"> </textarea>

						</div>

					</div>

				</div>

			</div>

		</div>

		<p class="hepl-block"></p>

		<!--End of row-->

		<div class="row">

			<div class="box box-default"><br />

				<div class="col-xs-offset-5">

					<button type="button" class="btn btn-primary" id="stock_issue_submit">Save</button>

					<button type="button" class="btn btn-default btn-cancel">Cancel</button>



				</div> <br />

			</div>

		</div>
</form>
</div> <!-- box-body-->

<div class="overlay" style="display:none">

	<i class="fa fa-refresh fa-spin"></i>

</div>

</div> <!-- Default box-->



<!-- /form -->

</section>

<!-- </div>







						<label for="" class="col-md-3 col-md-offset-1 ">GST No<span class="error">*</span></label>



						<div class="col-md-6">



							<input type="text" class="form-control" id="gst_no" name="cus[gst_no]" placeholder="Enter GST No">



							<p class="help-block cus_mobile"></p>



						</div>



					</div>



				</div>



			</div>



			<div class="modal-footer">



				<input type="hidden" name="cus[id_customer]" value="">



				<a href="#" id="add_newcutomer_old" class="btn btn-success">Add</a>



				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>



			</div>



		</div>



	</div>
</div> --> <!-- confirm-add-removed-end -->















<!-- / modal -->



<!--Customer Update-->



						<!-- <div class="col-md-6">



							<input type="text" class="form-control" id="ed_gst_no" name="cus[gst_no]" placeholder="Enter GST No">



							<p class="help-block cus_mobile"></p>



						</div> -->



					</div>



				</div>



			</div>



			<!-- <div class="modal-footer">



				<input type="hidden" name="cus[id_customer]" value="">



				<a href="#" class="btn btn-success">Add</a>



				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>



			</div> -->



		</div>



	</div>



</div>



<!--Customer Update-->



<!-- / modal -->



<!--Customer Update-->


<div class="modal fade" data-backdrop="static" data-keyboard="false" id="stock_otp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

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

							<input type="text" id="stock_trns_otp" name="stock_trns_otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required />

							<span class="input-group-btn">

								<button type="button" id="verify_stock_otp" class="btn btn-primary btn-flat" disabled>Verify</button>

							</span>

						</div>

					</div>

				</div>

				<div class="col-md-2">

					<div class='form-group'>

						<input type="button" id="resend_stock_otp" class="btn btn-warning btn-flat" value="Resend OTP" />

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

			<button type="button" class="submit_stock_issue btn btn-success btn-flat" disabled>Save And Submit</button>

			<button type="button" class="btn btn-danger btn-flat" data-dismiss="modal" id="close">Close</button>

		</div>

	</div>

</div>

</div>


