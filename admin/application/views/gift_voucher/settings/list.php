  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Gift Voucher
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Settings</a></li>
            <li class="active">Purchase Voucher Settings</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box box-primary">
              <div class="box-header with-border">
                  <h3 class="box-title">Purchase  Voucher Settings</h3>    <span id="total_gift" class="badge bg-green"></span>      
                           <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_gift_vocuher/gift_voucher_settings/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
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
                  <table id="gift_voucher_settings" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Branch</th>
                        <th>Name</th>
                        <th>Bill Amount</th>
                        <th>Credit Amount</th>
                        <th>Active/Inactive</th>
                        <th>Default</th>
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
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Delete Vocuher </h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this Voucher?</strong>
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
        <h4 class="modal-title" id="myModalLabel">Add Gift</h4>
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div>
                <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1" >Name</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="name" name="name" placeholder="Enter Gift Voucher Name" required="true" style="text-transform:uppercase;"> 
                	        <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 <?php if($this->session->userdata('branch_settings')==1){?>
				    <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1" >Select Branch</label>
                       <div class="col-md-4">
                       	 <select id="gift_branch" class="form-control" multiple style="width:100%;"></select>
                       	 <input type="hidden" id="id_branch">
                       </div>
                    </div>
				 </div>
				<?php }?>
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">From Date</label>
                       <div class="col-md-4">
                       	    <input class="form-control" id="valid_from" data-date-format="dd-mm-yyyy" name="valid_from" value="" type="text" placeholder="Valid From" />
                	        <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">To Date</label>
                       <div class="col-md-4">
                       	    <input class="form-control" id="valid_to" data-date-format="dd-mm-yyyy" name="valid_to" value="" type="text" placeholder="Valid To" />
                	        <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Amount</label>
                       <div class="col-md-4">
                       	 <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Min Amount" required="true"> 
                	        <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
      </div>
      <div class="modal-footer">
      	<a href="#" id="add_new_gift" class="btn btn-success" data-dismiss="modal" >Add</a>
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
        <h4 class="modal-title" id="myModalLabel">Edit Gift Voucher</h4>
        <input type="hidden" id="edit-id"> 
      </div>
      <div class="modal-body">
	  <div class="row" >
					<div class="col-md-offset-1 col-md-10" id='error-msg'></div>
				</div>
                <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1" >Name</label>
                       <div class="col-md-4">
                       	 <input type="text" class="form-control" id="ed_name" name="ed_name" placeholder="Enter Gift Voucher Name" required="true" style="text-transform:uppercase;"> 
                	        <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				 
				 <?php if($this->session->userdata('branch_settings')==1){?>
				    <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1" >Select Branch</label>
                       <div class="col-md-4">
                       	 <select id="ed_gift_branch" class="form-control" multiple style="width:100%;"></select>
                       	 <input type="hidden" id="ed_id_branch">
                       	 <div id="sel_br"></div> 
                       </div>
                    </div>
				 </div>
				<?php }?>
				
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">From Date</label>
                       <div class="col-md-4">
                       	    <input class="form-control" id="ed_valid_from" data-date-format="dd-mm-yyyy" name="ed_valid_from" value="" type="text" placeholder="Valid From" />
                	        <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">To Date</label>
                       <div class="col-md-4">
                       	    <input class="form-control" id="ed_valid_to" data-date-format="dd-mm-yyyy" name="ed_valid_to" value="" type="text" placeholder="Valid To" />
                	        <p class="help-block"></p>
                       </div>
                    </div>
				 </div>
				 
				 <div class="row">
				 	<div class="form-group">
                       <label for="scheme_code" class="col-md-2 col-md-offset-1 ">Amount</label>
                       <div class="col-md-4">
                       	 <input type="number" class="form-control" id="ed_amount" name="ed_amount" placeholder="Enter Min Amount" required="true"> 
                	        <p class="help-block"></p>
                       </div>
                    </div>
				 </div> 
      </div>
      <div class="modal-footer">
      	<a href="#" id="update_gift_voucher" class="btn btn-success" data-dismiss="modal" >Update</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

