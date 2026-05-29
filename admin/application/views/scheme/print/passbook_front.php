<html><head>
	<meta charset="utf-8">
	<title>Passbook</title>
	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/passbook.css">
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
		text-overflow: ellipsis;
	 }
	 .border{
	     border-bottom : 2px solid #000 !important;
	 } 
    /*#header { position: fixed; border-bottom:1px solid gray;}*/
    #footer { bottom:75px;position: fixed; border-top:1px solid gray;} .pagenum:before { content: counter(page); }
	
		.limit{
    		width:50px;
    		word-wrap: break-word;
			}
		td.border-bottom {
       		 border: 1pt solid;
      		}
	</style>
</head>
<body>
    <?php
        function moneyFormatIndia($num) {
            return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
        }
    ?>
    <span class="PDFReceipt"> 
        <div class="hare_krishna"></div>
        <?php if($customer['one_time_premium'] == 1 ){ ?>
        <div class="header_top"></div>
        <div style="width: 100%; text-transform:uppercase;">
			<div style="display: inline-block; width: 65%; padding-left: 40px">
                <label><?php echo 'Mr./Ms.'.$customer['account_name'].' - '.$customer['mobile']; ?></label><br>
                <?php echo (isset($customer['address1']) && $customer['address1']!='' ? "<label>".$customer['address1'].",</label><br>" :''); ?>
                <?php echo (isset($customer['address2']) && $customer['address2']!='' ? "<label>".$customer['address2'].",</label><br>" :''); ?>
                <?php echo (isset($customer['address3']) && $customer['address3']!='' ? "<label>".$customer['address3'].",</label><br>" :''); ?>
                <?php echo ($customer['city']!='' ? "<label>".$customer['city'].($customer['pincode']!='' ? ' - '.$customer['pincode'].'.' :'')."</label><br>":''); ?>
                <?php echo ($customer['state']!='' ? "<label>".$customer['state'].",</label>" :''); ?>
                <?php echo ($customer['country']!='' ? "<label>".$customer['country'].".</label><br>" :''); ?>
                <label><?php echo (isset($customer['pan_no']) && $customer['pan_no']!='' ? 'PAN : '.$customer['pan_no'] :''); ?></label>
			</div>
			<div style="width: 100%; text-align: right; display: inline-block; vertical-align: top; margin-top:-12px;">
				<div style="text-align: right; width:100%; ">
					<div style="text-align: left;width: 40%; display: inline-block;"> <?php echo $customer['branch'];?> </div>
				</div>
				<div style="text-align: right; width:100%; ">
					<div style="text-align: left;width: 40%; display: inline-block;"> <?php echo $customer['brn_address1'].",";?> </div>
				</div>
				<div style="text-align: right; width:100%; ">
					<div style="text-align: left;width: 40%; display: inline-block;"> <?php echo $customer['brn_address2'].",";?> </div>
				</div>
				
				<div style="text-align: right; width:100%; ">
					<div style="text-align: left;width: 40%; display: inline-block;"> <?php echo $customer['brn_city'].",";?> </div>
				</div>
				
				<div style="text-align: right; width:100%; ">
					<div style="text-align: left;width: 40%; display: inline-block;"> <?php echo $customer['brn_state'].",".$customer['brn_country'].".";?> </div>
				</div>
			</div>
		    <hr style="border-top:1px solid #000 !important">
		</div>
        <div style="width: 100%; text-transform:uppercase; padding-left: 40px;margin-top:10px;" align="center">
			<p style="font-size:15px;letter-spacing:1.5px;"><?php echo $customer['classification_name'];?></p><br/>
        </div>
        <br/><br/>
        <div style="width: 100%; text-transform:uppercase;">
			<div style="display: inline-block; width: 70%; padding-left: 40px;text-transform:uppercase;">
    			<table id="" class="table text-center" >
    				<tr>
    					<td class="table_heading" style="width: 25%">A/c No</td>
    					<td class="table_heading" style="width: 5%"> : </td>
    					<td class="table_heading" style="width: 30%"><?php echo $customer['scheme_acc_number'];?></td>
    				</tr>
    				<tr>
    					<td class="table_heading" style="width: 25%">Start Date</td>
    					<td class="table_heading" style="width: 5%"> : </td>
    					<td class="table_heading" style="width: 30%"><?php echo $customer['start_date'];?></td>
    				</tr>
    				<tr>
    					<td class="table_heading" style="width: 25%">Weight (g)</td>
    					<td class="table_heading" style="width: 5%"> : </td>
    					<td class="table_heading" style="width: 30%"><?php echo $customer['fixed_wgt'];?></td>
    				</tr>
    				<tr>
    					<td class="table_heading" style="width: 25%">Amount</td>
    					<td class="table_heading" style="width: 5%"> : </td>
    					<td class="table_heading" style="width: 30%"><?php echo $customer['payment_amount'];?></td>
    				</tr>
    				<?php if($customer['flexible_sch_type'] == 4 || $customer['flexible_sch_type'] == 5){ ?>
    				<tr>
    					<td class="table_heading" style="width: 25%">Purchase No</td>
    					<td class="table_heading" style="width: 5%"> : </td>
    					<td class="table_heading" style="width: 30%"><?php echo $customer['pur_ref_no'];?></td>
    				</tr>
    				<?php }else{ ?>
    				<tr>
    					<td class="table_heading" style="width: 25%">Receipt No</td>
    					<td class="table_heading" style="width: 5%"> : </td>
    					<td class="table_heading" style="width: 30%"><?php echo $customer['receipt_no'];?></td>
    				</tr>
    				<?php } ?>
    				<tr>
    					<td class="table_heading" style="width: 25%">Maturity Date</td>
    					<td class="table_heading" style="width: 5%"> : </td>
    					<td class="table_heading" style="width: 30%"><?php echo $customer['maturity_date'];?></td>
    					<!--<td class="table_heading" style="width: 30%"><?php echo date('d-m-Y', strtotime("+".$customer['maturity_days']." months", strtotime($customer['start_date'])));?></td>-->
    				</tr>
    			</table>
            </div>
			<div style="width: 100%; display: inline-block;" align="left">
				<div style="width:100px;height:100px;border:1px solid #000;">
                    
                </div>
			</div>
		</div> 
		<?php }else{ ?>
        <div style="width: 100%; text-transform:uppercase;">
			<!--Old HTMl code 14-12-2022 -->
			<!--<div style="font-size:13px !important;display: inline-block; width: 100%; margin-left:100px; padding-top: 120px; text-align:left;">
                <label>Account Name </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo 'Mr./Ms.'.$customer['account_name']; ?></b><br><br>
                <label>Account No    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  </label><b style="letter-spacing:1.5px;"><?php echo $customer['scheme_acc_number'];?></b><br/><br>
                <label>Address       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo (isset($customer['address1']) && $customer['address1']!='' ? "<label>".$customer['address1'].",</label>" :''); ?>
                <?php echo (isset($customer['address2']) && $customer['address2']!='' ? "<label>".$customer['address2'].", </label>" :'<br><br>'); ?>
                <?php echo (isset($customer['address3']) && $customer['address3']!='' ? "<label>".$customer['address3'].", </label><br>" :'<br><br>'); ?>
                
                <label>City       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo ($customer['city']!='' ? "<label>".$customer['city'].($customer['pincode']!='' && strlen($customer['pincode']) == 6? ' - '.$customer['pincode'].'.' :'-')."</label><br><br>":'<br><br>'); ?></br>
                <label>State      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo $customer['state']!='' ? "<label>".$customer['state']." ".$customer['country']."</label><br><br>" : (isset($customer['country']) ? "<label> ".$customer['country']."</label><br><br>": '' ) ?>
			    <label>Mobile No           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $customer['mobile']; ?><br><br></label>
			    <label>Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $customer['email']; ?><br><br></label>
			    <label><?php echo 'PAN No    :  '.$customer['pan_no']; ?><b style="margin-left:470px;">Customer Signature</b></label>
			    <?php 
			    if($customer['type'] == 0 || $customer['type'] == 2){ 
			        echo '<b style="font-size:12px!important;letter-spacing:1.5px;">'.$customer['amount'].'</b>';
			    }
			    ?>
			</div>-->
			
			<!--New HTMl code 14-12-2022 (Kanaga Durga)-->
			<div style="font-size:13px !important;display: inline-block; width: 100%; margin-left:213.38582677px; padding-top: 120px; text-align:left;">
                <table style="width:100%">
					<tr>
						<th>Account Name  </th>
						<th><b><?php echo 'Mr./Ms.'.$customer['account_name'];?> </b></th>
						<th style="text-align:center;">
						<b>
							<?php echo $customer['remark_open']!=''?"<b>Remarks</b>":" "  ?>
						</b>
						</th>
	   
					</tr>
					
					<tr >
						<th>Account No  </th>
						<th><b>
						    <!--Added by Durga starts here - 22.05.2023-->
						   <?php echo $customer['scheme_acc_number']; ?>
							<!--Added by Durga ends here - 22.05.2023-->
						     </b></th>
						<?php echo $customer['remark_open']!=''?"<td class='border-bottom limit' style='text-transform:lowercase;'>"
						.$customer['remark_open'].
						"</td>":'' ?>
					</tr>
					<tr >
						<th>Address  </th>
						<th>
						<?php echo (isset($customer['address1']) && $customer['address1']!='' ? "<label>".$customer['address1'].",</label>" :''); ?>
						<?php echo (isset($customer['address2']) && $customer['address2']!='' ? "<label>".$customer['address2'].", </label>" :'<br><br>'); ?>
						<?php echo (isset($customer['address3']) && $customer['address3']!='' ? "<label>".$customer['address3'].", </label>" :'<br><br>'); ?>
						</th>
					</tr>
					<tr >
						<th>City </th>
						<th>
						<?php echo ($customer['city']!='' ? "<label>".$customer['city'].($customer['pincode']!='' && 
						strlen($customer['pincode']) == 6? ' - '.$customer['pincode'].'.' :'')."</label>":'<br>'); ?>
						
						</th>
					</tr>
					<tr >
						<th>State </th>
						<th>
						</label><?php echo $customer['state']!='' ? "<label>".$customer['state']." ".$customer['country']."</label>
					" : (isset($customer['country']) ? "<label> ".$customer['country']."</label>": '' ) ?>
						</th>
					</tr>
					<tr>
						<th>Mobile no</th>
						
						<th>
						<?php echo $customer['mobile']; ?>
						</th>
					</tr>
					<tr>
						<th>Email</th>
						
						<th>
						<?php echo $customer['email']; ?>
						</th>
					</tr>
					<tr>
						<th>PAN no</th>
						
						<th>
						<?php echo $customer['pan_no']; ?>
						</th>
					</tr>
					<tr>
						<th></th>
						
						<th></th>
						
						<th>Customer Signature
						<?php 
						if($customer['type'] == 0 || $customer['type'] == 2)
						{ 
						echo '<b style="font-size:12px!important;letter-spacing:1.5px;">'.$customer['amount'].'</b>';
						}?>
						</th>
					</tr>

				</table>
			</div>
		</div>
		<!--Duplicate passbook /original passbook -->
			 <?php
    			if(isset($customer['duplicate_passbook_issued']) && $customer['duplicate_passbook_issued']==1 ) { ?>
    				    
    				    <br/><div style="text-align:right"><b>Duplicate</b></div>
    
    				    <?php
    				    }
    				    ?> 
    		<!--Duplicate passbook /original passbook -->
		<?php } ?> 
        <!--<div id="footer"></div>-->
    </span>          
</body></html>