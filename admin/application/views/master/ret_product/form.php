<!-- Content Wrapper. Contains page content -->
<style>
	.custom-label{
		font-weight: 400;
	}

	.add_prod_charge {

		cursor: pointer;

		color: blue;

	}

	.container-charges {
		
		padding-bottom: 25px;

	}

	.removeProdCharge {

		margin-left: 5px;

	}

</style>
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		Product
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
		<li class="active">Product</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- form -->
		<?php echo form_open_multipart(( $product['pro_id']!=NULL && $product['pro_id']>0 ?'admin_ret_catalog/ret_product/update/'.$product['pro_id']:'admin_ret_catalog/ret_product/save')); ?>
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
			<h3 class="box-title">Create Product</h3>
			<div class="box-tools pull-right">
			<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
			<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
			</div>
			</div>
			<div class="box-body">
			  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
		      </div>
				<div class="row">  
					<div class="tab-content col-md-12">
						<div class="tab-pane active" id="tab_1">
						<div class="col-md-12">
							<div class="row">
						    	<div class="col-md-4 pull-right">
							    	<div class="form-group ">
							    		<label>Status </label>
										 <input type="hidden" id="editid_product" value="<?php echo set_value('"product[pro_id]',(isset($product['pro_id'])?$product['pro_id']:"")); ?>"/>
							    		<input type="checkbox"  class="status" data-on-text="ACTIVE" data-off-text="INACTIVE"  value="1" <?php echo $product['product_status'] == 1? 'checked="true"':''; ?>>
										 <input type="hidden" id="product_status" name="product[product_status]" value="1">
							    	</div>
						    	</div>
						    </div> 
							</div>
							<!-- <div class="col-md-12"> -->
							<div class='row'>	
								<div class="col-sm-2">
							    	<div class='form-group'>
							           <label for="metal_type">Metal Type<span class="error"> *</span></label>
							           <select id="metal_sel" class="form-control" required="true" style="width:100%;"></select>
									   <input id="metal_id" name="product[metal_type]" type="hidden" value="<?php echo set_value('product[metal_type]',$product['metal_type']); ?>"/>
							                
							       </div> 
							    </div>
							    <div class='col-sm-2'>
						            <div class='form-group'>
						                <label>Category<span class="error"> *</span></label>
									<select id="category_sel" class="form-control" required="true" style="width:100%;"></select>
									<input id="category_id" name="product[cat_id]" type="hidden" value="<?php echo set_value('product[cat_id]',$product['cat_id']); ?>"/>
						            </div>
						        </div>
						        
						        <div class="col-sm-2"> 
					    			<div class="form-group">
					    			<label for="" data-toggle="tooltip">Tax Type</label>
									<p class="help-block"></p>
					    				<input type="radio" id="tax_type1" name="product[tax_type]" value="1" <?php echo $product['tax_type'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="tax_type1"> Inclusive</label>
										&nbsp;&nbsp;&nbsp;
										<input type="radio" id="tax_type2" name="product[tax_type]" value="2" <?php echo $product['tax_type'] == 2? 'checked="true"':''; ?>> <label class="custom-label" for="tax_type2"> Exclusive</label>
					    			</div>
							    </div>
						        
                			    <div class='col-sm-2'>
                				 	<div class="form-group">
                                       <label>Sales Tax Group<span class="error"> *</span></label>
                						<select id="tgrp_sel" class="form-control" name="product[tgrp_id]"></select>
                						<input id="tax_id"  type="hidden" value="<?php echo set_value('product[tgrp_id]',$product['tgrp_id']); ?>" />
                                    </div>
                				 </div>

								 <div class='col-sm-2'>
                				 	<div class="form-group">
                                       <label>Purchase Tax Group<span class="error"> *</span></label>
                						<select id="pur_tgrp_sel" class="form-control" name="product[tax_group_id]"></select>
                						<input id="pur_tax_id"  type="hidden" value="<?php echo set_value('product[tax_group_id]',$product['tax_group_id']); ?>" />
                                    </div>
                				 </div>
								 <div class='col-sm-2'>
								<div class='form-group'>
									<label>Section</label>
									<select id="section_sel" name="product[id_section][]" class="form-control" style="width:100%;" multiple></select>
									<?php foreach ($section as $k): ?>
										<input class="id_section" type="hidden" value="<?php echo isset($k['id_section']) ? $k['id_section'] . ',' : ''; ?>">
									<?php endforeach; ?>	
								</div>
							</div>


								  <!-- <div class='col-sm-2'>
									<div class='form-group'>
										<label>Section</label>
										<select id="section_sel" name="product[id_section][]" class="form-control" style="width:100%;" multiple>
											<?php foreach ($section as $k): ?>
												<option value="<?php echo $k['id_section']; ?>" <?php echo in_array($k['id_section'], $selected_sections) ? 'selected' : ''; ?>>
													 <php echo $k['section_name']; ?>
												</option>
											<?php endforeach; ?>
										</select>
										<?php foreach ($selected_sections as $section_id): ?>
											<input type="hidden" name="selected_sections[]" value="<?php echo $section_id; ?>">
										<?php endforeach; ?>
									</div>
								</div>  -->

						    </div>
						    <div class="row">
								<?php if($product['pro_id']!=NULL){	?>	        
								<!--<div class='col-sm-3'>
									<div class='form-group'>
										<label for="short_code"> Short Code</label>							                				
										<p><?php echo $product['product_short_code'] ?></p>
									</div> 
								</div>-->
								<?php }?>
								<div class='col-sm-2'>
						            <div class='form-group'>
						                <label for="product_name" data-toggle="tooltip"> Product name<span class="error"> *</span></label>
						                <input class="form-control" id="product_name" required="true" name="product[product_name]" placeholder="Enter product name" value="<?php echo set_value('"product[product_name]',(isset($product['product_name'])?$product['product_name']:"")); ?>" type="text" />
						            </div>
						        </div> 
								
								<div class="col-sm-2">
							    	<div class='form-group'>
						                <label for="hsn_code">HSN Code</label>
						                <input class="form-control" id="hsn_code" name="product[hsn_code]" value="<?php echo set_value('"product[hsn_code]',(isset($product['hsn_code'])?$product['hsn_code']:"")); ?>"  type="text" placeholder="Enter HSN code" />
						            </div>
							    </div>
							    
								<div class='col-sm-2'>
    						        <div class='form-group'>
    					                <label for="product_short_code"> Short Code<span class="error"> *</span></label>		                
    					                <input class="form-control" id="product_short_code" required="true" name="product[product_short_code]" placeholder="Enter short code" value="<?php echo set_value('"product[product_short_code]',(isset($product['product_short_code'])?$product['product_short_code']:"")); ?>" type="text" /> 			
    					            </div> 
    							</div>

								<div class='col-sm-2'>
    						        <div class='form-group'>
    					                <label for="product_short_code">No of pieces<span class="error"> </span></label>		                
    					                <input class="form-control" id="no_of_pieces"  name="product[no_of_pieces]" placeholder="Enter no of pieces" value="<?php echo set_value('"product[no_of_pieces]',(isset($product['no_of_pieces'])?$product['no_of_pieces']:"")); ?>" type="number" /> 			
    					            </div> 
    							</div>

							    <div class="col-sm-3"> 
					    			<div class="form-group">
					    			<label for="" data-toggle="tooltip">Stock Type</label>
									<p class="help-block"></p>
					    				<input type="radio" id="stock_type1" name="product[stock_type]" value="1" <?php echo $product['stock_type'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="stock_type1"> Tagged</label>
										&nbsp;&nbsp;&nbsp;
										<input type="radio" id="stock_type2" name="product[stock_type]" value="2" <?php echo $product['stock_type'] == 2? 'checked="true"':''; ?>> <label class="custom-label" for="stock_type2"> Non-Tagged</label>
					    			</div>
							    </div>
							    
							    <div class="col-sm-3 stone_type"  > 
					    			<div class="form-group">
					    			<label for="" data-toggle="tooltip">Product Type</label>
									<p class="help-block"></p>
					    				<input type="radio" id="stone_type0" name="product[stone_type]" value="0" <?php echo $product['stone_type'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="stone_type0"> Ornaments</label>
					    				&nbsp;&nbsp;&nbsp;
					    				<input type="radio" id="stone_type1" name="product[stone_type]" value="1" <?php echo $product['stone_type'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="stone_type1"> Stone</label>
										&nbsp;&nbsp;&nbsp;
										<input type="radio" id="stone_type2" name="product[stone_type]" value="2" <?php echo $product['stone_type'] == 2? 'checked="true"':''; ?>> <label class="custom-label" for="stone_type2"> Diamond</label>
					    			</div>
							    </div>

								<div class='col-sm-2 uom_block' style="<?php echo empty($product['stone_type']) || $product['stone_type'] == 0 ? 'display:none"' : ''; ?>">
                				 	<div class="form-group">
                                       <label>UOM</label> <br>
                						<select id="size_sel" class="form-control" name="product[uom_id]"></select>
                						<input id="size"  type="hidden" value="<?php echo set_value('product[uom_id]',$product['uom_id']); ?>" />
                                    </div>
                				 </div>
							    
							    <div class="col-sm-3">	
									<div class='form-group'>
						                <label for="other_materials">Wastage %  <span class="error"> *</span></label>							                
						                <p class="help-block"></p>
						                <div class="row">
						                	<div class="col-md-6 form-group">
						                		<div class="input-group">
						                			<span class="input-group-addon input-sm">Min.</span>
						                			<input type="number" id="min_markup" class="form-control" name="product[min_wastage]"  value="<?php echo set_value('product[min_wastage]',$product['min_wastage']); ?>" step="any" required="true">
						                		</div>
						                	</div> 
						                	<div class="col-md-6">
						                		<div class="input-group">
						                			<span class="input-group-addon input-sm">Max.</span>
						                			<input type="number" id="max_markup" class="form-control" name="product[max_wastage]"  value="<?php echo set_value('product[max_wastage]',$product['max_wastage']); ?>" step="any" required="true">
						                		</div> 
						                	</div>
						                </div>
						            </div>
						        </div> 
							    <div class="col-sm-2">
						      		<div class='form-group'>
						                <label for="sales_mode">Sales Based On</label>
									    <p class="help-block"></p>
						                <input type="radio" id="sales_mode2" name="product[sales_mode]" value="1" <?php echo $product['sales_mode'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="sales_mode2"> Fixed Rate</label>
						                &nbsp;&nbsp;&nbsp;
						                <input type="radio" id="sales_mode1" name="product[sales_mode]" value="2" <?php echo $product['sales_mode'] == 2? 'checked="true"':''; ?>> <label class="custom-label" for="sales_mode1"> Flexible Rate</label>
						            </div>	
						        </div>  
						        
								<div class="col-sm-3">
						      		<div class='form-group'>
						                <label for="sales_mode">Reorder Based On</label>
									    <p class="help-block"></p>
						                <input type="radio" id="reorder_based_on1" name="product[reorder_based_on]" value="1" <?php echo $product['reorder_based_on'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="reorder_based_on1">Weight Range</label>
						                &nbsp;&nbsp;&nbsp;
						                <input type="radio" id="reorder_based_on2" name="product[reorder_based_on]" value="2" <?php echo $product['reorder_based_on'] == 2? 'checked="true"':''; ?>> <label class="custom-label" for="reorder_based_on2">Size</label>
						            </div>	
						        </div>
						        
							</div>
							 <div class="row">
							     
							     <div class="col-sm-3">
						      		<div class='form-group'>
						                <label for="sales_mode">Purchase Based On</label>
									    <p class="help-block"></p>
						                <input type="radio" id="purchase_mode2" name="product[purchase_mode]" value="1" <?php echo $product['purchase_mode'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="purchase_mode2"> Fixed Rate</label>
						                &nbsp;&nbsp;&nbsp;
						                <input type="radio" id="purchase_mode1" name="product[purchase_mode]" value="2" <?php echo $product['purchase_mode'] == 2? 'checked="true"':''; ?>> <label class="custom-label" for="purchase_mode1"> Flexible Rate</label>
						            </div>	
						        </div>
						        
							     <div class="col-sm-2">
            					   <label for="chargeseme_name">Upload image</label>
                					   <div class="col-md-7">
                					         <p class="help-block"></p>
                							 <input id="ret_product_img" name="product[ret_product_img]" accept="image/*" type="file" >
                						<img src="<?php echo(isset($product['image'])?$product['image']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="ret_product_img_preview" style="width:304px;height:100%;" alt="ret product image"> 
										<p class="help-block">File size should not exceed 1MB<br/>Image format should be .jpg or .png</p>
									</div>
            				     </div>	
							     
							 </div><p></p>
						    <div class="row">
						      	
						       <!-- <div class="col-sm-4">	
						            <div class='form-group'>
						                <label for="wastage_type">Wastage Type</label>
									    <p class="help-block"></p>
						                <input type="radio" id="wastage_type" name="product[wastage_type]" value="1" <?php echo $product['wastage_type'] == 1? 'checked="true"':''; ?>>Fixed
									    &nbsp;&nbsp;&nbsp;
									    <input type="radio" id="wastage_type" name="product[wastage_type]" value="2" <?php echo $product['wastage_type'] == 2? 'checked="true"':''; ?>>Product base
						            </div>
						        </div> -->
						         
						     </div>
							 <div class="container-charges">
								 <legend>
									 Charges ( <span class="add_prod_charge"><i class="fa fa-plus"></i></span> )
								 </legend>
								 <div class="prod_charges">

								 	<?php if($product['pro_id'] !=	NULL && $product['pro_id']	>	0) { ?>
										
										<?php $i = 1;
											$total_charges = count($charges);
											foreach($charges as $charge) { 
										?>
											<div class="row row_prod_charges">					       
												<div class="col-md-3">
													<div class="form-group">
														<select class="form-control prod_charge_name" name="product[charges][id][]" placeholder="Charge Name">
															<?php foreach($charges_list as $list) { ?>
																<option <?php if($list['id_charge'] == $charge['charge_id']) { ?> selected <?php } ?> value="<?php echo $list['id_charge'] ?>"> <?php echo $list['name_charge'] ?> </option>
															<?php } ?>
														</select>
													</div>
												</div>

												<div class="col-md-2">
													<div class="form-group">
														<input class="form-control prod_charge_value" name="product[charges][value][]"  type="number" placeholder="Charge Value" step="any" value="<?php echo $charge['charge_value'] ?>" />
													</div>
												</div>
												<div class="col-md-1 product_charges_button">
													<div class="form-group">
														<?php if($i == $total_charges) { ?>
														<button type="button" class="btn btn-success addProdCharge"><i class="fa fa-plus"></i></button>
														<?php } ?>
														<button type="button" class="btn btn-danger removeProdCharge"><i class="fa fa-trash"></i></button>
													</div>
												</div>
											</div>

									<?php $i++; 
									}  } ?>

								 </div>
							 </div>
								 <legend>Other Details</legend> 
							     <div class="row">
							     	<div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="other_materials" class="col-md-6">Has Other Materials</label>
							                  <div class="col-md-6">
							                	<input type="radio" id="other_materials1" name="product[other_materials]" value="1" <?php echo $product['other_materials'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="other_materials1"> Yes</label>
										    &nbsp;&nbsp;&nbsp;
										    	<input type="radio" id="other_materials2" name="product[other_materials]" value="0" <?php echo $product['other_materials'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="other_materials2"> No</label>
											</div>
							            </div>
							        </div>
							        <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="other_materials" class="col-md-6">Enable Gift</label>
							                  <div class="col-md-6">
							                	<input type="radio" id="gift_applicable1" name="product[gift_applicable]" value="1" <?php echo $product['gift_applicable'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="gift_applicabl1"> Yes</label>
										    &nbsp;&nbsp;&nbsp;
										    	<input type="radio" id="gift_applicable2" name="product[gift_applicable]" value="0" <?php echo $product['gift_applicable'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="gift_applicabl2"> No</label>
											</div>
							            </div>
							        </div>
									<div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="other_materials" class="col-md-6">Display Purity</label>
							                  <div class="col-md-6">
							                	<input type="radio" id="display_purity_yes" name="product[display_purity]" value="1" <?php echo $product['display_purity'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="display_purity_yes"> Yes</label>
										    &nbsp;&nbsp;&nbsp;
										    	<input type="radio" id="display_purity_no" name="product[display_purity]" value="0" <?php echo $product['display_purity'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="display_purity_no"> No</label>
											</div>
							            </div>
							        </div>
							     </div>
							     <p></p>
							     <div class="row"> 
							        <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_stone" class="col-md-6">Has Stone</label>
							                  <div class="col-md-6">
							                <input type="radio" id="has_stone1" name="product[has_stone]" value="1" <?php echo $product['has_stone'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="has_stone1"> Yes</label>
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_stone2" name="product[has_stone]" value="0" <?php echo $product['has_stone'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="has_stone2"> No</label>
											</div>
							            </div>
							        </div>
									  <div class="col-sm-4">
										<div class="form-group">
											<label for="stone_board_rate_cal" class="col-md-6">Stone board Rate</label>
												 	<div class="col-md-6">
														<input type="radio" id="stone_board_rate_cal1" name="product[stone_board_rate_cal]" value="1" <?php echo $product['stone_board_rate_cal'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="stone_board_rate_cal1"> Yes</label>
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="stone_board_rate_cal2" name="product[stone_board_rate_cal]" value="0"  <?php echo $product['stone_board_rate_cal'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="stone_board_rate_cal2"> No</label>	
											  </div></div>													
										  </div>
										  <div class="col-sm-4">	
							            <div class='form-group'>
										<label for="less_stone_wt" class="col-md-6">Less Stone Wgt</label>
										<div class="col-md-6">
							               <input type="radio" id="less_stone_wt1" name="product[less_stone_wt]" value="1" <?php echo $product['less_stone_wt'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="less_stone_wt1"> Yes</label>	
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="less_stone_wt2" name="product[less_stone_wt]" value="0" <?php echo $product['less_stone_wt'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="less_stone_wt2"> No</label>	
							            </div> </div>
							        </div>
									<div class="col-sm-4">
						      	 <div class='form-group'>
						      <label for="ag_parts" class="col-md-6">Tag Split</label>
						       <div class="col-md-6">
						                 <input type="radio" id="ag_parts1" name="product[ag_parts]" value="1" <?php echo $product['ag_parts'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="ag_parts1"> Yes</label>
									    &nbsp;&nbsp;&nbsp;
									    <input type="radio" id="ag_parts2" name="product[ag_parts]" value="0"  <?php echo $product['ag_parts'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="ag_parts2"> No</label>
						         </div></div>
						     </div>
						         
								  <!--<div class="col-sm-4">
								<div class="form-group">
											<label for="max_markup" class="col-md-6">Max Markup</label>
												  <div class="col-md-6">
												<input type="number" id="max_markup" class="form-control" name="product[max_markup_per_for_rateitems]" width="15%" value="<?php echo set_value('product[max_markup_per_for_rateitems]',$product['max_markup_per_for_rateitems']); ?>" placeholder="Enter markup" >	
											  </div> </div>													
										  </div>-->
							     </div>
								 <p class="help-block"></p>
							     <div class="row">
								
										<!--<div class="col-sm-4">
								<div class="form-group">
								         <label class="col-md-6">Tag Merge</label>
											<div class="col-md-6">
							                 <input type="radio" id="tag_merge" name="product[tag_merge]" value="1" <?php echo $product['tag_merge'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="tag_merge" name="product[tag_merge]" value="0"  <?php echo $product['tag_merge'] == 0? 'checked="true"':''; ?>>No
    							         </div></div>																								
										  </div>-->
										<div class="col-sm-4">
							        	<div class='form-group'>
							               <label for="other_charges" class="col-md-6">Has Other Charges</label>
									 		<div class="col-md-6">
											<input type="radio" id="other_charges1" name="product[other_charges]" value="1" <?php echo $product['other_charges'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="other_charges1"> Yes</label>	
											&nbsp;&nbsp;&nbsp;
											<input type="radio" id="other_charges2" name="product[other_charges]" value="0"  <?php echo $product['other_charges'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="other_charges2"> No</label>		     
							      	</div></div>
							      </div>
							     
							       <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_screw" class="col-md-6">Has Screw</label>
							                  <div class="col-md-6">
							                 <input type="radio" id="has_screw1" name="product[has_screw]" value="1" <?php echo $product['has_screw'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="has_screw1"> Yes</label>
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_screw2" name="product[has_screw]" value="0"  <?php echo $product['has_screw'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="has_screw2"> No</label>
							            </div></div>
							        </div>
							      <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_size" class="col-md-6">Has Size</label>
							              <div class="col-md-6">
							                 <input type="radio" id="has_size1" name="product[has_size]" value="1" <?php echo $product['has_size'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="has_size1"> Yes</label>
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_size2" name="product[has_size]" value="0"  <?php echo $product['has_size'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="has_size2"> No</label>
							            </div></div>
							        </div>
							     </div>
							     <p class="help-block"></p>
							     <div class="row">
								 	 
							     						  														
													
								  
								  <!--<div class="col-sm-4">
								<div class="form-group">
												  <label for="tag_type" class="col-md-6">Tag Type</label>
												 		<div class="col-md-6">
														<input type="radio" id="tag_type" name="product[tag_type]" value="1" <?php echo $product['tag_type'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="tag_type" name="product[tag_type]" value="0" <?php echo $product['tag_type'] == 0? 'checked="true"':''; ?>>No	
											  </div></div>														
										  </div>
										  	  <div class="col-sm-4">
								      	  <div class='form-group'> 
								                <label for="central_exces_duty" class="col-md-6">Central Ex Duty</label>
								                <div class="col-md-6">
														<input type="radio" id="central_exces_duty" name="product[central_exces_duty]" value="1" <?php echo $product['central_exces_duty'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="central_exces_duty" name="product[central_exces_duty]" value="0"  <?php echo $product['central_exces_duty'] == 0? 'checked="true"':''; ?>>No  
								          </div> </div> 	 							            		
								      </div>-->
							      </div>
							      <p class="help-block"></p>
							      
							      <div class="row">	
								 	
									<!-- <div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="no_of_tags" class="col-md-6">No of Tags</label>
							        <div class="col-md-6">
							           <input type="number" id="no_of_tags" class="form-control" name="product[no_of_tags_to_print]" value="<?php echo set_value('product[no_of_tags_to_print]',$product['no_of_tags_to_print']); ?>" placeholder="no of tags" value="">
							         </div></div>
							     </div>-->    
							 
								      <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_fixed_price" class="col-md-6">Fixed Price</label>
							                <div class="col-md-6">
							                 <input type="radio" id="has_fixed_price1" name="product[has_fixed_price]" value="1" <?php echo $product['has_fixed_price'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="has_fixed_price1"> Yes</label>
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_fixed_price2" name="product[has_fixed_price]" value="0"  <?php echo $product['has_fixed_price'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="has_fixed_price2"> No</label>
							            </div></div>
							        </div>
							      <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_hook" class="col-md-6">Has Hook</label>
							               <div class="col-md-6">
							                <input type="radio" id="has_hook1" name="product[has_hook]" value="1" <?php echo $product['has_hook'] == 1? 'checked="true"':''; ?>> <label class="custom-label" for="has_hook1"> Yes</label>
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_hook2" name="product[has_hook]" value="0"  <?php echo $product['has_hook'] == 0? 'checked="true"':''; ?>> <label class="custom-label" for="has_hook2"> No</label>
							            </div> </div>
							        </div>
									<div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="calculation_based_on" class="col-md-6">Calc. based on</label>
							         <div class="col-md-6">
									 <select id="calculation_based_on"  name="product[calculation_based_on]" class="form-control" >
									    <option value=""></option>
				    				 	<option value="0" <?php echo $product['calculation_based_on'] == 0 ? 'selected="selected"':''; ?>>Mc & Wast On Gross</option>
				    				 	<option value="1" <?php echo $product['calculation_based_on'] == 1 ? 'selected="selected"':''; ?>>Mc & Wast On Net</option>
				    				 	<option value="2" <?php echo $product['calculation_based_on'] == 2 ? 'selected="selected"':''; ?>>Mc on Gross,Wast On Net</option>
				    				 	<option value="3" <?php echo $product['calculation_based_on'] == 3 ? 'selected="selected"':''; ?>>Fixed Rate</option>
				    				 	<option value="4" <?php echo $product['calculation_based_on'] == 4 ? 'selected="selected"':''; ?>>Fixed Rate based on Weight</option>
				    				 </select>
							         </div> </div>
							     </div>	
							      </div>
								  <p class="help-block"></p>
								  <div class="row">
								  
								   	<!--<div class='col-sm-4'>    
								            <div class='form-group'>
								                <label for="net_wt" class="col-md-6">Net Weight</label>
								                 	 <div class="col-md-6">
														<input type="radio" id="net_wt" name="product[net_wt]" value="1" <?php echo $product['net_wt'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="net_wt" name="product[net_wt]" value="0"  <?php echo $product['net_wt'] == 0? 'checked="true"':''; ?>>No  
								            </div>  </div>
								        </div> 	
											
							     								  														
								<div class="col-sm-4">
								<div class="form-group">
								 <label class="col-md-6">TaxGroup</label>
								  <div class="col-md-6">
											<select id="tax_sel" class="form-control" required="true"></select>
									    <input id="tax_id" name="product[tax_group_id]" type="hidden" value="<?php echo set_value('product[tax_group_id]',$product['tax_group_id']); ?>"/>
    							         </div>	</div>																							
										  </div>-->	
							    					
							
							      </div>
								  <p class="help-block"></p>
						<div class="row">
						
							     							  														
								<!--<div class="col-sm-4">
								<div class="form-group">
								 <label class="col-md-6">RFID Required</label>
											<div class="col-md-6">
							                 <input type="radio" id="rfid_required" name="product[rfid_required]" value="1" <?php echo $product['rfid_required'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="rfid_required" name="product[rfid_required]" value="0"  <?php echo $product['rfid_required'] == 0? 'checked="true"':''; ?>>No
    							         </div>	</div>																							
										  </div>
									 		  <div class="col-sm-4">
											<div class="form-group">
											<label class="col-md-6">Sales Markup</label>
											 <div class="col-md-6">
							                 <input type="radio" id="sales_markup" name="product[sales_markup]" value="1" <?php echo $product['sales_markup'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="sales_markup" name="product[sales_markup]" value="0"  <?php echo $product['sales_markup'] == 0? 'checked="true"':''; ?>>No
    							         </div>	</div>																							
										  </div> -->														
													
							      </div>
								  <p class="help-block"></p>
					 <div class="row">
					 	  <!--<div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="no_of_pieces" class="col-md-6">No of pieces</label>
							       <div class="col-md-6">
							           <input id="no_of_pieces" name="product[no_of_pieces]" class="form-control" placeholder="Enter no of pieces" value="<?php echo set_value('product[no_of_pieces]',$product['no_of_pieces']); ?>" type="number" />   
							         </div></div>
							     </div>	
								  	  <div class="col-sm-4">
								<div class="form-group">
											<label for="rfid_in_stock" class="col-md-6">RFID Stock</label>
												 <div class="col-md-6">
														<input type="radio" id="rfid_in_stock" name="product[rfid_in_stock]" value="1" <?php echo $product['rfid_in_stock'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="rfid_in_stock" name="product[rfid_in_stock]" value="0"  <?php echo $product['rfid_in_stock'] == 0? 'checked="true"':''; ?>>No	
											  </div></div>															
										  </div>-->	
							  					 	
							     							  														
													
							      </div>
								  <p class="help-block"></p>
								  <!--<div class="row">
								   <div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="hallmark" class="col-md-6">Hallmark</label>
							         <div class="col-md-6">
							           <input type="radio" id="hallmark" name="product[hallmark]" value="1" <?php echo $product['hallmark'] == 1? 'checked="true"':''; ?>>Yes
									&nbsp;&nbsp;&nbsp;
									<input type="radio" id="hallmark" name="product[hallmark]" value="0"  <?php echo $product['hallmark'] == 0? 'checked="true"':''; ?>>No   
							         </div> </div>
							     </div>
								 		 <div class="col-sm-4">
								      	  <div class='form-group'> 
								                <label for="stock_report" class="col-md-6">Stock Report</label>
								                 <div class="col-md-6"> 
														<input type="radio" id="stock_report" name="product[stock_report]" value="1" <?php echo $product['stock_report'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="stock_report" name="product[stock_report]" value="0"  <?php echo $product['stock_report'] == 0? 'checked="true"':''; ?>>No  
								          </div></div>  							            		
								      </div>
									  		
								       <div class="col-sm-4">
								<div class="form-group">
								 <label class="col-md-6">Counter</label>
										 <div class="col-md-6">	
							                 <input type="radio" id="counter" name="product[counter]" value="1"  <?php echo $product['counter'] == 1? 'checked="true"':''; ?>>KDM
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="counter" name="product[counter]" value="2"  <?php echo $product['counter'] == 2? 'checked="true"':''; ?>>Non-KDM
    							         </div>	</div>																							
										  </div>
											
							      </div>-->
								  <p class="help-block"></p>
								 
						</div>
					</div>  
				</div>  <!-- /Tab content --> 
			</div><!-- /.box-body -->
			<div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
				<div class="row">
				  <div class="col-md-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
					  </div> <br/>
					  </div> 
					</div>
				  </div> 
		</div><!-- /.box -->
		 <?php echo form_close();?>
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
