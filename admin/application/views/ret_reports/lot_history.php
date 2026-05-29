  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Lot History Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Lot History Report</a></li>
            <li class="active">Lot History Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Lot History Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                    <!--<div class="row">
    				  	<div class="col-md-offset-2 col-md-10">  
    	                  <div class="box box-default">  
    	                   <div class="box-body">  
    						   <div class="row">
    								
    								<div class="col-md-3"> 
    									<div class="form-group">    
    										<label>Select Lot</label> 
    										<select id="prod_select" class="form-control" style="width:100%;"></select>
    									</div> 
    								</div>
    							</div>
    						 </div>
    	                   </div> 
    	                  </div> 
                       </div>-->
                	   	<div class="box box-info stock_details">
						<div class="box-header with-border">
						  <h3 class="box-title">Lot Details</h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="lot_history_list" class="table table-bordered table-striped text-center">
										 <thead>
            							  <tr>
            							    <th>Lot No</th>
            							    <th>Category</th>
            							    <th>Bill No</th>
                                            <th>Bill Date</th>
            							    <th>Karigar Name</th>
            							    <th>Received Pcs</th>
            							    <th>Received Weight</th>
            							    <th>Sold Pcs</th>
            							    <th>Sold Weight</th>
            							  </tr>
		                            </thead> 
		                             <tbody></tbody>
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

