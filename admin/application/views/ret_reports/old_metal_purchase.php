  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Reports
			 <small>Old Metal Purchase</small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Old metal purchase</li>
          </ol>
        </section>

        <!-- Main content -->
     <style>
       /* Force Select2 single-select to match Bootstrap form-control height (34px) */
       #oldmetal_filter_bar .select2-container--default .select2-selection--single {
         height: 34px !important;
         border: 1px solid #ccd0d4 !important;
         border-radius: 4px !important;
       }
       #oldmetal_filter_bar .select2-container--default .select2-selection--single .select2-selection__rendered {
         line-height: 34px !important;
         padding-left: 10px !important;
       }
       #oldmetal_filter_bar .select2-container--default .select2-selection--single .select2-selection__arrow {
         height: 32px !important;
       }
     </style>
     <section class="content">
          <div class="row">
            <div class="col-xs-12">
               
               <div class="box box-primary">
			    <div class="box-header with-border">
                  <h3 class="box-title">Old Metal Purchase List</h3>  <span id="total_count" class="badge bg-green"></span>  
                 
                </div>
                 <div class="box-body">  
                   <div class="row">
                     <div class="col-md-12">
                       <div style="background:#f8f9fb; border:1px solid #e0e4ea; border-radius:6px; padding:16px 20px; margin-bottom:10px;" id="oldmetal_filter_bar">
                         <div style="display:flex; align-items:flex-end; flex-wrap:nowrap; gap:12px;">

                           <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
                           <div style="flex:1.2; min-width:0;">
                             <label style="font-size:11px; font-weight:700; color:#333; margin-bottom:4px; display:block; white-space:nowrap;">Select Branch</label>
                             <select id="branch_select" class="form-control ret_branch" style="width:100%;"></select>
                           </div>
                           <?php }else{?>
                           <input type="hidden" id="branch_filter" value="<?php echo $this->session->userdata('id_branch') ?>">
                           <input type="hidden" id="branch_name"   value="<?php echo $this->session->userdata('branch_name') ?>">
                           <?php }?>

                           <div style="flex:1; min-width:0;">
                             <label style="font-size:11px; font-weight:700; color:#333; margin-bottom:4px; display:block; white-space:nowrap;">Report Type</label>
                             <select id="oldmetal_report_type" class="form-control">
                               <option value="1">Summary</option>
                               <option value="2" selected>Detailed</option>
                               <option value="3">Bill Wise</option>
                               <option value="4">Employee Wise</option>
                             </select>
                           </div>

                           <div class="emp_div" style="flex:1; min-width:0; display:none;">
                             <label style="font-size:11px; font-weight:700; color:#333; margin-bottom:4px; display:block; white-space:nowrap;">Select Employee</label>
                             <select id="employee_select" class="form-control" multiple style="width:100%;"></select>
                           </div>

                           <div style="flex:1.2; min-width:0;">
                             <label style="font-size:11px; font-weight:700; color:#333; margin-bottom:4px; display:block; white-space:nowrap;">Select Metal</label>
                             <select id="metal" class="form-control" multiple style="width:100%;"></select>
                           </div>

                           <div style="flex:1.2; min-width:0;">
                             <label style="font-size:11px; font-weight:700; color:#333; margin-bottom:4px; display:block; white-space:nowrap;">Select Category</label>
                             <select id="old_metal_cat_filter" class="form-control" multiple style="width:100%;"></select>
                           </div>

                           <div style="flex:1.2; min-width:0;">
                             <label style="font-size:11px; font-weight:700; color:#333; margin-bottom:4px; display:block; white-space:nowrap;">Select Counter</label>
                             <select id="counter_sel" class="form-control" multiple style="width:100%;"></select>
                           </div>

                           <div style="flex:1.6; min-width:0;">
                             <label style="font-size:11px; font-weight:700; color:#333; margin-bottom:4px; display:block; white-space:nowrap;">Date Range</label>
                             <?php $fromdt = date("d/m/Y"); $todt = date("d/m/Y"); ?>
                             <div style="display:flex; gap:6px; align-items:center;">
                               <input type="text" class="form-control dateRangePicker" id="dt_range" placeholder="From Date - To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="" style="flex:1; min-width:0;">
                               <button type="button" id="old_metal_search" class="btn btn-info" style="white-space:nowrap; flex-shrink:0;">Search</button>
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
			  
                  <div class="table-responsive detailed_report">
	                 <table id="old_metal_report" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
                            <th width="10%">Branch</th>
                            <th width="10%">Bill Date</th>
                            <th width="15%">Bill No</th>
							<th width="10%">Customer</th>
							<th width="10%">Address</th>
							<th width="10%">State</th>
							<th width="10%">GST No</th>
							<th width="10%">Mobile</th>
							<th width="10%">Counter</th>
                            <th width="10%">Ornament Category</th>
							<th width="10%">Product</th>
                            <th width="5%">Gross Wgt</th>                                       
                            <th width="5%">Stone Wgt</th> 
                            <th width="5%">Dia Wgt</th>
                            <th width="5%">Dust Wgt</th>
                            <th width="5%">Pure Wgt</th>
                            <th width="5%">Wastage</th>
                            <th width="5%">Net Wgt</th> 
                            <th width="5%">Touch</th> 
                            <th width="5%">Purity %</th> 
                            <th width="10%">Rate</th>
                            <th width="10%">Value</th>
                            <th width="10%">Refund Amount</th>
                            <th width="10%">Status</th>
                            <th width="10%">Customer</th>
                            <th width="5%">Esti No</th>
                            <th width="5%">Sales Man</th>
							<th width="5%">Remark</th>
                            </tr>
	                    </thead> 
	                    <tbody> 
	                    </tbody>
	                 </table>
                  </div>

				  <div class="table-responsive bill_wise_report" style="display:none;">
	                 <table id="old_metal_bill_report" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
                             <th style="width:10%">Branch</th>
                            <th style="width:10%">Bill Date</th>
                            <th style="width:20%">Bill No</th>
							<th style="width:10%">Customer</th>
							<th style="width:10%">Address</th>
							<th style="width:10%">Mobile</th>
                            <th style="width:5%">Gross Wgt</th>                                       
                            <th style="width:5%">Stone Wgt</th> 
                            <th style="width:5%">Dia Wgt</th>
                            <th style="width:5%">Dust Wgt</th>
                            <th style="width:5%">Pure Wgt</th>
                            <th style="width:5%">Wastage</th>
                            <th style="width:5%">Net Wgt</th> 
                            <th style="width:10%">Value</th>
                            <th style="width:10%">Exchange Value</th>
                            <!-- <th style="width:10%">Advance Amount</th> -->
                            <th style="width:10%">Cash Amount</th>
                            <th style="width:10%">Cheque</th>
                            <th style="width:10%">Net Banking</th>
                            </tr>
	                    </thead> 
	                    <tbody> 
	                    </tbody>
	                 </table>
                  </div>
                  
                  <div class="table-responsive summary_report" style="display:none;">
	                 <table id="old_metal_detailed_report" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr>
	                        <th width="10%">Bill Date</th>
	                        <th width="5%">Branch</th>
	                        <th width="5%">Category</th>
	                        <th width="10%">Gross Wgt</th>                                       
                            <th width="10%">Net Wgt</th> 
                            <th width="10%">Dia Wgt</th> 
                            <th width="10%">Amount</th>
	                      </tr>
	                    </thead> 
	                    <tbody>  </tbody>
	                    <tfoot style="font-weight:bold;">
						<tr style="color:red">
							<td style="text-align:right"></td>
							<td style="text-align:right"></td>
							<td style="text-align:right"></td>
							<td style="text-align:right"></td>
							<td style="text-align:right"></td>
							<td style="text-align:right"></td>
							<td style="text-align:right"></td>
						</tr></tfoot>
	                 </table>
                  </div>

                  <div class="table-responsive emp_wise_report" style="display:none;">
	                 <table id="old_metal_emp_report" class="table table-bordered table-striped text-center">
	                    <thead>
	                      <tr style="background-color:#FFFFFF;">
	                        <th>S.no</th>
	                        <th>Item Name</th>
	                        <th>Category</th>
	                        <th>Bill No</th>
	                        <th>Trans Date</th>
	                        <th>Cash Counter</th>
	                        <th>Gwt</th>
	                        <th>Less Wt</th>
	                        <th>Dust Wt</th>
	                        <th>Wast Per</th>
	                        <th>Was Wt</th>
	                        <th>Nwt</th>
	                        <th>Purity</th>
	                        <th>Rate</th>
	                        <th>Stn Wt</th>
	                        <th>D Wt</th>
	                        <th>Amount</th>
	                        <th>Emp Name</th>
	                        <th>Estimate No</th>
	                        <th>Touch</th>
                            </tr>
	                    </thead> 
	                    <tbody></tbody>
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
      

