      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Settings
            <small>App Configuration  Setting</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> App Configuration settings</a></li>
            
          </ol>
        </section> 
        <!-- Main content -->
        <section class="content">  
          <!-- Default box -->
          <div class="box">
            <div class="box-header">
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <!-- <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button> -->
              </div>
            </div>
            <div class="box-body">
            <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
	                    <?php echo $message['message']; ?>
	                  </div>
	                  
	            <?php } ?>  
            <div class="col-md-12">
			

			  
			  <!-- General Settings content -->
			  
			<!-- <ul class="nav nav-pills nav-stacked col-md-2"> -->

		<!-- branch settings-->   
				
				
			  
			 
				 
				  <!-- <li><a href="#tab_config" data-toggle="pill">Config Settings</a></li> -->
			  
			<!-- </ul> -->
            <?php 

                echo form_open( 'admin_settings/config_setupdate'); 

           ?> 

							<h4 class="page-header">App Custom Fields</h4>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">Email</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_email_no" name="config[app_cus_email]" value="0" <?php if($config['app_cus_email'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_email_yes" name="config[app_cus_email]" value="1" <?php if($config['app_cus_email'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_email_yes_r" name="config[app_cus_email]" value="2" <?php if($config['app_cus_email'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">Address1(street)</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_strt_no" name="config[app_cus_address1]" value="0" <?php if($config['app_cus_address1'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_strt_yes" name="config[app_cus_address1]" value="1" <?php if($config['app_cus_address1'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_strt_yes_r" name="config[app_cus_address1]" value="2" <?php if($config['app_cus_address1'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">Address2(area)</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_area_no" name="config[app_cus_address2]" value="0" <?php if($config['app_cus_address2'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_area_yes" name="config[app_cus_address2]" value="1"<?php if($config['app_cus_address2'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_area_yes_r" name="config[app_cus_address2]" value="2" <?php if($config['app_cus_address2'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">Country</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_cntry_no" name="config[app_cus_country]" value="0" <?php if($config['app_cus_country'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_cntry_yes" name="config[app_cus_country]" value="1" <?php if($config['app_cus_country'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_cntry_yes_r" name="config[app_cus_country]" value="2" <?php if($config['app_cus_country'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">State</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_stt_no" name="config[app_cus_state]" value="0" <?php if($config['app_cus_state'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_stt_yes" name="config[app_cus_state]" value="1" <?php if($config['app_cus_state'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_stt_yes_r" name="config[app_cus_state]" value="2" <?php if($config['app_cus_state'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">City</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_city_no" name="config[app_cus_city]" value="0" <?php if($config['app_cus_city'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_city_yes" name="config[app_cus_city]" value="1" <?php if($config['app_cus_city'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_city_yes_r" name="config[app_cus_city]" value="2" <?php if($config['app_cus_city'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">Last Name</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_lnm_no" name="config[app_cus_lastname]" value="0" <?php if($config['app_cus_lastname'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_lnm_yes" name="config[app_cus_lastname]" value="1"<?php if($config['app_cus_lastname'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_lnm_yes_r" name="config[app_cus_lastname]" value="2" <?php if($config['app_cus_lastname'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<!-- <div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">Village</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_vlg_no" name="config[app_cus_village]" value="0" <?php if($config['app_cus_village'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_vlg_yes" name="config[app_cus_village]" value="1"<?php if($config['app_cus_village'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_vlg_yes_r" name="config[app_cus_village]" value="2" <?php if($config['app_cus_village'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 for="chargeseme_name" class="col-md-4">Customer Type</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="app_custype" name="config[app_cus_custype]" value="0" <?php if($config['app_cus_custype'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_custype" name="config[app_cus_custype]" value="1"<?php if($config['app_cus_custype'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="app_custype" name="config[app_cus_custype]" value="2" <?php if($config['app_cus_custype'] == 2){ ?> checked="true" <?php } ?>> Show and Mandatory
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div> -->
							
							<!--10th App Store Settings-->
							<h4 class="page-header">App Store Settings</h4>
							<div class="row">
								<div class="col-sm-12">
									<label for="Play Store URL" class="col-md-3 col-md-offset-1 ">Play Store URL</label>
										<div class="col-md-4">
											<input type="text" class="form-control" name="config[play_str_url]" id="id_play_str_url" value="<?php echo set_value(config[play_str_url],$config[play_str_url])  ?>" placeholder="eg:https://play.google.com/store/apps/details" />
											<p class="help-block"></p>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<label for="aPackage" class="col-md-3 col-md-offset-1 ">aPackage</label>
										<div class="col-md-4">
											<input type="text" class="form-control" name="config[app_a_pack]" id="id_app_a_pack" value="<?php echo set_value(config[app_a_pack],$config[app_a_pack])  ?>" placeholder="eg:" />
											<p class="help-block"></p>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<label for="iPackage" class="col-md-3 col-md-offset-1 ">iPackage</label>
										<div class="col-md-4">
											<input type="text" class="form-control" name="config[app_i_pack]" id="id_app_i_pack" value="<?php echo set_value(config[app_i_pack],$config[app_i_pack])  ?>" placeholder="eg:" />
											<p class="help-block"></p>
										</div>
								</div>
							</div>
							
							<!--11th Version Settings-->
							<h4 class="page-header">Version Settings</h4>
							<div class="row">
								<div class="col-sm-12">
									<label for="current_android_version" class="col-md-3 col-md-offset-1 ">Current Android Version</label>
										<div class="col-md-4">
											<input type="text" class="form-control" name="config[current_android_version]" id="id_current_android_version" value="<?php echo set_value(config[current_android_version],$config[current_android_version])  ?>" placeholder="eg:" />
											<p class="help-block"></p>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<label for="new_android_version" class="col-md-3 col-md-offset-1 ">New Android Version</label>
										<div class="col-md-4">
											<input type="text" class="form-control" name="config[new_android_version]" id="new_android_version" value="<?php echo set_value(config[new_android_version],$config[new_android_version])  ?>" placeholder="eg:" />
											<p class="help-block"></p>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<label for="current_ios_version" class="col-md-3 col-md-offset-1 ">Current IOS Version</label>
										<div class="col-md-4">
											<input type="text" class="form-control" name="config[current_ios_version]" id="current_ios_version" value="<?php echo set_value(config[current_ios_version],$config[current_ios_version])  ?>" placeholder="eg:" />
											<p class="help-block"></p>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<label for="new_ios_version" class="col-md-3 col-md-offset-1 ">New IOS Version</label>
										<div class="col-md-4">
											<input type="text" class="form-control" name="config[new_ios_version]" id="new_ios_version" value="<?php echo set_value(config[new_ios_version],$config[new_ios_version])  ?>" placeholder="eg:" />
											<p class="help-block"></p>
										</div>
								</div>
							</div>
							<!-- Code Added by Durga 19.06.2023 starts here -->
							<!--12th Chit Settings-->
							<h4 class="page-header">Chit Settings</h4>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 class="col-md-4">Enable Digi Gold</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_1" name="general[enable_digi_gold]" value="1"<?php if($general['enable_digi_gold'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_0" name="general[enable_digi_gold]"value="0" <?php if($general['enable_digi_gold'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 class="col-md-4">Show Video Shop</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_1" name="general[show_video_shop]" value="1"<?php if($general['show_video_shop'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="show_video_shop" name="general[show_video_shop]"value="0" <?php if($general['show_video_shop'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 class="col-md-4">Show Customer Order</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_1" name="general[show_customer_order]" value="1"<?php if($general['show_customer_order'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_0" name="general[show_customer_order]"value="0" <?php if($general['show_customer_order'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 class="col-md-4">Is Pin Required</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_1" name="general[is_pin_required]" value="1"<?php if($general['is_pin_required'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_0" name="general[is_pin_required]"value="0" <?php if($general['is_pin_required'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>
							<!-- Code Added by Durga 19.06.2023 ends here -->

							
							<!-- Code By Karthikai Kumaran for show social media links ON 16/10/2025-->
							<h4 class="page-header">Other Settings</h4>				
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 class="col-md-4">Show Languages</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_1" name="config[show_language]" value="1"<?php if($config['show_language'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_0" name="config[show_language]" value="0" <?php if($config['show_language'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 class="col-md-4">Show Close</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_1" name="config[show_close]" value="1"<?php if($config['show_close'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_0" name="config[show_close]" value="0" <?php if($config['show_close'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 class="col-md-4">Show Weight History</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_1" name="config[show_wgt_history]" value="1"<?php if($config['show_wgt_history'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_0" name="config[show_wgt_history]" value="0" <?php if($config['show_wgt_history'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<h5 class="col-md-4">Show Amount History</h5>
										<div class="col-md-8">
											
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_1" name="config[show_amt_history]" value="1"<?php if($config['show_amt_history'] == 1){ ?> checked="true" <?php } ?>> Yes
												</div>
												<div class="col-md-4">
													<input type="radio" id="id_show_gcode_0" name="config[show_amt_history]" value="0" <?php if($config['show_amt_history'] == 0){ ?> checked="true" <?php } ?>> No
												</div>
												<p class="help-block"></p>
										</div>
									</div>
								</div>
							</div>

							<!--last row Save /Cancel footer -->
							<div class="box-footer clearfix">
										<button class="btn btn-sm btn-app pull-left btn-cancel" type="button"><i class="fa fa-remove"></i> Cancel</button>
										<button class="btn btn-sm btn-app pull-right" id="submit_config_tab" name="config[submit_config_tab]" value="Config Tab"><i class="fa fa-save"></i> Save</button>
										<input  id="config_tab_name" type="hidden" name="config[tab_name]" value="" /> 
							</div>
						  </div><!-- div tab_config pane-->
				<!-- tab config settings  created by durga 29/12/2022 ends here-->
			          <?php echo form_close(); ?>
			      
					 
					 
					 
					 
					 
					
			          <div class="tab-pane" id="PayU"></div>
			             <!-- Gateway content -->
			               <!-- Custom Tabs -->
			                 <input type="hidden" id="pg_code" name="">
					              <input type="hidden" id="type" name="">
					              <input type="hidden" id="id_pg" name="">
					              <div class="nav-tabs-custom"></div>
					                <div class="tab-content">
					                 <div class="tab-pane" id="gateway_content">
					           			<div class="">
											 <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Key</label>
												   <div class="col-md-4">
													 <input type="text" class="form-control" name="demo[param_1]" id="param_1" />
													<p class="help-block"></p>
												   </div>
												</div>
											 </div>
											 <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Salt</label>
												   <div class="col-md-4">
													 <input type="text" class="form-control" name="demo[param_2]"  id="param_2"  />
													<p class="help-block"></p>
												   </div>
												</div>
											 </div>
											  <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Merchant Code</label>
												   <div class="col-md-4">
													 <input type="text" class="form-control" name="demo[param_3]"  id="param_3"  />
													<p class="help-block"></p>
												   </div>
												</div>
											 </div>
  											
											  <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 "> IV</label>
												   <div class="col-md-4">
													 <input type="text" class="form-control" name="demo[param_4]"  id="param_4"  />
													<p class="help-block"></p>
												   </div>
												</div>
											 </div>
											 <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Verify URL</label>
												   <div class="col-md-6">
													 <input type="text" class="form-control" name="demo[api_url]" id="api_url"/>
													<p class="help-block"></p>
												   </div>
												</div>
											 </div>
											 <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Gateway Name</label>
												   <div class="col-md-4">
													 <input type="text" class="form-control" name="demo[pg_name]" id="pg_name"/>
																		   
													<p class="help-block"></p>
												   </div>
												</div>
											 </div> 
											 <div class="row">
												<div class="form-group">
												   <label for="chargeseme_name" class="col-md-3 col-md-offset-1 ">Default</label>
												   <div class="col-md-4">
													 <input type="checkbox"  id="is_default" name="demo[is_default]" value="1" />
													<p class="help-block"></p>
												   </div>
												</div>
											 </div>
										 </div>	
					           							           			      
					                  </div> 
					                 
			       	 <!-- </div>  -->
					 
					      	 	<div class="overlay"  style="display:none;">
									<i class="fa fa-refresh fa-spin"></i>
								</div>
				
						
			    </form>
			  </div>
			    
	   
			          
			       
			         

		          
		         
              
            </div><!-- /.box-body -->
            <div class="box-footer">
            
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
          
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

	
	