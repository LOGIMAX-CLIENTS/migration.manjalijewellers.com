<?php
if (!isset($tag) || !is_array($tag)) {
    return;
}
$data = $tag; 

$tag_code = isset($data['tag_code']) ? $data['tag_code'] : "";
$gross_wt = (isset($data['gross_wt']) && $data['gross_wt'] > 0) ? $data['gross_wt'] : "0.00";
$net_wt = (isset($data['net_wt']) && $data['net_wt'] > 0) ? $data['net_wt'] : "0.00";
$stn_amt = (isset($data['stn_amount']) && $data['stn_amount'] > 0) ? round($data['stn_amount']) : "0";
$ot_chrg = (isset($data['charge_amount']) && $data['charge_amount'] > 0) ? round($data['charge_amount']) : "0";
$purity = (isset($data['purity_name']) && $data['purity_name'] > 0) ? number_format($data['purity_name'], 2, '.', '') : "";
$karigar_code = isset($data['id_karigar']) && !empty($data['id_karigar']) ? $data['id_karigar'] : (isset($data['code_karigar']) ? $data['code_karigar'] : "");
$wastage = (isset($data['retail_max_wastage_percent']) && $data['retail_max_wastage_percent'] > 0) ? round($data['retail_max_wastage_percent']) : "0";
$mc_value = (isset($data['tag_mc_value']) && $data['tag_mc_value'] > 0) ? $data['tag_mc_value'] : "0";
$sec_short_code = isset($data['ss_code']) ? $data['ss_code'] : "";

$other_metals = isset($data['other_metal_details']) ? $data['other_metal_details'] : [];



$is_multi_metal = (!empty($other_metals));

$diamonds = [];
if (isset($data['stone_details']) && is_array($data['stone_details'])) {
    foreach ($data['stone_details'] as $stone) {
        if (!is_array($stone)) continue;
        // Use CT (Carat) as a proxy for diamonds if stone_type is missing, or check stone_code if it starts with D
        if ((isset($stone['uom_short_code']) && $stone['uom_short_code'] == 'CT') || 
            (isset($stone['stone_type']) && $stone['stone_type'] == 1)) {
            $diamonds[] = $stone;
        }
    }
}

$diamond_chunks = array_chunk($diamonds, 5);
if (empty($diamond_chunks)) {
    $diamond_chunks = [[]]; // At least one tag even if no diamonds
}

$final_printer_code = "";

foreach ($diamond_chunks as $chunk) {
    $printer_code = "^XA\r\n" .
                    "^PW800\r\n" .
                    "^LL120\r\n" .
                    "^LS0\r\n" .
                    "^LH0,0\r\n" .
                    "^MNY\r\n\r\n";
                    
    $printer_code .= "^FO10,6^A0N,21,21^FD" . $tag_code . "^FS\r\n";
    
    $main_metal_code = (isset($data['metal_short_code']) && !empty($data['metal_short_code'])) ? $data['metal_short_code'] : "";

    if ($is_multi_metal) {
        $printer_code .= "^FO10,35^A0N,20,20^FDGr WT:" . number_format($gross_wt, 2, '.', '') . "^FS\r\n";
        
        if ($net_wt > 0) {
            $printer_code .= "^FO10,57^A0N,20,20^FD" . $main_metal_code . " WT:" . number_format($net_wt, 2, '.', '') . "^FS\r\n";
        }
        
        if (isset($other_metals[0]) && $other_metals[0]['tag_other_itm_grs_weight'] > 0) {
            $om_code = !empty($other_metals[0]['metal_code']) ? $other_metals[0]['metal_code'] : "";
            $printer_code .= "^FO10,77^A0N,20,20^FD" . $om_code . " WT:" . number_format($other_metals[0]['tag_other_itm_grs_weight'], 2, '.', '') . "^FS\r\n";
        }
        
        if ($ot_chrg > 0) {
            $printer_code .= "^FO10,100^A0N,20,20^FDOT CH:" . $ot_chrg . "^FS\r\n\r\n";
        }
    } else {
        $printer_code .= "^FO10,35^A0N,20,20^FDG.WT:" . $gross_wt . "^FS\r\n";
        $printer_code .= "^FO10,57^A0N,20,20^FDN.WT:" . $net_wt . "^FS\r\n";
        
        if ($stn_amt > 0) {
            $printer_code .= "^FO10,77^A0N,20,20^FDSt.Amt:" . $stn_amt . "^FS\r\n";
        }
        if ($ot_chrg > 0) {
            $printer_code .= "^FO10,100^A0N,20,20^FDOT CH:" . $ot_chrg . "^FS\r\n\r\n";
        }
    }

    $printer_code .= "^FO160,5^A0N,20,20^FD" . $sec_short_code . "^FS\r\n\r\n" .
                    
                    "^FO145,18^BQN,2,3.4^FDMA," . $tag_code . "^FS\r\n\r\n" .
                    
                    "^FT250,100^A0B,19,19^FD" . $purity . " " . $karigar_code . "^FS\r\n\r\n";
                    
    if ($wastage > 0) {
        $printer_code .= "^FO280,5^A0N,19,19^FDVA:" . $wastage . "%^FS\r\n";
    }
    if ($mc_value > 0) {
        $printer_code .= "^FO350,5^A0N,19,19^FDMC:" . $mc_value . "^FS\r\n\r\n";
    }
    
    $printer_code .= "";
    
    $y_pos = 25;
    foreach ($chunk as $dia) {
        $stn_name = (isset($dia['stone_code']) && !empty($dia['stone_code'])) ? $dia['stone_code'] : "Di";
        $pieces = isset($dia['pieces']) ? $dia['pieces'] : "0";
        $wt = isset($dia['stn_wt']) ? $dia['stn_wt'] : "0.00";
        $uom = isset($dia['uom_short_code']) ? $dia['uom_short_code'] : "CT";
        $rate = (isset($dia['stn_rpg']) && $dia['stn_rpg'] > 0) ? round($dia['stn_rpg'], 0) : "0";
        
        $dia_line = $stn_name . " PC-" . $pieces . " Wt-" . $wt . $uom . "@" . $rate;
        $printer_code .= "^FO280," . $y_pos . "^A0N,17,17^FD" . $dia_line . "^FS\r\n";
        $y_pos += 20;
    }
    
    $printer_code .= "^PQ1\r\n" .
                     "^XZ\r\n";
    
    $final_printer_code .= $printer_code;
}

echo $final_printer_code;

