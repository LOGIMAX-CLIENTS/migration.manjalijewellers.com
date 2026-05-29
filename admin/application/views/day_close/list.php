  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Day Close
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Catalog</a></li>
        <li class="active">Day Close</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Day Close</h3>
              <span id="total_metal" class="badge bg-green"></span>
              <?php if($access['add'] ==1){ ?>
              <a class="btn btn-success pull-right" id="day_close_dynamic"><i class="fa fa-times" aria-hidden="true"></i> Day Close</a>
              <?php }?>
            </div><!-- /.box-header -->
            <div class="box-body">
              <!-- Alert -->
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

              <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                  <div id="chit_alert"></div>

                </div>
              </div>

              <div class="table-responsive">
                <table id="dayclose_list" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all">All</label></th>
                      <th>Branch Name</th>
                      <th>Type</th>
                      <!-- <th>Day Close Time</th> -->
                      <th>Is Day Close</th>
                      <th>Entry Date</th>
                    </tr>
                  </thead>

                </table>
              </div>
              <div class="overlay" style="display:none">
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
          <h4 class="modal-title" id="myModalLabel">Delete Metal</h4>
        </div>
        <div class="modal-body">
          <strong>Are you sure! You want to delete this Metal record?</strong>
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
  <div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Add Metal </h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-offset-1 col-md-10" id='error-msg'></div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Metal
                <span class="error">*</span></label>
              <div class="col-md-4">
                <input type="text" class="form-control" id="metal_name" name="ad_metal_name" placeholder="Enter Metal name" required="true">
                <p class="help-block"></p>

              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Code
                <span class="error">*</span></label>
              <div class="col-md-4">
                <input type="text" class="form-control" id="metal_code" name="ad_metal_code" placeholder="Enter Metal code">
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Tax Group
                <span class="error">*</span></label>
              <div class="col-md-4">
                <select id="tgrp_sel" class="form-control"></select>
                <input id="tgrp_id" name="tgrp_id" type="hidden" />
              </div>
            </div>
          </div>
          <div class="row">
            <p class="help-block"></p>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Status</label>
              <div class="col-md-4">
                <input type="checkbox" class="status" id="ad_metalstatus" name="ad_metalstatus" data-on-text="YES" data-off-text="NO" checked="true" />
                <input type="hidden" id="addmetal_status" value="1" />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" id="newmetal" class="btn btn-success">Save & New</a>
          <a href="#" id="add_newmetal" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
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
          <h4 class="modal-title" id="myModalLabel">Edit Metal </h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-offset-1 col-md-10" id='error'></div>
          </div>
          <div class="row">
            <div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Tax Group
                <span class="error">*</span></label>
              <div class="col-md-4">
                <select id="ed_tgrp_sel" class="form-control"></select>
                <input id="ed_tgrp_id" name="tgrp_id" type="hidden" />
              </div>
            </div>
          </div>
          <div class="row">
            <p class="help-block"></p>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Metal
                <span class="error">*</span></label>
              <div class="col-md-4">
                <input type="hidden" id="edit-id" value="" />
                <input type="text" class="form-control" id="ed_metal_name" name="ed_metal_name" placeholder="Enter Metal Name">
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Code
                <span class="error">*</span></label>
              <div class="col-md-4">
                <input type="text" class="form-control" id="ed_metal_code" name="ed_metal_code" placeholder="Enter Metal Code">
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Status</label>
              <div class="col-md-4">
                <input type="checkbox" class="status" id="ed_metalstatus" name="ed_metalstatus" data-on-text="YES" data-off-text="NO" checked="true" />
                <input type="hidden" id="edmetal_status" value="1" />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" id="update_metal" class="btn btn-success" data-dismiss="modal">Update</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->