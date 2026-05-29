  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Version Details
          <span id="version_count" class="badge bg-green"></span>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Version List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <!-- <h3 class="box-title">Version List</h3>       -->
                  <div class="row">
                    <div class="col-md-2">
                          
                        <div class="form-group">                     
                            <button class="btn btn-default btn_date_range" id="version-dt-btn">
                            <span  style="display:none;" id="version_list1"></span>
                            <span  style="display:none;" id="version_list2"></span>
                            <i class="fa fa-calendar"></i> Date range picker
                            <i class="fa fa-caret-down"></i>
                            </button>  
                        </div>
                        </div> 
                        <div class="col-md-10">
                            <a class="btn btn-small btn-success pull-right" id="add_version" href="<?php echo base_url('index.php/settings/version/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 
                        </div>    
                    </div>   
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
                  <table id="version_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Version No</th>
                        <th>Description</th>  
                        <!-- <th>Client</th>   -->
                        <th>Date</th>                                        
                        <th>Action</th>
                      </tr>
                    </thead>

                  </table>
                  </div>
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
        <h4 class="modal-title" id="myModalLabel">Version</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this version?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->     
