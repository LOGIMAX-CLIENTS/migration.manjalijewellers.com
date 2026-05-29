 <style type="text/css">
 	#stockage_days {
 		text-align: center;
 		margin-top: 20px;
 	}

 	.stock_age_heading1 {
 		font-size: 20px;
 		font-weight: bold;
 		background: green !important;
 		color: white !important;
 	}

 	.stock_age_heading2 {
 		font-size: 18px;
 		font-weight: bold;
 		text-align: left !important;
 	}

 	.stock_age_heading3 {
 		font-size: 15px;
 		/* font-weight: bold; */
 		text-align: left !important;
 		font-style: italic;
 	}

 	#stock_age_dynamic_list thead tr th {
 		background: brown !important;
 		color: white;
 	}

 	.summary {
 		font-size: 15px;
 	}

 	.stock_age_total {
 		font-size: 16px;
 		font-weight: bold;
 	}

 	.val2, .stock_age_total {
 		/*background: green;
		color: white;*/
 	}

 	.val3, .stock_age_total {
 		/*background: yellow;*/
 	}

 	.val1.total_heading,
 	.val3.total_heading {
 		text-align: left !important;
 	}

 	.age_pcs,
 	.age_wt,
 	.age_nwt,
 	.age_dwt {
 		text-align: right !important;
 	}

 	.val1 {
 		font-size: 20px;
 	}

 	.plus-button,
 	.del-button {
 		padding: 4px 8px;
 		color: white;
 		border: none;
 		border-radius: 5px;
 		cursor: pointer;
 	}

 	.plus-button {
 		background-color: blue;
 	}

 	.del-button {
 		background-color: red;
 	}

 	.plus-button .fa,
 	.del-button .fa {
 		font-size: 12px;
 		/* Adjust the font size as needed */
 	}

 	.desc {
 		vertical-align: middle !important;
 	}

 	.btnDataSearch {
 		display: flex;
 		justify-content: space-around;
 	}

 	.modal_btns {
 		display: flex;
 		justify-content: space-around;
 	}

 	@media print {
 		.header-print {
 			display: table-header-group;
 		}
 	}
 </style>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
 	<!-- Content Header (Page header) -->
 	<section class="content-header">
 		<h1>
 			Reports
 			<small>Stock Age Analysis</small>
 		</h1>
 		<ol class="breadcrumb">
 			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
 			<li><a href="#">Reports</a></li>
 			<li class="active">Stock age</li>
 		</ol>
 	</section>

 	<!-- Main content -->
 	<section class="content">
 		<div class="row">
 			<div class="col-xs-12">

 				<div class="box box-primary">
 					<div class="box-header with-border">
 						<h3 class="box-title">Stock Age Analysis Report</h3> <span id="total_count" class="badge bg-green"></span>

 					</div>
 					<div class="box-body">
 						<div class="row">
 							<div class="col-md-12">
 								<div class="box box-default">
 									<div class="box-body">
 										<div class="row">
 											<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>
 												<div class="col-md-2">
 													<div class="form-group tagged">
 														<label>Select Branch</label>
 														<select id="branch_select" class="form-control" style="width:100%;" multiple></select>
														<input type="hidden" id="id_branch"  value="<?php echo $this->session->userdata('id_branch') ?>">
 													</div>
 												</div>
 											<?php } else { ?>
 												<!-- <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>">  -->
 												<div class="col-md-2">
 													<div class="form-group tagged">
 														<label>Select Branch</label>
 														<select id="branch_select" style="width:100%;" disabled></select>
 														<input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
 														<input type="hidden" id="id_branch" value="<?php echo $this->session->userdata('id_branch') ?>">
 														<input type="hidden" id="branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">
 													</div>
 												</div>
 											<?php } ?>
 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Select Metal</label>
 													<select class="form-control" id="metal" multiple></select>
 												</div>
 											</div>
 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Category</label>
 													<select id="category" class="form-control" multiple></select>
 												</div>
 											</div>

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Product</label>
 													<select id="prod_select" class="form-control" multiple></select>
 												</div>
 											</div>

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Design</label>
 													<select id="des_select" class="form-control" multiple></select>
 												</div>
 											</div>

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Sub Design</label>
 													<select id="sub_des_select" class="form-control" multiple></select>
 												</div>
 											</div>

 										</div>
 										<div class="row">

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Karigar</label>
 													<select id="karigar" class="form-control" multiple></select>
 												</div>
 											</div>

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Section</label>
 													<select id="section_select" class="form-control" multiple></select>
 												</div>
 											</div>

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Group By</label>
 													<select id="stock_age_group_by" class="form-control" multiple>
 														<option value="4">Branch</option>
 														<option value="1">Product</option>
 														<option value="5">Design</option>
 														<option value="6">SubDesign</option>
 														<option value="2">Section</option>
 														<option value="3">Karigar</option>
 														<option value="7">Size</option>
 													</select>
 												</div>
 											</div>

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Based on</label>
 													<select id="age_type" class="form-control" style="width:100%;">
 														<option value="1" selected>Tag Date</option>
 														<option value="2">Received Date</option>
 													</select>
 												</div>
 											</div>

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Summary/Detailed</label>
 													<select id="group_type" class="form-control">
 														<option value="1">Summary</option>
 														<option value="2">Detailed</option>
 													</select>
 												</div>
 											</div>

 											<div class="col-md-2">
 												<div class="form-group">
 													<label>Date</label>
 													<?php
														$fromdt = date("d/m/Y");
														$todt = date("d/m/Y");
														?>
 													<input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php //echo $fromdt.' - '.$todt
																																												?>" readonly="">
 												</div>
 											</div>

 										</div>
 										<div class="row">
 											<div class="col-md-10"></div>
 											<div class="col-md-2 btnDataSearch">
 												<div class="form-group">
 													<button type="button" id="stockage_days" class="btn btn-warning" data-toggle="modal">Enter Days</button>
 												</div>
 												<div class="form-group">
 													<br>
 													<button type="button" id="stockAgeDynamic_search" class="btn btn-info">Search</button>
 												</div>
 											</div>

 										</div>
 									</div>
 								</div>
 							</div>

 						</div>
 						<p></p>

 						<div class="row">
 							<div class="col-xs-12">
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
 							</div>
 						</div>

 						<div class="table-responsive stock_age_container">

 						</div>
 					</div><!-- /.box-body -->
 					<div class="overlay" style="display:none">
 						<i class="fa fa-refresh fa-spin"></i>
 					</div>

 				</div><!-- /.col -->
 			</div><!-- /.row -->
 	</section><!-- /.content -->
 </div><!-- /.content-wrapper -->


 <!-- modal -->
 <div class="modal fade" id="stockDaysModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 	<div class="modal-dialog">
 		<div class="modal-content">
 			<div class="modal-header">
 				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
 				<h4 class="modal-title" id="myModalLabel">Enter days</h4>
 			</div>
 			<div class="modal-body">
 				<div class="row">
 					<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
 				</div>
 				<div class="row">
 					<div class="form-group">

 						<table id="stock_age_days" class="table table-bordered table-striped text-center">
 							<thead>
 								<tr>
 									<th width="40%">From</th>
 									<th width="40%">To</th>
 									<th width="20%">Action</th>
 								</tr>
 							</thead>
 							<tbody>
 								<tr>
 									<td><input type="number" value="" class="form-control days_from" id="days_from_1" /></td>
 									<td><input type="number" value="" class="form-control days_to" /></td>
 									<td class="modal_btns">
 										<button class="plus-button stock_days_add"><i class="fa fa-plus fa-lg"></i></button>
 									</td>
 								</tr>
 							</tbody>
 						</table>

 					</div>
 				</div>

 			</div>
 			<div class="modal-footer">

 				<button type="button" class="btn btn-warning" data-dismiss="modal">Okay</button>

 			</div>
 		</div>
 	</div>
 </div>
 <!-- / modal -->