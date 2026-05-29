<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Advance Receipt Voucher</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 0 -5mm 0 -5mm;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th,
        tr {
            padding: 0px 2px !important;
            line-height: 1.5 !important;
            vertical-align: middle;
        }

        table.account_data td,
        th,
        tr {
            line-height: 1.5 !important;
            vertical-align: middle;
            padding: 0px 0px 0px 2px !important;
        }

        th {
            border: 1px solid #000;
        }

        .tab {
            border: 1px solid #000;
        }

        .heading {
            font-size: 12px;
        }

        .subheading {
            font-size: 11px;
            font-weight: normal !important;
        }

        .bottom_line {
            border-bottom: 1px solid #000 !important;
        }

        .right_line {
            border-right: 1px solid #000 !important;
        }

        .equal_width {
            width: 50%;
        }

        tr.bold-row td {
            font-weight: bold;
        }
    </style>
</head>

<body style="margin-top: 100px;">
    <?php
    function formatIndianCurrency($amount)
    {
        $amount = number_format($amount, 2, '.', '');
        $decimal = substr($amount, -3);
        $number = substr($amount, 0, -3);
        $lastThree = substr($number, -3);
        $rest = substr($number, 0, -3);
        if ($rest != '') {
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
        }
        return ($rest != '' ? $rest . ',' : '') . $lastThree . $decimal;
    }
    ?>
    <table class="tab">
        <tr class="heading">
            <th colspan="2">ADVANCE RECEIPT VOUCHER</th>
        </tr>
        <tr class="subheading">
            <th colspan="2">Section 31(3)(d) and Rule 50 of CGST Act and Rules, 2017</th>
        </tr>
        <tr>
            <td class="bottom_line right_line" style="vertical-align: top;">
                <table class="account_data" style="border-spacing: 0 4px;border-collapse: separate;">
                    <tr>
                        <td>Customer Name</td>
                        <td>:</td>
                        <td><?php echo strtoupper($records[0]['name']); ?></td>
                    </tr>
                    <tr>
                        <td width="30%">Address</td>
                        <td width="5%">:</td>
                        <td width="65%">
                            <?php echo ucfirst(strtolower($records[0]['address1'])); ?>
                            <?php echo ucfirst(strtolower($records[0]['address2'])); ?>
                            <?php echo ucfirst(strtolower($records[0]['address3'])); ?>
                            <?php echo ucfirst(strtolower($records[0]['village_name'])); ?>
                            <?php echo ucfirst(strtolower($records[0]['city'] . "   " . $records[0]['pincode'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Mobile</td>
                        <td>:</td>
                        <td><?php echo $records[0]['mobile']; ?></td>
                    </tr>
                    <tr>
                        <td>PAN</td>
                        <td>:</td>
                        <td><?php echo $records[0]['cus_pan_no']; ?></td>
                    </tr>
                    <tr>
                        <td>STATE</td>
                        <td>:</td>
                        <td><?php echo strtoupper($records[0]['state']) ?></td>
                    </tr>
                    <tr>
                        <td>State Code</td>
                        <td>:</td>
                        <td><?php echo $records[0]['state_code']; ?></td>
                    </tr>
                    <tr>
                        <td>Nominee Name</td>
                        <td>:</td>
                        <td><?php echo strtoupper($records[0]['nominee_name']); ?></td>
                    </tr>
                    <tr>
                        <td>Nominee Address</td>
                        <td>:</td>
                        <td><?php echo ucfirst(strtolower($records[0]['nominee_address1'])); ?>
                            <?php echo ucfirst(strtolower($records[0]['nominee_address2'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Nominee Relation</td>
                        <td>:</td>
                        <td><?php echo ucfirst(strtolower($records[0]['nominee_relationship'])); ?></td>
                    </tr>
                </table>
            </td>
            <td class="bottom_line" style="vertical-align: top;">
                <table class="account_data" style="border-spacing: 0 2px;border-collapse: separate;">
                    <tr>
                        <td>Receipt Id</td>
                        <td>:</td>
                        <td><?php echo $records[0]['receipt_no'] ?></td>
                    </tr>
                    <tr>
                        <td>Advance Number</td>
                        <td>:</td>
                        <td><?php echo $records[0]['scheme_acc_number'] ?></td>
                    </tr>
                    <tr>
                        <td>Advance Date</td>
                        <td>:</td>
                        <td><?php echo $records[0]['start_date'] ?></td>
                    </tr>
                    <tr>
                        <td>Due Date</td>
                        <td>:</td>
                        <td><?php echo $records[0]['due_date_to'] ?></td>
                    </tr>
                    <tr>
                        <td>Receipt Entry On</td>
                        <td>:</td>
                        <td><?php echo $records[0]['date_payment'] ?></td>
                    </tr>
                    <tr>
                        <td>Supplier GSTIN</td>
                        <td>:</td>
                        <td><?php echo $comp_details['gst_number'] ?></td>
                    </tr>
                    <tr>
                        <td>Currency</td>
                        <td>:</td>
                        <td><?php echo $comp_details['currency_symbol'] ?></td>
                    </tr>
                    <tr>
                        <td>Max Gold Rate</td>
                        <td>:</td>
                        <td><?php echo formatIndianCurrency($records[0]['topup_rate']); ?></td>
                    </tr>
                    <tr>
                        <td>Place of Supply</td>
                        <td>:</td>
                        <td><?php echo $comp_details['state'] ?></td>
                    </tr>
                    <tr>
                        <td>Tax Is Payable On Reverse Charge</td>
                        <td>:</td>
                        <td>No</td>
                    </tr>
                    <tr>
                        <td>Sales Person</td>
                        <td>:</td>
                        <td><?php echo $records[0]['referred_employee'] ?></td>
                    </tr>
                    <tr>
                        <td>Purity</td>
                        <td>:</td>
                        <td><?php echo $records[0]['purity'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="bottom_line">
                <table>
                    <tr class="bold-row">
                        <td>Advance Received</td>
                        <td>:</td>
                        <td><?php echo formatIndianCurrency($records[0]['payment_amount']); ?></td>
                    </tr>
                </table>
            </td>
            <td class="bottom_line">
                <table>
                    <tr class="bold-row">
                        <td><?php echo $comp_details['currency_name'] . ' ' . $records[0]['amount_in_words'] . ' Only' ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td class="equal_width">
                <table>
                    <tr>
                        <td>Towards the advance against purchase of approximate</td>
                        <td>:</td>
                        <td><b><?php echo $records[0]['topup_weight'] ?> GMS </b></td>
                    </tr>
                </table>
            </td>
            <td class="equal_width">
                <table>
                    <tr>
                        <td>Maximum gold rate per gram</td>
                        <td></td>
                        <td><?php echo formatIndianCurrency($records[0]['topup_rate']); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td>Validity : Days (<?php echo $records[0]['topup_slab_data']['slab_day_end'] ?>) Offer expire
                            date : <b><?php echo $records[0]['due_date_to'] ?> </b></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td>GOLD(HSN Code : <?php echo $records[0]['hsn_code'] ?> )</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td>Deposit type : ORDER</td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td>Payment Mode</td>
                        <td>:</td>
                        <td><?php echo $records[0]['multi_modes'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td>Slab : <?php
                        if ($records[0]['topup_slab_data']['slab_type'] == 1) {
                            $slab_value = $records[0]['topup_slab_data']['slab_value'] . '%';
                        } else {
                            $slab_value = 'INR' . $records[0]['topup_slab_data']['slab_value'];
                        }
                        echo $slab_value;
                        ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <td>Remarks : <?php echo $records[0]['pay_remarks'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td><b>Order Details :-</b></td>
                        <td>Order Wt : <?php echo $records[0]['topup_weight'] ?> GMS : Amount :
                            <?php echo formatIndianCurrency($records[0]['topup_amount']); ?>
                        </td>
                    </tr>
                </table>
            </td>
            <td></td>
        </tr>
        <!--<tr>
            <td colspan="2" style="height: 10px;">&nbsp;</td>
        </tr>-->
        <tr class="bold-row" style="line-height: 1.5 !important;">
            <td colspan="2">
                <strong class="heading">Terms and Conditions:</strong>
                <p>The gold rate applicable will be the lower of the rate on the date of order or the date of purchase,
                    subject to the following advance payment
                    conditions.</p>
                <ol>
                    <li>The original receipt must be produced at the time of purchase.</li>
                    <li>If the purchase is not completed within the stipulated period of the scheme, the benefit of the
                        lower gold rate will apply only to
                        the gold weight equivalent to the amount advanced.</li>
                    <li>The entire quantity of gold for which the advance has been made must be purchased.</li>
                    <li>Cash refunds are not permitted under this scheme.</li>
                    <li>Making charges will be calculated based on the gold rate on the billing day and must be paid at
                        the time of purchase.</li>
                    <li>Jewellery purchased under this advance scheme will be subject to making charges and all
                        applicable government taxes.</li>
                    <li>Cheque payments are subject to realization.</li>
                    <li>If the advance is made through old gold exchange or sales return, the gold rate on the date of
                        the advance payment will be applicable
                        for the purchase.</li>
                    <li>The advance amount is non-transferable.</li>
                    <li>This scheme is valid only for the purchase of gold ornaments. It is not applicable for gold
                        coins or gold bars.</li>
                    <li>Any changes in government tax rules will be applicable at the time of final billing.</li>
                    *The final billing gold rate will be the fixed gold rate as applicable under the scheme.
                </ol>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="height: 10px;">&nbsp;</td>
        </tr>
        <tr class="bold-row">
            <td>
                <table>
                    <tr>
                        <td>I acknowledge all the terms and conditions of this advance,</td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr style="text-align:right;">
                        <td><?php echo $records[0]['payment_branch'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="height: 10px;">&nbsp;</td>
        </tr>
        <tr class="bold-row">
            <td>
                <table>
                    <tr>
                        <td>CUSTOMER SIGNATURE</td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr style="text-align:right;">
                        <td>AUTHORISED SIGNATORY</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>