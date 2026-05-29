  <!-- Content Wrapper. Contains page content -->

  <style>

      .remove-btn {

          margin-top: -168px;

          margin-left: -38px;

          background-color: #e51712 !important;

          border: none;

          color: white !important;

      }

  </style>

  <div class="content-wrapper">

      <!-- Content Header (Page header) -->

      <section class="content-header">

          <h1>

              Advance Transfer

          </h1>

          <ol class="breadcrumb">

              <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>

              <li><a href="#">Billing Module</a></li>

              <li class="active">Advance Transfer</li>

          </ol>

      </section>



      <!-- Main content -->

      <section class="content product">



          <!-- Default box -->

          <div class="box box-primary">

              <div class="box-header with-border">

                  <div class="box-tools pull-right">

                      <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>

                      <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>

                  </div>

              </div>

              <div class="box-body">

                  <div class="row">





                      <div class="container">

                          <div class="row">

                              <div class="col-md-3">

                                  <?php if ($this->session->userdata('id_branch') == '') { ?>



                                      <select style="width:100%" id="branch_select" required></select>

                                      <input type="hidden" name="advance_trans_data[id_branch]" id="id_branch" required="">



                                  <?php } else { ?>

                                      <select id="branch_select" disabled></select>

                                      <input type="hidden" name="advance_trans_data[id_branch]" id="id_branch" value="<?php echo $this->session->userdata('id_branch'); ?>">

                                  <?php } ?>

                              </div>

                              <div class="col-md-4">

                                  <div class="row">

                                      <div class="col-sm-4">

                                          <label type="text">From Customer</label>

                                      </div>



                                      <div class="col-sm-8">

                                          <input class="form-control" type="text" name=advance_trans_data[adv_trns_from_cust] id="adv_trns_from_cust" autocomplete="off" placeholder="Enter Customer Name">

                                          <input type="hidden" id="from_cus_id">

                                          <input type='hidden' id='advance_transfer' name="advance_trans_data[advance_transfer][]" value="">

                                          <input type="hidden" name="advance_trans_data[tot_transfer_amount]" id="total_transfered_amt">

                                          <input id="is_eda" type="hidden" name="advance_trans_data[is_eda]" value="1" />

                                          <input id="from_cus_mobile" type="hidden" name="advance_trans_data[from_cus_mobile]" />

                                          <input id="is_otp_verfied" type="hidden" name="advance_trans_data[is_otp_verfied]" value="0" />

                                          <input id="adv_trans_otp" type="hidden" name="advance_trans_data[adv_trans_otp]" />

                                          <input id="send_resend" type="hidden" name="advance_trans_data[send_resend]" value="0" />

                                          <input id="otp_required" type="hidden" name="advance_trans_data[otp_required]" value=<?php echo $otp_settings ?> />

                                          <input id="validate_max_cash" type="hidden" value=<?php echo $settings['validate_cash_amt'] ?> />

                                          <input id="max_cash_amt" type="hidden" value=<?php echo $settings['max_cash_amt'] ?> />

                                          <input id="cus_payment_details" type="hidden" value="0" />

                                          <input id="adv_total_cash_amt" type="hidden" value="0" />



                                          <span class="customerAlert"></span>

                                      </div>

                                  </div>

                              </div>

                              <div class="col-md-4">

                                  <div class="col-sm-4">

                                      <label type="text">To Customer</label>

                                  </div>



                                  <div class="col-sm-8">



                                      <input class="form-control" type="text" name=advance_trans_data[adv_trns_to_cust] id="adv_trns_to_cust" placeholder="Enter Customer Name" autocomplete="off">

                                      <input type="hidden" id="to_cus_id" name="adv_trans[to_cus_id]">

                                      <span class="tocustomerAlert"></span>

                                  </div>

                              </div>

                          </div>

                          <p class="help-block"></p>

                          </br>







                          <div class="row">



                              <table id="advance_trns_list" class="table table-bordered table-striped text-center">



                                  <thead>



                                      <tr>



                                          <th width="15%">Select</th>



                                          <th width="15%">Receipt Bill No</th>



                                          <th width="20%">Receipt Balance Amount</th>



                                          <th width="25%">Transfer Amount</th>



                                          <th width="25%">Total Balance Amount</th>



                                      </tr>



                                  </thead>



                                  <tbody>



                                  </tbody>



                                  <tfoot>



                                      <tr>

                                          <td colspan="3"><b>Total Transfer Amount</b></td>

                                          <td><span class="total_transfered_amt">0.00</span></td>



                                      </tr>

                                  </tfoot>



                              </table>



                          </div>

                         

                          </br>





                          <div class="row">

                              <div class="box box-default"><br />

                                  <div class="col-xs-offset-5">

                                      <button type="button" id="submit_advance_transfer" class="btn btn-primary">Save</button>

                                      <button type="button" class="btn btn-default btn-cancel">Cancel</button>



                                  </div> <br />

                              </div>

                          </div>

                      </div>

                      <div class="overlay" style="display:none">

                          <i class="fa fa-refresh fa-spin"></i>

                      </div>





                  </div>

              </div>



      </section>



  </div>



  <div class="modal fade" data-backdrop="static" data-keyboard="false" id="otp_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      <div class="modal-dialog">

          <div class="modal-content">

              <div class="modal-header">

                  <h4 class="modal-title" id="myModalLabel">Verify OTP and Update Status</h4>

              </div>

              <div class="modal-body">

                  <div class="row">

                      <div class="col-md-12">

                          <h5>We have sent OTP to autorized mobile number. Kindly verify OTP to proceed further.</h5>

                      </div>

                  </div>

                  <p></p>

                  <div class="row otp_block">

                      <div class="col-md-2">

                          <div class='form-group'>

                              <label for="">OTP</label>

                          </div>

                      </div>

                      <div class="col-md-5">

                          <div class='form-group'>

                              <div class='input-group'>

                                  <input type="text" id="adv_trns_otp" name="adv_trns_otp" placeholder="Enter 6 Digit OTP" maxlength="6" class="form-control" required />

                                  <span class="input-group-btn">

                                      <button type="button" id="verify_advance_transfer_otp" class="btn btn-primary btn-flat" disabled>Verify</button>

                                  </span>

                              </div>

                          </div>

                      </div>

                      <div class="col-md-2">

                          <div class='form-group'>

                              <input type="button" id="resend_advance_transfer_otp" class="btn btn-warning btn-flat" value="Resend OTP" />

                          </div>

                      </div>

                  </div>

                  <div class="row">

                      <div class="col-md-12">

                          <span class="otp_alert"></span>

                      </div>

                  </div>

              </div>

              <div class="modal-footer">

                  <button type="button" class="submit_advance_transfer btn btn-success btn-flat" disabled>Save And Submit</button>

                  <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal" id="close">Close</button>

              </div>

          </div>

      </div>

  </div>