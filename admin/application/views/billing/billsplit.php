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
			<form id="bill_pay">
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
    					 			<label><?php echo $this->ret_billing_model->get_currentBranchName($this->session->userdata('id_branch')); ?> </label>
    								<input type="hidden" id="id_branch" name="billing[id_branch]" value="<?php echo $this->session->userdata('id_branch');?>"/>
    							<?php } ?>
							</div>
							<input type="hidden" id="enable_gift_voucher" value="">
							<p id="branchAlert" class="error" align="left"></p>
                          </div>
                            <!-- Billing To -->
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label>Billing To</label><br>
                                    <input type="radio"  id="billing_for1" name="billing[billing_for]" value="1" checked tabindex=2> Customer </label>
                                    <input type="radio"  id="billing_for2" name="billing[billing_for]" value="2" tabindex=3> Company </label>
                                    <input type="radio"  id="billing_for3" name="billing[billing_for]" value="3" tabindex=4> Supplier </label>
									<input class="form-control cus_id" id="bill_cus_id"  type="hidden" value="" />
                                </div>

                                <div class="col-md-2 .md-offset-5">
									<label>Employee</label></br>
									<select id="emp_select" name="billing[id_employee]" style="width:100%"></select>
								</div>

                                <div class="col-md-2" >
        				 		    <label><a  data-toggle="tooltip" title="Delivery Location">Delivery Location</a><span class="error">*</span></label>
        				 		    <div class="form-group">
        				 		        <select class="form-control" id="delivered_at" name="billing[delivered_at]">
        				 		            <option value="1" selected>Show Room</option>
        				 		            <option value="2">Customer Address</option>
        				 		        </select>
        				 		    </div>
        				 		</div>

                                <div class="col-md-2" style="<?php echo $billing['IsMetalForBilling'] == 1 ? 'display:block':'display:none'?>">
									<label>Select Metal<span class="error">*</span></label>
									<div class="form-group">
										<?php if($billing['IsMetalForBilling'] == 1) {?>
											<select class="form-control" id="select_metal_type" name="billing[metal_type]"></select>
										<?php }else {?>
											<input type="hidden" id="select_metal_type" name="billing[metal_type]">
										<?php } ?>
									</div>
        				 		</div>
                            </div>
                            <!-- Customer -->

                            <div class="form-group">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="hidden" id="form_secret" name="billing[form_secret]" value="<?php echo get_form_secret_key(); ?>">
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
										<input id="allow_bill_type" type="hidden" value="<?php echo set_value('billing[allow_bill_type]',$billing['allow_bill_type']); ?>" />
										<input id="is_eda" type="hidden"  name="billing[is_eda]" value="1" />


    									<input id="is_counter_req" type="hidden" value="<?php echo set_value('billing[is_counter_req]',$billing['is_counter_req']); ?>" />
    									<input id="counter_id" type="hidden" value="<?php echo $this->session->userdata('counter_id'); ?>" />
    									<input id="is_tcs_required"  type="hidden" value="<?php echo set_value('billing[is_tcs_required]',$billing['is_tcs_required']); ?>" />
    									<input id="tcs_tax_per"  name="billing[tcs_tax_per]" type="hidden" value="<?php echo set_value('billing[tcs_tax_per]',$billing['tcs_tax_per']); ?>" />
    									<input id="tcs_min_bill_amt"  type="hidden" value="<?php echo set_value('billing[tcs_min_bill_amt]',$billing['tcs_min_bill_amt']); ?>" />
    									<input id="repair_order_per"  type="hidden" value="<?php echo set_value('billing[repair_order_per]',$billing['repair_percentage']); ?>" />
    									<input id="credit_sales_otp_req"  type="hidden" value="<?php echo set_value('billing[otp_credit_approval]',$billing['otp_credit_approval']); ?>" />
    									<input id="bill_discount_type"  name="billing[bill_discount_type]" type="hidden" value="<?php echo set_value('billing[bill_discount_type]',$billing['bill_discount_type']); ?>" />
    									<input id="bill_split_min_amount"  name="billing[bill_split_min_amount]" type="hidden" value="<?php echo set_value('billing[bill_split_min_amount]',$billing['bill_split_min_amount']); ?>" />
    									<input id="bill_split_max_amount"  name="billing[bill_split_max_amount]" type="hidden" value="<?php echo set_value('billing[bill_split_max_amount]',$billing['bill_split_max_amount']); ?>" />


    									<input type="hidden" id="weightschemecaltype" value="<?php echo set_value('billing[weightschemecaltype]',$billing['weightschemecaltype']); ?>"/>
                                		<input type="hidden" id="weight_scheme_closure_type" value="<?php echo set_value('billing[weight_scheme_closure_type]',$billing['weight_scheme_closure_type']); ?>" />
                                		<input type="hidden" id="billing_emp_select_req" value="<?php echo set_value('billing[billing_emp_select_req]',$billing['billing_emp_select_req']); ?>" />

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

										<input type="hidden" id="branch_id_state" name="" value="<?php echo set_value('billing[cmp_state]',$billing['cmp_state']); ?>" >

										<input type="hidden" id="branch_id_country" name="" value="<?php echo set_value('billing[cmp_country]',$billing['cmp_country']); ?>" >

										<input type="hidden" id="branch_id_city" name="" value="<?php echo set_value('billing[cmp_city]',$billing['cmp_city']); ?>" >

										<input type="hidden" id="branch_pincode" value="" required="">
										<input type="hidden" id="branch_id_village" value="" required="">

										<input type="hidden" id="isMetal" name="billing[isMetal]" value="<?php echo set_value('billing[IsMetalForBilling]',$billing['IsMetalForBilling']);?>"/>
										<input type="hidden" id="allow_bill_type" value="<?php echo $this->session->userdata('allow_bill_type');?>" />


    									<input type="hidden" id="supplier_country"  value="">
                						<input type="hidden" id="supplier_state" value="">

    									<input id="bill_id" name="billing[bill_id]" type="hidden" value="<?php echo set_value('billing[bill_id]',$billing['bill_id']); ?>" />
										<input id="validate_max_cash" type="hidden" />
										<input id="max_cash_amt" type="hidden" />
										<input id="chit_total_cash_amt" type="hidden" value="0" />
										<input id="adv_total_cash_amt" type="hidden" value="0" />
    								</div>
                                </div>
								<p id=cus_info></p>
						        <p id="cusAlert" class="error" align="left"></p>
                            </div>

                        </div>
				 	</div>

			        <p></p>
                    <div class="row"> <!-- Row 2 -->
                            <div class="col-sm-12">
                                <a class="btn btn-app billType  btn-flat bg-green">
                                    <!--<span class="badge bg-yellow">3</span>-->
                                    <!--<i class="fa fa-bullhorn"></i> -->
                                    <input type="radio" class="bill_type_sales" id="bill_typesales" name="billing[bill_type]" value="1" checked tabindex=9> <label for="bill_typesales" class="custom-label"> SALES </label>
                                </a>

                                	<a class="btn btn-app billType btn-flat bg-fuchsia">
										<!--<i class="fa fa-bullhorn"></i> -->
										<input type="radio" class="bill_type_sales" id="bill_type_order_del" name="billing[bill_type]" value="9" <?php echo $billing['bill_type'] == 9 ? 'checked' : '' ?> tabindex=16> <label for="bill_type_order_del" class="custom-label">Order Delivery</label>
									</a>

									<a class="btn btn-app billType btn-flat bg-fuchsia">
										<!--<i class="fa fa-bullhorn"></i> -->
										<input type="radio" class="bill_type_sales" id="bill_type_salesPurch" name="billing[bill_type]" value="2" <?php echo $billing['bill_type'] == 2 ? 'checked' : '' ?> tabindex=10> <label for="bill_type_salesPurch" class="custom-label"> Sales & Purchase</label>
									</a>
                            </div>
                    </div>

					<div align="left"  style="background: #f5f5f5">
                		<ul class="nav nav-tabs" id="billing-tab">
                	      	<li id="item_summary" class="active"><a id="tab_items" href="#pay_items" data-toggle="tab">Item</a></li>
                		  	<li id="tab_tot_summary"><a href="#tot_summary" data-toggle="tab">Total Summary</a></li>
                		  	<li id="tab_make_pay"><a href="#make_pay" data-toggle="tab">Make Payment</a></li>
                		  	<li id="tab_inventory_issu"><a href="#inventory_issue" data-toggle="tab">Packaging Item</a></li>
                		  	<li id="tab_delivery_address"><a href="#customer_delivery_address" data-toggle="tab">Customer Delivery Address</a></li>
                	    </ul>
                	</div>
                	<div class="tab-content">
                		<div class="tab-pane active" id="pay_items">
                		    <div class="box box-default ">
        						<div class="box-body">
                			        <!-- Search Block	 -->
                					<div class="row">
                			    		<div class="col-sm-3 search_esti">
                							<div class="row">
                					    		<div class="col-sm-4">
                					    			<label>EstNo. </label>
                						 		</div>
                						 		<div class="col-sm-8">
                						 			<div class="form-group" >
                							 			<div class="input-group" >
                											<input class="form-control" id="filter_est_no"  tabindex=-1 name="filter_est_no" type="text" placeholder="Esti No." value="" autocomplete='off' tabindex=18/>
                											<span class="input-group-btn">
                						                      <button type="button" id="search_est_no" class="btn btn-default btn-flat" tabindex=19><i class="fa fa-search"></i></button>
                						                    </span>
                										</div>
                										<p id="searchEstiAlert" class="error" align="left"></p>
                									</div>
                						 		</div>
                						 	</div>
                						 </div>

                						<div class="col-md-2">
                						    <div class="row">
                						        <div class="col-sm-8">
                						            <label>Split Type</label><br>
                						            <input type="radio"   name="billing[split_type]" value="1" checked > Auto </label>
                                                    <input type="radio"   name="billing[split_type]" value="2" > Manual </label>
                						        </div>
                						    </div>
                                        </div>

                                        <div class="col-md-2 no_of_split" style="display:none;">
                						    <div class="row">
                						        <div class="col-sm-8">
                						            <label>Sales Split</label><br>
                						            <input type="text" id="no_of_split"  name="billing[no_of_split]" value="" placeholder="value with comma seprator" ></label>
                						        </div>
                						    </div>
                                        </div>

                                         <div class="col-sm-1 search_esti">
                							<div class="row">
                					    		<div class="col-sm-4">
                					    		    <label></label><br>
                					    			<button type="button" id="apply_split" class="btn btn-warning next-tab">Split</button>
                						 		</div>
                						 	</div>
                						 </div>
										 <div class="col-md-2 pu_no_of_split" style="display:none;">
											<div class="row">
												<div class="col-sm-8">
													<label>Purchase Split</label><br>
													<input type="text" id="pu_no_of_split"  name="" value="" placeholder="value with comma seprator" ></label>
												</div>
											</div>
										</div>
										<div class="col-sm-2 pu_no_of_split" style="display:none;">
											<div class="row">
												<div class="col-sm-4">
													<label></label><br>
													<button type="button" id="apply_pu_split" class="btn btn-warning next-tab">Purchase Split</button>
												</div>
											</div>
										</div>



										<div class="col-sm-1 eda_tax_calc" style="padding-left:100px;white-space:nowrap">
                						 	<div class="row">
                					    		<div class="col-sm-4">
                					    			<label for="eda_tax_calc">EDA TAX</label>
                						 		</div>
                						 		<div class="col-sm-8">
                						 			<div class="form-group" >
                							 			<div class="input-group" >
                											<input id="eda_tax_calc" type="checkbox" value=""/>
															<input type="hidden" name=billing[is_eda_tax_calc] id="is_eda_tax_calc" value="1">
                										</div>

                									</div>
                						 		</div>
                						 	</div>
                						</div>

                					</div>
        							<div class="row sale_details" >
        								<div class="col-md-12">
        							       <p class="text-light-blue">Sales Items</p>
        								   <div class="table-responsive">
        									 <table id="billing_sale_details" class="table table-bordered table-striped text-center">
        										<thead>
        										  <tr>
												    <th>#</th>
													<th>Show HUID</th>
													<th>HUID</th>
													<th>Item Type</th>
													<th>Tag Code</th>
													<th>Employee</th>
													<th>Is Partly</th>
        										    <th>No.of Bills</th>
													<th>Image</th>
        											<th>HSN Code</th>
        											<th>Section</th>
																			<th>Product</th>
																			<th>Design</th>
																			<th>Sub Design</th>
																			<th>Pcs</th>
																			<th>Purity</th>
																			<th>Size</th>
																			<th>Est Rate</th>
																			<th>G.Wt</th>
																			<th>L.Wt</th>
																			<th>N.Wt</th>
																			<th>Wast(%)</th>
																			<th>Wast Wt(g)</th>
																			<th>MC Type</th>
																			<th>MC</th>
																			<th>Rate</th>
																			<th>Discount</th>
																			<th>Taxable Amt</th>
																			<th>Tax(%)</th>
																			<th>Tax</th>
																			<th>Charges</th>
																			<th>Other Metal Amt</th>
																			<th>Amount</th>
																			<!-- <th>Partly</th> -->
																			<!-- <th>Tag No</th> -->
																			<th>Order No</th>
																			<th>Est No</th>
																			<th>Action</th>
        										  </tr>
        										</thead>
        										<tbody>
        											<?php if($this->uri->segment(3) == 'edit'){
        										foreach($est_other_item['item_details'] as $ikey => $ival){
        									$other_stone_price=0;
        									$other_stone_wt=0;
        									$stone_data=array();
        									foreach ($ival['stone_details'] as $data) {
        									$other_stone_price	+=	$data['price'];
        									$other_stone_wt	+=	$data['wt'];
        									$stone_data[]=array(
        									'bill_item_stone_id'=>$data['bill_item_stone_id'],
        									'stone_id'			=>$data['stone_id'],
        									'stone_pcs'			=>$data['pieces'],
        									'stone_wt'			=>$data['wt'],
        									'stone_price'		=>$data['price']
        									);
        									}
        									$stone_details=json_encode($stone_data);
        										echo '<tr id="'.$ikey.'">
        										<td>
        										<span>'.$ival['hsn_code'].'</span><input type="hidden" class="bill_det_id" name="sale[bill_det_id][]" value="'.$ival['bill_det_id'].'" /><input type="hidden" class="sale_pro_hsn" name="sale[hsn][]" value="'.$ival['hsn_code'].'" /><input type="hidden" class="sale_type" name="sale[sourcetype][]" value="1" /><input type="hidden" class="sale_item_type" name="sale[itemtype][]" value="'.$ival['item_type'].'" /><input type="hidden" class="is_est_details" value="1" name="sale[is_est_details][]" /><input type="hidden" class="est_itm_id" name="sale[est_itm_id][]" value="'.$ival['est_item_id'].'" /><input type="hidden" class="sale_cal_type" name="sale[calltype][]" value="'.$ival['calculation_based_on'].'" /><input type="hidden" class="sale_metal_type" value="'.$ival['metal_type'].'" /><input type="hidden" class="sale_purity" value="'.$ival['purid'].'"  name="sale[purity][]" /><input type="hidden" class="sale_size" value="'.$ival['size'].'"  name="sale[size][]" /><input type="hidden" class="sale_uom" value="'.$ival['uom'].'"  name="sale[uom][]" /><input type="hidden" class="total_tax" name="sale[total_tax][]"><input type="hidden" class="is_partial"  name="sale[is_partial][]" value="'.$ival['is_partial'].'"/>
        										<input type="hidden" class="total_tax" name="sale[item_total_tax][]">
        										</td>
        										<td>
        											<span>'.$ival['product_name'].'</span><input class="sale_product_id" type="hidden" name="sale[product][]" value="'.$ival['product_id'].'"/>
        										</td>
        										<td><span>'.$ival['design_code'].'</span><input type="hidden" class="sale_design_id" name="sale[design][]" value="'.$ival['design_id'].'" />
        										</td>
        										<td><span>'.$ival['piece'].'</span><input type="hidden" class="sale_pcs" name="sale[pcs][]" value="'.$ival['piece'].'"/>
        										<td><span>'.$ival['gross_wt'].'</span><input type="hidden" class="bill_gross_val" name="sale[gross][]" value="'.$ival['gross_wt'].'" /></td>
        										</td>
        										<td><span>'.$ival['less_wt'].'</span><input type="hidden" class="bill_less_val" name="sale[less][]" value="'.$ival['less_wt'].'" /></td>
        									    <td><span>'.$ival['net_wt'].'</span><input type="hidden" class="bill_net_val" name="sale[net][]" value="'.$ival['net_wt'].'" /></td>
        									    <td><span>'.$ival['wastage_percent'].'</span><input type="hidden" class="bill_wastage" name="sale[wastage][]" value="'.$ival['wastage_percent'].'" />
        									     </td>
        								        <td><span>'.($ival['mc_type']==1 ? 'Per Gram':'Per Piece').'</span><input type="hidden" class="bill_mctype" name="sale[bill_mctype][]" value="'.$ival['mc_type'].'" /><input type="hidden" class="bill_mc" name="sale[mc][]" value="'.$ival['mc_value'].'" />
        								        </td>
        								        <td><input type="number" class="bill_discount" name="sale[discount][]" value="'.$ival['bill_discount'].'" step="any" />
        								        </td>
        								        <td></td>
        										<td><span>'.$ival['tgrp_name'].'</span>
        											<input type="hidden" class="sale_tax_group" name="sale[taxgroup][]" value="'.$ival['tax_group_id'].'" />
        										</td>
        										<td></td>
        										<td>'.((sizeof($ival['stone_details'])>0) ?'<a href="#" onClick="create_new_empty_bill_sales_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a>' :'-').'
        										<input type="hidden" class="stone_details" value="'.$stone_details.'" name="sale[stone_details][]"/>
        									<input type="hidden" class="bill_stone_price" value="'.$other_stone_price.'" />
        									<input type="hidden" class="est_old_stone_val" value="" />
        									<input type="hidden" class="est_old_dust_val" value="" />
        									<input type="hidden" class="bill_material_price" value=""/>
        										</td>
        									<td><input type="number" class="bill_amount" name="sale[billamount][]" value="'.$ival['item_cost'].'" step="any" readonly /><input type="hidden" class="per_grm_amount" name="sale[per_grm][]" value="" step="any" />
        									</td>
        									<td>'.($ival['is_partial']==0 ? 'No': 'Yes').'</td>
        								    <td>
        								    <span>'.$ival['tag_id'].'</span><input type="hidden" class="sale_tag_id" name="sale[tag][]" value="'.$ival['tag_id'].'" />
        								    </td>
        								    <td>-</td>
        									<td>
        										<span>'.$ival['est_item_id'].'</span><input type="hidden" class="sale_est_itm_id" name="sale[estid][]" value="'.$ival['est_item_id'].'" />
        									</td>
        									<td>
        										<a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a>
        									</td>
        										</tr>';
        										}
        										}?>
        										</tbody>
        										<tfoot>
													<tr></tr>
        										</tfoot>
        									 </table>
        								  </div>
        								</div>
        							</div>


        							<div class="row purchase_details">
												<div class="col-md-12">
													<p class="text-light-blue">Purchase Items</p>
													<div class="table-responsive">
														<table id="purchase_item_details" class="table table-bordered table-striped text-center">
															<thead>
															<tr>
																		<th>Metal</th>
																		<th>Metal Type</th>
																		<th>Category</th>
																		<th>Purity</th>
																		<th>Pcs</th>
																		<th>G.Wt</th>
																		<th>Dust.Wt</th>
																		<th>Stone.Wt</th>
																		<th>Wastage(%)</th>
																		<th>Wastage Wt</th>
																		<th>N.Wt</th>
																		<th>Rate</th>
																		<th>Exchange Value(%)</th>
																		<!-- <th>Discount</th> -->
																		<th>Stone</th>
																		<th>Purpose</th>
																		<th>Remark</th>
																		<th>Amount</th>
																		<th>Action</th>
																	</tr>
															</thead>
															<tbody>
																<?php if ($this->uri->segment(3) == 'edit') {
																	foreach ($est_other_item['old_matel_details'] as $ikey => $ival) {
																		$net_wt = 0;
																		$other_stone_price = 0;
																		$other_stone_wt = 0;
																		$stone_data = array();
																		$net_wt = $ival['gross_wt'] - ($ival['dust_wt'] + $ival['stone_wt']);
																		foreach ($ival['stone_details'] as $data) {
																			$other_stone_price	+=	$data['price'];
																			$other_stone_wt	+=	$data['wt'];
																			$stone_data[] = array(
																				'bill_item_stone_id' => $data['bill_item_stone_id'],
																				'stone_id'			=> $data['stone_id'],
																				'stone_pcs'			=> $data['pieces'],
																				'stone_wt'			=> $data['wt'],
																				'stone_price'		=> $data['price']
																			);
																		}
																		$stone_details = json_encode($stone_data);
																		echo '<tr id="' . $ikey . '">
        									<td><span>' . ($ival['purpose'] == 1 ? 'Cash' : 'Exchange') . '</span></td>
        									<td><span>' . ($ival['metal_type'] == 1 ? 'Gold' : 'Silver') . '</span>
        										<input type="hidden" class="is_est_details" value="1" name="purchase[is_est_details][]" />
        										<input type="hidden" class="item_type" name="purchase[itemtype][]" value="' . $ival['item_type'] . '" />
        									    <input type="hidden" class="pur_metal_type"value="' . $ival['metal_type'] . '" name="purchase[metal_type][]"/>
        									    <input type="hidden" class="old_metal_sale_id" value="' . $ival['old_metal_sale_id'] . '" name="purchase[old_metal_sale_id][]"/>
        										</td>
        									<td>-</td>
        									<td><input type="number" class="pur_pcs" name="purchase[pcs][]" value="1" /></td>
        									<td><span>' . $ival['gross_wt'] . '</span><input type="hidden" class="pur_gross_val" name="purchase[gross][]" value="' . $ival['gross_wt'] . '"/></td>
        									<td><span>-</span><input type="hidden" class="pur_less_val" name="purchase[less][]" value="" /></td>
        									<td>
        										<span>' . $net_wt . '</span>
        										<input type="hidden" class="pur_net_val" name="purchase[net][]" value="' . $net_wt . '" />
        										<input type="hidden" class="est_old_dust_val" name="purchase[dust_wt][]" value="' . $ival['dust_wt'] . '" />
        										<input type="hidden" name="purchase[stone_wt][]" class="est_old_stone_val" value="' . $ival['stone_wt'] . '"/>
        									</td>
        									<td><span>' . $ival['wastage_percent'] . '</span><input type="hidden" class="pur_wastage" name="purchase[wastage][]" value="' . $ival['wastage_percent'] . '" />
        									</td>
        									<td><input type="number" class="pur_discount" name="purchase[discount][]" value="' . $ival['bill_discount'] . '" />
        									</td>
        									<td>
        									<a href="#" onClick="create_new_empty_bill_purchase_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a><input type="hidden" class="stone_details"  name="purchase[stone_details][]" value=' . $stone_details . '><input type="hidden" class="other_stone_price" value="' . $other_stone_price . '" /><input type="hidden" class="other_stone_wt" value="' . $other_stone_wt . '" /><input type="hidden" class="bill_material_price" value=""/>
        									</td>
        									<td><input type="number" class="bill_amount" name="purchase[billamount][]" value="' . $ival['amount'] . '" step="any" readonly /><input type="hidden" class="bill_rate_per_grm" name="purchase[rate_per_grm][]" value="' . $ival['rate_per_gram'] . '" step="any" readonly /></td>
        									<td><span>' . $ival['est_id'] . '</span><input type="hidden" class="pur_est_id" name="purchase[estid][]" value="' . $ival['est_id'] . '" /></td>
        									<td>-</td>
        									</tr>';
																	}
																} ?>
															</tbody>
															<tfoot>
																<tr>
																	<td></td>
																	<td></td>
																	<td></td>
																	<td style="text-align: right;" class="pur_det_pcs"></td>
																	<td style="text-align: right;" class="pur_det_gwt"></td>
																	<td style="text-align: right;" class="pur_det_lwt"></td>
																	<td style="text-align: right;" class="pur_det_nwt"></td>
																	<td></td>
																	<td></td>
																	<td style="text-align: right;" class="pur_det_dis"></td>
																	<td></td>
																	<td style="text-align: right;" class="pur_det_purvalue"></td>
																	<td></td>
																	<td></td>
																</tr>
															</tfoot>
														</table>
													</div>
												</div>
											</div>
        				            <div class="row split_sale_details">
        								<div class="col-md-12">
        							       <p class="text-light-blue">Sales Items</p>
        								   <div class="table-responsive">
        								       <input type="hidden" id="row_active_id">
        									 <table id="billing_split_sale_details" class="table table-bordered table-striped text-center">
        										<thead>
        										  <tr>
        											<th>S.No</th>

        											<th>Customer</th>
        											<th>KYC</th>
        											<th>Is Credit</th>
        											<th>HSN Code</th>
        											<th>Product</th>
        											<th>Design</th>
        											<th>Pcs</th>
        											<th>G.Wt</th>
        											<th>L.Wt</th>
        											<th>N.Wt</th>
        											<th>Wast(%)</th>
        											<th>Wast Wt(g)</th>
        											<th>MC</th>
        											<th>Discount</th>
        											<th>Taxable Amt</th>
        											<th>Tax(%)</th>
        											<th>Tax</th>
        											<th>Charges</th>
        											<th>Stone</th>
        											<th>Sales Amount</th>
        											<th>Purchase</th>
        											<th>Amount</th>
        											<th>Recd Amount</th>
        											<th>Payment</th>
        											<th>Due Date</th>
        											<th>Partly</th>
        											<th>Tag No</th>
        											<th>Order No</th>
        											<th>Est No</th>
        											<!--<th>Advance Paid</th> -->
        											<th>Action</th>
        										  </tr>
        										</thead>
        										<tbody>
        											<?php if($this->uri->segment(3) == 'edit'){
        										foreach($est_other_item['item_details'] as $ikey => $ival){
        									$other_stone_price=0;
        									$other_stone_wt=0;
        									$stone_data=array();
        									foreach ($ival['stone_details'] as $data) {
        									$other_stone_price	+=	$data['price'];
        									$other_stone_wt	+=	$data['wt'];
        									$stone_data[]=array(
        									'bill_item_stone_id'=>$data['bill_item_stone_id'],
        									'stone_id'			=>$data['stone_id'],
        									'stone_pcs'			=>$data['pieces'],
        									'stone_wt'			=>$data['wt'],
        									'stone_price'		=>$data['price']
        									);
        									}
        									$stone_details=json_encode($stone_data);
        										echo '<tr id="'.$ikey.'">
        										<td>
        										<span>'.$ival['hsn_code'].'</span><input type="hidden" class="bill_det_id" name="sale[bill_det_id][]" value="'.$ival['bill_det_id'].'" /><input type="hidden" class="sale_pro_hsn" name="sale[hsn][]" value="'.$ival['hsn_code'].'" /><input type="hidden" class="sale_type" name="sale[sourcetype][]" value="1" /><input type="hidden" class="sale_item_type" name="sale[itemtype][]" value="'.$ival['item_type'].'" /><input type="hidden" class="is_est_details" value="1" name="sale[is_est_details][]" /><input type="hidden" class="est_itm_id" name="sale[est_itm_id][]" value="'.$ival['est_item_id'].'" /><input type="hidden" class="sale_cal_type" name="sale[calltype][]" value="'.$ival['calculation_based_on'].'" /><input type="hidden" class="sale_metal_type" value="'.$ival['metal_type'].'" /><input type="hidden" class="sale_purity" value="'.$ival['purid'].'"  name="sale[purity][]" /><input type="hidden" class="sale_size" value="'.$ival['size'].'"  name="sale[size][]" /><input type="hidden" class="sale_uom" value="'.$ival['uom'].'"  name="sale[uom][]" /><input type="hidden" class="total_tax" name="sale[total_tax][]"><input type="hidden" class="is_partial"  name="sale[is_partial][]" value="'.$ival['is_partial'].'"/>
        										<input type="hidden" class="total_tax" name="sale[item_total_tax][]">
        										</td>
        										<td>
        											<span>'.$ival['product_name'].'</span><input class="sale_product_id" type="hidden" name="sale[product][]" value="'.$ival['product_id'].'"/>
        										</td>
        										<td><span>'.$ival['design_code'].'</span><input type="hidden" class="sale_design_id" name="sale[design][]" value="'.$ival['design_id'].'" />
        										</td>
        										<td><span>'.$ival['piece'].'</span><input type="hidden" class="sale_pcs" name="sale[pcs][]" value="'.$ival['piece'].'"/>
        										<td><span>'.$ival['gross_wt'].'</span><input type="hidden" class="bill_gross_val" name="sale[gross][]" value="'.$ival['gross_wt'].'" /></td>
        										</td>
        										<td><span>'.$ival['less_wt'].'</span><input type="hidden" class="bill_less_val" name="sale[less][]" value="'.$ival['less_wt'].'" /></td>
        									    <td><span>'.$ival['net_wt'].'</span><input type="hidden" class="bill_net_val" name="sale[net][]" value="'.$ival['net_wt'].'" /></td>
        									    <td><span>'.$ival['wastage_percent'].'</span><input type="hidden" class="bill_wastage" name="sale[wastage][]" value="'.$ival['wastage_percent'].'" />
        									     </td>
        								        <td><span>'.($ival['mc_type']==1 ? 'Per Gram':'Per Piece').'</span><input type="hidden" class="bill_mctype" name="sale[bill_mctype][]" value="'.$ival['mc_type'].'" /><input type="hidden" class="bill_mc" name="sale[mc][]" value="'.$ival['mc_value'].'" />
        								        </td>
        								        <td><input type="number" class="bill_discount" name="sale[discount][]" value="'.$ival['bill_discount'].'" step="any" />
        								        </td>
        								        <td></td>
        										<td><span>'.$ival['tgrp_name'].'</span>
        											<input type="hidden" class="sale_tax_group" name="sale[taxgroup][]" value="'.$ival['tax_group_id'].'" />
        										</td>
        										<td></td>
        										<td>'.((sizeof($ival['stone_details'])>0) ?'<a href="#" onClick="create_new_empty_bill_sales_stone_item($(this).closest(\'tr\'));" class="btn btn-success"><i class="fa fa-plus"></i></a>' :'-').'
        										<input type="hidden" class="stone_details" value="'.$stone_details.'" name="sale[stone_details][]"/>
        									<input type="hidden" class="bill_stone_price" value="'.$other_stone_price.'" />
        									<input type="hidden" class="est_old_stone_val" value="" />
        									<input type="hidden" class="est_old_dust_val" value="" />
        									<input type="hidden" class="bill_material_price" value=""/>
        										</td>
        									<td><input type="number" class="bill_amount" name="sale[billamount][]" value="'.$ival['item_cost'].'" step="any" readonly /><input type="hidden" class="per_grm_amount" name="sale[per_grm][]" value="" step="any" />
        									</td>
        									<td>'.($ival['is_partial']==0 ? 'No': 'Yes').'</td>
        								    <td>
        								    <span>'.$ival['tag_id'].'</span><input type="hidden" class="sale_tag_id" name="sale[tag][]" value="'.$ival['tag_id'].'" />
        								    </td>
        								    <td>-</td>
        									<td>
        										<span>'.$ival['est_item_id'].'</span><input type="hidden" class="sale_est_itm_id" name="sale[estid][]" value="'.$ival['est_item_id'].'" />
        									</td>
        									<td>
        										<a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a>
        									</td>
        										</tr>';
        										}
        										}?>
        										</tbody>
        										<tfoot>
        											<tr style="font-weight:bold;">
        											    <td>TOTAL</td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td class="total_bill_split_pcs"></td>
        											    <td class="total_bill_split_gwt"></td>
        											    <td class="total_bill_split_lwt"></td>
        											    <td class="total_bill_split_nwt"></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td></td>
        											    <td class="total_bill_split_amount"></td>
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

									<div class="row split_purchase_details">
										<div class="col-md-12">
											<p class="text-light-blue">Split Purchase Items</p>
												<div class="table-responsive">
													<table id="bill_split_pu_details" class="table table-bordered table-striped text-center">
														<thead>
															<tr>
																<th>S.No</th>
																<th>Product</th>
																<th>Design</th>
																<th>Pcs</th>
																<th>G.Wt</th>
																<th>L.Wt</th>
																<th>N.Wt</th>
																<th>Wastage(%)</th>
																<th>Wastage.Wt(%)</th>
																<th>Discount</th>
																<th>Stone</th>
																<th>Amount</th>
																<th>Est No</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
														<tfoot>
															<tr>
																<td></td>
																<td></td>
																<td></td>
																<td style="text-align: right;" class="split_pur_det_pcs"></td>
																<td style="text-align: right;" class="split_pur_det_gwt"></td>
																<td style="text-align: right;" class="split_pur_det_lwt"></td>
																<td style="text-align: right;" class="split_pur_det_nwt"></td>
																<td></td>
																<td></td>
																<td style="text-align: right;" class="split_pur_det_dis"></td>
																<td></td>
																<td style="text-align: right;" class="split_pur_det_purvalue"></td>
																<td></td>

															</tr>
														</tfoot>
													</table>
												</div>
											</div>
									</div>

                					<div class="row">
                						<div class="col-md-6">
                							<div class="box box-default stone_details" <?php echo !empty($est_other_item['stone_details']) ? '' : 'style="display:none;"' ;?>>
                								<div class="box-body">
                								    <p class="text-light-blue">Stone Details</p>
                									<div class="row">
                									  <div class="box-tools pull-right">
                										<button type="button" id="create_stone_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
                									  </div>
                									</div>
                									<div class="row">
                										<div class="box-body">
                										   <div class="table-responsive">
                											 <table id="estimation_stone_details" class="table table-bordered table-striped text-center">
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
                													<?php if($this->uri->segment(3) == 'edit'){
                														foreach($est_other_item['stone_details'] as $ikey => $ival){
                																echo '<tr><td><div>'.$ival['stone_name'].'</div><input type="hidden" class="stone_id" name="est_stones[stone_id][]" value="'.$ival['stone_id'].'" /></td><td><input type="number" class="stone_pcs" name="est_stones[stone_pcs][]" value="'.$ival['pieces'].'" /></td><td><input class="stone_wt" type="number" name="est_stones[stone_wt][]" value="'.$ival['wt'].'" /></td><td><input type="number" class="stone_price" name="est_stones[stone_price][]" value="'.$ival['price'].'" /></td></tr>';
                														}
                													}?>
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
                					</div>
                                    <div align="right" style="display:none;">
                                        <button type="button" class="btn btn-warning next-tab">Next</button>
                                    </div>
                        		</div>
                    		</div>
                		</div>
                		<div class="tab-pane" id="tot_summary">
                		    <div class="row">
                		        <div class="col-sm-12">
        							<div class="box box-default total_summary_details" >
        								<div class="box-body">
        									<div class="row">
        										<div class="col-md-offset-1 col-md-10">
        										   <div class="table-responsive">
        											 <table id="total_summary_details" class="table table-bordered table-striped">
        												<thead>
        													<tr>
        														<th>Weight</th>
        														<th>(Grms)</th>
        														<th>Amount</th>
        														<th>INR</th>
        													</tr>
        												</thead>
        												<tbody>
        													<tr>
        														<td>Sale Weight</td>
        														<td><span class="summary_lbl summary_sale_weight"></span></td>
        														<td>Taxable Sale Amount</td>
        														<td><span class="summary_lbl summary_sale_amt"></span></td>
        													</tr>
        													<tr style="display:none;">
        														<td></td>
        														<td><span class=""></span></td>
        														<td>TDS</td>
        														<td>
                                                                    <div class="input-group" style="width: 40%;">
                                                                    <input class="form-control tds_percent summary_lbl" type="number" name="billing[tds_percent]" id="tds_percent" tabindex="21"  />
                                                                    <span class="input-group-btn"><input type="number" class="form-control tds_tax_value summary_lbl" name="billing[tds_tax_value]" id="tds_tax_value" tabindex="22" style="width:100px;" readonly /></span>
                                                                    </div>
                                    			                </td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td class="text-right">CGST</td>
        														<td><span class="summary_lbl sales_cgst"></span>
        															<input type="hidden" id="cgst" class="cgst" name="billing[cgst]">
        														</td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td class="text-right">SGST</td>
        														<td><span class="summary_lbl sales_sgst"></span>
        														<input type="hidden" id="sgst" class="sgst" name="billing[sgst]">
        														</td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td class="text-right">IGST</td>
        														<td><span class="summary_lbl sales_igst"></span>
        														<input type="hidden" id="igst" class="igst" name="billing[igst]">
        														</td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td>Sale Amount</td>
        														<td><span class="summary_lbl sale_amt_with_tax"></span></td>
        													</tr>
        													<tr style="display:none;">
        														<td></td>
        														<td></td>
        														<td class="text-right">TCS</td>
        														<td>
        														    <div class="input-group" style="width: 40%;">
                                                                    <input class="form-control tcs_percent summary_lbl" type="number" name="billing[tcs_percent]" id="tcs_percent" tabindex="21"  />
                                                                    <span class="input-group-btn"><input type="number" class="form-control tcs_tax_amt summary_lbl" name="billing[tcs_tax_value]" id="tds_tax_value" tabindex="22" style="width:100px;" readonly /></span>
                                                                    </div>
        													</tr>
        													<tr style="display:none;">
        														<td></td>
        														<td></td>
        														<td class="text-right">Other Charges</td>
        														<td><div class="input-group" style="width: 40%;">
                                    									<input id="other_charges_taxable_amount"  name="billing[other_charges_amount]" class="form-control summary_lbl custom-inp add_other_charges" type="number" step="any" readonly tabindex="19" />
                                    									<span class="input-group-addon input-sm add_other_charges">+</span>
                                    									<input type="hidden" id="other_charges_details"  name="billing[other_charges_details]" />
                                    								</div>
                                    							</td>
        													</tr>
        													<tr style="display:none;">
        														<td></td>
        														<td></td>
        														<td class="text-right">CHARGES  TDS</td>
        														<td>
        														    <div class="input-group" style="width: 40%;">
                                                                    <input class="form-control tds_percent summary_lbl" type="number" name="billing[tds_percent]" id="tds_percent" tabindex="21"  />
                                                                    <span class="input-group-btn"><input type="number" class="form-control charges_tds_tax_value summary_lbl" name="billing[tds_tax_value]" id="tds_tax_value" tabindex="22" style="width:100px;" readonly /></span>
                                                                    </div>
                                    							</td>
        													</tr>
        													<tr style="display:none;">
        														<td></td>
        														<td></td>
        														<td>Other Charges CGST</td>
        														<td><span class="summary_lbl other_charges_cgst"></span></td>
        													</tr>
        													<tr style="display:none;">
        														<td></td>
        														<td></td>
        														<td>Other Charges SGST</td>
        														<td><span class="summary_lbl other_charges_sgst"></span></td>
        													</tr>
        													<tr style="display:none;">
        														<td></td>
        														<td></td>
        														<td>Other Charges IGST</td>
        														<td><span class="summary_lbl other_charges_igst"></span></td>
        													</tr>
        													<tr>
        														<td>Purchase Weight</td>
        														<td><span class="summary_lbl summary_pur_weight"></span></td>
        														<td>Purchase Amount</td>
        														<td><span class="summary_lbl summary_pur_amt"></span></td>
        													</tr>
        													<tr>
        														<td>Advance Paid Weight</td>
        														<td><span class="summary_lbl summary_adv_paid_wt"></td>
        														<td>Advance Paid Amount</td>
        														<td><span class="summary_lbl summary_adv_paid_amt"></span></td>
        													</tr>

        													<tr>
        														<td>Chit Paid Weight</td>
        														<td><span class="summary_lbl summary_chit_paid_wt"></td>
        														<td>Chit Paid Amount</td>
        														<td><span class="summary_lbl summary_chit_paid_amt"></span></td>
        													</tr>

        													<tr>
        														<td></td>
        														<td></td>
        														<td>Repair Amount</td>
        														<td><span class="summary_lbl summary_repair_amt"></span></td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td>Credit Due Amount
        														<input type="hidden" name="billing[credit_due_amt]" id="summary_credit_due_amt">
        														<input type="hidden" name="billing[credit_ret_amt]" id="summary_credit_due_ret_amt">
        														<input type="hidden" name="billing[return_paid_amount]" id="return_paid_amount">
        														</td>
        														<td><span class="summary_lbl summary_credit_due_ret_amt"></span></td>
        													</tr>
        													<tr>
        														<td>Return Weight</td>
        														<td><span class="summary_lbl summary_sale_ret_weight"></span></td>
        														<td>Return Amount</td>
        														<td><span class="summary_lbl summary_sale_ret_amt"></span></td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td>Credit Amount</td>
        														<td><span class="form-control summary_credit_amt" style="width: 200px;"></td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td class="text-right">Discount</td>
        														<td>
                													<div class="form-group">
                														<div class="input-group" id="sale_discount">
                															<input type="number" class="form-control summary_discount_amt summary_lbl" id="summary_discount_amt" name="billing[discount]" style="width: inherit;"><input type="hidden" id="total_discount">
                															<span class="input-group-btn">
                										                      <button type="button" id="disc_apply" class="btn btn-default btn-flat"  style="display:none;">Apply</button>

                										                    </span>
                										                    <span class="input-group-btn"> <!-- Bill Discount Reset Button-->
                    										                      <button type="button" id="disc_reset" class="btn btn-primary btn-flat" title="Discount Reset">Reset</button>
                    										                    </span>
                														</div>
                													</div>
            														<div class="input-group" id="credit_discount">
            															<input type="number" class="form-control credit_discount_amt" id="credit_discount_amt" name="billing[credit_discount_amt]"><input type="hidden" id="credit_discount_amt_value">
            														</div>
        														</td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td class="text-right">Handling Charges</td>
        														<td>
        														<div class="input-group">
        															<input type="number" class="form-control handling_charges summary_lbl" id="handling_charges" name="billing[handling_charges]">
        														</div>
        														</td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td class="text-right">Final Price</td>
        														<td><input type="number" class="form-control total_cost summary_lbl" id="total_cost" name="billing[total_cost]" value="" required style="width: fit-content;"><input type="hidden" id="total_payment_amount"></td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td class="text-right">Round Off</td>
        														<td><span class="summary_round_off"></span>
        														<input type="hidden" name="billing[round_off]" id="round_off">
        														</td>
        													</tr>
        													<tr>
        														<td></td>
        														<td></td>
        														<td></td>
        														<td><p class="error "><span id="paymentAlert"></span></p></td>
        													</tr>
        													<tr  style="color: #FF0000;font-weight:bold;">
        														<td class="" style="font-size:14px;"></td>
        														<td></td>
        														<td class="text-right gift_row" >Gift Voucher Worth</td>
        														<td><span class="summary_gift_voucher"></span>
        														<input class="form-control" id="gift_voucher_amt"  name="billing[gift_voucher_amt]" type="hidden" />
        														</td>
        													</tr>
        												</tbody>
        												<tfoot>
        													<tr></tr>
        												</tfoot>
        											 </table>
        											 <div align="right">
        											    <button type="button" class="btn btn-warning next-tab">Next</button>
        											 </div>
        										  </div>
        										</div>
        									</div>
        								</div>
        							</div>
        							<div class="box box-info summary_adv_details" style="display:none;">
        								<div class="box-header with-border">
        								  <h4 class="box-title">Total Summary</h4>
        								  <div class="box-tools pull-right">
        									<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        								  </div>
        								</div>
        								<div class="box-body">
        									<div class="row">
        										<div class="box-body">
        										   <div class="table-responsive">
        											 <table id="total_summary_details" class="table table-bordered table-striped">
        												<thead>
        													<tr>
        														<th>Balance</th>
        														<th></th>
        														<th>Received Advance</th>
        														<th></th>
        														<th>Store As</th>
        													</tr>
        												</thead>
        												<tbody>
        													<tr>
        														<td>Weight (Grams)</td>
        														<td><span class="adv_blc_wt"></span></td>
        														<td>Weight (Grams)</td>
        														<td><span class="adv_rcd_wt"></span>
        														<input type="hidden" class="max_wt" name="">
        														</td>
        														<td>
        														    <input type="hidden" name="billing[id_customerorder]" id="id_customerorder">
        															<input type="radio" name="billing[pur_store_as]" value="1" checked disabled>Amount&nbsp;&nbsp;
        															<input type="radio" name="billing[pur_store_as]" value="2" disabled>Weight
        														</td>
        													</tr>
        													<tr>
        														<td>Amount(INR)</td>
        														<td><span class="adv_blc_amt"></span></td>
        														<td>Amount(INR)</td>
        														<td>
        															<input type="text" class="adv_amt" name="billing[bill_amount]">
        														</td>
        														<td>
        															<input type="radio"  value="1" name="billing[sale_store_as]" checked disabled>Amount
        																&nbsp;&nbsp;
        																<input type="radio" value="2"  name="billing[sale_store_as]" disabled>Weight
        														</td>
        													</tr>
        													<tr style="font-weight: bold;">
        														<td>Advance Paid</td>
        														<td></td>
        														<td></td>
        														<td></td>
        														<td></td>
        													</tr>
        													<tr>
        														<td>Weight (Grmas)</td>
        														<td><span class="adv_paid_wt"></span></td>
        														<td></td>
        														<td></td>
        														<td></td>
        													</tr>
        													<tr>
        														<td>Amount (INR)</td>
        														<td><span class="adv_paid_amt"></span></td>
        														<td></td>
        														<td></td>
        														<td>
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
                		    </div>
                		</div>
                		<div class="tab-pane" id="make_pay">
                		    <div class="row">
        						<div class="col-sm-12">
        							<div class="box box-default payment_blk">
        								<div class="box-body">
        									<div class="row">
        										<div class="col-sm-5">
    											   <div class="table-responsive">
    												 <table id="payment_modes" class="table table-bordered">
    													<thead>
    													</thead>
    													<tbody>
    														<tr>
    															<td class="text-right"><b class="custom-label">Pay</b></td>
    															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
    															<td>
    																<input type="number" class="form-control pay_to_cus" name="billing[pay_to_cus]" value="" required readonly>
    															</td>
    														</tr>
    														<tr>
    															<td class="text-right"><b class="custom-label">Received</b></td>
    															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
    															<td>
    																<input type="number" class="form-control receive_amount" name="billing[tot_amt_received]" value="<?php echo set_value('billing[tot_amt_received]',isset($billing['tot_amt_received'])?$billing['pan_no']:0); ?>" >
    															</td>
    														</tr>
    														<tr>
    															<td class="text-right"><b class="custom-label">Returns</b></td>
    															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
    															<td>
    																<input type="number" class="form-control benifit_amount" name="billing[benifits]" value="" >
    															</td>
    														</tr>
    														<tr style="display:none">
    															<td class="text-right"><b class="custom-label">Calculate From</b></td>
    															<th class="text-right"></th>
    															<td>
    																<input type="radio" id="rate_calc1" value="1" name="billing[rate_calc]" checked >Gold
    																&nbsp;
    																<input type="radio" id="rate_calc2" value="2"  name="billing[rate_calc]" >Silver
    															</td>
    														</tr>

    														<tr>
    															<td class="text-right"><b class="custom-label">Image</b></td>
    															<th class="text-right"></th>
    															<td>
    															<input type="file" id="pan_images">
    															<input type="hidden"
    															id="panimg" name="billing[pan_img]">
    															<div id="pan_preview" ></div>
    															</td>
    														</tr>
    													</tbody>
    												 </table>
    											  </div>
    											  <label>Remark</label>
    											  <textarea class="form-control" id="remark" name="billing[remark]" <?php echo set_value('billing[remark]',isset($billing['remark'])?$billing['remark']:NULL); ?> rows="5" cols="500"> </textarea>
        										</div>
        										<div class="col-sm-5">
        										    <div class="table-responsive">
    												 <table id="payment_modes" class="table table-bordered table-striped">
    												     <tbody>
    														<?php if($billing['is_credit_enable']==1){?>
    														<tr style="display:none;">
    															<td class="text-right">Is Credit</td>
    															<td></td>
    															<td>
    																<input type="radio" id="is_credit_no" class="is_credit" name="billing[is_credit]" value="0" <?php echo $billing['is_credit'] == 0 ? 'checked':'' ?>> <label for="is_credit_no">&nbsp;No</label> &nbsp;&nbsp;
    																<input type="radio" id="is_credit_yes" class="is_credit" name="billing[is_credit]" value="1" <?php echo $billing['is_credit'] == 1 ? 'checked':'' ?>><label for="is_credit_yes">&nbsp;Yes</label>
    															</td>
    														</tr>
															<tr style="display:none;">
																<td class="text-right">Is Tobe</td>
																<td></td>
																<td>
																	<input type="radio" id="is_to_be_no" class="is_to_be" name="billing[is_to_be]" value="0" <?php echo $billing['is_to_be'] == 0 ? 'checked':'' ?>> <label for="is_to_be_no">&nbsp;No</label> &nbsp;&nbsp;
																	<input type="radio" id="is_to_be_yes" class="is_to_be" name="billing[is_to_be]" value="1" <?php echo $billing['is_to_be'] == 1 ? 'checked':'' ?>><label for="is_to_be_yes">&nbsp;Yes</label>
																</td>
															</tr>

    														<tr style="display:none;">
    															<td class="text-right">Credit Due Date</td>
    															<td></td>
            													<td>
            														<input class="form-control" id="credit_due_date" data-date-format="dd-mm-yyyy hh:mm:ss" name="billing[credit_due_date]" value="" type="text" placeholder="Credit Due Date" disabled/>
            													</td>
    														</tr>
    														<?php }?>
    														<tr style="display:none;">
    															<td class="text-right">Chit Utilization</td>
    															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
    															<td>
    																<span id="tot_chit_amt"></span>
    																<a class="btn bg-olive btn-xs pull-right" id="chit_util_modal" href="#" data-toggle="modal" ><b>+</b></a>
    																<input type="hidden"id="chit_details" name="billing[chit_uti]" value="">
    															</td>
    														</tr>
    														<tr style="display:none;">
    															<td class="text-right">Gift Voucher</td>
    															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
    															<td>
    																<span id="tot_voucher_amt"></span>
    																<a class="btn bg-olive btn-xs pull-right" id="gift_voucher_modal" href="#" data-toggle="modal" data-target="#gv-confirm-add" disabled><b>+</b></a>
    																<input type="hidden" id="giftVoucher_details" name="billing[vocuher]" value="">
    															</td>
    														</tr>
    														<tr>
    															<td class="text-right">CSH</td>
    															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
    															<td class="mode_<?php echo $mode['short_code']; ?>"><input class="form-control" id="total_bill_split_cash_amt" type="number" placeholder="CSH AMOUNT" value="" disabled/></td>
    														</tr>
															<tr>
    															<td class="text-right">CARD</td>
    															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
    															<td class="mode_<?php echo $mode['short_code']; ?>"><input class="form-control" id="total_bill_split_card_amt" type="number" placeholder="Enter Amount" value="" disabled/></td>
    														</tr>
															<tr>
    															<td class="text-right">NB</td>
    															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
    															<td class="mode_<?php echo $mode['short_code']; ?>"><input class="form-control" id="total_bill_split_net_banking_amt" type="number" placeholder="Enter Amount" value="" disabled/></td>
    														</tr>
															<tr>
    															<td class="text-right">CHQ</td>
    															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
    															<td class="mode_<?php echo $mode['short_code']; ?>"><input class="form-control" id="total_bill_chq_amt" type="number" placeholder="Enter Amount" value="" disabled/></td>
    														</tr>
															<tr>
    															<td class="text-right">ADVANCE ADJ</td>
    															<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
    															<td class="mode_<?php echo $mode['short_code']; ?>"><input class="form-control" id="total_bill_split_adv_adj_amt" type="number" placeholder="CSH AMOUNT" value="" disabled/></td>
    														</tr>

    													</tbody>
    													<tfoot>
    														<tr>
    															<th class="text-right custom-label">Total</th>
    															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
    															<th class="total_bill_split_recd_amt"></th>
    														</tr>
    														<tr>
    															<th class="text-right custom-label">Balance</th>
    															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
    															<th class="total_bill_split_blc_amt"></th>
    														</tr>
    														<tr style="display:none;">
    															<th class="text-right custom-label">Refund</th>
    															<th class="text-right"><input type="checkbox" id="chit_refund" name="billing[chit_refund]"></th>
    															<th class="chit_refund_amt"><input type="hidden" id="chit_refund_amt" name="billing[chit_refund_amt]" value=""></th>
    														</tr>

        													<tr style="display:none;">
    															<th class="text-right">Keep it As</th>
    															<!-- <th class="text-right"><input type="checkbox" id="chit_refund" name="billing[chit_refund]"></th> -->
    															<th></th>
    															<th>
    															<input type="radio" id="advance_no" name="billing[make_as_advance]" value="0" checked> <label for="advance_no" disabled>&nbsp;Refund</label>
    															<input type="radio" id="advance_yes" name="billing[make_as_advance]" value="1" disabled> <label for="advance_yes" >&nbsp;Advance</label> &nbsp;&nbsp;
    															<input type="radio" id="chit_deposit" name="billing[make_as_advance]" value="2" disabled> <label for="chit_deposit" >&nbsp;Chit Deposit</label> &nbsp;&nbsp;
    															<input type="hidden" id="chit_deposit_acc_details" name="billing[chit_deposit_details]" value="" >
    															</th>
    															<!-- <th class="chit_refund_amt"><input type="hidden" id="chit_refund_amt" name="billing[chit_refund_amt]" value=""></th> -->
    														</tr>
    														<tr style="display:none;">
    														<th class="text-right">Advance Amount</th>
    															<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
    															<th> <input type="text" class="form-control" id="advance_amount" name="billing[advance_deposit]" placeholder="Enter Advance Amount" disabled>
    															</th>
    														</tr>

    													</tfoot>
    												 </table>
    											  </div>
        										</div>
        									</div>
        									<div class="row">
        									    <div class="col-sm-12" align="center">
        									        <?php if($this->uri->segment(3) != 'edit'){?>
                            						<button type="button" id="pay_submit" class="btn btn-primary" disabled>Save</button>
                            						<?php }?>
                            						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
        									    </div>
        									</div>
        								</div>
        							</div>
        						</div>
        					</div>

        				 	<div class="row"  style="display:none">
        			    		<div class="col-sm-2">
        			    			<label>Select Delivery</label>
        				 		</div>
        				 		<div class="col-sm-10">
        				 			<div class="form-group">
        					 			<div class="input-group">
        					 				<select class="form-control" id="select_delivery" name="billing[id_delivery]" style="width:100%;"></select>
        								</div>
        							</div>
        				 		</div>
        				 	</div>
                		</div>

                		<div class="tab-pane" id="inventory_issue">
                		    <div class="row">
        						<div class="col-sm-6">
        							<div class="box box-default payment_blk">
        								<div class="box-body">
        									<div class="row">
        										<div class="col-md-12">
                                                    <div class="box-header with-border">
                                                	  <h3 class="box-title">Packaging  Box</h3>
                                                    	  <div class="box-tools pull-right">
                                                    		<button type="button" class="btn btn-primary" id="add_new_inv">Add</button>
                                                    	  </div>
                                                	</div>
                                        	      <div class="row">
                                        				<div class="box-body">
                                        				   <div class="table-responsive">
                                        					 <table id="estimation_other_inv_details" class="table table-bordered table-striped text-center">
                                        						<thead>
                                        						  <tr>
                                        							<th>Item Name</th>
                                        							<th>No of Pcs</th>
                                        							<th>Image</th>
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
                                                </div>
        									</div>

        								</div>
        							</div>
        						</div>
        					</div>
                		</div>

                		<div class="tab-pane" id="customer_delivery_address">
                		    <div class="row">
        						<div class="col-sm-6">
        							<div class="box box-default">
        								<div class="box-body">
        									<div class="row">
        										<div class="col-md-12">
                                        	        <div class="row">
                                    					<div class="col-md-12">
                                    						<div class="col-md-6">
                                    							<label for="">BILLING  ADDRESS</label></br>
                                    							<span id="cus_reg_address"></span>
                                    						</div>
                                    						<div class="col-md-6">
                                    							<label for="">DELIVERY  ADDRESS<span class="error"></span></label></br>
                                    							<span id="biling_address_as_delivery_addr"></span>

                                    						</div>
                                    					</div>
                                    				</div>

                                    				</br>
                                    				<div class="row add_type" style="display:none;">
                                    					<div class="col-md-offset-1">
                                    						 <input type="checkbox" id="make_delivery_address"  value="1" checked><span>&nbsp;Use this Billing Address as Delivery Address</span>
                                    						 <input type="hidden" name="billing[delivery_address_type]" id="delivery_address_type" value="1">
                                    					</div>
                                    				</div></br>
                                    				<div class="row" id="my_addr" style="display: none;">
                                    				 	<div class="form-group">
                                    						<label class="col-md-3 col-md-offset-1 ">My Address</label>
                                    						<div class="col-md-12">
                                    							<div class="col-md-6">
                                    								<div class="form-group">
                                    									<select class="form-control" id="select_myaddr" name="billing[id_delivery_address]" style="width:100%;"></select>
                                    								</div>
                                    				    		</div>
                                    					    	<div class="col-md-6">
                                    					    		<button type="button" class="btn btn-primary" data-toggle="tooltip" title="Add New Delivery Address" id="add_new_delivery_address"><i class="fa fa-plus-circle"></i></button>
                                    					    	</div>
                                    						</div>

                                    					</div>
                                    				</div></br>

                                                </div>
        									</div>

        								</div>
        							</div>
        						</div>


        						<div class="col-sm-6">
        							<div class="box box-default">
        								<div class="box-body">
        									<div class="row">
        										<div class="col-md-12">
                                        	        <div class="row new_address" style="display: none;">
                                    				 	<div class="form-group">
                                    				 	    <div class="row">
                                    				 	        <div class="col-md-4">
                                        				 			<div class="form-group">
                                        				 			    <label>Address Name<span class="error">*</span></label>
                                        					 		   <input type="text" class="form-control" name="billing[del_address_name]" id="del_address_name" placeholder="Enter Address Name">
                                        							</div>
                                        				    	</div>

                                        				    	<div class="col-md-4">
                                        				 			<div class="form-group">
                                        				 			    <label>Select Country<span class="error">*</span></label>
                                        					 		   <select class="form-control" id="del_country" name="billing[del_country]" style="width:100%;"></select>
                                        					 		    <input type="hidden" name="cus[id_country]" id="delivery_country">
                                        							</div>
                                        				    	</div>

                                        				 	    <div class="col-md-4">
                                        				 			<div class="form-group">
                                        				 			    <label>Select State<span class="error">*</span></label>
                                        					 		    <select class="form-control" id="del_state" name="billing[del_state]" style="width:100%;"></select>
                                        						        <input type="hidden" name="cus[id_state]" id="delivery_state">
                                        							</div>
                                        				    	</div>
                                        				    </div>
                                        				    <div class="row">
                                        				        <div class="col-md-4">
                                        				 			<div class="form-group">
                                        				 			    <label>Select City<span class="error">*</span></label>
                                        					 		    <select class="form-control" id="del_city" name="billing[del_city]"  style="width:100%;"></select>
                                        						        <input type="hidden" name="cus[id_city]" id="delivery_city">
                                        							</div>
                                        				    	</div>

                                        				    	<div class="col-md-4">
                                        				 			<div class="form-group">
                                        				 			    <label>Address1<span class="error">*</span></label>
                                        					 		    <input class="form-control" id="del_address1" name="billing[del_address1]" name="customer[address1]"  type="text" placeholder="Enter Address Here "  />
                                        							</div>
                                        				    	</div>

                                        				    	<div class="col-md-4">
                                        				 			<div class="form-group">
                                        				 			    <label>Address2<span class="error">*</span></label>
                                        					 		    <input class="form-control" id="del_address2"  name="billing[del_address2]" name="customer[address2]"  type="text" placeholder="Enter Address Here " />
                                        							</div>
                                        				    	</div>
                                        				    </div>
                                    				    	<div class="row">
                                        				    	<div class="col-md-4">
                                        				 			<div class="form-group">
                                        				 			    <label>Address3</label>
                                        					 		    <input class="form-control" id="del_address3" name="billing[del_address3]" name="customer[address3]"  type="text" placeholder="Enter Address Here " />
                                        							</div>
                                        				    	</div>

                                        				    	<div class="col-md-4">
                                        				 			<div class="form-group">
                                        				 			    <label>Pincode</label>
                                        					 		    <input class="form-control" id="del_pincode" name="billing[del_pincode]" name="customer[pincode]"  type="number" placeholder="Enter Pincode " />
                                        							</div>
                                        				    	</div>
                                    					</div>
                                    				</div></br>
                                                </div>
        									</div>

        								</div>
        							</div>
        						</div>

        					</div>
                		</div>
            		    <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" id="save_new_delivery_addr">Proceed</button>
                                </div>
                            </div>
    					</div>
                	</div>

							<!--<div class="form-group">
								<div class="col-md-10">
									<label for="Offer">Bill Type :</label>
									<div class="row">
										<div class="col-md-6">
											<input type="checkbox" class="bill_type_sales" name="sales" value="1" > <label for="bill_type_sales">Sales </label>
										</div>
										<div class="col-md-6">
											<input type="checkbox" id="bill_type_purchase" name="purchase" value="1"> <label for="bill_type_purchase">Purchase </label>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<input type="checkbox" id="select_bill_type_order_advanceamt_details" name="orderadvanceamt" value="1" <?php echo !empty($est_other_item['stone_details']) ? 'checked' : '' ;?>> <label for="select_bill_type_order_advanceamt_details">Order  Advance Amount </label>
										</div>
										<div class="col-md-6">
											<input type="checkbox" id="select_advance_oldmatel_details" name="oldmateladvance" value="1" > <label for="select_advance_oldmatel_details">Order Advance Metal </label>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<input type="checkbox" id="bill_type_advance" name="advancebill" value="1" > <label for="bill_type_advance">Advance Bill </label>
										</div>
										<div class="col-md-6">
											<input type="checkbox" id="bill_type_sales_return" name="salesreturn" value="1"> <label for="bill_type_sales_return">Sales Return </label>
										</div>
									</div>
							   </div>
							</div>-->

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
					    <div class="col-md-12">
                	        <div class="col-md-6">
                	            <label>V.A(%) - <span class="applied_wast_per"></span></label>
                	            <input type="number" class="form-control" id="wastage_per" placeholder="Enter V.A(%)" readonly>
                	        </div>
                	        <div class="col-md-6">
                	            <label>MC - <span class="applied_mc"></span></label>
                	            <input type="number" class="form-control" id="mc_value" placeholder="Enter MC" readonly>
                	        </div>
                	    </div>
					</div>
					<div class="row">
						<div class="box-body">
						   <div class="table-responsive">
							 <table id="estimation_chit_details" class="table table-bordered text-center">
								<thead>
								  <tr>
									<th>A/c Id</th>
									<th>Amount</th>
									<th>Weight</th>
									<th>Action</th>
								  </tr>
								</thead>
								<tbody>
										<tr>
											<td><input class="scheme_account" type="text" style="width: 100px;" />
												<input type="hidden" class="scheme_account_id"  name="chit_uti[scheme_account_id][]">
											</td>
											<td><span class="chit_amount"></span><input type="hidden" class="chit_amt" name="chit_uti[chit_amt][]" /><input type="hidden" class="chit_cash_pay" name="chit_uti[chit_cash_pay][]" /><input type="hidden" name="chit_uti[scheme_type][]" class="form-control scheme_type" id="scheme_type"  value="'+items.scheme_type+'" ></td>
											<td><span class="saved_weight"></span><input type="hidden" class="form-control closing_weight"><input type="hidden" name="chit_uti[wastage_per][]" class="form-control wastage_per"><input type="hidden" name="chit_uti[mc_value][]" class="form-control mc_value"><input type="hidden" name="chit_uti[savings_in_wastage][]" class="form-control savings_in_wastage"><input type="hidden" name="chit_uti[savings_in_mcvalue][]" class="form-control savings_in_mcvalue"><input type="hidden" name="chit_uti[closing_weight][]" class="form-control closing_weight"><input type="hidden" name="chit_uti[paid_installments][]" class="form-control paid_installments"><input type="hidden" name="chit_uti[total_installments][]" class="form-control total_installments"></td>
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
								<th>Card Name</th>
								<th>Type</th>
								<th>Device<span class="error">*</span></th>
								<th>Card No</th>
								<th>Amount</th>
								<th>Approval No</th>
								<th>Action</th>
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
			<a href="#" id="add_newcc" class="btn btn-success">Save</a>
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
								<th>Cheque Date</th>
								<th>Bank</th>
								<th>Cheque No</th>
								<th>Amount</th>
								<th>Action</th>
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
								<th class="upi_type" >Bank</th>
								<th>Payment Date</th>
								<th class="device" style="display:none">Device</th>
								<th>Ref No</th>
								<th>Amount</th>
								<th>Action</th>
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
			<a href="#" id="add_newnb" class="btn btn-success">Save</a>
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
			<button type="button" id="update_stone_details" class="btn btn-success" >Save</button>
			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>
</div>
<!-- sale stone details-->
<!--Purchase stone-->
<div class="modal fade" id="purchase_item_details_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:96%;">

        <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Old Metal Purchase</h4>
		</div>
        	<div class="modal-body">
					<div class="row">
						Total Sales Value : <span style="font-size: 22px;font-weight: bold;" class="modal-title curr_bill_sal_val"></span> &nbsp;&nbsp;&nbsp;&nbsp; Gross Wt : <span style="font-size: 22px;font-weight: bold;" class="modal-title curr_grs_wt"></span> &nbsp;&nbsp;&nbsp;&nbsp; Net Wt : <span style="font-size: 22px;font-weight: bold;" class="modal-title curr_net_wt"></span> &nbsp;&nbsp;&nbsp;&nbsp; Total Amount : <span style="font-size: 22px;font-weight: bold;" class="modal-title curr_tot_amt"></span>
					</div>
            		<div class="row">

            				<input type="hidden" id="pur_active_id"  name="">

            			    <table id="split_purchase_item_details" class="table table-bordered table-striped text-center">

								<thead>
									<tr>
										<th>Use</th>
										<th>Product</th>
										<th>Design</th>
										<th>Pcs</th>
										<th>G.Wt</th>
										<th>L.Wt</th>
										<th>N.Wt</th>
										<th>Wastage(%)</th>
										<th>Wastage.Wt(%)</th>
										<th>Discount</th>
										<th>Stone</th>
										<th>Amount</th>
										<th>Est No</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td style="text-align: right;" class="split_slt_pur_det_pcs"></td>
										<td style="text-align: right;" class="split_slt_pur_det_gwt"></td>
										<td style="text-align: right;" class="split_slt_pur_det_lwt"></td>
										<td style="text-align: right;" class="split_slt_pur_det_nwt"></td>
										<td></td>
										<td></td>
										<td style="text-align: right;" class="split_slt_pur_det_dis"></td>
										<td></td>
										<td style="text-align: right;" class="split_slt_pur_det_purvalue"></td>
										<td></td>

									</tr>
								</tfoot>
							</table>
            	</div>

          </div>

          <div class="modal-footer">

        	<button type="button" id="update_pur_item_details" class="btn btn-success">Save</button>

        	<button type="button" id="close_pur_item_details" class="btn btn-warning" data-dismiss="modal">Close</button>

          </div>

        </div>

    </div>
</div>
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
<div class="modal fade" id="customer-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal"><span
                              aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                      <h4 class="modal-title" id="myModalLabel">Customer Details</h4>
                  </div>
                  <div class="modal-body">
                      <form id="cus_pop"></form>
                      <div id="cus_bill_details"></div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>

<!--Credit History Details-->
<div class="modal fade" id="bill-credit-popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog" style="width:75%;">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel">Select Pending Bill</h4>
		</div>
		<div class="modal-body">
			<div class="row" id="bill_items_for_pending" style="display:none;">
				<div class="box-body">
					<p class="lead">Credit Pending Details</p>
					<div class="table-responsive">
						<table id="bill_items_tbl_for_pending" class="table table-bordered table-striped text-center">
							<thead>
								<tr>
									<th>Select</th>
									<th>Bill No</th>
									<th>Bill Date</th>
									<th>Tot Bill Amount</th>
									<th>Due Amount</th>
									<th>Paid Amount</th>
									<th>Bal Amount</th>
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
		<a href="#" id="update_bill_credit" class="btn btn-success">Add</a>
		<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
	  </div>
	</div>
</div>
</div>
<!--Credit History Details-->
<!-- / Payment Details -->
<div class="modal fade" id="payment_detail_modal" tabindex="" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:80%;">
		<div class="modal-content">
			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Payment Details</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12 pull-right">
							<button type="button" class="btn bg-olive btn-sm pull-right" id="add_new_payment"><i class="fa fa-user-plus"></i>ADD</button>
							<p class="error "><span id="cardPayAlert"></span></p>
						</div>
					</div>
					<p></p>
				   <div class="table-responsive">
					 <table id="pay_details_table" class="table table-bordered">
						<thead>
							<tr>
								<th>Payment Type</th>
								<th>Amount</th>
								<th>Card Name</th>
								<th>Device Type</th>
								<th>NB Type</th>
								<th>Card/Ref No</th>
								<th>Approval No</th>
								<th>Bank</th>

								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						</tbody>

					 </table><br>
					 <table id="bill_split_adv_adj" class="table table-bordered text-center">
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
					</table>
					<table id="split_total_pay_details" class="table table-bordered text-center">
								<tr style="font-weight:bold;">
									<td>Total Bill Amount</td>
									<td class="tot_bill_split_amount"></td>
									<td></td>

								</tr>
								<tr style="font-weight:bold;">
									<td>Total Received Amount</td>
									<td class="tot_recd_amount"></td>

								</tr>
								<tr style="font-weight:bold;">
									<td>Total Adjusted Amount</td>
									<td class="tot_adj_amount"></td>
								</tr>
								<tr style="font-weight:bold;">
									<td>Balance Amount</td>
									<td class="tot_blc_amt"></td>
								</tr>
					</table>
				  </div>
				</div>
			</div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-success" id="save_payment_details">Save</button>
			<button type="button" class="btn btn-warning" id="close_payment_details">Close</button>
		  </div>
		</div>
	</div>
</div>
<!-- / Payment Details -->
<div class="modal fade" id="imageModal_tag" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="width:60%;">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
          </div>
          <div class="modal-body">
			  <div class="row">
              	<div id="order_images" style="margin-top: 2%;"></div>
			  </div>
          </div>
          <div class="modal-footer">
              </br>
              <button type="button" id="close_stone_details" class="btn btn-warning"
                  data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>
<!-- / Payment Details -->
<div class="modal fade" id="bill_split_kyc_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" style="width:40%;">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel">KYC</h4>
          </div>
          <div class="modal-body">
				<div class="row">
					<div class="form-group">
					   <label for="pan_no" class="col-md-3 col-md-offset-1 ">PAN No</label>
					   <div class="col-md-4">
							<input type="text" class="form-control" id="pan_no" placeholder="Enter The PAN No" >
					   </div>
					</div>
				</div></br>

				<div class="row">
					<div class="form-group">
					   <label for="aadhar_no" class="col-md-3 col-md-offset-1 ">ADHAAR NO</label>
					   <div class="col-md-4">
							<input type="text" class="form-control" id="aadhar_no" maxLength="14" placeholder="Enter The ADHAAR NO" >
					   </div>
					</div>
				</div></br>

				<div class="row">
					<div class="form-group">
					   <label for="driving_license_no" class="col-md-3 col-md-offset-1 ">DRIVING LICENSE</label>
					   <div class="col-md-4">
							<input type="text" class="form-control" id="dl_no" maxLength="15" placeholder="Enter The DRIVING LICENSE NO" >
					   </div>
					</div>
				</div></br>

				<div class="row">
					<div class="form-group">
					   <label for="passport_no" class="col-md-3 col-md-offset-1 ">PASSPORT</label>
					   <div class="col-md-4">
							<input type="text" class="form-control" id="pp_no" maxLength="15" placeholder="Enter The PASSPORT NO" >
					   </div>
					</div>
				</div></br>


			</div>
           <div class="modal-footer">
    			<button type="button" class="btn btn-success" id="save_kyc_details">Save</button>
    			<button type="button" class="btn btn-warning" data-dismiss="modal" >Close</button>
		  </div>
      </div>
  </div>
</div>