




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
            Payment Report
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Payment Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Customer Refered Report</h3> 
				
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


						<div class="form-group">
		                    <div class="input-group report_date">
		                       <button class="btn btn-default btn_date_range" id="cus_ref_report_data">
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div>
             <div class="table-responsive">
                  <table  id="cus_refferal" class="table table-bordered table-striped text-center cus_refferal" >
                    <thead>
                      <tr>
                         <th>Customer Id</th>
                        <th>Customer Name</th>
                        <th>Referral Code</th>                     
                        <th>No of Referred </th>
                        <!-- <th>Type</th>-->
                        <th>Benefit Amount</th>
                           <!--   <th>Action</th> -->
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($accounts)) {                     		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>                                          
                         <td><a href="<?php echo base_url('index.php/reports/payment/cus_refferl_account/'.$account['id_customer'])?>">
						 <?php echo $account['id_customer'];?></a></td>
                   
                    	 <td><?php echo $account['name'];?></td>
                    	 <td><?php echo $account['cus_referalcode'];?></td>
                       	 <td><?php echo $account['refferal_count'];?></td>
                       	
                        <td><?php echo $account['benifits'];?></td>
                       	
                       
                       	
                       </tr>
                       <?php } } ?>
                    </tbody>
                     <tfoot>
                 <th></th> <th></th> <th></th> <th></th><th></th>
                    </tfoot>
                  </table>
                 </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


<!-- modal -->      

