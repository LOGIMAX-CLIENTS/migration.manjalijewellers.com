
 <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Old Metal Rates
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Masters</a></li>
            <li class="active">Old Metal Rates Listt</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
              <div class="box-header with-border">
                  <h3 class="box-title">Old Metal Rate</h3> 
                  <?php if($access['add'] ==1){ ?>
                           <a class="btn btn-success pull-right" href="#" data-toggle="modal" data-target="#confirm-add" ><i class="fa fa-user-plus"></i> Add</a> 
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
                  <table id="old_metal_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Metal</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Created By</th>
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
        <h4 class="modal-title" id="myModalLabel">Delete Rate</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this  record?</strong>
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
        <h4 class="modal-title" id="myModalLabel">Add Rate</h4>
      </div>
         <div class="modal-body">
             <div id="chit_alert"></div>
                  <form id="old_metal_rate">
                         <div class="row">
                             <div class="form-group">
                                       <label for="" class="col-md-4 col-md-offset-1">Gold</label>
                                       <div class="col-md-4">
                                          <input type="number" class="form-control" id="gold_rate" name="gold_rate" placeholder="Gold 24CT Rate">
                                       </div>
                                    </div>
                         </div><p class="help-block"></p>
        
                         <div class="row">
                             <div class="form-group">
                                       <label for="" class="col-md-4 col-md-offset-1">Silver</label>
                                       <div class="col-md-4">
                                          <input type="text" class="form-control" id="silver_rate" name="silver_rate" placeholder="Old Silver Rate">
                                       </div>
                                    </div>
                         </div><p class="help-block"></p>
                        
                         <div class="row">
                             <div class="form-group">
                                       <label for="" class="col-md-4 col-md-offset-1">Platinum</label>
                                       <div class="col-md-4">
                                          <input type="text" class="form-control" id="platinum_rate" name="platinum_rate" placeholder="Old Platinum Rate">
                                       </div>
                                    </div>
                         </div><p class="help-block"></p>
                    </form>
        </div>
      <div class="modal-footer">
      	<a href="#" id="add_old_rate" class="btn btn-success" data-dismiss="modal" >Add</a>
        <button type="button" id="old_metal_close" class="btn btn-warning" data-dismiss="modal">Close</button>
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
        <h4 class="modal-title" id="myModalLabel">Edit Old Rate</h4>
      </div>
       <div class="modal-body">
             <div id="chit_alert"></div>
                  <form id="old_metal_update">
                        <input type="hidden" id="id_old_metal_rate"  name="id_old_metal_rate">
                        <input type="hidden" id="id_metal"  name="id_metal">
                         <div class="row" id="gold_row" style="display:none;">
                             <div class="form-group">
                                       <label for="" class="col-md-4 col-md-offset-1">Gold</label>
                                       <div class="col-md-4">
                                          <input type="number" class="form-control" id="ed_gold_rate" name="ed_gold_rate" placeholder="Gold 24CT Rate">
                                       </div>
                                    </div>
                         </div><p class="help-block"></p>
        
                         <div class="row" id="silver_row" style="display:none;">
                             <div class="form-group">
                                       <label for="" class="col-md-4 col-md-offset-1">Silver</label>
                                       <div class="col-md-4">
                                          <input type="text" class="form-control" id="ed_silver_rate" name="ed_silver_rate" placeholder="Old Silver Rate">
                                       </div>
                                    </div>
                         </div><p class="help-block"></p>
                         
                         <div class="row" id="platinum_row" style="display:none;">
                             <div class="form-group">
                                       <label for="" class="col-md-4 col-md-offset-1">Platinum</label>
                                       <div class="col-md-4">
                                          <input type="text" class="form-control" id="ed_platinum_rate" name="ed_platinum_rate" placeholder="Old Platinum Rate">
                                       </div>
                                    </div>
                         </div><p class="help-block"></p>
                    </form>
        </div>
      <div class="modal-footer">
      	<a href="#" id="update_old_metal" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      


