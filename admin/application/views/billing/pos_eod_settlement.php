<!-- POS EOD Settlement Summary [BIL-NR01] -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>Settlement Summary <small>Daily POS Reconciliation</small></h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> POS</a></li>
            <li class="active">Settlement Summary</li>
            <li class="pull-right" style="float:right;">
                <a href="<?php echo base_url(); ?>index.php/admin_pos/posSettings" class="btn btn-sm btn-default">
                    <i class="fa fa-cog"></i> POS Settings
                </a>
                <a href="<?php echo base_url(); ?>index.php/admin_pos/posTransactions" class="btn btn-sm btn-info">
                    <i class="fa fa-list-alt"></i> Transaction Log
                </a>
            </li>
        </ol>
    </section>

    <section class="content">

        <!-- DATE FILTER -->
        <div class="box box-default">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Settlement Date</label>
                            <input type="text" class="form-control" id="eod_date" 
                                   value="<?php echo date('d-m-Y'); ?>" readonly
                                   style="cursor:pointer;" />
                        </div>
                    </div>
                    <div class="col-sm-2" style="padding-top:25px;">
                        <button class="btn btn-primary" onclick="loadSettlement()">
                            <i class="fa fa-search"></i> Load
                        </button>
                        <button class="btn btn-default" onclick="printSettlement()">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                    <div class="col-sm-3" style="padding-top:25px;">
                        <button class="btn btn-warning" onclick="runReconciliation()" id="btn_reconcile">
                            <i class="fa fa-refresh"></i> Reconcile Pending
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="row" id="summary_cards">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3 id="card_total_txn">0</h3>
                        <p>Total Transactions</p>
                    </div>
                    <div class="icon"><i class="fa fa-credit-card"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3 id="card_success_amt">₹0</h3>
                        <p>Successful Collections</p>
                    </div>
                    <div class="icon"><i class="fa fa-check-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3 id="card_failed">0</h3>
                        <p>Failed Transactions</p>
                    </div>
                    <div class="icon"><i class="fa fa-times-circle"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3 id="card_pending">0</h3>
                        <p>Pending ⚠️</p>
                    </div>
                    <div class="icon"><i class="fa fa-clock-o"></i></div>
                </div>
            </div>
        </div>

        <!-- DEVICE-WISE BREAKDOWN -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> Device-wise Breakdown</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tbl_eod_breakdown">
                        <thead>
                            <tr>
                                <th>Device</th>
                                <th>Provider</th>
                                <th class="text-center">Total</th>
                                <th class="text-center text-green">Success</th>
                                <th class="text-center text-green">Success ₹</th>
                                <th class="text-center text-red">Failed</th>
                                <th class="text-center text-yellow">Pending</th>
                                <th class="text-center"><strong>Net ₹</strong></th>
                            </tr>
                        </thead>
                        <tbody id="eod_breakdown_body">
                            <tr><td colspan="8" class="text-center text-muted">Click "Load" to view settlement data</td></tr>
                        </tbody>
                        <tfoot id="eod_breakdown_footer" style="display:none;">
                            <tr class="active" style="font-weight:bold;">
                                <td colspan="2"><strong>TOTAL</strong></td>
                                <td class="text-center" id="foot_total">0</td>
                                <td class="text-center text-green" id="foot_success">0</td>
                                <td class="text-center text-green" id="foot_success_amt">₹0</td>
                                <td class="text-center text-red" id="foot_failed">0</td>
                                <td class="text-center text-yellow" id="foot_pending">0</td>
                                <td class="text-center" id="foot_net">₹0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- TRANSACTION LIST FOR SELECTED DATE -->
        <div class="box box-info" id="txn_detail_box" style="display:none;">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-list"></i> Transactions — <span id="txn_detail_date"></span></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tbl_eod_transactions">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Txn Ref</th>
                                <th>Device</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Bill ID</th>
                                <th>Initiated By</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="eod_txn_body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</div>

<script>
$(function(){
    // Date picker
    $('#eod_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    });
    
    // Auto-load on page open
    loadSettlement();
});

function loadSettlement(){
    var dateStr = $('#eod_date').val();
    
    // Load summary cards + breakdown
    $.ajax({
        url: base_url + 'index.php/admin_pos/getEODData',
        type: 'POST',
        data: { date: dateStr },
        dataType: 'json',
        beforeSend: function(){
            $('#eod_breakdown_body').html('<tr><td colspan="8" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
        },
        success: function(res){
            if(res.status == 'success'){
                renderSummaryCards(res.summary);
                renderBreakdown(res.breakdown);
                renderTransactions(res.transactions, dateStr);
            } else {
                $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + (res.message || 'Failed to load data') });
            }
        },
        error: function(xhr){
            $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + xhr.statusText });
        }
    });
}

function renderSummaryCards(s){
    $('#card_total_txn').text(s.total || 0);
    $('#card_success_amt').text('₹' + formatAmount(s.success_amount || 0));
    $('#card_failed').text(s.failed || 0);
    $('#card_pending').text(s.pending || 0);
}

function renderBreakdown(data){
    var html = '';
    var totals = { total:0, success:0, success_amt:0, failed:0, pending:0, net:0 };
    
    if(!data || data.length == 0){
        html = '<tr><td colspan="8" class="text-center text-muted">No transactions for this date</td></tr>';
        $('#eod_breakdown_footer').hide();
    } else {
        $.each(data, function(i, row){
            html += '<tr>';
            html += '<td><strong>' + row.device_name + '</strong></td>';
            html += '<td><span class="label label-primary">' + row.provider_name + '</span></td>';
            html += '<td class="text-center">' + row.total + '</td>';
            html += '<td class="text-center text-green">' + row.success + '</td>';
            html += '<td class="text-center text-green">₹' + formatAmount(row.success_amount) + '</td>';
            html += '<td class="text-center text-red">' + row.failed + '</td>';
            html += '<td class="text-center">' + (row.pending > 0 ? '<span class="label label-warning">' + row.pending + ' ⚠️</span>' : '0') + '</td>';
            html += '<td class="text-center"><strong>₹' + formatAmount(row.success_amount) + '</strong></td>';
            html += '</tr>';
            
            totals.total += parseInt(row.total) || 0;
            totals.success += parseInt(row.success) || 0;
            totals.success_amt += parseFloat(row.success_amount) || 0;
            totals.failed += parseInt(row.failed) || 0;
            totals.pending += parseInt(row.pending) || 0;
            totals.net += parseFloat(row.success_amount) || 0;
        });
        
        $('#foot_total').text(totals.total);
        $('#foot_success').text(totals.success);
        $('#foot_success_amt').text('₹' + formatAmount(totals.success_amt));
        $('#foot_failed').text(totals.failed);
        $('#foot_pending').text(totals.pending);
        $('#foot_net').text('₹' + formatAmount(totals.net));
        $('#eod_breakdown_footer').show();
    }
    
    $('#eod_breakdown_body').html(html);
}

function renderTransactions(data, dateStr){
    if(!data || data.length == 0){
        $('#txn_detail_box').hide();
        return;
    }
    
    var html = '';
    $.each(data, function(i, t){
        var statusLabel = '';
        switch(parseInt(t.pos_req_status)){
            case 1: statusLabel = '<span class="label label-success">Success</span>'; break;
            case 2: statusLabel = '<span class="label label-danger">Failed</span>'; break;
            case 3: statusLabel = '<span class="label label-default">Cancelled</span>'; break;
            default: statusLabel = '<span class="label label-warning">Pending</span>';
        }
        
        html += '<tr>';
        html += '<td>' + (i+1) + '</td>';
        html += '<td><code>' + (t.pos_res_ref_id || '-') + '</code></td>';
        html += '<td>' + (t.device_name || '-') + '</td>';
        html += '<td>₹' + formatAmount(t.pos_req_amount) + '</td>';
        html += '<td>' + statusLabel + '</td>';
        html += '<td>' + (t.pos_req_bill_id || '-') + '</td>';
        html += '<td>' + (t.created_by_name || '-') + '</td>';
        html += '<td><small>' + (t.pos_req_createdon || '-') + '</small></td>';
        html += '</tr>';
    });
    
    $('#txn_detail_date').text(dateStr);
    $('#eod_txn_body').html(html);
    $('#txn_detail_box').show();
}

function formatAmount(paise){
    var amt = parseFloat(paise) / 100;
    return amt.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function printSettlement(){
    var dateStr = $('#eod_date').val();
    var printWin = window.open('', '_blank');
    var content = '<html><head><title>POS Settlement - ' + dateStr + '</title>';
    content += '<style>body{font-family:Arial;margin:20px;} table{width:100%;border-collapse:collapse;margin:15px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f5f5f5;} .text-right{text-align:right;} .text-center{text-align:center;} h2{margin-bottom:5px;} .summary{display:flex;gap:20px;margin:15px 0;} .card{border:1px solid #ddd;padding:10px 15px;border-radius:4px;flex:1;text-align:center;} .card h3{margin:0;} .card p{margin:5px 0 0;color:#666;}</style>';
    content += '</head><body>';
    content += '<h2>POS Settlement Summary</h2>';
    content += '<p>Date: ' + dateStr + ' | Generated: ' + new Date().toLocaleString() + '</p>';
    content += '<hr>';
    content += document.getElementById('tbl_eod_breakdown').outerHTML;
    content += '</body></html>';
    printWin.document.write(content);
    printWin.document.close();
    printWin.print();
}

function runReconciliation(){
    var btn = $('#btn_reconcile');
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Reconciling...');
    
    $.ajax({
        url: base_url + 'index.php/admin_pos/runReconciliation',
        type: 'POST',
        dataType: 'json',
        success: function(res){
            btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Reconcile Pending');
            if(res.status == 'success'){
                var r = res.results;
                var msg = 'Checked: ' + r.checked + ' | Recovered: ' + r.recovered + ' | Failed: ' + r.failed;
                if(r.recovered > 0){
                    $.toaster({ priority: 'success', title: 'Reconciliation Complete!', message: '</br>' + msg });
                } else if(r.checked == 0){
                    $.toaster({ priority: 'info', title: 'All Clear', message: '</br>No pending payments to reconcile.' });
                } else {
                    $.toaster({ priority: 'info', title: 'Reconciliation Done', message: '</br>' + msg });
                }
                // Reload settlement data
                loadSettlement();
            } else {
                $.toaster({ priority: 'danger', title: 'Error!', message: '</br>' + (res.message || 'Reconciliation failed') });
            }
        },
        error: function(){
            btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Reconcile Pending');
            $.toaster({ priority: 'danger', title: 'Error!', message: '</br>Failed to run reconciliation' });
        }
    });
}
</script>
