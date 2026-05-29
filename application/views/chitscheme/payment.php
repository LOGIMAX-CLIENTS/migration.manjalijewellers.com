<link href="<?php echo base_url() ?>assets/css/pages/payment.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/> 
<div class="main-container">
	<div class="main-container">
	<!-- main -->		  
		<div class="main"  id="schemPayList">
		  <!-- main-inner --> 
			<div class="main-inner">
				 <!-- container --> 
				<div class="container">
					<div class="row"> 
						<?php 
						$attributes =	array('id' => 'payForm', 'name' => 'payForm');
						echo form_open_multipart('paymt/paySubmit',$attributes);  
						?>
						<div class="col-md-12">
							<div align="center"><legend class="head">PAYMENT</legend></div>
							<div id='error-msg'></div>
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
							if($settings['auto_debit'] == 1){ ?>
							<div class="row">
								<div class="card">
									<div class="card_content">
									  <h4 class="card_title">
										<?php 
											echo "Cashfree Subscription automates your monthly payments after the initial checkout is completed. Subscribe Now!!";
											echo '<a href="'.base_url().'index.php/chitscheme/scheme_report" class="btn btn-xs btn-success pull-right">Subscribe</a>';
										?>
									  </h4>
									</div>
								</div>
							</div>
							<p class="help-block"></p>
							<?php }
							
							//check scheme account id		
							if(isset($content['chits'][0])){
								if($settings['cost_center'] == 3){ 	
									echo "<div class='row'><div class='col-md-12'>";				
									$idx = 0;
									foreach ($branches as $branch){
										echo "<button type='button' class='".($idx == 0 ? 'theme-btn-bg':'')." brn_btn btn btn-sm' id='brn_btn".$branch['id_branch']."' value='".$branch['id_branch']."'>".$branch['name']."</button>";
										$idx++;
									 }
									 echo "</div></div><p class='help-block'></p>";
								} 
							?>	
							<div class="schemeTable">
								<div class="table-responsive">
									<table id="scheme-amount" class="table table-bordered table-striped table-responsive display">
										<thead>
											<tr>
												<th width="5%">Select to Pay</th>	
												<?php if($this->session->userdata('branch_settings')==1){?>
												<th width="10%">Branch</th>
												<?php } ?>
												<th width="10%">A/c No.</th>
												<th width="10%">A/c Name</th>										
												<th width="10%">plan Name & Code</th>									
												<th width="22%">No of Due</th>
												<th width="18%">Payable</th>
												<?php if($settings['auto_debit']==1){?>
												<th width="5%">Auto-Debit</th>
												<?php } ?>
												<th width="5%">Status</th>	
												<th  width="23%" class="pay_amt">Payment amount </th>
											</tr>
										</thead>
										<tbody>
											<?php
											$i=0;
											foreach($content['chits'] as $chit){ 
											//echo "<pre>";print_r($chit);echo "</pre>";
											 // if((($chit['max_amount']=='' || $chit['max_amount']==0) && ($chit['min_amount']==''|| $chit['max_amount']==0)&& $chit['pay_duration']=='' && $chit['scheme_type']!=3 && $chit['current_total_amount'] >= $chit['max_amount'] ) || (($chit['pay_duration']==0 || $chit['pay_duration']==1 && $chit['scheme_type']==3) && (($chit['current_chances_pay']!= $chit['max_chance'])&&($chit['max_amount']!='' && $chit['min_amount']!=''&& $chit['current_total_amount']!= $chit['max_amount'])))){
											 if($chit['allow_pay'] == 'Y'|| $chit['allow_pay'] == 'N'){
											  $status =	($chit['previous_paid']==1?array('state'=>'Success','class'=>'success','msg'=>'Your payment for current month credited successfully'):($chit['current_paid_installments']==0?array('state'=>'Not Paid','class'=>'danger','msg'=>'You have not made any payment this month'):($chit['last_transaction']['payment_status']==1?array('state'=>'Success','class'=>'success','msg'=>'Your payment credited successfully'):($chit['last_transaction']['payment_status']==2?array('state'=>'Awaiting','class'=>'info','msg'=>'Your status will be updated after amount received from bank'):($chit['last_transaction']['payment_status']==3 ? array('state'=>'Failed','class'=>'danger','msg'=>'Your payment failed'):array('state'=>'Pending','class'=>'warning','msg'=>'Your payment failed'))))));
											   $i++;
											?>
											<tr class="pay_row brn_row_<?php echo $chit['id_branch']?>" style="display:<?php echo $settings['cost_center'] == 3 ? ( ($branches[0]['id_branch'] == $chit['id_branch'] ?'revert':'none')) :'revert'?> ">
											<td>
												<?php if($chit['allow_pay'] == 'Y'){?>
													<input type="checkbox" name="sch_all"  class="select_chit" id="select_id_<?php echo $chit['id_scheme_account'];?>" value="<?php echo $chit['id_scheme_account']; ?>" > 
												<?php }
												echo $chit['id_scheme_account']; ?>										
											</td>
											 <?php 	if($this->session->userdata('branch_settings')==1){?>
												<td><?php echo $chit['branch_name'];?></td>
											  <?php } ?>
											<td><?php 
												if($this->config->item('showGCodeInAcNo')==0)
												{
													echo ($chit['chit_number']== '' ? $this->config->item('default_acno_label'): $chit['chit_number']);
												}
												else
												{
												   echo ($chit['has_lucky_draw']==1 ? $chit['scheme_group_code'] : $chit['code']).' '.($chit['chit_number']!=''?$chit['chit_number']:"Not Allocated");
												}
												?>
											</td>					 	
											<td><?php echo $chit['account_name'];?></td>
											<td><?php echo $chit['scheme_name'].' - '.$chit['code'];?></td>
											<td>
												<?php if( $chit['scheme_type']==1 && $chit['is_flexible_wgt'] == 1){ ?>
												<input  class="sel_due"  name="pay[<?php echo $i;?>][sel_dues]"  Style="width:30%"  value="1" readonly="true"/>
												<?php } else if($chit['scheme_type']==3) { ?>
												<button type="button" value="<?php echo $i; ?>" class="dec_due">-</button>
												<input  class="sel_due"  name="pay[<?php echo $i;?>][sel_dues]"  Style="width:30%"  value="1" />
												<button type="button"  value="<?php echo $i; ?>" class="incre_due">+</button>
												<?php }else { ?>
												<button type="button" value="<?php echo $i; ?>" class="dec_due">-</button>
												<input  class="sel_due" name="pay[<?php echo $i;?>][sel_dues]"  Style="width:30%"  value="1" readonly="true"/>
												<!--<input type="hidden" class="inval" value=""/>-->
												<button type="button"  value="<?php echo $i; ?>" class="incre_due">+</button>
												<?php } ?>
											</td>
											<!--	<td><?php echo ($chit['scheme_type']==0 || $chit['scheme_type']==2 ? $chit['currency_symbol']." ".number_format($chit['payable'],'2','.',''):$chit['payable'].' g (Max)');?></td>-->
											<!-- <td class="pay_amount"><?php echo ($chit['scheme_type']==0 ?$chit['currency_symbol'].' '.number_format($chit['payable']):($chit['scheme_type']==2? $chit['currency_symbol'].' '.number_format($chit['payable']): (($chit['scheme_type'] ==1 && $chit['is_flexible_wgt'] == 0) ? $chit['max_weight'].' g' :$chit['payable']-$chit['total_paid_weight'].' g')));?>-->
											<?php if(($chit['firstPayamt_as_payamt']==1  && $chit['firstPayment_amt'] > 0) || ($chit['get_amt_in_schjoin']==1 && $chit['firstPayamt_as_payamt']==1)){?>	
											<td class="pay_amount"><?php echo ($chit['scheme_type']==3 ?$chit['currency_symbol'].'&nbsp;&nbsp;'."<input  class='pay_amtwgt'    style='width:40%;'  value=".$chit['min_amount']." disabled='true'/>".'&nbsp;&nbsp;'."<b></small>":($chit['scheme_type']==0 ?$chit['currency_symbol'].' '.number_format($chit['payable']):($chit['scheme_type']==2? $chit['currency_symbol'].' '.number_format($chit['payable']): (($chit['scheme_type'] ==1 && $chit['is_flexible_wgt'] == 0) ? $chit['max_weight'].' g' :$chit['payable']-$chit['total_paid_weight'].' g'))));?>
											<?php }else{?>
											<td class="pay_amount">
												<?php echo ($chit['scheme_type']==3 ?$chit['currency_symbol'].'&nbsp;&nbsp;'."<input  class='pay_amtwgt'    style='width:40%;'  value=".$chit['min_amount']." disabled='true'/>".'&nbsp;&nbsp;'."<button id='proceed' type='button'  value='$i' class='cal_amt' disabled='true' style='width:37%;' >Proceed</button> </br> <b><small>".(!empty($chit['maxamount'])?"Maximum  ".$chit['maxamount']:'')."<b></small>":($chit['scheme_type']==0 ?$chit['currency_symbol'].' '.number_format($chit['payable']):($chit['scheme_type']==2? $chit['currency_symbol'].' '.number_format($chit['payable']): (($chit['scheme_type'] ==1 && $chit['is_flexible_wgt'] == 0) ? $chit['max_weight'].' g' :$chit['payable']-$chit['total_paid_weight'].' g'))));?>
												<?php }?>
												<input type="hidden"  class="gst_val"/>
												<input type="hidden"  class="gst_type" name="pay[<?php echo $i;?>][gst_type]" value="<?php echo $chit['gst_type'];?>"/>
												<input type="hidden"  class="gst" name="pay[<?php echo $i;?>][gst]" value="<?php echo $chit['gst'];?>"/>
												<input type="hidden" name="pay[<?php echo $i;?>][actamt]" class="actamt"/>
												<input type="hidden" name="pay[<?php echo $i;?>][amount]" class="amount"/>
												<input type="hidden" class="discount" name="pay[<?php echo $i;?>][discount]" value="0"/>
												<input type="hidden" name="pay[<?php echo $i;?>][payment_amt]" value="0" class="payment_amt"/>
												<input type="hidden" name="pay[<?php echo $i;?>][productinfo]"  value="<?php echo $chit['code'];?>"/>
												<input type="hidden" class="id_scheme_account" name="pay[<?php echo $i;?>][udf1]"  value="<?php echo $chit['id_scheme_account'];?>"/>
												<input type="hidden" id="metal_wgt_roundoff"  value="<?php echo $chit['metal_wgt_roundoff'];?>" />
												<input type="hidden" id="metal_wgt_decimal"  value="<?php echo $chit['metal_wgt_decimal'];?>" />
												<!--customer Data-->
												<input type="hidden" name="cus[id_customer]"  value="<?php echo $chit['id_customer'];?>" />
												<input type="hidden" name="cus[firstname]"  value="<?php echo $chit['firstname'];?>" />
												<input type="hidden" name="cus[lastname]"  value="<?php echo $chit['lastname'];?>" />
												<input type="hidden" name="cus[email]"  value="<?php echo $chit['email'];?>" />
												<input type="hidden" name="cus[phone]"  value="<?php echo $chit['mobile'];?>" />
												<input type="hidden" name="cus[address1]"  value="<?php echo $chit['address1'];?>" />
												<input type="hidden" name="cus[address2]"  value="<?php echo $chit['address2'];?>" />
												<input type="hidden" name="cus[city]"  value="<?php echo $chit['city'];?>" />
												<input type="hidden" name="cus[state]"  value="<?php echo $chit['state'];?>" />
												<input type="hidden" name="cus[country]"  value="<?php echo $chit['country'];?>" />
												<input type="hidden" name="cus[zipcode]"  value="<?php echo $chit['zipcode'];?>" />
												<!--Payment Data-->
												
												<input type="hidden" id="one_time_premium" name="pay[<?php echo $i;?>][one_time_premium]"  value="<?php echo $chit['one_time_premium'];?>" />
												<input type="hidden" id="rate_fix_by" name="pay[<?php echo $i;?>][rate_fix_by]"  value="<?php echo $chit['rate_fix_by'];?>" />
												
												<input type="hidden" id="scheme_type" name="pay[<?php echo $i;?>][scheme_type]"  value="<?php echo $chit['scheme_type'];?>" />
												<input type="hidden" id="flexible_sch_type" name="pay[<?php echo $i;?>][flexible_sch_type]"  value="<?php echo $chit['flexible_sch_type'];?>" />
												<input type="hidden" id="is_flexible_wgt" name="pay[<?php echo $i;?>][is_flexible_wgt]"  value="<?php echo $chit['is_flexible_wgt'];?>" />
												<input type="hidden" id="max_weight" name="pay[<?php echo $i;?>][max_weight]"  value="<?php echo $chit['max_weight'];?>" />
												<input type="hidden" name="pay[<?php echo $i;?>][udf2]"  class="sel_weight" />
												<input type="hidden" name="pay[<?php echo $i;?>][rate_field]"  class="rate_field" id="rate_field" value="<?php echo $chit['rate_field'];?>" />
												<input type="hidden" class="id_metal" id="id_metal" value="<?php echo $chit['id_metal'];?>" />
												<input type="hidden"  id="metal_rates" name="pay[<?php echo $i;?>][udf3]"  class="metal_rate" value="<?php echo $chit['metal_rate'];?>" />
												<input type="hidden" name="pay[<?php echo $i;?>][udf4]"  class="payable"/>
												<input type="hidden" name="pay[<?php echo $i;?>][udf5]"  class="no_of_due"/>
												<input type="hidden" name="pay[<?php echo $i;?>][udf6]"  class="charge" value="0.00"/>
												<input type="hidden" name="pay[<?php echo $i;?>][allowed_dues]"  class="allowed_dues" value="" />
												<input type="hidden" name="pay[<?php echo $i;?>][ischecked]" value="0" class="ischecked"/>
												<input type="hidden" name="pay[<?php echo $i;?>][due_type]"  value="<?php echo $chit['due_type'];?>"/>
												<input type="hidden" name="pay[<?php echo $i;?>][total_paid_amount]"  class="total_paid_amount"  value="<?php echo $chit['total_paid_amount'];?>"/>
												<input type="hidden" name="pay[<?php echo $i;?>][last_paid_date]" class="last_paid_date"  value="<?php echo $chit['last_paid_date'];?>"/>
												<!--<input type="hidden" name="pay[<?php echo $i;?>][redeemed_amount]" class="redeemed_amt"  value=""/>
												<input type="hidden" name="pay[<?php echo $i;?>][ispoint_credited]" class="is_pointchk_credit"  value=""/>
												<input type="hidden" class="wallet_balance"/>-->
												<input name="wallet[wallet]" type="hidden" class="wallet">
												<input type="hidden" name="wallet[use_wallet]" class="use_wallet"  value=""/> 
												<input type="hidden" name="wallet[wallet_balance]" class="wallet_balance"/>
												<input type="hidden" name="wallet[redeem_percent]" class="redeem_percent"/> 
												<input type="hidden" value="<?php echo $chit['allow_wallet'];?>" class="allow_wallet"  value=""/>  
												<input type="hidden" name="pay[<?php echo $i;?>][id_branch]" value="<?php echo $chit['id_branch']?>"/>
											</td>
											<?php if($settings['auto_debit']==1){?>
											<td><?php echo $chit['auto_debit_status_msg']; ?></td>
											<?php } ?>
											<td><?php echo '<span class="label label-'.$status['class'].'">'.$status['state'].' <i rel="tooltip"  title="'.$status['msg'].'" class="icon-question-sign help-icon"></i></span>'
											 ?>	
											</td>
											<td class="show_pay"></td>
											</tr>
											<?php }  }    ?> 
										</tbody> 
									</table>
								</div>			 
							</div>
							<?php if($chit['id_branch']==1){?>	
								<span style="color: red;font-weight: bold;"><?php echo ($chit['disable_pay_reason'])?>   </span>
							<?php } ?>
							<div style="padding:5px;border: 1px solid #eee !important;background: #fff !important;">
								<span style="color: red;font-weight: bold;">NOTE : It will take 5 working days to settle the amount in your purchase plan account</span>
								<br/>
								<label>Proceed with : </label>
								<?php 
								if($settings['cost_center'] == 3){ 
									foreach(  $gateway as $row ) 
									{
										echo "<span class='brn_gateways' id='brn_".$row['id_branch']."' style=display:".($branches[0]['id_branch'] == $row['id_branch'] ?'block':'none').">";
										if($row['is_primary_gateway'] == 1){
											echo "
											<div style=' height: 75px;border: 1px solid #eee;background: #faf7f7;margin: 5px;padding: 10px;' >
												<input type='radio' checked='true' id='id_pg' name='payment[id_pg]' value='".$row['id_pg']."' >&nbsp;&nbsp;<img style='min-height: 30px;max-height: 30px;' src='".$row['pg_icon']."'/> <span style='margin-left:16px;'>".$row['pg_name']."</span>&nbsp;&nbsp;&nbsp;
												<br/>
												<span style = 'margin-left:16px;color:#6a6666'>".$row['description']."</span>
											</div>
											";
										}else{
											echo "
											<div style=' height: 75px;border: 1px solid #eee;background: #faf7f7;margin: 5px;padding: 10px;'>
												<input type='radio' id='id_pg' name='payment[id_pg]' value='".$row['id_pg']."' > &nbsp;&nbsp;<img style='min-height: 30px;max-height: 30px;' src='".$row['pg_icon']."'/> <span style='margin-left:16px;'>".$row['pg_name']."</span>&nbsp;&nbsp;&nbsp;										 
												<br/>
												<span style = 'margin-left:16px;color:#6a6666'>".$row['description']."</span>
											</div>
											";
										}
										echo "</span>";							
									}
								} else{ 
									foreach(  $gateway as $row ) 
									{
										if($row['is_primary_gateway'] == 1){
											echo "
											<div style=' height: 75px;border: 1px solid #eee;background: #faf7f7;margin: 5px;padding: 10px;' >
												<input type='radio' checked='true' id='id_pg' name='payment[id_pg]' value='".$row['id_pg']."' >&nbsp;&nbsp;<img style='min-height: 30px;max-height: 30px;' src='".$row['pg_icon']."'/> <span style='margin-left:16px;'>".$row['pg_name']."</span>&nbsp;&nbsp;&nbsp;
												<br/>
												<span style = 'margin-left:16px;color:#6a6666'>".$row['description']."</span>
											</div>
											";
										}else{
											echo "
											<div style=' height: 75px;border: 1px solid #eee;background: #faf7f7;margin: 5px;padding: 10px;'>
												<input type='radio' id='id_pg' name='payment[id_pg]' value='".$row['id_pg']."' > &nbsp;&nbsp;<img style='min-height: 30px;max-height: 30px;' src='".$row['pg_icon']."'/> <span style='margin-left:16px;'>".$row['pg_name']."</span>&nbsp;&nbsp;&nbsp;
												<br/>
												<span style = 'margin-left:16px;color:#6a6666'>".$row['description']."</span>
											</div>
											";
										}
									}
								?>
							<?php } ?>
								<?php if($chit['allow_wallet']==1 && $chit['useWalletForChit']==1){?>
									<div class="eligible_walletamt" style="display:none;margin: 5px; padding: 10px;">		
										<input type="checkbox"  class="ischk_wallet_pay" value="0" /> <span style="font-weight: bold;">&nbsp;&nbsp; USE WALLET&nbsp;&nbsp;&nbsp;Rs :&nbsp; </span>
										<input style="width:20%;" type="hidden" class="wallet_payamt" />
										<input name="wallet[redeem_request]" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" step="1" type="number" class="redeem_request">
									</div>
								<?php }?>							
								<div class="submit-block" > 
									<label>
										Payment Amount : Rs. <span id="tot_sel_amt"  style="font-size: 18px"> 0.00</span> 
									</label> 
								</div>
								<div class="submit-block"> 
									<input type="hidden" id="tot_amt" name="total_amount" value="0" />
									<button type="submit" id="proceed_pay" class="btn btn-primary confirm_pay">Confirm Pay</button>	 
								</div>
							</div>
							<br/>
						</div>
					<?php  } else { ?> 	
						<div class="alert alert-danger" align="center">
						  <button type="button" class="close" data-dismiss="alert">&times;</button>
						  <strong>Warning!</strong> You have not joined in any Purchase plan. Join first and then make payment.
						</div>
					<?php }  ?>
					</div>	
					<div class="overlayy" style="display:none;font-size: 20px; position: absolute;top: 0%; z-index: 60;width: 100%;height: 100%;left: 0%;background: rgba(255,255,255,0.7);">
						<i class="fa fa-refresh fa-spin" style="margin-left: 50%;margin-top: 40%;"></i>
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
<!-- modal-->
<div class="modal fade" id="payModal" tabindex="-1" role="dialog"  aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="gridSystemModalLabel">Payment</h4>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<!--<button type="button" class="btn btn-default" data-dismiss="modal">cancel</button>-->
				<button type="button" class="btn btn-default" data-dismiss="modal">Proceed</button>
			</div>
		<!--  </form>-->
		</div><!-- /.modal-content-->
	</div><!-- /.modal-dialog -->
</div>
<br />
<br />
<br />