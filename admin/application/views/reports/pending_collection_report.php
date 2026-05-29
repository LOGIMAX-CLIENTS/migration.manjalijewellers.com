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
            Pending Collection Remarks
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Pending Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                      <div class="box-header">
                 
                   
               <!--      <div class="col-xs-2">
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
                   </div> -->
                   
                
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
                  <table class="table table-bordered table-striped text-center" id="pending_remarks_list">
                    <thead>
                      <tr>
                     
                        <th>S.No</th>
                        <th>Date</th>
                        <th>Agent Name</th>                     
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Scheme Acc Id</th>
                        <th>Scheme Acc Number</th>
                        <th>Remarks</th>
                      </tr>
                    </thead>
                       <tfoot>
							<tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
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
      

