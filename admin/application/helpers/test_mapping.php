<?php
define('BASEPATH', 'dummy');
$data = [
    'billing' => ['tot_bill_amount' => 100, 'tot_amt_received' => 100, 'bill_type' => 1],
    'payment' => [
        ['payment_mode' => 'CASH', 'payment_amount' => 100]
    ],
    'comp_details' => ['company_name' => 'Test'],
    'est_other_item' => ['item_details' => [], 'old_matel_details' => [], 'return_details' => []]
];

// Mocking helper functions to avoid loading whole CI
function number_format_mock($n, $d) { return number_format($n, $d); }

// I'll just check the logic in receipt_helper.php manually or copy the relevant part
?>
