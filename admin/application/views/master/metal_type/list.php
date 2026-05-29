  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Old Metal Type
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Old Metal Type List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Old Metal Type List</h3><span id="total_count" class="badge bg-green"></span> 
                  <?php if($access['add']==1){?>      
                           <a class="btn btn-success pull-right" id="add_metal" href="#" data-target="#confirm-add" data-toggle="modal" ><i class="fa fa-user-plus"></i>Add</a> 
                           <?php }?>
                          </div><!-- /.box-header -->
                <div class="box-body">
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
                    <div class="table-responsive">
                        <table id="metal_list" class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Metal</th>
                                    <th>Name</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Metal Type</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this Metal Type?</strong>
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
<div class="modal fade" id="confirm-add"  aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Metal Type</h4>
      </div>
        <div class="modal-body">
            <div id="chit_alert1"></div>
            <div class="row">
                <div class="form-group">
                <form id="metal_crerate">
                    <div class="form-group">
                        <label for="first_name" class="col-md-3 col-md-offset-1 ">Metal Type<span class="error"> *</span></label>
                            <div class="col-md-5 ">
                                <input type="text" class="form-control" id="metal_type" name="metal_type"  placeholder="Enter Metal Type ">
                                <p class="help-block"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="first_name" class="col-md-3 col-md-offset-1 ">Select Metal<span class="error"> *</span></label>
                            <div class="col-md-5 ">
                                <select class="form-control" id="metal_sel" name="id_metal" style="width:100%;"></select>
                                <p class="help-block"></p>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>

      <div class="modal-footer">
        <a href="#" id="add_new_metal_type" class="btn btn-success">Save & New</a>
		<a href="#" id="add_metal_type" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Update Metal Type</h4>
      </div>
        <div class="modal-body">
            <form id="metal_update">
                <div class="row">
                    <div class="form-group">
                      <input type="hidden" id="id_metal_type" name="id_metal_type">
                        <div class="form-group">
                            <label for="first_name" class="col-md-3 col-md-offset-1 ">Metal Type<span class="error"> *</span></label>
                                <div class="col-md-5 ">
                                    <input type="text" class="form-control" id="ed_metal_name" name="ed_metal_type"  placeholder="Enter First Name">
                                    <p class="help-block"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="first_name" class="col-md-3 col-md-offset-1 ">Metal Type<span class="error"> *</span></label>
                                <div class="col-md-5 ">
                                    <select class="form-control" id="metal_category" name="id_metal" style="width:100%;"></select>
                                    <input type="hidden" id="metal_id">
                                    <p class="help-block"></p>
                                </div>
                        </div>
                    </div>
                </div>
                </form>
         </div>
      <div class="modal-footer">
        <a href="#" id="update_metal_type" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

