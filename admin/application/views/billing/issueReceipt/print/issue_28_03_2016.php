<html><head>
		<meta charset="utf-8">
		<title>Payment Report</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/billing_receipt_2.css">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Cinzel&display=swap" rel="stylesheet">
		<style >
		 .head
		 {
			 color: black;
			 font-size: 30px;
		 }
		 .alignCenter {
			 text-align: center;
		 }
		 .alignRight {
			 text-align: right;
		 }
		 .table_heading {
			 font-weight: bold;

		 }
		 .textOverflowHidden {
			white-space: nowrap;
			overflow: hidden;
			/* text-overflow: ellipsis; */
		 }
		.duplicate_copy * {
			font-size: 9px;
		}
		.duplicate_copy #pp th, .duplicate_copy #pp td{
			font-size: 9px !important;
		}


		.stones, .charges {
			font-style: italic;
		}
		.stones .stoneData, .charges .chargeData {
			font-size: 10px !important;
		}

        .addr_labels {
            display: inline-block;
            width: 30%;
        }

        .addr_values {
            display: inline-block;
            padding-left: -5px;
        }

		.rate_labels {
            display: inline-block;
            width: 30%;
        }

		.addr_brch_labels {
			display: inline-block;
			width: 30%;
		}

		.addr_brch_values {
			display: inline-block;
			padding-left: 2px;
		}
		.wrapper{
            display:flex;
            width: 100%;
			height:100px;
        }
        #a1, #a3{
            width:40%;
			font-size:12px;
			font-weight:bold;
        }
        #a2{
            width:20%;
			font-weight: bold;
			font-size:18px;
            text-align:center;
            text-transform: uppercase;
		}
        #a3{
            text-align:right;
        }
		.footer{
			text-align:justify;

		}

        .footer {

			width: 100%;

			height: 100px;

		}

		@page {

			size: A4;

			margin-top:140px;
			margin-bottom: 20px;

		}

		@media print {

			table.paging tfoot td {

				height: 110px;

			}

            .footer {

                position: fixed;

                bottom: 0;
            }
        }
    </style>

  </head>
  <body>

  	<div class="PDFReceipt" >

	  <?php

		$login_emp = $billing['emp_name'];

		$esti_sales_emp = '';
		$esti_purchase_emp = '';
		$esti_return_emp = '';

		$esti_sales_id = '';
		$esti_purchase_id = '';
		$esti_return_id = '';

		function moneyFormatIndia($num) {
			return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
		}

		$gold_metal_rate = ($billing['goldrate_22ct']>0 ? $billing['goldrate_22ct']:$metal_rate['goldrate_22ct']);
		$silver_metal_rate = ($billing['silverrate_1gm']>0 ?$billing['silverrate_1gm'] :$metal_rate['silverrate_1gm']);
		$metal_type = 0;

		$tot_sales_amt=0;
		$sales_cost = 0;
		foreach($est_other_item['item_details'] as $items) {
			$sales_cost += $items['item_cost'];
		}
		$tot_sales_amt  =  $sales_cost;

		$total_return=0;
		foreach($est_other_item['return_details'] as $items) {
			$total_return  += $items['item_cost'];
		}

		$pur_total_amt=0;
		foreach($est_other_item['old_matel_details'] as $items) {
			$pur_total_amt += $items['amount'];
		}
	?>

    <table class=paging><thead><tr><td>
	<div class="content-block" style="margin-top:23px !important;">
			<?php
    		    if(sizeof($est_other_item['item_details'])>0) //SALES BILL
    		    {
    		         if($billing['bill_type'] == 15){
    		             $invoice_no = $billing['branch_code'].'-SA-'.$billing['approval_ref_no'];
    		         }else{
    		            $invoice_no = $billing['branch_code'].'-SA-'.$billing['sales_ref_no'];
    		         }
    		    }else if(sizeof($est_other_item['old_matel_details'])>0) // OLD METAL ITEMS
    		    {
    		        $invoice_no =  $billing['branch_code'].'-PU-'.$billing['pur_ref_no'];
    		    }
    		    else if(sizeof($est_other_item['return_details'])>0) //SALES RETURN
    		    {
    		        $invoice_no =  $billing['branch_code'].'-SR-'.$billing['s_ret_refno'];
    		    }
    		    else if($billing['bill_type']==5) //ORDER ADVANCE
    		    {
    		        $invoice_no =  $billing['branch_code'].'-OD-'.$billing['order_adv_ref_no'];
    		    }
    		    else if($billing['bill_type']==8)   //CREDIT COLLECTION
    		    {
    		        $invoice_no =  $billing['branch_code'].'-CC-'.$billing['credit_coll_refno'];
    		    }
    		    else if($billing['bill_type']==10)   //CHIT PRE CLOSE
    		    {
    		        $invoice_no =  $billing['branch_code'].'-'.$billing['chit_preclose_refno'];
    		    }else
    		    {
    		        $invoice_no =  $billing['bill_no'];
    		    }
		    ?>

			<div class="wrapper">

				<div id="a1">
					<label><?php echo (!empty($issue['title']) ? $issue['title'].'. ' : 'Mr/Mrs. ').$issue['name']; ?></label><br>
					<label><?php echo ($issue['address1']!='' ? strtoupper($issue['address1']).',' :''); ?></label>
					<label><?php echo ($issue['address2']!='' ? strtoupper($issue['address2']).','."<br>" :''); ?></label>
					<label><?php echo ($issue['address3']!='' ? strtoupper($issue['address3']).','."<br>" :''); ?></label>
					<label><?php echo ($issue['village_name']!='' ? strtoupper($issue['village_name']).','."<br>" :''); ?></label>
					<label><?php echo ($issue['city_name']!='' ? strtoupper($issue['city_name']).($issue['pincode']!='' ? ' - '.$issue['pincode'].'.' :'')."<br>" :''); ?></label>
					<label><?php echo ($issue['cus_state']!='' ? strtoupper($issue['cus_state']).','."<br>" :''); ?></label>
					<!-- <label><?php echo ($issue['cus_country']!='' ? '<div class="addr_labels">Country</div><div class="addr_values">:&nbsp;&nbsp;'.strtoupper($issue['cus_country'])."</div><br>" :''); ?></label> -->
					<label><?php echo (isset($issue['pan_no']) && $issue['pan_no']!='' ? 'PAN NO : '.strtoupper($issue['pan_no'])."<br>" :''); ?></label>
					<label><?php echo (isset($issue['adhar_no']) && $issue['adhar_no']!='' ? 'AADHAR NO : '.strtoupper($issue['adhar_no'])."<br>" :''); ?></label>
					<label><?php echo (isset($issue['pp_no']) && $issue['pp_no']!='' ? 'PASSPORT NO : '.strtoupper($issue['pp_no'])."<br>" :''); ?></label>
					<label><?php echo (isset($issue['dl_no']) && $issue['dl_no']!='' ? 'DRIVING LICENSE NO : '.strtoupper($issue['dl_no'])."<br>" :''); ?></label>
					<label><?php echo (isset($issue['gst_number']) && $issue['gst_number']!='' ? 'GST NO : '.strtoupper($issue['gst_number'])."</div><br>" :''); ?></label>
					<label><?php echo 'MOBILE : '.$issue['mobile']; ?></label>
                </div>

               <div id="a2">



            </div>

               <div id="a3">
                   <label for="">Invoice No : <?php echo $issue['bill_no'] ;?></label> <br>
                    <label for="">Date : <?php echo $issue['date_add'] ;?></label> <label for="">Time : <?php echo $issue['time_add'] ;?></label> <br>
					<label><?php echo 'Gold 22-KT:&nbsp;&nbsp;'.number_format($metal_rate['goldrate_22ct'],2,'.','').'/'.'Gm'.'&nbsp;&nbsp;18-KT:&nbsp;&nbsp;'.number_format($metal_rate['goldrate_18ct'],2,'.','').'/'.'Gm'; ?></label><br>
                    <label><?php echo 'SILVER:&nbsp;&nbsp;'.number_format($metal_rate['silverrate_1gm'],2,'.','').'/'.'Gm'; ?></label><br>
					<label for="statecode"><?php echo 'State Code: '.$issue['state_code']; ?></label>
			</div>


			</div>
            <div style="width:100%; height: 10px; text-align:center; font-weight:bold; font-size:20px; text-transform: uppercase;">
            <label>
    					<?php echo $issue['receipt_type']; ?>
                </label>
            </div>
		</td></tr></thead>
		<tbody><tr><td>
        <div  class="content-wrapper">
 <div class="box">
  <div class="box-body">
 			<div  class="container-fluid">
				<div>
							<div  class="row">
                                <br>
    						    <hr class="item_dashed" >
    							<div class="col-xs-12">
    								<div class="table-responsive">
    								<table id="pp" class="table text-center" style="font-size:12px !important; ">
    										<thead>
    											<tr>
    												<th  colspan="7" style="text-transform:uppercase; text-align:left;">Description</th>
    												<th class="alignRight" style="text-transform:uppercase;" colspan="2">Amount</th>
    											</tr>
    										</thead>
    										<tbody>
                                                <tr>
                                                    <td colspan="9"><hr class="item_dashed" ></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9">&nbsp;</td>
                                                </tr>
    											<tr>
    											    <?php if($issue['type']==2){?>
														<?php
    												    if($issue['rct_type']==8)
    												    {?>

														<td colspan="7" style="text-transform:uppercase;"><?php echo 'Petty Cash Receipt Against Issue No :'.$issue['bills'].' From '.(!empty($issue['title']) ? $issue['title'].'. ' : 'Mr/Mrs. ').$issue['name'] .'-'.$issue['mobile'];?></td>

														<?php
                                                        }else{
															?>
															<td colspan="7" style="text-transform:uppercase;">
																<?php 
																	$customer_name = !empty($issue['from_customer']) ? $issue['from_customer'] : $issue['name'];
																	echo 'Received with thanks from '.(!empty($issue['title']) ? $issue['title'].'. ' : 'Mr/Mrs. ').$customer_name.' Towards Advance Bill No : '.$issue['bill_no'];
																?>
															</td>

														<?php
														}
    												    ?>
    												<?php }else if($issue['type']==1){?>
    												    <?php
    												    if($issue['issue_type']==3)
    												    {?>
    												        <td colspan="7" style="text-transform:uppercase;"><?php echo 'Refund to '.(!empty($issue['title']) ? $issue['title'].'. ' : 'Mr/Mrs. ').$issue['name'];?></td>
    												    <?php }else if($issue['issue_type']==1)
    												    {?>
    												        <td colspan="7" style="text-transform:uppercase;"><?php echo 'Payment Issue Voucher To '.(!empty($issue['title']) ? $issue['title'].'. ' : 'Mr/Mrs. ').$issue['name']; ?></td>
    												    <?php } elseif($issue['issue_type']==2){ ?>
                                                            <td colspan="7" style="text-transform:uppercase;"><?php echo 'Paid to '.(!empty($issue['title']) ? $issue['title'].'. ' : 'Mr/Mrs. ').$issue['name'].' Towards Issue Bill No : '.$issue['bill_no'];?></td>
                                                       <?php
                                                        }
    												    ?>

    												<?php }?>
    												<td colspan="2" class="alignRight"><?php echo 'Rs '. moneyFormatIndia(number_format($issue['amount'],2,'.',''));?></td>
    											</tr>
    									</tbody>
    											<tr>
    												<td colspan="9"><hr class="item_dashed"></td>
    											</tr>
    									</table><br>
    								</div>
    							 </div>
    							 <?php
        						if($issue['narration']!='')
        						{?>
        						    <p><b>REMARKS :- <?php echo $issue['narration'];?></b></p>
        						<?php }	?>
    						</div><br>


					<!--	<?php if(sizeof($payment)>0){?>
							<div  class="row">
							   <div class="col-xs-6">
									<div class="table-responsive" >
										<table id="pp" class="table text-center" style="width:40%;" align="center">

										<?php
										$i=1;
										$total_amt=0;
										$due_amount=0;
										$paid_advance=0;
										foreach($payment as $items)
											{
												$total_amt+=$items['payment_amount'];
											?>
											<tr style="font-weight:bold;">
											<td><?php echo $items['payment_mode'];?></td>
											<td>Rs.</td>
											<td><?php echo moneyFormatIndia(number_format($items['payment_amount'],2,'.',''));?></td>
											</tr>
										<?php $i++;}?>


											<tr>
												<td><hr style="border-bottom:0.5pt;width:170%;"></td>
											</tr>
											<tr style="font-weight: bold;">
												<td>Total</td>
												<td>Rs.</td>
												<td><?php echo number_format((float)($total_amt+$due_amount+$order_adv_pur+$paid_advance),2,'.','');?></td>
											</tr>


									</table><br>
								</div>
							 </div>
						</div><br><br><br>
						<?php }?>-->

						<?php
						if(sizeof($advance_adj_details)>0)
						{
						    $adjusted_amt=0;
						    foreach($advance_adj_details as $adj)
						    {
						        $adjusted_amt+=$adj['adjusted_amt'];
						    }
						}
						?>

						<?php if(sizeof($payment)>0){?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table id="pp" class="table text-center" style="width:100%; font-size:12px;" >

                                                <?php
										$total_amt=0;
										$due_amount=0;
										$paid_advance=0;
    										foreach($payment as $items)
											{
    											$total_amt+=$items['payment_amount'];
												if($items['payment_amount']>0){
    											?>
											<tr>
    											<th colspan="7"></th>
												<th class="alignRight"><?php if($items['payment_mode']=='NB' || $items['payment_mode']=='E-COM')
    											{
    											echo ($items['payment_mode']=='E-COM' ? 'Razorpay' : $items['nb_type'])."<br><span style='font-size:10px !important;'>". ($items['received_date']!= null ? ' Dt ('. $items['received_date'] .')' : '') ."</span>";
    											}
    											else
    											{
    											echo $items['payment_mode'];
    											}?>
    											</th>
												<th class="alignRight"><?php echo moneyFormatIndia(number_format($items['payment_amount'],2,'.',''));?></th>
											</tr>
    											<?php }} ?>
    											<?php
    											if($adjusted_amt>0)
    											{?>

    											<tr>
    											<th colspan="7"></th>
												<th class="alignRight">Adv Adj</th>
												<th class="alignRight"><?php echo moneyFormatIndia(number_format($adjusted_amt,2,'.',''));?>
    											<?php }
    											?>
												</tr>
                                                <tr>
    											<th colspan="7"></th>
												<th class="alignRight">Total</th>
												<th class="alignRight"><?php echo moneyFormatIndia(number_format((float)($total_amt+$due_amount+$order_adv_pur+$paid_advance+$adjusted_amt),2,'.',''));?>
                                                    </th>
												</tr>


                                        </table><br>
                                    </div>
                                </div>
                            </div><br><br>
                            <?php }?>

				</div>
				<div style="margin-top: 3px; margin-bottom: 3px">
					<div><span style="font-weight: bold;">Amount in Words</span> : <span >Rupees <?php echo $this->ret_billing_model->no_to_words($issue['amount']); ?> Only</span></div>
				</div><br>

				<?php if($issue['emp_name']!=''){?>
									<div style="font-weight:bold;">
										<br>
										<?php echo 'Emp : '.$issue['emp_name'].'/'.$issue['emp_code']?>

									</div><br>
								<?php } ?>

				<?php if(sizeof($receipt_adv_details)>0){
                    $tot_adv=0;
                    $adj_amt=0;

                    foreach($receipt_adv_details as $adv)
                    {
                      $tot_adv=$adv['receipt_amt'];
                      $adj_amt=$adv['utilized_amt'];

                    ?>
                     <div>
                        <table id="pp" class="table text-center"style="width:85%" >
                          <tr>
                           <td><b>Receipt No</b></td>
                           <td><b>Receipt Date</b></td>
                           <td><b>Receipt Amount</b></td>
                           <td><b>Utilized Amount</b></td>
                           <td><b>Refund Amount</b></td>
                           <td><b>Balance Amount</b></td>


                       </tr>
                          <tbody>
                              <tr>
                              <td><?php  echo $adv['bill_no'];?></td>
                              <td ><?php echo $adv['bill_date'];?></td>
                              <td ><?php echo moneyFormatIndia(number_format($adv['receipt_amt'],2,'.','' ));?></td>
                              <td ><?php echo moneyFormatIndia(number_format($adv['utilized_amt'],2,'.','' ));?></td>
                              <td ><?php echo moneyFormatIndia(number_format($adv['refund_amount'],2,'.','' ));?></td>
                              <td ><?php echo moneyFormatIndia(number_format($adv['balance_amount'],2,'.','' ));?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div><br>

                <?php }}?>




			</div>
 </div>
 </div><!-- /.box-body -->
</div>






   		</td></tr></tbody><tfoot><tr><td>&nbsp;</td></tr></tfoot></table>

		<div class="footer">
			<div style="height:30px;">

			</div>

				<div style="display:flex; font-weight:bold;">

					<div style="font-size:16px; text-align:left;width:50%">Customer Signature</div>
					<div style="font-size:16px; text-align:Right;width:48%;">For <?php echo $comp_details['company_name']?></div>

				</div>

			<div style="font-size:11px; width:98%;">
			<p>*Amount of tax (Invoice Inwards) subject to reverse charge. *Received ornaments in good condition. *The Hallmarking charge is included in the invoice.*The consumer can get the purity of the hallmarked jewellery/artefacts checked from any of the BIS recognized A&H center. The list of BIS recognized A&H centers along with address and contact details are available in the website:www.bis.gov.in
				   E.&O.E</p> </div><br>

			</div>
	</div>
  </body>
  <script type="text/javascript">
        window.print();
    </script>
</html>