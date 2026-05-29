<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Feedback Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Feedback Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Feedback Report</h3>  
                </div>
                <div class="box-body">
                   <div class="row">
	                   <div class="col-md-12">
	                   	<div class="table-responsive">
		                    <table id="feedback_list" class="table table-bordered table-striped text-center">
		                    <thead>
                          <tr>
                            <th width="10%">S.No</th>
                            <th width="10%">Customer Name</th>
                            <th width="10%">Mobile</th>
                            <th width="10%">Feedback Date</th>
                            <th width="10%">Feedback Taken By</th>
                          </tr>
		                    </thead> 
                           
							   
		                 </table>
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

      <div class="modal fade" id="modal_feedback" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Customer Feedback</h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error'></div>
            </div>
              <div class="row">
                <div class="form-group" id='fb_report_content'>
                  
                </div>
              </div>  
            </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>