  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Karigar List</h3><span id="total_count" class="badge bg-green"></span>  
                  <?php if($access['add']==1){?>     
                           <a class="btn btn-success pull-right" id="karigar_add" href="<?php echo base_url('index.php/admin_ret_catalog/karigar/add');?>"></i>Add</a> 
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
			<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div> 
					</div>
				  </div>
			<div class="row">
	                 <div class="col-md-12">
	             
	                 	<div class="col-md-2" style="margin-top: 20px;">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                       <button class="btn btn-default btn_date_range" id="user_date">
							  <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
							    <span  style="display:none;" id="user1"></span>
							    <span  style="display:none;" id="user2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		                </div>
		                
		                <div class="col-md-2"> 
    				      		<div class='form-group'>
    				               <label for="gender">Karigar For<span class="error">*</span></label>
    				                    <div class="form-group">
    									  <p class="help-block"></p>
    									  <select class="form-control item-required" id="karigar_for">
    									      <option value="" ></option>
    									      <option value="1" >Jewellery Manufacturer</option>
    									      <option value="2" >Jewellery Supplier</option>
    									      <option value="5" >Supplier & Manufacturer</option>
    									      <option value="3" >Testing / HM Centers</option>
    									      <option value="4" >Other Inventory Supplier</option>
    									  </select>
    				         	   </div> 
    				            </div> 
    					</div>
    					
    					<div class="col-md-2"> 
    				      		<div class='form-group'>
    				               <label for="gender">Type<span class="error">*</span></label>
    				                    <div class="form-group">
    									  <p class="help-block"></p>
    									  <select class="form-control item-required" id="karigar_type">
    									      <option value="" ></option>
    									      <option value="0" >Individual</option>
    									      <option value="1" >Company</option>
    									      <option value="2" >Firm</option>
    									  </select>
    				         	   </div> 
    				            </div> 
    					</div>
    					
    					<div class="col-md-2"> 
							<label></label>
							<div class="form-group"><p></p>
								<button type="button" id="supplier_search" class="btn btn-info">Search</button>   
							</div>
						</div>
    								
							</div>
					
	                 </div>			  
                  <div class="table-responsive">
                  <table id="karigar_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>User Name</th>
						<th>Mobile</th>
						<th>User Type</th>
						<th>Type</th>
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
<div class="modal fade" id="confirm-delete"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete User</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this user record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->  
<!-- modal -->      
<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add User</h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
		  </div>
           <div class="row">
                 
          <div class="form-group">
		  <?php echo form_open_multipart(('id_karigar'!=NULL && 'id_karigar'>0 ?'karigar/update/'.'id_karigar':'karigar/add')); ?>
                     	<div class="row">
            <div class="form-group">
             <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload image</label>
			 <div class="col-md-6">
							 <input id="user_img" name="user_img" accept="image/*" type="file" >
							 <p class="help-block">File size should not exceed 1MB<br/>Image format should be .jpg or .png</p>
							 <img src="<?php echo(isset($karigar['image'])?$karigar['image']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="user_img_preview" style="width:304px;height:100%;" alt="classfication image"> 
						<p class="help-block"></p>
					   </div>
             </div> 
        </div>
					  <div class="form-group">
                      <label for="first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error"> *</span></label>
					  <div class="col-md-5 ">
                         <input type="text" class="form-control" id="first_name" name="first_name"  placeholder="Enter First Name">
                           <p class="help-block"></p>
                         </div>
					 </div>
                       <label for="last_name" class="col-md-3 col-md-offset-1 ">Last Name</label>
                       <div class="col-md-5 ">
                       <input type="hidden" id="id" value="" />
                         <input type="text" class="form-control" id="last_name" name="last_name"  placeholder="Enter last name"/> 
                         <p class="help-block"></p>
                         </div>  
                       </div> 
                       <div class="form-group">
                       <label for="user_name" class="col-md-3 col-md-offset-1 ">User Name<span class="error"> *</span></label>
                       <div class="col-md-5">
                       <input type="text" class="form-control" id="user_name" name="user_name"  value="<?php echo set_value('short_name',(isset($short_name)?$short_name:"")); ?>" placeholder="Enter User Name">                      
                      <p class="help-block"></p>
                        
                       </div>
                       <label for="password" class="col-md-3 col-md-offset-1 ">Password<span class="error"> *</span></label>
                       <div class="col-md-5 ">
                       <input type="hidden" id="id" value="" />
                         <input type="text" class="form-control" id="password" name="password"  placeholder="Enter Password"/>                 
                      <p class="help-block"></p>
                        </div>  
                        <div class="form-group">
                      <label for="first_name" class="col-md-3 col-md-offset-1 ">Code</label>
					  <div class="col-md-5 ">
                         <input type="text" class="form-control" id="karigar_code" name="karigar_code"  placeholder="Enter code">
                           <p class="help-block"></p>
                         </div> </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address1</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="address1" name="address1"  placeholder="Enter Address1"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
         <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address2</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="address2" name="address2" placeholder="Enter Address2"> 
                         
                      <p class="help-block"></p>
                        
                       </div> 
					   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address3</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="address3" name="address3" placeholder="Enter Address3"> 
                         
                      <p class="help-block"></p>
                        
                       </div> 
					   <div class='form-group'>
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
             
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Email</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="email" name="email" value="<?php echo set_value('pincode',(isset($pincode)?$pincode:"")); ?>" placeholder="Enter Email"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Phone</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="phone" name="phone"  value="<?php echo set_value('phone',(isset($phone)?$phone:"")); ?>" placeholder="Enter Phone"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Mobile</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="mobile" name="mobiles"  value="<?php echo set_value('mobile',(isset($mobile)?$mobile:"")); ?>" placeholder="Enter Mobile"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Company</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="company" name="company"   value="<?php echo set_value('cusromercare',(isset($cusromercare)?$cusromercare:"")); ?>" placeholder="Enter Company Name"> 
                         
                      <p class="help-block"></p>
                        
                       </div> 
			  
             <div class='form-group'>
                      <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Status</label>
                        <div class="col-md-8">
                    <input type="checkbox" checked="true" class="alert-status" id="user" name="user" data-on-text="YES" data-off-text="NO" />
					<input type="hidden" id="user_status" value="1">
                    <p class="help-block"></p>
                    </div>
             </div>
                    </div>
                    </div>
      </div>
      <div class="modal-footer">
        <a href="#" id="add_newuser" class="btn btn-success">Save & New</a>
		<a href="#" id="add_user" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit User</h4>
      </div>
       <div class="modal-body">
           <div class="row">
                 
          <div class="form-group">
                     	<div class="row">
            <div class="form-group">
             <label for="chargeseme_name" class="col-md-3 col-md-offset-1">Upload image</label>
             <div class="col-md-6">
               <input id="ed_user_img" name="branch" accept="image/*" type="file" >
			   <p class="help-block">File size should not exceed 1MB<br/>Image format should be .jpg or .png</p>
               <img src="" class="img-thumbnail" id="ed_user_img_preview" style="width:304px;height:100%;" alt="classfication image"> 
                                           
            <p class="help-block"></p>
           </div>
        
             </div> 
        </div>
					  <div class="form-group">
                      <label for="first_name" class="col-md-3 col-md-offset-1 ">First Name<span class="error"> *</span></label>
					  <div class="col-md-5 ">
					  <input type="hidden" id="edit-id" value="" />
                         <input type="text" class="form-control" id="ed_first_name" name="ed_first_name"  placeholder="Enter First Name">
                           <p class="help-block"></p>
                         </div> </div>
                       <label for="last_name" class="col-md-3 col-md-offset-1 ">Last Name</label>
                       <div class="col-md-5 ">
                       <input type="hidden" id="id" value="" />
                         <input type="text" class="form-control" id="ed_last_name" name="ed_last_name"  placeholder="Enter last name"/> 
                         <p class="help-block"></p>
                         </div>  
                       </div> 
                       <div class="form-group">
                       <label for="user_name" class="col-md-3 col-md-offset-1 ">User Name<span class="error"> *</span></label>
                       <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_user_name" name="ed_user_name"  value="<?php echo set_value('short_name',(isset($short_name)?$short_name:"")); ?>" placeholder="Enter User Name">                      
                      <p class="help-block"></p>
                        
                       </div>
                       <label for="password" class="col-md-3 col-md-offset-1 ">Password<span class="error"> *</span></label>
                       <div class="col-md-5 ">
                       <input type="hidden" id="id" value="" />
                         <input type="text" class="form-control" id="ed_password" name="ed_password"  placeholder="Enter Password"/>                 
                      <p class="help-block"></p>
                        </div>  
                       <div class="form-group">
                      <label for="first_name" class="col-md-3 col-md-offset-1 ">Code</label>
					  <div class="col-md-5 ">
                         <input type="text" class="form-control" id="ed_karigar_code" name="ed_karigar_code"  placeholder="Enter code">
                           <p class="help-block"></p>
                         </div> </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address1</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_address1" name="ed_address1"  placeholder="Enter Address1"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
         <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address2</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_address2" name="ed_address2" placeholder="Enter Address2"> 
                         
                      <p class="help-block"></p>
                        
                       </div> 
					   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Address3</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_address3" name="ed_address3" placeholder="Enter Address3"> 
                         
                      <p class="help-block"></p>
                        
                       </div> 
					   <div class='form-group'>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Country</label>
              <div class="col-md-5">
                      <input type="hidden" class="form-control" id="countryval" name="country"  value="" placeholder="Enter Country"> 
                          <select class="form-control" id="ed_country" name="ed_country"  placeholder="Enter Short Name"></select>
                      <p class="help-block"></p>
                        
                       </div>
             </div>
             <label for="scheme_code" class="col-md-3 col-md-offset-1 " >State</label>
              <div class="col-md-5">
            
            <input  type="hidden" id="stateval" name="stateval" value=""/>

               <select class="form-control" id="ed_state" name="ed_state" required="true" ></select>

                     <!--  <input type="text" class="form-control" id="city" name="brc[city]" value="<?php echo set_value('brc[state]',$brc['state']); ?>" placeholder="Enter Short Name"> -->
                         
                      <p class="help-block"></p>
                        
                       </div>
             
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">City</label>
              <div class="col-md-5">
            
             <input  type="hidden" id="cityval" name="cityval" value=""/>

            <select class="form-control" id="ed_city" name="ed_city" ></select>
                     
                      <p class="help-block"></p>
                        
                       </div>
             
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Email</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_email" name="ed_email" value="<?php echo set_value('pincode',(isset($pincode)?$pincode:"")); ?>" placeholder="Enter Email"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Phone</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_phone" name="ed_phone"  value="<?php echo set_value('phone',(isset($phone)?$phone:"")); ?>" placeholder="Enter Phone"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Mobile</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_mobile" name="ed_mobile"  value="<?php echo set_value('mobile',(isset($mobile)?$mobile:"")); ?>" placeholder="Enter Mobile"> 
                         
                      <p class="help-block"></p>
                        
                       </div>
              <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Company</label>
              <div class="col-md-5">
                       <input type="text" class="form-control" id="ed_company" name="ed_company"   value="<?php echo set_value('cusromercare',(isset($cusromercare)?$cusromercare:"")); ?>" placeholder="Enter Company Name"> 
                         
                      <p class="help-block"></p>
                        
                       </div> 
			  
             <div class='form-group'>
                      <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Status</label>
                        <div class="col-md-8">
                    <input type="checkbox" checked="true" class="alert-status" id="ed_user" name="ed_user" data-on-text="YES" data-off-text="NO" />
					<input type="hidden" id="ed_user_status" value="1">
                    <p class="help-block"></p>
                    </div>
             </div>
                    </div>
                    </div>
      </div>
      <div class="modal-footer">
        <a href="#" id="update_user" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
<div class="modal fade" id="delete_karigar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Branch Group</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this branch group record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" onclick="delete_karigar()">Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
