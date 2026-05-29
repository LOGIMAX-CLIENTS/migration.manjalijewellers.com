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
			 <small>Purchase Payments</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Purchase payment  report</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Purchase payment</h3>  <span id="total_count" class="badge bg-green"></span>  
                </div>
                 <div class="box-body">  
                  <div class="row">
				  	<div class="col-md-12">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
								
								<div class="col-md-2"> 
									<div class="form-group">    
									    <div class="input-group">
                                          <button class="btn btn-default btn_date_range" id="rpt_payment_date">
                                                  <i class="fa fa-calendar"></i> Date range picker<i class="fa fa-caret-down"></i>
                                           </button>
            							                   	 <span style="display:none;" id="rpt_payments1"></span>
                                               <span style="display:none;" id="rpt_payments2"></span>
                                        </div>	
									</div> 
								</div>
								
								<div class="col-md-3"> 
									<select id="karigar" class="form-control" style="width:100%;"></select>
								</div>
								<div class="col-md-2"> 
									<div class="form-group">
										<input type="text" id="chq_no" class="form-control" placeholder="Cheque No">
									</div>
								</div>
								<div class="col-md-2"> 
									<select id="verify_filter" class="form-control">
										<option value="">All Status</option>
										<option value="1">Verified</option>
										<option value="2">Need to Verify</option>
									</select>
								</div>
								<div class="col-md-1"> 
									<div class="form-group">
										<button type="button" id="po_payment_search" class="btn btn-info">Search</button>   
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
									  <table id="purchase_pay_list" class="table table-bordered table-striped text-center">
										 <thead>
										  <tr>
										    <th width="10%">Ref No</th>
											<th width="10%">PO No</th>
										    <th width="10%">Supplier Name</th>
										    <th width="10%">Rate</th>
										    <th width="10%">Pure Wt</th>
										    <th width="10%">Rejected Gross Wt</th>
										    <th width="10%">Rejected Pure Wt</th>
										    <th width="10%">Amount</th>
										    <th width="10%">Payment Date</th>
											<th width="10%">Due Date</th>
										    <th width="10%">Payment Mode</th>
											<th width="10%">Payee Name</th>
											<th width="10%">Chq No</th>
											<th width="10%">Chq Date</th>
										    <th width="10%">Different Days</th>
										    <th width="8%">Status</th>
										    <!-- <th width="10%">Bank</th>
										    <th width="10%">Ref Date</th>
											<th width="10%">Purchase Date</th>
										    <th width="10%">Ref No</th> -->
										 </tr>
					                    </thead><tbody></tbody>
					                    <tfoot>
											<tr style="color:red; font-weight: bold">
					<td>Total</td>
					<td></td>
					<td></td>
					<td></td>
					<td style="text-align:right"></td>
					<td style="text-align:right"></td>
					<td style="text-align:right"></td>
					<td style="text-align:right"></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>
					                    </tfoot>
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