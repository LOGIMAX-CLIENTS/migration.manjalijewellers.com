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
						 <div class="col-md-3">  <h3 class="box-title">Supplier / Karigar Ledger</h3> </div>
						  <div class="col-md-4" style="display:none;"> 
						        <div class="form-group">
    						        <label class="control-label">Ledger Type : &nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    <input type="radio" id="typeAmount" name="ledger_type" value="1" checked tabindex="2"> <label for="typeAmount" > Amount &nbsp;</label>
                                    <input type="radio" id="typeMetal" name="ledger_type" value="2" tabindex="3"> <label for="typeMetal" > Metal &nbsp; </label>
                                </div>
							</div>
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
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
									  <table id="supplier_ledgere_list" class="table table-bordered table-striped text-center">
										 <thead>
                							  <tr>
                							    <th>Name</th>
                							    <th>Date</th>
                							    <th>Bill No</th>
                							    <th>Description</th>
                							    <th>Debit</th>
                							    <th>Credit</th>
                							    <th>Closing</th>
                							  </tr>
    		                             </thead> 
		                             <tbody></tbody>
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

