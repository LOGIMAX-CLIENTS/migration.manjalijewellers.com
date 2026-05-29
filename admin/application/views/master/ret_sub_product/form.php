<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		Sub Product
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
		<li class="active">SubProduct</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- form -->
		<?php echo form_open_multipart(( $sub_product['sub_pro_id']!=NULL && $sub_product['sub_pro_id']>0 ?'admin_ret_catalog/ret_sub_product/update/'.$sub_product['sub_pro_id']:'admin_ret_catalog/ret_sub_product/save')); ?>
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
			<h3 class="box-title">Create Sub Product</h3>
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
							    	<div class="form-group">
							    		<label>Status </label>
										 <input type="hidden" id="editid_product" value="<?php echo set_value('"sub_product[sub_pro_id]',(isset($sub_product['sub_pro_id'])?$sub_product['sub_pro_id']:"")); ?>"/>
							    		<input type="checkbox"  class="status" id="switch" data-on-text="ACTIVE" data-off-text="INACTIVE"  value="1" <?php echo $sub_product['sub_pro_status'] == 1? 'checked="true"':''; ?>>
										 <input type="hidden" id="subproduct_status" name="sub_product[sub_pro_status]" value="1">
							    	</div>
						    	</div>
						    </div> </div>
							<!-- <div class="col-md-12"> -->
							<div class='row'>							       
						        <div class='col-sm-4'>
						            <div class='form-group'>
						                <label for="product_name" data-toggle="tooltip"> Sub Product name<span class="error"> *</span></label>
						                <input class="form-control input_text" id="sub_product_name" required="true" name="sub_product[sub_pro_name]" placeholder="Enter product name" value="<?php echo set_value('"sub_product[sub_pro_name]',(isset($sub_product['sub_pro_name'])?$sub_product['sub_pro_name']:"")); ?>" type="text" />
						            </div>
						        </div>						        
							     <div class='col-sm-4'>
							        <div class='form-group'>
						                <label for="short_code"> Short Code<span class="error"> *</span></label>		                
						                <input class="form-control" id="product_short_code" required="true" name="sub_product[sub_pro_code]" placeholder="Enter short code" value="<?php echo set_value('"sub_product[sub_pro_code]',(isset($sub_product['sub_pro_code'])?$sub_product['sub_pro_code']:"")); ?>" type="text" /> 			
						            </div> 
					            </div>
								 <div class="col-sm-4">
							     	 <div class='form-group'>
							                <label for="metal_type">Select Metal Type<span class="error"> *</span></label>
							               <select id="metal_sel" class="form-control" required="true"></select>
									       <input id="metal_id" name="sub_product[metal_type]" type="hidden" value="<?php echo set_value('sub_product[metal_type]',$sub_product['metal_type']); ?>"/>
							                
							            </div> 
							     </div>	
						    </div>
						    <div class="row">
							    <div class="col-sm-4">
							      		<div class='form-group'>
							                <label for="sales_mode">Sales Mode</label>
										    <p class="help-block"></p>
							                <input type="radio" id="sales_mode" name="sub_product[sales_mode]" value="1" checked="true" <?php echo $sub_product['sales_mode'] == 1? 'checked="true"':''; ?>>Weight
											&nbsp;&nbsp;&nbsp;
											<input type="radio" id="sales_mode" name="sub_product[sales_mode]" value="2" <?php echo $sub_product['sales_mode'] == 2? 'checked="true"':''; ?>>Rate
											&nbsp;&nbsp;&nbsp;
											<input type="radio" id="sales_mode" name="sub_product[sales_mode]" value="3" <?php echo $sub_product['sales_mode'] == 3? 'checked="true"':''; ?>>Both
							            </div>	
							        </div>
									<div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="wastage_type">Wastage Type</label>
										    <p class="help-block"></p>
							                <input type="radio" id="wastage_type" name="sub_product[wastage_type]" value="1" <?php echo $sub_product['wastage_type'] == 1? 'checked="true"':''; ?>>Fixed
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="wastage_type" name="sub_product[wastage_type]" value="2" <?php echo $sub_product['wastage_type'] == 2? 'checked="true"':''; ?>>Product base
							            </div>
							        </div>
							     <div class="col-sm-4"> 
					    			<div class="form-group">
					    			<label for="stock_type" data-toggle="tooltip">Stock Type</label>
									<p class="help-block"></p>
					    				<input type="radio" id="stock_type" name="sub_product[stock_type]" value="1"  <?php echo $sub_product['stock_type'] == 1? 'checked="true"':''; ?>>Tagged
										&nbsp;&nbsp;&nbsp;
										<input type="radio" id="stock_type" name="sub_product[stock_type]" value="2" <?php echo $sub_product['stock_type'] == 2? 'checked="true"':''; ?>>Non-Tagged
					    			</div>
							      </div>	
							      
							    </div>
						 
								 <legend>Other Details</legend>
							     <div class="row">
							        <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_stone" class="col-md-6">Stone</label>
							                  <div class="col-md-6">
							                <input type="radio" id="has_stone" name="sub_product[has_stone]" value="1"  <?php echo $sub_product['has_stone'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_stone" name="sub_product[has_stone]" value="0"  <?php echo $sub_product['has_stone'] == 0? 'checked="true"':''; ?>>No
											</div>
							            </div>
							        </div>
									<div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="tag_split" class="col-md-6">Tag Split</label>
							       <div class="col-md-6">
							                 <input type="radio" id="tag_split" name="sub_product[tag_split]" value="1"  <?php echo $sub_product['tag_split'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="tag_split" name="sub_product[tag_split]" value="0" <?php echo $sub_product['tag_split'] == 0? 'checked="true"':''; ?>>No
							         </div></div>
							     </div>	
								 
							        <div class="col-sm-4">	
										<div class='form-group'>
							                <label for="other_materials" class="col-md-6">Other Materials</label>
							                <div class="col-md-6">
							                <input type="radio" id="other_materials" name="sub_product[other_materials]" value="1"   <?php echo $sub_product['other_materials'] == 1? 'checked="true"':''; ?>> Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="other_materials" name="sub_product[other_materials]" value="0"  <?php echo $sub_product['other_materials'] == 0? 'checked="true"':''; ?>> No
							            </div></div>
							        </div>
							     
							     </div>
								 <p class="help-block"></p>
							     <div class="row">
								  <div class="col-sm-4">
								<div class="form-group">
											<label for="stone_board_rate_cal" class="col-md-6">Stone board</label>
												 	<div class="col-md-6">
														<input type="radio" id="stone_board_rate_cal" name="sub_product[stone_board_rate_cal]" value="1"  <?php echo $sub_product['stone_board_rate_cal'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="stone_board_rate_cal" name="sub_product[stone_board_rate_cal]" value="0" <?php echo $sub_product['stone_board_rate_cal'] == 0? 'checked="true"':''; ?>>No	
											  </div></div>													
										  </div>
										<div class="col-sm-4">
								<div class="form-group">
								         <label class="col-md-6">Tag Merge</label>
											<div class="col-md-6">
							                 <input type="radio" id="tag_merge" name="sub_product[tag_merge]" value="1"  <?php echo $sub_product['tag_merge'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="tag_merge" name="sub_product[tag_merge]" value="0" <?php echo $sub_product['tag_merge'] == 0? 'checked="true"':''; ?>>No
    							         </div></div>																								
										  </div>
										       <div class="col-sm-4">
							        <div class='form-group'>
							               <label for="other_charges" class="col-md-6">Other Charges</label>
												 		<div class="col-md-6">
														<input type="radio" id="other_charges" name="sub_product[other_charges]" value="1"  <?php echo $sub_product['other_charges'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="other_charges" name="sub_product[other_charges]" value="0"  <?php echo $sub_product['other_charges'] == 0? 'checked="true"':''; ?>>No	     
							      	</div></div>
							      </div>
							     
							      
							      
							     </div>
							     <p class="help-block"></p>
							     <div class="row">
								 	 <div class="col-sm-4">	
							            <div class='form-group'>
										<label for="less_stone_wt" class="col-md-6">Less Stone Wgt</label>
										<div class="col-md-6">
							               <input type="radio" id="less_stone_wt" name="sub_product[less_stone_wt]" value="1"  <?php echo $sub_product['less_stone_wt'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="less_stone_wt" name="sub_product[less_stone_wt]" value="0"  <?php echo $sub_product['less_stone_wt'] == 0? 'checked="true"':''; ?>>No
							            </div> </div>
							        </div>
							     						  														
													
								  
								  <div class="col-sm-4">
								<div class="form-group">
												  <label for="tag_type" class="col-md-6">Tag Type</label>
												 		<div class="col-md-6">
														<input type="radio" id="tag_type" name="sub_product[tag_type]" value="1"  <?php echo $sub_product['tag_type'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="tag_type" name="sub_product[tag_type]" value="0" <?php echo $sub_product['tag_type'] == 0? 'checked="true"':''; ?>>No	
											  </div></div>														
										  </div>
										  	  <div class="col-sm-4">
								      	  <div class='form-group'> 
								                <label for="central_exces_duty" class="col-md-6">Central Ex Duty</label>
								                <div class="col-md-6">
														<input type="radio" id="central_exces_duty" name="sub_product[central_exces_duty]" value="1"  <?php echo $sub_product['central_exces_duty'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="central_exces_duty" name="sub_product[central_exces_duty]" value="0"  <?php echo $sub_product['central_exces_duty'] == 0? 'checked="true"':''; ?>>No  
								          </div> </div> 	 							            		
								      </div>
							      </div>
							      <p class="help-block"></p>
							   
							    
								  <p class="help-block"></p>
								  <div class="row">
								   	<div class='col-sm-4'>    
								            <div class='form-group'>
								                <label for="net_wt" class="col-md-6">Net Weight</label>
								                 	 <div class="col-md-6">
														<input type="radio" id="net_wt" name="sub_product[net_wt]" value="1"  <?php echo $sub_product['net_wt'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="net_wt" name="sub_product[net_wt]" value="0"  <?php echo $sub_product['net_wt'] == 0? 'checked="true"':''; ?>>No  
								            </div>  </div>
								        </div> 	
											
							     								  														
								<div class="col-sm-4">
								<div class="form-group">
								 <label class="col-md-6">TaxGroup</label>
								  <div class="col-md-6">
											<select id="tax_sel" class="form-control" required="true"></select>
									    <input id="tax_id" name="sub_product[tax_group_id]" type="hidden" value="<?php echo set_value('sub_product[tax_group_id]',$sub_product['tax_group_id']); ?>"/>
    							         </div>	</div>																							
										  </div>	
							    		 
								      <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="less_tax" class="col-md-6">Less Tax</label>
							                <div class="col-md-6">
							                 <input type="radio" id="less_tax" name="sub_product[less_tax]" value="1"  <?php echo $sub_product['less_tax'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="less_tax" name="sub_product[less_tax]" value="0"  <?php echo $sub_product['less_tax'] == 0? 'checked="true"':''; ?>>No
							            </div></div>
							        </div>			
							
							      </div>
								  <p class="help-block"></p>
						<div class="row">
						<div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="has_size" class="col-md-6">Size</label>
							              <div class="col-md-6">
							                 <input type="radio" id="has_size" name="sub_product[has_size]" value="1"  <?php echo $sub_product['has_size'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="has_size" name="sub_product[has_size]" value="0" <?php echo $sub_product['has_size'] == 0? 'checked="true"':''; ?>>No
							            </div></div>
							        </div>
							     							  														
								<div class="col-sm-4">
								<div class="form-group">
								 <label class="col-md-6">RFID Required</label>
											<div class="col-md-6">
							                 <input type="radio" id="rfid_required" name="sub_product[rfid_required]" value="1"  <?php echo $sub_product['rfid_required'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="rfid_required" name="sub_product[rfid_required]" value="0"  <?php echo $sub_product['rfid_required'] == 0? 'checked="true"':''; ?>>No
    							         </div>	</div>																							
										  </div>
									 		  <div class="col-sm-4">
											<div class="form-group">
											<label class="col-md-6">Wastage Billing</label>
											 <div class="col-md-6">
							                 <input type="radio" id="wastage_billing" name="sub_product[wastage_billing]" value="1"  <?php echo $sub_product['wastage_billing'] == 1? 'checked="true"':''; ?>>Yes
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="wastage_billing" name="sub_product[wastage_billing]" value="0"  <?php echo $sub_product['wastage_billing'] == 0? 'checked="true"':''; ?>>No
    							         </div>	</div>																							
										  </div> 														
													
							      </div>
								  <p class="help-block"></p>
					 <div class="row">
					 	  <div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="no_of_pieces" class="col-md-6">No of pieces</label>
							       <div class="col-md-6">
							           <input id="no_of_pieces" name="sub_product[no_of_pieces]" placeholder="Enter no of pieces" value="<?php echo set_value('sub_product[no_of_pieces]',$sub_product['no_of_pieces']); ?>" type="number" />   
							         </div></div>
							     </div>	
								  	  <div class="col-sm-4">
								<div class="form-group">
											<label for="rfid_in_stock" class="col-md-6">RFID Stock</label>
												 <div class="col-md-6">
														<input type="radio" id="rfid_in_stock" name="sub_product[rfid_in_stock]" value="1"  <?php echo $sub_product['rfid_in_stock'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="rfid_in_stock" name="sub_product[rfid_in_stock]" value="0"  <?php echo $sub_product['rfid_in_stock'] == 0? 'checked="true"':''; ?>>No	
											  </div></div>															
										  </div>	
							  					 	
							     <div class="col-sm-4">
							      	 <div class='form-group'>
							      <label for="calculation_based_on" class="col-md-6">Calculation</label>
							         <div class="col-md-6">
									 <select id="calculation_based_on"  name="sub_product[calculation_based_on]" class="form-control" >
				    				 	<option value="1" <?php echo $sub_product['calculation_based_on'] == 1 ? 'selected="selected"':''; ?>>Gross</option>
				    				 	<option value="2" <?php echo $sub_product['calculation_based_on'] == 2 ? 'selected="selected"':''; ?>>Net</option>
				    				 	<option value="3" <?php echo $sub_product['calculation_based_on'] == 3 ? 'selected="selected"':''; ?>>Both</option>
				    				 	<option value="4" <?php echo $sub_product['calculation_based_on'] == 4 ? 'selected="selected"':''; ?>>Fixed</option>
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
							           <input type="radio" id="hallmark" name="sub_product[hallmark]" value="1"  <?php echo $sub_product['hallmark'] == 1? 'checked="true"':''; ?>>Yes
									&nbsp;&nbsp;&nbsp;
									<input type="radio" id="hallmark" name="sub_product[hallmark]" value="0"  <?php echo $sub_product['hallmark'] == 0? 'checked="true"':''; ?>>No   
							         </div> </div>
							     </div>
								 		 <div class="col-sm-4">
								      	  <div class='form-group'> 
								                <label for="stock_report" class="col-md-6">Stock Report</label>
								                 <div class="col-md-6"> 
														<input type="radio" id="stock_report" name="sub_product[stock_report]" value="1"  <?php echo $sub_product['stock_report'] == 1? 'checked="true"':''; ?>>Yes
														&nbsp;&nbsp;&nbsp;
														<input type="radio" id="stock_report" name="sub_product[stock_report]" value="0"  <?php echo $sub_product['stock_report'] == 0? 'checked="true"':''; ?>>No  
								          </div></div>  							            		
								      </div>
									  		
								       <div class="col-sm-4">
								<div class="form-group">
								 <label class="col-md-6">Counter</label>
										 <div class="col-md-6">	
							                 <input type="radio" id="counter" name="sub_product[counter]" value="1"  <?php echo $sub_product['counter'] == 1? 'checked="true"':''; ?>>KDM
										    &nbsp;&nbsp;&nbsp;
										    <input type="radio" id="counter" name="sub_product[counter]" value="2"  <?php echo $sub_product['counter'] == 2? 'checked="true"':''; ?>>Non-KDM
    							         </div>	</div>																							
										  </div>
											
							      </div>
								  <p class="help-block"></p>
								  <p class="help-block"></p>
								   <p class="help-block"></p>
								 <!-- </div>--> 
						  </div>
		
					</div>  
				</div>  <!-- /Tab content --> 
			</div><!-- /.box-body -->
			<div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
				<div class="row">
			  <div class="col-xs-offset-5">
				<button type="submit"  class="btn btn-primary">Save</button> 
				<button type="button" class="btn btn-default btn-cancel">Cancel</button>
			  </div> <br/>
			
		  </div> 
		</div><!-- /.box -->
		 <?php echo form_close();?>
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
