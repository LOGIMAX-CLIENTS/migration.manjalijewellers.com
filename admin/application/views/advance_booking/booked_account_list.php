  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Reserve Booking
         <span id="booked_accounts_count" class="badge bg-green"></span> 
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Advance Booking</a></li>
            <li class="active">Reserve Booking</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">

               <div class="box box-primary">
			    
                 <div class="box-body">
   				   <div class="row">
   				        
					    <div class="col-sm-2">
					        <span id="bookings_acc_range" style="font-weight:bold;"></span> 
    						<div class="form-group">
    						   <button class="btn btn-default btn_date_range" id="prebook_pay-dt-btn">
    						       <span  style="display:none;" id="account_list1"></span>
                                    <span  style="display:none;" id="account_list2"></span>
    							<i class="fa fa-calendar"></i> Date range picker
    							<i class="fa fa-caret-down"></i>
    							</button>
							</div>
						</div>
						<div class="col-md-2">               					
                                  <div class="form-group">
                                      <label>Select Branch</label><br>
                                      <select id="branch_select" class="form-control" style="width:150px;" ></select>  
                                  </div>
                             
                          </div>
						<div class="col-md-2">
                        <div class="form-group" >
                               <div class="form-group"style="margin_left:-120px;">
                              <label>Search by mobile</label>
                              <input type="text" placeholder="Mobile Number" class="form-control ui-autocomplete-input" name="" id="mobile_number" autocomplete="off">
                              <input type="hidden" name="id_customer" id="id_customer"> 
                            </div>
                       </div>  
                      </div>
						<div class="col-md-6">
                            <div class="form-group" >
                                   <div class="form-group">
                                  
       
                                    <a class="btn btn-success pull-right" id="add_bookings" href="<?php echo base_url('index.php/admin_adv_booking/lock_gold_view');?>"><i class="fa fa-user-plus"></i> Add</a> 
                                </div>
                           </div>  
                        </div>
   				        
   				   </div>
					
			 <br/>
			  


                  <div class="table-responsive">
	                 <table id="pre_booking_acc_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th>Booking ID</th>
	                        <th>Customer Name</th>
	                        <th>Mobile </th>
	                        <th>Booking Name</th>
	                        <th>Booking Number</th>
	                        <th>Booked Amount</th>
	                        <th>Booked Weight</th>
	                        <th>Booked Rate</th>
	                        <th>Booking Date</th>
	                        <th>Total Amount Paid</th>
	                        <th>Balance Amount</th>
	                        <th>Branch</th>
	                        <th>Employee</th>
	                        <th>Account Status</th>
	                        <th>Booked Through</th>
	                        <th>Remarks</th>
	                        <th>Action</th>
	                      </tr>
	                    </thead>

	                 </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>

            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

<!-- modal -->      

<div class="modal fade" id="close_booking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog">
    
        <div class="modal-content">
        
            <div class="modal-header bg-yellow">
            
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                
                <h4 class="modal-title" id="myModalLabel" align="center">Booking Close</h4>
            
            </div>
        
            <div class="modal-body">
                <strong>Are you sure! You want to close this booking?</strong>
                <input type="hidden" id="id_booking_close"></input>
                <input type="hidden" id="employee_closed" value="<?php echo $this->session->userdata('uid'); ?>"></input>
                <input type="hidden" id="closing_id_branch" value="<?php echo $this->session->userdata('login_branch');  ?>"></input>
            </div>
        
            <div class="modal-footer">
                <a href="#" class="btn btn-danger btn-confirm" id="mark_close" >Close</a>
                <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
        
        
        </div>
    
    </div>

</div>

<!-- / modal --> 

