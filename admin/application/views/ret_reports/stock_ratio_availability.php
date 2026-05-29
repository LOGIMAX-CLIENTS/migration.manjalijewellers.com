 <style type="text/css">
	.hidden_branch_data {
		display: none;
	}

	.stock-child-max-width {
		max-width: 1400px;
		margin: 0 auto;
		overflow: scroll;
		padding-top: 10px;
		padding-bottom: 20px;
	}

	.stock_ratio_child_table th {
		background-color: #0000FF;
		color: white;
	}
	.stock_ratio_child_table .br_name, .stock_ratio_child_table .stock_perc {
		font-weight: bold;
		font-size: 18px;
	}

	.percentage {
		text-align: right !important;
	}
	@media print {
		.percentage {
			text-align: right !important;
		}
	}
 </style> 
 
 <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Stock ratio availability</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Stock ratio</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Stock ratio availability</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                  <div class="row">
				  <div class="col-md-12">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						<div class="row"> 
							<div class="col-md-2">
								<div class="form-group">
									<label>Product</label> 
									<select id="prod_select" class="form-control"></select>
								</div>  
							</div> 
							
							<div class="col-md-2">
								<div class="form-group">
									<label>Design</label> 
									<select id="des_select" class="form-control"></select>
								</div>  
							</div> 
							
							<div class="col-md-2">
								<div class="form-group">
									<label>Sub Design</label> 
									<select id="sub_des_select" class="form-control"></select>
								</div>  
							</div> 

							<div class="col-md-1">
								<div class="form-group">
									<label>Size</label> 
									<select id="size" class="form-control"></select>
								</div>  
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label>Weight Range</label> 
									<select id="wt_select" class="form-control"></select>
								</div>  
							</div>

							<div class="col-md-2"> 
								<div class="form-group">    
									<label>Date Range</label> 
									<?php   
										$fromdt = date("d/m/Y");
										$todt = date("d/m/Y");
									?>
									<input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php //echo $fromdt.' - '.$todt?>" readonly="">
								</div> 
							</div>

							<div class="col-md-1">
								<div class="form-group">
								    <br>
								 <button type="button" id="stockratio_search" class="btn btn-info">Search</button>   
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
			  
                  <div class="table-responsive stock_ratio_container">

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