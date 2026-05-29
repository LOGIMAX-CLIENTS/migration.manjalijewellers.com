-- Insert a Pre-Designed Standard Billing Template
INSERT INTO `print_templates` (
    `template_code`, 
    `template_name`, 
    `template_category`, 
    `paper_size`, 
    `template_html`, 
    `template_css`, 
    `gjs_data`,
    `is_active`,
    `is_default`
) VALUES (
    'BILL_PRE_DESIGN', 
    'Standard Billing Invoice (Pre-Designed)', 
    'billing', 
    'A4',
    '<div class="invoice-container">
        <div class="company-header" style="text-align:center; margin-bottom:20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
            <h1 style="margin:5px 0; font-size: 24px; text-transform: uppercase;">{{company_name}}</h1>
            <p style="margin:2px 0;">{{branch_address}}</p>
            <p style="margin:2px 0;"><strong>Phone:</strong> {{branch_phone}} | <strong>GSTIN:</strong> {{branch_gstin}}</p>
        </div>
        
        <div class="invoice-title" style="text-align:center; margin: 20px 0;">
            <span style="border: 1px solid #000; padding: 5px 15px; font-weight: bold; font-size: 16px;">TAX INVOICE</span>
        </div>

        <div class="info-section" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div class="customer-details" style="width: 48%; padding: 10px; border: 1px solid #eee; background: #f9f9f9;">
                <h4 style="margin-top: 0; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Billed To:</h4>
                <p><strong>Name:</strong> {{customer_name}}</p>
                <p><strong>Mobile:</strong> {{customer_mobile}}</p>
                <p><strong>Address:</strong> {{customer_address}}</p>
            </div>
            <div class="invoice-details" style="width: 48%; padding: 10px; border: 1px solid #eee; background: #f9f9f9;">
                <h4 style="margin-top: 0; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Invoice Details:</h4>
                <p><strong>Invoice No:</strong> {{bill_no}}</p>
                <p><strong>Date:</strong> {{bill_date}}</p>
            </div>
        </div>

        <table class="items-table" style="width:100%; border-collapse:collapse; margin:15px 0;">
            <thead>
                <tr style="background:#333; color: #fff;">
                    <th style="padding:10px; text-align: center;">S.No</th>
                    <th style="padding:10px; text-align: left;">Description</th>
                    <th style="padding:10px; text-align: right;">Gross Wt</th>
                    <th style="padding:10px; text-align: right;">Net Wt</th>
                    <th style="padding:10px; text-align: right;">Rate</th>
                    <th style="padding:10px; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                {{#items}}
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding:8px; text-align: center;">{{sno}}</td>
                    <td style="padding:8px; text-align: left;">{{description}}</td>
                    <td style="padding:8px; text-align: right;">{{gross_wt}}</td>
                    <td style="padding:8px; text-align: right;">{{net_wt}}</td>
                    <td style="padding:8px; text-align: right;">{{rate}}</td>
                    <td style="padding:8px; text-align: right;">{{amount}}</td>
                </tr>
                {{/items}}
            </tbody>
        </table>

        <div class="totals-section" style="margin-top:20px; display: flex; justify-content: flex-end;">
            <table style="width:40%; border-collapse: collapse;">
                <tr>
                    <td style="padding:5px; text-align: right;">Subtotal:</td>
                    <td style="padding:5px; text-align: right;">{{subtotal}}</td>
                </tr>
                <tr>
                    <td style="padding:5px; text-align: right;">CGST:</td>
                    <td style="padding:5px; text-align: right;">{{cgst}}</td>
                </tr>
                <tr>
                    <td style="padding:5px; text-align: right;">SGST:</td>
                    <td style="padding:5px; text-align: right;">{{sgst}}</td>
                </tr>
                <tr style="font-weight:bold; font-size: 1.1em; border-top: 2px solid #000;">
                    <td style="padding:10px; text-align: right;">Grand Total:</td>
                    <td style="padding:10px; text-align: right;">{{grand_total}}</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-top: 10px; text-align: right; font-style: italic;">
            Amount in words: {{amount_in_words}}
        </div>

        <div class="footer" style="margin-top: 50px; border-top: 1px solid #ccc; padding-top: 20px;">
            <div style="display: flex; justify-content: space-between;">
                <div style="text-align: center;">
                    <p>-----------------------------</p>
                    <p>Customer Signature</p>
                </div>
                <div style="text-align: center;">
                    <p>-----------------------------</p>
                    <p>Authorized Signature</p>
                </div>
            </div>
            <div style="margin-top: 20px; font-size: 12px; color: #666; text-align: center;">
                <p>Thank you for your business!</p>
            </div>
        </div>
    </div>',
    
    '.invoice-container { font-family: Arial, sans-serif; color: #333; }',
    
    '{}', -- Empty GrapesJS data for now, but HTML/CSS will render in preview
    1,
    0
);
