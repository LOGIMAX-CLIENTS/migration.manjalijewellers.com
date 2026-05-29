<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Receipt</title>
		<link rel="stylesheet" href="<?php echo base_url();?>assets/css/cus_receipt.css">
		<!--	<link rel="stylesheet" href="<?php echo base_url();?>assets/css/receipt_temp.css">-->
		
	</head>
	<style>
	
	 @page { 
                size: 85mm 141.224mm;
                margin-top:30px !important;
                 border :1px solid red;
            }
            

	    .alignMe{

				/*margin-top:14px;*/
				font-size:15px !important;
				/*margin-left:-20px;*/
			}
	     .alignMe b {
              display: inline-block;
              width: 50%;
              position: relative;
              padding-right: 10px; /* Ensures colon does not overlay the text */
         }

        .alignMe b::after {
              content: ":";
              position: absolute;
              right: 10px;
              line-height: 1.4; 
        }
        ul {
              list-style: none; /* Remove default list style (bullet/number) */
              padding: 0; /* Remove any default padding */
              margin: 0; /* Remove any default margin */
        }
       body{
           
           padding:5;
            border:1px solid ;
            /*margin:14px;*/

       }
       .dashed-line {
      border-top: 2px dashed #000; /* Change the color as needed */
      width: 100%;
      margin: 6px 0; /* Adjust the margin as needed */
    }
	</style>
	<body class="margin">
	<div class="PDF_CusReceipt">
	    <div class="" style="font-weight: 400 !important; font-size:16px !important;" align="center">
			 <img style="margin-top:-12px;margin-left:-10px;" src="<?php echo base_url() ?>assets/img/logo.png" width="100" height="55">			
			</div>
			<?php 
			 $total_installment=$records[0]['total_installments'];
             $maturitydate = date('d-m-Y', strtotime("+".$total_installment." months", strtotime($records_sch['start_date'])));
		
            ?>
            <?php 		
            // print_r($comp_details);
              $com_name=$comp_details['name'];
            $com_name=explode(" ",$com_name);
        $com_add=$comp_details['address1'];
            $com_add=explode(" ",$com_add);
            // print_r($com_add);
 ?>
	<span style="margin-left: 30px; margin-top: 130px !important;font-weight:bold;"><?php echo empty($comp_details['company_name']) && !isset($comp_details['company_name']) ? $com_name[0].' '.$com_name[1].' '.$com_name[2] .' '.$com_name[3]: $comp_details['company_name']; ?></span><br/>
            <span style="margin-left: 65px;font-size:13px !important;"><?php echo $com_add[0] .' '. $com_add[1].' '. $com_add[2] ; ?></span><br/>
            <span style="margin-left: 45px;font-size:13px !important;" ><?php echo   $com_add[3] .' '.$com_add[4] .' '.$com_add[5]; ?></span>
            <span style="margin-left: 55px;font-size:14px !important;"><?php echo $comp_details['city'] . ' - ' . $comp_details['pincode'] . '.'; ?></span><br/>
            			<span><?php echo 'Receipt Date : '.$records[0]['date_payment'];?></span><br/><br/>
            				<!--<span><?php echo 'Maturity Date : '.$maturitydate;?></span><br/><br/>-->
            				<span><?php echo 'Maturity Date : '.$maturitydate;?></span><br/>



			<div class="" style="font-weight: 400 !important; font-size:16px !important;" align="center">
				
			</div>
			</br>
			<div class="" style="font-weight: 400 !important; font-size:16px !important;" align="center">
				<?php echo $records[0]['scheme_name'];?>
				<!--<hr style="border-top: dashed ;">				-->
				 <div class="dashed-line"></div>
			</div>
			<div>
			<ul class="alignMe">
              <li><b>MS no</b> <?php echo $records[0]['scheme_acc_number']; ?></li>
              <li><b>Receipt No</b> <?php echo $records[0]['receipt_no']; ?></li>
              <li><b>Installment No</b> <?php echo $records[0]['paid_installments']; ?></li>
              <!--<li><b>Gold Rate</b> <?php echo $records[0]['metal_rate']; ?></li>-->
              <li><b>Paid Amount</b> <?php echo "Rs ".$records[0]['payment_amount']; ?></li>
            
              <?php if($records['scheme_type'] != 0 && $records[0]['flexible_sch_type'] != 1 && $records[0]['flexible_sch_type'] != 6 ): ?>
                <li><b>Paid Weight</b> <?php echo $records[0]['metal_weight'].' g'; ?></li>
                <li><b>Running Weight</b> <?php echo $records[0]['total_weight'].' g'; ?></li>
              <?php endif; ?>
            
              <li><b>Mode</b>
                <?php if($records[0]['multi_modes'] == ''): ?>
                  <?php echo $records[0]['payment_mode']; ?>
                  <!--<?php echo number_format((float)($records[0]['payment_amount']-$records[0]['discountAmt'].' INR '),2, '.', ''); ?>-->
                <?php else: ?>
                  <?php echo $records[0]['multi_modes']; ?>
                <?php endif; ?>
              </li>
              
              <li><b>Mobile</b> <?php echo $records[0]['mobile']; ?></li>
</ul>

			</div>
			<div>
				<?php if($records[0]['due_type']=='D') { ?>
					<p align="left">Paid by <?php echo $comp_details['company_name']; ?> for <br/>Mr/Mrs. <?php echo $records[0]['firstname'].(isset($records[0]['firstname'])?', ':''); ?></p>
					<div align="left"><span data-prefix><?php echo $comp_details['currency_symbol']; ?> </span><span style="font-weight: 400 !important; font-size:16px !important;"><?php echo $records[0]['payment_amount']; ?></span></div>
				<?php }else{ ?>
					<p align="left">Received with thanks from <br/>Mr/Mrs. <?php echo $records[0]['firstname'].(isset($records[0]['firstname'])?', ':''); ?></p>
					<div align="left">

						<span ><?php echo $comp_details['currency_symbol']; ?> </span><span style="font-weight: 400 !important; font-size:16px !important;"><?php echo $records[0]['payment_amount']; ?></span>
</div>
				<!-- </tr></span> -->
				<?php } ?>
				
				<p style="font-size:12px !important;"><?php echo ucwords($records[0]['amount_in_words']); ?> Only</p>
							
				<!--<p></p><br/>-->
				<div style="float:right;font-style:italic;font-size:11px;">This is system generated receipt and does not require signature.</div><br/><br/>
			</div>
		</div>
		<script type="text/javascript"> 
		this.print(); 
		</script> 
	</body>
	
</html>