<?php
$html = <<<HTML
<style>
* { font-family: DejaVu Sans, sans-serif; box-sizing: border-box; }
body { margin: 0; padding: 0; font-size: 10px; line-height: 1.2; }
.wrapper-table { width: 100%; table-layout: fixed; border-collapse: collapse; margin-bottom: 10px; }
.wrapper-table td { vertical-align: top; padding: 5px; border: none; }
.table_heading { font-weight: bold; }
table#pp { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom:10px; }
table#pp th, table#pp td { padding: 3px; border: none; font-size: 10px; }
.item_dashed { border-top: 1px dashed black; border-bottom: 0px; margin: 0; width: 100%; padding: 0; }
.alignCenter { text-align: center; }
.alignRight { text-align: right; }
.alignLeft { text-align: left; }
.section-title { font-weight: bold; text-transform: uppercase; margin-top: 5px; font-size: 11px; }
</style>

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
    <div style="page-break-inside:auto;">
        {{^is_sales_only}}<div class="section-title">Sales Details</div>{{/is_sales_only}}
        <table id="pp">
            <thead>
                <tr><td colspan="10"><hr class="item_dashed"></td></tr>
                <tr style="text-transform:uppercase;">
                    <th class="alignCenter" style="width: 5%;">S.No</th>
                    <th class="alignLeft" style="width: 28%;">Description</th>
                    <th class="alignRight" style="width: 8%;">HSN</th>
                    <th class="alignRight" style="width: 5%;">PCS</th>
                    <th class="alignRight" style="width: 9%;">GRS.WT</th>
                    <th class="alignRight" style="width: 9%;">NET.WT</th>
                    <th class="alignRight" style="width: 7%;">V.A</th>
                    <th class="alignRight" style="width: 7%;">MC</th>
                    <th class="alignRight" style="width: 10%;">Rate</th>
                    <th class="alignRight" style="width: 12%;">Amount</th>
                </tr>
                <tr><th colspan="10"><hr class="item_dashed"></th></tr>
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
        </table>
    </div>
    {{/has_sales_items}}

    <!-- MODULAR PURCHASE ITEMS SECTION -->
    {{#has_purchase_items}}
    <div style="page-break-inside:auto;">
        <div class="section-title">Purchase / Old Metal Details</div>
        <table id="pp">
            <thead>
                <tr><td colspan="8"><hr class="item_dashed"></td></tr>
                <tr style="text-transform:uppercase;">
                    <th class="alignCenter" style="width: 5%;">S.No</th>
                    <th class="alignLeft" style="width: 38%;">Description</th>
                    <th class="alignRight" style="width: 9%;">GRS.WT</th>
                    <th class="alignRight" style="width: 9%;">STN LESS</th>
                    <th class="alignRight" style="width: 9%;">MTL LESS</th>
                    <th class="alignRight" style="width: 9%;">NET.WT</th>
                    <th class="alignRight" style="width: 9%;">Rate</th>
                    <th class="alignRight" style="width: 12%;">Amount</th>
                </tr>
                <tr><td colspan="8"><hr class="item_dashed"></td></tr>
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
                    <td colspan="2" class="alignRight">Total:</td>
                    <td class="alignRight">{{total_old_metal_gross_wt}}</td>
                    <td colspan="2"></td>
                    <td class="alignRight">{{total_old_metal_net_wt}}</td>
                    <td></td>
                    <td class="alignRight">{{total_old_metal_amount}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    {{/has_purchase_items}}

    <!-- MODULAR RETURN ITEMS SECTION -->
    {{#has_return_items}}
    <div style="page-break-inside:auto;">
        <div class="section-title">Sales Return / Exchange Details</div>
        <table id="pp">
            <thead>
                <tr><td colspan="10"><hr class="item_dashed"></td></tr>
                <tr style="text-transform:uppercase;">
                    <th class="alignCenter" style="width: 5%;">S.No</th>
                    <th class="alignLeft" style="width: 28%;">Description</th>
                    <th class="alignRight" style="width: 8%;">HSN</th>
                    <th class="alignRight" style="width: 5%;">PCS</th>
                    <th class="alignRight" style="width: 9%;">GRS.WT</th>
                    <th class="alignRight" style="width: 9%;">NET.WT</th>
                    <th class="alignRight" style="width: 7%;">V.A</th>
                    <th class="alignRight" style="width: 7%;">MC</th>
                    <th class="alignRight" style="width: 10%;">Rate</th>
                    <th class="alignRight" style="width: 12%;">Amount</th>
                </tr>
                <tr><td colspan="10"><hr class="item_dashed"></td></tr>
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
                    <td colspan="3" class="alignRight">Total:</td>
                    <td class="alignRight">{{total_return_qty}}</td>
                    <td class="alignRight">{{total_return_gross_wt}}</td>
                    <td class="alignRight">{{total_return_net_wt}}</td>
                    <td colspan="3"></td>
                    <td class="alignRight">{{total_return_amount}}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    {{/has_return_items}}

    <!-- MODULAR ORDER ADVANCE ENTRIES -->
    {{#has_order_advance_entries}}
    <div style="page-break-inside:auto;">
        <div class="section-title">Order Advance Adjusted Details</div>
        <table id="pp" style="width:50%;">
            <thead>
                <tr><td colspan="4"><hr class="item_dashed"></td></tr>
                <tr style="text-transform:uppercase;">
                    <th class="alignLeft">Date</th>
                    <th class="alignRight">Rate/g</th>
                    <th class="alignRight">Weight</th>
                    <th class="alignRight">Amount</th>
                </tr>
                <tr><td colspan="4"><hr class="item_dashed"></td></tr>
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
        </table>
    </div>
    {{/has_order_advance_entries}}
    
    <!-- MODULAR CREDIT COLLECTION HISTORY -->
    {{#has_credit_collection_history}}
    <div style="page-break-inside:auto;">
        <div class="section-title">Credit Collection History (Ref: {{credit_ref_bill_no}})</div>
        <table id="pp" style="width:50%;">
            <thead>
                <tr><td colspan="3"><hr class="item_dashed"></td></tr>
                <tr style="text-transform:uppercase;">
                    <th class="alignLeft">Description</th>
                    <th class="alignLeft">Date</th>
                    <th class="alignRight">Amount</th>
                </tr>
                <tr><td colspan="3"><hr class="item_dashed"></td></tr>
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
        </table>
    </div>
    {{/has_credit_collection_history}}
    
    <!-- MODULAR CHIT PRE CLOSE ITEMS -->
    {{#has_chit_items}}
    <div style="page-break-inside:auto;">
        <div class="section-title">Chit Adjustment Details</div>
        <table id="pp" style="width:50%;">
            <thead>
                <tr><td colspan="3"><hr class="item_dashed"></td></tr>
                <tr style="text-transform:uppercase;">
                    <th class="alignCenter">S.No</th>
                    <th class="alignLeft">Chit Ref No</th>
                    <th class="alignRight">Utilized Amount</th>
                </tr>
                <tr><td colspan="3"><hr class="item_dashed"></td></tr>
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
        </table>
    </div>
    {{/has_chit_items}}

    <!-- MODULAR REPAIR ITEMS -->
    {{#has_repair_items}}
    <div style="page-break-inside:auto;">
        <div class="section-title">Repair Details</div>
        <table id="pp" style="width:80%;">
            <thead>
                <tr><td colspan="5"><hr class="item_dashed"></td></tr>
                <tr style="text-transform:uppercase;">
                    <th class="alignCenter">S.No</th>
                    <th class="alignLeft">Description</th>
                    <th class="alignRight">Init WT</th>
                    <th class="alignRight">Comp WT</th>
                    <th class="alignRight">Amount</th>
                </tr>
                <tr><td colspan="5"><hr class="item_dashed"></td></tr>
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
        </table>
    </div>
    {{/has_repair_items}}


	<hr class="item_dashed" style="margin-top:10px;">
	<table style="width:100%; border-collapse:collapse; font-size:12px; margin-top:5px;">
		<tr>
			<td style="width:60%; padding:0; vertical-align:top;">
				<div style="font-weight:bold; margin-bottom:5px;">Amount in Words:</div>
				<div style="font-style:italic; margin-bottom:10px;">{{amount_in_words}}</div>
				<br>
				<b>Terms & Conditions</b><br>
				<div style="font-size:9px;">{{terms_condition}}</div>
			</td>
			<td style="width:40%; padding:0; vertical-align:top;">
				<table style="width:100%; border-collapse:collapse; font-size:12px; font-weight:bold;">
					<!-- Subtotals for mixed bills, matching the original bill structure -->
					{{#has_sales_items}}
					<tr>
						<td style="text-align:right; padding:3px;">SALES AMOUNT :</td>
						<td style="text-align:right; padding:3px; width:40%;">₹ {{lbl_sales_amount}}</td>
					</tr>
					{{/has_sales_items}}
					
					{{#has_return_items}}
					<tr>
						<td style="text-align:right; padding:3px;">EXCHANGE AMOUNT :</td>
						<td style="text-align:right; padding:3px; width:40%;">₹ -{{lbl_exchange_amount}}</td>
					</tr>
					{{/has_return_items}}
					
					{{#has_purchase_items}}
					<tr>
						<td style="text-align:right; padding:3px;">OLD METAL (PURCH) AMOUNT :</td>
						<td style="text-align:right; padding:3px; width:40%;">₹ -{{total_old_metal_amount}}</td>
					</tr>
					{{/has_purchase_items}}
					
					<tr><td colspan="2"><hr class="item_dashed"></td></tr>
					
					<tr>
						<td style="text-align:right; padding:3px;">SUB TOTAL (Taxable) :</td>
						<td style="text-align:right; padding:3px; width:40%;">₹ {{sub_total}}</td>
					</tr>
					<tr>
						<td style="text-align:right; padding:3px;">SGST :</td>
						<td style="text-align:right; padding:3px;">₹ {{sgst_amount}}</td>
					</tr>
					<tr>
						<td style="text-align:right; padding:3px;">CGST :</td>
						<td style="text-align:right; padding:3px;">₹ {{cgst_amount}}</td>
					</tr>
					<tr>
						<td style="text-align:right; padding:3px;">IGST :</td>
						<td style="text-align:right; padding:3px;">₹ {{igst_amount}}</td>
					</tr>
					<tr>
						<td style="text-align:right; padding:3px;">Round Off :</td>
						<td style="text-align:right; padding:3px;">₹ {{round_off}}</td>
					</tr>
					<tr><td colspan="2"><hr class="item_dashed"></td></tr>
					<tr>
						<td style="text-align:right; padding:3px; font-size:14px;">NET PAYABLE :</td>
						<td style="text-align:right; padding:3px; font-size:14px;">₹ {{net_amount}}</td>
					</tr>
					<tr>
						<td style="text-align:right; padding:3px;">PAID AMOUNT :</td>
						<td style="text-align:right; padding:3px;">₹ {{total_paid}}</td>
					</tr>
					<tr>
						<td style="text-align:right; padding:3px;">BALANCE AMOUNT :</td>
						<td style="text-align:right; padding:3px;">₹ {{balance_amount}}</td>
					</tr>
					<tr><td colspan="2"><hr class="item_dashed"></td></tr>
				</table>
			</td>
		</tr>
	</table>
    
    <div style="margin-top:20px; text-align:right; font-weight:bold; font-size:12px;">
        For {{company_name}}<br><br><br>
        Authorized Signatory
    </div>
</div>
HTML;

$db = new PDO('mysql:host=localhost;dbname=arc_staging_24_02_26', 'root', '');
$stmt = $db->prepare('UPDATE print_templates SET template_html = :html WHERE id_template = 26');
$stmt->execute(['html' => $html]);
echo "Template 26 updated effectively modular!\n";
