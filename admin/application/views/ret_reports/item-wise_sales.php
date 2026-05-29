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
			 <small>Product-wise Sales</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Product-wise Sales report</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Product-wise Sales</h3>  <span id="total_count" class="badge bg-green"></span>  
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
										<select id="branch_select" class="form-control branch_filter"></select>
									</div> 
								</div> 
								<?php }else{?>
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
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
									<label>Select Product</label>
									<select id="prod_select" class="form-control" style="width:100%;"></select>
								</div>
								<div class="col-md-3"> 
									<label>Select Metal</label>
									<select id="metal" class="form-control" style="width:100%;"></select>
								</div>
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="item_sale_search" class="btn btn-info">Search</button>   
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
									  <table id="itemwise-sales" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">
										 <thead>
										  <tr>
										    <th width="10%">Product</th>
										    <th width="5%">Pcs</th>
										    <th width="10%">Tag No</th>
										    <th width="10%">Old Tag No</th>
										    <th width="10%">Bill No</th>
										    <th width="5%">Gwt(Grams)</th>
										    <th width="5%">Nwt(Grams)</th>
										    <th width="5%">CERT NO</th>
										    <th width="5%">STYLE code</th>
										    <th width="5%">Amount(Rs)</th>
										    
										  </tr>
					                    </thead><tbody></tbody>
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