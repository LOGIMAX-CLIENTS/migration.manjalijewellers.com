<!-- Content Wrapper. Contains page content -->

<style>

	.remove-btn{

		margin-top: -168px;

	    margin-left: -38px;

	    background-color: #e51712 !important;

	    border: none;

	    color: white !important;

	}

	.summary_lbl{

		font-weight:bold;

	}

	.stickyBlk {

	    margin: 0 auto;

	    top: 0;

	    width: 100%;

	    z-index: 999;

	    background: #fff;

	}

	.custom-label{

		font-weight: 600 !important;

	    letter-spacing: 0.5px !important;

	    text-transform: uppercase !important;

	}

	

    .form-group {

        margin-bottom: 1px;

    }

    

    

    

    *[tabindex]:focus {

    outline: 1px black solid;

    }

    

    .billType{

        padding : 3px !important;

        margin : 0px !important;

        height: auto;

    }

    

    #payment_modes td{

        padding : 1px 5px !important;

    }

   #payment_modes input[type=text],#payment_modes input[type=number], #payment_modes button {

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

      .checkbox-wrapper-31:hover .check {
    stroke-dashoffset: 0;
  }

  .checkbox-wrapper-31 {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 40px;
  }
  .checkbox-wrapper-31 .background {
    fill: #ccc;
    transition: ease all 0.6s;
    -webkit-transition: ease all 0.6s;
  }
  .checkbox-wrapper-31 .stroke {
    fill: none;
    stroke: #fff;
    stroke-miterlimit: 10;
    stroke-width: 2px;
    stroke-dashoffset: 100;
    stroke-dasharray: 100;
    transition: ease all 0.6s;
    -webkit-transition: ease all 0.6s;
  }
  .checkbox-wrapper-31 .check {
    fill: none;
    stroke: #fff;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2px;
    stroke-dashoffset: 22;
    stroke-dasharray: 22;
    transition: ease all 0.6s;
    -webkit-transition: ease all 0.6s;
  }
  .checkbox-wrapper-31 input[type=checkbox] {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    margin: 0;
    opacity: 0;
    -appearance: none;
    -webkit-appearance: none;
  }
  .checkbox-wrapper-31 input[type=checkbox]:hover {
    cursor: pointer;
  }
  .checkbox-wrapper-31 input[type=checkbox]:checked + svg .background {
    fill: #457dbeff;
  }
  .checkbox-wrapper-31 input[type=checkbox]:checked + svg .stroke {
    stroke-dashoffset: 0;
  }
  .checkbox-wrapper-31 input[type=checkbox]:checked + svg .check {
    stroke-dashoffset: 0;
  }
  #payment_method{
	display: none;
    flex-wrap: wrap;
    gap: 3rem;
    justify-content: center;
    flex-direction: column;
    margin-bottom: 10px;
    position: absolute;
    top: 5rem;
  }
  .checkbox-wrapper-32 {
    position: relative;
    display: inline-block;
    width: 20px;
    height: 30px;
  }
  .checkbox-wrapper-32 input[type=checkbox]:hover {
    cursor: pointer;
  }
  .checkbox-wrapper-32 input[type=checkbox]:checked + svg .background {
    fill: #6cbe45;
  }
  .checkbox-wrapper-32 input[type=checkbox]:checked + svg .stroke {
    stroke-dashoffset: 0;
  }
  .checkbox-wrapper-32 input[type=checkbox]:checked + svg .check {
    stroke-dashoffset: 0;
  }
  .checkbox-wrapper-32 .background {
    fill: #ccc;
    transition: ease all 0.6s;
    -webkit-transition: ease all 0.6s;
  }
  .checkbox-wrapper-32 .stroke {
    fill: none;
    stroke: #fff;
    stroke-miterlimit: 10;
    stroke-width: 2px;
    stroke-dashoffset: 100;
    stroke-dasharray: 100;
    transition: ease all 0.6s;
    -webkit-transition: ease all 0.6s;
  }
  .checkbox-wrapper-32 .check {
    fill: none;
    stroke: #fff;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 2px;
    stroke-dashoffset: 22;
    stroke-dasharray: 22;
    transition: ease all 0.6s;
    -webkit-transition: ease all 0.6s;
  }
  .checkbox-wrapper-32 input[type=checkbox] {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    margin: 0;
    opacity: 0;
    -appearance: none;
    -webkit-appearance: none;
  }

</style>

  <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <!--<section class="content-header">

          <h1>

        	Billing

            <small>Customer Billing</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Billings</a></li>

            <li class="active">Billing</li>

          </ol>

        </section>-->

        <!-- Main content -->

    <section class="content product">

		<!-- Default box -->

		<div class="box box-primary">

           

            <div class="box-body">

			<?php 

            	if($this->session->flashdata('chit_alert'))

            	 {

            		$message = $this->session->flashdata('chit_alert');

            ?>

				<div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

					<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

					<?php echo $message['message']; ?>

				</div>

            <?php } ?> 

         <!-- form container -->

          		<div class="row">

             	<!-- form -->

				<form id="bill_pay">

					<div class="col-md-12"> 	

                		<div class="box box-default ">
	           		        
							<div class="box-body" align="center">

					            <label class="pull-left">Bill Type <span class="error">*</span></label>

				            	    <div class="row">

   					            		<div class="col-sm-3">

	   							            <a class="btn btn-app btn-flat billType margin bg-green">

    							                <input type="radio" id="bill_type_sales" name="billing[bill_type]" value="1" checked> <label for="bill_type_sales" class="custom-label"> BILL </label>

    							            </a>

    							            <a class="btn btn-app btn-flat billType margin bg-teal"> 

    							                <input type="radio" id="bill_type_advance" name="billing[bill_type]" value="2" > <label for="bill_type_advance" class="custom-label"> RECEIPT</label>

    							            </a>

    							        </div>

    							            

                    					<div class="col-sm-3"> 

                    						<div class="row">				    	

												<div class="col-sm-4">

													<label>Karigar</label>

													<input type="hidden" id="id_karigar" value="">

													<input type="hidden" id="pay_id" value="" name="billing[pay_id]">

												</div>

												<div class="col-sm-8">

													<div class="form-group" > 

														<div class="input-group" > 

															<select class="form-control" id="select_karigar" name="billing[id_karigar]" style="width:100%;"></select>

														</div>

													</div>

												</div>

                    						</div>

                    					</div>

										<div class="col-sm-9"> 

											<h4><span class="supplierblc"></span></h4>

										</div> 

					            	</div>

							</div>
							<div class="checkbox-wrapper-31" id="payment_method">
								<input id="opening_checkbox" name="opening_checkbox" type="checkbox"/>
								<svg viewBox="0 0 35.6 35.6">
									<circle class="background" cx="17.8" cy="17.8" r="17.8"></circle>
									<circle class="stroke" cx="17.8" cy="17.8" r="14.37"></circle>
									<polyline class="check" points="11.78 18.12 15.55 22.23 25.17 12.87"></polyline>
								</svg>
								<input type="hidden" name="opening_checked" id="opening_checked" value="0"/>
								<label>OPENING</label>
							</div>
							
							<div class="box-body">

								<!-- Search Block	 -->

								<div class="row sale_details">

									<div class="col-md-12">

										<div class="row">

											<div class="col-sm-12">

												<div class="box box-default total_summary_details">

													<div class="box-body">

														<div class="row" id="payment_details_r6">

															<div class="col-sm-6">

															<div class="table-responsive">

																<div id="pending_bills_container" style="overflow: scroll;">

																</div>

    											  			</div>

        												</div> 

														<div class="col-md-6">

															<div class="table-responsive">
																	
																<table id="payment_modes" class="table table-bordered table-striped">

																	<tbody>

																		<tr>

																			<td class="text-right"><b class="custom-label">Net Amount</b></td>

																			<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>

																			<td> 

																				<input type="number" class="form-control balance_amount text-right" id="balance_amount" name="billing[balance_amount]" value="" required readonly>

																			</td>

																		</tr>

																		<!-- <tr>

																			<td class="text-right"><b class="custom-label">Balance Pure Wt</b></td>

																			<td class="text-right">(Grm)</td>

																			<td><input type="number" class="form-control" id="bal_purewt" value="" readonly></td>

																		</tr> -->

																		<tr style="display:none;">

																			<td class="text-right"><b class="custom-label">Received</b></td>

																			<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>

																			<td> 

																				<input type="number" class="form-control receive_amount text-right" id="receive_amount_net_amount" name="billing[tot_amt_received]" value="<?php echo set_value('billing[tot_amt_received]',isset($billing['tot_amt_received'])?$billing['pan_no']:0); ?>" >

																			</td>

																		</tr>

																		<tr>

																			<td class="text-right">Cash</td>

																			<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

																			<td>

																				<input type="number" class="form-control cash_amount text-right" name="billing[cash_amount]">

																			</td>

																		</tr>

																		<tr>

																			<td class="text-right">Net Banking</td>

																			<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

																			<td>

																				<a class="btn bg-olive btn-xs" id="net_bank_modal"  ><b>+</b></a> 
																				
																				<span id="tot_net_banking_amt" class="pull-right" style="padding-right: 6px;"></span>

																				<input type="hidden"id="net_banking_pay_details" name="billing[net_banking]" value="">

																			</td>

																		</tr>


																		<tr>

																			<td class="text-right">Cheque</td>

																			<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

																			<td>

																				<a class="btn bg-olive btn-xs" id="cheque_modal"><b>+</b></a> 
																				
																				<span id="cheque_payment" class="pull-right" style="padding-right: 6px;"></span>
																				
																				<input type="hidden"id="chq_payment" name="billing[chq_pay]" value="">

																			</td>

																		</tr>

																																				<tr>

																			<td class="text-right">Debit Note Adj.</td>

																			<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

																			<td>

																				<a class="btn bg-olive btn-xs" id="debit_note_modal_btn"><b>+</b></a> 
																				
																				<span id="tot_debit_adj_amt" class="pull-right" style="padding-right: 6px;"></span>

																				<input type="hidden" id="debit_adjustments_data" name="billing[debit_adjustments]" value="">
								<input type="hidden" id="purchase_return_adjustments_data" name="billing[purchase_return_adjustments]" value="">

																			</td>

																		</tr>



																		<tr style="display:none;">

																			<td class="text-right">Sales Adjustment</td>

																			<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

																			<td>

																				<a class="btn bg-olive btn-xs pull-right" id="" href="#" data-toggle="modal" data-target="#sales_adjustment_add" ><b>+</b></a> 
																				
																				<span id="tot_sales_amt"></span>

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

																			<td><span id="total_pay_amount" class="pull-right" style="padding-right: 6px;"></span></td>
																			<input type="hidden" name="billing[total_pay_amounts]" id="billing[total_pay_amounts]" value="0">

																		</tr>

																		<tr>

																			<td class="text-right">Balance Amount</td>

																			<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

																			<td><span id="bal_amount" class="pull-right" style="padding-right: 6px;"></span></td>

																		</tr>

																	</tbody>

																</table>

																<label>Remark</label>

																<textarea class="form-control" id="remark" name="billing[remark]" <?php echo set_value('billing[remark]',isset($billing['remark'])?$billing['remark']:NULL); ?> rows="5" cols="500"> </textarea>

															</div>

        												</div>
													</div>

        										</div>

        									</div>

        								</div>

									</div>

  								</div> 

        					</div> 

                		</div>

                    </div>

					<div class="row">

						<div class="col-sm-12" align="center">

							<button type="button" id="pay_submit" class="btn btn-primary" >Save</button> 

							<button type="button" class="btn btn-default btn-cancel">Cancel</button>

						</div>

					</div>

				 </form>	<!--/ Col --> 

			</div>	 <!--/ row -->

			   <p class="help-block"> </p>  

	            </div>  

	          <?php echo form_close();?>

	            <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

	             <!-- /form -->

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

										<!--<tr>

											<td>

    											<select class="form-control nb_type">

    											    <option value="RTGS">RTGS</option>

    											    <option value="NEFT">NEFT</option>

    											</select>

											</td>

											<td><input type="number" class="form-control pay_amount" type="number"/></td>

											<td><input type="number" class="form-control ref_no" type="text"/></td>

											<td><a href="#" onclick="remove_net_banking_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

										</tr>-->

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







<div class="modal fade" id="sales_adjustment_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Sales Details</h4>

			</div>

			<div class="modal-body"> 

				<div class="box-body chit_details">

					<div class="row"> 

						<div class="col-sm-12 pull-right">

							<button type="button" id="create_sales_details_row" class="btn bg-olive btn-sm pull-right"><i class="fa fa-plus"></i> Add</button>

						</div>

					</div>

					<div class="row">

						<div class="box-body">

						   <div class="table-responsive">

							 <table id="bill_details" class="table table-bordered text-center">

								<thead>

								  <tr>

									<th>Bill No</th>

									<th>Amount</th>

									<th>Action</th>

								  </tr>

								</thead> 

								<tbody>

										<tr>

											<td><input type="text" class="form-control bill_no"/><input type="hidden" class="form-control bill_id"/></td>

											<td><input type="number" class="form-control payment_amount" type="text" readonly/></td>

											<td><a href="#" onclick="remove_net_banking_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

										</tr>

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

			<a id="save_sales_details" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

 

 

 

 <div class="modal fade" id="advance_adjustment_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Advance Details</h4>

			</div>

			<div class="modal-body"> 

				<div class="box-body">

					<div class="row">

						<div class="box-body">

						   <div class="table-responsive">

							 <table id="advance_adj_details" class="table table-bordered text-center">

								<thead>

								  <tr>

									<th>Receipt Amount</th>

									<th>Adjusted Amount</th>

								  </tr>

								</thead>

								<tbody>

								    <tr>

								        <td><input type="number" class="form-control total_amount" readonly></td>

								        <td><input type="number" class="form-control adjusted_amount">

								        <input type="hidden" class="id_wallet">

								        </td>

								    </tr>

								</tbody>

							 </table>

						  </div>

						</div> 

					</div> 

				</div> 

			</div>

		  <div class="modal-footer" >

			<a id="save_adv_adj_details" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>


<!-- cheque-->


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

						<p class="error "><span id="chqPayAlert"></span></p>

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

						<!-- <tr> 

							<td><input id="cheque_datetime" data-date-format="dd-mm-yyyy " class="cheque_date" name="cheque_details[cheque_date][]" type="text"  placeholder="Cheque Date" /></td>

							<td><input name="cheque_details[bank_name][]" type="text"  class="bank_name"></td>

							<td><input name="cheque_details[bank_branch][]" type="text" class="bank_branch"></td>

							<td><input name="cheque_details[payee_name][]" type="text" class="payee_name" id="payee_names"></td>

							<td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]"/></td> 

							<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]"/></td> 

							<td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]"/></td> 



							<td><a href="#" onclick="removeCC_row($(this).closest('tr')) ;" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

						</tr>  -->

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

<!-- cheque-->

 <!-- Debit Note Adjustment Modal -->
<div class="modal fade" id="debit_note_modal" tabindex="-1" role="dialog" aria-labelledby="debitNoteModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="debitNoteModalLabel">Debit Note Adjustment</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="table-responsive">
						<table id="debit_entries_table" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
									<th width="40">Select</th>
									<th>Source</th>
									<th>Ref No</th>
									<th>Date</th>
									<th class="text-right">Total Amount</th>
									<th class="text-right">Spent Amount</th>
									<th class="text-right">Balance Amount</th>
									<th class="text-right">Adjust Amount</th>
									<th>Narration</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="7">Total</th>
									<th class="text-right"><span class="debit_total_amount">0.00</span></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a id="save_debit_adjustments" class="btn btn-success">Save</a>
				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- /Debit Note Adjustment Modal -->