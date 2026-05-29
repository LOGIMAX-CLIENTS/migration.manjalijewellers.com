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
			 <small>Metal Stock Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Metal Stock Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Metal Stock Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
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
        									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
        		                     </div> 
        		                  </div> 
        						    <?php }else{?>
        		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
        		                  <?php }?>
        		                  
        		                  <div class="col-md-3"> 
        		                     <div class="form-group tagged">
        		                       <label>Report Type</label>
        									<select id="report_type" class="form-control" style="width:100%;">
        									    <option value="1">Sales Return</option>
        									    <option value="2">Partly Sale</option>
        									    <option value="3">Old Metal</option>
        									    <option value="4">Metal Process</option>
        									    <option value="5">Bullion Purchase</option>
        									</select>
        		                     </div> 
        		                  </div> 
        		                  
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
										<button type="button" id="metal_stock_search" class="btn btn-info">Search</button>   
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
    						  <h3 class="box-title">Stock Details</h3>
    						  <div class="box-tools pull-right">
    							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
    						  </div>
    						</div>
    						<div class="box-body">
    							<div class="row">
    								<div class="box-body">
    								   <div class="table-responsive">
    									  <table id="sales_details" class="table table-bordered table-striped" style="width:100%;text-align:center;">
                							 <thead style="text-transform:uppercase;">
                							  <tr>
                	                            <th width="5%">Product</th>   
                	                            <th width="4%">Op Blc Pcs</th>   
                	                            <th width="4%">Op Blc Gwt</th>   
                	                            <th width="4%">Op Blc Nwt</th>   
                	                            <th width="4%">I/W Pcs</th>   
                	                            <th width="4%">I/W Gwt</th>   
                	                            <th width="4%">I/W Nwt</th>   
                	                            <th width="4%">O/W Pcs</th>   
                	                            <th width="4%">O/W Gwt</th>   
                	                            <th width="4%">O/W Nwt</th>   
                	                            <th width="4%">Closing Pcs</th>   
                	                            <th width="4%">Closing Gwt</th>   
                	                            <th width="4%">Closing Nwt</th>   
                							  </tr>
                		                    </thead>
                		                    <tbody></tbody>
                		                    <tfoot><tr style="font-weight:bold;"><td>Total</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
    		                            </table>
    								  </div>
    								</div> 
    							</div> 
    						</div>
    					</div>
    					
    					<div class="box box-info purchase_item_details" style="display:none;">
    						<div class="box-header with-border">
    						  <h3 class="box-title">Stock Details</h3>
    						  <div class="box-tools pull-right">
    							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
    						  </div>
    						</div>
    						<div class="box-body">
    							<div class="row">
    								<div class="box-body">
    								   <div class="table-responsive">
    									  <table id="purchase_item_details" class="table table-bordered table-striped text-left sales_list" style="width:100%;">
                							 <thead style="text-transform:uppercase;">
                							  <tr>
                                                <th width="5%">Category</th>   
                                                <th width="4%">Op Blc Gwt</th>   
                                                <th width="4%">Op Blc Nwt</th>   
                                                <th width="4%">I/W Gwt</th>   
                                                <th width="4%">I/W Nwt</th>   
                                                <th width="4%">O/W Gwt</th>   
                                                <th width="4%">O/W Nwt</th>   
                                                <th width="4%">Closing Gwt</th>   
                                                <th width="4%">Closing Nwt</th>   
                							  </tr>
                		                    </thead>
                		                    <tbody style="text-align: right;"></tbody>
                		                    <tfoot><tr style="font-weight:bold;"><td>Total</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
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

