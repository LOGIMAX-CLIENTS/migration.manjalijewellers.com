<!-- IRN E-Invoice Settings Dashboard -->
<style>
    /* ═══════════════════════════════════════════════════════ */
    /* IRN Dashboard — Premium Styling                        */
    /* ═══════════════════════════════════════════════════════ */
    .irn-dash .status-strip {
        display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px;
    }
    .irn-dash .status-card {
        flex: 1; min-width: 200px; border-radius: 6px; padding: 18px 20px;
        position: relative; overflow: hidden; color: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,.12);
        transition: transform .2s, box-shadow .2s;
    }
    .irn-dash .status-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.18); }
    .irn-dash .status-card .sc-icon { position: absolute; top: 12px; right: 16px; font-size: 38px; opacity: .2; }
    .irn-dash .status-card .sc-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: .85; margin-bottom: 4px; }
    .irn-dash .status-card .sc-value { font-size: 20px; font-weight: 700; }
    .irn-dash .status-card .sc-hint  { font-size: 11px; margin-top: 6px; opacity: .7; }
    .irn-dash .sc-disabled  { background: linear-gradient(135deg, #636e72, #2d3436); }
    .irn-dash .sc-debug     { background: linear-gradient(135deg, #fdcb6e, #e17055); }
    .irn-dash .sc-live       { background: linear-gradient(135deg, #00b894, #00cec9); }
    .irn-dash .sc-prod       { background: linear-gradient(135deg, #d63031, #e17055); }
    .irn-dash .sc-sandbox    { background: linear-gradient(135deg, #0984e3, #74b9ff); }
    .irn-dash .sc-branches   { background: linear-gradient(135deg, #6c5ce7, #a29bfe); }
    .irn-dash .sc-irn-count  { background: linear-gradient(135deg, #00b894, #55efc4); }

    /* Production Warning */
    .irn-dash .prod-warning {
        background: linear-gradient(135deg, #ff7675 0%, #d63031 100%);
        color: #fff; border-radius: 6px; padding: 16px 24px; margin-bottom: 20px;
        display: none; box-shadow: 0 2px 12px rgba(214,48,49,.3);
    }
    .irn-dash .prod-warning .pw-title { font-size: 15px; font-weight: 700; margin-bottom: 6px; }
    .irn-dash .prod-warning .pw-url { font-family: monospace; font-size: 12px; background: rgba(0,0,0,.15); padding: 4px 10px; border-radius: 3px; display: inline-block; margin: 2px 0; }
    .irn-dash .prod-warning .pw-match { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600; margin-left: 8px; }
    .irn-dash .pw-match-yes { background: rgba(255,255,255,.25); }
    .irn-dash .pw-match-no  { background: rgba(0,0,0,.25); }

    /* Config Panel */
    .irn-dash .config-panel { border: none; border-radius: 6px; box-shadow: 0 1px 8px rgba(0,0,0,.08); margin-bottom: 20px; }
    .irn-dash .config-panel .box-header { border-bottom: 2px solid #f1f2f6; padding: 14px 20px; }
    .irn-dash .config-panel .box-header .box-title { font-size: 14px; font-weight: 700; color: #2d3436; letter-spacing: .3px; }
    .irn-dash .config-panel .box-body { padding: 20px; }
    .irn-dash .config-panel .form-group label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #636e72; margin-bottom: 6px; }
    .irn-dash .config-panel .form-control { border-radius: 4px; border: 1px solid #dfe6e9; font-size: 13px; padding: 8px 12px; transition: border-color .2s, box-shadow .2s; }
    .irn-dash .config-panel .form-control:focus { border-color: #0984e3; box-shadow: 0 0 0 3px rgba(9,132,227,.1); }

    /* Branch Cards */
    .irn-dash .branch-card {
        border-radius: 6px; padding: 0; margin-bottom: 16px; overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,.08); border: 1px solid #f1f2f6;
        transition: border-color .3s, box-shadow .3s;
    }
    .irn-dash .branch-card:hover { border-color: #0984e3; box-shadow: 0 3px 12px rgba(9,132,227,.12); }
    .irn-dash .branch-card .bc-header {
        padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid #f1f2f6; background: #fafbfc;
    }
    .irn-dash .branch-card .bc-name { font-size: 13px; font-weight: 700; color: #2d3436; }
    .irn-dash .branch-card .bc-gst  { font-size: 11px; color: #636e72; font-family: monospace; }
    .irn-dash .branch-card .bc-badge { font-size: 10px; padding: 3px 8px; border-radius: 20px; font-weight: 600; }
    .irn-dash .bc-ready    { background: #dfe6e9; color: #636e72; }
    .irn-dash .bc-complete { background: #55efc4; color: #00b894; }
    .irn-dash .bc-missing  { background: #fab1a0; color: #d63031; }
    .irn-dash .branch-card .bc-body { padding: 14px 16px; }
    .irn-dash .branch-card .bc-body label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #636e72; margin-bottom: 4px; }
    .irn-dash .branch-card .bc-body .form-control { font-size: 12px; padding: 6px 10px; height: auto; }
    .irn-dash .branch-card .bc-footer {
        padding: 10px 16px; background: #fafbfc; border-top: 1px solid #f1f2f6;
        display: flex; align-items: center; justify-content: space-between;
    }
    .irn-dash .branch-card .bc-footer .auth-token { font-size: 10px; color: #b2bec3; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .irn-dash .branch-card .bc-footer .save-branch-btn { font-size: 12px; padding: 5px 14px; border-radius: 4px; }
    .irn-dash .save-ok { display: none; font-size: 12px; color: #00b894; margin-left: 8px; font-weight: 600; }

    /* Credential Inheritance Toggle */
    .irn-dash .bc-inherit-row {
        padding: 8px 0 6px; margin-bottom: 8px; border-bottom: 1px dashed #dfe6e9;
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .irn-dash .bc-inherit-row label.toggle-label {
        font-size: 11px !important; font-weight: 600 !important; color: #636e72 !important;
        text-transform: none !important; letter-spacing: 0 !important; margin: 0 !important; cursor: pointer;
    }
    .irn-dash .inherit-switch {
        position: relative; display: inline-block; width: 36px; height: 20px; vertical-align: middle;
    }
    .irn-dash .inherit-switch input { opacity: 0; width: 0; height: 0; }
    .irn-dash .inherit-switch .slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background: #dfe6e9; border-radius: 20px; transition: .3s;
    }
    .irn-dash .inherit-switch .slider:before {
        position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px;
        background: #fff; border-radius: 50%; transition: .3s;
    }
    .irn-dash .inherit-switch input:checked + .slider { background: #6c5ce7; }
    .irn-dash .inherit-switch input:checked + .slider:before { transform: translateX(16px); }
    .irn-dash .bc-source-select {
        font-size: 11px; padding: 3px 8px; border-radius: 4px; border: 1px solid #dfe6e9;
        background: #fff; color: #2d3436; max-width: 200px;
    }
    .irn-dash .bc-source-select:focus { border-color: #6c5ce7; outline: none; }
    .irn-dash .bc-body .form-control.inherited {
        background: #f8f9fa; color: #636e72; border-style: dashed;
    }
    .irn-dash .inherited-tag {
        font-size: 9px; color: #6c5ce7; font-weight: 600; margin-left: 4px;
    }

    /* Sync All Button */
    .irn-dash .btn-sync-all {
        background: linear-gradient(135deg, #6c5ce7, #a29bfe); border: none; color: #fff;
        padding: 5px 14px; border-radius: 4px; font-weight: 600; font-size: 11px;
        transition: opacity .2s; cursor: pointer; margin-left: 10px;
    }
    .irn-dash .btn-sync-all:hover { opacity: .85; color: #fff; }
    .irn-dash .sync-ok { display: none; font-size: 11px; color: #00b894; margin-left: 6px; font-weight: 600; }

    /* Activity Log */
    .irn-dash .activity-panel { border: none; border-radius: 6px; box-shadow: 0 1px 8px rgba(0,0,0,.08); }
    .irn-dash .irn-status-badge { font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
    .irn-dash .irn-badge-success { background: #55efc4; color: #00b894; }
    .irn-dash .irn-badge-pending { background: #ffeaa7; color: #d68910; }
    .irn-dash .irn-badge-failed  { background: #fab1a0; color: #d63031; }

    /* Save Button */
    .irn-dash .btn-save-config {
        background: linear-gradient(135deg, #0984e3, #74b9ff); border: none; color: #fff;
        padding: 10px 28px; border-radius: 4px; font-weight: 600; font-size: 13px;
        transition: opacity .2s, transform .1s;
    }
    .irn-dash .btn-save-config:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
    .irn-dash .btn-save-config:active { transform: translateY(0); }

    /* Responsive tweaks */
    @media (max-width: 768px) {
        .irn-dash .status-strip { flex-direction: column; }
        .irn-dash .status-card { min-width: 100%; }
    }
    @media print { a[href]:after { content: ""; } }
</style>

<div class="content-wrapper irn-dash">

    <!-- Content Header -->
    <section class="content-header">
        <h1>
            E-Invoice Settings
            <small style="color:#636e72;">IRN Configuration & Monitoring</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Settings</a></li>
            <li class="active">E-Invoice (IRN)</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- ═══════════════════  ALERT  ═══════════════════ -->
        <div id="irn_alert" class="row" style="display:none;">
            <div class="col-xs-12">
                <div class="alert alert-dismissable" id="irn_alert_box" style="border-radius:4px;border:none;box-shadow:0 1px 4px rgba(0,0,0,.1);">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <span id="irn_alert_msg"></span>
                </div>
            </div>
        </div>

        <!-- ═══════════════════  STATUS STRIP  ═══════════════════ -->
        <div class="status-strip" id="status_strip">
            <!-- Rendered by JS -->
        </div>

        <!-- ═══════════════════  PRODUCTION WARNING  ═══════════════════ -->
        <div class="prod-warning" id="prod_warning">
            <div class="pw-title"><i class="fa fa-exclamation-triangle"></i> PRODUCTION MODE ACTIVE</div>
            <div id="prod_warning_body"></div>
        </div>

        <!-- ═══════════════════  GSP CONFIG + BRANCH CREDENTIALS (side by side)  ═══════════════════ -->
        <div class="row">
            <!-- LEFT: GSP API Configuration -->
            <div class="col-md-6">
                <div class="box config-panel">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-plug" style="color:#0984e3;margin-right:8px;"></i>GSP API Configuration</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">

                        <!-- 1. GSP Auth Base URL (Production) -->
                        <div class="form-group">
                            <label>GSP Auth Base URL</label>
                            <input type="text" class="form-control" id="gsp_auth_base_url" placeholder="https://einvapi.charteredinfo.com/eivital/dec/v1.04/auth">
                        </div>

                        <!-- 2. GSP E-Invoice Base URL (Production) -->
                        <div class="form-group">
                            <label>GSP E-Invoice Base URL</label>
                            <input type="text" class="form-control" id="gsp_einvoice_base_url" placeholder="https://einvapi.charteredinfo.com/eicore/dec/v1.03/Invoice">
                        </div>

                        <!-- 3 & 4. Chartered Info Credentials -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Chartered Info ID</label>
                                    <input type="text" class="form-control" id="usp_id" placeholder="e.g. 1658319921">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>CI Password (Chartered Info)</label>
                                    <input type="password" class="form-control" id="ci_password" placeholder="CI Password">
                                </div>
                            </div>
                        </div>

                        <!-- Sandbox URLs (for non-production testing) -->
                        <div style="border:1px dashed #74b9ff;border-radius:5px;padding:12px 14px;margin:10px 0 14px;background:#f8f9ff;">
                            <div style="font-size:11px;font-weight:700;color:#0984e3;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">
                                <i class="fa fa-flask" style="margin-right:5px;"></i>Sandbox URLs <span style="font-weight:400;color:#636e72;text-transform:none;letter-spacing:0;">(used when Environment = Non-Production)</span>
                            </div>
                            <div class="form-group" style="margin-bottom:8px;">
                                <label style="color:#0984e3 !important;">Sandbox Auth URL</label>
                                <input type="text" class="form-control" id="sandbox_auth_url" placeholder="http://gstsandbox.charteredinfo.com/eivital/dec/v1.03/auth">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label style="color:#0984e3 !important;">Sandbox E-Invoice URL</label>
                                <input type="text" class="form-control" id="sandbox_einvoice_url" placeholder="http://gstsandbox.charteredinfo.com/eicore/dec/v1.03/Invoice">
                            </div>
                        </div>

                        <!-- 5. Email (moved to last) -->
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="irn_error_email" placeholder="admin@example.com">
                        </div>

                        <div style="text-align:right;margin-top:6px;">
                            <button type="button" class="btn btn-save-config" id="save_global_settings">
                                <i class="fa fa-save"></i> Save Configuration
                            </button>
                            <span class="save-ok" id="global_save_ok"><i class="fa fa-check-circle"></i> Saved</span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT: Branch GSP Credentials -->
            <div class="col-md-6">
                <div class="box config-panel">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-building" style="color:#6c5ce7;margin-right:8px;"></i>Branch GSP Credentials</h3>
                        <span id="branch_count" style="margin-left:10px;font-size:12px;color:#636e72;"></span>
                        <button type="button" class="btn btn-sync-all" id="sync_all_branches" title="Copy HO credentials to all branches">
                            <i class="fa fa-clone"></i> Sync All to HO
                        </button>
                        <span class="sync-ok" id="sync_all_ok"><i class="fa fa-check-circle"></i> All Synced</span>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body" style="max-height:520px;overflow-y:auto;">
                        <div class="row" id="branch_cards_container">
                            <!-- Branch cards rendered by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════  IRN ACTIVITY LOG  ═══════════════════ -->
        <div class="row">
            <div class="col-md-12">
                <div class="box activity-panel">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="font-size:14px;font-weight:700;color:#2d3436;">
                            <i class="fa fa-history" style="color:#d63031;margin-right:8px;"></i>Recent IRN Activity
                        </h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" id="refresh_activity"><i class="fa fa-refresh"></i></button>
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="irn_activity_table" class="table table-hover" style="font-size:12px;">
                                <thead>
                                    <tr style="background:#f8f9fa;">
                                        <th>Bill No</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Branch</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>IRN</th>
                                    </tr>
                                </thead>
                                <tbody id="irn_activity_body">
                                    <tr><td colspan="7" class="text-center" style="color:#b2bec3;padding:24px;">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div class="overlay" style="display:none">
            <i class="fa fa-refresh fa-spin"></i>
        </div>

    </section>
</div>


