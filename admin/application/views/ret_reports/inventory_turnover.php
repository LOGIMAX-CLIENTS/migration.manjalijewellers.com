 <style type="text/css">

	#inventory_turnover tr th:not(:first-child), #inventory_turnover tr td:not(:first-child) {

		text-align: right;

	}

	.total_row td {

		font-size: 20px;

		font-weight: bold;

	}
 </style> 
 
 <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Inventory Turnover</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Inventory Turnover</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Inventory Turnover Report</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                  <div class="row">
				  <div class="col-md-12">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						<div class="row"> 
							<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
        		                  <div class="col-md-2"> 
        		                     <div class="form-group tagged">
        		                       <label>Select Branch</label>
        									<select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
        		                     </div> 
        		                  </div> 
        						    <?php }else{?>
        		                    	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		                    	<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
        		                  <?php }?>  
							<div class="col-md-2">
								<div class="form-group">
									<label>Select Metal</label> 
									<select class="form-control" id="metal" multiple></select> 
								</div> 
							</div> 
							<div class="col-md-2">
								<div class="form-group">
									<label>Category</label> 
									<select id="category" class="form-control" multiple></select>
								</div>  
							</div> 
							
							<div class="col-md-2">
								<div class="form-group">
									<label>Product</label> 
									<select id="prod_select" class="form-control" multiple></select>
								</div>  
							</div> 
							
							<div class="col-md-2">
								<div class="form-group">
									<label>Design</label> 
									<select id="des_select" class="form-control" multiple></select>
								</div>  
							</div> 
							
							<div class="col-md-2">
								<div class="form-group">
									<label>Sub Design</label> 
									<select id="sub_des_select" class="form-control" multiple></select>
								</div>  
							</div> 

							<div class="col-md-2">
								<div class="form-group">
									<label>Karigar</label> 
									<select id="karigar" class="form-control" multiple></select>
								</div>  
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label>Section</label> 
									<select id="section_select" class="form-control" multiple></select>
								</div>  
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label>Group By</label> 
									<select id="group_by" class="form-control" multiple>
										<option value="1">Product</option>
										<option value="2">Design</option>
										<option value="3">SubDesign</option>
										<option value="4">Section</option>
										<option value="5">Karigar</option>
										<option value="6">Weight Range</option>
										<option value="7">Size</option>
									</select>
								</div>  
							</div> 
							<div class="col-md-1" style="display: none;">
								<div class="form-group">
									<label>Date Type</label> 
									<select id="date_type" class="form-control">
										<option value="1">Day</option>
										<option value="2">Month</option>
										<option value="3">Year</option>
									</select>
								</div>  
							</div> 
							<div class="col-md-2"> 
								<div class="form-group">    
									<label>Date</label> 
									<?php   
										$fromdt = date("d/m/Y");
										$todt = date("d/m/Y");
									?>
									<input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php //echo $fromdt.' - '.$todt?>" readonly="">
								</div> 
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label>Inventory Turns/Year</label> 
									<input type="text" value="" id="inventory_turns_per_year" class="form-control" />
								</div>  
							</div>

							<div class="col-md-1">
								<div class="form-group">
								    <br>
								 <button type="button" id="inventory_turnover_search" class="btn btn-info">Search</button>   
								</div>  
							</div> 
						
					   </div>
	                   </div> 
	                  </div> 
                   </div> 
                
                </div>
                <p></p>
                
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
			  
                  <div class="table-responsive inventory_turns_container">
				  		
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

<!-- modal -->      
<div class="modal fade" id="stockDaysModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Enter days</h4>
      </div>
      <div class="modal-body">
	    <div class="row" >
			<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
	    </div>
		<div class="row">
			<div class="form-group">

				<table id="stock_age_days" class="table table-bordered table-striped text-center">
					<thead>
						<tr>
							<th>From</th>
							<th>To</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="number" value="" class="form-control days_from" id="days_from_1" /></td>
							<td><input type="number" value="" class="form-control days_to" /></td>
							<td>
								<button class="plus-button stock_days_add"><i class="fa fa-plus fa-lg"></i></button> &nbsp; &nbsp;
							</td>
						</tr>
					</tbody>
				</table>
				
			</div>
		</div> 

      </div>
	  <div class="modal-footer">

        <button type="button" class="btn btn-warning" data-dismiss="modal">Okay</button>

      </div>
    </div>
  </div>
</div>
<!-- / modal -->  