<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Bond</title>
    <style>
        body {
            margin: 0;
            font-size: 12pt;
        }

        .no-print {
            display: none;
        }

        .wholeContainer {
           position: relative;
            top: 4cm; 
            left: 1cm;
            width: 16.5cm; 
        }

        .gapInRight {
            text-align: right;
            margin-bottom: -.2cm;
        }

        .contactDetails {
            float: left;
        }

        .contactDetails p {
            margin: 0;
            padding: 0;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .headerForBond {
            font-weight: bold;
        }

        .mainBondBox {
            /* margin-top: -15; */
            border: 2px solid #000;
            text-align: justify;
            min-height: 9cm;
            padding: .3cm;
        }

        .mainbox {
            text-align: left;
            margin-top: -.4cm;
            padding-top: 0;
        }


        .mainbox p {
            margin-bottom: 0.6cm;
            line-height: .6cm;
        }


        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="wholeContainer">
        <p class="gapInRight">Date: <?= $paymentDate ?></p>
        <p class="gapInRight">GOLD 22KT: INR <?= $goldRate ?></p>
        <p class="gapInRight">SILVER 1GM: INR <?= $silverRate ?></p>

        <div class="contactDetails">
            <p><?= $customerName ?></p>
            <p><?= ucwords($customerAddress1) ?></p>
            <p><?= ucwords($customerAddress2)?></p>
            <p><?= $city ?>(DT)</p>
            <p>Mobile:<?= $customerMobile?></p>
            <p>PAN No:<?= $customerPan?></p>
            <p>Maturity Date : <?= $maturityDate ?></p>
        </div>

        <div class="clearfix"></div>

        <p class="headerForBond"><?= $metalName?> DEPOSIT CHIT BOND</p>
        <div class="mainBondBox">
            <div class="mainbox">
                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;WE HAVE RECIVED YOUR <?= $paymentMode ?> <?=!empty($payment_ref_number) ? "(".$payment_ref_number.")" : "" ?> TRANSFER OF Rs. <?= $payment_amount ?>. THE EQUIVALENT WEIGHT OF <?= $metalName?> OF THIS AMOUNT HAS BEEN DEPOSITED IN THE  <?= $paymentWeight ?> GM <?= $schemeName?>.YOU WILL GET THE BENEFIT OF NEW <?=$metalName?> JEWELLERY OF THE SAME WEIGHT WITHOUT WASTAGE AFTER <?= $maturity ?> MONTHS FROM THE DATE OF DEPOSIT. ANY <?= $metalName ?> ITEMS TAKEN IN EXCESS OF THAT WILL BE COLLECTED TO 10% WASTAGE AND GST APPLICABLE AT THE TIME OF DELIVERY.</p>
                <p style="text-align: center;">THANKING YOU</p>
                <p>Yours <br>
                <?= $companyName?>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
<!-- <script>
    window.print();
</script> -->