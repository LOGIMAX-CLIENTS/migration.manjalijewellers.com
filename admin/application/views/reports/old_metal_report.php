  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>OG Purchase Report</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">OG Purchase Report</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content report_sel">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">OG Purchase Report List</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                  <div class="row">
				  <div class="col-md-offset-2 col-md-8">  
	                  <div class="box box-default">  
	                   <div class="box-body">  
					   <div class="row">
					   	  <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
		                  <div class="col-md-3"> 
		                     <div class="form-group tagged">
		                       <label>Select Branch</label>
									<select id="branch_select" class="form-control ret_branch"></select>
		                     </div> 
		                  </div> 
						    <?php }else{?>
		                     <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
		                     <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
		                  <?php }?>
		                  
						  <div class="col-md-3"> 
		                      <div class="form-group">
            		                    <div class="input-group">
            		                        <br>
            		                       <button class="btn btn-default btn_date_range" id="rpt_payment_date">
            							    <span  style="display:none;" id="rpt_payments1"></span>
            							    <span  style="display:none;" id="rpt_payments2"></span>
            		                        <i class="fa fa-calendar"></i> Date range picker
            		                        <i class="fa fa-caret-down"></i>
            		                      </button>
            		                    </div>
            		                 </div><!-- /.form group -->
		                  </div> 
						   <div class="col-md-3"> 
						   	 <label></label>
		                     <div class="form-group">
				                <button type="button" id="old_metal_search" class="btn btn-info">Search</button>   
		                    </div>
		                  </div>
						   </div>
	                   </div> 
	                  </div> 
                   </div> 
                
                </div>
                <p></p>
                
				   <div class="table-responsive">
	                 <table id="old_metal_report" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="5%">Receipt Id</th>
	                        <th width="5%">Receipt Date</th>
	                        <th width="5%">Branch</th>
	                        <th width="10%">Customer</th>
	                        <th width="10%">Acc No</th>
	                        <th width="10%">Acc Name</th>
                            <th width="5%">Esti No</th>
                            <th width="5%">Esti Created BY</th>
	                        <th width="10%">Gross Wgt</th>                                       
                            <th width="10%">Net Wgt</th> 
                            <th width="10%">Amount</th>
                            <th width="10%">Payment By</th>
	                      </tr>
	                    </thead> 
	                    <tbody> </tbody>
	                    <tfoot><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tfoot>
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
      


