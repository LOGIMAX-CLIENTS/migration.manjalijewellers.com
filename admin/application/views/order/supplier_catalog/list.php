  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
          Supplier Catalogue
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Orders</a></li>
            <li class="active">Supplier Catalogue</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="col-xs-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Supplier Catalogue List  </h3>  <span id="total_count" class="badge bg-green"></span>  
                <div class="pull-right">
                    <a class="btn btn-success pull-right" id="add_supp_cat" href="<?php echo base_url('index.php/admin_ret_supp_catalog/supplier_catalog/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
                </div>
              </div>
              <div class="box-body">   
                <br/>
                <div class="row">
                  <div class="col-xs-12">
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
                  </div>
              </div>
                <div class="table-responsive">
                  <table id="supp_cat_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Design</th>
                        <th>Sub Design</th>
                        <th>Design Code</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div><!-- /.box-body -->
              <div class="overlay" style="display:none">
				        <i class="fa fa-refresh fa-spin"></i>
				      </div>
          </div><!-- /.col -->
      </div><!-- /.row -->
    </section><!-- /.content -->

<!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Supplier Catalogue</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" id="design_no">
        <strong>Are you sure! You want to delete this supplier catalogue?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" id="remove_design">Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
  
