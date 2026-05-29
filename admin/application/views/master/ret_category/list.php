  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Category
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Category List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Category List</h3>    <span id="total_category" class="badge bg-green"></span>      

				  <?php if($access['add']==1){?>
                           <a class="btn btn-success pull-right" id="add_category" href="#"data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                <?php }?>
						</div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?> 
				<div class="col-md-2" style="margin-top: 20px;">
				<!-- Date and time range -->
				<div class="form-group">
				<div class="input-group">
				<button class="btn btn-default btn_date_range" id="category_date">
				<!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
				<span  style="display:none;" id="category1"></span>
				<span  style="display:none;" id="category2"></span>
				<i class="fa fa-calendar"></i> Date range picker
				<i class="fa fa-caret-down"></i>
				</button>
				</div>
				</div><!-- /.form group -->
				</div>				
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert" style="position: fixed; top: 50px; right: 20px; z-index: 10000; width: auto; min-width: 300px;"></div>
					</div>
				  </div>
						
                  <div class="table-responsive">
                  <table id="categorymtr_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
						<th>Category Name</th>
						<th>Short Code</th>
						<th>Metal Name</th>
						<th>Multi Metal</th>
						<th>Status</th>
                        <th>Action</th>
                      </tr>
                 	</thead>
                 
                  </table>
                  </div> <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

<!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Category</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this Category record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  
<!-- modal -->      
<div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Category </h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div>
				
				 <div class="row">
					<div class="form-group">
						<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
	                      <div class="col-md-5">
	                      	<input type="checkbox" class="status" id="ad_category_status" name="ad_category_status" data-on-text="YES" data-off-text="NO" checked="true"/>
						   <input type="hidden" id="add_category_status" value="1">
						  </div>    
					</div>
				</div>
				<p class="help-block"></p>
				<div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Category Name
					   <span class="error">*</span></label>
                       <div class="col-md-5">
                       	 <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter Category name" required="true"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">HSN Code
					   <span class="error">*</span></label>
                       <div class="col-md-5">
                       	 <input type="text" class="form-control" id="hsn_code" name="hsn_code" placeholder="Enter HSN Code" required="true"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				  <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Code
					   <span class="error">*</span></label>
                       <div class="col-md-5">
                       	 <input type="text" class="form-control" id="cat_code" name="cate_code" placeholder="Enter Category code"> 
                	  <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Category Type
					   <span class="error">*</span></label>
					   <div class="col-md-6">
							<input type="radio" value="1" name="category_type" id="ornament" checked /> <label for="ornament">Ornament</label>
							<input type="radio" value="2" name="category_type" id="bullion" /> <label for="bullion">Bullion</label>
							<input type="radio" value="3" name="category_type" id="stone" /> <label for="stone">Stone</label>
							<input type="radio" value="4" name="category_type" id="alloy" /> <label for="alloy">Alloy</label>
                      </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Is Multi Metal
					   <span class="error">*</span></label>
					   <div class="col-md-6">
							<input type="radio" value="1" name="is_multimetal" id="multimetal_yes" /> <label for="multimetal_yes">Yes</label>
							<input type="radio" value="0" name="is_multimetal" id="multimetal_no" checked /> <label for="multimetal_no">No</label>
                      </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Metal
					   <span class="error">*</span></label>
					   <div class="col-md-6">
						<select id="metal_category" class="form-control"></select>
						<input id="id_metal_category" name="metal" type="hidden" />
                      </div>
                    </div>
				 </div>
				<p></p>
				
				<div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Tax Group
					   <span class="error">*</span></label>
					   <div class="col-md-4">
						<select id="tgrp_sel" class="form-control"></select>
						<input id="tgrp_id" name="tgrp_id" type="hidden" />
                      </div>
                    </div>
				 </div><p></p>  
				 
				 <div class="row">	
					<div class='form-group'>
						<label for="has_size" class="col-md-3 col-md-offset-1">Purity</label>
					  <div class="col-md-5">
						 <select multiple id="purity_sel" class="form-control" required="true"></select>
						
					 <input id="pur_id" type="hidden" name="purity"/> 
					 <p class="help-block"></p>
					</div></div>
				</div>
				<div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Description
					   </label>
                       <div class="col-md-5">
                       	 <textarea  class="form-control" id="category_desc" name="category_desc" rows="5" cols="100"> </textarea>
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				<p class="help-block"></p>
				<div class="row">
				<div class="form-group">
					<label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload image</label>
						<div class="col-md-6">
							<input id="categorymtr_img" required="true" name="categorymtr_img" accept="image/*" type="file" >	
							<img src="<?php echo(isset($categorymtr['image'])?$categorymtr['image']: base_url().('assets/img/no_image.png')); ?>	" class="img-thumbnail" id="categorymtr_img_preview" style="width:304px;height:100%;" alt="category image">
						<p class="help-block">File size should not exceed 1MB<br/>Image format should be .jpg or .png</p>
						</div>
				</div> 
				</div>
      </div>
      <div class="modal-footer">
		<a href="#" id="add_categorynew" class="btn btn-success" >Save & New</a>
      	<a href="#" id="add_newcategory" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Category </h4>
      </div>
      <div class="modal-body">
	   <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error'></div>
				</div>
		    <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
			</div> 
			
			<div class="row">
				<div class="form-group">
					<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active
					</label>
					<div class="col-md-5">
					<input type="checkbox" class="status" id="ed_category_status" name="ed_category_status" data-on-text="YES" data-off-text="NO" checked="true" />
					<input type="hidden" id="edit_category_status" value="1">
					</div>    
				</div>
			</div>
			<p class="help-block"></p>
			 <div class="row">
			 	<div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Category Name
				   <span class="error">*</span></label>
                   <div class="col-md-5">
                   <input type="hidden" id="edit-id" value="" />
                   	<input type="text" class="form-control" id="ed_category_name" name="ed_category_name"  placeholder="Enter Category Name"> 
					<p class="help-block"></p>	
                   </div>
                </div>
			 </div> 
			 <div class="row">
			 	<div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">HSN Code
				   <span class="error">*</span></label>
                   <div class="col-md-5">
                   <input type="hidden" id="edit-id" value="" />
                   	<input type="text" class="form-control" id="ed_hsn_code" name="ed_hsn_code"  placeholder="Enter HSN COde"> 
					<p class="help-block"></p>	
                   </div>
                </div>
			 </div> 
			 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Code
					   <span class="error">*</span></label>
                       <div class="col-md-5">
                       	 <input type="text" class="form-control" id="ed_cate_code" name="cate_code" placeholder="Enter Category code"> 
                	  <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Category Type
					   <span class="error">*</span></label>
					   <div class="col-md-6">
							<input type="radio" value="1" name="ed_category_type" id="ed_ornament" /> <label for="ornament">Ornament</label>
							<input type="radio" value="2" name="ed_category_type" id="ed_bullion" /> <label for="bullion">Bullion</label>
							<input type="radio" value="3" name="ed_category_type" id="ed_stone" /> <label for="stone">Stone</label>
							<input type="radio" value="4" name="ed_category_type" id="ed_alloy" /> <label for="alloy">Alloy</label>
                      </div>
                    </div>
				</div>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Is Multi Metal
					   <span class="error">*</span></label>
					   <div class="col-md-6">
							<input type="radio" value="1" name="ed_is_multimetal" id="ed_multimetal_yes" /> <label for="ed_multimetal_yes">Yes</label>
							<input type="radio" value="0" name="ed_is_multimetal" id="ed_multimetal_no" checked /> <label for="ed_multimetal_no">No</label>
                      </div>
                    </div>
				 </div>
			 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Metal
					   <span class="error">*</span></label>
					   <div class="col-md-5">
						<select id="metal_category1" class="form-control"></select>
						<input id="id_metal_cate" name="metal" type="hidden" />
						<p class="help-block"></p>	
                      </div>
                    </div>
				 </div> 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Tax Group
					   <span class="error">*</span></label>
					   <div class="col-md-4">
						<select id="ed_tgrp_sel" class="form-control"></select>
						<input id="ed_tgrp_id" name="tgrp_id" type="hidden" />
                      </div>
                    </div>
				 </div>  
				 
				 <div class="row">	
					<div class='form-group'>
						<label for="has_size" class="col-md-3 col-md-offset-1">Purity</label>
					  <div class="col-md-5">
						<select multiple id="ed_purity_sel" class="form-control" required="true"></select>
						 <div id="sel_br" value=""></div> 
					 <input id="ed_pur_id" type="hidden" name="purity"/>
					<p class="help-block"></p>					 
					</div></div>
				</div>
				 <div class="row">   
                <div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Description
				   </label>
                   <div class="col-md-5">
                   	 <textarea rows="5" cols="100" class="form-control" id="ed_category_desc" name="ed_category_desc" placeholder="Enter Category description"> </textarea>
					<p class="help-block"></p>
                   </div>
                </div>
			 </div>
			<p class="help-block"></p>
			<div class="row">
				<div class="form-group">
					<label for="scheme_code" class="col-md-3 col-md-offset-1">Upload image</label>
					<div class="col-md-6"> 
						<input id="ed_categorymtr_img" required="true" name="ed_categorymtr_img" accept="image/*" type="file" >
						<img src="" class="img-thumbnail" id="ed_categorymtr_img_preview" style="width:304px;height:100%;" alt="category image"> 							
					<p class="help-block">File size should not exceed 1MB<br/>Image format should be .jpg or .png</p>
					</div>
				</div> 
				</div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_category" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

