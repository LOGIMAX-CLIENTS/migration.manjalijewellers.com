<style type="text/css">
.ad_rate
{
  position: relative;
  top:45px;
}

</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>Selling Diamond Rate
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Selling Diamond Rate</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Selling Diamond Rate List</h3><span id="total_rate" class="badge bg-green"></span> 
                  <?php if($access['add']==1){?>
                  <a class="btn btn-success pull-right" id="add_selling_rate" href="<?php echo base_url('index.php/admin_ret_catalog/selling_diamond_rate/add');?>"><i class="fa fa-user-plus"></i> Add </a> 
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
					    <div id="chit_alert">
             </div>
           </div>
           <div class="col-xs-10 pull-right ">
            <div class="col-xs-3 ad_rate">
                       <div class="form-group">
                      
                        </div>
                        </div>
                   </div>
				    </div> 
                  <div class="table-responsive">
                  <table id="selling_rate_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                      <th>ID</th>
					            <th>Quality Code</th>
                      <th>Effective Date</th>
                      <th>Status</th>
                      <th>Action</th>
                      </tr>
                 	</thead>
                  </table>
                  </div> 
                  <div class="overlay" style="display:none">
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
        <h4 class="modal-title" id="myModalLabel">Delete Diamond Rate</h4>
      </div>
      <div class="modal-body">
      <strong>Are you sure! You want to delete this Diamond Rate?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
 