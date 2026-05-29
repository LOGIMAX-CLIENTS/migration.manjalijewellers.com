  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Product Grouping
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Product Grouping</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Product Grouping List</h3>    <span id="total_category" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="add_productgrp" href="#"data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
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
				<button class="btn btn-default btn_date_range" id="productgrp_date">
				<!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
				<span  style="display:none;" id="productgrp1"></span>
				<span  style="display:none;" id="productgrp2"></span>
				<i class="fa fa-calendar"></i> Date range picker
				<i class="fa fa-caret-down"></i>
				</button>
				</div>
				</div><!-- /.form group -->
				</div>				
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					</div>
				  </div>
						
                  <div class="table-responsive">
                  <table id="product_group_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Metal Name</th>
                        <th>Product Group Name</th>
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
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Product Grouping</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this  record?</strong>
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
<div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Product Grouping </h4>
      </div>
      <div class="modal-body">
	    <div class="row" >
				<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
			</div>
				
				
				<div class="row">
          <div class="form-group" style="display:block">
                  <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Metal
                  <span class="error">*</span></label>
                    <div class="col-md-5">
                      <select id="metal_prodgrp" class="form-control" style="width:80%"></select>
                      <input id="id_metal_prodgrp" name="metal" type="hidden" />
                    </div>
                    <p class="help-block"></p><br>
                </div>
                <p class="help-block"></p><br>
				      	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Group Name
					              <span class="error">*</span></label>
                       <div class="col-md-5">
                       	 <input type="text" class="form-control" id="product_grpname" name="product_grpname" placeholder="Enter Group name" required="true"> 
                	      <p class="help-block"></p>
                   </div>
                </div>
				        </div>
                <p class="help-block"></p>
                <div class="row">
				          	<div class="form-group">
						          <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
	                      <div class="col-md-5">
	                      	<input type="checkbox" class="status" id="ad_pro_grp_status" name="ad_pro_grp_status" data-on-text="YES" data-off-text="NO" checked="true"/>
						              <input type="hidden" id="add_pro_grp_status" value="1">
						            </div>    
				          	</div>
                   </div>
                
                 
			     </div>
           <div class="modal-footer">
      	    <a href="#" id="add_pro_groupnew" class="btn btn-success" data-dismiss="modal">Save</a>
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
        <h4 class="modal-title" id="myModalLabel">Edit Product Grouping </h4>
      </div>
      <div class="modal-body">
	   <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error'></div>
				</div>
		    <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
			</div> 
			
			<div class="row">
      <div class="form-group" style="display:block">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Metal
              <span class="error">*</span></label>
              <div class="col-md-4">
                <select id="metal_prodgrp1" class="form-control"></select>
                <input id="id_metal_prodgrp1" name="metal" type="hidden" />
              </div>
              <p class="help-block"></p><br>
            </div>
            <p class="help-block"></p><br>
				 	<div class="form-group">
            <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Group Name
					   <span class="error">*</span></label>
                       <div class="col-md-5">
                       	 <input type="text" class="form-control" id="ed_product_grpname" name="ed_product_grpname" placeholder="Enter Category name" required="true"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				    </div>
            <p class="help-block"></p>
          
          <div class="row">
					<div class="form-group">
						<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
	                      <div class="col-md-5">
	                      	<input type="checkbox" class="status" id="ed_pro_grp_status" name="ed_pro_grp_status" data-on-text="YES" data-off-text="NO" checked="true"/>
						   <input type="hidden" id="edit_pro_grp_status" value="1">
						  </div>    
					</div>
				</div>
		</div>

      <div class="modal-footer">
      	<a href="#" id="update_productgrp" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

