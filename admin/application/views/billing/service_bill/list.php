  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Billing
            <small>Manage Billing(s)</small><span id="total_billing" class="badge bg-green"></span> 
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Billing</a></li>
            <li class="active">Billing</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    
                 <div class="box-body">
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
                    <div class="row">
				  	<div class="col-md-offset-2 col-md-8">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
						        
						        <div  class="col-md-2">
						           <label>Bill No</label>
						            <input class="form-control" id="filter_bill_no" name="filter_bill_no" type="text" placeholder="Bill No." value=""/>
						        </div>
						        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
						        <div  class="col-md-4">
						           <label>Branch</label>
						            <select class="form-control" id="branch_select" style="width:100%;"></select>
						        </div>
						        <?php }else{?>
						            <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
						        <?php }?>
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
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="search_service_bill" class="btn btn-info">Search</button>   
									</div>
								</div>
							</div>
						 </div>
	                   </div> 
	                  </div> 
                   </div>
                  <div class="table-responsive">
	                 <table id="billing_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="5%">Id</th>
	                        <th width="10%">Bill Date</th>
	                        <th width="10%">Branch</th>
	                        <th width="10%">Bill No</th>
	                        <th width="10%">Customer</th>
	                        <th width="10%">Mobile</th>
							<th width="10%">Amount</th>
							<th width="5%">Status</th>
	                        <th width="20%">Action</th>
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
        <h4 class="modal-title" id="myModalLabel">Cancel Bill</h4>
      </div>
        <div class="modal-body">
            <strong>Are you sure! You want to Cancel this bill?</strong>
            <p></p>
            <div class="row">
                
                 <div class="col-md-6 cancel_otp" style="display:none;">
            		<div class='form-group'>
        	    		<div class='input-group'>
        	                <input type="text" id="cancel_otp" name="cancel_otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required /> 
        	                <span class="input-group-btn">
        		            	<button type="button" id="verify_otp" class="btn btn-primary btn-flat" disabled >Verify</button>
        		            	<button type="button" id="resend_cancel_otp" class="btn btn-warning" disabled >Resend</button>
        		            </span>
        	            </div> 
                    </div>
            	</div>
        	
                <div class="col-md-12 bill_remarks">
                    <label>Remarks<span class="error">*</span></label>
                    <input type="hidden" id="bill_id" name="">
                    <input type="hidden" id="bill_cancel_otp" name="">
                    <textarea class="form-control" id="service_bill_remark" placeholder="Enter Remarks"  rows="5" cols="10"> </textarea>
                </div>
            </div><p></p>
            
        </div>
      <div class="modal-footer">
      	<button class="btn btn-danger" type="button" id="cancell_service_bill" disabled>Cancel</button>
      </div>
    </div>
  </div>
</div>  
<!-- / modal -->      