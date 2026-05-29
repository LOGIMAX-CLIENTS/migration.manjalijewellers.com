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
      </style>
      <div class="content-wrapper">
      	<!-- Content Header (Page header) -->
      	<section class="content-header">
      		<h1>
      			Master
      			<small>Order</small>
      		</h1>
      		<ol class="breadcrumb">
      			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      			<li><a href="#">Master</a></li>
      			<li class="active">Repair</li>
      		</ol>
      	</section>

      	<!-- Main content -->
      	<section class="content order">

      		<!-- Default box -->
      		<div class="box box-primary">
      			<div class="box-header with-border">
      				<h3 class="box-title">Repair Order</h3>

      			</div>
      			<div class="box-body">
      				<?php
						if ($this->session->flashdata('chit_alert')) {
							$message = $this->session->flashdata('chit_alert');
						?>
      					<div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
      						<button type="button" class="close" data-bs-dismiss="alert" aria-hidden="true">&times;</button>
      						<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
      						<?php echo $message['message']; ?>
      					</div>
      				<?php } ?>
      				<!-- form container -->
      				<!-- form -->
      				<form id="order_submit">
      					<div class="row">
      						<div class="col-sm-12">
      							<input type="hidden" id="id_customerorder" name="order[id_customerorder]" value="<?php echo $order['id_customerorder'] ?>" />
      							<div class="row">

      								<div class="col-md-2">
      									<div class="form-group">
      										<label>Type</label>
      										<div class="form-group">
      											<input type="radio" id="cus_repair_order" name="order[order_type]" value="3" <?php if ($order['order_type'] == 3) { ?> checked <?php } elseif ($order['order_type'] != 4) {
																																												echo "checked";
																																											} ?>><label for="cus_repair_order"> Customer </label>
      											&nbsp;&nbsp;&nbsp;
      											<input type="radio" id="stock_repair_order" name="order[order_type]" value="4" <?php if ($order['order_type'] == 4) { ?> checked <?php } ?>><label for="stock_repair_order"> Stock </label>
      										</div>
      									</div>
      								</div>

      								<div class="col-md-2">
      									<div class="form-group">
      										<label>Work At</label>
      										<div class="form-group">
      											<input type="radio" id="cus_repair_at_inhouse" name="order[work_at]" value="1" <?php if ($order['work_at'] != 2) { ?> checked <?php } ?>><label for="cus_repair_order"> In House </label>
      											&nbsp;&nbsp;&nbsp;
      											<input type="radio" id="cus_repair_at_outsource" name="order[work_at]" value="2" <?php if ($order['work_at'] == 2) { ?> checked <?php } ?>><label for="stock_repair_order"> Out Source </label>
      										</div>
      									</div>
      								</div>
      								<div class="col-md-2">
      									<div class="form-group">
      										<label>Employee</label>
      										<div class="form-group">
      											<select id="employee_sel" class="form-control" name="order[id_employee]" required></select>
      											<input type="hidden" id="id_employee" />
      										</div>
      									</div>
      								</div>
      								<div class="col-md-2">
      									<label>Order From <span class="error">*</span> </label>
      									<div class="form-group">
      										<?php if (empty($this->session->userdata('id_branch'))) { ?>
      											<select id="branch_select" class="form-control order_from" required style="width:100%;"></select>
      											<input id="id_branch" name="order[order_from]" type="hidden" />
												<input type="hidden" id="branch_id_country" value="" required="">
														<input type="hidden" id="branch_id_state" value="" required="">
														<input type="hidden" id="branch_id_city" value="" required="">
														<input type="hidden" id="branch_pincode" value="" required="">
														<input type="hidden" id="branch_id_village" value="" required="">
      										<?php } else { ?>
      											<!-- Branch-login: readonly text — avoids Select2 keyboard-open bug -->
      											<input type="text" class="form-control" value="<?php echo htmlspecialchars($this->session->userdata('branch_name')); ?>" readonly style="background-color:#e9ecef;cursor:not-allowed;font-weight:600;" />
      											<input id="id_branch" name="order[order_from]" type="hidden" value="<?php echo $this->session->userdata('id_branch'); ?>" />
												<input type="hidden" id="branch_id_country" value="" required="">
														<input type="hidden" id="branch_id_state" value="" required="">
														<input type="hidden" id="branch_id_city" value="" required="">
														<input type="hidden" id="branch_pincode" value="" required="">
														<input type="hidden" id="branch_id_village" value="" required="">
      										<?php } ?>
      									</div>
      								</div>
      								<div class="col-sm-4 cus_repair">
									  <label>Customer <span class="error">*</span></label>

      									<div class="form-group" style="display: flex;">
      										<div class="input-group">
      											<input class="form-control" id="cus_name" name="order[cus_name]" type="text" placeholder="Customer Name / Mobile" value="" required autocomplete="off" />
      											<input class="form-control cus_id" id="cus_id" name="order[order_to]" type="hidden" value="" />
      											<span id="customerAlert"></span>
      										</div>
      										<div class="input-group" >
      											<span data-toggle="tooltip" title="Add New Customer"><a href="#" class="btn btn-primary btn-del" id="add_new_customer_repair"><i class="fa fa-plus"></i></a></span>
      											<span data-bs-toggle="offcanvas" data-bs-target="#demo" id="edit_customer" class="btn btn-success"><i class="fa fa-edit"></i></span>

      										</div>
      									</div>
      									<p id="cusAlert" class="error" align="left"></p>
      								</div>
      								<div class="col-md-1 cus_repair" style="margin-top: 5px;">

      								</div>
      								<div class="col-md-2 cus_repair">
      									<div class="form-group">
      										<!-- <label>Search Bill No</label> -->
      										<div class="input-group">
      											<!-- <input class="form-control" id="filter_bill_no" name="filter_bill_no" type="text" placeholder="Bill No." value=""/>
										<span class="input-group-btn">
										<button type="button" id="search_bill_no" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button> -->
      											</span>
      										</div>
      									</div>
      								</div>
      								</br>
      								<div class="col-md-2 cus_repair">
      									<div class="form-group">
      										<label></label>
      										<button type="button" id="repaid_order_items" class="btn btn-primary" style="margin-top:1px;">Add Items</button>
      										<input type="hidden" id="cus_due_date" name="" value="<?php echo $order['cus_due_date']; ?>">
      										<input type="hidden" id="smith_remainder_date" name="" value="<?php echo $order['smith_remainder_date']; ?>">
      										<input type="hidden" id="smith_due_date" name="" value="<?php echo $order['smith_due_date']; ?>">
      										<input type="hidden" name="order[order_for]" value="2">
      									</div>
      								</div>

      								<div class="col-sm-2 stock_repair" style="display:none;">
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

      								<div class="col-sm-2 stock_repair" style="display:none;">
      									<div class="box-tools pull-left">
      										<div class="form-group">
      											<div class="input-group">
      												<input type="text" id="old_issue_tag_code" class="form-control" placeholder="old_Tag Scan Code">
      												<span class="input-group-btn">
      													<button type="button" id="issue_old_tag_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>
      												</span>
      											</div>
      											<p id="searchEstiAlert" class="error" align="left"></p>
      										</div>
      									</div>
      								</div>
      							</div>
      						</div>
      					</div>
      					<p class="hepl-block"></p>
      					<div class="row cus_repair">
      						<div class="col-md-12">
      							<div class="table-responsive">
      								<legend><i>Customer Order Items</i>
      									<!--<button id="add_order_item" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Item </button>-->
      									<input type="hidden" value="0" id="i_increment" />
      									<input type="hidden" value="0" id="cus_i_increment" />
      									<input type="hidden" id="cur_id" />
      									<p class="help-block"></p>
      								</legend>
      								<table id="custrepair_item_detail" class="table table-bordered table-striped">
      									<thead>
      										<tr>
      											<th width="10%;">Metal<span class="error">*</span></th>
      											<th width="10%;">Product<span class="error">*</span></th>
      											<th width="10%;">Design<span class="error">*</span></th>
      											<th width="10%;">Sub Design<span class="error">*</span></th>
      											<th width="10%;">Gross Wt<span class="error">*</span></th>
      											<th width="10%;">Less Wt<span class="error">*</span></th>
      											<th width="10%;">Net Wt<span class="error">*</span></th>
      											<th width="10%;">Pcs<span class="error">*</span></th>
      											<th width="10%;">C.Due<span class="error">*</span></th>
      											<th width="10%;">Repair Type<span class="error">*</span></th>
      											<th width="10%;">Image</th>
      											<th width="10%;">Description</th>
      											<th width="10%;">Action</th>
      										</tr>
      									</thead>
      									<tbody> </tbody>
      									<tfoot>
      										<tr>
      											<th>Total</th>
      											<td></td>
      											<td></td>
      											<td></td>
      											<th>
      												<span class="cus_tot_wgt"></span>
      											</th>
      											<td><span class="cus_tot_less_wgt"></span></td>
      											<th>
      												<span class="cus_tot_net_wgt"></span>
      											</th>
      											<th>
      												<span class="cus_tot_pcs"></span>
      											</th>
      										</tr>
      									</tfoot>
      								</table>
      							</div>
      						</div>
      					</div>

      					<div class="row stock_repair" style="display:none;">
      						<div class="col-md-12">
      							<legend><i>Stock Repair Items</i></legend>
      							<input type="hidden" value="0" id="cus_i_increment" />
      							<table id="tagissue_item_detail" class="table table-bordered table-striped">
      								<thead>
      									<tr>
      										<th width="10%;">Image</th>
      										<th width="10%;">Tag Code</th>
      										<th width="10%;">Old Tag Code</th>
      										<th width="10%;">Category</th>
      										<th width="10%;">Purity</th>
      										<th width="10%;">Product</th>
      										<th width="10%;">Design</th>
      										<th width="10%;">Sub Design</th>
      										<th width="10%;">Pcs</th>
      										<th width="10%;">GWgt</th>
      										<th width="10%;">NWgt</th>
      										<th width="10%;">LWgt</th>
											<th width="10%;">Repair Type<span class="error">*</span></th>
											<th width="10%;">Image</th>
      										<th width="10%;">Narration</th>
      										<th width="10%;">Action</th>
      									</tr>
      								</thead>
      								<tbody>
      								</tbody>
      								<tfoot>
      									<tr style="font-weight:bold;">
      										<td></td>
      										<td colspan="7" style="text-align: center;">Total</td>
      										<td class="total_pieces"></td>
      										<td class="total_gross_wt"></td>
      										<td class="total_net_wt"></td>
      										<td></td>
											<td></td>
											<td></td>
      									</tr>
      								</tfoot>
      							</table>
      						</div>
      					</div>

      					<p class="help-block"></p>

      					<!--End of row-->

      					<div class="row">
      						<div class="box box-default"><br />
      							<div class="col-xs-offset-5">
      								<button type="button" class="btn btn-primary" id="create_repair_order">Save</button>
      								<button type="button" class="btn btn-default btn-cancel">Cancel</button>

      							</div> <br />
      						</div>
      					</div>
      			</div> <!-- box-body-->
      			<div class="overlay" style="display:none">
      				<i class="fa fa-refresh fa-spin"></i>
      			</div>
      		</div> <!-- Default box-->
      		<?php echo form_close(); ?>

      		<!-- /form -->
      	</section>
      </div>

      <!--  Image Upload-->
      <!-- <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Add Image</h4>
			</div>

			<div class="modal-body">
				<input type="file" name="order_images" id="order_images" multiple="multiple">
					<input type="hidden" id="active_row">

			</div></br>
			<div id="uploadArea_p_stn" class="col-md-12"></div>
		  <div class="modal-footer">
			<button type="button" id="update_img" class="btn btn-success">Save</button>
			<button type="button" id="close_stone_details" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>
</div> -->



      <!--  Image Upload-->



      <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      	<div class="modal-dialog" style="width:60%;">

      		<div class="modal-content">

      			<div class="modal-header">

      				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>

      					<button class="btn btn-primary" id="toggle-webcam_button" style="float:right;margin-right:20px;">Enable WebCam</button>



      			</div>

      			<input type="file" name="order_images" id="order_images" multiple="multiple" style="color: transparent">

      			<input type="hidden" id="active_row">

      			<div class="modal-body">



      				<div class="target_preview_webcam" style="display:none">

      					<input type="button" value="Take Snapshot" onClick="take_snapshot('pre_images')" style="float:right" class="btn btn-warning" id="snap_shots"><br>

      					<div class="row">

      						<div class="col-md-12">

      							<div class="col-md-3"></div>

      							<div class="col-md-6" id="my_camera"></div>

      							<input type="hidden" name="image" class="image-cust">

      							<input type="hidden" id="customer_images" name="customer[cus_img]">

      							<div class="col-md-3"></div>

      						</div>

      					</div>



      					<div class="row" id="image_lot_list" style="display:none;">

      						<div class="col-md-12" style="font-weight:bold;">Orders Images</div>

      					</div>

      				</div>



      				<div id="uploadArea_p_stn" class="col-md-12"></div>

      			</div>


      			<div class="modal-footer">

      				<button type="button" id="update_img" class="btn btn-success">Save</button>



      				<button type="button" id="close_stone_details" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>

      			</div>

      		</div>

      	</div>

      </div>



      <!--  Image Upload-->

      <div class="modal fade" id="order_des" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      	<div class="modal-dialog">
      		<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      				<h4 class="modal-title" id="myModalLabel">Add Description</h4>
      			</div>

      			<div class="modal-body">

      				<div class="row">

      					<div class="col-md-10 col-md-offset-1">
      						<label for="user_lastname">Item Description</label>
      						<div class='form-group'>
      							<textarea cols="70" id="description" name="description"><?php echo set_value('sch[description]', (isset($sch['description']) ? $sch['description'] : "")); ?></textarea>
      						</div>
      					</div>
      				</div>
      			</div>

      			<div class="modal-footer">
      				<a href="#" class="btn btn-success add_order_desc">Add</a>
      				<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
      <!-- / modal -->



      <!--  Image Upload-->
      <div class="modal fade" id="BillModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      	<div class="modal-dialog" style="width:60%;">
      		<div class="modal-content">
      			<div class="modal-header">
      				<h4 class="modal-title" id="myModalLabel">Billing Details</h4>
      			</div>

      			<div class="modal-body">

      				<div class="row" id="bill_items_for_return" style="display:none;">
      					<div class="box-body">
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
      										<th>Amount</th>
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
      			</div></br>
      			<div id="uploadArea_p_stn" class="col-md-12"></div>
      			<div class="modal-footer">
      				<button type="button" id="update_bill_details" class="btn btn-success">Save</button>
      				<button type="button" id="close_stone_details" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
      </div>

      <!-- <div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
										<input class="form-control titlecase" id="pin_code_add" type="text" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' required />
										<p class="help-block pincode error"></p>
									</div>
							</div>
						</div></br>

						<div class="row">
							<div class="form-group">
							<label for="" class="col-md-3 col-md-offset-1 ">Customer Type<span class="error"></span></label>
							<div class="col-md-6">
								<input type="radio" id="cus_type"  name="cus[cus_type]" value="1" class="minimal" checked/> Individual
								<input type="radio" id="cus_type"  name="cus[cus_type]" value="2" class="minimal" /> Business
							</div>
							</div>
						</div></br>

						<div class="row">
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
			 <a href="#" id="add_newcutomer_repair" class="btn btn-success">Add</a>
			<button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div> -->

      <!-- / modal -->

      <input type="hidden" id="custom_active_id" class="custom_active_id" name="" value="" />



      <input type="hidden" id="custom_active_table" class="custom_active_table" name="" value="" />



      <div class="modal fade" id="cus_stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"  data-bs-backdrop="static">



      	<div class="modal-dialog" style="width:72%;">



      		<div class="modal-content">



      			<div class="modal-header">



      				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>



      			</div>



      			<div class="modal-body">



      				<div class="row">



      					<div class="box-tools pull-right">



      						<button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>



      					</div>



      				</div>



      				<div class="row">



      					<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">



      						<thead>



      							<tr>



      								<th width="5%">LWT</th>



      								<th width="15%">Type</th>



      								<th width="15%">Name</th>



      								<th width="10%">Pcs</th>



      								<th width="20%">Wt</th>





      								<th width="10%">Cal.Type</th>



      								<th width="15%">Rate</th>



      								<th width="17%">Amount</th>



      								<th width="10%">Action</th>



      							</tr>



      						</thead>



      						<tbody>



      						</tbody>



      						<tfoot>



      							   <tr style="font-weight:bold">
								   
									<td colspan ="3">TOTAL</td>
									<td class="stn_pcs"></td>
									<td class="stn_wt"></td>

									<td></td>
									<td class="stn_rate"></td>
									<td class="stn_amt"></td>
									<td></td>



								</tr>



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

      <script type="text/javascript">
      	var Categories = new Array();
      	var CategorysArr = new Array();
      	CategorysArr = JSON.parse('<?php echo json_encode($categories); ?>');
      </script>

	  <!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include moment.js -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

<!-- Include daterangepicker.js -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
