  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Making Type
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Making List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Making Type List</h3>    <span id="total_count" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="add_mak_type" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
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
		                       <button class="btn btn-default btn_date_range" id="make_date">
							  <!-- <input id="rpt_payments"  name="rpt_payment" type="hidden" value="" />-->
							    <span  style="display:none;" id="make1"></span>
							    <span  style="display:none;" id="make2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		                </div>	
							</div>
					
	                 </div>
					
                  <div class="table-responsive">
                  <table id="making_type_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Making Type</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Making Type</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this making type record?</strong>
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
        <h4 class="modal-title" id="myModalLabel">Add Making Type</h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div>
	 
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-4 col-md-offset-2 ">Making Type
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="making_name" name="making_name" placeholder="Enter type name" required="true"> 
                	  <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				 <div class="row">   
                    <div class="form-group">
                       <label for="scheme_code" class="col-md-4 col-md-offset-2 ">Short Code
					   <span class="error">*</span></label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="making_short_code" name="making_short_code" placeholder="Enter Short Code"> 
                	  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div> 
					<div class="row">
				<div class="form-group">
				<label for="scheme_code" class="col-md-4 col-md-offset-2 ">Active</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="status" id="make_switch" name="make_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="making_status" value="1">
						</div>    
						</div>
			</div>
      </div>
      <div class="modal-footer">
		<a href="#" id="addnew_type" class="btn btn-success">Save & New</a>
      	<a href="#" id="add_newtype" class="btn btn-warning" data-dismiss="modal">Save & Close</a>
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
        <h4 class="modal-title" id="myModalLabel">Edit Making Type</h4>
      </div>
      <div class="modal-body">
	   <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error'></div>
				</div>
     		 <div class="row">
			 	<div class="form-group">
                   <label for="scheme_code" class="col-md-4 col-md-offset-2 ">Making Type
				   <span class="error">*</span></label>
                   <div class="col-md-4">
                   <input type="hidden" id="edit-id" value="" />
                   	 <input type="text" class="form-control" id="ed_making_name" name="ed_making_name"  placeholder="Enter type Name"> 
              <p class="help-block"></p>
                   	
                   </div>
                </div>
			 </div>  
			  <div class="row">   
                <div class="form-group">
                   <label for="scheme_code" class="col-md-4 col-md-offset-2 ">Short Code
				   <span class="error">*</span></label>
                   <div class="col-md-4">
                   	 <input type="text" class="form-control" id="ed_making_short_code" name="ed_making_short_code" placeholder="Enter ShortName"> 
            	  <p class="help-block"></p>
                   	
                   </div>
                </div>
			 </div>  
				<div class="row">
				<div class="form-group">
				<label for="scheme_code" class="col-md-4 col-md-offset-2 ">Active</label>
                      <div class="col-md-4">
                      <input type="checkbox" checked="true" class="status" id="ed_make_switch" name="ed_make_switch" data-on-text="YES" data-off-text="NO" />
					   <input type="hidden" id="ed_making_status" value="1">
						</div>    
						</div>
			</div>
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_type" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

