# Chit Settings Module — Business Rules
> **Round**: 1 | **Date**: 2026-03-06

---

## RULE-SET-001: Metal Rate Discount Calculation
- **Source**: Controller L547-551
- **Formula**:
  - `goldrate_22ct = mjdmagoldrate_22ct - goldDiscAmt` (if enableGoldrateDisc=1)
  - `goldrate_18ct = market_gold_18ct - goldDiscAmt_18k` (if enableGoldrateDisc_18k=1)
  - `silverrate_1gm = mjdmasilverrate_1gm - silverDiscAmt` (if enableSilver_rateDisc=1)
- **Impact**: Affects all scheme payment calculations downstream

## RULE-SET-002: Permission Hierarchy
- **Source**: Controller L47-113, L282-385, Model L41-94
- **Rule**: 3-level hierarchy: Menu → Profile → Access
  - Profile 1 (Admin) gets full access on new menu items
  - Other profiles get zero access by default
  - Menu items with `submenus > 0` are parent items (treeview)
  - Special exclusions: id_menu 17 (non-admin), 18 (non-admin/non-profile-2)

## RULE-SET-003: Branch-Wise Rate Assignment
- **Source**: Controller L572-586
- **Rule**: When `branch_settings=1` AND `is_branchwise_rate=1`:
  - Each branch gets mapped to a metal rate via `branch_rate` table
  - Rate is used by `payment_model->get_metalrate_by_branch()` for scheme calculations

## RULE-SET-004: General Settings Singleton
- **Source**: Controller L1706-2122
- **Rule**: `chit_settings` table has ONE row (id_chit_settings=1)
  - Contains 60+ boolean/value flags controlling entire app behavior
  - Always updated (never inserted new rows after initial setup)
  - No versioning — changes are immediate and irreversible

## RULE-SET-005: Free Payment Cascade
- **Source**: Controller L2444-2483
- **Rule**: When `free_first_payment` is disabled (set to 0):
  - ALL schemes with `free_payment=1` are automatically updated to `free_payment=0`
  - Uses scheme_model→update_scheme_free_payment()
  - Transaction protected

## RULE-SET-006: Menu Auto-Permission on Create
- **Source**: Controller L65-75
- **Rule**: When a new menu item is created:
  1. Admin profile (id_profile=1) → view=1, add=1, edit=1, delete=1
  2. ALL other profiles → view=0, add=0, edit=0, delete=0
  - ⚠️ No cleanup if menu delete fails — access records may orphan

## RULE-SET-007: Bank Duplicate Prevention
- **Source**: Model L418-460
- **Rule**: On bank insert/update:
  - Check if `bank_name` already exists in `bank` table
  - Insert: Block if duplicate found
  - Update: Allow if name unchanged, block if duplicate with different id
  - Same pattern for department, designation, payment_mode

## RULE-SET-008: Rate Notification Trigger
- **Source**: Controller L587-605
- **Rule**: After rate save, check `canSendNoti(1)`:
  - If enabled → `send_RatesToAllUsers()` sends push via OneSignal API
  - Branch-specific notifications if branchwise rates enabled

## RULE-SET-009: Custom Account/Receipt Number Format
- **Source**: Controller L1751-1828
- **Rule**: Account and receipt numbers can be customized per format:
  - Components: `br_code`, `cmp_code`, `sch_code`, `grp_code`, `fin_yr`, `acc_num`, `hyphen`, `space`
  - When `schemeaccNo_displayFrmt=2`: Use custom format
  - Stored as concatenated string in `custom_AccDisplayFrmt`

## RULE-SET-010: Database Clear Categories
- **Source**: Controller L2147-2209
- **Rule**: Clear database operates in 10 categories:
  - masters, customer, scheme, account, wallet, log, promotions, daily_collection, metal_rates, access
  - Mode 1 = ALL, Mode 0 = Selected only
  - Also deletes image directories for customer/offers/arrivals

## RULE-SET-011: Profile Feature Toggles
- **Source**: Controller L135-258
- **Rule**: Each profile has ~40 feature toggles including:
  - `allow_acc_closing`, `req_otplogin`, `show_pending_download`, `show_cart`
  - `allow_bill_cancel`, `allow_order_cancel`, `allow_lot_cancel`
  - `bill_cancel_otp`, `credit_sales_otp_req`, `vendor_approval_otp_req`
  - `device_wise_login`, `allow_bill_type`, `allow_stock_type`
  - `metalrate_edit`, `metal_rate_datelimit`, `wedding_wastage_slab`

## RULE-SET-012: Weight Decimal & Rounding
- **Source**: Controller L1892-1894
- **Rule**: `metal_wgt_decimal` controls decimal places for weight display
  - `metal_wgt_roundoff`: 0=no rounding, 1=round off
  - Affects all weight calculations in scheme/account/payment modules

## RULE-SET-013: OTP Expiry Settings
- **Source**: Controller L1882-1886
- **Rule**: Multiple OTP expiry configurations:
  - `payOTP_exp`: Payment OTP expiry time
  - `loginOTP_exp`: Login OTP expiry time
  - `giftOTP_exp`: Gift issue OTP expiry time
  - `isOTPReqToLogin`, `isOTPRegForPayment`, `isOTPReqToGift`: Enable flags
