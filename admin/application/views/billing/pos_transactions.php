<!-- POS Payment Report — v12 (Functional Sharp Operational) -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
:root { --f:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; --mono:'SF Mono','Fira Code','Consolas',monospace;
--ink:#0f172a; --ink2:#1e293b; --ink3:#334155; --mute:#64748b; --faint:#94a3b8; --line:#e2e8f0; --wash:#f1f5f9; --bg:#f8fafc; --white:#fff;
--ok:#059669; --ok-bg:#ecfdf5; --warn:#d97706; --warn-bg:#fffbeb; --err:#dc2626; --err-bg:#fef2f2;
--accent:#2563eb; --accent-bg:#eff6ff;
--radius:8px; --shadow:0 1px 2px rgba(0,0,0,.04); }

/* ─ Page Shell ─ */
.pr-wrap { max-width:1400px; margin:0 auto; padding:20px 28px; font-family:var(--f); color:var(--ink); background:var(--bg); }
.pr-wrap * { box-sizing:border-box; }

/* ─ Header ─ */
.pr-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.pr-hdr h2 { font-size:18px; font-weight:700; margin:0; display:flex; align-items:center; gap:8px; letter-spacing:-.2px; color:var(--ink); }
.pr-hdr h2 i { color:var(--mute); font-size:16px; }
.pr-hdr-r { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }

/* ─ Date Picker ─ */
.pr-date-grp { display:flex; align-items:center; background:var(--white); border:1px solid var(--line); border-radius:var(--radius); padding:6px 12px; gap:6px; cursor:pointer; transition:border-color .15s; }
.pr-date-grp:hover { border-color:var(--faint); }
.pr-date-grp:focus-within { border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.pr-date-grp input { border:none; background:none; font-size:13px; width:205px; text-align:center; cursor:pointer; color:var(--ink2); font-weight:600; outline:none; font-family:var(--f); }
.pr-date-grp i { color:var(--mute); cursor:pointer; font-size:13px; }

/* ─ Buttons ─ */
.btn { display:inline-flex; align-items:center; gap:5px; padding:7px 14px; border-radius:var(--radius); font-size:12px; font-weight:600; border:1px solid var(--line); cursor:pointer; transition:all .15s; text-decoration:none; font-family:var(--f); background:var(--white); color:var(--ink3); }
.btn:hover { border-color:var(--faint); background:var(--wash); color:var(--ink); }
.btn-primary { background:var(--accent) !important; color:var(--white) !important; border-color:var(--accent) !important; }
.btn-primary:hover { background:#1d4ed8 !important; border-color:#1d4ed8 !important; color:var(--white) !important; box-shadow:0 2px 8px rgba(37,99,235,.2) !important; }
.btn-sm { padding:5px 10px; font-size:11px; }
.btn-export { background:var(--white); color:var(--ink3); }
/* Secondary actions — ghost style */
.btn-ghost { border:none; background:none; color:var(--faint); padding:6px 8px; }
.btn-ghost:hover { color:var(--ink3); background:var(--wash); border:none; }

/* ─ KPI STRIP — Collected is DOMINANT ─ */
.pr-kpi { display:flex; gap:0; margin-bottom:28px; background:var(--white); border:1px solid var(--line); border-radius:var(--radius); overflow:hidden; }
.kpi { flex:1; padding:14px 18px; display:flex; align-items:center; gap:12px; border-right:1px solid var(--wash); transition:background .15s; position:relative; }
.kpi:last-child { border-right:none; }
.kpi:hover { background:var(--bg); }
/* First KPI (Collected) = hero metric */
.kpi:first-child { flex:1.8; padding:16px 24px; }
.kpi:first-child .kpi-val { font-size:26px; }
.kpi:first-child .kpi-label { font-size:11px; color:var(--ok); }
.kpi-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; }
.kpi-icon-ok { background:var(--ok-bg); color:var(--ok); }
.kpi-icon-default { background:var(--wash); color:var(--mute); }
.kpi-icon-warn { background:var(--warn-bg); color:var(--warn); }
.kpi-icon-err { background:var(--err-bg); color:var(--err); }
.kpi-body { flex:1; min-width:0; }
.kpi-label { font-size:10px; font-weight:700; color:var(--faint); text-transform:uppercase; letter-spacing:.8px; margin-bottom:1px; }
.kpi-val { font-size:16px; font-weight:700; line-height:1.2; color:var(--ink2); letter-spacing:-.2px; font-variant-numeric:tabular-nums; }
.kpi-sub { font-size:10px; color:var(--faint); margin-top:1px; font-weight:500; }
@media(max-width:900px){ .pr-kpi { flex-wrap:wrap; } .kpi { flex:1 1 48%; border-bottom:1px solid var(--wash); } .kpi:first-child { flex:1 1 100%; } }

/* ─ Filter Bar ─ */
.pr-bar { display:flex; align-items:center; gap:4px; margin-bottom:6px; flex-wrap:wrap; }
.tab { padding:5px 12px; border-radius:20px; font-size:11px; font-weight:600; cursor:pointer; border:none; background:transparent; color:var(--faint); transition:all .15s; font-family:var(--f); }
.tab:hover { background:var(--wash); color:var(--ink3); }
.tab.active { background:var(--ink); color:var(--white); }
.tab-ct { font-size:9px; opacity:.5; margin-left:2px; }
.flt-sel { padding:5px 8px; border:1px solid var(--wash); border-radius:6px; font-size:11px; font-weight:500; color:var(--mute); background:var(--white); cursor:pointer; outline:none; font-family:var(--f); transition:border-color .15s; }
.flt-sel:focus { border-color:var(--accent); box-shadow:0 0 0 2px rgba(37,99,235,.06); }
.flt-search { border:1px solid var(--wash); border-radius:6px; padding:5px 8px; font-size:11px; width:170px; outline:none; font-family:var(--f); color:var(--ink2); background:var(--white); transition:border-color .15s; }
.flt-search:focus { border-color:var(--accent); box-shadow:0 0 0 2px rgba(37,99,235,.06); }

/* ─ Table Card — minimal container ─ */
.pr-card { background:var(--white); border:1px solid var(--wash); border-radius:var(--radius); overflow:hidden; }
.pr-card-hdr { padding:8px 16px; border-bottom:1px solid var(--wash); display:flex; align-items:center; justify-content:space-between; gap:8px; background:var(--bg); flex-wrap:wrap; }
.pr-card-title { font-size:11px; font-weight:600; color:var(--faint); display:flex; align-items:center; gap:6px; text-transform:uppercase; letter-spacing:.5px; }
.pr-card-title i { color:var(--faint); font-size:11px; }
.pr-card-actions { display:flex; align-items:center; gap:4px; }

/* ─ Reconcile Tooltip ─ */
.rec-wrap { position:relative; display:inline-block; }
.rec-tip { position:absolute; top:calc(100% + 10px); right:0; transform:translateY(-4px); background:var(--ink); color:var(--white); font-size:11px; padding:10px 14px; border-radius:8px; width:260px; line-height:1.5; z-index:100; font-weight:400; opacity:0; visibility:hidden; transition:all .2s ease; pointer-events:none; box-shadow:0 8px 24px rgba(0,0,0,.15); }
.rec-wrap:hover .rec-tip { opacity:1; visibility:visible; transform:translateY(0); }
.rec-tip::after { content:''; position:absolute; bottom:100%; right:20px; border:6px solid transparent; border-bottom-color:var(--ink); }

/* ─ TABLE — DARK HEADER for strong separation ─ */
.pr-tbl { width:100%; border-collapse:collapse; }
.pr-tbl thead th { padding:10px 12px; font-size:10px; font-weight:600; color:rgba(255,255,255,.9); border-bottom:none; text-align:left; white-space:nowrap; background:#475569; letter-spacing:.5px; text-transform:uppercase; }
.pr-tbl thead th:first-child { border-radius:0; }
.pr-tbl thead th:last-child { border-radius:0; }

/* Zebra rows — generous height, visible hover */
.pr-tbl tbody tr.pr-row { border-bottom:1px solid var(--line); transition:all .1s; cursor:pointer; }
.pr-tbl tbody tr.pr-row:nth-child(4n+1) { background:var(--white); }
.pr-tbl tbody tr.pr-row:nth-child(4n+3) { background:var(--bg); }
.pr-tbl tbody tr.pr-row:hover { background:#dbeafe; }
.pr-tbl tbody td { padding:15px 12px; font-size:12px; color:var(--ink2); vertical-align:middle; }

/* Sortable headers */
.pr-tbl thead th.sortable { cursor:pointer; position:relative; padding-right:20px; user-select:none; }
.pr-tbl thead th.sortable:hover { background:#3f4f63; color:var(--white); }
.pr-tbl thead th.sortable::after { content:'⇅'; position:absolute; right:6px; top:50%; transform:translateY(-50%); font-size:10px; opacity:.35; }
.pr-tbl thead th.sortable.sort-asc::after { content:'↑'; opacity:1; color:#93c5fd; }
.pr-tbl thead th.sortable.sort-desc::after { content:'↓'; opacity:1; color:#93c5fd; }

/* ─ Cell Styles ─ */
.c-bill a { color:var(--accent); font-weight:600; text-decoration:none; font-size:12px; }
.c-bill a:hover { text-decoration:underline; }
.c-bill-type { display:block; font-size:10px; color:var(--faint); font-weight:500; }
.c-none { color:var(--faint); font-size:11px; }
.c-name { font-weight:600; font-size:12px; color:var(--ink); }
.c-sub { display:block; font-size:10px; color:var(--faint); }

/* Payment mode — all monochrome */
.pm-tag { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:600; background:var(--wash); color:var(--ink3); }
.pm-card { background:var(--wash); color:var(--ink3); }
.pm-upi { background:var(--wash); color:var(--ink3); }
.pm-nb { background:var(--wash); color:var(--ink3); }
.pm-default { background:var(--wash); color:var(--mute); }
.pm-tag i { font-size:10px; color:var(--faint); }
.pm-sub { display:block; font-size:10px; color:var(--faint); margin-top:1px; }

/* Amount — HERO COLUMN */
.c-amt { font-weight:800; text-align:right; font-size:15px; font-variant-numeric:tabular-nums; color:var(--ink); letter-spacing:-.3px; }

/* Ref / UTR — de-emphasized */
.c-ref { font-size:10px; font-family:var(--mono); color:var(--mute); background:transparent; padding:2px 0; border-radius:0; display:inline-block; max-width:260px; word-break:break-all; white-space:normal; line-height:1.4; letter-spacing:.2px; font-weight:500; border:none; }
.c-ref:hover { color:var(--accent); cursor:pointer; }
.c-ref-label { display:block; font-size:8px; color:var(--faint); text-transform:uppercase; letter-spacing:.5px; margin-bottom:1px; font-family:var(--f); font-weight:700; }

/* Status — ONLY color element in the table */
.st { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:600; padding:3px 8px; border-radius:4px; }
.st-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
.st-1 { color:var(--ok); background:var(--ok-bg); } .st-1 .st-dot { background:var(--ok); }
.st-0 { color:var(--warn); background:var(--warn-bg); } .st-0 .st-dot { background:var(--warn); }
.st-2 { color:var(--err); background:var(--err-bg); } .st-2 .st-dot { background:var(--err); }
.st-3 { color:var(--faint); background:var(--wash); } .st-3 .st-dot { background:var(--faint); }

.c-dev { font-size:10px; color:var(--faint); font-weight:400; }
.c-time { font-size:11px; color:var(--mute); white-space:nowrap; font-weight:600; font-variant-numeric:tabular-nums; }

/* ─ Expand — OBVIOUS selected state ─ */
.exp-i { font-size:10px; color:var(--faint); transition:transform .2s; }
.pr-row.expanded { background:#e0e7ff !important; border-bottom:none !important; }
.pr-row.expanded td { color:var(--ink) !important; font-weight:600; }
.pr-row.expanded td:first-child { box-shadow:inset 4px 0 0 var(--accent); }
.pr-row.expanded .exp-i { transform:rotate(90deg); color:var(--accent); }

/* ─ Detail Row — WHITE panel against table bg, thick left accent ─ */
.pr-detail { display:none; }
.pr-detail > td { padding:0 !important; background:var(--bg); }
.pr-detail-inner { background:var(--white); border-left:4px solid var(--accent); margin:4px 12px 8px 0; padding:16px 24px 16px 40px; display:flex; gap:32px; flex-wrap:wrap; box-shadow:0 1px 3px rgba(0,0,0,.05); border-radius:0 6px 6px 0; }
.d-panel { flex:1; min-width:180px; padding:0; border:none; background:transparent; border-radius:0; }
.d-panel:hover { box-shadow:none; }
.d-hd { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.8px; margin-bottom:10px; padding-bottom:5px; display:flex; align-items:center; gap:5px; color:var(--ink3); border-bottom:2px solid var(--line); }
.d-hd i { width:auto; height:auto; border-radius:0; display:inline; font-size:11px; background:none !important; color:var(--ink3) !important; }
.d-hd-txn, .d-hd-pp, .d-hd-card, .d-hd-upi, .d-hd-nb, .d-hd-cust { color:var(--ink3); border-bottom-color:var(--line); }
.d-hd-txn i, .d-hd-pp i, .d-hd-card i, .d-hd-upi i, .d-hd-nb i, .d-hd-cust i { background:none !important; color:var(--ink3) !important; }
.d-row { display:flex; align-items:baseline; gap:8px; padding:4px 0; }
.d-row:last-child { border-bottom:none; }
.d-lbl { font-size:9px; font-weight:600; color:var(--faint); text-transform:uppercase; letter-spacing:.3px; min-width:72px; flex-shrink:0; }
.d-val { font-size:12px; color:var(--ink); font-weight:600; word-break:break-all; line-height:1.4; }
.d-val code { font-size:10px; font-family:var(--mono); color:var(--ink2); background:var(--wash); padding:1px 5px; border-radius:3px; border:none; letter-spacing:.2px; }
.d-val-ok { color:var(--ok) !important; font-weight:700 !important; }
.d-val-time { font-variant-numeric:tabular-nums; }

/* ─ Pagination ─ */
.pr-table-footer { display:flex; align-items:center; justify-content:space-between; padding:8px 16px; border-top:1px solid var(--wash); background:var(--bg); flex-wrap:wrap; gap:8px; }
.pr-entries { display:flex; align-items:center; gap:6px; font-size:11px; color:var(--faint); font-weight:500; }
.pr-entries select { padding:3px 6px; border:1px solid var(--wash); border-radius:4px; font-size:11px; color:var(--mute); background:var(--white); cursor:pointer; outline:none; font-family:var(--f); }
.pr-page-info { font-size:11px; color:var(--faint); font-weight:500; }
.pr-pagination { display:flex; align-items:center; gap:2px; }
.pr-pagination button { padding:5px 10px; border:1px solid var(--wash); border-radius:4px; background:var(--white); color:var(--mute); font-size:11px; font-weight:600; cursor:pointer; transition:all .1s; font-family:var(--f); }
.pr-pagination button:hover:not(:disabled) { background:var(--wash); color:var(--ink3); }
.pr-pagination button.active { background:var(--ink); color:var(--white); border-color:var(--ink); }
.pr-pagination button:disabled { opacity:.35; cursor:not-allowed; }

/* ─ Settlement ─ */
.pr-settle { margin-top:24px; }
.stbl { width:100%; border-collapse:collapse; }
.stbl thead th { padding:10px 12px; font-size:10px; font-weight:700; color:var(--faint); border-bottom:2px solid var(--line); text-align:left; background:var(--white); letter-spacing:.6px; text-transform:uppercase; }
.stbl tbody td { padding:10px 12px; font-size:12px; color:var(--ink2); border-bottom:1px solid var(--wash); }
.stbl tbody tr:hover { background:var(--bg); }
.stbl tfoot td { padding:10px 12px; font-weight:700; border-top:2px solid var(--ink); font-size:12px; background:var(--bg); }
.pr-empty { text-align:center; padding:60px 20px; color:var(--faint); font-size:13px; }
.pr-empty i { font-size:36px; display:block; margin-bottom:10px; color:var(--faint); }

@media(max-width:768px){ .pr-hdr { flex-direction:column; align-items:flex-start; } .pr-detail-inner { grid-template-columns:1fr; } .pr-table-footer { flex-direction:column; } .pr-kpi { flex-direction:column; } .kpi { border-right:none; border-bottom:1px solid var(--line); } }
</style>

<?php
    $COL_SPAN = 10;
    $s = isset($report['summary']) ? $report['summary'] : array('total'=>0,'success'=>0,'failed'=>0,'pending'=>0,'cancelled'=>0,'success_amount'=>0,'total_amount'=>0);
    $txns = isset($report['transactions']) ? $report['transactions'] : array();
    $breakdown = isset($report['breakdown']) ? $report['breakdown'] : array();
    $dateVal = isset($report_date) ? $report_date : date('d-m-Y');
    $dateToVal = isset($report_date_to) && $report_date_to ? $report_date_to : '';
    // Devices from transaction data (dynamic)
    $devices = array();
    foreach($txns as $t){
        $dn = $t['device_name']; if($dn && $dn != '-' && !in_array($dn, $devices)) $devices[] = $dn;
    }
    // Branches from master table
    $branches = isset($master_branches) ? $master_branches : array();
    // Company name for branding
    $companyName = isset($company['company_name']) ? $company['company_name'] : (isset($company['name']) ? $company['name'] : 'Lakshmanaa & Chari Son');
    $baseUrl = base_url().'index.php/';
    // Date range label for display
    $dateRangeLabel = $dateVal . ($dateToVal && $dateToVal != $dateVal ? ' → ' . $dateToVal : '');
?>

<div class="content-wrapper">
<section class="content">
<div class="pr-wrap">

    <!-- HEADER -->
    <div class="pr-hdr">
        <div>
            <h2><i class="fa fa-credit-card"></i> POS Collection Dashboard</h2>
            <div style="font-size:11px;color:var(--c3);margin-top:3px;font-weight:500;"><?php echo htmlspecialchars($companyName); ?> — Digital Payments Ledger</div>
        </div>
        <div class="pr-hdr-r">
            <div class="pr-date-grp" id="date_range_toggle">
                <i class="fa fa-calendar"></i>
                <input type="text" id="pr_daterange" value="<?php echo $dateRangeLabel; ?>" readonly />
            </div>
            <button class="btn btn-primary" onclick="loadReport()"><i class="fa fa-search"></i> Load</button>
            <button class="btn btn-ghost" onclick="exportCSV()" data-tip="Export to CSV"><i class="fa fa-download"></i></button>
            <button class="btn btn-ghost" onclick="printReport()" data-tip="Print report"><i class="fa fa-print"></i></button>
            <a href="<?php echo $baseUrl; ?>admin_pos/posSettings" class="btn btn-ghost" data-tip="POS Settings"><i class="fa fa-cog"></i></a>
        </div>
    </div>

    <!-- KPI -->
    <div class="pr-kpi">
        <div class="kpi"><div class="kpi-icon kpi-icon-ok"><i class="fa fa-check-circle"></i></div><div class="kpi-body"><div class="kpi-label">Collected</div><div class="kpi-val">₹<?php echo number_format(floatval($s['success_amount'])/100, 2); ?></div><div class="kpi-sub"><?php echo $s['success']; ?> payments received</div></div></div>
        <div class="kpi"><div class="kpi-icon kpi-icon-default"><i class="fa fa-exchange"></i></div><div class="kpi-body"><div class="kpi-label">Total</div><div class="kpi-val"><?php echo $s['total']; ?></div><div class="kpi-sub">transactions today</div></div></div>
        <div class="kpi"><div class="kpi-icon kpi-icon-warn"><i class="fa fa-clock-o"></i></div><div class="kpi-body"><div class="kpi-label">Pending</div><div class="kpi-val"><?php echo $s['pending']; ?></div><div class="kpi-sub">awaiting confirmation</div></div></div>
        <div class="kpi"><div class="kpi-icon kpi-icon-err"><i class="fa fa-times-circle"></i></div><div class="kpi-body"><div class="kpi-label">Failed</div><div class="kpi-val"><?php echo $s['failed']; ?></div><div class="kpi-sub"><?php echo $s['failed']; ?> failed · <?php echo $s['cancelled']; ?> cancelled</div></div></div>
    </div>

    <!-- FILTER BAR -->
    <div class="pr-bar">
        <span class="tab active" onclick="filterStatus('all',this)">All <span class="tab-ct"><?php echo $s['total']; ?></span></span>
        <span class="tab" onclick="filterStatus('1',this)">Success <span class="tab-ct"><?php echo $s['success']; ?></span></span>
        <span class="tab" onclick="filterStatus('0',this)">Pending <span class="tab-ct"><?php echo $s['pending']; ?></span></span>
        <span class="tab" onclick="filterStatus('2',this)">Failed <span class="tab-ct"><?php echo $s['failed']; ?></span></span>
        <span style="flex:1;"></span>
        <select class="flt-sel" id="flt_device" onchange="applyFilters()"><option value="">All Devices</option><?php foreach($devices as $dv): ?><option value="<?php echo htmlspecialchars($dv); ?>"><?php echo htmlspecialchars($dv); ?></option><?php endforeach; ?></select>
        <select class="flt-sel" id="flt_branch" onchange="applyFilters()"><option value="">All Branches</option><?php foreach($branches as $br): ?><option value="<?php echo htmlspecialchars($br['name']); ?>"><?php echo htmlspecialchars($br['name']); ?><?php if(!empty($br['short_name'])): ?> (<?php echo $br['short_name']; ?>)<?php endif; ?></option><?php endforeach; ?></select>
        <input type="text" class="flt-search" placeholder="🔍 Search transactions..." id="pr_search" oninput="applyFilters()" />
    </div>

    <!-- TABLE -->
    <div class="pr-card">
        <div class="pr-card-hdr">
            <span class="pr-card-title"><i class="fa fa-list"></i> Transaction Ledger — <?php echo $dateRangeLabel; ?> · <?php echo count($txns); ?> entries</span>
            <div class="pr-card-actions">
                <div class="rec-wrap">
                    <button class="btn btn-sm" onclick="runReconcile()" id="btn_reconcile"><i class="fa fa-refresh"></i> Reconcile</button>
                    <div class="rec-tip">Re-checks pending transactions with the payment gateway to recover any missed confirmations.</div>
                </div>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="pr-tbl" id="pr_table">
                <thead><tr>
                    <th style="width:24px;"></th>
                    <th style="width:30px;" class="sortable" data-col="0">#</th>
                    <th class="sortable" data-col="2">Bill No</th>
                    <th class="sortable" data-col="3">Customer</th>
                    <th>Payment Mode</th>
                    <th style="text-align:right;" class="sortable" data-col="5">Amount</th>
                    <th>UTR / Reference</th>
                    <th class="sortable" data-col="7">Status</th>
                    <th>Device</th>
                    <th class="sortable" data-col="9">Time</th>
                </tr></thead>
                <tbody>
                    <?php if(!empty($txns)): $i=1; foreach($txns as $t):
                        $st = intval($t['pos_req_status']);
                        $stText = array(0=>'Pending',1=>'Success',2=>'Failed',3=>'Cancelled');
                        $stLabel = isset($stText[$st]) ? $stText[$st] : 'Unknown';
                        $amt = floatval($t['pos_req_amount'])/100;
                        $hasBill = !empty($t['bill_number']) && !empty($t['pos_req_bill_id']);
                        $hasEst = !empty($t['bill_number']) && empty($t['pos_req_bill_id']) && !empty($t['pos_req_est_id']);
                        $billType = intval($t['ret_bill_type'] ?? 0);
                        $billLink = $hasBill ? $baseUrl.'admin_ret_billing/billing_invoice/'.$t['pos_req_bill_id'].'/'.$billType : '';

                        // Parse PhonePe response JSON for real UTR and card details
                        $cardNetwork=''; $cardType=''; $jsonCardNo=''; $ppRefId=''; $ppState=''; $ppResponseCode='';
                        if(!empty($t['pos_res_trans_data'])){
                            $td = json_decode($t['pos_res_trans_data'], true);
                            if($td){
                                // Real PhonePe UTR / Provider Reference
                                if(isset($td['providerReferenceId'])) $ppRefId = $td['providerReferenceId'];
                                if(isset($td['paymentState'])) $ppState = $td['paymentState'];
                                if(isset($td['payResponseCode'])) $ppResponseCode = $td['payResponseCode'];
                                // Card details from IEDC
                                if(isset($td['paymentInstruments']) && is_array($td['paymentInstruments'])){
                                    foreach($td['paymentInstruments'] as $pi){
                                        if(isset($pi['cardNetwork'])) $cardNetwork = $pi['cardNetwork'];
                                        if(isset($pi['cardType'])) $cardType = $pi['cardType'];
                                        if(isset($pi['last4Digits'])) $jsonCardNo = $pi['last4Digits'];
                                    }
                                }
                            }
                        }

                        $pm = strtoupper($t['payment_mode']);
                        $bpMode = strtoupper($t['bp_payment_mode'] ?: '');
                        $cardName = $t['bp_card_name'] ?: $cardNetwork;
                        $cardNo = $t['bp_card_no'] ?: ($t['card_last4'] ?: $jsonCardNo);
                        $approvalNo = $t['bp_approval_no'] ?: '';
                        $nbType = intval($t['bp_nb_type']);
                        $nbLabel = ($nbType==1?'RTGS':($nbType==2?'IMPS':($nbType==3?'UPI':'')));
                        $utr = $t['pos_utr'] ?: '';
                        $txnRef = $t['pos_res_ref_id'] ?: '';
                        // Real PhonePe reference (providerReferenceId) is the actual bank UTR
                        $realUTR = $ppRefId ?: $utr;
                        $payTime = $t['pos_req_createdon'] ? date('h:i:s A', strtotime($t['pos_req_createdon'])) : '-';
                        // Consistent date formatting for detail panel
                        $initiatedFmt = $t['pos_req_createdon'] ? date('d-m-Y · h:i:s A', strtotime($t['pos_req_createdon'])) : '-';
                        $confirmedAt = !empty($t['pos_success_at']) ? date('d-m-Y · h:i:s A', strtotime($t['pos_success_at'])) : '';
                        $payDevice = $t['bp_device_name'] ?: '';
                        $posDevice = $t['device_name'] ?: '-';

                        $isCard = ($bpMode=='CC'||$bpMode=='DC'||strpos($pm,'CARD')!==false||strpos($pm,'IEDC')!==false);
                        $isUPI = (strpos($pm,'DQR')!==false||strpos($pm,'UPI')!==false);
                        $isNB = ($bpMode=='NB'||$bpMode=='NETBANKING');

                        // For the main table UTR column: show real PhonePe ref, NOT our TX number
                        if($isNB) {
                            $pmClass='pm-nb'; $pmIcon='fa-university'; $pmText=$nbLabel?:'Net Banking';
                            $pmSub=''; $refDisplay=$approvalNo?:$realUTR?:$txnRef; $refLabel='REF';
                        } elseif($isUPI) {
                            $pmClass='pm-upi'; $pmIcon='fa-mobile'; $pmText='UPI';
                            $pmSub=''; $refDisplay=$realUTR?:$txnRef; $refLabel='REF';
                        } elseif($isCard) {
                            $pmClass='pm-card'; $pmIcon='fa-credit-card'; $pmText=$cardName?:'Card';
                            $pmSub=$cardNo?'•••• '.$cardNo:''; $refDisplay=$approvalNo?:$realUTR?:$txnRef; $refLabel='REF';
                        } else {
                            $pmClass='pm-default'; $pmIcon='fa-money'; $pmText=$pm?:'-';
                            $pmSub=''; $refDisplay=$realUTR?:$txnRef; $refLabel='REF';
                        }
                    ?>
                    <tr class="pr-row" data-status="<?php echo $st; ?>" data-device="<?php echo htmlspecialchars($posDevice); ?>" data-branch="<?php echo htmlspecialchars($t['branch_name']); ?>" data-amt="<?php echo $amt; ?>" onclick="toggleDetail(<?php echo $i; ?>,this)">
                        <td><i class="fa fa-chevron-right exp-i"></i></td>
                        <td style="color:var(--c4);"><?php echo $i; ?></td>
                        <td class="c-bill"><?php if($hasBill): ?><a href="<?php echo $billLink; ?>" target="_blank" onclick="event.stopPropagation();"><?php echo htmlspecialchars($t['bill_number']); ?></a><span class="c-bill-type"><?php echo $t['bill_type_label']; ?></span><?php elseif($hasEst): ?><span style="color:var(--warn);font-weight:600;font-size:11px;"><?php echo htmlspecialchars($t['bill_number']); ?></span><span class="c-bill-type">Estimate</span><?php else: ?><span class="c-none">—</span><?php endif; ?></td>
                        <td><span class="c-name"><?php echo htmlspecialchars($t['customer_name']); ?></span><?php if(!empty($t['customer_mobile'])): ?><span class="c-sub"><?php echo $t['customer_mobile']; ?></span><?php endif; ?></td>
                        <td><span class="pm-tag <?php echo $pmClass; ?>"><i class="fa <?php echo $pmIcon; ?>"></i> <?php echo $pmText; ?></span><?php if($pmSub): ?><span class="pm-sub"><?php echo $pmSub; ?></span><?php endif; ?></td>
                        <td class="c-amt">₹<?php echo number_format($amt, 2); ?></td>
                        <td><?php if($refDisplay): ?><span class="c-ref-label"><?php echo $refLabel; ?></span><span class="c-ref" title="<?php echo htmlspecialchars($refDisplay); ?>"><?php echo htmlspecialchars($refDisplay); ?></span><?php else: ?><span class="c-none">—</span><?php endif; ?></td>
                        <td><span class="st st-<?php echo $st; ?>"><span class="st-dot"></span> <?php echo $stLabel; ?></span></td>
                        <td class="c-dev"><?php echo htmlspecialchars($posDevice); ?></td>
                        <td class="c-time"><?php echo $payTime; ?></td>
                    </tr>
                    <tr class="pr-detail" id="detail_<?php echo $i; ?>"><td colspan="<?php echo $COL_SPAN; ?>"><div class="pr-detail-inner">
                        <div class="d-panel">
                            <div class="d-hd d-hd-txn"><i class="fa fa-exchange"></i> Transaction</div>
                            <div class="d-row"><span class="d-lbl">Internal ID</span><span class="d-val"><code><?php echo $t['pos_trans_no']?:'-'; ?></code></span></div>
                            <div class="d-row"><span class="d-lbl">Provider</span><span class="d-val"><?php echo htmlspecialchars($t['provider_name']); ?></span></div>
                            <div class="d-row"><span class="d-lbl">Bill Type</span><span class="d-val"><?php echo $t['bill_type_label']?:'Direct Payment'; ?></span></div>
                            <div class="d-row"><span class="d-lbl">Initiated</span><span class="d-val d-val-time"><?php echo $initiatedFmt; ?></span></div>
                            <?php if($confirmedAt): ?><div class="d-row"><span class="d-lbl">Confirmed</span><span class="d-val d-val-ok d-val-time"><?php echo $confirmedAt; ?></span></div><?php endif; ?>
                            <div class="d-row"><span class="d-lbl">Device</span><span class="d-val"><?php echo htmlspecialchars($posDevice); ?></span></div>
                        </div>
                        <div class="d-panel">
                            <div class="d-hd d-hd-pp"><i class="fa fa-shield"></i> Payment Details</div>
                            <?php // --- Bank/Gateway Reference (single source of truth) --- ?>
                            <?php $bestRef = $ppRefId ?: $utr ?: $approvalNo; ?>
                            <?php if($bestRef): ?>
                            <div class="d-row"><span class="d-lbl">Bank Ref</span><span class="d-val"><code><?php echo htmlspecialchars($bestRef); ?></code></span></div>
                            <?php endif; ?>
                            <?php if($ppState): ?><div class="d-row"><span class="d-lbl">Status</span><span class="d-val<?php echo $ppState=='COMPLETED'?' d-val-ok':''; ?>"><?php echo $ppState; ?></span></div><?php endif; ?>
                            <?php if($ppResponseCode): ?><div class="d-row"><span class="d-lbl">Response</span><span class="d-val<?php echo $ppResponseCode=='SUCCESS'?' d-val-ok':''; ?>"><?php echo $ppResponseCode; ?></span></div><?php endif; ?>
                            <?php // --- Card-specific fields --- ?>
                            <?php if($isCard): ?>
                            <div class="d-row"><span class="d-lbl">Card</span><span class="d-val"><?php echo ($cardName?:'-').($cardNo ? ' · •••• '.$cardNo : ''); ?></span></div>
                            <?php if($cardNetwork||$cardType): ?><div class="d-row"><span class="d-lbl">Network</span><span class="d-val"><?php echo $cardNetwork.($cardType?' · '.$cardType:''); ?></span></div><?php endif; ?>
                            <?php if($approvalNo && $approvalNo != $bestRef): ?><div class="d-row"><span class="d-lbl">Approval</span><span class="d-val"><code><?php echo $approvalNo; ?></code></span></div><?php endif; ?>
                            <?php endif; ?>
                            <?php // --- NB-specific fields --- ?>
                            <?php if($isNB && $nbLabel): ?>
                            <div class="d-row"><span class="d-lbl">NB Type</span><span class="d-val"><?php echo $nbLabel; ?></span></div>
                            <?php endif; ?>
                            <?php if($payDevice): ?><div class="d-row"><span class="d-lbl">Pay Device</span><span class="d-val"><?php echo htmlspecialchars($payDevice); ?></span></div><?php endif; ?>
                        </div>
                        <div class="d-panel">
                            <div class="d-hd d-hd-cust"><i class="fa fa-user"></i> People</div>
                            <div class="d-row"><span class="d-lbl">Customer</span><span class="d-val" style="font-weight:600;"><?php echo htmlspecialchars($t['customer_name']); ?></span></div>
                            <?php if(!empty($t['customer_mobile'])): ?><div class="d-row"><span class="d-lbl">Mobile</span><span class="d-val"><?php echo $t['customer_mobile']; ?></span></div><?php endif; ?>
                            <div class="d-row"><span class="d-lbl">Cashier</span><span class="d-val"><?php echo htmlspecialchars($t['staff_name']); ?></span></div>
                            <div class="d-row"><span class="d-lbl">Branch</span><span class="d-val"><?php echo htmlspecialchars($t['branch_name']); ?></span></div>
                        </div>
                    </div></td></tr>
                    <?php $i++; endforeach; else: ?>
                    <tr><td colspan="<?php echo $COL_SPAN; ?>" class="pr-empty"><i class="fa fa-inbox"></i><br>No payments found for this date</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Modern Pagination Footer -->
        <?php if(!empty($txns)): ?>
        <div class="pr-table-footer">
            <div class="pr-entries">
                Show <select id="pr_per_page" onchange="changePage(1)"><option value="10">10</option><option value="25" selected>25</option><option value="50">50</option><option value="100">100</option><option value="all">All</option></select> entries
            </div>
            <div class="pr-page-info" id="pr_page_info">Showing 1 to <?php echo min(25, count($txns)); ?> of <?php echo count($txns); ?> entries</div>
            <div class="pr-pagination" id="pr_pagination"></div>
        </div>
        <?php endif; ?>
    </div>

    <!-- SETTLEMENT -->
    <?php if(!empty($breakdown)): ?>
    <div class="pr-settle"><div class="pr-card">
        <div class="pr-card-hdr" style="cursor:pointer;" onclick="$('#settle_body').slideToggle(200);">
            <span class="pr-card-title"><i class="fa fa-bar-chart"></i> Device-wise Settlement Summary</span>
            <i class="fa fa-chevron-down" style="color:var(--c4);font-size:12px;"></i>
        </div>
        <div id="settle_body"><div style="overflow-x:auto;"><table class="stbl" id="settle_table"><thead><tr>
            <th>Device</th><th>Provider</th><th style="text-align:center;">Total</th><th style="text-align:center;">Success</th><th style="text-align:right;">Amount</th><th style="text-align:center;">Failed</th><th style="text-align:center;">Pending</th>
        </tr></thead><tbody>
            <?php $ft=array('total'=>0,'success'=>0,'s_amt'=>0,'failed'=>0,'pending'=>0);
            foreach($breakdown as $bk):
                $ft['total']+=intval($bk['total']); $ft['success']+=intval($bk['success']);
                $ft['s_amt']+=floatval($bk['success_amount']); $ft['failed']+=intval($bk['failed']); $ft['pending']+=intval($bk['pending']); ?>
            <tr>
                <td style="font-weight:600;"><?php echo htmlspecialchars($bk['device_name']); ?></td>
                <td class="c-dev"><?php echo htmlspecialchars($bk['provider_name']); ?></td>
                <td style="text-align:center;"><?php echo $bk['total']; ?></td>
                <td style="text-align:center;color:var(--ok);"><?php echo $bk['success']; ?></td>
                <td style="text-align:right;font-weight:600;">₹<?php echo number_format(floatval($bk['success_amount'])/100,2); ?></td>
                <td style="text-align:center;<?php echo intval($bk['failed'])>0?'color:var(--err);':''; ?>"><?php echo $bk['failed']; ?></td>
                <td style="text-align:center;<?php echo intval($bk['pending'])>0?'color:var(--warn);font-weight:700;':''; ?>"><?php echo $bk['pending']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody><tfoot><tr>
            <td colspan="2" style="font-weight:700;">Total</td>
            <td style="text-align:center;"><?php echo $ft['total']; ?></td>
            <td style="text-align:center;color:var(--ok);"><?php echo $ft['success']; ?></td>
            <td style="text-align:right;font-weight:700;">₹<?php echo number_format($ft['s_amt']/100,2); ?></td>
            <td style="text-align:center;"><?php echo $ft['failed']; ?></td>
            <td style="text-align:center;"><?php echo $ft['pending']; ?></td>
        </tr></tfoot></table></div></div>
    </div></div>
    <?php endif; ?>

</div>
</section>
</div>

<script>
var _activeStatus='all', _baseUrl='<?php echo $baseUrl; ?>', _curPage=1, _perPage=25;
var _dateFrom='<?php echo $dateVal; ?>', _dateTo='<?php echo $dateToVal; ?>';

(function waitForJquery(){
    if(typeof jQuery==='undefined'){ setTimeout(waitForJquery,50); return; }
    $(function(){
        // Date range picker using already-loaded moment.js + daterangepicker
        if(typeof $.fn.daterangepicker !== 'undefined'){
            var startDate = moment(_dateFrom, 'DD-MM-YYYY');
            var endDate = _dateTo ? moment(_dateTo, 'DD-MM-YYYY') : startDate.clone();
            $('#pr_daterange').daterangepicker({
                startDate: startDate,
                endDate: endDate,
                locale: { format:'DD-MM-YYYY', separator:' → ', applyLabel:'Apply', cancelLabel:'Single Date' },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1,'days'), moment().subtract(1,'days')],
                    'Last 7 Days': [moment().subtract(6,'days'), moment()],
                    'Last 30 Days': [moment().subtract(29,'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
                },
                opens:'left', autoApply:false, showDropdowns:true
            }, function(start, end) {
                _dateFrom = start.format('DD-MM-YYYY');
                _dateTo = end.format('DD-MM-YYYY');
                if(_dateFrom === _dateTo) {
                    $('#pr_daterange').val(_dateFrom);
                    _dateTo = '';
                } else {
                    $('#pr_daterange').val(_dateFrom + ' → ' + _dateTo);
                }
            });
            // Handle cancel = single date mode
            $('#pr_daterange').on('cancel.daterangepicker', function(ev, picker) {
                _dateTo = '';
                $('#pr_daterange').val(_dateFrom);
            });
        } else {
            // Fallback to basic datepicker
            $('#pr_daterange').datepicker({format:'dd-mm-yyyy',autoclose:true,todayHighlight:true}).on('changeDate',function(){ _dateFrom=$('#pr_daterange').val(); _dateTo=''; loadReport(); });
        }
        $(document).on('keydown',function(e){ if(e.key==='Escape') closeAll(); });
        // Column sorting
        $('.sortable').on('click', function(){ sortColumn($(this)); });
        // Init pagination
        initPagination();
    });
})();

/* Navigation */
function loadReport(){
    var url = _baseUrl+'admin_pos/posTransactions?date='+encodeURIComponent(_dateFrom);
    if(_dateTo && _dateTo !== _dateFrom) url += '&date_to='+encodeURIComponent(_dateTo);
    window.location.href = url;
}

/* Filtering */
function filterStatus(s,el){ _activeStatus=s; $('.tab').removeClass('active'); $(el).addClass('active'); applyFilters(); }
function applyFilters(){
    var s=_activeStatus, dev=$('#flt_device').val(), br=$('#flt_branch').val()||'', q=($('#pr_search').val()||'').toLowerCase();
    closeAll();
    $('.pr-row').each(function(){ var $r=$(this), show=true;
        if(s!='all'&&$r.data('status')!=parseInt(s)) show=false;
        if(dev&&$r.data('device')!=dev) show=false;
        if(br&&$r.data('branch')!=br) show=false;
        if(q&&$r.text().toLowerCase().indexOf(q)<0) show=false;
        $r.data('filtered', !show);
        $r.toggle(show); $r.next('.pr-detail').toggle(false);
    });
    _curPage=1; initPagination();
}

/* Pagination */
function initPagination(){
    var $rows = $('.pr-row').filter(function(){ return !$(this).data('filtered'); });
    var total = $rows.length;
    var pp = _perPage === 'all' ? total : parseInt(_perPage);
    var pages = Math.ceil(total / pp) || 1;
    if(_curPage > pages) _curPage = pages;
    // Hide all, show current page
    $rows.hide().each(function(idx){
        if(idx >= ((_curPage-1)*pp) && idx < (_curPage*pp)){
            $(this).show();
        }
    });
    // Update info
    var from = total ? ((_curPage-1)*pp + 1) : 0;
    var to = Math.min(_curPage*pp, total);
    $('#pr_page_info').text('Showing '+from+' to '+to+' of '+total+' entries');
    // Build pagination buttons
    var html = '<button '+((_curPage<=1)?'disabled':'')+' onclick="changePage('+(_curPage-1)+')">‹</button>';
    var startP = Math.max(1, _curPage-2), endP = Math.min(pages, _curPage+2);
    if(startP > 1) html += '<button onclick="changePage(1)">1</button>';
    if(startP > 2) html += '<button disabled>…</button>';
    for(var p=startP; p<=endP; p++){
        html += '<button class="'+(p===_curPage?'active':'')+'" onclick="changePage('+p+')">'+p+'</button>';
    }
    if(endP < pages-1) html += '<button disabled>…</button>';
    if(endP < pages) html += '<button onclick="changePage('+pages+')">'+pages+'</button>';
    html += '<button '+((_curPage>=pages)?'disabled':'')+' onclick="changePage('+(_curPage+1)+')">›</button>';
    $('#pr_pagination').html(html);
}
function changePage(p){ _curPage=p; _perPage=$('#pr_per_page').val(); initPagination(); closeAll(); }

/* Column Sorting */
function sortColumn($th){
    var dir = $th.hasClass('sort-asc') ? 'desc' : 'asc';
    $('.sortable').removeClass('sort-asc sort-desc');
    $th.addClass('sort-'+dir);
    var colIdx = $th.index();
    var $tbody = $('#pr_table tbody');
    var $rows = $tbody.find('tr.pr-row');
    var pairs = [];
    $rows.each(function(){ pairs.push({row:$(this), detail:$(this).next('.pr-detail')}); });
    pairs.sort(function(a,b){
        var aVal = a.row.find('td').eq(colIdx).text().trim();
        var bVal = b.row.find('td').eq(colIdx).text().trim();
        // Try numeric
        var aNum = parseFloat(aVal.replace(/[₹,]/g,''));
        var bNum = parseFloat(bVal.replace(/[₹,]/g,''));
        if(!isNaN(aNum) && !isNaN(bNum)) return dir==='asc' ? aNum-bNum : bNum-aNum;
        return dir==='asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
    });
    pairs.forEach(function(p){ $tbody.append(p.row); $tbody.append(p.detail); });
    _curPage=1; initPagination();
}

/* Detail expand */
function toggleDetail(n,row){ var r=$('#detail_'+n),$row=$(row);
    if(r.is(':visible')){ r.slideUp(120); $row.removeClass('expanded'); }
    else { $('.pr-detail').slideUp(120); $('.pr-row').removeClass('expanded'); r.slideDown(150); $row.addClass('expanded'); }
}
function closeAll(){ $('.pr-detail').slideUp(120); $('.pr-row').removeClass('expanded'); }

/* Export CSV */
function exportCSV(){
    var csv = 'Bill No,Customer,Payment Mode,Amount,UTR/Reference,Status,Device,Time\n';
    $('#pr_table tbody tr.pr-row').each(function(){
        var cols = []; $(this).find('td').each(function(i){ if(i>0) cols.push('"'+$(this).text().trim().replace(/"/g,'""')+'"'); });
        csv += cols.join(',') + '\n';
    });
    var blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'POS_Report_'+_dateFrom+'.csv';
    link.click();
}

/* Print */
function printReport(){
    var dt=$('#pr_daterange').val(), w=window.open('','_blank');
    var css='body{font-family:Arial,sans-serif;margin:20px;color:#333;}h2{font-size:16px;margin-bottom:4px;}h3{font-size:13px;color:#666;margin-top:0;}table{width:100%;border-collapse:collapse;margin:10px 0;}th,td{border:1px solid #ddd;padding:6px 8px;text-align:left;font-size:11px;}th{background:#1e293b;color:#fff;font-weight:600;}';
    var company='<?php echo addslashes($companyName); ?>';
    var html='<html><head><title>POS Collection Dashboard - '+dt+'</title><style>'+css+'</style></head><body><h2>'+company+' — POS Collection Dashboard</h2><h3>Digital Payments Ledger | '+dt+' | Generated: '+new Date().toLocaleString()+'</h3><table><thead>'+$('#pr_table thead').html()+'</thead><tbody>';
    $('#pr_table tbody tr.pr-row').each(function(){html+='<tr>'+$(this).html()+'</tr>';}); html+='</tbody></table>';
    if($('#settle_table').length) html+='<h3 style="margin-top:20px;">Device Settlement</h3>'+document.getElementById('settle_table').outerHTML;
    html+='</body></html>'; w.document.write(html); w.document.close(); w.print();
}

/* Reconcile */
function runReconcile(){ var btn=$('#btn_reconcile'); btn.prop('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Checking...');
    $.ajax({url:_baseUrl+'admin_pos/runReconciliation',type:'POST',dataType:'json',
        success:function(r){btn.prop('disabled',false).html('<i class="fa fa-refresh"></i> Reconcile');
            if(r.status=='success'){$.toaster({priority:'success',title:'Done',message:'</br>Checked: '+(r.results?.checked||0)+', Recovered: '+(r.results?.recovered||0)});loadReport();}
            else $.toaster({priority:'danger',title:'Error',message:'</br>'+(r.message||'Failed')});},
        error:function(){btn.prop('disabled',false).html('<i class="fa fa-refresh"></i> Reconcile');$.toaster({priority:'danger',title:'Error',message:'</br>Network error'});}
    });
}
</script>
