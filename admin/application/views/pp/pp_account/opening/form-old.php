<style type="text/css">
.ui-autocomplete { 
max-height: 200px; 
overflow-y: scroll; 
overflow-x: hidden;
}

#myCheck:checked + #area {
  display: block !important;
}

.col-sm-3 {
    width: 33.333333%;
}

</style>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Scheme Account
            <small>Customer Scheme Account profile</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Savings Scheme</a></li>
            <li class="active">New account</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

          <!-- Default box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Account Form</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
		<?php	$entry_date=$this->admin_settings_model->settingsDB('get','','');?>
            <?php echo form_open(( $scheme['id_scheme_account']!=NULL && $scheme['id_scheme_account']>0 ?'account/update/'.$scheme['id_scheme_account']:'account/save'),array('id'=>'acc_join','enctype' => 'multipart/form-data'));  ?>
              	<div class="col-md-10 col-md-offset-1">
				<div class="row">
				    <div class="col-md-4">
						<div class="form-group">
							<div class="btn-group" data-toggle="buttons">
										<label class="btn btn-primary <?php echo ($scheme['is_new']=='Y' ? 'active':''); ?>">
										  <input name="scheme[is_new]" id="is_new_y" type="radio" value="Y" <?php if($scheme['is_new']=='Y') { ?> checked="true" <?php } ?> > New
										</label>
										<label class="btn btn-primary <?php echo ($scheme['is_new']=='N' ? 'active':''); ?>">
										  <input name="scheme[is_new]" id="is_new_n" type="radio" value="N" <?php if($scheme['is_new']=='N') { ?> checked="true" <?php } ?>> Existing
										</label>
							</div>			
						</div>
					</div>
					<?php if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=1) || $this->config->item('showToAdminsOnly') == 0) {?> 
			    	<div class="col-md-3">
				    	<div class="form-group pull-right">
				    		<label>Stop Payment</label>
				    		<input type="checkbox"  id="block_payment" class="switch" data-on-text="YES" data-off-text="NO" name="scheme[disable_payment]" value="1" <?php if($scheme['disable_payment']==1) { ?> checked="true" <?php } ?>/>
				    	</div>
			    	</div>
			    	
			    	<!--gift articles -->
			    	<!--	<div class="col-md-3">
				    	<div class="form-group">
				    		<label>Gift Articles</label>
				    		<input type="checkbox"  id="show_gift_article" class="switch" data-on-text="YES" data-off-text="NO" name="scheme[show_gift_article]" value="1" <?php if($scheme['show_gift_article']==1) { ?> checked="true" <?php } ?>/>
				    	</div>
			    	</div>	-->
			    		<!--gift articles -->
			    	<div class="col-md-2">
				    	<div class="form-group pull-right">
				    		<label>Active</label>
				    		<input type="checkbox"  id="active" class="switch" data-on-text="YES" data-off-text="NO" name="scheme[active]" value="1" <?php if($scheme['active']==1) { ?> checked="true" <?php } ?>/>
				    	</div>
			    	</div>	
			    	<?php }else{ ?>
			    		<input type="hidden"  name="scheme[disable_payment]" value="0" <?php if($scheme['disable_payment']==1) { ?> checked="true" <?php } ?>/>
			    		<input type="hidden" class="switch" name="scheme[active]" value="1" <?php if($scheme['active']==1) { ?> checked="true" <?php } ?>/>
				    	
			    	<?php } ?>
			    	
			    </div>
			    
			    <!-- setting based branch option hh -->
			        <!--print_r($this->session->all_userdata());-->
					<!-- lines added by Durga may 30 2023 starts here-->
						<input type="hidden" id="branch_setting" value="<?php echo $this->session->userdata('branch_settings'); ?>" />
						<input type="hidden" id="emp_branch" value="<?php echo $this->session->userdata('id_branch'); ?>" />
					<!-- lines added by Durga may 30 2023 ends here-->	
					<div class="row">		
							
					<?php if(($this->session->userdata('branch_settings')==1)  && (($this->session->userdata('id_branch')=='')&&($this->session->userdata('is_branchwise_cus_reg')!=1) || ($this->session->userdata('branchWiseLogin')!=1))){?> 
					<div class="col-sm-4">					
						<div class="form-group" style="height:60px">
								<label for="" ><a  data-toggle="tooltip" title="Select branch to create Scheme Account"> Select Branch  </a> <span class="error">*</span></label>
								<select  required id="branch_select" class="form-control"></select>
								<input id="id_branch" name="scheme[id_branch]" type="hidden" value="<?php echo set_value('scheme[id_branch]',$scheme['id_branch']);?>"  />
						</div>					
					</div>
					<?php  } else {  ?>
					    	<input type="hidden" name="scheme[id_branch]"  value="<?php echo $this->session->userdata('id_branch'); ?>" >
					 <?php  }  ?> 
              			<div class="col-sm-4">
              			  <div class="form-group" style="height:60px">
              					<label for="" ><a  data-toggle="tooltip" title="Select customer to create Scheme Account">Customer Name </a> <span class="error">*</span></label>
              				    <!--<select required id="customer_select" class="form-control"></select>-->
								<input type="text" required="true" class="form-control mobile_number" name="mobile_number" placeholder="Enter The Mobile Number" id="mobile_number"  value="<?php echo set_value('scheme[mobile]',$scheme['mobile']); ?>"  style="width: 99%;" autocomplete="off">
								<input id="id_customer" name="scheme[id_customer]" type="hidden" value="<?php echo set_value('scheme[id_customer]',$scheme['id_customer']); ?>" />
              				</div> 
	
              				
              				<div class="form-group">
              					<div class="box box-default collapsed-box">
								  <div class="box-header with-border">
								    <h3 class="box-title">Customer Details</h3>
								    <div class="box-tools pull-right">
								      <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								    </div><!-- /.box-tools -->
								  </div><!-- /.box-header -->
								  <div class="box-body">
								
								   	<div class="row">
								   	 <div class="col-sm-4">
								   		<div class="form-group">
								   			<img id="cus_img" src="<?php echo base_url();?>assets/dist/img/no_image_available.png" class="img-thumbnail" alt=""  height="150"> 
								   		</div>
								   	 </div>	
								   	 <div class="col-sm-8">
								   		<div class="form-group">
								   			<label>Address</label>
								   			<label id="cus_address" ></label>
								   		</div>
								   		<div class="form-group">
								   			<label>Mobile</label>
								   			<label id="cus_mobile"></label>
								   		</div>
								   		<div class="form-group">
								   			<label>Phone</label>
								   			<label id="phone_mobile"></label>
								   		</div>
								   	 </div>	
								   	</div>
								  </div><!-- /.box-body -->
								</div><!-- /.box -->
              				</div>
              			</div>
              			<div class="col-sm-4">
              				<div class="form-group">
              					<label for="" ><a  data-toggle="tooltip" title="Select scheme ">Scheme</a><span class="error">*</span></label>
              	         		<input type="hidden" id="scheme_val" name="scheme_val" value="<?php echo set_value('scheme_val',$scheme['id_scheme']); ?>" />
               					 <select  required class="form-control" id="scheme" name="scheme[id_scheme]"></select>
              				</div>
              				
              				<div class="form-group">
              					<div class="box box-default collapsed-box">
								  <div class="box-header with-border">
								    <h3 class="box-title">Scheme Details</h3>
								    <div class="box-tools pull-right">
								      <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								    </div><!-- /.box-tools -->
								  </div><!-- /.box-header -->
								  <div class="box-body">
									  <div id="sch_content"> <!-- scheme content -->
									  
									   </div> <!--/ scheme content -->
								  </div><!-- /.box-body -->
								</div><!-- /.box -->
              				</div>
              			</div>
              			
              				<!-- Didable Payment and reason for the disabled -->   
                     <?php if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) {?>       
				    <label>Stop Payment</label>
				    <input type="checkbox"  id="myCheck"  name="scheme[disable_payment]" value="1" <?php if($scheme['disable_payment']==1) { ?> checked="true" <?php } ?>/>
                       <?php } ?> 
                       
                       <?php if($scheme['disable_payment']==1) { ?>
				    <div class="col-sm-3" id="area" style="display: none;" >
				        <label><a>Disable Payment Reason</a><span class="error">*</span></label>
                           <textarea class="form-control" id="disable_pay_reason"  name="scheme[disable_pay_reason]" rows="3" cols="30"><?php echo set_value('scheme[disable_pay_reason]',$scheme['disable_pay_reason']); ?></textarea>
                          </div>
                      <?php } else if($scheme['disable_payment']==0) {?>
                          <div class="col-sm-3" id="area" style="display: none;" >
				        <label><a>Disable Payment Reason</a><span class="error">*</span></label>
                           <textarea  class="form-control" id="disable_pay_reason"  name="scheme[disable_pay_reason]"  rows="3" cols="30"><?php echo set_value('scheme[disable_pay_reason]',$scheme['disable_pay_reason']); ?></textarea>
                          </div>
                          <?php }else if($scheme['disable_pay_reason']== '') {?>
                           <div class="col-sm-3" id="area" style="display: none;" >
				        <label><a>Disable Payment Reason</a><span class="error">*</span></label>
                           <textarea  class="form-control" id="disable_pay_reason"  name="scheme[disable_pay_reason]"  rows="3" cols="30"><?php echo set_value('scheme[disable_pay_reason]',$scheme['disable_pay_reason']); ?></textarea>
                          </div>
                     <?php }?>
              			
              
           <!--get_group code for new sch join code by hh-->
              	<!--	<div class="col-sm-4">					
						<div class="form-group" style="height:60px">
								<label for="" ><a  data-toggle="tooltip" title="Select Group Code"> Group Code  </a> <span class="error">*</span></label>
								<select id="group_select" class="form-control" required="true"></select>
								 <input id="group_code" name="scheme[group_code]" type="hidden" value="" required="true"/>
										<input id="id_scheme_group" name="scheme[id_scheme_group]" type="hidden" value="" required="true"/>
		
						</div>					
					</div>-->
              
              	</div>
					
              		<legend>Account Information</legend>
              		<div class="row">
             			<div class="col-md-12">
             			 <input name="scheme[group_code]" type="hidden" value="<?php echo set_value('scheme[group_code]',$scheme['group_code']); ?>" />
             			 <input type='hidden' class="form-control" id="acc_number" name="scheme[acc_number]" value="<?php echo set_value('scheme[acc_number]',$scheme['acc_number']); ?>"  />
             			<?php if(($scheme['scheme_acc_number']=='Not Allocated' || $scheme['scheme_acc_number']!='' )&& ($scheme['schemeacc_no_set']==0 || $scheme['schemeacc_no_set']==2)){?>
						<div class="col-md-3" >
              				<div class="form-group">
              					<label for="" ><a  data-toggle="tooltip" title="Enter Scheme Account number Correcly">Scheme A/c No</a></label>
              					<!--<?php echo "<br/><b>".$scheme['scheme_acc_number']."</b>"; ?>-->
              					<?php 
					

										if($scheme['schemeaccNo_displayFrmt'] == 0){   //only acc num
																		
											echo "<br/><b>".$scheme['scheme_acc_number']."</b>";
										
										}else if($scheme['schemeaccNo_displayFrmt'] == 1){ //based on acc number generation setting
											
											if($scheme['scheme_wise_acc_no']==0){
												echo "<br/><b>".$scheme['scheme_acc_number']."</b>";
											}else if($scheme['scheme_wise_acc_no']==1){
												echo "<br/><b>".$scheme['acc_branch'].'-'.$scheme['scheme_acc_number']."</b>";
											}else if($scheme['scheme_wise_acc_no']==2){
												echo "<br/><b>".$scheme['code'].'-'.$scheme['scheme_acc_number']."</b>";
											}else if($scheme['scheme_wise_acc_no']==3){
												echo "<br/><b>".$scheme['code'].$scheme['acc_branch'].'-'.$scheme['scheme_acc_number']."</b>";
											}else if($scheme['scheme_wise_acc_no']==4){
												echo "<br/><b>".$scheme['start_year'].'-'.$scheme['scheme_acc_number']."</b>";
											}else if($scheme['scheme_wise_acc_no']==5){
												echo "<br/><b>".$scheme['start_year'].''.$scheme['code'].'-'.$scheme['scheme_acc_number']."</b>";
											}else if($scheme['scheme_wise_acc_no']==6){
												echo "<br/><b>".$scheme['start_year'].''.$scheme['code'].''.$scheme['acc_branch'].'-'.$scheme['scheme_acc_number']."</b>";
											}
										}else if($scheme['schemeaccNo_displayFrmt'] == 2){  //customised
											echo "<br/><b>".$scheme['scheme_acc_number']."</b>";
										}
							
									?>
								<input type='hidden' class="form-control" id="scheme_acc_number" name="scheme[scheme_acc_number]" value="<?php echo set_value('scheme[scheme_acc_number]',$scheme['scheme_acc_number']); ?>"  />
              					 
              				</div>
              			</div>		
						
						
              			<?php }else if($scheme['id_scheme_account']!=NULL && $scheme['schemeacc_no_set']==1 && $scheme['paid_installments']==1){ ?>

						<div class="col-md-3" >
              				<div class="form-group">
              					<label for="" ><a  data-toggle="tooltip" title="Enter Scheme Account number Correcly">Scheme A/c No</a></label>
								<input type='text' class="form-control" id="scheme_acc_number" name="scheme[scheme_acc_number]" value="<?php echo set_value('scheme[scheme_acc_number]',$scheme['scheme_acc_number']); ?>"  />
              					 
              				</div>
              			</div>
						<?php }else if($scheme['paid_installments']>1){ ?>						
						<div class="col-md-3" >
              				<div class="form-group">
              					<label for="" ><a  data-toggle="tooltip" title="Enter Scheme Account number Correcly">Scheme A/c No</a></label>
								<input type='text' class="form-control" id="scheme_acc_number" name="scheme[scheme_acc_number]" value="<?php echo set_value('scheme[scheme_acc_number]',$scheme['scheme_acc_number']); ?>" />
              					 
              				</div>
              			</div>
						
						
						<?php } ?>
              		  
              			<div class="col-md-<?php echo ($scheme['id_scheme_account']!=NULL? '3' : '3');?>">
              				<div class="form-group">
              					<label for=""><a  data-toggle="tooltip" title="Enter Account Name">Scheme A/c Name </a><span class="error">*</span></label>
              					<input type="hidden" id="customer" name="scheme[customer]"  value="<?php echo set_value('scheme[customer]',$scheme['customer']); ?>" />            		
								<input type="hidden" id="id_scheme_account" name="scheme[id_scheme_account]"  value="<?php echo set_value('scheme[id_scheme_account]',$scheme['id_scheme_account']); ?>" />            		
					
					<?php if($entry_date[0]['cusName_edit']==1) { ?> <!-- A/c name edit option in admin based on the settings HH-->
					<input  type="text" class="form-control" id="account_name" name="scheme[account_name]" required="true" value="<?php echo set_value('scheme[account_name]',$scheme['account_name']); ?>"/>
					
					<?php }else if($scheme['id_scheme_account']=='' && $entry_date[0]['cusName_edit']==0) { ?>	
						         <!--<input  type="text" class="form-control" id="account_name" name="scheme[account_name]" required="true" value="<?php echo set_value('scheme[account_name]',$scheme['account_name']); ?>"/>-->
					<input  type="text" class="form-control" id="cus_name" name="scheme[cus_name]"   value="<?php echo set_value('scheme[cus_name]',$scheme['cus_name']); ?>" readonly="true"/>
					
					<?php }else if($scheme['id_scheme_account']!='' && $entry_date[0]['cusName_edit']==0) { ?>	
					
					<input  type="text" class="form-control" id="account_name" name="scheme[account_name]" required="true" value="<?php echo set_value('scheme[account_name]',$scheme['account_name']); ?>" readonly="true"/>
					
					<?php } ?>
						
              				
             				</div>
              			</div>
						
						<div class="col-md-3" >
								<div class="form-group" id="referalcode" style="display:block">
									<label for="" ><a  data-toggle="tooltip" title="Enter the valid Referral code Ex Customer  mobile or Employee Emp-1 etc ">Refferal Code</a><span class="error"><span></label>
									<input type='number' class='form-control width50' name="scheme[referal_code]" placeholder='Refferal Code' id='referal_code' value="<?php echo set_value('scheme[referal_code]',$scheme['referal_code']); ?>"/>
									<input type='hidden' class='form-control width50' id='referalcode_val' />
								</div>
              				</div>
              				
              				<div class="col-md-3" >
								<div class="form-group" id="referalcode" style="display:block">
									<label for="" ><a  data-toggle="tooltip" title="Enter the valid Agent code">Agent Code</a><span class="error"><span></label>
									<input type='number' class='form-control width50' name="scheme[agent_code]" placeholder='Agent Code' id='agent_code'
									    autocomplete="off" tabindex="3" maxlength="10" min="0000000001" max="9999999999" onchange="is_agent_exist(this)" value="<?php echo set_value('scheme[agent_code]',$scheme['agent_code']); ?>"/>
									

									
									
								</div>
              				</div>
              					<div class="col-md-3">
                  				<div class="form-group">
                  					<label for=""><a  data-toggle="tooltip" title="Scheme Account Join Date Correcly">Start Date</a></label>
                  					<div class='input-group date'>
    				                    <input type='text' id="start_date" name="scheme[start_date]" class="form-control" value="<?php echo set_value('scheme[start_date',$scheme['start_date']); ?>" required autocomplete="off" readonly="true"/>
    				                    <span class="input-group-addon">
    				                        <span class="glyphicon glyphicon-calendar"></span>
    				                    </span>
    				                </div>
                  				  </div>
              		         </div>
              				 
              				<input type='hidden' id="get_amt_in_schjoin" name="scheme[get_amt_in_schjoin]" class="form-control" value="<?php echo set_value('scheme[get_amt_in_schjoin',$scheme['get_amt_in_schjoin']); ?>" />
              				<input type='hidden' id="total_installments" name="scheme[total_installments]" class="form-control" value="<?php echo set_value('scheme[total_installments',$scheme['total_installments']); ?>" />
              				<input type='hidden' id="maturity_type" name="scheme[maturity_type]" class="form-control" value="<?php echo set_value('scheme[maturity_type',$scheme['maturity_type']); ?>" />
              				
              				<div class="col-md-3" id="get_amt" style="display: none;">
								<div class="form-group" style="display:block">
									<label for="" ><a  data-toggle="tooltip" title="Enter the Payment Amount ">Payment Amount</a><span class="error">*<span></label>
									<input type='number' class='form-control width50' name="scheme[firstPayment_amt]" placeholder='Payment Amount' id='firstPayment_amt' required='true'   value="<?php echo set_value('scheme[firstPayment_amt]',$scheme['firstPayment_amt']); ?>"/>
								    <input type="hidden" id="flx_denomintion">
								    <input type="hidden" id="min_amount">
								    <input type="hidden" id="max_amount">
								</div>
              				</div>
              				
              				<div class="col-md-3 col-xs-12" >
                                <div class="form-group" id="pan_number" style="display:none">
                                <label for="" ><a >PAN No.</a><span id="pan"><span></label>
                                <input type='text' class='form-control width50' name="scheme[pan_no]" placeholder='PAN Number' id='pan_no' value="<?php echo set_value('scheme[pan_no]',$scheme['pan_no']); ?>"  oninput="this.value = this.value.toUpperCase()"/>
                                <input type='hidden' class='form-control width50' id='referalcode_val' />
                                <input id="pan_req_amt" name="scheme[pan_req_amt]" type="hidden" value="<?php echo set_value('scheme[pan_req_amt]',$scheme['pan_req_amt']);?>"  />
                                <input id="is_pan_required" name="scheme[is_pan_required]" type="hidden" value="<?php echo set_value('scheme[is_pan_required]',$scheme['is_pan_required']);?>"  />
                                </div>
                            </div>
						
						    
                              <div class="col-md-5 col-xs-12" id="panimage" style="display:none;">
                                <div class="form-group" >
                                <label for="" ><a>Upload PAN Image</a></label>
                                <span><br/><b>Note: </b>
                                            <br/>Pan Card : &nbsp;Image file type should be <b>.jpg, .png, .jpeg</b></span>
                                <label for="bankFile">
                                <input id="bank-file-input" type="file" name="panFile"  accept="image/*" onchange="previewFileBank(this);" >
                                <input type="button" id="viewBankFile" value="View File" class="file-button" >
                                <i class="fa fa-close" style="margin-left: 270px;display:none;margin-top: -20px;" id="bank_img_close"></i>
                                <img id="bank-file" src="<?php echo base_url();?>assets/img/customer/pan/customer_pan_<?php echo $scheme['id_customer']; ?>.png"  style="width: 300px; height: 300px;margin-top: 10px; display:none;" />
                                </label>
                                </div>
                              </div>
              
              		    
              		         <?php if($scheme['maturity_date'] != NULL){ ?>
              				<div class="col-sm-3">
                  				<div class="form-group">
                  					<label for=""><a  data-toggle="tooltip" title="Scheme Account Join Date Correcly">Maturity Date</a></label>
                  					<div class='input-group date'>
    				                    <input type='text' id="maturity_date" name="scheme[maturity_date]" class="form-control" value="<?php echo set_value('scheme[maturity_date',$scheme['maturity_date']); ?>" readonly/>
    				                    <span class="input-group-addon">
    				                        <span class="glyphicon glyphicon-calendar"></span>
    				                    </span>
    				                </div>
                  				  </div>
              				</div>
              				<?php } ?>
              				
              			
              		    </div>	
              	</div>	
              	
              	
              	
              	
              		<div class="row" id="pan_data" style="display:none;">
              		    <legend>KYC Verification</legend>
             			<div class="col-md-12">
             			    
             			    <div class="col-md-3" >
								<div class="form-group" id="referalcode" style="display:block">
								    <strong>Customer PAN Details</strong>
									
								</div>
              				</div>
              				
             			    <div class="col-md-3" >
								<div class="form-group" id="referalcode" style="display:block">
									<label for="" ><a >PAN No.</a><span id="pan"><span></label>
									<input type='text' class='form-control width50' name="scheme[pan_no]" placeholder='PAN Number' id='pan_no' value="<?php echo set_value('scheme[pan_no]',$scheme['pan_no']); ?>"  oninput="this.value = this.value.toUpperCase()"/>
								</div>
              				</div>
							
							<div class="col-md-3" >
								<div class="form-group" id="referalcode" style="display:block">
									<label for="" ><a >PAN Card Holder Name</a><span id="pan"><span></label>
									<input type='text' class='form-control width50' name="scheme[pan_name]" placeholder='PAN Holder Name' id='pan_name' value="<?php echo set_value('scheme[pan_name]',$scheme['pan_name']); ?>"/>
								</div>
              				</div>
            
              				
              		    </div>	
              	</div>	
				
				<div class="row" id="aadhaar_data" style="display:none;">
             			<div class="col-md-12">
             			    <div class="col-md-3" >
								<div class="form-group" id="referalcode" style="display:block">
								    <strong>Customer Aadhaar Details</strong>
									
								</div>
              				</div>
             			    <div class="col-md-3" >
								<div class="form-group" id="referalcode" style="display:block">
									<label for="" ><a >Aadhaar No.</a><span id="pan"><span></label>
									<input type='text' class='form-control width50' name="scheme[aadhaar_no]" placeholder='Aadhaar Number' id='aadhaar_no' value="<?php echo set_value('scheme[aadhaar_no]',$scheme['aadhaar_no']); ?>"/>
								</div>
              				</div>
							
							<div class="col-md-3" >
								<div class="form-group" id="referalcode" style="display:block">
									<label for="" ><a>Aadhaar Holder Name</a><span id="pan"><span></label>
									<input type='text' class='form-control width50' name="scheme[aadhaar_name]" placeholder='Aadhaar Card Holder Name' id='aadhaar_name' value="<?php echo set_value('scheme[aadhaar_name]',$scheme['aadhaar_name']); ?>"/>
								</div>
              				</div>
            
              				
              		    </div>	
              	</div>	
              	
              	    
              		 <legend>Opening for existing customers</legend>              		 
              		 <div class="row">
              		             				
              				<div class="col-sm-3">
              					<div class="form-group">
              					<input type="hidden" id="scheme_type" name="scheme[scheme_type]" value="<?php echo set_value('scheme[scheme_type]',$scheme['scheme_type']); ?>" />
              					<label>Is Opening</label>
              						<div class="form-group">
	              						<?php if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) {?> 
	              					      <label class="checkbox-inline">
	              					      	<input type="checkbox" id="is_opening" name="scheme[is_opening]" value="1" <?php if($scheme['is_opening']==1){?>checked="true" <?php } ?>  />                          
	              					      	<span  data-toggle="tooltip" title="Please tick,if previous balance exist for the customer">previous balance </span>
	              					      </label>
	              					      <?php } else{ 
		              					      if($scheme['is_opening']==1){ 
		              					      	echo "Yes";
		              					      }else {
		              					      	echo "No"; ?>
		              					      <input type='hidden' name="scheme[is_opening]" value="<?php echo set_value('scheme[is_opening]',$scheme['is_opening']); ?>" />
	              					      <?php } }?>
	              					 </div>     
              					</div>
              				</div>
              				
              			 <div class="col-sm-3">
              				<div class="form-group">
              					<label for=""><span  data-toggle="tooltip" title="Number of paid installments">Paid Installments</span></label>
              					<?php if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) {?> 
				                    <input type='number' class="form-control open_bal" id="paid_installments" name="scheme[paid_installments]" value="<?php echo set_value('scheme[paid_installments]',$scheme['paid_installments']); ?>" />
				                <?php }else { ?>
									<input type='hidden' class="form-control open_bal"  name="scheme[paid_installments]" value="<?php echo set_value('scheme[paid_installments]',$scheme['paid_installments']); ?>" />
								<?php echo '<br/>'.$scheme['paid_installments']; } ?>
				           
              				  </div>
              				</div>	
              				
              			<div class="col-sm-3">
              				<div class="form-group">
              					<label for=""><span  data-toggle="tooltip" title="Total amount till last paid date">Closing Amount</span></label> <div class="input-group ">
              				
              					<?php if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) {?> 
              					<span class="input-group-addon input-sm" ><?php echo $this->session->userdata('currency_symbol')?></span>
				                    <input type='number' class="form-control input_currency open_bal" id="balance_amount" name="scheme[balance_amount]" value="<?php echo set_value('scheme[balance_amount]',$scheme['balance_amount']); ?>"  />	
				                <?php }else { ?>
									<input type='hidden' class="form-control input_currency open_bal"  name="scheme[balance_amount]" value="<?php echo set_value('scheme[balance_amount]',$scheme['balance_amount']); ?>"  />	 
								<?php echo $this->session->userdata('currency_symbol')." ".$scheme['balance_amount']; }	?>
              				  </div>
              				  </div>
              				</div>	
              				
              				<div class="col-sm-3">
              				<div class="form-group">
              					<label for=""><span  data-toggle="tooltip" title="Last paid date of the scheme account">Last paid date</span></label>
              					<?php ($scheme['last_paid_date']);if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) {?> 
              					<div class="input-group date">
				                    <input  type="text" class="form-control" name="scheme[last_paid_date]" id="last_paid_date"  data-inputmask="'alias': 'dd/mm/yyyy'" data-mask  data-date-format="dd/mm/yyyy" value="<?php echo set_value('scheme[last_paid_date]',$scheme['last_paid_date']); ?>"  />
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				             	</div>
				             	<?php }else { ?>
				             	 <input  type="hidden" class="form-control" name="scheme[last_paid_date]"value="<?php echo $scheme['last_paid_date'] != NULL ?set_value('scheme[last_paid_date]',$scheme['last_paid_date']):''; ?>"  /> 
				             	<?php echo '<br/>'.$scheme['last_paid_date']; }?>
								
				                    
				                    
				                </div>
              				  </div>
              				</div>	
              				
              				<div class="row">
              					<div class="col-sm-4">
		              				<div class="form-group">
		              					<label for=""><span  data-toggle="tooltip" title="Enter weight in grams only">Closing Weight</span></label>
		              					<?php if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) {?> 		              					
						                    <input type='text' class="form-control open_bal" id="balance_weight" name="scheme[balance_weight]" value="<?php echo set_value('scheme[balance_weight]',$scheme['balance_weight']); ?>"   />	
						                <?php }else { ?>
						                <input type='hidden' class="form-control open_bal" name="scheme[balance_weight]" value="<?php echo set_value('scheme[balance_weight]',$scheme['balance_weight']); ?>"   />	
						                <?php echo '<br/>'.$scheme['balance_weight']; } ?>
						            <p class="help-block">Total weight upto last month</p>	
	              				  </div>
	              				</div>	
	              				
	              				<div class="col-sm-4">
		              				<div class="form-group">
		              					<label for=""><span  data-toggle="tooltip"  title="Enter weight in grams only">Last Closing Weight</label>
		              					<?php if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) {?> 		   
						                    <input type='text' class="form-control open_bal" id="last_paid_weight" name="scheme[last_paid_weight]" value="<?php echo set_value('scheme[last_paid_weight]',$scheme['last_paid_weight']); ?>"  />	
						                <?php }else { ?>
						                	<input type='hidden' class="form-control open_bal" name="scheme[last_paid_weight]" value="<?php echo set_value('scheme[last_paid_weight]',$scheme['last_paid_weight']); ?>"  />	
						                <?php echo '<br/>'.$scheme['last_paid_weight'];} ?>
						              <p class="help-block">Total weight purchased in last month</p>      			            
	              				  </div>
	              				</div>	
	              				
	              				<div class="col-sm-4">
		              				<div class="form-group">
		              					<label for="">Payment Chances</label>
		              					<?php if(($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) {?> 		   
						                    <input type='text' class="form-control open_bal" id="last_paid_chances" name="scheme[last_paid_chances]" value="<?php echo set_value('scheme[last_paid_chances]',$scheme['last_paid_chances']); ?>"  />
						                    <?php }else { ?>
						                    <input type='hidden' class="form-control open_bal"  name="scheme[last_paid_chances]" value="<?php echo set_value('scheme[last_paid_chances]',$scheme['last_paid_chances']); ?>"  />
						                    <?php echo '<br/>'.$scheme['last_paid_chances'];} ?>
						            <p class="help-block">No.of payments made in last month</p>      			            				            
	              				  </div>
	              				</div>	
	              				
	              				<!--	<div class="col-md-2" >
    							         <?php if($this->session->userdata('branch_settings')==1){?>
    										<div class="form-group"  align="center">
    										<label for=""><a  data-toggle="tooltip" title="select emp"><h4>Select Employee * </h4></a></label>
    											<select id="emp_select" class="form-control"  required='true'></select>
    											<input id="id_employee" name="scheme[id_employee]" type="hidden" value=""/>
    										</div>
    							       <?php }?>
    							    </div>-->	
    							    
    							    
    							    	<div class="col-md-2" >
    							           <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch') == '' ){?>
    										<div class="form-group"  align="center">
    										<label for="">Select Employee * </label>
    											<select required id="employee_select" class="form-control"  required='true'></select>
    											<input id="id_employee" name="scheme[id_employee]" type="hidden" value=""/>
    										</div>
    							       <?php }?>
    							    </div>
	              				
              				</div>
              		  
              		  <!--gift count option hh-->
              		   <!--<legend> Gift Articles</legend>
                         	<div class="row">
                     		   	<div class="col-md-4"> 
                                 <button  type="button" class="btn btn-warning" id="calc_blc">Issue Gift</button> 
                                </div> 
                                <span class="col-md-3" id="err" style="color:red;"></span>
                                <div class="col-md-4 close_actionBtns"  style="display: none;"> 
                                    <!--<input  type="text" class="form-control" placeholder="Enter The Issued"  id="gift_issued" name="gift_issued"  /> -->
                                   <!-- <select name="gifts" id="gift_issued" class="form-control"></select>
                                 </div>
                      	        <div class="col-md-4 close_actionBtns"  style="display: none;">
                      	            <button type="button" class="btn btn-warning" id="verify_issue" required="true"><i class="fa fa-save"></i> Save</button>
                                </div>
              		        </div> -->
              		    						<!--12-12-2022 old code -->
              		    <!--<legend> Gift Articles</legend>
                         	<div class="row">
                            <span class="col-md-3" id="err" style="color:red;"></span>
		               
                            <div class ="col-md-12">
                                 
            		                <div class="col-md-6" id="show_gifts">
            					 	<div class="form-group">
            					 	    <label>Select Gift</label>
            		                   <select name="gifts" id="gift_list" class="form-control"></select>
            		                   	<input id="gift_val" name="gift[0][gifts]" type="hidden" value=""/>
            		                </div> 	
            		                </div>
            		                
            		                <div class="col-md-6" id="show_prizes">
            					 	<div class="form-group">
            		                   <label>Prize Details</label>
                                       <textarea  type="text" class="form-control" placeholder="Enter gift/prize issue description" name="gift[1][prize]" ></textarea>
            		                </div> 	
            		                </div>-->
<!--altered by RK - 12/12/2022 starts here-->
              		    <!-- show otp giftarticles-->
						
              		    <legend> Gift Articles</legend>
						  
						  
						   <div class="row">
                          <input type="hidden" id="otp_value" name="otp_value" value=""/>
                            <div class="row text-center" id="div_showotp_button">
                                <div class="col-md-12">
                                    <label class="btn btn-success" id="button_otp">Verify Otp</label>
                                </div>
                            </div>
                            
                            <div class="row" id="gift_articles">
                                <span class="col-md-3" id="err" style="color:red;"></span>
                                
                       
                                <div class ="col-md-12">
                                 
                                    <!--<div class="col-md-6" id="show_gifts">
                                    <div class="form-group">
                                        <label>Select Gift</label>
                                       <select name="gifts" id="gift_list" class="form-control"></select>
                                        <input id="gift_val" name="gift[0][gifts]" type="hidden" value=""/>
                                           
                                    </div>  
                                    </div>
                                    
                                      <div class="col-md-6" id="show_prizes">
                                    <div class="form-group">
                                       <label>Prize Details</label>
                                       <textarea  type="text" id="prize_details"  class="form-control" placeholder="Enter gift/prize issue description" name="gift[1][prize]" ></textarea>
                                    </div>  
                                    </div>-->
                                    <!--altered by RK - 12/12/2022 ends here-->
                                    
                                    
                                    <!-- Adding multiple gift select option -->
            		                	<div class="col-md-6" id="show_gifts">
											<div class="form-group"> 
												<label>Select Gift</label>
												<button type="button" id="add_gift_chart" class=" btn btn-success">ADD+</button>
												<span class="error" id="err_msg"></span>
												<!-- Chart for gift table while clicking add button -->
												<div class="table-responsive form-group" id=chart_gift_creation_tbl_div > 
												
													<table id="chart_gift_creation_tbl" width="50%" cellpadding="0"
														cellspacing="0" class="table table-bordered table-striped text-center"
														border="#729111 1px solid" >
														<!--hidden input for storing gift table number of rows  -->
														<input type="hidden" id="table_row_length" name="gift_table_length"/>
														<thead>
															<tr>
																<th>Gift<span class="error">*</span></th>
																<th>Quantity<span class="error">*</span></th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<!--  while Editing, load data from gift_issued(account_form/edit function in admin_manage) table in gift table  -->
															<?php if($gift_data)
															{
																foreach($gift_data as $key=>$gift) 
																{
																	// <!-- if gift type is GIFT , rows are added  -->
																	if($gift['type']=='GIFT')
																	{?>
																	<tr rowID="<?php echo $key+1 ?>" id="<?php echo $key+1 ?>">

																		<td>
																		<select required name='gifts' id='gift_list_<?php echo $key+1 ?>' name='gift_select[<?php echo $key+1 ?>]' 
																		class='form-control' onchange='set_selected_value(this.value,<?php echo $key+1 ?>)' ><input type='hidden' id='quantity_limit_<?php echo $key+1 ?>' value="<?php echo $gift['quantity_limit']-$gift['out_stock']; ?>"/></select>
																		<input id='gift_val_<?php echo $key+1 ?>' name='gift_list_data[<?php echo $key+1 ?>]' type='hidden' value='<?php echo $gift['gift_desc'] ?>'/>
																		</td>
																		<td>
																			<input required type='number' name='gift_quantity[<?php echo $key+1; ?>]' id='gift_quantity<?php echo $key+1; ?>' value='<?php echo $gift['quantity'];  ?>' oninput='check_quantity_limit(this.value,<?php echo $key+1 ?>)'/><br/><span class='error' id='limit_msg<?php echo $key+1 ?>'>Quantity Limit :<?php echo $gift['quantity_limit']-$gift['out_stock']; ?></span>
																		</td>
																		<td>
																			<div>
																			<button id='btn_delete_<?php echo $key+1; ?>' class='delete btn btn-danger'   name='delete'type='button'><i class='fa fa-trash'></i> REMOVE</button>
																			</div>
																		</td>
																	</tr>
																	<?php }
																	
																
																		}
															}
															else
															{?>
															<!-- <tr></tr> -->

															<?php }

															?>
															
														</tbody>
													</table>

												</div>
											
										
											</div>
										</div>
										
                                        <div class="col-md-6" id="show_prizes">
										<div class="form-group">
										<label>Prize Details</label>
													<?php 
													$count=count($gift_data);
													$prize_print=0;
													if($count>0)
														{
															
															foreach($gift_data as $key=>$gift) 
															{
																
																if($gift['type']=='PRIZE')
																{
																	
																	{
																		$prize_print=1;
																		 ?>
																		<textarea  type="text" class="form-control" id="prize_details" placeholder="Enter gift/prize issue description" name="gift[1][prize]" ><?php echo set_value('gift[1][prize]',$gift['gift_desc']); ?></textarea>
																	<?php }
																}
																
																
															}
															if($prize_print==0)
															{?>
																<textarea  type="text" class="form-control" id="prize_details" placeholder="Enter gift/prize issue description" name="gift[1][prize]" ></textarea>
															<?php }
														}
													else
													{
															
														?>
														
														<textarea  type="text" class="form-control" id="prize_details" placeholder="Enter gift/prize issue description" name="gift[1][prize]" ></textarea>
														<?php 
													} ?>					
										</div> 	
									</div>
                                    
                                
                                </div>
                            </div>
                        </div>
              	
              	
              	
              		    <div class="row  hasgift"  >
				            <div class="col-xs-12">
				           
				              <div class="box box-warning box-solid">
				                <div class="box-header">
				                  <h3 class="box-title"><i class="fa fa-gift"></i> Gift Articles</h3>      
				                  <!--<div class="box-tools pull-right">
					                <button  type="button" class="btn btn-success" id="calc_blc">Issue Gift</button>
					              </div>    
					              	<div class="box-tools pull-right">
										<div class="form-group">
										   <select id="calc_blc" class="btn btn-success" style="width:150px;">
										        <option value selected >Select Issue Type</option>
											   <option value=1 >Issue Gift</option>
											     <option value=2>Issue Prize</option>
											</select>
											<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
										</div>
							    </div>-->
				                </div><!-- /.box-header -->
				                <div class="box-body">
				                	<div class="row"> 
										<div class="col-md-6 close_actionBtns"  style="display: none;"> 
											<label>Gift/Prize Details</label>
											<textarea  type="text" class="form-control" placeholder="Enter gift/prize issue description"  id="gift_issued" name="gift_issued" ></textarea>
											<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
										</div>
										<div class="col-md-2 close_actionBtns"  style="display: none; padding: 26px">
										    <label></label>
											<button type="button" class="btn btn-success" id="submit_gift" required="true"><i class="fa fa-save"></i> Save</button>
											<input type="hidden" id="req_gift_issue_otp" name="req_gift_issue_otp"  value="<?php echo $entry_date[0]['req_gift_issue_otp'] ?>">
											<input type="hidden" id="req_prize_issue_otp" name="req_prize_issue_otp"  value="<?php echo $entry_date[0]['req_prize_issue_otp'] ?>">
										</div>
										<div class="col-md-3 gift_otp_blk" style="display: none;">
										    <label>Customer Verification OTP</label>
    										<div class="input-group margin">
                        						<input type="text" id="otp" name="otp" value="" placeholder="6 Digit OTP" class="form-control"/>
                        						<span class="input-group-btn">
                        							<button type="button" id="gift_verify_otp" name="gift_verify_otp" class="btn btn-info btn-flat">Verify
                        						</span> 
                    						</div> 
                    						<div class="col-md-2 gift_resent_otp" style="display: none;"> 
                    						<input type="button" id="resendotp" class="btn btn-warning" value="Resend OTP"  /> 
                    						</div> 
                    					</div> 
									</div>
									
									<div class="row"  id="otp_status">			    	
                        		    	<div class="col-md-12">
                        		    		<div class='form-group'>
                        		                <label ></label>	               
                        		        	</div>
                        		    	</div>			    	
                        			</div>	
            						<div class="row"  id="status">
										<div class="col-md-12">
											<div class='form-group'>
											<label></label>
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-12">
											<legend>Gift/Prize Details</legend>
											<div class="table-responsive">
							                 <table id="gift_issued_lists" class="table table-bordered table-striped text-center">
							                    <thead>
							                      <tr>
							                        <th>ID</label></th>
							                        <th>Type</th>
							                        <th>Description</th>
							                        <th>Quantity</th>
							                        <th>Status</th>
							                        <th>Issued By</th>   
							                        <th>Issued On</th>
							                      </tr>
							                    </thead> 
							                 </table>
						                  </div>
										</div>
									</div> 
				                </div><!-- /.box-body -->
				                <div class="overlay" style="display:none">
								  <i class="fa fa-refresh fa-spin"></i>
								</div>
				              </div><!-- /.box -->
				            </div><!-- /.col -->
				          </div><!-- /.row -->
				         
                <!--gift issued list  --> 		    
             		    
          
                          </div>  
            <!--gift count option hh-->
                          
              		  	 
      
          <!--        	<legend>Nominee details</legend>
              		<div class="row">
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Nominee Name</label>
              					<input  type="text" class="form-control" id="nominee_name" name="scheme[nominee_name]"  value=""/>
              				</div>
              			</div>
              			
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Relationship</label>
              					<input  type="text" class="form-control" id="nominee_relation" name="scheme[nominee_relation]" value=""/>
              				</div>
              			</div>
              		</div>
          		<legend>Introducer details</legend>
              		<div class="row">
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Name</label>
              					<input  type="text" class="form-control" id="intro_name" name="scheme[intro_name]"/>
              				</div>
              			</div>
              			
              			<div class="col-sm-6">
              				<div class="form-group">
              					<label for="">Mobile</label>
              					<input  type="text" class="form-control" id="intro_mobile" name="scheme[intro_mobile]"/>
              				</div>
              			</div>
              		</div>-->
              		
              		
              		<!-- Voucher block starts here -->
					<div class="col-md-10 col-md-offset-1" id="voucher_info_block" style="display:none;">
						
						<legend>Voucher</legend>
						<div class="row">
							<div class="col-md-12">
								<div class='form-group'>
									<div class="col-md-4">
										<label for=""><a  data-toggle="tooltip" title="Enter Voucher Number">Voucher Code </a></label>
										<input  type="text" placeholder="ex:S123" class="form-control" id="voucher_num" name="scheme[voucher_code]" value="<?php echo set_value('scheme[voucher_code]',$scheme['voucher_code']); ?>"/>
									</div>
									<div class="col-md-4">
										<label for=""><a  data-toggle="tooltip" title="Enter Voucher Amount">Voucher Amount </a></label>
										<input  type="text" placeholder="Enter the Amount" onkeypress="return /^[0-9]$/i.test(event.key)" class="form-control" id="voucher_value" name="scheme[voucher_value]" value="<?php echo set_value('scheme[voucher_value]',$scheme['voucher_value']); ?>"/>
									</div>
									<div class="col-md-4">
										<label for=""><a  data-toggle="tooltip" title="Upload Voucher Image">Voucher Image </a></label>
										<input id="voucher_img" name="voucher_img" accept="image/*" type="file" >
										<!-- <input id="voucher_img" name="scheme[voucher_img]" accept="image/*" type="file" > -->
							 			<img src="<?php echo(isset($scheme['voucher_img'])?$scheme['voucher_img']: base_url().('assets/img/no_image.png')); ?>" class="img-thumbnail" id="voucher_img_preview" style="width:304px;height:200px;" alt="Voucher image"> 
									</div>
									
								</div>
							</div>							
						</div>
					</div>
					<!-- Voucher block ends here -->
              		
              		<div class="row">
			    	
			    	<div class="col-md-12">
			    		<div class='form-group'>
			                <label for="user_lastname">Comments</label>
			               <textarea class="form-control" id="remark" name="scheme[remark_open]"><?php echo set_value('scheme[remark_open]',$scheme['remark_open']); ?></textarea>
			        	</div>
			    	</div>
			    	
			    </div>
			     <div class="row">
			       <div class="col-sm-12">
				   <div class="box box-default"><br/>
					  <div class="col-xs-offset-5">
						<button type="submit" id="submit" class="btn btn-primary">Save</button> 
						<button type="button" class="btn btn-default btn-cancel">Cancel</button>
					  </div> <br/>
					</div>
					</div>
				  </div> 
              		
              		
              	</div>
             </div>
				<div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
			    </div>
            </div>
            <div class="box-footer">            
            </div><!-- /.box-footer-->
          </div><!-- /.box -->
          

        </section><!-- /.content -->
      </div><!-- /.content-wrapper --> 
	 <!--<script type="text/javascript">
     var customerList  = new Array();
     var customerListArr = new Array();
     customerListArr = JSON.parse('<?php echo json_encode($cus); ?>');
     </script>-->	 

<!-- Created by RK 12/12/2022-->
			 <!--modal for gift issue ---- otp-- Starts here-->
			<!-- modal -->      
		<div class="modal fade" id="verify_otp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Verify OTP</h4>
			  </div>
			  <div class="row">
				<div class="modal-body text-center">
					 <label class="col-md-5 text-right">Mobile Number :</label>  
					 <input class="col-md-4" id="txt_mobile" type="text" value="" />
				</div>
			 </div>
			 <div class="row" id="otp_txt_box">
				<div class="modal-body text-center">
					 <label class="col-md-5 text-right">Enter OTP :</label>  
					 <input class="col-md-4" id="otp_data" type="text" value="" />
				</div>
			 </div>
			 <br>
			 <br>
			  <div class="modal-footer">
				<a href="#" id="send_otp_gift" class="btn btn-success btn-confirm" value="Send OTP" >Send OTP</a>
				<a href="#" id ="verify_otp_gift" class="btn btn-danger btn-confirm" value="Verify OTP" >Verify OTP</a>

				<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			  </div>
			 
			</div>
		  </div>
		</div>
		<!-- / modal -->   
		 <!--modal for gift issue ---- otp-- Ends  here--> 