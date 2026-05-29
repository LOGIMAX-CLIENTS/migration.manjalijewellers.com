/**
 * Konva.js Print Designer V2
 * Canvas-based WYSIWYG print template editor
 * - Drag & Drop elements from sidebar onto canvas
 * - Paper size auto-loaded from template DB record
 * - Properties panel on top, Elements on bottom
 */
(function () {
  'use strict';

  var PAPERS = {
    'A4': { w: 210, h: 297 }, 'A3': { w: 297, h: 420 }, 'A5': { w: 148, h: 210 },
    'Letter': { w: 216, h: 279 }, '58mm': { w: 58, h: 210 }, '80mm': { w: 80, h: 297 },
    'Thermal-58mm': { w: 58, h: 210 }, 'Thermal-80mm': { w: 80, h: 297 }
  };
  var PX_PER_MM = 3.7795;
  var cfg = window.KD_CONFIG || {};
  var stage, layer, guideLayer, transformer, selectedNode = null;
  var selectedNodes = []; // Multi-select: array of currently selected nodes
  var zoom = 1, pageW, pageH, originalPageH;
  var SNAP_THRESHOLD = 6;
  var guideLineH = null, guideLineV = null, guideLabelH = null, guideLabelV = null;

  // Rubber-band (marquee) selection state
  var rubberBand = { active: false, rect: null, startX: 0, startY: 0 };
  // Group drag state: track offsets of other selected nodes relative to dragged node
  var groupDragOffsets = [];

  // Print margins (mm)
  var margins = { top: 0, right: 0, bottom: 0, left: 0 };
  var marginGuides = { top: null, right: null, bottom: null, left: null };
  var marginOverlays = []; // shaded rectangles covering margin zones

  // Page Zones (mm) — header zone at top, footer zone at bottom
  var zones = { header_height: 0, footer_height: 0 };
  var zoneGuides = {
    headerBand: null, footerBand: null, headerLine: null, footerLine: null,
    headerLabel: null, footerLabel: null
  };

  // Undo/Redo history
  var history = [], historyIndex = -1, MAX_HISTORY = 50, historyPaused = false;

  // ══════════════════════════════════════
  // DISPLAY TEXT HELPERS
  // ══════════════════════════════════════
  // Strip {{}} braces from variable placeholders for cleaner canvas display.
  // The raw text (with {{}}) is preserved in the '_rawText' custom attribute.
  function stripBraces(text) {
    if (!text || typeof text !== 'string') return text || '';
    return text.replace(/\{\{([^}]+)\}\}/g, '$1');
  }
  // Apply display-friendly text to a Konva Text node.
  // Stores original in _rawText attr, sets .text() to stripped version.
  function applyDisplayText(node, rawText) {
    node.setAttr('_rawText', rawText);
    node.text(stripBraces(rawText));
  }
  // Get the raw (saveable) text for a node. Falls back to .text() if no _rawText.
  function getRawText(node) {
    return node.getAttr('_rawText') || node.text();
  }
  // Wrap a plain variable name back in {{}} for storage.
  // Only wraps if the value looks like a variable key (no braces already).
  function wrapBraces(val) {
    if (!val || typeof val !== 'string') return val || '';
    val = val.trim();
    if (!val) return '';
    // Already has braces — return as-is
    if (val.indexOf('{{') !== -1) return val;

    // Get all known variables
    var knownVars = typeof getAllVariablesList === 'function' ?
      getAllVariablesList().map(function (v) { return v.key.replace(/[{}]/g, ''); }) : [];

    // If it's a single token, check if it's a variable
    if (/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(val)) {
      if (knownVars.length > 0 && knownVars.indexOf(val) === -1) return val;
      return '{{' + val + '}}';
    }

    // Multi-variable: wrap each token ONLY if it is a known variable
    return val.replace(/([a-zA-Z_][a-zA-Z0-9_]*)/g, function (match) {
      if (knownVars.length > 0 && knownVars.indexOf(match) === -1) return match;
      return '{{' + match + '}}';
    });
  }

  function toast(msg, type) {
    var c = document.getElementById('kdToasts');
    var d = document.createElement('div');
    d.className = 'kd-toast ' + (type || '');
    d.textContent = msg;
    c.appendChild(d);
    setTimeout(function () { d.remove(); }, 3000);
  }

  // ══════════════════════════════════════
  // AUTO-EXTEND CANVAS
  // ══════════════════════════════════════
  // Dynamically grow the stage height when elements extend below the current boundary.
  // Original page height (for print) is preserved; only the design canvas stretches.
  function autoExtendCanvas() {
    if (!stage || !layer) return;
    var minH = Math.round(originalPageH * PX_PER_MM); // never shrink below one page
    var maxBottom = 0;
    var mt = margins.top * PX_PER_MM; // layer offset
    layer.getChildren().forEach(function (node) {
      if (node === transformer) return;
      var r = node.getClientRect({ relativeTo: layer });
      var bottom = r.y + r.height;
      if (bottom > maxBottom) maxBottom = bottom;
    });
    // Add layer offset (margin) + padding below last element
    var neededH = maxBottom + mt + 80; // 80px breathing room
    var newH = Math.max(minH, Math.ceil(neededH));
    if (newH !== stage.height()) {
      stage.height(newH);
      var container = document.getElementById('kdCanvasContainer');
      container.style.height = newH + 'px';
      drawMarginGuides(); // redraw guides for new height
      drawPageBreaks();   // show page break indicators
      updateRulers();
      stage.batchDraw();
    }
  }

  // Draw dashed page-break lines at each page boundary
  function drawPageBreaks() {
    // Remove old page break lines
    if (guideLayer) {
      guideLayer.find('._pageBreak').forEach(function (n) { n.destroy(); });
    }
    var onePageH = Math.round(originalPageH * PX_PER_MM);
    var totalH = stage.height();
    var sW = stage.width();
    if (totalH <= onePageH) return; // single page, no breaks needed
    for (var y = onePageH; y < totalH; y += onePageH) {
      var line = new Konva.Line({
        points: [0, y, sW, y],
        stroke: '#ef4444', strokeWidth: 1,
        dash: [8, 4], opacity: 0.5,
        listening: false, name: '_pageBreak'
      });
      guideLayer.add(line);
      // Page number label
      var pageNum = Math.floor(y / onePageH) + 1;
      var label = new Konva.Text({
        x: sW - 60, y: y + 4,
        text: 'Page ' + pageNum,
        fontSize: 10, fill: '#ef4444',
        opacity: 0.6, listening: false, name: '_pageBreak'
      });
      guideLayer.add(label);
    }
    guideLayer.batchDraw();
  }

  // ══════════════════════════════════════
  // INIT — paper from DB, no selector
  // ══════════════════════════════════════
  function init() {
    var size = cfg.paperSize || 'A4';
    var orient = cfg.pageOrientation || 'portrait';
    document.getElementById('kdPaperBadge').textContent = size + ' · ' + orient;
    setupCanvas(size, orient);
    setupDragDrop();
    bindEvents();
    bindMarginEvents();
    bindZoneEvents();
    bindVariablesPanel();
    loadVariables();
    loadComputedVars();
    loadDesign();
    setTimeout(updateRulers, 500); // initial ruler draw
  }

  function setupCanvas(size, orient) {
    var p = PAPERS[size] || PAPERS['A4'];
    pageW = p.w; pageH = p.h;
    if (orient === 'landscape') { var t = pageW; pageW = pageH; pageH = t; }
    originalPageH = pageH; // save for auto-extend reference
    var cW = Math.round(pageW * PX_PER_MM);
    var cH = Math.round(pageH * PX_PER_MM);
    var container = document.getElementById('kdCanvasContainer');
    container.style.width = cW + 'px';
    container.style.height = cH + 'px';
    if (stage) stage.destroy();
    stage = new Konva.Stage({ container: 'kdCanvasContainer', width: cW, height: cH });
    layer = new Konva.Layer();
    stage.add(layer);
    // Guide layer renders ON TOP of content layer
    guideLayer = new Konva.Layer({ listening: false });
    stage.add(guideLayer);
    initGuideLines();
    initInsertionIndicator();
    transformer = new Konva.Transformer({
      rotateEnabled: false, borderStroke: '#6366f1',
      anchorStroke: '#6366f1', anchorFill: '#fff', anchorSize: 8
    });
    layer.add(transformer);

    stage.on('click tap', function (e) {
      // If rubber-band was just used, skip the click
      if (rubberBand.justFinished) { rubberBand.justFinished = false; return; }

      if (e.target === stage) {
        // Click on empty canvas — deselect all
        if (!e.evt.shiftKey) deselect();
        return;
      }
      var node = findTopGroup(e.target);
      if (node === stage || node === layer) return;

      if (e.evt.shiftKey) {
        // Shift+click: toggle node in/out of multi-selection
        toggleMultiSelect(node);
      } else {
        // Normal click: select just this node
        selectNode(node);
      }
    });

    stage.on('dblclick dbltap', function (e) {
      var node = findTopGroup(e.target);
      if (node.getAttr && node.getAttr('customType') === 'data-table') {
        editTable(node); return;
      }
      if (node.getAttr && node.getAttr('customType') === 'static-table') {
        editStaticTable(node); return;
      }
      if (e.target.className === 'Text') editTextInline(e.target);
    });

    // ── Rubber-band (marquee) selection with auto-scroll ──────────────────────
    // Auto-scroll state for rubber-band selection
    var rbAutoScroll = { animId: null, scrollEl: null, speedX: 0, speedY: 0 };
    var AUTOSCROLL_EDGE = 40; // px — distance from edge to trigger scroll
    var AUTOSCROLL_SPEED = 8; // px per frame — base scroll speed

    /** Convert a window mouse event to stage (canvas) coordinates */
    function windowEventToStagePos(evt) {
      var containerRect = stage.container().getBoundingClientRect();
      return {
        x: (evt.clientX - containerRect.left) / zoom,
        y: (evt.clientY - containerRect.top) / zoom
      };
    }

    /** Auto-scroll animation loop — scrolls the container while near edges */
    function rbAutoScrollTick() {
      if (!rubberBand.active || !rbAutoScroll.scrollEl) return;
      var scrolled = false;
      if (rbAutoScroll.speedX !== 0) {
        rbAutoScroll.scrollEl.scrollLeft += rbAutoScroll.speedX;
        scrolled = true;
      }
      if (rbAutoScroll.speedY !== 0) {
        rbAutoScroll.scrollEl.scrollTop += rbAutoScroll.speedY;
        scrolled = true;
      }
      if (scrolled && rubberBand.rect) {
        // After scrolling, recalculate the current mouse position in stage space
        // and update the rubber-band rect accordingly
        var pos = windowEventToStagePos(rubberBand._lastEvt);
        var x = Math.min(rubberBand.startX, pos.x);
        var y = Math.min(rubberBand.startY, pos.y);
        var w = Math.abs(pos.x - rubberBand.startX);
        var h = Math.abs(pos.y - rubberBand.startY);
        rubberBand.rect.setAttrs({ x: x, y: y, width: w, height: h });
        guideLayer.batchDraw();
      }
      rbAutoScroll.animId = requestAnimationFrame(rbAutoScrollTick);
    }

    /** Start auto-scroll loop if not already running */
    function rbStartAutoScroll() {
      if (rbAutoScroll.animId) return;
      rbAutoScroll.animId = requestAnimationFrame(rbAutoScrollTick);
    }

    /** Stop auto-scroll loop */
    function rbStopAutoScroll() {
      if (rbAutoScroll.animId) {
        cancelAnimationFrame(rbAutoScroll.animId);
        rbAutoScroll.animId = null;
      }
      rbAutoScroll.speedX = 0;
      rbAutoScroll.speedY = 0;
    }

    /** Window-level mousemove handler for rubber-band with auto-scroll */
    function rbWindowMouseMove(evt) {
      if (!rubberBand.active) return;
      rubberBand._lastEvt = evt; // store for auto-scroll recalc

      // Update rubber-band rect from window mouse position
      var pos = windowEventToStagePos(evt);
      var x = Math.min(rubberBand.startX, pos.x);
      var y = Math.min(rubberBand.startY, pos.y);
      var w = Math.abs(pos.x - rubberBand.startX);
      var h = Math.abs(pos.y - rubberBand.startY);
      if (rubberBand.rect) {
        rubberBand.rect.setAttrs({ x: x, y: y, width: w, height: h });
        guideLayer.batchDraw();
      }

      // Auto-scroll: check mouse position relative to scroll container
      var scrollEl = rbAutoScroll.scrollEl;
      if (!scrollEl) return;
      var scrollRect = scrollEl.getBoundingClientRect();
      var dx = 0, dy = 0;

      // Bottom edge
      if (evt.clientY > scrollRect.bottom - AUTOSCROLL_EDGE) {
        dy = AUTOSCROLL_SPEED * Math.min(1, (evt.clientY - (scrollRect.bottom - AUTOSCROLL_EDGE)) / AUTOSCROLL_EDGE);
        dy = Math.max(dy, 2);
      }
      // Top edge
      else if (evt.clientY < scrollRect.top + AUTOSCROLL_EDGE) {
        dy = -AUTOSCROLL_SPEED * Math.min(1, ((scrollRect.top + AUTOSCROLL_EDGE) - evt.clientY) / AUTOSCROLL_EDGE);
        dy = Math.min(dy, -2);
      }
      // Right edge
      if (evt.clientX > scrollRect.right - AUTOSCROLL_EDGE) {
        dx = AUTOSCROLL_SPEED * Math.min(1, (evt.clientX - (scrollRect.right - AUTOSCROLL_EDGE)) / AUTOSCROLL_EDGE);
        dx = Math.max(dx, 2);
      }
      // Left edge
      else if (evt.clientX < scrollRect.left + AUTOSCROLL_EDGE) {
        dx = -AUTOSCROLL_SPEED * Math.min(1, ((scrollRect.left + AUTOSCROLL_EDGE) - evt.clientX) / AUTOSCROLL_EDGE);
        dx = Math.min(dx, -2);
      }

      rbAutoScroll.speedX = dx;
      rbAutoScroll.speedY = dy;

      if (dx !== 0 || dy !== 0) {
        rbStartAutoScroll();
      } else {
        rbStopAutoScroll();
      }
    }

    /** Window-level mouseup handler — finalize rubber-band selection */
    function rbWindowMouseUp(evt) {
      if (!rubberBand.active) return;
      rubberBand.active = false;
      rbStopAutoScroll();

      // Remove window listeners
      window.removeEventListener('mousemove', rbWindowMouseMove);
      window.removeEventListener('mouseup', rbWindowMouseUp);

      if (!rubberBand.rect) return;
      var selRect = rubberBand.rect.getClientRect();
      rubberBand.rect.destroy();
      rubberBand.rect = null;
      guideLayer.batchDraw();

      // Only process if the rectangle has meaningful size (not a simple click)
      if (selRect.width < 5 && selRect.height < 5) return;

      rubberBand.justFinished = true; // Prevent the click event from firing

      // Find all nodes that intersect the selection rectangle
      var hits = [];
      layer.getChildren().forEach(function (child) {
        if (child === transformer || child.className === 'Transformer') return;
        var cr = child.getClientRect();
        // Check intersection
        if (cr.x < selRect.x + selRect.width &&
          cr.x + cr.width > selRect.x &&
          cr.y < selRect.y + selRect.height &&
          cr.y + cr.height > selRect.y) {
          hits.push(child);
        }
      });

      if (hits.length === 0) {
        deselect();
      } else if (hits.length === 1) {
        selectNode(hits[0]);
      } else {
        selectMultipleNodes(hits);
      }
    }

    stage.on('mousedown', function (e) {
      // Only start rubber-band on empty canvas click (not on a node)
      if (e.target !== stage) return;
      if (e.evt.button !== 0) return; // Left button only

      var pos = stage.getPointerPosition();
      rubberBand.active = true;
      rubberBand.startX = pos.x;
      rubberBand.startY = pos.y;
      rubberBand._lastEvt = e.evt; // store native event for auto-scroll

      // Cache scroll container reference
      rbAutoScroll.scrollEl = document.getElementById('kdCanvasScroll');

      // Create the selection rectangle on guideLayer (above elements)
      if (rubberBand.rect) {
        rubberBand.rect.destroy();
      }
      rubberBand.rect = new Konva.Rect({
        x: pos.x, y: pos.y, width: 0, height: 0,
        fill: 'rgba(99, 102, 241, 0.1)',
        stroke: '#6366f1', strokeWidth: 1,
        dash: [4, 4],
        listening: false
      });
      guideLayer.add(rubberBand.rect);

      // Attach window-level listeners for tracking mouse outside canvas
      window.addEventListener('mousemove', rbWindowMouseMove);
      window.addEventListener('mouseup', rbWindowMouseUp);
    });

    // Keep stage mousemove for non-rubber-band mouse tracking (snap guides etc.)
    // The rubber-band update is now handled by rbWindowMouseMove above.
    stage.on('mousemove', function (e) {
      if (!rubberBand.active) return;
      // Rubber-band is handled by window listener — skip here to avoid double update
    });

    // Keep stage mouseup as a fallback (window mouseup is the primary handler)
    stage.on('mouseup', function (e) {
      if (!rubberBand.active) return;
      // Delegate to the window handler to avoid duplicate logic
      rbWindowMouseUp(e.evt);
    });

    // NOTE: Do NOT recordHistory() here — loadDesign() will record
    // the initial state after the async fetch completes.
  }

  /**
   * Walk up from any clicked child to find the top-level Group (table)
   * or return the node itself if it's not inside a Group.
   */
  function findTopGroup(node) {
    if (!node || node === layer || node === stage) return node;
    var current = node;
    var topGroup = null;
    // Safety: check if getParent exists (Konva Stage doesn't have it)
    while (current && typeof current.getParent === 'function') {
      var parent = current.getParent();
      if (!parent || parent === stage) break;
      if (current.className === 'Group' && parent === layer) {
        topGroup = current;
        break;
      }
      if (parent === layer) {
        topGroup = current;
        break;
      }
      current = parent;
    }
    return topGroup || node;
  }

  // ══════════════════════════════════════
  // SMART SNAP GUIDES (Figma-style)
  // ══════════════════════════════════════
  function initGuideLines() {
    // Horizontal guide (red dashed line)
    guideLineH = new Konva.Line({
      points: [], stroke: '#ef4444', strokeWidth: 1,
      dash: [6, 4], visible: false
    });
    // Vertical guide (red dashed line)
    guideLineV = new Konva.Line({
      points: [], stroke: '#ef4444', strokeWidth: 1,
      dash: [6, 4], visible: false
    });
    // Center label — H
    guideLabelH = new Konva.Label({ visible: false });
    guideLabelH.add(new Konva.Tag({ fill: '#ef4444', cornerRadius: 3 }));
    guideLabelH.add(new Konva.Text({ text: 'CENTER', fontSize: 9, fill: '#fff', padding: 2, fontFamily: 'Inter, Arial' }));
    // Center label — V
    guideLabelV = new Konva.Label({ visible: false });
    guideLabelV.add(new Konva.Tag({ fill: '#ef4444', cornerRadius: 3 }));
    guideLabelV.add(new Konva.Text({ text: 'CENTER', fontSize: 9, fill: '#fff', padding: 2, fontFamily: 'Inter, Arial' }));

    guideLayer.add(guideLineH);
    guideLayer.add(guideLineV);
    guideLayer.add(guideLabelH);
    guideLayer.add(guideLabelV);
  }

  function getSnapTargets(movingNode) {
    var sW = stage.width(), sH = stage.height();
    var targets = [];
    // Canvas center
    targets.push({ cx: sW / 2, cy: sH / 2, label: 'CENTER', type: 'canvas' });
    // Canvas edges
    targets.push({ cx: 0, cy: 0, label: 'EDGE', type: 'edge' });
    targets.push({ cx: sW, cy: sH, label: 'EDGE', type: 'edge' });
    // Other elements on canvas
    layer.children.forEach(function (child) {
      if (child === movingNode || child === transformer) return;
      if (child.className === 'Transformer') return;
      var rect = child.getClientRect({ relativeTo: layer });
      targets.push({
        cx: rect.x + rect.width / 2,
        cy: rect.y + rect.height / 2,
        left: rect.x, right: rect.x + rect.width,
        top: rect.y, bottom: rect.y + rect.height,
        label: '', type: 'element'
      });
    });
    return targets;
  }

  function handleSnapGuides(node) {
    var rect = node.getClientRect({ relativeTo: layer });
    var nodeCX = rect.x + rect.width / 2;
    var nodeCY = rect.y + rect.height / 2;
    var nodeLeft = rect.x, nodeRight = rect.x + rect.width;
    var nodeTop = rect.y, nodeBottom = rect.y + rect.height;
    var sW = stage.width(), sH = stage.height();
    var targets = getSnapTargets(node);
    var snapH = null, snapV = null;
    var bestDH = SNAP_THRESHOLD + 1, bestDV = SNAP_THRESHOLD + 1;

    for (var i = 0; i < targets.length; i++) {
      var t = targets[i];
      // Vertical alignment (X axis — show vertical line)
      // Check: node center-X vs target center-X
      var dCX = Math.abs(nodeCX - t.cx);
      if (dCX < bestDV) {
        bestDV = dCX;
        snapV = { x: t.cx, label: t.type === 'canvas' ? 'CENTER' : '', adjustX: t.cx - nodeCX };
      }
      // Check node edges vs target edges
      if (t.left !== undefined) {
        var dL = Math.abs(nodeLeft - t.left);
        if (dL < bestDV) { bestDV = dL; snapV = { x: t.left, label: '', adjustX: t.left - nodeLeft }; }
        var dR = Math.abs(nodeRight - t.right);
        if (dR < bestDV) { bestDV = dR; snapV = { x: t.right, label: '', adjustX: t.right - nodeRight }; }
      }

      // Horizontal alignment (Y axis — show horizontal line)
      var dCY = Math.abs(nodeCY - t.cy);
      if (dCY < bestDH) {
        bestDH = dCY;
        snapH = { y: t.cy, label: t.type === 'canvas' ? 'CENTER' : '', adjustY: t.cy - nodeCY };
      }
      if (t.top !== undefined) {
        var dT = Math.abs(nodeTop - t.top);
        if (dT < bestDH) { bestDH = dT; snapH = { y: t.top, label: '', adjustY: t.top - nodeTop }; }
        var dB = Math.abs(nodeBottom - t.bottom);
        if (dB < bestDH) { bestDH = dB; snapH = { y: t.bottom, label: '', adjustY: t.bottom - nodeBottom }; }
      }
    }

    // Apply snaps & show guides
    if (snapV && bestDV <= SNAP_THRESHOLD) {
      node.x(node.x() + snapV.adjustX);
      guideLineV.points([snapV.x, 0, snapV.x, sH]);
      guideLineV.visible(true);
      if (snapV.label) {
        guideLabelV.x(snapV.x + 4); guideLabelV.y(10);
        guideLabelV.getText().text(snapV.label);
        guideLabelV.visible(true);
      } else { guideLabelV.visible(false); }
    } else {
      guideLineV.visible(false);
      guideLabelV.visible(false);
    }

    if (snapH && bestDH <= SNAP_THRESHOLD) {
      node.y(node.y() + snapH.adjustY);
      guideLineH.points([0, snapH.y, sW, snapH.y]);
      guideLineH.visible(true);
      if (snapH.label) {
        guideLabelH.x(10); guideLabelH.y(snapH.y + 4);
        guideLabelH.getText().text(snapH.label);
        guideLabelH.visible(true);
      } else { guideLabelH.visible(false); }
    } else {
      guideLineH.visible(false);
      guideLabelH.visible(false);
    }

    guideLayer.batchDraw();
    updateRulerIndicators(node);
  }

  function hideGuides() {
    if (guideLineH) guideLineH.visible(false);
    if (guideLineV) guideLineV.visible(false);
    if (guideLabelH) guideLabelH.visible(false);
    if (guideLabelV) guideLabelV.visible(false);
    if (insertionLine) insertionLine.visible(false);
    if (guideLayer) guideLayer.batchDraw();
    hideRulerIndicators();
  }

  // ══════════════════════════════════════
  // SMART REFLOW SYSTEM (GrapesJS-style)
  // ══════════════════════════════════════
  var insertionLine = null;
  var preReflowPositions = {}; // store Y positions before drag starts

  function initInsertionIndicator() {
    insertionLine = new Konva.Line({
      points: [0, 0, stage.width(), 0],
      stroke: '#6366f1', strokeWidth: 2,
      dash: [8, 4], visible: false,
      listening: false
    });
    guideLayer.add(insertionLine);
  }

  /**
   * Get all layout-participating nodes sorted by Y position.
   */
  function getLayoutNodes(excludeNode) {
    var nodes = [];
    layer.getChildren().forEach(function (node) {
      if (node === transformer || node.className === 'Transformer') return;
      if (excludeNode && node === excludeNode) return;
      nodes.push(node);
    });
    nodes.sort(function (a, b) { return a.y() - b.y(); });
    return nodes;
  }

  /**
   * Save all Y positions before a drag begins so we can restore on cancel.
   */
  function savePreDragPositions() {
    preReflowPositions = {};
    layer.getChildren().forEach(function (node) {
      if (node === transformer || node.className === 'Transformer') return;
      preReflowPositions[node._id] = node.y();
    });
  }

  /**
   * Group nodes into "rows" — elements at a similar Y position (within ROW_THRESHOLD px)
   * are considered part of the same row and move together.
   */
  var ROW_THRESHOLD = 15; // px — elements within this Y-distance are in the same row

  function groupIntoRows(nodes) {
    if (nodes.length === 0) return [];
    var rows = [];
    var currentRow = [nodes[0]];
    var currentRowY = nodes[0].y();

    for (var i = 1; i < nodes.length; i++) {
      if (Math.abs(nodes[i].y() - currentRowY) <= ROW_THRESHOLD) {
        // Same row
        currentRow.push(nodes[i]);
      } else {
        // New row
        rows.push(currentRow);
        currentRow = [nodes[i]];
        currentRowY = nodes[i].y();
      }
    }
    rows.push(currentRow); // push last row
    return rows;
  }

  /**
   * Get the bounding box height of a row (max bottom - min top).
   */
  function getRowHeight(row) {
    var minY = Infinity, maxBottom = 0;
    row.forEach(function (node) {
      var r = node.getClientRect({ relativeTo: layer });
      if (r.y < minY) minY = r.y;
      var bottom = r.y + r.height;
      if (bottom > maxBottom) maxBottom = bottom;
    });
    return maxBottom - minY;
  }

  /**
   * Get the minimum Y of a row.
   */
  function getRowY(row) {
    var minY = Infinity;
    row.forEach(function (node) {
      if (node.y() < minY) minY = node.y();
    });
    return minY;
  }

  /**
   * During drag: Show insertion indicator line showing WHERE the element will be inserted.
   * Does NOT modify other nodes' positions (prevents history corruption and jitter).
   * The actual reflow happens on dragend via reflowAfterDrag().
   */
  function showInsertionIndicator(movingNode) {
    if (!movingNode) return;
    var movingRect = movingNode.getClientRect({ relativeTo: layer });
    var movingBottom = movingRect.y + movingRect.height;
    var GAP = 8;

    // Show insertion indicator at the bottom edge of moving node
    if (insertionLine) {
      insertionLine.points([0, movingBottom + GAP / 2, stage.width(), movingBottom + GAP / 2]);
      insertionLine.visible(true);
      guideLayer.batchDraw();
    }
  }

  /**
   * After drag ends: Reflow the layout so the dragged element is properly inserted
   * at its new position and all other elements adjust around it.
   * Uses saved pre-drag positions to compute the correct shifts.
   */
  function reflowAfterDrag(movedNode) {
    if (!movedNode) return;
    var GAP = 8;

    // Get ALL nodes (including the moved one) sorted by current Y
    var allNodes = getLayoutNodes(null);
    if (allNodes.length < 2) return;

    // Group into rows based on their current Y positions
    var rows = groupIntoRows(allNodes);

    // Now compact: remove vertical gaps between rows  
    var currentY = Math.max(10, getRowY(rows[0]));

    for (var i = 0; i < rows.length; i++) {
      var row = rows[i];
      var rowY = getRowY(row);
      var rowH = getRowHeight(row);

      // If there's a gap or overlap before this row, pull it up or push it down
      if (Math.abs(rowY - currentY) > 1) {
        var shift = rowY - currentY;
        row.forEach(function (node) {
          node.y(node.y() - shift);
        });
        rowY = currentY;
      }

      currentY = rowY + rowH + GAP;
    }

    layer.batchDraw();
    autoExtendCanvas();
  }

  /**
   * After drag ends or delete: compact the layout to remove vertical gaps.
   * Groups elements into rows, then slides rows up to fill any empty space.
   * Preserves relative X positions and within-row offsets.
   */
  function reflowLayout() {
    var nodes = getLayoutNodes(null);

    var rows = groupIntoRows(nodes);
    var GAP = 8;

    // Process rows from top to bottom
    var currentY = Math.max(10, getRowY(rows[0]));

    for (var i = 0; i < rows.length; i++) {
      var row = rows[i];
      var rowY = getRowY(row);
      var rowH = getRowHeight(row);

      // Check if this row contains a spacer — spacers are anchors, never moved
      var hasSpacer = row.some(function (n) { return n.getAttr && n.getAttr('customType') === 'spacer'; });

      if (hasSpacer) {
        // Don't move the spacer row — just update currentY to account for it
        currentY = rowY + rowH + GAP;
        continue;
      }

      // If this row's Y is different than where it should be, pull it up or push it down
      if (Math.abs(rowY - currentY) > 1) {
        var shift = rowY - currentY;
        row.forEach(function (node) {
          node.y(node.y() - shift);
        });
        rowY = currentY;
      }

      // Next row starts after this row's height + gap
      currentY = rowY + rowH + GAP;
    }

    layer.batchDraw();
    autoExtendCanvas();
  }

  // ══════════════════════════════════════
  // DRAG & DROP from sidebar to canvas
  // ══════════════════════════════════════
  function setupDragDrop() {
    var items = document.querySelectorAll('.kd-element-item[draggable]');
    var canvasArea = document.getElementById('kdCanvasArea');

    items.forEach(function (el) {
      el.addEventListener('dragstart', function (e) {
        e.dataTransfer.setData('tool', this.dataset.tool);
        e.dataTransfer.effectAllowed = 'copy';
        this.classList.add('kd-dragging');
      });
      el.addEventListener('dragend', function () {
        this.classList.remove('kd-dragging');
        canvasArea.classList.remove('kd-drag-over');
      });
    });

    canvasArea.addEventListener('dragover', function (e) {
      e.preventDefault();
      e.dataTransfer.dropEffect = 'copy';
      canvasArea.classList.add('kd-drag-over');
    });
    canvasArea.addEventListener('dragleave', function (e) {
      if (!canvasArea.contains(e.relatedTarget)) canvasArea.classList.remove('kd-drag-over');
    });
    canvasArea.addEventListener('drop', function (e) {
      e.preventDefault();
      canvasArea.classList.remove('kd-drag-over');

      // Check if this is a variable drop or element drop
      var varKey = e.dataTransfer.getData('varKey');
      var tool = e.dataTransfer.getData('tool');
      if (!tool && !varKey) return;

      // Calculate drop position on canvas (account for layer offset from margins)
      var containerRect = stage.container().getBoundingClientRect();
      var dropX = (e.clientX - containerRect.left) / zoom - layer.x();
      var dropY = (e.clientY - containerRect.top) / zoom - layer.y();
      // Clamp X only (Y is unlimited — canvas auto-extends)
      dropX = Math.max(10, Math.min(dropX, stage.width() - layer.x() - 50));
      dropY = Math.max(10, dropY);

      // ── Variable drop: create a text node with {{variable_key}} ──
      if (varKey) {
        var varLabel = e.dataTransfer.getData('varLabel') || varKey;
        addText('{{' + varKey + '}}', {
          x: dropX, y: dropY, width: 160,
          fontSize: 12, fill: '#000'
        });
        toast('Variable "' + varLabel + '" added', 'success');
        return;
      }

      // ── Element drop ──
      // Estimate the size of the new element for collision detection
      var estW = 200, estH = 30;
      if (tool === 'heading') { estW = 300; estH = 40; }
      else if (tool === 'rect') { estW = 150; estH = 80; }
      else if (tool === 'line') { estW = 300; estH = 4; }
      else if (tool === 'table') { estW = 500; estH = 200; }
      else if (tool === 'stable') { estW = 400; estH = 150; }
      else if (tool === 'spacer') { estW = 500; estH = 30; }
      else if (tool === 'qrcode') { estW = 80; estH = 80; }

      // Smart position — push below overlapping elements
      dropY = findFreeY(dropX, dropY, estW, estH);

      switch (tool) {
        case 'text': addText('Type here...', { x: dropX, y: dropY }); break;
        case 'heading': addText('Heading', { x: dropX, y: dropY, fontSize: 24, fontStyle: 'bold', width: 300 }); break;
        case 'rect': addRect(dropX, dropY); break;
        case 'line': addLine(dropX, dropY); break;
        case 'image': addImage(); break;
        case 'table': pendingTableDrop = { x: dropX, y: dropY }; showTableModal(); break;
        case 'stable': pendingTableDrop = { x: dropX, y: dropY }; showStaticTableModal(); break;
        case 'spacer': addSpacer(dropX, dropY); break;
        case 'qrcode': addQrCode(dropX, dropY); break;
      }
    });
  }

  // ══════════════════════════════════════
  // VARIABLES PANEL — Load, Render, Search
  // ══════════════════════════════════════
  var variablesData = {}; // { groupName: [{key, label, sample, ...}] }
  var computedVarsData = []; // [{id, var_name, var_label, formula}]

  function loadVariables() {
    if (!cfg.fieldsUrl) return;
    fetch(cfg.fieldsUrl)
      .then(function (r) { return r.json(); })
      .then(function (data) {
        variablesData = data;
        renderVariables(data);
      })
      .catch(function (err) {
        console.warn('Failed to load variables:', err);
        document.getElementById('kdVarsList').innerHTML =
          '<div class="kd-props-empty" style="padding:15px;"><p style="font-size:10px;color:#ef4444;">Failed to load variables</p></div>';
      });
  }

  function loadComputedVars() {
    if (!cfg.computedVarsUrl) return;
    fetch(cfg.computedVarsUrl)
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.success) computedVarsData = res.vars || [];
        else computedVarsData = [];
        // Re-render variables to include computed group
        renderVariables(variablesData, document.getElementById('kdVarSearch') ? document.getElementById('kdVarSearch').value : '');
      })
      .catch(function (err) { console.warn('Failed to load computed vars:', err); });
  }

  function renderVariables(grouped, filter) {
    var container = document.getElementById('kdVarsList');
    if (!container) return;
    container.innerHTML = '';
    filter = (filter || '').toLowerCase().trim();

    var totalShown = 0;

    // ── Computed Variables Group (always on top) ──
    if (computedVarsData.length > 0) {
      var cvFiltered = computedVarsData;
      if (filter) {
        cvFiltered = computedVarsData.filter(function (v) {
          return v.var_label.toLowerCase().indexOf(filter) !== -1 ||
            v.var_name.toLowerCase().indexOf(filter) !== -1;
        });
      }
      if (cvFiltered.length > 0) {
        totalShown += cvFiltered.length;
        var cvGroupDiv = document.createElement('div');
        cvGroupDiv.className = 'kd-var-group';

        var cvHeader = document.createElement('div');
        cvHeader.className = 'kd-var-group-header';
        cvHeader.style.background = 'rgba(16,185,129,0.12)';
        cvHeader.innerHTML = '<span class="kd-var-group-name" style="color:#10b981;"><i class="fa fa-calculator" style="margin-right:4px;"></i>COMPUTED</span>' +
          '<span style="display:flex;align-items:center;gap:4px;">' +
          '<span class="kd-var-group-count">' + cvFiltered.length + '</span>' +
          '<i class="fa fa-chevron-down kd-var-group-chevron"></i>' +
          '</span>';
        cvHeader.addEventListener('click', function () { cvGroupDiv.classList.toggle('collapsed'); });
        cvGroupDiv.appendChild(cvHeader);

        var cvItemsDiv = document.createElement('div');
        cvItemsDiv.className = 'kd-var-group-items';

        cvFiltered.forEach(function (v) {
          var chip = document.createElement('div');
          chip.className = 'kd-var-chip kd-var-chip-computed';
          chip.draggable = true;
          chip.setAttribute('data-var-key', v.var_name);
          chip.setAttribute('data-var-label', v.var_label);
          chip.setAttribute('title', '{{' + v.var_name + '}} = ' + v.formula);

          chip.innerHTML = '<span class="kd-var-chip-icon" style="color:#10b981;"><i class="fa fa-calculator"></i></span> ' +
            '<span style="flex:1;">' + v.var_label + '</span>' +
            '<span class="kd-cv-actions">' +
            '<i class="fa fa-pencil kd-cv-edit" data-cv-id="' + v.id + '" title="Edit"></i>' +
            '<i class="fa fa-trash kd-cv-delete" data-cv-id="' + v.id + '" title="Delete"></i>' +
            '</span>';

          // Drag events
          chip.addEventListener('dragstart', function (e) {
            e.dataTransfer.setData('varKey', v.var_name);
            e.dataTransfer.setData('varLabel', v.var_label);
            e.dataTransfer.effectAllowed = 'copy';
            chip.classList.add('kd-dragging');
          });
          chip.addEventListener('dragend', function () {
            chip.classList.remove('kd-dragging');
            var ca = document.getElementById('kdCanvasArea');
            if (ca) ca.classList.remove('kd-drag-over');
          });

          // Edit action
          chip.querySelector('.kd-cv-edit').addEventListener('click', function (e) {
            e.stopPropagation();
            openComputedVarModal(v);
          });

          // Delete action
          chip.querySelector('.kd-cv-delete').addEventListener('click', function (e) {
            e.stopPropagation();
            if (!confirm('Delete computed variable "' + v.var_label + '"?')) return;
            fetch(cfg.deleteComputedVarUrl + '/' + v.id, { method: 'DELETE' })
              .then(function (r) { return r.json(); })
              .then(function (res) {
                if (res.success) {
                  toast('Variable deleted', 'success');
                  loadComputedVars();
                } else {
                  toast('Delete failed', 'error');
                }
              })
              .catch(function () { toast('Delete error', 'error'); });
          });

          cvItemsDiv.appendChild(chip);
        });

        cvGroupDiv.appendChild(cvItemsDiv);
        container.appendChild(cvGroupDiv);
      }
    }

    // ── Standard Variable Groups ──
    var groupNames = Object.keys(grouped);

    groupNames.forEach(function (groupName) {
      var vars = grouped[groupName];
      var filtered2 = vars;
      if (filter) {
        filtered2 = vars.filter(function (v) {
          return v.label.toLowerCase().indexOf(filter) !== -1 ||
            v.key.toLowerCase().indexOf(filter) !== -1;
        });
      }
      if (filtered2.length === 0) return;
      totalShown += filtered2.length;

      var groupDiv = document.createElement('div');
      groupDiv.className = 'kd-var-group';

      // Group header
      var header = document.createElement('div');
      header.className = 'kd-var-group-header';
      header.innerHTML = '<span class="kd-var-group-name">' + groupName + '</span>' +
        '<span style="display:flex;align-items:center;gap:4px;">' +
        '<span class="kd-var-group-count">' + filtered2.length + '</span>' +
        '<i class="fa fa-chevron-down kd-var-group-chevron"></i>' +
        '</span>';
      header.addEventListener('click', function () {
        groupDiv.classList.toggle('collapsed');
      });
      groupDiv.appendChild(header);

      // Items container
      var itemsDiv = document.createElement('div');
      itemsDiv.className = 'kd-var-group-items';

      filtered2.forEach(function (v) {
        var chip = document.createElement('div');
        chip.className = 'kd-var-chip';
        chip.draggable = true;
        chip.setAttribute('data-var-key', v.key);
        chip.setAttribute('data-var-label', v.label);
        chip.setAttribute('title', '{{' + v.key + '}}' + (v.sample ? ' — e.g. ' + v.sample : ''));
        chip.innerHTML = '<span class="kd-var-chip-icon"><i class="fa fa-tag"></i></span> ' + v.label;

        // Drag events
        chip.addEventListener('dragstart', function (e) {
          e.dataTransfer.setData('varKey', v.key);
          e.dataTransfer.setData('varLabel', v.label);
          e.dataTransfer.effectAllowed = 'copy';
          chip.classList.add('kd-dragging');
        });
        chip.addEventListener('dragend', function () {
          chip.classList.remove('kd-dragging');
          var ca = document.getElementById('kdCanvasArea');
          if (ca) ca.classList.remove('kd-drag-over');
        });

        itemsDiv.appendChild(chip);
      });

      groupDiv.appendChild(itemsDiv);
      container.appendChild(groupDiv);
    });

    if (totalShown === 0) {
      container.innerHTML = '<div class="kd-props-empty" style="padding:15px;"><p style="font-size:10px;">No matching variables</p></div>';
    }
  }

  // Flatten all available variables into a single searchable list
  function getAllVariablesList() {
    var list = [];
    // Standard variables from registry
    var groups = variablesData || {};
    Object.keys(groups).forEach(function (groupName) {
      var items = groups[groupName];
      if (!Array.isArray(items)) return;
      items.forEach(function (v) {
        var k = v.key || v.variable || v.name || '';
        if (!k) return;
        list.push({
          key: k,
          label: v.label || k,
          group: groupName
        });
      });
    });
    // Existing computed variables (can be nested in formulas)
    if (Array.isArray(computedVarsData)) {
      computedVarsData.forEach(function (cv) {
        list.push({
          key: cv.var_name,
          label: cv.var_label,
          group: 'Computed'
        });
      });
    }
    return list;
  }

  // ── Computed Variable Modal — Tag-Based Formula Editor ──

  // Lookup helper: given a var_name, return its label
  function getVarLabel(varName) {
    // Check computed vars first
    for (var i = 0; i < computedVarsData.length; i++) {
      if (computedVarsData[i].var_name === varName) return { label: computedVarsData[i].var_label, computed: true };
    }
    // Check standard variables
    var groups = variablesData || {};
    var keys = Object.keys(groups);
    for (var g = 0; g < keys.length; g++) {
      var items = groups[keys[g]];
      if (!Array.isArray(items)) continue;
      for (var j = 0; j < items.length; j++) {
        var k = items[j].key || items[j].variable || items[j].name || '';
        if (k === varName) return { label: items[j].label || varName, computed: false };
      }
    }
    return { label: varName, computed: false };
  }

  // Create a tag span element for a variable
  function createFormulaTag(varName) {
    var info = getVarLabel(varName);
    var tag = document.createElement('span');
    tag.className = 'kd-formula-tag' + (info.computed ? ' computed' : '');
    tag.contentEditable = 'false';
    tag.setAttribute('data-var', varName);
    tag.innerHTML = '<i class="fa ' + (info.computed ? 'fa-calculator' : 'fa-tag') + ' kd-ft-icon"></i>' +
      '<span class="kd-ft-label">' + info.label + '</span>' +
      '<i class="fa fa-times kd-ft-remove"></i>';
    // Remove tag on X click
    tag.querySelector('.kd-ft-remove').addEventListener('mousedown', function (e) {
      e.preventDefault();
      e.stopPropagation();
      tag.remove();
      syncFormulaFromEditor();
    });
    return tag;
  }

  // Render formula string (e.g. "{{mc}} / {{gross_wt}}") into the editor as tags + operators
  function renderFormulaToEditor(formula) {
    var editor = document.getElementById('kdCvFormulaEditor');
    editor.innerHTML = '';
    if (!formula) return;
    // Split on {{...}} tokens, keeping delimiters
    var parts = formula.split(/({{[^}]+}})/g);
    parts.forEach(function (part) {
      var match = part.match(/^{{(.+?)}}$/);
      if (match) {
        var tag = createFormulaTag(match[1]);
        editor.appendChild(tag);
      } else if (part) {
        // Text node for operators like " / " or " + "
        editor.appendChild(document.createTextNode(part));
      }
    });
  }

  // Read the editor DOM and produce a raw formula string like "{{mc}} / {{gross_wt}}"
  function syncFormulaFromEditor() {
    var editor = document.getElementById('kdCvFormulaEditor');
    var hiddenInput = document.getElementById('kdCvFormula');
    var formula = '';
    editor.childNodes.forEach(function (node) {
      if (node.nodeType === 3) { // text node
        formula += node.textContent;
      } else if (node.nodeType === 1) { // element
        if (node.classList && node.classList.contains('kd-formula-tag')) {
          formula += '{{' + node.getAttribute('data-var') + '}}';
        } else {
          formula += node.textContent;
        }
      }
    });
    hiddenInput.value = formula.trim();
    return formula.trim();
  }

  // ── Formula Autocomplete ──
  var formulaSuggestionHighlight = -1;

  function showFormulaSuggestions(filter) {
    var dd = document.getElementById('kdCvFormulaSuggestions');
    var all = getAllVariablesList();

    // Sanitize filter and split into multiple search terms for fuzzy matching
    var rawSearch = (filter || '').toLowerCase().replace(/\{\{|\}\}/g, '').trim();
    var searchTerms = rawSearch.replace(/_/g, ' ').split(/\s+/).filter(Boolean);

    var matches = searchTerms.length ? all.filter(function (v) {
      var k = (v.key || '').toLowerCase().replace(/_/g, ' ');
      var l = (v.label || '').toLowerCase().replace(/_/g, ' ');
      // Match if EVERY search term is found in either the key or the label
      return searchTerms.every(function (term) {
        return k.indexOf(term) !== -1 || l.indexOf(term) !== -1;
      });
    }) : all;

    // Sort matches: prioritize those that START with the first search term
    if (searchTerms.length > 0) {
      var firstTerm = searchTerms[0];
      matches.sort(function (a, b) {
        var aLabel = a.label.toLowerCase();
        var bLabel = b.label.toLowerCase();
        var aStarts = aLabel.indexOf(firstTerm) === 0;
        var bStarts = bLabel.indexOf(firstTerm) === 0;
        if (aStarts && !bStarts) return -1;
        if (!aStarts && bStarts) return 1;
        return aLabel.localeCompare(bLabel);
      });
    }

    matches = matches.slice(0, 50);
    dd.innerHTML = '';
    formulaSuggestionHighlight = -1;

    if (matches.length === 0) { dd.classList.remove('open'); return; }

    matches.forEach(function (v, idx) {
      var item = document.createElement('div');
      item.className = 'kd-formula-suggestions-item';
      item.innerHTML = '<span class="kd-fs-label">' + v.label + '</span>' +
        '<span class="kd-fs-key">{{' + v.key + '}}</span>' +
        '<span class="kd-fs-group">' + v.group + '</span>';
      item.dataset.idx = idx;
      item.dataset.key = v.key;
      item.addEventListener('mousedown', function (e) {
        e.preventDefault();
        insertFormulaVar(v.key);
      });
      dd.appendChild(item);
    });
    dd.classList.add('open');
  }

  function hideFormulaSuggestions() {
    var dd = document.getElementById('kdCvFormulaSuggestions');
    if (dd) dd.classList.remove('open');
    formulaSuggestionHighlight = -1;
  }

  // Insert a variable tag at cursor position in the formula editor, replacing the trigger text ({{...)
  function insertFormulaVar(varKey) {
    var editor = document.getElementById('kdCvFormulaEditor');
    hideFormulaSuggestions();

    var sel = window.getSelection();
    if (sel.rangeCount) {
      var range = sel.getRangeAt(0);
      var textNode = range.endContainer;

      // If cursor is at the end of the editor or not in a text node
      if (textNode.nodeType !== 3) {
        var tag = createFormulaTag(varKey);
        var afterNode = document.createTextNode(' ');
        range.insertNode(afterNode);
        range.insertNode(tag);
        range.setStart(afterNode, 1);
        range.collapse(true);
        sel.removeAllRanges();
        sel.addRange(range);
      } else {
        var text = textNode.textContent;
        var pos = range.endOffset;
        var beforeText = text.substring(0, pos);

        // Strategy 1: Check for {{ trigger
        var triggerIdx = beforeText.lastIndexOf('{{');
        var replaceStart = -1;

        if (triggerIdx >= 0 && beforeText.indexOf('}}', triggerIdx) === -1) {
          replaceStart = triggerIdx;
        } else {
          // Strategy 2: Find the plain text word that triggered the suggestion
          var wordMatch = beforeText.match(/([a-zA-Z_][a-zA-Z0-9_ ]*?)$/);
          if (wordMatch) {
            replaceStart = pos - wordMatch[1].length;
          }
        }

        if (replaceStart >= 0) {
          var before = text.substring(0, replaceStart);
          var after = text.substring(pos);

          textNode.textContent = before;
          var tag = createFormulaTag(varKey);
          var afterNode = document.createTextNode(after || ' ');

          var parent = textNode.parentNode;
          var nextSib = textNode.nextSibling;
          parent.insertBefore(tag, nextSib);
          parent.insertBefore(afterNode, tag.nextSibling);

          var newRange = document.createRange();
          newRange.setStart(afterNode, (after ? 0 : 1));
          newRange.collapse(true);
          sel.removeAllRanges();
          sel.addRange(newRange);
        }
      }
    }
    syncFormulaFromEditor();
    editor.focus();
  }

  function bindFormulaEditor() {
    var editor = document.getElementById('kdCvFormulaEditor');
    var dd = document.getElementById('kdCvFormulaSuggestions');
    if (!editor || !dd) return;

    // On input: detect {{ trigger and show suggestions
    editor.addEventListener('input', function () {
      syncFormulaFromEditor();

      var sel = window.getSelection();
      if (!sel.rangeCount) return;
      var range = sel.getRangeAt(0);
      var textNode = range.endContainer;
      if (textNode.nodeType !== 3) { hideFormulaSuggestions(); return; }

      var text = textNode.textContent;
      var pos = range.endOffset;
      var before = text.substring(0, pos);

      // Strategy 1: Check for {{ trigger (existing behavior)
      var triggerIdx = before.lastIndexOf('{{');
      if (triggerIdx >= 0 && before.indexOf('}}', triggerIdx) === -1) {
        var partial = before.substring(triggerIdx + 2);
        showFormulaSuggestions(partial);
        return;
      }

      // Strategy 2: Plain text typing — find the last "word" (letters, digits, underscores)
      // after the last operator or space. e.g. "* net" → "net", "/ gross_wt" → "gross_wt"
      var wordMatch = before.match(/([a-zA-Z_][a-zA-Z0-9_ ]*?)$/);
      if (wordMatch && wordMatch[1].trim().length >= 2) {
        showFormulaSuggestions(wordMatch[1].trim());
      } else {
        hideFormulaSuggestions();
      }
    });

    // Keyboard navigation for suggestions and Backspace handling
    editor.addEventListener('keydown', function (e) {
      // 1. Suggestion navigation
      var items = dd.querySelectorAll('.kd-formula-suggestions-item');
      var isSuggestionsOpen = dd.classList.contains('open') && items.length > 0;

      if (isSuggestionsOpen) {
        if (e.key === 'ArrowDown') {
          e.preventDefault();
          formulaSuggestionHighlight = Math.min(formulaSuggestionHighlight + 1, items.length - 1);
          items.forEach(function (it, i) { it.classList.toggle('highlighted', i === formulaSuggestionHighlight); });
          if (items[formulaSuggestionHighlight]) items[formulaSuggestionHighlight].scrollIntoView({ block: 'nearest' });
          return;
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          formulaSuggestionHighlight = Math.max(formulaSuggestionHighlight - 1, 0);
          items.forEach(function (it, i) { it.classList.toggle('highlighted', i === formulaSuggestionHighlight); });
          if (items[formulaSuggestionHighlight]) items[formulaSuggestionHighlight].scrollIntoView({ block: 'nearest' });
          return;
        } else if ((e.key === 'Enter' || e.key === 'Tab') && formulaSuggestionHighlight >= 0) {
          e.preventDefault();
          var selItem = items[formulaSuggestionHighlight];
          if (selItem) insertFormulaVar(selItem.dataset.key);
          return;
        } else if (e.key === 'Escape') {
          e.preventDefault();
          hideFormulaSuggestions();
          return;
        }
      }

      // 2. Prevent Enter from creating new lines in a single-line formula editor
      if (e.key === 'Enter') {
        e.preventDefault();
        return;
      }

      // 3. Robust Backspace/Delete handling for variable tags
      if (e.key === 'Backspace' || e.key === 'Delete') {
        var sel = window.getSelection();
        if (!sel.isCollapsed) return;

        var range = sel.getRangeAt(0);
        var node = range.startContainer;
        var offset = range.startOffset;

        if (e.key === 'Backspace') {
          // If at start of text node, or in a node with just a space
          if (node.nodeType === 3 && offset === 0) {
            var prev = node.previousSibling;
            if (prev && prev.classList && prev.classList.contains('kd-formula-tag')) {
              e.preventDefault();
              prev.remove();
              syncFormulaFromEditor();
            }
          }
          // Handle the case where cursor is after a tag in the parent div
          else if (node === editor && offset > 0) {
            var prev = editor.childNodes[offset - 1];
            if (prev && prev.classList && prev.classList.contains('kd-formula-tag')) {
              e.preventDefault();
              prev.remove();
              syncFormulaFromEditor();
            }
          }
        } else { // Delete key
          // If at end of text node, check next sibling
          if (node.nodeType === 3 && offset === node.textContent.length) {
            var next = node.nextSibling;
            if (next && next.classList && next.classList.contains('kd-formula-tag')) {
              e.preventDefault();
              next.remove();
              syncFormulaFromEditor();
            }
          }
          // Handle deletion of tag immediately following cursor in parent div
          else if (node === editor && offset < editor.childNodes.length) {
            var next = editor.childNodes[offset];
            if (next && next.classList && next.classList.contains('kd-formula-tag')) {
              e.preventDefault();
              next.remove();
              syncFormulaFromEditor();
            }
          }
        }
      }
    });

    // Prevent pasting rich HTML
    editor.addEventListener('paste', function (e) {
      e.preventDefault();
      var text = (e.clipboardData || window.clipboardData).getData('text/plain');
      document.execCommand('insertText', false, text);
    });

    // Hide suggestions on blur
    editor.addEventListener('blur', function () {
      setTimeout(function () { hideFormulaSuggestions(); }, 200);
    });
  }

  function openComputedVarModal(existing) {
    var modal = document.getElementById('kdComputedVarModal');
    var nameInput = document.getElementById('kdCvName');
    var labelInput = document.getElementById('kdCvLabel');
    var formulaInput = document.getElementById('kdCvFormula');
    var title = document.getElementById('kdCvModalTitle');

    if (existing) {
      title.textContent = 'Edit Computed Variable';
      nameInput.value = existing.var_name;
      nameInput.disabled = true; // Can't rename
      labelInput.value = existing.var_label;
      formulaInput.value = existing.formula;
      renderFormulaToEditor(existing.formula);
    } else {
      title.textContent = 'Create Computed Variable';
      nameInput.value = '';
      nameInput.disabled = false;
      labelInput.value = '';
      formulaInput.value = '';
      renderFormulaToEditor('');
    }

    modal.style.display = 'flex';
    if (!existing) nameInput.focus(); else document.getElementById('kdCvFormulaEditor').focus();
  }

  function closeComputedVarModal() {
    document.getElementById('kdComputedVarModal').style.display = 'none';
    hideFormulaSuggestions();
  }

  function saveComputedVar() {
    var nameInput = document.getElementById('kdCvName');
    var labelInput = document.getElementById('kdCvLabel');

    // Sync the editor to hidden input before reading
    var formula = syncFormulaFromEditor();

    var varName = nameInput.value.replace(/[^a-z0-9_]/gi, '_').toLowerCase().trim();
    var varLabel = labelInput.value.trim();

    if (!varName) { toast('Variable name is required', 'error'); return; }
    if (!formula) { toast('Formula is required', 'error'); return; }
    if (!varLabel) varLabel = varName;

    var payload = { var_name: varName, var_label: varLabel, formula: formula };

    fetch(cfg.saveComputedVarUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res.success) {
          toast('Computed variable saved!', 'success');
          closeComputedVarModal();
          loadComputedVars();
        } else {
          toast(res.message || 'Save failed', 'error');
        }
      })
      .catch(function (err) { toast('Save error: ' + err.message, 'error'); });
  }

  function bindComputedVarModal() {
    var createBtn = document.getElementById('kdBtnCreateComputedVar');
    if (createBtn) {
      createBtn.addEventListener('click', function () { openComputedVarModal(null); });
    }
    document.getElementById('kdCvCancel').addEventListener('click', closeComputedVarModal);
    document.getElementById('kdCvModalClose').addEventListener('click', closeComputedVarModal);
    document.getElementById('kdCvSave').addEventListener('click', saveComputedVar);
    bindFormulaEditor();
  }

  function bindVariablesPanel() {
    // ─── Tab Switching ───
    var tabBar = document.getElementById('kdTabBar');
    if (tabBar) {
      tabBar.addEventListener('click', function (e) {
        var btn = e.target.closest('.kd-tab');
        if (!btn) return;
        var tabId = btn.dataset.tab;
        // Deactivate all tabs and content
        tabBar.querySelectorAll('.kd-tab').forEach(function (t) { t.classList.remove('active'); });
        document.querySelectorAll('.kd-tab-content').forEach(function (c) { c.classList.remove('active'); });
        // Activate clicked tab
        btn.classList.add('active');
        var content = document.querySelector('.kd-tab-content[data-tab="' + tabId + '"]');
        if (content) content.classList.add('active');
      });
    }

    // Search
    var searchInput = document.getElementById('kdVarSearch');
    if (searchInput) {
      var debounce = null;
      searchInput.addEventListener('input', function () {
        clearTimeout(debounce);
        var val = searchInput.value;
        debounce = setTimeout(function () {
          renderVariables(variablesData, val);
        }, 150);
      });
    }

    // Computed variable modal
    bindComputedVarModal();
  }

  // ─── Switch to a specific sidebar tab programmatically ───
  function switchToTab(tabId) {
    var tabBar = document.getElementById('kdTabBar');
    if (!tabBar) return;
    var btn = tabBar.querySelector('.kd-tab[data-tab="' + tabId + '"]');
    if (btn) btn.click();
  }

  // ══════════════════════════════════════
  // VARIABLE DROPDOWN — autocomplete for table modal inputs
  // ══════════════════════════════════════
  function getAllVariablesList() {
    // Build a flat, deduplicated list of all variables from variablesData
    var list = [];
    var seen = {}; // track keys to avoid duplicates across loop groups
    var groups = variablesData || {};
    Object.keys(groups).forEach(function (groupName) {
      var items = groups[groupName];
      if (!Array.isArray(items)) return;
      items.forEach(function (item) {
        var k = item.key || item.variable || item.name || '';
        if (!k || seen[k]) return; // skip empty or already-added keys
        seen[k] = true;
        list.push({
          key: k,
          label: item.label || item.key || '',
          group: groupName
        });
      });
    });
    // Add computed variables
    if (computedVarsData && computedVarsData.length) {
      computedVarsData.forEach(function (cv) {
        if (seen[cv.var_name]) return;
        seen[cv.var_name] = true;
        list.push({
          key: cv.var_name,
          label: cv.var_label || cv.var_name,
          group: 'COMPUTED'
        });
      });
    }
    return list;
  }

  function createVarDropdown(input) {
    // Prevent duplicate
    if (input.parentElement.classList.contains('kd-var-dropdown-wrap')) return;
    // Wrap the input
    var wrap = document.createElement('div');
    wrap.className = 'kd-var-dropdown-wrap';
    input.parentElement.insertBefore(wrap, input);
    wrap.appendChild(input);
    // Create dropdown
    var dd = document.createElement('div');
    dd.className = 'kd-var-dropdown';
    wrap.appendChild(dd);
    var highlighted = -1;

    // Extract the last token being typed (after any separator: space : / , ( ) + - * |)
    // e.g. "stone_type_label wt : stone_wt / sto" → "sto"
    function getLastToken(val) {
      var m = (val || '').match(/[\w.]+$/);
      return m ? m[0] : '';
    }

    // Replace only the last token in val with replacement
    // e.g. replaceLastToken("foo : bar / sto", "stone_wt") → "foo : bar / stone_wt"
    function replaceLastToken(val, replacement) {
      // If no partial token at end, just append
      if (!/[\w.]+$/.test(val)) return val + replacement;
      return val.replace(/[\w.]+$/, replacement);
    }

    function render(fullVal) {
      var all = getAllVariablesList();
      var token = getLastToken(fullVal).toLowerCase().replace(/\{\{|\}\}/g, '').trim();
      // Don't open if nothing to filter on
      if (!token) { dd.classList.remove('open'); dd.innerHTML = ''; return; }
      var matches = all.filter(function (v) {
        return v.key.toLowerCase().indexOf(token) !== -1 || v.label.toLowerCase().indexOf(token) !== -1;
      });
      matches = matches.slice(0, 80); // limit
      dd.innerHTML = '';
      highlighted = -1;
      if (matches.length === 0) {
        dd.classList.remove('open');
        return;
      }
      matches.forEach(function (v, idx) {
        var item = document.createElement('div');
        item.className = 'kd-var-dropdown-item';
        item.innerHTML = '<span class="kd-vd-key">' + v.key + '</span><span class="kd-vd-group">' + v.group + '</span>';
        item.dataset.idx = idx;
        item.addEventListener('mousedown', function (e) {
          e.preventDefault(); // prevent blur
          // Replace only the last typed token, keeping the prefix intact
          input.value = replaceLastToken(input.value, v.key);
          dd.classList.remove('open');
          input.focus();
        });
        dd.appendChild(item);
      });
      dd.classList.add('open');
    }

    input.addEventListener('focus', function () {
      // Only open on focus if user has already typed something
      // (type-to-search autocomplete — don't flood on blank focus)
      if (input.value.trim()) render(input.value);
    });
    input.addEventListener('input', function () {
      render(input.value);
    });
    input.addEventListener('blur', function () {
      setTimeout(function () { dd.classList.remove('open'); }, 150);
    });
    input.addEventListener('keydown', function (e) {
      var items = dd.querySelectorAll('.kd-var-dropdown-item');
      if (!items.length) return;
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        highlighted = Math.min(highlighted + 1, items.length - 1);
        items.forEach(function (it, i) { it.classList.toggle('highlighted', i === highlighted); });
        if (items[highlighted]) items[highlighted].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        highlighted = Math.max(highlighted - 1, 0);
        items.forEach(function (it, i) { it.classList.toggle('highlighted', i === highlighted); });
        if (items[highlighted]) items[highlighted].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'Enter' && highlighted >= 0) {
        e.preventDefault();
        items[highlighted].dispatchEvent(new MouseEvent('mousedown'));
      } else if (e.key === 'Escape') {
        dd.classList.remove('open');
      }
    });
  }

  // Wire dropdowns to all body/footer field inputs after rendering column config
  function wireVarDropdowns() {
    var cont = document.getElementById('kdTblColConfig');
    if (!cont) return;
    // Only wire body-field and footer-field inputs — NOT header (thead labels are static text)
    cont.querySelectorAll('.kd-col-field, .kd-col-f-field').forEach(function (inp) {
      createVarDropdown(inp);
    });
  }

  // Find a free Y position that doesn't overlap existing elements
  function findFreeY(x, y, w, h) {
    var GAP = 8; // px gap between elements
    var maxAttempts = 50;
    for (var attempt = 0; attempt < maxAttempts; attempt++) {
      var hasCollision = false;
      var pushBelow = y;
      layer.getChildren().forEach(function (node) {
        if (node === transformer) return;
        var r = node.getClientRect({ relativeTo: layer });
        // Check overlap: horizontal AND vertical
        var overlapX = x < r.x + r.width && x + w > r.x;
        var overlapY = y < r.y + r.height && y + h > r.y;
        if (overlapX && overlapY) {
          hasCollision = true;
          var bottom = r.y + r.height + GAP;
          if (bottom > pushBelow) pushBelow = bottom;
        }
      });
      if (!hasCollision) return y;
      y = pushBelow; // try below the colliding element
    }
    return y;
  }

  var pendingTableDrop = null;

  // ══════════════════════════════════════
  // SELECT / DESELECT (Multi-select aware)
  // ══════════════════════════════════════
  function deselect() {
    selectedNode = null;
    selectedNodes = [];
    groupDragOffsets = [];
    transformer.nodes([]);
    layer.batchDraw();
    document.getElementById('kdContextTools').style.display = 'none';
    document.getElementById('kdPropsBody').innerHTML =
      '<div class="kd-props-empty"><i class="fa fa-mouse-pointer" style="font-size:24px;opacity:0.3;"></i><p>Select an element on the canvas to edit</p></div>';
  }

  function selectNode(node) {
    selectedNode = node;
    selectedNodes = [node];
    groupDragOffsets = [];
    transformer.nodes([node]);
    layer.batchDraw();
    showContextBar(node);
    showProperties(node);
  }

  /**
   * Shift+click: toggle a node in/out of the current multi-selection.
   */
  function toggleMultiSelect(node) {
    var idx = selectedNodes.indexOf(node);
    if (idx >= 0) {
      // Remove from selection
      selectedNodes.splice(idx, 1);
    } else {
      // Add to selection
      selectedNodes.push(node);
    }

    if (selectedNodes.length === 0) {
      deselect();
    } else if (selectedNodes.length === 1) {
      selectNode(selectedNodes[0]);
    } else {
      selectedNode = selectedNodes[0]; // Primary for property panel
      transformer.nodes(selectedNodes);
      selectedNodes.forEach(function (n) {
        setupGroupDrag(n);
      });
      layer.batchDraw();
      showMultiSelectUI();
    }
  }

  /**
   * Rubber-band result: select all nodes hit by the marquee.
   */
  function selectMultipleNodes(nodes) {
    selectedNodes = nodes;
    selectedNode = nodes[0]; // Primary
    transformer.nodes(nodes);
    nodes.forEach(function (n) {
      setupGroupDrag(n);
    });
    layer.batchDraw();
    showMultiSelectUI();
  }

  /**
   * Show UI feedback for multi-selection: element count, limited context bar.
   */
  function showMultiSelectUI() {
    switchToTab('props');
    var count = selectedNodes.length;
    document.getElementById('kdContextTools').style.display = 'flex';
    // Hide text-specific and shape-specific controls for multi-select
    document.getElementById('kdCtxText').style.display = 'none';
    document.getElementById('kdCtxShape').style.display = 'none';
    document.getElementById('kdCtxZIndex').style.display = 'flex';

    var minX = Infinity, minY = Infinity;
    selectedNodes.forEach(function (n) {
      var x = n.x();
      var y = n.y();
      if (x < minX) minX = x;
      if (y < minY) minY = y;
    });
    if (minX === Infinity) { minX = 0; minY = 0; }
    minX = Math.round(minX);
    minY = Math.round(minY);

    var h = '<div class="kd-prop-row"><span class="kd-prop-label">Selection</span><span style="font-size:11px;color:var(--kd-accent); text-transform:uppercase; font-weight:700;">' + count + ' Elements</span></div>';
    h += '<div class="kd-prop-divider"></div>';
    h += '<div class="kd-prop-row"><span class="kd-prop-label">Bulk X</span><input class="kd-prop-input" type="number" id="kp_bulk_x" value="' + minX + '"></div>';
    h += '<div class="kd-prop-row"><span class="kd-prop-label">Bulk Y</span><input class="kd-prop-input" type="number" id="kp_bulk_y" value="' + minY + '"></div>';
    h += '<div class="kd-prop-divider"></div>';
    h += '<div style="font-size:10px;color:var(--kd-text-dim);padding:4px 6px;line-height:1.4;">';
    h += '<i class="fa fa-info-circle" style="color:var(--kd-accent);margin-right:4px;"></i>';
    h += 'Adjusting Bulk X/Y moves all selected elements relative to their top-left boundary.</div>';

    document.getElementById('kdPropsBody').innerHTML = h;

    var bx = document.getElementById('kp_bulk_x');
    if (bx) {
      bx.onchange = function () {
        var newVal = Math.round(+this.value || 0);
        var curMinX = Infinity;
        selectedNodes.forEach(function (n) {
          var x = n.x();
          if (x < curMinX) curMinX = x;
        });
        if (curMinX === Infinity) curMinX = 0;
        var dx = newVal - curMinX;
        if (dx !== 0) {
          selectedNodes.forEach(function (n) {
            n.x(n.x() + dx);
          });
          layer.batchDraw();
          recordHistory();
          showMultiSelectUI();
        }
      };
      bx.onkeydown = function (e) {
        if (e.key === 'Enter') {
          this.blur();
        }
      };
    }

    var by = document.getElementById('kp_bulk_y');
    if (by) {
      by.onchange = function () {
        var newVal = Math.round(+this.value || 0);
        var curMinY = Infinity;
        selectedNodes.forEach(function (n) {
          var y = n.y();
          if (y < curMinY) curMinY = y;
        });
        if (curMinY === Infinity) curMinY = 0;
        var dy = newVal - curMinY;
        if (dy !== 0) {
          selectedNodes.forEach(function (n) {
            n.y(n.y() + dy);
          });
          layer.batchDraw();
          autoExtendCanvas();
          recordHistory();
          showMultiSelectUI();
        }
      };
      by.onkeydown = function (e) {
        if (e.key === 'Enter') {
          this.blur();
        }
      };
    }
  }

  /**
   * Attach group-drag behavior to a node.
   * When dragging one node in a multi-selection, all other selected nodes move together.
   */
  function setupGroupDrag(node) {
    node.off('dragstart.multisel dragmove.multisel dragend.multisel');

    node.on('dragstart.multisel', function () {
      if (selectedNodes.length <= 1) return;
      // Store offsets of other selected nodes relative to this node
      var baseX = node.x(), baseY = node.y();
      groupDragOffsets = [];
      selectedNodes.forEach(function (n) {
        if (n === node) return;
        groupDragOffsets.push({
          node: n,
          dx: n.x() - baseX,
          dy: n.y() - baseY
        });
      });
    });

    node.on('dragmove.multisel', function () {
      if (groupDragOffsets.length === 0) return;
      var x = node.x(), y = node.y();
      groupDragOffsets.forEach(function (o) {
        o.node.x(x + o.dx);
        o.node.y(y + o.dy);
      });
      layer.batchDraw();
      updateMultiSelectPropFields();
    });

    node.on('dragend.multisel', function () {
      groupDragOffsets = [];
      updateMultiSelectPropFields();
      recordHistory();
    });
  }

  function showContextBar(node) {
    var bar = document.getElementById('kdContextTools');
    var txtG = document.getElementById('kdCtxText');
    var shpG = document.getElementById('kdCtxShape');
    var zG = document.getElementById('kdCtxZIndex');
    bar.style.display = 'flex';
    txtG.style.display = 'none';
    shpG.style.display = 'none';
    zG.style.display = 'flex';

    if (node.className === 'Text') {
      txtG.style.display = 'flex';
      document.getElementById('kdCtxFontSize').value = node.fontSize();
      document.getElementById('kdCtxFontColor').value = node.fill() || '#000000';
      var ff = document.getElementById('kdCtxFontFamily');
      for (var i = 0; i < ff.options.length; i++) { if (ff.options[i].value === node.fontFamily()) ff.selectedIndex = i; }
      // Sync text case dropdown
      var tcSel = document.getElementById('kdCtxTextCase');
      if (tcSel) tcSel.value = node.getAttr('textCase') || 'none';
    } else {
      shpG.style.display = 'flex';
    }
  }

  function showProperties(node) {
    switchToTab('props');
    var body = document.getElementById('kdPropsBody');
    var type = node.getClassName ? node.getClassName() : (node.className || 'Element');
    if (node.getAttr('customType') === 'data-table') type = 'Dynamic Table';
    if (node.getAttr('customType') === 'static-table') type = 'Static Table';
    if (node.getAttr('customType') === 'spacer') type = 'Spacer';
    if (node.getAttr('customType') === 'qrcode') type = 'QR Code';

    var h = '<div class="kd-prop-row"><span class="kd-prop-label">Type</span><span style="font-size:11px;color:var(--kd-info); text-transform:uppercase; font-weight:700;">' + type + '</span></div>';
    h += '<div class="kd-prop-divider"></div>';
    h += '<div class="kd-prop-row"><span class="kd-prop-label">X</span><input class="kd-prop-input" type="number" id="kp_x" value="' + Math.round(node.x()) + '"></div>';
    h += '<div class="kd-prop-row"><span class="kd-prop-label">Y</span><input class="kd-prop-input" type="number" id="kp_y" value="' + Math.round(node.y()) + '"></div>';
    h += '<div class="kd-prop-row"><span class="kd-prop-label">W</span><input class="kd-prop-input" type="number" id="kp_w" value="' + Math.round(node.width() * (node.scaleX ? node.scaleX() : 1)) + '"></div>';
    h += '<div class="kd-prop-row"><span class="kd-prop-label">H</span><input class="kd-prop-input" type="number" id="kp_h" value="' + Math.round(node.height() * (node.scaleY ? node.scaleY() : 1)) + '"></div>';

    // ── Spacer-specific info ──
    if (node.getAttr('customType') === 'spacer') {
      h += '<div class="kd-prop-divider"></div>';
      h += '<div style="font-size:10px;color:var(--kd-text-dim);padding:4px 6px;background:rgba(99,102,241,0.08);border-radius:4px;margin-top:4px;">';
      h += '<i class="fa fa-info-circle" style="color:var(--kd-accent);margin-right:4px;"></i>';
      h += 'Spacer creates fixed vertical space. It will NOT be collapsed by reflow.</div>';
    }

    if (node.className === 'Text') {
      h += '<div class="kd-prop-divider"></div>';
      h += '<div style="margin-bottom:4px;"><span class="kd-prop-label">Content</span></div>';
      h += '<textarea class="kd-prop-input" id="kp_text" rows="4" style="resize:vertical;width:100%;">' + escHtml(getRawText(node)) + '</textarea>';

      // Auto Wrap Text checkbox
      var isWrap = node.wrap() === 'word' || node.wrap() === 'char';
      h += '<div class="kd-prop-divider"></div>';
      h += '<div class="kd-prop-row" style="gap:6px;">';
      h += '  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:11px;color:var(--kd-text);">';
      h += '    <input type="checkbox" id="kp_autoWrap" ' + (isWrap ? 'checked' : '') + ' style="accent-color:var(--kd-accent);">';
      h += '    <i class="fa fa-paragraph" style="color:var(--kd-accent);"></i> Auto Wrap Text';
      h += '  </label>';
      h += '</div>';
    }

    // ── Repeat on Every Page checkbox ──
    var isRepeat = node.getAttr('repeatEveryPage') || false;
    h += '<div class="kd-prop-divider"></div>';
    h += '<div class="kd-prop-row" style="gap:6px;">';
    h += '  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:11px;color:var(--kd-text);">';
    h += '    <input type="checkbox" id="kp_repeatEveryPage" ' + (isRepeat ? 'checked' : '') + ' style="accent-color:var(--kd-accent);">';
    h += '    <i class="fa fa-copy" style="color:var(--kd-accent);"></i> Repeat on every page';
    h += '  </label>';
    h += '</div>';
    if (isRepeat) {
      h += '<div style="font-size:10px;color:var(--kd-success);padding:2px 4px;">✓ This element will appear on every printed page</div>';
    }

    // ── Condition Variable (show/hide element at print time) ──
    var condVar = node.getAttr('conditionVar') || '';
    h += '<div class="kd-prop-divider"></div>';
    h += '<div class="kd-prop-row" style="flex-direction:column;align-items:flex-start;gap:4px;">';
    h += '  <span class="kd-prop-label" title="If set, this element only prints when this backend variable is truthy (e.g. if_sales_item=1)"><i class="fa fa-eye" style="margin-right:4px;"></i>Condition Variable</span>';
    h += '  <input class="kd-prop-input" type="text" id="kp_condVar" value="' + escHtml(condVar) + '" placeholder="e.g. has_return_items  or  var1||var2  or  var1&&var2" style="width:100%;font-size:11px;">';
    h += '</div>';
    if (condVar) {
      var condDesc = '';
      if (condVar.indexOf('||') > -1) {
        var parts = condVar.split('||').map(function (s) { return '<b>' + escHtml(s.trim()) + '</b>'; });
        condDesc = '⚠ Prints when ANY of: ' + parts.join(' OR ') + ' is truthy';
      } else if (condVar.indexOf('&&') > -1) {
        var parts = condVar.split('&&').map(function (s) { return '<b>' + escHtml(s.trim()) + '</b>'; });
        condDesc = '⚠ Prints only when ALL of: ' + parts.join(' AND ') + ' are truthy';
      } else {
        condDesc = '⚠ Prints only when <b>' + escHtml(condVar) + '</b> = 1';
      }
      h += '<div style="font-size:10px;color:var(--kd-warning);padding:2px 4px;">' + condDesc + '</div>';
    }

    // ── QR Code specific: URL Path (for QR Code elements) ──
    if (node.className === 'Image' && node.getAttr('customType') === 'qrcode') {
      var qrPath = node.getAttr('qrUrlPath') || 'printbill';
      h += '<div class="kd-prop-divider"></div>';
      h += '<div class="kd-prop-row" style="flex-direction:column;align-items:flex-start;gap:4px;">';
      h += '  <span class="kd-prop-label"><i class="fa fa-qrcode" style="margin-right:4px;"></i>QR URL Path</span>';
      h += '  <input class="kd-prop-input" type="text" id="kp_qrUrlPath" value="' + escHtml(qrPath) + '" placeholder="e.g. printbill" style="width:100%;font-size:11px;">';
      h += '</div>';
      h += '<div style="font-size:9px;color:var(--kd-text-dim);padding:2px 4px;line-height:1.5;">';
      h += 'QR encodes:<br><code style="color:var(--kd-accent);word-break:break-all;">{base_url}/admin_app_api/<b>' + escHtml(qrPath) + '</b>/{bill_id}</code>';
      h += '</div>';
    }
    // ── Image Source (for regular Image elements, not QR) ──
    else if (node.className === 'Image') {
      var imgSrc = node.getAttr('imageSrc') || '';
      h += '<div class="kd-prop-divider"></div>';
      h += '<div class="kd-prop-row" style="flex-direction:column;align-items:flex-start;gap:4px;">';
      h += '  <span class="kd-prop-label"><i class="fa fa-image" style="margin-right:4px;"></i>Image Source</span>';
      h += '  <input class="kd-prop-input" type="text" id="kp_imageSrc" value="' + escHtml(imgSrc) + '" placeholder="e.g. admin/assets/img/logo.png" style="width:100%;font-size:11px;">';
      h += '</div>';
      h += '<div style="font-size:9px;color:var(--kd-text-dim);padding:2px 4px;line-height:1.4;">';
      h += 'File path relative to root folder';
      h += '</div>';
    }

    body.innerHTML = h;
    bind('kp_x', function (v) { node.x(+v); layer.batchDraw(); });
    bind('kp_y', function (v) { node.y(+v); layer.batchDraw(); autoExtendCanvas(); });
    bind('kp_w', function (v) { node.width(+v); node.scaleX(1); layer.batchDraw(); });
    bind('kp_h', function (v) { node.height(+v); node.scaleY(1); layer.batchDraw(); autoExtendCanvas(); });
    var ti = document.getElementById('kp_text');
    if (ti) ti.oninput = function () { applyDisplayText(node, this.value); layer.batchDraw(); };

    // Bind auto-wrap checkbox
    var aw = document.getElementById('kp_autoWrap');
    if (aw) {
      aw.onchange = function () {
        if (this.checked) {
          node.wrap('word');
          node.ellipsis(false);
        } else {
          node.wrap('none');
          node.ellipsis(true);
        }
        layer.batchDraw();
        showProperties(node); // refresh properties panel
        recordHistory();
      };
    }

    // Bind repeat-every-page checkbox
    var rp = document.getElementById('kp_repeatEveryPage');
    if (rp) rp.onchange = function () {
      node.setAttr('repeatEveryPage', this.checked);
      showProperties(node);
      recordHistory();
    };

    // Bind condition variable input
    var ci = document.getElementById('kp_condVar');
    if (ci) ci.onchange = function () {
      var val = this.value.trim();
      if (val) node.setAttr('conditionVar', val);
      else { node.setAttr('conditionVar', undefined); }
      showProperties(node); // refresh to show/hide warning
      recordHistory();
    };

    // Bind image source input (regular Image elements)
    var imgI = document.getElementById('kp_imageSrc');
    if (imgI) imgI.onchange = function () {
      var val = this.value.trim();
      node.setAttr('imageSrc', val);
      recordHistory();
    };

    // Bind QR URL Path input (QR Code elements)
    var qrI = document.getElementById('kp_qrUrlPath');
    if (qrI) qrI.onchange = function () {
      var val = this.value.trim() || 'printbill';
      node.setAttr('qrUrlPath', val);
      showProperties(node); // refresh to update URL preview
      recordHistory();
    };

    // Update props on drag/transform
    node.off('dragmove.props transform.props transformend.props dragend.guides dragmove.guides dragstart.reflow dragmove.reflow dragend.reflow');
    node.on('dragmove.props', function () { updatePropFields(node); });

    // Table specific transform handling: resize columns instead of scaling data
    if (node.getAttr('customType') === 'data-table' || node.getAttr('customType') === 'static-table') {
      node.on('transform', function () {
        var sx = node.scaleX();
        var sy = node.scaleY();
        var cfg = JSON.parse(node.getAttr('tableConfig') || '{}');
        if (sx !== 1) {
          cfg.columns.forEach(function (c) { c.width *= sx; });
        }
        // sy adjustment for font size could be complex, let's stick to width first
        node.scaleX(1);
        node.scaleY(1);
        node.setAttr('tableConfig', JSON.stringify(cfg));
        renderTableInGroup(node, cfg);
        updatePropFields(node);
      });
    }

    // Text node transform handling: convert scale to width/height to prevent text stretching
    if (node.className === 'Text') {
      node.on('transform.textfix', function () {
        var sx = node.scaleX();
        var sy = node.scaleY();
        // Convert scale into actual width/height
        node.width(Math.max(20, node.width() * sx));
        node.height(Math.max(10, node.height() * sy));
        // Reset scale so text renders at normal proportions
        node.scaleX(1);
        node.scaleY(1);
        updatePropFields(node);
      });
    }

    node.on('transformend.props', function () { updatePropFields(node); recordHistory(); });
    // Smart snap guides on drag
    node.on('dragmove.guides', function () { handleSnapGuides(node); });
    node.on('transform.guides', function () { updateRulerIndicators(node); });
    node.on('dragend.guides', function () { hideGuides(); });

    // ── Reflow: show indicator during drag, settle layout on end ──
    node.on('dragstart.reflow', function () { savePreDragPositions(); });
    node.on('dragmove.reflow', function () { showInsertionIndicator(node); });
    node.on('dragend.reflow', function () {
      // No auto-reflow on drag — user keeps elements where they drop them.
      // Layout compaction runs at Save time (saveDesign) to clean up gaps.
      if (insertionLine) { insertionLine.visible(false); guideLayer.batchDraw(); }
      recordHistory();
      autoExtendCanvas();
    });
    node.on('transformend.autoext', function () { autoExtendCanvas(); });

    // ── Multi-select group drag ──
    setupGroupDrag(node);
  }

  function updatePropFields(node) {
    var xi = document.getElementById('kp_x'), yi = document.getElementById('kp_y');
    var wi = document.getElementById('kp_w'), hi = document.getElementById('kp_h');
    if (xi) xi.value = Math.round(node.x());
    if (yi) yi.value = Math.round(node.y());
    if (wi) wi.value = Math.round(node.width() * (node.scaleX ? node.scaleX() : 1));
    if (hi) hi.value = Math.round(node.height() * (node.scaleY ? node.scaleY() : 1));
  }

  function updateMultiSelectPropFields() {
    var xi = document.getElementById('kp_bulk_x');
    var yi = document.getElementById('kp_bulk_y');
    if (!xi && !yi) return;

    var minX = Infinity, minY = Infinity;
    selectedNodes.forEach(function (n) {
      var x = n.x();
      var y = n.y();
      if (x < minX) minX = x;
      if (y < minY) minY = y;
    });
    if (minX === Infinity) { minX = 0; minY = 0; }

    if (xi) xi.value = Math.round(minX);
    if (yi) yi.value = Math.round(minY);
  }

  function bind(id, fn) { var el = document.getElementById(id); if (el) el.onchange = function () { fn(this.value); recordHistory(); }; }
  function escHtml(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

  // ══════════════════════════════════════
  // ADD ELEMENTS
  // ══════════════════════════════════════
  function addText(text, opts) {
    opts = opts || {};
    var raw = text || 'Type here...';
    var t = new Konva.Text({
      x: opts.x || 50, y: opts.y || 50, text: stripBraces(raw),
      fontSize: opts.fontSize || 14, fontFamily: opts.fontFamily || 'Arial',
      fill: opts.fill || '#000', width: opts.width || 200, draggable: true,
      fontStyle: opts.fontStyle || 'normal', align: opts.align || 'left',
      wrap: 'none', ellipsis: true
    });
    t.setAttr('_rawText', raw);
    layer.add(t); t.moveToBottom(); transformer.moveToTop();
    layer.batchDraw(); selectNode(t); recordHistory(); autoExtendCanvas(); return t;
  }

  function addRect(x, y) {
    var r = new Konva.Rect({
      x: x || 50, y: y || 50, width: 150, height: 80,
      fill: 'transparent', stroke: '#000', strokeWidth: 1, draggable: true
    });
    layer.add(r); r.moveToBottom(); transformer.moveToTop();
    layer.batchDraw(); selectNode(r); recordHistory(); autoExtendCanvas(); return r;
  }

  function addLine(x, y) {
    var l = new Konva.Line({
      points: [0, 0, 300, 0], stroke: '#000', strokeWidth: 1,
      x: x || 50, y: y || 100, draggable: true,
      hitStrokeWidth: 20  // thick invisible hit area so line is clickable
    });
    layer.add(l); l.moveToBottom(); transformer.moveToTop();
    layer.batchDraw(); selectNode(l); recordHistory(); autoExtendCanvas(); return l;
  }

  function addImage() {
    var inp = document.createElement('input');
    inp.type = 'file'; inp.accept = 'image/*';
    inp.onchange = function () {
      if (!this.files[0]) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        var img = new Image();
        img.onload = function () {
          var ki = new Konva.Image({
            x: 50, y: 50, image: img,
            width: Math.min(img.width, stage.width() * 0.5),
            height: Math.min(img.height, stage.height() * 0.5),
            draggable: true,
            imageSrc: e.target.result // Store data URI for save/load persistence
          });
          layer.add(ki); ki.moveToBottom(); transformer.moveToTop();
          layer.batchDraw(); selectNode(ki); recordHistory(); autoExtendCanvas();
        };
        img.src = e.target.result;
      };
      reader.readAsDataURL(this.files[0]);
    };
    inp.click();
  }

  function addSpacer(x, y) {
    var fullW = stage.width() - (margins.left + margins.right) * PX_PER_MM;
    var spacer = new Konva.Rect({
      x: x || 10, y: y || 100, width: fullW, height: 30,
      fill: 'rgba(148,163,184,0.08)', stroke: '#94a3b8', strokeWidth: 1,
      dash: [6, 4], draggable: true,
      customType: 'spacer'
    });
    layer.add(spacer); spacer.moveToBottom(); transformer.moveToTop();
    layer.batchDraw(); selectNode(spacer); recordHistory(); autoExtendCanvas();
    return spacer;
  }

  // ══════════════════════════════════════
  // QR CODE ELEMENT
  // ══════════════════════════════════════
  function addQrCode(x, y) {
    var size = 80; // 80px square on canvas
    // Create a placeholder QR image using canvas
    var c = document.createElement('canvas');
    c.width = size; c.height = size;
    var ctx = c.getContext('2d');
    // Draw a simple QR-like placeholder
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, size, size);
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.strokeRect(2, 2, size - 4, size - 4);
    // Finder patterns (3 corners)
    var fp = 18;
    [[6, 6], [size - fp - 6, 6], [6, size - fp - 6]].forEach(function (p) {
      ctx.fillStyle = '#000';
      ctx.fillRect(p[0], p[1], fp, fp);
      ctx.fillStyle = '#fff';
      ctx.fillRect(p[0] + 4, p[1] + 4, fp - 8, fp - 8);
      ctx.fillStyle = '#000';
      ctx.fillRect(p[0] + 7, p[1] + 7, fp - 14, fp - 14);
    });
    // Center text
    ctx.fillStyle = '#666';
    ctx.font = '9px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('QR CODE', size / 2, size / 2 + 3);

    var img = new Image();
    img.onload = function () {
      var ki = new Konva.Image({
        x: x || 50,
        y: y || 50,
        image: img,
        width: size,
        height: size,
        draggable: true,
        imageSrc: 'qr_code_base64', // Variable name resolved at print time
        customType: 'qrcode'
      });
      layer.add(ki); ki.moveToBottom(); transformer.moveToTop();
      layer.batchDraw(); selectNode(ki); recordHistory(); autoExtendCanvas();
    };
    img.src = c.toDataURL();
  }

  // ══════════════════════════════════════
  // DYNAMIC TABLE
  // ══════════════════════════════════════
  var editingTableNode = null; // tracks table being edited

  function showTableModal(editMode) {
    var modal = document.getElementById('kdTableModal');
    modal.dataset.mode = 'dynamic';
    document.getElementById('kdTblModalTitle').textContent = editMode ? 'Edit Dynamic Table' : 'Configure Dynamic Table';
    document.getElementById('kdTblApplyText').textContent = editMode ? 'Apply Changes' : 'Insert Table';
    // Show dynamic-only fields
    document.getElementById('kdTblType').closest('.kd-form-row').style.display = '';
    // Hide record size if it exists
    var rsWrap = document.getElementById('kdTblRecordSizeWrap');
    if (rsWrap) rsWrap.style.display = 'none';
    if (!editMode) {
      document.getElementById('kdTblType').value = 'items';
      document.getElementById('kdTblName').value = 'Sales Items';
      renderTableColConfig();
    }
    // Type change logic
    var typeSel = document.getElementById('kdTblType');
    typeSel.onchange = function () {
      var nameInput = document.getElementById('kdTblName');
      var selectedText = this.options[this.selectedIndex].text;
      if (this.value !== 'custom') nameInput.value = selectedText;
    };
    // Footer toggle logic
    var ftChk = document.getElementById('kdTblShowFooter');
    if (ftChk) {
      ftChk.onchange = function () {
        var flf = document.getElementById('kdTblFooterLabelField');
        var fcf = document.getElementById('kdTblFooterColField');
        if (flf) flf.style.display = this.checked ? '' : 'none';
        if (fcf) fcf.style.display = this.checked ? '' : 'none';
      };
      ftChk.onchange(); // apply initial state
    }
    // Sub-row toggle logic
    var subChk = document.getElementById('kdTblHasSubRows');
    if (subChk) {
      subChk.onchange = function () {
        var wrap = document.getElementById('kdSubRowConfigWrap');
        if (wrap) wrap.style.display = this.checked ? '' : 'none';
        if (this.checked) {
          var n = +document.getElementById('kdTblColCount').value || 4;
          document.getElementById('kdTblSubColCount').value = n;
          // Sync sub-row styling defaults from current body values
          var bFont = document.getElementById('kdTblBodyFont');
          var bSize = document.getElementById('kdTblBodySize');
          var bColor = document.getElementById('kdTblBodyColor');
          var sFont = document.getElementById('kdTblSubBodyFont');
          var sSize = document.getElementById('kdTblSubBodySize');
          var sColor = document.getElementById('kdTblSubBodyColor');
          if (bFont && sFont) sFont.value = bFont.value;
          if (bSize && sSize) sSize.value = bSize.value;
          if (bColor && sColor) sColor.value = bColor.value;
          renderSubRowColConfig();
        }
      };
      if (!editMode) {
        subChk.checked = false;
        var wrap = document.getElementById('kdSubRowConfigWrap');
        if (wrap) wrap.style.display = 'none';
      }
    }
    // Sub-column count change
    var subColCnt = document.getElementById('kdTblSubColCount');
    if (subColCnt) {
      subColCnt.onchange = function () { renderSubRowColConfig(); };
    }
    modal.style.display = 'flex';
  }

  function renderTableColConfig(colData) {
    var n = +document.getElementById('kdTblColCount').value || 4;
    // If no explicit colData passed, read existing values from DOM inputs
    if (!colData) {
      var existingRows = document.querySelectorAll('#kdTblColConfig .kd-tbl-col-row');
      if (existingRows.length > 0) {
        colData = [];
        existingRows.forEach(function (row) {
          colData.push({
            header: row.querySelector('.kd-col-header').value,
            field: wrapBraces(row.querySelector('.kd-col-field').value),
            footerField: row.querySelector('.kd-col-f-field') ? wrapBraces(row.querySelector('.kd-col-f-field').value) : '',
            width: +row.querySelector('.kd-col-width').value || 100,
            headerAlign: row.querySelector('.kd-col-h-align') ? row.querySelector('.kd-col-h-align').value : 'center',
            align: row.querySelector('.kd-col-align').value,
            hidden: row.querySelector('.kd-col-hidden') ? row.querySelector('.kd-col-hidden').checked : false,
            headerColspan: row.querySelector('.kd-col-hcs') ? (+row.querySelector('.kd-col-hcs').value || 1) : 1,
            bodyColspan: row.querySelector('.kd-col-bcs') ? (+row.querySelector('.kd-col-bcs').value || 1) : 1,
            footerColspan: row.querySelector('.kd-col-fcs') ? (+row.querySelector('.kd-col-fcs').value || 1) : 1
          });
        });
      }
    }
    var cont = document.getElementById('kdTblColConfig');
    var h = '';
    for (var i = 0; i < n; i++) {
      var cd = (colData && colData[i]) ? colData[i] : null;
      var hdr = cd ? cd.header : 'Col ' + (i + 1);
      var fld = cd ? cd.field : '';
      // Footer field: auto-generate hint, but only pre-fill value for brand-new columns
      var autoFooter = cd ? getFooterValue({ field: cd.field, footerField: '' }, null, i) : '';
      var ffld;
      if (!cd) {
        ffld = '';  // no column data at all
      } else if (cd._footerFieldSet) {
        ffld = cd.footerField || '';  // user explicitly set (even to empty)
      } else if (cd.footerField) {
        ffld = cd.footerField;  // has a non-empty saved value
      } else {
        ffld = autoFooter;  // never been saved yet, pre-fill with auto
      }
      var wid = cd ? cd.width : 100;
      var aln = cd ? cd.align : 'left';
      var haln = cd ? cd.headerAlign : 'center';
      var isHidden = cd ? !!cd.hidden : false;
      var hcs = cd ? (cd.headerColspan || 1) : 1;
      var bcs = cd ? (cd.bodyColspan || 1) : 1;
      var fcs = cd ? (cd.footerColspan || 1) : 1;
      h += '<div class="kd-tbl-col-row" draggable="true" data-col-idx="' + i + '">';
      // Drag handle
      h += '<span class="kd-col-drag-handle" title="Drag to reorder" style="cursor:grab;color:var(--kd-text-muted);font-size:14px;width:15px;user-select:none;display:flex;align-items:center;">⋮⋮</span>';
      h += '<span style="color:var(--kd-text-muted);font-size:10px;width:15px;" class="kd-col-index">#' + (i + 1) + '</span>';
      // Hide toggle
      h += '<label title="Hide this column" style="display:flex;align-items:center;cursor:pointer;margin-right:4px;">';
      h += '<input type="checkbox" class="kd-col-hidden" ' + (isHidden ? 'checked' : '') + ' style="margin:0;">';
      h += '<i class="fa ' + (isHidden ? 'fa-eye-slash' : 'fa-eye') + '" style="margin-left:3px;font-size:11px;color:' + (isHidden ? 'var(--kd-danger)' : 'var(--kd-text-muted)') + ';" data-hide-icon></i>';
      h += '</label>';
      h += '<input class="kd-col-header" placeholder="Header" value="' + escHtml(hdr) + '" title="Header Label">';
      h += '<div style="display:flex; flex-direction:column; gap:2px; flex:1;">';
      h += '<input class="kd-col-field" placeholder="Body field" value="' + escHtml(stripBraces(fld)) + '" title="Body Data Field">';
      h += '<input class="kd-col-f-field" placeholder="' + escHtml(stripBraces(autoFooter) || 'Foot field') + '" value="' + escHtml(stripBraces(ffld)) + '" title="Footer Data Field" style="font-size:9px; border-color:var(--kd-accent);">';
      h += '</div>';
      h += '<input class="kd-col-width" type="number" value="' + wid + '" min="20" max="500" title="Width (px)">';
      h += '<div style="display:flex; flex-direction:column; gap:2px;">';
      h += '<span style="font-size:8px; color:var(--kd-accent); text-align:center;">HDR</span>';
      h += '<select class="kd-col-h-align" title="Header Alignment">';
      h += '<option value="left"' + (haln === 'left' ? ' selected' : '') + '>L</option>';
      h += '<option value="center"' + (haln === 'center' ? ' selected' : '') + '>C</option>';
      h += '<option value="right"' + (haln === 'right' ? ' selected' : '') + '>R</option>';
      h += '</select></div>';
      h += '<div style="display:flex; flex-direction:column; gap:2px;">';
      h += '<span style="font-size:8px; color:var(--kd-accent); text-align:center;">DATA</span>';
      h += '<select class="kd-col-align" title="Data Alignment">';
      h += '<option value="left"' + (aln === 'left' ? ' selected' : '') + '>L</option>';
      h += '<option value="center"' + (aln === 'center' ? ' selected' : '') + '>C</option>';
      h += '<option value="right"' + (aln === 'right' ? ' selected' : '') + '>R</option>';
      h += '</select></div>';
      // Colspan row (wraps to next line via CSS flex-wrap + width:100%)
      h+='<div class="kd-col-cs-row">';
      h+='<span class="kd-cs-label">Colspan:</span>';
      h+='<div class="kd-cs-pair"><input class="kd-col-hcs" type="number" value="'+hcs+'" min="1" max="20" title="Header Colspan"><span>Hdr</span></div>';
      h+='<div class="kd-cs-pair"><input class="kd-col-bcs" type="number" value="'+bcs+'" min="1" max="20" title="Body Colspan"><span>Body</span></div>';
      h+='<div class="kd-cs-pair"><input class="kd-col-fcs" type="number" value="'+fcs+'" min="1" max="20" title="Footer Colspan"><span>Ftr</span></div>';
      h+='</div>';
      h+='</div>';
    }
    cont.innerHTML = h;
    // Wire hide toggle icon sync
    cont.querySelectorAll('.kd-col-hidden').forEach(function (chk) {
      chk.onchange = function () {
        var icon = this.parentElement.querySelector('[data-hide-icon]');
        if (icon) {
          icon.className = 'fa ' + (this.checked ? 'fa-eye-slash' : 'fa-eye');
          icon.style.color = this.checked ? 'var(--kd-danger)' : 'var(--kd-text-muted)';
        }
        // Dim the row visually
        this.closest('.kd-tbl-col-row').style.opacity = this.checked ? '0.45' : '1';
      };
      // Apply initial state
      if (chk.checked) chk.closest('.kd-tbl-col-row').style.opacity = '0.45';
    });
    // Setup drag-and-drop on column rows
    _setupColDragDrop();
    // Wire variable dropdowns on body/footer field inputs
    wireVarDropdowns();
  }

  // ═══ COLUMN DRAG-AND-DROP REORDER ═══
  var _dragColSrc = null;
  function _setupColDragDrop() {
    var cont = document.getElementById('kdTblColConfig');
    var rows = cont.querySelectorAll('.kd-tbl-col-row');
    rows.forEach(function (row) {
      row.addEventListener('dragstart', function (e) {
        _dragColSrc = this;
        this.style.opacity = '0.4';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', this.getAttribute('data-col-idx'));
      });
      row.addEventListener('dragend', function () {
        this.style.opacity = '1';
        // Remove all drag-over highlights
        cont.querySelectorAll('.kd-tbl-col-row').forEach(function (r) { r.classList.remove('kd-col-drag-over'); });
      });
      row.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        this.classList.add('kd-col-drag-over');
      });
      row.addEventListener('dragleave', function () {
        this.classList.remove('kd-col-drag-over');
      });
      row.addEventListener('drop', function (e) {
        e.preventDefault();
        this.classList.remove('kd-col-drag-over');
        if (_dragColSrc === this) return;
        // Read current column data from all rows before swapping
        var allRows = cont.querySelectorAll('.kd-tbl-col-row');
        var colArr = [];
        allRows.forEach(function (r) {
          colArr.push({
            header: r.querySelector('.kd-col-header').value,
            field: wrapBraces(r.querySelector('.kd-col-field').value),
            footerField: r.querySelector('.kd-col-f-field') ? wrapBraces(r.querySelector('.kd-col-f-field').value) : '',
            width: +r.querySelector('.kd-col-width').value || 100,
            headerAlign: r.querySelector('.kd-col-h-align') ? r.querySelector('.kd-col-h-align').value : 'center',
            align: r.querySelector('.kd-col-align').value,
            headerColspan: r.querySelector('.kd-col-hcs') ? (+r.querySelector('.kd-col-hcs').value || 1) : 1,
            bodyColspan: r.querySelector('.kd-col-bcs') ? (+r.querySelector('.kd-col-bcs').value || 1) : 1,
            footerColspan: r.querySelector('.kd-col-fcs') ? (+r.querySelector('.kd-col-fcs').value || 1) : 1
          });
        });
        var fromIdx = +_dragColSrc.getAttribute('data-col-idx');
        var toIdx = +this.getAttribute('data-col-idx');
        // Move the item
        var moved = colArr.splice(fromIdx, 1)[0];
        colArr.splice(toIdx, 0, moved);
        // Update col count and re-render
        document.getElementById('kdTblColCount').value = colArr.length;
        renderTableColConfig(colArr);
      });
    });
  }

  // Open modal pre-filled with existing table config
  function editTable(groupNode) {
    editingTableNode = groupNode;
    var cfgStr = groupNode.getAttr('tableConfig');
    if (!cfgStr) { toast('No config found on this table', 'error'); return; }
    var cfg;
    try { cfg = JSON.parse(cfgStr); } catch (e) { toast('Invalid table config', 'error'); return; }
    // Fill modal fields
    document.getElementById('kdTblType').value = cfg.type || 'items';
    document.getElementById('kdTblName').value = cfg.name || 'Sales Items';
    document.getElementById('kdTblColCount').value = cfg.columns ? cfg.columns.length : 4;
    document.getElementById('kdTblShowHeader').checked = cfg.showHeader !== false;
    document.getElementById('kdTblHeaderBg').value = cfg.headerBg || '#ffffff';
    document.getElementById('kdTblHeaderColor').value = cfg.headerColor || '#000000';
    document.getElementById('kdTblBorderWidth').value = cfg.borderWidth || 1;
    document.getElementById('kdTblShowFooter').checked = !!cfg.showFooter;
    document.getElementById('kdTblFooterLabel').value = cfg.footerLabel || 'Total';
    document.getElementById('kdTblFooterColSpan').value = cfg.footerColSpan || 2;
    document.getElementById('kdTblShowOuterBorder').checked = cfg.showOuterBorder !== false;
    document.getElementById('kdTblShowSideLines').checked = cfg.showSideLines !== false;
    document.getElementById('kdTblShowVLines').checked = cfg.showVLines !== false;
    document.getElementById('kdTblShowHLinesHeader').checked = cfg.showHLinesHeader !== false;
    document.getElementById('kdTblShowHLinesBody').checked = cfg.showHLinesBody !== false;
    document.getElementById('kdTblShowHLinesFooter').checked = cfg.showHLinesFooter !== false;

    document.getElementById('kdTblHeaderFont').value = cfg.headerFont || 'Arial';
    document.getElementById('kdTblHeaderSize').value = cfg.headerSize || 11;
    document.getElementById('kdTblBodyFont').value = cfg.bodyFont || 'Arial';
    document.getElementById('kdTblBodySize').value = cfg.bodySize || 11;
    document.getElementById('kdTblBodyColor').value = cfg.bodyColor || '#000000';
    document.getElementById('kdTblFooterFont').value = cfg.footerFont || 'Arial';
    document.getElementById('kdTblFooterSize').value = cfg.footerSize || 11;
    document.getElementById('kdTblCellPadding').value = cfg.cellPadding || 2;
    document.getElementById('kdTblTableMargin').value = cfg.tableMargin || 0;

    renderTableColConfig(cfg.columns);
    // Load sub-row config
    var subChk = document.getElementById('kdTblHasSubRows');
    if (subChk) {
      subChk.checked = !!cfg.hasSubRows;
      var wrap = document.getElementById('kdSubRowConfigWrap');
      if (wrap) wrap.style.display = cfg.hasSubRows ? '' : 'none';
    }
    if (cfg.hasSubRows && cfg.subRowColumns) {
      document.getElementById('kdTblSubColCount').value = cfg.subRowColumns.length;
      document.getElementById('kdTblSubBodyFont').value = cfg.subBodyFont || cfg.bodyFont || 'Arial';
      document.getElementById('kdTblSubBodySize').value = cfg.subBodySize || cfg.bodySize || 11;
      document.getElementById('kdTblSubBodyColor').value = cfg.subBodyColor || cfg.bodyColor || '#000000';
      renderSubRowColConfig(cfg.subRowColumns);
    }
    showTableModal(true);
  }

  function readTableFormConfig() {
    var rows = document.querySelectorAll('#kdTblColConfig .kd-tbl-col-row');
    var cols = [];
    rows.forEach(function (row) {
      var h = row.querySelector('.kd-col-header');
      var f = row.querySelector('.kd-col-field');
      var ff = row.querySelector('.kd-col-f-field');
      var w = row.querySelector('.kd-col-width');
      var ha = row.querySelector('.kd-col-h-align');
      var a = row.querySelector('.kd-col-align');
      if (h && f && w && a && ha) {
        var hid = row.querySelector('.kd-col-hidden');
        var hcsEl = row.querySelector('.kd-col-hcs');
        var bcsEl = row.querySelector('.kd-col-bcs');
        var fcsEl = row.querySelector('.kd-col-fcs');
        cols.push({
          header: h.value,
          field: wrapBraces(f.value),
          footerField: ff ? wrapBraces(ff.value) : '',
          _footerFieldSet: true,  // mark that user explicitly touched this
          width: +w.value || 100,
          headerAlign: ha.value,
          align: a.value,
          hidden: hid ? hid.checked : false,
          headerColspan: hcsEl ? (+hcsEl.value || 1) : 1,
          bodyColspan: bcsEl ? (+bcsEl.value || 1) : 1,
          footerColspan: fcsEl ? (+fcsEl.value || 1) : 1
        });
      }
    });

    var getVal = function (id, def) { var el = document.getElementById(id); return el ? el.value : def; };
    var getNum = function (id, def) { var el = document.getElementById(id); return el ? +el.value : def; };
    var getChk = function (id, def) { var el = document.getElementById(id); return el ? el.checked : def; };

    return {
      type: getVal('kdTblType', 'items'),
      name: getVal('kdTblName', 'Sales Items'),
      columns: cols,
      fontSize: getNum('kdTblFontSize', 11),
      showHeader: getChk('kdTblShowHeader', true),
      headerBg: getVal('kdTblHeaderBg', '#ffffff'),
      headerColor: getVal('kdTblHeaderColor', '#000000'),
      borderWidth: getNum('kdTblBorderWidth', 1),
      showFooter: getChk('kdTblShowFooter', false),
      footerLabel: getVal('kdTblFooterLabel', 'Total'),
      footerColSpan: getNum('kdTblFooterColSpan', 2),
      showOuterBorder: getChk('kdTblShowOuterBorder', true),
      showSideLines: getChk('kdTblShowSideLines', true),
      showVLines: getChk('kdTblShowVLines', true),
      showHLinesHeader: getChk('kdTblShowHLinesHeader', true),
      showHLinesBody: getChk('kdTblShowHLinesBody', true),
      showHLinesFooter: getChk('kdTblShowHLinesFooter', true),

      headerFont: getVal('kdTblHeaderFont', 'Arial'),
      headerSize: getNum('kdTblHeaderSize', 11),
      bodyFont: getVal('kdTblBodyFont', 'Arial'),
      bodySize: getNum('kdTblBodySize', 11),
      bodyColor: getVal('kdTblBodyColor', '#000000'),
      footerFont: getVal('kdTblFooterFont', 'Arial'),
      footerSize: getNum('kdTblFooterSize', 11),
      cellPadding: getNum('kdTblCellPadding', 2),
      tableMargin: getNum('kdTblTableMargin', 0),
      hasSubRows: getChk('kdTblHasSubRows', false),
      subRowColumns: _readSubRowCols(),
      subBodyFont: getVal('kdTblSubBodyFont', getVal('kdTblBodyFont', 'Arial')),
      subBodySize: getNum('kdTblSubBodySize', getNum('kdTblBodySize', 11)),
      subBodyColor: getVal('kdTblSubBodyColor', getVal('kdTblBodyColor', '#000000'))
    };
  }

  function _readSubRowCols() {
    var chk = document.getElementById('kdTblHasSubRows');
    if (!chk || !chk.checked) return [];
    var rows = document.querySelectorAll('#kdTblSubColConfig .kd-tbl-col-row');
    var cols = [];
    rows.forEach(function (row) {
      var f = row.querySelector('.kd-col-field');
      var a = row.querySelector('.kd-col-align');
      if (f && a) {
        cols.push({
          field: wrapBraces(f.value),
          align: a.value
        });
      }
    });
    return cols;
  }

  function renderSubRowColConfig(colData) {
    var n = +document.getElementById('kdTblSubColCount').value || 4;
    var cont = document.getElementById('kdTblSubColConfig');
    var h = '';
    for (var i = 0; i < n; i++) {
      var cd = (colData && colData[i]) ? colData[i] : null;
      var fld = cd ? cd.field : '';
      var aln = cd ? cd.align : 'left';
      h += '<div class="kd-tbl-col-row" style="gap:4px;">';
      h += '<span style="color:var(--kd-text-muted);font-size:10px;width:25px;">#' + (i + 1) + '</span>';
      h += '<input class="kd-col-field" placeholder="Stone field" value="' + escHtml(stripBraces(fld)) + '" title="Sub-row field" style="flex:2;">';
      h += '<select class="kd-col-align" title="Alignment">';
      h += '<option value="left"' + (aln === 'left' ? ' selected' : '') + '>L</option>';
      h += '<option value="center"' + (aln === 'center' ? ' selected' : '') + '>C</option>';
      h += '<option value="right"' + (aln === 'right' ? ' selected' : '') + '>R</option>';
      h += '</select>';
      h += '</div>';
    }
    cont.innerHTML = h;
    // Wire variable dropdowns on sub-row field inputs
    cont.querySelectorAll('.kd-col-field').forEach(function (inp) {
      createVarDropdown(inp);
    });
  }

  function insertTable() {
    try {
      var tCfg = readTableFormConfig();
      var modal = document.getElementById('kdTableModal');
      var isStatic = modal.dataset.mode === 'static';

      if (isStatic) {
        var rsEl = document.getElementById('kdTblRecordSize');
        tCfg.recordSize = rsEl ? (+rsEl.value || 3) : 3;
      }

      // Edit mode — update existing table in-place
      if (editingTableNode) {
        var nodeType = editingTableNode.getAttr('customType');
        if (nodeType === 'static-table') {
          tCfg.recordSize = document.getElementById('kdTblRecordSize') ? (+document.getElementById('kdTblRecordSize').value || tCfg.recordSize || 3) : (tCfg.recordSize || 3);
          renderStaticTableInGroup(editingTableNode, tCfg);
        } else {
          renderTableInGroup(editingTableNode, tCfg);
        }
        editingTableNode = null;
        // reflowLayout(); // Removed — it was destroying manual positioning and multi-column layouts
        layer.batchDraw();
        recordHistory();
      } else if (isStatic) {
        var pos = pendingTableDrop || { x: 30, y: 30 };
        pendingTableDrop = null;
        drawStaticTable(tCfg, pos.x, pos.y);
      } else {
        var pos2 = pendingTableDrop || { x: 30, y: 30 };
        pendingTableDrop = null;
        drawTable(tCfg, pos2.x, pos2.y);
      }
    } catch (e) {
      console.error('Table creation error:', e);
      toast('Error creating table: ' + e.message, 'error');
    } finally {
      document.getElementById('kdTableModal').style.display = 'none';
      pendingTableDrop = null;
      editingTableNode = null;
    }
  }

  // ══════════════════════════════════════
  // SAMPLE DATA GENERATOR
  // ══════════════════════════════════════
  var SAMPLE_ITEMS = [
    { sno: '1', desc: 'Gold Ring 22K BIS', hsn: '7113', pcs: '1', gwt: '8.520', nwt: '8.200', rate: '6,850.00', amt: '56,170.00', making: '1,200.00', discount: '0.00', tax: '2,808.50', cgst: '1,404.25', sgst: '1,404.25' },
    { sno: '2', desc: 'Gold Chain 22K', hsn: '7113', pcs: '1', gwt: '15.340', nwt: '15.100', rate: '6,850.00', amt: '1,03,435.00', making: '2,500.00', discount: '500.00', tax: '5,171.75', cgst: '2,585.88', sgst: '2,585.88' },
    { sno: '3', desc: 'Diamond Pendant 18K', hsn: '7113', pcs: '1', gwt: '4.250', nwt: '3.800', rate: '5,200.00', amt: '19,760.00', making: '3,800.00', discount: '0.00', tax: '988.00', cgst: '494.00', sgst: '494.00' }
  ];

  function getSampleData(cols, rowCount) {
    var rows = [];
    for (var r = 0; r < rowCount; r++) {
      var item = SAMPLE_ITEMS[r % SAMPLE_ITEMS.length];
      var row = [];
      cols.forEach(function (c) {
        var hdr = (c.header || '').toLowerCase().replace(/[^a-z0-9]/g, '');
        var val = matchSampleField(hdr, item, r);
        row.push(val);
      });
      rows.push(row);
    }
    return rows;
  }

  function matchSampleField(hdr, item, idx) {
    // Match header text to sample field
    if (/^(sno|s\.?no|sl|#|serial|no)$/.test(hdr)) return item.sno;
    if (/desc|name|item|product|particular/.test(hdr)) return item.desc;
    if (/hsn|hsncode|saccode/.test(hdr)) return item.hsn;
    if (/^(pcs|qty|quantity|pieces|nos)$/.test(hdr)) return item.pcs;
    if (/^(gwt|grosswt|grossweight|grswt)$/.test(hdr)) return item.gwt;
    if (/^(nwt|netwt|netweight|netwt)$/.test(hdr)) return item.nwt;
    if (/^(rate|price|ratepergm|ratepergram)$/.test(hdr)) return item.rate;
    if (/^(amt|amount|total|value|totalamt|netamt)$/.test(hdr)) return item.amt;
    if (/making|labour|mc|makingcharge/.test(hdr)) return item.making;
    if (/disc|discount/.test(hdr)) return item.discount;
    if (/^(tax|gst|taxamt)$/.test(hdr)) return item.tax;
    if (/cgst/.test(hdr)) return item.cgst;
    if (/sgst/.test(hdr)) return item.sgst;
    if (/weight|wt/.test(hdr)) return item.gwt;
    // Fallback
    return '---';
  }

  // (Removed duplicate getFooterValue — canonical version is below at NUMERIC_FOOTER_FIELDS)

  function drawTable(cfg, startX, startY) {
    var group = new Konva.Group({ x: startX || 30, y: startY || 30, draggable: true });
    group.setAttr('customType', 'data-table');
    group.on('dblclick', function () { editTable(group); });
    renderTableInGroup(group, cfg);
    layer.add(group); group.moveToBottom(); transformer.moveToTop();
    layer.batchDraw(); selectNode(group); recordHistory();
  }

  // Generate sample data rows for table preview in designer
  function getSampleData(cols, numRows) {
    var data = [];
    for (var r = 0; r < numRows; r++) {
      var row = [];
      for (var c = 0; c < cols.length; c++) {
        var col = cols[c];
        var field = col.field || '';
        // Use the placeholder name stripped of {{ }} as sample text
        var sampleText = field.replace(/\{\{|\}\}/g, '');
        if (!sampleText) sampleText = col.header || 'col' + c;
        // Add row number for clarity
        row.push(sampleText);
      }
      data.push(row);
    }
    return data;
  }

  // Get footer value for a column in the table preview
  // Only auto-generate totals for numeric fields; non-numeric = empty
  var NUMERIC_FOOTER_FIELDS = ['amount', 'qty', 'pcs', 'piece', 'gross_wt', 'net_wt', 'rate',
    'mc', 'va_content', 'item_cost', 'item_taxable', 'item_total_tax',
    'total_cgst', 'total_sgst', 'total_igst', 'rate_per_grm',
    'old_metal_amount', 'return_amount', 'taxable_amount',
    'cgst_amt', 'sgst_amt', 'igst_amt'];
  function getFooterValue(col, sampleData, colIdx) {
    // If user entered a custom footer field, use it
    if (col.footerField) return col.footerField;
    // Otherwise auto-generate ONLY for numeric fields
    var field = col.field || '';
    var name = field.replace(/\{\{|\}\}/g, '').trim();
    if (!name) return '';
    // Check if this field is numeric-summable
    if (NUMERIC_FOOTER_FIELDS.indexOf(name) === -1) return '';
    return '{{total_' + name + '}}';
  }

  function renderTableInGroup(group, cfg) {
    group.destroyChildren();
    // Default showHeader to true if not explicitly set
    if (typeof cfg.showHeader === 'undefined') cfg.showHeader = true;
    // Normalize footer key: API uses 'footer', designer uses 'showFooter'
    if (typeof cfg.showFooter === 'undefined' && typeof cfg.footer !== 'undefined') cfg.showFooter = cfg.footer;
    // Default header colors to white bg + black text
    if (!cfg.headerBg) cfg.headerBg = '#ffffff';
    if (!cfg.headerColor) cfg.headerColor = '#000000';
    var allCols = cfg.columns || [], fs = cfg.fontSize || 11, pad = cfg.cellPadding || 2;
    // Filter out hidden columns for visual rendering (they remain in saved config)
    var cols = allCols.filter(function (c) { return !c.hidden; });
    var rowH = fs + pad * 2 + 4, totalW = 0;
    cols.forEach(function (c) { totalW += (c.width || 100); });
    var sampleRows = 1;
    var sampleData = getSampleData(cols, sampleRows);
    var subRowH = 0;
    if (cfg.hasSubRows && cfg.subRowColumns && cfg.subRowColumns.length > 0) {
      var subFs = cfg.subBodySize || (cfg.bodySize || fs);
      subRowH = subFs + pad * 2 + 4;
    }
    var totalH = (cfg.showHeader ? rowH : 0) + sampleRows * (rowH + subRowH) + (cfg.showFooter ? rowH : 0);

    group.setAttr('tableConfig', JSON.stringify(cfg));
    // Store computed dimensions on the Group so they persist in JSON for PHP reflow
    group.width(totalW);
    group.height(totalH);
    var bw = cfg.borderWidth || 1;

    // Outer Border (Horizontal lines at very top and very bottom)
    if (cfg.showOuterBorder !== false) {
      group.add(new Konva.Line({ points: [0, 0, totalW, 0], stroke: '#000', strokeWidth: bw }));
      group.add(new Konva.Line({ points: [0, totalH, totalW, totalH], stroke: '#000', strokeWidth: bw }));
    }
    // Side Borders (Vertical lines at very left and very right)
    if (cfg.showSideLines !== false) {
      group.add(new Konva.Line({ points: [0, 0, 0, totalH], stroke: '#000', strokeWidth: bw }));
      group.add(new Konva.Line({ points: [totalW, 0, totalW, totalH], stroke: '#000', strokeWidth: bw }));
    }

    // Fill Rect (for white background)
    var bgRect = new Konva.Rect({ x: 0, y: 0, width: totalW, height: totalH, fill: '#fff', listening: false });
    group.add(bgRect);
    bgRect.moveToBottom();

    var cy = 0;

    // Header
    if (cfg.showHeader) {
      // Background
      group.add(new Konva.Rect({ x: 0, y: 0, width: totalW, height: rowH, fill: cfg.headerBg || '#fff' }));

      // Header Top Line
      if (cfg.showHLinesHeader !== false) {
        group.add(new Konva.Line({ points: [0, 0, totalW, 0], stroke: '#000', strokeWidth: bw }));
      }

      var cx = 0;
      var hdrSkip = 0;
      cols.forEach(function (c, i) {
        if (hdrSkip > 0) { cx += c.width; hdrSkip--; return; }
        var hcs = Math.min(c.headerColspan || 1, cols.length - i);
        var spanW = c.width;
        for (var si = 1; si < hcs; si++) spanW += (cols[i + si] ? cols[i + si].width : 0);
        if (hcs > 1) hdrSkip = hcs - 1;
        group.add(new Konva.Text({
          x: cx + pad, y: pad, text: c.header,
          fontSize: cfg.headerSize || fs, fontFamily: cfg.headerFont || 'Arial',
          fontStyle: 'bold', fill: cfg.headerColor || '#000',
          width: spanW - pad * 2, align: c.headerAlign || 'center'
        }));
        cx += spanW;
        // Vertical line
        if (i + hcs < cols.length && cfg.showVLines !== false) {
          group.add(new Konva.Line({ points: [cx, 0, cx, totalH], stroke: '#000', strokeWidth: bw }));
        }
      });
      // Header Bottom line
      if (cfg.showHLinesHeader !== false) {
        group.add(new Konva.Line({ points: [0, rowH, totalW, rowH], stroke: '#000', strokeWidth: bw }));
      }
      cy = rowH;
    }
    // Body rows — use sample data
    for (var r = 0; r < sampleRows; r++) {
      var cx2 = 0;
      var bodySkip = 0;
      cols.forEach(function (c, ci) {
        if (bodySkip > 0) { cx2 += c.width; bodySkip--; return; }
        var bcs = Math.min(c.bodyColspan || 1, cols.length - ci);
        var spanW = c.width;
        for (var si = 1; si < bcs; si++) spanW += (cols[ci + si] ? cols[ci + si].width : 0);
        if (bcs > 1) bodySkip = bcs - 1;
        var cellText = sampleData[r] && sampleData[r][ci] !== undefined ? sampleData[r][ci] : (c.field || '...');
        group.add(new Konva.Text({
          x: cx2 + pad, y: cy + pad, text: stripBraces(String(cellText)),
          fontSize: cfg.bodySize || fs, fontFamily: cfg.bodyFont || 'Arial',
          fill: cfg.bodyColor || '#000', width: spanW - pad * 2, align: c.align || 'left',
          wrap: 'none', ellipsis: true
        }));
        cx2 += spanW;
      });
      cy += rowH;
      // Row line between main and sub-row
      if (cfg.showHLinesBody !== false) {
        if (r < sampleRows - 1 || (!cfg.showFooter)) {
          group.add(new Konva.Line({ points: [0, cy, totalW, cy], stroke: r < sampleRows - 1 ? '#ccc' : '#000', strokeWidth: r < sampleRows - 1 ? 1 : bw }));
        }
      }
      // Sub-row preview (when hasSubRows is enabled)
      if (cfg.hasSubRows && cfg.subRowColumns && cfg.subRowColumns.length > 0) {
        var subFs = cfg.subBodySize || (cfg.bodySize || fs);
        var subColor = cfg.subBodyColor || (cfg.bodyColor || '#000');
        var subRowH = subFs + pad * 2 + 4;
        // Dashed separator for sub-row
        group.add(new Konva.Line({ points: [0, cy, totalW, cy], stroke: '#aaa', strokeWidth: 0.5, dash: [3, 3] }));
        var cx3 = 0;
        cols.forEach(function (c, ci) {
          var subCol = cfg.subRowColumns[ci] || {};
          var subField = subCol.field || '';
          var subText = subField ? stripBraces(subField) : '';
          group.add(new Konva.Text({
            x: cx3 + pad, y: cy + pad, text: subText,
            fontSize: subFs, fontFamily: cfg.subBodyFont || cfg.bodyFont || 'Arial',
            fill: subColor, width: c.width - pad * 2, align: subCol.align || 'left',
            wrap: 'none', ellipsis: true
          }));
          cx3 += c.width;
        });
        cy += subRowH;
      }
    }
    // Footer
    if (cfg.showFooter) {
      // Footer Top line
      if (cfg.showHLinesFooter !== false) {
        group.add(new Konva.Line({ points: [0, cy, totalW, cy], stroke: '#000', strokeWidth: bw }));
      }

      group.add(new Konva.Rect({ x: 0, y: cy, width: totalW, height: rowH, fill: '#f5f5f5' }));

      // Footer Bottom line
      if (cfg.showHLinesFooter !== false) {
        group.add(new Konva.Line({ points: [0, cy + rowH, totalW, cy + rowH], stroke: '#000', strokeWidth: bw }));
      }

      // Footer cols — use per-column footerColspan to merge cells
      var fx = 0;
      var ftrSkip = 0;
      for (var fj = 0; fj < cols.length; fj++) {
        if (ftrSkip > 0) { fx += cols[fj].width; ftrSkip--; continue; }
        var c = cols[fj];
        var fcs = Math.min(c.footerColspan || 1, cols.length - fj);
        var fSpanW = c.width;
        for (var fsi = 1; fsi < fcs; fsi++) fSpanW += (cols[fj + fsi] ? cols[fj + fsi].width : 0);
        if (fcs > 1) ftrSkip = fcs - 1;
        // Determine footer cell value
        var ftVal = '';
        if (c._footerFieldSet) {
          ftVal = c.footerField || '';
        } else if (c.footerField) {
          ftVal = c.footerField;
        } else {
          ftVal = getFooterValue(c, sampleData, fj);
        }
        // If footerField is empty and this looks like it was the old label span area,
        // check if cfg.footerLabel is set and this is column 0
        if (!ftVal && fj === 0 && fcs > 1 && cfg.footerLabel) {
          ftVal = cfg.footerLabel;
        }
        group.add(new Konva.Text({
          x: fx + pad, y: cy + pad, text: stripBraces(ftVal),
          fontSize: cfg.footerSize || fs, fontFamily: cfg.footerFont || 'Arial',
          fontStyle: 'bold', fill: '#000', width: fSpanW - pad * 2, align: c.align || 'right',
          wrap: 'none', ellipsis: true
        }));
        fx += fSpanW;
      }
      // Vertical lines in footer if enabled
      if (cfg.showVLines !== false) {
        var fcx = 0;
        cols.forEach(function (c, i) {
          fcx += c.width;
          if (i < cols.length - 1) {
            group.add(new Konva.Line({ points: [fcx, cy, fcx, cy + rowH], stroke: '#000', strokeWidth: bw }));
          }
        });
      }
    }
  }
  // ══════════════════════════════════════
  // STATIC TABLE
  // ══════════════════════════════════════

  function showStaticTableModal() {
    var modal = document.getElementById('kdTableModal');
    document.getElementById('kdTblModalTitle').textContent = 'Configure Static Table';
    document.getElementById('kdTblApplyText').textContent = editingTableNode ? 'Apply Changes' : 'Insert Static Table';
    // Hide dynamic-only fields (table type selector)
    document.getElementById('kdTblType').closest('.kd-form-row').style.display = 'none';
    // Show/create record size field
    var existingRS = document.getElementById('kdTblRecordSize');
    if (!existingRS) {
      var rsField = document.createElement('div');
      rsField.className = 'kd-form-field';
      rsField.id = 'kdTblRecordSizeWrap';
      rsField.style.cssText = 'flex:1;';
      rsField.innerHTML = '<label>Rows (Record Size)</label><input type="number" id="kdTblRecordSize" value="3" min="1" max="50">';
      var colCountEl = document.getElementById('kdTblColCount').closest('.kd-form-field');
      colCountEl.parentElement.insertBefore(rsField, colCountEl.nextSibling);
    } else {
      document.getElementById('kdTblRecordSizeWrap').style.display = '';
    }
    modal.dataset.mode = 'static';
    if (!editingTableNode) renderTableColConfig();
    modal.style.display = 'flex';
  }

  function drawStaticTable(cfg, startX, startY) {
    var group = new Konva.Group({ x: startX || 30, y: startY || 30, draggable: true });
    group.setAttr('customType', 'static-table');
    group.on('dblclick', function () { editStaticTable(group); });
    renderStaticTableInGroup(group, cfg);
    layer.add(group); group.moveToBottom(); transformer.moveToTop();
    layer.batchDraw(); selectNode(group); recordHistory();
  }

  function editStaticTable(groupNode) {
    editingTableNode = groupNode;
    var cfgStr = groupNode.getAttr('tableConfig');
    if (!cfgStr) { toast('No config found', 'error'); return; }
    var cfg;
    try { cfg = JSON.parse(cfgStr); } catch (e) { toast('Invalid config', 'error'); return; }
    document.getElementById('kdTblName').value = cfg.name || 'Static Table';
    document.getElementById('kdTblColCount').value = cfg.columns ? cfg.columns.length : 3;
    document.getElementById('kdTblShowHeader').checked = cfg.showHeader !== false;
    document.getElementById('kdTblHeaderBg').value = cfg.headerBg || '#ffffff';
    document.getElementById('kdTblHeaderColor').value = cfg.headerColor || '#000000';
    document.getElementById('kdTblBorderWidth').value = cfg.borderWidth || 1;
    document.getElementById('kdTblShowFooter').checked = !!cfg.showFooter;
    document.getElementById('kdTblFooterLabel').value = cfg.footerLabel || 'Total';
    document.getElementById('kdTblFooterColSpan').value = cfg.footerColSpan || 2;
    document.getElementById('kdTblShowOuterBorder').checked = cfg.showOuterBorder !== false;
    document.getElementById('kdTblShowSideLines').checked = cfg.showSideLines !== false;
    document.getElementById('kdTblShowVLines').checked = cfg.showVLines !== false;
    document.getElementById('kdTblShowHLinesHeader').checked = cfg.showHLinesHeader !== false;
    document.getElementById('kdTblShowHLinesBody').checked = cfg.showHLinesBody !== false;
    document.getElementById('kdTblShowHLinesFooter').checked = cfg.showHLinesFooter !== false;
    document.getElementById('kdTblHeaderFont').value = cfg.headerFont || 'Arial';
    document.getElementById('kdTblHeaderSize').value = cfg.headerSize || 11;
    document.getElementById('kdTblBodyFont').value = cfg.bodyFont || 'Arial';
    document.getElementById('kdTblBodySize').value = cfg.bodySize || 11;
    document.getElementById('kdTblBodyColor').value = cfg.bodyColor || '#000000';
    document.getElementById('kdTblFooterFont').value = cfg.footerFont || 'Arial';
    document.getElementById('kdTblFooterSize').value = cfg.footerSize || 11;
    renderTableColConfig(cfg.columns);
    showStaticTableModal();
    var rsEl = document.getElementById('kdTblRecordSize');
    if (rsEl) rsEl.value = cfg.recordSize || 3;
  }

  function renderStaticTableInGroup(group, cfg) {
    group.destroyChildren();
    if (typeof cfg.showHeader === 'undefined') cfg.showHeader = true;
    if (!cfg.headerBg) cfg.headerBg = '#ffffff';
    if (!cfg.headerColor) cfg.headerColor = '#000000';
    var allCols = cfg.columns || [], fs = cfg.fontSize || 11, pad = 4;
    var cols = allCols.filter(function (c) { return !c.hidden; });
    var rowH = fs + pad * 2 + 4, totalW = 0;
    cols.forEach(function (c) { totalW += (c.width || 100); });
    var recordSize = cfg.recordSize || 3;
    var totalH = (cfg.showHeader ? rowH : 0) + recordSize * rowH + (cfg.showFooter ? rowH : 0);
    group.setAttr('tableConfig', JSON.stringify(cfg));
    // Store computed dimensions on the Group so they persist in JSON for PHP reflow
    group.width(totalW);
    group.height(totalH);
    var bw = cfg.borderWidth || 1;
    // Background
    group.add(new Konva.Rect({ x: 0, y: 0, width: totalW, height: totalH, fill: '#fff', listening: false }));
    // Outer borders
    if (cfg.showOuterBorder !== false) {
      group.add(new Konva.Line({ points: [0, 0, totalW, 0], stroke: '#000', strokeWidth: bw }));
      group.add(new Konva.Line({ points: [0, totalH, totalW, totalH], stroke: '#000', strokeWidth: bw }));
    }
    if (cfg.showSideLines !== false) {
      group.add(new Konva.Line({ points: [0, 0, 0, totalH], stroke: '#000', strokeWidth: bw }));
      group.add(new Konva.Line({ points: [totalW, 0, totalW, totalH], stroke: '#000', strokeWidth: bw }));
    }
    var cy = 0;
    // Header
    if (cfg.showHeader) {
      group.add(new Konva.Rect({ x: 0, y: 0, width: totalW, height: rowH, fill: cfg.headerBg || '#fff' }));
      if (cfg.showHLinesHeader !== false) group.add(new Konva.Line({ points: [0, 0, totalW, 0], stroke: '#000', strokeWidth: bw }));
      var cx = 0;
      cols.forEach(function (c, i) {
        group.add(new Konva.Text({ x: cx + pad, y: pad, text: c.header, fontSize: cfg.headerSize || fs, fontFamily: cfg.headerFont || 'Arial', fontStyle: 'bold', fill: cfg.headerColor || '#000', width: c.width - pad * 2, align: c.headerAlign || 'center' }));
        cx += c.width;
        if (i < cols.length - 1 && cfg.showVLines !== false) group.add(new Konva.Line({ points: [cx, 0, cx, totalH], stroke: '#000', strokeWidth: bw }));
      });
      if (cfg.showHLinesHeader !== false) group.add(new Konva.Line({ points: [0, rowH, totalW, rowH], stroke: '#000', strokeWidth: bw }));
      cy = rowH;
    }
    // Body rows — show fields in row 0, empty for rest
    for (var r = 0; r < recordSize; r++) {
      var cx2 = 0;
      cols.forEach(function (c) {
        var cellText = (r === 0) ? (c.field ? stripBraces(c.field) : '...') : '';
        group.add(new Konva.Text({ x: cx2 + pad, y: cy + pad, text: String(cellText), fontSize: cfg.bodySize || fs, fontFamily: cfg.bodyFont || 'Arial', fill: cfg.bodyColor || '#000', width: c.width - pad * 2, align: c.align || 'left', wrap: 'none', ellipsis: true }));
        cx2 += c.width;
      });
      cy += rowH;
      if (cfg.showHLinesBody !== false) {
        if (r < recordSize - 1 || !cfg.showFooter) {
          group.add(new Konva.Line({ points: [0, cy, totalW, cy], stroke: r < recordSize - 1 ? '#ccc' : '#000', strokeWidth: r < recordSize - 1 ? 1 : bw }));
        }
      }
    }
    // Footer
    if (cfg.showFooter) {
      if (cfg.showHLinesFooter !== false) group.add(new Konva.Line({ points: [0, cy, totalW, cy], stroke: '#000', strokeWidth: bw }));
      group.add(new Konva.Rect({ x: 0, y: cy, width: totalW, height: rowH, fill: '#f5f5f5' }));
      if (cfg.showHLinesFooter !== false) group.add(new Konva.Line({ points: [0, cy + rowH, totalW, cy + rowH], stroke: '#000', strokeWidth: bw }));
      var fLabel = cfg.footerLabel || 'Total';
      var fSpan = Math.min(cfg.footerColSpan || 2, cols.length);
      var fLabelW = 0;
      for (var fi = 0; fi < fSpan; fi++) fLabelW += cols[fi].width;
      group.add(new Konva.Text({ x: pad, y: cy + pad, text: fLabel, fontSize: cfg.footerSize || fs, fontFamily: cfg.footerFont || 'Arial', fontStyle: 'bold', fill: '#000', width: fLabelW - pad * 2, align: 'right' }));
      var fx = fLabelW;
      for (var fj = fSpan; fj < cols.length; fj++) {
        var c = cols[fj];
        group.add(new Konva.Text({ x: fx + pad, y: cy + pad, text: stripBraces(c.footerField || ''), fontSize: cfg.footerSize || fs, fontFamily: cfg.footerFont || 'Arial', fontStyle: 'bold', fill: '#000', width: c.width - pad * 2, align: c.align || 'right', wrap: 'none', ellipsis: true }));
        fx += c.width;
      }
    }
  }

  // ══════════════════════════════════════
  // INLINE TEXT EDIT
  // ══════════════════════════════════════
  function editTextInline(textNode) {
    var pos = textNode.getClientRect();
    var box = stage.container().getBoundingClientRect();
    var ta = document.createElement('textarea');
    ta.value = getRawText(textNode);
    ta.style.cssText = 'position:fixed;z-index:9999;border:2px solid #6366f1;outline:none;padding:4px;'
      + 'font-size:' + textNode.fontSize() * zoom + 'px;font-family:' + textNode.fontFamily() + ';'
      + 'color:' + textNode.fill() + ';background:rgba(255,255,255,0.95);resize:none;'
      + 'left:' + (box.left + pos.x * zoom) + 'px;top:' + (box.top + pos.y * zoom) + 'px;'
      + 'width:' + (pos.width * zoom + 20) + 'px;height:' + (pos.height * zoom + 20) + 'px;';
    document.body.appendChild(ta);
    ta.focus(); ta.select();
    function finish() { applyDisplayText(textNode, ta.value); layer.batchDraw(); ta.remove(); showProperties(textNode); }
    ta.addEventListener('blur', finish);
    ta.addEventListener('keydown', function (e) { if (e.key === 'Escape') finish(); });
  }

  // ══════════════════════════════════════
  // DELETE
  // ══════════════════════════════════════
  function deleteSelected() {
    if (!selectedNode && selectedNodes.length === 0) {
      toast('Nothing selected', 'error'); return;
    }

    // Multi-delete
    if (selectedNodes.length > 1) {
      var count = selectedNodes.length;
      var nodesToDestroy = selectedNodes.slice(); // copy
      deselect();
      nodesToDestroy.forEach(function (n) { n.destroy(); });
      // reflowLayout(); // Removed — it was destroying manual positioning and multi-column layouts
      recordHistory();
      autoExtendCanvas();
      toast(count + ' elements deleted', 'success');
      return;
    }

    // Single delete
    var typeName = selectedNode.getAttr('customType') || selectedNode.className || 'Element';
    var nodeToDestroy = selectedNode;
    deselect();
    nodeToDestroy.destroy();
    // reflowLayout(); // Removed — it was destroying manual positioning and multi-column layouts
    recordHistory();
    autoExtendCanvas();
    toast(typeName + ' deleted', 'success');
  }

  // ══════════════════════════════════════
  // UNDO / REDO
  // ══════════════════════════════════════
  function recordHistory() {
    if (historyPaused) return;
    // Serialize layer state (exclude Transformer)
    var state = [];
    layer.children.forEach(function (child) {
      if (child === transformer || child.className === 'Transformer') return;
      state.push(child.toJSON());
    });
    var json = JSON.stringify(state);
    // Don't record duplicate states
    if (historyIndex >= 0 && history[historyIndex] === json) return;
    // Truncate any redo states
    history = history.slice(0, historyIndex + 1);
    history.push(json);
    if (history.length > MAX_HISTORY) history.shift();
    historyIndex = history.length - 1;
    updateUndoRedoButtons();
  }

  function undo() {
    if (historyIndex <= 0) { toast('Nothing to undo', ''); return; }
    historyIndex--;
    restoreHistory(history[historyIndex]);
    toast('Undo', 'success');
  }

  function redo() {
    if (historyIndex >= history.length - 1) { toast('Nothing to redo', ''); return; }
    historyIndex++;
    restoreHistory(history[historyIndex]);
    toast('Redo', 'success');
  }

  function restoreHistory(json) {
    historyPaused = true;
    deselect();
    // Remove all children except transformer
    var toRemove = [];
    layer.children.forEach(function (child) {
      if (child === transformer || child.className === 'Transformer') return;
      toRemove.push(child);
    });
    toRemove.forEach(function (c) { c.destroy(); });
    // Restore from JSON
    var items = JSON.parse(json);
    var tableNodes = [];
    items.forEach(function (itemJson) {
      try {
        var node = Konva.Node.create(itemJson);
        if (node) {
          node.draggable(true);
          if (node.getAttr('customType') === 'data-table') {
            node.on('dblclick', function () { editTable(node); });
            tableNodes.push(node);
          }
          else if (node.getAttr('customType') === 'static-table') {
            node.on('dblclick', function () { editStaticTable(node); });
            tableNodes.push(node);
          }
          else if (node.className === 'Text') {
            node.on('dblclick', function () { editTextInline(node); });
            // Apply display text transform after restore
            var rawT = node.getAttr('_rawText') || node.text();
            if (rawT && rawT.indexOf('{{') !== -1) {
              node.setAttr('_rawText', rawT);
              node.text(stripBraces(rawT));
            }
            // Restore wrap and ellipsis settings if saved, otherwise default to none/true
            var savedWrap = node.getAttr('wrap') || 'none';
            var savedEllipsis = node.getAttr('ellipsis') !== undefined ? node.getAttr('ellipsis') : true;
            node.wrap(savedWrap);
            node.ellipsis(savedEllipsis);
          }
          layer.add(node);
        }
      } catch (e) { console.warn('Restore error:', e); }
    });
    // Rebuild table visuals
    tableNodes.forEach(function (tNode) {
      var tcStr = tNode.getAttr('tableConfig');
      if (tcStr) {
        try {
          var tc = typeof tcStr === 'string' ? JSON.parse(tcStr) : tcStr;
          if (tNode.getAttr('customType') === 'static-table') {
            renderStaticTableInGroup(tNode, tc);
          } else {
            renderTableInGroup(tNode, tc);
          }
        } catch (ex) { console.warn('Table restore rebuild error:', ex); }
      }
    });
    transformer.moveToTop();
    layer.batchDraw();
    historyPaused = false;
    updateUndoRedoButtons();
  }

  function updateUndoRedoButtons() {
    var undoBtn = document.getElementById('kdBtnUndo');
    var redoBtn = document.getElementById('kdBtnRedo');
    if (undoBtn) undoBtn.disabled = (historyIndex <= 0);
    if (redoBtn) redoBtn.disabled = (historyIndex >= history.length - 1);
  }

  // ══════════════════════════════════════
  // SAVE / LOAD
  // ══════════════════════════════════════
  function saveDesign() {
    // NOTE: reflowLayout() removed — it was destroying multi-column layouts
    // (e.g., invoice templates with left/right detail columns and center QR code)
    // by forcibly stacking rows with 8px gaps. User positioning is now preserved as-is.
    layer.batchDraw();

    // Build a clean stage JSON with ONLY the content layer (exclude guideLayer)
    // Strip layer x/y offset (margin shift) — renderer applies its own offset from margin config
    // Restore raw {{variable}} text in all Text nodes before serializing
    layer.find('Text').forEach(function (tn) {
      var raw = tn.getAttr('_rawText');
      if (raw) tn.text(raw);
    });
    // Temporarily assign custom attributes to avoid Konva default-stripping
    layer.find('Text').forEach(function (tn) {
      tn.setAttr('_saveWrap', tn.wrap() || 'none');
      tn.setAttr('_saveEllipsis', tn.ellipsis() !== false);
    });

    var layerObj = layer.toObject();
    delete layerObj.attrs.x;
    delete layerObj.attrs.y;

    // Restore the attributes in the serialized JSON
    (function restoreAttrs(nodes) {
      if (!nodes) return;
      nodes.forEach(function (n) {
        if (n.className === 'Text' && n.attrs) {
          if (n.attrs.hasOwnProperty('_saveWrap')) {
            n.attrs.wrap = n.attrs._saveWrap;
            delete n.attrs._saveWrap;
          }
          if (n.attrs.hasOwnProperty('_saveEllipsis')) {
            n.attrs.ellipsis = n.attrs._saveEllipsis;
            delete n.attrs._saveEllipsis;
          }
        }
        if (n.children) restoreAttrs(n.children);
      });
    })(layerObj.children);

    // Clean up from live nodes
    layer.find('Text').forEach(function (tn) {
      tn.setAttr('_saveWrap', undefined);
      tn.setAttr('_saveEllipsis', undefined);
    });

    var stageData = {
      attrs: { width: stage.width(), height: stage.height() },
      className: 'Stage',
      children: [layerObj]
    };
    var json = JSON.stringify(stageData);
    // Re-apply display text after serialization so canvas stays clean
    layer.find('Text').forEach(function (tn) {
      var raw = tn.getAttr('_rawText');
      if (raw) tn.text(stripBraces(raw));
    });
    fetch(cfg.saveDesignUrl, {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        konva_json: json,
        paper_size: cfg.paperSize,
        orientation: cfg.pageOrientation,
        margins: margins,
        zones: zones
      })
    }).then(function (r) { return r.json(); }).then(function (res) {
      if (res.success) {
        toast('Saved successfully!', 'success');
        if (res.new_id) {
          var newUrl = cfg.baseUrl + 'index.php/print-templates/designer-v2/' + res.new_id;
          window.history.replaceState(null, '', newUrl);
          cfg.templateId = res.new_id;
          cfg.saveDesignUrl = cfg.baseUrl + 'index.php/print-templates/save-design/' + res.new_id;
          cfg.loadDesignUrl = cfg.baseUrl + 'index.php/print-templates/load-design/' + res.new_id;
          // Update computed variable URLs to point to new template
          cfg.computedVarsUrl = cfg.baseUrl + 'index.php/print-templates/computed-vars/' + res.new_id;
          cfg.saveComputedVarUrl = cfg.baseUrl + 'index.php/print-templates/save-computed-var/' + res.new_id;
          cfg.deleteComputedVarUrl = cfg.baseUrl + 'index.php/print-templates/delete-computed-var/' + res.new_id;
          // Reload computed vars with new template scope
          loadComputedVars();
        }
      } else {
        toast('Save failed: ' + (res.message || ''), 'error');
      }
    }).catch(function (e) { toast('Save error: ' + e.message, 'error'); });
  }

  function loadDesign() {
    if (!cfg.loadDesignUrl) return;
    fetch(cfg.loadDesignUrl).then(function (r) { return r.json(); }).then(function (data) {
      if (!data) return;
      // Load margins if saved
      if (data.margins) {
        margins.top = parseFloat(data.margins.top) || 0;
        margins.right = parseFloat(data.margins.right) || 0;
        margins.bottom = parseFloat(data.margins.bottom) || 0;
        margins.left = parseFloat(data.margins.left) || 0;
        applyMarginsToUI();
        drawMarginGuides();
      }
      // Load zones if saved — state is set immediately, but visual guides
      // are deferred until after autoExtendCanvas() so stage.height() is correct.
      if (data.zones) {
        zones.header_height = parseFloat(data.zones.header_height) || 0;
        zones.footer_height = parseFloat(data.zones.footer_height) || 0;
        applyZonesToUI();
      }
      var jsonStr = null;
      if (data.konva_json) jsonStr = data.konva_json;
      else if (typeof data === 'string') jsonStr = data;
      if (!jsonStr) return;
      try {
        var parsed = typeof jsonStr === 'string' ? JSON.parse(jsonStr) : jsonStr;
        if (parsed.children && parsed.children[0] && parsed.children[0].children) {
          var tableNodes = [];
          parsed.children[0].children.forEach(function (child, idx) {
            if (child.className === 'Transformer') return;
            var childStr = typeof child === 'string' ? child : JSON.stringify(child);
            var node;
            try { node = Konva.Node.create(childStr); } catch (ce) { console.warn('Node create error at idx ' + idx + ':', ce); return; }
            if (node) {
              node.draggable(true);
              var ct = node.getAttr('customType');
              if (ct === 'data-table') {
                node.on('dblclick', function () { editTable(node); });
                tableNodes.push(node);
              }
              else if (ct === 'static-table') {
                node.on('dblclick', function () { editStaticTable(node); });
                tableNodes.push(node);
              }
              else if (node.className === 'Text') {
                node.on('dblclick', function () { editTextInline(node); });
                // Normalize any baked-in scale from older saves — convert to width/height
                var tsx = node.scaleX(), tsy = node.scaleY();
                if (tsx !== 1 || tsy !== 1) {
                  node.width(Math.max(20, node.width() * tsx));
                  node.height(Math.max(10, node.height() * tsy));
                  node.scaleX(1);
                  node.scaleY(1);
                }
                // Apply display text: strip {{}} for canvas, keep raw for save
                var rawT = node.text();
                if (rawT && rawT.indexOf('{{') !== -1) {
                  node.setAttr('_rawText', rawT);
                  node.text(stripBraces(rawT));
                }
                // Restore wrap and ellipsis settings if saved, otherwise default to none/true
                var savedWrap = node.getAttr('wrap') || 'none';
                var savedEllipsis = node.getAttr('ellipsis') !== undefined ? node.getAttr('ellipsis') : true;
                node.wrap(savedWrap);
                node.ellipsis(savedEllipsis);
              }
              else if (node.className === 'Image') {
                // Restore image source on load — serialization doesn't include the HTMLImageElement
                var imgSrc = node.getAttr('imageSrc') || node.getAttr('src') || '';
                if (node.getAttr('customType') === 'qrcode') {
                  // Regenerate QR placeholder
                  (function (imgNode) {
                    var sz = imgNode.width() || 80;
                    var cvs = document.createElement('canvas');
                    cvs.width = sz; cvs.height = sz;
                    var cx = cvs.getContext('2d');
                    cx.fillStyle = '#fff'; cx.fillRect(0, 0, sz, sz);
                    cx.strokeStyle = '#000'; cx.lineWidth = 2; cx.strokeRect(2, 2, sz - 4, sz - 4);
                    var fp = Math.round(sz * 0.22);
                    [[6, 6], [sz - fp - 6, 6], [6, sz - fp - 6]].forEach(function (p) {
                      cx.fillStyle = '#000'; cx.fillRect(p[0], p[1], fp, fp);
                      cx.fillStyle = '#fff'; cx.fillRect(p[0] + 4, p[1] + 4, fp - 8, fp - 8);
                      cx.fillStyle = '#000'; cx.fillRect(p[0] + 7, p[1] + 7, fp - 14, fp - 14);
                    });
                    cx.fillStyle = '#666'; cx.font = '9px Arial'; cx.textAlign = 'center';
                    cx.fillText('QR CODE', sz / 2, sz / 2 + 3);
                    var pi = new Image();
                    pi.onload = function () { imgNode.image(pi); layer.batchDraw(); };
                    pi.src = cvs.toDataURL();
                  })(node);
                } else if (imgSrc) {
                  // Regular image — restore from saved data URI
                  (function (imgNode, src) {
                    var pi = new Image();
                    pi.onload = function () { imgNode.image(pi); layer.batchDraw(); };
                    pi.src = src;
                  })(node, imgSrc);
                }
              }
              layer.add(node);
            }
          });
          // Second pass: rebuild table visuals from tableConfig
          tableNodes.forEach(function (tNode, ti) {
            var tcStr = tNode.getAttr('tableConfig');
            if (tcStr) {
              try {
                var tc = typeof tcStr === 'string' ? JSON.parse(tcStr) : tcStr;
                if (tNode.getAttr('customType') === 'static-table') {
                  renderStaticTableInGroup(tNode, tc);
                } else {
                  renderTableInGroup(tNode, tc);
                }
              } catch (ex) { console.warn('Table rebuild error:', ex); }
            }
          });

          // NOTE: reflowLayout() removed from load — it was destroying
          // multi-column positioning stored in the JSON. Tables already
          // recalculate their own dimensions during the rebuild above.

          transformer.moveToTop(); layer.batchDraw();
        }
      } catch (e) { console.warn('Load error:', e); }
      // Reset history and record the loaded state as the FIRST entry
      history = []; historyIndex = -1;
      setTimeout(function () {
        recordHistory();
        autoExtendCanvas();
        // Draw zone guides AFTER autoExtendCanvas so stage.height() = full canvas height
        drawZoneGuides();
        drawMarginGuides();
      }, 200);

    }).catch(function (e) { console.warn('loadDesign fetch error:', e); });
  }

  // ══════════════════════════════════════
  // PREVIEW
  // ══════════════════════════════════════
  function showPreview() {
    var modal = document.getElementById('kdPreviewModal');
    var page = document.getElementById('kdPreviewPage');
    page.style.width = Math.round(pageW * PX_PER_MM) + 'px';
    page.style.height = Math.round(pageH * PX_PER_MM) + 'px';
    deselect();
    // Hide guides for clean print-ready preview
    if (guideLayer) guideLayer.hide();
    stage.toImage({
      pixelRatio: 2, callback: function (img) {
        // Restore guides after capture
        if (guideLayer) { guideLayer.show(); guideLayer.batchDraw(); }
        page.innerHTML = ''; img.style.width = '100%'; img.style.height = '100%';
        page.appendChild(img);
      }
    });
    modal.style.display = 'flex';
  }

  // ══════════════════════════════════════
  // ZOOM
  // ══════════════════════════════════════
  function setZoom(z) {
    zoom = Math.max(0.25, Math.min(3, z));
    var wrapper = document.getElementById('kdCanvasWrapper');
    wrapper.style.transform = 'scale(' + zoom + ')';
    wrapper.style.transformOrigin = 'top center';
    document.getElementById('kdZoomLevel').textContent = Math.round(zoom * 100) + '%';
    updateRulers();
  }

  // ══════════════════════════════════════
  // BIND ALL EVENTS
  // ══════════════════════════════════════
  function bindEvents() {
    // Click-to-add fallback (for elements that can't be dropped, like image)
    document.querySelectorAll('.kd-element-item').forEach(function (el) {
      el.addEventListener('click', function () {
        var tool = this.dataset.tool;
        if (tool === 'image') addImage();
        else if (tool === 'qrcode') addQrCode();
        // Other tools use drag-drop only
      });
    });

    // Zoom
    document.getElementById('kdZoomIn').onclick = function () { setZoom(zoom + 0.1); };
    document.getElementById('kdZoomOut').onclick = function () { setZoom(zoom - 0.1); };
    document.getElementById('kdZoomFit').onclick = function () { setZoom(1); };

    // Actions
    document.getElementById('kdBtnSave').onclick = saveDesign;
    document.getElementById('kdBtnPreview').onclick = showPreview;
    document.getElementById('kdBtnDelete').onclick = deleteSelected;

    // Undo / Redo
    document.getElementById('kdBtnUndo').onclick = undo;
    document.getElementById('kdBtnRedo').onclick = redo;

    // Ruler sync on scroll
    document.getElementById('kdCanvasScroll').addEventListener('scroll', updateRulers);

    // Context bar — text
    document.getElementById('kdCtxFontSize').onchange = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.fontSize(+this.value); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxFontColor').onchange = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.fill(this.value); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxFontFamily').onchange = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.fontFamily(this.value); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxBold').onclick = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.fontStyle(selectedNode.fontStyle() === 'bold' ? 'normal' : 'bold'); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxItalic').onclick = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.fontStyle(selectedNode.fontStyle() === 'italic' ? 'normal' : 'italic'); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxUnderline').onclick = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.textDecoration(selectedNode.textDecoration() === 'underline' ? '' : 'underline'); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxTextCase').onchange = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.setAttr('textCase', this.value); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxAlignLeft').onclick = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.align('left'); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxAlignCenter').onclick = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.align('center'); layer.batchDraw(); recordHistory(); } };
    document.getElementById('kdCtxAlignRight').onclick = function () { if (selectedNode && selectedNode.className === 'Text') { selectedNode.align('right'); layer.batchDraw(); recordHistory(); } };

    // Context bar — shape
    document.getElementById('kdCtxFill').onchange = function () { if (selectedNode && selectedNode.fill) selectedNode.fill(this.value); layer.batchDraw(); recordHistory(); };
    document.getElementById('kdCtxStroke').onchange = function () { if (selectedNode && selectedNode.stroke) selectedNode.stroke(this.value); layer.batchDraw(); recordHistory(); };
    document.getElementById('kdCtxStrokeWidth').onchange = function () { if (selectedNode && selectedNode.strokeWidth) selectedNode.strokeWidth(+this.value); layer.batchDraw(); recordHistory(); };

    // Z-index
    document.getElementById('kdCtxBringFront').onclick = function () { if (selectedNode) { selectedNode.moveToTop(); transformer.moveToTop(); layer.batchDraw(); } };
    document.getElementById('kdCtxSendBack').onclick = function () { if (selectedNode) { selectedNode.moveToBottom(); layer.batchDraw(); } };

    // Table modal
    document.getElementById('kdTblColCount').onchange = function () { renderTableColConfig(); };
    document.getElementById('kdTblApply').onclick = insertTable;
    document.getElementById('kdTblCancel').onclick = function () { document.getElementById('kdTableModal').style.display = 'none'; pendingTableDrop = null; editingTableNode = null; };
    document.getElementById('kdTableModalClose').onclick = function () { document.getElementById('kdTableModal').style.display = 'none'; pendingTableDrop = null; editingTableNode = null; };

    // Preview modal
    document.getElementById('kdPreviewClose').onclick = function () { document.getElementById('kdPreviewModal').style.display = 'none'; };
    document.getElementById('kdPreviewPrint').onclick = function () { window.print(); };

    // Keyboard shortcuts
    document.addEventListener('keydown', function (e) {
      var tag = document.activeElement.tagName;
      // Allow default behavior for inputs, textareas, selects, and contenteditable elements
      if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || document.activeElement.isContentEditable) return;
      if (e.key === 'Delete' || e.key === 'Backspace') {
        e.preventDefault(); deleteSelected();
      }
      if (e.ctrlKey && e.key === 's') { e.preventDefault(); saveDesign(); }
      if (e.ctrlKey && e.key === 'z') { e.preventDefault(); undo(); }
      if (e.ctrlKey && (e.key === 'y' || e.key === 'Y')) { e.preventDefault(); redo(); }
      // Ctrl+A: Select all elements
      if (e.ctrlKey && (e.key === 'a' || e.key === 'A')) {
        e.preventDefault();
        var allNodes = [];
        layer.getChildren().forEach(function (child) {
          if (child === transformer || child.className === 'Transformer') return;
          allNodes.push(child);
        });
        if (allNodes.length > 0) {
          selectMultipleNodes(allNodes);
          toast(allNodes.length + ' elements selected', 'info');
        }
      }
      // Escape: deselect
      if (e.key === 'Escape') { deselect(); }
    });
  }

  // ══════════════════════════════════════
  // RULER SYSTEM — Zero starts at page corner
  // ══════════════════════════════════════
  function updateRulers() {
    var scroll = document.getElementById('kdCanvasScroll');
    var wrapper = document.getElementById('kdCanvasWrapper');
    var area = document.getElementById('kdCanvasArea');
    if (!scroll || !wrapper || !area) return;

    // Get the canvas container's position relative to the ruler area
    var container = document.getElementById('kdCanvasContainer');
    if (!container) return;
    var containerRect = container.getBoundingClientRect();
    var areaRect = area.getBoundingClientRect();

    // startX/startY = pixel position of the page's top-left corner relative to the ruler's 0 position
    // The rulers start at left:25px (ruler width), so we offset by 25px
    var startX = containerRect.left - areaRect.left - 25;
    var startY = containerRect.top - areaRect.top - 25;

    var pxPerMm = PX_PER_MM * zoom;

    renderTicks('kdRulerTop', 'h', startX, pageW, pxPerMm);
    renderTicks('kdRulerBottom', 'h', startX, pageW, pxPerMm);
    renderTicks('kdRulerLeft', 'v', startY, pageH, pxPerMm);
    renderTicks('kdRulerRight', 'v', startY, pageH, pxPerMm);
  }

  function renderTicks(id, dir, startPos, maxMm, pxPerMm) {
    var el = document.getElementById(id);
    if (!el) return;
    el.innerHTML = '';

    var rulerSize = (dir === 'h') ? el.offsetWidth : el.offsetHeight;

    // Adaptive step based on zoom to keep ticks readable
    var step = 1;
    if (zoom < 0.7) step = 2;
    if (zoom < 0.4) step = 5;

    // Label interval — show labels every 10mm normally, wider at low zoom
    var labelInterval = 10;
    if (zoom < 0.5) labelInterval = 20;
    if (zoom < 0.3) labelInterval = 50;

    for (var mm = 0; mm <= maxMm; mm += step) {
      var pos = startPos + (mm * pxPerMm);
      // Visibility check — skip ticks outside ruler bounds
      if (pos < -20 || pos > rulerSize + 20) continue;

      var tick = document.createElement('div');
      var type = 'tiny';
      if (mm % 5 === 0) type = 'minor';
      if (mm % 10 === 0) type = 'major';
      tick.className = 'kd-tick ' + type;
      if (dir === 'h') {
        tick.style.left = pos + 'px';
      } else {
        tick.style.top = pos + 'px';
      }
      el.appendChild(tick);

      // Labels at the labelInterval
      if (mm % labelInterval === 0 && mm > 0) {
        var lbl = document.createElement('span');
        lbl.className = 'kd-tick-label';
        lbl.textContent = mm;
        if (dir === 'h') {
          lbl.style.left = (pos + 2) + 'px';
          lbl.style.top = '2px';
        } else {
          lbl.style.top = (pos + 2) + 'px';
          lbl.style.left = '2px';
        }
        el.appendChild(lbl);
      }
    }
  }

  // ══════════════════════════════════════
  // MARGIN SYSTEM
  // ══════════════════════════════════════
  function bindMarginEvents() {
    var btn = document.getElementById('kdBtnMargin');
    var popover = document.getElementById('kdMarginPopover');
    if (!btn || !popover) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = popover.classList.toggle('open');
      if (isOpen) {
        // Position the popover below the button using fixed positioning
        var btnRect = btn.getBoundingClientRect();
        popover.style.top = (btnRect.bottom + 4) + 'px';
        popover.style.left = Math.max(0, btnRect.right - 280) + 'px'; // Right-align, 280px is popover width
      }
    });

    // Close popover on outside click
    document.addEventListener('click', function (e) {
      if (!popover.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
        popover.classList.remove('open');
      }
    });

    // Bind margin input changes
    ['Top', 'Right', 'Bottom', 'Left'].forEach(function (side) {
      var input = document.getElementById('kdMargin' + side);
      if (input) {
        input.addEventListener('input', function () {
          margins[side.toLowerCase()] = parseFloat(this.value) || 0;
          updateMarginPreview();
          drawMarginGuides();
        });
      }
    });
  }

  function applyMarginsToUI() {
    var el;
    el = document.getElementById('kdMarginTop'); if (el) el.value = margins.top;
    el = document.getElementById('kdMarginRight'); if (el) el.value = margins.right;
    el = document.getElementById('kdMarginBottom'); if (el) el.value = margins.bottom;
    el = document.getElementById('kdMarginLeft'); if (el) el.value = margins.left;
    updateMarginPreview();
    updateMarginButton();
  }

  function updateMarginPreview() {
    var preview = document.getElementById('kdMarginPreview');
    if (!preview) return;
    var hasMargin = margins.top > 0 || margins.right > 0 || margins.bottom > 0 || margins.left > 0;
    if (hasMargin) {
      preview.textContent = 'T:' + margins.top + '  R:' + margins.right + '  B:' + margins.bottom + '  L:' + margins.left + ' mm';
    } else {
      preview.textContent = 'No margins set';
    }
    updateMarginButton();
  }

  function updateMarginButton() {
    var btn = document.getElementById('kdBtnMargin');
    if (!btn) return;
    var hasMargin = margins.top > 0 || margins.right > 0 || margins.bottom > 0 || margins.left > 0;
    if (hasMargin) btn.classList.add('has-margin');
    else btn.classList.remove('has-margin');
  }

  function drawMarginGuides() {
    if (!guideLayer) return;
    // Remove existing margin guide lines
    ['top', 'right', 'bottom', 'left'].forEach(function (side) {
      if (marginGuides[side]) marginGuides[side].destroy();
      marginGuides[side] = null;
    });
    // Remove existing margin overlay rectangles
    marginOverlays.forEach(function (r) { r.destroy(); });
    marginOverlays = [];

    var sW = stage.width(), sH = stage.height();
    var mt = margins.top * PX_PER_MM;
    var mr = margins.right * PX_PER_MM;
    var mb = margins.bottom * PX_PER_MM;
    var ml = margins.left * PX_PER_MM;

    // ── CORE: Shift the content layer to create real white-space margins ──
    // This makes the canvas WYSIWYG — content physically moves by the margin amount
    layer.x(ml);
    layer.y(mt);
    layer.batchDraw();

    var hasAny = mt > 0 || mr > 0 || mb > 0 || ml > 0;
    if (!hasAny) { guideLayer.batchDraw(); return; }

    // Shaded overlay style — semi-transparent fill covering the margin "dead zone"
    var overlayFill = 'rgba(99, 102, 241, 0.08)'; // subtle indigo tint
    var edgeStyle = { stroke: '#6366f1', strokeWidth: 1, dash: [6, 3], opacity: 0.6, listening: false, name: '_marginGuide' };

    // ── TOP margin overlay ──
    if (mt > 0) {
      var topRect = new Konva.Rect({
        x: 0, y: 0, width: sW, height: mt,
        fill: overlayFill, listening: false, name: '_marginGuide'
      });
      guideLayer.add(topRect);
      marginOverlays.push(topRect);
      marginGuides.top = new Konva.Line(Object.assign({ points: [0, mt, sW, mt] }, edgeStyle));
      guideLayer.add(marginGuides.top);
    }

    // ── BOTTOM margin overlay ──
    if (mb > 0) {
      var botRect = new Konva.Rect({
        x: 0, y: sH - mb, width: sW, height: mb,
        fill: overlayFill, listening: false, name: '_marginGuide'
      });
      guideLayer.add(botRect);
      marginOverlays.push(botRect);
      marginGuides.bottom = new Konva.Line(Object.assign({ points: [0, sH - mb, sW, sH - mb] }, edgeStyle));
      guideLayer.add(marginGuides.bottom);
    }

    // ── LEFT margin overlay ──
    if (ml > 0) {
      var leftRect = new Konva.Rect({
        x: 0, y: mt, width: ml, height: sH - mt - mb,
        fill: overlayFill, listening: false, name: '_marginGuide'
      });
      guideLayer.add(leftRect);
      marginOverlays.push(leftRect);
      marginGuides.left = new Konva.Line(Object.assign({ points: [ml, 0, ml, sH] }, edgeStyle));
      guideLayer.add(marginGuides.left);
    }

    // ── RIGHT margin overlay ──
    if (mr > 0) {
      var rightRect = new Konva.Rect({
        x: sW - mr, y: mt, width: mr, height: sH - mt - mb,
        fill: overlayFill, listening: false, name: '_marginGuide'
      });
      guideLayer.add(rightRect);
      marginOverlays.push(rightRect);
      marginGuides.right = new Konva.Line(Object.assign({ points: [sW - mr, 0, sW - mr, sH] }, edgeStyle));
      guideLayer.add(marginGuides.right);
    }

    guideLayer.batchDraw();
  }

  // ══════════════════════════════════════
  // RULER INDICATORS
  // ══════════════════════════════════════
  function updateRulerIndicators(node) {
    if (!node) { hideRulerIndicators(); return; }
    var container = document.getElementById('kdCanvasContainer');
    var area = document.getElementById('kdCanvasArea');
    if (!container || !area) return;

    var rect = node.getClientRect({ relativeTo: layer });
    var containerRect = container.getBoundingClientRect();
    var areaRect = area.getBoundingClientRect();

    // Position relative to the canvas area (which contains rulers)
    var offsetX = containerRect.left - areaRect.left;
    var offsetY = containerRect.top - areaRect.top;

    // Start and End positions in pixels on the screen
    var x1 = offsetX + (rect.x * zoom);
    var y1 = offsetY + (rect.y * zoom);
    var x2 = offsetX + ((rect.x + rect.width) * zoom);
    var y2 = offsetY + ((rect.y + rect.height) * zoom);

    // Update Crosshairs (Vertical lines track X, Horizontal track Y)
    setInd('kdIndL', 'v', x1);
    setInd('kdIndR', 'v', x2);
    setInd('kdIndT', 'h', y1);
    setInd('kdIndB', 'h', y2);
  }

  function setInd(id, dir, pos) {
    var el = document.getElementById(id);
    if (!el) return;
    el.style.display = 'block';
    if (dir === 'v') el.style.left = pos + 'px';
    else el.style.top = pos + 'px';
  }

  function hideRulerIndicators() {
    ['kdIndL', 'kdIndR', 'kdIndT', 'kdIndB'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.style.display = 'none';
    });
  }

  // ==========================================
  // ZONE SYSTEM — Header / Footer
  // ==========================================
  function bindZoneEvents() {
    var btn = document.getElementById('kdBtnZones');
    var popover = document.getElementById('kdZonePopover');
    if (!btn || !popover) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = popover.classList.toggle('open');
      if (isOpen) {
        // Position the popover below the button using fixed positioning
        var btnRect = btn.getBoundingClientRect();
        popover.style.top = (btnRect.bottom + 4) + 'px';
        popover.style.left = Math.max(0, btnRect.right - 300) + 'px';

        // Sync inputs with current values
        document.getElementById('kdZoneHeader').value = zones.header_height;
        document.getElementById('kdZoneFooter').value = zones.footer_height;
        updateZonePreview();
      }
    });

    // Close popover on outside click
    document.addEventListener('click', function (e) {
      if (!popover.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
        popover.classList.remove('open');
      }
    });

    // Bind input changes
    ['Header', 'Footer'].forEach(function (type) {
      var input = document.getElementById('kdZone' + type);
      if (input) {
        input.addEventListener('input', function () {
          zones[type.toLowerCase() + '_height'] = parseFloat(this.value) || 0;
          updateZonePreview();
          drawZoneGuides();

          var btnZones = document.getElementById('kdBtnZones');
          if (zones.header_height > 0 || zones.footer_height > 0) {
            btnZones.classList.add('has-zones');
          } else {
            btnZones.classList.remove('has-zones');
          }
        });
      }
    });
  }

  function applyZonesToUI() {
    var headerInput = document.getElementById('kdZoneHeader');
    var footerInput = document.getElementById('kdZoneFooter');
    if (headerInput) headerInput.value = zones.header_height;
    if (footerInput) footerInput.value = zones.footer_height;

    var btnZones = document.getElementById('kdBtnZones');
    if (btnZones) {
      if (zones.header_height > 0 || zones.footer_height > 0) {
        btnZones.classList.add('has-zones');
      } else {
        btnZones.classList.remove('has-zones');
      }
    }
    updateZonePreview();
  }

  function updateZonePreview() {
    var preview = document.getElementById('kdZonePreview');
    if (!preview) return;
    if (zones.header_height === 0 && zones.footer_height === 0) {
      preview.textContent = 'No zones set';
      preview.style.borderColor = 'var(--kd-border)';
    } else {
      preview.innerHTML = '<strong>Zones Active:</strong><br>' +
        'Header Height: ' + zones.header_height + 'mm<br>' +
        'Footer Height: ' + zones.footer_height + 'mm';
      preview.style.borderColor = '#fb923c';
    }
  }

  function drawZoneGuides() {
    if (!guideLayer) return;

    // Destroy old zone guides
    guideLayer.find('._zoneGuide').forEach(function (n) { n.destroy(); });

    var sW = stage.width();
    var sH = stage.height();
    var hh = zones.header_height || 0;
    var fh = zones.footer_height || 0;

    var hhPx = hh * PX_PER_MM;
    var fhPx = fh * PX_PER_MM;

    // ── HEADER BAND (blue) ──
    if (hhPx > 0) {
      zoneGuides.headerBand = new Konva.Rect({
        x: 0, y: 0, width: sW, height: hhPx,
        fill: 'rgba(96,165,250,0.08)', listening: false, name: '_zoneGuide'
      });
      guideLayer.add(zoneGuides.headerBand);

      zoneGuides.headerLine = new Konva.Line({
        points: [0, hhPx, sW, hhPx],
        stroke: '#60a5fa', strokeWidth: 1.5,
        dash: [8, 4], opacity: 0.85, listening: false, name: '_zoneGuide'
      });
      guideLayer.add(zoneGuides.headerLine);

      zoneGuides.headerLabel = new Konva.Text({
        x: 6, y: hhPx - 14, text: 'HEADER ZONE ↓ ' + hh + 'mm',
        fontSize: 9, fontFamily: 'Arial', fill: '#60a5fa',
        opacity: 0.9, listening: false, name: '_zoneGuide'
      });
      guideLayer.add(zoneGuides.headerLabel);
    }

    // ── FOOTER BAND (orange) ──
    if (fhPx > 0) {
      var footerY = sH - fhPx;
      zoneGuides.footerBand = new Konva.Rect({
        x: 0, y: footerY, width: sW, height: fhPx,
        fill: 'rgba(251,146,60,0.10)', listening: false, name: '_zoneGuide'
      });
      guideLayer.add(zoneGuides.footerBand);

      zoneGuides.footerLine = new Konva.Line({
        points: [0, footerY, sW, footerY],
        stroke: '#fb923c', strokeWidth: 1.5,
        dash: [8, 4], opacity: 0.85, listening: false, name: '_zoneGuide'
      });
      guideLayer.add(zoneGuides.footerLine);

      zoneGuides.footerLabel = new Konva.Text({
        x: 6, y: footerY + 3, text: 'FOOTER ZONE ↑ ' + fh + 'mm',
        fontSize: 9, fontFamily: 'Arial', fill: '#fb923c',
        opacity: 0.9, listening: false, name: '_zoneGuide'
      });
      guideLayer.add(zoneGuides.footerLabel);
    }

    guideLayer.batchDraw();
  }

  document.addEventListener('DOMContentLoaded', init);
})();
