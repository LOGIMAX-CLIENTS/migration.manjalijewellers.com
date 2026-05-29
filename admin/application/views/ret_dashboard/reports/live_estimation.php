  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Estimation
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Estimation List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Live Estimation List</h3>    
				  <span id="total_plans" class="badge bg-aqua"></span>  
                         <!--  <a class="btn btn-success pull-right" id="add_plan" href="<?php echo base_url('index.php/wallet/master/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> -->
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
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					 
					</div>
				  </div>

				 <div class="row">
	                 
	                 <div class="col-md-12">
	             
	                 	<div class="col-md-2" style="margin-top: 20px;">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                       <button class="btn btn-default btn_date_range" id="estimation_date">
							  <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
							    <span  style="display:none;" id="estimation1"></span>
							    <span  style="display:none;" id="estimation2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		                </div>
		     
						  <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-2">
								<div class="form-group" >
									<label>Filter by Branch </label>
									<select id="branch_select" class="form-control branch_filter"></select>
									<input type ="hidden" id="id_branch">
								</div>
							</div>
							<?php }?>
							<div class="col-md-2">
								<div class="form-group" >
									<label>Filter by Type</label>
									<select id="filter_type" class="form-control" >
									<option value= "0">All</option>
									<option value= "1">Purchase</option>
									<option value= "2">Sales</option>
									<option value= "3">Sales & Purchase</option>
									</select>
									<input type="hidden" id="id_filter" value=0 name="">
								</div>
							</div>
					</div>
					
	         </div>
				
					
					
                  <div class="table-responsive">
                  <table id="estimation_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th width="5%">S.No.</th>
                        <th  width="20%">Customer</th>                       
                        <th  width="10%">Type</th>
                        <th  width="10%">Sale Weight</th>                 
                        <th  width="10%">Sale Amount</th>
                        <th  width="10%">Purchase Weight</th>      
						<th  width="10%">Purchase Amount</th>      
						<th  width="10%">Chit UTI</th>      
						<th  width="10%">Gift Voucher UTI</th>      
						<th  width="10%">Discount</th>
						<th  width="10%">Net Amount</th>
                      </tr>
                    </thead>
                    <tfoot>
			            <tr>
			            	<th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
			            </tr>
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
      


    
