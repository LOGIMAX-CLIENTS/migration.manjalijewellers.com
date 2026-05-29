      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Product
            <small>Add Product</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
            <li><a href="#">Product</a></li>
            <li class="active">Add Product</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Product</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div>
	             <!-- form -->
				<?php echo form_open_multipart(( $product['id_product']!=NULL && $product['id_product']>0 ?'catalog/product/update/'.$product['id_product']:'catalog/product/save')); ?>
                   <div class="row">
							<div class="form-group col-md-6">
							      <label for="chargeseme_name" class="col-md-3 col-md-offset-3 ">Product Name</label>
							      <div class="col-md-6">
								    <input type="text" class="form-control" id="product_name" name="product[product_name]" placeholder="Enter Product Name" value="<?php echo set_value('product[product_name]',$product['product_name']);  ?>" />
								     <p class="help-block"></p>
								     <input type="hidden" class="form-control" id="product_id" value="<?php echo set_value('product[id_product]',$product['id_product']); ?>"/>
								   </div>                       	
							  </div>
							<div class="form-group col-md-6">
							      <label for="chargeseme_name" class="col-md-3">Product Code</label>
							      <div class="col-md-6">
								    <input type="text" style="text-transform: uppercase;" onkeypress="return /^[a-zA-Z0-9-]$/i.test(event.key)" class="form-control" id="product_code" required="true" name="product[code]" placeholder="Enter Product Code" value="<?php echo set_value('product[code]',$product['code']); ?>"/>
								     <p class="help-block"></p>
								   </div>                       	
							  </div>
                    </div>
				 
				 <div class="row">
					<div class="form-group col-md-6">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-3">Weight</label>
					   <div class="col-md-6">
								    <input type="number" class="form-control" id="weight" name="product[weight]" step="0.01"  placeholder="Enter Product Weight"value="<?php echo set_value('product[weight]',$product['weight']); ?>"/>
						</div> 
					</div>
					<div class="form-group col-md-6">
							      <label for="chargeseme_name" class="col-md-3">Size</label>
							      <div class="col-md-6">
								    <input type="number" class="form-control" id="product_size" name="product[size]" step="0.01" placeholder="Enter Product Size" value="<?php echo set_value('product[size]',$product['size']); ?>"/>
								     
								   </div>                       	
							  </div>
				 </div>
				 <div class="row">
					<div class="form-group col-md-6">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-3">Purity</label>
					   <div class="col-md-6">
								    <input type="number" class="form-control" id="purity" name="product[purity]" step="0.01" placeholder="Enter Product Purity" value="<?php echo set_value('product[purity]',$product['purity']); ?>"/>
								    
						</div> 
					</div>
					<div class="form-group col-md-6">
							      <label for="chargeseme_name" class="col-md-3">price</label>
							      <div class="col-md-6">
								    <input type="number" class="form-control" id="price" name="product[price]"  placeholder="Enter Product Price" value="<?php echo set_value('product[price]',$product['price']); ?>"/>
								   </div>                       	
							  </div>
				 </div>
				  <div class="row">
					<div class="form-group col-md-6">
					   <label for="Offer" class="col-md-3 col-md-offset-3">Status*</label>
					   <div class="col-md-6">
						 <div class="col-md-5">
								<input type="radio" id="select_options" name="product[active]" value="1"  <?php if($product['active']==1) { ?> checked="true" <?php } ?>  > Active
						</div>
						<div class="col-md-5">						
							<input type="radio" id="select_options"  name="product[active]" value="0"  <?php if($product['active']==0) { ?> checked="true" <?php } ?>> In Active							
						</div>						
						<p class="help-block"></p>
						
					   </div>
					</div>
					
			
					<div class="form-group col-md-6">
					   <label for="chargeseme_name" class="col-md-3">Upload Product image</label>
					   <div class="col-md-6">
							 <input id="product_img" name="product[product_img]" accept="image/*" type="file" >
							 <img src="<?php echo(isset($product['proimage'])?$product['proimage']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="product_img_preview" style="width:304px;height:100%;" alt="Offer image"> 
                            							 
						<p class="help-block"></p>
					 </div>
				
				     </div>	 
					 
				</div>
				 <div class="row">				 
			                <div class="form-group col-md-6">
									<label for="parent_name" class="col-md-3 col-md-offset-3">Choose Category*</label>
									<div class="col-md-6">
										<?php
											$model_name = 'Catalog_model';
											echo $this->$model_name->CategoryList($product['id_parent']);
										?>	
									</div>
									<input type="hidden" class="form-control" id="id_parent" name="product[id_category]" value="<?php echo set_value('$product[id_category]',$product['id_category']); ?>"/>
							</div>
				 </div><br/>
				 <div class="row">
					<div class="form-group col-md-6">
					   <label for="chargeseme_name" class="col-md-3">Product Description</label>
					   <div class="col-md-6">
						 <textarea class="form-control" id="description" name="product[description]" placeholder="Enter Product Description"><?php echo set_value('product[description]',$product['description']); ?></textarea>			   
						<p class="help-block"></p>
						
					   </div>
					</div>
					<div class="form-group col-md-6">
							      <label for="chargeseme_name" class="col-md-3">Type</label>
							      <div class="col-md-6">
								    <input type="text" class="form-control" id="type" name="product[type]"  placeholder="Enter Product Type" value="<?php echo set_value('product[type]',$product['type']); ?>"/>
								   </div>                       	
							  </div>
				 </div>
			     <div class="row">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit"  class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
					  </div> <br/>
					</div>
				  </div> 
			    
	            </div>  
	          <?php echo form_close();?>
	             <!-- /form -->
	          </div>
             <!-- /form container -->
            </div><!-- /.box-body -->
             <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>

        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
