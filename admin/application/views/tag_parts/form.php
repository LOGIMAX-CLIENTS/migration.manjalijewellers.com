<!-- Content Wrapper. Contains page content -->

<style>
    /* ========== PARTLY SALE FORM — 2-Column, Flat Design ========== */
    .ts-page .content-header { display: none; }
    .ts-page .content { padding: 0 !important; }

    /* Full page container */
    .ts-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 50px);
        overflow: hidden;
    }

    /* Header bar — flat dark */
    .ts-header {
        background: #2c3e50;
        color: #fff;
        padding: 8px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .ts-header h3 { margin: 0; font-weight: 700; font-size: 15px; letter-spacing: 0.3px; }
    .ts-header h3 i { margin-right: 8px; opacity: 0.7; }
    .ts-header .ts-back {
        color: rgba(255,255,255,0.6);
        font-size: 13px;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,0.25);
        padding: 4px 14px;
        border-radius: 3px;
        transition: all 0.15s;
    }
    .ts-header .ts-back:hover { background: rgba(255,255,255,0.1); color: #fff; }

    /* Two-column body */
    .ts-body {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* LEFT PANEL */
    .ts-left {
        width: 340px;
        min-width: 300px;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        background: #f7f9fb;
        flex-shrink: 0;
    }

    /* Scan section */
    .ts-scan-section {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }
    .ts-scan-section label {
        font-weight: 700;
        font-size: 11px;
        color: #6b7a8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
    }
    .ts-scan-section .input-group { width: 100%; }
    .ts-scan-section .input-group .form-control {
        height: 36px;
        font-size: 14px;
        border: 1.5px solid #d0d7e0;
        border-radius: 3px 0 0 3px;
    }
    .ts-scan-section .input-group .form-control:focus {
        border-color: #4a90d9;
        box-shadow: 0 0 0 2px rgba(74,144,217,0.1);
    }
    .ts-scan-section .input-group-btn .btn {
        height: 36px;
        padding: 0 16px;
        font-weight: 700;
        font-size: 12px;
        border-radius: 0 3px 3px 0;
        background: #4a90d9;
        border-color: #4a90d9;
        color: #fff;
    }
    .ts-scan-section .input-group-btn .btn:hover { background: #3a7bc8; }
    .ts-scan-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 3px;
        margin-top: 8px;
        display: none;
    }
    .ts-scan-error i { margin-right: 4px; }

    /* Tag details section */
    .ts-details-section {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        flex: 1;
        overflow-y: auto;
    }
    .ts-details-section .ts-section-title {
        font-weight: 700;
        font-size: 11px;
        color: #6b7a8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .ts-details-section .ts-section-title i { margin-right: 5px; color: #4a90d9; }
    .ts-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 12px;
    }
    .ts-detail-item {
        display: flex;
        flex-direction: column;
    }
    .ts-detail-item .ts-dlbl {
        font-size: 10px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .ts-detail-item .ts-dval {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }
    .ts-detail-placeholder {
        color: #94a3b8;
        font-size: 13px;
        font-style: italic;
        padding: 20px 0;
        text-align: center;
    }

    /* Parts config section */
    .ts-config-section {
        padding: 14px 16px;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .ts-config-section .ts-section-title {
        font-weight: 700;
        font-size: 11px;
        color: #6b7a8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }
    .ts-config-section .ts-section-title i { margin-right: 5px; color: #4a90d9; }
    .ts-config-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .ts-config-row label {
        font-weight: 600;
        font-size: 12px;
        color: #475569;
        margin: 0;
        white-space: nowrap;
    }
    .ts-config-row .parts-count-input {
        width: 60px;
        height: 34px;
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        border: 1.5px solid #d0d7e0;
        border-radius: 3px;
    }
    .ts-config-row .parts-count-input:focus {
        border-color: #4a90d9;
        box-shadow: 0 0 0 2px rgba(74,144,217,0.1);
        outline: none;
    }
    .ts-config-btns {
        display: flex;
        gap: 8px;
    }
    .ts-config-btns .btn {
        flex: 1;
        padding: 8px 0;
        font-weight: 700;
        font-size: 12px;
        border-radius: 3px;
    }
    .ts-config-btns .btn-info {
        background: #4a90d9;
        border-color: #4a90d9;
        color: #fff;
    }
    .ts-config-btns .btn-info:hover { background: #3a7bc8; }

    /* RIGHT PANEL */
    .ts-right {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        background: #fff;
    }
    .ts-right .ts-table-header {
        padding: 8px 16px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .ts-right .ts-table-header h4 {
        margin: 0;
        font-weight: 700;
        font-size: 14px;
        color: #1e293b;
    }
    .ts-right .ts-table-header h4 i { margin-right: 6px; color: #4a90d9; }
    .ts-right .ts-table-header .ts-save-group .btn {
        padding: 5px 16px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 3px;
    }
    .ts-right .ts-table-header .ts-save-group .btn-success {
        background: #16a34a;
        border-color: #16a34a;
        color: #fff;
    }
    .ts-right .ts-table-header .ts-save-group .btn-success:hover { background: #15803d; }
    .ts-right .ts-table-header .ts-save-group .btn-success:disabled {
        background: #94a3b8;
        border-color: #94a3b8;
    }

    /* Scrollable table */
    .ts-table-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: auto;
    }
    .ts-table-scroll table { margin: 0; width: 100%; border-collapse: collapse; }
    .ts-table-scroll thead th {
        background: #2c3e50;
        color: #fff;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 9px 8px;
        border: none;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .ts-table-scroll tbody td {
        padding: 5px 6px;
        vertical-align: middle;
        font-size: 12px;
        border-bottom: 1px solid #f0f3f6;
    }
    .ts-table-scroll tbody tr:hover td { background: #f8fafc; }
    .ts-table-scroll tbody input {
        text-align: center;
        width: 100%;
        border: 1px solid #d0d7e0;
        border-radius: 3px;
        padding: 4px 6px;
        font-size: 12px;
        font-weight: 600;
        transition: border-color 0.15s;
        background: #fff;
    }
    .ts-table-scroll tbody input:focus {
        border-color: #4a90d9;
        outline: none;
        box-shadow: 0 0 0 2px rgba(74,144,217,0.1);
    }
    .ts-table-scroll tbody input.is-invalid { border-color: #dc2626; background: #fef2f2; }
    .ts-table-scroll tbody input.is-valid { border-color: #16a34a; background: #f0fdf4; }
    .ts-table-scroll tbody input[readonly] { background: #f8fafc; color: #94a3b8; cursor: default; border-color: #e2e8f0; }

    .ts-table-scroll .totals-row td {
        font-weight: 700;
        background: #f1f5f9 !important;
        font-size: 12px;
        color: #1e293b;
        border-top: 2px solid #e2e8f0;
    }
    .ts-table-scroll tfoot tr:nth-child(2) td {
        background: #e8f4fd !important;
        font-weight: 600;
        color: #1e293b;
    }
    .ts-table-scroll .validation-row td {
        font-weight: 700;
        padding: 6px 8px !important;
        background: #fafbfc !important;
    }
    .valid-indicator { color: #16a34a; font-size: 16px; }
    .invalid-indicator { color: #dc2626; font-size: 16px; }

    /* Balance banner */
    .ts-balance-banner {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 5px;
        padding: 8px 14px;
        margin: 8px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }
    .ts-balance-banner .ts-bal-sep { color: #93c5fd; font-weight: normal; }
    .ts-balance-banner .ts-bal-code { color: #1e40af; font-weight: 700; }
    .ts-balance-banner .ts-bal-value { font-size: 13px; }
    .ts-balance-banner .ts-bal-ok { color: #16a34a; }
    .ts-balance-banner .ts-bal-warn { color: #d97706; }
    .ts-balance-banner .ts-bal-error { color: #dc2626; }

    /* Legend column */
    .col-legend { width: 30px !important; text-align: center; }
    .col-legend i { font-size: 16px; }

    /* Chit weight input */
    .ts-chit-section {
        padding: 10px 16px;
        background: #fffbeb;
        border-top: 1px solid #fde68a;
        flex-shrink: 0;
    }
    .ts-chit-section label {
        font-weight: 700;
        font-size: 11px;
        color: #92400e;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        display: block;
    }
    .ts-chit-section .input-group { width: 100%; }
    .ts-chit-section .input-group .form-control {
        height: 34px;
        font-size: 13px;
        border: 1.5px solid #fbbf24;
        border-radius: 3px 0 0 3px;
    }
    .ts-chit-section .input-group-btn .btn {
        height: 34px;
        background: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
        font-weight: 700;
        font-size: 12px;
    }
    .ts-chit-section .input-group-btn .btn:hover { background: #d97706; }
</style>

<div class="content-wrapper ts-page">
    <section class="content">
        <div class="ts-container">

            <!-- ===== HEADER BAR ===== -->
            <div class="ts-header">
                <h3><i class="fa fa-code-fork"></i> Partly Sale</h3>
                <a href="<?php echo base_url(); ?>index.php/admin_ret_tag_parts/list_view" class="ts-back">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>

            <!-- ===== BODY: 2 columns ===== -->
            <div class="ts-body">

                <!-- LEFT PANEL -->
                <div class="ts-left">

                    <!-- Scan -->
                    <div class="ts-scan-section">
                        <label><i class="fa fa-barcode"></i> Enter Tag</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="tag_scan_code" placeholder="Scan or type tag code..." autofocus />
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="button" id="btn_fetch_tag">
                                    <i class="fa fa-search"></i> Scan
                                </button>
                            </span>
                        </div>
                        <div id="tag_scan_error" class="ts-scan-error">
                            <i class="fa fa-exclamation-circle"></i><span id="tag_scan_error_text"></span>
                        </div>
                    </div>

                    <!-- Tag Details -->
                    <div class="ts-details-section">
                        <div class="ts-section-title"><i class="fa fa-info-circle"></i> Tag Details</div>
                        <div id="tag_details_placeholder" class="ts-detail-placeholder">
                            Scan a tag to view details
                        </div>
                        <div id="step_base_info" style="display:none;">
                            <div class="ts-detail-grid">
                                <div class="ts-detail-item"><span class="ts-dlbl">Tag Code</span><span class="ts-dval" id="base_tag_code_display">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Product</span><span class="ts-dval" id="info_product">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Design</span><span class="ts-dval" id="info_design">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Purity</span><span class="ts-dval" id="info_purity">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Lot</span><span class="ts-dval" id="info_lot">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Gross Wt</span><span class="ts-dval" id="info_gwt">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Net Wt</span><span class="ts-dval" id="info_nwt">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Less Wt</span><span class="ts-dval" id="info_less_wt">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Stone Wt</span><span class="ts-dval" id="info_stone_wt">-</span></div>
                                <div class="ts-detail-item"><span class="ts-dlbl">Diamond Wt</span><span class="ts-dval" id="info_dia_wt">-</span></div>
                                <div class="ts-detail-item" style="grid-column: 1 / -1; margin-top:6px; padding-top:6px; border-top:1px solid #e2e8f0;">
                                    <span class="ts-dlbl">Estimated Cost</span>
                                    <span class="ts-dval" id="info_cost" style="color:#16a34a; font-size:15px;">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parts Configuration -->
                    <div class="ts-config-section">
                        <div class="ts-section-title"><i class="fa fa-cogs"></i> Parts Configuration</div>
                        <div class="ts-config-row">
                            <label>Number of Parts</label>
                            <input type="number" class="parts-count-input" id="parts_count" min="2" max="99" value="2" />
                        </div>
                        <div class="ts-config-btns">
                            <button class="btn btn-info" type="button" id="btn_calculate_parts">
                                <i class="fa fa-calculator"></i> Re-calculate
                            </button>
                            <button class="btn btn-default" type="button" id="btn_reset_tag">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>



                </div><!-- /.ts-left -->

                <!-- RIGHT PANEL -->
                <div class="ts-right">
                    <div class="ts-table-header">
                        <h4><i class="fa fa-th-list"></i> Part Tag Weights</h4>
                        <div class="ts-save-group" id="step_actions" style="display:none;">
                            <button class="btn btn-success" type="button" id="btn_save_parts" disabled>
                                <i class="fa fa-check-circle"></i> Save
                            </button>
                            <button class="btn btn-default" type="button" id="btn_cancel_parts" style="margin-left:4px;">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>

                    <!-- Balance Banner -->
                    <div id="balance_banner" class="ts-balance-banner" style="display:none;">
                        <span>Base Tag:</span>
                        <span id="bal_tag_code" class="ts-bal-code"></span>
                        <span class="ts-bal-sep">│</span>
                        <span>GWT: <b id="bal_base_gwt">0.000</b></span>
                        <span class="ts-bal-sep">│</span>
                        <span>Balance: <b id="bal_remaining" class="ts-bal-value ts-bal-ok">0.000</b></span>
                    </div>
                    <div class="ts-table-scroll">
                        <table id="child_weight_table" class="table text-center child-weight-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="17%">Tag Code</th>
                                    <th width="13%">Gross Wt</th>
                                    <th width="13%">Net Wt</th>
                                    <th width="13%">Less Wt</th>
                                    <th width="13%">Stone Wt</th>
                                    <th width="13%">Diamond Wt</th>
                                    <th width="3%"></th>
                                </tr>
                            </thead>
                            <tbody id="child_weight_tbody">
                                <!-- Dynamically populated -->
                            </tbody>
                            <tfoot style="display:none;">
                                <tr class="totals-row">
                                    <td colspan="2"><b>Part Total</b></td>
                                    <td id="total_gwt">0.000</td>
                                    <td id="total_nwt">0.000</td>
                                    <td id="total_less_wt">0.000</td>
                                    <td id="total_stone_wt">0.000</td>
                                    <td id="total_dia_wt">0.000</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>Base</b></td>
                                    <td id="base_gwt">0.000</td>
                                    <td id="base_nwt">0.000</td>
                                    <td id="base_less_wt">0.000</td>
                                    <td id="base_stone_wt">0.000</td>
                                    <td id="base_dia_wt">0.000</td>
                                    <td></td>
                                </tr>
                                <tr class="validation-row">
                                    <td colspan="2"><b>Validation</b></td>
                                    <td id="validate_gwt"><span class="invalid-indicator"><i class="fa fa-times-circle"></i></span></td>
                                    <td id="validate_nwt"><span class="invalid-indicator"><i class="fa fa-times-circle"></i></span></td>
                                    <td id="validate_less_wt"><span class="invalid-indicator"><i class="fa fa-times-circle"></i></span></td>
                                    <td id="validate_stone_wt"><span class="invalid-indicator"><i class="fa fa-times-circle"></i></span></td>
                                    <td id="validate_dia_wt"><span class="invalid-indicator"><i class="fa fa-times-circle"></i></span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div><!-- /.ts-right -->

            </div><!-- /.ts-body -->

        </div><!-- /.ts-container -->
    </section>
</div>

<!-- Hidden fields -->
<input type="hidden" id="base_tag_id" value="" />
<input type="hidden" id="base_tag_code" value="" />
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>" />
<input type="hidden" id="id_branch" value="<?php echo $this->session->userdata('id_branch') ?: ''; ?>" />
<input type="hidden" id="branch_id_country" value="" />
<input type="hidden" id="branch_id_state" value="" />
<input type="hidden" id="branch_id_city" value="" />
<input type="hidden" id="branch_pincode" value="" />
<input type="hidden" id="branch_id_village" value="" />



