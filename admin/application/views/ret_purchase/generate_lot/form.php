      <!-- Content Wrapper. Contains page content -->

    <style>

    	

		.remove-btn{

			margin-top: -168px;

		    margin-left: -38px;

		    background-color: #e51712 !important;

		    border: none;

		    color: white !important;

		}

        .input-group {

                position :relative;

                display: inherit;

                border-collapse: separate;

            }

       .input-group .input-group-addon {

                border-radius: 0;

                border-color: #d2d6de;

                background-color: #fff;

                border-left: 1px solid #000;

                height: 34px;

            }

		

		

    </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

       



        <!-- Main content -->

        <section class="content order">



          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">Item Details</h3>

            </div>

            <div class="box-body">

             <!-- form container --> 

	             <!-- form -->

				<form id="lot_generate_form">

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
									<select class="form-control" name="lot[po_no]" id="select_po_ref_no" style="width:150px;" placeholder="Select Po No"></select>			
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


								<?php if($access['edit']==1){?>
				                <button id="generate_lot" type="button" class="btn btn-primary" >UPDATE</button>
								<?php }?>
				            </div>

				        </div>

				    </div>

				</div>

				<div class="row">

				        <div class="col-md-12">

				            <div class="col-sm-2"> 

    					 		 <label>Remark</label>	

    					 		<textarea class="form-control" id="remark" rows="2" cols="100"> </textarea>

    					 	</div>

						</div>

				</div></br>

				

				<div class="row">

					<div class="col-md-12">

					    <div class="table-responsive">

					        <input type="hidden" id="stone_active_id">

						 <table id="item_detail" class="table table-bordered table-striped">

							<thead style="text-transform:uppercase;">

						          <tr>

						            <th width="5%;"><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th> 

						            <th width="5%;">PO Ref No</th> 

						            <th width="5%;">QC Checked By</th> 

						            <th width="5%;">Section</th> 

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



<div class="modal fade" id="lgt_stoneModal" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:85%; margin: 30px auto;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Stone Details</h4>

			</div>

			<div class="modal-body">

    			<div class="row">

					   

<style>
#lgt_cus_item_details td, #lgt_cus_item_details th { padding: 4px 5px; font-size: 14px; vertical-align: middle !important; }
#lgt_cus_item_details .form-control { height: 35px; padding: 3px 6px; font-size: 14px; }
#lgt_cus_item_details .uom_id { font-size: 13px !important; width: 60px !important; }
#lgt_cus_item_details .input-group { flex-wrap: nowrap; }
#lgt_cus_item_details .input-group-btn { width: auto; }
#lgt_cus_item_details label { margin-bottom: 0; font-weight: normal; font-size: 14px; }
</style>

    					<table id="lgt_cus_item_details" class="table table-bordered table-striped table-condensed text-center" style="margin-bottom:5px;">

        					<thead>

            					<tr>

                					<th width="4%">LWT</th>

                					<th width="8%">Type</th>

                					<th width="9%">Name</th>

                					<th width="7%">Code</th>

                					<th width="5%">Pcs</th>

                					<th width="10%">Wt</th>

                					<th width="8%">Cal.Type</th>

                					<th width="9%">Cut</th>

                					<th width="7%">Color</th>

                					<th width="7%">Clarity</th>

                					<th width="7%">Shape</th>

                					<th width="7%">Rate</th>

                					<th width="7%">Amount</th>

                					<th width="5%">Action</th>

            					</tr>

        					</thead> 

        					<tbody>

							</tbody>										

        					<tfoot><tr style="font-weight:bold;">

							<td colspan="3">Total</td>

							<td></td>

							<td class="ps_tot_pcs"></td>

							<td class="ps_tot_wt"></td>

							<td></td>

							<td></td>

							<td></td>

							<td></td>

							<td></td>

							<td></td>

							<td class="ps_tot_amt"></td>

							<td></td>

							</tr></tfoot>

    					</table>

    			</div>

		  </div>

		  <div class="modal-footer">

		    <button type="button" id="save_lot_stone_details" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>  



<div class="modal fade" id="cus_stoneModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:72%;">

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

                					<th width="15%">#</th>

                					<th width="15%">Stone</th>

                					<th width="5%">Pcs</th>   

                					<th width="22%">Wt</th>

                					<th width="12%">Rate</th>

                					<th width="15%">Amount</th>

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

			<button type="button" id="remove_stone_details" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

            

