  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
       
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
                 <div class="box-body">  
                	   	<div class="box box-info stock_details">
						<div class="box-header with-border">
						 <div class="col-md-3">  <h3 class="box-title">Smith Transactions</h3> </div>
						  
							<div class="col-md-2"> 
								<select id="karigar" class="form-control" style="width:100%;"></select>
							</div>
							<div class="col-md-2"> 
								<div class="form-group">    
			
									<?php   
										$fromdt = date("d/m/Y");
										$todt = date("d/m/Y");
								    ?>
		                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
								</div> 
							</div>
							 <div class="col-md-2"> 
                                 <div class="form-group">
                                     <button type="button" id="smith_transactions" class="btn btn-info">Search</button>   
                                 </div>
                             </div>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="smith_ledgere_list" class="table table-bordered table-striped text-center">
										 <thead>
                							  <tr>
											        <th rowspan="2">Date</th>
											        <th rowspan="2">Ref No</th>
											        <th rowspan="2">Voucher Type</th>
													<th colspan="3">Metal</th>
													<th colspan="3">Amount</th>
                							  </tr>
            							  <tr>
            							    <th>Debit</th>
            							    <th>Credit</th>
            							    <th>Closing</th>
            							    
            							    <th>Debit</th>
            							    <th>Credit</th>
            							    <th>Closing</th>
            							  </tr>
		                            </thead> 
		                             <tbody></tbody>
		                             <tfoot>
											<tr>
											    <th style="text-align:left">Total : </th>
											    <th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												
											</tr>
										</tfoot>
									 </table>
								  </div>
								</div> 
							</div> 
						</div>
					</div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

