  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  	<!-- Content Header (Page header) -->
  	<section class="content-header">
  		<h1>Cash Collection</h1>
  		<ol class="breadcrumb">
  			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
  			<li><a href="#">Billing</a></li>
  			<li class="active">Cash Collection</li>
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
  								<h3 class="box-title">Cash Collection List</h3>
  								
  								<div class="pull-right">
								  <?php if($access['add']==1){?>
  									<a class="btn btn-success pull-right" id="add_lot" href="<?php echo base_url('index.php/admin_ret_billing/cash_collection/add'); ?>"><i class="fa fa-plus-circle"></i> Add</a>
									  <?php }?>
  								</div>
  							</div>
  							<div class="box-body">

  								<div class="row">
  									<div class="col-md-2">

  										<div class="pull-left">

  											<div class="form-group">

  												<button class="btn btn-default btn_date_range" id="ltInward-dt-btn">

  													<span style="display:none;" id="cash_date1"></span>

  													<span style="display:none;" id="cash_date2"></span>

  													<i class="fa fa-calendar"></i> Date range picker

  													<i class="fa fa-caret-down"></i>

  												</button>

  											</div>

  										</div>

  									</div>
  								</div>
  								<div class="row">
  									<div class="box-body">
  										<div class="table-responsive">
  											<table id="cash_collection_list" class="table table-bordered table-striped text-center">
  												<thead>
  													<tr>
  														<th>Date</th>
  														<th>Branch</th>
  														<th>Counter</th>
  														<th>Type</th>
  														<th>Sales Amount</th>
  														<th>Cash in Hand</th>
  														<th>Opening Balance</th>
  														<th>Total Amount</th>
  														<th>Difference</th>
  														<th>Details</th>
														<th width="10%">Action</th>
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