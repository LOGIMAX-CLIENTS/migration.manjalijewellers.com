<!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Master

            <small>QC Issue/ Receipt Details</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Purchase</a></li>

            <li class="active">QC Issue/ Receipt Details</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">QC Issue/ Receipt Details</h3>  <span id="total_count" class="badge bg-green"></span>  

                  <div class="pull-right">

                  <?php if($access['add']==1){?>

                  	 <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/qc_issue_receipt/add');?>" ><i class="fa fa-plus-circle"></i> QC ISSUE</a> 

                  	 &nbsp;<a class="btn btn-primary pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/qc_issue_receipt/qc_entry');?>" ><i class="fa fa-plus-circle"></i> QC RECEIPT</a> 

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

								<th width="5%;">Po No</th> 

								<th width="5%;">Karigar</th> 

    				            <th width="5%;">Employee</th>

    				            <th width="5%;" style="text-align:right;">Issue Pcs</th> 

    				            <th width="5%;" style="text-align:right;">Issue Gwt</th> 

    				            <th width="5%;" style="text-align:right;">Issue Lwt</th> 

    				            <th width="5%;" style="text-align:right;">Nwt</th>

    				            <th width="5%;" style="text-align:right;">Recd Gwt</th> 

    				            <th width="5%;" style="text-align:right;">Recd Lwt</th> 

    				            <th width="5%;" style="text-align:right;">Recd Nwt</th> 

    				            <th width="5%;">Details</th>
								
								<th width="5%;">Action</th>

    				          </tr>

    				          <tfoot><tr style="font-weight:bold;"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>

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


<!-- / modal -->      

<div class="modal fade" id="qc_confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Cancel QC Issue/Receipt</h4>

        <input type="hidden" id="qc_process_id">

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to Cancel this Entry?</strong>

      </div>

      

      <div class="col-md-12 bill_remarks">

        <label>Remarks<span class="error">*</span></label>

        <textarea class="form-control" id="qc_cancel_remark" placeholder="Enter Remarks"  rows="5" cols="10"> </textarea>

     </div>

                

      <div class="modal-footer">

      	<button type="button" id="qc_cancel" class="btn btn-danger btn-confirm" data-dismiss="modal" disabled>Cancel</button>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->    

<!-- Add this to your HTML file -->
<!-- Add this to your HTML file -->
<!-- Modal for Selecting Reason and Confirming Deletion -->
<div id="deleteReasonModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header text-center">
                <button type="button" class="close close-btn" aria-label="Close">&times;</button>
                <h4 class="modal-title">Cancel QC Issue/Receipt</h4>
            </div>

            <!-- Hidden Input to Hold ID -->
            <input type="hidden" id="qc_id">

            <!-- Modal Body -->
            <div class="modal-body">
                <h5>Select Reason for Cancellation</h5>
                <select id="deleteReasonSelect" class="form-control"></select>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer text-center">
                <button id="confirmDeleteBtn" class="btn btn-danger" disabled>Confirm Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

