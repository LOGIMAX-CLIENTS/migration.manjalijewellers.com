  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
          <h1>
              Ledger Adjustment
              <small></small>
          </h1>
          <ol class="breadcrumb">
              <li><a href="<?php echo base_url('dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
              <li><a href="#">Billing</a></li>
              <li class="active">Ledger Adjustment List</li>
          </ol>
      </section>

      <!-- Main content -->
      <section class="content">
          <div class="row">
              <div class="col-xs-12">

                  <div class="box">
                      <div class="box-header">
                          <h3 class="box-title">Ledger Adjustment List</h3>
                          <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/admin_ret_billing/bank_ledger_transfer/add'); ?>"><i class="fa fa-plus"></i>Add Transfer</a><br><br>
                      </div><!-- /.box-header -->
                      <div class="box-body">
                          <!-- Alert -->
                          <?php
                            if ($this->session->flashdata('alert')) {
                                $message = $this->session->flashdata('alert');
                            ?>
                              <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                  <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                                  <?php echo $message['message']; ?>
                              </div>

                          <?php } ?>
                          <div class="table-responsive">
                              <table id="ledger_transfer_table" class="table table-bordered table-striped text-center">
                                  <thead>
                                      <tr>
                                          <th>S.NO</th>
                                          <th>Date</th>
                                          <th>From Ledger</th>
                                          <th>To Ledger</th>
                                          <th>Transfer Type</th>
                                          <th>Amount</th>
                                          <th>Narration</th>
                                      </tr>
                                  </thead>
                                  <tbody>
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

<!-- Hidden input for base URL used by external JS -->
<input type="hidden" id="ledger_transfer_base_url" value="<?php echo base_url(); ?>">

<script>
    $(document).ready(function() {
        $('#ledger_transfer_table').DataTable({
            "order": [[ 0, "asc" ]] 
        });
    });
</script>
