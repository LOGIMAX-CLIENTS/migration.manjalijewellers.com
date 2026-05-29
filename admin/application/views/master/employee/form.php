  <div class="content-wrapper">



        <!-- Content Header (Page header) -->



        <section class="content-header">



          <h1>



            Employee



            <small>complete profile</small>



          </h1>



          <ol class="breadcrumb">



            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>



            <li><a href="#">Masters</a></li>



            <li class="active">Employee</li>



          </ol>



        </section>





    <!-- Main content -->



        <section class="content">


  		<?php $old = $this->session->flashdata('old_emp'); ?>




          <!-- Default box -->



          <div class="box">



            <div class="box-header with-border">



              <h3 class="box-title">Employee</h3>



              <div class="box-tools pull-right">



                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>



                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>



              </div>



            </div>



            <div class="box-body">



			  



             <!-- form container -->



              <div>



	             <!-- form -->



	            <?php



	            	$attributes=""	;



	            	$url=($emp && $emp['id_employee']!=NULL? "employee/update/".$emp['id_employee'] :"employee/save/" );



	            		 echo form_open_multipart($url,array('id'=>'emp_join'),$attributes);



	            ?>



	            <div class="col-md-12">



	            <div class='row'>	              



			       <div class='col-sm-4'>    



			           <div class='form-group'>



			            <input id="emp_image" name="emp_img" accept="image/*" type="file" >



							 <img src="<?php echo base_url((isset($emp['emp_img_path'])?$emp['emp_img_path']:(isset($old['emp_img_path'])?$old['emp_img_path']:'assets/img/default.png'))); ?>" class="img-thumbnail" id="emp_img_preview" style="width:304px;height:100%;" alt="Employee image"> 



							<!-- <input type="hidden" name="emp[employee_img]" value="<?php echo set_value('emp[employee_img]',$emp['emp_img'])?>" />-->



			      		<!--<img src="<?php echo base_url('assets/img/default.png');?>" class="img-thumbnail" alt="Cinque Terre" width="304" height="236"> 



			      		     <input id="exampleInputFile" type="file">-->



			      	</div>



			        </div>



			        



			     <div class='col-sm-8'>



			        <legend>Basic Information</legend>



			         <div class='col-sm-6'>



			            <div class='form-group'>



			                <label for="emp_firstname">First name <span class="error">*</span></label>



			                <input  type="hidden" id="id_employee" name="emp[id_employee]" value="<?php echo set_value('emp[id_employee]',$emp['id_employee']); ?>" />



			                <input class="form-control" id="firstname" name="emp[firstname]" required="true" value="<?php echo isset($old['firstname']) ? $old['firstname'] : $emp['firstname']; ?>"  size="30" type="text" />



			            </div>



			            </div>	



			            <div class='col-sm-6'>



			            <div class='form-group'>



			                <label for="emp_lastname">Last name <span class="error">*</span></label>



			                <input class="form-control" id="lastname" name="emp[lastname]" required="true"  value="<?php echo isset($old['lastname']) ? $old['lastname'] : $emp['lastname']; ?>"  size="30" type="text" />



			                



			            </div>



			         </div>		            



			            <div class='col-sm-6'>



				             <div class='form-group'>



				                <label for="emp_title">Date of Birth</label>


                               



<input class="form-control" id="date_of_birth" name="emp[date_of_birth]" value="<?php echo isset($old['date_of_birth']) ? $old['date_of_birth'] :$emp['date_of_birth']; ?>" size="30" type="date" />



				            </div>			            



			            </div>



			            



		     			<div class='col-sm-6'>    



				            <div class='form-group'>



				                <label for="emp_lastname">Age</label>



				                <input class="form-control co-md-6" id="emp_age" name="emp[age]" required="true" size="30" readonly="true" type="text" />



				            </div> 	



			       		</div>



			       		<legend>Offical Information</legend>



			       		 <div class='col-sm-6'>



				             <div class='form-group'>



				                <label for="emp_title">Employee Code <span class="error">*</span></label>

  									<span id="empErr" style="color:red;">
										<?= $this->session->flashdata('empErr'); ?>
									</span>

				                <input class="form-control" id="emp_code" name="emp[emp_code]"  value="<?php echo isset($old['emp_code']) ? $old['emp_code'] : $emp['emp_code']; ?>" size="30" type="text" minlength="3"  required="true" />



				            </div>			            



			            </div>



			            



			              <div class='col-sm-6'>



				             <div class='form-group'>



				                <label for="emp_title">Date of Joining</label>



				                <input class="form-control" id="date_of_join" name="emp[date_of_join]" value="<?php echo isset($old['date_of_join']) ? $old['date_of_join'] : $emp['date_of_join']; ?>" />



				            </div>			            



			            </div>

                        

			           <div class='col-sm-6'>



				             <div class='form-group'>



				                <label for="dept">Dept <span class="error">*</span></label>



				                <input  type="hidden" id="deptval" name="deptval" value="<?php echo isset($old['dept']) ? $old['dept'] : $emp['dept']; ?>"/>



				                <!--<select class="form-control" id="dept" name="emp[dept]">-->

								<select required="true" class="form-control" id="dept" name="emp[dept]">



				                </select>



				            </div>			            



			            </div>

			             <div class='col-sm-6'>



				             <div class='form-group'>

								

								



				                <label for="designation">Designation <span class="error">*</span></label>



				                 <input  type="hidden" id="designval" name="designval" value="<?php echo isset($old['designation']) ? $old['designation'] : $emp['designation']; ?>"/>



				                <select required="true" class="form-control" id="designation" name="emp[designation]" >



				                </select>



				            </div>			            



			            </div>

			             <div class="col-md-12">

			        

                    <div class='col-sm-6'>

				    	<div class="form-group pull-right">

				    		<label>Active</label>

				    		<input type="checkbox"  id="active" class="switch" data-on-text="YES" data-off-text="NO" name="emp[active]" value="1" <?php if((isset($old['active']) && $old['active']==1) || (!isset($old) && $emp['active']==1)) { ?> checked="true" <?php } ?>/>

				    	</div>

			    	     </div>

			            	

			            	</div>



			            </div>

                          

                               </div>



		                  <div class="row">

                       	<legend>Contact Information</legend>



			      <div class="col-sm-4"> 	



			      		<div class='form-group'>



			                <label for="address1">Address1</label>



			                <input class="form-control" id="address1" name="emp[address1]" value="<?php echo isset($old['address1']) ? $old['address1'] : $emp['address1']; ?>" size="30" type="text" />



			            </div>	



			            <div class='form-group'>



			                <label for="address2">Address2</label>



			                <input class="form-control" id="address2"  name="emp[address2]" value="<?php echo isset($old['address2']) ? $old['address2'] : $emp['address2']; ?>" size="30" type="text" />



			            </div> 



						<div class='form-group'>



			                <label for="address3">Address3</label>



			                <input class="form-control" id="address3"  name="emp[address3]" value="<?php echo isset($old['address3']) ? $old['address3'] : $emp['address3']; ?>" size="30" type="text" />



			            </div>



			             <div class='form-group'>



			                <label for="pincode">Pincode</label>



			                <input class="form-control" id="pincode" name="emp[pincode]"   value="<?php echo isset($old['pincode']) ? $old['pincode'] : $emp['pincode']; ?>"  size="30" type="text" />



			            </div>



			           



			       </div>



			       <div class="col-sm-4"> 



			       		 <div class='form-group'>



			                <label for="country">Country <span class="error">*</span></label>



			                <input  type="hidden" id="countryval" name="countryval" required="true" value="<?php echo isset($old['country']) ? $old['country'] : $emp['id_country']; ?>"/>



			                <select class="form-control" id="country" name="emp[country]" required="true"></select>


			            </div>



			            <div class='form-group'>



			                <label for="state">State <span class="error">*</span></label>



			                  <input  type="hidden" id="stateval" name="stateval" required="true" value="<?php echo isset($old['state']) ? $old['state'] : $emp['id_state']; ?>"/>



			                <select class="form-control" id="state" name="emp[state]" required="true"></select>




			            </div>



			            <div class='form-group'>



			                <label for="city">City <span class="error">*</span></label>
			                
			                



			                    <input  type="hidden" id="cityval" name="cityval" required="true" value="<?php echo isset($old['city']) ? $old['city'] : $emp['id_city']; ?>"/>



			                <select class="form-control" id="city" name="emp[city]" required="true"></select>



			            </div>



			      </div> 



			      



			      <div class="col-sm-4">



			      	  



			      	  <div class='form-group'>



			                <label for="phone">Phone</label>



			                <input class="form-control" id="phone" name="emp[phone]"   value="<?php echo isset($old['phone']) ? $old['phone'] : $emp['phone']; ?>" size="30" type="text" />



			            </div> 		 



			            <div class='form-group'>



			                <label for="mobile">Mobile <span class="error">*</span></label>



<!-- coded by ARVK --> 			                

			                <div class="input-group">

	              				<span class="input-group-addon input-sm"><?php echo $this->session->userdata('mob_code')?></span>

						    	<input type="hidden" id="mob_no_len" value="<?php echo $this->session->userdata('mob_no_len')?>"/>

                        <input class="form-control" id="mobile" name="emp[mobile]" value="<?php echo isset($old['mobile']) ? $old['mobile'] : $emp['mobile']; ?>" size="30" type="text"  required="true" minlength="10" maxlength="10" onkeypress="return /^[a-zA-Z0-9]$/i.test(event.key)"/>							</div> 

<!-- /coded by ARVK -->

			                

			            </div> 	



			            <div class='form-group'>



			                <label for="email">Email</label>



			                <input class="form-control" id="email" name="emp[email]"  value="<?php echo isset($old['email']) ? $old['email'] : $emp['email']; ?>" size="30" type="email" />



			            </div> 	



			            		



			      </div>  



			      



			      <div class="col-sm-4">



			        



			      </div>



			    	



			    </div>



			      <legend>Login information</legend>



			    <div class="row">



			    	<div class="col-sm-3">



			    	 	<label for="customer_lastname">User name <span class="error">*</span></label>



			    			<div class="form-group">



			    				<input type="text" class="form-control" id="username" required="true" name="emp[username]"  value="<?php echo isset($old['username']) ? $old['username'] : $emp['username']; ?>" />



			    			</div>



			     	</div>



			   



			    	<div class="col-sm-3">



			    	 	<label for="customer_lastname">Password <span class="error">*</span></label>



			    			<div class="form-group">

			    				<!-- 	<input  <?php  if($this->session->userdata('id_branch')=='' && $this->session->userdata('uid')!=1){?> type="text" <?php }?> type="Password" required="true" class="form-control" id="passwd" name="emp[passwd]" value="<?php echo set_value('emp[passwd]',$emp['passwd']); ?>" /> -->

			    					<input type="text" required="true" class="form-control" id="passwd" name="emp[passwd]" value="<?php echo set_value('emp[passwd]',$emp['passwd']); ?>" />

			    			

			    			</div>





			     	</div>



			     	<div class='col-sm-3'>



				             <div class='form-group'>



				                <label for="designation">User Type <span class="error">*</span></label>



				                 <input  type="hidden" id="usertypeval" name="usertype"  value="<?php echo isset($old['id_profile']) ? $old['id_profile'] : $emp['id_profile']; ?>"/>



				                <select class="form-control" id="profile"   required="true" name="emp[id_profile]" >



				                </select>



				            </div>			            



			            </div>

                        <?php if($this->session->userdata('branch_settings')==1){?> 

                            <div class='col-sm-3'>

                                <?php if($this->session->userdata('login_branch')==0){?> 

                                    <div class="form-group" style="height:60px">

                                        <label for="" ><a  data-toggle="tooltip" title="Select branch to create Scheme Account"> Select Branch  </a> <span class="error">*</span></label>

                                        <select  id="branch_select" required="true" class="form-control"></select>

                                        <input id="id_branch" name="emp[id_branch]" type="hidden" value="<?php echo isset($old['id_branch']) ? $old['id_branch'] : $emp['id_branch'];?>"  />

                                    </div>	

                                <?php }?>

                            <?php if($this->session->userdata('login_branch')==1){?> 

                                <div class="form-group" style="height:60px">

                                    <label for="" ><a  data-toggle="tooltip" title="Select branch to create Scheme Account"> Select Branch Permissions </a> <span class="error">*</span></label>

                                    <!-- <select id="login_branch_select" required="true" class="form-control select2 cls" data-placeholder="Select Your Baranchs"></select>
									  -->
									<select   multiple="multiple" id="login_branch_select" class="form-control select2 cls" data-placeholder="Select Your Branches"></select>

                                    <input id="login_branch" name="emp[login_branches]" type="hidden" />

                                    <div id="sel_br" data-sel_br='<?php echo isset($old['login_branches']) ? $old['login_branches'] : $emp['login_branches'];?>'></div> 

                                </div>	

                            <?php }?>

                            </div>

                        <?php } ?>

			    </div> 



			    



			    <div class="row">



			    	



			    	<div class="col-md-12">



			    		<div class='form-group'>



			                <label for="comments">Comments</label>



			               <textarea class="form-control" id="comments" name="emp[comments]"><?php echo isset($old['comments']) ? $old['comments'] : $emp['comments']; ?> </textarea>



			        	</div>



			    	</div>



			    	



			    </div>



			     <div class="row">



				   <div class="box box-default"><br/>



					  <div class="col-xs-offset-5">



						<button type="submit" id="save" class="btn btn-primary">Save</button> 



						<button type="button" class="btn btn-cancel btn-default">Cancel</button>



						



					  </div> <br/>



					</div>



				  </div> 



			    



	            </div>  



	            </form>  



	             <!-- /form -->



	          </div>



             <!-- /form container -->



            </div><!-- /.box-body -->



             <div class="overlay" style="display:none">



				  <i class="fa fa-refresh fa-spin"></i>



				</div>



            <div class="box-footer">



             



            </div><!-- /.box-footer-->



          </div><!-- /.box -->







        </section><!-- /.content -->



      </div><!-- /.content-wrapper -->

	<script type="text/javascript">

    	var emp_id ="<?php echo $emp['id_employee']; ?>";  

    	var mob_no_len ="<?php echo $this->session->userdata('mob_no_len')?>"; 

  	</script>

  