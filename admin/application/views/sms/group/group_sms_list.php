  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Group SMS/Email
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">SMS/Email</a></li>
            <li class="active">Group SMS/Mail</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title"> List</h3>      
                          <?php /*?> <a class="btn btn-primary pull-right" href="<?php echo base_url('index.php/account/update/client'); ?>"><i class="fa fa-retweet"></i> Sync Account</a> <?php */?>
                           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/sms/group_form/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 
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
                  <table id="sch_acc_list" class="table table-bordered table-striped dataTable text-center grid" >
                    <thead>
                      <tr>
                        <th>ID</th>
                     <!--   <th>Account.No</th>
                        <th>Client ID</th>-->
                        
                        <th>Customer</th> 
                        <th>Group</th>    
						<th>Mail</th>                        
						<th>SMS</th>
						<th>Status</th> 	
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($accounts)) {                     		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>
                         <td><?php echo $account['id_scheme_account'];?></td>
                       	 
						
						 
                       	 <td><?php echo $account['name'];?></td>
                       	 <td><?php echo $account['mobile'];?></td>
						  <td><?php echo ($account['is_new'] == 'Y' ? 'New' : 'Existing')?></td>
                       	 <td><?php echo $account['code'];?></td>
						 <td><?php echo $account['scheme_acc_number'];?></td>
                       	 <td><?php echo date("d-m-Y",strtotime($account['start_date']));?></td>
                       	<!-- <td><?php echo $account['scheme_type'];?></td>-->
                       	 <td><?php echo ( $account['scheme_type'] == 'Amount' ? "Rs. ".$account['amount'] : "max ".$account['max_weight']." gm/month");?></td>
                       
                       	 <td><a class="label label-<?php echo ($account['active'] == 'Active' ? 'success':'danger' )?>" href="<?php echo base_url('index.php/account/status/'.($account['active'] == 'Active' ? 0:1 ).'/'.$account['id_scheme_account']); ?>"><?php echo $account['active']?></a></td>
                       	
                       	 <td style="width:180px;" >
						   <div class="btn-group inline" >
                       	 	<a href="<?php echo base_url('index.php/account/edit/'.$account['id_scheme_account']);?>" class="btn btn-primary input-sm" ><i class="fa fa-edit"></i> </a>
                       	 	<a href="#" class="btn btn-danger input-sm btn-del" data-href="<?php echo base_url('index.php/account/delete/'.$account['id_scheme_account']); ?>"  data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i> </a>                       	 	                      	 	
                       	 	<a href="<?php echo base_url('index.php/account/close/scheme/'.$account['id_scheme_account']); ?>" class="btn btn-warning input-sm" ><i class="fa fa-user-times"></i> </a>
                       	   </div>
                       	 	
                       	 </td>
                       	
                       </tr>
                       <?php } } ?>
                    </tbody>
                 <!--   <tfoot>
                      <tr>
                        
                      </tr>
                    </tfoot> -->
                  </table>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
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
        <h4 class="modal-title" id="myModalLabel">Delete Scheme</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this scheme?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
<!-- modal close account -->      
<div class="modal fade" id="confirm-close" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Confirm Close Scheme</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to close this scheme account?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Close Account</a>
        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal close account -->  
