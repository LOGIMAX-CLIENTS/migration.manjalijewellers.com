<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<?php 
$company_details = $this->login_model->company_details();
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
									<img src="<?php echo base_url() ?>assets/img/logo.png?<?php time()?>" />
								</a>
							</td>
						</tr>

<tr>
	<td align="center" class="titleblock" style="padding:7px 0">
		<font size="2" face="Open-sans, sans-serif" color="#555454">
			<span class="title" style="font-weight:500;font-size:28px;text-transform:uppercase;line-height:33px">Hi <?php echo ucfirst($walData['name']); ?></span>
			<?php if($type == 1) {  ?>
			<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px;">
							Wallet Account No. <?php echo $walData['wallet_acc_number'] ?></p>
							<span style="color:#777">
							 Your <?php echo $company_details['company_name']?> saving scheme wallet account is successfully created for your account.<br /><br />
							Following are the details of your wallet account:<br /></span>
							<table class="table" style="width:100%;padding-top: 5px;">
							<tbody>
							<tr><td style="width:20%">
							<span style="color:#333"><strong>Mobile No.</strong></span> </td><td><?php echo $walData['mobile'] ?><br />
							</td></tr>
							
							<tr><td style="width:20%">
							<span style="color:#333"><strong>Issued Date</strong></span></td><td> <?php echo $walData['issued_date'] ?><br /></td></tr>
							<tr><td style="width:20%">
							<span style="color:#333"><strong>Active</strong></span></td><td> <?php echo ($walData['active']=='1'?"Yes":"No");   ?></td></tr>
							</tbody>
							</table>
							<br />
						
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
<?php }else if($type == 15)  { ?> 

		<span class="title" style="font-size:20px; line-height:33px"> Invite - How it works</span><br/>
		<span class="title" style="font-size:14px;line-height:33px"> Use following links for enrolling Saving Schemes with <?php echo $company_details['company_name']; ?> </span><br/>

<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px;">Mobile app</p>
						<ol style="margin-bottom:0">
							<li>Download <?php echo $company_details['company_name']; ?> app  <?php echo $applink; ?> for joining saving schemes</li>
						</ol>
					</font>
				</td>
				<td width="10" style="padding:7px 0">&nbsp;</td>
			</tr>
		</table>
	</td>
</tr>
 <div>
    <div>
    </div> 
</div>
<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px;">Web app</p>
						<ol style="margin-bottom:0">
							<li>Use this link  <?php echo $weblink; ?> for joining saving schemes in PCs </li>
							<li>Use this code <?php echo $referral; ?> for enrollment</li>
							<li> Get more benefits</li>
							<li> Share this app to your friends and get credits in your wallet once your friends paid their first installment</li> 
						</ol>
					</font>
				</td>
				<td width="10" style="padding:7px 0">&nbsp;</td>
			</tr>
		</table>
	</td>
</tr>

<?php }else{  ?>




			<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px;">
							Wallet Transaction</p>
							<span style="color:#777">
							 Your <?php echo $company_details['company_name']?> saving scheme wallet transaction is successfully processed.<br /><br />
							Following are the details of your wallet transaction:<br /></span>
							<table class="table" style="width:100%;padding-top: 5px">
							<tbody>
							<tr><td style="width:20%">
							<span style="color:#333"><strong>Mobile No.</strong></span> </td><td><?php echo $walData['mobile'] ?><br />
							</td></tr>
							<tr><td style="width:20%">
							<span style="color:#333"><strong>Wallet A/c No.</strong></span> </td><td><?php echo $walData['wallet_acc_number'] ?><br />
							</td></tr>
							<tr><td style="width:20%">
							<span style="color:#333"><strong>Transaction Date</strong></span></td><td> <?php echo $walData['date_transaction'] ?><br /></td></tr>
							
							<tr><td style="width:20%">
							<span style="color:#333"><strong>Point(<?php echo ($walData['transaction_type']=='0'?'Issue':'Redeem');?>)</strong></span></td><td> <?php echo $walData['value']?></td></tr>
							
							</tbody>
							</table>
							<br />
						
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
			<span><strong>Note:</strong>You received this mail, because it was registered in  <?php echo $company_details['company_name']?> saving scheme. Please ignore this mail if it's not relevant to you.</span>
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
</html><?php

?>