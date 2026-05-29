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
            <li class="active">All Scheme Payment Collection</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">All Scheme Payment Collection</h3>      
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
				
				 <div class="row">
	                 
	                 <div class="col-md-12">
	                 	<div class="col-md-offset-2 col-md-2">
			                <label for="report_date">Report Date</label>
			            </div>
			            <div class="col-md-2" style="margin-left: -100px;">
			                	<input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="schwisereport_date" name="customer[report_date]" value="<?php echo date('d-m-Y'); ?>" placeholder="Report date" type="text" />
			            </div>
						
						  
						   
						  <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-5">
									<div class="form-group" >
									<label>Select Branch </label>
									<select id="branch_select" class="form-control" style="width:200px;" ></select>
									<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
								</div>
							</div>
							<?php }?>
		                 </div>
	                 </div>









				
                   <div class="table-responsive">
                  <table id="payschcoll_data" class="table table-bordered table-striped text-center date_pay_report"  >
                    <thead>
                      <tr>
                        <th>Scheme Name</th>
                        <th>Branch Name</th>
                        <th>Opening</th>                        
                        <th>Collection</th>
						<th>Incentive</th>   
                        <th>Paid</th>                        
                        <th>Return</th>                        
                        <th>G.WayCharge</th>        
                        <th>Closing</th>
                        <th>Total</th>    
                      </tr>
                    </thead>
					 <tfoot>
					<th>Total</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
					 </tfoot>
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

<!-- / modal -->      

