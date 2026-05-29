# eTail ERP — Complete System Flow Review Document
### For Domain Expert Review
**Date:** 2026-03-27 | **Prepared By:** AI System Brain Analysis

> **What is this document?**
> We studied how the eTail ERP system works by analyzing its entire codebase. Below, we describe every business flow — step by step — as we understood it from the code.
>
> **Why do we need your review?**
> You are the domain expert. You know how these flows **should** work in real business. We need you to tell us:
> - Is our understanding **correct**?
> - Is anything **wrong** or **different** from how it actually works?
> - Is anything **missing** that the system should do but we didn't capture?
>
> **How to review:** For each item, mark in the ✅/❌/❓ column:
> - ✅ **Correct** — Yes, this is how it works
> - ❌ **Wrong** — No, this is not how it works (please explain what's different in the Comments column)
> - ❓ **Missing / Need Clarification** — There are additional steps not listed, or you're not sure
>
> **What we cover:** 47 business flows across 15 categories — Billing, Cancellation, Orders, Schemes, Estimations, Inventory, Branches, Customers, Payments, Repairs, Settings, Reports, and Status Codes.
>
> **⚠️ Issues We Found:** Throughout this document, we highlight issues and gaps we discovered. Please confirm whether these are real problems or expected behavior.

---

# PART A: BILLING FLOWS (6 Flows)

> Billing is the core revenue flow — every sale, return, and exchange goes through the billing system. Below are all 6 types of bills we found.

---

## A1. Sales Bill (Regular Sale)

> A regular sale where the customer buys jewelry and pays for it. This is the most complex flow in the system — a single bill touches 12 different data areas.

### A1-A. Before Creating a Bill

| # | Pre-Condition | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Employee must be selected | The salesperson is recorded on the bill | | |
| 2 | Branch must be selected | The branch determines the applicable metal rate and tax rules | | |
| 3 | Metal rate must be available | Today's gold/silver/platinum rate must have been entered for the selected branch | | |
| 4 | Day closing date determines bill date | The bill date is NOT today's calendar date — it is the "last closed day + 1" from the Day Closing process | | |
| 5 | Duplicate prevention check | A one-time code is generated when the form loads to prevent accidental double-submission | | |

### A1-B. Item Recording (Per Each Jewelry Piece in the Bill)

| # | Detail Captured | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Tag scanned or searched | Each jewelry piece is identified by its tag code (barcode scan or manual entry) | | |
| 2 | Product & design recorded | Product type (Ring, Chain, Necklace, etc.) and design number are recorded | | |
| 3 | Purity & metal type | e.g., 22KT Gold, 18KT Gold, 925 Silver — purity determines the rate per gram | | |
| 4 | Gross weight (with stones) | The total weight of the piece including any stones | | |
| 5 | Less weight (stone weight) | Weight of stones/materials subtracted from gross to get net metal weight | | |
| 6 | Net weight (pure metal) | The actual metal weight used for pricing = Gross − Less | | |
| 7 | Stone details per piece | For each stone: stone name, type, weight, pieces count, rate, and amount are recorded individually | | |
| 8 | Other materials per piece | Any non-stone materials (like enamel work, rhodium plating) are recorded separately | | |
| 9 | Making charges (MC) | Charged either as per gram, percentage of metal value, or flat amount per piece | | |
| 10 | Wastage / Value Addition (VA) | Charged either as percentage of metal weight, flat weight, or flat amount | | |
| 11 | Other charges | Additional charges like hallmark charges, certification charges, etc. | | |
| 12 | Estimation link | If this sale originated from a prior quotation/estimation, that estimation is linked to this item | | |

### A1-C. Pricing Calculation (How the Bill Amount is Computed)

| # | Calculation Step | Formula / Logic | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Metal value | Net Weight × Rate per gram (based on purity and today's rate) | | |
| 2 | + Wastage value | Based on VA type: percentage of weight, flat weight, or flat amount | | |
| 3 | + Making charges | Based on MC type: per gram of net weight, flat amount, or percentage | | |
| 4 | + Stone value | Sum of all stone amounts for the item | | |
| 5 | + Other charges | Sum of all additional charges | | |
| 6 | = Item cost before tax | Total of all the above | | |
| 7 | + CGST + SGST (or IGST) | Tax calculated based on the tax group assigned to the product. If customer is from the same state as the branch → CGST+SGST; if different state → IGST | | |
| 8 | = **Item total** | Final price per item including tax | | |

### A1-D. Payment Recording (How the Customer Pays)

| # | Payment Method | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Cash** | Cash amount is recorded | | |
| 2 | **Card (Credit/Debit)** | Card number (last 4 digits), card type, transaction date, and bank name are recorded | | |
| 3 | **UPI / Net Banking** | Transaction reference number and bank name are recorded | | |
| 4 | **Cheque** | Cheque number, bank name, and date are recorded | | |
| 5 | **Wallet balance** | If the customer has wallet credits from referrals or incentives, those can be used | | |
| 6 | **Advance / Order deposit** | If the customer had a prior advance payment or order deposit, it is adjusted against this bill | | |
| 7 | **Scheme balance** | If the customer has a matured scheme account, that balance can be applied (see D4) | | |
| 8 | **Gift voucher** | If the customer has a valid gift voucher, its value is deducted from the bill | | |
| 9 | **Split payment** | Multiple payment modes can be combined in a single bill (e.g., part cash + part card) | | |

### A1-E. What Happens After the Bill is Saved

| # | Action | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Each tagged item marked as "Sold" | The tag's status changes to "Sold" in the inventory system | | |
| 2 | Tag movement logged | A movement history record is created for each item (for audit trail) | | |
| 3 | Estimation marked as "Billed" | If the sale came from an estimation, that estimation's status is updated | | |
| 4 | Loose (non-tagged) stock reduced | For items without individual tags, the stock quantity is reduced by the sold weight | | |
| 5 | Accounting entries created | Debit and credit journal entries are created for financial reporting | | |
| 6 | Bill number generated | A unique bill number is generated using the configured numbering format | | |
| 7 | Customer PAN/Aadhaar saved | If customer provided identity documents at this point, they are saved / updated | | |
| 8 | Bill printed | The bill/receipt is generated as a PDF in the configured format | | |

### A1-F. Bill Editing (Changing a Saved Bill)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Edit loads existing data | All items, payments, stone details, and advance adjustments are loaded back into the form | | |
| 2 | User modifies items/payments | Staff can add/remove items, change quantities, or change payment modes | | |
| 3 | **All child records are deleted and re-created** | When saved, the system deletes ALL item details, payment records, and stone records — then reinserts them fresh from the form | | |
| 4 | Tag statuses and estimation links refreshed | All inventory statuses and estimation links are recalculated | | |

**⚠️ DANGER:** This "delete everything then re-insert" approach means if the save fails halfway through, all the item and payment details for that bill could be permanently lost. The original creation timestamps on child records are also lost on every edit.

### A1-G. Bill Splitting (One Transaction → Multiple Bills)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer requests split | Customer wants items split across multiple bills (e.g., tax planning, gift receipts) | | |
| 2 | Each split becomes a separate bill | Each split gets its own bill number, its own payment records, and is treated as an independent bill | | |
| 3 | Splits are linked | All split bills share a common reference ID so they can be traced back to the original transaction | | |

### A1-H. E-Invoice / GST Compliance

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | E-invoice generated | For bills above the threshold, an e-invoice is generated by calling the GST Service Provider (GSP) API | | |
| 2 | IRN + QR code received | The system receives an Invoice Reference Number (IRN) and a QR code from the government portal | | |
| 3 | IRN stored on the bill | The IRN and QR code are saved against the bill for compliance | | |

**Questions for You:**
- ❓ Are there any other payment methods used beyond Cash, Card, UPI, Cheque, Wallet, Advance, Scheme, and Gift Voucher?
- ❓ Is bill printing mandatory, or can it be skipped?
- ❓ Is bill editing used frequently in practice? Should it be restricted after a certain time (e.g., can't edit after day closing)?
- ❓ Under what circumstances is bill splitting used — is it common or rare?
- ❓ At what bill value is e-invoicing mandatory?

---

## A2. Sales with Old Metal Exchange

> Customer brings old jewelry, its value is deducted from the bill. Customer pays only the difference.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | All regular sale steps | Everything from A1 above happens first | | |
| 2 | Old metal items recorded | Each piece of old jewelry is recorded — weight, purity, and estimated value | | |
| 3 | Old metal stored in "pocket" | The old metal goes into a collection pocket for later melting and testing | | |
| 4 | Value deducted from bill | Net bill = Sale Amount − Old Metal Value | | |
| 5 | Estimation old metal linked | If the old metal was already noted in a prior estimation, those records are linked | | |

**⚠️ Issue Found:** When this bill is cancelled, the old metal pocket records are **NOT reversed** — the pocket still shows metal from the cancelled bill. This means old metal reports will be incorrect.

**Questions for You:**
- ❓ Is old metal valuation done by the staff at the counter, or does it go through a separate testing process before billing?

---

## A3. Sales Return with Exchange

> Customer returns previously bought item(s) and buys new items in the same transaction. Return value is deducted from the new purchase.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | All regular sale steps | New items billed same as A1 above | | |
| 2 | Return items recorded | Returned items are linked back to the original sale bill | | |
| 3 | Returned items freed | The returned jewelry pieces are marked as "Returned" in inventory (available for resale after processing) | | |
| 4 | Return value deducted | Net bill = New Sale Amount − Return Value | | |
| 5 | Return event logged | A record is created tracking which items were returned and when | | |

**Questions for You:**
- ❓ Is there a time limit for returns (e.g., within 7 days, 30 days, or no limit)?
- ❓ Can a customer return items without buying anything new? (See A6 for standalone returns)

---

## A4. Purchase Bill (Receiving Goods from Supplier)

> When the shop receives jewelry FROM a supplier or karigar (craftsman), a purchase bill is created.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Purchase bill created | Bill created with supplier reference, items received | | |
| 2 | Items recorded | Each item's weight, rate, stone details are recorded | | |
| 3 | Payment recorded | Payment to supplier is recorded (if immediate) | | |
| 4 | Purchase Order linked | If this purchase was against a prior PO, the PO is linked | | |
| 5 | Received items tagged | Jewelry pieces received get their tags updated in inventory | | |
| 6 | Loose stock increased | For items without individual tags, stock weight is added to inventory | | |
| 7 | Accounting entries created | Financial entries recorded (purchase amount, supplier payable) | | |
| 8 | Bill numbered | Purchase bill gets its own number series (separate from sales bills) | | |

**Related Supplier Flows:**

| # | Sub-Flow | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Purchase Return to Supplier | Items returned to supplier, inventory reduced | | |
| 2 | Supplier PO Payment | Payment made against a purchase order | | |
| 3 | Quality Check (QC) | Items go through quality inspection process | | |
| 4 | Hallmarking | Items sent for BIS hallmarking and received back | | |
| 5 | Rate Fixing | Metal rate locked for PO items at an agreed date | | |
| 6 | Lot Generation from PO | Received items grouped into a lot for tagging | | |

**⚠️ Issue Found:** When a purchase bill is cancelled, the system **adds stock back** to inventory — but this is the **wrong direction** for purchases. Cancelling a purchase should **remove** the received stock, not add more.

**Questions for You:**
- ❓ How does the rate fixing process work in practice? Is the rate locked at order time, delivery time, or a custom date?
- ❓ Is QC and Hallmarking done for every purchase, or only for certain items?

---

## A5. Supplier Sales Bill (Selling TO Another Supplier)

> Selling jewelry to another supplier/dealer (B2B sale, not to an end customer).

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | All regular sale steps | Same as A1 but linked to a supplier instead of a customer | | |
| 2 | Supplier referenced | Bill is linked to the supplier record instead of a customer record | | |
| 3 | Items sold to supplier | Jewelry pieces marked as sold in inventory | | |

**Questions for You:**
- ❓ Is supplier sales billing commonly used? How often does this happen in practice?
- ❓ Does the supplier sale affect the regular sales reports, or is it tracked separately?

---

## A6. Sales Return — Standalone (Customer Returns Without Buying)

> Customer returns previously bought items and gets a refund. No new purchase in this transaction.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Return bill created | A return bill is created referencing the original sale bill | | |
| 2 | Returned items recorded | Each returned item is linked to the original bill | | |
| 3 | Returned items freed | Returned jewelry pieces become available in inventory again | | |
| 4 | Return event logged | A record is created for audit and tracking | | |
| 5 | Refund processed | Refund amount is paid back to the customer | | |
| 6 | Loose stock restored | If loose (non-tagged) items are returned, stock quantity is added back | | |
| 7 | Accounting entries created | Refund financial entries recorded | | |
| 8 | Original bill balance recalculated | If the original bill had credit (unpaid) balance, it is recalculated after the return | | |

**Questions for You:**
- ❓ Is a manager's approval required for standalone returns?
- ❓ Can a partial return be done (return some items from a bill, keep others)?

---

# PART B: BILL CANCELLATION (What Happens When a Bill is Cancelled?)

> When a bill needs to be cancelled (due to errors, customer change of mind, etc.), the system needs to undo everything that was done during billing. Below is what we found the system DOES reverse and what it DOES NOT.

---

## B1. Bill Cancellation — Full Process

> When a bill is cancelled, the system attempts to undo everything that was done during billing. Below is the step-by-step cancellation process, what gets properly reversed, and what does NOT get reversed.

### B1-A. Cancellation Process (How It Happens)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Staff initiates cancellation | The cancel action is triggered on a specific bill | | |
| 2 | Bill status → "Cancelled" | The bill's status flag changes to cancelled | | |
| 3 | Payment receipt → cancelled | The payment receipt linked to this bill is also marked as cancelled | | |
| 4 | Cancellation reason recorded | The system logs who cancelled and why (remark) | | |
| 5 | Linked records processed | Each type of linked record is processed for reversal (see below) | | |

### B1-B. What Gets Properly Reversed (✅)

| # | Reversal Action | Applies To | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Estimation unlinked from bill | All bill types | | |
| 2 | Estimation items freed (become available again for re-billing) | All bill types | | |
| 3 | Sold jewelry pieces freed → status becomes "Available" | All bill types | | |
| 4 | Loose stock quantities restored | All bill types | | |
| 5 | Old metal estimation details freed | All bill types | | |
| 6 | Returned items restored to original state | All bill types | | |
| 7 | Gift vouchers ISSUED with the bill → cancelled | All bill types | | |
| 8 | Gift vouchers REDEEMED in the bill → restored to usable | All bill types | | |
| 9 | Scheme/chit balance used → restored to the customer's account | All bill types | | |
| 10 | Green tag wallet incentive → reversed | All bill types | | |
| 11 | Scheme deposit payment → reversed | If scheme advance was used | | |
| 12 | Repair order status → reverted to previous state | Repair Delivery only | | |
| 13 | Order delivery and advance → reverted | Order Delivery only | | |
| 14 | Credit collection status → reopened | Credit Collection only | | |
| 15 | Sales return credit balance → recalculated | Sales Return only | | |
| 16 | Order advance rate type → reverted | Order Advance only | | |
| 17 | Packaging/other inventory items → restored | All bill types | | |

### B1-C. What Does NOT Get Reversed (❌ — Gaps Found)

| # | What Is NOT Reversed | Business Impact | Severity | ✅/❌/❓ | Comments |
|---|---|---|---|---|---|
| 1 | **Card/POS payment records not cancelled** | Card payment details remain in the system after cancel — POS reconciliation reports will show payments that don't have matching bills | HIGH | | |
| 2 | **Accounting entries not reversed** | Cash reports and P&L will show incorrect numbers because the cancelled bill's debit/credit entries are still counted | HIGH | | |
| 3 | **Section-level stock not updated** | Stock reports for specific counters/sections may show wrong quantities even though the overall branch count is correct | MEDIUM | | |
| 4 | **Advance balance not reversed for some bill types** | Customer's advance balance may be incorrect after cancellation — they could lose or gain money | HIGH | | |
| 5 | **Old metal pocket records not reversed** | Old metal reports continue to count metal from cancelled bills — overreporting old metal holdings | MEDIUM | | |
| 6 | **Purchase cancellation adds stock instead of removing it** | This is a CRITICAL directional error — cancelling a purchase should REMOVE the received stock, but the system ADDS more stock instead | CRITICAL | | |

**Questions for You:**
- ❓ How often are bills cancelled in practice? Is it a daily occurrence or rare?
- ❓ Is there a manager approval required before cancelling a bill?
- ❓ Should cancelled bills still appear in reports (for audit trail), or should they be completely excluded?
- ❓ For accounting entry reversal — should a reverse journal entry be created, or should the original entry be deleted?

---

## B2. Cancellation Coverage by Bill Type

> This shows how thoroughly each bill type's cancellation works. A higher percentage means more things are properly reversed.

| # | Bill Type | How Complete Is Cancellation? | Known Gap | ✅/❌/❓ | Comments |
|---|---|---|---|---|---|
| 1 | Regular Sales | ~70% | Accounting entries not reversed, POS records not cleared | | |
| 2 | Sales + Old Metal | ~65% | Old metal pocket not reversed | | |
| 3 | Sales Return Exchange | ~70% | Returned item status reversal not fully verified | | |
| 4 | Purchase | ~70% | ⚠️ Stock direction is WRONG (adds instead of removes) | | |
| 5 | Order Advance | ~75% | Rate type revert exists and works | | |
| 6 | Sales Return Standalone | ~75% | May incorrectly add stock twice on cancel | | |
| 7 | Credit Collection | ~75% | Credit status set to "unpaid" instead of recalculating actual balance | | |
| 8 | Order Delivery | ~80% | Most complete cancellation | | |
| 9 | Scheme Pre-Close | ~70% | Whether the account properly reopens is not verified | | |
| 10 | Repair Delivery | ~75% | Repair order status is properly reverted | | |
| 11 | Supplier Sales | ~70% | Accounting entries not reversed | | |

**Questions for You:**
- ❓ Is there any bill type that should NEVER be allowed to be cancelled (e.g., purchase bills, or bills older than X days)?
- ❓ For Credit Collection cancellation — should the system recalculate the exact remaining balance, or is resetting to "unpaid" acceptable?

---

# PART C: CUSTOMER ORDER FLOWS (4 Flows)

> When a customer orders a custom jewelry piece, the lifecycle goes: Order → Advance Payment → Manufacturing → Delivery. Below are all the steps.

---

## C1. Customer Order Lifecycle (Order → Advance → Manufacturing → Delivery)

> A custom jewelry order goes through a multi-step lifecycle: the customer describes what they want, pays an advance, the karigar (craftsman) manufactures it, and the customer picks it up and pays the balance.

### C1-A. Creating a New Order

| # | Detail Captured | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer selected | Order is linked to a registered customer | | |
| 2 | Financial year recorded | The order is tagged to a financial year for reporting | | |
| 3 | Order number generated | A unique order number is auto-generated | | |
| 4 | For each item in the order: | | | |
| 4a | — Product, design, and purity | What type of jewelry, which design, and what gold purity | | |
| 4b | — Weight and rate | Estimated weight and the agreed metal rate | | |
| 4c | — Stone requirements | Specific stones requested (type, quality, count, weight) — stored per item | | |
| 4d | — Reference images uploaded | Customer can provide images/drawings of what they want — uploaded and stored per item | | |
| 4e | — Webcam photos captured | Staff can capture photos of reference pieces using webcam | | |
| 4f | — Other charges | Any custom charges (engraving, special finish, etc.) | | |
| 5 | **Stock item reserved** | If the customer selected an existing piece from stock (instead of custom), that piece is **reserved** — its tag status changes to "Reserved" so no one else can sell it | | |
| 6 | Tag status logged | A movement record is created for the reservation | | |

### C1-B. Shopping Cart (Alternative Order Path)

> There is a shopping cart feature where items can be added to a cart first, then converted to an order.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Item added to cart | Staff adds items from stock/catalog to a cart (status = "In Cart") | | |
| 2 | Cart reviewed | All cart items are reviewed with filters (product, design, weight, karigar) | | |
| 3 | "Place Order" clicked | Cart items are migrated to a formal customer order (cart status → "Placed") | | |
| 4 | Vendor email sent | An automated email with a secure link is sent to the vendor/karigar to acknowledge the order | | |

### C1-C. Vendor / Karigar Acknowledgement

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Vendor receives email | Email contains a tokenized link (one-time secure link) | | |
| 2 | Vendor clicks link | Opens a public page (no login required) showing the order items | | |
| 3 | Vendor accepts order | The order item status is updated to "Acknowledged" | | |

### C1-D. Order Editing

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Order loaded into form | All items, stones, images are loaded back | | |
| 2 | Items can be added or modified | Staff can change weights, rates, or add/remove items | | |
| 3 | **Stones are deleted and re-inserted** | On update, all stone records for each item are deleted first, then re-inserted. If the update fails midway, stone details could be lost | | |
| 4 | **Images are deleted and re-inserted** | Same pattern — old images are removed and new ones added | | |

**⚠️ Issue Found:** The delete-and-re-insert pattern for stones and images during order edit means if the save fails partway, the data is lost.

### C1-E. Order Cancellation

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | OTP verification (3-step process) | Staff must: (a) Confirm intent, (b) Send OTP to authorized mobile, (c) Enter OTP to proceed | | |
| 2 | Order marked as "Cancelled" | The order status changes to cancelled | | |
| 3 | All order items cancelled | Each item's status changes to cancelled | | |
| 4 | Reserved items freed | Any reserved tags become "Available" again | | |
| 5 | Advance refund created | A refund receipt is created to return the advance to the customer | | |

### C1-F. Order Deletion (Permanent Removal)

| # | What Gets Removed | Cleaned Up? | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Order record | ✅ Yes — removed | | |
| 2 | Order item details | ✅ Yes — removed | | |
| 3 | Advance payment record | ✅ Yes — removed | | |
| 4 | Order images (photos/drawings) | ❌ NO — image files remain on the server forever | | |
| 5 | Stone details | ❌ NO — stone records remain without a parent order (orphans) | | |
| 6 | Other charges | ❌ NO — additional charge records remain (orphans) | | |
| 7 | Reserved items | ❌ NO — reserved tags still point to the deleted order (stuck as "Reserved" forever) | | |

**Questions for You:**
- ❓ Is order deletion used in production, or only cancellation?
- ❓ Should there be a time limit for order cancellation (e.g., cannot cancel after manufacturing has started)?
- ❓ Is partial delivery supported (deliver some items from an order, keep others pending)?
- ❓ Is the shopping cart feature actively used? Do all clients use it?
- ❓ How does the karigar/vendor email acknowledgement workflow actually work in practice — do vendors respond timely?

---

## C2. Order Advance Payment

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Advance bill created | A bill is generated specifically for the advance amount, linked to the customer order | | |
| 2 | Payment recorded | Customer pays by Cash, Card, or Cheque — same payment modes as regular billing (A1-D) | | |
| 3 | Accounting entries created | Financial entries for the advance (advance receivable) | | |
| 4 | **Rate type recorded** | Whether the advance locks the metal rate at today's price or at delivery-date price | | |

**Questions for You:**
- ❓ Does the advance lock the gold rate at the time of advance, or is the rate applied at the time of delivery?

---

## C3. Order Delivery

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Order items verified | Staff verifies that the manufactured items match the customer's specifications | | |
| 2 | Delivery bill created | A bill is created for: Total amount − Advance already paid = Balance due | | |
| 3 | Advance deducted | The advance paid earlier is automatically subtracted from the bill | | |
| 4 | Items marked as "Sold" | Jewelry pieces are marked as sold in inventory | | |
| 5 | Order items marked as "Delivered" | Each item's status is updated from "Completed" to "Delivered" | | |
| 6 | Accounting entries created | Financial entries for delivery with advance reversal (debit customer, credit advance account) | | |

**Questions for You:**
- ❓ What happens if the manufactured item's weight differs from the estimated weight at order time? Does the price change?
- ❓ Can a customer reject the manufactured item and request rework?

---

## C4. Advance Booking

> A simpler version of customer orders — just a booking with advance, with NO manufacturing workflow.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Booking record created | Customer details, what item they want, expected date, and advance amount | | |
| 2 | Advance payment recorded | Payment collected (Cash, Card, or Cheque) | | |
| 3 | Item reserved | If a specific jewelry piece is identified, it is reserved for the customer | | |
| 4 | Customer notified | SMS/notification sent confirming the booking | | |

**Questions for You:**
- ❓ What is the difference between a "Customer Order" (C1) and an "Advance Booking" (C4) in practice? When would staff use one vs the other?
- ❓ Does the advance booking have an expiry date?

---

# PART D: SCHEME / SAVINGS PLAN FLOWS (5 Flows)

> Schemes are monthly savings plans where customers pay installments over a period (typically 11 or 12 months) and get a bonus/benefit at maturity. This is one of the most important modules in the system.

---

## D2. Scheme Account Open / Close

> After a scheme is created (see D1), customers can "open an account" under that scheme to start saving. This section covers the full lifecycle: opening → saving → closing.

### D2-A. Opening a New Account

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer selected | Account is linked to a registered customer | | |
| 2 | Scheme selected | The specific scheme plan is chosen (determines installment amount, tenure, benefits) | | |
| 3 | Agent/referral code verified | If a sales agent or referral brought this customer, their code is verified for correctness | | |
| 4 | Branch determined | The account's branch is set based on the staff's login branch or customer's registered branch | | |
| 5 | Maturity date calculated | Based on the scheme's maturity type: either a fixed number of days or a fixed number of months from today | | |
| 6 | KYC documents uploaded | PAN card, Aadhaar card images are uploaded | | |
| 7 | **Account record created** | A new scheme account record is created with 35+ fields | | |
| 8 | Client ID generated | A unique client ID is generated in the format: Code/Prefix/AccountNumber | | |
| 9 | Opening gifts issued | If the scheme includes a free gift at joining (e.g., a lucky draw coupon, a gift item), the gift is issued from the store's inventory | | |
| 10 | Prize gifts issued | If there are additional prize gifts, they are also issued | | |

### D2-B. First Payment & Account Number

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Free (first) payment recorded** | If the scheme settings say "first installment is free" (automatically marked as paid on joining), a payment record is created automatically | | |
| 2 | Receipt number generated | A receipt number is generated for the first payment | | |
| 3 | Due date calculated | The next payment due date is calculated based on the payment frequency | | |
| 4 | Paid installment count updated | The total number of paid installments for this account is updated | | |
| 5 | **Scheme account number generated** | The formal scheme account number is generated ONLY after the first successful payment — if the very first payment fails, the account won't have a valid number | | |

### D2-C. Notifications on Account Opening

| # | Channel | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | SMS | Welcome / joining SMS sent to customer | | |
| 2 | Email | Welcome email sent (if email is available) | | |
| 3 | WhatsApp | Welcome WhatsApp message sent (if WhatsApp integration is enabled) | | |

### D2-D. Voucher Issued at Joining

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | If scheme has voucher | A gift voucher or card is issued to the customer | | |
| 2 | Voucher image uploaded | The voucher's image or details are stored | | |
| 3 | Referral wallet credit | If the customer was referred by someone, a wallet credit (incentive) is added to the referrer's wallet | | |

### D2-E. Normal Closing (After All Installments Are Paid)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | OTP verification | Customer must verify via OTP before closing | | |
| 2 | Closing form loaded | The system shows: total paid amount, benefit amount, deductions, and final closing balance | | |
| 3 | **Benefit calculated** | The bonus amount depends on: | | |
| 3a | — Benefit chart lookup | Based on the number of months paid, the applicable benefit percentage or amount is looked up from the scheme's benefit chart | | |
| 3b | — DigiGold calculation | If it's a DigiGold scheme: benefit = accumulated weight × current rate | | |
| 3c | — Maturity-days formula | If using maturity days: a different formula based on days elapsed | | |
| 4 | **Deductions applied** | Any applicable deductions: | | |
| 4a | — Employee referral deduction | If the employee who referred this account has left, the referral benefit may be deducted | | |
| 4b | — Customer intro deduction | If someone who introduced this customer's account is no longer active, their referral credit may be reversed | | |
| 4c | — Agent referral deduction | Similar deduction for inactive agents | | |
| 4d | — Gift value deduction | If the customer received a gift at joining but closes before a minimum tenure, the gift value may be deducted | | |
| 5 | **One-time premium discount** | A special discount may apply if the customer joined under a promotional offer | | |
| 6 | **GA bonus calculation** | General Advance bonus calculation (if applicable) | | |
| 7 | **MCVA purchase discount** | If a Making Charge / VA purchase discount is configured, it is calculated | | |
| 8 | **Final closing balance** | Total Paid + Benefits − Deductions = Closing Balance | | |
| 9 | Account marked as closed | Active = No, Closed = Yes | | |
| 10 | Webcam photo captured | A photo of the customer collecting the closure amount is captured | | |
| 11 | SMS/Email/WhatsApp sent | Closure confirmation notification sent | | |

### D2-F. Employee Incentive on Closing

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | If employee closing incentive is enabled | The employee who manages this account gets a wallet credit as an incentive | | |
| 2 | Credit amount calculated | Based on weight or number of installments | | |
| 3 | Wallet account auto-created | If the employee doesn't have a wallet account, one is automatically created | | |

### D2-G. Revert (Undo a Closed Account)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Revert requested | Admin decides to undo the closure (e.g., error in closing, wrong benefit amount) | | |
| 2 | DigiGold check | If the customer has created a DigiGold account linked to this, the revert is BLOCKED | | |
| 3 | Account reopened | Active = Yes, Closed = No — account goes back to active status | | |
| 4 | Incentive reversed | If an employee closing incentive was credited, a debit (reversal) entry is added to their wallet | | |
| 5 | SMS/Email sent | Revert notification sent to the customer | | |

### D2-H. Passbook Printing

| # | Format | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Front page | Customer name, scheme name, account number, joining details | | |
| 2 | Back page (open account) | Payment history with installment amounts and dates | | |
| 3 | Close page | Closing details with benefit amount and final balance | | |
| 4 | Specific payment | Individual payment receipt can be printed | | |
| 5 | Bond receipt | For metal-weight-based accounts: bond with purity, rate, and weight details | | |

**Account Status Combinations:**

| # | Status | What It Means | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Active + Not Closed | Account is open and running — customer is paying installments | | |
| 2 | Inactive + Not Closed | Account is suspended or blocked — payments are not being accepted | | |
| 3 | Active + Closed | Account has matured and been closed normally with full benefit | | |
| 4 | Inactive + Closed | Account was pre-closed before maturity (early closure) | | |

**⚠️ Issue Found:** During account opening, the OTP is sent to the customer's mobile **and also returned in the system response** — this is a security concern because the OTP value is visible in the system's data.

**Questions for You:**
- ❓ Can a customer have multiple active accounts under the same scheme?
- ❓ What is the typical passbook format — is it a physical booklet or a printed receipt?
- ❓ When an account is "suspended" (Inactive + Not Closed), how is it reactivated?
- ❓ How often is the "Revert Closure" feature used? Should it require manager approval?
- ❓ Is the DigiGold scheme actively used by clients? How does it differ from regular schemes in practice?

---

## D3. Monthly Installment Payment (Scheme Collection)

> Each month, the customer pays an installment towards their scheme account. This is the most frequent transaction in the scheme module and one of the most complex operations in the system.

### D3-A. Collecting a Payment

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer + scheme account selected | Staff picks the customer and their active scheme account | | |
| 2 | OTP generated and sent | An OTP is sent to the customer's mobile for verification — the customer must provide this OTP before payment is finalized | | |
| 3 | **Payment mode determined** | The system supports: Cash, Credit Card, Debit Card, Cheque, Net Banking, Gift Voucher, Advance Adjustment, Referral Wallet, or MULTI (combination of modes) | | |
| 4 | **Multi-installment support** | If the customer wants to pay multiple installments at once, the amount is divided equally across the installments | | |
| 5 | **GST calculated** | Two modes: (a) Exclusive: GST added on top of payment, (b) Inclusive: GST extracted from the payment amount | | |
| 6 | **Metal weight calculated** | Based on scheme type: | | |
| 6a | — Fixed weight scheme | Weight = Payment Amount ÷ Today's metal rate | | |
| 6b | — Flexible weight scheme | Weight depends on the flexible scheme sub-type settings | | |
| 6c | — Standard scheme | Weight is provided directly (not calculated) | | |
| 7 | **Branch determined** | Branch assignment follows these rules: | | |
| 7a | — Branch-wise customer | Payment goes to customer's registered branch | | |
| 7b | — Branch-wise login | Payment goes to the employee's login branch | | |
| 7c | — Pay from other branch | If enabled, employee can collect for a different branch | | |
| 8 | **Receipt number generated** | If auto-receipt is configured, a unique receipt number is generated | | |
| 9 | **DigiGold benefits** | If this is a DigiGold scheme, special benefit calculations apply per payment | | |
| 10 | Payment record created | The payment is saved with 30+ fields including amount, date, mode, branch, employee, metal weight, GST, receipt number | | |

### D3-B. After Payment is Saved

| # | Action | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Account number generated (if first payment)** | If this is the very first payment for this account, the formal scheme account number is generated now — not at account opening time | | |
| 2 | Payment mode details saved | For each mode used (cash, card, cheque, etc.), detailed records are saved (card last 4 digits, bank name, cheque number, etc.) | | |
| 3 | Wallet transaction created | If wallet credits were used, a wallet debit transaction is recorded | | |
| 4 | Referral incentive credited | If a referral bonus is due at this installment number, the referrer's wallet is credited | | |
| 5 | Agent/employee incentive | If incentive rules apply, the collecting agent/employee gets a wallet credit | | |
| 6 | Paid installment count updated | The scheme account's total paid installment count is incremented | | |
| 7 | SMS/Email confirmation | Payment confirmation is sent via SMS and email to the customer | | |
| 8 | External system sync | If ERP integration is active, the payment is synced to the external system | | |

### D3-C. Payment Editing

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Payment loaded | Staff loads an existing payment for editing | | |
| 2 | Changes made | Amount, date, mode, remark can be modified | | |
| 3 | **Old payment mode records soft-deleted** | Existing payment mode details are marked as inactive (not physically deleted) | | |
| 4 | **New payment mode records inserted** | Fresh mode detail records are created | | |
| 5 | Benefits recalculated | Any benefits linked to this payment are recalculated | | |

### D3-D. Payment Deletion

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Payment is permanently deleted** | The payment record is removed from the database entirely — NOT a soft delete | | |
| 2 | **No child cleanup** | Payment mode details, status logs, and advance utilization records become orphaned (no parent) | | |
| 3 | **Installment count NOT corrected** | The account's "total paid installments" count becomes stale — it still counts the deleted payment | | |

**⚠️ CRITICAL Issue:** Payment deletion is a permanent hard-delete with NO audit trail and NO cleanup of related records. Also, it uses a GET request which means it could be accidentally triggered by a link click.

### D3-E. General Advance Payment

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | GA payment created | A "General Advance" payment is recorded separately from regular installments | | |
| 2 | Stored in separate tables | GA payments go into a completely different set of records than regular payments | | |
| 3 | Used during closing | The GA balance is factored into the final closing calculation | | |

### D3-F. Post-Dated Cheque (PDC) Flow

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | PDC recorded | A future-dated cheque is recorded with its maturity date | | |
| 2 | On maturity date | Admin reviews the PDC and marks it as "Success" | | |
| 3 | Converted to payment | If successful, the PDC is converted to a regular payment — receipt number generated, account number assigned if needed | | |
| 4 | If cheque bounces | Admin marks PDC as failed — status is updated, notification sent | | |

### D3-G. Payment Revert (Undo Approval)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Payment reverted | Admin can revert a previously approved payment back to "Awaiting" status | | |
| 2 | Installment count decremented | The paid installment count is reduced by 1 | | |
| 3 | **Receipt number NOT reversed** | The receipt number remains — potentially causing numbering gaps | | |
| 4 | **Wallet not reversed** | If wallet credits were used, they are NOT returned | | |
| 5 | **Referral benefits NOT reversed** | If referral incentives were credited, they remain | | |

**What happens when a payment is CANCELLED:**

| # | Step | Reversed? | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Payment status → "Cancelled" | ✅ Yes | | |
| 2 | Audit log created (who cancelled + reason) | ✅ Yes | | |
| 3 | External system notified | ✅ Yes | | |
| 4 | Wallet balance restored | ❌ NO — wallet deduction is not reversed | | |
| 5 | Installment count corrected for online payments | ❌ NO — count is not decremented for gateway-initiated cancellations | | |

**Questions for You:**
- ❓ Can a customer pay multiple installments at once (e.g., pay 3 months in advance)?
- ❓ What happens if a payment bounces (e.g., cheque bounce or gateway failure)?
- ❓ Is there a late payment fee or penalty for overdue installments?
- ❓ How often is the "Payment Delete" feature used? Should it be disabled in production?
- ❓ Is General Advance actively used? How does it differ from paying regular installments early?

---

## D4. Using Scheme Balance to Pay for a Purchase (Chit Utilization)

> When a scheme account matures (or even before), the customer can use their accumulated scheme balance to pay for a jewelry purchase instead of receiving cash.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Scheme balance shown at billing | When creating a bill, the customer's scheme balance is displayed | | |
| 2 | Utilization amount entered | Staff enters how much of the scheme balance to use against this bill | | |
| 3 | Adjustment record created | A record linking the scheme account to this bill is created | | |
| 4 | Scheme payment created | A payment entry is created showing the scheme balance used | | |
| 5 | Scheme balance reduced | The utilized amount is deducted from the customer's scheme account | | |
| 6 | Accounting entries created | Financial entries for the scheme utilization | | |

**Questions for You:**
- ❓ Can a customer use a partially matured scheme balance, or must the scheme be fully matured first?
- ❓ Can the scheme balance be used across multiple bills, or must it be used in a single bill?

---

## D5. Scheme Pre-Close (Early Closure Before Maturity)

> If a customer wants to close their scheme account before completing all installments.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Pre-close bill created | A settlement bill is created with the final amount | | |
| 2 | Penalty or reduced benefit calculated | Based on the pre-close penalty chart (if applicable) or the benefit chart, the settlement amount is calculated — customer may get less benefit or face a deduction | | |
| 3 | Payment processed | Settlement amount is paid to the customer (cash or card) | | |
| 4 | Account marked as closed | The account is closed with a "pre-closed" status | | |
| 5 | Accounting entries created | Financial entries for the pre-closure | | |

**⚠️ Issue Found:** If a pre-close bill is cancelled, it's unclear whether the account properly reopens. This needs verification.

**Questions for You:**
- ❓ Is there a minimum number of installments before pre-close is allowed?
- ❓ Does the customer receive cash, or is the amount only usable for a purchase (scheme utilization)?

---

# PART E: ESTIMATION / QUOTATION FLOWS (4 Flows)

> An estimation (quotation) is created when a customer is browsing or enquiring about products. It captures what the customer is interested in and can later be converted into a bill.

---

## E1. Estimation → Bill Conversion

> An estimation (quotation) is a detailed price quote given to a customer who is browsing. It captures every item the customer is interested in, calculates a total, and can later be converted into a bill.

### E1-A. Creating an Estimation (Item Types)

| # | Item Type | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Tagged items** | Jewelry pieces that have an individual tag (barcode) — scanned or searched by tag code | | |
| 2 | **Catalog items** | Items from the product catalog without a specific tag — staff enters product, purity, weight manually | | |
| 3 | **Custom items** | Completely custom-specified items not in either tag or catalog | | |
| 4 | **Old metal exchange** | Customer brings old jewelry to exchange — its weight, purity, and value are recorded per piece | | |
| 5 | **Stones in old metal** | Stones in the old jewelry are separately recorded (deducted from old metal weight) | | |

### E1-B. Per-Item Details Captured

| # | Detail | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Product, design, purity | What the item is and its metal composition | | |
| 2 | Gross weight, less weight, net weight | Same as billing (see A1-B) | | |
| 3 | Metal rate | Today's rate for the item's metal and purity | | |
| 4 | Making charges (MC) | Per gram, flat, or percentage — same 3 modes as billing | | |
| 5 | Wastage / Value Addition (VA) | Percentage of weight, flat weight, or flat amount | | |
| 6 | Stone details per item | Each stone: name, type, weight, pieces, rate, amount | | |
| 7 | Other materials per item | Non-stone materials attached to the item | | |
| 8 | Other charges per item | Additional charges (hallmark, certification, etc.) | | |
| 9 | **VA Slab discount** | A volume-based discount that applies different wastage rates based on metal type and weight range | | |
| 10 | **Stone discount (bulk)** | A percentage discount that can be applied across ALL stones in all items | | |

### E1-C. Pricing Calculation (Same as Billing, Plus Extras)

| # | Component | Formula | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Metal value | Net Weight × Rate | | |
| 2 | + Wastage | Based on VA type | | |
| 3 | + Making charges | Based on MC type | | |
| 4 | + Stone total | Sum of all stones | | |
| 5 | + Other charges | Sum of additional charges | | |
| 6 | − Old metal value | Weight × rate of customer's old jewelry deducted | | |
| 7 | − Chit/scheme balance | If customer has a matured scheme account, that amount is deducted | | |
| 8 | − Advance balance | If customer has prior advance or order deposits, those are deducted | | |
| 9 | − Sales return credit | If customer has credits from prior returns, those are deducted | | |
| 10 | + Tax (CGST+SGST or IGST) | Tax on the net amount based on customer's state vs. branch state | | |
| 11 | = **Grand total** | Final amount the customer would need to pay | | |

### E1-D. Special Features in Estimation

| # | Feature | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Tag merge** | Multiple small tags can be "merged" into one line item for estimation purposes | | |
| 2 | **Credit collection lookup** | For customers with unpaid credit bills, the system shows all pending credit amounts side by side during estimation | | |
| 3 | **Day closing gate** | The system checks if the branch's day has been closed — if closed, new estimations for that date should not be allowed | | |
| 4 | **Sales return in estimation** | Customers can include items they want to return in the same estimation (feature flag controlled) | | |
| 5 | **Image capture** | Webcam photos of items or customers can be captured and stored with the estimation | | |

### E1-E. Estimation Editing

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Existing data loaded | 7 different data queries are run to load all estimation data: header, tag items, non-tag items, home bill items, old metal, chit details, and other details | | |
| 2 | User makes changes | Staff modifies items, prices, quantities | | |
| 3 | **All child records deleted and re-created** | On save, ALL item records, stone records, material records, charge records, old metal records, chit records, and gift voucher records are deleted first, then re-inserted fresh | | |
| 4 | Header updated | The estimation header (customer, date, totals) is updated in place | | |

**⚠️ DANGER:** The "delete-then-re-insert" pattern puts ALL estimation data at risk if the save fails midway. Item IDs change on every edit, which could break any external references.

### E1-F. Print Recalculation Risk

| # | What Happens | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | **The print template RECALCULATES values** | The PDF print template does NOT just display saved values — it recalculates VA, MC, stone totals, chit benefits, and tax from scratch | | |
| 2 | **Risk: Printed total ≠ Screen total** | If any calculation logic was changed in the print template but not on the screen (or vice versa), the printed amount will differ from the screen amount | | |
| 3 | **Tax split recalculated** | CGST/SGST vs. IGST is re-determined at print time based on customer's state | | |

### E1-G. Converting Estimation to Bill

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Duplicate check | Before converting, the system checks if this estimation was already billed | | |
| 2 | Items transferred | All estimation items are copied to the bill form | | |
| 3 | Estimation status updated | The estimation is marked as "Billed" | | |
| 4 | Standard billing steps | All billing steps from A1 apply from this point | | |

**Questions for You:**
- ❓ How long does an estimation remain valid? Does it expire after a certain period?
- ❓ Can an estimation be converted to a bill by a different employee than the one who created it?
- ❓ Is the VA Slab discount commonly used? How are the slab ranges defined?
- ❓ Should the print template use saved values instead of recalculating — to prevent discrepancies?

---

## E2. Estimation Discount / Manager Approval (EDA)

> If an employee offers a discount that exceeds their allowed limit, the system triggers a manager approval workflow.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Employee enters discount | Discount amount or percentage is set on the estimation | | |
| 2 | Limit checked | System compares the discount against the employee's allowed discount limit | | |
| 3 | If over limit → approval required | The estimation is flagged for manager/admin approval | | |
| 4 | Manager approves or rejects | Manager reviews and decides via the approval screen | | |
| 5 | Employee notified | The employee is notified of the decision | | |
| 6 | On billing: discount applied | If approved, the discount is carried over to the bill | | |
| 7 | Denied estimation | An estimation denied by the manager should NOT be convertible to a bill | | |

**⚠️ Issue Found:** The discount limit check happens only on the screen (client-side) — a technically savvy person could bypass this check and apply any discount amount.

**Questions for You:**
- ❓ What is the typical discount limit for employees? Is it a percentage or a fixed amount?
- ❓ Can a manager approve a discount retroactively (after the bill is already created)?

---

## E3. Gift Voucher in Estimation

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Voucher applied | Staff enters the gift voucher number at estimation, its value is deducted | | |
| 2 | Voucher validated | System checks if the voucher is valid, not expired, and not already used | | |
| 3 | On billing: voucher redeemed | When the estimation is converted to a bill, the voucher is marked as used | | |
| 4 | On bill cancel: voucher restored | If the bill is cancelled, the voucher becomes usable again | | |

---

## E4. Scheme Balance Adjustment in Estimation

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer's scheme balance shown | The customer's accumulated scheme savings balance is displayed | | |
| 2 | Adjustment amount entered | Staff enters how much of the scheme balance to apply | | |
| 3 | Utilization record saved | A record is saved linking the scheme account to this estimation | | |
| 4 | On billing: adjustment transferred | When the estimation becomes a bill, the scheme adjustment is applied to the bill | | |

---

# PART F: STOCK / INVENTORY FLOWS (5 Flows)

> Every jewelry piece is tracked individually using a "Tag" (a barcode label on the item). Below are all the inventory management flows.

---

## F1. Tagging (Creating Tags for Jewelry Pieces)

> Each jewelry piece gets a unique tag with a barcode. This tag is how the system tracks the item through its entire lifecycle — from creation to sale. Tagging is one of the most feature-rich modules with 88 internal endpoints and 300+ JS functions.

### F1-A. Creating a New Tag

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Lot selected | The tag is created from a lot (batch) of raw material received from a supplier | | |
| 2 | Product & design selected | The product type (Ring, Necklace, etc.) and design number are assigned | | |
| 3 | Metal purity set | The purity grade (22KT, 18KT, 916, etc.) is selected | | |
| 4 | Weights recorded | Gross weight, less weight (stones), and net weight are entered — about 50 fields total per tag | | |
| 5 | Making charges & wastage | MC and VA values are set based on product/design settings | | |
| 6 | **Section/counter assigned** | The tag is placed in a specific display section or counter in the store | | |
| 7 | Stone details added | For each stone in the item: stone type, weight, pieces, rate, amount — stored as batch records | | |
| 8 | Material details added | For each non-stone material: material type, weight, rate, amount | | |
| 9 | Images captured | Product images via camera or file upload — stored as files with database references | | |
| 10 | QR code generated | A unique QR code is generated and saved as an image file — this is what gets printed on the physical tag | | |
| 11 | Tag code generated | A unique tag code is assigned and the tag record is updated | | |
| 12 | **Lot balance decremented** | The raw material lot's balance is reduced by the weight used for this tag | | |
| 13 | **If lot fully tagged** | When the entire lot weight has been converted to tags, the lot is automatically marked as "completed" | | |
| 14 | Section movement logged | A log entry is created recording the tag's initial placement | | |

### F1-B. Tag Editing (Update Existing Tag)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Existing tag loaded | All details (weights, stones, materials, images) are loaded | | |
| 2 | **Old lot balance restored** | Before applying the new values, the original lot's balance is restored (adding back the old weight) | | |
| 3 | **Old stone balance restored** | Similarly, old stone weights are restored to the lot | | |
| 4 | Tag record updated | The 50+ fields are updated in place | | |
| 5 | **Stones deleted and re-inserted** | Old stone records are removed, new ones inserted | | |
| 6 | **Materials deleted and re-inserted** | Same pattern for materials | | |
| 7 | Images updated | New images added, old ones replaced if needed | | |
| 8 | **New lot balance decremented** | The new lot's balance is reduced by the new weight | | |
| 9 | Branch transfer handled | If the branch was changed, a branch transfer record is created | | |

### F1-C. Tag Deletion (OTP Protected)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Staff clicks delete | System initiates OTP verification | | |
| 2 | OTP sent to authorized mobile | A 6-digit OTP is sent to the branch manager's mobile number | | |
| 3 | OTP stored in session | The OTP is temporarily stored for verification | | |
| 4 | Staff enters OTP | The entered OTP is compared with the session OTP | | |
| 5 | If verified → tag marked as "Deleted" | The tag status changes to "Deleted" (soft delete — not physically removed) | | |
| 6 | **Lot balance restored** | The weight that was consumed when creating this tag is added back to the lot | | |
| 7 | **Loose stock restored** | If this was a non-tag stock item, the loose stock quantity is restored | | |
| 8 | Admin approval alternative | For some branches, admin credentials can be used instead of OTP | | |

### F1-D. Re-Tagging (Old Tag → New Tag)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Old tags selected | Staff selects one or more tags to be retagged (e.g., due to design change, damage, etc.) | | |
| 2 | Old tags deactivated | Each old tag's status changes to "Other Issue" | | |
| 3 | Link record created | A record linking old tags to the retag process is created | | |
| 4 | Old stones/materials copied | Stone and material details from old tags are preserved in the retag record | | |
| 5 | **New lot generated from old tags** | A new raw material lot is automatically created from the weight of the old tags | | |
| 6 | New tags can be created from this lot | Staff can now create fresh tags from the newly generated lot | | |

### F1-E. Order Linking (Connect Tag to Customer Order)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Tag searched | Staff finds the tag to be linked | | |
| 2 | Order searched | Staff finds the customer order to link to | | |
| 3 | Link created | The tag is associated with the specific order item | | |
| 4 | Order item marked as "Tag Linked" | The order item's status updates to show a tag has been assigned | | |
| 5 | **Unlinking requires OTP** | To remove a link, staff must verify via OTP — this prevents accidental unlinking | | |

### F1-F. Collection Mapping (Grouping Tags for Display)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Collection selected | A named collection or category is selected (e.g., "Wedding Ring Collection") | | |
| 2 | Tags assigned | Tags are grouped under this collection | | |
| 3 | Reference number generated | A unique reference for this collection mapping | | |
| 4 | **Edit uses delete-then-insert** | When editing a collection mapping, all item links are deleted and re-created | | |

### F1-G. Purchase Cost Calculation

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | PO details fetched | If this tag came from a purchase order, the PO details are loaded | | |
| 2 | Cost calculated | Metal value + Making charges + Stone value + Wastage = Total purchase cost | | |
| 3 | Tag updated | The calculated purchase cost is saved on the tag | | |

**Tag Lifecycle — All Possible States:**

| # | Status | What It Means | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Available** | In stock, ready for sale | | |
| 2 | **Sold** | Purchased by a customer | | |
| 3 | **In Transit** | Being transferred to another branch | | |
| 4 | **Purchased** | Received from a supplier (just arrived) | | |
| 5 | **Reserved** | Set aside for a customer order | | |
| 6 | **Returned** | Returned by a customer | | |
| 7 | **Cancelled** | The sale was cancelled, item is back | | |
| 8 | **Approval Stock** | Sent to customer for approval/viewing | | |
| 9 | **Under Repair** | Given for repair work | | |
| 10 | **Metal Issue** | Sent for melting or reprocessing | | |

**Questions for You:**
- ❓ Are all 10 tag statuses actively used, or are some unused?
- ❓ Is "Approval Stock" used for home trials / customer approvals?
- ❓ What happens to a "Returned" tag — does it go back to "Available" automatically, or does it need processing first?

---

## F2. Lot Inward (How Stock Enters the System)

> A "Lot" is a batch of raw material or jewelry received together from a supplier or karigar. It is the first step in the inventory lifecycle — stock enters as a lot, then individual tags are created from the lot pieces.

### F2-A. Creating a New Lot

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Lot header created | Lot number generated, linked to supplier/karigar, branch, date, and receiving employee | | |
| 2 | Day closing date checked | The lot date is based on the branch's day closing date, not the calendar date | | |
| 3 | Lot images uploaded | Product photos can be uploaded and stored in a folder per lot | | |

### F2-B. Lot Items (Per Piece/Design in the Lot)

| # | Detail Captured | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Product & design | What type of jewelry and which design | | |
| 2 | Purity & metal type | The metal composition | | |
| 3 | Piece count & weights | Number of pieces, gross weight, less weight, net weight | | |
| 4 | Stone details | Each stone: type, weight, pieces, rate — stored per item | | |
| 5 | Stone certificate images | Certificate photos (precious, semi-precious, normal) can be captured and stored | | |
| 6 | Other metals | Additional metals in the piece (e.g., mixed metal items) | | |
| 7 | Other charges | Any extra charges related to this lot item | | |

### F2-C. Non-Tag Stock (Loose Items)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | If item is marked as "Non-Tag" (loose stock) | No individual tag will be created — the weight is added to the branch's loose stock pool | | |
| 2 | Existing stock check | The system checks if this product+purity already exists in loose stock for this branch | | |
| 3 | If exists → quantity added | The received weight is added to the existing loose stock quantity | | |
| 4 | If not exists → new record | A new loose stock record is created for this product+purity+branch combination | | |
| 5 | Movement logged | A log entry records this stock movement | | |

### F2-D. Lot Editing

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Lot loaded | Header and all items are loaded for editing | | |
| 2 | Existing items can be modified | Weights, stones, and details can be changed | | |
| 3 | New items can be added | Additional items can be added to the lot | | |
| 4 | **Stones deleted and re-inserted** | Same delete-then-insert pattern as other modules | | |
| 5 | **Other metals and charges NOT cleaned up** | If other metals or charges were removed during edit, the old records remain as orphans | | |

**⚠️ Issue Found:** The branch field is deliberately commented out in the edit form — meaning the receiving branch CANNOT be changed after lot creation, even if it was entered incorrectly.

### F2-E. Lot Deletion

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Header deleted only** | The lot header record is removed from the database | | |
| 2 | **Item details NOT deleted** | Lot item records remain as orphans with no parent lot | | |
| 3 | **Stone details NOT deleted** | Stone records remain as orphans | | |
| 4 | **Other metals/charges NOT deleted** | Same — orphaned | | |
| 5 | Image folder deleted | The lot's image folder is physically removed from the server | | |

**⚠️ CRITICAL Issue:** Lot deletion uses a GET request (no CSRF protection) and only deletes the header — leaving ALL child records as orphans. There is NO check for whether tags have already been created from this lot.

### F2-F. Lot Merge (Combining Multiple Lots)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Source lots selected | Two or more lots are selected for merging | | |
| 2 | New merged lot created | A new lot is created containing items from all selected lots | | |
| 3 | Individual items transferred | Each item from the source lots is recorded in the new lot | | |
| 4 | **Source lots NOT marked as merged** | The original lots remain in the system without any indication that they were merged — they could be used again | | |

### F2-G. Lot Split (Dividing a Lot)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Source lot selected | A lot is selected for splitting | | |
| 2 | Split quantities defined | Staff specifies how to divide each item | | |
| 3 | Source lot marked as "Split" | The original lot is flagged | | |
| 4 | **NO new lot created** | The system only records the split amounts — it does NOT actually create a new lot with the split quantities | | |

### F2-H. Lot Cancel & Lot Close

| # | Operation | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Lot Cancel** | The lot's status is changed to "Cancelled" with a reason, who cancelled, and when | | |
| 2 | **Lot Close** | When all pieces in a lot have been tagged, the lot is manually closed (marked as completed) | | |
| 3 | **Lot Close typo risk** | The system's lot close operation has a coding error where the field name contains a trailing space — this may cause the close to fail silently | | |

**Questions for You:**
- ❓ Can a lot be edited after tags have been generated from it?
- ❓ Should lot deletion be blocked if any tag from it has been sold or transferred?
- ❓ Is lot merge actively used? What's a common scenario where lots are merged?
- ❓ For lot split — should it actually create a new separate lot?

---

## F3. Stock Issue (Sending Items for Melting/Process)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Items issued for melting/processing | Selected jewelry pieces are issued — their status changes to "Metal Issue" | | |
| 2 | Issue details recorded | Who issued them, when, why, and to which destination | | |
| 3 | Stock reports updated | Issued items are excluded from the "available stock" count | | |

---

## F4. Other Inventory (Non-Jewelry Items)

> Besides jewelry, the shop also tracks packaging materials, boxes, bags, gift wraps, etc.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Items tracked | Items like covers, boxes, bags, gift wraps, and other packaging materials | | |
| 2 | Stock added on purchase | When packaging items are purchased, stock count goes up | | |
| 3 | Stock reduced on billing | When items are included in a bill (e.g., gift box with a sale), stock count goes down | | |
| 4 | Stock restored on cancel | If the bill is cancelled, the packaging items are added back to stock | | |

---

## F5. Section Transfer (Moving Items Within a Branch)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Item moved between sections/counters | Within the same branch, a jewelry piece is moved from one display counter to another | | |
| 2 | Movement logged | The transfer is recorded with timestamp | | |
| 3 | Item stays "Available" | No status change — only the section/counter location changes | | |

---

# PART G: BRANCH FLOWS (3 Flows)

> For shops with multiple branches, stock and sales need to be managed across locations.

---

## G1. Branch Transfer (Sending Stock to Another Branch)

> When stock needs to move from one branch to another, a branch transfer is created. This is a 3-step process: Create → Approve (Transit) → Download (Receive). The system supports 5 different types of items for transfer.

### G1-A. Creating a New Transfer

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Transfer code generated | A unique transfer code is auto-generated | | |
| 2 | Source and destination branches selected | Which branch is sending and which is receiving | | |
| 3 | Security check | A one-time form secret (CSRF token) is validated | | |
| 4 | Item type selected | 5 types of items can be transferred (see G1-B) | | |
| 5 | Items added | Specific items are selected for transfer | | |
| 6 | Transfer record created | Master transfer record is saved with status = "Pending" | | |
| 7 | Activity logged | The transfer creation is recorded in the activity log | | |

### G1-B. What Can Be Transferred (5 Types)

| # | Type | What It Contains | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Tagged Items** | Individual jewelry pieces with tags — each tag is validated before adding to the transfer | | |
| 2 | **Non-Tagged Items** | Loose stock (by weight) — no individual tags, transferred by product+purity+weight | | |
| 3 | **Old Metal / Sales Return / Partly Sale** | Old metal from exchanges, returned items, and partly sold items — each has its own sub-type | | |
| 4 | **Packaging Items** | Non-jewelry items (boxes, bags, cards) — transferred by piece count | | |
| 5 | **Repair Orders** | Items linked to repair orders — the transfer moves both the tag and the repair order association | | |

### G1-C. Transit Approval (Step 2 — Items Leave Source Branch)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Day closing dates validated | The system checks that the destination branch's day closing date is not OLDER than the source branch's — to prevent backdated transfers | | |
| 2 | Transfer status → "In Transit" | The master record is updated | | |
| 3 | **Per Item-Type Changes:** | | | |
| 3a | Tagged → tag status = "In Transit" | Each tag is marked as in-transit and its current branch is updated to the destination | | |
| 3b | Non-Tagged → movement logged | Each loose stock item gets a log entry with status "In Transit" | | |
| 3c | Old Metal/SR/PS → transit flags set | Items are flagged as "transferred to stock" | | |
| 3d | Packaging → status = "In Transit" (FIFO/LIFO) | Packaging items are picked in order based on the configured issue preference (first-in-first-out or last-in-first-out) | | |
| 3e | Repair Orders → tag goes in-transit | The associated tag is marked in-transit and the repair order's branch is updated | | |
| 4 | Section logs created | Movement logs are created for auditing | | |

### G1-D. Download (Step 3 — Items Arrive at Destination Branch)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Transfer status → "Downloaded" | The master record is finalized | | |
| 2 | **Per Item-Type Changes:** | | | |
| 2a | Tagged → tag status = "Available" | Each tag becomes available for sale at the new branch | | |
| 2b | Non-Tagged → stock transferred | If this product+purity already exists at the destination → weight is ADDED. If not exists → a new stock record is created | | |
| 2c | Old Metal → branch updated | The old metal item's "current branch" is updated and "transferred" flag is set | | |
| 2d | Packaging → branch updated | Packaging items are moved to the destination with status = "Available" | | |
| 2e | Repair Orders → tag available | The tag becomes available at the new branch, repair order branch is updated | | |
| 3 | Download date and employee recorded | Who received the items and when | | |

### G1-E. Scan-Based Download (Alternative — Tag by Tag)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Instead of bulk download | Staff scans each tag's barcode one at a time | | |
| 2 | Each scan downloads one tag | The individual tag is moved to the destination branch | | |
| 3 | **Auto-completion** | After each scan, the system checks if all tags have been scanned. If yes → the transfer is automatically marked as "Downloaded" | | |
| 4 | Each tag has its own transaction | Unlike bulk download, each scan is its own database transaction | | |

### G1-F. Transfer Cancellation — ⚠️ CRITICAL GAPS

| # | What Should Be Reversed | Actually Reversed? | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Transfer marked as "Cancelled" | ✅ Yes — status updates correctly | | |
| 2 | Items freed from "In Transit" status | ❌ NO — items are **stuck as "In Transit" forever** — they can't be sold or transferred again | | |
| 3 | Items returned to original branch | ❌ NO — items still show as belonging to the destination branch | | |
| 4 | Loose stock restored at source branch | ❌ NO — source branch has permanently lost this stock in reports | | |
| 5 | Section/counter stock restored | ❌ NO | | |
| 6 | Old metal transfer flag restored | ❌ NO | | |
| 7 | Transfer audit log created | ❌ NO — no record of why the transfer was cancelled | | |
| 8 | Packaging/other inventory restored | ❌ NO | | |

**⚠️ SUMMARY:** Branch transfer cancellation only updates the master status — it does NOT reverse ANY of the stock changes made during transit approval. This is one of the most critical bugs in the system: cancelling a transfer after transit approval leaves items permanently stuck.

**Questions for You:**
- ❓ How often are branch transfers cancelled in practice?
- ❓ When a transfer is cancelled mid-transit (items already shipped), what is the physical process — are items returned to the source branch?
- ❓ Is scan-based download used more often than bulk download?
- ❓ For packaging item transfers — is FIFO or LIFO the standard preference?

---

## G2. Branch Management

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Branch created | New branch set up with name, code, address, and contact details | | |
| 2 | Users assigned to branch | Staff members are linked to their working branch | | |
| 3 | Branch-based filtering | All reports, lists, and data are automatically filtered based on the user's branch | | |

---

## G3. Sales Transfer Between Branches

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Return items transferred | Items returned at one branch but originally sold at another branch — the return is credited to the original selling branch | | |
| 2 | Stock updated at both branches | Returned stock is adjusted at both the receiving and selling branches | | |

---

# PART H: CUSTOMER FLOWS (2 Flows)

---

## H1. Customer Registration / Edit / Delete

> A Customer is the **central identity** in the system — everything else (scheme accounts, payments, billing, orders, estimations) is linked to a customer record. This section covers how customers are registered, edited, deleted, and managed.

### H1-A. Before Registering a Customer

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer limit check | If a maximum customer limit is configured in settings, the system checks how many customers already exist. If the limit is reached, staff **cannot register a new customer** | | |
| 2 | Access control | System checks whether the logged-in user has permission to access the customer module. If not → user is redirected to dashboard | | |
| 3 | Registration date | If the branch has a custom entry date feature enabled, the customer's registration date comes from the branch's day-closing date (allows back-dated entries). Otherwise, today's date is used | | |
| 4 | Form opens | A large registration form opens with tabs: Personal Details, Address, KYC Documents, Bank Details, Photos | | |

**Questions for You:**
- ❓ Is the customer limit a total count across all branches, or per-branch? (We found: it's global across all branches — branch-level limits are NOT supported)

### H1-B. Customer Types

| # | Type | How It Works | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Individual** | Standard customer — first name and last name shown | | |
| 2 | **Company** | Company/business customer — company name is stored as the customer name; company name is also recorded in the address | | |

**Questions for You:**
- ❓ Are there any other customer types beyond Individual and Company?
- ❓ Does choosing "Company" type change anything in scheme accounts, billing, or reports?

### H1-C. Personal Details Captured

| # | Field | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | First name | Required | | |
| 2 | Last name | Optional | | |
| 3 | Mobile number | Required. System checks for duplicates before saving. Each mobile must be unique (per company in multi-company setup) | | |
| 4 | Email | Optional. System checks for duplicates | | |
| 5 | Username | For customer portal/app login. Must be unique | | |
| 6 | Password | For portal/app login | | |
| 7 | Gender | Male / Female / Other | | |
| 8 | Date of birth | Used for age calculation and birthday celebration reports | | |
| 9 | Wedding anniversary | Used for anniversary promotions | | |
| 10 | Title | Mr / Mrs / Ms | | |
| 11 | Religion | Optional | | |
| 12 | Marital status | Optional | | |
| 13 | Languages known | Optional | | |
| 14 | Profession | Selected from profession master list | | |
| 15 | VIP flag | Mark customer as VIP for special treatment | | |
| 16 | Promo SMS consent | Whether customer has opted in for promotional messages | | |
| 17 | Admin comments | Internal notes about the customer | | |

### H1-D. Identity Documents Captured

| # | Document | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | PAN number | Captured as text | | |
| 2 | Aadhaar number | Captured as text | | |
| 3 | Driving licence number | Captured as text | | |
| 4 | Passport number | Captured as text | | |
| 5 | Voter ID | Captured as text | | |
| 6 | Ration card number | Captured as text | | |
| 7 | GST number | Captured as text | | |

### H1-E. Address Captured

| # | Field | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Address line 1, 2, 3 | Multi-line address | | |
| 2 | Pincode | PIN code | | |
| 3 | Country / State / City | Selected from dropdown lists (geography masters) | | |
| 4 | Village | Selected from village master. Post office and taluk are auto-filled from the village selected | | |
| 5 | Company name | Appears only for Company-type customers | | |

### H1-F. Nominee Details

| # | Field | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Nominee name | Name of the nominee | | |
| 2 | Nominee relationship | Father / Mother / Spouse / etc. | | |
| 3 | Nominee mobile | Contact number | | |
| 4 | Nominee PAN | For high-value schemes (regulatory requirement) | | |
| 5 | Nominee address | Two address lines | | |

**Questions for You:**
- ❓ Is nominee information mandatory for any schemes, or always optional at registration?
- ❓ Can a nominee be added or changed after a scheme account is opened?

### H1-G. KYC Documents & Images

> In addition to capturing identity numbers (above), the system also accepts **photo/document uploads** of KYC documents:

| # | Document | What Gets Uploaded | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **PAN Card** | Front image, back image, PAN number | | |
| 2 | **Aadhaar Card** | Front image, back image, document file, Aadhaar number | | |
| 3 | **Bank Passbook** | Passbook image, account number, IFSC code, bank name, branch name, account type | | |
| 4 | **Cheque** | Cheque leaf image | | |
| 5 | **Driving Licence / Passport** | Document images | | |

**How images are captured:**
| Method | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| Webcam capture | Staff can use the computer's camera to take a photo directly | | |
| File upload | Staff can upload a saved image or scanned document | | |

**KYC Verification Process:**
| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Documents uploaded during registration (or later during edit) | All KYC documents are **optional** — nothing is mandatory at registration | | |
| 2 | Uploaded KYC appears in the Reports module as "pending verification" | Admin can see all customers with unverified KYC | | |
| 3 | Admin reviews and approves | KYC is marked as verified, customer's KYC status is updated | | |

**Questions for You:**
- ❓ Can multiple bank accounts be saved for one customer? (We found: YES — multiple allowed)
- ❓ Is there a maximum number of KYC documents per customer?
- ❓ Which KYC documents are mandatory for which scheme types?

### H1-H. Wallet Account (Automatically Created)

> Some setups have a "Wallet" feature where each customer gets a digital wallet at registration.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Check if wallet feature is enabled | System checks the global settings | | |
| 2 | Wallet number generated | A unique wallet account number is assigned to the customer | | |
| 3 | Wallet created | A wallet account is created for the customer | | |
| 4 | Welcome SMS sent | If SMS service is enabled, a wallet welcome SMS is sent to the customer | | |
| 5 | Welcome Email sent | If email service is enabled, a welcome email is sent | | |

**⚠️ Issue Found:** If the wallet creation fails (e.g., due to a network issue), the customer registration still goes through successfully — but the customer ends up without a wallet. There is no way to retry wallet creation automatically.

**Questions for You:**
- ❓ Is the wallet feature used by all clients, or only some?
- ❓ Can a wallet be manually created later if the automatic creation fails?

### H1-I. Branch & Employee Recording

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Branch assigned | Customer is linked to the branch where the registration was done | | |
| 2 | Creating employee recorded | The system records which employee registered this customer | | |
| 3 | Customer source | System records how the customer was registered: Admin Panel, Web App, Mobile App, Collection App, Retail App, Import, or Sync | | |
| 4 | Financial year captured | The financial year at the time of registration is stored | | |
| 5 | Custom entry date | If the branch uses custom entry dates, that date is used instead of today's date | | |

**⚠️ Issue Found:** The customer source is **always recorded as "Admin"** regardless of how the customer was actually registered (even from mobile app, web app, etc.). This makes it impossible to track where customers are coming from.

### H1-J. Offline/ERP Data Sync (Only for certain setups)

> Some clients migrate from offline (ERP/software) to our online system. When a new customer is registered and their mobile number matches an existing offline record, the system automatically transfers their old scheme accounts and payments.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Mobile number matched | System checks if this customer's mobile already exists in the offline data staging area | | |
| 2 | Scheme accounts transferred | For each matching offline plan, a scheme account is created in the online system | | |
| 3 | Payments transferred | All past payments from the offline system are imported | | |
| 4 | Cancelled payments handled | Offline cancellations are also imported and marked as cancelled | | |
| 5 | Offline records marked as synced | The original offline records are marked as "transferred" so they don't get imported again | | |

**⚠️ Issue Found:** If this sync process fails partway through, some accounts/payments may be transferred while others are missed — and the offline records may be incorrectly marked as "done". There is no way to retry a failed sync.

**Questions for You:**
- ❓ Is this offline/ERP sync feature actively used by current clients?
- ❓ If a sync fails partially, is there a manual way to fix it?

### H1-K. After Registration is Complete

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer photo saved | If a photo was provided, it is saved on the server | | |
| 2 | KYC document images saved | All uploaded KYC images are saved on the server | | |
| 3 | KYC records created | Separate records are created for PAN, Aadhaar, and each bank account | | |
| 4 | Activity logged | System records who created the customer and when | | |
| 5 | Everything saved together | All customer data is saved as one unit — if any part fails, nothing is saved | | |
| 6 | Redirected to customer list | Staff sees a success message and the updated customer list | | |

### H1-L. Editing a Customer

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | All personal fields can be changed | Same form as registration, pre-filled with existing data | | |
| 2 | Mobile/Email uniqueness re-checked | System checks that the new mobile/email doesn't belong to another customer | | |
| 3 | Address updated | If an address already exists, it is updated. If not (rare case), a new address record is created | | |
| 4 | KYC update | System checks if a KYC document of that type already exists. If yes → updates it. If no → creates a new one. **⚠️ This may sometimes create duplicate KYC records instead of updating the existing one** | | |
| 5 | ⚠️ Customer photo NOT saved on edit | When staff uploads a new photo during edit, the photo is taken but **NOT saved** — the old photo remains. This appears to be a bug | | |
| 6 | New webcam photo can be captured | Staff can re-take the customer photo during edit | | |

**Questions for You:**
- ❓ Should any fields be **locked from editing** after registration (e.g., once a customer has active scheme accounts, should mobile or branch be locked)?
- ❓ Can a customer be moved to a different branch through editing?

### H1-M. Profile Quick Update

> A simplified screen for quickly editing basic customer details without opening the full form.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Search customer | Staff searches by name or mobile number | | |
| 2 | Basic fields edited | Only personal details and address — no KYC or bank changes | | |
| 3 | Changes saved | Saved with activity logging | | |

### H1-N. Deleting a Customer

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Safety check first | Before allowing deletion, the system checks for dependencies | | |

**Deletion is BLOCKED if any of these exist:**

| # | Dependency | What's Checked | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Active scheme accounts | Customer has an active, open scheme account | | |
| 2 | Billing records | Customer has active billing/invoice records | | |
| 3 | Customer orders | Customer has active/pending orders | | |
| 4 | Estimation records | Customer has estimation/quotation records | | |
| 5 | Gift cards/vouchers | Customer has purchased gift cards or vouchers | | |

**What actually gets cleaned up when a customer IS deleted:**

| # | Data | Cleaned Up? | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer record | ✅ Yes — permanently removed | | |
| 2 | Address record | ✅ Yes — permanently removed | | |
| 3 | Wallet account | ✅ Yes — permanently removed | | |
| 4 | KYC documents & records | ❌ NO — KYC data remains in the system without a customer linked to it | | |
| 5 | Activity log of deletion | ❌ NO — no record is created that this customer was deleted, by whom, or when | | |
| 6 | Customer photo on server | ❌ NO — image files remain on the server taking up space | | |
| 7 | KYC document images on server | ❌ NO — document images remain on the server | | |

**Questions for You:**
- ❓ Is customer deletion used in production, or only during initial setup/testing?
- ❓ Should deletion be a "soft delete" (mark as inactive, keep all data) instead of permanently removing the record?
- ❓ Should customers with **closed** (completed) scheme accounts also be blocked from deletion? (Currently only **active** accounts block deletion)

### H1-O. Customer Status Flags

> Each customer has three independent status flags:

| # | Status | What It Means | How It's Changed | ✅/❌/❓ | Comments |
|---|---|---|---|---|---|
| 1 | **Active / Inactive** | Whether the customer is active in the system. Inactive customers are excluded from scheme joining, billing, etc. | Toggled from customer list by admin | | |
| 2 | **Profile Complete / Incomplete** | Whether all required customer information has been collected | Toggled from customer list by admin | | |
| 3 | **KYC Verified / Pending** | Whether the customer's KYC documents have been verified by admin | Set through the KYC approval screen in Reports module | | |

**Status Combinations — What Should Happen?**

| # | State | Your Expected Behavior | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Active + Profile Complete + KYC Verified | Everything works — customer can join schemes, make payments, etc. | | |
| 2 | Active + Profile Incomplete + KYC Pending | Can this customer still join schemes? Can they make payments? | | |
| 3 | Inactive + Profile Complete + KYC Verified | Should this customer be blocked from ALL operations? | | |
| 4 | Active + Profile Complete + KYC Pending | Can this customer join schemes that require KYC verification? | | |

**Questions for You:**
- ❓ If a customer is marked inactive but already has active scheme accounts — what should happen to those accounts?
- ❓ Should "Profile Incomplete" block any operations (scheme joining, payments)?
- ❓ At what point is KYC verification mandatory — at scheme joining? At first payment? Or is it never mandatory?

### H1-P. Agent & Employee Assignment

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Assign agent to customers | Admin selects multiple customers from the list, chooses an agent, and clicks "Assign" to bulk-assign | | |
| 2 | Assign employee to customers | Same process — select customers, choose an employee, bulk-assign | | |
| 3 | Agent dropdown | Shows all active agents | | |
| 4 | Employee dropdown | Shows all active employees | | |

**⚠️ Issue Found:** Both the agent assignment and employee assignment features have a bug — when the admin clicks "Assign," it looks like it works, but **no agent or employee is actually assigned**. The assignment silently fails every time.

### H1-Q. Issues We Found (Please confirm if these are real problems)

| # | Issue We Found | Severity | ✅ Real Problem / ❌ Not a Problem / ❓ Need Clarification | Comments |
|---|---|---|---|---|
| 1 | **Agent assignment silently fails** — admin thinks agents are assigned, but nothing actually changes | 🔴 HIGH | | |
| 2 | **Employee assignment silently fails** — same problem as agent assignment | 🔴 HIGH | | |
| 3 | **Customer photo not saved when editing** — new photo is captured but old photo remains | 🔴 HIGH | | |
| 4 | **KYC documents not cleaned up on customer deletion** — orphan KYC data remains in the system | 🟡 MED | | |
| 5 | **Customer photos not cleaned up on deletion** — image files remain on the server | 🟡 MED | | |
| 6 | **Customer password not properly secured** — anyone with database access can read all customer passwords in plain text | 🔴 HIGH | | |
| 7 | **Wallet may not be created even though the customer was registered** — wallet creation and customer registration are not fully linked | 🟡 MED | | |
| 8 | **Offline data sync can fail partially** — some accounts/payments transferred, others missed, with no way to retry | 🟡 MED | | |
| 9 | **Customer status can be changed via a simple URL** — someone with knowledge of the URL pattern can activate/deactivate customers without proper authorization | 🟡 MED | | |
| 10 | **Customer source always recorded as "Admin"** — no way to know if customer registered via web app, mobile app, or import | 🟡 MED | | |
| 11 | **Scheme count shows wrong number** — when viewing a customer's profile, the system shows the scheme count of a completely different customer | 🔴 HIGH | | |
| 12 | **Customers with completed scheme history can be deleted** — only active accounts prevent deletion; customers with closed/completed scheme history can still be deleted, losing that history | 🟡 MED | | |

---

## H2. Old Metal Pocket (Customer Gold Exchange)

> When a customer brings old jewelry (broken chains, old rings, etc.), the shop takes it and processes it through melting and testing to determine its actual pure gold/silver content. This lifecycle is tracked through a "Pocket."

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Pocket Created** | Old metal is received from the customer — weight is recorded and it is stored in a collection pocket | | |
| 2 | **Melting** | The pocket is sent for melting — melt loss (weight lost during melting) is recorded | | |
| 3 | **Testing** | The melted metal is tested for purity — the actual pure metal weight is calculated | | |
| 4 | **Refining** | If needed, the metal is sent for further refining to improve purity | | |
| 5 | **Pocket Closed** | The final pure weight is determined and the pocket is marked as closed | | |

**Pocket Lifecycle — All Possible States:**

| # | Status | What It Means | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Open / Created** | Old metal received but not yet processed | | |
| 2 | **Melting In Progress** | Sent for melting | | |
| 3 | **Tested** | Purity testing completed | | |
| 4 | **Refining** | Sent for further purification | | |
| 5 | **Closed** | Final pure weight determined, pocket completed | | |

**⚠️ CRITICAL Issue:** There is **NO cancel or delete operation** for old metal pockets. Once a pocket is created — even by mistake — it cannot be removed without directly editing the database. There is no way to fix errors through the normal system interface.

**Questions for You:**
- ❓ How long does the melting + testing process typically take (days/weeks)?
- ❓ Is there a need to be able to cancel a pocket (e.g., wrong weight entered, or customer changed their mind)?
- ❓ When old metal value is used against a bill (A2), and the bill is cancelled — what should happen to the pocket?

---

# PART I: PAYMENT & RECEIPT FLOWS (4 Flows)

> These flows cover payments that happen outside of regular billing — online payments, credit collections, advances, and purchase orders.

---

## I1. Online Payment (Payment Gateway)

> When a customer pays their scheme installment through the mobile app or customer portal using a payment gateway (like Razorpay, PayU, etc.)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer initiates payment | Customer opens the app/portal, selects their scheme account, enters the amount, and chooses a payment gateway | | |
| 2 | Payment marked as "Pending" | A pending payment record is created before the customer is redirected to the gateway page | | |
| 3 | Gateway processes payment | Customer enters card/UPI details and completes the payment on the gateway's page | | |
| 4 | Gateway sends confirmation | The payment gateway sends back a success or failure notification — the system verifies the authenticity of this response | | |
| 5 | Payment status updated | Payment is marked as "Successful" or "Failed" based on the gateway's response | | |
| 6 | Receipt generated + SMS sent | On success: a receipt is generated and a confirmation SMS is sent to the customer | | |
| 7 | External system sync | If ERP integration is enabled, the payment is synced to the external system | | |

**Questions for You:**
- ❓ Which payment gateways are currently active/used by clients?
- ❓ What happens if a payment times out at the gateway (neither success nor failure)?

---

## I2. Credit Collection (Collecting Payment on a Previous Unpaid Bill)

> When a customer had an unpaid (credit) bill and comes back to pay the outstanding amount.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Credit collection bill created | A collection record is created, linked to the original unpaid bill | | |
| 2 | Payment recorded | Customer pays the outstanding amount (Cash, Card, or Cheque) | | |
| 3 | Original bill status updated | The original bill is marked as "Paid" (or "Partially Paid" if only part of the amount was collected) | | |
| 4 | Accounting entries created | Financial entries for the collection | | |

**What happens when a credit collection is CANCELLED:**

| # | Step | Reversed? | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Original bill status reopened | ✅ Yes — but it sets status to "Unpaid" regardless of whether partial payments exist | | |
| 2 | Accounting entries reversed | ❌ NO — financial entries remain, making reports inaccurate | | |

**Questions for You:**
- ❓ Is partial payment (paying part of the outstanding amount) supported?
- ❓ Is there a due date / credit period for unpaid bills?

---

## I3. Issue / Receipt (Advance Given / Money Borrowed)

> This covers cash advances given TO employees/customers, and the receipt of money back FROM them.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Issue (Money Given Out)** | An advance or loan amount is given to a customer or employee | | |
| 2 | Payment recorded | The cash/card payment made to the borrower is recorded | | |
| 3 | Accounting entries created | Financial entries (borrower owes money) | | |
| 4 | **Receipt (Money Returned)** | The borrower returns the advance/loan amount | | |
| 5 | Return payment recorded | The cash/card payment received from the borrower is recorded | | |
| 6 | Accounting entries created | Financial entries (borrower's balance reduced) | | |

**⚠️ Issue Found:** When an issue or receipt is cancelled, the accounting entries are NOT reversed — same gap as in all other bill types.

---

## I4. Purchase Order (Ordering from Supplier)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | PO created | A purchase order is created with: supplier, items/designs needed, quantities, and agreed rates | | |
| 2 | PO items recorded | Each item's details (product, weight, rate) are recorded | | |
| 3 | PO approved | The purchase order goes through an approval workflow | | |
| 4 | Goods received (GRN) | When the supplier delivers, a Goods Received Note is created — this triggers Lot Inward (F2) | | |
| 5 | PO payment made | Payment is made to the supplier against the PO | | |

**Questions for You:**
- ❓ Can a PO be partially fulfilled (supplier delivers some items, rest pending)?
- ❓ Is there a PO approval hierarchy (e.g., manager approval needed above a certain amount)?

---

# PART J: REPAIR FLOW (1 Flow)

---

## J1. Repair Order → Repair Delivery

> When a customer brings a jewelry piece for repair (resizing, polishing, fixing broken parts, etc.)

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Repair order created | A repair order is created with customer details and the item's issues/requirements | | |
| 2 | Item tagged for repair | The item's status is changed to "Under Repair" in the system | | |
| 3 | Repair work completed | The karigar (craftsman) completes the repair work | | |
| 4 | Repair delivery bill created | A delivery bill is created with the repair charges | | |
| 5 | Payment recorded | Customer pays the repair charges | | |
| 6 | Order item marked as "Delivered" | The item's status is updated to "Delivered" | | |
| 7 | Accounting entries created | Financial entries for the repair service | | |

**⚠️ Issue Found:** There is no status guard — an item can be marked as "Delivered" without first being marked as "Completed" (repair finished). This means a delivery bill can be created before the repair is actually done.

**Questions for You:**
- ❓ Does the repair process involve any intermediate steps between receiving and completing (e.g., estimation, approval of charges)?
- ❓ Can a customer's own item (not from our shop) be taken for repair?

---

# PART K: SETTINGS & CONFIGURATION (4 Flows)

---

## K1. General Settings

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | Settings changes take effect **immediately** across all modules — there is no "save and apply later" | | |
| 2 | 60+ individual settings control how the system behaves (receipt numbering, GST, wallet limits, metal rates, and more) | | |
| 3 | All billing, scheme, and report behavior is driven by these settings | | |

**Questions for You:**
- ❓ Who has access to change settings? Is it restricted to admin only?
- ❓ Is there a change log that records what was changed, when, and by whom?
- ❓ Can different branches have different settings (e.g., different GST rates)?

---

## K2. Metal Rate Update

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Admin updates metal rate | Gold, Silver, and Platinum rates are entered in the settings | | |
| 2 | Rate published to mobile app | The new rate is written to a file that the mobile app reads | | |
| 3 | All operations use the new rate immediately | Every billing, estimation, and scheme calculation uses the updated rate right away — no caching | | |

**Questions for You:**
- ❓ How often are rates updated? Multiple times a day?
- ❓ Is there a rate history maintained? Can you see what the rate was on a past date?
- ❓ Is the rate per gram, per 10g, or per 8g?

---

## K3. Access Control (Who Can Do What)

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | Each menu item and page has access control — only users with the right role can access it | | |
| 2 | Users can only see data from their assigned branch | | |
| 3 | Report visibility is controlled by settings — some reports may be hidden from certain roles | | |

**Questions for You:**
- ❓ How many user roles exist? (e.g., Admin, Manager, Sales Staff, Cashier, etc.)
- ❓ Can a user be assigned to multiple branches?
- ❓ Are there any operations that should require two-person authorization (dual control)?

---

## K4. Day Closing

> Day closing is a process done at the end of each business day. It affects how dates work in the system.

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Day close performed per branch | Each branch closes its day independently | | |
| 2 | Bill dates set from day closing | All bills use the "last closed date + 1" as their date (not the actual calendar date) | | |
| 3 | Reports filtered by day close date | Date-based reports respect the day closing dates, not calendar dates | | |

**Questions for You:**
- ❓ What happens if a branch forgets to close the day for several days — do bills pile up on the wrong date?
- ❓ Can a day close be undone/reopened if done by mistake?

---

# PART L: KYC & REPORTING FLOWS (9 Flows)

---

## L1. KYC Approval Flow

| # | Step | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Customer submits KYC | Documents are uploaded from the mobile app, customer portal, or by staff during registration | | |
| 2 | Admin sees pending list | All customers with unverified KYC appear in a pending list in the Reports module | | |
| 3 | Admin reviews and approves | Admin checks the documents and marks them as verified or rejected | | |

**Note:** KYC approval is done from the **Reports module**, not from the Customer module. This may be confusing for staff.

**Questions for You:**
- ❓ Is there a different approval for different document types (PAN vs Aadhaar vs Bank)?
- ❓ Can KYC be auto-verified using any external service (e.g., Aadhaar OTP verification)?

---

## L2. Cash Summary Report

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | Shows all cash movements for a day or period | | |
| 2 | Includes: billing payments, issue/receipt transactions, advances, and scheme adjustments | | |
| 3 | ⚠️ The accuracy of this report depends on accounting entries being correct — since bill cancellations don't reverse accounting entries (see B1), this report may show incorrect numbers | | |

---

## L3. Day Transactions Report

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | Shows all transactions for a specific day and branch | | |
| 2 | Includes: bills, issue/receipt, payments, and cancellations | | |

---

## L4. Stock In/Out Report

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | Shows all jewelry movement: items received, sold, transferred between branches, and returned | | |
| 2 | Based on each item's tag status and movement history | | |

---

## L5. GST Reports

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | GSTR-1 and GSTR-3B reports are generated from billing data | | |
| 2 | HSN-wise summary is generated based on product categorization | | |

**Questions for You:**
- ❓ Are the GST reports currently accurate enough for direct filing, or do they need manual correction?
- ❓ Is e-Way Bill generation supported?

---

## L6. Branch Transfer Report

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | Shows all transfers between branches with their current status | | |
| 2 | Statuses shown: Created, In Transit, Downloaded (received), Cancelled | | |

---

## L7. Scheme / Savings Plan Reports

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | **Collection Summary** — scheme-wise total collections for a period | | |
| 2 | **Outstanding Report** — customers who haven't paid their installments (overdue list) | | |
| 3 | **Maturity Report** — customers whose schemes are due for closure/maturity | | |
| 4 | **Member Report** — complete list of all scheme members with their status | | |

**Questions for You:**
- ❓ Are there any other scheme reports needed (e.g., agent-wise collection, branch-wise collection)?
- ❓ Is there a report for scheme profitability (total collected vs total benefit paid)?

---

## L8. Customer Ledger

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | Shows the complete transaction history for a single customer | | |
| 2 | Includes: all bills, payments, returns, credits, advances, and scheme transactions | | |

---

## L9. Purchase / Supplier Report

| # | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|
| 1 | **Purchase Summary** — total purchases by supplier for a period | | |
| 2 | **Purchase Return Tracking** — items returned to suppliers | | |
| 3 | **Supplier Outstanding** — how much is owed to each supplier | | |

---

# PART M: STATUS CODES REFERENCE

> Below are all the status values used across the system. We listed them so you can verify that we've captured all possible states correctly.

---

## M1. Payment Status

| # | Status | What It Means | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Successful** | Payment completed and confirmed | | |
| 2 | **Awaiting Admin Approval** | Payment made but needs admin to approve before it's finalized | | |
| 3 | **Failed** | Payment attempt failed | | |
| 4 | **Cancelled** | Payment was cancelled after it was recorded | | |
| 5 | **Refund Processed** | A refund has been issued against this payment | | |
| 6 | **Pending (Gateway)** | Payment initiated on payment gateway but not yet confirmed | | |
| 7 | **Failed (Legacy)** | Old/legacy failure status from a previous system version | | |

**Questions for You:**
- ❓ Is "Awaiting Admin Approval" actively used? In what scenarios does a payment need admin approval?
- ❓ Is there a "Partial Refund" status, or is it always full refund?

---

## M2. Bill Types

| # | Bill Type | Description | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Regular Sales** | Standard customer sale | | |
| 2 | **Sales + Old Metal** | Sale with old jewelry exchange | | |
| 3 | **Sales Return Exchange** | Return old items + buy new items | | |
| 4 | **Purchase** | Receiving goods from supplier | | |
| 5 | **Order Advance** | Advance payment against a customer order | | |
| 6 | *(Not found — possibly unused?)* | We didn't find this type in use — is it reserved or discontinued? | | |
| 7 | **Sales Return (Standalone)** | Customer returns items without buying new ones | | |
| 8 | **Credit Collection** | Collecting payment on a previously unpaid bill | | |
| 9 | **Order Delivery** | Delivering a completed customer order | | |
| 10 | **Scheme Pre-Close** | Early closure of a scheme account | | |
| 11 | **Repair Delivery** | Delivering a repaired item to the customer | | |
| 12 | **Supplier Sales** | Selling items to another supplier/dealer | | |

**Questions for You:**
- ❓ Is Bill Type 6 reserved for something? Or was it used in an older version?
- ❓ Are there any other bill types not listed above?

---

## M3. Tag Status (Jewelry Piece Lifecycle)

| # | Status | What It Means | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Available** | In stock, ready for sale | | |
| 2 | **Sold** | Purchased by a customer | | |
| 3 | **In Transit** | Being transferred to another branch | | |
| 4 | **Purchased (from Supplier)** | Just received from supplier | | |
| 5 | **Reserved (for Order)** | Set aside for a customer order | | |
| 6 | **Returned** | Returned by a customer | | |
| 7 | **Cancelled (Sale Cancelled)** | The sale was cancelled, item is back | | |
| 8 | **Approval Stock** | Sent for customer viewing/approval | | |
| 9 | **Under Repair** | Given for repair work | | |
| 10 | **Metal Issue** | Sent for melting or reprocessing | | |

---

## M4. Branch Transfer Status

| # | Status | What It Means | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Created** | Transfer order created but not yet shipped | | |
| 2 | **In Transit** | Items shipped, on the way to destination branch | | |
| 3 | **Cancelled** | Transfer was cancelled | | |
| 4 | **Downloaded (Received)** | Destination branch has accepted and received the items | | |

---

## M5. Old Metal Pocket Status

| # | Status | What It Means | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **Open / Created** | Old metal received, pending processing | | |
| 2 | **Melting In Progress** | Sent for melting | | |
| 3 | **Tested** | Purity tested | | |
| 4 | **Refining** | Sent for further purification | | |
| 5 | **Closed** | Final pure weight determined, process complete | | |

---

## M6. Customer Order Status

| # | Status | What It Means | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | **New** | Order just placed | | |
| 2 | **Confirmed** | Order confirmed by the shop | | |
| 3 | **Completed** | Manufacturing/work completed, ready for delivery | | |
| 4 | **Delivered** | Item delivered to the customer | | |
| 5 | **Cancelled** | Order was cancelled | | |

**Questions for You:**
- ❓ Are there other intermediate statuses (e.g., "Manufacturing In Progress", "Quality Check")?
- ❓ Can an order move backward (e.g., from "Completed" back to "In Progress")?

---

# PART N: SMS GATEWAY SUPPORT

> The system sends SMS notifications for various events (payments, account opening, OTP verification, etc.). Multiple SMS gateways are supported:

| # | Gateway | What We Understood | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | MSG91 | Supported and found in the system | | |
| 2 | Nettyfish | Supported and found in the system | | |
| 3 | Pinnacle | Supported and found in the system | | |
| 4 | Way2Mint | Supported and found in the system | | |
| 5 | SMSJUST | Supported and found in the system | | |

**⚠️ Issue Found:** The same SMS sending logic is **copy-pasted in 16 different places** across the system. This means if a gateway changes their API, 16 places need to be updated manually — very error-prone.

**Questions for You:**
- ❓ Which gateways are currently active with live clients?
- ❓ Are there any other notification channels used (WhatsApp, Push Notifications)?

---

# PART O: MODULE CONNECTIONS (How Everything is Linked)

> Understanding which modules depend on which others is important for knowing the impact of changes.

| # | Module | What It Does | Depends On | ✅/❌/❓ | Comments |
|---|---|---|---|---|---|
| 1 | **Settings** | Central configuration — controls how everything else works | Nothing (standalone) | | |
| 2 | **Scheme** | Defines savings plan rules and templates | Settings | | |
| 3 | **Account** | Manages scheme account lifecycle (open → pay → close) | Settings, Scheme, Payment | | |
| 4 | **Payment** | Processes all payments (online, offline, collections) | Settings, Scheme, Account | | |
| 5 | **Reports** | All reports, dashboards, and KYC approval | Settings, Payment, Account | | |
| 6 | **Tagging** | Individual jewelry piece management | Settings, Lot Inward | | |
| 7 | **Estimation** | Customer enquiry and quotation | Settings, Tagging, Customer | | |
| 8 | **Billing** | Bill creation, cancellation, and all bill types | Settings, Estimation, Tagging, Account | | |

**Modules NOT yet fully analyzed:**

| # | Module | Coverage | ✅/❌/❓ | Comments |
|---|---|---|---|---|
| 1 | Purchase | Not analyzed | | |
| 2 | Customer Master | Not analyzed | | |
| 3 | Employee | Not analyzed | | |
| 4 | Mobile API | Not analyzed | | |
| 5 | Dashboard | Not analyzed | | |
| 6 | Wallet | Not analyzed | | |
| 7 | Lot Inward | Not analyzed | | |
| 8 | Branch Transfer | Not analyzed | | |

**Questions for You:**
- ❓ Are there any other modules or features not listed above that the system has?
- ❓ Which of the unanalyzed modules is most critical to analyze next?

---

# TEAM SIGN-OFF

> Please review each part and sign off once you've verified the flows are correct or have provided your comments.

| # | Part | Section | Reviewed By | Date | ✅ / ❌ / ❓ |
|---|---|---|---|---|---|
| 1 | A | Billing Flows (6 flows) | | | ☐ |
| 2 | B | Bill Cancellation | | | ☐ |
| 3 | C | Order Flows (4 flows) | | | ☐ |
| 4 | D | Scheme/Savings Plan Flows (5 flows) | | | ☐ |
| 5 | E | Estimation Flows (4 flows) | | | ☐ |
| 6 | F | Stock/Inventory Flows (5 flows) | | | ☐ |
| 7 | G | Branch Flows (3 flows) | | | ☐ |
| 8 | H | Customer Flows (2 flows) | | | ☐ |
| 9 | I | Payment/Receipt Flows (4 flows) | | | ☐ |
| 10 | J | Repair Flow (1 flow) | | | ☐ |
| 11 | K | Settings & Configuration (4 flows) | | | ☐ |
| 12 | L | KYC & Reports (9 flows) | | | ☐ |
| 13 | M | Status Codes | | | ☐ |
| 14 | N | SMS Gateways | | | ☐ |
| 15 | O | Module Connections | | | ☐ |

**Overall Assessment**: ☐ All Correct / ☐ Corrections Needed / ☐ Significant Gaps Found

**Signatures:**

| Name | Role | Signature | Date |
|---|---|---|---|
| | | | |
| | | | |
| | | | |

