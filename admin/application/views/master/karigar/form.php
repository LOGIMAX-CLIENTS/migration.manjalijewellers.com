<style type="text/css">

.add_wastage {

	cursor: pointer;

	color: blue;

}

.add_karigar_wastage, .remove_karigar_wastage {

	text-align: center;

}

.remove_karigar_wastage {

	margin-left: 5px;

}

.title-add-wastage {

	padding-bottom: 15px;

    padding-top: 15px;

}

.karigar_wastage_buttons {

	padding: 0px;

	text-align: center;

}

.label_wastage_product, .label_wastage_design, .label_sub_design {

	width: 50% !important;

}

.select2 {

	width: 100% !important;

}

.save_btn{

	text-align: center;	

    padding-top: 75px;

}

.ord_img

  {

    padding:5px 10px;

    background:#605CA8;

    border:1px solid #605CA8;

    position:relative;

    color:#fff;

    border-radius:2px;

    text-align:center;

    float:left;

    cursor:pointer;

  }

  .order_images_new{

    position: absolute;

    z-index: 1000;

    opacity: 0;

    cursor: pointer;

    right: 0;

    top: 0;

    height: 100%;

    font-size: 24px;

    width: 100%;

  }

  

   input[type=number]::-webkit-inner-spin-button, 

    input[type=number]::-webkit-outer-spin-button { 

    -webkit-appearance: none;

    -moz-appearance: none;

    appearance: none;

    margin: 0; 

    }

</style>

</head>



<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">

<!-- Content Header (Page header) -->

	<section class="content-header">

		<h1>

		Karigar

		

		</h1>

		<ol class="breadcrumb">

		<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>

		<li class="active">Add Karigar</li>

		</ol>

	</section>

	<!-- Main content -->

	<section class="content">

		<!-- form -->

		<form id="karigar_general">

		<?php echo form_open_multipart(""); ?>

		<!-- Default box -->

		<div class="box">

			<div class="box-header with-border">

			<h3 class="box-title">Add User</h3>

			<div class="box-tools pull-right">

			<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

			<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

			</div>

			</div>

			<div class="box-body">

				<div class="col-md-12">  

					<ul class="nav nav-pills nav-stacked col-md-2">

						<li class="active"><a href="#tab_1" data-toggle="pill">General</a></li>

						<li class="vendor_det" style="display:none"><a href="#tab_2" data-toggle="pill">Contract Price</a></li>

						<li class="vendor_det" style="display:none"><a href="#tab_3" data-toggle="pill">Stone</a></li>

						<li class="vendor_det" style="display:none"><a href="#tab_4" data-toggle="pill">KYC</a></li>

					</ul>

					

					<div class="tab-content col-md-10">

						

						<div class="tab-pane active" id="tab_1"> 

							<div class="col-md-12">

							<div class="row" >

								<div class="col-md-offset-1 col-md-10" id='error-msg'></div>

							</div>

							<div class="row"> 

    							<div class="col-md-4"> 

    								      		<div class='form-group'>

    								               <label for="gender">User Type <span class="error">*</span></label>

    								                    <div class="form-group">

    													  <p class="help-block"></p>

    													    <input type="hidden" id="user_type" value="<?php echo $karigar['karigar_type']?>">

    														<input type="radio" class = "user_type" id = "click_label_individual" name="karigar[user_type]" value="0" <?php if($karigar['karigar_type']==0){ ?> checked <?php } ?>   ><label for="click_label_individual">&nbsp;&nbsp;Individual</label>&nbsp;&nbsp;

    														<input type="radio" class = "user_type" id = "click_label_company" name="karigar[user_type]" value="1" <?php if($karigar['karigar_type']==1){ ?> checked <?php } ?> ><label for="click_label_company">&nbsp;&nbsp;Company</label>

    														<input type="radio" class = "user_type" id = "click_label_company" name="karigar[user_type]" value="2" <?php if($karigar['karigar_type']==2){ ?> checked <?php } ?> ><label for="click_label_company">&nbsp;&nbsp;Firm</label>

    								         	   </div> 

    								            </div> 

    								</div> 

    								<div class="col-md-2"> 

    							      		<div class='form-group'>

    							               <label for="gender">User For <span class="error">*</span></label>

    							                    <div class="form-group">

    												  <p class="help-block"></p>

    												  <select class="form-control item-required" id="karigar_for" name="karigar[karigar_for]" required>

    												      <option value="1" <?php if($karigar['karigar_for']==1){ ?> selected <?php } ?> >Jewellery Manufacturer</option>

    												      <option value="2" <?php if($karigar['karigar_for']==2){ ?> selected <?php } ?>>Jewellery Supplier</option>

    												      <option value="5" <?php if($karigar['karigar_for']==5){ ?> selected <?php } ?>>Supplier & Manufacturer</option>

    												      <option value="3" <?php if($karigar['karigar_for']==3){ ?> selected <?php } ?>>Testing / HM Centers</option>

    												      <option value="4" <?php if($karigar['karigar_for']==4){ ?> selected <?php } ?>>Other Inventory Supplier</option>

    												  </select>

    							         	   </div> 

    							            </div> 

    								</div>

									

    								<div class="col-md-2"> 

							      		<div class='form-group'>

							               <label for="gender">Is TCS <span class="error">*</span></label>

							                    <div class="form-group">

												  <p class="help-block"></p>

												    <input type="hidden" id="is_tcs" value="<?php echo $karigar['is_tcs']?>">

												    

													<input type="radio" class = "is_tcs" id = "click_label_tcsyes" name="karigar[is_tcs]" value="1" <?php if($karigar['is_tcs']==1){ ?> checked <?php } ?>   ><label for="click_label_tcsyes">&nbsp;&nbsp;Yes</label>&nbsp;&nbsp;

													<input type="radio" class = "is_tcs" id = "click_label_tcsno" name="karigar[is_tcs]" value="0" <?php if($karigar['is_tcs']==0){ ?> checked <?php } ?> ><label for="click_label_tcsno">&nbsp;&nbsp;No</label>

							         	   </div> 

							            </div> 

    								</div> 

    								<div class="col-md-3"> 

							      		<div class='form-group'>

    						                <label for="first">TCS(%)<span class="error">*</span></label>

    						                <input class="form-control" id="tcs" name="karigar[tcs_tax]" type="number" step="any" value="<?php echo set_value('karigar[tcs_tax]',$karigar['tcs_tax']); ?>" placeholder="TCS (%)" <?php if($karigar['is_tcs']==0){ ?> disabled <?php } ?>/>

    						            </div>

    								</div> 

				      	 		

			                </div>	

		                	<div class='row'>

		                	    <div class="col-md-2"> 

							      		<div class='form-group'>

							               <label for="gender">Is TDS <span class="error">*</span></label>

							                    <div class="form-group">

												  <p class="help-block"></p>

												    <input type="hidden" id="is_tds" value="<?php echo $karigar['is_tds']?>">

												    

													<input type="radio" class = "is_tds" id = "click_label_tdsyes" name="karigar[is_tds]" value="1" <?php if($karigar['is_tds']==1){ ?> checked <?php } ?>   ><label for="click_label_tdsyes">&nbsp;&nbsp;Yes</label>&nbsp;&nbsp;

													<input type="radio" class = "is_tds" id = "click_label_tdsno" name="karigar[is_tds]" value="0" <?php if($karigar['is_tds']==0){ ?> checked <?php } ?> ><label for="click_label_tdsno">&nbsp;&nbsp;No</label>

							         	   </div> 

							            </div> 

    								</div> 



									<div class="col-md-3">

										<div class="form-group">

										<label for="first">Opening Balance Amount<span class="error">*</span></label>

										<div class="input-group" style="width:200px;">
										
											<input type="number" class="form-control" id="opening_bal_amt" name="karigar[opening_balance_amount]" value="<?php echo set_value('karigar[opening_balance_amount]',$karigar['opening_balance_amount']); ?>"  tabindex="11" style="width:120px;"><input type="hidden" class="form-control" id="bal_amount">
											
											<span class="input-group-btn">
											
											<select class="form-control" id="order_fin_year_select" name="karigar[fin_year_code]" style="width:100px;">
											
											<?php 
											foreach($financial_year as $fin_year)
											{?>

											<option value="<?php echo $fin_year['fin_year_code']; ?>" <?php echo ($fin_year['fin_year_code'] == $karigar['fin_year_code'] ? 'selected' : ''); ?>><?php echo $fin_year['fin_year_name']; ?></option>
										   	
											<?php }
											?>
											</select>
											</span>										
										</div> 
										</div> 

										</div>

    								<div class="col-md-3"> 

							      		<div class='form-group'>

    						                <label for="first">TDS(%)<span class="error">*</span></label>

    						                <input class="form-control" id="tds_tax" name="karigar[tds_tax]" type="number" step="any" value="<?php echo set_value('karigar[tds_tax]',$karigar['tds_tax']); ?>" placeholder="TDS(%)" <?php if($karigar['is_tds']==0){ ?> disabled <?php } ?>/>

    						            </div>

    								</div> 

									<?php if($this->uri->segment(3) == 'edit'){?>

										<div class="col-md-3 kar_calc" <?php echo $karigar['karigar_for']==1 ? 'style="display:block;"': 'style="display:none;"' ?>>

											<div class='form-group'>

												<label for="gender">Calc Type<span></span></label>

												<div class="form-group">

													<p class="help-block"></p>

													<select class="form-control" id="karigar_calc_type" name="karigar[karigar_calc_type]">

														<option value=""></option>

														<option value="1" <?php if($karigar['karigar_calc_type']==1){ ?> selected <?php } ?>>Pure Weight x Rate</option>

														<option value="2" <?php if($karigar['karigar_calc_type']==2){ ?> selected <?php } ?>>Purchase Touch</option>

														<option value="3" <?php if($karigar['karigar_calc_type']==3){ ?> selected <?php } ?>>Pure Weight x Wastage %</option>

													</select>

												</div>

											</div>	

										</div>

									<?php }else{?>

										<div class="col-md-3 kar_calc" style="display:none;">

											<div class='form-group'>

												<label for="gender">Calc Type<span></span></label>

												<div class="form-group">

													<p class="help-block"></p>

													<select class="form-control" id="karigar_calc_type" name="karigar[karigar_calc_type]">

														<option value=""></option>

														<option value="1" <?php if($karigar['karigar_calc_type']==1){ ?> selected <?php } ?>>Pure Weight x Rate</option>

														<option value="2" <?php if($karigar['karigar_calc_type']==2){ ?> selected <?php } ?>>Purchase Touch</option>

														<option value="3" <?php if($karigar['karigar_calc_type']==3){ ?> selected <?php } ?>>Pure Weight x Wastage %</option>

													</select>

												</div>

											</div>	

										</div>		

									<?php }?> 

									<div class="col-sm-2">	

    										<div class='form-group'>

    											<label for="scheme_code">Status</label>&nbsp;&nbsp;

    											<input type="checkbox" checked="true" class="alert-status" id="user" name="user" data-on-text="YES" data-off-text="NO" />

    											<input type="hidden" id="user_status" value="1">

    											<p class="help-block"></p>

    										</div>

    									</div>

		                	</div>

							<div class='row'>							       

						        <div class='col-sm-4'>

						            <div class='form-group'>

						                <label for="first">First Name<span class="error">*</span></label>

						                <input class="form-control item-required" id="first_name" name="karigar[first_name]" type="text" value="<?php echo set_value('karigar[firstname]',$karigar['firstname']); ?>" required placeholder="First Name" style="text-transform:uppercase"/>

						            </div>

						        </div>

						        <div class='col-sm-4'>

						            <div class='form-group'>

						                <label for="last">Last Name</label>

						                <input class="form-control input_text" id="last_name_karigar" name="karigar[last_name_karigar]"  type="text" value="<?php echo set_value('karigar[lastname]',$karigar['lastname']); ?>" placeholder="Last Name" style="text-transform:uppercase"/>

						            </div>

						        </div>

								<div class="col-sm-4" style="display:none;">

								    <div class='form-group'>

    					                <label for="code_number">Code<span class="error">*</span></label>

    					                <input class="form-control titlecase" id="karigar_code" name="karigar[karigar_code]"  type="text" placeholder="Code"  value="<?php echo set_value('karigar[code_karigar]',$karigar['code_karigar']); ?>"/>

						            </div>

						    	</div>

									

						        <div class='col-sm-4 registered_type'>

						            <div class='form-group'>

						                <label for="company">Company Name<span class="error">*</span></label>

						                <input class="form-control input_text" id="company_karigar" name="karigar[company_karigar]" type="text" placeholder="Company Name" value="<?php echo set_value('karigar[company]',$karigar['company']); ?>" style="text-transform:uppercase"/>

						            </div>

						        </div>	

						        <div class="col-sm-4 registered_type">

								    <div class='form-group'>

    					                <label for="gst_number_karigar">GST Number<span class="error">*</span></label>

    					                <input class="form-control" id="gst_number_karigar"  name="karigar[gst_number]"  type="text" placeholder="GST Number" value="<?php echo set_value('karigar[gst_number]',$karigar['gst_number']); ?>" style="text-transform:uppercase"/>

						            </div>

						    	</div>

						    

								

							     <div class='col-sm-4'>

							        <div class='form-group'>

						                <label for="phone">Mobile<span class="error">*</span></label>

						                <input class="form-control input_number"  name="karigar[mobile]" id="karigar_mobile"  type="text" placeholder="Mobile Number" value="<?php echo set_value('karigar[contactno1]',$karigar['contactno1']); ?>" />

						            </div>

					            </div> 	

                                <div class="col-sm-4">

							     	 <div class='form-group'>

						                <label for="email">Email</label>

						                <input class="form-control" id="email_karigar"  name="karigar[email]" type="text" placeholder="Email"  value="<?php echo set_value('karigar[email]',$karigar['email']); ?>" />

						            </div>

							     </div>									

						    </div>

						    <div class="row">

							      <div class="col-sm-4">

							    	<div class='form-group'>

						                <label for="phone">Phone</label>

						                <input class="form-control input_number" id="phone_karigar" name="karigar[phone]"  type="text" placeholder="Phone Number" value="<?php echo set_value('karigar[contactno2]',$karigar['contactno2']); ?>" />

						            </div>

							     </div>

								 <div class='col-sm-4'>

						            <div class='form-group'>

						                <label for="username" data-toggle="tooltip" title="Invalid characters 0-9"> User name</label>

						                <input class="form-control" id="user_name_karigar" name="karigar[user_name]"  type="text" placeholder="Username" value="<?php echo set_value('karigar[urname]',$karigar['urname']); ?>"/>

						            </div>

						        </div>	

							     <div class="col-sm-4"> 

					    			 <div class='form-group'>

						                <label for="password">Password</label>

						                <input class="form-control" id="password" name="karigar[password]"  type="text" placeholder="Password" value="<?php echo set_value('karigar[psword]',$karigar['psword']); ?>" />

						            </div>

							      </div>	

							      

								<div class="col-sm-4">

							      		<div class='form-group'>

							                <label for="address1">Address1<span class="error">*</span></label>

							                <input class="form-control titlecase" id="address1" name="karigar[address1]" type="text" placeholder="Address" value="<?php echo set_value('karigar[address1]',$karigar['address1']); ?>" required style="text-transform:uppercase"/>

							            </div>	

							        </div>

							        <div class="col-sm-4">	

							            <div class='form-group'>

							                <label for="address2">Address2</label>

							                <input class="form-control titlecase" id="address2_karigar" name="karigar[address2]"  type="text" placeholder="Address" value="<?php echo set_value('karigar[address2]',$karigar['address2']); ?>" style="text-transform:uppercase"/>

							            </div>

							        </div>

							        <div class="col-sm-4">	

										<div class='form-group'>

							                <label for="address3">Address3</label>

							                <input class="form-control titlecase" id="address3" name="karigar[address3]"  type="text" placeholder="Address" value="<?php echo set_value('karigar[address3]',$karigar['address3']); ?>" style="text-transform:uppercase"/>

							            </div>

							        </div>

									<div class="col-sm-4">	

							           <div class='form-group'>

							                <label for="country">Country<span class="error">*</span></label>

                          					 <select class="form-control item-required" id="country"  name="karigar[country]" placeholder="Enter Short Name" required></select>

											 <input id="id_country" type="hidden"  ></input>

											 <input id="ed_id_country" type="hidden" value="<?php echo $karigar['id_country'] ?>" ></input>

											 <input type="hidden" id="branch_id_state" name="">

											 <input type="hidden" id="branch_id_country" name="">

											 <input type="hidden" id="branch_id_city" name="">

							            </div>

							        </div>

									 <div class="col-sm-4">

							            <div class='form-group'>

							                <label for="state">State<span class="error">*</span></label>

							                <select class="form-control item-required edit_karigar" id="state" name="karigar[stateval]" required></select>

											<input id="id_state" type="hidden"></input>

											<input id="ed_id_state" type="hidden"  value="<?php echo ($karigar['id_state']); ?>" ></input>

							            </div>

							        </div>

									<div class="col-sm-4">	

							            <div class='form-group'>

							                <label for="city">City<span class="error">*</span></label>

							                <select class="form-control item-required" id="city" name="karigar[cityval]" required></select>

											<input id="id_city" type="hidden"></input>

											<input id="ed_id_city" type="hidden"  value="<?php echo ($karigar['id_city']); ?>"></input>

							            </div>

							        </div>

							    </div>

						   

							     <div class="row">

    								 	<div class="col-sm-4">	

    							            <div class='form-group'>

    							                <label for="pincode">Pincode<span class="error">*</span></label>

    							                <input class="form-control item-required" type="text" id="kar_pincode" name="karigar[pincode]" value="<?php echo set_value('karigar[pincode]',$karigar['pincode']); ?>" required/>

    							            </div>

    							        </div>

    						            <div class="col-md-4">

                    	                     <div class="form-group">

                    	                       <label>Narration</label>

                                                <textarea class="form-control" id="remarks" name="karigar[remarks]" rows="5" cols="500"><?php echo ($karigar['remarks']); ?> </textarea>

                    	                     </div> 

                				        </div>

    								    <!---->

                                        <div class="row">

									    <div class="col-sm-4">

                                            <div class="form-group">

                                                <label for="chargeseme_name">Upload image</label>

                                                <br>

                                                <input id="user_img" name="karigar[user_img]" accept="image/*" type="file" >

                                                <p class="help-block">File size should not exceed 1MB<br/>Image format should be .jpg or .png</p>

                                                <img src="<?php echo(isset($karigar['image'])? base_url().'assets/img/karigar/'.$karigar['image']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="user_img_preview" style="width:148px;height:100%;" alt="classfication image"> 

                                                <p class="help-block"></p>

                                            </div> 

                                        </div>

									</div>

								</div>	

								<div class="row save_btn">

									<div class="col-sm-12">

										<div class="box-default">

											<button type="button" id="add_newkar"  class="btn btn-primary">Save</button> 

											<button type="button" id="add_karNext"  class="btn btn-info">Save & Next</button> 

											<button type="button" class="btn btn-default btn-cancel">Cancel</button>

										</div>

									</div>			

								</div>			

							</div>				

						</div>

						<input type="hidden" name="karigar_id" id="id_karigar" value="<?php echo set_value('karigar[id_karigar]',$karigar['id_karigar']); ?>">

						<div class="tab-pane tab-wastage col-md-12" id="tab_2"> 

							<div class='row title-add-wastage'>

								Add Product ( <span class="add_wastage"><i class="fa fa-plus"></i></span> )

							</div>

							

							<div class="row">

    						 	<div class="table-responsive">

    						 	    <input type="hidden" id="i_increment">

                                      <table id="product_item_list" class="table table-bordered table-striped text-center">

                                            <thead>

                                              <tr>

                                                    <th width="10%;">All</th>

                                                    <th >Category</th> 

													<th >Purity</th>

                                                    <th style="width:10%;">Product</th> 

                                                    <th >Design</th> 

                                                    <th >Sub Design</th>


                                                    <th >Kar Calc Type</th>  

                                                    <th >Touch</th> 

                                                    <th >Calc Type</th> 

                                                    <th >V.A Type</th> 

                                                    <th class="va_perc" style="display:block;">V.A(%)</th>

                                                    <th class="va_wgt" style="display:none;">V.A Wgt</th>

                                                    <th>MC Type</th> 

                                                    <th>MC</th>

                                                    <th>Charges</th> 

													<th>Stone Calc Type</th>

													<th>UOM</th>

													<th>Quality Code</th>

													<th>From Cent</th>

													<th>To Cent</th>

													<th>Rate</th>

                                                    <th>Image</th> 

                                                    <th>Action</th> 

                                             </tr>

                                         </thead>

                                         <tbody></tbody>

                                      </table>

                                </div>

							</div>

							<div class="row save_btn">

								<div class="col-sm-12">

									<div class="box-default">

										<button type="button" id="add_NewcontractPrice"  class="btn btn-primary">Save</button> 

										<button type="button" id="add_contractPriceNext"  class="btn btn-info">Save & Next</button> 

										<button type="button" class="btn btn-default btn-cancel">Cancel</button>

									</div>

								</div>			

							</div>

						</div>

						<div class="tab-pane tab-stones" id="tab_3">

							<div class='row title-add-stone'>

								Add Stone ( <span class="add_stone"><i class="fa fa-plus"></i></span> )

							</div>

							<div class="row">

    						 	<div class="table-responsive">

    						 	    <input type="hidden" id="i_increment_stn">

                                      <table id="stone_item_list" class="table table-bordered table-striped text-center">

                                            <thead>

                                              <tr>

                                                <th>All</th>

                                                <th>Stone Type</th>

                                                <th>Stone Name</th>

                                                <th>UOM</th>

                                                <th>Calc Type</th>

                                                <th  style="width:10%;" >Code</th>

                                                <th  style="width:10%;" >From Cent</th>

                                                <th  style="width:10%;" >To Cent</th>

												<th>Rate</th>

												<th>Action</th>

                                              </tr>

                                         </thead>

                                         <tbody></tbody>

                                      </table>

                                </div>

    						</div>	

							<div class="row save_btn">

								<div class="col-sm-12">

									<div class="box-default">

										<button type="button" id="add_NewStone"  class="btn btn-primary">Save</button> 

										<button type="button" id="add_StoneNext"  class="btn btn-info">Save & Next</button> 

										<button type="button" class="btn btn-default btn-cancel">Cancel</button>

									</div>

								</div>			

							</div>					

						</div>

						<div class="tab-pane tab-stones" id="tab_4">



						<div class="col-md-12">

							<div class="col-md-12">

        							<div class='row title-add-kyc'>

        									Add Bank Details ( <span class="add_bank"><i class="fa fa-plus"></i></span> )

        							</div>

        

        							<div class="row">

            						 	<div class="table-responsive">

            						 	    <input type="hidden" id="nb_increment">

                                              <table id="bank_list" class="table table-bordered table-striped text-center">

                                                    <thead>

                                                      <tr>

                                                        <th>Bank Name</th>

                                                        <th>Account Holder Name</th>

                                                        <th>Account Number</th>

        												<th>IFSC Code</th>

                                                        <th>Action</th>

                                                      </tr>

                                                 </thead>

                                                 <tbody>

        

        										 <?php if($this->uri->segment(3) == 'edit')

                                                    {	

                                                        foreach($bank as $ikey => $ival){

                                                            echo '<tr id="bank_details">

                                                            <td><input class="form-control " id="bank_name"  name="bank[bank_name][]" type="text" placeholder="Bank Name"value="'.$ival['bank_name'].'"/></td>

                                                            <td><input class="form-control" id="acc_holder" type="text" name="bank[acc_holder][]"  type="text" placeholder="Account Holder Name" value="'.$ival['account_name'].'"/></td> 

                                                            <td><input class="form-control" id="acc_number"  name="bank[acc_number][]" type="text" placeholder="Account Number" value="'.$ival['account_number'].'" /></td>	

                                                            <td><input class="form-control" id="ifsc_code" name="bank[ifsc_code][]" type="text" placeholder="IFSC Code" value="'.$ival['ifsc_code'].'" style="text-transform:uppercase"/></td>

                                                            <td><a href="#" onClick="create_new_empty_bank_kyc_detail();"class="btn btn-success"><i class="fa fa-plus"></i><br></button><a href="#" onClick="$(this).closest(\'tr\').remove();" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>

                                                            </tr>';  

                                                        }

                                                    }?> 

        										 </tbody>

                                              </table>

                                        </div>

            						</div>

						    </div>

						<!-- Bank Details ends -->	



						<div class="col-md-12">

							<div class='row title-add-kyc'>

									Add KYC ( <span class="add_kyc"><i class="fa fa-plus"></i></span> )

							</div>



							<div class="row">

    						 	<div class="table-responsive">

    						 	    <input type="hidden" id="i_increment">

                                      <table id="kyc_list" class="table table-bordered table-striped text-center">

                                            <thead>

                                              <tr>

                                                <th>Proof Name</th>

                                                <th>ID</th>

                                                <th>Images</th>

												<th>Document</th>

                                                <th>Action</th>

                                              </tr>

                                         </thead>

                                         <tbody></tbody>

                                      </table>

                                </div>

    						</div>



						</div>

							<div class="row save_btn">

								<div class="col-sm-12">

									<div class="box-default">

										<button type="button" id="add_NewKyc"  class="btn btn-primary">Save</button> 

										<button type="button" class="btn btn-default btn-cancel">Cancel</button>

										<input type="hidden" name="kyc[image_type]" id="image_type" value="">

									    <input type="hidden" name="kyc[document_type]" id="document_type" value="">

									</div>

								</div>			

							</div>



						</div>

						</div>

					

					</div>  <!-- /Tab content --> 

				</div><!-- /.box-body -->

				<div class="overlay" style="display:none">

				  <i class="fa fa-refresh fa-spin"></i>

				</div>

		</div><!-- /.box -->

		<!--<div class="row">

		   <div class="box box-default"><br/>

			  <div class="col-xs-offset-5">

				<button type="button" id="add_newuser"  class="btn btn-primary">Save</button> 

				<button type="button" class="btn btn-default btn-cancel">Cancel</button>

			  </div> <br/>

			</div>

		</div> -->

		<?php echo form_close();?> 

	</section><!-- /.content -->

</div><!-- /.content-wrapper -->

<!--  Image Upload-->

<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Image</h4>

			</div>

			<div class="modal-body">

				<input type="file" name="order_images" id="order_images" multiple="multiple">

			</div></br>

			<div id="uploadArea_p_stn" class="col-md-12"></div>

		  <div class="modal-footer">

			<button type="button" id="update_img" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>





<div class="modal fade" id="front_imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

			    <input type="hidden" id="frimg_active_row" value="0">

				<h4 class="modal-title" id="myModalLabel">Add Image</h4>

			</div>

			<div class="modal-body">

				<input type="file" name="order_images_front" id="order_images_front" multiple="multiple">

			</div></br>

			<div id="uploadArea_front" class="col-md-12"></div>

		  <div class="modal-footer">

			<button type="button" id="update_img_front" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>





<div class="modal fade" id="back_imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Add Image</h4>

				<input type="hidden" id="bkimg_active_row" value="0">

			</div>

			<div class="modal-body">

				<input type="file" name="order_images_back" id="order_images_back" multiple="multiple">

			</div></br>

			<div id="uploadArea_back" class="col-md-12"></div>

		  <div class="modal-footer">

			<button type="button" id="update_img_back" class="btn btn-success">Save</button>

			<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

		  </div>

		</div>

	</div>

</div>







<div class="modal fade" id="docModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

    <div class="modal-dialog" style="width:60%;">

		<div class="modal-content">

			<div class="modal-header">

				<h4 class="modal-title" id="myModalLabel">Document Upload</h4>

			</div>

			<div class="modal-body">

				<div>	

					

					<input type="file" name="doc_file" id="doc_file">

					<input type="hidden" id="doc_active_row" value="0">	

					<input type="hidden" name="kyc_doc_file" id="kyc_doc_file" value="">	



				</div></br>

				<div class="row">

					<div class="col-md-9">

						<div class="col-md-12 box-items no-paddingwidth" style="max-height: 300px;overflow: auto;">

							<div class="col-md-12 col-xs-12 recent_bills no-paddingwidth blog-box">

								<div class="col-md-12 col-xs-12">

									<div class="col-md-12 col-xs-12 no-paddingwidth container-table">

										<table class="table table-bordered" id="kyc_document_pre">

											<thead>

												<tr>

													<th width="1%">FileName</th>

													<th width="2%">Action</th>

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

			</div>	

			<div class="modal-footer">

				<button type="button" id="update_doc" class="btn btn-success">Save</button>

				<button type="button" id="close_stone_details" class="btn btn-warning" data-dismiss="modal">Close</button>

			</div>

		</div>

	</div>

</div>





<div class="modal fade" id="Kar_wast_imageModal"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Image Upload</h4>

      </div>

      <div class="modal-body">

            <div class="row col-xs-8">

                    <div class="col-md-offset-1">

                        <div class="ord_img">

                            Add Image

                            <input type="hidden" id="wastImg_active_row" value="0">



                            <input id="wast_pro_images" class="order_images_new" name="order_images_new" accept="image/*" type="file" multiple="true">

                            <form id="subdesign_img_form">

                                 <input type="hidden" name="wastpro_img[id_karikar_wast]" id="id_karikar_wast">

                                <input type="hidden" name="wastpro_img[wast_pro_image]" id="wast_pro_image" value="">

                            </form>

                        </div>



						<div class="col-md-4">

                               <input type="button" value="Take Snapshot"  class="btn btn-warning" id="snap_shots"><br>

                            </div>

							<div class="row">

                       <div class="col-md-12">

                             <div class="col-md-3"></div>

                             <div class="col-md-6" id="my_camera"></div>

                             <input type="hidden" name="image" class="image-tag">

                             <div class="col-md-3"></div>

                      </div>

                  </div>

                    </div>

			    </div></br></br></br>

               	<div class="row">

					<div class="col-md-9">

						<div class="col-md-12 box-items no-paddingwidth" style="max-height: 300px;overflow: auto;">

							<div class="col-md-12 col-xs-12 recent_bills no-paddingwidth blog-box">

								<div class="col-md-12 col-xs-12">

									<div class="col-md-12 col-xs-12 no-paddingwidth container-table">

										<table class="table table-bordered" id="wast_img_preview">

											<thead>

											<tr>

												<th width="1%">#</th>

												<th width="1%">Img</th>

												<th width="2%">Action</th>

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

      </div>

      <div class="modal-footer">

      	<button type="button" class="btn btn-success" id="wastproimg_submit">Save</button>

        <button type="button" class="btn btn-warning" id="close_imgModal">Close</button>

      </div>

    </div>

  </div>

</div>





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

<script type="text/javascript">

var cust_id ="<?php echo $customer['id_customer']; ?>";   

var mob_no_len ="<?php echo $this->session->userdata('mob_no_len')?>";   

</script>