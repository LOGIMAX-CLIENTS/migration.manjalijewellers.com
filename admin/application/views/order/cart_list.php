  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

    <!-- Content Header (Page header) -->

    <section class="content-header">

      <h1>

        Master

        <small>Manage your Order</small>

      </h1>

      <ol class="breadcrumb">

        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

        <li><a href="#">Master</a></li>

        <li class="active">Cart</li>

      </ol>

    </section>



    <!-- Main content -->

    <section class="content">

      <div class="row">

        <div class="col-xs-12">

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">Cart List</h3> <span id="total_count" class="badge bg-green"></span>



            </div>

            <div class="box-body">

              <div class="row">

                <div class="col-md-offset-2 col-md-10">

                  <div class="box box-default">

                    <div class="box-body">

                      <div class="row">

                        <div class="col-md-2">

                          <div class="form-group tagged">

                            <label>Select Product</label>

                            <select id="prod_select" class="form-control" style="width:100%;"></select>

                          </div>

                        </div>

                        <div class="col-md-2">

                          <div class="form-group tagged">

                            <label>Select Design</label>

                            <select id="des_select" class="form-control design" style="width:100%;"></select>

                          </div>

                        </div>

                        <div class="col-md-2">

                          <div class="form-group tagged">

                            <label>Select Weight Range</label>

                            <select id="wt_select" class="form-control" style="width:100%;"></select>

                          </div>

                        </div>



                        <div class="col-md-2">

                          <div class="form-group tagged">

                            <label>Select Karigar</label>

                            <select id="select_karigar" class="form-control" style="width:100%;"></select>

                          </div>

                        </div>



                        <div class="col-md-3">

                          <div class="form-group tagged">

                            <label>Select Due Date</label>

                            <input class="form-control smith_due_dt" data-date-format="dd-mm-yyyy" value="" type="text" placeholder="Smith Due Date" style="width: 100px;" />

                          </div>

                        </div>



                      </div>

                    </div>

                  </div>

                </div>

              </div>

              <div class="col-xs-12">

                <div class="row">

                  <div class="col-md-6">

                    <div class="btn-group" data-toggle="buttons">
                      <?php if ($access['add'] == 1) { ?>
                        <label class="btn btn-success" id="approve">

                          <input type="radio" name="order_status_btn" value="1"><i class="icon fa fa-check"></i>Order Place
                        <?php } ?>
                        </label>

                        <?php if ($access['add'] == 1) { ?>
                          <label class="btn btn-danger" id="reject">


                            <input type="radio" name="order_status_btn" value="2"><i class="icon fa fa-remove"></i> Empty Cart
                          <?php } ?>
                          </label>

                    </div>

                  </div>

                </div>

              </div></br></br>

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

                <table id="cart_list" class="table table-bordered table-striped text-center">

                  <thead>

                    <tr>

                      <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all" />All</label></th>

                      <th>Order Date</th>

                      <th>Product</th>

                      <th>Design</th>

                      <th>Sub Design</th>

                      <th>Weight</th>
                      <th>Piece</th>
                      <th>Min</th>
                      <th>Max</th>
                      <th>Ordered</th>
                      <th>Tagged</th>
                      <th>Stock</th>
                      <th>Total</th>
                      <th>Shortage</th>
                      <th>Excess</th>
                      <th>Size</th>
                      <th>Emp</th>

                    </tr>

                  </thead>

                  <tbody></tbody>

                </table>

              </div>

            </div><!-- /.box-body -->

            <div class="overlay" style="display:none;">

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

          <h4 class="modal-title" id="myModalLabel">Delete Order</h4>

        </div>

        <div class="modal-body">

          <strong>Are you sure! You want to delete this Order?</strong>

        </div>

        <div class="modal-footer">

          <a href="#" class="btn btn-danger btn-confirm">Delete</a>

          <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

        </div>

      </div>

    </div>

  </div>

  <!-- / modal -->