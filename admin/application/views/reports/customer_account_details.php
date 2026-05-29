  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Chit Analysis Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Retail Reports</a></li>
            <li class="active">Chit Analysis Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Chit Analysis Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                     <div class="row">
    						<div class="col-xs-12" id="success_res" style="display:none;">
    						<!-- Alert -->
    							   <div class="alert alert-success alert-dismissable">
    								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    								<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?> <span id="success_msg"></span>!</h4>
    							  </div>
    						</div>
    						
    						<div class="col-xs-12" id="failed_res" style="display:none;">
    						<!-- Alert -->
    							   <div class="alert alert-danger alert-dismissable">
    								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    								<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?><span id="failed_msg"></span>!</h4>
    							  </div>
    						</div>
    						
    				   </div>
                  <div class="row">
				  	<div class="col-md-offset-2 col-md-10">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
								<div class="col-md-2"> 
									<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
								
									<div class="form-group tagged">
										<label>Select Branch</label>
										<select id="branch_select" class="form-control branch_filter"></select>
									</div> 
									<?php }else{?>
										<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
									<?php }?> 
								</div>  
								<div class="col-md-2"> 
    								<div class="form-group tagged">
    								    	<label>Select Scheme</label>
    								    	<select id="scheme_select" class="form-control scheme_select"></select>
    								</div>
								</div>
						        <div class="col-md-2">
						          <label>Select Date Range</label>
        		                  <div class="form-group">
        		                    <div class="input-group">
        		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">
        							    <span  style="display:none;" id="rpt_payments1"></span>
        							    <span  style="display:none;" id="rpt_payments2"></span>
        		                        <i class="fa fa-calendar"></i> Date range picker
        		                        <i class="fa fa-caret-down"></i>
        		                      </button>
        		                    </div>
        		                 </div>
        		                </div>
						
							</div>
						 </div>
	                   </div> 
	                  </div> 
                   </div> 
                	   	<div class="box box-info stock_details">
						<div class="box-header with-border">
						  <h3 class="box-title">Account List</h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="acc_list" class="table table-bordered table-striped text-center">
										 <thead>
            							  <tr>
            							    <th>Id</th>
            							    <th>Customer</th>
            							    <th>Mobile</th>
            							    <th>Scheme</th>
            							    <th>Acc/No</th>
            							    <th>Tot Acc</th>
            							    <th>Active Acc</th>
            							    <th>Start Date</th>
            							    <th>Ins</th>
            							    <th>Pending Due</th>
            							    <th>Last Paid Date</th>
            							    <th>Paid Before (in Months)</th>
            							    <th>Closing Date</th>
            							    <th>Status</th>
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

