      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Master
            <small>Sub-category</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">sub-category</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Sub-category</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
             <!-- form container -->
              <div>
	             <!-- form -->
				<?php echo form_open_multipart(( $sub_category['id_subcategory']!=NULL && $sub_category['id_subcategory']>0 ?'sub_category/update/'.$sub_category['id_subcategory']:'sub_category/save')); ?>
				<?php if(isset($sub_category['id_subcategory'])) { ?>
				<div class="row">
					<div class="form-group">
					   <label for="sub_category" class="col-md-3 col-md-offset-1 ">Status</label>
					   <div class="col-md-6">
						 <input type="checkbox" id="status" class="switch" data-on-text="Active" data-off-text="Inactive" name="sub_category[status]" value="1" <?php if($sub_category['status']==1) { ?> checked="true" <?php } ?>/>	   
						<p class="help-block"></p>
						
					   </div>
					</div>
			    	
			    </div>
				<?php } ?>
			    
				<div class="row">
					<div class="form-group">
					   <label for="sub_category" class="col-md-3 col-md-offset-1 ">Category</label>
					   <div class="col-md-6">
						 <select id="category_select" class="form-control"></select>
								<input id="id_category" name="sub_category[id_category]" type="hidden" value="<?php echo set_value('sub_category[id_category]',$sub_category['id_category']); ?>" />	   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>
				 	 
				 <div class="row">
					<div class="form-group">
					   <label for="sub_category" class="col-md-3 col-md-offset-1 ">Sub Category</label>
					   <div class="col-md-6">
						 <input class="form-control" id="name" name="sub_category[name]" type="text" required="true" placeholder="Sub-Category Name" autofocus="true" value="<?php echo set_value('sub_category[name]',$sub_category['name']); ?>" />	
						  <input class="form-control"  name="sub_category[id_subcategory]" type="hidden" value="<?php echo set_value('sub_category[id_subcategory]',$sub_category['id_subcategory']); ?>" />		   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	 
				 
				 <div class="row">
					<div class="form-group">
					   <label for="sub_category" class="col-md-3 col-md-offset-1 ">Description</label>
					   <div class="col-md-6">
						<textarea class="form-control" id="description" name="sub_category[description]"><?php echo set_value('sub_category[description]',$sub_category['description']); ?></textarea>	   
						<p class="help-block"></p>
						
					   </div>
					</div>
				 </div>	 
				
			    <div class="row">
					<div class="form-group">
					   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Upload image</label>
					   <div class="col-md-6">
							 <input id="sub_category_img" name="sub_category_img" accept="image/*" type="file" >
							 <p class="help-block">File size should not exceed 1MB<br/>Image format should be .jpg or .png</p>
							 <img src="<?php echo($sub_category['image']!= NULL ? base_url().('assets/img/sub_category/').$sub_category['image']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="sub_category_img_preview" style="width:304px;height:100%;" alt="sub_category image"> 
							  <input  name="sub_category[image]" type="hidden" value="<?php echo set_value('sub_category[image]',$sub_category['image']); ?>"> 
							 
						<p class="help-block"></p>
						
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
