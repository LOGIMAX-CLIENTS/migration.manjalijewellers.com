
<style>
/* --- Toggle Switch --- */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
  vertical-align: middle;
}

.switch input { display: none; }

.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #605ca8; /* your color */
}

input:checked + .slider:before {
  transform: translateX(26px);
}
</style> 
 
 
 <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Company

            <small>profile</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Settings</a></li>

            <li class="active">Company</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">



          <!-- Default box -->

          <div class="box">

            <div class="box-header with-border">

              <h3 class="box-title">Complete the profile</h3>

              <!--<div class="box-tools pull-right">

                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

              </div>-->

            </div>

            <div class="box-body">

	            <div class="col-md-12">

				  

				  <?php

				    $attributes = array('autocomplete' => "off",'role'=>'form');

		    		 echo form_open(($comp['id_company']==NULL?'settings/company/save':'settings/company/update/'.$comp['id_company']), $attributes); 

				  //form validation

				    if(validation_errors())

				    {

						echo '<div class="alert alert-danger alert-dismissable">

                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

                    <h4><i class="icon fa fa-warning"></i> Warning!</h4>    <strong>'.validation_errors().'</strong>              

            </div>';

					}

					

					?>  

				  <div class="row">

				   <div class="col-md-6">

						<div class="form-group ">

						  <label for="company_name">Company Name</label>

						  <input type="text" class="form-control" id="company_name" name="comp[company_name]" placeholder="Logimax Technologies (p) LTD" required="true" value="<?php echo set_value('comp[company_name]',$comp['company_name']); ?>">

						  <p class="help-block"></p>

						</div>

						

						<div class="form-group">

						  <label for="address1">Address</label>

						  <input type="text" class="form-control" id="address1" name="comp[address1]" value="<?php echo set_value('comp[address1]',$comp['address1']);?>" placeholder="Logimax Technologies (p) LTD">

						  <p class="help-block"></p>

						</div>

						

						 <div class="form-group">

							  <label for="address2">Address2</label>

							  <input type="text" class="form-control" id="address2" name="comp[address2]" value="<?php echo set_value('comp[address2]',$comp['address2']);?>" placeholder="Street">

							  <p class="help-block"></p>

						</div>



						<div class="form-group">

						  <label for="country">Country</label>

						   <input type="hidden" id="countryval" name="comp[countryval]" value="<?php echo set_value('comp[country]',$comp['id_country']);?>" />

						  <select class="form-control" id="country" name="comp[country]">

						  </select>

						  

						  <p class="help-block"></p>

						</div>				

						

						<div class="form-group">

						  <label for="state">State</label>

						   <input type="hidden" id="stateval" name="comp[stateval]" value="<?php echo set_value('comp[state]',$comp['id_state']);?>" />

						  <select class="form-control"  id="state" name="comp[state]"  >

						  	

						  </select> 

						 

						  <p class="help-block"></p>

						</div>

						

						<div class="form-group">

						  <label for="city">City</label>

						  <input type="hidden" id="cityval" name="comp[cityval]" value="<?php echo set_value('comp[cityval]',$comp['id_city']);?>" />

						  <select class="form-control"  id="city" name="comp[city]"  >				  	

						  </select> 

				

						  <p class="help-block"></p>

						</div>

						

						 <div class="form-group">

						  <label for="pincode">Pincode</label>

						  <input type="text" class="form-control" id="pincode" name="comp[pincode]" value="<?php echo set_value('comp[pincode]',$comp['pincode']);?>" placeholder="641044">

						  <p class="help-block"></p>

						</div>
						
						<div class="form-group">

							<label for="phone">Toll Free Number</label>
		                
			            
	              			<!--	<span class="input-group-addon input-sm"><?php echo $this->session->userdata('mob_code')?></span>-->
						    	<input type="text" class="form-control" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" id="tollfree1" name="comp[tollfree1]" value="<?php echo set_value('comp[tollfree1]',$comp['tollfree1']);?>" placeholder="">
							

						  <p class="help-block"></p>

						</div>
						
						<div class="form-group ">

						  <label for="comp_name_in_sms">Company Name Sms</label>  <small>NOTE : this field for sms.</small>
						 
						  <input type="text" class="form-control" id="comp_name_in_sms" name="comp[comp_name_in_sms]" placeholder="Enter comp name for sms" required="true" value="<?php echo set_value('comp[comp_name_in_sms]',$comp['comp_name_in_sms']); ?>">

						  <p class="help-block"></p>

						</div>
						

						<!-- Code By Karthikai Kumaran for show social media links ON 16/10/2025-->
						
						<div class="form-group">
						<div style="display: flex; align-items: center; gap: 10px;">
							<label for="showSocialMediaLinks" class="control-label">Enable Social Media Links</label>
							
							<!-- <label class="switch"> -->
							<!-- <input type="checkbox" value="1" id="showSocialMediaLinks" name="comp[show_social_media]"
								<?php if($comp['show_social_media']==1){ echo "checked"; } ?>> -->
			    				<input type="checkbox" class="social-toggle" id="active" data-on-text="YES" data-off-text="NO" name="comp[show_social_media]" value="1" <?php if($comp['show_social_media']==1) { ?> checked="true" <?php } ?>/>
							<!-- <span class="slider"></span> -->
							<!-- </label> -->
						</div>
						</div>
				   </div>

				   <div class="col-md-6">
				       
				      
				       <div class="form-group">

						  <label for="shortcode">Short Code</label>

						  <input type="text" class="form-control" id="short_code" name="comp[short_code]" value="<?php echo set_value('comp[short_code]',$comp['short_code']);?>" placeholder="LMX">

						  <p class="help-block"></p>

						</div>

						

						<div class="form-group ">

						  <label for="phone">Phone</label>

						  <input type="text" class="form-control" id="phone" name="comp[phone]" value="<?php echo set_value('comp[phone]',$comp['phone']);?>" placeholder="044 25468744">

						  <p class="help-block"></p>

						</div>

						<div class="form-group ">

						  <label for="phone">Customer Care</label>

						  <input type="text" class="form-control" id="phone1" name="comp[phone1]" value="<?php echo set_value('comp[phone1]',$comp['phone1']);?>" placeholder="044 25468744">

						  <p class="help-block"></p>

						</div>

						

						<div class="form-group">

						  <label for="phone">Mobile</label>

						  
<!-- coded by ARVK --> 			                
			                <div class="input-group">
	              				<span class="input-group-addon input-sm"><?php echo $this->session->userdata('mob_code')?></span>
						    	<input type="text" class="form-control" id="mobile" name="comp[mobile]" value="<?php echo set_value('comp[mobile]',$comp['mobile']);?>" placeholder="" >
							</div> 
<!-- /coded by ARVK -->

						  <p class="help-block"></p>

						</div>
						
						<div class="form-group">

						  <label for="phone">whats App No</label>
                               <div class="input-group">
	              				<span class="input-group-addon input-sm"><?php echo $this->session->userdata('mob_code')?></span>
						    	<input type="text" class="form-control" id="whatsapp_no" name="comp[whatsapp_no]" value="<?php echo set_value('comp[whatsapp_no]',$comp['whatsapp_no']);?>" placeholder="" >
							</div> 
                             <p class="help-block"></p>

						</div>
						
						

						<div class="form-group">

						  <label for="phone">Mobile 1</label>
<!-- coded by ARVK --> 			                
			                <div class="input-group">
	              				<span class="input-group-addon input-sm"><?php echo $this->session->userdata('mob_code')?></span>
						    	<input type="text" class="form-control" id="mobile1" name="comp[mobile1]" value="<?php echo set_value('comp[mobile1]',$comp['mobile1']);?>" placeholder="">
							</div> 
<!-- /coded by ARVK -->
						  

						  <p class="help-block"></p>

						</div>

										

						<div class="form-group">

						  <label for="email">Email</label>

						  <input type="text" class="form-control" id="email" name="comp[email]" value="<?php echo set_value('comp[email]',$comp['email']);?>" placeholder="yourid@domain.com">

						  <p class="help-block"></p>

						</div>				

						

						 <div class="form-group">

							  <label for="website">Website</label>

							  <input type="text" class="form-control" id="website" name="comp[website]" value="<?php echo set_value('comp[website]',$comp['website']);?>" placeholder="www.logimaxindia.com">

							  <p class="help-block"></p>

						</div>

						<div class="form-group">

							  <label for="map">company Map</label>

							  <input type="text" class="form-control" id="map" name="comp[map]" value="<?php echo set_value('comp[map]',$comp['map_url']);?>" placeholder="">

							  <p class="help-block"></p>

						</div>
						
				   </div>

				   </div>
				
				   <!-- Code By Karthikai Kumaran for show social media links ON 16/10/2025 -->
				
				<div id="urlsForSocialMediaLink" style="display: none;">

					<h4>Social Media Links</h4>
					<hr>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label for="fb_url">Facebook URL</label>
							  	<input type="text" class="form-control" id="fb_url" name="comp[fb_url]" value="<?php echo set_value('comp[fb_url]',$comp['fb_url']);?>" placeholder="https://www.facebook.com/logimaxindia">
							  	<p class="help-block text-danger"></p>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="whatsapp_url">WhatsApp URL</label>
							  	<input type="text" class="form-control" id="whatsapp_url" name="comp[whatsapp_url]" value="<?php echo set_value('comp[whatsapp_url]',$comp['whatsapp_url']);?>" placeholder="https://wa.me/1234567890">
							  	<p class="help-block text-danger"></p>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="insta_url">Instagram URL</label>
							  	<input type="text" class="form-control" id="insta_url" name="comp[insta_url]" value="<?php echo set_value('comp[insta_url]',$comp['insta_url']);?>" placeholder="https://www.instagram.com/logimaxindia">
							  	<p class="help-block text-danger"></p>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="youtube_url">YouTube URL</label>
							  	<input type="text" class="form-control" id="youtube_url" name="comp[youtube_url]" value="<?php echo set_value('comp[youtube_url]',$comp['youtube_url']);?>" placeholder="https://www.youtube.com/logimaxindia">
							  	<p class="help-block text-danger"></p>
							</div>
						</div>
					</div>
				</div>

				   <legend style="display: none;">Bank Account Details</legend>

				   <div class="row" style="display: none;">

				   <div class="col-sm-6">

				   	 <div class="form-group">

				   	 	<label>Bank A/c No</label>

				   	 	<input  type="text" class="form-control" id="bank_acc_number" name="comp[bank_acc_number]" value="<?php echo set_value('comp[bank_acc_number]',$comp['bank_acc_number']);?>"/>

				   	 	<p class="help-block"></p>

				   	 </div> 

				   </div> 

				   <div class="col-sm-6">

				   	 <div class="form-group">

				   	 	<label>Account Name</label>

				   	 	<input  type="text" class="form-control" id="bank_acc_name" name="comp[bank_acc_name]" value="<?php echo set_value('comp[bank_acc_name]',$comp['bank_acc_name']);?>"/>

				   	 	<p class="help-block"></p>

				   	 </div>

				   	</div> 

				   	

				   	<div class="col-sm-6">

				   	 <div class="form-group">

				   	 	<label>Bank Name</label>

				   	 	<input  type="text" class="form-control" id="bank_name" name="comp[bank_name]" value="<?php echo set_value('comp[bank_name]',$comp['bank_name']);?>" />

				   	 	<p class="help-block"></p>

				   	 </div>

				   	</div>	

				   	<div class="col-sm-6">

				   	 <div class="form-group">

				   	 	<label>Branch Name</label>

				   	 	<input  type="text" class="form-control" id="bank_branch" name="comp[bank_branch]" value="<?php echo set_value('comp[bank_branch]',$comp['bank_branch']);?>"/>

				   	 	<p class="help-block"></p>

				   	 </div>

				   	</div>  	

				   	

				   	<div class="col-sm-6">

				   	 <div class="form-group">

				   	 	<label>IFSC Code</label>

				   	 	<input  type="text" id="bank_ifsc" name="comp[bank_ifsc]" class="form-control" value="<?php echo set_value('comp[bank_ifsc]',$comp['bank_ifsc']);?>"/>

				   	 	<p class="help-block"></p>

				   	 </div>

				   	</div>	

				   	

				   	 

				   </div>

				   <div class="row">

				   <div class="box box-default"><br/>

					  <div class="col-xs-offset-5">

						<button type="submit" class="btn btn-primary">Save</button> 

						<button type="button" class="btn btn-default">Cancel</button>

						

					  </div> <br/>

					</div>

				  </div>

				  

				  

				  

				  </form>

				</div>

            </div><!-- /.box-body -->

            <div class="box-footer">

             

            </div><!-- /.box-footer-->

          </div><!-- /.box -->



        </section><!-- /.content -->

      </div><!-- /.content-wrapper -->
      <script type="text/javascript">
    	var mob_no_len ="<?php echo $this->session->userdata('mob_no_len')?>"; 
  	</script>