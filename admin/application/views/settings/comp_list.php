  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Customer Details
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Setttings</a></li>
            <li class="active">Company List</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Company List</h3>      
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
                        <th>Code</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($comps)) {                     		
                     	 foreach($comps as $comp)
						{
                      ?>
                       <tr>
                         <td><?php echo $comp['id_company'];?></td>
                       	 
                       	
                       	 <td><?php echo $comp['company_name'];?></td>
                       	 <td><?php echo $comp['short_code'];?></td>
                       	 
                       	 <td>
                       	 	<a href="<?php echo base_url('index.php/settings/company/edit/'.$comp['id_company']) ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
                       	
                       	 <!--	<a href="#" class="btn btn-danger btn-del" data-href="<?php echo base_url('index.php/settings/company/delete/'.$customer['id_customer']) ?>" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-user-times"></i> Delete</a>-->
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

<!-- Login modal -->      
<div class="modal fade" id="create-login" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Create Customer Login</h4>
      </div>
      <div class="modal-body">
         <?php echo form_open('customer/login');?>
         	<div class="">
         			 <div class="row">
				 	<div class="form-group">
                       <label for="username" class="col-md-4 col-md-offset-1 ">User Name</label>
                       <div class="col-md-6">
                       	 <input type="text" class="form-control" id="scheme_code" name="login[username]" placeholder="User Name"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	 
				 <div class="row">
				 	<div class="form-group">
                       <label for="username" class="col-md-4 col-md-offset-1 ">Password</label>
                       <div class="col-md-6">
                       	 <input type="password" class="form-control" id="passwd" name="login[passwd]" placeholder="Password"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>	 
				 <div class="row">
				 	<div class="form-group">
                       <label for="passwd" class="col-md-4 col-md-offset-1 ">Confirm Password</label>
                       <div class="col-md-6">
                       	 <input type="password" class="form-control" id="confirm_passwd" name="login[confirm_passwd]" placeholder="Confirm Password"> 
                  <p class="help-block"></p>
                       	
                       </div>
                    </div>
				 </div>
				  <div class="row">
				 	<div class="form-group">
				 	 <label for="active" class="col-md-4 col-md-offset-1 ">Active</label>
				 		<div class="col-md-6">
				 		
					 		<label>
	                      		<input type="checkbox" id="active" class="minimal" name="login[active]" class="flat-red" checked/>
	                      
	                    	</label>
				 		</div>
				 	</div>
				 </div>
         	</div>  
         <?php echo form_close();?>
            
      </div>
      <div class="modal-footer">        
             <button type="submit" class="btn btn-success btn-login">Create</button>
       <!-- <a href="#" class="btn btn-success btn-login"  >Create</a>-->
        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<!-- / login modal --> 