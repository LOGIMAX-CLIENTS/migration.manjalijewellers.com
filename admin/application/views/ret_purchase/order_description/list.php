<!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

           Master

            <small>Purchase Order Instructions</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Purchase</a></li>

            <li class="active">Instructions</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Instructions List</h3>  <span id="total_count" class="badge bg-green"></span>  

                  <div class="pull-right">

                  <?php if($access['add']==1){?>

                  	 <a class="btn btn-success pull-right" id="add_des" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                     <?php }?>
				  </div>

                </div>

                 <div class="box-body">  

                   

				   <div class="row">

						<div class="col-xs-12">

						<!-- Alert -->

						<?php 

							if($this->session->flashdata('chit_alert'))

							 {

								$message = $this->session->flashdata('chit_alert');

						?>

							   <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">

								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

								<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>

								<?php echo $message['message']; ?>

							  </div>

							  

						<?php } ?>  

						</div>

				   </div>

			  

                  <div class="table-responsive">

	                 <table id="des_list" class="table table-bordered table-striped text-center">

	                    <thead>

	                      <tr>

	                        <th>#</th>

	                        <th>Instructions</th>

	                        <th>Action</th>

	                      </tr>

	                    </thead> 

	                 </table>

                  </div>

                </div><!-- /.box-body -->

                <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

            

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

        <h4 class="modal-title" id="myModalLabel">Delete Order Instructions</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this Instructions?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

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

        <h4 class="modal-title" id="myModalLabel">Add Instructions</h4>

      </div>

         <div class="modal-body">

            <form id="myform">

                <div class="row">

                    <div class="form-group">

                        <label for="" class="col-sm-4 col-md-offset-1 ">Instructions<span class="error">*</span></label>

                        <div class="col-sm-12">

                            <textarea id="order_des"></textarea>

                        </div>

                    </div>

                </div><p class="help-block"></p>

            </form>

        </div>

      <div class="modal-footer">

      	<a href="#" id="add_order_des" class="btn btn-success" data-dismiss="modal" >Add</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>



<div class="modal fade" id="confirm-edit"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Update Instructions</h4>

      </div>

         <div class="modal-body">

            <form id="myform">

                <div class="row">

                    <div class="form-group">

                        <label for="" class="col-sm-4 col-md-offset-1 ">Instructions<span class="error">*</span></label>

                        <div class="col-sm-12">

                            <input type="hidden" id="edit-id" value="" />

                            <textarea id="ed_rder_des"></textarea>

                        </div>

                    </div>

                </div><p class="help-block"></p>

            </form>

        </div>

      <div class="modal-footer">

      	<a href="#" id="update_order_des" class="btn btn-success" data-dismiss="modal" >Update</a>

        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

<!-- / modal -->