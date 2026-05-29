 <style type="text/css">

  .ord_img

  {

    padding:5px 10px;

    background:#605CA8;

    border:1px solid #605CA8;

    position:relative;

    color:#fff;

    border-radius:2px;

    text-align:center;

    float:left;

    cursor:pointer;

  }

  .order_images_new{

    position: absolute;

    z-index: 1000;

    opacity: 0;

    cursor: pointer;

    right: 0;

    top: 0;

    height: 100%;

    font-size: 24px;

    width: 100%;

  }

</style>

 <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Sub Design Mapping List

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Sub Design Mapping List </li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

              <div class="box-header with-border">

                  <h3 class="box-title">Sub Design Mapping List</h3> 
                  <span id="total_count" class="badge bg-green"></span>

                          

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

                                            <div class="col-md-3"> 

                                                <div class="form-group tagged">

                                                    <label>Select Product</label>

                                                    <select id="select_product" class="form-control" style="width:100%;"></select>

                                                </div> 

                                            </div>

                                            <div class="col-md-3"> 

                                                <div class="form-group tagged">

                                                    <label>Select Design</label>

                                                    <select id="select_design" class="form-control" style="width:100%;" ></select>

                                                </div> 

                                            </div>

                                            

                                            <div class="col-md-3"> 

                                                <div class="form-group tagged">

                                                    <label>Select Sub Design</label>

                                                    <select id="sub_design_sel" class="form-control" style="width:100%;" multiple></select>

                                                </div> 

                                            </div>

                                           
                                            <?php if ($access['add']) { ?>
                                            <div class="col-md-2"> 

                                                <label></label>

                                                    <div class="form-group">

                                                        <button type="button" id="update_sup_design_mapping" class="btn btn-info">Add</button>   

                                                    </div>

                                            </div>
                                            <?php } ?>
                                           

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

                                            <div class="col-md-3"> 

                                                <div class="form-group tagged">

                                                    <label>Select Product</label>

                                                    <select id="prod_filter" class="form-control" style="width:100%;"></select>

                                                </div> 

                                            </div>

                                            <div class="col-md-3"> 

                                                <div class="form-group tagged">

                                                    <label>Select Design</label>

                                                    <select id="select_design_fitler" class="form-control" style="width:100%;" ></select>

                                                </div> 

                                            </div>

                                            

                                            <div class="col-md-3"> 

                                                <div class="form-group tagged">

                                                    <label>Select Sub Design</label>

                                                    <select id="sub_design_filter" class="form-control" style="width:100%;"></select>

                                                </div> 

                                            </div>

                                            <div class="col-md-2"> 

                                                <label></label>

                                                    <div class="form-group">

                                                        <button type="button" id="search_sub_design_maping" class="btn btn-success">Search</button>   

                                                    </div>

                                            </div>
                                             <?php if ($access['delete']) { ?>
                                 

                                            <div class="col-md-1"> 

                                                <label></label>

                                                    <div class="form-group">

                                                        <button type="button" id="delete_sub_design_mapping" class="btn btn-danger"  style="margin-left: -34px;" disabled>Delete</button>   

                                                    </div>

                                            </div>

                                            

                                        <?php } ?>

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

                        <th>ID</th>

                        <th>Product</th>

                        <th>Design</th>

                        <th>Sub Design</th>

                        <th>Image</th>

                        <th>Add Image</th>

                        <th>Add Karigar</th>

                        <th>Description</th>

                        <th>Details</th>

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









<!--  Image Upload-->





<div class="modal fade" id="imageModal_new"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Image Upload</h4>

      </div>

      <div class="modal-body">

            <div class="row col-xs-8">

                    <div class="col-md-offset-1">

                        <div class="ord_img">

                            Add Image

                            <input id="sub_design_images" class="order_images_new" name="order_images_new" accept="image/*" type="file" multiple="true">

                            <form id="subdesign_img_form">

                                 <input type="hidden" name="subdesign[id_sub_design_mapping]" id="id_sub_design_mapping">

                                <input type="hidden" name="subdesign[subdesign_images]" id="subdesign_images" value="">

                            </form>

                        </div>

                    </div>

			    </div></br></br></br>

               <div class="row">

                        <div class="col-md-9">

                          <div class="col-md-12 box-items no-paddingwidth" style="max-height: 300px;overflow: auto;">

                    			<div class="col-md-12 col-xs-12 recent_bills no-paddingwidth blog-box">

                    				<div class="col-md-12 col-xs-12">

                    					<div class="col-md-12 col-xs-12 no-paddingwidth container-table">

                    						<table class="table table-bordered" id="design_img_preview">

                    							<thead>

                    							<tr>

                    								<th width="1%">#</th>

                    								<th width="1%">Img</th>

                    								<th width="2%">Action</th>

                    							</tr>

                    							</thead>

                    							<tbody>

                    							    

                    							</tbody>

                    						</table>

                    					</div>

                    				</div>

                    			</div>

                    		</div>

                       </div>

                    </div>

      </div>

      <div class="modal-footer">

      	<button type="button" class="btn btn-success" id="subdesignimg_submit">Save</button>

        <button type="button" class="btn btn-warning" id="close_img_modal">Close</button>

      </div>

    </div>

  </div>

</div>









<div class="modal fade" id="order_des_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  	<div class="modal-dialog">

    	<div class="modal-content">

      		<div class="modal-header">

        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        		<h4 class="modal-title" id="myModalLabel">Add Description</h4>

      		</div>

      		

		    <div class="modal-body">

			

				<div class="row">			

                    <input type="hidden" id="id">

			    	<div class="col-md-10 col-md-offset-1">

                        <label for="user_lastname">Item Description</label>

						<div class='form-group'>

			               	<textarea  cols="70"  id="description_new" name="description_new" ></textarea>

			        	</div>

			    	</div>

			    </div> 

			</div>

			

			<div class="modal-footer">

				<button type="button" class="btn btn-success" id="subdesigndes_submit" data-dismiss="modal">Save</button>

				<button type="button" class="btn btn-danger" id="close_des_modal" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div>





<div class="modal fade" id="karigar_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  	<div class="modal-dialog">

    	<div class="modal-content">

      		<div class="modal-header">

        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        		<h4 class="modal-title" id="myModalLabel">Add Karigar</h4>

      		</div>

      		

		    <div class="modal-body">

			

				<div class="row">			

                    <input type="hidden" id="id_sub_des">

			    	<div class="col-md-4 col-md-offset-1">

                        <label for="user_lastname">Karigar</label>

						<div class='form-group'>

			               	<select id="karigar_sel" class="form-control" multiple></select>

			               	<input type="hidden" id="karigar">

			        	</div>

			    	</div>

			    </div> 

			</div>

			

			<div class="modal-footer">

				<button type="button" class="btn btn-success" id="karigar_prod_submit" >Save</button>

				<button type="button" class="btn btn-danger" id="close_des_modal" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div>



