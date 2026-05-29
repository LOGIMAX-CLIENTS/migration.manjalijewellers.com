SIZE 24 mm, 20 mm
SPEED 3
DENSITY 9
DIRECTION 0,0
REFERENCE 0,0
OFFSET 0 mm
SET PEEL OFF
SET CUTTER OFF
SET PARTIAL_CUTTER OFF
SET TEAR ON
CLS
CODEPAGE 1252
TEXT 190,148,"ROMAN.TTF",180,2,7,"<?= $customer['account_name']?>"
TEXT 190,125,"ROMAN.TTF",180,2,7,"<?= $customer['scheme_acc_number']?>"
TEXT 190,101,"ROMAN.TTF",180,2,7,"End Date <?= $customer['maturity_date']?>"
TEXT 190,32,"ROMAN.TTF",180,2,7,"<?= $customer['mobile']?>"
QRCODE 79,79,L,3,A,180,M2,S7,"<?= $customer['id_scheme_account']?>"
PRINT 1,1
