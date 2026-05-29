<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		Design
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
		<li class="active">Design</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- form -->
		<?php echo form_open_multipart(($design['design_no']!=NULL && $design['design_no']>0 ?'admin_ret_catalog/ret_design/update/'.$design['design_no']:'admin_ret_catalog/ret_design/save')); ?>
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
			<h3 class="box-title">Create Design</h3>
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
							
							<div class='row'>							       
						       <!-- <div class='col-sm-3'>
						            <div class='form-group'>
						                <label for="product_name" data-toggle="tooltip">Select Product<span class="error"> *</span></label>
						                  <select multiple id="multi_prod_sel" class="form-control" required="true" style="width:100%;"></select>
									       <input id="product"  type="hidden" name="products" data-product='<?php echo $product['pro_id'];?>'>
						            </div>
						        </div>-->						        
							     <div class='col-sm-3'>
							        <div class='form-group'>
						                <label for="short_code">Design Name<span class="error"> *</span></label>		                
						                <input class="form-control" id="design_name" required="true" name="design[design_name]" placeholder="Enter design name" value="<?php echo set_value('"design[design_name]',(isset($design['design_name'])?$design['design_name']:"")); ?>" type="text" /> 			
						            </div> 
					            </div>
					            
					              <div class="col-md-3">
							    	<div class="form-group ">
									 <input type="hidden" id="editid_design" value="<?php echo set_value('"design[design_no]',(isset($design['design_no'])?$design['design_no']:"")); ?>"/>
							    		<label>Status</label><br>
							    		<input type="checkbox"  class="design" id="designstatus" data-on-text="ACTIVE" data-off-text="INACTIVE"  value="1" <?php echo $design['design_status'] == 1? 'checked="true"':''; ?>>
										 <input type="hidden" id="d_status" name="design[design_status]" value="1">
							    	</div>
						    	</div>


					            <?php if($design['design_no']!=NULL){	?>	        
						        	<div class='col-sm-3'>
								        <div class='form-group'>
							                <label for="short_code"> Design Code</label>							                			
							                <p><?php echo $design['design_no'] ?></p>
							            </div> 
						            </div>
						        <?php }?>
								 <!--<div class="col-sm-4">
							     	 <div class='form-group'>
							                <label for="metal_type">Design Code<span class="error"> *</span></label>
							                <input class="form-control" id="design_code" required="true" name="design[design_code]" placeholder="Enter design code" value="<?php echo set_value('"design[design_code]',(isset($design['design_code'])?$design['design_code']:"")); ?>" type="text" />
							                
							            </div> 
							     </div>	-->
						    </div>
						    
						 
	                        	<div class="box box-default sale_details collapsed-box">
	                        	    <div class="box-header with-border">
            						  <h3 class="box-title">Other Details</h3>
            						  <div class="box-tools pull-right">
            							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            						  </div>
            						</div>
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="length" class="col-md-6">Min Length</label>
                                                    <div class="col-md-6">
                                                        <input id="min_length" class="form-control" type="number" name="design[min_length]" placeholder="Enter min length"  value="<?php echo set_value('design[min_length]',$design['min_length']); ?>"/>
                                                    </div>
                                                </div>													
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="col-md-6">Min Width</label>
                                                        <div class="col-md-6">
                                                            <input id="min_width" class="form-control" type="number" name="design[min_width]" placeholder="Enter min width" value="<?php echo set_value('design[min_width]',$design['min_width']); ?>"/>
                                                        </div>
                                                    </div>																								
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label class="col-md-6">Fixed Rate</label>
                                                    <div class="col-md-6">
                                                        <input id="fixed_rate" class="form-control" type="number" name="design[fixed_rate]" placeholder="Enter fixed rate"  value="<?php echo set_value('design[fixed_rate]',$design['fixed_rate']); ?>"/>	
                                                    </div>	
                                                </div>																							
                                            </div>	
                                        </div>
                                        <p class="help-block"></p>
                                        <div class="row">
                                            <div class="col-sm-4">	
                                                <div class='form-group'>
                                                    <label for="less_stone_wt" class="col-md-6">Max Length</label>
                                                    <div class="col-md-6">
                                                        <input id="max_length" class="form-control" type="number" name="design[max_length]" placeholder="Enter max length"  value="<?php echo set_value('design[max_length]',$design['max_length']); ?>"/>
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="tag_type" class="col-md-6">Max Width</label>
                                                    <div class="col-md-6">
                                                        <input id="max_width" class="form-control" type="number" name="design[max_width]" placeholder="Enter max width"  value="<?php echo set_value('design[max_width]',$design['max_width']); ?>"/>
                                                    </div>
                                                </div>														
                                            </div>
                                            <div class="col-sm-4">	
                                                <div class='form-group'>
                                                    <label for="less_tax" class="col-md-6">Usage Type</label>
                                                    <div class="col-md-6">
                                                        <input type="radio" id="usage_type" name="design[usage_type]" value="1"  <?php echo $design['usage_type'] == 1? 'checked="true"':''; ?>>Regular
                                                        &nbsp;&nbsp;&nbsp;
                                                        <input type="radio" id="usage_type" name="design[usage_type]" value="2"  <?php echo $design['usage_type'] == 2? 'checked="true"':''; ?>>Function
                                                    </div>
                                                </div>
                                            </div>	
                                        </div>
                                        <p class="help-block"></p>
                                        <div class="row">
                                        <p class="help-block"></p></div>
                                        
                                        <div class="row">
                                            <div class='col-sm-4'>    
                                                <div class='form-group'>
                                                    <label for="net_wt" class="col-md-6">Min Weight</label>
                                                    <div class="col-md-6">
                                                        <input id="min_weight" class="form-control" type="number" name="design[min_weight]" placeholder="Enter min weight"  value="<?php echo set_value('design[min_weight]',$design['min_weight']); ?>"/>	
                                                    </div>  
                                                </div>
                                            </div> 	
                                            <div class="col-sm-4">
                                                <div class='form-group'>
                                                    <label for="other_charges" class="col-md-6">Min Dia</label>
                                                    <div class="col-md-6">
                                                        <input id="min_dia" class="form-control" type="number" name="design[min_dia]" placeholder="Enter min dia"  value="<?php echo set_value('design[min_dia]',$design['min_dia']); ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">	
                                                <div class='form-group'>
                                                    <label for="has_size" class="col-md-6">Other Materials</label>
                                                    <div class="col-md-6">
                                                        <select multiple id="material_sel" class="form-control"></select>
                                                        <input id="material_id" type="hidden" name="materials" data-material='<?php echo $material['material_id'];?>'/>    
                                                    </div>
                                                </div>
                                            </div>
                                        
                                        </div>
                                        <div class="row">
                                        <p class="help-block"></p></div>
                                        
                                        <div class="row">
                                            <div class="col-sm-4">	
                                                <div class='form-group'>
                                                    <label for="has_size" class="col-md-6">Max Weight</label>
                                                    <div class="col-md-6">
                                                        <input id="max_weight" class="form-control" type="number" name="design[max_weight]" placeholder="Enter max weight"  value="<?php echo set_value('design[max_weight]',$design['max_weight']); ?>"/>	
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class='form-group'> 
                                                    <label for="central_exces_duty" class="col-md-6">Max Dia</label>
                                                    <div class="col-md-6">
                                                        <input id="min_dia" class="form-control" type="number" name="design[min_dia]" placeholder="Enter max dia"  value="<?php echo set_value('design[min_dia]',$design['min_dia']); ?>"/>
                                                    </div> 
                                                </div> 	 							            		
                                            </div>
                                            <div class="col-sm-4">	
                                                <div class='form-group'>
                                                    <label for="has_size" class="col-md-6">Purity</label>
                                                    <div class="col-md-6">
                                                        <select multiple id="purity_sel" class="form-control" ></select>
                                                        <input id="pur_id" type="hidden" name="purity" data-purity='<?php echo $purity['pur_id']; ?>'/> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                        <p class="help-block"></p></div> <div class="row">
                                        <p class="help-block"></p></div>
                                        
                                        <div class="row">
                                            <div class="col-sm-4" id="des_screw">
                                                <div class='form-group'> 
                                                    <label for="central_exces_duty" class="col-md-6">Screw Type</label>
                                                    <div class="col-md-6">
                                                        <select id="screw_sel" class="form-control" ></select>
                                                        <input id="screw_id"  type="hidden" name="design[screw_type]" value="<?php echo set_value('design[screw_type]',$design['screw_type']); ?>"/>	
                                                    </div> 
                                                </div> 	 							            		
                                            </div>
                                            <div class="col-sm-4" id="des_hook">	
                                                <div class='form-group'>
                                                    <label for="has_size" class="col-md-6">Hook Type</label>
                                                    <div class="col-md-6">
                                                        <select id="hook_sel" class="form-control"></select>
                                                        <input id="hook_id"  type="hidden" name="design[hook_type]" value="<?php echo set_value('design[hook_type]',$design['hook_type']); ?>"/>	
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4" id="des_hook">	
                                                <div class='form-group'>
                                                    <label for="size" class="col-md-6">Select Size</label>
                                                        <div class="col-md-6">
                                                        <select id="select_size" name="design[id_size]" class="form-control" ></select>
                                                        <input type="hidden" id="id_size" value="<?php echo set_value('design[id_size]',$design['id_size']); ?>" name="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                        <p class="help-block"></p></div> <div class="row">
                                        <p class="help-block"></p></div>
                                        <div class="row">
            							    <div class="col-sm-4">
            							      		<div class='form-group'>
            							                <label for="sales_mode">Select Theme</label>
            										    <p class="help-block"></p>
            							                <select id="theme_sel" class="form-control" style="width:100%;"></select>
            									       <input id="theme_id"  type="hidden" name="design[theme]" value="<?php echo set_value('design[theme]',$design['theme']); ?>"/>
            							            </div>	
            							        </div>
            									<div class="col-sm-4">	
            							            <div class='form-group'>
            							                <label for="design_for">Design For</label>
            										    <p class="help-block"></p>
            							                <input type="radio" id="design_for" name="design[design_for]" value="1" <?php echo $design['design_for'] == 1? 'checked="true"':''; ?>>Male
            										    &nbsp;&nbsp;&nbsp;
            										    <input type="radio" id="design_for" name="design[design_for]" value="2" <?php echo $design['design_for'] == 2? 'checked="true"':''; ?>>Female 
            											&nbsp;&nbsp;&nbsp;
            											<input type="radio" id="design_for" name="design[design_for]" value="3" <?php echo $design['design_for'] == 3? 'checked="true"':''; ?>>Unisex 
            											&nbsp;&nbsp;&nbsp;
            											<input type="radio" id="design_for" name="design[design_for]" value="4" <?php echo $design['design_for'] == 4? 'checked="true"':''; ?>>Kids
            							            </div>
            							        </div>
            							   <div class="col-sm-4"> 
            					    			<div class="form-group">
            					    			<label for="stock_type" data-toggle="tooltip">Select Karigar</label>
            									<p class="help-block"></p>
            					    		   <select multiple id="karigar_sel"  class="form-control" style="width:100%;"> </select>
            					              <input id="karigar" name="karigars" type="hidden" data-karigar='<?php echo $karigar['karigar_id'];?>'>
            					               
            					    			</div>
            							      </div>	
            							      
            							    </div>
            						</div>
	                        	</div>
						
								<div class="row"><p class="help-block"></p></div>
								<div class="row"><p class="help-block"></p></div>
								<div class="row"><p class="help-block"></p></div>
					
                       <!-- <div class="row">
                        	<div class="box box-default sale_details collapsed-box">
    						    <div class="box-header with-border">
        						  <h3 class="box-title">Size information</h3>
        						  <div class="box-tools pull-right">
        							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        						  </div>
        						</div>
        						<div class="box-body">
        						    <button id="add_size_info" type="button" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Size </button>
        						    <div class="col-md-6" id="des_size">
                                        <input  type="hidden" value="0" id="si_increment" />	
                                        <p class="help-block"></p></legend>
                                        <table id="size_detail" class="table table-bordered table-striped text-center">
                                            <thead>
                                                <tr>
                                                <th width="5%">S.No.</th>
                                                <th width="30%">Size</th>	
                                                <th width="10%">Action</th>		
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6" id="des_stone">
                                        <input  type="hidden" value="0" id="s_increment" />	
                                        <p class="help-block"></p></legend>
                                        <table id="stone_detail" class="table table-bordered table-striped text-center">
                                            <thead>
                                                <tr>
                                                <th>S.No.</th>
                                                <th>Stone Name</th>
                                                <th>Pieces</th>	
                                                <th>Action</th>		
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
        						</div>
    						</div>
                        </div>-->			
						   
								  
							  <div class="row">
								  <p class="help-block"></p></div>
									
					   <!-- Dynamic image upload-->
				   <!--<div class="row">
				       <div class="col-sm-12">
					       <input type="hidden" id="imgCount" value="0" /> 
					       <button type="button" id="uploadDesign"  class="btn btn-success pull-right"><i class="fa fa-plus"></i> Add Design</button>
				      </div> 
				      <hr />
				
			       </div>-->   
			       <div class="row" style="display:none;">
				       <div class="col-sm-12">
				       <div class="box box-default">
				        <div class="box-header with-border">
						    <legend><i>Uploaded Images</i></legend>
						</div>
						<p class="help-block">Note : Image size shouldn't exceed <b>1 MB</b>.   Upload <b>.jpg or .png </b>images only.</p> 
						
						  <input id="design_img" name="default_img" accept="image/*" type="file" >
						  
						 <img src="<?php echo $design['default_img'] != ''?base_url('assets/img/designs/'.$design['design_no'].'/'.$design['default_img']) : base_url('assets/img/no_image.png'); ?>" id="design_img_preview" alt="Design Image" width="200" height="200">
					     <button class="btn btn-small remove-btn" id="remove_img" value="<?php echo $design['default_img'] ?>" type="button" title="Remove Image"><i class="fa fa-trash" ></i></button> 
						<?php 
						if(!empty($images)){?>
						 <?php foreach($images as $image){ ?>
									
									<img src="<?php echo base_url('assets/img/designs/'.$design['design_no'].'/'.$image['image']); ?>" alt="Design Image" id="design_img_preview" width="200" height="200"> 
									<button class="btn btn-small remove-btn" value="<?php echo $image['id_image'] ?>" type="button" onclick="delete_design('<?php echo $image['id_image'] ?>');" title="Remove Image"><i class="fa fa-trash" ></i></button> 
						
						<?php } 
						}
						?>
				    	<div id="uploadArea">
				        </div>
				      </div> 
					</div>
				      </div> 
			       </div>    
			     <!-- End of Dynamic image upload-->
							
								  <div class="row">
								   <p class="help-block"></p>
								 <!-- </div>--> </div>
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
