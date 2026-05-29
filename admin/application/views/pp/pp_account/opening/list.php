  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Scheme Account List
		 
			<span id="total_accounts" class="badge bg-aqua"></span>
            
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Savings Schemes</a></li>
            <li class="active">Scheme Account</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
           <!-- <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header"><h4>Sync A/c and Payment</h4></div>
                        <div class="box-body">
                            <?php 
        	               /* Jilaba Sync */
        	               if((($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) && $this->config->item('integrationType') == 1 ){  ?>
        				   <div class="row"> 	
        	                 <div class="col-md-12">                  
                               <div class="form-group">
                                <a class="btn  btn-primary pull-right" href="<?php echo base_url('index.php/admin_manage/update_client_jil'); ?>"><i class="fa fa-retweet"></i> Sync Account</a> 
                               </div>
                              </div>
                              </div><br/> 
        	               <?php 
        	               }
        	               /* Tool Sync */
        	               else if((($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) && $this->config->item('integrationType') == 2 ){  
        						echo form_open('admin_manage/update_client',array('id'=>'sync_acc')); ?> 
        						<div class="row"> 
        					<div class="col-md-12">
        						  <div class="col-md-3">
        						  	<label>Trans Date</label> <input id="sync_date" required="true" name="sync_trans_date" type="date"/>
        						  </div> 
        						  <?php if($this->session->userdata('branch_settings')==1){?>
        						  <div class="col-md-3">
        						    <div class="form-group">
        						      <label>Branch &nbsp;&nbsp;</label>
        						      <select id="sync_branch" name="sync_branch_id" class="form-control" style="width:150px;"></select>  
        						    </div>  
        						  </div>
        						  <?php } ?>
        						  <div class="col-md-3">                  
        							  <div class="form-group">
        							    <button type="submit" class="btn  btn-warning "><i class="fa fa-retweet"></i> Sync Data</button> 
        							  </div>
        						  </div> 
        					<?php echo form_close();}?> 
                        </div>
                    </div>
                </div>
            </div>    
            -->
            
          <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-body">  
		         <div class="row">
		           <div class="col-sm-8 col-sm-offset-2">
		            <div id="error-msg"></div>
		            <div id="payment_container"></div>
		          </div>
		        </div> 
		        
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
			<!--	 <h3 class="col-md-4 box-title ">Scheme Account List <span id="total_accounts" class="badge bg-aqua"></span></h3> -->
				 <br/>
	              <div class="col-md-12"> 
                    <?php if($this->payment_model->entry_date_settings()==1){?>	
						<div class="col-md-2">
							<div class="form-group">
							   <label>Filter Date By</label>
								<select id="date_Select" class="form-control" style="width:150px;"> 
								    <option value=1 selected>Start Date</option>
								     <option value=2>Entry Date</option>
								</select>
								<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
							</div>
					    </div>
				    <?php }?>
					<div class="col-md-2">
					    <br/> 
	                   <div class="form-group">                     
	                    <button class="btn btn-default btn_date_range" id="account-dt-btn">
	                      <span  style="display:none;" id="account_list1"></span>
	                      <span  style="display:none;" id="account_list2"></span>
	                      <i class="fa fa-calendar"></i> Date range picker
	                      <i class="fa fa-caret-down"></i>
	                     </button>  
	                   </div>
	                </div> 
	                  <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
	                    <div class="col-md-2">
	                      <div class="form-group">
	                       <div class="form-group">
		                      <label>Filter By Branch</label>
		                      <select id="branch_select" class="form-control" style="width:150px;"></select>
		                      <input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
		                    </div> 
		                       </div> 
	                    </div>
	                  <?php }else{?>
	                    <input id="id_branch" name="scheme[id_branch]" type="hidden" value="<?php echo $this->session->userdata('id_branch'); ?>"/>
	                  <?php }?>
		                  
                      <div class="col-md-2">
                        <div class="form-group">
                               <div class="form-group">
                              <label>Search by mobile</label>
                              <input type="text" placeholder="Mobile Number" class="form-control" name="" id="mobilenumber" >
                              <input type="hidden" name="id_customer" id="id_customer"/> 
                            </div>
                       </div>  
                      </div>  
                      
                      <div class="col-md-2">
                        <div class="form-group">
                               <div class="form-group">
                              <label>Joined Date</label>
                              <select id="join_day" class="form-control" style="width:150px;"> 
                                <option value=0>Select duration</option>
								<option value=324>324 days</option>
								<option value=355>355 days</option>
							  </select>
                            </div>
                       </div>  
                      </div> 
                      
                      <div class="col-md-2">
	                      <div class="form-group">
	                       <div class="form-group">
		                      <label>Filter By Scheme</label>
		                      <select id="scheme_select" class="form-control" style="width:150px;"></select>
		                      <input id="id_scheme" name="scheme[id_scheme]" type="hidden" value=""/>
		                    </div> 
		                       </div> 
	                    </div>
	                    
	                    <div class="col-md-2" id="scheme_group" style="display:none;">
	                      <div class="form-group">
	                       <div class="form-group">
		                      <label>Filter By Group</label>
		                      <select id="group_select" class="form-control" style="width:150px;"></select>
		                      <input id="group_id" name="scheme[group_id]" type="hidden" value=""/>
		                    </div> 
		                       </div> 
	                    </div>
                      
	                 <?php if($this->account_model->get_accnosettings()==1){?>	 
    					<div class="col-md-2 " > 
	                    <br/>
    	                   <div class="form-group pull-right">
    	                    <button type="button"  id="conform_save" class="btn btn-primary conform_sch"><i class="fa fa-user-plus"></i>Update Accno</button>
    	                   </div>  
                        </div>           
	                <?php }?>  
                    
        			<div class="col-md-2">   
        			<br/>               
					  <div class="form-group pull-right">
		                <a class="btn bg-green" id="add" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 
		              </div> 
					</div> 
					
				<!--	<div class="col-md-2">   
        			<br/>               
					  <div class="form-group pull-right">
		                <a class="btn btn-warning" id="rate_fix" href="">Fix Rate</a> 
		              </div> 
					</div> -->
					
		          </div>
		       </div>
		          <br/>
               <?php if($this->account_model->get_accnosettings()==1){?> 
                <div class="table-responsive">
                  <table id="sch_acc_list" class="table table-bordered table-striped text-center grid" role="grid">
                    <thead>
                      <tr>
                        <th><label class="checkbox-inline"><input type="checkbox" id="select_aldata"  name="select_all" value="all"/>All</label></th> 
                        <th>Sch ID</th> 
                        <th>Cus Id</th> 
                        <th>Customer</th>
                        <th>Mobile</th> 
                        <th>Branch</th>
                        <th>A/c Name</th> 
						<th>Scheme code</th>
						<th> A/c No</th>   
						<th>Group Code</th>  
						<th>Type</th>       
						<th>Start Date</th>
                        <th>Custom Entry Date</th>
                         <th>Last Paid Date</th>
						<th>Scheme Type</th> 
                        <th>Installment Payable</th> 
                        <th>PAN No.</th> 
                        <th>Paid Ins</th>
                        <th>Gift</th>
                        <th>Status</th>
                        <th>Account Wallet</th>   <!-- DGS-DCNM -->
                        <th>Created Through</th>
                      <th>Rate Fix</th> 
                        <th>Saved Wgt</th>
                         <th>Referred By</th>
                        <th>Ref Code</th>
                        <th>Agent Name & Code</th>
                        <th>Action</th>
                      </tr>
                    </thead> 
                  </table>
                  </div>
                <?php }else{?>  
      
                  <div class="table-responsive">
                          <table id="sch_acc_list" class="table table-bordered table-striped text-center grid" role="grid">
                            <thead>
                              <tr> 
                                <th>Sch ID</th>
                                <th>Cus Id</th>  
                                <th>Customer Name</th>  
                                <th>Mobile</th>
                                <th>Branch</th>
                                <th>A/c Name</th>  
                               <th>Scheme Code</th>
                               <th> A/c No</th>
                               <th>Group Code</th>  
                               <th>Type</th>  
                               <th>Start Date</th>
                               <th>Custom Entry Date</th>
                               <th>Last Paid Date</th>
                                <th>Scheme Type</th> 
                                <th>Installment Payable</th> 
                                <th>PAN No.</th> 
                                <th>Paid Ins</th>
                                 <th>Gift</th>
                                <th>Status</th>
                                <th>Account Wallet</th>   <!-- DGS-DCNM -->
                                <th>Created Through</th>
                                 <th>Rate Fix</th> 
                                 <th>Saved Wgt</th>
                                 <th>Referred By</th>
                                 <th>Ref Code</th>
                                  <th>Agent Name & Code</th>
                                <th>Action</th>
                              </tr>
                            </thead> 
                          </table>
                          </div>
                  
              <?php }?>       
                </div>
				  <label>Note:&nbsp;Last 7 days Scheme Account List</label>
              </div><!-- /.box-body -->
                 <div class="overlay" style="display:none">
				<i class="fa fa-refresh fa-spin"></i>
				</div>
				 
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      
<!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Scheme</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this scheme?</strong>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
<!-- modal close account -->      
<div class="modal fade" id="confirm-close" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Confirm Close Scheme</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to close this scheme account?</strong>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-danger btn-confirm" >Close Account</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal close account -->  

<!-- Rate Fix Modal HH -->
<div class="modal fade" id="otp_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">

	    <div class="modal-content">
 <div class="modal-header ">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 id="myModalLabel">Mobile Number Verification</h3>

		  </div>

<div class="modal-body">
	<p>Please enter the code sent to your mobile number</p>

	<div>

                    	<input type="hidden" id="id_scheme_account" value="<?php echo set_value('id_scheme_account[id_scheme_account]'); ?>"/>
                    		<input type="hidden" id="mobile" value="<?php echo set_value('mobile[mobile]'); ?>"/>
					
					<label style="display:inline; margin:5px" for="otp">Enter Code:</label>

	<input  style="display:inline; width:30%; margin:5px" type="text" id="otp" name="otp" value="" class="form-control" required/>

<!--<a style="margin-right:1%;margin-left:1%;cursor: pointer;" id="resendOTP" >Resend OTP</a> -->
  <input type="submit" id="resendotp" value="Resend OTP" class="resendotp">  </input>

<span id="OTPloader"><img src="<?php echo base_url()?>assets/img/loader.gif" ></span>
	</div>
<div class="modal-footer">
	<input style="margin-left:1%" type="submits" value="Submit" id="submits" style="background-color:#0079C0"  class="button btn btn-primary btn-large" />
</div>
 </div>
</div>
</div>
	</div>
	<!-- Rate Fix Modal  -->
	
	
		
<!-- Chit Wallet Screen Modal DCNM- DGS -->

  <div class="modal fade" id="chit_wallet_screen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header modalwallet-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel">Transaction Details</h4>
		</div> 
      <div class="modal-body">
			 <table class="table wallet_table">
					<thead>
						<p class="wallet_table1">Wallet screen</p>
						<tr>
							<th  style="text-align:center">Joined Date : <p id="start_date"></p></th>
							<th colspan="2" style="text-align:center">Payment can be made till : <p id="allow_pay_till"></p> </th>
						</tr>
					<!--	<tr>
						  <th style="text-align:center"><span>Redemption date</span> / <span id="days_count"></span></th>
					  </tr>-->
					</thead>
					<tbody id="chit_tab">
						<tr>
							<td style="text-align:center">Total count of amount paid</td>
							<td style="text-align:center" id="pay_count"></td>
						</tr>
						<tr>
							<td style="text-align:center">Total advance amount paid</td>
							<td style="text-align:center" id="paid_tot"></td>
						</tr>
						<tr>
							<td style="text-align:center">Total weight saved</td>
							<td style="text-align:center" id="saved_wgt"></td>
						</tr>
						<tr id="int_td" style="display:none;">
							<td style="text-align:center">Total interest saved till <p id="cur_day"></p> <p id="interest"></p></td>
							<td style="text-align:center" id="saved_int"></td>
						</tr>
						<tr id="debit_td" style="display:none;">
							<td style="text-align:center">If preclosed till <p id="debit_day"></p> <p id="debit_int"></p></td>
							<td style="text-align:center" id="debit_intval"></td>
						</tr>
						<!--<tr id="happy_td" style="display:none;">
							<td colspan="2" style="text-align:center">Date Crossed... STAY HAPPY</td>
						</tr>-->
					</tbody>
				</table>
            <strong>For more detailed information, <a href="#" id="chit_report_link" target="_blank">click here</a></strong>
			
      </div>
    </div>
  </div>
</div>

<!-- Chit Wallet Screen Modal DCNM- DGS -->