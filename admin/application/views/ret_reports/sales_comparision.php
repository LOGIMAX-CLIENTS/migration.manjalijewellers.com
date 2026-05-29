  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Sales Comparision</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Retail Reports</a></li>
            <li class="active">Sales Comparision</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Sales Comparision Details</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                     <div class="row">
    						<div class="col-xs-12" id="success_res" style="display:none;">
    						<!-- Alert -->
    							   <div class="alert alert-success alert-dismissable">
    								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    								<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?> <span id="success_msg"></span>!</h4>
    							  </div>
    						</div>
    						
    						<div class="col-xs-12" id="failed_res" style="display:none;">
    						<!-- Alert -->
    							   <div class="alert alert-danger alert-dismissable">
    								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    								<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?><span id="failed_msg"></span>!</h4>
    							  </div>
    						</div>
    						
    				   </div>
                  <div class="row">
				  	<div class="col-md-offset-2 col-md-10">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
								<div class="col-md-2"> 
									<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
								
									<div class="form-group tagged">
										<label>Select Branch</label>
										<select id="branch_select" class="form-control branch_filter"></select>
									</div> 
									<?php }else{?>
										<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
									<?php }?> 
								</div>  
								<div class="col-md-3"> 
									<div class="form-group">    
										<label>Select Product</label> 
										<select id="prod_select" class="form-control" style="width:100%;"></select>
										<input id="smith_due_date"  value="<?php echo $settings['smith_due_date'];?>" type="hidden" />
                                        <input id="smith_remainder_date"  value="<?php echo $settings['smith_remainder_date'];?>" type="hidden" />
									</div> 
								</div>
								
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="sales_comparision_search" class="btn btn-info">Search</button>   
									</div>
								</div>
							</div>
						 </div>
	                   </div> 
	                  </div> 
                   </div> 
                
			  
                   <div class="row">
	                    <div class="col-md-6">
	                        <div class="box box-primary">
                                <div class="box-header with-border">
                                  <i class="fa fa-bar-chart-o"></i>
                    
                                  <h3 class="box-title">Metal Details</h3>
                    
                                  <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                  </div>
                                </div>
                                <div class="box-body">
                                   <div class="chart" id="metal_chart" style="height: 250px;"></div>
                                </div>
                                <!-- /.box-body-->
                              </div>
                       </div> 
                       <div class="col-md-6">
	                        <div class="box box-primary">
                                <div class="box-header with-border">
                                  <i class="fa fa-bar-chart-o"></i>
                    
                                  <h3 class="box-title">Sales Graph</h3>
                    
                                  <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                  </div>
                                </div>
                                <div class="box-body">
                                   <div class="chart" id="sales_chart" style="height: 250px;"></div>
                                </div>
                                <!-- /.box-body-->
                              </div>
                       </div> 
                   </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

