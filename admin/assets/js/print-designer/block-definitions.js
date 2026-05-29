/**
 * Block Definitions — Print Template Designer
 * 
 * Defines all available block types with their metadata and settings schemas.
 * Each block type has:
 *   - label: Display name
 *   - icon: Emoji icon for the palette and cards
 *   - category: Grouping in the palette (Header, Body, Footer)
 *   - description: Short tooltip/help text
 *   - settings: Array of configurable options with types and defaults
 *   - applicableCategories: (optional) Array of template category IDs where this block is relevant
 *                           If omitted, the block is available for all categories.
 * 
 * Setting types:
 *   - toggle: boolean on/off switch
 *   - select: dropdown with predefined options
 *   - column-picker: multi-checkbox for toggling table columns
 *   - text: free text input
 *   - number: numeric input
 */

const BLOCK_DEFINITIONS = {

    // ─── HEADER BLOCKS ─────────────────────────────────────────

    'company-header': {
        label: 'Company Header',
        icon: '🏢',
        category: 'Header',
        description: 'Company name, address, contact, GSTIN',
        hasFieldEditor: true,
        fieldGroups: [
            { key: 'company', label: 'Company Fields' }
        ],
        defaultFields: [
            { label: '', placeholder: 'company_name', group: 'company', style: 'title' },
            { label: '', placeholder: 'company_address', group: 'company' },
            { label: 'Ph:', placeholder: 'company_mobile', group: 'company' },
            { label: 'GSTIN:', placeholder: 'company_gstin', group: 'company' }
        ],
        availablePlaceholders: [
            { key: 'company_name', label: 'Company Name', group: 'company' },
            { key: 'company_address', label: 'Company Address', group: 'company' },
            { key: 'company_mobile', label: 'Company Mobile', group: 'company' },
            { key: 'company_gstin', label: 'Company GSTIN', group: 'company' },
            { key: 'company_email', label: 'Company Email', group: 'company' }
        ],
        settings: [
            { key: 'show_logo', label: 'Show Logo', type: 'toggle', default: true },
            { key: 'font_size', label: 'Font Size', type: 'select', 
              options: ['10px','12px','14px','16px','18px'], default: '14px' },
            { key: 'text_align', label: 'Alignment', type: 'select', 
              options: ['left','center','right'], default: 'center' },
            { key: 'border_bottom', label: 'Bottom Border', type: 'toggle', default: true },
            { key: 'repeat_every_page', label: 'Repeat on Every Page', type: 'toggle', default: true }
        ]
    },

    'customer-info': {
        label: 'Customer & Invoice Info',
        icon: '👤',
        category: 'Header',
        description: 'Customer details + Invoice number, date, rates',
        hasFieldEditor: true,
        fieldGroups: [
            { key: 'customer', label: 'Customer Fields' },
            { key: 'invoice', label: 'Invoice Fields' }
        ],
        defaultFields: [
            { label: 'To:', placeholder: 'customer_name', group: 'customer' },
            { label: '',    placeholder: 'customer_address', group: 'customer' },
            { label: '',    placeholder: 'customer_city', group: 'customer', suffix: ' - {{customer_pincode}}' },
            { label: 'Ph:', placeholder: 'customer_mobile', group: 'customer' },
            { label: 'PAN:', placeholder: 'customer_pan', group: 'customer' },
            { label: 'INVOICE NO', placeholder: 'invoice_no', group: 'invoice' },
            { label: 'DATE', placeholder: 'bill_date', group: 'invoice' },
            { label: 'TIME', placeholder: 'bill_time', group: 'invoice' },
            { label: 'GOLD 22-KT', placeholder: 'gold_rate', group: 'invoice', suffix: '/GM' },
            { label: 'GOLD 18-KT', placeholder: 'gold_rate_18ct', group: 'invoice', suffix: '/GM' },
            { label: 'SILVER', placeholder: 'silver_rate', group: 'invoice', suffix: '/GM' }
        ],
        availablePlaceholders: [
            { key: 'customer_name', label: 'Customer Name', group: 'customer' },
            { key: 'customer_address', label: 'Customer Address', group: 'customer' },
            { key: 'customer_city', label: 'Customer City', group: 'customer' },
            { key: 'customer_pincode', label: 'Customer Pincode', group: 'customer' },
            { key: 'customer_mobile', label: 'Customer Mobile', group: 'customer' },
            { key: 'customer_pan', label: 'Customer PAN', group: 'customer' },
            { key: 'customer_gstin', label: 'Customer GSTIN', group: 'customer' },
            { key: 'customer_adhar', label: 'Customer Aadhar', group: 'customer' },
            { key: 'customer_passport', label: 'Customer Passport', group: 'customer' },
            { key: 'invoice_no', label: 'Invoice No', group: 'invoice' },
            { key: 'bill_date', label: 'Bill Date', group: 'invoice' },
            { key: 'bill_time', label: 'Bill Time', group: 'invoice' },
            { key: 'title', label: 'Bill Title', group: 'invoice' },
            { key: 'irnno', label: 'IRN No', group: 'invoice' },
            { key: 'ack_no', label: 'Ack No', group: 'invoice' },
            { key: 'gold_rate', label: 'Gold Rate (22ct)', group: 'invoice' },
            { key: 'gold_rate_18ct', label: 'Gold Rate (18ct)', group: 'invoice' },
            { key: 'silver_rate', label: 'Silver Rate', group: 'invoice' },
            { key: 'billed_by', label: 'Billed By', group: 'invoice' }
        ],
        settings: [
            { key: 'layout', label: 'Layout', type: 'select', 
              options: ['two-column', 'single-column'], default: 'two-column' },
            { key: 'show_borders', label: 'Show Column Borders', type: 'toggle', default: true },
            { key: 'font_size', label: 'Font Size', type: 'select', 
              options: ['10px','11px','12px','13px','14px'], default: '12px' }
        ]
    },

    // ─── BODY / TABLE BLOCKS ───────────────────────────────────

    'items-table': {
        label: 'Items Table (Sales/Order)',
        icon: '📋',
        category: 'Body',
        description: 'Main items table with configurable columns',
        conditionKey: 'has_sales_items',
        settings: [
            { key: 'table_title', label: 'Table Title', type: 'text', default: '' },
            { key: 'font_size', label: 'Font Size', type: 'select', 
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'show_footer_totals', label: 'Show Totals Row', type: 'toggle', default: true },
            { key: 'loop_key', label: 'Data Source', type: 'select',
              options: ['items', 'order_items'], default: 'items' },
            { key: 'columns', label: 'Visible Columns', type: 'column-picker',
              options: [
                  { key: 'sno', label: 'S.No', default: true },
                  { key: 'description', label: 'Description', default: true },
                  { key: 'hsn_code', label: 'HSN Code', default: true },
                  { key: 'purity', label: 'Purity', default: false },
                  { key: 'qty', label: 'Qty/PCS', default: true },
                  { key: 'gross_wt', label: 'Gross Wt', default: true },
                  { key: 'net_wt', label: 'Net Wt', default: true },
                  { key: 'va_percent', label: 'VA %', default: false },
                  { key: 'mc', label: 'MC', default: false },
                  { key: 'rate', label: 'Rate', default: true },
                  { key: 'amount', label: 'Amount', default: true }
              ],
              default: ['sno','description','hsn_code','qty','gross_wt','net_wt','rate','amount']
            }
        ]
    },

    'purchase-table': {
        label: 'Purchase Items Table',
        icon: '🛒',
        category: 'Body',
        description: 'Purchase items with stone/metal less columns',
        conditionKey: 'has_purchase_items',
        applicableCategories: [2, 4], // Sales & Purchase, Purchase
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select', 
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'show_footer_totals', label: 'Show Totals Row', type: 'toggle', default: true },
            { key: 'columns', label: 'Visible Columns', type: 'column-picker',
              options: [
                  { key: 'sno', label: 'S.No', default: true },
                  { key: 'description', label: 'Description', default: true },
                  { key: 'hsn_code', label: 'HSN Code', default: true },
                  { key: 'qty', label: 'Qty/PCS', default: true },
                  { key: 'gross_wt', label: 'Gross Wt', default: true },
                  { key: 'net_wt', label: 'Net Wt', default: true },
                  { key: 'stone_less', label: 'Stone Less', default: true },
                  { key: 'metal_less', label: 'Metal Less', default: true },
                  { key: 'va_percent', label: 'VA %', default: false },
                  { key: 'rate', label: 'Rate', default: true },
                  { key: 'amount', label: 'Amount', default: true }
              ],
              default: ['sno','description','hsn_code','qty','gross_wt','net_wt','stone_less','metal_less','rate','amount']
            }
        ]
    },

    'sales-return-table': {
        label: 'Sales Return / Exchange Table',
        icon: '🔄',
        category: 'Body',
        description: 'Return/exchange items table',
        conditionKey: 'has_return_items',
        applicableCategories: [3, 7], // Sales & Return, Sales Return
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select', 
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'show_footer_totals', label: 'Show Totals Row', type: 'toggle', default: true },
            { key: 'columns', label: 'Visible Columns', type: 'column-picker',
              options: [
                  { key: 'sno', label: 'S.No', default: true },
                  { key: 'description', label: 'Description', default: true },
                  { key: 'hsn_code', label: 'HSN Code', default: true },
                  { key: 'qty', label: 'Qty/PCS', default: true },
                  { key: 'gross_wt', label: 'Gross Wt', default: true },
                  { key: 'net_wt', label: 'Net Wt', default: true },
                  { key: 'va_percent', label: 'VA %', default: false },
                  { key: 'rate', label: 'Rate', default: true },
                  { key: 'taxable_amount', label: 'Taxable Amt', default: false },
                  { key: 'amount', label: 'Amount', default: true }
              ],
              default: ['sno','description','hsn_code','qty','gross_wt','net_wt','rate','amount']
            }
        ]
    },

    'credit-collection-table': {
        label: 'Credit Collection Table',
        icon: '💳',
        category: 'Body',
        description: 'Credit bill payment details',
        conditionKey: 'has_credit_collection_history',
        applicableCategories: [8], // Credit Bill Payment
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'columns', label: 'Visible Columns', type: 'column-picker',
              options: [
                  { key: 'sno', label: 'S.No', default: true },
                  { key: 'bill_no', label: 'Bill No', default: true },
                  { key: 'bill_date', label: 'Bill Date', default: true },
                  { key: 'bill_amount', label: 'Bill Amount', default: true },
                  { key: 'due_amount', label: 'Due Amount', default: true },
                  { key: 'received_amount', label: 'Received Amt', default: true },
                  { key: 'balance', label: 'Balance', default: true }
              ],
              default: ['sno','bill_no','bill_date','bill_amount','due_amount','received_amount','balance']
            }
        ]
    },

    'chit-preclose-table': {
        label: 'Chit Pre-Close Table',
        icon: '🪙',
        category: 'Body',
        description: 'Chit scheme pre-close details',
        conditionKey: 'has_chit_items',
        applicableCategories: [10], // Chit Proclose
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'columns', label: 'Visible Columns', type: 'column-picker',
              options: [
                  { key: 'sno', label: 'S.No', default: true },
                  { key: 'scheme_name', label: 'Scheme', default: true },
                  { key: 'months', label: 'Months', default: true },
                  { key: 'due_amount', label: 'Due Amount', default: true },
                  { key: 'received_amount', label: 'Received', default: true },
                  { key: 'balance', label: 'Balance', default: true }
              ],
              default: ['sno','scheme_name','months','due_amount','received_amount','balance']
            }
        ]
    },

    // ─── FOOTER / SUMMARY BLOCKS ──────────────────────────────

    'advance-payment': {
        label: 'Advance / Payment Details',
        icon: '💰',
        category: 'Footer',
        description: 'Sub total, advance, payment mode breakdown',
        applicableCategories: [5, 6], // Order Advance, Advance
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'show_sub_total', label: 'Show Sub Total', type: 'toggle', default: true },
            { key: 'show_approx_total', label: 'Show Approx Total', type: 'toggle', default: true },
            { key: 'show_payment_modes', label: 'Show Payment Modes', type: 'toggle', default: true }
        ]
    },

    'tax-summary': {
        label: 'Tax Summary / Grand Total',
        icon: '🧾',
        category: 'Footer',
        description: 'Subtotal, CGST, SGST, IGST, Round Off, Grand Total',
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'show_subtotal', label: 'Show Subtotal', type: 'toggle', default: true },
            { key: 'show_cgst', label: 'Show CGST', type: 'toggle', default: true },
            { key: 'show_sgst', label: 'Show SGST', type: 'toggle', default: true },
            { key: 'show_igst', label: 'Show IGST', type: 'toggle', default: false },
            { key: 'show_round_off', label: 'Show Round Off', type: 'toggle', default: true },
            { key: 'show_amount_words', label: 'Amount in Words', type: 'toggle', default: true }
        ]
    },

    'payment-info': {
        label: 'Payment Modes',
        icon: '🏦',
        category: 'Footer',
        description: 'Cash, Card, UPI, Cheque payment breakdown',
        conditionKey: 'has_payment_details',
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'show_cash', label: 'Show Cash', type: 'toggle', default: true },
            { key: 'show_card', label: 'Show Card', type: 'toggle', default: true },
            { key: 'show_upi', label: 'Show UPI', type: 'toggle', default: true },
            { key: 'show_cheque', label: 'Show Cheque', type: 'toggle', default: true },
            { key: 'show_balance', label: 'Show Balance', type: 'toggle', default: true }
        ]
    },

    'remark-block': {
        label: 'Remark / Notes',
        icon: '📝',
        category: 'Footer',
        description: 'Remark, additional notes, or terms',
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px'], default: '11px' },
            { key: 'show_label', label: 'Show "Remark:" Label', type: 'toggle', default: true }
        ]
    },

    'signature-block': {
        label: 'Signature Area',
        icon: '✍️',
        category: 'Footer',
        description: 'Customer and authorized signatory areas',
        settings: [
            { key: 'layout', label: 'Layout', type: 'select',
              options: ['two-column', 'three-column'], default: 'two-column' },
            { key: 'show_customer_sign', label: 'Customer Signature', type: 'toggle', default: true },
            { key: 'show_auth_sign', label: 'Authorized Signatory', type: 'toggle', default: true },
            { key: 'show_cashier', label: 'Cashier', type: 'toggle', default: false },
            { key: 'spacing_top', label: 'Top Spacing', type: 'select', 
              options: ['20px','40px','60px','80px'], default: '40px' }
        ]
    },

    'amount-words': {
        label: 'Amount in Words',
        icon: '🔤',
        category: 'Footer',
        description: 'Grand total written in words',
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px'], default: '12px' },
            { key: 'font_weight', label: 'Bold', type: 'toggle', default: true },
            { key: 'show_prefix', label: 'Show "Rupees" Prefix', type: 'toggle', default: true }
        ]
    },

    'footer': {
        label: 'Footer',
        icon: '🔻',
        category: 'Footer',
        description: 'Customer Signature (left) & For Company Name (right)',
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px','14px'], default: '12px' },
            { key: 'spacing_top', label: 'Top Spacing', type: 'select',
              options: ['20px','30px','40px','60px','80px'], default: '40px' },
            { key: 'repeat_every_page', label: 'Repeat on Every Page', type: 'toggle', default: true }
        ]
    },

    // ─── LAYOUT / UTILITY BLOCKS ─────────────────────────────────

    'custom-text': {
        label: 'Custom Text',
        icon: '📄',
        category: 'Layout',
        description: 'Free-text content (terms, notes, headers)',
        settings: [
            { key: 'content', label: 'Content', type: 'textarea', default: '' },
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px','14px','16px','18px'], default: '12px' },
            { key: 'text_align', label: 'Alignment', type: 'select',
              options: ['left','center','right'], default: 'left' },
            { key: 'bold', label: 'Bold', type: 'toggle', default: false }
        ]
    },

    'spacer': {
        label: 'Spacer',
        icon: '↕️',
        category: 'Layout',
        description: 'Adjustable vertical spacing',
        settings: [
            { key: 'height', label: 'Height', type: 'select',
              options: ['10px','20px','30px','40px','50px','60px','80px'], default: '20px' }
        ]
    },

    'page-break': {
        label: 'Page Break',
        icon: '📃',
        category: 'Layout',
        description: 'Force page break when printing',
        settings: []
    },

    'custom-html': {
        label: 'Custom HTML',
        icon: '🖥️',
        category: 'Layout',
        description: 'Custom HTML block (imported legacy design)',
        settings: [
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['10px','11px','12px','13px','14px'], default: '12px' }
        ]
    },

    'line-solid': {
        label: 'Solid Line',
        icon: '─',
        category: 'Layout',
        description: 'A solid horizontal rule',
        settings: [
            { key: 'color',         label: 'Line Color',    type: 'text',   default: '#000000' },
            { key: 'thickness',     label: 'Thickness (px)',type: 'select', options: ['1','2','3','4','5'], default: '1' },
            { key: 'width',         label: 'Width %',       type: 'select', options: ['25','50','75','100'], default: '100' },
            { key: 'margin_top',    label: 'Top Margin',    type: 'select', options: ['0px','5px','10px','15px','20px'], default: '5px' },
            { key: 'margin_bottom', label: 'Bottom Margin', type: 'select', options: ['0px','5px','10px','15px','20px'], default: '5px' }
        ]
    },

    'line-dashed': {
        label: 'Dashed Line',
        icon: '╌',
        category: 'Layout',
        description: 'A dashed horizontal rule',
        settings: [
            { key: 'color',         label: 'Line Color',    type: 'text',   default: '#000000' },
            { key: 'thickness',     label: 'Thickness (px)',type: 'select', options: ['1','2','3','4','5'], default: '1' },
            { key: 'width',         label: 'Width %',       type: 'select', options: ['25','50','75','100'], default: '100' },
            { key: 'margin_top',    label: 'Top Margin',    type: 'select', options: ['0px','5px','10px','15px','20px'], default: '5px' },
            { key: 'margin_bottom', label: 'Bottom Margin', type: 'select', options: ['0px','5px','10px','15px','20px'], default: '5px' }
        ]
    },

    'line-dotted': {
        label: 'Dotted Line',
        icon: '┈',
        category: 'Layout',
        description: 'A dotted horizontal rule',
        settings: [
            { key: 'color',         label: 'Line Color',    type: 'text',   default: '#000000' },
            { key: 'thickness',     label: 'Thickness (px)',type: 'select', options: ['1','2','3','4','5'], default: '1' },
            { key: 'width',         label: 'Width %',       type: 'select', options: ['25','50','75','100'], default: '100' },
            { key: 'margin_top',    label: 'Top Margin',    type: 'select', options: ['0px','5px','10px','15px','20px'], default: '5px' },
            { key: 'margin_bottom', label: 'Bottom Margin', type: 'select', options: ['0px','5px','10px','15px','20px'], default: '5px' }
        ]
    },

    'divider': {
        label: 'Divider Line',
        icon: '➖',
        category: 'Layout',
        description: 'Click directly on the line in the canvas to edit it',
        settings: [
            { key: 'line_style', label: 'Line Style', type: 'select',
              options: [
                  '─── Solid Line ───',
                  '- - - Dashed - - -',
                  '· · · Dotted · · ·',
                  '═══ Double ═══',
                  '* * * Stars * * *',
                  '— — — Em Dash — — —',
                  '▬▬▬▬▬ Block ▬▬▬▬▬',
                  '(custom)'
              ],
              default: '- - - Dashed - - -' },
            { key: 'content', label: 'Divider Text', type: 'text', default: '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -' },
            { key: 'font_size', label: 'Font Size', type: 'select',
              options: ['8px', '10px', '12px', '14px', '16px'], default: '12px' },
            { key: 'text_align', label: 'Alignment', type: 'select',
              options: ['left', 'center', 'right'], default: 'center' },
            { key: 'margin_top', label: 'Top Margin', type: 'select',
              options: ['0px', '5px', '10px', '15px', '20px'], default: '5px' },
            { key: 'margin_bottom', label: 'Bottom Margin', type: 'select',
              options: ['0px', '5px', '10px', '15px', '20px'], default: '5px' }
        ]
    },

    'conditional-section': {
        label: 'Conditional Section',
        icon: '🔀',
        category: 'Layout',
        description: 'Show/hide content based on a condition',
        settings: [
            { key: 'condition_key', label: 'Condition Variable', type: 'select',
              options: [
                  'has_sales_items', 'has_purchase_items', 'has_return_items',
                  'has_old_metal', 'has_advance_details', 'has_payment_details',
                  'has_payment_breakdown', 'has_credit_collection_history',
                  'has_order_items', 'has_chit_items', 'has_repair_items',
                  'has_order_advance_entries'
              ], default: 'has_sales_items' },
            { key: 'content', label: 'Content', type: 'textarea', default: '' }
        ]
    }
};

// List of all available condition variables
const CONDITION_VARIABLES = [
    'none',
    'has_sales_items', 'has_purchase_items', 'has_return_items',
    'has_chit_items', 'has_payment_details', 'has_credit_collection_history',
    'has_repair_items', 'has_order_advance_entries', 'has_advance_details',
    'has_transfer_items'
];

// Auto-inject 'Repeat on Every Page' toggle and 'Condition Variable' into every block type's settings
(function() {
    const repeatSetting = { key: 'repeat_every_page', label: 'Repeat on Every Page', type: 'toggle', default: false };
    for (const [key, def] of Object.entries(BLOCK_DEFINITIONS)) {
        const hasRepeat = def.settings.some(s => s.key === 'repeat_every_page');
        if (!hasRepeat) {
            def.settings.push(repeatSetting);
        }
        // Auto-inject condition_key setting (skip conditional-section which already has it)
        if (key !== 'conditional-section') {
            const hasCondition = def.settings.some(s => s.key === 'condition_key');
            if (!hasCondition) {
                def.settings.push({
                    key: 'condition_key',
                    label: 'Condition Variable',
                    type: 'select',
                    options: CONDITION_VARIABLES,
                    default: def.conditionKey || 'none'
                });
            }
        }
    }
})();

// Helper: Get block definitions filtered by template category
function getBlocksForCategory(categoryId) {
    const filtered = {};
    for (const [key, def] of Object.entries(BLOCK_DEFINITIONS)) {
        if (!def.applicableCategories || def.applicableCategories.includes(parseInt(categoryId))) {
            filtered[key] = def;
        }
    }
    return filtered;
}

// Helper: Get default settings for a block type
function getDefaultSettings(blockType) {
    const def = BLOCK_DEFINITIONS[blockType];
    if (!def) return {};
    
    const settings = {};
    for (const s of def.settings) {
        if (s.type === 'column-picker') {
            // Default is the array of default-true column keys
            settings[s.key] = s.default || s.options.filter(o => o.default).map(o => o.key);
        } else {
            settings[s.key] = s.default;
        }
    }
    // Initialize fields from defaultFields for blocks with field editor
    if (def.hasFieldEditor && def.defaultFields) {
        settings.fields = JSON.parse(JSON.stringify(def.defaultFields));
    }
    return settings;
}

// Helper: Generate unique block ID
function generateBlockId() {
    return 'blk_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 5);
}
