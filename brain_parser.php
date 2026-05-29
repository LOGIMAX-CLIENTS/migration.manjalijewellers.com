<?php
$content = file_get_contents('c:/xampp/htdocs/etail_v2/admin/assets/js/ret_billing.js');

if (substr($content, 0, 2) === "\xff\xfe") {
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
}

preg_match_all('/function\s+([a-zA-Z0-9_]+)\s*\(/i', $content, $funcs, PREG_OFFSET_CAPTURE);

$urls = [];
preg_match_all('/url\s*:\s*base_url\s*\+\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches, PREG_OFFSET_CAPTURE);
if (empty($matches[0])) {
    preg_match_all('/url\s*:\s*[\'"]([^\'"]+)[\'"]/i', $content, $matches, PREG_OFFSET_CAPTURE);
}

echo "## AJAX ENDPOINTS FOUND:\n";
$unique_urls = [];
foreach ($matches[1] as $match) {
    $url = $match[0];
    $offset = $match[1];
    
    $parentFunc = 'Unknown';
    $closestDist = PHP_INT_MAX;
    foreach ($funcs[1] as $f) {
        $fName = $f[0];
        $fOffset = $f[1];
        if ($fOffset < $offset && ($offset - $fOffset) < $closestDist) {
            $closestDist = $offset - $fOffset;
            $parentFunc = $fName;
        }
    }
    $key = "$parentFunc -> $url";
    if (!isset($unique_urls[$key])) {
        echo "- Function: {$parentFunc} -> URL: {$url}\n";
        $unique_urls[$key] = true;
    }
}

echo "\n## MAJOR FUNCTIONS (Top 50 by size/offset diff):\n";
$funcSizes = [];
for($i=0; $i<count($funcs[1]); $i++) {
    $fName = $funcs[1][$i][0];
    $start = $funcs[1][$i][1];
    $end = isset($funcs[1][$i+1]) ? $funcs[1][$i+1][1] : strlen($content);
    $size = $end - $start;
    $funcSizes[$fName] = $size;
}
arsort($funcSizes);
$top = array_slice($funcSizes, 0, 50);
foreach($top as $k => $v) {
    echo "- {$k}: ~{$v} bytes\n";
}
