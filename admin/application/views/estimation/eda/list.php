  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Estimate Discount Approval
            <small>Estimate Discount Approval</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Estimation</a></li>
            <li class="active">Estimate Discount Approval</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">EDA List</h3>  <span id="total_eda" class="badge bg-green"></span>  
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
				   	<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
				    <div class="row">
				        <div class="col-md-offset-2 col-md-8">  
    				        <div class="col-md-3"> 
        	                     <div class="form-group tagged">
        	                       <label>Select Branch</label>
        								<select id="branch_select" class="form-control ret_branch"></select>
        	                     </div>
            		          </div>
        		          </div>
				    </div>
				   <?php }else{?>
				   	<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
				   <?php }?>
				   
			  
                  	<div class="table-responsive">
                      <input type="hidden" id="id_branch"  value="<?php echo $this->session->userdata('id_branch') ?>">
	                 	<table id="eda_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th>Est No.</th>
	                        <th>Date</th>
	                        <th>Customer</th>
	                        <th>Mobile</th>
							<th>Product Name</th>
							<th>Total Amount</th>
							<th>Final Amount</th>
							<th>Discount</th>
	                        <th style="width: 15%;">Action</th>
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
      </div><!-- /.content-wrapper -->

<!-- modal -->
<div class="modal fade" id="confirm-approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Approve Estimation Discount</h4>
      </div>
      <div class="modal-body">
            <strong>Are you sure! You want to approve this Estimation Discount?</strong>
			<input type="hidden" id="esti_id" />
			<input type="hidden" id="estimate_final_amt" />
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-success btn-approve" >Approve</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      

<div class="modal fade" id="confirm-reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Reject Estimation Discount</h4>
      </div>
      <div class="modal-body">
            <strong>Are you sure! You want to reject this Estimation Discount?</strong>
			<input type="hidden" id="esti_reject_id" />
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-reject" >Reject</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
