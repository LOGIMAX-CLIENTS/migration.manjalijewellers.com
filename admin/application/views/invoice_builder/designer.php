<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Builder V2 — <?php echo htmlspecialchars($template['template_name']); ?></title>

    <!-- Base GrapesJS -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.13/dist/css/grapes.min.css">
    
    <!-- V2 Builder Styles -->
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/print-designer/builder-v2.css?v=<?= time() ?>">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* General styling for the full-screen builder */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
        }

        /* Top Toolbar */
        .bd-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            height: 50px;
            padding: 0 20px;
            border-bottom: 1px solid #d2d6de;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            z-index: 10;
        }

        .bd-title-info {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .bd-paper-spec {
            font-size: 11px;
            font-weight: normal;
            background: #eef1f6;
            color: #3c8dbc;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #d2d6de;
        }

        .bd-actions {
            display: flex;
            gap: 8px;
        }

        .bd-btn {
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid #d2d6de;
            background: #fff;
            color: #444;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .bd-btn:hover { background: #f4f4f4; color: #333; }
        .bd-btn-primary { background: #3c8dbc; color: #fff; border-color: #367fa9; }
        .bd-btn-primary:hover { background: #367fa9; color: #fff; }

        /* Main Workspace Layout */
        .bd-workspace {
            display: flex;
            height: calc(100vh - 50px);
            width: 100vw;
        }

        /* Left Sidebar: Components Palette */
        .bd-sidebar-left {
            width: 260px;
            background: #fff;
            border-right: 1px solid #d2d6de;
            display: flex;
            flex-direction: column;
        }
        
        /* Middle: The Canvas Container */
        .bd-canvas-container {
            flex: 1;
            position: relative;
            background: #e9ecef; /* Darker background to make the white paper pop */
            overflow: hidden;
            display: flex;
            justify-content: center;
            /* Padding allows scrolling past the paper edge */
            padding: 40px;
        }

        /* Right Sidebar: Settings & Configuration */
        .bd-sidebar-right {
            width: 300px;
            background: #fff;
            border-left: 1px solid #d2d6de;
            display: flex;
            flex-direction: column;
        }

        /* Right Sidebar Tabs */
        .bd-tabs {
            display: flex;
            background: #f9fafc;
            border-bottom: 1px solid #d2d6de;
        }
        .bd-tab {
            flex: 1;
            text-align: center;
            padding: 10px 0;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            border-bottom: 2px solid transparent;
        }
        .bd-tab.active {
            color: #3c8dbc;
            border-bottom-color: #3c8dbc;
            background: #fff;
        }

        .bd-panel-content {
            flex: 1;
            overflow-y: auto;
        }
        
        /* Hidden panels */
        .bd-tab-pane { display: none; height: 100%; }
        .bd-tab-pane.active { display: block; }

    </style>
</head>
<body>

<!-- Toast Notification -->
<div id="toast-msg" style="display:none; position:fixed; bottom:20px; right:20px; background:#28a745; color:#fff; padding:10px 20px; border-radius:4px; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:9999; font-weight:500; border: 1px solid #218838;">
    ✅ Saved Successfully!
</div>

<!-- Top Toolbar -->
<div class="bd-toolbar">
    <div class="bd-title-info">
        📄 <?php echo htmlspecialchars($template['template_name']); ?>
        <span class="bd-paper-spec" id="paper-spec-badge">
            <?php echo htmlspecialchars($template['paper_size'] ?? 'A4'); ?>
        </span>
    </div>
    
    <!-- Central Tools: Zoom & Grid -->
    <div class="bd-actions bd-actions-center" style="display:flex; gap:5px; margin-left:20px; flex:1;">
        <button id="btn-zoom-out" class="bd-btn" title="Zoom Out" style="padding:4px 8px;">🔍 -</button>
        <button id="btn-zoom-reset" class="bd-btn" title="Reset Zoom" style="padding:4px 8px;">100%</button>
        <button id="btn-zoom-in" class="bd-btn" title="Zoom In" style="padding:4px 8px;">🔍 +</button>
        
        <div style="width: 1px; background: #ddd; margin: 0 5px;"></div>
        
        <button id="btn-toggle-grid" class="bd-btn" title="Toggle Grid/Outlines" style="padding:4px 8px;"><i class="fa fa-th"></i> Grid</button>
        <button id="btn-shortcuts" class="bd-btn" title="Keyboard Shortcuts" style="padding:4px 8px; color: #555;">⌨️ Shortcuts</button>
        <button id="btn-clear" class="bd-btn" title="Clear Template" style="padding:4px 8px; color:#d9534f;"><i class="fa fa-trash"></i> Clear</button>
    </div>

    <div class="bd-actions bd-actions-right">
        <a href="<?php echo site_url('invoice-builder'); ?>" class="bd-btn" title="Exit Builder">← Back to List</a>
        <a id="btn-preview" href="<?php echo site_url('invoice-builder/preview/'.$template['id_template']); ?>" class="bd-btn" target="_blank" title="Save first, then preview in new tab">🔍 Preview with Data</a>
        <button id="btn-undo" class="bd-btn" title="Undo">↩</button>
        <button id="btn-redo" class="bd-btn" title="Redo">↪</button>
        <button id="btn-save" class="bd-btn bd-btn-primary">💾 Save Design</button>
    </div>
</div>

<!-- Main Workspace -->
<div class="bd-workspace">
    <!-- Left Sidebar: Building Blocks -->
    <div class="bd-sidebar-left">
        <div style="padding: 10px 15px; font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #d2d6de; background:#f9fafc;">
            Building Blocks
        </div>
        <div id="gjs-blocks" class="bd-panel-content"></div>
    </div>

    <!-- Center: The Canvas frame representing the paper -->
    <div class="bd-canvas-container">
        <!-- The actual GrapesJS iframe mounts inside this #gjs div -->
        <div id="gjs"></div>
    </div>

    <!-- Right Sidebar: Configuration -->
    <div class="bd-sidebar-right">
        <div class="bd-tabs">
            <div class="bd-tab active" data-tab="tab-traits">Settings</div>
            <div class="bd-tab" data-tab="tab-styles">Design</div>
            <div class="bd-tab" data-tab="tab-layers">Structure</div>
        </div>
        <div class="bd-panel-content">
            <div id="tab-traits" class="bd-tab-pane active"></div>
            <div id="tab-styles" class="bd-tab-pane"></div>
            <div id="tab-layers" class="bd-tab-pane"></div>
        </div>
    </div>
</div>

<!-- Shortcuts Modal -->
<div id="shortcuts-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:450px; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.2); overflow:hidden;">
        <div style="padding:15px 20px; background:#f4f6f9; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center;">
            <h4 style="margin:0; font-size:16px; color:#333;">Keyboard Shortcuts</h4>
            <button id="close-shortcuts" style="background:none; border:none; font-size:22px; cursor:pointer; color:#888;">&times;</button>
        </div>
        <div style="padding:20px;">
            <table style="width:100%; font-size:14px; color:#444; border-collapse: collapse;">
                <tr style="border-bottom:1px solid #eee;"><td style="padding:8px 0;"><strong>Ctrl + S</strong> / <strong>Cmd + S</strong></td><td style="padding:8px 0;">Save Design</td></tr>
                <tr style="border-bottom:1px solid #eee;"><td style="padding:8px 0;"><strong>Ctrl + Z</strong> / <strong>Cmd + Z</strong></td><td style="padding:8px 0;">Undo</td></tr>
                <tr style="border-bottom:1px solid #eee;"><td style="padding:8px 0;"><strong>Ctrl + Shift + Z</strong></td><td style="padding:8px 0;">Redo</td></tr>
                <tr style="border-bottom:1px solid #eee;"><td style="padding:8px 0;"><strong>Backspace</strong> / <strong>Delete</strong></td><td style="padding:8px 0;">Remove selected block</td></tr>
                <tr style="border-bottom:1px solid #eee;"><td style="padding:8px 0;"><strong>↑ / ↓ / ← / →</strong></td><td style="padding:8px 0;">Nudge selected element</td></tr>
                <tr><td style="padding:8px 0;"><strong>Esc</strong></td><td style="padding:8px 0;">Deselect block / Close modals</td></tr>
            </table>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    const TEMPLATE_CONFIG = {
        templateId: <?php echo (int)$template['id_template']; ?>,
        paperSize: '<?php echo htmlspecialchars($template['paper_size'] ?? 'A4'); ?>',
        baseUrl: '<?php echo base_url(); ?>index.php/',
        category: '<?php echo htmlspecialchars($template['template_category'] ?? 'general'); ?>'
    };
</script>

<!-- GrapesJS Library -->
<script src="https://unpkg.com/grapesjs@0.21.13"></script>

<!-- Custom Builders for V2 -->
<script src="<?php echo base_url();?>assets/js/print-designer/variable-plugin.js"></script>
<script src="<?php echo base_url();?>assets/js/print-designer/invoice-components.js"></script>
<script src="<?php echo base_url();?>assets/js/print-designer/builder-v2.js"></script>

</body>
</html>
