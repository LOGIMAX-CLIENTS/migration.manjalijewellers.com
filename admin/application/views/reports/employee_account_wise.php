  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Account Employee Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Account Employee Report</li>
          </ol>
        </section>
        <!-- Main content -->
       <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                	
	              <!--  <?php 
	               /* Jilaba Sync */
	               if((($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) && $this->config->item('integrationType') == 1 ){  ?>
	                 <div class="col-md-12">                  
                       <div class="form-group">
                        <a class="btn  btn-primary pull-right" href="<?php echo base_url('index.php/admin_manage/update_client_jil'); ?>"><i class="fa fa-retweet"></i> Sync Account</a> 
                       </div>
                      </div><br/>
	                <hr />
	               <?php 
	               }
	               /* Tool Sync */
	               else if((($this->config->item('showToAdminsOnly') == 1 && $this->session->userdata('profile') <=2) || $this->config->item('showToAdminsOnly') == 0) && $this->config->item('integrationType') == 2 ){  
						echo form_open('admin_manage/update_client',array('id'=>'sync_acc')); ?>
						<hr />
						
						<div class="row"> 
						  <div class="col-md-3">
						  	<h4>Sync A/c and Payment</h4>
						  </div>
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
						</div> 
						<hr />
					<?php echo form_close();}?> 
						
                  <h3 class="box-title">Scheme Account List</h3> <span id="total_accounts" class="badge bg-aqua"></span> -->     
        		</div><!-- /.box-header -->
        
        
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
        
                <div class=""> 
	            	<div class="row">
		              <div class="col-md-12">
		                <div class="col-md-4">
		                  <div class="pull-left">
		                   <div class="form-group">                     
		                    <button class="btn btn-default btn_date_range pull-right" id="account-dt-btn">
		                      <span  style="display:none;" id="account_list1"></span>
		                      <span  style="display:none;" id="account_list2"></span>
		                      <i class="fa fa-calendar"></i> Date range picker
		                      <i class="fa fa-caret-down"></i>
		                     </button>  
		                   </div>
		                  </div>            
		                </div>
		                <div class="col-md-8">
		                <!--    <div class="col-md-2" >
			                  <?php if($this->account_model->get_accnosettings()==1){?>
			                       <div class="form-group">
			                    <button type="button"  id="conform_save" class="btn btn-primary  pull-right conform_sch"><i class="fa fa-user-plus"></i>Add Accno</button>
			                    </div>                      
			                  <?php }?>
		                    </div>  -->
		                  <?php if($this->session->userdata('branch_settings')==1){?>
		                    <div class="col-md-4">
		                       <div class="form-group" style="    margin-left: 50px;">
			                      
			                      <select id="branch_select" class="form-control" style="width:150px;"></select>
			                      <input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
			                    </div> 
		                    </div>
		                  <?php }?>
						  
						  					  
						  <div class="col-md-4">
		                       <div class="form-group" style="    margin-left: 25px;">
			                     
                           <select id="emp_select" class="form-control"required="true"></select>
                        <input id="id_employee" name="id_employee" type="hidden" value="" required="true"/>
                        
			                      
			                    </div> 
		                    </div>
		                  
		                <!--  <div class="col">
		                  	<a class="btn  pull-right bg-green" id="add" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 
		                  </div> -->
		                </div>
		            </div>    
		          </div>  
      <?php if($this->account_model->get_accnosettings()==1){?> 
                <div class="table-responsive">
                  <table id="emp_acc_list" class="table table-bordered table-striped dataTable text-center grid" >
                    <thead>
                      <tr>
                      <!--  <th><label class="checkbox-inline"><input type="checkbox" id="select_aldata"  name="select_all" value="all"/>All</label></th>-->
                      
                        <th>Sch ID</th>
                     <!--   <th>Account.No</th>
                        <th>Client ID</th>-->
                        
                        <th>Cus Id</th> 
            
                        <th>Customer</th>
            
                        <th>Mobile</th> 
                        <th>A/c Name</th> 
            
						<th>Scheme code</th>
            
						<th> A/c No</th>   
						<th>Type</th>           
                        <th>Start Date</th> 
						<th>Scheme Type</th> 
                        <th>Installment Payable</th>
                        
                        <th>PAN No.</th>
                        
                        <th>Paid Ins</th>
                       
                        <th>Status</th>
						<th>Referred By</th>
                        <th>Type</th>
                        
                      </tr>
                    </thead>
               <tfoot>
							<tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
						</tfoot>
                  </table>
                  </div>
      <?php }else{?>  
      
          <div class="table-responsive">
                  <table id="emp_acc_list" class="table table-bordered table-striped dataTable text-center grid" >
                    <thead>
                      <tr>
                      
                        <th>Sch ID</th>
                        <th>Cus Id</th> 
            
                        <th>Customer Name</th>
            
                        <th>Mobile</th> 
                        <th>A/c Name</th> 
                      
						<th>Scheme Code</th>
                        <th> A/c No</th>   
						<th>Type</th>           
                        <th>Start Date</th> 
						<th>Scheme Type</th> 
                        <th>Installment Payable</th>
                        
                        <th>PAN No.</th>
                        
                        <th>Paid Ins</th>
                        
                        <th>Status</th>
						
						<th>Referred By</th>
						
                        <th>Type</th>
                        
                      </tr>
                    </thead>
					<tfoot>
							<tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
						</tfoot> 
               
                  </table>
                  </div>
          
      <?php }?>       
                </div>
				  <label>Note:&nbsp;Last 7 days Employee Scheme Account List</label>
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