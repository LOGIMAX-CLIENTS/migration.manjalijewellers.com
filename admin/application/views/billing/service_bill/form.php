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

	.payment_blk input[type=text], .payment_blk input[type=number]{

		width: 250px;

	} 

	.gift_details {

      color: #FF0000;

    }

    .billType{

        padding : 3px !important;

        margin : 0px !important;

        height: auto;

    }

    .form-group {

        margin-bottom: 1px;

    }

    #payment_modes td, #total_summary_details td{

        padding : 1px 5px !important;

    }

    #payment_modes input[type=text],#payment_modes input[type=number], #payment_modes button,#total_summary_details input[type=text],#total_summary_details input[type=number], #total_summary_details button {

        height: 25px !important;

        padding: 1px 5px !important;

    }

    

    

    *[tabindex]:focus {

    outline: 1px black solid;

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

            <!-- <div class="box-header with-border">

              <h3 class="box-title">Add Billing</h3>

              <div class="box-tools pull-right">

                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

              </div>

            </div> -->

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

			<?php 

				$metal_rates = $this->admin_settings_model->metal_ratesDB("last");

			?>

			<form id="service_bill">

				<div class="col-md-12"> 	

					<div class="row"> 

					    <div class="col-md-12" style="padding: 0px;">

					       <!--Cost Center-->

    					   <div class="form-group">

                              <div class="col-md-2">

                                   <label>Cost Centre<span class="error">*</span></label><br>

                                <?php if($this->session->userdata('id_branch') == "") { ?>

    								<select name="billing[id_branch]" id="id_branch" class="form-control" required tabindex=1 autofocus>

    									<?php echo $this->ret_billing_model->get_currentBranches($billing['id_branch']); ?>

    								</select>

    							<?php }else { ?>

    					 			<label><?php echo $this->ret_billing_model->get_currentBranchName($type == 'add' ? $this->session->userdata('id_branch') : $billing['id_branch']); ?> </label>

    								<input type="hidden" id="id_branch" name="billing[id_branch]" value="<?php echo $type == 'add' ? $this->session->userdata('id_branch') : $billing['id_branch'];?>"/>

    							<?php } ?>

							</div>

							<input type="hidden" id="enable_gift_voucher" value="">

							<p id="branchAlert" class="error" align="left"></p>

                          </div>

                            <!-- Billing To -->

                            

                            <!-- Customer -->

                            <?php 

        				 		$this->session->unset_userdata('FORM_SECRET');

    				 		    $form_secret=md5(uniqid(rand(), true));

    					        $this->session->set_userdata('FORM_SECRET', $form_secret);

				 		    ?>

                            <div class="form-group">

                                <div class="col-md-3">

                                    <label>Customer<span class="error">*</span></label><br>

                                    <div class="input-group">

    									<input class="form-control" id="cus_search" name="billing[cus_name]" type="text"  placeholder="Name / Mobile"  value="<?php echo set_value('billing[cus_name]',isset($billing['cus_name'])?$billing['cus_name']:NULL); ?>" required autocomplete="off" tabindex=4/>

    									<input class="form-control" id="bill_cus_id" name="billing[bill_cus_id]" type="hidden" value="<?php echo set_value('billing[bill_cus_id]',$billing['bill_cus_id']); ?>"/>

    									<input type="hidden" id="FORM_SECRET" name="billing[form_secret]" value="<?php echo $form_secret; ?>"

    									<input type="hidden" id="validity_days" name="billing[validity_days]" value="">

    									<input type="hidden" id="validate_date" name="billing[validate_date]" value="">

    									<input type="hidden" id="id_set_gift_voucher" name="billing[id_set_gift_voucher]" value="">

    									<input type="hidden" id="gift_type" name="billing[gift_type]" value="">

    									<input type="hidden" id="utilize_for" name="billing[utilize_for]" value="">

    									<input type="hidden" id="issue_for" name="billing[issue_for]" value="">

    									<input type="hidden" id="bill_value" value="">

    									<input type="hidden" id="credit_value" value="">

    									<input type="hidden" id="calc_type" value="">

    									<input type="hidden" id="goldrate_22ct" name="billing[goldrate_22ct]" value="">

    									<input type="hidden" id="silverrate_1gm" name="billing[silverrate_1gm]" value="">

    									<input type="hidden" id="goldrate_18ct" name="billing[goldrate_18ct]" value="">

    									<input type="hidden" id="goldrate_24ct" name="billing[goldrate_24ct]" value="">

    									<input type="hidden" id="platinum_1g" name="billing[platinum_1g]" value="">

    									

    									

    									<input id="disc_limit_type" type="hidden" value="<?php echo set_value('billing[disc_limit_type]',$billing['disc_limit_type']); ?>" />

										<input id="disc_limit" type="hidden" value="<?php echo set_value('billing[disc_limit]',$billing['disc_limit']); ?>" />

										<input id="otp_dis_approval" type="hidden" value="<?php echo set_value('billing[otp_dis_approval]',$billing['otp_dis_approval']); ?>" />

											

											

    									<input id="is_counter_req" type="hidden" value="<?php echo set_value('billing[is_counter_req]',$billing['is_counter_req']); ?>" />

    									<input id="counter_id" type="hidden" value="<?php echo $this->session->userdata('counter_id'); ?>" />

    									<input id="is_tcs_required"  type="hidden" value="<?php echo set_value('billing[is_tcs_required]',$billing['is_tcs_required']); ?>" />

    									<input id="tcs_tax_per"  name="billing[tcs_tax_per]" type="hidden" value="<?php echo set_value('billing[tcs_tax_per]',$billing['tcs_tax_per']); ?>" />

    									<input id="tcs_min_bill_amt"  type="hidden" value="<?php echo set_value('billing[tcs_min_bill_amt]',$billing['tcs_min_bill_amt']); ?>" />

    									<input id="repair_order_per"  type="hidden" value="<?php echo set_value('billing[repair_order_per]',$billing['repair_percentage']); ?>" />

    									<input id="credit_sales_otp_req"  type="hidden" value="<?php echo set_value('billing[credit_sales_otp_req]',$billing['credit_sales_otp_req']); ?>" />

    									<input id="tot_purchase_amt"  type="hidden" value="" />

    									<input type="hidden" id="tcs_total_tax_amount" name="billing[tcs_tax_amt]" value="">

    									<label style="display:none;" class="per-grm-sale-value"> </label>

    									<label style="display:none;" class="silver_per-grm-sale-value"> </label>

    									

    									<input type="hidden" id="cus_state" name="billing[cus_state]">

										<input type="hidden" id="cus_country" name="billing[cus_country]">

										

										<input type="hidden" id="cus_del_state" name="billing[cus_state]">

										<input type="hidden" id="cus_del_country" name="billing[cus_country]">

											

										<input type="hidden" id="cmp_state" name="" value="<?php echo set_value('billing[cmp_state]',$billing['cmp_state']); ?>" >

											

										<input type="hidden" id="cmp_country" name="" value="<?php echo set_value('billing[cmp_country]',$billing['cmp_country']); ?>" >

											

    									

    									<input id="bill_id" name="billing[bill_id]" type="hidden" value="<?php echo set_value('billing[bill_id]',$billing['bill_id']); ?>" />

										<input id="validate_max_cash" type="hidden" />

										<input id="max_cash_amt" type="hidden" />

										<input id="chit_total_cash_amt" type="hidden" value="0" />

										<input id="adv_total_cash_amt" type="hidden" value="0" />

    									<span id="customerAlert"></span>

    									<span class="input-group-btn">

                                            <a class="btn btn-default" id="add_new_customer" href="#"  data-toggle="tooltip" title="Add Customer" tabindex=5><i class="fa fa-user-plus"></i></a>

                                            <a class="btn btn-warning" id="edit_estimation_detalis" href="#"  tabindex=5 data-toggle="tooltip" title="Edit Customer" ><i class="fa fa-user-plus"></i></a>

                                        </span>

    								</div>

                                </div>

                            </div>

                            <!-- Billing To -->

                            

                        </div> 

				 	</div>

				 	<p></p><p></p><p></p>

	            

			       

					<div align="left"  style="background: #f5f5f5">

                		<ul class="nav nav-tabs" id="billing-tab">

                	      	<li id="item_summary" class="active"><a id="tab_items" href="#pay_items" data-toggle="tab">Item</a></li>

                		  	<li id="tab_tot_summary"><a href="#tot_summary" data-toggle="tab">Total Summary</a></li>

                	    </ul>

                	</div>

                	<div class="tab-content">

                		<div class="tab-pane active" id="pay_items">

                		    <div class="box box-default ">

        						<div class="box-body">

                			        <!-- Search Block	 -->

                					

        							<div class="row sale_details">

        								<div class="col-md-12">

        							       <p class="text-light-blue">Sales Items</p>

        								   <div class="table-responsive">

                                                <table id="billing_repair_order_other_details" class="table table-bordered table-striped text-center repair_without_orderno">

                                                    <thead>

                                                        <tr>

                                                            <th>Type</th>

                                                            <th>Product</th>

                                                            <th>Pcs</th>

                                                            <th>Completed Weight</th>

                                                            <th>TAX(%)</th>

                                                            <th>Amount</th>

                                                            <th>Action</th>

                                                        </tr>

                                                    </thead> 

                                                        <tbody>

                                                        </tbody>

                                                    <tfoot>

                                                    <tr></tr>

                                                    </tfoot>

                                                </table>

        								  </div>

        								</div> 

        							</div> 



                                    <div align="right" style="display:none;">

                                        <button type="button" class="btn btn-warning next-tab">Next</button> 

                                    </div>

                        		</div>

                    		</div>

                		</div>

                		<div class="tab-pane" id="tot_summary">

                		    <div class="row">

                		        <div class="col-sm-6">

        							<div class="box box-default total_summary_details" style="">

        								<div class="box-body">

        									<div class="row">

        										<div class="col-md-offset-1 col-md-10">

        										   <div class="table-responsive">

        											 <table id="total_summary_details" class="table table-bordered table-striped">

        												<thead>

        													<tr>

        														<th>Amount</th>

        														<th>INR</th>

        													</tr>

        												</thead> 

        												<tbody> 

        													<tr>

        														<td>Taxable Sale Amount</td>

        														<td><span class="summary_lbl summary_sale_amt"></span></td>

        													</tr>

        													<tr>

        														<td class="text-right">CGST</td>

        														<td><span class="summary_lbl sales_cgst"></span>

        															<input type="hidden" id="cgst" class="cgst" name="billing[cgst]">

        														</td>

        													</tr>

        													<tr>

        														<td class="text-right">SGST</td>

        														<td><span class="summary_lbl sales_sgst"></span>

        														<input type="hidden" id="sgst" class="sgst" name="billing[sgst]">

        														</td>

        													</tr>

        													<tr>

        														<td class="text-right">IGST</td>

        														<td><span class="summary_lbl sales_igst"></span>

        														<input type="hidden" id="igst" class="igst" name="billing[igst]">

        														</td>

        													</tr>

        													<tr>

        														<td>Sale Amount</td>

        														<td><span class="summary_lbl sale_amt_with_tax"></span></td>

        													</tr>

        												

        													<tr>

        														<td class="text-right">Final Price</td>

        														<td><input type="number" class="form-control total_cost summary_lbl" id="total_cost" name="billing[total_cost]" value="" required style="width: fit-content;"></td>

        													</tr>

        													<tr>

    															<td class="text-right">Received</td>

    															<td> 

    																<input type="number" class="form-control service_bill_receive_amount" name="billing[tot_amt_received]" value="<?php echo set_value('billing[tot_amt_received]',isset($billing['tot_amt_received'])?$billing['pan_no']:0); ?>" style="width: fit-content;" required readonly >

    															</td>

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

        						<div class="col-sm-5">

								    <div class="table-responsive">

									 <table id="payment_modes" class="table table-bordered table-striped">

									     <tbody>

											

											

							

											<?php 

											$modes = $this->ret_billing_model->get_payModes();

											if(sizeof($modes)>0){

											foreach($modes as $mode){

												$cash = ($mode['short_code'] == "CSH" ? '<input class="form-control" id="service_bill_cash" name="billing[cash_payment]" type="text" placeholder="Enter Amount" value=""/>' : '');

												$card = ($mode['short_code'] == "CC" ? '<a class="btn bg-olive btn-xs pull-right" id="card_detail_modal" href="#" data-toggle="modal" data-target="#card-detail-modal" ><b>+</b></a> ' : '');

												$cheque = ($mode['short_code'] == "CHQ"  ? '<a class="btn bg-olive btn-xs pull-right" id="cheque_modal" href="#" data-toggle="modal" data-target="#cheque-detail-modal" ><b>+</b></a> ' : '');

												$net_banking = ($mode['short_code'] == "NB"  ? '<a class="btn bg-olive btn-xs pull-right"  href="#" " id="net_bank_modal" data-toggle="modal" data-target="#net_banking_modal" ><b>+</b></a> ' : '');

											?>

											<tr>

												<td class="text-right"><?php echo $mode['mode_name']; ?>

												</td>

												<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>

												<td class="mode_<?php echo $mode['short_code']; ?>">

													<span class="<?php echo $mode['short_code'];?>"></span>

													<input type="hidden" id="card_payment" name="billing[card_pay]" value="">

													<input type="hidden" id="chq_payment" name="billing[chq_pay]" value="">

													<input type="hidden" id="nb_payment" name="billing[net_bank_pay]" value="">

													<?php echo $cash; ?> 

													<?php echo $card; ?> 

													<?php echo $cheque; ?> 

													<?php echo $net_banking; ?> 

												</td> 

											</tr>

											<?php }}?>

											

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

                		    <div class="row">

							    <div class="col-sm-12" align="center">

							        <?php if($this->uri->segment(3) != 'edit'){?>

            						<button type="button" id="service_bill_pay_submit" class="btn btn-primary" disabled>Save</button> 

            						<?php }?>

            						<button type="button" class="btn btn-default btn-cancel">Cancel</button>

							    </div>

							</div> 

                		</div>

				 	<p></p>

				</div>	<!--/ Col --> 

			</div>	 <!--/ row -->

			   <p class="help-block"> </p>  

	            </div>  

	          <?php echo form_close();?>

	           

	             <!-- /form -->

	          </div>

	           <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

             </section>

            </div>

<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Add Customer</h4>

			</div>

			<div class="modal-body">

                <div class="row" style="display:none;">   

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Customer Type<span class="error"></span></label>

					     <div class="col-md-6">

						 <input type="radio" id="cus_type1"  name="cus[cus_type]" value="1" class="minimal" checked/> Individual

						 <input type="radio" id="cus_type2"  name="cus[cus_type]" value="2" class="minimal" /> Business

					   </div>

					</div>

				</div></br>

				 

				<div class="row">

					<div class="form-group">

					   <label for="cus_first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="cus_first_name" name="cus[first_name]" placeholder="Enter customer first name" required="true"> 

							<p class="help-block cus_first_name error"></p>

					   </div>

					</div>

				</div> 

				<div class="row">   

					<div class="form-group">

					   <label for="cus_mobile" class="col-md-3 col-md-offset-1 ">Mobile<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="number" class="form-control" id="cus_mobile" name="cus[mobile]" placeholder="Enter customer mobile"> 

							<p class="help-block cus_mobile error"></p>

					   </div>

					</div>

				</div>

				<div class="row">

					<div class="form-group">

					   <label for="cus_pan" class="col-md-3 col-md-offset-1 ">Pan</label>

					   <div class="col-md-6">

							<input type="text" class="form-control pan_no" id="pan" name="cus[pan]" placeholder="Enter Pan ID"> 

							<p class="help-block cus_email error"></p>

					   </div>

					</div>

				</div>

				<div class="row">

					<div class="form-group">

					   <label for="cus_aadhar" class="col-md-3 col-md-offset-1 ">Aadhar</label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="aadharid" name="cus[cus_aadhar]" maxlength="14" placeholder="Enter aadhar ID"> 

							<p class="help-block cus_email error"></p>

					   </div>

					</div>

				</div>

				

				<div class="row">

					<div class="form-group">

					   <label for="cus_email" class="col-md-3 col-md-offset-1 ">Email</label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="cus_email" name="cus[cus_email]" placeholder="Enter Email ID"> 

							<p class="help-block cus_email error"></p>

					   </div>

					</div>

				</div>

				

				<div class="row">   

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Select Country<span class="error">*</span></label>

					   <div class="col-md-6">

						 <select class="form-control" id="country" style="width:100%;"></select>

						 <input type="hidden" name="cus[id_country]" id="id_country"> 

					   </div>

					</div>

				</div></br>

				

			    <div class="row">   

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Select State<span class="error">*</span></label>

					   <div class="col-md-6">

						 <select class="form-control" id="state" style="width:100%;"></select>

						  <input type="hidden" name="cus[id_state]" id="id_state">

					   </div>

					</div>

				</div></br>

				

				 <div class="row">   

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Select City<span class="error">*</span></label>

					   <div class="col-md-6">

						 <select class="form-control" id="city"  style="width:100%;"></select>

						  <input type="hidden" name="cus[id_city]" id="id_city">

					   </div>

					   

					</div>

				</div></br>

				

				<div class="row">   

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">Select Area</label>

					   <div class="col-md-6">

						 <select class="form-control" id="sel_village" style="width:100%;"></select>

							<input type="hidden" name="cus[id_village]" id="id_village" name="">

							<p class="help-block sel_village error"></p>

					   </div>

					</div>

				</div></br>

			

				<div class="row">

					<div class="form-group">

					    <label for="address1" class="col-md-3 col-md-offset-1 ">Address1<span class="error">*</span></label>

						   <div class="col-md-6">

								<input class="form-control" id="address1" name="customer[address1]" value=""  type="text" placeholder="Enter Address Here 1" required />

								<p class="help-block address1 error"></p>

							</div>

					</div>

				</div></br>

				<div class="row">

					<div class="form-group">

					    <label for="address2" class="col-md-3 col-md-offset-1">Address2</label>

						   <div class="col-md-6">

								<input class="form-control" id="address2" name="customer[address2]" placeholder="Enter Address Here 2" value=""  type="text" />

							</div>

					</div>

				</div></br>

				<div class="row">

					<div class="form-group">

					    <label for="address3" class="col-md-3 col-md-offset-1">Address3</label>

						   <div class="col-md-6">

								<input class="form-control titlecase" id="address3" name="customer[address3]" value=""  type="text" placeholder="Enter Address Here 3" />

							</div>

					</div>

				</div></br>

				<div class="row">

					<div class="form-group">

					    <label for="pincode" class="col-md-3 col-md-offset-1">Pin Code<span class="error"></span></label>

						   <div class="col-md-6">

								<input class="form-control titlecase" id="pin_code_add" type="number" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' required />

								<p class="help-block pincode error"></p>

							</div>

					</div>

				</div></br>

			

				<div class="row gst" style="display:none;">   

					<div class="form-group">

					   <label for="" class="col-md-3 col-md-offset-1 ">GST No<span class="error"></span></label>

					   <div class="col-md-6"> 

							<input type="text" class="form-control" id="gst_no" name="cus[gst_no]" placeholder="Enter GST No"> 

							<p class="help-block cus_mobile"></p>

					   </div>

					</div>

				</div>

			</div>

		  <div class="modal-footer">

		     <input type="hidden" name="cus[id_customer]" id="id_customer" value="">

			 <a href="#" id="add_newcutomer" class="btn btn-success">Add</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- modal for fetch estimation details -->      

<div class="modal fade" id="estimation-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" style="width:75%;">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Select to add billing</h4>

			</div>

			<div class="modal-body">

				<div class="row" id="est_items_to_sale_convertion_tbl" style="display:none;">

					<div class="box-body">

						<p class="lead">Estimation</p>

						    <div class="row">

								<div class="col-md-6">

									<label>Non Tag Available Pieces : <span id="blc_pcs"></span>,</label>

									<label>Available Weight : <span id="blc_gwt"></span></label>

								</div>

								<div class="col-md-6">

									<label>Total Pieces : <span id="tot_pcs"></span>,</label>

									<label>Total Weight : <span id="tot_wt"></span></label>

								</div>

							</div>

						<div class="table-responsive">

							<table id="est_items_to_sale_convertion" class="table table-bordered table-striped text-center">

								<thead>

									<tr>

										<th><label class="checkbox-inline"><input type="checkbox" id="select_Allsale" name="select_all" value="all"/>All</label></th>

										<th>Product</th>

										<th>Design</th>

										<th>Pcs</th>    

										<th>Purity</th>   

										<th>Size</th> 

										<th>G.Wt</th>   

										<th>L.Wt</th>   

										<th>N.Wt</th>   

										<th>Wast(%)</th>   

										<th>Wast Wt(g)</th>   

										<th>MC</th>   

										<th>Discount</th>   

										<th>Tax Group</th>   

										<th>Tax</th>   

										<th>Amount</th>

										<th>Partly</th>

										<th>Tag No</th>

										<!--<th>Advance Paid</th> -->

									</tr>

								</thead> 

								<tbody>

								</tbody>

								<tfoot>

									<tr></tr>

								</tfoot>

							</table>

							<p></p>

						</div>

					</div> 

				</div> 

				<div class="row" id="est_olditems_to_sale_convertion_tbl" style="display:none;"> 

					<div class="box-body">

						<p class="lead">Purchase</p>

						<div class="table-responsive">

							<table id="est_olditems_to_sale_convertion" class="table table-bordered table-striped text-center">

								<thead>

									<tr>

										<th><label class="checkbox-inline"><input type="checkbox" id="select_Allpur" name="select_all" value="all"/>All</label></th>

										<th>Purpose</th>

										<th>Category</th>

										<th>G.Wt</th>   

										<th>Dust.Wt</th>   

										<th>Stn.Wt</th>   

										<th>N.Wt</th>   

										<th>Wastage(%)</th>   

										<th>Wastage.Wt(%)</th>

										<th>Rate Per grm</th>  

										<th>Discount</th> 

										<th>Amount</th>

									</tr>

								</thead> 

								<tbody>

								</tbody>

								<tfoot>

									<tr></tr>

								</tfoot>

							</table>

						</div>

					</div> 

				</div> 

			</div>

		  <div class="modal-footer">

			<a href="#" id="update_estimation_to_bill" class="btn btn-success">Add</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / esti to billing modal -->        

<!--Gift Voucher-->

<div class="modal fade" id="gv-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Gift Voucher</h4>

			</div>

			<div class="modal-body">

				<!--Gift Voucher--> 

					 <!--<?php echo !empty($est_other_item['voucher_details']) ? '' : 'style="display:none;"' ;?>-->

				<div class="box-body gift_voucher_details">

					<div class="row"> 

						<!--<div class="col-sm-12 pull-right">

							<button type="button" id="create_gift_voucher_details" class="btn bg-olive  btn-sm pull-right"><i class="fa fa-plus"></i> Add</button>

							<p class="error "><span id="voucherAlert"></span></p>

						</div>-->

					</div>

					<div class="row">

						<div class="box-body">

						   <div class="table-responsive">

							 <table id="gift_voucher_details" class="table table-bordered text-center">

								<thead>

								  <tr>

									<th>Voucher No</th>  

									<th>Amount</th>

									<th>Action</th>

								  </tr>

								</thead> 

								<tbody>

									<?php if($this->uri->segment(3) == 'edit'){

										foreach($est_other_item['voucher_details'] as $ikey => $ival){

												echo '<tr><td><input class="voucher_no" type="number" name="gift_voucher[voucher_no][]" style="width: 100px;" value="'.$ival['voucher_no'].'" /></td><td><input type="number" class="gift_voucher_amt" style="width: 100px;"  name="gift_voucher[gift_voucher_amt][]" value=""'.$ival['gift_voucher_amt'].'  /></td></tr>';

										}

									}else{ ?>

										<tr>

											<td><input class="voucher_no" type="text" name="gift_voucher[voucher_no][]" style="width: 100px;" /><input type="hidden" class="id_gift_card"></td>

											<td><input type="number" class="gift_voucher_amt" style="width: 100px;"  name="gift_voucher[gift_voucher_amt][]" readonly /></td>

											<td><a href="#" onclick="removeGift_voucher($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

										</tr>

									<?php }?>

								</tbody>

								<tfoot>

									<tr>

										<th >Total</th>

										<th colspan=2><span class="gift_total_amount"></span></th>

									</tr>

								</tfoot>

							 </table>

						  </div>

						</div> 

					</div> 

				</div>  

				<!--./Gift Voucher-->

			</div>

		  <div class="modal-footer">

			<a href="#" id="add_newvoucher" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / Gift Voucher Modal -->  

<!-- Chit Utilization -->

<div class="modal fade" id="chit-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Chit Utilization</h4>

			</div>

			<div class="modal-body"> 

				<!--<?php echo !empty($est_other_item['chit_details']) ? '' : 'style="display:none;"' ;?>-->

				<div class="box-body chit_details">

					<div class="row"> 

						<div class="col-sm-12 pull-right">

							<button type="button" id="create_chit_details" class="btn bg-olive btn-sm pull-right"><i class="fa fa-plus"></i> Add</button>

							<p class="error "><span id="chitUtilAlert"></span></p>

						</div>

					</div>

					<div class="row">

						<div class="box-body">

						   <div class="table-responsive">

							 <table id="estimation_chit_details" class="table table-bordered text-center">

								<thead>

								  <tr>

									<th>A/c Id</th>

									<th>Scheme</th>

									<th>Amount</th>

									<th>Action</th>

								  </tr>

								</thead> 

								<tbody>

										<tr>

											<td><input class="scheme_account" type="text" style="width: 100px;" />

												<input type="hidden" class="scheme_account_id"  name="chit_uti[scheme_account_id][]">

											</td>

											<td><span class="sch"></span></td>

											<td><span class="chit_amount"></span><input type="hidden" class="chit_amt" name="chit_uti[chit_amt][]" /><input type="hidden" class="chit_cash_pay" name="chit_uti[chit_cash_pay][]" /></td>

											<td><a href="#" onclick="removeChit_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

										</tr>

								</tbody>

								<tfoot>

									<tr>

										<th colspan=2>Total</th>

										<th colspan=2><input type="hidden" class="chit_cash_amt" /><span class="total_amount"></span></th>

									</tr>

								</tfoot>

							 </table>

							   <!-- <div class="col-md-12">

        							<div class="row">

        							   <div class="col-md-4">

        							      <input type="hidden" id="mobile" value="">

        							      <input type="hidden" id="send_resend" value="0">

                                         <button class="btn btn-primary" id="send_otp" value="Send OTP">Send OTP</button>

        							   </div>

        							   <div class="col-md-4">

                                         <input type="number" class="form-control" id="user_otp" disabled>

        							   </div>

        							 </div>

        							 <span id="otp_alert"></span>

							    </div>-->

						  </div>

						</div> 

					</div> 

				</div> 

			</div>

		  <div class="modal-footer" >

			<a id="add_newchit_util" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / Chit Utilisation -->  

<!-- Advance Adj -->

<div class="modal fade" id="adv-adj-confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" style="width:75%;">

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

									<th width="10%;">Refund Amount</th> 

									<th width="10%;">Refund Mode</th> 

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

			<a href="#" id="add_adv_adj" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal" id="close_add_adj">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / Advance Adj -->  

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

								<!-- <th>Card Name</th>

								<th>Type</th> 

								<th>Device<span class="error">*</span></th> 

								<th>Card No</th> 

								<th>Amount</th> 

								<th>Approval No</th> 

								<th>Action</th>  -->

								<th width="12%">Card Name</th>
								<th width="5%">Type</th> 
								<th width="15%">Device<span class="error">*</span></th> 
								<th width="10%">Date</th>
								<th width="23%">Card No</th> 
								<th width="20%">Amount</th> 
								<th width="20%">Approval No</th> 
								<th width="5%">Action</th>

							</tr>											

						</thead> 

						<tbody>

							<?php if($this->uri->segment(3) == 'edit'){

								/*foreach($est_other_item['card_details'] as $ikey => $ival){

										echo '<tr><td><input class="card_name" type="number" name="card_details[card_name][]" value="'.$ival['card_name'].'" /></td><td><input class="card_type" type="number" name="card_details[card_type][]" value="'.$ival['card_type'].'" /></td><td><input type="number" class="card_no" style="width: 100px;"  name="card_details[card_no][]" value="'.$ival['card_no'].'"  /></td><td><input type="number" class="card_amt" style="width: 100px;"  name="card_details[card_amt][]" value="'.$ival['card_amt'].'"  /></td><td>-</td></tr>';

								}*/

							}else{ ?>

							<!--<tr> 

								<td><select name="card_details[card_name][]" class="card_name"><option value="1">RuPay</option><option value="2">VISA</option><option value="3">Mastro</option><option value="4">Master</option></select></td>

								<td><select name="card_details[card_type][]" class="card_type"><option value="1">CC</option><option value="2">DC</option></select></td>

								<td><input type="number" step="any" class="card_no" name="card_details[card_no][]"/></td> 

								<td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]"/></td> 

								<td><input type="text" step="any" class="ref_no" name="card_details[ref_no][]"/></td> 

								<td><a href="#" onclick="removeCC_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  

							</tr> -->

							<?php } ?>

						</tbody>

						<tfoot>

							<tr>

								<th  colspan=4>Total</th>

								<th colspan=3>

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

			<a href="#" id="service_bill_card" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / Advance Adj -->  

<!-- cheque-->

<!-- Card Details -->

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

								<!-- <th>Cheque Date</th>

								<th>Bank</th> 

								<th>Cheque No</th>  

								<th>Amount</th>  

								<th>Action</th>   -->

								<th width="20%">Cheque Date</th>
								<th width="20%">Bank</th> 
								<th width="25%">Cheque No</th>  
								<th width="30%">Amount</th>  
								<th width="5%">Action</th> 

							</tr>											

						</thead> 

						<tbody>

							<!--<tr> 

								<td><input id="cheque_datetime" data-date-format="dd-mm-yyyy hh:mm:ss" class="cheque_date" name="cheque_details[cheque_date][]" type="text" required="true" placeholder="Cheque Date" /></td>

								<td><input name="cheque_details[bank_name][]" type="text" required="true" class="bank_name"></td>

								<td><input name="cheque_details[bank_branch][]" type="text" required="true" class="bank_branch"></td>

								<td><input type="number" step="any" class="cheque_no" name="cheque_details[cheque_no][]"/></td> 

								<td><input type="text" step="any" class="bank_IFSC" name="cheque_details[bank_IFSC][]"/></td> 

								<td><input type="number" step="any" class="payment_amount" name="cheque_details[payment_amount][]"/></td> 

								<td><a href="#" onclick="removeChq_row($(this).closest('tr'))" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  

							</tr> -->

						</tbody>

						<tfoot>

							<tr>

								<!-- <td>Total</td><td></td><td></td><td></td><td></td><td><span class="chq_total_amount"></span></td><td></td> -->
								<td colspan="2">Total</td>
								<td colspan="3"><span class="chq_total_amount"></span></td>

							</tr>

						</tfoot>

					 </table>

				  </div>

				</div>  

			</div>

		  <div class="modal-footer">

			<a href="#" id="service_bill_chq" class="btn btn-success">Save</a>

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

								<!-- <th>Type</th>

								<th class="upi_type" >Bank</th> 

								<th>Payment Date</th> 

								<th class="device" style="display:none">Device</th> 

								<th>Ref No</th>  

								<th>Amount</th>  

								<th>Action</th>  -->

								<th width="15%">Type</th>
								<th width="20%">Bank/Device</th>
								<th width="20%">Payment Date</th>
								<th width="20%">Ref No</th>
								<th width="20%">Amount</th>
								<th width="5%">Action</th>

							</tr>											

						</thead>

						<tbody>

						    

						</tbody> 

						<tfoot>

							<tr>

								<th  colspan=3>Total</th>

								<th colspan=3>

									<span class="nb_total_amount"></span>

								</th>

							</tr>

						</tfoot>

					 </table>

				  </div>

				</div>  

			</div>

		  <div class="modal-footer">

			<a href="#" id="service_bill_net_bank" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- Net Banking-->

<!-- Return Bill Modal -->      

<div class="modal fade" id="bill-detail-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" style="width:75%;">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Select to Return</h4>

			</div>

			<div class="modal-body">

				<div class="row" id="bill_items_for_return" style="display:none;">

					<div class="box-body">

						<p class="lead">Bill Item Details</p>

						<div class="table-responsive">

							<table id="bill_items_tbl_for_return" class="table table-bordered table-striped text-center">

								<thead>

									<tr>

										<th>Select</th>

										<th>Product</th>

										<th>Design</th>

										<th>Pcs</th>    

										<th>Purity</th>   

										<th>Size</th> 

										<th>G.Wt</th>   

										<th>L.Wt</th>   

										<th>N.Wt</th>   

										<th>Wast(%)</th>   

										<th>MC</th>   

										<th>Discount</th>   

										<th>Tax Group</th>   

										<th>Tax</th>   

										<th>Amount</th>

										<th>Partly</th>

										<th>Tag No</th>

									</tr>

								</thead> 

								<tbody>

								</tbody>

								<tfoot>

									<tr></tr>

								</tfoot>

							</table>

							<p></p>

						</div>

					</div> 

				</div> 

				<div class="row" id="bill_old_items_purchased" style="display:none;"> 

					<div class="box-body">

						<p class="lead">Purchased Items</p>

						<div class="table-responsive">

							<table id="bill_old_items_purchased_tbl" class="table table-bordered table-striped text-center">

								<thead>

									<tr>

										<th>Select</th>

										<th>Purpose</th>

										<th>Category</th>

										<!--<th>Purity</th>  --> 

										<th>G.Wt</th>   

										<th>Dust.Wt</th>   

										<th>Stn.Wt</th>   

										<th>N.Wt</th>   

										<th>Wastage(%)</th>   

										<th>Rate Per grm</th>  

										<th>Discount</th> 

										<th>Amount</th>

									</tr>

								</thead> 

								<tbody>

								</tbody>

								<tfoot>

									<tr></tr>

								</tfoot>

							</table>

						</div>

					</div> 

				</div> 

			</div>

		  <div class="modal-footer">

			<a href="#" id="update_bill_return" class="btn btn-success">Add</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / Return Bill Modal -->  

<!-- sale stone details-->

<div class="modal fade" id="stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:72%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>

			</div>

			<div class="modal-body">

				<div class="row">

			<div class="box-tools pull-right">

			<!--<button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>-->

			</div>

			</div>

				<div class="row">

						<input type="hidden" id="active_id"  name="">

					<table id="estimation_stone_item_details" class="table table-bordered table-striped text-center">

					<thead>

					<tr>

					<th width="5%">LWT</th>

					<th width="15%">Name</th>

					<th width="10%">Pcs</th>   

					<th width="20%">Wt</th>

					<th width="10%">Cal.Type</th>

					<th width="10%">Rate Per Gram</th>

					<th width="17%">Amount</th>

					</tr>

					</thead> 

					<tbody>

					</tbody>										

					<tfoot>

					<tr></tr>

					</tfoot>

					</table>

			</div>

		  </div>

		  <div class="modal-footer">

			<button type="button" id="update_stone_details" class="btn btn-success" style="display:none;">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

</div>

<!-- sale stone details-->

<!--Purchase stone-->

<div class="modal fade" id="PurstoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>

			</div>

			<div class="modal-body">

				<div class="row">

			<div class="box-tools pull-right">

			</div>

			</div>

				<div class="row">

						<input type="hidden" id="pur_active_id"  name="">

					<table id="estimation_pur_stone_item_details" class="table table-bordered table-striped text-center">

					<thead>

					<tr>

					<th>Stone</th>

					<th>Pcs</th>   

					<th>Wt</th>

					<th>Price</th>

					<th>Action</th>

					</tr>

					</thead> 

					<tbody>

					</tbody>										

					<tfoot>

					<tr></tr>

					</tfoot>

					</table>

			</div>

		  </div>

		  <div class="modal-footer">

			<button type="button" id="update_pur_stone_details" class="btn btn-success">Save</button>

			<button type="button" id="close_pur_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

</div>

<!--Purchase stone-->

<!--Return Bill No-->

<div class="modal fade" id="billno-detail-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" style="width:75%;">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Select to Return</h4>

			</div>

			<div class="modal-body">

				<div class="row">

					<div class="col-md-4">	

						<div class="form-group">

							<label>Select Bill No</label>

							 <select id="billno_select" name="billno_select" class="form-control" style="width:100%;" multiple></select>  

							<input type="hidden" id="filter_Billno" name="">

						</div>

					</div>

				</div>

				<div class="row" id="bill_items_return" style="display:none;">

					<div class="box-body">

						<p class="lead">Bill Item Details</p>

						<div class="table-responsive">

							<table id="bill_items_for_return" class="table table-bordered table-striped text-center">

								<thead>

									<tr>

										<th>Select</th>

										<th>Product</th>

										<th>Design</th>

										<th>Pcs</th>    

										<th>Purity</th>   

										<th>Size</th> 

										<th>G.Wt</th>   

										<th>L.Wt</th>   

										<th>N.Wt</th>   

										<th>Wast(%)</th>   

										<th>MC</th>   

										<th>Discount</th>   

										<th>Tax Group</th>   

										<th>Tax</th>   

										<th>Amount</th>

										<th>Partly</th>

										<th>Tag No</th>

									</tr>

								</thead> 

								<tbody>

								</tbody>

								<tfoot>

									<tr></tr>

								</tfoot>

							</table>

							<p></p>

						</div>

					</div> 

				</div> 

			</div>

		  <div class="modal-footer">

			<a href="#" id="update_billreturn" class="btn btn-success">Add</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / Return Bill Modal -->  

<!--Return Bill No-->

<!-- emp modal -->      

<div class="modal fade" id="emp_add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

				<h4 class="modal-title" id="myModalLabel">Add Employee</h4>

			</div>

			<div class="modal-body">

				<div class="row">

					<div class="form-group">

					   <label for="cus_first_name" class="col-md-3 col-md-offset-1 ">Employee Name<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="text" class="form-control" id="emp_firstname" name="emp[firstname]" placeholder="Enter customer first name" required="true"> 

							<p class="help-block cus_first_name"></p>

					   </div>

					</div>

				</div> 

				<div class="row">   

					<div class="form-group">

					   <label for="cus_mobile" class="col-md-3 col-md-offset-1 ">Mobile<span class="error">*</span></label>

					   <div class="col-md-6">

							<input type="number" class="form-control" id="emp_mobile" name="emp[mobile]" placeholder="Enter customer mobile"> 

							<p class="help-block cus_mobile"></p>

					   </div>

					</div>

				</div>

			</div>

		  <div class="modal-footer">

			<a href="#" id="add_newemployee" class="btn btn-success">Add</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- / emp modal -->

<div class="modal fade" id="charge_items_popup" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog modal-lg">

	<div class="modal-content">

		<div class="modal-header">

			<button type="button" class="close" data-dismiss="modal"><span

					aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

			<h4 class="modal-title" id="myModalLabel">Charges</h4>

		</div>

		<div class="modal-body">

			<div>

			<table id="billing_charges_details" class="table table-bordered table-striped text-center">

				<thead>

				<tr>

				<th>Charge Code</th>

				<th>Value</th>   

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

<!-- CHIT DEPOSIT -->

<div class="modal fade" id="chit_deposit_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog modal-lg">

	<div class="modal-content">

	    <div class="modal-header">

			<h4 class="modal-title" id="myModalLabel">Account Details</h4>

		</div>

		<div class="modal-body">

			<div>

			<table id="chit_deposit_details" class="table table-bordered table-striped text-center">

				<thead>

				<tr>

				<th>Select Scheme</th>

				<th>Select Account</th>

				<th>Action</th>

				</tr>

				</thead> 

				<tbody>

				</tbody>										

			</table>

			</div>

		</div>

		<div class="modal-footer">

			<button type="button" class="btn btn-success" id="save_chit_deposit">Save</button>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		</div>

	</div>

</div>

</div>

<!-- CHIT DEPOSIT -->





<div class="modal fade" id="otp_validation" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" id = 'close_modl'><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">OTP Validation </h4>

      </div>

      <div class="modal-body">

    	  <div class="row" >

    					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>

    	  </div> 

            <div class="row">

                <div class="form-group">

                    <div class="col-md-8">

					    <div class="form-group"> 

    					    <label>OTP Verification</label>

                            <div class="input-group margin">

                                <input type="text" id="otp_by_emp" class="form-control" required placeholder="6 Digit OTP" />

                                <span class="input-group-btn">

                                    <button type="button" class="btn btn-warning btn-flat" id="resend_otp">Resend OTP</button>

                                </span>

                            </div>

                        </div>

			    	</div>

                

                            

                </div>

			</div>

	 </div>				 

      <div class="modal-footer">

		<a href="#" id="otp_submit_validation" class="btn btn-success">Submit</a>

        <button type="button" class="btn btn-danger" id="discount_close_modal" >Close</button>

      </div>

    </div>

  </div>

</div>





<div class="modal fade" id="otp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">



		<h4 class="modal-title" id="myModalLabel">Verify OTP and Update Status</h4>

	  </div>

      <div class="modal-body"> 

         	<div class="row" > 

         		<div class="col-md-12">

         			<h5>We have sent OTP to autorized mobile number. Kindly verify OTP to proceed further.</h5> 

		    	</div>

		    </div>

		    <p></p>

         	<div class="row otp_block"> 

		    	<div class="col-md-2">

		    		<div class='form-group'>

		                <label for="">OTP</label>

		            </div>

		    	</div> 

		    	<div class="col-md-5">

		    		<div class='form-group'>

			    		<div class='input-group'>

			                <input type="text" id="credit_otp" name="otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required /> 

			                <span class="input-group-btn">

				            	<button type="button" id="verify_credit_otp" class="btn btn-primary btn-flat" disabled >Verify</button>

				            </span>

			            </div> 

		            </div>

		    	</div> 

		    	<div class="col-md-2">

		    		<div class='form-group'>

		               <input type="button" id="resend_credit_otp" class="btn btn-warning btn-flat" disabled value="Resend OTP"/> <span id="timer"></span>

					 

		            </div>

		    	</div>     

			 </div> 

			 <div class="row">

			 	<div class="col-md-12">

			 		<span class="otp_alert"></span>

			 	</div>

			 </div>  

	</div>  

	<div class="modal-footer">

		<button type="button" id="approve" class="btn btn-success btn-flat" disabled>Approve</button>	 

		<button type="button" id="cancel_credit_otp" class="btn btn-danger btn-flat" id="close">Close</button>

	</div>

   </div>

  </div>

</div> 

