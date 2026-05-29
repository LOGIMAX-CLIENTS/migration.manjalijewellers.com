<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header -->
	<!-- Main content -->
	<section class="content">
		<!-- Default box -->
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Add Physical Stock Entry</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
					<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
				</div>
			</div>
			<div class="box-body">
				<!-- form container -->
				<div class="row">
					<div class="col-sm-12">
						<!--Block 1 — Branch / Employee / Date / Product Type / Search-->
						<div class="row" style="align-items:flex-end; display:flex; flex-wrap:wrap;">
							<div class="col-sm-2">
								<label>Branch <span class="error">*</span></label>
								<div class="form-group">
									<select id="lt_rcvd_branch_sel" class="ret_branch form-control" required="true"></select>
								</div>
							</div>
							<div class="col-sm-2">
								<label>Metal</label>
								<div class="form-group">
									<select id="stock_metal_filter" class="form-control" style="width:100%;">
										<option value="">All Metals</option>
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<label>Employee<span class="error tagged"> *</span></label>
								<div class="form-group">
									<select id="select_emp" class="form-control"></select>
								</div>
							</div>
							<div class="col-sm-2">
								<label>Date <span class="error">*</span></label>
								<div class="form-group">
									<input class="form-control product_dt" id="emp_stock_date" name="order[smith_due_dt]" placeholder="Select Date" type="text" tabindex="2" disabled readonly>
								</div>
							</div>
							<div class="col-sm-2">
								<label>Product Type</label>
								<div class="form-group">
									<select id="stock_product_type" class="form-control">
										<option value="1">Tagged Products</option>
										<option value="2">Non-Tagged Products</option>
									</select>
								</div>
							</div>
							<div class="col-sm-2">
								<label>&nbsp;</label>
								<div class="form-group" style="display:flex; gap:5px;">
									<button id="btn_search_products" class="btn btn-info">
										<i class="fa fa-search"></i> Search
									</button>
									<button type="button" class="btn btn-default" onclick="print_employee_entry()" title="Print">
										<i class="fa fa-print"></i> Print
									</button>
								</div>
							</div>
						</div>

						<!-- Hidden setting value from PHP -->
						<input type="hidden" id="stock_audit_by" value="<?php echo isset($stock_audit_by) ? $stock_audit_by : 1; ?>">

						<hr style="border-top:3px solid #3c8dbc;">

						<!-- Product table + save — hidden until Search is clicked with a branch -->
						<div id="product_table_section" style="display:none;">
							<div id="item_details">
								<div class="row">
									<div class="col-md-12">
										<input type="hidden" id="lot_inward_details_id" class="form-control" value="">
										<input type="hidden" id="id_sub_design" class="form-control" value="">
										<input type="hidden" id="id_design" class="form-control" value="">

										<div id="printable_area">
											<!-- Print-only header (hidden on screen) -->
											<div id="print_header" class="print-only-block">
												<h3 style="text-align:center;margin:0 0 5px 0;font-weight:bold;">Physical Stock Entry</h3>
												<table style="width:100%;margin-bottom:10px;font-size:13px;">
													<tr>
														<td><strong>Branch:</strong> <span id="print_branch"></span></td>
														<td><strong>Employee:</strong> <span id="print_employee"></span></td>
														<td><strong>Date:</strong> <span id="print_date"></span></td>
														<td><strong>Type:</strong> <span id="print_product_type"></span></td>
													</tr>
												</table>
												<hr style="border-top:1px solid #333;margin:5px 0;">
											</div>

											<div class="row">
												<div class="col-md-6">
													<table class="table table-bordered product_pcs product_pcs_left">
														<thead>
															<tr>
																<th>Section / Product</th>
																<th class="th_pcs_col">Pieces</th>
																<th class="th_wt_col" style="display:none;">Weight (gm)</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
												<div class="col-md-6">
													<table class="table table-bordered product_pcs product_pcs_right">
														<thead>
															<tr>
																<th>Section / Product</th>
																<th class="th_pcs_col">Pieces</th>
																<th class="th_wt_col" style="display:none;">Weight (gm)</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</div>

											<!-- Print-only footer with Authorized Signature -->
											<div id="print_footer" class="print-only-block">
												<div style="margin-top:60px;text-align:right;padding-right:30px;">
													<div style="border-top:1px solid #333;display:inline-block;padding-top:5px;min-width:200px;text-align:center;">
														<strong>Authorized Signature</strong>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div style="display:flex;justify-content:center;margin-top:15px;">
								<button class="btn btn-primary save_product_pcs" style="text-align:center;">
									<i class="fa fa-save"></i> Save
								</button>
							</div>
						</div><!-- /#product_table_section -->

					</div><!-- /.col-sm-12 -->
				</div><!-- /.row -->
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Print-only CSS -->
<style>
/* Hide print-only blocks on screen */
.print-only-block {
	display: none;
}

@media print {
	/* Hide navigation, sidebar, buttons, form controls */
	.main-header, .main-sidebar, .content-header, .box-tools,
	.save_product_pcs, .overlay, nav, .breadcrumb,
	.main-footer, .control-sidebar,
	#btn_search_products, .box-title,
	.select2-container, .datemask, .error,
	footer { display: none !important; }

	/* Hide the filter row entirely */
	.box-body > .row > .col-sm-12 > .row:first-child { display: none !important; }
	.box-body > .row > .col-sm-12 > hr { display: none !important; }
	.box-body > .row > .col-sm-12 > input[type="hidden"] { display: none !important; }

	/* Show the product table section */
	#product_table_section { display: block !important; }

	/* Layout reset */
	.content-wrapper { margin-left: 0 !important; padding: 0 !important; }
	.box { border: none !important; box-shadow: none !important; margin: 0 !important; }
	.box-body { padding: 0 !important; }
	#printable_area { display: block !important; }

	/* Show print-only blocks */
	.print-only-block { display: block !important; }

	/* Table styling for print */
	.product_pcs { width: 100% !important; border-collapse: collapse !important; }
	.product_pcs th, .product_pcs td {
		border: 1px solid #333 !important;
		padding: 4px 8px !important;
		font-size: 12px !important;
	}
	.product_pcs thead tr {
		background: #eee !important;
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}
	.section-header-row td {
		background: #f5f5f5 !important;
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}

	/* Hide input fields and show their values as text */
	.product_pcs input[type="number"],
	.product_pcs input[type="text"] {
		border: none !important;
		background: transparent !important;
		box-shadow: none !important;
		padding: 0 !important;
		font-size: 12px !important;
		width: auto !important;
		-webkit-appearance: none;
		-moz-appearance: textfield;
	}
	/* Hide spinner buttons in print */
	.product_pcs input[type="number"]::-webkit-inner-spin-button,
	.product_pcs input[type="number"]::-webkit-outer-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}

	/* Footer stays at bottom */
	#print_footer {
		position: fixed;
		bottom: 30px;
		right: 0;
		width: 100%;
	}

	/* Ensure page breaks work well */
	@page {
		margin: 15mm 10mm 25mm 10mm;
		size: A4 portrait;
	}
}
</style>