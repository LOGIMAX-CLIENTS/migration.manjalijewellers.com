  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

         	Video Shopping Appointment

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">All appointment requests</a></li>

          </ol>

        </section>

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">All appointment requests</h3> <span class="badge bg-green" id="total_appt_req"></span>

                </div><!-- /.box-header -->

                <div class="box-body">    

                <div class="row">

		          <div class="col-md-12">

		          	<span id="alert_message"></span>

		          </div>

		        </div> 

	                  

				<div class="row"> 

					<div class="col-md-2 pull-left"> 

						<label for="" >Slot Date</label>

						<div class="form-group">

						   <button class="btn btn-default btn_date_range" id="appt_req-dt-btn"> 

							<span  style="display:none;" id="appt_req_list1"></span>

							<span  style="display:none;" id="appt_req_list2"></span>

							<i class="fa fa-calendar"></i> Date range picker

							<i class="fa fa-caret-down"></i>

							</button>

						</div>					

					</div>  

	            	<div class="col-md-2 pull-left">

		                <div class="form-group">

							 <label for="" >Filter by Status</label>

							<select id="filtered_status" class="form-control">

								<option value="">All</option>

								<option value="0">Open</option>

								<option value="1">Allotted</option>

								<option value="2">Rejected</option>

								<option value="3">Completed</option>

								<option value="4">Closed</option>

							</select>

						</div>

					</div>

					<div class="col-md-8 pull-right">

						<label></label>

		                <div class="form-group">

						     <?php if($access['edit']==1){ ?>

							  <button type="button"  class="btn btn-success" id="completed">Completed</button>

							  <?php }?>
						</div>

					</div>

                </div> 

                <p></p>

                <div class="row"> 

					<div class="col-md-12"> 

		                <div class="table-responsive">

		                  <table id="appt_request_list" class="table table-bordered table-striped dataTable text-center grid" >

		                  <thead>

							  <tr>

		                        <th>#</th>

		                        <th>Name</th>

		                        <th>Mobile</th>

		                        <th>Booking Date & Time</th>

		                        <th>Pref. Category</th>

		                        <th>Pref. Item</th>

		                        <th>Email</th>

		                        <th>Location</th>

		                        <th>Requested On</th>

								<th>Pref. Language</th>

								<th>Status</th>
								<th>Remarks</th>
								<th>Action</th>
								

								

		                  <!--<th>Whatsapp</th>   

		                        <th>Slot Alloted Emp</th> 

								<th>Pref. Slot</th> 

							<th>Description</th> 

								<th>Customer Feedback</th> 

								<th>Reject Reason</th>  -->

		                        

		                      </tr>

		                    </thead>

		                    <tbody> 

		                    </tbody>

		                   </table>

		                  </div>

	                  </div>

                  </div>

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      

      

      <!-- modal -->

     <!-- Available Emp Details -->



<div class="modal fade" id="appt_req_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header bg-green">

       <h4 class="modal-title" id="myModalLabel" align="center">Allocate Employee</h4>

      </div>

      <div class="modal-body">  

         	<div class="row">

	          <div class="col-md-12">

	          	<span id="chit_alert"></span>

	          </div>

	        </div>

	        <h5 class="lead">Available Employees :</h5> 

	        <div class="row">

	          <div class="col-md-offset-1 col-md-10">

	          	<div id="allot_emp_data" class="available_emp"></div>

	          </div>

	        </div> 

			<input type="hidden" id="id_appt_request_ae" value="<?php echo set_value('id_appt_request[id_appt_request]'); ?>"/> 

             <div class="row" align="right">			    	

		    	<div class="col-md-12">

		    		<div class='form-group'>

		                <h5 class="lead text-green">Total selected : <span class="tot_emp_sel">0</span></h5>

		        	</div>

		    	</div>			    	

			 </div>

				

			 <div class="modal-footer">

			      <div class="row">

				  <input type="hidden" id="id_employee" value="<?php echo set_value('id_employee[id_employee]'); ?>"/>     

      	<button type="button" id="req_allocate" class="btn btn-success"  disabled="true">Allocate</button>	

		<button type="button"  class="btn btn-warning" data-dismiss="modal" id="cancel">Cancel</button>

         </div>

			  </div>    

	   </div>

		 

	</div>  



   </div>

  </div>



<!-- modal -->    

<!-- After Completed  Get Appt Feedback Status From Customers hh -->



<div class="modal fade" id="update_feedback_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header bg-yellow">

       <h4 class="modal-title" id="myModalLabel" align="center">Customer Feedback</h4>

      </div>

      <div class="modal-body">

      	<div class="row">

          <div class="col-md-12">

          	<span id="chit_alert"></span>

          </div>

        </div>

		<div class="row">

			<div class="form-group">  

				<input type="hidden" id="id_appt_request_cf" value="<?php echo set_value('id_appt_request[id_appt_request]'); ?>"/> 

				<label  class="col-md-3">Customer Feedback</label>

				<div class="col-md-9">

					<textarea id="feedback_status" name="feedback_status"  placeholder="Enter Feedback Status" required="true"  rows="5" cols="50" required > </textarea>

					<p class="help-block"></p>

				</div> 

			</div>

		</div> 

      </div>

      <div class="modal-footer">

      	<button type="button" id="add_Feedback_status" class="btn btn-success" data-dismiss="modal" disabled >Submit</button>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal --> 

<!-- Appt Request Reject Status for Customers hh -->



<div class="modal fade" id="update_reject_reason" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header bg-red">

       <h4 class="modal-title" id="myModalLabel" align="center">Update Reject Reason</h4>

      </div>

      <div class="modal-body">

          <div class="row">

	          <div class="col-md-12">

	          	<span id="chit_alert"></span>

	          </div>

          </div>

          <div class="row">

			<div class="form-group">  

				<input type="hidden" id="id_appt_request_rj" value=""/> 

				<label  class="col-md-3">Reject Reason</label>

				<div class="col-md-9">

					<textarea id="reject_reason" name="reject_reason"  placeholder="Enter Reject Reason" required="true"  rows="5" cols="60" required > </textarea>

					<p class="help-block"></p>

				</div>

			</div>

		  </div> 

      </div>

      <div class="modal-footer">

      	<button type="button" id="add_reject_reason" class="btn btn-danger" data-dismiss="modal" disabled >Reject</button>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->

<!-- modal -->      
<div class="modal fade" id="update_appreq_status" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-yellow">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel" align="center">Update Enquiry Status</h4>
      </div>
      <div class="modal-body">
          <div class="row">
		 	<div class="form-group">
               <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Status</label>
               <div class="col-md-4">
                 <input type="hidden" class="form-control" id="id_app_req" name="id_app_req"/>
				 <select id="filtered_req_status" class="form-control">
								<option value="">All</option>
								<option value="0">Open</option>
								<option value="1">Allotted</option>
								<option value="2">Rejected</option>
								<option value="3">Completed</option>
								<option value="4">Closed</option>
							</select>
                 <p class="help-block"></p>
               </div>
            </div>
		  </div> 
		  <div class="row">
		 	<div class="form-group">  
                 <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Remarks</label>
                   <div class="col-md-9">
                   	 <textarea id="app_reqremarks" name="app_reqremarks"  placeholder="Enter Remarks" required="true"  rows="5" cols="50"> </textarea>
                     <p class="help-block"></p>
                   </div>
                 <p class="help-block"></p> 
            </div>
		  </div> 
		  <!-- <div class="row">
		 	<div class="form-group">  
                 <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Internal Status</label>
                   <div class="col-md-9">
                   	 <textarea id="internal_stat" name="internal_stat"  placeholder="Internal Status" required="true"  rows="5" cols="50"> </textarea>
                     <p class="help-block"></p>
                   </div>
                 <p class="help-block"></p> 
            </div>
		  </div>  -->
      </div>
      <div class="modal-footer">
      	<a href="#" id="add_app_req_status" class="btn btn-success" data-dismiss="modal" >Add</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->

<style type="text/css">

.popover1{

    width:230px;

    height:330px;    

}

.trans tr{

	 width:50%;

    height:50%;

	font-size:15px;

	

}

.row{

    margin-right: -9px

}



</style>