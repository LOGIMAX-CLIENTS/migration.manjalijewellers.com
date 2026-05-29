<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->   
    <!-- Main content -->
	<section class="content-header">
		<h1>
			Lot Vs Tagged Vault
			<small></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Retail Reports</a></li>
			<li class="active">Lot Vs Tagged Vault</li>
		</ol>
	</section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">              
               <div class="box box-primary">
			   		<div class="box-header with-border">
						<div class="col-md-2"> 
							<input type="text" class="form-control" id="lotno" placeholder="Lot No" value="" >  
						</div>
						<div class="col-md-2"> 
							<select id="karigar" class="form-control" style="width:100%;" multiple></select>
						</div>
						<div class="col-md-2"> 
							<select id="metal" class="form-control" style="width:100%;" multiple></select>
						</div>
						<div class="col-md-2"> 
							<select id="category" class="form-control" style="width:100%;" multiple></select>
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
								<button type="button" id="lotvstageed_issue" class="btn btn-info">Search</button>   
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
									<table id="lotvstag_list" class="table table-bordered table-striped text-center">
										<thead>
											<tr class="tablerow">
												<th colspan="4">Purchase</th>
												<th colspan="9">Lot Created</th>
												<th colspan="7">Tagged</th>
												<th colspan="7">Balance</th>
												<th colspan="4">Details</th>
											</tr>
                        				 	<tr>
											 	<th>Karigar</th>
												<th>Po RefNo</th>
												<th>Po Date</th>
												<th>Item</th>

												<th style="border-left: 1px solid black;">Lot No</th>
												<th >LotDate</th>
												<th>Lot pcs</th>
												<th>Lot Grs Wt</th>
												<th>Lot Net Wt</th>
												<th>Lot Dia Pcs</th>
												<th>Lot Dia Wt</th>
												<th>Lot Stone Pcs</th>
												<th>Lot Stone Wt</th>
												
												<th style="border-left: 1px solid black;">Tag Pcs</th>
												<th>Tag Grs Wt</th>
												<th>Tag Net Wt</th>
												<th>Tag Dia Pcs</th>
												<th>Tag Dia Wt</th>
												<th>Tag Stone Pcs</th>
												<th>Tag Stone Wt</th>
												
												<th style="border-left: 1px solid black;">Bal Pcs</th>
												<th>Bal Grs Wt</th>
												<th>Bal Net Wt</th>
												<th>Bal Dia Pcs</th>
												<th>Bal Dia Wt</th>
												<th>Bal Stone Pcs</th>
												<th>Bal Stone Wt</th>
												
												<th style="border-left: 1px solid black;">User</th>
												<th>From Tag</th>
												<th>To Tag</th>
												<th>Remarks</th>
            							    
            							  	</tr>
		                            	</thead> 
		                             	<tbody></tbody>
		                             	<tfoot></tfoot>
									</table>
								</div>
							</div> 
						</div> 
					</div><!-- /.box-body -->
					<div class="overlay" style="display:none">
						<i class="fa fa-refresh fa-spin"></i>
					</div> 
				</div>
            </div>			 
        </div><!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

