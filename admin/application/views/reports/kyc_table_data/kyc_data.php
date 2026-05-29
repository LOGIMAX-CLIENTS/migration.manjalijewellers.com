  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
         Customer Kyc  
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Kyc</a></li>
            <!--<li class="active">reg_list</li>-->
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Customer Kyc Data List</h3> <span class="badge bg-green" id="total_kyc"></span>
                </div><!-- /.box-header -->
                <div class="box-body">    
	            <?php 
				  $attributes = array('id' => 'settled_payments');
				//  echo form_open('payment/update_status',$attributes);
				 ?>
            	 <div class="row">
                	 <div class="col-md-6">
	                	<div class="btn-group" data-toggle="buttons">
					        <label class="btn btn-success" id="in_progress">
					            <input type="radio" name="upd_status_btn" value="1"><i class="icon fa fa-check"></i> In Progress
					        </label>
					        <label class="btn btn-warning" id="verified">
					            <input type="radio" name="upd_status_btn"  value="2"><i class="icon fa fa-check"></i> Verified
					        </label>
					        <label class="btn btn-danger" id="reject">
					            <input type="radio" name="upd_status_btn"  value="3"><i class="icon fa fa-remove"></i> Reject
					        </label>
						</div>
						<div class="form-group col-md-4 pull-right">
						 <label for="" ><a  data-toggle="tooltip" title="Select status"> Filter by Status  </a> <span class="error"></span></label>
						<select id="filtered_status" class="form-control">
							<option value="4">All</option>
							<option value="0">Pending</option>
							<option value="1">In Progress</option>
							<option value="2">Verified</option>
							<option value="3">Rejected</option>
						</select>
						
						
						</div>
                	 </div> 
	                
						<div class="col-md-2 pull-right">
									    <br/>
										<div class="form-group">
										   <button class="btn btn-default btn_date_range" id="kyc-dt-btn"> 
											<span  style="display:none;" id="kyc_list1"></span>
											<span  style="display:none;" id="kyc_list2"></span>
											<i class="fa fa-calendar"></i> Date range picker
											<i class="fa fa-caret-down"></i>
											</button>
										</div>					
									</div> 	
						</div> 	
						<div class="col-md-3 pull-right">
						    <label for="mobile_no">Mobile Number <span class="error"></span></label>
						    <div class="input-group">
							  <input type="text" id="mobile_no" class="form-control" placeholder="Search Mobile No">
							  <span class="input-group-btn">
								<button type="button" class="btn btn-info btn-flat" id="search_kyc_mobile"><i class="fa fa-search"></i></button>
							  </span>
							</div>
						</div>
			
				 </div> 
                <!-- Alert -->
                <ul class="nav nav-tabs">
                    <li id="tab_customer" class="active list_type"><a value="1" href="#kyc_list" data-toggle="tab">Customer KYC</a></li>
                    <li id="tab_nominee" class="list_type"><a value="2" href="#kyc_list" data-toggle="tab">Nominee KYC</a></li>
                </ul>
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
                <div class="table-responsive">
                  <table id="kyc_list" class="table table-bordered table-striped dataTable text-center grid" >
                  <thead>
				<tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Kyc Type</th>
                        <th>Number</th>
                        <th>Name</th>
                        <th>Bank IFSC</th>
					    <th>Bank Branch</th>
                        <th>Status</th>
                         <th>DOB</th>
                        <th>Emp Verified By</th>
                        <th>Verification Type</th>
                        <th>Added By</th>
                        <th>Action</th>
                        <th>Last Update</th>
                        <th>Submited On</th>
                        <th>Images</th>
                     
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
<!-- Kyc detail Modal starts-->
<div class="modal fade" id="kyc_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header bg-yellow">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel" align="center">KYC Detail</h4>

      </div>

      <div class="modal-body">

    	       

           <div class="kyc-det"></div>    

      </div>

      <!--<div class="modal-footer">

      	<div class="col-sm-6 col-sm-offset-3">

          <button type="button" class="btn btn-block btn-warning" data-dismiss="modal">Close</button>

        </div>

      </div>-->

    </div>

  </div>

</div>

<!-- Kyc detail Modal ends-->