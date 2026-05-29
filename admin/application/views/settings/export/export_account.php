  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Scheme Details
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Settings</a></li>
            <li class="active">Export Account</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Scheme Account List</h3>      
                          
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
	            <?php echo form_open('settings/export/account');?>
	            <div class="row">
                    	<div class="col-sm-12">
                    		<div class="form-group">
                    			<label class="radio-inline">
                    				<input type="radio" class="minimal" name="filter_by" value="0"/>
                    				All
                    			</label>
                    			
                    			<label class="radio-inline">
                    				<input type="radio" class="minimal" name="filter_by" value="1"/>
                    				Ref Number
                    			</label>
                    			
                    			<label class="radio-inline">
                    				<input type="radio" class="minimal" name="filter_by" value="2"/>
                    				Without Ref Number
                    			</label>
                    		</div>
                    	</div>
                    	  	<div  class="row">
                 		<div class="col-sm-4">
                 			<div class="form-group">
                 				<label class="col-sm-4">From date </label>
                 				<div class="form-group">
                 				<div class="col-sm-8 input-group date">
								    <input type="text" id="from_date" name="from_date" class="form-control myDatePicker" value="">
								    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
								</div>
                 			</div>
                 		</div>
                 		
                 		<div class="col-sm-4">
                 			<div class="form-group">
                 				<label class="col-sm-4"> 
                 				 <input  type="checkbox" id="is_to_date" name="is_to_date" value="1"/>
                 				To date</label>
                 				<div class="form-group">
                 				<div class="col-sm-8 input-group date">
								    <input type="text" id="to_date" class="form-control myDatePicker" name="to_date" value="">
								    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
								</div>
                 			</div>
                 		</div>
                 	<div class="col-sm-4">
                      <button class="btn btn-primary" type="submit"><i class="fa fa-file-excel-o"></i> Export</button> 
                    	</div>
                 	</div>
                    
                    </div>    
                </form>
                  <table id="sch_acc_list" class="table table-bordered table-striped text-center">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Account.No</th>
                        <th>Ref.No</th>                        
                        <th>Customer</th> 
                         <th>Mobile</th>                       
                        <th>Scheme Code</th>
                        <th>Start Date</th>
                       <!-- <th>Type</th>-->
                        <th>Amount</th>
                       
                        <th>Created on</th>
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
                       	 <td><?php echo $account['scheme_acc_number'];?></td>
                       	 <td><?php echo $account['ref_no'];?></td>
                       	 <td><?php echo $account['name'];?></td>
                       	 <td><?php echo $account['mobile'];?></td>
                       	 <td><?php echo $account['code'];?></td>
                       	 <td><?php echo date("d-m-Y",strtotime($account['start_date']));?></td>
                       	<!-- <td><?php echo $account['scheme_type'];?></td>-->
                       	 <td><?php echo $account['amount'];?></td>
                       
                       	 
                       	 <td><?php echo date("d-m-Y",strtotime($account['date_add']));?></td>
                       	
                       </tr>
                       <?php } } ?>
                    </tbody>
                 <!--   <tfoot>
                      <tr>
                        
                      </tr>
                    </tfoot> -->
                  </table>
                  
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
