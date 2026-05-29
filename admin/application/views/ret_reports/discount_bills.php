 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
	    Reports
		 <small>Discount Bills</small>
	  </h1>
	  <ol class="breadcrumb">
	    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
	    <li><a href="#">Retail Reports</a></li>
	    <li class="active">Discount Bills</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content">
	  <div class="row">
	    <div class="col-xs-12">
	       <div class="box box-primary">
		    <div class="box-header with-border">
	          <h3 class="box-title">Bills Discounts List</h3>  <span id="total_count" class="badge bg-green"></span>
	        </div>
	         <div class="box-body">
	          <div class="row">
			  	<div class="col-md-12">
	              <div class="box box-default">
	               <div class="box-body">
					   <div class="row">
							<div class="col-md-2">
								<?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
								<div class="form-group tagged">
									<label>Select Branch</label>
									<select id="branch_select" class="form-control branch_filter"></select>
								</div>
								<?php }else{?>
									<input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>">
										<input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>">
								<?php }?>
							</div>
							<div class="col-md-2">
								<div class="form-group">
									<label>Bill Date</label>
									<?php
										$fromdt = date("d/m/Y", strtotime('-0 days'));
										$todt = date("d/m/Y");
								    ?>
		                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">
								</div>
							</div>
							<div class="col-md-2">
                            	<div class="form-group">
                            	    <label>Select Metal</label>
                            		<select id="metal" style="width:100%;"></select>
                            	</div>
                            </div>

            				<div class="col-md-2">
            					<div class="form-group">
            					    <label>Select Category</label>
            						<select id="category" style="width: 100%;"></select>
            					</div>
            				</div>
							 <div class="col-md-2">
								<div class="form-group">
								    <label>Select Product</label>
									<select id="prod_select" style="width: 100%;"></select>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
								    <label>Select Design</label>
									<select id="des_select" style="width: 100%;"></select>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
								    <label>Select Sub Design</label>
									<select id="sub_des_select" style="width: 100%;"></select>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
								    <label>Discount Type</label>
									<select id="is_otp_approved" style="width: 100%;">
								       <option value="" selected >All</option>
									   <option value="1"> OTP APPROVED DISCOUNT</option>
									   <!-- <option value="2"> NORMAL</option> -->
									</select>
								</div>
							</div>
							<div class="col-md-2">
								<label></label>
								<div class="form-group">
									<button type="button" id="disc_bill_search" class="btn btn-info">Search</button>
								</div>
							</div>
						</div>
					 </div>
	               </div>
	              </div>
	           </div>

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

	           <div class="row without_bill_detail" style="display: none">
	               <div class="col-md-12">
	               	<div class="table-responsive">
	                 <table id="disc_bill_list" class="table table-bordered table-striped text-center">
	                    <thead>
						  <tr>
						    <th>Customer</th>
						    <th>Mobile</th>
						    <th>Bill No</th>
						    <th>Bill Type</th>
						    <th>Bill Date</th>
						    <th>Discount</th>
						    <th>Bill Amount</th>
						  </tr>
	                    </thead>
	                     <tbody>
	                </tbody>

	                 </table>
	              </div>
	               </div>
	           </div>
	           <div class="row with_bill_detail" >
	               <div class="col-md-12">
	               	<div class="table-responsive">
	                 <table id="disc_bill_detail" class="table table-bordered table-striped text-center">
	                    <thead>
						  <tr>
						    <th>Branch</th>
						    <th>Bill Date</th>
						    <th>Bill No</th>
						    <th>Customer</th>
						    <th>Mobile</th>
						    <th>Tag No</th>
						    <th>Product</th>
						    <th>Design</th>
						    <th>Sub Design</th>
						    <th>Grs Wgt</th>
						    <th>Net Wgt</th>
						    <th>V.A(%)</th>
						    <th>MC</th>
						    <th>Item Cost</th>
						    <th>Discount</th>
						    <th>Discount %</th>
							<th>Is OTP Approved</th>
							<th>Approved By</th>
						    <th>Emp</th>
						    <th>Emp Code</th>
						  </tr>
	                    </thead>
	                     <tbody>
	                </tbody>
					<tfoot>
							<tr style="font-weight:bold;">
								<td></td>
								<td></td>
								<td></td>
								<td></td>

								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>

								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td></td>
								<td></td>

								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						 </tfoot>

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


