 
  
  <!-- Content Wrapper. Contains page content -->
   
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Categorywise Stock Report
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Account Reports</a></li>
            <li class="active">Categorywise Stock Report</li>
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
									    <label class="trans_centre">Branch</label>
										<select id="branch_select" class="form-control branch_filter"></select>
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
										<button type="button" id="categorywise_stock_search" class="btn btn-info" style="margin-left:0px;">Search</button>   
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
								      
									  	  <table id="categorywise_stock_list" class="table table-bordered table-striped text-center">
                							<thead>
                							  <tr>
											        <th rowspan="2">Category</th>
													<th colspan="4">Opening</th>
													<th colspan="4">Inward</th>
                    							    <th colspan="4">Outward</th>
													<th colspan="4">Closing</th>
                							  </tr>
                							  <tr>
                							      <th>Pcs</th>
                							      <th>Gross Wt</th>
                							      <th>Net Wt</th>
                							      <th>Value</th>
                							      
                							      <th>Pcs</th>
                							      <th>Gross Wt</th>
                							      <th>Net Wt</th>
                							      <th>Value</th>
                							      
                							      
                							      <th>Pcs</th>
                							      <th>Gross Wt</th>
                							      <th>Net Wt</th>
                							      <th>Value</th>
                							      
                							      <th>Pcs</th>
                							      <th>Gross Wt</th>
                							      <th>Net Wt</th>
                							      <th>Value</th>
                							  </tr>
                		                    </thead> 
            		                    <tbody></tbody>
										<tfoot>
											<tr style="font-weight: bold; color:red">
											    <td style="text-align:left">Total : </td>
											    <td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
												<td style="text-align:right"></td>
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
      

