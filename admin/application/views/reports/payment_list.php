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
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Payment List</h3> <span id="total" class="badge bg-green"></span>     
                         
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
	                 <div class="row">
	                 
	                 	<div class="col-md-12">
	                 		         	 <!-- Date and time range -->
	                 	    <div class="col-md-3" style="">
    		                  <div class="form-group">
    		                    <div class="input-group">
    		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">
    		                        <span  style="display:none;" id="rpt_payment_date1"></span>
									<span  style="display:none;" id="rpt_payment_date2"></span>
    		                        <i class="fa fa-calendar"></i> Date range picker
    		                        <i class="fa fa-caret-down"></i>
    		                      </button>
    		                    </div>
    		                 </div><!-- /.form group -->
    		               </div>
		                 <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-3" style="margin-top:3px;margin-left:-120px;">
									<div class="form-group">
									<select id="branch_select" class="form-control" style="width:200px;" ></select>
									<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
								</div>
							</div>
							<?php }?>
		                 </div>
	                 	
		             
	                 </div> 
                
                      <table id="report_payment_daterange" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th>ID</th>
	                        <th>Date</th>
	                        <th>Customer</th>
	                        <th>Branch Name</th>
	                        <th>A/c Name</th>
	                        <th>Scheme A/c No</th>
	                        <th>Mobile</th>                                          
	                        <th>Type</th>                                           
	                        <th>Mode</th>                                           
	                        <th>Metal Rate (<?php echo $this->session->userdata('currency_symbol')?>)</th>                                           
	                        <th>Metal Weight (g)</th>                                           
	                        <th>Amount (<?php echo $this->session->userdata('currency_symbol')?>)</th>                                           
	                        <th>Ref No</th>                                           
	                        <th>Status</th>                                           	                        
	                      </tr>
	                    </thead> 

	                 </table>
                  
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


<!-- / modal -->  
