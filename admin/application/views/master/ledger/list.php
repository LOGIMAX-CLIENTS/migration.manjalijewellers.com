  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
          <h1>
              Bank Ledger Master
              <small></small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
              <li><a href="#">Master</a></li>
              <li class="active">Bank Ledger List</li>
          </ol>
      </section>

      <!-- Main content -->
      <section class="content">
          <div class="row">
              <div class="col-xs-12">

                  <div class="box">
                      <div class="box-header">
                          <h3 class="box-title">Bank Ledger List</h3>
                          <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/settings/ledger/add'); ?>"><i class="fa fa-plus"></i> Add</a><br><br>
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
                          <div class="table-responsive">
                              <table id="ledger_table" class="table table-bordered table-striped text-center">
                                  <thead>
                                      <tr>
                                          <th>Ledger No</th>
                                          <th>Ledger Name</th>
                                          <th>Opening Date</th>
                                          <th>Opening Balance</th>
                                          <th>Bank Name</th>
                                          <th>Minimum Balance</th>
                                          <th>POS Devices</th>
                                          <th>Action</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php if (!empty($ledgers)) {
                                            foreach ($ledgers as $ledger) { ?>
                                              <tr>
                                                  <td><?php echo $ledger['id_ledger']; ?></td>
                                                  <td style="text-align: left"><?php echo $ledger['ledger_name']; ?></td>
                                                  <td style="text-align: left"><?php echo (isset($ledger['opening_date']) && !empty($ledger['opening_date'])) ? date('d-m-Y', strtotime($ledger['opening_date'])) : ''; ?></td>
                                                  <td style="text-align: right"><?php echo number_format($ledger['opening_balance'], 2); ?></td>
                                                  <td style="text-align: left"><?php echo isset($ledger['bank_names']) ? $ledger['bank_names'] : ''; ?></td>
                                                  <td style="text-align: right"><?php echo number_format($ledger['min_balance'], 2); ?></td>
                                                  <td style="text-align: left"><?php echo isset($ledger['device_names']) ? $ledger['device_names'] : ''; ?></td>
                                                  <td>
                                                      <a href="<?php echo base_url('index.php/settings/ledger/edit/' . $ledger['id_ledger']); ?>" class="btn btn-primary btn-edit"><i class='fa fa-edit'></i>Edit</a>
                                                      <a href="javascript:void(0);" data-href="<?php echo base_url('index.php/settings/ledger/Delete/' . $ledger['id_ledger']); ?>" class="btn btn-danger btn-del" data-toggle="modal" data-target="#confirm-delete"><i class='fa fa-trash'></i>Delete</a>
                                                  </td>
                                              </tr>
                                      <?php }
                                        } ?>
                                  </tbody>
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
                  <h4 class="modal-title" id="myModalLabel">Delete Ledger</h4>
              </div>
              <div class="modal-body">
                  <strong>Are you sure! You want to delete this ledger?</strong>
              </div>
              <div class="modal-footer">
                  <a href="#" class="btn btn-danger btn-confirm">Delete</a>
                  <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>
  <!-- / modal -->