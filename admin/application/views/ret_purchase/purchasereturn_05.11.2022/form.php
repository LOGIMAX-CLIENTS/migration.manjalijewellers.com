      <!-- Content Wrapper. Contains page content -->
    <style>
    	.remove-btn{
			margin-top: -168px;
		    margin-left: -38px;
		    background-color: #e51712 !important;
		    border: none;
		    color: white !important;
		}
		
		
    </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
       

        <!-- Main content -->
        <section class="content order">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Purchase Return</h3>
            </div>
            <div class="box-body">
             <!-- form container --> 
	             <!-- form -->
				<form id="qc_entry_form">
				<div class="row">
				    <div class="col-md-12">
				        
				            <div class="col-md-8">
				                <div class="row">
            				        <div class="col-md-3">
                	                     <div class="form-group">
                	                       <label>PO Ref No</label>
                							    <select class="form-control" id="select_po_ref_no"></select>
                	                     </div> 
            				        </div>
            						<div class="col-md-1">
            				            <div class="form-group">
            				                <br>
            				                <button id="search_by_refno" type="button" class="btn btn-primary" >OR</button>
            				            </div>
            				        </div>
            						
            				        <div class="col-md-4">
                	                     <div class="form-group">
                	                       <label>Select Supplier</label>
                							    <select class="form-control" id="select_karigar">
            									</select>
                	                     </div> 
            				        </div>
            						<div class="col-md-3">
                	                     <div class="form-group">
                	                       <label>Return reason(*)</label>
                							<div class="form-group" >  
            										<input type="radio" id="return_by_damage" name="returnreason" value="1" checked><label for="return_by_damage">Damage</label>
            										&nbsp;&nbsp;&nbsp;
            								<input type="radio" id="return_by_excess" name="returnreason" value="2" ><label for="return_by_excess">Excess</label>    
            									</div>
                	                     </div> 
            				        </div>
        				        </div>
        				        
        				        <div class="row">
        				            <div class="col-md-3"> 
            							<label>Tag Code</label>
            							<div class="form-group">
            							    <input type="text" class="form-control" id="tag_number" placeholder="Enter Tag Code">
            							    <!--<input type="hidden" id="tag_id">-->
            							</div>
            						</div>
            						
                                    <div class="col-md-3"> 
                                        <label>Old Tag</label>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="old_tag_number" placeholder="Enter Tag Code">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                	                     <div class="form-group">
                	                       <label>Non Tag Product</label>
                							  <select class="form-control" id="select_non_tag_product"></select>
                	                     </div> 
            				        </div>
                                        
            						<div class="col-md-3"> 
            							<label></label>
            							<div class="form-group">
            							    <button type="button" id="tag_history_search" class="btn btn-info">Search</button>   
            						    </div>
            						</div>
        				        </div>
				        
    				        </div>
				        
				        

						<div class="col-md-3">
    	                     <div class="form-group">
    	                       <label>Narration(*)</label>
    							<div class="form-group" >  
									<textarea id="returnnarration" name="returnnarration" rows="4" cols="50"></textarea>
								</div>
    	                     </div> 
				        </div>
				    </div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
					    <div class="table-responsive">
                         <h4>Return Item Details</h4>
						 <table id="return_item_detail" class="table table-bordered table-striped">
							<thead style="text-transform:uppercase;">
						          <tr>
						            <th width="5%;"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>  
									<th width="10%;">Supplier</th> 
						            <th width="10%;">Category</th> 
						            <th width="10%;">Return Pcs</th> 
						            <th width="10%;">Return Wt</th>
						            <th width="10%;">Stone</th>
						            <th width="10%;">Other Metal</th>
						            <th width="15%;">Rate</th>
						            <th width="10%;">Amount</th>
						            <th width="10%;">Action</th>
						          </tr>
					         </thead>
					         <tbody></tbody>
					         <tfoot>
					             <tr style="font-weight:bold;">
    					             <td colspan="2" style="text-align: center;">TOTAL</td>
    					             <td class=""></td>
    					             <td class="return_pcs"></td>
    					             <td class="return_wt"></td>
    					             <td class=""></td>
    					             <td></td>
    					             <td></td>
    					             <td></td>
					             </tr>
					       </tfoot>
						</table>
					    </div>
					</div> 
				</div>
				
				
				<div class="row" style="display:none;">
					<div class="col-md-12">
					    <div class="table-responsive">
                         <h4>Return Item Details</h4>
                          <input type="hidden" id="custom_active_id" value="0">
						 <table id="item_detail" class="table table-bordered table-striped">
							<thead style="text-transform:uppercase;">
							     
						          <tr>
						            <th width="10%;"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>  
									<th width="10%;">Supplier</th> 
						            <th width="15%;">Product</th> 
						            <th width="15%;">Design</th> 
						            <th width="10%;">Sub Design</th> 
						            <th width="5%;">Pur Pcs</th> 
						            <th width="5%;">Pur Wt</th> 
						            <th width="5%;">Return Pcs</th> 
						            <th width="5%;">Return Wt</th>
						            <th width="10%;">Old Tag</th>
						            <th width="10%;">Action</th>
						          </tr>
					         </thead>
					         <tbody></tbody>
					         <tfoot>
					             <tr style="font-weight:bold;">
    					             <td colspan="6" style="text-align: center;">TOTAL</td>
    					             <td class=""></td>
    					             <td class="return_pcs"></td>
    					             <td class="return_wt"></td>
    					             <td class=""></td>
    					             <td></td>
					             </tr></tfoot>
						</table>
					    </div>
					</div> 
				</div>	
				
					<div class="row">
        				    <div class="col-md-12">
        				        
        				    </div>
        				    <div class="box box-default total_summary_details">
								<div class="box-body">
									<div class="row">
										<div class="col-md-offset-1 col-md-6">
										    <label>Total Summary Details</label>
										   <div class="table-responsive">
											 <table id="total_summary_details" class="table table-bordered table-striped">
												<tbody> 
													<tr>
												
														<th>Amount</th>
														<th><input type="number" class="form-control total_summary_payable_amt" readonly></th>
													</tr>
													<tr>
														
														<th>Other Charges</th>
														<th>
														    <div class="input-group ">
                            									<input id="other_charges_amount"  name="return_charges_amount" class="form-control custom-inp add_other_charges" type="number" step="any" readonly tabindex="19" />
                            									<span class="input-group-addon input-sm add_other_charges">+</span>
                            									<input type="hidden" id="other_charges_details"  name="order[other_charges_details]" />
                            								</div>
														</th>
													</tr>
												
													<tr>
														
														<th>Round Off</th>
														<th><input type="number" class="form-control return_round_off" name="return_round_off" readonly></th>
													</tr>
													<tr>
														
														<th>Final Price</th>
														<th><input type="number" class="form-control return_total_cost" name="return_total_cost" readonly></th>
													</tr>
														
												</tbody>
												<tfoot>
													<tr></tr>
												</tfoot>
											 </table>
											
										  </div>
										</div>
									</div>
								</div>
							</div>
        				</div>
				
				<div class="row">
					<div class="col-sm-12" align="center">
						<button type="button" id="return_po_items_submit" class="btn btn-primary" >Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
					</div>
				</div>
				
				<p class="help-block"></p>
 
				  <?php echo form_close();?>
	           </div>  
	            
	           <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
	       </div>  
        </section>
</div>

<div class="modal fade" id="cus_stoneModal" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:75%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Stone Details</h4>
			</div>
			<div class="modal-body">
			    <div class="row">
                			<div class="col pull-right">
                			    <button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
                			</div>
            			</div>
    			<div class="row">
    					
    					<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">
        					<thead>
            					<tr>
            					    <th width="5%">LWT</th>
                					<th width="10%">Type</th>
                					<th width="15%">Name</th>
                					<th width="10%">Pcs</th>   
                					<th width="20%">Wt</th>
                					<th width="10%">Cal.Type</th>
                					<th width="15%">Rate</th>
                					<th width="15%">Amount</th>
                					<th width="5%">Action</th>
            					</tr>
        					</thead> 
        					<tbody></tbody>										
        					<tfoot>
        					    <tr></tr>
        					</tfoot>
    					</table>
    			</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" id="update_return_stn_details" class="btn btn-success">Save</button>
			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>


<div class="modal fade" id="cus_chargeModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:50%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Charges( <span class="add_charges"><i class="fa fa-plus"></i></span> )</h4>

			</div>

			<div class="modal-body">

				<div class="row">

					<input type="hidden" id="charge_active_row" value="0">

					<table id="table_charges" class="table table-bordered table-striped text-center">

    					<thead>

        					<tr>

								<th>SNo</th>

            					<th>Charge Name</th>

            					<th>Charge</th>

            					<th>Action</th>

        					</tr>

    					</thead> 

    					<tbody></tbody>										

    					<tfoot><tr></tr></tfoot>

					</table>

			    </div>

		    </div>

		  <div class="modal-footer">

			<button type="button" id="update_charge_details" class="btn btn-success">Save</button>

			<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>
    
<div class="modal fade" id="other_metalmodal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:72%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Other Metals</h4>
			</div>
			<div class="modal-body">
				<div class="row">
        			<div class="col pull-right">
        			    <button type="button" id="create_other_metal_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
        			</div>
    			</div>
				<div class="row">
					<input type="hidden" id="charge_active_row" value="0">
					<table id="other_metal_table" class="table table-bordered table-striped text-center">
    					<thead>
        					<tr>
            					<th width="15%">Metal</th>
            					<th width="10%">Purity</th>
            					<th width="10%">Pcs</th>
            					<th width="10%">Gwt</th>
            					<th width="10%" style="display:none;">V.A(%)</th>
            					<th width="10%" style="display:none;">Mc Type</th>
            					<th width="10%" style="display:none;">Mc</th>
            					<th width="10%">Rate</th>
            					<th width="10%">Amount</th>
            					<th width="10%">Action</th>
        					</tr>
    					</thead> 
    					<tbody></tbody>										
    					<tfoot><tr style="font-weight:bold;"><td>Total</td><td></td><td class="total_pcs"></td><td class="total_wt"></td><td style="display:none;"></td><td style="display:none;"></td><td style="display:none;"></td><td></td><td class="total_amount"></td><td></td></tr></tfoot>
					</table>
			    </div>
		    </div>
		  <div class="modal-footer">
			<button type="button" id="update_return_other_metal_details" class="btn btn-success">Save</button>
			<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>
  