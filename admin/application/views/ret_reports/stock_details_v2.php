 <!-- Content Wrapper. Contains page content -->



 <div class="content-wrapper">

 	<!-- Content Header (Page header) -->

 	<section class="content-header">

 		<h1>

 			Stock Report

 		</h1>

 		<ol class="breadcrumb">

 			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

 			<li><a href="#">Reports</a></li>

 			<li class="active">Stock report</li>

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

 									<div class="col-md-12">

 										<?php if ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == 0) { ?>

 											<div class="col-md-2">

 												<div class="form-group tagged">

 													<label>Select Branch</label>

 													<select id="branch_select" class="form-control branch_filter"></select>

 												</div>

 											</div>

 										<?php } else { ?>

 											<input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">

 											<input type="hidden" id="branch_name" value="<?php echo $this->session->userdata('branch_name') ?>">

 										<?php } ?>



 										<div class="col-md-2">

 											<div class="form-group">

 												<label>Date</label>

 												<?php

													$fromdt = date("d/m/Y");

													$todt = date("d/m/Y");

													?>

 												<input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt . ' - ' . $todt ?>" readonly="">

 											</div>

 										</div>



 										<div class="col-md-2">

 											<label>Select Metal</label>

 											<select id="metal" class="form-control" style="width:100%;" multiple></select>

 										</div>

 										<div class="col-md-2">

 											<label>Select Category</label>

 											<select id="category" class="form-control" style="width:100%;"></select>

 										</div>

 										<div class="col-md-2">

 											<label>Select Grouping</label>

 											<select id="prod_group_id" class="form-control" style="width:100%;"></select>

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
 								</div>


 								<div class="row">

 									<div class="col-md-12">

 										<div class="col-md-2">

 											<label>Select Sub Design</label>

 											<select id="sub_des_select" class="form-control" style="width:100%;"></select>

 										</div>

 										<div class="col-md-2">

 											<label>List By</label>

 											<select id="list_by" class="form-control" style="width:100%;">

 												<option value="0">ALL</option>

 												<option value="1">Tag</option>

 												<option value="2">Non tag</option>

 											</select>

 										</div>

 										<div class="col-md-2">

 											<label>Group By</label>

 											<select id="select_group_by" class="form-control" style="width:100%;">

 												<option value="1">Product</option>

 												<option value="2">Category</option>

 												<option value="3">Section and WeightRange</option>

 												<option value="4">Product,Design and Sub Design</option>

 											</select>

 										</div>

 										<div class="col-md-2">

 											<label></label>

 											<div class="form-group">

 												<button type="button" id="stock_detail_search_v2" class="btn btn-info">Search</button>

 											</div>

 										</div>

 									</div>
 								</div>



 							</div>



 							<div class="box box-info stock_details">

 								<div class="box-header with-border">

 									<h3 class="box-title">Stock Summary</h3>

 									<div class="box-tools pull-right">

 										<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-plus"></i></button>

 									</div>

 								</div>

 								<div class="box-body collapse">

 									<div class="row">

 										<div class="box-body col-md-offset-2 col-md-8">



 											<table id="stock_list_summary" class="table table-bordered table-striped text-center">

 												<thead>

 													<tr>

 														<th>STOCK</th>

 														<th>Pcs</th>

 														<th>Weight</th>

 													</tr>



 												</thead>

 												<tbody>

 													<tr>

 														<td>Opening Stock</td>

 														<td class="opqty"></td>

 														<td class="opwt"></td>

 													</tr>

 													<tr>

 														<td>Inward Stock</td>

 														<td class="inqty"></td>

 														<td class="inwt"></td>

 													</tr>

 													<tr>

 														<td>Lot Inward Stock</td>

 														<td class="lotinqty"></td>

 														<td class="lotinwt"></td>

 													</tr>

 													<tr>

 														<td> Branch Transfer Inward Stock</td>

 														<td class="brinqty"></td>

 														<td class="brinwt"></td>

 													</tr>

 													<tr>

 														<td>Outward Stock</td>

 														<td class="outqty"></td>

 														<td class="outwt"></td>

 													</tr>

 													<tr>

 														<td>Issued Stock</td>

 														<td class="issqty"></td>

 														<td class="isswt"></td>

 													</tr>

 													<tr>

 														<td> Showroom Sale</td>

 														<td class="soldqty"></td>

 														<td class="soldwt"></td>

 													</tr>

 													<tr>

 														<td> Other O/W</td>

 														<td class="otherqty"></td>

 														<td class="otherwt"></td>

 													</tr>



 													<tr>

 														<td>Closing Stock </td>

 														<td class="clsqty"></td>

 														<td class="clswt"></td>

 													</tr>



 												</tbody>





 											</table>



 										</div>

 									</div>

 								</div>

 							</div>



 							<div class="box-body">

 								<div class="row">

 									<div class="box-body">

 										<div class="table-responsive">

 											<table id="stock_list" class="table table-bordered table-striped text-center" style="width:100%">

 												<thead>

 													<!--<tr>

							  	<th colspan="1"></th>

							  	<th colspan="3">Opening</th>

								<th colspan="3">Inward</th>

								<th colspan="3">Outward</th>

								<th colspan="3">Closing</th>

		                      </tr>-->

 													<tr>

 														<th>Category/Product</th>

 														<th>Op Stock</th>

 														<th>Op Gross Wt</th>

 														<th>Op Net Wt</th>

 														<th>Op dia Wt</th>

 														<th>I/W Pcs</th>

 														<th>I/W Gross Wt</th>

 														<th>I/W Net Wt</th>

 														<th>I/W Dia Wt</th>

 														<th>O/W Pcs</th>

 														<th>O/W Gross Wt</th>

 														<th>O/W Net Wt</th>

 														<th>O/W Dia Wt</th>

 														<th>Closing Stock</th>

 														<th>Closing Gross Wt</th>

 														<th>Closing Net Wt</th>

 														<th>Closing Dia Wt</th>

 														<th>Intransit</th>

 														<th>Closing Stock</th>



 													</tr>

 												</thead>

 												<tbody></tbody>

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