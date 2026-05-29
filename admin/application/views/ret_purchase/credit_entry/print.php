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

.heading {

text-align: center;
font-size: 15px !important;

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
			<span style="padding-left: 12px; font-weight: bold; font-size:20px"><?php echo $paymentdetails['trans_type'];?>&nbsp; Entry</span>
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
                <div style="text-align: left !important;width: 100%; display: inline-block;"> 

					<label><b>Issue Type &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </b>&nbsp;&nbsp;&nbsp;<?php echo $paymentdetails['trans_type'];?></label><br>
                    <label><b>Transcation Type : </b>&nbsp;&nbsp;&nbsp;<?php echo $paymentdetails['accountto'];?></label><br>
                    </div>

                </div>
			</td>
			<td width="36%" >
				<div style="text-align: right !important; display: inline-block; vertical-align: top;">
					<div style="text-align: left !important;width: 100%; display: inline-block;"> 
						<label><b>Issue No &nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;&nbsp;<?php echo $paymentdetails['transbillno'];?></label><br>
						<label><b>Issue Date :</b>&nbsp;&nbsp;&nbsp;<?php echo $paymentdetails['transdate'];?></label><br>
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
						<label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;'.$paymentdetails['karigar']."</div>"; ?></label><br>
						<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;'.$paymentdetails['mobile']."</div>"; ?></label><br>
    					<label><?php echo ($paymentdetails['address1']!='' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($paymentdetails['address1']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($paymentdetails['address2']!='' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;'.strtoupper($paymentdetails['address2']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($paymentdetails['city_name']!='' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($paymentdetails['city_name']).($paymentdetails['pincode']!='' ? ' - '.$paymentdetails['pincode'].'.' :'')."</div><br>" :''); ?></label>
    					<label><?php echo ($paymentdetails['state_name']!='' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($paymentdetails['state_name']).','."</div><br>" :''); ?></label>
    					<label><?php echo ($paymentdetails['gst_number']!='' ? '<div class="addr_labels">GST</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($paymentdetails['gst_number']).','."</div><br>" :''); ?></label>
					</div>
				</div>
			</td>
		</tr>
	</table>	
	<table width="921" height="700" border="1" cellpadding="3" cellspacing="0">
		<tr>
			<td height="25" colspan="2">
				<div style="text-align: right !important; display: inline-block; vertical-align: top;">
					<label><b>DESCRPTION</b></label><br>
				</div>
			</td>
			<td width="25%" >
				<div style="text-align: right !important; display: inline-block; vertical-align: top;">
				<label><b>AMOUNT :</b>&nbsp;&nbsp;&nbsp;<?php echo moneyFormatIndia($paymentdetails['transamount']);?></label><br>
				</div>
			</td>
			
		</tr>
	</table>
	<table width="921" height="700" border="1" cellpadding="3" cellspacing="0">
	<tr>
			<td width="25%" height="100" valign="top" style="border-right: none;"colspan="4" >
				<div style="text-align: right !important; display: inline-block; vertical-align: top;">
					<div style="text-align: left !important;width: 100%; display: inline-block;">
				
					<label><?php echo $paymentdetails['remarks'];?></label>
					</div>
				</div>
			</td>

			<td width="25%" height="10px">
            <div style="text-align: right !important; display: inline-block; vertical-align: top; font-weight: bold; font-size:13px">
            </div>    
       	 </td>
<!-- 
			<td width="25%" >
			<div style="text-align: left !important; display: inline-block; vertical-align: top; font-weight: bold; font-size:13px"> -->
			
			
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
									<?php echo  $paymentdetails['emp'] .'-'. date("d-m-y h:i:sa"); ?>
							</div>
						</div>
	
</body>
</html>
<script type="text/javascript">
window.print();
</script>