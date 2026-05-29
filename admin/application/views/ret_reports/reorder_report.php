  <!-- Content Wrapper. Contains page content -->
  <style>

.table-border {
    border: 2px solid #b4a9a9
}

.table-border>thead>tr>th,.table-border>tbody>tr>th,.table-border>tfoot>tr>th,.table-border>thead>tr>td,.table-border>tbody>tr>td,.table-border>tfoot>tr>td {
    border: 1px solid #333
}

.table-border>thead>tr>th,.table-border>thead>tr>td {
    border-bottom-width: 2px
}
</style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Re-order Items</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Retail Reports</a></li>

            <li class="active">Re-order Items</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">



               <div class="box box-primary">

                 <div class="box-body">

                  <div class="row">

				  	<div class="col-md-12">

	                  <div class="box box-default">

	                   <div class="box-body">

						   <div class="row">

						       <div class="col-md-2">

									<div class="form-group">

										<label>Select Product<span class="error">*</span></label>

										<select id="prod_select" class="form-control" style="width:100%;"></select>

									</div>

								</div>

								<div class="col-md-2">

									<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>



									<div class="form-group tagged">

										<label>Select Branch</label>

										<select id="branch_select" class="form-control branch_filter"></select>

									</div>

									<?php }else{?>

										<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">


									<?php }?>

								</div>

								<div class="col-md-2">

									<div class="form-group">

										<label>Select Section</label>

										<select id="section_select" class="form-control" style="width:100%;"></select>

									</div>

								</div>



								<div class="col-md-2">

									<div class="form-group">

										<label>Select Design</label>

										<select id="des_select" style="width:100%;"></select>

									</div>

								</div>



								<div class="col-md-2">

									<div class="form-group">

										<label>Select Sub Design</label>

										<select id="sub_des_select" style="width:100%;"></select>

									</div>

								</div>



							</div>

							<div class="row">

							    <div class="col-md-2">

									<div class="form-group">

										<button type="button" id="reorder_search" class="btn btn-info">Search</button>

									</div>

								</div>

								<div class="col-md-2">

									<div class="form-group">

										<!-- <button type="button" id="reorder_print" class="btn btn-warning">Print</button> -->

										<button id="btnExport" onclick="fnReorderReportExcelReport('1');" class="btn btn-success "><i class="fa fa-file-excel-o"></i></button>

									</div>

								</div>

								
								<div class="col-md-2">

									<div class="form-group">

										<button type="button" id="reorder_print" class="btn btn-warning">Print</button>


									</div>

								</div>

								<div class="row">



								<div class="col-md-3">

    								<div class="form-group">

    							        <input type="radio" name="report_type" id="report_type0" value="0" checked> <label for="report_type0">Available</label>  &nbsp;&nbsp;

    							        <input type="radio" name="report_type" id="report_type1" value="1" > <label for="report_type1">Shortage</label>  &nbsp;&nbsp;

    							        <input type="radio" name="report_type" id="report_type2" value="2" > <label for="report_type2">Excess</label>  &nbsp;&nbsp;

    							    </div>

								</div>

								<div class="col-md-2">

    								<div class="form-group">

    							        <input type="radio" name="item_type" id="type1" value="1" checked> <label for="type1">Weight Range</label>  &nbsp;&nbsp;

    							        <input type="radio" name="item_type" id="type2"  value="2" > <label for="type2">Size</label>  &nbsp;&nbsp;

    							    </div>

								</div>
								
								<div class="col-md-3">

    								<div class="form-group">

    							        <input type="radio" name="available_stock_type" id="available_stock_type1" value="1" checked> <label for="type1">Pieces</label>  &nbsp;&nbsp;

    							        <input type="radio" name="available_stock_type" id="available_stock_type2"  value="2" > <label for="type2">Weight</label>  &nbsp;&nbsp;

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



                   <div class="row">

	                   <div class="col-md-12">

	                   	<div class="table-responsive">

		                 <table id="reorder_item_list"class="table table-striped table-border">
						 <!-- table-bordered -->
		                     <thead></thead>

		                    <tbody>

							 </tbody>

							 <tfoot></tfoot>

		                 </table>

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





<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<input  type="hidden" value="0" id="i_increment" />

				<h4 class="modal-title" id="myModalLabel">Add to Cart</h4>

			    Product  : <b><span id="product_name"></span> | </b>

			    Design  : <b><span id="design_name"></span> | </b>

			    Weight Range : <b><span id="weight_name"></span> | </b>

			    Min Pcs : <b><span id="min_pcs"></span> | </b>

			    Max Pcs : <b><span id="max_pcs"></span> | </b>

			</div>

			<div id="chit_alert" style="width: 92%;margin-left: 3%;"></div>

			<form id="order_cart">

			<div class="modal-body">



			</div>

			</form>

		  <div class="modal-footer">

			<a href="#" id="create_order" class="btn btn-success">Add to Cart</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>





