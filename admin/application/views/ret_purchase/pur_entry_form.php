      <!-- Content Wrapper. Contains page content -->

	  <style>

.remove-btn{

	margin-top: -168px;

	margin-left: -38px;

	background-color: #e51712 !important;

	border: none;

	color: white !important;

}

.sm{

	font-weight: normal;

}

}



*[tabindex]:focus {

	outline: 1px black solid;

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

	  <h3 class="box-title">SUPPLIER BILL ENTRY</h3>

	</div>

	<div class="box-body">

	 <!-- form container --> 

		 <!-- form -->

		<form id="purhcase_entry_form">

			

		 <div align="left"  style="background: #f5f5f5">

			<ul class="nav nav-tabs" id="billing-tab">

				  <li class="active"><a id="tab_bill_details" href="#bill_details" data-toggle="tab">BILL DETAILS</a></li>

				  <li ><a id="tab_items" href="#item_details" data-toggle="tab">ITEM DETAILS</a></li>

				  <li id="tab_tot_summary"><a href="#tot_summary" data-toggle="tab">SUMMARY DETAILS</a></li>

			</ul>

		</div></br>

		

		

		<div class="tab-content">

			

			<div class="tab-pane active" id="bill_details">

				<div class="row">

					<div class="col-md-12">

						

						 <div class="col-md-2">

							 <div class="form-group">

								  <?php 

										 $this->session->unset_userdata('pur_entry_form_secret');

										 $pur_entry_form_secret=md5(uniqid(rand(), true));

										$this->session->set_userdata('pur_entry_form_secret', $pur_entry_form_secret);

									 ?>

							   <label>Select GRN<span class="error">*</span></label>

									<select id="select_grn" class="form-control" name="order[id_grn]" style="width:100%;" tabindex="1"></select>

									<input type="hidden" id="id_grn" value="<?php echo $po_item['po_grn_id'];?>">

									<input type="hidden" id="pur_entry_form_secret" name="order[pur_entry_form_secret]" value="<?php echo $pur_entry_form_secret;?>">

									<input type="hidden" id="supplier_bill_entry_calc" name="order[supplier_bill_entry_calc]" value="<?php echo $po_item['supplier_bill_entry_calc'];?>">

							 </div> 

						</div>

						

						

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Purchase Type</label>

								<div class="form-group" >  

										<input type="hidden" name="order[po_id]" id="po_id" value="<?php echo $po_item['po_id'];?>">

										<input type="radio" id="oranment_type" name="order[po_type]" value="1" <?php echo ($po_item['po_type']==1 ? 'checked' :'')?> ><label for="oranment_type">Oranments</label>

										<input type="radio" id="mt_type" name="order[po_type]" value="2" <?php echo ($po_item['po_type']==2 ? 'checked' :'')?> ><label for="mt_type">Bullion Purchase</label>

										<input type="radio" id="st_type" name="order[po_type]" value="3" <?php echo ($po_item['po_type']==3 ? 'checked' :'')?> ><label for="st_type">Stone</label>

									</div>

							 </div> 

						</div>

						

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Against Order</label>

								<div class="form-group" >  

										<input type="radio"  class="aganist_order" id="aganist_order" name="order[stock_type]" value="1" tabindex="2" <?php echo ($po_item['purchase_type']==1 ? 'checked' :'')?> > <label class="custom-label" for="order">Yes</label>

										<input type="radio"  class="aganist_order" id="aganist_purchase" name="order[stock_type]" value="2" tabindex="3" <?php echo ($po_item['purchase_type']==2 ? 'checked' :'')?> ><label class="custom-label" for="purchase">No</label>

									</div>

							 </div> 

						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Purchase Type</label>
								<div class="form-group">
									<select class="form-control" name="purchase_type" id="purchase_type" style="width: min-content;"><option value="1">stock</option><option value="2">walkin</option><option value="3">expo</option></select>
								</div>
							</div>
						</div>

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Approval Stock</label>

								<div class="form-group" >  

										<input type="radio" id="approval_stock_yes" name="order[is_suspense_stock]" value="1" <?php echo ($po_item['is_suspense_stock']==1 ? 'checked' :'')?> disabled> <label class="custom-label" for="approval_stock_yes">Yes</label>

										&nbsp;&nbsp;&nbsp;

										<input type="radio" id="approval_stock_no"  name="order[is_suspense_stock]" value="0" <?php echo ($po_item['is_suspense_stock']==0 ? 'checked' :'')?> disabled> <label class="custom-label" for="approval_stock_no">No</label>

									</div>

							 </div> 

						</div>

						

						<div class="col-md-2">

							 <div class="form-group">

								 <label>Select Karigar<span class="error">*</span></label>

								 

								 <div class="input-group">

										<select id="select_karigar" class="form-control"  style="width:100%;" tabindex="4"></select>

										<input type="hidden" id="id_karigar" name="order[id_karigar]" value="<?php echo $po_item['po_karigar_id'];?>">

										<input type="hidden" id="tds_purchase_limit" name="settings[tds_purchase_limit]" value="">

										<input type="hidden" id="tds_item_cost" name="tds_details[tds_item_cost]" value="">

										<input type="hidden" id="opening_balance" name="tds_details[opening_balance]" value="">

										<input type="hidden" id="fin_year_code" name="tds_details[fin_year_code]" value="">

										<input type="hidden" id="cmp_country" value="<?php echo $comp_details['id_country'];?>">

										<input type="hidden" id="cmp_state" value="<?php echo $comp_details['id_state'];?>">

										<input type="hidden" id="supplier_state" value="">

										<input type="hidden" id="supplier_country" value="">

									<span class="input-group-btn" >

										<a class="btn btn-warning" id="edit_karigar" href="#"  data-toggle="tooltip" title="Edit Supplier" style="height: 29px;padding-left: 14px;"><i class="fa fa-user-plus"></i></a>

									</span>

							  </div>

							 </div> 

						</div>

					

						

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Hallmark</label>

								<div class="form-group" >  

										<input type="radio" name="order[is_halmerked]" value="1" <?php echo ($po_item['is_po_halmarked']==1?'checked':'')?> tabindex="5"> YES

										&nbsp;&nbsp;&nbsp;

										<input type="radio" name="order[is_halmerked]" value="0" <?php echo ($po_item['is_po_halmarked']==0?'checked':'')?>> NO

									</div>

							 </div> 

						</div>

				
						<!-- <div class="col-md-2">

							 <div class="form-group">

							   <label>Select Order<span class="error">*</span></label>

									<select id="select_po_no" class="form-control" name="order[purchase_order_no]" style="width:100%;" tabindex="1" disabled></select>

									<input type="hidden" id="received_pcs" name="order[received_pcs]">

									<input type="hidden" id="received_wt" name="order[received_wt]">

									<input type="hidden" id="is_cus_repair_order" name="order[is_cus_repair_order]">

							 </div> 

						</div> 

						

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Select Category<span class="error">*</span></label>

									<select id="select_category" name="order[id_category]" class="form-control" style="width:100%;" tabindex="2"></select>

									<input type="hidden" id="id_category" class="form-control" value="<?php echo $po_item['id_category']; ?>" >

							 </div> 

						</div>

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Select Purity<span class="error">*</span></label>

									<select id="select_purity" name="order[id_purity]" class="form-control"  style="width:100%;" tabindex="3"></select>

									<input type="hidden" name="order[purity]" class="form-control" value="<?php echo $po_item['id_purity']; ?>" id="id_purity" />

							 </div> 

						</div> -->

					  </div>

					  </div>

				<div class="row">

					<div class="col-md-12">

					
						<div class="col-md-2">

							<div class="form-group">

								<label>Rate Fixed</label>

								<div class="form-group" >  

									<input type="radio" class="is_rate_fixed"  id="is_rate_fixed_yes" name="is_rate_fixed" value="1" checked tabindex="6"> YES

									&nbsp;&nbsp;&nbsp;

									<input type="radio" class="is_rate_fixed" id="is_rate_fixed_no" name="is_rate_fixed" value="0" > NO

								</div>

							</div> 

						</div>

						<div class="col-md-2">

							 <div class="form-group">

							   <label>E-Way Bill No</label>

								<input type="text" class="form-control ewaybillno" name="order[ewaybillno]" value="<?php echo $po_item['ewaybillno'];?>" placeholder="Enter The Bill No." tabindex="7">

							 </div> 

						</div>

						

						<div class="col-md-2">

							 <div class="form-group">

							   <label>IRN No</label>

								<input type="text" class="form-control po_irnno" name="order[po_irnno]" value="<?php echo $po_item['po_irnno'];?>" placeholder="Enter The IRN No." tabindex="8">

							 </div> 

						</div>

						

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Dispatch Through</label>

								<select id="despatch_through" class="form-control" name="order[despatch_through]" style="width:100%;" tabindex="9">

									<option value="1" <?php echo ($po_item['despatch_through']==1 ? 'selected' :'')?> >Courier</option>

									 <option value="2" <?php echo ($po_item['despatch_through']==2 ? 'selected' :'')?> >Manual Delivery</option>

								</select>

							 </div> 

						</div>

						

					 <!-- </div>

					 <div class="col-md-12"> -->

						<!-- Reference no -->

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Ref No</label>

								<input type="text" class="form-control po_ref_no" name="order[po_supplier_ref_no]" value="<?php echo $po_item['po_supplier_ref_no'];?>" placeholder="Enter supplier bill Ref no." tabindex="10">

							 </div> 

						</div>

						

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Ref Date</label>

								<input type="date" class="form-control po_ref_date" name="order[po_ref_date]" value="<?php echo $po_item['po_ref_date'];?>" dateformat="d-M-y"  placeholder="Select bill Ref date." tabindex="11">

							 </div> 

						</div>

						

					</div>

				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-2">
							<div class="form-group">
								<label>Against Karigar Issue</label>
								<div class="form-group" >  
									<input type="radio" id="aganist_karigar_metal_issue" name="aganist_karigar_metal_issue" value="1" ><label for="aganist_karigar_metal_issue_yes">&nbsp;Yes</label>
									<input type="radio" id="aganist_karigar_metal_issue" name="aganist_karigar_metal_issue" value="0" checked><label for="aganist_karigar_metal_issue_no">&nbsp;No</label>
								</div>
							</div> 
						</div>
						<div class="col-md-2">

							 <div class="form-group">

							   <label>Due Date</label>

								<input type="date" class="form-control po_due_date" name="order[due_date]" value="<?php echo date('Y-m-d');?>" dateformat="d-M-y"  placeholder="Select bill due date." tabindex="11">

							 </div> 

						</div>

						<div class="col-md-2 metalissueref" style="display:none;">
							<div class="form-group">
								<label>Select IssueRef NO</label>
								<div class="form-group" >  
									<select class="form-control" id="select_metalissueref_no" name="select_metalissueref_no" style="width:100%;" disabled></select>
								</div> 
							</div>
						</div>

						<div class="col-md-2 metalissueref" style="display:none;">
							<div class="form-group">
								<label></label>
								<div class="form-group" >  
									<button type="button" id="metal_issue_search" class="btn btn-info">Search</button> 
								</div> 
							</div>
						</div>
					</div>							
				</div>

			</div>

			

			<div class="tab-pane" id="item_details">

				<div class="row">

					<div class="col-md-12">

						<h4>Item Details</h4>

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Select Order<span class="error">*</span></label>

									<select id="select_po_no" class="form-control" name="order[purchase_order_no]" style="width:100%;" tabindex="1" disabled></select>

									<input type="hidden" id="received_pcs" name="order[received_pcs]">

									<input type="hidden" id="received_wt" name="order[received_wt]">

									<input type="hidden" id="is_cus_repair_order" name="order[is_cus_repair_order]">

									<input type="hidden" id="po_item_id" >

							 </div> 

						</div> 

						

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Select Category<span class="error">*</span></label>

									<select id="select_category" name="order[id_category]" class="form-control" style="width:100%;" tabindex="2"></select>

									<input type="hidden" id="id_category" class="form-control" value="" >

							 </div> 

						</div>

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Select Purity<span class="error">*</span></label>

									<select id="select_purity" name="order[id_purity]" class="form-control"  style="width:100%;" tabindex="3" <?php echo $po_item['supplier_bill_entry_calc']==1 ? '': '' ?> ></select>

									<input type="hidden" name="order[purity]" class="form-control" value="" id="id_purity" />

							 </div> 

						</div>

						 <div class="col-md-2">

							 <div class="form-group">

							   <label>Select Product<span class="error">*</span></label>

									<select id="select_product" class="form-control" name="order[id_product]" style="width:100%;" tabindex="7" ></select>

									<input type="hidden" class="form-control purchase_mode" id="purchase_mode" value="" />

									<input type="hidden" id="pro_id" class="form-control" value="" >

							 </div> 

						</div>

						 <div class="col-md-2 oranments oranmentspo">

							 <div class="form-group">

							   <label>Select Design<span class="error">*</span></label>

									<select id="select_design" class="form-control"  style="width:100%;" tabindex="8"></select>

									<input type="hidden" id="design_id" class="form-control" value="" >



							 </div> 

						</div>

						 <div class="col-md-2 oranments oranmentspo">

							 <div class="form-group">

							   <label>Sub Design<span class="error">*</span></label>

									<select id="select_sub_design" class="form-control"  style="width:100%;" tabindex="9"></select>

									<input type="hidden" id="sub_design_id" class="form-control" value="" >

							 </div> 

						</div>

					 </div>

				 </div>

				 <div class="row">

					 <div class="col-md-12">

					 	<div class="col-md-2">

								<div class="form-group">

								<label>Quality Code<span class="error"></span></label>

								<select id="select_quality" class="form-control" name="order[quality_id]" style="width:100%;" tabindex="10" disabled></select>

								</div> 

						</div>

					   <div class="col-md-2">

							 <div class="form-group">

							   <label>Pcs<span class="error">*</span></label>

								<input type="number" class="form-control" id="tot_pcs" tabindex="11">

							 </div> 

						</div>

						<div class="col-md-2 oranments-bullion">

							 <div class="form-group">

							   <label>GWT<span class="error">*</span></label>

							   <div class="input-group"style="width:200px;">
									<input type="number" class="form-control" id="tot_gwt" tabindex="12" style="width:120px;"><input type="hidden" class="form-control" id="order_gwt">
									<span class="input-group-btn">
									<select id="pur_uom" class="form-control"  style="width:80px;">
										<option value="1" selected>Gram</option>
										<option value="6">Carat</option>
									</select>
									</span>										
								</div> 
							 </div> 

						</div>

						

						<!--<div class="col-md-2 stone_purchase" style="display:none;">

							 <div class="form-group">

							   <label>GWT<span class="error">*</span></label>

								 <div class="input-group" style="width:200px;">

									   <input class="stone_wt form-control" type="number" name="stone_wt" value="" style="width: 120px;" tabindex="11">

									   <span class="input-group-btn" style="width: 80px;"><select class="stone_uom_id form-control" name="uom_id"></select></span>

									   <input type="hidden" class="form-control" id="order_gwt_uom" >

								 </div>

							 </div>

						 </div>

					   

						<div class="col-md-2 stone_purchase" style="display:none;">

							 <div class="form-group">

							   <label>Cal Type</label>

								<div class="form-group" >  

										<input type="radio" class="stone_cal_type" id="call_by_wt" name="cal_type" value="1" tabindex="12" checked> <label class="custom-label" for="call_by_wt">Wt</label>

										<input type="radio" class="stone_cal_type" id="call_by_pcs" name="cal_type" value="2"><label class="custom-label" for="call_by_pcs">Pcs</label>

									</div>

							 </div> 

						</div>-->

						

					   

						<div class="col-md-2 oranments">

							  <div class="form-group">

									<label>LWT</label>

									<div class="input-group ">

										<input id="tot_lwt" class="form-control custom-inp add_stone_details" type="number" step="any" readonly tabindex="13"/>

										<span class="input-group-addon input-sm add_stone_details">+</span>

										<input type="hidden" id="stone_details">

										<input type="hidden" id="stone_price">

									</div>

								</div> 

						</div>

						<div class="col-md-2 oranments">

							 <div class="form-group">

							   <label>NWT<span class="error">*</span></label>

								<input type="number" class="form-control" id="tot_nwt"  readonly>

							 </div> 

						</div>

						<div class="col-md-2 oranments">

							 <div class="form-group">

							   <label>Wastage(%)</label>

								<input type="number" class="form-control" id="tot_wastage_perc" tabindex="14" disabled >

							 </div> 

						</div>

					 </div>

				 </div>

				 <div class="row">

					 <div class="col-md-12">

					 <div class="col-md-2 oranments">

						<div class="form-group">

							<label>Wastage Wgt</label>

							<input type="number" class="form-control" id="tot_wastage_wgt" tabindex="14" disabled >

							<input type="hidden" id="wastage_type">

						</div> 

					</div>

						<div class="col-md-2 oranments">

							 <div class="form-group">

							   <label>Calculation Based On<span class="error">*</span></label>

									<select id="calculation_based_on" class="form-control" name="order[calculation_based_on]" style="width:100%;" tabindex="15" disabled>

										<option value="0" <?php echo (isset($po_item['calculation_based_on']) && $po_item['calculation_based_on'] == 0 ? 'selected' : ''); ?>>Mc & Wast On Gross</option>

										<option value="1" <?php echo (!isset($po_item['calculation_based_on']) || $po_item['calculation_based_on'] == 1 ? 'selected' : ''); ?>>Mc & Wast On Net</option>

										<option value="2" <?php echo (isset($po_item['calculation_based_on']) && $po_item['calculation_based_on'] == 2 ? 'selected' : ''); ?>>Mc on Gross,Wast On Net</option>

									</select>

							 </div> 

						</div>

						<div class="col-md-2 oranments">

							 <div class="form-group">

							   <label>Mc Type<span class="error">*</span></label>

									<select id="mc_type" class="form-control" name="order[mc_type]" style="width:100%;" tabindex="15" disabled>

										<option value="1">Per Gram</option>

										<option value="2">Per Piece</option>

									</select>

							 </div> 

						</div>

						<div class="col-md-1 oranments">

							 <div class="form-group">

							   <label>MC</label>

								<input type="number" class="form-control"  id="mc_value" placeholder="M.C" tabindex="16" disabled>

								Tot Mc:<span class="tot_mc_val" ></span>

							 </div> 

						</div>

						<div class="col-md-1 oranments">

							 <div class="form-group">

							   <label>TOUCH<span class="error">*</span></label>

								<input type="number" class="form-control purchase_touch" name="order[purchase_touch]" id="purchase_touch" tabindex="17" value="92" disabled>

							 </div> 

						</div>

						

						<div class="col-md-2 oranments">

							<div class="form-group">

									<label for="">Type<span></span></label>

									<select class="form-control" id="karigar_calc_type" name="order[karigar_calc_type]" disabled>

										<option value="1" <?php if($karigar['karigar_calc_type']==1){ ?> selected <?php } ?>> Weight x Rate</option>

										<option value="2" <?php if($karigar['karigar_calc_type']==2){ ?> selected <?php } ?>>Purchase Touch</option>

										<option value="3" <?php if($karigar['karigar_calc_type']==3){ ?> selected <?php } ?>>Weight x Wastage %</option>

									</select>

							</div>	

						</div> 

						<div class="col-md-1 oranments">

				 <div class="form-group">

				   <label>Purity Calc<span class="error">*</span></label>

					<select id="purity_type" class="form-control" name="order[purity_type]" style="width:100%;">
						<option value="999" selected>999</option>
						<option value="995">995</option>
					</select>

				 </div> 

				</div>

				<div class="col-md-1 oranments">

				 <div class="form-group">

				   <label>PURE<span class="error">*</span></label>

					<input type="number" class="form-control" name="order[tot_purewt]" id="tot_purewt" readonly>

				 </div> 

				</div>

					
					 </div>

				 </div>

				 <div class="row">

					 <div class="col-md-12">

						 
						<div class="col-md-2">

							<div class="form-group">

								<label>Rate<span class="call_type_label"></span><span class="tax_type"></span><span class="error">*</span></label>
								<div class="input-group"style="width:200px;">
									<input type="text" class="form-control"  id="rate_per_gram" placeholder="Per Gram" tabindex="18" style="width:120px;">
									<span class="input-group-btn">
										<select id="rate_calc_type" class="form-control"  style="width:80px;">
											<option value="1" selected>Gram</option>
											<option value="2">Pcs</option>
										</select>
									</span>
								</div> 	

							</div> 

						</div>
						<div class="col-md-2 oranments">

							  <div class="form-group">

									<label>Other Metals</label>

									<div class="input-group ">

										<input type="hidden" id="other_metal_wt">

										<input type="hidden" id="other_metal_wast_wt">

										<input type="hidden" id="other_metal_mc_amount">

										<input id="other_metal_amount" class="form-control custom-inp add_other_metals" type="number" step="any" readonly tabindex="19" />

										<span class="input-group-addon input-sm add_other_metals">+</span>

										<input type="hidden" id="other_metal_details">

									</div>

								</div> 

						</div>

						<div class="col-md-2 oranments">

							  <div class="form-group">

									<label>Other Charges</label>

									<div class="input-group ">

										<input id="other_charges_amount" class="form-control custom-inp add_other_charges" type="number" step="any" readonly tabindex="19" />

										<span class="input-group-addon input-sm add_other_charges">+</span>

										<input type="hidden" id="other_charges_details">

									</div>

								</div> 

						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Taxable Amount<span class="error">*</span></label>
								<input type="text" class="form-control" id="item_total_taxable" disabled>
							</div> 
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label>Tax Rate<span class="error">*</span></label>
								<input type="text" class="form-control" id="item_total_tax" disabled>
							</div> 
						</div>

						<div class="col-md-2">

							 <div class="form-group">

							   <label>Amount<span class="error">*</span></label>

								<input type="text" class="form-control" id="item_cost" disabled>

								<input type="hidden" class="form-control" id="item_total_tax">

								<input type="hidden" class="form-control" id="item_cgst_cost">

								<input type="hidden" class="form-control" id="item_sgst_cost">

								<input type="hidden" class="form-control" id="item_igst_cost">

								<input type="hidden" class="form-control" id="item_tax_percentage">

								<input type="hidden" class="form-control" id="tax_type">

								<input type="hidden" class="form-control" id="tax_group_id">

								<input type="hidden" class="form-control calculation_based_on">

							 </div> 

						</div>

						</div>
						<div class="row">

							<div class="col-md-4">

								<div class="form-group">

								<label>Narration</label>

									<textarea class="form-control" id="item_remarks" rows="5" cols="500"> </textarea>

								</div> 

							</div>

					 </div> 

				 </div>

				<div class="row">

					<div class="col-md-12">

							<div class="form-group" align="center">

								<button id="add_po_order_items" type="button" class="btn btn-success" tabindex="20"><i class="fa fa-plus"></i> Add Item </button>

							</div>

						</div>

				</div>

			</div>

			<div class="tab-pane" id="tot_summary">

				<div class="row">

					<div class="col-md-12">

						<div class="table-responsive">

						<p class="help-block"></p></legend>

						 <table id="item_detail" class="table table-bordered table-striped">

							<thead style="text-transform:uppercase;">

								  <tr>

									<th width="5%;">Category</th> 

									<th width="5%;">Product</th> 

									<th width="5%;">Design</th> 

									<th width="5%;">Sub Design</th> 

									<th width="5%;">Purity</th> 

									<th width="5%;">Pcs</th> 

									<th width="5%;">Gwt</th> 

									<th width="5%;">Lwt</th> 

									<th width="5%;">Nwt</th> 

									<th width="5%;">Dia Wt</th> 

									<th width="5%;">Pure Wt</th> 

									<th width="5%;">V.A(%)</th> 

									<th width="5%;">V.A</th> 

									<th width="10%;">MC</th> 

									<th width="5%;">Stn Amt</th> 

									<th width="5%;">Other Metal</th> 

									<th width="5%;">Other Charges</th> 

									<th width="10%;">Amount</th> 

									<th width="10%;">Action</th> 

								  </tr>

							 </thead>

							 <tbody> 

								<?php if(sizeof($po_item_details)>0)

								{

									foreach($po_item_details as $ikey => $ival)

									{

								

									$stone_data=[];

								

									$otherMetal_data = [];

									

									$other_charges_data = [];

									$chargeData = [];

									

									$charges_amount = 0;

								

									foreach($ival['stn_details'] as $stn)

									{

										$stone_data[]=array(

								

											'show_in_lwt'=>$stn['is_apply_in_lwt'],

								

											'stones_type' => $stn['stone_type'],

											

											'stone_id' => $stn['po_stone_id'],

								

											'stone_pcs'   => $stn['po_stone_pcs'],

								

											'stone_wt'   => $stn['po_stone_wt'],

											

											'stone_uom_id' => $stn['po_stone_uom'],

								

											//'stone_name'   => $stn['stone_name'],

								

											'stone_price'   => $stn['po_stone_amount'],

								

											'stone_rate' => $stn['po_stone_rate'],

											'stone_quality_id' => $stn['po_quality_id'],

											//'stone_cal_type' => $stn['po_stone_calc_based_on'],

								

										

										);

								

									}

								

									$stone_details=json_encode($stone_data);

								

								

									foreach($ival['other_metal_details'] as $otrm)

									{

								

								

										$otherMetal_data[]=array(

								

											'id_metal'    =>  $otrm['po_item_metal'],

								

											'id_purity'   =>  $otrm['po_other_item_purity'],

								

											'pcs'     =>  $otrm['po_other_item_pcs'],

								

											'gwt'     =>  $otrm['po_other_item_gross_weight'],

								

											'wastage_perc'   =>  0,

								

											'calc_type'   =>  $otrm['po_other_item_cal_type'],

								

											'rate_per_gram'   =>  $otrm['po_other_item_rate'],

								

											'amount'   =>  $otrm['po_other_item_amount'],

								

											'mc_value'  => 0,

										);

								

									}

									$metal_details=json_encode($otherMetal_data);

									

									foreach($ival['other_charge_details'] as $val)

									{

										if($val['calc_type']==2)

										{

											$charges_amount+=($val['pur_othr_charge_value']*$ival['no_of_pcs']);

										}else{

											$charges_amount+=($val['pur_othr_charge_value']);

										}

										

										$chargeData[] = array(

											'charge_value'      =>$val['pur_othr_charge_value'],

											'charge_id'         =>$val['pur_othr_chrg_id'],

											'calc_type'         =>$val['calc_type'],

											'char_with_tax'     =>0,

											'charge_tax'        =>0,

											'charge_tax_value'  =>0,

										);

									}

									$charges_details =json_encode($chargeData);

								

									

								

								

								

								

											//echo "<pre>";print_r($ival);exit;

											echo '<tr id="'.$ikey.'"">

								

												<td><input type="hidden" class="id_category" name="order_item[id_category][]" value="'.$ival['po_item_cat_id'].'"><input type="hidden" class="po_item_id" name="order_item[po_item_id][]" value="'.$ival['po_item_id'].'"><input type="hidden" class="po_order_no" name="order_item[po_order_no][]" value="'.$ival['po_order_no'].'">'.$ival['category_name'].'</td>

												

												<td><input type="hidden" class="id_product" name="order_item[id_product][]" value="'.$ival['po_item_pro_id'].'">'.$ival['product_name'].'<input type="hidden" name="order_item[po_purchase_mode][]" value="'.$ival['po_purchase_mode'].'">

												

												<td><input type="hidden" class="id_design" name="order_item[id_design][]" value="'.$ival['po_item_des_id'].'">'.$ival['design_name'].'</td>

												

												<td><input type="hidden" class="id_sub_design" name="order_item[id_sub_design][]" value="'.$ival['po_item_sub_des_id'].'">'.$ival['sub_design_name'].'</td>

												

												<td><input type="hidden" class="id_purity" name="order_item[id_purity][]" value="'.$ival['id_purity'].'">'.$ival['purity_name'].'</td>

												

												<td><input type="hidden" class="tot_pcs" name="order_item[tot_pcs][]" value="'.$ival['no_of_pcs'].'">'.$ival['no_of_pcs'].'</td>

												

												<td><input type="hidden" class="tot_gwt" name="order_item[tot_gwt][]" value="'.$ival['gross_wt'].'"><input type="hidden" class="cal_type" name="order_item[cal_type][]" value="'.$ival['cal_type'].'"><input type="hidden" class="gwt_uom" name="order_item[gwt_uom][]" value="'.$ival['gross_wt'].'">'.$ival['gross_wt'].'</td>

												

												<td><input type="hidden" class="tot_lwt" name="order_item[tot_lwt][]" value="'.$ival['less_wt'].'">'.$ival['less_wt'].'</td>

												

												<td><input type="hidden" class="tot_nwt" name="order_item[tot_nwt][]" value="'.$ival['net_wt'].'">'.$ival['net_wt'].'</td>

								

												<td><input type="hidden" class="total_diawt" name="order_item[total_diawt][]" value="'.$ival['dia_wt'].'">'.$ival['dia_wt'].'</td>

												

												<td><input type="hidden" class="tot_purewt" name="order_item[tot_purewt][]" value="'.$ival['item_pure_wt'].'"><input type="hidden" class="pur_calc_type" name="order_item[pur_calc_type][]" value="'.$ival['pure_wt_calc_type'].'"><input type="hidden" class="purity_type" name="order_item[purity_type][]" value="'.(isset($ival['purity_type']) ? $ival['purity_type'] : 999).'">'.$ival['item_pure_wt'].'</td>

												

												<td>'.$ival['item_wastage'].'</td>

												

												<td><input type="hidden" class="purchase_touch" name="order_item[purchase_touch][]" value="'.$ival['purchase_touch'].'"><input type="hidden" class="tot_wastage_perc" name="order_item[tot_wastage_perc][]" value="'.$ival['item_wastage'].'">'.$ival['item_wastage'].'</td>

												

												<td><input type="hidden" class="mc_value" name="order_item[mc_value][]" value="'.$ival['mc_value'].'"><input type="hidden" class="mc_type" name="order_item[mc_type][]" value="'.$ival['mc_type'].'">'.$ival['mc_value'].'-'.$ival['mctype'].'</td>

												

												<td><input type="hidden" class="stone_details" name="order_item[stone_details][]" value='.$stone_details.'><input type="hidden" class="cus_repair_amt" name="order_item[cus_repair_amt][]" ><input type="hidden" class="stone_price" name="order_item[stone_price][]" value="'.$ival['stn_amt'].'">'.$ival['stn_amt'].'</td>

												

												<td>'.$ival['oth_metal_amt'].'<input type="hidden" class="other_metal_details" name="order_item[other_metal_details][]" value='.$metal_details.'><input type="hidden" class="other_metal_amt" value="'.$ival['oth_metal_amt'].'"></td>

												

												<td>'.$charges_amount.'<input type="hidden" class="other_charges_amount" value='.$charges_amount.'><input type="hidden" class="other_chrg_details" name="order_item[other_chrg_details][]" value='.$charges_details.'></td>

												

												<td><input type="hidden" class="total_payable_amt" name="order_item[total_payable_amt][]" value="'.$ival['item_cost'].'"><input type="hidden" class="cgst_cost" name="order_item[cgst_cost][]" value="'.$ival['total_cgst'].'" ><input type="hidden" class="sgst_cost" name="order_item[sgst_cost][]" value="'.$ival['total_sgst'].'" ><input type="hidden" class="igst_cost" name="order_item[igst_cost][]" value="'.$ival['total_igst'].'" ><input type="hidden" class="total_tax_rate" name="order_item[total_tax_rate][]" value="'.$ival['total_tax'].'" ><input type="hidden" class="tax_group" name="order_item[tax_group][]" value="'.$ival['tax_group'].'" ><input type="hidden" class="tax_percentage" name="order_item[tax_percentage][]" value="'.$ival['tax_percentage'].'"><input type="hidden" class="ratecaltype" name="order_item[ratecaltype][]" value="'.$ival['po_purchase_mode'].'"><input type="hidden" class="rate_calc_type" name="order_item[rate_calc_type][]" value="'.$ival['rate_calc_type'].'"><input type="hidden" class="rate_per_gram" name="order_item[rate_per_gram][]" value="'.$ival['fix_rate_per_grm'].'"><input type="hidden" name="order_item[item_cost][]" value="'.$ival['item_cost'].'"><input type="hidden" name="order_item[is_halmerked][]" value="'.$ival['is_halmarked'].'"><input type="hidden" name="order_item[is_rate_fixed][]" value="'.$ival['is_rate_fixed'].'"><input type="hidden" class="calculation_based_on" name="order_item[calculation_based_on][]" value="'.$ival['calculation_based_on'].'"><input type="hidden" class="item_remarks" name="order_item[item_remarks][]" value="'.$ival['remark'].'">'.$ival['item_cost'].'</td>

												

												<td><a href="#"  onClick="Edit_ItemDetails($(this).closest(\'tr\'));" class="btn btn-primary btn-edit" ><i class="fa fa-edit"></i></a><a href="#" onClick="remove_po_entry_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

								

											</tr>';

									}

								}?>

							 </tbody>

							 <tfoot>

								 <tr style="font-weight:bold;">

									 <td colspan="5" style="text-align:center;">TOTAL</td>

									 <td style="text-align:right;"><span class="total_pcs" ></span></td>

									 <td style="text-align:right;"><span class="total_gwt" style="text-align:right;"></span></td>

									 <td style="text-align:right;"><span class="total_lwt" style="text-align:right;"></span></td>

									 <td style="text-align:right;"><span class="total_nwt" style="text-align:right;"></span></td>

									 <td style="text-align:right;"><span class="tot_diawt" style="text-align:right;"></span></td>

									 <td style="text-align:right;"><span class="total_pure_wt" style="text-align:right;"></span></td>

									 <td></td>

									 <td></td>

									 <td></td>

									 <td style="text-align:right;"><span class="tot_stone_price" style="text-align:right;"></span></td>

									 <td style="text-align:right;"><span class="other_metal_amt" style="text-align:right;"></span></td>

									 <td style="text-align:right;"><span class="other_chrg_amt" style="text-align:right;"></span></td>

									 <td style="text-align:right;"><span class="total_amt" style="text-align:right;"></span></td>

									 <td></td>

								 </tr>

							 </tfoot>

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

								<!--<div class="col-md-6">

									<label>Total Summary Details</label>

								   <div class="table-responsive">

									 <table id="total_summary_details" class="table table-bordered table-striped">

										<tbody> 

											<tr>

												<th>Pure Weight</th>

												<th><span class="tot_paybale_purewt"></span></th>

												<th>Amount</th>

												<th><input type="number" class="form-control total_summary_payable_amt" readonly></th>

											</tr>

											<tr>

												<th>Pcs</th>

												<th><span class="total_pcs"></span></th>

												<th>Other Charges</th>

												<th>

													<div class="input-group ">

														<input id="other_charges_amount"  name="order[other_charges_amount]" class="form-control custom-inp add_other_charges" type="number" step="any" readonly tabindex="19" />

														<span class="input-group-addon input-sm add_other_charges">+</span>

														<input type="hidden" id="other_charges_details"  name="order[other_charges_details]" />

													</div>

												</th>

											</tr>

											<tr>

												<th></th>

												<th></th>

												<th>Discount</th>

												<th>

													<input type="number" class="form-control tot_discount" name="order[discount]" id="tot_discount" tabindex="20"  />

												</th>

											</tr>

											<tr>

												<th></th>

												<th></th>

												<th>TCS</th>

												<th>

													<div class="input-group" >

														   <input class="form-control tcs_percent" type="number" name="order[tcs_percent]" id="tcs_percent" tabindex="21"  />

														   <span class="input-group-btn"><input type="number" class="form-control tcs_tax_value" name="order[tcs_tax_value]" id="tcs_tax_value" tabindex="22" style="width:200px;" readonly /></span>

													</div>

												</th>

											</tr>

											<tr style="display:none;">

												<th></th>

												<th></th>

												<th>TDS</th>

												<th>

													<div class="input-group" >

														   <input class="form-control tds_percent" type="number" name="order[tds_percent]" id="tds_percent" tabindex="21"  />

														   <span class="input-group-btn"><input type="number" class="form-control tds_tax_value" name="order[tds_tax_value]" id="tds_tax_value" tabindex="22" style="width:200px;" readonly /></span>

													</div>

												</th>

											</tr>

											<tr>

												<th></th>

												<th></th>

												<th>Final Price</th>

												<th><input type="number" class="form-control total_cost" name="order[total_cost]"><input type="hidden" class="form-control tot_purchase_wt" name="order[tot_purchase_wt]"></th>

											</tr>

												

										</tbody>

										<tfoot>

											<tr></tr>

										</tfoot>

									 </table>

									

								  </div>

								</div>-->

								

								<div class="col-md-6">

									<label>Total Summary Details</label>

								   <div class="table-responsive">

									 <table id="total_summary_details" class="table table-bordered table-striped" style="text-transform: uppercase">

										<tbody> 

											<tr>

										

												<th>Pure Weight</th>

												<th></th>

												<th><span class="tot_paybale_purewt"></span><input type="hidden" class="form-control tot_purchase_wt" name="order[tot_purchase_wt]"></th>

											</tr>

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

														   <input class="form-control po_tds_percent" type="number" name="order[tds_percent]" id="po_tds_percent" tabindex="21" value="<?php echo $po_item['tds_percent'];?>" readonly/><input type="hidden" id="bal_tds_percent" name="order[tds_percent]" value="">

														   <span class="input-group-btn"><input type="number" class="form-control item_tds_tax_value" name="order[tds_tax_value]" id="item_tds_tax_value" tabindex="22" style="width:200px;"  readonly/></span>

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

														   <input class="form-control po_tcs_percent" type="number" name="order[tcs_percent]" id="po_tcs_percent" tabindex="21"  readonly/>

														   <span class="input-group-btn"><input type="number" class="form-control item_tcs_tax_value" name="order[tcs_tax_value]" id="item_tcs_tax_value" tabindex="22" style="width:200px;" readonly /></span>

													</div>

												</th>

											</tr>

											<tr>

												<th>Other Charges</th>

												<th>(+)</th>

												<th><input id="other_charges_taxable_amount"  name="order[other_charges_amount]" class="form-control custom-inp" type="number" step="any" readonly tabindex="19" /><input type="hidden" id="other_charges_details"  name="order[other_charges_details]" /></th>

											</tr>

											<tr>

												<th>Charges TDS</th>

												<th>(-)</th>

												<th>

													<div class="input-group" >

														   <input class="form-control charges_tds_percent" type="number" name="order[charges_tds_percent]" id="charges_tds_percent" tabindex="21"  readonly/>

														   <span class="input-group-btn"><input type="number" class="form-control other_charges_tds_tax_value" name="order[other_charges_tds_tax_value]" id="other_charges_tds_tax_value" tabindex="22" style="width:200px;" readonly /></span>

													</div>

											</tr>

											<tr>

												<th>Other Charges Tax</th>

												<th>(+)</th>

												<th><input type="number" class="form-control other_charges_tax" name="order[other_charges_tax]" id="other_charges_tax" readonly></th>

											</tr>

											

											<tr>

											

												<th>Discount</th>

												<th>(-)</th>

												<th>

													<input type="number" class="form-control po_discount" id="po_discount" name="order[discount]" tabindex="20"  readonly/>

												</th>

											</tr>

											

											<tr>

												

												<th>Round Off</th>

												<th></th>

												<th><input type="number" class="form-control grn_round_off" name="order[round_off]" readonly></th>

											</tr>

											<tr>

												

												<th>Final Price</th>

												<th></th>

												<th><input type="number" class="form-control total_cost" name="order[total_cost]" readonly></th>

											</tr>

												

										</tbody>

										<tfoot>

											<tr></tr>

										</tfoot>

									 </table>

									

								  </div>

								</div>

								

								<div class="col-md-6">

									<label>GRN Entry Details</label>

									<div class="table-responsive">

										<table id="grn_item_details_preview" class="table table-bordered table-striped" style="text-transform: uppercase">

											<thead>

											  <tr>

												<th width="10%;">Category</th>

												<th width="5%;">Pieces</th>

												<th width="5%;">Weight</th>

												<th width="5%;">Less wt</th>

												<th width="5%;">Net wt</th>

												<th width="5%;">Dia wt</th>

												<th width="5%;">Rate</th>

												<th width="5%;">Amount</th>

											  </tr>

											</thead>

											<tbody></tbody>

											<tfoot>

									<tr style="font-weight:bold;">

									 <td  style="text-align:center;">TOTAL</td>

									 <td style="text-align:left;"><span class="total_gt_pcs" ></span></td>

									 <td style="text-align:left;"><span class="total_gt_gwt" style="text-align:right;"></span></td>

									 <td style="text-align:left;"><span class="total_gt_lwt" style="text-align:right;"></span></td>

									 <td style="text-align:left;"><span class="total_gt_nwt" style="text-align:right;"></span></td>

									 <td style="text-align:left;"><span class="total_gt_diawt" style="text-align:right;"></span></td>

									 <td style="text-align:left;"><span class="total_gt_rate" style="text-align:right;"></span></td>

									 <td style="text-align:left;"><span class="total_gt_amount" style="text-align:right;"></span></td>

								   </tr>

									 <tr style="font-weight:bold;">

									 <td style="text-align:center;">Difference</td>

									 <td style="text-align:left;"><span class="total_gb_pcs" >-</span></td>

									 <td style="text-align:left;"><span class="total_gb_gwt" style="text-align:left;">-</span></td>

									 <td style="text-align:left;"><span class="total_gb_lwt" style="text-align:left;">-</span></td>

									 <td style="text-align:left;"><span class="total_gb_nwt" style="text-align:left;">-</span></td>

									 <td style="text-align:left;"><span class="total_gb_diawt" style="text-align:left;">-</span></td>

									 <td><td>

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

				   <div class=""><br/>

					  <div class="col-xs-offset-5">

						<button type="button" id="submit_pur_entry" class="btn btn-primary">Save</button>

						<button type="button" class="btn btn-default btn-cancel">Cancel</button>

						

					  </div> <br/>

					</div>

				</div>

		  

			</div>

		</div>

		<?php echo form_close();?>

		 </div>

		  <div class="overlay" style="display:none;">

		  <i class="fa fa-refresh fa-spin"></i>

		</div>

	   </div>  

	   

	   

   </div>  

</section>

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

				<tfoot><tr style="font-weight:bold;"><td>Total</td><td></td><td class="total_pcs"></td><td class="total_wt"></td><td></td><td></td><td></td><td></td><td class="total_amount"></td><td></td></tr></tfoot>

			</table>

		</div>

	</div>

  <div class="modal-footer">

	<button type="button" id="update_other_metal_details" class="btn btn-success">Save</button>

	<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>

  </div>

</div>

</div>

</div>







<div class="modal fade" id="cus_stoneModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog" style="width:95%;">

<div class="modal-content">

	<div class="modal-header">

		<h4 class="modal-title" id="myModalLabel">Stone Details</h4>

	</div>

	<div class="modal-body">

		<div class="row">
			<div class="col-md-2"> 
				<div class="form-group" >
					<label>Aganist Karigar Issue</label><br>
					<!--<input type="radio" id="aganist_supplier_issue" name="aganist_supplier_issue" value="1" ><label for="aganist_supplier_issue_yes">&nbsp;Yes</label>
					<input type="radio" id="aganist_supplier_issue" name="aganist_supplier_issue" value="0" checked><label for="aganist_supllier_issue_no">&nbsp;No</label>-->
					<input type="checkbox" id="aganist_supplier_issue" name="aganist_supplier_issue" value="0">
				</div>
			</div>
			<div class="col-md-2"> 
				<div class="form-group" >
					<label>Select Issue Ref No</label> 
					<select class="form-control" id="select_issue_ref_no" name="select_issue_ref_no" style="width:100%;" disabled></select>
				</div>
			</div>
		</div>
		<div class="col pull-right stn_add" style="display:none;">
			<button type="button" id="create_karigarissue_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>
		</div>

		<div class="row">

				<input type="hidden" id="stone_active_row" value="0">

				<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">

					<thead>

						<tr>

							<th width="5%">LWT</th>

							<th width="5%" style="display:none" class="metalissse_product">Product</th>

							<th width="10%">Type</th>

							<th width="13%">Name</th>

							<th width="13%">Code</th>

							<th width="10%">Pcs</th>   

							<th width="17%">Wt</th>

							<th width="10%">Cal.Type</th>

							<th width="10%">Cut</th>

							<th width="10%">Color</th>

							<th width="10%">Clarity</th>

							<th width="10%">Shape</th>

							<th width="10%">Rate</th>

							<th width="15%">Amount</th>

							<th width="10%">Action</th>

							

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

	<button type="button" id="update_stone_details" class="btn btn-success">Save</button>

	<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

  </div>

</div>

</div>

</div>



<div class="modal fade" id="cus_orderdetailsModal" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog" style="width:73%;">

<div class="modal-content">

	<div class="modal-header">

		<h4 class="modal-title" id="myModalLabel">Order Details</h4>

	</div>

	<div class="modal-body">

		<div class="row">

			 <div class="col-md-12">

						

						<div class="col-md-6">

							 <div class="form-group">

							   <label>Ordered Pcs : </label>

								<strong>  

									  <span id="orderedpcs"></span> 

								</strong>

							 </div> 

						</div>

						<div class="col-md-6">

							 <div class="form-group">

							   <label>Ordered Wt : </label>

								<strong>  

									  <span id="orderedwt"></span> 

								</strong>

							 </div> 

						</div>

						<div class="col-md-6">

							 <div class="form-group">

							   <label>Received Pcs : </label>

								<strong>  

									  <span id="receivedpcs"></span> 

								</strong>

							 </div> 

						</div>

						<div class="col-md-6">

							 <div class="form-group">

							   <label>Received Wt : </label>

								<strong>  

									  <span id="receivedwt"></span> 

								</strong>

							 </div> 

						</div>

			</div>

		</div>

		<div class="row">

				<input type="hidden" id="cur_order_row" value="0">

				<table id="order_items_details" class="table table-bordered table-striped text-center">

					<thead>

						<tr>

							<th width="10%">Category</th>

							<th width="10%">Product</th>

							<th width="10%">Design</th>

							<th width="10%">Sub Design</th>   

							<th width="7%">Pcs</th>

							<th width="7%">Weight Range</th>

							<th width="7%">Rec Pcs</th>

							<th width="10%">Rec Wt</th>

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

	<button type="button" id="close_order_details" class="btn btn-warning" data-dismiss="modal">Close</button>

  </div>

</div>

</div>

</div>



<!-- Charges Modal -->



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

						

						<th>Type</th>



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



<!-- <div class="modal fade" id="other_metalmodal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

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

				<tfoot><tr style="font-weight:bold;"><td>Total</td><td></td><td class="total_pcs"></td><td class="total_wt"></td><td></td><td></td><td></td><td></td><td class="total_amount"></td><td></td></tr></tfoot>

			</table>

		</div>

	</div>

  <div class="modal-footer">

	<button type="button" id="update_other_metal_details" class="btn btn-success">Save</button>

	<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>

  </div>

</div>

</div>

</div> -->

	



<div class="modal fade" id="confirm-edit"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog">

<div class="modal-content">

	<div class="modal-header">

		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

		<h4 class="modal-title" id="myModalLabel">Edit Karigar</h4>

	</div>

	<div class="modal-body">

		<div class="row">

			<div class="form-group">

			   <label for="kar_first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error">*</span></label>

			   <div class="col-md-6">

					<input type="text" class="form-control" id="kar_first_name" name="kar[first_name]" placeholder="Enter Karigar first name" required="true"> 



					<p class="help-block kar_first_name error"></p>

			   </div>

			</div>

		</div> 

		<div class="row">

			<div class="form-group">

			   <label for="kar_pan_no" class="col-md-3 col-md-offset-1 ">Pan No<span class="error">*</span></label>

			   <div class="col-md-6">

					<input type="text" class="form-control" id="kar_pan_no" name="kar[kar_pan_no]" placeholder="Enter Karigar Pan No" required="true"> 

					<p class="help-block kar_first_name error"></p>

			   </div>

			</div>

		</div> 

		<div class="row">   

			<div class="form-group">

			   <label for="" class="col-md-3 col-md-offset-1 ">Select Country<span class="error">*</span></label>

			   <div class="col-md-6">

				 <select class="form-control" id="country" style="width:100%;"></select>

				 <input type="hidden" name="kar[id_country]" id="id_country"> 

			   </div>

			</div>

		</div></br>

		<div class="row">   

			<div class="form-group">

			   <label for="" class="col-md-3 col-md-offset-1 ">Select State<span class="error">*</span></label>

			   <div class="col-md-6">

				 <select class="form-control" id="ed_state" style="width:100%;"></select>

				  <input type="hidden" name="kar[id_state]" id="ed_id_state">

			   </div>

			</div>

		</div></br>

		

		 <div class="row">   

			<div class="form-group">

			   <label for="" class="col-md-3 col-md-offset-1 ">Select City<span class="error">*</span></label>

			   <div class="col-md-6">

				 <select class="form-control" id="ed_city"  style="width:100%;"></select>

				  <input type="hidden" name="kar[id_city]" id="ed_id_city">

			   </div>

			   

			</div>

		</div></br>

	

		<div class="row">

			<div class="form-group">

				<label for="address1" class="col-md-3 col-md-offset-1 ">Address1<span class="error">*</span></label>

				   <div class="col-md-6">

						<input class="form-control" id="address1" name="kar[address1]" value=""  type="text" placeholder="Enter Address Here 1" required />

						<p class="help-block address1 error"></p>

					</div>

			</div>

		</div></br>

		<div class="row">

			<div class="form-group">

				<label for="address2" class="col-md-3 col-md-offset-1">Address2</label>

				   <div class="col-md-6">

						<input class="form-control" id="address2" name="kar[address2]" placeholder="Enter Address Here 2" value=""  type="text" />

					</div>

			</div>

		</div></br>

		<div class="row">

			<div class="form-group">

				<label for="address3" class="col-md-3 col-md-offset-1">Address3</label>

				   <div class="col-md-6">

						<input class="form-control titlecase" id="address3" name="kar[address3]" value=""  type="text" placeholder="Enter Address Here 3" />

					</div>

			</div>

		</div></br>

		<div class="row">

			<div class="form-group">

				<label for="pincode" class="col-md-3 col-md-offset-1">Pin Code<span class="error">*</span></label>

				   <div class="col-md-6">

						<input class="form-control titlecase" id="pin_code_add" type="text" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' required />

						<p class="help-block pincode error"></p>

					</div>

			</div>

		</div></br>



		<div class="row gst">   

			<div class="form-group">

			   <label for="" class="col-md-3 col-md-offset-1 ">GST No<span class="error">*</span></label>

			   <div class="col-md-6"> 

					<input type="text" class="form-control" id="gst_no" name="kar[gst_no]" placeholder="Enter GST No"> 

					<p class="help-block kar_mobile"></p>

			   </div>

			</div>

		</div>

	</div>

  <div class="modal-footer">

	 <input type="hidden" name="kar[id_karigar]" id="id_karigar" value="">

	 <a href="#" id="update_kardetails" class="btn btn-success">Update</a>

	<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

  </div>

</div>

</div>

</div>

<div class="modal fade" id="other_metal_issue_modal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:85%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Kargiar Issue Details</h4>

			</div>

			<div class="modal-body">

				<div class="row">

        			<div class="col pull-right">

        			</div>

    			</div>

				<div class="row">

					<input type="hidden" id="charge_active_row" value="0">

					<table id="karigar_metal_issue_table" class="table table-bordered table-striped text-center">

    					<thead>

        					<tr>

            					<th width="15%">Category</th>

            					<th width="10%">Product</th>

            					<th width="10%">Design</th>

            					<th width="10%">Sub Design</th>

            					<th width="10%">Purity</th>

            					<th width="10%">Actual Pcs</th>

								<th width="10%">Actual Weight</th>

            					<th width="10%">Pcs</th>

            					<th width="10%">Weight</th>

            					<th width="10%">Action</th>

        					</tr>

    					</thead> 

    					<tbody></tbody>	
						<tfoot>

							<tr style="font-weight:bold;font-size:15px">
								<td>Total:</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td class="tot_actual_issued_pcs"></td>
								<td class="tot_actual_issued_weight"></td>
								<td class="tot_issued_pcs"></td>
								<td class="tot_issued_weight"></td>							
								<td></td>
							</tr>

						</tfoot>									


					</table>

			    </div>

		    </div>

		  <div class="modal-footer">

			<button type="button" id="update_karigar_metalissue_details" class="btn btn-success">Save</button>

			<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

