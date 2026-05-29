<!-- Content Wrapper. Contains page content -->



<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Reserve Order
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Stock Report</a></li>
			<li class="active">Reserve Order</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xs-12">

				<div class="box box-primary">
					<div class="box-body">
						<div class="box box-info stock_details">
							<div class="box-header with-border">
								<div class="row">
									<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
										<div class="col-md-2">
											<label>Branch</label>
											<select class="form-control" id="branch_select" style="width:100%;"></select>
										</div>
									<?php } else { ?>
										<input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
									<?php } ?>
									<div class="col-md-2">
										<label>Select Order No</label>
										<select id="select_order" class="form-control" style="width:100%;"></select>
									</div>
									<div class="col-md-2">
										<label>Select Status</label>
										<select id="status" class="form-control" style="width:100%;">
											<option value="">Select Type</option>
											<option value="3">Work In Process</option>
											<option value="4">Delivery Ready</option>
										</select>
									</div>
									<div class="col-md-2">
										<label>Customer</label>
										<input class="form-control" type="text" placeholder="Enter Customer" id="ro_cus_name" autocomplete="off">
										<input type="hidden" id="ro_cus_id">

									</div>


									<div class="col-md-2">
										<label>Select Product</label>
										<select id="prod_select" class="form-control" style="width:100%;"></select>
									</div>
									<div class="col-md-2">
										<label>Select Design</label>
										<select id="des_select" class="form-control" style="width:100%;"></select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-2">
										<label>Select Sub Design</label>
										<select id="sub_des_select" class="form-control" style="width:100%;"></select>
									</div>

									<div class="col-md-2"> 

                                <div class="form-group">
                                    <div class="input-group">
                                      <label>Date</label>
                                      <br>
                                      <button class="btn btn-default btn_date_range" id="rpt_date_picker">
                                      <i class="fa fa-calendar"></i> Date range picker
                                      <i class="fa fa-caret-down"></i>
                                      </button>
                                      <span  style="display:none;" id="rpt_from_date"></span>
                                    <span  style="display:none;" id="rpt_to_date"></span>
                                      <span id="reportrange"></span>
                                    </div>
                                    </div>
                                    </div>
									<div class="col-md-2">
										<label></label>
										<div class="form-group">
											<button type="button" id="reserve_order_search" class="btn btn-info">Search</button>
										</div>
									</div>

								</div>

							</div>
							<div class="box-body">
								<div class="row">
									<div class="box-body">
										<div class="table-responsive">
											<table id="reserve_order_list" class="table table-bordered table-striped text-center">
												<thead>

													<tr>
														<th width="2%">#</th>
														<th width="7%">Branch</th>
														<th width="9%">Customer</th>
														<th width="9%">Order No</th>
														<th width="7%">Date</th>
														<th width="9%">Old Tag Id</th>
														<th width="10%">Tag Code</th>
														<th width="10%">Product</th>
														<th width="10%">Design</th>
														<th width="10%">Sub Design</th>
														<th width="7%">G.Wt</th>
														<th width="7%">N.Wt</th>
														<th width="10%">Status</th>
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
					</div>
					<div class="overlay" style="display:none">
						<i class="fa fa-refresh fa-spin"></i>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>