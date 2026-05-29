<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cus_receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
	</head>
	
	<body class="margin">
	<div class="PDF_CusReceipt">
			<?php foreach($img as $s){?>
			<div style="page-break-after:always;">
			<table class="meta" style="width: 70%" align="left">
				<tr>
					<td colspan="3"><span><?php echo $s['product_name'];?></span></td>
				</tr>
				<tr>
					<td><span><?php echo $s['product_id'].'-'.$s['tag_id'];?></span></td>
					<td><span ><?php echo $s['short_code'];?></span></td>
				</tr>
				<tr>
					<td><span>Wt : <?php echo number_format($s['net_wt'],'3','.','');?></span></td>
				</tr>
			</table>
			<table class="meta" style="width: 50%" align="right">
				<tr>
					<td><img src="<?php echo $s['src'];?>"></td>
				</tr>
			</table>
			
			</div>
			<?php }?>
		</div>
		<script type="text/javascript"> 
		this.print(); 
		</script> 
	</body>
	
</html>