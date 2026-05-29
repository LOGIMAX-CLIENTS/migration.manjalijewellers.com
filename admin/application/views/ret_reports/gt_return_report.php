<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Green Tag Return Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Stock Ageing</a></li>
            <li class="active">Green Tag Return Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Green Tag Return Report</h3>    <span id="Green_Tag_Return_Report" class="badge bg-green"></span>
                           
                </div><!-- /.box-header -->
                <div class="box-body">
               
               
				
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					 
					</div>
				  </div>
						
                <div class="table-responsive">
                    <input type="hidden" id="emp_sales_incentive_gold_perg" value="<?php echo $emp_sales_incentive_gold_perg;?>">
                    <input type="hidden" id="emp_sales_incentive_silver_perg" value="<?php echo $emp_sales_incentive_silver_perg;?>">
                  <table id="gt_return_report_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr> 
											<th>Bill No</th>
											<th>Est No</th>
					                        <th>Bill Date</th>
											<th>Tag Date</th>
											<th>Tag Code</th>
											<th>Return Date</th>
                                            <th>G.wt</th>
            							    <th>N.wt</th>
											<th>Incentive Amt</th>
											<th>Product Name</th>
            							    <th>Item Cost</th>
            							    <th>Employee</th>
            							    <th>Emp Code</th>
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