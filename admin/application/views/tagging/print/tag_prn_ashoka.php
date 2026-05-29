<?php
$data = $tag; 

$product_name = "";
if(isset($data['product_name']) && $data['product_name'] != '') {
    $product_name = "^FO30,12^A0N,18,20^FD".substr($data['product_name'], 0, 11)."^FS". "\r\n\r\n";

}

$mrp = "";
if((isset($data['sales_mode']) && $data['sales_mode'] == 1 && isset($data['sales_value']) && $data['sales_value'] != "" ) || $data['calculation_based_on'] == 4) {
    $sales_value = round($data['sales_value'], 0);
    $mrp = '^FO230,40^A0N,18,20^FDMRP: '.$sales_value.'^FS'."\r\n\r\n";
}


$grs_wt ="";
if(isset($data['gross_wt']) && $data['gross_wt'] > 0 && $data['calculation_based_on'] != 3) {
    $grs_wt = "^FO230,12^A0N,18,20^FDG.Wt: ".$data['gross_wt']." Gm^FS"."\r\n\r\n" ;
}

$net_wt = "";
if(isset($data['net_wt']) && $data['net_wt'] > 0 && $mrp == "" && ($data['stn_wt'] > 0 || $data['dia_wt'] > 0)) {
    $net_wt = "^FO230,40^A0N,18,20^FDN.Wt: ".$data['net_wt']." Gm^FS"."\r\n\r\n";
}

$dia_wt = "";
if(isset($data['dia_wt']) && $data['dia_wt'] > 0 && $mrp == "") {
    $dia_wt = "^FO230,70^A0N,18,20^FDD.Wt: ".$data['dia_wt'].$data['dia_uom_short_code']."^FS"."\r\n\r\n";
}
$huid = "";
if(isset($data['hu_id']) && $data['hu_id'] != '' && $mrp == "" && $dia_wt == "" && $data['metal_id'] == 1) {
    if($data['stn_wt'] > 0) {
        $y = 70;
    } else {
        $y = 50;
    }
    $huid_parts = explode(', ', $data['hu_id']);
    $huid_val = implode('/', array_slice($huid_parts, 0, 2));
    $huid = "^FO230,".$y."^A0N,18,20^FD".$huid_val."^FS"."\r\n\r\n";
}

$purity = "";
if(isset($data['purity_description']) && $data['purity_description'] != '' && $mrp == "") {
    $purity = "^FO30,40^A0N,18,20^FD".$data['purity_description']."^FS"."\r\n\r\n";
}

$design = "";
if(isset($data['design_name']) && $data['design_name'] != '') {
    $design = "^FO30,74^A0N,18,20^FD".substr($data['design_name'], 0, 17)."^FS"."\r\n\r\n";
}

$size = "";
if(isset($data['size']) && $data['size'] != ''){
    $size = "^FO144,95^A0N,20,24^FDSz: ".$data['size']."^FS"."\r\n\r\n";
}

$wastage = "";
if(isset($data['retail_max_wastage_percent']) && $data['retail_max_wastage_percent'] > 0 && $mrp == "") {
    $wastage_value = round($data['retail_max_wastage_percent'], 0);
    $wastage = "^FO230,96^A0N,18,20^FDW s : ".$wastage_value."%^FS"."\r\n\r\n";
}

$mc = "";
if(isset($data['tag_mc_value']) && $data['tag_mc_value'] > 0 && $mrp == "") {
     $mc_value = round($data['tag_mc_value'], 0);
    $mc = "^FO320,96^A0N,18,20^FDMc: ".$mc_value."^FS"."\r\n\r\n";
}
$vendar_code = "";
if(isset($data['vendar_code']) && $data['vendar_code'] != '') {
    $vendar_code = "^FO390,8^A0R,18,19^FD".$data['brnc_scode'].' - '.$data['vendar_code']."^FS"."\r\n\r\n";    
}


$category = "";
if(isset($data['category_name']) && $data['category_name'] != '' && ($data['calculation_based_on']==3 || $data['calculation_based_on']==4 || $data['metal_id'] == 2)) {
    $category = "^FO30,40^A0N,18,20^FD".substr($data['category_name'], 0, 11)."^FS"."\r\n\r\n";

}

$printer_code = '^XA'."\r\n".
                '^LH001,00'."\r\n".
                '^MD10'."\r\n\r\n".

                $product_name.             
                
                $grs_wt.
                
                $mrp.

                $category.

                $net_wt.

                $dia_wt.

                $huid.

                $purity.

                $design.

                '^FO30,95^A0N,20,24^FD'.$data['tag_code'].'^FS'. "\r\n" .

                $size.

                $wastage.

                $mc.

                $vendar_code.

                '^FO144,8^BQN,2,3^FDLA,'.$data['tag_code'].'^FS'. "\r\n\r\n" .

                '^PQ1'. "\r\n" .
                '^XZ'. "\r\n" ;

echo $printer_code;
