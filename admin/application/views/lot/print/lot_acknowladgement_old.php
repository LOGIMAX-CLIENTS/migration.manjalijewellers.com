<html><head>
		<meta charset="utf-8">
		<title>Payment Report</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/lot_ack.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		<style >
		 .head
		 {
			 color: black;
			 font-size: 50px;
		 }
         </style>
</head><body>
<span class="PDFReceipt">
<div><img alt="" src="<?php echo base_url();?>assets/img/receipt_logo.png"></div>
			<div class="address" align="center">
				<h2>LOT RECEIPT - <?php echo strtoupper($lot_inwards_detail[0]['rcvd_branch_name']);?></h2>
			</div><br>
<div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
				<div id="printable">
						<div  class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
								<table id="pp" class="table text-center">
									<!--	<thead> -->
											<tr>
												<th>S.NO</th>
												<th>LOT NO</th>
												<th>ITEMS</th>
												<th>PCS</th>		
												<th>GWT</th>
												<th>CPCS</th>
												<th>CGWT</th>
											</tr>
										<!--</thead>
										<tbody>-->
											<?php 
											$i=1;
											$no_of_piece=0;
											$gross_wt=0;
											foreach($lot_inwards_detail as $lot)
											{
											$no_of_piece+=$lot['no_of_piece'];
											$gross_wt+=$lot['gross_wt'];
											?>
												<tr>
													<?php if($type==1){?>
														<td><?php echo $i;?></td>
														<td><?php echo $lot['lot_no'];?></td>
														<td><?php echo $lot['pro_name'];?></td>
														<td><?php echo $lot['no_of_piece'];?></td>
														<td><?php echo $lot['gross_wt'];?></td>
														<td>0</td>
														<td>0.000</td>
													<?php }else{
													$tag_piece=0;
													$tag_wt=0;
													$no_of_tag_piece=0;
													$no_of_tag_wt=0;
													?>
														<td><?php echo $i;?></td>
														<td><?php echo $lot['lot_no'];?></td>
														<td><?php echo $lot['pro_name'];?></td>
														<td><?php echo $lot['no_of_piece'];?></td>
														<td><?php echo $lot['gross_wt'];?></td>
														<?php foreach($tag_details as $tag)
														{
														$no_of_tag_piece+=$tag['piece'];
														$no_of_tag_wt+=$tag['gross_wt'];
															if($tag['lot_product']==$lot['lot_product'])
															{
																$tag_piece=$tag['piece'];
																$tag_wt=$tag['gross_wt'];
															}
														}?>
														<td><?php echo $tag_piece;?></td>
														<td><?php echo $tag_wt;?></td>
													<?php }?>
													</tr>
											<?php $i++;
											}?>
									<!--</tbody> -->
										<tfoot>
											<tr>
											<td><b>TOTAL</b></td>
											<td></td>
											<td></td>
											<td><b><?php echo $no_of_piece;?></b></td>
											<td><b><?php echo number_format((float)$gross_wt,3,'.','')?></b></td>
											<td><?php echo($type==1 ? 0: $no_of_tag_piece);?></td>
											<td><?php echo($type==1 ? 0.000: number_format((float)$no_of_tag_wt,'3','.',''));?></td>
											</tr>
										</tfoot>
									</table><br>	
									<?php if($type==2){?>
									<div  class="container-fluid">
									<div id="printable">
											<div  class="row">
												<label><b>Branch Wise Summary</b></label></br>
												<div class="col-xs-12">
													<div class="table-responsive">
														<table id="pp" class="table text-center">
														<tr>
															<th>S.NO</th>
															<th>ITEMS</th>
															<th>PCS</th>		
															<th>GWT</th>		
															<th>CODE</th>
															<th>RATE</th>
														</tr>
														<?php 
														$i=1;
														$tot_piece=0;
														$tot_wt=0;
														$sub_tot_piece=0;
														$sub_tot_wt=0;
														$tr='';
														$last_branch=$tag_details[0]['current_branch'];
														foreach($tag_details as $key =>$data)
														{
														$tot_piece+=$data['piece'];
														$tot_wt+=$data['gross_wt'];
														$tr.='<tr>
														<td>'.$i.'</td>
														<td>'.$data['design_name'].'</td>
														<td>'.$data['piece'].'</td>
														<td>'.$data['gross_wt'].'</td>
														<td>'.$data['name'].'</td>
														<td>0</td>
														</tr>';
														if($last_branch==$data['current_branch'])
														{
															$sub_tot_piece+=$data['piece'];
															$sub_tot_wt+=$data['gross_wt'];
														}
														if($last_branch!=$tag_details[$key+1]['current_branch'])
														{
															$tr.='<tr>
																	<td><b>SUB TOTAL</b></td>
																	<td></td>
																	<td><b>'.$sub_tot_piece.'</b></td>
																	<td><b>'.number_format((float)$sub_tot_wt,'3','.','').'</b></td>
																	<td></td>
																	<td></td>
																  </tr>';
															$sub_tot_piece=0;
															$sub_tot_wt=0;
														}
														$last_branch=$tag_details[$key+1]['current_branch'];
														$i++;
														}?>
														<?php echo $tr;?>
														</br>
														<tr>
															<td><b>Grand Total</b></td>
															<td></td>
															<td><b><?php echo $tot_piece;?></b></td>
															<td><b><?php  echo number_format((float)$tot_wt,'3','.','');?></b></td>
															<td></td>
															<td></td>
														</tr>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php }?>
									<div class="grand_total" align="center">
										<div class="row">
											<?php if($type==2){?>
												<label><b>Diff.Gwt :  <?php echo number_format((float)($no_of_tag_wt-$gross_wt),3,'.','')?></b></label>
											<?php }else{?>
												<label><b>Diff.Gwt : - <?php echo number_format((float)($gross_wt),3,'.','')?></b></label>
											<?php }?>
										</div><br>
										<div class="row" style="margin-left:-30px;">
											<?php if($type==2){?>
												<label><b>Diff.Pcs :  <?php echo ($no_of_tag_piece-$no_of_piece);?></b></label>
												<?php }else{?>
													<label><b>Diff.Pcs : - <?php echo $no_of_piece;?></b></label>
												<?php }?>
										</div>
									</div>
								</div>	
							 </div>	
						</div>
				</div><p></p>
						<div align="right">
							<div class="row">
								<label>Date : <?php echo date('d-m-Y');?></label>
							</div><br>
							<div class="row">
							<label>Time : <?php echo date('h:i A', strtotime(date('d-m-Y H:i:s')));?></label>
							</div>
						</div>
			</div>
 </div>
 </div><!-- /.box-body --> 
</div>
 </span>          
</body></html>