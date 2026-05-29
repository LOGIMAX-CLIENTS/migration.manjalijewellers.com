  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Branch
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Branch List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Branch List</h3>  
                           <?php if($access['add']==1){?>    
                           <a class="btn btn-success pull-right" id="branch_add" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                           <?php }?>
                          </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
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
                  <div class="table-responsive">
                  <table id="branch_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Branch</th>
                        <th>Short Name</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                      
                 </thead>
                 
                  </table>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

<!-- modal -->      
<div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Branch</h4>
      </div>
      <div class="modal-body">
           <div class="row">
                 
          <div class="form-group">
                     	<div class="row">
            <div class="form-group">
             <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload branch image</label>
             <div class="col-md-6">
               <input id="branch_img" name="branch" accept="image/*" type="file" >
               <img src="<?php echo(isset($category['catimage'])?$category['catimage']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="sch_branch_img_preview" style="width:304px;height:100%;" alt="classfication image"> 
                                           
            <p class="help-block"></p>
           </div>
        
             </div> 
        </div>
					  <div class="form-group">
                      <label for="head_office" class="col-md-3 col-md-offset-1 ">Head Office<span class="error">*</span></label>
					  <div class="col-md-5 ">
                         <input type="radio" class="type" id="is_ho" name="is_ho" value="1"> Yes &nbsp; &nbsp;					
							<input type="radio" class="type" id="is_ho" name="is_ho" value="0"> No
							<input type="hidden"  value=""><span class="error" id="error"></span>
                         
                      <p class="help-block"></p>
                         </div> </div>
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Branch<span class="error">*</span></label>
                       <div class="col-md-5 ">
                       <input type="hidden" id="id" value="" />
                         <input type="text" class="form-control" id="branch" name="branch" value="<?php echo set_value('branch',(isset($branch)?$branch:"")); ?>" placeholder="Enter branch"/> 
                         
                      <p class="help-block"></p>
                         </div>  
                       </div> 
                       <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Name</label>
                       <div class="col-md-5">
                       <input type="text" class="form-control" id="short_name" name="short_name"  value="<?php echo set_value('short_name',(isset($short_name)?$short_name:"")); ?>" placeholder="Enter Short Name"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
                            <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Map URL</label>
                       <div class="col-md-5 ">
                       <input type="hidden" id="id" value="" />
                         <input type="text" class="form-control" id="map_url" name="map_url" value="<?php echo set_value('map_url',(isset($map_url)?$map_url:"")); ?>" placeholder="Enter map url"/> 
                         
                      <p class="help-block"></p>
                         </div>  
                       
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address1</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="address1" name="address1" value="<?php echo set_value('address1',(isset($address1)?$address1:"")); ?>" placeholder="Enter Address1"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
         <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address2</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="address2" name="address2"value="<?php echo set_value('address2',(isset($address2)?$address2:"")); ?>" placeholder="Enter Address2"> 
                         
                      <p class="help-block"></p>
                        
                       </div><div class='form-group'>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Country</label>
              <div class="col-md-5">
                      <input type="hidden" class="form-control" id="countryval" name="country"  value="" placeholder="Enter Country"> 
                          <select class="form-control" id="country" name="country"  placeholder="Enter Short Name"></select>
                      <p class="help-block"></p>
                        
                       </div>
             </div>
             <label for="scheme_code" class="col-md-3 col-md-offset-1 " >State</label>
              <div class="col-md-5">
            
            <input  type="hidden" id="stateval" name="stateval" value=""/>

               <select class="form-control" id="state" name="state" required="true" ></select>

                     <!--  <input type="text" class="form-control" id="city" name="brc[city]" value="<?php echo set_value('brc[state]',$brc['state']); ?>" placeholder="Enter Short Name"> -->
                         
                      <p class="help-block"></p>
                        
                       </div>
             
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">City</label>
              <div class="col-md-5">
            
             <input  type="hidden" id="cityval" name="cityval" value=""/>

            <select class="form-control" id="city" name="city" ></select>
                     
                      <p class="help-block"></p>
                        
                       </div>
             
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Pin Code</label>
              <div class="col-md-5">
 <input type="text" class="form-control input_number" minlength="6" maxlength="6" id="branch_pincode" name="pincode" value="<?php echo set_value('pincode',(isset($pincode)?$pincode:"")); ?>" placeholder="Enter Pincode">                          
                      <p class="help-block"></p>
                        
                       </div>


            <label for="" class="col-md-3 col-md-offset-1 ">Select Area<span class="error"></span></label>

            <div class="col-md-5" style="display: flex;">

              <select class="form-control" id="sel_village" name="sel_village" style="width:100%;"></select>

              <input type="hidden" id="id_village" name="id_village"  Value=""/>

              <span class="input-group-btn">

              <button type="button" class="btn btn-success add_new_village"><i class="fa fa-plus"></i></button>

            </span>

            <p class="help-block"></p>

            </div>
            <div style="clear:both;height:15px;"></div>

              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Phone</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="phone" name="phone"  value="<?php echo set_value('phone',(isset($phone)?$phone:"")); ?>" placeholder="Enter Phone"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Mobile</label>
              <div class="col-md-5">
<input type="text" class="form-control input_number" minlength="10" maxlength="10" id="mobile" onkeypress="return /^[0-9]$/i.test(event.key)" name="mobiles"  value="<?php echo set_value('mobile',(isset($mobile)?$mobile:"")); ?>" placeholder="Enter Mobile">                          
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">C.Care</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="cusromercare" name="cusromercare"   value="<?php echo set_value('cusromercare',(isset($cusromercare)?$cusromercare:"")); ?>" placeholder="Enter Customercare"> 
                         
                      <p class="help-block"></p>
                        
                       </div> 
					   <label for="scheme_code" class="col-md-3 col-md-offset-1 " >Day Close</label>
              <div class="col-md-5">
			   <input class="form-control" name="day_close" type="text" id="day_close" value=""> 
			 <p class="help-block"></p>
                       </div>
					     
             <div class='form-group'>
                      <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Metal Type</label>
                        <div class="col-md-5">
                    <input type="radio" id="metal_type_manual" name="metal_type" value="0">Manual &nbsp;
                    
                    <input type="radio" id="metal_type_auto" name="metal_type" value="1" >Automatic &nbsp;
					
					 <input type="radio" id="metal_type_partial" name="metal_type" value="2" >Partial
                                <!-- <input type="radio" id="metal_type" name="metal_type" value="1" checked="true">Manual &nbsp;<input type="radio" id="metal_type" name="metal_type" value="2" checked="true">automate-->
                              
                                <p class="help-block"></p>
                    <?php  if($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=1)  {  ?> 
				   
					Gold : (Rate diff. from market rate) <input type="text" class="form-control" id="partial_goldrate" disabled="true" name="partial_rate" 
								
							value="<?php echo set_value('partial_goldrate_diff',(isset($partial_goldrate_diff)?$partial_goldrate_diff:"")); ?>"	placeholder="Enter Gold discount rate"/> 
								<p class="help-block"></p>
							
                    Silver : (Rate diff. from market rate) <input type="text" disabled="true"  style="width"class="form-control" id="partial_silverate" name="partial_rate" 
								
							value="<?php echo set_value('partial_silverrate_diff',(isset($partial_silverrate_diff)?$partial_silverrate_diff:"")); ?>"	placeholder="Enter Silver discount rate"/>
							
					
                                <p class="help-block"></p>
								
				   <?php }else{ ?>
				   	<input type="hidden" class="form-control" id="partial_goldrate" name="partial_rate" 
								
							value="<?php echo set_value('partial_goldrate_diff',(isset($partial_goldrate_diff)?$partial_goldrate_diff:"")); ?>"	placeholder="Enter Gold discount rate"/> 
								<p class="help-block"></p>
							
                    <input type="hidden" style="width"class="form-control" id="partial_silverate" name="partial_rate" 
								
							value="<?php echo set_value('partial_silverrate_diff',(isset($partial_silverrate_diff)?$partial_silverrate_diff:"")); ?>"	placeholder="Enter Silver discount rate"/>
							
				   <?php }?>             
                                 </div>
             </div>
			  
             <div class='form-group'>
                      <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Show to</label>
                        <div class="col-md-8">
                    <input type="radio" id="show_to_all" name="show_to_all" value="0">Own
                     &nbsp; &nbsp;
                    <input type="radio" id="show_to_all" name="show_to_all" value="1" >All
                     &nbsp; &nbsp;
                    <input type="radio" id="show_to_all" name="show_to_all" value="2" >All Cus Only
                      &nbsp; &nbsp;
                    <input type="radio" id="show_to_all" name="show_to_all" value="3" >All Emp only
                                <p class="help-block"></p>
                                 </div>
             </div>
             <div class='form-group'>
                      <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Special Voucher</label>
                        <div class="col-md-8">
                                <input type="radio" id="enable_gift_voucher_no" name="enable_gift_voucher" value="0">No
                                 &nbsp; &nbsp;
                                <input type="radio" id="enable_gift_voucher_yes" name="enable_gift_voucher" value="1" >Yes
                                 &nbsp; &nbsp;
                         <p class="help-block"></p>
                        </div>
             </div>
             
             <!--GST Numebr  -->
            <div class='form-group'> 
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">GST Number</label>
              <div class="col-md-5 ">
<input type="text" class="form-control" minlength="15" maxlength="15" id="gst_number" onkeypress="return /^[A-Za-z0-9]$/i.test(event.key)" name="gst_number" value="<?php echo set_value('gst_number', (isset($gst_number) ? $gst_number : "")); ?>" placeholder="ex:11AAAAA1111Z1A1" title="Format :11AAAAA1111Z1A1" />                <p class="help-block"></p>
              </div>
            </div>

            <!--Google Review URL -->
            <div class='form-group'>
              <label for="google_review_url" class="col-md-3 col-md-offset-1 ">Google Review URL</label>
              <div class="col-md-5 ">
<input type="url" class="form-control" id="google_review_url" name="google_review_url" value="" placeholder="https://search.google.com/local/writereview?placeid=..." pattern="https://(www\.)?(google\.(com|co\.\w{2,3})|maps\.google\.(com|co\.\w{2,3})|search\.google\.com|g\.page)/.*" title="Must be a valid Google Review URL (https://google.com/..., https://g.page/...)" />                <p class="help-block text-muted" style="font-size:11px;">Only Google Review URLs accepted (google.com, maps.google.com, g.page)</p>
              </div>
            </div>
            
                    </div>
                    </div>
      </div>
      <div class="modal-footer">
        <a href="#" id="add_branch" class="btn btn-success"  >Add</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Branch</h4>
      </div>
      <div class="modal-body">
          <div class="row">
		
          <div class="form-group">
           		 <div class="row">
            <div class="form-group">
             <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload Branch image</label>
             <div class="col-md-6">
               <input type="hidden" class="form-control" id="ed_id" name="ed_id" value=""> 
               <input id="edit_sch_branch_img" required="true" name="branch" accept="image/*" type="file" >
               <img src="" class="img-thumbnail" id="edit_sch_branch_img_preview" style="width:304px;height:100%;" alt="Offer image"> 
                                           
            <p class="help-block"></p>
           </div>
        
             </div> 
        </div>
		        <label for="head_office" class="col-md-3 col-md-offset-1 ">Head Office<span class="error">*</span></label>
					  <div class="col-md-5 ">
                         <input type="radio" class="type" id="ed_is_ho" name="ed_is_ho" value="1"> Yes &nbsp;&nbsp;&nbsp;					
							<input type="radio" class="type" id="ed_is_ho" name="ed_is_ho" value="0"> No
                         
                      <p class="help-block"></p>
                         </div> 
				
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Branch</label>
                       <div class="col-md-5 ">
                       <input type="hidden" id="edit-id" value="" />
                         <input type="text" class="form-control" id="ed_branch" name="ed_branch" value="<?php echo set_value('branch',(isset($branch)?$branch:"")); ?>" placeholder="Enter branch"/> 
                         
                      <p class="help-block"></p>
                         </div>  
                       </div> 
                       <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Name</label>
                       <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_short_name" name="ed_short_name" value="<?php echo set_value('short_name',(isset($short_name)?$short_name:"")); ?>" placeholder="Enter Short Name"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
                        <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Map URL</label>
                       <div class="col-md-5 ">
                       <input type="hidden" id="id" value="" />
                         <input type="text" class="form-control" id="ed_map_url" name="ed_map_url" value="<?php echo set_value('map_url',(isset($map_url)?$map_url:"")); ?>" placeholder="Enter map url"/> 
                         
                      <p class="help-block"></p>
                         </div>  
                       
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address1</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_address1" name="ed_address1" value="<?php echo set_value('ed_address1',(isset($ed_address1)?$ed_address1:"")); ?>" placeholder="Enter Address1"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
         <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address2</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_address2" name="ed_address2" value="<?php echo set_value('ed_address2',(isset($ed_address2)?$ed_address2:"")); ?>" placeholder="Enter Address2"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
            <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Country</label>
              <div class="col-md-5">
            
            
        <!--    
                   <input type="text" class="form-control" id="ed_country" name="ed_country" value="<?php echo set_value('ed_country',(isset($ed_country)?$ed_country:"")); ?>" placeholder="Enter Short Name"> 
                         <select class="form-control" id="country_ed" name="country"  placeholder="Enter Short Name" ></select>-->
             
             
              <input type="hidden" class="form-control" id="ed_country" name="ed_country"   placeholder="Enter Country"> 
                          <select class="form-control" id="ed_countrys" name="ed_country"  placeholder="Enter Short Name"></select>
                      <p class="help-block"></p>
                        
                       </div>
             
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">State</label>
              <div class="col-md-5">
                   <!--  <input type="text" class="form-control" id="ed_stae" name="ed_stae" value="<?php echo set_value('ed_stae',(isset($ed_stae)?$ed_stae:"")); ?>" placeholder="State">
                           <select class="form-control" id="state" name="state"  placeholder="Enter Short Name"></select>-->
               <input  type="hidden" id="ed_stae" name="ed_stae"/>

            <select class="form-control" id="ed_states" name="ed_stae" required="true" ></select>
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">City</label>
              <div class="col-md-5">
                       <!--<input type="text" class="form-control" id="ed_city" name="ed_city" value="<?php echo set_value('ed_city',(isset($ed_city)?$ed_city:"")); ?>" placeholder="Enter Short Name"> 
                              <select class="form-control" id="city" name="city"   placeholder="Enter Short Name"></select>-->
                  
                  <input  type="hidden" id="ed_city" name="ed_city" required="true"/>

            <select class="form-control" id="ed_citys" name="ed_city" ></select>
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Pin Code</label>
              <div class="col-md-5">
<input type="text" class="form-control input_number" minlength="6" maxlength="6" id="ed_pincode" onkeypress="return /^[0-9]$/i.test(event.key)" name="ed_pincode" value="<?php echo set_value('ed_pincode',(isset($ed_pincode)?$ed_pincode:"")); ?>" placeholder="Enter Pincode">                          
                      <p class="help-block"></p>
                        
                       </div>

              <label for="" class="col-md-3 col-md-offset-1 ">Select Area<span class="error"></span></label>

            <div class="col-md-5" style="display: flex;">

              <select class="form-control" id="ed_sel_village" style="width:100%;"></select>

              <input type="hidden" id="ed_id_village">

              <span class="input-group-btn">

              <button type="button" class="btn btn-success add_new_village"><i class="fa fa-plus"></i></button>

            </span>

            <p class="help-block"></p>

            </div>
            <div style="clear:both;height:15px;"></div>

              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Phone</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_phone" name="ed_phone" value="<?php echo set_value('ed_phone',(isset($ed_phone)?$ed_phone:"")); ?>" placeholder="Enter Short Phone"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Mobile</label>
              <div class="col-md-5">
<input type="text" class="form-control input_number" minlength="10" maxlength="10"  id="ed_mobile" onkeypress="return /^[0-9]$/i.test(event.key)" name="ed_mobile" value="<?php echo set_value('ed_mobile',(isset($ed_mobile)?$ed_mobile:"")); ?>" placeholder="Enter Mobile">                       <p class="help-block"></p>              
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">C.Care</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_customer_care" name="ed_customer_care" value="<?php echo set_value('ed_customer_care',(isset($ed_customer_care)?$ed_customer_care:"")); ?>" placeholder="Enter Customercare">      
                      <p class="help-block"></p>                
                       </div>
					     <label for="scheme_code" class="col-md-3 col-md-offset-1 " >Day Close</label>
						<div class="col-md-5">
						<input class="form-control" name="ed_day_close" type="text" id="ed_day_close" value=""> 
			            <p class="help-block"></p>
						</div>
             <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Metal Type</label>
              <div class="col-md-5">
            <input type="hidden" id="ed_metal_types">
          <input type="radio" id="rate_manual" class="ed_metal_type" name="ed_metal_type" value="0">Manual &nbsp;
          
          <input type="radio" id="rate_auto" class="ed_metal_type" name="ed_metal_type" value="1">Automatic &nbsp;
		  
		   <input type="radio" id="rate_partial" class="ed_metal_type" name="ed_metal_type" value="2">Partial
                      <!-- <input type="radio" id="metal_type" name="metal_type" value="1" checked="true">Manual &nbsp;<input type="radio" id="metal_type" name="metal_type" value="2" checked="true">automate-->
                    <?php         
	               if($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=1)  {  ?>  
					  
                    <p class="help-block"></p>
					Gold : (Rate diff. from market rate) <input type="text" class="form-control" id="ed_partial_goldrate" name="ed_partial_goldrate" 
								
							value="<?php echo set_value('partial_goldrate_diff',(isset($partial_goldrate_diff)?$partial_goldrate_diff:"")); ?>"	placeholder="Enter Gold discount rate"/> 
								<p class="help-block"></p>
							
                    Silver : (Rate diff. from market rate) <input type="text" style="width" class="form-control" id="ed_partial_silverate" name="ed_partial_silverate" 
								
							value="<?php echo set_value('partial_silverrate_diff',(isset($partial_silverrate_diff)?$partial_silverrate_diff:"")); ?>"	placeholder="Enter Silver discount rate"/>
					
                      
					  <?php } else{?>
					  	<input type="hidden" class="form-control" id="ed_partial_goldrate" name="ed_partial_goldrate" 
								
							value="<?php echo set_value('partial_goldrate_diff',(isset($partial_goldrate_diff)?$partial_goldrate_diff:"")); ?>"	placeholder="Enter Gold discount rate"/> 
								<p class="help-block"></p>
							
                    <input type="hidden" style="width" class="form-control" id="ed_partial_silverate" name="ed_partial_silverate" 
								
							value="<?php echo set_value('partial_silverrate_diff',(isset($partial_silverrate_diff)?$partial_silverrate_diff:"")); ?>"	placeholder="Enter Silver discount rate"/>
					
                      
					  <?php } ?>
                      <p class="help-block"></p>
                        
                       </div>
                <div class='form-group'>
                      <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Show to</label>
                        <div class="col-md-8"> 
                    <input type="radio" id="ed_show_to_all" name="ed_show_to_all" value="0">Own
                     &nbsp; &nbsp;
                    <input type="radio" id="ed_show_to_all" name="ed_show_to_all" value="1" >All
                     &nbsp; &nbsp;
                    <input type="radio" id="ed_show_to_all" name="ed_show_to_all" value="2" >All Cus Only
                      &nbsp; &nbsp;
                    <input type="radio" id="ed_show_to_all" name="ed_show_to_all" value="3" >All Emp only
                                
                                <p class="help-block"></p>
                                  
                                 </div>
             </div>
             <div class='form-group'>
                      <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Special Voucher</label>
                        <div class="col-md-8">
                                <input type="radio" id="ed_enable_gift_voucher_no" name="ed_enable_gift_voucher" value="0">No
                                 &nbsp; &nbsp;
                                <input type="radio" id="ed_enable_gift_voucher_yes" name="ed_enable_gift_voucher" value="1" >Yes
                                 &nbsp; &nbsp;
                         <p class="help-block"></p>
                        </div>
             </div>
             
              <!--Edit GST Numebr  -->
            <div class='form-group'>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">GST Number</label>
              <div class="col-md-5 ">
<input type="text" class="form-control" minlength="15" maxlength="15" id="ed_gst_number" onkeypress="return /^[A-Za-z0-9]$/i.test(event.key)" name="ed_gst_number" value="<?php echo set_value('gst_number', (isset($gst_number) ? $gst_number : "")); ?>" placeholder="ex:11AAAAA1111Z1A1" title="Format :11AAAAA1111Z1A1" />                <p class="help-block"></p>
              </div>
            </div>
            <!-- End of Edit Gst Number -->

            <!--Edit Google Review URL -->
            <div class='form-group'>
              <label for="ed_google_review_url" class="col-md-3 col-md-offset-1 ">Google Review URL</label>
              <div class="col-md-5 ">
<input type="url" class="form-control" id="ed_google_review_url" name="ed_google_review_url" value="" placeholder="https://search.google.com/local/writereview?placeid=..." pattern="https://(www\.)?(google\.(com|co\.\w{2,3})|maps\.google\.(com|co\.\w{2,3})|search\.google\.com|g\.page)/.*" title="Must be a valid Google Review URL (https://google.com/..., https://g.page/...)" />                <p class="help-block text-muted" style="font-size:11px;">Only Google Review URLs accepted (google.com, maps.google.com, g.page)</p>
              </div>
            </div>
            <!-- End of Edit Google Review URL -->
            
                    </div>
                    </div>
            
      </div>
      <div class="modal-footer">
        <a href="" id="update_branch" class="btn btn-success" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

<!-- area modal -->

    <div class="modal fade" id="confirm-area" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      <div class="modal-dialog">

        <div class="modal-content">

          <div class="modal-header">

            <button type="button" class="close" data-bs-dismiss="offcanvas"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

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
            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>



          </div>

        </div>

      </div>

    </div>


    <!-- area modal -->
