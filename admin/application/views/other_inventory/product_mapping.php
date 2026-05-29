

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

            <li class="active">Product Mapping List List</li>

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

                                                    <label>Select Item</label>

                                                    <select id="select_item" class="form-control" style="width:100%;" ></select>

                                                </div> 

                                            </div>

                                            

                                            <div class="col-md-4"> 

                                                <div class="form-group tagged">

                                                    <label>Select Product</label>

                                                    <select id="select_product" class="form-control" style="width:100%;" multiple></select>

                                                </div> 

                                            </div>

                                            

                                            <div class="col-md-2"> 

                                                <label></label>

                                                    <div class="form-group">

                                                    <?php if($access['edit']==1){?>

                                                        <button type="button" id="update_product_mapping" class="btn btn-info">Update</button>   


                                                        <?php }?>
                                                    </div>

                                            </div>

                                        </div>

                                        <div class="row" id="delete_row">

                                            <div class="col-md-2"> 

                                                <label></label>

                                                    <div class="form-group">

                                                    <?php if($access['delete']==1){?>

                                                        <button type="button" id="delete_product_mapping" class="btn btn-danger">Delete</button>   

                                                        <?php }?>
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

                                                    <label>Select Item</label>

                                                    <select id="item_filter" class="form-control" style="width:100%;" ></select>

                                                </div> 

                                            </div>

                                            <div class="col-md-2"> 

                                                <label></label>

                                                    <div class="form-group">

                                                        <button type="button" id="search_design_maping" class="btn btn-success">Search</button>   

                                                    </div>

                                            </div>

                                        </div>

                                    </div>

                                </div> 

                            </div>

                        </div>

                   </div> 

                

                 <div class="table-responsive">

                  <table id="mapping_list" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th>#</th>

                        <th>Product</th>

                        <th>Item</th>

                        <th>Size</th>

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

<!-- modal -->      

<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Add Sub Design</h4>

      </div>

         <div class="modal-body">

             <div id="error-msg"></div>

                  <form id="myform">

                         <div class="row">

                            <div class="form-group">

                                <label for="" class="col-md-4 col-md-offset-1">Sub Design</label>

                                <div class="col-md-4">

                                    <input type="text" class="form-control" id="sub_design_name" name="sub_design_name" placeholder="Enter Sub Design">

                                </div></br>

                            </div>

                        </div><p></p>

                         <div class="row">

                            <div class="form-group">  

            					<label for="" class="col-md-4 col-md-offset-1">Short Code</label>

            					<div class="col-md-4">

                                    <input type="text" class="form-control" id="sub_design_code" name="sub_design_code" placeholder="Enter Short Code">

                                </div></br>

                            </div>

                        </div><p></p>

                        <div class="row">

                            <div class="form-group">     

                                <label for="scheme_code" class="col-md-4 col-md-offset-1">Status</label>

                                    <div class="col-md-4">

                                    <input type="checkbox" class="status" id="sd_status" name="ad_status" data-on-text="YES" data-off-text="NO" value="1" checked="true"/>

                                    <input type="hidden" id="sub_des_status" value="1">

                                </div> 

                            </div>

                        </div><p></p>

                    </form>

        </div>

      <div class="modal-footer">

      	<a href="#" id="add_subdesign" class="btn btn-success" >Save & Close</a>

      	<a href="#" id="add_new_subdesign" class="btn btn-success" >Save & New</a>

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

        <h4 class="modal-title" id="myModalLabel">Edit Sub Design</h4>

      </div>

        <div class="modal-body">

            <div class="row" >

                <div class="col-md-offset-1 col-md-10" id='error_message'></div>

            </div>

            <div class="row">

                <div class="form-group">

                    <label for="" class="col-sm-4 col-md-offset-1 ">Sub Design<span class="error">*</span></label>

                        <div class="col-sm-4">

                            <input type="hidden" id="edit-id" value="" />

                            <input type="text" id="ed_sub_design_name" class="form-control" placeholder="Enter Sub design">

                        </div></br>

                </div>

            </div><p></p>

            <div class="row">

                <div class="form-group">           

					<label for="" class="col-sm-4 col-md-offset-1 ">Short Code<span class="error">*</span></label>

					     <div class="col-sm-4">

                            <input type="text" id="ed_sub_design_code" class="form-control" placeholder="Enter Short code">

                        </div></br>

                </div>

            </div><p></p>

            <div class="row">

                <div class="form-group">  

					<label for="scheme_code" class="col-md-4 col-md-offset-1">Status</label>

                    <div class="col-md-4">

                    <input type="checkbox" class="status" id="ed_sub_status" name="ad_status" data-on-text="YES" data-off-text="NO" value="1"/>

                    <input type="hidden" id="ed_sd_status" value="">

					</div> 

				</div>

			</div>

        </div>

      <div class="modal-footer">

      	<a href="#" id="update_subdesign" class="btn btn-success" >Update</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      





