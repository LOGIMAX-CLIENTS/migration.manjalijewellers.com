<!-- Content Wrapper. Contains page content -->

<style>

	.remove-btn{

		margin-top: -168px;

	    margin-left: -38px;

	    background-color: #e51712 !important;

	    border: none;

	    color: white !important;

	}

	.stickyBlk {

	    margin: 0 auto;

	    top: 0;

	    width: 100%;

	    z-index: 999;

	    background: #fff;

	}

	.tag-form .form-group{

	    margin-bottom: 1px;

	    display: block;

        width: 100%;

        /*height: 25px;*/

        padding: 2px 6px;

	}

	.tag-form .input-group {

	    margin-bottom: 1px;

        width: 100%;

        /*height: 25px;*/

	}

	.tag-form input, .tag-form .input-group-addon{

	    /*height : 25px;*/

	}

	.tag-form select{

	    /*height : 25px;*/

	    padding:0px 0px 0px 12px;

	}

	.add_attributes, .add_charges, #create_other_metal_item_details {

		cursor: pointer;

		color: blue;

	}

	.remove_tag_attribute, .remove_charge_item_details, .remove_other_metal{

		margin-left: 5px;

	}

	#update_attribute_detail tr td .select2-container {

		width: 100% !important;

	}

	.huid-group, .certs-group {

		text-align: center;

	}

	.huids {

		width: 49%;

		display: inline-block !important;

	}

	.certs {

		width: 44%;

		display: inline-block !important;

	}

	.cert_rm_icon {

		width: 9%;

		display: inline-block !important;

		height: 25px;

		padding: 6px;

		margin-bottom: 5px;

		cursor: pointer;

	}

	#cert_img_preview {

		width: 75%;
		height: 75%;

	}

	.cert_img_container {

		display: none;
		padding-top: 25px;
		text-align: center;

	}

	.cert_img_preview_container {

		padding-top: 10px;

	}

	.multimetal {

		display: none;

	}

	.tag_reload_div {

		color: white;
		background: red;
		font-size: 17px;
		font-weight: bold;
		padding: 10px;
		margin-bottom: 10px;
		border-radius: 5px;
		display: none;

	}

	#tag_gwt, #gwt_uom_id {

		display: inline-block;

	}

	#tag_gwt {

		width: 100%;

	}

	#gwt_uom_id {

		width: 25%;

		display: none;

	}

	.stone_calc {
		display: none;
	}



	*[tabindex]:focus {

		outline: 1px black solid;

	}

	#tag_wast_perc , #tag_wast_value {
		display: inline-block;
	}

	#tag_wast_perc {
		width: 40%;
	}

	#tag_wast_value {
		width: 40%;
	}
	#tag_wast_perc , #tag_wast_value, #tag_pur_wast_perc , #tag_pur_wast_value {
		display: inline-block;
	}
	#tag_wast_perc, #tag_pur_wast_perc {
		width: 40%;
	}
	#tag_wast_value, #tag_pur_wast_value {
		width: 40%;
	}



</style>

<?php if(isset($tag_prints) && trim($tag_prints) != '') { ?>

  	<script type="text/javascript">

  	 	window.open('<?php echo base_url() ?>index.php/admin_ret_tagging/tagging/generate_barcode?tag=<?php echo $tag_prints ?>', '_blank');

  	</script>

<?php } ?>

<div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <!--<section class="content-header">

          <h1>

        	Tagging

            <small>Tag</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Tagging</a></li>

            <li class="active">Tag</li>

          </ol>

    </section>-->

    <!-- Main content -->

    <section class="content product">

          <!-- Default box -->

          <div class="box box-primary">

            <!--<div class="box-header with-border">

              <h3 class="box-title">Add Tagging</h3>

              <div class="box-tools pull-right">

                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

              </div>

            </div>-->

            <div class="box-body">

			<div class="tag_reload_div"> Tag completed. Please <a href="">click here</a> to reload </div>

             <!-- form container -->

              <div class="row">

	             <!-- form -->

				<?php echo form_open_multipart(( $tagging['tag_id'] != NULL && $tagging['tag_id'] > 0 ? 'admin_ret_tagging/tagging/update/'.$tagging['tag_id']:'admin_ret_tagging/tagging/save'),array('id'=>'tag_form')); ?>

				<div class="col-sm-12">

					<!-- Lot Details Start Here -->

					<div class="row">

			    		<div class="col-sm-4">

			    			<?php if($this->session->userdata('branch_settings')==1){?>

							 	<div class="row">

						    		<div class="col-sm-3">

						    			<label>Branch </label>

							 		</div>

							 			<div class="col-sm-8">

							 			<div class="form-group">

							 				<?php if($tagging['lot_recv_branch']==1){?>
                                                    <?php if($this->session->userdata('id_branch')==0){?>
                                                        <select id="branch_select" class="form-control ret_branch" required></select>
                                                        <input id="id_branch" name="lt_item[id_branch]" type="hidden" value="<?php echo set_value('tagging[id_branch]',$tagging['id_branch']); ?>" />
                                                    <?php } else{?>
                                                        <select id="branch_select" class="form-control ret_branch" required disabled></select>
                                                        <input id="id_branch" name="lt_item[id_branch]" type="hidden" value="<?php echo set_value('tagging[id_branch]',$this->session->userdata('id_branch')); ?>" />
								 			        <?php }?>


								 			<?php }else{?>

								 				<select id="branch_select" class="form-control ret_branch" required disabled></select>

								 				<input id="id_branch" name="lt_item[id_branch]" type="hidden"  value="<?php echo set_value('tagging[id_branch]',$tagging['id_branch']); ?>" />

								 			<?php }?>

										</div>

							 		</div>

							 	</div>

							 <?php }?>

			    			<div class="row">

					    		<div class="col-sm-3">

					    			<label>Lot No </label>

						 		</div>

						 		<div class="col-sm-8">

						 			<div class="form-group">

							 			<select id="tag_lot_received_id" tabindex="1" class="form-control" required autofocus></select>

										<input id="tag_lot_id" name="tagging[tag_lot_id]" type="hidden" value="<?php echo set_value('tagging[tag_lot_id]',$tagging['tag_lot_id']); ?>" />

										<!--<input id="tag_id" name="tagging[tag_id]" type="hidden" value="<?php echo set_value('tagging[tag_id]',$tagging['tag_id']); ?>" />-->

										<input id="weight_per" name="tagging[weight_per]" type="hidden" value="<?php echo set_value('tagging[weight_per]',$tagging['weight_per']); ?>" />

									    <input id="allow_tag_pcs" name="tagging[allow_tag_pcs]" type="hidden" value="<?php echo set_value('tagging[allow_tag_pcs]',$tagging['allow_tag_pcs']); ?>" />

									    <input type="hidden" id="is_section_req" value="<?php echo $tagging['is_section_req']?>"/>
									    <input type="hidden" id="sell_mc_va_edit_access" value="<?php echo $tagging['sell_mc_va_edit_access']?>"/>
									    <input type="hidden" id="pur_mc_va_edit_access" value="<?php echo $tagging['pur_mc_va_edit_access']?>"/>

										<input type="hidden" id="is_lot_split" value="">

										<input type="hidden" id="lot_pur_stone_details" value="">

										<input type="hidden" id="is_va_mc_based_on_branch" name="is_va_mc_based_on_branch"  value="<?php echo set_value('is_va_mc_based_on_branch', $tagging['is_va_mc_based_on_branch']); ?>" >

									</div>

						 		</div>

						 	</div>

							<div class="row emp">

								<div class="col-sm-3">

									<label>Employee<span class="error">*</span></label>

								</div>

								<div class="col-sm-8">

									<div class="form-group">

										<input type="hidden" id="id_employee">

										<select class="form-control" id="emp_select" tabindex="2"></select>

									</div>

								</div>

							</div>


						 	<div class="row">

					    		<div class="col-sm-3">

					    			<label>Product </label>

						 		</div>

						 		<div class="col-sm-8">

						 			<div class="form-group">

						 			    <select class="form-control" tabindex="2" id="tag_lt_prod"></select>

										<input id="tag_lt_prodId" name="tagging[tag_lt_prodId]" type="hidden" value="" />

										<span id="productAlert" class="error"></span>

									</div>

						 		</div>

						 	</div>

						 	<!--<div class="row">

					    		<div class="col-sm-3">

					    			<label>Design </label>

						 		</div>

						 		<div class="col-sm-8">

						 			<div class="form-group">

						 			    <select class="form-control" id="tag_lt_design"></select>

										<input id="tag_lt_designId" name="tagging[tag_lt_prodId]" type="hidden" value="" />

										<span id="designAlert" class="error"></span>

									</div>

						 		</div>

						 	</div>-->

						 	<?php if($this->session->userdata('branch_settings')==1){?>

							 	<div class="row">

						    		<div class="col-sm-3">

						    			<label>ToBranch </label>

							 		</div>

							 			<div class="col-sm-8">

							 			<div class="form-group">

							 				<select id="current_branch" class="form-control ret_branch" required disabled></select>
								 			<input id="current_branch_id" name="lt_item[to_branch]" type="hidden" value="<?php echo set_value('tagging[id_branch]',$tagging['id_branch']); ?>" />

										</div>

							 		</div>

							 	</div>

							 <?php }?>

							 <?php
								if($tagging['is_va_mc_based_on_branch']==1)
								{?>
									<div class="row">
										<div class="col-sm-3">
											<label>Tag Branch<span class="error">*</span></label>
										</div>
											<div class="col-sm-8">
												<div class="form-group">
													<select class="form-control ret_branch" id="tag_branch_va_mc"  tabindex="3"></select>
												</div>
											</div>
										</div>
									<?php }
                            	?>

								<div class="row" <?php echo ($tagging['is_section_req']==1 ? 'style="display:block";' : 'style="display:none";') ?>>
									<div class="col-sm-3">
										<label>Section<span class="error">*</span></label>
									</div>
									<div class="col-sm-8">
										<div class="form-group">
											<input type="hidden" id="id_section">
											<select class="form-control" id="section_select" tabindex="3"></select>
										</div>
									</div>
								</div>

								<div class="row">
				 		        	    <div class="col-sm-3">
							 				<label>Order No</label>
							 			</div>
							 			<div class="col-sm-7">
							 				<div class="form-group">
						 		        		<input id="tag_order_no" class="form-control custom-inp" type="text" step="any" readonly="">
											</div>
							 			</div>
				 		        	</div>

						 	<!--<div class="row">

					    		<div class="col-sm-3">

					    			<label>TagDate </label>

						 		</div>

						 		<div class="col-sm-8">

						 			<div class="form-group">

							 			<div class="input-group" >

											<input class="form-control" id="tag_datetime" data-date-format="dd-mm-yyyy" name="lt_item[tag_datetime]" type="text" required="true" placeholder="Lot Date" value="<?php echo set_value('tagging[tag_datetime]',$tagging['tag_datetime']); ?>" readonly />

										</div>

									</div>

						 		</div>

						 	</div>-->

				 			<!--<div class="row">

					    		<div class="col-sm-3">

					    			<label>Employee </label>

						 		</div>

						 		<div class="col-sm-8">

						 			<div class="form-group">

							 			<select id="emp_select" class="form-control" required></select>

										<input id="id_employee" name="tagging[created_by]" type="hidden" value="<?php echo set_value('tagging[created_by]',$tagging['created_by']); ?>" />

									</div>

						 		</div>

						 	</div>-->

						 	<div class="lot-table">

    						 	<div class="table-responsive">

    								<table id="lt-det" class="table table-bordered text-center">

    									<thead>

        									<tr>

												<th width="10%"></th>

												<th width="10%"><span class="lt_desc_tab">Pieces</span></th>

												<th width="10%"><span class="lt_desc_tab">Grs Wt</span></th>

												<th width="10%"><span class="lt_desc_tab">Net Wt</span></th>

												<th width="10%"><span class="lt_desc_tab">Dia Wt</span></th>

												<th width="10%"><span class="lt_desc_tab">Stone Wt</span></th>

        									</tr>

    									</thead>

    									<tbody>

    									    <tr>

												<td><b>Lot</b></td>

												<td><span class="lt_disp_val disp_lot_pcs"></span></td>

												<td><span class="lt_disp_val disp_lot_wt"></span></td>

												<td><span class="lt_disp_val disp_lot_nwt"></span></td>

												<td><span class="lt_disp_val disp_lot_dwt"></span></td>

												<td><span class="lt_disp_val disp_lot_stwt"></span></td>

    									    </tr>

    									    <tr>

												<td><b>Completed</b></td>

												<td><span class="lt_disp_val disp_lot_tag_pcs"></span></td>

												<td><span class="lt_disp_val disp_lot_tag_wt"></span></td>

												<td><span class="lt_disp_val disp_lot_tag_nwt"></span></td>

												<td><span class="lt_disp_val disp_lot_tag_dwt"></span></td>

												<td><span class="lt_disp_val disp_lot_tag_stwt"></span></td>

    									    </tr>

											<tr>

												<td><b>Balance</b></td>

												<td><span class="lt_disp_val disp_lot_bal_pcs"></span></td>

												<td><span class="lt_disp_val disp_lot_bal_wt"></span></td>

												<td><span class="lt_disp_val disp_lot_bal_nwt"></span></td>

												<td><span class="lt_disp_val disp_lot_bal_dwt"></span></td>

												<td><span class="lt_disp_val disp_lot_bal_stwt"></span></td>

											</tr>


    									</tbody>

    								</table>

    							</div>

    							<input type="hidden" id="lt_id_tax_group" name="lt_id_tax_group"/>

								<input type="hidden" id="tax_percentage" name="">

								<input type="hidden" id="tgi_calculation" name="">

								<input type="hidden" id="metal_rate" name="">

								<input type="hidden" id="silverrate_1gm" name="">

								<input type="hidden" id="platinum_1g" name="">

								<input type="hidden" id="purity" name="lt_item[purity]">

								<input class="form-control lot_bal_wt" id="lot_bal_wt" type="hidden" step=any  value="0" readonly />

                                <input class="form-control lot_bal_prec_wt" id="lot_bal_prec_wt" type="hidden" step=any  value="0" readonly />

                                <input class="form-control lot_bal_prec_pcs" id="lot_bal_prec_pcs" type="hidden" step=any  value="0" readonly />

                                <input class="form-control lot_bal_semi_pre_wt" id="lot_bal_semi_pre_wt" type="hidden" step=any  value="0" readonly />

                                <input class="form-control lot_bal_normal_wt" id="lot_bal_normal_wt" type="hidden" step=any  value="0" readonly />

                                <input class="form-control lot_bal_normal_pcs" id="lot_bal_normal_pcs" type="hidden" step=any  value="0" readonly />

                                <input class="form-control issuspensestock" id="is_suspense_stock" type="hidden" step=any  value="0" readonly />

								<input class="form-control lot_bal_stone_wt" id="lot_bal_stone_wt" type="hidden" step=any  value="0" readonly />

								<input class="form-control lot_bal_dia_wt" id="lot_bal_dia_wt" type="hidden" step=any  value="0" readonly />

								<input type="hidden" id="tag_cat_type" />

								<input type="hidden" id="tag_product_stone_type" />

							</div>

								<div class="row">

								    <div class="col-sm-3">

						 		            <b><span class="lt_desc_tab">Metal</span></b>

						 		   </div>

						 		   <div class="col-sm-8">

					 		            <!--<span class="lt_disp_val lot_metal"></span>-->

					 		            <span id="lt_metal" class="">-</span>

					 		        </div>

								</div>

								<div class="row">

								    <div class="col-sm-3">

						 		            <b><span class="lt_desc_tab">Category</span></b>

						 		   </div>

						 		   <div class="col-sm-8">

					 		            <!--<span class="lt_disp_val lot_cat"></span>-->

					 		            <span id="lt_category" class="">-</span>

					 		        </div>

								</div>

								<div class="row">

								    <div class="col-sm-3">

						 		            <b><span class="lt_desc_tab">Receipt On</span></b>

						 		   </div>

						 		   <div class="col-sm-8">

					 		            <!--<span class="lt_disp_val lot_receipton"></span>-->

					 		            <span id="lt_date" class="">-</span>

					 		        </div>

								</div>

								<div class="row">

								    <div class="col-sm-3">

						 		            <b><span class="lt_desc_tab">Supplier</span></b>

						 		   </div>

						 		   <div class="col-sm-8">

					 		            <!--<span class="lt_disp_val lot_receipton"></span>-->

					 		            <span id="lt_karigar_name" class="">-</span>

					 		        </div>

								</div>


								<div class="row cert_img_container">

								    <div class="col-sm-12">

						 		            <b><span class="lt_desc_tab">Certification Image</span></b>

						 		   </div>

						 		   <div class="col-sm-12 cert_img_preview_container">

					 		            <!--<span class="lt_disp_val lot_receipton"></span>-->

					 		            <img src="" id="cert_img_preview" />

					 		        </div>

								</div>

				 		</div>

				 		<div class="col-sm-8">

        				 	<div class="row" >

        					 	<div class="col-sm-7 tag-form">

						 			<div class="row">

							 			<input type="hidden" class="form-control" id="tag_id_lot_inward_detail"/>

							 			<input type="hidden" class="form-control" id="calculation_based_on"/>

				 		        		<input type="hidden" class="form-control" id="id_metal"/>

				 		        		<input type="hidden" class="form-control" id="tax_group_id"/>

				 		        		<input type="hidden" class="form-control" id="tag_lot_no"/>

				 		        		<input type="hidden" class="form-control" id="tag_sales_mode"/>

				 		        		<input type="hidden" class="form-control" id="id_purity"/>

				 		        		<input type="hidden" class="form-control" id="tag_tax_type"/>

				 		        		<input type="hidden" class="form-control" id="tag_product_short_code"/>

										 <input type="hidden" class="form-control" id="margin_mrp"/>

				 		        	</div>

				 		        	<div class="row">

                                        <div class="col-sm-3">

                                              <label>No.of items <span class="error">*</span></label>

                                        </div>

                                        <div class="col-sm-7">

                                           <div class="form-group">

                                           <input id="bulk_tag" class="form-control custom-inp" type="number" step="any" value="1" tabindex="5"/>

                                           <!-- <input id="tag_act_blc_pcs" type="hidden" />

                                           <input id="tag_blc_pcs" type="hidden" />

                                           <div style="display:none">

                                           <span id="tag_blc_pcs_disp" class="text-red"></span> -->

                                            </div>


                                            </div>

                                        </div>


				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Design <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<select class="form-control" id="des_select" tabindex="3"></select>

						 		        		<input type="hidden" id="id_design">

						 		        		<input type="hidden" id="tag_id" value="">

						 		        		<input type="hidden" id="tag_saved" value="">

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Sub Design <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

							 				    <input type="hidden" id="id_sub_design">

						 		        		<select class="form-control" id="sub_des_select" tabindex="3"></select>

											</div>

							 			</div>

				 		        	</div>

									<div class="row quality">

										<div class="col-sm-3">

											<label>Quality Code <span class="error">*</span></label>

										</div>

										<div class="col-sm-7">

											<div class="form-group">

												<input type="hidden" id="id_quality">

												<select class="form-control" id="quality_code" tabindex="4"></select>

											</div>

										</div>

									</div>



				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Pieces <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="tag_pcs" class="form-control custom-inp" type="number" step="any" value="1" tabindex="5"/>

						 		        		<input id="tag_act_blc_pcs" type="hidden" />

						 		        		<input id="tag_blc_pcs" type="hidden" />

												<input id="product_pcs" type="hidden" />

						 		        		<div style="display:none"><span id="tag_blc_pcs_disp" class="text-red"></span></div>

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Gross Wgt <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="tag_gwt" class="form-control custom-inp" type="number" step="any" tabindex="6"/>

												<select id="gwt_uom_id" class="form-control"></select>

						 		        		<input id="tag_act_gross_blc" type="hidden" />

						 		        		<input id="tag_blc_gross" type="hidden" />

						 		        		<div style="display:none"><span id="tag_blc_gross_disp" class="text-red"></span></div>

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Less Wgt</label>

							 			</div>

							 			<div class="col-sm-7">

							 			    <div class="form-group">

												<div class="input-group ">

													<input id="tag_lwt" class="form-control custom-inp add_tag_lwt" type="number" step="any" readonly tabindex="7"/>

													<span class="input-group-addon input-sm add_tag_lwt" tabindex="8">+</span>

													<input type="hidden" id="lot_pur_stone_details" value="">

												</div>

											</div>

							 			</div>

				 		        	</div>

									<div class="row multimetal">

				 		        	    <div class="col-sm-3">

							 				<label>Other Metals</label>

							 			</div>

							 			<div class="col-sm-7">

							 			    <div class="form-group">

												<div class="input-group ">

													<input id="other_metal_amount" type="hidden" />

													<input type="number" step="any" readonly id="other_metal_wt" class="form-control custom-inp add_other_metals" />

													<span class="input-group-addon input-sm add_other_metals">+</span>

													<input type="hidden" id="other_metal_details">

												</div>

											</div>

							 			</div>

									</div>

				 		            <div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Net Wgt</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="tag_nwt" class="form-control custom-inp" type="number" step="any" readonly/>

											</div>

							 			</div>

				 		        	</div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label>Purchase Touch</label>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="form-group">
                                                <input id="purchase_touch" class="form-control custom-inp" type="text" maxlength="6" autocomplete="off" tabindex="8" value="<?php echo (isset($tagging->purchase_touch)) ? $tagging->purchase_touch : ''; ?>" />
                                            </div>
                                        </div>
                                    </div>
									<div class="row">
				 		        	    <div class="col-sm-3">
							 				<label>Pur Wast %</label>
							 			</div>
							 			<div class="col-sm-7">
							 				<div class="form-group">
						 		        		<input id="tag_pur_wast_perc" class="form-control custom-inp" type="number" step="any" value="0" tabindex="9" />%
												&nbsp;&nbsp;&nbsp;
												<input id="tag_pur_wast_value" class="form-control custom-inp" type="number" step="any" value="0" tabindex="9" /> Wt
											</div>
							 			</div>
				 		        	</div>
				 		        	<div class="row">
				 		        	    <div class="col-sm-3">
							 				<label>Pur MC Type</label>
							 			</div>
							 			<div class="col-sm-7">
							 				<div class="form-group">
						 		        		<select class="form-control" id="tag_pur_id_mc_type" tabindex="10">
								 					<option value="">--N/A--</option>
			                               	 		<option value="1">Per Gram</option>
			                               	 		<option value="2">Per Piece</option>
			                               	 		<option value="3">% On Price</option>
			                               	 	</select>
											</div>
							 			</div>
				 		        	</div>
				 		        	<div class="row">
				 		        	    <div class="col-sm-3">
							 				<label>Pur MC <span class="error">*</span></label>
							 			</div>
							 			<div class="col-sm-7">
							 				<div class="form-group">
						 		        		<input id="tag_pur_mc_value" class="form-control custom-inp" type="number" step="any" autocomplete="off" value="0" tabindex="11">
											</div>
							 			</div>
				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Wastage %</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="tag_wast_perc" class="form-control custom-inp" type="number" step="any" value="0" tabindex="9" />%
												&nbsp;&nbsp;&nbsp;
												<input id="tag_wast_value" class="form-control custom-inp" type="number" step="any" value="0" tabindex="9" /> Wt

												 <input type="hidden" id="min_va" />

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>MC Type</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<select class="form-control" id="tag_id_mc_type" tabindex="10">

								 					<option value="">--N/A--</option>

			                               	 		<option value="1">Per Piece</option>

			                               	 		<option value="2">Per Gram</option>

			                               	 		<option value="3">% On Price</option>

			                               	 	</select>

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>MC <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="tag_mc_value" class="form-control custom-inp" type="number" step="any" autocomplete="off" value="0" tabindex="10">

												<input type="hidden" id="min_mc" />

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Size</label>
							 				<input type="hidden" id="has_size" value="" />
                                            <input type="hidden" id="id_size" >

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<select class="form-control no-padding" id="tag_size" tabindex="11"></select>

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row" style="display: none;">

				 		        	    <div class="col-sm-3">

							 				<label>Design For <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<select class="form-control" id="tag_design_for">

						 		        			<option value="2">Female</option>

						 		        			<option value="1">Male</option>

						 		        			<option value="3">Unisex</option>

						 		        		</select>

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row tag_calc">

				 		        	    <div class="col-sm-3">

							 				<label>Calc Type <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<select class="form-control" id="tag_calculation_based_on" >
												 <!-- disabled -->

						 		        			<option value="">--Choose--</option>

						 		        			<option value="0">Mc & Wast On Gross</option>

						 		        			<option value="1">Mc & Wast On Net</option>

						 		        			<option value="2">Mc on Gross,Wast On Net</option>

						 		        			<option value="3">MRP</option>

						 		        			<option value="4">Fixed Rate based on Weight</option>

						 		        		</select>

											</div>

							 			</div>

				 		        	</div>

									<div class="row stone_calc">

				 		        	    <div class="col-sm-3">

							 				<label>Calc Type <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<select class="form-control" id="stone_calculation_based_on" tabindex="12">

						 		        			<option value="">--Choose--</option>

						 		        			<option value="1">Calc based on Weight</option>

						 		        			<option value="2">Calc based on Pcs</option>

							 					</select>

											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>HUID</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group huid-group">

						 		        		<input id="tag_huid" class="huids form-control custom-inp" type="text" step="any" value="" tabindex="12" />

												<input id="tag_huid2" class="huids form-control custom-inp" type="text" step="any" value="" tabindex="13" />

											</div>

							 			</div>

				 		        	</div>


				 		        	<div class="row">

                                            <div class="col-sm-3">

                                                <label>HUID</label>

                                            </div>

                                            <div class="col-sm-7">

                                                <div class="form-group">

                                                    <div class="input-group ">

                                                        <input id="tag_huid_model"
                                                            class="form-control custom-inp display_huid_modal"
                                                            type="number" step="any" readonly />
                                                        <span
                                                            class="input-group-addon input-sm display_huid_modal">+</span>
                                                        <input type="hidden" id="other_huid_details">

                                                    </div>
                                                </div>

                                            </div>

                                        </div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Rate / MRP <span class="error">*</span></label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="tag_sell_rate" class="form-control" type="number" step="any"  tabindex="14"/>

											</div>

							 			</div>

				 		        	</div>

									 <!--	Charges	 -->

							 		<input type="hidden" id="tag_stone_details">

							 		<input type="hidden" id="tag_charge_amt">

									<div class="row other_charges" style="display: none;">

				 		        	    <div class="col-sm-3">

							 				<label>Charges</label>

							 			</div>

							 			<div class="col-sm-7">

							 			    <div class="form-group">

												<div class="input-group ">

													<input id="tag_charge" class="form-control custom-inp add_tag_charge" type="number" step="any" readonly/>

													<span class="input-group-addon input-sm add_tag_charge">+</span>

												</div>

											</div>

							 				<!--<button type="button" class="btn btn-success btn-sm" data-toggle="tooltip" title="Add Charges" onclick="add_charge()"><i class="fa fa-plus"></i> Add</button>-->

							 			</div>

				 		        	</div>

									<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Attributes</label>

							 			</div>

							 			<div class="col-sm-7">

							 			    <div class="form-group">

												<div class="input-group ">

													<input id="tag_attribute" class="form-control custom-inp display_attribute_modal" type="number" step="any" readonly tabindex="15"/>
													<span class="input-group-addon input-sm display_attribute_modal">+</span>

												</div>
											</div>

							 			</div>

				 		        	</div>

				 		        	<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Sale Value</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="tag_sale_value" class="form-control" type="number" step="any" readonly />

											</div>

							 			</div>

				 		        	</div>

									<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Certification No</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group certs-group">

						 		        		<input class="form-control certs" id="cert_no" type="text" tabindex="16" />

												<input class="form-control certs" id="cert_img" type="file" tabindex="17" onchange="validate_image(event, this, 'cert_img_preview')" />

												<span class="input-group-addon input-sm cert_rm_icon" onclick="remove_cert_image()">&#215;</span>

												<input type="hidden" id="cert_img_base64" value="" />

											</div>

							 			</div>

				 		        	</div>

									<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Manuf Code</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="manufacture_code" class="form-control" type="text" tabindex="18" />

											</div>

							 			</div>

				 		        	</div>

									<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Style Code</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="style_code" class="form-control" type="text" tabindex="19" />

											</div>

							 			</div>

				 		        	</div>

									 <div class="row">

				`						<div class="col-sm-3">

											<label>Old Tag Id</label>

										</div>

										<div class="col-sm-7">

											<div class="form-group">

												<input id="remarks" class="form-control" type="text" tabindex="20" />

										</div>

										</div>

										</div>


										<div class="row">

										<div class="col-sm-3">

										<label>Narration</label>

										</div>

									  <div class="col-sm-7">

										<div class="form-group">

										<textarea class="form-control"  id="narration" rows="2" cols="100"> </textarea>

										</div>

										</div>

										</div>


									<div class="row">

				 		        	    <div class="col-sm-3">

							 				<label>Purchase Cost</label>

							 			</div>

							 			<div class="col-sm-7">

							 				<div class="form-group">

						 		        		<input id="tag_purchase_cost" class="form-control" type="number" tabindex="21" />

											</div>

							 			</div>

				 		        	</div>

									<div class="row">

										<div class="col-sm-3">

											<label>Is New Arrival</label>

										</div>

										<div class="col-sm-7">

											<div class="form-group">

												<select class="form-control" id="is_new_arrival" tabindex="21">

													<option value="1">Yes</option>

													<option value="0" selected>No</option>

												</select>

											</div>

										</div>

									</div>

				 		        	<div class="row" style="display:none;">

										<div class="col-sm-3">

											<label>Branch Division</label>

										</div>

										<div class="col-sm-7">

											<div class="form-group">

												<select class="form-control" id="tag_product_division" tabindex="22">

						 		        			<option value="">--Choose--</option>

													<?php
														foreach($product_division as $pd) { ?>

														<option value="<?php echo $pd['id_pro_division'] ?>" ><?php echo $pd['div_value']; ?></option>

													<?php } ?>

						 		        		</select>

										</div>

										</div>

									</div>

				 		        	<div class="row">
				 		        	    <div class="col-sm-3">
							 				<label>Choose Image</label>
							 			</div>
							 			<div class="col-sm-7">
							 				<div class="form-group">
						 		        		 <input type="hidden" id="custom_active_id">
						 		        		    <a href="#" onclick="update_image_upload();" tabindex="23" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a>
						 		        		    <div id="tag_img" data-img='[]' ></div>
                                                    <input type="hidden" id="tag_img_url">
                                                    <input type="hidden" class="form-control" id="tag_img_copy"/>
                                                    <input type="hidden" class="form-control" id="tag_img_default"/>
                                                    <input type="hidden" id="tag_images">
											</div>
							 			</div>
				 		        	</div>

				 		        	<!--<div class="row">

				 		        	    <div class="col-sm-3">

							 			</div>

										<div class="col-sm-8">

											<div class="table-responsive">

											<table id="table_charges" class="table table-bordered text-center">

												<thead>

												<tr>

													<th width="10%">Charges Type  <span class="error">*</span></th>

													<th width="10%">Value  <span class="error">*</span></th>

													<th width="10%">Action</th>

												</tr>

												</thead>

												<tbody>

												</tbody>

											</table>

										</div>

										</div>

									</div>-->

									<br/>

						 			<div class="row">

							 			<div class="col-sm-12" align="center">



							 			        <iframe  id="iFramePdf" name="iFramePdf" src="" style="display:none;"></iframe>



							 				<button type="button" id="addTagToPreview" class="btn btn-success btn-flat" tabindex="24">Add</button>

							 				<button type="button"  id="addTagToPreviewAndCopy" class="btn btn-info btn-flat">Add & Copy</button>

							 				<button type="button" style="display: none" id="updateTagInPreview" class="btn btn-primary btn-flat" tabindex="21">Update</button>

							 				<button type="button" id="reset_tag_form" class="btn btn-default btn-flat" tabindex="22" style="display: none;" >Reset</button>

							 			</div>

							 	    </div>

        						</div>

								<div class="col-sm-5">

									<div class="box box-info">

										<div class="box-body">

											<p>OTHER METALS</p>

											<table id="othermetals-det" class="table table-bordered text-center">

												<thead>

													<tr>

														<th><span class="lt_desc_tab">Metal</span></th>

														<th><span class="lt_desc_tab">Purity</span></th>

														<th><span class="lt_desc_tab">NWt</span></th>

														<th><span class="lt_desc_tab">VA(%)</span></th>

														<th><span class="lt_desc_tab">MC</span></th>

														<th><span class="lt_desc_tab">Amount</span></th>

													</tr>

												</thead>

												<tbody>

												</tbody>

											</table>

										</div>

									</div>

                                    <div class="box box-info">

                                        <!--<div class="box-header with-border">

                                            <h3 class="box-title">Stone Details</h3>

                                        </div>-->

                                        <div class="box-body">

                                            <p>STONE DETAILS</p>

                                            <table id="stone-det" class="table table-bordered text-center">

            									<thead>

                									<tr>

                										<th width="10%"><span class="lt_desc_tab">Stone</span></th>

                										<th width="10%"><span class="lt_desc_tab">Pieces</span></th>

                										<th width="10%"><span class="lt_desc_tab">Weight</span></th>

                										<th width="10%"><span class="lt_desc_tab">Amount</span></th>

                									</tr>

            									</thead>

            									<tbody>

            									</tbody>

            								</table>

                                        </div>

                                    </div>

                                    <br/>

                                    <div class="box box-info">

                                        <!--<div class="box-header with-border">

                                            <h3 class="box-title">Charges Details</h3>

                                        </div>-->

                                        <div class="box-body">

                                            <p>CHARGES</p>

                                            <table id="charges-det" class="table table-bordered text-center">

            									<thead>

                									<tr>

                										<th width="10%"><span class="lt_desc_tab">Charge</span></th>

                										<th width="10%"><span class="lt_desc_tab">Amount</span></th>

                									</tr>

            									</thead>

            									<tbody>

            									</tbody>

            								</table>

                                        </div>

                                    </div>

									<br/>
                                    <div class="box box-info">

                                        <div class="box-body">

                                            <p>ATTRIBUTES</p>

                                            <table id="attributes-det" class="table table-bordered text-center">

            									<thead>

                									<tr>

                										<th width="10%"><span class="lt_desc_tab">Attribute Name</span></th>

                										<th width="10%"><span class="lt_desc_tab">Value</span></th>

                									</tr>

            									</thead>

            									<tbody>

            									</tbody>

            								</table>

                                        </div>

                                    </div>

        					   </div>

    					   </div>

    				    </div>

				 		<!--<div class="col-sm-4">-->

				 			<!-- Lot remaining wait details start here -->

							<!-- <div class="box box-solid">

								<div class="box-header with-border">

								  <h3 class="box-title">Lot Details</h3>

								  <div class="box-tools pull-right">

									<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

								  </div>

								</div>

								<div class="box-body">

									<div class="row">

										<div class="col-sm-3">

											<label>Metal : </label>&nbsp;&nbsp;<span id="lt_metal" class=""></span>

										</div>

										<div class="col-sm-3">

											<label>Category : </label>&nbsp;&nbsp;<span id="lt_category" class=""></span>

										</div>

										<div class="col-sm-3">

											<label>Tax Group : </label>&nbsp;&nbsp;<span id="lt_tax_group" class=""></span>

											<input type="hidden" id="lt_id_tax_group" name="lt_id_tax_group"/>

											<input type="hidden" id="tax_percentage" name="">

											<input type="hidden" id="tgi_calculation" name="">

											<input type="hidden" id="metal_rate" name="">

											<input type="hidden" id="silverrate_1gm" name="">

											<input type="hidden" id="purity" name="lt_item[purity]">

										</div>

										<div class="col-sm-3">

											<label>Lot Date : </label>&nbsp;&nbsp;<span id="lt_date" class=""></span>

										</div>

									</div>

									<h5 class="text-red">Lot Balance :</h5>

									<div class="row">

										<div class="col-sm-3">

										<legend class="sub-title small">Item Details</legend>

											<div class="row">

												<div class="col-sm-4">

													<label>Gross Wgt </label>

												</div>

												<div class="col-sm-8">

													<div class="form-group">

														<div class="input-group ">

															<input class="form-control lot_bal_wt" id="lot_bal_wt" type="number" step=any  value="0" readonly />

															<span class="input-group-addon input-sm lot_bal_wt_uom">UOM</span>

														</div>

													</div>

												</div>

											</div>

											<div class="row">

												<div class="col-sm-4">

													<label>Pieces </label>

												</div>

												<div class="col-sm-8">

													<div class="form-group">

														<div class="input-group ">

															<input class="form-control lot_bal_pcs" id="lot_bal_pcs" type="number" step=any  value="0" readonly />

															<span class="input-group-addon input-sm">Pcs</span>

														</div>

													</div>

												</div>

											</div>

										</div>

										<div class="col-sm-3">

										<legend class="sub-title small">Precious</legend>

											<div class="row">

												<div class="col-sm-4">

													<label>Weight </label>

												</div>

												<div class="col-sm-8">

													<div class="form-group">

														<div class="input-group ">

															<input class="form-control lot_bal_prec_wt" id="lot_bal_prec_wt" type="number" step=any  value="0" readonly />

															<span class="input-group-addon input-sm lot_bal_prec_wt_uom">UOM</span>

														</div>

													</div>

												</div>

											</div>

											<div class="row">

												<div class="col-sm-4">

													<label>Pieces </label>

												</div>

												<div class="col-sm-8">

													<div class="form-group">

														<div class="input-group ">

															<input class="form-control lot_bal_prec_pcs" id="lot_bal_prec_pcs" type="number" step=any  value="0" readonly />

															<span class="input-group-addon input-sm">Pcs</span>

														</div>

													</div>

												</div>

											</div>

										</div>

										<div class="col-sm-3">

										<legend class="sub-title small">Semi-Precious</legend>

											<div class="row">

												<div class="col-sm-4">

													<label>Weight </label>

												</div>

												<div class="col-sm-8">

													<div class="form-group">

														<div class="input-group ">

															<input class="form-control lot_bal_semi_pre_wt" id="lot_bal_semi_pre_wt" type="number" step=any  value="0" readonly />

															<span class="input-group-addon input-sm lot_bal_semi_pre_wt_uom">UOM</span>

														</div>

													</div>

												</div>

											</div>

											<div class="row">

												<div class="col-sm-4">

													<label>Pieces </label>

												</div>

												<div class="col-sm-8">

													<div class="form-group">

														<div class="input-group">

															<input class="form-control lot_bal_semi_pre_pcs" id="lot_bal_semi_pre_pcs" type="number" step=any  value="0" readonly />

															<span class="input-group-addon input-sm">Pcs</span>

														</div>

													</div>

												</div>

											</div>

										</div>

										<div class="col-sm-3">

										<legend class="sub-title small">Normal</legend>

											<div class="row">

												<div class="col-sm-4">

													<label>Weight </label>

												</div>

												<div class="col-sm-8">

													<div class="form-group">

														<div class="input-group ">

															<input class="form-control lot_bal_normal_wt" id="lot_bal_normal_wt" type="number" step=any  value="0" readonly />

															<span class="input-group-addon input-sm lot_bal_normal_wt_uom">UOM</span>

														</div>

													</div>

												</div>

											</div>

											<div class="row">

												<div class="col-sm-4">

													<label>Pieces </label>

												</div>

												<div class="col-sm-8">

													<div class="form-group">

														<div class="input-group ">

															<input class="form-control lot_bal_normal_pcs" id="lot_bal_normal_pcs" type="number" step=any  value="0" readonly />

															<span class="input-group-addon input-sm">Pcs</span>

														</div>

													</div>

												</div>

											</div>

										</div>

									</div>

								</div>

							</div> -->

							<!-- Lot remaining wait details end here -->

				 		<!--</div>-->

				 	</div>

				 	<div class="row stickyBlk" style="display: none;">

				 		<div class="col-md-12">

				 			<div class="col-md-1">

				 				<label>Copy Row</label>

				 			</div>

				 			<div class="col-md-2">

			 		        	<div class="input-group">

			 		        		<input type="number" class="form-control" id="row_value" placeholder="No.of Rows" tabindex="19">

			 		                <input type="hidden" id="id_cmp_emp" name="billing[id_cmp_emp]">

	                                <span class="input-group-btn">

	                                	<button type="button" id="add_multiple_tag" class="btn btn-info" tabindex="20">Copy</button>

	                                </span>

			 		        	</div>

				 		    </div>

				 		    <div class="col-md-2 pull-right">

				 		        <button type="button"  id="add_more_tag" class="btn btn-success" tabindex="18">Add Tag</button>

				 		    </div>

				 		</div>

				 	</div></br>

				    	<div class="row">
				 	        <div class="col-md-2">
    				 	        <select class="form-control to_branch"  id=""></select>
    				 	    </div>
    				 	    <div class="col-md-2">
    				 	        <button type="button" class="btn btn-primary" id="add_to_transfer">Add to Transfer</button>
    				 	    </div>
    				 	    <div class="col-md-6">
    				 	        <p style="text-align:center;"><span>TOTAL PCS&nbsp;<b> <span id="total_pcs">0</span></b>:&nbsp;&nbsp;TOTAL WT&nbsp;<b><span id="total_wt">0</span></b></p></span>
    				 	    </div>
				 	    </div></br>

    				 	<div class="row">

    					 	<div class="col-sm-12">

    					 		<div class="table-responsive">

    					 		    <input type="hidden" id="custom_active_id"  name="">

    			                 <table id="lt_item_tag_preview" class="table table-bordered table-striped text-center"> <!--old id="lt_item_list"-->

    			                    <thead>

    			                      <tr>

    			                        <th width="5%">Lot</th>

    			                        <th width="5%">Tag No</th>

    			                        <th width="10%">Product</th>

    			                        <th width="10%">Design</th>

    			                        <th width="10%">Sub Design</th>

    			                        <!--<th width="10%">Design For</th>-->

    			                        <th width="10%">Calc Type</th>

    			                        <!--<th width="10%">Size</th> -->

    			                        <th width="5%">Pieces</th>

    			                        <th width="10%">Gross Wgt</th>

    			                        <th width="10%">Less Wgt</th>

    			                        <th width="10%">Net Wgt</th>

    			                        <th width="5%">Wast %</th>

    			                        <!--<th width="5%">MC Type</th>-->

    			                        <th width="10%">Making Charge</th>

    			                        <!--<th width="5%">Stone</th>-->

    			                        <th width="10%">Sell Rate</th>

    			                        <th width="5%">Sales Rate</th>

    			                        <th width="10%">Action</th>

    			                      </tr>

    			                    </thead>

    			                    <tbody>

    			                    </tbody>

    			                 </table>

    		                  </div>

    					 	</div>

    				 	</div>

				 	<p class="help-block"></p>

				 	<!--/Block 2-->

				</div>	<!--/ Col -->

			</div>	 <!--/ row -->

		   <p class="help-block"> </p>

		     <!--<div class="row">

			   <div class="box box-default"><br/>

				  <div class="col-xs-offset-5">

				    <button type="submit"  id="tag_submit" class="btn btn-primary">Save</button>

					<button type="button" class="btn btn-default btn-cancel">Cancel</button>

				  </div> <br/>

				</div>

			  </div> -->

            </div>

          <?php echo form_close();?>

            <div class="overlay" style="display:none">

			  <i class="fa fa-refresh fa-spin"></i>

			</div>

             <!-- /form -->

          </div>

    </section>

</div>

<!--  custom items-->

<div class="modal fade" id="cus_stoneModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:95%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>

			</div>

			<div class="modal-body" style="overflow-x: auto;">

				<!--<div class="row">-->

    <!--    			<div class="col pull-right">-->

    <!--    			    <button type="button" id="create_stone_item_details" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add</button>-->

    <!--    			</div>-->

    <!--			</div>-->

				<div class="row">

					<input type="hidden" id="stone_active_row" value="0">

					<div class="table-responsive">

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

					<th>Pur Rate</th>

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
						<td></td>
						<td class="stn_tot_amount"></td>
						<td></td>
					</tr>

					</tfoot>

					</table>

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

<!-- ./Charges Modal -->

<!-- Delete tag modal -->

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Delete Tagging</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this tagging?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / Delete Tag modal -->

</div>

<!--  Image Upload-->


<!-- Attribute Modal -->
<div class="modal fade" id="attribute_modal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:50%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Attributes( <span class="add_attributes"><i class="fa fa-plus"></i></span> )</h4>

			</div>

			<div class="modal-body">

				<div class="row">

					<table id="table_attribute_detail" class="table table-bordered table-striped text-center">

    					<thead>

        					<tr>

								<th width="10%">SNo</th>

            					<th width="35%">Attribute Name</th>

            					<th width="35%">Value</th>

            					<th width="20%">Action</th>

        					</tr>

    					</thead>

    					<tbody></tbody>

					</table>

			    </div>

		    </div>

		  <div class="modal-footer">

			<button type="button" id="update_attribute_details" class="btn btn-success">Save</button>

			<button type="button" id="close_attribute_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

<!-- Attribute Modal -->

<div class="modal fade" id="other_metalmodal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:80%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Other Metals( <span id="create_other_metal_item_details"><i class="fa fa-plus"></i></span> )</h4>

			</div>

			<div class="modal-body">

				<div class="row">

					<input type="hidden" id="charge_active_row" value="0">

					<table id="other_metal_table" class="table table-bordered table-striped text-center">

    					<thead>

        					<tr>

            					<th width="12%">Metal</th>

            					<th width="12%">Purity</th>

            					<th width="10%">Nwt</th>

								<th width="8%">V.A(%)</th>

            					<th width="10%">Mc Type</th>

            					<th width="10%">Mc</th>

            					<th width="10%">Rate</th>

            					<th width="10%">Amount</th>

            					<th width="10%">Action</th>

        					</tr>

    					</thead>

    					<tbody></tbody>

    					<tfoot><tr style="font-weight:bold;"><td>Total</td><td></td><td class="total_wt"></td><td></td><td></td><td></td><td></td><td class="total_amount"></td><td></td></tr></tfoot>

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

</div>






<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add Image</h4>
            </div>
            <div class="modal-body">
                  <div class="row">
                      <div class="col-md-12">
                            <div class="col-md-8">
                                <label>Note - Click Snapshot Button To Take Your Images Screen Shot</label>
                                <label>Press CTRL + I to take Images Screen Shot</label>
                            </div>
                            <div class="col-md-4">
                               <input type="button" value="Take Snapshot" onClick="take_snapshot('pre_images')" class="btn btn-warning" id="snap_shots"><br>
                            </div>
                      </div>
                  </div>
                  <div class="row">
                       <div class="col-md-12">
                             <div class="col-md-3"></div>
                             <div class="col-md-6" id="my_camera"></div>
                             <input type="hidden" name="image" class="image-tag">
                             <div class="col-md-3"></div>
                      </div>
                  </div>
                  <div class="row" id="image_lot_list" style="display:none;">
                      <div class="col-md-12" style="font-weight:bold;">Lot Images List</div>
                  </div><br>
                  <div class="row">
                     <div class="col-md-12" id="uploadArea_p_stn"></div>
                  </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="update_img" class="btn btn-success">Save</button>
            <button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
          </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true" data-backdrop="static" data-keyboard="false">
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
              <button type="button" id="update_img_bulk" class="btn btn-success">Save</button>
              <button type="button" id="close_stone_details" class="btn btn-warning"
                  data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>



<!-- Huid Modal -->
<div class="modal fade" id="huid_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:50%;">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Add HUID( <span class="add_attributes"><i
                            class="fa fa-plus"></i></span> )</h4>

            </div>

            <div class="modal-body">

                <div class="row">

                    <table id="table_huid_detail" class="table table-bordered table-striped text-center">

                        <thead>

                            <tr>

                                <th width="10%">SNo</th>

                                <th width="35%">HUID</th>

                                <th width="20%">Action</th>

                            </tr>

                        </thead>

                        <tbody></tbody>

                    </table>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" id="update_huid_details" class="btn btn-success">Save</button>

                <button type="button" id="close_huid_details" class="btn btn-warning"
                    data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>

<!-- Huid Modal End-->

<div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true" data-backdrop="static" data-keyboard="false">
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
              <button type="button" id="update_img_bulk" class="btn btn-success">Save</button>
              <button type="button" id="close_stone_details" class="btn btn-warning"
                  data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>


