<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cockpit.css">

<!-- Content Wrapper. Contains page content -->
<div class="row">
		<div class="box-body">
			<div class="col-md-12 col-xs-12 container-row" style="text-transform:uppercase">
				<div class="col-md-4 col-xs-12 box-items no-paddingwidth">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							Pending Order
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Customer Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_order_pending">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Stock Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_stock_pending">
										-
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>

				</div>
				<div class="col-md-4 col-xs-12 box-items no-paddingwidth">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							Work In progress
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Customer Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_cusorder_wip">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Stock Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_stkorder_wip">
										-
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-xs-12 box-items no-paddingwidth">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							Delivery Ready
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Customer Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_cusorder_dready">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Stock Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_stockorder_dready">
										-
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-xs-12 container-row" style="text-transform:uppercase">
				<div class="col-md-4 col-xs-12 box-items no-paddingwidth">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							Delivered
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Customer Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_cusorder_delivered">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Stock Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_stkorder_delivered">
										-
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 col-xs-12 box-items no-paddingwidth">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							karigar Reminder
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Customer Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_cusorder_kar_reminder">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Stock Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_stkorder_kar_reminder">
										-
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-xs-12 box-items no-paddingwidth">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							Karigar OverDue 
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Customer Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_cusorder_kar_overdue">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Stock Order
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="om_stkorder_kar_overdue">
										-
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-xs-12 container-row" style="display:none">
				<div class="col-md-4 col-xs-12 box-items no-paddingwidth">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							OVER DUE ORDER
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Created
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_estimation_created">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Converted
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 col-xs-12 box-items no-paddingwidth" style="display:none">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							REJECTED BY ADMIN
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Created
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_estimation_created">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Converted
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-xs-12 box-items no-paddingwidth" style="display:none">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							REJECTED BY VENDOR
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Created
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_estimation_created">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Converted
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 col-xs-12 container-row" style="display:none">
				<div class="col-md-4 col-xs-12 box-items no-paddingwidth">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							TODAY ORDER
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Created
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_estimation_created">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Converted
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 col-xs-12 box-items no-paddingwidth" style="display:none">
					<div class="col-md-12 col-xs-12 estimation no-paddingwidth">
						<div class="col-md-12 col-xs-12 item-heading">
							TODAY DELIVERY
						</div>
						<div class="col-md-12 col-xs-12">
							<div class="col-md-9 col-xs-12 no-paddingwidth">
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Created
									</div>
									<div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_estimation_created">
										-
									</div>
								</div>
								<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">
									<div class="col-md-7 col-xs-7 no-paddingwidth label-text">
										Converted
									</div>
								</div>
							</div>
							<div class="col-md-3 col-xs-3 images-right hidden-xs">
								<img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />
							</div>
						</div>
					</div>
				</div>


			</div>
			<div class="overlay" style="display:none;">
				<i class="fa fa-refresh fa-spin"></i>
			</div>
		</div>
	</div>

<div class="row order_details" style="display:none">

	<div class="col-md-12 col-xs-12 container-row">

		<div class="col-md-12 col-xs-12 box-items no-paddingwidth">

			<div class="col-md-12 col-xs-12 new_customer no-paddingwidth">

				<div class="col-md-12 col-xs-12 item-heading">

				    CUSTOMER ORDER

				</div>

				<div class="col-md-12">

				    	<div class="col-md-6 container-table">

    						<table class="table table-bordered" id="cus_order_details">

    							<thead>

                                <tr>

    								<th >BRANCH</th>

    								<th >CUSTOMER</th>

    								<th >PRODUCT</th>

    								<th >GRAMS/PCS</th>

    								<th >STATUS</th>

    							</tr>

    							</thead>

    							<tbody></tbody>

                                <tfoot >

                                    <td class="numbers" style="font-weight:bold;">Total</td>

                                    <td class="numbers"></td>

                                    <td class="total_pcs" style="font-weight:bold;">0.000</td>

                                </tfoot>

    						</table>

    					</div>

				</div>

				

					<div class="col-md-12">

				    

					<div class="col-md-12 col-xs-12 no-paddingwidth container-table">

						<table class="table table-bordered" id="customer_order_table">

							<thead>

                            <tr>

                                    <th  style="text-align:center">BRANCH</th>

                                    <th colspan="2" style="text-align:center">ALLOCATION</th>

                                    <th colspan="3" style="text-align:center">KARIGAR STATUS</th>

                                    <th colspan="4" style="text-align:center">CUSTOMER STATUS</th>

							</tr>

							<tr>

							    <th></th>

							    <th>PENDING<span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							    <th>ALLOCATED <span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							    <th>PENDING<span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							    <th>DELIVERED<span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							    <th>OVER DUE<span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							    <th>PENDING<span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							    <th>DEL READY<span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							    <th>DELIVERED<span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							    <th>OVER DUE<span style="font-size: 12px;font-weight: normal;"><br>(GRMS/PCS)</span></th>

							</tr>

							</thead>

							<tbody>

							</tbody>

                            <tfoot>

                                <td></td>

                                <td></td>

                                <td class="numbers">0.00</td>

                                <td class="numbers">0.000</td>

                                <td class="numbers">0.000</td>

                                <td class="numbers">0</td>

                            </tfoot>

						</table>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

	