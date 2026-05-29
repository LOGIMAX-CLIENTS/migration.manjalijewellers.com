  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Process
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Process</a></li>
            <li class="active">Process List</li>
          </ol>
        </section>


        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Process List</h3><span id="total_offers" class="badge bg-green"></span>  
                  <?php if($access['add']==1){?>    
                           <a class="btn btn-success pull-right" id="add_offer" href="<?php echo base_url('index.php/admin_ret_metal_process/metal_process/add');?>" ><i class="fa fa-user-plus"></i> Add</a> 
                           <?php }?>
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
                          <table id="process_list" class="table table-bordered table-striped text-center">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Process</th>
                                    <th>Status</th>
                                     <th>Action</th>
                                  </tr>
                                </thead>
                          </table>
                      </div>
                    </div><!-- /.box-body -->
                 <div class="overlay" style="display:none;">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

 

