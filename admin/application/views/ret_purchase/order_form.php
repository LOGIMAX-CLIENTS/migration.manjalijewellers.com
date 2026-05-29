<!-- Content Wrapper. Contains page content -->

    <style>

    	.remove-btn{

			margin-top: -168px;

		    margin-left: -38px;

		    background-color: #e51712 !important;

		    border: none;

		    color: white !important;

		}

		.sm{

			font-weight: normal;

		}

		}

    </style>

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->





        <!-- Main content -->

        <section class="content order">



          <!-- Default box -->

          <div class="box box-primary">

            <div class="box-header with-border">

              <h3 class="box-title">PURCHASE ORDER</h3>

            </div>

            <div class="box-body">

             <!-- form container -->

	             <!-- form -->

				<form id="order_submit">

				<div class="row">

				    <div class="col-md-12">

				        <div class="col-md-4">

    	                     <div class="form-group">

    	                            <label>Order For<span class="error">*</span></label></br>

    	                            <input type="radio" id="stock_order" name="order[order_for]" value="1" checked="true" <?php echo ($purchase_details[0]['order_type']==1 ? 'checked' :'') ?>> <label for="stock_order" class="custom-label" > Stock Order </label>

    	                            <input type="radio" id="cus_order" name="order[order_for]" value="2" <?php echo ($purchase_details[0]['order_type']==2 ? 'checked' :'') ?>> <label for="cus_order" class="custom-label"> Customer Order </label>

    	                            <input type="radio" id="stock_repair_order" name="order[order_for]" value="3"<?php echo ($purchase_details[0]['order_type']==3 ? 'checked' :'') ?> > <label for="stock_repair_order" class="custom-label"> Stock Repair Order </label>

    	                     </div>

    			        </div>

				        <div class="col-md-2">

    	                     <div class="form-group">

    	                       <label>Select Karigar<span class="error">*</span></label>

    								<select id="select_karigar" class="form-control" name="order[id_karigar]" style="width:100%;" tabindex="1">></select>

    	                     </div>

				        </div>



				        <div class="col-md-2 stock_repair" style="display:none;">

    	                     <div class="form-group">

    	                       <label>Select Branch<span class="error">*</span></label>

    								<select id="branch_select" class="form-control" name="order[id_branch]" style="width:100%;" tabindex="1" disabled></select>

    								<input id="id_branch" name="lt_item[id_branch]" type="hidden" value="<?php echo set_value('po_item[id_branch]',$po_item['id_branch']); ?>" />

    	                     </div>

				        </div>



				        <div class="col-md-2">

    	                     <div class="form-group">

    	                       <label>Due Date<span class="error">*</span></label>

    							<input class="form-control datemask date smith_due_dt" name="order[smith_due_dt]" placeholder="Select Due Date" data-date-format="dd-mm-yyyy" type="text" tabindex="2"/>

    	                     </div>

    			        </div>



    			        <div class="col-md-2">

    	                     <div class="form-group">

    	                       <label>Customer Order No<span class="error">*</span></label>

    								<select id="select_order_no" name="order[id_customer_order]" class="form-control"  style="width:100%;" tabindex="3" disabled></select>

    	                     </div>



    			        </div>



    			        <div class="col-md-2 stock_and_cus_ord">

    	                     <div class="form-group"></br>

    	                       <button type="button" id="add_image" class="btn btn-primary" >Add Image</button>

    	                       <input type="hidden" id="order_iamges">

                               <!-- PUR-CLT02: Preview container for uploaded images -->
                               <div id="add_image_preview" style="margin-top:5px; display:flex; flex-wrap:wrap; gap:5px;"></div>

    			        </div>


    			     </div>


                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Rate Fixed</label><br>
                            <input type="checkbox" id="rate_fixed" name="order[rate_type]" value="1" style="transform: scale(1.3);">
                        </div>
                    </div>

                     <div class="col-md-2">
                        <div class="form-group">
                            <label>Rate Value</label>
                            <input type="number" step="any" id="rate_value" name="rate_value" class="form-control" style="width:100%;" value="<?php echo (isset($gold_rate) ? $gold_rate : ''); ?>" readonly>
                        </div>
                    </div>

    			 </div>

    			 <div class="row stock_and_cus_ord">

    			     <div class="col-md-12">

    			        <div class="col-md-2">

    	                     <div class="form-group">

    	                       <label>Select Product<span class="error">*</span></label>

    								<select id="select_product" class="form-control"  style="width:100%;" tabindex="4"></select>

    	                     </div>

    			        </div>



    			         <div class="col-md-2 design">

    	                     <div class="form-group">

    	                       <label>Select Design<span class="error">*</span></label>

    								<select id="select_design" class="form-control"  style="width:100%;" tabindex="5"></select>

    	                     </div>

    			        </div>

    			         <div class="col-md-2 sub_design">

    	                     <div class="form-group">

    	                       <label>Sub Design<span class="error">*</span></label>

    								<select id="select_sub_design" class="form-control"  style="width:100%;" tabindex="6"></select>

    	                     </div>

    			        </div>



    			        <div class="col-md-2 stock_order">

    	                     <div class="form-group">

    	                       <label>Select Weight Range<span class="error">*</span></label>

    								<select id="select_weight_range" class="form-control"  style="width:100%;" tabindex="7"></select>

									<input type="hidden" class="id_weight_range" id="id_weight_range" >

									<input id="id_customerorder" name="order[id_customerorder]" type="hidden" value="<?php echo $purchase_details[0]['id_customerorder']?>">

									<input type="hidden" id="id_karigar" value="<?php echo $purchase_details[0]['id_karigar']?>"/>

    	                     </div>

    			        </div>



    			        <div class="col-md-2 customer_order" style="display:none;">

    	                     <div class="form-group">

    	                       <label>Weight<span class="error">*</span></label>

    								<input id="order_weight" class="form-control"  style="width:100%;" tabindex="7"></select>



    	                     </div>

    			        </div>



    			        <div class="col-md-2 stock_order">

    	                     <div class="form-group">

    	                       <label>Select Size</label>

    								<select id="select_size" class="form-control"  style="width:100%;" tabindex="8"></select>

    	                     </div>

    			        </div>



    			        <div class="col-md-2 pcs">

    	                     <div class="form-group">

    	                       <label>Pcs<span class="error">*</span></label>

    							<input type="number" class="form-control" id="tot_pcs" tabindex="9">

    	                     </div>

    			        </div>



				    </div>

				</div>

				<div class="row">

				    <div class="col-md-12 remark">

				        <div class="col-sm-10">

				 			<div class="form-group">

							 <label>Note</label>

					 			<div class="input-group">

					 				<textarea class="form-control" id="remark" rows="5" cols="100"> </textarea>

								</div>

							</div>

				 		</div>

				    </div>

				</div>





				<div class="row">

				    <div class="col-sm-12" align="center">

						<button type="button" id="add_order_item" class="btn btn-primary">Add Item</button>

						<button type="button" id="order_item_cancel" class="btn btn-default btn-cancel">Cancel</button>

				    </div>

				</div>







				<div class="row">

					<div class="col-md-12">

					    <input type="hidden" id="i_increment">

					    <div class="table-responsive item_detailed" >

						<p class="help-block"></p></legend>

						 <table id="item_detail" class="table table-bordered table-striped">

						 <input type="hidden" id="custom_active_id">



							<thead>

						          <tr>

									<th width="15%;">Image</th>



						            <th width="15%;">Product</th>

						            <th width="15%;">Design</th>

						            <th width="15%;">Sub Design</th>

						            <th width="10%;">Size</th>

						            <th width="10%;">Wgt Range</th>

						            <th width="10%;">Approx Wt</th>

						            <th width="10%;">Pcs</th>

									<th width="10%;">Remarks</th>

						            <th width="10%;">Due Date</th>

						            <th width="10%;">Action</th>

						          </tr>

					         </thead>

					         <tbody>

    							 <?php if($this->uri->segment(3) == 'edit')

    							 foreach($item_details as $ikey => $ival){

                                    {

                                    	echo '<tr>

                                    			<td><div class="product_name">'.$ival['product_name'].'</div><input class="id_product" type="hidden" name="order_details[product][]" value="'.$ival['id_product'].'" /></td>

                                    			<td><div class="design_name">'.$ival['design_name'].'</div><input class="design_no" type="hidden" name="order_details[design][]" value="'.$ival['design_no'].'" /></td>

                                    			<td><div class="sub_design_name">'.$ival['sub_design_name'].'</div><input class="id_sub_design" type="hidden" name="order_details[sub_design][]" value="'.$ival['id_sub_design'].'" /></td>

                                    			<td><div class="size">'.$ival['size'].'</div><input class="id_size" type="hidden" name="order_details[size][]" value="'.$ival['id_size'].'" /></td>

                                    			<td><div class="weight_range">'.$ival['weight_range'].'</div><input class="id_weight_range" type="hidden" name="order_details[weight_range][]" value="'.$ival['id_weight_range'].'" /></td>

                                    			<td><div class="weight">'.$ival['weight'].'</div><input class="approx_wt" type="hidden" name="order_details[order_wt][]" value="'.$ival['weight'].'" /></td>

                                    			<td><div class="tot_items">'.$ival['tot_items'].'</div><input class="piece" type="hidden" name="order_details[piece][]" value="'.$ival['tot_items'].'" /></td>

                                    			<td><div class="smith_due_date">'.$ival['smith_due_date'].'</div><input type="hidden" name="order_details[due_date][]" value="'.$ival['smith_due_date'].'" /></td>

                                    			<td><a href="#" onClick="edit_purchaseorder_items($(this).closest(\'tr\'))" class="btn btn-success btn-edit"style="padding:5px;"  data-id ><i class="fa fa-edit" ></i></a><a href="#" onClick="remove_purchase_order_row($(this).closest(\'tr\'))" class="btn btn-danger btn-del btn-md" style="padding:5px;" ><i class="fa fa-trash"></i></a></td>

                                    	</tr>';

                                    	}

                                    }

        							?>

					         </tbody>



					         <tfoot>

					             <tr style="font-weight:bold;">



					                 <td colspan="6" style="text-align:center;">Total</td>

					                 <td><input type="hidden" class="order_wt" id="order_wt" name="order[order_wt]"><span class="tot_wt"></span></td>

					                 <td><input type="hidden" class="order_pcs" id="order_pcs" name="order[order_pcs]"><span class="tot_pcs"></span></td>

					                 <td></td>

									 <td></td>

					                 <td></td>

					             </tr>

					         </tfoot>

						</table>

					    </div>

					</div>

				</div>



				<div class="row">

					<div class="col-md-12">

					    <div class="table-responsive purchase_detailed" style="display:none">

						<p class="help-block"></p></legend>

						 <table id="purchase_details" class="table table-bordered table-striped">

							<thead>

						          <tr>

								    <th width="15%;">Image</th>

									<th class="tag_code" width="15%;">Tag Code</th>

						            <th width="15%;">Product</th>

						            <th width="15%;">Design</th>

						            <th width="15%;">Sub Design</th>

						            <th width="10%;">Size</th>

						            <th width="10%;">Pcs</th>

									<th width="10%;">GWT</th>

									<th width="10%;">LWT</th>

						            <th width="10%;">NWT</th>

						            <th width="10%;">Stone Value</th>

									<th width="10%;">Wastage %</th>

						            <th width="10%;">MC Value</th>

									<th width="10%;">Remarks</th>

						            <th width="10%;">Description</th>

						            <th width="10%;">Action</th>

						          </tr>

					         </thead>

					         <tbody>

    							 <?php if($this->uri->segment(3) == 'edit')

    							 foreach($item_details as $ikey => $ival){

                                    {

                                    	echo '<tr>

                                    			<td><div class="product_name">'.$ival['product_name'].'</div><input class="id_product" type="hidden" name="order_details[product][]" value="'.$ival['id_product'].'" /></td>

                                    			<td><div class="design_name">'.$ival['design_name'].'</div><input class="design_no" type="hidden" name="order_details[design][]" value="'.$ival['design_no'].'" /></td>

                                    			<td><div class="sub_design_name">'.$ival['sub_design_name'].'</div><input class="id_sub_design" type="hidden" name="order_details[sub_design][]" value="'.$ival['id_sub_design'].'" /></td>

                                    			<td><div class="size">'.$ival['size'].'</div><input class="id_size" type="hidden" name="order_details[size][]" value="'.$ival['id_size'].'" /></td>

                                    			<td><div class="weight_range">'.$ival['weight_range'].'</div><input class="id_weight_range" type="hidden" name="order_details[weight_range][]" value="'.$ival['id_weight_range'].'" /></td>

                                    			<td><div class="weight">'.$ival['weight'].'</div><input class="approx_wt" type="hidden" name="order_details[order_wt][]" value="'.$ival['weight'].'" /></td>

                                    			<td><div class="tot_items">'.$ival['tot_items'].'</div><input class="piece" type="hidden" name="order_details[piece][]" value="'.$ival['tot_items'].'" /></td>

                                    			<td><div class="smith_due_date">'.$ival['smith_due_date'].'</div><input type="hidden" name="order_details[due_date][]" value="'.$ival['smith_due_date'].'" /></td>

                                    			<td><a href="#" onClick="edit_purchaseorder_items($(this).closest(\'tr\'))" class="btn btn-success btn-edit"style="padding:5px;"  data-id ><i class="fa fa-edit" ></i></a><a href="#" onClick="remove_purchase_order_row($(this).closest(\'tr\'))" class="btn btn-danger btn-del btn-md" style="padding:5px;" ><i class="fa fa-trash"></i></a></td>

                                    	</tr>';

                                    	}

                                    }

        							?>

					         </tbody>



					         <tfoot>

					             <tr style="font-weight:bold;">

								 	<td class="tag_code" style="display:none;" ></td>

					                 <td colspan="5" style="text-align:center;">Total</td>

					                 <td><input type="hidden" class="order_pcs" id="order_pcs" name="order[order_pcs]"><span class="tot_pcs"></span></td>

									 <td><input type="hidden" class="order_wt" id="order_wt" name="order[order_wt]"><span class="tot_wt"></span></td>

									 <td></td>

					             </tr>

					         </tfoot>

						</table>

					    </div>

					</div>

				</div>









				<p class="help-block"></p>





				<div class="row">

				    <div class="col-sm-12" align="center">

						<button type="button" id="create_order" class="btn btn-primary">save</button>

						<button type="button" class="btn btn-default btn-cancel">Cancel</button>

				    </div>

				</div>



	           </div>

	            <?php echo form_close();?>



	       </div>

	       <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

        </section>

    </div>





<!--  Image Upload-->

<div class="modal fade" id="imageModal_new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Image</h4>

			</div>

			<div class="modal-body" style="height: 200px;">

           <div class="ord_img">

              Add Image

            <input id="order_images_new" class="order_images_new" name="order_images_new" accept="image/*" type="file" multiple="true">

        </div>

        <br>

         <div id="order_images" style="margin-top: 2%;"></div>

		</div>



		  <div class="modal-footer">

			<button type="button" id="update_img_new" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details_new" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>

</div>





<!--  Image Upload-->



<div class="modal fade" id="order_des" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



  	<div class="modal-dialog">



    	<div class="modal-content">



      		<div class="modal-header">



        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>



        		<h4 class="modal-title" id="myModalLabel">Add Description</h4>



      		</div>



      		<div class="modal-body">



				<div class="row">



        			<div class="col-md-10 col-md-offset-1">



        				<label for="user_lastname">Item Description</label>



						<div class='form-group'>



			               	<textarea  cols="70"  id="description" name="description" ></textarea>



			        	</div>



			    	</div>



			    </div>



			</div>



			<div class="modal-footer">



				<a href="#" class="btn btn-success add_order_desc">Add</a>



				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>



			</div>



		</div>



	</div>



</div>









<div class="modal fade" id="imageModal_bulk_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">

      <div class="modal-dialog" style="width:90%;">

          <div class="modal-content">

              <div class="modal-header">

                  <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

              </div>

              <div class="modal-body">

    			  <div class="row">

                  	<div id="order_images_preview" style="margin-top: 2%;"></div>

    			  </div>

              </div>

              <div class="modal-footer">

                  </br>

                  <button type="button" id="close_stone_details" class="btn btn-warning"

                      data-dismiss="modal">Close</button>

              </div>

          </div>

      </div>

</div>





<div class="modal fade" id="imageModal_preview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog" style="width: 90%;">

        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div id="modalImageContainer" style="margin-top: 2%;">

                        <img id="modalImage" src="" width="20%" height="auto" />

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

            </div>

        </div>

    </div>

</div>


<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Image Preview</h4>
			</div>
			<input type="file" name="pur_order_images" id="pur_order_images" multiple="multiple">
			<input type="hidden" id="active_row">
			<div class="modal-body">
			<div id="uploadArea_p_stn" class="col-md-12"></div>

			</div>

		  <div  class="modal-footer">

		  	<button type="button"  id="update_pur_img" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>



<div class="modal fade" id="cus_stoneModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog" style="width:72%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Add Stone</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="box-tools pull-right">
					</div>
				</div>
				<div class="row">
					<table id="estimation_stone_cus_item_details" class="table table-bordered table-striped text-center">
						<thead>
							<tr>
								<th width="5%">LWT</th>
								<th width="15%">Type</th>
								<th width="15%">Name</th>
								<th width="10%">Pcs</th>
								<th width="20%">Wt</th>
								<th width="10%">Cal.Type</th>
								<th width="15%">Rate</th>
								<th width="17%">Amount</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
		  	</div>
		  	<div class="modal-footer">
				<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>
		  	</div>
		</div>
	</div>
</div>
