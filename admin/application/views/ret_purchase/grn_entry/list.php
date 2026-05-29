  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Master

        <small>GRN Entry List</small>

      </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li><a href="#">GRN</a></li>

        <li class="active">Entry</li>

      </ol>

    </section>

    <!-- Main content -->

    <section class="content">

      <div class="row">

        <div class="col-xs-12">



          <div class="box box-primary">

            <div class="box-header with-border">

              <div class="pull-left">

                <div class="form-group">

                  <button class="btn btn-default btn_date_range" id="grn-dt-btn">

                    <span style="display:none;" id="grn_date1"></span>

                    <span style="display:none;" id="grn_date2"></span>

                    <i class="fa fa-calendar"></i> Date range picker

                    <i class="fa fa-caret-down"></i>

                  </button>

                </div>

              </div>

              <div class="pull-right">
                <?php if ($access['add'] == 1) { ?>

                  <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/grnentry/add'); ?>"><i class="fa fa-plus-circle"></i> Add</a>
                <?php } ?>
              </div>

            </div>

            <div class="box-body">



              <div class="row">

                <div class="col-xs-12">

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

                </div>

              </div>



              <div class="table-responsive">

                <table id="grn_list" class="table table-bordered table-striped text-center">

                  <thead>

                    <tr>

                      <th width="1%;">#</th>

                      <th width="5%;">GRN No</th>

                      <th width="5%;">Image</th>

                      <th width="5%;">Date</th>

                      <th width="5%;">Supplier</th>

                      <th width="5%;">Purchase Type</th>

                      <th width="5%;">Mobile</th>

                      <th width="5%;">REF NO</th>

                      <th width="5%;">REF DATE</th>

                      <th width="5%;">Amount</th>

                      <th width="5%;">Status</th>

                      <th width="10%;">Action</th>

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

          <h4 class="modal-title" id="myModalLabel">Cancel GRN Entry</h4>

          <input type="hidden" id="grn_id">

        </div>

        <div class="modal-body">

          <strong>Are you sure! You want to Cancel this Entry?</strong>

        </div>



        <div class="col-md-12 bill_remarks">

          <label>Remarks<span class="error">*</span></label>

          <textarea class="form-control" id="cancel_remark" placeholder="Enter Remarks" rows="5" cols="10"> </textarea>

        </div>



        <div class="modal-footer">

          <button type="button" id="grn_cancel" class="btn btn-danger btn-confirm" data-dismiss="modal" disabled>Cancel</button>

          <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

        </div>

      </div>

    </div>

  </div>

  <!-- / modal -->

  <div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width:90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">Image Preview</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div id="order_images" style="margin-top: 2%;"></div>
          </div>
        </div>
        <div class="modal-footer">
          </br>
          <button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>