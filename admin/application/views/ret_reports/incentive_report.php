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
										<button type="button" id="incentive_search" class="btn btn-info">Search</button>   
									</div>
								</div>
							    <div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="incentive_pay" class="btn btn-success" style = "margin-left:-42px">Mark as Paid</button>   
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
				  <div id="cash_abstract">
				   	<div class="box box-info sales_details" >
						<div class="box-header with-border">
						  <h3 class="box-title">Sales Details</h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="incentive_list" class="table table-bordered table-striped text-left sales_list" style="width:100%;">
            							 <thead style="text-transform:uppercase;">
            							  <tr>
            	                            <th width="1%">All<input type="checkbox" id="selectAll"/>
            	                            <th width="10%">Acc Number</th>   
            	                            <th width="10%">Emp Name</th>   
            	                            <th width="10%">Branch</th>   
            	                            <th width="10%">Mobile</th>   
            	                            <th width="10%">Value(Rs)</th>  
            	                            <th width="10%">Settlement Amount(Rs)</th>
            							  </tr>
            		                    </thead>
            		                    <tbody ></tbody>
            		                    <tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
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
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

