  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Stone
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Masters</a></li>
        <!-- <li><a href="#">Discount</a></li> -->
        <li class="active">Stone Discount </li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Stone Discount</h3>
              <a class="btn btn-success pull-right" id="save_stone" href="#" data-toggle="modal" data-target="#confirm-add"><i class="fa fa-user-plus"></i> Add </a>
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
                <table id="stone_disc_list" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Stone Type</th>
                      <th>Min Disc %</th>
                      <th>Max Disc %</th>
                      <th>Status</th>
                      <th>Action</th>
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
          <h4 class="modal-title" id="myModalLabel">Delete Stone Discount</h4>
        </div>
        <div class="modal-body">
          <strong>Are you sure! You want to delete this Stone Discount record?</strong>
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
          <h4 class="modal-title" id="myModalLabel">Add Stone Discount</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-offset-1 col-md-10" id='error-msg'></div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Stone type
                <span class="error">*</span></label>
              <div class="col-md-4">
                <Select class="form-control" id="stone_type" name="stonetype" placeholder="Select Stone type" required></Select>
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Min Disc %
                <span class="error">*</span></label>
              <div class="col-md-4">
                <input type="number" class="form-control" id="min_disc" name="mindisc" placeholder="Enter Min Disc %" min="0" max="100" required />
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Max Disc %
                <span class="error">*</span></label>
              <div class="col-md-4">
                <input type="number" class="form-control" id="max_disc" name="maxdisc" placeholder="Enter Max Disc %" min="0" max="100" required />
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Status</label>
              <div class="col-md-4">
                <input type="checkbox" class="status" id="disc_sts" name="discstatus" data-on-text="YES" data-off-text="NO" checked="true" />
                <input type="hidden" id="disc_status" value="1" />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" id="add_disc" class="btn btn-success">Save </a>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
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
          <h4 class="modal-title" id="myModalLabel">Edit Stone Discount</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Stone Type
                <span class="error">*</span></label>
              <div class="col-md-4">
                <select class="form-control" id="ed_stone_type" name="stonetype" placeholder="Enter Tax name" required ></select>
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Min Disc %
                <span class="error">*</span></label>
              <div class="col-md-4">
                <input type="text" class="form-control" id="ed_min_disc" name="mindisc" placeholder="Enter Tax code" required />
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Max Disc %
                <span class="error">*</span></label>
              <div class="col-md-4">
                <input type="text" class="form-control" id="ed_max_disc" name="maxdisc" placeholder="Enter Tax percentage" required />
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group">
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Status</label>
              <div class="col-md-4">
                <input type="checkbox" class="status" id="ed_disc_status" name="ed_discstatus" data-on-text="YES" data-off-text="NO" />
                <input type="hidden" id="ed_discstatus" value="1">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" id="update_stn_disc" class="btn btn-success" data-dismiss="modal">Update</a>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->
