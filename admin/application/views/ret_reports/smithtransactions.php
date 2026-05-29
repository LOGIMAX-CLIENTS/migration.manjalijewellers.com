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

									 

					<div class="col-md-2"> 

						<select id="metal" class="form-control" style="width:100%;" multiple></select>

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

					<div class="col-md-2"> 

						<select id="stoneType" class="form-control" style="width:100%;">

							<option selected value="0"> Pure(Grm) </option>

							<option value="1"> Stone(Grm) </option>

							<option value="2"> Diamond(CT) </option>

						</select>

					</div>

					<div class="col-md-2"> 
						<input type="checkbox" id="group_by_required" class="group_by_required" value="1"> 
						<label for="group_by_required"> Group By Item</label>
					</div>
					
					 <div class="col-md-2"> 

						 <div class="form-group">

							 <button type="button" id="smith_ledger_search" class="btn btn-info">Search</button>   

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

								 <tr class="tablerow">

									  <th colspan="3"></th>

									  <th colspan="5">Issue</th>

									<th colspan="5">Receipt</th>

									<th colspan="2">Amount</th>

									<th colspan="5">Balance</th>

									<th colspan="3"></th>

								  </tr>

								  <tr>

									<th>Particulars</th>

									<th>Tran No</th>

									<th>Tran Date</th>



									<th>Issue Pcs</th>

									<th>IssueGross Wt</th>

									<th>IssueNet Wt</th>

									<th>Issue Pure Wt</th>

									<th>Issue UOM</th>



									<th>Receipt Pcs</th>

									<th>Receipt Gross Wt</th>

									<th>Receipt Net Wt</th>

									<th>Receipt Pure Wt</th>

									

									<th>Receipt UOM</th>



									<th>Amount Debit</th>

									<th>Amount Credit</th>

									

									<th>Blc Pcs</th>

									<th>Blc Gross Wt</th>

									<th>Blc Net Wt</th>

									<th>Blc Pure Wt</th>

									<th>Blc Amt</th>

									

									

									<th>Touch</th>

									<th>Rate</th>

									<th>Narration</th>







									

								  </tr>

							</thead> 

							 <tbody></tbody>

							 <tfoot></tfoot>

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



