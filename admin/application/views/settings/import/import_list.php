  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Imported details
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">settings</a></li>
            <li class="active">Imported list</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
      
          <div class="row">
            <div class="col-xs-12">
           
              <div class="box">
                <div class="box-header">

                  <h3 class="box-title">Send login details to customers</h3>      
                        <a class="btn btn-danger pull-right"  href="<?php echo base_url('index.php/settings/import/download');?>" ><i class="fa fa-download"></i> Rejected</a>
                        <button type="submit" class="btn btn-warning pull-right" id="sendSMS"><i class="fa fa-send-o"></i> Send SMS</button>
                   <button type="submit" class="btn btn-primary pull-right" id="sendEmail"><i class="fa fa-envelope-o"></i> Send Email</button>      
                 <input  type="hidden" id="lower"  name="lower" value="<?php echo $first_id;?>"/>
                  <input  type="hidden" id="upper" name="upper" value="<?php echo $last_id; ?>"/>    	
                </div><!-- /.box-header -->
                <div class="box-body">
                <!-- Alert -->
                <?php 
                	if($this->session->flashdata('chit_alert'))
                	 {
                		$message = $this->session->flashdata('chit_alert');
                ?>
                       
	                    <div class="alert alert-success alert-dismissable" style="display: none;">
	                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	                    <h4><i class="icon fa fa-check"></i>Send Login!</h4>
	                        <div id="alert_msg" ></div>
	                  </div> 
	                  
	            <?php } ?>      
                  <div class="table-responsive">
                  <table id="imported_list" class="table table-bordered table-striped text-center grid">
                    <thead>
                      <tr>
                        <th><label class="checkbox-inline"><input type="checkbox" id="sel_imported_all" name="select_all" value="all"/>All</label> ID </th>                  
                        <th>Customer</th> 
                         <th>Mobile</th>  
                        <th>email</th>
                      </tr>
                    </thead>
                  
                  </table>
               
				</div> 
                </div><!-- /.box-body -->
                 <div class="overlay" style="display: none;">
                   <i class="fa fa-refresh fa-spin"></i>
                	</div>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
 