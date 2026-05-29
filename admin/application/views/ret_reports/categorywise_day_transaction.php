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

			      <small>CategoryWise Day Transactions Report</small>

          </h1>

          <ol class="breadcrumb">

            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

            <li><a href="#">Reports</a></li>

            <li class="active">CategoryWise Day Transactions Repor</li>

          </ol>

        </section>

        <!-- Main content -->

        <section class="content">

          <div class="row">

            <div class="col-xs-12">

               <div class="box box-primary">

                  <div class="box-header with-border">

                    <h3 class="box-title">CategoryWise Day Transactions Report</h3>  <span id="total_count" class="badge bg-green"></span>  

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

                            <select id="metal" class="form-control" style="width:100%;"></select>

                          </div>

                          <div class="col-md-2 sales_cat"> 

                            <label>Select category</label>

                            <select id="category" style="width: 100%;"></select>

                          </div>

                          <div class="col-md-2 pur_cat" style="display:none;">
                            <label>Select Old Metal category</label>
                            <select id="old_metal_type" style="width: 100%;"></select>
                          </div>

                          <div class="col-md-2"> 
                            <label>Transcation Type</label>
                            <select id="bill_type" class="form-control" style="width:100%;">
                                <option value="0" selected>All</option>
                                <option value="1" >SALES</option>
                                <option value="2" >PURCHASE</option>
                                <option value="3" >SALE RETURN</option>
                                <option value="4" >ORDER ADVANCE</option>
                                <option value="5" >GENERAL ADVANCE</option>
                                <option value="7" >PAYMENT</option>
                                <option value="6" >PURCHASE PLAN</option>
                            </select>
                          </div>

                          <div class="col-md-2"> 
                            <label></label>
                            <div class="form-group">
                              <button type="button" id="categorywise_day_trans_search" class="btn btn-info">Search</button>   
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

                  <div class="table-responsive detail_report" style="">

                    <table id="categorywise_day_transactiton_list"class="table table-bordered table-striped text-center">

                      <thead>

                        <tr>

                          <!-- <th>Branch</th> -->

                          <th>Category</th>

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

                          <th>UPI</th>

                          <th>RTGS</th>

                          <th>IMPS</th>

                          <th>NEFT</th>

                          <th>ONLINE</th>

                          <th>Old Metal</th>

                          <th>Sales Return</th>

                          <th>Advance Adj</th>

                          <th>Chit Ultilization</th>
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