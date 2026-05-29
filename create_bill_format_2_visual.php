<?php
// Script to insert Bill Format 2 template and its blocks to Db
$conn = new mysqli('localhost', 'root', '', 'skjewels_skjetaillive');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Insert Detailed Jewelry Table Block
$block_html = '
<table style="width:100%; border-collapse:collapse; font-size: 10px; font-family: DejaVu Sans, sans-serif;">
    <thead style="text-transform:uppercase; font-size:10px; border-top: 1px solid black; border-bottom: 1px solid black;">
        <tr>
            <th style="width: 5%; text-align:center; padding:5px 3px;">S.No</th>
            <th style="width: 28%; text-align:left; padding:5px 3px;">Description</th>
            <th style="width: 8%; text-align:right; padding:5px 3px;">HSN</th>
            <th style="width: 5%; text-align:right; padding:5px 3px;">PCS</th>
            <th style="width: 9%; text-align:right; padding:5px 3px;">GRS.WT</th>
            <th style="width: 9%; text-align:right; padding:5px 3px;">NET.WT</th>
            <th style="width: 7%; text-align:right; padding:5px 3px;">V.A</th>
            <th style="width: 7%; text-align:right; padding:5px 3px;">MC</th>
            <th style="width: 10%; text-align:right; padding:5px 3px;">Rate</th>
            <th style="width: 12%; text-align:right; padding:5px 3px;">Amount</th>
        </tr>
    </thead>
    <tbody>
        {{#items}}
        <tr>
            <td style="text-align:center; padding:3px;">{{sno}}</td>
            <td style="text-align:left; padding:3px;">{{description}}</td>
            <td style="text-align:right; padding:3px;">{{hsn_code}}</td>
            <td style="text-align:right; padding:3px;">{{qty}}</td>
            <td style="text-align:right; padding:3px;">{{gross_wt}}</td>
            <td style="text-align:right; padding:3px;">{{net_wt}}</td>
            <td style="text-align:right; padding:3px;">{{va_percent}}</td>
            <td style="text-align:right; padding:3px;">{{mc}}</td>
            <td style="text-align:right; padding:3px;">{{rate}}</td>
            <td style="text-align:right; padding:3px;">{{taxable_amount}}</td>
        </tr>
        {{/items}}
        <tr>
            <td colspan="10" style="border-top:1px solid black;"></td>
        </tr>
    </tbody>
</table>
';

$stmt = $conn->prepare("INSERT INTO print_template_custom_blocks (block_name, block_html, block_category) VALUES (?, ?, 'Legacy Layouts') 
                        ON DUPLICATE KEY UPDATE block_html = VALUES(block_html)");
if ($stmt) {
    $name = 'Bill Format 2 - Jewelry Table';
    $stmt->bind_param("ss", $name, $block_html);
    $stmt->execute();
}

// 2. Insert Full Template
$template_html = '<div style="font-family: DejaVu Sans, sans-serif; padding: 20px; font-size: 10px;">
    <!-- Header -->
    <div style="text-align:center; margin-bottom: 20px;">
        <div style="font-size:30px; font-weight:bold;">{{company_name}}</div>
        <div style="font-size:12px;"><strong>{{company_address}} | {{company_city}} - {{company_pincode}} | Phone : {{company_mobile}}</strong></div>
        <div style="font-size:12px;"><strong>GSTIN : {{company_gstin}} | PAN : {{company_pan}}</strong></div>
    </div>
    
    <div style="text-align:center; font-size: 18px; font-weight:bold; text-transform:uppercase; margin-bottom: 10px;text-decoration: underline;">
        {{title}}
    </div>

    <!-- Customer & Invoice Info -->
    <div style="display: flex; width: 100%; border: 1px solid transparent; margin-bottom: 20px;">
        <div style="width: 40%; padding-right: 15px;">
            <div style="font-weight:bold; font-size:12px; text-transform:uppercase; margin-bottom:5px;">{{customer_name}}</div>
            <div>{{customer_address}}</div>
            <div>{{customer_city}} - {{customer_pincode}}</div>
            <div>Mobile: {{customer_mobile}}</div>
            <div>GSTIN: {{customer_gstin}}</div>
        </div>
        <div style="width: 20%; text-align:center;">
             <div style="width: 100px; height: 100px; background: #eee; border: 1px dashed #ccc; display:inline-flex; align-items:center; justify-content:center; color:#888;">QR Code</div>
        </div>
        <div style="width: 40%; padding-left: 15px;">
            <div style="display:flex;"><div style="width:40%; font-weight:bold;">Invoice No</div><div style="width:60%;">: {{invoice_no}}</div></div>
            <div style="display:flex;"><div style="width:40%; font-weight:bold;">Date</div><div style="width:60%;">: {{bill_date}}</div></div>
            <div style="display:flex;"><div style="width:40%; font-weight:bold;">Time</div><div style="width:60%;">: {{bill_time}}</div></div>
            <div style="display:flex;"><div style="width:40%; font-weight:bold;">Gold 22-KT</div><div style="width:60%;">: {{gold_rate}}/Gm</div></div>
            <div style="display:flex;"><div style="width:40%; font-weight:bold;">Silver</div><div style="width:60%;">: {{silver_rate}}/Gm</div></div>
        </div>
    </div>

    <!-- Title Bar -->
    <div style="text-align:center; font-weight:bold; font-size:14px; margin: 10px 0;">
        SALES BILL
    </div>

    <!-- Items Table -->
    ' . $block_html . '

    <!-- Payment & Terms -->
    <div style="display: flex; width: 100%; margin-top: 20px;">
        <div style="width: 60%; padding-right: 15px;">
            <div style="font-weight:bold; margin-bottom:10px;">Amount in words: {{amount_in_words}}</div>
            <div style="font-size:11px; text-align:justify; margin-top:20px;">
                * Amount of tax subject to reverse charge. * Received ornaments in good condition. * Hallmarking charge is included. * Check purity at BIS recognized centers. E.&O.E
            </div>
        </div>
        <div style="width: 40%;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr><td style="padding: 3px; font-weight: bold;">Cash Received</td><td style="padding: 3px; text-align: right;">Rs. {{pay_cash}}</td></tr>
                <tr><td style="padding: 3px; font-weight: bold;">Card Payment</td><td style="padding: 3px; text-align: right;">Rs. {{pay_card}}</td></tr>
                <tr><td style="padding: 3px; font-weight: bold;">UPI / Online</td><td style="padding: 3px; text-align: right;">Rs. {{pay_upi}}</td></tr>
                <tr><td style="padding: 3px; font-weight: bold; border-top: 1px dashed black; font-size: 14px;">Grand Total</td><td style="padding: 3px; text-align: right; border-top: 1px dashed black; font-size: 14px;">Rs. {{grand_total}}</td></tr>
            </table>
        </div>
    </div>
    
    <!-- Signatures -->
    <div style="display: flex; justify-content: space-between; margin-top: 50px; font-weight: bold;">
        <div style="border-top:1px solid black; padding-top:5px; width:200px; text-align:center;">Customer Signature</div>
        <div style="border-top:1px solid black; padding-top:5px; width:200px; text-align:center;">For {{company_name}}</div>
    </div>
</div>';

$stmt2 = $conn->prepare("INSERT INTO print_templates (template_code, template_name, template_category, template_html, is_active, gjs_data) VALUES ('BILL_FMT_2_VISUAL', 'Bill Format 2 (Visual Designer)', '1', ?, 1, '{}')");
if ($stmt2) {
    $stmt2->bind_param("s", $template_html);
    $stmt2->execute();
    echo "Successfully inserted Template and Blocks! ID: " . $conn->insert_id . "\n";
} else {
    echo "Error inserting template: " . $conn->error;
}
?>
