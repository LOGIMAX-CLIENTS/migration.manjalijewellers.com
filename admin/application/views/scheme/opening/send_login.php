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
            <li><a href="#">Manage Account</a></li>
            <li class="active">Send SMS</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
        <?php echo form_open("account/send/sms"); ?>
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Send Login</h3>      
                           <button type="submit" class="btn btn-primary pull-right" ><i class="fa fa-send-o"></i> Send SMS</button>
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
                
                  <table id="import_list" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr>
                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>
                        <th>Account.No</th>
                        <th>Ref.No</th>                        
                        <th>Customer</th> 
                         <th>Mobile</th>                       
                        <th>Scheme Code</th>
                        <th>Start Date</th>
                       <!-- <th>Type</th>-->
                        <th>Amount</th>
                        <th>Created</th>
                      </tr>
                    </thead>
                    <tbody>
                     <?php 
                     	if(isset($accounts)) {                     		
                     	 foreach($accounts as $account)
						{
                      ?>
                       <tr>
                         <td><label class="checkbox-inline"><input type="checkbox" name="account_id[]" value="<?php echo $account['id_scheme_account'];?>" /><?php echo $account['id_scheme_account'];?></label></td>
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
                 </form> 
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
 