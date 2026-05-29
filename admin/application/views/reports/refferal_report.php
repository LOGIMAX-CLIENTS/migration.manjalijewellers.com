<style type="text/css">
.DTTT_container{
margin-bottom:0 !important;
}
</style>
  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
           Employee Reffered Customer Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Employee Reffered Customer Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <!--<h3 class="box-title">Employee Reffered Customer Report</h3>      -->
                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 
						 
                </div><!-- /.box-header -->
                <div class="box-body">
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

				<!-- Lines Createdby Kanaga Durga 7/12/22-->
				  <div class="row">
					<div class="col-md-2">
					  <div class="form-group">
						<button class="btn btn-default btn_date_range" id="referral-dt-btn"> 
						  <span  style="display:none;" id="referal_list1"></span>
						  <span  style="display:none;" id="referal_list2"></span>
						  <i class="fa fa-calendar"></i> Date range picker
						  <i class="fa fa-caret-down"></i>
						</button>
					  </div>  
					</div>
					 <div class="col-md-2" id="credit_type_div">
                                                <div class="form-group" >
                                                    <label>Credit/Debit</label>									
                                                    <select id="credit_select" class="form-control" style="width:200px; ">
                                                        <option value='All'>All</option>
                                                        <option value='0'>Credit</option>
                                                        <option value='1'>Debit</option>
                                                    </select>
                                                    <input id="id_credittype"  name="id_credittype" type="hidden" value="" />
                                                </div>
                    </div>
				  </div>
				  <!-- up to here-->
				  
				  
                <div class="table-responsive">
                  <table  id="reff_report" class="table table-bordered table-striped text-center  reff_reports" >
                    <thead>
                      <tr>
                        <th>Customer Id</th>
                        <th>Customer Name</th>
                         <th>Scheme Code</th>
                        <th>Scheme Payment</th>
                         <!--<th>Referral Amount</th>-->
                        <th>Credit Amount</th>
                        <th>Debit Amount</th>
                        <th>Credit Type</th>
                        <th>Credit/Debit for</th>
                        <th>Scheme Account No</th>
                        <th>Receipt No</th>
                        <th>Credit/Debit Date</th>
                          
                      </tr>
                    </thead>
                   <!-- <tbody>
                     <?php 
                     	if(isset($accounts)) {                     		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>
							 <td><?php echo $account['id_customer'];?></td>
						 <td><?php echo $account['cus_name'];?></td>
						  <td><?php echo $account['code'];?></td>
						 <td><?php echo $account['payment_amount'];?></td>
						 <td><?php echo $account['benefit'];?></td>
						 <?php if($account['issue_type'] == 'Credit') {?>
						    <td><b style="color:#29eb29;"><?php echo $account['issue_type']?></b></td>
						   <?php }else{ ?>
						   <td><b style="color:red;"><?php echo $account['issue_type']?></b></td>
						   <?php } ?>
						  <td><?php echo $account['credit_for'];?></td>
                    	 <td><?php echo $account['scheme_acc_number'];?></td>
                    	 <td><?php echo $account['receipt_no'];?></td>
                         <td><?php echo $account['date_payment'];?></td>
                       	
                       </tr>
                       <?php } } ?>
                    </tbody>-->
                    <tfoot>
						<tr style="font-weight:bold;">
						  <td></td>
						  <td><b> Total Amount </b></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
						  <td></td>
                      </tr>
                    </tfoot>
                  </table>
                 </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      



<!-- / modal -->      

