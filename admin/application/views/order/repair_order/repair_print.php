<html>

<head>
    <meta charset="utf-8">
    <title>Order Booking Proforma invoice</title>
    <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/billing_receipt.css"> -->
    <style type="text/css">
    body,
    html {
        margin-bottom: 0;
        font-size: 12px !important;
    }

    .item_dashed {
        width: 100%;
    }

    span {
        display: inline-block;
    }

    .alignRight {

        text-align: right;
    }

    .alignLeft {

        text-align: left;
    }

    .addr_headers_labels {
        display: inline-block;
        width: 20%;
    }

    .addr_rate_labels {
        display: inline-block;
        width: 10%;
    }

    .addr_labels {
        display: inline-block;
        width: 20%;
    }

    .addr_values {
        display: inline-block;
        padding-left: -5px;
    }

    .addr_delv_labels {
        display: inline-block;
        width: 25%;
    }

    .addr_delv_values {
        display: inline-block;
        padding-left: 5px;
    }

    .addr_brch_labels {
        display: inline-block;
        width: 20%;
    }

    .addr_brch_values {
        display: inline-block;
        padding-left: 20px;
    }

    .stones,
    .charges {
        font-style: italic;
    }

    .stones .stoneData,
    .charges .chargeData {
        font-size: 12px !important;
    }
	.heading {

		text-align: center;

		font-size: 15px !important;

	}
    	@page {

			size: A4;

			margin-top: 5cm;
			margin-left: 25px;
			margin-right: 35px;

		}

    </style>
</head>

<body>
    <span class="PDFReceipt">
        <div class="heading">
            <div class="company_name">
                <h1><?php echo strtoupper($comp_details['company_name']); ?></h1>
            </div>
            <div><?php echo strtoupper($comp_details['address1']) ?> ,
                <?php echo strtoupper($comp_details['address2']) ?></div>
            <?php echo ($comp_details['email']!='' ? '<div>Email : '.$comp_details['email'].' </div>' :'') ?>
            <?php echo ($comp_details['gst_number']!='' ? '<div>GST : '.$comp_details['gst_number'].' </div>' :'') ?>
        </div><br>

        <?php
			function moneyFormatIndia($num)
			{
				return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
			}
			?>
        <!-- <div style="width: 100%; text-transform:uppercase;font-size: 11px !important;">
            <div style="display: inline-block; width: 50%; padding-left:0px;margin-top:140px;">
                <label><?php echo '<div class="addr_headers_labels">CIN</div><div class="addr_values">:&nbsp;&nbsp;' . $comp_details['cin_number'] . "</div>"; ?></label><br>
                <label><?php echo '<div class="addr_headers_labels">GSTIN</div><div class="addr_values">:&nbsp;&nbsp;' . $comp_details['gst_number'] . "</div>"; ?></label><br>
            </div>

             <div style="width: 50%; text-align: right; display: inline-block; vertical-align: top;">
				<div style="text-align: right;width: 100%; display: inline-block;margin-top:-30px;display:none">
					<img width="50%" src="<?php echo dirname(base_url()) ?>/assets/img/receipt_logo.png" />
				</div>
			</div> 
        </div>
        <p></p> -->

        <div style="width: 100%; text-transform:uppercase;font-size: 11px !important;margin-top:25px;">

            <div style="display: inline-block; width: 55%; padding-left:0px;">
                <?php if($repair['order_type'] != 4) { ?>
                <label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;' . 'Mr./Ms.' . $repair['firstname'] . "</div>"; ?></label><br>
                <label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;' . $repair['mobile'] . "</div>"; ?></label><br>
                <label><?php echo ($repair['cus_address1'] != '' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($repair['cus_address1']) . ',' . "</div><br>" : ''); ?></label>
                <label><?php echo ($repair['cus_address2'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($repair['cus_address2']) . ',' . "</div><br>" : ''); ?></label>
                <label><?php echo ($repair['cus_address3'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($repair['cus_address3']) . ',' . "</div><br>" : ''); ?></label>
                <label><?php echo ($repair['village_name'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($repair['village_name']) . ',' . "</div><br>" : ''); ?></label>
                <label><?php echo ($repair['cus_ciy'] != '' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($repair['cus_ciy']) . ($repair['pincode'] != '' ? ' - ' . $repair['pincode'] . '.' : '') . "</div><br>" : ''); ?></label>
                <label><?php echo ($repair['cus_state'] != '' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($repair['cus_state'] . ($repair['state_code'] != '' ? '-' . $repair['state_code'] : '')) . '.' . "</div><br>" : ''); ?></label>
                <!-- <label>
					<?php echo (isset($repair['pan_no']) && $repair['pan_no'] != '' ? '<div class="addr_labels">PAN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($repair['pan_no']) . "</div><br>" : ''); ?></label> -->
                <label><?php echo (isset($repair['gst_number']) && $repair['gst_number'] != '' ? '<div class="addr_labels">GSTIN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($repair['gst_number']) . "</div><br>" : ''); ?></label>
                <?php }else{?>

					<label><?php echo ($repair['order_date'] != '' ? '<div class="addr_brch_labels">Repair Date</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($repair['order_date']) . '' . "</div><br>" : ''); ?></label>
                    <label><?php echo ($repair['order_no'] != '' ? '<div class="addr_brch_labels">Repair No</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($repair['order_no']) . '' . "</div><br>" : ''); ?></label>
                   

				<?php }?>
            </div>


        <?php if($repair['order_type'] != 4) { ?>


        <div style="display: inline-block; width: 10%; padding-left:0px;"></div>
        <div
            style="width: 50%; text-align: right !importan; display: inline-block; vertical-align: top;margin-top:-14px !important;">
            <div style="text-align: right !important;width: 210%; display: inline-block;">
                <label><?php echo ($repair['order_date'] != '' ? '<div class="addr_brch_labels">Repair Date</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($repair['order_date']) . '' . "</div><br>" : ''); ?></label>
                <label><?php echo ($repair['order_no'] != '' ? '<div class="addr_brch_labels">Repair No</div><div class="addr_brch_values">:&nbsp;&nbsp;' . strtoupper($repair['order_no']) . '' . "</div><br>" : ''); ?></label>
            </div>
        </div>
        </div>

        <?php }?>

        <div
            style="width: 100%; text-transform:uppercase;font-size: 11px !important;text-align:center;margin-top:-45px !important;">
            <label><b>Repair Order Proforma invoice</b></label>
        </div>
        <p></p>


        <!-- <div style="text-align: left;width: 100%; text-transform:uppercase;margin-top:-8px;font-size: 11px !important">
			<div style="text-align: left; width:100%;height: 18px; ">
				<label><?php echo '<div class="addr_rate_labels">GOLD</div><div class="addr_values">:&nbsp;&nbsp;' . number_format($metal_rate['goldrate_22ct'], 2, '.', '') . '/' . 'Gm' . "</div>"; ?></label><br>
				<label><?php echo '<div class="addr_rate_labels">SILVER</div><div class="addr_values">:&nbsp;&nbsp;' . number_format($metal_rate['silverrate_1gm'], 2, '.', '') . '/' . 'Gm' . "</div>"; ?></label><br>
			</div>
		</div> -->

        <!-- <div style="width: 100%; text-align: right; text-transform:uppercase;display: inline-block;margin-top:-40px;font-size: 11px !important">
			<div style="text-align: right; width:100%;height: 18px;">
				<div style="width: 80%; display: inline-block"> Order Date &nbsp; : &nbsp; </div>
				<div style="width: 20%; display: inline-block; margin-top: -2px;text-align: left;"><?php echo $cus_details['order_date']; ?></div>
			</div> -->


        <!-- <div style="text-align: right; width:100%;height: 18px;">
				<div style="width: 80%; display: inline-block"> Order no &nbsp; : &nbsp; </div>
				<div style="width: 15%; display: inline-block; margin-top: -2px;text-align: left;"><?php echo $cus_details['orderno']; ?></div>
			</div>

		</div> -->


        <div class="content-wrapper">
            <div class="box">
                <div class="box-body">
                    <div class="container-fluid">
                        <div id="printable">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="table-responsive">
                                        <hr class="item_dashed">
										<?php if($repair['order_type'] != 4) { ?>
                                        <table id="pp" class="table text-center item_dashed" style="width:100%">
                                            <thead style="text-transform:uppercase;font-size:10px;">

                                                <tr>
                                                    <th class="table_heading alignLeft" style="width: 5%">S.NO</th>
                                                    <th class="table_heading aligncenter" style="width: 26%">Description
                                                    </th>
                                                    <th class="table_heading alignRight" style="width: 9%">Size</th>
                                                    <th class="table_heading alignRight" style="width: 15%">Pcs</th>
                                                    <th class="table_heading alignRight" style="width: 15%">Weight</th>
													<th class="table_heading alignRight" style="width: 15%">Due Date </th>
                                                    <th class="table_heading alignRight" style="width: 15%">Repair Type </th>
                                                    <!-- <th class="table_heading alignRight" style="width: 9%">Rate</th>
													<th class="table_heading alignRight" style="width: 9%">Amount</th> -->
                                                </tr>
                                            </thead>
                                            <tr>
                                                <td colspan="7">
                                                    <hr class="item_dashed">
                                                </td>
                                            </tr>
                                            <!--</thead>
										  <tbody>-->
                                            <?php
											$i = 1;
											$total_wt = 0;
											$totalitems = 0;
											$total_cgst = 0;
											$total_sgst = 0;
											$total_igst = 0;

											$total_nwt = 0;
											$total_lwt = 0;
											$total_amt = 0;

											foreach ($repair_details as $val) {
												// print_r($val);exit;
												$totalitems += $val['totalitems'];
												$total_wt += $val['gross_wt'];
												$total_amt += $val['rate'];
												$total_lwt += $val['less_wt'];
												$total_nwt += $val['net_wt'];

												$total_cgst     += $val['total_cgst'];
												$total_sgst     += $val['total_sgst'];
												$total_igst     += $val['total_igst'];

											?>
                                            <tr>
                                                <td class="alignLeft"><?php echo $i; ?></td>
                                                <td class="alignLeft">
                                                    <?php echo $val['product_name'] . '&nbsp;' . '(' . $val['design_name'] . ')'; ?>
                                                </td>
                                                <td class="alignRight">
                                                    <?php echo $val['size_name'] != '' ? $val['size_name'] : '-'; ?>
                                                </td>
                                                <td class="alignRight"><?php echo $val['totalitems']; ?></td>
                                                <td class="alignRight"><?php echo $val['gross_wt']; ?></td>
                                                <td class="alignRight"><?php echo $val['cus_due_date']; ?></td>
												<td class="alignRight"><?php echo $val['repair_type']; ?></td>
                                                <!-- <td class="alignRight"><?php echo $val['net_wt']; ?></td>
                                                <td class="alignRight"><?php echo $val['tag_code']; ?></td>
                                                <td class="alignRight"><?php echo $val['repair_type']; ?></td> -->
                                                <!-- <td class="alignRight"><?php echo $val['rate_per_gram']; ?></td>
													<td class="alignRight"><?php echo $val['rate']; ?></td> -->
                                            </tr>


                                            <?php
												if (count($val['stone_details']) > 0) {
													foreach ($val['stone_details'] as $stoneItems) {	?>
                                            <tr class='stones'>
                                                
                                                <td colspan="2" class='alignLeft stoneData'>
                                                    <?php echo $stoneItems['stone_name']; ?></td>
                                                <td></td>
                                                <td class="alignRight stoneData">
                                                    <?php echo moneyFormatIndia(number_format((float)($stoneItems['wt']), 3, '.', '')) . '/' . $stoneItems['uom_short_code']; ?>
                                                </td>
                                                <td colspan="2" class="alignRight stoneData">
                                                    <?php echo $stoneItems['rate_per_gram'] . ' /  ' . $stoneItems['uom_short_code']; ?>
                                                </td>
                                                <td colspan="1" class="alignRight stoneData">
                                                    <?php echo moneyFormatIndia('Rs : ' . $stoneItems['amount']); ?>
                                                </td>
                                                <td></td>
                                                <!-- <td></td>
													<td></td> -->
                                            </tr>
                                            <?php }
												}
												?>

                                            <?php
												if ($val['description'] != '') { ?>
                                            <tr>
                                                <td colspan="5">Remarks :
                                                    <?php echo $val['description'] . ($val['cus_duedate'] != '' ? ' - Due Date' . $val['cus_duedate'] : ''); ?>
                                                </td>
                                            </tr>
                                            <?php }
												?>
                                            <?php $i++;
											} ?>
                                            <tr>
                                                <td colspan="7">
                                                    <hr class="item_dashed">
                                                </td>
                                            </tr>
                                            <!--</tbody> -->


                                            <tr>
                                                <td colspan="2"><b>TOTAL</b></td>
                                                <td></td>
                                                <td class="alignRight">
                                                    <b><?php echo number_format((float)$totalitems, 0, '.', '') ?></b>
                                                </td>
                                                <td class="alignRight">
                                                    <b><?php echo number_format((float)$total_wt, 3, '.', '') ?></b>
                                                </td>

                                                 <td class="alignRight"><b></td> 
                                                 <td class="alignRight"><b></td>

                                            </tr>
                                            <tr>
                                                <td colspan="7">
                                                    <hr class="item_dashed">
                                                </td>
                                            </tr>



                                        </table><br>
										<?php }else{?>
										<table id="pp" class="table text-center item_dashed" style="width:100%">
                                            <thead style="text-transform:uppercase;font-size:10px;">

                                                <tr>
                                                    <th class="table_heading alignLeft" style="width: 5%">S.NO</th>
                                                    <th class="table_heading aligncenter" style="width: 26%">Description
                                                    </th>
                                                    <th class="table_heading alignRight" style="width: 9%">Size</th>
                                                    <th class="table_heading alignRight" style="width: 9%">Pcs</th>
                                                    <th class="table_heading alignRight" style="width: 9%">Gwt</th>
                                                    <th class="table_heading alignRight" style="width: 9%">Lwt</th>
                                                    <th class="table_heading alignRight" style="width: 9%">Nwt</th>
                                                    <th class="table_heading alignRight" style="width: 9%">Tag Code</th>
                                                    <th class="table_heading alignRight" style="width: 15%">Repair Type
                                                    </th>
                                                    <!-- <th class="table_heading alignRight" style="width: 9%">Rate</th>
													<th class="table_heading alignRight" style="width: 9%">Amount</th> -->
                                                </tr>
                                            </thead>
                                            <tr>
                                                <td colspan="9">
                                                    <hr class="item_dashed">
                                                </td>
                                            </tr>
                                            <!--</thead>
										  <tbody>-->
                                            <?php
											$i = 1;
											$total_wt = 0;
											$totalitems = 0;
											$total_cgst = 0;
											$total_sgst = 0;
											$total_igst = 0;

											$total_nwt = 0;
											$total_lwt = 0;
											$total_amt = 0;

											foreach ($repair_details as $val) {
												// print_r($val);exit;
												$totalitems += $val['totalitems'];
												$total_wt += $val['gross_wt'];
												$total_amt += $val['rate'];
												$total_lwt += $val['less_wt'];
												$total_nwt += $val['net_wt'];

												$total_cgst     += $val['total_cgst'];
												$total_sgst     += $val['total_sgst'];
												$total_igst     += $val['total_igst'];

											?>
                                            <tr>
                                                <td class="alignLeft"><?php echo $i; ?></td>
                                                <td class="alignLeft">
                                                    <?php echo $val['product_name'] . '&nbsp;' . '(' . $val['design_name'] . ')'; ?>
                                                </td>
                                                <td class="alignRight">
                                                    <?php echo $val['size_name'] != '' ? $val['size_name'] : '-'; ?>
                                                </td>
                                                <td class="alignRight"><?php echo $val['totalitems']; ?></td>
                                                <td class="alignRight"><?php echo $val['gross_wt']; ?></td>
                                                <td class="alignRight"><?php echo $val['less_wt']; ?></td>
                                                <td class="alignRight"><?php echo $val['net_wt']; ?></td>
                                                <td class="alignRight"><?php echo $val['tag_code']; ?></td>
                                                <td class="alignRight"><?php echo $val['repair_type']; ?></td>
                                                <!-- <td class="alignRight"><?php echo $val['rate_per_gram']; ?></td>
													<td class="alignRight"><?php echo $val['rate']; ?></td> -->
                                            </tr>


                                            <?php
												if (count($val['stone_details']) > 0) {
													foreach ($val['stone_details'] as $stoneItems) {	?>
                                            <tr class='stones'>
                                                <td></td>
                                                <td colspan="2" class='alignLeft stoneData'>
                                                    <?php echo $stoneItems['stone_name']; ?></td>
                                                <td></td>
                                                <td class="alignRight stoneData">
                                                    <?php echo moneyFormatIndia(number_format((float)($stoneItems['wt']), 3, '.', '')) . '/' . $stoneItems['uom_short_code']; ?>
                                                </td>
                                                <td colspan="2" class="alignRight stoneData">
                                                    <?php echo $stoneItems['rate_per_gram'] . ' /  ' . $stoneItems['uom_short_code']; ?>
                                                </td>
                                                <td colspan="2" class="alignRight stoneData">
                                                    <?php echo moneyFormatIndia('Rs : ' . $stoneItems['amount']); ?>
                                                </td>
                                                <td></td>
                                                <!-- <td></td>
													<td></td> -->
                                            </tr>
                                            <?php }
												}
												?>

                                            <?php
												if ($val['description'] != '') { ?>
                                            <tr>
                                                <td colspan="5">Remarks :
                                                    <?php echo $val['description'] . ($val['cus_duedate'] != '' ? ' - Due Date' . $val['cus_duedate'] : ''); ?>
                                                </td>
                                            </tr>
                                            <?php }
												?>
                                            <?php $i++;
											} ?>
                                            <tr>
                                                <td colspan="9">
                                                    <hr class="item_dashed">
                                                </td>
                                            </tr>
                                            <!--</tbody> -->


                                            <tr>
                                                <td colspan="2"><b>TOTAL</b></td>
                                                <td></td>
                                                <td class="alignRight">
                                                    <b><?php echo number_format((float)$totalitems, 0, '.', '') ?></b>
                                                </td>
                                                <td class="alignRight">
                                                    <b><?php echo number_format((float)$total_wt, 3, '.', '') ?></b>
                                                </td>
                                                <td class="alignRight">
                                                    <b><?php echo number_format((float)$total_lwt, 3, '.', '') ?></b>
                                                </td>
                                                <td class="alignRight">
                                                    <b><?php echo number_format((float)$total_nwt, 3, '.', '') ?></b>
                                                </td>
                                                <td class="alignRight"><b></td>
                                                <!-- <td class="alignRight"><b></td>
												<td class="alignRight"><b></td> -->
                                                <!-- <td class="alignRight"><b><?php echo number_format((float)$total_amt, 3, '.', '') ?></b></td> -->
                                                <td class="alignRight"><b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="9">
                                                    <hr class="item_dashed">
                                                </td>
                                            </tr>



                                        </table><br>
										<?php }?>
                                    </div>


                                </div>
                            </div><br><br><br><br><br><br><br>

                            <div class="row" style="text-transform:uppercase;">
                                <label>Customer Signature</label>
                                <label style="margin-left:20%;">Operator Signature</label>
                                <label style="margin-left:20%;">Cashier Signature</label>

                            </div><br>

                            <div class="row" style="text-transform:uppercase;">
                                <label></label>&nbsp;&nbsp;
                                <label
                                    style="margin-left:35%;"><?php echo ($repair['emp_name'] != '' ? $repair['emp_name'] :"") . '&nbsp;' . ($dCData['is_day_closed'] == 0 ? ' - ' . date("d-m-y h:i:sa") : ''); ?></label>
                                <label style="margin-left:20%;"></label>

                            </div>




                        </div>
                    </div>
                </div><!-- /.box-body -->
            </div>

    </span>

</body>

</html>