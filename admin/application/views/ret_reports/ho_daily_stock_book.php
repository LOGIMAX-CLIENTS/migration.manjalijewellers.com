<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		H.O Daily Stock Report
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Stock Reports</a></li>
		<li class="active">H.O Daily Stock Report</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary">
					<div class="box-header with-border">

						<div class="col-md-2"> 
							<div class="form-group">  
								<label>Date Picker</label>  
								<?php   
									$fromdt = date("d/m/Y");
									$todt = date("d/m/Y");
								?>
								<br />
								<input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
							</div> 
						</div>
						<div class="col-md-2"> 
							<label>Select Metal</label>
							<select id="metal" class="form-control" style="width:100%;" multiple></select>
						</div>		
							
						<div class="col-md-1"> 
							<div class="form-group">
								<br />
								<button type="button" id="ho_daily_stock_search" class="btn btn-info" style="margin-left:0px;">Search</button>   
							</div>
						</div>
					</div>

					<div class="box-body">  
						<div class="row">
							<div class="col-xs-12">
								<!-- Alert -->
								<?php 
									if($this->session->flashdata('chit_alert'))
									{
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

						<div class="box box-info stock_details">
							<div class="box-body">
								<div class="row">
									<div class="box-body">
										<div class="table-responsive">
											<table id="ho_daily_stock_list" class="table table-bordered table-striped text-center">
												<thead>
													<tr>
														<th colspan="7">H.O DAILY STOCK BOOK</th>
													</tr>
													<tr>
														<th></th>
														<th>PARTICULARS</th>
														<th>GRS WT</th>
														<th>NET WT</th>
														<th>DIA WT</th>
														<th>STONE (GRM)</th>
														<th>STONE (CT)</th>
													</tr>
												</thead> 
												<tbody></tbody>

												<tfoot>
												</tfoot>

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

      



