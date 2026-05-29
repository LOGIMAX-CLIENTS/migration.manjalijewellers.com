/**
 * BlockEditor — Core editor class for the Print Template Designer
 * 
 * Manages the canvas state (array of block configs), renders the palette,
 * canvas block cards, settings panel, and handles save/load via AJAX.
 * 
 * Dependencies: jQuery, Sortable.js, BLOCK_DEFINITIONS (block-definitions.js)
 */

class BlockEditor {

    constructor(options) {
        this.templateId = options.templateId;
        this.templateName = options.templateName || 'Untitled Template';
        this.baseUrl = options.baseUrl || '';
        this.categoryId = options.categoryId || 0;

        // State
        this.blocks = [];           // Array of block config objects
        this.selectedBlockId = null; // Currently selected block ID
        this.isDirty = false;       // Unsaved changes flag
        this.sortableInstance = null;
        this.customBlocks = [];     // Store fetched custom blocks

        // Undo / Redo
        this._undoStack = [];       // Snapshots before each mutation
        this._redoStack = [];       // Snapshots saved on undo
        this._maxHistory = 50;      // Max undo steps

        // DOM refs
        this.$palette = $('#be-palette-list');
        this.$canvas = $('#be-canvas');
        this.$settings = $('#be-settings');
        this.$previewFrame = $('#be-preview-frame');
        this.$previewPane = $('#be-preview-pane');

        this._init();
    }

    // ── Initialization ─────────────────────────────────────────

    async _init() {
        this._renderPalette();
        this._bindToolbarEvents();
        this._bindKeyboardShortcuts();
        this._bindPreviewToggle();
        
        // Fetch default block templates for the designer
        try {
            const response = await fetch(this.baseUrl + 'admin_print_template/get_block_templates');
            this.blockTemplates = await response.json();
            console.log('Loaded block templates:', this.blockTemplates);
        } catch (e) {
            console.error('Failed to load block templates:', e);
            this.blockTemplates = {};
        }

        // Fetch custom blocks
        try {
            const resp = await fetch(this.baseUrl + 'admin_print_template/get_custom_blocks');
            if (resp.ok) {
                const data = await resp.json();
                this.customBlocks = data || [];
                console.log('Loaded custom blocks:', this.customBlocks);
            }
        } catch (e) {
            console.error('Failed to load custom blocks:', e);
        }

        this._renderPalette();


        this._loadDesign();

        // Unsaved-changes protection
        window.addEventListener('beforeunload', (e) => {
            if (this.isDirty) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }

    _renderPalette() {
        const available = getBlocksForCategory(this.categoryId);
        const categories = {};

        // Group by category
        for (const [type, def] of Object.entries(available)) {
            if (!categories[def.category]) categories[def.category] = [];
            categories[def.category].push({ type, ...def });
        }

        let html = '';
        for (const [cat, blocks] of Object.entries(categories)) {
            html += `<div class="be-palette-category">${cat}</div>`;
            for (const b of blocks) {
                html += `
                    <div class="be-palette-item" data-type="${b.type}" title="${b.description}">
                        <span class="icon">${b.icon}</span>
                        <div>
                            <div class="label">${b.label}</div>
                            <div class="desc">${b.description}</div>
                        </div>
                    </div>`;
            }
        }



        // Render Custom Blocks
        if (this.customBlocks && this.customBlocks.length > 0) {
            html += `<div class="be-palette-category">Custom Blocks</div>`;
            for (const b of this.customBlocks) {
                // b: { id, label, category, content }
                // We use a special type 'custom-block' and store ID in data-custom-id
                html += `
                    <div class="be-palette-item" data-type="custom-block" data-custom-id="${b.id}" title="${b.label}">
                        <span class="icon">🧩</span>
                        <div>
                            <div class="label">${b.label}</div>
                            <div class="desc">Custom Saved Block</div>
                        </div>
                    </div>`;
            }
        }

        this.$palette.html(html);

    // Click to add (unbind first to prevent duplicate handlers on re-render)
        this.$palette.off('click', '.be-palette-item').on('click', '.be-palette-item', (e) => {
            const $el = $(e.currentTarget);
            const type = $el.data('type');
            if (type === 'custom-block') {
                const customId = $el.data('custom-id');
                this.addCustomBlock(customId);
            } else {
                this.addBlock(type);
            }
        });
    }

    _bindToolbarEvents() {
        $('#be-btn-save').on('click', () => this.save());
        $('#be-btn-preview').on('click', () => this.openPreview());
        $('#be-btn-undo').on('click', () => this.undo());
        $('#be-btn-redo').on('click', () => this.redo());
    }

    _bindKeyboardShortcuts() {
        $(document).on('keydown', (e) => {
            // Ignore when typing in input/textarea/select
            const tag = (e.target.tagName || '').toLowerCase();
            if (tag === 'input' || tag === 'textarea' || tag === 'select') return;

            if (e.ctrlKey && e.key === 'z' && !e.shiftKey) {
                e.preventDefault();
                this.undo();
            } else if ((e.ctrlKey && e.key === 'y') || (e.ctrlKey && e.shiftKey && e.key === 'Z')) {
                e.preventDefault();
                this.redo();
            } else if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                this.save();
            } else if (e.key === 'Delete' && this.selectedBlockId) {
                e.preventDefault();
                this.removeBlock(this.selectedBlockId);
            } else if (e.ctrlKey && e.key === 'd' && this.selectedBlockId) {
                e.preventDefault();
                this.duplicateBlock(this.selectedBlockId);
            } else if (e.key === 'Escape') {
                this.selectedBlockId = null;
                this.$canvas.find('.be-block-card').removeClass('selected');
                this._renderSettings();
            }
        });
    }

    _bindPreviewToggle() {
        $('#be-preview-toggle').on('click', () => {
            this.$previewPane.toggleClass('collapsed');
            $('#be-preview-toggle').toggleClass('collapsed');
        });
    }

    _initSortable() {
        if (this.sortableInstance) {
            this.sortableInstance.destroy();
        }
        if (this._paletteSortable) {
            this._paletteSortable.destroy();
        }

        const canvasEl = document.getElementById('be-canvas');
        if (!canvasEl) return;

        // Canvas — accepts drops from palette + reorder within
        this.sortableInstance = Sortable.create(canvasEl, {
            group: { name: 'blocks', pull: false, put: ['palette'] },
            handle: '.be-drag-handle',
            animation: 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onAdd: (evt) => {
                // A palette item was dropped into the canvas
                const droppedEl = evt.item;
                const type = droppedEl.dataset.type;
                const insertIndex = evt.newIndex;

                // Remove the cloned DOM node (we'll re-render properly)
                droppedEl.remove();

                if (type) {
                    this._pushHistory();
                    this.addBlockAt(type, insertIndex);
                }
            },
            onEnd: (evt) => {
                // Ignore cross-list drops (handled by onAdd)
                if (evt.from !== evt.to) return;

                this._pushHistory();
                // Reorder blocks array to match new DOM order
                const ids = Array.from(canvasEl.querySelectorAll('.be-block-card'))
                                 .map(el => el.dataset.blockId);
                
                const reordered = [];
                ids.forEach((id, index) => {
                    const block = this.blocks.find(b => b.id === id);
                    if (block) {
                        block.order = index;
                        reordered.push(block);
                    }
                });
                this.blocks = reordered;
                this._markDirty();
                this._refreshPreview();
            }
        });

        // Palette — draggable source (clone mode, so palette items stay)
        const paletteEl = document.getElementById('be-palette-list');
        if (paletteEl) {
            this._paletteSortable = Sortable.create(paletteEl, {
                group: { name: 'palette', pull: 'clone', put: false },
                sort: false,          // Don't allow reordering within palette
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                filter: '.be-palette-category',  // Don't drag category headers
            });
        }
    }

    // ── Block CRUD ──────────────────────────────────────────────

    addBlock(type) {
        const def = BLOCK_DEFINITIONS[type];
        if (!def) return;

        const block = {
            id: generateBlockId(),
            type: type,
            enabled: true,
            order: this.blocks.length,
            settings: getDefaultSettings(type)
        };

        this._pushHistory();
        this.blocks.push(block);
        this._markDirty();
        this._renderCanvas();
        this.selectBlock(block.id);
        this._refreshPreview();
        this._showToast(`Added: ${def.label}`, 'success');
    }

    addCustomBlock(customId) {
        // Find configuration in fetched custom blocks
        const customDef = this.customBlocks.find(b => b.id == customId);
        if (!customDef) {
            console.error('Custom block not found:', customId);
            return;
        }

        const block = {
            id: generateBlockId(),
            type: 'custom-saved-block', 
            enabled: true,
            order: this.blocks.length,
            label: customDef.label,
            settings: {
                content: customDef.content, 
                text_align: 'left'
            }
        };

        this._pushHistory();
        this.blocks.push(block);
        this._markDirty();
        this._renderCanvas();
        this.selectBlock(block.id);
        this._refreshPreview();
        this._showToast(`Added: ${customDef.label}`, 'success');
    }

    /** Insert a new block at a specific index (used by drag-from-palette) */
    addBlockAt(type, index) {
        const def = BLOCK_DEFINITIONS[type];
        if (!def) return;

        const block = {
            id: generateBlockId(),
            type: type,
            enabled: true,
            order: index,
            settings: getDefaultSettings(type)
        };

        this.blocks.splice(index, 0, block);
        // Re-index order
        this.blocks.forEach((b, i) => b.order = i);

        this._markDirty();
        this._renderCanvas();
        this.selectBlock(block.id);
        this._refreshPreview();
        this._showToast(`Added: ${def.label}`, 'success');
    }

    removeBlock(id) {
        const idx = this.blocks.findIndex(b => b.id === id);
        if (idx === -1) return;

        const def = BLOCK_DEFINITIONS[this.blocks[idx].type];
        this._pushHistory();
        this.blocks.splice(idx, 1);

        // Re-index order
        this.blocks.forEach((b, i) => b.order = i);

        if (this.selectedBlockId === id) {
            this.selectedBlockId = null;
            this._renderSettings();
        }

        this._markDirty();
        this._renderCanvas();
        this._refreshPreview();
        this._showToast(`Removed: ${def ? def.label : 'Block'}`, 'success');
    }

    toggleBlock(id) {
        const block = this.blocks.find(b => b.id === id);
        if (!block) return;
        this._pushHistory();
        block.enabled = !block.enabled;
        this._markDirty();
        this._renderCanvas();
        this._refreshPreview();
    }

    duplicateBlock(id) {
        const block = this.blocks.find(b => b.id === id);
        if (!block) return;

        const newBlock = JSON.parse(JSON.stringify(block));
        newBlock.id = generateBlockId();
        newBlock.order = this.blocks.length;

        this._pushHistory();
        this.blocks.push(newBlock);
        this._markDirty();
        this._renderCanvas();
        this.selectBlock(newBlock.id);
        this._refreshPreview();

        const def = BLOCK_DEFINITIONS[block.type];
        this._showToast(`Duplicated: ${def ? def.label : 'Block'}`, 'success');
    }

    selectBlock(id) {
        this.selectedBlockId = id;
        
        // Update card selection
        this.$canvas.find('.be-block-card').removeClass('selected');
        this.$canvas.find(`.be-block-card[data-block-id="${id}"]`).addClass('selected');

        this._renderSettings();
    }

    updateBlockSetting(id, key, value) {
        const block = this.blocks.find(b => b.id === id);
        if (!block) return;
        this._pushHistory();
        block.settings[key] = value;
        this._markDirty();

        // Update visual preview on the canvas card
        const $card = this.$canvas.find(`.be-block-card[data-block-id="${id}"] .bvp-container`);
        if ($card.length) {
            $card.html(this._renderVisualPreview(block));
        }

        this._refreshPreview();
    }

    // ── Canvas Rendering ────────────────────────────────────────

    _renderCanvas() {
        if (this.blocks.length === 0) {
            this.$canvas.html(`
                <div class="be-canvas-empty">
                    <div class="icon">📄</div>
                    <div class="title">No blocks added yet</div>
                    <div class="hint">Click blocks from the left panel to add them to your template</div>
                </div>`);
            return;
        }

        let html = '';
        const sorted = [...this.blocks].sort((a, b) => a.order - b.order);

        for (const block of sorted) {
            const def = BLOCK_DEFINITIONS[block.type] || {};
            const isSelected = block.id === this.selectedBlockId;
            const isDisabled = !block.enabled;

            const condKey = (block.settings && block.settings.condition_key && block.settings.condition_key !== 'none') 
                ? block.settings.condition_key : '';

            html += `
                <div class="be-block-card ${isSelected ? 'selected' : ''} ${isDisabled ? 'disabled' : ''}" 
                     data-block-id="${block.id}">
                    <div class="be-block-card-header">
                        <span class="be-drag-handle" title="Drag to reorder">☰</span>
                        <span class="be-block-icon">${def.icon || '📦'}</span>
                        <span class="be-block-title">${def.label || block.type}</span>
                        ${condKey ? `<span class="be-cond-badge" title="Conditional: ${condKey}" style="font-size:10px; background:#fff3cd; color:#856404; padding:1px 6px; border-radius:10px; margin-left:6px; border:1px solid #ffc107; white-space:nowrap;">🔀 ${condKey}</span>` : ''}
                        <div class="be-block-actions">
                            <button class="be-card-customize" title="Customize Design" data-action="customize">
                                <span class="cust-icon">🎨</span> Customize
                            </button>
                            <button class="toggle" title="${block.enabled ? 'Disable' : 'Enable'}" data-action="toggle">
                                ${block.enabled ? '👁' : '🚫'}
                            </button>
                            <button class="duplicate" title="Duplicate" data-action="duplicate">📋</button>
                            <button class="delete" title="Remove" data-action="delete">✕</button>
                        </div>
                    </div>
                    <div class="be-block-preview-mini bvp-container">
                        ${this._renderVisualPreview(block)}
                    </div>
                </div>`;
        }

        this.$canvas.html(html);

        // Unbind previous events to prevent memory leak on re-render
        this.$canvas.off('click', '.be-block-card');
        this.$canvas.off('click', '.be-block-actions button');

        // Bind card events
        this.$canvas.on('click', '.be-block-card', (e) => {
            if ($(e.target).closest('.be-block-actions').length) return;
            const id = $(e.currentTarget).data('block-id');
            this.selectBlock(id);
        });

        this.$canvas.on('click', '.be-block-actions button', (e) => {
            e.stopPropagation();
            const $btn = $(e.currentTarget);
            const id = $btn.closest('.be-block-card').data('block-id');
            const action = $btn.data('action');
            
            if (action === 'delete') this.removeBlock(id);
            else if (action === 'toggle') this.toggleBlock(id);
            else if (action === 'duplicate') this.duplicateBlock(id);
            else if (action === 'customize') this.openBlockDesigner(id);
        });

        this._initSortable();

        // ── Inline divider editing ──────────────────────────────
        // Prevent card selection when clicking on a contenteditable divider
        this.$canvas.on('click', '.bvp-divider-editable', (e) => {
            e.stopPropagation();
            // Still select the block so settings panel updates
            const id = $(e.currentTarget).closest('.be-block-card').data('block-id');
            this.selectBlock(id);
        });

        // On blur (user finished editing) — save the typed content
        this.$canvas.on('blur', '.bvp-divider-editable', (e) => {
            const $el = $(e.currentTarget);
            const id  = $el.data('block-id');
            const key = $el.data('key');
            const newText = $el.text().trim() || $el.html().trim();
            
            const block = this.blocks.find(b => b.id === id);
            if (!block) return;
            
            const current = (block.settings[key] || '').toString().trim();
            if (newText === current) return; // no change

            this._pushHistory();
            block.settings[key] = newText;
            this._markDirty();

            // Sync the right-panel text input if it's currently showing this block
            if (this.selectedBlockId === id) {
                const $panelInput = this.$settings.find(`input[data-key="${key}"][data-block-id="${id}"]`);
                if ($panelInput.length) $panelInput.val(newText);
            }

            this._refreshPreview();
        });

        // Prevent Enter from adding <br> (keep to one line)
        this.$canvas.on('keydown', '.bvp-divider-editable', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.currentTarget.blur();
            }
        });
    }

    _getMiniPreview(block) {
        // Legacy fallback — kept for compatibility
        const def = BLOCK_DEFINITIONS[block.type];
        if (!def) return block.type;
        const parts = [];
        for (const s of def.settings) {
            if (s.type === 'toggle' && block.settings[s.key] === true) parts.push(s.label);
            else if (s.type === 'column-picker' && block.settings[s.key]) parts.push(`${block.settings[s.key].length} columns`);
            else if (s.type === 'select' && s.key === 'font_size') parts.push(block.settings[s.key]);
        }
        return parts.length > 0 ? parts.join(' · ') : def.description;
    }

    // ── Undo / Redo ────────────────────────────────────────────
    //
    // Simple dual-stack model:
    //   _undoStack: snapshots of blocks BEFORE each mutation
    //   _redoStack: snapshots saved when the user performs undo
    //   Any new mutation clears the redo stack.

    /** Deep-clone the blocks array */
    _snapshotState() {
        return JSON.parse(JSON.stringify(this.blocks));
    }

    /** Called BEFORE every mutation to save the current state for undo */
    _pushHistory() {
        this._undoStack.push(this._snapshotState());

        // Hard cap
        if (this._undoStack.length > this._maxHistory) {
            this._undoStack.shift();
        }

        // Any new action invalidates redo
        this._redoStack = [];
        this._updateUndoRedoButtons();
    }

    undo() {
        if (this._undoStack.length === 0) return;

        // Save current state to redo stack before restoring
        this._redoStack.push(this._snapshotState());

        // Pop the last saved state and restore
        const snapshot = this._undoStack.pop();
        this._restoreState(snapshot);
        this._showToast('Undo', 'success');
    }

    redo() {
        if (this._redoStack.length === 0) return;

        // Save current state to undo stack before restoring
        this._undoStack.push(this._snapshotState());

        // Pop from redo stack and restore
        const snapshot = this._redoStack.pop();
        this._restoreState(snapshot);
        this._showToast('Redo', 'success');
    }

    /** Restore a snapshot into the editor (does NOT push history) */
    _restoreState(snapshot) {
        this.blocks = JSON.parse(JSON.stringify(snapshot));
        this.selectedBlockId = null;
        this._renderCanvas();
        this._renderSettings();
        this._markDirty();
        this._refreshPreview();
        this._updateUndoRedoButtons();
    }

    _updateUndoRedoButtons() {
        const $undo = $('#be-btn-undo');
        const $redo = $('#be-btn-redo');
        if ($undo.length) $undo.prop('disabled', this._undoStack.length === 0);
        if ($redo.length) $redo.prop('disabled', this._redoStack.length === 0);
    }

    /**
     * Generate a visual HTML preview for a block card.
     * Each block type gets a styled mini-representation.
     */
    _renderVisualPreview(block) {
        const s = block.settings || {};
        const fs = s.font_size || '12px';

        // If block has been customized via Block Designer, show the custom HTML directly
        let previewHtml = '';

        if (block.custom_html) {
            previewHtml = `<div class="bvp-custom-html" style="font-size:10px; overflow:hidden; max-height:120px; border:1px dashed #3c8dbc; padding:4px; background:#fafafa;">
                ${block.custom_html}
            </div>`;
        } else {
            previewHtml = this._renderBasePreview(block);
        }

        // Global Conditional Wrapper for all blocks
        const conditionKey = (block.settings && block.settings.condition_key) || 'none';
        if (conditionKey !== 'none' && block.type !== 'conditional-section') {
            return `<div class="bvp-conditional-wrapper" data-condition="${conditionKey}">
                <div class="bvp-cond-tag header">{{#${conditionKey}}}</div>
                <div class="bvp-cond-content">${previewHtml}</div>
                <div class="bvp-cond-tag footer">{{/${conditionKey}}}</div>
            </div>`;
        }

        return previewHtml;
    }

    /**
     * Internal helper to render the base preview of a block without global wrappers.
     */
    _renderBasePreview(block) {
        const s = block.settings || {};
        const fs = s.font_size || '12px';

        switch (block.type) {
            case 'custom-html': {
                const content = block.content || block.custom_html || '<em>Custom HTML</em>';
                const css = block.css || '';
                return `<div class="bvp-custom-html" style="font-size:10px; overflow:hidden; max-height:120px; border:1px dashed #999; padding:4px; background:#fafafa;">
                    ${css ? '<style scoped>' + css + '</style>' : ''}
                    ${content}
                </div>`;
            }

            case 'custom-saved-block': {
                const s = block.settings || {};
                const content = s.content || '<em>Custom Content</em>';
                // Strip scripts/styles for preview if needed, or just show it
                return `<div class="bvp-custom-saved" style="font-size:10px; overflow:hidden; max-height:80px; border:1px dashed #ccc; padding:2px;">
                    ${content}
                </div>`;
            }

            case 'company-header': {
                const companyFields = s.fields || [];
                let compHtml = `<div class="bvp-company" style="text-align:${s.text_align || 'center'}; font-size:${fs};">`;
                if (s.show_logo) compHtml += '<div class="bvp-logo-placeholder">🏢</div>';
                for (const f of companyFields) {
                    const lbl = f.label ? `<span class="bvp-label">${f.label}</span> ` : '';
                    const suf = f.suffix || '';
                    const sty = f.style === 'title' ? ' style="font-size:18px;font-weight:bold;"' : '';
                    compHtml += `<div class="bvp-field"${sty}>${lbl}{{${f.placeholder}}}${suf}</div>`;
                }
                if (s.border_bottom !== false) compHtml += '<div class="bvp-border-bottom"></div>';
                compHtml += '</div>';
                return compHtml;
            }

            case 'customer-info': {
                const allFields = s.fields || [];
                const isTwoCol = (s.layout || 'two-column') === 'two-column';
                const showBorders = s.show_borders !== false;
                const custFields = allFields.filter(f => f.group === 'customer');
                const invFields = allFields.filter(f => f.group === 'invoice');
                const borderStyle = showBorders && isTwoCol ? 'border-right:2px solid #000; padding-right:8px;' : '';
                let custHtml = `<div class="bvp-customer ${isTwoCol ? 'bvp-two-col' : ''}" style="font-size:${fs};">`;
                // Customer column (left side)
                custHtml += `<div class="bvp-cust-col" style="${borderStyle}">`;
                for (const f of custFields) {
                    const lbl = f.label ? `<span class="bvp-label">${f.label}</span> ` : '';
                    const suf = f.suffix || '';
                    custHtml += `<div class="bvp-field">${lbl}{{${f.placeholder}}}${suf}</div>`;
                }
                custHtml += '</div>';
                // Invoice column (right side) — rendered as label : value table
                if (invFields.length > 0) {
                    if (isTwoCol) {
                        custHtml += '<div class="bvp-cust-col"><table class="bvp-inv-table">';
                        for (const f of invFields) {
                            const lbl = f.label || '';
                            const suf = f.suffix || '';
                            custHtml += `<tr><td class="bvp-inv-lbl">${lbl}</td><td class="bvp-inv-sep">:</td><td class="bvp-inv-val">{{${f.placeholder}}}${suf}</td></tr>`;
                        }
                        custHtml += '</table></div>';
                    } else {
                        custHtml += '<div style="margin-top:8px;">';
                        for (const f of invFields) {
                            const lbl = f.label ? `<span class="bvp-label">${f.label}</span> ` : '';
                            const suf = f.suffix || '';
                            custHtml += `<div class="bvp-field">${lbl}{{${f.placeholder}}}${suf}</div>`;
                        }
                        custHtml += '</div>';
                    }
                }
                custHtml += '</div>';
                return custHtml;
            }

            case 'items-table':
            case 'purchase-table':
            case 'sales-return-table':
            case 'credit-collection-table':
            case 'chit-preclose-table':
                return this._renderTableVisual(block);

            case 'tax-summary':
                let taxHtml = '<div class="bvp-tax" style="font-size:' + fs + ';">';
                const taxRows = [
                    { key: 'show_subtotal', label: 'Subtotal', val: '₹45,000' },
                    { key: 'show_cgst', label: 'CGST @1.5%', val: '₹675' },
                    { key: 'show_sgst', label: 'SGST @1.5%', val: '₹675' },
                    { key: 'show_igst', label: 'IGST @3%', val: '₹1,350' },
                    { key: 'show_round_off', label: 'Round Off', val: '-₹0.50' },
                ];
                for (const row of taxRows) {
                    if (s[row.key] !== false) {
                        taxHtml += `<div class="bvp-tax-row"><span>${row.label}</span><span>${row.val}</span></div>`;
                    }
                }
                taxHtml += '<div class="bvp-tax-row bvp-grand-total"><span>Grand Total</span><span>₹46,349</span></div>';
                if (s.show_amount_words !== false) {
                    taxHtml += '<div class="bvp-amount-words">Rupees Forty-Six Thousand Three Hundred Forty-Nine Only</div>';
                }
                taxHtml += '</div>';
                return taxHtml;

            case 'payment-info':
                let payHtml = '<div class="bvp-payment" style="font-size:' + fs + ';">';
                payHtml += '<div class="bvp-pay-title">Payment Details</div>';
                const payRows = [
                    { key: 'show_cash', label: 'Cash', val: '₹20,000' },
                    { key: 'show_card', label: 'Card', val: '₹15,000' },
                    { key: 'show_upi', label: 'UPI', val: '₹11,349' },
                    { key: 'show_cheque', label: 'Cheque', val: '—' },
                    { key: 'show_balance', label: 'Balance', val: '₹0' },
                ];
                for (const row of payRows) {
                    if (s[row.key] !== false) {
                        payHtml += `<div class="bvp-tax-row"><span>${row.label}</span><span>${row.val}</span></div>`;
                    }
                }
                payHtml += '</div>';
                return payHtml;

            case 'advance-payment':
                let advHtml = '<div class="bvp-payment" style="font-size:' + fs + ';">';
                advHtml += '<div class="bvp-pay-title">Advance / Payment</div>';
                if (s.show_sub_total !== false) advHtml += '<div class="bvp-tax-row"><span>Sub Total</span><span>₹25,000</span></div>';
                if (s.show_approx_total !== false) advHtml += '<div class="bvp-tax-row"><span>Approx Total</span><span>₹26,500</span></div>';
                if (s.show_payment_modes !== false) advHtml += '<div class="bvp-tax-row"><span>Advance Paid</span><span>₹10,000</span></div>';
                advHtml += '</div>';
                return advHtml;

            case 'remark-block':
                return `<div class="bvp-remark" style="font-size:${fs};">
                    ${s.show_label !== false ? '<span class="bvp-label">Remark:</span> ' : ''}
                    <span class="bvp-muted">Customer remark or additional notes will appear here...</span>
                </div>`;

            case 'signature-block':
                const cols = s.layout === 'three-column' ? 3 : 2;
                let sigHtml = `<div class="bvp-signature bvp-sig-${cols}" style="margin-top:${s.spacing_top || '20px'};">`;
                if (s.show_customer_sign !== false) sigHtml += '<div class="bvp-sig-col"><div class="bvp-sig-line"></div><div class="bvp-sig-label">Customer Signature</div></div>';
                if (s.show_cashier) sigHtml += '<div class="bvp-sig-col"><div class="bvp-sig-line"></div><div class="bvp-sig-label">Cashier</div></div>';
                if (s.show_auth_sign !== false) sigHtml += '<div class="bvp-sig-col"><div class="bvp-sig-line"></div><div class="bvp-sig-label">Authorized Signatory</div></div>';
                sigHtml += '</div>';
                return sigHtml;

            case 'amount-words':
                return `<div class="bvp-amount-words-block" style="font-size:${fs}; ${s.font_weight ? 'font-weight:bold;' : ''}">
                    ${s.show_prefix !== false ? '<span class="bvp-label">Rupees</span> ' : ''}
                    Forty-Six Thousand Three Hundred Forty-Nine Only
                </div>`;

            case 'custom-text':
                const textContent = s.content || '<em>Click to add text...</em>';
                const textBold = s.bold ? 'font-weight:bold;' : '';
                const textAlign = s.text_align || 'left';
                return `<div class="bvp-custom-text" style="font-size:${fs}; text-align:${textAlign}; ${textBold}">
                    ${textContent}
                </div>`;

            case 'spacer':
                const spacerH = s.height || '20px';
                return `<div class="bvp-spacer" style="height:${spacerH};">
                    <span class="bvp-spacer-label">↕ ${spacerH}</span>
                </div>`;

            case 'page-break':
                return `<div class="bvp-page-break">
                    <span class="bvp-pb-line"></span>
                    <span class="bvp-pb-label">✂ Page Break</span>
                    <span class="bvp-pb-line"></span>
                </div>`;

            case 'line-solid': {
                const lsColor = s.color || '#000000';
                const lsThick = s.thickness || '1';
                const lsWidth = s.width || '100';
                const lsMt = s.margin_top || '5px';
                const lsMb = s.margin_bottom || '5px';
                return `<div style="margin-top:${lsMt}; margin-bottom:${lsMb}; text-align:center;">
                    <div style="display:inline-block; width:${lsWidth}%; border-top:${lsThick}px solid ${lsColor};"></div>
                </div>`;
            }

            case 'line-dashed': {
                const ldColor = s.color || '#000000';
                const ldThick = s.thickness || '1';
                const ldWidth = s.width || '100';
                const ldMt = s.margin_top || '5px';
                const ldMb = s.margin_bottom || '5px';
                return `<div style="margin-top:${ldMt}; margin-bottom:${ldMb}; text-align:center;">
                    <div style="display:inline-block; width:${ldWidth}%; border-top:${ldThick}px dashed ${ldColor};"></div>
                </div>`;
            }

            case 'line-dotted': {
                const ltColor = s.color || '#000000';
                const ltThick = s.thickness || '1';
                const ltWidth = s.width || '100';
                const ltMt = s.margin_top || '5px';
                const ltMb = s.margin_bottom || '5px';
                return `<div style="margin-top:${ltMt}; margin-bottom:${ltMb}; text-align:center;">
                    <div style="display:inline-block; width:${ltWidth}%; border-top:${ltThick}px dotted ${ltColor};"></div>
                </div>`;
            }

            case 'divider': {
                const divContent = s.content || '- - - - - - - - - - - - - - - - - - - -';
                const divAlign = s.text_align || 'center';
                const divFs = s.font_size || '12px';
                const mt = s.margin_top || '5px';
                const mb = s.margin_bottom || '5px';
                return `<div class="bvp-divider bvp-divider-editable"
                    contenteditable="true"
                    data-block-id="${block.id}"
                    data-key="content"
                    title="Click to edit divider text"
                    style="margin-top:${mt}; margin-bottom:${mb}; text-align:${divAlign}; font-size:${divFs}; overflow:hidden; white-space:nowrap; letter-spacing:2px; cursor:text; outline:none; min-height:1em;"
                >${divContent}</div>`;
            }

            case 'conditional-section': {
                const condKey = s.condition_key || 'has_sales_items';
                const condContent = s.content || '<em>Add content here...</em>';
                // Special case for conditional-section: it already HAS the tags inside the block designer or here
                return `<div class="bvp-conditional" style="border:1px dashed #f39c12; padding:6px; background:#fff8e1; border-radius:4px;">
                    <div style="font-size:10px; color:#f39c12; font-weight:bold; margin-bottom:4px;">🔀 IF: {{#${condKey}}}</div>
                    <div style="font-size:11px;">${condContent}</div>
                    <div style="font-size:10px; color:#f39c12; font-weight:bold; margin-top:4px;">{{/${condKey}}}</div>
                </div>`;
            }

            default: {
                let html = `<div class="bvp-generic">${this._getMiniPreview(block)}</div>`;
                
                // If this generic/default block has a condition, wrap it visually
                const conditionKey = (block.settings && block.settings.condition_key) || 'none';
                if (conditionKey !== 'none' && block.type !== 'conditional-section') {
                    return `<div class="bvp-conditional-wrapper" data-condition="${conditionKey}">
                        <div class="bvp-cond-tag header">{{#${conditionKey}}}</div>
                        <div class="bvp-cond-content">${html}</div>
                        <div class="bvp-cond-tag footer">{{/${conditionKey}}}</div>
                    </div>`;
                }
                return html;
            }
        }
    }

    /**
     * Render a table-type block visual with column headers.
     */
    _renderTableVisual(block) {
        const s = block.settings || {};
        const fs = s.font_size || '12px';
        const def = BLOCK_DEFINITIONS[block.type];
        const colDef = def?.settings.find(st => st.type === 'column-picker');
        const activeColumns = s.columns || (colDef ? colDef.default : []);
        const allCols = colDef ? colDef.options : [];

        // Filter to visible columns
        const visibleCols = allCols.filter(c => activeColumns.includes(c.key));

        if (visibleCols.length === 0) {
            return '<div class="bvp-generic">No columns selected</div>';
        }

        let html = `<div class="bvp-table-wrap" style="font-size:${fs};">`;
        if (s.table_title) {
            html += `<div class="bvp-table-title">${s.table_title}</div>`;
        }
        html += '<table class="bvp-table"><thead><tr>';
        for (const col of visibleCols) {
            html += `<th>${col.label}</th>`;
        }
        html += '</tr></thead><tbody>';

        // Sample data rows (2 rows)
        const sampleRows = [
            { sno: '1', description: 'Gold Ring 22K', hsn_code: '7113', purity: '22K', qty: '1', gross_wt: '8.500', net_wt: '8.200', va_percent: '10%', mc: '500', rate: '7,250', amount: '₹60,150', stone_less: '0.30', metal_less: '0.00', taxable_amount: '₹60,150', bill_no: 'INV-101', bill_date: '15-Feb', bill_amount: '₹50,000', due_amount: '₹20,000', received_amount: '₹15,000', balance: '₹5,000', scheme_name: 'Gold Plan', months: '12' },
            { sno: '2', description: 'Silver Anklet', hsn_code: '7113', purity: '925', qty: '2', gross_wt: '45.00', net_wt: '44.50', va_percent: '8%', mc: '300', rate: '95', amount: '₹4,228', stone_less: '0.50', metal_less: '0.00', taxable_amount: '₹4,228', bill_no: 'INV-102', bill_date: '16-Feb', bill_amount: '₹30,000', due_amount: '₹10,000', received_amount: '₹10,000', balance: '₹0', scheme_name: 'Silver Plan', months: '6' },
        ];

        for (const row of sampleRows) {
            html += '<tr>';
            for (const col of visibleCols) {
                html += `<td>${row[col.key] || '—'}</td>`;
            }
            html += '</tr>';
        }

        if (s.show_footer_totals !== false) {
            html += '<tr class="bvp-totals-row"><td colspan="' + Math.max(1, visibleCols.length - 1) + '"><strong>Total</strong></td><td><strong>₹64,378</strong></td></tr>';
        }
        html += '</tbody></table></div>';
        return html;
    }

    // ── Settings Panel ──────────────────────────────────────────

    _renderSettings() {
        if (!this.selectedBlockId) {
            this.$settings.html(`
                <div class="be-settings-empty">
                    <div class="icon">⚙️</div>
                    <div>Select a block to configure its settings</div>
                </div>`);
            return;
        }

        const block = this.blocks.find(b => b.id === this.selectedBlockId);
        if (!block) return;

        const def = BLOCK_DEFINITIONS[block.type];
        if (!def) return;

        let html = `
            <div class="be-settings-header">
                <h3>${def.icon} ${def.label}</h3>
                <div class="block-type">${def.description}</div>
            </div>
            <div class="be-settings-body">`;

        // Group settings by type for better UX
        const toggles = def.settings.filter(s => s.type === 'toggle');
        const selects = def.settings.filter(s => s.type === 'select' || s.type === 'text' || s.type === 'number' || s.type === 'textarea');
        const columnPickers = def.settings.filter(s => s.type === 'column-picker');

        // Layout & Style settings
        if (selects.length > 0) {
            html += `<div class="be-settings-group">
                        <div class="be-settings-group-title">Layout & Style</div>`;
            for (const s of selects) {
                html += this._renderSettingControl(s, block.settings[s.key], block.id);
            }
            html += `</div>`;
        }

        // Field editor (for blocks with hasFieldEditor)
        if (def.hasFieldEditor) {
            html += this._renderFieldEditor(block, def);
        }

        // Toggle settings
        if (toggles.length > 0) {
            html += `<div class="be-settings-group">
                        <div class="be-settings-group-title">Visibility</div>`;
            for (const s of toggles) {
                html += this._renderSettingControl(s, block.settings[s.key], block.id);
            }
            html += `</div>`;
        }

        // Column pickers
        if (columnPickers.length > 0) {
            for (const s of columnPickers) {
                html += `<div class="be-settings-group">
                            <div class="be-settings-group-title">${s.label}</div>`;
                html += this._renderSettingControl(s, block.settings[s.key], block.id);
                html += `</div>`;
            }
        }

        html += `</div>`;
        this.$settings.html(html);
        this._bindSettingsEvents();
    }

    // ── Field Editor ──────────────────────────────────────────────

    _renderFieldEditor(block, def) {
        let html = '';
        const fields = block.settings.fields || [];

        for (const fg of def.fieldGroups) {
            const groupFields = fields.filter(f => f.group === fg.key);
            html += `<div class="be-settings-group">
                <div class="be-settings-group-title">${fg.label}</div>
                <div class="be-field-list" data-group="${fg.key}" data-block-id="${block.id}">`;

            for (let i = 0; i < groupFields.length; i++) {
                const f = groupFields[i];
                const globalIdx = fields.indexOf(f);
                html += `<div class="be-field-row" data-field-idx="${globalIdx}">
                    <span class="be-field-drag">≡</span>
                    <input type="text" class="be-field-label" value="${(f.label || '').replace(/"/g, '&quot;')}" 
                           placeholder="Label" data-field-idx="${globalIdx}" data-block-id="${block.id}" />
                    <span class="be-field-chip">{{${f.placeholder}}}</span>
                    <button class="be-field-remove" data-field-idx="${globalIdx}" data-block-id="${block.id}" title="Remove">✕</button>
                </div>`;
            }

            html += `</div>`;

            // "Add Field" dropdown — only show placeholders not already used
            const usedKeys = groupFields.map(f => f.placeholder);
            const available = (def.availablePlaceholders || []).filter(p => 
                p.group === fg.key && !usedKeys.includes(p.key)
            );

            if (available.length > 0) {
                html += `<div class="be-field-add-wrap">
                    <select class="be-field-add-select" data-group="${fg.key}" data-block-id="${block.id}">
                        <option value="">+ Add Field...</option>`;
                for (const p of available) {
                    html += `<option value="${p.key}">${p.label}</option>`;
                }
                html += `</select></div>`;
            }

            html += `</div>`;
        }
        return html;
    }

    _bindFieldEditorEvents() {
        const self = this;

        // Label editing
        this.$settings.find('.be-field-label').off('input').on('input', function() {
            const idx = parseInt($(this).data('field-idx'));
            const blockId = $(this).data('block-id');
            const block = self.blocks.find(b => b.id === blockId);
            if (block && block.settings.fields && block.settings.fields[idx]) {
                block.settings.fields[idx].label = $(this).val();
                self._markDirty();
                self._renderCanvas();
                self._refreshPreview();
            }
        });

        // Remove field
        this.$settings.find('.be-field-remove').off('click').on('click', function() {
            const idx = parseInt($(this).data('field-idx'));
            const blockId = $(this).data('block-id');
            const block = self.blocks.find(b => b.id === blockId);
            if (block && block.settings.fields) {
                self._pushHistory();
                block.settings.fields.splice(idx, 1);
                self._markDirty();
                self._renderCanvas();
                self._renderSettings();
                self._refreshPreview();
            }
        });

        // Add field
        this.$settings.find('.be-field-add-select').off('change').on('change', function() {
            const key = $(this).val();
            if (!key) return;
            const blockId = $(this).data('block-id');
            const group = $(this).data('group');
            const block = self.blocks.find(b => b.id === blockId);
            const def = BLOCK_DEFINITIONS[block.type];
            const placeholder = def.availablePlaceholders.find(p => p.key === key);
            if (block && placeholder) {
                self._pushHistory();
                if (!block.settings.fields) block.settings.fields = [];
                block.settings.fields.push({
                    label: placeholder.label + ':',
                    placeholder: placeholder.key,
                    group: group
                });
                self._markDirty();
                self._renderCanvas();
                self._renderSettings();
                self._refreshPreview();
            }
        });

        // Drag-reorder fields within each group
        this.$settings.find('.be-field-list').each(function() {
            const listEl = this;
            const blockId = $(listEl).data('block-id');
            const group = $(listEl).data('group');
            if (listEl._sortable) listEl._sortable.destroy();
            listEl._sortable = Sortable.create(listEl, {
                handle: '.be-field-drag',
                animation: 150,
                ghostClass: 'be-field-ghost',
                onEnd(evt) {
                    const block = self.blocks.find(b => b.id === blockId);
                    if (!block || !block.settings.fields) return;
                    self._pushHistory();
                    // Reorder: get group fields, reorder them, rebuild full array
                    const allFields = block.settings.fields;
                    const groupFields = allFields.filter(f => f.group === group);
                    const otherFields = allFields.filter(f => f.group !== group);
                    // Apply the drag reorder to groupFields
                    const moved = groupFields.splice(evt.oldIndex, 1)[0];
                    groupFields.splice(evt.newIndex, 0, moved);
                    // Rebuild: other groups first (in original order), then reordered group
                    const def = BLOCK_DEFINITIONS[block.type];
                    const rebuiltFields = [];
                    for (const fg of def.fieldGroups) {
                        if (fg.key === group) {
                            rebuiltFields.push(...groupFields);
                        } else {
                            rebuiltFields.push(...allFields.filter(f => f.group === fg.key));
                        }
                    }
                    block.settings.fields = rebuiltFields;
                    self._markDirty();
                    self._renderCanvas();
                    self._renderSettings();
                    self._refreshPreview();
                }
            });
        });
    }

    _renderSettingControl(setting, value, blockId) {
        switch (setting.type) {
            case 'toggle':
                return `
                    <div class="be-setting-row">
                        <span class="be-setting-label">${setting.label}</span>
                        <label class="be-toggle">
                            <input type="checkbox" data-block-id="${blockId}" 
                                   data-key="${setting.key}" ${value ? 'checked' : ''}>
                            <span class="be-toggle-slider"></span>
                        </label>
                    </div>`;

            case 'select':
                const opts = setting.options.map(o =>
                    `<option value="${o}" ${o === value ? 'selected' : ''}>${o}</option>`
                ).join('');
                return `
                    <div class="be-setting-row">
                        <span class="be-setting-label">${setting.label}</span>
                        <select data-block-id="${blockId}" data-key="${setting.key}">${opts}</select>
                    </div>`;

            case 'text':
                return `
                    <div class="be-setting-row">
                        <span class="be-setting-label">${setting.label}</span>
                        <input type="text" data-block-id="${blockId}" data-key="${setting.key}" 
                               value="${value || ''}" placeholder="${setting.default || ''}">
                    </div>`;

            case 'number':
                return `
                    <div class="be-setting-row">
                        <span class="be-setting-label">${setting.label}</span>
                        <input type="number" data-block-id="${blockId}" data-key="${setting.key}" 
                               value="${value || setting.default || 0}">
                    </div>`;

            case 'textarea':
                const escapedVal = (value || '').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return `
                    <div class="be-setting-row be-setting-row-textarea">
                        <span class="be-setting-label">${setting.label}</span>
                        <textarea data-block-id="${blockId}" data-key="${setting.key}"
                                  rows="4" placeholder="Enter text content...">${value || ''}</textarea>
                    </div>`;

            case 'column-picker':
                const activeColumns = value || [];
                let cpHtml = '<div class="be-column-picker">';
                for (const col of setting.options) {
                    const checked = activeColumns.includes(col.key);
                    cpHtml += `
                        <div class="be-column-item">
                            <input type="checkbox" id="col_${col.key}" 
                                   data-block-id="${blockId}" data-key="${setting.key}" 
                                   data-col-key="${col.key}" ${checked ? 'checked' : ''}>
                            <label for="col_${col.key}">${col.label}</label>
                        </div>`;
                }
                cpHtml += '</div>';
                return cpHtml;

            default:
                return '';
        }
    }

    _bindSettingsEvents() {
        // Toggle changes
        this.$settings.find('.be-toggle input').off('change').on('change', (e) => {
            const $el = $(e.target);
            this.updateBlockSetting($el.data('block-id'), $el.data('key'), e.target.checked);
        });

        // Select changes (exclude field editor add-select)
        this.$settings.find('select:not(.be-field-add-select)').off('change').on('change', (e) => {
            const $el = $(e.target);
            const key = $el.data('key');
            const blockId = $el.data('block-id');
            const val = $el.val();

            this.updateBlockSetting(blockId, key, val);

            // ── Line Style preset → auto-fill content ──────────────────
            if (key === 'line_style' && val !== '(custom)') {
                const PRESETS = {
                    '─── Solid Line ───':     '─────────────────────────────────────────────────────────',
                    '- - - Dashed - - -':     '- - - - - - - - - - - - - - - - - - - - - - - - - - - - -',
                    '· · · Dotted · · ·':     '· · · · · · · · · · · · · · · · · · · · · · · · · · · · ·',
                    '═══ Double ═══':         '═══════════════════════════════════════════════════════',
                    '* * * Stars * * *':      '* * * * * * * * * * * * * * * * * * * * * * * * * * * * *',
                    '— — — Em Dash — — —':   '— — — — — — — — — — — — — — — — — — — — — — — — — — — —',
                    '▬▬▬▬▬ Block ▬▬▬▬▬':     '▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬▬'
                };
                const preset = PRESETS[val];
                if (preset) {
                    this.updateBlockSetting(blockId, 'content', preset);
                    // Sync the text input in the right panel
                    this.$settings.find(`input[data-key="content"][data-block-id="${blockId}"]`).val(preset);
                    // Sync the live canvas divider element
                    this.$canvas.find(`.bvp-divider-editable[data-block-id="${blockId}"]`).text(preset);
                }
            }
        });

        // Text/number changes (exclude field editor label inputs)
        this.$settings.find('input[type="text"][data-key], input[type="number"]').off('input').on('input', (e) => {
            const $el = $(e.target);
            const val = e.target.type === 'number' ? parseFloat($el.val()) : $el.val();
            this.updateBlockSetting($el.data('block-id'), $el.data('key'), val);
        });

        // Textarea changes
        this.$settings.find('textarea[data-key]').off('input').on('input', (e) => {
            const $el = $(e.target);
            this.updateBlockSetting($el.data('block-id'), $el.data('key'), $el.val());
        });

        // Column picker changes
        this.$settings.find('.be-column-picker input').off('change').on('change', (e) => {
            const $el = $(e.target);
            const blockId = $el.data('block-id');
            const settingKey = $el.data('key');

            // Gather all checked column keys
            const checked = [];
            this.$settings.find(`.be-column-picker input[data-key="${settingKey}"]`).each(function() {
                if (this.checked) checked.push($(this).data('col-key'));
            });

            this.updateBlockSetting(blockId, settingKey, checked);
        });

        // Field editor events (for blocks with hasFieldEditor)
        this._bindFieldEditorEvents();
    }

    // ── Preview ─────────────────────────────────────────────────

    _refreshPreview() {
        // Debounce preview updates
        clearTimeout(this._previewTimer);
        this._previewTimer = setTimeout(() => {
            this._doRefreshPreview();
        }, 400);
    }

    _doRefreshPreview() {
        if (this.blocks.length === 0) {
            this.$previewFrame.attr('srcdoc', '<p style="text-align:center;color:#aaa;padding:40px;">Add blocks to see preview</p>');
            return;
        }

        const config = this.getConfig();

        $.ajax({
            url: this.baseUrl + 'print-templates/preview-live/' + this.templateId,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(config),
            success: (response) => {
                if (response && response.html) {
                    this.$previewFrame.attr('srcdoc', response.html);
                }
            },
            error: () => {
                // Silently fail preview — not critical
            }
        });
    }

    openPreview() {
        window.open(this.baseUrl + 'print-templates/preview/' + this.templateId, '_blank');
    }

    // ── Save / Load ─────────────────────────────────────────────

    getConfig() {
        return {
            version: 1,
            blocks: this.blocks.map(b => ({
                id: b.id,
                type: b.type,
                label: b.label,             // Preserve block label (for custom blocks)
                enabled: b.enabled,
                order: b.order,
                settings: b.settings || {},
                custom_html: b.custom_html, // Include custom HTML in the config
                custom_css: b.custom_css,   // Include custom CSS for Style Manager persistence
                content: b.content,         // Preserve legacy content
                css: b.css                  // Preserve legacy css
            })),
            global_settings: {
                font_family: 'Arial, sans-serif'
            }
        };
    }

    save() {
        const config = this.getConfig();

        $('#be-btn-save').prop('disabled', true).html('⏳ Saving...');

        $.ajax({
            url: this.baseUrl + 'print-templates/save-design/' + this.templateId,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json', // Expect JSON response
            data: JSON.stringify(config),
            success: (response) => {
                if (response && response.success) {
                    this._clearDirty();
                    this._showToast('Template saved as new version!', 'success');

                    // If the server inserted a new versioned record, update templateId
                    // and redirect so subsequent saves target the new record
                    if (response.new_id && response.new_id != this.templateId) {
                        this.templateId = response.new_id;
                        // Replace the current browser URL without full page reload
                        const newUrl = this.baseUrl + 'print-templates/designer/' + response.new_id;
                        if (window.history && window.history.replaceState) {
                            window.history.replaceState({ templateId: response.new_id }, '', newUrl);
                        }
                    }
                } else {
                    this._showToast('Save failed: ' + (response.message || 'Unknown error'), 'error');
                }
            },
            error: (xhr) => {
                this._showToast('Save failed. Please try again.', 'error');
            },
            complete: () => {
                $('#be-btn-save').prop('disabled', false).html('💾 Save');
            }
        });
    }

    _loadDesign() {
        $.ajax({
            url: this.baseUrl + 'print-templates/load-design/' + this.templateId,
            type: 'GET',
            dataType: 'json',
            success: (data) => {
                if (data && data.blocks && data.blocks.length > 0) {
                    // New block editor format
                    this.blocks = data.blocks;
                    this._renderCanvas();
                    this._refreshPreview();
                } else if (data && (data['gjs-html'] || data['pages'])) {
                    // Legacy GrapesJS format — convert to a single HTML block
                    let legacyHtml = '';
                    let legacyCss = '';

                    if (data['gjs-html']) {
                        legacyHtml = data['gjs-html'];
                    } else if (data['pages'] && data['pages'].length > 0) {
                        // Extract HTML from GrapesJS pages structure
                        let page = data['pages'][0];
                        if (page && page.frames && page.frames.length > 0) {
                            let comps = page.frames[0].component;
                            if (comps && comps.components) {
                                // Attempt to serialize components to HTML
                                legacyHtml = this._gjsComponentsToHtml(comps.components);
                            }
                        }
                    }

                    if (data['gjs-css']) {
                        legacyCss = data['gjs-css'];
                    } else if (data['styles'] && data['styles'].length > 0) {
                        legacyCss = data['styles'].map(s => {
                            let selStr = (s.selectors || []).map(sel => typeof sel === 'string' ? sel : (sel.name || '')).join('');
                            let props = '';
                            if (s.style) {
                                props = Object.entries(s.style).map(([k,v]) => k + ':' + v).join(';');
                            }
                            return selStr ? selStr + '{' + props + '}' : '';
                        }).join('\n');
                    }

                    if (legacyHtml) {
                        // Set custom_html directly so Block Designer can open it
                        const fullHtml = (legacyCss ? '<style>' + legacyCss + '</style>' : '') + legacyHtml;
                        this.blocks = [{
                            id: 'legacy-block-1',
                            type: 'custom-html',
                            label: 'Legacy Design (Imported)',
                            custom_html: fullHtml,
                            content: legacyHtml,
                            css: legacyCss,
                            enabled: true,
                            order: 0,
                            settings: {}
                        }];
                        this._renderCanvas();
                        this._refreshPreview();
                        this._showToast('Legacy design loaded. Save to convert to new format.', 'info');
                    } else {
                        this._renderCanvas();
                    }
                } else {
                    // Empty template — show empty state
                    this._renderCanvas();
                }
            },
            error: () => {
                this._showToast('Failed to load template data', 'error');
                this._renderCanvas();
            }
        });
    }

    // Helper: Convert GrapesJS component tree to basic HTML string
    _gjsComponentsToHtml(components) {
        if (!components || !Array.isArray(components)) return '';

        return components.map(comp => {
            let tag = comp.tagName || 'div';
            let attrs = '';
            let style = '';

            // Build attributes
            if (comp.attributes) {
                Object.entries(comp.attributes).forEach(([k, v]) => {
                    if (k !== 'id') attrs += ' ' + k + '="' + v + '"';
                });
            }

            // Build inline style
            if (comp.style) {
                style = ' style="' + Object.entries(comp.style).map(([k,v]) => k + ':' + v).join(';') + '"';
            }

            // Content
            let inner = '';
            if (comp.type === 'textnode' || comp.type === 'text') {
                return comp.content || '';
            }
            if (comp.content) {
                inner = comp.content;
            }
            if (comp.components && comp.components.length > 0) {
                inner += this._gjsComponentsToHtml(comp.components);
            }

            // Self-closing tags
            if (['img', 'br', 'hr', 'input'].includes(tag)) {
                return '<' + tag + attrs + style + ' />';
            }

            return '<' + tag + attrs + style + '>' + inner + '</' + tag + '>';
        }).join('');
    }

    // ── Utilities ────────────────────────────────────────────────

    _markDirty() {
        this.isDirty = true;
        $('.be-toolbar-title .be-dirty-dot').remove();
        $('.be-toolbar-title').append('<span class="be-dirty-dot" title="Unsaved changes"> ●</span>');
    }

    _clearDirty() {
        this.isDirty = false;
        $('.be-dirty-dot').remove();
    }

    _showToast(message, type) {
        const $toast = $(`<div class="be-toast ${type}">${message}</div>`);
        $('body').append($toast);
        setTimeout(() => $toast.fadeOut(300, function() { $(this).remove(); }), 2500);
    }

    /**
     * Open the block designer modal for a specific block.
     */
    openBlockDesigner(blockId) {
        const block = this.blocks.find(b => b.id === blockId);
        if (!block) return;

        const def = BLOCK_DEFINITIONS[block.type];
        // Use existing custom HTML, or block.content for legacy custom-html blocks,
        // or fall back to default template for this type
        let currentHtml = block.custom_html || '';
        if (!currentHtml && block.type === 'custom-html' && block.content) {
            // Legacy imported block — use the content + css
            currentHtml = (block.css ? '<style>' + block.css + '</style>' : '') + block.content;
        }
        if (!currentHtml) {
            currentHtml = (this.blockTemplates && this.blockTemplates[block.type]) || '<div>No template found for this block type</div>';
        }
        
        // Find index for the callback
        const index = this.blocks.findIndex(b => b.id === blockId);

        // ALWAYS strip inline border styles from loaded HTML
        // Old templates have borders baked in. Users can add borders back via the Borders sector.
        // NOTE: Only strip auto-generated GrapesJS borders (1px dashed), not user-applied borders
        currentHtml = currentHtml
            .replace(/border:\s*1px\s+dashed\s+[^;"]+;?\s*/gi, '');

        // Get stored CSS for this block (for Style Manager persistence)
        const currentCss = block.custom_css || '';

        // Open the global BlockDesigner (defined in designer.php)
        if (window.BlockDesigner) {
            window.BlockDesigner.open(
                block.type, 
                def ? def.label : block.type, 
                currentHtml, 
                index,
                (newHtml, newCss) => {
                    this.updateBlockDesign(blockId, newHtml, newCss);
                },
                currentCss  // Pass stored CSS so Style Manager can reload it
            );
        } else {
            console.error('BlockDesigner not found');
        }
    }

    /**
     * Callback when a block design is saved from the modal.
     */
    updateBlockDesign(blockId, newHtml, newCss) {
        const block = this.blocks.find(b => b.id === blockId);
        if (block) {
            this._pushHistory();
            block.custom_html = newHtml; // Store custom HTML (with inline styles for rendering)
            block.custom_css = newCss || '';  // Store raw CSS (for Style Manager on re-open)
            // For custom-html blocks, also update content so preview works
            if (block.type === 'custom-html') {
                block.content = newHtml;
            }
            this._markDirty();
            this._showToast('Block design updated', 'success');
            // Re-render canvas cards to show updated preview
            this._renderCanvas();
            // Refresh live preview
            this._refreshPreview();
        }
    }
}

// ── Initialize on DOM Ready ─────────────────────────────────────

$(document).ready(function() {
    // These values are injected by designer.php
    if (typeof TEMPLATE_CONFIG !== 'undefined') {
        window.blockEditor = new BlockEditor(TEMPLATE_CONFIG);
    }
});
