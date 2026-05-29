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
            <li class="active">Mode-wise Payment Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Collection Summary</h3>      
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
	                     	<div class="col-md-3">
										<div class="form-group" style="    margin-left: 50px;">
										   <label>Select Date</label>
											<select id="date_Select" class="form-control" style="width:150px;">
											    <option value=1 selected>Payment Date</option>
											     <option value=2>Entry Date</option>
											</select>
											<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
										</div>
							    </div>
	                 	<div class="col-md-3"  style="margin-top:20px">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                       <button class="btn btn-default btn_date_range" id="paymentmodewise_date">
							    <span style="display:none;" id="rpt_payments1"></span>
							    <span style="display:none;" id="rpt_payments2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		               </div>
						
						   <div class="col-md-3" style="margin-top:20px">
								<div class="form-group" >
								
									<select id="scheme_select" class="form-control" style="width:200px; margin-left: 15px !important;"></select>
									<input id="id_schemes"  name="id_scheme" type="hidden" value="" />
								</div>
						   </div>
						   <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-3" style="margin-top:20px">
									<div class="form-group" >
								
									<select id="branch_select" class="form-control" style="width:200px;" ></select>
									<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
								</div>
							</div>
							<?php }?>
		                 </div>
	                 </div>


				<?php if($this->payment_model->get_gstsettings()==1){?>
                   <div class="table-responsive">
                   <table id="paymentmodewise_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>S.NO</th>
                        <th>Pay.Mode</th>
                        <th>Collection</th> 
                        <th>SGST</th>							
					    <th>CGST</th>							
					    <th>T.GST</th>
						<th>Total</th> 
                      </tr>
                    </thead>
					<tfoot>
							<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
						</tfoot>
                  </table>
				<?php }else {?>

					<div class="table-responsive">
                   <table id="paymentmodewise_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>S.NO</th>
                        <th>Pay.Mode</th>
                        <th>Collection</th>
						<th>Total</th> 
                      </tr>
                    </thead>
					<tfoot>
							<tr><td></td><td></td><td></td><td></td></tr>
						</tfoot>
                  </table>
				

				
				<?php }?>  
				  
                  
                </div>
                </div>
				 <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				  </div><!-- /.box-body -->
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


 

