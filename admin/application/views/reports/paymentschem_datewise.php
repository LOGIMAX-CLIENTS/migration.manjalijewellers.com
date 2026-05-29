  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Payment Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Payment Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">All Scheme On Date Report</h3>      
                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 
                	                	
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
	                     	<div class="col-md-3">
										<div class="form-group" style="    margin-left: 50px;">
										   <label>Select Date</label>
											<select id="date_Select" class="form-control" style="width:150px;">
											    <option value=1 selected>Payment Date</option>
											     <option value=2>Entry Date</option>
											</select>
											<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
										</div>
							    </div>
	                 
			            <div class="col-md-3">
			                 <label for="report_date">Report Date</label>
			                	<input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="schreport_date" name="customer[report_date]" value="<?php echo date('d-m-Y'); ?>" placeholder="Report date" type="text" />
			            </div>
						
						  
						   
						  <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-3" style="margin-top: 23px;">
									<div class="form-group" >
									<select id="branch_select" class="form-control" style="width:200px;" ></select>
									<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
								</div>
							</div>
							<?php }?>
								<div class="col-md-3" style="">
									<div class="form-group" >
									<select id="emp_select" class="form-control" style="width:200px;" ></select>
									<input id="id_employee" name="id_employee" type="hidden" value=""/>
								</div>
							</div>
							
								<div class="col-md-3" style="margin-top: 23px;">
									<div class="form-group" >
									<select id="pay_mode" class="form-control" style="width:200px;">
									    <option value=''>All</option>
									    <option value=0>Offline</option>
									    <option value=1>Online</option>
									</select>
									<input id="added_by" name="added_by" type="hidden" value=""/>
								</div>
							</div>
							
		                 </div>
	                 </div>
				
		<!-- gst settings -->
				<input id="gstset" type="hidden" value="<?php echo $this->payment_model->get_gstsettings();?>"/>
				
		<!-- gst settings -->
				
				<?php if($this->payment_model->get_gstsettings()==1){?>
				
				
                  <table id="schdatewise_report" class="table table-bordered table-striped text-center date_pay_report"  >
                    <thead>
                      <tr>
                        <th>Mode</th>      
                        <th>Group</th>      
                        <th>Branch</th>
                        <th>No.of.Receipts</th>
						<th>PayAmount</th> 
                        <th>SGST</th>							
					    <th>CGST</th>							
					    <th>T.GST</th>
						<th>Total</th>                        
                                               
                        					                      
                      </tr>
                    </thead>
                    <tfoot id="schemedate" style="font-weight:600;">
					  <tr>
                        <td></td> <td></td> <td></td><td></td><td></td><td></td><td ></td><td ></td><td ></td> <td ></td>               
                      </tr>				  
						<tr>
                        <td></td> <td style="text-align:center;"></td> <td></td><td></td><td></td><td></td><td ></td><td ></td>                
                      </tr> 
					 <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td></td><td ></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td></td><td ></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td></td><td ></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td></td><td ></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td></td><td ></td><td ></td>
                      </tr>
					  <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td></td><td ></td><td ></td>
                      </tr>
                      <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td></td><td ></td><td ></td>
                      </tr>
					  <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td></td><td ></td><td ></td>
                      </tr>
					  
                    </tfoot> 
                  </table>
				  <?php }else { 
				  
				  
				  ?>
				  
				  <table id="schdatewise_report" class="table table-bordered table-striped text-center date_pay_report"  >
                    <thead>
                      <tr>
                        <th>Mode</th>      
                        <th>Group</th>
                        <th>Branch</th>
                        <th>No.of.Receipts</th>
						<th>PayAmount</th>
						<th>Total</th>                        
                                               
                        					                      
                      </tr>
                    </thead>
                    <tfoot id="schemedate" style="font-weight:600;">
					  <tr>
                        <td></td> <td></td> <td></td><td></td><td></td><td ></td>               
                      </tr>				  
						<tr>
                        <td></td> <td style="text-align:center;"></td> <td></td><td></td><td></td><td ></td>               
                      </tr> 
					 <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td ></td>
                      </tr> <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td ></td>
                      </tr> 
                      <tr>
                        <td></td> <td style="text-align:left;"></td> <td></td><td style="text-align:right;"></td><td></td><td ></td>
                      </tr> 
                    </tfoot> 
                  </table>
				  
				  
				  <?php }?>
				  
				  
                </div><!-- /.box-body -->
				<div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				  </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
<!-- / modal -->