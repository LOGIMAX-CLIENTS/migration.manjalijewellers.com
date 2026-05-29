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
    #footer { bottom:75px;position: fixed; border-top:1px solid gray;} .pagenum:before { content: counter(page); } </style>
</head>
<body>
    <?php
        function moneyFormatIndia($num) {
            return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
        }
    ?>
    <span class="PDFReceipt"> 
        <div class="hare_krishna"></div>
        
        <div style="width: 100%;">
           
            <div style="font-size:12px !important;display: inline-block; margin-top: 115px; text-align:left;">
                <label></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b style="margin-left:220px;text-align:right;"><?php echo $customer[0]['receipt_no']; ?></b>&nbsp;<b style="margin-left:110px;"><?php echo $customer[0]['date_payment']; ?></b>
            <br>
            <br>
           

			<p style="font-size:11px !important;display: inline-block; width: 100%; text-align:left;margin-left:200px;">
                <label></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo 'Mr./Ms.'.$customer[0]['name']; ?></b><br>
              
                <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo (isset($customer[0]['address1']) && $customer[0]['address1']!='' ? "<label>".$customer[0]['address1'].",</label>" :''); ?>
                <?php echo (isset($customer[0]['address2']) && $customer[0]['address2']!='' ? "<label>".$customer[0]['address2'].", </label><br>" :'<br>'); ?>
                <?php echo (isset($customer[0]['address3']) && $customer[0]['address3']!='' ? "<label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$customer[0]['address3'].", </label><br>" :'<br>'); ?>
                
                <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo ($customer[0]['city']!='' ? "<label>".$customer[0]['city'].($customer[0]['pincode']!='' && strlen($customer[0]['pincode']) == 6? ' - '.$customer[0]['pincode'].'.' :'-')."</label><br>":'<br>'); ?></br></br>
               <!-- <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><?php echo $customer[0]['state']!='' ? "<label>".$customer[0]['state']." ".$customer[0]['country']."</label><br>" : (isset($customer[0]['country']) ? "<label> ".$customer[0]['country']."</label>": '' ) ?>-->
                <br>
	            <label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><b style="letter-spacing:1.5px;"><?php echo $customer[0]['scheme_acc_number'];?></b><b style="margin-left:120px;"><?php echo $customer[0]['paid_installments'];?></b><br><br>
	           <p style='margin-top:83px;margin-left:200px;'><label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><b style="letter-spacing:1.5px;"><?php echo $customer[0]['payment_amount'];?></b>
	            <label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label><b style="letter-spacing:1.5px;"><?php echo $customer[0]['payment_mode'];?></b>
			    <?php 
			    if($customer[0]['type'] == 0 || $customer[0]['type'] == 2){ 
			        echo '<b style="font-size:12px!important;letter-spacing:1.5px;">'.$customer[0]['amount'].'</b>';
			    }
			    ?>
			    </p>
			</p>
			</div>
		</div>

        <!--<div id="footer"></div>-->
    </span>          
</body></html>