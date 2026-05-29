<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qr - Other Inventory</title>

    <style>
    @page {
        size: 45mm 45mm;
        margin: 0;
    }

    @media print {
        .single {
            page-break-inside: avoid !important;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
        }

        img {
            height: 120px;
            width: 120px;
        }

        .a1 {
            margin-top: -1px;
        }

        
        .a1 label {
            display: block;
            margin-bottom: -3px; /* Adjust the margin as needed */
        }

        .a1 img {
            display: block;
            margin: 0 auto;
        }
    }
</style>
    
</head>
<body>
    <?php
    // echo '<pre>';print_r($data);
    foreach($data as $d){    ?>

        <div class="single">
        <div class="a1">
            <label><?php echo $d['pro_name'];?></label>
            <img src="<?php echo $d['qrcode']?>" alt=""><label for="">Ref No: <?php echo $d['item_ref_no'];?></label>
        </div>
    </div>

     
    
    <?php } ?>
</body>
<script>
    this.print();
    
</script>
</html>