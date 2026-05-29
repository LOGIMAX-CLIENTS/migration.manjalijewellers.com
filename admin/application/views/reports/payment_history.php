<html>
<style type="text/css" media="print">
    .nonPrintable {
        display: none;
    }

    /*class for the element we don’t want to print*/
    size: auto;
    /* auto is the initial value */
    margin: 0mm;

    #header,
    #nav,
    .noprint {
        display: none;
    }
</style>
<script>
    function myFunction() {
        window.print();
    }
</script>

<body>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Report
                <small></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo base_url('index.php/account/new'); ?>">Manage account</a></li>
                <li class="active">Account Report</li>
            </ol>
        </section>
        <div class="box">
            <div class="box-body">
                <div class="container-fluid">
                    <div id="printable">
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
                                    <?php
                                    $scheme_acc_number = $account['customer']['scheme_acc_number'];
                                    ?>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Account No</label>
                                            <p><?php echo $scheme_acc_number; ?></p>
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
                                        <div class="form-group">
                                            <label>Status</label>
                                            <p><span class="badge"><?php echo $account['customer']['status']; ?> </span>
                                            </p>
                                        </div>
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
                                        <div class="form-group">
                                            <label>Scheme code</label>
                                            <p><?php echo $account['customer']['scheme_code']; ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Paid Installments</label>
                                            <p><?php echo '<span class="badge bg-teal">' . ($account['customer']['paid_installments'] . "/" . $account['customer']['total_installments']) . '</span>'; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Installment Payable</label>
                                            <!-- based on the scheme type to showed payable & sch type HH -->
                                            <?php if ($account['customer']['type'] == 3 && ($account['customer']['flexible_sch_type'] == 1 || $account['customer']['flexible_sch_type'] == 2)) { ?>
                                                <p><?php echo ($account['customer']['currency_symbol'] . " Min " . $account['customer']['min_amount'] . " Max " . $account['customer']['max_amount']); ?>
                                                </p>
                                            <?php } else if ($account['customer']['type'] == 3 && ($account['customer']['flexible_sch_type'] == 3 || $account['customer']['flexible_sch_type'] == 4)) { ?>
                                                <p><?php echo ("Min " . formatMetalWeight($account['customer']['min_weight']) . " g" . " Max " . formatMetalWeight($account['customer']['max_weight']) . " g/month"); ?>
                                                </p>
                                            <?php } else if ($account['customer']['type'] == 0 || $account['customer']['type'] == 2) { ?>
                                                <p><?php echo ($account['customer']['currency_symbol'] . " " . $account['customer']['amount']); ?>
                                                </p>
                                            <?php } else { ?>
                                                <p><?php echo ($account['customer']['type'] == 1 && ($account['customer']['min_weight'] = $account['customer']['max_weight']) ? formatMetalWeight($account['customer']['min_weight']) . "g/month" : "Min " . formatMetalWeight($account['customer']['min_weight']) . " g" . " Max " . formatMetalWeight($account['customer']['max_weight']) . " g/month"); ?>
                                                <p>
                                                <?php } ?>
                                                <!--<p><?php echo ($account['customer']['scheme_type'] == 'Amount' ? $this->session->userdata('currency_symbol') : '') . ' ' . $account['customer']['payable']; ?></p>-->
                                        </div>
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <p><?php echo $account['customer']['start_date']; ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <p><?php echo $account['customer']['address1']; ?> <br />
                                                <?php echo $account['customer']['address2']; ?><br />
                                                <?php echo $account['customer']['address3']; ?><br />
                                                <?php echo $account['customer']['city'] . ' ' . $account['customer']['pincode']; ?>
                                                <br />
                                                <?php echo $account['customer']['state'] . ','; ?>
                                                <?php echo $account['customer']['country']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table id="pp" class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th>Installment No</th>
                                                <th>Payment Date</th>
                                                <th>Payment Mode</th>
                                                <th>Rate</th>
                                                <th>Amount (<?php echo $this->session->userdata('currency_symbol') ?>)
                                                </th>
                                                <th>Weight (Gm)</th>
                                                <th>Other benefits (Gm)</th>
                                                <th>Paid Amount
                                                    (<?php echo $this->session->userdata('currency_symbol') ?>)</th>
                                                <th>Running Weight (Gm)</< /th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($account['payment'])) {
                                                $bal_amt = 0;
                                                $bal_wt = 0;
                                                $otherWgt = 0;
                                                $prev_wt = formatMetalWeight($account['customer']['balance_weight']);
                                                $prev_amt = number_format($account['customer']['balance_amount'], "2", ".", "");
                                                $type = $account['customer']['type'];
                                                foreach ($account['payment'] as $pay) {
                                                    $bal_amt = number_format(($bal_amt + ($pay['payment_amount'] != "" ? $pay['payment_amount'] : 0)), "2", ".", "");
                                                    $bal_wt = formatMetalWeight(($bal_wt + ($pay['metal_weight'] != "" ? $pay['metal_weight'] : 0)));
                                                    $otherWgt = formatMetalWeight(($otherWgt + ($pay['dg_other_benefit_wgt'] != "" ? $pay['dg_other_benefit_wgt'] : 0)));
                                            ?>
                                                    <tr>
                                                        <td style="text-align: right;"><?php echo $pay['installment']; ?> </td>
                                                        <td><?php echo $pay['date_payment']; ?> </td>
                                                        <td><?php echo $pay['payment_mode']; ?> </td>
                                                        <td style="text-align: right;">
                                                            <?php if ($pay['is_editing_enabled'] == 1 && $pay['metalrate_edit_date']) { ?>
                                                                <a data-toggle="tooltip" title="<?php echo $pay['metalrate_edit_date']; ?>"><?php echo $pay['metal_rate']; ?></a>
                                                            <?php } else { ?>
                                                                <?php echo $pay['metal_rate']; ?>
                                                            <?php } ?>
                                                        </td>
                                                        <td style="text-align: right;"><?php echo $pay['payment_amount']; ?>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $pay['metal_weight']; ?>
                                                        </td>
														<td style="text-align: right;">
                                                            <?php echo !empty($pay['dg_other_benefit_wgt']) ? $pay['dg_other_benefit_wgt'] : "-" ; ?>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <?php echo number_format($bal_amt, "2", ".", ""); ?>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($bal_wt); ?>
                                                        </td>
                                                    </tr>
                                            <?php }
                                            } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="warning">
                                                <th colspan="4">Total</th>
                                                <td style="text-align: right;"><?php echo $bal_amt; ?> </td>
                                                <td style="text-align: right;">
                                                    <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $bal_wt; ?>
                                                </td>
												<td style="text-align: right;"><?php echo !empty($otherWgt) ? $otherWgt : "-" ; ?></td>
                                                <td style="text-align: right;"><?php echo $bal_amt; ?> </td>
                                                <td style="text-align: right;">
                                                    <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $bal_wt; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="4">Previous Weight</th>
                                                <td style="text-align: right;">
                                                    <?php echo ($type == 1 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?>
                                                </td>
                                                <td style="text-align: right;">
                                                    <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $prev_wt; ?>
                                                </td>
												<td></td>
                                                <td style="text-align: right;">
                                                    <?php echo ($type == 1 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?>
                                                </td>
                                                <td style="text-align: right;">
                                                    <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($prev_wt); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="4">Previous Amount </th>
                                                <td style="text-align: right;">
                                                    <?php echo ($type == 0 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?>
                                                </td>
												<td> - </td>
                                                <td> - </td>
                                                <td style="text-align: right;">
                                                    <?php echo ($type == 0 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?>
                                                </td>
                                                <td> - </td>
                                            </tr>
                                            <tr class="info">
                                                <th colspan="4">Total Paid</th>
                                                <th style="text-align: right;">
                                                    <?php echo $this->session->userdata('currency_symbol') . " " . number_format(($bal_amt + $prev_amt), "2", ".", ""); ?>
                                                </th>
                                                <th style="text-align: right;">
                                                    <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : number_format(($bal_wt + $prev_wt), "3", ".", "") . " Gm"; ?>
                                                </th>
												<th></th>
                                                <th style="text-align: right;">
                                                    <?php echo $this->session->userdata('currency_symbol') . " " . number_format(($bal_amt + $prev_amt), "2", ".", ""); ?>
                                                </th>
                                                <th style="text-align: right;">
                                                    <?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight(($bal_wt + $prev_wt)) . " Gm"; ?>
                                                </th>
                                            </tr>
                                            <?php if ($account['customer']['is_closed'] == 1) { ?>
                                                <tr class="success">
                                                    <th colspan="4">Deductions/Tax</th>
                                                    <!--<th colspan="4" align="right"><?php echo number_format($account['customer']['deductions'], "2", ".", ""); ?>  </th> -->
                                                    <th colspan="5" style="text-align: right;">
                                                        <?php echo number_format($account['customer']['deductions'], "2", ".", ""); ?>
                                                    </th>
                                                </tr>
                                                <tr class="success">
                                                    <th colspan="4">Benefits</th>
                                                    <?php if ($account['customer']['is_weight_scheme'] == 1 && $account['customer']['add_ben_type'] == 1) { ?>
                                                        <th colspan="5" style="text-align: right;">
                                                            <?php echo $account['customer']['benefits'] + $account['customer']['closing_benefits'] . " Gm"; ?>
                                                        </th>
                                                    <?php } else { ?>
                                                        <th colspan="5" style="text-align: right;">
                                                            <?php echo $this->session->userdata('currency_symbol') . ' ' . number_format(($account['customer']['benefits'] + $account['customer']['closing_benefits']), "2", ".", "") . " "; ?>
                                                        </th>
                                                    <?php } ?>
                                                </tr>
                                                <!-- based on the scheme type to showed closed amt/ wgt HH -->
                                                <!--<tr class="success">
                                                  <?php if ($account['customer']['type'] == 3 && ($account['customer']['flexible_sch_type'] == 1 || $account['customer']['flexible_sch_type'] == 2)) { ?>
                                                <th colspan="3">Closing Amount</th>
                                                <th colspan="4" align="right"><?php echo $this->session->userdata('currency_symbol') . ' ' . $account['customer']['closing_amount']; ?>  </th>
                                                <?php } else if ($account['customer']['type'] == 0 || $account['customer']['type'] == 2) { ?>
                                                <th colspan="3">Closing Amount</th>
                                                <th colspan="4" align="right"><?php echo $this->session->userdata('currency_symbol') . ' ' . $account['customer']['closing_amount']; ?>  </th>
                                                <?php } else { ?>
                                                <th colspan="3">Closing Weight</th>
                                                <th colspan="4" align="right"><?php echo formatMetalWeight($account['customer']['closing_balance']) . " Gm"; ?>  </th>
                                                <?php } ?>
                                             </tr> -->
                                                <tr class="success">
                                                    <th colspan="4">Closing Amount</th>
                                                    <th colspan="5" style="text-align: right;">
                                                        <?php echo $this->session->userdata('currency_symbol') . ' ' . $account['customer']['closing_amount']; ?>
                                                    </th>
                                                </tr>
                                                <tr class="success">
                                                    <th colspan="4">Closing Weight</th>
                                                    <!--<th colspan="4" style="text-align: right;"><?php echo number_format(($bal_wt + $prev_wt), "3", ".", "") . " Gm"; ?>  </th>   -->
                                                    <th colspan="5" style="text-align: right;">
                                                        <?php echo formatMetalWeight(($bal_wt + $prev_wt)) . " Gm"; ?>
                                                    </th>
                                                </tr>
                                            <?php } ?>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default"><br />
                        <div class="col-sm-offset-5">
                            <a target="_blank"
                                href="<?php echo base_url('index.php/account/close/invoice_history/' . $pay['id_scheme_account']); ?>"><button
                                    class="btn btn-primary">Print</button></a>
                            <a href="<?php echo base_url('index.php/account/new'); ?>"><button type="button"
                                    class="btn btn-default">Back</button></a>
                        </div> <br />
                    </div>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div>
</body>

</html>