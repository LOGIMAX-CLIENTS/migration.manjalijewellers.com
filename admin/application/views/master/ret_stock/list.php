<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Stock Issue Type
				<small></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
				<li><a href="#">Masters</a></li>
				<li class="active">Stock Issue Type</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h3 class="box-title">Stock Issue Type</h3> 
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
								<table id="stock_list" class="table table-bordered table-striped text-center">
									<thead>
										<tr>
											<th>ID</th>
											<th>Stock Name</th>
											<th>Stock Type</th>
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
      

 
<!-- Add Modal Start -->      
	<div class="modal fade" id="confirm-add"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Add Stock Issue Type</h4>
				</div>
				<div class="modal-body">
				 <div id="error-msg"></div>
					  <form id="myform">
						<div class="row">
							<div class="form-group">
								<label for="" class="col-md-4 col-md-offset-1">Stock Name</label>
								<div class="col-md-4">
									<input type="text" class="form-control" id="stock_name" name="stock_name" placeholder="Enter Stock Name" style=" text-transform: uppercase;">
								</div>
							</div>
						</div><p class="help-block"></p>
						<div class="row"  style="margin-bottom: 15px;">
							<div class="form-group">
								<label for="" class="col-md-4 col-md-offset-1">Stock Type</label>
								<div class="col-md-6">
									<input type="radio" value="1" name="stock_type" id="stock_remove"  /> <label for="stock_remove">Remove From Stock</label>
									<input type="radio" value="0" name="stock_type" id="stock_no" checked/> <label for="stock_no">No</label>
								</div>
							</div>
						</div>
								
						<div class="row">
							<div class="form-group">
								<label for="" class="col-md-4 col-md-offset-1">Active</label>
								<div class="col-md-4">
									<input type="checkbox" checked="true" class="status" id="stock_sts" name="switch" data-on-text="YES" data-off-text="NO" />
									<input type="hidden" id="stock_status" value="1">
								</div>
							</div>
						</div>
					 </form>
				</div>
			  <div class="modal-footer">
					<a href="#" id="add_stak" class="btn btn-success" >Save & Close</a>
					<!--<a href="#" id="add_new_devn" class="btn btn-success" >Save & New</a>-->
					<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			  </div>
			</div>
		</div>
	</div>
<!-- / End Add modal -->

<!-- Edit Modal Start -->      
	<div class="modal fade" id="confirm-edit"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Edit Stock Issue Type</h4>
				</div>
				<div class="modal-body">
					<div class="row" >
						<div class="col-md-offset-1 col-md-10" id='error_message'></div>
					</div>
					<div class="row">
						<div class="form-group">
							<label for="" class="col-sm-4 col-md-offset-1 ">Enter Stock Issue Type<span class="error">*</span></label>
							<div class="col-sm-4">
								<input type="hidden" id="edit-id" value="" />
								<input type="text" id="ed_stock_name" class="form-control" placeholder="Enter Stock Name" style=" text-transform: uppercase;">
							</div>
						</div>
					</div> <p class="help-block"></p> 
					<div class="row" style="margin-bottom: 15px;">
						<div class="form-group">
								<label for="" class="col-md-4 col-md-offset-1">Stock Type</label>
							<div class="col-md-6">
								<input type="radio" value="1" name="ed_stock_type" id="ed_stock_remove"  onclick="return false;" /> <label for="ed_stock_remove">Remove From Stock</label>
								<input type="radio" value="0" name="ed_stock_type" id="ed_stock_no" checked  onclick="return false;"/> <label for="ed_stock_no">No</label>
							</div>
						</div>
					</div>
							
					<div class="row">
						<div class="form-group">
							<label for="" class="col-md-4 col-md-offset-1">Active</label>
							<div class="col-md-4">
								<input type="checkbox" checked="true" class="status" id="ed_stock_sts" name="switch" data-on-text="YES" data-off-text="NO" />
								<input type="hidden" id="ed_stock_status" value="">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" id="update_stock" class="btn btn-success" >Update</a>
					<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
<!-- / Edit Modal End-->

<!-- Delete modal -->      
	<div class="modal fade" id="confirm-delete"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title" id="myModalLabel">Delete Stock Issue Type</h4>
				</div>
			  <div class="modal-body">
					<strong>Are you sure! You want to delete this Stock Issue Type ?</strong>
			  </div>
			  <div class="modal-footer">
					<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
					 <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
			  </div>
			</div>
		</div>
	</div>
<!-- / Delete Modal End -->       


