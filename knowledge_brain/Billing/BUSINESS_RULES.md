# Billing Module — Business Rules

> **Round 9** | Rules extracted from controller, model, and JS code

---

## RULE-BIL-001: Bill Number Generation

**Formula**: Branch metal code prefix + sequential number per branch+metal_type+is_eda combination
**Implementation**: Model `code_number_generator()` L5221-5257
**Validation**: Server-side only
**Edge cases**: Race condition if two users generate simultaneously (relies on MAX query)

---

## RULE-BIL-002: Bill Date from Day Closing

**Formula**: `bill_date = (dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : dCData['entry_date'])`
**Implementation**: Controller `billing('save')` and `billing('split_save')` — L323
**Validation**: Server-side only
**Edge cases**: If day closing not done, bills post with stale date

---

## RULE-BIL-003: EDA (No-2) Bill Toggle

**Formula**: `Ctrl+Enter` toggles `is_eda` between 1 (Normal) and 2 (EDA)
**Implementation**: JS L420-502 — keyboard listener
**Validation**: Client-side — EDA hides card/cheque/NB payment options, chit utilization, gift voucher
**Edge cases**: If order advance is eda=1 and user switches to eda=2, page reloads (L444-448)

---

## RULE-BIL-004: Credit Sales OTP

**Formula**: If `credit_sales_otp_req == 1` AND is_credit is checked → OTP required before credit sale
**Implementation**: JS `send_credit_bill_otp()`, Controller `send_credit_bill_otp()` L8982-9060
**Validation**: Server-side OTP verification
**Edge cases**: Two approval types — OTP (type=1) or Mobile App Approval (type=2)

---

## RULE-BIL-005: Discount OTP/Approval

**Formula**: Discount on bill requires OTP if `bill_disc_approval_type == 1`, Mobile App if `== 2`
**Implementation**: JS `discount_otp()` / `send_mobile_approval_request()`, Controller `sendotp()` / `admin_approval()`
**Validation**: Both client-side validation + server-side OTP
**Edge cases** (BIL-CLT02):

- Three independent retail settings control whether each limit check is enforced:
    - `enable_emp_disc_limit` (1=enforce, 0=bypass emp limit)
    - `enable_mc_va_disc_limit` (1=enforce, 0=bypass MC/VA limit)
    - `enable_disc_blw_metal_rate` (1=enforce, 0=bypass disc-below-metal-rate check)
- When a toggle is `0`, the corresponding status is forced `true` (bypassed) BEFORE the unified gate runs
- OTP vs. toaster is still decided by employee master (`otp_emp_dis_approval`, `otp_mcva_dis_approval`)
- All three defaults to `1` (enabled) if not set in DB
- **Added/Updated**: 2026-03-03 (BIL-CLT02)

---

## RULE-BIL-006: Form Secret (Duplicate Prevention)

**Formula**: `form_secret` from session compared to POST — prevents double-submit
**Implementation**: Controller `billing('save')` L351-353 — `strcasecmp($form_secret, $this->session->userdata('FORM_SECRET'))`
**Validation**: Server-side only
**Edge cases**: Split save appends tag + key to form_secret for uniqueness per split

---

## RULE-BIL-007: Tag Status Management

**Formula**: On sale → `tag_status = 1` (sold). On cancel → `tag_status = 0` (available)
**Implementation**: Controller save flow L732 and cancel flow
**Validation**: Server-side. Pre-check via `get_tag_status()` before allowing sale
**Edge cases**: Partial sale items have `is_partial` flag on tag

---

## RULE-BIL-008: Metal Rate at Billing Time

**Formula**: Metal rates (22ct gold, 14ct, 9ct, 18ct, silver) captured from branch at time of billing
**Implementation**: Controller L327 `get_branchwise_rate()`, saved to `ret_billing` header
**Validation**: Server-side
**Edge cases**: Rates are branch-specific and time-frozen at bill creation

---

## RULE-BIL-009: TCS (Tax Collected at Source) Calculation

**Formula**: TCS tax = `tcs_percent × applicable_amount`
**Implementation**: Controller L438-440 — `tcs_tax_amt`, `tcs_tax_per` saved; Model `get_customer_wise_tcs_percent()` L10676-10701
**Validation**: Server-side
**Edge cases**: TCS is customer-wise and financial-year-specific

---

## RULE-BIL-010: Credit Status Mapping

**Formula**: `credit_status = (is_credit == 1) ? 2 : 1` — 2=credit pending, 1=paid
**Implementation**: Controller L410
**Validation**: Server-side
**Edge cases**: Credit due date is only set when is_credit=1

---

## RULE-BIL-011: Bill Cancellation OTP

**Formula**: Bill cancel requires OTP sent to branch registered mobile
**Implementation**: Controller `send_bill_cancel_otp()` L8698-8774, `verify_otp_for_billcancel()` L8776-8834
**Validation**: Server-side OTP
**Edge cases**: Cancellation reverses all — tags, estimations, payments, journals

---

## RULE-BIL-012: Bill Split

**Formula**: Single estimation can be split into multiple bills for different customers
**Implementation**: Controller `billing('split_save')` L303+ — each split shares `bill_split_ref_id`, has `is_bill_split = 1`
**Validation**: Server-side
**Edge cases**: Each split gets its own customer, payments, and bill number

---

## RULE-BIL-013: Advance Deposit

**Formula**: Bill can receive advance deposit — `make_as_advance` flag + `advance_deposit` amount
**Implementation**: Controller L433-436
**Validation**: Server-side
**Edge cases**: Advance vs normal payment handling

---

## RULE-BIL-014: MC/VA Limits

**Formula**: Making Charges (MC) and Value Addition (VA) have product/design-specific limits
**Implementation**: Model `get_mc_va_limit()` L8418-8564 (146 lines) — checks against product, design, sub-design, weight, lot number, branch
**Validation**: Server-side (called during save)
**Edge cases**: Different limits by gross weight ranges, branch-specific overrides

---

## RULE-BIL-015: Bill Type Behavior

**Formula**: `bill_type` determines visible sections and calculation logic
**Implementation**: JS L237-358 — switch on `bill_type` values 1-15
**Validation**: Client-side UI control
**Edge cases**: See INVARIANT_MATRIX.md for complete behavior grid

---

## RULE-BIL-016: Suspense Stock (bill_type=15)

**Formula**: `bill_type == 15` marks tag log as suspense stock (`issuspensestock = 1`)
**Implementation**: Controller L759, L795
**Validation**: Server-side
**Edge cases**: Tag tracking differs for suspense stock

---

## RULE-BIL-017: Maximum Cash Limit

**Formula**: Cash payment per bill/day has configurable maximum limit
**Implementation**: Model `get_maxcash_settings()` L4443-4451
**Validation**: Server-side check
**Edge cases**: Compliance requirement — related to India's cash transaction limits (₹2L single, ₹2L daily)

---

## RULE-BIL-018: GST Tax Per Line Item

**Formula**: Each billing item stores `total_cgst`, `total_sgst`, `total_igst`, `item_total_tax`, and `tax_group_id` individually
**Implementation**: Controller save flow L547-589 — values come from JS calculation, stored to `ret_bill_details`
**Validation**: Client-side calculation, server stores as-is
**Edge cases**: Tax group determines which rates apply (intra-state = CGST+SGST, inter-state = IGST)

---

## RULE-BIL-019: Stone Details Per Item

**Formula**: Each sold item can have multiple stone entries (stone_id, weight, pieces, price, UOM)
**Implementation**: Controller L629-667 — JSON-decoded `stone_details` from POST, inserted to `ret_billing_item_stones`
**Validation**: Server-side insert
**Edge cases**: `is_apply_in_lwt` flag controls whether stone weight is subtracted as less weight

---

## RULE-BIL-020: Other Metal Details Per Item

**Formula**: Each item can carry multiple "other metal" entries with their own purity, weight, wastage, MC
**Implementation**: Controller L670-703 — JSON-decoded `other_metal_details`, inserted to `ret_bill_other_metals`
**Validation**: Server-side insert
**Edge cases**: Calculation type (`tag_other_itm_cal_type`) determines how rate is applied

---

## RULE-BIL-021: Tag Log (Audit Trail)

**Formula**: Every tag sold gets a status log entry in `ret_taging_status_log` with `status=1` (sold)
**Implementation**: Controller L745-769 — logs from_branch, form_secret (tag+key composite), suspense flag
**Validation**: Server-side — `form_secret` is bill form_secret + tag_id + key for per-item uniqueness
**Edge cases**: Section-aware tags also get a section log entry with `from_section` / `to_section`

---

## RULE-BIL-022: Estimation Linkage on Sale

**Formula**: When item is sold from estimation, update `ret_estimation_items.purchase_status=1` and set `bil_detail_id`
**Implementation**: Controller L708-721
**Validation**: Server-side
**Edge cases**: Also updates `ret_estimation.estbillid` header with the billing ID

---

## RULE-BIL-023: Sales Reference Number

**Formula**: After bill details insert, generate a `sales_ref_no` per branch+metal_type+is_eda combo
**Implementation**: Controller L625-627 — `generateRefNo()` model method, saved to `ret_billing.sales_ref_no`
**Validation**: Server-side
**Edge cases**: Sequential — same race condition risk as bill_no generation

---

## RULE-BIL-024: Advance Transfer

**Formula**: Transfer advance from one customer receipt to another via `ret_advance_transfer` table
**Implementation**: Controller `advance_transfer('save')` L9941-10056 — creates `ret_issue_receipt` with `receipt_type=7, type=2`
**Validation**: Server-side; optional OTP via `advance_transfer_otp` setting
**Edge cases**: Each transfer row links to source `transfer_receipt_id` and stores OTP code if required

---

## RULE-BIL-025: Service Bill Flow

**Formula**: Separate billing flow for repair/service — uses `ret_service_bill` + `ret_service_bill_details` + `ret_service_bill_payment`
**Implementation**: Controller `service_bill('save')` L9171-9478 — bill_no via `service_bill_number_generator()`
**Validation**: Server-side form_secret + day closing check
**Edge cases**: Supports Cash, CC, DC, CHQ, NB payments separately; cancellation sets `bill_status=2`

---

## RULE-BIL-026: Cash Collection Denomination

**Formula**: End-of-day cash collection records denomination-wise breakdown (₹500 × 10, ₹100 × 25, etc.)
**Implementation**: Controller `cash_collection('save')` L11856+ — insert to `ret_cash_collection` header + `ret_cash_collection_details` per denomination
**Validation**: Server-side
**Edge cases**: `total_amount = sales_amount + opening_balance`; mismatch with `cash_on_hand` indicates cash discrepancy

---

## RULE-BIL-027: Multi-Payment Mode Support

**Formula**: A single bill can have simultaneous Cash + Card + Cheque + Net Banking payments, each in `ret_billing_payment`
**Implementation**: Controller save flow L470-533 — iterates over decoded JSON arrays for each payment type
**Validation**: Server-side insert per payment row
**Edge cases**: `payment_mode` values: Cash, CC, DC, CHQ, NB. Each mode has different required fields (card_no, cheque_no, bank, ref_number)

---

## RULE-BIL-028: Day Closing Dependency

**Formula**: Bill cannot be created if day closing data is missing for the branch (`sizeof($dCData) > 0` check)
**Implementation**: Controller save L356-362 — `getBranchDayClosingData()` gate
**Validation**: Server-side
**Edge cases**: Produces "Set the Day Closing Data for the selected Branch" error. This is a hard block — no workaround

---

## RULE-BIL-029: Partial Sale

**Formula**: A tag can be partially sold (`is_partial_sale = 1`) — the tag remains available for future sale of remaining items
**Implementation**: Controller L595 (`is_partial_sale` in bill details), L736-738 (updates `ret_taging.is_partial`)
**Validation**: Server-side
**Edge cases**: Only applies when `itemtype == 0`; partial tag retains `tag_status=0` (available) unlike full sale

---

## RULE-BIL-030: Service Bill Cancellation

**Formula**: Service bills can be cancelled by setting `bill_status=2` with a cancel reason
**Implementation**: Controller `cancel_service_bill()` L9547-9592 — updates `ret_service_bill` with `cancelled_date`, `cancel_reason`, `cancelled_by`
**Validation**: Same-day cancellation check via `allow_cancel` (entry_date matches bill_date)
**Edge cases**: No payment reversal logic visible — cancelled service bill payments may remain as-is

---

## RULE-BIL-031: Two-Tier Discount Distribution (Type 2 Bill Discount)

**Formula**: Type 2 (specific) bill discount is distributed only to items with MC > 0 OR VA > 0. Items with MC=0 AND VA=0 receive ₹0 discount.

**Algorithm** (multi-pass):

- **Tier 1**: Absorb discount from margin above minimum MC/VA (`mc - min_mc` and `va - min_va`). Multi-pass redistribution handles overflow when an item's capacity is exhausted.
- **Tier 2**: If Tier 1 insufficient, absorb remaining from minimum MC/VA values (requires OTP approval). Same multi-pass overflow redistribution.
- **item_blc_discount**: Last resort — only when ALL MC+VA capacity across all eligible items is exhausted.

**Implementation**: JS `calculateDiscountAllocations()` (~L7808-8034), `_distributeDiscountToTier()` (~L8035-8076), called from `calculateSaleBillRowTotal()` (~L8039)
**Validation**: Client-side only — pre-pass before row-level calculation
**Edge cases**: Type 1 (general discount) bypasses this entirely and uses the original ratio-based distribution. Console logs `BIL-CLT01 alloc[N]` for debugging.
**Added**: 2026-02-25 (BIL-CLT01 fix)

---

## RULE-BIL-032: Credit Due Amount (Accounting for Returns)

**Formula**: `due_amount = (original_due_amount) - (total_credit_collection_paid) - (SUM(return_item_cost WHERE make_as_advance = 1))`

**Condition**: Only sales returns kept as advance (`make_as_advance = 1` on the return `ret_billing` record) reduce the credit due. Cash refunds (`make_as_advance = 0`) do NOT reduce the due — the customer already received cash back, so the original debt is unchanged.

**Implementation**: Model `getBillData()` (~L5620-5637) and `getCreditBillDetails()` (~L5882-5889)
**Validation**: Server-side calculation used for display in sales return summary and credit bill search
**Edge cases**:
- If `return_item_cost` is not updated during return save, the due amount will be overstated.
- If `make_as_advance` filter is missing, BOTH cash refunds AND advance returns subtract from due (double-deduction for cash refunds).
**Added**: 2026-03-04 (BIL-INT01), **Refined**: 2026-03-07 (BIL-INT04)
**Implementation**: Model `getBillData()` (~L5620-5627)
**Validation**: Server-side calculation used for display in sales return summary
**Edge cases**: If `return_item_cost` is not updated during return save, the due amount will be overstated.
**Added**: 2026-03-04 (BIL-INT01 fix)
## RULE-BIL-032: item_type Classification in ret_bill_details

**Formula**: Every row in `ret_bill_details` has an `item_type` column that controls which section of every report the item appears in.

| item_type | Meaning | Who Sets It | Report Section |
|---|---|---|---|
| `0` | Tagged Partial Sale | JS `createSaleBillSplitRow` (when `idx=0` + `tag_id` set) | "Partly Sale" section |
| `1` | Sale Item (Old Metal) | Controller split_save L653 | Purchase section |
| `2` | Full Sale / Home Bill | JS `sale_item_type` field defaulted to 2 | "SALES" / "HOME BILL" sections |
| `3` | Partly Sale (Tagged) | Controller L3206 | "Partly Sale" section |

**Critical rule**: `item_type = 2` is required for an item to appear in the Cash Abstract "HOME BILL" section (filter: `d.item_type = 2 AND d.tag_id IS NULL`).

**Bug**: Before BIL-CLT02 fix, `createSaleBillSplitRow` in `ret_billing.js` hardcoded `sale_item_type: idx == 0 ? 0 : 2`. This meant the **first** split row of a Home Bill was saved as `item_type = 0`, causing it to vanish from the Cash Abstract report.

**Fix applied (BIL-CLT02)**:
- JS (`ret_billing.js`): `sale_item_type` is now `curRow.find(".sale_item_type").val()` — dynamic from source row
- Model (`ret_reports_model.php`): `getBillDetails` HOME BILL query expanded to `(d.item_type=2 OR (d.item_type=0 AND d.is_partial_sale=1)) AND d.tag_id IS NULL` to recover historical records

**Verification rule**: When diagnosing missing items in Cash Abstract, first check `item_type` and `tag_id` in `ret_bill_details` for the affected bill row. If `item_type=0 AND tag_id IS NULL`, the item is a Home Bill split that was saved with the wrong type.

**Added**: 2026-03-06 (BIL-CLT02 fix)

---

## RULE-BIL-033: Bill Split Piece Counting

**Formula**: In a split bill, pieces should only be counted on the **first** split row to avoid double-counting in reports.

**Implementation**:
- JS `createSaleBillSplitRow`: `sale_pcs: idx == 0 ? curRow.find(".sale_pcs").val() : 0`
- JS `ratio_apply` handler: `sale_pcs: index == 0 ? curRow.find(".sale_pcs").val() : 0`

**Bug**: Before BIL-CLT02 fix, subsequent split rows had `sale_pcs = 1` (hardcoded), causing piece counts to be inflated in report totals.

**Fix applied (BIL-CLT02)**: Changed `: 1` → `: 0` in both `createSaleBillSplitRow` and `ratio_apply`.

**Added**: 2026-03-06 (BIL-CLT02 fix)

---

## RULE-BIL-034: POS Payment Flow and Reversal Contract

**Formula**: When `enable_pos_payment == 1`, the billing form triggers POS transaction via `UploadBilledTransaction()` which calls the provider API. Payment is confirmed only when `pos_req_status = 1` (SUCCESS).

**Cancellation Gap ⚠️**: `cancel_bill()` does NOT call `cancelTransactionRequest()`. If a bill paid via POS machine is cancelled:
  1. `ret_billing.is_cancelled` is set to 1 (bill voided)
  2. `ret_billing_payment` entries remain untouched
  3. `ret_pos_requests.pos_req_status` stays at 1 (SUCCESS)
  4. The POS machine / provider has no notification of the void
  5. Settlement reconciliation will show a mismatch

**POS Transaction Status State Machine**:
  - `0` = INIT/PENDING — auto-expires after 30 min (set to 3 by `getActivePOSTransaction()`)
  - `1` = SUCCESS
  - `2` = CANCELLED — only set when `cancelTransactionRequest()` is explicitly called
  - `3` = FAILED — set by timeout auto-expire

**Implementation**: Controller `UploadBilledTransaction()` ~L12438, `cancelTransactionRequest()` ~L12567, `getTransactionStatus()` ~L12522
**Validation**: Server ↔ provider API (PhonePe, PineLabs, etc.) — callback via `phonepeCallback()`
**Risk**: FR-BIL-005 (🔴 HIGH) — documented in FLOW_RISK_MATRIX.md
**Added**: 2026-03-24 (Round 9 — POS integration documentation)

---

## RULE-BIL-035: Chit Gift Display in Bill Print

**Formula**: When `show_chit_gift_in_bill == 1` (from `ret_settings`) AND a bill utilizes a chit account that has active gifts issued (`gift_issued.status = 1, type = 1`), those gift items are displayed as main line items in the bill print copy.

**Data Flow**:
1. Model `getOtherEstimateItemsDetails()` checks `ret_billing_chit_utilization` for chit accounts used in the bill
2. For each chit account, queries `gift_issued` for active gifts (`status=1, type=1`)
3. Gift records are returned as `chit_gift_details[]` array with `gift_desc`, `quantity`, `scheme_acc_number`
4. View `bill_format_2.php` renders each gift as a main item row with sequential S.NO, gift name in DESCRIPTION, quantity in PCS, and `0.00` in AMOUNT
5. All other columns (HSN, GRS.WT, NET.WT, V.A, MC, RATE) are left blank for gift rows

**Implementation**: Model `getOtherEstimateItemsDetails()` ~L1887-1900, View `bill_format_2.php` ~L872-886
**Validation**: Server-side — setting gate + data query
**Edge cases**:
- Gift items get their own S.NO (e.g., if bill has 1 item and 4 gifts, S.NO goes 1,2,3,4,5)
- Gift amount is always `0.00` — does NOT affect bill totals
- Currently implemented only in `bill_format_2.php` — other formats may need similar changes
- Setting name is `show_chit_gift_in_bill` (not `show_gift_in_bill`)
**Added**: 2026-04-07 (Feature Request)
# Billing Module — Business Rules

> **Round 9** | Rules extracted from controller, model, and JS code

---

## RULE-BIL-001: Bill Number Generation

**Formula**: Branch metal code prefix + sequential number per branch+metal_type+is_eda combination
**Implementation**: Model `code_number_generator()` L5221-5257
**Validation**: Server-side only
**Edge cases**: Race condition if two users generate simultaneously (relies on MAX query)

---

## RULE-BIL-002: Bill Date from Day Closing

**Formula**: `bill_date = (dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : dCData['entry_date'])`
**Implementation**: Controller `billing('save')` and `billing('split_save')` — L323
**Validation**: Server-side only
**Edge cases**: If day closing not done, bills post with stale date

---

## RULE-BIL-003: EDA (No-2) Bill Toggle

**Formula**: `Ctrl+Enter` toggles `is_eda` between 1 (Normal) and 2 (EDA)
**Implementation**: JS L420-502 — keyboard listener
**Validation**: Client-side — EDA hides card/cheque/NB payment options, chit utilization, gift voucher
**Edge cases**: If order advance is eda=1 and user switches to eda=2, page reloads (L444-448)

---

## RULE-BIL-004: Credit Sales OTP

**Formula**: If `credit_sales_otp_req == 1` AND is_credit is checked → OTP required before credit sale
**Implementation**: JS `send_credit_bill_otp()`, Controller `send_credit_bill_otp()` L8982-9060
**Validation**: Server-side OTP verification
**Edge cases**: Two approval types — OTP (type=1) or Mobile App Approval (type=2)

---

## RULE-BIL-005: Discount OTP/Approval

**Formula**: Discount on bill requires OTP if `bill_disc_approval_type == 1`, Mobile App if `== 2`
**Implementation**: JS `discount_otp()` / `send_mobile_approval_request()`, Controller `sendotp()` / `admin_approval()`
**Validation**: Both client-side validation + server-side OTP
**Edge cases** (BIL-CLT02):

- Three independent retail settings control whether each limit check is enforced:
    - `enable_emp_disc_limit` (1=enforce, 0=bypass emp limit)
    - `enable_mc_va_disc_limit` (1=enforce, 0=bypass MC/VA limit)
    - `enable_disc_blw_metal_rate` (1=enforce, 0=bypass disc-below-metal-rate check)
- When a toggle is `0`, the corresponding status is forced `true` (bypassed) BEFORE the unified gate runs
- OTP vs. toaster is still decided by employee master (`otp_emp_dis_approval`, `otp_mcva_dis_approval`)
- All three defaults to `1` (enabled) if not set in DB
- **Added/Updated**: 2026-03-03 (BIL-CLT02)

---

## RULE-BIL-006: Form Secret (Duplicate Prevention)

**Formula**: `form_secret` from session compared to POST — prevents double-submit
**Implementation**: Controller `billing('save')` L351-353 — `strcasecmp($form_secret, $this->session->userdata('FORM_SECRET'))`
**Validation**: Server-side only
**Edge cases**: Split save appends tag + key to form_secret for uniqueness per split

---

## RULE-BIL-007: Tag Status Management

**Formula**: On sale → `tag_status = 1` (sold). On cancel → `tag_status = 0` (available)
**Implementation**: Controller save flow L732 and cancel flow
**Validation**: Server-side. Pre-check via `get_tag_status()` before allowing sale
**Edge cases**: Partial sale items have `is_partial` flag on tag

---

## RULE-BIL-008: Metal Rate at Billing Time

**Formula**: Metal rates (22ct gold, 14ct, 9ct, 18ct, silver) captured from branch at time of billing
**Implementation**: Controller L327 `get_branchwise_rate()`, saved to `ret_billing` header
**Validation**: Server-side
**Edge cases**: Rates are branch-specific and time-frozen at bill creation

---

## RULE-BIL-009: TCS (Tax Collected at Source) Calculation

**Formula**: TCS tax = `tcs_percent × applicable_amount`
**Implementation**: Controller L438-440 — `tcs_tax_amt`, `tcs_tax_per` saved; Model `get_customer_wise_tcs_percent()` L10676-10701
**Validation**: Server-side
**Edge cases**: TCS is customer-wise and financial-year-specific

---

## RULE-BIL-010: Credit Status Mapping

**Formula**: `credit_status = (is_credit == 1) ? 2 : 1` — 2=credit pending, 1=paid
**Implementation**: Controller L410
**Validation**: Server-side
**Edge cases**: Credit due date is only set when is_credit=1

---

## RULE-BIL-011: Bill Cancellation OTP

**Formula**: Bill cancel requires OTP sent to branch registered mobile
**Implementation**: Controller `send_bill_cancel_otp()` L8698-8774, `verify_otp_for_billcancel()` L8776-8834
**Validation**: Server-side OTP
**Edge cases**: Cancellation reverses all — tags, estimations, payments, journals

---

## RULE-BIL-012: Bill Split

**Formula**: Single estimation can be split into multiple bills for different customers
**Implementation**: Controller `billing('split_save')` L303+ — each split shares `bill_split_ref_id`, has `is_bill_split = 1`
**Validation**: Server-side
**Edge cases**: Each split gets its own customer, payments, and bill number

---

## RULE-BIL-013: Advance Deposit

**Formula**: Bill can receive advance deposit — `make_as_advance` flag + `advance_deposit` amount
**Implementation**: Controller L433-436
**Validation**: Server-side
**Edge cases**: Advance vs normal payment handling

---

## RULE-BIL-014: MC/VA Limits

**Formula**: Making Charges (MC) and Value Addition (VA) have product/design-specific limits
**Implementation**: Model `get_mc_va_limit()` L8418-8564 (146 lines) — checks against product, design, sub-design, weight, lot number, branch
**Validation**: Server-side (called during save)
**Edge cases**: Different limits by gross weight ranges, branch-specific overrides

---

## RULE-BIL-015: Bill Type Behavior

**Formula**: `bill_type` determines visible sections and calculation logic
**Implementation**: JS L237-358 — switch on `bill_type` values 1-15
**Validation**: Client-side UI control
**Edge cases**: See INVARIANT_MATRIX.md for complete behavior grid

---

## RULE-BIL-016: Suspense Stock (bill_type=15)

**Formula**: `bill_type == 15` marks tag log as suspense stock (`issuspensestock = 1`)
**Implementation**: Controller L759, L795
**Validation**: Server-side
**Edge cases**: Tag tracking differs for suspense stock

---

## RULE-BIL-017: Maximum Cash Limit

**Formula**: Cash payment per bill/day has configurable maximum limit
**Implementation**: Model `get_maxcash_settings()` L4443-4451
**Validation**: Server-side check
**Edge cases**: Compliance requirement — related to India's cash transaction limits (₹2L single, ₹2L daily)

---

## RULE-BIL-018: GST Tax Per Line Item

**Formula**: Each billing item stores `total_cgst`, `total_sgst`, `total_igst`, `item_total_tax`, and `tax_group_id` individually
**Implementation**: Controller save flow L547-589 — values come from JS calculation, stored to `ret_bill_details`
**Validation**: Client-side calculation, server stores as-is
**Edge cases**: Tax group determines which rates apply (intra-state = CGST+SGST, inter-state = IGST)

---

## RULE-BIL-019: Stone Details Per Item

**Formula**: Each sold item can have multiple stone entries (stone_id, weight, pieces, price, UOM)
**Implementation**: Controller L629-667 — JSON-decoded `stone_details` from POST, inserted to `ret_billing_item_stones`
**Validation**: Server-side insert
**Edge cases**: `is_apply_in_lwt` flag controls whether stone weight is subtracted as less weight

---

## RULE-BIL-020: Other Metal Details Per Item

**Formula**: Each item can carry multiple "other metal" entries with their own purity, weight, wastage, MC
**Implementation**: Controller L670-703 — JSON-decoded `other_metal_details`, inserted to `ret_bill_other_metals`
**Validation**: Server-side insert
**Edge cases**: Calculation type (`tag_other_itm_cal_type`) determines how rate is applied

---

## RULE-BIL-021: Tag Log (Audit Trail)

**Formula**: Every tag sold gets a status log entry in `ret_taging_status_log` with `status=1` (sold)
**Implementation**: Controller L745-769 — logs from_branch, form_secret (tag+key composite), suspense flag
**Validation**: Server-side — `form_secret` is bill form_secret + tag_id + key for per-item uniqueness
**Edge cases**: Section-aware tags also get a section log entry with `from_section` / `to_section`

---

## RULE-BIL-022: Estimation Linkage on Sale

**Formula**: When item is sold from estimation, update `ret_estimation_items.purchase_status=1` and set `bil_detail_id`
**Implementation**: Controller L708-721
**Validation**: Server-side
**Edge cases**: Also updates `ret_estimation.estbillid` header with the billing ID

---

## RULE-BIL-023: Sales Reference Number

**Formula**: After bill details insert, generate a `sales_ref_no` per branch+metal_type+is_eda combo
**Implementation**: Controller L625-627 — `generateRefNo()` model method, saved to `ret_billing.sales_ref_no`
**Validation**: Server-side
**Edge cases**: Sequential — same race condition risk as bill_no generation

---

## RULE-BIL-024: Advance Transfer

**Formula**: Transfer advance from one customer receipt to another via `ret_advance_transfer` table
**Implementation**: Controller `advance_transfer('save')` L9941-10056 — creates `ret_issue_receipt` with `receipt_type=7, type=2`
**Validation**: Server-side; optional OTP via `advance_transfer_otp` setting
**Edge cases**: Each transfer row links to source `transfer_receipt_id` and stores OTP code if required

---

## RULE-BIL-025: Service Bill Flow

**Formula**: Separate billing flow for repair/service — uses `ret_service_bill` + `ret_service_bill_details` + `ret_service_bill_payment`
**Implementation**: Controller `service_bill('save')` L9171-9478 — bill_no via `service_bill_number_generator()`
**Validation**: Server-side form_secret + day closing check
**Edge cases**: Supports Cash, CC, DC, CHQ, NB payments separately; cancellation sets `bill_status=2`

---

## RULE-BIL-026: Cash Collection Denomination

**Formula**: End-of-day cash collection records denomination-wise breakdown (₹500 × 10, ₹100 × 25, etc.)
**Implementation**: Controller `cash_collection('save')` L11856+ — insert to `ret_cash_collection` header + `ret_cash_collection_details` per denomination
**Validation**: Server-side
**Edge cases**: `total_amount = sales_amount + opening_balance`; mismatch with `cash_on_hand` indicates cash discrepancy

---

## RULE-BIL-027: Multi-Payment Mode Support

**Formula**: A single bill can have simultaneous Cash + Card + Cheque + Net Banking payments, each in `ret_billing_payment`
**Implementation**: Controller save flow L470-533 — iterates over decoded JSON arrays for each payment type
**Validation**: Server-side insert per payment row
**Edge cases**: `payment_mode` values: Cash, CC, DC, CHQ, NB. Each mode has different required fields (card_no, cheque_no, bank, ref_number)

---

## RULE-BIL-028: Day Closing Dependency

**Formula**: Bill cannot be created if day closing data is missing for the branch (`sizeof($dCData) > 0` check)
**Implementation**: Controller save L356-362 — `getBranchDayClosingData()` gate
**Validation**: Server-side
**Edge cases**: Produces "Set the Day Closing Data for the selected Branch" error. This is a hard block — no workaround

---

## RULE-BIL-029: Partial Sale

**Formula**: A tag can be partially sold (`is_partial_sale = 1`) — the tag remains available for future sale of remaining items
**Implementation**: Controller L595 (`is_partial_sale` in bill details), L736-738 (updates `ret_taging.is_partial`)
**Validation**: Server-side
**Edge cases**: Only applies when `itemtype == 0`; partial tag retains `tag_status=0` (available) unlike full sale

---

## RULE-BIL-030: Service Bill Cancellation

**Formula**: Service bills can be cancelled by setting `bill_status=2` with a cancel reason
**Implementation**: Controller `cancel_service_bill()` L9547-9592 — updates `ret_service_bill` with `cancelled_date`, `cancel_reason`, `cancelled_by`
**Validation**: Same-day cancellation check via `allow_cancel` (entry_date matches bill_date)
**Edge cases**: No payment reversal logic visible — cancelled service bill payments may remain as-is

---

## RULE-BIL-031: Two-Tier Discount Distribution (Type 2 Bill Discount)

**Formula**: Type 2 (specific) bill discount is distributed only to items with MC > 0 OR VA > 0. Items with MC=0 AND VA=0 receive ₹0 discount.

**Algorithm** (multi-pass):

- **Tier 1**: Absorb discount from margin above minimum MC/VA (`mc - min_mc` and `va - min_va`). Multi-pass redistribution handles overflow when an item's capacity is exhausted.
- **Tier 2**: If Tier 1 insufficient, absorb remaining from minimum MC/VA values (requires OTP approval). Same multi-pass overflow redistribution.
- **item_blc_discount**: Last resort — only when ALL MC+VA capacity across all eligible items is exhausted.

**Implementation**: JS `calculateDiscountAllocations()` (~L7808-8034), `_distributeDiscountToTier()` (~L8035-8076), called from `calculateSaleBillRowTotal()` (~L8039)
**Validation**: Client-side only — pre-pass before row-level calculation
**Edge cases**: Type 1 (general discount) bypasses this entirely and uses the original ratio-based distribution. Console logs `BIL-CLT01 alloc[N]` for debugging.
**Added**: 2026-02-25 (BIL-CLT01 fix)

---

## RULE-BIL-032: Credit Due Amount (Accounting for Returns)

**Formula**: `due_amount = (original_due_amount) - (total_credit_collection_paid) - (SUM(return_item_cost WHERE make_as_advance = 1))`

**Condition**: Only sales returns kept as advance (`make_as_advance = 1` on the return `ret_billing` record) reduce the credit due. Cash refunds (`make_as_advance = 0`) do NOT reduce the due — the customer already received cash back, so the original debt is unchanged.

**Implementation**: Model `getBillData()` (~L5620-5637) and `getCreditBillDetails()` (~L5882-5889)
**Validation**: Server-side calculation used for display in sales return summary and credit bill search
**Edge cases**:
- If `return_item_cost` is not updated during return save, the due amount will be overstated.
- If `make_as_advance` filter is missing, BOTH cash refunds AND advance returns subtract from due (double-deduction for cash refunds).
**Added**: 2026-03-04 (BIL-INT01), **Refined**: 2026-03-07 (BIL-INT04)
**Implementation**: Model `getBillData()` (~L5620-5627)
**Validation**: Server-side calculation used for display in sales return summary
**Edge cases**: If `return_item_cost` is not updated during return save, the due amount will be overstated.
**Added**: 2026-03-04 (BIL-INT01 fix)
## RULE-BIL-032: item_type Classification in ret_bill_details

**Formula**: Every row in `ret_bill_details` has an `item_type` column that controls which section of every report the item appears in.

| item_type | Meaning | Who Sets It | Report Section |
|---|---|---|---|
| `0` | Tagged Partial Sale | JS `createSaleBillSplitRow` (when `idx=0` + `tag_id` set) | "Partly Sale" section |
| `1` | Sale Item (Old Metal) | Controller split_save L653 | Purchase section |
| `2` | Full Sale / Home Bill | JS `sale_item_type` field defaulted to 2 | "SALES" / "HOME BILL" sections |
| `3` | Partly Sale (Tagged) | Controller L3206 | "Partly Sale" section |

**Critical rule**: `item_type = 2` is required for an item to appear in the Cash Abstract "HOME BILL" section (filter: `d.item_type = 2 AND d.tag_id IS NULL`).

**Bug**: Before BIL-CLT02 fix, `createSaleBillSplitRow` in `ret_billing.js` hardcoded `sale_item_type: idx == 0 ? 0 : 2`. This meant the **first** split row of a Home Bill was saved as `item_type = 0`, causing it to vanish from the Cash Abstract report.

**Fix applied (BIL-CLT02)**:
- JS (`ret_billing.js`): `sale_item_type` is now `curRow.find(".sale_item_type").val()` — dynamic from source row
- Model (`ret_reports_model.php`): `getBillDetails` HOME BILL query expanded to `(d.item_type=2 OR (d.item_type=0 AND d.is_partial_sale=1)) AND d.tag_id IS NULL` to recover historical records

**Verification rule**: When diagnosing missing items in Cash Abstract, first check `item_type` and `tag_id` in `ret_bill_details` for the affected bill row. If `item_type=0 AND tag_id IS NULL`, the item is a Home Bill split that was saved with the wrong type.

**Added**: 2026-03-06 (BIL-CLT02 fix)

---

## RULE-BIL-033: Bill Split Piece Counting

**Formula**: In a split bill, pieces should only be counted on the **first** split row to avoid double-counting in reports.

**Implementation**:
- JS `createSaleBillSplitRow`: `sale_pcs: idx == 0 ? curRow.find(".sale_pcs").val() : 0`
- JS `ratio_apply` handler: `sale_pcs: index == 0 ? curRow.find(".sale_pcs").val() : 0`

**Bug**: Before BIL-CLT02 fix, subsequent split rows had `sale_pcs = 1` (hardcoded), causing piece counts to be inflated in report totals.

**Fix applied (BIL-CLT02)**: Changed `: 1` → `: 0` in both `createSaleBillSplitRow` and `ratio_apply`.

**Added**: 2026-03-06 (BIL-CLT02 fix)

---

## RULE-BIL-034: POS Payment Flow and Reversal Contract

**Formula**: When `enable_pos_payment == 1`, the billing form triggers POS transaction via `UploadBilledTransaction()` which calls the provider API. Payment is confirmed only when `pos_req_status = 1` (SUCCESS).

**Cancellation Gap ⚠️**: `cancel_bill()` does NOT call `cancelTransactionRequest()`. If a bill paid via POS machine is cancelled:
  1. `ret_billing.is_cancelled` is set to 1 (bill voided)
  2. `ret_billing_payment` entries remain untouched
  3. `ret_pos_requests.pos_req_status` stays at 1 (SUCCESS)
  4. The POS machine / provider has no notification of the void
  5. Settlement reconciliation will show a mismatch

**POS Transaction Status State Machine**:
  - `0` = INIT/PENDING — auto-expires after 30 min (set to 3 by `getActivePOSTransaction()`)
  - `1` = SUCCESS
  - `2` = CANCELLED — only set when `cancelTransactionRequest()` is explicitly called
  - `3` = FAILED — set by timeout auto-expire

**Implementation**: Controller `UploadBilledTransaction()` ~L12438, `cancelTransactionRequest()` ~L12567, `getTransactionStatus()` ~L12522
**Validation**: Server ↔ provider API (PhonePe, PineLabs, etc.) — callback via `phonepeCallback()`
**Risk**: FR-BIL-005 (🔴 HIGH) — documented in FLOW_RISK_MATRIX.md
**Added**: 2026-03-24 (Round 9 — POS integration documentation)

---

## RULE-BIL-035: Chit Gift Display in Bill Print

**Formula**: When `show_chit_gift_in_bill == 1` (from `ret_settings`) AND a bill utilizes a chit account that has active gifts issued (`gift_issued.status = 1, type = 1`), those gift items are displayed as main line items in the bill print copy.

**Data Flow**:
1. Model `getOtherEstimateItemsDetails()` checks `ret_billing_chit_utilization` for chit accounts used in the bill
2. For each chit account, queries `gift_issued` for active gifts (`status=1, type=1`)
3. Gift records are returned as `chit_gift_details[]` array with `gift_desc`, `quantity`, `scheme_acc_number`
4. View `bill_format_2.php` renders each gift as a main item row with sequential S.NO, gift name in DESCRIPTION, quantity in PCS, and `0.00` in AMOUNT
5. All other columns (HSN, GRS.WT, NET.WT, V.A, MC, RATE) are left blank for gift rows

**Implementation**: Model `getOtherEstimateItemsDetails()` ~L1887-1900, View `bill_format_2.php` ~L872-886
**Validation**: Server-side — setting gate + data query
**Edge cases**:
- Gift items get their own S.NO (e.g., if bill has 1 item and 4 gifts, S.NO goes 1,2,3,4,5)
- Gift amount is always `0.00` — does NOT affect bill totals
- Currently implemented only in `bill_format_2.php` — other formats may need similar changes
- Setting name is `show_chit_gift_in_bill` (not `show_gift_in_bill`)
**Added**: 2026-04-07 (Feature Request)
