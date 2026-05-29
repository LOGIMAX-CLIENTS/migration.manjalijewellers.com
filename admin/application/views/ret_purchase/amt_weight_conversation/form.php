<!-- Content Wrapper. Contains page content -->

<style>

      	.remove-btn {

      		margin-top: -168px;

      		margin-left: -38px;

      		background-color: #e51712 !important;

      		border: none;

      		color: white !important;

      	}
		h4{
			font-size: 14px;
    		font-weight: 800;
		}
      	

      	 input[type=number]::-webkit-inner-spin-button, 

            input[type=number]::-webkit-outer-spin-button { 

            -webkit-appearance: none;

            -moz-appearance: none;

            appearance: none;

            margin: 0; 

        }

        

      	.badge-cr {
      		color: #e51712;
      		font-weight: bold;
      		font-size: 13px;
      		margin-left: 5px;
      	}
      	.badge-dr {
      		color: #00a65a;
      		font-weight: bold;
      		font-size: 13px;
      		margin-left: 5px;
      	}
      </style>

      <div class="content-wrapper">

      	<!-- Content Header (Page header) -->

      	<section class="content-header">

      		<h1>

      			Pure Weight to Amount Conversion 

      		</h1>

      	</section>



      	<!-- Main content -->

      	<section class="content product">

      		<div class="box box-primary">

      			<div class="box-body">

      				<div class="row">

      					<form id="supplier_rate_cut">

							<input type="hidden" id="issue_against_po_bills" name="issue[issue_against_po_bills]" value="<?php echo $is_against_po_bills;?>">

      						<div class="container">

      						    <div class="row">

      						        <div class="col-md-6">

      						                <div class="row">

                  									<div class="col-xs-4">

                  										<label type="text">Cost Center</label>

                  									</div>

            

                  									<div class="col-xs-4">

                  										<select class="form-control" style="width:100%" id="branch_select" disabled></select>

                  										<input type="hidden" name=supplier_rate_cut[id_branch] id="id_branch">

														  <input type="hidden" id="cmp_country" value="<?php echo $comp_details['id_country'];?>">

														  <input type="hidden" id="cmp_state" value="<?php echo $comp_details['id_state'];?>">

                  									</div>

            

                  								</div><br />

                  								

                  								<div class="row">

                  								    <div class="col-xs-4">

                  										<label type="text">Opening Balance</label>

                  									</div>

                  									<div class="col-sm-4">

                  										<input type="radio" value="1" name="supplier_rate_cut[is_opening_blc]" ></input>

                  										<label for="amt_to_wt">Yes</label>

            

                  									</div>

                  									<div class="col-md-4">

            

                  										<input type="radio" value="0" name="supplier_rate_cut[is_opening_blc]"  checked></input>

                  										<label for="wt_to_amt">No</label>

            

                  									</div>

                  								</div></br>

                  								

                  									<div class="row">

                  									<div class="col-sm-4">

                  										<input type="radio" value="1" name="supplier_rate_cut[rate_cut_type]" id="amt_to_wt" ></input>

                  										<label for="amt_to_wt">Amount</label>

            

                  									</div>

                  									<div class="col-md-4">

            

                  										<input type="radio" value="2" name="supplier_rate_cut[rate_cut_type]" id="wt_to_amt" checked></input>

                  										<label for="wt_to_amt">Pure to Amount</label>

            

                  									</div>

                  									<div class="col-md-4">

                  										<input type="radio" value="3" name="supplier_rate_cut[rate_cut_type]" id="amt_to_pure"></input>

                  										<label for="amt_to_pure">Amount to Pure</label>

                  									</div>

                  								</div></br>



												<div class="row convert_to">

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  												<label type="text">Convert Bill To</label>

                  											</div>

                  										</div>

                  									</div>

                  									<div class="col-xs-6">

                  										<div class="form-group">

                  											<div class="input-group ">

															  <input type="radio" value="1" name="supplier_rate_cut[convert_to]" checked>Supplier</input>&nbsp;

															  <input type="radio" value="2" name="supplier_rate_cut[convert_to]" >Manufacurer</input><br>

															  <input type="radio" value="3" name="supplier_rate_cut[convert_to]">Stone supplier </input>&nbsp;

															  <input type="radio" value="4" name="supplier_rate_cut[convert_to]" >Diamond Supplier </input>&nbsp;

															  <input type="radio" value="5" name="supplier_rate_cut[convert_to]" >Approval </input>&nbsp;

            

                  											</div>

                  										</div>

                  									</div>

                  								</div>

                  								

                  								<div class="row conversion_type">

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  												<label type="text">Conversion Type</label>

                  											</div>

                  										</div>

                  									</div>

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

															  <input type="radio" value="1" name="supplier_rate_cut[conversion_type]" checked>Fix</input>&nbsp;

															  <input type="radio" value="2" name="supplier_rate_cut[conversion_type]" >Unfix</input>&nbsp;

            

                  											</div>

                  										</div>

                  									</div>

                  								</div>



												  



                  								<div class="row">

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  												<label type="text">Supplier</label>

                  											</div>

                  										</div>

                  									</div>

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  												<select class="form-control" name=supplier_rate_cut[id_karigar] style="width:100%" id="select_karigar"></select>

            

                  											</div>

                  										</div>

                  									</div>

                  								</div>

												<div class="row po_select">

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  												<label type="text">PO No</label>

                  											</div>

                  										</div>

                  									</div>

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

															  	<span class="">

																	<select class="form-control" id="pur_fin_year_select" style="width:100px;">

																		<?php

																			foreach ($financial_year as $fin_year) { ?>

																				<option value=<?php echo $fin_year['fin_year_code']; ?> <?php echo ($fin_year['fin_status'] == 1 ? 'selected' : '')  ?>><?php echo $fin_year['fin_year_name']; ?></option>

																			<?php }

																		?>

																	</select>

																	<span class="" style="float: right;margin-left: 103px;margin-top: -28px;">

																		<select id="select_po_ref_no" class="form-control" name="supplier_rate_cut[po_id]" style="width:150px;" ></select>

																	</span>

																</span>

            

                  											</div>

                  										</div>

                  									</div>

                  								</div>

                  								

                  								<div class="row op_balance" style="display:none;">

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  												<label type="text">O/p Ref No</label>

                  											</div>

                  										</div>

                  									</div>

                  									<div class="col-xs-4" >

                  										<div class="form-group">

                  											<div class="input-group ">

															  <select id="opening_ref_no" class="form-control" name="supplier_rate_cut[id_smith_company_op_balance]" style="width:100%;" ></select>

            

                  											</div>

                  										</div>

                  									</div>

                  								</div>

												<div class="row">

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">
                  												<label type="text">Select Metal <span style="color:red">*</span></label>
                  											</div>

                  										</div>

                  									</div>

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

															  <select id="select_metal" class="form-control" name="supplier_rate_cut[id_metal]" style="width:100%;"></select>

            

                  											</div>

                  										</div>

                  									</div>

                  								</div>

												<div class="row">

													<div class="col-md-4"> 

														<h4>Bill Amt(Rs)</h4>

													</div>

													<div class="col-md-4"> 

														<h4><span class="availableamtbalance"></span></h4>

														<input type="hidden" id="availableamtbalance" name="billing[availableamtbalance]" value="0">

													</div>

												

												</div>

												<div class="row">

													<div class="col-md-4"> 

														<h4>Bill Pure Wt(Grms)</h4>

													</div>

													<div class="col-md-4"> 

														<h4><span class="availablepurebalance"></span></h4>

														<input type="hidden" id="availablepurebalance" name="billing[availablepurebalance]" value="0">

													</div>                		                    

												</div>

												

                  								 <div class="row">

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  												<label type="text">Select Category <span style="color:red">*</span></label>

                  											</div>

                  										</div>

                  									</div>

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

															  <select id="select_category" class="form-control" name="supplier_rate_cut[id_ret_category]" style="width:100%;"></select>

            

                  											</div>

                  										</div>

                  									</div>

                  								</div>

                  								

                  								<div class="row">

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  												<label type="text">Select Product <span style="color:red">*</span></label>

                  											</div>

                  										</div>

                  									</div>

                  									<div class="col-xs-4">

                  										<div class="form-group">

                  											<div class="input-group ">

															  <select id="select_product" class="form-control" name="supplier_rate_cut[id_product]" style="width:100%;"></select>

            

                  											</div>

                  										</div>

                  									</div>

                  								</div></br>

                  								

                  								<div class="row">

                  								    <div class="col-sm-4">

                  										<div class="form-group">

                  											<div class="input-group ">

                  											    <label>Pure Balance <span class="weight_bln_type"></span></label>

                  												<input class="form-control" id='wt_balance' type="number" placeholder="Weight" readonly>

                  												<input class="form-control" id='wt_balance_type' type="hidden" placeholder="Weight">

														<input type="hidden" id="cashcrdr_weight_type" name="supplier_rate_cut[weight_type]" value="">

                  											</div>

                  										</div>

                  									</div>

            

            

                  									<div class="col-xs-4">

                  									    <label>Amount Balance <span class="amt_bln_type"></span></label>

                  										<input class="form-control" type="number" id="amt_balance" placeholder="Amount" readonly>

														<input type="hidden" id="cashcrdr_amount_type" name="supplier_rate_cut[amount_type]" value="">

                  									</div>

                  								</div>

                  								

                  								

                  							

                  								<!-- Amount to Weight -->

                  								<div class="type1_amt">

                  									<div class="row">

                  										<div class="col-xs-4">

                  											<label type="text">To Pay</label>

                  										</div>

                  										<div class="col-xs-4">

                  											<input class="form-control charges_amount" name=supplier_rate_cut[charges_amount] placeholder="Received Amount" type="number"></input>

            

                  										</div>

                  									</div></br>

                  								</div>

                  								<div class="type2_wt" >

                  									<div class="row">

                  										<div class="col-xs-4">

                  											<label type="text">Pure Weight</label>

                  										</div>

                  										<div class="col-xs-4">

                  											<input class="form-control src_weight" name=supplier_rate_cut[type2_wt] placeholder="Weight" type="number"></input>

                  										</div>

                  									</div></br>

                  								</div>

                  								<div class="row rate_field">

                  									<div class="col-xs-4">

                  										<label type="text">Rate(Incl.GST)</label>

                  									</div>

                  									<div class="col-xs-4">

                  										<input class="form-control src_rate" name=supplier_rate_cut[src_rate] placeholder="Rate" type="number"></input>

            

                  									</div>

                  								</div></br>

                  								<div class="type1_wt" style="display:none">

                  									<div class="row">

                  										<div class="col-xs-4">

                  											<label type="text">Weight</label>

                  										</div>

                  										<div class="col-xs-4">

                  											<input class="form-control type1_wt" placeholder="Weight" id="src_weight" name=supplier_rate_cut[type1_wt] type="number" readonly></input>

                  										</div>

                  									</div></br>

                  								</div>



									



                  								<div class="type2_amt" >

                  									<div class="row">

                  										<div class="col-xs-4">

                  											<label type="text">Taxable Amount</label>

                  										</div>

                  										<div class="col-xs-4">

                  											<input class="form-control taxable_amt" placeholder="Taxable Amount" id="taxable_amt" name=supplier_rate_cut[taxable_amt] type="number" readonly></input>

                  										</div>

                  									</div></br>

                  								</div>



                  								<div class="gst_row" >

                  									<div class="row">

                  										<div class="col-xs-4">

                  											<label type="text">GST <span class="tax_perc">3%</span> </label>

                  										</div>

                  										<div class="col-xs-4">

                  											<input class="form-control tax_amount" placeholder="Tax" id="tax_amount" name=supplier_rate_cut[tax_amount] type="number" readonly></input>

                  											<input id="tax_percentage" name=supplier_rate_cut[tax_percentage] type="hidden"/>

                  											<input id="igst_cost" name=supplier_rate_cut[igst_cost] type="hidden"/>

                  											<input id="sgst_cost" name=supplier_rate_cut[sgst_cost] type="hidden"/>

                  											<input id="cgst_cost" name=supplier_rate_cut[cgst_cost] type="hidden"/>

                  										</div>

                  									</div></br>

                  								</div>

                  								

                  								<div class="" >

                  									<div class="row">

                  										<div class="col-xs-4">

                  											<label type="text">Roundoff</label>

                  										</div>

                  										<div class="col-xs-4">

                  											<input class="form-control round_off" placeholder="Roundoff" id="round_of_amt" name=supplier_rate_cut[round_off] type="number" readonly></input>

                  										</div>

                  									</div></br>
												<div class="row">
													<div class="col-xs-4">
														<label>Apply TDS</label>
													</div>
													<div class="col-xs-1">
														<input type="checkbox" id="is_tds" name="supplier_rate_cut[is_tds]" value="1">
													</div>
													<div class="col-xs-3 tds_input_div" style="display:none;">
														<input class="form-control" id="tds_percent" name="supplier_rate_cut[tds_percent]" type="number" step="0.01" placeholder="TDS %">
														<span id="tds_amount_label" style="font-size: 11px; float: right;">TDS Amt: <span id="tds_amount_display">0.00</span></span>
														<input type="hidden" id="tds_tax_value" name="supplier_rate_cut[tds_tax_value]" value="0">
													</div>
												</div><br/>

                  								</div>





												  <div class="" >

                  									<div class="row">

                  										<div class="col-xs-4">

                  											<label type="text">Net Amount</label>

                  										</div>

                  										<div class="col-xs-4">

                  											<input class="form-control total_bill_amount" placeholder="Amount" id="total_bill_amount" name=supplier_rate_cut[total_bill_amount] type="number" readonly></input>

                  										</div>

                  									</div></br>

                  								</div>

            

                  								<div class="row">

                  									<div class="col-xs-4">

                  										<label type="text">Remark</label>

                  									</div>

                  									<div class="col-xs-6">

                  										<textarea rows="5" cols="10" name=supplier_rate_cut[src_remark] class="form-control" placeholder="Enter Here....." type="text"></textarea rows="30">

            

                  									</div>

                  								</div></br>

      						        </div>

      						        

      						        <div class="col-md-6" >


									    <div class="table-responsive payment_details" style="display:none;">

									        <table id="payment_modes" class="table table-bordered table-striped">

									            <tbody>

									                <tr>

														<td class="text-right">Cash</td>

														<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

														<td>

															<input type="number" class="form-control cash_amount" name="billing[cash_amount]">

														</td>

													</tr>

													<tr>

														<td class="text-right">Net Banking</td>

														<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

														<td>

															<span id="tot_net_banking_amt"></span>

															<a class="btn bg-olive btn-xs pull-right" id="net_bank_modal"  ><b>+</b></a> 

															<input type="hidden"id="net_banking_pay_details" name="billing[net_banking]" value="">

														</td>

													</tr>

													<tr>

														<td class="text-right">Cheque</td>

														<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

														<td>

															<span id="cheque_payment"></span>

															<a class="btn bg-olive btn-xs pull-right" id="cheque_modal"  ><b>+</b></a> 

															<input type="hidden"id="chq_payment" name="billing[chq_pay]" value="">

														</td>

													</tr>

													<tr style="display:none;">

														<td class="text-right">Sales Adjustment</td>

														<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

														<td>

															<span id="tot_sales_amt"></span>

															<a class="btn bg-olive btn-xs pull-right" id="" href="#" data-toggle="modal" data-target="#sales_adjustment_add" ><b>+</b></a> 

															<input type="hidden"id="sales_details" name="billing[sales_details]" value="">

														</td>

													</tr>

													<tr style="display:none;">

														<td class="text-right">Advance Adjustment</td>

														<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

														<td>

															<span id="total_adv_adj"></span>

															<a class="btn bg-olive btn-xs pull-right" id="advance_adj" href="#" ><b>+</b></a> 

															<input type="hidden"id="adv_details" name="billing[adv_details]" value="">

														</td>

													</tr>

													<tr style="font-weight:bold;">

														<td class="text-right">Total Amount</td>

														<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

														<td><span id="total_pay_amount"></span></td>

													</tr>

													<tr>

														<td class="text-right">Balance Amount</td>

														<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

														<td><span id="bal_amount"></span></td>

													</tr>

									            </tbody>

									        </table>

									    </div>

									</div>

      						    </div>

      								

      						<div class="row">

      							<div class="box box-default"><br />

      								<div class="col-xs-offset-5">

      									<button type="button" id="supplier_rate_cut_submit" class="btn btn-primary">Save</button>

      									<button type="button" class="btn btn-default btn-cancel">Cancel</button>



      								</div> <br />

      							</div>

      						</div>

      					</div>

						<div class="overlay" style="display:none">

      						<i class="fa fa-refresh fa-spin"></i>

      					</div>

	                </form>

      			</div>

      		  </div>

      	   </div>

      	</section>



      </div>

      



<div class="modal fade" id="netbanking_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Net Banking Details</h4>

			</div>

			<div class="modal-body"> 

				<div class="box-body chit_details">

					<div class="row"> 

						<div class="col-sm-12 pull-right">

							<button type="button" id="create_net_banking_row" class="btn bg-olive btn-sm pull-right"><i class="fa fa-plus"></i> Add</button>

						</div>

					</div>

					<div class="row">

						<div class="box-body">

						   <div class="table-responsive">

							 <table id="net_banking_details" class="table table-bordered text-center">

								<thead>

								  <tr>

								    <th>Type</th>

								    <th>Bank</th>

								    <th>Payment Date</th>

									<th>Amount</th>

									<th>Ref NO</th>

									<th>Action</th>

								  </tr>

								</thead> 

								<tbody>

									

								</tbody>

								<tfoot>

									<tr>

										<th colspan=2>Total</th>

										<th colspan=2><span class="total_amount"></span></th>

									</tr>

								</tfoot>

							 </table>

						  </div>

						</div> 

					</div> 

				</div> 

			</div>

		  <div class="modal-footer" >

			<a id="save_net_banking" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>
<!-- cheque modal -->
<div class="modal fade" id="cheque-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" style="width:60%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel">Cheque Details</h4>
		</div>
		<div class="modal-body"> 
			<div class="box-body">
				<div class="row"> 
					<div class="col-sm-12 pull-right">
						<button type="button" class="btn bg-olive btn-sm pull-right" id="new_chq"><i class="fa fa-user-plus"></i>ADD</button>
						<p class="error "> <span id="chqPayAlert"></span></p>
					</div>
				</div>
				<p></p>
			   <div class="table-responsive">
				 <table id="chq_details" class="table table-bordered">
					<thead>
						<tr> 
							<th>Cheque Date</th>
							<th>Bank</th> 
							<th>Branch</th>  
							<input type="hidden" name="payee_name" id="payee_name" value="">
							<th>Payee Name</th>  
							<th>Cheque No</th>  
							<th>IFSC Code</th>  
							<th>Amount</th>  
							<th>Action</th> 
						</tr>											
					</thead> 
					<tbody>
					</tbody>
					<tfoot>
						<tr>
							<td>Total</td><td></td><td></td><td></td><td></td><td><span class="chq_total_amount"></span></td><td></td>
						</tr>
					</tfoot>
				 </table>
			  </div>
			</div>  
		</div>
	  <div class="modal-footer">
		<a href="#" id="save_issue_chq" class="btn btn-success">Save</a>
		<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
	  </div>
	</div>
</div>
</div>

<script type="text/javascript">
    var bank_details = [];
    var chq_payment = [];
    $(document).ready(function() {
        get_bank_acc_details();
    });
</script>
