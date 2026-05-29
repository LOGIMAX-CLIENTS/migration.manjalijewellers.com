  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Counters
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Counter List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Counter List</h3>    <span id="total_count" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="add_count" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
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
	                 
	                 <div class="col-md-12">
	             
	                 	<div class="col-md-2" style="margin-top: 20px;">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                       <button class="btn btn-default btn_date_range" id="counter_date">
							  <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
							    <span  style="display:none;" id="counter1"></span>
							    <span  style="display:none;" id="counter2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		                </div>
		     
						  <?php if($this->session->userdata('branch_settings')==1){?>
							<div class="col-md-2">
								<div class="form-group" >
									<label>Select Branch </label>
									<select id="branch_select" class="form-control branch_filter"></select>
								</div>
							</div>
							<?php }?>
							<div class="col-md-2">
								<div class="form-group" >
									<label>Select Floor</label>
									<select id="floor_filter" class="form-control" ></select>
								</div>
							</div>
					</div>
					
	         </div>
					
                  <div class="table-responsive">
                  <table id="counter_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Counter Name</th>
                        <th>MAC ID</th>
                        <th>Short Name</th>
						<th>Floor Name</th>
						<th>Branch</th>
						<th>Status</th>
                        <th>Action</th>
                      </tr>
                 	</thead>
                 
                  </table>
                  </div> <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
                </div><!-- /.box-body -->
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
        <h4 class="modal-title" id="myModalLabel">Delete Counter</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this counter record?</strong>
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
<div class="modal fade" id="confirm-add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Branch Counter</h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div>
	   <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Floor
					   <span class="error">*</span></label>
					   <div class="col-md-4">
						<select id="counter_flr_sel" class="form-control"></select>
						<input id="counter_flr_id" name="floor_id" type="hidden" />
                      </div>
                    </div>
				 </div>  
                	  <p class="help-block"></p>
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Counter Name
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="counter_name" name="counter_name" placeholder="Enter Counter name" required="true"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Name
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="counter_short_code" name="counter_short_code" placeholder="Enter Short Name"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
					<div class="row">
				<div class="form-group">
				<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="status" id="ctr_status" name="switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="counter_status" value="1">
						</div>    
						</div>
			</div>
      </div>
      <div class="modal-footer">
		<a href="#" id="add_countnew" class="btn btn-success">Save & New</a>
      	<a href="#" id="add_newcount" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Branch Counter</h4>
      </div>
      <div class="modal-body">
	   <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error'></div>
				</div>
	  <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Select Floor
					   <span class="error">*</span></label>
                       <div class="col-md-4">
						<select id="ed_counter_flr_sel" class="form-control" required></select>
						<input id="ed_counter_flr_id" name="floor_id" type="hidden" value="<?php echo set_value('floor_id','floor_id'); ?>"/>
                	  <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
     		 <div class="row">
			 	<div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Counter Name
				   <span class="error">*</span></label>
                   <div class="col-md-4">
                   <input type="hidden" id="edit-id" value="" />
                   	 <input type="text" class="form-control" id="ed_counter_name" name="ed_counter_name"  placeholder="Enter Counter Name"> 
              <p class="help-block"></p>
                   	
                   </div>
                </div>
			 </div>  
			  <div class="row">   
                <div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Name
				   <span class="error">*</span></label>
                   <div class="col-md-4">
                   	 <input type="text" class="form-control" id="ed_counter_short_code" name="ed_counter_short_code" placeholder="Enter ShortName"> 
            	  <p class="help-block"></p>
                   	
                   </div>
                </div>
			 </div>  
				<div class="row">
				<div class="form-group">
				<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="status" id="ed_ctr_status" name="ed_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="ed_counter_status" value="">
						</div>    
						</div>
			</div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_counter" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

