/* Invoice Components Plugin for V2 Builder */

grapesjs.plugins.add('invoice-components-plugin', (editor, options) => {
    const bm = editor.BlockManager;
    const domc = editor.DomComponents;

    // ----------------------------------------------------------------
    // 1. SMART COMPONENTS
    // ----------------------------------------------------------------

    // Smart Invoice Table
    domc.addType('smart-invoice-table', {
      isComponent: el => el.tagName === 'TABLE' && el.classList && el.classList.contains('smart-invoice-table'),
      
      model: {
        defaults: {
          tagName: 'table',
          classes: ['smart-invoice-table'],
          style: { width: '100%', 'border-collapse': 'collapse', 'font-family': 'Roboto, sans-serif', 'font-size': '12px', 'margin-bottom': '5px' },
          traits: [
            { type: 'checkbox', name: 'show_hsn', label: 'Show HSN Code' },
            { type: 'checkbox', name: 'show_va', label: 'Show VA %' },
            { type: 'checkbox', name: 'show_discount', label: 'Show Discount' },
            { type: 'checkbox', name: 'show_tax', label: 'Show Tax Info' }
          ],
          draggable: true,
          droppable: false, // Prevent dropping elements INSIDE the table
          // The default structure of the table
          components: `
            <thead style="background:#f4f4f4; border-bottom:2px solid #333; border-top:1px solid #333;">
              <tr>
                <th style="padding:8px; text-align:left;">S.No</th>
                <th style="padding:8px; text-align:left;">Item Name</th>
                <th data-column="hsn" style="padding:8px; text-align:center; display:none;">HSN</th>
                <th style="padding:8px; text-align:center;">Qty</th>
                <th style="padding:8px; text-align:right;">Rate</th>
                <th data-column="va" style="padding:8px; text-align:right; display:none;">VA %</th>
                <th data-column="discount" style="padding:8px; text-align:right; display:none;">Disc</th>
                <th data-column="tax" style="padding:8px; text-align:right; display:none;">Tax</th>
                <th style="padding:8px; text-align:right;">Total</th>
              </tr>
            </thead>
            <tbody>
              {{#items}}
              <tr style="border-bottom:1px solid #eee;">
                <td style="padding:8px; text-align:left;">{{sno}}</td>
                <td style="padding:8px; text-align:left;">{{item_name}}</td>
                <td data-column="hsn" style="padding:8px; text-align:center; display:none;">{{item_hsn}}</td>
                <td style="padding:8px; text-align:center;">{{item_qty}}</td>
                <td style="padding:8px; text-align:right;">{{item_rate}}</td>
                <td data-column="va" style="padding:8px; text-align:right; display:none;">{{item_va}}</td>
                <td data-column="discount" style="padding:8px; text-align:right; display:none;">{{item_disc}}</td>
                <td data-column="tax" style="padding:8px; text-align:right; display:none;">{{item_tax}}</td>
                <td style="padding:8px; text-align:right;">{{item_total}}</td>
              </tr>
              {{/items}}
            </tbody>
          `,
        },
        init() {
            this.on('change:traits', this.handleTraitChange);
        },
        handleTraitChange() {
            // When traits are modified, toggle column visibility.
            const showHsn = this.getTrait('show_hsn').getValue();
            const showVa = this.getTrait('show_va').getValue();
            const showDiscount = this.getTrait('show_discount').getValue();
            const showTax = this.getTrait('show_tax').getValue();
             
            const toggleCols = (name, show) => {
                 const gjsCols = this.find(`[data-column="${name}"]`);
                 gjsCols.forEach(c => {
                     c.addStyle({ display: show ? 'table-cell' : 'none' });
                 });
            };
             
            toggleCols('hsn', showHsn);
            toggleCols('va', showVa);
            toggleCols('discount', showDiscount);
            toggleCols('tax', showTax);
        }
      }
    });

    bm.add('smart-invoice-table', {
        label: 'Smart Item Table',
        category: 'Advanced Layouts',
        content: { type: 'smart-invoice-table' },
        attributes: { class: 'fa fa-table' }
    });

    bm.add('page-break', {
        label: 'Page Break',
        category: 'Advanced Layouts',
        content: '<div class="page-break-indicator" style="page-break-after: always; break-after: page; height: 10px;"></div>',
        attributes: { class: 'fa fa-scissors' }
    });

    // ----------------------------------------------------------------
    // 2. BASIC BLOCKS & PRESETS
    // ----------------------------------------------------------------

    bm.add('text', {
        label: 'Text Box',
        category: 'Basic Elements',
        content: { type: 'text', content: 'Insert your text here', resizable: true, style: { padding: '10px', 'font-family': 'Roboto, sans-serif', 'min-height': '30px', 'margin-bottom': '5px' } },
        attributes: { class: 'fa fa-font' }
    });

    bm.add('image', {
        label: 'Image',
        category: 'Basic Elements',
        content: { type: 'image', resizable: true, style: { color: 'black', width: '200px', 'max-width': '100%', 'margin-bottom': '5px' } },
        attributes: { class: 'fa fa-picture-o' }
    });

    bm.add('columns-1', {
        label: '1 Column',
        category: 'Basic Elements',
        content: `
            <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable="true" style="display: flex; width: 100%; gap: 15px; margin-bottom: 5px;">
                <div data-gjs-type="default" data-gjs-droppable="true" style="width: 100%; min-height: 40px;"></div>
            </div>
        `,
        attributes: { class: 'fa fa-square-o' }
    });

    bm.add('columns-2', {
        label: '2 Columns',
        category: 'Basic Elements',
        content: `
            <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable="true" style="display: flex; width: 100%; gap: 15px; margin-bottom: 5px;">
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":0,"cr":1,"bl":0,"bc":0,"br":0}' style="width: 50%; min-height: 40px;"></div>
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":1,"cr":0,"bl":0,"bc":0,"br":0}' style="width: 50%; min-height: 40px;"></div>
            </div>
        `,
        attributes: { class: 'fa fa-columns' }
    });

    bm.add('columns-3', {
        label: '3 Columns',
        category: 'Basic Elements',
        content: `
            <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable="true" style="display: flex; width: 100%; gap: 15px; margin-bottom: 5px;">
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":0,"cr":1,"bl":0,"bc":0,"br":0}' style="width: 33.33%; min-height: 40px;"></div>
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":1,"cr":1,"bl":0,"bc":0,"br":0}' style="width: 33.33%; min-height: 40px;"></div>
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":1,"cr":0,"bl":0,"bc":0,"br":0}' style="width: 33.33%; min-height: 40px;"></div>
            </div>
        `,
        attributes: { class: 'fa fa-th-large' }
    });

    bm.add('columns-4', {
        label: '4 Columns',
        category: 'Basic Elements',
        content: `
            <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable="true" style="display: flex; width: 100%; gap: 15px; margin-bottom: 5px;">
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":0,"cr":1,"bl":0,"bc":0,"br":0}' style="width: 25%; min-height: 40px;"></div>
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":1,"cr":1,"bl":0,"bc":0,"br":0}' style="width: 25%; min-height: 40px;"></div>
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":1,"cr":1,"bl":0,"bc":0,"br":0}' style="width: 25%; min-height: 40px;"></div>
                <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable='{"tl":0,"tc":0,"tr":0,"cl":1,"cr":0,"bl":0,"bc":0,"br":0}' style="width: 25%; min-height: 40px;"></div>
            </div>
        `,
        attributes: { class: 'fa fa-th' }
    });
    
    // Explicit Wrapper/Container Block
    bm.add('container-box', {
        label: 'Container Box',
        category: 'Basic Elements',
        content: `
            <div data-gjs-type="default" data-gjs-droppable="true" data-gjs-resizable="true" style="width: 100%; padding: 10px; min-height: 40px; margin-bottom: 5px; border: 1px solid transparent;"></div>
        `,
        attributes: { class: 'fa fa-square-o' }
    });

    // Divider Line Component
    domc.addType('divider-line', {
      model: {
        defaults: {
          tagName: 'div',
          draggable: true,
          droppable: false,
          style: { 'border-top': '1px solid #000', 'margin-top': '10px', 'margin-bottom': '10px', 'width': '100%', 'height': '1px' },
          traits: [
            {
               type: 'select',
               name: 'border-style',
               label: 'Line Style',
               options: [
                 { value: 'solid', name: 'Solid' },
                 { value: 'dashed', name: 'Dashed' },
                 { value: 'dotted', name: 'Dotted' },
                 { value: 'dash-dot', name: 'Dash-Dot' },
               ],
               changeProp: 1
            },
            {
               type: 'color',
               name: 'border-color',
               label: 'Color',
               changeProp: 1
            },
            {
               type: 'number',
               name: 'border-width',
               label: 'Thickness (px)',
               changeProp: 1
            }
          ]
        },
        init() {
            this.on('change:border-style change:border-color change:border-width', this.handleStyleChange);
        },
        handleStyleChange() {
            const style = this.get('border-style') || 'solid';
            const color = this.get('border-color') || '#000000';
            const width = (this.get('border-width') || 1) + 'px';
            
            if (style === 'dash-dot') {
                // Approximate dash-dot using repeating-linear-gradient
                this.addStyle({ 
                    'border-top': 'none',
                    'height': width,
                    'background-image': `linear-gradient(to right, ${color} 0%, ${color} 70%, transparent 70%, transparent 80%, ${color} 80%, ${color} 90%, transparent 90%, transparent 100%)`,
                    'background-size': '25px 100%',
                    'background-repeat': 'repeat-x'
                });
            } else {
                this.addStyle({ 
                    'border-top': `${width} ${style} ${color}`, 
                    'height': '1px',
                    'background-image': 'none'
                });
            }
        }
      }
    });

    bm.add('line-solid', {
        label: 'Solid Line',
        category: 'Basic Elements',
        content: { type: 'divider-line', style: { 'border-top': '1px solid #000', 'margin': '10px 0' } },
        attributes: { class: 'fa fa-minus' }
    });

    bm.add('line-dashed', {
        label: 'Dashed Line',
        category: 'Basic Elements',
        content: { type: 'divider-line', style: { 'border-top': '1px dashed #000', 'margin': '10px 0' } },
        attributes: { class: 'fa fa-ellipsis-h' }
    });

    bm.add('line-dotted', {
        label: 'Dotted Line',
        category: 'Basic Elements',
        content: { type: 'divider-line', 'border-style': 'dotted', style: { 'border-top': '1px dotted #000', 'margin': '10px 0' } },
        attributes: { class: 'fa fa-dot-circle-o' }
    });

    bm.add('line-dashdot', {
        label: 'Dash-Dot Line',
        category: 'Basic Elements',
        content: { type: 'divider-line', 'border-style': 'dash-dot', style: { 'margin': '10px 0' } },
        attributes: { class: 'fa fa-minus-square-o' }
    });
    
    bm.add('layout-spacer', {
        label: 'Spacer (20px)',
        category: 'Basic Elements',
        content: '<div style="height:20px;"></div>',
        attributes: { class: 'fa fa-arrows-v' }
    });

    bm.add('preset-kv', {
        label: 'Key-Value Pair',
        category: 'Pre-sets',
        content: `
            <div style="display:flex; margin-bottom:5px; font-family: Roboto, sans-serif; font-size: 14px;">
                <div style="font-weight:bold; width:120px;">Label:</div>
                <div>Value</div>
            </div>
        `,
        attributes: { class: 'fa fa-list-alt' }
    });

    bm.add('preset-signature', {
        label: 'Signature Row',
        category: 'Pre-sets',
        content: `
            <div style="display:flex; justify-content:space-between; margin-top:40px; text-align:center; font-family: Roboto, sans-serif;">
                <div style="width:30%; border-top:1px solid #000; padding-top:5px;">Customer Sign</div>
                <div style="width:30%; border-top:1px solid #000; padding-top:5px;">Authorized Sign</div>
            </div>
        `,
        attributes: { class: 'fa fa-pencil-square-o' }
    });

    // Header preset
    bm.add('preset-header', {
        label: 'Store Header',
        category: 'Pre-sets',
        content: `
            <div style="text-align:center; font-family: Roboto, sans-serif; margin-bottom: 20px;">
                <h2 style="margin:0; font-size:24px; font-weight:bold;">STORE NAME</h2>
                <div style="font-size:12px; color:#555; margin-top:5px;">123 Business Road, City District, State 12345</div>
                <div style="font-size:12px; color:#555;">GSTIN: 29ABCDE1234F1Z5 | Ph: 9876543210</div>
            </div>
        `,
        attributes: { class: 'fa fa-id-card-o' }
    });

});
