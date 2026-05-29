
 <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
			<h1>
				Account Head
				<small></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Masters</a></li>
				<li class="active">Account Head</li>
			</ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
              <div class="box-header with-border">
					<h3 class="box-title">Account Head</h3> 
                   <?php if($access['add']==1){?>
                    <a class="btn btn-success pull-right" id="" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
                    <?php }?>
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
                  <div class="table-responsive">
                  <table id="accounthed_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
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
<div class="modal fade" id="confirm-delete"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Account Head</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this Account Head ?</strong>
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
<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Account Head</h4>
      </div>
         <div class="modal-body">
            <div id="error-msg"></div>
			<form id="myform">
                <div class="row">
                    <div class="form-group">
                        <label for="" class="col-md-4 col-md-offset-1">Account Name</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Account Name" style=" text-transform: uppercase;">
                        </div>
                    </div>
                </div><p class="help-block"></p>
                    
                <div class="row">
                    <div class="form-group">
                        <label for="" class="col-md-4 col-md-offset-1">Active</label>
                        <div class="col-md-4">
							<input type="checkbox" checked="true" class="status" id="acc_status" name="switch" data-on-text="YES" data-off-text="NO" />
							<input type="hidden" id="account_status" value="1">
                        </div>
                    </div>
                </div>
            </form>
        </div>
		<div class="modal-footer">
			<a href="#" id="add_acc_name" class="btn btn-success" >Save & Close</a>
			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
		</div>
    </div>
  </div>
</div>
<!-- / modal -->
<!-- modal -->      
<div class="modal fade" id="confirm-edit"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Account Head</h4>
      </div>
        <div class="modal-body">
            <div class="row" >
                <div class="col-md-offset-1 col-md-10" id='error_message'></div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="" class="col-sm-4 col-md-offset-1 ">Enter Account Head<span class="error">*</span></label>
					<div class="col-sm-4">
						<input type="hidden" id="edit-id" value="" />
						<input type="text" id="ed_account_name" class="form-control" placeholder="Enter Account Head" style=" text-transform: uppercase;">
					</div>
                </div>
            </div> <p class="help-block"></p>   
            <div class="row">
				<div class="form-group">
					<label for="" class="col-md-4 col-md-offset-1">Active</label>
					<div class="col-md-4">
						<input type="checkbox" checked="true" class="status" id="ed_acc_status" name="switch" data-on-text="YES" data-off-text="NO" />
						<input type="hidden" id="ed_account_status" value="">
					</div>
				</div>
            </div>

        </div>
      <div class="modal-footer">
      	<a href="#" id="update_accsection" class="btn btn-success" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      


