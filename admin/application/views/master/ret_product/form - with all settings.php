<!-- Content Wrapper. Contains page content -->
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
						        <div class='col-sm-4'>
						            <div class='form-group'>
						                <label>Select Category<span class="error"> *</span></label>
									<select id="category_sel" class="form-control" required="true"></select>
									<input id="category_id" name="product[cat_id]" type="hidden" value="<?php echo set_value('product[cat_id]',$product['cat_id']); ?>"/>
						            </div>
						        </div>
						        <div class='col-sm-4'>
						            <div class='form-group'>
						                <label for="product_name" data-toggle="tooltip"> Product name<span class="error"> *</span></label>
						                <input class="form-control input_text" id="product_name" required="true" name="product[product_name]" placeholder="Enter product name" value="<?php echo set_value('"product[product_name]',(isset($product['product_name'])?$product['product_name']:"")); ?>" type="text" />
						            </div>
						        </div>						        
							     <div class='col-sm-4'>
							        <div class='form-group'>
						                <label for="short_code"> Short Code<span class="error"> *</span></label>		                
						                <input class="form-control" id="product_short_code" required="true" name="product[product_short_code]" placeholder="Enter short code" value="<?php echo set_value('"product[product_short_code]',(isset($product['product_short_code'])?$product['product_short_code']:"")); ?>" type="text" /> 			
						            </div> 
					            </div>
						    </div>
						    <div class="row">
							     <div class="col-sm-4">
							     	 <div class='form-group'>
							                <label for="metal_type">Select Metal Type<span class="error"> *</span></label>
							               <select id="metal_sel" class="form-control" required="true"></select>
									       <input id="metal_id" name="product[metal_type]" type="hidden" value="<?php echo set_value('product[metal_type]',$product['metal_type']); ?>"/>
							                
							            </div> 
							     </div>						    
							      <div class="col-sm-4">
							    	<div class='form-group'>
						                <label for="hsn_code">HSN Code</label>
						                <input class="form-control" id="hsn_code" name="product[hsn_code]" value="<?php echo set_value('"product[hsn_code]',(isset($product['hsn_code'])?$product['hsn_code']:"")); ?>"  type="text" placeholder="Enter HSN code" />
						            </div>
							     </div>
							     <div class="col-sm-4"> 
					    			<div class="form-group">
					    			<label for="stock_type" data-toggle="tooltip">Stock Type</label>
									<p class="help-block"></p>
					    				<input type="radio" id="stock_type" name="product[stock_type]" value="1" <?php echo $product['stock_type'] == 1? 'checked="true"':''; ?>>Tagged
										&nbsp;&nbsp;&nbsp;
										<input type="radio" id="stock_type" name="product[stock_type]" value="2" <?php echo $product['stock_type'] == 2? 'checked="true"':''; ?>>Non-Tagged
					    			</div>
							      </div>	
							      
							    </div>
						    <div class="row">
							      <div class="col-sm-4">
							      		<div class='form-group'>
							                <label for="sales_mode">Sales Mode</label>
										    <p class="help-block"></p>
							                <input type="radio" id="sales_mode" name="product[sales_mode]" value="1" <?php echo $product['sales_mode'] == 1? 'checked="true"':''; ?>>Weight
											&nbsp;&nbsp;&nbsp;
											<input type="radio" id="sales_mode" name="product[sales_mode]" value="2" <?php echo $product['sales_mode'] == 2? 'checked="true"':''; ?>>Rate
											&nbsp;&nbsp;&nbsp;
											<input type="radio" id="sales_mode" name="product[sales_mode]" value="3" <?php echo $product['sales_mode'] == 3? 'checked="true"':''; ?>>Both
							            </div>	
							        </div>
							        <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="wastage_type">Wastage Type</label>
										    <p class="help-block"></p>
							                <input type="radio" id="wastage_type" name="product[wastage_type]" value="1" <?php echo $product['wastage_type'] == 1? 'checked="true"':''; ?>>Fixed
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="wastage_type" name="product[wastage_type]" value="2" <?php echo $product['wastage_type'] == 2? 'checked="true"':''; ?>>Product base
							            </div>
							        </div>
							        <div class="col-sm-4">	
										<div class='form-group'>
							                <label for="other_materials">Other Materials</label>
							                <p class="help-block"></p>
							                <input type="radio" id="other_materials" name="product[other_materials]" value="1" <?php echo $product['other_materials'] == 1? 'checked="true"':''; ?>> Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="other_materials" name="product[other_materials]" value="0" <?php echo $product['other_materials'] == 0? 'checked="true"':''; ?>> No
							            </div>
							        </div>
							     </div>
								 <legend>Other Details</legend>
							     <div class="row">
							        <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_stone" class="col-md-6">Stone</label>
							                  <div class="col-md-6">
							                <input type="radio" id="has_stone" name="product[has_stone]" value="1" <?php echo $product['has_stone'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_stone" name="product[has_stone]" value="0" <?php echo $product['has_stone'] == 0? 'checked="true"':''; ?>>No
											</div>
							            </div>
							        </div>
									<div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="tag_split" class="col-md-6">Tag Split</label>
							       <div class="col-md-6">
							                 <input type="radio" id="tag_split" name="product[tag_split]" value="1" <?php echo $product['tag_split'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="tag_split" name="product[tag_split]" value="0"  <?php echo $product['tag_split'] == 0? 'checked="true"':''; ?>>No
							         </div></div>
							     </div>	
						         
								  <div class="col-sm-4">
								<div class="form-group">
											<label for="max_markup" class="col-md-6">Max Markup</label>
												  <div class="col-md-6">
												<input type="number" id="max_markup" class="form-control" name="product[max_markup_per_for_rateitems]" width="15%" value="<?php echo set_value('product[max_markup_per_for_rateitems]',$product['max_markup_per_for_rateitems']); ?>" placeholder="Enter markup" >	
											  </div> </div>													
										  </div>
							     </div>
								 <p class="help-block"></p>
							     <div class="row">
								  <div class="col-sm-4">
								<div class="form-group">
											<label for="stone_board_rate_cal" class="col-md-6">Stone board</label>
												 	<div class="col-md-6">
														<input type="radio" id="stone_board_rate_cal" name="product[stone_board_rate_cal]" value="1" <?php echo $product['stone_board_rate_cal'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="stone_board_rate_cal" name="product[stone_board_rate_cal]" value="0"  <?php echo $product['stone_board_rate_cal'] == 0? 'checked="true"':''; ?>>No	
											  </div></div>													
										  </div>
										<div class="col-sm-4">
								<div class="form-group">
								         <label class="col-md-6">Tag Merge</label>
											<div class="col-md-6">
							                 <input type="radio" id="tag_merge" name="product[tag_merge]" value="1" <?php echo $product['tag_merge'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="tag_merge" name="product[tag_merge]" value="0"  <?php echo $product['tag_merge'] == 0? 'checked="true"':''; ?>>No
    							         </div></div>																								
										  </div>
										       <div class="col-sm-4">
							        <div class='form-group'>
							               <label for="other_charges" class="col-md-6">Other Charges</label>
												 		<div class="col-md-6">
														<input type="radio" id="other_charges" name="product[other_charges]" value="1" <?php echo $product['other_charges'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="other_charges" name="product[other_charges]" value="0"  <?php echo $product['other_charges'] == 0? 'checked="true"':''; ?>>No	     
							      	</div></div>
							      </div>
							     
							      
							      
							     </div>
							     <p class="help-block"></p>
							     <div class="row">
								 	 <div class="col-sm-4">	
							            <div class='form-group'>
										<label for="less_stone_wt" class="col-md-6">Less Stone Wgt</label>
										<div class="col-md-6">
							               <input type="radio" id="less_stone_wt" name="product[less_stone_wt]" value="1" <?php echo $product['less_stone_wt'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="less_stone_wt" name="product[less_stone_wt]" value="0" <?php echo $product['less_stone_wt'] == 0? 'checked="true"':''; ?>>No
							            </div> </div>
							        </div>
							     						  														
													
								  
								  <div class="col-sm-4">
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
								      </div>
							      </div>
							      <p class="help-block"></p>
							      
							      <div class="row">	
								  <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_screw" class="col-md-6">Screw</label>
							                  <div class="col-md-6">
							                 <input type="radio" id="has_screw" name="product[has_screw]" value="1" <?php echo $product['has_screw'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_screw" name="product[has_screw]" value="0"  <?php echo $product['has_screw'] == 0? 'checked="true"':''; ?>>No
							            </div></div>
							        </div>	
									 <div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="no_of_tags" class="col-md-6">No of Tags</label>
							        <div class="col-md-6">
							           <input type="number" id="no_of_tags" class="form-control" name="product[no_of_tags_to_print]" value="<?php echo set_value('product[no_of_tags_to_print]',$product['no_of_tags_to_print']); ?>" placeholder="no of tags" value="">
							         </div></div>
							     </div>    
							 
								      <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_fixed_price" class="col-md-6">Fixed Price</label>
							                <div class="col-md-6">
							                 <input type="radio" id="has_fixed_price" name="product[has_fixed_price]" value="1" <?php echo $product['has_fixed_price'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_fixed_price" name="product[has_fixed_price]" value="0"  <?php echo $product['has_fixed_price'] == 0? 'checked="true"':''; ?>>No
							            </div></div>
							        </div>
							    
							      </div>
								  <p class="help-block"></p>
								  <div class="row">
								  <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_hook" class="col-md-6">Hook</label>
							               <div class="col-md-6">
							                <input type="radio" id="has_hook" name="product[has_hook]" value="1" <?php echo $product['has_hook'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_hook" name="product[has_hook]" value="0"  <?php echo $product['has_hook'] == 0? 'checked="true"':''; ?>>No
							            </div> </div>
							        </div>
								   	<div class='col-sm-4'>    
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
										  </div>	
							    					
							
							      </div>
								  <p class="help-block"></p>
						<div class="row">
						<div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_size" class="col-md-6">Size</label>
							              <div class="col-md-6">
							                 <input type="radio" id="has_size" name="product[has_size]" value="1" <?php echo $product['has_size'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_size" name="product[has_size]" value="0"  <?php echo $product['has_size'] == 0? 'checked="true"':''; ?>>No
							            </div></div>
							        </div>
							     							  														
								<div class="col-sm-4">
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
										  </div> 														
													
							      </div>
								  <p class="help-block"></p>
					 <div class="row">
					 	  <div class="col-sm-4">
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
										  </div>	
							  					 	
							     <div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="calculation_based_on" class="col-md-6">Calculation</label>
							         <div class="col-md-6">
									 <select id="calculation_based_on"  name="product[calculation_based_on]" class="form-control" >
				    				 	<option value="1" <?php echo $product['calculation_based_on'] == 1 ? 'selected="selected"':''; ?>>Gross</option>
				    				 	<option value="2" <?php echo $product['calculation_based_on'] == 2 ? 'selected="selected"':''; ?>>Net</option>
				    				 	<option value="3" <?php echo $product['calculation_based_on'] == 3 ? 'selected="selected"':''; ?>>Both</option>
				    				 	<option value="4" <?php echo $product['calculation_based_on'] == 4 ? 'selected="selected"':''; ?>>Fixed</option>
				    				 </select>
							         </div> </div>
							     </div>								  														
													
							      </div>
								  <p class="help-block"></p>
								  <div class="row">
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
											
							      </div>
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
