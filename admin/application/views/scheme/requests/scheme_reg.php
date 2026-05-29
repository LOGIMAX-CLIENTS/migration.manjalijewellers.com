  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
         Scheme Registration
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Scheme</a></li>
            <!--<li class="active">reg_list</li>-->
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Scheme Register List</h3> <span class="badge bg-green" id="total_requests"></span>
                </div><!-- /.box-header -->
                <div class="box-body">    
	            <?php 
				  $attributes = array('id' => 'settled_payments');
				//  echo form_open('payment/update_status',$attributes);
				 ?>
            	 <div class="row">
                	 <div class="col-md-6">
	                	<div class="btn-group" data-toggle="buttons">
					        <label class="btn btn-success" id="approve">
					            <input type="radio" name="upd_status_btn" value="1"><i class="icon fa fa-check"></i> Approve
					        </label>
					        <label class="btn btn-danger" id="reject">
					            <input type="radio" name="upd_status_btn"  value="2"><i class="icon fa fa-remove"></i> Reject
					        </label>
					        <label class="btn btn-warning" id="revert" style="display:none;">
					            <input type="radio" name="upd_status_btn"  value="3"><i class="icon fa fa-remove"></i> Revert
					        </label>
						</div>
						<div class="form-group col-md-6 pull-right">
						 <label for="" ><a  data-toggle="tooltip" title="Select status"> Filter by Status  </a> <span class="error"></span></label>
						<select id="filtered_status" class="form-control">
							<option value="3">All</option>
							<option value="0">Processing</option>
							<option value="1">Approved</option>
							<option value="2">Rejected</option>
						</select>
						
						
						</div>
                	 </div> 
	                <div class="col-md-6">
					 
						<?php if($this->session->userdata('branch_settings')==1){?>
							<div class="form-group col-md-6">
							 <label for="" ><a  data-toggle="tooltip" title="Select branch"> Filter by Branch  </a></label>
							<select id="sel_branch" class="form-control"></select>
							</div>
							<?php }?>
						<div class="pull-right">
							<div class="form-group">
							   <button class="btn btn-default btn_date_range" id="reqList-dt-btn">
							    <span  style="display:none;" id="payment_list1"></span>
                                 <span  style="display:none;" id="payment_list2"></span>   
								<i class="fa fa-calendar"></i> Date range picker
								<i class="fa fa-caret-down"></i>
								</button>
							</div>
						 </div>
					</div>
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
                  <table id="scheme_reg_list" class="table table-bordered table-striped dataTable text-center grid" >
                  <thead>
					<?php $data=$this->admin_settings_model->settingsDB('get','','');?>
                      <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Code</th>
                        <th>Group Code</th>
                        <th>Chit No.</th>
						<?php if($data[0]['getExisting_balance']==1){?>
                        <th>Paid Installments</th>
                        <th>Closing Amount</th>
                        <th>Last paid date</th>
                        <th>Closing Weight</th>
                        <th>Last Closing Weight</th>
                        <th>Payment Chances</th>
                      <?php }?>
                     <th>First Installment Payment Amount</th>
                        <th>Ac Name</th>
                        <th>Branch</th>    
						<th>Requested On</th>  
						<th>Status</th> 	
                        <th>Remark</th>   
                      </tr>
                    </thead>
                    <tbody>
                       </form>
                    </tbody>
               <!--  <tfoot>
                      <tr >
                         <td colspan="10"> <p style="text-align:left"></p></td>
                      </tr>
                    </tfoot> -->
                  </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
       

<style type="text/css">
.popover1{
    width:230px;
    height:330px;    
}
.trans tr{
	 width:50%;
    height:50%;
	font-size:15px;
	
}
</style>