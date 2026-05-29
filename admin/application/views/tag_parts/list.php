<!-- Content Wrapper. Contains page content -->

<style>
    /* ========== PREMIUM PARTLY SALE LIST ========== */
    .ts-page .content-header { margin-bottom: 0; }

    /* Gradient header */
    .ts-header-banner {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #3c8dbc 100%);
        color: #fff;
        padding: 22px 25px;
        border-radius: 6px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(30,60,114,0.3);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ts-header-banner h3 {
        margin: 0;
        font-weight: 700;
        font-size: 20px;
        letter-spacing: 0.5px;
    }
    .ts-header-banner h3 i {
        margin-right: 10px;
        font-size: 22px;
        opacity: 0.85;
    }
    .ts-header-banner .ts-header-actions .btn {
        border: 2px solid rgba(255,255,255,0.6);
        color: #fff;
        background: rgba(255,255,255,0.12);
        font-weight: 600;
        border-radius: 20px;
        padding: 7px 18px;
        transition: all 0.2s;
    }
    .ts-header-banner .ts-header-actions .btn:hover {
        background: #fff;
        color: #1e3c72;
        border-color: #fff;
    }

    /* Filter card */
    .ts-filter-card {
        background: #fff;
        border: 1px solid #e8ecf1;
        border-radius: 6px;
        padding: 16px 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .ts-filter-card .filter-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ts-filter-card .filter-group label {
        font-weight: 600;
        color: #5a6a85;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
        white-space: nowrap;
    }
    .ts-filter-card .filter-group .form-control {
        border-radius: 4px;
        border: 1px solid #d5dce6;
        height: 34px;
        font-size: 13px;
    }
    .ts-filter-card .filter-group .form-control:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 2px rgba(60,141,188,0.15);
    }
    .ts-filter-card .ts-filter-actions {
        margin-left: auto;
        display: flex;
        gap: 8px;
    }
    .ts-filter-card .ts-filter-actions .btn {
        border-radius: 4px;
        font-weight: 600;
        font-size: 12px;
        padding: 7px 16px;
    }

    /* Status badges */
    .ts-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .ts-badge-active {
        background: linear-gradient(135deg, #00b09b, #96c93d);
        color: #fff;
    }
    .ts-badge-reverted {
        background: linear-gradient(135deg, #8e9eab, #adb5bd);
        color: #fff;
    }
    .ts-badge-partial {
        background: linear-gradient(135deg, #f2994a, #f2c94c);
        color: #fff;
    }
    .ts-badge-billed {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
    }

    /* Premium table */
    .ts-table-card {
        background: #fff;
        border: 1px solid #e8ecf1;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .ts-table-card .ts-table-header {
        padding: 14px 20px;
        border-bottom: 1px solid #e8ecf1;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ts-table-card .ts-table-header h4 {
        margin: 0;
        font-weight: 700;
        font-size: 15px;
        color: #2c3e50;
    }
    .ts-table-card .ts-table-header h4 i {
        margin-right: 8px;
        color: #3c8dbc;
    }
    .ts-table-card .ts-table-body { padding: 0; }
    .ts-table-card .table { margin: 0; }
    .ts-table-card .table > thead > tr > th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        color: #5a6a85;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 10px;
    }
    .ts-table-card .table > tbody > tr > td {
        padding: 10px;
        vertical-align: middle;
        font-size: 13px;
        border-top: 1px solid #f0f3f6;
    }
    .ts-table-card .table > tbody > tr:hover { background: #f8fafc; }

    /* Action buttons */
    .ts-action-btn {
        padding: 4px 10px;
        margin: 1px;
        font-size: 11px;
        border-radius: 4px;
        font-weight: 600;
        transition: all 0.15s;
    }
    .ts-action-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }

    /* EOD Queue highlight card */
    .ts-eod-card {
        background: #fff;
        border: 1px solid #f0c040;
        border-left: 4px solid #f39c12;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(243,156,18,0.1);
        margin-bottom: 20px;
    }
    .ts-eod-card .ts-eod-header {
        padding: 12px 20px;
        background: linear-gradient(135deg, #f39c12 0%, #f7b731 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ts-eod-card .ts-eod-header h4 {
        margin: 0;
        font-weight: 700;
        font-size: 15px;
        color: #fff;
    }
    .ts-eod-card .ts-eod-header h4 i { margin-right: 8px; }
    .ts-eod-card .ts-eod-count {
        background: rgba(255,255,255,0.25);
        color: #fff;
        padding: 4px 14px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 12px;
    }
    .ts-eod-card .ts-eod-body { padding: 0; }
    .ts-eod-card .ts-eod-footer {
        padding: 10px 20px;
        background: #fffbf0;
        border-top: 1px solid #f0e6cc;
        font-size: 12px;
        color: #8a7240;
    }
    .ts-eod-card .ts-eod-footer i { margin-right: 5px; }

    /* Modal */
    .ts-modal-header {
        background: linear-gradient(135deg, #1e3c72, #3c8dbc);
        color: #fff;
        border-radius: 5px 5px 0 0;
    }
    .ts-modal-header .close { color: #fff; opacity: 0.8; }
    .ts-modal-header .close:hover { opacity: 1; }
</style>

<div class="content-wrapper ts-page">

    <section class="content">

        <!-- Premium Header Banner -->
        <div class="ts-header-banner">
            <h3><i class="fa fa-code-fork"></i> Partly Sale Management</h3>
            <div class="ts-header-actions">
                <a href="<?php echo base_url(); ?>index.php/admin_ret_tag_parts/form" class="btn btn-sm">
                    <i class="fa fa-plus-circle"></i> New Partly Sale
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="ts-filter-card">
            <div class="filter-group">
                <label><i class="fa fa-filter"></i> Status</label>
                <select class="form-control" id="filter_status" style="width:120px;">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="2">Reverted</option>
                    <option value="3">Partial</option>
                    <option value="4">Billed</option>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fa fa-calendar"></i> Period</label>
                <input type="text" class="form-control" id="dt_range" placeholder="Select date range" autocomplete="off" style="width:220px;" readonly>
            </div>
            <div class="ts-filter-actions">
                <button class="btn btn-primary btn-sm" id="btn_filter_history">
                    <i class="fa fa-search"></i> Search
                </button>
                <button class="btn btn-default btn-sm" id="btn_reset_filter">
                    <i class="fa fa-times-circle"></i> Reset
                </button>
            </div>
        </div>

        <!-- Parts History Table Card -->
        <div class="ts-table-card">
            <div class="ts-table-header">
                <h4><i class="fa fa-history"></i> Partly Sale History</h4>
            </div>
            <div class="ts-table-body">
                <div class="table-responsive">
                    <table id="parts_history_table" class="table text-center" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Base Tag</th>
                                <th>Parts</th>
                                <th>Base GWT</th>
                                <th>Base NWT</th>
                                <th>Status</th>
                                <th>Unbilled</th>
                                <th>Created By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTable populated -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- EOD Revert Queue -->
        <div class="ts-eod-card">
            <div class="ts-eod-header">
                <h4><i class="fa fa-clock-o"></i> EOD Revert Queue</h4>
                <span class="ts-eod-count" id="eod_queue_count">0 active</span>
            </div>
            <div class="ts-eod-body">
                <div class="table-responsive">
                    <table id="eod_queue_table" class="table text-center" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Base Tag</th>
                                <th>Total Parts</th>
                                <th>Unbilled</th>
                                <th>Base GWT</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="eod_queue_tbody">
                            <!-- Dynamically populated -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="ts-eod-footer">
                <i class="fa fa-info-circle"></i> Active partly sales with unbilled parts will be auto-reverted during Day Close.
            </div>
        </div>

    </section>

</div><!-- /.content-wrapper -->

<!-- Detail View Modal -->
<div class="modal fade" id="partsDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header ts-modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-eye"></i> Partly Sale Detail — <span id="modal_base_code"></span></h4>
            </div>
            <div class="modal-body" id="parts_detail_content">
                <!-- Populated via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>" />
<input type="hidden" id="user_role" value="<?php echo $this->session->userdata('role'); ?>" />
<input type="hidden" id="special_access" value="<?php echo $this->session->userdata('special_access'); ?>" />
<input type="hidden" id="access_edit" value="<?php echo isset($access['edit']) ? $access['edit'] : 0; ?>" />
<input type="hidden" id="access_delete" value="<?php echo isset($access['delete']) ? $access['delete'] : 0; ?>" />

<!-- Admin Modify Weights Modal (loaded inline for in-page modal access) -->
<?php $this->load->view('tag_parts/admin_modify'); ?>
