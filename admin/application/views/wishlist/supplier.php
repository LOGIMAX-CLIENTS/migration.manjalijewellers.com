<style type="text/css">
	.imgDiv {
		margin-bottom: 10px;
	}
	.imgDiv img {
		width: 100%;
	}
 </style> 
 
  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
		        Supplier Catalogue Wishlist
            <small>Supplier Catalogue Wishlist</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Supplier Catalogue </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Supplier Catalogue </h3>  <span id="total_data" class="badge bg-green"></span>  
                </div>
                 <div class="box-body">  
                   
				   <div class="row">
						<div class="col-xs-12">
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
						</div>
				   </div>

				   <div class="row">
						<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
						<div class="col-md-2"> 
							<div class="form-group">    
								<label>Branch</label> 
								<select type="text" class="form-control" id="branch_select" name="branch_select">
								</select>
							</div> 
						</div>
						<?php }else{?>
						<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
						<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
						<?php }?>
						<div class="col-md-2"> 
							<div class="form-group">    
								<label>Employee</label> 
								<select type="text" class="form-control" id="emp_sel" name="emp_sel">
								</select>
							</div> 
						</div>
						<div class="col-md-2"> 
							<div class="form-group">    
								<label>Status</label> 
								<select type="text" class="form-control" id="wishlist_status" name="wishlist_status">
									<option value="0">All</option>
									<option value="1">Created</option>
									<option value="2">Converted</option>
									<option value="3">Not Converted</option>
								</select>
							</div> 
						</div>
						<div class="col-md-2"> 
							<div class="form-group"> 
							<label>DateRange</label>
							<button class="btn btn-default btn_date_range" id="account-dt-btn">
								<span  style="display:none;" id="suppWishlist_list1"></span>
								<span  style="display:none;" id="suppWishlist_list2"></span>
								<i class="fa fa-calendar"></i> Date range picker
								<i class="fa fa-caret-down"></i>
								</button>  
							</div>
						</div>
						<div class="col-md-2"> 
							<div class="form-group">    
								<label></label> 
								<div class="form-group">
									<button type="button" id="search" class="btn btn-info">Search</button>   
								</div>
							</div> 
						</div>
				    </div>

				  
                 	<div class="table-responsive">
	                 	<table id="supp_wishlist_list" class="table table-bordered table-striped text-center">
							<thead>
							<tr>
								<th>Date</th>
								<th>Branch Name</th>
								<th>Supplier Catalogue Id</th>
								<th>Customer Name</th>
								<th>Mobile</th>
								<th>Product</th>
								<th>Design</th>
								<th>Sub Design</th>
								<th>Employee</th>
								<th>Status</th>
								<th>Remarks on Close</th>
								<th>Close Date</th>
								<th>Action</th>
								<th></th>
							</tr>
							</thead> 
							<tbody>

							</tbody>
	                 	</table>
                 	</div>
                </div><!-- /.box-body -->
                
				<div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper --> 
 
	<!-- modal -->
	<div class="modal fade" id="followup-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php echo form_open('admin_ret_wishlist/followup_submit'); ?>
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel">Update</h4>
		</div>
		<div class="modal-body">
			<div id="followup">
				<div class='row'>							       

					<div class='col-sm-12'>
						<div class='form-group'>
							<label >Choose Type <span class="error"> *</span></label> &nbsp; &nbsp;

							<input type="radio" value="1" id="follow_up" name="followup_type" checked/> <label for="follow_up"> Follow Up </label>  &nbsp;  &nbsp;	
							<input type="radio" value="2" id="convert_order" name="followup_type" /> <label for="convert_order"> Convert Order </label>  &nbsp;  &nbsp;	
							<input type="radio" value="3" id="close_wishlist" name="followup_type" /> <label for="close_wishlist"> Close Wishlist </label>	
						</div>
					</div>

				</div>

				<div class='row'>

					<div class='col-md-12'>

						<div class='col-sm-6'>
							<div class='form-group'>
								<label for="followup_br_name">Branch</label>  &nbsp;  &nbsp;
								<input type='text' class='form-control followup_br_name' id='followup_br_name' autocomplete='off' value='' required readonly />
							</div>
						</div>

						<div class='col-sm-6'>
							<div class='form-group'>
								<label for="followup_emp_name">Employee</label>  &nbsp;  &nbsp;
								<input type='text' class='form-control followup_emp_name' id='followup_emp_name' autocomplete='off' value='' required readonly />
							</div>
						</div>

					</div>

					<div class='col-md-12'>

						<div class='col-sm-6'>
							<div class='form-group'>
								<label for="followup_date"><span id="date_text">Date</span><span class="error"> *</span></label>  &nbsp;  &nbsp;
								<input type='text' class='form-control followup_date' name='followup_date' id='followup_date' autocomplete='off' value='' required readonly />
							</div>
						</div>

					</div>

					<div class='col-sm-12'>
						<div class='form-group'>
							<label for="followup_remarks"><span id="remarks_text">Remarks</span><span class="error"> *</span></label>
							<textarea id="followup_remarks" name="followup_remarks" class="form-control" rows="5" required></textarea>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<input type="hidden" id="id_wishlist_enq" name="id_wishlist_enq" value="" />
			<input type="hidden" id="form_type" name="form_type" value="2" />
			<button type="submit" class="btn btn-success">Update</a>
        	<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
		</div>
		<?php echo form_close();?>
		</div>
	</div>
	</div>
	<!-- / modal --> 