    <style>
        .stickyBlk {
		    margin: 0 auto;
		    top: 0;
		     max-width: 1200px
		    z-index: 999;
		    background: #fff;
		}
		
    </style>

  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Sales and Purchase Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Sales and Purchase Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Sales and Purchase Report (Bill Wise)</h3>  <span id="total_count" class="badge bg-green"></span>
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
								
								<div class="col-md-3"> 
									<div class="form-group tagged">
										<label>Select Village</label>
										<select id="select_village" class="form-control"></select>
									</div> 
								</div> 
								
								<div class="col-md-2"> 
									<div class="form-group tagged">
										<label>Metal</label>
										<select id="rep_metal" class="form-control">
										    <option value="0"> - All - </option>
										    <option value="1"> - Gold - </option>
										    <option value="2"> - Silver - </option>
										</select>
									</div> 
								</div> 
								
								<?php }else{?>
								<div class="col-md-3"> 
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
        		                    <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
								</div>
								<?php }?> 
								
								<div class="col-md-3"> 
									<div class="form-group">    
										<label>Date</label> 
										<?php   
											$fromdt = date("d/m/Y");
											$todt = date("d/m/Y");
									    ?>
			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
									</div> 
								</div>
								<!--<div class="col-md-2"> 
									<label>Select Village</label>
									<select id="village_select" class="form-control" style="width:100%;"></select>
								</div>
								<div class="col-md-2"> 
									<label>Select Customer</label>
									<select id="cus_select" class="form-control" style="width:100%;"></select>
								</div>-->
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="sales_purchase_search" class="btn btn-info">Search</button>   
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
						<div class="box-header with-border">
						  <h3 class="box-title">Sales Purchase and Exchange</h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="sales_purchase_list" class="table table-bordered table-striped text-center">
									  <thead>
            							  <tr style="text-transform:uppercase;">
            							    <th>Bill No</th>
            							    <th>Bill Date</th>
            							    <th>Customer Name</th>
            							    <th>Customer Mobile No</th>
            							    <th>GST No</th>
            							    <th>State</th>
            							    <th>Sales Gross Wt (in Gms)</th>
											<th></th>
            							    <th>Sales Net Wt (in Gms)</th>
											<th></th>
											<th>Purchase Gross Wt (in Gms)</th>
											<th></th>
            							    <th>Purchase Net Wt (in Gms)</th>
											<th></th>
											<th>Exchange Gross Wt (in Gms)</th>
											<th></th>
            							    <th>Exchange Net Wt (in Gms)</th>
											<th></th>
											<th>Sales Taxable Amount</th>
											<th>CGST</th>
											<th>SGST</th>
											<th>IGST</th>
											<th>Total Sales Amt</th>
											<th>Sales Return Taxable Amount</th>
											<th>Sales Return Tax Amount</th>
											<th>Total Exchange Amt</th>
											<th>Total Purchase Amt</th>
											<th>Net Amt</th>
											<th>Cash</th>
											<th>Card</th>
											<th>Chit</th>
											<th>UPI/Mobile Transfer/Net Banking/RTGS/NEFT/IMPS/Online Transfer</th>
											<th>Sales Rate</th>
											<th></th>
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
      

