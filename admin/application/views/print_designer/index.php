<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Designer — Dynamic Template Editor</title>

    <!-- Font Awesome (same version used in AdminLTE header) -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Designer CSS -->
    <link href="<?php echo base_url(); ?>assets/print-designer/css/designer.css" rel="stylesheet">

    <script>var BASE_URL = '<?php echo base_url(); ?>';</script>
</head>
<body>

<div class="pd-app">
    <!-- ═══ Top Toolbar ═══════════════════════════════════ -->
    <div class="pd-toolbar">
        <div class="pd-toolbar-brand">
            <i class="fa fa-print"></i> Print Designer
        </div>
        <div class="pd-toolbar-divider"></div>

        <!-- Page Format -->
        <div class="pd-toolbar-group">
            <label>Page</label>
            <select id="pd-page-size">
                <option value="A4">A4</option>
                <option value="A3">A3</option>
                <option value="A5">A5</option>
                <option value="Letter">Letter</option>
                <option value="Legal">Legal</option>
                <option value="80mm">80mm Receipt</option>
                <option value="58mm">58mm Receipt</option>
                <option value="Custom">Custom</option>
            </select>
        </div>

        <div class="pd-toolbar-group" id="pd-custom-size" style="display:none;">
            <label>W(mm)</label>
            <input type="number" id="pd-custom-w" value="210" min="10" max="1000">
            <label>H(mm)</label>
            <input type="number" id="pd-custom-h" value="297" min="10" max="2000">
        </div>

        <div class="pd-toolbar-group">
            <label>Orient</label>
            <select id="pd-orientation">
                <option value="portrait">Portrait</option>
                <option value="landscape">Landscape</option>
            </select>
        </div>

        <div class="pd-toolbar-divider"></div>

        <!-- Zoom Controls -->
        <div class="pd-toolbar-group">
            <button class="pd-btn pd-btn-icon" id="pd-zoom-out" title="Zoom Out (Ctrl+Minus)">
                <i class="fa fa-search-minus"></i>
            </button>
            <span id="pd-zoom-level" style="font-size: 11px; min-width: 35px; text-align: center; color: var(--pd-text-dim);">100%</span>
            <button class="pd-btn pd-btn-icon" id="pd-zoom-in" title="Zoom In (Ctrl+Plus)">
                <i class="fa fa-search-plus"></i>
            </button>
            <button class="pd-btn pd-btn-icon" id="pd-zoom-reset" title="Reset Zoom">
                <i class="fa fa-refresh"></i>
            </button>
        </div>

        <div class="pd-toolbar-divider"></div>

        <!-- Snap Grid -->
        <button class="pd-btn pd-btn-icon" id="pd-grid-toggle" title="Toggle Grid">
            <i class="fa fa-th"></i>
        </button>

        <!-- Undo / Redo -->
        <button class="pd-btn pd-btn-icon" id="pd-undo" title="Undo (Ctrl+Z)">
            <i class="fa fa-undo"></i>
        </button>
        <button class="pd-btn pd-btn-icon" id="pd-redo" title="Redo (Ctrl+Y)">
            <i class="fa fa-repeat"></i>
        </button>

        <div class="pd-toolbar-spacer"></div>

        <!-- Actions -->
        <button class="pd-btn" id="pd-new">
            <i class="fa fa-file-o"></i> New
        </button>
        <button class="pd-btn pd-btn-primary" id="pd-save">
            <i class="fa fa-save"></i> Save
        </button>
        
        <div class="pd-toolbar-divider"></div>
        
        <div class="pd-toolbar-group">
            <label id="pd-preview-label">Preview ID</label>
            <input type="text" id="pd-preview-row-id" placeholder="ID" style="width: 60px;">
            <button class="pd-btn pd-btn-info" id="pd-preview">
                <i class="fa fa-eye"></i> Preview
            </button>
        </div>

        <button class="pd-btn pd-btn-success" id="pd-export-pdf">
            <i class="fa fa-file-pdf-o"></i> PDF
        </button>
        <button class="pd-btn pd-btn-icon" id="pd-close" title="Close Designer">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <!-- ═══ Main Layout ═══════════════════════════════════ -->
    <div class="pd-main">
        <!-- ─── Left Sidebar ──────────────────────────── -->
        <div class="pd-sidebar-left">
            <div class="pd-sidebar-tabs">
                <button class="pd-sidebar-tab active" data-tab="pd-panel-elements">Elements</button>
                <button class="pd-sidebar-tab" data-tab="pd-panel-variables">Variables</button>
                <button class="pd-sidebar-tab" data-tab="pd-panel-templates">Templates</button>
            </div>

            <div class="pd-sidebar-content">
                <!-- Elements Panel -->
                <div class="pd-tab-panel active" id="pd-panel-elements">
                    <div class="pd-elements-grid">
                        <div class="pd-element-item" id="pd-add-text" draggable="true" data-type="text">
                            <i class="fa fa-font"></i>
                            <span>Text</span>
                        </div>
                        <div class="pd-element-item" id="pd-add-heading" draggable="true" data-type="heading">
                            <i class="fa fa-header"></i>
                            <span>Heading</span>
                        </div>
                        <div class="pd-element-item" id="pd-add-rect" draggable="true" data-type="rect">
                            <i class="fa fa-square-o"></i>
                            <span>Rectangle</span>
                        </div>
                        <div class="pd-element-item" id="pd-add-line" draggable="true" data-type="line">
                            <i class="fa fa-minus"></i>
                            <span>Solid Line</span>
                        </div>
                        <div class="pd-element-item" id="pd-add-line-dashed" draggable="true" data-type="line-dashed">
                            <span style="font-size:16px;letter-spacing:2px;">&#8212;&#8212;</span>
                            <span>Dashed Line</span>
                        </div>
                        <div class="pd-element-item" id="pd-add-line-dotted" draggable="true" data-type="line-dotted">
                            <span style="font-size:16px;letter-spacing:3px;">&#183;&#183;&#183;</span>
                            <span>Dotted Line</span>
                        </div>
                        <div class="pd-element-item" id="pd-add-line-dashdot" draggable="true" data-type="line-dashdot">
                            <span style="font-size:13px;letter-spacing:1px;">-&#183;-&#183;</span>
                            <span>Dash&#8209;Dot</span>
                        </div>
                        <div class="pd-element-item" id="pd-add-image" draggable="true" data-type="image">
                            <i class="fa fa-image"></i>
                            <span>Image</span>
                        </div>
                        <div class="pd-element-item" id="pd-add-table" draggable="true" data-type="table">
                            <i class="fa fa-table"></i>
                            <span>Table Grid</span>
                        </div>
                    </div>

                    <div style="margin-top: 16px; border-top: 1px solid var(--pd-border); padding-top: 12px;">
                        <button class="pd-btn pd-btn-danger" id="pd-delete" style="width: 100%;">
                            <i class="fa fa-trash"></i> Delete Selected
                        </button>
                    </div>
                </div>

                <!-- Variables Panel -->
                <div class="pd-tab-panel" id="pd-panel-variables">
                    <div class="pd-var-table-select">
                        <select id="pd-table-select">
                            <option value="">— Loading tables... —</option>
                        </select>
                    </div>
                    <input type="text" class="pd-var-search" id="pd-var-search" placeholder="Search columns...">
                    <div class="pd-var-list" id="pd-var-list">
                        <div style="text-align:center; color: var(--pd-text-dim); padding: 20px; font-size: 12px;">
                            Select a table above to see columns
                        </div>
                    </div>
                </div>

                <!-- Templates Panel -->
                <div class="pd-tab-panel" id="pd-panel-templates">
                    <div class="pd-template-list" id="pd-templates-list">
                        <div class="pd-loading"><div class="pd-spinner"></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Canvas Area ───────────────────────────── -->
        <div class="pd-canvas-area">
            <div class="pd-canvas-wrapper">
                <div class="pd-canvas-container">
                    <canvas id="pd-canvas"></canvas>
                </div>
            </div>

            <!-- Context Bar (font controls, z-index) -->
            <div class="pd-context-bar">
                <!-- Text controls -->
                <div id="pd-ctx-text-controls" style="display:none; align-items:center; gap:6px; flex-wrap: wrap;">
                    <label>Font</label>
                    <select id="pd-ctx-font-family">
                        <option value="Arial">Arial</option>
                        <option value="Helvetica">Helvetica</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Verdana">Verdana</option>
                        <option value="Tahoma">Tahoma</option>
                        <option value="Trebuchet MS">Trebuchet MS</option>
                        <option value="Impact">Impact</option>
                    </select>

                    <label>Size</label>
                    <input type="number" id="pd-ctx-font-size" value="14" min="6" max="120">

                    <label>Color</label>
                    <input type="color" id="pd-ctx-font-color" value="#000000">

                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-bold" title="Bold"><i class="fa fa-bold"></i></button>
                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-italic" title="Italic"><i class="fa fa-italic"></i></button>
                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-underline" title="Underline"><i class="fa fa-underline"></i></button>

                    <div class="pd-toolbar-divider"></div>

                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-align-left" title="Left"><i class="fa fa-align-left"></i></button>
                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-align-center" title="Center"><i class="fa fa-align-center"></i></button>
                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-align-right" title="Right"><i class="fa fa-align-right"></i></button>
                </div>

                <!-- Shape controls -->
                <div id="pd-ctx-shape-controls" style="display:none; align-items:center; gap:6px;">
                    <label>Stroke</label>
                    <input type="color" id="pd-ctx-stroke-color" value="#000000">
                    <label>Width</label>
                    <input type="number" id="pd-ctx-stroke-width" value="1" min="0" max="20">
                </div>

                <!-- Line-only controls (shown only for line elements) -->
                <div id="pd-ctx-line-controls" style="display:none; align-items:center; gap:6px;">
                    <label>Style</label>
                    <select id="pd-ctx-line-style">
                        <option value="solid">─── Solid</option>
                        <option value="dashed">- - - Dashed</option>
                        <option value="dotted">· · · Dotted</option>
                        <option value="dashdot">-·-·- Dash-Dot</option>
                    </select>
                </div>

                <!-- Z-index controls (always visible) -->
                <div style="margin-left:auto; display:flex; gap:4px;">
                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-bring-front" title="Bring to Front"><i class="fa fa-angle-double-up"></i></button>
                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-bring-forward" title="Bring Forward"><i class="fa fa-angle-up"></i></button>
                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-send-backward" title="Send Backward"><i class="fa fa-angle-down"></i></button>
                    <button class="pd-btn pd-btn-icon pd-btn-sm" id="pd-ctx-send-back" title="Send to Back"><i class="fa fa-angle-double-down"></i></button>
                </div>
            </div>
        </div>

        </div>
    </div>

    <!-- ═══ Status Bar ════════════════════════════════════ -->
    <div class="pd-statusbar">
        <div class="pd-status-item">
            <span class="pd-status-dot"></span>
            <span>Ready</span>
        </div>
        <div class="pd-status-item">
            <i class="fa fa-arrows-alt" style="font-size:10px;"></i>
            <span id="pd-status-size">210mm × 297mm</span>
        </div>
    </div>
</div>

<!-- ═══ Save Modal ═════════════════════════════════════════ -->
<div class="pd-modal-overlay" id="pd-save-modal">
    <div class="pd-modal">
        <h3><i class="fa fa-save"></i> Save Template</h3>
        <input type="hidden" id="pd-save-id" value="">
        <div class="pd-modal-field">
            <label>Template Name</label>
            <input type="text" id="pd-save-name" placeholder="e.g. Invoice Format A4">
        </div>
        <div class="pd-modal-actions">
            <button class="pd-btn" id="pd-save-cancel">Cancel</button>
            <button class="pd-btn pd-btn-primary" id="pd-save-confirm"><i class="fa fa-save"></i> Save</button>
        </div>
    </div>
</div>

<!-- ═══ Quick Table Setup Modal ══════════════════════════ -->
<div class="pd-modal-overlay" id="pd-quick-table-modal">
    <div class="pd-modal pd-quick-table-dialog">
        <h3><i class="fa fa-table"></i> Insert Dynamic Table</h3>
        <p class="pd-quick-table-hint">Configure the basic structure before setting up columns.</p>

        <div class="pd-quick-table-grid">
            <!-- Column count -->
            <div class="pd-quick-option">
                <div class="pd-quick-option-icon"><i class="fa fa-columns"></i></div>
                <label>Number of Columns</label>
                <input type="number" id="pd-qt-col-count" value="3" min="1" max="20" class="pd-qt-big-input">
            </div>

            <!-- thead -->
            <div class="pd-quick-option pd-quick-option-active" id="pd-qt-thead-option">
                <div class="pd-quick-option-icon pd-quick-option-icon-green"><i class="fa fa-header"></i></div>
                <label>Header Row (thead)</label>
                <div class="pd-qt-toggle-row">
                    <input type="checkbox" id="pd-qt-show-header" checked>
                    <label for="pd-qt-show-header" style="text-transform:none;font-size:12px;">Always show header</label>
                </div>
                <small>Default: ON — shows column labels at the top</small>
            </div>

            <!-- tfoot -->
            <div class="pd-quick-option" id="pd-qt-tfoot-option">
                <div class="pd-quick-option-icon"><i class="fa fa-th-large"></i></div>
                <label>Footer Row (tfoot)</label>
                <div class="pd-qt-toggle-row">
                    <input type="checkbox" id="pd-qt-show-footer">
                    <label for="pd-qt-show-footer" style="text-transform:none;font-size:12px;">Add summary footer</label>
                </div>
                <small>Optional — use for totals or summary text</small>
            </div>
        </div>

        <div class="pd-modal-actions">
            <button class="pd-btn" id="pd-qt-cancel">Cancel</button>
            <button class="pd-btn pd-btn-primary" id="pd-qt-continue">
                <i class="fa fa-arrow-right"></i> Continue to Column Setup
            </button>
        </div>
    </div>
</div>

<!-- ═══ Table Config Modal ════════════════════════════════ -->
<div class="pd-modal-overlay" id="pd-table-config-modal">
    <div class="pd-modal pd-modal-wide">
        <h3><i class="fa fa-table"></i> Configure Data Table</h3>

        <!-- Source Table & Filter -->
        <div class="pd-tbl-cfg-section">
            <div class="pd-tbl-cfg-row">
                <div class="pd-modal-field" style="flex:1;">
                    <label>Source Table (detail rows)</label>
                    <select id="pd-tbl-source-table">
                        <option value="">— Same as template table —</option>
                    </select>
                </div>
                <div class="pd-modal-field" style="flex:1;">
                    <label>Filter (WHERE clause)</label>
                    <input type="text" id="pd-tbl-source-filter" placeholder="e.g. sale_id = {{sale_id}}">
                </div>
            </div>
        </div>

        <!-- Table Style -->
        <div class="pd-tbl-cfg-section">
            <h4>Table Style</h4>
            <div class="pd-tbl-cfg-row">
                <div class="pd-modal-field" style="flex:0 0 80px;">
                    <label>Font Size</label>
                    <input type="number" id="pd-tbl-font-size" value="11" min="6" max="30">
                </div>
                <div class="pd-modal-field" style="flex:0 0 80px;">
                    <label>Cell Padding</label>
                    <input type="number" id="pd-tbl-cell-padding" value="4" min="0" max="20">
                </div>
                <div class="pd-modal-field" style="flex:0 0 60px;">
                    <label>Border</label>
                    <input type="number" id="pd-tbl-border-width" value="1" min="0" max="5">
                </div>
                <div class="pd-modal-field" style="flex:0 0 50px;">
                    <label>Border Color</label>
                    <input type="color" id="pd-tbl-border-color" value="#000000">
                </div>
                <div class="pd-modal-field" style="flex:0 0 auto;">
                    <label>Header</label>
                    <div style="display:flex; align-items:center; gap:6px; padding-top:4px;">
                        <input type="checkbox" id="pd-tbl-show-header" checked>
                        <label for="pd-tbl-show-header" style="text-transform:none; font-size:12px;">Show</label>
                    </div>
                </div>
                <div class="pd-modal-field" style="flex:0 0 50px;">
                    <label>Hdr BG</label>
                    <input type="color" id="pd-tbl-header-bg" value="#333333">
                </div>
                <div class="pd-modal-field" style="flex:0 0 50px;">
                    <label>Hdr Color</label>
                    <input type="color" id="pd-tbl-header-color" value="#ffffff">
                </div>
            </div>
        </div>

        <!-- Columns -->
        <div class="pd-tbl-cfg-section">
            <h4>Columns <button class="pd-btn pd-btn-sm pd-btn-primary" id="pd-tbl-add-col"><i class="fa fa-plus"></i> Add Column</button></h4>
            <div class="pd-tbl-col-list" id="pd-tbl-col-list">
                <!-- Column rows will be rendered by JS -->
            </div>
        </div>

        <!-- Cell Merging -->
        <div class="pd-tbl-cfg-section">
            <h4>Cell Merging (Header) <button class="pd-btn pd-btn-sm" id="pd-tbl-add-merge"><i class="fa fa-plus"></i> Add Merge</button></h4>
            <div class="pd-tbl-merge-list" id="pd-tbl-merge-list">
                <div class="pd-tbl-merge-empty">No cell merges configured</div>
            </div>
        </div>

        <!-- Footer Row -->
        <div class="pd-tbl-cfg-section" id="pd-tbl-footer-section">
            <h4>
                <i class="fa fa-th-large" style="font-size:11px;"></i> Footer Row (tfoot)
                <label style="font-weight:400;text-transform:none;font-size:11px;color:var(--pd-text-dim);margin-left:4px;">
                    <input type="checkbox" id="pd-tbl-show-footer" style="margin-right:4px;">Enable Footer Row
                </label>
            </h4>
            <div id="pd-tbl-footer-cols" style="display:none;">
                <p style="font-size:11px;color:var(--pd-text-dim);margin-bottom:8px;">Enter static text or a <code>{{variable}}</code> for each footer cell. Leave blank to span with merged text.</p>
                <div class="pd-tbl-col-list" id="pd-tbl-footer-col-list">
                    <!-- Footer cell inputs rendered by JS -->
                </div>
            </div>
        </div>

        <div class="pd-modal-actions">
            <button class="pd-btn" id="pd-tbl-cfg-cancel">Cancel</button>
            <button class="pd-btn pd-btn-primary" id="pd-tbl-cfg-apply"><i class="fa fa-check"></i> Apply</button>
        </div>
    </div>
</div>

<!-- Hidden form for PDF Preview POST -->
    <form id="pd-preview-form" action="<?php echo base_url(); ?>index.php/print_designer/preview_pdf" method="POST" target="_blank">
        <input type="hidden" name="layout_json" id="pd-preview-json">
        <input type="hidden" name="table_name" id="pd-preview-table">
        <input type="hidden" name="row_id" id="pd-preview-row">
        <input type="hidden" name="page_size" id="pd-preview-size">
        <input type="hidden" name="orientation" id="pd-preview-orient">
        <input type="hidden" name="custom_w" id="pd-preview-custom-w">
        <input type="hidden" name="custom_h" id="pd-preview-custom-h">
        <input type="hidden" name="_ts" id="pd-preview-ts">
    </form>

<!-- ═══ Toast Container ═══════════════════════════════════ -->
<div class="pd-toast-container" id="pd-toast-container"></div>

<!-- ═══ Scripts ════════════════════════════════════════════ -->
<!-- Fabric.js from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<!-- Designer scripts -->
<script src="<?php echo base_url(); ?>assets/print-designer/js/api.js"></script>
<script src="<?php echo base_url(); ?>assets/print-designer/js/variables.js"></script>
<script src="<?php echo base_url(); ?>assets/print-designer/js/table-renderer.js"></script>
<script src="<?php echo base_url(); ?>assets/print-designer/js/designer.js"></script>

</body>
</html>
