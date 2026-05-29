<html><head>
		<meta charset="utf-8">
		<title>Vendor Acknowledgement - <?php echo $order['pur_no'];?> </title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/reorder.css">
		<style type="text/css">
		body, html {
		margin-bottom:0
		}
		 span { display: inline-block; }
		 
		 table {
          border-collapse: collapse !important;
        }
        table, th{
		  border-collapse: collapse !important;
        }
		 table, th, td {
		  border-collapse: collapse !important;
          border: 2px solid;
        }
        
	</style>
</head><body>
<span class="PDFReceipt">
    
<div class="flex_div">
				<div style="width:100%;">
				<div class="" align="center">
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
					<h2>REORDER REPORT</h2>
				</div>
				<div class="" align="center" width="20%;">
					<h2>
						<?php echo strtoupper($this->session->userdata['username']).'-' .date("d-m-y h:i:sa"); ?>
					</h2>
				</div>
				</div>
			</div>
			
	<div  class="content-wrapper">
	<div class="box">
	<div class="box-body">
				<div  class="container-fluid">
					<div id="printable">
					<?php 
							foreach($product_details as $prod)
							{?>
                        <div class="table-responsive">
							
											<table id="reorder_item_list" class="table text-center order_details">
													<thead>
														<?php 
																$weight_range = '';
																$sizeDet = '';
																$grand_total_row = '';
																$total_reorder_value = 0;
																$groupedData = [];
																$tHead = '';
																if($prod['reorder_based_on']==1)
																{
																	foreach($prod['weight_details'] as $val)
																	{
																		$weight_range.='<td style="text-align: center;" colspan='.sizeof($val['size_details']).'>'.$val['weight_description'].'</td>';
																	}

																	foreach($prod['weight_details'] as $val)
																	{
																		foreach($val['size_details'] as $itm){
																			$sizeDet.='<td style="text-align:right;">'.($itm['value'].''.$itm['name']).'</td>';
																		}
																	}
																	
																	$tHead.='<tr style="font-weight:bold;text-transform:uppercase;"><td style="width:10%;">Branch</td><td style="width:10%;">Section</td><td style="width:10%;">Product</td><td style="width:10%;">Design</td><td style="width:10%;">Sub Design</td>'.$weight_range.'<td>TOTAL</tr>';
																	$tHead.= '<tr style="font-weight:bold;text-transform:uppercase;"><td></td><td></td><td></td><td></td><td></td>'.$sizeDet.'</tr>';
																	echo $tHead;
																}else
																{
																	foreach($prod['size_details'] as $itm){
																		$sizeDet.='<td style="text-align:right;">'.($itm['value'].''.$itm['name']).'</td>';
																	}
																	$tHead.='<tr style="font-weight:bold;text-transform:uppercase;"><td style="width:10%;">Branch</td><td style="width:10%;">Section</td><td style="width:10%;">Product</td><td style="width:10%;">Design</td><td style="width:10%;">Sub Design</td>'.$sizeDet.'<td>TOTAL</tr>';
																	echo $tHead;
																}
														?>
													</thead>
													<tbody>
															<?php 
															$tbody = '';
															$tfoot = '';
															foreach($prod['stock_details'] as $val)
															{
																$tag_details = '';
																$row_total_pcs = 0;
																if($prod['reorder_based_on']==1)
																{
																	foreach($val['weight_details'] as $item)
																	{
																		foreach($item['tag_size_details'] as $size)
																		{   
																				$row_total_pcs+=($size['value']!='' && $size['value']!=null ? $size['value'] :0);
																				$tag_details.='<td style="text-align:right;">'.$size['value'].'</td>';
																		}
																	}	
																}else{
																		foreach($val['size_details'] as $size)
																		{   
																				$row_total_pcs+=($size['value']!='' && $size['value']!=null ? $size['value'] :0);
																				$tag_details.='<td style="text-align:right;">'.$size['value'].'</td>';
																		}
																}
																
																	$tbody.='<tr><td>'.$val['branch_name'].'</td><td>'.$val['section_name'].'</td><td>'.$val['product_name'].'</td><td>'.$val['design_name'].'</td><td>'.$val['sub_design_name'].'</td>'.$tag_details.'<td style="text-align:right;">'.$row_total_pcs.'</td></tr>';
															}


															echo $tbody;

															?>
															
													</tbody>

													<tfoot>
													<?php
														$groupedData = [];

														if ($prod['reorder_based_on'] == 1) {
															foreach ($prod['stock_details'] as $stk) {
																foreach ($stk['weight_details'] as $weight) {
																	foreach ($weight['tag_size_details'] as $tagSize) {
																		$idWeight = $tagSize['id_weight'];
																		$idSize = empty($tagSize['id_size']) ? 0 : $tagSize['id_size'];
																		$value = $tagSize['value'];

																		$key = $idWeight . '_' . $idSize;

																		if (isset($groupedData[$key])) {
																			$groupedData[$key]['value'] += $value;
																		} else {
																			$groupedData[$key] = [
																				'branch_name' => $stk['branch_name'],
																				'design_name' => $stk['design_name'],
																				'id_branch' => $stk['id_branch'],
																				'id_design' => $stk['id_design'],
																				'id_product' => $stk['id_product'],
																				'id_weight' => $idWeight,
																				'id_size' => $idSize,
																				'value' => $value,
																			];
																		}
																	}
																}
															}
														} else {
															foreach ($prod['stock_details'] as $stk) {
																foreach ($stk['size_details'] as $tagSize) {
																	$idWeight = $tagSize['id_weight'];
																	$idSize = empty($tagSize['id_size']) ? 0 : $tagSize['id_size'];
																	$value = $tagSize['value'];

																	$key = $idWeight . '_' . $idSize;

																	if (isset($groupedData[$key])) {
																		$groupedData[$key]['value'] += $value;
																	} else {
																		$groupedData[$key] = [
																			'branch_name' => $stk['branch_name'],
																			'design_name' => $stk['design_name'],
																			'id_branch' => $stk['id_branch'],
																			'id_design' => $stk['id_design'],
																			'id_product' => $stk['id_product'],
																			'id_weight' => $idWeight,
																			'id_size' => $idSize,
																			'value' => $value,
																		];
																	}
																}
												 			}
														}

														$total_reorder_value = 0;
														$grand_total_row = '';

														foreach ($groupedData as $val) {
															$total_reorder_value += $val['value'];
															$grand_total_row .= '<td style="text-align:right;">' . $val['value'] . '</td>';
														}

														echo '<tr style="font-weight:bold;">
																<td>TOTAL</td>
																<td></td>
																<td></td>
																<td></td>
																<td></td>' . $grand_total_row . '
																<td style="text-align:right;" class="total_reorder_value">' . $total_reorder_value . '</td>
															</tr>';
														?>
														<!-- <tr style="font-weight:bold;">
															<td>TOTAL</td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<?php 
															echo $grand_total_row;?>
															<td style="text-align:right;" class="total_reorder_value"><?php echo $total_reorder_value;?></td>
														</tr> -->
													</tfoot>
												</table>
									<?php ?>
							
                        </div><br><br><br>
					<?php }
					?>
				</div>    
			</div>				
				
 </div>
 </div><!-- /.box-body --> 
</div>
 </span>          
</body></html>

