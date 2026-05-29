  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Zone

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Zone List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

              <div class="box-header with-border">

                  <h3 class="box-title">Zone List</h3>    <span id="total_purity" class="badge bg-green"></span>      
                  <?php if($access['add']==1){?>
                  <a class="btn btn-success pull-right" id="add_purity" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                  <?php }?>
                </div><!-- /.box-header -->

				

                <div class="box-body">

              		<div id="chit_alert1" style="width: 92%;margin-left: 3%;"></div>			

                  <div class="table-responsive">

                  <table id="zone_list" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

                        <th>ID</th>

                        <th>Branch</th>

                        <th>Zone</th>

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

        <h4 class="modal-title" id="myModalLabel">Delete Zone</h4>

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



<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

		<div class="modal-content">

			<div class="modal-header">

				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

			</div>

			<div id="chit_alert" style="width: 92%;margin-left: 3%;"></div>

			<form id="zone_form">

                <div class="modal-body">

                    <div class="row" >

                        <div class="col-md-offset-1 col-md-10" id='error-msg'></div>

                    </div>

                    <?php if($this->session->userdata('branch_settings')==1){?>

                    <div class="row">

                        <div  class="form-group">

                            <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Branch</label>

                                <div class="col-md-4">

                                    <select class="form-control" id="branch_select" name="id_branch" style="width:100%;"></select>

                                <input type="hidden" id="branch_settings" value="<?php echo $this->session->userdata('branch_settings');?>">

                                </div>

                        </div>

                    </div><p></p>

                    <?php }?>

                    <div class="row">

                        <div class="form-group">

                            <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Zone Name</label>

                                <div class="col-md-4">

                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter The Name" required="true"> 

                                <p class="help-block"></p>

                            </div>

                        </div>

                    </div>     

                </div>

			</form>

		  <div class="modal-footer">

			<a href="#" id="addnew_zone" class="btn btn-success">Save and New</a>

			<a href="#" id="add_zone" class="btn btn-success">Save</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>



<!-- modal -->      

<div class="modal fade" id="confirm-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Edit Zone</h4>

      </div>

      <div class="modal-body">

	    <div id="chit_alert" style="width: 92%;margin-left: 3%;"></div>

			<form id="ed_zone_form">

                <div class="modal-body">

                    <div class="row" >

                        <div class="col-md-offset-1 col-md-10" id='error-msg'></div>

                    </div>

                    <?php if($this->session->userdata('branch_settings')==1){?>

                    <div class="row">

                        <div  class="form-group">

                            <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Branch</label>

                                <div class="col-md-4">

                                    <select class="form-control ed_branch_select" id="branch_select" name="ed_id_branch" style="width:100%;"></select>

                                    <input type="hidden" id="branch_settings" value="<?php echo $this->session->userdata('branch_settings');?>">

                                </div>

                        </div>

                    </div><p></p>

                    <?php }?>

                    <div class="row">

                        <div class="form-group">

                            <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Zone Name</label>

                                <div class="col-md-4">

                                <input type="hidden" id="id_zone" name="id_zone">

                                <input type="text" class="form-control" id="ed_name" name="ed_name" placeholder="Enter The Name" required="true"> 

                                <p class="help-block"></p>

                            </div>

                        </div>

                    </div>     

                </div>

			</form>   

      </div>

        <div class="modal-footer">

			<a href="#" id="update_zone" class="btn btn-success">Update</a>

			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

    </div>

  </div>

</div>

<!-- / modal -->      



