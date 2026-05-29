  <!-- Content Wrapper. Contains page content -->

  <style>
  	.btn-custom-orange {
  		background-color: orange;
  		border-color: orange;
  		color: white;
  	}
  </style>
  <div class="content-wrapper">

  	<!-- Content Header (Page header) -->

  	<section class="content-header">

  		<h1>

  			Lot

  			<small>Manage your Lot(s)</small>

  		</h1>

  		<ol class="breadcrumb">

  			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

  			<li><a href="#">Lot</a></li>

  			<li class="active">Lot Inward</li>

  		</ol>

  	</section>

  	<!-- Main content -->

  	<section class="content">

  		<div class="row">

  			<div class="col-xs-12">

  				<div class="box box-primary">

  					<div class="box-header with-border">

  						<h3 class="box-title">Lot List</h3> <span id="total_product" class="badge bg-green"></span>

  						<div class="pull-right">

						  <?php if($access['add']==1){?>
  							<a class="btn btn-success pull-right" id="add_lot" href="<?php echo base_url('index.php/admin_ret_lot/lot_inward/add'); ?>"><i class="fa fa-plus-circle"></i> Add</a>
							  <?php }?>
  						</div>

  					</div>

  					<div class="box-body">

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

  						<div class="row">

  							<div class="form-group">

  								<div class="col-md-2">

  									<div class="pull-left">

  										<div class="form-group">

  											<button class="btn btn-default btn_date_range" id="ltInward-dt-btn">

  												<span style="display:none;" id="lt_date1"></span>

  												<span style="display:none;" id="lt_date2"></span>

  												<i class="fa fa-calendar"></i> Date range picker

  												<i class="fa fa-caret-down"></i>

  											</button>

  										</div>

  									</div>

  								</div>



  								<div class="col-md-2">

  									<div class="form-group">

  										<select id="metal" style="width:100%;"></select>

  									</div>

  								</div>



  								<div class="col-md-2">

  									<select id="select_emp" style="width:100%"></select>

  								</div>
								
								<div class="col-md-2">

									<select id="lot_type"  style="width:100%"> <option value = '2'>Non Tag</option> <option value = '1'>Tagged</option></select>

								</div>



  								<div class="col-md-2">

  									<div class="form-group">

  										<button type="button" id="lot_inward_search" class="btn btn-info"><i class="fa fa-search"></i></button>

  									</div>

  								</div>

  								<div class="col-md-2">
  									<div class="form-group">
  										<button type="button" id="lot_closed" class="btn btn-success">Completed</i></button>
  									</div>
  								</div>



  							</div>

  						</div>

  						<div class="table-responsive">

  							<table id="lot_inward_list" class="table table-bordered table-striped text-center">

  								<thead>

  									<tr>

  										<th width="5%">LOT NO</th>

  										<th width="5%">LOT DATE</th>

  										<th width="5%">LOT FROM</th>

  										<th width="5%">REF NO</th>

										<th width="5%">PRODUCT NAME</th>

  										<th width="3%">KARIGAR</th>

  										<th width="3%">EMPLOYEE</th>

  										<th width="5%">RECD PCS</th>

  										<th width="5%">RECD WT</th>

  										<th width="5%">TAGGED PCS</th>

  										<th width="5%">TAGGED WT</th>

  										<th width="1%"></th>

  										<th width="1%">PUR DETAILS</th>

  										<th width="5%">BLC PCS</th>

  										<th width="5%">BLC WT</th>

										<th width="3%">PURE WT</th>

  										<th width="15%">ACTION</th>

  									</tr>

  								</thead>

  								<tfoot>

  									<tr style="font-weight: bold; color:red">

  										<td></td>
  										<td></td>
  										<td></td>
  										<td></td>
  										<td></td>
  										<td></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td style="text-align:right;"></td>
  										<td></td>

  									</tr>

  								</tfoot>

  							</table>

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

  <!-- <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Product</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Product?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div> -->

  <!-- / modal -->



  <div class="modal fade" id="purchase_details" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  	<div class="modal-dialog" style="width:95%;">

  		<div class="modal-content">

  			<div class="modal-header">

  				<h4 class="modal-title" id="myModalLabel">LOT PURCHASE DETAILS</h4>

  			</div>

  			<div class="modal-body">



  				<div class="row">

  					<input type="hidden" id="" value="0">

  					<table id="lot_pur_details" class="table table-bordered table-striped text-center">

  						<thead>

  							<tr>

  								<th>Id</th>

  								<th>Product</th>

  								<th>Design</th>

  								<th>Sub Design</th>

  								<th>Purchase Wastage</th>

  								<th>Purchase MC Type</th>

  								<th>Purchase MC</th>

  								<th>Purchase Type</th>

  								<th>Purchase Touch</th>

  								<th>Purchase Rate</th>


  							</tr>

  						</thead>

  						<tbody>

  						</tbody>

  						<tfoot>

  							<!-- <tr style="font-weight:bold;font-size:15px">
						<td>Total:</td>
						<td></td>
						<td></td>
						<td></td>
						<td class="stn_tot_pcs"></td>
						<td class="stn_tot_weight"></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="stn_tot_amount"></td>
						<td></td>
					</tr> -->

  						</tfoot>

  					</table>

  				</div>

  			</div>

  			<div class="modal-footer">

  				<button type="button" id="update_stone_details" class="btn btn-success">Save</button>

  				<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

  			</div>

  		</div>

  	</div>

  </div>

  <!-- modal -->

  <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  	<div class="modal-dialog">

  		<div class="modal-content">

  			<div class="modal-header">

  				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

  				<h4 class="modal-title" id="myModalLabel">Cancel Lot Generate</h4>

  				<input type="hidden" id="lot_id">

  			</div>

  			<div class="modal-body">

  				<strong>Are you sure! You want to Cancel this Entry?</strong>

  			</div>



  			<div class="col-md-12 bill_remarks">

  				<label>Remarks<span class="error">*</span></label>

  				<textarea class="form-control" id="cancel_remark" placeholder="Enter Remarks" rows="5" cols="10"> </textarea>

  			</div>



  			<div class="modal-footer">

  				<button type="button" id="lot_cancel" class="btn btn-danger btn-confirm" data-dismiss="modal" disabled>Cancel</button>

  				<button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

  			</div>

  		</div>

  	</div>

  </div>

  <!-- / modal -->