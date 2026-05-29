<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cockpit.css">

<!-- Content Wrapper. Contains page content -->

<div class="row cock_pit">

    <div class="box-body">

        <div class="col-md-12 col-xs-12 container-row">

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 estimation no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        ESTIMATION

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

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_estimation_converted">

                                    -

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3 col-xs-3 images-right hidden-xs">

                            <img src="<?php echo base_url() ?>assets/img/dashboard/ESTIMATION.svg" />

                        </div>

                    </div>

                </div>

                <div style="margin-top: 10px;" class="col-md-12 col-xs-12 estimation no-paddingwidth blog-box">
    
                    <div class="col-md-12 col-xs-12 item-heading">
    
                        BANK DEPOSIT
    
                    </div>
    
                    <div class="col-md-12 col-xs-12">
    
                        <div class="col-md-12 col-xs-12 no-paddingwidth">
    
                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items table-responsive">
    
                                <table class="table table-bordered table-striped" id="branch_wise_bank_deposit">
                                    <thead>
                                        <tr>
                                            <th>Branch</th>
                                            <th class="number_text">Opening(₹)</th>
                                            <th class="number_text">Inward(₹)</th>
                                            <th class="number_text">Deposit(₹)</th>
                                            <th class="number_text">Closing(₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="text-align: center;">
                                            <td colspan="5">No Records found</td>
                                        </tr>
                                    </tbody>
                                </table>
    
                            </div>
    
    
    
                        </div>
    
                    </div>
    
                </div>
            </div>


          <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 sales no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        SALES

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <!-- <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-3 col-xs-3 no-paddingwidth label-text">

                                    Gold  &nbsp; <span class="badge bg-green" id="cp_gold_count" style="font-size:10px"> - </span>

                                </div>

                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_gold_pcs">

                                    -

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_gold_wt">

                                    -

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_gold_amt">

                                      -

                                </div>







                            </div>



                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-3 col-xs-3 no-paddingwidth label-text">

                                    Silver &nbsp; <span class="badge bg-green" id="cp_silver_count" style="font-size:10px"> - </span>

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_silver_pcs">

                                    -

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_silver_wt">

                                    -

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_silver_amt">

                                    -

                                </div>









                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-3 col-xs-3 no-paddingwidth label-text">

                                    Diamond &nbsp; <span class="badge bg-green" id="cp_dia_count" style="font-size:10px"> - </span>

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_dia_pcs">

                                    -

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_dia_wt">

                                    -

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_dia_amt">

                                    -

                                </div>



                            </div>



                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-3 col-xs-3 no-paddingwidth label-text">

                                    MRP &nbsp; <span class="badge bg-green" id="cp_mrp_count" style="font-size:10px"> - </span>

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_mrp_pcs">

                                    -

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_mrp_wt">

                                    -

                                </div>



                                <div class="col-md-3 col-xs-3 no-paddingwidth label-value" id="cp_mrp_amt">

                                    -

                                </div>

                            </div> -->

                            <div class="col-md-12 col-xs-12 no-paddingwidth container-table">

                           <table class="table " id="metal_sales"  >



                           </table>

                           </div>

                        </div>

                        <!-- <div class="col-md-3 col-xs-3 images-right hidden-xs">

                            <img  src="<?php echo base_url() ?>assets/img/dashboard/SALES.svg" />

                        </div> -->

                    </div>

                </div>

            </div>



            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 slow_moving_items no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

					 YET TO APPROVE

                    </div>

                    <?php

				    $profile=$this->admin_settings_model->profileDB('get',$this->session->userdata('profile'));

				     ?>

                    <div class="col-md-12 col-xs-12">

                    <?php if($profile['approval_type']==0||$profile['approval_type']==1){?>

                        <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                             CONTRACT PRICE

                            </div>

                            <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="contact_price">

                                -

                            </div>

                        </div>

                        <?php }?>

                        <?php if($profile['approval_type']==0||$profile['approval_type']==2){?>

                        <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                              BT APPROVE

                            </div>

                            <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="bt_approve">

                                -

                            </div>

                        </div>

                        <?php }?>

                        <?php if($profile['approval_type']==0||$profile['approval_type']==4){?>

                        <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                               BT DOWNLOAD

                            </div>

                            <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="bt_download">

                                -

                            </div>

                        </div>

                        <?php }?>

                    </div>

                </div>

            </div>



            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 slow_moving_items no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        GREEN TAG

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                TOTAL SALES

                            </div>

                            <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_greentag_sales">

                                -

                            </div>

                        </div>

                        <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                No. of Tags

                            </div>

                            <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_greentag_count">

                                -

                            </div>

                        </div>

                        <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                Sales Incentive Amount

                            </div>

                            <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="cp_greentag_rs">

                                -

                            </div>

                        </div>

                    </div>

                </div>

            </div>



        </div>



        <div class="col-md-12 col-xs-12 container-row">

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 old_metal no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        OLD METALPURCHASE

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-9 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                            <table class="table " id="old_metal_purchase">

                                    <thead>

                                        <tr>

                                            <th  style=" text-align:left;" >Metal</th>

                                             <th style=" text-align:right;" >GWT (g)</th>

                                            <th  style=" text-align:right;" >NWT (g)</th>
                                            
                                           

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>

                                            <td colspan="3">No Records found</td>

                                        </tr>

                                    </tbody>

                                    <tfoot>



                                    </tfoot>

                            </table>

                            </div>

                        </div>

                        <div class="col-md-3 col-xs-3 images-right hidden-xs">

                            <img src="<?php echo base_url() ?>assets/img/dashboard/OLD_METALPURCHASE.svg" />

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 sales_credit no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        SALES CREDIT

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-9 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Issue

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="tot_bill_amt">

                                    -

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Received

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="creditreceived">

                                    -

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3 col-xs-3 images-right hidden-xs">

                            <img src="<?php echo base_url() ?>assets/img/dashboard/SALES_CREDIT.svg" />

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 gift_voucher no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        GIFT VOUCHER

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-9 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Utilized

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="gift_tot_utlized">

                                    -

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Issued

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="gift_tot_issued">

                                    -

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Sold

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="gift_tot_sold">

                                    -

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3 col-xs-3 images-right hidden-xs">

                            <img src="<?php echo base_url() ?>assets/img/dashboard/GIFT_VOUCHER.svg" />

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <div class="col-md-12 col-xs-12 container-row">

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 customer_visit no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        CUSTOMER VISIT

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">

                                    New &nbsp; <span class="no_small" id="totalnewcusbill"> - </span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-value" id="newcisbillsalewt">

                                    -

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-value" id="newcusbillsale">

                                    -

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">

                                    Old &nbsp; <span class="no_small" id="totaloldcusbill"> - </span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-value" id="oldcusbillsalewt">

                                    -

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-value" id="oldcusbillsale">

                                    -

                                </div>

                            </div>



                        </div>

                    </div>

                </div>







            </div>

            <!--<div class="col-md-4 col-xs-12 box-items no-paddingwidth">

			<div class="col-md-12 col-xs-12 influencer_referral no-paddingwidth">

				<div class="col-md-12 col-xs-12 item-heading">

					INFLUENCER / REFERRAL

				</div>

				<div class="col-md-12 col-xs-12">

					<div class="col-md-12 col-xs-12 no-paddingwidth">

						<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

							<div class="col-md-7 col-xs-7 no-paddingwidth label-text">

								Total Sales

							</div>

							<div class="col-md-5 col-xs-5 no-paddingwidth label-value">

								-

							</div>

						</div>

						<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

							<div class="col-md-7 col-xs-7 no-paddingwidth label-text">

								Earned

							</div>

							<div class="col-md-5 col-xs-5 no-paddingwidth label-value">

								-

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>-->

            <!--<div class="col-md-4 col-xs-12 box-items no-paddingwidth">

			<div class="col-md-12 col-xs-12 reward_points no-paddingwidth">

				<div class="col-md-12 col-xs-12 item-heading">

					REWARD POINTS

				</div>

				<div class="col-md-12 col-xs-12">

					<div class="col-md-12 col-xs-12 no-paddingwidth">

						<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

							<div class="col-md-7 col-xs-7 no-paddingwidth label-text">

								Utilized

							</div>

							<div class="col-md-5 col-xs-5 no-paddingwidth">

								<span class="points">-</span> | <span>-</span>

							</div>

						</div>

						<div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

							<div class="col-md-7 col-xs-7 no-paddingwidth label-text">

								Total Earned

							</div>

							<div class="col-md-5 col-xs-5 no-paddingwidth">

								<span  class="points">-</span> | <span>-</span>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>-->

        <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 tag_sales no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        VIRTUAL TAG SALES

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-9 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Home Bill &nbsp; <span class="no_small" id="cp_virtual_tag_homesale_pcs"> - </span>

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value"

                                    id="cp_virtual_tag_homesale_wt">

                                    -

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Partly Sold &nbsp; <span class="no_small" id="cp_virtual_tag_tagsplit_pcs">-</span>

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value"

                                    id="cp_virtual_tag_tagsplit_wt">

                                    -

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3 col-xs-3 images-right hidden-xs">

                            <img src="<?php echo base_url() ?>assets/img/dashboard/VIRTUAL_TAG_SALES.svg" />

                        </div>

                    </div>

                </div>

            </div>
            
            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 tag_sales no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        PARTLY SOLD DETAILS

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-9 col-xs-12 no-paddingwidth">



                            <table class="table " id="vir_partly_sale">

                                <thead>

                                    <tr>

                                        <th style=" text-align:left;">Tot Wt (g)</th>

                                        <th style=" text-align:right;">Sold  (g)</th>

                                        <th style=" text-align:right;">Balance Wt (g)</th>



                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>

                                        <td colspan="3">No Records found</td>

                                    </tr>

                                </tbody>

                                <tfoot>



                                </tfoot>
                            </table>

                        </div>

                        <div class="col-md-3 col-xs-3 images-right hidden-xs">

                            <img src="<?php echo base_url() ?>assets/img/dashboard/VIRTUAL_TAG_SALES.svg" />

                        </div>

                    </div>

                </div>

            </div>



            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 tag_sales no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        SALES RETURN

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-9 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Total Grams

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value"

                                    id="cp_salesreturn_wt">

                                    -

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    No of Tags

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value"

                                    id="cp_salesreturn_pcs">

                                    -

                                </div>

                            </div>

                        </div>



                    </div>

                </div>

            </div>







        </div>



        <div class="col-md-12 col-xs-12 container-row">

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 branch_transfer no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        BRANCH TRANSFER

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div id="branch_transfer">



                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 lot no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        LOT

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Received &nbsp; <span class="no_small" id="lot_pcs">-</span>

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="lot_wt">

                                    -

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Tagged &nbsp; <span class="no_small" id="tagged_pcs">-</span>

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="tagged_wt">

                                    -

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                  Non Tagged &nbsp; <span class="no_small" id="non_tagged_pcs">-</span>

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="non_tagged_wt">

                                    -

                                </div>

                            </div>

                             <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                 Pending &nbsp; <span class="no_small" id="pending_pcs">-</span>

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth label-value" id="pending_wt">

                                    -

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 orders no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        ORDERS

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div id="orders">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



     <div class="col-md-12 col-xs-12 container-row">

               <!-- <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 branch_transfer no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        GOLD STOCK

                    </div>



                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div id="stock">

                                <div class="pad">



                                    <div class="clearfix">

                                        <span class="pull-left">Opening</span>

                                        <small class="pull-right" id="opening_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-light-blue" id="progress_opening_gwt">

                                        </div>

                                    </div>



                                    <div class="clearfix">

                                        <span class="pull-left">Inward</span>

                                        <small class="pull-right" id="tot_inward_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-blue" id="progress_tot_inward_gwt"></div>

                                    </div>





                                    <div class="clearfix">

                                        <span class="pull-left">Sales</span>

                                        <small class="pull-right" id="tot_sales_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-yellow" id="progress_tot_sales_gwt"></div>

                                    </div>



                                    <div class="clearfix">

                                        <span class="pull-left">Closing</span>

                                        <small class="pull-right" id="available_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-green" id="progress_available_gwt"></div>

                                    </div>



                                </div>

                            </div>

                            <div id="stock_pie">

                            </div>



                        </div>

                    </div>

                </div>

            </div>





            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 branch_transfer no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        SILVER STOCK

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div id="stock">

                                <div class="pad">



                                    <div class="clearfix">

                                        <span class="pull-left">Opening</span>

                                        <small class="pull-right" id="s_opening_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-light-blue" id="s_progress_opening_gwt">

                                        </div>

                                    </div>



                                    <div class="clearfix">

                                        <span class="pull-left">Inward</span>

                                        <small class="pull-right" id="s_tot_inward_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-blue" id="s_progress_tot_inward_gwt">

                                        </div>

                                    </div>





                                    <div class="clearfix">

                                        <span class="pull-left">Sales</span>

                                        <small class="pull-right" id="s_tot_sales_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-yellow" id="s_progress_tot_sales_gwt">

                                        </div>

                                    </div>



                                    <div class="clearfix">

                                        <span class="pull-left">Closing</span>

                                        <small class="pull-right" id="s_available_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-green" id="s_progress_available_gwt">

                                        </div>

                                    </div>



                                </div>

                            </div>

                            <div id="s_stock_pie">

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 branch_transfer no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        DAIMOND STOCK

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div id="stock">

                                <div class="pad">



                                    <div class="clearfix">

                                        <span class="pull-left">Opening</span>

                                        <small class="pull-right" id="d_opening_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-light-blue" id="d_progress_opening_gwt">

                                        </div>

                                    </div>



                                    <div class="clearfix">

                                        <span class="pull-left">Inward</span>

                                        <small class="pull-right" id="d_tot_inward_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-blue" id="d_progress_tot_inward_gwt">

                                        </div>

                                    </div>





                                    <div class="clearfix">

                                        <span class="pull-left">Sales</span>

                                        <small class="pull-right" id="d_tot_sales_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-yellow" id="d_progress_tot_sales_gwt">

                                        </div>

                                    </div>



                                    <div class="clearfix">

                                        <span class="pull-left">Closing</span>

                                        <small class="pull-right" id="d_available_gwt">-</small>

                                    </div>

                                    <div class="progress xs">

                                        <div class="progress-bar progress-bar-green" id="d_progress_available_gwt">

                                        </div>

                                    </div>



                                </div>

                            </div>

                            <div id="s_stock_pie">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div>







            </div> -->



            <div class="col-md-12 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 lot no-paddingwidth ">

                    <div class="col-md-12 col-xs-12 item-heading">

                       STOCK

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth container-table">

                            <table class="table table-bordered" id="metal_stock_items_table">

                                <thead>

                                    <tr>

                                    <th  style=" text-align:left;" >Name</th>

                                        <th style=" text-align:right;" >Opening (g)</th>

                                        <th  style=" text-align:right;" >Inward (g)</th>

                                        <th  style=" text-align:right;" >Outward (g)</th>

                                        <th  style=" text-align:right;" >Sales (g)</th>

                                        <th  style=" text-align:right;" >Closing (g)</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>

                                        <td colspan="6">No Records found</td>

                                    </tr>

                                </tbody>

                                <tfoot>



                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>

            </div>



            <div class="col-md-10 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 lot no-paddingwidth reorder_items">

                    <div class="col-md-12 col-xs-12 item-heading">

                        REORDER ITEMS

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth container-table">

                            <table class="table table-bordered" id="reorder_items_table">

                                <thead>

                                    <tr>

                                        <th>Product</th>

                                        <th>Design</th>

                                        <th>Weight Range</th>

                                        <th>Available</th>

                                        <th>Shortage</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>

                                        <td colspan="5">No Records found</td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <div class="col-md-12 col-xs-12 container-row">

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 karigar_orders no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        KARIGAR ORDERS

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">

                                    Today

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success">Delivered</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success" id="karigar_today_delivered">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items add-border">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">



                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-warning">Pending</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-warning" id="karigar_today_pending">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items add-border">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">

                                    Tommorrow

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success">Delivered</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success" id="karigar_tomm_delivered">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items pending-del">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Pending Delivery

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth alignCenter">

                                    <span class="label label-default" id="karigar_pending_delivery">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Yet to Delivery

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth alignCenter">

                                    <span class="label label-default" id="karigar_yet_to_delivery">-</span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 customer_orders no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        CUSTOMER ORDERS

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">

                                    Orders

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success">Received</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success" id="customer_today_received">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items ">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">



                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success">Allocated</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success" id="customer_today_allocated">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items ">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">



                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-danger">Pending</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-danger" id="customer_today_pending">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items ">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">



                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-info">Delivery Ready</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-info" id="customer_today_ready">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items ">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">



                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success">Delivered</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success" id="customer_today_delivered">-</span>

                                </div>

                            </div>

                            <!-- <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">

                                    Tommorrow

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success">Delivery Ready</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-success" id="customer_tomm_delivery_ready">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items add-border">

                                <div class="col-md-4 col-xs-4 no-paddingwidth label-text">



                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-warning">Pending</span>

                                </div>

                                <div class="col-md-4 col-xs-4 no-paddingwidth alignCenter">

                                    <span class="label label-warning" id="customer_tomm_pending">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items pending-del">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Pending Delivery

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth alignCenter">

                                    <span class="label label-default" id="customer_pending_delivery">-</span>

                                </div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items">

                                <div class="col-md-7 col-xs-7 no-paddingwidth label-text">

                                    Yet to Delivery

                                </div>

                                <div class="col-md-5 col-xs-5 no-paddingwidth alignCenter">

                                    <span class="label label-default" id="customer_yet_to_delivery">-</span>

                                </div>

                            </div> -->

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 stock_comm no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        STOCK

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-6 col-xs-6 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items image">

                                <img src="<?php echo base_url() ?>assets/img/dashboard/gold.svg" />

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items qty label-value"

                                id="stock_total_gold_weight">

                                -

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items comm_name">

                                Gold

                            </div>

                        </div>

                        <div class="col-md-6 col-xs-6 no-paddingwidth">

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items image">

                                <img src="<?php echo base_url() ?>assets/img/dashboard/silver.svg" />

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items qty label-value"

                                id="stock_total_silver_weight">

                                -

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth inside-items comm_name">

                                Silver

                            </div>

                        </div>

                    </div>

                </div>



            </div>

        </div>

        <div class="col-md-12 col-xs-12 container-row">

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 new_customer no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        NEW CUSTOMER

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth container-table">

                            <table class="table table-bordered" id="new_customer_table">

                                <thead>

                                    <tr>

                                        <th>Customer</th>

                                        <th>Branch</th>

                                        <th>Joined Through</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>

                                        <td colspan="3">No Records found</td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-8 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 recent_bills no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        RECENT BILLS

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth container-table">

                            <table class="table table-bordered" id="recent_bills_table">

                                <thead>

                                    <tr>

                                        <th>Branch</th>

                                        <th>Bill No</th>

                                        <th>Customer</th>

                                        <th>Type</th>

                                        <th>Amount</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>

                                        <td colspan="5">No Records found</td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-12 col-xs-12 container-row">

            <div class="col-md-4 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 cash_abstract no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        CASH ABSTRACT

                    </div>

                    <div class="col-md-12 col-xs-12 no-paddingwidth">

                        <div class="col-md-12 col-xs-12 no-paddingwidth cash_abs_details">

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Sales</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="sales_amount">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">TAX/VAT -S</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="sales_total_tax_amount">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Sales Return</div>

                                <div class="col-md-1 col-xs-1 symbol">-</div>

                                <div class="col-md-5 col-xs-5 values color" id="sales_return">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">TAX/VAT -SR</div>

                                <div class="col-md-1 col-xs-1 symbol">-</div>

                                <div class="col-md-5 col-xs-5 values color" id="sales_return_total_tax_amount">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Purchase</div>

                                <div class="col-md-1 col-xs-1 symbol">-</div>

                                <div class="col-md-5 col-xs-5 values color" id="purchase_amount">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Advance Receipt</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="advance_receipt">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Advance Deposit</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="advance_deposit">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Advance Refund</div>

                                <div class="col-md-1 col-xs-1 symbol">-</div>

                                <div class="col-md-5 col-xs-5 values color" id="ca_advance_refund">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">
                                <div class="col-md-6 col-xs-6 text">Credit Sales</div>
                                <div class="col-md-1 col-xs-1 symbol">+</div>
                                <div class="col-md-5 col-xs-5 values color" id="credit_sale">-</div>
                            </div>

                            <!-- <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Other Expenses</div>

                                <div class="col-md-1 col-xs-1 symbol">-</div>

                                <div class="col-md-5 col-xs-5 values color" id="other_expenses">-</div>

                            </div> -->

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">
                                <div class="col-md-6 col-xs-6 text">Credit Receipt</div>
                                <div class="col-md-1 col-xs-1 symbol">-</div>
                                <div class="col-md-5 col-xs-5 values color" id="credit_receipt">-</div>
                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Other Expenses</div>

                                <div class="col-md-1 col-xs-1 symbol">-</div>

                                <div class="col-md-5 col-xs-5 values color" id="other_expenses">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Handing Charges</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="handling_charge">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca total">

                                <div class="col-md-6 col-xs-6 text">Total</div>

                                <div class="col-md-1 col-xs-1 symbol"></div>

                                <div class="col-md-5 col-xs-5 values color" id="trans_total">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Cash</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="cash">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Cheque Received</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="chq_recd">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Cheque Issued</div>

                                <div class="col-md-1 col-xs-1 symbol">-</div>

                                <div class="col-md-5 col-xs-5 values color" id="chq_issued">-</div>

                            </div>


                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Card</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="card">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Net Banking</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="nb">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Advance ADJ</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="advadj">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Chit Utilized</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="chituti">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Handing Charges</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="handlingcharge">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Order ADJ</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="orderadj">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca odd">

                                <div class="col-md-6 col-xs-6 text">Gift Voucher</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="giftvoucher">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca even">

                                <div class="col-md-6 col-xs-6 text">Round Off</div>

                                <div class="col-md-1 col-xs-1 symbol">+</div>

                                <div class="col-md-5 col-xs-5 values color" id="roundoff">-</div>

                            </div>

                            <div class="col-md-12 col-xs-12 no-paddingwidth row_ca total">

                                <div class="col-md-6 col-xs-6 text">Total</div>

                                <div class="col-md-1 col-xs-1 symbol"></div>

                                <div class="col-md-5 col-xs-5 values color" id="paymodes_total">-</div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-8 col-xs-12 box-items no-paddingwidth">

                <div class="col-md-12 col-xs-12 estimation_details no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        ESTIMATION DETAILS

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth container-table">

                            <table class="table table-bordered" id="estimation_details_table">

                                <thead>

                                    <tr>

                                        <th>Branch</th>

                                        <th>Est No</th>

                                        <th>Customer</th>

                                        <th>Amount</th>

                                        <th>Status</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>

                                        <td colspan="5">No Records found</td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <div class="col-md-12 col-xs-12 trending no-paddingwidth">

                    <div class="col-md-12 col-xs-12 item-heading">

                        TRENDING - Des -0010

                    </div>

                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-12 col-xs-12 no-paddingwidth container-table">

                            <table class="table table-bordered trending_table">

                                <thead>

                                    <tr>

                                        <th></th>

                                        <th>Today</th>

                                        <th>Week</th>

                                        <th>Month</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>

                                        <td>Sold</td>

                                        <td class="values">-</td>

                                        <td class="values">-</td>

                                        <td class="values">-</td>

                                    </tr>

                                    <tr>

                                        <td>Gram</td>

                                        <td class="values">-</td>

                                        <td class="values">-</td>

                                        <td class="values">-</td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="overlay" style="display:none;">

        <i class="fa fa-refresh fa-spin"></i>

    </div>

</div>