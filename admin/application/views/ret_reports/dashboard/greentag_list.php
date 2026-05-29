  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

      <!-- Content Header (Page header) -->

      <section class="content-header">

          <h1>

              Green Tag Sales

              <small></small>

          </h1>

          <ol class="breadcrumb">

              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

              <li><a href="#">Reports</a></li>

              <li class="active">Green Tag</li>

          </ol>

      </section>

      <!-- Main content -->

      <section class="content">

          <div class="row">

              <div class="col-xs-12">

                  <div class="box box-primary">

                      <div class="box-header">

                          <h3 class="box-title">Green Tag Sales List</h3>

                      </div><!-- /.box-header -->

                      <div class="box-body">

                          <div class="table-responsive">
                             <input type="hidden" id="emp_sales_incentive_gold_perg" value="<?php echo $emp_sales_incentive_gold_perg?>">
                             <input type="hidden" id="emp_sales_incentive_silver_perg" value="<?php echo $emp_sales_incentive_silver_perg?>">
                              <table id="dash_greentag_list" class="table table-bordered table-striped text-center">

                                  <thead>

                                      <tr>

                                          <th>S.No</th>

                                          <th>Bill No</th>

                                          <th>Customer Name</th>

                                          <th>Bill Date</th>

                                          <th>Product Name</th>

                                          <th>Piece</th>

                                          <th>Amount (Rs)</th>

                                          <th>Gross Wt (Gm)</th>

                                      </tr>

                                  </thead>
                                  <tbody>
                                      
                                  </tbody>
                                  <tfoot>
                                  <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                  </tfoot>
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