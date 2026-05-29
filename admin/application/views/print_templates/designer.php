<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Template Designer — <?php echo htmlspecialchars($template['template_name']); ?></title>

    <!-- Block Editor Styles -->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/block-editor.css?v=<?= time() ?>">

    <!-- GrapesJS Styles (for block designer modal) -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.13/dist/css/grapes.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SortableJS for drag-and-drop (block editor) -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
    /* ── Block Designer Modal ── */
    .bd-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.8);
        z-index: 9000;
    }
    .bd-overlay.active { display: flex; flex-direction: column; }

    .bd-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;
        background: #f9fafc;
        border-bottom: 1px solid #d2d6de;
        flex-shrink: 0;
    }
    .bd-header-title {
        color: #333333;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .bd-header-title .bd-block-label {
        color: #3c8dbc;
        background: #eef1f6;
        padding: 3px 12px;
        border-radius: 4px;
        font-size: 13px;
        border: 1px dotted #3c8dbc;
    }
    .bd-header-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .bd-btn {
        padding: 8px 18px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .bd-btn-cancel {
        background: #ffffff;
        color: #444444;
        border: 1px solid #d2d6de;
    }
    .bd-btn-cancel:hover { background: #f4f4f4; color: #333333; }
    .bd-btn-save {
        background: #3c8dbc;
        color: #fff;
    }
    .bd-btn-save:hover { background: #367fa9; }

    .bd-body {
        flex: 1;
        overflow: hidden;
        position: relative;
    }

    /* ── GrapesJS Custom Layout ── */
    .bd-layout {
        display: flex;
        height: 100%;
        background: #f4f6f9; /* AdminLTE Background */
    }
    .bd-sidebar {
        width: 260px;
        background: #ffffff;
        border-right: 1px solid #d2d6de;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }
    .bd-sidebar-right {
        border-right: none;
        border-left: 1px solid #d2d6de;
        width: 280px; /* Slightly wider for styles/traits */
    }
    .bd-sidebar-header {
        padding: 10px 15px;
        font-weight: 600;
        color: #333;
        background: #f9fafc;
        border-bottom: 1px solid #d2d6de;
        font-size: 13px;
        text-transform: uppercase;
        flex-shrink: 0;
    }
    .bd-sidebar-content {
        flex: 1;
        overflow-y: auto;
        position: relative;
    }
    
    .bd-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .bd-toolbar {
        display: flex;
        justify-content: space-between;
        padding: 8px 15px;
        background: #ffffff;
        border-bottom: 1px solid #d2d6de;
        align-items: center;
        flex-shrink: 0;
    }
    .bd-devices, .bd-actions {
        display: flex;
        gap: 5px;
    }
    .bd-devices .be-btn, .bd-actions .be-btn {
        padding: 5px 10px;
        font-size: 13px;
    }
    .bd-devices .be-btn.active {
        background: #eef1f6;
        border-color: #3c8dbc;
        color: #3c8dbc;
    }
    
    /* Tabs inside right sidebar */
    .bd-tabs {
        display: flex;
        background: #f9fafc;
        border-bottom: 1px solid #d2d6de;
        flex-shrink: 0;
    }
    .bd-tab {
        flex: 1;
        text-align: center;
        padding: 10px 5px;
        font-size: 13px;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        border-right: 1px solid #d2d6de;
    }
    .bd-tab:last-child {
        border-right: none;
    }
    .bd-tab.active {
        color: #3c8dbc;
        border-bottom: 2px solid #3c8dbc;
        background: #ffffff;
    }
    .bd-tab-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .bd-tab-pane {
        display: none;
        flex: 1;
        height: 100%;
    }
    .bd-tab-pane.active {
        display: block;
    }
    
    #gjs {
        flex: 1;
        overflow: hidden;
    }

    /* Remove GrapesJS dark theme overrides */

    /* Placeholder tags in GrapesJS canvas */
    .bd-body .ph-tag {
        display: inline-block;
        background: #dbeafe;
        color: #1e40af;
        padding: 1px 6px;
        border-radius: 3px;
        font-family: monospace;
        font-size: 11px;
        border: 1px dashed #93c5fd;
        cursor: default;
    }

    /* ── Customize Button on Block Cards ── */
    .be-card-customize {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border: 1px solid rgba(203,166,247,0.3);
        background: rgba(203,166,247,0.08);
        color: #cba6f7;
        border-radius: 5px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .be-card-customize:hover {
        background: rgba(203,166,247,0.18);
        border-color: rgba(203,166,247,0.5);
    }
    .be-card-customize .cust-icon { font-size: 13px; }

    /* ── Custom Save Modal ── */
    #custom-save-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        z-index: 100000;
        align-items: center;
        justify-content: center;
    }
    #custom-save-modal.active {
        display: flex;
    }
    .csm-content {
        background: #ffffff;
        border: 1px solid #d2d6de;
        border-radius: 4px;
        width: 400px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        animation: csm-slide-up 0.2s ease-out;
    }
    @keyframes csm-slide-up {
        0% { transform: translateY(20px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    .csm-title {
        color: #333333;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .csm-input {
        width: 100%;
        background: #ffffff;
        border: 1px solid #d2d6de;
        color: #333333;
        padding: 10px 12px;
        border-radius: 4px;
        margin-bottom: 20px;
        outline: none;
    }
    .csm-input:focus {
        border-color: #3c8dbc;
    }
    .csm-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    /* ── Left panel: Blocks / Variables tabs ── */
    .be-palette-tabs {
        display: flex;
        border-bottom: 1px solid #d2d6de;
        background: #f9fafc;
        flex-shrink: 0;
    }
    .be-palette-tab {
        flex: 1;
        text-align: center;
        padding: 9px 4px;
        font-size: 12px;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.15s;
        user-select: none;
    }
    .be-palette-tab.active {
        color: #3c8dbc;
        border-bottom: 2px solid #3c8dbc;
        background: #fff;
    }

    /* Variable Search */
    .bv-search-wrap {
        padding: 8px;
        border-bottom: 1px solid #eee;
        background: #fff;
        flex-shrink: 0;
    }
    .bv-search {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #d2d6de;
        border-radius: 4px;
        font-size: 12px;
        outline: none;
        box-sizing: border-box;
        color: #333;
    }
    .bv-search:focus { border-color: #3c8dbc; }

    /* Variable list container */
    #bv-list {
        flex: 1;
        overflow-y: auto;
        padding: 6px 8px 20px;
    }

    /* Group header */
    .bv-group-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #999;
        margin: 12px 0 5px;
        padding-left: 2px;
    }
    .bv-group-label:first-child { margin-top: 4px; }

    /* Individual variable chip */
    .bv-chip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 8px;
        margin-bottom: 3px;
        background: #f0f7ff;
        border: 1px solid #c8e1f7;
        border-radius: 4px;
        cursor: grab;
        font-size: 11px;
        color: #1565c0;
        transition: background 0.15s, transform 0.1s;
        user-select: none;
    }
    .bv-chip:hover {
        background: #dbeafe;
        border-color: #3c8dbc;
        transform: translateX(2px);
    }
    .bv-chip:active { transform: scale(0.97); cursor: grabbing; }
    .bv-chip.bv-boolean { background: #fff3e0; border-color: #f9a825; color: #7b4f00; }
    .bv-chip.bv-number  { background: #e8f5e9; border-color: #66bb6a; color: #1b5e20; }
    .bv-chip-tag { font-family: monospace; font-weight: 600; font-size: 11px; }
    .bv-chip-desc { font-size: 10px; color: #777; max-width: 105px; overflow: hidden;
                    white-space: nowrap; text-overflow: ellipsis; text-align: right; }
    .bv-chip.copied { background: #d4edda; border-color: #28a745; color: #155724; }

    /* Toast */
    #bv-toast {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: #333;
        color: #fff;
        padding: 7px 16px;
        border-radius: 20px;
        font-size: 12px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s, transform 0.2s;
        z-index: 99999;
    }
    #bv-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

    /* No-results message */
    .bv-no-result {
        text-align: center;
        color: #aaa;
        font-size: 12px;
        padding: 20px 8px;
    }
    /* ── Table Cell Properties context bar ── */
    #tc-bar {
        display: none;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #fffbea;
        border-bottom: 2px solid #f9a825;
        flex-shrink: 0;
        flex-wrap: wrap;
        font-size: 12px;
        animation: tc-bar-in 0.15s ease-out;
    }
    @keyframes tc-bar-in {
        from { opacity:0; transform:translateY(-4px); }
        to   { opacity:1; transform:translateY(0); }
    }
    #tc-bar.active { display: flex; }
    .tc-label {
        font-weight: 700;
        color: #7b4f00;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        white-space: nowrap;
    }
    .tc-separator {
        width: 1px;
        height: 20px;
        background: #f0c040;
        margin: 0 4px;
    }
    .tc-field {
        display: flex;
        align-items: center;
        gap: 4px;
        background: #fff;
        border: 1px solid #f9a825;
        border-radius: 4px;
        padding: 2px 6px;
    }
    .tc-field label {
        font-size: 11px;
        color: #666;
        margin: 0;
        white-space: nowrap;
    }
    .tc-field input[type=number] {
        width: 42px;
        border: none;
        outline: none;
        font-size: 13px;
        font-weight: 700;
        color: #333;
        text-align: center;
        background: transparent;
        -moz-appearance: textfield;
    }
    .tc-field input[type=number]::-webkit-inner-spin-button,
    .tc-field input[type=number]::-webkit-outer-spin-button { opacity: 1; }
    .tc-btn {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 4px 9px;
        border: 1px solid #e0c060;
        background: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;
        color: #5a3e00;
        font-weight: 500;
        white-space: nowrap;
        transition: background 0.15s;
    }
    .tc-btn:hover { background: #fff9e0; border-color: #f9a825; }
    .tc-btn.tc-danger { border-color: #f44336; color: #c62828; }
    .tc-btn.tc-danger:hover { background: #fff0f0; }
    .tc-btn.tc-active-cell {
        background: #e8f5e9;
        border-color: #4caf50;
        color: #2e7d32;
        font-weight: 700;
    }
    #tc-cell-info {
        font-size: 10px;
        color: #999;
        margin-left: auto;
        white-space: nowrap;
    }
    /* Main Canvas - Conditional Wrapper */
    .bvp-conditional-wrapper {
        border: 2px dashed #f39c12 !important;
        background: #fff8e1 !important;
        padding: 5px !important;
        margin: 5px 0 !important;
        border-radius: 4px;
        position: relative;
    }
    .bvp-cond-tag {
        font-family: monospace;
        font-size: 10px;
        background: #f39c12;
        color: #fff;
        padding: 2px 6px;
        border-radius: 2px;
        display: inline-block;
        font-weight: bold;
        position: relative;
        z-index: 2;
    }
    .bvp-cond-tag.header {
        margin-bottom: 5px;
    }
    .bvp-cond-tag.footer {
        margin-top: 5px;
    }
    .bvp-cond-content {
        padding: 4px;
        background: #fff;
        border: 1px solid #ffeeba;
        border-radius: 2px;
    }
    /* GJS Designer - Conditional Elements */
    .gjs-conditional-element {
        outline: 1px dashed #f39c12 !important;
        outline-offset: -1px;
        position: relative;
    }
    .gjs-conditional-element::after {
        content: 'Requirement Active';
        position: absolute;
        top: 0;
        right: 0;
        background: #f39c12;
        color: #fff;
        font-size: 8px;
        padding: 1px 3px;
        z-index: 10;
        pointer-events: none;
    }
    </style>
</head>
<body>

<!-- ── Toolbar ── -->
<div class="be-toolbar">
    <div class="be-toolbar-title">
        📄 <?php echo htmlspecialchars($template['template_name']); ?>
        <span style="font-weight:400; font-size:12px; color:var(--be-text-muted); margin-left:8px;">
            (<?php echo htmlspecialchars($template['paper_size'] ?? 'A4'); ?> · <?php echo htmlspecialchars($template['orientation'] ?? 'Portrait'); ?>)
        </span>
    </div>
    <div class="be-toolbar-actions">
        <button id="be-btn-undo" class="be-btn be-btn-outline" title="Undo (Ctrl+Z)" disabled>↩ Undo</button>
        <button id="be-btn-redo" class="be-btn be-btn-outline" title="Redo (Ctrl+Y)" disabled>↪ Redo</button>
        <a href="<?php echo site_url('print-templates'); ?>" class="be-btn be-btn-outline" style="text-decoration:none;">← Back to List</a>
        <button id="be-btn-preview" class="be-btn be-btn-outline" title="Open preview in new tab">👁 Preview</button>
        <button id="be-btn-create-block" class="be-btn be-btn-outline" title="Create a new reusable block">➕ New Block</button>
        <button id="be-btn-save" class="be-btn be-btn-primary">💾 Save</button>
    </div>
</div>

<!-- ── Customize Hint Banner (shown once, dismissible) ── -->
<div id="be-customize-hint" style="
    display: flex; align-items: center; gap: 10px;
    background: linear-gradient(90deg, #6366f1 0%, #3c8dbc 100%);
    color: #fff; padding: 8px 18px; font-size: 12px; font-weight: 500;
    flex-shrink: 0;
">
    <span style="font-size:18px;">🎨</span>
    <span>
        <strong>Tip:</strong> Click the
        <strong style="background:rgba(255,255,255,0.2); padding:1px 8px; border-radius:10px; margin:0 4px;">🎨 Customize</strong>
        button on any block card below to open the full Visual Block Designer.
    </span>
    <button onclick="document.getElementById('be-customize-hint').remove(); localStorage.setItem('hide_customize_hint','1');"
        style="margin-left:auto; background:rgba(255,255,255,0.2); border:none; color:#fff; border-radius:50%; width:22px; height:22px; cursor:pointer; font-size:14px; line-height:22px; text-align:center; padding:0;" title="Dismiss">✕</button>
</div>
<script>if (localStorage.getItem('hide_customize_hint')) { document.getElementById('be-customize-hint').style.display='none'; }</script>

<!-- ── Block Editor (Template Assembler) ── -->
<div class="be-editor" id="be-editor-simple">

    <!-- Left: Tabbed Palette (Blocks + Variables) -->
    <div class="be-palette" style="display:flex; flex-direction:column; overflow:hidden;">
        <!-- Tabs -->
        <div class="be-palette-tabs">
            <div class="be-palette-tab active" id="bpt-blocks">🧱 Blocks</div>
            <div class="be-palette-tab" id="bpt-vars">🔖 Variables</div>
        </div>

        <!-- Blocks content -->
        <div id="bpt-blocks-panel" style="flex:1; overflow-y:auto;">
            <div id="be-palette-list"></div>
        </div>

        <!-- Variables content (hidden initially) -->
        <div id="bpt-vars-panel" style="display:none; flex:1; flex-direction:column; overflow:hidden;">
            <div class="bv-search-wrap">
                <input type="text" class="bv-search" id="bv-search"
                       placeholder="🔍 Search variable..." autocomplete="off">
            </div>
            <div id="bv-list"></div>
        </div>
    </div>

    <!-- Center: Canvas + Preview -->
    <div class="be-center">
        <div id="be-canvas" class="be-canvas"></div>
        <div id="be-preview-toggle" class="be-preview-toggle">
            <span>📐 Live Preview</span>
            <span class="arrow">▼</span>
        </div>
        <div id="be-preview-pane" class="be-preview-pane">
            <iframe id="be-preview-frame" srcdoc="<p style='text-align:center;color:#aaa;padding:40px;'>Add blocks to see preview</p>"></iframe>
        </div>
    </div>

    <!-- Right: Settings Panel -->
    <div id="be-settings" class="be-settings">
        <div class="be-settings-empty">
            <div class="icon">⚙️</div>
            <div>Select a block to<br>configure its settings</div>
        </div>
    </div>

</div>

<!-- Variables toast notification -->
<div id="bv-toast">Copied to clipboard!</div>

<!-- ── Block Designer Modal (GrapesJS) ── -->
<div id="block-designer-overlay" class="bd-overlay">
    <div class="bd-header">
        <div class="bd-header-title">
            🎨 Block Designer
            <span class="bd-block-label" id="bd-block-label">Company Header</span>
        </div>
        <div class="bd-header-actions">
            <button class="bd-btn bd-btn-cancel" id="bd-cancel">✕ Cancel</button>
            <button class="bd-btn bd-btn-primary" id="bd-save-custom" style="margin-right:10px;">💾 Save as Block</button>
            <button class="bd-btn bd-btn-save" id="bd-save">💾 Apply to Template</button>
        </div>
    </div>
    <div class="bd-body">
        <div class="bd-layout">
            <!-- Left Sidebar: Blocks + Variables tabs -->
            <div class="bd-sidebar bd-sidebar-left" style="display:flex; flex-direction:column; overflow:hidden; width:280px;">

                <!-- Tab switcher -->
                <div class="be-palette-tabs" style="border-bottom:1px solid #d2d6de;">
                    <div class="be-palette-tab active" id="gjs-tab-blocks">🧱 Blocks</div>
                    <div class="be-palette-tab" id="gjs-tab-vars">🔖 Variables</div>
                </div>

                <!-- Blocks panel -->
                <div id="gjs-tab-blocks-panel" class="bd-sidebar-content" style="flex:1; overflow-y:auto;">
                    <div id="gjs-blocks"></div>
                </div>

                <!-- Variables panel -->
                <div id="gjs-tab-vars-panel" style="display:none; flex:1; flex-direction:column; overflow:hidden;">
                    <div class="bv-search-wrap">
                        <input type="text" class="bv-search" id="gjs-bv-search"
                               placeholder="🔍 Search variable..." autocomplete="off">
                    </div>
                    <div id="gjs-bv-list" style="flex:1; overflow-y:auto; padding:6px 8px 20px;"></div>
                </div>

            </div>

            <!-- Center: Canvas & Toolbar -->
            <div class="bd-main">
                <div class="bd-toolbar">
                    <div class="bd-devices">
                        <button class="be-btn be-btn-outline active" id="gjs-btn-desktop" title="Desktop">🖥 Desktop</button>
                        <button class="be-btn be-btn-outline" id="gjs-btn-tablet" title="Tablet">📱 Tablet</button>
                        <button class="be-btn be-btn-outline" id="gjs-btn-mobile" title="Mobile">📱 Mobile</button>
                    </div>
                    <div class="bd-actions">
                        <button class="be-btn be-btn-outline" id="gjs-btn-code" title="Edit HTML Source" style="background:#fff3cd; border-color:#ffc107; color:#856404; font-weight:600;">&lt;/&gt; Edit Code</button>
                        <button class="be-btn be-btn-outline" id="gjs-btn-undo" title="Undo">↩ Undo</button>
                        <button class="be-btn be-btn-outline" id="gjs-btn-redo" title="Redo">↪ Redo</button>
                        <button class="be-btn be-btn-outline" id="gjs-btn-clear" title="Clear Canvas">🗑 Clear</button>
                    </div>
                </div>

                <!-- ── Table Cell Properties Bar ── -->
                <div id="tc-bar">
                    <span class="tc-label">📐 Cell</span>
                    <div class="tc-separator"></div>

                    <!-- Colspan -->
                    <div class="tc-field" title="Number of columns this cell spans">
                        <label>Colspan</label>
                        <input type="number" id="tc-colspan" min="1" max="20" value="1">
                    </div>

                    <!-- Rowspan -->
                    <div class="tc-field" title="Number of rows this cell spans">
                        <label>Rowspan</label>
                        <input type="number" id="tc-rowspan" min="1" max="20" value="1">
                    </div>

                    <button class="tc-btn" id="tc-apply" title="Apply colspan/rowspan">✔ Apply</button>

                    <div class="tc-separator"></div>

                    <!-- Row operations -->
                    <span class="tc-label" style="color:#1565c0;">Row</span>
                    <button class="tc-btn" id="tc-row-before" title="Insert row above">↑ Before</button>
                    <button class="tc-btn" id="tc-row-after"  title="Insert row below">↓ After</button>
                    <button class="tc-btn tc-danger" id="tc-row-delete" title="Delete this row">✕ Row</button>

                    <div class="tc-separator"></div>

                    <!-- Column operations -->
                    <span class="tc-label" style="color:#1b5e20;">Col</span>
                    <button class="tc-btn" id="tc-col-before" title="Insert column before">← Before</button>
                    <button class="tc-btn" id="tc-col-after"  title="Insert column after">→ After</button>
                    <button class="tc-btn tc-danger" id="tc-col-delete" title="Delete this column">✕ Col</button>

                    <span id="tc-cell-info"></span>
                </div>

                <div id="gjs"></div>
            </div>

            <!-- Code Editor Modal -->
            <div id="code-editor-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:10000; justify-content:center; align-items:center;">
                <div style="background:#1e1e2e; border-radius:8px; width:80%; max-width:900px; max-height:85vh; display:flex; flex-direction:column; box-shadow:0 8px 32px rgba(0,0,0,0.4);">
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 20px; border-bottom:1px solid #444; background:#2d2d3d; border-radius:8px 8px 0 0;">
                        <span style="color:#fff; font-weight:600; font-size:15px;">&lt;/&gt; HTML Source Editor</span>
                        <span style="color:#aaa; font-size:12px;">Edit variable names, table structure, loop tags directly</span>
                    </div>
                    <textarea id="code-editor-textarea" style="flex:1; width:100%; min-height:400px; background:#1e1e2e; color:#d4d4d4; font-family:'Cascadia Code','Fira Code','Consolas',monospace; font-size:13px; line-height:1.6; padding:15px 20px; border:none; outline:none; resize:none; box-sizing:border-box; tab-size:4;"></textarea>
                    <div style="display:flex; justify-content:flex-end; gap:10px; padding:12px 20px; border-top:1px solid #444; background:#2d2d3d; border-radius:0 0 8px 8px;">
                        <button id="code-editor-cancel" class="be-btn" style="padding:8px 20px; background:#555; color:#fff; border:none; border-radius:4px; cursor:pointer;">Cancel</button>
                        <button id="code-editor-apply" class="be-btn" style="padding:8px 20px; background:#28a745; color:#fff; border:none; border-radius:4px; cursor:pointer; font-weight:600;">✓ Apply Changes</button>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Settings & Tabs -->
            <div class="bd-sidebar bd-sidebar-right">
                <div class="bd-tabs">
                    <div class="bd-tab active" data-tab="gjs-styles">Styles</div>
                    <div class="bd-tab" data-tab="gjs-traits">Settings</div>
                    <div class="bd-tab" data-tab="gjs-layers">Layers</div>
                    <div class="bd-tab" data-tab="gjs-vars">Variables</div>
                </div>
                <div class="bd-tab-content">
                    <div id="gjs-styles" class="bd-tab-pane active bd-sidebar-content"></div>
                    <div id="gjs-traits" class="bd-tab-pane bd-sidebar-content"></div>
                    <div id="gjs-layers" class="bd-tab-pane bd-sidebar-content"></div>
                    <div id="gjs-vars" class="bd-tab-pane bd-sidebar-content">
                        <div class="bd-vars-container" style="padding:8px;">
                            <p class="bd-vars-help" style="font-size:11px;color:#666;margin:0 0 8px;">Click any tag to copy it. Drag into the HTML editor.</p>

                            <div class="bd-var-group">🏢 Company</div>
                            <div class="bd-var-item" data-tag="{{company_name}}">{{company_name}} <small>Shop name</small></div>
                            <div class="bd-var-item" data-tag="{{company_address}}">{{company_address}} <small>Address</small></div>
                            <div class="bd-var-item" data-tag="{{company_mobile}}">{{company_mobile}} <small>Mobile</small></div>
                            <div class="bd-var-item" data-tag="{{company_gstin}}">{{company_gstin}} <small>GSTIN</small></div>
                            <div class="bd-var-item" data-tag="{{company_pan}}">{{company_pan}} <small>PAN</small></div>

                            <div class="bd-var-group">📄 Bill / Invoice</div>
                            <div class="bd-var-item" data-tag="{{invoice_no}}">{{invoice_no}} <small>Invoice No.</small></div>
                            <div class="bd-var-item" data-tag="{{bill_date}}">{{bill_date}} <small>Date</small></div>
                            <div class="bd-var-item" data-tag="{{bill_time}}">{{bill_time}} <small>Time</small></div>
                            <div class="bd-var-item" data-tag="{{title}}">{{title}} <small>Bill type label</small></div>
                            <div class="bd-var-item" data-tag="{{remark}}">{{remark}} <small>Remark</small></div>

                            <div class="bd-var-group">👤 Customer</div>
                            <div class="bd-var-item" data-tag="{{customer_name}}">{{customer_name}} <small>Name</small></div>
                            <div class="bd-var-item" data-tag="{{customer_mobile}}">{{customer_mobile}} <small>Mobile</small></div>
                            <div class="bd-var-item" data-tag="{{customer_address}}">{{customer_address}} <small>Address</small></div>
                            <div class="bd-var-item" data-tag="{{customer_city}}">{{customer_city}} <small>City</small></div>
                            <div class="bd-var-item" data-tag="{{customer_pincode}}">{{customer_pincode}} <small>PIN</small></div>
                            <div class="bd-var-item" data-tag="{{customer_gstin}}">{{customer_gstin}} <small>GSTIN</small></div>
                            <div class="bd-var-item" data-tag="{{customer_pan}}">{{customer_pan}} <small>PAN</small></div>

                            <div class="bd-var-group">🥇 Metal Rates</div>
                            <div class="bd-var-item" data-tag="{{gold_rate}}">{{gold_rate}} <small>Gold 22kt/gm</small></div>
                            <div class="bd-var-item" data-tag="{{gold_rate_18ct}}">{{gold_rate_18ct}} <small>Gold 18kt/gm</small></div>
                            <div class="bd-var-item" data-tag="{{silver_rate}}">{{silver_rate}} <small>Silver/gm</small></div>

                            <div class="bd-var-group">💰 Totals</div>
                            <div class="bd-var-item" data-tag="{{sub_total}}">{{sub_total}} <small>Subtotal</small></div>
                            <div class="bd-var-item" data-tag="{{grand_total}}">{{grand_total}} <small>Grand total</small></div>
                            <div class="bd-var-item" data-tag="{{cgst_amount}}">{{cgst_amount}} <small>CGST</small></div>
                            <div class="bd-var-item" data-tag="{{sgst_amount}}">{{sgst_amount}} <small>SGST</small></div>
                            <div class="bd-var-item" data-tag="{{igst_amount}}">{{igst_amount}} <small>IGST</small></div>
                            <div class="bd-var-item" data-tag="{{round_off}}">{{round_off}} <small>Round off</small></div>
                            <div class="bd-var-item" data-tag="{{amount_in_words}}">{{amount_in_words}} <small>In words</small></div>
                            <div class="bd-var-item" data-tag="{{total_paid}}">{{total_paid}} <small>Paid</small></div>
                            <div class="bd-var-item" data-tag="{{balance_amount}}">{{balance_amount}} <small>Balance</small></div>

                            <div class="bd-var-group">💳 Payment Modes</div>
                            <div class="bd-var-item" data-tag="{{pay_cash}}">{{pay_cash}} <small>Cash</small></div>
                            <div class="bd-var-item" data-tag="{{pay_card}}">{{pay_card}} <small>Card</small></div>
                            <div class="bd-var-item" data-tag="{{pay_upi}}">{{pay_upi}} <small>UPI</small></div>
                            <div class="bd-var-item" data-tag="{{pay_cheque}}">{{pay_cheque}} <small>Cheque</small></div>
                            <div class="bd-var-item" data-tag="{{pay_neft}}">{{pay_neft}} <small>NEFT</small></div>

                            <div class="bd-var-group">👨‍💼 Employee</div>
                            <div class="bd-var-item" data-tag="{{billed_by}}">{{billed_by}} <small>Billed by</small></div>
                            <div class="bd-var-item" data-tag="{{sales_emp}}">{{sales_emp}} <small>Salesperson</small></div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Custom Save Modal ── -->
<div id="custom-save-modal">
    <div class="csm-content">
        <div class="csm-title">Save Custom Block</div>
        <input type="text" id="csm-block-name" class="csm-input" placeholder="e.g., Hero Section, Footer v2..." autocomplete="off">
        <div class="csm-actions">
            <button class="bd-btn bd-btn-cancel" id="csm-btn-cancel">Cancel</button>
            <button class="bd-btn bd-btn-save" id="csm-btn-save">Save Block</button>
        </div>
    </div>
</div>

<!-- ── Scripts ── -->
<script src="<?php echo base_url();?>assets/js/print-designer/block-definitions.js"></script>
<script>
    const TEMPLATE_CONFIG = {
        templateId: <?php echo (int)$template['id_template']; ?>,
        templateName: <?php echo json_encode($template['template_name']); ?>,
        baseUrl: '<?php echo base_url(); ?>index.php/',
        categoryId: <?php echo (int)($template['category_id'] ?? $template['category'] ?? 0); ?>
    };
    const API_URL = '<?php echo base_url(); ?>index.php/';
</script>

<!-- ── Bill Variables Registry (from bill.php config) ── -->
<script>
(function() {
'use strict';

/* PHP-generated variable definitions */
<?php
if (!function_exists('get_bill_variables')) {
    $bill_cfg = APPPATH . 'config/bill.php';
    if (file_exists($bill_cfg)) require_once $bill_cfg;
}
$bill_vars = function_exists('get_bill_variables') ? get_bill_variables() : [];
echo 'var BILL_VARIABLES = ' . json_encode($bill_vars, JSON_UNESCAPED_UNICODE) . ';';
?>

var GROUP_ICONS = {
    'Company':'🏢','Bill':'📄','Customer':'👤',
    'Metal Rates':'🥇','Totals':'💰','Payment':'💳',
    'Employee':'👨‍💼','Flags':'🚩',
    'Sales Items':'🛍️',
    'Adjustments':'🔄','Reference Numbers':'🔖',
    'Bill Summary':'📊','Old/Purchase Metal':'♻️',
    'Order':'📋','Repair':'🔧','Chit/Scheme':'🎴',
    'Credit Collection':'💳','Sales Return':'↩️'
};

var _toastTimer = null;
function showVarToast(msg) {
    var t = document.getElementById('bv-toast');
    if (!t) return;
    t.textContent = msg || 'Copied!';
    t.classList.add('show');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(function() { t.classList.remove('show'); }, 1800);
}

function copyVarText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).catch(function() { fallbackVarCopy(text); });
    } else { fallbackVarCopy(text); }
}
function fallbackVarCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;top:-999px;left:-999px;opacity:0;';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); } catch(e) {}
    document.body.removeChild(ta);
}

function insertAtCursorVar(el, text) {
    if (!el) return;
    var s = el.selectionStart, e = el.selectionEnd, v = el.value;
    el.value = v.substring(0, s) + text + v.substring(e);
    el.selectionStart = el.selectionEnd = s + text.length;
    el.dispatchEvent(new Event('input', { bubbles: true }));
}

function renderBillVariables(filter) {
    var list = document.getElementById('bv-list');
    if (!list) return;
    list.innerHTML = '';
    filter = (filter || '').toLowerCase().trim();

    var groups = {}, order = [];
    BILL_VARIABLES.forEach(function(v) {
        var g = v.group || 'Other';
        if (!groups[g]) { groups[g] = []; order.push(g); }
        groups[g].push(v);
    });

    var found = 0;
    order.forEach(function(grp) {
        var vars = groups[grp];
        if (filter) {
            vars = vars.filter(function(v) {
                return v.column.toLowerCase().indexOf(filter) !== -1 ||
                       (v.description||'').toLowerCase().indexOf(filter) !== -1 ||
                       grp.toLowerCase().indexOf(filter) !== -1;
            });
        }
        if (!vars.length) return;
        found += vars.length;

        var lbl = document.createElement('div');
        lbl.className = 'bv-group-label';
        lbl.textContent = (GROUP_ICONS[grp] || '') + ' ' + grp;
        list.appendChild(lbl);

        vars.forEach(function(v) {
            var chip = document.createElement('div');
            chip.className = 'bv-chip' +
                (v.type === 'boolean' ? ' bv-boolean' : '') +
                (v.type === 'number'  ? ' bv-number'  : '');
            chip.setAttribute('draggable', 'true');
            chip.setAttribute('data-tag', v.tag);
            chip.setAttribute('title', (v.description || v.column) + '\nClick to copy · Drag to code editor');

            var tagSpan = document.createElement('span');
            tagSpan.className = 'bv-chip-tag';
            tagSpan.textContent = v.tag;
            var descSpan = document.createElement('span');
            descSpan.className = 'bv-chip-desc';
            descSpan.textContent = v.description || '';

            chip.appendChild(tagSpan);
            chip.appendChild(descSpan);

            chip.addEventListener('click', function() {
                var tag = v.tag;
                copyVarText(tag);
                var codeTA = document.getElementById('code-editor-textarea');
                if (codeTA && document.activeElement === codeTA) insertAtCursorVar(codeTA, tag);
                chip.classList.add('copied');
                setTimeout(function() { chip.classList.remove('copied'); }, 900);
                showVarToast('Copied: ' + tag);
            });

            chip.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', v.tag);
                e.dataTransfer.effectAllowed = 'copy';
                chip.style.opacity = '0.5';
            });
            chip.addEventListener('dragend', function() { chip.style.opacity = ''; });

            list.appendChild(chip);
        });
    });

    if (!found) {
        var msg = document.createElement('div');
        msg.className = 'bv-no-result';
        msg.textContent = filter ? 'No matches for "' + filter + '"' : 'No variables found.';
        list.appendChild(msg);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    renderBillVariables('');

    var searchInput = document.getElementById('bv-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() { renderBillVariables(this.value); });
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { this.value = ''; renderBillVariables(''); }
        });
    }

    /* Tab switching */
    var tabBlocks = document.getElementById('bpt-blocks');
    var tabVars   = document.getElementById('bpt-vars');
    var panelBlocks = document.getElementById('bpt-blocks-panel');
    var panelVars   = document.getElementById('bpt-vars-panel');

    if (tabBlocks && tabVars && panelBlocks && panelVars) {
        tabBlocks.addEventListener('click', function() {
            tabBlocks.classList.add('active'); tabVars.classList.remove('active');
            panelBlocks.style.display = '';    panelVars.style.display = 'none';
        });
        tabVars.addEventListener('click', function() {
            tabVars.classList.add('active');   tabBlocks.classList.remove('active');
            panelVars.style.display = 'flex';  panelBlocks.style.display = 'none';
            if (searchInput) searchInput.focus();
        });
    }

    /* Allow drag into code-editor textarea */
    var codeTA = document.getElementById('code-editor-textarea');
    if (codeTA) {
        codeTA.addEventListener('dragover', function(e) {
            e.preventDefault(); e.dataTransfer.dropEffect = 'copy';
        });
        codeTA.addEventListener('drop', function(e) {
            e.preventDefault();
            var text = e.dataTransfer.getData('text/plain');
            if (text) insertAtCursorVar(codeTA, text);
        });
    }

    /* Right‑sidebar bd-var-items: click to copy */
    document.querySelectorAll('.bd-var-item[data-tag]').forEach(function(el) {
        el.style.cursor = 'pointer';
        var tag = el.getAttribute('data-tag');
        el.addEventListener('click', function() {
            copyVarText(tag); showVarToast('Copied: ' + tag);
        });
        el.setAttribute('draggable', 'true');
        el.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', tag);
            e.dataTransfer.effectAllowed = 'copy';
        });
    });

    /* ── GrapesJS Modal Variables tab ── */
    var gjsTabBlocks     = document.getElementById('gjs-tab-blocks');
    var gjsTabVars       = document.getElementById('gjs-tab-vars');
    var gjsTabBlocksPanel = document.getElementById('gjs-tab-blocks-panel');
    var gjsTabVarsPanel  = document.getElementById('gjs-tab-vars-panel');
    var gjsBvList        = document.getElementById('gjs-bv-list');
    var gjsBvSearch      = document.getElementById('gjs-bv-search');

    /* Render variables into the modal's var list */
    function renderGjsVariables(filter) {
        if (!gjsBvList) return;
        gjsBvList.innerHTML = '';
        filter = (filter || '').toLowerCase().trim();

        var groups = {}, order = [];
        BILL_VARIABLES.forEach(function(v) {
            var g = v.group || 'Other';
            if (!groups[g]) { groups[g] = []; order.push(g); }
            groups[g].push(v);
        });

        var found = 0;
        order.forEach(function(grp) {
            var vars = groups[grp];
            if (filter) {
                vars = vars.filter(function(v) {
                    return v.column.toLowerCase().indexOf(filter) !== -1 ||
                           (v.description||'').toLowerCase().indexOf(filter) !== -1 ||
                           grp.toLowerCase().indexOf(filter) !== -1;
                });
            }
            if (!vars.length) return;
            found += vars.length;

            var lbl = document.createElement('div');
            lbl.className = 'bv-group-label';
            lbl.textContent = (GROUP_ICONS[grp] || '') + ' ' + grp;
            gjsBvList.appendChild(lbl);

            vars.forEach(function(v) {
                var chip = document.createElement('div');
                chip.className = 'bv-chip' +
                    (v.type === 'boolean' ? ' bv-boolean' : '') +
                    (v.type === 'number'  ? ' bv-number'  : '');
                chip.setAttribute('draggable', 'true');
                chip.setAttribute('data-tag', v.tag);
                chip.setAttribute('title', (v.description || v.column) + '\nClick to copy · Drag to code editor');

                var tagSpan = document.createElement('span');
                tagSpan.className = 'bv-chip-tag';
                tagSpan.textContent = v.tag;
                var descSpan = document.createElement('span');
                descSpan.className = 'bv-chip-desc';
                descSpan.textContent = v.description || '';

                chip.appendChild(tagSpan);
                chip.appendChild(descSpan);

                chip.addEventListener('click', function() {
                    copyVarText(v.tag);
                    var codeTA = document.getElementById('code-editor-textarea');
                    if (codeTA && document.activeElement === codeTA) insertAtCursorVar(codeTA, v.tag);
                    chip.classList.add('copied');
                    setTimeout(function() { chip.classList.remove('copied'); }, 900);
                    showVarToast('Copied: ' + v.tag);
                });

                chip.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', v.tag);
                    e.dataTransfer.effectAllowed = 'copy';
                    chip.style.opacity = '0.5';
                });
                chip.addEventListener('dragend', function() { chip.style.opacity = ''; });

                gjsBvList.appendChild(chip);
            });
        });

        if (!found) {
            var msg = document.createElement('div');
            msg.className = 'bv-no-result';
            msg.textContent = filter ? 'No matches for "' + filter + '"' : 'No variables.';
            gjsBvList.appendChild(msg);
        }
    }

    /* Initial render for modal */
    renderGjsVariables('');

    if (gjsBvSearch) {
        gjsBvSearch.addEventListener('input', function() { renderGjsVariables(this.value); });
        gjsBvSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { this.value = ''; renderGjsVariables(''); }
        });
    }

    /* GrapesJS modal tab switching */
    if (gjsTabBlocks && gjsTabVars && gjsTabBlocksPanel && gjsTabVarsPanel) {
        gjsTabBlocks.addEventListener('click', function() {
            gjsTabBlocks.classList.add('active');
            gjsTabVars.classList.remove('active');
            gjsTabBlocksPanel.style.display = '';
            gjsTabVarsPanel.style.display = 'none';
        });
        gjsTabVars.addEventListener('click', function() {
            gjsTabVars.classList.add('active');
            gjsTabBlocks.classList.remove('active');
            gjsTabVarsPanel.style.display = 'flex';
            gjsTabBlocksPanel.style.display = 'none';
            if (gjsBvSearch) gjsBvSearch.focus();
        });
    }
});

})();
</script>

<script src="<?php echo base_url();?>assets/js/print-designer/block-editor.js"></script>


<!-- GrapesJS Library -->
<script src="https://unpkg.com/grapesjs@0.21.13"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic"></script>

<!-- Block Designer Controller -->
<script>
(function() {
    'use strict';

    // ── Define Smart Placeholders Plugin ──
    grapesjs.plugins.add('gjs-smart-placeholders', (editor, options) => {
        const bm = editor.BlockManager;

        bm.add('invoice-items-placeholder', {
            label: `
                <div class="gjs-block-label">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"></path>
                    </svg>
                    <div class="gjs-block-name">Invoice Items Table</div>
                </div>`,
            category: 'Smart Placeholders',
            content: `
                <div class="smart-placeholder" data-type="invoice-items" style="padding: 20px; text-align: center; border: 2px dashed #3c8dbc; background: #f9fafc; color: #3c8dbc; font-weight: bold; margin: 10px 0;">
                    [ Standard Invoice Items Table ]<br>
                    <small style="font-weight: normal; color: #666;">(Will be dynamically generated at print time)</small>
                </div>
            `,
            attributes: { class: 'gjs-block-section' }
        });
    });

    // GrapesJS is initialized on-demand inside initGrapesJS() when the
    // Customize modal opens. No page-level init is needed here.

    let gjsEditor = null;
    let activeBlockType = null;
    let activeBlockIndex = null;
    let onSaveCallback = null;

    const $overlay = $('#block-designer-overlay');
    const $label = $('#bd-block-label');

    // ── Public API: open the block designer ──
    // ── Public API: open the block designer ──
    window.BlockDesigner = {
        /**
         * Open the GrapesJS block designer modal.
         * @param {string} blockType — e.g., 'company-header'
         * @param {string} blockLabel — e.g., 'Company Header'
         * @param {string} html — current block HTML (or default template)
         * @param {number} blockIndex — index in the blocks array
         * @param {function} onSave — callback(inlinedHtml, rawCss) when user saves
         * @param {string} css — optional raw CSS to load into the Style Manager
         */
        open: function(blockType, blockLabel, html, blockIndex, onSave, css) {
            activeBlockType = blockType;
            activeBlockIndex = blockIndex;
            onSaveCallback = onSave;

            $label.text(blockLabel);
            $overlay.addClass('active');

            // Allow the DOM to paint, then init GrapesJS
            setTimeout(function() {
                initGrapesJS(html, blockType, css);
            }, 100);
        }
    };

    // ── Cancel ──
    $('#bd-cancel').on('click', function() {
        closeDesigner();
    });

    // ── Save ──
    // ── Save as Custom Block Modal Open ──
    $('#bd-save-custom').on('click', function() {
        if (!gjsEditor) return;
        $('#csm-block-name').val('');
        $('#custom-save-modal').addClass('active');
        $('#csm-block-name').focus();
    });

    $('#csm-btn-cancel').on('click', function() {
        $('#custom-save-modal').removeClass('active');
    });

    $('#csm-block-name').on('keypress', function(e) {
        if (e.which === 13) {
            $('#csm-btn-save').click();
        }
    });

    $('#csm-btn-save').on('click', function() {
        if (!gjsEditor) return;

        const blockName = $('#csm-block-name').val().trim();
        if (!blockName) {
            $('#csm-block-name').css('border-color', '#f38ba8');
            setTimeout(() => $('#csm-block-name').css('border-color', ''), 1000);
            return;
        }

        const html = gjsEditor.getHtml();
        const css = gjsEditor.getCss();
        let fullHtml = inlineCssToHtml(html, css);

        const payload = {
            name: blockName,
            html: fullHtml
        };

        const $btn = $(this);
        const originalText = $btn.text();
        $btn.prop('disabled', true).text('Saving...');

        fetch(TEMPLATE_CONFIG.baseUrl + 'admin_print_template/save_custom_block', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#custom-save-modal').removeClass('active');
                closeDesigner();
                location.reload(); 
            } else {
                alert('Error saving block: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Failed to save block.');
        })
        .finally(() => {
            $btn.prop('disabled', false).text(originalText);
        });
    });

    /**
     * Merge CSS into HTML for saving/rendering.
     * GrapesJS generates CSS with selectors like #iabc, .class etc.
     * Strategy: First try to inline styles. If any CSS rules couldn't be matched,
     * embed them as a <style> block so they're never lost.
     */
    function inlineCssToHtml(html, css) {
        if (!css || !css.trim()) return html;

        // Create a temporary container to work with
        const container = document.createElement('div');
        container.innerHTML = html;

        let unmatchedCss = '';

        // Parse CSS rules and apply to matching elements
        try {
            const tempStyle = document.createElement('style');
            tempStyle.textContent = css;
            document.head.appendChild(tempStyle);
            const sheet = tempStyle.sheet;

            if (sheet && sheet.cssRules) {
                for (let i = 0; i < sheet.cssRules.length; i++) {
                    const rule = sheet.cssRules[i];
                    if (rule.type !== CSSRule.STYLE_RULE) continue;

                    const selector = rule.selectorText;
                    if (!selector) continue;

                    // Skip body-level rules that GrapesJS generates
                    if (selector === 'body' || selector === '*') continue;

                    try {
                        const matchedEls = container.querySelectorAll(selector);
                        if (matchedEls.length === 0) {
                            // No elements matched — keep this rule as embedded CSS
                            unmatchedCss += rule.cssText + '\n';
                            continue;
                        }
                        matchedEls.forEach(el => {
                            // Merge each CSS property into the element's inline style
                            for (let j = 0; j < rule.style.length; j++) {
                                const prop = rule.style[j];
                                const value = rule.style.getPropertyValue(prop);
                                const priority = rule.style.getPropertyPriority(prop);
                                el.style.setProperty(prop, value, priority);
                            }
                        });
                    } catch(e) {
                        // Invalid selector — keep as embedded CSS
                        unmatchedCss += rule.cssText + '\n';
                    }
                }
            }
            document.head.removeChild(tempStyle);
        } catch(e) {
            console.warn('CSS inlining failed:', e);
            // Fallback: return html with <style> block
            return '<style>' + css + '</style>\n' + html;
        }

        let result = container.innerHTML;

        // If there were unmatched CSS rules, embed them as a <style> block
        if (unmatchedCss.trim()) {
            result = '<style>' + unmatchedCss + '</style>\n' + result;
        }

        return result;
    }

    // ── Apply to Template ──
    $('#bd-save').on('click', function() {
        if (!gjsEditor) return;

        const html = gjsEditor.getHtml();
        const css = gjsEditor.getCss();

        // Inline CSS rules into the HTML for print rendering
        let fullHtml = inlineCssToHtml(html, css);

        if (typeof onSaveCallback === 'function') {
            // Pass both the inlined HTML (for rendering) and raw CSS (for Style Manager persistence)
            onSaveCallback(fullHtml, css);
        }

        closeDesigner();
    });

    // ── ESC key to cancel ──
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#block-designer-overlay').hasClass('active')) {
            closeDesigner();
        }
    });

    // ── Create New Block ──
    $('#be-btn-create-block').on('click', function() {
        // Open block designer with empty content
        // open(blockType, blockLabel, html, blockIndex, onSave)
        window.BlockDesigner.open('custom', 'New Custom Block', '', -1, function(html) {
             // Optional: handle save if we implemented a direct save to list without saving as block first
             // But for now, user should use "Save as Block"
        });
        
        // Hide "Apply to Template" button since we are not editing a template block
        $('#bd-save').hide();
    });

    function closeDesigner() {
        $overlay.removeClass('active');
        if (gjsEditor) {
            gjsEditor.destroy();
            gjsEditor = null;
        }
        // Clear the container for next use
        $('#gjs').html('');
        activeBlockType = null;
        activeBlockIndex = null;
        onSaveCallback = null;
    }

    // ══════════════════════════════════════════════════════════════
    // TABLE CELL PROPERTIES BAR
    // Shows colspan / rowspan controls + row/col insert/delete
    // whenever a <td> or <th> is selected in GrapesJS.
    // ══════════════════════════════════════════════════════════════
    function setupTableCellBar(editor) {
        const bar        = document.getElementById('tc-bar');
        const inColspan  = document.getElementById('tc-colspan');
        const inRowspan  = document.getElementById('tc-rowspan');
        const btnApply   = document.getElementById('tc-apply');
        const cellInfo   = document.getElementById('tc-cell-info');
        if (!bar) return;

        let selectedModel = null;

        /* ── Helper: get the live DOM element inside the canvas iframe ── */
        function getLiveDom(model) {
            try {
                const canvasDoc = editor.Canvas.getDocument();
                const id = model.getId();
                return canvasDoc.getElementById(id) || null;
            } catch(e) { return null; }
        }

        /* ── Helper: get <tr> parent ── */
        function getTr(el) {
            let p = el;
            while (p && p.tagName !== 'TR') p = p.parentElement;
            return p;
        }

        /* ── Helper: get <tbody> / <table> parent ── */
        function getTbody(el) {
            let p = el;
            while (p && p.tagName !== 'TBODY' && p.tagName !== 'THEAD' && p.tagName !== 'TABLE') p = p.parentElement;
            return p;
        }

        /* ── Helper: get column index of a td/th within its row ── */
        function getCellIndex(td) {
            const row = getTr(td);
            if (!row) return -1;
            return Array.from(row.children).indexOf(td);
        }

        /* ── Update the bar from current model attributes ── */
        function refreshBar(model) {
            const attrs = model.getAttributes();
            const cs = parseInt(attrs.colspan) || 1;
            const rs = parseInt(attrs.rowspan) || 1;
            inColspan.value = cs;
            inRowspan.value = rs;
            const tag = (model.get('tagName') || 'td').toUpperCase();
            const idx = getCellIndex(getLiveDom(model));
            cellInfo.textContent = tag + (idx >= 0 ? ' [col '+(idx+1)+']' : '') +
                ' · cs:' + cs + ' rs:' + rs;
        }

        /* ── Show bar ── */
        function showBar(model) {
            selectedModel = model;
            bar.classList.add('active');
            refreshBar(model);
        }

        /* ── Hide bar ── */
        function hideBar() {
            selectedModel = null;
            bar.classList.remove('active');
            if (cellInfo) cellInfo.textContent = '';
        }

        /* ── GrapesJS selection events ── */
        editor.on('component:selected', function(model) {
            const tag = (model.get('tagName') || '').toLowerCase();
            if (tag === 'td' || tag === 'th') {
                showBar(model);
            } else {
                hideBar();
            }
        });
        editor.on('component:deselected', hideBar);

        /* ── Apply colspan + rowspan ── */
        function applySpan() {
            if (!selectedModel) return;
            const cs = Math.max(1, parseInt(inColspan.value) || 1);
            const rs = Math.max(1, parseInt(inRowspan.value) || 1);
            const attrs = {};
            if (cs > 1) { attrs.colspan = cs; } else { attrs.colspan = null; }
            if (rs > 1) { attrs.rowspan = rs; } else { attrs.rowspan = null; }
            // Remove null attrs
            const clean = {};
            Object.keys(attrs).forEach(k => { if (attrs[k] !== null) clean[k] = attrs[k]; });
            selectedModel.removeAttributes(['colspan','rowspan']);
            if (Object.keys(clean).length) selectedModel.addAttributes(clean);
            refreshBar(selectedModel);
            showVarToast('Applied — cs:' + cs + ' rs:' + rs);
        }
        if (btnApply) btnApply.addEventListener('click', applySpan);
        if (inColspan) inColspan.addEventListener('keydown', e => { if (e.key==='Enter') applySpan(); });
        if (inRowspan) inRowspan.addEventListener('keydown', e => { if (e.key==='Enter') applySpan(); });

        /* ── Rebuild HTML from canvas DOM back into GrapesJS ── */
        function syncFromDom() {
            try {
                const canvasDoc = editor.Canvas.getDocument();
                const bodyHtml  = canvasDoc.body.innerHTML;
                editor.setComponents(bodyHtml);
            } catch(e) { console.warn('sync error', e); }
        }

        /* ── Row: insert before ── */
        document.getElementById('tc-row-before').addEventListener('click', function() {
            if (!selectedModel) return;
            const td = getLiveDom(selectedModel);
            if (!td) return;
            const tr = getTr(td);
            if (!tr) return;
            const cols = tr.children.length;
            const newTr = document.createElement('tr');
            for (let i = 0; i < cols; i++) {
                const cell = document.createElement('td');
                cell.setAttribute('style', td.getAttribute('style') || '');
                cell.innerHTML = '&nbsp;';
                newTr.appendChild(cell);
            }
            tr.parentElement.insertBefore(newTr, tr);
            syncFromDom();
            showVarToast('Row inserted above');
        });

        /* ── Row: insert after ── */
        document.getElementById('tc-row-after').addEventListener('click', function() {
            if (!selectedModel) return;
            const td = getLiveDom(selectedModel);
            if (!td) return;
            const tr = getTr(td);
            if (!tr) return;
            const cols = tr.children.length;
            const newTr = document.createElement('tr');
            for (let i = 0; i < cols; i++) {
                const cell = document.createElement('td');
                cell.setAttribute('style', td.getAttribute('style') || '');
                cell.innerHTML = '&nbsp;';
                newTr.appendChild(cell);
            }
            tr.parentElement.insertBefore(newTr, tr.nextSibling);
            syncFromDom();
            showVarToast('Row inserted below');
        });

        /* ── Row: delete ── */
        document.getElementById('tc-row-delete').addEventListener('click', function() {
            if (!selectedModel) return;
            const td = getLiveDom(selectedModel);
            if (!td) return;
            const tr = getTr(td);
            if (!tr) return;
            const tbody = tr.parentElement;
            if (tbody && tbody.children.length <= 1) {
                showVarToast('Cannot delete the only row!'); return;
            }
            if (!confirm('Delete this row?')) return;
            tbody.removeChild(tr);
            hideBar();
            syncFromDom();
            showVarToast('Row deleted');
        });

        /* ── Col: insert before ── */
        document.getElementById('tc-col-before').addEventListener('click', function() {
            if (!selectedModel) return;
            const td = getLiveDom(selectedModel);
            if (!td) return;
            const idx = getCellIndex(td);
            if (idx < 0) return;
            const tbody = getTbody(td);
            if (!tbody) return;
            Array.from(tbody.rows).forEach(function(row) {
                const refCell = row.cells[idx];
                const newCell = document.createElement(row === getTr(td) ? td.tagName : 'td');
                newCell.innerHTML = '&nbsp;';
                row.insertBefore(newCell, refCell || null);
            });
            syncFromDom();
            showVarToast('Column inserted before');
        });

        /* ── Col: insert after ── */
        document.getElementById('tc-col-after').addEventListener('click', function() {
            if (!selectedModel) return;
            const td = getLiveDom(selectedModel);
            if (!td) return;
            const idx = getCellIndex(td);
            if (idx < 0) return;
            const tbody = getTbody(td);
            if (!tbody) return;
            Array.from(tbody.rows).forEach(function(row) {
                const refCell = row.cells[idx + 1];
                const newCell = document.createElement(row === getTr(td) ? td.tagName : 'td');
                newCell.innerHTML = '&nbsp;';
                row.insertBefore(newCell, refCell || null);
            });
            syncFromDom();
            showVarToast('Column inserted after');
        });

        /* ── Col: delete ── */
        document.getElementById('tc-col-delete').addEventListener('click', function() {
            if (!selectedModel) return;
            const td = getLiveDom(selectedModel);
            if (!td) return;
            const idx = getCellIndex(td);
            if (idx < 0) return;
            const tbody = getTbody(td);
            if (!tbody) return;
            if (tbody.rows[0] && tbody.rows[0].cells.length <= 1) {
                showVarToast('Cannot delete the only column!'); return;
            }
            if (!confirm('Delete column ' + (idx+1) + '?')) return;
            Array.from(tbody.rows).forEach(function(row) {
                if (row.cells[idx]) row.deleteCell(idx);
            });
            hideBar();
            syncFromDom();
            showVarToast('Column deleted');
        });
    }

    function initGrapesJS(html, blockType, existingCss) {
        // Destroy previous instance if exists
        if (gjsEditor) {
            gjsEditor.destroy();
            gjsEditor = null;
        }
        $('#gjs').html('');

        // ── Prevent variable chip drags from triggering GrapesJS block sorter ──
        // We mark any dragstart from a [data-tag] chip and stop it from
        // entering the GrapesJS layer manager.
        let _isVarDrag = false;
        document.addEventListener('dragstart', function(e) {
            const chip = e.target.closest('[data-tag]');
            _isVarDrag = !!chip;
        }, true);
        document.addEventListener('dragend', function() {
            _isVarDrag = false;
        }, true);

        // Block GrapesJS's own sorter dragover when a variable chip is in flight
        const gjsContainer = document.getElementById('gjs');
        if (gjsContainer) {
            gjsContainer.addEventListener('dragover', function(e) {
                if (_isVarDrag) {
                    e.stopImmediatePropagation();
                }
            }, true);
            gjsContainer.addEventListener('dragenter', function(e) {
                if (_isVarDrag) {
                    e.stopImmediatePropagation();
                }
            }, true);
        }

        gjsEditor = grapesjs.init({
            container: '#gjs',
            fromElement: false,
            noticeOnUnload: false,
            height: '100%',
            width: 'auto',

            storageManager: { type: 'none' },

            // Custom UI Layout
            panels: { defaults: [] },
            blockManager: { appendTo: '#gjs-blocks' },
            layerManager: { appendTo: '#gjs-layers' },
            traitManager: { appendTo: '#gjs-traits' },

            
            // Add Plugins
            plugins: ['gjs-blocks-basic', 'gjs-smart-placeholders'],
            pluginsOpts: {
                'gjs-blocks-basic': {
                    flexGrid: true, // Enable Flexbox blocks
                    blocks: ['column1', 'column2', 'column3', 'text', 'link', 'image', 'video', 'map'], // Select blocks to show
                    category: 'Basic Structures'
                }
            },

            // Canvas configuration for proper UTF-8 and ₹ symbol rendering
            canvas: {
                styles: [
                    'https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap'
                ]
            },

            canvasCss: `
                * { box-sizing: border-box; }
                body {
                    margin: 10px;
                    font-family: 'Noto Sans', Arial, sans-serif;
                    font-size: 13px;
                    /* Extra bottom padding so the last row is NEVER hidden under
                       the GrapesJS floating component toolbar (approx 40px tall) */
                    padding-bottom: 120px;
                }
                table { border-collapse: collapse; width: 100%; border: 1px solid #eee; }
                td, th {
                    padding: 8px 4px;
                    /* Show a subtle cursor hint that the cell is editable */
                    cursor: text;
                    min-height: 25px;
                    min-width: 50px;
                }
                /* Only remove default underlines from links - NOT from u/span/etc 
                   so Bold/Italic/Underline toolbar buttons work correctly */
                a { text-decoration: none; }
                
                /* Highlight selected table cells clearly */
                td.gjs-selected, th.gjs-selected {
                    outline: 2px solid #3c8dbc !important;
                    outline-offset: -1px;
                    background-color: rgba(60, 141, 188, 0.05);
                }
                .ph-tag {
                    display: inline-block;
                    background: #dbeafe;
                    color: #1e40af;
                    padding: 1px 6px;
                    border-radius: 3px;
                    font-family: monospace;
                    font-size: 11px;
                    border: 1px dashed #93c5fd;
                    cursor: grab;
                }
                .gjs-conditional-element {
                    outline: 1px dashed #f39c12 !important;
                    outline-offset: -1px;
                    position: relative;
                    min-height: 20px;
                }
                .gjs-conditional-element::after {
                    content: 'Requirement Active';
                    position: absolute;
                    top: 0;
                    right: 0;
                    background: #f39c12;
                    color: #fff;
                    font-size: 8px;
                    padding: 1px 3px;
                    z-index: 10;
                    pointer-events: none;
                }
            `,

            styleManager: {
                appendTo: '#gjs-styles',
                sectors: [{
                    name: 'Layout',
                    open: true,
                    buildProps: ['display', 'position', 'float', 'top', 'right', 'bottom', 'left', 'overflow', 'opacity', 'z-index', 'visibility', 'cursor'],
                    properties: [
                        { name: 'Display', property: 'display', type: 'select', defaults: 'block',
                          list: [{value: 'block'}, {value: 'flex'}, {value: 'inline-block'}, {value: 'inline'}, {value: 'none'}, {value: 'table'}, {value: 'table-row'}, {value: 'table-cell'}, {value: 'grid'}] },
                        { name: 'Position', property: 'position', type: 'select', defaults: 'static',
                          list: [{value: 'static'}, {value: 'relative'}, {value: 'absolute'}, {value: 'fixed'}] },
                        { name: 'Float', property: 'float', type: 'radio', defaults: 'none',
                          list: [{value: 'none', className: 'fa fa-times'}, {value: 'left', className: 'fa fa-align-left'}, {value: 'right', className: 'fa fa-align-right'}] },
                        { name: 'Overflow', property: 'overflow', type: 'select', defaults: 'visible',
                          list: [{value: 'visible'}, {value: 'hidden'}, {value: 'scroll'}, {value: 'auto'}] },
                        { name: 'Visibility', property: 'visibility', type: 'select', defaults: 'visible',
                          list: [{value: 'visible'}, {value: 'hidden'}, {value: 'collapse'}] },
                        { name: 'Cursor', property: 'cursor', type: 'select', defaults: 'auto',
                          list: [{value: 'auto'}, {value: 'pointer'}, {value: 'default'}, {value: 'not-allowed'}, {value: 'grab'}, {value: 'text'}] }
                    ]
                },{
                    name: 'Dimension',
                    open: true,
                    buildProps: ['width', 'min-width', 'max-width', 'height', 'min-height', 'max-height', 'padding', 'margin'],
                    properties: [
                        { property: 'padding', type: 'composite', properties: [
                            { name: 'Top', property: 'padding-top', type: 'integer', units: ['px','%','em','rem','vh','vw'], defaults: 0 },
                            { name: 'Right', property: 'padding-right', type: 'integer', units: ['px','%','em','rem','vh','vw'], defaults: 0 },
                            { name: 'Bottom', property: 'padding-bottom', type: 'integer', units: ['px','%','em','rem','vh','vw'], defaults: 0 },
                            { name: 'Left', property: 'padding-left', type: 'integer', units: ['px','%','em','rem','vh','vw'], defaults: 0 }
                        ]},
                        { property: 'margin', type: 'composite', properties: [
                            { name: 'Top', property: 'margin-top', type: 'integer', units: ['px','%','em','rem','vh','vw'], defaults: 0 },
                            { name: 'Right', property: 'margin-right', type: 'integer', units: ['px','%','em','rem','vh','vw'], defaults: 0 },
                            { name: 'Bottom', property: 'margin-bottom', type: 'integer', units: ['px','%','em','rem','vh','vw'], defaults: 0 },
                            { name: 'Left', property: 'margin-left', type: 'integer', units: ['px','%','em','rem','vh','vw'], defaults: 0 }
                        ]}
                    ]
                },{
                    name: 'Typography',
                    open: false,
                    buildProps: ['font-family', 'font-size', 'font-weight', 'font-style', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-transform', 'text-shadow', 'white-space', 'word-spacing', 'word-break', 'overflow-wrap'],
                    properties: [
                        { name: 'Font', property: 'font-family', type: 'select', defaults: 'Arial, sans-serif',
                          list: [
                              {value: 'Arial, sans-serif', name: 'Arial'},
                              {value: 'Roboto, sans-serif', name: 'Roboto'},
                              {value: 'Times New Roman, serif', name: 'Times New Roman'},
                              {value: 'Georgia, serif', name: 'Georgia'},
                              {value: 'Courier New, monospace', name: 'Courier New'},
                              {value: 'Verdana, sans-serif', name: 'Verdana'},
                              {value: 'Tahoma, sans-serif', name: 'Tahoma'},
                              {value: 'Noto Sans, sans-serif', name: 'Noto Sans'}
                          ] },
                        { name: 'Weight', property: 'font-weight', type: 'select', defaults: 'normal',
                          list: [{value: '100', name: 'Thin'}, {value: '300', name: 'Light'}, {value: 'normal', name: 'Normal'}, {value: '500', name: 'Medium'}, {value: '600', name: 'Semi-Bold'}, {value: 'bold', name: 'Bold'}, {value: '800', name: 'Extra Bold'}] },
                        { name: 'Style', property: 'font-style', type: 'radio', defaults: 'normal',
                          list: [{value: 'normal', name: 'N', className: 'fa fa-font'}, {value: 'italic', name: 'I', className: 'fa fa-italic'}] },
                        { name: 'Align', property: 'text-align', type: 'radio', defaults: 'left',
                          list: [{value: 'left', className: 'fa fa-align-left'}, {value: 'center', className: 'fa fa-align-center'}, {value: 'right', className: 'fa fa-align-right'}, {value: 'justify', className: 'fa fa-align-justify'}] },
                        { name: 'Decoration', property: 'text-decoration', type: 'radio', defaults: 'none',
                          list: [{value: 'none', name: 'N', className: 'fa fa-times'}, {value: 'underline', name: 'U', className: 'fa fa-underline'}, {value: 'line-through', name: 'S', className: 'fa fa-strikethrough'}] },
                        { name: 'Transform', property: 'text-transform', type: 'select', defaults: 'none',
                          list: [{value: 'none'}, {value: 'uppercase'}, {value: 'lowercase'}, {value: 'capitalize'}] },
                        { name: 'Word Break', property: 'word-break', type: 'select', defaults: 'normal',
                          list: [{value: 'normal'}, {value: 'break-all'}, {value: 'break-word'}, {value: 'keep-all'}] },
                        { name: 'Overflow Wrap', property: 'overflow-wrap', type: 'select', defaults: 'normal',
                          list: [{value: 'normal'}, {value: 'break-word'}, {value: 'anywhere'}] }
                    ]
                },{
                    name: 'Borders',
                    open: false,
                    buildProps: ['border-width', 'border-style', 'border-color', 'border-radius', 'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width'],
                    properties: [
                        { name: 'Border Style', property: 'border-style', type: 'select', defaults: 'none',
                          list: [{value: 'none'}, {value: 'solid'}, {value: 'dashed'}, {value: 'dotted'}, {value: 'double'}, {value: 'groove'}, {value: 'ridge'}] }
                    ]
                },{
                    name: 'Background',
                    open: false,
                    buildProps: ['background-color', 'background-image', 'background-repeat', 'background-position', 'background-size'],
                    properties: [
                        { name: 'Repeat', property: 'background-repeat', type: 'select', defaults: 'repeat',
                          list: [{value: 'repeat'}, {value: 'no-repeat'}, {value: 'repeat-x'}, {value: 'repeat-y'}] },
                        { name: 'Size', property: 'background-size', type: 'select', defaults: 'auto',
                          list: [{value: 'auto'}, {value: 'cover'}, {value: 'contain'}, {value: '100%'}] }
                    ]
                },{
                    name: 'Flex Container',
                    open: false,
                    properties: [
                        { name: 'Direction', property: 'flex-direction', type: 'radio', defaults: 'row',
                          list: [{value: 'row', name: 'Row'}, {value: 'row-reverse', name: 'Row Rev'}, {value: 'column', name: 'Col'}, {value: 'column-reverse', name: 'Col Rev'}] },
                        { name: 'Justify', property: 'justify-content', type: 'select', defaults: 'flex-start',
                          list: [{value: 'flex-start', name: 'Start'}, {value: 'center', name: 'Center'}, {value: 'flex-end', name: 'End'}, {value: 'space-between', name: 'Between'}, {value: 'space-around', name: 'Around'}, {value: 'space-evenly', name: 'Evenly'}] },
                        { name: 'Align Items', property: 'align-items', type: 'select', defaults: 'stretch',
                          list: [{value: 'stretch'}, {value: 'flex-start', name: 'Top'}, {value: 'center', name: 'Center'}, {value: 'flex-end', name: 'Bottom'}, {value: 'baseline'}] },
                        { name: 'Wrap', property: 'flex-wrap', type: 'select', defaults: 'nowrap',
                          list: [{value: 'nowrap'}, {value: 'wrap'}, {value: 'wrap-reverse'}] },
                        { name: 'Gap', property: 'gap' },
                        { name: 'Flex Grow', property: 'flex-grow', type: 'integer', defaults: 0, min: 0 },
                        { name: 'Flex Shrink', property: 'flex-shrink', type: 'integer', defaults: 1, min: 0 },
                        { name: 'Flex Basis', property: 'flex-basis', type: 'integer', units: ['px','%','em','auto'], defaults: 'auto' },
                        { name: 'Align Self', property: 'align-self', type: 'select', defaults: 'auto',
                          list: [{value: 'auto'}, {value: 'stretch'}, {value: 'flex-start', name: 'Top'}, {value: 'center', name: 'Center'}, {value: 'flex-end', name: 'Bottom'}, {value: 'baseline'}] }
                    ]
                },{
                    name: 'Table Styles',
                    open: false,
                    properties: [
                        { name: 'Border Collapse', property: 'border-collapse', type: 'radio', defaults: 'collapse',
                          list: [{value: 'collapse', name: 'Collapse'}, {value: 'separate', name: 'Separate'}] },
                        { name: 'Border Spacing', property: 'border-spacing' },
                        { name: 'Vertical Align', property: 'vertical-align', type: 'select', defaults: 'middle',
                          list: [{value: 'top'}, {value: 'middle'}, {value: 'bottom'}, {value: 'baseline'}] },
                        { name: 'Table Layout', property: 'table-layout', type: 'radio', defaults: 'auto',
                          list: [{value: 'auto', name: 'Auto'}, {value: 'fixed', name: 'Fixed'}] }
                    ]
                },{
                    name: 'Print & Page',
                    open: false,
                    properties: [
                        { name: 'Page Break Before', property: 'page-break-before', type: 'select', defaults: 'auto',
                          list: [{value: 'auto'}, {value: 'always'}, {value: 'avoid'}] },
                        { name: 'Page Break After', property: 'page-break-after', type: 'select', defaults: 'auto',
                          list: [{value: 'auto'}, {value: 'always'}, {value: 'avoid'}] },
                        { name: 'Page Break Inside', property: 'page-break-inside', type: 'select', defaults: 'auto',
                          list: [{value: 'auto'}, {value: 'avoid'}] },
                        { name: 'Box Sizing', property: 'box-sizing', type: 'select', defaults: 'border-box',
                          list: [{value: 'content-box'}, {value: 'border-box'}] }
                    ]
                },{
                    name: 'Effects',
                    open: false,
                    buildProps: ['opacity', 'box-shadow', 'text-shadow'],
                    properties: [
                        { name: 'Opacity', property: 'opacity', type: 'slider', defaults: 1, step: 0.01, max: 1, min: 0 }
                    ]
                }]
            },


            
            // Disable default panel to use only block manager if needed, but we keep default panels for now
        });

        // ── Condition Variables for Traits ───────────────────────
        const CONDITION_VARIABLES = [
            { value: 'none',                        name: 'No Requirement' },
            { value: 'has_sales_items',             name: 'If Sales Items Exist' },
            { value: 'has_purchase_items',          name: 'If Purchase Items Exist' },
            { value: 'has_return_items',            name: 'If Return/Exchange Exist' },
            { value: 'has_old_metal',               name: 'If Old Metal Exist' },
            { value: 'has_advance_details',         name: 'If Advance Paid' },
            { value: 'has_payment_details',         name: 'If Multi-Payment Exist' },
            { value: 'has_credit_collection_history', name: 'If Credit Collections Exist' },
            { value: 'has_order_items',             name: 'If Order Items Exist' },
            { value: 'has_chit_items',              name: 'If Chit Items Exist' },
            { value: 'has_repair_items',            name: 'If Repair Items Exist' },
            { value: 'has_order_advance_entries',   name: 'If Order Advance Exist' }
        ];

        // ── Global Requirement Trait ───────────────────────────
        // Add 'Requirement' to the base component so everything can be conditional
        const defaultType = gjsEditor.DomComponents.getType('default');
        const defaultModel = defaultType.model;
        const defaultTraits = defaultModel.prototype.defaults.traits || [];

        gjsEditor.DomComponents.addType('default', {
            model: {
                defaults: {
                    traits: [
                        ...defaultTraits,
                        {
                            type: 'select',
                            label: 'Requirement',
                            name: 'requirement',
                            options: CONDITION_VARIABLES,
                            changeProp: 1
                        }
                    ],
                    requirement: 'none',
                },

                init() {
                    this.listenTo(this, 'change:requirement', this.handleRequirementChange);
                    // Initial check
                    this.handleRequirementChange();
                },

                handleRequirementChange() {
                    const req = this.get('requirement');
                    const el = this.getEl();
                    if (!el) return;

                    if (req && req !== 'none') {
                        el.classList.add('gjs-conditional-element');
                        el.setAttribute('title', 'Requirement: ' + req);
                    } else {
                        el.classList.remove('gjs-conditional-element');
                        el.removeAttribute('title');
                    }
                }
            }
        });

        // Override toHTML to wrap components in mustache tags if requirement is set
        gjsEditor.on('component:toHTML', (args) => {
            const comp = args.component;
            const req = comp.get('requirement');
            if (req && req !== 'none') {
                args.html = `{{#${req}}}${args.html}{{/${req}}}`;
            }
        });

        // ── Flexible Drag & Drop for Table Elements ─────────────
        // Relax GrapesJS's strict HTML structure rules so users can freely
        // drop text/divs/spans into cells, between rows, etc.
        gjsEditor.on('component:selected', (component) => {
            const tag = component.get('tagName');
            if (tag === 'td' || tag === 'th') {
                // Make cell editable (double-click triggers RTE)
                if (!component.get('editable')) {
                    component.set('editable', true);
                }
                // Allow dropping anything into cells
                component.set('droppable', true);
            }
        });

        // On editor load, walk all components and relax drag/drop rules
        gjsEditor.on('load', () => {
            const relaxRules = (component) => {
                const tag = (component.get('tagName') || '').toLowerCase();

                if (tag === 'td' || tag === 'th') {
                    component.set({ droppable: true, editable: true });
                }
                if (tag === 'tr') {
                    // Allow dropping cells AND other elements into rows
                    component.set({ droppable: true });
                }
                if (tag === 'table' || tag === 'tbody' || tag === 'thead' || tag === 'tfoot') {
                    component.set({ droppable: true });
                }
                if (tag === 'div' || tag === 'span' || tag === 'p' || tag === 'b' || tag === 'strong') {
                    // Allow text elements to be dragged anywhere
                    component.set({ draggable: true, droppable: true });
                }

                // Recurse into children
                component.components().each(child => relaxRules(child));
            };

            const wrapper = gjsEditor.DomComponents.getWrapper();
            if (wrapper) relaxRules(wrapper);
        });

        // Also relax rules for newly added components (drag from sidebar)
        gjsEditor.on('component:add', (component) => {
            const tag = (component.get('tagName') || '').toLowerCase();
            if (tag === 'td' || tag === 'th') {
                component.set({ droppable: true, editable: true });
            }
            if (tag === 'tr') {
                component.set({ droppable: true });
            }
            // Allow any new text/div to be draggable anywhere
            if (['div', 'span', 'p', 'b', 'strong', 'i', 'u'].includes(tag)) {
                component.set({ draggable: true, droppable: true });
            }
        });

        // ── Register Divider Line Component ────────────────────────
        const domc = gjsEditor.DomComponents;
        domc.addType('divider-line', {
            model: {
                defaults: {
                    tagName: 'div',
                    droppable: false,
                    attributes: { class: 'divider-line' },
                    styles: `
                        .divider-line { 
                            width: 100%; 
                            margin: 10px 0; 
                            border-top: 1px solid #000;
                            height: 0;
                        }
                    `,
                    traits: [
                        {
                            type: 'select',
                            label: 'Line Style',
                            name: 'line-style',
                            options: [
                                { value: 'solid', name: 'Solid' },
                                { value: 'dashed', name: 'Dashed' },
                                { value: 'dotted', name: 'Dotted' },
                                { value: 'dash-dot', name: 'Dash-Dot' }
                            ],
                            changeProp: 1,
                        },
                        {
                            type: 'color',
                            label: 'Line Color',
                            name: 'line-color',
                            changeProp: 1,
                        },
                        {
                            type: 'number',
                            label: 'Thickness (px)',
                            name: 'line-thickness',
                            changeProp: 1,
                        }
                    ],
                    'line-style': 'solid',
                    'line-color': '#000000',
                    'line-thickness': 1,
                },

                init() {
                    this.listenTo(this, 'change:line-style change:line-color change:line-thickness', this.handleStyleChange);
                },

                handleStyleChange() {
                    const style = this.get('line-style');
                    const color = this.get('line-color');
                    const thickness = this.get('line-thickness');
                    
                    const css = {
                        'border-top-width': thickness + 'px',
                        'border-top-color': color,
                        'border-top-style': style === 'dash-dot' ? 'dashed' : style,
                    };

                    // For Dash-Dot, we use a gradient to simulate it since CSS border-style doesn't support it
                    if (style === 'dash-dot') {
                        css['border-top-style'] = 'none';
                        css['background-image'] = `linear-gradient(to right, ${color} 0%, ${color} 70%, transparent 70%, transparent 85%, ${color} 85%, ${color} 100%)`;
                        css['background-size'] = '12px ' + thickness + 'px';
                        css['background-repeat'] = 'repeat-x';
                        css['height'] = thickness + 'px';
                    } else {
                        css['background-image'] = 'none';
                        css['height'] = '0';
                    }

                    this.addStyle(css);
                }
            }
        });

        // ── Block Manager Extras ───────────────────────────────
        gjsEditor.BlockManager.add('conditional-area', {
            label: 'Conditional Area',
            category: 'Logic',
            content: {
                type: 'default',
                tagName: 'div',
                content: 'Drop elements here for conditional logic...',
                style: {
                    padding: '10px',
                    border: '1px dashed #f39c12',
                    'background-color': '#fffbe6',
                    'min-height': '50px'
                },
                requirement: 'has_sales_items'
            }
        });

        // ── Table-Specific Blocks ──────────────────────────────
        // These blocks use <tr>/<td> so they can be dropped inside <table>/<tbody>
        gjsEditor.BlockManager.add('table-label-row', {
            label: `<div class="gjs-block-label">
                <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z"></path>
                </svg>
                <div class="gjs-block-name">Label Row (2-col)</div>
            </div>`,
            category: 'Table Helpers',
            content: `<tr>
                <td style="padding:4px; font-weight:bold; border-bottom:1px solid #eee;">Label</td>
                <td style="padding:4px; text-align:right; border-bottom:1px solid #eee;">Value</td>
            </tr>`,
            attributes: { title: 'Add a Label : Value row inside a table' }
        });

        gjsEditor.BlockManager.add('table-row-3col', {
            label: `<div class="gjs-block-label">
                <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M4 5v14h16V5H4zm14 2v4h-4V7h4zM10 7v4H6V7h4zm-4 6h4v4H6v-4zm6 4v-4h4v4h-4z"></path>
                </svg>
                <div class="gjs-block-name">Table Row (3-col)</div>
            </div>`,
            category: 'Table Helpers',
            content: `<tr>
                <td style="padding:4px; border-bottom:1px solid #eee;">Cell 1</td>
                <td style="padding:4px; border-bottom:1px solid #eee;">Cell 2</td>
                <td style="padding:4px; text-align:right; border-bottom:1px solid #eee;">Cell 3</td>
            </tr>`,
            attributes: { title: 'Add a 3-column row inside a table' }
        });

        gjsEditor.BlockManager.add('table-fullwidth-label', {
            label: `<div class="gjs-block-label">
                <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M5 4v3h5.5v12h3V7H19V4z"></path>
                </svg>
                <div class="gjs-block-name">Full-Width Label</div>
            </div>`,
            category: 'Table Helpers',
            content: `<tr>
                <td colspan="10" style="padding:6px 4px; font-weight:bold; text-align:center; border-bottom:1px solid #eee;">Text Label</td>
            </tr>`,
            attributes: { title: 'Add a full-width text label row spanning all columns' }
        });

        gjsEditor.BlockManager.add('text-below-table', {
            label: `<div class="gjs-block-label">
                <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                    <path d="M2.5 4v3h5v12h3V7h5V4h-13zm19 5h-9v3h3v7h3v-7h3V9z"></path>
                </svg>
                <div class="gjs-block-name">Text (Outside Table)</div>
            </div>`,
            category: 'Table Helpers',
            content: `<div style="padding:6px 4px; font-size:12px;">Text label outside table</div>`,
            attributes: { title: 'Add a text element outside of the table structure' }
        });

        // Setup UI Action Listeners
        $('#gjs-btn-undo').off('click').on('click', () => gjsEditor.UndoManager.undo());
        $('#gjs-btn-redo').off('click').on('click', () => gjsEditor.UndoManager.redo());
        $('#gjs-btn-clear').off('click').on('click', () => {
             if(confirm('Are you sure you want to clear the canvas?')) {
                 gjsEditor.DomComponents.clear();
             }
        });

        // ── Table Cell Properties Bar ──────────────────────────
        setupTableCellBar(gjsEditor);
        // ──────────────────────────────────────────────────────

        // Code Editor - Edit HTML Source
        $('#gjs-btn-code').off('click').on('click', () => {
            if (!gjsEditor) return;
            const currentHtml = gjsEditor.getHtml();
            const currentCss = gjsEditor.getCss();
            // Format for editing: show HTML with embedded style if any
            let editableCode = currentHtml;
            if (currentCss && currentCss.trim()) {
                editableCode = '<style>\n' + currentCss + '\n</style>\n\n' + currentHtml;
            }
            $('#code-editor-textarea').val(editableCode);
            $('#code-editor-modal').css('display', 'flex');
            // Focus and select for easy editing
            setTimeout(() => $('#code-editor-textarea').focus(), 100);
        });

        $('#code-editor-cancel').off('click').on('click', () => {
            $('#code-editor-modal').hide();
        });

        $('#code-editor-apply').off('click').on('click', () => {
            if (!gjsEditor) return;
            let code = $('#code-editor-textarea').val();
            // Extract <style> if present
            let css = '';
            let html = code;
            const styleMatch = code.match(/<style[^>]*>([\s\S]*?)<\/style>/i);
            if (styleMatch) {
                css = styleMatch[1];
                html = code.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '').trim();
            }
            gjsEditor.setComponents(html);
            if (css) {
                gjsEditor.setStyle(css);
            }
            $('#code-editor-modal').hide();
        });

        // Close code editor on Escape
        $(document).on('keydown.codeEditor', function(e) {
            if (e.key === 'Escape' && $('#code-editor-modal').is(':visible')) {
                $('#code-editor-modal').hide();
            }
        });

        $('#gjs-btn-desktop').off('click').on('click', function() {
            gjsEditor.setDevice('Desktop');
            $('.bd-devices .be-btn').removeClass('active');
            $(this).addClass('active');
        });
        $('#gjs-btn-tablet').off('click').on('click', function() {
            gjsEditor.setDevice('Tablet');
            $('.bd-devices .be-btn').removeClass('active');
            $(this).addClass('active');
        });
        $('#gjs-btn-mobile').off('click').on('click', function() {
            gjsEditor.setDevice('Mobile portrait');
            $('.bd-devices .be-btn').removeClass('active');
            $(this).addClass('active');
        });

        $('.bd-tab').off('click').on('click', function() {
            $('.bd-tab').removeClass('active');
            $(this).addClass('active');
            $('.bd-tab-pane').removeClass('active');
            $('#' + $(this).data('tab')).addClass('active');
        });

        // Load the block content into the editor
        // Extract <style> blocks from HTML before loading (for backward compatibility with old saves)
        let loadCss = '';
        let loadHtml = html || '<div>Start designing your block here</div>';
        const styleMatch = loadHtml.match(/<style[^>]*>([\s\S]*?)<\/style>/i);
        if (styleMatch) {
            loadCss = styleMatch[1];
            loadHtml = loadHtml.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '').trim();
        }
        // Auto-wrap standalone tables in a container div so there's always
        // a valid drop zone after the table for <div>-based blocks (Text, etc.)
        // GrapesJS won't allow <div> drops inside <table>/<tbody>/<tr>.
        const _tempDiv = document.createElement('div');
        _tempDiv.innerHTML = loadHtml;
        if (_tempDiv.children.length === 1 && _tempDiv.children[0].tagName === 'TABLE') {
            loadHtml = '<div class="table-wrapper">' + loadHtml + '<div style="min-height:10px;"></div></div>';
        }
        gjsEditor.setComponents(loadHtml);
        // Load CSS: prefer passed-in existingCss (from block.custom_css), fall back to embedded <style>
        const cssToLoad = existingCss || loadCss;
        if (cssToLoad) {
            gjsEditor.setStyle(cssToLoad);
        }

        // ── Issue 1 Fix: Make all text elements double-click editable ──
        // Walk the component tree and set editable:true on text-containing elements
        function makeComponentsEditable(component) {
            const children = component.get('components');
            const tagName = (component.get('tagName') || '').toLowerCase();
            const editableTags = ['div', 'span', 'td', 'th', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'li', 'a', 'strong', 'em', 'b', 'i', 'u', 'small', 'sub', 'sup', 'caption', 'figcaption', 'blockquote', 'pre', 'code', 'dt', 'dd'];

            if (children && children.length > 0) {
                // Has child components — check if any are text nodes
                let hasTextContent = false;
                children.forEach(child => {
                    const childType = child.get('type');
                    if (childType === 'textnode' || childType === 'text') {
                        hasTextContent = true;
                    }
                });

                // If this element has text + is an editable tag, make it editable
                if (hasTextContent && editableTags.includes(tagName)) {
                    component.set({ type: 'text', editable: true });
                }

                // Recurse into children
                children.forEach(child => makeComponentsEditable(child));
            } else {
                // Leaf node — if it's an editable tag type, make it editable
                if (editableTags.includes(tagName)) {
                    component.set({ type: 'text', editable: true });
                }
            }
        }

        // Apply to all root components
        const wrapper = gjsEditor.DomComponents.getWrapper();
        if (wrapper) {
            const rootComponents = wrapper.get('components');
            if (rootComponents) {
                rootComponents.forEach(comp => makeComponentsEditable(comp));
            }
        }

        // ── Also make newly added components editable ──
        gjsEditor.on('component:add', (component) => {
            makeComponentsEditable(component);
        });

        // ── Variable chip drop → inject text, not new component ──
        // GrapesJS renders inside an iframe. We must intercept drop there.
        gjsEditor.on('load', () => {
            try {
                const canvasWin = gjsEditor.Canvas.getWindow();
                const canvasDoc = gjsEditor.Canvas.getDocument();
                if (!canvasDoc) return;

                // Track which tag is being dragged from the variable list
                let _draggedVarTag = null;

                // Listen for dragstart on the host document (variable chips)
                document.addEventListener('dragstart', function(e) {
                    const chip = e.target.closest('[data-tag]');
                    if (chip) {
                        _draggedVarTag = chip.getAttribute('data-tag');
                    } else {
                        _draggedVarTag = null;
                    }
                }, true);
                document.addEventListener('dragend', function() {
                    _draggedVarTag = null;
                }, true);

                // Allow dragover in the canvas
                canvasDoc.addEventListener('dragover', function(e) {
                    if (_draggedVarTag) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.dataTransfer.dropEffect = 'copy';
                    }
                }, true);

                // On drop — inject text into selected component or element under cursor
                canvasDoc.addEventListener('drop', function(e) {
                    if (!_draggedVarTag) return;
                    e.preventDefault();
                    e.stopPropagation();

                    const tag = _draggedVarTag;
                    _draggedVarTag = null;

                    // Try the GrapesJS selected component first
                    let sel = gjsEditor.getSelected();

                    // If nothing selected, find the element under the cursor
                    if (!sel) {
                        const el = canvasDoc.elementFromPoint(e.clientX, e.clientY);
                        if (el) {
                            // Walk GrapesJS components to find a match
                            const findComp = (parent) => {
                                const children = parent.get('components');
                                if (!children) return null;
                                for (let i = 0; i < children.length; i++) {
                                    const c = children.at(i);
                                    if (c.getEl && c.getEl() === el) return c;
                                    const found = findComp(c);
                                    if (found) return found;
                                }
                                return null;
                            };
                            sel = findComp(gjsEditor.DomComponents.getWrapper());
                        }
                    }

                    if (sel) {
                        // Get the live DOM element from the canvas iframe
                        const el = sel.getEl ? sel.getEl() : null;

                        if (el) {
                            // Read what is actually in the DOM
                            const rawHtml = el.innerHTML;

                            // Detect "empty" cell states GrapesJS leaves:
                            //   - "&nbsp;" → \u00A0 in actual DOM text
                            //   - "<br>" or "<br/>"
                            //   - truly empty string
                            const isEmpty = !rawHtml.trim() ||
                                            rawHtml.trim() === '<br>' ||
                                            rawHtml.trim() === '<br/>' ||
                                            /^[\u00A0\s]+$/.test(rawHtml);

                            const newContent = isEmpty ? tag : (rawHtml + tag);

                            // Write directly to the live DOM element
                            el.innerHTML = newContent;

                            // Sync the full canvas DOM back into GrapesJS model
                            try {
                                const body = gjsEditor.Canvas.getDocument().body;
                                gjsEditor.setComponents(body.innerHTML);
                            } catch(syncErr) {
                                console.warn('GrapesJS resync warning:', syncErr);
                            }

                            showVarToast('Inserted: ' + tag);
                        } else {
                            // Fallback: try set('content') for virtual components
                            const existing = (sel.get('content') || '').trim();
                            sel.set('content', existing ? existing + tag : tag);
                            showVarToast('Inserted: ' + tag);
                        }
                    } else {
                        showVarToast('Click a cell first, then drop the variable');
                    }
                }, true);

            } catch(err) {
                console.warn('Variable drop intercept setup failed:', err);
            }
        });

        // Add placeholder fields as draggable blocks based on block type
        loadPlaceholderBlocks(blockType);
    }

    function loadPlaceholderBlocks(blockType) {
        const categoryId = TEMPLATE_CONFIG.categoryId;

        fetch(API_URL + 'admin_print_template/get_fields/' + categoryId)
            .then(r => r.json())
            .then(fields => {
                if (!gjsEditor) return;
                const bm = gjsEditor.BlockManager;

                // Add fields as draggable placeholders
                fields.forEach(field => {
                    bm.add('ph-' + field.placeholder_key, {
                        label: field.placeholder_label || field.placeholder_key,
                        category: 'Placeholders',
                        content: '<span class="ph-tag">{{' + field.placeholder_key + '}}</span>',
                        attributes: {
                            title: field.description || field.placeholder_key
                        }
                    });
                });

                // Add Pre-built Layout Presets
                bm.add('preset-signature', {
                    label: 'Signature Row',
                    category: 'Pre-sets',
                    content: `
                        <div style="display:flex; justify-content:space-between; margin-top:40px; text-align:center;">
                            <div style="width:30%; padding-top:5px;">Customer Sign</div>
                            <div style="width:30%; padding-top:5px;">Authorized Sign</div>
                        </div>
                    `
                });
                
                bm.add('preset-kv', {
                    label: 'Key-Value Pair',
                    category: 'Pre-sets',
                    content: `
                        <div style="display:flex; margin-bottom:5px;">
                            <div style="font-weight:bold; width:120px;">Label:</div>
                            <div>Value</div>
                        </div>
                    `
                });

                bm.add('line-solid', {
                    label: '━━ Solid Line',
                    category: 'Structure',
                    attributes: { class: 'gjs-fonts gjs-f-hr' },
                    content: { type: 'divider-line', 'line-style': 'solid' }
                });

                bm.add('line-dashed', {
                    label: '--- Dashed Line',
                    category: 'Structure',
                    attributes: { class: 'gjs-fonts gjs-f-hr' },
                    content: { type: 'divider-line', 'line-style': 'dashed' }
                });

                bm.add('line-dotted', {
                    label: '... Dotted Line',
                    category: 'Structure',
                    attributes: { class: 'gjs-fonts gjs-f-hr' },
                    content: { type: 'divider-line', 'line-style': 'dotted' }
                });

                bm.add('line-dashdot', {
                    label: '-.- Dash-Dot Line',
                    category: 'Structure',
                    attributes: { class: 'gjs-fonts gjs-f-hr' },
                    content: { type: 'divider-line', 'line-style': 'dash-dot' }
                });
                
                bm.add('layout-spacer', {
                    label: 'Spacer (20px)',
                    category: 'Structure',
                    content: '<div style="height:20px;"></div>'
                });

                // ── Dynamic Tables (Loop-ready, rows auto-repeat) ──
                bm.add('dynamic-sales-table', {
                    label: '📋 Sales Items Table',
                    category: 'Dynamic Tables',
                    content: `
                        <table style="width:100%; border-collapse:collapse; font-size:12px;">
                            <thead>
                                <tr>
                                    <th style="padding:4px; text-align:center;">S.No</th>
                                    <th style="padding:4px; text-align:left;">Description</th>
                                    <th style="padding:4px; text-align:center;">HSN</th>
                                    <th style="padding:4px; text-align:center;">Pcs</th>
                                    <th style="padding:4px; text-align:right;">Gross Wt</th>
                                    <th style="padding:4px; text-align:right;">Net Wt</th>
                                    <th style="padding:4px; text-align:right;">Rate</th>
                                    <th style="padding:4px; text-align:right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- {{#items}} -->
                                <tr>
                                    <td style="padding:4px; text-align:center;">{{sno}}</td>
                                    <td style="padding:4px;">{{description}}</td>
                                    <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                                    <td style="padding:4px; text-align:center;">{{qty}}</td>
                                    <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                                    <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                                    <td style="padding:4px; text-align:right;">{{rate}}</td>
                                    <td style="padding:4px; text-align:right;">{{amount}}</td>
                                </tr>
                                <!-- {{/items}} -->
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:bold;">
                                    <td colspan="3" style="padding:4px;">Total</td>
                                    <td style="padding:4px; text-align:center;">{{total_qty}}</td>
                                    <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                                    <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                                    <td style="padding:4px;"></td>
                                    <td style="padding:4px; text-align:right;">{{sub_total}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    `,
                    attributes: { title: 'Rows repeat for each item. Edit columns as needed.' }
                });

                bm.add('dynamic-purchase-table', {
                    label: '🛒 Purchase Items Table',
                    category: 'Dynamic Tables',
                    content: `
                        <table style="width:100%; border-collapse:collapse; font-size:12px;">
                            <thead>
                                <tr>
                                    <th style="padding:4px; text-align:center;">S.No</th>
                                    <th style="padding:4px; text-align:left;">Description</th>
                                    <th style="padding:4px; text-align:center;">HSN</th>
                                    <th style="padding:4px; text-align:center;">Pcs</th>
                                    <th style="padding:4px; text-align:right;">Gross Wt</th>
                                    <th style="padding:4px; text-align:right;">Stone Less</th>
                                    <th style="padding:4px; text-align:right;">Net Wt</th>
                                    <th style="padding:4px; text-align:right;">Rate</th>
                                    <th style="padding:4px; text-align:right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- {{#purchase_items}} -->
                                <tr>
                                    <td style="padding:4px; text-align:center;">{{sno}}</td>
                                    <td style="padding:4px;">{{description}}</td>
                                    <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                                    <td style="padding:4px; text-align:center;">{{qty}}</td>
                                    <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                                    <td style="padding:4px; text-align:right;">{{stn_less}}</td>
                                    <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                                    <td style="padding:4px; text-align:right;">{{rate}}</td>
                                    <td style="padding:4px; text-align:right;">{{amount}}</td>
                                </tr>
                                <!-- {{/purchase_items}} -->
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:bold;">
                                    <td colspan="4" style="padding:4px;">Total</td>
                                    <td style="padding:4px; text-align:right;">{{total_gross_wt}}</td>
                                    <td style="padding:4px;"></td>
                                    <td style="padding:4px; text-align:right;">{{total_net_wt}}</td>
                                    <td style="padding:4px;"></td>
                                    <td style="padding:4px; text-align:right;">{{sub_total}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    `,
                    attributes: { title: 'Rows repeat for each purchase item.' }
                });

                bm.add('dynamic-return-table', {
                    label: '🔄 Sales Return Table',
                    category: 'Dynamic Tables',
                    content: `
                        <table style="width:100%; border-collapse:collapse; font-size:12px;">
                            <thead>
                                <tr>
                                    <th style="padding:4px; text-align:center;">S.No</th>
                                    <th style="padding:4px; text-align:left;">Description</th>
                                    <th style="padding:4px; text-align:center;">HSN</th>
                                    <th style="padding:4px; text-align:center;">Pcs</th>
                                    <th style="padding:4px; text-align:right;">Gross Wt</th>
                                    <th style="padding:4px; text-align:right;">Net Wt</th>
                                    <th style="padding:4px; text-align:right;">Rate</th>
                                    <th style="padding:4px; text-align:right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- {{#return_items}} -->
                                <tr>
                                    <td style="padding:4px; text-align:center;">{{sno}}</td>
                                    <td style="padding:4px;">{{description}}</td>
                                    <td style="padding:4px; text-align:center;">{{hsn_code}}</td>
                                    <td style="padding:4px; text-align:center;">{{qty}}</td>
                                    <td style="padding:4px; text-align:right;">{{gross_wt}}</td>
                                    <td style="padding:4px; text-align:right;">{{net_wt}}</td>
                                    <td style="padding:4px; text-align:right;">{{rate}}</td>
                                    <td style="padding:4px; text-align:right;">{{amount}}</td>
                                </tr>
                                <!-- {{/return_items}} -->
                            </tbody>
                        </table>
                    `,
                    attributes: { title: 'Rows repeat for each returned item.' }
                });

                bm.add('dynamic-payment-table', {
                    label: '💰 Payment Breakdown',
                    category: 'Dynamic Tables',
                    content: `
                        <table style="width:60%; border-collapse:collapse; font-size:12px; margin-top:5px;">
                            <thead>
                                <tr>
                                    <th style="padding:4px; text-align:left;">Payment Mode</th>
                                    <th style="padding:4px; text-align:right;">Amount</th>
                                    <th style="padding:4px; text-align:left;">Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- {{#payment_breakdown}} -->
                                <tr>
                                    <td style="padding:3px 4px; font-weight:bold;">{{mode}}</td>
                                    <td style="padding:3px 4px; text-align:right;">{{amount}}</td>
                                    <td style="padding:3px 4px; font-size:10px; color:#666;">{{reference}}</td>
                                </tr>
                                <!-- {{/payment_breakdown}} -->
                            </tbody>
                        </table>
                    `,
                    attributes: { title: 'Rows repeat for each payment mode (Cash, Card, UPI, etc.)' }
                });

                bm.add('dynamic-advance-table', {
                    label: '📅 Advance Details',
                    category: 'Dynamic Tables',
                    content: `
                        <table style="width:50%; border-collapse:collapse; font-size:12px; margin-top:5px;">
                            <thead>
                                <tr>
                                    <th style="padding:4px; text-align:left;">Date</th>
                                    <th style="padding:4px; text-align:right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- {{#advance_details}} -->
                                <tr>
                                    <td style="padding:3px 4px;">{{date}}</td>
                                    <td style="padding:3px 4px; text-align:right;">₹{{amount}}</td>
                                </tr>
                                <!-- {{/advance_details}} -->
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:bold;">
                                    <td style="padding:4px;">Total Advance</td>
                                    <td style="padding:4px; text-align:right;">₹{{order_advance_adj}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    `,
                    attributes: { title: 'Rows repeat for each advance payment entry.' }
                });

                bm.add('dynamic-custom-loop', {
                    label: '🔁 Custom Loop Table',
                    category: 'Dynamic Tables',
                    content: `
                        <table style="width:100%; border-collapse:collapse; font-size:12px;">
                            <thead>
                                <tr>
                                    <th style="padding:4px;">Column 1</th>
                                    <th style="padding:4px;">Column 2</th>
                                    <th style="padding:4px;">Column 3</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- {{#items}} -->
                                <tr>
                                    <td style="padding:4px;">{{field1}}</td>
                                    <td style="padding:4px;">{{field2}}</td>
                                    <td style="padding:4px;">{{field3}}</td>
                                </tr>
                                <!-- {{/items}} -->
                            </tbody>
                        </table>
                        <div style="font-size:10px; color:#999; padding:4px;">
                            ⓘ Edit the loop tag and field names to match your data source.
                            Change {{#items}} to your loop key, and {{field1}} to your field names.
                        </div>
                    `,
                    attributes: { title: 'Generic loop table. Edit column headers, loop key ({{#items}}), and field placeholders.' }
                });
            })
            .catch(err => console.error('Error loading placeholder fields:', err));

        // Load Custom Blocks
        fetch(API_URL + 'admin_print_template/get_custom_blocks')
            .then(r => r.json())
            .then(blocks => {
                if (!gjsEditor) return;
                const bm = gjsEditor.BlockManager;
                blocks.forEach(block => {
                     bm.add(block.id, {
                         label: block.label,
                         category: block.category,
                         content: block.content,
                         attributes: block.attributes
                     });
                });
            })
            .catch(err => console.error('Error loading custom blocks:', err));
    }

})();
</script>

<!-- Custom Save Modal -->
<div id="custom-save-modal">
    <div class="csm-content">
        <div class="csm-title">Save as Custom Block</div>
        <input type="text" id="csm-block-name" class="csm-input" placeholder="Enter a name for this custom block..." autocomplete="off">
        <div class="csm-actions">
            <button class="be-btn be-btn-outline" style="padding: 6px 12px;" id="csm-btn-cancel">Cancel</button>
            <button class="be-btn be-btn-primary" style="padding: 6px 12px;" id="csm-btn-save">Save Block</button>
        </div>
    </div>
</div>

<style>
/* Adjust GrapesJS modal to match light theme */
.gjs-mdl-dialog {
    background-color: var(--be-bg-dark);
    color: var(--be-text);
    border: 1px solid var(--be-border);
}
.gjs-mdl-header {
    border-bottom: 1px solid var(--be-border);
}
.gjs-mdl-title {
    color: var(--be-text);
}
.gjs-mdl-btn-close {
    color: var(--be-text-muted);
}
</style>

</body>
</html>
