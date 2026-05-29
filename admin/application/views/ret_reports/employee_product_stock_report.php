<!-- Content Wrapper. Contains page content -->
<style>
#stock_list { width: auto !important; min-width: 50%; }
#stock_list th, #stock_list td { padding: 5px 8px !important; white-space: nowrap; font-size: 13px; }
.dataTables_wrapper { overflow-x: auto; }
.dataTables_scrollHeadInner, .dataTables_scrollBody table { width: auto !important; }
</style>
<div class="content-wrapper">
 	<!-- Content Header (Page header) -->
 	<section class="content-header">
 		<h1>Physical Stock Report</h1>
 		<ol class="breadcrumb">
 			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
 			<li><a href="#">Reports</a></li>
 			<li class="active"> Physical Stock Report</li>
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

 								<!-- ===== ROW 1: Branch, Date, List By, Metal, Employee ===== -->
 								<div class="row">
 									<div class="col-md-12">
 										<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
 											<div class="col-md-3">
 												<div class="form-group tagged">
 													<label>Select Branch</label>
 													<select id="branch_select" class="form-control branch_filter" style="width:100%;" multiple></select>
 												</div>
 											</div>
 										<?php } else { ?>
 											<input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
 											<input type="hidden" id="branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">
 										<?php } ?>
 										<input type="hidden" id="stock_audit_by" value="<?php echo isset($stock_audit_by) ? $stock_audit_by : 1; ?>">
 										<input type="hidden" id="select_group_by" value="1">

 										<div class="col-md-2">
 											<div class="form-group">
 												<label>Date</label>
 			 									<?php
									// Use print_date from URL if available, else today
									$fromdt = (!empty($print_date)) ? $print_date : date("d/m/Y");
									?>
 									<input type="text" class="form-control pull-right" id="dt_range" placeholder="Select Date" value="<?php echo $fromdt ?>" style="cursor:pointer;">
 											</div>
 										</div>

 										<div class="col-md-2">
 											<div class="form-group">
 												<label>List By</label>
 												<select id="list_by" class="form-control" style="width:100%;">
 													<option value="0">ALL</option>
 													<option value="1">Tagged</option>
 													<option value="2">Non-Tagged</option>
 												</select>
 											</div>
 										</div>

 										<div class="col-md-2" id="id_metal_div">
 											<div class="form-group">
 												<label>Select Metal</label>
 												<select class="form-control select2" multiple="multiple" id="id_metal" name="id_metal[]">
 													<option value="0">All Metal</option>
 												</select>
 											</div>
 										</div>

 										<div class="col-md-2" id="id_employee_div">
 											<div class="form-group">
 												<label>Select Employee</label>
 												<select class="form-control select2" id="id_employee" name="id_employee">
 													<option value="0">All Employee</option>
 												</select>
 											</div>
 										</div>
 									</div>
 								</div><!-- end row 1 -->

 								<!-- ===== ROW 2: Show Difference, Search, Update ===== -->
 								<div class="row" style="margin-top:5px;">
 									<div class="col-md-12">
 										<div class="col-md-2" id="id_diff_filter_div">
 											<div class="form-group">
 												<label>Show Difference</label>
 												<select class="form-control" id="diff_filter" style="width:100%;">
 													<option value="0">All Products</option>
 													<option value="1">Diff Pcs Only</option>
 													<option value="2">Diff Weight Only</option>
 													<option value="4">Non Entered Records</option>
 												</select>
 											</div>
 										</div>
 										<div class="col-md-2">
 											<label>&nbsp;</label>
 											<div class="form-group">
 												<button type="button" id="stock_product_detail_search" class="btn btn-info">Search</button>
 											</div>
 										</div>
 										<div class="col-md-2" style="display: none;">
 											<label>&nbsp;</label>
 											<div class="form-group">
 												<button type="button" id="stock_product_detail_update" class="btn btn-success">Update</button>
 											</div>
 										</div>
 										<div class="col-md-2">
 											<label>&nbsp;</label>
 											<div class="form-group">
 												<button type="button" id="mark_audit_complete_btn" class="btn btn-warning"><i class="fa fa-check-circle"></i> Mark Complete</button>
 											</div>
 										</div>
 										<div class="col-md-2">
 											<label>&nbsp;</label>
 											<div class="form-group" style="padding-top:5px;">
 												<span id="audit_status_badge"></span>
 											</div>
 										</div>
 									</div>
 								</div><!-- end row 2 -->

 							</div><!-- end box-header -->
 						</div><!-- end box-info -->

 						<div class="box-body">
 							<div class="row">
 								<div class="box-body">
 									<div class="table-responsive">
 										<table id="stock_list" class="table table-bordered table-striped text-center">
 											<thead>
 												<tr>
 													<th>Product</th>
 													<th id="th_sys_pcs">System pcs</th>
 													<th id="th_sys_wt">System Weight (gm)</th>
 													<th id="th_emp_pcs" style="cursor:pointer;color:#3c8dbc;" title="Click on pcs value to see employee entries">Entered pcs <i class="fa fa-info-circle"></i></th>
 													<th id="th_emp_wt" style="cursor:pointer;color:#3c8dbc;" title="Click on weight to see employee entries">Entered Weight (gm) <i class="fa fa-info-circle"></i></th>
 													<th id="th_diff_pcs">Difference Pcs</th>
 													<th id="th_diff_wt">Difference Weight</th>
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

<!-- Entry Log Modal -->
<div class="modal fade" id="entry_log_modal" tabindex="-1" role="dialog" aria-labelledby="entryLogLabel">
  <div class="modal-dialog" style="width:65%;" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc;color:#fff;">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff;"><span>&times;</span></button>
        <h4 class="modal-title" id="entryLogLabel"><i class="fa fa-list"></i> Employee Entry Log</h4>
      </div>
      <div class="modal-body">
        <p id="entry_log_product_info" class="text-muted" style="margin-bottom:10px;"></p>
        <div class="table-responsive">
          <table id="entry_log_table" class="table table-bordered table-striped text-center">
            <thead style="background:#3c8dbc;color:#fff;">
              <tr>
                <th>#</th>
                <th>Employee</th>
                <th>Branch</th>
                <th>Pcs / Weight</th>
                <th>Type</th>
                <th>Date &amp; Time</th>
              </tr>
            </thead>
            <tbody id="entry_log_tbody">
              <tr><td colspan="6">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div><!-- /entry_log_modal -->

<script>
// Handle URL parameters from Physical Stock Inspect page (print_date, print_branch, auto_print)
$(document).ready(function() {
	var urlParams = new URLSearchParams(window.location.search);
	var printDate = urlParams.get('print_date');
	var printBranch = urlParams.get('print_branch');
	var autoPrint = urlParams.get('auto_print');

	if (printDate && printDate != '') {
		// Set the date field with the provided date (format: dd/mm/yyyy)
		$('#dt_range').val(printDate);
	}

	if (printBranch && printBranch != '') {
		// Set branch filter if it exists (hidden input for non-HO users)
		if ($('#branch_filter').length > 0) {
			$('#branch_filter').val(printBranch);
		}
		// For HO users with branch_select multi-select
		if ($('#branch_select').length > 0) {
			$('#branch_select').val([printBranch]).trigger('change');
		}
	}

	// Auto-trigger search if print_date is provided
	if (printDate && printDate != '') {
		setTimeout(function() {
			$('#stock_product_detail_search').trigger('click');
		}, 500);
	}
});
</script>
