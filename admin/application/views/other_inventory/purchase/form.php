<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

<!-- Content Header (Page header) -->

	<section class="content-header">

		<h1>

		Purchase Details

		</h1>

		<ol class="breadcrumb">

		<li><a href="#"><i class="fa fa-dashboard"></i>Master</a></li>

		<li class="active">Purchase details</li>

		</ol>

	</section>

     <!-- Default box -->

    <section class="content">

      <form id="inventory_entry">  

		<div class="box">

			<div class="box-header with-border">

              <h3 class="box-title">Supplier Details</h3>

                <div class="box-tools pull-right">

                 <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                 <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>



                </div>

            </div> 

            <div class="box-body">

				    <div class='row'>	

                        <div class="col-md-2">

				            <div class='form-group'>

				                <label>Supplier Name<span class="error"> *</span></label>

								<select  class="form-control" name="purchase[id_karigar]" id="select_karigar" required="true" style="width:100%;"></select>

							</div>

				        </div>		

			

                        <div class="col-md-2">

					    	<div class='form-group'>

					           <label>Supplier Bill No<span class="error"></span></label>

							   <input id="sup_refno"  class="form-control" name="purchase[sup_refno]" type="text"  placeholder="Enter Ref No" value="" autocomplete="off"/>							      

							</div>

						</div>

						<div class="col-md-2">

					    	<div class='form-group'>

					           <label>Supplier Bill Date<span class="error"></span></label>

							   <input id="sup_billdate"  class="form-control" name="purchase[sup_billdate]" type="Date"  placeholder="" value="" autocomplete="off"/>							      

							</div>

					    </div>

					    <!-- <div class="col-md-2">

					    	<div class='form-group'>

					           <label>Bill Copy<span class="error"></span></label>

							   <input id="pur_bill_img" name="purchase[pur_bill_img]" accept="image/*" type="file" >

							</div>

					    </div> -->
				
						<div class="col-sm-2 image_block">
                                  <label>Bill Copy</label>
                                 <div>
								   <input type="hidden" class="form-control" id="cmp_state" value="<?php echo ($comp_details['id_state'] != '' ? $comp_details['id_state'] : 0); ?>"/>
								   <input type="hidden" id="kar_state" value=''>
								   <input type="hidden" id="custom_active_id">
                                    <div id="tag_img" data-img='[]'></div>
                                        <input type="hidden" class="form-control" id="tag_img_copy"/>
                                        <input type="hidden" class="form-control" id="tag_img_default"/>
                                        <input type="hidden" class="form-control" id="tag_images"/>
                                        <a href="#" onclick="grn_update_image_upload();" class="btn btn-default btn-sm"><i class="fa fa-plus"></i></a>
                                
									</div>
                                
								</div>

					</div>

                </div>

                

            <div class="box-header with-border">

                    <h3 class="box-title">Item Details</h3>

            </div> 

            <div class="box-body">

				    <div class='row'>	

                        <div class="col-md-2">

				            <div class='form-group'>

				                <label>Select Item<span class="error"> *</span></label>

								<select  class="form-control" id="select_item"  style="width:100%;"></select>

							</div>

				        </div>		

			

                        <div class="col-md-2">

					    	<div class='form-group'>

					           <label>Quantity<span class="error">*</span></label>

							   <input id="buy_quantity"  class="form-control"  type="number"  placeholder="Quantity" value="" autocomplete="off"/>							      

							</div>

						</div>

						

						<div class="col-md-2">

					    	<div class='form-group'>

					           <label>Rate/Pcs<span class="error">*</span></label>

							   <input id="buy_rate"  class="form-control"  type="number"  placeholder="Rate" value="" autocomplete="off"/>							      

							</div>

						</div>

		


						<div class="col-md-2">

					    	<div class='form-group'>

					           <label>Taxable Amount<span class="error">*</span></label>

							   <input id="buy_amount"  class="form-control"  type="number"  placeholder="Amount" readonly value="" autocomplete="off"/>							      

							</div>

						</div>


						<div class="col-md-2">

						<div class='form-group'>

						<label>GST %<span class="error"></span></label>

						<input id="tax_amount"  class="form-control"  type="number"  placeholder="GST"  value="" />	

						<input id="pur_gst_amount"  class="form-control"  type="hidden"  value="" />							      
				      
						</div>

						</div>

						

						<div class="col-md-2">

						<div class='form-group'>

						<label>Total Amount<span class="error">*</span></label>

						<input id="gst_amount"  class="form-control"  type="number"  placeholder="Amount" readonly value="" autocomplete="off"/>							      

						</div>

						</div>
						

						<div class="col-md-2">

					    	<div class='form-group'>

					           </br>

							   <button id="add_item_info" type="button" class="btn btn-success pull-left"><i class="fa fa-plus"></i> Add item</button>

							</div>

						</div>

						

						

						

					</div>

                </div>   

            

                    <div class="box-body">

                        <div class="row">

                    <table id="pur_details" class="table table-bordered table-striped text-center">

                    <thead>

                      <tr>

					    <th>Item name</th>

                        <th>Quantity</th>

                        <th>Rate</th>

                        <th>Taxable Amount</th>

						<th>GST %</th>

						<th>CGST %</th>

						<th>SGST %</th>

						<th>IGST %</th>

						<th>GST Amount </th>

						<th>Total Amount</th>

                        <th>Action</th>

                      </tr>

                 	</thead>

                     <tbody>

                    
                     </tbody>
					<tfoot>
						<tr style="font-weight:bold;">
							<td></td> 
							<td  style="text-align:right"class='pur_quantity'></td>
							<td></td> 
							<td  style="text-align:right"class='pur_amount'></td> 
							<td></td> 
							<td></td> 
							<td></td> 
							<td></td> 
							<td></td> 
							<td  style="text-align:right"class='pur_gst_amount'></td> 
							<td></td> 
						</tr>
					</tfoot>
                  </table>

                   </div>


		            <div class="row">

		                <div class="box box-default"><br/>

			                <div class="col-xs-offset-5">

				               <button type="button" id="inventory_submit"  class="btn btn-primary">save</button> 

				               <button type="button" class="btn btn-default btn-cancel">Cancel</button>

				            </div> <br/>

			            </div>

		            </div> 

            </div>

        </form>

    </section>

</div>




<div class="modal fade" id="grn_imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" style="width:60%;">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title" id="myModalLabel">Add Image</h4>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-md-12">
                      <div class="col-md-8">
                          <label>Note - Click Snapshot Button To Take Your Images Screen Shot</label>
                          <input id="bulktag_images" class="bulktag_images" name="bulktag_images" accept="image/*" type="file" multiple="true">
                      </div>
                      <div class="col-md-4">
                          <input type="button" value="Take Snapshot" onClick="take_snapshot('pre_images')"
                              class="btn btn-warning" id="snap_shots"><br>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-md-12">
                      <div class="col-md-3"></div>
                      <div class="col-md-6" id="my_camera"></div>
                      <input type="hidden" name="image" class="image-tag">
                      <div class="col-md-3"></div>
                  </div>
              </div>
              <div class="row" id="image_lot_list" style="display:none;">
                  <div class="col-md-12" style="font-weight:bold;"> Images List</div>
              </div><br>
              <div class="row">
                  <div class="col-md-12" id="uploadArea_p_stn"></div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" id="grn_update_img" class="btn btn-success">Save</button>
              <button type="button" id="close_stone_details" class="btn btn-warning"
                  data-dismiss="modal">Close</button>
          </div>
      </div>
  </div>
</div>

     