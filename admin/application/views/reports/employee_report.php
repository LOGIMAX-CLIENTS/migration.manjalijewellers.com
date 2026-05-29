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
                      <div class="box-header">
                   <div class="col-xs-4">
                  <h3 class="box-title">Employee-wise Payment Report</h3>      
                   </div>
                   
                    <div class="col-xs-2">
                       <div class="form-group">
                           <select id="emp_select" class="form-control"required="true"></select>
                        <input id="id_employee" name="id_employee" type="hidden" value="" required="true"/>
                        </div>
                   </div>
                    <div class="col-xs-2">
                       <div class="form-group">
                           <select id="branch_select" class="form-control"required="true"></select>
                        <input id="id_branch" name="id_branch" type="hidden" value="" required="true"/>
                        </div>
                   </div>
                   
                      <?php if($this->payment_model->entry_date_settings()==1){?>	
								<div class="col-xs-3">
										<div class="form-group" style="">
										   <label>Select Date</label>
											<select id="date_Select" class="form-control" style="width:150px;">
											    <option value=1 selected>Payment Date</option>
											     <option value=2>Entry Date</option>
											</select>
											<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
										</div>
							    </div>
							    <?php }?>
                   
                   		
               
                
                </div><!-- /.box-header -->
                <div class="col-xs-2">
										<div class="pull-left">
											<div class="form-group">											
									 <button class="btn btn-default btn_date_range pull-right" id="empwisereport_date">
									    <span  style="display:none;" id="rpt_payments1"></span>
										<span  style="display:none;" id="rpt_payments2"></span>
										<i class="fa fa-calendar"></i> Date range picker
										<i class="fa fa-caret-down"></i>
										</button>	
											</div>
										 </div>						
									</div>
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
                <div class="table-responsive">
                  <table class="table table-bordered table-striped text-center" id="emp_list" >
                    <thead>
                      <tr>
                     
                        <th>Id Employee</th>
                        <th>Date</th>
                        <th>Employee Name</th>                     
                        <th>Customer Name</th>
                        <th>Mobile Number</th>
                       
                        <th>Payment Amount</th>
                      </tr>
                    </thead>
                       <tfoot>
							<tr><th></th><th></th><th></th><th></th><th></th><th></th></tr>
						</tfoot> 
                    
                  </table>
                 </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


<!-- modal -->      
      

