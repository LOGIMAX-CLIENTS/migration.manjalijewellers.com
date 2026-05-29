<?php
define('BASEPATH', __DIR__ . '/admin/system/');
require __DIR__ . '/admin/index.php';
// But initializing CI from CLI is tricky, let's just use curl
$url = 'http://localhost/etail_development_src/admin/index.php/admin_ret_billing/billing_invoice/50';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$html = curl_exec($ch);
curl_close($ch);

if (strpos($html, 'Sales Details') !== false) {
    echo "SALES TABLE FOUND\n";
}
if (strpos($html, 'Sales Return / Exchange Details') !== false) {
    echo "RETURN TABLE FOUND\n";
}
if (strpos($html, 'Purchase / Old Metal Details') !== false) {
    echo "PURCHASE TABLE FOUND\n";
}
if (strpos($html, 'SALES AMOUNT :') !== false) {
    echo "SALES AMOUNT FOOTER FOUND\n";
}
