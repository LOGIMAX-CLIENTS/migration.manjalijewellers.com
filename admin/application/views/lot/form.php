      <!-- Content Wrapper. Contains page content -->


<style>


.remove-btn{



	margin-top: -168px;



	margin-left: -38px;



	background-color: #e51712 !important;



	border: none;



	color: white !important;



}



.removeimg-btn{



	margin-top: -133px;



	margin-left: -38px;



	background-color: #bbb2b1 !important;;



	border: none;



	color: white !important;



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



<section class="content-header">



  <h1>



	Lot



	<small>Lot Inward</small>



  </h1>



  <ol class="breadcrumb">



	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>



	<li><a href="#">Lot</a></li>



	<li class="active">Lot Inward</li>



  </ol>



</section>



<!-- Main content -->



<section class="content ">



  <!-- Default box -->



  <div class="box box-primary">



	<div class="box-header with-border">



	  <h3 class="box-title">Add Lot Inward</h3>



	  <div class="box-tools pull-right">



		<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>



		<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>



	  </div>



	</div>



	<div class="box-body">



	 <!-- form container -->



	  <div class="row">



		 <!-- form -->



		<?php



		$attributes 		=	array('id' => 'lot_form', 'name' => 'lotForm','target'=>'_blank');



		echo form_open_multipart(( $inward['lot_no']!=NULL && $inward['lot_no']>0 ?'admin_ret_lot/lot_inward/update/'.$inward['lot_no']:'admin_ret_lot/lot_inward/save'),$attributes); ?>


		<input id="lot_no" name="inward[lot_no]" type="hidden" value="<?php echo set_value('inward[lot_no]',$inward['lot_no']); ?>" />


		<input type="hidden" name="is_purchase_cost_from_lot"  id="is_purchase_cost_from_lot"  value="<?php echo set_value('is_purchase_cost_from_lot',$is_purchase_cost_from_lot); ?>"  >

		<input id="cmp_country" name="inward[cmp_country]" type="hidden" value="<?php echo set_value('inward[cmp_country]',$comp_details['id_country']); ?>" />

		<input id="cmp_state" name="inward[cmp_state]" type="hidden" value="<?php echo set_value('inward[cmp_state]',$comp_details['id_state']); ?>" />



		<div class="col-sm-12">



			<!--Block 1-->



			 <div class="row">



				<div class="col-sm-2">


				 <label>Lot Received At <span class="error"> *</span></label>

							<?php
							 if($inward['lot_receive_settings'] == 1) // Any Branch
							 {
							 ?>
								<div class="form-group" >

									<select id="lt_rcvd_branch_sel" class="ret_branch form-control" required="true"></select>

								</div>
							 <?php

							 }else{

							 ?>
								 <span ><?php echo $inward['rcvd_branch_name']; ?></span>

							 <?php }?>


							 <input id="id_branch" name="inward[lot_received_at]" type="hidden" value="<?php echo set_value('inward[lot_received_at]',$inward['lot_received_at']); ?>"/>



				 </div>



				 <div class="col-sm-2">


							<label>Gold Smith<span class="error tagged"> *</span></label>


							  <div class="form-group" >



								<select id="lt_gold_smith" class="form-control"></select>

								<input  id ="supplier_state" type="hidden" name="">

								<input  id ="supplier_country" type="hidden" name="">

								<input  id ="tds_percent" type="hidden" name="">

								<input  id ="tcs_percent" type="hidden" name="">


								<input id="lt_gold_smith_id" name="inward[gold_smith]"type="hidden" value="<?php echo set_value('inward[gold_smith]',$inward['gold_smith']); ?>"/>


							</div>

				 </div>



				 <div class="col-sm-2">


							<label>Stock Type </label>


							  <div class="form-group" >



								<input type="radio" id="stock_type" name="inward[stock_type]" value="1" <?php echo $inward['stock_type'] == 1? 'checked="true"':''; ?>> Tagged



								&nbsp;&nbsp;&nbsp;



								<input type="radio" id="stock_type" name="inward[stock_type]" value="2" <?php echo $inward['stock_type'] == 2? 'checked="true"':''; ?>> Non-Tagged



							</div>


				 </div>

				 <input type="hidden"  id="lt_type_select" name="inward[lot_type]" value ="1">


				<div class="col-sm-2">

							<label>Category <?php echo $inward['stock_type'] == 1? '<span class="error tagged"> *</span>':''; ?></label>

							  <div class="form-group" >

								<select id="category" class="form-control" <?php echo $inward['stock_type'] == 1? 'required="true"':''; ?>></select>

								<input id="id_category" name="inward[id_category]" type="hidden" value="<?php echo set_value('inward[id_category]',$inward['id_category']); ?>" />

							</div>

				 </div>

		 <?php

			if($inward['is_supplierbill_entry_req']==0)

			{?>

				 <div class="col-sm-2">

								<label>Select GRN</label>

								  <div class="form-group" >

								  <select id="select_grn" name="inward[grn_id]" class="form-control"></select>

								</div>

				 </div>


				 <?php }

					?>


				<div class="col-sm-2">



							<label>Purity <?php echo $inward['stock_type'] == 1? '<span class="error tagged"> *</span>':''; ?></label>

							  <div class="form-group" >



								<select id="purity" class="form-control" <?php echo $inward['stock_type'] == 1? 'required="true"':''; ?>></select>



								<input id="id_purity" name="inward[id_purity]" type="hidden" value="<?php echo set_value('inward[id_purity]',$inward['id_purity']); ?>" />



							</div>

				 </div>

			 </div>



			 <p class="help-block"></p>

			 <hr  style ="border-top:3px solid #3c8dbc;">

			 <!--/Block 1-->

	<!-- <div class="box-header with-border">

			<h5 class="box-title">Add Lot Items</h5>

			</div> -->

	<div  id="item_details">

		<div class="row">



			<div class="col-md-12">



			 <!-- <h7>ITEM  DETAILS</h7> -->



			 <input type="hidden" id="lot_inward_details_id" class="form-control" value="" >
 
 			 <input type="hidden" id="lot_tagged_count" class="form-control" value="0" >

			 <input type="hidden" id="id_sub_design" class="form-control" value="" >

			 <input type="hidden" id="id_design" class="form-control" value="" >

				<div class="col-md-2">

					 <div class="form-group">

					   <label>Select Section<span class="error">*</span></label>

							<select id="select_section" class="form-control lot_section " name="order[purchase_order_no]" style="width:100%;"   disabled ></select>

					 </div>

				</div>



				<!--<div class="col-md-2">-->

				<!--	 <div class="form-group">-->

				<!--	   <label>Select Category<span class="error">*</span></label>-->

				<!--			<select id="select_category" name="order[id_category]" class="form-control" style="width:100%;" ="2"></select>-->

				<!--			<input type="hidden" id="id_category" class="form-control" value="" >-->

				<!--	 </div> -->

				<!--</div>-->

				<!--<div class="col-md-2">-->

				<!--	 <div class="form-group">-->

				<!--	   <label>Select Purity<span class="error">*</span></label>-->

				<!--			<select id="select_purity" name="order[id_purity]" class="form-control"  style="width:100%;" ="3" <?php echo $po_item['supplier_bill_entry_calc']==1 ? '': '' ?> ></select>-->

				<!--			<input type="hidden" name="order[purity]" class="form-control" value="" id="id_purity" />-->

				<!--	 </div> -->

				<!--</div>-->

				 <div class="col-md-2">

					 <div class="form-group">

					   <label>Select Product<span class="error">*</span></label>

							<select id="select_product" class="form-control lot_product" name="order[id_product]" style="width:100%;"  ></select>

							<input type="hidden" class="form-control purchase_mode" id="purchase_mode" value="" />

							<input type="hidden" class="form-control sales_mode" id="sales_mode" value="" />

							<input type="hidden" id="pro_id" class="form-control" value="" >

					 </div>

				</div>

				 <div class="col-md-2 oranments oranmentspo">

					 <div class="form-group">

					   <label>Select Design<span class="error">*</span></label>

							<select id="select_design" class="form-control design"  style="width:100%;" ></select>

							<input type="hidden" id="design_id" class="form-control" value="" >



					 </div>

				</div>

				 <div class="col-md-2 oranments oranmentspo">

					 <div class="form-group">

					   <label>Sub Design<span class="error">*</span></label>

							<select id="select_sub_design" class="form-control lot_sub_design"  style="width:100%;" ></select>

							<input type="hidden" id="sub_design_id" class="form-control" value="" >

					 </div>

				</div>

				<!-- <div class="col-md-2">

					 <div class="form-group">

					   <label>Design For<span class="error">*</span></label>

							<select class="sel_design_for form-control" id="sel_desingn_for" style="width:80px;" autocomplete="off">
								<option value="1">Male</option>
								<option value="2">Female</option>
								<option value="3">Unisex</option>
							  </select>

					 </div>

				</div> -->

				<div class="col-md-2 oranments">

					<div class="form-group">

					<label>Calculation Based On<span class="error">*</span></label>

						<select id="calculation_based_on" class="form-control" name="order[calculation_based_on]" style="width:100%;" disabled  >

							<option value="0">Mc & Wast On Gross</option>

							<option value="1" selected>Mc & Wast On Net</option>

							<option value="2">Mc on Gross,Wast On Net</option>

							<option value="3">Fixed Rate</option>

							<option value="4">Fixed Rate based on Weight</option>

						</select>

					</div>

				</div>

			 </div>

		 </div>

		 <div class="row">

			 <div class="col-md-12">

			 <!--	<div class="col-md-2">-->

				<!--		<div class="form-group">-->

				<!--		<label>Quality Code<span class="error"></span></label>-->

				<!--		<select id="select_quality" class="form-control" name="order[quality_id]" style="width:100%;" ="10" disabled></select>-->

				<!--		</div> -->

				<!--</div>-->

			   <div class="col-md-2">

					 <div class="form-group">

					   <label>Pcs<span class="error">*</span></label>

						<input type="number" class="form-control" id="lot_pcs" >

					 </div>

				</div>

				<!-- <div class="col-md-2 oranments-bullion">

					 <div class="form-group">

					   <label>GWT<span class="error">*</span></label>

					   <div class="input-group"style="width:200px;">
							<input type="number" class="form-control" id="lot_gross_wt"  style="width:120px;"><input type="hidden" class="form-control" id="order_gwt">
							<span class="input-group-btn">
								<select id="lot_gross_uom" class="form-control wt_uom"  style="width:80px;">

								</select>
							</span>
						</div>
					 </div>

				</div> -->


				<div class="col-md-2 oranments">

					 <div class="form-group">

					   <label>GWT<span class="error">*</span></label>

					   <div class="input-group">

						<input type="number" class="form-control" id="lot_gross_wt"  >
						<span class="input-group-btn">
							<select id="lot_gross_uom" class="form-control wt_uom"  style="width:65px;">

							</select>
						</span>

					  </div>

					 </div>

				</div>



				<!--<div class="col-md-2 stone_purchase" style="display:none;">

					 <div class="form-group">

					   <label>GWT<span class="error">*</span></label>

						 <div class="input-group" style="width:200px;">

							   <input class="stone_wt form-control" type="number" name="stone_wt" value="" style="width: 120px;" ="11">

							   <span class="input-group-btn" style="width: 80px;"><select class="stone_uom_id form-control" name="uom_id"></select></span>

							   <input type="hidden" class="form-control" id="order_gwt_uom" >

						 </div>

					 </div>

				 </div>



				<div class="col-md-2 stone_purchase" style="display:none;">

					 <div class="form-group">

					   <label>Cal Type</label>

						<div class="form-group" >

								<input type="radio" class="stone_cal_type" id="call_by_wt" name="cal_type" value="1" ="12" checked> <label class="custom-label" for="call_by_wt">Wt</label>

								<input type="radio" class="stone_cal_type" id="call_by_pcs" name="cal_type" value="2"><label class="custom-label" for="call_by_pcs">Pcs</label>

							</div>

					 </div>

				</div>-->





				<div class="col-md-2 oranments">

					  <div class="form-group">

							<label>LWT</label>

							<div class="input-group ">

								<input id="lot_lwt" class="form-control custom-inp add_stone_details" type="number" step="any" readonly  />

								<input type="hidden" id="lot_stone_details" />

								<input type="hidden" id="stone_price" />

								<span class="input-group-addon input-sm add_tag_lwt" >+</span>
								<!-- <span class="input-group-addon input-sm add_stone_details">+</span> -->
								<!-- <span class="input-group-btn">
									<select id="lot_lwt_uom" class="form-control"  style="width:80px;">
										<option value="1" selected>Gram</option>
										<option value="6">Carat</option>
									</select>
								</span> -->
							</div>

						</div>

				</div>

				<div class="col-md-2 oranments">

					 <div class="form-group">

					   <label>NWT<span class="error">*</span></label>

					   <div class="input-group ">

						<input type="number" class="form-control" id="lot_net_wt"  readonly>
						<span class="input-group-btn">
							<select id="lot_net_uom" class="form-control wt_uom"  style="width:65px;">

							</select>
						</span>

					  </div>

					 </div>

				</div>

				<!-- <div class="col-md-2 oranments">

					 <div class="form-group">

					 <label>Purchase Mc<span class="error">*</span></label>

					 <div class="input-group ">



					   <input type="number" class="form-control"  id="mc_value" placeholder="M.C"  >

					   <span class="input-group-btn">

							<select id="mc_type" class="form-control" name="order[mc_type]" style="width:80px;" >

								<option value="1">Per Gram</option>

								<option value="2">Per Piece</option>

							</select>

						</span>

						</div>

					 </div>

				</div> -->

				<!-- <div class="col-md-1 oranments">

					 <div class="form-group">

					   <label>Purchase MC <span class="error">*</span></label>

						<input type="number" class="form-control"  id="mc_value" placeholder="M.C"  >



					 </div>

				</div> -->

			<!-- <div class="col-md-2 oranments">

					<div class="form-group">

					<label>Purchase Wastage(%) <span class="error">*</span></label>

						<input type="number" class="form-control" id="lot_wastage"   >

					</div>

			</div> -->


			<div class="col-md-2 oranments">

				<div class="form-group">

					<label>Other Metals</label>

					<div class="input-group ">

						<input type="hidden" id="other_metal_wt">

						<input type="hidden" id="other_metal_wast_wt">

						<input type="hidden" id="other_metal_mc_amount">

						<input id="other_metal_amount" class="form-control custom-inp add_other_metals" type="number" step="any" readonly />

						<span class="input-group-addon input-sm add_other_metals">+</span>

						<input type="hidden" id="other_metal_details">

					</div>

				</div>

				</div>

			<div class="col-md-2 oranments">

				<div class="form-group">

					<label>Other Charges</label>

					<div class="input-group ">

						<input id="other_charges_amount" class="form-control custom-inp add_other_charges" type="number" step="any" readonly  />

						<span class="input-group-addon input-sm add_other_charges">+</span>

						<input type="hidden" id="other_charges_details">

					</div>

				</div>

			</div>




				<!-- <div class="col-md-2 oranments">

				<label> Stone<span class="error">*</span></label><br>

				<a href="#" onclick="show_stone_modal_new($('#lt_item_list tbody').length+1);" class="btn btn-default btn-sm"><i class="fa fa-plus-circle"></i></a>
				<input type="hidden" class="precious_stone" name="" >
				<input type="hidden" class="precious_stone_pcs" name="">
				<input type="hidden" class="precious_stone_wt" name="" >
				<input type="hidden" class="p_stn_certif_uploaded" name="">
				<input type="hidden" class="precious_st_certif" name="" >
				<input type="hidden" class="semi_precious_stn" name="" >
				<input type="hidden" class="semi_precious_st_pcs" name="" >
				<input type="hidden" class="semi_precious_st_wt" name="">
				<input type="hidden" class="sp_stn_certif_uploaded" name="">
				<input type="hidden" class="semiprecious_st_certif" name="" >
				<input type="hidden" class="normal_stn" name="" >
				<input type="hidden" class="normal_st_pcs" name="" >
				<input type="hidden" class="normal_st_wt" name="">
				<input type="hidden" class="normal_st_certif" name="" >
				<input type="hidden" class="n_stn_certif_uploaded" name="">
				<input type="hidden" name="" class="nor_wt_uom" >
				<input type="hidden" name="" class="semi_wt_uom" >
				<input type="hidden" name="" class="pre_wt_uom" >



				</div> -->


			 </div>

		 </div>

		 <div class="row">

			<div class="col-md-12">

			 <!--  <div class="col-md-2 oranments">

				<div class="form-group">

					<label>Wastage Wgt</label>

					<input type="number" class="form-control" id="lot_wastage" ="14" disabled >

					<input type="hidden" id="wastage_type">

				</div>

			</div> -->







			<?php if($is_purchase_cost_from_lot == 1){?>


				<div class="col-md-2 oranments">

					 <div class="form-group">

					 <label>Purchase Mc<span class="error">*</span></label>

					 <div class="input-group ">



					   <input type="number" class="form-control"  id="mc_value" placeholder="M.C"  >

					   <span class="input-group-btn">

							<select id="mc_type" class="form-control" name="order[mc_type]" style="width:80px;" >

								<option value="1">Per Gram</option>

								<option value="2">Per Piece</option>

							</select>

						</span>

						</div>

					 </div>

				</div>

				<div class="col-md-2 oranments">

					<div class="form-group">

					<label>Purchase Wastage(%) <span class="error">*</span></label>

						<input type="number" class="form-control" id="lot_wastage"   >

					</div>

			    </div>



				<div class="col-md-2 oranments">

					 <div class="form-group">

					   <label>Purchase Touch<span class="error">*</span></label>

						<input type="number" class="form-control purchase_touch" name="order[purchase_touch]" id="purchase_touch"  value="92" >

					 </div>

				</div>






				<div class="col-md-2 oranments">

					<div class="form-group">

							<label for="">Type<span class="error">*</span></label>

							<select class="form-control" id="karigar_calc_type" name="order[karigar_calc_type]" >

								<option value="1" > Weight x Rate</option>

								<option value="2">Purchase Touch</option>

								<option value="3" selected  >Weight x Wastage %</option>

							</select>

					</div>

				</div>

				<div class="col-md-2 oranments">

					 <div class="form-group">

					   <label>PURE<span class="error">*</span></label>

						<input type="number" class="form-control" name="order[tot_purewt]" id="tot_purewt" readonly>

					 </div>

				</div>

				<div class="col-md-2">

					<div class="form-group">

						<label>Purchase Rate<span class="call_type_label"></span><span class="tax_type"></span></label>
						<div class="input-group" >
							<input type="text" class="form-control"  id="rate_per_gram" placeholder="Per Gram" >
							<span class="input-group-btn">
								<select id="rate_calc_type" class="form-control" style="width:65px;" >
									<option value="1" selected>Gram</option>
									<option value="2">Pcs</option>
								</select>
							</span>
						</div>

					</div>

				</div>

				<?php }?>



				<!--<div class="col-md-2 oranments">-->

				<!--	 <div class="form-group">-->

				<!--	   <label>PURE<span class="error">*</span></label>-->

				<!--		<input type="number" class="form-control" name="order[tot_purewt]" id="tot_purewt" readonly>-->

				<!--	 </div> -->

				<!--</div>-->


			 </div>

		 </div>

		 <div class="row">

			 <div class="col-md-12">





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

						<!-- <input type="hidden" class="form-control" id="item_total_tax"> -->

						<input type="hidden" class="form-control" id="item_cgst_cost">

						<input type="hidden" class="form-control" id="item_sgst_cost">

						<input type="hidden" class="form-control" id="item_igst_cost">

						<input type="hidden" class="form-control" id="item_tax_percentage">

						<input type="hidden" class="form-control" id="tax_type">

						<input type="hidden" class="form-control" id="tax_group_id">

						<!-- <input type="hidden" class="form-control" id="calculation_based_on"> -->

					 </div>

				</div>

				</div>

			</div>


		 </div>

		<div class="row">

			<div class="col-md-12">

					<div class="form-group" align="center">

						<button id="add_lot_items" type="button" class="btn btn-success" ><i class="fa fa-plus"></i> Add Item </button>

					</div>

				</div>

		</div>

	</div>

	<br>
	<br>
	<br>



			 <!--Block 2-->



			 <!-- <button class="btn btn-success pull-right" type="button" id="add_lot_item"><i class="fa fa-plus-circle"></i> Add Item</button> -->



			 <div class="row">



				 <div class="col-sm-12">



					 <div class="table-responsive">



					 <table id="lt_item_list" class="table table-bordered table-striped text-center">



						 <input type="hidden" id="curRow" name="curRow" value="-1">



						<thead>



						  <tr>



							<th width="5%">Section</th>

							<th width="5%">Product</th>



							<th width="5%">Design</th>

							<th width="5%">Sub Design</th>







							<th width="5%">Pieces</th>



							<th width="15%">Gross Wgt</th>



							<th width="15%">Less Wgt</th>



							<th width="10%">Net Wgt</th>



							<th width="5%">Wast %</th>



							<th width="10%">Making Charge</th>





							<th width="5%">Pur Touch</th>

							<th width="5%">Pur Rate</th>

							<th width="5%">Stone</th>

							<th width="5%">Taxable Amount</th>

							<th width="5%">Tax Rate</th>

							<th width="5%">Amount</th>

							<th width="5%">Action</th>



						  </tr>



						</thead>



						<tbody>



							<!-- <php if($this->uri->segment(3) == 'edit')



							{



							foreach($inward_details as $ikey => $ival){



							$gwt_uom='';



							$lwt_uom='';



							$nwt_uom='';



							$selected=' selected="selected"';



							/* foreach($uom as $u){



								 if($ival['gross_wt_uom'] == $u['uom_id']){



									$gwt_uom.= "<option selected value=".$u['uom_id'].">".$u['code']."</option>";



								}else{



									$gwt_uom.= "<option value=".$u['uom_id'].">".$u['code']."</option>";



								}



								if($ival['net_wt_uom'] == $u['uom_id']){



									$lwt_uom.= "<option selected value=".$u['uom_id'].">".$u['code']."</option>";



								}else{



									$lwt_uom.= "<option value=".$u['uom_id'].">".$u['code']."</option>";



								}



								if($ival['less_wt_uom'] == $u['uom_id']){



									$nwt_uom.= "<option selected value=".$u['uom_id'].">".$u['code']."</option>";



								}else{



									$nwt_uom.= "<option value=".$u['uom_id'].">".$u['code']."</option>";



								}



							} */



							foreach ($uom as $key => $val) {



								$gwt_selected=($val['uom_id']==$ival['gross_wt_uom'] ? 'selected':'');



								$nwt_selected=($val['uom_id']==$ival['net_wt_uom'] ? 'selected':'');



								$lwt_selected=($val['uom_id']==$ival['less_wt_uom'] ? 'selected':'');



								$gwt_uom.= '<option '.$gwt_selected.' value="'.$val['uom_id'].'" >'.$val['code'].'</option>';



								$lwt_uom.= '<option value="'.$val['uom_id'].'" '.$lwt_selected.'>'.$val['code'].'</option>';



								$nwt_uom.= '<option value="'.$val['uom_id'].'" '.$nwt_selected.'>'.$val['code'].'</option>';



							}



							echo '<tr id="'.$ikey.'">



									<td><input type="text" class="lot_product" name="inward_item['.$ikey.'][product]" value="'.$ival['pro_name'].'" style="width:80px;"/><input type="hidden" class="pro_id" name="inward_item['.$ikey.'][lot_product]" value="'.$ival['lot_product'].'" /><input type="hidden" class="sales_mode" name="inward_item['.$ikey.'][sales_mode]" value="'.$ival['sales_mode'].'" /><input type="hidden" id="id_lot_inward_detail" class="id_lot_inward_detail" value="'.$ival['id_lot_inward_detail'].'"></td>



									<td><input type="text" class="design" name="inward_item['.$ikey.'][design]" value="'.$ival['design'].'" required style="width:80px;"/><input type="hidden" class="des_id" name="inward_item['.$ikey.'][lot_id_design]" value="'.$ival['lot_id_design'].'"/></td>



									<td>



										<select class="sel_design_for" style="width:80px;">



										<option value="1"



										"'.($ival['design_for']==1 ? $selected:'').'" >Male</option>



										<option value="2" "'.($ival['design_for']==2 ? $selected:'').'">Female</option>



										<option value="3" "'.($ival['design_for']==3 ? $selected:'').'">Unisex</option>



										</select>



									<input type="hidden" class="design_for" name="inward_item['.$ikey.'][design_for]" value="'.$ival['design_for'].'"></td>



									<td><input type="number" step="any" value="'.$ival['no_of_piece'].'" name="inward_item['.$ikey.'][pcs]" class="lot_pcs" style="width:60px;"></td>



									<td><div class="input-group"><input type="number" step="any" name="inward_item['.$ikey.'][gross_wt]" class="gross_wt" value="'.$ival['gross_wt'].'" style="width:80px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="gross_wt_uom" name="inward_item['.$ikey.'][gross_wt_uom]">"'.$gwt_uom.'"</select></span></div></td>



									<td><div class="input-group"><input type="number" step="any" name="inward_item['.$ikey.'][less_wt]" class="lot_lwt" value="'.$ival['less_wt'].'" style="width:80px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="less_wt_uom" name="inward_item['.$ikey.'][less_wt_uom]">"'.$lwt_uom.'"</select></span></div></td>



									<td><div class="input-group"><input type="number" step="any" name="inward_item['.$ikey.'][net_wt]" class="lot_nwt" value="'.$ival['net_wt'].'" style="width:80px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;font-size: 16px;border: none;"><select class="net_wt_uom" name="inward_item['.$ikey.'][net_wt_uom]">"'.$nwt_uom.'"</select></span></div></td>



									<td><input type="number" step="any" class="wastage_percentage" value="'.$ival['wastage_percentage'].'" name="inward_item['.$ikey.'][wastage_percentage]" style="width:60px;"></td>



									<td><div class="input-group"><input type="number" step="any" class="making_charge" name="inward_item['.$ikey.'][making_charge]" value="'.$ival['making_charge'].'" style="width:70px;"><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;border: none;font-size: 16px;"><select class="mc_type"  style="width:80px;" ><option value="1" "'.($ival['mc_type']==1? $selected :'').'">Per Gram</option><option value="2" "'.($ival['mc_type']==2? $selected :'').'">Per Piece</option></select></span><input type="hidden"  name="inward_item['.$ikey.'][id_mc_type]" class="id_mc_type" value="'.$ival['mc_type'].'"><input type="hidden"  name="inward_item['.$ikey.'][id_lot_inward_detail]" class="id_lot_inward_detail" value="'.$ival['id_lot_inward_detail'].'"></div></td>



									<td><input type="number" step="any" class="buy_rate" name="inward_item['.$ikey.'][buy_rate]" value="'.$ival['buy_rate'].'" style="width:80px;"><span class="buy_rt_type">'.($ival['sales_mode'] == 1 ? "Per Piece":($ival['sales_mode'] == 2 ?"Per Gram"  : "")).'</span></td>



									<td><input type="number" step="any" class="sell_rate" name="inward_item['.$ikey.'][sell_rate]" value="'.$ival['sell_rate'].'" style="width:80px;"><span class="sell_rt_type">'.($ival['sales_mode'] == 1 ? "Per Piece":($ival['sales_mode'] == 2 ?"Per Gram"  : "")).'</span></td>



										<td><div class="input-group"><input type="number" step="any" value="'.$ival['size'].'" class="size" name="inward_item['.$ikey.'][size]" style="width:70px;" ><span class="input-group-addon input-sm no-padding" style="background-color: transparent;height: 12px;border: none;"></span></div></td>







									<td><a href="#" onClick="show_stone_modal($(this).closest(\'tr\'),'.$ikey.');" class="btn btn-default btn-sm"><i class="fa fa-plus-circle"></i></a><input type="hidden" class="precious_stone" name="inward_item['.$ikey.'][precious_stone]" value="'.$ival['precious_stone'].'" /><input type="hidden" class="precious_stone_pcs" name="inward_item['.$ikey.'][precious_st_pcs]" value="'.$ival['precious_st_pcs'].'"/><input type="hidden" class="precious_stone_wt" name="inward_item['.$ikey.'][precious_st_wt]" value="'.$ival['precious_st_wt'].'"><input type="hidden" class="p_stn_certif_uploaded" name="inward_item['.$ikey.'][p_stn_certif_uploaded]" value="'.$ival['precious_st_certif'].'"><input type="hidden" class="precious_st_certif" name="inward_item['.$ikey.'][precious_st_certif]" value=""><input type="hidden" class="semi_precious_stn" name="inward_item['.$ikey.'][semi_precious_stn]" value="'.$ival['semi_precious_stone'].'"/><input type="hidden" class="semi_precious_st_pcs" name="inward_item['.$ikey.'][semi_precious_st_pcs]" value="'.$ival['semi_precious_st_pcs'].'"/><input type="hidden" class="semi_precious_st_wt" name="inward_item['.$ikey.'][semi_precious_st_wt]" value="'.$ival['semi_precious_st_wt'].'"><input type="hidden" class="sp_stn_certif_uploaded" name="inward_item['.$ikey.'][sp_stn_certif_uploaded]" value="'.$ival['semiprecious_st_certif'].'"><input type="hidden" class="semiprecious_st_certif" name="inward_item['.$ikey.'][semiprecious_st_certif]" value=""><input type="hidden" class="normal_stn" name="inward_item['.$ikey.'][normal_stn]" value="'.$ival['normal_stone'].'"/><input type="hidden" class="normal_st_pcs" name="inward_item['.$ikey.'][normal_st_pcs]" value="'.$ival['normal_st_pcs'].'"/><input type="hidden" class="normal_st_wt" name="inward_item['.$ikey.'][normal_st_wt]" value="'.$ival['normal_st_wt'].'"><input type="hidden" class="n_stn_certif_uploaded" name="inward_item['.$ikey.'][n_stn_certif_uploaded]" value="'.$ival['normal_st_certif'].'"><input type="hidden" class="normal_st_certif" name="inward_item['.$ikey.'][normal_st_certif]" value=""><input type="hidden" name="inward_item['.$ikey.'][nor_wt_uom]" class="nor_wt_uom"><input type="hidden" name="inward_item['.$ikey.'][semi_wt_uom]" class="semi_wt_uom" value="'.$ival['semi_precious_st_uom'].'"><input type="hidden" name="inward_item['.$ikey.'][pre_wt_uom]" class="pre_wt_uom" value="'.$ival['precious_st_uom'].'" ></td>



									<td><a href="#" onClick="remove_row($(this).closest(\'tr\'));" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>



							</tr>';



						}?>



						   <php }?> -->



						</tbody>

						<tfoot>
						 <tr style="font-weight:bold;">
						 <td colspan="4" style="text-align:center;">Total</td>
							<td class="lt_piece"></td>
							<td class="lt_gross_wt"></td>
							<td class="lt_less_wt"></td>
							<td class="lt_net_wt"></td>
							<td></td>
							<td></td>

							<td></td>
							<td class="lt_pur_rate"></td>
							<td></td>
							<td class="lt_tax_amount"></td>
							<td class="lt_tax_rate"></td>
							<td class="lt_amount"></td>
							<td></td>
					  </tr>

					   </tfoot>

					 </table>



				  </div>



				 </div>



			 </div>



			 <p class="help-block"></p>



			 <div class="row">



				 <div class="col-sm-7">



					 <legend>Upload Image</legend>



					 <p class="help-block">Note : Image size shouldn't exceed <b>1 MB</b>.   Upload <b>.jpg or .png </b>images only.</p>



					 <input type="file" name="lot_image" id="lot_images" multiple>



					 <input type="hidden" name="inward[image_name]" id="image_name" name="">



					 <div id="lot_img" class="col-md-12">



							 <?php



							 $lot_imgs = explode('#',$inward['lot_images']);



							 foreach($lot_imgs as $images){



								 if($images){?>



									 <div class="col-md-2">



											 <a value="<?php echo $images ?>" type="button" title="Remove Image" onclick="remove_img('<?php echo $images ?>','lot','lot_images','<?php echo $inward['lot_no'] ?>','<?php echo $inward['lot_images'] ?>');"><i class="fa fa-trash" ></i></a> <img  class="thumbnail" src="<?php echo base_url('assets/img/lot/'.$inward['lot_no'].'/'.$images); ?>" alt="Certificate Image" style="width:100px;height: 100px;">



									 </div>



							 <?php }}?>



					 </div>



					 <!--<button id="lot_img_upload" type="button" class="btn btn-success" style="display: none;">Upload</button>-->



				 </div>



				 <div class="col-sm-5">



					 <label>Narration</label>



					 <textarea class="form-control" id="narration" name="inward[narration]" <?php echo set_value('inward[narration]',$inward['narration']); ?> rows="5" cols="100"> </textarea>



				 </div>



			 </div>



			 <!--/Block 5-->

<!--

			 <p></p>



			 <div class="row">



				  <div class="col-xs-offset-5">



					<button type="button"  id="add_more_lot" class="btn btn-success">Add Lot</button>



				  </div>



			 </div>



			 <br/>



			 <br/>



			 <hr>







			 <legend>Preview</legend>



			  <div class="row">



				 <div class="col-sm-12">



					 <div class="table-responsive">



					 <table id="lt_preview" class="table table-bordered table-striped text-center">



						<thead>



						  <tr>



							<th width="10%">Lot Type</th>



							<th width="10%">Category</th>



							<th width="10%">Purity</th>



							<th width="10%">Stock Type</th>



							<th width="10%">Gold Smith</th>



							<th width="10%">Order No</th>



							<th width="5%">Pieces</th>



							<th width="10%">Gross Wgt</th>



							<th width="10%">Less Wgt</th>



							<th width="10%">Net Wgt</th>



							<th width="5%">Precious</th>



							<th width="5%">Semi precious</th>



							<th width="5%">Normal</th>



						  </tr>



						</thead>



						<tbody></tbody>



						<tfoot><tr><td>TOTAL</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>



					 </table>



				  </div>



				 </div>



			 </div>


-->
			 <!--/Block 6-->



		 </div>	<!--/ Col -->



		 </div>	 <!--/ row -->



	   <p class="help-block"> </p>



		 <div class="row">



		   <div class="box box-default"><br/>



			  <div class="col-xs-offset-5">



				<button type="button"  class="btn btn-primary" id="save_all">Save All</button>



				<button type="button" class="btn btn-default btn-cancel">Cancel</button>



			  </div> <br/>



			</div>



		  </div>



		</div>



	  <?php echo form_close();?>



		<div class="overlay" style="display:none">



		  <i class="fa fa-refresh fa-spin"></i>



		</div>



		 <!-- /form -->



	  </div>



	 </section>



	</div>



<!-- Stone Details -->



<div class="modal fade" id="stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



<div class="modal-dialog" style="width:60%;">



<div class="modal-content">



	<div class="modal-header">



		<h4 class="modal-title" id="myModalLabel">Stone Details</h4>



	</div>



	<div class="modal-body">



			 <div class="row">



				 <div class="col-xs-offset-1 col-sm-9">



				 <input type="hidden" id="row_id" name="row_id" value=""/>



				 <!--<legend class="sub-title">Stone Details</legend>-->



					 <div class="row"> <!-- Precious Stone -->



						<div class="col-sm-3">



							 <input type="checkbox" id="precious_stone" name="inward[precious_stone]" value="0"/>



							<label>Precious </label>



						 </div>



						 <div class="col-sm-9">



						 <div class="row">



							 <div class="col-sm-4">



								 <label>Stone Pcs</label>



								 <input class="form-control" id="precious_st_pcs" name="inward[precious_st_pcs]" type="number"  step=any  placeholder="No. of Pieces" disabled="true" />



							 </div>



							 <div class="col-sm-5">



								 <label>Stone Wgt</label>



								 <div class="form-group">



									 <div class="input-group">



										 <input class="form-control" id="precious_st_wt" name="inward[precious_st_wt]" type="number"  step=any  placeholder="Enter stone wgt"  disabled="true"/>



										 <span class="input-group-addon input-sm no-padding">



											 <select id="pre_wt_uom" class="uom" name="inward[pre_wt_uom]">



											 <?php foreach($uom as $u){



											echo "<option value=".$u['uom_id'].">".$u['code']."</option>";



											}?>



										 </select>



										 </span>



									</div>



								</div>



							 </div>



							 <div class="col-sm-3">



							 <label></label>



								 <input type="hidden" id="imgCount_p_stn" value="0" />



								   <button  type="button" id="uploadImg_p_stn" class="btn btn-success pull-right" disabled="true"><i class="fa fa-plus"></i> Certificate</button>



							 </div>



							 <div id="uploadedArea_p_stn" class="col-md-12" style="display: none;">



								   <p class="text-green">Uploaded Certificates :</p>



							   </div>



							   <div id="uploadArea_p_stn" class="col-md-12" style="display: none;">



								   <input type="file" name="pre_images" id="pre_images" class="pre_images" multiple="multiple">



							   </div>



						 </div>



						 </div>



					 </div>



					 <p class="help-block"></p>



					<!-- / Precious Stone -->



					 <div class="row">		<!-- Semi-Precious Stone-->



						<div class="col-sm-3">



							 <input type="checkbox" id="semi_precious_stn" name="inward[semi_precious_stone]" value="0"/>



							<label>Semi-Precious </label>



						 </div>



						 <div class="col-sm-9">



						 <div class="row">



							 <div class="col-sm-4">



								 <label>Stone Pcs</label>



								 <input class="form-control" id="semi_precious_st_pcs" name="inward[semi_precious_st_pcs]" type="number"  step=any  placeholder="No. of Pieces" disabled="true"/>



							 </div>



							 <div class="col-sm-5">



								 <label>Stone Wgt</label>



								 <div class="form-group">



									 <div class="input-group">



										 <input class="form-control" id="semi_precious_st_wt" name="inward[semi_precious_st_wt]" type="number"  step=any  placeholder="Enter stone wgt" disabled="true"/>



										 <span class="input-group-addon input-sm no-padding">



											 <select id="semi_wt_uom" class="uom" name="inward[semi_wt_uom]">



											 <?php foreach($uom as $u){



											echo "<option value=".$u['uom_id'].">".$u['code']."</option>";



											}?>



										 </select>



										 </span>



									</div>



								</div>



							 </div>



							 <div class="col-sm-3">



								 <label></label>



								 <input type="hidden" id="imgCount_sp_stn" value="0" />



								   <button type="button" id="uploadImg_sp_stn" class="btn btn-success pull-right" disabled="true"><i class="fa fa-plus"></i> Certificate</button>



							 </div>



							 <div id="uploadedArea_sp_stn" class="col-md-12" style="display: none;">



								   <p class="text-green">Uploaded Certificates :</p>



							   </div>



							 <div id="uploadArea_sp_stn" class="col-md-12" style="display: none;">



								   <input type="file" name="semi_pre_imgs" id="semi_pre_imgs" multiple="multiple">



							   </div>



						 </div>



						 </div>



					 </div>



					 <p class="help-block"></p>



					<!-- / Semi-Precious Stone -->



					 <div class="row">		<!-- Normal Stone -->



						<div class="col-sm-3">



							 <input type="checkbox" id="normal_stn" name="inward[normal_stone]" value="0"/>



							<label>Normal </label>



						 </div>



						 <div class="col-sm-9">



							 <div class="row">



								 <div class="col-sm-4">



									 <label>Stone Pcs</label>



									 <input class="form-control" id="normal_st_pcs" name="inward[normal_st_pcs]" type="number"  step=any  placeholder="No. of Pieces" disabled="true"/>



								 </div>



								 <div class="col-sm-5">



									 <label>Stone Wgt</label>



									 <div class="form-group">



										 <div class="input-group">



											 <input class="form-control" id="normal_st_wt" name="inward[normal_st_wt]" type="number"  step=any  placeholder="Enter stone wgt" disabled="true"/>



											 <span class="input-group-addon input-sm no-padding">



												 <select id="nor_wt_uom" class="uom" name="inward[nor_wt_uom]">



											 <?php foreach($uom as $u){



											echo "<option value=".$u['uom_id'].">".$u['code']."</option>";



											}?>



										 </select>



											 </span>



										</div>



									</div>



								 </div>



								 <div class="col-sm-3">



									 <label></label>



									 <input type="hidden" id="imgCount_n_stn" value="0" />



									   <button  type="button" id="uploadImg_n_stn" class="btn btn-success pull-right" disabled="true"><i class="fa fa-plus"></i> Certificate</button>



								 </div>



								 <div id="uploadedArea_n_stn" class="col-md-12" style="display: none;">



									   <p class="text-green">Uploaded Certificates :</p>



								   </div>



								 <div id="uploadArea_n_stn" class="col-md-12" style="display: none;">



									   <input type="file" name="norm_pre_imgs" id="norm_pre_imgs" multiple="multiple">



								   </div>



							 </div>



						 </div>



					 </div>



					 <p class="help-block"></p>



		   <!-- / Normal Stone-->



				 </div>



			 </div>



  </div>



  <div class="modal-footer">



	<button type="button" id="update_stone_details" class="btn btn-success">Save</button>



	<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>



  </div>



</div>



</div>



</div>



</div>



<!-- old metals -->



<!-- modal -->



<div class="modal fade" id="lot_inwards_detail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



<div class="modal-dialog">



<div class="modal-content">



<div class="modal-header">



<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>



<h4 class="modal-title" id="myModalLabel">Delete Product</h4>



</div>



<div class="modal-body">



	   <strong>Are you sure! You want to delete this Item?</strong></br></br>



		<div class="alert alert-success" role="alert" id="successMsg" style="display: none;">



		</div>



	  <div class="alert alert-danger" role="alert" id="errorMsg" style="display: none;">



		</div>



</div>



<div class="modal-footer">



  <a href="#" class="btn btn-danger btn-confirm" id="delete_confirm">Delete</a>



<button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>



</div>



</div>



</div>



</div>



<!-- / modal -->



<!-- modal -->



<div class="modal fade" id="Userconfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



<div class="modal-dialog">



<div class="modal-content">



<div class="modal-header">



<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>



<h4 class="modal-title" id="myModalLabel">Category Alert</h4>



</div>



<div class="modal-body">



	 <strong>This Category Will Change in Your Added Items..</strong>



</div>



<div class="modal-footer">



  <a href="#" class="btn btn-danger btn-confirm" id="proceed">Proceed</a>



  <a href="#" class="btn btn-danger btn-confirm" id="cancel">Cancel</a>



</div>



</div>



</div>



</div>



<!-- / modal -->



<script type="text/javascript">



<?php if($this->uri->segment(3) == 'edit'){?>



var lot_preview_item=<?php echo json_encode($inward_details);?>



<?php }?>



</script>




<div class="modal fade" id="cus_stoneModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog" style="width:95%;">

<div class="modal-content">

	<div class="modal-header">

		<h4 class="modal-title" id="myModalLabel">Add Stone</h4>

	</div>

	<div class="modal-body">

		<!--<div class="row">-->

<!--    			<div class="col pull-right">-->

<!--    			    <button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>-->

<!--    			</div>-->

<!--			</div>-->

		<div class="row">

			<input type="hidden" id="stone_active_row" value="0">

			<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">

			<thead>

			<tr>

			<th>LWT</th>

			<th>Type</th>

			<th>Name</th>

			<th>Code</th>

			<th>Pcs</th>

			<th>Wt</th>

			<th>Cal.Type</th>

			<th>Cut</th>

			<th>Color</th>

			<th>Clarity</th>

			<th>Shape</th>

			<th>Rate</th>

			<th>Amount</th>

			<th>Action</th>

			</tr>

			</thead>

			<tbody>

			</tbody>

			<tfoot>

			<tr style="font-weight:bold;font-size:15px">
				<td>Total:</td>
				<td></td>
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