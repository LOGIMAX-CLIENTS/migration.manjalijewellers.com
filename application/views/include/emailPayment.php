<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">





<html>





	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">





		





		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />





		





		





		<style>	@media only screen and (max-width: 300px){ 





				body {





					width:218px !important;





					margin:auto !important;





				}





				.table {width:195px !important;margin:auto !important;}





				.logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto !important;display: block !important;}		





				span.title{font-size:20px !important;line-height: 23px !important}





				span.subtitle{font-size: 14px !important;line-height: 18px !important;padding-top:10px !important;display:block !important;}		





				td.box p{font-size: 12px !important;font-weight: bold !important;}





				.table-recap table, .table-recap thead, .table-recap tbody, .table-recap th, .table-recap td, .table-recap tr { 





					display: block !important; 





				}





				.table-recap{width: 200px!important;}





				.table-recap tr td, .conf_body td{text-align:center !important;}	





				.address{display: block !important;margin-bottom: 10px !important;}





				.space_address{display: none !important;}	





			}





	@media only screen and (min-width: 301px) and (max-width: 500px) { 





				body {width:308px!important;margin:auto!important;}





				.table {width:285px!important;margin:auto!important;}	





				.logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}	





				.table-recap table, .table-recap thead, .table-recap tbody, .table-recap th, .table-recap td, .table-recap tr { 





					display: block !important; 





				}





				.table-recap{width: 295px !important;}





				.table-recap tr td, .conf_body td{text-align:center !important;}





				





			}





	@media only screen and (min-width: 501px) and (max-width: 768px) {





				body {width:478px!important;margin:auto!important;}





				.table {width:450px!important;margin:auto!important;}	





				.logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}			





			}





	@media only screen and (max-device-width: 480px) { 





				body {width:308px!important;margin:auto!important;}





				.table {width:285px;margin:auto!important;}	





				.logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}





				





				.table-recap{width: 295px!important;}





				.table-recap tr td, .conf_body td{text-align:center!important;}	





				.address{display: block !important;margin-bottom: 10px !important;}





				.space_address{display: none !important;}	





			}





</style>











	</head>





	<body style="-webkit-text-size-adjust:none;background-color:#fff;width:650px;font-family:Open-sans, sans-serif;color:#555454;font-size:13px;line-height:18px;margin:auto">





		<table class="table table-mail" style="width:100%;margin-top:10px;-moz-box-shadow:0 0 5px #afafaf;-webkit-box-shadow:0 0 5px #afafaf;-o-box-shadow:0 0 5px #afafaf;box-shadow:0 0 5px #afafaf;filter:progid:DXImageTransform.Microsoft.Shadow(color=#afafaf,Direction=134,Strength=5)">





			<tr>





				<td class="space" style="width:20px;padding:7px 0">&nbsp;</td>





				<td align="center" style="padding:7px 0">





					<table class="table" bgcolor="#ffffff" style="width:100%">





						<tr>





							<td align="center" class="logo" style="border-bottom:4px solid #333333;padding:7px 0">





								<a title="{shop_name}" href="{shop_url}" style="color:#337ff1">





									<img src="<?php echo base_url() ?>assets/img/receipt_logo.png?<?php time()?>" />





								</a>





							





							</td>





						</tr>











<tr>





	<td align="center" class="titleblock" style="padding:7px 0">





		<font size="2" face="Open-sans, sans-serif" color="#555454">





			<span class="title" style="font-weight:500;font-size:28px;text-transform:uppercase;line-height:33px">Hi <?php echo $payData['firstname'].' '.$payData['lastname']; ?></span>





			<?php if($type != -1 && $payData['id_payment_status'] == 1) { ?>





			<br/>





			<span class="subtitle" style="font-weight:500;font-size:16px;text-transform:uppercase;line-height:25px">Thank you for payment with <?php echo $company_details['company_name']?></span>





			<?php } ?>





		</font>





	</td>





</tr>





<tr>





	<td class="space_footer" style="padding:0!important">&nbsp;</td>





</tr>





<?php if($type != -1) { ?>


<?php if($payData['id_payment_status'] == 1) { ?>





<tr>





	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">





		<table class="table" style="width:100%">





			<tr>





				<td width="10" style="padding:7px 0">&nbsp;</td>





				<td style="padding:7px 0">





					<font size="2" face="Open-sans, sans-serif" color="#555454">





						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">





							Payment No. <?php echo $payData['id_payment'].' - '.$payData['payment_mode'] ?> .</p>





						<span style="color:#777">





						Your payment to the <?php echo $company_details['company_name']?> for the Purchase plan - <?php echo $payData['scheme_name'] ?> is processed successfully.





						</span>





					</font>





				</td>





				<td width="10" style="padding:7px 0">&nbsp;</td>





			</tr>





		</table>





	</td>





</tr>





<tr>





	<td class="space_footer" style="padding:0!important">&nbsp;</td>





</tr>


<?php } ?>


<tr>





	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">





		<table class="table" style="width:100%">





			<tr>





				<td width="10" style="padding:7px 0">&nbsp;</td>





				<td style="padding:7px 0">





				  <font size="2" face="Open-sans, sans-serif" color="#555454">





				  <?php if($type == 1) { ?>





						<p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">





							You have selected to pay by other mode</p>





						<span style="color:#777">





							Following are the details of your payment<br />


							<table class="table" style="width:100%;font-size:12px;padding-top: 5px;">


							<tbody>


							<tr><td style="width: 25%">


							<span style="color:#333"><strong>Amount</strong></span></td><td > <?php echo $payData['payment_amount'] ?><br />


</td></tr>


							<tr><td style="width: 25%">


							<span style="color:#333"><strong>Payment Reference No</strong></span></td><td > <?php echo $payData['payment_ref_number'] ?><br />


</td></tr>


							<tr><td style="width: 25%">


							<span style="color:#333"><strong>Bank Name</strong></span></td><td > <?php echo $payData['bank_name'] ?><br>


</td></tr>


							<tr><td style="width: 25%">


							<span style="color:#333"><strong>Bank Branch</strong></span> </td><td ><?php echo $payData['bank_branch'] ?><br />


</td></tr>


							<tr><td style="width: 25%">


							<span style="color:#333"><strong>Bank A/c No.</strong></span> </td><td ><?php echo $payData['bank_acc_no'] ?><br />


</td></tr>


							<tr><td style="width: 25%">


							<span style="color:#333"><strong>Payment Date</strong></span></td><td > <?php echo $payData['date_payment'] ?>


</td></tr>


							


							


							</tbody>


							</table>


						</span>





					<?php } else if($type == 2) { ?>





					<p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">





							You have selected to pay by <?php echo $payData['payment_mode'] ?></p>





						<span style="color:#777">





							Following are the details of your payment:<br /></span>





							<table class="table" style="width:100%;font-size:12px;padding-top: 5px;">





							<tbody>





							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Payment Amount</strong></span></td><td> <?php echo $payData['currency_symbol'].' '.$payData['payment_amount'] ?><br />	</td></tr>





							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Transaction ID</strong></span> </td><td><?php echo $payData['trans_id'] ?><br>	</td></tr>





							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Payment Date</strong></span></td><td> <?php echo $payData['date_payment'] ?><br />	</td></tr>





							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Group Code</strong></span> </td><td><?php echo $payData['code'] ?><br />	</td></tr>


<tr><td style="width: 25%">





							<span style="color:#333"><strong>Membership No.</strong></span> </td><td><?php echo $payData['scheme_acc_number'] ?><br />	</td></tr>





								</tbody>





							</table>





						<?php } else if($type == 3) { ?>





					<p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">





							You have selected to pay by <?php echo $payData['payment_mode'] ?></p>





						<span style="color:#777">





							Following are the details of your payment:<br /></span>





							<table class="table" style="width:100%;font-size:12px;padding-top: 5px;">





							<tbody>





							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Payment Amount</strong></span></td><td> <?php echo $payData['currency_symbol'].' '.$payData['payment_amount'] ?><br />	</td></tr>





							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Transaction ID</strong></span> </td><td><?php echo $payData['trans_id'] ?><br>	</td></tr>





							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Payment Date</strong></span></td><td> <?php echo $payData['date_payment'] ?><br />	</td></tr>





							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Group Code</strong></span> </td><td><?php echo $payData['code'] ?><br />	</td></tr>

							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Membership No.</strong></span> </td><td><?php echo $payData['scheme_acc_number'] ?><br />	</td></tr>


							<tr><td style="width: 25%">





							<span style="color:#333"><strong>Status</strong></span> </td><td><?php echo $payData['payment_status'] ?><br />	</td></tr>





								</tbody>





							</table>





						





					<?php } ?>





					</font>





				</td>





				<td width="10" style="padding:7px 0">&nbsp;</td>





			</tr>





		</table>





	</td>





</tr>





<?php } else {?> 





<tr>





	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">





		<table class="table" style="width:100%">





			<tr>





				<td width="10" style="padding:7px 0">&nbsp;</td>





				<td style="padding:7px 0">





					<font size="2" face="Open-sans, sans-serif" color="#555454">





						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">





							Payment processing error</p>





						<span style="color:#777">





						There is a problem with your payment for <?php echo $company_details['company_name']?>. Please contact customer care at your earliest convenience.<br/>


						Phone :<?php echo $company_details['phone']?><br/>


						Mobile:<?php echo $company_details['mobile']?><br/>





						</span>





					</font>





				</td>





				<td width="10" style="padding:7px 0">&nbsp;</td>





			</tr>





		</table>





	</td>





</tr>





<?php } ?>





<tr>





	<td class="space_footer" style="padding:0!important">&nbsp;</td>





</tr>





<tr>





	<td class="linkbelow" style="padding:7px 0">





		<font size="2" face="Open-sans, sans-serif" color="#555454">


		


<?php if($payData['id_payment_status'] == 1){?>


	


			<span>





				You can view your payment details and download your receipt from online saving account.<a href="<?php echo site_url()?>">Click here</a><br/></span>


<?php }?>





		</font>





	</td>





</tr>





<tr>





	<td class="linkbelow" style="padding:7px 0">





		<font size="2" face="Open-sans, sans-serif" color="#555454">


			


<span><strong>Note:</strong>You received this mail, because it was registered in <?php echo $company_details['company_name']?> saving Purchase plan. Please ignore this mail if it's not relevant to you.</span>





		</font>





	</td>





</tr>

















						<tr>





							<td class="space_footer" style="padding:0!important">&nbsp;</td>





						</tr>





						<tr>





							<td class="footer" style="border-top:4px solid #333333;padding:7px 0">





							</td>





						</tr>





					</table>





				</td>





				<td class="space" style="width:20px;padding:7px 0">&nbsp;</td>





			</tr>





		</table>





	</body>





</html>