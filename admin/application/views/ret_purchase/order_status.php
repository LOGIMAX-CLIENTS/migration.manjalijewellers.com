  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Purchase Order
          </h1>

        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
                 <div class="box-body">  
                   
				   <div class="row">
						<div class="col-xs-12">
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
						</div>
				   </div>
				   
				    <div class="row">
    				  	<div class="col-md-10">  
    						   <div class="row">
    						       <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <br>
                                                <button class="btn btn-default btn_date_range" id="rpt_date_picker"
                                                     <i class="fa fa-calendar"></i> Date range picker
                                                     <i class="fa fa-caret-down"></i>
                                                 </button>
                                                 <span style="display:none;" id="rpt_from_date"></span>
                                                 <span style="display:none;" id="rpt_to_date"></span>
                                            </div>
                                        </div><!-- /.form group -->
                                    </div>
    						        <div class="col-md-2"> 
    									<div class="form-group"> 
    									    </br>
    									    <select id="select_karigar" class="form-control"></select>
    									</div> 
    								</div>
    								
    								<div class="col-md-2"> 
    									<div class="form-group"> 
    									    </br>
    									    <select id="select_category" class="form-control"></select>
    									</div> 
    								</div>
    								
    								<div class="col-md-2"> 
    									<div class="form-group"> 
    									    </br>
    									    <select id="select_product" class="form-control"></select>
    									</div> 
    								</div>
    								
    								<div class="col-md-2"> 
    									<div class="form-group"> 
    									    </br>
    									    <select id="select_design" class="form-control"></select>
    									</div> 
    								</div>
    								
    								<div class="col-md-2"> 
    									<div class="form-group"> 
    									    </br>
    									    <select id="select_sub_design" class="form-control"></select>
    									</div> 
    								</div>
    								
    								<div class="col-md-2"> 
                                    	<div class="form-group"> 
                                    		</br>
                                    		<select id="select_order_for" class="form-control">
                                    			<option value=""></option>
                                    			<option value="1"> Stock Order </option>
                                    			<option value="2"> Customer Order </option>
                                    		</select>
                                    	</div> 
                                    </div>
    
    								
    								<div class="col-md-2"> 
    									<label></label>
    									<div class="form-group">
    										<button type="button" id="ordet_status_search" class="btn btn-info">Search</button>   
    									</div>
    								</div>
    							</div>
    	                 </div> 
                   </div>
			  
                  <div class="table-responsive">
	                 <table id="order_details_list" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">
	                    <thead>
	                      <tr>
	                        <th>PUR ORD NO</th>
	                        <th>Cus Ref No</th>
	                        <th>Karigar</th> 
	                        <th>Order Date</th>
	                        <th>Due Date</th> 
	                        <th>Status</th>                                       
	                        <th>Product</th>                                       
	                        <th>Design</th>                                       
	                        <th>Sub Design</th>                                
	                        <th>Pcs</th>                                       
	                        <th>Weight</th>                                       
	                        <th>Remarks</th>                                       
	                        <th>Weight Range</th>                                       
	                      </tr>
	                    </thead> 
	                 </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      
