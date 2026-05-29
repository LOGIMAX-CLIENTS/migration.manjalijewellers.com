  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Master
            <small>Manage your Order</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
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
                    <?php if($access['add']==1){?>
                  	 <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_order/order/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
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
                        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
                        <div class="col-md-2"> 
    	                     <div class="form-group tagged">
    	                       <label>Select Branch</label>
    								<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
    	                     </div> 
    	                  </div> 
        		        <?php }else{?>
        		            <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		            <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
        		        <?php }?>
                        <div class="col-md-2"> 
                            <div class="form-group"><br>
                                <div class="input-group">
                                    <button class="btn btn-default btn_date_range" id="rpt_payment_date">
                                        <span  style="display:none;" id="rpt_payments1"></span>
                                        <span  style="display:none;" id="rpt_payments2"></span>
                                        <i class="fa fa-calendar"></i> Date range picker
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-2"> 
    						<label></label>
    						<div class="form-group">
    							<button type="button" id="cus_order_search" class="btn btn-info">Search</button>   
    						</div>
    					</div>
								
                    </div>
			  
                  <div class="table-responsive">
	                 <table id="order_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th>#</th>
	                        <th width="10%;">Order NO</th>
	                        <th >Order For</th>
                          <th>Order Type</th>
	                        <th>Order To</th>                                   
	                        <th>Order Date</th>                                       
	                        <th>Order Items</th> 
                          <th>Tag Code</th>                                      
	                        <th>Details</th>                                       
	                        <th width="20%;">Action</th>
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
      


      <div class="modal fade" id="confirm-ordercancell" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Cancell Order</h4>
        </div>
        <div class="modal-body">
          <p>
            <strong>Are you sure! You want to cancel this order?</strong>
          </p>
          <div class="col-md-12  cancel_otp_confirmation" style="display:block;white-space:no-wrap">
            <!-- <div class='form-group'>
              <div class='input-group'>
                <span class="input-group-btn"> -->
                  <!-- OTP will be sent to the administrator for approval. Do you wish to proceed? -->
                <!-- </span>
              </div>
            </div> -->
          </div>
          <div class="col-md-6 cancel_otp" style="display:none;">
            <div class='form-group'>
              <div class='input-group'>
                <input type="text" id="cancel_otp" name="cancel_otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required />
                <span class="input-group-btn">
                  <!-- <button type="button" id="verify_otp" class="btn btn-primary btn-flat" disabled>Verify</button> -->
                  <button type="button" id="resend_cancel_otp" class="btn btn-warning" disabled>Resend</button>
                </span>
              </div>
            </div>
          </div>
          <div class="row order_remarks" style="display: block;">
            <div class="col-md-12">
              <label>Remarks<span class="error">*</span></label>
              <input type="hidden" id="order_id" name="">
              <input type="hidden" id="id_orderdetails" name="">
              <textarea class="form-control" id="order_cancel_remark" placeholder="Enter Remarks" rows="5" cols="10"> </textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer order_remarks" style="display: block;">
          <button class="btn btn-danger" type="button" id="cancell_delete" disabled>Delete</button>
        </div>
        <div class="modal-footer cancel_otp_confirmation" style="display: none;">
          <button type="button" class="btn btn-success" id="send_order_cancel_otp_yes">Yes</button>
          <button type="button" class="btn btn-danger" id="send_order_cancel_otp_no">No</button>
        </div>
        <div class="modal-footer verify_otp" style="display: none;">
				<a href="#" id="verfiy_ord_can_otp" class="btn btn-success">Verify</a>
				<button type="button" class="btn btn-danger" id="order_cancel_close_modal">Close</button>
			</div>
      </div>
    </div>
  </div>  


<div class="modal fade" id="confirm-ordercancell" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cancell Order</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to cancel this order?</strong>
                       <p></p>
                    <div class="row">
                      <div class="col-md-12">
                        <label>Remarks<span class="error">*</span></label>
                        <input type="hidden" id="order_id" name="">
                        <input type="hidden" id="id_orderdetails" name="">
                        <textarea class="form-control" id="order_cancel_remark" placeholder="Enter Remarks"  rows="5" cols="10"> </textarea>
                      </div>
                    </div>
      </div>
      <div class="modal-footer">
      	<button class="btn btn-danger" type="button" id="cancell_delete" disabled>Delete</button>
      </div>
    </div>
  </div>
</div> 


<div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog" style="width:90%;">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
              </div>
              <div class="modal-body">
    			  <div class="row">
                  	<div id="order_images" style="margin-top: 2%;"></div>
    			  </div>
              </div>
              <div class="modal-footer">
                  </br>
                  <button type="button" id="close_stone_details" class="btn btn-warning"
                      data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
</div>


