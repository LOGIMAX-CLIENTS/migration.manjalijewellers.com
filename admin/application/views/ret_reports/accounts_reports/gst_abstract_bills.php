 <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<style> 
	.alignRight {
		text-align: right !important;
	}

	@media print {
		html,
		body {
		height: auto;
		width: 380mm;
		margin: 0 !important;
		padding: 0 !important;
		overflow: hidden;
		}
     }
     
	</style>
	<section class="content-header">
	  <h1>
	    Reports
		 <small>GST Bills</small>
	  </h1>
	  <ol class="breadcrumb">
	    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	    <li><a href="#">Retail Reports</a></li>
	    <li class="active">GST Abstract</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content">
	  <div class="row">
	    <div class="col-xs-12">
	       <div class="box box-primary">
		    <div class="box-header with-border">
	          <h3 class="box-title">GST Abstract</h3>  <span id="total_count" class="badge bg-green"></span>   
	        </div>
	         <div class="box-body">  
	          <div class="row">
			  	<div class="col-md-10">  
	              <div class="box box-default">  
	               <div class="box-body">  
					   <div class="row">
							<div class="col-md-2"> 
								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
								<div class="form-group">
									<label>Select Branch</label>
									<select id="branch_select" class="form-control branch_filter" style="width:100%;"></select>
								</div> 
								<?php }else{?>
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 
									<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 
								<?php }?> 
							</div>  
							<div class="col-md-2">
							    <div class="form-group">
							        <label>Select Metal</label>
							        <select id="metal" class="form-control" ></select>
							    </div>
							</div>
							
							<div class="col-md-2">
							    <div class="form-group">
							        <label>Select Category</label>
							        <select id="category" class="form-control" multiple></select>
							    </div>
							</div>
							<div class="col-md-2"> 
    							 <div class="form-group">
        		                    <div class="input-group">
        		                        <label>Select Bill Type</label>
        		                       <select class="form-control" style="width:100%;" id="report_type">
        		                           <option value="0" selected>All</option>
        		                           <option value="1">Unregistered Bills</option>
        		                           <option value="2">Registered Bills</option>
        		                       </select>
        		                    </div>
        		                 </div><!-- /.form group -->
							</div>

							<div class="col-md-2"> 
								<div class="form-group">
								<label>Bill Type</label>
								<select class="form-control" style="width:100%;" id="bill_report_type">
								<option value="0" selected>Summary</option>
								<option value="1">Detailed</option>
								</select>
								</div>
							</div>


							<!-- <div class="col-md-2"> 
								<div class="form-group">
								<label>Bill Amount</label>
								<select class="form-control" style="width:100%;" id="bill_report_type">
								<option value="0" selected>All</option>
								<option value="1" >Below 1 lakh</option>
								<option value="2">Above 1 lakh</option>
								</select>
								</div>
							</div> -->

							
							<div class="col-md-2" style="display:none"> 
    							 <div class="form-group">
        		                    <div class="input-group">
        		                        <label>GST Filter</label>
        		                       <select class="form-control" style="width:100%;" id="gst_filter">
        		                           <option value="0" selected>All</option>
        		                           <option value="1">Overseas Bills</option>
        		                           <option value="2">Inter State Bills</option>
        		                           <option value="3">Intra State Bills</option>
        		                       </select>
        		                    </div>
        		                 </div><!-- /.form group -->
							</div>
							
							<div class="col-md-2">
								<div class="form-group">
									<div class="input-group">
										<br>
										<button class="btn btn-default btn_date_range"
											id="rpt_date_picker">

											<i class="fa fa-calendar"></i> Date range picker
											<i class="fa fa-caret-down"></i>
										</button>
										<span style="display:none;" id="rpt_from_date"></span>
										<span style="display:none;" id="rpt_to_date"></span>
									</div>
								</div><!-- /.form group -->
							</div>

							
						                  
							<div class="col-md-2"> 
								<label></label>
								<div class="form-group">
									<button type="button" id="gst_abstract_bill_search" class="btn btn-info">Search</button>   
								</div>
							</div>
						</div>
					 </div>
	               </div> 
	              </div> 
	           </div> 
	        
			 
	           <div class="row">
	               <div class="col-md-12">
	               	<div class="table-responsive">
	                 <table id="gst_abstract_list" class="table table-bordered table-striped text-center">
	                    <thead>
						  <tr>
						    <th>Category</th>
							<th>HSN Code</th>
						    <th>Bill No</th>
							<th>Bill Date</th>
						    <th>Pcs</th>
						    <th>Gwt(Grams)</th>
						    <th>NWT(Grams)</th>
						    <th>Taxable Amount(Rs)</th>
						    <th>SGST</th>
						    <th>CGST</th>
						    <th>IGST</th>
						    <th>GST</th>
						    <th>Total Amount(Rs)</th>
							<th>Name</th>
							<th>GSTIN</th>
							<th>PAN</th>
						  </tr>
	                    </thead> 
	                     <tbody></tbody>
	                     <tfoot><tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></tfoot>
	                 </table>
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
      

