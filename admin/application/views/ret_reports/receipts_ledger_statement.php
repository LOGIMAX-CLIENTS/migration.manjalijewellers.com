  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper">

<!-- Content Header (Page header) -->



<!-- Main content -->



 <section class="content-header">

    <div class="row">

        <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>

                  <div class="col-md-3"> 

                     <div class="form-group tagged">

                            <select id="branch_filter" class="form-control ret_branch"></select>

                     </div> 

                  </div> 

                    <?php }else{?>

                     <input type="hidden" id="branch_filter"  value="<?php echo $this->session->userdata('id_branch') ?>"> 

                     <input type="hidden" id="branch_name"  value="<?php echo $this->session->userdata('branch_name') ?>"> 

                  <?php }?>

      <div class="col-md-2">

            <select id="reqledger_type" class="form-control">

                <option value="1">Receipts</option>

                <option value="2">Payments</option>

            </select>

        </div>

        

        <div class="col-md-3"> 

            <select id="ledger_type" class="form-control">

                <option value="0"> - ALL - </option>

                <option value="1">Receipt Sales</option>

                <option value="2">Receipt Order</option>

                <option value="3">Chit Receipt</option>

                <option value="4">General Receipts</option>

            </select>

        </div> 

        

        <div class="col-md-2"> 

                            <div class="form-group">    

                                <?php   

                                    $fromdt = date("d/m/Y");

                                    $todt = date("d/m/Y");

                                ?>

                                   <input type="text" class="form-control pull-right dateRangePicker" id="dt_range" placeholder="From Date -  To Date" value="<?php echo $fromdt.' - '.$todt?>" readonly="">  

                            </div> 

        </div>

        

        <!-- <div class="col-md-2">

            <select id="ledger_type" class="form-control">

                <option value="0"> - ALL - </option>

                <option value="1">Debtor</option>

                <option value="2">Creditor</option>

            </select>

        </div> -->

        <div class="col-md-1">

             <div class="form-group">

                 <button type="button" id="receipts_stmt_search"

                     class="btn btn-info">Search</button>

             </div>

        </div>

  </div>

</section>



<section class="content">

  <div class="row">

    <div class="col-xs-12">

       

       <div class="box box-primary">

         <div class="box-body">  

                   <div class="box box-info stock_details">

                <div class="box-header with-border">

                  <h3 class="box-title">Receipts Register</h3>

                  <div class="box-tools pull-right">

                    <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                  </div>

                </div>

                <div class="box-body">

                    <div class="row">

                        <div class="box-body">

                           <div class="table-responsive">

                              <table id="receipts_ledger_list" class="table table-bordered table-striped text-center">

                                 <thead>

                                  <tr>

                                    <th>Date</th>

                                    <th>Particulars</th>

                                    <th>Vouher Type</th>

                                    <th>Voucher No</th>

                                    <th>Bill Id</th>

                                    <th>Gross Total</th>

                                    <th>Cash</th>

                                    <th>A/C</th>

                                  </tr>

                            </thead> 

                             <tbody></tbody>

                              <tfoot>

                                     <tr>

                                         <td class="dt-right"></td>

                                         <td class="dt-right"></td>

                                         <td class="dt-right"></td>

                                         <td class="dt-right"></td>

                                         <td class="dt-right"></td>

                                         <td class="dt-right"></td>

                                         <td class="dt-right"></td>

                                         <td class="dt-right"></td>

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



