<link href="<?php echo base_url() ?>assets/css/pages/dashboard.css" rel="stylesheet">
<?php 
$header_content = $this->login_model->company_details();
?>
<div class="main-container">
	<div class="main-container">
		<!-- main -->		  
		<div class="main"  id="schemPayList">
				<!-- main-inner --> 
			<div class="main-inner">
				<!-- container --> 
				<div class="container">
					<div class="row">
						<div class="span12">
							<div align="center"><legend><span class="head">CLOSED PURCHASE PLAN ACCOUNTS</span></legend></div>
							<?php
							if($this->session->flashdata('successMsg')) { ?>
							<div class="alert alert-success" align="center">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong><?php echo $this->session->flashdata('successMsg'); ?> </strong>
							</div>     
							<?php } else if($this->session->flashdata('errMsg')) { ?>							 
							<div class="alert alert-danger" align="center">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong><?php echo $this->session->flashdata('errMsg'); ?></strong>
							</div>
							<?php } 
								if(isset($closed[0]['id_scheme_account']))
								{ 
								if($is_branchwise_cus_reg == 0 && $branch_settings == 1){		
								echo "<div class='row'><div class='col-md-12'>";				
								$idx = 0;
								foreach ($branches as $branch){
								echo "<button type='button' class='".($idx == 0 ? 'theme-btn-bg':'')." brn_btn btn btn-sm' id='brn_btn".$branch['id_branch']."' value='".$branch['id_branch']."'>".$branch['name']."</button>";
								$idx++;
							}
								echo "</div></div><p class='help-block'></p>";
							}
							?>
							<div class="closedAccounts">
								<?php 
									$i = 0;
									foreach($closed as $key => $value)
									{ 
									if($i == 0){
								?>
								<div class="table-responsive">
									<table  id="closed_acc" class="table table-bordered table-striped table-responsive display">
										<thead>
											<tr>					
												<th>ID</th>
												<!--<th>Group Code</th>-->
												<th>A/c No.</th>
												<th>A/c Name</th>
												<th>Purchase plan Code</th>							
												<th>Start Date</th>							
												<th>Type</th>
												<?php if($header_content['is_multi_commodity']== '1'){ ?> 
												<th>Metal Type</th>
												<?php } ?>
												<th>Amount</th>
												<th>Closing Balance</th>
												<th>Closed Date</th>
											</tr>
										</thead>
										<tbody>
											<?php $i++; } ?>
											<tr class="pay_card closed_ac_<?php echo $value['id_branch']?>" style="display:<?php echo $is_branchwise_cus_reg == 0 && $branch_settings == 1 ? ($branches[0]['id_branch'] == $value['id_branch'] ?'revert':'none'):'revert'?> ">
											<td><?php echo $value['id_scheme_account']; ?></td>
											<td><?php  echo (($value['scheme_acc_number']==' Not Allocated') ?$this->config->item('default_acno_label') : $value['code'].'-'.$value['scheme_acc_number'])?></td>
											<td><?php echo $value['account_name'];?></td>
											<td><?php echo $value['code'];?></td>
											<td><?php echo $value['start_date'];?></td>
											<td><?php echo $value['scheme_type'];?></td>
											<?php if ($value['is_multi_commodity']=='1'){ ?>  <!-- metal type showed based on  is_multi_commodity sett HH-->
											<td><?php echo ($value['id_metal']==1 ? '<span class="badge bg-yellow">'. $value['metal'].'</span>':'<span class="badge bg-gray">'. $value['metal'].'</span>'); ?></td>
											<?php } ?>
											<td><?php echo $value['amount']; ?></td>
											<td><?php echo $value['closing_balance']; ?></td>
											<td><?php echo $value['closing_date']; ?></td>
											<?php } ?> 
										</tbody> 
									</table>
								</div>			 
							</div>
							<?php  } else { ?> 	
							<div class="alert alert-danger" align="center">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<strong>Warning!</strong> You have not yet closed any Purchase plan accounts. 
							</div>
						<?php }  ?> 			
						</div>	
					</div>
					<!-- /container --> 
				</div>
				<!-- /main-inner --> 
			</div>
		<!-- /main -->	
		</div>	
	</div>
</div>