<style>

.content-header{

  padding: 5px 15px 0 3px !important;

}

.breadcrumb{

   margin-bottom: 10px !important;

}

.content{

	padding-top: 5px;

}

#btnExport{
	margin-top:10px;
}
.totals-summary {
  display: flex;
  justify-content: start; 
  align-items: center;
  gap: 40px; 
  margin-top: 15px;
  margin-left:10px;
  font-size: 16px;
  font-weight: bolder;

}

.totals-summary span {
  white-space: nowrap;
}

</style>

<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

  <!-- Content Header (Page header) -->

  <section class="content-header">

	  <div class="row col-md-12">

		  <div class="col-md-2">
		  </div>
		  
		  <div class="col-md-2">
                <?php   
                    $fromdt = date("d-m-Y");
                ?>
		      <input class="form-control available_stk_date" dateformat="d-M-y" value="<?php echo $fromdt?>"  type="text" />
		  </div>

		  <div class="col-md-2"> 

							  <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

						  

							  <div class="form-group tagged">

								  <select id="branch_select" class="form-control branch_filter" ></select>

							  </div> 

							  <?php }else{?>

								  <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

							  <?php }?> 

		  </div> 

		  

		  <div class="col-md-2">

			  <div class="form-group">

				  <select id="metal" style="width:100%;" multiple></select>

			  </div>

		  </div>

						  

		  <div class="col-md-2"> 

			  <div class="form-group">

				  <select id="category" style="width: 100%;" multiple></select>

			  </div> 

		  </div>

		 

	</div>

  </section>



  <!-- Main content -->

  <section class="content">

	<div class="row">

	  <div class="col-xs-12">

		 

		 <div class="box box-primary">

		  

		   <div class="box-body">  

			 <div class="box box-info stock_details">

						<div class="box-header with-border">

							  <div class="row">
							  
								  <div class="col-md-2 "> 
									  <div class="form-group ">
										  <select id="section_select" class="form-control" style="width:100%;" multiple></select>
									  </div>
								  </div>
								   
								<div class="col-md-2"> 

									  <div class="form-group">

										  <select id="prod_select" style="width: 100%;" multiple></select>

									  </div> 

								  </div> 

								  <div class="col-md-2"> 

									  <div class="form-group">

										  <select id="des_select" style="width: 100%;" multiple></select>

									  </div> 

								  </div>

								  <div class="col-md-2"> 
									  <div class="form-group">
										  <select id="sub_des_select" style="width: 100%;" multiple></select>
									  </div> 
								  </div>

								  

								  <div class="col-md-2"> 

									  <div class="form-group">

										  <select id="select_collection" style="width: 100%;" multiple></select>

									  </div> 

								  </div>

								  

								  <div class="col-md-1"> 

									  <div class="form-group"> 

										  <input type="number" id="from_weight" class="form-control" placeholder="From Weight">

									  </div> 

								  </div>

								  <div class="col-md-1"> 

									  <div class="form-group"> 

										  <input type="number" id="to_weight" class="form-control" placeholder="To Weight">

									  </div> 

								  </div>


								  </div>

								  <div class="row"> 

								  <div class="col-md-2"> 

									  <div class="form-group">

										  <select id="select_size" style="width: 100%;"></select>

									  </div> 

								  </div>

								  

								  <div class="col-md-2"> 

									  <div class="form-group">

										  <select id="karigar" class="form-control" style="width:100%;"multiple></select>

									  </div>

								  </div>

								  <div class="col-md-2"> 

									<div class="form-group">

										<select id="select_purity" class="form-control" style="width:100%;"></select>

									</div>

									</div>

								  <div class="col-md-2"> 

										  <div class="form-group">

										  <input type="number" id="from_lotno" class="form-control" placeholder="From Lot No">

										  </div>

								  </div>

								  <div class="col-md-2"> 

										  <div class="form-group">

										  <input type="number" id="to_lotno" class="form-control" placeholder="To Lot No">

										  </div>

								  </div>

																	  
							   <div class="col-md-2 groupBy" > 

								  <!-- <label>Group By</label> -->

								  <select id="group_by" class="form-control" style="width:100%;">

								  <option value="0">All</option>

								  <option value="1">Karigar</option>

								  </select>

							   </div>

							   </div>

							   <div class="row">

							   <div class="col-md-2"> 

											<div class="form-group">

											<input type="number" id="from_age" class="form-control" placeholder="From Age">

											</div>

											</div>

											<div class="col-md-2"> 

											<div class="form-group">

											<input type="number" id="to_age" class="form-control" placeholder="To Age">

											</div>

											</div>


										<div class="col-md-2"> 

											<div class="form-group">

											<input type="text" id="tag_huid" class="form-control" placeholder="Enter HUID">

											</div>

											</div>

								  <div class="col-md-2"> 

									  <div class="form-group">

										  <button type="button" id="tag_design_search" class="btn btn-info"><i class="fa fa-search"></i></button>   

									  </div>

								  </div>

							</div>

							</div>


							<div class="row align-items-center">

									<div class="col-md-11">
										<div class="totals-summary">
										<span id="total_count"></span>
										<span id="total_wt"></span>
										<span id="total_net_wt"></span>
										<span id="total_pure"></span>
										<span id="total_purchase_cost"></span>
										</div>
									</div>

									<div class="col-md-1 text-end">
										<div class="form-group mb-0">
										<button id="btnExport" onclick="fntaggedItemsExcelReport('1');" class="btn btn-success">
											<i class="fa fa-file-excel-o"></i>
										</button>
										</div>
									</div>

								</div>
	

						<div class="box-body collapse">

							<div class="row">

								<div class="box-body col-md-offset-2 col-md-8">

									<div class="table-responsive">

										<div>

											<table id="tag_item_branchwise"

												class="table table-bordered table-striped text-center">

												<thead>

													<tr>

														<th width="10%;">Branch</th>

														<th width="10%;">Piece</th>

														<th width="10%;">Gross Wt</th>

														<th width="10%;">Net Wt</th>

													</tr>

												</thead>

												<tbody>

													

												</tbody>

											</table>

										</div>

									</div>

								</div>

							</div>

						</div>

			  </div>

		  

			 

			 <div class="row">

				 <div class="col-md-12">

					 <div class="table-responsive">

				   <table id="tag_items_list" class="table table-bordered table-striped text-center">

					  <thead>

						<tr>

								<th width="10%;">S.No</th>

								<th width="10%;">Lot No</th>

								<th width="10%;">PO No</th>

								<th width="10%;">Order No</th>

							    <th width="10%;">Karigar</th>

							    <th width="10%;">Image</th>

							    <th width="10%;">Tag No</th>

							    <th width="10%;">Old Tag</th>

							    <th width="10%;">Tag Date</th>

							    <th width="10%;">Branch</th>

							    <th width="10%;">Category</th>

								 <th width="10%;">Purity</th>

							    <th width="10%;">Product</th>

							    <th width="10%;">Design</th>

							    <th width="10%;">Sub Design</th>

							    <th width="10%;">Collection</th>

								<th width="10%;">Section</th>

								<th width="10%;">HUID</th>

							    <th width="10%;">Pcs</th>

							    <th width="10%;">Gross Wgt</th>

							    <th width="10%;">Net Wgt</th>

								<th width="10%;">Purchase Cost</th>

								<th width="10%;">Purchase MC</th>
							    
								<th width="10%;">Purchase Wastage</th>

							    <th width="10%;">Pure Wgt</th>

								<th width="10%;">Stn pcs</th>

								<th width="10%;">Stn wt</th>

								<th width="10%;">Dia pcs</th>

								<th width="10%;">Dia wt</th>

							    <th width="1%">Size/Length</th>

							    <th width="10%;">Wastage</th>
							    <th width="10%;">Min V.A (%)</th>

							    <th width="10%;">Mc /G </th>

							    <th width="10%;">Mc /pcs </th>

							    <th width="10%;">Sales Value</th>

							    <th width="5%;">Attributes</th>

				 			    <th width="10%;">Cert No</th>

							    <th width="10%;">Style Code</th>

							    <th width="10%;">Tot Est</th>

								<th width="10%;">Tagged User Name</th>

								<th width="10%;">Age</th>

								<th width="10%;">Remarks</th>

								<th width="10%;">Is Green Tag</th>

						</tr>

					  </thead> 

					  <tbody></tbody>

				   </table>

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


<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

  <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

</div>

<div class="modal-body">

  <img src="" id="imagepreview" style="width: 300px; height: 264px;" >

</div>

<div class="modal-footer">

  <button type="button" class="btn btn-default danger" data-dismiss="modal">Close</button>

</div>

</div>

</div>

</div>