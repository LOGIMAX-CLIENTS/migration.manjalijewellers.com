<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Template Designer V2 — <?= htmlspecialchars($template['template_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/print-designer/konva-designer.css') ?>">
</head>
<body class="kd-body">

<!-- ═══ TOOLBAR ═══════════════════════════════════════ -->
<div class="kd-toolbar">
    <div class="kd-toolbar-left">
        <a href="<?= site_url('print-templates') ?>" class="kd-btn kd-btn-sm kd-btn-ghost" title="Back to List">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        <div class="kd-toolbar-divider"></div>
        <h1 class="kd-template-name"><?= htmlspecialchars($template['template_name']) ?></h1>
        <span class="kd-badge kd-badge-accent">V2 Canvas</span>
        <span class="kd-badge kd-badge-paper" id="kdPaperBadge"><?= htmlspecialchars($template['paper_size'] ?? 'A4') ?></span>
    </div>
    <div class="kd-toolbar-center">
        <!-- Zoom -->
        <div class="kd-toolbar-group kd-zoom-group">
            <button class="kd-btn kd-btn-icon" id="kdZoomOut" title="Zoom Out"><i class="fa fa-search-minus"></i></button>
            <span id="kdZoomLevel">100%</span>
            <button class="kd-btn kd-btn-icon" id="kdZoomIn" title="Zoom In"><i class="fa fa-search-plus"></i></button>
            <button class="kd-btn kd-btn-icon" id="kdZoomFit" title="Fit to View"><i class="fa fa-compress"></i></button>
        </div>

        <div class="kd-toolbar-divider"></div>

        <!-- Dynamic Context Tools (Moved from bottom) -->
        <div id="kdContextTools" style="display:flex; align-items:center; gap:10px;">
            <div class="kd-toolbar-group" id="kdCtxText" style="display:none;">
                <select id="kdCtxFontFamily" class="kd-select-sm">
                    <option value="Arial">Arial</option>
                    <option value="Helvetica">Helvetica</option>
                    <option value="Times New Roman">Times New Roman</option>
                    <option value="Courier New">Courier New</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Verdana">Verdana</option>
                </select>
                <input type="number" id="kdCtxFontSize" class="kd-input-sm" value="14" min="6" max="120" style="width:45px;">
                <input type="color" id="kdCtxFontColor" value="#000000" class="kd-color-input">
                <button class="kd-btn kd-btn-icon kd-btn-sm" id="kdCtxBold" title="Bold"><i class="fa fa-bold"></i></button>
                <button class="kd-btn kd-btn-icon kd-btn-sm" id="kdCtxItalic" title="Italic"><i class="fa fa-italic"></i></button>
                <button class="kd-btn kd-btn-icon kd-btn-sm" id="kdCtxUnderline" title="Underline"><i class="fa fa-underline"></i></button>
                <select id="kdCtxTextCase" class="kd-select-sm" title="Text Case" style="width:55px; font-size:10px;">
                    <option value="none">Aa</option>
                    <option value="uppercase">AB</option>
                    <option value="lowercase">ab</option>
                    <option value="capitalize">Ab</option>
                </select>
                <div class="kd-toolbar-divider"></div>
                <button class="kd-btn kd-btn-icon kd-btn-sm" id="kdCtxAlignLeft"><i class="fa fa-align-left"></i></button>
                <button class="kd-btn kd-btn-icon kd-btn-sm" id="kdCtxAlignCenter"><i class="fa fa-align-center"></i></button>
                <button class="kd-btn kd-btn-icon kd-btn-sm" id="kdCtxAlignRight"><i class="fa fa-align-right"></i></button>
            </div>
            <div class="kd-toolbar-group" id="kdCtxShape" style="display:none;">
                <label style="font-size:10px; color:var(--kd-text-muted);">FILL</label>
                <input type="color" id="kdCtxFill" value="#ffffff" class="kd-color-input">
                <label style="font-size:10px; color:var(--kd-text-muted);">STROKE</label>
                <input type="color" id="kdCtxStroke" value="#000000" class="kd-color-input">
                <input type="number" id="kdCtxStrokeWidth" class="kd-input-sm" value="1" min="0" max="20" style="width:40px;">
            </div>
            <div class="kd-toolbar-group" id="kdCtxZIndex" style="display:none;">
                <div class="kd-toolbar-divider"></div>
                <button class="kd-btn kd-btn-icon kd-btn-sm" id="kdCtxBringFront" title="Bring to Front"><i class="fa fa-angle-double-up"></i></button>
                <button class="kd-btn kd-btn-icon kd-btn-sm" id="kdCtxSendBack" title="Send to Back"><i class="fa fa-angle-double-down"></i></button>
            </div>
        </div>
    </div>
    <div class="kd-toolbar-right">
        <button class="kd-btn kd-btn-icon" id="kdBtnUndo" title="Undo (Ctrl+Z)" disabled><i class="fa fa-undo"></i></button>
        <button class="kd-btn kd-btn-icon" id="kdBtnRedo" title="Redo (Ctrl+Y)" disabled><i class="fa fa-repeat"></i></button>
        <div class="kd-toolbar-divider"></div>
        <button class="kd-btn kd-btn-sm" id="kdBtnPreview"><i class="fa fa-eye"></i> Preview</button>
        <button class="kd-btn kd-btn-sm kd-btn-margin" id="kdBtnMargin" title="Print Margins"><i class="fa fa-arrows-alt"></i> Margins<span class="kd-margin-dot"></span></button>
        <button class="kd-btn kd-btn-sm kd-btn-zone" id="kdBtnZones" title="Page Zones (Header/Footer/Body)"><i class="fa fa-columns"></i> Zones<span class="kd-zone-dot"></span></button>
        <button class="kd-btn kd-btn-sm kd-btn-primary" id="kdBtnSave"><i class="fa fa-save"></i> Save</button>
    </div>
</div>

<!-- ═══ MAIN LAYOUT ════════════════════════════════ -->
<div class="kd-main">

    <!-- LEFT: Tabbed Sidebar -->
    <div class="kd-sidebar" id="kdSidebar">

        <!-- Tab Bar -->
        <div class="kd-tab-bar" id="kdTabBar">
            <button class="kd-tab active" data-tab="props" title="Properties">
                <i class="fa fa-sliders"></i><span class="kd-tab-label">Properties</span>
            </button>
            <button class="kd-tab" data-tab="elements" title="Elements">
                <i class="fa fa-th-large"></i><span class="kd-tab-label">Elements</span>
            </button>
            <button class="kd-tab" data-tab="vars" title="Variables">
                <i class="fa fa-code"></i><span class="kd-tab-label">Variables</span>
            </button>
        </div>

        <!-- Tab Content: Properties -->
        <div class="kd-tab-content active" id="kdTabProps" data-tab="props">
            <div class="kd-props-body" id="kdPropsBody">
                <div class="kd-props-empty">
                    <i class="fa fa-mouse-pointer" style="font-size:24px;opacity:0.3;"></i>
                    <p>Select an element on the canvas to edit</p>
                </div>
            </div>
        </div>

        <!-- Tab Content: Elements -->
        <div class="kd-tab-content" id="kdTabElements" data-tab="elements">
            <div class="kd-tab-content-header">
                <span class="kd-drag-hint">drag onto canvas</span>
            </div>
            <div class="kd-element-grid" id="kdElementGrid">
                <div class="kd-element-item" data-tool="text" draggable="true">
                    <div class="kd-element-icon"><i class="fa fa-font"></i></div>
                    <span>Text</span>
                </div>
                <div class="kd-element-item" data-tool="heading" draggable="true">
                    <div class="kd-element-icon"><i class="fa fa-header"></i></div>
                    <span>Heading</span>
                </div>
                <div class="kd-element-item" data-tool="image" draggable="true">
                    <div class="kd-element-icon"><i class="fa fa-image"></i></div>
                    <span>Image</span>
                </div>
                <div class="kd-element-item" data-tool="rect" draggable="true">
                    <div class="kd-element-icon"><i class="fa fa-square-o"></i></div>
                    <span>Rectangle</span>
                </div>
                <div class="kd-element-item" data-tool="line" draggable="true">
                    <div class="kd-element-icon"><i class="fa fa-minus"></i></div>
                    <span>Line</span>
                </div>
                <div class="kd-element-item" data-tool="table" draggable="true">
                    <div class="kd-element-icon"><i class="fa fa-table"></i></div>
                    <span>Dyn Table</span>
                </div>
                <div class="kd-element-item" data-tool="stable" draggable="true">
                    <div class="kd-element-icon" style="position:relative;"><i class="fa fa-table"></i><span style="position:absolute;bottom:0;right:0;font-size:7px;font-weight:bold;color:var(--kd-accent);">S</span></div>
                    <span>Static Tbl</span>
                </div>
                <div class="kd-element-item" data-tool="spacer" draggable="true">
                    <div class="kd-element-icon"><i class="fa fa-arrows-v"></i></div>
                    <span>Spacer</span>
                </div>
                <div class="kd-element-item" data-tool="qrcode" draggable="true">
                    <div class="kd-element-icon"><i class="fa fa-qrcode"></i></div>
                    <span>QR Code</span>
                </div>
            </div>
        </div>

        <!-- Tab Content: Variables -->
        <div class="kd-tab-content" id="kdTabVars" data-tab="vars">
            <div class="kd-vars-body" id="kdVarsBody">
                <!-- Search + Create -->
                <div style="padding:6px 10px 4px 10px; display:flex; gap:4px;">
                    <input type="text" id="kdVarSearch" class="kd-prop-input" placeholder="Search variables..." style="flex:1; font-size:11px; padding:5px 8px;">
                    <button class="kd-btn kd-btn-sm" id="kdBtnCreateComputedVar" title="Create Computed Variable" style="padding:3px 8px; font-size:12px; white-space:nowrap;">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
                <div class="kd-vars-list" id="kdVarsList">
                    <div class="kd-props-empty" style="padding:15px;">
                        <i class="fa fa-spinner fa-spin" style="font-size:16px;opacity:0.4;"></i>
                        <p style="font-size:10px;">Loading variables...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="kd-sidebar-footer">
            <button class="kd-btn kd-btn-sm kd-btn-danger" id="kdBtnDelete" style="width:100%;">
                <i class="fa fa-trash"></i> Delete Selected
            </button>
        </div>
    </div>

    <!-- CENTER: Canvas Area -->
    <div class="kd-canvas-area" id="kdCanvasArea">
        <!-- Rulers -->
        <div class="kd-ruler-corner top-left">0</div>
        <div class="kd-ruler-corner top-right">0</div>
        <div class="kd-ruler-corner bottom-left">0</div>
        <div class="kd-ruler-corner bottom-right">0</div>
        <div class="kd-ruler kd-ruler-h kd-ruler-top" id="kdRulerTop"></div>
        <div class="kd-ruler kd-ruler-h kd-ruler-bottom" id="kdRulerBottom"></div>
        <div class="kd-ruler kd-ruler-v kd-ruler-left" id="kdRulerLeft"></div>
        <div class="kd-ruler kd-ruler-v kd-ruler-right" id="kdRulerRight"></div>

        <!-- Full-length Crosshair Indicators -->
        <div class="kd-crosshair kd-crosshair-v" id="kdIndL"></div>
        <div class="kd-crosshair kd-crosshair-v" id="kdIndR"></div>
        <div class="kd-crosshair kd-crosshair-h" id="kdIndT"></div>
        <div class="kd-crosshair kd-crosshair-h" id="kdIndB"></div>

        <div class="kd-canvas-scroll" id="kdCanvasScroll">
            <div class="kd-canvas-wrapper" id="kdCanvasWrapper">
                <div id="kdCanvasContainer"></div>
            </div>
        </div>

            </div>
        </div>
    </div>
</div>

<!-- ═══ TABLE CONFIG MODAL ════════════════════════════════ -->
<div class="kd-modal-overlay" id="kdTableModal" style="display:none;">
    <div class="kd-modal kd-modal-wide">
        <div class="kd-modal-header">
            <h3><i class="fa fa-table"></i> <span id="kdTblModalTitle">Configure Dynamic Table</span></h3>
            <button class="kd-btn kd-btn-icon" id="kdTableModalClose"><i class="fa fa-times"></i></button>
        </div>
        <div class="kd-modal-body">
            <div class="kd-form-row" style="margin-bottom:10px;border-bottom:1px solid rgba(255,255,255,0.08);padding-bottom:10px;">
                <div class="kd-form-field" style="flex:1;">
                    <label>Table Type</label>
                    <select id="kdTblType">
                        <option value="items">Sales Items</option>
                        <option value="old_gold">Old Gold / Exchange</option>
                        <option value="stone_details">Stone Details</option>
                        <option value="exchange">Exchange Items</option>
                        <option value="karigar">Karigar Items</option>
                        <option value="tax_summary">Tax Summary</option>
                        <option value="advance_amount">Advance Amount</option>
                        <option value="order_advance">Order Advance</option>
                        <option value="chit_adjustment">Chit Adjustment</option>
                        <option value="receipt_adjustment">Receipt Adjustment</option>
                        <option value="order_delivery">Order Delivery Items</option>
                        <option value="repair_order">Repair Order</option>
                        <option value="credit_collection">Credit Collection</option>
                        <option value="chit_pre_close">Chit Pre-Close</option>
                        <option value="purchase">Purchase Items</option>
                        <option value="tax_detail_breakdown">Tax Detail Breakdown</option>
                        <option value="payment">Payment Details</option>
                        <option value="payment_methods">Payment Methods (per transaction)</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div class="kd-form-field" style="flex:2;">
                    <label>Table Name <small style="opacity:0.5;">(used to identify in print engine)</small></label>
                    <input type="text" id="kdTblName" value="Sales Items" placeholder="e.g. Sales Items, Old Gold">
                </div>
            </div>
            <div class="kd-form-row">
                <div class="kd-form-field" style="flex:1;">
                    <label>Columns</label>
                    <input type="number" id="kdTblColCount" value="4" min="1" max="15">
                </div>
                <div class="kd-form-field" style="flex:1;">
                    <label>Border Width</label>
                    <input type="number" id="kdTblBorderWidth" value="1" min="0" max="5">
                </div>
                <div class="kd-form-field" style="flex:1;">
                    <label>Cell Padding</label>
                    <input type="number" id="kdTblCellPadding" value="2" min="0" max="20">
                </div>
                <div class="kd-form-field" style="flex:1;">
                    <label>Table Margin</label>
                    <input type="number" id="kdTblTableMargin" value="0" min="0" max="30">
                </div>
                <div class="kd-form-field" style="flex:4; flex-direction:row; align-items:center; gap:12px; flex-wrap:wrap; background:rgba(255,255,255,0.03); padding:10px; border-radius:4px;">
                    <label style="margin:0;"><input type="checkbox" id="kdTblShowOuterBorder" checked> Box</label>
                    <label style="margin:0;"><input type="checkbox" id="kdTblShowSideLines" checked> Sides</label>
                    <label style="margin:0;"><input type="checkbox" id="kdTblShowVLines" checked> Vertical</label>
                    <label style="margin:0;"><input type="checkbox" id="kdTblShowHLinesHeader" checked> H-Head</label>
                    <label style="margin:0;"><input type="checkbox" id="kdTblShowHLinesBody" checked> H-Body</label>
                    <label style="margin:0;"><input type="checkbox" id="kdTblShowHLinesFooter" checked> H-Foot</label>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px; margin-top:15px; border-top:1px solid rgba(255,255,255,0.1); padding-top:15px;">
                <!-- Header Styles -->
                <div class="kd-section-group">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <h5 style="margin:0; color:var(--kd-accent);">HEADER SECTION</h5>
                        <input type="checkbox" id="kdTblShowHeader" checked>
                    </div>
                    <div class="kd-form-grid-2">
                        <div class="kd-form-field">
                            <label>Font</label>
                            <select id="kdTblHeaderFont" class="kd-select-sm">
                                <option value="Arial">Arial</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Verdana">Verdana</option>
                            </select>
                        </div>
                        <div class="kd-form-field">
                            <label>Size</label>
                            <input type="number" id="kdTblHeaderSize" value="11" class="kd-input-sm">
                        </div>
                    </div>
                    <div class="kd-form-grid-2" style="margin-top:8px;">
                        <div class="kd-form-field">
                            <label>BG Color</label>
                            <input type="color" id="kdTblHeaderBg" value="#ffffff" class="kd-color-input" style="width:100%;">
                        </div>
                        <div class="kd-form-field">
                            <label>Text Color</label>
                            <input type="color" id="kdTblHeaderColor" value="#000000" class="kd-color-input" style="width:100%;">
                        </div>
                    </div>
                </div>

                <!-- Body Styles -->
                <div class="kd-section-group">
                    <h5 style="margin:0 0 8px 0; color:var(--kd-accent);">BODY SECTION</h5>
                    <div class="kd-form-grid-2">
                        <div class="kd-form-field">
                            <label>Font</label>
                            <select id="kdTblBodyFont" class="kd-select-sm">
                                <option value="Arial">Arial</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Verdana">Verdana</option>
                            </select>
                        </div>
                        <div class="kd-form-field">
                            <label>Size</label>
                            <input type="number" id="kdTblBodySize" value="11" class="kd-input-sm">
                        </div>
                    </div>
                    <div class="kd-form-field" style="margin-top:8px;">
                        <label>Body Text Color</label>
                        <input type="color" id="kdTblBodyColor" value="#000000" class="kd-color-input" style="width:100%;">
                    </div>
                </div>

                <!-- Footer Styles -->
                <div class="kd-section-group">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <h5 style="margin:0; color:var(--kd-accent);">FOOTER SECTION</h5>
                        <input type="checkbox" id="kdTblShowFooter">
                    </div>
                    <div class="kd-form-grid-2">
                        <div class="kd-form-field">
                            <label>Font</label>
                            <select id="kdTblFooterFont" class="kd-select-sm">
                                <option value="Arial">Arial</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Verdana">Verdana</option>
                            </select>
                        </div>
                        <div class="kd-form-field">
                            <label>Size</label>
                            <input type="number" id="kdTblFooterSize" value="11" class="kd-input-sm">
                        </div>
                    </div>
                    <div class="kd-form-grid-2" style="margin-top:8px;">
                         <div class="kd-form-field">
                            <label>Label</label>
                            <input type="text" id="kdTblFooterLabel" value="Total" class="kd-input-sm">
                        </div>
                        <div class="kd-form-field">
                            <label>ColSpan</label>
                            <input type="number" id="kdTblFooterColSpan" value="2" class="kd-input-sm">
                        </div>
                    </div>
                </div>
            </div>

            <h4 style="margin-top:12px;">Column Configuration</h4>
            <div id="kdTblColConfig"></div>

            <!-- ═══ SUB-ROW (STONE DETAILS) SECTION ═══ -->
            <div style="margin-top:15px; border-top:1px solid rgba(255,255,255,0.1); padding-top:12px;" id="kdSubRowSection">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <h4 style="margin:0; display:flex; align-items:center; gap:8px;">
                        <i class="fa fa-gem" style="color:var(--kd-accent);"></i> Sub-Row (Stone Details)
                    </h4>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:11px; color:var(--kd-text);">
                        <input type="checkbox" id="kdTblHasSubRows" style="accent-color:var(--kd-accent);">
                        Enable Sub-Rows
                    </label>
                </div>
                <div id="kdSubRowConfigWrap" style="display:none;">
                    <div style="font-size:10px; color:var(--kd-text-dim); padding:6px 8px; background:rgba(99,102,241,0.08); border-radius:4px; margin-bottom:10px;">
                        <i class="fa fa-info-circle" style="color:var(--kd-accent); margin-right:4px;"></i>
                        Each item row will be followed by its stone detail sub-rows. Configure the sub-row column mapping below — it must match the same column count as the main row.
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:8px; margin-bottom:10px;">
                        <div class="kd-form-field">
                            <label>Sub-Row Font</label>
                            <select id="kdTblSubBodyFont" class="kd-select-sm">
                                <option value="Arial">Arial</option>
                                <option value="Helvetica">Helvetica</option>
                                <option value="Times New Roman">Times New Roman</option>
                                <option value="Verdana">Verdana</option>
                            </select>
                        </div>
                        <div class="kd-form-field">
                            <label>Sub-Row Size</label>
                            <input type="number" id="kdTblSubBodySize" value="11" class="kd-input-sm" min="6" max="20">
                        </div>
                        <div class="kd-form-field">
                            <label>Sub-Row Color</label>
                            <input type="color" id="kdTblSubBodyColor" value="#000000" class="kd-color-input" style="width:100%;">
                        </div>
                        <div class="kd-form-field">
                            <label>Sub Columns</label>
                            <input type="number" id="kdTblSubColCount" value="4" min="1" max="15" class="kd-input-sm">
                        </div>
                    </div>
                    <h5 style="margin:8px 0 4px 0; color:var(--kd-accent);">Sub-Row Column Mapping</h5>
                    <div id="kdTblSubColConfig"></div>
                </div>
            </div>
        </div>
        <div class="kd-modal-footer">
            <button class="kd-btn" id="kdTblCancel">Cancel</button>
            <button class="kd-btn kd-btn-primary" id="kdTblApply"><i class="fa fa-check"></i> <span id="kdTblApplyText">Insert Table</span></button>
        </div>
    </div>
</div>

<!-- ═══ PREVIEW MODAL ════════════════════════════════════ -->
<div class="kd-modal-overlay" id="kdPreviewModal" style="display:none;">
    <div class="kd-modal kd-modal-preview">
        <div class="kd-modal-header">
            <h3><i class="fa fa-eye"></i> Print Preview</h3>
            <div style="display:flex;gap:8px;">
                <button class="kd-btn kd-btn-sm kd-btn-primary" id="kdPreviewPrint"><i class="fa fa-print"></i> Print</button>
                <button class="kd-btn kd-btn-icon" id="kdPreviewClose"><i class="fa fa-times"></i></button>
            </div>
        </div>
        <div class="kd-preview-body" id="kdPreviewBody">
            <div class="kd-preview-page" id="kdPreviewPage"></div>
        </div>
    </div>
</div>

<!-- Toast container -->
<div class="kd-toast-container" id="kdToasts"></div>

<!-- Margin Settings Popover (outside kd-body to avoid overflow:hidden clipping) -->
<div class="kd-margin-popover" id="kdMarginPopover">
    <h4><i class="fa fa-arrows-alt"></i> Print Margins (mm)</h4>
    <div class="kd-margin-grid">
        <div class="kd-margin-field">
            <label>Top</label>
            <input type="number" id="kdMarginTop" value="0" min="0" max="50" step="1">
        </div>
        <div class="kd-margin-field">
            <label>Right</label>
            <input type="number" id="kdMarginRight" value="0" min="0" max="50" step="1">
        </div>
        <div class="kd-margin-field">
            <label>Bottom</label>
            <input type="number" id="kdMarginBottom" value="0" min="0" max="50" step="1">
        </div>
        <div class="kd-margin-field">
            <label>Left</label>
            <input type="number" id="kdMarginLeft" value="0" min="0" max="50" step="1">
        </div>
    </div>
    <div class="kd-margin-preview" id="kdMarginPreview">No margins set</div>
</div>

<!-- Zone Settings Popover (outside kd-body to avoid overflow:hidden clipping) -->
<div class="kd-zone-popover" id="kdZonePopover">
    <h4><i class="fa fa-columns"></i> Page Zones (mm)</h4>
    <div class="kd-zone-grid">
        <div class="kd-zone-field kd-zone-header">
            <label><i class="fa fa-arrow-up"></i> Header Height</label>
            <div class="kd-zone-field-unit">
                <input type="number" id="kdZoneHeader" value="0" min="0" max="150" step="1">
                <span>mm</span>
            </div>
        </div>
        <div class="kd-zone-field kd-zone-footer">
            <label><i class="fa fa-arrow-down"></i> Footer Height</label>
            <div class="kd-zone-field-unit">
                <input type="number" id="kdZoneFooter" value="0" min="0" max="150" step="1">
                <span>mm</span>
            </div>
        </div>
    </div>
    <div class="kd-zone-preview" id="kdZonePreview">No zones set</div>
    <div class="kd-zone-hint">
        <i class="fa fa-info-circle"></i>
        Elements in the header/footer zone repeat on every printed page automatically.
    </div>
</div>

<!-- ═══ COMPUTED VARIABLE MODAL ════════════════════════════ -->
<div class="kd-modal-overlay" id="kdComputedVarModal" style="display:none;">
    <div class="kd-modal" style="max-width:520px;">
        <div class="kd-modal-header">
            <h3><i class="fa fa-calculator"></i> <span id="kdCvModalTitle">Create Computed Variable</span></h3>
            <button class="kd-btn kd-btn-icon" id="kdCvModalClose"><i class="fa fa-times"></i></button>
        </div>
        <div class="kd-modal-body" style="padding:15px;">
            <div style="margin-bottom:12px;">
                <label class="kd-prop-label">Variable Name</label>
                <input type="text" id="kdCvName" class="kd-prop-input" placeholder="e.g. total_after_discount" style="width:100%;">
                <small style="color:var(--kd-text-dim); font-size:10px;">Lowercase, underscores only. Will be used as {{variable_name}}</small>
            </div>
            <div style="margin-bottom:12px;">
                <label class="kd-prop-label">Label</label>
                <input type="text" id="kdCvLabel" class="kd-prop-input" placeholder="e.g. Total After Discount" style="width:100%;">
            </div>
            <div style="margin-bottom:12px; position:relative;">
                <label class="kd-prop-label">Formula</label>
                <input type="hidden" id="kdCvFormula">
                <div id="kdCvFormulaEditor" class="kd-formula-editor" contenteditable="true" spellcheck="false" data-placeholder="Type {{ to insert a variable, use +, -, *, / operators"></div>
                <div id="kdCvFormulaSuggestions" class="kd-formula-suggestions"></div>
                <small style="color:var(--kd-text-dim); font-size:10px;">Type a label name (e.g. <code style="color:var(--kd-accent);">net</code>, <code style="color:var(--kd-accent);">gross</code>) to search variables. Use +, -, *, / operators between tags.</small>
            </div>
            <div style="margin-bottom:8px; padding:8px; background:rgba(99,102,241,0.1); border-radius:6px; font-size:11px; color:var(--kd-text-dim);">
                <i class="fa fa-info-circle" style="color:var(--kd-accent);"></i>
                <strong>Example:</strong> <code style="color:var(--kd-accent);">{{sub_total}} - {{discount}}</code> → If sub_total=1000 and discount=100, result = 900
            </div>
        </div>
        <div class="kd-modal-footer" style="padding:10px 15px; display:flex; gap:8px; justify-content:flex-end;">
            <button class="kd-btn kd-btn-sm" id="kdCvCancel">Cancel</button>
            <button class="kd-btn kd-btn-sm kd-btn-primary" id="kdCvSave"><i class="fa fa-check"></i> Save Variable</button>
        </div>
    </div>
</div>

<!-- ═══ TEMPLATE DATA ════════════════════════════════════ -->
<script>
window.KD_CONFIG = {
    templateId: <?= (int)$template['id_template'] ?>,
    templateCode: <?= json_encode($template['template_code']) ?>,
    templateCategory: <?= json_encode($template['template_category']) ?>,
    paperSize: <?= json_encode($template['paper_size'] ?? 'A4') ?>,
    pageOrientation: <?= json_encode($template['page_orientation'] ?? 'portrait') ?>,
    baseUrl: <?= json_encode(rtrim(base_url(), '/') . '/') ?>,
    templateCategory: <?= json_encode($template['template_category'] ?? '1') ?>,
    fieldsUrl: <?= json_encode(site_url('print-templates/fields-v2/' . ($template['template_category'] ?? '1'))) ?>,
    loadDesignUrl: <?= json_encode(site_url('print-templates/load-design/' . $template['id_template'])) ?>,
    saveDesignUrl: <?= json_encode(site_url('print-templates/save-design/' . $template['id_template'])) ?>,
    computedVarsUrl: <?= json_encode(site_url('print-templates/computed-vars/' . $template['id_template'])) ?>,
    saveComputedVarUrl: <?= json_encode(site_url('print-templates/save-computed-var/' . $template['id_template'])) ?>,
    deleteComputedVarUrl: <?= json_encode(site_url('print-templates/delete-computed-var/' . $template['id_template'])) ?>
};
</script>

<!-- Konva.js CDN -->
<script src="https://unpkg.com/konva@9/konva.min.js"></script>

<!-- Designer Script -->
<script src="<?= base_url('assets/js/print-designer/konva-designer.js') ?>?v=<?= time() ?>"></script>

</body>
</html>
