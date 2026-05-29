# Tagging Module — Business Rules

> **Module**: Tagging
> **Last Updated**: 2026-02-24 (R6 verified)

---

## 1. Tag Code Generation Rules

| Rule ID    | Rule                                                  | Implementation                                                                                  |
| ---------- | ----------------------------------------------------- | ----------------------------------------------------------------------------------------------- |
| BR-TAG-001 | Tag code = `{product_short_code}-{sequential_number}` | Controller: `generateTagCode()` (L6148-6218) + Model: `getlastTagCode()` (L1953)                |
| BR-TAG-002 | Tag code must be unique across all branches           | `getlastTagCode()` gets max per product; controller increments                                  |
| BR-TAG-003 | Tag code sequence resets per financial year           | `get_financialyear_by_status()` referenced in old `code_number_generator()` (L1825, now unused) |

---

## 2. Weight Calculation Rules

| Rule ID    | Rule                                                       | Implementation                                 |
| ---------- | ---------------------------------------------------------- | ---------------------------------------------- |
| BR-WGT-001 | `net_wt = gross_wt - less_wt`                              | JS calculation + controller validation         |
| BR-WGT-002 | Calculation based on: 0=gross_wt, 1=net_wt, 2=gross_wt     | `calculation_based_on` field in ret_taging     |
| BR-WGT-003 | Wastage weight = (`base_weight` × `wastage_percent`) / 100 | JS: `wastage_weight` calc, model: raw SQL CASE |
| BR-WGT-004 | Stone weight deducted from gross to get metal net          | Stone details affect billing calculations      |

---

## 3. Making Charge Rules

| Rule ID   | Rule                                           | Implementation                                                          |
| --------- | ---------------------------------------------- | ----------------------------------------------------------------------- |
| BR-MC-001 | MC Type 1 = Per Piece (flat amount)            | `tag_mc_type = 1`                                                       |
| BR-MC-002 | MC Type 2 = Per Gram (rate × weight)           | `tag_mc_type = 2`                                                       |
| BR-MC-003 | MC Type 3 = % On Price                         | `tag_mc_type = 3`                                                       |
| BR-MC-004 | MC limits enforced from `ret_selling_settings` | `get_mc_va_limit()` validates against product/design/subdesign settings |

---

## 4. Pricing Rules

| Rule ID    | Rule                                                           | Implementation                      |
| ---------- | -------------------------------------------------------------- | ----------------------------------- |
| BR-PRC-001 | `sell_rate` = metal rate at tagging time                       | From `get_branchwise_rate()`        |
| BR-PRC-002 | `item_rate` = adjusted rate (may differ from sell_rate)        | User can override                   |
| BR-PRC-003 | `sales_value` = computed total (metal + MC + wastage + stones) | JS calculation                      |
| BR-PRC-004 | Metal rates are branch-specific                                | `ret_metal_rate` joined with branch |
| BR-PRC-005 | Rate calculation types: Touch-based or Purity-based            | `lot_rate_calc_type` field          |
| BR-PRC-006 | Stone calculation: 1=Weight-based, 2=Pieces-based              | `stone_calculation_based_on` field  |

---

## 5. Tag Lifecycle Rules

| Rule ID    | Rule                                               | Implementation                      |
| ---------- | -------------------------------------------------- | ----------------------------------- |
| BR-LIF-001 | New tag starts at status 0 (On Sale)               | Default insert                      |
| BR-LIF-002 | Deleted tag status = 2, restores lot balance       | `verify_otp()` / `admin_approval()` |
| BR-LIF-003 | Tag delete requires OTP or admin approval          | `send_tag_otp()` flow               |
| BR-LIF-004 | Sold tag status = 1 (set by Billing, not Tagging)  | Billing module controls             |
| BR-LIF-005 | Retagged tag status = 3 (Other Issue)              | `create_retag()`                    |
| BR-LIF-006 | In-transit tag status = 4 (set by Branch Transfer) | BT module controls                  |
| BR-LIF-007 | Tag status transitions are NOT validated centrally | No state machine — direct writes    |
| BR-LIF-008 | Duplicate tag printing requires logging            | `ret_dup_print_log` table           |

---

## 6. Lot Balance Rules

| Rule ID    | Rule                                                      | Implementation                                    |
| ---------- | --------------------------------------------------------- | ------------------------------------------------- |
| BR-LOT-001 | Creating a tag decrements lot_inward_detail balance       | `tagging('save')` deducts piece, gross_wt, net_wt |
| BR-LOT-002 | Deleting a tag restores lot_inward_detail balance         | `verify_otp()` adds back                          |
| BR-LOT-003 | Editing a tag: old balance restored, new balance deducted | `update_tagging_data()` two-step                  |
| BR-LOT-004 | Lot marked complete when all items tagged                 | `ret_lot_inwards.lot_status = 1`                  |

---

## 7. HUID (Hallmark Unique ID) Rules

| Rule ID   | Rule                                       | Implementation                      |
| --------- | ------------------------------------------ | ----------------------------------- |
| BR-HU-001 | HUID must be unique across all tags        | `validate_huid()` checks ret_taging |
| BR-HU-002 | Tag can have up to 2 HUIDs (hu_id, hu_id2) | Two columns in ret_taging           |
| BR-HU-003 | HUID validation required before save       | Controller calls `validate_huid()`  |

---

## 8. OTP Rules

| Rule ID    | Rule                                                       | Implementation                                                                 |
| ---------- | ---------------------------------------------------------- | ------------------------------------------------------------------------------ |
| BR-OTP-001 | Tag delete requires OTP sent to branch registered mobile   | `send_tag_otp()`                                                               |
| BR-OTP-002 | Order unlinking requires OTP (configurable per profile)    | `order_unlink_otp` in profile settings                                         |
| BR-OTP-003 | OTP stored in session as `tagging_otp`, compared on verify | `$this->session->set_userdata('tagging_otp', $sent_otp)` (L5102, L5188, L5452) |
| BR-OTP-004 | Admin can bypass OTP with credentials                      | `admin_approval()` (L5068)                                                     |
| BR-OTP-005 | OTP has 60-second expiry stored in `tagging_otp_exp`       | `set_userdata('tagging_otp_exp', time()+60)` (L5104, L5190, L5454)             |
| BR-OTP-006 | OTP also persisted to DB `otp` table with `otp_gen_time`   | `insertData($insData,'otp')` (L5124, L5212, L5474)                             |

---

## 9. Branch / Section Rules

| Rule ID    | Rule                                                | Implementation                                  |
| ---------- | --------------------------------------------------- | ----------------------------------------------- |
| BR-BRN-001 | Tags created at one branch can be auto-transferred  | `tagging('save')` creates BT if branches differ |
| BR-BRN-002 | Section assignment logged in section_tag_status_log | INSERT on tag create/edit                       |
| BR-BRN-003 | `current_branch` tracks physical location           | Updated by BT module                            |
| BR-BRN-004 | `cost_center` = branch where cost is allocated      | Set at creation, usually = id_branch            |

---

## 10. Category Type Rules

| Rule ID    | Rule                                                        | Implementation                           |
| ---------- | ----------------------------------------------------------- | ---------------------------------------- |
| BR-CAT-001 | cat_type 1 = Ornament                                       | Standard jewelry items                   |
| BR-CAT-002 | cat_type 2 = Bullion                                        | Gold/silver bars/coins                   |
| BR-CAT-003 | cat_type 3 = Stone                                          | Loose stones (uses UOM instead of grams) |
| BR-CAT-004 | cat_type 4 = Alloy                                          | Alloy items                              |
| BR-CAT-005 | Stone category uses `uom_gross_wt` instead of default grams | `gwt_uom_id` for UOM                     |
