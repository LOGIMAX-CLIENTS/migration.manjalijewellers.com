<!-- Content Wrapper. Contains page content -->
<style>
#inspect_table { width: 100% !important; }
#inspect_table th, #inspect_table td { padding: 6px 10px !important; white-space: nowrap; font-size: 13px; }
.badge-completed { background-color: #00a65a; color: #fff; padding: 3px 10px; border-radius: 3px; font-size: 12px; }
.badge-pending { background-color: #f39c12; color: #fff; padding: 3px 10px; border-radius: 3px; font-size: 12px; }
.badge-no-entry { background-color: #d2d6de; color: #666; padding: 3px 10px; border-radius: 3px; font-size: 12px; }
</style>
<div class="content-wrapper">
 	<!-- Content Header (Page header) -->
 	<section class="content-header">
 		<h1>Physical Stock Inspect Report</h1>
 		<ol class="breadcrumb">
 			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
 			<li><a href="#">Reports</a></li>
 			<li class="active"> Physical Stock Inspect Report</li>
 		</ol>
 	</section>

 	<!-- Main content -->
 	<section class="content">
 		<div class="row">
 			<div class="col-xs-12">
 				<div class="box box-primary">
 					<div class="box-body">
 						<div class="box box-info">
 							<div class="box-header with-border">
 								<div class="row">
 									<div class="col-md-12">
									<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
										<div class="col-md-3">
											<div class="form-group">
												<label>Select Branch</label>
												<select id="inspect_branch_select" class="form-control branch_filter" style="width:100%;" multiple></select>
											</div>
										</div>
									<?php } else { ?>
										<input type="hidden" id="inspect_branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
									<?php } ?>
										<input type="hidden" id="inspect_stock_audit_by" value="<?php echo isset($stock_audit_by) ? $stock_audit_by : 1; ?>">

										<div class="col-md-3">
									<div class="form-group">
										<label>Date Range</label>
										<?php $from_dt = date("01/m/Y"); $to_dt = date("d/m/Y"); ?>
										<input type="text" class="form-control" id="inspect_date_range" placeholder="Select Date Range" value="<?php echo $from_dt . ' - ' . $to_dt; ?>" style="cursor:pointer;" readonly>
									</div>
								</div>

										<div class="col-md-2">
											<label>&nbsp;</label>
											<div class="form-group">
												<button type="button" id="inspect_search_btn" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
											</div>
										</div>
 									</div>
								</div><!-- end row -->
 							</div><!-- end box-header -->
 						</div><!-- end box-info -->

 						<div class="box-body">
 							<div class="row">
 								<div class="box-body">
 									<div class="table-responsive">
 										<table id="inspect_table" class="table table-bordered table-striped">
 											<thead>
 												<tr>
 													<th style="width:50px;">S.No</th>
 													<th>Date</th>
 													<th>Employee(s)</th>
 													<th style="text-align:center;">Entries</th>
 													<th style="text-align:center;">Status</th>
 													<th>Completed By</th>
 													<th>Completed On</th>
 													<th style="text-align:center;">Print</th>
 												</tr>
 											</thead>
 											<tbody></tbody>
 										</table>
 									</div>
 								</div>
 							</div>
 						</div>

 					</div><!-- end box-primary body -->
 				</div><!-- end box-primary -->

 				<div class="overlay" style="display:none">
 					<i class="fa fa-refresh fa-spin"></i>
 				</div>

 			</div><!-- /.col -->
 		</div><!-- /.row -->
 	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
