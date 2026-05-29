  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Old Metal Analyse</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Old metal Analyse</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Old Metal Purchase List</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                  <div class="row">
				  <div class="col-md-offset-2 col-md-10">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
					   <div class="row">
					   	  <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
		                  <div class="col-md-2"> 
		                     <div class="form-group tagged">
		                       <label>Select Branch</label>
									<select id="branch_select" class="form-control" style="width:100%;" multiple></select>
									<input type="hidden" id="id_branch">
		                     </div> 
		                  </div> 
						    <?php }else{?>
		                     <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
		                     <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
		                  <?php }?>
		                  <div class="col-md-2">
		                     <div class="form-group">
		                        <label>Select Metal</label>
									<select id="filter_metal" class="form-control">
									<option value=""></option>
									<option value="0">All</option>
									<option value="1">Gold</option>
									<option value="2">Silver</option>
									</select>
		                     </div>
		                  </div>
		                  <div class="col-md-2">
		                     <div class="form-group">
		                        <label>Select Category</label>
									<select id="old_metal_type" class="form-control" style="width:100%;" multiple></select>
									<input type="hidden" id="id_old_metal_type">
		                     </div>
		                  </div>
						  <div class="col-md-3"> 
		                     <div class="form-group">    
		                   		  <label>Date</label> 
		                   		  <?php   
									$fromdt = date("d/m/Y", strtotime('-0 days'));
									$todt = date("d/m/Y");
								  ?>
		                   		  <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  
		                     </div>
		                  </div> 
						   <div class="col-md-3 pull-right"> 
						   	 <label></label>
		                     <div class="form-group">
				                <button type="button" id="old_metal_analyse_search" class="btn btn-info">Search</button>   
		                    </div>
		                  </div>
						   </div>
	                   </div> 
	                  </div> 
                   </div> 
                
                </div>
                <p></p>
                
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
			  
                  <div class="table-responsive">
	                 <table id="old_metal_report" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="10%">Bill Date</th>
	                        <th width="10%">Bill No</th>
	                        <th width="10%">Gross Wgt</th>                                       
	                        <th width="10%">Stone Wgt</th>                                       
							<th width="10%">Dust Wgt</th>
							<th width="10%">Dia Wgt</th>
							<th width="10%">Final Wgt</th>
							<th width="10%">Wastage</th>
                            <th width="10%">Net Wgt</th>
                            <th width="10%">Purity %</th> 
                            <th width="10%">Rate</th>
                            <th width="10%">Rate(24 CT)</th>
                            <th width="10%">Amount</th>
                            <th width="10%">Esti No</th>
							<th width="10%">Employee Name</th>
	                      </tr>
	                    </thead> 
	                    <tbody> 
	                    </tbody>
	                 </table>
                  </div>
                </div><!-- /.box-body -->
                <div class="overlay" style="display:none">
				  <i class="fa fa-refresh fa-spin"></i>
				</div>
            
            </div><!-- /.col -->
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      


