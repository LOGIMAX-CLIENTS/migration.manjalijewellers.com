      <!-- Content Wrapper. Contains page content -->
      <style>
      	.remove-btn {
      		margin-top: -168px;
      		margin-left: -38px;
      		background-color: #e51712 !important;
      		border: none;
      		color: white !important;
      	}

      	.summary_lbl {
      		font-weight: bold;
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

      	.payment_blk .form-control {
      		width: 250px;
      	}

      	.gift_details {
      		color: #FF0000;
      	}

      	input[type=number]::-webkit-inner-spin-button,
      	input[type=number]::-webkit-outer-spin-button {
      		-webkit-appearance: none;
      		-moz-appearance: none;
      		appearance: none;
      		margin: 0;
      	}
      </style>
      <div class="content-wrapper">
      	<!-- Content Header (Page header) -->
      	<section class="content-header">
      		<h1>
      			Metal Process
      			<small>Metal Process</small>
      		</h1>
      		<ol class="breadcrumb">
      			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      			<li><a href="#">Metal Process</a></li>
      			<li class="active">Metal Process</li>
      		</ol>
      	</section>
      	<!-- Main content -->
      	<section class="content product">
      		<!-- Default box -->
      		<div class="box box-primary">
      			<div class="box-header with-border">
      				<h3 class="box-title">Metal Process</h3>
      				<div class="box-tools pull-right">
      					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
      					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
      				</div>
      			</div>
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
      					<form id="process_issue">
      						<div class="col-md-12">
      							<div class="row"> <!--stickyBlk-->
      								<div class="col-sm-12">
      									<div class="box box-solid">
      										<!--<div class="box-header">
					              <h3 class="box-title">Bill Type</h3>
					            </div>-->
      										<div class="box-body">
      											<div class="row">

      												<div class="col-md-2">
      													<label>Process For</label></br>
      													<input type="radio" id="issue_process" name="process[process_for]" value="1" checked> <label for="issue_process" class="custom-label"> ISSUE </label>
      													<input type="radio" id="receipt_process" name="process[process_for]" value="2"> <label for="receipt_process" class="custom-label"> RECEIPT </label>
      													<input type="hidden" id="id_branch" value="<?php echo $branch['id_branch']; ?>">
      												</div>

      												<div class="col-md-2 melting" style="display:none">
      													<label>Against Melting</label></br>
      													<input type="radio" id="against_melting_yes" name="testing_receipt[against_melting]" value="1"><label for="issue_process" class="custom-label">Yes</label>
      													<input type="radio" id="against_melting_no" name="testing_receipt[against_melting]" value="2" checked><label for="receipt_process" class="custom-label">No</label>
      												</div>

      												<div class="col-md-2">
      													<label>Process Type</label></br>
      													<select class="form-control" id="select_process" name="process[id_metal_process]" style="width:100%;"></select>
      												</div>
      												<div class="col-md-2 vendor">
      													<label>Select Vendor</label></br>
      													<select class="form-control" id="karigar" name="process[id_karigar]" style="width:100%;"></select>
      												</div>

      												<div class="col-md-2">
      													<label></label></br>
      													<button type="button" id="process_filter" class="btn btn-info">Search</button>
														  <input type="hidden" id="chg_tax_type" name="process[chg_tax_type]">
      												</div>

      											</div>
      											<p class="help-block"></p>
      										</div>
      										<!-- /.box-body -->
      									</div>
      								</div>
      							</div>
      							<div class="box box-default melting_issue" style="display:none;">
      								<div class="box-header with-border">
      									<div class="box-body">
      										<div class="row">
      											<div class="col-md-12">
      												<div class="col-md-2">
      													<label>Select Type</label></br>
      													<select class="form-control" name="process[melting_trans_type]" id="melting_trans_type" style="width:100%;">
      														<option value="1" selected>Old Metal</option>
      														<option value="2">Tagged</option>
      														<option value="3">Non Tagged</option>
      													</select>
      												</div>
      												<div class="col-md-2">
      													<label>Select Pocket</label></br>
      													<select class="form-control" id="select_pocket" style="width:100%;"></select>
      												</div>
      											</div>
      										</div>
      										<div class="row">
      											<div class="box-body">
      												<div class="table-responsive old_metal_melting_details">
      													<table id="pocket_details" class="table table-bordered table-striped text-center">
      														<thead>
      															<tr>
      																<th><input type="checkbox" id="old_metal_select_all" name="old_metal_select_all" value="all" />Pocket No</label></th>
      																<th>Metal Type</th>
      																<th>Category</th>
      																<th>Pcs</th>
      																<th>G.Wt (Grams)</th>
      																<th>N.Wt (Grams)</th>
      																<th>Dia.Wt</th>
      																<th>Purity</th>
      																<th>Rate Per gram</th>
      																<th>Value (Rs)</th>
      																<th>Action</th>
      															</tr>
      														</thead>
      														<tbody></tbody>
      														<tfoot>
      														</tfoot>
      													</table>
      												</div>

      												<div class="table-responsive tagged_item_melting_details" style="display:none;">
      													<table id="tagged_pocket_details" class="table table-bordered table-striped text-center">
      														<thead>
      															<tr>
      																<th><input type="checkbox" id="tag_select_all" name="tag_select_all" value="all" />Pocket No</label></th>
      																<th>Tag Code</th>
      																<th>Pcs</th>
      																<th>G.Wt (Grams)</th>
      																<th>N.Wt (Grams)</th>
      																<th>Purity</th>
      																<th>Value(Rs)</th>
      																<th>Action</th>
      															</tr>
      														</thead>
      														<tbody></tbody>
      														<tfoot>
      															<tr style="font-weight:bold;">
      																<td>Total</td>
      																<input type="hidden" class="total_pcs" name="process[tag_piece]">
      																<input type="hidden" class="tot_gross_wt" name="process[tag_gross_wt]">
      																<input type="hidden" class="tot_net_wt" name="process[tag_net_wt]">
      																<input type="hidden" class="tot_avg_purity" name="process[tag_purity]">
      																<input type="hidden" class="tag_tot_amount" name="process[tag_amount]">
      																<td></td>
      																<td width="10%"><input type="text" class="tot_pcs" disabled="true" placeholder="Pieces" /></td>
      																<td width="10%"><input type="text" class="tot_gwt" disabled="true" placeholder="Gross Weight" /></td>
      																<td width="10%"><input type="text" class="tot_nwt" disabled="true" placeholder="Net Weight" /></td>
      																<td width="10%"><input type="text" class="tot_purity" disabled="true" placeholder="Purity" /></td>
      																<td><input type="text" class="tot_value" disabled="true" placeholder="Total Value" /></td>
      																<td></td>
      															</tr>
      														</tfoot>

      													</table>
      												</div>

      												<div class="table-responsive non_tagged_item_melting_details" style="display:none;">
      													<table id="non_tagged_pocket_details" class="table table-bordered table-striped text-center">
      														<thead>
      															<tr>
      																<th><input type="checkbox" id="non_tag_select_all" name="non_tag_select_all" value="all" />Pocket No</label></th>
      																<th>Product</th>
      																<th>Pcs</th>
      																<th>G.Wt (Grams)</th>
      																<th>N.Wt (Grams)</th>
      																<th>Action</th>
      															</tr>
      														</thead>
      														<tbody></tbody>
      														<tfoot>
      															<tr style="font-weight:bold;">
      																<td>Total</td>
      																<input type="hidden" class="total_pcs" name="process[non_tag_piece]">
      																<input type="hidden" class="tot_gross_wt" name="process[non_tag_gross_wt]">
      																<input type="hidden" class="tot_net_wt" name="process[non_tag_net_wt]">
      																<td></td>
      																<td><input type="text" class="tot_pcs" disabled="true" placeholder="Pieces" /></td>
      																<td><input type="text" class="tot_gwt" disabled="true" placeholder="Gross Weight" /></td>
      																<td><input type="text" class="tot_nwt" disabled="true" placeholder="Net Weight" /></td>
      																<td></td>
      															</tr>
      														</tfoot>
      													</table>
      												</div>

      											</div>
      										</div>
      									</div>
      								</div>
      							</div>

      							<div class="box box-default polish_issue" style="display:none;">
      								<div class="box-header with-border">
      									<div class="box-body">
      										<div class="row">
      											<div class="col-md-12">
      												<div class="col-md-2">
      													<label>Select Pocket</label></br>
      													<select class="form-control" id="select_polish_pocket" style="width:100%;"></select>
      												</div>
      											</div>
      										</div>
      										<div class="row">
      											<div class="box-body">
      												<div class="table-responsive">
      													<table id="polish_pocket_details" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">
      														<thead>
      															<tr>
      																<th>Pocket No</th>
      																<th>Metal Type</th>
      																<th>Pcs</th>
      																<th>G.Wt (Grams)</th>
      																<th>N.Wt (Grams)</th>
      																<th>Dia Wt (Grams)</th>
      																<th>Purity</th>
      																<th>Value (Rs)</th>
      																<th>Issue Pcs</th>
      																<th>Issue Gwt</th>
      																<th>Issue Nwt</th>
      																<th>Issue Purity</th>
      																<th>Avg Purity</th>
      																<th>Action</th>
      															</tr>
      														</thead>
      														<tbody></tbody>
      														<tfoot></tfoot>
      													</table>
      												</div>
      											</div>
      										</div>
      									</div>
      								</div>
      							</div>

      							<div class="box box-default testing_issue" style="display:none;">
      								<div class="box-header with-border">
      									<div class="box-body">
      										<div class="row">
      											<div class="box-body">
      												<div class="table-responsive">
      													<table id="testing_process_details" class="table table-bordered table-striped text-center">
      														<thead>
      															<tr>
      																<th>Process No</th>
      																<th>Category Name</th>
      																<th>Act Wt (Grams)</th>
      																<th>Received Wt (Grams)</th>
      																<th>Purity</th>
      																<th>Rate</th>
      																<th>Value (Rs)</th>
      																<th>Action</th>
      															</tr>
      														</thead>
      														<tbody></tbody>
      														<tfoot></tfoot>
      													</table>
      												</div>
      											</div>
      										</div>
      									</div>
      								</div>
      							</div>

      							<div class="box box-default refining_issue" style="display:none;">
      								<div class="box-header with-border">
      									<div class="box-body">
      										<div class="row">
      											<div class="box-body">
      												<div class="table-responsive">
      													<table id="refining_issue_details" class="table table-bordered table-striped text-center">
      														<thead>
      															<tr>
      																<th>Process No</th>
      																<th>Category Name</th>
      																<th>Wt(Grams)</th>
      																<th>Purity</th>
      																<th>Value (Rs)</th>
      																<th>Action</th>
      															</tr>
      														</thead>
      														<tbody></tbody>
      														<tfoot></tfoot>
      													</table>
      												</div>
      											</div>
      										</div>
      									</div>
      								</div>
      							</div>

      							<section class="content" id="receipt_det" style="display:none;">
      								<div align="left" style="background: #f5f5f5">
      									<ul class="nav nav-tabs">
      										<li class="active"><a id="tab_receipt_details" href="#receipt_details" data-toggle="tab">Receipt Details</a></li>
      										<!--<li id="tab_payment_details"><a href="#payment_details" data-toggle="tab">Payment Details</a></li>-->
      									</ul>
      								</div>
      								<div class="tab-content" style="background: #f5f5f5">
      									<input type="hidden" id="active_id">
      									<div class="tab-pane active" id="receipt_details">
      										<div class="box box-default melting_receipt" style="display:none;">
      											<div class="box-header with-border">
      												<div class="box-body">
      													<div class="row">
      														<div class="box-body">
      															<div class="table-responsive">
      																<table id="melting_receipt" class="table table-bordered table-striped text-center">
      																	<thead>
      																		<tr>
      																			<th>Process No</th>
      																			<th>G.Wt (Grams)</th>
      																			<th>N.Wt (Grams)</th>
      																			<th>Purity</th>
      																			<th>Value (Rs)</th>
      																			<th>Category</th>
      																			<th>Recd WT(Grams)</th>
      																			<th>Prod Loss(Grams)</th>
      																			<th>Value(Rs)</th>
      																			<th>Charges(Rs)</th>
      																			<th>Ref No</th>
      																			<th>Action</th>
      																		</tr>
      																	</thead>
      																	<tbody></tbody>
      																	<tfoot></tfoot>
      																</table>
      															</div>
      														</div>
      													</div>
      												</div>
      											</div>
      										</div>

      										<div class="box box-default testing_receipt" style="display:none;">
      											<div class="box-header with-border">
      												<div class="box-body">

      													<div class="row">
      														<div class="box-body">
      															<div class="table-responsive">
      																<table id="testing_receipt" class="table table-bordered table-striped text-center">
      																	<thead>
      																		<tr>
      																			<th>Process No</th>
      																			<th>Category Name</th>
      																			<th>N.Wt (Grams)</th>
      																			<th>Purity</th>
      																			<th>Value (Rs)</th>
      																			<th>Recd WT(Grams)</th>
      																			<th>Prod Loss(Grams)</th>
      																			<th>Recd Purity(%)</th>
      																			<th>Value(Rs)</th>
      																			<th>Charges(Rs)</th>
      																			<th>Ref No</th>
      																			<th>Action</th>
      																		</tr>
      																	</thead>
      																	<tbody></tbody>
      																	<tfoot></tfoot>
      																</table>
      															</div>
      														</div>
      													</div>
      												</div>
      											</div>
      										</div>
      										<div class="box box-default against_melting_receipt" style="display:none;">
      											<div class="box-header with-border">
      												<div class="box-body">
      													<div class="row">
      														<div class="col-md-12">
      															<div class="col-md-2">
      																<label>Select Process</label></br>
      																<select class="form-control" id="select_metal_process" style="width:100%;"></select>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="row" style="font-size:16px;">
      															<input type="radio" class="add_to_next_process" id="add_to_next_process" name="process[process_type]" value="1" checked> <label for="add_to_next_process">Add to Next Process</label>
      															<input type="radio" class="add_to_acc_stock" id="add_to_acc_stock" name="process[process_type]" value="2"> <label for="add_to_acc_stock">Add to Stock</label>
      														</div>
      														<div class="box-body">
      															<div class="table-responsive">
      																<table id="against_melting_receipt" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">
      																	<thead>
      																		<tr>
      																			<th>Process No</th>
      																			<th>Vendor</th>
      																			<th>Category</th>
      																			<th>Actual Weight(Grams)</th>
      																			<th>Recd WT(Grams)</th>
      																			<th>Purity</th>
      																			<th>Action</th>
      																		</tr>
      																	</thead>
      																	<tbody></tbody>
      																	<tfoot></tfoot>
      																</table>
      															</div>
      														</div>
      													</div>

      													<div class="row">
      														<label>Testing Issue Details</label>
      														<div class="box-body">
      															<div class="table-responsive">
      																<table id="testing_issue_details" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">
      																	<thead>
      																		<tr>
      																			<th>Process No</th>
      																			<th>Vendor</th>
      																			<th>Category</th>
      																			<th>Actual Weight(Grams)</th>
      																			<th>Recd WT(Grams)</th>
      																			<th>Pord Loss WT(Grams)</th>
      																			<th>Purity</th>
      																		</tr>
      																	</thead>
      																	<tbody></tbody>
      																	<tfoot></tfoot>
      																</table>
      															</div>
      														</div>
      													</div>

      												</div>
      											</div>
      										</div>


      										<div class="box box-default against_refining_receipt" style="display:none;">
      											<div class="box-header with-border">
      												<div class="box-body">
      													<div class="row">
      														<div class="col-md-12">
      															<div class="col-md-2">
      																<label>Select Process</label></br>
      																<select class="form-control" id="select_refining_process" style="width:100%;"></select>
      															</div>
      														</div>
      													</div>
      													<div class="row">
      														<div class="box-body">
      															<div class="table-responsive">
      																<table id="against_refining_receipt" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">
      																	<thead>
      																		<tr>
      																			<th>Process No</th>
      																			<th>Recd WT(Grams)</th>
      																			<th>Purity</th>
      																			<th>Action</th>
      																		</tr>
      																	</thead>
      																	<tbody></tbody>
      																	<tfoot></tfoot>
      																</table>
      															</div>
      														</div>
      													</div>
      												</div>
      											</div>
      										</div>

      										<div class="box box-default refining_receipt" style="display:none;">
      											<div class="box-header with-border">
      												<div class="box-body">
      													<div class="row">
      														<div class="box-body">
      															<div class="table-responsive">

      																<table id="refining_receipt" class="table table-bordered table-striped text-center">
      																	<thead>
      																		<tr>
      																			<th>Process No</th>
      																			<th>Category Name</th>
      																			<th>N.Wt (Grams)</th>
      																			<th>Purity</th>
      																			<th>Value (Rs)</th>
      																			<th>Category</th>
      																			<th>Recd WT(Grams)</th>
      																			<th>Prod Loss(Grams)</th>
      																			<th>Charges(Rs)</th>
      																			<th>Tax (%)</th>
      																			<th>Tax Value</th>
      																			<th>Final Value(Rs)</th>
      																			<th>Ref No</th>
      																			<th>Action</th>
      																		</tr>
      																	</thead>
      																	<tbody></tbody>
      																	<tfoot></tfoot>
      																</table>
      															</div>
      														</div>
      													</div>
      												</div>
      											</div>
      										</div>

      										<div class="box box-default polishing_receipt" style="display:none;">
      											<div class="box-header with-border">
      												<div class="box-body">
      													<div class="row">
      														<div class="box-body">
      															<div class="table-responsive">
      																<table id="polishing_receipt" class="table table-bordered table-striped text-center">
      																	<thead>
      																		<tr>
      																			<th>Process No</th>
      																			<th>Metal Type</th>
      																			<th>G.Wt (Grams)</th>
      																			<th>N.Wt (Grams)</th>
      																			<th>Dia Wt (Grams)</th>
      																			<th>Category</th>
      																			<th>Recd Pcs</th>
      																			<th>Recd GWT(Grams)</th>
      																			<th>Recd NWT(Grams)</th>
      																			<!--<th >Prod Loss(Grams)</th>-->
      																			<th>Charges(Rs)</th>
      																			<th>Ref No</th>
      																			<th>Action</th>
      																		</tr>
      																	</thead>
      																	<tbody></tbody>
      																	<tfoot></tfoot>
      																</table>
      															</div>
      														</div>
      													</div>

      												</div>
      											</div>
      										</div>

      									</div>

      									<!-- <div class="tab-pane" id="payment_details">	 -->
      									<!-- <div class="box box-default receipt_Payment">
                                    <div class="box-header with-border">
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="box-body">
            								        <div class="table-responsive">
            								             <table id="receipt_payment_details" class="table table-bordered table-striped text-center">
            								                 <thead>
            										            <tr>
            										                <th width="10%;">Pay Mode</th>
            										                <th width="10%;">Amount</th>
            										                <th width="10%;">Ref No</th>
            										          </thead>
            										          <tbody>
            										              <tr>
            										                  <td>CASH</td>
            										                  <td><input type="number" name="receipt_payment[cash_amount]" class="form-control cash_amt" placeholder="Enter Amount"></td>
            										                  <td><input type="number" class="form-control" placeholder="Enter Ref No"></td>
            										              </tr>
            										              <tr>
            										                  <td>Net Banking</td>
            										                  <td><input type="number" name="receipt_payment[net_banking_amount]" class="form-control net_bank_amt" placeholder="Enter Amount"></td>
            										                  <td><input type="number" name="receipt_payment[net_banking_ref_no]" class="form-control" placeholder="Enter Ref No"></td>
            										              </tr>
            										          </tbody>
            										           <tfoot>
            										               <tr style="font-weight:bold;">
            										                  <td>Total</td>
            										                  <td><span class="total_amount"></span></td>
            										                  <td></td>
            										              </tr>
            										           </tfoot>
            								             </table>
            								        </div>
            								    </div>
                                            </div>
                                        </div>
                                    </div>
            					</div>
    				        </div> -->
      								</div>
      							</section>
      						</div> <!--/ Col -->
      				</div> <!--/ row -->


      				<div class="row">
      					<div class="col-sm-6 ">
      						<label>Remark</label>
      						<textarea class="form-control" id="remark" name="process[remark]" rows="5" cols="100" style="width: 452px; height: 100px;"></textarea>
      					</div>
      				</div><br>
					
      				<p class="help-block"> </p>
      				<div class="row">
      					<div class="box box-default"><br />
      						<div class="col-xs-offset-5">
      							<button type="button" id="issue_submit" class="btn btn-primary">Save</button>
      							<button type="button" class="btn btn-default btn-cancel">Cancel</button>
      						</div> <br />
      					</div>
      				</div>
      			</div>
      			<?php echo form_close(); ?>
      			<div class="overlay" style="display:none">
      				<i class="fa fa-refresh fa-spin"></i>
      			</div>
      			<!-- /form -->
      		</div>
      	</section>
      </div>

      <!-- / emp modal -->




      <!--  Image Upload-->
      <div class="modal fade" id="category_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      	<div class="modal-dialog" style="width:60%;">
      		<div class="modal-content">
      			<div class="modal-header">
      				<h4 class="modal-title" id="myModalLabel">Add Weight</h4>
      			</div>
      			<div class="modal-body">
      				<!--<div class="row">
                    <div class="box-tools pull-right">
                        <button type="button" id="add_new_category" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
                    </div>
                </div>-->
      				<div class="table-responsive">
      					<table id="category_row" class="table table-bordered table-striped text-center">
      						<thead>
      							<tr>
      								<th width="10%;">Category</th>
      								<th width="10%;">Section</th>
      								<th width="10%;">Product</th>
      								<th width="10%;">Design</th>
      								<th width="10%;">Sub Design</th>
      								<th width="10%;">Pcs</th>
      								<th width="10%;">Weight</th>
      								<th width="10%;">Action</th>
      						</thead>
      						<tbody>
      						</tbody>
      					</table>
      				</div>
      				<br>
      			</div>

      			<div class="modal-footer">
      				<button type="button" id="update_category" class="btn btn-success">Save</button>
      				<button type="button" id="close_stone_details_new" class="btn btn-warning" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
      </div>


      <div class="modal fade" id="refining_category_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      	<div class="modal-dialog" style="width:60%;">
      		<div class="modal-content">
      			<div class="modal-header">
      				<h4 class="modal-title" id="myModalLabel">Add Weight</h4>
      			</div>
      			<div class="modal-body">
      				<div class="row">
      					<div class="box-tools pull-right">
      						<button type="button" id="add_new_ref_category" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
      					</div>
      				</div>
      				<div class="table-responsive">
      					<table id="category_row" class="table table-bordered table-striped text-center">
      						<thead>
      							<tr>
      								<th width="10%;">Category</th>
      								<th width="10%;">Section</th>
      								<th width="10%;">Product</th>
      								<th width="10%;">Design</th>
      								<th width="10%;">Sub Design</th>
      								<th width="10%;">Purity</th>
      								<th width="10%;">Weight</th>
      								<th width="10%;">Action</th>
      						</thead>
      						<tbody>
      						</tbody>
      					</table>
      				</div>
      				<br>
      			</div>

      			<div class="modal-footer">
      				<button type="button" id="update_refining_category" class="btn btn-success">Save</button>
      				<button type="button" id="close_stone_details_new" class="btn btn-warning" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
      </div>



      <div class="modal fade" id="polishing_category_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      	<div class="modal-dialog" style="width:88%;">
      		<div class="modal-content">
      			<div class="modal-header">
      				<h4 class="modal-title" id="myModalLabel">Add Weight</h4>
      			</div>
      			<div class="modal-body">
      				<div class="row">
      					<div class="box-tools pull-right">
      						<button type="button" id="add_new_polishing_category" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
      					</div>
      				</div>
      				<div class="table-responsive">
      					<table id="category_row" class="table table-bordered table-striped text-center">
      						<thead>
      							<tr>
      								<th width="10%;">Is Non Tag</th>
      								<th width="10%;">Category</th>
      								<th width="10%;">Section</th>
      								<th width="10%;">Product</th>
      								<th width="10%;">Design</th>
      								<th width="10%;">Sub Design</th>
      								<th width="10%;">Purity</th>
      								<th width="10%;">Pcs</th>
      								<th width="10%;">Gwt</th>
      								<th width="10%;">Nwt</th>
      								<th width="10%;">Dia wt</th>
      								<th width="10%;">Action</th>
      						</thead>
      						<tbody>
      						</tbody>
      					</table>
      				</div>
      				<br>
      			</div>

      			<div class="modal-footer">
      				<button type="button" id="update_polishing_category" class="btn btn-success">Save</button>
      				<button type="button" id="close_stone_details_new" class="btn btn-warning" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
      </div>