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
            Purchase Return Report
          </h1>
          
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
								
								<div class="col-md-2"> 
								     <label></label>
    								 <div class="form-group">
                                          <button class="btn btn-default btn_date_range"  id="rpt_date_picker">
                                                    <i class="fa fa-calendar"></i> Date range picker
                                                    <i class="fa fa-caret-down"></i>
                                            </button>
                                                <span style="display:none;" id="rpt_from_date"></span>
                                                <span style="display:none;" id="rpt_to_date"></span>
                                         </div><!-- /.form group -->
                                </div>
                                
                                <div class="col-md-2"> 
									<label>Report Type</label>
									<select id="pur_type" class="form-control" style="width:100%;">
									     <option value="1">Detailed</option>
									     <option value="2">Summary</option>
									</select>
								</div>
								
								<div class="col-md-2"> 
									<label>Type</label>
									<select id="purchase_type" class="form-control" style="width:100%;">
									    <option value="0">All</option>
									     <option value="1">Ornaments</option>
									     <option value="2">Bullion Purchase</option>
									     <option value="3">Stones</option>
									</select>
								</div>
								
								<div class="col-md-2"> 
									<label>Select Metal</label>
									<select id="metal" class="form-control" style="width:100%;" multiple></select>
								</div>
								
								<div class="col-md-2"> 
                                	<label>Select category</label>
                                	<select id="category" style="width: 100%;" multiple></select>
                                </div>
                                
                                <div class="col-md-2"> 
									<label>Select Product</label>
									<select id="prod_select" class="form-control" style="width:100%;"></select>
								</div>

							</div><p></p>
									
							<div class="row">
							    <div class="col-md-2"> 
									<label>Select Design</label>
									<select id="des_select" class="form-control" style="width:100%;"></select>

								</div>
								<div class="col-md-2"> 
									<label>Select Sub Design</label>
									<select id="sub_des_select" class="form-control" style="width:100%;"></select>

								</div>
							    <div class="col-md-2"> 
									<label>Select Karigar</label>
									<select id="karigar" class="form-control" style="width:100%;" multiple></select>
								</div>
								<div class="col-md-2"> 
									<label>Status Type</label>
									<select id="bill_type" class="form-control" style="width:100%;">
									     <option value="1">Success</option>
									     <option value="2">Cancelled</option>
									</select>
								</div>

								
								<div class="col-md-2"> 
									<label>Transcation Type</label>
									<select id="transcation_type" class="form-control" style="width:100%;">
									     <option value="">All</option>
									    <option value="1">Supplier</option>
									     <option value="2">Manufaucturers</option>
									     <option value="3">Approval Ledger</option>
									     <option value="4">Stone Supplier</option>
										 <option value="5">Abstract</option>

									</select>
								</div>
								
								<div class="col-md-2 tag_code"> 
								    <label>Tag Code</label>
										  <div class="form-group">
										  <input type="text" id="tag_code" class="form-control" placeholder="Enter Tag Code">
										  </div>
								  </div>
							</div>
								  <!-- <div class="col-md-2 bt_code"> 
								    <label>BT Code</label>
										  <div class="form-group">
										  <input type="text" id="bt_code" class="form-control" placeholder="Enter BT Code">
										  </div>
								  </div>
		 --> 
		 					<div class="row">
							 	<div class="col-md-2">
									<div class="form-group">
										<label>Return Purchase Type</label>
										<select id="return_purchase_type" class="form-control">
											<option value="">All</option>
											<option value="0">Purchase</option>
											<option value="1">Sales</option>
											<option value="2">Sales Return</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Purchase Type</label>
										<select id="purchase_type2" class="form-control">
											<option value="">All</option>
											<option value="1">Stock</option>
											<option value="2">Walkin</option>
											<option value="3">Expo</option>
										</select>
									</div>
								</div>
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="purchase_return_search" class="btn btn-info">Search</button>   
									</div>
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
									  <table id="purchase_return_bills" class="table table-bordered table-striped text-center">
										 <thead>
										  <tr>
										   <th width="10%">S.No</th>
										    <th width="10%">Po Ref No</th>
										    <th width="10%">PO No</th>
										    <th width="10%">Date</th>
											<th width="10%">BT No</th>
											<th width="10%">Return Purchase Type</th>
											<th width="10%">Purchase Type</th>
										    <th width="10%">Karigar</th>
										    <th width="20%">Category</th>
										    <!-- <th width="20%">Purity</th> -->
										    <th width="20%">Product</th>
										    <th width="20%">Design</th>
										    <th width="20%">Sub Design</th>
										    <th width="10%">Pcs</th>
										    <th width="10%">Gwt</th>
										    <th width="10%">Lwt</th>
										    <th width="10%">Nwt</th>
										    <th width="10%">Dia Wt</th>
											<th width="10%">Stone Wt</th>
										    <th width="10%">V.A</th>
										    <th width="10%">MC Type</th>
										    <th width="10%">MC</th>
										    <th width="10%">Touch</th>
										    <th width="10%">Pure</th>
										    <th width="10%">Rate</th>
										    <th width="10%">Other Metal</th>
										    <th width="10%">Stone</th>
										    <th width="10%">Taxable Amount</th>
										    <th width="10%">Tax</th>
										    <th width="10%">GST Amount</th>
										    <th width="10%">Remark</th>
											<th width="10%">Transcation Type</th>
										    <th width="10%">Employee</th>
										 </tr>
					                    </thead><tbody></tbody>
					                    <tfoot>
					                        <td></td>
											<td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
											<td></td>

					                    </tfoot>
									 </table>
								  </div>
								</div> 
							</div> 
						</div>
					</div>
					
					 <div class="box box-info summary_details" style="display:none">
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="summary_return_bills" class="table table-bordered table-striped text-center">
										 <thead>
										  <tr>
										   <th width="10%"> S.No</th>
										    <th width="10%">Po Ref No</th>
										    <th width="10%">PO No</th>
										    <th width="10%">Date</th>
											<th width="10%">BT No</th>
										    <th width="10%">Karigar</th>
										    <th width="10%">Pcs</th>
										    <th width="10%">Gwt</th>
										    <th width="10%">Lwt</th>
										    <th width="10%">Nwt</th>
										    <th width="10%">Dia Wt</th>
											<th width="10%">Stone Wt</th>
										    <th width="10%">V.A</th>
										    <th width="10%">MC Type</th>
										    <th width="10%">MC</th>
										    <th width="10%">Touch</th>
										    <th width="10%">Pure</th>
										    <th width="10%">Rate</th>
										    <th width="10%">Other Metal</th>
										    <th width="10%">Stone</th>
										    <th width="10%">Taxable Amount</th>
										    <th width="10%">GST Amount</th>
										    <th width="10%">Total Amount</th>
											<th width="10%">Transcation Type</th>
											<th width="10%">Employee</th>

										 </tr>
					                    </thead><tbody></tbody>
					                    <tfoot>
					                        <td></td>
											<td></td>
					                        <td></td>
					                        <td></td>
											<td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
											<td></td>
					                        <td></td>
					                        <td></td>
					                        <td></td>
											<td></td>
					                        <td></td>
					                        <td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>

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
      
<div class="modal fade" id="stone_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">
	<div class="modal-content">
	    <div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Stone Details</h4>
		</div>
		<div class="modal-body">
			<div>
			<table id="stone_details" class="table table-bordered table-striped text-center">
				<thead>
				<tr>
				<th>Stone Name</th>
				<th>Stone Pcs</th>
				<th>Weight</th>
				<th>Rate</th>
				<th>Amount</th>
				</tr>
				</thead> 
				<tbody>
				</tbody>										
			</table>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
</div>