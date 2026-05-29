  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Other Inventory Purchase
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Masters</a></li>
        <li class="active">Other Inventory Purchase List</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Other Inventroy Purchase List</h3><span id="total_items" class="badge bg-green"></span>
              <a class="btn btn-success pull-right" id="add_pur_details" href="<?php echo base_url('index.php/admin_ret_other_inventory/purchase_entry/add'); ?>"><i class="fa fa-user-plus"></i> Add </a>
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
              <div class="box-body">
                <div class="row">
                  <div class="col-md-offset-2 col-md-8">
                    <div class="box box-default">
                      <div class="box-body">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group">
                              <div class="input-group">
                                <br>
                                <button class="btn btn-default btn_date_range" id="date_range_picker">
                                  <i class="fa fa-calendar"></i> Date range picker
                                  <i class="fa fa-caret-down"></i>
                                </button>
                                <span style="display:none;" id="from_date"></span>
                                <span style="display:none;" id="to_date"></span>
                              </div>
                            </div><!-- /.form group -->
                          </div>

                          <div class="col-md-3">
                            <div class='form-group'>
                              <label><span class="error"> </span></label>
                              <select class="form-control" name="purchase[id_karigar]" id="select_karigar" required="true" style="width:100%;"></select>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <label></label>
                            <div class="form-group">
                              <button type="button" id="purchase_item_search" class="btn btn-info">Search</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="table-responsive">
                <table id="other_item_pur" class="table table-bordered table-striped text-center">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Supplier Name</th>
                      <th>Image</th>
                      <th>Entry Date</th>
                      <th>Order Ref No</th>
                      <th>Supplier Refno</th>
                      <th>Supplier Bill date</th>
                      <th>Total Pieces</th>
                      <th>Tag Pieces</th>
                      <th>Balance Pieces</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th></th>
                      <th style="width:10%">Action</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr style="font-weight:bold;">
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td style='text-align: right;'></td>
                      <td style='text-align: right;'></td>
                      <td style='text-align: right;'></td>
                      <td style='text-align: right;'></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tfoot>
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
  <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Delete item</h4>
        </div>
        <div class="modal-body">
          <strong>Are you sure! You want to delete this item?</strong>
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
  <!-- modal -->
  <div class="modal fade" id="confirm-purchase-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Cancel Purchase Entry</h4>
          <input type="hidden" id="otr_inven_pur_id">
        </div>
        <div class="modal-body">
          <strong>Are you sure! You want to Cancel this Entry?</strong>
        </div>

        <div class="col-md-12 bill_remarks">
          <label>Remarks<span class="error">*</span></label>
          <textarea class="form-control" id="cancel_remark" placeholder="Enter Remarks" rows="5" cols="10"> </textarea>
        </div>

        <div class="modal-footer">
          <button type="button" id="purchase_cancel" class="btn btn-danger btn-confirm" data-dismiss="modal" disabled>Cancel</button>
          <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- / modal -->