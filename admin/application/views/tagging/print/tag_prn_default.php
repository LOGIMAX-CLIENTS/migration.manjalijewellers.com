<?php
$data = $tag; // Tag data passed from controller

$mrp = "";
if($data['calculation_based_on'] == 3 || $data['calculation_based_on'] == 4) {
    // Tax details should be provided or fetched. 
    // Since get_tax_details is in controller, we assume it might be pre-fetched or we call directly if CI instance available.
    // For now, mirroring existing controller logic which uses $this->get_tax_details()
    $CI =& get_instance();
    $tax_details = $CI->get_tax_details();
    
    $sell_rate  = $data['sell_rate'];
    $total_tax_rate = 0;
    if($data['calculation_based_on'] == 4) {
        $sell_rate  = $sell_rate * $data['gross_wt'];
    }
    if($data['tax_type'] == 2) {
        if(count($tax_details) > 0){
            // Tax Calculation - using CI instance to call controller methods if they are public
            $base_value_tax = round($CI->calculate_base_value_tax($sell_rate, $data['tgrp_id'], $tax_details), 2);
            $base_value_amt = round(($sell_rate + $base_value_tax), 2);
            $arrived_value_tax = round(($CI->calculate_arrived_value_tax($base_value_amt, $data['tgrp_id'], $tax_details)), 2);
            $arrived_value_amt = round($base_value_amt + $arrived_value_tax, 2);
            $total_tax_rate = round($base_value_tax + $arrived_value_tax, 2);
        }
    }
    $sale_value = round(($sell_rate+$total_tax_rate), 2);
    $mrp = '^FT05,65^A0N,23,19^FH\^FDRs.'.round($sale_value, 0).'^FS'. "\r\n";
} else {
    $mrp = '^FT05,65^A0N,23,19^FH\^FD^FS'. "\r\n";
}

$stone_amt = "";
if($data['stn_amount'] > 0) {
    $stone_amt = '^FT150,90^A0N,20,19^FH\^FDAMT: '.$data['stn_amount'].'^FS'. "\r\n";
}

$huid1 = "";
if(trim($data['hu_id']) != "" && trim($data['hu_id']) != "-") {
    $huid1 = '^FT283,40^A0N,20,24^FH\^FDHUID:'.$data['hu_id'].'^FS'. "\r\n";
} else {
    $huid1 = '^FT283,40^A0N,20,24^FH\^FD^FS'. "\r\n";
}

$huid2 = "";
if(isset($data['hu_id2']) && trim($data['hu_id2']) != "" && trim($data['hu_id2']) != "-") {
    $huid2 = '^FT283,61^A0N,20,24^FH\^FDHUID:'.$data['hu_id2'].'^FS'. "\r\n";
} else {
    $huid2 = '^FT283,61^A0N,20,24^FH\^FD^FS'. "\r\n";
}

$code_karigar = "";
if($data['size'] > 0) {
    $code_karigar = '^FT220,50^A0B,20,19^FH\^FD'.$data['code_karigar'].'^FS'. "\r\n";
} else {
    $code_karigar = '^FT220,50^A0B,20,19^FH\^FD^FS'. "\r\n";
}

$size = "";
if($data['size'] > 0) {
    $size = '^FT250,69^A0B,20,19^FH\^FDSIZE:'.$data['size'].'^FS'. "\r\n";
} else {
    $size = '^FT250,69^A0B,20,19^FH\^FD^FS'. "\r\n";
}

$printer_code = ' CT~~CD,~CC^~CT~'. "\r\n" .
                '^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR4,4~SD27^JUS^LRN^CI0^XZ'. "\r\n" .
                '^XA'. "\r\n" .
                '^MMT'. "\r\n" .
                '^PW740'. "\r\n" .
                '^LL101'. "\r\n" .
                '^LS0'. "\r\n" .
                '^FT05,01^A0N,25,24^FH\^FD'.$data['tag_code'].'^FS'. "\r\n" .
                '^FT05,40^A0N,23,24^FH\^FDG.WT: '.$data['gross_wt'].'^FS'. "\r\n" .
                $mrp.
                $stone_amt.
                $huid1.
                $huid2.
                '^FT283,89^A0N,20,19^FH\^FD'.($data['display_purity']+0).'^FS'. "\r\n" .
                '^FT530,70^A0B,28,28^FH\^FDKJS^FS'. "\r\n" .
                $code_karigar.
                $size.
                '^FT283,10^A0N,20,24^FH\^FD'.$data['product_name'].'^FS'. "\r\n" .
                '^FT450,70^BQN,2,2'. "\r\n" .
                '^FD'.$data['tag_id'].'^FS'. "\r\n" .
                '^PQ1,0,1,Y^XZ'. "\r\n";

echo $printer_code;
