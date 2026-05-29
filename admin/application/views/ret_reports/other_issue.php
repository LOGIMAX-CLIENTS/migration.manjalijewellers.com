  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Other Issue</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Other Issue</li>
          </ol>
        </section>
        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Other Issue</h3>  <span id="total_count" class="badge bg-green"></span>  
                </div>
                 <div class="box-body">  
                  <div class="row">
				  	<div class="col-md-12">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
						   <div class="row">
								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
								<div class="col-md-2"> 
									<div class="form-group tagged">
										<label>Select Branch</label>
										<select id="branch_select" class="form-control branch_filter"></select>
									</div> 
								</div> 
								<?php }else{?>
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
								<?php }?>
								<div class="col-md-2" style="display:<?= ($date_visible==0 ? 'none':'block' ) ?>"> 
									<div class="form-group">    
										<label>Date</label> 
										<?php   
											$fromdt = date("d/m/Y");
											$todt = date("d/m/Y");
									    ?>
			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
									</div> 
								</div> 
								<div class="col-md-2">
                                    <label for="">Stock Type</label>
                                    <select id="stock_type" class="form-control" style="width:100%;">
                                        <option value="0" selected="">All</option>
                                        <option value="1">Tagged</option>
                                        <option value="2">Non Tagged</option>
                                        <option value="3">Accounts Stock</option>
                                    </select>
                                </div>
								<div class="col-md-2">
                                    <label for="">Report Type</label>
                                    <select id="issue_report_type" class="form-control" style="width:100%;">
                                        <option value="1"selected="" >Detailed</option>
                                        <option value="2">Summary</option>
                                    </select>
                                </div>
								<div class="col-md-2 issue_group" style="display:none">
                                    <label for="">Group by</label>
                                    <select id="issue_group_by" class="form-control" style="width:100%;">
                                        <option value="1">Metal</option>
                                        <option value="2">Section</option>
                                    </select>
                                </div>

								<div class="col-md-2"> 
									<label>Select Metal</label>
									<select id="metal" class="form-control" style="width:100%;"></select>
								</div>

								<div class="col-md-2 karigar"> 
									<label>Select Karigar</label>
									<select id="karigar" class="form-control" style="width:100%;"></select>
								</div>

							</div>
						 </div>
						 <div class="row">
						 <div class="col-md-2"> 
									<label>Select Product</label>
									<select id="prod_select" class="form-control" style="width:100%;"></select>
								</div>
				                 <div class="col-md-2"> 
									<label>Select Design</label>
									<select id="des_select" class="form-control" style="width:100%;"></select>
							    	</div>  
								<div class="col-md-2"> 
									<label>Select Sub Design</label>
									<select id="sub_des_select" class="form-control" style="width:100%;"></select>
								</div>
				            <div class="col-md-2"> 
									<label>Select Section</label>
									<select id="section_select" class="form-control" style="width:100%;"></select>
								</div>
								<!-- <div class="col-md-2"> 
    									<label>Tag Code</label>
    									<div class="form-group">
    									    <input type="text" class="form-control" id="tag_number" placeholder="Enter Tag Code">
    									</div>
    								</div> -->
				                 <div class="col-md-2"> 
									<label></label>
									<div class="form-group">
										<button type="button" id="other_issue_search" class="btn btn-info">Search</button>   
									</div>
								</div>
								</div> 
								</div> 
                             </div> 
				           </div> 
            <!-- 	    </div> 
	                  </div> 
                   </div>  -->
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
						<div class="box-header with-border">
						  <h3 class="box-title">Branch Transfer Details</h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="box-body">
								   <div class="table-responsive detailed">
									  <table id="other_issue_list" class="table table-bordered table-striped text-center">
										<thead>
										  <tr style="text-transform:uppercase;"> 
										  <tr style="text-transform:uppercase;"> 
										    <th width="10%">Section</th> 
										    <th width="10%">Product</th>
											<th width="10%">Design</th>  
											<th width="10%">Sub Design</th>  
											<th width="10%">Karigar</th>
										    <th width="10%">Tag No</th>
										    <th width="10%">Pieces</th>
										    <th width="10%">Gross Wt</th>
										    <th width="10%">Net Wt</th> 
											<th width="10%">Dia Wt</th> 
											<th width="10%">Employee</th> 
										    <th width="10%">From Branch</th>
										    <th width="10%">To Branch</th>  
											<th width="10%"> Remark </th>  
										  </tr>
										  </tr>
					                    </thead> 
					                    <tbody>
										</tbody>
									 </table>
								  </div>
								  <div class="box-body">
								   <div class="table-responsive summary" style ="display:none">
									  <table id="other_issue_summary_list" class="table table-bordered table-striped text-center">
										<thead>
										  <tr style="text-transform:uppercase;"> 
										    <th width="10%">Metal/Section</th>
											<th width="10%">Karigar</th>
										    <th width="10%">Pieces</th>
										    <th width="10%">Gross Wt</th>
										    <th width="10%">Net Wt</th> 
											<th width="10%">Dia Wt</th> 
										    <th width="10%">From Branch</th>
										    <th width="10%">To Branch</th>  
											<th width="10%">Remark </th>  
										  </tr>
					                    </thead> 
					                    <tbody></tbody>
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