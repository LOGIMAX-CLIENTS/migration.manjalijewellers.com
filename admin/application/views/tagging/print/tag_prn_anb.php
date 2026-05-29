<?php
$data = $tag; 

$mrp = "";
if(isset($data['sales_mode']) && $data['sales_mode'] == 1 && isset($data['sales_value']) && $data['sales_value'] != "" ) {
    $mrp = 'A87,114,0,1,1,1,N,"Rs.'.round($data['sales_value'], 0)."\"\r\n";
}

$stn_wt = "";
if(isset($data['stn_wt']) && $data['stn_wt'] > 0) {
    $stn_wt = "A37,235,0,2,1,1,N,\"S.Wt:\"\r\n" .
                "A104,235,0,2,1,1,N,\"".number_format($data['stn_wt'],2)."".$data['stuom_short_code']."\"\r\n" ;
}

$code_karigar = "";
if(isset($data['code_karigar']) && $data['code_karigar'] != '') {
    $code_karigar = "A210,132,3,1,1,1,N,\"".$data['short_code']."\"\r\n";
} 

$size = "";
if(isset($data['id_size']) && $data['id_size'] > 0 && $mrp == '') {
    $size = "A87,94,0,2,1,1,N,\"SIZE:\"\r\n" .
            "A134,94,0,2,1,1,N,\"".$data['size']."\"\r\n";
} else if(isset($data['id_size']) && $data['id_size'] > 0 && $mrp != '') {
    $size = "A87,94,0,2,1,1,N,\"SIZE:\"\r\n" .
            "A150,94,0,2,1,1,N,\"".$data['size']."\"\r\n";
}

$narration = "";
if(isset($data['narration']) && $data['narration'] != '') {
    $narration = "A87,74,0,2,1,1,N,\"".$data['narration']."\"\r\n";
}

$tag_code ="";
if(isset($data['id_metal']) && ($data['id_metal'] == 1 || $data['id_metal'] == 3)) {
    $tag_code = "A34,135,0,2,1,1,N,\"".$data['tag_code']."\"\r\n";
}

$silver_tag ="";
if(isset($data['id_metal']) && $data['id_metal'] == 2 && $mrp == '') {
    $silver_tag = "A34,135,0,2,1,1,N,\"".$data['tag_code']."\"\r\n";
}

$silver_mrp_tag ="";
if(isset($data['id_metal']) && $data['id_metal'] == 2 && $mrp != '') {
    $silver_mrp_tag = "A34,135,0,2,1,1,N,\"".$data['tag_code']."\"\r\n";
}

$printer_code = "CLS\r\n".
                "I8,1,001\r\n" .
                "Q288,32+0\r\n" .
                "q559\r\n" .
                "H13\r\n" .
                "ZB\r\n" .
                "R0,0\r\n" .
                "N\r\n" .


                "b36,65,QR,0,0,o0,r2,m4,g1,s8,\"".$data['tag_code']."\"\r\n" .

                $silver_tag.

                $narration.

                $silver_mrp_tag.


                $size.

            
                $mrp.

                $tag_code .


                $code_karigar.

                "A37,172,0,2,1,1,N,\"".$data['product_name']."\"\r\n" .
                "A37,195,0,2,1,1,N,\"G.Wt:\"\r\n" .
                "A104,195,0,2,1,1,N,\"".number_format($data['gross_wt'],3)."gm\"\r\n" .

                "A37,215,0,2,1,1,N,\"N.Wt:\"\r\n" .
                "A104,215,0,2,1,1,N,\"".number_format($data['net_wt'],3)."gm\"\r\n" .


                $stn_wt.


                "RF1,0,0,12,1,\"422D36353137390000000000\"\r\n" .

                "W1,1\r\n".
                "PRINT 1";

echo $printer_code;
