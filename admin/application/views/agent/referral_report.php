<!-- Content Wrapper. Contains page content -->  
<div class="content-wrapper">
   <!-- Content Header (Page header) -->        
   <section class="content-header">
      <h1>            Agent Referral Report           <small></small>  
      <span id="record_count" class="badge bg-green"></span>   </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
         <li><a href="#">Agnet</a></li>
         <li class="active">Referral Report</li>
      </ol>
   </section>
   <!-- Main content -->        
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box box-primary">
               <div class="box-header with-border">
                  
                  
                  <div class="col-md-2" style="margin-top:20px">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                        <button class="btn btn-default btn_date_range" id="settlement-dt-btn">
							    <span style="display:none;" id="referral_list1"></span>
							    <span style="display:none;" id="referral_list2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		               </div>
               </div>
               <!-- /.box-header -->                
               <div class="box-body">
                  <!-- Alert -->                
                  <?php                	
                  if($this->session->flashdata('chit_alert'))                	 {                		
                  $message = $this->session->flashdata('chit_alert');                	?>                       
                  <div  class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>	                    
                     <h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
                     <?php echo $message['message']; ?>	                  
                  </div>
                  <?php } ?>			
                  <div class="row">
                     <div class="col-md-12">
                         	
                        <div class="table-responsive">
                           <table id="agent_referral_list" class="table table-bordered table-striped text-center table_list">
                              <thead>
                                 <tr>
                                    <th>Agent Name</th>
                                    <th>Available Earnings</th>
                                    <th>Referrals</th>
                                    <th>Conversions</th>
                                    <th>Pending Conversion</th>
                                    <th>Pending Dues</th>
                                    <th>Scheme Total Amt</th>
                                    <th>View</th>
                                 </tr>
                              </thead>
                           </table>
                        </div>
                        <div class="overlay" style="display:none;">					    
                        <i class="fa fa-refresh fa-spin"></i>					  
                        </div>
                     </div>
                  </div>
               </div>
               <!-- /.box-body -->              
            </div>
            <!-- /.box -->            
         </div>
         <!-- /.col -->          
      </div>
      <!-- /.row -->        
   </section>
   <!-- /.content -->      
</div>
<!-- /.content-wrapper -->