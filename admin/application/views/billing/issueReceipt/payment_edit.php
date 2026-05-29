      <!-- Content Wrapper. Contains page content -->
      <style>
      	.remove-btn {
      		margin-top: -168px;
      		margin-left: -38px;
      		background-color: #e51712 !important;
      		border: none;
      		color: white !important;
      	}

      	.sm {
      		font-weight: normal;
      	}
		.container {
			left: 0;
			right: 0;
			margin: auto;
			margin-top: 1px;
			border: 1px solid rgba(180, 180, 180, .5);
			border-radius: 25px;
		}
		fieldset 
		{
			border: 1px solid #ddd !important;
			margin: 0;
			padding: 10px;       
			position: relative;
			border-radius:4px;
			padding-left:10px!important;
		}	
	
		legend
		{
			font-size:14px;
			font-weight:bold;
			margin-bottom: 0px; 
			width: 11%;
			border-bottom: none;
			padding: 5px 5px 5px 5px; 
		}
		.legend2 {
			width: 9%;
		}
		.modal_btns {
			display: flex;
			justify-content: space-between;
		}
      </style>
      <div class="content-wrapper">
      	<!-- Content Header (Page header) -->
      	<section class="content-header">
      		<h1>
      			Issue / Receipt — Payment Edit
      		</h1>
      		<ol class="breadcrumb">
      			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      			<li><a href="<?php echo base_url('index.php/admin_ret_billing/issue/list'); ?>">Issue &amp; Receipt</a></li>
      			<li class="active">Payment Edit</li>
      		</ol>
      	</section>

      	<!-- Main content -->
      	<section class="content order">

      		<!-- Default box -->
      		<div class="box box-primary">
				<div class="container">
					<div class="box-body">
					  <fieldset id="ir_searchDet">
					  <legend>Voucher Details</legend>
      				<?php
						if ($this->session->flashdata('chit_alert')) {
							$message = $this->session->flashdata('chit_alert');
						?>
      					<div class="alert alert-<?php echo $message['class']; ?> alert-dismissable">
      						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      						<h4><i class="icon fa fa-check"></i> <?php echo $message['title']; ?>!</h4>
      						<?php echo $message['message']; ?>
      					</div>

      				<?php } ?>
					<br>

					<!-- Voucher info (populated by JS on page load) -->
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" class="col-md-2">Bill No</label>
						<div class="col-md-3">
							<div class="input-group col-md-12">
								<input type="text" class="form-control text" id="ir_info_billno_display" disabled>
							</div>
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" class="col-md-2">Type</label>
						<div class="col-md-3">
							<div class="input-group col-md-12">
								<input type="text" class="form-control text" id="ir_info_type_display" disabled>
							</div>
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" class="col-md-2">Party</label>
						<div class="col-md-3">
							<div class="input-group col-md-12">
								<input type="text" class="form-control text" id="ir_info_party_display" disabled>
							</div>
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" class="col-md-2">Date</label>
						<div class="col-md-3">
							<div class="input-group col-md-12">
								<input type="text" class="form-control text" id="ir_info_date_display" disabled>
							</div>
						</div>
					</div>

					<br>
					<input type="hidden" id="ir_id_issue_rcpt" value="">
					</fieldset>

					<fieldset id="ir_payDet" class="fs2" style="display:none;">
					  <legend class="legend2">Payment Details</legend>

					<!-- Voucher Amount -->
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" id="ir_rcvd_amt_lbl" class="col-md-2">Voucher Amount</label>
						<div class="col-md-3">
							<div class="input-group col-md-12">
								<input class="form-control" id="ir_voucher_amount" type="text" disabled value="" />
							</div>
						</div>
					</div>

					<br>
					<!-- Cash -->
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" class="col-md-2">Cash</label>
						<div class="col-md-3">
							<div class="input-group col-md-12">
								<input class="form-control" id="ir_cash_payment" autocomplete="off" type="number" step="any" min="0" placeholder="Enter Amount" value="" />
							</div>
						</div>
					</div>

					<br>
					<!-- Card Payment -->
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" class="col-md-2">Card Payment</label>
						<span class="input-group col-md-3">
							<div class="col-md-12">
								<div class="input-group">
									<input class="form-control" id="ir_cc_total_display" type="text" readonly value="" />
									<a class="input-group-addon btn btn-default" id="ir_card_modal_btn" href="#"><b>+</b></a>
								</div>
							</div>
						</span>
					</div>

					<br>
					<!-- Cheque -->
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" class="col-md-2">Cheque</label>
						<span class="input-group col-md-3">
							<div class="col-md-12">
								<div class="input-group">
									<input class="form-control" id="ir_chq_total_display" type="text" readonly value="" />
									<a class="input-group-addon btn btn-default" id="ir_cheque_modal_btn" href="#"><b>+</b></a>
								</div>
							</div>
						</span>
					</div>

					<br>
					<!-- Net Banking + Update button -->
					<div class="row">
						<div class="col-md-2"></div>
						<label for="" class="col-md-2">Net Banking</label>
						<div class="col-md-3">
							<div class="input-group">
								<input class="form-control" id="ir_nb_total_display" type="text" readonly />
								<a class="input-group-addon btn btn-default" id="ir_net_bank_modal_btn" href="#"><b>+</b></a>
							</div>
						</div>
						<div class="col-md-2">
							<button class="btn btn-primary pull-right" id="ir_save_payment_edit">Update</button>
						</div>
					</div>
      			</div>

      			<p class="hepl-block"></p>
      			<div class="row">
					<div class="col-md-2 col-md-offset-5">
						<input type="hidden" id="ir_card_payment_json" value="[]" />
						<input type="hidden" id="ir_chq_payment_json" value="[]" />
						<input type="hidden" id="ir_nb_payment_json" value="[]" />
						<button type="button" class="btn btn-default btn-cancel" id="ir_cancel_edit">Back</button>
					</div> <br><br>
      			</div>

				<!-- Alert area for payment validation errors -->
				<div class="row" id="ir_pay_alert_row" style="display:none;">
					<div class="col-md-8 col-md-offset-2">
						<div class="alert alert-danger" id="ir_pay_alert_msg"></div>
					</div>
				</div>

				  <div class="overlay" id="ir_page_overlay" style="display:none">
      			<i class="fa fa-refresh fa-spin"></i>
			</div>
		</fieldset>
		</div> <!-- box-body-->
      </div> <!-- Default box-->
	  </div>
      </section>
      </div>
      <div class="modal fade" id="card-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      	<div class="modal-dialog" style="width:80%;">
      		<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close card_close_btn" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      				<h4 class="modal-title" id="myModalLabel">Card Details</h4>
      			</div>
      			<div class="modal-body">
      				<div class="box-body">
      					<div class="row">
      						<div class="col-sm-12 pull-right">
							<span class="h_amt" style="color:red;">Balance Amount:</span>
							<span class="balance_amount f_amt" style="color:red;">0</span>
      						<button type="button" class="btn bg-olive btn-sm pull-right" id="new_card"><i class="fa fa-user-plus"></i>ADD</button>
      						<p class="error "><span id="cardPayAlert"></span></p>
      					</div>
      					</div>
      					<p></p>
      					<div class="table-responsive">
      					<table id="card_details" class="table table-bordered">
      						<thead>
      							<tr>
								<th width="17%">Card Name</th>
								<th width="15%">Type</th>
								<th width="10%">Device<span class="error">*</span></th>
								<th width="13%">Date</th>
								<th width="13%">Card No</th>
								<th width="14%">Amount</th>
								<th width="13%">Approval No</th>
								<th width="5%">Action</th>
      							</tr>
      						</thead>
      						<tbody></tbody>
      						<tfoot>
      							<tr>
      								<th colspan="5">Total</th>
      								<th colspan="3">
								  <span class="cc_total_amount"></span>
								  <span class="cc_total_amt" style="display:none;"></span>
								  <span class="dc_total_amt" style="display:none;"></span>
								</th>
      							</tr>
      						</tfoot>
      					</table>
      					</div>
      				</div>
      			</div>
      			<div class="modal-footer">
      				<a href="#" id="add_newcc" class="btn btn-success">Save</a>
      				<button type="button" class="btn btn-close btn-warning card_close_btn" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>

      <!-- Cheque Details -->
      <div class="modal fade" id="cheque-detail-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      	<div class="modal-dialog" style="width:70%;">
      		<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close chq_close_btn" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      				<h4 class="modal-title">Cheque Details</h4>
      			</div>
      			<div class="modal-body">
      				<div class="box-body">
      					<div class="row">
      						<div class="col-sm-12 pull-right">
							<span class="h_amt" style="color:red;">Balance Amount:</span>
							<span class="balance_amount f_amt" style="color:red;">0</span>
      						<button type="button" class="btn bg-olive btn-sm pull-right" id="new_chq"><i class="fa fa-user-plus"></i>ADD</button>
      						<p class="error"><span id="chqPayAlert"></span></p>
      					</div>
      					</div>
      					<p></p>
      					<div class="table-responsive">
      					<table id="chq_details" class="table table-bordered">
      						<thead>
      							<tr>
								<th width="20%">Cheque Date</th>
								<th width="20%">Bank</th>
								<th width="25%">Cheque No</th>
								<th width="30%">Amount</th>
								<th width="5%">Action</th>
      							</tr>
      						</thead>
      						<tbody></tbody>
      						<tfoot>
      							<tr>
								<td colspan="3">Total</td>
								<td colspan="2"><span class="chq_total_amount"></span></td>
      							</tr>
      						</tfoot>
      					</table>
      					</div>
      				</div>
      			</div>
      			<div class="modal-footer">
      				<a href="#" id="add_newchq" class="btn btn-success">Save</a>
      				<button type="button" class="btn btn-close btn-warning chq_close_btn" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>

      <!-- Net Banking / UPI Details -->
      <div class="modal fade" id="net_banking_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
      	<div class="modal-dialog" style="width:62%;">
      		<div class="modal-content">
      			<div class="modal-header">
      				<button type="button" class="close NB_close_btn" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      				<h4 class="modal-title">Net Banking / UPI Details</h4>
      			</div>
      			<div class="modal-body">
      				<div class="box-body">
      					<div class="row">
      						<div class="col-sm-12 pull-right">
							<span class="h_amt" style="color:red;">Balance Amount:</span>
							<span class="balance_amount f_amt" style="color:red;">0</span>
      						<button type="button" class="btn bg-olive btn-sm pull-right" id="new_net_bank"><i class="fa fa-user-plus"></i>ADD</button>
      						<p class="error"><span id="NetBankAlert"></span></p>
      					</div>
      					</div>
      					<p></p>
      					<div class="table-responsive">
      					<table id="net_bank_details" class="table table-bordered">
      						<thead>
      							<tr>
								<th width="15%">Type</th>
								<th width="20%">Bank/Device</th>
								<th width="20%">Net Banking Date</th>
								<th width="20%">Ref No</th>
								<th width="20%">Amount</th>
								<th width="5%">Action</th>
      							</tr>
      						</thead>
      						<tbody></tbody>
      						<tfoot>
      							<tr>
								<th colspan="4">Total</th>
								<th colspan="2"><span class="nb_total_amount"></span></th>
      							</tr>
      						</tfoot>
      					</table>
      					</div>
      				</div>
      			</div>
      			<div class="modal-footer">
      				<a href="#" id="add_newnb" class="btn btn-success">Save</a>
      				<button type="button" class="btn btn-close btn-warning NB_close_btn" data-dismiss="modal">Close</button>
      			</div>
      		</div>
      	</div>
      </div>
