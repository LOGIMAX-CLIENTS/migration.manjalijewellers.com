  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
       
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">METAL ISSUE LIST</h3>  <span id="total_billing" class="badge bg-green"></span>  
                  <div class="pull-right">
                  <?php if($access['add']==1){?>
                  	 <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/karigarmetalissue/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
                     <?php }?>
                    </div>
                </div>
                 <div class="box-body">
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
                  
                  <div class="table-responsive">
	                 <table id="stock_issue_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="5%">Id</th>
	                        <th width="5%">Issue Date</th>
	                        <th width="5%">Ref No</th>
	                        <th width="5%">Karigar</th>
	                        <th width="5%">Issue Weight</th>
	                        <th width="5%">Pure  Weight</th>
                          <th width="5%">Po No</th>
	                        <th width="5%">Order No</th>
	                        <th width="5%">Status</th>
                          <th width="5%">Metal Issue Type</th>
	                        <th width="5%">Remarks</th>
	                        <th width="5%">Action</th>
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

<div class="modal fade" id="confirm-billcancell" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cancell Bill</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to Cancell this bill?</strong>
                       <p></p>
                    <div class="row">
                      <div class="col-md-12">
                        <label>Remarks<span class="error">*</span></label>
                        <input type="hidden" id="metal_issue_id" name="">
                        <textarea class="form-control" id="metal_issue_cancel_remark" placeholder="Enter Remarks"  rows="5" cols="10"> </textarea>
                      </div>
                    </div>
      </div>
      <div class="modal-footer">
      	<button class="btn btn-danger" type="button" id="cancell_delete" disabled>Delete</button>
      </div>
    </div>
  </div>
</div> 
<!-- / modal -->      