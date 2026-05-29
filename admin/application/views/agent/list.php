  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Agent 
            <small>Manage your agent profiles</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Agent</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Agent List</h3>  <span id="total_customers" class="badge bg-green"></span>  
                  <div class="pull-right">
                  	 <a class="btn btn-success pull-right" id="add_agent" href="<?php echo base_url('index.php/agent/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
                  	  
				  </div>
                </div>
                 <div class="box-body">   
   				   <div class="row"> 
   				        <?php if($entry_date[0]['edit_custom_entry_date']==1){?>	
						<div class="col-sm-2">
							<div class="form-group">
							   <label>Filter Date By</label>
								<select id="date_Select" class="form-control">
								    <option value=1 selected>Reg. Date</option>
								     <option value=2>Entry Date</option>
								</select>
								<input id="id_type" name="scheme[id_type]" type="hidden" value=""/>
							</div>
					    </div>
					    <?php }?> 
					    <div class="col-sm-2">
					        <br/>
    						<div class="form-group">
    						   <button class="btn btn-default btn_date_range" id="customer-dt-btn">
    							<i class="fa fa-calendar"></i> Date range picker
    							<i class="fa fa-caret-down"></i>
    							</button>
							</div>
						</div> 	
						<div class="col-sm-2">											  
							<div class="form-group">								
								<label>Filter By Village</label>
								<select id="village_select" class="form-control"></select>
								<input type="hidden" id="id_village">
							</div>
						</div>
						<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('is_branchwise_cus_reg')==1  ){?> 
    						<div class="col-sm-2">											  
    							<div class="form-group">	
    							    <label>Filter By Branch</label>
    								<select id="branch_select" class="form-control"></select>
    								<input id="id_branch" name="customer[id_branch]" type="hidden" value="<?php echo set_value('customer[id_branch]',$customer['id_branch']);?>"  />
    							</div>
    						</div>													  
    					<?php }?> 
   				        <div class="col-sm-4">
   				            <br/>
   				            <button type="submit" class="btn btn-warning" id="send-login-sms"><i class="fa fa-send-o"></i> Send Login SMS</button> &nbsp;&nbsp;
		                    <button type="submit" class="btn btn-primary" id="send-login-email"><i class="fa fa-envelope-o"></i> Send Login Email</button>      
		                </div>
   				   </div>   
			 <br/>
			   <div class="row">
					<div class="col-xs-12">
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
					</div>
			   </div>
			
				
                  <div class="table-responsive">
	                 <table id="agent_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>
	                        <th>Name</th>
	                        <th>Mobile</th>                                          
							<th>Referal Code</th>
	                        <?php if($entry_date[0]['edit_custom_entry_date']==1){?>
	                        <th>Entry Date <option>Date Add</option></th>                                      
	                        <?php }else{?>
	                         <th>Member Since<option>Date Add</option></th>  
	                        <?php }?>
							
	                        <th>Status</th>                                           
                                          
	                        <th>Created Through</th>                                           
	                        <th>Action</th>
	                      </tr>
	                    </thead> 

	                 </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
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
        <h4 class="modal-title" id="myModalLabel">Delete Customer</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this customer?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      






