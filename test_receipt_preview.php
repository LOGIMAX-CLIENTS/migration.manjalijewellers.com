<?php
/**
 * Receipt Preview Debug Tool
 * 
 * Usage: http://localhost/manjalijewellers_old/test_receipt_preview.php?pay_id=123
 * 
 * Shows the receipt output in 3 modes:
 *   1. Browser-friendly preview (ESC/POS codes stripped, styled like a 58mm receipt)
 *   2. Raw string (escape codes visible as hex)
 *   3. Hex dump (for debugging exact bytes)
 */

// Bootstrap CodeIgniter
$_SERVER['CI_ENV'] = 'development';
define('ENVIRONMENT', 'development');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Minimal CI bootstrap
$system_path = 'system';
$application_folder = 'application';

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', FCPATH . $system_path . '/');
define('APPPATH', FCPATH . $application_folder . '/');
define('VIEWPATH', APPPATH . 'views/');

// We can't easily bootstrap the full REST controller outside CI.
// Instead, directly query the DB and simulate the receipt output.

// Load Globals class (used by database.php for DB credentials)
require(FCPATH . 'global_configs.php');

// Load CI's database config
require(APPPATH . 'config/database.php');

$db_config = $db['default'];
$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Get pay_id from URL
$pay_id = isset($_GET['pay_id']) ? intval($_GET['pay_id']) : 0;

// If no pay_id, show recent payments to pick from
if ($pay_id <= 0) {
    $result = $conn->query("SELECT p.id_payment, DATE_FORMAT(p.date_payment,'%d-%m-%Y') as date_payment, 
        p.payment_amount, IFNULL(sa.account_name, c.firstname) as customer_name, 
        sa.scheme_acc_number, s.scheme_name
        FROM payment p 
        LEFT JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
        LEFT JOIN customer c ON c.id_customer = sa.id_customer
        LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
        WHERE p.payment_status = 1 and p.added_by = 3
        ORDER BY p.id_payment DESC LIMIT 20");
    
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Select Payment to Preview</title>';
    echo '<style>
        body { font-family: "Segoe UI", sans-serif; background: #1a1a2e; color: #e0e0e0; padding: 40px; }
        h1 { color: #e94560; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th { background: #16213e; color: #e94560; padding: 12px; text-align: left; }
        td { padding: 10px 12px; border-bottom: 1px solid #333; }
        tr:hover { background: #16213e; }
        a { color: #00d2ff; text-decoration: none; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style></head><body>';
    echo '<h1>🧾 Receipt Preview - Select a Payment</h1>';
    echo '<p>No <code>pay_id</code> provided. Pick one from recent payments:</p>';
    echo '<table><tr><th>ID</th><th>Date</th><th>Customer</th><th>A/C No</th><th>Scheme</th><th>Amount</th><th>Action</th></tr>';
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id_payment'] . '</td>';
            echo '<td>' . $row['date_payment'] . '</td>';
            echo '<td>' . htmlspecialchars($row['customer_name'] ?? '-') . '</td>';
            echo '<td>' . htmlspecialchars($row['scheme_acc_number'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['scheme_name'] ?? '-') . '</td>';
            echo '<td>₹' . number_format($row['payment_amount'], 2) . '</td>';
            echo '<td><a href="?pay_id=' . $row['id_payment'] . '">Preview Receipt →</a></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No payments found with status=1</td></tr>';
    }
    
    echo '</table></body></html>';
    $conn->close();
    exit;
}

// Fetch payment data (same query as get_entry_records)
$sql = "SELECT e.firstname as emp_name, pay.id_scheme_account, 
    DATE_FORMAT(pay.date_payment,'%d-%m-%Y') as date_payment, 
    sch.scheme_name, pay.payment_amount, sch_acc.account_name as firstname, 
    cus.lastname, cus.mobile, addr.address1,
    cus.email,
    if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking',if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',if(payment_mode='OP','Other',if(payment_mode='CSH','CASH',if(payment_mode='Wallet','Wallet',if(payment_mode='UPI','UPI','-')))))))) as payment_mode,
    pay.receipt_no, pay.metal_rate, pay.id_branch, pay.id_payment, sch_acc.account_name,
    cmp.company_name,
    if(pay.payment_status=1,'Success',if(pay.payment_status=2,'Awaiting',if(pay.payment_status=3,'Pending',if(pay.payment_status=4,'Cancelled',if(pay.payment_status=5,'Failed','-'))))) as payment_status,
    (select IFNULL(IF(sch_acc.is_opening=1,IFNULL(sch_acc.paid_installments,0)+ IFNULL(if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight, COUNT(Date_Format(paym.date_payment,'%Y%m')), sum(paym.no_of_dues)),0), if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight or sch.scheme_type=3, COUNT(Date_Format(paym.date_payment,'%Y%m')), sum(paym.no_of_dues))) ,0) from payment paym where paym.payment_status=1 and paym.id_scheme_account=pay.id_scheme_account group by paym.id_scheme_account) as paid_due,
    IFNULL(pay.metal_weight, 0) as metal_weight,
    if(pay.receipt_no is null,'',pay.receipt_no) as receipt_no,
    if(cs.has_lucky_draw=1,concat(concat(ifnull(sch_acc.group_code,''),' ',ifnull(sch_acc.scheme_acc_number,'Not Allocated')),' - ',sch.code ),concat(sch.code,' ',ifnull(sch_acc.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
    cmp.tollfree1,
    IFNULL((SELECT SUM(py.metal_weight) FROM payment py WHERE py.payment_status=1 and py.id_scheme_account=pay.id_scheme_account), 0) as acc_weight,
    (SELECT SUM(py.payment_amount) FROM payment py WHERE py.payment_status=1 and py.id_scheme_account=pay.id_scheme_account) as tot_paid_amount,
    IFNULL(pay.saved_benefits,'0.000') as saved_benefits,
    IFNULL(pay.saved_benefit_amt,'0.00') as saved_benefit_amt,
    sch.scheme_name, sch.is_digi, IF(sch_acc.maturity_date IS NULL OR sch_acc.maturity_date = '0000-00-00', '-', DATE_FORMAT(sch_acc.maturity_date, '%d-%m-%Y')) as maturity_date
    FROM payment as pay 
    LEFT JOIN scheme_account sch_acc ON sch_acc.id_scheme_account = pay.id_scheme_account 
    LEFT JOIN employee e on e.id_employee = pay.id_employee
    LEFT JOIN scheme sch ON sch.id_scheme = sch_acc.id_scheme 
    LEFT JOIN customer as cus ON cus.id_customer = sch_acc.id_customer 
    join chit_settings cs
    join company cmp 
    LEFT JOIN address as addr ON addr.id_customer = cus.id_customer 
    WHERE pay.payment_status=1 and pay.id_payment = " . $pay_id;

$result = $conn->query($sql);
$payment = $result ? $result->fetch_assoc() : null;

if (!$payment) {
    die("<h2 style='color:red;font-family:sans-serif;'>No payment found for pay_id = $pay_id (or payment_status != 1)</h2>
         <p><a href='?'>← Back to list</a></p>");
}

// Fetch company data (same as get_company)
$company_result = $conn->query("SELECT c.company_name, c.address1, c.pincode, ct.name as city 
    FROM company c 
    LEFT JOIN city ct ON c.id_city = ct.id_city LIMIT 1");
$company = $company_result->fetch_assoc();

// Number to words function (simplified version of no_to_words)
function no_to_words_simple($no) {
    $words = array('0'=>'','1'=>'One','2'=>'Two','3'=>'Three','4'=>'Four','5'=>'Five',
        '6'=>'Six','7'=>'Seven','8'=>'Eight','9'=>'Nine','10'=>'Ten','11'=>'Eleven',
        '12'=>'Twelve','13'=>'Thirteen','14'=>'Fourteen','15'=>'Fifteen','16'=>'Sixteen',
        '17'=>'Seventeen','18'=>'Eighteen','19'=>'Nineteen','20'=>'Twenty','30'=>'Thirty',
        '40'=>'Forty','50'=>'Fifty','60'=>'Sixty','70'=>'Seventy','80'=>'Eighty','90'=>'Ninety',
        '100'=>'Hundred &','1000'=>'Thousand','100000'=>'Lakh','10000000'=>'Crore');
    
    $nos = explode('.', $no);
    $val = '';
    if (isset($nos[0])) {
        $val = convert($nos[0], $words) . ' Rupees';
    }
    if (isset($nos[1]) && $nos[1] != 0) {
        $val .= ' and ' . convert($nos[1], $words) . ' Paisa';
    }
    return $val;
}

function convert($num, $words) {
    if ($num == 0) return '';
    if ($num < 20) return $words["$num"] ?? '';
    if ($num < 100) {
        $ten = intval($num / 10) * 10;
        $unit = $num % 10;
        return ($words["$ten"] ?? '') . ($unit > 0 ? ' ' . ($words["$unit"] ?? '') : '');
    }
    if ($num < 1000) return ($words[intval($num / 100)] ?? '') . ' ' . ($words['100'] ?? '') . ' ' . convert($num % 100, $words);
    if ($num < 100000) return convert(intval($num / 1000), $words) . ' ' . ($words['1000'] ?? '') . ' ' . convert($num % 1000, $words);
    if ($num < 10000000) return convert(intval($num / 100000), $words) . ' ' . ($words['100000'] ?? '') . ' ' . convert($num % 100000, $words);
    return convert(intval($num / 10000000), $words) . ' ' . ($words['10000000'] ?? '') . ' ' . convert($num % 10000000, $words);
}

$amt_to_words = no_to_words_simple($payment['payment_amount']);
$HR = '================================';

// Build the exact same receipt string as getPaymentData()
$paymentstring = "";
$paymentstring .= "\x1b\x45\x01   " . $company['company_name'] . "\x1b\x45\x01\r\n";
$paymentstring .= "\x1b\x45\x01   " . $company['address1'] . "\x1b\x45\x01\r\n";
$paymentstring .= "\x1b\x45\x01   " . $company['city'] . ' - ' . $company['pincode'] . "\x1b\x45\x01\r\n";
$paymentstring .= "\x1b\x45\x01 Print Taken On : " . date('d-m-Y h:i:s A') . "\x1b\x45\x01\r\n";
$paymentstring .= "\r\n";
$paymentstring .= "      \x1b\x45\x01 " . $payment['scheme_name'] . " \x1b\x45\x01 \r\n";
$paymentstring .= $HR . "\r\n";
$paymentstring .= "\x1b\x45\x01A/C Name     " . ($payment['firstname'] ?? '-') . "\r\n";
$paymentstring .= "\x1b\x45\x01A/C No       " . ($payment['scheme_acc_number']) . "\r\n";
$paymentstring .= "\x1b\x45\x01RECEIPT NO   " . ($payment['receipt_no']) . "\r\n";
$paymentstring .= "\x1b\x45\x01PAID DUE     " . ($payment['paid_due']) . "\r\n";
$paymentstring .= "\x1b\x45\x01PAID MODE    " . ($payment['payment_mode']) . "\r\n";
$paymentstring .= "\x1b\x45\x01PAID AMT     " . ($payment['payment_amount']) . "\r\n";
$paymentstring .= "\x1b\x45\x01PAID WGT     " . ($payment['metal_weight']) . " G\r\n";
if ($payment['is_digi'] == 1) {
    $paymentstring .= "\x1b\x45\x01BENEFIT WGT  " . ($payment['saved_benefits'] > 0 ? $payment['saved_benefits'] : '0.000') . " G\r\n";
    // $paymentstring .= "\x1b\x45\x01BENEFIT AMT  " . ($payment['saved_benefit_amt'] > 0 ? number_format($payment['saved_benefit_amt'],2,'.','') : '0.00') . "\r\n";
}
$paymentstring .= "\x1b\x45\x01MOBILE       " . ($payment['mobile']) . "\r\n";
$paymentstring .= "\x1b\x45\x01METAL RATE   " . number_format($payment['metal_rate'], 2, '.', '') . "\r\n";
$paymentstring .= "\x1b\x45\x01TOTAL AMT    " . number_format($payment['tot_paid_amount'] ?? 0, 2, '.', '') . "\r\n";
$paymentstring .= "\x1b\x45\x01TOTAL WGT    " . ($payment['acc_weight']) . " G\r\n";
$paymentstring .= "\x1b\x45\x01PAID DATE    " . substr($payment['date_payment'],0,10) . "\r\n";
$paymentstring .= "\x1b\x45\x01MATURITY DATE " . (isset($payment['maturity_date']) && $payment['maturity_date'] != '' ? $payment['maturity_date'] : '-') . "\r\n";
$paymentstring .= $HR . "\r\n";
$paymentstring .= "\x1b\x45\x01Received with thanks from\x1b\x45\x01\r\n";
$paymentstring .= "\x1b\x45\x01" . $payment['firstname'] . "\x1b\x45\x01\r\n";
$paymentstring .= "\x1b\x45\x01\r\n";
$paymentstring .= "INR \x1b\x45\x01" . $payment['payment_amount'] . "\x1b\x45\x01\r\n";
$paymentstring .= "\x1b\x45\x01" . $amt_to_words . "\x1b\x45\x01\r\n";
$paymentstring .= "For \x1b\x45\x01" . $company['company_name'] . "\x1b\x45\x01\r\n";
$paymentstring .= "\x1b\x45\x01\r\n";
$paymentstring .= "                      Signature\r\n";

// Clean version (strip ESC/POS codes for browser display)
$clean = preg_replace('/\x1b\x45[\x00\x01]/', '', $paymentstring);
$clean = str_replace("\r\n", "\n", $clean);
$clean = str_replace("\r", "", $clean);

// Hex-escaped version (show escape codes as readable text)
$hex_visible = str_replace("\x1b\x45\x01", '<span class="esc">[ESC E 01]</span>', htmlspecialchars($paymentstring, ENT_QUOTES, 'UTF-8', false));
$hex_visible = str_replace("\r\n", '<span class="cr">↵</span>' . "\n", $hex_visible);
$hex_visible = str_replace("\t", '<span class="cr">→</span>', $hex_visible);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt Preview - Payment #<?= $pay_id ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #0f0f23; 
            color: #e0e0e0; 
            padding: 30px; 
        }
        h1 { color: #e94560; margin-bottom: 5px; }
        .subtitle { color: #888; margin-bottom: 30px; font-size: 14px; }
        .subtitle a { color: #00d2ff; text-decoration: none; }
        .subtitle a:hover { text-decoration: underline; }
        
        .tabs { display: flex; gap: 0; margin-bottom: 0; }
        .tab { 
            padding: 12px 24px; 
            background: #1a1a3e; 
            border: 1px solid #333; 
            border-bottom: none; 
            cursor: pointer; 
            color: #888; 
            font-weight: 600;
            border-radius: 8px 8px 0 0;
            transition: all 0.2s;
        }
        .tab:hover { color: #fff; background: #222255; }
        .tab.active { color: #e94560; background: #16213e; border-color: #e94560; }
        
        .panel { 
            display: none; 
            background: #16213e; 
            border: 1px solid #333; 
            border-radius: 0 8px 8px 8px; 
            padding: 30px;
            min-height: 400px;
        }
        .panel.active { display: block; }
        
        /* Receipt preview - simulates 58mm thermal paper */
        .receipt-paper {
            background: #fff;
            color: #000;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 12px 10px;
            width: 280px;
            margin: 0 auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            border-radius: 2px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        /* Raw view */
        .raw-view {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.8;
            white-space: pre-wrap;
            word-wrap: break-word;
            background: #0d1117;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #333;
        }
        .esc { color: #e94560; font-weight: bold; font-size: 11px; }
        .cr { color: #00d2ff; font-size: 11px; }
        
        /* Data table */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table th { text-align: left; padding: 8px 12px; background: #1a1a3e; color: #e94560; border-bottom: 1px solid #333; }
        .data-table td { padding: 8px 12px; border-bottom: 1px solid #222; }
        .data-table td:first-child { color: #888; width: 180px; }
        
        .badge { 
            display: inline-block; 
            padding: 2px 8px; 
            border-radius: 4px; 
            font-size: 11px; 
            font-weight: 600; 
        }
        .badge-success { background: #1a4731; color: #4ade80; }
        .badge-info { background: #1e3a5f; color: #60a5fa; }
    </style>
</head>
<body>

<h1>🧾 Receipt Preview — Payment #<?= $pay_id ?></h1>
<p class="subtitle">
    <a href="?">← Back to list</a> &nbsp;|&nbsp; 
    Customer: <strong><?= htmlspecialchars($payment['firstname'] ?? '-') ?></strong> &nbsp;|&nbsp;
    Status: <span class="badge badge-success"><?= $payment['payment_status'] ?></span>
</p>

<div class="tabs">
    <div class="tab active" onclick="showTab(0)">📄 Receipt Preview</div>
    <div class="tab" onclick="showTab(1)">🔍 Raw ESC/POS</div>
    <div class="tab" onclick="showTab(2)">📊 Data Fields</div>
</div>

<!-- Tab 1: Receipt Preview (58mm paper simulation) -->
<div class="panel active" id="panel-0">
    <p style="text-align:center; color:#888; margin-bottom:15px; font-size:12px;">
        ↓ Simulated 58mm thermal receipt (ESC/POS bold codes stripped) ↓
    </p>
    <div class="receipt-paper"><?= htmlspecialchars($clean) ?></div>
</div>

<!-- Tab 2: Raw ESC/POS with visible escape codes -->
<div class="panel" id="panel-1">
    <p style="color:#888; margin-bottom:15px; font-size:12px;">
        <span class="esc">[ESC E 01]</span> = Bold ON command (hex: <code>1B 45 01</code>) &nbsp;|&nbsp;
        <span class="cr">↵</span> = CRLF line ending &nbsp;|&nbsp;
        <span class="cr">→</span> = Tab character
    </p>
    <div class="raw-view"><?= $hex_visible ?></div>
</div>

<!-- Tab 3: Data fields from DB -->
<div class="panel" id="panel-2">
    <table class="data-table">
        <tr><th colspan="2">Payment Record (from get_entry_records)</th></tr>
        <?php foreach ($payment as $key => $val): ?>
        <tr>
            <td><?= htmlspecialchars($key) ?></td>
            <td><?= htmlspecialchars($val ?? 'NULL') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><th colspan="2">Company Record (from get_company)</th></tr>
        <?php foreach ($company as $key => $val): ?>
        <tr>
            <td><?= htmlspecialchars($key) ?></td>
            <td><?= htmlspecialchars($val ?? 'NULL') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><th colspan="2">Computed</th></tr>
        <tr><td>Amount in Words</td><td><?= htmlspecialchars($amt_to_words) ?></td></tr>
    </table>
</div>

<script>
function showTab(idx) {
    document.querySelectorAll('.tab').forEach((t, i) => t.classList.toggle('active', i === idx));
    document.querySelectorAll('.panel').forEach((p, i) => p.classList.toggle('active', i === idx));
}
</script>

</body>
</html>
