  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Floor 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Branch Floor List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Branch floor List</h3>    <span id="total_floor" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="add_floor" href="#" data-toggle="modal"  ><i class="fa fa-user-plus"></i> Add </a> 
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
				<div class="form-group">
				  <div class="col-md-2">
					<div class="pull-left">
					    <div class="form-group">
						<button class="btn btn-default btn_date_range" id="fl_branch-dt-btn">
						<span  style="display:none;" id="fl_date1"></span>
						<span  style="display:none;" id="fl_date2"></span>
						<i class="fa fa-calendar"></i> Date range picker
						<i class="fa fa-caret-down"></i>
						</button>
						</div>
					</div>						
				  </div>	
				 <?php if($this->session->userdata('branch_settings')==1){?>
					<div class="col-md-2">
						<div class="form-group" >
							<label>Select Branch </label>
							<select id="branch_select" class="form-control branch_filter"></select>
						</div>
					</div>
				 <?php }?>
				</div>
				  <div class="row">
					<div class="col-sm-10 col-sm-offset-1">
					<div id="chit_alert"></div>
					</div>
				  </div>				
                  <div class="table-responsive">
                  <table id="floor_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
						<th>Floor Name</th>
						<th>Short Code</th>
						<th>Branch Name</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Branch Floor</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this branch floor record?</strong>
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
        <h4 class="modal-title" id="myModalLabel">Add Branch Floor</h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
			<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
	  </div>
		<div class="form-group">
		 
		  <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1">Branch 
					   <span class="error">*</span></label>
                       <div class="col-md-4">
					   <select id="floor_branch" class="ret_branch form-control"></select>
					   <input id="id_branch" name="ad_fl_branch_id" type="hidden" value="<?php echo set_value('ad_fl_branch_id','ad_fl_branch_id'); ?>" required/>
                       </div>
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1">Floor Name 
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="fl_name" name="floorname" placeholder="Enter Floor name"required/> 
                	  <p class="help-block"></p>
                       </div>
                    </div>
				 </div>  
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1">Short Code 
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="fl_shortcode" name="floorshortcode" placeholder="Enter short code" required="true" /> 
						<p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
				<div class="row">
					<div class="form-group">
					   <label for="scheme_code" class="col-md-3 col-md-offset-1">Status</label>
                       <div class="col-md-4">
                       <input type="checkbox" class="status" id="fl_status" name="ad_status" data-on-text="YES" data-off-text="NO" value="1" checked="true"/>
                       <input type="hidden" id="branch_fl_status" value="1">
					   </div>    	
                </div>
		  </div>
      </div>
      <div class="modal-footer">
		<a href="#" id="save_newfloor" class="btn btn-success">Save & New</a>
      	<a href="#" id="save_floor" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
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
        <h4 class="modal-title" id="myModalLabel">Edit Branch Floor</h4>
      </div>
      <div class="modal-body">
	    <div class="row" >
			<div class="col-md-offset-1 col-md-10" id='error-msg1'></div>
	    </div>
     		  <div class="row">
				 	<div class="form-group">
						<label for="scheme_code" class="col-md-3 col-md-offset-1">Branch 
						<span class="error">*</span></label>
						<div class="col-md-4">
					    <input type="hidden" id="edit-id" value="" />
						<select id="edbranchid" class="ret_branch form-control"></select>
						<input id="ed_fl_branch_id" name="ed_fl_branch_id" type="hidden" value="<?php echo set_value('ed_fl_branch_id','ed_fl_branch_id'); ?>" required/>
						<p class="help-block"></p>	
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1">Floor Name 
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_floorname" name="floorname" value="<?php echo set_value('"floor[floor_name]',(isset($floor['floor_name'])?$floor['floor_name']:"")); ?>" placeholder="Enter Floor name" required/> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>  
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-3 col-md-offset-1">Short Code 
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_floorshortcode" name="floorshortcode" value="<?php echo set_value('"floor[floor_short_code]',(isset($floor['floor_short_code'])?$floor['floor_short_code']:"")); ?>" placeholder="Enter short code" required="true"> 
						<p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
				<div class="row">
					<div class="form-group">
				 	<label for="scheme_code" class="col-md-3 col-md-offset-1">Status</label>
                    <div class="col-md-4">
                    <input type="checkbox" class="status" id="ed_status" name="ad_status" data-on-text="YES" data-off-text="NO" value="1"/>
                    <input type="hidden" id="ed_fl_status" value="">
					</div>    	
				</div>
		  </div> 
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_floor" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

