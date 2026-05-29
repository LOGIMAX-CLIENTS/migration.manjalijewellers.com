  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Old Metal Category
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Masters</a></li>
        <li class="active">Old Metal Category List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Old Metal Category List</h3><span id="total_count" class="badge bg-green"></span>
              <a class="btn btn-success pull-right" id="add_old_metal_cat" href="#" data-target="#confirm-add" data-toggle="modal"><i class="fa fa-user-plus"></i>Add</a>
            </div><!-- /.box-header -->
            <div class="box-body">
              <?php
              if ($this->session->flashdata('chit_alert')) {
                $message = $this->session->flashdata('chit_alert');
              ?>
                <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                  <?php echo $message['message']; ?>
                </div>

              <?php } ?>
              <div class="table-responsive">
                <table id="old_metal_cat_list" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Metal</th>
                      <th>Old Metal</th>
                      <th>Category Name</th>
                      <th>Percentage</th>
                      <th>Discount</th>
                      <th>Touch From</th>
                      <th>Touch To</th>
                      <th>Status</th>
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
  <div class="modal fade" id="confirm-delete" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Delete Old Metal Category</h4>
        </div>
        <div class="modal-body">
          <strong>Are you sure! You want to delete this Old Metal Category?</strong>
        </div>
        <div class="modal-footer">
          <a href="#" class="btn btn-danger btn-confirm">Delete</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->
  <!-- modal -->
  <div class="modal fade" id="confirm-add" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Add Metal Category</h4>
        </div>
        <div class="modal-body">
          <div id="chit_alert1"></div>
          <div class="row">
            <div class="form-group">
              <form id="old_metal_cat_create">
                <div class="form-group col-md-12">
                  <label for="old_metal_cat" class="col-md-5 col-md-offset-1 ">Old Metal Category Name<span class="error"> *</span></label>
                  <div class="col-md-5 ">
                    <input type="text" class="form-control" id="old_metal_cat" name="old_metal_cat" placeholder="Enter Old Metal Category ">
                    <p class="help-block"></p>
                  </div>
                </div>
                <div class="form-group col-md-12">
                  <label for="old_metal_perc" class="col-md-5 col-md-offset-1 ">Percertage<span class="error"> *</span></label>
                  <div class="col-md-5 ">
                    <input type="text" class="form-control" id="old_metal_perc" name="old_metal_perc" placeholder="Enter Metal Category Percentage ">
                    <p class="help-block"></p>
                  </div>
                </div>

                <div class="form-group col-md-12">
                        <label for="old_metal_perc" class="col-md-5 col-md-offset-1 ">Discount<span class="error"> *</span></label>
                            <div class="col-md-5 ">
                                <input type="text" class="form-control" id="old_metal_discount" name="old_metal_discount"  placeholder="Enter Metal Discount ">
                                <p class="help-block"></p>
                        </div>
                    </div>
                <div class="form-group col-md-12">
                  <label for="touch_from" class="col-md-5 col-md-offset-1 ">Touch From</label>
                  <div class="col-md-5 ">
                    <input type="number" step="0.01" class="form-control" id="touch_from" name="touch_from" placeholder="Enter Touch From" value="0">
                    <p class="help-block"></p>
                  </div>
                </div>
                <div class="form-group col-md-12">
                  <label for="touch_to" class="col-md-5 col-md-offset-1 ">Touch To</label>
                  <div class="col-md-5 ">
                    <input type="number" step="0.01" class="form-control" id="touch_to" name="touch_to" placeholder="Enter Touch To" value="0">
                    <p class="help-block"></p>
                  </div>
                </div>
                <div class="form-group col-md-12">
                  <label for="id_old_metal_type" class="col-md-5 col-md-offset-1 ">Select Old Metal<span class="error"> *</span></label>
                  <div class="col-md-5 ">
                    <select class="form-control" id="id_old_metal_type" name="id_old_metal_type" style="width:100%;"></select>
                    <p class="help-block"></p>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <a href="#" id="add_new_old_metal_category" class="btn btn-success">Save & New</a>
          <a href="#" id="add_old_metal_category" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
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
          <h4 class="modal-title" id="myModalLabel">Update Old Metal Category</h4>
        </div>
        <div class="modal-body">
          <form id="old_metal_cat_update">
            <input type="hidden" id="id_old_metal_cat" name="id_old_metal_cat">
            <div class="form-group col-md-12">
              <label for="ed_old_metal_cat" class="col-md-5 col-md-offset-1 ">Old Metal Category Name<span class="error"> *</span></label>
              <div class="col-md-5 ">
                <input type="text" class="form-control" id="ed_old_metal_cat" name="ed_old_metal_cat" placeholder="Enter Old Metal Category ">
                <p class="help-block"></p>
              </div>
            </div>
            <div class="form-group col-md-12">
              <label for="ed_old_metal_perc" class="col-md-5 col-md-offset-1 ">Percertage<span class="error"> *</span></label>
              <div class="col-md-5 ">
                <input type="text" class="form-control" id="ed_old_metal_perc" name="ed_old_metal_perc" placeholder="Enter Metal Category Percentage ">
                <p class="help-block"></p>
              </div>
            </div>

            <div class="form-group col-md-12">
                    <label for="ed_old_metal_perc" class="col-md-5 col-md-offset-1 ">Discount<span class="error"> *</span></label>
                        <div class="col-md-5 ">
                            <input type="text" class="form-control" id="ed_old_metal_discount" name="ed_old_metal_discount"  placeholder="Enter Metal Category Percentage ">
                            <p class="help-block"></p>
                    </div>
                </div>

            <div class="form-group col-md-12">
              <label for="ed_touch_from" class="col-md-5 col-md-offset-1 ">Touch From</label>
              <div class="col-md-5 ">
                <input type="number" step="0.01" class="form-control" id="ed_touch_from" name="ed_touch_from" placeholder="Enter Touch From" value="0">
                <p class="help-block"></p>
              </div>
            </div>
            <div class="form-group col-md-12">
              <label for="ed_touch_to" class="col-md-5 col-md-offset-1 ">Touch To</label>
              <div class="col-md-5 ">
                <input type="number" step="0.01" class="form-control" id="ed_touch_to" name="ed_touch_to" placeholder="Enter Touch To" value="0">
                <p class="help-block"></p>
              </div>
            </div>
            <div class="form-group col-md-12">
              <label for="ed_id_old_metal_type" class="col-md-5 col-md-offset-1 ">Select Old Metal<span class="error"> *</span></label>
              <div class="col-md-5 ">
                <select class="form-control" id="ed_id_old_metal_type" name="ed_id_old_metal_type" style="width:100%;"></select>
                <p class="help-block"></p>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <a href="#" id="update_old_metal_cat" class="btn btn-success" data-dismiss="modal">Update</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->