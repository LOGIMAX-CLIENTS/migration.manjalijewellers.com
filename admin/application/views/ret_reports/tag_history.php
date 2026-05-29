  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Tag History Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Tag History Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Tag History Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                  <div class="row">
				  	<div class="col-md-offset-2 col-md-8">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
						            <div class="col-md-2"> 
    									<label>Tag Code</label>
    									<div class="form-group">
    									    <input type="text" class="form-control" id="tag_number" placeholder="Enter Tag Code">
    									    <!--<input type="hidden" id="tag_id">-->
    									</div>
    								</div>
    								
                                    <div class="col-md-2"> 
                                        <label>Old Tag</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="old_tag_number" placeholder="Enter Tag Code">
                                        </div>
                                    </div>

									<div class="col-md-2"> 
                                        <label>HUID</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="tag_huid" placeholder="Enter HUID">
                                        </div>
                                    </div>
                                        
    								<div class="col-md-2"> 
    									<label></label>
    									<div class="form-group">
										    <button type="button" id="tag_history_search" class="btn btn-info">Search</button>   
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
						<div class="box-header with-border">
						  <h3 class="box-title">Tag Details</h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">  
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="tag_history" class="table table-bordered table-striped text-center">
										 <thead>
                							  <tr style="text-transform:uppercase;">
                							    <th width="10%">Image</th>
                							    <th width="10%">Tag Id</th>
                							    <th width="10%">Tag Code</th>
                							    <th width="10%">Old Tag Code</th>
                							    <th width="10%">Tag Date</th>
                							    <th width="10%">Supplier</th>
                							    <th width="10%">Product</th>
                							    <th width="10%">Design</th>
                							    <th width="10%">Sub Design</th>
                							    <th width="10%">Gross Wt</th>
                							    <th width="10%">Net Wt</th>
                							    <th width="10%">Tag Status</th>
                							    <th width="10%">Emp</th>
                							    <th width="10%">Detail</th>
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
      

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
      </div>
      <div class="modal-body">
        <img src="" id="imagepreview" style="width: 300px; height: 264px;" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

