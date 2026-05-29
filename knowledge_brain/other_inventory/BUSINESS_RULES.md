# BUSINESS RULES — Other Inventory
> **Module:** Other Inventory | **Built:** 2026-03-14

---

## RULE-OI-001: Stock Calculation Formula

**Formula:**
```
Closing Stock = Opening Balance (pieces) + Inward (pieces) - Outward (pieces)
Closing Amount = Opening Amount + Inward Amount - Outward Amount
```

**Implementation:**
- SQL in `other_inventory_stock()` — Model L319–388
- PHP aggregation in controller `stock_details()` — Controller L834–844
- Opening balance: log records WHERE date <= (from_date - 1 day)
- Inward: log WHERE status=0 AND to_branch={branch} AND date IN range
- Outward: log WHERE (status=1 OR status=3 OR status=4) AND from_branch={branch} AND date IN range

**Validation:** Server-side only (SQL aggregation)
**Log status codes (CONFIRMED Round 2):**
- 0 = Inward: New tagged pieces arrive at branch (from purchase or branch transfer receipt)
- 1 = Issue: Items issued to customer via OI module OR via Billing (`admin_ret_billing` L4565)
- 3 = Branch Transfer Other Issue: `is_other_issue=1` in branch transfer; e.g., items transferred from OI stock into another BT context (`admin_ret_brntransfer` L608)
- 4 = Branch Transfer In-Transit: Items removed from source branch pending download at destination (`admin_ret_brntransfer` L812)

---

## RULE-OI-002: Issue Preference (FIFO/FILO)

**Formula:**
- FIFO (issue_preference=1): Select oldest pieces first → ORDER BY pur_item_detail_id ASC
- FILO (issue_preference=2): Select newest pieces first → ORDER BY pur_item_detail_id DESC
- Controlled by `issue_preference` column in `ret_other_inventory_item`

**Implementation:**
- `get_other_inventory_purchase_items_details()` — Model L436–444
- Query: `ORDER BY pur_item_detail_id ASC/DESC LIMIT {total_pcs}`

**Validation:** Client-side selection; server-side enforcement at issue time
**Edge case:** If item has fewer pieces than requested, LIMIT silently returns fewer records — no error raised

---

## RULE-OI-003: Purchase Reference Number Generation

**Formula:**
```
ref_no = MAX(otr_inven_pur_order_ref) + 1, zero-padded to 5 digits
Example: 00001, 00002, ..., 99999
```

**Implementation:** `generatePurNo()` — Model L76–98

**⚠️ Race Condition:** Uses MAX() in non-transactional context. Concurrent saves could generate duplicates.

---

## RULE-OI-004: Piece Reference Number Format

**Formula:**
```
item_ref_no = {id_other_item}-{alpha_prefix}{5-digit-seq}
Examples: "1-00001", "1-A00001", "1-B00001"
Alpha prefix increments from '' → 'A' → 'B' ... when seq reaches 99999
```

**Implementation:**
- `generaterefCode($lastTagCode)` — Controller L803–833
- `getlastrefno()` — Model L149–153 (gets last ref_no from details table)
- Sequential increment using preg_match to split alpha and numeric parts

**⚠️ Race Condition:** `getlastrefno()` is called inside a loop without locking — concurrent tagging could generate duplicate `item_ref_no`.

---

## RULE-OI-005: Item Image Upload Rules

**Formula:**
- Max size: 1 MB (1048576 bytes) — validated in JS `validateImage()` at L387
- Allowed types: jpg, png, jpeg, svg
- Storage path: `assets/img/other_inventory/{sku_id}/{timestamp}.jpg`
- All images converted to JPEG via GD `imagejpeg()`

**Implementation:** JS L385–417 (client-side), `upload_img()` Controller L84–115 (server-side), `set_image_other()` Controller L36–54

**Validation:** Both client-side (size + extension check) and server-side (getimagesize)

---

## RULE-OI-006: Purchase Cancel Rule

**Formula:**
- Cancel = set `purchase_bill_status = 2` (1=active, 2=cancelled)
- Cancel reason captured in `cancel_reason` column
- **No stock reversal** — tagged pieces remain in `ret_other_inventory_purchase_items_details`

**Implementation:** `purchase_entry('cancel_purchase_entry')` — Controller L668–691
**⚠️ Risk:** Cancellation does NOT reverse already-tagged pieces or log entries

---

## RULE-OI-007: GST Storage in Purchase

**Formula:**
```
inv_pur_itm_total = amount including GST (gst_amount from JS)
inv_pur_itm_gst   = tax rate % (tax_amount from JS)
gst_amount        = GST value in currency (pur_gst_amount from JS)
```

**Implementation:** `purchase_entry('save')` L582-584 + purchase_items table

**⚠️ Naming confusion:** `inv_pur_itm_total` = total WITH GST; `gst_amount` = the GST component in currency. The field naming is misleading.

---

## RULE-OI-008a: Issue Form Source Tracking

**Formula:**
```
issue_form = 1 → Item was issued during Billing flow (admin_ret_billing, L4506)
issue_form = 2 → Item was issued manually via OI Issue module directly (OI controller, L915)
```

**Implementation:**
- Billing-initiated: `admin_ret_billing.php` L4502–4520 (OI items linked to bills via `est_oth_inv`)
- Manual issue: `admin_ret_other_inventory.php` L915 (`issue_item('save')`)
- Both paths write to `ret_other_invnetory_issue` and update `ret_other_inventory_purchase_items_log` with status=1

**UI:** Issue module only shows items with `issue_form=2` in the issue list. Billing-issued items appear in billing invoices.

**Formula:**
- Each OI item can be linked to multiple schemes with:
  - `item_issue_limit` = max pieces allowed per scheme
  - `from_ins` / `to_ins` = installment range eligibility
- On item update: ALL existing mappings deleted + re-inserted (delete-then-insert pattern)

**Implementation:**
- Save: Controller L165–208 (`other_inventory(save)`)
- Update: Controller L295–340 (`other_inventory(update)`)
- Model: `get_inv_chit_gift()`, `delete_gift_map_data()`, `insertData(..., 'gift_mapping')`

---

## RULE-OI-009: Reorder Level Enforcement

**Formula:**
```
Reorder triggered when: available_stock < min_pcs (per item per branch)
Report shows: current available vs min_pcs and max_pcs
```

**Implementation:** `get_reorder_report()` — Model L604–622
- Stock = SUM(piece) WHERE status=0 AND current_branch={id_branch}
- Compared against `ret_other_inventory_reorder_settings.min_pcs`

**Validation:** Report only — no automatic reorder trigger

---

## RULE-OI-010: Product Mapping Rule

**Formula:**
- One OI item can be mapped to MULTIPLE retail products
- One retail product can be mapped to MULTIPLE OI items (many-to-many via `ret_other_inventory_product_link`)
- `id_product=0` → map to ALL active products
- Duplicate mapping check: `check_other_inv_products_maping()` prevents duplicate entries

**Implementation:** `update_product_mapping()` — Controller L1172–1213

---

## RULE-OI-011: Day Closing Entry Date Rule

**Formula:**
```
IF (day_closing.entry_date == today's date):
    entry_date = NOW() (timestamp)
ELSE:
    entry_date = day_closing.entry_date (date only — no time component)
```

**Implementation:** `purchase_entry('save')` L559–560, `product_details('save')` L734–735, `issue_item('save')` L913–914

**⚠️ Inconsistency:** When day is closed, time is lost (entry_date = date string, not datetime). Could cause ordering issues.
