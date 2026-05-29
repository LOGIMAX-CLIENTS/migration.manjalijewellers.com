 
  
  <!-- Content Wrapper. Contains page content -->
   
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Categorywise Branch Transfer Report
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Account Reports</a></li>
            <li class="active">Categorywise Branch Transfer Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                    <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
								<div class="col-md-2"> 
									<div class="form-group tagged">
									    <label class="trans_centre">To Centre</label>
										<select id="branch_select" class="form-control branch_filter"></select>
									</div> 
								</div> 
								<div class="col-md-2"> 
									<div class="form-group tagged">
									    <label>Cost Centre</label>
										<select id="branch_select_to" class="form-control branch_filter"></select>
									</div> 
								</div> 
								<?php }else{?>
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
									<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
								<?php }?> 
								<div class="col-md-2"> 
									<div class="form-group">    
										<?php   
											$fromdt = date("d/m/Y");
											$todt = date("d/m/Y");
									    ?>
									    <br />
			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
									</div> 
								</div>
								
								<div class="col-md-1"> 
								    <label></label>
									<select id="transtype" class="form-control" style="width:100%;">
									    <option value="1" selected> Issue </option>
									    <option value="2"> Receipt </option>
									</select>
								</div>
								
								
								<div class="col-md-2"> 
								    <label></label>
									<select id="selecttransitemtypes" class="form-control" style="width:100%;" multiple>
									    <option value="0" selected> - All - </option>
									    <option value="1"> Tag </option>
									    <option value="2"> Non Tag </option>
									    <option value="3"> Sales Return</option>
									    <option value="4"> Partly Sale</option>
									    <option value="5"> Old Metal</option>
									</select>
								</div>
								
							<!-- 	<div class="col-md-1"> 
								    <label></label>
									<select id="transitemtype" class="form-control" style="width:100%;">
									    <option value="0" selected> - All - </option>
									    <option value="1"> Tag </option>
									    <option value="2"> Non Tag </option>
									    <option value="3"> Purchase Items </option>
									</select>
								</div>
								<div class="col-md-1 disp-purchasetype" style="display:none;"> 
								    <label></label>
									<select id="transpurchasetype" class="form-control" style="width:100%;">
									    <option value="0" selected> - All - </option>
									    <option value="1"> Old Metal </option>
									    <option value="2"> Sales Return </option>
									    <option value="3"> Partly Sale </option>
									</select>
								</div> -->
								
								<div class="col-md-2 other-category" style="display:none;"> 
								    <label></label>
									<select id="category" class="form-control" style="width:100%;"></select>
								</div>
								
								<div class="col-md-2 oldcategory" style="display:none;"> 
								    <label></label>
									<select id="oldcategory" class="form-control" style="width:100%;"></select>
								</div>
								
								
								<div class="col-md-1"> 
									<div class="form-group">
									    <br />
										<button type="button" id="categorywise_bt_search" class="btn btn-info" style="margin-left:0px;">Search</button>   
									</div>
								</div>
                 
                </div>
				
                 <div class="box-body">  
				   <div class="row">
						<div class="col-xs-12">
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
						</div>
				   </div>
				    
				   	<div class="box box-info stock_details">
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive">
								      
									  	  <table id="categorywise_bt_list" class="table table-bordered table-striped text-center">
                							<thead>
                							  <tr>
											        <th>Category</th>
													<th>From Branch</th>
													<th>To Branch</th>
                    							    <th>Piece</th>
													<th>Gwt</th>
													<th>Netwt</th>
													<th>Diawt</th>
													<th>Amount</th>
													<th>BT Code</th>
                    	                           	<th>BT Id</th>
                    	                           	<th>Approved On</th>
                    	                           	<th>Downloaded On</th>
                							  </tr>
                		                    </thead> 
            		                    <tbody></tbody>
										<tfoot>
											<tr style="color:red">
											    <th style="text-align:right"></th>
											    <th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
												<th style="text-align:right"></th>
											</tr>
										</tfoot>
									 </table>
								  </div>
								</div> 
							</div> 
						</div>
					</div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      

