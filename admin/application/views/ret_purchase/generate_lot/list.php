<!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Lot Generate</h3>  <span id="total_count" class="badge bg-green"></span>  

                  <div class="pull-right">

                  	 &nbsp;
					   <?php if($access['add']==1){?>
					 <a class="btn btn-primary pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/lot_generate/add');?>" ><i class="fa fa-plus-circle"></i>Add</a> 
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

				       <div class="col-md-3"> 

							 <div class="form-group">

    		                    <div class="input-group">

    		                        <br>

    		                       <button class="btn btn-default btn_date_range" id="rpt_date_picker">

    							    

    		                        <i class="fa fa-calendar"></i> Date range picker

    		                        <i class="fa fa-caret-down"></i>

    		                      </button>

    		                       <span  style="display:none;" id="rpt_from_date"></span>

    							    <span  style="display:none;" id="rpt_to_date"></span>

    		                    </div>

    		                 </div><!-- /.form group -->

						</div>

						<div class="col-md-2"> 

							<label></label>

							<div class="form-group">

								<button type="button" id="search_qc_issue_details" class="btn btn-info">Search</button>   

							</div>

						</div>

				   </div>

			  

                  <div class="table-responsive">

	                 <table id="item_list" class="table table-bordered table-striped text-center">

	                    <thead>

    				          <tr>

    				            <th width="5%;">#</th> 

    				            <th width="5%;">Ref No</th> 

    				            <th width="5%;">Date</th> 

    				            <th width="5%;">Employee</th>

    				            <th width="5%;" style="text-align:right;">Issue Pcs</th> 

    				            <th width="5%;" style="text-align:right;">Issue Gwt</th> 

    				            <th width="5%;" style="text-align:right;">Issue Lwt</th> 

    				            <th width="5%;" style="text-align:right;">Nwt</th>

    				            <th width="5%;" style="text-align:right;">Recd Gwt</th> 

    				            <th width="5%;" style="text-align:right;">Recd Lwt</th> 

    				            <th width="5%;" style="text-align:right;">Recd Nwt</th> 

    				            <th width="5%;">Action</th> 

    				          </tr>

    				          <tfoot><tr style="font-weight:bold;"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>

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