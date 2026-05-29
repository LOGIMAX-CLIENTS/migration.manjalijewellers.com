  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Payment 
            <small>Manage your post-dated payments</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo site_url('postdated/payment/list');?>">Post Dated Payment</a></li>
            <li class="active">Payment List</li> 
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
         
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
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title"><span id="pay_type"></span>Payment List</h3>   <span id="total_payments" class="badge bg-green"></span>  
                </div>
                <div class="box-header"> 
                  <div class="form-inline">
          	 	 	 <div class="form-group">
          	 	 	  <div id="datepicker_container" >
	                       <label for="">Date</label>
             	    	   <div class='input-group date'>
			                    <input type='text' class="form-control myDatePicker datemask" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask   data-date-format="yyyy/mm/dd" id="sub_date" />
			                    <span class="input-group-addon">
			                        <span class="glyphicon glyphicon-calendar"></span>
			                    </span>			                    
			                </div>
			            </div>
	                 </div>	 
                     <div class="form-group">
                     	<label>Charges</label>
                     	 <div class="input-group col-md-3">
                     	 <span class="input-group-addon"><?php echo $this->session->userdata('currency_symbol')?></span>
                     	<input type="text" id="sub_charge" class="form-control input_currency" />
                     </div>
                      <div class="form-group">
                        	<label>Payment Status</label>
                        	<select id="sel_payment_status"  class="form-control pay_status"></select>
                     </div>
                      <div class="form-group">  
                           <label></label>
                   			<button class="btn btn-primary" id="update_status">Proceed</button>	
                     </div>  
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
         
	   
                  <div class="table-responsive">
		              <table id="rep_post_payment_list" class="table table-bordered table-striped text-center"  width="100%">
	                    <thead>
	                      <tr>
	                        <th><label class='checkbox-inline'><input type='checkbox'  id="select_all" />ID</label></th>
	                        <th>Date</th>
	                        <th>Customer</th>
	                        <th>A/c Name</th>
	                        <th>Scheme A/c No</th>
	                        <th>Mode</th>
	                        <th>Cheque No</th>                                           
	                        <th>Payee Bank</th>                                           
	                        <th>Drawee Name</th>                                           
	                        <th>Drawee A/c</th>                                           
	                        <th>Drawee Bank</th>    
	                        <th>Amount (<?php echo $this->session->userdata('currency_symbol')?>)</th> 
	                        <th>Ref No</th>                                           
	                        <th>Status</th>
	                        <th>Action</th>
	                      </tr>
	                    </thead>
                        <tfoot ><th colspan="8"></th><th colspan="2">Total Amount (<?php echo $this->session->userdata('currency_symbol')?>)</th><th id="ftotal"></th><th></th><th></th></tfoot>
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
        <h4 class="modal-title" id="myModalLabel">Delete Payment</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this payment?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
