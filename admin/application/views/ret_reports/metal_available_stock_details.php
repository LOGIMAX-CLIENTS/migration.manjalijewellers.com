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
            Available Stock Report
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
						       <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
        		                  <div class="col-md-3"> 
        		                     <div class="form-group tagged">
        		                       <label>Select Branch</label>
        									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
        		                     </div> 
        		                  </div> 
        		                  
        		                  <!--<div class="col-md-3"> 
        		                     <div class="form-group tagged">
        		                       <label>Select Type</label>
        									<select id="acc_stock_report_type" class="form-control" style="width:100%;">
        									    <option value="0">Old Metal</option>
        									    <option value="1">Bullion Purchase</option>
        									 </select>
        		                     </div> 
        		                  </div> -->
        		                  
        						    <?php }else{?>
        		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
        		                  <?php }?>
								<div class="col-md-3" style="display:none;"> 
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
										<button type="button" id="metal_available_stock_search" class="btn btn-info">Search</button>   
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
								   <div class="table-responsive ">
									  <table id="stock_details" class="table table-bordered table-striped text-center sales_list" style="width:100%;">
            							 <thead style="text-transform:uppercase;">
            							  <tr>
            	                            <th width="5%">Category</th>   
            	                            <th width="5%">Product</th>   
            	                            <th width="4%">Gross Wt</th>   
            	                            <th width="4%">Net Wt</th> 
            	                             <th width="4%">Purity</th>   
            	                            <th width="4%">Value(Rs)</th>   
            	                            <th width="4%">Action</th>   
            							  </tr>
            		                    </thead>
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

