<?php
function moneyFormatIndia($num)
{
	$nums = explode(".",$num);
	if(count($nums)>2){
	return "0";
	}else{
	if(count($nums)==1){
	$nums[1]="00";
	}
	$num = $nums[0];
	$explrestunits = "" ;
	if(strlen($num)>3){
	$lastthree = substr($num, strlen($num)-3, strlen($num));
	$restunits = substr($num, 0, strlen($num)-3); 
	$restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
	$expunit = str_split($restunits, 2);
	for($i=0; $i<sizeof($expunit); $i++)
	{
	if($i==0)
	{
	$explrestunits .= (int)$expunit[$i].","; 
	}
	else
	{
	$explrestunits .= $expunit[$i].",";
	}
	}
	$thecash = $explrestunits.$lastthree;
	} else {
	$thecash = $num;
	}
	return $thecash.".".$nums[1]; 
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style type="text/css">
body{
	font-size: 12px;
}
table{
	width: 100%;
}
.rateborders{
	border-top: none;
	border-bottom: none;
	height: 20px;
	text-align: right;
	font-size: 14px;
}
.desc{
	text-align: right;
	font-size: 14px;
	height: 25px;
	border-bottom: none;
	border-top: none;
}
.descheading{
	text-align: center;
	font-weight: bold;
	height: 30px;
	font-size: 12px;
	border-top: none;
}
.nos_label{
	padding-right: 5px;
	font-weight: bold;
}
.nos_values{
	padding-left : 5px;
}
.iconDetails {
	 margin-left:2%;
	float:left; 
	width:40px;	
	height:40px;	
} 
.container2 {
	width:350px;
	height:auto;
	padding:1%;
    float:left;
}
.branchadd{
	padding-left: 8px !important;
    padding: 0px;
    margin-top: -91px;
    margin-left: 216px;
    border-left: 1px solid;
    margin-bottom: 24px;
}

        .addr_brch_labels {
			display: inline-block;
			width: 40%;
		}

		.addr_brch_values {
			display: inline-block;
			padding-left: 2px;
		}
		
		 .addr_labels {
            display: inline-block;
            width: 30%;	
        }
		
        .addr_values {
            display: inline-block;
            padding-left: -5px;
			white-space: nowrap;
			text-overflow: clip;
			
        }

		.footer .footer_left, .footer .footer_center, .footer .footer_right {
			display: inline-block;

		}

		.heading {

			text-align: center;
			font-size: 15px !important;

			}
		.footer {
			padding-top: 70px;
			font-weight: bold;
		}
		.footer .footer_left {
			text-align: left;
			vertical-align: top;
			width: 33%;
		}

		.footer .footer_center {
			text-align: center;
			vertical-align: top;
			width: 33%;
		}

		.footer .footer_right {
			text-align: right;
			vertical-align: top;
			width: 33%;
		}

		.textOver {
			display: -webkit-box;
			max-width: 200px;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			font-size:12px;
			word-wrap: break-word;
		}
		
h4 {margin:0}
.left {float:left;width:45px;}
.right {float:left;margin:0 0 0 5px;width:400px;}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Receipt Payment</title>
</head>
<body>


<div class="heading">
				<div class="company_name"><h1><?php echo strtoupper($comp_details['company_name']); ?></h1></div>
				<div><?php echo strtoupper($comp_details['address1']) ?> , <?php echo strtoupper($comp_details['address2']) ?></div>
				<?php echo ($comp_details['email']!='' ? '<div>Email : '.$comp_details['email'].' </div>' :'') ?>
				<?php echo ($comp_details['gst_number']!='' ? '<div>GST : '.$comp_details['gst_number'].' </div>' :'') ?>
			</div><br>

	<p style="text-align: center">
		<div style="text-align: center">
			<span style="padding-left: 12px; font-weight: bold; font-size:20px">Issue Of Payment</span>
		</div>
	</p><br>
	<?php

	if (version_compare(phpversion(), '7.1', '>=')) {
		ini_set( 'precision', 14 );
		ini_set( 'serialize_precision', -1 );
	}
	function formatnumber($num){

		return floatval(number_format($num, 2, '.', ''));

	}?>
	<table width="921" height="756" border="1" cellpadding="3" cellspacing="0">
		<tr>
			<td height="30" colspan="2">
				<div style="text-align: right !important; display: inline-block; vertical-align: top;">
				<label><b>Transcation Type :</b>&nbsp;<?php echo $paymentdetails['paydetails']['bill_type'];?></label><br>
				</div>
			</td>
			<td width="36%" >
				<div style="text-align: right !important; display: inline-block; vertical-align: top;">
					<div style="text-align: left !important;width: 100%; display: inline-block;"> 
						<label><b>Issue No &nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;&nbsp;<?php echo $paymentdetails['paydetails']['pay_refno'];?></label><br>
						<label><b>Issue Date :</b>&nbsp;&nbsp;&nbsp;<?php echo $paymentdetails['paydetails']['pay_date'];?></label><br>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<table width="921" height="700" border="1" cellpadding="3" cellspacing="0">
		<tr>
			<td width="50%" height="100" valign="top" style="border-right: none;">
				<div class="textOver" style="text-align: right !important; display: inline-block; vertical-align: top;">
					<div style="text-align: left !important;width: 100%; display: inline-block;">
						<label><b>Paid by</b></label><br><br>
						<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.$comp_details['company_name']."</div>"; ?></label><br>
    					<label><?php echo ($comp_details['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($comp_details['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($comp_details['address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($comp_details['city']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['city']).($comp_details['pincode']!='' ? ' - '.$comp_details['pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($comp_details['state']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['state']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($comp_details['gst_number']!='' ? '<div class="addr_labels">GST</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($comp_details['gst_number']).','."</div><br>" :''); ?></label>
					</div>
				</div>
			</td>
			<td width="50%" height="75" valign="top" style="border-right: none;">
				<div class="textOver" style="text-align: right !important; display: inline-block; vertical-align: top;">
					<div style="text-align: left !important;width: 100%; display: inline-block;">
						<label><b>Paid to</b></label><br><br>
						<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.$paymentdetails['paydetails']['karigar']."</div>"; ?></label><br>
						<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$paymentdetails['paydetails']['mobile']."</div>"; ?></label><br>
    					<label><?php echo ($paymentdetails['paydetails']['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($paymentdetails['paydetails']['address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($paymentdetails['paydetails']['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($paymentdetails['paydetails']['address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($paymentdetails['paydetails']['city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($paymentdetails['paydetails']['city_name']).($paymentdetails['paydetails']['pincode']!='' ? ' - '.$paymentdetails['paydetails']['pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($paymentdetails['paydetails']['state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($paymentdetails['paydetails']['state_name']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($paymentdetails['paydetails']['gst_number']!='' ? '<div class="addr_labels">GST</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($paymentdetails['paydetails']['gst_number']).','."</div><br>" :''); ?></label>
					</div>
				</div>
			</td>
		</tr>
	</table>	
	<table width="921" height="700" border="1" cellpadding="3" cellspacing="0">
		<tr>
			<td height="25" colspan="2">
				<div style="text-align: right !important; display: inline-block; vertical-align: top;">
					<label><b>AMOUNT</b></label><br>
				</div>
			</td>
			<td width="25%" style="text-align: right !important;">
			<div style="text-align: right !important; display: inline-block; vertical-align: top; font-weight: ; font-size:13px">
				<label>
					<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<b><?php echo moneyFormatIndia($paymentdetails['paydetails']['tot_cash_pay']);?></b></label><br></div>
			</td>
		</tr>
	</table>
	<table width="921" height="700" border="1" cellpadding="3" cellspacing="0">
	<tr>
			<td width="25%" height="100" valign="top" style="border-right: none;" colspan="2">
				<div style="text-align: right !important; display: inline-block; vertical-align: top;">
					<div style="text-align: left !important;width: 100%; display: inline-block;">
						<!-- <label><php echo $paymentdetails['paydetails']['remarks'];?></label><br> -->
						<?php
														$total_amt = 0;
														$cash_amt = 0;
														$net_banking_amt = 0;
														$imps_amt = 0;
														$rtgs_amt = 0;
														$neft_amt = 0;

														foreach ($payment as $items) {
															$total_amt += $items['payment_amount'];
															if ($items['transfer_type'] == 4 && $items['payment_mode'] == 'CSH') {
																$cash_amt += $items['payment_amount'];
															}
															if ($items['transfer_type'] == 1 && $items['payment_mode'] == 'RTGS') {
																$net_banking_amt += $items['payment_amount'];
																$rtgs_amt += $items['payment_amount'];
															}
															if ($items['transfer_type'] == 1 && $items['payment_mode'] == 'IMPS') {
																$net_banking_amt += $items['payment_amount'];
																$imps_amt += $items['payment_amount'];
															}
															if ($items['transfer_type'] == 1 && $items['payment_mode'] == 'NEFT') {
																$net_banking_amt += $items['payment_amount'];
																$neft_amt += $items['payment_amount'];
															}
															if ($items['payment_mode'] == 'CHQ') {
																$chq_amt += $items['payment_amount'];
																// print_r($chq_amt);exit;
															}
															
														}
														 ?>
					</div>
				</div>
			</td>

			<td width="25%" height="10px">
            <div style="text-align: right !important; display: inline-block; vertical-align: top; font-weight: bold; font-size:13px">
                <table>
				<?php if ($cash_amt > 0) { ?>
                    <tr>
                        <td>CASH</td>
                    </tr>
                    <tr>
                        <td>
                            <hr class="item_dashed" style="width:210% !important;">
                        </td>
                    </tr>
					<?php } ?>

					<?php if ($chq_amt > 0) { ?>
						<?php foreach ($payment as $chq) {
							if ($chq['payment_mode'] == 'CHQ') { ?>
                           <tr>
						   <td>CHEQUE (<?php echo ($chq['cheque_no'] != '' ? 'Chq\ Ref.No - ' . $chq['cheque_no'] : '') . ($chq['ref_date'] != '' ? ' .Dtd - ' . $chq['ref_date'] : '') . ($chq['bank_name'] != '' ? '- ' . $chq['bank_name'] : '') ?>)</td>
							</tr>
							<tr>
								<td>
									<hr class="item_dashed" style="width:210% !important;">
								</td>
							</tr>														
								<?php }
										}
						} ?>

					<?php if ($rtgs_amt > 0) {
									foreach ($payment as $rtgs) {
										if ($rtgs['payment_mode'] == 'RTGS') {
											$rtgs_ref_no = $rtgs['ref_no']; ?>
											<tr>
											<td>RTGS (<?php echo ($rtgs_ref_no != '' ? 'Ref.No - ' . $rtgs_ref_no : '') . ($rtgs['ref_date'] != '' ? ' .Dtd - ' . $rtgs['ref_date'] : ''); ?>)</td>
									    	</tr>
											<tr>
												<td>
													<hr class="item_dashed" style="width:210% !important;">
												</td>
											</tr>
										<?php }
										?>
								<?php }
								}  ?>	

                               <?php if ($imps_amt > 0) {
									foreach ($payment as $imps) {
										if ($imps['payment_mode'] == 'IMPS') {
											$imps_ref_no = $rtgs['ref_no']; ?>
											<tr>
											<td>IMPS (<?php echo ($imps_ref_no != '' ? 'Ref.No - ' . $imps_ref_no : '') . ($imps['ref_date'] != '' ? ' .Dtd - ' . $imps['ref_date'] : ''); ?>)</td>
									    	</tr>
											<tr>
												<td>
													<hr class="item_dashed" style="width:210% !important;">
												</td>
											</tr>
										<?php }
										?>
								<?php }
								}  ?>	



									<?php if ($neft_amt > 0) {
									foreach ($payment as $neft) {
										if ($neft['payment_mode'] == 'NEFT') {
											$neft_ref_no = $rtgs['ref_no']; ?>
											<tr>
											<td>NEFT (<?php echo ($neft_ref_no != '' ? 'Ref.No - ' . $neft_ref_no : '') . ($neft['ref_date'] != '' ? ' .Dtd - ' . $neft['ref_date'] : ''); ?>)</td>
									    	</tr>
											<tr>
												<td>
													<hr class="item_dashed" style="width:210% !important;">
												</td>
											</tr>
										<?php }
										?>
								<?php }
								}  ?>	




					<!-- <php if ($neft_amt > 0) {?>

                    <tr>
                        <td>NEFT</td>
                    </tr>
                    <tr>
                        <td>
                            <hr class="item_dashed" style="width:210% !important;">
                        </td>
                    </tr>
					<php } ?> -->

                    <tr>
                        <td>TOTAL</td>
                    </tr>
					<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>

											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>

                    
                </table>
            </div>    
       	 </td>
			<!--<td width="25%" height="10px">
			<div style="text-align: right !important; display: inline-block; vertical-align: top; font-weight: bold; font-size:13px">
				<table>
					<tr>
						<td>Total</td>
					</tr>
					<tr>
						<td>
							<hr class="item_dashed" style="width:210% !important;">
						</td>
					</tr>
					<tr>
						<td>Sub Total</td>
					</tr>
					<tr>
						<td>
							<hr class="item_dashed" style="width:210% !important;">
						</td>
					</tr>
					<tr>
						<td>CGST</td>
					</tr>
					<tr>
						<td>
							<hr class="item_dashed" style="width:210% !important;">
						</td>
					</tr>
					<tr>
						<td>SGST</td>
					</tr>
					<tr>
						<td>
							<hr class="item_dashed" style="width:210% !important;">
						</td>
					</tr>
					<tr>
						<td>IGST</td>
					</tr>
					<tr>
						<td>
							<hr class="item_dashed" style="width:210% !important;">
						</td>
					</tr>
					<tr>
						<td>Tax</td>
					</tr>
					<tr>
						<td>
							<hr class="item_dashed" style="width:210% !important;">
						</td>
					</tr>
					<tr>
						<td>Total</td>
					</tr>
				</table>
				</div>	
			</td>-->
			<td width="25%" >
			<div style="text-align: right !important; display: inline-block; vertical-align: top; font-weight: ; font-size:13px">
			<table>

							<?php if ($cash_amt > 0) { ?>
								<tr>
									<td style="text-align: right;"><?php echo moneyFormatIndia(number_format($cash_amt, 2, '.', '')); ?></td>
								</tr>
								<tr><td></td></tr>
								<tr><td></td></tr>
								<tr><td></td></tr>
								<tr><td></td></tr>
								<tr><td></td></tr>
								<tr><td></td></tr>
								<tr><td></td></tr>

								<!-- <tr><td></td></tr>
								<tr><td></td></tr>
								<tr><td></td></tr>
								<tr><td></td></tr> -->

								<?php } ?>



								<?php if ($chq_amt > 0) {
									foreach ($payment as $chq) {
										if ($chq['payment_mode'] == 'CHQ') {
											$chq_amount = $chq['payment_amount']; ?>
											<tr>
												<td class="alignRight" ><?php echo moneyFormatIndia(number_format(	$chq_amount, 2, '.', '')); ?></td>
											</tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>


										<?php }
										?>
								<?php }
								}  ?>		



								<?php if ($rtgs_amt > 0) {
									foreach ($payment as $rtgs) {
										if ($rtgs['payment_mode'] == 'RTGS') {
											$rtgs_amount = $rtgs['payment_amount']; ?>
											<tr>
												<td class="alignRight" ><?php echo moneyFormatIndia(number_format($rtgs_amount, 2, '.', '')); ?></td>
											</tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>

										<?php }
										?>
								<?php }
								}  ?>													

						
								<?php if ($imps_amt > 0) {
								foreach ($payment as $imps) {
									if ($imps['transfer_type'] == 1 && $imps['payment_mode'] == 'IMPS') {
										$imps_amount = $imps['payment_amount']; ?>
										  <tr>
											<td class="alignRight" ><?php echo moneyFormatIndia(number_format($imps_amount, 2, '.', '')); ?></td>
										</tr>
										<tr><td></td></tr>
										<tr><td></td></tr>
										<tr><td></td></tr>
										<tr><td></td></tr>
									<?php	}
									?>
									<?php }
									} ?>	
						
								<?php if ($neft_amt > 0) {
									foreach ($payment as $neft) {
										if ($neft['transfer_type'] == 1 && $neft['payment_mode'] == 'NEFT') {
											$neft_amount = $neft['payment_amount']; ?>
											<tr>
												<td class="alignRight" ><?php echo moneyFormatIndia(number_format($neft_amount, 2, '.', '')); ?></td>
											</tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
										<?php	}
										?>
								<?php }
								} ?>	
						          <?php  $total_amt = $cash_amt + $rtgs_amount+ $imps_amount +$neft_amount +$chq_amt;?>
								<tr>
									<td style="text-align: right;"><b><?php echo moneyFormatIndia(number_format($total_amt, 2, '.', '')); ?></b></td>
								</tr>
								            <tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>

											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<tr><td></td></tr>
											<!-- <tr><td></td></tr>
											<tr><td></td></tr> -->

							</table>

						</div>
					</td>

				</tr>
			</table>
			
		</div>
			
		</td>




		</tr>
	</table>

	<div class="footer">
							<div class="footer_left" style="width:25%;">
									<label>Audited By</label>
							</div>
							<div class="footer_left" style="width:20%;">
									<label>Party Sign</label>
							</div>
							<div class="footer_left" style="width:25%;">
									<label>Manager Sign</label>
							</div>
							<div class="footer_left" style="width:35%;">
									<label>Operator </label><br>
									<?php echo  $paymentdetails['paydetails']['emp'] .'-'. date("d-m-y h:i:sa"); ?>
							</div>
						</div>
	
</body>
</html>
<script type="text/javascript">
window.print();
</script>