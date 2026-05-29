<!-- Content Wrapper. Contains page content -->
    <head>
        <style>
            .vertical-line {
                width: 1px; /* Width of the line */
                background-color: #ccc; /* Color of the line */
            }
            
            div.accounts{
                overflow-x: auto;
                overflow-y: scroll;
            }
            
            .grid-container {
              display: grid;grid-template-columns: repeat(8, 20fr);height:130px;grid-column-gap: 5px;grid-row-gap: 5px;padding: 10px;border-radius: 1px;
            }
            .grid-item {
              background-color: #gray;height:120px;weight:120px;font-size: 30px;text-align: center;border-radius:8px;
            }
            
             .swap {
            width: 100px;
            height: 100px;
            border: 1px solid #000;
            margin: 10px;
            display: inline-block;
        }
        </style>
        
    </head>
    
    <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
              <h1>
                2 Booking
                <small><span class="badge bg-green" id="member_account_count"></span></small>
              </h1><span id="total" class="badge bg-green"></span>
              <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="#">Advance Booking</a></li>
                <li class="active">Reserve Booking</li>
              </ol>
            </section>
    
            <!-- Main content starts -->
            <section class="content">
                

                      <!-- MAIN row starts-->
                      <div class="row">
                            <!-- MAIN col starts-->
                            <div class="col-xs-12">
                                <!--main box starts-->
                                <div class="box box-primary">
                                    <div class="box-body">
                                        
                                    <form id="adv_payment_form">  
                                        <div class="row col-md-12"><!-- Head Part starts-->
                                            
                                            <!-- Branch -->
                                            <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?> 			
                                            <div class="col-md-3" id="branch_div">
                                                <div class="form-group" >
                                                    <label>Select Branch </label>
                                                    <select id="branch_select" class="form-control" style="width:100%;" ></select>
                                                    <input id="id_branch" name="gen[id_branch]"  type="hidden" value=""/>
                                                </div>
                                            </div>
                                            <?php }else{?>
                                            <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
                                            <input type="hidden" id="login_branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
                                            <?php }?>
                                            
                                            <!--Customer -->
                                            <div class="col-md-3">
                                        		<div class="form-group">
                                        			<label for="" >Customer Mobile</label>
                    								<input type="text" class="form-control mobile_number" name="mobile_number" placeholder="Enter Mobile Number" id="mobile_number" value="<?php echo ($this->session->userdata('cus_mobile')!='' ? $this->session->userdata('cus_mobile').'-'.$this->session->userdata('cus_name') :''); ?>" style="width: 99%;">
                                        		</div>	
                            	            </div>
                                            
                                            <!-- Metal By plans created-->
                                            <div class="col-md-3">
                    						 	<div class="form-group">
                    		                       <label for="metal" >Metal</label>
                    		                       <input type="hidden" id="plan_val" name="plan[plan_val]" />
                    		                       	 <select id="plan"  class="form-control" required="true">
                    		                       	 </select>
                    		                  		 <p class="help-block"></p>                       	
                    		                    </div>
                    		                </div> 
                    		                
                    		                <!-- Employee -->
                                           <!-- <div class="col-md-3">
                                                <div class="form-group" >
                                                    <label>Employee </label>
                                                    <select id="employee_select" class="form-control" style="width:100%;" ></select>
                                                    <input id="id_employee" name="gen[id_employee]"  type="hidden" />
                                                </div>
                                            </div>-->
                                            
    
                    		                
                    		                
                    		                <!-- hidden fields -->
                                            <div class="form-group">
                                    			<input type="hidden" name="id_customer" id="id_customer" value=""/>
                                    			<input type="hidden" name="cus_name" id="cus_name" value=""/>
                                    			<input type="hidden" name="phone" id="phone" value=""/>
                                    			<input type="hidden" name="email" id="email" value=""/>
                                    			<input type="hidden" name="source_type" id="source_type" value="ADMIN"/>

                                                  <input type="hidden" id="plan_minimum" name="plan_minimum" />
                                                  <input type="hidden" id="plan_maximum" name="plan_maximum" />
                                                  <input type="hidden" name="booking_rate" />
                                                  <input type="hidden" name="id_adv_booking" />
                                                  
                                                  <input type="hidden" name="advance_paid" />
                                                  <input type="hidden" name="advance_value" />
                                                  <input type="hidden"  name="balance_adv_amt" />
                                                  <input type="hidden" name="balance_amt" />
                                                  <input type="hidden" name="balance_paid" />
                                                  
                                                  <input type="hidden" name="booking_amount" />
                                                  <input type="hidden"  name="booking_date" />
                                                  <input type="hidden"  name="booking_name" />
                                                  <input type="hidden"  name="booking_number" />
                                                  <input type="hidden"  name="booking_status" />
                                                  <input type="hidden"  name="eligible_on" />
                                                  <input type="hidden" name="max_payable_amount" />
                                                  <input type="hidden"  name="metal" />
                                                  <input type="hidden"  name="min_payable_amount" />
                                                  <input type="hidden"  name="online_advance_amt" />
                                                  <input type="hidden"  name="status" />
                                                  <input type="hidden"  name="total_advance_amt" />
                                                  <input type="hidden"  name="total_paid_amount" />
                                            </div>
                                            
                                        </div><!-- Head Part ends -->
                                      
                                        
                                        <!-- load accounts with new booking starts-->
                                        <div  class="row col-md-12 accounts" style="display:none;">
                                            <hr>
                                            <div class="grid-item col-md-1" id="add_booking" style="color:#ed0972;font-weight: 700;font-size: 15px;display: grid;grid-template-columns: repeat(1, 1fr);height:180px;grid-column-gap: 5px;grid-row-gap: 5px;padding: 10px;border-radius: 1px;">
                                                <span style="font-size: 100px;color: #8e1ba3;font-weight: 100;">+</span>
                                                <span style="font-weight: 600;font-size: 14px;color: #8e1ba3;">New Booking</span>
                                            </div>
                                            <div id="list_accounts"></div>
                                        </div>
                                        <!-- load accounts with new booking ends-->
                                        
                                        <br/><br/>
                                        <!--load booking and payment form -->
                                        
                                        <div class="row col-md-12"><hr></div>  
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div id="booking_form"  style="display:none;">
                                                            <span id="plan_min_max" style="font-weight: 400;"></span> </br>
                                                          <div class="row">
                                                              
                                                                  
                                                              
                                                              <div class="col-md-5" id="book_amt">
                                                                  <label for="payment_amount" >Booking Amount </label>
                                                                  
                                                                  <input type="number" class="form-control received_amt"   id="booking_amount" />
                                                              </div>
                                                              <div class="col-md-2" style="text-align:center;" >
                                                                  <label for="swap"> </label><br/>
                                                                  <button type="button" class="btn btn-primary" id="swap"><i class="fa fa-arrows-h"></i></button>
                                                              </div>
                                                              <div class="col-md-5" id="book_wt">
                                                                  <label for="payment_amount" >Booking Weight </label>
                                                                  
                                                                  <input type="text" class="form-control received_amt"   id="booking_weight" />
                                                              </div>
                                                          </div>
                                                         
                                                          <div class="row">
                                                              <div class="col-md-5" class="pull-right" style="float:left;">
                                                                  <label for="payment_amount" >Booking Rate </label>
                                                                  <input type="any" class="form-control"   id="booking_rate"  readonly/>
                                                              </div>
                                                              <div class="col-md-4" class="pull-right" style="float:right;">
                                                                   <label for="payment_amount" ></label>
                                                                 <input type="button" class="form-control btn btn-success" style="display:none;"  id="create_booking"  value="Create Booking"/>
                                                              </div>
                                                              
                                                          </div>
                                                          <span id="create_acc_content" style="color: red;font-weight: 800;"></span> </br>
                                                         
                                                         
   
    
                                                        </div><br/><br/>
                                            </div>
                                        </div>  

                                        <!--<div class="row">
                                            <div class="col-md-6">
                                                <div id="booking_form"  style="display:none;">
                                                            <span id="plan_min_max" style="font-weight: 400;"></span> </br>
                                                          <div class="row">
                                                              
                                                                

                                                              <div class="col-md-5" id="book_amt">
                                                                  <label for="payment_amount" >Booking Amount </label>
                                                                  
                                                                  <input type="number" class="form-control received_amt"  />
                                                              </div>
                                                              
                                                              <div class="col-md-5" id="book_wt">
                                                                  <label for="payment_amount" >Booking Weight </label>
                                                                  
                                                                  <input type="any" class="form-control received_amt"   id="booking_weight" />
                                                              </div>
                                                         
                                                                  <button type="button" class="btn btn-primary col-md-2" id="swapDivs" ><i class="fa fa-arrows-h"></i></button>
                                                            
                                                          </div>
                                                         
                                                          <div class="row">
                                                              <div class="col-md-5" class="pull-right" style="float:left;">
                                                                  <label for="payment_amount" >Booking Rate </label>
                                                                  <input type="any" class="form-control"   id="booking_rate"  readonly/>
                                                              </div>
                                                              <div class="col-md-4" class="pull-right" style="float:right;">
                                                                   <label for="payment_amount" ></label>
                                                                 <input type="button" class="form-control btn btn-success" style="display:none;"  id="create_booking"  value="Create Booking"/>
                                                              </div>
                                                              
                                                          </div>
                                                          <span id="create_acc_content" style="color: red;font-weight: 800;"></span> </br>
                                                         
                                                         
   
    
                                                        </div><br/><br/>
                                            </div>
                                        </div>-->
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                                <div id="payment_form"  style="display:none;">
                                                    
                                                    <div class="col-md-6" style="border-right:1px solid black;display:none;" id="make_pay_block" >         
                                                    <p id="pay_error" style="color: red;font-weight: 800;"></p>
                                                        <div class="row">
                                                          <div class="col-md-6">
                                                              
    
    					                                    <label for="payment_amount" >Payment Amount </label>
                                                              
                                                             <input type="number" class="form-control received_amt"   id="payable"  name="payable"/>
                                                              <span id="min_payable_amount_span"></span> - 
                                                              <span id="max_payable_amount_span"></span>
                                                            
    
    					  
                                                          </div>
                                                         
                                                          <div class="col-md-4" style="margin-top:25px;float:right;margin-right:30px;">
                                                              	<div class="input-group input-group-sm" style="float:right;">
    
                                                          			<input type="button" class="form-control btn btn-primary"   id="proceed"  value="Proceed"/>
                                
                                                          		</div>
                                                          </div>
                                                        </div>
                                                        <br/>
                                                        
                                                        <div class="row">
                                                          
                                                              
                                                                	<div class="box box-info payment_blk">
            
            								                            <div class="box-header with-border">
            
                                                								  <h3 class="box-title">Make Payment</h3>
                                                
                                                								  
            
            								                            </div>
            
            								                            <div class="box-body">
            
                                        									<div class="row">
                                        
                                        										<div class="col-sm-11">
                                        
                                        											<div class="box-body">
                                        
                                        											   <div class="table-responsive">
                                        
                                        												 <table id="payment_modes" class="table table-bordered table-striped" style="pointer-events: none;opacity: 0.4;">
                                        
                                        													<thead>
                                        
                                        													</thead> 
                                        
                                        													<tbody>
                                        
                                        													    <tr>
                                        
                                        															<td class="text-right"><b class="custom-label">Payment Amount</b><span class="error">*</span></td>
                                        
                                        															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
                                        
                                        															<td> 
                                        
                                        																<input type="text" class="form-control"  name="amount" id="payment_amt" readonly="true" />
                                        
                                        															</td>
                                        
                                        														</tr>
                                        
                                        														
                                        
                                        														<?php 
                                        
                                        														$modes = $this->payment_model->get_payModes();
                                        
                                        														if(sizeof($modes)>0){
                                        
                                        														foreach($modes as $mode){
                                                                                                    
                                        														   
                                                                                                    $cash = ($mode['short_code'] == "CSH" ? '<div id="cash_div"><input class="form-control" id="make_pay_cash" name="cus_pay_mode[cash_payment]" type="text" placeholder="Enter Amount" value=""/></div>' : '');
                                        														
                                                                                                    $card = ($mode['short_code'] == "CC"? '<div id="card_div"><a class="btn bg-olive btn-xs pull-right" id="card_detail_modal" href="#" data-toggle="modal" data-target="#card-detail-modal" ><b>+</b></a></div> ' : '');
                                        														
                                                                                                    $cheque = ($mode['short_code'] == "CHQ"  ? '<div id="cheque_div"><a class="btn bg-olive btn-xs pull-right" id="cheque_modal" href="#" data-toggle="modal" data-target="#cheque-detail-modal" ><b>+</b></a></div> ' : '');
                                        															
                                                                                                    
                                                                                                    $net_banking = ($mode['short_code'] == "NB"  ? '<div id="nb_div"><a id="netbankmodal" class="btn bg-olive btn-xs pull-right"  href="#" data-toggle="modal" data-target="#net_banking_modal" ><b>+</b></a></div> ' : '');
                                        														    
                                        														   
                                                                                                    // $voucher = ($mode['short_code'] == "VCH"  ? '<div id="VCH_div"><a class="btn bg-olive btn-xs pull-right" id="vch_modal" href="#" data-toggle="modal" data-target="#vch-detail-modal" ><b>+</b></a></div> ' : '');
                                        
                                        															
                                        														
                                        														?>
                                                                                                <?php if($mode['short_code'] != "VCH")
                                                                                                { ?>
                                                                                                
                                        														<tr>
                                        
                                        															<td class="text-right"><?php echo $mode['mode_name']; ?>
                                        
                                        															</td>
                                        
                                        															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
                                        
                                        															<td class="mode_<?php echo $mode['short_code']; ?>">
                                        
                                        																<span class="<?php echo $mode['short_code'];?>"></span>
                                        
                                        															<input type="hidden" id="card_payment" name="cus_pay_mode[card_pay]" value="">
                                        
                                        															<input type="hidden" id="chq_payment" name="cus_pay_mode[chq_pay]" value="">
                                        
                                        															<input type="hidden" id="nb_payment" name="cus_pay_mode[net_bank_pay]" value="">
                                        														
                                        															<input type="hidden" id="vch_payment" name="cus_pay_mode[vch_pay]" value="">
                                        															
                                        
                                        															<?php echo $cash; ?> 
                                        
                                        															<?php echo $card; ?> 
                                        
                                        															<?php echo $cheque; ?> 
                                        
                                        															<?php echo $net_banking; ?> 
                                        															
                                        															<?php echo $voucher; ?> 
                                        															
                                        
                                        															</td> 
                                        
                                        														</tr>
                                        														<?php } ?>
                                        
                                        														<?php }}?>
                                        
                                        														<tr>
                                        
                                        															<td class="text-right">Advance Adj</td>
                                        
                                        															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
                                        
                                        															<td>
                                        
                                        																<span id="tot_adv_adj"></span>
                                        
                                        																
                                                                                                        <div id="adv_adj_div">
                                        			                                                        <a class="btn bg-olive btn-xs pull-right" id="adv_adj_modal" onclick="get_advance_details()" href="#" data-toggle="modal"><b>+</b></a> 
                                        		                                                        </div>
                                        																<input type="hidden" id="adv_adj_details" name="cus_pay_mode[adv_adj]" value="">
                                        
                                        																<input type="hidden" id="ord_adv_adj_details" name="cus_pay_mode[order_adv_adj]" value="">
                                        
                                        																<input type='hidden' id='advance_muliple_receipt' name="adv[advance_muliple_receipt][]" value="">
                                        
                                        																<input type="hidden" id="excess_adv_amt" name="adv[excess_adv_amt][]" value="">
                                        
                                        															</td>
                                        
                                        														</tr>
                                        
                                        													</tbody>
                                        
                                        													<tfoot>
                                        
                                        														<tr>
                                        
                                        															<th class="text-right custom-label">Total</th>
                                        
                                        															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
                                        
                                        															<th class="sum_of_amt"></th>
                                        
                                        														</tr>
                                        
                                        														<tr>
                                        
                                        															<th class="text-right custom-label">Balance</th>
                                        
                                        															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
                                        
                                        															<th class="bal_amount"></th>
                                        
                                        														</tr>
                                        
                                        													</tfoot>
                                        
                                        												 </table>
                                        
                                        											  </div>
                                        
                                        											</div> 
                                        
                                        										</div> 
                                        
                                        									</div>
                                        
                                        								</div>
            
            							                            </div>
                                                          
                                                      </div>
                                                        <div class="row">
                                                          <div class="col-md-6"  style="float:right;">
                                                              
    					                                    	<button type="button" class="btn btn-default btn-warning" id="pay_cancel">Cancel</button>
                                                                <input type="button" class="btn btn-default btn-success"   id="pay_submit"  value="Save"/>
    					  
                                                          </div>
                                                      </div>
                                                    </div> 
                                                    
                                                    <div class="row">
                                                        
                                                        <div class="col-md-6"  id="payment-detail-box" style="display:none;">
                                                            
                                                            <!--<div  class="box box-solid box-default" ></div>-->
                                                        </div>
                                                        
                                                        <div class="col-md-6"  id="ledger-box" style="display:none;">
                                                            
                                                            
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                                
                                            </div>
                                        </div>    
                                        
                                    </form>
                                    </div><!--box body-->

                                    </div>
                                </div>
                                
                            </div>
                       
             </section>
    </div>
    
  <!-- Card Details -->

<div class="modal fade" id="card-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Card Details</h4>

			</div>

			<div class="modal-body"> 

				<div class="box-body">

					<div class="row"> 

						<div class="col-sm-12 pull-right">

							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_card"><i class="fa fa-user-plus"></i>ADD</button>

							<p class="error "><span id="cardPayAlert"></span></p>

						</div>

					</div>

					<p></p>

				   <div class="table-responsive">

					 <table id="card_details" class="table table-bordered">

						<thead>

							<tr> 

								<th>Card Name</th>

								<th>Type</th> 

								<th>Device</th> 

								<th>Card No</th>  

								<th>Amount</th>  

								<th>Approval No</th> 

								<th>Action</th> 

							</tr>											

						</thead> 

						<tbody>

					
						</tbody>

						<tfoot>

							<tr>

								<th  colspan=3>Total</th>

								<th colspan=2>

									<span class="cc_total_amount"></span>

									<span class="cc_total_amt" style="display: none;"></span>

									<span class="dc_total_amt" style="display: none;"></span>

								</th>

							</tr>

						</tfoot>

					 </table>

				  </div>

				</div>  

			</div>

		  <div class="modal-footer">

			<a href="#" id="add_newcc" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div> 

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

								<th>Cheque No</th>  

								<th>IFSC Code</th>  

								<th>Amount</th>  

								<th>Action</th> 

							</tr>											

						</thead> 

						<tbody>

							<tr> 

								<td><input id="cheque_datetime" data-date-format="dd-mm-yyyy" class="cheque_date" name="cheque_details[cheque_date][]" type="text" required="true" placeholder="Cheque Date" /></td>
								<!-- <td><input class="form-control datemask date nb_date" data-date-format="dd-mm-yyyy" name="cheque_details[cheque_date][]" type="text" placeholder="Cheque Date" style="width: 100px;" /></td> -->
								<td><input name="cheque_details[bank_name][]" type="text" required="true" class="bank_name" onkeypress="return /[a-zA-Z]/i.test(event.key)"></td>

								<td><input name="cheque_details[bank_branch][]" type="text" required="true" class="bank_branch" onkeypress="return /[a-zA-Z]/i.test(event.key)" ></td>

								<td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]"/></td> 

								<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"  /></td> 

								<td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]"/></td> 

								<td><a href="#" onclick="removeChq_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  

							</tr> 

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

			<a href="#" id="add_newchq" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- cheque-->

<!-- Net Banking-->

<div class="modal fade" id="net_banking_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Net Banking Details</h4>

			</div>

			<div class="modal-body"> 

				<div class="box-body">

					<div class="row"> 

						<div class="col-sm-12 pull-right">

							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_net_bank"><i class="fa fa-user-plus"></i>ADD</button>

							<p class="error "><span id="NetBankAlert"></span></p>

						</div>

					</div>

					<p></p>

				   <div class="table-responsive">

					 <table id="net_bank_details" class="table table-bordered">

						<thead>

							<tr> 

								<th>Type</th> 
                                <th>Bank/Device</th>
								<!--<th class="upi_type">Bank</th>-->
								
								<!--	<th class="device" style="display:none;">Device</th>-->
								
								<th >Payment Date</th>

							

								<th>Ref No</th>  

								<th>Amount</th>  

								<th>Action</th> 

							</tr>											

						</thead> 

						<tbody id="net_bankdetails">

							<!--<tr id='0'> 

								
                                <td><select name="nb_details[nb_type][]" class="nb_type0" id="nb_type_0"><option value="">Select Type</option><option value=1>RTGS</option><option value=2>IMPS</option><option value=3>UPI</option></select></td>
								<td>

									<select name="nb_details[nb_bank][]" class="nb_bank0" style="width:150px;"><option value="">Select Bank</option>

										<?php 

											$banks = $this->payment_model->get_bank_acc_details();

											foreach($banks as $bank)

											{ 

												echo '<option value="'.$bank['id_bank'].'">'.$bank['acc_number'].'</option>';

											}

										?>

									</select>

								</td>

								<td>

									<select name="nb_details[nb_device][]" class="nb_device0" style="width:150px;"><option value="">Select Device</option>

										<?php 

											$devices = $this->payment_model->get_payment_device_details();

											foreach($devices as $device)

											{ 

												echo '<option value="'.$device['id_device'].'">'.$device['device_name'].'</option>';

											}

										?>

									</select>

								</td>

								<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]"/></td> 

								<td><input type="number" step="any" class="amount" name="nb_details[amount][]"/></td> 

								<td><a href="#" onclick="removeNb_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  

							</tr> -->

						</tbody>

						<tfoot>

							<tr>

								<th  colspan=4>Total</th>

								<th colspan=4>

									<span class="nb_total_amount"></span>

								</th>

							</tr>

						</tfoot>

					 </table>

				  </div>

				</div>  

			</div>

		  <div class="modal-footer">

			<a href="#" id="add_newnb" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>



<!-- Advance Adj -->

<div class="modal fade" id="adv-adj-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Advance Adjustment</h4>

			</div>

			<div class="modal-body"> 

				<!--<?php echo !empty($est_other_item['chit_details']) ? '' : 'style="display:none;"' ;?>-->

				<div class="box-body chit_details"> 

					<div class="row">

						<div class="box-body">

						   <div class="table-responsive">

						   <div class="col-md-8">

							<div class="form-group">

			                  <label for="">Store As <span class="error"> *</span></label>&nbsp;&nbsp;

			                  <input type="radio" name="store_receipt_as" id="store_receipt_as_1" value="1" checked=""> Amount &nbsp;&nbsp;

			                      <input type="radio" name="store_receipt_as" id="store_receipt_as_2" value="2"> Weight  &nbsp;&nbsp;

			                  		<input type="hidden" id="id_ret_wallet" name="">

			                </div>

							</div>

							 <table id="bill_adv_adj" class="table table-bordered text-center">

								<thead>

								  <tr>

									<th width="5%;">Select</th>

									<th width="10%;">Receipt No</th>

									<th width="10%;">Total Amount</th>

									<th width="10%;">Adjusted Amount</th> 

									<th width="10%;">Balance Amount</th> 

								  </tr>

								</thead> 

								<tbody>

							</tbody>

								<tfoot>

									<tr>

									    <td colspan="2">Total</td>

									    <td><span class="total_adv_amt"></span></td>

									    <td><span class="total_adj_adv_amt"></span></td>

									    <td><span class="total_blc_amt"></span></td>

									 </tr>

									 <tr>

									    <td colspan="3">Total Bill Amount</td>

									    <td><span class="total_bill_amt"></span></td>

									 </tr>

								</tfoot>

							 </table>

						  </div>

						</div> 

					</div> 

				</div> 

			</div>

		  <div class="modal-footer">

			<a href="#" id="save_receipt_adv_adj" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal" id="close_add_adj">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / Advance Adj -->  



    
    