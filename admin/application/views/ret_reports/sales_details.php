  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Sales Analysis Details</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Sales Analysis Detail</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Sales Analysis Detail Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                    <div class="tab-content">
                         <div class="tab-pane active" id="retail">
                                <div class="nav-tabs-custom">
                                     <div class="tab-content">
                                            <div class="box-body">
                                                <div class="table-responsive">
                                                    <table id="sales_analysis_list" class="table table-bordered table-striped text-left sales_list" style="width: 100% !important;">
                                                        <thead>
                                                            <tr>
                                                                <th width="3%">Product Name</th>
                                                                <th width="3%">Weight Range</th>
                                    	                        <th width="3%">Pcs</th>    
                                    	                        <th width="3%">Gwt</th>
                                    							<th width="3%">Nwt</th>
                                    							<th width="3%">Amount</th>
                                    							<th width="3%">Details</th>
                                                            </tr>
                                                        </thead>
                                                    <tbody></tbody>
                                                    <tfoot><tr style="font-weight:bold;"><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
                                                    </table>
                                                </div>
                                            </div> 
                                     </div>
                                </div>
                          </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


