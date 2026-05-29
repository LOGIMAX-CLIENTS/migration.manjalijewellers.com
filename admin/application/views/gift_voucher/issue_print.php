<html><head>
		<meta charset="utf-8">
		<title>Gift Issue Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/billing_receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		<style >
		 .head
		 {
			 color: black;
			 font-size: 50px;
		 }
         </style>
</head>
<body style="margin-top:10% !important;">
<span class="PDFReceipt">
            <?php 
            if($type==3)
            {
                $gift_det=$this->gift_voucher_model->getPromotionalVouchers($ref_no);
                foreach($gift_det as $issue_details)
                {

            ?>
                <div class="row">
				
                   	
				<table class="meta" style="width:30%;" align="center">
					<tr>
						<th style="text-transform:uppercase;">gift voucher</th>
					</tr>
				</table>	
				<table class="meta" style="width:40%;margin-top:-5%;text-transform:uppercase;" align="right">
				    <?php if($issue_details['branch_name']!=''){?>
					<tr>
						<th><span>Branch</span></th>
						<td><span><?php echo $issue_details['branch_name'];?></span></td>
					</tr><br><br>
					<?php }?>
					<tr>
						<th><span>Date</span></th>
						<td><span><?php echo $issue_details['date_add'];?></span></td>
					</tr>
				</table>
			</div><br><br><br>
		    <table class="meta" style="width:30%;" align="center">
					<tr>
						<th style="text-transform:uppercase;"><span><b><?php echo $issue_details['name'];?></b></span></th>
					</tr>
			</table>
		    <div class="row" align="center;">
		         
		    </div>
			<div class="row">
			       
                    <hr style="border-bottom:0.5px;">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                               
                                <table id="pp" class="table text-center">
                                    	
                                    <tr>
                                        <?php if($issue_details['voucher_type']==1){?>
                                            <?php if($issue_details['credit_type']==1){ ?>
                                                <td colspan="8"><span>Gift Voucher Worth <b>Rs.<?php echo $issue_details['amount'];?></b> Your Redeem Code <b><?php echo $issue_details['code'].'.';?> Valid Till <?php echo $issue_details['valid_to']; ?></b><?php ?></b></span></td>
                                            <?php }else if($issue_details['credit_type']==2){ ?>
                                                <td colspan="8"><span>Gift Voucher Worth <b><?php echo $issue_details['percentage'] . ' %'; ?></b> Your Redeem Code <b><?php echo $issue_details['code'].'.';?> Valid Till <?php echo $issue_details['valid_to']; ?></b><?php ?></b></span></td>
                                            <?php } ?>
                                        <?php }else{?>
                                            <td colspan="8"><span>Gift Voucher Worth <b><?php echo $issue_details['amount'].' Gram '.($issue_details['utilize_for']==0 ? 'For Gold and Silver' :$issue_details['metal']);?>  </b> Your Redeem Code <b><?php echo $issue_details['code'].'.';?> Valid Till <?php echo $issue_details['valid_to']; ?></b><?php ?></b></span></td>
                                        <?php }?>
                                        
                                    </tr>
                                    <tr>
                                        <td colspan="12"><hr style="border-bottom:0.5pt;"></td>
                                    </tr>
                                </table><br>	
                            </div>	
                        </div>	
            </div><br>
            	<?php 
                   	 if($issue_details['description']!='')
                   	 {?>
                   	 <label>Terms and Conditions</label><br><br>	
                   	   <?php  echo $issue_details['description'];?>
                <?php  }?>
            </div><br>
           
            <div class="row" style="text-transform:uppercase;margin-top:40%;">
					<label style="margin-left:30%;"><?php echo $comp_details['company_name']?></label>
			</div>
			
			<div style="page-break-after: always;"></div>
                
            <?php }?>
            
            <?}else{?>
            <!--<div>
                <img alt="" src="<?php echo base_url();?>assets/img/receipt_logo.png">
            </div>-->

            <div class="" align="left">
				<label style="font-weight:bold;"><?php echo 'Mr./Ms.'.$issue['cus_name']; ?></label><br>			
				<label><?php echo ($issue['branch_name']!='' ?$issue['branch_name'].',' :'').'Mobile No : '.$issue['mobile']; ?></label>
			</div>
			<p></p>
			<div class="row">
				<table class="meta" style="width:30%;" align="left">
					<tr>
						<th><span>Gold Rate</span></th>
						<td><span><?php echo $metal_rate['goldrate_22ct'].'/'.'Gm'; ?></span></td>
					</tr>
					<tr style="padding-top:-0.5em;">
						<th><span>Silver Rate</span></th>
						<td><span><?php echo $metal_rate['silverrate_1gm'].'/'.'Gm'; ?></span></td>
					</tr>
				</table>	
				<table class="meta" style="width:30%;" align="center">
					<tr>
						<th style="text-transform:uppercase;">gift issue receipt</th>
					</tr>
				</table>	
				<table class="meta" style="width:40%;margin-top:-6%;text-transform:uppercase;" align="right">
					<tr>
						<th><span>State</span></th>
						<td><span><?php echo $comp_details['state'];?></span></td>
					</tr><br><br>
					
					<tr>
						<th><span>Date</span></th>
						<td><span><?php echo $issue['date_add'];?></span></td>
					</tr>
				</table>
			</div>
			<div class="row">
                    <hr style="border-bottom:0.5px;">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table id="pp" class="table text-center">
                                    <tr>
                                            <td colspan="8"><span>Gift Voucher Worth <b><?php echo 'Rs.'.$issue['amount'];?>  </b> Your Redeem Code <b><?php echo $issue['code'].'.';?> <?php echo ($issue['valid_to']!='' ? 'Valid Till'.$issue['valid_to'] :''); ?></b><?php ?></b></span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="12"><hr style="border-bottom:0.5pt;"></td>
                                    </tr>
                                </table><br>	
                            </div>	
                        </div>	
            </div><br>
			<?php }?>
 </span>          
</body></html>