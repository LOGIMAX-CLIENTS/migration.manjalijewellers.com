<style>

.stickyBlk {

	margin: 0 auto;

	top: 0;

	 max-width: 1200px

	z-index: 999;

	background: #fff;

}

th{
	border-bottom:solid thin;
}



</style>



<!-- Content Wrapper. Contains page content -->



<div class="content-wrapper">

<!-- Content Header (Page header) -->

<section class="content-header">

  <h1>

	Reports

	 <small>Tag Wise Profit Report</small>

  </h1>

  <ol class="breadcrumb">

	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

	<li><a href="#">Reports</a></li>

	<li class="active">Tag Wise Profit Report</li>

  </ol>

</section>



<!-- Main content -->

<section class="content">

  <div class="row">

	<div class="col-xs-12">



	   <div class="box box-primary">

		<div class="box-header with-border">

		  <h3 class="box-title">Tag Wise Profit List</h3>  <span id="total_count" class="badge bg-green"></span>



		</div>

		 <div class="box-body">

		  <div class="row">

			  <div class="col-md-offset-2 col-md-8">

			  <div class="box box-default">

			   <div class="box-body">

				   <div class="row">

						<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

						<div class="col-md-3">

							<div class="form-group tagged">

								<label>Select Branch</label>

								<select id="branch_select" class="form-control branch_filter"></select>

							</div>

						</div>



						<?php }else{?>

						<div class="col-md-3">

							<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">

							<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>">

						</div>

						<?php }?>

						<div class="col-md-3">

							<div class="form-group tagged">

								<label>Select Metal</label>

								<select id="metal" class="form-control"></select>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group tagged">

								<label>Group By</label>

								<select id="group_by" class="form-control">
									<option value="1">Karigar</option>
									<option value="2">Product</option>
									<option value="3">Section</option>
								</select>

							</div>

						</div>



						<div class="col-md-3">

							<div class="form-group">

								<label>Date</label>

								<?php

									$fromdt = date("d/m/Y");

									$todt = date("d/m/Y");

								?>

								   <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">

							</div>

						</div>

						<!--<div class="col-md-2">

							<label>Select Village</label>

							<select id="village_select" class="form-control" style="width:100%;"></select>

						</div>

						<div class="col-md-2">

							<label>Select Customer</label>

							<select id="cus_select" class="form-control" style="width:100%;"></select>

						</div>-->

						<div class="col-md-2">

							<label></label>

							<div class="form-group">

								<button type="button" id="tag_wise_profit_search" class="btn btn-info">Search</button>

							</div>

						</div>

					</div>

				 </div>

			   </div>

			  </div>

		   </div>



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

				<div class="box-header with-border">

				  <h3 class="box-title">Tag Wise Profit Details</h3>

				  <div class="box-tools pull-right">

					<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

				  </div>

				</div>

				<div class="box-body">

					<div class="row">

						<div class="box-body">

						   <div class="table-responsive">

							  <table id="tagprofit_details" class="table table-bordered table-striped text-center">

								 <thead>

								 <tr style="text-transform:uppercase;border: solid thin;" >
								 <th style="text-transform:uppercase;border: solid thin;" colspan="14" rowspan="1">ITEM</th>
								 <th style="text-transform:uppercase;border: solid thin;" colspan="8" rowspan="1">SALES</th>
								 <th style="text-transform:uppercase;border: solid thin;"  colspan="7" rowspan="1">PURCHASE</th>
								 <th style="text-transform:uppercase;border: solid thin;" colspan="6" rowspan="1">PROFIT</th>

								</tr>

								  <tr  style="text-transform:uppercase;">

									<th width="1%" style="border-left: solid thin;">Product</th>

									<th width="1%">Design</th>

									<th width="1%">Sub Design</th>

									<th width="1%">Bill No</th>

									<th width="1%">Bill Date</th>

									<th width="1%">Tag Code</th>

									<th width="1%">Old Tag Code</th>

									<th width="1%">Piece</th>

									<th width="1%">Grs wt</th>

									<th width="1%" >Net wt</th>

									<th width="1%">STN PCS</th>

									<th width="1%">STN WT</th>

									<th width="1%">DIA PCS</th>

									<th width="1%" style="border-right: solid thin;" >DIA WT</th>

									<th width="1%">V.A(%)</th>

									<th width="1%">V.A(GM)</th>

									<th width="1%" >MC</th>

									<th width="1%">STN AMT</th>

									<th width="1%">DIA AMT</th>

									<th width="1%">Rate</th>

									<th width="1%">Taxable Amt</th>

									<th width="1%" style="border-right: solid thin;" >Tax Amt</th>

									<th width="1%">V.A(%)</th>

									<th width="1%"> V.A(GM) </th>

									<th width="1%">STN AMT</th>

									<th width="1%">DIA AMT</th>

									<th width="1%"> Rate</th>

									<th width="1%">Taxable Amt</th>

									<th width="1%" style="border-right: solid thin;"> Tax Amt</th>

									<th width="1%">Sale Amount</th>

									<th width="1%">Purchase Amount</th>

									<th width="1%">Profit@Wastage</th>

									<th width="1%">Profit@Wastage%</th>

									<th width="1%" style="border-right: solid thin;" >Profit@Amount(Rs)</th>

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





