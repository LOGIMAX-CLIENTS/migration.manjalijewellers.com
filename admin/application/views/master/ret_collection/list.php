  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Collection

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Collection List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

                <div class="box-header with-border">

                  <h3 class="box-title">Collection List</h3>    <span id="total_metal" class="badge bg-green"></span>      

                           <a class="btn btn-success pull-right" id="add_collc" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 

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

					

				  <div class="row">

					<div class="col-sm-10 col-sm-offset-1">

					<div id="chit_alert"></div>

					 

					</div>

				  </div>

						

                  <div class="table-responsive">

                  <table id="collection_list" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

            <th>ID</th>

						<th>Collection Name</th>

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

        <h4 class="modal-title" id="myModalLabel">Delete Collection</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Collection record?</strong>

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

        <h4 class="modal-title" id="myModalLabel">Add Collection </h4>

      </div>

      <div class="modal-body">

    	  <div class="row" >

    				<div class="row">

    				 	<div class="form-group">

                           <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Name

    					   <span class="error">*</span></label>

                           <div class="col-md-4">

                           	 <input type="text" class="form-control" id="add_collection_name" name="add_collection_name" placeholder="Enter Collection name" required="true"> 

                    	  <p class="help-block"></p>

                           	

                           </div>

                        </div>

    				 </div>

          </div>

    </div>



      <div class="modal-footer">

		<a href="#" id="add_newcollection" class="btn btn-success">Save & New</a>

      	<a href="#" id="add_collection" class="btn btn-warning" data-dismiss="modal">Save & Close</a>

        <button type="button" class="btn btn-danger" data-dismiss="modal" >Close</button>

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

        <h4 class="modal-title" id="myModalLabel">Edit Collection </h4>

      </div>

      <div class="modal-body">

    	  <div class="row" >

    				<div class="row">

    				 	<div class="form-group">

                           <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Name

    					   <span class="error">*</span></label>

                           <div class="col-md-4">

                           	 <input type="text" class="form-control" id="ed_collection_name" name="ed_collection_name" placeholder="Enter Collection name" required="true"> 

                    	  <p class="help-block"></p>

                           	

                           </div>

                        </div>

    				 </div>

          </div>

    </div>

      <div class="modal-footer">

      	<a href="#" id="update_collection" class="btn btn-success" data-dismiss="modal" >Update</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->      



