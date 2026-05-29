<?php
$data = $tag; 
$mrp = "";

if($data['sales_mode'] == 1 && $data['sales_value'] != "" ) {

    $mrp_val = round($data['sales_value'], 0);
    $mrp_str = (string)$mrp_val;
    $len = strlen($mrp_str);
    if($len > 3) {
        $last3 = substr($mrp_str, -3);
        $remaining = substr($mrp_str, 0, -3);
        $remaining = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $remaining);
        $mrp_formatted = $remaining . "," . $last3;
    } else {
        $mrp_formatted = $mrp_str;
    }

    $mrp = '^FO585,75^A0N,20,22^FDMRP:Rs '.$mrp_formatted.'^FS'."\r\n";


}

$grs_wt = "";

if($data['gross_wt'] > 0 ){
    $grs_wt = '^FO585,75^A0N,21,25^FDg.wt: '.$data['gross_wt'].'g^FS'. "\r\n";
}

$stone_wt = "";

if($data['stn_wt'] > 0) {

    $stone_wt = '^FO585,100^A0N,18,20^FDst.wt: '.$data['stn_wt'].$data['stuom_short_code'].'^FS'. "\r\n";

}

$dia_wt = "";

if($data['dia_wt'] > 0) {

    $dia_wt = '^FO585,100^A0N,20,22^FDd.wt: '.number_format($data['dia_wt'], 2).$data['dia_uom_short_code'].'^FS'. "\r\n";

}

$design = '^FO585,45^A0N,20,22^FD'.$data['design_name'].'^FS'. "\r\n" ;

if($data['stn_wt'] > 0 && $data['dia_wt'] > 0){
    $grs_wt = '^FO585,57^A0N,20,24^FDg.wt: '.$data['gross_wt'].'g^FS'. "\r\n";
    $stone_wt = '^FO585,80^A0N,18,20^FDst.wt: '.$data['stn_wt'].$data['stuom_short_code'].'^FS'. "\r\n";
    $dia_wt = '^FO585,100^A0N,18,20^FDd.wt: '.number_format($data['dia_wt'], 2).$data['dia_uom_short_code'].'^FS'. "\r\n";
    $design = '^FO585,35^A0N,19,20^FD'.$data['design_name'].'^FS'. "\r\n";
}

$size = "";
if($data['size'] != "") {
    $size = '^FO380,90^A0N,18,20^FDSZ-'.$data['size'].'^FS'. "\r\n";
}


$branch_code = "";
if($data['sales_mode'] == 1) {
    $branch_code = '^FO450,48^A0B,20,25^FD '.$data['brnc_scode'].$data['metal_short_code'].'^FS'. "\r\n";
}else{
    $branch_code = '^FO460,48^A0B,20,25^FD '.$data['brnc_scode'].$data['metal_short_code'].'^FS'. "\r\n";
}

$tag_code1 = "";
if($data['sales_mode'] == 1) {
    $tag_code1 = '^FO480,35^BQN,3,3^FDLA,'.$data['tag_code'].'^FS'. "\r\n";
}else{
    $tag_code1 = '^FO490,35^BQN,3,3^FDLA,'.$data['tag_code'].'^FS'. "\r\n";
}

$wastage = "";
if($data['retail_max_wastage_percent'] > 0 && $data['sales_mode'] != 1 && $data['metal_id'] == 1) {
    $wastage_percent = (float)$data['retail_max_wastage_percent'];
    $integer_part = (int)$wastage_percent;
    $decimal_part = round($wastage_percent - $integer_part, 2);

    $map = ['Z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
    $alphabetical_wastage = "";
    $digits = str_split((string)$integer_part);
    foreach ($digits as $digit) {
        if (isset($map[$digit])) {
            $alphabetical_wastage .= $map[$digit];
        }
    }

    if ($decimal_part >= 0.65) {
        $alphabetical_wastage .= "+";
    }

    $wastage = '^FO725,90^A0N,20,22^FD'.$alphabetical_wastage.'^FS'. "\r\n";
}



$printer_code = '^XA'. "\r\n" .

                '^LH001,00'. "\r\n" .

                '^MD10'. "\r\n" .

                '^FO380,13^A0N,18,20^FD'.$data['product_name'].'^FS'. "\r\n" .

                $size.

                $branch_code.

                $tag_code1.
                
                $grs_wt.

                $stone_wt.

                $dia_wt.
                
                '^FO585,15^A0N,20,24^FD'.$data['tag_code'].'^FS'. "\r\n" .
                
                $design.

                $mrp.
                
                $wastage.

                '^PQ1'. "\r\n" .
                '^XZ'. "\r\n";

echo $printer_code;