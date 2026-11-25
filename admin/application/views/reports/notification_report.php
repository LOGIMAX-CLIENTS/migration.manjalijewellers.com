<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Notification Report
			<small><span class="badge bg-green" id="notification_total"></span></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Reports</a></li>
			<li class="active">Notification Report</li>
		</ol>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary">
					<div class="box-header with-border"></div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Sent Date</label>
									<div>
										<button class="btn btn-default btn_date_range" id="rpt_payment_date">
											<span style="display:none;" id="rpt_payments1"></span>
											<span style="display:none;" id="rpt_payments2"></span>
											<i class="fa fa-calendar"></i>
											<span id="notification_date_range"></span>
											<i class="fa fa-caret-down"></i>
										</button>
									</div>
								</div>
							</div>
							<!-- shown only in the device wise view -->
							<div class="col-md-2" id="notification_platform_filter" style="display:none;">
								<div class="form-group">
									<label for="notification_platform">Platform</label>
									<select id="notification_platform" class="form-control">
										<option value="">All</option>
										<option value="1">Android</option>
										<option value="2">iOS</option>
									</select>
								</div>
							</div>
							<!-- shown only in the device wise view -->
							<div class="col-md-2" id="notification_status_filter" style="display:none;">
								<div class="form-group">
									<label for="notification_status">Send Status</label>
									<select id="notification_status" class="form-control">
										<option value="">All</option>
										<option value="1">Sent</option>
										<option value="0">Failed</option>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label for="notification_view">View</label>
									<select id="notification_view" class="form-control">
										<option value="notification">Notification wise</option>
										<option value="device">Device wise</option>
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label>&nbsp;</label>
									<div>
										<button type="button" id="search_notification_report" class="btn btn-info">Search</button>
									</div>
								</div>
							</div>
						</div>

						<p>
							<strong>Sent:</strong> <span id="notification_sent"></span>
							&nbsp;&nbsp;
							<strong>Failed:</strong> <span id="notification_failed"></span>
						</p>
						<p class="text-muted">Sent means Firebase accepted the notification for delivery. It does not confirm that the customer's phone received it.<br>FCM Token is the app's push address on that phone. It changes when the app is reinstalled, so it does not identify the device permanently.</p>
						<div class="alert alert-warning" id="notification_limit_alert" style="display:none;"></div>

						<div class="table-responsive" id="notification_group_view">
							<table id="notification_group_table" class="table table-bordered table-striped">
								<thead>
									<tr style="text-transform:uppercase;">
										<th>S.No</th>
										<th>Sent Time</th>
										<th>Notification Title</th>
										<th>Notification Details</th>
										<th>Customers</th>
										<th>Sent</th>
										<th>Failed</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>

						<div class="table-responsive" id="notification_device_view" style="display:none;">
							<table id="notification_report_table" class="table table-bordered table-striped">
								<thead>
									<tr style="text-transform:uppercase;">
										<th>S.No</th>
										<th>Customer</th>
										<th>Mobile</th>
										<th>FCM Token (last 8)</th>
										<th>Platform</th>
										<th>Notification Title</th>
										<th>Notification Details</th>
										<th>Sent Time</th>
										<th>Send Status</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
					<div class="overlay" style="display:block">
						<i class="fa fa-refresh fa-spin"></i>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- customers one notification was sent to, opened from the notification wise view -->
	<div class="modal fade" id="notification_customers_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Customers: <span id="notification_customers_heading"></span></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="notification_customers_platform">Platform</label>
								<select id="notification_customers_platform" class="form-control">
									<option value="">All</option>
									<option value="1">Android</option>
									<option value="2">iOS</option>
									<option value="unknown">Unknown</option>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="notification_customers_status">Send Status</label>
								<select id="notification_customers_status" class="form-control">
									<option value="">All</option>
									<option value="1">Sent</option>
									<option value="0">Failed</option>
								</select>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table id="notification_customers_table" class="table table-bordered table-striped" style="width:100%;">
							<thead>
								<tr style="text-transform:uppercase;">
									<th>S.No</th>
									<th>Customer</th>
									<th>Mobile</th>
									<th>FCM Token (last 8)</th>
									<th>Platform</th>
									<th>Notification Details</th>
									<th>Sent Time</th>
									<th>Send Status</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</div>
