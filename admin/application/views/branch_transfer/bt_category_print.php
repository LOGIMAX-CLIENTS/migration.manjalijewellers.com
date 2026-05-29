<?php


function moneyFormatIndia($num)

{

    $nums = explode(".", $num);

    if (count($nums) > 2) {

        return "0";
    } else {

        if (count($nums) == 1) {

            $nums[1] = "00";
        }

        $num = $nums[0];

        $explrestunits = "";

        if (strlen($num) > 3) {

            $lastthree = substr($num, strlen($num) - 3, strlen($num));

            $restunits = substr($num, 0, strlen($num) - 3);

            $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;

            $expunit = str_split($restunits, 2);

            for ($i = 0; $i < sizeof($expunit); $i++) {

                if ($i == 0) {

                    $explrestunits .= (int)$expunit[$i] . ",";
                } else {

                    $explrestunits .= $expunit[$i] . ",";
                }
            }

            $thecash = $explrestunits . $lastthree;
        } else {

            $thecash = $num;
        }

        return $thecash . "." . $nums[1];
    }
}
?>
<html>

<head>

    <meta charset="utf-8">

    <title>Branch Copy - <?php echo $btrans[0]['branch_trans_code']; ?></title>

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bt_ack.css">

    <!--	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/receipt_temp.css">-->

    <style type="text/css">
        body,
        html {

            margin-bottom: 0
        }


        @page {
            margin: 40;
            padding: 10; // you can set margin and padding 0 
        }

        .first_box {
            display: flex;
            width: 100%;
            height: 100px;
        }


        .a1 {
            border: 1px solid black;
            border-collapse: collapse;
            border-bottom: none;
            width: 100%;
        }

        .logo {
            height: 80px;
            width: 80px;
        }

        .second_box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .header_label {

            height: 30px !important;

            vertical-align: top;

            max-height: 10px !important;

        }

        .boxes {
            display: flex;
            gap: 10px;
            border: 1px solid red;
        }

        .last_box {
            border: 1px solid black;
            border-top: none;

            border-collapse: collapse;


        }


        .signatory {
            text-align: right;
            border: 1px solid black;

        }

        /* internal */
        .PDFReceipt * {

            font-family: sans-serif;

            font-size: 12px;

        }

        .PDF_CusReceipt * {

            font-family: sans-serif;

            font-size: 12px;

        }

        .PDF_receipt_thermal * {

            font-family: sans-serif;

            font-size: 6px;

        }

        .margin {
            margin: -20px !important;
        }

        article,
        .PDF_CusReceipt.meta table,
        .PDFReceipt .inventory table {
            margin: 0 0 1em;
        }

        article,
        .PDF_receipt_thermal.meta table,
        .PDFReceipt .inventory table {
            margin: 0 0 1em;
        }

        article,
        .PDFReceipt.meta table,
        .PDFReceipt .inventory table {
            margin: 0 0 3em;
        }

        article:after {
            clear: both;
            content: "";
            display: table;
        }



        .PDFReceipt .meta:after,
        .PDFReceipt .balance:after {
            clear: both;
            content: "";
            display: table;
        }



        /* table meta */



        .PDFReceipt .meta th {
            width: 20%;
        }

        .PDFReceipt .meta td {
            width: 30%;
        }


        .PDF_CusReceipt .meta th,
        .name {
            width: 20%;
        }

        .PDF_receipt_thermal .meta th,
        .name {
            width: 20%;
        }

        .PDF_CusReceipt .meta td {
            width: 30%;
        }

        .PDF_receipt_thermal .meta td {
            width: 30%;
        }



        /* table items */



        .PDFReceipt .inventory {
            clear: both;
            width: 100%;
        }

        .PDFReceipt .inventory th {
            font-weight: bold;
            text-align: center;
        }

        .PDF_CusReceipt .meta th {
            font-weight: bold;
            text-align: left;
        }

        .PDF_receipt_thermal .meta th {
            font-weight: bold;
            text-align: left;
        }




        .PDFReceipt .inventory td:nth-child(1) {
            width: 26%;
        }

        .PDFReceipt .inventory td:nth-child(2) {
            width: 38%;
        }

        .PDFReceipt .inventory td:nth-child(3) {
            text-align: right;
            width: 12%;
        }

        .PDFReceipt .inventory td:nth-child(4) {
            text-align: right;
            width: 12%;
        }

        .PDFReceipt .inventory td:nth-child(5) {
            text-align: right;
            width: 12%;
        }



        /* table balance */



        .PDFReceipt .balance th {
            width: 40%;
        }

        .PDFReceipt .balance td {
            width: 36%;
        }

        .PDFReceipt .balance td {
            text-align: right;
        }







        .PDFReceipt table tr td {

            height: 1px;

            font-size: 11px !important;

        }

        .PDF_CusReceipt table tr td {

            height: 1px;

            font-size: 11px !important;

        }

        .PDF_receipt_thermal table tr td {

            height: 1px;

            /* font-size: 8px !important; */

        }



        .receiptDts div {

            height: 30px;

            text-align: center;

            border-left: none
        }

        .PDFReceipt .address {

            vertical-align: top;

            font-size: 12px;

            padding-top: 8px;

            border-top: 1px solid #939393;

        }



        .PDFReceipt .heading {

            font: bold 80% sans-serif;

            letter-spacing: 0.5em;

            text-transform: uppercase;

            background: #393939;
            border-radius: 0.25em;
            color: #FFF;
            margin: 0 0 1em;
            padding: 0.5em 0;

            height: 20px;
            text-align: center;

        }

        .PDFReceipt .values {

            background-color: #fff;

            color: #676767;

        }

        .PDFReceipt .txtAckowlege {

            height: 40px;

            text-align: left;

            padding-top: 15px;

            border-top: 1px solid #939393;

            font: bold 80% sans-serif;

        }

        .PDFReceipt p {

            height: 4px;

        }



        .PDFReceipt .alignText {

            float: right;

        }

        .PDFReceipt .useraddr {

            margin-bottom: 80px;

            font-size: 15px !important;

            font-weight: bold;

        }

        .item_dashed1 {
            border-top: 1px dashed black;
            border-bottom: 0px;
            margin-left: 0px !important;
            width: 1500% !important;
            padding-top: -1px !important;

        }

        .item_dashed2 {
            border-top: 1px dashed black;
            border-bottom: 0px;
            margin-left: 0px !important;
            width: 1700% !important;
            padding-top: -1px !important;

        }

        .item_dashed {
            border-top: 1px dashed black;
            border-bottom: 0px;
            margin-left: 0px !important;
            width: 2200% !important;
            padding-top: -1px !important;

        }

        .sumamry_dashed {
            border-top: 1px dashed black;
            border-bottom: 0px;
            margin-left: 0px !important;
            width: 1300% !important;
            padding-top: -1px !important;

        }

        .PDFReceipt table {
            font-size: 50%;
            table-layout: fixed;
            width: 100%;
        }

        .PDFReceipt table {}
    </style>

</head>

<body>

    <div class="PDFReceipt">


        <h1 style="text-align: center;">Delivery Challan - Branch Transfer</h1>

        <div class="a1">
            <div class="first_box">
            <?php 
                        
                        $imagePath = FCPATH . 'assets/img/ams_logo.png';
                        $imgData = base64_encode(file_get_contents($imagePath));
                        $src = 'data:image/png;base64,' . $imgData;
                    ?>
                <div class="">
                    <img alt="" class="logo" src="<?php echo $src; ?>">
                </div>

                <div class="" style="text-align:center;">
                    <h2><?php echo $comp_details['short_code']; ?></h2>

                    <p style="margin-top:-7px !important;"><?php echo $comp_details['address1']; ?> </p>

                    <p><?php echo $comp_details['address2'] . $comp_details['city'] . '-' . $comp_details['pincode'] . '.'; ?>

                    </p>
                </div>
            </div>

        </div>

        <table style="border:1px solid black; border-collapse: collapse;table-layout: fixed;">
            <tr>

                <td style="border:1px solid black;vertical-align:top;">

                    <p>Party</p>

                    &nbsp;

                    <h2><?php echo $comp_details['short_code']; ?></h2>

                    <div style="margin-top:-7px !important;"><?php echo $btrans[0]['from_branch_address1']; ?> </div>

                    <div><?php echo $btrans[0]['from_branch_address2'] . $btrans[0]['from_city_name'] . '-' . $btrans[0]['from_pincode'] . '.'; ?></div>

                    <div><?php echo 'GSTIN:' . $btrans[0]['from_gst']; ?></div>

                    <div style=""><?php echo 'State : ' . $btrans[0]['from_state_name']; ?></div>

                    <br>



                </td>
                <td style="border: 1px solid black; padding: 0; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: 1px solid black; padding: 5px;;">
                                <span style="font-weight:bold;">Document No :</span><?php echo $btrans[0]['branch_transfer_id']; ?>
                            </td>
                            <td style="border: 1px solid black; padding: 5px;;">

                                <span style="font-weight:bold;">Created Date :</span><?php echo $btrans[0]['created_time']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 5px;;">

                            </td>
                            <td style="border: 1px solid black; padding: 5px;;">
                                <span style="font-weight:bold;">
                                    Mode/Terms of payment </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 5px;;">
                                <span style="font-weight:bold;"> Reference No & Date :</span><?php echo $btrans[0]['branch_trans_code']; ?> <br>
                                <?php echo $btrans[0]['approve_date']; ?>
                            </td>
                            <td style="border: 1px solid black; padding: 5px;;">
                                <!-- Add the delivery note here -->
                                <span style="font-weight:bold;"> Other References </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 5px;">
                                <span style="font-weight:bold;">Dispatched Through :</span>
                                <br><?php echo $btrans[0]['from_branch']; ?>
                            </td>
                            <td style="border: 1px solid black; padding: 5px;">
                                <!-- Add supplier reference here -->
                                <span style="font-weight:bold;">Destination :</span><?php echo $btrans[0]['to_branch']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; padding: 5px;">
                                <span style="font-weight:bold;"> Landing Date :</span>
                            </td>
                            <td style="border: 1px solid black; padding: 5px;">
                                <!-- Add supplier reference here -->

                            </td>
                        </tr>
                        <!-- Add more rows as per your design -->
                    </table>
                </td>


            </tr>




            <tr>

                <td style="border:1px solid black;vertical-align:top;">

                    <p>Dispatch to</p>

                    &nbsp;

                    <h2><?php echo $comp_details['short_code']; ?></h2>

                    <div style="margin-top:-7px !important;"><?php echo $btrans[0]['to_branch_address1']; ?> </div>

                    <div><?php echo $btrans[0]['to_branch_address2'] . $btrans[0]['to_city_name'] . '-' . $btrans[0]['to_pincode'] . '.'; ?></div>

                    <div><?php echo 'GSTIN:' . $btrans[0]['to_gst']; ?></div>

                    <div style=""><?php echo 'State : ' . $btrans[0]['to_state_name']; ?></div>

                </td>


                <td>



                </td>
            </tr>


        </table>

        <div class="content-wrapper">

            <div class="box">

                <div class="box-body">

                    <div class="container-fluid">

                        <div class="row">

                            <div class="col-xs-12">

                                <div class="table-responsive">



                                    <?php if ($type == 2) { //print_type

                                        if ($btrans[0]['transfer_item_type'] == 1)         //TAGGED

                                        { ?>

                                            <table id="pp" class="table text-center" style="border:solid 1px black;border-collapse:collapse;vertical-align:top;border-top:none">

                                                <tr>

                                                    <th width="7%;" style="text-align:left;border:solid 1px black">S.NO</th>

                                                    <th width="15%;" style="text-align:left;border:solid 1px black">TAG CODE</th>

                                                    <?php if ($btrans[0]['collections_required'] == 1) { ?>

                                                        <th width="20%;" style="text-align:left;border:solid 1px black">CATEGORY</th>

                                                    <?php } ?>

                                                    <th width="25%;" style="text-align:left;border:solid 1px black">ITEMS</th>

                                                    <th width="20%;" style="text-align:right;border:solid 1px black">PCS</th>

                                                    <th width="20%;" style="text-align:right;border:solid 1px black">GWT</th>

                                                </tr>


                                                <?php

                                                $i = 1;

                                                $pcs = 0;
                                                $gross_wt = 0;
                                                $tot_sales_value = 0;

                                                foreach ($btrans as $items) {

                                                    $pcs += $items['piece'];

                                                    $gross_wt += $items['gross_wt'];

                                                ?>

                                                    <tr>

                                                        <td style="text-align:left;border:solid 1px black"><?php echo $i; ?></td>

                                                        <td style="text-align:left;border:solid 1px black"><?php echo $items['tag_code']; ?></td>

                                                        <?php if ($btrans[0]['collections_required'] == 1) { ?>

                                                            <td style="text-align:left;border:solid 1px black"><?php echo $items['collection_name']; ?></td>

                                                        <?php } ?>

                                                        <td style="text-align:left;border:solid 1px black"><?php echo $items['parent_prods_name']; ?></td>

                                                        <td style="text-align:right;border:solid 1px black"><?php echo $items['piece']; ?></td>

                                                        <td style="text-align:right;border:solid 1px black"><?php echo $items['gross_wt']; ?></td>

                                                    </tr>

                                                <?php

                                                    $i++;
                                                }

                                                ?>


                                                <tfoot>

                                                    <tr style="font-weight:bold;">

                                                        <td style="text-align:left;border:solid 1px black"></td>

                                                        <td style="text-align:left;border:solid 1px black"></td>

                                                        <td style="text-align:left;border:solid 1px black"></td>

                                                        <td style="text-align:left;border:solid 1px black">TOTAL</td>

                                                        <td style="text-align:right;border:solid 1px black"><?php echo $pcs; ?></td>

                                                        <td style="text-align:right;border:solid 1px black"><?php echo number_format($gross_wt, '3', '.', ''); ?></td>

                                                    </tr>

                                                    <tr>
                                                        <td colspan="6" style="text-align:left;border:solid 1px black;font-style:italic;">No E-way bill is required to be generated as per the Goods covered under this Invoice are exempted as per serial
                                                            NO.4/5 to the Annexure to Rule 138(14) of the CGST Rule </td>
                                                    </tr>

                                                </tfoot>

                                            </table>

                                        <?php } else if ($btrans[0]['transfer_item_type'] == 2) { ?> <!-- NON TAGGED -->



                                            <table id="pp" class="table text-center">

                                                <tr>

                                                    <th width="7%;" style="text-align:left;">S.NO</th>

                                                    <th width="25%;" style="text-align:left;">ITEMS</th>

                                                    <th width="20%;" style="text-align:right;">PCS</th>

                                                    <th width="20%;" style="text-align:right;">GWT</th>

                                                </tr>

                                                <tr>

                                                    <td>
                                                        <hr class="detail_dashed">
                                                    </td>

                                                </tr>

                                                <?php

                                                $i = 1;

                                                $pcs = 0;
                                                $gross_wt = 0;
                                                $tot_sales_value = 0;

                                                foreach ($btrans as $items) {

                                                    $pcs += $items['piece'];

                                                    $gross_wt += $items['gross_wt'];

                                                ?>

                                                    <tr>

                                                        <td style="text-align:left;"><?php echo $i; ?></td>

                                                        <td style="text-align:left;"><?php echo $items['tag_code']; ?></td>

                                                        <td style="text-align:left;"><?php echo $items['parent_prods_name']; ?></td>

                                                        <td style="text-align:right;"><?php echo $items['piece']; ?></td>

                                                        <td style="text-align:right;"><?php echo $items['gross_wt']; ?></td>

                                                    </tr>

                                                <?php

                                                    $i++;
                                                }

                                                ?>

                                                <tr>

                                                    <td>
                                                        <hr class="detail_dashed">
                                                    </td>

                                                </tr>

                                                <tfoot>

                                                    <tr style="font-weight:bold;">

                                                        <td style="text-align:left;"></td>

                                                        <td style="text-align:left;">TOTAL</td>

                                                        <td style="text-align:right;"><?php echo $pcs; ?></td>

                                                        <td style="text-align:right;"><?php echo number_format($gross_wt, '3', '.', ''); ?></td>

                                                    </tr>

                                                </tfoot>

                                            </table>



                                        <?php } else if ($btrans[0]['transfer_item_type'] == 4)  // OLD METAL

                                        { ?>



                                            <table id="pp" class="table text-center">

                                                <tr>

                                                    <th width="7%;" style="text-align:left;">S.NO</th>

                                                    <th width="7%;" style="text-align:left;">TAG NO</th>

                                                    <th width="15%;" style="text-align:left;">CATEGORY</th>

                                                    <th width="20%;" style="text-align:right;">Piece</th>

                                                    <th width="20%;" style="text-align:right;">G.Wt</th>

                                                </tr>

                                                <tr>

                                                    <td>
                                                        <hr class="sumamry_dashed">
                                                    </td>

                                                </tr>

                                                <?php

                                                $i = 1;

                                                $net_wt = 0;
                                                $gross_wt = 0;
                                                $amount = 0;
                                                $piece = 0;

                                                foreach ($btrans as $items) {

                                                    $net_wt += $items['net_wt'];

                                                    $gross_wt += $items['grs_wt'];

                                                    $piece += $items['piece'];

                                                ?>

                                                    <tr>

                                                        <td style="text-align:left;"><?php echo $i; ?></td>

                                                        <td style="text-align:left;"><?php echo $items['tag_code']; ?></td>

                                                        <td style="text-align:left;"><?php echo $items['name']; ?></td>

                                                        <td style="text-align:right;"><?php echo $items['piece']; ?></td>

                                                        <td style="text-align:right;"><?php echo $items['grs_wt']; ?></td>


                                                    </tr>

                                                <?php

                                                    $i++;
                                                }

                                                ?>

                                                <tr>

                                                    <td>
                                                        <hr class="sumamry_dashed">
                                                    </td>

                                                </tr>

                                                <tfoot>

                                                    <tr style="font-weight:bold;">

                                                        <td style="text-align:left;"></td>

                                                        <td style="text-align:left;"></td>

                                                        <td style="text-align:left;">TOTAL</td>

                                                        <td style="text-align:right;"><?php echo number_format($piece, '0', '.', ''); ?></td>

                                                        <td style="text-align:right;"><?php echo number_format($gross_wt, '3', '.', ''); ?></td>

                                                    </tr>

                                                </tfoot>

                                            </table>

                                        <?php }
                                    } else if ($type == 1) { ?>

                                        <?php

                                        if ($btrans[0]['transfer_item_type'] == 1 || $btrans[0]['transfer_item_type'] == 2)     // TAGGED

                                        { ?>

                                            <table id="pp" class="table text-center" style="border:solid 1px black;border-collapse:collapse;border-top:none;">

                                                <tr>

                                                    <th width="7%;" style="text-align:left;border:solid 1px black">S.NO</th>

                                                    <?php if ($btrans[0]['collections_required'] == 1) { ?>

                                                        <th width="20%;" style="text-align:left;border:solid 1px black">CATEGORY</th>

                                                    <?php } ?>

                                                    <th width="25%;" style="text-align:left;border:solid 1px black">ITEMS</th>

                                                    <th width="20%;" style="text-align:right;border:solid 1px black">PCS</th>

                                                    <th width="20%;" style="text-align:right;border:solid 1px black">GWT</th>

                                                </tr>

                                                <!-- <tr>

                                                    <td>
                                                        <hr class="sumamry_dashed">
                                                    </td>

                                                </tr> -->

                                                <?php

                                                $i = 1;

                                                $pcs = 0;
                                                $gross_wt = 0;
                                                $tot_sales_value = 0;

                                                foreach ($btrans as $items) {

                                                    $pcs += $items['piece'];

                                                    $gross_wt += $items['gross_wt'];

                                                ?>

                                                    <tr>

                                                        <td style="text-align:left;border:solid 1px black"><?php echo $i; ?></td>

                                                        <?php if ($btrans[0]['collections_required'] == 1) { ?>

                                                            <td style="text-align:left;border:solid 1px black"><?php echo $items['collection_name']; ?></td>

                                                        <?php } ?>

                                                        <td style="text-align:left;border:solid 1px black"><?php echo $items['product_name']; ?></td>

                                                        <td style="text-align:right;border:solid 1px black"><?php echo $items['piece']; ?></td>

                                                        <td style="text-align:right;border:solid 1px black"><?php echo $items['gross_wt']; ?></td>

                                                    </tr>

                                                <?php

                                                    $i++;
                                                }

                                                ?>

                                                <!-- <tr>

                                                    <td>
                                                        <hr class="sumamry_dashed">
                                                    </td>

                                                </tr> -->

                                                <tfoot>

                                                    <tr style="font-weight:bold;">

                                                        <td style="text-align:left;border:solid 1px black"></td>

                                                        <td style="text-align:left;border:solid 1px black"></td>

                                                        <td style="text-align:left;border:solid 1px black">TOTAL</td>

                                                        <td style="text-align:right;border:solid 1px black"><?php echo $pcs; ?></td>

                                                        <td style="text-align:right;border:solid 1px black"><?php echo number_format($gross_wt, '3', '.', ''); ?></td>

                                                    </tr>

                                                </tfoot>

                                                <!-- <tr>

                                                    <td>
                                                        <hr class="sumamry_dashed">
                                                    </td>

                                                </tr> -->
                                                <tr>
                                                    <td colspan="5" style="text-align:left;border:solid 1px black;font-style:italic;">No E-way bill is required to be generated as per the Goods covered under this Invoice are exempted as per serial
                                                        NO.4/5 to the Annexure to Rule 138(14) of the CGST Rule </td>
                                                </tr>

                                            </table><br>

                                        <?php } else if ($btrans[0]['transfer_item_type'] == 3)    // Old Metal

                                        { ?>

                                             <?php

                                            if (sizeof($purchase_item_details['old_metal_details']) > 0) { ?>
                                                   
                                                <br><b><span style="text-align:center;">OLD METAL</span></b>

                                                <table id="pp" class="table text-center">

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <tr style="text-transform:uppercase;">

                                                        <th width="2%;">S.NO</th>

                                                        <th width="5%;">Type</th>

                                                        <th width="5%;">Bill NO</th>

                                                        <th width="5%;">G.Wt</th>

                                                        <th width="5%;">N.Wt</th>

                                                        <th width="5%;">Dia.Wt</th>

                                                        <th width="5%;" style="text-align:right;">Value(Rs)</th>

                                                    </tr>

                                                    <!-- <?php
                                                            if ($items['remark'] != '') { ?>
                                                                    <tr>
                                                                            <td></td>
                                                                            <td colspan="5">REMARKS :- <?php echo $items['remark']; ?></td>
                                                                        </tr>
                                                                <?php }
                                                                ?> -->

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <?php

                                                    $i = 1;

                                                    $net_wt = 0;
                                                    $gross_wt = 0;
                                                    $amount = 0;
                                                    $dia_wt=0;

                                                    foreach ($purchase_item_details['old_metal_details'] as $items) {

                                                        $net_wt += $items['net_wt'];

                                                        $gross_wt += $items['grs_wt'];

                                                        $amount += $items['amount'];

                                                        $dia_wt+=$items['dia_wt'];


                                                        $tot_net_wt += $items['net_wt'];

                                                        $tot_gross_wt += $items['grs_wt'];

                                                        $tot_amount += $items['amount'];

                                                        $tot_dia_wt+=$items['dia_wt'];


                                                    ?>

                                                        <tr style="text-align:center;">

                                                            <td><?php echo $i; ?></td>

                                                            <td><?php echo $items['item_type']; ?></td>

                                                            <td><?php echo $items['bill_no']; ?></td>

                                                            <td><?php echo $items['grs_wt']; ?></td>

                                                            <td><?php echo $items['net_wt']; ?></td>

                                                            <td><?php echo number_format(floatval($items['dia_wt']), 3); ?></td>

                                                            <td style="text-align:right;"><?php echo $items['amount']; ?></td>

                                                        </tr>

                                                    <?php

                                                        $i++;
                                                    }

                                                    ?>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <tr style="text-transform:uppercase;">

                                                        <th colspan="3">SUB TOTAL</th>

                                                        <th><?php echo number_format($tot_gross_wt, 3, '.', ''); ?></th>

                                                        <th><?php echo number_format($tot_net_wt, 3, '.', ''); ?></th>

                                                        <th><?php echo number_format($tot_dia_wt,3,'.','');?></th>


                                                        <th style="text-align:right;"><?php echo number_format($tot_amount, 2, '.', ''); ?></th>

                                                    </tr>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                </table>

                                            <?php } ?>


                                            <?php

                                            if (sizeof($purchase_item_details['sales_return_details']) > 0) { ?>

                                                <br><b><span style="text-align:center;">SALES RETURN</span></b>



                                                <table id="pp" class="table text-center">

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <tr style="text-transform:uppercase;">

                                                        <th width="2%;">S.NO</th>

                                                        <th width="5%;">CATEGORY</th>

                                                        <th width="5%;">Type</th>

                                                        <th width="5%;" style="text-align:right;">G.Wt</th>

                                                        <th width="5%;" style="text-align:right;">Value(Rs)</th>

                                                    </tr>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <?php

                                                    $i = 1;

                                                    $sales_ret_net_wt = 0;
                                                    $sales_ret_gross_wt = 0;
                                                    $sales_ret_amount = 0;

                                                    foreach ($purchase_item_details['sales_return_details'] as $items) {

                                                        $sales_ret_net_wt += $items['net_wt'];

                                                        $sales_ret_gross_wt += $items['grs_wt'];

                                                        $sales_ret_amount += $items['amount'];



                                                    ?>

                                                        <tr style="text-align:center;">

                                                            <td><?php echo $i; ?></td>

                                                            <td><?php echo $items['name']; ?></td>

                                                            <td><?php echo $items['product_name']; ?></td>

                                                            <td style="text-align:right;"><?php echo $items['grs_wt']; ?></td>

                                                            <td style="text-align:right;"><?php echo $items['amount']; ?></td>

                                                        </tr>

                                                    <?php

                                                        $i++;
                                                    }

                                                    ?>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <tr style="text-transform:uppercase;">

                                                        <th colspan="3">SUB TOTAL</th>

                                                        <th style="text-align:right;"><?php echo number_format($sales_ret_gross_wt, 3, '.', ''); ?></th>

                                                        <th style="text-align:right;"><?php echo number_format($sales_ret_amount, 2, '.', ''); ?></th>

                                                    </tr>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                </table>

                                            <?php } ?>



                                            <?php



                                            if (sizeof($purchase_item_details['partly_sales_details']) > 0) { ?>

                                                <br><b><span style="text-align:center;">PARTLY SALE</span></b>



                                                <table id="pp" class="table text-center">

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <tr style="text-transform:uppercase;">

                                                        <th width="2%;">S.NO</th>

                                                        <th width="5%;">CATEGORY</th>

                                                        <th width="5%;">Piece</th>

                                                        <th width="5%;">G.Wt</th>

                                                        <th width="5%;" style="text-align:right;">Value(Rs)</th>

                                                    </tr>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <?php

                                                    $i = 1;

                                                    $net_wt = 0;
                                                    $gross_wt = 0;
                                                    $amount = 0;
                                                    $piece = 0;

                                                    foreach ($purchase_item_details['partly_sales_details'] as $items) {

                                                        $net_wt += $items['net_wt'];

                                                        $gross_wt += $items['grs_wt'];

                                                        $amount += $items['amount'];

                                                        $piece += $items['piece'];



                                                    ?>

                                                        <tr style="text-align:center;">

                                                            <td><?php echo $i; ?></td>

                                                            <td><?php echo $items['name']; ?></td>

                                                            <td style="text-align:right;"><?php echo $items['piece']; ?></td>

                                                            <td style="text-align:right;"><?php echo $items['grs_wt']; ?></td>

                                                            <td style="text-align:right;"><?php echo $items['amount']; ?></td>

                                                        </tr>

                                                    <?php

                                                        $i++;
                                                    }

                                                    ?>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <tr style="text-transform:uppercase;">

                                                        <th colspan="3">SUB TOTAL</th>

                                                        <th style="text-align:right;"><?php echo number_format($piece, 0, '.', ''); ?></th>

                                                        <th style="text-align:right;"><?php echo number_format($gross_wt, 3, '.', ''); ?></th>

                                                        <th style="text-align:right;"><?php echo number_format($amount, 2, '.', ''); ?></th>

                                                    </tr>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                </table>

                                            <?php }

                                            ?>



                                        <?php } else if ($btrans[0]['transfer_item_type'] == 3) { ?>

                                            <table id="pp" class="table text-center">

                                                <tr>

                                                    <th width="7%;" style="text-align:left;">S.NO</th>

                                                    <?php if ($btrans[0]['collections_required'] == 1) { ?>

                                                        <th width="20%;" style="text-align:left;">COLLECTION</th>

                                                    <?php } ?>

                                                    <th width="25%;" style="text-align:left;">ITEMS</th>

                                                    <th width="20%;" style="text-align:right;">PCS</th>

                                                    <th width="20%;" style="text-align:right;">GWT</th>

                                                </tr>

                                                <tr>

                                                    <td>
                                                        <hr class="sumamry_dashed">
                                                    </td>

                                                </tr>

                                                <?php

                                                $i = 1;

                                                $pcs = 0;
                                                $gross_wt = 0;
                                                $tot_sales_value = 0;

                                                foreach ($btrans as $items) {

                                                    $pcs += $items['totalitems'];

                                                    $gross_wt += $items['weight'];

                                                ?>

                                                    <tr>

                                                        <td style="text-align:left;"><?php echo $i; ?></td>

                                                        <?php if ($btrans[0]['collections_required'] == 1) { ?>

                                                            <td style="text-align:left;"><?php echo $items['collection_name']; ?></td>

                                                        <?php } ?>

                                                        <td style="text-align:left;"><?php echo $items['product_name']; ?></td>

                                                        <td style="text-align:left;"><?php echo $items['totalitems']; ?></td>

                                                        <td style="text-align:left;"><?php echo $items['weight']; ?></td>

                                                    </tr>

                                                <?php

                                                    $i++;
                                                }

                                                ?>

                                                <tr>

                                                    <td>
                                                        <hr class="sumamry_dashed">
                                                    </td>

                                                </tr>

                                                <tfoot>

                                                    <tr style="font-weight:bold;">

                                                        <td style="text-align:left;"></td>

                                                        <td style="text-align:left;"></td>

                                                        <td style="text-align:left;">TOTAL</td>

                                                        <td style="text-align:right;"><?php echo $pcs; ?></td>

                                                        <td style="text-align:right;"><?php echo number_format($gross_wt, '3', '.', ''); ?></td>

                                                    </tr>

                                                </tfoot>

                                                <tr>

                                                    <td>
                                                        <hr class="sumamry_dashed">
                                                    </td>

                                                </tr>

                                            </table><br>

                                        <?php }

                                        ?>



                                    <?php } elseif ($type == 3) { ?>



                                        <?php

                                        if ($btrans[0]['transfer_item_type'] == 1  || $btrans[0]['transfer_item_type'] == 2)     // TAGGED

                                        { ?>

                                            <table id="pp" class="table text-center" style="border:1px solid black;border-collapse:collapse;width:100%;border-top:none">

                                                <tr>

                                                    <th width="7%;" style="text-align:left; border-bottom:1px solid black;">S.NO</th>

                                                    <th width="20%;" style="text-align:left; border:1px solid black;border-top:none;">CATEGORY</th>

                                                    <th width="20%;" style="text-align:left; border:1px solid black">HSN Code</th>

                                                    <th width="20%;" style="text-align:right; border:1px solid black;">Quantity</th>

                                                    <th width="20%;" style="text-align:right; border:1px solid black;">Rate</th>

                                                    <th width="20%;" style="text-align:right; border:1px solid black;">Amount</th>

                                                </tr>

                                                <!-- <tr>

                                                    <td>

                                                        <hr class="sumamry_dashed">

                                                    </td>

                                                </tr> -->

                                                <?php

                                                $i = 1;

                                                $pcs = 0;
                                                $gross_wt = 0;
                                                $tot_sales_value = 0;
                                                $buy_rate = 0;
                                                $item_amount = 0;
                                                $total_amount = 0;
                                                $rate = 0;
                                                $total_rate = 0;
                                                foreach ($btrans as $items) {

                                                    $pcs += $items['piece'];

                                                    $gross_wt += $items['gross_wt'];
                                                    $buy_rate += $items['buy_rate'];

                                                    $rate = $items['buy_rate'] / $items['gross_wt'];
                                                    $total_rate += $rate;
                                                ?>


                                                    <tr>

                                                        <td style="text-align:left;border:1px solid black"><?php echo $i; ?></td>

                                                    
                                                        <td style="text-align:left;border:1px solid black"><?php echo $items['name']; ?></td>


                                                        <td style="text-align:left;border:1px solid black"><?php echo $items['hsn_code']; ?></td>

                                                        <td style="text-align:right;border:1px solid black"><?php echo $items['gross_wt']; ?></td>

                                                        <td style="text-align:right;border:1px solid black"><?php echo moneyFormatIndia(number_format($rate, '2', '.', '')); ?></td>
                                                        <td style="text-align:right;border:1px solid black"><?php echo moneyFormatIndia(number_format($items['buy_rate'], '2', '.', '')); ?></td>
                                                    </tr>

                                                <?php

                                                    $i++;
                                                }

                                                ?>



                                                <tfoot>

                                                    <tr style="font-weight:bold;">

                                                        <td style="text-align:left;border:1px solid black"></td>

                                                        <td style="text-align:left;border:1px solid black">TOTAL</td>

                                                        <td style="text-align:left;border:1px solid black">

                                                        </td>


                                                        <td style="text-align:right;border:1px solid black"><?php echo $gross_wt; ?></td>


                                                        <td style="text-align:right;border:1px solid black">
                                                            <?php echo moneyFormatIndia(number_format($total_rate, '2', '.', '')); ?>
                                                        </td>

                                                        <td style="text-align:right;border:1px solid black">

                                                            <?php echo moneyFormatIndia(number_format($buy_rate, '2', '.', '')); ?>
                                                        </td>





                                                    </tr>

                                                </tfoot>



                                            </table>

                                            <?php

                                            $amt_in_words_total   = $this->ret_billing_model->no_to_words($buy_rate);

                                            ?>


                                            <table height="50px" style="border: 1px solid black;border-collapse:collapse;border-top:none;">
                                                <tr>
                                                    <td>Amount Chargeable(in words) <br>

                                                        <b><?= 'INR ' . $amt_in_words_total; ?></b>
                                                    </td>

                                                    <td style="text-align: right;">
                                                        E & OE
                                                    </td>
                                                </tr>
                                            </table>




                                            <table style="border:1px solid black;border-collapse:collapse;width:100%;border-top:none">
                                                <tr>
                                                    <td style="text-align: center;border-right:1px solid black;font-weight:bold;">HSN /SAC</td>

                                                    <td style="width:25%;text-align:right;font-weight:bold;">Taxable Value</td>
                                                </tr>

                                                <?php

                                                foreach ($btrans as $items) { ?>



                                                    <tr>
                                                        <td style="text-align: center;border:1px solid black"><?php echo $items['hsn_code'] ?></td>

                                                        <td style="width:25%;text-align:right;border:1px solid black"><?php echo moneyFormatIndia(number_format($buy_rate, '2', '.', '')) ?></td>
                                                    </tr>

                                                <?php } ?>

                                                <tr>
                                                    <td style="text-align: right;border:1px solid black;font-weight:bold">Total</td>

                                                    <td style="width:25%;text-align:right;border:1px solid black"><?php echo moneyFormatIndia(number_format($buy_rate, '2', '.', '')) ?></td>
                                                </tr>



                                            </table>


                                            <table class="last_box" style="width: 100%;" height="100px">

                                                <tr class="z1">Tax Amount Nil

                                                    <td style="width: 50%;;border:"> Tax Amount Nil</td>
                                                    <td style="width: 50%;"></td>

                                                </tr>

                                                <tr class="z1" style="font-style: italic;width:50%;">
                                                    <td style="width: 50%;"> No E-way bill is required to be generated as per the Goods covered under this Invoice are exempted as per serial
                                                        NO.4/5 to the Annexure to Rule 138(14) of the CGST Rules</td>
                                                    <td style="width: 50%;"></td>
                                                </tr>

                                                <tr class="signatory">
                                                    <td style="width: 50%;"></td>

                                                    <td style="width: 50%;">for Daeiou Jewels Private Limited <br>
                                                        <br>
                                                        Authorised Signatory
                                                    </td>
                                                </tr>
                                            </table>










                                        <?php }



                                        if ($btrans[0]['transfer_item_type'] == 3) { ?>


                                             <?php

                                            if (sizeof($purchase_item_details['old_metal_details']) > 0) { ?>
                                                
                                               <!-- <b><span style="text-align:center;">OLD METAL</span></b> -->

                                               <table id="pp" class="table text-center" style="border:1px solid black;border-collapse:collapse;width:100%;border-top:none">

                                                    <!-- <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr> -->

                                                    <tr style="text-transform:uppercase;">

                                                        <th style="text-align:left; border-bottom:1px solid black;" width="2%;">S.NO</th>

                                                        <th style="text-align:left; border-bottom:1px solid black;" width="5%;">Category</th>

                                                        <th style="text-align:left; border-bottom:1px solid black;" width="5%;">Piece</th>

                                                        <th style="text-align:left; border-bottom:1px solid black;" width="5%;">G.Wt</th>

                                                        <!-- <th width="5%;">N.Wt</th> -->

                                                        <!-- <th width="5%;">Dia.Wt</th> -->

                                                        <th  width="5%;" style="text-align:right;border-bottom:1px solid black;">Value(Rs)</th>

                                                    </tr>

                                                    <!-- <?php
                                                            if ($items['remark'] != '') { ?>
                                                                    <tr>
                                                                            <td></td>
                                                                            <td colspan="5">REMARKS :- <?php echo $items['remark']; ?></td>
                                                                        </tr>
                                                                <?php }
                                                                ?> -->

                                                    <!-- <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr> -->

                                                    <?php

                                                    $i = 1;

                                                    $net_wt = 0;
                                                    $gross_wt = 0;
                                                    $amount = 0;
                                                    $dia_wt=0;

                                                    foreach ($purchase_item_details['old_metal_details'] as $items) {

                                                        $net_wt += $items['net_wt'];

                                                        $gross_wt += $items['grs_wt'];

                                                        $amount += $items['amount'];

                                                        $dia_wt+=$items['dia_wt'];


                                                        $tot_net_wt += $items['net_wt'];

                                                        $tot_gross_wt += $items['grs_wt'];

                                                        $tot_amount += $items['amount'];

                                                        $tot_dia_wt+=$items['dia_wt'];


                                                    ?>

                                                        <tr style="text-align:center;">

                                                            <td style="text-align:left; border-bottom:1px solid black;"><?php echo $i; ?></td>

                                                            <td style="text-align:left; border-bottom:1px solid black;"><?php echo $items['item_type']; ?></td>

                                                            <td style="text-align:left; border-bottom:1px solid black;"><?php echo $items['piece']; ?></td>

                                                            <td style="text-align:left; border-bottom:1px solid black;"><?php echo $items['grs_wt']; ?></td>

                                                            <!-- <td><?php echo $items['net_wt']; ?></td> -->

                                                            <!-- <td><?php echo number_format(floatval($items['dia_wt']), 3); ?></td> -->

                                                            <td style="text-align:right;border-bottom:1px solid black;"><?php echo $items['amount']; ?></td>

                                                        </tr>

                                                    <?php

                                                        $i++;
                                                    }

                                                    ?>

                                                    <!-- <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr> -->

                                                    <tr style="text-transform:uppercase;">

                                                        <th style="text-align:left; border-bottom:1px solid black;" colspan="3">SUB TOTAL</th>

                                                        <th style="text-align:left; border-bottom:1px solid black;"><?php echo number_format($tot_gross_wt, 3, '.', ''); ?></th>

                                                        <!-- <th><?php echo number_format($tot_net_wt, 3, '.', ''); ?></th> -->

                                                        <!-- <th><?php echo number_format($tot_dia_wt,3,'.','');?></th> -->


                                                        <th style="text-align:right;border-bottom:1px solid black;"><?php echo number_format($tot_amount, 2, '.', ''); ?></th>

                                                    </tr>

                                                    <!-- <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr> -->
                                                 <?php
                                                    $amt_in_words_old_metal   = $this->ret_billing_model->no_to_words($tot_amount);
                                                  ?>
                                                </table>

                                                <table height="100%" style="border: 1px solid black;border-collapse:collapse;border-top:none;">
                                                <tr>
                                                    <td style="padding: 10px;">Amount Chargeable(in words) <br>

                                                        <b><?= 'INR ' . $amt_in_words_old_metal; ?></b>
                                                    </td>

                                                    <td style="text-align: right;padding:10px;">
                                                        E & OE
                                                    </td>
                                                </tr>
                                             </table>




                                                <table style="border:1px solid black;border-collapse:collapse;width:100%;border-top:none">
                                                    <tr>
                                                        <td style="text-align: center;border-right:1px solid black;font-weight:bold;padding:10px;">HSN /SAC</td>

                                                        <td style="width:25%;text-align:right;font-weight:bold;padding:10px">Taxable Value</td>
                                                    </tr>

                                                    <?php

                                                    foreach ($btrans as $items) { ?>



                                                        <tr>
                                                            <td style="text-align: center;border:1px solid black;padding:10px"><?php echo $items['hsn_code'] ?></td>

                                                            <td style="width:25%;text-align:right;border:1px solid black;padding:10px"><?php echo moneyFormatIndia(number_format($tot_amount, '2', '.', '')) ?></td>
                                                        </tr>

                                                    <?php } ?>

                                                    <tr>
                                                        <td style="text-align: right;border:1px solid black;font-weight:bold;padding:10px">Total</td>

                                                        <td style="width:25%;text-align:right;border:1px solid black;padding:10px"><?php echo moneyFormatIndia(number_format($tot_amount, '2', '.', '')) ?></td>
                                                    </tr>



                                                </table>


                                                <table class="last_box" style="width: 100%;" height="100px">


                                                    <tr class="">

                                                        <td style="padding:10px;">Declaration</td>
                                                        <td style="width: 50%;padding:10px;"></td>

                                                    </tr>
                                                    <tr class="">
                                                        <td style="padding:10px;">We Declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
                                                        </td>

                                                        <td style="width: 50%;padding:10px">
                                                        </td>

                                                    </tr>

                                                    <br>
                                                    <br>

                                                    </tr>
                                                    <tr class="z1" style="margin-top: 20px;padding-top:10px;">Tax Amount Nil

                                                        <td style="width: 50%;padding:5px"> Tax Amount Nil</td>
                                                        <td style="width: 50%;padding:5px"></td>

                                                    </tr>

                                                    <tr class="z1" style="font-style: italic;width:50%;">
                                                        <td style="width: 50%; font-weight:bold;padding:5px;"> No E-way bill is required to be generated as per the Goods covered under this Invoice are exempted as per serial
                                                            NO.4/5 to the Annexure to Rule 138(14) of the CGST Rules</td>
                                                        <td style="width: 50%;"></td>
                                                    </tr>

                                                    <tr class="signatory">
                                                        <td style="width: 50%;"></td>

                                                        <td style="width: 50%;padding:5px">for Daeiou Jewels Private Limited <br>
                                                            <br>
                                                            Authorised Signatory
                                                        </td>
                                                    </tr>
                                                </table>

                                            <?php } ?>

                                            
                                            <?php

                                             if (sizeof($purchase_item_details['sales_return_details']) > 0) { ?>

                                                <!-- <br><b><span style="text-align:center;">SALES RETURN2</span></b> -->



                                                  <table id="pp" class="table text-center" style="border:1px solid black;border-collapse:collapse;width:100%;border-top:none">

                                                    <!-- <tr style="width:50%;">

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr> -->

                                                    <tr style="text-transform:uppercase;">

                                                        <th style="text-align:left; border-bottom:1px solid black;" width="7%;">S.NO</th>

                                                        <th style="text-align:left; border-bottom:1px solid black;" width="20%;">Category</th>

                                                        <th width="20%;" style="text-align:right;border-bottom:1px solid black;">Piece</th>

                                                        <th width="10%;" style="text-align:right;border-bottom:1px solid black;">G.Wt</th>

                                                        <th width="10%;" style="text-align:right;border-bottom:1px solid black;">Value(Rs)</th>

                                                    </tr>

                                                    <!-- <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr> -->

                                                    <?php
                                                    
                                                    $i = 1;

                                                    $sales_ret_net_wt = 0;
                                                    $sales_ret_gross_wt = 0;
                                                    $sales_ret_amount = 0;

                                                    foreach ($purchase_item_details['sales_return_details'] as $items) {

                                                        $sales_ret_net_wt += $items['net_wt'];

                                                        $sales_ret_gross_wt += $items['grs_wt'];

                                                        $sales_ret_amount += $items['amount'];

                                                        $piece += $items['piece'];

                                                    ?>

                                                        <tr style="text-align:center;">

                                                            <td style="text-align:left; border-bottom:1px solid black;"><?php echo $i; ?></td>

                                                            <td style="text-align:left; border-bottom:1px solid black;"><?php echo $items['name']; ?></td>

                                                            <td  style="text-align:right;border-bottom:1px solid black;"><?php echo $items['piece']; ?></td>

                                                            <td style="text-align:right;border-bottom:1px solid black;"><?php echo $items['grs_wt']; ?></td>

                                                            <td style="text-align:right;border-bottom:1px solid black;"><?php echo $items['amount']; ?></td>

                                                        </tr>

                                                    <?php

                                                        $i++;
                                                    }
                                                    $amt_in_words_sales_return   = $this->ret_billing_model->no_to_words($sales_ret_amount);

                                                    ?>

                                                    <!-- <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr> -->

                                                    <tr style="text-transform:uppercase;">

                                                        <th style="text-align:left; border-bottom:1px solid black;" colspan="2">SUB TOTAL</th>

                                                        <th style="text-align:right;border-bottom:1px solid black;"><?php echo number_format($piece, 0, '.', ''); ?></th>

                                                        <th style="text-align:right;border-bottom:1px solid black;"><?php echo number_format($sales_ret_gross_wt, 3, '.', ''); ?></th>

                                                        <th style="text-align:right;border-bottom:1px solid black;"><?php echo number_format($sales_ret_amount, 2, '.', ''); ?></th>

                                                    </tr>

                                                    <!-- <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr> -->

                                                </table>


                                                <table height="100%" style="border: 1px solid black;border-collapse:collapse;border-top:none;">
                                                <tr>
                                                    <td style="padding: 10px;">Amount Chargeable(in words) <br>

                                                        <b><?= 'INR ' . $amt_in_words_sales_return; ?></b>
                                                    </td>

                                                    <td style="text-align: right;padding:10px;">
                                                        E & OE
                                                    </td>
                                                </tr>
                                             </table>




                                              <table style="border:1px solid black;border-collapse:collapse;width:100%;border-top:none">
                                                <tr>
                                                    <td style="text-align: center;border-right:1px solid black;font-weight:bold;padding:10px;">HSN /SAC</td>

                                                    <td style="width:25%;text-align:right;font-weight:bold;padding:10px">Taxable Value</td>
                                                </tr>

                                                <?php

                                                foreach ($btrans as $items) { ?>



                                                    <tr>
                                                        <td style="text-align: center;border:1px solid black;padding:10px"><?php echo $items['hsn_code'] ?></td>

                                                        <td style="width:25%;text-align:right;border:1px solid black;padding:10px"><?php echo moneyFormatIndia(number_format($sales_ret_amount, '2', '.', '')) ?></td>
                                                    </tr>

                                                <?php } ?>

                                                <tr>
                                                    <td style="text-align: right;border:1px solid black;font-weight:bold;padding:10px">Total</td>

                                                    <td style="width:25%;text-align:right;border:1px solid black;padding:10px"><?php echo moneyFormatIndia(number_format($sales_ret_amount, '2', '.', '')) ?></td>
                                                </tr>



                                             </table>


                                               <table class="last_box" style="width: 100%;" height="100px">


                                                <tr class="">

                                                    <td style="padding:10px;">Declaration</td>
                                                    <td style="width: 50%;padding:10px;"></td>

                                                </tr>
                                                <tr class="">
                                                    <td style="padding:10px;">We Declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
                                                    </td>

                                                    <td style="width: 50%;padding:10px">
                                                    </td>

                                                </tr>

                                                <br>
                                                <br>

                                                </tr>
                                                <tr class="z1" style="margin-top: 20px;padding-top:10px;">Tax Amount Nil

                                                    <td style="width: 50%;padding:5px"> Tax Amount Nil</td>
                                                    <td style="width: 50%;padding:5px"></td>

                                                </tr>

                                                <tr class="z1" style="font-style: italic;width:50%;">
                                                    <td style="width: 50%; font-weight:bold;padding:5px;"> No E-way bill is required to be generated as per the Goods covered under this Invoice are exempted as per serial
                                                        NO.4/5 to the Annexure to Rule 138(14) of the CGST Rules</td>
                                                    <td style="width: 50%;"></td>
                                                </tr>

                                                <tr class="signatory">
                                                    <td style="width: 50%;"></td>

                                                    <td style="width: 50%;padding:5px">for Daeiou Jewels Private Limited <br>
                                                        <br>
                                                        Authorised Signatory
                                                    </td>
                                                </tr>
                                              </table>


                                            <?php } ?>



                                            <?php



                                            if (sizeof($purchase_item_details['partly_sales_details']) > 0) { ?>

                                                <br><b><span style="text-align:center;">PARTLY SALE</span></b>



                                                <table id="pp" class="table text-center">

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <tr style="text-transform:uppercase;">

                                                        <th width="2%;">S.NO</th>

                                                        <th width="5%;">Piece</th>

                                                        <th width="5%;">G.Wt</th>

                                                        <th width="5%;" style="text-align:right;">Value(Rs)</th>

                                                    </tr>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <?php

                                                    $i = 1;

                                                    $net_wt = 0;
                                                    $gross_wt = 0;
                                                    $amount = 0;

                                                    foreach ($purchase_item_details['partly_sales_details'] as $items) {

                                                        $net_wt += $items['net_wt'];

                                                        $gross_wt += $items['grs_wt'];

                                                        $amount += $items['amount'];
                                                        $piece += $items['piece'];



                                                    ?>

                                                        <tr style="text-align:center;">

                                                            <td><?php echo $i; ?></td>

                                                            <td><?php echo $items['piece']; ?></td>

                                                            <td><?php echo $items['grs_wt']; ?></td>

                                                            <td style="text-align:right;"><?php echo $items['amount']; ?></td>

                                                        </tr>

                                                    <?php

                                                        $i++;
                                                    }

                                                    ?>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                    <tr style="text-transform:uppercase;">

                                                        <th colspan="2">SUB TOTAL</th>

                                                        <th><?php echo number_format($piece, 3, '.', ''); ?></th>

                                                        <th><?php echo number_format($gross_wt, 3, '.', ''); ?></th>

                                                        <th style="text-align:right;"><?php echo number_format($amount, 2, '.', ''); ?></th>

                                                    </tr>

                                                    <tr>

                                                        <td>
                                                            <hr class="old_sumamry_dashed" style="width:700px !important;">
                                                        </td>

                                                    </tr>

                                                </table>

                                            <?php }

                                            ?>



                                    <?php }
                                    } ?>

                                </div>

                            </div>

                        </div>



                        <br><br><br><br>

                        <!-- <div class="row" style="text-transform:uppercase;">

                            <label>individual wt verified by</label>

                            <label style="margin-left:20%;">received by</label>

                            <label style="margin-left:30%;">approved By</label>

                        </div> -->


                        <div style="text-align:center">
                            This is a computer generated Document
                        </div>

                    </div>

                </div><!-- /.box-body -->

            </div><!-- box -->

        </div>

    </div>

</body>

</html>

<?php ?>