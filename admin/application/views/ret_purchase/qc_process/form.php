      <!-- Content Wrapper. Contains page content -->

    <style>

    	.remove-btn{

			margin-top: -168px;

		    margin-left: -38px;

		    background-color: #e51712 !important;

		    border: none;

		    color: white !important;

		}

		

		

    </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

       



        <!-- Main content -->

        <section class="content order">



          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">QC Issue</h3>

            </div>

            <div class="box-body">

             <!-- form container --> 

	             <!-- form -->

				<form id="qc_entry_form">

				<div class="row">

				    <div class="col-md-12">

				        <div class="col-md-3">

    	                     <div class="form-group">

							  <label>PO Ref No</label>
    	                       	<div class="input-group">
									<select class="form-control" id="pur_fin_year_select" style="width:100px;">
										<?php
											foreach ($financial_year as $fin_year) { ?>
												<option value=<?php echo $fin_year['fin_year_code']; ?> <?php echo ($fin_year['fin_status'] == 1 ? 'selected' : '')  ?>><?php echo $fin_year['fin_year_name']; ?></option>
											<?php }
										?>
									</select>
									
									<select class="form-control" id="select_po_ref_no" style="width: 150px;"></select>
								</div>
    	                     </div> 

				        </div>

				        <div class="col-md-2">

    	                     <div class="form-group">

    	                       <label>Select Employee</label>

    							    <select class="form-control" id="emp_select"></select>

    	                     </div> 

				        </div>

				         <div class="col-md-2">

				            <div class="form-group">

				                <br>

				                <button id="qc_issue_submit" type="button" class="btn btn-primary" >UPDATE</button>
								<input type = "hidden" id="qc_process_id">

				            </div>

				        </div>

				    </div>

				</div>

				

				<div class="row">

					<div class="col-md-12">

					    <div class="table-responsive">

                         <h4>Item Details</h4>

                         <input type="hidden" id="custom_active_id" value="0">

						 <table id="item_detail" class="table table-bordered table-striped">

							<thead style="text-transform:uppercase;">

							     

						          <tr>

						            <th width="5%;"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th> 

						            <th width="5%;">Karigar</th> 

						            <th width="5%;">Product</th> 

						            <th width="5%;">Design</th> 

						            <th width="5%;">Sub Design</th> 

						            <th width="5%;">Pcs</th> 

						            <th width="5%;">Gwt</th> 

						            <th width="5%;">Lwt</th> 

						            <th width="5%;">Nwt</th> 

						            <th width="1%;">Action</th> 

						          </tr>

					         </thead>

					         <tbody></tbody>

					         <tfoot><tr style="font-weight:bold;"><td colspan="5" style="text-align: center;">TOTAL</td><td class="total_pcs"></td><td class="total_gwt"></td><td class="total_lwt"></td><td class="total_nwt"></td><td></td></tr></tfoot>

						</table>

					    </div>

					</div> 

				</div>	

				<p class="help-block"></p>

 

				  <?php echo form_close();?>

	           </div>  

	            <div class="overlay" style="display:none">

        		  <i class="fa fa-refresh fa-spin"></i>

        		</div>

	           

	       </div>  

        </section>

        

</div>





<div class="modal fade" id="cus_stoneModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:90%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>

			</div>

			<div class="modal-body">

    			<div class="row">

    			        <input type="hidden" id="activeRow">

    					<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">

        					<thead>

            					<tr>

                					<th width="5%">LWT</th>

                					<th width="10%">Type</th>

                					<th width="13%">Name</th>

									<th width="13%">Code</th>

                					<th width="10%">Pcs</th>   

                					<th width="17%">Wt</th>

                					<th width="10%">Cal.Type</th>

									<th width="10%">Cut</th>

									<th width="10%">Color</th>

									<th width="10%">Clarity</th>

									<th width="10%">Shape</th>

                					<th width="10%">Rate</th>

                					<th width="15%">Amount</th>

                					<th width="10%">Action</th>

                					

            					</tr>

        					</thead> 

        					<tbody></tbody>										

        					<tfoot>

        					    <tr></tr>

        					</tfoot>

    					</table>

    			</div>

		  </div>

		  <div class="modal-footer">

			<button type="button" id="update_issue_stn_details" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

            

