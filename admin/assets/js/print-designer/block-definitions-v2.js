/**
 * Block Definitions V2 — WYSIWYG (No code visible to users)
 * All previews show sample data, all settings use visual controls
 */
window.BLOCK_DEFS_V2 = {

    // ── SAMPLE DATA for visual previews ──────────
    _samples: {
        company_name: 'Demo Jewellers', company_address: '456 Gold St, Chennai', company_city: 'Chennai',
        company_pincode: '600002', company_mobile: '9988776655', company_email: 'info@demo.com',
        company_gstin: '33XYZCO1234E1Z6', company_pan: 'XYZCO1234E', branch_name: 'Main Branch',
        customer_name: 'John Doe', customer_mobile: '9876543210', customer_address: '123 Main St',
        customer_city: 'Chennai', customer_pincode: '600001', customer_gstin: '33AABCC1234D1Z5',
        customer_pan: 'AABCC1234D', customer_adhar: '1234 5678 9012',
        invoice_no: 'INV-2023-001', bill_date: '30-03-2026', bill_time: '10:00 AM',
        title: 'Tax Invoice', gold_rate: '7,250', silver_rate: '95', billed_by: 'Admin',
        sub_total: '1,25,600.00', sgst_amount: '1,884.00', cgst_amount: '1,884.00',
        igst_amount: '0.00', round_off: '0.32', net_payable: '1,29,368.00', grand_total: '1,29,368.00',
        amount_in_words: 'One Lakh Twenty Nine Thousand Three Hundred Sixty Eight Rupees Only',
        cash_amount: '50,000.00', card_amount: '29,368.00', upi_amount: '50,000.00',
        cheque_amount: '0.00', balance_amount: '0.00', paid_amount: '1,29,368.00',
        remark: 'Thank you for your purchase!', mc: '500.00', va_percent: '5.00',
        chit_adj: '1,50,000.00', advance_adj: '5,000.00',
    },

    _sampleItems: [
        {sno:'1', description:'Gold Ring 22KT BIS', hsn_code:'711319', purity:'916', qty:'1', gross_wt:'5.200', net_wt:'5.000', va_percent:'5.00', mc:'500.00', rate:'7,250', amount:'36,750.00'},
        {sno:'2', description:'Gold Chain 22KT Hallmark', hsn_code:'711319', purity:'916', qty:'1', gross_wt:'8.500', net_wt:'8.200', va_percent:'4.50', mc:'800.00', rate:'7,250', amount:'60,250.00'},
        {sno:'3', description:'Gold Bangle 22KT', hsn_code:'711319', purity:'916', qty:'2', gross_wt:'12.100', net_wt:'11.800', va_percent:'3.00', mc:'1,200.00', rate:'7,250', amount:'86,750.00'},
    ],

    _getSample: function(key) {
        return this._samples[key] || '';
    },

    // ── Layout Blocks ──────────────────────────
    'company-header': {
        label: 'Company Header', icon: '🏢', category: 'Layout',
        desc: 'Company name, address, GSTIN, logo',
        defaults: {
            show_logo: true, font_size: '14px', text_align: 'center',
            border_bottom: true, repeat_every_page: true,
            fields: [
                {label: '', placeholder: 'company_name', group: 'company', style: 'title'},
                {label: '', placeholder: 'company_address', group: 'company'},
                {label: 'Ph:', placeholder: 'company_mobile', group: 'company'},
                {label: 'GSTIN:', placeholder: 'company_gstin', group: 'company'},
            ]
        },
        preview: function(s) {
            var S = BLOCK_DEFS_V2._samples;
            var html = '<div class="bvp-company" style="text-align:'+(s.text_align||'center')+'">';
            if (s.show_logo) html += '<div class="bvp-logo-placeholder">🏢</div>';
            (s.fields||[]).forEach(function(f) {
                var val = S[f.placeholder] || f.placeholder;
                var style = f.style === 'title' ? 'font-weight:700;font-size:14px;' : '';
                html += '<div class="bvp-field" style="'+style+'">';
                if (f.label) html += '<span class="bvp-label">'+f.label+' </span>';
                html += val + '</div>';
            });
            if (s.border_bottom) html += '<div class="bvp-border-bottom"></div>';
            html += '</div>';
            return html;
        }
    },

    'customer-info': {
        label: 'Customer / Invoice Info', icon: '👤', category: 'Layout',
        desc: 'Customer details + invoice info side by side',
        defaults: {
            layout: 'two-column', show_borders: true, font_size: '12px',
            fields: [
                {label:'To:', placeholder:'customer_name', group:'customer'},
                {label:'', placeholder:'customer_address', group:'customer'},
                {label:'Ph:', placeholder:'customer_mobile', group:'customer'},
                {label:'GSTIN:', placeholder:'customer_gstin', group:'customer'},
                {label:'Invoice No', placeholder:'invoice_no', group:'invoice'},
                {label:'Date', placeholder:'bill_date', group:'invoice'},
                {label:'Time', placeholder:'bill_time', group:'invoice'},
                {label:'Gold 22KT', placeholder:'gold_rate', group:'invoice', suffix:'/Gm'},
                {label:'Silver', placeholder:'silver_rate', group:'invoice', suffix:'/Gm'},
            ]
        },
        preview: function(s) {
            var S = BLOCK_DEFS_V2._samples;
            var cust = (s.fields||[]).filter(function(f){return f.group==='customer';});
            var inv  = (s.fields||[]).filter(function(f){return f.group==='invoice';});
            var bdr = (s.show_borders !== false) ? 'border-right:1px solid var(--d2-border);' : '';
            var html = '<div class="bvp-customer"><div class="bvp-two-col">';
            html += '<div class="bvp-cust-col" style="'+bdr+'padding-right:6px;">';
            cust.forEach(function(f) {
                html += '<div class="bvp-field">';
                if(f.label) html += '<span class="bvp-label">'+f.label+' </span>';
                html += (S[f.placeholder]||'—') + '</div>';
            });
            html += '</div><div class="bvp-cust-col" style="padding-left:6px;"><table class="bvp-inv-table">';
            inv.forEach(function(f) {
                html += '<tr><td class="bvp-inv-lbl">'+f.label+'</td><td class="bvp-inv-sep">:</td><td>'+(S[f.placeholder]||'—')+(f.suffix||'')+'</td></tr>';
            });
            html += '</table></div></div></div>';
            return html;
        }
    },

    'custom-text': {
        label: 'Text / Title', icon: '📝', category: 'Layout',
        desc: 'Add any text, title, or heading',
        defaults: { content: 'Tax Invoice', font_size: '14px', text_align: 'center', bold: true },
        preview: function(s) {
            var bold = s.bold ? 'font-weight:700;' : '';
            var text = s.content || 'Text';
            // Replace any {{placeholders}} with sample values for display
            text = text.replace(/\{\{(\w+)\}\}/g, function(m,k) { return BLOCK_DEFS_V2._samples[k] || k; });
            return '<div class="bvp-custom-text" style="text-align:'+(s.text_align||'left')+';'+bold+'">'+text+'</div>';
        }
    },

    'divider': {
        label: 'Divider Line', icon: '➖', category: 'Layout',
        desc: 'Horizontal separator line',
        defaults: { style: 'dashed', thickness: '1px', margin_top: '5px', margin_bottom: '5px' },
        preview: function(s) {
            var st = s.style || 'dashed';
            return '<div style="border-top:1px '+st+' var(--d2-border);margin:4px 0;"></div>';
        }
    },

    'spacer': {
        label: 'Empty Space', icon: '↕️', category: 'Layout',
        desc: 'Add vertical gap between sections',
        defaults: { height: '20px' },
        preview: function(s) {
            var h = parseInt(s.height) || 20;
            return '<div class="bvp-spacer" style="height:'+h+'px"><span class="bvp-spacer-label">↕ '+h+'px gap</span></div>';
        }
    },

    'page-break': {
        label: 'Page Break', icon: '✂️', category: 'Layout',
        desc: 'Start a new page here (for printing)',
        defaults: {},
        preview: function() {
            return '<div class="bvp-page-break"><div class="bvp-pb-line"></div><span class="bvp-pb-label">✂️ NEW PAGE STARTS HERE</span><div class="bvp-pb-line"></div></div>';
        }
    },

    // ── Data Blocks — VISUAL TABLE ────────────────
    'items-table': {
        label: 'Items Table', icon: '📊', category: 'Tables',
        desc: 'Sales items with columns you choose',
        defaults: {
            font_size: '12px', show_footer_totals: true, show_header_border: true,
            show_row_borders: true, loop_key: 'items', condition_key: 'has_sales_items',
            columns: [
                {field:'sno', label:'S.No', visible:true, align:'center', width:'5%'},
                {field:'description', label:'Description', visible:true, align:'left', width:'25%'},
                {field:'hsn_code', label:'HSN Code', visible:true, align:'center', width:'8%'},
                {field:'purity', label:'Purity', visible:false, align:'center', width:'6%'},
                {field:'qty', label:'Qty', visible:true, align:'center', width:'5%'},
                {field:'gross_wt', label:'Gross Wt', visible:true, align:'right', width:'8%'},
                {field:'net_wt', label:'Net Wt', visible:true, align:'right', width:'8%'},
                {field:'va_percent', label:'VA%', visible:false, align:'right', width:'6%'},
                {field:'mc', label:'MC', visible:false, align:'right', width:'8%'},
                {field:'rate', label:'Rate', visible:true, align:'right', width:'8%'},
                {field:'amount', label:'Amount', visible:true, align:'right', width:'10%'},
            ],
            // Formula columns — user can add calculated columns
            formula_columns: []
        },
        preview: function(s) {
            var cols = (s.columns || BLOCK_DEFS_V2['items-table'].defaults.columns).filter(function(c){return c.visible;});
            var items = BLOCK_DEFS_V2._sampleItems;
            var hdrBorder = (s.show_header_border !== false) ? 'border-bottom:2px solid var(--d2-text-muted);' : '';
            var rowBorder = (s.show_row_borders !== false) ? 'border-bottom:1px solid rgba(255,255,255,0.06);' : '';

            var html = '<table class="bvp-mini-table"><thead><tr style="'+hdrBorder+'">';
            cols.forEach(function(c) { html += '<th style="text-align:'+c.align+'">'+c.label+'</th>'; });
            html += '</tr></thead><tbody>';

            items.forEach(function(item) {
                html += '<tr style="'+rowBorder+'">';
                cols.forEach(function(c) { html += '<td style="text-align:'+c.align+'">'+(item[c.field]||'')+'</td>'; });
                html += '</tr>';
            });

            if (s.show_footer_totals !== false) {
                html += '<tr style="font-weight:700;border-top:2px solid var(--d2-text-muted);">';
                var lastIdx = cols.length - 1;
                cols.forEach(function(c, i) {
                    if (i === lastIdx) html += '<td style="text-align:right;">₹1,83,750.00</td>';
                    else if (i === 0) html += '<td colspan="1" style="text-align:right;font-weight:700;">Total</td>';
                    else html += '<td></td>';
                });
                html += '</tr>';
            }
            html += '</tbody></table>';
            return html;
        }
    },

    'purchase-table': {
        label: 'Old Metal / Purchase Table', icon: '🔄', category: 'Tables',
        desc: 'Old gold/silver exchange items',
        defaults: {
            font_size: '12px', loop_key: 'old_matel_details', condition_key: 'has_purchase_items',
            show_header_border: true, show_row_borders: true,
            columns: [
                {field:'sno', label:'S.No', visible:true, align:'center'},
                {field:'metal_name', label:'Metal', visible:true, align:'left'},
                {field:'purity', label:'Purity', visible:true, align:'center'},
                {field:'gross_wt', label:'Gross Wt', visible:true, align:'right'},
                {field:'net_wt', label:'Net Wt', visible:true, align:'right'},
                {field:'rate_per_gram', label:'Rate/Gm', visible:true, align:'right'},
                {field:'amount', label:'Amount', visible:true, align:'right'},
            ]
        },
        preview: function(s) {
            var cols = (s.columns||[]).filter(function(c){return c.visible;});
            var html = '<table class="bvp-mini-table"><thead><tr>';
            cols.forEach(function(c) { html += '<th>'+c.label+'</th>'; });
            html += '</tr></thead><tbody>';
            html += '<tr><td>1</td><td>Gold</td><td>916</td><td>8.500</td><td>8.200</td><td>5,000</td><td>41,000.00</td></tr>';
            html += '<tr><td>2</td><td>Silver</td><td>925</td><td>15.000</td><td>14.800</td><td>70</td><td>1,036.00</td></tr>';
            html += '</tbody></table>';
            return html;
        }
    },

    'sales-return-table': {
        label: 'Sales Return Table', icon: '🔙', category: 'Tables',
        desc: 'Returned items table',
        defaults: {
            font_size: '12px', loop_key: 'return_items', condition_key: 'has_return_items',
            show_header_border: true, show_row_borders: true,
            columns: [
                {field:'sno', label:'S.No', visible:true, align:'center'},
                {field:'description', label:'Description', visible:true, align:'left'},
                {field:'qty', label:'Qty', visible:true, align:'center'},
                {field:'gross_wt', label:'Gross Wt', visible:true, align:'right'},
                {field:'net_wt', label:'Net Wt', visible:true, align:'right'},
                {field:'amount', label:'Amount', visible:true, align:'right'},
            ]
        },
        preview: function(s) {
            var cols = (s.columns||[]).filter(function(c){return c.visible;});
            var html = '<table class="bvp-mini-table"><thead><tr>';
            cols.forEach(function(c) { html += '<th>'+c.label+'</th>'; });
            html += '</tr></thead><tbody><tr>';
            html += '<td>1</td><td>Gold Ring 22KT</td><td>1</td><td>5.200</td><td>5.000</td><td>36,250.00</td>';
            html += '</tr></tbody></table>';
            return html;
        }
    },

    // ── Summary Blocks ────────────────────────────
    'tax-summary': {
        label: 'Tax & Total', icon: '🧾', category: 'Summary',
        desc: 'CGST, SGST, IGST, Round Off, Grand Total',
        defaults: {
            font_size: '12px', show_subtotal: true, show_cgst: true, show_sgst: true,
            show_igst: false, show_round_off: true, show_amount_words: true
        },
        preview: function(s) {
            var S = BLOCK_DEFS_V2._samples;
            var html = '<div style="max-width:250px;margin-left:auto;">';
            if (s.show_subtotal!==false) html += '<div class="bvp-tax-row"><span>Subtotal</span><span>₹'+S.sub_total+'</span></div>';
            if (s.show_cgst!==false) html += '<div class="bvp-tax-row"><span>CGST (1.5%)</span><span>₹'+S.cgst_amount+'</span></div>';
            if (s.show_sgst!==false) html += '<div class="bvp-tax-row"><span>SGST (1.5%)</span><span>₹'+S.sgst_amount+'</span></div>';
            if (s.show_igst) html += '<div class="bvp-tax-row"><span>IGST</span><span>₹'+S.igst_amount+'</span></div>';
            if (s.show_round_off!==false) html += '<div class="bvp-tax-row"><span>Round Off</span><span>₹'+S.round_off+'</span></div>';
            html += '<div class="bvp-tax-row bvp-grand-total" style="border-top:2px solid var(--d2-text);padding-top:4px;margin-top:4px;"><span>Grand Total</span><span>₹'+S.grand_total+'</span></div>';
            if (s.show_amount_words!==false) html += '<div class="bvp-amount-words" style="margin-top:4px;">'+S.amount_in_words+'</div>';
            html += '</div>';
            return html;
        }
    },

    'payment-info': {
        label: 'Payment Breakdown', icon: '💰', category: 'Summary',
        desc: 'Cash, Card, UPI, Cheque, Balance',
        defaults: {
            font_size: '12px', show_cash: true, show_card: true, show_upi: true,
            show_cheque: true, show_balance: true, condition_key: 'has_payment_details'
        },
        preview: function(s) {
            var S = BLOCK_DEFS_V2._samples;
            var html = '<div style="max-width:250px;"><div class="bvp-pay-title">Payment Details</div>';
            if(s.show_cash!==false) html += '<div class="bvp-tax-row"><span>Cash</span><span>₹'+S.cash_amount+'</span></div>';
            if(s.show_card!==false) html += '<div class="bvp-tax-row"><span>Card</span><span>₹'+S.card_amount+'</span></div>';
            if(s.show_upi!==false) html += '<div class="bvp-tax-row"><span>UPI</span><span>₹'+S.upi_amount+'</span></div>';
            if(s.show_cheque!==false) html += '<div class="bvp-tax-row"><span>Cheque</span><span>₹'+S.cheque_amount+'</span></div>';
            if(s.show_balance!==false) html += '<div class="bvp-tax-row bvp-grand-total"><span>Balance</span><span>₹'+S.balance_amount+'</span></div>';
            html += '</div>';
            return html;
        }
    },

    'amount-words': {
        label: 'Amount in Words', icon: '📖', category: 'Summary',
        desc: 'Total amount written in words',
        defaults: { font_size: '12px', show_prefix: true, font_weight: '' },
        preview: function(s) {
            var pre = (s.show_prefix !== false) ? 'Rupees ' : '';
            return '<div class="bvp-amount-words" style="font-size:11px;">' + pre + BLOCK_DEFS_V2._samples.amount_in_words + '</div>';
        }
    },

    'remark-block': {
        label: 'Remark / Note', icon: '📋', category: 'Summary',
        desc: 'Remark, terms, or thank-you notes',
        defaults: { font_size: '12px', show_label: true },
        preview: function(s) {
            return '<div class="bvp-remark">' + (s.show_label!==false ? '<strong>Remark:</strong> ' : '') + BLOCK_DEFS_V2._samples.remark + '</div>';
        }
    },

    'advance-payment': {
        label: 'Adjustment Details', icon: '💸', category: 'Summary',
        desc: 'Chit, Advance, Order Advance adjustments',
        defaults: {
            font_size: '12px', condition_key: 'has_payment_details',
            show_chit: true, show_advance: true, show_order_advance: true
        },
        preview: function(s) {
            var S = BLOCK_DEFS_V2._samples;
            var html = '<div style="max-width:250px;"><div class="bvp-pay-title">Adjustments</div>';
            if(s.show_chit!==false) html += '<div class="bvp-tax-row"><span>Chit Adjustment</span><span>₹'+S.chit_adj+'</span></div>';
            if(s.show_advance!==false) html += '<div class="bvp-tax-row"><span>Advance</span><span>₹'+S.advance_adj+'</span></div>';
            html += '</div>';
            return html;
        }
    },

    // ── Footer Blocks ──────────────────────────
    'signature-block': {
        label: 'Signature Area', icon: '✍️', category: 'Footer',
        desc: 'Customer & authorized signature lines',
        defaults: { show_customer_sign: true, show_auth_sign: true, show_cashier: false, spacing_top: '40px' },
        preview: function(s) {
            var html = '<div class="bvp-signature" style="margin-top:12px;">';
            if (s.show_customer_sign!==false) html += '<div class="bvp-sig-col"><div class="bvp-sig-line" style="width:120px;border-top:1px solid var(--d2-text-muted);margin:0 auto;"></div><div class="bvp-sig-label">Customer Signature</div></div>';
            if (s.show_cashier) html += '<div class="bvp-sig-col"><div class="bvp-sig-line" style="width:120px;border-top:1px solid var(--d2-text-muted);margin:0 auto;"></div><div class="bvp-sig-label">Cashier</div></div>';
            if (s.show_auth_sign!==false) html += '<div class="bvp-sig-col"><div class="bvp-sig-line" style="width:120px;border-top:1px solid var(--d2-text-muted);margin:0 auto;"></div><div class="bvp-sig-label">Authorized Signatory</div></div>';
            html += '</div>';
            return html;
        }
    },

    // ── Advanced ──────────────────────────────────
    'conditional-section': {
        label: 'Show Only If...', icon: '🔀', category: 'Advanced',
        desc: 'Show this section only when condition is met',
        defaults: { condition_key: 'has_sales_items', content: '' },
        preview: function(s) {
            var condLabels = {
                'has_sales_items': 'bill has sales items',
                'has_purchase_items': 'bill has exchange/purchase items',
                'has_return_items': 'bill has return items',
                'has_payment_details': 'bill has payment details',
                'has_order_advance_entries': 'order has advance entries',
                'has_chit_items': 'bill has chit items',
            };
            var label = condLabels[s.condition_key] || s.condition_key || 'condition';
            return '<div class="bvp-generic">🔀 Show only if <strong>' + label + '</strong></div>';
        }
    },

    'custom-html': {
        label: 'Custom Section', icon: '🧩', category: 'Advanced',
        desc: 'For IT team — paste custom HTML (advanced)',
        defaults: { custom_html: '', css: '' },
        preview: function(s) {
            if (s.custom_html) {
                var stripped = s.custom_html.replace(/<[^>]+>/g, '').substring(0, 100);
                return '<div class="bvp-generic">🧩 Custom: ' + (stripped || 'Empty') + '</div>';
            }
            return '<div class="bvp-generic">🧩 Empty Custom Section</div>';
        }
    },

    'credit-collection-table': {
        label: 'Credit Collection History', icon: '📑', category: 'Tables',
        desc: 'Credit payment collection records',
        defaults: { font_size: '12px', condition_key: 'has_credit_collection_history' },
        preview: function() {
            return '<table class="bvp-mini-table"><thead><tr><th>Date</th><th>Receipt No</th><th>Amount</th></tr></thead>'
                + '<tbody><tr><td>15-01-2026</td><td>REC-001</td><td>₹25,000</td></tr>'
                + '<tr><td>15-02-2026</td><td>REC-012</td><td>₹25,000</td></tr></tbody></table>';
        }
    },

    'chit-preclose-table': {
        label: 'Chit Pre-Close Details', icon: '🔒', category: 'Tables',
        desc: 'Scheme pre-closure breakdown',
        defaults: { font_size: '12px', condition_key: 'has_chit_items' },
        preview: function() {
            return '<table class="bvp-mini-table"><thead><tr><th>Scheme</th><th>Paid</th><th>Bonus</th><th>Total</th></tr></thead>'
                + '<tbody><tr><td>Gold Plan A</td><td>₹1,20,000</td><td>₹10,000</td><td>₹1,30,000</td></tr></tbody></table>';
        }
    },
};

/**
 * Available fields organized by group — for the visual Field Picker dropdown
 * Users pick from these lists, they never type code
 */
window.FIELD_GROUPS = {
    'Company': [
        {key:'company_name', label:'Company Name'}, {key:'company_address', label:'Address'},
        {key:'company_city', label:'City'}, {key:'company_pincode', label:'Pincode'},
        {key:'company_mobile', label:'Phone'}, {key:'company_email', label:'Email'},
        {key:'company_gstin', label:'GSTIN'}, {key:'company_pan', label:'PAN'},
        {key:'branch_name', label:'Branch Name'},
    ],
    'Customer': [
        {key:'customer_name', label:'Customer Name'}, {key:'customer_mobile', label:'Mobile'},
        {key:'customer_address', label:'Address'}, {key:'customer_city', label:'City'},
        {key:'customer_pincode', label:'Pincode'}, {key:'customer_gstin', label:'GSTIN'},
        {key:'customer_pan', label:'PAN'}, {key:'customer_adhar', label:'Aadhar'},
    ],
    'Bill Details': [
        {key:'invoice_no', label:'Invoice No'}, {key:'bill_date', label:'Date'},
        {key:'bill_time', label:'Time'}, {key:'title', label:'Bill Title'},
        {key:'billed_by', label:'Billed By'}, {key:'irnno', label:'IRN No'},
        {key:'ack_no', label:'Ack No'},
    ],
    'Rates': [
        {key:'gold_rate', label:'Gold Rate (22ct)'}, {key:'gold_rate_18ct', label:'Gold Rate (18ct)'},
        {key:'silver_rate', label:'Silver Rate'},
    ],
    'Tax & Amount': [
        {key:'sub_total', label:'Sub Total'}, {key:'sgst_amount', label:'SGST'},
        {key:'cgst_amount', label:'CGST'}, {key:'igst_amount', label:'IGST'},
        {key:'round_off', label:'Round Off'}, {key:'net_payable', label:'Net Payable'},
        {key:'grand_total', label:'Grand Total'}, {key:'amount_in_words', label:'Amount in Words'},
    ],
    'Payment': [
        {key:'cash_amount', label:'Cash'}, {key:'card_amount', label:'Card'},
        {key:'upi_amount', label:'UPI'}, {key:'cheque_amount', label:'Cheque'},
        {key:'balance_amount', label:'Balance'}, {key:'paid_amount', label:'Total Paid'},
    ],
    'Adjustments': [
        {key:'chit_adj', label:'Chit Adjustment'}, {key:'advance_adj', label:'Advance'},
        {key:'order_advance_adj', label:'Order Advance'}, {key:'receipt_adj', label:'Receipt'},
    ],
    'Item Columns': [
        {key:'sno', label:'S.No'}, {key:'description', label:'Description'},
        {key:'hsn_code', label:'HSN Code'}, {key:'purity', label:'Purity'},
        {key:'qty', label:'Qty/PCS'}, {key:'gross_wt', label:'Gross Wt'},
        {key:'net_wt', label:'Net Wt'}, {key:'va_percent', label:'VA %'},
        {key:'va_content', label:'VA Content'}, {key:'mc', label:'MC (Making Charge)'},
        {key:'rate', label:'Rate'}, {key:'amount', label:'Amount'},
        {key:'stone_less', label:'Stone Less'}, {key:'metal_less', label:'Metal Less'},
        {key:'taxable_amount', label:'Taxable Amount'},
    ],
};

/**
 * Condition labels — human-readable (no code shown to user)
 */
window.CONDITION_LABELS = {
    'none': 'Always show',
    'has_sales_items': 'When bill has sales items',
    'has_purchase_items': 'When bill has exchange/purchase items',
    'has_return_items': 'When bill has return items',
    'has_payment_details': 'When bill has payment details',
    'has_order_advance_entries': 'When order has advance entries',
    'has_credit_collection_history': 'When bill has credit history',
    'has_chit_items': 'When bill has chit items',
    'has_repair_items': 'When bill has repair items',
    'show_sales_title': 'When Sales title is needed',
    'qr_code_image': 'When QR code is available',
};
