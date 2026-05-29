<html><head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Estimation</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/estimation.css">
	<style type="text/css">
		body, html {
		margin-bottom:0
		}
		 @page { 
		 	size: 78mm;
		 	margin-bottom:40px !important;
		 } 
		 
		 /* output size */
		 span { display: inline-block; }
	</style>
</head>
<body class="plugin">
	<span class="PDFReceipt">
		<div class="printable">
			<div class="item_details">
    			<div class="breif_copy">
    				<label><b>BRIEF COPY</b></label>
    			</div>
    			<div style="margin-top:4px;font-size:10px;width:100%;">
    				<label><?php echo $estimation['created_time'].' , Estimation - '. $estimation['esti_no'];?></label>
    				<br><label><?php echo $estimation['customer_name'].($estimation['village_name']!='' ? ' / '.$estimation['village_name']:'').' / '.$estimation['mobile']; ?></label>
    				<?php 
    				if(sizeof($est_other_item['item_details'])>0)
    				{
    
    					foreach($est_other_item['item_details'] as $items){?>
    					<br><span style="font-size:10px;"><?php echo $items['product_name'].' / '.($items['design_name']!='' ? $items['design_name'].' / ' : '').$items['net_wt'].' / '.($items['size']!='' ? $items['size'].' - ':'').($items['tag_id']!='' ? ' / '.$items['tag_code'] :'');?></span>
    				<?php }?>
    				<p></p>
    				<?php }?>
    					<?php foreach($est_other_item['old_matel_details'] as $items){?>
    					<br><label><?php echo $items['metal'].' / '.$items['gross_wt'].' / '.$items['amount'];?></label>
    				<?php }?>
    				<br><label>EMP-ID : <?php echo $this->session->userdata('uid').' / '.date('h:i A', strtotime(date('d-m-Y H:i:s')));?></label>
    			</div><p></p>
			</div>
		</div>
	 </span>          
</body></html>