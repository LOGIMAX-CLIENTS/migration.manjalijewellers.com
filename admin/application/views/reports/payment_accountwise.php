<style type="text/css">
    .DTTT_container {
        margin-bottom: 0 !important;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Scheme Account Statment
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Reports</a></li>
            <li class="active">Account Report</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content report_sel">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!--<div class="box-header with-border">
                        <h3 class="box-title">Scheme Account Statment</h3>-->
                    <!--           <a class="btn btn-success pull-right" href="<?php echo base_url('index.php/account/add'); ?>"><i class="fa fa-user-plus"></i> Add</a>-->
                    <!--</div>--><!-- /.box-header -->
                    <div class="box-body">
                        <div class="container-fluid"> <!-- content wrapper -->
                            <div class="row">
                                <div class="box box-default ">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Account Details</h3>
                                        <div class="box-tools pull-right">
                                            <button class="btn btn-box-tool" data-widget="collapse"><i
                                                    class="fa fa-minus"></i></button>
                                        </div><!-- /.box-tools -->
                                    </div><!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <?php
                                                if (@getimagesize(base_url() . 'assets/img/customer/' . $account['customer']['id_customer'] . '/customer.jpg')) {
                                                    echo '<img  class="img-thumbnail" src="' . base_url() . 'assets/img/customer/' . $account['customer']['id_customer'] . '/customer.jpg" width="240" height="240" >';
                                                } else {
                                                    echo '<img   class="img-thumbnail" src="' . base_url() . 'assets/img/default.png" width="240" height="240" >';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Account No</label>
                                                <p><?php echo $account['customer']['scheme_acc_number']; ?></p>
                                                </p>
                                                <input type="hidden" id="id_scheme_account" name=""
                                                    value="<?php echo !empty($account['payment']) ? $account['payment'][0]['id_scheme_account'] : $account['customer']['id_scheme_account']; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Customer</label>
                                                <p><?php echo ucfirst($account['customer']['customer_name']); ?></p>
                                            </div>
                                            <div class="form-group">
                                                <label>A/c Name</label>
                                                <p><?php echo ucfirst($account['customer']['account_name']); ?></p>
                                            </div>
                                            <div class="form-group">
                                                <label>Mobile</label>
                                                <p><?php echo $account['customer']['mobile']; ?></p>
                                            </div>
                                            <!-- Lines added by Durga 08.05.2023 starts here - to add maturity date -->
                                            <div class="form-group">
                                                <label>Maturity Date</label>
                                                <?php
                                                $maturitydate = '';
                                                if (!empty($account['customer']['maturity_date'])) {
                                                    $maturitydate = $account['customer']['maturity_date'];
                                                } else {
                                                    $total_installment = $account['customer']['total_installments'];
                                                    if(!empty($account['customer']['maturity_days']) && $account['customer']['maturity_days'] > 0){
                                                        $maturitydate = date('d-m-Y', strtotime("+" . $account['customer']['maturity_days'] . " days", strtotime($account['customer']['start_date'])));
                                                    }elseif($total_installment > 0 && $account['customer']['is_digi'] == 0){
                                                        $maturitydate = date('d-m-Y', strtotime("+" . $total_installment . " months", strtotime($account['customer']['start_date'])));
                                                    }
                                                    if ($account['customer']['maturity_type'] == 2) {
                                                        $maturitydate = date('d-m-Y', strtotime("+" . $account['customer']['maturity_days'] + $account['customer']['closing_maturity_days'] . " days", strtotime($account['customer']['start_date'])));
                                                    } elseif(!empty($account['customer']['closing_maturity_days']) && $maturitydate == '') {
                                                        $maturitydate = date('d-m-Y', strtotime("+" . $account['customer']['closing_maturity_days'] . " days", strtotime($account['customer']['start_date'])));
                                                    }
                                                }
                                                ?>
                                                <p><?php echo $maturitydate; ?></p>
                                            </div>
                                            <!-- Lines added by Durga 08.05.2023 ends here - to add maturity date -->
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Scheme</label>
                                                <p><?php echo $account['customer']['scheme_name']; ?></p>
                                            </div>
                                            <div class="form-group">
                                                <label>Scheme Type</label>
                                                <p><?php echo $account['customer']['scheme_type']; ?></p>
                                            </div>
                                            <?php if ($this->session->userdata('enable_old_sch_acc_no') == 1) { ?>
                                                <div class="form-group">
                                                    <label>Old A/c No</label>
                                                    <p><?php echo $account['customer']['old_scheme_acc_number']; ?></p>
                                                </div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <label>Scheme code</label>
                                                <p><?php echo $account['customer']['scheme_code']; ?></p>
                                            </div>
                                            <!--	<div class="form-group">
                                    <label>Paid Installments</label>
                                    <p><?php echo '<span class="badge bg-teal">' . ($account['customer']['paid_installments'] . "/" . $account['customer']['total_installments']) . '</span>'; ?></p>
                                </div> -->
                                            <div class="form-group">
                                                <label>Paid Installments</label>
                                                <?php if ($account['customer']['show_ins_type'] == 1) { ?>
                                                    <p><?php echo '<span class="badge bg-teal">' . ($account['customer']['paid_installments'] . "/" . $account['customer']['total_installments']) . '</span>'; ?>
                                                    </p>
                                                <?php } else { ?>
                                                    <p><?php echo '<span class="badge bg-teal">' . ($account['customer']['paid_installments']) . '</span>'; ?>
                                                    </p>
                                                <?php } ?>
                                            </div>
                                            <!-- coded by Jothika on 16-10-2025 [disable payment issue] -->
                                            <!-- Stop Payment Switch -->
                                            <div class="form-group mb-0">
                                                <label>Stop Payment</label>
                                                <input type="checkbox" id="block_payment"
                                                    name="disable_payment" value="1"
                                                    data-on-text="YES" data-off-text="NO"
                                                    <?php if ($account['customer']['disable_payment'] == 1) { ?> checked <?php } ?>>
                                                <input type="hidden" name="db_stop_payment"
                                                    value="<?php echo set_value('db_stop_payment', $account['customer']['disable_payment']); ?>" />
                                            </div>
                                            <div id="disable_pay_block" style="display:none;">
                                                <!-- div payment disable reason starts here -->
                                                <div class="" id="area">
                                                    <label>Disable Payment Reason</label>
                                                    <textarea class="form-control" id="disable_pay_reason" name="scheme[disable_pay_reason]" rows="3" cols="30"><?php echo set_value('scheme[disable_pay_reason]', $account['customer']['disable_pay_reason']); ?></textarea>
                                                </div>
                                                <!-- div payment disable reason ends here -->
                                            </div>
                                            <!-- div update payment disable reason button starts here -->
                                            <div id="block_button" style="display:none;">
                                                <input type="hidden" id="hidden_scheme_id" value="<?php echo $account['customer']['id_scheme_account']; ?>" />
                                                <div class="form-group">
                                                    <button id="update_reason" class="btn btn-success" value="" style="margin-top:8px;">
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- div update payment disable reason button ends here -->
                                            <?php if ($account['customer']['is_lumpSum'] == 1) { ?>
                                                <div class="form-group">
                                                    <label>Joined Weight</label>
                                                    <p><?php echo '<span >' . ($account['customer']['lump_joined_weight']) . ' g/ins </span>'; ?>
                                                    </p>
                                                </div>
                                            <?php } ?>
                                            <?php if ($account['customer']['is_topup_scheme'] == 1) { ?>
                                                <div class="form-group">
                                                    <strong style="font-size:17px;">Advance Booking Details</strong>
                                                    <table class="table table-condensed">
                                                        <tbody>
                                                            <tr>
                                                                <th>Booking Amount</th>
                                                                <td><span id="topup_amount"><?php echo 'INR ' . $account['customer']['topup_amount']; ?></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Booking Weight</th>
                                                                <td><span id="topup_weight"><?php echo $account['customer']['topup_weight'] . ' g'; ?></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th>Booking Metal Rate</th>
                                                                <td><span id="topup_rate"><?php echo 'INR ' . $account['customer']['topup_rate']; ?></span></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Installment Payable</label>
                                                <?php if ($account['customer']['is_lumpSum'] == 1) { ?>
                                                    <p> <?php echo $account['customer']['lump_payable_weight'] . ' g/ins'; ?>
                                                    </p>
                                                <?php } else if ($account['customer']['is_topup_scheme'] == 1) { ?>
                                                    <p> <?php echo 'INR ' . $account['customer']['topup_amount']; ?>
                                                    </p>
                                                <?php } else { ?>
                                                    <!-- based on the scheme type to showed payable & sch type HH -->
                                                    <?php if ($account['customer']['type'] == 3 && ($account['customer']['flexible_sch_type'] == 1 || $account['customer']['flexible_sch_type'] == 2)) { ?>
                                                        <p><?php echo ($account['customer']['currency_symbol'] . " Min " . $account['customer']['min_amount'] . " Max " . $account['customer']['max_amount']); ?>
                                                        </p>
                                                    <?php } else if ($account['customer']['type'] == 3 && ($account['customer']['flexible_sch_type'] == 3 || $account['customer']['flexible_sch_type'] == 4)) { ?>
                                                        <p><?php echo ("Min " . $account['customer']['min_weight'] . " g" . " Max " . $account['customer']['max_weight'] . " g/month"); ?>
                                                        </p>
                                                    <?php } else if ($account['customer']['type'] == 0 || $account['customer']['type'] == 2) { ?>
                                                        <p><?php echo ($account['customer']['currency_symbol'] . " " . $account['customer']['amount']); ?>
                                                        </p>
                                                    <?php } else { ?>
                                                        <p><?php echo ($account['customer']['type'] == 1 && ($account['customer']['min_weight'] = $account['customer']['max_weight']) ? $account['customer']['min_weight'] . "g/month" : "Min " . $account['customer']['min_weight'] . " g" . " Max " . $account['customer']['max_weight'] . " g/month"); ?>
                                                        <p>
                                                        <?php } ?>
                                                        <!--<p><?php echo ($account['customer']['scheme_type'] == 'Amount' ? $this->session->userdata('currency_symbol') : '') . ' ' . $account['customer']['payable']; ?></p>-->
                                                    <?php } ?>
                                            </div>
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <p><?php echo $account['customer']['start_date']; ?></p>
                                            </div>
                                            <div class="form-group">
                                                <label>Address</label> <!--- hh -->
                                                <p><?php if ($account['customer']['address1'] == 0) { ?>
                                                        <td><span><?php echo $account['customer']['address1']; ?></span>
                                                        </td>
                                                        <td><span><?php echo $account['customer']['address2']; ?></span>
                                                        </td>
                                                        <td><span><?php echo $account['customer']['address3']; ?></span>
                                                        </td>
                                                        <td><span><?php echo $account['customer']['city'] . '  ' . $account['customer']['pincode']; ?></span>
                                                        </td>
                                                    <?php } else { ?>
                                                        <td><span><?php echo $account['']; ?></span></td>
                                                    <?php } ?>
                                                </p>
                                            </div>
                                            <!-- div remark starts here -->
                                            <div class="form-group">
                                                <label for="remarks">Remarks</label>
                                                <textarea class="form-control" id="acc_remark"
                                                    name="scheme[remark_open]" rows="4"
                                                    cols="50"><?php echo $account['customer']['remark_open']; ?></textarea>
                                            </div>
                                            <!-- div remark ends here -->
                                            <!-- div update remark button starts here -->
                                            <input type="hidden" id="hidden_scheme_id"
                                                value="<?php echo $account['customer']['id_scheme_account']; ?>" />
                                            <div class="form-group"> <button id="update_remark" class="btn btn-success">
                                                    Update Remarks </button> </div>
                                            <!-- div update remark button ends here -->
                                        </div>
                                    </div><!-- /.box-body -->
                                </div><!-- /.box -->
                            </div>
                            <div class="row">
                                <div id="alert_msg"></div>
                                <!--	      <?php if ($this->session->userdata('id_branch') == '') { ?>-->
                                <!--	<input type="button" value="Cancel Payment" id="can_payment" class="btn btn-info pull-right" name="">-->
                                <!--<?php } ?>-->
                                <!--Advace payment list starts-->
                                <?php
                                if ($account['customer']['allow_general_advance'] == 1) { ?>
                                    <div class="box box-default">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">General Advance Payment List</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-primary" id="gen_adv_btn">Show List</button>
                                                <button class="btn btn-primary hide_table" style="display:none;">Hide
                                                    List</button>
                                            </div><!-- /.box-tools -->
                                            <br />
                                            <div class="box-body">
                                                <div class="adv_table" style="display:none;">
                                                    <div class="col-xs-12">
                                                        <div class="table-responsive">
                                                            <table id="gen_adv_table_byid"
                                                                class="table table-bordered table-striped text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sno</th>
                                                                        <th>Payment Date</th>
                                                                        <th>Payment Mode</th>
                                                                        <th style="text-align:right;">Payment Amount(INR)
                                                                        </th>
                                                                        <th style="text-align:right;">Weight(g)</th>
                                                                        <th style="text-align:right;">Metal Rate(INR)</th>
                                                                        <th>Payment Status</th>
                                                                        <th>Receipt No</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                                <tfoot>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    <?php
                                }
                    ?>
                    <!--Advace payment list ends-->
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table id="cancel" class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <!-- <th>Installment No</th> -->
                                        <th>Installment (Month/Day)</th>
                                        <th>Payment Date</th>
                                        <th>Payment Mode</th>
                                        <th>Rate</th>
                                        <th>GST (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                        <th>Amount (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                        <th>Reference number</th>
                                        <th>Remarks</th>
                                        <th>Discount Amount</th>
                                        <th>Paid Amount (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                        <th>Saved Weight (g)</th>
                                        <!--	<th>Balance Amount (<?php echo $this->session->userdata('currency_symbol') ?>)</th> -->
                                        <th>Running Weight (g)</th>
                                        <?php if ($account['customer']['flexible_sch_type'] == 3) { ?>
                                            <th>Avg Weight (g)</th>
                                        <?php } ?>
                                        <?php if (!empty($account['customer']['is_digi'])) { ?>
                                            <th>Benefit Weight (g)</th>
                                            <th>Benefit Amount (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                            <th>Other benefit WGT (g)</th>
                                        <?php } ?>
                                        <th>Status</th>
                                        <th>Receipt No</th>
                                        <!--<?php if ($this->session->userdata('id_branch') == '') { ?>-->
                                        <!--<th>Cancel-->
                                        <!--	<input type="checkbox" id="cancel_all" name="">-->
                                        <!--</th>-->
                                        <!--   <?php } ?>-->
                                        <th>Reprint</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($account['payment'])) {
                                        // echo '<pre>';print_r($account['payment']);exit;
                                        $bal_amt = 0;
                                        $bal_wt = 0;
                                        // $prev_wt = number_format($account['customer']['balance_weight'], $this->session->userdata('metal_wgt_decimal')) : number_format($account['customer']['balance_weight'], $this->session->userdata('metal_wgt_decimal'), '.', '');
                                        $prev_wt = formatMetalWeight($account['customer']['balance_weight']);
                                        $prev_amt = number_format($account['customer']['balance_amount'], "2", ".", "");
                                        $type = $account['customer']['type'];
                                        $discount = 0;
                                        $total = 0;
                                        $otherBenefitWgt = 0;
                                        foreach ($account['payment'] as $pay) {
                                            $gst_amt = ($pay['gst_type'] == 1 ? $pay['payment_amount'] * ($pay['gst'] / 100) : $pay['payment_amount'] - ($pay['payment_amount'] * (100 / (100 + $pay['gst']))));
                                            $discount = number_format(($discount + ($pay['discountAmt'] != "" ? $pay['discountAmt'] : 0)), "2", ".", "");
                                            $disc = $discount;    //hh
                                            $b_amt = number_format(($bal_amt + ($pay['payment_amount'] != "" ? $pay['payment_amount'] - $pay['discountAmt'] : 0)), "2", ".", "");
                                            $bal_amt = ($pay['gst'] > 0 ? ($pay['gst_type'] == 0 ? $b_amt - $gst_amt : $b_amt) : $b_amt);
                                            $total = number_format(($total + ($pay['payment_amount'] != "" ? $pay['payment_amount'] : 0)), "2", ".", "");
                                            $total_amount = $total;  //hh
                                            // $bal_wt = $this->session->userdata('metal_wgt_roundoff') == 0 ? trim_decimal($bal_wt + ($pay['metal_weight'] != "" ? $pay['metal_weight'] : 0), $this->session->userdata('metal_wgt_decimal')) : number_format($bal_wt + ($pay['metal_weight'] != "" ? $pay['metal_weight'] : 0), $this->session->userdata('metal_wgt_decimal'), '.', '');
                                            $bal_wt = formatMetalWeight($bal_wt + ($pay['metal_weight'] != "" ? $pay['metal_weight'] : 0));
                                            $totalBenefit = formatMetalWeight($totalBenefit + ($pay['saved_benefits'] != "" ? $pay['saved_benefits'] : 0));
                                            $totalBenefitAmt = number_format(($totalBenefitAmt + ($pay['saved_benefit_amt'] != "" ? $pay['saved_benefit_amt'] : 0)), "2", ".", "");
                                            $otherBenefitWgt = formatMetalWeight(($otherBenefitWgt + ($pay['dg_other_benefit_wgt'] != "" ? $pay['dg_other_benefit_wgt'] : 0)));
                                    ?>
                                            <tr>
                                                <td><?php echo $pay['id_payment']; ?></td>
                                                <td style="text-align: right;"><?php echo $pay['installment']; ?></td>
                                                <td><?php echo $pay['date_payment']; ?> </td>
                                                <td><?php echo $pay['payment_mode']; ?> </td>
                                                <td style="text-align: right;">
                                                    <?php if ($pay['is_editing_enabled'] == 1 && $pay['metalrate_edit_date']) { ?>
                                                        <a data-toggle="tooltip" title="<?php echo $pay['metalrate_edit_date']; ?>"><?php echo $pay['metal_rate']; ?></a>
                                                    <?php } else { ?>
                                                        <?php echo $pay['metal_rate']; ?>
                                                    <?php } ?>
                                                </td>
                                                <td style="text-align: right;">
                                                    <?php echo ($pay['gst'] > 0 ? number_format($gst_amt, "2", ".", "") : '-'); ?>
                                                </td>
                                                <td style="text-align: right;">
                                                    <?php echo number_format((float) $pay['payment_amount'] - $pay['discountAmt'], 2, '.', ''); ?>
                                                    <br /><?php echo ($pay['gst'] > 0 ? ($pay['gst_type'] == 0 ? "<span style='color:#7ea0bd;'>Inclusive of GST</span>" : "") : "") ?>
                                                </td>
                                                <?php $collapseId = 'paymentDetails' . $pay['id_payment']; ?>
                                                <td>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link" type="button"
                                                                data-toggle="collapse" data-target="#<?= $collapseId ?>">
                                                                View Payment Details
                                                            </button>
                                                        </div>
                                                        <div id="<?= $collapseId ?>" class="collapse">
                                                            <div class="card-body">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Mode</th>
                                                                            <th>Amount</th>
                                                                            <th>Reference</th>
                                                                            <th>Date</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $ref_string = $pay['ref_number'];
                                                                        $rows = explode(",", $ref_string);
                                                                        foreach ($rows as $row):
                                                                            $mode = $amount = $ref = $date = "-";
                                                                            $parts = array_map('trim', explode('//$$//', $row));
                                                                            // Assign based on available parts
                                                                            if (isset($parts[0]))
                                                                                $mode = $parts[0];
                                                                            if (isset($parts[1]))
                                                                                $amount = $parts[1];
                                                                            if (isset($parts[2]) && $parts[2] !== '-')
                                                                                $ref = $parts[2];
                                                                            if (isset($parts[3]))
                                                                                $date = $parts[3];
                                                                        ?>
                                                                            <tr>
                                                                                <td><?= $mode ?></td>
                                                                                <td><?= $amount ?></td>
                                                                                <td><?= $ref ?></td>
                                                                                <td style="white-space: nowrap;"><?= $date ?>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- <td><?php echo $pay['ref_number']; ?></td> -->
                                                <td><?php echo $pay['remark'] != "" && $pay['remark'] != null ? $pay['remark'] : '-'; ?>
                                                </td>
                                                <td style="text-align: right;"><?php echo $pay['discountAmt']; ?> </td>
                                                <td style="text-align: right;"><?php echo $pay['payment_amount']; ?>
                                                    <br /><?php echo ($pay['gst'] > 0 ? ($pay['gst_type'] == 0 ? "<span style='color:#7ea0bd;'>Inclusive of GST</span>" : "") : "") ?>
                                                </td>
                                                <td style="text-align: right;">
                                                    <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($pay['metal_weight']); ?>
                                                </td>
                                                <!--	<td><?php echo number_format($bal_amt, "2", ".", ""); ?></td>		-->
                                                <td style="text-align: right;">
                                                    <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($bal_wt); ?>
                                                </td>
                                                <?php if ($account['customer']['flexible_sch_type'] == 3) { ?>
                                                    <td><?php echo formatMetalWeight($bal_wt / $account['customer']['paid_installments']); ?>
                                                    </td>
                                                <?php } ?>
                                                <?php if (!empty($account['customer']['is_digi'])) { ?>
                                                    <td> <?php echo ($pay['saved_benefits'] != 0 && $pay['saved_benefits'] != "") ? formatMetalWeight($pay['saved_benefits']) : "-"; ?> </td>
                                                    <td> <?php echo ($pay['saved_benefit_amt'] != 0 && $pay['saved_benefit_amt'] != "") ? $pay['saved_benefit_amt'] : "-"; ?></td>
                                                    <td><?php echo formatMetalWeight($pay['dg_other_benefit_wgt']); ?></td>
                                                <?php } ?>
                                                <td><?php echo $pay['payment_status'] ?> </td>
                                                <td><?php echo $pay['receipt_no'] ?> </td>
                                                <!--<?php if ($this->session->userdata('id_branch') == '') { ?>										-->
                                                <!--<td><input type="checkbox" id="payment_cancel" name="payment_cancel[]" value="<?php echo $pay['id_payment']; ?>" ></td>-->
                                                <!--   <?php } ?>-->
                                                <td><input type="checkbox" id="payment_reprint" name="payment_reprint[]"
                                                        value="<?php echo $pay['id_payment']; ?>"></td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr class="warning" style="font-weight:bold;">
                                        <th colspan="4">Total</th>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;">
                                            <?php echo number_format($bal_amt, "2", ".", ""); ?> </td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;"><?php echo $disc; ?> </td>
                                        <td style="text-align: right;"><?php echo $total_amount; ?> </td>
                                        <td style="text-align: right;">
                                            <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($bal_wt); ?>
                                        </td>
                                        <!--	<td><?php echo number_format($bal_amt, "2", ".", ""); ?> </td>  -->
                                        <td style="text-align: right;">
                                            <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($bal_wt); ?>
                                        </td>
                                        <!--<th> </th>										-->
                                        <?php if ($account['customer']['is_digi'] == 1) { ?>
                                            <td> <?= formatMetalWeight($totalBenefit); ?> </td>
                                            <td> <?= $totalBenefitAmt ?> </td>
                                            <td>
                                                <?php echo ($otherBenefitWgt != 0 && $otherBenefitWgt != "") ? $otherBenefitWgt : "-"; ?>
                                            </td>
                                        <?php } ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <tr>
                                    <tr>
                                        <th colspan="4">Previous Weight</th>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;">
                                            <?php echo ($type == 1 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;">
                                            <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $prev_wt; ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <?php echo ($type == 1 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?>
                                        </td>
                                        <td style="text-align: right;">
                                            <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($prev_wt); ?>
                                        </td>
                                        <td></td>
                                        <?php if ($account['customer']['is_digi'] == 1) { ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        <?php } ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <tr>
                                    <tr>
                                        <th colspan="4">Previous Amount </th>
                                        <td></td>
                                        <td></td>
                                        <td> </td>
                                        <td style="text-align: right;">
                                            <?php echo ($type == 0 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?>
                                        </td>
                                        <td> </td>
                                        <td> </td>
                                        <td> </td>
                                        <td> </td>
                                        <td style="text-align: right;">
                                            <?php echo ($type == 0 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?>
                                        </td>
                                        <!-- <td> - </td> -->
                                        <?php if ($account['customer']['is_digi'] == 1) { ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        <?php } ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <tr>
                                    <tr class="success">
                                        <th colspan="4">Gross Total</th>
                                        <td> </td>
                                        <td> </td>
                                        <td> </td>
                                        <td> </td>
                                        <td> </td>
                                        <!---->
                                        <th style="text-align: right;">
                                            <?php echo ($type == 1 || $type == 2 ? number_format($disc, "2", ".", "") : '-'); ?>
                                        </th>
                                        <th style="text-align: right;">
                                            <?php echo $this->session->userdata('currency_symbol') . " " . number_format(($discount + $total), "2", ".", ""); ?>
                                        </th>
                                        <th style="text-align: right;">
                                            <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' :formatMetalWeight(($bal_wt + $prev_wt)) . " g"; ?>
                                        </th>
                                        <!--	<th><?php echo $this->session->userdata('currency_symbol') . " " . number_format($bal_amt, "2", ".", ""); ?> </th>  -->
                                        <th style="text-align: right;">
                                            <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $bal_wt . " g"; ?>
                                        </th>
                                        <?php if ($account['customer']['is_digi'] == 1) { ?>
                                            <th> </th>
                                            <th> </th>
                                            <th> <?php echo (!empty($otherBenefitWgt)) ? $otherBenefitWgt . "g" : "" ?> </th>
                                        <?php } ?>
                                        <th> </th>
                                        <th> </th>
                                        <th><input type="button" value="Reprint" id="pay_reprint"
                                                class="btn bg-maroon pull-right" name=""></th>
                                    <tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    </div>
                    <div class="col-xs-12">
                        <!-- <div class="table-responsive">
                                    <table id="cancel" class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Payment Date</th>
                                                <th>Payment Mode</th>
                                                <th>Rate</th>
                                                <th>GST (<?php echo $this->session->userdata('currency_symbol') ?>)</th>									
                                                <th>Amount (<?php echo $this->session->userdata('currency_symbol') ?>)</th>											
                                                <th>Discount Amount</th>	
                                                <th>Total Amount</th>
                                                <th>Weight (g)</th>
                                                <th>Running Weight (g)</th>
                                                <?php if ($account['customer']['flexible_sch_type'] == 3) { ?>
                                                <th>Avg Weight (g)</th>
                                                <?php } ?>
                                                <th>Status</</th>
                                                <th>Receipt No</</th>
                                                <?php if ($this->session->userdata('id_branch') == '') { ?>
                                                <th>Cancell
                                                    <input type="checkbox" id="cancel_all" name="">
                                                </th>
                                                <?php } ?>
                                                <th>Reprint</th>
                                            </tr>
                                        </thead> -->
                        <!-- <tbody>
                                        <?php
                                        if (isset($account['payment'])) {
                                            $bal_amt = 0;
                                            $bal_wt = 0;
                                            $prev_wt = formatMetalWeight($account['customer']['balance_weight']);
                                            $prev_amt = number_format($account['customer']['balance_amount'], "2", ".", "");
                                            $type = $account['customer']['type'];
                                            $discount = 0;
                                            $total = 0;
                                            foreach ($account['payment'] as $pay) {
                                                $gst_amt = ($pay['gst_type'] == 1 ? $pay['payment_amount'] * ($pay['gst'] / 100) : $pay['payment_amount'] - ($pay['payment_amount'] * (100 / (100 + $pay['gst']))));
                                                $discount = number_format(($discount + ($pay['discountAmt'] != "" ? $pay['discountAmt'] : 0)), "2", ".", "");
                                                $disc = $discount;    //hh
                                                $b_amt = number_format(($bal_amt + ($pay['payment_amount'] != "" ? $pay['payment_amount'] - $pay['discountAmt'] : 0)), "2", ".", "");
                                                $bal_amt = ($pay['gst'] > 0 ? ($pay['gst_type'] == 0 ? $b_amt - $gst_amt : $b_amt) : $b_amt);
                                                $total = number_format(($total + ($pay['payment_amount'] != "" ? $pay['payment_amount'] : 0)), "2", ".", "");
                                                $total_amount = $total;  //hh
                                                $bal_wt = formatMetalWeight(($bal_wt + ($pay['metal_weight'] != "" ? $pay['metal_weight'] : 0)));
                                        ?> -->
                        <!-- <tr>
                                                <td ><?php echo $pay['id_payment']; ?></td>
                                                <td><?php echo $pay['date_payment']; ?> </td>
                                                <td><?php echo $pay['payment_mode']; ?> </td>	
                                                <td><?php echo $pay['metal_rate']; ?> </td>												
                                                <td><?php echo ($pay['gst'] > 0 ? number_format($gst_amt, "2", ".", "") : '-'); ?></td>		
                                                <td><?php echo number_format((float) $pay['payment_amount'] - $pay['discountAmt'], 2, '.', ''); ?>  <br/><?php echo ($pay['gst'] > 0 ? ($pay['gst_type'] == 0 ? "<span style='color:#7ea0bd;'>Inclusive of GST</span>" : "") : "") ?></td>	
                                                <td><?php echo $pay['discountAmt']; ?> </td>
                                                <td><?php echo $pay['payment_amount']; ?>  <br/><?php echo ($pay['gst'] > 0 ? ($pay['gst_type'] == 0 ? "<span style='color:#7ea0bd;'>Inclusive of GST</span>" : "") : "") ?></td>	
                                                <td><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $pay['metal_weight']; ?> </td>	
                                            <?php echo number_format($bal_amt, "2", ".", ""); ?></td>		
                                                 <td><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($bal_wt); ?> </td>	
                                                <?php if ($account['customer']['flexible_sch_type'] == 3) { ?>
                                                 <td><?php echo formatMetalWeight($bal_wt / $account['customer']['paid_installments']); ?> </td>	
                                                <?php } ?>
                                                <td><?php echo $pay['payment_status'] ?> </td>	
                                                <td><?php echo $pay['receipt_no'] ?> </td>
                                                <?php if ($this->session->userdata('id_branch') == '') { ?>                                        
                                                <td><input type="checkbox" id="payment_cancel" name="payment_cancel[]" value="<?php echo $pay['id_payment']; ?>" ></td>
                                                <?php } ?>
                                                <td><input type="checkbox" id="payment_reprint" name="payment_reprint[]" value="<?php echo $pay['id_payment']; ?>" ></td>
                                            </tr>	
     <?php }
                                        } ?>										 -->
                        <!-- </tbody>
                                        <tfoot>
                                             <tr class="warning" style="font-weight:bold;">
                                                <th colspan="4">Total</th>
                                                  <td></td>
                                                <td><?php echo number_format($bal_amt, "2", ".", ""); ?> </td>
                                                 <td><?php echo $disc; ?> </td>
                                                     <td><?php echo $total_amount; ?> </td>
                                                <td><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $bal_wt; ?> </td>
                                            <?php echo number_format($bal_amt, "2", ".", ""); ?> </td> 
                                                <td><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $bal_wt; ?> </td>	
                                                    <th> </th>										
                                               <th colspan="4"></th>
                                             <tr>
                                             <tr> -->
                        <!-- <th colspan="4">Previous Weight</th>
                                                <td> <?php echo ($type == 1 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?> </td>
                                                <td><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $prev_wt; ?> </td>
                                                <td><?php echo ($type == 1 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?> </td>
                                                <td><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($prev_wt); ?> </td>
                                                <th> </th>
                                              <th colspan="4"></th>
                                             <tr>			
                                             <tr>
                                                <th colspan="4">Previous Amount </th>
                                                <td> - </td>
                                                <td><?php echo ($type == 0 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?> </td>
                                                <td> - </td>
                                                <td><?php echo ($type == 0 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?> </td>
                                                <td> - </td>
                                                <th> </th>
                                                  <th colspan="4"></th>
                                             <tr>  -->
                        <!-- <tr class="success">
                                                <th colspan="4">Gross Total</th>
                                                <td> - </td>
                                                <th><?php echo $this->session->userdata('currency_symbol') . " " . number_format(($bal_amt + $prev_amt), "2", ".", ""); ?>  </th>
                                                <th> <?php echo ($type == 1 || $type == 2 ? number_format($disc, "2", ".", "") : '-'); ?> </th>
                                                <th><?php echo $this->session->userdata('currency_symbol') . " " . number_format(($discount + $total), "2", ".", ""); ?>  </th>											
                                                <th><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($bal_wt + $prev_wt) . " g"; ?>  </th> -->
                        <!--	<th><?php echo $this->session->userdata('currency_symbol') . " " . number_format($bal_amt, "2", ".", ""); ?> </th>  -->
                        <!-- <th><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $bal_wt . " g"; ?> </th>	
                                                <th> </th>
                                                <th> </th>
                                                <th > <th><input type="button" value="Reprint" id="pay_reprint" class="btn bg-maroon pull-right" name=""></th></th>
                                             <tr>
                                        </tfoot>
                                    </table>	
                                </div>	
                            </div>	 -->
                    </div>
                    <div class="row">
                        <div class="col-sm-12" align="right">
                            <?php
                            if ($account['customer']['one_time_premium'] == 1 && $account['customer']['paid_installments'] > 0) {
                            ?>
                                <a class="btn bg-purple" href="<?php echo base_url() . 'index.php/admin_manage/passbook_print/bond/' . (!empty($account['payment']) ? $account['payment'][0]['id_scheme_account'] : $account['customer']['id_scheme_account']) . '/' . (isset($pay) ? $pay['id_payment'] : '') ?>" target="_blank"><i class="fa fa-print"></i> Chit Bond</a>
                            <?php
                            } else {
                            ?>
                                <a class="btn bg-green passbook_print"
                                    href="<?php echo base_url() . 'index.php/admin_manage/passbook_print/F/' . (!empty($account['payment']) ? $account['payment'][0]['id_scheme_account'] : $account['customer']['id_scheme_account']); ?>"
                                    target="_blank"><i class="fa fa-print"></i> Passbook Print Front</a>
                                <?php if ($account['customer']['one_time_premium'] == 0 && !empty($account['payment'])) { ?>
                                    <a class="btn bg-purple passbook_print"
                                        href="<?php echo base_url() . 'index.php/admin_manage/passbook_print/B/' . $account['payment'][0]['id_scheme_account']; ?>"
                                        target="_blank"><i class="fa fa-print"></i> Passbook Print Back</a>
                            <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
</div><!-- /.col -->
</div><!-- /.row -->
</section><!-- /.content -->
</div><!-- /.content-wrapper -->
<div id="cancel_payment" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                <button type="button" id="close_model" class="close" data-dismiss="modal"
                    aria-hidden="true">&times;</button>
                <h3 id="myModalLabel">Are you sure want to cancel this payment</h3>
                <input type="submit" id="yes" value="Yes" class="btn btn-success"> </input>
                <input type="submit" id="no" value="No" class="btn btn-danger"> </input>
            </div>
        </div>
    </div>
    <?php
    // function trim_decimal($value, $precision)
    // {
    //     $precision = (int)$precision;
    //     // Convert scientific notation to decimal string safely
    //     if (stripos((string)$value, 'e') !== false) {
    //         $value = sprintf('%.20f', $value); // expand E notation
    //         $value = rtrim($value, '0');
    //         $value = rtrim($value, '.');
    //     }
    //     $value = (string)$value;
    //     // Precision = 0 → integer only
    //     if ($precision === 0) {
    //         return explode('.', $value)[0];
    //     }
    //     // No decimal point → pad zeros
    //     if (strpos($value, '.') === false) {
    //         return $value . '.' . str_repeat('0', $precision);
    //     }
    //     list($int, $dec) = explode('.', $value, 2);
    //     // Truncate + pad
    //     $dec = substr($dec, 0, $precision);
    //     $dec = str_pad($dec, $precision, '0');
    //     return $int . '.' . $dec;
    // }
