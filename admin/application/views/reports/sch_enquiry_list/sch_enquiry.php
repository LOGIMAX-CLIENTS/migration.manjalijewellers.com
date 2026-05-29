  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
         Scheme Enquiry 
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Scheme Enquiry</a></li>
            <!--<li class="active">reg_list</li>-->
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Customer Scheme Enquiry List</h3> <span class="badge bg-green" id="total_sch_enq"></span>
                </div><!-- /.box-header -->
                <div class="box-body">    
	            <?php 
				  $attributes = array('id' => 'settled_payments');
				//  echo form_open('payment/update_status',$attributes);
				 ?>
            	 <div class="row">
                
	                
						<div class="col-md-2 pull-right">
									    <br/>
										<div class="form-group">
										   <button class="btn btn-default btn_date_range" id="sch_enq-dt-btn"> 
											<span  style="display:none;" id="sch_enq_list1"></span>
											<span  style="display:none;" id="sch_enq_list2"></span>
											<i class="fa fa-calendar"></i> Date range picker
											<i class="fa fa-caret-down"></i>
											</button>
										</div>					
									</div> 	
			
				 </div> 
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
                  <table id="sch_enquiry_list" class="table table-bordered table-striped dataTable text-center grid" >
                  <thead>
				<tr>
                        <th>ID</th>
                        <th>Customer ID</th>
                        <th>Mobile</th>
                        <th>Intrested Amount (&#8377;)</th>
                        <th>Message</th>
                        <th>Intrested Weight (g)</th>
                        <th>Enquiry Dtae</th>
					 
                      </tr>
                    </thead>
                    <tbody>
                       </form>
                    </tbody>
               <!--  <tfoot>
                      <tr >
                         <td colspan="10"> <p style="text-align:left"></p></td>
                      </tr>
                    </tfoot> -->
                  </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
       

<style type="text/css">
.popover1{
    width:230px;
    height:330px;    
}
.trans tr{
	 width:50%;
    height:50%;
	font-size:15px;
	
}
</style>