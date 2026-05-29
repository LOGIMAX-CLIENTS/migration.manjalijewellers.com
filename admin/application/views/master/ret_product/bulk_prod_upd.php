  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Bulk Product
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="Active">Bulk Product List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Bulk Product List</h3><span id="total_prod" class="badge bg-green"></span>       
                </div><!-- /.box-header -->
			 <div class="box-body">
                <!-- Alert -->
                <div class="row">
                   
                    <div class="col-md-12"> 
						<div class="col-md-2" style="margin-top: 20px;">
						  <div class="form-group">
						    <div class="input-group">
						        <button class="btn btn-default btn_date_range" id="prod-dt-btn">
							    <span  style="display:none;" id="prod1"></span>
								<span  style="display:none;" id="prod2"></span>
						        <i class="fa fa-calendar"></i> Date range picker
						        <i class="fa fa-caret-down"></i>
						      </button>
						    </div>
						  </div><!-- /.form group -->
						</div>
							                     
						<div class="col-md-2">
							<div class="form-group" >
							  <label>Product</label><br>
							  <select id="filterproduct_sel" class="form-control" style="width:150px;" ><option value="">All</option></select>
							  <input id="product_name" name="product_name" type="hidden" value=""/>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group" >
							  <label>Tax Group</label><br>
							  <select id="filtertax_sel" class="form-control" style="width:150px;" ><option value="">All</option></select>
							  <input id="tax_group_id" name="tax_group_id" type="hidden" value=""/>
						</div>
						</div>
						<div class="col-md-2">
							<div class="form-group" >
							  <label>Status</label><br>
							  <select id="filterprod_status" class="form-control" style="width:150px;" ><option value="">All</option><option value="1">Active</option><option value="0">InActive</option></select>
							  <input id="product_status" name="product_status" type="hidden" value=""/>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-offset-2 col-md-8">
						<div class="box"> 
							<div class="box-body"> 
								<div class="col-md-2">
									<label>Update Product</label>
								</div>
								<div class="col-md-3">
									<div class="form-group"> 
									<select id="up_prod_status" class="form-control" style="width:100px;" >
									<option value=""> -- Status -- </option>
									<option value="1">Active</option>
									<option value="0">InActive</option></select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group" > 
									<select id="up_tax_sel" class="form-control" style="width:100px;" >
									<option value=""> -- Choose -- </option></select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group"> 
									<button type="button"  class="btn btn-primary prod_update" name="update" style="width:100px;">Update</button>							
									</div>
								</div>
							</div>
						</div>
					 </div> 
				 </div> 
				<div class="row"> 
					<div class="col-md-12"> 
						<?php $attributes =	array('id' => 'pro_id', 'name' => 'product_bulk');
	//					 echo form_open_multipart('admin_ret_catalog/bulkprodupdated/update',$attributes);?>					 
						   <div class="bulkproduct">					
						   </div>
	                      <table id="txprod_list" style="width:100% !important" class="table table-bordered table-striped text-center">
	                      <thead>
	                        <tr>
	                          <tr>
	                           <th width="5%"><label class="checkbox-inline"><input type="checkbox" id="select_prodata"  name="select_all" value=""/> &nbsp;&nbsp;All</label></th>
	                          <th>Product</th>
							  <th>Short Code</th>
	                          <th>Tax Group</th>
	                          <th>Status</th>
	                        </tr>                                                        
	                        </tr>
	                      </thead> 

	                   </table> 			 
					   </div>
				   </div>
				     <!-- /.box-body -->
              <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
			  </div>
			  
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      
      
 
<!-- modal -->      
