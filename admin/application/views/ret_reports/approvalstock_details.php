 
  
  <!-- Content Wrapper. Contains page content -->
    
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Approval Stock Report
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Approval Stock report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
                 <div class="box-body">  
				   	<div class="box box-info stock_details">
						<div class="box-header with-border">
						  <div class="row">
								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
								<div class="col-md-2"> 
									<div class="form-group tagged">
										<label>Select Branch</label>
										<select id="branch_select" class="form-control branch_filter"></select>
									</div> 
								</div> 
								<?php }else{?>
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
									<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
								<?php }?> 
								
								<div class="col-md-2"> 
									<div class="form-group">    
										<label>Date</label> 
										<?php   
											$fromdt = date("d/m/Y");
											$todt = date("d/m/Y");
									    ?>
			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
									</div> 
								</div>
								
								<div class="col-md-2"> 
									<label>Select Metal</label>
									<select id="metal" class="form-control" style="width:100%;"></select>
								</div>
								<div class="col-md-2"> 
									<label>Select Category</label>
									<select id="category" class="form-control" style="width:100%;"></select>
								</div>
								
								<div class="col-md-2"> 
									<label>Select Product</label>
									<select id="prod_select" class="form-control" style="width:100%;"></select>
								</div>
								<div class="col-md-2"> 
									<label>Group By</label>
									<select id="select_group_by" class="form-control" style="width:100%;">
									    <option value="1">Product</option>
									    <option value="2">Category</option>
									</select>
								</div>
								
							</div>
							<div class="row">
							    <div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="approval_stock_detail_search" class="btn btn-info">Search</button>   
									</div>
								</div>
							</div>
							
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="stock_list" class="table table-bordered table-striped text-center">
										 <thead>
		                      <!--<tr>
							  	<th colspan="1"></th>
							  	<th colspan="3">Opening</th>
								<th colspan="3">Inward</th>
								<th colspan="3">Outward</th>
								<th colspan="3">Closing</th>
		                      </tr>-->
							  <tr>
							    <th width="10%">Category/Product</th>
	                            <th width="10%">Op Stock</th>   
								<th width="10%">Op Gross Wt</th>   
	                            <th width="10%">Op Net Wt</th>
	                            <th width="10%">I/W Pcs</th>  
		                        <th width="10%">I/W Gross Wt</th>  
	                            <th width="10%">I/W Net Wt</th> 
	                            <th width="10%">O/W Pcs</th>  
		                        <th width="10%">O/W Gross Wt</th>  
	                            <th width="10%">O/W Net Wt</th> 
	                            <th width="10%">Closing Stock</th>  
		                        <th width="10%">Closing Gross Wt</th>  
	                            <th width="10%">Closing Net Wt</th> 
	                            <th width="10%">Intransit</th> 
	                            <th width="10%">Closing Stock</th> 
	                            
							  </tr>
		                    </thead> 
		                    <tbody></tbody>
									 </table>
								  </div>
								</div> 
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
      

