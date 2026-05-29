<?php
$db = new mysqli('localhost', 'root', '', 'arc_staging_24_02_26');
$db->set_charset('utf8');
$r = $db->query('SELECT template_html FROM print_templates WHERE id_template=26');
$row = $r->fetch_assoc();
$html = $row['template_html'];

// Replace ? currency symbols with ₹ HTML entity
$html = preg_replace('/>\?<\/td>/', '>&#8377;</td>', $html);

// Fix Summery Details table (id="i7fp") - convert to 3-column alignment
$s = strpos($html, '<table id="i7fp"');
if ($s !== false) {
    $e = strpos($html, '</table>', $s) + 8;
    $nt = '<table id="i7fp" style="width:100%;border-collapse:collapse;font-size:inherit;"><tbody>';
    $nt .= '<tr><td colspan="3" style="padding:2px;font-weight:bold;">Summery Details</td></tr>';
    $nt .= '<tr><td style="padding:2px;text-align:right;">Sales Amount :</td><td style="padding:2px;text-align:center;width:30px;">&#8377;</td><td style="padding:2px;text-align:right;width:120px;">{{sales_total_amount}}</td></tr>';
    $nt .= '<tr><td style="padding:2px;text-align:right;">Purchase Amount :</td><td style="padding:2px;text-align:center;width:30px;">&#8377;</td><td style="padding:2px;text-align:right;width:120px;">{{purchase_total_amount}}</td></tr>';
    $nt .= '<tr><td style="padding:2px;text-align:right;">Return Amount :</td><td style="padding:2px;text-align:center;width:30px;">&#8377;</td><td style="padding:2px;text-align:right;width:120px;border-bottom:1px solid #999;">{{lbl_return_amount}}</td></tr>';
    $nt .= '<tr style="font-weight:bold;"><td style="padding:4px 2px;text-align:right;">Net Amount:</td><td style="padding:4px 2px;text-align:center;width:30px;">&#8377;</td><td style="padding:4px 2px;text-align:right;width:120px;border-bottom:2px solid black;">{{net_amount}}</td></tr>';
    $nt .= '</tbody></table>';
    $html = substr($html, 0, $s) . $nt . substr($html, $e);
    echo "Fixed summery table\n";
} else {
    echo "Summery table not found\n";
}

// Fix Tax table (id="iqlo") - convert to 3-column alignment
$ts = strpos($html, '<table id="iqlo"');
if ($ts !== false) {
    $te = strpos($html, '</table>', $ts) + 8;
    $nt2 = '<table id="iqlo" style="width:100%;border-collapse:collapse;font-size:inherit;"><tbody>';
    $nt2 .= '<tr><td style="padding:2px;text-align:right;">Subtotal:</td><td style="padding:2px;text-align:center;width:30px;">&#8377;</td><td style="padding:2px;text-align:right;width:120px;">{{sub_total}}</td></tr>';
    $nt2 .= '<tr><td style="padding:2px;text-align:right;">CGST @%:</td><td style="padding:2px;text-align:center;width:30px;">&#8377;</td><td style="padding:2px;text-align:right;width:120px;">{{cgst_amount}}</td></tr>';
    $nt2 .= '<tr><td style="padding:2px;text-align:right;">SGST @%:</td><td style="padding:2px;text-align:center;width:30px;">&#8377;</td><td style="padding:2px;text-align:right;width:120px;border-bottom:1px solid #999;">{{sgst_amount}}</td></tr>';
    $nt2 .= '<tr style="font-weight:bold;"><td style="padding:4px 2px;text-align:right;">Grand Total:</td><td style="padding:4px 2px;text-align:center;width:30px;">&#8377;</td><td style="padding:4px 2px;text-align:right;width:120px;">{{grand_total}}</td></tr>';
    $nt2 .= '</tbody></table>';
    $html = substr($html, 0, $ts) . $nt2 . substr($html, $te);
    echo "Fixed tax table\n";
} else {
    echo "Tax table not found\n";
}

// Fix Payment Details - replace remaining ? with ₹
$html = str_replace('>? ', '>&#8377; ', $html);

// Update DB
$st = $db->prepare('UPDATE print_templates SET template_html=? WHERE id_template=26');
$st->bind_param('s', $html);
$st->execute();
echo 'Rows:' . $st->affected_rows . "\n";
$st->close();
$db->close();
echo "DONE\n";
