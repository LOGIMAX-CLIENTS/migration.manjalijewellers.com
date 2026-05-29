<!-- Content Wrapper. Contains page content -->







<div class="content-wrapper">



	<!-- Content Header (Page header) -->



	<section class="content-header">



		<h1>



			Reports



			<small>Sales Report</small>



		</h1>



		<ol class="breadcrumb">



			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>



			<li><a href="#">Reports</a></li>



			<li class="active">Sales Report</li>



		</ol>



	</section>







	<!-- Main content -->



	<section class="content">



		<div class="row">



			<div class="col-xs-12">







				<div class="box box-primary">



					<div class="box-header with-border">



						<h3 class="box-title">Employee Sales Referrals</h3> <span id="total_count"
							class="badge bg-green"></span>







					</div>



					<div class="box-body">



						<div class="row">



							<div class="col-md-offset-2 col-md-10">



								<div class="box box-default">



									<div class="box-body">



										<div class="row">



											<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>



												<div class="col-md-2">



													<div class="form-group tagged">



														<label>Select Branch</label>



														<select id="branch_select"
															class="form-control branch_filter"></select>



													</div>



												</div>



											<?php } else { ?>



												<input type="hidden" id="branch_filter"
													value="<?php echo $this->session->userdata('id_branch') ?>">



											<?php } ?>







											<div class="col-md-2">



												<div class="form-group">



													<label>Date</label>



													<?php



													$fromdt = date("d/m/Y");



													$todt = date("d/m/Y");



													?>



													<input type="text" class="form-control pull-right dateRangePicker"
														id="dt_range" placeholder="From Date -  To Date"
														value="<?php echo $fromdt . ' - ' . $todt ?>" readonly="">



												</div>



											</div>



											<!--<div class="col-md-2"> 



									<label>Group By</label>



									<select id="est_ref_group_by" class="form-control" style="width:100%;"></select>



								</div>-->







											<div class="col-md-2">



												<label>Select Metal</label>



												<select id="metal" class="form-control" style="width:100%;" multiple></select>



											</div>



											<div class="col-md-2">

												<label>Select Report Type</label>

												<select id="id_report_type" class="form-control" style="width:100%;">

													<option value="1">Detailed</option>

													<option value="2">Summary</option>

												</select>

											</div>



											<div class="col-md-2">



												<label>Select Category</label>



												<select id="category" class="form-control" style="width:100%;"></select>



											</div>







											<div class="col-md-2">



												<label>Select Product</label>



												<select id="prod_select" class="form-control" style="width:100%;"
													multiple></select>



											</div>

											</div>

											<div class="row">


											<div class="col-md-2">



												<label>Select Design</label>



												<select id="des_select" class="form-control" style="width:100%;"
													multiple></select>



											</div>


											<div class="col-md-2">



												<label>Select Sub Design</label>



												<select id="sub_des_select" class="form-control" style="width:100%;"
													multiple></select>



											</div>

											<div class="col-md-2">



											<label>Select Employee</label>



											<select id="emp_select" class="form-control" style="width:100%;" multiple></select>



											</div>



											<div class="col-md-2">



												<label></label>



												<div class="form-group">



													<button type="button" id="ref_search"
														class="btn btn-info">Search</button>



												</div>



											</div>



										</div>



									</div>



								</div>



							</div>



						</div>







						<div class="row">



							<div class="col-xs-12">



								<!-- Alert -->



								<?php



								if ($this->session->flashdata('chit_alert')) {



									$message = $this->session->flashdata('chit_alert');



									?>



									<div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">



										<button type="button" class="close" data-dismiss="alert"
											aria-hidden="true">&times;</button>



										<h4><i class="icon fa fa-check"></i>
											<?php echo $message['title']; ?>!
										</h4>



										<?php echo $message['message']; ?>



									</div>







								<?php } ?>



							</div>



						</div>



						<div class="box box-info stock_details">

							<div class="box-header with-border">

								<h3 class="box-title">Sales Details</h3>

								<div class="box-tools pull-right">

									<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
										title="Collapse"><i class="fa fa-minus"></i></button>

								</div>

							</div>

							<div class="box-body">

								<div class="row">

									<div class="box-body">

										<div class="table-responsive ref_list_sum" style="Display:none;">

											<table id="ref_list_sum"
												class="table table-bordered table-striped text-center">

												<thead>

													<tr style="text-transform:uppercase;">

														<th width="10%">Branch Name</th>

														<th width="10%">Employee Name</th>

														<th width="10%">Employee Code</th>

														<th width="10%">No of Sales</th>

														<th width="10%">Pcs</th>

														<th width="10%">GWT</th>

														<th width="10%">LWt</th>

														<th width="10%">NWt</th>

														<th width="10%">DIA WT</th>

														<th width="10%">Total Bill Amount</th>

													</tr>

												</thead>

												<tbody></tbody>

												<tfoot style="font-weight:bold;">

													<tr>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
													</tr>
												</tfoot>



											</table>

										</div>

										<div class="table-responsive ref_list_det">

											<table id="ref_list_det"
												class="table table-bordered table-striped text-center">

												<thead>
													<tr style="text-transform: uppercase;">
														<th rowspan="2">S.No</th>
														<!-- <th rowspan="2">Bill no</th> -->
														<th rowspan="2">Tag code</th>
														<th rowspan="2">Old Tag code</th>
														<th rowspan="2">Product</th>
														<th rowspan="2">Design</th>
														<th rowspan="2">Sub Design</th>
														<th rowspan="2">Pcs</th>
														<th rowspan="2">GWT</th>
														<th rowspan="2">LWt</th>
														<th rowspan="2">NWt</th>
														<th rowspan="2">DIA WT</th>
														<th colspan="3">Stock</th>
														<th colspan="3">Sale</th>
														<th colspan="3">Differ</th>
														<th rowspan="2">Amount</th>
													</tr>
													<tr style="text-transform: uppercase;">
														<th>Wast %</th>
														<th>Wast Wt</th>
														<th>MC</th>
														<th>Wast %</th>
														<th>Wast Wt</th>
														<th>MC</th>
														<th>Wast %</th>
														<th>Wast Wt</th>
														<th>MC</th>
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



					</div><!-- /.box-body -->



					<div class="overlay" style="display:none">



						<i class="fa fa-refresh fa-spin"></i>



					</div>



				</div>



			</div><!-- /.col -->



		</div><!-- /.row -->



	</section><!-- /.content -->



</div><!-- /.content-wrapper -->