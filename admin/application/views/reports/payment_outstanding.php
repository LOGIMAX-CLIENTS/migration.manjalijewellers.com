  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Payment Outstanding Report</h3>      
                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 
                	
                	<div class="col-md-12">
	              <!--  	<div class="form-group col-md-offset-3 col-md-2">-->
			                
			            <!--</div>--><br>
			            
	                </div>
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
	                 	    <div class="col-md-2">
	                 	        <div class="form-group">
			                        <label for="report_date">Report Date</label>
			                	    <input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="payoutcus" name="customer[report_date]" value="<?php echo date('d-m-Y'); ?>" placeholder="Report date" type="text" />
			                    </div>
			                </div>
		                 <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-3" style="margin-top:28px;">
									<div class="form-group">
									<select id="branch_select" class="form-control" style="width:200px;" ></select>
									<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
								</div>
							</div>
							<?php }?>
		                 </div>
	                 	
		             
	                 </div><br>
	            
	            
                  <table id="payout_list" class="table table-bordered table-striped text-center date_pay_report"  >
                    <thead>
                      <tr>
                        <th>S.No</th>      
                        <th>Group</th>      
                        <th>M.No</th>
                        <th>Name</th>
						<th>Tot.Ins</th> 
						<th>Paid.Ins</th> 
                        <th>Payment</th>							
					    <th>J.Date</th>							
					    <th>T.Paidamount</th>
					    <th>T.Paidweight</th>
						<th>Due.Ins</th>                        
						<th>Mobile</th>                        
						<th>Last P.date</th>                    
                      </tr>
                    </thead>
                   <!-- <tfoot id="schemedate" style="font-weight:600;">
					  <tr>
                        <td></td> <td style="text-align:center;"></td> <td></td><td></td><td></td><td></td><td ></td><td ></td>                
                      </tr> 					  
                    </tfoot> -->
                  </table>
                </div><!-- /.box-body -->				
				<div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				  </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
<!-- / modal -->