/**
 * ============================================================
 * PRINT DESIGNER — Variables Manager
 * Handles table selection, column fetching, and drag-drop chips
 * ============================================================
 */
var VariablesManager = (function() {
    'use strict';

    var _currentTable = '';
    var _currentVariables = [];
    var _searchTerm = '';

    function init() {
        _bindTableSelect();
        _bindSearch();
        _loadTables();
    }

    function _loadTables() {
        var select = document.getElementById('pd-table-select');
        if (!select) return;

        select.innerHTML = '<option value="">\u2014 Loading tables... \u2014</option>';
        API.getTables(function(tables) {
            select.innerHTML = '<option value="">\u2014 Select a source \u2014</option>';

            // ── Fixed top entry: Billing (bill_format_2) ────────────
            var billGroup = document.createElement('optgroup');
            billGroup.label = '\ud83d\udcc4 Print Templates';

            var billOpt = document.createElement('option');
            billOpt.value = '__bill__';
            billOpt.textContent = '\ud83e\uddf3 Bill (bill_format_2)';
            billGroup.appendChild(billOpt);

            var estOpt = document.createElement('option');
            estOpt.value = '__estimation__';
            estOpt.textContent = '\ud83d\udccb Estimation';
            billGroup.appendChild(estOpt);

            select.appendChild(billGroup);

            // ── Separator then DB tables ─────────────────────────────
            if (tables && tables.length) {
                var dbGroup = document.createElement('optgroup');
                dbGroup.label = '\ud83d\uddc4 Database Tables';
                tables.forEach(function(t) {
                    var opt = document.createElement('option');
                    opt.value = t.TABLE_NAME;
                    opt.textContent = t.TABLE_NAME;
                    if (t.TABLE_COMMENT) {
                        opt.textContent += ' (' + t.TABLE_COMMENT + ')';
                    }
                    dbGroup.appendChild(opt);
                });
                select.appendChild(dbGroup);
            }
        });
    }

    function _bindTableSelect() {
        var select = document.getElementById('pd-table-select');
        if (!select) return;

        select.addEventListener('change', function() {
            var table = this.value;

            // Update Preview ID label to reflect the selected source
            var previewLabel = document.getElementById('pd-preview-label');
            var previewInput = document.getElementById('pd-preview-row-id');
            if (previewLabel) {
                previewLabel.textContent = (table === '__bill__') ? 'Bill ID' : (table === '__estimation__') ? 'Estimation ID' : 'Preview ID';
            }
            if (previewInput) {
                previewInput.placeholder = (table === '__bill__') ? 'bill_id' : (table === '__estimation__') ? 'estimation_id' : 'ID';
            }

            if (table) {
                loadVariables(table);
            } else {
                _currentTable = '';
                _currentVariables = [];
                _renderChips([]);
            }
        });
    }

    function _bindSearch() {
        var input = document.getElementById('pd-var-search');
        if (!input) return;

        input.addEventListener('input', function() {
            _searchTerm = this.value.toLowerCase();
            _renderChips(_filterVariables());
        });
    }

    function loadVariables(tableName) {
        _currentTable = tableName;
        var list = document.getElementById('pd-var-list');
        if (list) {
            list.innerHTML = '<div class="pd-loading"><div class="pd-spinner"></div></div>';
        }

        API.getVariables(tableName, function(variables) {
            _currentVariables = variables || [];
            _renderChips(_currentVariables);

            // Notify designer about new table context
            if (typeof Designer !== 'undefined' && Designer.setTableContext) {
                Designer.setTableContext(tableName);
            }
        });
    }

    function _filterVariables() {
        if (!_searchTerm) return _currentVariables;
        return _currentVariables.filter(function(v) {
            return v.column.toLowerCase().indexOf(_searchTerm) !== -1 ||
                   v.type.toLowerCase().indexOf(_searchTerm) !== -1 ||
                   (v.description && v.description.toLowerCase().indexOf(_searchTerm) !== -1);
        });
    }

    function _renderChips(variables) {
        var list = document.getElementById('pd-var-list');
        if (!list) return;

        if (!variables || !variables.length) {
            list.innerHTML = '<div style="text-align:center; color: var(--pd-text-dim); padding: 20px; font-size: 12px;">' +
                (_currentTable ? 'No columns found' : 'Select a source above') + '</div>';
            return;
        }

        list.innerHTML = '';

        // Check if variables have group info (bill variables do)
        var hasGroups = variables.length > 0 && variables[0].group;

        if (hasGroups) {
            // Group rendering: emit a header row for each new group
            var currentGroup = null;
            variables.forEach(function(v) {
                if (v.group && v.group !== currentGroup) {
                    currentGroup = v.group;
                    var hdr = document.createElement('div');
                    hdr.style.cssText = 'font-size:10px; font-weight:bold; text-transform:uppercase; ' +
                        'color:var(--pd-accent); padding:6px 4px 2px; letter-spacing:0.5px; ' +
                        'border-top:1px solid var(--pd-border); margin-top:4px;';
                    hdr.textContent = currentGroup;
                    list.appendChild(hdr);
                }
                list.appendChild(_buildChip(v));
            });
        } else {
            variables.forEach(function(v) {
                list.appendChild(_buildChip(v));
            });
        }
    }

    function _buildChip(v) {
        var chip = document.createElement('div');
        chip.className = 'pd-var-chip';
        chip.draggable = true;
        chip.dataset.tag = v.tag;
        chip.dataset.column = v.column;
        chip.dataset.type = v.type;

        var tagSpan = '<span class="pd-var-tag">' + _escHtml(v.tag) + '</span>';
        var typeSpan = '<span class="pd-var-type">' + _escHtml(v.type) + '</span>';
        var pkSpan   = v.is_pk ? '<span class="pd-var-pk">PK</span>' : '';

        // Show description as tooltip if present
        if (v.description) {
            chip.title = v.description;
        }

        chip.innerHTML = tagSpan + pkSpan + typeSpan;

        // Drag start
        chip.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', v.tag);
            e.dataTransfer.setData('application/x-pd-variable', JSON.stringify(v));
            e.dataTransfer.effectAllowed = 'copy';
            chip.style.opacity = '0.5';
        });

        chip.addEventListener('dragend', function() {
            chip.style.opacity = '1';
        });

        // Click to add directly
        chip.addEventListener('click', function() {
            if (typeof Designer !== 'undefined' && Designer.addVariableText) {
                Designer.addVariableText(v.tag, v.column);
            }
        });

        return chip;
    }

    function _escHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // ─── Public API ─────────────────────────────────────────

    return {
        init: init,
        loadVariables: loadVariables,
        getCurrentTable: function() { return _currentTable; },
        getCurrentVariables: function() { return _currentVariables; },
        setTable: function(tableName) {
            var select = document.getElementById('pd-table-select');
            if (select) {
                select.value = tableName;
            }
            if (tableName) loadVariables(tableName);
        }
    };

})();
