INSERT INTO print_template_custom_blocks (block_name, block_html, block_category) VALUES 
('Bill Format 2 - Header', '
<div class="print-header" style="text-align:center; margin:-20px 0 0 0;">
    <img src="{{company_logo_url}}" style="width:auto;height:50px;">
    <div style="font-size:20px; font-weight:bold; margin-top:5px;">{{company_name}}</div>										
    <div style="font-size:12px;"><strong>{{company_address1}}, {{company_address2}}, {{company_address3}} | {{company_city}} - {{company_pincode}} | Phone : {{company_phone}}</strong><br></div>
    <div style="font-size:12px;"><strong>GSTIN : {{company_gstin}} | PAN : {{company_pan}}</strong><br></div>
</div>
', 'Legacy Layouts'),

('Bill Format 2 - Customer Info', '
<table style="width:100%; border-collapse:collapse; margin-top:0px; font-size: 12px; font-weight: bold;">
    <tr>
        <td style="width:33%; border:none; vertical-align:top; border-right:2px solid black; padding-right:10px;">
            <table style="width:100%; line-height: 1.4;">
                <tr>
                    <td><span style="font-weight:bold; font-size:14px; text-transform:uppercase">{{customer_name}}</span></td>
                </tr>
                <tr>
                    <td>{{customer_address1}}, {{customer_address2}}</td>
                </tr>
                <tr>
                    <td>{{customer_address3}}</td>
                </tr>
                <tr>
                    <td>{{customer_city}} - {{customer_pincode}}, {{customer_state}}</td>
                </tr>
                <tr>
                    <td>Mobile : {{customer_mobile}}</td>
                </tr>
                <tr>
                    <td>State Code : {{customer_state_code}}</td>
                </tr>
                <tr>
                    <td>GSTIN : {{customer_gstin}}</td>
                </tr>
            </table>
        </td>
        <td style="width:33%; border:none; vertical-align:top; text-align:center;">
             <!-- E-Invoice QR Placeholder -->
        </td>
        <td style="width:34%; border:none; vertical-align:top; border-left:2px solid black; padding-left:10px;">
            <table style="width:100%; line-height: 1.4;">
                <tr>
                    <td style="width:40%;">Invoice No</td>
                    <td style="width:5%;">:</td>
                    <td style="width:55%;">{{invoice_no}}</td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>:</td>
                    <td>{{bill_date}}</td>
                </tr>
                <tr>
                    <td>Time</td>
                    <td>:</td>
                    <td>{{bill_time}}</td>
                </tr>
                <tr>
                    <td>Place of Supply</td>
                    <td>:</td>
                    <td>{{place_of_supply}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
', 'Legacy Layouts'),

('Bill Format 2 - Title', '
<div style="text-align:center; font-weight:bold; font-size:18px; margin: 10px 0; border-top: 2px solid black; border-bottom: 2px solid black; padding: 5px 0;">
    {{document_title}}
</div>
', 'Legacy Layouts'),

('Bill Format 2 - Tax Matrix & Totals', '
<table style="width:100%; border-collapse:collapse; margin-top:20px; text-align:center; font-size: 12px;" border="1">
    <thead style="text-transform:uppercase; font-size:13px; font-weight: bold;">
        <tr>
            <td style="width: 5%; padding: 5px;">S.No</td>
            <td style="width: 15%; padding: 5px; text-align:left;">HSN</td>
            <td style="width: 15%; padding: 5px; text-align: right;">Taxable Value</td>
            <td style="width: 15%; padding: 5px;">CGST Amount</td>
            <td style="width: 15%; padding: 5px;">SGST Amount</td>
            <td style="width: 15%; padding: 5px;">IGST Amount</td>
            <td style="width: 20%; padding: 5px; text-align: right;">Tax Amount</td>
        </tr>
    </thead>
    <tbody>
        <!-- Data will be populated dynamically by the template engine if handled as a block loop -->
        <tr>
            <td style="padding: 5px;">1</td>
            <td style="padding: 5px; text-align:left;">711319</td>
            <td style="padding: 5px; text-align: right;">{{total_taxable_value}}</td>
            <td style="padding: 5px;">{{total_cgst_amount}}</td>
            <td style="padding: 5px;">{{total_sgst_amount}}</td>
            <td style="padding: 5px;">{{total_igst_amount}}</td>
            <td style="padding: 5px; text-align: right;">{{total_tax_amount}}</td>
        </tr>
        <tr>
            <td colspan="7" style="border-left: 0; border-right: 0;">&nbsp;</td>
        </tr>
        <tr style="font-weight: bold; font-size: 14px;">
            <td colspan="5" style="text-align: right; padding: 5px; border-right: 0;">GRAND TOTAL : </td>
            <td colspan="2" style="text-align: right; padding: 5px; border-left: 0;">Rs. {{grand_total}}</td>
        </tr>
    </tbody>
</table>
', 'Legacy Layouts'),

('Bill Format 2 - Payment Summary', '
<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 10px; font-size: 12px;">
    <div style="font-weight: bold; width: 45%;">
        Amount in words: {{amount_in_words}} Only.
    </div>
    <table style="width: 45%; border-collapse: collapse; float: right;">
        <tr>
            <td style="padding: 3px; font-weight: bold;">Cash Received</td>
            <td style="padding: 3px; text-align: right;">Rs. {{pay_cash}}</td>
        </tr>
        <tr>
            <td style="padding: 3px; font-weight: bold;">Card Payment</td>
            <td style="padding: 3px; text-align: right;">Rs. {{pay_card}}</td>
        </tr>
        <tr>
            <td style="padding: 3px; font-weight: bold;">UPI / Online</td>
            <td style="padding: 3px; text-align: right;">Rs. {{pay_upi}}</td>
        </tr>
        <tr>
            <td style="padding: 3px; font-weight: bold;">Advance Adjusted</td>
            <td style="padding: 3px; text-align: right;">Rs. {{advance_adjusted}}</td>
        </tr>
        <tr>
            <td style="padding: 3px; font-weight: bold; border-top: 1px dashed black;">Balance Due</td>
            <td style="padding: 3px; text-align: right; border-top: 1px dashed black;">Rs. {{balance_due}}</td>
        </tr>
    </table>
    <div style="clear:both;"></div>
</div>
', 'Legacy Layouts'),

('Bill Format 2 - Footer', '
<div style="margin-top: 30px; border-top: 1px solid black; padding-top: 10px;">
    <table style="width:100%; border-collapse:collapse; font-weight:bold;">
        <tr>
            <td style="font-size:16px; text-align:left; width:50%; padding:10px 0; border:none;">Customer Signature</td>
            <td style="font-size:16px; text-align:right; width:50%; padding:10px 0; border:none;">For {{company_name}}</td>
        </tr>
    </table>

    <div style="font-size:11px; width:100%; margin-top: 10px; text-align: justify;">
        <p>*Amount of tax (Invoice Inwards) subject to reverse charge. *Received ornaments in good condition. *The Hallmarking charge is included in the invoice. *The consumer can get the purity of the hallmarked jewellery/artefacts checked from any of the BIS recognized A&H center. The list of BIS recognized A&H centers along with address and contact details are available in the website:www.bis.gov.in E.&O.E</p>
    </div>
</div>
', 'Legacy Layouts');
