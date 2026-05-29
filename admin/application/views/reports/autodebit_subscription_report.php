  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
         Auto Debit Subscription
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Auto Debit Subscription</a></li>
            <!--<li class="active">reg_list</li>-->
          </ol>
        </section>
        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">           
              <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Auto Debit Subscription Status List</h3> <span class="badge bg-green" id=""></span>
                </div><!-- /.box-header -->
                <div class="box-body">    
	            <?php 
				  $attributes = array('id' => 'settled_payments');
				 ?>
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
	            <div class="row">
	                	<!--<div class="col-md-2">
							<div class="form-group">
							   <button class="btn btn-default btn_date_range" id="payment-dt-btn"> 
								<span  style="display:none;" id="payment_list1"></span>
								<span  style="display:none;" id="payment_list2"></span>
								<i class="fa fa-calendar"></i> Date range picker
								<i class="fa fa-caret-down"></i>
								</button>
							</div>					
						</div>-->
						
							<div class="col-md-3">
					   <?php if($this->session->userdata('branch_settings')==1){?>
						
							<div class="form-group" style="" >
								<label>Branch &nbsp;&nbsp;</label>
								<select id="branch_select" class="form-control" style="width:150px;" ></select>
								<input id="id_branch" name="scheme[id_branch]" type="hidden" value=""/>
							</div>
						<?php }?>
	                </div>
						
	               <div class="col-md-2">
                               <div class="form-group">
                              <label>Search by mobile</label>
                              <input type="text" placeholder="Mobile Number" class="form-control" name="" id="mobilenumber" >
                              <input type="hidden" name="id_customer" id="id_customer"/> 
                            </div>
                    </div> 
                   
	            </div>
	           
                
                <div class="table-responsive">
                  <table id="autodebit_subscription" class="table table-bordered table-striped dataTable text-center grid" >
                  <thead>
				<tr>
                        <th>#</th>
                        <th>Branch</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>A/C Name</th>
                        <th>A/C No.</th>
                        <th>Subscription Status</th>
                        <th>Last Updated On</th>
                        
                      </tr>
                    </thead>
                    <tbody>
                       </form>
                    </tbody>
               <!--  <tfoot>
                      <tr >
                         <td colspan="8"> <p style="text-align:left"></p></td>
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
