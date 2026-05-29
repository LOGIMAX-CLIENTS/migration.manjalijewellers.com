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
            <li class="active">Payment List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">All Scheme</h3> <span id="total" class="badge bg-green"></span>     
                         
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
	                        <?php if($this->payment_model->entry_date_settings()==1){?>	
								<div class="col-md-2">
										<div class="form-group">
										   <label>Select Date</label>
											<select id="date_Select" class="form-control" style="width:150px;">
											    <option value=1 selected>Payment Date</option>
											     <option value=2>Entry Date</option>
											</select>
											<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
										</div>
							    </div>
							    <?php }?>
							    
	                 	<div class="col-md-2" style="margin-top: 20px;">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">
							  <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
							    <span  style="display:none;" id="rpt_payments1"></span>
							    <span  style="display:none;" id="rpt_payments2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		                </div>
		                
		                
						
						   <div class="col-md-2">
								<div class="form-group" >
									<label>Scheme name</label>									
									<select id="scheme_select" class="form-control"></select>
									<input id="id_schemes"  name="id_scheme" type="hidden" value="" />
								</div>
						   </div>
						   
	               
						  <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-2">
									<div class="form-group" >
									<label>Select Branch </label>
									<select id="branch_select" class="form-control" ></select>
									<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
								</div>
							</div>
							<?php }?>
								<div class="col-md-2">
										<div class="form-group">
										   <label>Select Employee</label>
											<select id="emp_select" class="form-control"></select>
											<input id="id_employee" name="scheme[id_employee]" type="hidden" value=""/>
										</div>
									</div>
		                
		                
		                	<div class="col-md-2">
										<div class="form-group">
										   <label>Select Accounts</label>
											<select id="acc_Select" class="form-control" style="width:150px;">
											    <option value='0' selected>All</option>
											     <option value=1>Active</option>
											     <option value=2>Closed</option>
											</select>
											<input id="id_pay" name="scheme[id_pay]" type="hidden" value=""/>
										</div>
							    </div>
		                
		                 </div>
	                 </div>
					 
					 <?php if($this->payment_model->get_gstsettings()==1){?>
					 
                      <table id="report_payment_daterange" class="table table-bordered table-striped text-center ">
	                    <thead>
	                      <tr>
							<th>S.No</th>							
	                        <th>Scheme Code</th>
							<th>M.No</th>														<th>Recpt.No</th>
							<th>A/c.Name</th>
							<th>Amount</th>
							<th>Ins</th>
							<th>Pay.Date</th>
							<th>Emp code</th>
							<th>Payment Ref No</th>
						    <th>Transaction Id</th>
						    <th>Card No</th>
							<th>Mode</th>
							<th>M.Rate</th>
							<th>Weight(g)</th>
							<th>B.Name</th>
							<th>Pay.Amt</th>
							<th>Discount</th>
							<th>Incen</th>
							<th>SGST</th>							
							<th>CGST</th>							
							<th>T.GST</th>
							<th>Total</th> 
	                      </tr>
	                    </thead>
						<tfoot>
							<tr><th></th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
							<!--<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>	-->					
						</tfoot> 
					<tbody> 
					</tbody>
	               </table>
				   
				   	<?php }else {?>      
					
					<table id="report_payment_daterange" class="table table-bordered table-striped text-center ">
	                    <thead>
	                      <tr>
							<th>S.No</th>							
	                        <th>Scheme Code</th>
							<th>M.No</th>													
							<th>Recpt.No</th>
							<th>A/c.Name</th>
							<th>Amount</th>
							<th>Ins</th>
							<th>Pay.Date</th>
						    <th>Emp Code</th>
						    <th>Payment Ref No</th>
						    <th>Transaction Id</th>
						    <th>Card No</th>
							<th>Mode</th>
							<th>M.Rate</th>
							<th>Weight(g)</th>
							<th>B.Name</th>
							<th>Pay.Amt</th>
							<th>Discount</th>
							<th>Incen</th>
							<th>Total</th> 
	                      </tr>
	                    </thead>
						<tfoot>
						<tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>		
						</tfoot> 
					<tbody> 
					</tbody>
	               </table>
				   	<?php }?>
				   
				   
                </div>
				  <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				  </div>
				<!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

<!-- / modal -->  