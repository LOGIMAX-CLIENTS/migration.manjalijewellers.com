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
                  
                  <h3 class="box-title">Employee collection summary</h3>      
                 
                </div><!-- /.box-header --><br>
                   <div class="row">
                   
                   <div class="col-sm-2"> 
		                <div class="form-group">											
							<button class="btn btn-default btn_date_range" id="empwisereport_date">
						    <span  style="display:none;" id="rpt_payments1"></span>
							<span  style="display:none;" id="rpt_payments2"></span>
							<i class="fa fa-calendar"></i> Date range picker
							<i class="fa fa-caret-down"></i>
							</button>	
						</div>
				   </div>	
                   
                  <?php if ($this->session->userdata('branch_settings') == 1) { ?>
                    <?php if ($this->session->userdata('login_branch') == 1) { ?>
                      <div class="col-sm-2"> 
                            <select id="branch_select" class="form-control"required="true" multiple></select>
                            <input id="id_branch" name="id_branch" type="hidden" value="" required="true"/> 
                      </div>
                    <?php } else { ?>
                      <div class="col-sm-2"> 
                            <select id="branch_select" class="form-control"required="true"></select>
                            <input id="id_branch" name="id_branch" type="hidden" value="" required="true"/> 
                      </div>
                    <?php } ?>
                  <?php } else { ?>
                    <div class="col-sm-2"> 
                           <select id="branch_select" class="form-control"required="true"></select>
                        <input id="id_branch" name="id_branch" type="hidden" value="" required="true"/> 
                   </div>
                  <?php } ?>
                    

                   <div class="col-md-3" style="">
					    <div class="form-group" >
							<select id="emp_select" class="form-control" style="width:200px;" ></select>
							<input id="id_employee" name="id_employee" type="hidden" value=""/>
						</div>
				   </div>
                   <div class="col-sm-4 pull-right">
                       <button type="button" class="btn btn-primary " id="search_empCol_list" value="">Search</button>
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
                  <table class="table table-bordered table-striped text-center" id="emp_summary_list"  style="overflow:auto;">
                    <thead>
                      <tr id="align">
                     
                        <th>#</th>
                        <th>Employee Name</th>  
                        <th>Employee Name</th>
                        <th>Branch Name</th> 
                        <th>Scheme code</th>          
                        <th>No.of receipts</th>
                        <th>Payment Amount</th>
                      </tr>
                    </thead>
                       
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
      

