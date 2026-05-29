      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Category
            <small>Add Category</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Category</a></li>
            <li class="active">Add Category</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Category</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div>
	             <!-- form -->
				
				 
				
				<?php echo form_open_multipart(( $category['id_category']!=NULL && $category['id_category']>0 ?'catalog/category/update/'.$category['id_category']:'catalog/category/save')); ?>
                   <div class="row">
				 	<div class="form-group">
                       <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Category Name</label>
                       <div class="col-md-4">
                       	<input type="text" class="form-control" id="category_name" name="category[category_name]" value="<?php echo set_value('category[category_name]',isset($category['category_name']) ? $category['category_name'] : ''); ?>"/>
                        <p class="help-block"></p>
                        <input type="hidden" class="form-control" id="catagory_id" value="<?php echo set_value('category[id_category]',isset($category['id_category']))? $category['id_category'] : ''; ?>"/>
							</div>                       	
                       </div>
                    </div>
				 </div>
			 				
				 
			 				
				  <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Category Description</label>
					   <div class="col-md-4">
						 <textarea class="form-control" id="description" name="category[description]"><?php echo set_value('category[description]',isset($category['description']) ? $category['description'] : ''); ?></textarea>			   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>
				 
				 <div class="row">
					<div class="form-group">
					   <label for="Offer" class="col-md-3 col-md-offset-1 ">Status*</label>
					   <div class="col-md-4">
						 <div class="col-md-3">
								<input type="radio" id="select_options" name="category[active]" value="1"  <?php if($category['active']==1) { ?> checked="true" <?php } ?>  > Active
						</div>
						<div class="col-md-3">						
							<input type="radio" id="select_options"  name="category[active]" value="0"  <?php if($category['active']==0) { ?> checked="true" <?php } ?>> In Active							
						</div>						
						<p class="help-block"></p>
						
					   </div>
					</div>
				</div>
				
				 <div class="row">
				    <div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload Category image</label>
					   <div class="col-md-6">
							 <input id="catimage" name="category[catimage]" accept="image/*" type="file" >
							 <img src="<?php echo(isset($category['catimage'])?$category['catimage']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="catimage_preview" style="width:304px;height:100%;" alt="Offer image"> 
                            							 
						<p class="help-block"></p>
					 </div>
				
				     </div>	
				</div>
				
				
				 <div class="row">				 
			                <div class="form-group">
									<label for="parent_name" class="col-md-3 col-md-offset-1 ">Parent category*</label>
									<div class="col-md-6">
										<?php
											$model_name = 'Catalog_model';
											//echo $this->$model_name->CategoryList($catagory['id_parent']);
											echo $this->$model_name->CategoryList($category['id_parent']);
										
										
										?>	
									</div>
									<input type="hidden" class="form-control" id="id_parent" name="category[id_parent]" value="<?php echo set_value('$category[id_parent]',isset($category['id_parent']) ? $category['id_parent'] : ''); ?>"/>
							</div>
							
				 </div>
				 
			    
	            </div>
	            
				<br /> 
			     <div class="row"> 
					  <div class="col-xs-offset-5">
						<button type="submit"  class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
						
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
