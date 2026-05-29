<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Payment Report</title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/receipt.css">
    <!--    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/receipt_temp.css">-->
    <style>
        .head {
            color: black;
            font-size: 50px;
        }

        .alignLeft {
            text-align: left !important;
        }

        .alignRight {
            text-align: right !important;
        }

        .alignCenter {
            text-align: center !important;
        }


        /* Ensure proper page breaks */
        @media print {
            .table {
                page-break-inside: auto;
            }

            .table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .table thead {
                display: table-header-group;
                /* Repeat table headers on new pages */
            }

            .table tfoot {
                display: table-footer-group;
                /* Optional: Repeat table footers */
            }
        }
    </style>
</head>

<body>
    <div class="PDFReceipt">

        <div><img alt="" src="<?php echo base_url(); ?>assets/img/receipt_logo.png" style="width:30%;"></div>
        <div class="address" align="right">
            <?php echo $comp_details['address1'] . ' ,'; ?>
            <?php echo $comp_details['address2'] . ' ,'; ?>
            <?php echo $comp_details['city'] . '  ' . $comp_details['pincode'] . ' ,'; ?>
            <?php echo $comp_details['state'] . ' ,'; ?>
            <?php echo $comp_details['country'] . ' .'; ?>


            <!--<p> <?php echo 'Phone : ' . $comp_details['phone']; ?></p><p> <?php echo 'Mobile : ' . $comp_details['mobile']; ?></p><p></p>-->
        </div>


        <div class="heading"><?php echo $account['customer']['scheme_name']; ?></div>

        <table class="meta" style="width: 40%" align="right">

            <tr>
                <th><span>Mobile</span></th>
                <td><span> <?php echo $account['customer']['mobile']; ?></span></td>
            </tr>
            <tr>

                <th><span>Scheme A/c No</span></th>
                <td><span><?php echo $account['customer']['scheme_acc_number']; ?></span></td>
            </tr>
            <tr>
                <th><span>A/c Name</span></th>
                <td><span><?php echo $account['customer']['account_name']; ?></span></td>
            </tr>
            <tr>
                <th><span>Start Date</span></th>
                <td><span><?php echo $account['customer']['start_date']; ?></span></td>
            </tr>
            <tr>
                <th><span>Paid Installments</span></th>
                <td><span><?php echo $account['customer']['paid_installments']; ?></span></td>
            </tr>
        </table>
        <p></p>
        <div class="useraddr">
            <p><?php echo  $account['customer']['customer_name'] ?></p>
            <p> <?php echo  $account['customer']['address1'] . (!empty($account['customer']['address1']) ? ',<br/>' : ''); ?> </p>
            <p><?php echo  $account['customer']['address2'] . (!empty($account['customer']['address2']) ? ',<br/>' : ''); ?> </p>
            <p><?php echo  $account['customer']['address3'] . (!empty($account['customer']['address3']) ? ',<br/>' : ''); ?> </p>
            <p><?php echo  $account['customer']['city'] . "  " . $account['customer']['pincode']; ?>
            </p>
        </div>
        <div class="content-wrapper">

            <div class="box">

                <div class="box-body">


                    <div class="container-fluid">

                        <div id="printable">
                            <div class="row">

                                <div class="col-xs-12">


                                    <div class="table-responsive">

                                        <table id="pp" class="table table-bordered table-striped text-center">

                                            <!--    <thead> -->

                                            <tr>

                                                <th class="alignCenter">Payment Date</th>

                                                <th class="alignCenter">Payment Mode</th>

                                                <th class="alignCenter">Rate</th>

                                                <th class="alignCenter">Amount (<?php echo $this->session->userdata('currency_symbol') ?>)</th>

                                                <th class="alignCenter">Weight (Gm)</th>

                                                <th class="alignCenter">Paid Amount (<?php echo $this->session->userdata('currency_symbol') ?>)</th>

                                                <th class="alignCenter">Running Weight (Gm)</th>





                                            </tr>

                                            <!--</thead>

                                        <tbody>-->

                                            <?php



                                            if (isset($account['payment'])) {

                                                $bal_amt = 0;

                                                $bal_wt = 0;

                                                $prev_wt = formatMetalWeight($account['customer']['balance_weight']);

                                                $prev_amt = number_format($account['customer']['balance_amount'], "2", ".", "");

                                                $type = $account['customer']['type'];

                                                foreach ($account['payment'] as $pay) {

                                                    $bal_amt = number_format(($bal_amt + ($pay['payment_amount'] != "" ? $pay['payment_amount'] : 0)), "2", ".", "");

                                                    $bal_wt = formatMetalWeight(($bal_wt + ($pay['metal_weight'] != "" ? $pay['metal_weight'] : 0)));



                                            ?>

                                                    <tr>

                                                        <td><?php echo $pay['date_payment']; ?> </td>

                                                        <td><?php echo $pay['payment_mode']; ?> </td>

                                                        <td class="alignRight"><?php echo $pay['metal_rate']; ?> </td>

                                                        <td class="alignRight"><?php echo $pay['payment_amount']; ?> </td>

                                                        <td class="alignRight"><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $pay['metal_weight']; ?> </td>

                                                        <td class="alignRight"><?php echo number_format($bal_amt, "2", ".", ""); ?> </td>

                                                        <td class="alignRight"><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($bal_wt); ?> </td>




                                                    </tr>

                                            <?php }
                                            } ?>

                                            <!--    </tbody> 

                                        <tfoot>-->

                                            <tr class="warning">

                                                <th colspan="3">Total</th>

                                                <td class="alignRight"><?php echo $bal_amt; ?> </td>

                                                <td class="alignRight"><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $bal_wt; ?> </td>

                                                <td class="alignRight"><?php echo $bal_amt; ?> </td>

                                                <td class="alignRight"><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $bal_wt; ?> </td>

                                            </tr>

                                            <tr>

                                                <th colspan="3">Previous Weight</th>

                                                <td class="alignRight"> <?php echo ($type == 1 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?> </td>

                                                <td class="alignRight"><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : $prev_wt; ?> </td>

                                                <td class="alignRight"><?php echo ($type == 1 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?> </td>

                                                <td class="alignRight"><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight($prev_wt); ?> </td>

                                            </tr>

                                            <tr>

                                                <th colspan="3">Previous Amount </th>

                                                <td class="alignRight"><?php echo ($type == 0 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?> </td>

                                                <td class="alignRight"> - </td>

                                                <td class="alignRight"><?php echo ($type == 0 || $type == 2 ? number_format($prev_amt, "2", ".", "") : '-'); ?> </td>

                                                <td class="alignRight"> - </td>
                                            </tr>

                                            <tr class="">

                                                <th colspan="3">Total Paid</th>

                                                <th class="alignRight"><?php echo $this->session->userdata('currency_symbol') . " " . number_format(($bal_amt + $prev_amt), "2", ".", ""); ?> </th>

                                                <th class="alignRight"><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight(($bal_wt + $prev_wt)) . " Gm"; ?> </th>

                                                <th class="alignRight"><?php echo $this->session->userdata('currency_symbol') . " " . number_format(($bal_amt + $prev_amt), "2", ".", ""); ?> </th>

                                                <th class="alignRight"><?php echo ($pay['scheme_type'] == '0' || $pay['flexible_sch_type'] == '1') ? '-' : formatMetalWeight(($bal_wt + $prev_wt)) . " Gm"; ?> </th>


                                            </tr>

                                        </table>
                                        <?php if ($account['customer']['is_closed'] == 1) { ?>
                                            <table id="pp" class="table table-bordered table-striped text-center">
                                                <tr class="success">


                                                    <th colspan="2">Deductions/Tax</th>

                                                    <!--    <th colspan="4" align="right"><?php echo number_format($account['customer']['deductions'], "2", ".", ""); ?>  </th>-->
                                                    <th class="alignRight" colspan="2"><?php echo number_format($account['customer']['deductions'], "2", ".", ""); ?> </th>
                                                    <!-- </tr> -->
                                                    <!-- <tr class="success"> -->

                                                    <th colspan="2">Benefits</th>



                                                    <?php if ($account['customer']['is_weight_scheme'] == 1 && $account['customer']['add_ben_type'] == 1) { ?>

                                                        <th class="alignRight" colspan="2"><?php echo $account['customer']['benefits'] + $account['customer']['closing_benefits'] . " Gm"; ?></th>

                                                    <?php } else { ?>

                                                        <th colspan="2" class="alignRight"><?php echo $this->session->userdata('currency_symbol') . ' ' . number_format(($account['customer']['benefits'] + $account['customer']['closing_benefits']), "2", ".", "") . " "; ?></th>

                                                    <?php } ?>
                                                </tr>
                                                <!-- based on the scheme type to showed closed amt/ wgt HH -->
                                                <!--  <tr class="success">
                                                  <?php if ($account['customer']['type'] == 3 && ($account['customer']['flexible_sch_type'] == 1 || $account['customer']['flexible_sch_type'] == 2)) { ?>
                                                
                                                <th colspan="3">Closing Amount</th>
                                                <th colspan="4" align="right"><?php echo $this->session->userdata('currency_symbol') . ' ' . $account['customer']['closing_balance']; ?>  </th>
                                                    
                                                <?php } else if ($account['customer']['type'] == 0) { ?>
                                                
                                                <th colspan="3">Closing Amount</th>
                                                <th colspan="4" align="right"><?php echo $this->session->userdata('currency_symbol') . ' ' . $account['customer']['closing_balance']; ?>  </th>
                                                
                                                <?php } else { ?>
                                                
                                                <th colspan="3">Closing Weight</th>
                                                <th colspan="4" align="right"><?php echo number_format($account['customer']['closing_balance'], "3", ".", "") . " Gm"; ?>  </th>
                                                <?php } ?>
                                             </tr>  -->

                                                <tr class="success">
                                                    <th colspan="2">Closing Amount</th>
                                                    <th colspan="2" class="alignRight"><?php echo $this->session->userdata('currency_symbol') . ' ' . $account['customer']['closing_amount']; ?> </th>
                                                    <!-- </tr>
                                            <tr class="success"> -->
                                                    <th colspan="2">Closing Weight</th>
                                                    <th colspan="2" class="alignRight"><?php echo number_format(($bal_wt + $prev_wt), "3", ".", "") . " Gm"; ?> </th>
                                                </tr>
                                            <?php } ?>
                                            <!--    </tfoot>-->

                                            </table>



                                    </div>

                                </div>

                            </div>
                        </div>
                        <p></p>
                        <h5>REMARK : <?php echo $account['customer']['status']; ?></h5>

                        <?php echo date('d-m-Y H:i:s'); ?>

                    </div>
                </div>
            </div><!-- /.box-body -->


            <!--function printDiv(printable) {
var printContents = document.getElementById(divName).innerHTML;
var originalContents = document.body.innerHTML;
document.getElementById('header').style.display = 'none';
document.getElementById('footer').style.display = 'none';
document.body.innerHTML = printContents;

window.print();


document.body.innerHTML = originalContents;
}-->



        </div>
    </div>
</body>

</html>