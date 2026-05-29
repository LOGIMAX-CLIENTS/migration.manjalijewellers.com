  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Other Inventory Tagging

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Other Inventory Tagging List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

                <div class="box-header with-border">

                  <h3 class="box-title">Other Inventroy Tagging List</h3><span id="total_items" class="badge bg-green"></span>      

                 <a class="btn btn-success pull-right" id="add_pro_details" href="<?php echo base_url('index.php/admin_ret_other_inventory/product_details/add');?>"><i class="fa fa-user-plus"></i> Add </a> 

                </div><!-- /.box-header -->

                <div class="box-body">

                <!-- Alert -->

                <?php 

                	if($this->session->flashdata('chit_alert'))

                	 {

                		$message = $this->session->flashdata('chit_alert');

                    ?>

                       <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

	                    <?php echo $message['message']; ?>

	                  </div>

	                  

	            <?php } ?>

				 <!-- <div class="box-body">

                        <div class="row">

                            <div class="col-md-offset-2 col-md-8">

                                <div class="box box-default">

                                    <div class="box-body">

                                        <div class="row">

                                            <div class="col-md-3">

                                                <div class="form-group">

                                                    <div class="input-group">

                                                        <br>

                                                        <button class="btn btn-default btn_date_range" id="date_range_picker">

                                                            <i class="fa fa-calendar"></i> Date range picker

                                                            <i class="fa fa-caret-down"></i>

                                                        </button>

                                                        <span style="display:none;" id="from_date"></span>

                                                        <span style="display:none;" id="to_date"></span>

                                                    </div>

                                                </div>

                                            </div>

                                            

                                            <div class="col-md-2">

                                                <label></label>

                                                <div class="form-group">

                                                    <button type="button" id="purchase_item_search" class="btn btn-info">Search</button>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                </div> -->

				

                  <div class="table-responsive">

                  <table id="other_item_product" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th>ID</th>

                        <th>Ref No</th>

                        <th>Date</th>

                        <th>Pieces</th>

                        <!-- <th>Action</th> -->
 

                      </tr>

                 	</thead>

                   <tfoot>
                    <tr style="font-weight:bold;">
                      <td></td>
                      <td></td>
                      <td></td>
                      <td style='text-align: right;'></td>
                   </tr>

                  <tfoot>

                  </table>

                  </div> 

                  

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div><!-- /.box -->

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

        <h4 class="modal-title" id="myModalLabel">Delete item</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this item?</strong>

      </div>

      <div class="modal-footer">

       	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->  

<!-- modal -->      

