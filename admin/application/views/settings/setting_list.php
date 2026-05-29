  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Settings Details
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Settings</a></li>
            <li class="active">settings List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Settings List</h3>      
                          <!-- <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/customer/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> -->
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
                  <table id="emp_list" class="table grid table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Symbol</th>
						<th>Allow Advance</th>
						<th>Allow Pending</th>
                        <th>Allow Pre-closer</th> 
						<th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($settings)) {                     		
                     	 foreach($settings as $setting)
						{
                      ?>
                       <tr>
                         <td><?php echo $setting['id'];?></td>
						 	 <td><?php echo $setting['currency_name'];?></td>
                       	 <td><?php echo $setting['currency_symbol'];?></td>
						 <td><?php echo ($setting['adv_payment']== 1?"YES":"NO");?></td>
						 <td><?php echo ($setting['allow_pending_due']== 1?"YES":"NO");?></td>
                       	 <td><?php echo ($setting['pre_closer']== 1?"YES":"NO");?></td>
                       	
                       	 <td>
                       	 	<a href="<?php echo base_url('index.php/settings/generalsettings/edit/'.$setting['id']) ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
                       	
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
        <h4 class="modal-title" id="myModalLabel">Delete Customer</h4>
      </div>
      <div class="modal-body">
               <strong>Are you sure! You want to delete this customer record?</strong>
      </div>
      <div class="modal-footer">
      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- / modal -->      
