<style>
	/* Create a class for the radio button container */
	.radio-container {
		display: flex;
		/* Make the container a flex container */
		gap: 10px;
		/* Add a gap of 10 pixels between radio buttons */
	}

	/* Create a class for the radio buttons */
	.radio-button {
		margin-right: 5px;
		/* Add some spacing to the right of each radio button */
	}
</style>


<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

	<!-- Content Header (Page header) -->

	<section class="content-header">

		<h1>

			Customer Outstanding Payment Report

			<small></small>
			<span class="badge bg-green" style="font-size: 14px;margin-left: 130px;color:#000;">Amount in : <b class="" id="currency_symbol"></b>
				&nbsp; | &nbsp; Weight in : <b>Gram</b>
			</span>

		</h1><span id="total" class="badge bg-green"></span>

		<ol class="breadcrumb">

			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

			<li><a href="#">Reports</a></li>

			<li class="active">Customer Outstanding Payment Report</li>

		</ol>

	</section>



	<!-- Main content -->

	<section class="content report_sel">

		<div class="row">

			<div class="col-xs-12">



				<div class="box box-primary">

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



								<div class="col-md-2">

									<div class="form-group">

										<span style="font-weight:bold;">Select View</span><br />

										<input type="radio" name="tableview" value="1" checked="true"> Summary



										<input type="radio" name="tableview" value="2"> Detailed

									</div>

								</div>



								<!--<div class="col-md-2">

    		                  <div class="form-group">

    		                    <div class="input-group">

									<span style="font-weight:bold" id="out_standing_date_range"></span>

    		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date" >

    							 

    							    <span  style="display:none;" id="rpt_payments1"></span>

    							    <span  style="display:none;" id="rpt_payments2"></span>

    		                        <i class="fa fa-calendar"></i> Date range picker

    		                        <i class="fa fa-caret-down"></i>

    		                      </button>

    		                    </div>

    		                 </div>

    		                </div>-->

								<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>

									<div class="col-md-2">

										<div class="form-group">

											<label>Joined Branch </label>

											<select id="branch_select" class="form-control" style="width:100%;"></select>

											<input id="id_branch" name="scheme[id_branch]" type="hidden" value="" />

										</div>

									</div>

								<?php } else { ?>

									<input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">

									<input type="hidden" id="login_branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">

								<?php } ?>







								<div class="col-md-2">

									<div class="form-group">

										<label>Scheme</label>

										<select id="scheme_select" class="form-control" style="width:100%; "></select>

										<input id="id_schemes" name="id_scheme" type="hidden" value="" />

									</div>

								</div>

								<div class="col-md-3">
									<label></label>
									<div class="form-group">
										<label class="radio-button">
											<input type="radio" name="datepick" id="show_all" checked="true" value="0"> All
										</label>
										<label>
											<input type="radio" id="singleDatepicker" name="datepick" class="date-radio"> Single Date
											<input type="hidden" id="datesingle_search">
										</label>
										<div class="datepicker-container" style="display: none;">
											<input type="text" class="datepicker">
										</div>
										<label class="radio-button">
											<input type="radio" name="datepick" id="rpt_payment_date">Date range
											<span style="display:none;" id="rpt_payments1"></span>
											<span style="display:none;" id="rpt_payments2"></span>
											<input type="hidden"  id="hidden_from_date">
											<input type="hidden"  id="hidden_to_date">
										</label><br>
										<span id="from_to_repdate"></span>
									</div>
								</div>



								<div class="col-md-1 pull-right">
									<label></label>
									<div class="form-group">
										<button type="button" id="search_scheme_list" class="btn btn-info pull-right">Search</button>
									</div>
								</div>

							</div>
						</div>

						<div class="row">

							<div class="col-md-12">
								<div class="col-md-3 pull-right">
									<label></label>
									<div class="form-group pull-right">
										<button type="button" id="excel_export" class="btn btn-success">Summary Excel</button>
										<button class="btn btn-success" id="excel_detailed_list" onclick="exportOutstandingReportToExcel();" ><i class="fa fa-file-excel-o"></i> Detailed</button>
										<!-- <a type="button" target="_blank" href="<?php echo base_url('index.php/admin_reports/exl_rep_outstanding/export_excel'); ?>" class="btn btn-success">Detailed </a> -->
									</div>
								</div>
							</div>
						</div>


						<!--<div class="col-md-2" id="group_sel" style="display:none;">

    								<div class="form-group" >

    									<label>Group Name</label>									

    									<select id="id_group" class="form-control" style="width:200px; margin-left: 15px !important;"></select>

    									<input id="id_group_name"  name="id_group_name" type="hidden" value="" />

    								</div>

    						   </div>-->





						<!-- <div class="col-md-2">

                                    <div class="form-group">

                                        <label> Select Status</label>

                                        <select id="is_live" class="form-control" style="width:100%;border-radius: 5px !important;">

                                            <option value="">All</option>

                                            <option value="0">Live</option>

                                            <option value="1">Closed</option>

                                        </select>

                                    </div>

                                </div> -->


						<!-- 
                                <div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="search_scheme_list" class="btn btn-info">Search</button>   

									</div>

							    </div> -->

						<!-- <div class="col-md-2"> 

									<label></label> -->

						<!--<button type="button" id="xl_export_outstanding" class="btn btn-success">Excel</button>   -->

						<!--<div class="form-group">

									

										<a type="button" target="_blank"  href="<?php echo base_url('index.php/admin_reports/exl_rep_outstanding/export_excel'); ?>" class="btn btn-success">Excel</a>   

									</div>-->

						<!-- </div> -->

					</div>

				</div>

				<!-- </br> -->

				<!-- <div class="row">

				        <div class="col-md-2">

                            <div class="form-group">

                                <a href="<?php echo base_url('index.php/reports/payment_schemewise'); ?>" target="_blank"><button class="btn btn-warning">Summary Report</button></a>

                            </div>

                        </div>

                        <div class="col-md-2">

                            <div class="form-group">

                                <button class="btn btn-warning">Mode Wise Report</button>

                            </div>

                        </div>

					 </div>-->



				<div class="box box-info stock_details" id="summary_block" style="display: block;">

					<div class="box-header with-border">

						<h3 class="box-title" style="font-weight:bold;">Customer Outstanding Payment Report Summary <span class="summary_description"></span></h3>

						<div class="box-tools pull-right">

							<button class="btn btn-success" id="print_summary"><i class="fa fa-print"></i> Print</button>

						</div>

					</div>

					<div class="box-body">

						<div class="row" style="background: #ecf0f5;">

							<div class="col-md-12" style="text-align:center;font-weight: bold;">Scheme-wise Accounts</div>

							<!-- <div class="col-md-6" style="text-align:center;font-weight: bold;">Group-wise Accounts</div> -->

						</div>

						<div class="row">

							<div class="box-body col-md-12" id="offline_modewise"></div>

							<!-- <div class="box-body col-md-4" id="online_modewise"></div> -->

						</div>

					</div>



				</div>

				<div id="modesummary"></div>

				<div class="box box-info">

					<div class="box-body">

						<div class="table-responsive" id="out_standing_table_div" style="display:none;">

							<table id="scheme_wise_detail_report" class="table table-bordered table-striped text-center">



								<thead>



									<tr>

										<th>S.No</th>

										<th>Code</th>

										<!--<th>Group Code</th>-->

										<th>Account No</th>

										<th>Acc Name</th>

										<th>Mobile</th>

										<th>Cus Name</th>

										<th>Address1</th>

										<th>Address2</th>

										<th>Area</th>

										<th>city</th>

										<th>State</th>

										<th>pincode</th>

										<th>Joined On</th>

										<th>Outstanding Amt</th>

										<th>Outstanding Wgt</th>

										<th>Last&nbsp;Paid&nbsp;On</th>  <!-- esakki 11-11 -->

										<th>Maturity On</th>

										<th>Scheme Type</th>

										<th>Source</th>

										<th>Referred Employee</th>

										<th>Employee</th>

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