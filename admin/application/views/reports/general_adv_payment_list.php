  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          General Advance Payment Report<span id="total" class="badge bg-green"></span>
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active"> General Advance Payment Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                
                         
                </div><!-- /.box-header -->
                <div class="box-body">
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
	                 <div class="row">
    	                   <div class="col-md-12">
    	                       
    	                 	<div class="col-md-2">
    		                  <div class="form-group">
    		                    <div class="input-group">
    		                        <span id="gen_adv_report_date_range" style="font-weight:bold;"></span><br/>
    		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">
    							  <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
    							    <span  style="display:none;" id="rpts_payments1"></span>
    							    <span  style="display:none;" id="rpts_payments2"></span>
    		                        <i class="fa fa-calendar"></i> 
    		                        <?php if($this->payment_model->entry_date_settings()==1){?>
    		                        Payment Date
    		                        <?php }else{?>
    		                        Payment Date
    		                        <?php }?> 
    		                        <i class="fa fa-caret-down"></i>
    		                      </button>
    		                    </div>
    		                 </div><!-- /.form group -->
    		                </div>
    		                
    		                <!-- Added by Durga 16-06-2023 starts here-->
                        <?php if($this->payment_model->entry_date_settings()==1){?>	
                              <div class="col-md-2">
                                  <div class="form-group">
                                    <label>Filter Date By</label>
                                    <select id="date_Select" class="form-control">
                                        <option value=1 selected>Payment Date</option>
                                        <option value=2>Custom Entry Date</option>
                                    </select>
                                    <input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
                                  </div>
                              </div>
    							      <?php }else{?>
    							        <input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
    							      <?php }?> 
                        <!-- Added by Durga 16-06-2023 ends here-->
                        
    		              <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?> 			
							<div class="col-md-2">
									<div class="form-group" >
									<label>Select Branch </label>
									<select id="branch_select" class="form-control" style="width:200px;" ></select>
									<input id="id_branch" name="scheme[id_branch]"  type="hidden" value=""/>
								</div>
							</div>
							<?php }else{?>
							<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
							<input type="hidden" id="login_branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
							<?php }?>
        		                
        		           <!-- <div class="col-md-2" >
                                <div class="form-group">
                                    <label>Group Name</label>									
                                    <select id="classify_select" class="form-control" style="width:200px; margin-left: 15px !important;"></select>
                                    <input id="id_classifications"  name="id_classification" type="hidden" value="" />
                                </div>
                            </div> -->
    						
    						   <div class="col-md-2">
    								<div class="form-group" >
    									<label>Scheme Name</label>									
    									<select id="scheme_select" class="form-control" style="width:200px; margin-left: 15px !important;"></select>
    									<input id="id_schemes"  name="id_scheme" type="hidden" value="" />
    								</div>
    						   </div>
    						   
    						   <div class="col-md-2">
    								<div class="form-group" >
    									<label>Pay Mode</label>									
    									<select id="mode_select" class="form-control" style="width:200px; margin-left: 15px !important;"></select>
    									<input id="id_pay_mode"  name="id_pay_mode" type="hidden" value="" />
    								</div>
    						   </div>
    						   
    						 
    							
    							<div class="col-md-2">
                                    <div class="form-group">
                                        <label>Paid Through</label>
                                        <select id="select_pay_mode" class="form-control" style="width:200px;">
                                            <option value="">All</option>
                                            <option value="0">Admin</option>
                                            <option value="2">Online</option>
                                            <option value="3">Admin App</option>
                                            <option value="1">Web App</option>
                                        </select>
                                    </div>
                                </div>
                <!-- Update changes for source file bugs and suggestions on 15-6-2023....  Task: account status new filter -->                
                               <!-- <div class="col-md-2">
                                      <div class="form-group">
                                        <label>Account Type</label>
                                        <select id="select_acc_type" class="form-control" style="width:200px;">
                                          <option value="" disabled selected hidden>Select Account Type</option>
                                          <option value="0">All</option>
                                          <option value="1">Active</option>
                                          <option value="2">Closed</option>
                                        </select>
                                      </div>            
                                </div>  
                                
                                <div class="col-md-2">
                                      <div class="form-group">
                                        <label>Report Type</label>
                                        <select id="report_type" class="form-control" style="width:200px;">
                                          <option value="1">Scheme Wise</option>  
                                          <option value="2">Area Wise</option>
                                          <option value="0">Common</option>
                                        </select>
                                      </div>            
                                </div>  
                                <div class="col-md-2">
                                      <div class="form-group">
                                        <label>Employee</label>
                                        <select id="employee_select" class="form-control" style="width:200px;">
                                          
                                        </select>
                                      </div>            
                                </div>  -->
                                
                                <div class="col-md-2 text-right"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="search_gen_pay_list" class="btn btn-info">Search</button>   
									</div>
							    </div>
    					</div>
	                 </div>
					 </br>
				
					 
					 <!--<div class="box box-info stock_details collapsed-box">
						<div class="box-header with-border">
						  
						  <h3 class="box-title">
						       Source Wise Report Summary &nbsp;
						    <span class="summery_description"></span>
						    <b id="summary_total_amt" class="badge bg-green" style="font-size: 16px;height: 25px;margin-left: 400px;"></b>
						  </h3>
						  
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>
						  </div>
						</div>
					    <div class="box-body collapse" style="display: none;">
					        
					       <div class="row" style="background: #ecf0f5;">
					           <div class="col-md-4" style="text-align:center;font-weight: bold;">Showroom Collection Mode-wise</div>
					           <div class="col-md-4" style="text-align:center;font-weight: bold;">Online Collection Mode-wise</div>
					           <div class="col-md-4" style="text-align:center;font-weight: bold;">AdminApp Collection Mode-wise</div>
					           <div class="box-tools pull-right">
                                 <button class="btn btn-success" id="print_source_summary" style="display:none;margin-top:-20px;"><i class="fa fa-print"></i> Print</button>
                               </div>
					       </div>
							<div class="row">
								<div class="box-body col-md-4" id="offline_modewise"></div>
                                <div class="box-body col-md-4" id="online_modewise"></div>
                                <div class="box-body col-md-4" id="adminApp_modewise"></div>
							</div>
						</div>

					</div>-->
					
					
					<!-- <div id="modesummary"></div> -->
                    <div class="row">
                        <div class="box-body">
                             <div class="table-responsive">
                                    <table id="gen_adv_rpt_table" class="table table-bordered table-striped text-center">
                                        
                                        <thead>
                                            
                                            <tr  style="text-transform:uppercase;">
                                                <th>S.No</th>
                                                <th>Payment_Date</th>
                                                <th>Custom Entry Date</th>
                                                <th>Scheme Code</th>
                                                <th>Group Code</th>
                                                <th>Acc No</th>
                                                <th>Acc Name</th>
                                                <th>Mobile</th>
                                                <th>Recpt.No</th>
                                                <th>Payment Count</th>
                                                <th>Mode</th>
                                                <th>Received.Amt</th>
                                                <th>Metal Rate</th>
                                                <th>Metal Weight</th>
                                                <th>Paid By</th>
                                                <th>Transaction Id</th>
                                                <th>Ref no</th>
                                                <th>Type</th>
                                                <th>Payment Status</th>
                                                <th>Cost Center</th>
                                                <th>Paid Through</th>
                                                <th>Account Status</th>
                                                
                                                <th>Remarks</th>
                                            </tr>
                                        </thead> 
                                        <tbody> 
                                       
                                        </tbody>
                                        <tfoot>
                                        <tr  style="text-transform:uppercase;font-weight:bold">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>Total</td>
                                                <td></td>
                                                
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                
                                                <td></td>
                                            </tr>
                                        </tfoot>
        	                        </table>
                             </div>
                            
                        </div>
                    </div>
				<!-- /.box-body -->
              </div><!-- /.box -->
              	<div class="overlay" style="display:block">
				  <i class="fa fa-refresh fa-spin"></i>
				  </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

