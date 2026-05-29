  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Mail Request
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Mails</a></li>
            <li class="active">Joining request</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Scheme Joining Request</h3>      
                      <!--<a class="btn btn-success pull-right" href="<?php // echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> -->
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
                <div class="table_responsive">
                  <table id="sch_acc_list" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Chit No</th>
                        <th>Customer</th> 
                        <th>Mobile</th>                      
                        <th>Comments</th>
                        
                        <th>Status</th>                     
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($requests)) {                     		
                     	 foreach($requests as $request)
						{
                      ?>
                       <tr>
                         <td><?php echo $request['id_enquiry'];?></td>
                         <td><?php echo date("d-m-Y",strtotime($request['date_enquiry']));?></td>
                       	 <td><?php echo $request['chit_acc_number'];?></td>
                       	 
                       	 <td><?php echo $request['name'];?></td>
                       	 <td><?php echo $request['mobile'];?></td>
                       	 <td><?php echo $request['comments'];?></td> 
                       	 <td><?php echo ($request['status']==1?'Approved':($request['status']==2?'Hold':($request['status']==3?'Rejected':'Pending')));?></td>                       	 
                                     
                       
                       	 
                       	 <td>
                       	 <?php if($request['status']!=1) {?>
                       	 <a href="<?php echo base_url('index.php/mail/joining/update/1/'.$request['id_enquiry']); ?>" class="btn btn-success input-sm" ><i class="fa fa-check-circle"></i> Approve</a>
                       	 <a href="<?php echo base_url('index.php/mail/joining/update/2/'.$request['id_enquiry']); ?>" class="btn btn-warning input-sm" ><i class="fa fa-dot-circle-o"></i> Hold</a>
                       	 <a href="<?php echo base_url('index.php/mail/joining/update/3/'.$request['id_enquiry']); ?>" class="btn btn-danger input-sm" ><i class="fa fa-times-circle"></i> Reject</a>
                       	 <?php } ?>
                       	 	
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
