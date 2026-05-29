<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Konva V2 Canvas Receipt Renderer
 * ─────────────────────────────────
 * Converts a saved Konva stage JSON into printable HTML,
 * substituting {{field}} placeholders with real bill data.
 *
 * Called when  ret_settings.template_based = 3
 */

/**
 * Evaluate a condition variable as truthy/falsy.
 * Falsy: undefined, null, empty string, '0', 0, false, '0.00'
 * Truthy: 1, >1, any non-empty string, non-zero number
 */
function _konva_is_truthy($value)
{
    if ($value === null || $value === '' || $value === false) return false;
    if ($value === 0 || $value === '0') return false;
    if ($value === '0.00') return false;
    return true;
}

/**
 * Evaluate a compound condition expression against bill data.
 * Supports:
 *   - Single variable:  "has_return_items"
 *   - OR expression:    "has_return_items||has_sales_items"  (true if ANY is truthy)
 *   - AND expression:   "has_return_items&&has_sales_items"  (true only if ALL are truthy)
 * Note: mixing || and && in one expression is not supported — use one operator type.
 * @return bool  true if the condition is met (element should show)
 */
function _konva_eval_condition($condExpr, $bill_data)
{
    $condExpr = trim($condExpr);
    if ($condExpr === '') return true; // no condition = always show

    // OR expression: var1||var2||var3
    if (strpos($condExpr, '||') !== false) {
        $parts = explode('||', $condExpr);
        foreach ($parts as $part) {
            $key = trim($part);
            if ($key === '') continue;
            $val = isset($bill_data[$key]) ? $bill_data[$key] : null;
            if (_konva_is_truthy($val)) return true; // any truthy = pass
        }
        return false; // none were truthy
    }

    // AND expression: var1&&var2&&var3
    if (strpos($condExpr, '&&') !== false) {
        $parts = explode('&&', $condExpr);
        foreach ($parts as $part) {
            $key = trim($part);
            if ($key === '') continue;
            $val = isset($bill_data[$key]) ? $bill_data[$key] : null;
            if (!_konva_is_truthy($val)) return false; // any falsy = fail
        }
        return true; // all were truthy
    }

    // Single variable (original behavior)
    $val = isset($bill_data[$condExpr]) ? $bill_data[$condExpr] : null;
    return _konva_is_truthy($val);
}

/**
 * Resolve/override conditionVar for chit adjustment vs pre-close elements on the canvas
 */
function _konva_get_node_condition($attrs)
{
    $condVar = isset($attrs['conditionVar']) ? $attrs['conditionVar'] : '';
    if ($condVar === 'has_chit_items') {
        $id = isset($attrs['id']) ? (string)$attrs['id'] : '';
        $text = isset($attrs['text']) ? (string)$attrs['text'] : '';
        $tableConfig = isset($attrs['tableConfig']) ? $attrs['tableConfig'] : '';
        $tableConfigStr = is_array($tableConfig) ? json_encode($tableConfig) : (string)$tableConfig;
        
        // If it's a pre-close element
        if (strpos($id, 'pre_close') !== false || strpos($id, 'preclose') !== false ||
            strpos(strtolower($text), 'pre-close') !== false || strpos(strtolower($text), 'preclose') !== false ||
            strpos($tableConfigStr, 'chit_pre_close') !== false) {
            return 'is_chit_preclose';
        }
        
        // If it's a chit adjustment element
        if (strpos($id, 'chit_adjustment') !== false || 
            strpos(strtolower($text), 'chit adjustment') !== false || 
            strpos($tableConfigStr, 'chit_adjustment') !== false) {
            return 'has_chit_adj';
        }
    }
    return $condVar;
}

/**
 * Main entry: render a Konva V2 template for a given bill
 *
 * @param  int    $template_id   print_templates.id_template
 * @param  array  $bill_data     Combined/mapped bill data (flat key=>value + 'items' array, etc.)
 * @return string|false          Full HTML page ready for echo / DomPDF, or false on failure
 */
function render_konva_template($template_id, $bill_data)
{
    $CI =& get_instance();
    $CI->load->model('print_template_model');

    $template = $CI->print_template_model->get_by_id($template_id);
    if (!$template) return false;

    // The Konva JSON lives inside gjs_data
    $gjs = json_decode($template['gjs_data'], true);
    if (!$gjs) return false;


    $konva_json_str = isset($gjs['konva_json']) ? $gjs['konva_json'] : null;
    if (!$konva_json_str) return false;

    $stage = is_string($konva_json_str) ? json_decode($konva_json_str, true) : $konva_json_str;
    if (!$stage) return false;

    // Paper dimensions
    $paper   = $template['paper_size'] ?? 'A4';
    $orient  = $template['page_orientation'] ?? 'portrait';
    $papers  = [
        'A4'=>[210,297], 'A3'=>[297,420], 'A5'=>[148,210],
        'Letter'=>[216,279], '58mm'=>[58,210], '80mm'=>[80,297],
    ];
    $dims = isset($papers[$paper]) ? $papers[$paper] : $papers['A4'];
    $pw = $dims[0]; $ph = $dims[1];
    if ($orient === 'landscape') { $tmp=$pw; $pw=$ph; $ph=$tmp; }

    // Conversion constant — MUST be defined before any px<->mm math
    $px_per_mm = 3.7795;

    // Extract print margins (from gjs_data or DB columns)
    $mt = 0; $mr = 0; $mb = 0; $ml = 0;
    if (isset($gjs['margins'])) {
        $mt = (float)($gjs['margins']['top'] ?? 0);
        $mr = (float)($gjs['margins']['right'] ?? 0);
        $mb = (float)($gjs['margins']['bottom'] ?? 0);
        $ml = (float)($gjs['margins']['left'] ?? 0);
    } elseif (isset($template['margin_top'])) {
        $mt = (float)$template['margin_top'];
        $mr = (float)$template['margin_right'];
        $mb = (float)$template['margin_bottom'];
        $ml = (float)$template['margin_left'];
    }

    // Extract zone heights (mm and px). If not set, default to 0 (no zones).
    $zone_header_mm = 0;
    $zone_footer_mm = 0;
    $zone_header_px = 0;
    $zone_footer_px = 0;
    if (isset($gjs['zones'])) {
        $zone_header_mm = (float)($gjs['zones']['header_height'] ?? 0);
        $zone_footer_mm = (float)($gjs['zones']['footer_height'] ?? 0);
        $zone_header_px = $zone_header_mm * $px_per_mm;
        $zone_footer_px = $zone_footer_mm * $px_per_mm;
    }


    // Walk the Konva tree -> produce HTML blocks
    // Margins are applied by offsetting every element's position (top/left) during rendering
    // This is necessary because position:absolute ignores CSS padding/margin on parent containers
    $elements_html = '';
    $all_elements = [];  // Collects elements with positions for page-break calculation
    $base_offset = ['top' => $mt, 'left' => $ml]; // mm offset for print margins

    // ── Resolve computed variables (formulas stored in DB) ──
    $computed_vars = $CI->print_template_model->get_computed_vars($template_id);
    if (!empty($computed_vars)) {
        // Build a scalar-only flat map for the expression parser.
        // $bill_data may contain arrays (items, billing, etc.) that would break str_replace in the tokenizer.
        $scalar_data = [];
        foreach ($bill_data as $k => $v) {
            if (is_scalar($v) || is_null($v)) {
                $scalar_data[$k] = (string)$v;
            }
        }

        // Multiple passes to allow computed vars that reference other computed vars
        for ($pass = 0; $pass < 3; $pass++) {
            foreach ($computed_vars as $cv) {
                $formula = $cv['formula'];
                // Strip {{}} wrappers from formula to get raw expression
                // e.g. "{{sub_total}} - {{discount}}" → "sub_total - discount"
                $expr = preg_replace('/\{\{(\w+)\}\}/', '$1', $formula);
                $result = _tpl_eval_expr($expr, $scalar_data);
                if ($result !== null) {
                    $formatted = number_format($result, 2);
                    $bill_data[$cv['var_name']] = $formatted;
                    $scalar_data[$cv['var_name']] = (string)$result; // Feed forward raw for next pass math
                }
            }
        }
    }

    // Stash template ID in bill_data so table renderer can evaluate computed vars per-row
    $bill_data['_template_id'] = $template_id;

    if (isset($stage['children'])) {
        foreach ($stage['children'] as $layer_idx => $layer_data) {
            if (!isset($layer_data['children'])) continue;
            // Skip the guideLayer (2nd layer) — it only contains UI guides, not content
            if ($layer_idx > 0) continue;

            $nodes = $layer_data['children'];

            // ── PASS 1: Compute hidden zones by walking nodes sorted by Y ──
            // Strategy: sort all renderable nodes by Y position, then identify
            // contiguous blocks where ALL conditional elements are hidden.
            // Non-conditional elements sandwiched between hidden conditional
            // elements are absorbed into the zone.

            // First, build a sorted list with visibility info
            $node_info = []; // [{y, isConditional, isHidden, idx}]
            foreach ($nodes as $idx => $node) {
                $attrs = isset($node['attrs']) ? $node['attrs'] : [];
                $cls   = isset($node['className']) ? $node['className'] : '';

                // Skip non-renderable nodes
                if ($cls === 'Transformer') continue;
                $nm = isset($attrs['name']) ? $attrs['name'] : '';
                if ($nm === '_marginGuide' || $nm === '_pageBreak' || $nm === '_zoneGuide') continue;

                if ($cls === 'Line' && isset($attrs['dash']) && isset($attrs['stroke'])) {
                    if ($attrs['stroke'] === '#38bdf8' || $attrs['stroke'] === '#6366f1') continue;
                }
                if ($cls === 'Rect' && $nm === '_marginGuide') continue;

                // Get node Y position
                $nodeY = floatval($attrs['y'] ?? 0);
                // For lines, add the point offset
                if ($cls === 'Line') {
                    $pts = $attrs['points'] ?? [];
                    if (count($pts) >= 4) {
                        $nodeY += min($pts[1], $pts[3]);
                    }
                }

                $condVar = _konva_get_node_condition($attrs);
                $isConditional = !empty($condVar);
                $isHidden = false;
                if ($isConditional) {
                    $isHidden = !_konva_eval_condition($condVar, $bill_data);
                }

                // Calculate element height for proper zone sizing
                $elemH = 20; // default for Text-like elements
                if ($cls === 'Group') {
                    // Table group — compute designed height from tableConfig
                    $tCfgStr = isset($attrs['tableConfig']) ? $attrs['tableConfig'] : '';
                    if (!empty($tCfgStr)) {
                        $tCfg = is_string($tCfgStr) ? json_decode($tCfgStr, true) : $tCfgStr;
                        if ($tCfg) {
                            $fs = isset($tCfg['bodySize']) ? $tCfg['bodySize'] : (isset($tCfg['fontSize']) ? $tCfg['fontSize'] : 11);
                            $rH = $fs + 4 * 2 + 4; // pad=4, same as designer
                            $sR = 1; // data-table uses 1 sample row in designer
                            $ct = isset($attrs['customType']) ? $attrs['customType'] : '';
                            if ($ct === 'static-table') $sR = isset($tCfg['recordSize']) ? $tCfg['recordSize'] : 3;
                            $hdr = (isset($tCfg['showHeader']) && $tCfg['showHeader'] !== false) ? $rH : 0;
                            $ftr = (!empty($tCfg['showFooter'])) ? $rH : 0;
                            $elemH = $hdr + $sR * $rH + $ftr;
                        }
                    }
                } elseif ($cls === 'Text') {
                    $elemH = floatval($attrs['fontSize'] ?? 12) + 8;
                } elseif ($cls === 'Rect') {
                    $elemH = floatval($attrs['height'] ?? 20);
                } elseif ($cls === 'Image') {
                    $elemH = floatval($attrs['height'] ?? 30);
                } elseif ($cls === 'Line') {
                    $pts = $attrs['points'] ?? [];
                    if (count($pts) >= 4) {
                        $elemH = abs($pts[3] - $pts[1]) + 2;
                    } else {
                        $elemH = 2;
                    }
                }

                $node_info[] = [
                    'y' => $nodeY,
                    'cls' => $cls,
                    'height' => $elemH,
                    'isConditional' => $isConditional,
                    'isHidden' => $isHidden,
                    'isVisible' => $isConditional && !$isHidden,
                    'idx' => $idx
                ];
            }

            // Sort by Y position
            usort($node_info, function($a, $b) {
                if ($a['y'] == $b['y']) return 0;
                return ($a['y'] < $b['y']) ? -1 : 1;
            });

            // Walk sorted nodes to identify hidden zones
            // A hidden zone starts at the first hidden conditional node and extends
            // until we hit a non-conditional node that is NOT followed by another
            // hidden conditional node, or a visible conditional node.
            $merged_zones = [];
            $zone_start = null;
            $last_hidden_bottom = null; // Y + height of the last hidden element
            $pending_non_conditional = []; // non-conditional nodes between hidden conditionals

            for ($ni = 0; $ni < count($node_info); $ni++) {
                $info = $node_info[$ni];

                if ($info['isHidden']) {
                    // Hidden conditional element
                    if ($zone_start === null) {
                        $zone_start = $info['y'];
                    }
                    // Track the bottom edge (Y + height) of this hidden element
                    $bottom = $info['y'] + $info['height'];
                    if ($last_hidden_bottom === null || $bottom > $last_hidden_bottom) {
                        $last_hidden_bottom = $bottom;
                    }
                    // Absorb any pending non-conditional nodes
                    $pending_non_conditional = [];
                } elseif ($info['isVisible']) {
                    // Visible conditional element — ends any open zone
                    if ($zone_start !== null) {
                        // End the zone exactly at the next visible element's Y.
                        // This collapses the hidden element's space AND any whitespace 
                        // between it and the next visible element, effectively making
                        // the next element "occupy" the space of the removed one.
                        $zone_end = $info['y'];
                        if ($zone_end > $zone_start) {
                            $merged_zones[] = ['yStart' => $zone_start, 'yEnd' => $zone_end];
                        }
                        $zone_start = null;
                        $last_hidden_bottom = null;
                        $pending_non_conditional = [];
                    }
                } else {
                    // Non-conditional element
                    if ($zone_start !== null) {
                        // Only Line elements (separators) can be absorbed into the hidden zone.
                        // Text, Rect, Group = real content = zone boundary.
                        if ($info['cls'] === 'Line') {
                            // Check if there's another hidden conditional element after this line
                            $has_more_hidden = false;
                            for ($nj = $ni + 1; $nj < count($node_info); $nj++) {
                                if ($node_info[$nj]['isHidden']) {
                                    // Only absorb if next hidden is within reasonable distance
                                    if ($node_info[$nj]['y'] - $info['y'] < 200) {
                                        $has_more_hidden = true;
                                    }
                                    break;
                                }
                                if ($node_info[$nj]['isVisible']) {
                                    break;
                                }
                                // If next is non-conditional Text/Group, stop looking
                                if ($node_info[$nj]['cls'] !== 'Line') {
                                    break;
                                }
                            }

                            if ($has_more_hidden) {
                                // This separator line is sandwiched — absorb it
                                $pending_non_conditional[] = $info;
                            } else {
                                // Zone ends at this line
                                $merged_zones[] = ['yStart' => $zone_start, 'yEnd' => $info['y']];
                                $zone_start = null;
                                $last_hidden_bottom = null;
                                $pending_non_conditional = [];
                            }
                        } else {
                            // Non-Line content element (Text/Rect/Group) — end the zone here
                            // Cap at this element's Y so the zone doesn't extend into its space
                            $zone_end = $info['y'];
                            if ($zone_end > $zone_start) {
                                $merged_zones[] = ['yStart' => $zone_start, 'yEnd' => $zone_end];
                            }
                            $zone_start = null;
                            $last_hidden_bottom = null;
                            $pending_non_conditional = [];
                        }
                    }
                }
            }
            // Close any open zone at end of nodes
            if ($zone_start !== null && $last_hidden_bottom !== null) {
                $merged_zones[] = ['yStart' => $zone_start, 'yEnd' => $last_hidden_bottom];
            }

            // ── PASS 1.5: Table Delta Pre-calculation with Page-Splitting ──
            $table_deltas = [];
            $table_meta = [];
            for ($ni = 0; $ni < count($node_info); $ni++) {
                $info = $node_info[$ni];
                if ($info['isHidden']) continue;
                $node_idx = $info['idx'];
                $node_data = $nodes[$node_idx];
                $node_cls = $node_data['className'] ?? '';
                $node_ct = $node_data['attrs']['customType'] ?? '';
                if ($node_cls !== 'Group' || ($node_ct !== 'data-table' && $node_ct !== 'static-table')) continue;

                $tblAttrs = $node_data['attrs'] ?? [];
                $tblCfgStr = $tblAttrs['tableConfig'] ?? '';
                if (empty($tblCfgStr)) continue;
                $tblCfg = is_string($tblCfgStr) ? json_decode($tblCfgStr, true) : $tblCfgStr;
                if (!$tblCfg) continue;

                $tblType = $tblCfg['type'] ?? 'items';
                $tblItems = _konva_get_table_items($tblType, $bill_data);

                // Heights in px (96dpi)
                $fontSize = $tblCfg['bodySize'] ?? $tblCfg['fontSize'] ?? 11;
                $pad = isset($tblCfg['cellPadding']) ? intval($tblCfg['cellPadding']) : 2;
                $rowH = $fontSize + $pad * 2 + 2;
                $hasHeader = ($tblCfg['showHeader'] ?? true) !== false;
                $hasFooter = !empty($tblCfg['showFooter']);
                
                $hasSubRows = !empty($tblCfg['hasSubRows']);
                $subRowH = 0;
                if ($hasSubRows) {
                    $subFontSize = $tblCfg['subBodySize'] ?? ($fontSize > 2 ? $fontSize - 1 : $fontSize);
                    $subRowH = $subFontSize + $pad * 2 + 2;
                }

                $rowH_mm = $rowH / $px_per_mm;
                $subRowH_mm = $subRowH / $px_per_mm;
                $headerH_mm = $hasHeader ? ($rowH / $px_per_mm) : 0;
                $footerH_mm = $hasFooter ? ($rowH / $px_per_mm) : 0;

                $tableY = $info['y'];

                // Calculate cumulative collapse zones shift up to this table
                $y_shift_px = 0;
                foreach ($merged_zones as $zone) {
                    if ($zone['yEnd'] <= $tableY) {
                        $y_shift_px += ($zone['yEnd'] - $zone['yStart']);
                    }
                }
                $y_shift_mm = $y_shift_px / $px_per_mm;

                // Calculate previous table deltas that shift this table down
                $y_table_delta_px = 0;
                foreach ($table_deltas as $td) {
                    $tableDesignBottom = $td['tableY'] + $td['designTimeH'];
                    if ($tableY > $tableDesignBottom) {
                        $y_table_delta_px += $td['delta'];
                    }
                }
                $y_table_delta_mm = $y_table_delta_px / $px_per_mm;

                $reserved_bottom_mm = max($mb, $zone_footer_mm);
                $tableTopAbsMm = ($tableY / $px_per_mm) + $base_offset['top'] - $y_shift_mm + $y_table_delta_mm;
                $usableH_page1 = $ph - $tableTopAbsMm - $reserved_bottom_mm - 2;
                $usableH_page2plus = $ph - max($mt, $zone_header_mm) - $reserved_bottom_mm - 2;

                $min_required_h = $headerH_mm + 2 * $rowH_mm;
                $table_starts_on_page2 = false;
                if ($usableH_page1 < $min_required_h) {
                    $table_starts_on_page2 = true;
                }

                $chunks = [];
                $current_chunk = [];
                $current_chunk_h = $headerH_mm;
                $is_first_page = !$table_starts_on_page2;

                foreach ($tblItems as $itm) {
                    $item_h = $rowH_mm;
                    if ($hasSubRows && !empty($itm['_sub_rows'])) {
                        $item_h += count($itm['_sub_rows']) * $subRowH_mm;
                    }
                    
                    $usable_h = $is_first_page ? $usableH_page1 : $usableH_page2plus;

                    if ($current_chunk_h + $item_h + $footerH_mm > $usable_h && !empty($current_chunk)) {
                        $chunks[] = $current_chunk;
                        $current_chunk = [$itm];
                        $current_chunk_h = $headerH_mm + $item_h;
                        $is_first_page = false;
                    } else {
                        $current_chunk[] = $itm;
                        $current_chunk_h += $item_h;
                    }
                }
                if (!empty($current_chunk)) {
                    $chunks[] = $current_chunk;
                }
                if (empty($chunks)) {
                    $chunks[] = [];
                }

                $total_chunks = count($chunks);
                $current_page_idx = $table_starts_on_page2 ? 1 : 0;
                $running_gaps_px = 0;
                $prev_chunk_end_relative = null;

                foreach ($chunks as $chunk_idx => $chunk_items) {
                    if ($chunk_idx === 0) {
                        $chunkTopMm = $table_starts_on_page2 ? ($ph + $base_offset['top']) : $tableTopAbsMm;
                        if ($table_starts_on_page2) {
                            $gap_mm = ($ph + $base_offset['top']) - $tableTopAbsMm;
                            $running_gaps_px += $gap_mm * $px_per_mm;
                        }
                    } else {
                        $chunkTopMm = $current_page_idx * $ph + $base_offset['top'];
                        if ($prev_chunk_end_relative !== null) {
                            $gap_mm = ($ph - $prev_chunk_end_relative) + $base_offset['top'];
                            $running_gaps_px += $gap_mm * $px_per_mm;
                        }
                    }

                    $chunkRowCount = empty($chunk_items) ? 1 : count($chunk_items);
                    $chunkSubRowCount = 0;
                    if ($hasSubRows && !empty($chunk_items)) {
                        foreach ($chunk_items as $itm) {
                            if (!empty($itm['_sub_rows'])) {
                                $chunkSubRowCount += count($itm['_sub_rows']);
                            }
                        }
                    }
                    $chunkH_px = (($chunk_idx === 0 ? $hasHeader : true) ? $rowH : 0)
                               + ($chunkRowCount * $rowH)
                               + ($chunkSubRowCount * $subRowH)
                               + (($chunk_idx === $total_chunks - 1 && $hasFooter) ? $rowH : 0);
                    $chunkH_mm = $chunkH_px / $px_per_mm;

                    $prev_chunk_end_relative = ($chunk_idx === 0 && !$table_starts_on_page2)
                                             ? ($tableTopAbsMm - ($current_page_idx * $ph) + $chunkH_mm)
                                             : ($base_offset['top'] + $chunkH_mm);

                    $current_page_idx++;
                }

                // Calculate total actual height
                $totalActualH_px = 0;
                foreach ($chunks as $chunk_idx => $chunk_items) {
                    $chunkRowCount = empty($chunk_items) ? 1 : count($chunk_items);
                    $chunkSubRowCount = 0;
                    if ($hasSubRows && !empty($chunk_items)) {
                        foreach ($chunk_items as $itm) {
                            if (!empty($itm['_sub_rows'])) {
                                $chunkSubRowCount += count($itm['_sub_rows']);
                            }
                        }
                    }
                    $chunkH_px = (($chunk_idx === 0 ? $hasHeader : true) ? $rowH : 0)
                               + ($chunkRowCount * $rowH)
                               + ($chunkSubRowCount * $subRowH)
                               + (($chunk_idx === $total_chunks - 1 && $hasFooter) ? $rowH : 0);
                    $totalActualH_px += $chunkH_px;
                }

                $designPad = isset($tblCfg['cellPadding']) ? intval($tblCfg['cellPadding']) : 2;
                $designRowH = $fontSize + $designPad * 2 + 4;
                $designSampleRows = ($node_ct === 'static-table')
                    ? (isset($tblCfg['recordSize']) ? intval($tblCfg['recordSize']) : 3)
                    : 1;
                $designTimeH = ($hasHeader ? $designRowH : 0)
                             + $designSampleRows * $designRowH
                             + ($hasFooter ? $designRowH : 0);

                $totalDelta = $totalActualH_px - $designTimeH + $running_gaps_px;

                $table_deltas[] = [
                    'tableY'      => $tableY,
                    'designTimeH' => $designTimeH,
                    'actualH'     => $totalActualH_px + $running_gaps_px,
                    'delta'       => $totalDelta
                ];
                $table_meta[] = [
                    'tableY'  => $tableY,
                    'actualH' => $totalActualH_px + $running_gaps_px,
                    '_only_meta' => true
                ];
            }

            // ── PASS 2: Render each node at DESIGN position ──
            foreach ($nodes as $node) {
                $attrs = isset($node['attrs']) ? $node['attrs'] : [];
                $customType = isset($attrs['customType']) ? $attrs['customType'] : '';

                $cls = isset($node['className']) ? $node['className'] : '';
                $nodeY = floatval($attrs['y'] ?? 0);
                if ($cls === 'Line') {
                    $pts = $attrs['points'] ?? [];
                    if (count($pts) >= 4) {
                        $nodeY += min($pts[1], $pts[3]);
                    }
                }

                // Skip rendering if conditional and false
                $condVar = _konva_get_node_condition($attrs);
                if (!empty($condVar)) {
                    if (!_konva_eval_condition($condVar, $bill_data)) {
                        continue;
                    }
                } else {
                    // Skip unconditional elements inside hidden zones
                    foreach ($merged_zones as $zone) {
                        if ($nodeY >= $zone['yStart'] && $nodeY < $zone['yEnd']) {
                            continue 2;
                        }
                    }
                }

                // Calculate cumulative Y-shift from COLLAPSE zones only
                $y_shift_px = 0;
                foreach ($merged_zones as $zone) {
                    if ($zone['yEnd'] <= $nodeY) {
                        $y_shift_px += ($zone['yEnd'] - $zone['yStart']);
                    }
                }
                $y_shift_mm = $y_shift_px / $px_per_mm;
                $node_offset = [
                    'top'  => $base_offset['top'] - $y_shift_mm,
                    'left' => $base_offset['left']
                ];

                // If this is a table, run page-splitting logic
                if ($cls === 'Group' && ($customType === 'data-table' || $customType === 'static-table')) {
                    $tblAttrs = $node['attrs'] ?? [];
                    $tblCfgStr = $tblAttrs['tableConfig'] ?? '';
                    if (!empty($tblCfgStr)) {
                        $tblCfg = is_string($tblCfgStr) ? json_decode($tblCfgStr, true) : $tblCfgStr;
                        if ($tblCfg) {
                            $tblType = $tblCfg['type'] ?? 'items';
                            $tblItems = _konva_get_table_items($tblType, $bill_data);
                            
                            $fontSize = $tblCfg['bodySize'] ?? $tblCfg['fontSize'] ?? 11;
                            $pad = isset($tblCfg['cellPadding']) ? intval($tblCfg['cellPadding']) : 2;
                            $rowH = $fontSize + $pad * 2 + 2;
                            $hasHeader = ($tblCfg['showHeader'] ?? true) !== false;
                            $hasFooter = !empty($tblCfg['showFooter']);
                            
                            $hasSubRows = !empty($tblCfg['hasSubRows']);
                            $subRowH = 0;
                            if ($hasSubRows) {
                                $subFontSize = $tblCfg['subBodySize'] ?? ($fontSize > 2 ? $fontSize - 1 : $fontSize);
                                $subRowH = $subFontSize + $pad * 2 + 2;
                            }
                            
                            $rowH_mm = $rowH / $px_per_mm;
                            $subRowH_mm = $subRowH / $px_per_mm;
                            $headerH_mm = $hasHeader ? ($rowH / $px_per_mm) : 0;
                            $footerH_mm = $hasFooter ? ($rowH / $px_per_mm) : 0;
                            
                            // Compute tableTopAbsMm (including previous table deltas)
                            $y_table_delta_px = 0;
                            foreach ($table_deltas as $td) {
                                $tableDesignBottom = $td['tableY'] + $td['designTimeH'];
                                if ($nodeY > $tableDesignBottom) {
                                    $y_table_delta_px += $td['delta'];
                                }
                            }
                            $y_table_delta_mm = $y_table_delta_px / $px_per_mm;
                            
                            $reserved_bottom_mm = max($mb, $zone_footer_mm);
                            $tableTopAbsMm = ($nodeY / $px_per_mm) + $base_offset['top'] - $y_shift_mm + $y_table_delta_mm;
                            $usableH_page1 = $ph - $tableTopAbsMm - $reserved_bottom_mm - 2;
                            $usableH_page2plus = $ph - max($mt, $zone_header_mm) - $reserved_bottom_mm - 2;
                            
                            $min_required_h = $headerH_mm + 2 * $rowH_mm;
                            $table_starts_on_page2 = false;
                            if ($usableH_page1 < $min_required_h) {
                                $table_starts_on_page2 = true;
                            }
                            
                            $chunks = [];
                            $current_chunk = [];
                            $current_chunk_h = $headerH_mm;
                            $is_first_page = !$table_starts_on_page2;
                            
                            foreach ($tblItems as $item) {
                                $item_h = $rowH_mm;
                                if ($hasSubRows && !empty($item['_sub_rows'])) {
                                    $item_h += count($item['_sub_rows']) * $subRowH_mm;
                                }
                                
                                $usable_h = $is_first_page ? $usableH_page1 : $usableH_page2plus;
                                
                                if ($current_chunk_h + $item_h + $footerH_mm > $usable_h && !empty($current_chunk)) {
                                    $chunks[] = $current_chunk;
                                    $current_chunk = [$item];
                                    $current_chunk_h = $headerH_mm + $item_h;
                                    $is_first_page = false;
                                } else {
                                    $current_chunk[] = $item;
                                    $current_chunk_h += $item_h;
                                }
                            }
                            if (!empty($current_chunk)) {
                                $chunks[] = $current_chunk;
                            }
                            if (empty($chunks)) {
                                $chunks[] = [];
                            }
                            
                            $total_chunks = count($chunks);
                            $current_page_idx = $table_starts_on_page2 ? 1 : 0;
                            $prev_chunk_end_relative = null;
                            
                            foreach ($chunks as $chunk_idx => $chunk_items) {
                                if ($chunk_idx === 0) {
                                    $chunkTopMm = $table_starts_on_page2 ? ($ph + max($mt, $zone_header_mm)) : $tableTopAbsMm;
                                } else {
                                    $chunkTopMm = $current_page_idx * $ph + max($mt, $zone_header_mm);
                                }
                                
                                $showHeaderOverride = true;
                                if ($chunk_idx === 0) {
                                    $showHeaderOverride = $hasHeader;
                                }
                                $showFooterOverride = ($chunk_idx === $total_chunks - 1) ? $hasFooter : false;
                                
                                $chunk_offset = [
                                    'top'  => $chunkTopMm - ($attrs['y'] ?? 0) / $px_per_mm,
                                    'left' => $node_offset['left']
                                ];
                                
                                $chunkHtml = _konva_render_table($attrs, $bill_data, $px_per_mm, $chunk_offset, $chunk_items, $showHeaderOverride, $showFooterOverride);
                                
                                if ($chunk_idx > 0) {
                                    $chunkHtml = preg_replace(
                                        '/data-design-h="[\d.]+"/',
                                        'data-design-h="0"',
                                        $chunkHtml,
                                        1
                                    );
                                }
                                
                                $chunkRowCount = empty($chunk_items) ? 1 : count($chunk_items);
                                $chunkSubRowCount = 0;
                                if ($hasSubRows && !empty($chunk_items)) {
                                    foreach ($chunk_items as $itm) {
                                        if (!empty($itm['_sub_rows'])) {
                                            $chunkSubRowCount += count($itm['_sub_rows']);
                                        }
                                    }
                                }
                                $chunkH_px = (($chunk_idx === 0 ? $hasHeader : true) ? $rowH : 0)
                                           + ($chunkRowCount * $rowH)
                                           + ($chunkSubRowCount * $subRowH)
                                           + (($chunk_idx === $total_chunks - 1 && $hasFooter) ? $rowH : 0);
                                $chunkH_mm = $chunkH_px / $px_per_mm;
                                
                                // Tag chunk HTML with data-node-y and data-php-delta = 0
                                $chunkHtml = preg_replace(
                                    '/^(<div\s)/',
                                    '<div data-node-y="' . round($nodeY, 2) . '" data-php-delta="0" ',
                                    $chunkHtml,
                                    1
                                );
                                
                                $all_elements[] = [
                                    'html'                     => $chunkHtml,
                                    'topMm'                    => $chunkTopMm,
                                    'heightMm'                 => $chunkH_mm,
                                    'repeat'                   => false,
                                    'footerZone'               => false,
                                    'offsetFromCanvasBottomPx' => 0,
                                    'nodeY'                    => $nodeY
                                ];
                                
                                $prev_chunk_end_relative = ($chunk_idx === 0 && !$table_starts_on_page2)
                                                         ? ($tableTopAbsMm - ($current_page_idx * $ph) + $chunkH_mm)
                                                         : ($base_offset['top'] + $chunkH_mm);
                                
                                $current_page_idx++;
                            }
                            
                            continue; // Skip the default node rendering block
                        }
                    }
                }

                $rendered_html = _konva_render_node($node, $bill_data, $px_per_mm, $node_offset);
                if (!empty($rendered_html)) {
                    // Compute approximate table delta for PAGE-BREAK estimation
                    $y_table_delta_px = 0;
                    foreach ($table_deltas as $td) {
                        $tableDesignBottom = $td['tableY'] + $td['designTimeH'];
                        if ($nodeY > $tableDesignBottom) {
                            $y_table_delta_px += $td['delta'];
                        }
                    }
                    $y_table_delta_mm = $y_table_delta_px / $px_per_mm;
                    
                    // effectiveTopMm includes table delta for page-break calc
                    $effectiveTopMm = ($nodeY / $px_per_mm) + $base_offset['top'] - $y_shift_mm + $y_table_delta_mm;

                    // Inject the approximate delta into the element's CSS top position
                    if ($y_table_delta_mm > 0.01) {
                        $rendered_html = preg_replace(
                            '/top:\s*([\d.]+)mm/',
                            'top:' . round($effectiveTopMm, 2) . 'mm',
                            $rendered_html,
                            1
                        );
                    }

                    // For page-break estimation, use approx table height
                    $nodeH_px = floatval($attrs['height'] ?? 20);
                    if ($customType === 'data-table' || $customType === 'static-table') {
                        foreach ($table_meta as $tz) {
                            if (isset($tz['tableY']) && abs($tz['tableY'] - $nodeY) < 2) {
                                $nodeH_px = $tz['actualH'];
                                break;
                            }
                        }
                    }
                    $nodeH_mm = $nodeH_px / $px_per_mm;

                    // Tag the HTML with data-node-y for JS reflow, and data-php-delta
                    $rendered_html = preg_replace(
                        '/^(<div\s)/',
                        '<div data-node-y="' . round($nodeY, 2) . '" data-php-delta="' . round($y_table_delta_px, 2) . '" ',
                        $rendered_html,
                        1
                    );

                    // ── Zone classification ──
                    $isRepeat    = !empty($attrs['repeatEveryPage']);
                    $isFooterZone = false;

                    if ($zone_header_px > 0 || $zone_footer_px > 0) {
                        $stageH_px = isset($stage['attrs']['height']) ? (float)$stage['attrs']['height'] : ($ph * $px_per_mm);
                        // Stage-relative Y coordinates
                        $stageY = $nodeY + $base_offset['top'] * $px_per_mm;
                        
                        if (!$isRepeat && $zone_header_px > 0 && $stageY < $zone_header_px) {
                            $isRepeat = true; // header zone → repeat on every page at designed Y
                        } elseif ($zone_footer_px > 0 && $stageY >= ($stageH_px - $zone_footer_px)) {
                            $isFooterZone = true; // footer zone → pin to last page bottom
                            // Absolute offset from the stage bottom
                            $offsetFromCanvasBottomPx = $stageH_px - $stageY;
                        }
                    }

                    $all_elements[] = [
                        'html'                     => $rendered_html,
                        'topMm'                    => $effectiveTopMm,
                        'heightMm'                 => $nodeH_mm,
                        'repeat'                   => $isRepeat,
                        'footerZone'               => $isFooterZone,
                        'offsetFromCanvasBottomPx' => $isFooterZone ? $offsetFromCanvasBottomPx : 0,
                        'nodeY'                    => $nodeY
                    ];
                }
            }
        }
    }

    // ══════════════════════════════════════════════════════
    // Pass 3: Auto Page-Break & Repeat-Every-Page Assembly
    // ══════════════════════════════════════════════════════
    
    // DEBUG: Output reflow data as HTML comments when requested
    if (!empty($_GET['debug_reflow'])) {
        echo '<!-- REFLOW_DEBUG:' . json_encode([
            'table_deltas' => $table_deltas,
            'merged_zones' => $merged_zones,
            'elements_count' => count($all_elements)
        ]) . ' -->';
    }
    
    $pageH_mm = $ph; // full page height for positioning

    // Sort elements by their effective top position
    usort($all_elements, function($a, $b) {
        return $a['topMm'] <=> $b['topMm'];
    });

    // Separate elements into 3 buckets:
    //   $repeat_elements   → header zone + explicit repeatEveryPage → appear on every page at designed Y
    //   $footer_elements   → footer zone → pinned to BOTTOM of LAST page only
    //   $normal_elements   → body content → paginated normally
    $repeat_elements = [];
    $footer_elements = [];
    $normal_elements = [];
    foreach ($all_elements as $el) {
        if (!empty($el['footerZone'])) {
            $footer_elements[] = $el;
        } elseif ($el['repeat']) {
            $repeat_elements[] = $el;
        } else {
            $normal_elements[] = $el;
        }
    }

    // Assign normal elements to pages
    $pages = [[]]; // array of pages, each page is an array of element HTML
    $currentPage = 0;
    
    // Page 1 boundaries
    $pageTopMm = $mt;
    $pageBottomMm = $ph - max($mb, $zone_footer_mm) - 2;
    $pageFirstElTop = []; // track the first element's absolute top on each page (for margin enforcement)

    foreach ($normal_elements as $el) {
        $elTop = $el['topMm'];
        $elBottom = $elTop + $el['heightMm'];
        
        // Check if element exceeds current page boundary
        if ($elBottom > $pageBottomMm && $elTop > $pageTopMm + 5) {
            // Push to next page
            $currentPage++;
            
            // Page 2+ boundaries
            $pageTopMm = $currentPage * $pageH_mm + max($mt, $zone_header_mm);
            $pageBottomMm = $currentPage * $pageH_mm + $ph - max($mb, $zone_footer_mm) - 2;
            
            $pages[$currentPage] = [];
        }
        
        // For elements on page 2+, compute position relative to their page div
        // Each .kv-page div is position:relative, so top must be relative to its own div
        $relativeTop = $elTop - ($currentPage * $pageH_mm);
        
        if ($currentPage > 0) {
            // Track the first element on this page to calculate shift needed
            if (!isset($pageFirstElTop[$currentPage])) {
                $pageFirstElTop[$currentPage] = $relativeTop;
            }
            
            // Enforce top margin / header zone boundary on every subsequent page:
            // Shift all elements down so the first element starts at max($mt, $zone_header_mm)
            $firstEl = $pageFirstElTop[$currentPage];
            $targetTop = max($mt, $zone_header_mm);
            if ($firstEl < $targetTop) {
                // Shift all elements on this page by the difference
                $marginShift = $targetTop - $firstEl;
                $relativeTop += $marginShift;
            }
        }
        
        // Re-render the HTML with adjusted top position
        if ($currentPage > 0 || abs($relativeTop - $el['topMm']) > 0.01) {
            $el['html'] = preg_replace(
                '/top:\s*[\d.]+mm/',
                'top:' . round($relativeTop, 2) . 'mm',
                $el['html'],
                1  // only replace first occurrence (the outermost element)
            );
        }
        
        $pages[$currentPage][] = $el['html'];
    }

    // Build final HTML with page breaks
    $totalPages = count($pages);
    
    $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Print</title>';
    $html .= '<style>';
    $html .= '@page { size: '.$pw.'mm '.$ph.'mm; margin: 0; }';
    $html .= '* { box-sizing: border-box; margin:0; padding:0; }';
    $html .= 'html,body { width:'.$pw.'mm; position:relative; font-family:Arial,sans-serif; }';
    $html .= '.kv-page { width:'.$pw.'mm; min-height:'.$ph.'mm; position:relative; overflow:visible; }';
    $html .= '@media print { html,body { width:'.$pw.'mm; margin:0; padding:0; } .kv-page { overflow:visible; page-break-after:always; } .kv-page:last-child { page-break-after:auto; } }';
    $html .= '</style></head><body>';

    for ($p = 0; $p < $totalPages; $p++) {
        $html .= '<div class="kv-page">';
        
        // Add repeat-every-page elements to every page
        if ($p > 0 && !empty($repeat_elements)) {
            foreach ($repeat_elements as $rEl) {
                // Clone the element with adjusted top for this page
                $cloneHtml = $rEl['html'];
                $newTop = $rEl['topMm']; // same relative position on each page (page div is position:relative)
                $cloneHtml = preg_replace(
                    '/top:\s*[\d.]+mm/',
                    'top:' . round($newTop, 2) . 'mm',
                    $cloneHtml,
                    1
                );
                $html .= $cloneHtml;
            }
        } else if ($p === 0) {
            // First page: repeat elements are already included in their original position
            foreach ($repeat_elements as $rEl) {
                $html .= $rEl['html'];
            }
        }
        
        // Add page-specific elements
        if (isset($pages[$p])) {
            foreach ($pages[$p] as $elHtml) {
                $html .= $elHtml;
            }
        }

        // ── Footer zone: pin to bottom of EVERY page ──
        // Position each footer element relative to the page bottom using its
        // designed distance from the canvas bottom. This makes the footer
        // appear at the correct bottom position on every printed page.
        if (!empty($footer_elements)) {
            foreach ($footer_elements as $fEl) {
                // Distance from canvas bottom → distance from print page bottom
                $offsetFromBottomMm = $fEl['offsetFromCanvasBottomPx'] / $px_per_mm;
                // Position = pageH minus the offset from bottom (accounts for margin)
                $pinnedTopMm = $ph - $offsetFromBottomMm;
                $fHtml = preg_replace(
                    '/top:\s*-?[\d.]+mm/',
                    'top:' . round($pinnedTopMm, 2) . 'mm',
                    $fEl['html'],
                    1
                );
                $html .= $fHtml;
            }
        }

        $html .= '</div>';
    }
    
    // ── JS Post-Render Reflow ──
    // Measures actual table heights in the browser and applies correction
    // (real delta - PHP estimated delta) so page-break positions stay accurate.
    $html .= '<script>';
    $html .= '(function(){';
    $html .= 'var pxPerMm=3.7795;';
    // Collect all tables with their design heights
    $html .= 'var tds=[];';
    $html .= 'document.querySelectorAll("div[data-design-h]").forEach(function(d){';
    $html .= '  var t=d.querySelector("table");if(!t)return;';
    $html .= '  var dh=parseFloat(d.dataset.designH)||0;';
    $html .= '  var ny=parseFloat(d.dataset.nodeY)||0;';
    $html .= '  var ah=t.getBoundingClientRect().height;';
    // delta = actual browser height (CSS px) - design height (canvas px)
    $html .= '  tds.push({y:ny,designH:dh,actualH:ah,delta:ah-dh,type:d.dataset.tableType||""});';
    $html .= '});';
    $html .= 'tds.sort(function(a,b){return a.y-b.y});';
    // For each positioned element, compute correction: real shift - PHP estimated shift
    $html .= 'document.querySelectorAll("div[data-node-y]").forEach(function(el){';
    $html .= '  var ny=parseFloat(el.dataset.nodeY)||0;';
    $html .= '  var isTable=(el.dataset.designH!==undefined);';
    $html .= '  var phpDelta=parseFloat(el.dataset.phpDelta)||0;';
    $html .= '  var realShift=0;';
    $html .= '  tds.forEach(function(td){';
    $html .= '    if(isTable && Math.abs(td.y-ny)<2)return;';
    $html .= '    var tblDesignBottom=td.y+td.designH;';
    $html .= '    if(ny>tblDesignBottom){realShift+=td.delta;}';
    $html .= '  });';
    // Correction = real browser shift - PHP estimated shift
    $html .= '  var correction=realShift-phpDelta;';
    $html .= '  if(Math.abs(correction)>0.5){';
    $html .= '    var cur=parseFloat(el.style.top)||0;';
    $html .= '    el.style.top=(cur+correction/pxPerMm).toFixed(2)+"mm";';
    $html .= '  }';
    $html .= '});';

    // ── Pass 2: Collapse design gap below the main items table ──
    // Only applies to type=items. Finds the first element below the items table
    // canvas bottom, calculates the designed gap, and shifts only elements between
    // the items table and the next table up to close it.
    $html .= '(function(){';
    $html .= '  var itemsTbl=null;';
    $html .= '  var nextTblY=Infinity;';
    $html .= '  tds.forEach(function(td){';
    $html .= '    if(td.type==="items"&&!itemsTbl)itemsTbl=td;';
    $html .= '    else if(itemsTbl&&td.y>itemsTbl.y)nextTblY=Math.min(nextTblY,td.y);';
    $html .= '  });';
    $html .= '  if(!itemsTbl)return;';
    $html .= '  var tblCanvasBottom=itemsTbl.y+itemsTbl.designH;';
    // Find the closest element below the items table canvas bottom
    $html .= '  var firstGap=Infinity;';
    $html .= '  document.querySelectorAll("div[data-node-y]").forEach(function(el){';
    $html .= '    if(el.dataset.designH!==undefined)return;';
    $html .= '    var eny=parseFloat(el.dataset.nodeY)||0;';
    $html .= '    var g=eny-tblCanvasBottom;';
    $html .= '    if(g>0&&g<firstGap)firstGap=g;';
    $html .= '  });';
    // Collapse by (firstGap - 3px margin), only for elements between items table and next table
    $html .= '  if(firstGap>5&&firstGap<Infinity){';
    $html .= '    var collapseMm=(firstGap-3)/pxPerMm;';
    $html .= '    document.querySelectorAll("div[data-node-y]").forEach(function(el){';
    $html .= '      if(el.dataset.designH!==undefined)return;';
    $html .= '      var eny=parseFloat(el.dataset.nodeY)||0;';
    // Only shift elements between items table bottom and next table Y
    $html .= '      if(eny>tblCanvasBottom&&eny<nextTblY){';
    $html .= '        var cur=parseFloat(el.style.top)||0;';
    $html .= '        el.style.top=(cur-collapseMm).toFixed(2)+"mm";';
    $html .= '      }';
    $html .= '    });';
    $html .= '  }';
    $html .= '})();';

    // Auto-trigger print dialog after reflow completes
    $html .= 'setTimeout(function(){window.print();},150);';
    $html .= '})();';
    $html .= '</script>';
    $html .= '</body></html>';

    return $html;
}

/**
 * Render a single Konva node to absolutely-positioned HTML
 * $offset = ['top' => mm, 'left' => mm] — print margin offset applied to coordinates
 */
function _konva_render_node($node, $bill_data, $px_per_mm, $offset = ['top'=>0,'left'=>0])
{
    $className = isset($node['className']) ? $node['className'] : '';
    $attrs     = isset($node['attrs']) ? $node['attrs'] : [];
    $customType = isset($attrs['customType']) ? $attrs['customType'] : '';
    $name = isset($attrs['name']) ? $attrs['name'] : '';

    // Skip Transformer nodes
    if ($className === 'Transformer') return '';

    // Skip margin guide lines and zone guide shapes
    if ($name === '_marginGuide' || $name === '_pageBreak' || $name === '_zoneGuide') return '';
    // Also skip any dashed guide-colored lines (old or new guide colors)
    if ($className === 'Line' && isset($attrs['dash']) && isset($attrs['stroke'])) {
        if ($attrs['stroke'] === '#38bdf8' || $attrs['stroke'] === '#6366f1') return '';
    }
    // Skip Rect overlays from margin or zone areas
    if ($className === 'Rect' && ($name === '_marginGuide' || $name === '_zoneGuide')) return '';


    // ── Conditional Visibility ──
    // Supports: single var, OR (var1||var2), AND (var1&&var2)
    // Elements without conditionVar always render.
    $condVar = _konva_get_node_condition($attrs);
    if (!empty($condVar)) {
        if (!_konva_eval_condition($condVar, $bill_data)) {
            return ''; // condition not met — skip this element
        }
    }

    $offT = $offset['top'];
    $offL = $offset['left'];

    // ── GROUP (could be a data-table) ──
    if ($className === 'Group') {
        if ($customType === 'data-table' || $customType === 'static-table') {
            return _konva_render_table($attrs, $bill_data, $px_per_mm, $offset);
        }
        // Generic group — render children
        $html = '';
        if (isset($node['children'])) {
            foreach ($node['children'] as $child) {
                $html .= _konva_render_node($child, $bill_data, $px_per_mm, $offset);
            }
        }
        return $html;
    }

    // ── SPACER (invisible positioning block) ──
    if ($customType === 'spacer') {
        $x = ($attrs['x'] ?? 0) / $px_per_mm + $offL;
        $y = ($attrs['y'] ?? 0) / $px_per_mm + $offT;
        $w = ($attrs['width'] ?? 100) / $px_per_mm;
        $h = ($attrs['height'] ?? 30) / $px_per_mm;
        return '<div style="position:absolute; left:'.round($x,2).'mm; top:'.round($y,2).'mm; '
            . 'width:'.round($w,2).'mm; height:'.round($h,2).'mm;"></div>';
    }

    // ── TEXT ──
    if ($className === 'Text') {
        $x  = ($attrs['x'] ?? 0) / $px_per_mm + $offL;
        $y  = ($attrs['y'] ?? 0) / $px_per_mm + $offT;
        $w  = isset($attrs['width']) ? ($attrs['width'] / $px_per_mm) : 'auto';
        $h  = isset($attrs['height']) ? ($attrs['height'] / $px_per_mm) : null;
        $text = $attrs['text'] ?? '';
        $fontSize   = $attrs['fontSize'] ?? 14;
        $fontFamily  = $attrs['fontFamily'] ?? 'Arial';
        $fontStyle   = $attrs['fontStyle'] ?? 'normal';
        $fill        = $attrs['fill'] ?? '#000';
        $align       = $attrs['align'] ?? 'left';
        $textDecoration = $attrs['textDecoration'] ?? '';
        $textCase       = $attrs['textCase'] ?? 'none';
        $lineHeight     = $attrs['lineHeight'] ?? 1;  // Konva default is 1
        $wrap           = $attrs['wrap'] ?? 'none';

        // Substitute variables
        $text = _konva_substitute($text, $bill_data);

        // Convert font-size from px (96dpi canvas) to pt for DomPDF accuracy
        // 1px at 96dpi = 0.75pt (72dpi print standard)
        $fontSizePt = round($fontSize * 0.75, 1);

        $wStyle = is_numeric($w) ? 'width:'.round($w,2).'mm;' : '';
        // Set height if available to prevent overflow into adjacent elements, but allow it to grow if wrapping is enabled
        if ($wrap === 'word' || $wrap === 'char') {
            $hStyle = ($h !== null) ? 'min-height:'.round($h,2).'mm; height:auto; overflow:visible;' : '';
        } else {
            $hStyle = ($h !== null) ? 'height:'.round($h,2).'mm; overflow:hidden;' : '';
        }
        $fw = (strpos($fontStyle, 'bold') !== false) ? 'font-weight:bold;' : '';
        $fi = (strpos($fontStyle, 'italic') !== false) ? 'font-style:italic;' : '';
        $td = ($textDecoration === 'underline') ? 'text-decoration:underline;' : '';
        $tc = ($textCase && $textCase !== 'none') ? 'text-transform:'.$textCase.';' : '';

        // Detect if this is a single-line text (no newlines and has a width)
        $hasNewlines = strpos($text, "\n") !== false;
        if ($wrap === 'word' || $wrap === 'char') {
            $ws = 'white-space:normal; word-wrap:break-word;';
        } else {
            $ws = (!$hasNewlines && is_numeric($w)) ? 'white-space:nowrap;' : '';
        }

        return '<div style="position:absolute; left:'.round($x,2).'mm; top:'.round($y,2).'mm; '
            . $wStyle . $hStyle
            . 'font-size:'.$fontSizePt.'pt; font-family:'.$fontFamily.'; '
            . $fw . $fi . $td . $tc . $ws
            . 'color:'.$fill.'; text-align:'.$align.'; line-height:'.$lineHeight.'; overflow:visible;">'
            . nl2br(htmlspecialchars($text))
            . '</div>';
    }

    // ── RECT ──
    if ($className === 'Rect') {
        $x = ($attrs['x'] ?? 0) / $px_per_mm + $offL;
        $y = ($attrs['y'] ?? 0) / $px_per_mm + $offT;
        $w = ($attrs['width'] ?? 100) / $px_per_mm;
        $h = ($attrs['height'] ?? 100) / $px_per_mm;
        $fill   = $attrs['fill'] ?? 'transparent';
        $stroke = $attrs['stroke'] ?? '#000';
        $sw     = $attrs['strokeWidth'] ?? 1;

        return '<div style="position:absolute; left:'.round($x,2).'mm; top:'.round($y,2).'mm; '
            . 'width:'.round($w,2).'mm; height:'.round($h,2).'mm; '
            . 'background:'.$fill.'; border:'.$sw.'px solid '.$stroke.'; box-sizing:border-box;"></div>';
    }

    // ── LINE ──
    if ($className === 'Line') {
        $points = $attrs['points'] ?? [];
        if (count($points) >= 4) {
            // Line's x/y is its canvas position; points are relative offsets
            $nodeX = floatval($attrs['x'] ?? 0);
            $nodeY_line = floatval($attrs['y'] ?? 0);

            $x1 = ($nodeX + $points[0]) / $px_per_mm + $offL;
            $y1 = ($nodeY_line + $points[1]) / $px_per_mm + $offT;
            $x2 = ($nodeX + $points[2]) / $px_per_mm + $offL;
            $y2 = ($nodeY_line + $points[3]) / $px_per_mm + $offT;

            $stroke = $attrs['stroke'] ?? '#000';
            $sw     = $attrs['strokeWidth'] ?? 1;

            // Determine line orientation and render appropriately
            $lx = min($x1, $x2);
            $ly = min($y1, $y2);
            $lw = abs($x2 - $x1);
            $lh = abs($y2 - $y1);

            if ($lh < 0.3) {
                // Horizontal line
                $lh = 0;
                return '<div style="position:absolute; left:'.round($lx,2).'mm; top:'.round($ly,2).'mm; '
                    . 'width:'.round(max($lw, 0.3),2).'mm; height:0; '
                    . 'border-top:'.$sw.'px solid '.$stroke.';"></div>';
            } elseif ($lw < 0.3) {
                // Vertical line
                $lw = 0;
                return '<div style="position:absolute; left:'.round($lx,2).'mm; top:'.round($ly,2).'mm; '
                    . 'width:0; height:'.round(max($lh, 0.3),2).'mm; '
                    . 'border-left:'.$sw.'px solid '.$stroke.';"></div>';
            } else {
                // Diagonal line — approximate with a rotated div
                $angle = atan2($y2 - $y1, $x2 - $x1) * 180 / M_PI;
                $length = sqrt($lw * $lw + $lh * $lh);
                return '<div style="position:absolute; left:'.round($x1,2).'mm; top:'.round($y1,2).'mm; '
                    . 'width:'.round($length,2).'mm; height:0; '
                    . 'border-top:'.$sw.'px solid '.$stroke.'; '
                    . 'transform-origin:0 0; transform:rotate('.round($angle,2).'deg);"></div>';
            }
        }
    }
    // ── IMAGE ──
    // Supports QR codes, company logos, and other image elements.
    // The image source is stored in imageSrc or dataKey attr, which can be:
    //   1. A variable name (e.g., "qr_code_base64") → resolved from $bill_data
    //   2. A direct URL or data URI
    if ($className === 'Image') {
        $x = ($attrs['x'] ?? 0) / $px_per_mm + $offL;
        $y = ($attrs['y'] ?? 0) / $px_per_mm + $offT;
        $w = ($attrs['width'] ?? 80) / $px_per_mm;
        $h = ($attrs['height'] ?? 80) / $px_per_mm;
        $customType = $attrs['customType'] ?? '';

        $src = '';

        // ── QR Code: generate dynamically using qrUrlPath ──
        if ($customType === 'qrcode') {
            $bill_id = $bill_data['bill_id'] ?? '';
            $qrUrlPath = $attrs['qrUrlPath'] ?? 'printbill';

            // Priority 1: E-Invoice QR (already base64-encoded by the e-invoice system)
            $is_eda = $bill_data['is_eda'] ?? 0;
            $irnno  = $bill_data['irnno'] ?? '';
            $bqr    = $bill_data['qr_code_image'] ?? '';
            if ($is_eda && !empty($irnno) && !empty($bqr)) {
                $src = 'data:image/png;base64,' . $bqr;
            } elseif ($bill_id) {
                // Priority 2: Generate bill QR with custom URL path
                $CI_qr = &get_instance();
                $CI_qr->load->library('phpqrcode/qrlib');
                $qr_dir = FCPATH . 'bill_qrcode';
                if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, TRUE);

                $qr_content = base_url() . "index.php/admin_app_api/" . $qrUrlPath . "/" . $bill_id;
                $qr_file = $qr_dir . '/' . $bill_id . '_' . $qrUrlPath . '.png';
                QRcode::png($qr_content, $qr_file);

                if (file_exists($qr_file)) {
                    $src = 'data:image/png;base64,' . base64_encode(file_get_contents($qr_file));
                }
            }
        } else {
            // ── Regular Image: resolve imageSrc ──
            $src = $attrs['imageSrc'] ?? $attrs['dataKey'] ?? $attrs['src'] ?? '';

            // Three resolution modes:
            // 1. Variable name (no slashes, no colons) → look up in $bill_data
            // 2. Root-relative file path (has slashes, no colon) → convert to base64 via FCPATH
            // 3. Full URL or data URI (has colon) → use as-is
            if ($src && !preg_match('/[\/\\\\:]/', $src)) {
                $src = $bill_data[$src] ?? '';
            } elseif ($src && strpos($src, ':') === false) {
                $abs_path = FCPATH . ltrim($src, '/\\');
                if (file_exists($abs_path)) {
                    $mime = mime_content_type($abs_path);
                    $src = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($abs_path));
                } else {
                    $src = '';
                }
            }
        }

        if (empty($src)) return '';

        return '<div style="position:absolute; left:'.round($x,2).'mm; top:'.round($y,2).'mm; '
            . 'width:'.round($w,2).'mm; height:'.round($h,2).'mm;">'
            . '<img src="'.htmlspecialchars($src).'" style="width:100%; height:100%; object-fit:contain;" />'
            . '</div>';
    }

    return '';
}

/**
 * Render a data-table Group into an HTML <table>
 */
function _konva_render_table($attrs, $bill_data, $px_per_mm, $offset = ['top'=>0,'left'=>0], $items_override = null, $show_header_override = null, $show_footer_override = null)
{
    $cfgStr = isset($attrs['tableConfig']) ? $attrs['tableConfig'] : '';
    if (!$cfgStr) return '';

    $cfg = is_string($cfgStr) ? json_decode($cfgStr, true) : $cfgStr;
    if (!$cfg) return '';

    $cols  = $cfg['columns'] ?? [];
    $type  = $cfg['type'] ?? 'items';
    // Filter out hidden columns
    $cols = array_values(array_filter($cols, function($c) { return empty($c['hidden']); }));
    if (empty($cols)) return '';

    // Position (with margin offset)
    $x = ($attrs['x'] ?? 0) / $px_per_mm + $offset['left'];
    $y = ($attrs['y'] ?? 0) / $px_per_mm + $offset['top'];

    // Styling
    $bw = ($cfg['borderWidth'] ?? 1) . 'px';
    $headerBg    = $cfg['headerBg'] ?? '#fff';
    $headerColor = $cfg['headerColor'] ?? '#000';
    $headerFont  = $cfg['headerFont'] ?? 'Arial';
    $headerSize  = $cfg['headerSize'] ?? 11;
    $bodyFont    = $cfg['bodyFont'] ?? 'Arial';
    $bodySize    = $cfg['bodySize'] ?? 11;
    $bodyColor   = $cfg['bodyColor'] ?? '#333';
    $footerFont  = $cfg['footerFont'] ?? 'Arial';
    $footerSize  = $cfg['footerSize'] ?? 11;
    $cellPad     = isset($cfg['cellPadding']) ? intval($cfg['cellPadding']) : 2;
    $tblMargin   = isset($cfg['tableMargin']) ? intval($cfg['tableMargin']) : 0;

    // Border flags
    $showOuterBorder  = $cfg['showOuterBorder'] ?? true;
    $showVLines       = $cfg['showVLines'] ?? true;
    $showSideLines    = $cfg['showSideLines'] ?? true;
    $showHLinesHeader = $cfg['showHLinesHeader'] ?? true;
    $showHLinesBody   = $cfg['showHLinesBody'] ?? true;
    $showHLinesFooter = $cfg['showHLinesFooter'] ?? true;

    // Calculate total width
    $totalW = 0;
    foreach ($cols as $c) $totalW += ($c['width'] ?? 100);
    $totalW_mm = $totalW / $px_per_mm;

    // Resolve item data based on table type (or use pre-sliced override for page splitting)
    $items = ($items_override !== null) ? $items_override : _konva_get_table_items($type, $bill_data);

    // ── Evaluate computed variables per-item row ──
    // Formulas like "mc / gross_wt" reference item-level fields, not bill scalars.
    // Inject the computed result into each item so _konva_resolve_field finds it.
    $CI =& get_instance();
    $tpl_id = isset($bill_data['_template_id']) ? $bill_data['_template_id'] : 0;
    if ($tpl_id > 0) {
        $cv_list = $CI->print_template_model->get_computed_vars($tpl_id);
        if (!empty($cv_list) && !empty($items)) {
            foreach ($items as &$itm) {
                // Build per-row scalar map: item fields + bill-level scalars
                $row_data = [];
                foreach ($itm as $rk => $rv) {
                    if (is_scalar($rv) || is_null($rv)) {
                        $row_data[$rk] = str_replace(',', '', (string)$rv);
                    }
                }
                foreach ($bill_data as $bk => $bv) {
                    if (!isset($row_data[$bk]) && (is_scalar($bv) || is_null($bv))) {
                        $row_data[$bk] = str_replace(',', '', (string)$bv);
                    }
                }
                // Evaluate each computed var in the row context
                foreach ($cv_list as $cv) {
                    $expr = preg_replace('/\{\{(\w+)\}\}/', '$1', $cv['formula']);
                    $result = _tpl_eval_expr($expr, $row_data);
                    if ($result !== null) {
                        $itm[$cv['var_name']] = number_format((float)$result, 2);
                        $row_data[$cv['var_name']] = (string)$result;
                    }
                }
            }
            unset($itm); // break reference
        }
    }

    // Calculate design height for early returns/collapsed tables
    $designPad = isset($cfg['cellPadding']) ? intval($cfg['cellPadding']) : 2;
    $designRowH = $bodySize + $designPad * 2 + 4;
    $designSampleRows = (isset($attrs['customType']) && $attrs['customType'] === 'static-table')
        ? (isset($cfg['recordSize']) ? intval($cfg['recordSize']) : 3)
        : 1;
    $hasHeader = ($show_header_override !== null) ? (bool)$show_header_override : (($cfg['showHeader'] ?? true) !== false);
    $hasFooter = ($show_footer_override !== null) ? (bool)$show_footer_override : !empty($cfg['showFooter']);
    $designH = ($hasHeader ? $designRowH : 0) + $designSampleRows * $designRowH + ($hasFooter ? $designRowH : 0);

    // Suppress conditional tables when no data exists, but render a hidden container
    // so the JS reflow can measure it as 0px and correctly apply collapse shifts.
    if ($type === 'tax_detail_breakdown' && empty($items)) {
        return '<div data-design-h="'.round($designH, 2).'" data-table-type="'.$type.'" data-node-y="'.round($attrs['y'] ?? 0, 2).'" style="position:absolute; left:'.round($x,2).'mm; top:'.round($y,2).'mm; width:'.round($totalW_mm,2).'mm; display:none;">'
            . '<table style="display:none;"></table>'
            . '</div>';
    }

    $conditional_types = [
        'advance_amount', 'order_advance', 'chit_adjustment', 'receipt_adjustment',
        'repair_order', 'credit_collection', 'chit_pre_close', 'payment_methods', 'payment_breakdown'
    ];
    if (in_array($type, $conditional_types) && empty($items)) {
        return '<div data-design-h="'.round($designH, 2).'" data-table-type="'.$type.'" data-node-y="'.round($attrs['y'] ?? 0, 2).'" style="position:absolute; left:'.round($x,2).'mm; top:'.round($y,2).'mm; width:'.round($totalW_mm,2).'mm; display:none;">'
            . '<table style="display:none;"></table>'
            . '</div>';
    }

    // Build border style helpers
    $borderNone = 'border:none;';
    $borderV = $showVLines ? 'border-left:'.$bw.' solid #000; border-right:'.$bw.' solid #000;' : '';
    $cellBorderV = $showVLines ? 'border-left:'.$bw.' solid #000;' : '';
    $outerBorder = $showOuterBorder ? 'border:'.$bw.' solid #000;' : '';

    // Start HTML (apply tableMargin if set)
    $marginStyle = $tblMargin > 0 ? 'margin:'.$tblMargin.'px;' : '';
    $designH = ($hasHeader ? $designRowH : 0) + $designSampleRows * $designRowH + ($hasFooter ? $designRowH : 0);
    // Use canvas group height — matches what user designed around
    $canvasGroupH = floatval($attrs['height'] ?? 0);
    if ($canvasGroupH > 0) {
        $designH = $canvasGroupH;
    }

    $html = '<div data-design-h="'.round($designH, 2).'" data-table-type="'.$type.'" style="position:absolute; left:'.round($x,2).'mm; top:'.round($y,2).'mm; width:'.round($totalW_mm,2).'mm; '.$marginStyle.'">';
    $html .= '<table style="width:100%; border-collapse:collapse; '.($showOuterBorder ? 'border:'.$bw.' solid #000;' : '').'">';

    // ── HEADER ── (default to true if not explicitly false; suppressed on continuation pages)
    $showHeader = $hasHeader;
    if ($showHeader !== false) {
        $html .= '<thead><tr style="background:'.$headerBg.'; color:'.$headerColor.';">';
        $skipH = 0;
        foreach ($cols as $i => $col) {
            if ($skipH > 0) { $skipH--; continue; }
            $align = $col['headerAlign'] ?? 'center';
            $wPx   = $col['width'] ?? 100;
            $hcs   = min(intval($col['headerColspan'] ?? 1), count($cols) - $i);
            if ($hcs < 1) $hcs = 1;

            // Accumulate width of spanned columns
            $spanW = $wPx;
            for ($si = 1; $si < $hcs; $si++) {
                $spanW += ($cols[$i + $si]['width'] ?? 100);
            }
            if ($hcs > 1) $skipH = $hcs - 1;

            $bStyle = '';
            if ($showVLines && $i > 0) $bStyle .= 'border-left:'.$bw.' solid #000;';
            if ($showHLinesHeader) $bStyle .= 'border-top:'.$bw.' solid #000; border-bottom:'.$bw.' solid #000;';
            if (!$showSideLines && ($i === 0)) $bStyle .= 'border-left:none;';
            if (!$showSideLines && ($i + $hcs >= count($cols))) $bStyle .= 'border-right:none;';

            $csAttr = $hcs > 1 ? ' colspan="'.$hcs.'"' : '';
            $html .= '<th'.$csAttr.' style="padding:'.$cellPad.'px; text-align:'.$align.'; '
                . 'font-family:'.$headerFont.'; font-size:'.$headerSize.'px; '
                . 'width:'.$spanW.'px; '.$bStyle.'">'
                . htmlspecialchars($col['header'] ?? '')
                . '</th>';
        }
        $html .= '</tr></thead>';
    }

    // ── BODY ──
    $hasSubRows     = !empty($cfg['hasSubRows']);
    $subRowCols     = [];
    $subBodyFont    = $bodyFont;
    $subBodySize    = $bodySize;
    $subBodyColor   = $cfg['subBodyColor'] ?? $bodyColor;
    if ($hasSubRows && !empty($cfg['subRowColumns'])) {
        $subRowCols = array_values(array_filter($cfg['subRowColumns'], function($c) { return empty($c['hidden']); }));
        $subBodyFont  = $cfg['subBodyFont'] ?? $bodyFont;
        $subBodySize  = $cfg['subBodySize'] ?? $bodySize;
        $subBodyColor = $cfg['subBodyColor'] ?? $bodyColor;
    }

    $html .= '<tbody>';
    // ── Exchange reference row ──
    if (in_array($type, ['exchange', 'return']) && !empty($bill_data['exchange_ref_bill_no'])) {
        $html .= '<tr style="font-weight:bold; text-transform:uppercase;">'
            . '<td colspan="'.count($cols).'" style="padding:'.$cellPad.'px; font-family:'.$bodyFont.'; font-size:'.$bodySize.'px;">'
            . 'Exchange (Refer sale bill no : ' . htmlspecialchars($bill_data['exchange_ref_bill_no']) . ')'
            . '</td></tr>';
    }
    if (empty($items)) {
        $html .= '<tr><td colspan="'.count($cols).'" style="padding:'.$cellPad.'px; text-align:center; color:#999;">No records</td></tr>';
    } else {
        foreach ($items as $rowIdx => $item) {
            // ── Main item row ──
            $html .= '<tr>';
            $skipB = 0;
            foreach ($cols as $ci => $col) {
                if ($skipB > 0) { $skipB--; continue; }
                $field = $col['field'] ?? '';
                $align = $col['align'] ?? 'left';
                $bcs   = min(intval($col['bodyColspan'] ?? 1), count($cols) - $ci);
                if ($bcs < 1) $bcs = 1;

                $cellVal = _konva_resolve_field($field, $item, $bill_data);

                if ($bcs > 1) $skipB = $bcs - 1;

                $bStyle = '';
                if ($showVLines && $ci > 0) $bStyle .= 'border-left:'.$bw.' solid #000;';
                if ($showHLinesBody && $rowIdx < count($items)-1) $bStyle .= 'border-bottom:1px solid #ccc;';
                if (!$showSideLines && ($ci === 0)) $bStyle .= 'border-left:none;';
                if (!$showSideLines && ($ci + $bcs >= count($cols))) $bStyle .= 'border-right:none;';

                $csAttr = $bcs > 1 ? ' colspan="'.$bcs.'"' : '';
                $html .= '<td'.$csAttr.' style="padding:'.$cellPad.'px; text-align:'.$align.'; '
                    . 'font-family:'.$bodyFont.'; font-size:'.$bodySize.'px; '
                    . 'color:'.$bodyColor.'; '.$bStyle.'">'
                    . htmlspecialchars($cellVal)
                    . '</td>';
            }
            $html .= '</tr>';

            // ── Sub-rows (stone details etc.) ──
            if ($hasSubRows && !empty($subRowCols) && !empty($item['_sub_rows'])) {
                foreach ($item['_sub_rows'] as $subItem) {
                    $html .= '<tr>';
                    foreach ($cols as $ci => $col) {
                        $subCol = isset($subRowCols[$ci]) ? $subRowCols[$ci] : null;
                        $subField = $subCol ? ($subCol['field'] ?? '') : '';
                        $subAlign = $subCol ? ($subCol['align'] ?? 'left') : 'left';

                        $subVal = '';
                        if (!empty($subField)) {
                            $subVal = _konva_resolve_field($subField, $subItem, $bill_data);
                        }

                        $bStyle = '';
                        if ($showVLines && $ci > 0) $bStyle .= 'border-left:'.$bw.' solid #000;';
                        if (!$showSideLines && ($ci === 0)) $bStyle .= 'border-left:none;';
                        if (!$showSideLines && ($ci === count($cols)-1)) $bStyle .= 'border-right:none;';

                        $html .= '<td style="padding:'.$cellPad.'px; text-align:'.$subAlign.'; '
                            . 'font-family:'.$subBodyFont.'; font-size:'.$subBodySize.'px; '
                            . 'color:'.$subBodyColor.'; '.$bStyle.'">'
                            . htmlspecialchars($subVal)
                            . '</td>';
                    }
                    $html .= '</tr>';
                }
            }
        }
    }
    $html .= '</tbody>';

    // ── FOOTER ── (suppressed on non-final pages during split)
    if ($hasFooter) {
        $footerLabel = $cfg['footerLabel'] ?? 'Total';

        $html .= '<tfoot><tr style="background:#f5f5f5; font-weight:bold;">';

        // Per-column footer colspan rendering
        $skipF = 0;
        for ($fi = 0; $fi < count($cols); $fi++) {
            if ($skipF > 0) { $skipF--; continue; }
            $col = $cols[$fi];
            $fcs = min(intval($col['footerColspan'] ?? 1), count($cols) - $fi);
            if ($fcs < 1) $fcs = 1;
            if ($fcs > 1) $skipF = $fcs - 1;

            $footerField = $col['footerField'] ?? '';
            $align = $col['align'] ?? 'right';
            $fVal = '';

            if (!empty($footerField)) {
                $fVal = _konva_resolve_field($footerField, [], $bill_data);
            }

            $fBStyle = '';
            if ($showVLines && $fi > 0) $fBStyle .= 'border-left:'.$bw.' solid #000;';
            if ($showHLinesFooter) $fBStyle .= 'border-top:'.$bw.' solid #000; border-bottom:'.$bw.' solid #000;';
            if (!$showSideLines && ($fi === 0)) $fBStyle .= 'border-left:none;';
            if (!$showSideLines && ($fi + $fcs >= count($cols))) $fBStyle .= 'border-right:none;';

            $csAttr = $fcs > 1 ? ' colspan="'.$fcs.'"' : '';
            $html .= '<td'.$csAttr.' style="padding:'.$cellPad.'px; text-align:'.$align.'; '
                . 'font-family:'.$footerFont.'; font-size:'.$footerSize.'px; '
                . $fBStyle.'">'
                . htmlspecialchars($fVal)
                . '</td>';
        }
        $html .= '</tr></tfoot>';
    }

    $html .= '</table></div>';
    return $html;
}

/**
 * Get the correct item array based on table type
 */
function _konva_get_table_items($type, $bill_data)
{
    switch ($type) {
        case 'items':
        case 'sales':
            return $bill_data['sales_items'] ?? $bill_data['items'] ?? [];
        case 'old_gold':
            return $bill_data['old_metal_items'] ?? [];
        case 'stone_details':
            return $bill_data['stone_items'] ?? [];
        case 'exchange':
        case 'return':
            return $bill_data['return_items'] ?? [];
        case 'karigar':
            return $bill_data['transfer_items'] ?? [];
        case 'tax_summary':
            // Build a simple tax summary from mapped data
            $rows = [];
            if (!empty($bill_data['cgst_amount']) && $bill_data['cgst_amount'] !== '0.00') {
                $rows[] = ['description' => 'CGST', 'amount' => $bill_data['cgst_amount']];
            }
            if (!empty($bill_data['sgst_amount']) && $bill_data['sgst_amount'] !== '0.00') {
                $rows[] = ['description' => 'SGST', 'amount' => $bill_data['sgst_amount']];
            }
            if (!empty($bill_data['igst_amount']) && $bill_data['igst_amount'] !== '0.00') {
                $rows[] = ['description' => 'IGST', 'amount' => $bill_data['igst_amount']];
            }
            return $rows;
        // -- Template 369 Table Types --
        // Advance Amount table: date + amount rows from advance_details
        // Matches bill_format_2.php lines 2527-2638
        case 'advance_amount':
            return $bill_data['advance_details'] ?? [];
        // Order Advance Amount table: date + rate + weight + amount rows from order_adj
        // Matches bill_format_2.php lines 3478-3544
        case 'order_advance':
            return $bill_data['order_advance_entries'] ?? [];
        // Chit Adjustment table: sno + ref_no + amount rows from chit_details
        // Matches bill_format_2.php lines 2466-2507
        case 'chit_adjustment':
            return $bill_data['chit_general_items'] ?? [];
        // Receipt Adjustment table: receipt_no, date, amounts from receiptDetails
        // Matches bill_format_2.php lines 3559-3596
        case 'receipt_adjustment':
            return $bill_data['receipt_adjustment_items'] ?? [];
        // Order Delivery Items table: same as sales items but for bill_type=5
        // Matches bill_format_2.php lines 1639-1700
        case 'order_delivery':
            return $bill_data['order_delivery_items'] ?? $bill_data['sales_items'] ?? [];
        // Repair Order Details table: sno, description, weight, completed_wt, amount
        // Matches bill_format_2.php lines 2259-2280
        case 'repair_order':
            return $bill_data['repair_order_items'] ?? [];
        // Credit Collection History: label, date, amount
        // Matches bill_format_2.php lines 2418-2430
        case 'credit_collection':
            return $bill_data['credit_collection_history'] ?? [];
        // Chit Pre-Close Items: sno, ref_no, amount (bill_type=10 variant)
        // Matches bill_format_2.php lines 680-698
        case 'chit_pre_close':
            return $bill_data['chit_pre_close_items'] ?? [];
        // Purchase Items: purchase_sno, description, qty, gross_wt, net_wt, rate, amount
        // Matches bill_format_2.php lines 2098-2130
        case 'purchase':
            return $bill_data['purchase_items'] ?? [];
        // Tax Detail Breakdown: HSN-wise tax split table
        // Matches bill_format_2.php lines 2688-2730
        case 'tax_detail_breakdown':
            return $bill_data['tax_detail_items'] ?? [];
        case 'payment_methods':
            return $bill_data['payment_methods'] ?? [];
        case 'payment_breakdown':
            return $bill_data['payment_breakdown'] ?? [];
        default:
            return $bill_data['items'] ?? [];
    }
}

/**
 * Resolve a {{field}} from an item row, falling back to bill_data
 */
function _konva_resolve_field($field, $item, $bill_data)
{
    if (empty($field)) return '';

    // Static literal: values wrapped in single quotes render as-is (e.g. 'Total' → Total)
    $trimmed = trim($field);
    if (strlen($trimmed) >= 2 && $trimmed[0] === "'" && substr($trimmed, -1) === "'") {
        return substr($trimmed, 1, -1);
    }

    // If it's a {{variable}} pattern
    if (preg_match('/^\{\{(\w+)\}\}$/', trim($field), $m)) {
        $key = $m[1];
        // First check item row
        if (isset($item[$key])) return $item[$key];
        // Then check bill_data
        if (isset($bill_data[$key])) {
            $v = $bill_data[$key];
            return is_array($v) ? '' : $v;
        }
        return '';
    }

    // Bare field name without {{}} (e.g. "item_total" instead of "{{item_total}}")
    // — the designer may store column fields without braces
    $bare = trim($field);
    if (preg_match('/^\w+$/', $bare)) {
        if (isset($item[$bare])) return $item[$bare];
        if (isset($bill_data[$bare])) {
            $v = $bill_data[$bare];
            return is_array($v) ? '' : $v;
        }
        // Not found in any data source — return empty, not the literal name
        return '';
    }

    // Could be a mix: "INV-{{bill_no}}"
    return _konva_substitute($field, array_merge($bill_data, $item));
}

/**
 * Replace all {{key}} placeholders in a string
 */
function _konva_substitute($text, $data)
{
    if (!$data || !is_string($text)) return $text;

    return preg_replace_callback('/\{\{(.+?)\}\}/', function($matches) use ($data) {
        $key = trim($matches[1]);
        if (isset($data[$key])) {
            $v = $data[$key];
            return is_array($v) ? '' : $v;
        }
        // Variable not found in data — show empty instead of raw {{placeholder}}
        return '';
    }, $text);
}

/**
 * Auto-sum a column from item rows (for footer)
 */
function _konva_auto_sum($col, $items)
{
    $field = $col['field'] ?? '';
    if (empty($field)) return '';

    // Extract key from {{key}}
    if (preg_match('/^\{\{(\w+)\}\}$/', trim($field), $m)) {
        $key = $m[1];
    } else {
        return '';
    }

    // Check if this column is numeric-summable
    $numericFields = ['amount','qty','pcs','gross_wt','net_wt','rate','mc',
        'taxable_amount','cgst_amt','sgst_amt','igst_amt',
        'old_metal_amount','return_amount',
        'stone_pieces','stone_wt','stone_amount',
        'return_stone_wt','return_stone_amount',
        'old_metal_stone_wt','old_metal_stone_amount','dust_wt','old_metal_dust_wt'];

    if (!in_array($key, $numericFields)) return '';

    $sum = 0;
    foreach ($items as $item) {
        if (isset($item[$key])) {
            $v = str_replace(',', '', $item[$key]);
            $n = floatval($v);
            if (!is_nan($n)) $sum += $n;
        }
    }

    // Format based on field type
    if (in_array($key, ['qty','pcs'])) return number_format($sum, 0);
    if (in_array($key, ['gross_wt','net_wt'])) return number_format($sum, 3);
    return number_format($sum, 2);
}

/**
 * Find the active V2 Konva template for a given bill type / branch
 *
 * @param  int       $bill_type_id
 * @param  int|null  $branch_id
 * @return array|false  Template row or false
 */
function find_konva_template($bill_type_id, $branch_id = null)
{
    $CI =& get_instance();
    $CI->load->model('print_template_model');

    // bill_type directly maps to template_category (numeric IDs)
    $category = (int)$bill_type_id;

    // Try specific category first, then fallback to Common (0)
    $categories_to_try = [$category];
    if ($category !== 0) {
        $categories_to_try[] = 0; // Common/global fallback
    }

    foreach ($categories_to_try as $cat) {
        // Build query using raw WHERE to avoid CI2 compatibility issues
        $where = "template_category = " . (int)$cat . " AND is_active = 1";
        if ($branch_id) {
            $where .= " AND (id_branch = " . (int)$branch_id . " OR id_branch IS NULL)";
        }
        $sql = "SELECT * FROM print_templates WHERE $where ORDER BY " 
             . ($branch_id ? "id_branch DESC, " : "")
             . "id_template DESC LIMIT 1";
        
        $query = $CI->db->query($sql);
        $template = $query->row_array();

        if ($template) {
            // Verify it has Konva data
            $gjs = json_decode($template['gjs_data'], true);
            if ($gjs && isset($gjs['konva_json'])) {
                return $template;
            }
        }
    }

    return false;
}
