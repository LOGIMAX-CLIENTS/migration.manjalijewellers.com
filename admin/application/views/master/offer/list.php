  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Promotion
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Promotion List</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Offers/Banners/Popup List</h3>    
                           <a class="btn btn-success pull-right" id="add_offer" href="<?php echo base_url('index.php/settings/offers/add');?>" ><i class="fa fa-user-plus"></i> Add</a> 
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
	            
	            
	             <h4 class="box-title">Popup List <span id="total_popup" class="badge bg-green"></span> </h4>
                  <div class="table-responsive">
	                  <table id="popup_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th>ID</th>
	                        <th>Image</th>
	                        <th>Popup</th>
	                        <th>Update</th>
	                      </tr>
		                </thead>    
	                  </table>
                  </div>
                  <br/>
                  <h4 class="box-title">Offers/Banners List <span id="total_offers" class="badge bg-green"></span></h4>    
                  <div class="table-responsive">
	                  <table id="offer_list" class="table table-bordered table-striped text-center">
	                     <thead>
		                      <tr>
		                        <th>ID</th>                        
		                        <th>Type</th>
		                        <th>Image</th>
		                        <th>Title</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Offer</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this offer record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>