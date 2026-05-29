<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
		Customer
		<small>Complete profile</small>
		</h1>
		<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Master</a></li>
		<li class="active">Add Customer</li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<!-- form -->
		<form id="cus_profile">
		<!-- Default box -->
		<div class="box">
			<div class="box-header with-border">
    			<h3 class="box-title">Profile</h3>
    			<div class="box-tools pull-right">
        			<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        			<button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
    			</div>
			</div>
			<div class="box-body">
				<div class="col-md-12">  
					<div class="tab-content col-md-10">
							<div class="col-md-12">
							    <div class="row">
							        <div class="col-md=10">
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
							        </div>
							        <div id="chit_alert1"></div> 
							    </div>
							    <div class='row' id="progress_bar" style="display:none;">
							       <div class="progress progress-striped active">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <span class="skill">%</span>
                                        </div>
                                    </div>
							    </div>
							    <div class='row'>
							         <div class='col-sm-4'>
							            <input type="text" class="form-control" id="search_customer" placeholder="Search Mobile/Name">
							            <input type="hidden" id="id_customer"  name="customer[id_customer]">
							            <div id="customerAlert"></div>
							         </div>
							          <div class="col-md-4"> 
                                            <div class="form-group"> 
                                                <label>Send Promotion SMS </label>
                                                <input type="checkbox"  id="show_gift_article" class="switch" data-on-text="YES" data-off-text="NO" name="customer[send_promo_sms]" value="1" /> 
                                            </div>	 
                                        </div>
							    </div></br>
    							<div class='row'>							       
    						        <div class='col-sm-4'>
    						            <div class='form-group'>
    						                <label for="customer_firstname"> <a  data-toggle="tooltip" title="Invalid characters 0-9"> First name</a> <span class="error">*</span></label>
    						                <input class="form-control input_text" id="firstname" name="customer[firstname]" value="" type="text" />
    						            </div>
    						        </div>
    						        <div class='col-sm-4'>
    						            <div class='form-group'>
    						                <label for="customer_lastname" data-toggle="tooltip" title="Invalid characters 0-9"> Last name</label>
    						                <input class="form-control input_text" id="lastname" name="customer[lastname]" value="" type="text" />
    						            </div>
    						        </div>	
    						        <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="city">Village <span class="error">*</span> </label>
							                  <input  type="hidden" id="id_village" value=""/>
							                <select class="form-control" id="Village" name="customer[id_village]" style="width:100%;"></select>
							            </div>
							        </div>
    						        	
    						    </div>
						    </div></br>
						    
						    <div class="col-md-12">
							     <div class="row">
							         
							         <div class="col-sm-4"> 
                                            <div class='form-group'>
                                                    <div class='form-group'>
                                                        <label for="date_of_birth">Date of Birth <span class="error">*</span> </label>
                                                        <div id="wrapper"></div>
                                                         <input id="datepicker"  name="customer[date_of_birth]"  type="text" class="dob"><br><br>
                                                    </div>
                                            </div>
							           </div>
							           <div class="col-sm-4"> 
                                            <div class='form-group'>
                                                    <div class='form-group'>
                                                        <label for="date_of_birth">Date of Wedding</label>
                                                        <div id="wrapper">
                                                            <input id="datepicker1"  name="customer[date_of_wed]"  type="text" class="wedding"><br><br>
                                                        </div>
                                                    </div>
                                            </div>
							           </div>
							           <div class="col-sm-4">
    							     	 <div class='form-group'>
    							                <label for="email">E-Mail</label>
    							                <input class="form-control" id="email" name="customer[email]"  value=""  type="email" />
    							                
    							            </div> 
    							     </div>
							     </div>
							  </div><br/>
						 
						    <div class="col-md-12">
						        <div class="row">
							      <div class="col-sm-4">
							      		<div class='form-group'>
							                <label for="address1">Address1</label>
							                <input class="form-control titlecase" id="address1" name="customer[address1]" type="text" />
							            </div>	
							        </div>
							        <div class="col-sm-4">	
							            <div class='form-group'>
							                <label for="address2">Address2</label>
							                <input class="form-control titlecase" id="address2" name="customer[address2]" value=""   type="text" />
							            </div>
							        </div>
							        <div class="col-sm-4">	
										<div class='form-group'>
							                <label for="address3">Address3</label>
							                <input class="form-control titlecase" id="address3" name="customer[address3]" value=""   type="text" />
							            </div>
							        </div>
							     </div>
							 </div>
							 
    							 <div class="col-md-12">
        						    <div class="row">
        							        <div class="col-sm-4">	
        							            <div class='form-group'>
        							                <label for="country">Country </label>
        							                 <input  type="hidden" id="countryval" name="countryval" value=""/>
        							                <select class="form-control" id="country" name="customer[country]"  style="width:100%;"></select>
        							            </div>
        							        </div>
        							        <div class="col-sm-4">	
        							            <div class='form-group'>
        							                <label for="state">State </label>
        							                <input  type="hidden" id="stateval" name="stateval" value=""/>
        							                <select class="form-control" id="state" name="customer[state]" style="width:100%;"></select>
        							            </div>
        							        </div>
        							        <div class="col-sm-4">	
        							            <div class='form-group'>
        							                <label for="city">City </label>
        							                  <input  type="hidden" id="cityval" name="cityval" value=""/>
        							                <select class="form-control" id="city" name="customer[city]" style="width:100%;"></select>
        							            </div>
        							        </div>
        							</div>
    						    </div>
							   <div class="col-md-12">
							       <div class="row">
							           <div class="col-sm-4"> 
                                            <div class='form-group'>
                                                <label for="gender">Gender <span class="error">*</span></label>
                                                <div class="form-group">
                                                    <p class="help-block"></p>
                                                    <input type="radio" id="gender_male" name="customer[gender]" value="0" class="minimal" />Male
                                                    <input type="radio" id="gender_female" name="customer[gender]" value="1" class="minimal" />Female
                                                    <input type="radio" id="gender_others" name="customer[gender]" value="3" class="minimal" />Others
                                                </div> 
                                            </div>
							           </div>
    							        <div class="col-sm-4">	
    							            <div class='form-group'>
                                                    <label for="pincode">Pincode</label>
                                                    <input class="form-control input_number"  minlength="6" maxlength="6" id="cus_pincode" name="customer[pincode]"  value=""   type="text" />
                                                </div>
    							        </div>
    							       <div class="col-sm-4">	
    							             <div class="form-group">
                                                    <label for="" ><a  data-toggle="tooltip" title="Select branch to create Scheme Account"> Select Religion</a></label>
                                                    <select  id="religion_select" name="customer[religion]" class="form-control">
                                                        <option> Select Religion Name</option>
                                                        <option value="1">Hindu</option>
                                                        <option value="2">Muslim</option>
                                                        <option value="3">Christian</option>
                                                    </select>
                                                    <input type="hidden"  id="religion" value="">				
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
		<div class="row">
		   <div class="box box-default"><br/>
			  <div class="col-xs-offset-5">
				<button type="button" id="update_profile"  class="btn btn-primary">Save</button> 
				<button type="button" class="btn btn-default btn-cancel">Cancel</button>
			  </div> <br/>
			</div>
		  </div> 
		<?php echo form_close();?> 
	</section><!-- /.content -->
</div><!-- /.content-wrapper -->
   <script type="text/javascript">

    var cust_id ="<?php echo $customer['id_customer']; ?>";   

    var mob_no_len ="<?php echo $this->session->userdata('mob_no_len')?>";   

  </script>