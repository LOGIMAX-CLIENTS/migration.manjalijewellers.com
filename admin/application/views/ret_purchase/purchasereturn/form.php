      <!-- Content Wrapper. Contains page content -->

    <style>

    	.remove-btn{

			margin-top: -168px;

		    margin-left: -38px;

		    background-color: #e51712 !important;

		    border: none;

		    color: white !important;

		}

		

		 #total_summary_details td{

        padding : 1px 5px !important;

    }

   #total_summary_details input[type=text],#total_summary_details input[type=number], #total_summary_details button {

        height: 25px !important;

        padding: 1px 5px !important;

    }

    

    input[type=number]::-webkit-inner-spin-button, 

        input[type=number]::-webkit-outer-spin-button { 

        -webkit-appearance: none;

        -moz-appearance: none;

        appearance: none;

        margin: 0; 

        }

		

    </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

       



        <!-- Main content -->

        <section class="content order">



          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">Supplier Return / Sales</h3>

            </div>

            <div class="box-body">

             <!-- form container --> 

	             <!-- form -->

				<form id="qc_entry_form">

				<div class="row">

				    <div class="col-md-12">

				                <div class="row">

				                    <div class="col-md-3">

                                    		<div class="form-group">

                                    		<label>Select Type(*)</label>

                                    		<div class="form-group" >  

                                    				<input type="radio" id="purchase_ret_type" name="purchase_type" value="0" checked><label for="purchase_ret_type">Return</label>

                                    				&nbsp;&nbsp;&nbsp;

                                    				<input type="radio" id="sales_ret_type" name="purchase_type" value="1" ><label for="sales_ret_type">Sales</label>   &nbsp;&nbsp;&nbsp;
                                    				
                                    				<?php if($enable_pur_return_sales_return == 1): ?>
                                    				<input type="radio" id="sales_return_type" name="purchase_type" value="2" ><label for="sales_return_type">Sales Return</label>
                                    				<?php endif; ?>    

                                    			</div>

                                    		</div> 

                                    </div>



				                    <div class="col-md-3 return_type">

                	                     <div class="form-group">

                	                       <label>Stock Type(*)</label>

                							<div class="form-group" >  

            										<input type="radio" id="normal_stock_stype" name="stock_type" value="0" checked><label for="normal_stock_stype">Normal Stock</label>

            										&nbsp;&nbsp;&nbsp;

            								        <input type="radio" id="approval_stock_type" name="stock_type" value="1" ><label for="approval_stock_type">Suspense Stock</label>    

            									</div>

                	                     </div> 

            				        </div>

									<div class="col-md-3 pur_return_receipt_type">
										<div class="form-group">
											<label>Receipt Type(*)</label>
											<div class="form-group">
												<input type="radio" id="po_stype" name="purret_receipt_type" value="0" checked><label for="normal_stock_stype">PO RefNO</label>

												&nbsp;&nbsp;&nbsp;

												<input type="radio" id="tag_type" name="purret_receipt_type" value="1" ><label for="approval_stock_type">Tag</label>  
												
												&nbsp;&nbsp;&nbsp;

												<input type="radio" id="nontag_type" name="purret_receipt_type" value="2" ><label for="approval_stock_type">NonTag</label>  

											</div>
										</div>
									</div>

				                    <div class="col-md-3 return_type">

                	                     <div class="form-group">

                	                       <label>Filter by Supplier<span class="error">*</span></label>

                							    <select class="form-control" id="select_karigar" style="width:75%;">

            									</select>

            									<input type="hidden" id="cmp_country"  value="<?php echo $comp_details['id_country'];?>">

                								<input type="hidden" id="cmp_state"  value="<?php echo $comp_details['id_state'];?>">

                								<input type="hidden" id="supplier_country"  value="">

                								<input type="hidden" id="supplier_state" value="">

                								<input type="hidden" id="ret_karigar_calc_type" value="">

                	                     </div> 

            				        </div>

								</div>
								<div class="row">

            				        <div class="col-md-3 purretPo">

                	                     <div class="form-group">

                	                       <label>PO Ref No</label><br>

										   <div class="input-group">
												
												<select class="form-control" id="pur_fin_year_select" style="width:100px;">
													<?php
														foreach ($financial_year as $fin_year) { ?>
															<option value=<?php echo $fin_year['fin_year_code']; ?> <?php echo ($fin_year['fin_status'] == 1 ? 'selected' : '')  ?>><?php echo $fin_year['fin_year_name']; ?></option>
														<?php }
													?>
												</select>
												
												<select class="form-control" id="select_po_ref_no" disabled style="width: 150px;"></select>
											</div>

                	                     </div> 

            				        </div>
            				        
            				        


									<div class="col-md-3">
                	                     <div class="form-group">
                	                       <label>Issue to Supplier<span class="error">*</span></label>
                							    <select class="form-control" id="purret_to_karigar" style="width:75%;">
            									</select>
                	                     </div> 
            				        </div>
            				        
            				        <div class="col-md-3 sales_return_type" style="display:none;">
                	                     <div class="form-group">
                	                       <label>Bill No</label><br>
                							    <select class="form-control" id="sales_return_bill_no"  style="width:75%;"></select>
                	                     </div> 
            				        </div>


									<div class="col-md-3">
										<div class="form-group">
											<label>Purchase Return Convert To(*)</label>
											<div class="form-group">
												<input type="radio" value="1" name="pur_return_convert_to" checked><label>Supplier</label></input>&nbsp;
												<input type="radio" value="2" name="pur_return_convert_to" ><label>Manufaucturers</label></input>&nbsp;
												<input type="radio" value="3" name="pur_return_convert_to"><label>Approval Ledger</label></input><br>
												<input type="radio" value="4" name="pur_return_convert_to" ><label>Stone Supplier</label></input>&nbsp;
												<input type="radio" value="5" name="pur_return_convert_to" ><label>Diamond Supplier</label></input>&nbsp;
											</div>
										</div>
									</div>



            						<div class="col-md-3 return_type">

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

								

        				        

        				        <div class="row purretTag" style="display:none;">

									<div class="col-md-3">
										<div class="form-group">
											<label>Tag Issue From(*)</label>
											<div class="form-group">
												<input type="radio" value="1" name="tag_issue_from" checked><label>Available Stock</label></input>&nbsp;
												<input type="radio" value="2" name="tag_issue_from" ><label>Sales Return</label></input><br>&nbsp;
												<input type="radio" value="3" name="tag_issue_from"><label>Partly Sales</label></input>
												<input type="radio" value="4" name="tag_issue_from"><label>H.O Other Issue</label></input>
											</div>
										</div>
									</div>


									<div class="col-md-2 branch_transfer_issue" style="display:none;"> 
										<label>BT Code</label>
										<div class="form-group">
											<input type="text" class="form-control" id="bt_number" placeholder="Enter BT Code">
										</div>
									</div>

        				            <div class="col-md-2 tag_issue"> 

            							<label>Tag Code</label>

            							<div class="form-group">

            							    <input type="text" class="form-control" id="tag_number" placeholder="Enter Tag Code" disabled>

            							    <!--<input type="hidden" id="tag_id">-->

            							</div>

            						</div>

            						

                                    <div class="col-md-2 tag_issue"> 

                                        <label>Old Tag</label>

                                        <div class="form-group">

                                            <input type="text" class="form-control" id="old_tag_number" placeholder="Enter Tag Code" disabled>

                                        </div>

                                    </div>

                                    

            						<div class="col-md-2"> 

            							<label></label>

            							<div class="form-group">

            							    <button type="button" id="tag_history_search" class="btn btn-info" disabled>Search</button>   

            						    </div>

            						</div>

        				        </div>

				    </div>

				    <div class="row purretNonTag" style="display:none;">

				      <div class="col-md-12">


					  		<div class="col-md-3">
								<div class="form-group">
									<label>NonTag Issue From(*)</label>
									<div class="form-group">
										<input type="radio" value="1" name="nontag_issue_from" checked><label>Available Stock</label></input>&nbsp;
										<input type="radio" value="2" name="nontag_issue_from" ><label>Nontag Sales Return</label></input><br>&nbsp;
										<input type="radio" value="3" name="nontag_issue_from"><label>Nontag Other Issue</label></input>
									</div>
								</div>
							</div>

					  		<div class="col-md-2 nt_issue">
								<div class="form-group">
									<label>Non Tag Section<span class="error">*</span></label>
									<select class="form-control" id="select_section" style="width:100%;"></select>
								</div>
							</div>

    			            <div class="col-md-2 nt_issue">

        	                     <div class="form-group">

        	                       <label>Non Tag Product<span class="error">*</span></label>

        							  <select class="form-control" id="select_product" style="width:100%;" disabled></select>

        	                     </div> 

    				        </div>

    				        <div class="col-md-2 nt_issue"> 

                                <div class="form-group" >

                                    <label>Design<span class="error">*</span></label> 

                                    <select class="form-control" id="select_design" style="width:100%;"></select>

                                </div>

    						 </div>

    						 <div class="col-md-2 nt_issue"> 

                                <div class="form-group" >

                                    <label>Sub Design<span class="error">*</span></label> 

                                    <select class="form-control" id="select_sub_design" style="width:100%;"></select>

                                </div>

    						 </div>

    						 <div class="col-md-2 nt_issue"> 

                                <div class="form-group" >

                                    <label>Piece<span class="error">*</span></label> 

                                    <input class="form-control" type="number" id="issue_pcs"  placeholder="Pcs">

                                    <b>Avail Pcs :<span class="available_pcs"></span></b>

                                </div>

    						 </div>

    						 

    						 <div class="col-md-2 nt_issue"> 

                                <div class="form-group" >

                                    <label>Weight<span class="error">*</span></label> 

                                    <input class="form-control" type="number" id="issue_weight"  placeholder="Weight">

                                    <b>Avail Wt :<span class="available_weight"></span></b>

                                </div>

    						 </div>

    						 <div class="col-md-2 nt_issue"> 

    							<label></label>

    							<div class="form-group">

    							    <button type="button" id="set_non_tag_stock_list" class="btn btn-info">Add</button>   

    						    </div>

    						</div>

							<div class="col-md-2 branch_transfer_issue" style="display:none;"> 
								<label>BT Code</label>
								<div class="form-group">
									<input type="text" class="form-control" id="nt_bt_number" placeholder="Enter BT Code">
								</div>
							</div>

							<div class="col-md-2 branch_transfer_issue" style="display:none;"> 
								<label></label>
								<div class="form-group">
									<button type="button" id="nontag_search" class="btn btn-info">Search</button>   
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

									<th width="10%;">Tag Code</th> 

                                    <th width="10%;">Category</th> 

                                    <th width="10%;">Product</th> 

                                    <th width="10%;">Design</th> 

                                    <th width="10%;">Sub Design</th> 

                                    <th width="10%;">Pcs</th> 

                                    <th width="10%;">GWt</th>

                                    <th width="10%;">LWt</th>

                                    <th width="10%;">NWt</th>

                                    <th width="10%;">V.A(%)</th>

                                    <th width="10%;">V.A Wgt</th>

                                    <th width="10%;">Mc type</th>

                                    <th width="10%;">Mc</th>

                                    <th width="10%;">Touch</th>

                                    <th width="10%;">Calc Type</th>

                                    <th width="10%;">Pure</th>

                                    <th width="15%;">Rate(per GRM)</th>

                                    <th width="10%;">Other MetalWt</th>

                                    <th>Other Charges</th>

                                    <th width="10%;">Taxable Amt</th>

                                    <th width="10%;">Tax</th>

                                    <th width="10%;">Amount</th>

                                    <th width="10%;">Action</th>

                                 </tr>

					         </thead>

					         <tbody></tbody>

					         <tfoot>

                            	<tr style="font-weight:bold;">

                            		<td colspan="6" style="text-align: center;">TOTAL</td>

                            		<td class="return_pcs"></td>

                            		<td class="return_gwt"></td>

                            		<td class="return_lwt"></td>

                            		<td class="return_nwt"></td>

                            		<td></td>

                            		<td class=""></td>

                            		<td></td>

                            		<td></td>

                            		<td></td>

									<td></td>

									<td class="return_pure"></td>

									
                            		<td></td>

                            		<td></td>

                            		<td></td>

									<td></td>

									<td></td>

									<td class="return_amount"></td>

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

										<div class="col-md-6">

										    <label>Total Summary Details</label>

										   <div class="table-responsive">

											 <table id="total_summary_details" class="table table-bordered table-striped" style="text-transform: uppercase">

												<tbody> 

													<tr>

												

														<th>Taxable Amount</th>

														<th></th>

														<th><input type="number" class="form-control total_summary_taxable_amt" readonly></th>

													</tr>

													<tr>

													    <th>TDS</th>

													    <th>(-)</th>

													    <th>

													        <div class="input-group" >

                                			                       <input class="form-control tds_percent" type="number" name="order[tds_percent]" id="tds_percent" tabindex="21"  />

                                			                       <span class="input-group-btn"><input type="number" class="form-control tds_tax_value" name="order[tds_tax_value]" id="tds_tax_value" tabindex="22" style="width:200px;" readonly /></span>

                                			                </div>

													    </th>

													</tr>

													<tr>

												

														<th>CGST</th>

														<th>(+)</th>

														<th><input type="number" class="form-control total_summary_cgst_amount" readonly></th>

													</tr>

													<tr>

												

														<th>SGST</th>

														<th>(+)</th>

														<th><input type="number" class="form-control total_summary_sgst_amount" readonly></th>

													</tr>

													<tr>

												

														<th>IGST</th>

														<th>(+)</th>

														<th><input type="number" class="form-control total_summary_igst_amount" readonly></th>

													</tr>

													<tr>

													    <th>TCS</th>

													    <th>(+)</th>

													    <th>

													        <div class="input-group" >

                                			                       <input class="form-control tcs_percent" type="number" name="order[tcs_percent]" id="tcs_percent" tabindex="21"  />

                                			                       <span class="input-group-btn"><input type="number" class="form-control tcs_tax_value" name="order[tcs_tax_value]" id="tcs_tax_value" tabindex="22" style="width:200px;" readonly /></span>

                                			                </div>

													    </th>

													</tr>

													<tr >

														<th>Other Charges</th>

														<th>(+)</th>

														<th>

														    <div class="input-group ">

                            									<input id="other_charges_taxable_amount"  name="order[other_charges_amount]" class="form-control custom-inp add_other_charges" type="number" step="any" readonly tabindex="19" />

                            			

                            								</div>

														</th>

													</tr>

													<tr>

													    <th>Charges TDS</th>

													    <th>(-)</th>

													    <th>

													        <div class="input-group" >

                                			                       <input class="form-control charges_tds_percent" type="number" name="order[charges_tds_percent]" id="charges_tds_percent" tabindex="21"  />

                                			                       <span class="input-group-btn"><input type="number" class="form-control other_charges_tds_tax_value" name="order[other_charges_tds_tax_value]" id="other_charges_tds_tax_value" tabindex="22" style="width:200px;" readonly /></span>

                                			                </div>

													    </th>

													</tr>

													<tr style="display:none;">

														<th>Other Charges Tax</th>

														<th>(+)</th>

													    <th><input type="number" class="form-control other_charges_tax" name="order[other_charges_tax]" id="other_charges_tax" readonly></th>

													</tr>

													

													<tr>

														<th>Other Charges CGST</th>

														<th>(+)</th>

													    <th><span class="other_charges_cgst"></span></th>

													</tr>

													

													<tr>

														<th>Other Charges SGST</th>

														<th>(+)</th>

													    <th><span class="other_charges_sgst"></span></th>

													</tr>

													

													<tr>

														<th>Other Charges IGST</th>

														<th>(+)</th>

													    <th><span class="other_charges_igst"></span></th>

													</tr>

													

													<tr>

													

														<th>Discount</th>

														<th>(-)</th>

														<th>

														    <input type="number" class="form-control return_discount" name="order[discount]" tabindex="20"  />

														</th>

													</tr>

													

													<tr>

														

														<th>Round Off</th>

														<th><select class="round_off_symbol"  name="order[round_off_type]"><option value="1">+</option><option value="0">-</option></select></th>

														<th><input type="number" class="form-control return_round_off" name="order[round_off]"></th>

													</tr>

												    

													

													<tr>

														

														<th>Final Price</th>

														<th></th>

														<th><input type="number" class="form-control return_total_cost" name="order[total_cost]" readonly></th>

													</tr>

														

												</tbody>

												<tfoot>

													<tr></tr>

												</tfoot>

											 </table>

											

										  </div>

										</div>

										<div class="col-md-6">

                                        	<div class="table-responsive">

                                        		<table id="return_item_details_preview" class="table table-bordered table-striped" style="text-transform: uppercase">

                                        			<thead>

                                        				<tr>

                                        				<th width="10%;">Category</th>

                                        				<th width="5%;">Pieces</th>

                                        				<th width="5%;">Weight</th>

                                        				<th width="5%;">Rate</th>

                                        				<th width="5%;">Amount</th>

                                        				</tr>

                                        			</thead>

                                        			<tbody></tbody>

                                        			<tfoot>

                                        				<tr>

                                        					

                                        				</tr>

                                        			</tfoot>

                                        		</table>

                                        		</div> 

                                        </div>

									</div>

								</div>

							</div>

        				</div>

				

				<div class="row">

				    <div class="col-md-3">

    	                     <div class="form-group">

    	                       <label>Narration(*)</label>

    							<div class="form-group" >  

									<textarea id="returnnarration" name="returnnarration" rows="4" cols="50"></textarea>

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

    <div class="modal-dialog" style="width:90%;">

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

									<th width="13%">Code</th>

                					<th width="10%">Pcs</th>   

                					<th width="20%">Wt</th>

                					<th width="10%">Cal.Type</th>

									<th width="10%">Cut</th>

									<th width="10%">Color</th>

									<th width="10%">Clarity</th>

									<th width="10%">Shape</th>

                					<th width="15%">Rate</th>

                					<th width="15%">Amount</th>

                					<th width="5%">Action</th>

            					</tr>

        					</thead> 

        					<tbody></tbody>										

        					<tfoot>

								<tr style="font-weight:bold;font-size:15px">
									<td>Total:</td>
									<td></td>
									<td style="display:none" class="metalissse_product"></td>
									<td></td>
									<td></td>
									<td class="stn_tot_pcs"></td>
									<td class="stn_tot_weight"></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td class="stn_tot_amount"></td>
									<td></td>
								</tr>

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

            					<th width="10%">V.A(%)</th>

            					<th width="10%">Mc Type</th>

            					<th width="10%">Mc</th>

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





<div class="modal fade" id="pur_chargeModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



    <div class="modal-dialog" style="width:50%;">



		<div class="modal-content">



			<div class="modal-header">



				<h4 class="modal-title" id="myModalLabel">Add Charges( <span class="add_pur_charges"><i class="fa fa-plus"></i></span> )</h4>



			</div>



			<div class="modal-body">



				<div class="row">



					<input type="hidden" id="charge_active_row" value="0">



					<table id="table_charges" class="table table-bordered table-striped text-center">



    					<thead>



        					<tr>



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



			<button type="button" id="update_pur_charge_details" class="btn btn-success">Save</button>



			<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>



		  </div>



		</div>



	</div>



</div>

  