# Data Flow Chains — End-to-End Business Flows

> Last updated: 2026-03-26
> Source: 28 module brains

---

## Flow 1: Tag Lifecycle (Core Retail — Happy Path)

```
Lot Inward
      │
      ▼
[LOT Module] → INSERT ret_lot_inwards + ret_lot_inwards_detail
      │
      ▼
Tagging (Create Tag)
      │
[Tagging Module: admin_ret_tagging.php]
  → INSERT ret_taging (tag_status=0 Available)
  → INSERT ret_taging_stones, ret_taging_material
  → DEDUCT ret_lot_inward_detail balance
  → INSERT ret_taging_status_log
  → Optional: INSERT ret_branch_transfer (if cross-branch)
      │
      ▼
Estimation (Customer Enquiry)
      │
[Estimation Module: admin_ret_estimation.php]
  → RESERVE ret_taging (reserve_status=1)
  → INSERT ret_estimation + ret_estimation_items
  → Optional: Link to customer order
  → EDA approval queue if discount exceeds limit
      │
      ▼
Billing (Convert Estimation to Bill)
      │
[Billing Module: admin_ret_billing.php]
  → READ ret_estimation + ret_estimation_items
  → UPDATE ret_taging.tag_status = 1 (Sold)        ⚠️ Cross-module write
  → UPDATE ret_estimation.estbillid                 ⚠️ Partial commit risk (XMOD-006)
  → INSERT ret_billing, ret_bill_details
  → INSERT ret_journal (accounts)
  → Tax, wallet, voucher, scheme logic
  → INSERT ret_taging_status_log                    ⚠️ 7th module to write this table
      │
      ▼
Reports / Dashboard
  → Reads ret_taging, ret_billing, ret_estimation
  → Stock detail, sales summary, aging analysis
```

---

## Flow 2: Branch Transfer Lifecycle

```
Transfer Created
      │
[Branch Transfer: admin_ret_brntransfer.php]
  → UPDATE ret_taging.tag_status = 4 (In Transit)
  → UPDATE ret_taging.current_branch
  → INSERT ret_taging_status_log
  → INSERT ret_branch_transfer + ret_brch_transfer_tag_items
  → INSERT ret_section_tag_status_log
  → Optional: UPDATE ret_nontag_item (for NT transfers)
  → Optional: UPDATE ret_other_inventory (for packaging)
  → Optional: UPDATE customerorderdetails.current_branch
      │
      ▼ (transferred to destination branch)
      │
Transfer Downloaded / Received
      │
[Branch Transfer: download_tag()]
  → UPDATE ret_taging.tag_status = 0 (Available)
  → UPDATE ret_taging.current_branch = destination
  → INSERT ret_taging_status_log
  → UPDATE ret_branch_transfer status
  → Optional: UPDATE ret_nontag_item (restore stock)
  → Optional: UPDATE ret_other_inventory status transitions (0→4→0)
```

---

## Flow 3: Customer Scheme Journey (Chit/Savings)

```
Customer Registration
      │
[Customer Module] → INSERT customer
[Customer Module] → INSERT wallet_account (if wallet enabled)
      │
      ▼
Scheme Account Opening
      │
[Account Module: admin_manage.php]
  → INSERT scheme_account (active=1, is_closed=0)
  → SMS/WhatsApp notification
  → Passbook print
  → Optional: Gift issue at join
      │
      ▼
Monthly Installment Payments
      │
[Payment Module: admin_payment.php → SaveAll()]
  → GST calculation, metal weight calculation
  → receipt_no generation (7 modes via chit_settings)
  → INSERT payment (payment_status=1)
  → INSERT payment_mode_details
  → Wallet/referral/incentive logic
  → SMS/Email dispatch
  → Sync API (if integration enabled)
      │
      ▼
Account Closing (Maturity / Pre-close)
      │
[Account Module: close_account_form()]
  → OTP verification
  → Benefit/deduction calculation
  → UPDATE scheme_account (is_closed=1)
  → Gift issue (final), SMS notification
```

---

## Flow 4: Purchase to Tag (Karigar/Vendor Flow)

```
Purchase Order Created
      │
[Purchase Module]
  → INSERT ret_purchase_order + ret_purchase_order_items
  → Karigar assignment, rate fixing
      │
      ▼
GRN (Goods Received Note)
      │
[Purchase Module]
  → INSERT ret_grn_entry + ret_grn_items
  → QC process (ret_po_qc_issue_process)
      │
      ▼
Lot from GRN
      │
[LOT Module]
  → INSERT ret_lot_inwards (lot_from=1)
  → INSERT ret_lot_inwards_detail
      │
      ▼
Tagging from Lot → (see Flow 1)
```

---

## Flow 5: Old Metal Process

```
Old Metal Received (from Billing sale)
      │
[Old Metal Process Module]
  → READ ret_bill_old_metal_sale_details
  → CREATE pocket (ret_old_metal_pocket)
  → UPDATE ret_bill_old_metal_sale_details.is_pocketed=1
  → Optional: UPDATE ret_taging.tag_process=1 (for tagged items)
      │
      ▼
Melting / Testing / Polishing / Refining
      │
[Old Metal Process]
  → INSERT ret_old_metal_melting / ret_old_metal_testing / ret_old_metal_polishing
      │
      ▼
Receipt (converts processed metal back to inventory)
      │
[Old Metal Process]
  → INSERT ret_lot_inwards (lot_from=2/4/5)       ⚠️ Cross-module write to LOT
  → INSERT ret_lot_inwards_detail
  → UPDATE ret_nontag_item (stock increment)       ⚠️ Cross-module write
  → INSERT ret_nontag_item_log
```

---

## Flow 6: Sales Transfer (Inter-Branch Sale)

```
Transfer Request Created
      │
[Sales Transfer Module]
  → INSERT ret_billing (bill_type=13 or 14)        ⚠️ Writes to Billing's table
  → INSERT ret_bill_details
  → UPDATE ret_taging.tag_status, current_branch
  → INSERT ret_taging_status_log
  → Uses ret_billing_model.code_number_generator()  ⚠️ Cross-model method call
      │
      ▼
Download at Destination
      │
[Sales Transfer Module]
  → UPDATE ret_billing.download_date, download_by
  → UPDATE ret_taging.tag_status = 0, current_branch = destination
```

---

## Flow 7: Stock Issue/Receipt

```
Stock Issue (Exhibition / Display)
      │
[Stock Issue Module]
  → UPDATE ret_taging.tag_status = 7 (Issued)
  → INSERT ret_stock_issue + ret_stock_issue_detail
  → INSERT ret_taging_status_log
  → SMS notification
      │
      ▼
Stock Receipt (Return from display)
      │
[Stock Issue Module]
  → UPDATE ret_taging.tag_status = 0 (Available)
  → UPDATE ret_stock_issue status
  → INSERT ret_taging_status_log
```

---

## Flow 8: Customer Order to Tag to Bill

```
Customer Order Placed
      │
[Customer Order Module]
  → INSERT customerorder + customerorderdetails
  → Optional: email to karigar for order acceptance
      │
      ▼
Tag Linked to Order
      │
[Tagging / Customer Order]
  → UPDATE ret_taging.id_orderdetails
  → UPDATE ret_taging.tag_status = 8 (Reserved)
      │
      ▼
Estimation with Order Tag → Billing (see Flow 1)
      │
  → Order advance adjustment on billing
```

---

## Flow 9: Section Transfer (Within Branch)

```
Section Transfer
      │
[Section Transfer Module]
  → UPDATE ret_taging.id_section (new section)
  → UPDATE ret_taging.tag_status = 14 (lock during transfer)
  → INSERT ret_section_tag_status_log
  → UPDATE ret_taging.tag_status = 0 (unlock after transfer)
  → Optional: UPDATE ret_nontag_item (non-tag section transfer)
  → Optional: UPDATE ret_home_section_item
```

---

## Flow 10: Settings Change Impact

```
Admin changes ret_settings or chit_settings
      │
[Settings Module]
  → UPDATE ret_settings / chit_settings
  → Optional: UPDATE ../api/rate.txt (metal rate)
      │
      ▼ (IMMEDIATE — settings read fresh on every request)
      │
[ALL 28 modules] → settingsDB() or get_ret_settings() reads updated values
  ⚠️ No versioning, no cache invalidation, no audit trail for which setting changed
  ⚠️ Renaming a ret_settings key breaks all consumers silently
```
