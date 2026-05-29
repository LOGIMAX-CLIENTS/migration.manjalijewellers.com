/**
 * Print Template Designer V2 — WYSIWYG Engine
 * USER NEVER SEES CODE — everything is visual:
 *   - Dropdowns to pick fields
 *   - Checkboxes to toggle columns
 *   - Visual table with sample data
 *   - Formula builder with field dropdowns + operators
 */
(function() {
'use strict';

var CFG = window.D2_CONFIG;
var DEFS = window.BLOCK_DEFS_V2;
var FIELD_GROUPS = window.FIELD_GROUPS || {};
var COND_LABELS = window.CONDITION_LABELS || {};

function DesignerV2() {
    this.blocks = [];
    this.selectedBlockId = null;
    this.history = [];
    this.historyIdx = -1;
    this.maxHistory = 50;
    this.dirty = false;
    this.blockCounter = 0;
    this.init();
}

DesignerV2.prototype.init = function() {
    var self = this;
    this._initPaperSettings();
    this._initPalette();
    this._initSortable();
    this._initTabs();
    this._initToolbar();
    this._initKeyboard();
    this._loadDesign();
    this._loadMCVA();
};

// ═══════════════════════════════════════════════
// PAPER SETTINGS
// ═══════════════════════════════════════════════
DesignerV2.prototype._initPaperSettings = function() {
    var self = this;
    var el = function(id) { return document.getElementById(id); };
    el('d2PaperSize').value = CFG.paperSize;
    el('d2PaperOrient').value = CFG.pageOrientation;
    el('d2MT').value = CFG.marginTop;
    el('d2MR').value = CFG.marginRight;
    el('d2MB').value = CFG.marginBottom;
    el('d2ML').value = CFG.marginLeft;
    ['d2PaperSize','d2PaperOrient','d2MT','d2MR','d2MB','d2ML'].forEach(function(id) {
        el(id).addEventListener('change', function() { self._markDirty(); });
    });
};

DesignerV2.prototype._getPaperSettings = function() {
    return {
        paper_size: document.getElementById('d2PaperSize').value,
        page_orientation: document.getElementById('d2PaperOrient').value,
        margin_top: parseInt(document.getElementById('d2MT').value) || 10,
        margin_right: parseInt(document.getElementById('d2MR').value) || 10,
        margin_bottom: parseInt(document.getElementById('d2MB').value) || 10,
        margin_left: parseInt(document.getElementById('d2ML').value) || 10
    };
};

// ═══════════════════════════════════════════════
// LOAD DESIGN
// ═══════════════════════════════════════════════
DesignerV2.prototype._loadDesign = function() {
    var self = this;
    fetch(CFG.loadDesignUrl).then(function(r) { return r.json(); }).then(function(data) {
        if (data && data.blocks) {
            self.blocks = data.blocks.map(function(b) { b.id = b.id || self._genId(); return b; });
        }
        self._render();
        self._pushHistory();
    }).catch(function(e) {
        console.warn('No existing design, starting blank', e);
        self._render();
    });
};

// ═══════════════════════════════════════════════
// LOAD MC/VA
// ═══════════════════════════════════════════════
DesignerV2.prototype._loadMCVA = function() {
    fetch(CFG.mcvaUrl).then(function(r) { return r.json(); }).then(function(config) {
        if (config) {
            document.getElementById('d2MCMode').value = config.mc_mode || 'per_gram';
            document.getElementById('d2MCLabel').value = config.mc_label || 'MC';
            document.getElementById('d2VAMode').value = config.va_mode || 'percentage';
            document.getElementById('d2VALabel').value = config.va_label || 'VA %';
        }
    }).catch(function(){});
};

// ═══════════════════════════════════════════════
// PALETTE (Left Panel)
// ═══════════════════════════════════════════════
DesignerV2.prototype._initPalette = function() {
    var self = this;
    var list = document.getElementById('d2PaletteList');
    var categories = {};

    Object.keys(DEFS).forEach(function(type) {
        if (type.charAt(0) === '_') return; // skip _samples, _getSample
        var def = DEFS[type];
        var cat = def.category || 'Other';
        if (!categories[cat]) categories[cat] = [];
        categories[cat].push({type: type, label: def.label, icon: def.icon, desc: def.desc});
    });

    var html = '';
    Object.keys(categories).forEach(function(cat) {
        html += '<div class="d2-palette-cat">' + cat + '</div>';
        categories[cat].forEach(function(item) {
            html += '<div class="d2-palette-item" data-block-type="' + item.type + '">'
                + '<span class="d2-pi-icon">' + item.icon + '</span>'
                + '<div><div class="d2-pi-label">' + item.label + '</div>'
                + '<div class="d2-pi-desc">' + (item.desc||'') + '</div></div></div>';
        });
    });
    list.innerHTML = html;

    list.addEventListener('click', function(e) {
        var item = e.target.closest('.d2-palette-item');
        if (item) self.addBlock(item.dataset.blockType);
    });

    document.getElementById('d2PaletteSearch').addEventListener('input', function(e) {
        var q = e.target.value.toLowerCase();
        list.querySelectorAll('.d2-palette-item').forEach(function(el) {
            var label = el.querySelector('.d2-pi-label').textContent.toLowerCase();
            var desc = el.querySelector('.d2-pi-desc').textContent.toLowerCase();
            el.style.display = (label.indexOf(q) >= 0 || desc.indexOf(q) >= 0) ? '' : 'none';
        });
    });
};

// ═══════════════════════════════════════════════
// SORTABLE (Drag-Drop)
// ═══════════════════════════════════════════════
DesignerV2.prototype._initSortable = function() {
    var self = this;
    this.sortable = new Sortable(document.getElementById('d2Canvas'), {
        animation: 200,
        handle: '.d2-drag-handle',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        filter: '.d2-canvas-empty',
        onEnd: function() {
            var ids = Array.from(document.getElementById('d2Canvas').querySelectorAll('.d2-block-card'))
                .map(function(el) { return el.dataset.blockId; });
            self.blocks = ids.map(function(id, i) {
                var b = self.blocks.find(function(x) { return x.id === id; });
                if (b) b.order = i;
                return b;
            }).filter(Boolean);
            self._pushHistory();
            self._markDirty();
        }
    });
};

// ═══════════════════════════════════════════════
// TABS
// ═══════════════════════════════════════════════
DesignerV2.prototype._initTabs = function() {
    var self = this;
    document.querySelectorAll('.d2-canvas-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            var name = tab.dataset.tab;
            document.querySelectorAll('.d2-canvas-tab').forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');

            document.getElementById('d2CanvasArea').style.display = (name === 'design') ? 'block' : 'none';
            document.getElementById('d2PreviewArea').style.display = (name === 'preview') ? 'flex' : 'none';

            if (name === 'preview') self._refreshPreview('sample');
        });
    });

    document.querySelectorAll('[data-preview-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-preview-mode]').forEach(function(b) { b.classList.remove('d2-btn-primary'); });
            btn.classList.add('d2-btn-primary');
            self._refreshPreview(btn.dataset.previewMode);
        });
    });

    document.getElementById('d2BtnPrint').addEventListener('click', function() {
        var frame = document.getElementById('d2PreviewFrame');
        if (frame.contentWindow) frame.contentWindow.print();
    });
};

// ═══════════════════════════════════════════════
// TOOLBAR
// ═══════════════════════════════════════════════
DesignerV2.prototype._initToolbar = function() {
    var self = this;
    document.getElementById('d2BtnSave').addEventListener('click', function() { self.save(); });
    document.getElementById('d2BtnPreview').addEventListener('click', function() {
        document.querySelector('.d2-canvas-tab[data-tab="preview"]').click();
    });
    document.getElementById('d2BtnUndo').addEventListener('click', function() { self.undo(); });
    document.getElementById('d2BtnRedo').addEventListener('click', function() { self.redo(); });
    document.getElementById('d2BtnDefaults').addEventListener('click', function() { self.loadDefaultTemplate(); });
    document.getElementById('d2BtnSaveMCVA').addEventListener('click', function() { self._saveMCVA(); });
};

// ═══════════════════════════════════════════════
// KEYBOARD
// ═══════════════════════════════════════════════
DesignerV2.prototype._initKeyboard = function() {
    var self = this;
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') { e.preventDefault(); self.save(); }
        if (e.ctrlKey && e.key === 'z') { e.preventDefault(); self.undo(); }
        if (e.ctrlKey && e.key === 'y') { e.preventDefault(); self.redo(); }
        if (e.key === 'Delete' && self.selectedBlockId) {
            var active = document.activeElement;
            if (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.tagName === 'SELECT')) return;
            self.removeBlock(self.selectedBlockId);
        }
    });
};

// ═══════════════════════════════════════════════
// BLOCK CRUD
// ═══════════════════════════════════════════════
DesignerV2.prototype.addBlock = function(type) {
    var def = DEFS[type];
    if (!def) return;
    var block = {
        id: this._genId(),
        type: type,
        enabled: true,
        order: this.blocks.length,
        settings: JSON.parse(JSON.stringify(def.defaults || {}))
    };
    this.blocks.push(block);
    this._render();
    this._pushHistory();
    this._markDirty();
    this.selectBlock(block.id);
    this._toast('Added: ' + def.label, 'success');
};

DesignerV2.prototype.removeBlock = function(id) {
    this.blocks = this.blocks.filter(function(b) { return b.id !== id; });
    this.blocks.forEach(function(b, i) { b.order = i; });
    if (this.selectedBlockId === id) {
        this.selectedBlockId = null;
        this._renderSettings();
    }
    this._render();
    this._pushHistory();
    this._markDirty();
};

DesignerV2.prototype.duplicateBlock = function(id) {
    var src = this.blocks.find(function(b) { return b.id === id; });
    if (!src) return;
    var clone = JSON.parse(JSON.stringify(src));
    clone.id = this._genId();
    var idx = this.blocks.indexOf(src) + 1;
    this.blocks.splice(idx, 0, clone);
    this.blocks.forEach(function(b, i) { b.order = i; });
    this._render();
    this._pushHistory();
    this._markDirty();
    this.selectBlock(clone.id);
};

DesignerV2.prototype.toggleBlock = function(id) {
    var block = this.blocks.find(function(b) { return b.id === id; });
    if (block) {
        block.enabled = !block.enabled;
        this._render();
        this._pushHistory();
        this._markDirty();
    }
};

DesignerV2.prototype.selectBlock = function(id) {
    this.selectedBlockId = id;
    document.querySelectorAll('.d2-block-card').forEach(function(el) {
        el.classList.toggle('selected', el.dataset.blockId === id);
    });
    this._renderSettings();
};

// ═══════════════════════════════════════════════
// RENDER CANVAS (Visual preview, NO code visible)
// ═══════════════════════════════════════════════
DesignerV2.prototype._render = function() {
    var self = this;
    var canvas = document.getElementById('d2Canvas');
    var empty = document.getElementById('d2CanvasEmpty');

    if (this.blocks.length === 0) {
        empty.style.display = '';
        canvas.querySelectorAll('.d2-block-card').forEach(function(el) { el.remove(); });
        return;
    }
    empty.style.display = 'none';

    var sorted = this.blocks.slice().sort(function(a,b) { return a.order - b.order; });
    var html = '';

    sorted.forEach(function(block) {
        var def = DEFS[block.type] || {label: block.type, icon:'❓', preview:function(){return '<div class="bvp-generic">Unknown Block</div>';}};
        var enabled = block.enabled !== false;
        var condKey = block.settings ? block.settings.condition_key : null;
        var condLabel = '';
        if (condKey && condKey !== 'none' && COND_LABELS[condKey]) {
            condLabel = COND_LABELS[condKey];
        }
        var previewHtml = def.preview(block.settings || {});

        html += '<div class="d2-block-card' + (!enabled ? ' disabled' : '') + (block.id === self.selectedBlockId ? ' selected' : '') + '" data-block-id="' + block.id + '">'
            + '<div class="d2-block-card-hdr">'
            + '<span class="d2-drag-handle" title="Drag to reorder">⠿</span>'
            + '<span class="d2-block-icon">' + def.icon + '</span>'
            + '<span class="d2-block-title">' + def.label + '</span>';
        if (condLabel) {
            html += '<span class="d2-cond-badge" title="This section is conditional">📋 ' + condLabel + '</span>';
        }
        html += '<div class="d2-block-actions">'
            + '<button title="' + (enabled ? 'Hide this section' : 'Show this section') + '" data-action="toggle">' + (enabled ? '👁' : '🚫') + '</button>'
            + '<button title="Make a copy" data-action="duplicate">📋</button>'
            + '<button title="Remove" data-action="delete" class="delete">🗑</button>'
            + '</div></div>'
            + '<div class="d2-block-preview">' + previewHtml + '</div>'
            + '</div>';
    });

    canvas.innerHTML = html;
    canvas.appendChild(empty);
    empty.style.display = 'none';

    canvas.querySelectorAll('.d2-block-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-action]');
            if (btn) {
                var action = btn.dataset.action;
                var id = card.dataset.blockId;
                if (action === 'delete') self.removeBlock(id);
                else if (action === 'duplicate') self.duplicateBlock(id);
                else if (action === 'toggle') self.toggleBlock(id);
                return;
            }
            self.selectBlock(card.dataset.blockId);
        });
    });
};

// ═══════════════════════════════════════════════
// SETTINGS PANEL — ALL VISUAL, NO CODE
// ═══════════════════════════════════════════════
DesignerV2.prototype._renderSettings = function() {
    var container = document.getElementById('d2BlockSettings');
    var titleEl = document.getElementById('d2SettingsTitle');

    if (!this.selectedBlockId) {
        titleEl.textContent = 'Template Settings';
        container.innerHTML = '<div class="d2-settings-empty">👆 Click any block above to edit it</div>';
        return;
    }

    var block = this.blocks.find(function(b) { return b.id === this.selectedBlockId; }.bind(this));
    if (!block) return;

    var def = DEFS[block.type] || {};
    titleEl.textContent = (def.label || block.type);
    var s = block.settings || {};
    var html = '';

    // ── General Settings (all blocks have these) ──
    html += '<div class="d2-settings-section"><div class="d2-settings-section-title">General</div>';
    html += this._ui_toggle('Enabled', block.enabled !== false, 'enabled');
    html += this._ui_select('Font Size', s.font_size || '12px', 'font_size',
        [{v:'10px',l:'Small (10)'},{v:'11px',l:'11'},{v:'12px',l:'Normal (12)'},{v:'13px',l:'13'},{v:'14px',l:'Medium (14)'},{v:'16px',l:'Large (16)'},{v:'18px',l:'XL (18)'},{v:'20px',l:'XXL (20)'}]);

    // Condition — shown as human-readable dropdown
    var condOpts = Object.keys(COND_LABELS).map(function(k) { return {v:k, l:COND_LABELS[k]}; });
    html += this._ui_select('Show this block', s.condition_key || 'none', 'condition_key', condOpts);
    html += '</div>';

    // ── Type-Specific Settings ──
    html += this._renderTypeSettings(block);

    container.innerHTML = html;
    this._bindSettings(container, block);
};

DesignerV2.prototype._renderTypeSettings = function(block) {
    var s = block.settings || {};
    var html = '';

    switch(block.type) {
        case 'company-header':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Header Options</div>';
            html += this._ui_toggle('Show Logo', s.show_logo !== false, 'show_logo');
            html += this._ui_toggle('Bottom Border', s.border_bottom !== false, 'border_bottom');
            html += this._ui_toggle('Repeat on Every Page', !!s.repeat_every_page, 'repeat_every_page');
            html += this._ui_select('Alignment', s.text_align || 'center', 'text_align',
                [{v:'left',l:'Left'},{v:'center',l:'Center'},{v:'right',l:'Right'}]);
            html += '</div>';
            html += this._renderFieldList(block, 'company', 'Company Fields', 'Company');
            break;

        case 'customer-info':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Layout</div>';
            html += this._ui_select('Layout Style', s.layout || 'two-column', 'layout',
                [{v:'two-column',l:'Side by Side (2 columns)'},{v:'single-column',l:'Stacked (1 column)'}]);
            html += this._ui_toggle('Show Borders', s.show_borders !== false, 'show_borders');
            html += '</div>';
            html += this._renderFieldList(block, 'customer', 'Customer Fields', 'Customer');
            html += this._renderFieldList(block, 'invoice', 'Invoice Fields', 'Bill Details');
            break;

        case 'custom-text':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Text Content</div>';
            html += '<div class="d2-field-row"><span class="d2-field-label">Text</span>'
                + '<input type="text" class="d2-field-input" data-setting="content" value="' + this._esc(s.content || '') + '" placeholder="Enter text..."></div>';
            html += this._ui_select('Alignment', s.text_align || 'center', 'text_align',
                [{v:'left',l:'Left'},{v:'center',l:'Center'},{v:'right',l:'Right'}]);
            html += this._ui_toggle('Bold', !!s.bold, 'bold');
            html += '<div style="padding:6px 0;"><em style="font-size:11px;color:var(--d2-text-muted);">💡 Tip: You can use a field value here by choosing one from the dropdown below:</em></div>';
            html += this._ui_fieldPicker('Insert Field Value', 'content_field_insert');
            html += '</div>';
            break;

        case 'items-table':
        case 'purchase-table':
        case 'sales-return-table':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">📊 Choose Columns to Show</div>';
            html += '<p style="font-size:11px;color:var(--d2-text-muted);margin-bottom:8px;">Check/uncheck to show or hide columns. Edit column names by clicking them.</p>';
            var cols = s.columns || (DEFS[block.type] ? DEFS[block.type].defaults.columns : []) || [];
            html += '<div class="d2-col-picker">';
            cols.forEach(function(col, idx) {
                var sampleVal = DEFS._sampleItems ? (DEFS._sampleItems[0] ? (DEFS._sampleItems[0][col.field]||'—') : '—') : '—';
                html += '<div class="d2-col-item" data-col-idx="'+idx+'">'
                    + '<input type="checkbox" data-col-vis="'+idx+'" ' + (col.visible ? 'checked' : '') + ' title="Show/Hide this column">'
                    + '<input type="text" class="d2-fe-label-input" data-col-label="'+idx+'" value="'+_esc(col.label)+'" title="Edit column header name" style="width:80px;">'
                    + '<span style="font-size:10px;color:var(--d2-text-muted);flex:1;">e.g. '+sampleVal+'</span>'
                    + '<select class="d2-fe-suffix" data-col-align="'+idx+'" title="Column alignment" style="width:60px;">'
                    + '<option value="left" '+(col.align==='left'?'selected':'')+'>Left</option>'
                    + '<option value="center" '+(col.align==='center'?'selected':'')+'>Center</option>'
                    + '<option value="right" '+(col.align==='right'?'selected':'')+'>Right</option>'
                    + '</select></div>';
            });
            html += '</div>';

            // Formula column builder
            html += '<div style="margin-top:10px;padding-top:10px;border-top:1px solid rgba(255,255,255,0.06);">';
            html += '<div class="d2-settings-section-title">🧮 Add Calculated Column</div>';
            html += '<p style="font-size:11px;color:var(--d2-text-muted);margin-bottom:6px;">Create an auto-calculated column (e.g. Net Wt × Rate)</p>';
            html += '<div class="d2-field-row"><span class="d2-field-label">Column Name</span>'
                + '<input type="text" class="d2-field-input" id="d2FormulaName" placeholder="e.g. Total MC"></div>';
            html += '<div class="d2-field-row"><span class="d2-field-label">Formula</span>'
                + '<div style="flex:1;display:flex;gap:4px;">'
                + '<select class="d2-field-input" id="d2FormulaField1" style="flex:1;">' + this._formulaFieldOptions() + '</select>'
                + '<select class="d2-field-input" id="d2FormulaOp" style="width:50px;">'
                + '<option value="*">×</option><option value="+">+</option><option value="-">−</option><option value="/">&divide;</option>'
                + '</select>'
                + '<select class="d2-field-input" id="d2FormulaField2" style="flex:1;">' + this._formulaFieldOptions() + '</select>'
                + '</div></div>';
            html += '<button class="d2-btn d2-btn-sm d2-btn-primary" id="d2BtnAddFormula" style="width:100%;margin-top:4px;">➕ Add Calculated Column</button>';
            html += '</div>';

            if (block.type === 'items-table') {
                html += '<div style="margin-top:8px;">';
                html += this._ui_toggle('Show Total Row', s.show_footer_totals !== false, 'show_footer_totals');
                html += '</div>';
            }
            html += '</div>';
            break;

        case 'tax-summary':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Which Tax Lines to Show</div>';
            html += this._ui_toggle('Sub Total', s.show_subtotal !== false, 'show_subtotal');
            html += this._ui_toggle('CGST', s.show_cgst !== false, 'show_cgst');
            html += this._ui_toggle('SGST', s.show_sgst !== false, 'show_sgst');
            html += this._ui_toggle('IGST (Interstate)', !!s.show_igst, 'show_igst');
            html += this._ui_toggle('Round Off', s.show_round_off !== false, 'show_round_off');
            html += this._ui_toggle('Amount in Words', s.show_amount_words !== false, 'show_amount_words');
            html += '</div>';
            break;

        case 'payment-info':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Which Payment Details to Show</div>';
            html += this._ui_toggle('Cash', s.show_cash !== false, 'show_cash');
            html += this._ui_toggle('Card', s.show_card !== false, 'show_card');
            html += this._ui_toggle('UPI', s.show_upi !== false, 'show_upi');
            html += this._ui_toggle('Cheque', s.show_cheque !== false, 'show_cheque');
            html += this._ui_toggle('Balance Due', s.show_balance !== false, 'show_balance');
            html += '</div>';
            break;

        case 'advance-payment':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Adjustment Details</div>';
            html += this._ui_toggle('Chit Adjustment', s.show_chit !== false, 'show_chit');
            html += this._ui_toggle('Advance', s.show_advance !== false, 'show_advance');
            html += this._ui_toggle('Order Advance', s.show_order_advance !== false, 'show_order_advance');
            html += '</div>';
            break;

        case 'signature-block':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Signature Lines</div>';
            html += this._ui_toggle('Customer Signature', s.show_customer_sign !== false, 'show_customer_sign');
            html += this._ui_toggle('Cashier', !!s.show_cashier, 'show_cashier');
            html += this._ui_toggle('Authorized Signatory', s.show_auth_sign !== false, 'show_auth_sign');
            html += '<div class="d2-field-row"><span class="d2-field-label">Space Above</span>'
                + '<select class="d2-field-input" data-setting="spacing_top">'
                + '<option value="20px"' + (s.spacing_top==='20px'?' selected':'') + '>Small (20px)</option>'
                + '<option value="40px"' + ((s.spacing_top||'40px')==='40px'?' selected':'') + '>Medium (40px)</option>'
                + '<option value="60px"' + (s.spacing_top==='60px'?' selected':'') + '>Large (60px)</option>'
                + '<option value="80px"' + (s.spacing_top==='80px'?' selected':'') + '>Extra Large (80px)</option>'
                + '</select></div>';
            html += '</div>';
            break;

        case 'spacer':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Gap Size</div>';
            html += '<div class="d2-field-row"><span class="d2-field-label">Height</span>'
                + '<select class="d2-field-input" data-setting="height">'
                + '<option value="10px"'+(s.height==='10px'?' selected':'')+'>Small (10px)</option>'
                + '<option value="20px"'+((s.height||'20px')==='20px'?' selected':'')+'>Medium (20px)</option>'
                + '<option value="30px"'+(s.height==='30px'?' selected':'')+'>Large (30px)</option>'
                + '<option value="50px"'+(s.height==='50px'?' selected':'')+'>Extra Large (50px)</option>'
                + '</select></div>';
            html += '</div>';
            break;

        case 'divider':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Line Style</div>';
            html += this._ui_select('Style', s.style || 'dashed', 'style',
                [{v:'dashed',l:'Dashed (- - -)'},{v:'solid',l:'Solid (—)'},{v:'dotted',l:'Dotted (···)'}]);
            html += '</div>';
            break;

        case 'remark-block':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Remark Options</div>';
            html += this._ui_toggle('Show "Remark:" label', s.show_label !== false, 'show_label');
            html += '</div>';
            break;

        case 'amount-words':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Display Options</div>';
            html += this._ui_toggle('Show "Rupees" at start', s.show_prefix !== false, 'show_prefix');
            html += '</div>';
            break;

        case 'conditional-section':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Content Inside</div>';
            html += '<p style="font-size:11px;color:var(--d2-text-muted);">This section only shows when the condition above is met.</p>';
            html += '</div>';
            break;

        case 'custom-html':
            html += '<div class="d2-settings-section"><div class="d2-settings-section-title">Advanced</div>';
            html += '<p style="font-size:11px;color:var(--d2-text-muted);margin-bottom:6px;">⚠️ For IT team only. Paste custom HTML here.</p>';
            html += '<textarea data-setting="custom_html" class="d2-field-input" rows="6" placeholder="Paste HTML here...">' + this._esc(s.custom_html || '') + '</textarea>';
            html += '</div>';
            break;
    }
    return html;
};

// ── Visual Field List Editor (for company-header, customer-info) ──
DesignerV2.prototype._renderFieldList = function(block, groupName, sectionTitle, fieldGroupName) {
    var s = block.settings || {};
    var fields = (s.fields || []).filter(function(f) { return f.group === groupName; });
    var available = FIELD_GROUPS[fieldGroupName] || [];

    var html = '<div class="d2-settings-section"><div class="d2-settings-section-title">' + sectionTitle + '</div>';
    html += '<div class="d2-field-editor-list" data-group="' + groupName + '">';

    fields.forEach(function(f, i) {
        var sampleVal = DEFS._samples[f.placeholder] || '—';
        html += '<div class="d2-fe-row" data-field-idx="' + i + '">'
            + '<span class="d2-fe-drag" title="Drag to reorder">⠿</span>'
            + '<input type="text" class="d2-fe-label-input" data-fe-label="' + groupName + '_' + i + '" value="' + _esc(f.label) + '" placeholder="Label" title="Edit the label text">'
            + '<span class="d2-fe-placeholder" title="Sample: ' + sampleVal + '">' + (available.find(function(a){return a.key===f.placeholder;}) || {label:f.placeholder}).label + '</span>'
            + '<input type="text" class="d2-fe-suffix" data-fe-suffix="' + groupName + '_' + i + '" value="' + _esc(f.suffix || '') + '" placeholder="suffix" title="Text after value, e.g. /Gm">'
            + '<button class="d2-fe-remove" data-fe-remove="' + groupName + '_' + i + '" title="Remove this field">✕</button>'
            + '</div>';
    });

    html += '</div>';

    // "Add Field" dropdown
    html += '<div style="display:flex;gap:4px;margin-top:6px;">'
        + '<select class="d2-field-input" id="d2AddField_' + groupName + '" style="flex:1;">'
        + '<option value="">— Choose a field to add —</option>';
    available.forEach(function(f) {
        html += '<option value="' + f.key + '">' + f.label + '</option>';
    });
    html += '</select>'
        + '<button class="d2-btn d2-btn-sm d2-btn-primary" data-add-field="' + groupName + '">Add</button></div>';
    html += '</div>';
    return html;
};

// ── Formula Field Options (for calculated columns) ──
DesignerV2.prototype._formulaFieldOptions = function() {
    var cols = (FIELD_GROUPS['Item Columns'] || []);
    var html = '<option value="">Pick field...</option>';
    cols.forEach(function(c) {
        html += '<option value="' + c.key + '">' + c.label + '</option>';
    });
    return html;
};

// ── field picker dropdown (for custom-text field insertion) ──
DesignerV2.prototype._ui_fieldPicker = function(label, id) {
    var html = '<div class="d2-field-row"><span class="d2-field-label">' + label + '</span>'
        + '<select class="d2-field-input" id="d2_' + id + '"><option value="">— Pick a field —</option>';
    Object.keys(FIELD_GROUPS).forEach(function(group) {
        html += '<optgroup label="' + group + '">';
        FIELD_GROUPS[group].forEach(function(f) {
            html += '<option value="' + f.key + '">' + f.label + '</option>';
        });
        html += '</optgroup>';
    });
    html += '</select></div>';
    return html;
};

// ═══════════════════════════════════════════════
// BIND SETTINGS (connect UI to data)
// ═══════════════════════════════════════════════
DesignerV2.prototype._bindSettings = function(container, block) {
    var self = this;

    // Standard setting inputs (toggles, selects, text)
    container.querySelectorAll('[data-setting]').forEach(function(input) {
        var handler = function() {
            var key = input.dataset.setting;
            if (key === 'enabled') {
                block.enabled = input.checked;
            } else if (input.type === 'checkbox') {
                block.settings[key] = input.checked;
            } else {
                block.settings[key] = input.value;
            }
            self._render();
            self._pushHistory();
            self._markDirty();
        };
        input.addEventListener('change', handler);
        if (input.type === 'text' || input.tagName === 'TEXTAREA') input.addEventListener('input', handler);
    });

    // Column visibility checkboxes
    container.querySelectorAll('[data-col-vis]').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var idx = parseInt(cb.dataset.colVis);
            if (block.settings.columns && block.settings.columns[idx]) {
                block.settings.columns[idx].visible = cb.checked;
                self._render();
                self._pushHistory();
                self._markDirty();
            }
        });
    });

    // Column label editing
    container.querySelectorAll('[data-col-label]').forEach(function(inp) {
        inp.addEventListener('input', function() {
            var idx = parseInt(inp.dataset.colLabel);
            if (block.settings.columns && block.settings.columns[idx]) {
                block.settings.columns[idx].label = inp.value;
                self._render();
                self._markDirty();
            }
        });
    });

    // Column alignment
    container.querySelectorAll('[data-col-align]').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var idx = parseInt(sel.dataset.colAlign);
            if (block.settings.columns && block.settings.columns[idx]) {
                block.settings.columns[idx].align = sel.value;
                self._render();
                self._markDirty();
            }
        });
    });

    // Field editor — label edits
    container.querySelectorAll('[data-fe-label]').forEach(function(inp) {
        inp.addEventListener('input', function() {
            var parts = inp.dataset.feLabel.split('_');
            var group = parts[0];
            var idx = parseInt(parts[1]);
            var fields = (block.settings.fields || []).filter(function(f) { return f.group === group; });
            if (fields[idx]) {
                fields[idx].label = inp.value;
                self._render();
                self._markDirty();
            }
        });
    });

    // Field editor — suffix edits
    container.querySelectorAll('[data-fe-suffix]').forEach(function(inp) {
        inp.addEventListener('input', function() {
            var parts = inp.dataset.feSuffix.split('_');
            var group = parts[0];
            var idx = parseInt(parts[1]);
            var fields = (block.settings.fields || []).filter(function(f) { return f.group === group; });
            if (fields[idx]) {
                fields[idx].suffix = inp.value;
                self._render();
                self._markDirty();
            }
        });
    });

    // Field editor — remove button
    container.querySelectorAll('[data-fe-remove]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var parts = btn.dataset.feRemove.split('_');
            var group = parts[0];
            var idx = parseInt(parts[1]);
            var groupFields = (block.settings.fields || []).filter(function(f) { return f.group === group; });
            if (groupFields[idx]) {
                var placeholder = groupFields[idx].placeholder;
                block.settings.fields = block.settings.fields.filter(function(f,i) {
                    return !(f.group === group && f.placeholder === placeholder);
                });
                self._render();
                self._renderSettings();
                self._pushHistory();
                self._markDirty();
            }
        });
    });

    // "Add Field" buttons for field groups
    container.querySelectorAll('[data-add-field]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var group = btn.dataset.addField;
            var select = document.getElementById('d2AddField_' + group);
            if (!select || !select.value) return;
            var key = select.value;
            var labelMap = {};
            Object.keys(FIELD_GROUPS).forEach(function(g) {
                FIELD_GROUPS[g].forEach(function(f) { labelMap[f.key] = f.label; });
            });
            if (!block.settings.fields) block.settings.fields = [];
            block.settings.fields.push({label: '', placeholder: key, group: group, suffix: ''});
            select.value = '';
            self._render();
            self._renderSettings();
            self._pushHistory();
            self._markDirty();
        });
    });

    // Formula column add button
    var formulaBtn = container.querySelector('#d2BtnAddFormula');
    if (formulaBtn) {
        formulaBtn.addEventListener('click', function() {
            var name = document.getElementById('d2FormulaName');
            var f1 = document.getElementById('d2FormulaField1');
            var op = document.getElementById('d2FormulaOp');
            var f2 = document.getElementById('d2FormulaField2');
            if (!name || !name.value || !f1.value || !f2.value) {
                self._toast('Please fill column name and both fields', 'error');
                return;
            }
            if (!block.settings.columns) block.settings.columns = [];
            block.settings.columns.push({
                field: '__formula_' + Date.now(),
                label: name.value,
                visible: true,
                align: 'right',
                formula: f1.value + op.value + f2.value,
                formula_field1: f1.value,
                formula_op: op.value,
                formula_field2: f2.value
            });
            name.value = '';
            f1.value = '';
            f2.value = '';
            self._render();
            self._renderSettings();
            self._pushHistory();
            self._markDirty();
            self._toast('Calculated column added!', 'success');
        });
    }

    // Insert field into custom text
    var fieldInsert = container.querySelector('#d2_content_field_insert');
    if (fieldInsert) {
        fieldInsert.addEventListener('change', function() {
            if (!fieldInsert.value) return;
            var contentInput = container.querySelector('[data-setting="content"]');
            if (contentInput) {
                var label = fieldInsert.options[fieldInsert.selectedIndex].text;
                contentInput.value = (contentInput.value ? contentInput.value + ' ' : '') + '{{' + fieldInsert.value + '}}';
                block.settings.content = contentInput.value;
                self._render();
                self._markDirty();
            }
            fieldInsert.value = '';
        });
    }
};

// ═══════════════════════════════════════════════
// UI HELPERS (generate settings HTML)
// ═══════════════════════════════════════════════
DesignerV2.prototype._ui_toggle = function(label, checked, key) {
    return '<div class="d2-field-row"><span class="d2-field-label">' + label + '</span>'
        + '<label class="d2-toggle"><input type="checkbox" data-setting="' + key + '"' + (checked ? ' checked' : '') + '>'
        + '<span class="d2-toggle-slider"></span></label></div>';
};

DesignerV2.prototype._ui_select = function(label, value, key, options) {
    var html = '<div class="d2-field-row"><span class="d2-field-label">' + label + '</span>'
        + '<select class="d2-field-input" data-setting="' + key + '">';
    options.forEach(function(opt) {
        var v = typeof opt === 'string' ? opt : opt.v;
        var l = typeof opt === 'string' ? opt : opt.l;
        html += '<option value="' + v + '"' + (v === value ? ' selected' : '') + '>' + l + '</option>';
    });
    html += '</select></div>';
    return html;
};

// ═══════════════════════════════════════════════
// SAVE
// ═══════════════════════════════════════════════
DesignerV2.prototype.save = function() {
    var self = this;
    var data = { version: 1, blocks: this.blocks, paper_settings: this._getPaperSettings() };

    fetch(CFG.saveDesignUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    }).then(function(r) { return r.json(); }).then(function(result) {
        if (result.success) {
            self._toast('✅ Template saved successfully!', 'success');
            self.dirty = false;
            document.getElementById('d2UnsavedDot').classList.remove('visible');
            if (result.new_id) {
                CFG.templateId = result.new_id;
                CFG.saveDesignUrl = CFG.baseUrl + 'print-templates/save-design-v2/' + result.new_id;
                CFG.loadDesignUrl = CFG.baseUrl + 'print-templates/load-design/' + result.new_id;
                CFG.previewUrl = CFG.baseUrl + 'print-templates/preview-live-v2/' + result.new_id;
            }
        } else {
            self._toast('❌ Save failed: ' + (result.message || 'Unknown error'), 'error');
        }
    }).catch(function(e) {
        self._toast('❌ Save failed: ' + e.message, 'error');
    });
};

// ── MC/VA Save ──
DesignerV2.prototype._saveMCVA = function() {
    var self = this;
    var data = {
        document_type: CFG.templateCategory,
        mc_mode: document.getElementById('d2MCMode').value,
        mc_label: document.getElementById('d2MCLabel').value,
        va_mode: document.getElementById('d2VAMode').value,
        va_label: document.getElementById('d2VALabel').value
    };
    fetch(CFG.mcvaSaveUrl, {
        method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
    }).then(function(r) { return r.json(); }).then(function(result) {
        self._toast(result.success ? '✅ MC/VA config saved!' : '❌ Save failed', result.success ? 'success' : 'error');
    }).catch(function() { self._toast('❌ MC/VA save failed', 'error'); });
};

// ═══════════════════════════════════════════════
// LOAD DEFAULT TEMPLATE
// ═══════════════════════════════════════════════
DesignerV2.prototype.loadDefaultTemplate = function() {
    var self = this;
    if (this.blocks.length > 0 && !confirm('This will replace your current design. Continue?')) return;

    fetch(CFG.defaultTemplateUrl).then(function(r) { return r.json(); }).then(function(defaults) {
        if (defaults && defaults.length > 0) {
            var tmpl = defaults[0];
            var blocksData = typeof tmpl.blocks_json === 'string' ? JSON.parse(tmpl.blocks_json) : tmpl.blocks_json;
            if (blocksData && blocksData.blocks) {
                self.blocks = blocksData.blocks.map(function(b) { b.id = self._genId(); return b; });
                self._render();
                self._pushHistory();
                self._markDirty();
                self._toast('✅ Default template loaded!', 'success');
            }
        }
    }).catch(function() { self._toast('Failed to load default template', 'error'); });
};

// ═══════════════════════════════════════════════
// PREVIEW
// ═══════════════════════════════════════════════
DesignerV2.prototype._refreshPreview = function(mode) {
    var frame = document.getElementById('d2PreviewFrame');
    var data = { version: 1, blocks: this.blocks, paper_settings: this._getPaperSettings() };
    var url = CFG.previewUrl + '?mode=' + (mode || 'sample');

    fetch(url, {
        method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data)
    }).then(function(r) { return r.text(); }).then(function(html) {
        frame.srcdoc = html;
    }).catch(function(e) {
        frame.srcdoc = '<p style="color:red;padding:20px;">Preview failed: ' + e.message + '</p>';
    });
};

// ═══════════════════════════════════════════════
// UNDO / REDO
// ═══════════════════════════════════════════════
DesignerV2.prototype._pushHistory = function() {
    this.historyIdx++;
    this.history = this.history.slice(0, this.historyIdx);
    this.history.push(JSON.stringify(this.blocks));
    if (this.history.length > this.maxHistory) { this.history.shift(); this.historyIdx--; }
    this._updateUndoRedo();
};

DesignerV2.prototype.undo = function() {
    if (this.historyIdx <= 0) return;
    this.historyIdx--;
    this.blocks = JSON.parse(this.history[this.historyIdx]);
    this.selectedBlockId = null;
    this._render();
    this._renderSettings();
    this._updateUndoRedo();
    this._markDirty();
};

DesignerV2.prototype.redo = function() {
    if (this.historyIdx >= this.history.length - 1) return;
    this.historyIdx++;
    this.blocks = JSON.parse(this.history[this.historyIdx]);
    this.selectedBlockId = null;
    this._render();
    this._renderSettings();
    this._updateUndoRedo();
    this._markDirty();
};

DesignerV2.prototype._updateUndoRedo = function() {
    document.getElementById('d2BtnUndo').disabled = this.historyIdx <= 0;
    document.getElementById('d2BtnRedo').disabled = this.historyIdx >= this.history.length - 1;
};

// ═══════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════
DesignerV2.prototype._genId = function() {
    return 'blk_' + Date.now() + '_' + (++this.blockCounter);
};

DesignerV2.prototype._markDirty = function() {
    this.dirty = true;
    document.getElementById('d2UnsavedDot').classList.add('visible');
};

DesignerV2.prototype._esc = function(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
};

DesignerV2.prototype._toast = function(msg, type) {
    var container = document.getElementById('d2Toasts');
    var toast = document.createElement('div');
    toast.className = 'd2-toast ' + (type || '');
    toast.textContent = msg;
    container.appendChild(toast);
    setTimeout(function() { toast.remove(); }, 3000);
};

// Standalone escape function (used in template strings)
function _esc(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// Init
document.addEventListener('DOMContentLoaded', function() {
    window.D2 = new DesignerV2();
});

})();
