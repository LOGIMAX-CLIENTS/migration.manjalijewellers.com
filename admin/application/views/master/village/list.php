  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Village
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Village</a></li>
            <li class="active">Village List</li>
          </ol>
        </section>


        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Village List</h3>    <span id="total_offers" class="badge bg-green"></span>     
                  <?php if($access['add']==1){?> 
                           <a class="btn btn-success pull-right" id="add_offer" href="<?php echo base_url('index.php/settings/village_form/add');?>" ><i class="fa fa-user-plus"></i> Add</a> 
                           <?php }?>
                          </div><!-- /.box-header -->
                <div class="box-body">
                         <div class="row"> 
                         
                            <div class="col-sm-2">
        						<div class="form-group">
        						     <label class="product_for">Select Branch</label>
        						     <select id="branch_select" class="form-control"></select>
    							</div>
    						</div>
    						
    						<!-- <div class="col-sm-2">
        						<div class="form-group">
        						     <label class="product_for">Filter Zone</label>
        						     <select id="select_zone" class="form-control"></select>
    							</div>
    						</div>
    						
   				        	<div class="col-sm-2" >
        						<div class="form-group">
        						     <label class="product_for">Allocate Zone</label>
        						     <select id="update_zone" class="form-control"></select>
    							</div>
						    </div>
						    <div class="col-sm-2" style="margin-top:15px;">
        						<div class="form-group">
        						        <label class="product_for"></label>
                            <?php if($access['add']==1){?>
										<button type="button" id="zone_update" class="btn btn-success">Allocate Zone</button>   
									  <?php }?>
                  </div>
						    </div> -->
       				   </div> 
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
                  <table id="village_list" class="table table-bordered table-striped text-center">
                        <thead>
                          <tr>
                            <th><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>
                            <th>Village</th>
                            <th>Zone</th>
                            <th>Branch</th>
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

 
       <!-- modal -->      
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete village</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this village record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
