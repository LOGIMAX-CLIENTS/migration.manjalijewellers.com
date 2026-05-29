<html>
<head>
    <meta charset="utf-8">
    <title>Sales Transfer Invoice - <?php echo $print_data['header']['bill_no']; ?></title>
    <style type="text/css">
        body, html { margin: 0; padding: 10px; font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
        .print-container { max-width: 800px; margin: 0 auto; border: 1px solid #000; padding: 15px; }
        table { width: 100%; border-collapse: collapse; }
        .header-table td { padding: 3px 5px; vertical-align: top; }
        .header-title { text-align: center; font-size: 18px; font-weight: bold; margin: 10px 0 5px; text-transform: uppercase; }
        .header-subtitle { text-align: center; font-size: 12px; color: #444; margin-bottom: 10px; }
        .details-table { margin: 10px 0; }
        .details-table th, .details-table td { border: 1px solid #333; padding: 4px 6px; text-align: center; font-size: 10px; }
        .details-table th { background: #f0f0f0; font-weight: bold; text-transform: uppercase; }
        .details-table td { vertical-align: middle; }
        .details-table tfoot td { font-weight: bold; background: #f5f5f5; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .text-center { text-align: center !important; }
        .info-block { border: 1px solid #ccc; padding: 8px; margin-bottom: 10px; }
        .info-block h4 { margin: 0 0 5px; font-size: 11px; text-transform: uppercase; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 3px; }
        .info-block p { margin: 2px 0; }
        .gst-label { font-weight: bold; color: #d32f2f; }
        .hsn-col { background: #fffde7 !important; }
        .type-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; color: #fff; }
        .type-1 { background: #1976d2; }
        .type-2 { background: #00897b; }
        .type-3 { background: #f57c00; }
        .type-4 { background: #d32f2f; }
        .type-5 { background: #616161; }
        .tax-section { margin-top: 10px; }
        .tax-section table { width: 50%; float: right; }
        .tax-section td { padding: 3px 6px; border: 1px solid #ccc; }
        .signature-row { margin-top: 40px; clear: both; }
        .signature-row td { padding-top: 30px; text-align: center; border-top: 1px solid #333; width: 33%; }
        .clearfix::after { content: ""; clear: both; display: table; }
        .no-print { margin-bottom: 10px; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 5px; }
        }
    </style>
</head>
<body>
<?php
    $h = $print_data['header'];
    $items = $print_data['items'];
    $typeLabels = array(1 => 'Tagged', 2 => 'Non-Tagged', 3 => 'Old Gold', 4 => 'Sales Return', 5 => 'Partly Sold');
    $billTypeLabel = ($h['bill_type'] == 14) ? 'SALES RETURN TRANSFER INVOICE' : 'DEEMED SALES TRANSFER INVOICE';

    // Determine tax type
    $isIntra = ($h['from_state'] == $h['to_state']);
?>

<!-- Print button -->
<div class="no-print text-center">
    <button onclick="window.print();" style="padding:8px 20px; font-size:14px; cursor:pointer; background:#1976d2; color:#fff; border:none; border-radius:4px;">
        <i class="fa fa-print"></i> Print Invoice
    </button>
    <button onclick="window.close();" style="padding:8px 20px; font-size:14px; cursor:pointer; background:#666; color:#fff; border:none; border-radius:4px; margin-left:10px;">
        Close
    </button>
</div>

<div class="print-container">
    <!-- Company Header -->
    <div class="header-title"><?php echo $h['company_name'] ?: 'Company Name'; ?></div>
    <div class="header-subtitle"><?php echo $billTypeLabel; ?></div>

    <!-- From / To Info -->
    <table class="header-table" style="margin-bottom:10px;">
        <tr>
            <td width="50%">
                <div class="info-block">
                    <h4>From (Consignor)</h4>
                    <p><strong><?php echo $h['from_branch_name']; ?></strong></p>
                    <p><?php echo $h['from_address'] ?: ''; ?></p>
                    <p class="gst-label">GSTIN: <?php echo $h['from_gst'] ?: 'N/A'; ?></p>
                </div>
            </td>
            <td width="50%">
                <div class="info-block">
                    <h4>To (Consignee)</h4>
                    <p><strong><?php echo $h['to_branch_name']; ?></strong></p>
                    <p><?php echo $h['to_address'] ?: ''; ?></p>
                    <p class="gst-label">GSTIN: <?php echo $h['to_gst'] ?: 'N/A'; ?></p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Invoice Meta -->
    <table class="header-table" style="border:1px solid #ccc; margin-bottom:10px;">
        <tr>
            <td><strong>Invoice No:</strong> <?php echo $h['bill_no']; ?></td>
            <td><strong>Date:</strong> <?php echo $h['bill_date']; ?></td>
            <td><strong>Created By:</strong> <?php echo $h['created_by_name'] ?: '-'; ?></td>
        </tr>
        <tr>
            <td><strong>Direction:</strong> <?php echo ($h['bill_type'] == 14) ? 'Sales Return Transfer' : 'Sales Transfer'; ?></td>
            <td><strong>Gold Rate:</strong> ₹<?php echo number_format(floatval($h['goldrate_22ct']), 2); ?>/gm</td>
            <td><strong>Tax Type:</strong> <?php echo $isIntra ? 'Intra-State (SGST+CGST)' : 'Inter-State (IGST)'; ?></td>
        </tr>
        <?php if (!empty($h['download_date'])) { ?>
        <tr>
            <td colspan="2"><strong>Downloaded:</strong> <?php echo $h['download_date']; ?> by <?php echo $h['download_by_name'] ?: '-'; ?></td>
            <td><strong>Status:</strong> <span style="color:green; font-weight:bold;">Downloaded ✓</span></td>
        </tr>
        <?php } else { ?>
        <tr>
            <td colspan="3"><strong>Status:</strong> <span style="color:orange; font-weight:bold;">⏳ In Transit / Pending Download</span></td>
        </tr>
        <?php } ?>
    </table>

    <!-- Line Items Table -->
    <table class="details-table">
        <thead>
            <tr>
                <th width="4%">S.No</th>
                <th width="8%">Type</th>
                <th width="10%">Tag Code</th>
                <th width="12%">Product</th>
                <th width="8%" class="hsn-col">HSN</th>
                <th width="5%">Pcs</th>
                <th width="7%">Gr.Wt</th>
                <th width="7%">Nt.Wt</th>
                <th width="7%">Rate/gm</th>
                <th width="8%">Taxable</th>
                <?php if ($isIntra) { ?>
                    <th width="6%">SGST</th>
                    <th width="6%">CGST</th>
                <?php } else { ?>
                    <th width="6%">IGST</th>
                <?php } ?>
                <th width="8%">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $totPcs = 0; $totGross = 0; $totNet = 0; $totTaxable = 0;
            $totSgst = 0; $totCgst = 0; $totIgst = 0; $totCost = 0;
            $hsnBreakup = array(); // HSN-wise accumulation
            $i = 1;

            foreach ($items as $item) {
                $transType = $item['salesTransType'] ?: 1;
                $typeLabel = isset($typeLabels[$transType]) ? $typeLabels[$transType] : 'Tagged';
                $taxable = $item['item_cost'] - $item['item_total_tax'];

                $totPcs     += $item['piece'];
                $totGross   += $item['gross_wt'];
                $totNet     += $item['net_wt'];
                $totTaxable += $taxable;
                $totSgst    += $item['total_sgst'];
                $totCgst    += $item['total_cgst'];
                $totIgst    += $item['total_igst'];
                $totCost    += $item['item_cost'];

                // HSN accumulation
                $hsn = $item['hsn_code'] ?: 'N/A';
                if (!isset($hsnBreakup[$hsn])) {
                    $hsnBreakup[$hsn] = array('taxable' => 0, 'sgst' => 0, 'cgst' => 0, 'igst' => 0, 'total' => 0);
                }
                $hsnBreakup[$hsn]['taxable'] += $taxable;
                $hsnBreakup[$hsn]['sgst']    += $item['total_sgst'];
                $hsnBreakup[$hsn]['cgst']    += $item['total_cgst'];
                $hsnBreakup[$hsn]['igst']    += $item['total_igst'];
                $hsnBreakup[$hsn]['total']   += $item['item_cost'];
        ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><span class="type-badge type-<?php echo $transType; ?>"><?php echo $typeLabel; ?></span></td>
                <td class="text-left"><?php echo $item['tag_code'] ?: '-'; ?></td>
                <td class="text-left"><?php echo $item['product_name']; ?><br><small><?php echo $item['design_name']; ?></small></td>
                <td class="hsn-col"><?php echo $hsn; ?></td>
                <td><?php echo $item['piece']; ?></td>
                <td><?php echo number_format($item['gross_wt'], 3); ?></td>
                <td><?php echo number_format($item['net_wt'], 3); ?></td>
                <td class="text-right"><?php echo number_format($item['rate_per_grm'], 2); ?></td>
                <td class="text-right"><?php echo number_format($taxable, 2); ?></td>
                <?php if ($isIntra) { ?>
                    <td class="text-right"><?php echo number_format($item['total_sgst'], 2); ?></td>
                    <td class="text-right"><?php echo number_format($item['total_cgst'], 2); ?></td>
                <?php } else { ?>
                    <td class="text-right"><?php echo number_format($item['total_igst'], 2); ?></td>
                <?php } ?>
                <td class="text-right"><strong><?php echo number_format($item['item_cost'], 2); ?></strong></td>
            </tr>
        <?php $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                <td><strong><?php echo $totPcs; ?></strong></td>
                <td><strong><?php echo number_format($totGross, 3); ?></strong></td>
                <td><strong><?php echo number_format($totNet, 3); ?></strong></td>
                <td></td>
                <td class="text-right"><strong><?php echo number_format($totTaxable, 2); ?></strong></td>
                <?php if ($isIntra) { ?>
                    <td class="text-right"><strong><?php echo number_format($totSgst, 2); ?></strong></td>
                    <td class="text-right"><strong><?php echo number_format($totCgst, 2); ?></strong></td>
                <?php } else { ?>
                    <td class="text-right"><strong><?php echo number_format($totIgst, 2); ?></strong></td>
                <?php } ?>
                <td class="text-right"><strong>₹<?php echo number_format($totCost, 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- HSN Breakup -->
    <div style="margin-top:15px;">
        <h4 style="font-size:11px; margin-bottom:5px; text-transform:uppercase; border-bottom:1px solid #333; padding-bottom:3px;">HSN Summary</h4>
        <table class="details-table" style="width:70%;">
            <thead>
                <tr>
                    <th>HSN Code</th>
                    <th>Taxable Value</th>
                    <?php if ($isIntra) { ?>
                        <th>SGST (1.5%)</th>
                        <th>CGST (1.5%)</th>
                    <?php } else { ?>
                        <th>IGST (3%)</th>
                    <?php } ?>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($hsnBreakup as $hsn => $vals) { ?>
                <tr>
                    <td class="hsn-col"><strong><?php echo $hsn; ?></strong></td>
                    <td class="text-right"><?php echo number_format($vals['taxable'], 2); ?></td>
                    <?php if ($isIntra) { ?>
                        <td class="text-right"><?php echo number_format($vals['sgst'], 2); ?></td>
                        <td class="text-right"><?php echo number_format($vals['cgst'], 2); ?></td>
                    <?php } else { ?>
                        <td class="text-right"><?php echo number_format($vals['igst'], 2); ?></td>
                    <?php } ?>
                    <td class="text-right"><strong><?php echo number_format($vals['total'], 2); ?></strong></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Tax Summary -->
    <div class="clearfix tax-section">
        <table style="float:right; width:40%;">
            <tr><td class="text-left"><strong>Taxable Amount:</strong></td><td class="text-right">₹<?php echo number_format($totTaxable, 2); ?></td></tr>
            <?php if ($isIntra) { ?>
                <tr><td class="text-left">SGST @ 1.5%:</td><td class="text-right">₹<?php echo number_format($totSgst, 2); ?></td></tr>
                <tr><td class="text-left">CGST @ 1.5%:</td><td class="text-right">₹<?php echo number_format($totCgst, 2); ?></td></tr>
            <?php } else { ?>
                <tr><td class="text-left">IGST @ 3%:</td><td class="text-right">₹<?php echo number_format($totIgst, 2); ?></td></tr>
            <?php } ?>
            <tr style="font-weight:bold; background:#e3f2fd;"><td class="text-left">Grand Total:</td><td class="text-right">₹<?php echo number_format($totCost, 2); ?></td></tr>
        </table>
    </div>

    <?php if (!empty($h['remark'])) { ?>
    <div style="clear:both; margin-top:15px; padding:5px; border:1px solid #ddd;">
        <strong>Remarks:</strong> <?php echo htmlspecialchars($h['remark']); ?>
    </div>
    <?php } ?>

    <!-- Signature Block -->
    <table style="margin-top:50px; width:100%;">
        <tr class="signature-row">
            <td>Prepared By</td>
            <td>Checked By</td>
            <td>Received By</td>
        </tr>
    </table>

    <div style="text-align:center; margin-top:15px; font-size:9px; color:#999;">
        This is a computer-generated invoice. | Generated on: <?php echo date('d-m-Y H:i'); ?>
    </div>
</div>

<script>
    // Auto-print on load if query param present
    if (window.location.search.indexOf('autoprint=1') > -1) {
        window.onload = function() { window.print(); };
    }
</script>
</body>
</html>
