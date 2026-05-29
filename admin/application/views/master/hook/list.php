  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Hook
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Hook List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Hook List</h3><span id="total_hook" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="add_hook" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
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
				<div class="col-md-2" style="margin-top: 20px;">
				<!-- Date and time range -->
				<div class="form-group">
				<div class="input-group">
				<button class="btn btn-default btn_date_range" id="hook_date">
				<!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
				<span  style="display:none;" id="hook1"></span>
				<span  style="display:none;" id="hook2"></span>
				<i class="fa fa-calendar"></i> Date range picker
				<i class="fa fa-caret-down"></i>
				</button>
				</div>
				</div><!-- /.form group -->
				</div>				
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					</div>
				  </div>
						
                  <div class="table-responsive">
                  <table id="hook_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
						<th>Name</th>
						<th>Short Code</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Hook</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this Hook record?</strong>
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
        <h4 class="modal-title" id="myModalLabel">Add Hook </h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div> 
				<div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Hook Name
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="hook_name" name="hook_name" placeholder="Enter Hook name" required="true"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Code
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="hook_code" name="hook_code" placeholder="Enter Hook code"> 
						 <p class="help-block"></p>                      	
                       </div>
                    </div>
				 </div>
					<div class="row">
				<div class="form-group">
				<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="status" id="ad_hook_status" name="switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="add_hook_status" value="1">
						</div>    
						</div>
			</div>
      </div>
      <div class="modal-footer">
		<a href="#" id="new_addhook" class="btn btn-success">Save & New</a>
      	<a href="#" id="add_newhook" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
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
        <h4 class="modal-title" id="myModalLabel">Edit Hook</h4>
      </div>
      <div class="modal-body">
	   <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error'></div>
				</div>
		    <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
			</div>
			 <div class="row">
			 	<div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Hook Name
				   <span class="error">*</span></label>
                   <div class="col-md-4">
                   <input type="hidden" id="edit-id" value="" />
                   	<input type="text" class="form-control" id="ed_hook_name" name="ed_hook_name"  placeholder="Enter Hook Name"> 
					<p class="help-block"></p>	
                   </div>
                </div>
			 </div> 
			 <div class="row">   
                <div class="form-group">
                   <label for="scheme_code" class="col-md-3 col-md-offset-1 ">Short Code
				   <span class="error">*</span></label>
                   <div class="col-md-4">
                   	 <input type="text" class="form-control" id="ed_hook_code" name="ed_hook_code" placeholder="Enter Hook Code"> 
					<p class="help-block"></p>
                   </div>
                </div>
			 </div>  
			<div class="row">
			<div class="form-group">
			<label for="scheme_code" class="col-md-3 col-md-offset-1 ">Active</label>
				  <div class="col-md-4">
				  <input type="checkbox" checked="true" class="status" id="ed_hook_status" name="ed_switch" data-on-text="YES" data-off-text="NO" />
				  <input type="hidden" id="edit_hook_status" value="1">
				  </div>    
			</div>
			</div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_hook" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

