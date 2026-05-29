  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Pre Booking Payment 
         <span id="total_bookings" class="badge bg-green"></span> 
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Payment</a></li>
            <li class="active">Pre Booking Payment list</li>
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
					        <span id="bookings_range" style="font-weight:bold;"></span> 
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
	                 <table id="pre_bookingpay_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th>Payment ID </th>
	                        <th>Customer Name</th>
	                        <th>Mobile </th>
	                        <th>Receipt</th>
	                        <th>Booking Number</th>
	                        <th>Payment Date</th>
	                        <th>Payment Amount</th>
	                        <th>Payment Mode</th>
	                        <th>Payment Type</th>
	                        <th>Payment Status</th>
							<th>Payment Through</th>
							<th>Ref number</th>
							<th>Branch </th>
							<th>Employee </th>
							<th>Remarks </th>
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



