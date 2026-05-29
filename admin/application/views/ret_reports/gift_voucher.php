  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Sales Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Sales Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Gift Voucher Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
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
										<select id="branch_select" class="form-control branch_filter"></select>
									</div> 
								</div> 
								<?php }else{?>
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
								<?php }?> 
								
								<div class="col-md-3"> 
									<div class="form-group tagged">
										<label>Report Type</label>
										<select id="report_type" class="form-control">
										    <option value="1">Issued</option>
										    <option value="2">Reedemed</option>
										</select>
									</div> 
								</div> 
								
								<div class="col-md-3"> 
									<div class="form-group">    
										<label>Date</label> 
										<?php   
											$fromdt = date("d/m/Y");
											$todt = date("d/m/Y");
									    ?>
			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
									</div> 
								</div>
								<!--<div class="col-md-2"> 
									<label>Select Village</label>
									<select id="village_select" class="form-control" style="width:100%;"></select>
								</div>
								<div class="col-md-2"> 
									<label>Select Customer</label>
									<select id="cus_select" class="form-control" style="width:100%;"></select>
								</div>-->
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="gift_search" class="btn btn-info">Search</button>   
									</div>
								</div>
								<div class="col-md-1"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="cancel_gift" class="btn btn-danger">Cancel</button>   
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
						  <h3 class="box-title">Sales Details</h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row" id="issued" style="display:none;">  
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="gift_issued" class="table table-bordered table-striped text-center">
										 <thead>
                							  <tr style="text-transform:uppercase;">
                							    <th width="10%"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>
                							    <th width="10%">Code</th>
                							    <th width="10%">Branch Name</th>
                							    <th width="10%">Date</th>
                							    <th width="10%">Issue To</th>
                							    <th width="10%">Amount</th>
                							    <th width="10%">Status</th>
                							  </tr>
                		                    </thead> 
                		                    <tbody></tbody>
                		                    <tfoot></tfoot>
									 </table>
								  </div>
								</div> 
							</div> 
							<div class="row" id="received" style="display:none;">
								<div class="box-body">
								   <div class="table-responsive">
									 <table id="gift_reeived" class="table table-bordered table-striped text-center" >
										 <thead>
                							  <tr style="text-transform:uppercase;">
                							   
                							    <th width="10%">Branch Name</th>
                							     <th width="10%">Bill No</th>
                							      <th width="10%">Code</th>
                							    <th width="10%">Date</th>
                							    <th width="10%">Beneficiary</th>
                							    <th width="10%">Amount</th>
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
      

