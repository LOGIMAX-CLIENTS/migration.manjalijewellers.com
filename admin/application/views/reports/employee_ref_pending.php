




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
                  <h3 class="box-title">Customer-wise Payment Report</h3>      
                <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>--> 
						 <div class="col-sm-12">
							<div class="col-md-6">
								<div class="col-md-4">
									<div class="form-group">	
										<label for="" ><a  data-toggle="tooltip" title="Select branch "> Select Branch  </a> <span class="error">*</span></label>
										<select id="branch_select" class="form-control"></select>
														
											<input id="id_branch" name="account[id_branch]" type="hidden" value="" />	
									</div>
								</div>
							</div>
						</div>
				
				
				
				
				
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
                <div class="table-responsive">
                  <table  id="employee_refferal_pending" class="table table-bordered table-striped text-center employee_refferal_pendings" >
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Emp code</th>                     
                        <th>Reff count</th>
                        <!-- <th>Type</th>-->
                        <th>benifit amount</th>
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
                         <td><a href="<?php echo base_url('index.php/reports/payment/refferl_account/'.$account['id_employee'])?>"> <?php echo $account['id_employee'];?></a></td>
                    <!--         <td><?php echo (trim($account['ref_no'])!=NULL?$account['ref_no']:'<b style=color:red>N/A</b>');?></td>
                    	 <td><?php echo $account['name'];?></td>-->
                    	 <td><?php echo $account['emp_code'];?></td>
                       	 <td><?php echo $account['refferal_count'];?></td>
                       	
                       <!--	 <td><?php echo $account['benifits'];?></td>-->
                       	
                       <!--  <td>
                       	 	<a href="<?php echo base_url('index.php/account/edit/'.$account['id_scheme_account']);?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>                       	 	
                       	 	    
                      	 </td> -->
                       	
                       </tr>
                       <?php } } ?>
                    </tbody>
                     <tfoot>
                
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

