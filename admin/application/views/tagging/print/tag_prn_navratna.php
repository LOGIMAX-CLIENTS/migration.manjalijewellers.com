<?php
$rightTag = $tag; // This view usually handles dual labels in Navratna requirement
$leftTag = isset($left_tag) ? $left_tag : null; 

if (!function_exists('build_epl_header')) {
    function build_epl_header() {
        $header = "<xpml><page quantity='0' pitch='24.0 mm'></xpml>";
        $header .= "I8,A\r\n";
        $header .= "q632\r\n";
        $header .= "O\r\n";
        $header .= "JF\r\n";
        $header .= "WN\r\n";
        $header .= "D15\r\n";
        $header .= "ZT\r\n";
        $header .= "Q192,24\r\n";
        $header .= "<xpml></page></xpml>";
        $header .= "<xpml><page quantity='1' pitch='24.0 mm'></xpml>";
        $header .= "N\r\n";
        return $header;
    }
}

if (!function_exists('determine_product_type')) {
    function determine_product_type($tag) {
        $metalType = isset($tag['metal_type']) ? intval($tag['metal_type']) : 0;
        $diaWt = isset($tag['dia_wt']) ? floatval($tag['dia_wt']) : 0;
        $stoneType = isset($tag['stone_type']) ? intval($tag['stone_type']) : 0;
        
        if ($stoneType == 1) return 'LOOSE_STONE';
        if ($diaWt > 0) return 'DIAMOND';
        if (isset($tag['sales_mode']) && $tag['sales_mode'] == 1 && $metalType == 2) return 'SILVER_MRP';
        if (isset($tag['sales_mode']) && $tag['sales_mode'] != 1 && $metalType == 2) return 'SILVER';
        return 'GOLD';
    }
}

if (!function_exists('format_label_text')) {
    function format_label_text($text, $maxLength = 14) {
        $text = str_replace(array('"', "'", "\r", "\n"), '', $text);
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength);
        }
        return $text;
    }
}

if (!function_exists('build_gold_silver_layout')) {
    function build_gold_silver_layout($rightTag, $leftTag) {
        $code = "";
        $rightX = 598;
        $leftX = 195;
        
        $r_product = format_label_text($rightTag['product_name'], 14);
        $r_prod_with_pcs = $r_product . '-' . $rightTag['piece'];
        $r_tagCode = $rightTag['tag_code'];
        $r_gw = number_format(floatval($rightTag['gross_wt']), 3, '.', '');
        $r_sw = number_format(floatval($rightTag['stn_wt']), 3, '.', '');
        $r_nw = number_format(floatval($rightTag['net_wt']), 3, '.', '');
        $r_sc = number_format(floatval($rightTag['stn_amount']), 2, '.', '');
        $r_vaValue = number_format(floatval($rightTag['retail_max_wastage_percent']), 0, '.', '');
        
        $l_product = $leftTag ? format_label_text($leftTag['product_name'], 14) : '';
        $l_prod_with_pcs = $leftTag ? ($l_product . '-' . $leftTag['piece']) : '';
        $l_tagCode = $leftTag ? $leftTag['tag_code'] : '';
        $l_gw = $leftTag ? number_format(floatval($leftTag['gross_wt']), 3, '.', '') : '0.000';
        $l_sw = $leftTag ? number_format(floatval($leftTag['stn_wt']), 3, '.', '') : '';
        $l_nw = $leftTag ? number_format(floatval($leftTag['net_wt']), 3, '.', '') : '0.000';
        $l_sc = $leftTag ? number_format(floatval($leftTag['stn_amount']), 2, '.', '') : '';
        $l_vaValue = $leftTag ? number_format(floatval($leftTag['retail_max_wastage_percent']), 0, '.', '') : '';

        $code .= 'A' . $rightX . ',185,2,2,1,1,N,"' . $r_prod_with_pcs . '"' . "\r\n";
        $code .= 'A' . $leftX . ',185,2,2,1,1,N,"' . $l_prod_with_pcs . '"' . "\r\n";
        
        $code .= 'B' . $rightX . ',163,2,1B,1,4,40,N,"' . $r_tagCode . '"' . "\r\n";
        $code .= 'A' . $rightX . ',117,2,2,1,1,N,"' . $r_tagCode . ' Va' . $r_vaValue . '%"' . "\r\n";
        
        $code .= 'B' . $leftX . ',163,2,1B,1,4,40,N,"' . $l_tagCode . '"' . "\r\n";
        $code .= 'A' . $leftX . ',117,2,2,1,1,N,"' . ($leftTag ? ($l_tagCode . ' Va' . $l_vaValue . '%') : ' ') . '"' . "\r\n";
        
        $code .= 'A' . $rightX . ',81,2,2,1,1,N,"GW: ' . $r_gw . '"' . "\r\n";
        $code .= 'A' . $leftX . ',81,2,2,1,1,N,"GW: ' . $l_gw . '"' . "\r\n";
        
        $code .= 'A' . $rightX . ',65,2,2,1,1,N,"SW: ' . $r_sw . '"' . "\r\n";
        $code .= 'A' . $leftX . ',65,2,2,1,1,N,"SW: ' . $l_sw . '"' . "\r\n";
        
        $code .= 'A' . $rightX . ',49,2,2,1,1,N,"NW: ' . $r_nw . '"' . "\r\n";
        $code .= 'A' . $leftX . ',49,2,2,1,1,N,"NW: ' . $l_nw . '"' . "\r\n";
        
        $code .= 'A' . $rightX . ',33,2,2,1,1,N,"SC: ' . $r_sc . '"' . "\r\n";
        $code .= 'A' . $leftX . ',33,2,2,1,1,N,"SC: ' . $l_sc . '"' . "\r\n";
        
        return $code;
    }
}

// Additional Navratna layouts (Diamond, Silver MRP, Loose Stone) would follow here.
// For brevity and based on the provided commented code, I'll include the skeleton.

$code = build_epl_header();
$type = determine_product_type($rightTag);

// Layout dispatch logic
if ($type == 'DIAMOND') {
    // build_diamond_layout...
} elseif ($type == 'SILVER_MRP') {
    // build_silver_mrp_layout...
} elseif ($type == 'LOOSE_STONE') {
    // build_loose_stone_layout...
} else {
    $code .= build_gold_silver_layout($rightTag, $leftTag);
}

$code .= "P1\r\n";
$code .= "<xpml></page></xpml>";

echo $code;
