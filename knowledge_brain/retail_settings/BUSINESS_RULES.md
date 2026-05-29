# BUSINESS RULES — Retail Settings
> **Module:** Retail Settings | **Round:** 3 | **Date:** 2026-03-16

---

## §1 — ret_settings Rules

### BR-RSET-001: Metal Rate Auto-Discount Computation
**Trigger:** `metal_rates()` → Save type  
**Rule:** When `enableGoldrateDisc=1` in `chit_settings`, the saved `goldrate_22ct` is auto-computed as:
```
goldrate_22ct = mjdmagoldrate_22ct - goldDiscAmt
goldrate_18ct = mjdmagoldrate_18ct - goldDiscAmt_18k  (if enableGoldrateDisc_18k=1)
```
Also writes to `../api/rate.txt` for legacy API.  
**Risk:** If `goldDiscAmt` is misset in chit_settings, all calculated rates will be wrong.

---

### BR-RSET-002: SMS Gateway Selection
**Rule:** SMS gateway is selected from PHP config file, **not** from DB:  
`$this->config->item('sms_gateway')` → 1=MSG91, 2=Nettyfish, 3=SpearUC, 4=Asterixt, 5=Qikberry  
**Impact:** Changing the gateway in DB has NO effect. Must update `config/config.php`.

---

### BR-RSET-003: Profile Permission Gate
**Rule:** All module CRUD operations are gated by `access` table: `add`, `edit`, `delete`, `view` flags per `id_profile × id_menu`.  
**Method:** `get_access($url)` in `admin_settings_model.php` (L96) — called in every controller method.  
**Risk:** If `access` table rows are missing for a menu item, access defaults to denied.

---

### BR-RSET-004: ret_settings Lookup By Name (Not ID)
**Rule:** `ret_settings` is a key-value store. All lookups MUST use `name` column:
```php
// Correct
$this->db->where('name', 'is_tcs_required')->get('ret_settings')->row()->value;
// Wrong — IDs can shift on deletion
$this->db->where('id_ret_settings', 18)->get('ret_settings');
```
**Code location:** `get_ret_settings($settings)` in `admin_settings_model.php` L2402-2406.

---

### BR-RSET-005: Metal Rate File Write
**Rule:** Metal rate save writes to `../api/rate.txt` using `file_put_contents()`.  
**Format:** PHP array string (not JSON) — downstream API consumers must PHP `eval()` it.  
**Risk:** If web server process doesn't have write permission on `../api/`, rate save silently fails the file write but still saves to DB.

---

### BR-RSET-006: TCS Application Logic
**Rule:** TCS is applied if ALL conditions are met:
1. `is_tcs_required = 1` (from `ret_settings`)
2. Bill amount ≥ `tcs_min_bill_amt` (default ₹50,00,000 for B2B)
3. Customer type = B2B
Rate used: `tcs_tax_per` (default 0.1%)

---

### BR-RSET-007: Cash Validation Gate
**Rule:** `validate_cash_amt` in `ret_settings` controls cash validation mode:
- `0` = No validation (currently active)
- `1` = Validate — block cash payments above `max_cash_amt` (₹2,00,000)
- `2` = Do Not Validate  
**Warning:** Currently set to `0` meaning the `max_cash_amt` limit is NOT enforced. Significant compliance risk.

---

### BR-RSET-008: Bill Split Threshold
**Rule:** Bill split activates when bill amount is between `bill_split_min_amount` (₹1,95,000) and `bill_split_max_amount` (₹1,99,000).  
**Purpose:** Used to split bills just below ₹2L cash limit for PAN card compliance.  
**Risk:** Window is only ₹4,000 wide — edge cases at boundary may behave unpredictably.

---

### BR-RSET-009: Discount Application Type
**Rule:** `bill_discount_type = 2` → Discount applied to VA (Value Added) and MC (Making Charges), not the gold base rate.  
`bill_discount_apply_on = 1` → Discount applied specifically to VA.  
**Impact:** Affects all billing module discount calculations.

---

### BR-RSET-010: Weight Scheme Calculation Type
**Rule:** `weightschemecaltype` controls how weight scheme VA & MC are calculated:
- `1` = Manual VA & MC
- `2` = Based on **Highest** VA & MC (currently active)
- `3` = Based on Lowest VA & MC
- `4` = Based on Average VA & MC

`weight_scheme_closure_type = 3` → Tag Split-based closure method.

---

### BR-RSET-011: OTP Gate Matrix
**Rule:** Multiple OTP gates exist independently in `ret_settings`:

| Setting | Current Value | Gate |
|---|---|---|
| `is_otp_required_for_approval` | `0` | Branch transfer creation — **OFF** |
| `advance_transfer_otp` | `0` | Advance transfer — **OFF** |
| `order_delievery_otp` | `1` | Order delivery — **ON** |
| `vendor_approval_otp` | `0` | Vendor approval — **OFF** |
| `stock_issue_otp` | `1` | Stock issue — **ON** |

---

### BR-RSET-012: Stock Movement Color Thresholds
**Rule:** Stock age color coding is based on `slow_moving_*`, `non_moving_*`, `fast_moving_*` settings.  
**Format:** `days,#COLOR_HEX,flag`  
- Gold fast: 0 days → green (`#228B22`)
- Gold slow: 120+ days → yellow (`#E4D00A`)  
- Gold non-moving: 180+ days → red (`#D22B2B`)  
Same thresholds apply for silver.

---

### BR-RSET-013: Supplier Bill Entry Calculation
**Rule:** `supplier_bill_entry_calc = 2` → Manual entry (not auto-populated from supplier master).  
**Impact:** Users must manually enter bill amounts; no auto-fill from supplier rate card.

---

### BR-RSET-014: Section Required Flag
**Rule:** `is_section_required = 1` → Section selection is mandatory in billing.  
**Impact:** Bills cannot be saved without selecting a section. Affects all retail billing workflows.

---

### BR-RSET-015: ret_settings CRUD Data Flow
**Full flow for Update:**
```
User → /usersms/ret_settings_form/Edit/{id}
     → admin_usersms::ret_settings_form('Edit', $id) [L3226-3230]
     → admin_usersms_model::get_entry_recordss($id)
     → Renders: settings/retail_setting/form.php
     
User submits form → /usersms/ret_settings_post/Update/{id}
                 → admin_usersms::ret_settings_post('Update', $id) [L3276-3302]
                 → Builds array: {id_ret_settings, name, value, description, updated_by, updated_on}
                 → trans_begin()
                 → admin_usersms_model::update_ret_settings($data)
                 → ← [admin_settings_model::retail_settingsDB('update', $name, $set_array) at L2381-2384]
                 → UPDATE ret_settings SET value=?, description=?, updated_by=?, updated_on=? WHERE name=?
                 → trans_commit() OR trans_rollback()
                 → redirect to settings/retail_setting/list
```
**Key finding:** `retail_settingsDB('update')` uses `WHERE name=?` not `WHERE id_ret_settings=?`. The `name` is the primary update key.

---

## §2 — Settings Module-Specific Rules

### BR-RSET-016: clear_database() Is Unprotected
**Rule:** `settings/clear_database` is a POST method that calls `truncateFromArray()` on multiple tables.  
**No OTP, no confirmation, no profile check beyond basic login.**  
**Severity:** 🔴 CRITICAL — Production data destruction risk.

### BR-RSET-017: Permission Save Is Not Transactional Per-Row
**Rule:** `permission($type='Save')` loops through menu items saving access rows one by one.  
If the loop fails mid-iteration, partial permissions are saved — no rollback covers individual insert failures.

### BR-RSET-018: Payment Mode Duplicate Prevention
**Rule:** `paymodeDB('insert')` checks for duplicate `mode_name` before insert. Returns `false` if duplicate found (not an error response, just false).  
Same applied to `insert_dept()` and `insert_design()` — duplicate name prevention added in July 2025.

### BR-RSET-019: Company Profile Has No Concurrency Protection
**Rule:** `company_post()` does a direct `update_company()` without any optimistic locking or change log. Last writer wins.

---

*Source: admin_settings.php, admin_usersms.php, admin_settings_model.php | Round 3 | 2026-03-16*
