 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Metal Process Report
            <small>Manage Process</small>
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
								<div class="col-md-3"> 
									 <div class="form-group">
            		                    <div class="input-group">
            		                        <br>
            		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">
            							    <span  style="display:none;" id="rpt_payments1"></span>
            							    <span  style="display:none;" id="rpt_payments2"></span>
            		                        <i class="fa fa-calendar"></i> Date range picker
            		                        <i class="fa fa-caret-down"></i>
            		                      </button>
            		                    </div>
            		                 </div><!-- /.form group -->
								</div>
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="metal_process_detailed_search" class="btn btn-info">Search</button>   
									</div>
								</div>
							<!--	<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="cash_abstract_print" class="btn btn-info">Print</button>   
									</div>
								</div>
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="export_csv" class="btn btn-info">Export</button>   
									</div>
								</div>-->
							</div>
						 </div>
	                   </div> 
	                  </div> 
                   </div>
			  
                  <div class="table-responsive">
	                 <table id="process_details" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="10%">Pocket No</th>
	                        <th width="10%">#</th>
	                        <th width="10%">Date</th>
	                        <th width="10%">Pcs</th>
	                        <th width="10%">Gross Wt</th>
	                        <th width="10%">Net Wt</th>
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