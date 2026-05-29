/**
 * ============================================================
 * PRINT DESIGNER — Table Renderer
 * Handles:
 *  1. Quick Table Setup Wizard (column count, thead, tfoot)
 *  2. Full config modal (columns, styling, merges, footer)
 *  3. Fabric.js canvas preview object creation
 * ============================================================
 */
var TableRenderer = (function() {
    'use strict';

    var _editingObject  = null;  // Fabric.js object currently being configured
    var _tempConfig     = null;  // Temp config while modal is open
    var _dropX          = null;  // Canvas x position from drag
    var _dropY          = null;  // Canvas y position from drag

    // ─── Default Table Config ────────────────────────────────

    function _defaultConfig(numCols, showHeader, showFooter) {
        numCols    = numCols    || 3;
        showHeader = (showHeader !== false);
        showFooter = !!showFooter;

        var columns = [];
        for (var i = 1; i <= numCols; i++) {
            columns.push({ field: '', header: 'Column ' + i, width: 100, align: 'left' });
        }

        // Footer row cells (one per column)
        var footerRow = [];
        for (var j = 0; j < numCols; j++) {
            footerRow.push({ text: '' });
        }

        return {
            columns:      columns,
            showHeader:   showHeader,
            headerBg:     '#333333',
            headerColor:  '#ffffff',
            borderColor:  '#000000',
            borderWidth:  1,
            fontSize:     11,
            cellPadding:  4,
            mergedCells:  [],
            sourceTable:  '',
            sourceFilter: '',
            showFooter:   showFooter,
            footerRow:    footerRow,
            footerBg:     '#f0f0f0',
            footerColor:  '#000000'
        };
    }

    // ─── STEP 1: Quick Table Wizard ──────────────────────────

    /**
     * Show the quick-setup wizard before the full config modal.
     * @param {number|null} x  Canvas drop x (null = use default 50)
     * @param {number|null} y  Canvas drop y (null = use default 50)
     * @param {object|null} fabricObj  Existing fabric object (skip wizard if set)
     */
    function showQuickTableModal(x, y, fabricObj) {
        // If editing an existing table, skip wizard and go straight to config
        if (fabricObj) {
            openConfigModal(fabricObj);
            return;
        }

        _dropX = (x !== null && x !== undefined) ? x : 50;
        _dropY = (y !== null && y !== undefined) ? y : 50;

        // Reset wizard fields
        var colCount   = document.getElementById('pd-qt-col-count');
        var showHeader = document.getElementById('pd-qt-show-header');
        var showFooter = document.getElementById('pd-qt-show-footer');
        if (colCount)   colCount.value   = 3;
        if (showHeader) showHeader.checked = true;
        if (showFooter) showFooter.checked = false;

        // Sync thead option visual state
        _syncTheadOption();

        document.getElementById('pd-quick-table-modal').classList.add('visible');
    }

    function _syncTheadOption() {
        var showHeader = document.getElementById('pd-qt-show-header');
        var theadOption = document.getElementById('pd-qt-thead-option');
        if (!showHeader || !theadOption) return;
        if (showHeader.checked) {
            theadOption.classList.add('pd-quick-option-active');
        } else {
            theadOption.classList.remove('pd-quick-option-active');
        }
    }

    function _syncTfootOption() {
        var showFooter = document.getElementById('pd-qt-show-footer');
        var tfootOption = document.getElementById('pd-qt-tfoot-option');
        if (!showFooter || !tfootOption) return;
        if (showFooter.checked) {
            tfootOption.classList.add('pd-quick-option-active');
        } else {
            tfootOption.classList.remove('pd-quick-option-active');
        }
    }

    function _closeQuickModal() {
        document.getElementById('pd-quick-table-modal').classList.remove('visible');
    }

    function _continueFromWizard() {
        var numCols    = parseInt(document.getElementById('pd-qt-col-count').value) || 3;
        var showHeader = document.getElementById('pd-qt-show-header').checked;
        var showFooter = document.getElementById('pd-qt-show-footer').checked;

        numCols = Math.max(1, Math.min(20, numCols));

        _closeQuickModal();

        // Build fresh config with the wizard values
        _tempConfig = _defaultConfig(numCols, showHeader, showFooter);
        _editingObject = null;

        // Open the full config modal
        _openFullModal();
    }

    // ─── STEP 2: Full Config Modal ───────────────────────────

    function openConfigModal(fabricObj) {
        _editingObject = fabricObj || null;

        if (_editingObject && _editingObject.tableConfig) {
            _tempConfig = JSON.parse(JSON.stringify(_editingObject.tableConfig));
            // Ensure footerRow array matches column count
            _ensureFooterRowLength();
        } else {
            _tempConfig = _defaultConfig();
        }

        _openFullModal();
    }

    function _ensureFooterRowLength() {
        if (!_tempConfig) return;
        var numCols = _tempConfig.columns.length;
        if (!_tempConfig.footerRow) _tempConfig.footerRow = [];
        while (_tempConfig.footerRow.length < numCols) {
            _tempConfig.footerRow.push({ text: '' });
        }
        _tempConfig.footerRow.length = numCols; // trim if excess
    }

    function _openFullModal() {
        // Populate source table dropdown
        _populateSourceTableDropdown();

        // Set style/options form values
        document.getElementById('pd-tbl-font-size').value    = _tempConfig.fontSize;
        document.getElementById('pd-tbl-cell-padding').value = _tempConfig.cellPadding;
        document.getElementById('pd-tbl-border-width').value = _tempConfig.borderWidth;
        document.getElementById('pd-tbl-border-color').value = _tempConfig.borderColor;
        document.getElementById('pd-tbl-show-header').checked = _tempConfig.showHeader;
        document.getElementById('pd-tbl-header-bg').value    = _tempConfig.headerBg;
        document.getElementById('pd-tbl-header-color').value = _tempConfig.headerColor;
        document.getElementById('pd-tbl-source-filter').value = _tempConfig.sourceFilter || '';

        // Footer toggle
        var showFooterChk = document.getElementById('pd-tbl-show-footer');
        if (showFooterChk) {
            showFooterChk.checked = !!_tempConfig.showFooter;
            _toggleFooterSection(_tempConfig.showFooter);
        }

        // Render column rows
        _renderColumnRows();

        // Render merge rows
        _renderMergeRows();

        // Render footer row inputs
        _renderFooterRowInputs();

        // Show modal
        document.getElementById('pd-table-config-modal').classList.add('visible');
    }

    function _closeModal() {
        document.getElementById('pd-table-config-modal').classList.remove('visible');
        _editingObject = null;
        _tempConfig    = null;
    }

    function _toggleFooterSection(on) {
        var footerCols = document.getElementById('pd-tbl-footer-cols');
        if (footerCols) footerCols.style.display = on ? 'block' : 'none';
    }

    function _applyConfig() {
        // Read style values
        _tempConfig.fontSize     = parseInt(document.getElementById('pd-tbl-font-size').value) || 11;
        _tempConfig.cellPadding  = parseInt(document.getElementById('pd-tbl-cell-padding').value) || 4;
        _tempConfig.borderWidth  = parseInt(document.getElementById('pd-tbl-border-width').value) || 1;
        _tempConfig.borderColor  = document.getElementById('pd-tbl-border-color').value;
        _tempConfig.showHeader   = document.getElementById('pd-tbl-show-header').checked;
        _tempConfig.headerBg     = document.getElementById('pd-tbl-header-bg').value;
        _tempConfig.headerColor  = document.getElementById('pd-tbl-header-color').value;
        _tempConfig.sourceTable  = document.getElementById('pd-tbl-source-table').value;
        _tempConfig.sourceFilter = document.getElementById('pd-tbl-source-filter').value;
        _tempConfig.showFooter   = document.getElementById('pd-tbl-show-footer').checked;

        // Read columns from DOM
        _readColumnsFromDOM();

        // Read merges from DOM
        _readMergesFromDOM();

        // Read footer cells from DOM
        _readFooterCellsFromDOM();

        // Validate
        if (_tempConfig.columns.length === 0) {
            Designer.showToast('Add at least one column', 'error');
            return;
        }

        if (_editingObject) {
            _editingObject.tableConfig = JSON.parse(JSON.stringify(_tempConfig));
            _redrawTableOnCanvas(_editingObject);
        } else {
            _createTableObject(_tempConfig, _dropX || 50, _dropY || 50);
        }

        _closeModal();
        Designer.showToast('Table configuration applied', 'success');
    }

    // ─── Source Table Dropdown ───────────────────────────────

    function _populateSourceTableDropdown() {
        var select     = document.getElementById('pd-tbl-source-table');
        var mainSelect = document.getElementById('pd-table-select');

        select.innerHTML = '<option value="">— Same as template table —</option>';
        if (mainSelect) {
            mainSelect.querySelectorAll('option').forEach(function(opt) {
                if (opt.value) {
                    var newOpt = document.createElement('option');
                    newOpt.value       = opt.value;
                    newOpt.textContent = opt.textContent;
                    select.appendChild(newOpt);
                }
            });
        }

        if (_tempConfig.sourceTable) {
            select.value = _tempConfig.sourceTable;
        }

        select.onchange = function() {
            _tempConfig.sourceTable = this.value;
            _loadFieldOptionsForTable(this.value);
        };

        var sourceTable = _tempConfig.sourceTable || VariablesManager.getCurrentTable();
        if (sourceTable) {
            _loadFieldOptionsForTable(sourceTable);
        }
    }

    var _fieldOptions = [];

    function _loadFieldOptionsForTable(tableName) {
        if (!tableName) {
            _fieldOptions = [];
            return;
        }
        API.getVariables(tableName, function(variables) {
            _fieldOptions = variables || [];
            _renderColumnRows();
            _renderFooterRowInputs();
        });
    }

    // ─── Column Rows ─────────────────────────────────────────

    function _renderColumnRows() {
        var container = document.getElementById('pd-tbl-col-list');
        container.innerHTML = '';

        _tempConfig.columns.forEach(function(col, idx) {
            var row = _buildColumnRow(col, idx, false);
            container.appendChild(row);
        });
    }

    function _buildColumnRow(col, idx, isFooter) {
        var row = document.createElement('div');
        row.className  = 'pd-tbl-col-row';
        row.dataset.idx = idx;

        // Drag handle (columns only)
        if (!isFooter) {
            var handle = document.createElement('span');
            handle.className = 'pd-tbl-col-handle';
            handle.innerHTML = '<i class="fa fa-bars"></i>';
            row.appendChild(handle);
        }

        // Field select
        var fieldSelect = document.createElement('select');
        fieldSelect.className = 'pd-tbl-col-field';
        fieldSelect.innerHTML = '<option value="">— Custom —</option>';
        _fieldOptions.forEach(function(v) {
            var opt = document.createElement('option');
            opt.value       = v.tag;
            opt.textContent = v.column + ' (' + v.type + ')';
            if (col.field === v.tag) opt.selected = true;
            fieldSelect.appendChild(opt);
        });

        if (isFooter) {
            // Footer cell: free-text (not a DB field selector)
            row.appendChild(fieldSelect);
            fieldSelect.style.display = 'none';

            var footerInput = document.createElement('input');
            footerInput.type        = 'text';
            footerInput.className   = 'pd-tbl-col-header';
            footerInput.placeholder = 'Footer text or {{variable}}';
            footerInput.value       = col.text || '';
            footerInput.style.flex  = '2';
            footerInput.oninput = function() {
                _tempConfig.footerRow[idx].text = this.value;
            };
            row.appendChild(footerInput);
        } else {
            var headerInput = document.createElement('input');
            headerInput.type        = 'text';
            headerInput.className   = 'pd-tbl-col-header';
            headerInput.placeholder = 'Header text';
            headerInput.value       = col.header || '';

            fieldSelect.onchange = function() {
                _tempConfig.columns[idx].field = this.value;
                if (this.value && !_tempConfig.columns[idx].header) {
                    var colName = this.value.replace(/\{\{|\}\}/g, '');
                    headerInput.value = colName;
                    _tempConfig.columns[idx].header = colName;
                }
            };
            headerInput.oninput = function() {
                _tempConfig.columns[idx].header = this.value;
            };
            row.appendChild(fieldSelect);
            row.appendChild(headerInput);
        }

        // Width
        var widthInput = document.createElement('input');
        widthInput.type      = 'number';
        widthInput.className = 'pd-tbl-col-width';
        widthInput.placeholder = 'W';
        widthInput.title     = 'Width (px)';
        widthInput.value     = col.width || 100;
        widthInput.min       = 20;
        widthInput.max       = 500;
        widthInput.onchange  = function() {
            if (isFooter) {
                _tempConfig.footerRow[idx].width = parseInt(this.value) || 100;
            } else {
                _tempConfig.columns[idx].width = parseInt(this.value) || 100;
            }
        };
        row.appendChild(widthInput);

        // Alignment
        var alignSelect = document.createElement('select');
        alignSelect.className = 'pd-tbl-col-align';
        alignSelect.innerHTML = '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>';
        alignSelect.value     = col.align || 'left';
        alignSelect.onchange  = function() {
            if (isFooter) {
                _tempConfig.footerRow[idx].align = this.value;
            } else {
                _tempConfig.columns[idx].align = this.value;
            }
        };
        row.appendChild(alignSelect);

        // Remove button (columns only; footer rows are fixed-length)
        if (!isFooter) {
            var removeBtn = document.createElement('button');
            removeBtn.className = 'pd-tbl-col-remove';
            removeBtn.innerHTML = '<i class="fa fa-times"></i>';
            removeBtn.title     = 'Remove column';
            removeBtn.onclick   = function() {
                _tempConfig.columns.splice(idx, 1);
                _ensureFooterRowLength();
                _renderColumnRows();
                _renderFooterRowInputs();
            };
            row.appendChild(removeBtn);
        }

        return row;
    }

    function _readColumnsFromDOM() {
        var rows = document.querySelectorAll('#pd-tbl-col-list .pd-tbl-col-row');
        _tempConfig.columns = [];
        rows.forEach(function(row) {
            _tempConfig.columns.push({
                field:  (row.querySelector('.pd-tbl-col-field')  || {}).value  || '',
                header: (row.querySelector('.pd-tbl-col-header') || {}).value  || '',
                width:  parseInt((row.querySelector('.pd-tbl-col-width') || {}).value) || 100,
                align:  (row.querySelector('.pd-tbl-col-align')  || {}).value  || 'left'
            });
        });
    }

    function _addColumn() {
        _readColumnsFromDOM();
        _tempConfig.columns.push({ field: '', header: 'Column ' + (_tempConfig.columns.length + 1), width: 100, align: 'left' });
        _ensureFooterRowLength();
        _renderColumnRows();
        _renderFooterRowInputs();
    }

    // ─── Merge Rows ──────────────────────────────────────────

    function _renderMergeRows() {
        var container = document.getElementById('pd-tbl-merge-list');
        container.innerHTML = '';

        if (!_tempConfig.mergedCells || _tempConfig.mergedCells.length === 0) {
            container.innerHTML = '<div class="pd-tbl-merge-empty">No cell merges configured</div>';
            return;
        }

        _tempConfig.mergedCells.forEach(function(merge, idx) {
            var row = document.createElement('div');
            row.className = 'pd-tbl-merge-row';
            row.innerHTML =
                '<label>Row</label><input type="number" class="pd-tbl-merge-r" value="' + (merge.row || 0) + '" min="0" max="100">' +
                '<label>Col</label><input type="number" class="pd-tbl-merge-c" value="' + (merge.col || 0) + '" min="0" max="50">' +
                '<label>Colspan</label><input type="number" class="pd-tbl-merge-cs" value="' + (merge.colspan || 1) + '" min="1" max="20">' +
                '<label>Rowspan</label><input type="number" class="pd-tbl-merge-rs" value="' + (merge.rowspan || 1) + '" min="1" max="20">' +
                '<button class="pd-tbl-merge-remove" title="Remove"><i class="fa fa-times"></i></button>';

            row.querySelector('.pd-tbl-merge-remove').onclick = function() {
                _tempConfig.mergedCells.splice(idx, 1);
                _renderMergeRows();
            };
            container.appendChild(row);
        });
    }

    function _readMergesFromDOM() {
        var rows = document.querySelectorAll('#pd-tbl-merge-list .pd-tbl-merge-row');
        _tempConfig.mergedCells = [];
        rows.forEach(function(row) {
            _tempConfig.mergedCells.push({
                row:     parseInt(row.querySelector('.pd-tbl-merge-r').value)  || 0,
                col:     parseInt(row.querySelector('.pd-tbl-merge-c').value)  || 0,
                colspan: parseInt(row.querySelector('.pd-tbl-merge-cs').value) || 1,
                rowspan: parseInt(row.querySelector('.pd-tbl-merge-rs').value) || 1
            });
        });
    }

    function _addMerge() {
        _readMergesFromDOM();
        _tempConfig.mergedCells.push({ row: 0, col: 0, colspan: 2, rowspan: 1 });
        _renderMergeRows();
    }

    // ─── Footer Row Inputs ───────────────────────────────────

    function _renderFooterRowInputs() {
        var container = document.getElementById('pd-tbl-footer-col-list');
        if (!container) return;
        container.innerHTML = '';

        if (!_tempConfig.footerRow) _tempConfig.footerRow = [];
        _ensureFooterRowLength();

        _tempConfig.footerRow.forEach(function(cell, idx) {
            var colLabel = (_tempConfig.columns[idx] && _tempConfig.columns[idx].header)
                ? _tempConfig.columns[idx].header
                : ('Col ' + (idx + 1));

            var row = document.createElement('div');
            row.className = 'pd-tbl-col-row';

            var labelEl = document.createElement('span');
            labelEl.style.cssText = 'font-size:10px;color:var(--pd-text-dim);min-width:60px;font-weight:600;';
            labelEl.textContent   = colLabel + ':';
            row.appendChild(labelEl);

            var input = document.createElement('input');
            input.type        = 'text';
            input.className   = 'pd-tbl-col-header';
            input.placeholder = 'Footer text or {{variable}}';
            input.value       = cell.text || '';
            input.style.flex  = '1';
            input.oninput = function() {
                _tempConfig.footerRow[idx].text = this.value;
            };
            row.appendChild(input);

            var alignSelect = document.createElement('select');
            alignSelect.className = 'pd-tbl-col-align';
            alignSelect.innerHTML = '<option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>';
            alignSelect.value     = cell.align || 'left';
            alignSelect.onchange  = function() {
                _tempConfig.footerRow[idx].align = this.value;
            };
            row.appendChild(alignSelect);

            container.appendChild(row);
        });
    }

    function _readFooterCellsFromDOM() {
        // Already kept in sync via oninput handlers; just re-read for safety
        var inputs  = document.querySelectorAll('#pd-tbl-footer-col-list input[type="text"]');
        var selects = document.querySelectorAll('#pd-tbl-footer-col-list select');
        _tempConfig.footerRow = [];
        inputs.forEach(function(inp, i) {
            _tempConfig.footerRow.push({
                text:  inp.value,
                align: selects[i] ? selects[i].value : 'left'
            });
        });
    }

    // ─── Canvas Table Object (visual preview) ────────────────

    function _createTableObject(config, startX, startY) {
        var canvas   = Designer.getCanvas();
        var objects  = [];
        var totalW   = 0;
        var cols     = config.columns;

        cols.forEach(function(c) { totalW += c.width; });

        var rowH       = config.fontSize + (config.cellPadding * 2) + 4;
        var previewRows = 3;
        var totalRows  = (config.showHeader ? 1 : 0) + previewRows + (config.showFooter ? 1 : 0);
        var totalH     = totalRows * rowH;

        // Background
        objects.push(new fabric.Rect({
            left: 0, top: 0, width: totalW, height: totalH,
            fill: '#ffffff', stroke: config.borderColor,
            strokeWidth: config.borderWidth
        }));

        var curY = 0;

        // ── Header row ──
        if (config.showHeader) {
            objects.push(new fabric.Rect({
                left: 0, top: 0, width: totalW, height: rowH,
                fill: config.headerBg, stroke: config.borderColor,
                strokeWidth: config.borderWidth
            }));

            var curX = 0;
            cols.forEach(function(col) {
                objects.push(new fabric.Text(col.header || '—', {
                    left: curX + config.cellPadding, top: config.cellPadding,
                    fontSize: config.fontSize, fontFamily: 'Arial',
                    fontWeight: 'bold', fill: config.headerColor, selectable: false
                }));
                if (curX > 0) {
                    objects.push(new fabric.Line([curX, 0, curX, totalH], {
                        stroke: config.borderColor, strokeWidth: config.borderWidth, selectable: false
                    }));
                }
                curX += col.width;
            });
            curY += rowH;
        }

        // ── Sample data rows ──
        for (var r = 0; r < previewRows; r++) {
            objects.push(new fabric.Line([0, curY, totalW, curY], {
                stroke: config.borderColor, strokeWidth: config.borderWidth, selectable: false
            }));
            var curX2 = 0;
            cols.forEach(function(col) {
                var cellText = col.field ? col.field : '...';
                objects.push(new fabric.Text(cellText, {
                    left: curX2 + config.cellPadding, top: curY + config.cellPadding,
                    fontSize: config.fontSize, fontFamily: 'Arial',
                    fill: '#555555', selectable: false
                }));
                curX2 += col.width;
            });
            curY += rowH;
        }

        // ── Footer row ──
        if (config.showFooter && config.footerRow && config.footerRow.length) {
            objects.push(new fabric.Line([0, curY, totalW, curY], {
                stroke: config.borderColor, strokeWidth: config.borderWidth, selectable: false
            }));
            objects.push(new fabric.Rect({
                left: 0, top: curY, width: totalW, height: rowH,
                fill: config.footerBg || '#f0f0f0', stroke: config.borderColor,
                strokeWidth: config.borderWidth
            }));
            var curX3 = 0;
            config.footerRow.forEach(function(cell, i) {
                var cellTxt = cell.text || (cols[i] ? 'Total ' + (cols[i].header || '') : '');
                objects.push(new fabric.Text(cellTxt.substring(0, 18), {
                    left: curX3 + config.cellPadding, top: curY + config.cellPadding,
                    fontSize: config.fontSize, fontFamily: 'Arial',
                    fontWeight: 'bold', fill: config.footerColor || '#000',
                    selectable: false
                }));
                curX3 += (cols[i] ? cols[i].width : 100);
            });
            curY += rowH;
        }

        // ── "…dynamic rows…" indicator ──
        objects.push(new fabric.Text('⋮  dynamic rows from DB  ⋮', {
            left: totalW / 2, top: (config.showHeader ? rowH : 0) + config.cellPadding,
            fontSize: 9, fontFamily: 'Arial', fill: '#aaa',
            originX: 'center', selectable: false
        }));

        var group = new fabric.Group(objects, {
            left: startX, top: startY,
            subTargetCheck: false
        });
        group.customType  = 'data-table';
        group.tableConfig = JSON.parse(JSON.stringify(config));

        canvas.add(group);
        canvas.setActiveObject(group);
        canvas.renderAll();
        return group;
    }

    function _redrawTableOnCanvas(fabricObj) {
        var canvas = Designer.getCanvas();
        var left   = fabricObj.left;
        var top    = fabricObj.top;
        var config = fabricObj.tableConfig;

        canvas.remove(fabricObj);
        _createTableObject(config, left, top);
    }

    // ─── Init (bind modal buttons) ───────────────────────────

    function init() {
        // ── Quick wizard buttons ──
        var qtCancel = document.getElementById('pd-qt-cancel');
        if (qtCancel) qtCancel.addEventListener('click', _closeQuickModal);

        var qtContinue = document.getElementById('pd-qt-continue');
        if (qtContinue) qtContinue.addEventListener('click', _continueFromWizard);

        // Sync visual states on checkbox change
        var qtShowHeader = document.getElementById('pd-qt-show-header');
        if (qtShowHeader) qtShowHeader.addEventListener('change', _syncTheadOption);

        var qtShowFooter = document.getElementById('pd-qt-show-footer');
        if (qtShowFooter) qtShowFooter.addEventListener('change', _syncTfootOption);

        // ── Full config modal buttons ──
        var addColBtn = document.getElementById('pd-tbl-add-col');
        if (addColBtn) addColBtn.addEventListener('click', _addColumn);

        var addMergeBtn = document.getElementById('pd-tbl-add-merge');
        if (addMergeBtn) addMergeBtn.addEventListener('click', _addMerge);

        var cancelBtn = document.getElementById('pd-tbl-cfg-cancel');
        if (cancelBtn) cancelBtn.addEventListener('click', _closeModal);

        var applyBtn = document.getElementById('pd-tbl-cfg-apply');
        if (applyBtn) applyBtn.addEventListener('click', _applyConfig);

        // Footer toggle
        var showFooterChk = document.getElementById('pd-tbl-show-footer');
        if (showFooterChk) {
            showFooterChk.addEventListener('change', function() {
                _tempConfig.showFooter = this.checked;
                _toggleFooterSection(this.checked);
                if (this.checked) _renderFooterRowInputs();
            });
        }
    }

    // ─── Public API ──────────────────────────────────────────

    return {
        init:               init,
        openConfigModal:    openConfigModal,
        showQuickTableModal: showQuickTableModal,
        createNewTable: function(x, y) {
            showQuickTableModal(x, y, null);
        }
    };

})();
