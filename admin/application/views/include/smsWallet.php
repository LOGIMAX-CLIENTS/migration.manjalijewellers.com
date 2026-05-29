<?php if($type == 1){?>

Hi <?php echo ucfirst($msg['msg']['name'])?>, wallet account <?php echo $msg['wallet_acc_number'] ?> activated for your mobile number.

<?php } else{?>

Dear <?php echo ucfirst($msg['msg']['name'])?>, <?php echo $msg['value'].' '.$msg['transaction_type']==0?' credited to ':' debited from ' ?> your wallet.
 
 <?php }?>




