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

									<img src="<?php echo base_url() ?>assets/img/logo.png?<?php time()?>" />

								</a>

							</td>

						</tr>



<tr>

	<td align="center" class="titleblock" style="padding:7px 0">

		<font size="2" face="Open-sans, sans-serif" color="#555454">

			<span class="title" style="font-weight:500;font-size:28px;text-transform:uppercase;line-height:33px">Hi <?php echo $name ?>,</span><br/>

			<?php if($type == 1 ) { ?>

			<span class="subtitle" style="font-weight:500;font-size:16px;text-transform:uppercase;line-height:25px">Thank you for creating an account at <?php echo $company['company_name'];?> online  Purchase plan.</span>

			<?php } ?>

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

						Login details						</p>

						<span style="color:#777;line-height:5px;">

							Here are your login details:<br /> 

							<span style="color:#333"><strong>Mobile No.: </strong></span> <?php echo $schData['mobile']; ?><br />

							<span style="color:#333"><strong>Password: </strong></span> <?php echo $schData['passwd']; ?>

						</span>

						<br/>

						<strong style="color:#4485F5"> Kindly change your password.</strong><br/><br />

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



<tr>

	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">

		<table class="table" style="width:100%">

			<tr>

				<td width="10" style="padding:7px 0">&nbsp;</td>

				<td style="padding:7px 0">

					<font size="2" face="Open-sans, sans-serif" color="#555454">

						<p style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">Important Security Tips:</p>

						<ol style="margin-bottom:0">

							<li>Always keep your account details safe.</li>

							<li>Never disclose your login details to anyone.</li>

							<li>Change your password regularly.</li>

							<li>Should you suspect someone is using your account illegally, please notify us immediately.</li>

						</ol>

					</font>

				</td>

				<td width="10" style="padding:7px 0">&nbsp;</td>

			</tr>

		</table>

	</td>

</tr>



<?php }else if($type == 0) { ?>

<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
						OTP for verification </p>
						<span style="color:#777">
						Your OTP to change <?php echo $company['company_name'];?> employee login password is <?php echo $otp;?>.
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

	

<?php }else if($type == 2) { ?>

<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
						Password change </p>
						<span style="color:#777">
					 Your password has been changed successfully for <?php echo $company['company_name'];?> Employee login.
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

<?php } else if($type == 3) { ?>

<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
						Account closing verification</p>
						<span style="color:#777">
					 	To verify your identity, please use the following code:
					 	<br/><br/>
					 	<b style="font-size: 30px !important;"><?php echo $otp;?></b>.
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

<?php } else if($type == 4) { ?>

<tr>
	<td class="box" style="border:1px solid #D6D4D4;background-color:#f8f8f8;padding:7px 0">
		<table class="table" style="width:100%">
			<tr>
				<td width="10" style="padding:7px 0">&nbsp;</td>
				<td style="padding:7px 0">
					<font size="2" face="Open-sans, sans-serif" color="#555454">
						<p data-html-only="1" style="border-bottom:1px solid #D6D4D4;margin:3px 0 7px;text-transform:uppercase;font-weight:500;font-size:18px;padding-bottom:10px">
						Payment OTP</p>
						<span style="color:#777">
					 	Your OTP for saving Purchase plan payment:
					 	<br/><br/>
					 	<b style="font-size: 30px !important;"><?php echo $otp;?></b>.Will expire within <?php echo $duration ?>&nbsp; Sec
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

	<td class="space_footer" style="padding:0!important">&nbsp;</td>

</tr>

<?php if($type != 0) { ?>

<tr>

	<td class="linkbelow" style="padding:7px 0">

		<font size="2" face="Open-sans, sans-serif" color="#555454">

			<span><strong>Note:</strong>You received this mail, because it was registered in <?php echo $company['company_name'];?> saving Purchase plan. Please ignore this mail if it's not relevant to you.</span>

		</font>

	</td>

</tr>

<?php } ?>

						<tr>

							<td class="space_footer" style="padding:0!important">&nbsp;</td>

						</tr>

						

					</table>

				</td>

				<td class="space" style="width:20px;padding:7px 0">&nbsp;</td>

			</tr>

		</table>

	</body>

</html>