<style type="text/css">
.DTTT_container{
margin-bottom:0 !important;
}
</style>
 <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Payment Date-wise
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Payment Date-wise</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Payment Between Dates</h3>      
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
                    
          				
                     <form id="payment_range" >
						  <div class="row">	
							<div class="col-md-7">
							    <div class="form-group"> 
									<label class="col-md-3" for="status">Status</label>
									 <div class="btn-group col-md-9" data-toggle="buttons">
										<label class="btn btn-primary">
										  <input name="pay_status" id="pay_all" type="radio" value="ALL"> All
										</label>
										<label class="btn btn-primary">
										  <input name="pay_status" id="pay_approved" type="radio" value="1"> Approved
										</label>
										<label class="btn btn-primary">
										  <input name="pay_status" id="pay_pending" type="radio" value="0"> Pending
										</label>	
										<label class="btn btn-primary">
										  <input name="pay_status" id="pay_rejected" type="radio" value="2"> Rejected
										</label>
								    	<label class="btn btn-primary">
										  <input name="pay_status" id="pay_failed" type="radio" value="-1"> Failed
										</label>
									  </div>
								</div>	  
							</div>
							<div class="col-md-5">
							    <div class="form-group"> 
									<label class="col-md-3" for="pay_mode">Pay Mode</label>
									 <div class="btn-group col-md-9" data-toggle="buttons">
										<label class="btn btn-primary">
										  <input name="pay_mode" id="mode_all" type="radio" value="ALL"> All
										</label>
										<label class="btn btn-primary">
										  <input name="pay_mode" id="mode_cc" type="radio" value="CC"> CC
										</label>
										<label class="btn btn-primary">
										  <input name="pay_mode" id="mode_dc" type="radio" value="DC"> DC
										</label>	
										<label class="btn btn-primary">
										  <input name="pay_mode" id="mode_nb" type="radio" value="NB"> NB
										</label>
										<label class="btn btn-primary">
										  <input name="pay_mode" id="mode_op" type="radio" value="OP"> OP
										</label>
								    </div>
								</div>	   
							</div>
						  </div>	
						  <br/>
						  <div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label col-sm-4" for="frm_date">From Date</label>
										<div class="col-sm-8"> 
										   <input class="form-control myDatePicker"  data-date-format="dd-mm-yyyy" id="frm_date" name="frm_date" value="" required="true" type="text" />
										</div>   
									</div>
								</div>	
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label col-sm-4" for="frm_date">To Date</label>
										<div class="col-sm-8"> 
											<input class="form-control myDatePicker"  data-date-format="dd-mm-yyyy" id="to_date" name="to_date" value="" required="true" type="text" />
										</div>	
									</div>
								</div>
								<div class="col-md-4">
								   <button class="btn btn-success" id="gen_rep" type="button">Generate Report</button>
								</div>
								
						  </div>		
					 </form>
					 <br/><br/>
					 <div class="row">
						<div class="col-md-12">
						  <div class="table-responsive">
						  <div id="report_wrapper">
						  
						  </div>
						 <!--  <table id="payment_list" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
							    <th>P.ID</th>	
								<th>Paid Date</th>							
								<th>Trans ID</th>	
								<th>PayU ID</th>									
								<th>Client ID</th>
								<th>Name</th>
							    <th>Mobile</th>
								<th>Sch. Code</th>    
								<th>Ms.No</th>
								<th>Pay Mode</th>    
								<th>Card No</th>   
								<th>Metalrate (&#8377;)</th>
								<th>Metalweight (g)</th>
								<th>Amount (&#8377;)</th>
								<th>Charge (&#8377;)</th>
								<th>Total Paid (&#8377;)</th>
								<th>Pay Status</th>
								<th>Remark</th> 
							  </tr>
							</thead>
							<tbody>
							  
							<tbody>
						   </table> -->
						   </div>
						</div>
					 </div>
                 
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
       