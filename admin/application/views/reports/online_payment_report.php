<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Online Payment Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Online Payment Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Online Payment Report</h3>    <span id="Online_Payment_Report" class="badge bg-green"></span>
                </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?> 
				<div class="row">
                    <div class="col-md-2">
                        <div class="pull-left">
                          <div class="form-group">
                             <button class="btn btn-default" id="online_payment_report_date" style="margin-top: 20px;">
                            <i class="fa fa-calendar"></i> Date range picker
                            <i class="fa fa-caret-down"></i>
                            </button>
                            <span style="display:none;" id="from_date"></span>
                            <span style="display:none;" id="to_date"></span>
                          </div>
                         </div>
                     </div>
                     <?php if($this->session->userdata('branch_settings')==1){ ?>
                     <div class="col-md-2">
                         <label>Select Branch</label>
                         <select id="branch_select" class="form-control"></select>
                     </div>
                     <?php } ?>
                     <div class="col-md-2">
                         <label>Select Payment Status</label>
                         <select id="pay_status" class="form-control"></select>
                     </div>
                     
                     <div class="col-md-2">
                        <label></label>
                        <div class="form-group">
                            <button type="button" id="online_report_search" class="btn btn-info">Search</button>
                        </div>
                    </div>
                                            
                </div>	
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					 
					</div>
				  </div>
						
                <div class="table-responsive">
                  <table id="online_payment_report_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <?php if($this->payment_model->entry_date_settings()==1){?>	
                        <th>Entry Date</th>
                        <?php }else{?>
                        <th>Payment Date</th>
                        <?php }?>
                        <th>Customer</th>
                        <th>A/c Name</th>
                        <th>Scheme code</th>
                        <th>A/c No</th>
                        <th>Mobile</th> 
                        <th>Received (<?php echo $this->session->userdata('currency_symbol');?>)</th>   
                        <th>Bounus</th> 
                        <th>Metal Weight(g)</th> 
                        <th>Metal Rate (<?php echo $this->session->userdata('currency_symbol');?>)</th> 
                         <th>Status</th> 
                        <th>Total Paid Instal.</th>
                        <th>Type</th>                                           
                        <th>Mode</th>                                           
                        <th>Ref No</th>                                           
                        <th>Paid Through</th>
                      </tr>
                 	</thead>
                        <tbody></tbody>
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