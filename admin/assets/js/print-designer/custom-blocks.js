// Custom GrapesJS Blocks for Print Designer

function registerCustomComponents(editor) {
    // Add custom component for Resizable Declaration
    editor.DomComponents.addType('declaration-block', {
        isComponent: el => el.classList && el.classList.contains('declaration-block'),
        model: {
            defaults: {
                tagName: 'div',
                draggable: true,
                droppable: false,
                resizable: true, // Enable native resizing
                editable: true,
                style: {
                    'border': '1px solid #000',
                    'padding': '5px',
                    'font-size': '10px',
                    'margin-top': '10px',
                    'box-sizing': 'border-box',
                    'page-break-inside': 'avoid',
                    'white-space': 'normal',
                    'word-wrap': 'break-word',
                    'overflow-wrap': 'break-word',
                    'width': '100%',
                    'min-height': '50px'
                }
            }
        }
    });

    // Add custom component for Resizable Text Block (Global Auto-Scaling)
    editor.DomComponents.addType('resizable-text-block', {
        isComponent: el => (el.classList && el.classList.contains('resizable-text-block')) || (el.getAttribute && el.getAttribute('data-gjs-type') === 'resizable-text-block'),
        model: {
            defaults: {
                tagName: 'div',
                draggable: true,
                droppable: false,
                resizable: true, 
                editable: true,
                traits: [
                    {
                        type: 'number',
                        name: 'fontsize',
                        label: 'Font Size (px)',
                        changeProp: 1, 
                        min: 8,
                        max: 100
                    }
                ],
                style: {
                    'padding': '5px',
                    'box-sizing': 'border-box',
                    'min-height': '50px',
                    // Default font size can be overridden by specific blocks
                }
            },
            init() {
                this.on('change:fontsize', this.handleFontSizeChange);
                this.on('change:style:width', this.handleWidthChange);
                
                // Set initial trait value from style if exists
                 const style = this.getStyle();
                 const currentFontSize = style['font-size'];
                 if(currentFontSize) {
                     this.set('fontsize', parseInt(currentFontSize));
                 }

                 // Initialize _prevWidth to make resizing work on the first movement
                 const currentWidth = style.width;
                 if (currentWidth && currentWidth.endsWith('px')) {
                     this._prevWidth = parseFloat(currentWidth);
                 }
            },
            handleWidthChange() {
                const style = this.getStyle();
                const width = style.width;
                
                if (width && width.endsWith('px')) {
                    const currentWidth = parseFloat(width);
                    const prevWidth = this._prevWidth;
                    
                    if (prevWidth) {
                        const ratio = currentWidth / prevWidth;
                        if(ratio > 0.1 && ratio < 10) {
                            // Get current font size from trait or style or default
                            let currentFontSize = this.get('fontsize');
                            if (!currentFontSize) {
                                const styleFs = this.getStyle()['font-size'];
                                currentFontSize = styleFs ? parseInt(styleFs) : 12;
                            }

                            let newFontSize = Math.round(currentFontSize * ratio);
                            
                            if (newFontSize < 8) newFontSize = 8;
                            if (newFontSize > 100) newFontSize = 100;
                            
                            if (newFontSize !== currentFontSize) {
                                this.set('fontsize', newFontSize); 
                            }
                        }
                    }
                    this._prevWidth = currentWidth;
                }
            },
            handleFontSizeChange() {
                const fontSize = this.get('fontsize');
                this.addStyle({ 'font-size': fontSize + 'px' });
            }
        }
    });

    // Add custom component for Invoice Title
    editor.DomComponents.addType('invoice-title', {
        isComponent: el => el.classList && el.classList.contains('invoice-title-block'),
        model: {
            defaults: {
                tagName: 'div',
                draggable: true,
                resizable: true,
                editable: true,
                traits: [
                    { type: 'text', name: 'label', label: 'Title Text', changeProp: 1 },
                    { type: 'number', name: 'fontsize', label: 'Font Size (px)', changeProp: 1, min: 8, max: 100 }
                ],
                style: { 'text-align': 'center', 'font-weight': 'bold', 'font-size': '24px', 'margin': '10px 0' }
            },
            init() {
                this.on('change:label', this.handleLabelChange);
                this.on('change:fontsize', this.handleFontSizeChange);
            },
            handleLabelChange() {
                const label = this.get('label') || 'TAX INVOICE';
                this.set('content', label);
            },
            handleFontSizeChange() {
                const fontSize = this.get('fontsize');
                this.addStyle({ 'font-size': fontSize + 'px' });
            }
        }
    });

    // Add custom component for Customer Info
    editor.DomComponents.addType('customer-info-v2', {
        isComponent: el => el.classList && el.classList.contains('customer-info-block'),
        model: {
            defaults: {
                tagName: 'div',
                draggable: true,
                resizable: true,
                editable: true,
                traits: [
                    { type: 'text', name: 'to_label', label: 'To Label', changeProp: 1 },
                    { type: 'text', name: 'ph_lbl', label: 'Phone Label', changeProp: 1 },
                    { type: 'text', name: 'pan_lbl', label: 'PAN Label', changeProp: 1 },
                    { type: 'text', name: 'adhar_lbl', label: 'Aadhar Label', changeProp: 1 },
                    { type: 'text', name: 'gst_lbl', label: 'GSTIN Label', changeProp: 1 },
                    { type: 'number', name: 'fontsize', label: 'Font Size (px)', changeProp: 1, min: 8, max: 100 }
                ]
            },
            init() {
                this.on('change:to_label change:ph_lbl change:pan_lbl change:adhar_lbl change:gst_lbl', this.updateLabels);
                this.on('change:fontsize', this.handleFontSizeChange);
            },
            updateLabels() {
                const el = this.view.el;
                const mapping = {
                    'to_label': '.lbl-to',
                    'ph_lbl': '.lbl-cph',
                    'pan_lbl': '.lbl-cpan',
                    'adhar_lbl': '.lbl-cadhar',
                    'gst_lbl': '.lbl-cgst'
                };
                Object.keys(mapping).forEach(trait => {
                    const val = this.get(trait);
                    const labelEl = el.querySelector(mapping[trait]);
                    if (labelEl && val) labelEl.innerHTML = val;
                });
            },
            handleFontSizeChange() {
                const fontSize = this.get('fontsize');
                this.addStyle({ 'font-size': fontSize + 'px' });
            }
        }
    });

    // Add custom component for Bill Meta
    editor.DomComponents.addType('bill-meta-info', {
        isComponent: el => el.classList && el.classList.contains('bill-meta-block'),
        model: {
            defaults: {
                tagName: 'div',
                draggable: true,
                resizable: true,
                traits: [
                    { type: 'text', name: 'inv_lbl', label: 'Invoice No Label', changeProp: 1 },
                    { type: 'text', name: 'date_lbl', label: 'Date Label', changeProp: 1 },
                    { type: 'text', name: 'time_lbl', label: 'Time Label', changeProp: 1 },
                    { type: 'text', name: 'gold_lbl', label: 'Gold 22K Label', changeProp: 1 },
                    { type: 'text', name: 'gold18_lbl', label: 'Gold 18K Label', changeProp: 1 },
                    { type: 'text', name: 'silver_lbl', label: 'Silver Label', changeProp: 1 },
                    { type: 'number', name: 'fontsize', label: 'Font Size (px)', changeProp: 1, min: 8, max: 100 }
                ]
            },
            init() {
                const traitNames = ['inv_lbl', 'date_lbl', 'time_lbl', 'gold_lbl', 'gold18_lbl', 'silver_lbl'];
                traitNames.forEach(name => this.on('change:' + name, this.updateLabels));
                this.on('change:fontsize', this.handleFontSizeChange);
            },
            updateLabels() {
                const el = this.view.el;
                const mapping = {
                    'inv_lbl': '.lbl-inv',
                    'date_lbl': '.lbl-date',
                    'time_lbl': '.lbl-time',
                    'gold_lbl': '.lbl-gold',
                    'gold18_lbl': '.lbl-gold18',
                    'silver_lbl': '.lbl-silver'
                };
                Object.keys(mapping).forEach(trait => {
                    const selector = mapping[trait];
                    const val = this.get(trait);
                    const labelEl = el.querySelector(selector);
                    if (labelEl && val) labelEl.innerHTML = val;
                });
            },
            handleFontSizeChange() {
                const fontSize = this.get('fontsize');
                this.addStyle({ 'font-size': fontSize + 'px' });
            }
        }
    });

    // Add custom component for Company Header
    editor.DomComponents.addType('company-header-v2', {
        isComponent: el => el.classList && el.classList.contains('company-header-block'),
        model: {
            defaults: {
                tagName: 'div',
                draggable: true,
                resizable: true,
                traits: [
                    { type: 'text', name: 'ph_lbl', label: 'Phone Label', changeProp: 1 },
                    { type: 'text', name: 'gst_lbl', label: 'GSTIN Label', changeProp: 1 },
                    { type: 'text', name: 'email_lbl', label: 'Email Label', changeProp: 1 },
                    { type: 'number', name: 'fontsize', label: 'Font Size (px)', changeProp: 1, min: 8, max: 100 }
                ]
            },
            init() {
                this.on('change:ph_lbl change:gst_lbl change:email_lbl', this.updateLabels);
                this.on('change:fontsize', this.handleFontSizeChange);
            },
            updateLabels() {
                const el = this.view.el;
                const mapping = {
                    'ph_lbl': '.lbl-ph',
                    'gst_lbl': '.lbl-gstin',
                    'email_lbl': '.lbl-email'
                };
                Object.keys(mapping).forEach(trait => {
                    const val = this.get(trait);
                    const labelEl = el.querySelector(mapping[trait]);
                    if (labelEl && val) labelEl.innerHTML = val;
                });
            },
            handleFontSizeChange() {
                const fontSize = this.get('fontsize');
                this.addStyle({ 'font-size': fontSize + 'px' });
            }
        }
    });

    // Add custom component for Bill Footer
    editor.DomComponents.addType('bill-footer-v2', {
        isComponent: el => el.classList && el.classList.contains('bill-footer-v2-block'),
        model: {
            defaults: {
                tagName: 'div',
                draggable: true,
                resizable: true,
                traits: [
                    { type: 'text', name: 'sub_lbl', label: 'Subtotal Label', changeProp: 1 },
                    { type: 'text', name: 'cgst_lbl', label: 'CGST Label', changeProp: 1 },
                    { type: 'text', name: 'sgst_lbl', label: 'SGST Label', changeProp: 1 },
                    { type: 'text', name: 'igst_lbl', label: 'IGST Label', changeProp: 1 },
                    { type: 'text', name: 'round_lbl', label: 'Round Off Label', changeProp: 1 },
                    { type: 'text', name: 'grand_lbl', label: 'Grand Total Label', changeProp: 1 },
                    { type: 'number', name: 'fontsize', label: 'Font Size (px)', changeProp: 1, min: 8, max: 100 }
                ]
            },
            init() {
                const traitNames = ['sub_lbl', 'cgst_lbl', 'sgst_lbl', 'igst_lbl', 'round_lbl', 'grand_lbl'];
                traitNames.forEach(name => this.on('change:' + name, this.updateLabels));
                this.on('change:fontsize', this.handleFontSizeChange);
            },
            updateLabels() {
                const el = this.view.el;
                const mapping = {
                    'sub_lbl': '.lbl-sub',
                    'cgst_lbl': '.lbl-cgst',
                    'sgst_lbl': '.lbl-sgst',
                    'igst_lbl': '.lbl-igst',
                    'round_lbl': '.lbl-round',
                    'grand_lbl': '.lbl-grand'
                };
                Object.keys(mapping).forEach(trait => {
                    const selector = mapping[trait];
                    const val = this.get(trait);
                    const labelEl = el.querySelector(selector);
                    if (labelEl && val) labelEl.innerHTML = val;
                });
            },
            handleFontSizeChange() {
                const fontSize = this.get('fontsize');
                this.addStyle({ 'font-size': fontSize + 'px' });
            }
        }
    });
}

function addPrintBlocks(editor, templates = {}) {



    const bm = editor.BlockManager;

    // ===== HEADER BLOCKS =====


    bm.add('invoice-title', {
        label: 'Title Box',
        category: 'Basic',
        content: `
            <div class="invoice-title-block" data-gjs-type="invoice-title">
                TAX INVOICE
            </div>
        `,
        attributes: { class: 'fa fa-font' }
    });

    bm.add('company-header-v2', {
        label: 'Adv. Header (Logo)',
        category: 'Basic',
        content: `
            <div class="company-header-block" data-gjs-type="company-header-v2" style="text-align:center; margin-bottom:10px; padding: 5px; font-size: 14px;">
                <div style="display:flex; align-items:center; justify-content:center;">
                     <div style="margin-right:10px;">
                         <!-- Placeholder for Logo - User can replace src -->
                         <img src="https://via.placeholder.com/80" style="height:80px; width:auto;" />
                     </div>
                     <div>
                        <h2 style="margin:2px 0; font-size:1.5em;">{{company_name}}</h2>
                        <div style="font-size:0.9em;">{{company_address}}</div>
                        <div style="font-size:0.9em;"><span class="lbl-ph">Ph:</span> {{company_mobile}}</div>
                        <div style="font-size:0.9em;"><span class="lbl-gstin">GSTIN:</span> {{company_gstin}}</div>
                        <div style="font-size:0.9em;"><span class="lbl-email">Email:</span> {{company_email}}</div>
                     </div>
                </div>
            </div>
        `,
        attributes: { class: 'fa fa-building' }
    });

    bm.add('customer-info-v2', {
        label: 'Adv. Customer Info',
        category: 'Basic',
        content: `
            <div data-gjs-type="customer-info-v2" class="customer-info-block" style="font-weight:bold; font-size:12px; padding:5px;">
                <div style="margin-bottom:2px;" class="lbl-to">To:</div>
                <div style="padding: 2px;">{{customer_name}}</div>
                <div style="padding: 2px;">{{customer_address}}</div>
                <div style="padding: 2px;">{{customer_city}} - {{customer_pincode}}</div>
                <div style="padding: 2px;"><span class="lbl-cph">Ph:</span> {{customer_mobile}}</div>
                <div style="padding: 2px;"><span class="lbl-cpan">PAN:</span> {{customer_pan}}</div>
                <div style="padding: 2px;"><span class="lbl-cadhar">Aadhar:</span> {{customer_adhar}}</div>
                <div style="padding: 2px;"><span class="lbl-cgst">GSTIN:</span> {{customer_gstin}}</div>
            </div>
        `,
        attributes: { class: 'fa fa-user-plus' }
    });

    bm.add('bill-meta-info', {
        label: 'Adv. Bill Meta',
        category: 'Basic',
        content: `
            <div data-gjs-type="bill-meta-info" class="bill-meta-block" style="padding: 5px; font-size:12px;">
                <table style="width:100%; font-size:inherit; font-weight:bold; border-collapse: collapse;">
                    <tr>
                        <td style="padding:2px;" class="lbl-inv">INVOICE NO</td>
                        <td style="padding:2px;">:</td>
                        <td style="padding:2px;">{{invoice_no}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px;" class="lbl-date">DATE</td>
                        <td style="padding:2px;">:</td>
                        <td style="padding:2px;">{{bill_date}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px;" class="lbl-time">TIME</td>
                        <td style="padding:2px;">:</td>
                        <td style="padding:2px;">{{bill_time}}</td>
                    </tr>
                    <tr>
                        <td style="padding:2px;" class="lbl-gold">GOLD 22-KT</td>
                        <td style="padding:2px;">:</td>
                        <td style="padding:2px;">{{gold_rate}}/GM</td>
                    </tr>
                     <tr>
                        <td style="padding:2px;" class="lbl-gold18">GOLD 18-KT</td>
                        <td style="padding:2px;">:</td>
                        <td style="padding:2px;">{{gold_rate_18ct}}/GM</td>
                    </tr>
                    <tr>
                        <td style="padding:2px;" class="lbl-silver">SILVER</td>
                        <td style="padding:2px;">:</td>
                        <td style="padding:2px;">{{silver_rate}}/GM</td>
                    </tr>
                </table>
            </div>
        `,
        attributes: { class: 'fa fa-info-circle' }
    });


    // ===== INFO BLOCKS =====
    // Use template from API if available, else fallback to default


    // ===== TABLE BLOCKS =====




    // ===== OLD METAL TABLE =====
    const oldMetalTableContent = `
        <style> .dynamic-old-metal-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-old-metal-table" data-gjs-type="table-wrapper" style="display:block; padding:5px; margin-top:10px;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:5px;">Old Metal Purchase Details</div>
            <table class="old-metal-table" style="width:100%; border-collapse:collapse; font-size:inherit;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">S.No</th>
                        <th style="padding:4px; text-align:left;">Description</th>
                        <th style="padding:4px; text-align:center;">Purity</th>
                        <th style="padding:4px; text-align:center;">Qty</th>
                        <th style="padding:4px; text-align:right;">GRS.WT</th>
                        <th style="padding:4px; text-align:right;">NET.WT</th>
                        <th style="padding:4px; text-align:right;">Rate</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#old_metal_items}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{sno}}</td>
                        <td style="padding:4px;">{{description}}</td>
                         <td style="padding:4px; text-align:center;">{{purity}}</td>
                         <td style="padding:4px; text-align:center;">{{qty}}</td>
                        <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{rate}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/old_metal_items}} -->
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="3" style="padding:4px; text-align:center;">Total Purchase</td>
                        <td style="padding:4px; text-align:center;">{{total_old_metal_qty}}</td>
                        <td style="padding:4px; text-align:right;">{{total_old_metal_gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{total_old_metal_net_wt}}</td>
                        <td></td>
                        <td style="padding:4px; text-align:right;">{{total_old_metal_amount}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

    bm.add('old-metal-table', {
        label: 'Old Metal Table',
        category: 'Tables',
        content: oldMetalTableContent,
        attributes: { class: 'fa fa-recycle' }
    });
    
    // ===== SALES TRANSFER TABLE =====
     const transferTableContent = `
        <style> .dynamic-transfer-table { height: auto !important; min-height: 50px; } </style>
         <div class="dynamic-transfer-table" data-gjs-type="table-wrapper" style="display:block; padding:5px; margin-top:10px;">
            <div style="font-weight:bold; font-size:12px; margin-bottom:5px;">Sales Transfer Details</div>
            <table class="sales-transfer-table" style="width:100%; border-collapse:collapse; font-size:inherit;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">S.No</th>
                        <th style="padding:4px; text-align:left;">Description</th>
                        <th style="padding:4px; text-align:center;">HSN</th>
                        <th style="padding:4px; text-align:center;">PCS</th>
                        <th style="padding:4px; text-align:right;">GRS.WT</th>
                        <th style="padding:4px; text-align:right;">NET.WT</th>
                        <th style="padding:4px; text-align:right;">Rate</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#transfer_items}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{sno}}</td>
                        <td style="padding:4px;">{{description}}</td>
                         <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                         <td style="padding:4px; text-align:center;">{{qty}}</td>
                        <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{rate}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/transfer_items}} -->
                </tbody>
                 <tfoot>
                    <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="3" style="padding:4px; text-align:center;">Total Transfer</td>
                         <td style="padding:4px; text-align:center;">{{total_transfer_qty}}</td>
                        <td style="padding:4px; text-align:right;">{{total_transfer_gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{total_transfer_net_wt}}</td>
                        <td></td>
                        <td style="padding:4px; text-align:right;">{{total_transfer_amount}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

    bm.add('sales-transfer-table', {
        label: 'Transfer Table',
        category: 'Tables',
        content: transferTableContent,
        attributes: { class: 'fa fa-exchange' }
    });

    // ===== SALES TABLE V2 =====
    const itemsTableV2Content = `
            <style> .dynamic-sales-table-v2 { height: auto !important; min-height: 50px; } </style>
            <div class="dynamic-sales-table-v2" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
                <table class="sales-table-v2" style="width:100%; border-collapse:collapse; font-size:inherit;">
                    <thead>
                        <tr style="border-bottom: 1px solid #000;">
                            <th style="padding:4px;">S.No</th>
                            <th style="padding:4px; text-align:left;">Description</th>
                            <th style="padding:4px; text-align:center;">HSN</th>
                            <th style="padding:4px; text-align:center;">PCS</th>
                            <th style="padding:4px; text-align:right;">GRS.WT</th>
                            <th style="padding:4px; text-align:right;">NET.WT</th>
                            <th style="padding:4px; text-align:right;">V.A</th>
                            <th style="padding:4px; text-align:right;">MC</th>
                            <th style="padding:4px; text-align:right;">Rate</th>
                            <th style="padding:4px; text-align:right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- {{#sales}} -->
                        <tr style="border-bottom: 1px dashed #000;">
                            <td style="padding:4px; text-align:center;">{{sno}}</td>
                            <td style="padding:4px;">{{description}}</td>
                             <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                             <td style="padding:4px; text-align:center;">{{qty}}</td>
                            <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{va_content}}</td>
                            <td style="padding:4px; text-align:right;">{{mc}}</td>
                            <td style="padding:4px; text-align:right;">{{rate}}</td>
                            <td style="padding:4px; text-align:right;">{{amount}}</td>
                        </tr>
                        <!-- {{/sales}} -->
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                            <td colspan="3" style="padding:4px; text-align:center;">Total</td>
                            <td style="padding:4px; text-align:center;">{{total_qty}}</td>
                            <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                            <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                            <td colspan="3"></td>
                            <td style="padding:4px; text-align:right;">{{sub_total}}</td>
                        </tr>
                        </tfoot>
                </table>
            </div>
        `;

    bm.add('sales-table-v2', {
        label: 'Sales Table V2',
        category: 'Tables',
        content: itemsTableV2Content,
        attributes: { class: 'fa fa-table' }
    });

    bm.add('bill-footer-v2', {
        label: 'Bill Footer V2',
        category: 'Basic',
        content: `
             <div class="bill-footer-v2-block" data-gjs-type="bill-footer-v2" style="margin-top:10px; padding: 5px; display:flex; justify-content:space-between; align-items:flex-start; font-size:12px;">
                    <table style="width:100%; border-collapse:collapse; font-size:inherit;">
                        <tr>
                            <td style="padding:2px; text-align:right; width: 70%;" class="lbl-sub">Subtotal:</td>
                            <td style="padding:2px; text-align:center; width: 5%;">₹</td>
                            <td style="padding:2px; text-align:right; font-weight:bold; width: 25%;">{{sub_total}}</td>
                        </tr>
                        <tr>
                            <td style="padding:2px; text-align:right;" class="lbl-cgst">CGST:</td>
                            <td style="padding:2px; text-align:center;">₹</td>
                            <td style="padding:2px; text-align:right;">{{cgst_amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding:2px; text-align:right;" class="lbl-sgst">SGST:</td>
                            <td style="padding:2px; text-align:center;">₹</td>
                            <td style="padding:2px; text-align:right;">{{sgst_amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding:2px; text-align:right;" class="lbl-igst">IGST:</td>
                            <td style="padding:2px; text-align:center;">₹</td>
                            <td style="padding:2px; text-align:right;">{{igst_amount}}</td>
                        </tr>
                        <tr>
                            <td style="padding:2px; text-align:right;" class="lbl-round">Round Off:</td>
                            <td style="padding:2px; text-align:center;">₹</td>
                            <td style="padding:2px; text-align:right;">{{round_off}}</td>
                        </tr>
                        <tr style="border-top:1px solid #000; border-bottom: 2px solid #000;">
                            <td style="padding:4px; text-align:right; font-weight:bold;" class="lbl-grand">Grand Total:</td>
                            <td style="padding:4px; text-align:center; font-weight:bold;">₹</td>
                            <td style="padding:4px; text-align:right; font-weight:bold;">{{grand_total}}</td>
                        </tr>
                    </table>
            </div>
        `,
        attributes: { class: 'fa fa-calculator' }
    });







    // Return Table Block


    // Order Receipt Table Block (Items Ordered)
    const orderReceiptTableContent = `
        <style> .dynamic-order-receipt-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-order-receipt-table" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
            <table class="order-receipt-table" style="width:100%; border-collapse:collapse; font-size:inherit;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                       <th style="padding:4px;">S.No</th>
                        <th style="padding:4px; text-align:left;">Description</th>
                        <th style="padding:4px; text-align:center;">HSN</th>
                        <th style="padding:4px; text-align:center;">PCS</th>
                        <th style="padding:4px; text-align:right;">GRS.WT</th>
                        <th style="padding:4px; text-align:right;">NET.WT</th>
                        <th style="padding:4px; text-align:right;">Rate</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#order_items}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{sno}}</td>
                        <td style="padding:4px;">{{description}}</td>
                         <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                         <td style="padding:4px; text-align:center;">{{qty}}</td>
                        <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{rate}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/order_items}} -->
                </tbody>
                 <tfoot>
                     <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="3" style="padding:4px; text-align:center;">Total</td>
                        <td style="padding:4px; text-align:center;">{{total_qty}}</td>
                        <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                        <td colspan="2" style="padding:4px; text-align:right;">{{order_amount}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    bm.add('order-receipt-table', {
        label: 'Order Receipt Table',
        category: 'Tables',
        content: orderReceiptTableContent,
        attributes: { class: 'fa fa-list-alt' }
    });

    // Advance Payment Block (Advance History/Utilized)
    const advancePaymentBlockContent = `
        <style> .dynamic-advance-payment-block { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-advance-payment-block" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
            <table class="advance-details-table" style="width:100%; border-collapse:collapse; font-size:inherit;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">Date</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#advance_details}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{date}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/advance_details}} -->
                </tbody>
                <tfoot>
                     <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td style="padding:4px; text-align:right;">Total Adjusted</td>
                        <td style="padding:4px; text-align:right;">{{order_advance_adj}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    bm.add('advance-payment-block', {
        label: 'Advance History',
        category: 'Basic',
        content: advancePaymentBlockContent,
        attributes: { class: 'fa fa-history' }
    });

    // Order Delivery Table Block
    const orderDeliveryTableContent = `
        <style> .dynamic-order-delivery-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-order-delivery-table" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
            <table class="order-delivery-table" style="width:100%; border-collapse:collapse; font-size:inherit;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">S.No</th>
                        <th style="padding:4px; text-align:left;">Description</th>
                        <th style="padding:4px; text-align:center;">HSN</th>
                        <th style="padding:4px; text-align:center;">PCS</th>
                        <th style="padding:4px; text-align:right;">GRS.WT</th>
                        <th style="padding:4px; text-align:right;">NET.WT</th>
                        <th style="padding:4px; text-align:right;">V.A</th>
                        <th style="padding:4px; text-align:right;">MC</th>
                        <th style="padding:4px; text-align:right;">Rate</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#order_delivery_items}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{sno}}</td>
                        <td style="padding:4px;">{{description}}</td>
                         <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                         <td style="padding:4px; text-align:center;">{{qty}}</td>
                        <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{va_content}}</td>
                        <td style="padding:4px; text-align:right;">{{mc}}</td>
                        <td style="padding:4px; text-align:right;">{{rate}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/order_delivery_items}} -->
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                        <td colspan="3" style="padding:4px; text-align:center;">Total</td>
                        <td style="padding:4px; text-align:center;">{{total_qty}}</td>
                        <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                        <td colspan="3"></td>
                        <td style="padding:4px; text-align:right;">{{sub_total}}</td>
                    </tr>
                    </tfoot>
            </table>
        </div>
    `;
    bm.add('order-delivery-table', {
        label: 'Order Delivery Table',
        category: 'Tables',
        content: orderDeliveryTableContent,
        attributes: { class: 'fa fa-truck' }
    });

    // Order Advance Entries Block (Paid Advances)
    const orderAdvanceDetailsBlockContent = `
        <style> .dynamic-order-advance-details { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-order-advance-details" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
            <table class="order-advance-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">Date</th>
                        <th style="padding:4px; text-align:right;">Rate</th>
                        <th style="padding:4px; text-align:right;">Weight</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#order_advance_entries}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{date}}</td>
                        <td style="padding:4px; text-align:right;">{{rate}}</td>
                        <td style="padding:4px; text-align:right;">{{weight}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/order_advance_entries}} -->
                </tbody>
                <tfoot>
                     <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="3" style="padding:4px; text-align:right;">Total Paid</td>
                        <td style="padding:4px; text-align:right;">{{total_paid}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    bm.add('order-advance-details-block', {
        label: 'Order Advance Entries',
        category: 'Basic',
        content: orderAdvanceDetailsBlockContent,
        attributes: { class: 'fa fa-list' }
    });

    // Credit Collection Table
    const creditCollTableContent = `
        <style> .dynamic-credit-coll-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-credit-coll-table" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
            <table class="credit-collection-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                         <th style="padding:4px;">Date</th>
                        <th style="padding:4px; text-align:left;">Description</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#credit_collection_history}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{date}}</td>
                        <td style="padding:4px;">{{label}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/credit_collection_history}} -->
                </tbody>
                 <tfoot>
                     <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="2" style="padding:4px; text-align:right;">Balance Amount</td>
                        <td style="padding:4px; text-align:right;">{{credit_balance_amount}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    bm.add('credit-collection-table', {
        label: 'Credit Collection Table',
        category: 'Tables',
        content: creditCollTableContent,
        attributes: { class: 'fa fa-credit-card' }
    });

    // Credit Collection Details
    const creditCollDetailsContent = templates['credit-collection-details-block'] || `<div>Credit Collection Details Not Found</div>`;
    bm.add('credit-collection-details-block', {
        label: 'Credit Details History',
        category: 'Basic',
        content: creditCollDetailsContent,
        attributes: { class: 'fa fa-history' }
    });

    // Purchase Bill Table
    const purchaseBillTableContent = `
        <style> .dynamic-purchase-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-purchase-table" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
            <table class="purchase-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">S.No</th>
                        <th style="padding:4px; text-align:left;">Description</th>
                        <th style="padding:4px; text-align:right;">Gross Wt</th>
                        <th style="padding:4px; text-align:right;">Stn Less</th>
                        <th style="padding:4px; text-align:right;">Mtl Less</th>
                        <th style="padding:4px; text-align:right;">Net Wt</th>
                        <th style="padding:4px; text-align:right;">Rate</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#purchase_items}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{sno}}</td>
                        <td style="padding:4px;">{{description}}</td>
                        <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{stn_less}}</td>
                        <td style="padding:4px; text-align:right;">{{mtl_less}}</td>
                        <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{rate}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/purchase_items}} -->
                </tbody>
                <tfoot>
                     <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="7" style="padding:4px; text-align:right;">Total Purchase Amount</td>
                        <td style="padding:4px; text-align:right;">{{purchase_total_amount}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    bm.add('purchase-bill-table', {
        label: 'Purchase Bill Table',
        category: 'Tables',
        content: purchaseBillTableContent,
        attributes: { class: 'fa fa-shopping-bag' }
    });

    // Sales & Purchase Table
    const salesPurchaseTableContent = templates['sales-purchase-table'] || `<div>Sales & Purchase Table Not Found</div>`;
    bm.add('sales-purchase-table', {
        label: 'Sales & Purchase Table',
        category: 'Tables',
        content: salesPurchaseTableContent,
        attributes: { class: 'fa fa-exchange' }
    });

    // Repair Order Table
    const repairOrderTableContent = `
        <style> .dynamic-repair-order-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-repair-order-table" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
             <table class="repair-order-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">S.No</th>
                        <th style="padding:4px; text-align:left;">Description</th>
                        <th style="padding:4px; text-align:right;">Rep Wt</th>
                        <th style="padding:4px; text-align:right;">Comp Wt</th>
                        <th style="padding:4px; text-align:right;">Rate</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#repair_order_items}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{sno}}</td>
                        <td style="padding:4px;">{{description}}</td>
                        <td style="padding:4px; text-align:right;">{{repair_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{completed_wt}}</td>
                        <td style="padding:4px; text-align:right;">{{rate}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/repair_order_items}} -->
                </tbody>
                <tfoot>
                     <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="5" style="padding:4px; text-align:right;">Total Amount</td>
                        <td style="padding:4px; text-align:right;">{{repair_order_total}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    bm.add('repair-order-table', {
        label: 'Repair Order Table',
        category: 'Tables',
        content: repairOrderTableContent,
        attributes: { class: 'fa fa-wrench' }
    });

    // Supplier Sales Table
    const supplierSalesTableContent = `
        <style> .dynamic-supplier-sales-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-supplier-sales-table" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
             <div style="font-weight:bold; margin-bottom:5px;">Supplier Sales Items</div>
             ${itemsTableV2Content.replace('sales-table-v2', 'supplier-sales-table').replace('dynamic-sales-table-v2', 'dynamic-supplier-sales-table')}
        </div>
    `;
    bm.add('supplier-sales-table', {
        label: 'Supplier Sales Table',
        category: 'Tables',
        content: supplierSalesTableContent,
        attributes: { class: 'fa fa-truck' }
    });

    // Approval Stock Bill Table
    const approvalStockTableContent = `
         <style> .dynamic-approval-stock-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-approval-stock-table" data-gjs-type="table-wrapper" style="display:block; padding:5px;">
             <div style="font-weight:bold; margin-bottom:5px;">Approval Stock Items</div>
             ${itemsTableV2Content.replace('sales-table-v2', 'approval-stock-table').replace('dynamic-sales-table-v2', 'dynamic-approval-stock-table')}
        </div>
    `;
    bm.add('approval-stock-table', {
        label: 'Approval Stock Table',
        category: 'Tables',
        content: approvalStockTableContent,
        attributes: { class: 'fa fa-check-square-o' }
    });

    // Purchase Amount Block
    const purchaseAmountBlockContent = templates['purchase-amount-block'] || `<div>Purchase Amount Block Not Found</div>`;
    bm.add('purchase-amount-block', {
        label: 'Purchase Amount',
        category: 'Basic',
        content: purchaseAmountBlockContent,
        attributes: { class: 'fa fa-money' }
    });



    // Purchase Invoice Block (Summary)
    const purchaseInvoiceBlockContent = templates['purchase-invoice-block'] || `<div>Purchase Invoice Block Not Found</div>`;
    bm.add('purchase-invoice-block', {
        label: 'Purchase Invoice Block',
        category: 'Basic',
        content: purchaseInvoiceBlockContent,
        attributes: { class: 'fa fa-calculator' }
    });

    // Chit Pre Close Table
    const chitPreCloseTableContent = `
        <style> .dynamic-chit-pre-close-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-chit-pre-close-table" data-gjs-type="table-wrapper" style="display:block; padding:5px; border:1px dashed #535050ff;">
            <table class="chit-pre-close-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">S.No</th>
                        <th style="padding:4px; text-align:left;">Ref No</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#chit_pre_close_items}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{sno}}</td>
                        <td style="padding:4px;">{{ref_no}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/chit_pre_close_items}} -->
                </tbody>
                <tfoot>
                     <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="2" style="padding:4px; text-align:right;">Total Amount</td>
                        <td style="padding:4px; text-align:right;">{{chit_pre_close_total}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    bm.add('chit-pre-close-table', {
        label: 'Chit Pre Close Table',
        category: 'Tables',
        content: chitPreCloseTableContent,
        attributes: { class: 'fa fa-list-alt' }
    });

    // Chit Details Table (General)
    const chitDetailsTableContent = `
        <style> .dynamic-chit-details-table { height: auto !important; min-height: 50px; } </style>
        <div class="dynamic-chit-details-table" data-gjs-type="table-wrapper" style="display:block; padding:5px; border:1px dashed #535050ff;">
            <table class="chit-details-table" style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr style="border-bottom: 1px solid #000;">
                        <th style="padding:4px;">S.No</th>
                        <th style="padding:4px; text-align:left;">Ref No</th>
                        <th style="padding:4px; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- {{#chit_general_items}} -->
                    <tr style="border-bottom: 1px dashed #000;">
                        <td style="padding:4px; text-align:center;">{{sno}}</td>
                        <td style="padding:4px;">{{ref_no}}</td>
                        <td style="padding:4px; text-align:right;">{{amount}}</td>
                    </tr>
                    <!-- {{/chit_general_items}} -->
                </tbody>
                <tfoot>
                     <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td colspan="2" style="padding:4px; text-align:right;">Total Amount</td>
                        <td style="padding:4px; text-align:right;">{{chit_general_total}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    bm.add('chit-details-table', {
        label: 'Chit Details Table',
        category: 'Tables',
        content: chitDetailsTableContent,
        attributes: { class: 'fa fa-list' }
    });





    // ===== FOOTER BLOCKS =====

    // ===== FOOTER BLOCKS =====
    // ===== FOOTER BLOCKS =====


    bm.add('signatures-v2', {
        label: 'Signatures V2',
        category: 'Basic',
        content: `
            <div class="signatures-v2-block" data-gjs-type="resizable-text-block" style="margin-top:40px; display:flex; justify-content:space-between; font-size:12px; padding: 10px;">
                <div style="text-align:center; min-width:150px;">
                    <div style="border-top:1px solid #000; margin-bottom:5px;"></div>
                    <strong>Customer Signature</strong>
                </div>
                <div style="text-align:center; min-width:150px;">
                     <div style="margin-bottom:5px;">
                        {{billed_by}}
                     </div>
                    <div style="border-top:1px solid #000;"></div>
                    <strong>Operator Signature</strong>
                </div>
                <div style="text-align:center; min-width:150px;">
                    <div style="border-top:1px solid #000; margin-bottom:5px;"></div>
                    <strong>Cashier Signature</strong>
                </div>
            </div> 
        `,
        attributes: { class: 'fa fa-pencil' }
    });

    bm.add('duplicate-copy', {
        label: 'Duplicate Copy',
        category: 'Basic',
        content: `
            <div class="duplicate-copy-block" data-gjs-type="resizable-text-block" style="text-align:center; font-weight:bold; font-size:14px; margin-bottom:10px;">
                ( Duplicate Copy )
            </div>
        `,
        attributes: { class: 'fa fa-files-o' }
    });

    bm.add('qr-code', {
        label: 'QR Code',
        category: 'Basic',
        content: `
            <div style="text-align:center; padding:5px;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=Example" alt="QR Code" style="width:80px; height:80px;" />
            </div>
        `,
        attributes: { class: 'fa fa-qrcode' }
    });

    bm.add('metal-rates-box', {
        label: 'Metal Rates Box',
        category: 'Basic',
        content: `
            <div class="metal-rates-block" data-gjs-type="resizable-text-block" style="border:1px solid #ccc; padding:5px; font-size:12px; display:inline-block;">
                <strong>Rates:</strong> Gold: {{gold_rate}} | Silver: {{silver_rate}}
            </div>
        `,
        attributes: { class: 'fa fa-diamond' }
    });

    bm.add('payment-details', {
        label: 'Pay Breakdown',
        category: 'Tables',
        content: `
            <div class="dynamic-payment-details" data-gjs-type="table-wrapper" style="margin-top:10px; display:block; padding:5px; border:1px dashed #535050ff;">
                <div style="font-weight:bold; border-bottom:1px solid #000; margin-bottom:5px; font-size:13px;">Payment Details</div>
                 <table style="width:100%; border-collapse:collapse; font-size:inherit;">
                    <!-- {{#payment_breakdown}} -->
                    <tr>
                        <td style="width:30%;">{{mode}}</td>
                        <td style="width:20%; text-align:right;">{{amount}}</td>
                        <td style="width:50%; padding-left:10px; font-size:0.9em;">{{reference}}</td>
                    </tr>
                    <!-- {{/payment_breakdown}} -->
                 </table>
            </div>
        `,
        attributes: { class: 'fa fa-list-alt' }
    });

    // Amount Received Details Block (New)
    const amountReceivedDetailsContent = templates['amount-received-details'] || `
        <div class="amount-received-details-block" data-gjs-type="resizable-text-block" style="border:1px solid #000; padding:5px; margin-top:10px; display:flex;">
             <div style="flex:1;">Payment Details Full List</div>
             <div style="width:200px;">Payment Summary</div>
        </div>`;

    bm.add('amount-received-details', {
        label: 'Amt. Rec. Details',
        category: 'Basic',
        content: amountReceivedDetailsContent,
        attributes: { class: 'fa fa-money' }
    });

    bm.add('terms-condition-dynamic', {
        label: 'Dynamic Terms',
        category: 'Basic',
        content: `
             <div class="terms-conditions-block" data-gjs-type="resizable-text-block" style="margin-top:10px; font-size:11px;">
                <strong>Terms and Conditions:</strong><br>
                {{terms_condition}}
            </div>
        `,
        attributes: { class: 'fa fa-gavel' }
    });

    bm.add('amount-in-words', {
        label: 'Amt In Words',
        category: 'Basic',
        content: `
            <div class="amount-in-words-block" data-gjs-type="resizable-text-block" style="margin-top:10px; font-size:12px; font-weight:bold;">
                Amount in Words: <span style="font-style:italic;">{{amount_in_words}}</span>
            </div>
        `,
        attributes: { class: 'fa fa-font' }
    });

    bm.add('review-block', {
        label: 'Review Block',
        category: 'Basic',
        content: `
             <div class="review-block-instance" data-gjs-type="resizable-text-block" style="text-align:center; margin-top:10px; border:1px solid #ccc; padding:5px; display:inline-block;">
                <div style="font-weight:bold; margin-bottom:5px; font-size:12px;">Scan to Review</div>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=ReviewLink" alt="Review QR" style="width:60px; height:60px;" />
            </div>
        `,
        attributes: { class: 'fa fa-star' }
    });



    bm.add('signatures', {
        label: 'Signatures',
        category: 'Basic',
        content: `
            <div class="signatures-isolated-block" data-gjs-type="resizable-text-block" style="margin-top:40px; display:flex; justify-content:space-between; font-size:12px;">
                <div style="text-align:center; min-width:150px;">
                    <div style="border-top:1px solid #000; margin-bottom:5px;"></div>
                    <strong>Customer Signature</strong>
                </div>
                <div style="text-align:center; min-width:150px;">
                     <div style="margin-bottom:5px;">
                        {{billed_by}}
                     </div>
                    <div style="border-top:1px solid #000;"></div>
                    <strong>Operator Signature</strong>
                </div>
                <div style="text-align:center; min-width:150px;">
                    <div style="border-top:1px solid #000; margin-bottom:5px;"></div>
                    <strong>Cashier Signature</strong>
                </div>
            </div> 
        `,
        attributes: { class: 'fa fa-pencil' }
    });



    bm.add('logimax-footer', {
        label: 'Logimax Footer',
        category: 'Basic',
        content: `
            <div class="logimax-footer-block" data-gjs-type="resizable-text-block" style="width:100%; margin-top:20px;">
                <table style="width:100%; border-collapse:collapse; font-weight:bold;">
                    <tr>
                        <td style="font-size:16px; text-align:left; width:50%; padding:10px 0;">Customer Signature</td>
                        <td style="font-size:16px; text-align:right; width:50%; padding:10px 0;">For {{company_name}}</td>
                    </tr>
                </table>
                <div style="font-size:11px; width:100%; margin-top:5px; text-align:justify;">
                    *Amount of tax (Invoice Inwards) subject to reverse charge. *Received ornaments in good condition. *The Hallmarking charge is included in the invoice.*The consumer can get the purity of the hallmarked jewellery/artefacts checked from any of the BIS recognized A&H center. The list of BIS recognized A&H centers along with address and contact details are available in the website:www.bis.gov.in
                    E.&O.E
                </div>
            </div>
        `,
        attributes: { class: 'fa fa-file-text' }
    });
}
