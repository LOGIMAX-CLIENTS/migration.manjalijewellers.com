<style>

  @media print {
    a[href]:after {
      content: "";
    }
  }

</style>

     <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

        <!-- Content Header (Page header) -->

        <section class="content-header">

          <h1>

            Reports

			 <small>Detailed Transactions Report</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">Detailed Transactions Repor</li>

          </ol>

        </section>

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               <div class="box box-primary">

			    <div class="box-header with-border">

                  <h3 class="box-title">Detailed Transactions Report</h3>  <span id="total_count" class="badge bg-green"></span>  

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

								<div class="col-md-2"> 

									<div class="form-group">    

										<label>Date</label> 

										<?php   

											$fromdt = date("d/m/Y");

											$todt = date("d/m/Y");

									    ?>

			                   		    <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  

									</div> 

								</div>

								<div class="col-md-2 detail_report"> 

									<label>Select Metal</label>

									<select id="metal" class="form-control" style="width:100%;" multiple></select>

								</div>

								

								<div class="col-md-2 detail_report"> 

									<label>Select category</label>

									<select id="category" style="width: 100%;"></select>

								</div>

								<!--<div class="col-md-2"> 

                                		<label>Select Section</label>

                                		<select id="section_select" class="form-control" style="width:100%;"></select>

                                </div>-->

								<div class="col-md-2 detail_report"> 

									<label>Select Product</label>

									<select id="prod_select" class="form-control" style="width:100%;"></select>

								</div>

								

							</div>

							<div class="row">                
                <div class="col-md-2">
                        <label>Report Type</label>
                        <select id="det_sales_report_type" class="form-control" style="width:100%;">
                          <option value="2" selected>Detailed</option>
                          <option value="3"> Summary</option>
                          <option value="1">Metal wise Summary</option>
                        </select>
                      </div>
                  
                <div class="col-md-2"> 
                    <label>Transcation Type</label>
                    <select id="bill_type" class="form-control" style="width:100%;">
                        <option value="0" selected>All</option>
                          <option value="1">SALES</option>
                          <option value="2">PURCHASE</option>
                          <option value="3">SALE RETURN</option>
                          <option value="4">ORDER ADVANCE</option>
                          <option value="5">CREDIT COLLECTION</option>
                          <option value="6">ADVANCE </option>
                          <option value="7">PAYMENT </option>
                          <option value="8">REPAIR </option>
                          <option value="9">PURCHASE PLAN</option>
                          <option value="10">PETTY CASH PAYMENT</option>
                          <option value="11">PETTY CASH RECEIPT</option>
                    </select>
                  </div>


							
							    <div class="col-md-2"> 

									<label></label>

									<div class="form-group">

										<button type="button" id="detailed_day_trans_search" class="btn btn-info">Search</button>   

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

				   	<div class="box box-info stock_details">

						<div class="box-body">

							<div class="row">

								<div class="box-body">

								   <div class="table-responsive detail_day_report" style="display:none;">

                                   <table id="detailed_day_transactiton_list"class="table table-bordered table-striped text-center" style="width:100px;">

                                        <thead>

                                            <tr>

                                                <th>S.No</th>

                                                <th>Branch</th>

                                                <th>Bill No</th>

                                                <th >Bill Date</th>

                                                <th>Bill Type</th>

                                                <th>Emp Name</th>

                                                <th>Bill Counter</th>

                                                <th>Purity</th>

                                                <th>Product</th>

                                                <th>Pcs</th>

                                                <th>Grswt</th>

                                                <th>Lesswt</th>

                                                <th>Netwt</th>

                                                <th>Diawt</th>

                                                <th>Taxable Amount</th>

                                                <th>SGST</th>

                                                <th>CGST</th>

                                                <th>IGST</th>

                                                <th>GST(Tax)</th>

                                                <th>Total Amount</th>

                                                <th>Cash</th>

                                                <th>Card</th>

                                                <th>Cheque</th>

                                                <th>Advance Adj</th>

                                                <th>UPI (Scanner)</th>

                                                <th>UPI (Transfer)</th>

                                                <th>RTGS</th>

                                                <th>IMPS</th>

                                                <th>NEFT</th>

                                                <th>Old Metal Purchase</th>

                                                <th>Sales Return</th>

                                                <th>Chit Ulitization</th>

                                                <th>Gift Voucher</th> 

                                                <th>Customer Name</th> 

                                                <th>Mobile Number</th> 

                                                <th>Pan Number</th> 

                                                <th>Gst Number</th> 

                                            </tr>

                                        </thead>

                                        <tbody></tbody>

                                        <tfoot>

                                            <tr>

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

                                                <td></td>

                                            </tr>

                                        </tfoot>

                                    </table>

									</div>

                  <div class="table-responsive bill_wise_summary_report" style="display:none;">

                      <table id="bill_wise_smry_transactiton_list" class="table table-bordered table-striped text-center" >

                        <thead>

                          <tr>

                            <th >Bill No</th>

                            <th >Description</th>

                            <th >Pcs</th>

                            <th >Grswt</th>

                            <th >Lesswt</th>

                            <th >Netwt</th>

                            <th >Diawt</th>

                            <th >Value</th>

                            <th >Cash</th>

                            <th >UPI </th>

                            <th >NB </th>

                            <th >Card</th>

                            <th >Cheque</th>

                            <th >Advance Adj</th>

                            <th >Chit Utilization</th>

                            <th >Old Metal Purchase</th>

                            <th >Sales Return</th>

                            <th >Due</th>

                            <!-- <th >Remarks</th> -->

                          </tr>

                        </thead>

                        <tbody></tbody>

                        <tfoot>

                          <tr>

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

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <!-- <td></td> -->

                          </tr>

                        </tfoot>

                      </table>

                  </div>


									 <div class="table-responsive summary_report" style="display:none;">

									     <table id="sales_summary_report" class="table table-bordered table-striped text-center" style="text-transform:uppercase;">

    										 <thead>

    										  <tr>

    											<th width="10%">Section</th>

    											<th width="10%">Tags</th>

    										    <th width="5%">Pcs</th>					    

    										    <th width="5%">Gwt(Grams)</th>

    										    <th width="5%">Dia Wt(Grams)</th>

    										    <th width="5%">Nwt(Grams)</th>

    										    <th width="5%">TAG Gwt(Grams)</th>

    										    <th width="5%">Diff Wt(Grams)</th>

    										    <th width="5%">Wastage(%)</th>

    										    <th width="5%">Wastage(Grams)</th>

    										    <th width="5%">MC</th>

    											<th width="5%">Amount(Rs)</th>

    											<th width="5%">Stn Amount(Rs)</th>

    										  </tr>

    					                    </thead><tbody></tbody>

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

      

      

     <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      <div class="modal-dialog">

        <div class="modal-content">

          <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

            <h4 class="modal-title" id="myModalLabel">Image Preview</h4>

          </div>

          <div class="modal-body">

            <img src="" id="imagepreview" style="width: 300px; height: 264px;" >

          </div>

          <div class="modal-footer">

            <button type="button" class="btn btn-default danger" data-dismiss="modal">Close</button>

          </div>

        </div>

      </div>

    </div>