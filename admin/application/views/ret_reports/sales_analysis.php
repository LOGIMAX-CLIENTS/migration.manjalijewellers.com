  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Sales Analysis</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Sales Analysis</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Sales Analysis Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                      <div class="row">
    				  <div class="col-md-offset-2 col-md-10">  
    	                  <div class="box box-default">  
        	                   <div class="box-body">  
            					   <div class="row">
            					       
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
        								
                					   	  <!-- <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
                    		                  <div class="col-md-2"> 
                    		                     <div class="form-group tagged">
                    		                       <label>Select Branch</label>
                    									<select id="branch_select" class="form-control ret_branch" style="width:100%;" ></select>
                    									<input type="hidden" id="id_branch">
                    		                     </div> 
                    		                  </div>
                		                  <?php }?> -->

                                      <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

                                <div class="col-md-2"> 

                                  <div class="form-group tagged">

                                      <label class="trans_centre">Select Branch</label>

                                    <select id="branch_select" class="form-control ret_branch"></select>

                                  </div> 

                                </div> 


                                <?php }else{?>

                                <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

                                <?php }?>
                                                                      
                		                  <div class="col-md-2"> 
                		                     <div class="form-group tagged">
                		                       <label>Select Zone</label>
                									<select id="select_zone" class="form-control" style="width:100%;" ></select>
                		                     </div> 
                		                  </div>
                		                  
                		                  <div class="col-md-2"> 
                		                     <div class="form-group tagged">
                		                       <label>Select Product</label>
                									<select id="prod_select" class="form-control" style="width:100%;" ></select>
                		                     </div> 
                		                  </div>
                		                  
                		                  <div class="col-md-2"> 
                		                     <div class="form-group tagged">
                		                       <label>Select Village</label>
                									<select id="select_village" class="form-control" style="width:100%;" ></select>
                		                     </div> 
                		                  </div>
                		                  
                						  
                						   <div class="col-md-3 pull-right"> 
                						   	 <label></label>
                		                     <div class="form-group">
                				                <button type="button" id="sales_analysis" class="btn btn-info">Search</button>   
                		                    </div>
                		                  </div>
            						   </div>
        	                        </div> 
    	                  </div> 
                       </div> 
                    </div>
                <p></p>
                <div class="nav-tabs-custom">

                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#retail" id="retail_tab" data-toggle="tab">Retail</a></li>  
                        <li ><a href="#crm" id="crm_tab" data-toggle="tab">CRM</a></li>
                    </ul>
                    <div class="tab-content">
                         <div class="tab-pane active" id="retail">
                                <div class="nav-tabs-custom">
                                     <div class="tab-content">
                                            <div class="box-body">
                                                <div class="table-responsive">
                                                    <table id="sales_analysis_list" class="table table-bordered table-striped text-left sales_list" style="width: 100% !important;">
                                                        <thead>
                                                            <tr>
                                                                <th width="3%">Area</th>
                                    	                        <th width="3%">Pcs</th>    
                                    	                        <th width="3%">Gwt</th>
                                    							<th width="3%">Nwt</th>
                                    							<th width="3%">Amount</th>
                                                            </tr>
                                                        </thead>
                                                    <tbody ></tbody>
                                                    </table>
                                                </div>
                                            </div> 
                                     </div>
                                </div>
                          </div>
                          <div class="tab-pane" id="crm">
                                <div class="nav-tabs-custom">
                                     <div class="tab-content">
                                            <div class="box-body">
                                                <div class="table-responsive">
                                                    <table id="chit_analysis_list" class="table table-bordered table-striped text-left sales_list" style="width: 100% !important;">
                                                        <thead >
                                                            <tr>
                                    	                        <th>id</th>
                                    	                        <th>Customer</th>
                                    	                        <th>Mobile</th>
                                    	                        <th>Zone</th>
                                    	                        <th>Area</th>
                                    	                        <th>Total Acc</th>
                                    	                        <th>Active Acc</th>
                                    	                        <th>Closed Acc</th>
                                    	                        <th>No.of Bills</th>
                                    	                      </tr>
                                                        </thead>
                                                            <tbody></tbody>
                                                    </table>
                                                </div>
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
      


