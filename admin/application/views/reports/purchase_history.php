  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
         Purchase Payment 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Purchase Payment</a></li>
            <!--<li class="active">reg_list</li>-->
          </ol>
        </section>
        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Customer Purchase Payment List</h3> <span class="badge bg-green" id=""></span>
                </div><!-- /.box-header -->
                <div class="box-body">    
	            <?php 
				  $attributes = array('id' => 'settled_payments');
				 ?>
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
	                	<div class="col-md-2">
							<div class="form-group">
							   <button class="btn btn-default btn_date_range" id="payment-dt-btn"> 
								<span  style="display:none;" id="payment_list1"></span>
								<span  style="display:none;" id="payment_list2"></span>
								<i class="fa fa-calendar"></i> Date range picker
								<i class="fa fa-caret-down"></i>
								</button>
							</div>					
						</div>
	               <div class="col-md-2">
                               <div class="form-group">
                              <label>Search by mobile</label>
                              <input type="text" placeholder="Mobile Number" class="form-control" name="" id="mobilenumber" >
                              <input type="hidden" name="id_customer" id="id_customer"/> 
                            </div>
                    </div> 
	            </div>
	           
                
                <div class="table-responsive">
                  <table id="purchase_history" class="table table-bordered table-striped dataTable text-center grid" >
                  <thead>
				<tr>
                        <th>Booking No</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Purchase Type</th>
                        <th>Delivery Preference</th>
                        <th>Amount</th>
                        <th>Weight</th>
                        <th>Transcation ID</th>
                        <th>Status</th>
                        <!--<th>IS Delivered</th>-->
					    <th>Date</th>
					    <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                       </form>
                    </tbody>
               <!--  <tfoot>
                      <tr >
                         <td colspan="10"> <p style="text-align:left"></p></td>
                      </tr>
                    </tfoot> -->
                  </table>
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
     <!-- Need to ask otp when customer purchase the jewel for AT special HH -->

<div class="modal fade" id="otp_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
		<h4 class="modal-title" id="myModalLabel">Verify and Update Status</h4>
	  </div>
      <div class="modal-body"> 
         	<div class="row" > 
         		<div class="col-md-12">
         			<h5>Send OTP to registerd mobile number and verify customer to update status.</h5>
		    		<div class='form-group'>
		                <input type="button" id="send_otp" class="btn btn-warning" value="Send OTP"  /> 
					
						<input type="hidden" id="mobile" value="<?php echo set_value('id_purch_customer[mobile]'); ?>"/>
						<input type="hidden" id="id_purch_payment" value="<?php echo set_value('id_purch_payment[id_purch_payment]'); ?>"/> 
						<input type="hidden" id="id_purch_customer" value="<?php echo set_value('id_purch_customer[id_purch_customer]'); ?>"/>
						
		            </div>
		    	</div>
		    </div>
         	<div class="row otp_block" style="display: none"> 
		    	<div class="col-md-2">
		    		<div class='form-group'>
		                <label for="">OTP</label>
		                
		            </div>
		    	</div> 
		    	<div class="col-md-5">
		    		<div class='form-group'>
		                <input type="text" id="otp" name="id_purch_customer[otp]" placeholder="Please Enter The Your 6 Digit OTP"maxlength="6" class="form-control" required />
		                <input type="hidden" id="otp_sent" name="id_purch_customer[sent_otp]" value=""/>
		            </div>
		    	</div> 
		    	<div class="col-md-2">
		    		<div class='form-group'>
		               <button type="button" id="verify_otp" class="btn btn-success" disabled >Verify OTP</button>
		            </div>
		    	</div>     
			 </div>
			 <div class="row">			    	
		    	<div class="col-md-12">
		    		<div class='form-group'>
		                <label id="otp_status"></label>	               
		        	</div>
		    	</div>			    	
			 </div>	
	         <div class="row close_actionBtns" style="display: none;">
	         	<div class="col-md-2">
		    		<div class='form-group'>
		                <label for="">Remark</label>
		                
		            </div>
		    	</div>
	            <div class="col-md-8"> 
	                <textarea  type="text" id="closed" name="closed"  placeholder="Enter The Remarks" class="form-control" style= "width: 94%" required></textarea> 
	            </div>
	         
	         	<div class="close_actionBtns"  style="display: none;"> 
		<button type="button" id="verify_issue" class="btn btn-success  required"="true">Delivered</button>	 
		</div>
	         </div> 
		 
	</div>  
<div class="modal-footer">
		<button type="button"  class="btn btn-warning" data-dismiss="modal" id="close">Close</button>
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
</style>