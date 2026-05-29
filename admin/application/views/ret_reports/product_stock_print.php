<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Summary Stock Details</title>

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/estimation.css">

    <style type="text/css">
        body,
        html {

            margin-bottom: 0;

        }

        .alignLeft {

            text-align: left;

        }

        .alignRight {

            text-align: right;

        }

        .item_name {

            font-weight: bold;

        }

        .finalTotal {

            width: 109.6%;

            font-size: 18px !important;

        }

        @page {

            size: 78mm 600mm;

            margin-bottom: 150px !important;

        }

        .estimation {

            font-size: 11px !important;

        }

        .estimation td {

            padding-top: 0px !important;

        }

        span {
            display: inline-block;
        }
    </style>

</head>

<?php

function moneyFormatIndia($num)
{

    return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);

} ?>

<body class="plugin">

    <span class="PDFReceipt">

        <div class="printable">

            <div class="" style="text-align: center;">

                <table>

                    <tr>
                        <th style="text-align:center;"> <?php echo $comp_details['company_name']; ?></th>
                    </tr>

                    <tr>
                        <th style="text-align:center;">STOCK REPORT - <?php echo $branch_details['name']; ?></th>
                    </tr>

                    <tr>
                        <th style="text-align:center;"> <?= $this->session->userdata['username']; ?>&nbsp;-&nbsp;
                            <?php echo date("d-m-Y h:i:s "); ?>
                        </th>
                    </tr>

                    <tr>
                        <th> </th>
                    </tr>

                </table>

            </div>

            <div class="item_details">

                <table class="estimation" width="100%">

                    <tr>
                        <th class="alignLeft" colspan=2></th>

                        <th class="alignCenter" colspan=2>Opening</th>

                        <th class="alignCenter" colspan=2>Live Stock</th>
                    </tr>

                    <tr>

                        <td colspan="6">
                            <hr class="item_dashed">
                        </td>

                    </tr>

                    <tr>

                        <th class="alignLeft"> S.no</th>

                        <th class="alignLeft"> Pro</th>

                        <th class="alignRight">Op.Pcs</th>

                        <th class="alignRight">Op.Gwt</th>

                        <th class="alignRight">Cl.Pcs</th>

                        <th class="alignRight">Cl.Gwt</th>

                    </tr>

                    <tr>

                        <td colspan="6">
                            <hr class="item_dashed">
                        </td>

                    </tr>

                    <tbody>

                        <?php

                        $op_piece = 0;
                        $op_gross_wts = 0;
                        $cl_piece = 0;
                        $cl_gross_wt = 0;

                        foreach ($list as $key => $data) { ?>

                            <tr style="font-weight:bold !important;">

                                <td class="alignLeft"><?php echo $key + 1; ?></td>

                                <td colspan="5" class="alignLeft">
                                    <?php echo $data['section_name'] . ' - ' . $data['product_name']; ?></td>

                            </tr>

                            <tr style="font-weight:500 !important;font-size:13px !important;">
                                <td colspan="2" class="alignLeft"></td>
                                <td class="alignRight"><?php echo $data['op_piece']; ?></td>

                                <td class="alignRight"><?php echo number_format($data['op_gross_wt'], 3); ?></td>

                                <td class="alignRight"><?php echo $data['cl_piece']; ?></td>

                                <td class="alignRight"><?php echo number_format($data['cl_gross_wt'], 3); ?></td>

                            </tr>

                            <?php

                            $op_piece += (float) $data['op_piece'];

                            $op_gross_wts += (float) $data['op_gross_wt'];

                            $cl_piece += (float) $data['cl_piece'];

                            $cl_gross_wt += (float) $data['cl_gross_wt'];

                        } ?>

                        <tr>

                            <td colspan="6">
                                <hr class="item_dashed">
                            </td>

                        </tr>

                    </tbody>

                    <tfoot>

                        <tr>

                            <th class="alignLeft" colspan=2> Total </th>

                            <th class="alignRight"><?php echo $op_piece; ?></th>

                            <th class="alignRight"><?php echo number_format($op_gross_wts, 3); ?></th>

                            <th class="alignRight"><?php echo $cl_piece; ?></th>

                            <th class="alignRight"><?php echo number_format($cl_gross_wt, 3); ?></th>

                        </tr>

                    </tfoot>

                </table>

            </div>

            <!-- NON-TAGGED STOCK SECTION -->
            <div class="item_details" style="margin-top:10px;">
                <table class="estimation" width="100%">
                    <tr>
                        <th colspan="6" class="alignLeft" style="font-size:12px;padding-top:4px;">NON-TAGGED STOCK</th>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <hr class="item_dashed">
                        </td>
                    </tr>
                    <tr>
                        <th class="alignLeft">S.no</th>
                        <th class="alignLeft">Pro</th>
                        <th class="alignRight">Op.Pcs</th>
                        <th class="alignRight">Op.Gwt</th>
                        <th class="alignRight">Cl.Pcs</th>
                        <th class="alignRight">Cl.Gwt</th>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <hr class="item_dashed">
                        </td>
                    </tr>
                    <tbody>
                        <?php
                        $nt_op_piece = 0;
                        $nt_op_gwt = 0;
                        $nt_cl_piece = 0;
                        $nt_cl_gwt = 0;
                        if (!empty($non_tag_items)) {
                            foreach ($non_tag_items as $nt_key => $nt_data) { ?>
                                <tr style="font-weight:bold !important;">
                                    <td class="alignLeft"><?php echo $nt_key + 1; ?></td>
                                    <td colspan="5" class="alignLeft">
                                        <?php echo $nt_data['section_name'] . ' - ' . $nt_data['product_name']; ?></td>
                                </tr>
                                <tr style="font-weight:500 !important;font-size:13px !important;">
                                    <td colspan="2" class="alignLeft"></td>
                                    <td class="alignRight"><?php echo $nt_data['op_piece']; ?></td>
                                    <td class="alignRight"><?php echo number_format($nt_data['op_gross_wt'], 3); ?></td>
                                    <td class="alignRight"><?php echo $nt_data['cl_piece']; ?></td>
                                    <td class="alignRight"><?php echo number_format($nt_data['cl_gross_wt'], 3); ?></td>
                                </tr>
                                <?php
                                $nt_op_piece += (float) $nt_data['op_piece'];
                                $nt_op_gwt += (float) $nt_data['op_gross_wt'];
                                $nt_cl_piece += (float) $nt_data['cl_piece'];
                                $nt_cl_gwt += (float) $nt_data['cl_gross_wt'];
                            }
                        } else { ?>
                            <tr>
                                <td colspan="6" class="alignLeft" style="font-style:italic;">No non-tagged stock found.</td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="6">
                                <hr class="item_dashed">
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="alignLeft" colspan=2> Total </th>
                            <th class="alignRight"><?php echo $nt_op_piece; ?></th>
                            <th class="alignRight"><?php echo number_format($nt_op_gwt, 3); ?></th>
                            <th class="alignRight"><?php echo $nt_cl_piece; ?></th>
                            <th class="alignRight"><?php echo number_format($nt_cl_gwt, 3); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </span>

</body>

</html>