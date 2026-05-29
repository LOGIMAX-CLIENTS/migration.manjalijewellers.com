  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Day Closing Report
          </h1>
         
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
                 <div class="box-body">  
                  <div class="row">
				  	<div class="col-md-offset-2 col-md-8">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
						       
						           <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
            		                  <div class="col-md-3"> 
            		                     <div class="form-group tagged">
            		                       <label>Select Branch</label>
            									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
            		                     </div> 
            		                  </div> 
            						    <?php }else{?>
            		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
        		                  <?php }?>
        		                  
						            <div class="col-md-3"> 
    									<div class="form-group">    
    										<label>Date</label> 
    										<?php   
    											$fromdt = date("m/d/Y");
    									    ?>
    			                   		    <input type="text" class="form-control pull-right datePicker" id="" placeholder="From Date -  To Date" value="<?php echo $fromdt?>" readonly="">  
    									</div> 
    								</div>
    								
                                    
                                        
    								<div class="col-md-2"> 
    									<label></label>
    									<div class="form-group">
										    <button type="button" id="day_close_report_search" class="btn btn-info">Search</button>   
									    </div>
    								</div>
							</div>
						 </div>
	                   </div> 
	                  </div> 
                   </div> 
                
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
				   	<div class="box box-info stock_details">
						<div class="box-body">
							<div class="row">  
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="day_close_report" class="table table-bordered table-striped text-center">
										 <thead>
                							  <tr style="text-transform:uppercase;">
                							    
                							    <th width="10%">Branch</th>
                							    <th width="10%">Stock Type</th>
                							    <th width="10%">No.of Records</th>
                							    <th width="10%">Action</th>
                							  </tr>
                		                    </thead> 
                		                    <tbody></tbody>
                		                    <tfoot></tfoot>
									 </table>
								  </div>
								</div> 
							</div> 
						</div>
					</div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      
<div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Update Stock</h4>
      </div>
      <div class="modal-body">
                <input type="hidden" id="stock_type">
                <input type="hidden" id="stock_branch">
               <strong>Are you sure! You want to Update The Stock?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" id="update_stock">Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal --> 
