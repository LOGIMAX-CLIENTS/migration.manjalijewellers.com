<!-- Content Wrapper. Contains page content -->

 <div class="content-wrapper">

     <!-- Content Header (Page header) -->

      <style>

     @media print {



         html,

         body {

             height: auto;

             width: 190vh;

             margin: 0 !important;

             padding: 0 !important;

             overflow: hidden;

         }

     }

     </style>

       <section class="content-header">

            <div class="row">

                <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

		                  <div class="col-md-3"> 

		                     <div class="form-group tagged">

									<select id="branch_select" class="form-control ret_branch"></select>

		                     </div> 

		                  </div> 

						    <?php }else{?>

		                     <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

		                     <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

		                   <?php }?>

				

				<div class="col-md-2">

                     <div class="form-group">

                         <div class="input-group">

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

                    <div class="form-group tagged">

                        <select id="mode_select"

                            class="form-control mode_filter" >

                            <option value="0">All</option>

                            <option value="1">Bill</option>

                            <option value="2">Receipts</option>

                            <option value="3">Chit</option>

                            <option value="4">Payment</option>

                            <option value="5">All Receipts</option>

                        </select>

                    </div>

                </div>

                                

				<div class="col-md-1">

                     <div class="form-group">

                         <button type="button" id="day_trans_search"

                             class="btn btn-info">Search</button>

                     </div>

                 </div>

				<div class="col-md-4 box-tools pull-right">

                      <ol class="breadcrumb">

                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

                        <li><a href="#">Accounts Reports</a></li>

                        <li class="active">Transactions</li>

                      </ol>

                </div>

          </div>

        </section>



     <!-- Main content -->

     <section class="content">

         <div class="row">

             <div class="col-xs-12">

                 <div class="box box-primary">

                     <div class="box-body">

                         <div class="box box-info stock_details collapsed-box">

						<div class="box-header with-border">

						  <h3 class="box-title">Payment Summary <span class="summery_description"></span></h3>

						  <div class="box-tools pull-right">

							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse"><i class="fa fa-plus"></i></button>

						  </div>

						</div>

					<div class="box-body collapse" style="display: none;">

					       <div class="row" style="background: #ecf0f5;">

					           <div class="col-md-4" style="text-align:center;font-weight: bold;">Receipts</div>

					           <div class="col-md-4" style="text-align:center;font-weight: bold;">Payments</div>

					       </div>

							<div class="row">

								<div class="box-body col-md-4">

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Cash

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_cash_receipts"></span></strong></div>

										

									</div>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Card

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_card_receipts"></span></strong></div>

										

									</div>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Cashfree

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_cashfree_receipts"></span></strong></div>

										

									</div>

								    <div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Cheque

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_cheque_receipts"></span></strong></div>

									</div>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											NetBanking

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_nb_receipts"></span></strong></div>

									</div>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Paytm

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_paytm_receipts"></span></strong></div>

									</div>

									<div class="row">

									</div>

									<hr>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											<strong>Total Receipts</strong>

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_amt_receipts"></span></strong></div>

									</div>

								</div>

								

								<div class="box-body col-md-4">

									

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Cash

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_cash_paymet"></span></strong></div>

									</div>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Card

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_card_paymet"></span></strong></div>

									</div>

									<div class="row" >

										<div class="col-md-6" style="text-align: left;"> 

											Cashfree

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_cashfree_paymet"></span></strong></div>

										

									</div>

								    <div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Cheque

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_cheque_paymet"></span></strong></div>

									</div>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											NetBanking

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_nb_paymet"></span></strong></div>

									</div>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											Paytm

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_paytm_paymet"></span></strong></div>

									</div>

									

									<div class="row">

									</div>

									<hr>

									<div class="row">

										<div class="col-md-6" style="text-align: left;"> 

											<strong>Total Payments</strong>

										</div>

										<div class="col-md-2"><lable> : </lable></div>

										<div class="col-md-4" style="text-align: right;"><strong><span class="total_amt_payments"></span></strong></div>

									</div>

								</div>

							</div>

						</div>



					</div>

                         <div class="row">

                             <div class="col-md-12">

                                 <div class="table-responsive">

                                     <table id="day_transactiton_list"

                                         class="table table-bordered table-striped text-center">

                                         <thead>

                                             <tr>

                                                 <th>Bill No</th>

                                                 <th>Date</th>

                                                 <th>Customer</th>

                                                 <th>Mobile</th>

                                                 <th>Grswt</th>

                                                 <th>Netwt</th>

                                                 <th>Diawt</th>

                                                 <th>Amount</th>

                                                 <th>SGST</th>

                                                 <th>CGST</th>

                                                 <th>IGST</th>

                                                 <th>GST</th>

                                                 <th>Total</th>

                                                 <th>Cash</th>

                                                 <th>Card</th>

                                                 <th>Paytm</th>

                                                 <th>Cheque</th>

                                                 <th>NB</th>

                                                 <th>Cash Free</th>

                                                 <th>Chit Utilize</th>

                                                 <th>Due</th>

                                                 <th>Discount</th>

                                                 <th>Handling Chrg</th>

                                                 <th>Order Adv adj</th>

                                                 <th>Adv adj</th>

                                                 <th>Purchase No</th>

                                                 <th>Purchase Amt</th>

                                                 <th>Sales Return</th> 

                                                 <th>Sales Return Amt</th> 

                                                 <th>Gift Voucher</th> 

                                                 <th>Round Off</th> 

                                             </tr>

                                         </thead>

                                         <tbody></tbody>

                                         <tfoot>

                                             <tr style="text-align:right;">

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

                                                 <td style="text-align: right;"></td>

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