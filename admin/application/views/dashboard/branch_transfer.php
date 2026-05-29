<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cockpit.css">
<!-- Content Wrapper. Contains page content -->
<div class="row credit_history">
	<div class="col-md-12 col-xs-12 container-row">
		<div class="col-md-12 col-xs-12 box-items no-paddingwidth">
			<div class="col-md-12 col-xs-12 new_customer no-paddingwidth">
				<div class="col-md-12 col-xs-12 item-heading">
					STOCK AND BRANCH TRANSFER
				</div>
				<div class="col-md-12 col-xs-12">
				
                     <div class="col-md-6 col-xs-12 no-paddingwidth container-table">
						<table class="table table-bordered" id="branch_transfer_table_download_pending">
							<thead>
							<tr>
								<th colspan="5">DOWNLOAD PENDING</th>
							</tr>
                            <tr>
								<th>PRODUCT</th>
								<th class="">FROM BRANCH</th>
								<th class="">TO BRANCH</th>
								<th class="">TOTAL PCS</th>
								<th class="">TOTAL GWT</th>
							</tr>
							</thead>
							<tbody></tbody>
                            <tfoot></tfoot>
						</table>
					</div>
				
					<div class="col-md-6 col-xs-12 no-paddingwidth container-table">
						<table class="table table-bordered" id="branch_transfer_table_approved_pending">
							<thead>
							<tr>
								<th colspan="5">APPROVAL PENDING</th>
							</tr>
                            <tr>
								<th>PRODUCT</th>
								<th class="">FROM BRANCH</th>
								<th class="">TO BRANCH</th>
								<th class="">TOTAL PCS</th>
								<th class="">TOTAL GWT</th>
							</tr>
							</thead>
							<tbody></tbody>
                            <tfoot></tfoot>
						</table>
					</div>
				
				</div>
			</div>

			<!-- Sales Transfer In-Transit Section -->
			<div class="col-md-12 col-xs-12 new_customer no-paddingwidth" style="margin-top:15px;">
				<div class="col-md-12 col-xs-12 item-heading">
					SALES TRANSFER - IN TRANSIT
					<span id="st_intransit_count" class="badge bg-orange" style="margin-left:10px;"></span>
				</div>
				<div class="col-md-12 col-xs-12">
					<div class="col-md-12 col-xs-12 no-paddingwidth container-table">
						<table class="table table-bordered" id="sales_transfer_intransit_table">
							<thead>
							<tr>
								<th colspan="8">IN-TRANSIT TRANSFERS <small>(Dispatched, Not Yet Downloaded)</small></th>
							</tr>
							<tr>
								<th>BILL NO</th>
								<th>DATE</th>
								<th>FROM BRANCH</th>
								<th>TO BRANCH</th>
								<th>ITEMS</th>
								<th style="text-align:right;">VALUE (₹)</th>
								<th>DAYS IN TRANSIT</th>
								<th>STATUS</th>
							</tr>
							</thead>
							<tbody></tbody>
							<tfoot></tfoot>
						</table>
					</div>
				</div>
			</div>
			<!-- /Sales Transfer In-Transit Section -->

		</div>
	</div>
</div>