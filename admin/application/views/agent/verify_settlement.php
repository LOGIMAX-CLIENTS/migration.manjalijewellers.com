  <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Agent 

            <small></small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Agent</a></li>

            <li class="active">Approval List</li>

          </ol>

        </section>



        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

           

              <div class="box">

                <div class="box-header">

                  <h3 class="box-title">Agent Settlement Approval</h3> <span class="badge bg-green" id="total_payments"></span>

               

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

	            <?php 

				  $attributes = array('id' => 'pay_status_form');

				//  echo form_open('payment/update_status',$attributes);

				 ?>

                <div class="row">

                	
			</div> 
				
				<div class="row">
	                 <div class="col-md-12">
	                     	       	
						<div class="error-msg">
                	</div>
                	<div class="col-md-5">

                	<div class="btn-group" data-toggle="buttons">

				        <label class="btn btn-success">

				            <input type="radio" name="pay_status" value="1"><i class="icon fa fa-check"></i> Approve

				        </label>

				        <label class="btn btn-danger">

				            <input type="radio" name="pay_status" value="2"><i class="icon fa fa-remove"></i> Reject

				        </label>

						 <label class="btn bg-aqua">

				            <input type="radio" name="pay_status" value="3" checked=""><i class="icon fa fa-reply"></i> Settled

				        </label>
						
					 </div>

                </div>		
							   
	                 	<div class="col-md-2" style="margin-top:20px">
	                 		         	 <!-- Date and time range -->
		                  <div class="form-group">
		                    <div class="input-group">
		                        <button class="btn btn-default btn_date_range" id="settlement-dt-btn">
							    <span style="display:none;" id="settle_list1"></span>
							    <span style="display:none;" id="settle_list2"></span>
		                        <i class="fa fa-calendar"></i> Date range picker
		                        <i class="fa fa-caret-down"></i>
		                      </button>
		                    </div>
		                 </div><!-- /.form group -->
		               </div>
		               
		               <div class="col-md-3">
										<div class="form-group">
										   <label>Select Settlmts</label>
											<select id="settle_Select" class="form-control" style="width:150px;">
											   <!-- <option value='0' selected>All</option>-->
											     <option value=1>Settled</option>
											     <option value=2>UnSettled</option>
											</select>
											<input id="id_settled" name="scheme[id_settled]" type="hidden" value=""/>
										</div>
							    </div>
		               
							<div class="col-md-3">
							         <?php if($this->session->userdata('branch_settings')==1){?>				
										<div class="form-group" style="    margin-left: 50px;">
										   <label>Select Branch &nbsp;</label>
											<select id="branch_select" class="form-control" style="width:150px;"></select>
											<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
										</div>
										
										
							       <?php }?>
							    </div>	
							    
		                 </div>
	                 </div></br>

				  <div class="table-responsive">

                  <table id="online_payments" class="table table-bordered table-striped table-condensed text-center">

                    <thead>

                      <tr>

	                        <th><label class="checkbox-inline"><input type="checkbox" id="select_all" name="select_all" value="all"/>All</label></th>

	                       
	                        
	                        <th>Agent</th>
	                        
	                        <th>Referal Code</th> 

                          <th>Cash Point</th>

	                        <th>Transaction Date</th>

	                        <th>Scheme Acc No.</th>
	                        <th>Transaction Type</th>

	                        <th>Status</th>

	                       

	                      </tr>

                    </thead>

                    <tbody>

                     

                    

                    </tbody>

                 

                  </table>
                  
                  

                  </div>

                </div><!-- /.box-body -->				 
               <!-- <div class="overlay" style="display:none">				 <i class="fa fa-refresh fa-spin"></i>				 </div> -->
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