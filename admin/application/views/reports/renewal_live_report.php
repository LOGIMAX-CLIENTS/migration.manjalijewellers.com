<style>
	@media print {
		.bold-on-print {
			font-weight: bold !important;
		}
	}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Renewal/Live Report
			<!-- <span id="total" class="badge bg-green"></span> -->
			<!-- <span class="badge bg-green" style="font-size: 14px;margin-left: 130px;color:#000;">Amount in : <b class="" id="currency_symbol"></b>
       &nbsp; | &nbsp; Weight in : <b >Gram</b>
    </span>  -->
			<small></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Reports</a></li>
			<li class="active">Renewal/Live Report</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content report_sel">
		<div class="row">
			<div class="col-xs-12">

				<div class="box box-primary">
					<!--  <div class="box-header with-border">
                
                         
                </div> -->
					<!-- /.box-header -->
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
						<div class="row">
							<div class="col-md-12">
							<?php if($this->session->userdata('branch_settings')==1){ ?>
								<div class="col-md-2">
									<div class="form-group">
										<label>Branch</label>
										<select id="branch_select" class="form-control">

										</select>
									</div>
								</div>
							<?php } ?>
								<div class="col-md-2">
									<div class="form-group">
										<!--<label>Date Picker</label>	-->
										<div class="input-group">
											<span id="renewal_livereport_date_range" style="font-weight:bold;"></span><br />
											<button class="btn btn-default btn_date_range" id="renew_payment_date">
												<!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
												<span style="display:none;" id="renew_payments1"></span>
												<span style="display:none;" id="renew_payments2"></span>
												<i class="fa fa-calendar"></i>
												Closed Date
												<i class="fa fa-caret-down"></i>
											</button>
										</div>
									</div><!-- /.form group -->
								</div>

								<div class="col-md-2" style=" margin-left: -41px;">
									<div class="form-group">
										<label>Referred Employee</label>
										<select id="employee_select" class="form-control" style="width:200px; margin-left: 15px !important;"></select>
										<input id="ref_code" name="ref_code" type="hidden" value="" />
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										<label>Scheme</label>
										<select id="scheme_select" class="form-control">

										</select>
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										<label>Filter By Renewals</label>
										<select id="renew_type" class="form-control">
											<option value="0">All</option>
											<option value="1">Renewed</option>
											<option value="2">Not Renewed</option>
										</select>
									</div>
								</div>


								<div class="col-md-2">
									<div class="form-group">
										<label>Report Group By</label>
										<select id="renew_groupBy" class="form-control">
										    <option value="2">Customer wise</option>
											<option value="1">Referred Employee-wise</option>
											
										</select>
									</div>
								</div>
							</div>

						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="col-md-1 pull-right">
									<!-- <label></label> -->
									<div class="form-group">
										<button type="button" id="search_renewal_list" class="btn btn-info pull-right">Search</button>
									</div>
								</div>
							</div>
						</div>
						<!-- </br> -->


						<div class="row">
							<div class="box-body">
								<div class="table-responsive">
									<table id="renewal_live_report" class="table table-bordered table-striped text-center cell-border ">

										<thead style="font-size:15px;">

											<tr>
												<th colspan="3" style="color:#177ec5;">Customer</th>
												<th colspan="2" style="color:#E74C3C;">Closed Accounts</th>
												<th colspan="2" style="color:#7D3C98;">Renewal Accounts</th>
												<th colspan="2" style="color:#28B463;">Active Accounts</th>
											</tr>
											<tr>
												<th width="2%" style="color:#177ec5;">S.No</th>
												<th style="color:#177ec5;">Cus Name</th>
												<th width="5%" style="color:#177ec5;">Mobile</th>
												<th style="color:#E74C3C;">Count</th>
												<th style="color:#E74C3C;">Acc</th>
												<th style="color:#7D3C98;">Count</th>
												<th style="color:#7D3C98;">Acc</th>
												<th style="color:#28B463;">Count</th>
												<th style="color:#28B463;">Acc</th>
											</tr>
										</thead>
										<tbody>

										</tbody>
									</table>
								</div>

							</div>
						</div>
						<!-- /.box-body -->
					</div><!-- /.box -->
					<div class="overlay" style="display:block">
						<i class="fa fa-refresh fa-spin"></i>
					</div>
				</div><!-- /.col -->
			</div><!-- /.row -->
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- / modal -->