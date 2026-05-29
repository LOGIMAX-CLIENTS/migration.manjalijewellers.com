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

      	.tag_scanning,
      	.add_home_bill {

      		display: none;

      		padding-top: 20px;

      	}

      	.tag_name {

      		background: #F6F6F6;

      	}
		textarea {
			width: 100%;
			resize: vertical;
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

      			<li class="active">Order</li>

      		</ol>

      	</section>



      	<!-- Main content -->

      	<section class="content order">



      		<!-- Default box -->

      		<div class="box box-primary">

      			<div class="box-header with-border">

      				<h3 class="box-title">Create Order</h3>

      				<div class="box-tools pull-right">

      					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

      					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

      				</div>

      			</div>

      			<div class="box-body">

      				<!-- form container -->

      				<!-- form -->

      				<form id="order_submit">

      					<input type="hidden" id="order_id" name="order[order_id]" value="<?php echo $order['id_customerorder'] ?>" />

      					<div class="row">

      						<div class="col-sm-12">

      							<div class="row">

      								<!--<div class="col-sm-3">

								<label>Order Date <span class="error"> *</span></label>

								<input class="form-control datemask date" data-date-format="dd-mm-yyyy" id="lt_date" name="order[order_date]" type="text" required="true" placeholder="Order Date" value="<?php echo set_value('order[order_date]', $order['order_date']); ?>" readonly />

							</div>-->

      								<div class="col-sm-2" style="display:none;">

      									<div class='form-group'>

      										<label for="order_for">Order For <span class="error">*</span></label>

      										<div class="form-group">

      											<input type="radio" name="order[order_for]" value="2" class="minimal" <?php if ($order['order_for'] == 2) { ?> checked <?php } ?> required /> Customer &nbsp;

      											<input type="radio" name="order[order_for]" value="1" class="minimal" <?php if ($order['order_for'] == 1) { ?> checked <?php } ?> /> Branch &nbsp;

      										</div>

      									</div>

      								</div>



      								<div class="col-sm-2" style="display:none;">

      									<div class='form-group'>

      										<label for="order_for">Rate Calculation <span class="error">*</span></label>

      										<div class="form-group">

      											<input type="radio" name="order[rate_calc_from]" value="1" class="minimal" <?php if ($order['rate_calc_from'] == 1) { ?> checked <?php } ?> required /> Order Rate &nbsp;

      											<input type="radio" name="order[rate_calc_from]" value="2" class="minimal" <?php if ($order['rate_calc_from'] == 2) { ?> checked <?php } ?> /> Current Rate &nbsp;

      										</div>

      									</div>

      								</div>



      								<div class="col-md-3 cus">

      									<div class="form-group">

      										<label>Customer <span class="error">*</span></label>

      										<div class="input-group">



      											<input class="form-control" id="cus_name" name="order[cus_name]" type="text" placeholder="Customer Name / Mobile" value="<?php echo set_value('order[cus_name]', isset($order['cus_name']) ? $order['cus_name'] : NULL); ?>" required autocomplete="off" />



      											<input class="form-control cus_id" id="cus_id" name="order[order_to]" type="hidden" value="<?php echo set_value('order[order_to]', $order['order_to']); ?>" />



      											<input class="form-control" id="cus_order" name="order[order_no]" type="hidden" value="">



												<input id="allow_bill_type" type="hidden" value="<?php echo set_value('order[allow_bill_type]', $order['allow_bill_type']); ?>" />
												
												<input id="is_eda" type="hidden" name="order[is_eda]" value="1" />

      											<label style="display:none;" class="per-grm-sale-value"></label>



      											<label style="display:none;" class="silver_per-grm-sale-value"></label>



      											<label style="display:none;" class="mjdmagoldrate_24ct"></label>



      											<label style="display:none;" class="mjdmagoldrate_22ct"></label>



      											<label style="display:none;" class="mjdmasilverrate_1gm"></label>



      											<label style="display:none;" class="goldrate_24ct"></label>



      											<label style="display:none;" class="goldrate_22ct"></label>



      											<label style="display:none;" class="silverrate_1gm"></label>



      											<label style="display:none;" class="silverrate_999"></label>



      											<label style="display:none;" class="silverrate_1kg"></label>



      											<label style="display:none;" class="goldrate_18ct"></label>


												<label style="display:none;" class="goldrate_14ct"></label>



      											<label style="display:none;" class="platinum_1g"></label>



      											<input type="hidden" id="cus_state" name="">

												<input type="hidden" id="cmp_country" name="">

      											<input type="hidden" id="cmp_state" name="">

      											<span class="input-group-btn">

      												<button type="button" id="add_new_customer" class="btn btn-success"><i class="fa fa-plus"></i></button>

      												<button type="button" id="edit_customer" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#demo"><i class="fa fa-edit"></i></button>

      											</span>

      										</div>

      									</div>

      									<p id="cusAlert" class="error" align="left"></p>

      								</div>

      								<!-- <div class="col-sm-1">

				 		    	<label></label>

    				 		    <div class="form-group">

    				 		        <a href="#" class="btn btn-primary btn-del" id="edit_customer"><i class="fa fa-edit" ></i></a>

    				 		     </div>

				 		    </div> -->

      								<div class="col-sm-3 brn" <?php if ($order['order_for'] == 2) { ?>style="display: none" <?php } ?>>

      									<div class="form-group">

      										<label class="">Branch (Order For) </label>

      										<select id="order_to_br" name="order[order_to_br]" class="form-control ret_branch"></select>

      										<input type="hidden" id="id_order_to_br" name="order[id_branch]">

      									</div>

      								</div>

      								<div class="col-md-2">

      									<label>Order Branch <span class="error">*</span> </label>

      									<div class="form-group">

      										<?php if ($this->session->userdata('id_branch') == '') { ?>

      											<?php if ($this->uri->segment(3) != 'edit') { ?>

      												<select id="branch_select" class="form-control order_from"></select>
													<input type="hidden" id="branch_id_country" value="" required="">
													<input type="hidden" id="branch_id_state" value="" required="">
													<input type="hidden" id="branch_id_city" value="" required="">
													<input type="hidden" id="branch_pincode" value="" required="">
													<input type="hidden" id="branch_id_village" value="" required="">



      											<?php } else { ?>

      												<select id="branch_select" class="form-control order_from" disabled></select>
													  <input type="hidden" id="branch_id_country" value="" required="">
													<input type="hidden" id="branch_id_state" value="" required="">
													<input type="hidden" id="branch_id_city" value="" required="">
													<input type="hidden" id="branch_pincode" value="" required="">
													<input type="hidden" id="branch_id_village" value="" required="">

      											<?php } ?>

      											<input type="hidden" name="order[order_from]" id="id_branch" value="" required="">

      										<?php } else { ?>

      											<!--<select id="branch_select" class="form-control order_from" disabled></select>

				 					<input id="id_branch" name="order[order_from]"  type="hidden" value="<?php echo $this->session->userdata('id_branch'); ?>"/>-->



      											<select id="branch_select" class="form-control order_from" disabled></select>

      											<input type="hidden" name="order[order_from]" id="id_branch" value="<?php echo $this->session->userdata('id_branch'); ?>">



      										<?php } ?>

      									</div>

      								</div>



      								<div class="col-sm-2">

      									<label>Employee <span class="error">*</span> </label>

      									<div class="form-group">

      										<select id="issue_employee" class="form-control" style="width:100%;" required></select>

      										<input type="hidden" name="order[order_taken_by]" id="id_employee" value="">

      									</div>

      								</div>

      								<div class="col-md-4">

      									<label>Balance Type <span class="error">*</span> </label><br>

      									<input type="radio" name="order[balance_type]" value="1" id="metal_bal_type" /> &nbsp;

      									<label for="metal_bal_type">Metal Balance</label> &nbsp;

      									<input type="radio" name="order[balance_type]" value="2" id="cash_bal_type" checked /> &nbsp;

      									<label for="cash_bal_type">Cash Balance</label>

      								</div>

									 <div class="col-md-4 eda_tax_calc" style="display:none;padding-left:100px;white-space:nowrap">
									 	<label for="eda_tax_calc">EDA TAX</label>
										 <input id="eda_tax_calc" type="checkbox" value="" />
										 <input type="hidden" name=order[is_eda_tax_calc] id="is_eda_tax_calc" value="0">
									 </div>





      								<!--<div class=" col-md-3">

					    		<?php if ($this->session->userdata('branch_settings') == 1) { ?>

								<div class="form-group">

								 	<label>Order Branch <span class="error">*</span> </label>

    								<select required id="order_from" class="form-control ret_branch"></select>

    							</div>

								<?php } ?>



					    	</div> -->

      								<div class="col-sm-4" style="display: none">

      									<div class="form-group" style="height:60px">

      										<label for=""><a data-toggle="tooltip" title="Select estimatio to create order">Estimation No </a> </label>

      										<input type="text" class="form-control esti_no" name="order[est_no]" placeholder="Estimation Number" id="esti_no" value="<?php echo set_value('order[est_no]', isset($order['est_no']) ? $order['est_no'] : NULL); ?>" style="width: 99%;" autocomplete="off">

      									</div>

      									<p></p>

      								</div>

      							</div>

      						</div>

      						<div class="col-sm-12">

      							<div class="row">

      								<div class="col-md-3 order_types" style="max-width:290px;">

      									<label>Order Type <span class="error">*</span></label></br>

      									<input type="radio" name="order[order_type]" id="customer_order" value="2" checked /> <label for="customer_order"> Customized Order </label> &nbsp;&nbsp;

      									<input type="radio" name="order[order_type]" id="tag_order" value="5" /> <label for="tag_order"> Tag Reserve </label>

      									&nbsp;&nbsp;

      									<!--<input type="radio" name="order[order_type]" id="homebill_order" value="6" /> <label for="homebill_order">  Home Bill Order </label>-->

      								</div>

      								<div class="col-md-3 order_types" style="min-width:340px;">
      									<label>Rate Type <span class="error">*</span></label></br>
      									<input type="radio" name="order[rate_type]" id="order_rate" value="1" checked /> <label for="customer_order">Order Rate(Fixed)</label> &nbsp;&nbsp;
      									<input type="radio" name="order[rate_type]" id="delivery_rate" value="2" /> <label for="tag_order">Delivery Rate(Un Fixed)</label>
      									&nbsp;&nbsp;
      								</div>

      								<div class="col-md-2 add_item">

      									<label></label><br>

      									<button id="add_order_item" type="button" class="btn btn-success"><i class="fa fa-plus"></i> Add Item </button>

      								</div>



      								<div class="col-sm-2 add_home_bill">

      									<button id="add_home_bill_item" type="button" class="btn btn-success"><i class="fa fa-plus"></i> Add Home Bill </button>

      								</div>



      								<div class="col-sm-2  tag_scanning">

      									<div class="box-tools pull-left">

      										<div class="form-group">

      											<div class="input-group">

      												<input type="text" id="est_tag_scan" class="form-control" placeholder="New Tag Scan">

      												<span class="input-group-btn">

      													<button type="button" id="tag_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>

      												</span>

      											</div>



      											<p id="searchEstiAlert" class="error" align="left"></p>

      										</div>

      									</div>

      								</div>



      								<div class="col-sm-2  tag_scanning">

      									<div class="input-group">

      										<input type="text" id="est_tag_barcode_scan" class="form-control" placeholder="OLD Tag Scan">

      										<span class="input-group-btn">

      											<button type="button" id="tag_barcode_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>

      										</span>

      									</div>

      								</div>

      								<div class="col-sm-2 tag_scanning">

      									<div class="input-group">

      										<input type="text" id="est_no" class="form-control" placeholder="Estimation No">

      										<span class="input-group-btn">

      											<button type="button" id="esti_search" class="btn btn-default btn-flat"><i class="fa fa-search"></i></button>

      										</span>

      									</div>

      								</div>



      							</div>

      						</div>

      						<div class=" col-md-7" style="display: none">

      							<div class="form-group">

      								<div class="box box-info ">

      									<div class="box-header with-border">

      										<h3 class="box-title">Estimation Details</h3>

      										<div class="box-tools pull-right">

      											<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

      										</div>

      									</div>

      									<div class="box-body">

      										<div class="row">

      											<div class="col-sm-6">

      												<div class="row brn" <?php if ($order['order_for'] == 2) { ?>style="display: none" <?php } ?>>

      													<div class="col-sm-6">

      														<label class="">Branch (Order For) </label>

      													</div>

      													<div class="col-sm-6">

      														<select id="order_to_br" name="order[order_to_br]" class="form-control ret_branch"></select>

      														<input type="hidden" id="id_order_to_br" name="order[id_branch]">

      													</div>

      												</div>

      												<div class="row cus" <?php if ($order['order_for'] == 1) { ?>style="display: none" <?php } ?>>

      													<div class="col-sm-6">

      														<label class="">Customer </label>

      													</div>

      													<div class="col-sm-6">

      														<span id="cus" class=""></span>

      														<input type="hidden" id="id_customer" name="order[id_customer]">

      													</div>

      												</div>

      												<div class="row cus" <?php if ($order['order_for'] == 1) { ?>style="display: none" <?php } ?>>

      													<div class="col-sm-6">

      														<label class="">Mobile </label>

      													</div>

      													<div class="col-sm-6">

      														<span id="mobile" class=""></span>

      													</div>

      												</div>

      											</div>

      											<div class="col-sm-6">

      												<div class="row">

      													<div class="col-sm-6">

      														<label class="">Est Date </label>

      													</div>

      													<div class="col-sm-6">

      														<span id="est_date" class=""></span>

      													</div>

      												</div>

      												<div class="row">

      													<div class="col-sm-6">

      														<label class="">Total Cost </label>

      													</div>

      													<div class="col-sm-6">

      														<span id="tot_cost" class=""></span>

      													</div>

      												</div>

      											</div>

      										</div>

      									</div>

      								</div>

      							</div>

      						</div>

      					</div>



      					<p class="hepl-block"></p>



      					<div class="row">

      						<div class="col-md-12">

      							<div class="table-responsive">



      								<input type="hidden" value="0" id="i_increment" />

      								<input type="hidden" id="cur_id" />

      								<p class="help-block"></p>
      								</legend>

      								<input type="hidden" id="smith_remainder_date" name="" value="<?php echo $order['smith_remainder_date']; ?>">

      								<input type="hidden" id="smith_due_date" name="" value="<?php echo $order['smith_due_date']; ?>">

      								<input type="hidden" id="cus_due_date" name="" value="<?php echo $order['cus_due_date']; ?>">

      								<input type="hidden" id="customer_order_description_req" name="" value="<?php echo $customer_order_description_req; ?>">


      								<table id="item_detail" class="table table-bordered table-striped">

      									<thead>

      										<tr>

      											<th width="25%;" style="display: none;">Order Type</th>

      											<th width="10%;">Tag Code</th>

      											<th style='display:none' width="10%;">Category</th>

      											<th width="10%;">Product</th>

      											<th width="10%;">Design</th>

      											<th width="10%;">Sub Design</th>

      											<th width="10%;">Purity</th>

      											<th width="10%;">Gross Wt</th>

      											<th width="10%;">Less Wt</th>

      											<th width="10%;">Net Wt</th>

      											<th width="10%;">Size</th>

      											<th width="10%;">Pcs</th>

      											<th width="10%;">Wast %</th>

      											<th width="10%;">Wast Wgt</th>

      											<th width="10%;">MC Type</th>

      											<th width="10%;">MC Value</th>

      											<th width="10%;">Other Charge</th>

      											<th width="10%;">Charge Amt</th>

      											<!--<th width="10%;">Stone</th> -->

      											<th width="10%;">Stone Amt</th>

      											<th width="10%;">Rate</th>

      											<th width="10%;">Taxable Amt</th>

      											<th width="10%;">Amount</th>

      											<th width="5%;">Image</th>

      											<th width="5%;">Des</th>

      											<th width="10%;">C.Due</th>

      											<!--<th width="10%;">S.Due</th>

						            <th width="10%;">S.Reminder</th>-->

      											<th width="10%;">Action</th>

      										</tr>

      									</thead>

      									<tbody>

      									</tbody>

      								</table>

      							</div>

      						</div>

      					</div>

      					<p class="help-block"></p>





      					<div class="row">

      						<div class="box box-default"><br />

      							<div class="col-xs-offset-5">

      								<button type="button" id="create_order" class="btn btn-primary">save</button>

      								<button type="button" class="btn btn-default btn-cancel">Cancel</button>



      							</div> <br />

      						</div>

      					</div>

      			</div>

      			<?php echo form_close(); ?>

      			<div class="overlay" style="display:none">

      				<i class="fa fa-refresh fa-spin"></i>

      			</div>

      		</div>

      	</section>

      </div>



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

										<input type="number" class="form-control" id="cus_mobile" maxlength="10" name="cus[mobile]" placeholder="Enter customer mobile">

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

      <!-- / modal -->

      <!--Customer Update-->

      <!-- <div class="modal fade" id="confirm-edit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Edit Customer</h4>

			</div>

			<div class="modal-body">

				<div class="row">

					<div class="form-group">

					   <label for="cus_first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="ed_cus_first_name" name="cus[first_name]" placeholder="Enter customer first name" required="true" style="text-transform:uppercase">

							<p class="help-block cus_first_name"></p>

					   </div>

					</div>

				</div>

				<div class="row">

					<div class="form-group">

					   <label for="cus_mobile" class="col-md-3 col-md-offset-1 ">Mobile<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="ed_cus_mobile" name="cus[mobile]" placeholder="Enter customer mobile" disabled>

							<p class="help-block cus_mobile"></p>

					   </div>

					</div>

				</div>

				<div class="row">

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Select Area<span class="error"></span></label>

					   <div class="col-md-6">

						 <select class="form-control" id="ed_sel_village" style="width:100%;"></select>

						 <input type="hidden" id="ed_id_village">

					   </div>

					</div>

				</div></br>

				<div class="row">

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Customer Type<span class="error"></span></label>

					   <div class="col-md-6">

						 <input type="radio" id="ed_cus_type1"  name="ed_cus[cus_type]" value="1" class="minimal" checked/> Individual

						 <input type="radio" id="ed_cus_type2"  name="ed_cus[cus_type]" value="2" class="minimal" /> Business

					   </div>

					</div>

				</div>



			</div>

		  <div class="modal-footer">

			<a href="#" id="update_cutomer" class="btn btn-success">Update</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

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

      			<input type="file" name="order_images" id="order_images" multiple="multiple">

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

      				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

      				<h4 class="modal-title" id="myModalLabel">Add Description</h4>

      			</div>

      			<div class="modal-body">

      				<div class="row">

      					<div class="col-md-10 col-md-offset-1">

      						<label for="user_lastname">Item Description</label>

      						<div class='form-group'>

      							<textarea cols="70" id="description" name="description" rows="5"></textarea>

      						</div>

      					</div>

      				</div>

      			</div>

      			<div class="modal-footer">

      				<a href="#" class="btn btn-success add_order_desc">Add</a>

      				<button type="button" class="btn btn-close btn-danger" data-bs-dismiss="modal">Close</button>

      			</div>

      		</div>

      	</div>

      </div>

      <!-- / modal -->



      <!-- Stone Modal -->



      <input type="hidden" id="custom_active_id" class="custom_active_id" name="" value="" />



      <input type="hidden" id="custom_active_table" class="custom_active_table" name="" value="" />



      <div class="modal fade" id="cus_stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



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



      							<tr></tr>



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



      				<button type="button" id="close_stone_details" class="btn btn-close btn-warning" data-bs-dismiss="modal">Close</button>



      			</div>



      		</div>



      	</div>



      </div>





      <script type="text/javascript">
      	var Categories = new Array();

      	var CategorysArr = new Array();

      	CategorysArr = JSON.parse('<?php echo json_encode($categories); ?>');
      </script>