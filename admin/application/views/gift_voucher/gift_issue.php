  <!-- Content Wrapper. Contains page content --> 
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Gift Voucher
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Gift Voucher</a></li>
        <li class="active">Issue Vouchers</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Issue Vouchers</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body"> 
            <!-- Alert -->
			<?php 
				if($this->session->flashdata('chit_alert'))
				 {
					$message = $this->session->flashdata('chit_alert');
			?>
				   <div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
					<?php echo $message['message']; ?>
				  </div>
			<?php } ?>  
             <!-- form -->
             <form id="gift_issue">
			<p class="help-block"></p> 
			<div class="row">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-3">
						    <?php if($this->session->userdata('branch_settings')==1 && $this->session->userdata('id_branch')==0){?>
							<div class="form-group">
			                  <label for="">Select Branch <span class="error"> *</span></label>
			                 	<select id="branch_select" class="form-control" ></select>
			                 	<input type="hidden" name="gift[id_branch]" id="id_branch">
			                </div>
			                <?php }else{?>
			                    <label><?php echo $this->gift_voucher_model->get_currentBranchName($this->session->userdata('id_branch'));?> </label>
			                    <input type="hidden" name="gift[id_branch]" id="id_branch" value="<?php echo $this->session->userdata('id_branch') ?>">
			                <?php }?>
			                <span id="branchAlert" class="error"></span>
						</div>
						
						<div class="col-md-5">
							<div class="form-group">
			                  <label for="">Voucher Type <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <input type="radio" name="gift[gift_type]" id="free_card1" value="1" checked=""> Free  &nbsp;&nbsp;
			                      <input type="radio" name="gift[gift_type]" id="free_card2" value="2" > Paid  &nbsp;&nbsp;
			                      <input type="radio" name="gift[gift_type]" id="free_card3" value="3" > Promotional  &nbsp;&nbsp;
				              </div>
			                </div>
						</div>
						
						<div class="col-md-4">
							<div class="form-group">
			                  <label for="">Voucher For <span class="error"> *</span></label>
			                  <div class="form-group"> 
			                      <input type="radio" name="gift[gift_for]" id="gift_for1" value="1" >Employee &nbsp;&nbsp;
			                      <input type="radio" name="gift[gift_for]" id="gift_for2" value="2" checked=""> Customer  &nbsp;&nbsp;
			                      
				              </div>
			                </div>
						</div>
					</div>
				</div>
				<div class="col-md-8" >
					<div class="row">		
						<div class="col-md-4" id="cus_select" >
							<div class="form-group">
			                  <label for="">Customer <span class="error"> *</span></label>
			                  <input type="text" id="cus_name" class="form-control" autocomplete="off" placeholder="Enter Name/Mobile"> 
			                  <input type="hidden" name="gift[id_customer]" id="id_customer">  
			                  <span id="customerAlert"></span>
			                </div>
						</div>
						
						<div class="col-md-4" id="cus_transfer_to" >
							<div class="form-group">
			                  <label for="">Customer Transfer To</label>
			                  <input type="text" id="purchase_cus_search" class="form-control" autocomplete="off" placeholder="Enter Name/Mobile"> 
			                  <input type="hidden" name="gift[purchase_to]" id="purchase_to">  
			                  <span id="customerAlert"></span>
			                </div>
						</div>
						
						<div class="col-md-4" id="emp_select" style="display:none;">
							<div class="form-group">
			                  <label for="">Employee <span class="error"> *</span></label>
			                  <input type="text" name="name" id="emp_name" class="form-control" autocomplete="off" placeholder="Enter Name/Mobile"> 
			                  <input type="hidden" name="gift[id_employee]" id="id_employee">  
			                  <span id="employeeAlert"></span>
			                </div>
						</div>
						
						<div class="col-md-4" id="gift_receipts" style="display:none;">
							<div class="form-group">
			                  <label for="">No.of Voucher</label>
			                  <input type="number" value="1"  name="gift[no_of_receipts]" class="form-control" autocomplete="off" placeholder="No.of Receipts"> 
			                </div>
						</div>
						
						<div class="col-md-4 promotional_type" style="display:none;">
							<div class="form-group">
    			                  <label for="">Select Voucher <span class="error"> *</span></label><br>
    			                 	<select id="select_gift" class="form-control" name="gift[id_gift_card]"></select>
    			                </div>
						</div>
						
						<div class="col-md-4 free_type">
							<div class="form-group">
			                  <label for="">Voucher Amount <span class="error"> *</span></label>
			                  <input type="text" id="vocuher_amount" name="gift[vocuher_amount]" class="form-control" autocomplete="off" placeholder="Enter Name/Mobile"> 
			                </div>
						</div>
						
					</div>
				</div>
			    <div class="col-sm-4">
					<div class="box box-info payment_blk" style="display:none;">
						<div class="box-header with-border">
						  <h3 class="box-title">Make Payment</h3>
						  
						</div>
						<div class="box-body">
							<div class="row">
								<div class="col-sm-12">
									<div class="box-body">
									   <div class="table-responsive">
										 <table id="payment_modes" class="table table-bordered table-striped">
											<thead>
											</thead> 
											<tbody>
												<tr>
													<td class="text-right"><b class="custom-label">Receive</b></td>
													<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
													<td> 
														<input type="number" style="width:130px" class="form-control receive_amount" placeholder="Amount" name="gift[tot_amt_received]" value="" disabled>
													</td>
												</tr> 
												
							 
												<?php 
												$modes = $this->gift_voucher_model->get_payModes();
												if(sizeof($modes)>0){
												foreach($modes as $mode){
													$cash = ($mode['short_code'] == "CSH" ? '<input class="form-control cash_pay" style="width:130px" id="make_pay_cash" name="payment[cash_payment]" type="text" placeholder="Enter Amount" value="" disabled/>' : '');
													$card = ($mode['short_code'] == "CC" || $mode['short_code'] == "DC" ? '<a class="btn bg-olive btn-xs pull-right" id="card_detail_modal" href="#" data-toggle="modal" data-target="#card-detail-modal" ><b>+</b></a> ' : '');
													$cheque = ($mode['short_code'] == "CHQ"  ? '<a class="btn bg-olive btn-xs pull-right" id="cheque_modal" href="#" data-toggle="modal" data-target="#cheque-detail-modal" disabled><b>+</b></a> ' : '');
													$net_banking = ($mode['short_code'] == "NB"  ? '<a class="btn bg-olive btn-xs pull-right"  href="#" data-toggle="modal" data-target="#net_banking_modal" ><b>+</b></a> ' : '');
												?>
												<tr>
													<td class="text-right"><?php echo $mode['mode_name']; ?>
													</td>
													<td class="text-right"><?php echo $this->session->userdata('currency_symbol')?></td>
													<td class="mode_<?php echo $mode['short_code']; ?>">
														<span class="<?php echo $mode['short_code'];?>"></span>
													<input type="hidden" id="card_payment" name="payment[card_pay]" value="">
													<input type="hidden" id="chq_payment" name="payment[chq_pay]" value="">
													<input type="hidden" id="nb_payment" name="payment[net_bank_pay]" value="">
													<?php echo $cash; ?> 
													<?php echo $card; ?> 
													<?php echo $cheque; ?> 
													<?php echo $net_banking; ?> 
													</td> 
												</tr>
												<?php }}?>
											</tbody>
											<tfoot>
												<tr>
													<th class="text-right custom-label">Total</th>
													<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
													<th class="receipt_total_amount"></th>
												</tr>
												<tr>
													<th class="text-right custom-label">Balance</th>
													<th class="text-right"><?php echo $this->session->userdata('currency_symbol')?></th>
													<th class="receipt_bal_amount"></th>
												</tr>
											</tfoot>
										 </table>
									  </div>
									</div> 
								</div> 
							</div>
						</div>
					</div>
				</div>
			</div>  
			</form>
			<p class="help-block"></p> 
			<!--End of row-->
		</div>	     
		<div class="box-footer clearfix"> 
			<div class="row">
				<div class="col-xs-offset-5">
					<button id="issue_submit" type="button"  class="btn btn-primary btn-flat" ><i class="fa fa-plus"></i> Save</button>
					<button type="button" class="btn btn-default btn-flat btn-cancel">Back</button>
				</div> <br/>
			</div>
		</div> 
        <div class="overlay" style="display:none">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
  </div>    
 </section>
</div> 
  

  <!-- Card Details -->
<div class="modal fade" id="card-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Card Details</h4>
			</div>
			<div class="modal-body"> 
				<div class="box-body">
					<div class="row"> 
						<div class="col-sm-12 pull-right">
							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_card"><i class="fa fa-user-plus"></i>ADD</button>
							<p class="error "><span id="cardPayAlert"></span></p>
						</div>
					</div>
					<p></p>
				   <div class="table-responsive">
					 <table id="card_details" class="table table-bordered">
						<thead>
							<tr> 
								<th>Card Name</th>
								<th>Type</th> 
								<th>Card No</th>  
								<th>Amount</th>  
								<th>Action</th> 
							</tr>											
						</thead> 
						<tbody>
							<?php if($this->uri->segment(3) == 'edit'){
								/*foreach($est_other_item['card_details'] as $ikey => $ival){
										echo '<tr><td><input class="card_name" type="number" name="card_details[card_name][]" value="'.$ival['card_name'].'" /></td><td><input class="card_type" type="number" name="card_details[card_type][]" value="'.$ival['card_type'].'" /></td><td><input type="number" class="card_no" style="width: 100px;"  name="card_details[card_no][]" value="'.$ival['card_no'].'"  /></td><td><input type="number" class="card_amt" style="width: 100px;"  name="card_details[card_amt][]" value="'.$ival['card_amt'].'"  /></td><td>-</td></tr>';
								}*/
							}else{ ?>
							<tr> 
								<td><select name="card_details[card_name][]" class="card_name"><option value=2>VISA</option><option value=2>RuPay</option><option value=3>Mastro</option><option value=4>Master</option></select></td>
								<td><select name="card_details[card_type][]" class="card_type"><option value=1>CC</option><option value=2>DC</option></select></td>
								<td><input type="number" step="any" class="card_no" name="card_details[card_no][]"/></td> 
								<td><input type="number" step="any" class="card_amt" name="card_details[card_amt][]"/></td> 
								
								<td><a href="#" onclick="removeCC_row($(this).closest('tr')) ;" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>
							</tr> 
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<th  colspan=3>Total</th>
								<th colspan=2>
									<span class="cc_total_amount"></span>
									<span class="cc_total_amt" style="display: none;"></span>
									<span class="dc_total_amt" style="display: none;"></span>
								</th>
							</tr>
						</tfoot>
					 </table>
				  </div>
				</div>  
			</div>
		  <div class="modal-footer">
			<a href="#" id="add_card" class="btn btn-success">Save</a>
			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>
<!-- Card Details -->


<!-- Net Banking-->
<div class="modal fade" id="net_banking_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:60%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Net Banking Details</h4>
			</div>
			<div class="modal-body"> 
				<div class="box-body">
					<div class="row"> 
						<div class="col-sm-12 pull-right">
							<button type="button" class="btn bg-olive btn-sm pull-right" id="new_net_bank"><i class="fa fa-user-plus"></i>ADD</button>
							<p class="error "><span id="NetBankAlert"></span></p>
						</div>
					</div>
					<p></p>
				   <div class="table-responsive">
					 <table id="net_bank_details" class="table table-bordered">
						<thead>
							<tr> 
								<th>Type</th> 
								<th>Ref No</th>  
								<th>Amount</th>  
								<th>Action</th> 
							</tr>											
						</thead> 
						<tbody>
							<tr> 
								<td><select name="nb_details[nb_type][]" class="nb_type"><option value=1>RTGS</option><option value=2>IMPS</option></select></td>
								<td><input type="number" step="any" class="ref_no" name="nb_details[ref_no][]"/></td> 
								<td><input type="number" step="any" class="amount" name="nb_details[amount][]"/></td> 
								<td><a href="#" onClick="removeNb_row($(this).closest(\'tr\'));" class="btn btn-danger btn-del"><i class="fa fa-trash"></i></a></td>  
							</tr> 
						</tbody>
						<tfoot>
							<tr>
								<th  colspan=2>Total</th>
								<th colspan=2>
									<span class="nb_total_amount"></span>
								</th>
							</tr>
						</tfoot>
					 </table>
				  </div>
				</div>  
			</div>
		  <div class="modal-footer">
			<a href="#" id="save_net_banking" class="btn btn-success">Save</a>
			<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
		  </div>
		</div>
	</div>
</div>
<!-- Net Banking-->