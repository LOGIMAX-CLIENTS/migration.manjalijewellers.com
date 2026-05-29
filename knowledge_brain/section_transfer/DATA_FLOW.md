# DATA FLOW — Section Transfer
> Built: 2026-03-14 | Round 1

---

## Flow 0: Page Load

1. Browser hits `/admin_ret_section_transfer/ret_section_transfer/list`
2. Controller `ret_section_transfer('list')` → fetches `counter_change_otp` flag from `admin_settings_model->profileDB()`
3. Passes `$data['counter_change_otp']` to view `section_transfer/list`
4. View renders: hidden input `#allow_order_item_cancel_otp` with OTP flag value
5. JS `$(document).ready` → calls `get_ActiveSections(id_branch)` + `get_ActiveProduct()`

### Initialization AJAX calls (parallel on page load)
| Call | Destination | Populates |
|---|---|---|
| `get_ActiveProduct()` | `admin_ret_reports/get_ActiveProduct` | `#prod_select`, `#prod_filter` dropdowns |
| `get_ActiveSections(id_branch)` | `admin_ret_catalog/get_sectionBranchwise` | `#select_frm_section`, `#select_to_section` dropdowns |

---

## Flow 1: Tagged Item Search

**Trigger**: User clicks `#section_tag_search` (or types ≥4 chars in `#tag_code`/`#tag_code_old`)

1. **JS validation** (L408–447):
   - Branch must be selected
   - Product must be selected
   - If `section_item_type == 1` → call `getSectionTags()`

2. **JS `getSectionTags()`** (L472–789):
   - POST to `admin_ret_section_transfer/ret_section_transfer/getSectionTags`
   - Sends: `id_branch`, `id_section`, `tag_code`, `old_tag_id`, `est_no`, `id_product`

3. **Controller** `ret_section_transfer('getSectionTags')` (L97–103):
   - Calls `ret_section_transfer_model->getSectionTags($_POST)`
   - Returns `json_encode($data)`

4. **Model `getSectionTags($data)`** (L79–239):
   - Checks `est_no`: 
     - If empty: simple `ret_taging` query (no estimation join)
     - If set: adds `ret_estimation_items` + `ret_estimation` JOINs, filters by date and esti_no
   - Both branches JOIN: `ret_section`, `ret_product_master`, `branch`, `customerorderdetails`
   - Filter: `tag_status=0`
   - If only branch/section selected (no tag code/est): appends `AND (id_orderdetails IS NULL OR id_orderdetails='')`
   - Returns array of tag rows

5. **JS response** (L540–729):
   - For each tag: checks if already in table → skip with warning
   - Checks if tag has `orderno` → blocks with warning
   - Appends row to `#section_trans_list tbody`
   - Clears search inputs, calls `calculateSectiontotal()`

---

## Flow 2: Tagged Transfer (Save — Type 1)

**Trigger**: User selects checkboxes and clicks `#section_transfer`

1. **JS click handler** (L900–1009):
   - Validates `#select_to_section` not empty
   - If `trans_type==1`: collects checked rows → `SectionTagData` array with `{tag_id, id_branch, trans_from_section, pcs, grs_wt, net_wt}`
   - If `allow_order_item_cancel_otp == 1` → show OTP modal, else call `add_to_trans()` directly

2. *(If OTP required)* OTP flow: see Flow 4

3. **JS `add_to_trans()`** (L1015–1069):
   - POST to `admin_ret_section_transfer/ret_section_transfer/save`
   - Sends: `trans_data[]`, `section_item_type`, `trans_to_section`, `id_branch`

4. **Controller `save`** (L107–549):
   - Reads `trans_to_section`, `id_branch`
   - Gets day-closing datetime: `admin_settings_model->getBranchDayClosingData()` → `$datetime`
   - `$this->db->trans_begin()`
   - **foreach** `trans_data`:
     - If `section_item_type == 1`:
       - `get_tag_details($tag_id)` → checks `tag_status == 0`
       - `updateData(['id_section'=>$transfer_to_section], 'tag_id', $tag_id, 'ret_taging')` — moves tag to new section
       - `insertData($secTags, 'ret_section_tag_status_log')` — log the transfer
       - `sectionData($transfer_to_section)` → checks `is_home_bill_counter`
       - **If home counter**:
         - `checkSectionItemExist()` → `ret_home_section_item`
         - If exists: `updatesecNTData($section_item, '-')` — DECREMENTS home stock (⚠️ bug candidate: should DECREMENT from OLD section, INCREMENT at new)
         - `insertData($section_item_log, 'ret_home_section_item_log')`
         - `updatestatus($tag_id)` → sets `tag_status=14` in `ret_taging`
         - `insertData($taging_status_log, 'ret_taging_status_log')`
   - `trans_status() === TRUE` → `trans_commit()`, success JSON
   - else: `trans_rollback()`, failure JSON
   - JS: `window.location.reload()`

---

## Flow 3: Non-Tagged Transfer (Save — Type 2)

**Trigger**: User selects NT items (type=2) and clicks `#section_transfer`

1. User selects `Non Tagged` radio → JS shows NT section filters
2. Clicks Search → `getNonTaggedItem()` (L1125):
   - Calls `admin_ret_brntransfer/branch_transfer/getNonTaggedItem` (CROSS-MODULE)
   - Sends: `prodId`, `from_brn`
   - Fills `#bt_nt_search_list` DataTable with editable pcs/wt fields

3. User edits quantities (validates: pcs ≤ balance, gwt ≥ nwt) → checks rows → Transfer

4. **JS `#section_transfer` handler** (type==2 path, L950–968):
   - Collects: `{id_nontag_item, branch, id_section, product, design, id_sub_design, no_of_piece, gross_wt, net_wt}`

5. **Controller `save`** (type==2 path, L317–517):
   - **Deduct from source** (if `id_nontag_item != ''`):
     - `updateNTData($nt_data, '-')` — DECREMENTS `ret_nontag_item`
     - `insertData($section_nontag_log, 'ret_section_nontag_item_log')` — status=4, to_section=NULL ⚠️
   - **Add to destination**:
     - `checkNonTagItemExist($nt_data)` — looks for matching record at to_section
     - If exists: `updateNTData($nt_data, '+')` — INCREMENTS
     - If not: `insertData($nt_data, 'ret_nontag_item')` — new record
     - `insertData($section_nontag_log, 'ret_section_nontag_item_log')` — status=0

---

## Flow 4: OTP Flow (Counter-Change Gate)

**Trigger**: Transfer attempted when `allow_order_item_cancel_otp == 1`

1. JS shows `#confirm-sec_transotp` modal with confirmation step
2. User clicks `#send_counter_change_otp_yes` (L1419):
   - Switches modal to OTP input display
   - Calls `counterchange_otp(tot_grs_wt, tot_pcs)`

3. **`counterchange_otp()`** (L1495–1565):
   - POST to `send_counterchange_otp/`
   - Sends: `total_gwt`, `tot_pcs`, `id_branch`, `from_section`, `to_section`

4. **Controller `send_counterchange_otp()`** (L558–621):
   - Gets OTP mobile(s) from `getBrnachOtpRegMobile($id_branch)` → `branch.otp_verif_mobileno`
   - For each mobile: generates `mt_rand(1001,9999)`, stores in `session('counterchange_otp')`, expiry `time()+300`
   - `insertData($insData, 'otp')` — records OTP in DB
   - Returns `{status:true, OTP: $sent_otp}` ⚠️ OTP returned in response (development mode)

5. User enters OTP → clicks `#verfiy_counter_change_otp`:
   - POST to `verify_counter_change_otp/`
   - Sends `otp`

6. **Controller `verify_counter_change_otp()`** (L623–664):
   - Reads `session('counterchange_otp')`, compares to POST otp
   - Checks `time() >= session('counterchange_otp_exp')` → expired
   - If valid & not expired: `updateData(['is_verified'=>1, ...], 'otp_code', $post_otp, 'otp')`
   - Returns `{status:true}` → JS modal closes, calls `add_to_trans(SectionTagData)`

---

## Flow 5: Branch Change

**Trigger**: `#branch_select` change event (L227)

- Calls `get_ActiveSections(this.value)`
- Repopulates `#select_frm_section` and `#select_to_section` dropdowns via `admin_ret_catalog/get_sectionBranchwise`
