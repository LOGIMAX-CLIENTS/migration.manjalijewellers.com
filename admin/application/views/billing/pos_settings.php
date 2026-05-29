<!-- POS Settings — v10 (System-Level Flat Design) -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
:root { --f:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; --mono:'SF Mono','Fira Code','Consolas',monospace;
--ink:#0f172a; --ink2:#1e293b; --ink3:#334155; --mute:#64748b; --faint:#94a3b8; --line:#e2e8f0; --wash:#f1f5f9; --bg:#f8fafc; --white:#fff;
--ok:#059669; --ok-bg:#ecfdf5; --warn:#d97706; --warn-bg:#fffbeb; --err:#dc2626; --err-bg:#fef2f2;
--accent:#2563eb; --accent-bg:#eff6ff;
--radius:8px; --shadow:0 1px 2px rgba(0,0,0,.04); }

/* ─ Shell ─ */
.pos-content { padding:20px 28px; display:flex; flex-direction:column; width:100%; box-sizing:border-box; font-family:var(--f); color:var(--ink); max-width:1400px; margin:0 auto; background:var(--bg); }

/* ─ Header ─ */
.pos-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.pos-hdr h2 { font-size:18px; font-weight:700; color:var(--ink); margin:0; display:flex; align-items:center; gap:8px; letter-spacing:-.2px; }
.pos-hdr h2 i { color:var(--mute); font-size:16px; }
.pos-hdr-r { display:flex; align-items:center; gap:8px; }
.pos-hdr-badge { font-size:10px; padding:4px 10px; border-radius:4px; background:var(--wash); color:var(--faint); font-weight:600; border:1px solid var(--line); }

/* ─ Grid ─ */
.pos-grid { display:grid; grid-template-columns:1fr; gap:24px; align-items:start; flex:1; }
body.sidebar-collapse .pos-grid { grid-template-columns:1fr 1fr; }
@media(max-width:768px){ .pos-grid { grid-template-columns:1fr !important; } }

/* ─ Section Card ─ */
.pos-col { background:var(--white); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow); overflow:visible; }
.pos-col-hdr { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--line); }
.pos-col-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--faint); display:flex; align-items:center; gap:6px; }
.pos-col-title i { font-size:12px; }
.pos-col-body { }
.pos-col-desc { font-size:11px; color:var(--faint); padding:8px 16px; border-bottom:1px solid var(--wash); line-height:1.5; font-weight:400; }

/* ─ Buttons ─ */
.pb { display:inline-flex; align-items:center; gap:4px; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; border:1px solid var(--line); background:var(--white); color:var(--ink3); cursor:pointer; transition:all .15s; text-decoration:none !important; white-space:nowrap; font-family:var(--f); }
.pb:hover { background:var(--wash); border-color:var(--faint); color:var(--ink); }
.pb-p { background:var(--ink) !important; border-color:var(--ink) !important; color:var(--white) !important; }
.pb-p:hover { background:var(--ink2) !important; }
.pb-d { color:var(--err); border-color:var(--line); }
.pb-d:hover { background:var(--err-bg); border-color:var(--err); }
.pb-s { padding:4px 8px; font-size:11px; }
.pb-ghost { border:none; background:none; color:var(--mute); padding:4px 6px; }
.pb-ghost:hover { color:var(--ink); background:var(--wash); }

/* ─ Tooltip ─ */
[data-tip] { position:relative; }
[data-tip]:hover::after { content:attr(data-tip); position:absolute; bottom:calc(100% + 8px); left:0; background:var(--ink); color:var(--white); font-size:11px; font-weight:500; padding:5px 10px; border-radius:6px; white-space:nowrap; z-index:9999; pointer-events:none; animation:tipIn .15s ease; }
[data-tip]:hover::before { content:''; position:absolute; bottom:calc(100% + 4px); left:12px; border:5px solid transparent; border-top-color:var(--ink); z-index:9999; pointer-events:none; animation:tipIn .15s ease; }
@keyframes tipIn { from{opacity:0;transform:translateY(4px)} to{opacity:1;transform:translateY(0)} }
.tip-r[data-tip]:hover::after { left:auto; right:0; }
.tip-r[data-tip]:hover::before { left:auto; right:12px; }

/* ─ Provider/Device Item ─ */
.pi { border-bottom:1px solid var(--wash); }
.pi:last-child { border-bottom:none; }

/* Collapsed header row — always visible */
.pi-row { display:flex; align-items:center; padding:12px 16px; gap:12px; cursor:pointer; transition:background .1s; user-select:none; }
.pi-row:hover { background:var(--bg); }
.pi-chevron { font-size:10px; color:var(--faint); transition:transform .2s; flex-shrink:0; width:16px; text-align:center; }
.pi.open .pi-chevron { transform:rotate(90deg); color:var(--ink3); }
.pi-name { font-size:13px; font-weight:600; color:var(--ink); flex:1; min-width:0; display:flex; align-items:center; gap:6px; }
.pi-name code { font-size:10px; color:var(--faint); background:var(--wash); padding:1px 6px; border-radius:3px; font-weight:500; font-family:var(--mono); }
.pi-status { display:flex; align-items:center; gap:6px; font-size:11px; color:var(--mute); font-weight:500; flex-shrink:0; }
.pi-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.pi-dot-live { background:var(--ok); box-shadow:0 0 4px rgba(5,150,105,.4); }
.pi-dot-uat { background:var(--warn); }
.pi-dot-off { background:var(--faint); }
.pi-type { font-size:10px; color:var(--faint); font-weight:600; text-transform:uppercase; letter-spacing:.3px; flex-shrink:0; }

/* Default star */
.pi-star { color:#f59e0b; font-size:11px; margin-right:2px; }

/* Expanded detail — hidden by default */
.pi-body { display:none; padding:0 16px 14px 44px; }
.pi.open .pi-body { display:block; }

/* Detail grid */
.pi-detail { display:flex; gap:24px; flex-wrap:wrap; margin-bottom:10px; }
.pi-detail-col { min-width:0; }
.pi-detail-col.col-wide { flex:2; }
.pi-detail-col.col-narrow { flex:1; }

/* Meta line */
.pi-meta { font-size:11px; color:var(--mute); display:flex; align-items:center; gap:12px; margin-bottom:6px; font-weight:500; }
.pi-meta .fa-check-circle { color:var(--faint); font-size:10px; }
.pi-meta .fa-times-circle { color:var(--line); font-size:10px; }

/* URL */
.pi-url { font-family:var(--mono); font-size:11px; color:var(--faint); background:var(--bg); padding:5px 8px; border-radius:4px; margin-bottom:8px; word-break:break-all; border:1px solid var(--wash); letter-spacing:.2px; }

/* Action row */
.pi-acts { display:flex; align-items:center; gap:4px; }

/* Credential Grid */
.pc { display:grid; grid-template-columns:repeat(auto-fill, minmax(130px,1fr)); gap:2px 16px; margin-bottom:8px; }
.pcl { font-size:9px; text-transform:uppercase; letter-spacing:.7px; color:var(--faint); font-weight:700; }
.pcv { font-size:12px; color:var(--ink3); font-family:var(--mono); display:flex; align-items:center; gap:4px; margin-bottom:6px; letter-spacing:.3px; }
.pcv-linked { font-family:var(--f) !important; color:var(--ink3) !important; font-weight:600; }

/* Eye toggle */
.pe { width:20px; height:20px; display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--line); border-radius:4px; background:var(--white); color:var(--faint); cursor:pointer; font-size:10px; padding:0; flex-shrink:0; transition:all .12s; }
.pe:hover,.pe.on { border-color:var(--ink3); color:var(--ink3); background:var(--wash); }

/* Default checkbox */
.pdef { display:flex; align-items:center; gap:4px; font-size:11px; color:var(--mute); cursor:pointer; margin:0; font-weight:600; margin-left:auto; }
.pdef:hover { color:var(--ink); }
.pdef input { accent-color:var(--ink); margin:0; width:15px; height:15px; }

/* Security note */
.psn { display:flex; align-items:center; gap:6px; padding:8px 16px; background:var(--warn-bg); color:#92400e; font-size:11px; border-top:1px solid rgba(245,158,11,.1); font-weight:500; }
.psn i { color:var(--warn); font-size:12px; }

/* Empty */
.pem { text-align:center; padding:28px; color:var(--faint); font-size:12px; font-weight:500; }
.pem i { font-size:24px; color:var(--line); display:block; margin-bottom:6px; }

/* ═══ MODAL STYLES ═══ */
.modal-backdrop { background-color:var(--ink) !important; }
.modal-backdrop.in { opacity:.5 !important; }
.pos-modal .modal-content { border-radius:12px; border:none; box-shadow:0 20px 60px rgba(0,0,0,.12); overflow:hidden; }
.pos-modal .modal-header { background:var(--white); border-bottom:1px solid var(--line); padding:16px 22px; }
.pos-modal .modal-title { font-size:15px; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:8px; }
.pos-modal .modal-title i { color:var(--mute); }
.pos-modal .modal-body { padding:20px 22px; }
.pos-modal .form-group { margin-bottom:14px; }
.pos-modal .modal-body label { font-size:11px; text-transform:uppercase; letter-spacing:.5px; font-weight:700; color:var(--faint); margin-bottom:5px; display:block; }
.pos-modal .modal-body .form-control { border-radius:6px; border:1.5px solid var(--line); font-size:13px; padding:8px 12px; background:var(--bg); transition:all .2s; font-family:var(--f); color:var(--ink); }
.pos-modal .modal-body .form-control:focus { border-color:var(--accent); background:var(--white); box-shadow:0 0 0 3px rgba(37,99,235,.06); outline:none; }
.pos-modal .modal-body .form-control::placeholder { color:var(--faint); }
.pos-modal .modal-body legend { font-size:10px; text-transform:uppercase; letter-spacing:1px; font-weight:700; color:var(--ink3); background:var(--wash); padding:6px 12px; border-radius:4px; margin:10px 0 14px; border:1px solid var(--line); display:block; width:auto; }
.pos-modal .modal-body .checkbox { margin:4px 0; }
.pos-modal .modal-body .checkbox label { text-transform:none !important; letter-spacing:0 !important; font-weight:500 !important; font-size:13px !important; color:var(--ink3); display:inline-flex; align-items:center; gap:6px; padding:0; cursor:pointer; border:none; background:none; white-space:nowrap; }
.pos-modal .modal-body .checkbox input[type="checkbox"] { accent-color:var(--ink); margin:0 4px 0 0; flex-shrink:0; width:16px; height:16px; }
.pos-modal .modal-footer { border-top:1px solid var(--line); padding:14px 22px; background:var(--bg); display:flex; justify-content:flex-end; gap:8px; }
.pw-w { position:relative; }
.pw-w .form-control { padding-right:36px; }
.pw-t { position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--faint); cursor:pointer; font-size:13px; padding:2px; transition:color .12s; }
.pw-t:hover { color:var(--ink); }
@media(max-width:768px){ .pi-row { flex-wrap:wrap; } .pi-detail { flex-direction:column; } .pc { grid-template-columns:1fr 1fr; } }
</style>

<div class="content-wrapper">
    <section class="content pos-content">

        <div class="pos-hdr">
            <h2><i class="fa fa-shield"></i> POS Settings</h2>
            <div class="pos-hdr-r">
                <?php
                    $acl_check = $this->admin_settings_model->get_access('admin_pos/posSettings');
                    $acl_view = (!empty($acl_check) && isset($acl_check['view'])) ? $acl_check['view'] : 'null';
                ?>
                <span class="pos-hdr-badge">Profile <?php echo $this->session->userdata('profile'); ?> · ACL view=<?php echo $acl_view; ?></span>
                <a href="<?php echo base_url(); ?>index.php/admin_pos/posTransactions" class="pb tip-r" data-tip="View all POS payment transactions"><i class="fa fa-list-alt"></i> Transactions</a>
            </div>
        </div>

        <div class="pos-grid">

            <!-- LEFT: PROVIDERS -->
            <div class="pos-col">
                <div class="pos-col-hdr">
                    <span class="pos-col-title"><i class="fa fa-plug"></i> Providers (<?php echo count($providers); ?>)</span>
                    <button class="pb pb-p pb-s" onclick="openAddProviderModal()" data-tip="Register a new payment provider"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="pos-col-desc">Payment gateways that process POS transactions. Click a row to expand details.</div>
                <div class="pos-col-body">
                    <?php if(!empty($providers)): foreach($providers as $p): ?>
                    <div class="pi" onclick="togglePI(this)">
                        <div class="pi-row">
                            <i class="fa fa-chevron-right pi-chevron"></i>
                            <div class="pi-name"><?php echo htmlspecialchars($p['provider_name']); ?> <code><?php echo $p['provider_code']; ?></code></div>
                            <span class="pi-type"><?php echo strtoupper($p['auth_type']); ?></span>
                            <div class="pi-status">
                                <?php if($p['is_env_live']): ?>
                                    <span class="pi-dot pi-dot-live"></span> Live
                                <?php else: ?>
                                    <span class="pi-dot pi-dot-uat"></span> UAT
                                <?php endif; ?>
                                <?php if(!$p['is_active']): ?>
                                    · <span style="color:var(--faint);">Inactive</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="pi-body" onclick="event.stopPropagation();">
                            <div class="pi-detail">
                                <div class="pi-detail-col col-wide">
                                    <div class="pi-meta">
                                        <span><i class="fa <?php echo $p['has_qr_display']?'fa-check-circle':'fa-times-circle'; ?>"></i> QR Display</span>
                                        <span><i class="fa <?php echo $p['has_callback']?'fa-check-circle':'fa-times-circle'; ?>"></i> S2S Callback</span>
                                    </div>
                                    <div class="pi-url"><?php echo $p['is_env_live'] ? ($p['api_url_live_init']?:'—') : ($p['api_url_uat_init']?:'—'); ?></div>
                                </div>
                            </div>
                            <div class="pi-acts">
                                <button class="pb pb-s" onclick="editProvider(<?php echo $p['id_provider']; ?>)" data-tip="Edit provider settings"><i class="fa fa-pencil"></i> Edit</button>
                                <button class="pb pb-s" onclick="viewProviderUrls(<?php echo $p['id_provider']; ?>)" data-tip="View all API endpoint URLs"><i class="fa fa-link"></i> URLs</button>
                                <?php if($p['is_env_live']): ?>
                                <button class="pb pb-s" onclick="toggleProviderEnv(<?php echo $p['id_provider']; ?>,1)" data-tip="Switch to UAT sandbox"><i class="fa fa-flask"></i> Switch to UAT</button>
                                <?php else: ?>
                                <button class="pb pb-s" onclick="toggleProviderEnv(<?php echo $p['id_provider']; ?>,0)" data-tip="Switch to Live production"><i class="fa fa-rocket"></i> Switch to Live</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="pem"><i class="fa fa-plug"></i> No providers configured</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT: DEVICES -->
            <div class="pos-col">
                <div class="pos-col-hdr">
                    <span class="pos-col-title"><i class="fa fa-credit-card"></i> Devices (<?php echo count($devices); ?>)</span>
                    <button class="pb pb-p pb-s" onclick="openAddDeviceModal()" data-tip="Register a new POS terminal"><i class="fa fa-plus"></i> Add</button>
                </div>
                <div class="pos-col-desc">POS terminals linked to a provider. Click a row to manage credentials.</div>
                <div class="pos-col-body">
                    <?php if(!empty($devices)): foreach($devices as $d): ?>
                    <?php
                        $pcode=!empty($d['provider_code'])?$d['provider_code']:'';
                        $dts=array('0'=>'Card+UPI','1'=>'Card','3'=>'UPI');
                        $dtl=isset($dts[isset($d['devicetype'])?$d['devicetype']:''])?$dts[$d['devicetype']]:'—';
                    ?>
                    <div class="pi" onclick="togglePI(this)">
                        <div class="pi-row">
                            <i class="fa fa-chevron-right pi-chevron"></i>
                            <div class="pi-name">
                                <?php if($d['is_default']): ?><i class="fa fa-star pi-star"></i><?php endif; ?>
                                <?php echo htmlspecialchars($d['dispname']); ?>
                            </div>
                            <span class="pi-type"><?php echo $dtl; ?></span>
                            <div class="pi-status">
                                <span style="color:var(--faint);font-size:11px;"><?php echo !empty($d['provider_name'])?$d['provider_name']:'—'; ?></span>
                                <?php if(!isset($d['is_active'])||$d['is_active']): ?>
                                    <span class="pi-dot pi-dot-live"></span>
                                <?php else: ?>
                                    <span class="pi-dot pi-dot-off"></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="pi-body" onclick="event.stopPropagation();">
                            <div class="pc">
                                <?php
                                $secrets=array('Merchant ID'=>isset($d['merchantid'])?$d['merchantid']:'','Salt Key'=>isset($d['salt_key'])?$d['salt_key']:'','Store ID'=>isset($d['store_id'])?$d['store_id']:'','Token'=>isset($d['securitytoken'])?$d['securitytoken']:'','IMEI'=>isset($d['imei'])?$d['imei']:'');
                                foreach($secrets as $l=>$v): if(empty($v)) continue; ?>
                                <div>
                                    <div class="pcl"><?php echo $l; ?></div>
                                    <div class="pcv"><span class="vm" data-full="<?php echo htmlspecialchars($v); ?>">••••<?php echo substr($v,-4); ?></span><button class="pe" onclick="pt(this)" data-tip="Reveal for 10 seconds"><i class="fa fa-eye"></i></button></div>
                                </div>
                                <?php endforeach; ?>
                                <?php if(!empty($d['linked_device_name'])&&$d['linked_device_name']!='-'): ?>
                                <div><div class="pcl">Linked</div><div class="pcv pcv-linked"><i class="fa fa-link" style="font-size:10px;"></i> <?php echo htmlspecialchars($d['linked_device_name']); ?></div></div>
                                <?php endif; ?>
                            </div>
                            <div class="pi-acts">
                                <button class="pb pb-s" onclick="editDevice(<?php echo $d['id_device']; ?>)" data-tip="Edit credentials and settings"><i class="fa fa-pencil"></i> Edit</button>
                                <button class="pb pb-s pb-d" onclick="deleteDevice(<?php echo $d['id_device']; ?>)" data-tip="Deactivate this device"><i class="fa fa-power-off"></i></button>
                                <label class="pdef tip-r" data-tip="Set as default POS device for billing"><input type="checkbox" <?php echo $d['is_default']?'checked':''; ?> onchange="setDefaultDevice(<?php echo $d['id_device']; ?>)"> Default</label>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="pem"><i class="fa fa-credit-card"></i> No devices configured</div>
                    <?php endif; ?>
                    <div class="psn"><i class="fa fa-shield"></i> Credentials masked — click <i class="fa fa-eye" style="font-size:10px;"></i> to reveal (10s auto-hide). Access controlled via Settings → Permission.</div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- ═══ PROVIDER MODAL ═══ -->
<div class="modal fade pos-modal" id="providerModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" style="font-size:22px;color:var(--faint);opacity:1;margin:0;">&times;</button>
            <h4 class="modal-title" id="providerModalTitle"><i class="fa fa-plug"></i> Add Provider</h4>
        </div>
        <div class="modal-body"><form id="frm_pos_provider"><input type="hidden" name="id_provider" id="prov_id_provider" />
            <div class="row">
                <div class="col-sm-4"><div class="form-group"><label>Provider Name *</label><input type="text" class="form-control" name="provider_name" id="prov_provider_name" placeholder="e.g. PhonePe DQR" required /></div></div>
                <div class="col-sm-4"><div class="form-group"><label>Provider Code *</label><input type="text" class="form-control" name="provider_code" id="prov_provider_code" placeholder="e.g. phonepe_dqr" required /></div></div>
                <div class="col-sm-4"><div class="form-group"><label>Auth Type</label><select class="form-control" name="auth_type" id="prov_auth_type"><option value="token">Token</option><option value="sha256_header">SHA256 Header</option><option value="x_verify">X-VERIFY (PhonePe)</option></select></div></div>
            </div>
            <legend>UAT Endpoints</legend>
            <div class="row">
                <div class="col-sm-4"><div class="form-group"><label>Init URL</label><input type="text" class="form-control" name="api_url_uat_init" id="prov_api_url_uat_init" placeholder="https://..." /></div></div>
                <div class="col-sm-4"><div class="form-group"><label>Status URL</label><input type="text" class="form-control" name="api_url_uat_status" id="prov_api_url_uat_status" placeholder="https://..." /></div></div>
                <div class="col-sm-4"><div class="form-group"><label>Cancel URL</label><input type="text" class="form-control" name="api_url_uat_cancel" id="prov_api_url_uat_cancel" placeholder="https://..." /></div></div>
            </div>
            <legend>Live Endpoints</legend>
            <div class="row">
                <div class="col-sm-4"><div class="form-group"><label>Init URL</label><input type="text" class="form-control" name="api_url_live_init" id="prov_api_url_live_init" placeholder="https://..." /></div></div>
                <div class="col-sm-4"><div class="form-group"><label>Status URL</label><input type="text" class="form-control" name="api_url_live_status" id="prov_api_url_live_status" placeholder="https://..." /></div></div>
                <div class="col-sm-4"><div class="form-group"><label>Cancel URL</label><input type="text" class="form-control" name="api_url_live_cancel" id="prov_api_url_live_cancel" placeholder="https://..." /></div></div>
            </div>
            <legend>Configuration</legend>
            <div class="row">
                <div class="col-sm-3"><div class="form-group"><label>Environment</label><select class="form-control" name="is_env_live" id="prov_is_env_live"><option value="0">UAT (Sandbox)</option><option value="1">LIVE (Production)</option></select></div></div>
            </div>
            <div class="row" style="margin-top:4px;">
                <div class="col-sm-4"><label style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:var(--ink3);cursor:pointer;text-transform:none;letter-spacing:0;"><input type="checkbox" name="has_qr_display" id="prov_has_qr_display" value="1" style="accent-color:var(--ink);width:16px;height:16px;" /> QR Display</label></div>
                <div class="col-sm-4"><label style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:var(--ink3);cursor:pointer;text-transform:none;letter-spacing:0;"><input type="checkbox" name="has_callback" id="prov_has_callback" value="1" style="accent-color:var(--ink);width:16px;height:16px;" /> S2S Callback</label></div>
                <div class="col-sm-4"><label style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:500;color:var(--ink3);cursor:pointer;text-transform:none;letter-spacing:0;"><input type="checkbox" name="is_active" id="prov_is_active" value="1" checked style="accent-color:var(--ink);width:16px;height:16px;" /> Active</label></div>
            </div>
        </form></div>
        <div class="modal-footer">
            <button type="button" class="pb" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
            <button type="button" class="pb pb-p" onclick="saveProvider()"><i class="fa fa-save"></i> Save Provider</button>
        </div>
    </div></div>
</div>

<!-- ═══ DEVICE MODAL ═══ -->
<div class="modal fade pos-modal" id="deviceModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" style="font-size:22px;color:var(--faint);opacity:1;margin:0;">&times;</button>
            <h4 class="modal-title" id="deviceModalTitle"><i class="fa fa-credit-card"></i> Add Device</h4>
        </div>
        <div class="modal-body"><form id="frm_pos_device"><input type="hidden" name="id_device" id="dev_id_device" />
            <legend>Device Info</legend>
            <div class="row">
                <div class="col-sm-6"><div class="form-group"><label>Device Name *</label><input type="text" class="form-control" name="dispname" id="dev_dispname" placeholder="e.g. PhonePe DQR Terminal" required /></div></div>
                <div class="col-sm-6"><div class="form-group"><label>Provider *</label>
                    <select class="form-control" name="id_provider" id="dev_id_provider" required onchange="toggleDeviceFields()">
                        <option value="">-- Select Provider --</option>
                        <?php if(!empty($providers)):foreach($providers as $p):?>
                        <option value="<?php echo $p['id_provider'];?>" data-code="<?php echo $p['provider_code'];?>"><?php echo $p['provider_name'];?></option>
                        <?php endforeach;endif;?>
                    </select>
                </div></div>
            </div>
            <div class="row">
                <div class="col-sm-4"><div class="form-group"><label>Merchant ID *</label><input type="text" class="form-control" name="merchantid" id="dev_merchantid" placeholder="Gateway Merchant ID" required /></div></div>
                <div class="col-sm-4"><div class="form-group"><label>Device Type</label><select class="form-control" name="devicetype" id="dev_devicetype"><option value="0">Card + UPI</option><option value="1">Card Only</option><option value="3">UPI Only</option></select></div></div>
                <div class="col-sm-4"><div class="form-group"><label>Default Device</label><select class="form-control" name="is_default" id="dev_is_default"><option value="0">No</option><option value="1">Yes — Primary</option></select></div></div>
            </div>
            <div class="row">
                <div class="col-sm-6"><div class="form-group"><label>Linked Payment Device</label>
                    <select class="form-control" name="id_pay_device" id="dev_id_pay_device"><option value="">-- None --</option>
                    <?php if(!empty($pay_devices)):foreach($pay_devices as $pd):?><option value="<?php echo $pd['id_device'];?>"><?php echo $pd['device_name'];?></option><?php endforeach;endif;?>
                    </select>
                </div></div>
            </div>

            <div id="pinelabs_fields" style="display:none;">
                <legend>Pine Labs Credentials</legend>
                <div class="row">
                    <div class="col-sm-4"><div class="form-group"><label>Security Token</label><div class="pw-w"><input type="password" class="form-control" name="securitytoken" id="dev_securitytoken" placeholder="••••••••" /><button type="button" class="pw-t" onclick="pwt(this)"><i class="fa fa-eye"></i></button></div></div></div>
                    <div class="col-sm-4"><div class="form-group"><label>IMEI</label><input type="text" class="form-control" name="imei" id="dev_imei" placeholder="Terminal IMEI" /></div></div>
                    <div class="col-sm-4"><div class="form-group"><label>POS Code</label><input type="text" class="form-control" name="poscode" id="dev_poscode" /></div></div>
                </div>
            </div>

            <div id="phonepe_fields" style="display:none;">
                <legend>PhonePe Credentials</legend>
                <div class="row">
                    <div class="col-sm-4"><div class="form-group"><label>Salt Key</label><div class="pw-w"><input type="password" class="form-control" name="salt_key" id="dev_salt_key" placeholder="••••••••" /><button type="button" class="pw-t" onclick="pwt(this)"><i class="fa fa-eye"></i></button></div></div></div>
                    <div class="col-sm-4"><div class="form-group"><label>Salt Index</label><input type="text" class="form-control" name="salt_index" id="dev_salt_index" value="1" /></div></div>
                    <div class="col-sm-4"><div class="form-group"><label>Provider ID</label><div class="pw-w"><input type="password" class="form-control" name="provider_id" id="dev_provider_id" placeholder="X-PROVIDER-ID" /><button type="button" class="pw-t" onclick="pwt(this)"><i class="fa fa-eye"></i></button></div></div></div>
                </div>
                <div class="row">
                    <div class="col-sm-4"><div class="form-group"><label>Store ID</label><input type="text" class="form-control" name="store_id" id="dev_store_id" /></div></div>
                    <div class="col-sm-4"><div class="form-group"><label>Terminal ID</label><input type="text" class="form-control" name="terminal_id" id="dev_terminal_id" /></div></div>
                    <div class="col-sm-4"><div class="form-group"><label>Callback URL</label><input type="text" class="form-control" name="callback_url" id="dev_callback_url" placeholder="https://..." /></div></div>
                </div>
            </div>
        </form></div>
        <div class="modal-footer">
            <button type="button" class="pb" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
            <button type="button" class="pb pb-p" onclick="saveDevice()"><i class="fa fa-save"></i> Save Device</button>
        </div>
    </div></div>
</div>

<!-- URLs Modal -->
<div class="modal fade pos-modal" id="providerUrlsModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal" style="font-size:22px;color:var(--faint);opacity:1;">&times;</button><h4 class="modal-title" id="providerUrlsTitle"><i class="fa fa-link"></i> API Endpoints</h4></div>
    <div class="modal-body" id="providerUrlsBody"></div>
</div></div></div>

<script>
/* Toggle collapsible items */
function togglePI(el){ el.classList.toggle('open'); }
/* Credential reveal */
function pt(b){var s=b.previousElementSibling,f=s.getAttribute('data-full');if(b.classList.contains('on')){b.classList.remove('on');b.innerHTML='<i class="fa fa-eye"></i>';s.textContent='••••'+f.slice(-4);s.style.color='';if(b._t)clearTimeout(b._t);}else{b.classList.add('on');b.innerHTML='<i class="fa fa-eye-slash"></i>';s.textContent=f;s.style.color='var(--ink)';b._t=setTimeout(function(){b.classList.remove('on');b.innerHTML='<i class="fa fa-eye"></i>';s.textContent='••••'+f.slice(-4);s.style.color='';},10000);}}
function pwt(b){var i=b.parentElement.querySelector('input');i.type=i.type==='password'?'text':'password';b.innerHTML=i.type==='password'?'<i class="fa fa-eye"></i>':'<i class="fa fa-eye-slash"></i>';}
</script>
<script src="<?php echo base_url(); ?>assets/js/pos_settings.js"></script>
