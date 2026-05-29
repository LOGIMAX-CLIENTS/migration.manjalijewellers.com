 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Old Metal PnL Report
            <small>Profit / Loss Analysis</small>
          </h1>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
                 <div class="box-body">  
				    <div class="row">
				  	<div class="col-md-offset-2 col-md-8">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
								<div class="col-md-4"> 
									 <div class="form-group">
            		                    <div class="input-group">
            		                        <br>
            		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">
            					            <span  style="display:none;" id="rpt_payments1"></span>
            					            <span  style="display:none;" id="rpt_payments2"></span>
            		                        <i class="fa fa-calendar"></i> Date range picker
            		                        <i class="fa fa-caret-down"></i>
            		                      </button>
            		                    </div>
            		                 </div><!-- /.form group -->
								</div>
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="old_metal_pnl_search" class="btn btn-info">Search</button>   
									</div>
								</div>
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="old_metal_pnl_print" class="btn btn-default"><i class="fa fa-print"></i> Print</button>   
									</div>
								</div>
								<div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="old_metal_pnl_export" class="btn btn-default"><i class="fa fa-file-excel-o"></i> Export</button>   
									</div>
								</div>
							</div>
					    </div>
	                   </div> 
	                  </div> 
                   </div>
			  
                  <div class="table-responsive">
	                 <table id="old_metal_pnl_table" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="5%">S.No</th>
	                        <th width="10%">Pocket No</th>
	                        <th width="12%">Pocket Date</th>
	                        <th width="12%">Total Old Gold Wt (g)</th>
	                        <th width="10%">Avg Purity (%)</th>
	                        <th width="14%">Expected Pure Wt (g)</th>
	                        <th width="14%">Actual Refined Pure Wt (g)</th>
	                        <th width="12%">Difference (g)</th>
	                        <th width="10%">Status</th>
	                      </tr>
	                    </thead>
	                    <tbody></tbody>
	                    <tfoot>
	                      <tr>
	                        <th colspan="3">Total</th>
	                        <th id="pnl_total_old_gold_wt"></th>
	                        <th id="pnl_total_avg_purity"></th>
	                        <th id="pnl_total_expected_pure_wt"></th>
	                        <th id="pnl_total_actual_refined_wt"></th>
	                        <th id="pnl_total_difference"></th>
	                        <th id="pnl_net_status"></th>
	                      </tr>
	                    </tfoot>
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
