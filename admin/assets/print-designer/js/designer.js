/**
 * ============================================================
 * PRINT DESIGNER — Core Designer (Fabric.js Canvas)
 * Canvas management, objects, undo/redo, snap, z-index
 * ============================================================
 */
var Designer = (function() {
    'use strict';

    // ─── Paper Sizes (in mm) ────────────────────────────────
    var PAPER_SIZES = {
        'A4':      { w: 210,  h: 297 },
        'A3':      { w: 297,  h: 420 },
        'A5':      { w: 148,  h: 210 },
        'Letter':  { w: 216,  h: 279 },
        'Legal':   { w: 216,  h: 356 },
        '80mm':    { w: 80,   h: 297 },
        '58mm':    { w: 58,   h: 210 },
        'Custom':  { w: 210,  h: 297 }
    };

    var MM_TO_PX = 3.7795275591; // 1mm = 3.78px at 96dpi

    var _canvas = null;
    var _currentTemplateId = null;
    var _tableContext = '';
    var _gridEnabled = false;
    var _gridSize = 10;
    var _undoStack = [];
    var _redoStack = [];
    var _undoLock = false;
    var _pageSize = 'A4';
    var _orientation = 'portrait';
    var _customW = 210;
    var _customH = 297;
    var _zoomLevel = 1.0;

    // ─── Init ───────────────────────────────────────────────

    function init() {
        console.log('Designer: init starting...');
        _createCanvas();
        _bindToolbar();
        _bindElementButtons();
        _bindContextBar();
        _bindKeyboard();
        _bindCanvasDrop();
        _bindSidebarTabs();
        _bindTemplateActions();
        _applyPageSize();
        _saveUndoState();

        // Init sub-modules
        console.log('Designer: initializing sub-modules...');
        VariablesManager.init();
        TableRenderer.init();

        // Ensure tables can always be double-clicked, even if loaded from JSON
        _canvas.on('mouse:dblclick', function(e) {
            if (e.target && e.target.customType === 'data-table') {
                if (typeof TableRenderer !== 'undefined' && TableRenderer.openConfigModal) {
                    TableRenderer.openConfigModal(e.target);
                }
            }
        });

        // Selection change for context bar
        _canvas.on('selection:created', _updateContextBar);
        _canvas.on('selection:updated', _updateContextBar);
        _canvas.on('selection:cleared', _clearContextBar);

        // Load template list
        _refreshTemplateList();

        showToast('Designer ready', 'info');
    }

    function _createCanvas() {
        _canvas = new fabric.Canvas('pd-canvas', {
            backgroundColor: '#ffffff',
            selection: true,
            preserveObjectStacking: true
        });

        // Snap to grid
        _canvas.on('object:moving', function(e) {
            if (_gridEnabled) {
                var obj = e.target;
                obj.set({
                    left: Math.round(obj.left / _gridSize) * _gridSize,
                    top: Math.round(obj.top / _gridSize) * _gridSize
                });
            }
        });
    }

    // ─── Page Size ──────────────────────────────────────────

    function _applyPageSize() {
        var size = PAPER_SIZES[_pageSize] || PAPER_SIZES['A4'];
        var w, h;

        if (_pageSize === 'Custom') {
            w = _customW;
            h = _customH;
        } else {
            w = size.w;
            h = size.h;
        }

        if (_orientation === 'landscape') {
            var tmp = w; w = h; h = tmp;
        }

        var canvasW = Math.round(w * MM_TO_PX);
        var canvasH = Math.round(h * MM_TO_PX);

        _canvas.setWidth(canvasW);
        _canvas.setHeight(canvasH);
        _canvas.renderAll();

        // Update status bar
        var sizeDisplay = document.getElementById('pd-status-size');
        if (sizeDisplay) {
            sizeDisplay.textContent = w + 'mm × ' + h + 'mm (' + canvasW + '×' + canvasH + 'px)';
        }
    }

    // ─── Zoom ───────────────────────────────────────────────

    function _applyZoom() {
        if (!_canvas) return;

        _canvas.setZoom(_zoomLevel);
        
        // Resize the actual canvas element to trigger container scrollbars
        var size = PAPER_SIZES[_pageSize] || PAPER_SIZES['A4'];
        var w = (_pageSize === 'Custom') ? _customW : size.w;
        var h = (_pageSize === 'Custom') ? _customH : size.h;
        if (_orientation === 'landscape') { var t = w; w = h; h = t; }

        _canvas.setWidth(Math.round(w * MM_TO_PX * _zoomLevel));
        _canvas.setHeight(Math.round(h * MM_TO_PX * _zoomLevel));
        _canvas.renderAll();

        var zoomDisplay = document.getElementById('pd-zoom-level');
        if (zoomDisplay) zoomDisplay.textContent = Math.round(_zoomLevel * 100) + '%';
    }

    function _zoomIn() {
        if (_zoomLevel < 3.0) {
            _zoomLevel += 0.1;
            _applyZoom();
        }
    }

    function _zoomOut() {
        if (_zoomLevel > 0.2) {
            _zoomLevel -= 0.1;
            _applyZoom();
        }
    }

    function _zoomReset() {
        _zoomLevel = 1.0;
        _applyZoom();
    }

    // ─── Toolbar Bindings ───────────────────────────────────

    function _bindToolbar() {
        // Page size
        var sizeSelect = document.getElementById('pd-page-size');
        if (sizeSelect) {
            sizeSelect.addEventListener('change', function() {
                _pageSize = this.value;
                var customFields = document.getElementById('pd-custom-size');
                if (customFields) {
                    customFields.style.display = _pageSize === 'Custom' ? 'flex' : 'none';
                }
                _applyPageSize();
            });
        }

        // Zoom buttons
        var btnIn = document.getElementById('pd-zoom-in');
        var btnOut = document.getElementById('pd-zoom-out');
        var btnReset = document.getElementById('pd-zoom-reset');
        if (btnIn) btnIn.addEventListener('click', _zoomIn);
        if (btnOut) btnOut.addEventListener('click', _zoomOut);
        if (btnReset) btnReset.addEventListener('click', _zoomReset);

        // Mouse wheel zoom
        _canvas.on('mouse:wheel', function(opt) {
            if (opt.e.ctrlKey) {
                var delta = opt.e.deltaY;
                var zoom = _zoomLevel;
                zoom *= 0.999 ** delta;
                if (zoom > 3) zoom = 3;
                if (zoom < 0.1) zoom = 0.1;
                _zoomLevel = zoom;
                _applyZoom();
                opt.e.preventDefault();
                opt.e.stopPropagation();
            }
        });

        // Orientation
        var orientSelect = document.getElementById('pd-orientation');
        if (orientSelect) {
            orientSelect.addEventListener('change', function() {
                _orientation = this.value;
                _applyPageSize();
            });
        }

        // Custom dimensions
        var cwInput = document.getElementById('pd-custom-w');
        var chInput = document.getElementById('pd-custom-h');
        if (cwInput) cwInput.addEventListener('change', function() { _customW = parseFloat(this.value) || 210; _applyPageSize(); });
        if (chInput) chInput.addEventListener('change', function() { _customH = parseFloat(this.value) || 297; _applyPageSize(); });

        // Grid toggle
        var gridBtn = document.getElementById('pd-grid-toggle');
        if (gridBtn) {
            gridBtn.addEventListener('click', function() {
                _gridEnabled = !_gridEnabled;
                this.classList.toggle('active', _gridEnabled);
                _drawGrid();
            });
        }

        // Undo / Redo
        var undoBtn = document.getElementById('pd-undo');
        var redoBtn = document.getElementById('pd-redo');
        if (undoBtn) undoBtn.addEventListener('click', undo);
        if (redoBtn) redoBtn.addEventListener('click', redo);

        // Export PDF
        var pdfBtn = document.getElementById('pd-export-pdf');
        if (pdfBtn) pdfBtn.addEventListener('click', _exportPDF);

        // Preview PDF
        var previewBtn = document.getElementById('pd-preview');
        if (previewBtn) previewBtn.addEventListener('click', _previewPDF);

        // Preview toggle
        var previewToggleBtn = document.getElementById('pd-toggle-preview');
        if (previewToggleBtn) {
            previewToggleBtn.addEventListener('click', function() {
                var sidebar = document.querySelector('.pd-sidebar-right');
                if (sidebar) sidebar.classList.toggle('collapsed');
            });
        }

        // Save
        var saveBtn = document.getElementById('pd-save');
        if (saveBtn) saveBtn.addEventListener('click', function() { _showSaveModal(); });

        // New
        var newBtn = document.getElementById('pd-new');
        if (newBtn) newBtn.addEventListener('click', function() { _showSaveModal(true); });

        // Close designer
        var closeBtn = document.getElementById('pd-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                if (confirm('Close the designer? Unsaved changes will be lost.')) {
                    window.history.back();
                }
            });
        }
    }

    // ─── Element Buttons ────────────────────────────────────

    function _bindElementButtons() {
        // Add text
        _bindElementItem('pd-add-text', function() {
            var text = new fabric.Textbox('', {
                left: 50, top: 50, width: 200,
                fontSize: 14, fontFamily: 'Arial',
                fill: '#000000', editable: true,
                placeholder: 'Type here...'
            });
            _canvas.add(text);
            _canvas.setActiveObject(text);
            _canvas.renderAll();
        });

        // Add heading
        _bindElementItem('pd-add-heading', function() {
            var text = new fabric.Textbox('Heading', {
                left: 50, top: 50, width: 300,
                fontSize: 24, fontFamily: 'Arial',
                fontWeight: 'bold', fill: '#000000', editable: true
            });
            _canvas.add(text);
            _canvas.setActiveObject(text);
            _canvas.renderAll();
        });

        // Add rectangle
        _bindElementItem('pd-add-rect', function() {
            var rect = new fabric.Rect({
                left: 50, top: 50, width: 150, height: 80,
                fill: 'transparent', stroke: '#000000', strokeWidth: 1
            });
            _canvas.add(rect);
            _canvas.setActiveObject(rect);
            _canvas.renderAll();
        });

        // Add line (solid)
        _bindElementItem('pd-add-line', function() {
            var line = new fabric.Line([0, 0, 300, 0], {
                left: 50, top: 100,
                stroke: '#000000', strokeWidth: 1
            });
            line.lineStyle = 'solid';
            _canvas.add(line);
            _canvas.setActiveObject(line);
            _canvas.renderAll();
        });

        // Add dashed line
        _bindElementItem('pd-add-line-dashed', function() {
            var line = new fabric.Line([0, 0, 300, 0], {
                left: 50, top: 100,
                stroke: '#000000', strokeWidth: 1,
                strokeDashArray: [10, 6]
            });
            line.lineStyle = 'dashed';
            _canvas.add(line);
            _canvas.setActiveObject(line);
            _canvas.renderAll();
        });

        // Add dotted line
        _bindElementItem('pd-add-line-dotted', function() {
            var line = new fabric.Line([0, 0, 300, 0], {
                left: 50, top: 100,
                stroke: '#000000', strokeWidth: 2,
                strokeDashArray: [2, 6]
            });
            line.lineStyle = 'dotted';
            _canvas.add(line);
            _canvas.setActiveObject(line);
            _canvas.renderAll();
        });

        // Add dash-dot line
        _bindElementItem('pd-add-line-dashdot', function() {
            var line = new fabric.Line([0, 0, 300, 0], {
                left: 50, top: 100,
                stroke: '#000000', strokeWidth: 1,
                strokeDashArray: [10, 5, 2, 5]
            });
            line.lineStyle = 'dashdot';
            _canvas.add(line);
            _canvas.setActiveObject(line);
            _canvas.renderAll();
        });

        // Add image placeholder
        _bindElementItem('pd-add-image', function() {
            var rect = new fabric.Rect({
                left: 50, top: 50, width: 120, height: 120,
                fill: '#f0f0f0', stroke: '#ccc', strokeWidth: 1,
                rx: 4, ry: 4
            });
            var label = new fabric.Text('Image', {
                fontSize: 12, fill: '#999',
                originX: 'center', originY: 'center'
            });
            var group = new fabric.Group([rect, label], {
                left: 50, top: 50,
                subTargetCheck: false
            });
            group.customType = 'image-placeholder';
            _canvas.add(group);
            _canvas.setActiveObject(group);
        });

        // Add table grid — route through the quick wizard
        _bindElementItem('pd-add-table', function() {
            TableRenderer.showQuickTableModal(null, null);
        });

        // Delete selected
        var delBtn = document.getElementById('pd-delete');
        if (delBtn) delBtn.addEventListener('click', function() {
            var active = _canvas.getActiveObject();
            if (active) {
                _canvas.remove(active);
                _canvas.discardActiveObject();
                _canvas.renderAll();
            }
        });
    }

    function _bindElementItem(id, action) {
        var el = document.getElementById(id);
        if (!el) return;
        
        // Click to add
        el.addEventListener('click', action);
        
        // Drag to add
        el.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('pd-type', el.dataset.type);
            e.dataTransfer.effectAllowed = 'copy';
            el.style.opacity = '0.5';
        });
        el.addEventListener('dragend', function() {
            el.style.opacity = '1';
        });
    }

    // ─── Table Grid Helper ──────────────────────────────────

    function _addTableGrid(rows, cols, startX, startY) {
        var cellW = 100, cellH = 25;
        var objects = [];

        for (var r = 0; r <= rows; r++) {
            var line = new fabric.Line(
                [0, r * cellH, cols * cellW, r * cellH],
                { stroke: '#000', strokeWidth: 1, selectable: false }
            );
            objects.push(line);
        }
        for (var c = 0; c <= cols; c++) {
            var line = new fabric.Line(
                [c * cellW, 0, c * cellW, rows * cellH],
                { stroke: '#000', strokeWidth: 1, selectable: false }
            );
            objects.push(line);
        }

        var group = new fabric.Group(objects, {
            left: startX, top: startY
        });
        group.customType = 'table-grid';
        _canvas.add(group);
        _canvas.setActiveObject(group);
    }

    // ─── Context Bar (Font Controls) ────────────────────────

    function _bindContextBar() {
        _bindCtx('pd-ctx-font-family', 'change', function(v) { _setActiveProp('fontFamily', v); });
        _bindCtx('pd-ctx-font-size', 'change', function(v) { _setActiveProp('fontSize', parseInt(v)); });
        _bindCtx('pd-ctx-font-color', 'input', function(v) { _setActiveProp('fill', v); });
        _bindCtx('pd-ctx-bold', 'click', function() { _toggleActiveProp('fontWeight', 'bold', 'normal'); });
        _bindCtx('pd-ctx-italic', 'click', function() { _toggleActiveProp('fontStyle', 'italic', 'normal'); });
        _bindCtx('pd-ctx-underline', 'click', function() { _toggleActiveProp('underline', true, false); });
        _bindCtx('pd-ctx-align-left', 'click', function() { _setActiveProp('textAlign', 'left'); });
        _bindCtx('pd-ctx-align-center', 'click', function() { _setActiveProp('textAlign', 'center'); });
        _bindCtx('pd-ctx-align-right', 'click', function() { _setActiveProp('textAlign', 'right'); });
        _bindCtx('pd-ctx-stroke-color', 'input', function(v) { _setActiveProp('stroke', v); });
        _bindCtx('pd-ctx-stroke-width', 'change', function(v) { _setActiveProp('strokeWidth', parseInt(v)); });

        // ── Line style dropdown ──────────────────────────────
        var lineStyleSel = document.getElementById('pd-ctx-line-style');
        if (lineStyleSel) {
            lineStyleSel.addEventListener('change', function() {
                var obj = _canvas.getActiveObject();
                if (!obj || obj.type !== 'line') return;
                var style = this.value;
                var dashMap = {
                    'solid':   null,
                    'dashed':  [10, 6],
                    'dotted':  [2, 6],
                    'dashdot': [10, 5, 2, 5]
                };
                obj.set('strokeDashArray', dashMap[style] || null);
                obj.lineStyle = style;
                _canvas.renderAll();
                _saveUndoState();
            });
        }

        // Z-index controls
        _bindCtx('pd-ctx-bring-front', 'click', function() { var o = _canvas.getActiveObject(); if(o) { _canvas.bringToFront(o); _canvas.renderAll(); } });
        _bindCtx('pd-ctx-send-back', 'click', function() { var o = _canvas.getActiveObject(); if(o) { _canvas.sendToBack(o); _canvas.renderAll(); } });
        _bindCtx('pd-ctx-bring-forward', 'click', function() { var o = _canvas.getActiveObject(); if(o) { _canvas.bringForward(o); _canvas.renderAll(); } });
        _bindCtx('pd-ctx-send-backward', 'click', function() { var o = _canvas.getActiveObject(); if(o) { _canvas.sendBackwards(o); _canvas.renderAll(); } });
    }

    function _bindCtx(id, event, handler) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener(event, function() {
                var val = (this.tagName === 'SELECT' || this.tagName === 'INPUT') ? this.value : null;
                handler(val);
            });
        }
    }

    function _setActiveProp(prop, value) {
        var obj = _canvas.getActiveObject();
        if (!obj) return;
        obj.set(prop, value);
        _canvas.renderAll();
        _saveUndoState();
    }

    function _toggleActiveProp(prop, val1, val2) {
        var obj = _canvas.getActiveObject();
        if (!obj) return;
        obj.set(prop, obj[prop] === val1 ? val2 : val1);
        _canvas.renderAll();
        _saveUndoState();
    }

    function _updateContextBar() {
        var obj = _canvas.getActiveObject();
        if (!obj) return;

        _setCtxValue('pd-ctx-font-family', obj.fontFamily);
        _setCtxValue('pd-ctx-font-size', obj.fontSize);
        _setCtxValue('pd-ctx-font-color', obj.fill);
        _setCtxValue('pd-ctx-stroke-color', obj.stroke);
        _setCtxValue('pd-ctx-stroke-width', obj.strokeWidth);

        var textControls  = document.getElementById('pd-ctx-text-controls');
        var shapeControls = document.getElementById('pd-ctx-shape-controls');
        var lineControls  = document.getElementById('pd-ctx-line-controls');
        var isText = (obj.type === 'textbox' || obj.type === 'i-text' || obj.type === 'text');
        var isLine = (obj.type === 'line');

        if (textControls)  textControls.style.display  = isText ? 'flex' : 'none';
        if (shapeControls) shapeControls.style.display = (!isText) ? 'flex' : 'none';
        if (lineControls)  lineControls.style.display  = isLine ? 'flex' : 'none';

        // Restore line style dropdown selection
        if (isLine) {
            var lineStyleSel = document.getElementById('pd-ctx-line-style');
            if (lineStyleSel) {
                lineStyleSel.value = obj.lineStyle || 'solid';
            }
        }
    }

    function _clearContextBar() {
        var textControls  = document.getElementById('pd-ctx-text-controls');
        var shapeControls = document.getElementById('pd-ctx-shape-controls');
        var lineControls  = document.getElementById('pd-ctx-line-controls');
        if (textControls)  textControls.style.display  = 'none';
        if (shapeControls) shapeControls.style.display = 'none';
        if (lineControls)  lineControls.style.display  = 'none';
    }

    function _setCtxValue(id, val) {
        var el = document.getElementById(id);
        if (el && val !== undefined && val !== null) el.value = val;
    }

    // ─── Canvas Drop Zone ───────────────────────────────────

    function _bindCanvasDrop() {
        var wrapper = document.querySelector('.pd-canvas-wrapper');
        if (!wrapper) return;

        wrapper.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        });

        wrapper.addEventListener('drop', function(e) {
            e.preventDefault();
            var canvasEl = _canvas.getElement();
            var rect = canvasEl.getBoundingClientRect();
            var x = e.clientX - rect.left;
            var y = e.clientY - rect.top;

            // 1. Check for variable drop
            var varTag = e.dataTransfer.getData('text/plain');
            if (varTag && varTag.match(/^\{\{.+\}\}$/)) {
                _addDroppedElement('variable', x, y, varTag);
                return;
            }

            // 2. Check for static element drop
            var type = e.dataTransfer.getData('pd-type');
            if (type) {
                _addDroppedElement(type, x, y);
            }
        });
    }

    function _addDroppedElement(type, x, y, data) {
        var obj = null;
        switch(type) {
            case 'variable':
                obj = new fabric.Textbox(data, {
                    left: x, top: y, width: 180,
                    fontSize: 14, fontFamily: 'Arial',
                    fill: '#6366f1', editable: true
                });
                break;
            case 'text':
                obj = new fabric.Textbox('Type here...', {
                    left: x, top: y, width: 200,
                    fontSize: 14, fontFamily: 'Arial',
                    fill: '#000000', editable: true
                });
                break;
            case 'heading':
                obj = new fabric.Textbox('Heading', {
                    left: x, top: y, width: 300,
                    fontSize: 24, fontFamily: 'Arial',
                    fontWeight: 'bold', fill: '#000000', editable: true
                });
                break;
            case 'rect':
                obj = new fabric.Rect({
                    left: x, top: y, width: 150, height: 80,
                    fill: 'transparent', stroke: '#000000', strokeWidth: 1
                });
                break;
            case 'line':
                obj = new fabric.Line([0, 0, 300, 0], {
                    left: x, top: y,
                    stroke: '#000000', strokeWidth: 1
                });
                obj.lineStyle = 'solid';
                break;
            case 'line-dashed':
                obj = new fabric.Line([0, 0, 300, 0], {
                    left: x, top: y,
                    stroke: '#000000', strokeWidth: 1,
                    strokeDashArray: [10, 6]
                });
                obj.lineStyle = 'dashed';
                break;
            case 'line-dotted':
                obj = new fabric.Line([0, 0, 300, 0], {
                    left: x, top: y,
                    stroke: '#000000', strokeWidth: 2,
                    strokeDashArray: [2, 6]
                });
                obj.lineStyle = 'dotted';
                break;
            case 'line-dashdot':
                obj = new fabric.Line([0, 0, 300, 0], {
                    left: x, top: y,
                    stroke: '#000000', strokeWidth: 1,
                    strokeDashArray: [10, 5, 2, 5]
                });
                obj.lineStyle = 'dashdot';
                break;
            case 'image':
                var r = new fabric.Rect({
                    width: 120, height: 120, fill: '#f0f0f0', stroke: '#ccc', strokeWidth: 1, rx: 4, ry: 4
                });
                var l = new fabric.Text('Image', { fontSize: 12, fill: '#999', originX: 'center', originY: 'center', left: 60, top: 60 });
                obj = new fabric.Group([r, l], { left: x, top: y, customType: 'image-placeholder' });
                break;
            case 'table':
                TableRenderer.showQuickTableModal(x, y);
                return;
        }

        if (obj) {
            _canvas.add(obj);
            _canvas.setActiveObject(obj);
            _canvas.renderAll();
            _saveUndoState();
        }
    }

    // ─── Sidebar Tabs ───────────────────────────────────────

    function _bindSidebarTabs() {
        var tabs = document.querySelectorAll('.pd-sidebar-tab');
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                tabs.forEach(function(t) { t.classList.remove('active'); });
                this.classList.add('active');

                var target = this.dataset.tab;
                document.querySelectorAll('.pd-tab-panel').forEach(function(p) {
                    p.classList.remove('active');
                });
                var panel = document.getElementById(target);
                if (panel) panel.classList.add('active');
            });
        });
    }

    // ─── Grid ───────────────────────────────────────────────

    function _drawGrid() {
        // Remove existing grid lines
        var objects = _canvas.getObjects();
        for (var i = objects.length - 1; i >= 0; i--) {
            if (objects[i].isGrid) _canvas.remove(objects[i]);
        }

        if (!_gridEnabled) { _canvas.renderAll(); return; }

        var w = _canvas.getWidth();
        var h = _canvas.getHeight();
        var gridPx = _gridSize * MM_TO_PX;

        for (var x = gridPx; x < w; x += gridPx) {
            var line = new fabric.Line([x, 0, x, h], {
                stroke: '#e0e0e0', strokeWidth: 0.5,
                selectable: false, evented: false, excludeFromExport: true
            });
            line.isGrid = true;
            _canvas.add(line);
            _canvas.sendToBack(line);
        }
        for (var y = gridPx; y < h; y += gridPx) {
            var line = new fabric.Line([0, y, w, y], {
                stroke: '#e0e0e0', strokeWidth: 0.5,
                selectable: false, evented: false, excludeFromExport: true
            });
            line.isGrid = true;
            _canvas.add(line);
            _canvas.sendToBack(line);
        }
        _canvas.renderAll();
    }

    // ─── Undo / Redo ────────────────────────────────────────

    function _saveUndoState() {
        if (_undoLock) return;
        var json = JSON.stringify(_canvas.toJSON(['customType', 'tableConfig', 'lineStyle']));
        _undoStack.push(json);
        if (_undoStack.length > 50) _undoStack.shift();
        _redoStack = [];
        _updateUndoButtons();
    }

    function undo() {
        if (_undoStack.length <= 1) return;
        _undoLock = true;
        _redoStack.push(_undoStack.pop());
        var state = _undoStack[_undoStack.length - 1];
        _canvas.loadFromJSON(state, function() {
            _canvas.renderAll();
            _undoLock = false;
            _updateUndoButtons();
        }, function(o, object) {
            if (o.customType) object.customType = o.customType;
            if (o.tableConfig) object.tableConfig = o.tableConfig;
            if (o.lineStyle)   object.lineStyle   = o.lineStyle;
        });
    }

    function redo() {
        if (!_redoStack.length) return;
        _undoLock = true;
        var state = _redoStack.pop();
        _undoStack.push(state);
        _canvas.loadFromJSON(state, function() {
            _canvas.renderAll();
            _undoLock = false;
            _updateUndoButtons();
        }, function(o, object) {
            if (o.customType) object.customType = o.customType;
            if (o.tableConfig) object.tableConfig = o.tableConfig;
            if (o.lineStyle)   object.lineStyle   = o.lineStyle;
        });
    }

    function _updateUndoButtons() {
        var undoBtn = document.getElementById('pd-undo');
        var redoBtn = document.getElementById('pd-redo');
        if (undoBtn) undoBtn.disabled = _undoStack.length <= 1;
        if (redoBtn) redoBtn.disabled = !_redoStack.length;
    }

    // ─── Keyboard Shortcuts ─────────────────────────────────

    function _bindKeyboard() {
        document.addEventListener('keydown', function(e) {
            // Only if designer is active
            if (!document.querySelector('.pd-app')) return;

            if (e.ctrlKey && e.key === 'z') { e.preventDefault(); undo(); }
            if (e.ctrlKey && e.key === 'y') { e.preventDefault(); redo(); }
            if (e.ctrlKey && e.key === 's') { e.preventDefault(); _showSaveModal(); }
            if (e.key === 'Delete' || e.key === 'Backspace') {
                var active = _canvas.getActiveObject();
                if (active && !active.isEditing) {
                    e.preventDefault();
                    _canvas.remove(active);
                    _canvas.discardActiveObject();
                    _canvas.renderAll();
                }
            }
        });
    }

    // ─── Canvas Change Handler ──────────────────────────────

    function _onCanvasChange() {
        _saveUndoState();
        // Side preview disabled
        /*
        clearTimeout(_onCanvasChange._timer);
        _onCanvasChange._timer = setTimeout(function() {
            PreviewEngine.refreshPreview();
        }, 150);
        */
    }

    // ─── Save / Load ────────────────────────────────────────

    function _showSaveModal(forceNew) {
        var overlay = document.getElementById('pd-save-modal');
        if (!overlay) return;

        var nameInput = document.getElementById('pd-save-name');
        var idInput = document.getElementById('pd-save-id');

        if (forceNew || !_currentTemplateId) {
            if (nameInput) nameInput.value = '';
            if (idInput) idInput.value = '';
        } else {
            if (idInput) idInput.value = _currentTemplateId;
        }

        overlay.classList.add('visible');

        // Save button
        var saveBtn = document.getElementById('pd-save-confirm');
        if (saveBtn) {
            saveBtn.onclick = function() {
                _doSave();
            };
        }

        // Cancel
        var cancelBtn = document.getElementById('pd-save-cancel');
        if (cancelBtn) {
            cancelBtn.onclick = function() {
                overlay.classList.remove('visible');
            };
        }
    }

    function _doSave() {
        var name = document.getElementById('pd-save-name').value.trim();
        var id = document.getElementById('pd-save-id').value;
        var tableName = VariablesManager.getCurrentTable();

        if (!name) {
            showToast('Please enter a template name', 'error');
            return;
        }
        if (!tableName) {
            showToast('Please select a table first', 'error');
            return;
        }

        // Get canvas JSON (exclude grid lines)
        var canvasJSON = _canvas.toJSON(['customType', 'tableConfig', 'lineStyle']);
        canvasJSON.objects = canvasJSON.objects.filter(function(o) { return !o.isGrid; });

        var data = {
            id: id || null,
            name: name,
            table_name: tableName,
            layout_json: JSON.stringify(canvasJSON),
            page_size: _pageSize,
            orientation: _orientation,
            page_width_mm: _pageSize === 'Custom' ? _customW : null,
            page_height_mm: _pageSize === 'Custom' ? _customH : null,
            thumbnail: _canvas.toDataURL({ format: 'png', quality: 0.3, multiplier: 0.2 })
        };

        API.saveTemplate(data, function(response) {
            if (response && response.success) {
                _currentTemplateId = response.id;
                document.getElementById('pd-save-id').value = response.id;
                document.getElementById('pd-save-modal').classList.remove('visible');
                showToast('Template saved!', 'success');
                _refreshTemplateList();
            } else {
                showToast('Save failed: ' + (response ? response.message : 'Unknown error'), 'error');
            }
        });
    }

    function loadTemplate(id) {
        API.loadTemplate(id, function(response) {
            if (!response || !response.success) {
                showToast('Failed to load template', 'error');
                return;
            }

            var tpl = response.data;
            _currentTemplateId = tpl.id;

            // Set page size
            _pageSize = tpl.page_size || 'A4';
            _orientation = tpl.orientation || 'portrait';
            _setCtxValue('pd-page-size', _pageSize);
            _setCtxValue('pd-orientation', _orientation);

            var customFields = document.getElementById('pd-custom-size');
            if (customFields) {
                customFields.style.display = _pageSize === 'Custom' ? 'flex' : 'none';
            }
            if (_pageSize === 'Custom') {
                _customW = parseFloat(tpl.page_width_mm) || 210;
                _customH = parseFloat(tpl.page_height_mm) || 297;
                _setCtxValue('pd-custom-w', _customW);
                _setCtxValue('pd-custom-h', _customH);
            }

            _applyPageSize();

            // Set table
            if (tpl.table_name) {
                VariablesManager.setTable(tpl.table_name);
            }

            // Load canvas
            if (tpl.layout_json) {
                var canvasData = typeof tpl.layout_json === 'string' ? JSON.parse(tpl.layout_json) : tpl.layout_json;
                _undoLock = true;
                _canvas.loadFromJSON(canvasData, function() {
                    _canvas.renderAll();
                    _undoLock = false;
                    _undoStack = [JSON.stringify(_canvas.toJSON(['customType', 'tableConfig', 'lineStyle']))];
                    _redoStack = [];
                    _updateUndoButtons();
                    if (_gridEnabled) _drawGrid();
                }, function(o, object) {
                    if (o.customType) object.customType = o.customType;
                    if (o.tableConfig) object.tableConfig = o.tableConfig;
                    if (o.lineStyle)   object.lineStyle   = o.lineStyle;
                });
            }

            // Update save modal name
            var nameInput = document.getElementById('pd-save-name');
            if (nameInput) nameInput.value = tpl.name;
            var idInput = document.getElementById('pd-save-id');
            if (idInput) idInput.value = tpl.id;

            showToast('Template loaded: ' + tpl.name, 'success');
        });
    }

    // ─── Template List ──────────────────────────────────────

    function _bindTemplateActions() {
        // Handled via event delegation in _refreshTemplateList
    }

    function _refreshTemplateList() {
        var container = document.getElementById('pd-templates-list');
        if (!container) return;

        container.innerHTML = '<div class="pd-loading"><div class="pd-spinner"></div></div>';

        API.listTemplates(function(response) {
            if (!response || !response.success) {
                container.innerHTML = '<div style="color: var(--pd-text-dim); padding: 20px; font-size: 12px; text-align: center;">No templates found</div>';
                return;
            }

            var templates = response.data || [];
            if (!templates.length) {
                container.innerHTML = '<div style="color: var(--pd-text-dim); padding: 20px; font-size: 12px; text-align: center;">No templates yet. Create one!</div>';
                return;
            }

            container.innerHTML = '';
            templates.forEach(function(tpl) {
                var card = document.createElement('div');
                card.className = 'pd-template-card';
                card.innerHTML =
                    '<div class="pd-template-card-header">' +
                        '<span class="pd-template-card-name">' + _escHtml(tpl.name) + '</span>' +
                        (tpl.is_default == 1 ? '<span class="pd-template-default-badge">Default</span>' : '') +
                    '</div>' +
                    '<div class="pd-template-card-meta">' +
                        '<span><i class="fa fa-table"></i> ' + _escHtml(tpl.table_name) + '</span>' +
                        '<span><i class="fa fa-file-o"></i> ' + _escHtml(tpl.page_size) + '</span>' +
                        '<span>' + _escHtml(tpl.updated_at || '') + '</span>' +
                    '</div>' +
                    '<div class="pd-template-card-actions" style="margin-top:8px;">' +
                        '<button class="pd-btn pd-btn-sm pd-btn-primary pd-tpl-load" data-id="' + tpl.id + '"><i class="fa fa-folder-open"></i> Load</button>' +
                        '<button class="pd-btn pd-btn-sm pd-tpl-dup" data-id="' + tpl.id + '"><i class="fa fa-copy"></i></button>' +
                        '<button class="pd-btn pd-btn-sm pd-tpl-default" data-id="' + tpl.id + '" title="Set Default"><i class="fa fa-star"></i></button>' +
                        '<button class="pd-btn pd-btn-sm pd-btn-danger pd-tpl-del" data-id="' + tpl.id + '"><i class="fa fa-trash"></i></button>' +
                    '</div>';

                // Event delegation
                card.querySelector('.pd-tpl-load').addEventListener('click', function() { loadTemplate(this.dataset.id); });
                card.querySelector('.pd-tpl-dup').addEventListener('click', function() {
                    API.duplicateTemplate(this.dataset.id, function(r) {
                        if (r && r.success) { showToast('Template duplicated', 'success'); _refreshTemplateList(); }
                    });
                });
                card.querySelector('.pd-tpl-default').addEventListener('click', function() {
                    API.setDefault(this.dataset.id, function(r) {
                        if (r && r.success) { showToast('Set as default', 'success'); _refreshTemplateList(); }
                    });
                });
                card.querySelector('.pd-tpl-del').addEventListener('click', function() {
                    if (confirm('Delete this template?')) {
                        var delId = this.dataset.id;
                        API.deleteTemplate(delId, function(r) {
                            if (r && r.success) {
                                if (_currentTemplateId == delId) _currentTemplateId = null;
                                showToast('Template deleted', 'success');
                                _refreshTemplateList();
                            }
                        });
                    }
                });

                container.appendChild(card);
            });
        });
    }

    // ─── Export PDF ──────────────────────────────────────────

    function _previewPDF() {
        var tableName = VariablesManager.getCurrentTable();
        var rowId = document.getElementById('pd-preview-row-id').value.trim() || '0';
        
        var canvasJSON = _canvas.toJSON(['customType', 'tableConfig']);
        canvasJSON.objects = canvasJSON.objects.filter(function(o) { return !o.isGrid; });

        // Fill the hidden form and submit
        document.getElementById('pd-preview-json').value = JSON.stringify(canvasJSON);
        document.getElementById('pd-preview-table').value = tableName;
        document.getElementById('pd-preview-row').value = rowId;
        document.getElementById('pd-preview-size').value = _pageSize;
        document.getElementById('pd-preview-orient').value = _orientation;
        document.getElementById('pd-preview-custom-w').value = _pageSize === 'Custom' ? _customW : '';
        document.getElementById('pd-preview-custom-h').value = _pageSize === 'Custom' ? _customH : '';
        document.getElementById('pd-preview-ts').value = Date.now();

        document.getElementById('pd-preview-form').submit();
        showToast('Opening PDF preview...', 'info');
    }

    function _exportPDF() {
        var tableName = VariablesManager.getCurrentTable();
        if (!tableName) {
            showToast('Select a table first', 'error');
            return;
        }

        var rowIds = prompt('Enter row ID(s) for PDF export (comma-separated for bulk):');
        if (!rowIds) return;

        var canvasJSON = _canvas.toJSON(['customType']);
        canvasJSON.objects = canvasJSON.objects.filter(function(o) { return !o.isGrid; });

        var data = {
            table_name: tableName,
            row_ids: rowIds.split(',').map(function(s) { return s.trim(); }),
            layout_json: JSON.stringify(canvasJSON),
            page_size: _pageSize,
            orientation: _orientation,
            page_width_mm: _pageSize === 'Custom' ? _customW : null,
            page_height_mm: _pageSize === 'Custom' ? _customH : null
        };

        showToast('Generating PDF...', 'info');

        API.exportPDF(data, function(blob) {
            API.downloadBlob(blob, 'print-designer-export.pdf');
            showToast('PDF downloaded!', 'success');
        }, function(err) {
            showToast('PDF export failed: ' + err, 'error');
        });
    }

    // ─── Utilities ──────────────────────────────────────────

    function _escHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function showToast(message, type) {
        type = type || 'info';
        var container = document.getElementById('pd-toast-container');
        if (!container) return;

        var toast = document.createElement('div');
        toast.className = 'pd-toast pd-toast-' + type;
        toast.innerHTML = '<i class="fa fa-' + (type === 'success' ? 'check' : type === 'error' ? 'times' : 'info-circle') + '"></i> ' + message;
        container.appendChild(toast);

        setTimeout(function() {
            toast.style.opacity = '0';
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    }

    function addVariableText(tag, column) {
        var text = new fabric.Textbox(tag, {
            left: 100 + Math.random() * 100,
            top: 100 + Math.random() * 100,
            width: 180,
            fontSize: 14,
            fontFamily: 'Arial',
            fill: '#6366f1',
            editable: true
        });
        _canvas.add(text);
        _canvas.setActiveObject(text);
        _canvas.renderAll();
    }

    // ─── Public API ─────────────────────────────────────────

    return {
        init: init,
        getCanvas: function() { return _canvas; },
        setTableContext: function(t) { _tableContext = t; },
        addVariableText: addVariableText,
        showToast: showToast,
        loadTemplate: loadTemplate,
        undo: undo,
        redo: redo
    };

})();

// ─── Bootstrap on DOM Ready ─────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    Designer.init();
});
