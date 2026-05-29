<html>

<head>

	<meta charset="utf-8">
	<title>Karigar Receipt</title>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/karigar_receipt.css">
	<style>
		.head {
			color: black;
			font-size: 35px;
		}
		/* .bank_details{
			position: fixed;
			height: 100px;
			bottom: 0;
		} */
		.alignCenter {
			text-align: center;
		}

		.alignRight {
			text-align: right;
		}

		.table_heading {
			font-weight: bold;
		}

		.textOverflowHidden {
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.duplicate_copy * {
			font-size: 9px;
		}

		.duplicate_copy #pp th,
		.duplicate_copy #pp td {
			font-size: 10px !important;
		}




		.return_dashed {
			width: 2400%;
		}

		.old_metal_dashed {
			width: 2550%;
		}

		.stones,
		.charges {
			font-style: italic;
		}

		.stones .stoneData,
		.charges .chargeData {
			font-size: 14px !important;
		}

		.addr_labels {
			display: inline-block;
			width: 30%;
		}

		.addr_values {
			display: inline-block;
			padding-left: -5px;
		}

		.rate_labels {
			display: inline-block;
			width: 30%;
		}

		.addr_brch_labels {
			display: inline-block;
			width: 30%;
		}

		.addr_brch_values {
			display: inline-block;
			padding-left: 2px;
		}

		.page {
			height: 980px;
			/* Set page height */
			page-break-after: always;
			/* Force page break after each page */
			position: relative;
			/* Enable positioning of child elements */
			box-sizing: border-box;
			/* Include borders and padding in element's total width and height */
			padding: 40px;
			/* Add padding */
			border: 1px solid black;
			background-color: #fff;
			/* Set background color */
			margin-bottom: 20px;
			/* Add margin bottom to separate pages */
		}
		.flex_div{
			display: flex;
		}
	</style>
    
</head>

<body>
</head>


<body>
<span class="PDFReceipt">
<!-- <div class="" align="center">
					<h2>KARIGAR REPORT</h2>
			</div> -->
            <div class="flex_div">
				<div style="width:100%;">
				<div class="" align="center">
				<img src="<?php echo dirname(base_url()) ?>/assets/img/logo.png" />
			    <h2><?php echo strtoupper($comp_details['company_name']); ?>
					</h2>
				</div>
				<div class="" align="center" width="20%;">
					<h2>
						<?php echo strtoupper($comp_details['address1']); ?>
					</h2>
				</div>
				<div class="" align="center" width="20%;">
					<h2>
						<?php echo strtoupper($comp_details['address2']); ?>
					</h2>
				</div>
				<div class="" align="center" width="20%;">
					<h2>
						<?php echo strtoupper($comp_details['address3']); ?>
					</h2>
				</div>
				<div class="" align="center" width="20%;">
					<h2>VENDOR CONTRACT AMENDMENTS</h2>
				</div>
				</div>
			</div>
			
			
			
<?php 
function moneyFormatIndia($num)

{

	return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);

}

?>

	<div class="pawge">
		<span class="PDFReceipt">

			<div class="maindiv" style="margin-top:0px !important; ">
				<div class="manepally"> </div>
				<!-- <div class="header_top"> -->

				</div><br>
				<div style="width: 100%; text-transform:uppercase;height:140px; font-weight:bold;">
					<?php if ($karigar['billing_for'] == 1 || $karigar['billing_for'] == 2) { ?>		
					<?php } else { ?>
						<div class="flex_div">
						<div style="display: inline-block; width: 50%; font-weight: bold">
                            <label><?php echo '<div class="addr_labels">Name</div><div class="addr_values">:&nbsp;&nbsp;' . 'Mr./Ms.' . $karigar['karigar'] . "</div>"; ?></label><br>
							<label><?php echo '<div class="addr_labels">Mobile</div><div class="addr_values">:&nbsp;&nbsp;' . $karigar['contactno1'] . "</div>"; ?></label><br>
							<label><?php echo ($karigar['address1'] != '' ? '<div class="addr_labels">Address</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($karigar['address1']) . ',' . "</div><br>" : ''); ?></label>
                            <label><?php echo ($karigar['address2'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($karigar['address2']) . ($karigar['address3'] != '' ? ',' . $karigar['address3'] . '.' : '') . "</div><br>" : ''); ?></label>
							<!-- <label><php echo ($karigar['address3'] != '' ? '<div class="addr_labels"></div><div class="addr_values">&nbsp;&nbsp;&nbsp;' . strtoupper($karigar['address3']) . '.' . "</div><br>" : ''); ?></label> -->
							<label><?php echo ($karigar['city'] != '' ? '<div class="addr_labels">city</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($karigar['city']) . ($karigar['pincode'] != '' ? ' - ' . $karigar['pincode'] . '.' : '') . "</div><br>" : ''); ?></label>
							<label><?php echo ($karigar['state'] != '' ? '<div class="addr_labels">State</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($karigar['state'] . '-' . $karigar['state_code']) . ',' . "</div><br>" : ''); ?></label>
							<label><?php echo ($karigar['country'] != '' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($karigar['country']) . "</div><br>" : ''); ?></label> 
                            <label><?php echo ($karigar['gst_number'] != '' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($karigar['gst_number']) . "</div><br>" : ''); ?></label>  
							<!-- <label><php echo (isset($karigar['gst_number']) && $karigar['gst_number'] != '' ? '<div class="addr_labels">GST IN</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($karigar['gst_number']) . "</div><br>" : ''); ?></label> -->
                            <label><?php echo ($karigar['code_karigar'] != '' ? '<div class="addr_labels">Dealer Id</div><div class="addr_values">:&nbsp;&nbsp;' . strtoupper($karigar['code_karigar']) . "</div><br>" : ''); ?></label>            
						
						</div>
						<div style="display: inline-block; width: 50%; font-weight: bold; text-align: right;">
						<?php foreach($kyc as $k){ ?>
								<label><?php echo ($k['kyc_name'] != '' ? '<div class="addr_values">' . strtoupper($k['kyc_name']).'&nbsp;&nbsp&nbsp;&nbsp:&nbsp;&nbsp;'  . "</div><div class='addr_labels' style='text-align:left !important;'>".$k['kyc_number']."</div><br>" : ''); ?></label>
								
						<?php	} ?>
						</div>
							
						</div>
						

					<?php } ?>

					<div>
                            <!-- <div style=" text-align:center;margin-top:-120px !important; font-size:9px;">E-Bill</div> -->

						<!-- <div align="center"><img src="<?php echo base_url(); ?>bill_qrcode/<?php echo $qrfilename; ?>.png" style="margin-top:0px" width="130" height="130" src="F:\xampp\htdocs\etail_v5\admin\assets\img\billing\02090.jpg"></div> -->

					</div>

					<div style="display: inline-block; width: 10%; padding-left:20px;"></div>
                    
                    <div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
				<div id="printable" >
						<div  class="row">
                        
							<div class="col-xs-12">
								<div class="table-responsive"  >
								    <?php 
								    if(sizeof($wastage)>0)
								    {?>
								        <h2 width="25%;" style="text-align:center;"> Product pricing </h2>
										<hr class="item_dashed">
            								<table id="wastage" class="table text-center">
            									    <thead>
                                                     <tr>
            												<th width="8%;" style="text-align:left;">S.NO</th>
            												<th  width="25%;" style="text-align:left;">CATEGORIES</th>
            												<th  width="25%;"style="text-align:left;">PRODUCT</th>		
            												<th  width="20%;" style="text-align:left;">DESIGN</th>
            												<th  width="20%;" style="text-align:left;">SUB DESIGN</th>
            												<th  width="20%;" style="text-align:left;">PURITY</th>
            												<th  width="20%;" style="text-align:left;">TOUCH</th>
                                                            <th  width="20%;" style="text-align:left;">VA%</th>
                                                            <th  width="20%;" style="text-align:left;">MC VALUE</th>
                                                            <th  width="20%;" style="text-align:left;">REQ DATE</th>
                                                            <th  width="20%;" style="text-align:left;">APPR DATE</th>
                                                            <th  width="20%;" style="text-align:left;">Created By</th>
                                                            <th  width="20%;" style="text-align:left;">Approved by</th>
            											</tr>
            										</thead>
            										<tbody>
                                                        <td colspan="13">
                                                            <hr class="item_dashed">
                                                        </td>   
                                                            <?php 
                                                            {	
																$i=1;
                                                            foreach($wastage as $ikey => $ival){?>
                                                                	<tr>
                														<!-- <td style="text-align:left;"><?php echo $ival['id_karikar_wast'];?></td> -->
                														<td style="text-align:left;"><?php echo $i++;?></td>
                														<td style="text-align:left;"><?php echo $ival['name'];?></td>
                														<td style="text-align:left;"><?php echo $ival['product_name'];?></td>
                														<td style="text-align:left;"><?php echo $ival['design_name'];?></td>
                														<td style="text-align:left;"><?php echo $ival['sub_design_name'];?></td>
                														<td style="text-align:left;"><?php echo number_format($ival['purity'],2,'.','')+0;?></td>
                                                                        <td style="text-align:left;"><?php echo $ival['pur_touch'];?></td>
                                                                        <td style="text-align:left;"><?php echo $ival['wastage_per'];?></td>
                                                                        <td style="text-align:left;"><?php echo ($ival['mc_value']>0?$ival['mc_value'].'/'.($ival['mc_type']==1?'Pcs':'Grm'):$ival['mc_value']);?></td>
                                                                        <td style="text-align:left;"><?php echo $ival['requested_date'];?></td>
                                                                        <td style="text-align:left;"><?php echo $ival['approved_date'];?></td>
                                                                        <td style="text-align:left;"><?php echo $ival['created_by'];?></td>
                                                                        <td style="text-align:left;"><?php echo $ival['approved_by'];?></td>
                													</tr>
                                                            <?php }
                                                            }?> 
                                                            <td colspan="13">
            													<hr class="item_dashed">
            												</td>  
            									</tbody>
            							</table><br>
								    <?php }
								    ?>
								    
								    <?php 
								    if(sizeof($stone)>0)
								    {?>
								        <h2 width="25%;" style="text-align:center;">Stone/Diamond</h2>
        								<hr class="item_dashed">
										<table id="wastage" class="table text-center">
        									
        											<tr>
        												 <th style="text-align:left; width: 7%;">S.NO</th>
        												<th  style="text-align:left; width: 15%;">STONE TYPE</th>
        												<th  style="text-align:left; width: 15%;"> STONE NAME</th>		
        												<th  style="text-align:left; width: 7%;"> UOM</th>
        												<th  style="text-align:left; width: 15%;">CALC TYPE</th>
														<th  style="text-align:left; width: 18%;"> QUALITY </th>
														<th  style="text-align:left; width: 15%;"> Clarity </th>
														<th  style="text-align:left; width: 15%;"> COLOR </th>
														<th  style="text-align:left; width: 15%;"> CUT </th>
														<th  style="text-align:left; width: 15%; white-space: normal;"> SHAPE </th>
														<th  style="text-align:right; width: 15%;">FROM Cent</th>
														<th  style="text-align:right; width: 15%;">TO Cent</th>
        												<th  style="text-align:right; width: 15%;">RATE</th>
        												<!-- <th  width="20%;" style="text-align:left;">TOTAL AMOUNT</th> -->
        											</tr>
        											<!-- <tr>
        												<td><hr class="item_dashed1"></td>
        											</tr> -->
        										</thead>
        										<tbody>
                                                <td colspan="13">
        													<hr class="item_dashed">
        												</td>   
                                                        <?php 
                                                        {	
															$j=1;
                                                            foreach($stone as $ikey => $ival)
                                                            {  
                                                                if($ival['stone_cal_type']==1)
                                                                {
                                                           
																$stonetypes = "Wt";
                                                                }
                                                                else if($ival['stone_cal_type']==2)
                                                                {
																$stonetypes = "Amt";
                                                                }
                                                                ?>

                                                             <tr>
        														<!-- <td style="text-align:left;"><?php echo $ival['id_karigar_stone'];?></td> -->
        														<td style="text-align:left;"><?php echo $j++;?></td>
        														<td style="text-align:left;"><?php echo $ival['stone_type'];?></td>
        														<td style="text-align:left;"><?php echo $ival['stone_name'];?></td>
        														<td style="text-align:left;"><?php echo $ival['uom_code'];?></td>
        														<td style="text-align:left;"><?php echo $stonetypes ;?></td>
																<td style="text-align:left;"><?php echo $ival['code'];?></td>
																<td style="text-align:left;"><?php echo $ival['clarity'];?></td>
																<td style="text-align:left;"><?php echo $ival['color'];?></td>
																<td style="text-align:left;"><?php echo $ival['cut'];?></td>
																<td style="text-align:left;"><?php echo $ival['shape'];?></td>
																<td style="text-align:right;"><?php echo $ival['from_wt'];?></td>
        														<td style="text-align:right;"><?php echo $ival['to_wt'];?></td>
																<td style="text-align:right;"><?php echo moneyFormatIndia($ival['rate_per_gram']);?></td>
        													</tr>
                                                            <?php }   
                                                        }?> 
                                                        <td colspan="13">
        													<hr class="item_dashed">
        												</td>  
        									</tbody>
        									</table><br>
								    <?php }
								    ?>
								    
								    <?php 
								    if(sizeof($bank)>0)
								    {?>
                                     <div class="bank_details">
									 <h2 width="25%;" style="text-align:center;">BANK DETAILS</h2>
                                        <table id="wastage" class="table text-center">
                                            <thead>
                                                <td colspan="4">
                                                    <hr class="item_dashed">
                                                </td>
                                                <tr>
                                                    <th width="7%;" style="text-align:left;">BANK NAME</th>
                                                    <th  width="25%;" style="text-align:left;"> ACCOUNT HOLDER NAME</th>
                                                    <th  width="20%;"style="text-align:left;">ACCOUNT NUMBER</th>		
                                                    <th  width="20%;" style="text-align:left;">IFSC CODE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <td colspan="4">
                                                    <hr class="item_dashed">
                                                </td>   
                                                    <?php 
                                                    {	
                                                    foreach($bank as $ikey => $ival)
                                                    {?>
                                                    <tr>
                                                        <td style="text-align:left;"><?php echo $ival['bank_name'];?></td>
                                                        <td style="text-align:left;"><?php echo $ival['account_name'];?></td>
                                                        <td style="text-align:left;"><?php echo $ival['account_number'];?></td>
                                                        <td style="text-align:left;"><?php echo $ival['ifsc_code'];?></td>
                                                        </tr>
                                                    <?php }
                                                    }?> 
                                                    <td colspan="4">
                                                        <hr class="item_dashed">
                                                    </td>  
                                            </tbody>
                                        </table>
									 </div>  
									   <br>
								    <?php }
								    ?>
                                

				</div>
            </div>
		</div></div></div></div></div>			
                <script type="text/javascript">
				window.print();
			</script>
		
		</span>

</body>
</html>
