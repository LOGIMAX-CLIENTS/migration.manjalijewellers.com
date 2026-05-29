  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Master
            <small>SUPPLIER PAYMENT LIST</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Supplier Payment</a></li>
            <li class="active">Entry</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <div class="pull-left">
					    <div class="form-group"> 
    						<button class="btn btn-default btn_date_range" id="sp-dt-btn">
        						<span  style="display:none;" id="sp_date1"></span>
        						<span  style="display:none;" id="sp_date2"></span>
        						<i class="fa fa-calendar"></i> Date range picker
        						<i class="fa fa-caret-down"></i>
    						</button>
						</div>
					</div>
                  <div class="pull-right">
                  <?php if($access['add']==1){?>
                  	 <a class="btn btn-success pull-right" id="add_Order" href="<?php echo base_url('index.php/admin_ret_purchase/supplier_po_payment/add');?>" ><i class="fa fa-plus-circle"></i> Add</a> 
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
	                 <table id="payment_list" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="5%">Id</th>
	                        <th width="10%">Pay Date</th>
	                        <th width="10%">Pay Ref no</th>
	                        <th width="10%">Supplier</th>
	                        <th width="10%">Amount</th>
	                        <th width="10%">Type</th>
	                        <th width="10%">Status</th>
	                        <th width="10%">Action</th>
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
                        <input type="hidden" id="pay_id" name="">
                        <textarea class="form-control" id="payment_cancel_remark" placeholder="Enter Remarks"  rows="5" cols="10"> </textarea>
                      </div>
                    </div>
      </div>
      <div class="modal-footer">
      	<button class="btn btn-danger" type="button" id="payment_cancel" disabled>Cancel</button>
      </div>
    </div>
  </div>
</div> 
<!-- / modal -->      