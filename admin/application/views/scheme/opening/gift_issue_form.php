<style type="text/css">
.ui-autocomplete { 
max-height: 200px; 
overflow-y: scroll; 
overflow-x: text;
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
        <h1>Gift Issue<small> Gifts from inventory</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Savings Scheme</a></li>
            <li class="active">Gift Issue</li>
        </ol>
    </section>
    
    <!-- Main content -->
    <section class="content">
    <!-- Default box -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Issue Gift Form</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="box-body">
            <div class="col-md-12"> 
            
            
                <div class="row">
                    
                    

                	<div class="col-md-3">

                		<div class="form-group">

                			<label for="" >Customer Mobile</label>

							<input type="text" class="form-control mobile_number" name="mobile_number" placeholder="Enter Mobile Number" id="mobile_number" value="<?php echo ($this->session->userdata('cus_mobile')!='' ? $this->session->userdata('cus_mobile').'-'.$this->session->userdata('cus_name') :''); ?>" style="width: 99%;">

                			<input type="hidden" name="gift[id_customer]" id="id_customer" value="<?php echo $this->session->userdata('cus_id'); ?>"/>

                			<input type="hidden" id="cus_mobile" value=""/>

                		</div>	

                	</div>
                	
                	<!--<div class="col-md-3">
                        
                        <div class="form-group">

                            <label for="" >Search By Account No</label>

                            <input type="text" class="form-control Scheme_account_no" name="Scheme_account_no" placeholder="Enter Scheme Account No" id="Scheme_account_no" value="<?php echo ($this->session->userdata('cus_mobile')!='' ? $this->session->userdata('cus_mobile').'-'.$this->session->userdata('cus_name') :''); ?>" style="width: 99%;">

                        </div> 
                        
                    </div>-->

		            <div class="col-md-3">

                        <div class="form-group">
                        
                            <label for="" >Scheme A/c No</label>
                            
                            <select class="form-control" name="gift[id_scheme_account]" id="scheme_account" style="width:100%;"></select>
                            
                            <input type="hidden" class="form-control" id="id_scheme_account"  value="" />
                        
                        </div>

					</div>
					
					<div class="col-md-3">

                        <div class="form-group">
                            

                            <label for="" >Cost Center</label>
                            
                            <select class="form-control"  name="gift[id_branch]" id="branch_select" style="width:100%;"  <?php echo ($this->session->userdata('id_branch') > 0 ? 'disabled' : '') ?> ></select>
                            
                            <input type="hidden" class="form-control" id="branch_sel"  value="<?php echo ( $this->session->userdata('id_branch') > 0 ? $this->session->userdata('id_branch')  : '') ?>" />
                            
                            

                        </div>

					</div>
						  
					<!--<div class="col-md-3 ">

							<div class="form-group">

								<label for="">SCAN QR/ BAR CODE (Account)</label>

								<input  id="qr_scan_scheme_account_id" type="text" class="form-control" />

							</div>	
                    </div>	-->
					
                </div>
                
                <hr>
                
               
                            		
                            		
        <form id="gift_issue_form">   
        
        <input id="paid_installments" type="hidden" name="paid_installments" />
        
        <input id="isOTPReqToGift" type="hidden" value="<?php echo $this->session->userdata('isOTPReqToGift'); ?>" />
                <div class="row col-md-12">
                    <div class="text-center">

                        <div class="col-md-6">
                            
                           
                                
                            
                            
                           <div id="newGiftIssueDiv" style="display:none;">
                            <!--Select or scan gift-->		
                            <div class="row">
                                <div class="col-md-12">
                            	   
                            		<input type="hidden" id="cur_gift_value" class="form-control" value="0">
        
                            		<div class="col-md-6">
                            			<label for="selectBox">Scan Your Gift</label>
                            			<input type="text" id="ref_gift" class="form-control">
                            		</div>	
                            		
                            		<div class="col-md-6">
                            			<label for="selectBox">Select Your Gift</label>
                            			<select id="gift_select_inv" class="form-control">
                            			    
                            			</select>
                            		</div>
                            	</div>	
                            </div>
                            <br/><br/>
                            <!--Yet to Issue gift Data-->
                           
                            <div class="row">	
                        		<div class="col-md-12" style="align:center;">
                        		    <input type="hidden" id="gift_scheme" name="id_scheme" class="form-control">
                            		<input type="hidden" id="gift_account" name="id_scheme_account" class="form-control">
                            		<input id="id_branch" name="id_branch" type="hidden" value="<?php if($this->session->userdata('id_branch') > 0){echo $this->session->userdata('id_branch') ;} ?>"/>
                        			<table id="gift_chart_tbl_inv" width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-striped text-center" border="#729111 1px solid" >
                        				<input type="hidden" id="gift_tbl_len" name="gift_table_length" value="0"/>
                        				<thead>
                        					<tr>
                        					    <th>Gift ID</th>
                        						<th>Gift Name</th>
                        						<th>Qty(Issued/Assigned)</th>
                        						<th>Issue Qty</th>
                        						<th>Unit Price</th>
                        					    <th>Action</th>
                        					</tr>
                        				</thead>
                        				<tbody></tbody>
                        			</table>
                        		</div>
                    		</div>
                           
                            <span id="gift_selected_details" style="font-weight:bold;color:orange;"></span>
                    <?php if($setting[0]['isOTPReqToGift'] == 1){ ?>        
                        <div id="verify_gift_otp_block">  
                            <input id="isOTPReqToGift" type="hidden" value="<?php echo $setting[0]['isOTPReqToGift']; ?>" />
                            <input id="isVerified" type="hidden" value="0" />
                            <input id="otp_exp" type="hidden" value="<?php echo $setting[0]['giftOTP_exp']?>"/>
                             <div class="row">
                				<div class="modal-body text-center">
                					 <label class="col-md-5 text-right">Mobile Number :</label>  
                					 <input class="col-md-4" id="txt_mobile" type="number"  disabled/>
                					   <input id="id_cus" type="hidden" value="" />
                				</div>
                			 </div>
                			 <div class="row" id="otp_txt_box">
                				<div class="modal-body text-center">
                					 <label class="col-md-5 text-right">Enter OTP :</label>  
                					 <input class="col-md-4" id="otp_data" type="text" value="" minlength="6" maxlength="6" />
                				</div>
                			 </div>
                			 <br/>
                			 <span class="success" id="suc_msg_otp" style="color: green;font-weight: 600;"></span>
                			 <span class="error" id="err_msg_otp"></span>
                			 <br/>
                            <div class="row" id="gift_otp_btns">
            		            <input type="button" id="send_otp_gift" class="btn btn-success btn-confirm" value="Send OTP" />
            			        <input type="button" id ="verify_otp_gift" class="btn btn-danger btn-confirm" value="Verify OTP" disabled/>
            			    </div> 
            			</div>    
            		<?php } ?>	
            		
            		<div class="pull-right">
                        <button type="button" id="save_gift" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-warning" id="cancel_form" >Cancel</button>
                    </div>
                        </div> 
                        <p id="no_gifts_avail" style="display:none;font-size: 25px;color:red;"></p>
                        </div>
                        
                        
            		    <!--Issued list-->
                        <div class="col-md-6">
                        			<input type="hidden" id="total_gift_value" value=0 />
                        			<input type="hidden" id="gift_name" />
                        			<input type="hidden" id="gift_id" />
                        			<input type="hidden" id="gift_amount" />
                        			<div class="col-md-12" style="align:center;">
                        			    
                        										<span class="error" id="err_msg"></span>
                        					<span id="gift_table_issued_title"></span>
                        					<table class="table table-bordered table-striped text-center" id="gift_table_byaccount" style="display:none;width:100%">
                        					<thead>
                        						<tr>
                        						<th>SNO</th>
                        						<th>Gift Name</th>
                        						<th>Gift value/unit</th>
                        						<th>Gift count(Issue/Assgn)</th>
                        						<th>Total Gift Value</th>
                        						<th>Issued Date</th>
                        						<th>Employee</th>
                        						<th>Cancel Gift</th>
                        						</tr>
                        					</thead>
                        					<tbody>
                        						
                        					</tbody>
                        					</table>
                        			</div>
                        		</div>
                    </div>
                </div>
                
                
            </form>    
            </div>
        </div>
    </div>
    </section>
</div>


  <div class="modal fade" id="cancel_issued_gift" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header modalwallet-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel">Cancel Issued Gift</h4>
		</div> 
        <div class="modal-body">
			<form id="gift_cancel_form">
			    <div class="row">
                    <div class="modal-body ">
                    	 <label class="col-md-2 text-left">Remarks :</label>  
                    	 <textarea class="col-md-10 text-center" style="height:80%" name="txt_deduct_remarks" type="number" ></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="modal-body text-center">
                    	 <input type="hidden" id="deduct_id_employee" name="deduct_id_employee" value="<?php echo $this->session->userdata('uid'); ?>" />
                    	 <input type="hidden" id="deduct_id_gift_issued" name="deduct_id_gift_issued" value="" />
                    </div>
                </div> 
			</form>
        </div>
        <div class="modal-footer">
            <button type="button" id="deduct_gift" class="btn btn-success">Update</button>
            <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        </div>  
    </div>
  </div>
</div>