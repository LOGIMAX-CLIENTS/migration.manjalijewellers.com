

 <!-- Content Wrapper. Contains page content -->

 <div class="content-wrapper">

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box box-primary">

              <div class="box-header with-border">

                  <h3 class="box-title">Karigar Approval List</h3> 

                          

                </div><!-- /.box-header -->

				

                <div class="box-body">

                  <div class="row">



                  <div class="col-md-3"> 

                      <div class="form-group tagged">

                          <label>Approval For :<span class="error"></span></label>

                          <div class="form-group">

                          <input type="radio" class = "aprroval_for" id="wast_approval" name="app[approval_for]"      value="0" checked><label for="">&nbsp;&nbsp;Wastage</label>&nbsp;&nbsp;

    					  <input type="radio" class = "aprroval_for"  id="stn_approval"  name="app[approval_for]"     value="1" ><label for="">&nbsp;&nbsp;Stone</label>&nbsp;&nbsp;

                      </div> 

                      </div> 

                  </div>



                  </div> 

                <div class="row">

                        <div class="col-md-12">

                            <div class="col-md-6">  

                                <div class="box box-primary">  

                                    <div class="box-body"> 

                                        <div class="row">

                                            <div class="col-md-4"> 

                                                <div class="form-group tagged">

                                                    <label>Select karigar<span class="error"></span></label>

                                                    <select id="karigar_sel" class="form-control" style="width:100%;"></select>
                                                    
                                                    <input id="is_otp_verfied" type="hidden" name="approval_for[is_otp_verfied]" value="0" />

													                          <input id="send_resend" type="hidden" name="approval_for[send_resend]" value="0" />

                                                    <input id="resend_mobile" type="hidden" name="approval_for[resend_mobile]" value=""/>

                                                   <input id="otp_required" type="hidden" name="approval_for[otp_required]" value=<?php echo $otp_settings ?> />

                                                </div> 

                                            </div>

                                            <div class="col-md-2"> 

                                                <label></label>

                                                    <div class="form-group">

                                                        <button type="button" id="approval_search" class="btn btn-info">Search</button>   

                                                    </div>

                                            </div>

                                        </div>

                                        <!-- <div class="row" id="delete_row">

                                            <div class="col-md-2"> 

                                                <label></label>

                                                    <div class="form-group">

                                                        <button type="button" id="delete_product_mapping" class="btn btn-danger">Delete</button>   

                                                    </div>

                                            </div>

                                        </div> -->

                                    </div>

                                </div> 

                            </div>

                            

                            <div class="col-md-6">  

                                <div class="box box-primary">  

                                    <div class="box-body"> 

                                        <div class="row">

                                            <div class="col-md-4"> 

                                                <div class="form-group tagged">

                                                    <label>Select Status</label>

                                                    <select id="select_status" class="form-control" style="width:100%;">

                                                        <option value="1">Approve</option>

                                                        <option value="2">Reject</option>

                                                        <option value="3">Hold</option>

                                                    </select>

                                                </div> 

                                            </div>

                                            <div class="col-md-2"> 

                                                <label></label>

                                                    <div class="form-group">

                                                    <?php if($access['add']==1){?>

                                                        <button type="button" id="status_submit" class="btn btn-success">Submit</button>   
                                                        
                                                        <?php }?>
                                                    </div>

                                            </div>

                                        </div>

                                    </div>

                                </div> 

                            </div>

                        </div>

                   </div> 

                

                 <div class="table-responsive wastages" style="display:block;">

                    <table id="karigar_wastage_list" class="table table-bordered table-striped text-center">

                      <thead>

                        <tr>

                          <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>

                          <th>Karigar</th>

                          <th>Company</th>

                          <th>Category</th>

                          <th>Product</th>

                          <th>Design</th>

                          <th>Sub Design</th>

                          <th>Purity</th>

                          <th>Karigar Calc Type</th>

                          <th>Pur Touch</th>

                          <th>Calc Type</th>

                          <th>V.A Type</th>

                          <th>V.A(%)</th>

                          <th>V.A Wgt</th>

                          <th>MC Type</th>

                          <th>MC Value</th>

                          <th>Image</th>

                          <th>Charges</th>

                          <th>Stone Calc type</th>

                          <th>UOM</th>

                          <th>Quality Code</th>

                          <th>From Cent</th>

                          <th>TO Cent</th>

                          <th>Rate</th>

                          <th>Status</th>

                        </tr>

                      </thead>

                      <tbody><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tbody>

                    </table>

                  </div>



                  <div class="table-responsive stones" style="display:none;">

                    <table id="karigar_stones_list" class="table table-bordered table-striped text-center">

                      <thead>

                        <tr>

                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all_stn" name="select_all_stn" value="all"/>All</label></th>

                          <th>Karigar</th>

                          <th>Company</th>

                          <th>Stone Type</th>

                          <th>Stone Name</th>

                          <th>Uom</th>

                          <th>Calc Type</th>

                          <th>Rate</th>

                          <th>From Wt</th>

                          <th>To Wt</th>

                          <th>Quality Code</th>

                          <th>Clarity</th>

                          <th>Color</th>

                          <th>Cut</th>

                          <th>Shape</th>

                          <th>Status</th>

                          

                        </tr>

                      </thead>

                      <tbody><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tbody>

                    </table>

                  </div> 



                  <div class="table-responsive charges" style="display:none;">

                    <table id="karigar_charges_list" class="table table-bordered table-striped text-center">

                      <thead>

                        <tr>

                          <th>ID</th>

                          <th>Karigar</th>

                          <th>Charge Name</th>

                          <th>Charge Value</th>

                        </tr>

                      </thead>

                      <tbody><td></td><td></td><td></td><td></td></tbody>

                    </table>

                  </div> 

                 

                </div><!-- /.box-body -->

                 <div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

              </div><!-- /.box -->

            </div><!-- /.col -->

          </div><!-- /.row -->

        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->

      





<div class="modal fade" id="cus_chargeModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:50%;">

		<div class="modal-content">

			<div class="modal-body">

				<div class="row">

					<input type="hidden" id="charge_active_row" value="0">

					<table id="table_charges" class="table table-bordered table-striped text-center">

    					<thead>

        					<tr>

								<th>SNo</th>

            					<th>Charge Name</th>

            					<th>Calc Type</th>

            					<th>Charge</th>

            					<th>Action</th>

        					</tr>

    					</thead> 

    					<tbody></tbody>										

    					<tfoot><tr></tr></tfoot>

					</table>

			    </div>

		    </div>

		  <div class="modal-footer">

      <button type="button" id="update_charge_details" class="btn btn-success">Save</button>

			<button type="button" id="close_charge_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>





<div class="modal fade" id="imageModal_wastage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"

  aria-hidden="true" data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog" style="width:60%;">

      <div class="modal-content">

          <div class="modal-header">

              <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

          </div>

          <div class="modal-body">

			  <div class="row">

              	<div id="order_images" style="margin-top: 2%;"></div>

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




<div class="modal fade" data-backdrop="static" data-keyboard="false" id="vendor_otp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-dialog">

	<div class="modal-content">

		<div class="modal-header">

			<h4 class="modal-title" id="myModalLabel">Verify OTP and Update Status</h4>

		</div>

		<div class="modal-body">

			<div class="row">

				<div class="col-md-12">

					<h5>We have sent OTP to autorized mobile number. Kindly verify OTP to proceed further.</h5>

				</div>

			</div>

			<p></p>

			<div class="row otp_block">

				<div class="col-md-2">

					<div class='form-group'>

						<label for="">OTP</label>

					</div>

				</div>

				<div class="col-md-5">

					<div class='form-group'>

						<div class='input-group'>

							<input type="text" id="vendor_trns_otp" name="vendor_trns_otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required />

							<span class="input-group-btn">

								<button type="button" id="verify_vendor_otp" class="btn btn-primary btn-flat" disabled>Verify</button>

							</span>

						</div>

					</div>

				</div>

				<div class="col-md-2">

					<div class='form-group'>

						<input type="button" id="resend_vendor_otp" class="btn btn-warning btn-flat" value="Resend OTP" />

					</div>

				</div>

			</div>

			<div class="row">

				<div class="col-md-12">

					<span class="otp_alert"></span>

				</div>

			</div>

		</div>

		<div class="modal-footer">

			<button type="button" class="submit_vendor_approval btn btn-success btn-flat" disabled>Save And Submit</button>

			<button type="button" class="btn btn-danger btn-flat" data-dismiss="modal" id="close">Close</button>

		</div>

	</div>

</div>

</div>





