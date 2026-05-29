<style>
  	/* CSS for Drill-down */
  	.drill-collapsed {
	    display: none;
	}
	.drill-close {
	    display: none;
	}
	.drill-open {
	    display: block;
	}
	.drill-detail {
	    background:#fdfdfd
	}
	/* .CSS for Drill-down */
  </style>
     <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Purchase Bills Rate Unfixing Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Purchase Bills Rate Unfixing Report</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
                 <div class="box-body">  
                  <div class="row">
				  	<div class="col-md-12">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
								
								<!-- <div class="col-md-2"> 
									<div class="form-group">    
										<label>Date</label> 
										<?php   
											$fromdt = date("d/m/Y");
											$todt = date("d/m/Y");
									    ?>
			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
									</div> 
								</div> -->

                                <div class="col-md-2"> 
									<label>Select Category</label>
									<select id="category" style="width: 100%;" multiple></select>
								</div>

								<div class="col-md-2"> 
									<label>Select Metal</label>
									<select id="metal" style="width: 100%;"multiple></select>
								</div>

								<div class="col-md-2"> 
									<label>Select Karigar</label>
									<select id="karigar" class="form-control" style="width:100%;" multiple></select>
								</div>
								
								<div class="col-md-2"> 
									<label>Purchase Type</label>
									<select id="purchase_type" class="form-control" style="width:100%;">
									    <option value="0">All</option>
									     <option value="1">Ornaments</option>
									     <option value="2">Metal</option>
									     <option value="3">Stone</option>
									</select>
								</div>

								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="unfixing_search" class="btn btn-info">Search</button>   
									</div>
								</div>
							</div>
						 </div>
	                   </div> 
	                  </div> 
                   </div> 
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
				   	<div class="box box-info stock_details">
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
								   <table id="unfixing_list"class="table table-striped table-bordered">									
										 <thead>
										  	<tr>
												<th>GRN No</th>
												<th>PO No</th>
												<th>Bill Date</th>
                                                <th>Category</th>
												<th>Karigar</th>
												<th>Gross Wgt</th>
												<th>Net Wgt</th>
												<th>Dia Wgt</th>
												<th>Stone Amount</th>
												<th>Taxable Amount</th>
												<th>Tax</th>
												<th>Total Amount</th>
												<th>Pure Wgt</th>
												<th>Un Fix Rate</th>
												<th>Fixed Wgt</th>
												<th>Return Wgt</th>
												<th>Balance Wgt</th>
												<th>Fixed Rate</th>
												<th>Cr/Dr</th>
										 	</tr>
					                    </thead>
										<tbody>

										</tbody>
					                   
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