<html><head>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<title>Estimation</title>

		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/estimation.css">

		<style type="text/css">

			body, html {

			margin-bottom:0

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

				size: 78mm;

				margin-bottom: 150px !important;

			} 

			.header

			{

			    font-size:12px !important;

			    margin-top:2px !important;

			}

			

			/* output size */

			span { display: inline-block; }

		</style>

	</head>

	<?php

	    $amt_in_words   = $this->ret_billing_model->no_to_words($repair_details['amount']);

		function moneyFormatIndia($num) {

		return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);

	}?>

<body class="plugin">

	<span class="PDFReceipt">

		<div class="printable">

		   

			<div class="header">		

				<b><?php echo $comp_details['company_name'].'-'.$comp_details['name'];?></b><br>

				<?php echo $comp_details['address1'];?><br>

				<?php echo$comp_details['address2'];?><br>

				<?php echo $comp_details['city'].'-'.$comp_details['pincode'].'.';?><br><br>

				<label><b>Repair Receipt</b></label>

			</div>

			<hr class="item_dashed" style="width:115% !important;">

			<p></p>

			<div class="tap_head">

				<div class="">

					<span style="font-weight:bold;"><?php echo $billing['cus_name']; ?> </span><br>

					<span><?php echo ($billing['city']!='' ?  $billing['city'].($billing['pincode']!='' ? '-'.$billing['pincode']:'') :''); ?> </span>

				</div>

			</div><p></p>

			<div>

				<table class="metal_rate" width="120%">

				

					<tbody>

						<tr>

							<td>REC NO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp;<?php echo $billing['bill_no'];?></td>

							<td style="text-align:right;">DATE &nbsp;:&nbsp;<?php echo $billing['bill_date'];?></td>

						</tr>

						

						<tr>

						<td><hr class="item_dashed" style="width:190% !important;"></td>

						</tr>

					

					</tbody>

				</table>

			</div>

				

			

    			<div class="item_details">

        		    <table class="estimation" style="width:115%;">

        				    <tr>

        				        <td class="alignLeft">REPAIR CHARGES</td>

        				        <td class="alignRight"><?php echo $repair_details['taxable_amount']?></td>

        				    </tr>

        				    <tr>

        				        <td class="alignLeft">CGST <?php echo $repair_details['repair_percent']/2;?>%</td>

        				        <td class="alignRight"><?php echo moneyFormatIndia(number_format($repair_details['cgst'],2,'.',''));?></td>

							</tr>

        				    <tr>

        				        <td class="alignLeft">SGST <?php echo $repair_details['repair_percent']/2;?>%</td>

        				        <td class="alignRight"><?php echo moneyFormatIndia(number_format($repair_details['sgst'],2,'.',''))?></td>

        				    </tr>

        				    <tr>

    						    <td><hr class="item_dashed" style="width:120% !important;"></td>

    						</tr>

    						<tr style="font-weight:bold;">

        				        <td class="alignLeft">NET AMOUNT</td>

        				        <td class="alignRight"><?php echo moneyFormatIndia(number_format($repair_details['amount'],2,'.',''))?></td>

        				    </tr>

        				    <tr>

    						    <td><hr class="item_dashed" style="width:120% !important;"></td>

    						</tr>

        				    <tr>

        				        <td style="width:120%;"><?php echo $amt_in_words.' ONLY';?></td>

        				    </tr>

        				    <tr>

        				        <td style="width:120%;"><label>EMP-ID : <?php echo $billing['id_employee'].' / '.$billing['emp_name'].' / '.date('h:i A', strtotime(date('d-m-Y H:i:s')));?></label></td>

        				    </tr>

        				</table>

					

            </div>

        </div>

	 </span>          

</body></html>