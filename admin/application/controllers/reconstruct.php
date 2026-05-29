<?php
    /**
     * Compile a single block into HTML based on its type and settings.
     */
    private function compile_single_block($type, $settings, $font_family, $template_data = []) {
        // Use custom HTML from GrapesJS if available
        if (!empty($settings['custom_html'])) {
            return $this->parse_dynamic_variables($settings['custom_html'], $template_data);
        }

        $fs = isset($settings['font_size']) ? $settings['font_size'] : '12px';
        
        switch ($type) {
            
            case 'company-header':
                return $this->compile_company_header($settings, $fs);
            
            case 'customer-info':
                return $this->compile_customer_info($settings, $fs);
            
            case 'items-table':
                return $this->compile_items_table($settings, $fs);
            
            case 'purchase-table':
                return $this->compile_purchase_table($settings, $fs);
            
            case 'sales-return-table':
                return $this->compile_sales_return_table($settings, $fs);
            
            case 'credit-collection-table':
                return $this->compile_credit_collection_table($settings, $fs);

            case 'chit-preclose-table':
                return $this->compile_chit_preclose_table($settings, $fs);
            
            case 'advance-payment':
                return $this->compile_advance_payment($settings, $fs);
            
            case 'tax-summary':
                return $this->compile_tax_summary($settings, $fs);
            
            case 'payment-info':
                return $this->compile_payment_info($settings, $fs);
            
            case 'remark-block':
                return $this->compile_remark_block($settings, $fs);
            
            case 'signature-block':
                return $this->compile_signature_block($settings);
            
            case 'amount-words':
                return $this->compile_amount_words($settings, $fs);

            case 'custom-text':
                // For regular custom-text, we shouldn't necessarily parse variables, but it could be useful
                return $this->compile_custom_text($settings, $fs);

            case 'custom-saved-block':
                $html = isset($settings['content']) ? $settings['content'] : '';
                
                // --- Smart Placeholder Injection ---
                if (strpos($html, 'data-type="invoice-items"') !== false) {
                    $items_html = $this->compile_legacy_invoice_items($template_data, $fs);
                    // Regex to find the placeholder div and replace it entirely
                    $html = preg_replace('/<div[^>]*class="[^"]*smart-placeholder[^"]*"[^>]*data-type="invoice-items"[^>]*>.*?<\/div>/is', $items_html, $html);
                }

                return $this->parse_dynamic_variables($html, $template_data);

            case 'spacer':
                return $this->compile_spacer($settings);

            case 'page-break':
                return $this->compile_page_break();
            
            default:
                return '<!-- Unknown block type: ' . htmlspecialchars($type) . ' -->';
        }
    }

    /**
     * Parses {{variable.name}} placeholders in HTML and replaces them with data.
     * 
     * @param string $html The raw HTML from GrapesJS
     * @param array $data The data array (typically contains 'billing', 'comp_details', etc.)
     * @return string The processed HTML
     */
    private function parse_dynamic_variables($html, $data) {
        if (empty($html) || empty($data)) {
            return $html;
        }

        // We expect data to heavily rely on the 'billing' array for receipts
        $billing = isset($data['billing']) ? $data['billing'] : [];
        $comp = isset($data['comp_details']) ? $data['comp_details'] : [];
        
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', function($matches) use ($billing, $comp, $data) {
            $path = $matches[1]; // e.g., 'customer.name' or 'invoice_no'
            
            // Map specific dot-notation variable requests to our legacy array structures
            switch ($path) {
                // Customer context
                case 'customer.name': return isset($billing['customer_name']) ? htmlspecialchars($billing['customer_name']) : '';
                case 'customer.phone': return isset($billing['mobile']) ? htmlspecialchars($billing['mobile']) : '';
                case 'customer.address1': return isset($billing['address1']) ? htmlspecialchars($billing['address1']) : '';
                case 'customer.address2': return isset($billing['address2']) ? htmlspecialchars($billing['address2']) : '';
                case 'customer.city': return isset($billing['city']) ? htmlspecialchars($billing['city']) : '';
                case 'customer.pincode': return isset($billing['pincode']) ? htmlspecialchars($billing['pincode']) : '';
                case 'customer.pan': return isset($billing['pan_no']) ? htmlspecialchars($billing['pan_no']) : '';
                case 'customer.gst': return isset($billing['gst_number']) ? htmlspecialchars($billing['gst_number']) : '';
                case 'customer.aadhar': return isset($billing['adhar_no']) ? htmlspecialchars($billing['adhar_no']) : '';

                // Invoice Context
                case 'invoice.no': 
                    // Use the mapped invoice_no if available, otherwise fallback
                    return isset($data['invoice_no']) ? htmlspecialchars($data['invoice_no']) : (isset($billing['bill_no']) ? htmlspecialchars($billing['bill_no']) : '');
                case 'invoice.date': return isset($billing['bill_date']) ? htmlspecialchars($billing['bill_date']) : '';
                case 'invoice.time': return isset($billing['bill_time']) ? htmlspecialchars($billing['bill_time']) : '';
                case 'invoice.type': return isset($billing['is_eda']) && $billing['is_eda'] == 1 ? 'TAX INVOICE' : 'PROFORMA';
                
                // Company / Shop context
                case 'shop.name': return isset($comp['company_name']) ? htmlspecialchars($comp['company_name']) : '';
                case 'shop.gst':  return isset($comp['gst_number']) ? htmlspecialchars($comp['gst_number']) : '';
                case 'shop.pan':  return isset($comp['pan_number']) ? htmlspecialchars($comp['pan_number']) : '';
                
                // Live Rates
                case 'rate.gold22': return isset($billing['goldrate_22ct']) ? number_format($billing['goldrate_22ct'], 2) : '';
                case 'rate.gold18': return isset($billing['goldrate_18ct']) ? number_format($billing['goldrate_18ct'], 2) : '';
                case 'rate.silver': return isset($billing['silverrate_1gm']) ? number_format($billing['silverrate_1gm'], 2) : '';
                
                // Totals
                case 'totals.grand_total': return isset($billing['tot_bill_amount']) ? number_format($billing['tot_bill_amount'], 2) : '0.00';
                case 'totals.amount_received': return isset($billing['tot_amt_received']) ? number_format($billing['tot_amt_received'], 2) : '0.00';
                
                default:
                    // --- Generic Dot-Notation Array Traversal ---
                    // Enables access to deeply nested variables like {{payment.0.payment_amount}}
                    $keys = explode('.', $path);
                    $current = $data;
                    $found = true;

                    foreach ($keys as $key) {
                        if (is_array($current) && array_key_exists($key, $current)) {
                            $current = $current[$key];
                        } elseif (is_object($current) && isset($current->$key)) {
                            $current = $current->$key;
                        } else {
                            $found = false;
                            break;
                        }
                    }

                    if ($found) {
                        if (is_scalar($current)) {
                            return htmlspecialchars($current ?? '');
                        }
                    }

                    // Fallback: Check if it's a direct array key in $billing
                    if (isset($billing[$path]) && is_scalar($billing[$path])) {
                        return htmlspecialchars($billing[$path]);
                    }
                    // Return the placeholder intact if we don't know it, so the user knows it failed to map
                    return $matches[0];
            }
        }, $html);
    }

    /**
     * Renders a basic, standard item table HTML block.
     * In a full implementation, this might read from legacy helpers or views, 
     * but here it's generated dynamically to completely bypass GrapesJS logic.
     */
    private function compile_legacy_invoice_items($template_data, $fs) {
        $items = isset($template_data['items']) ? $template_data['items'] : [];
        if (empty($items)) {
            return '<div style="padding: 10px; border: 1px dashed #ccc; text-align: center;">No Items Found</div>';
        }

        $html = '<table style="width: 100%; border-collapse: collapse; font-size: ' . $fs . '; margin-top: 10px;">';
        
        // Header
        $html .= '<thead><tr style="border-bottom: 2px solid #000; border-top: 1px solid #000; font-weight: bold; text-align: left;">';
        $html .= '<th style="padding: 5px;">S.No</th>';
        $html .= '<th style="padding: 5px;">Description</th>';
        $html .= '<th style="padding: 5px;">HSN</th>';
        $html .= '<th style="padding: 5px;">Purity</th>';
        $html .= '<th style="padding: 5px; text-align: right;">Gross Wt</th>';
        $html .= '<th style="padding: 5px; text-align: right;">Net Wt</th>';
        $html .= '<th style="padding: 5px; text-align: center;">Qty</th>';
        $html .= '<th style="padding: 5px; text-align: right;">Rate</th>';
        $html .= '<th style="padding: 5px; text-align: right;">Amount</th>';
        $html .= '</tr></thead>';

        // Body
        $html .= '<tbody>';
        foreach ($items as $item) {
            $html .= '<tr style="border-bottom: 1px solid #eee;">';
            $html .= '<td style="padding: 5px;">' . htmlspecialchars($item['sno'] ?? '') . '</td>';
            $html .= '<td style="padding: 5px;">' . htmlspecialchars($item['description'] ?? '') . '</td>';
            $html .= '<td style="padding: 5px;">' . htmlspecialchars($item['hsn_code'] ?? '') . '</td>';
            $html .= '<td style="padding: 5px;">' . htmlspecialchars($item['purity'] ?? '') . '</td>';
            $html .= '<td style="padding: 5px; text-align: right;">' . htmlspecialchars($item['gross_wt'] ?? '') . '</td>';
            $html .= '<td style="padding: 5px; text-align: right;">' . htmlspecialchars($item['net_wt'] ?? '') . '</td>';
            $html .= '<td style="padding: 5px; text-align: center;">' . htmlspecialchars($item['qty'] ?? '') . '</td>';
            $html .= '<td style="padding: 5px; text-align: right;">' . htmlspecialchars($item['rate'] ?? '') . '</td>';
            $html .= '<td style="padding: 5px; text-align: right;">' . htmlspecialchars($item['amount'] ?? '') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }

    // ── Legacy Layout Compilers ──
    private function compile_company_header($s, $fs) {
        $align = isset($s['text_align']) ? $s['text_align'] : 'center';
        $border = (!isset($s['border_bottom']) || $s['border_bottom']) ? 'border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:10px;' : '';
        $show_logo = isset($s['show_logo']) ? $s['show_logo'] : true;
        
        $html = '<div class="print-header" style="text-align:' . $align . '; margin:-20px 0 0 0; font-size:' . $fs . '; ' . $border . '">';

        if ($show_logo) {
            $html .= '<img src="{{company_logo_url}}" style="width:auto;height:50px;">';
        }

        // New fields-based rendering mapped to legacy structure
        if (isset($s['fields']) && is_array($s['fields'])) {
            $html .= '<div style="font-size:20px; font-weight:bold; margin-top:5px;">{{company_name}}</div>';
            $html .= '<div style="font-size:12px;"><strong>{{company_address}} | {{company_city}} - {{company_pincode}} | Phone : {{company_mobile}}</strong><br></div>';
            $html .= '<div style="font-size:12px;"><strong>GSTIN : {{company_gstin}} | PAN : {{company_pan}}</strong><br></div>';
        } else {
            // Backward compatibility
            $html .= '<div style="font-size:20px; font-weight:bold; margin-top:5px;">{{company_name}}</div>';
            $html .= '<div style="font-size:12px;"><strong>{{company_address}} | Phone : {{company_mobile}}</strong><br></div>';
            if (isset($s['show_gstin']) && $s['show_gstin']) {
                $html .= '<div style="font-size:12px;"><strong>GSTIN : {{company_gstin}}</strong><br></div>';
            }
        }

        $html .= '</div>';
        return $html;
    }

    private function compile_customer_info($s, $fs) {
        $layout = isset($s['layout']) ? $s['layout'] : 'two-column';
        $isTwoCol = ($layout === 'two-column');
        
        $html = '<table style="width:100%; border-collapse:collapse; margin-top:0px; font-size: ' . $fs . '; font-weight: bold; margin-bottom: 10px;">';
        $html .= '<tr>';
        
        // Customer side
        $html .= '<td style="width:33%; border:none; vertical-align:top; border-right:2px solid black; padding-right:10px;">';
        $html .= '<table style="width:100%; line-height: 1.4;">';
        $html .= '<tr><td><span style="font-weight:bold; font-size:14px; text-transform:uppercase">{{customer_name}}</span></td></tr>';
        $html .= '<tr><td>{{customer_address}}</td></tr>';
        $html .= '<tr><td>{{customer_city}} - {{customer_pincode}}</td></tr>';
        $html .= '<tr><td>Mobile : {{customer_mobile}}</td></tr>';
        $html .= '<tr><td>GSTIN : {{customer_gstin}}</td></tr>';
        $html .= '<tr><td>PAN : {{customer_pan}}</td></tr>';
        $html .= '</table>';
        $html .= '</td>';
        
        // Placeholder for QR
        $html .= '<td style="width:33%; border:none; vertical-align:top; text-align:center;"></td>';
        
        // Invoice side
        $html .= '<td style="width:34%; border:none; vertical-align:top; border-left:2px solid black; padding-left:10px;">';
        $html .= '<table style="width:100%; line-height: 1.4;">';
        $html .= '<tr><td style="width:40%;">Invoice No</td><td style="width:5%;">:</td><td style="width:55%;">{{invoice_no}}</td></tr>';
        $html .= '<tr><td>Date</td><td>:</td><td>{{bill_date}}</td></tr>';
        $html .= '<tr><td>Time</td><td>:</td><td>{{bill_time}}</td></tr>';
        
        if (!isset($s['show_gold_rate']) || $s['show_gold_rate']) {
             $html .= '<tr><td>GOLD 22-KT</td><td>:</td><td>{{gold_rate}}/GM</td></tr>';
        }
        if (!isset($s['show_gold_18kt']) || $s['show_gold_18kt']) {
             $html .= '<tr><td>GOLD 18-KT</td><td>:</td><td>{{gold_rate_18ct}}/GM</td></tr>';
        }
        if (!isset($s['show_silver_rate']) || $s['show_silver_rate']) {
             $html .= '<tr><td>SILVER</td><td>:</td><td>{{silver_rate}}/GM</td></tr>';
        }
        $html .= '</table>';
        $html .= '</td>';
        
        $html .= '</tr>';
        $html .= '</table>';
        
        return $html;
    }
