# Hardcoded Values — System-Wide Magic Numbers & Strings

> Aggregated from BUSINESS_RULES.md, SCHEMA_ANALYSIS.md, INVARIANT_MATRIX.md, and FORENSIC_TEMPLATE.md across all module brains.
> Last updated: 2026-03-26 (Round 3 refresh)

---

## Tag Status Values (ret_taging.tag_status)

| Value | Meaning | Modules Using It |
|---|---|---|
| 0 | Available / On Sale | Tagging, Billing (cancel), BT (download), SI (receipt) |
| 1 | Sold Out | Billing |
| 2 | Deleted | Tagging |
| 3 | Other Issue / Retagged | Tagging (retag), BT (otherIssue download) |
| 4 | In Transit | Branch Transfer |
| 5 | Deleted for Stock | Stock Issue |
| 6 | Sales Return | Billing/Returns |
| 7 | Stock Issue (Mktg/Photo) | Stock Issue |
| 8 | Repair Item | Repair module |
| 9 | Purchase Return | Purchase Return |
| 10 | EDA Sale | Billing/EDA |
| 11 | Booked for Advance | Customer Order |
| 13 | Added to Pocket | Pocket module |
| 14 | Section Transfer Lock | Section Transfer |
| 17 | Metal Issue | Metal Process |

> Values 12, 15, 16 are **unused/reserved** — not documented anywhere.

---

## Payment Status Codes (payment.payment_status)

| Value | Meaning | Set By |
|---|---|---|
| 1 | Success | Payment, Chit Reports |
| 2 | Awaiting Approval | Payment |
| 3 | Failure | Payment (gateway) |
| 4 | Cancelled | Payment, Chit Reports |
| 6 | Refund | Payment |
| 7 | Pending | Payment (online) |
| -1 | Failed (legacy) | Payment (legacy) |

---

## Bill Types (ret_billing.bill_type)

| Value | Meaning | Used By |
|---|---|---|
| 1 | Regular Sale | Billing |
| 2 | B2B Sale | Billing |
| 3 | Sales Return | Billing |
| 8 | Metal Bill | Billing |
| 13 | Sales Transfer (Outward) | Sales Transfer |
| 14 | Sales Transfer (Inward) | Sales Transfer |
| I | Issue Receipt | Billing (issue/receipt) |
| R | Receipt | Billing (issue/receipt) |

---

## Customer Order Status (customerorder.order_status / customerorderdetails.orderstatus)

| Value | Meaning | Module |
|---|---|---|
| 0 | In Cart | Customer Order |
| 1 | Order Placed | Customer Order |
| 2 | Order Rejected (Cart) | Customer Order |
| 3 | Work in Progress / Assigned | Customer Order |
| 4 | Completed / Ready | Customer Order |
| 5 | Delivered | Customer Order |
| 6 | New Order Rejected | Customer Order |
| 8 | Reject after Assigned | Customer Order |

---

## Customer Order Types (customerorder.order_type)

| Value | Meaning | Used By |
|---|---|---|
| 1 | Stock Order | Customer Order |
| 2 | Customer Order | Customer Order |
| 3 | Customer Repair | Customer Order |
| 4 | Stock Repair | Customer Order |
| 5 | Tagged Order | Customer Order |
| 6 | Home Bill | Customer Order |

---

## Customer Source (customer.added_by)

| Value | Meaning | Set By |
|---|---|---|
| 0 | WebApp | Online registration |
| 1 | Admin | Admin panel (⚠️ always 1 in admin add — CUS-BUG-008) |
| 2 | MobileApp | Mobile app |
| 3 | CollectionApp | Chit Collection App |
| 4 | RetailApp | Retail App |
| 5 | Sync | ERP Sync |
| 6 | Import | Bulk import |

---

## Scheme Type (scheme.scheme_type)

| Value | Meaning | Used By |
|---|---|---|
| 0 | Amount-based | Payment, Account, Scheme |
| 1 | Weight-based | Payment, Account, Scheme |
| 2 | Amount to Weight | Payment, Account, Scheme |
| 3 | Flexible | Payment, Account, Scheme |

## Flexible Scheme Sub-Type (scheme.flexible_sch_type)

| Value | Meaning | Weight Calc |
|---|---|---|
| 2 | Amount-based (with wgt_convert) | If `wgt_convert != 2` → `amount_to_weight()` |
| 3 | Weight-based | `amount_to_weight()` |
| 4 | Type 4 | `amount_to_weight()` |
| 5 | Type 5 (with wgt_store_as) | If `wgt_store_as == 1` → `amount_to_weight()` |
| 7 | Type 7 | `amount_to_weight()` |
| 8 | Type 8 | `amount_to_weight()` (no GST deduction) |

---

## Payment Due Types (payment.due_type)

| Code | Meaning | Used By |
|---|---|---|
| ND | Normal Due | Payment |
| PD | Pending Due | Payment |
| AD | Advance Due | Payment |
| GA | General Advance | Payment |
| A | Advance (online) | Payment |
| P | Pending (online) | Payment |
| S | Split | Payment |
| PN | Pending + Normal | Payment |
| AN | Advance + Normal | Payment |

---

## Payment Source (payment.added_by)

| Value | Meaning | Set By |
|---|---|---|
| 0 | Admin Panel | Manual entry |
| 1 | Mobile App | Online payment |
| 2 | Website | Online payment |
| 3 | Gateway Callback | Auto-triggered |

---

## Purchase Bill Type (ret_purchase_order.gst_bill_type)

| Value | Meaning | Ref No Format |
|---|---|---|
| P | Regular | P-XXXX |
| PM | Metal | PM-XXXX |
| PA | Accessories | PA-XXXX |

---

## KYC Type (kyc.kyc_type)

| Value | Meaning | Module |
|---|---|---|
| 1 | Bank/Passbook/Cheque | Customer |
| 2 | PAN | Customer |
| 3 | Aadhar | Customer |

---

## Lot From Values (ret_lot_inwards.lot_from)

| Value | Meaning | Created By |
|---|---|---|
| 1 | Standard GRN | LOT |
| 2 | Melting Receipt | Old Metal Process |
| 4 | Testing Receipt | Old Metal Process |
| 5 | Polishing Receipt | Old Metal Process |

> Value 3 is missing — Refining does NOT create a lot record.

---

## Branch Transfer Status (ret_branch_transfer.status)

| Value | Meaning |
|---|---|
| 1 | Pending |
| 2 | In Transit |
| 3 | Cancelled |
| 4 | Downloaded |

---

## Branch Transfer Item Types (ret_branch_transfer.transfer_item_type)

| Value | Meaning | Child Table |
|---|---|---|
| 1 | Tagged Items | `ret_brch_transfer_tag_items` |
| 2 | Non-Tagged Items | `ret_brch_transfer_non_tag_items` |
| 3 | Old Metal / SR / PS | `ret_brch_transfer_old_metal` |
| 4 | Packaging | `ret_branch_transfer_other_inventory` |
| 5 | Repair Orders | `ret_repair_orders_trans_data` |

## Branch Transfer Old Metal Item Type (ret_brch_transfer_old_metal.item_type)

| Value | Meaning |
|---|---|
| 1 | Old Metal |
| 2 | Sales Return |
| 3 | Partly Sale |

---

## Stock Issue Types

| Field | Value | Meaning |
|---|---|---|
| `stock_type` | 1 | Tagged Items |
| `stock_type` | 2 | Non-Tagged Items |
| `issued_to` | 1 | Customer |
| `issued_to` | 2 | Employee |
| `issued_to` | 3 | Karigar |
| `status` | 0 | Pending |
| `status` | 1 | Issued |
| `status` | 2 | Rejected |
| Detail `status` | 1 | Issued |
| Detail `status` | 3 | Received/Returned |

---

## Old Metal Process Types

| Value | Meaning | Receipt Creates Lot? |
|---|---|---|
| 1 | Melting | Yes (lot_from=2) |
| 2 | Testing | Yes (lot_from=4) |
| 3 | Refining | No |
| 4 | Polishing | Yes (lot_from=5) |

## Old Metal Melting Status (ret_old_metal_melting.melting_status)

| Value | Meaning |
|---|---|
| 0 | Pending |
| 1 | Receipt Done |
| 2 | Testing Issued |
| 3 | Testing Complete |
| 4 | Refining Issued |
| 5-6 | Stock |

---

## LOT Types (ret_lot_inwards.lot_type)

| Value | Meaning |
|---|---|
| 1 | Normal |
| 2 | Customer (linked to order) |
| 3 | Repair |

## LOT Origin (ret_lot_inwards.lot_from) — Extended

| Value | Meaning | Created By |
|---|---|---|
| 1 | Manual / Standard GRN | LOT module |
| 2 | Melting Receipt | Old Metal Process |
| 4 | Testing Receipt | Old Metal Process |
| 5 | Polishing Receipt | Old Metal Process |
| 7 | Merge | LOT module (merge) |

---

## Other Inventory Issue Form (ret_other_invnetory_issue.issue_form)

| Value | Meaning | Set By |
|---|---|---|
| 1 | Issued via Billing | `admin_ret_billing` L4506 |
| 2 | Issued via OI Module | OI controller L915 |

## Other Inventory Log Status (ret_other_inventory_purchase_items_log.status)

| Value | Meaning | Module |
|---|---|---|
| 0 | Inward (purchase/receipt) | OI |
| 1 | Issue (to customer/billing) | OI / Billing |
| 3 | BT Other Issue | Branch Transfer |
| 4 | BT In-Transit | Branch Transfer |

---

## Employee Device App Type (employee_devices.app_type)

| Value | Meaning |
|---|---|
| 1 | Collection App |
| 2 | Estimation App |

## Employee Device Status (employee_devices.device_status)

| Value | Meaning |
|---|---|
| 0 | Disabled |
| 1 | Enabled |

---

## Karigar Type (ret_karigar.karigar_for)

| Value | Meaning | Used By |
|---|---|---|
| 1 | Karigar (manufacturer) | Purchase, LOT, Tagging |
| 2 | Vendor (supplier) | Purchase |
| 4 | OI Supplier | Other Inventory |

---

## Receipt Number Modes (chit_settings based)

| Mode | Format | Used By |
|---|---|---|
| 1 | Branch prefix + auto-increment | Payment |
| 2 | Scheme prefix + auto-increment | Payment |
| 3 | Year prefix + auto-increment | Payment |
| 4-7 | Various compound formats | Payment |

---

## SMS Gateway IDs (duplicated 8+ times)

| Gateway | Identifier | Used By |
|---|---|---|
| MSG91 | `sendSMS_MSG91()` | Account, Payment, Customer, Employee, etc. |
| Nettyfish | `sendSMS_Nettyfish()` | Same |
| SpearUC | `sendSMS_SpearUC()` | Same |
| Asterixt | `sendSMS_Asterixt()` | Same |
| Qikberry | `sendSMS_Qikberry()` | Same |

---

## Common Config Flags (chit_settings / ret_settings)

| Setting | Values | Impact |
|---|---|---|
| `validate_cash_amt` | 0/1 | 0 = cash limit NOT enforced |
| `is_otp_required_for_approval` | 0/1 | OTP flow for BT/approval |
| `bill_discount_type` | VA/MC | Discount calculation method |
| `calculation_based_on` | 1/2 | Billing calculation mode |
| `chit_rate_calculation_type` | 1/2/3 | Scheme rate method |
| `is_tcs_required` | 0/1 | TCS calculation toggle |
| `is_section_required` | 0/1 | Mandatory section selection |
| `allow_eda_button` | profile/branch JSON | EDA visibility |
