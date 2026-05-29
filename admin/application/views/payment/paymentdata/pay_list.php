  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
         Payment Records
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo site_url('payment/pay_list');?>">Payment</a></li>
            <li class="active">Payment Data</li>
          </ol> 
        </section>

        <!-- Main content -->
        <section class="content">
      
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
				
					<div class="box-header with-border">
					  <h3 class="box-title">Online Payment Data</h3>
					  <div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
					  </div>
					</div>
                </div><!-- /.box-header -->
               

			   <div class="box-body">
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
				
					<!--<div class="col-md-5">
							         <?php if($this->session->userdata('branch_settings')==1){?>				
										<div class="form-group" style="    margin-left: 50px;">
										   <label>Select Branch &nbsp;</label>
											<select id="branch_select" class="form-control" style="width:150px;"></select>
											<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
										</div>
							       <?php }?>-->
							    </div>	
						
					<!--	<div class="col-md-6">						
							<div class="col-md-offset-1 col-md-2">
			                <label for="report_date">Report Date</label>
			            </div>
			            <div class="col-md-3">
			                	<input class="form-control datemask"  data-date-format="dd-mm-yyyy" id="offlinepayments_date" name="customer[report_date]" value="<?php echo date('d-m-Y'); ?>" placeholder="Report date" type="text" />
			            </div>
										
						</div>
					</div>	-->				
				
				      
				       </br><div class="row">
              <div class="col-md-12">
                 <div class="pull-left">
                 <div class="form-group" style= "margin-left: 40px;">
                      <label>Enter Transaction ID &nbsp;&nbsp;</label>
                      <input type="text" name="" style="width:160px" id="transid" >
                      <button type="submit" id="trans_submit" name="trans_submit" class="btn btn-primary">Search</button>
												
                      
                    </div>

                 </div>
            </div>    
          </div></br> 
				
				
				<!-- Alert -->
                     
                  <div class="table-responsive">
	                 <table id="payments_data_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th style="width:15px">ID</th>
	                        <th style="width:10px">Transaction ID</th>
	                         <!--<th style="width:15px">Act Amount (<?php echo $this->session->userdata('currency_symbol');?>)</th>-->
	                        <th style="width:15px">Type</th>
	                        <!--<th>Mode</th>-->
	                        <th style="width:15px">Date</th>
	                        <th style="width:15px">Customer</th>
	                        <!--<th>Mobile</th>-->
	                        <th style="width:15px">A/c Name</th>
	                        <th style="width:15px">A/c No</th>
	                        <!--<th>Transaction ID</th>-->
	                         <th style="width:15px">Branch</th>
	                        <th style="width:15px">Trans Data</th>                                       
	                        <!--<th>Metal Weight(g)</th>-->
	                        <!--<th>Amount(<?php echo $this->session->userdata('currency_symbol');?>)</th>-->                                          
	                        <th style="width:15px">Receipt No</th> 
	                      
	                        <th style="width:15px">Status</th>                                          
	                        <th style="width:15px">Remark</th>                                           
	                       <th style="width:15px">Last Update</th> 
	                      </tr>
	                    </thead> 

	                 </table>
                  </div>
				  
			
			
				  
				 <!-- <label>Note:&nbsp;Last 7 days Payment List</label>-->
                   	</div>  <div class="overlay" style="display: none;">
                  <!--<i class="fa fa-refresh fa-spin"></i>-->
                	</div>
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
 