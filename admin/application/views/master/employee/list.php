  <style type="text/css">
  
    .employee_device_status .row {
  
        padding-bottom: 10px;
  
    }
  
  </style>

  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Employee Details

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Masters</a></li>

            <li class="active">Employee List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">Employee List</h3> <span id="total_employees" class="badge bg-green"></span>      

                           <a class="btn btn-success pull-right" id="add_employee"  href="<?php echo base_url('index.php/employee/add'); ?>"><i class="fa fa-user-plus"></i> Add</a> 

                           <!--Lines Added by Durga 19-07-2023 starts here-->

                            <input type="hidden" id="device_enable_count" value="<?php echo $device_enable_count; ?>" />

                            <input type="hidden" id="login_profile" value="<?php echo $this->session->userdata['profile']; ?>" />

                            <input type="hidden" id="enable_device_limit" value="<?php echo $chit_data['chitCollectionEmpCount']; ?>" />

                            <a class="btn btn-success pull-right" data-toggle="modal" data-target="#enable_device_modal" style="margin-right:20px;display:none;" id="enable_device_modal_button"><i class="fa fa-check"></i> ENABLE DEVICE</a> 

                            <!--Lines Added by Durga 19-07-2023 ends here-->

                </div><!-- /.box-header -->

                <div class="box-body">

                <!-- Alert -->

                <?php 

                	if($this->session->flashdata('chit_info'))

                	 {

                		$message = $this->session->flashdata('chit_info');

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

                        <th>Branch</th>

                        <th>Dept</th>

                        <th>Username</th>

                        <th>Mobile</th>

                        <th>Code</th>

                        <th>Type</th>
                        
                        <th>Devices</th>

                        <th>Status</th>

                        <th>Action</th>

                      </tr>

                    </thead>

                  <!--  <tbody>

                     <?php 

                     	/* if(isset($employees)) {                     		

                     	 foreach($employees as $employee)

						{

                      ?>

                       <tr>

                         <td><?php echo $employee['id_employee'];?></td>

                       	 <td><?php echo $employee['firstname']." ".ucfirst($employee['lastname'][0]);?></td>

                       	

                       	 <td><?php echo $employee['dept'];?></td>

                       	 <td><?php echo ($employee['username']!=NULL?$employee['username']:"-");?></td>

                       	 	 <td><?php echo $employee['usertype'];?></td>

                       	 <td><?php echo ($employee['active']==1 ? 'Active':'Disabled');?></td>

                       	 <td>

                       	 	<a href="<?php echo base_url('index.php/employee/edit/'.$employee['id_employee']) ?>" class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>

                       	<!-- 	<?php echo ($employee['username']!=NULL? anchor('#', '<i class="fa fa-user-plus"></i> Edit A/c', array( 'title'=>"Get Login Account",'class'=>'btn btn-warning','data-target'=>'#create-login','data-toggle'=>'modal','data-href'=>'')):anchor('#', 'Create A/c', array( 'title'=>"Get Login Account",'class'=>'btn btn-warning','data-target'=>'#create-login','data-toggle'=>'modal','data-href'=>'')));?>-->

                       	 	<a href="#" class="btn btn-danger btn-del" data-href="<?php echo base_url('index.php/employee/delete/'.$employee['id_employee']) ?>" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-user-times"></i> Delete</a>

                       	 </td>

                       </tr>

                       <?php } }  */?>

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

        <h4 class="modal-title" id="myModalLabel">Delete Employee</h4>

      </div>

      <div class="modal-body">

               <strong>Are you sure! You want to delete this employee record?</strong>

      </div>

      <div class="modal-footer">

      	<a href="#" class="btn btn-danger btn-confirm" >Delete</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Close</button>

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

        <h4 class="modal-title" id="myModalLabel">Create Employee Login</h4>

      </div>

      <div class="modal-body">

         <form>

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

                       	 <input type="text" class="form-control" id="scheme_code" name="login[username]" placeholder="User Name"> 

                  <p class="help-block"></p>

                       	

                       </div>

                    </div>

				 </div>	 

				 <div class="row">

				 	<div class="form-group">

                       <label for="username" class="col-md-4 col-md-offset-1 ">Confirm Password</label>

                       <div class="col-md-6">

                       	 <input type="text" class="form-control" id="scheme_code" name="login[username]" placeholder="User Name"> 

                  <p class="help-block"></p>

                       	

                       </div>

                    </div>

				 </div>

         	</div>  

         </form>

            

      </div>

      <div class="modal-footer">        

        <a href="#" class="btn btn-success btn-login"  >Create</a>

        <button type="button" class="btn btn-warning btn-cancel" data-dismiss="modal">Cancel</button>

      </div>

    </div>

  </div>

</div>

<!-- / login modal --> 



<!-- enable device modal Added by Durga -->      

<!-- enable device modal -->      

<div class="modal fade" id="enable_device_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Enable Device</h4>

      </div>

      <div class="modal-body">

      <?php echo form_open("employee/enable_device") ?>

         <!-- <form> -->

         	<div class="employee_device_status">

         			 <div class="row">

				 	          <div class="form-group">

                       <label for="employee" class="col-md-4 col-md-offset-1 ">Employee</label>

                       <div class="col-md-6">

                       	 <select id="employee_select" style="width:200px;" required>

                          </select>

                          <input type="hidden" name="enable_device[id_employee]" id="employee_id">

                        <p class="help-block"></p>

                       	

                        </div>

                    </div>

				     </div>	 

              <div class="row">

                      <div class="form-group">

                            <label for="uuid" class="col-md-4 col-md-offset-1 ">Device Type</label>

                            <div class="col-md-6">

                              <select id="app_type" style="width:200px;" class="form-control" name="enable_device[app_type]" required>

                                <option value="1">Chit Collection</option>

                                <option value="2">Estimation App</option>

                              </select>

                            </div>

                        </div>

              </div>

              <div class="row">

                      <div class="form-group">

                            <label for="uuid" class="col-md-4 col-md-offset-1 ">Device UUID</label>

                            <div class="col-md-6">

                              <select id="device_select" style="width:200px;" required>

                              </select>

                              <input type="hidden" class="form-control" id="device_uuid" name="enable_device[uuid]" > 

                              <p class="help-block"></p>

                            </div>

                        </div>

              </div>	

              <div class="row">

                      <div class="form-group">

                            <label for="uuid" class="col-md-4 col-md-offset-1 ">Choose Method</label>

                            <div class="col-md-6">

                              <select id="enable_device" style="width:200px;" name="enable_device[enable_device]" required class="form-control">

                                <option value="1">Enable</option>
                              
                                <option value="0">Disable</option>

                              </select>

                            </div>

                        </div>

              </div>	 

              <!-- <div class="row">

                      <div class="form-group">

                            <label for="uuid" class="col-md-4 col-md-offset-1 ">Device UUID</label>

                            <div class="col-md-6">

                              <input type="text" class="form-control" id="device_uuid" name="enable_device[uuid]" placeholder="UUID" autocomplete="off" required> 

                              <p class="help-block"></p>

                              

                            </div>

                        </div>

              </div>	  

              <div class="row">

                      <div class="form-group">

                          <div class="col-md-1">

                            <input type="checkbox" id="enable_device" name="enable_device[enable_device]" <?php if($employees['enable_device_collection']==1){?>checked="true" <?php } ?> value="1" >

                            

                          </div>

                           <label class="col-md-5"  style="position:relative; left: -25px" for="enable_device">

                            Enable Device For Chit Collection



                           </label>

                           <div class="col-md-1">

                            <input type="checkbox" id="enable_estimation_device" name="enable_device[enable_estimation_device]" <?php if($employees['enable_estimation_app']==1){?>checked="true" <?php } ?> value="1" >

                            

                          </div>

                           <label class="col-md-5"  style="position:relative; left: -25px" for="enable_estimation_device">

                            Enable Device For Estimation App



                           </label>

                            

                               

                              <p class="help-block"></p>

                              

                          

                      </div>

              </div>-->

         	</div>  

         <!-- </form> -->

            

      </div>

      <div class="modal-footer">        

        <button type="submit" class="btn btn-success btn-submit" id="chit_save">Save</button>

        <button type="button" class="btn btn-warning" id="chit_cancel">Cancel</button>

      </div>

      <?php echo form_close(); ?>

    </div>

  </div>

</div>

<!-- / enable device modal --> 


<!-- enable device modal -->      

<div class="modal fade" id="view_devices_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

        <h4 class="modal-title" id="myModalLabel">Devices</h4>

      </div>

      <div class="modal-body">

        <div class="row ">

          <table class='table table-bordered table-striped text-center devices_table'>
            
            <thead>
                    
                <tr>
                      
                <td>ID</td>
                      
                <td>Device ID</td>
                      
                <td>Device Type</td>
                      
                <td>Date</td>
                      
                <td>App Type</td>
                      
                <td>Device Info</td>
                      
                <td>Status</td>
                    
              </tr>
                  
            </thead>
                
            <tbody>
                
            </tbody>
                
          </table>

        </div>

      </div>

    </div>

  </div>

</div>

<!-- / view devices modal --> 