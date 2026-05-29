<?php
$css = <<<CSS
* { font-family: DejaVu Sans, sans-serif; box-sizing: border-box; }
body { margin: 0; padding: 0; font-size: 10px; line-height: 1.2; }
.wrapper-table { width: 100%; table-layout: fixed; border-collapse: collapse; margin-bottom: 10px; }
.wrapper-table td { vertical-align: top; padding: 5px; border: none; }
.table_heading { font-weight: bold; }
table#pp { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom:10px; }
table#pp th, table#pp td { padding: 3px; border: none; font-size: 10px; }

/* Dotted line for table head and foot (compatible with DOMPDF) */
.dotted-cell { border-top: 1px dashed black !important; border-bottom: 1px dashed black !important; padding-top:4px; padding-bottom:4px; }
.dotted-cell-top { border-top: 1px dashed black !important; padding-top:4px; }
.dotted-cell-bottom { border-bottom: 1px dashed black !important; padding-bottom:4px; }

.alignCenter { text-align: center; }
.alignRight { text-align: right; }
.alignLeft { text-align: left; }
.section-title { font-weight: bold; text-transform: uppercase; margin-top: 5px; font-size: 11px; }

/* Currency Alignment */
.currency-val { float: right; }
.currency-sym { float: left; margin-right: 5px; }
.table-summary { width:100%; border-collapse:collapse; font-size:12px; font-weight:bold; }
.table-summary td { padding:3px; }
.table-summary .label-col { text-align:right; width:65%; }
.table-summary .val-col { text-align:right; width:35%; }
CSS;

$html = <<<HTML
<div class="PDFReceipt">
	<div style="width:100%; text-align:center;" class="new_tax">
		<div style="text-decoration: underline; font-weight:bold; font-size:18px; text-transform:uppercase;">{{title}}</div>
	</div>

	<table class="wrapper-table">
		<tr>
			<td style="width:40%">
				<b>Mr/Mrs. {{customer_name}}</b><br>
				{{customer_address}}<br>
				{{customer_city}} - {{customer_pincode}}<br>
				MOBILE: {{customer_mobile}}<br>
				GST IN: {{customer_gstin}}
			</td>
			<td style="width:20%; text-align:center;">
				{{#qr_code_image}}<img src="data:image/png;base64,{{qr_code_image}}" alt="QR Code" style="height:100px; width:100px;">{{/qr_code_image}}
			</td>
			<td style="width:40%">
				<table style="font-weight:bold; width:100%; border-collapse:collapse;">
					<tr><td style="width:40%; padding:2px;">Invoice No</td><td style="width:10%; padding:2px;">:</td><td style="width:50%; padding:2px;">{{invoice_no}}</td></tr>
					<tr><td style="padding:2px;">Date</td><td style="padding:2px;">:</td><td style="padding:2px;">{{bill_date}}</td></tr>
					<tr><td style="padding:2px;">Time</td><td style="padding:2px;">:</td><td style="padding:2px;">{{bill_time}}</td></tr>
					<tr><td style="padding:2px;">Gold 22-KT</td><td style="padding:2px;">:</td><td style="padding:2px;">{{gold_rate}}/Gm</td></tr>
					<tr><td style="padding:2px;">Gold 18-KT</td><td style="padding:2px;">:</td><td style="padding:2px;">{{gold_rate_18ct}}/Gm</td></tr>
					<tr><td style="padding:2px;">SILVER</td><td style="padding:2px;">:</td><td style="padding:2px;">{{silver_rate}}/Gm</td></tr>
					<tr><td style="padding:2px;">GSTIN</td><td style="padding:2px;">:</td><td style="padding:2px;">{{company_gstin}}</td></tr>
				</table>
			</td>
		</tr>
	</table>

    <!-- MODULAR SALES ITEMS SECTION -->
    {{#has_sales_items}}
    <div style="page-break-inside:auto; margin-bottom:15px;">
        {{#show_sales_title}}<div class="section-title">Sales Details</div>{{/show_sales_title}}
        <table id="pp">
            <thead>
                <tr style="text-transform:uppercase;">
                    <th class="dotted-cell alignCenter" style="width: 5%;">S.No</th>
                    <th class="dotted-cell alignLeft" style="width: 28%;">Description</th>
                    <th class="dotted-cell alignRight" style="width: 8%;">HSN</th>
                    <th class="dotted-cell alignRight" style="width: 5%;">PCS</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">GRS.WT</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">NET.WT</th>
                    <th class="dotted-cell alignRight" style="width: 7%;">V.A</th>
                    <th class="dotted-cell alignRight" style="width: 7%;">MC</th>
                    <th class="dotted-cell alignRight" style="width: 10%;">Rate</th>
                    <th class="dotted-cell alignRight" style="width: 12%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- {{#sales_items}} -->
                <tr>
                    <td class="alignCenter">{{sno}}</td>
                    <td class="alignLeft">{{description}}</td>
                    <td class="alignRight">{{hsn_code}}</td>
                    <td class="alignRight">{{qty}}</td>
                    <td class="alignRight">{{gross_wt}}</td>
                    <td class="alignRight">{{net_wt}}</td>
                    <td class="alignRight">{{va_content}}</td>
                    <td class="alignRight">{{mc}}</td>
                    <td class="alignRight">{{rate}}</td>
                    <td class="alignRight">{{amount}}</td>
                </tr>
                <!-- {{/sales_items}} -->
            </tbody>
            <tfoot>
                <tr style="font-weight:bold;">
                    <td colspan="3" class="dotted-cell alignRight">Total:</td>
                    <td class="dotted-cell alignRight">{{total_sales_qty}}</td>
                    <td class="dotted-cell alignRight">{{total_sales_gross_wt}}</td>
                    <td class="dotted-cell alignRight">{{total_sales_net_wt}}</td>
                    <td colspan="3" class="dotted-cell"></td>
                    <td class="dotted-cell alignRight">{{total_sales_amount}}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Tax details directly after sales items -->
        <table style="width:100%; border-collapse:collapse; font-size:12px; font-weight:bold; margin-top:5px;">
            <tr>
                <td style="width:60%;"></td>
                <td style="width:40%;">
                    <table class="table-summary">
                        <tr>
                            <td class="label-col">SUB TOTAL (Taxable) :</td>
                            <td class="val-col"><span class="currency-sym">₹</span><span class="currency-val">{{sub_total}}</span></td>
                        </tr>
                        <tr>
                            <td class="label-col">SGST :</td>
                            <td class="val-col"><span class="currency-sym">₹</span><span class="currency-val">{{sgst_amount}}</span></td>
                        </tr>
                        <tr>
                            <td class="label-col">CGST :</td>
                            <td class="val-col"><span class="currency-sym">₹</span><span class="currency-val">{{cgst_amount}}</span></td>
                        </tr>
                        <tr>
                            <td class="label-col">IGST :</td>
                            <td class="val-col"><span class="currency-sym">₹</span><span class="currency-val">{{igst_amount}}</span></td>
                        </tr>
                        <tr>
                            <td class="label-col dotted-cell-bottom">Round Off :</td>
                            <td class="val-col dotted-cell-bottom"><span class="currency-sym">₹</span><span class="currency-val">{{round_off}}</span></td>
                        </tr>
                        <tr>
                            <td class="label-col">SALES AMOUNT :</td>
                            <td class="val-col"><span class="currency-sym">₹</span><span class="currency-val">{{lbl_sales_amount}}</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    {{/has_sales_items}}

    <!-- MODULAR PURCHASE ITEMS SECTION -->
    {{#has_purchase_items}}
    <div style="page-break-inside:auto; margin-bottom:15px;">
        <div class="section-title">Purchase / Old Metal Details</div>
        <table id="pp">
            <thead>
                <tr style="text-transform:uppercase;">
                    <th class="dotted-cell alignCenter" style="width: 5%;">S.No</th>
                    <th class="dotted-cell alignLeft" style="width: 38%;">Description</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">GRS.WT</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">STN LESS</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">MTL LESS</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">NET.WT</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">Rate</th>
                    <th class="dotted-cell alignRight" style="width: 12%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- {{#purchase_items}} -->
                <tr>
                    <td class="alignCenter">{{purchase_sno}}</td>
                    <td class="alignLeft">{{purchase_description}}</td>
                    <td class="alignRight">{{purchase_gross_wt}}</td>
                    <td class="alignRight">{{purchase_stn_less}}</td>
                    <td class="alignRight">{{purchase_mtl_less}}</td>
                    <td class="alignRight">{{purchase_net_wt}}</td>
                    <td class="alignRight">{{purchase_rate}}</td>
                    <td class="alignRight">{{purchase_amount}}</td>
                </tr>
                <!-- {{/purchase_items}} -->
            </tbody>
            <tfoot>
                <tr style="font-weight:bold;">
                    <td colspan="2" class="dotted-cell alignRight">Total:</td>
                    <td class="dotted-cell alignRight">{{total_old_metal_gross_wt}}</td>
                    <td colspan="2" class="dotted-cell"></td>
                    <td class="dotted-cell alignRight">{{total_old_metal_net_wt}}</td>
                    <td class="dotted-cell"></td>
                    <td class="dotted-cell alignRight">{{total_old_metal_amount}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    {{/has_purchase_items}}

    <!-- MODULAR RETURN ITEMS SECTION -->
    {{#has_return_items}}
    <div style="page-break-inside:auto; margin-bottom:15px;">
        <div class="section-title">Sales Return / Exchange Details</div>
        <table id="pp">
            <thead>
                <tr style="text-transform:uppercase;">
                    <th class="dotted-cell alignCenter" style="width: 5%;">S.No</th>
                    <th class="dotted-cell alignLeft" style="width: 28%;">Description</th>
                    <th class="dotted-cell alignRight" style="width: 8%;">HSN</th>
                    <th class="dotted-cell alignRight" style="width: 5%;">PCS</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">GRS.WT</th>
                    <th class="dotted-cell alignRight" style="width: 9%;">NET.WT</th>
                    <th class="dotted-cell alignRight" style="width: 7%;">V.A</th>
                    <th class="dotted-cell alignRight" style="width: 7%;">MC</th>
                    <th class="dotted-cell alignRight" style="width: 10%;">Rate</th>
                    <th class="dotted-cell alignRight" style="width: 12%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- {{#return_items}} -->
                <tr>
                    <td class="alignCenter">{{return_sno}}</td>
                    <td class="alignLeft">{{return_description}}</td>
                    <td class="alignRight">{{return_hsn_code}}</td>
                    <td class="alignRight">{{return_qty}}</td>
                    <td class="alignRight">{{return_gross_wt}}</td>
                    <td class="alignRight">{{return_net_wt}}</td>
                    <td class="alignRight">{{return_va_content}}</td>
                    <td class="alignRight">{{return_mc}}</td>
                    <td class="alignRight">{{return_rate}}</td>
                    <td class="alignRight">{{return_amount}}</td>
                </tr>
                <!-- {{/return_items}} -->
            </tbody>
            <tfoot>
                <tr style="font-weight:bold;">
                    <td colspan="3" class="dotted-cell alignRight">Total:</td>
                    <td class="dotted-cell alignRight">{{total_return_qty}}</td>
                    <td class="dotted-cell alignRight">{{total_return_gross_wt}}</td>
                    <td class="dotted-cell alignRight">{{total_return_net_wt}}</td>
                    <td colspan="3" class="dotted-cell"></td>
                    <td class="dotted-cell alignRight">{{total_return_amount}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    {{/has_return_items}}

    <!-- MODULAR ORDER ADVANCE ENTRIES -->
    {{#has_order_advance_entries}}
    <div style="page-break-inside:auto; margin-bottom:15px;">
        <div class="section-title">Order Advance Adjusted Details</div>
        <table id="pp" style="width:50%;">
            <thead>
                <tr style="text-transform:uppercase;">
                    <th class="dotted-cell alignLeft">Date</th>
                    <th class="dotted-cell alignRight">Rate/g</th>
                    <th class="dotted-cell alignRight">Weight</th>
                    <th class="dotted-cell alignRight">Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- {{#order_advance_entries}} -->
                <tr>
                    <td class="alignLeft">{{date}}</td>
                    <td class="alignRight">{{rate}}</td>
                    <td class="alignRight">{{weight}}</td>
                    <td class="alignRight">{{amount}}</td>
                </tr>
                <!-- {{/order_advance_entries}} -->
            </tbody>
            <tfoot><tr><td colspan="4" class="dotted-cell-top"></td></tr></tfoot>
        </table>
    </div>
    {{/has_order_advance_entries}}
    
    <!-- MODULAR CREDIT COLLECTION HISTORY -->
    {{#has_credit_collection_history}}
    <div style="page-break-inside:auto; margin-bottom:15px;">
        <div class="section-title">Credit Collection History (Ref: {{credit_ref_bill_no}})</div>
        <table id="pp" style="width:50%;">
            <thead>
                <tr style="text-transform:uppercase;">
                    <th class="dotted-cell alignLeft">Description</th>
                    <th class="dotted-cell alignLeft">Date</th>
                    <th class="dotted-cell alignRight">Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- {{#credit_collection_history}} -->
                <tr>
                    <td class="alignLeft">{{label}}</td>
                    <td class="alignLeft">{{date}}</td>
                    <td class="alignRight">{{amount}}</td>
                </tr>
                <!-- {{/credit_collection_history}} -->
                <tr style="font-weight:bold;">
                    <td colspan="2" class="alignRight">Balance Due :</td>
                    <td class="alignRight">{{credit_balance_amount}}</td>
                </tr>
            </tbody>
             <tfoot><tr><td colspan="3" class="dotted-cell-top"></td></tr></tfoot>
        </table>
    </div>
    {{/has_credit_collection_history}}
    
    <!-- MODULAR CHIT PRE CLOSE ITEMS -->
    {{#has_chit_items}}
    <div style="page-break-inside:auto; margin-bottom:15px;">
        <div class="section-title">Chit Adjustment Details</div>
        <table id="pp" style="width:50%;">
            <thead>
                <tr style="text-transform:uppercase;">
                    <th class="dotted-cell alignCenter">S.No</th>
                    <th class="dotted-cell alignLeft">Chit Ref No</th>
                    <th class="dotted-cell alignRight">Utilized Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- {{#chit_general_items}} -->
                <tr>
                    <td class="alignCenter">{{sno}}</td>
                    <td class="alignLeft">{{ref_no}}</td>
                    <td class="alignRight">{{amount}}</td>
                </tr>
                <!-- {{/chit_general_items}} -->
            </tbody>
            <tfoot><tr><td colspan="3" class="dotted-cell-top"></td></tr></tfoot>
        </table>
    </div>
    {{/has_chit_items}}

    <!-- MODULAR REPAIR ITEMS -->
    {{#has_repair_items}}
    <div style="page-break-inside:auto; margin-bottom:15px;">
        <div class="section-title">Repair Details</div>
        <table id="pp" style="width:80%;">
            <thead>
                <tr style="text-transform:uppercase;">
                    <th class="dotted-cell alignCenter">S.No</th>
                    <th class="dotted-cell alignLeft">Description</th>
                    <th class="dotted-cell alignRight">Init WT</th>
                    <th class="dotted-cell alignRight">Comp WT</th>
                    <th class="dotted-cell alignRight">Amount</th>
                </tr>
            </thead>
            <tbody>
                <!-- {{#repair_order_items}} -->
                <tr>
                    <td class="alignCenter">{{sno}}</td>
                    <td class="alignLeft">{{description}}</td>
                    <td class="alignRight">{{repair_wt}}</td>
                    <td class="alignRight">{{completed_wt}}</td>
                    <td class="alignRight">{{amount}}</td>
                </tr>
                <!-- {{/repair_order_items}} -->
            </tbody>
             <tfoot><tr><td colspan="5" class="dotted-cell-top"></td></tr></tfoot>
        </table>
    </div>
    {{/has_repair_items}}

    <!-- PAYMENT DETAILS & OVERALL FOOTER -->
	<div style="border-top: 1px dashed black; margin-top:20px;"></div>
	<table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:5px;">
		<tr>
			<td style="width:50%; padding:0; vertical-align:top;">
				<div style="font-weight:bold; margin-bottom:5px;">Amount in Words:</div>
				<div style="font-style:italic; margin-bottom:15px;">{{amount_in_words}}</div>
				<br>
				<b>Terms & Conditions</b><br>
				<div style="font-size:9px;">{{terms_condition}}</div>
                <br><br><br><br>
                <div style="font-weight:bold; font-size:11px;">Customer Signature</div>
			</td>
			<td style="width:50%; padding:0; vertical-align:top;">
				<table class="table-summary" style="width:80%; float:right;">
					
					{{#has_return_items}}
					<tr>
						<td class="label-col">EXCHANGE AMOUNT :</td>
						<td class="val-col" style="color:red;"><span class="currency-sym">₹</span><span class="currency-val">-{{lbl_exchange_amount}}</span></td>
					</tr>
					{{/has_return_items}}
					
					{{#has_purchase_items}}
					<tr>
						<td class="label-col">OLD METAL (PURCH) AMOUNT :</td>
						<td class="val-col" style="color:red;"><span class="currency-sym">₹</span><span class="currency-val">-{{total_old_metal_amount}}</span></td>
					</tr>
					{{/has_purchase_items}}
					
					<tr>
						<td class="label-col dotted-cell-top" style="padding-top:5px; font-size:14px;">NET PAYABLE :</td>
						<td class="val-col dotted-cell-top" style="padding-top:5px; font-size:14px;"><span class="currency-sym">₹</span><span class="currency-val">{{net_amount}}</span></td>
					</tr>
					<tr>
						<td class="label-col">PAID AMOUNT :</td>
						<td class="val-col"><span class="currency-sym">₹</span><span class="currency-val">{{total_paid}}</span></td>
					</tr>
					<tr>
						<td class="label-col dotted-cell-bottom" style="padding-bottom:5px;">BALANCE AMOUNT :</td>
						<td class="val-col dotted-cell-bottom" style="padding-bottom:5px;"><span class="currency-sym">₹</span><span class="currency-val">{{balance_amount}}</span></td>
					</tr>

                    <tr>
                        <td colspan="2" style="text-align:right; font-weight:bold; font-size:12px; padding-top:40px;">
                            For {{company_name}}<br><br><br>
                            Authorized Signatory
                        </td>
                    </tr>
				</table>
			</td>
		</tr>
	</table>
</div>
HTML;

$db = new PDO('mysql:host=localhost;dbname=arc_staging_24_02_26', 'root', '');
// Delete the old global template
$db->query("DELETE FROM print_templates WHERE template_code='global'");

// Insert correctly mapped row
$stmt = $db->prepare('INSERT INTO print_templates (template_name, template_code, template_category, template_html, template_css, paper_size, page_orientation, is_active, created_at, updated_at) VALUES (:template_name, :template_code, :template_category, :template_html, :template_css, :paper_size, :page_orientation, :is_active, NOW(), NOW())');
$stmt->execute([
    'template_name' => 'global',
    'template_code' => 'global',
    'template_category' => 0,
    'template_html' => $html,
    'template_css' => $css,
    'paper_size' => 'A4',
    'page_orientation' => 'portrait',
    'is_active' => 1
]);

echo "Fixed DB columns successfully!";
