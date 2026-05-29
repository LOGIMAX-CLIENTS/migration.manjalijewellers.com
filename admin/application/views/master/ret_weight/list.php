  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Weight Range
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Weight Range List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Weight Range List</h3>    <span id="total_weights" class="badge bg-green"></span>      
                           <!-- <a class="btn btn-success pull-right" id="add_wt" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a>  -->
                           <a class="btn btn-success pull-right" id="add_wt"  href="<?php echo base_url('index.php/admin_ret_catalog/weight/new');?>"  ><i class="fa fa-user-plus"></i> Add</a>

                          </div><!-- /.box-header -->
                <div class="box-body">

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Filter Product By</label>
                                        <select id="sel_prod" class="form-control">
                                        <input type="hidden" id="product" value=''>
                                        </select>
                                    </div>
                                </div>
								<div class="row">
									<div class="col-sm-10 col-sm-offset-1">
									</div>
								</div>				
                  <div class="table-responsive">
                  <table id="weight_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <!--<th>Design</th>
                        <th>Sub Design</th>-->
                        <th>Weight Range Name</th>
                        <th>From Weight</th>
                        <th>To Weight</th>
                        <th>Created by</th>
                        <th>Created date</th>
                        <th>Updated by</th>
                        <th>Updated date</th>
                        <th>Action</th>
                      </tr>
                 </thead>
                </table>
                  </div>
                </div><!-- /.box-body -->
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
        <h4 class="modal-title" id="myModalLabel">Delete Weight</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this weight record?</strong>
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
<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Weight Range</h4>
      </div>
  <div class="modal-body">
               
                	<div id="chit_alert"></div>
									 
                <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Select Product<span class="error">*</span></label>
                       <div class="col-md-4">
                       	    <select id="weight_prod" class="form-control" style="width:100%;"></select>
                       	    <input type="hidden" id="product" value=''>
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>
                
                <!--<div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Select Design<span class="error"></span></label>
                       <div class="col-md-4">
                       	    <select id="weight_design" class="form-control" style="width:100%;"></select>
                       	    <input type="hidden" id="id_design" value=''>
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>
                
                 <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Select Sub Design<span class="error"></span></label>
                       <div class="col-md-4">
                       	    <select id="weight_sub_design" class="form-control" style="width:100%;"></select>
                       	    <input type="hidden" id="id_sub_design" value=''>
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>-->
                
                <!-- <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Units<span class="error">*</span></label>
                       <div class="col-md-4">
                       	    <select id="uom" id="uom" style="width:100%;"></select>
                            <p class="help-block"></p>
                       </div>
                    </div> -->
                </div><p class="help-block"></p>
                
                 <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Value<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="number" class="form-control" id="name" name="name" placeholder="Weight Range Value" autocomplete="off"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>
                
                <div class="row">
				 	<div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">From Weight<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="number" step="any" class="form-control" id="from_weight" name="from_weight" placeholder="Enter From Weight"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
				 </div><p class="help-block"></p>
				 <div class="row">
				     <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">To Weight<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="number" step="any" class="form-control" id="to_weight" name="to_weight" placeholder="Enter To Weight"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 <div class="row">
				     <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">To Description<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="weight_desc" name="weight_desc" placeholder="Enter Description"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="add_weight" class="btn btn-success">Save and New</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Weight Range</h4>
      </div>
      <div class="modal-body">
                 <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Value<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_name" name="ed_name" placeholder="Weight Range Name" autocomplete="off"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
                <!-- </div><p class="help-block"></p>
                 <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Units<span class="error">*</span></label>
                       <div class="col-md-4">
                       	    <select  id="ed_uom" style="width:100%;"></select>
                       	    <input type="hidden" id="id_uom">
                            <p class="help-block"></p>
                       </div>
                    </div> -->
                </div><p class="help-block"></p>
                <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Select Product<span class="error">*</span></label>
                       <div class="col-md-4">
                       	    <select id="ed_wt_range_prod" class="form-control" style="width:100%;"></select>
                       	    <input type="hidden" id="ed_product">
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>
                
                  <!--<div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Select Design<span class="error">*</span></label>
                       <div class="col-md-4">
                       	    <select id="ed_wt_range_des" class="form-control" style="width:100%;"></select>
                       	    <input type="hidden" id="ed_wt_des">
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>
                
                <div class="row">
                    <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">Select Sub Design<span class="error">*</span></label>
                       <div class="col-md-4">
                       	    <select id="ed_wt_range_sub_des" class="form-control" style="width:100%;"></select>
                       	    <input type="hidden" id="ed_wt_sub_des">
                            <p class="help-block"></p>
                       </div>
                    </div>
                </div><p class="help-block"></p>-->
                
                <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-4 col-md-offset-1 ">From Weight<span class="error">*</span></label>
                       <div class="col-md-4">
                       <input type="hidden" id="edit-id" value="" />
                       	 <input type="number" step="any" class="form-control" id="ed_from_weight" name="weight"  placeholder="Enter From Weight"> 
                       </div>
                    </div>
				 </div>   <p class="help-block"></p> 
				 <div class="row">
				     <div class="form-group">
                       <label for="scheme_code" class="col-md-4 col-md-offset-1 ">To Weight<span class="error">*</span></label>
                       <div class="col-md-4">
                       <input type="hidden" id="edit-id" value="" />
                       	 <input type="number" step="any" class="form-control" id="ed_to_weight" name="weight"  placeholder="Enter To Weight"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 
				 <div class="row">
				     <div class="form-group">
                       <label for="" class="col-md-4 col-md-offset-1 ">To Description<span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_weight_desc" name="weight_desc" placeholder="Enter Description"> 
                            <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_weight" class="btn btn-success" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

