  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Gift Issued Report
			<span id="total_gift" class="badge bg-green"></span> 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Gift Issued Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <!-- <div class="box-header with-border">
                  <h3 class="box-title">Gift Report</h3> <span id="total" class="badge bg-green"></span>     
                         
                </div>/.box-header -->
                <div class="overlay" style="display:block">
				  <i class="fa fa-refresh fa-spin"></i>
				  </div>
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
    	                   	<div class="col-md-22">
    	                       
										<div class="col-md-2">
											<div class="form-group">
                                                <b><span id="from_date_selected"></span>
												<span id="to_date_selected"></span></b>
												<button class="btn btn-default btn_date_range"  id="rpt_payment_date">
												<!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
													<span  style="display:none;" id="rpt_payments1"></span>
													<span  style="display:none;" id="rpt_payments2"></span>
													<i class="fa fa-calendar"></i> Date range picker
													<i class="fa fa-caret-down"></i>
												</button>
												
											</div><!-- /.form group -->
										</div>
										<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?> 			
											<div class="col-md-1">
													<div class="form-group" >
													<label>Branch</label>
													<select id="branch_select" class="form-control"  style="width:100px; margin-left: 15px !important;" ></select>
													<input id="id_branch" name="scheme[id_branch]"  type="hidden" value=""/>
												</div>
											</div>
										<?php }else{?>
											<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
										<?php }?>

										
										<div class="col-md-2">
												<div class="form-group" >
													<label>Scheme name</label>									
													<select id="scheme_select" class="form-control" style="width:100px; margin-left: 15px !important;"></select>
													<input id="id_schemes"  name="id_scheme" type="hidden" value="" />
												</div>
										</div>
										
											<div class="col-md-1">
												<div class="form-group" >
													<label>Gift Name</label>									
													<select id="gift_list" class="form-control" style="width:100px; margin-left: 15px !important;"></select>
													<input id="id_gift_select"  name="id_gift_select" type="hidden" value="" />
												</div>
										</div>
											<!--	<div class="col-md-2">
														<div class="form-group" >
															<label>Metal Name</label>									
															<select id="metal_select" class="form-control" style="width:100px; margin-left: 15px !important;"></select>
															<input id="id_metal_select"  name="id_metal_select" type="hidden" value="" />
														</div>
												</div> -->
												<div class="col-md-2">
													
													<div class="form-group">
														<label>Filter By Employee</label>
														<select id="employee_select" class="form-control" style="width:100px; margin-left: 15px !important;"></select>
														<input id="id_employee" name="id_employee" type="hidden" value=""/>
													</div>
												</div>
												
											<div class="col-md-1"> 
												<label>Group By</label>
												<div class="form-group">
												    <select id="issue_group_by" class="form-control" style="width:100px;">
												        <option value="1">By Scheme</option>
												        <option value="2">By Gift</option>
												    </select>
												
												</div>
										    </div>
										    
										    <div class="col-md-2"> 
												<label>Filter By Status</label>
												<div class="form-group">
												    <select id="gift_status" class="form-control" style="width:100px;">
												        <option value="1">Issued</option>
												        <option value="2">Cancelled</option>
												    </select>
												
												</div>
										    </div>
										    
										   
										
										<div class="col-md-1 pull-right"> 
												<label></label>
												<div class="form-group">
													<button type="button" id="search_gift_list" class="btn btn-info">Search</button>   
												</div>
										</div>
							</div>
						</div>
					 

					<!-- <div class="box box-info stock_details collapsed-box">
						
						<div class="box-body collapse" style="display: none;">
							
								<div class="col-md-22">
							
										<div class="col-md-8">
												<table id="gift_summary" class="table table-bordered table-striped text-center">
													<thead>
														<th>Scheme Name</th>
														<th>Issued Count</th>
														<th>Weight (in gms)</th>
													</thead>
													<tbody></tbody>
									
												</table>
								
										</div>
								</div>
							  
							
						</div>

					</div>-->
					
					
							<div class="row">
									<div class="box-body">
										<div class="table-responsive">
												<table id="gift_report_list" class="table table-bordered table-striped text-center">
													
													<thead>
														
														<tr  style="text-transform:uppercase;">
															<th>Sno</th>
															<th>Customer Name</th>
															<th>Mobile</th>
															<th>Account No</th>
															<th>Joined Date</th>
														    <th>Joined Branch</th>
															<th>Paid Installment</th>
															<th style="width:100%">Gift Issued On</th>
															<th>Gift Issued EMP</th>
															<th>Referred EMP</th>
															<th>Payment Amount</th>
															<th>Gift</th>
															<th>Quantity</th>
														<!--	<th>Weight (in gms)</th>-->
															<th>Gift Status</th>
															<th>Cancelled Date</th>
															<th>Cancelled EMP</th>
															<th>Cancelled Remarks</th>
														</tr>
													</thead> 
													<tbody> 
												
													</tbody>
												</table>
										</div><!--tabe-responsive-->
										
									</div><!--box-body-->
							</div><!--row-->
				<!-- /.box-body -->
              </div><!-- /.box -->
              	
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

<!-- / modal -->  