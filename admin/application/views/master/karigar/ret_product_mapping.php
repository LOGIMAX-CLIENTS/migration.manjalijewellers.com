
 <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Product Mapping List
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Product Mapping List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
                
           
              <div class="box box-primary">
              <div class="box-header with-border">
                  <h3 class="box-title">Product Mapping List</h3> 
                          
                </div><!-- /.box-header -->
				
                <div class="box-body">
                <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">  
                                <div class="box box-primary">  
                                    <div class="box-body"> 
                                        <div class="box-header with-border">
                                          <h3 class="box-title">Mapping Details</h3> 
                                        </div><!-- /.box-header -->
                                        <div class="row">
                                            <div class="col-md-4"> 
                                                <div class="form-group tagged">
                                                    <label>Select Product</label>
                                                    <select id="select_product" class="form-control" style="width:100%;"></select>
                                                </div> 
                                            </div>
                                            <div class="col-md-4"> 
                                                <div class="form-group tagged">
                                                    <label>Select Design</label>
                                                    <select id="select_design" class="form-control" style="width:100%;"></select>
                                                </div> 
                                            </div>
                                            <div class="col-md-4"> 
                                                <div class="form-group tagged">
                                                    <label>Select Karigar</label>
                                                    <select id="karigar_sel" class="form-control" style="width:100%;" multiple></select>
                                                </div> 
                                            </div>
                                            <div class="col-md-2"> 
                                                <label></label>
                                                    <div class="form-group">
                                                        <button type="button" id="update_product_mapping" class="btn btn-info">Update</button>   
                                                    </div>
                                            </div>
                                            <div class="col-md-2"> 
                                                <label></label>
                                                    <div class="form-group">
                                                        <button type="button" id="delete_product_mapping" class="btn btn-danger">Delete</button>   
                                                    </div>
                                            </div>
                                        </div>
                                     
                                    </div>
                                </div> 
                            </div>
                            
                            <div class="col-md-6">  
                                <div class="box box-primary">  
                                    <div class="box-body"> 
                                         <div class="box-header with-border">
                                          <h3 class="box-title">Filter Details</h3> 
                                        </div><!-- /.box-header -->
                                        <div class="row">
                                            <div class="col-md-4"> 
                                                <div class="form-group tagged">
                                                    <label>Select Product</label>
                                                    <select id="prod_filter" class="form-control" style="width:100%;"></select>
                                                </div> 
                                            </div>
                                            <div class="col-md-4"> 
                                                <div class="form-group tagged">
                                                    <label>Select Design</label>
                                                    <select id="select_design_fitler" class="form-control" style="width:100%;" ></select>
                                                </div> 
                                            </div>
                                            <div class="col-md-4"> 
                                                <div class="form-group tagged">
                                                    <label>Select Karigar</label>
                                                    <select id="select_karigar_filter" class="form-control" style="width:100%;" ></select>
                                                </div> 
                                            </div>
                                            <div class="col-md-2"> 
                                                <label></label>
                                                    <div class="form-group">
                                                        <button type="button" id="search_karigar_products" class="btn btn-success">Search</button>   
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                   </div> 
                
                 <div class="table-responsive">
                  <table id="subdesign_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>
                        <th>Karigar</th>
                        <th>Product</th>
                        <th>Design</th>
                        <th>Action</th>
                      </tr>
                 	</thead>
                 
                  </table>
                  </div> 
                 
                </div><!-- /.box-body -->
                 <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

<!-- modal -->      
<div class="modal fade" id="confirm-delete"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Design Mapping</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this Design Mapping ?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  
