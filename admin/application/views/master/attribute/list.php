  <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Attribute
      <small></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Attribute</a></li>
      <li class="active">Attribute List</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Attribute List</h3><span id="total_count" class="badge bg-green"></span>       
              <a class="btn btn-success pull-right" id="attribute_add" href="<?php echo base_url('index.php/admin_ret_catalog/attribute/add');?>"></i>Add</a> 
            </div><!-- /.box-header -->
          <div class="box-body">
            <!-- Alert -->
            <?php 
                if($this->session->flashdata('chit_alert')) {
                $message = $this->session->flashdata('chit_alert');
                ?>
                <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
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
                        <button class="btn btn-default" id="attribute_date">
                          <span  style="display:none;" id="user1"></span>
                          <span  style="display:none;" id="user2"></span>
                          <i class="fa fa-calendar"></i> Date range picker
                          <i class="fa fa-caret-down"></i>
                        </button>
                      </div>
                    </div><!-- /.form group -->
                </div>	
              </div>
            </div>			  
            <div class="table-responsive">
              <table id="attribute_list" class="table table-bordered table-striped text-center">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Attribute Name</th>
                    <th>Status</th>
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
<div class="modal fade" id="confirm-delete"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Attribute</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this attribute?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->