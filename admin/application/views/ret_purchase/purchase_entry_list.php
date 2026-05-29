  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Master

        <small>Supplier Bill Entry List</small>

      </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li><a href="#">Purchase</a></li>

        <li class="active">Entry</li>

      </ol>

    </section>



    <!-- Main content -->

    <section class="content">

      <div class="row">

        <div class="col-xs-12">



          <div class="box box-primary">

            <div class="box-header with-border">

              <div class="pull-right">

                <?php if ($access['add'] == 1) { ?>

                  <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/purchase/purchase_add'); ?>"><i class="fa fa-plus-circle"></i> Add</a>

                <?php } ?>
              </div>

            </div>
            <p></p>

            <div class="row">

              <div class="col-md-12">

                <div class="col-md-2">

                  <div class="form-group">

                    <button class="btn btn-default btn_date_range" id="sbe-dt-btn">

                      <span style="display:none;" id="sbe_date1"></span>

                      <span style="display:none;" id="sbe_date2"></span>

                      <i class="fa fa-calendar"></i> Date range picker

                      <i class="fa fa-caret-down"></i>

                    </button>

                  </div>

                </div>



                <div class="col-md-2">

                  <div class="form-group">

                    <?php if ($access['edit'] == 1) { ?>

                      <button type="button" id="po_id_approve" class="btn btn-success">Approve</button>
                    <?php } ?>
                  </div>

                </div>



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

                <table id="pur_entry_list" class="table table-bordered table-striped text-center">

                  <thead>

                    <tr>

                      <th>#</th>

                      <th>GRN No</th>

                      <th>Date</th>

                      <th>Due date</th>

                      <th>Po No</th>

                      <th>Karigar</th>

                      <th>Category</th>

                      <th>Purity</th>

                      <th>Gwt</th>

                      <th>Lwt</th>

                      <th>Nwt</th>

                      <th>Purchase Amt</th>

                      <th>Pure Wt</th>

                      <th>Purchase Type</th>

                      <th>GRN Wt</th>

                      <th>Diff Wt</th>

                      <th>Employee</th>

                      <th>Status</th>

                      <th width="10%;">Action</th>

                    </tr>

                  <tfoot>
                    <tr style="font-weight:bold;">
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                  </tfoot>

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
          <input type="hidden" id="po_id">
        </div>
        <div class="modal-body">
          <strong>Are you sure! You want to Cancel this Entry?</strong>
        </div>

        <div class="col-md-12 bill_remarks">
          <label>Remarks<span class="error">*</span></label>
          <textarea class="form-control" id="po_cancel_remark" placeholder="Enter Remarks" rows="5" cols="10"> </textarea>
        </div>

        <div class="modal-footer">
          <button type="button" id="cancel_po" class="btn btn-danger btn-confirm" data-dismiss="modal" disabled>Cancel</button>
          <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->