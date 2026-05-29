 <!--Customer modal-->



    <!-- / modal -->

    <div class="modal fade" id="confirm-add" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      <div class="modal-dialog">

        <div class="modal-content">

          <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

            <h4 class="modal-title" id="myModalLabel">Add Customer</h4>

          </div>

          <div class="modal-body">

            <ul class="nav nav-tabs">

              <li class="active"><a href="#tab_general" data-toggle="tab">GENERAL</a></li>

              <li><a href="#tab_kyc" data-toggle="tab">KYC</a></li>

            </ul>

            <div class="tab-content"><br />
              <?php
              $CI =& get_instance();
              $CI->load->model('ret_billing_model');
              $pan_verification_modal = $CI->ret_billing_model->get_ret_settings('pan_verification');
              ?>
              <input type="hidden" id="pan_verification_modal" value="<?php echo $pan_verification_modal; ?>">
              <div class="tab-pane active" id="tab_general">



                <div class="row">



                  <div class="form-group">



                    <label for="cus_first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error">*</span></label>



                    <div class="input-group">



                      <span class="input-group-addon">



                        <select name="title" id="title">



                          <option value="none" disabled="" hidden=""></option>



                          <option value="Mr" selected>Mr</option>



                          <option value="Ms">Ms</option>



                          <option value="Mrs">Mrs</option>



                          <option value="Dr">Dr</option>



                          <option value="Prof">Prof</option>



                        </select>



                      </span>



                      <input type="text" class="form-control" style="width:65%;" id="cus_first_name" name="cus[first_name]" placeholder="Enter customer first name" required="true">



                    </div>



                  </div>



                </div>



                <div class="row">



                  <div class="form-group">



                    <label for="cus_gender" class="col-md-3 col-md-offset-1 ">Gender<span class="error">*</span></label>



                    <div class="col-md-6">



                      <input type="radio" name="customer[gender]" value="0" class="minimal" <?php if ($customer['gender'] == 0) { ?> checked <?php } ?> required />Male



                      <input type="radio" name="customer[gender]" value="1" class="minimal" <?php if ($customer['gender'] == 1) { ?> checked <?php } ?> />Female



                      <input type="radio" name="customer[gender]" value="3" class="minimal" <?php if ($customer['gender'] == 3) { ?> checked <?php } ?> />Others







                      <p class="help-block cus_gender error"></p>



                    </div>



                  </div>



                </div>



                <div class="row">



                  <div class="form-group">



                    <label for="cus_mobile" class="col-md-3 col-md-offset-1 ">Mobile<span class="error">*</span></label>



                    <div class="col-md-6">



                      <input type="number" class="form-control" id="cus_mobile" name="cus[mobile]" placeholder="Enter customer mobile">



                      <p class="help-block cus_mobile error"></p>



                    </div>



                  </div>



                </div>



                <div class="row">



                  <div class="form-group">



                    <label for="cus_email" class="col-md-3 col-md-offset-1 ">Email</label>



                    <div class="col-md-6">



                      <input type="text" class="form-control" id="cus_email" name="cus[cus_email]" placeholder="Enter Email ID">







                      <p class="help-block cus_email error"></p>



                    </div>



                  </div>



                </div>



                <div class="row">



                  <div class="form-group">



                    <label for="" class="col-md-3 col-md-offset-1 ">Select Country<span class="error">*</span></label>



                    <div class="col-md-6">



                      <select class="form-control" id="country" style="width:100%;"></select>



                      <input type="hidden" name="cus[id_country]" id="id_country">



                    </div>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="" class="col-md-3 col-md-offset-1 ">Select State<span class="error">*</span></label>



                    <div class="col-md-6">



                      <select class="form-control" id="state" style="width:100%;"></select>



                      <input type="hidden" name="cus[id_state]" id="id_state">



                    </div>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="" class="col-md-3 col-md-offset-1 ">Select City<span class="error">*</span></label>



                    <div class="col-md-6">



                      <select class="form-control" id="city" style="width:100%;"></select>



                      <input type="hidden" name="cus[id_city]" id="id_city">



                    </div>







                  </div>



                </div></br>





                <div class="row">



                  <div class="form-group">



                    <label for="address1" class="col-md-3 col-md-offset-1 ">Address1<span class="error">*</span></label>



                    <div class="col-md-6">



                      <input class="form-control" id="address1" name="customer[address1]" value="" type="text" placeholder="Enter Address Here 1" required />



                      <p class="help-block address1 error"></p>



                    </div>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="address2" class="col-md-3 col-md-offset-1">Address2</label>



                    <div class="col-md-6">



                      <input class="form-control" id="address2" name="customer[address2]" placeholder="Enter Address Here 2" value="" type="text" />



                    </div>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="address3" class="col-md-3 col-md-offset-1">Address3</label>



                    <div class="col-md-6">



                      <input class="form-control titlecase" id="address3" name="customer[address3]" value="" type="text" placeholder="Enter Address Here 3" />



                    </div>



                  </div>



                </div></br>



                



                <div class="row">



                  <div class="form-group">



                    <label for="pincode" class="col-md-3 col-md-offset-1">Pin Code<span class="error">*</span></label>



                    <div class="col-md-6">



                      <input class="form-control titlecase" id="pin_code_add" type="text" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' required />



                      <p class="help-block pincode error"></p>



                    </div>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="" class="col-md-3 col-md-offset-1 ">Select Area<span class="error"></span></label>



                    <div class="col-md-5">



                      <select class="form-control" id="sel_village" style="width:100%;"></select>



                      <input type="hidden" id="id_village">



                    </div>



                    <span class="input-group-btn">



      									<button type="button" class="btn btn-success add_new_village"><i class="fa fa-plus"></i></button>



      								</span>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="" class="col-md-3 col-md-offset-1 ">Select Profession</label>



                    <div class="col-md-6">



                      <select class="form-control" id="profession" style="width:100%;"></select>



                      <input type="hidden" name="cus[profession]" id="professionval">



                    </div>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="pincode" class="col-md-3 col-md-offset-1">Date of Birth</label>



                    <div class="col-md-6">



                      <input class="form-control ed_date_of_birth" id="date_of_birth" name="customer[date_of_birth]" value="<?php echo set_value('customer[date_of_birth]', $customer['date_of_birth']); ?>" type="text" />



                      <p class="help-block pincode error"></p>



                    </div>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="pincode" class="col-md-3 col-md-offset-1">Wedding Date</label>



                    <div class="col-md-6">



                      <input class="form-control ed_date_of_wed" id="date_of_wed" name="customer[date_of_wed]" value="<?php echo set_value('customer[date_of_wed]', $customer['date_of_wed']); ?>" type="text" />



                      <p class="help-block pincode error"></p>



                    </div>



                  </div>



                </div></br>



                <div class="row">



                  <div class="form-group">



                    <label for="" class="col-md-3 col-md-offset-1 ">Upload Image<span class="error"></span></label>



                    <div class="col-md-6">



                      <input id="cus_image" name="cus_img" accept="image/*" type="file">



                      <p class="help-block cus_mobile"></p>







                      <input type="button" value="Take Snapshot" class="btn btn-warning" id="snap_shots"><br>







                      <div class="row">



                        <div class="col-md-12">



                          <div class="col-md-3"></div>



                          <div class="col-md-6" id="my_camera"></div>



                          <input type="hidden" name="image" class="image-tag">



                          <div class="col-md-3"></div>



                        </div>



                      </div>







                      <img src="<?php echo base_url('assets/img/default.png') ?>" class="img-thumbnail" id="cus_img_preview" style="width:175px;height:100%;" alt="Customer image">



                      <input type="hidden" id="customer_img" name="customer[customer_img]" value="<?php echo set_value('customer[customer_img]', $customer['cus_img']) ?>" />



                    </div>















                  </div>



                </div>



                



                <div class="row gst" style="display:none">



                  <div class="form-group">



                    <label for="" class="col-md-3 col-md-offset-1 ">GST No<span class="error">*</span></label>



                    <div class="col-md-6">



                      <input type="text" class="form-control" id="gst_no" name="cus[gst_no]" placeholder="Enter GST No">



                      <p class="help-block cus_mobile"></p>



                    </div>



                  </div>



                </div>

              </div>

              <div class="tab-pane" id="tab_kyc">



                <div class="row">

                  <div class="form-group">

                    <label for="cus_pan" class="col-md-3 col-md-offset-1 ">Pan</label>

                    <div class="col-md-6">

                      <div class="input-group">
																						<input type="text" class="form-control pan_no" name="billing[pan_no]" id="pan_no" value="<?php echo set_value('billing[pan_no]', isset($billing['pan_no']) ? $billing['pan_no'] : NULL); ?>">
																						<?php if (isset($pan_verification_modal) && $pan_verification_modal == 1) { ?>
																						<span class="input-group-btn">
 																							<button type="button" class="btn btn-primary btn-flat" id="btn_verify_pan" disabled><i class="fa fa-check-circle"></i> Verify</button>
 																						</span>
 																						<?php } ?>
																					</div>
																					<input type="hidden" id="pan_verified_flag" value="0">
																					<span id="pan_verify_status" style="font-size:12px; display:block; margin-top:3px;"></span>

                      <p class="help-block cus_email error" style="text-transform:uppercase"></p>

                    </div>

                  </div>

                </div>

                <div class="row">

                  <div class="form-group">

                    <label for="cus_aadhar" class="col-md-3 col-md-offset-1 ">Aadhar</label>

                    <div class="col-md-6">

                      <input type="text" class="form-control" id="aadharid" name="cus[cus_aadhar]" maxlength="14" placeholder="Enter aadhar ID">

                      <p class="help-block cus_email error"></p>

                    </div>

                  </div>

                </div>

                <div class="row">

                  <div class="form-group">

                    <label for="cus_dl" class="col-md-3 col-md-offset-1 ">Driving License</label>

                    <div class="col-md-6">

                      <input type="text" class="form-control dl_no" id="dl" name="cus[cus_dl]" maxlength="15" placeholder="Enter Driving License No">

                      <p class="help-block cus_email error"></p>

                    </div>

                  </div>

                </div>

                <div class="row">

                  <div class="form-group">

                    <label for="cus_dl" class="col-md-3 col-md-offset-1 ">PassPort</label>

                    <div class="col-md-6">

                      <input type="text" class="form-control pp_no" id="pp" name="cus[cus_pp]" maxlength="15" placeholder="Enter Passport No" style="text-transform:uppercase">

                      <p class="help-block cus_email error"></p>

                    </div>

                  </div>

                </div>

              </div>

            </div>

          </div>

          <div class="modal-footer">

            	<input type="hidden" name="cus[id_customer]" id="id_customer" value="">

            <a href="#" id="add_newcutomer" class="btn btn-success">Add</a>

            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>

          </div>



        </div>



      </div>



    </div>

    <!-- / modal -->



 <!--Customer modal-->

 

 

 

 

 <!-- area modal -->



 <div class="modal fade" id="confirm-area" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



      	<div class="modal-dialog">



      		<div class="modal-content">



      			<div class="modal-header">



      				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>



      				<h4 class="modal-title" id="myModalLabel">Add Village</h4>



      			</div>



      			<div class="modal-body">













      				<div class="row">



      					<div class="form-group">



      						<label for="pincode" class="col-md-3 col-md-offset-1">Pincode</label>



      						<div class="col-md-6">



      							<input class="form-control titlecase" id="new_pincode" type="text" placeholder="Enter Pincode" onkeypress='return  (event.charCode >= 48 && event.charCode <= 57)' readonly required />



      						</div>



      					</div>



      				</div></br>



      				<div class="row">



      					<div class="form-group">



      						<label for="area" class="col-md-3 col-md-offset-1 ">Area<span class="error">*</span></label>



      						<div class="col-md-6">



      							<input class="form-control" id="village" value="" type="text" placeholder="Enter Area Here " required />



      							<p class="help-block address1 error"></p>



      						</div>



      					</div>



      				</div></br>







      			</div></br>







      			<div class="modal-footer">



      			



      				<a href="#" id="add_new_area" class="btn btn-success">Add</a>



      				<button type="button" class="btn btn-warning new_village_close" data-dismiss="modal">Close</button>



      			</div>



      		</div>



      	</div>



      </div>





<!-- area modal -->