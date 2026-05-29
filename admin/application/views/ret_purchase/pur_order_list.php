  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Master

            <small>Purchase Order List</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Purchase</a></li>

            <li class="active">Order</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">



               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Order List</h3>  <span id="total_count" class="badge bg-green"></span>

                  <div class="pull-right">

				  <?php if($access['add']==1){ ?>
                  	 <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/purchase/add');?>" ><i class="fa fa-plus-circle"></i> Add</a>
					   <?php }?>
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



				   <div class="row">

				       <div class="col-md-12">

				            <div class="col-md-2">

                                <div class="form-group">

                                    <div class="input-group">



                                        <button class="btn btn-default btn_date_range" id="rpt_date_picker"

                                             <i class="fa fa-calendar"></i> Date range picker

                                             <i class="fa fa-caret-down"></i>

                                         </button>

                                         <span style="display:none;" id="rpt_from_date"></span>

                                         <span style="display:none;" id="rpt_to_date"></span>

                                    </div>

                                </div><!-- /.form group -->

                            </div>

    				       <div class="col-md-1">

    				           <label></label>

							    <?php if($access['delete']==1){?>
    					        <button type="button" id="order_cancel" class="btn btn-danger">Cancel</button>
								<?php }?>
    					    </div>

							<div class="col-md-2">

							<select id="select_order" class="form-control" style="width:100%">
							<option value =0>All</option>
							<option value =1>Stock Order</option>
							<option value =2>Customer Order</option>
							<option value =3>Repair Order</option>

						  </select>

							</div>

    					    <div class="col-md-3">

    					        <label></label>

    					        <button type="button" id="order_close" class="btn btn-warning">Close</button>

    					    </div>

    					    <div class="col-md-2">

								<div class="form-group">

									<button type="button" id="pur_ord_search" class="btn btn-info">Search</button>

								</div>

							</div>

				       </div>



				   </div>



                  <div class="table-responsive">

	                 <table id="order_list" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th width="1%;">#</th>

	                        <th width="5%;">PO NO</th>

	                        <th width="5%;">Order Date</th>

	                        <th width="5%;">Status</th>

	                        <th width="5%;">Karigar</th>

	                        <th width="5%;">Mobile</th>

	                        <th width="1%;">Order For</th>

	                        <th width="5%;">Order Pcs</th>

	                        <th width="5%;">Order Wt</th>

	                        <th width="5%;">Delivered Pcs</th>

	                        <th width="5%;">Delivered Wt</th>

	                        <th width="10%;">Cus Order No</th>

	                        <th width="10%;">Order Branch</th>

	                        <th width="10%;">Smith Due Date</th>

	                        <th width="10%;">Reject Reason</th>

	                        <th width="10%;">Rejected Date</th>

	                        <th width="10%;">Action</th>

	                      </tr>

	                    </thead>

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

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Order</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Order?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->

