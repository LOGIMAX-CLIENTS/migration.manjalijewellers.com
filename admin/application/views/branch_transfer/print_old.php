<!doctype html>
<html><head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
	</head><body>
	<span class="PDFReceipt">
		<div><img alt="" src="<?php echo base_url();?>assets/img/receipt_logo.png?<?php time();?>"></div>
		<div class="address" align="right"> 
				 <?php  echo ($comp_details['address1'] != '') ? $comp_details['address1'].' ,' : ''; ?>				
				<?php echo ( $comp_details['address2'] != '')?$comp_details['address2'].' ,'  : ''; ?>
				<?php echo ( $comp_details['city'] != '')? $comp_details['city'].'  '.$comp_details['pincode'].' ,'  : ''; ?>
				<?php echo ( $comp_details['state'] != '')? $comp_details['state'].' ,'  : ''; ?>
				<?php echo ( $comp_details['country'] != '')? $comp_details['country'].' .'  : ''; ?>
				<p> <?php echo($comp_details['phone'] != '')?  'Phone : '. $comp_details['phone'] : '';?></p><p> <?php ($comp_details['mobile'] != '') ? 'Mobile : '.$comp_details['mobile'] :'';?></p><p></p>
			</div>
			
			<div class="heading">Branch Transfer</div>
			<table class="meta" style="width: 100%" align="center"> 
				<tr>
					<th>Code</th>
					<td><?php echo $btrans[0]['branch_trans_code']; ?></span></td>
				</tr>
				<tr>
					<th>Date</th>
					<td><?php echo $btrans[0]['created_time']; ?></span></td>
				</tr>
				<tr>
					<th>From</th>
					<td><?php echo $btrans[0]['from_branch']; ?></span></td>
				</tr>
				<tr>
					<th>To</th>
					<td><?php echo $btrans[0]['to_branch']; ?></span></td>
				</tr>
			</table>
			<p></p>
			<p></p> 
			<table class="inventory"> 
					<tr> 
						<th width="15%">Product</th>
						<th width="10%">Gross Wgt</th>
						<th width="10%">Net Wgt</th>
						<th width="10%">Piece</th>
						<th width="15%">Tag No</th>
						<th width="15%">Lot No</th>
					</tr>
					<?php
					 $tot_gross = 0;
					 $tot_net = 0;
					 $tot_piece = 0;
					 if($btrans[0]['transfer_item_type'] == 1) {
						foreach($btrans as $btran){?> 
						<tr> 
							<td><span><?php echo $btran['product'] ?></span></td>
							<td><span><?php echo $btran['t_gross'] ?></span></td> 
							<td><span><?php echo $btran['t_net'] ?></span></td> 
							<td><span><?php echo $btran['t_piece'] ?></span></td> 
							<td><span><?php echo $btran['tag_id'] ?></span></td> 
							<td><span><?php echo $btran['t_lot'] ?></span></td>
						</tr> 
					<?php 
						$tot_gross = $tot_gross + (is_null($btran['t_gross']) ? 0 : $btran['t_gross'] );
						$tot_net = $tot_net + (is_null($btran['t_net']) ? 0 : $btran['t_net'] );
						$tot_piece = $tot_piece + (is_null($btran['t_piece']) ? 0 : $btran['t_piece'] );
						} ?>
						<tr align="right"> 
							<th><span>Total </span></th> 
							<td><span><?php echo $tot_gross ?></span></td>
							<td><span><?php echo $tot_net ?></span></td>
							<td><span><?php echo $tot_piece ?></span></td>
						</tr>
					<?php }else { 
						foreach($btrans as $btran){?>    
						<tr> 
							<td><span><?php echo $btran['product'] ?></span></td>
							<td><span><?php echo $btran['grs_wt'] ?></span></td> 
							<td><span><?php echo $btran['net_wt'] ?></span></td> 
							<td><span><?php echo $btran['pieces'] ?></span></td> 
							<td><span> - </span></td> 
							<td><span><?php echo $btran['lot_no'] ?></span></td>
						</tr>
						<?php 
						$tot_gross = $tot_gross + (is_null($btran['grs_wt']) ? 0 : $btran['grs_wt'] );
						$tot_net = $tot_net + (is_null($btran['net_wt']) ? 0 : $btran['net_wt'] );
						$tot_piece = $tot_piece + (is_null($btran['pieces']) ? 0 : $btran['pieces'] );
						} ?>
						<tr align="right"> 
							<th><span>Total </span></th> 
							<td><span><?php echo $tot_gross ?></span></td>
							<td><span><?php echo $tot_net ?></span></td>
							<td><span><?php echo $tot_piece ?></span></td>
						</tr>
					<?php } ?>  
			</table>
			<p></p>  
		<aside>
			
			<div >
				<p class="txtAckowlege"></p>
			</div>
		</aside>
		</span>
	</body></html>