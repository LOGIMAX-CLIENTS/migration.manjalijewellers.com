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







			<span class="title" style="font-weight:500;font-size:28px;text-transform:uppercase;line-height:33px">Hi <?php echo $schData['firstname'].' '.$schData['lastname']; ?></span>







			







		</font>







	</td>







</tr>







<tr>







	<td class="space_footer" style="padding:0!important">&nbsp;</td>







</tr>







<?php if($type == 1) { ?>







<tr>







	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">







		<table class="table" style="width:100%">







			<tr>







				<td width="10" style="padding:7px 0">&nbsp;</td>







				<td style="padding:7px 0">







					<font size="2" face="Open-sans, sans-serif" color="#555454">







						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">







							Membership No. <?php echo $schData['scheme_acc_number']; ?></p>







						







						<span style="color:#777">







						Thanks for joining with <?php echo $company['company_name']?> Purchase Plan - <strong><?php echo $schData['scheme_name'] ?>.<br/><br/>







						Following are the details of the Purchase Plan you have joined:<br /><br /></span>







							<table class="table" style="width:100%;font-size:12px">







							<tbody>







							<tr><td style="width: 25%">







							<span style="color:#333"><strong>A/c Name</strong></span> </td><td><?php echo $schData['account_name'] ?><br />







							</td></tr>







							<tr><td style="width: 25%">







							<span style="color:#333"><strong>Purchase plan Name</strong></span> </td><td><?php echo $schData['scheme_name'] ?><br />







							</td></tr>







							<tr><td style="width: 25%">







							<span style="color:#333"><strong>Total Installments</strong></span></td><td> <?php echo $schData['total_installments'] ?><br /></td></tr>





















							







				    	<tr><td style="width: 25%">







							<span style="color:#333"><strong>Payable</strong></span>







							</td><td> 







							<?php







							 if(($schData['scheme_type'] == "0" || $schData['scheme_type'] == "2")) 	







							 {







							 echo $schData['currency_symbol'].' '.$schData['amount'].' /month ' ;







							 }else if($schData['scheme_type'] == "3" && (!empty($schData['max_amount']) || $schData['max_amount']!="0")){
								 
								 
								 
								 
								  //echo 'Min.'.number_format($schData['min_amount'],'3','.','').' month <br/>';


							    echo 'Min.'.$schData['currency_symbol'].' '.$schData['min_amount'].' /month ' ;




							  //echo 'Max.'.number_format($schData['max_amount'],'3','.','').' month ';//
								 
							   echo 'Max.'.$schData['currency_symbol'].' '.$schData['max_amount'].' /month ' ; 
								 
								 
								 
								 
							 }else if($schData['scheme_type'] == "3" && (!empty($schData['max_weight']) || $schData['max_weight']!="0")){





							  echo 'Min.'.$schData['currency_symbol'].' '.$schData['min_amount'].' /month ' ;



							   echo 'Max.'.number_format($schData['max_weight'],'3','.','').' g/month ';







							  }else{







							 echo 'Min.'.number_format($schData['min_weight'],'3','.','').' g/month <br/>';







							 echo 'Max.'.number_format($schData['max_weight'],'3','.','').' g/month ';







							  }?><br />







							 </td></tr>







							







							







							</tbody>







							</table>







							<br />







						<strong> Please proceed for the payment in payment section of our site. <a href="<?php echo site_url()?>">Click to pay</a></strong>







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







<?php } else if($type == 2){?> 







<tr>







	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">







		<table class="table" style="width:100%">







			<tr>







				<td width="10" style="padding:7px 0">&nbsp;</td>







				<td style="padding:7px 0">







					<font size="2" face="Open-sans, sans-serif" color="#555454">







						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">







							Purchase plan Account No. <?php echo $accData['scheme_acc_number'] ?></p>







						







						<span style="color:#777">







						Thanks for your registration with <?php echo $company['company_name']?> Purchase Plan<strong>.<br/><br/>







						Following are the details of the Purchase Plan you have registered:<br /><br /></span>







							<table class="table" style="width:100%;font-size:12px">







							<tbody>







							<tr><td >







							<span style="color:#333"><strong>A/c Name</strong></span> </td><td><?php echo $accData['account_name'] ?><br />







							</td></tr>







							<tr><td >







							<span style="color:#333"><strong>Start Date</strong></span> </td><td><?php echo $accData['start_date'] ?><br />







							</td></tr>







							







							







							</tbody>







							</table>







							<br />







						<strong> Please proceed for the payment in payment section of our site.</strong>







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















<?php } else if($type == -1){?> 







<tr>







	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">







		<table class="table" style="width:100%">







			<tr>







				<td width="10" style="padding:7px 0">&nbsp;</td>







				<td style="padding:7px 0">







					<font size="2" face="Open-sans, sans-serif" color="#555454">







						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px;color:#058D81">







							Error in joining Purchase plan</p>







						<span style="color:#777">







						There is a problem with your Purchase plan joining. Please contact administrator or try again later.







						</span>







					</font>







				</td>







				<td width="10" style="padding:7px 0">&nbsp;</td>







			</tr>







		</table>







	</td>







</tr>







<?php } else if($type==3){ ?>
    <tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
					<!--	<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
							Scheme Account No.</p>-->
						<span style="color:#777">
						Thanks for your Eequiry with <?php echo $company['company_name']?> Purchase Plan<strong>.<br/><br/>

						Following are the details of the Purchase Plan you have Enquired:<br /><br /></span>
							<table class="table" style="width:100%;font-size:12px">
							<tbody>
							<tr><td >
							<span style="color:#333"><strong>Name</strong></span> </td><td><?php echo $name ?><br />
							</td></tr>
							<tr><td >
							<span style="color:#333"><strong>Amount</strong></span> </td><td><?php echo ($amount!='' ?$amount :'-') ?><br />
							</td></tr>
							<tr><td >
							<span style="color:#333"><strong>Weight</strong></span> </td><td><?php echo  ($weight!='' ?$weight :'-') ?><br />
							</td></tr>
							</tbody>
							</table>
							<br />
						<strong></strong>
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







	<td class="space_footer" style="padding:0!important">&nbsp;</td>







</tr>







<tr>







	<td class="linkbelow" style="padding:7px 0">







		<font size="2" face="Open-sans, sans-serif" color="#555454">







			<span><strong>Note:</strong>You received this mail, because it was registered in <?php echo $company['company_name']?> Purchase Plan. Please ignore this mail if it's not relevant to you.</span>







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