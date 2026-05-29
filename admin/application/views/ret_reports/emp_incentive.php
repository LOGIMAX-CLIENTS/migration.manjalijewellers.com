    <style>
   @media print 
   {    
        table tr td.sales
        { 
          font-weight:bold;
        }
    }
    </style> 
  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Employee Incentive Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Employee Incentive Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Employee Incentive List</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
            
				   	<div class="box box-info" >
						<div class="box-header with-border">
						  <h3 class="box-title">Employee Details</h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="emp_list" class="table table-bordered table-striped text-left" style="width:100%;">
            							 <thead style="text-transform:uppercase;">
            							  <tr>
            	                            <th width="1%">#</th>   
            	                            <th width="5%">Date</th>   
            	                            <th width="5%">Bill No/Acc No</th>   
            	                            <th width="5%">Type</th> 
            	                            <th width="5%">Tag Code</th>   
            	                            <th width="5%">Product Name</th>   
            	                            <th width="5%">Value(Rs)</th> 
            	                            <th width="5%">Remarks</th> 
            							  </tr>
            		                    </thead>
            		                    <tbody ></tbody>
            		                    <tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
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
      

