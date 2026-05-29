<?php if($type == 1){?>

Hi <?php echo ucfirst($schData['msg']['firstname'])?>, Thanks for joining with <?php echo $company['company_name'] ?> -<?php echo $schData['msg']['scheme_name'] ?>.Your Scheme A/c No. is <?php echo $schData['msg']['scheme_acc_number']?> (<?php echo $schData['msg']['scheme_type']?>).Please proceed for the payment.

<?php } else if($type == 2){?>

Dear <?php echo ucfirst($schData['msg']['firstname']); ?>, Your <?php echo $company['company_name'] ?> saving scheme a/c No. <?php echo $schData['msg']['scheme_acc_number']?> was closed on <?php echo date('d-m-Y') ?>.Closing Balance is <?php echo ($schData['msg']['scheme_type']=='Amount'?"Rs. ".$schData['closing_balance'] : number_format($schData['closing_balance'],'3','.','') ." g")?>.For details contact:<?php echo $company['phone']?>

<?php }else{?>
Hi <?php echo ucfirst($schData['msg']['firstname']); ?>, Your <?php echo $company['company_name'] ?> saving scheme A/c No. <?php echo $schData['msg']['scheme_acc_number'] ?> has been reverted successfully.For details contact:<?php echo $company['phone']?>
 
 <?php }?>




