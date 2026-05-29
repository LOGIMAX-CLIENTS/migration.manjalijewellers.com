<?php
$cheques = array();
foreach($payment as $p) {
    if($p['payment_mode'] == 'CHQ') {
        $cheques[] = $p;
    }
}
if(empty($cheques)) {
    echo "No Cheque Details Found";
    exit;
}
$this->load->model('ret_billing_model');
if (!function_exists('moneyFormatIndia')) {
    function moneyFormatIndia($num) {
        $explrestunits = "";
        if(strlen($num)>3){
            $lastthree = substr($num, strlen($num)-3);
            $restunits = substr($num, 0, strlen($num)-3); 
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++){
                if($i==0) {
                    $explrestunits .= (int)$expunit[$i].","; 
                } else {
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $thecash = $explrestunits.$lastthree;
        } else {
            $thecash = $num;
        }
        return $thecash; 
    }
}
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cheque Print</title>
    <style>
        @page { margin: 0; size: 576pt 252pt; }
        body { margin: 0; padding: 0; font-family: 'Courier New', Courier, monospace; font-size: 14px; color: #000; line-height: 1; }
        .cheque-leaf {
            position: relative;
            width: 576pt;
            height: 250pt; /* Reduced slightly to prevent sub-pixel overflow */
            overflow: hidden;
        }
        .page-break { page-break-before: always; }
        .date-container { position: absolute; top: 22pt; right: 48pt; font-size: 16pt; font-weight: bold; letter-spacing: 14pt; word-spacing: 0; }
        .payee { position: absolute; top: 62pt; left: 75pt; font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .amount-words { position: absolute; top: 92pt; left: 50pt; width: 320pt; line-height: 24pt; font-size: 12pt; font-weight: bold; text-transform: capitalize; }
        .amount-figures { position: absolute; top: 12pt; right: 40pt; font-size: 16pt; font-weight: bold; border: 0px solid #000; padding: 2pt 5pt; }
        .account-payee { position: absolute; top: 25pt; left: 15pt; border-top: 1.5pt solid #000; border-bottom: 1.5pt solid #000; padding: 3pt 8pt; transform: rotate(-15deg); font-size: 10pt; font-weight: bold; text-align: center; }
        .amount-in-first { position: absolute; top: 100pt; right: 50pt; font-size: 12pt; font-weight: bold; text-transform: capitalize; }
        .account-number { position: absolute; top: 140pt; left: 100pt; font-size: 14pt; font-weight: bold; }
    </style>
</head>
<body><?php foreach($cheques as $index => $cheque): 
    $date_val = str_replace('-', '', $cheque['cheque_date']);
    $date_arr = str_split($date_val);
    $amount = (float)$cheque['payment_amount'];
    $amt_words = $this->ret_billing_model->no_to_words($amount);
    $payee = (!empty($cheque['payee_name']) ? $cheque['payee_name'] : $paymentdetails['paydetails']['karigar']);
?><div class="cheque-leaf <?php echo ($index > 0 ? 'page-break' : ''); ?>">
<div class="account-payee">A/C PAYEE ONLY</div>
<div class="account-number"><?php echo (!empty($cheque['acc_number']) ? $cheque['acc_number'] : ''); ?></div>
<div class="date-container"><?php echo implode('', $date_arr); ?></div>
<div class="payee"> <?php echo strtoupper($payee); ?> </div>
<div class="amount-words"><div> <?php echo $amt_words; ?> ONLY  </div></div>
<div class="amount-in-first"><?php echo number_format($amount, 2); ?> /-</div>
</div><?php endforeach; ?></body></html><?php // No trailing newline allowed here ?>
