  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Estimation
            <small>Manage Estimation(s)</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Estimation</a></li>
            <li class="active">Estimation</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Estimation List</h3>  <span id="total_estimation" class="badge bg-green"></span>  
                  <div class="pull-right">
                  	 <a class="btn btn-success pull-right" id="add_estimation" href="<?php echo base_url('index.php/admin_ret_estimation/estimation/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
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
				   	<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
				    <div class="row">
				        <div class="col-md-offset-2 col-md-8">  
    				        <div class="col-md-3"> 
        	                     <div class="form-group tagged">
        	                       <label>Select Branch</label>
        								<select id="branch_select" class="form-control ret_branch"></select>
        	                     </div> 
            		          </div> 
            		          <div class="col-md-3"> 
        	                     <div class="form-group"> 
        	                      <label>DateRange</label>
            	                    <button class="btn btn-default btn_date_range" id="account-dt-btn">
            	                      <span  style="display:none;" id="est_list1"></span>
            	                      <span  style="display:none;" id="est_list2"></span>
            	                      <i class="fa fa-calendar"></i> Date range picker
            	                      <i class="fa fa-caret-down"></i>
            	                     </button>  
            	                   </div>
            		          </div> 
        		          </div>
				    </div>
				   <?php }else{?>
				   	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
				   <?php }?>
				   
			  
                  <div class="table-responsive">
                      <input type="hidden" id="id_branch"  value="<?php echo $this->session->userdata('id_branch') ?>">
	                 <table id="estimation_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="10%">Est No.</th>
	                        <th width="5%">EMP Name</th>
                            <th width="5%">EMP Code</th>
	                        <th width="10%">Date</th>
	                        <th width="10%">Customer</th>
	                        <th width="10%">Mobile</th>
							<th width="10%">Tot.Amount</th>
							<th width="15%">Product Name</th>
							<th width="10%">Bill No</th>
							<th width="10%">Created through</th>
							<th width="10%">Rating</th>
							<th width="10%">Review</th>
							<th width="10%">Remarks</th>
	                        <th width="15%">Action</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Estimation</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this estimation?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
