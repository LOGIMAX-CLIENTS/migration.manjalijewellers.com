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
            Staff Incentive Report
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-body">  
				   	<div class="box box-info" >
						 <div class="row">
								
								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
        		                  <div class="col-md-2"> 
        		                     <div class="form-group tagged">
        		                       <label>Select Branch</label>
        									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
        		                     </div> 
        		                  </div> 
        						    <?php }else{?>
        		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
        		                  <?php }?>
								
                                
								<div class="col-md-2"> 
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
										<button type="button" id="staff_incentive_search" class="btn btn-info">Search</button>   
									</div>
								</div>
							</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="emp_list"  class="table table-bordered table-striped text-left sales_list" style="width:100%;">
            							 <thead style="text-transform:uppercase;">
            							  <tr>
            	                            <th width="1%">#</th>   
            	                            <th width="5%">Emp Name</th>   
            	                            <th width="5%;">Emp Code</th>   
            	                            <th width="5%;">Branch</th> 
            	                            <th width="5%;">Total Referred</th> 
            	                            <th width="5%;">Join Benefit Amt(Rs)</th>
            	                            <th width="5%;">Total Closed</th> 
            	                            <th width="5%;">Closing Benefit Amt(Rs)</th>
            	                            <th width="5%;">Total Amount(Rs)</th>
            							  </tr>
            		                    </thead>
            		                    <tbody style="text-align:center;"></tbody>
            		                    
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
      

