<!-- Content Wrapper. Contains page content -->  
<div class="content-wrapper">
   <!-- Content Header (Page header) -->        
   <section class="content-header">
      <h1>            Agent Settlement            <small></small>          </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
         <li><a href="#">Loyalty</a></li>
         <li class="active">Agent Settlement List</li>
      </ol>
   </section>
   <!-- Main content -->        
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box box-primary">
               
               <!-- /.box-header -->                
               <div class="box-body">
                  <!-- Alert -->                
                  <?php                	
                  if($this->session->flashdata('chit_alert'))                	 {                		
                  $message = $this->session->flashdata('chit_alert');                	?>                       
                  <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
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
                     <div class="col-md-11">
                        <div class="box box-default">
                           <div class="box-body">
                              <div class="row">
                                 <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>								
                                 <div class="col-md-2">
                                    <div class="form-group tagged">										
	                                    <label>Select Branch</label>										
	                                    <select id="branch_select" class="form-control branch_filter"></select>																</div>
                                 </div>
                                 <?php }else{?>									
                                 <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 								<?php }?>																
                                 <div class="col-md-2">
									    <br/>
										<div class="form-group">
										   <button class="btn btn-default btn_date_range" id="payment-dt-btn"> 
											<span  style="display:none;" id="agent_date1"></span>
											<span  style="display:none;" id="agent_date2"></span>
											<i class="fa fa-calendar"></i> Date range picker
											<i class="fa fa-caret-down"></i>
											</button>
										</div>					
									</div> 	
                                 <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>								
                                 <div class="col-md-2">
                                    <div class="form-group tagged">										
	                                    <label>Settlement Branch</label>										
	                                    <select id="settlement_branch" class="form-control branch_filter"></select>															</div>
                                 </div>
                                 <?php }else{?>									
                                 	<input type="hidden" id="settlement_branch" class="settlement_branch" value="<?php echo $this->session->userdata('id_branch') ?>"> 								
                                 <?php }?>								
                                 <div class="col-md-2">
                                    <label>Settlement Min Amt:</label>									
                                    <div class="form-group">    									   
                                    	<span id="settle_amount_limitaion"style="font-weight:bold">0.00</span>	
                                    	</div>
                                 </div>
                                 <div class="col-md-2">
                                    <label>Settlement Max Amt:</label>									
                                    <div class="form-group">    									   
                                    	<span id="settle_max_amount_limitaion"style="font-weight:bold">0.00</span>	
                                    	</div>
                                 </div>
                                 
                                 <div class="col-md-2">
										<div class="form-group">
										   <label>Select Agent</label>
											<select id="agent_select" class="form-control"></select>
											<input id="id_agent" name="id_agent" type="hidden" value=""/>
										</div>
									</div>
                                <div class="col-md-2">
                                    <label></label>									
                                    <div class="form-group">										
                                    <button type="button" id="influ_settlement" class="btn bg-aqua" onclick="get_settlement_records();">Bulk Settle</button>   									
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <br/>				
                  <div class="row">
                     <div class="col-md-12">
                        <div class="table-responsive">
                           <table id="agent_settlement" class="table table-bordered table-striped text-center table_list">
                              <thead>
                                 <tr>
                                    <th>S.No</th>
                                    <th>Agent Name</th>
                                    <th>Mobile</th>
                                    <th>Branch</th>
                                    <th>No.of Referal</th>
                                    <th>Settlement Pending</th>
                                    <th>Amt to Settle</th>
                                     <th>Detail</th>
                                    <th>Settlement</th>
                                   
                                 </tr>
                              </thead>
                           </table>
                        </div>
                        <div class="overlay" style="display:none">					    
                        <i class="fa fa-refresh fa-spin"></i>					  
                        </div>
                     </div>
                  </div>
               </div>
               <!-- /.box-body -->              
            </div>
            <!-- /.box -->            
         </div>
         <!-- /.col -->          
      </div>
      <!-- /.row -->        
   </section>
   <!-- /.content -->      
</div>
<!-- /.content-wrapper -->

<!-- modal -->      
<div class="modal fade" id="req-update-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header" style="background: orange;">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>            
            <h4 class="modal-title" id="myModalLabel">Settlement Amount</h4>
         </div>
         <div class="modal-body">
             <div class="row">
                 <div id="error"></div>
             </div>
             <input type="hidden" id="min_amt" value="">            
	        <input type="hidden" id="max_amt" value=""> 
             <p class="help-block"></p>
            <div class="row">
           
            <span class="profile-list"></span>
              
                 <p class="help-block"></p>
                 </div><p class="help-block"></p>
            <div class="row">
               
               <label for="amount" class="col-md-4 reject_reason">Amount to Settle </label>                
               <div class="col-md-5 reject_reason">
                  <input type="number" id="settle_pts" class="form-control" placeholder="Enter Amount to Settle" value="">
                  <p class="help-block"></p>
               </div>
            </div>
            <div class="row">
               
               <label for="amount" class="col-md-4 reject_reason">Select Mode </label>                
               <div class="col-md-5 reject_reason">
                  <select  id="pref_mode" class="form-control" placeholder="Select Mode" value="">
                      <option value="">--Select Mode--</option>
                      <option value="1">Cash</option>
                      <option value="2">Online</option>
                  </select>
                  <p class="help-block"></p>
               </div>
            </div>
            <div class="row">
               
               <label for="amount" class="col-md-4 reject_reason">UTR Number </label>                
               <div class="col-md-5 reject_reason">
                  <input type="number" id="utr_no" class="form-control" placeholder="Enter UTR" value="" disabled="disabled">
                  <p class="help-block"></p>
               </div>
            </div>
            
            <div class="row">
               
               <label for="amount" class="col-md-4 reject_reason" style="color:red;">Note: Amount Limit </label>                
               
                  <b><div class="col-md-2" id="min_settle" style="color:red;"></div></b>
                
               
            </div>
         </div>
         <div class="modal-footer">	        
	                    
	         <input type="hidden" value="" id="cus_id" />   
	         <input type="hidden" value="" id="preferred_mode" /> 
	         <input type="hidden" value="" id="bank_name" /> 
	         <input type="hidden" value="" id="ifsc_code" /> 
	         <input type="hidden" value="" id="bank_account_number" /> 
	         <button onclick="updateSettlement()" class="btn btn-primary btn-confirm update_item">Update</button>            
	         <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>        
         </div>
      </div>
   </div>
</div>
<!-- / modal -->