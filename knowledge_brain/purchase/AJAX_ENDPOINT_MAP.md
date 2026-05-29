# PURCHASE MODULE — AJAX ENDPOINT MAP
> **Module:** Purchase | **Version:** 2.0 | **Date:** 2026-03-25 | **Round:** 5
> **Source:** `admin/assets/js/ret_purchase_order.js` (83,372 lines)
> **Total AJAX calls:** 187 (`url:` occurrences)

---

## Sub-Module 1: Purchase Order (Customer Order Creation/View)
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L5719 | `admin_ret_purchase/get_customer_order_pending_details` | `get_customer_order_pending_details()` | Pending customer orders |
| L5887 | `admin_ret_purchase/get_stock_repair_order_details` | `get_stock_repair_order_details()` | Repair order details |
| L6775 | `admin_ret_order/get_img_by_order_id` | Orders controller | Order images |
| L8809 | Dynamic `url` var | `purchase/save` | Create customer order |
| L8925 | `admin_ret_purchase/purchase/purchase_order` | `purchase('purchase_order')` | PO list AJAX |
| L9253 | `admin_ret_purchase/send_karigar_sms` | `send_karigar_sms()` | Karigar SMS + PDF |
| L11536 | `admin_ret_purchase/get_KarigarOrders` | `get_KarigarOrders()` | Karigar order list |
| L11776 | `admin_ret_purchase/get_KarigarOrders` | `get_KarigarOrders()` | Karigar order list (duplicate call) |
| L11968 | `admin_ret_purchase/get_pur_order_Details` | `get_pur_order_Details()` | PO details |
| L12595 | `admin_ret_purchase/update_order_close` | `update_order_close()` | Close order |
| L12789 | `admin_ret_purchase/update_order_cancel` | `update_order_cancel()` | Cancel order |
| L12945 | `admin_ret_purchase/get_karigar_pending_order_details` | `get_karigar_pending_order_details()` | Pending order details |
| L13229 | `admin_ret_purchase/update_order_delivery` | `update_order_delivery()` | Update delivery |
| L13425 | `admin_ret_purchase/update_po_approval` | `update_po_approval()` | PO approval |
| L13513 | `admin_ret_purchase/purchase/purchase_entry` | `purchase('purchase_entry')` | Purchase entry save |
| L14152 | `admin_ret_purchase/purchase/cancel_po_entry` | `purchase('cancel_po_entry')` | Cancel PO entry |
| L14204 | `admin_ret_purchase/approvalstock/ajax_purchase_list` | `approvalstock('ajax_purchase_list')` | Approval PO list |

---

## Sub-Module 2: QC Issue/Receipt
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L21296 | `admin_ret_purchase/get_purchase_issue_entry_items` | `get_purchase_issue_entry_items()` | QC issue entry items |
| L22444 | `admin_ret_purchase/update_qc_status` | `update_qc_status()` | Update QC status |
| L22360 | Dynamic `url` | `update_qc_issue()` | QC issue save |
| L21172 | Dynamic `url` | AJAX save | QC process save |
| L26818 | `admin_ret_purchase/qc_issue_receipt/purchase_issue` | `qc_issue_receipt('purchase_issue')` | QC issue list |
| L26926 | `admin_ret_purchase/qc_issue_receipt/purchase_receipt` | `qc_issue_receipt('purchase_receipt')` | QC receipt list |
| L27506 | `admin_ret_purchase/get_karigar_details` | `get_karigar_details()` | Karigar details |
| L27630 | `admin_ret_estimation/get_employee` | Estimation controller | Employee list |
| L27746 | `admin_ret_purchase/qc_issue_receipt/qc_item_details` | `qc_issue_receipt('qc_item_details')` | QC item details |
| L29366 | `admin_ret_purchase/qc_issue_receipt/ajax` | `qc_issue_receipt('ajax')` | QC list AJAX |
| L29262 | Dynamic `url` | `qc_issue_receipt()` | QC issue save |
| L66143 | `admin_ret_purchase/qc_issue_receipt/qcIssuedEdit` | `qc_issue_update()` | Edit QC issued |
| L66575 | `admin_ret_purchase/qc_issue_receipt/qcReceiptEdit` | `qc_issue_receipt()` | Edit QC receipt |
| L81812 | `admin_ret_catalog/ret_qc_cancel_reason/ajax` | Catalog controller | QC cancel reasons |
| L81927 | `admin_ret_purchase/qc_issue_receipt_cancel` | `qc_issue_receipt_cancel()` | Cancel QC issue |

---

## Sub-Module 3: Hallmarking Issue/Receipt
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L30378 | `admin_ret_purchase/halmarking_issue_receipt/get_pending_halmarking_items` | `halmarking_issue_receipt()` | Pending HM items |
| L31000 | `admin_ret_purchase/update_halmarking_issue` | `update_halmarking_issue()` | Save HM issue |
| L31100 | `admin_ret_purchase/halmarking_issue_receipt/ajax` | `halmarking_issue_receipt('ajax')` | HM list AJAX |
| L31285 | `admin_ret_purchase/halmarking_issue_receipt/get_halmarking_issue_orders` | `halmarking_issue_receipt()` | HM issue orders |
| L32005 | `admin_ret_purchase/update_halmarking_receipt` | `update_halmarking_receipt()` | Save HM receipt |

---

## Sub-Module 4: Supplier Payment
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L10133 | `admin_ret_purchase/get_po_balance` | `get_po_balance()` | PO balance |
| L10325 | `admin_ret_purchase/supplier_po_payment/get_supplier_pay_details` | `supplier_po_payment('get_supplier_pay_details')` | Supplier pay details |
| L10651 | `admin_ret_purchase/supplier_po_payment/paymenthistory` | `supplier_po_payment('paymenthistory')` | Payment history |
| L32149 | `admin_ret_purchase/purchase_payment/purchase_payment_details` | `purchase_payment()` | Purchase payment details |
| L32657 | `admin_ret_purchase/purchase_payment/purchase_payment_details` | `purchase_payment()` | Payment details (duplicate call) |
| L33581 | Dynamic `url` | Payment save | Purchase payment save |
| L33843 | Dynamic `url` | Payment save | Supplier payment save |
| L33977 | `admin_ret_billing/get_bank_acc_details` | Billing controller | Bank account dropdown |
| L34029 | `admin_ret_purchase/supplier_po_payment/ajax` | `supplier_po_payment('ajax')` | Supplier payment list |
| L34314 | `admin_ret_purchase/supplier_po_payment/cancel_pay_entry` | `supplier_po_payment()` | Cancel payment |
| L34362 | `admin_ret_purchase/supplier_po_payment/po_pay_details/{id}` | `supplier_po_payment('po_pay_details')` | Payment details by ID |
| L36612 | `admin_ret_purchase/check_cheque_number_exist` | `check_cheque_number_exist()` | Cheque number validation |
| L37501 | `admin_ret_purchase/supplier_po_payment/supplier_advance_details` | `supplier_po_payment()` | Advance details |
| L58801 | `admin_ret_purchase/get_po_balance` | `get_po_balance()` | PO balance (approval screen) |
| L58917 | `admin_ret_purchase/get_po_balance_details` | `get_po_balance_details()` | PO balance details |

---

## Sub-Module 4b: Payment Screen (New in R4/R5)
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L82819 | `admin_ret_purchase/get_pending_po_bills_after_tagging_without_pcs_with_weight` | `get_pending_po_bills_after_tagging_without_pcs_with_weight()` | Pending PO bills (weight-based) |
| L82846 | `admin_ret_purchase/getPending_payment_po_bills` | `getPending_payment_po_bills()` | Pending payment bills report |
| L82898 | `admin_ret_purchase/getPending_payment_po_bills_for_payment` | `getPending_payment_po_bills_for_payment()` | Bills for payment (left panel) |
| L83166 | `admin_ret_purchase/get_crdr_debit_entries_for_payment` | `get_crdr_debit_entries_for_payment()` | CR/DR debits for payment (right panel) |

---

## Sub-Module 5: Rate Fixing
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L34606 | `admin_ret_purchase/getOrderNosBySearch` | `getOrderNosBySearch()` | Search order numbers |
| L34858 | `admin_ret_purchase/get_approvl_ratefix_po` | `get_approvl_ratefix_po()` | Approval rate fix PO |
| L34998 | `admin_ret_purchase/get_rate_fixing_po_no` | `get_rate_fixing_po_no()` | Rate fix PO numbers |
| L35772 | `admin_ret_purchase/rate_fixing/rate_fixing_items` | `rate_fixing('rate_fixing_items')` | Rate fixing items list |
| L36084 | Dynamic `url` | `rate_fixing()` | Rate fixing save |
| L55110 | `admin_ret_purchase/update_ratefix_approval` | `update_ratefix_approval()` | Approve rate fix |
| L55194 | `admin_ret_purchase/rate_fixing` | `rate_fixing()` | Rate fixing list |
| L55437 | `admin_ret_purchase/rate_fixing/approval_rate_fix_list` | `rate_fixing('approval_rate_fix_list')` | Approval rate fix list |
| L55722 | `admin_ret_purchase/rate_fixing/cancel` | `rate_fixing('cancel')` | Cancel rate fix |
| L59931 | `admin_ret_purchase/get_approval_rate_fixing_po_no` | `get_approval_rate_fixing_po_no()` | Approval rate fix PO no |
| L60051 | `admin_ret_purchase/opening_balance_ratefixing` | `opening_balance_ratefixing()` | Opening balance rate fix |
| L71826 | `admin_ret_purchase/approval_unfixing_report/ajax` | `approval_unfixing_report('ajax')` | Unfixing report |
| L72678 | `admin_ret_purchase/approval_rate_fixed/ajax` | `approval_rate_fixed('ajax')` | Rate fixed list |

---

## Sub-Module 6: Return (Purchase Return)
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L37253 | `admin_ret_purchase/get_bill_details` | `get_bill_details()` | Bill details search |
| L37868 | `admin_ret_purchase/getpurchase_po_list` | `getpurchase_po_list()` | Purchase PO list |
| L37980 | `admin_ret_purchase/purchasereturn/ajax` | `purchasereturn('ajax')` | Return list AJAX |
| L38293 | `admin_ret_purchase/purchasereturn/cancel_ret_entry` | `purchasereturn()` | Cancel return entry |
| L38349 | `admin_ret_purchase/getreturn_po_list` | `getreturn_po_list()` | Return PO list |
| L43142 | `admin_ret_purchase/updateporeturnitems` | `updateporeturnitems()` | Update return items |
| L43358 | `admin_ret_purchase/get_supplier_sale` | `get_supplier_sale()` | Supplier sale data |
| L43806 | `admin_ret_purchase/returnpoitems` | `returnpoitems()` | Process return items |

---

## Sub-Module 7: Retagging (calls into ret_tagging)
| JS Line | AJAX URL | Controller | Purpose |
|---|---|---|---|
| L38710 | `admin_ret_tagging/retagging/ajax` | Tagging | Retagging list AJAX |
| L38904 | `admin_ret_tagging/retagging/partly_sale` | Tagging | Partly sale details |
| L39096 | `admin_ret_tagging/retagging/ajax` | Tagging | Retagging (duplicate) |
| L39286 | `admin_ret_tagging/retagging/non_tag_details` | Tagging | Non-tag details |
| L39460 | `admin_ret_tagging/retagging/non_tag_other_issue` | Tagging | Non-tag other issue |
| L39642 | `admin_ret_tagging/retagging/ajax` | Tagging | Retagging (3rd call) |
| L39864 | `admin_ret_tagging/retagging/partly_sale` | Tagging | Partly sale (duplicate) |
| L40074 | `admin_ret_tagging/retagging/non_tag_details` | Tagging | Non-tag details (dup) |
| L40222 | `admin_ret_tagging/retagging/non_tag_other_issue` | Tagging | Non-tag other issue (dup) |
| L41720 | `admin_ret_purchase/get_qc_faild_items_by_poid` | `get_qc_faild_items_by_poid()` | QC failed items by PO |
| L41950 | `admin_ret_purchase/get_qc_faild_items_by_poid` | `get_qc_faild_items_by_poid()` | QC failed items by PO (dup) |
| L42446 | `admin_ret_purchase/get_qc_faild_items_by_supid` | `get_qc_faild_items_by_supid()` | QC failed by supplier |
| L79471 | `admin_ret_purchase/retagging_report/ajax` | `retagging_report('ajax')` | Retagging report |
| L82589 | `admin_ret_purchase/getTaggedRefNo` | (none — model direct) | Tagged ref numbers |

---

## Sub-Module 8: Order Description
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L44120 | `admin_ret_purchase/order_description/save` | `order_description('save')` | Save order description |
| L44216 | `admin_ret_purchase/order_description/ajax` | `order_description('ajax')` | Order description list |
| L44453 | `admin_ret_purchase/order_description/edit/{id}` | `order_description()` | Edit order description |
| L44537 | `admin_ret_purchase/order_description/update` | `order_description('update')` | Update order description |

---

## Sub-Module 9: Karigar Metal Issue
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L45416 | `admin_ret_purchase/karigarmetalissue/ajax` | `karigarmetalissue('ajax')` | Metal issue list |
| L45693 | `admin_ret_purchase/karigarmetalissue/metalissue_cancel` | `karigarmetalissue()` | Cancel metal issue |
| L45769 | `admin_ret_purchase/karigarmetalissue/available_stock_details` | `karigarmetalissue()` | Available stock details |
| L74128 | `admin_ret_purchase/karigarmetalissue/available_stock_details` | `karigarmetalissue()` | Available stock (duplicate) |
| L81728 | `admin_ret_purchase/karigarmetalissue/available_metal_opening_stock_details` | `karigarmetalissue()` | Opening stock details |
| L82714 | `admin_ret_purchase/karigarmetalissue/search_po` | `karigarmetalissue()` | Search PO for metal issue |
| L82754 | `admin_ret_purchase/karigarmetalissue/get_approval_po_bills` | `karigarmetalissue()` | Approval PO bills for issue |

---

## Sub-Module 10: GRN Entry
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L54635 | Dynamic `url` | `grnentry()` | GRN save |
| L54723 | `admin_ret_purchase/grnentry` | `grnentry()` | GRN list AJAX |
| L55818 | `admin_ret_purchase/grnentry/cancel_grn_entry` | `grnentry('cancel_grn_entry')` | Cancel GRN entry |
| L56122 | `admin_ret_purchase/purchase/active_grns` | `purchase('active_grns')` | Active GRN list |
| L79177 | `admin_ret_purchase/grnentry/pur_edit` | `grnentry('pur_edit')` | Edit GRN/purchase |
| L79307 | `admin_ret_purchase/get_img_by_grn_id` | `get_img_by_grn_id()` | GRN images |
| L27135 | `admin_ret_purchase/get_po_ratecut_balance_details` | `get_po_ratecut_balance_details()` | PO rate cut balance |

---

## Sub-Module 11: Lot Generation / Non-Tag
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L58461 | `admin_ret_purchase/order_place` | `order_place()` | Place order |
| L58661 | `admin_ret_purchase/convert_to_normal_stock` | `convert_to_normal_stock()` | Convert approval to normal |
| L59823 | `admin_ret_purchase/generateLot` | `generateLot()` | Generate lot (AJAX) |
| L75624 | `admin_ret_purchase/nontag_receipt/ajax` | `nontag_receipt('ajax')` | Non-tag receipt list |
| L75966 | `admin_ret_purchase/nontag_receipt/get_NontagLots` | `nontag_receipt()` | Non-tag lots |
| L76214 | `admin_ret_purchase/get_lot_nontag_details` | `get_lot_nontag_details()` | Lot non-tag details |
| L76400 | `admin_ret_purchase/getNonTagLotItemDetails` | `getNonTagLotItemDetails()` | Non-tag lot item details |
| L57833 | `admin_ret_purchase/approvalstock_tag/ajax` | `approvalstock_tag('ajax')` | Approval stock tag list |
| L58197 | `admin_ret_reports/order_status/order_status` | Reports controller | Order status |

---

## Sub-Module 12: Supplier Rate Cut / HO Vault
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L61201 | `admin_ret_purchase/supplier_rate_cut/save` | `supplier_rate_cut('save')` | Save supplier rate cut |
| L61273 | `branch/branchname_list` | Branch controller | Branch list dropdown |
| L61471 | `admin_ret_purchase/supplier_rate_cut/ajax` | `supplier_rate_cut('ajax')` | Rate cut list |
| L61851 | `admin_ret_purchase/supplier_rate_cut/cancel` | `supplier_rate_cut('cancel')` | Cancel rate cut |
| L61987 | `admin_ret_purchase/headoffice_valut_report/ajax` | `headoffice_valut_report('ajax')` | HO vault report |
| L63067 | `admin_ret_purchase/headoffice_valut_report/ajax` | `headoffice_valut_report('ajax')` | HO vault (duplicate call) |

---

## Sub-Module 13: Smith Company Opening Balance / CR-DR Entry
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L73564 | `admin_ret_purchase/smith_cmpy_op_bal/save` | `smith_cmpy_op_bal('save')` | Save opening balance |
| L73700 | `admin_ret_purchase/smith_cmpy_op_bal/ajax` | `smith_cmpy_op_bal('ajax')` | Opening balance list |
| L76920 | `admin_ret_purchase/get_tds_percent` | `get_tds_percent()` | TDS percentage |
| L77050 | Dynamic `url` | `credit_debit_entry()` | CR/DR entry save |
| L77146 | `admin_ret_purchase/credit_debit_entry/ajax` | `credit_debit_entry('ajax')` | CR/DR list AJAX |
| L77630 | `admin_ret_purchase/credit_debit_entry/cd_edit/{id}` | `credit_debit_entry('cd_edit')` | Edit CR/DR entry |
| L81152 | `admin_ret_purchase/credit_debit_entry/cancel_crdr_entry` | `credit_debit_entry('cancel_crdr_entry')` | Cancel CR/DR entry |

---

## Sub-Module 14: Metal Issue Receipt
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L80628 | `admin_ret_purchase/metal_issue_receipt/getKarigarIssueRefNo` | `metal_issue_receipt('getKarigarIssueRefNo')` | Karigar issue ref no |
| L80776 | `admin_ret_purchase/metal_issue_receipt/get_metal_issue_details` | `metal_issue_receipt('get_metal_issue_details')` | Metal issue details |
| L81028 | `admin_ret_purchase/metal_issue_receipt/save` | `metal_issue_receipt('save')` | Save metal issue receipt |
| L69632 | `admin_ret_purchase/getKarigarIssueRefNo` | `getKarigarIssueRefNo()` | Karigar issue ref |
| L69780 | `admin_ret_purchase/getKarigarMetalIssueLooseStones` | `getKarigarMetalIssueLooseStones()` | Loose stones |
| L70796 | `admin_ret_purchase/getKarigarMetalIssueLooseStones` | `getKarigarMetalIssueLooseStones()` | Loose stones (duplicate) |

---

## Sub-Module 15: Karigar Update
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L56827 | `admin_ret_purchase/get_karigar_details` | `get_karigar_details()` | Karigar details |
| L57205 | `admin_ret_purchase/update_karigar` | `update_karigar()` | Update karigar |
| L57249 | `settings/company/getcountry` | Settings | Country dropdown |
| L57385 | `settings/company/getstate` | Settings | State dropdown |
| L57545 | `settings/company/getcity` | Settings | City dropdown |

---

## Sub-Module 16: Weight Gain/Loss
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L66927 | `admin_ret_purchase/weight_gain_loss/ajax` | `weight_gain_loss('ajax')` | Weight gain/loss list |
| L68329 | `admin_ret_purchase/get_Available_SupplierPo/ajax` | `get_Available_SupplierPo()` | Available supplier POs |
| L69164 | Dynamic `url` | `karigar_metal_issue()` | Metal issue process |
| L69330 | `admin_ret_reports/tag_history/ajax` | Reports controller | Tag history |

---

## Sub-Module 17: Tag History Reports (cross-module)
| JS Line | AJAX URL | Controller | Purpose |
|---|---|---|---|
| L40382 | `admin_ret_reports/tag_history/ajax` | Reports | Tag history AJAX |
| L41014 | `admin_ret_reports/tag_history/ajax` | Reports | Tag history (duplicate) |
| L69330 | `admin_ret_reports/tag_history/ajax` | Reports | Tag history (3rd) |

---

## Sub-Module 18: Catalog Lookups (Cross-Module)
| JS Line | AJAX URL | Target | Purpose |
|---|---|---|---|
| L5077 | `admin_ret_catalog/get_karigar_wise_wastage` | Catalog | Karigar wastage config |
| L5121 | `admin_ret_catalog/get_karigar_wise_stones` | Catalog | Karigar stone rates |
| L5161 | `admin_ret_catalog/get_karigar_wise_charges` | Catalog | Karigar charges |
| L5313 | `admin_ret_catalog/category/active_category` | Catalog | Active categories |
| L5353 | `admin_ret_catalog/karigar/active_list` | Catalog | Active karigars |
| L8093 | `admin_ret_catalog/get_ActiveProducts` | Catalog | Active products |
| L8493 | `admin_ret_catalog/get_active_design_products` | Catalog | Design products |
| L8609 | `admin_ret_catalog/get_ActiveSubDesigns` | Catalog | Sub-designs |
| L14429 | `admin_ret_catalog/active_metals` | Catalog | Active metals |
| L14717 | `admin_ret_catalog/get_ActiveProducts` | Catalog | Products (metal issue) |
| L14781 | `admin_ret_catalog/get_MetalCategory` | Catalog | Metal categories |
| L15209 | `admin_ret_catalog/charges/getActiveChargesList` | Catalog | Active charges |
| L15277 | `admin_ret_catalog/get_ActiveProducts` | Catalog | Products (3rd) |
| L16398 | `admin_ret_catalog/get_active_design_products` | Catalog | Design products (dup) |
| L16880 | `admin_ret_catalog/get_ActiveSubDesigns` | Catalog | Sub-designs (dup) |
| L17016 | `admin_ret_catalog/category/cat_purity` | Catalog | Category purity |
| L22564 | `admin_ret_catalog/ajax_getPurity` | Catalog | Purity data |
| L39864 | `admin_ret_catalog/category/active_category` | Catalog | Categories (dup) |
| L45981 | `admin_ret_catalog/get_sectionBranchwise` | Catalog | Section by branch |
| L46917 | `admin_ret_billing/getAllTaxgroupItems` | Billing | Tax group items |
| L51507 | `admin_ret_catalog/get_NonTagProducts` | Catalog | Non-tag products |
| L51915 | `admin_ret_catalog/category/active_category` | Catalog | Categories (3rd) |
| L56042 | `admin_ret_catalog/get_quality_code` | Catalog | Quality code |
| L76828 | Dynamic `url` | Catalog | Misc lookup |
| L81176 | `admin_ret_catalog/old_metal_cat/active_oldmetal` | Catalog | Old metal categories |
| L81256 | `admin_ret_catalog/old_metal_cat/active_oldmetal` | Catalog | Old metal (dup) |

---

## Sub-Module 19: Tagging Lookups (Cross-Module)
| JS Line | AJAX URL | Target | Purpose |
|---|---|---|---|
| L8269 | `admin_ret_purchase/get_ActiveWeightRange` | Purchase | Weight ranges |
| L8413 | `admin_ret_tagging/get_ActiveSize` | Tagging | Size dropdown |
| L24842 | `admin_ret_tagging/getStoneItems` | Tagging | Stone items |
| L24890 | `admin_ret_tagging/getStoneTypes` | Tagging | Stone types |
| L24930 | `admin_ret_tagging/get_ActiveUOM` | Tagging | Unit of measure |
| L34770 | `admin_ret_tagging/admin_ret_purchase` | Tagging | (supplier entry lookup) |

---

## Sub-Module 20: Order Sub-Modules (Product/Design/Size)
| JS Line | AJAX URL | Controller Method | Purpose |
|---|---|---|---|
| L15439 | `admin_ret_purchase/get_OrderProducts` | `get_OrderProducts()` | Order products |
| L15559 | `admin_ret_purchase/get_OrderProductsDesign` | `get_OrderProductsDesign()` | Product designs |
| L16764 | `admin_ret_purchase/get_OrderSubDesigns` | `get_OrderSubDesigns()` | Sub designs |
| L18299 | `admin_ret_purchase/delete_supplier_entry_item` | `delete_supplier_entry_item()` | Delete PO item |
| L10013 | `admin_ret_purchase/get_karigar_pending_order_details` | `get_karigar_pending_order_details()` | Pending order details |

---

## Summary Table
| Sub-Module | Internal Calls | Cross-Module Calls | Total |
|---|---|---|---|
| 1. Purchase Order | 17 | 0 | 17 |
| 2. QC Issue/Receipt | 13 | 2 | 15 |
| 3. Hallmarking | 5 | 0 | 5 |
| 4. Supplier Payment | 15 | 1 | 16 |
| 4b. Payment Screen | 4 | 0 | 4 |
| 5. Rate Fixing | 13 | 0 | 13 |
| 6. Return | 8 | 0 | 8 |
| 7. Retagging | 7 | 6 | 13 |
| 8. Order Description | 4 | 0 | 4 |
| 9. Karigar Metal Issue | 7 | 0 | 7 |
| 10. GRN Entry | 7 | 0 | 7 |
| 11. Lot/Non-Tag | 9 | 0 | 9 |
| 12. Rate Cut / HO Vault | 5 | 1 | 6 |
| 13. Smith Opg Bal / CR-DR | 7 | 0 | 7 |
| 14. Metal Issue Receipt | 6 | 0 | 6 |
| 15. Karigar Update | 2 | 3 | 5 |
| 16. Weight Gain/Loss | 3 | 1 | 4 |
| 17. Tag History | 0 | 3 | 3 |
| 18. Catalog Lookups | 0 | 26 | 26 |
| 19. Tagging Lookups | 1 | 5 | 6 |
| 20. Order Sub-Modules | 5 | 0 | 5 |
| **TOTAL** | **~137** | **~50** | **~187** |