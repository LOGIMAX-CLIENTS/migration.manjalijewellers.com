  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Metal Process
            <small>Manage Process</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Process</a></li>
            <li class="active">List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Process List</h3>  <span id="total_estimation" class="badge bg-green"></span>  
                  <div class="pull-right">
				    <?php if($access['add']==1){?>
                  	 <a class="btn btn-success pull-right" id="add_estimation" href="<?php echo base_url('index.php/admin_ret_metal_process/metal_process_issue/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
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
				        <div class="col-md-offset-2 col-md-8">  
							<div class="col-md-3"> 
									<div class="form-group"> 
									<label>DateRange</label>
										<button class="btn btn-default btn_date_range" id="date_range_picker">
										 <span  style="display:none;" id="rpt_payments1"></span>
                                         <span  style="display:none;" id="rpt_payments2"></span>
										<i class="fa fa-calendar"></i> Date range picker
										<i class="fa fa-caret-down"></i>
										</button>  
									</div>
								</div> 
        		          </div>
				    </div>
			  
                  <div class="table-responsive">
	                 <table id="process_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="10%">#</th>
	                        <th width="10%">Process No.</th>
	                        <th width="10%">Date</th>
	                        <th width="10%">Process For</th>
	                        <th width="10%">Process Type</th>
	                        <th width="10%">Karigar</th>
	                        <th width="10%">Employee</th>
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
