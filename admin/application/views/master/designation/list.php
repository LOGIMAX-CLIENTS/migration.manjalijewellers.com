  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Designation
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Designation List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Designation List</h3>       <span id="total_designations" class="badge bg-green"></span> 
                           <a class="btn btn-success pull-right" id="add_designation" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                </div><!-- /.box-header -->
                <div class="box-body">
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
                  <div class="table-responsive">
                  <table id="design_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Designation</th>
                        
                        <th>Action</th>
                      </tr>
                      
                 </thead>

                  </table>
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
        <h4 class="modal-title" id="myModalLabel">Delete Designation</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this designation record?</strong>
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
        <h4 class="modal-title" id="myModalLabel">Add Designation</h4>
      </div>
      <div class="modal-body">
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Designation</label>
                       <div class="col-md-4">
                       	 <!--<input type="text" class="form-control" id="designation" name="designation" value="<?php echo set_value('designation',(isset($design)?$design:"")); ?>" placeholder="Enter Designation"> -->						 <input type="text" class="form-control" id="designation" title="Only Alphabets and Space Allowed" name="designation" value="<?php echo set_value('designation',(isset($design)?$design:"")); ?>" placeholder="Enter Designation">  
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>      
      </div>
      <div class="modal-footer">
      	<a href="#" id="add_design" class="btn btn-success" data-dismiss="modal" >Add</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
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
        <h4 class="modal-title" id="myModalLabel">Edit Designation</h4>
      </div>
      <div class="modal-body">
          <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Designation</label>
                       <div class="col-md-4">
                       <input type="hidden" id="edit-id" value="" />
                       	 <!--<input type="text" class="form-control" id="ed_design" name="designation" value="<?php echo set_value('designation',(isset($design)?$design:"")); ?>" placeholder="Enter Designation">-->							<input type="text" title="Only Alphabets and Space Allowed" class="form-control" id="ed_design" name="designation" value="<?php echo set_value('designation',(isset($design)?$design:"")); ?>" placeholder="Enter Designation">
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>      
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_design" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

