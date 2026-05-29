  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Issue
            <small>Manage Issue(s)</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Issue & Receipt</a></li>
            <li class="active">Receipt</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">

               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Issue List</h3>  <span id="total_billing" class="badge bg-green"></span>
                  <div class="pull-right">
				   <?php if($access['add']==1){?>
                  	 <a class="btn btn-success pull-right" id="add_billing" href="<?php echo base_url('index.php/admin_ret_billing/issue/add');?>" ><i class="fa fa-plus-circle"></i> Add</a>
				   <?php }?>
					</div>
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
				  	<div class="col-md-12">
	                  <div class="box box-default">
	                   <div class="box-body">
						   <div class="row">


						        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
						        <div  class="col-md-2">
						           <label>Branch</label>
						            <select class="form-control" id="branch_select"></select>
						        </div>
						        <?php }else{?>
						            <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
						        <?php }?>

								<div  class="col-md-2">
						           <label>Employee</label>
						            <select class="form-control" id="emp_select"></select>
						        </div>
								<div  class="col-md-2">
						           <label>Karigar</label>
						            <select class="form-control" id="select_karigar"></select>
						        </div>
								<div  class="col-md-2">
						           <label>Account Head</label>
						            <select class="form-control" id="select_acc_head"></select>
						        </div>

						        <div class="col-md-2">
									<div class="form-group">
										<label>Date</label>
										<?php
											$fromdt = date("d/m/Y");
											$todt = date("d/m/Y");
									    ?>
			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">
									</div>
								</div>

								<div class="col-md-2">
									<label></label>
									<div class="form-group">
										<button type="button" id="issue_search" class="btn btn-info">Search</button>
									</div>
								</div>

								<div class="col-md-2">
									<label></label>
									<div class="form-group">
										<?php if($access['delete']==1) { ?>
										<button type="button" id="close_issue" class="btn btn-success">Close Issue</button>
									<?php }?>
									</div>
								</div>
							</div>
						 </div>
	                   </div>
	                  </div>
                   </div>

                  <div class="table-responsive">
	                 <table id="receipt_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
                          <th width="10%"><input type="checkbox" id="check_all_bill" value ="1" >Id</th>
	                        <th>Date</th>
	                        <th>Bill No</th>
							<th>Issue To</th>
	                        <th>Issue Type</th>
							<th>Account Head</th>
	                        <th>Customer Name</th>
							<th>Employee Name</th>
							<th>Karigar Name</th>
							<th>Mobile</th>
							<th>Ref no</th>
                            <th>Tot.Amount</th>
							<th>Branch </th>
							<th>Status </th>
							<th>Remarks </th>
	                        <th width="10%">Action</th>
	                      </tr>
	                    </thead>

						<tbody>
						</tbody>

						<tfoot>
							<td>Total</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tfoot>
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
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cancel Payment</h4>
      </div>
      <div class="modal-body">
                <input type="hidden" id="id_issue_receipt">
               <strong>Are you sure! You want to Cancel this Payment?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" id="cancel_payment">Cancel</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->
