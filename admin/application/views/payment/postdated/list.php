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
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Post-dated Payment List</h3> <span id="total_payments" class="badge bg-green"></span>  
                   <div class="pull-right">
				   <?php if($access['add']==1){?>
                  	 <a class="btn btn-success pull-right" id="add_post_payment" href="<?php echo base_url('index.php/postdated/payment/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
					   <?php }?>
                  </div> 
                </div>
             <!-- /.box-header --> 
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
							</br><div class="row">
								<div class="col-md-12">
								 <div class="col-md-2">
								  <div class="pull-right">
									<div class="form-group">
									   <button class="btn btn-default btn_date_range" id="ppayment-dt-btn">
									    <span  style="display:none;" id="ppayment_list1"></span>
										<span  style="display:none;" id="ppayment_list2"></span>
										<i class="fa fa-calendar"></i> Date range picker
										<i class="fa fa-caret-down"></i>
										</button>
									</div>
								 </div>					
								</div>

							<div class="col-md-8 col-md-offset-2">
							   <div class="col-md-7">
							   </div>							
								<div class="col-md-5">
									<?php if($this->session->userdata('branch_settings')==1){?>						
												<div class="form-group" style="    margin-left: 50px;">
												   <label>Select Branch &nbsp;&nbsp;</label>
													<select id="branch_select" class="form-control" style="width:150px;"></select>
													<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
												</div>
									<?php }?>
								</div>
						   </div>	
						 </div>	
					</div></br>	




						   
	   
                  <div class="table-responsive">
		              <table id="post_payment_list" class="table table-bordered table-striped text-center"  width="100%">
	                    <thead>
	                      <tr>
	                        <th><label class='checkbox-inline'><!--<input type='checkbox'  id="select_all" />-->ID</label></th>
	                        <th>Date</th>
	                        <th>Customer</th>
	                        <th>A/c Name</th>
	                        <th>Scheme code</th>
	                        <th>A/c No</th>
	                        <th>Mode</th>
	                        <th>Cheque No</th>                                           
	                        <th>Payee Bank</th>                                           
	                        <th>Drawee Name</th>                                           
	                        <th>Drawee A/c No</th>                                           
	                        <th>Drawee Bank</th>    
	                        <th>Amount (<?php echo $this->session->userdata('currency_symbol');?>)</th>
	                        <th>Status</th>
	                        <th>Action</th>
	                      </tr>
	                    </thead>
                      <!--  <tfoot ><th colspan="8"></th><th colspan="2">Total Amount</th><th id="ftotal"></th><th></th><th></th></tfoot>-->
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
