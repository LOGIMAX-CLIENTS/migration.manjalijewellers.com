<style>
   @media print 
   {    
        table tr td.sales
        { 
          font-weight:bold;
        }
        @media print {
            a[href]:after {
            content: "";
            }
        }
    }

</style> 
<!-- Content Wrapper. Contains page content -->

<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">

		<h1>

		GSTR1 REPORT

		</h1>

	</section>

			<!-- Main content -->
	<section class="content">

		<div class="row">
			
			<div class="col-xs-12">
						
				<div class="box box-primary">
							
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

														<select id="branch_select" class="form-control ret_branch" style="width:100%;" ></select>

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
											
												<div class="form-group">
												
													<label>Select Type</label>
													
													<div class="input-group">
													
														<select class="form-control" id="report_select" style="width:100%;">
													
															<option value="b2b" selected>B2b</option>

															<option value="b2cs_others">B2CS Others</option>

															<option value="b2cl">B2CL</option>

															<option value="b2b_return">Cr notes-regd. persons</option>

															<option value="b2c_return">Cr notes-B2CL</option>

															<option value="hsn_summary">HSN Summary</option>

															<option value="export">Export</option>

															<option value="mon_doc_smry">Monthly Document Summary</option>

														</select>
														
													</div>
											
												</div>
											
											</div>
											
											<div class="col-md-2"> 
											
												<label></label>
												
												<div class="form-group">
												
													<button type="button" id="gst_r1_search" class="btn btn-info">Search</button>   
													
												</div>
											
											</div>
										
										</div>
									
									</div>
								
								</div> 
							
							</div> 
						
						</div> 
						
						<!--<div class="row" style="padding-left: 50px;">
							
							<div class="col-md-1">
									
								<div class="form-group">
									
									<button id="btnExport" onclick="fn_GST_R1_Excel_Report('1');" class="btn btn-success "><i class="fa fa-file-excel-o"></i>&nbsp;</button>
									
								</div>
								
							</div>

						</div>-->
								
						<div id="cash_abstract">
							
							<div class="box box-info sales_details" >
										
								<div class="box-header with-border">
										
									<div class="box-tools pull-right">
										
										<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
								
									</div>
								
								</div>
								
								<div class="box-body">
										
									<div class="row">
									
										<div class="box-body">
									
											<div class="table-responsive gstR1_ReportWrapper">
												
											</div>
											
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