# METHOD INDEX — Section Transfer
> Built: 2026-03-14 | Last Refreshed: 2026-03-24 (Round 14)

---

## 7a. Controller Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | JS Caller |
|---|---|---|---|---|
| `index()` | L61–67 | — | — | Direct URL |
| `ret_section_transfer('list')` | L83–93 | `ret_profile` (via admin_settings_model) | — | Page load |
| `ret_section_transfer('getSectionTags')` | L97–103 | `ret_taging`, `ret_section`, `ret_product_master`, `branch`, `customerorderdetails`, `ret_estimation_items`, `ret_estimation` | — | `getSectionTags()` JS L500 |
| `ret_section_transfer('save')` | L107–549 | `ret_day_closing`, `ret_taging`, `ret_section` | `ret_taging`, `ret_section_tag_status_log`, `ret_home_section_item`, `ret_home_section_item_log`, `ret_taging_status_log`, `ret_nontag_item`, `ret_section_nontag_item_log` | `add_to_trans()` JS L1035 |
| `send_counterchange_otp()` | L558–621 | `branch`, `ret_profile` (via admin_settings) | `otp` | `counterchange_otp()` JS L1505 |
| `verify_counter_change_otp()` | L623–664 | — | `otp` | `#verfiy_counter_change_otp` click JS L1577 |

---

## 7b. Model Methods (Alphabetical)

| Method | Lines | Tables Read | Tables Written | Called By |
|---|---|---|---|---|
| `checkNonTagItemExist($data)` | L247–273 | `ret_nontag_item` | — | Controller `save` L399 |
| `checkSectionItemExist($data)` | L321–353 | `ret_home_section_item` | — | Controller `save` L201 |
| `deleteData($id_field,$id_value,$table)` | L49–59 | — | `{any}` | Not called in current controller |
| `getBranchDayClosingData($id_branch)` | L67–75 | `ret_day_closing` | — | `getSectionTags()` L87, Controller `save` L121 |
| `getBrnachOtpRegMobile($id_branch)` | L399–405 | `branch` | — | Controller `send_counterchange_otp` L567 |
| `getSectionTags($data)` | L79–239 | `ret_taging`, `ret_section`, `ret_product_master`, `branch`, `customerorderdetails` (JOIN both branches — orderno/orderid), `ret_estimation_items`, `ret_estimation` | — | Controller `getSectionTags` L99. R14: `onlyBranchSelected` guard at L229-231 filters out order-reserved tags when only branch/section filter active |
| `get_tag_details($tag_id)` | L295–303 | `ret_taging` | — | Controller `save` L137 |
| `insertData($data,$table)` | L23–33 | — | `{any}` | Controller `save` (multiple), `send_counterchange_otp` L592 |
| `sectionData($transfer_to_section)` | L309–317 | `ret_section` | — | Controller `save` L177 |
| `updateData($data,$id_field,$id_value,$table)` | L35–47 | — | `{any}` | Controller `save` L147, `verify_counter_change_otp` L646 |
| `updateNTData($data,$arith)` | L277–287 | — | `ret_nontag_item` | Controller `save` L353, L437 |
| `updatesecNTData($data,$arith)` | L361–377 | — | `ret_home_section_item` | Controller `save` L239 |
| `updatestatus($tag_id)` | L381–393 | — | `ret_taging` | Controller `save` L287 |

---

## 7c. JS → Controller AJAX Map

### Internal Endpoints (same controller)

| JS Line | JS Function | AJAX URL | Controller Method |
|---|---|---|---|
| L500 | `getSectionTags()` | `admin_ret_section_transfer/ret_section_transfer/getSectionTags` | `ret_section_transfer('getSectionTags')` |
| L1035 | `add_to_trans()` | `admin_ret_section_transfer/ret_section_transfer/save` | `ret_section_transfer('save')` |
| L1505 | `counterchange_otp()` | `admin_ret_section_transfer/send_counterchange_otp/` | `send_counterchange_otp()` |
| L1577 | `#verfiy_counter_change_otp click` | `admin_ret_section_transfer/verify_counter_change_otp/` | `verify_counter_change_otp()` |

### Cross-Module Endpoints

| JS Line | JS Function | AJAX URL | External Controller |
|---|---|---|---|
| L147 | `get_ActiveProduct()` | `admin_ret_reports/get_ActiveProduct` | `admin_ret_reports` |
| L285 | `get_ActiveSections()` | `admin_ret_catalog/get_sectionBranchwise` | `admin_ret_catalog` |
| L1133 | `getNonTaggedItem()` | `admin_ret_brntransfer/branch_transfer/getNonTaggedItem` | `admin_ret_brntransfer` |

---

## 7d. Table → Methods Reverse Map

| Table | Read By | Written By |
|---|---|---|
| `ret_taging` | `getSectionTags`, `get_tag_details` | `updateData` (id_section), `updatestatus` (tag_status→14) |
| `ret_section` | `getSectionTags`, `sectionData` | — |
| `ret_section_tag_status_log` | — | `insertData` (Controller save, type 1) |
| `ret_home_section_item` | `checkSectionItemExist` | `updatesecNTData` (arith decrement '-') |
| `ret_home_section_item_log` | — | `insertData` (Controller save, type 1, home counter) |
| `ret_taging_status_log` | — | `insertData` (Controller save, type 1, home counter) |
| `ret_nontag_item` | `checkNonTagItemExist` | `updateNTData` (arith '-' or '+'), `insertData` (new record) |
| `ret_section_nontag_item_log` | — | `insertData` (Controller save, type 2) |
| `otp` | — | `insertData` (send OTP), `updateData` (verify OTP) |
| `ret_day_closing` | `getBranchDayClosingData` | — |
| `branch` | `getBrnachOtpRegMobile` | — |
| `ret_product_master` | `getSectionTags` (JOIN) | — |
| `customerorderdetails` | `getSectionTags` (JOIN) | — |
| `ret_estimation_items` | `getSectionTags` (JOIN, est_no path) | — |
| `ret_estimation` | `getSectionTags` (JOIN, est_no path) | — |
