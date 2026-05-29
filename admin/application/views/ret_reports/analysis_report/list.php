  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Sales Analysis</small>
          </h1>
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
            					       
            					        <div class="col-md-2" > 
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
        								
                					   	  <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
                    		                  <div class="col-md-2"> 
                    		                     <div class="form-group tagged">
                    		                       <label>Select Branch</label>
                    									<select id="branch_select" class="form-control ret_branch" style="width:100%;" ></select>
                    									<input type="hidden" id="id_branch">
                    		                     </div> 
                    		                  </div>
                		                  <?php }?>
                		                  
                		                 <!-- <div class="col-md-2"> 
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
                		                  </div>-->
                		                  
                						  
                						   <div class="col-md-3"> 
                						   	 <label></label>
                		                     <div class="form-group">
                				                <button type="button" id="sales_analysis" class="btn btn-info">Search</button>   
                		                    </div>
                		                  </div>
            						   </div>
            						    <div class="box-body"> 
                	                        <div class="row">
                    	                        <div align="left" >
                                            		<ul class="nav nav-tabs">
                                            	      	<li class="active"><a id="home_city" href="" data-toggle="tab">Home City</a></li>
                                            		  	<li id="other_city"><a href="" data-toggle="tab">Other City</a></li>
                                            	    </ul>
                                            	</div>
                	                        </div>
                						 </div>
        	                        </div> 
    	                  </div> 
                       </div> 
                    </div>
                <p></p>
                    <div class="box-body home_city" >
                        <div class="table-responsive">
                            <table id="sales_analysis_list" class="table table-bordered table-striped text-left sales_list" style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th width="1%">S.No</th>
                                        <th width="3%">Area</th>
                                        <th width="3%">Branch</th>
            	                        <th width="1%">Tot Cus</th>    
            	                        <th width="1%">New Cus</th>    
            	                        <th width="1%">Without Acc</th>    
            	                        <th width="3%">Gold(G)</th>
            							<th width="3%">Silver(G)</th>
            							<th width="3%">MRP Items(Rs)</th>
            							<th width="3%">Tot Acc</th>
            							<th width="3%">Act Acc</th>
            							<th width="3%">Closed Acc</th>
                                    </tr>
                                </thead>
                            <tbody ></tbody>
                            <tfoot ><tr style="font-weight: bold;color: red">
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
							<td style="text-align: right"></td>
						</tr></tfoot>
                            </table>
                        </div>
                    </div> 
                    
                    <div class="box-body other_city" style="display:none;">
                        <div class="table-responsive">
                            <table id="sales_analysis_other_city_list" class="table table-bordered table-striped text-left sales_list" style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th width="1%">S.No</th>
                                        <th width="3%">Area</th>
                                        <th width="3%">Branch</th>
            	                        <th width="1%">Tot Cus</th>    
            	                        <th width="1%">New Cus</th>    
            	                        <th width="1%">Without Acc</th>    
            	                        <th width="3%">Gold(G)</th>
            							<th width="3%">Silver(G)</th>
            							<th width="3%">MRP Items(Rs)</th>
            							<th width="3%">Tot Acc</th>
            							<th width="3%">Act Acc</th>
            							<th width="3%">Closed Acc</th>
                                    </tr>
                                </thead>
                            <tbody ></tbody>
                            <tfoot ><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
                            </table>
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
      


