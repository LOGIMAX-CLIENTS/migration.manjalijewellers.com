  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Sales Transfer
          </h1>
          
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                   <button class="btn btn-default btn_date_range" id="account-dt-btn">
            	                      <span  style="display:none;" id="from_date"></span>
            	                      <span  style="display:none;" id="to_date"></span>
            	                      <i class="fa fa-calendar"></i> Date range picker
            	                      <i class="fa fa-caret-down"></i>
            	   </button> 
                   <div class="pull-right">
                        <button type="button" class="btn btn-danger pull-right" id="bt_cancel" style="display:none;">Cancel</button> 
                      	 <a class="btn btn-success pull-right" id="add_estimation" href="<?php echo base_url('index.php/admin_ret_sales_transfer/sales_transfer/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
    				  </div>
                    </div>
                
                 <div class="box-body">
                     <?php 
                        	if($this->session->flashdata('chit_alert'))
                        	 {
                        		$message = $this->session->flashdata('chit_alert');
                        ?>
                               <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
        	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        	                    <h4><i class="<?php echo $message['icon']; ?>"></i> <?php echo $message['title']; ?>!</h4>
        	                    <?php echo $message['message']; ?>
        	                  </div>
        	            <?php } ?> 
        	   
                  <div class="table-responsive">
	                 <table id="bt_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="10%">From Branch</th>
	                        <th width="10%">To Branch</th>
	                        <th width="10%">Bill Date</th>
	                        <th width="10%">Bill No</th>
	                        <th width="10%">Action</th>
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
<!--<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Billing</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this billing?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>-->
<div class="modal fade" id="confirm-billcancell" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cancell Bill</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to Cancell this bill?</strong>
                       <p></p>
                    <div class="row">
                      <div class="col-md-12">
                        <label>Remarks<span class="error">*</span></label>
                        <input type="hidden" id="bill_id" name="">
                        <textarea class="form-control" id="cancel_remark" placeholder="Enter Remarks"  rows="5" cols="10"> </textarea>
                      </div>
                    </div>
      </div>
      <div class="modal-footer">
      	<button class="btn btn-danger" type="button" id="cancell_delete" disabled>Delete</button>
      </div>
    </div>
  </div>
</div> 
<!-- / modal -->      