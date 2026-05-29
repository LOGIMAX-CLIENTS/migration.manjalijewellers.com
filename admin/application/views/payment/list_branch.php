  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Payment 
            <small>Manage your offline payments</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo site_url('payment/list');?>">Payment</a></li>
            <li class="active">Payment List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
         
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Payment List</h3> <span id="total_payments" class="badge bg-green"></span>  
                  <div class="pull-right">
                  	 <a class="btn btn-success pull-right" id="add_post_payment" href="<?php echo base_url('index.php/payment/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
				  </div>
                </div>
                    
                 
                <div class="box-body">
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
						   
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-2">
										<div class="pull-left">
											<div class="form-group">
											   <button class="btn btn-default btn_date_range" id="payment-dt-btn">
												<i class="fa fa-calendar"></i> Date range picker
												<i class="fa fa-caret-down"></i>
												</button>
											</div>
										 </div>						
									</div>
									<div class="col-md-2">
											<div class="form-group" >
												<label for="" ><a  data-toggle="tooltip" title="Select branch to create Scheme Account"> Select Branch  </a> <span class="error">*</span></label>
												<select id="branch_select" class="form-control"></select>
												<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
											</div>
									   
									 </div>
								</div>
						   </div>
                  <div class="table-responsive">
	                 <table id="payment_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th>ID</th>
	                        <th>Date</th>
	                        <th>Customer</th>
	                        <th>A/c Name</th>
	                        <th>A/c No</th>
	                        <th>Mobile</th>                                          
	                        <th>Type</th>                                           
	                        <th>Mode</th>                                           
	                        <th>Metal Rate (<?php echo $this->session->userdata('currency_symbol');?>)</th>  
	                        <th>Metal Weight(g)</th>                                           
	                        <th>Amount (<?php echo $this->session->userdata('currency_symbol');?>)</th>   
	                        <th>Ref No</th>                                           
	                        <th>Status</th>                                           
	                        <th>Action</th>
	                      </tr>
	                    </thead> 

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
<!-- modal -->      

<div class="modal fade" id="pay_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header bg-yellow">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel" align="center">Transaction Detail</h4>

      </div>

      <div class="modal-body">

    	       

           <div class="trans-det"></div>    

      </div>

      <!--<div class="modal-footer">

      	<div class="col-sm-6 col-sm-offset-3">

          <button type="button" class="btn btn-block btn-warning" data-dismiss="modal">Close</button>

        </div>

      </div>-->

    </div>

  </div>

</div>

<!-- / modal -->       
