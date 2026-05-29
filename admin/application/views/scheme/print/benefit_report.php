<?php setlocale(LC_MONETARY, 'en_IN');  ?> 


<!doctype html>
<html>
<head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
		<style>
         @media print
         {
         .firstrow {page-break-inside:auto}
         }
        </style>

		
</head>
<body>
	<div class="PDFReceipt" >

<!--header part logo,comp details-->
		<div class="chit_detailslogo"><img alt="" src="<?php echo base_url();?>assets/img/receipt_logo.jpg?<?php time();?>" class=""></div>
            <div class="address" align="right"> 
                    <?php  echo ($comp_details['address1'] != '') ? $comp_details['address1'].' ,' : ''; ?>				
                    <?php echo ( $comp_details['address2'] != '')?$comp_details['address2'].' ,'  : ''; ?>
                    <?php echo ( $comp_details['city'] != '')? $comp_details['city'].'  '.$comp_details['pincode'].' ,'  : ''; ?>
                    <?php echo ( $comp_details['state'] != '')? $comp_details['state'].' ,'  : ''; ?>
                    <?php echo ( $comp_details['country'] != '')? $comp_details['country'].' .'  : ''; ?>
                    <p> <?php echo($comp_details['phone'] != '')?  'Phone : '. $comp_details['phone'] : '';?></p><p> <?php ($comp_details['mobile'] != '') ? 'Mobile : '.$comp_details['mobile'] :'';?></p><p></p>
            </div>
			
			<div class="heading"><?php echo $sch['scheme_name'].'-'.$account['code']?></div>
        </div>

<!--cus details-->
        <div class="" >
				
            <div class="" style="width:50%;display:inline-block;padding-top:30px;">
                <table class="chit_customer_details" >					
                    <tr>
                        <th><span style="width:40%;">Address</span></th>
                        <td style=""><span> <?php echo $sch['add1'].", ".$sch['add2'].", ".$sch['add3'].",<br>".$sch['city']." - ".$sch['pincode']." <br>".$sch['state']; ?></span></td>	
                        <td></td>			
                                            
                                            
                    </tr>	
                    <tr>						
                        <th style=""><span>Branch</span></th>						
                        <td style=""><span><?php echo $sch['branch_name']?></span></td>		
                        <td></td>			
                                            
                    </tr>			
                </table>
            </div>
            <div style="width:50%;display:inline-block;margin-top:0px;">
                <table class="chit_customer_details" style="" align="right">
                    <tr>
                        <th><span>Customer Name  </span></th>
                        <td><span> <?php echo $sch['customer_name']?></span></td>
                    </tr>
                    <tr>
                        <th><span >Mobile  </span></th>
                        <td><span><?php echo $sch['mobile']?></span></td>
                    </tr>
                    <tr>
                        <th><span >A/c Name</span></th>
                        <td><span><?php echo $sch['account_name']?></span></td>
                    </tr>
                    <tr>
                        <th><span >A/c No</span></th>
                        <td><span><?php echo $sch['scheme_acc_number']?></span></td>
                	</tr>
                    <tr>
                        <th><span >Paid Installments</span></th>
                        <td><span><?php echo $account['paid_installments'].'/'.$account['total_installments']?></span></td>
                    </tr>
                    </tr>
                </table>
            </div>
		</div>
        
<!--payment details-->
         

         <table class="chit_details" style="page-break-inside: auto;width: 100%;table-layout:fixed;">
            <tr>
                <th colspan="6" style="text-align:center">Chit starting date : <?php echo date("d-m-Y", strtotime($account['start_date']))?></th>
                <th colspan="6" style="text-align:center">Maturity Date: <?php echo ($account['maturity_date'] != null ? date("d-m-Y", strtotime($account['maturity_date'])) : '-' )?> </th>
            </tr>
            <tr>
                <th colspan="6" style="text-align:center">Benefit Type: <?php echo ($benefit['type'] == 1 ? 'Bonus' : 'Interest'); ?></th> 
                <th colspan="6" style="text-align:center">Benefit Value: <?php echo ($benefit['type'] == 1 ? 'INR' : ''); ?> <?php echo $benefit['interest_val']; ?></th>
            </tr>
            
         </table>

         <table class="chit_details" style="margin-top:1px;page-break-inside: auto;width: 100%;table-layout:fixed;">
         
                    
				<thead>
					<tr>
						<th width="5%"  style="text-align:center">Ins No</th>
						<th width="14%" style="text-align:center">Paid date</th>
						<th style="text-align:center">Amount Paid (INR)</th>
   
                    <?php if($account['sch_typ']== 1 || $account['sch_typ']== 2 || ($account['sch_typ']==3 && (($account['flexible_sch_type']==2 && ($account['wgt_convert'] == 0 || $account['wgt_convert'] == 1)) || $account['flexible_sch_type']==3 || $account['flexible_sch_type']==4 || $account['flexible_sch_type']==5 || $account['flexible_sch_type']==7))) {  ?>
                        <th style="text-align:center">Board Rate (INR)</th>
						<th style="text-align:center">Saved weight (in grms)</th>
                    <?php } ?>

						<th style="text-align:center">Difference days</th>

                    <?php if($benefit['type'] == 2){ ?> 
						<th style="text-align:center"> Benefit <?php if($account['is_weight'] == 0){echo '(INR)'; }else{echo '(grm)'; } ?></th>
                    <?php } ?>

						<th style="text-align:center">Receipt No</th>
                    
                    <?php if($account['installment_cycle'] == 2){ ?>    
                        <th  width="14%" style="text-align:center">Due Date</th>
						<th  width="14%" style="text-align:center">Grace Date</th>
                        <th style="text-align:center">Due Limit Crossed</th>
					<?php } ?>	
					</tr>
				</thead>
				<tbody>
                <?php foreach($payData as $pay){
                    $total_interest += $pay['pay_int'];
                    $total_amt += $pay['paid_amt'];
                    $total_wgt += $pay['metal_weight'];
                ?>    
                    <tr>
                        <td width="1%" style="text-align:center"><?php echo $pay['installment']?></td>
                        <td width="14%" style="text-align:center"><?php echo $pay['paid_date']?></td>
                        <td style="text-align:center"><?php echo $pay['paid_amt']?></td>
    
                    <?php if($account['sch_typ']== 1 || $account['sch_typ']== 2 || ($account['sch_typ']==3 && (($account['flexible_sch_type']==2 && ($account['wgt_convert'] == 0 || $account['wgt_convert'] == 1)) || $account['flexible_sch_type']==3 || $account['flexible_sch_type']==4 || $account['flexible_sch_type']==5 || $account['flexible_sch_type']==7))) {  ?>
                        <td style="text-align:center"><?php echo $pay['metal_rate']?></td>
                        <td style="text-align:center"><?php echo $pay['metal_weight']?></td>
                    <?php } ?>
                        

                        <td style="text-align:center"><?php echo $pay['days_diff']?></td>

                    <?php if($benefit['type'] == 2){ ?> 
                        <td style="text-align:center"><?php echo $pay['pay_int']?></td>
                    <?php } ?>

                        <td style="text-align:center"><?php echo $pay['receipt_no']?></td>
                    
                    <?php if($account['installment_cycle'] == 2){ ?>    
                        <td  width="14%" style="text-align:center"><?php echo $pay['due_date']?></td>
                        <td  width="14%" style="text-align:center"><?php echo $pay['grace_date']?></td>
                        <td style="text-align:center"><?php echo $pay['is_limit_exceed']?></td>
                    <?php } ?>	
                    
                    </tr>
                <?php }  ?> 

                    <tr height="5%">
                        <th colspan="2" style="text-align:center">Total</th>
                        <th style="text-align:center"><?php echo $total_amt; ?></th>
    
                    <?php if($account['sch_typ']== 1 || $account['sch_typ']== 2 || ($account['sch_typ']==3 && (($account['flexible_sch_type']==2 && ($account['wgt_convert'] == 0 || $account['wgt_convert'] == 1)) || $account['flexible_sch_type']==3 || $account['flexible_sch_type']==4 || $account['flexible_sch_type']==5 || $account['flexible_sch_type']==7))) {  ?>
                        <th style="text-align:center"></th>
                        <th style="text-align:center"><?php echo $total_wgt; ?></th>
                    <?php } ?>
                        
                        <th style="text-align:center">&nbsp;&nbsp;</th>

                    <?php if($benefit['type'] == 2){ ?> 
                        <th style="text-align:center"><?php echo $total_interest; ?></th>
                    <?php } ?>

                        <th style="text-align:center">&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    
                    <?php if($account['installment_cycle'] == 2){ ?>    
                        <th  width="14%" style="text-align:center"></th>
                        <th  width="14%" style="text-align:center"></th>
                        <th style="text-align:center"></th>
                    <?php } ?>	
                        
                    </tr>
                
                
                </tbody>
			</table>

        

    </div>

    <div style="margin-top:70px;font-size:12px;">
    <p>Print Taken On : <?php echo date('d-m-Y h:i:s A'); ?>
    <br/>Employee : <?php echo $this->session->userdata('username'); ?> </p>

    </div>
</body>

</html>