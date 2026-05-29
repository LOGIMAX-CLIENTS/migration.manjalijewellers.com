  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Master
            <small>Branch Transfer</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Master</a></li>
            <li class="active">Branch Transfer</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Branch Transfer Approval List</h3>  <span id="total_count" class="badge bg-green"></span>  
                  <!-- <a class="btn btn-success pull-right" id="add_bnk"  href="<?php echo base_url('index.php/admin_ret_brntransfer/branch_transfer/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> -->
                 
                </div>
                 <div class="box-body">  
                  <div class="row">  
                  	<div class="col-md-offset-1 col-md-10">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
		                  <div class="col-md-3">
			                 <label for="">Type</label>
		                     <div class="form-group"> 
			                      <input type="radio" name="transfer_item_type" id="type1" value="1" checked> Tagged  &nbsp;&nbsp;
			                      <input type="radio" name="transfer_item_type" id="type2" value="2"> Non Tagged 
				             </div> 
		                     <div class="form-group">    
		                   		  <label>Trans Code </label> 
		                   		  <input type="text" class="form-control pull-right" id="bt_trans_code" placeholder="Branch Transfer Code">  
		                     </div> 
		                  </div>
		                  <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
		                     <div class="col-md-3">
		                        <div class="form-group">
		                          <label>From Branch <span class="error"> *</span></label>
		                            <select id="filter_from_brn" class="form-control ret_branch"></select> 
		                        </div>   
		                        <div class="form-group">
		                          <label>To Branch <span class="error"> *</span></label>
		                            <select id="filter_to_brn" class="form-control"></select> 
		                        </div>  
		                    </div> 
		                  <?php }else{?>
		                     <input type="hidden" id="id_branch"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
		                  <?php }?>  
		                  <div class="col-md-3">
		                     <div class="form-group">
		                       <label>Lot</label> 
		                       <select class="form-control" id="lotno"></select>
		                     </div>  
		                     <div class="form-group">
		                       <label>Product</label> 
		                       <input type="text" class="form-control" id="product" placeholder="Product Name/Code" autocomplete="off">
							   <input type="hidden" class="form-control" id="id_product">
		                     </div>
		                  </div> 
		                  <div class="col-md-3 "> 
		                     <div class="form-group tagged">
		                       <label>Design</label> 
		                       <input type="text" class="form-control" id="design" placeholder="Design"  autocomplete="off">
							   <input type="hidden" class="form-control" id="id_design">
		                     </div> 
		                     <div class="form-group">    
		                   		  <label>Date </label> 
		                   		  <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date">  
		                     </div>
		                     <div class="form-group">
		                     	<button type="button" id="btran_filter" class="btn btn-info pull-right">Filter</button>  
		                     </div>
		                  </div> 
	                   </div> 
	                  </div> 
                   </div> 
                  </div> 
                  <div class="row status_blk">
	                  <div class="col-md-2 pull-right">
	                    <div class="form-group ">
	                        <label> </label>
	                          <div class="btn-group" data-toggle="buttons">
	                            <label class="btn btn-success" id="approve">
	                                <input type="radio" name="upd_status_btn" value="2"><i class="icon fa fa-check"></i> Approve
	                            </label>
	                            <label class="btn btn-danger" id="reject">
	                                 <input type="radio" name="upd_status_btn"  value="3"><i class="icon fa fa-remove"></i> Reject
	                            </label>
	                          </div>
	                      </div>
	                  </div>
	              </div> 
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
			  
                  <div class="table-responsive tagged">
	                 <table id="bt_approval_list" class="table table-bordered table-striped text-center ">
	                    <thead>
	                      <tr>
	                        <th><label class="checkbox-inline"><input type="checkbox" id="appr_sel_all_tg" name="appr_sel_all_tg" value="all"/>All</label></th>  
                          	<th width="25%">From Branch</th>  
                          	<th width="25%">To Branch</th> 
                          	<th width="5%">Lot</th> 
                          	<th width="5%">Type</th>  
                          	<th width="10%">Tag No</th>  
                          	<th width="15%">Product</th>  
                          	<th width="15%">Design</th>    
                          	<th width="5%">Pcs</th>  
                          	<th width="5%">G.wt</th>  
                          	<th width="5%">N.wt</th>  
                          	<th width="10%">Status</th>
	                      </tr>
	                    </thead> 
	                    <tfoot>
	                    	<tr>
	                    		<th colspan="8">Total</th>
	                    		<td><input type="text" class="t_tot_pieces" disabled="true" placeholder="Pieces"/></td>
	                    		<td><input type="text" class="t_tot_gross_wt" disabled="true" placeholder="Gross Weight"/></td>
	                    		<td><input type="text" class="t_tot_net_wt" disabled="true" placeholder="Net Weight"/></td>
	                    		<td></td>
	                    	</tr>
	                    </tfoot>
	                 </table>
                  </div>
                  <div class="table-responsive non_tagged"  style="display: none">
	                 <table id="bt_approval_list_nt" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th><label class="checkbox-inline"><input type="checkbox" id="appr_sel_all_nt" name="appr_sel_all_nt" value="all"/>All</label></th> 
                          	<th>From Branch</th>  
                          	<th>To Branch</th> 
                          	<th>Lot</th>
                          	<!--<th>Lot Date</th>  -->
                          	<th>Type</th>  
                          	<th>Product</th>   
                          	<th>Pcs</th>  
                          	<th>G.wt</th>  
                          	<th>N.wt</th>  
                          	<th>Status</th>
	                      </tr>
	                    </thead> 
	                    <tfoot>
	                    	<tr>
	                    		<th colspan="6">Total</th>
	                    		<td><input type="text" class="nt_tot_pieces" disabled="true" placeholder="Pieces"/></td>
	                    		<td><input type="text" class="nt_tot_gross_wt" disabled="true" placeholder="Gross Weight"/></td>
	                    		<td><input type="text" class="nt_tot_net_wt" disabled="true" placeholder="Net Weight"/></td>
	                    		<td></td>
	                    	</tr>
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
        <h4 class="modal-title" id="myModalLabel">Delete Order</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this Order?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->   

<!-- modal -->      
<div class="modal fade" id="confirm-view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Order Details</h4>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <input type="hidden" id="id_orderdetails"  name="">
        <a href="#" class="btn btn-success btn-confirm" id="reason_submit" >Submit</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="image-view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Order Images</h4>
      </div>
      <div class="modal-body">
             <div id="imagePreview"></div>  
      </div>
    
    </div>
  </div>
</div>
<!-- / modal -->      
