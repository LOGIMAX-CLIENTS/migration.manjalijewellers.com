<!doctype html>

<html>

	<head>

		<meta charset="utf-8">

		<title>Tag</title>

		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_barcode.css">

	</head>
	<body>

		<div class="printBody">
			<?php foreach($img as $s) { ?>

				<?php 
					$product_name = substr($s['product_name'],0,16);
					$design_name = substr($s['design_name'],0,17);
					$sub_design_name = substr($s['sub_design_name'],0,17);

					$huid = "";

					if($s['hu_id'] != "-" && $s['hu_id'] != "") {
						
						$huid = $huid.$s['hu_id'];

					}

					if($s['hu_id'] != "-" && $s['hu_id'] != "" && $s['hu_id2'] != "-" && $s['hu_id2'] != "") {

						$huid = $huid.",";

					}

					if($s['hu_id2'] != "-" && $s['hu_id2'] != "") {
						
						$huid = $huid.$s['hu_id2'];

					}

					
				?>

				<div class="PDF_CusReceipt">
					<div class="printable" >
						<div class="firstCol">

						</div>
						<div class="secondCol">
							<div class="tag_code"><?php echo $s['tag_code'] ?></div>

							<div class="gwt">G.Wt-<?php echo $s['gross_wt']; ?> 
							                <?php echo ($s['tag_type']==1) ? " - AS" : ""; ?>
							</div>

							<div class="swt"><?php echo $s['sales_mode'] == 1 ? "AMT: Rs.".round($s['sales_value'],0) : (($s['stn_wt'] > 0 || $s['stn_amount'] > 0) ? ("S.Wt-".$s['stn_wt']."/".round($s['stn_amount'],0)) : ""); ?></div>

							<div class="nwt">N.Wt-<?php echo $s['net_wt']; ?> </div>

							<div class="huid"><?php echo $huid  != "" ? "HUID: ".$huid : "" ?></div>

							<?php /*<div><?php echo $s['stn_wt'] > 0 ? "STN: ".$s['stn_wt']."G" : "" ?></div>

							<div><?php echo $s['stn_amount'] > 0 ? "S.AMT: Rs.".round($s['stn_amount'],0) : "" ?></div>*/ ?>
							
							
						</div>
						<div class="thirdCol">

							<div class="rightCol">
								
								<div class="tagAndProduct">
									
									<div class="product_division"><?php echo $s['product_division'] ?></div>

									<div class="blank_row"></div>

									<div><?php echo $s['size'] != "" ? "Size-".$s['size'].'<br>' : "" ?></div>

									<div class="blank_row"></div>

									<div class="design_name"><?php echo $product_name; ?></div>

								</div>

								<div class="qrImg">
								
									<img src="<?php echo $s['src'];?>">
								
								</div>

								<div class="comp_shortcode_1">

									

								</div>

								<div class="comp_shortcode_2">

								ARIA

								</div>

							</div>

						</div>

					</div>
					
				</div>

			<?php } ?>

		</div>

		<script type="text/javascript"> 

		this.print(); 

		</script> 

	</body>
</html>