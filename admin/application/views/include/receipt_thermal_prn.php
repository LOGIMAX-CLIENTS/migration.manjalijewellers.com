SIZE 24. mm, 20 mm
GAP 3 mm, 0 mm
SPEED 1
DENSITY 18
DIRECTION 0,0
REFERENCE 0,0
OFFSET 0 mm
SHIFT 0
SET PEEL OFF
SET CUTTER OFF
SET TEAR ON
CLS
CODEPAGE 850
TEXT 190,140,"ROMAN.TTF",180,2,7,"Rc: <?= $records[0]['receipt_no']?>"
TEXT 55,140,"ROMAN.TTF",180,2,7,"Ins: <?= intval($records[0]['installment']); ?>"
TEXT 190,116,"ROMAN.TTF",180,2,7,"Dt : <?= (date('d-m-Y', strtotime(str_replace("/", "-", $records[0]['date_payment'])))) ?>"
TEXT 190,88,"ROMAN.TTF",180,2,7,"<?=$records[0]['scheme_acc_number']?>"
<?php if ($records[0]['scheme_type'] != 0) : ?>
TEXT 190,60,"ROMAN.TTF",180,2,8,"Rs. <?= number_format($records[0]['payment_amount']) ?> Wt: <?= $records[0]['metal_weight']?>"
<?php else : ?>
TEXT 190,60,"ROMAN.TTF",180,2,8,"Rs. <?= number_format($records[0]['payment_amount']) ?>"
<?php endif; ?>
<?php if ($records[0]['scheme_type'] != 0) : ?>
TEXT 190,28,"ROMAN.TTF",180,2,8,"Rt. <?= intval($records[0]['metal_rate']) ?> T.W: <?= ($records[0]['running_weight'])?>"
<?php else : ?>
TEXT 190,28,"ROMAN.TTF",180,2,8,"T.Amt: <?= $records[0]['running_amount']?>"
<?php endif; ?>
PRINT 1,1
E
