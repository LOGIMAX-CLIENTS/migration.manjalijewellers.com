# Auto Debit Implementation Workflow

> A complete, reusable blueprint for implementing Cashfree Subscription-based recurring auto-debit for monthly installment schemes (jewellery savings plans).  
> **Version**: 1.0 | **Last Updated**: 19-Jun-2026

---

## Table of Contents

1. [Concept Overview](#concept-overview)
2. [Prerequisites — Cashfree Dashboard Setup](#prerequisites--cashfree-dashboard-setup)
3. [Database Schema](#database-schema)
4. [Configuration Settings](#configuration-settings)
5. [Phase 1 — Subscribe (Create Subscription)](#phase-1--subscribe-create-subscription)
6. [Phase 2 — Customer Authorization (UI)](#phase-2--customer-authorization-ui)
7. [Phase 3 — Authorization Callback (Return URL)](#phase-3--authorization-callback-return-url)
8. [Phase 4 — Webhook: Monthly Auto-Payment](#phase-4--webhook-monthly-auto-payment)
9. [Phase 5 — Webhook: Payment Declined](#phase-5--webhook-payment-declined)
10. [Phase 6 — Webhook: Status Change](#phase-6--webhook-status-change)
11. [Phase 7 — Unsubscribe (Cancel)](#phase-7--unsubscribe-cancel)
12. [Phase 8 — Retry Failed Payment](#phase-8--retry-failed-payment)
13. [Payment Blocking Logic](#payment-blocking-logic)
14. [Due Month/Year Calculation Algorithm](#due-monthyear-calculation-algorithm)
15. [Metal Weight Conversion](#metal-weight-conversion)
16. [Post-Payment Processing Checklist](#post-payment-processing-checklist)
17. [SMS / Notification Triggers](#sms--notification-triggers)
18. [Logging Strategy](#logging-strategy)
19. [Security Considerations](#security-considerations)
20. [Mobile App Integration](#mobile-app-integration)
21. [Status Code Reference](#status-code-reference)
22. [API Reference — Cashfree Endpoints](#api-reference--cashfree-endpoints)
23. [Implementation Checklist](#implementation-checklist)

---

## Concept Overview

```
Customer joins a Scheme (e.g., 11-month gold savings)
    │
    ▼
Customer clicks "Subscribe" on their scheme account
    │
    ▼
System creates a Cashfree Subscription (links to a pre-configured Plan)
    │
    ▼
Customer authorizes via bank (UPI / NetBanking / Debit Card on Cashfree hosted page)
    │
    ▼
Subscription becomes ACTIVE
    │
    ▼
Every month, Cashfree auto-debits the installment amount from customer's bank
    │
    ▼
Cashfree sends Webhook → System creates Payment record automatically
    │
    ▼
After all installments → Subscription status changes to COMPLETED
```

**Key Principle**: One subscription per scheme account. Customer subscribes once, monthly payments happen automatically until expiry or cancellation.

### Flow Diagram

```
┌──────────────┐     ┌────────────────┐     ┌──────────────────┐
│   Customer   │────▶│   Your Server  │────▶│  Cashfree API    │
│   (Web/App)  │     │   (Backend)    │     │  (Payment GW)    │
└──────┬───────┘     └───────┬────────┘     └────────┬─────────┘
       │                     │                       │
       │  1. Click Subscribe │                       │
       │────────────────────▶│  2. Create sub in DB  │
       │                     │  3. POST /subscriptions│
       │                     │──────────────────────▶│
       │                     │     4. Return authLink │
       │                     │◀──────────────────────│
       │  5. Show Authorize  │                       │
       │◀────────────────────│                       │
       │                     │                       │
       │  6. Open authLink   │                       │
       │─────────────────────────────────────────────▶
       │                     │  7. Customer authorizes│
       │                     │     (UPI/NB/Card)     │
       │                     │                       │
       │                     │  8. POST to returnUrl │
       │                     │◀──────────────────────│
       │                     │  9. Update status     │
       │  10. Show "Active"  │                       │
       │◀────────────────────│                       │
       │                     │                       │
       │                     │  MONTHLY:             │
       │                     │  11. Webhook: payment │
       │                     │◀──────────────────────│
       │                     │  12. Insert payment   │
       │  13. SMS sent       │                       │
       │◀────────────────────│                       │
```

---

## Prerequisites — Cashfree Dashboard Setup

> **Do this BEFORE writing any code.**

### Step 1: Create Cashfree Account
- Sign up at [cashfree.com](https://www.cashfree.com)
- Get your **Client ID** and **Client Secret** from the dashboard

### Step 2: Create Subscription Plans
Each jewellery scheme needs a corresponding plan on Cashfree:

| Cashfree Plan Field | Maps To | Example |
|---|---|---|
| Plan ID | `scheme.sync_scheme_code` | `GOLD_1000_MONTHLY` |
| Plan Name | Scheme name | "Gold Savings 1000" |
| Type | Always "PERIODIC" | `PERIODIC` |
| Amount | Monthly installment | `1000` |
| Interval Type | Always "month" | `month` |
| Interval Value | Always `1` (monthly) | `1` |
| Max Cycles | Total installments | `11` |

### Step 3: Configure Webhook URL
- In Cashfree dashboard → Subscriptions → Settings
- Set webhook URL to: `https://yourdomain.com/autodebit/webhook`

### Step 4: Note API URLs

| Environment | Base URL |
|---|---|
| Test/Sandbox | `https://test.cashfree.com/` |
| Production | `https://api.cashfree.com/` |

---

## Database Schema

### Table 1: `auto_debit_subscription` (NEW TABLE)

Core table tracking each subscription.

```sql
CREATE TABLE `auto_debit_subscription` (
    `id_auto_debit_subscription` INT AUTO_INCREMENT PRIMARY KEY,
    `id_scheme_account`     INT NOT NULL,              -- FK → scheme_account
    `subscription_id`       VARCHAR(100) NOT NULL,      -- Unique ID you generate and send to Cashfree
    `sub_reference_id`      VARCHAR(100) DEFAULT NULL,  -- Cashfree's reference ID (returned by their API)
    `plan_id`               VARCHAR(100) NOT NULL,      -- Cashfree plan ID (matches scheme's sync_scheme_code)
    `auth_link`             TEXT DEFAULT NULL,           -- Cashfree authorization URL for customer to click
    `auth_status`           TINYINT DEFAULT 1,           -- 1=INIT, 2=PENDING, 3=ACTIVE, 4=ON_HOLD, 5=CANCELLED, 6=COMPLETED
    `message`               TEXT DEFAULT NULL,           -- Last response message from Cashfree
    `first_charge_delay`    INT DEFAULT 0,               -- Days to delay first charge after authorization
    `expires_on`            DATETIME DEFAULT NULL,       -- When subscription auto-expires
    `status`                TINYINT DEFAULT 0,           -- 0=inactive record, 1=active record (soft delete flag)
    `added_by`              TINYINT DEFAULT 0,           -- 0=Web App, 1=Mobile App, 2=Admin
    `created_on`            DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_update`           DATETIME DEFAULT NULL,

    INDEX `idx_scheme_account` (`id_scheme_account`),
    INDEX `idx_subscription_id` (`subscription_id`),
    INDEX `idx_sub_reference_id` (`sub_reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Table 2: Add column to `scheme_account` (EXISTING TABLE)

```sql
ALTER TABLE `scheme_account` 
ADD COLUMN `auto_debit_status` TINYINT DEFAULT 0 
    COMMENT '0=Not subscribed, 1=INITIALIZED, 2=BANK_PENDING, 3=ACTIVE, 4=ON_HOLD, 5=CANCELLED, 6=COMPLETED';
```

### Table 3: Add columns to `scheme` (EXISTING TABLE — Scheme Master)

```sql
ALTER TABLE `scheme` 
ADD COLUMN `auto_debit_plan_type` TINYINT DEFAULT 0 
    COMMENT '0=Auto-debit not available for this scheme, 1=Auto-debit enabled',
ADD COLUMN `sync_scheme_code` VARCHAR(100) DEFAULT NULL 
    COMMENT 'Cashfree Plan ID mapped to this scheme';
```

### Table 4: Add settings to `chit_settings` (EXISTING TABLE — Global Config)

```sql
ALTER TABLE `chit_settings`
ADD COLUMN `auto_debit` TINYINT DEFAULT 0 
    COMMENT '0=Feature OFF globally, 1=Feature ON',
ADD COLUMN `auto_debit_allow_app_pay` TINYINT DEFAULT 1 
    COMMENT '0=Block manual payments when subscribed, 1=Always allow manual, 2=Block only when subscription is ACTIVE';
```

### Table 5: `gateway` (EXISTING TABLE — Payment Gateway Credentials)

```sql
-- Ensure a record exists for Cashfree Subscription gateway
-- Key fields:
--   pg_code    = 4              (identifies Cashfree Subscription)
--   param_1    = Client Secret
--   param_3    = Client ID  
--   api_url    = https://api.cashfree.com/ (production) or https://test.cashfree.com/ (sandbox)
--   id_branch  = branch FK      (for multi-branch credential separation)
--   is_default = 1              (marks the active gateway)

-- Example insert:
INSERT INTO `gateway` (pg_code, param_1, param_3, api_url, id_branch, is_default)
VALUES (4, 'YOUR_CLIENT_SECRET', 'YOUR_CLIENT_ID', 'https://test.cashfree.com/', 1, 1);
```

### Table 6: `payment` (EXISTING TABLE — needs these columns)

```sql
-- Ensure these columns exist in your payment table:
-- payment_type       VARCHAR(50)   → Will store "Cash Free"
-- payment_mode       VARCHAR(50)   → Will store "Subscription"
-- payment_ref_number VARCHAR(100)  → Cashfree's cf_paymentId (unique per charge)
-- added_by           TINYINT       → 4 = Cashfree Subscription
-- payment_status     TINYINT       → 1=success, 2=awaiting approval, 3=failure
-- due_type           VARCHAR(5)    → 'ND'=Normal, 'AD'=Advance, 'PD'=Pending
-- due_month          INT           → Month the payment is attributed to (1-12)
-- due_year           INT           → Year the payment is attributed to
-- metal_rate         DECIMAL(10,2) → Gold rate at time of payment
-- metal_weight       DECIMAL(10,3) → Calculated weight (for weight-based schemes)
-- remark             TEXT          → Auto-filled with payment details or failure reason
```

---

## Configuration Settings

Define these in your application config file:

```
# Payment auto-approval for auto-debit payments
auto_pay_approval:
  1 = Auto-approve (mark payment as SUCCESS immediately)
  2 = Auto-approve + sync to ERP/intermediate tables
  3 = Auto-approve + sync + intermediate table insert
  Any other value = Require manual admin approval (mark as AWAITING)

# Manual payment behavior when auto-debit is active
auto_debit_allow_app_pay:
  0 = Block ALL manual app/web payments when any subscription exists
  1 = Always allow manual payments alongside auto-debit  
  2 = Block manual payments ONLY when subscription is ACTIVE (status=3)
```

---

## Phase 1 — Subscribe (Create Subscription)

### Trigger
Customer clicks **"Subscribe"** button on their scheme account details page.

### Route
```
POST /subscription/create/{id_scheme_account}
```

### Algorithm

```
FUNCTION createSubscription(id_scheme_account):

    // ─── STEP 1: Fetch Required Data ───
    planDetail = DB.query("""
        SELECT 
            c.firstname, c.lastname, c.email, c.mobile,
            s.sync_scheme_code, s.total_installments,
            sa.id_scheme_account, sa.id_branch,
            -- Count paid installments
            IFNULL(paid_installment_count, 0) as paid_installments,
            -- Gateway credentials
            g.param_1 as client_secret, 
            g.param_3 as client_id,
            g.api_url,
            -- Check if subscription already exists
            IFNULL(ad.id_auto_debit_subscription, 0) as existing_subscription
        FROM scheme_account sa
            JOIN customer c ON sa.id_customer = c.id_customer
            JOIN scheme s ON s.id_scheme = sa.id_scheme
            LEFT JOIN auto_debit_subscription ad 
                ON ad.id_scheme_account = sa.id_scheme_account AND ad.status = 1
            LEFT JOIN gateway g 
                ON g.id_branch = sa.id_branch AND g.is_default = 1 AND g.pg_code = 4
            LEFT JOIN (
                SELECT id_scheme_account, COUNT(*) as paid_installment_count
                FROM payment WHERE payment_status IN (1, 2)
                GROUP BY id_scheme_account
            ) p ON p.id_scheme_account = sa.id_scheme_account
        WHERE sa.id_scheme_account = {id_scheme_account}
    """)

    // ─── STEP 2: Validation / Guard Checks ───
    IF planDetail IS EMPTY:
        RETURN error("No scheme account found")

    IF planDetail.existing_subscription > 0:
        RETURN error("Already subscribed. Check authorization status.")

    pendingInstallments = planDetail.total_installments - planDetail.paid_installments
    
    IF pendingInstallments <= 0:
        RETURN error("All installments already paid")

    IF planDetail.client_id IS NULL OR planDetail.api_url IS NULL:
        RETURN error("Payment gateway not configured")

    // ─── STEP 3: Generate Unique Subscription ID ───
    subscriptionId = generateUniqueId()
    // Examples: uniqid(time()), UUID v4, or "SUB_" + timestamp + random

    // ─── STEP 4: Calculate Subscription Expiry ───
    expiresOn = addMonths(CURRENT_DATETIME, pendingInstallments)
    // e.g., if 8 pending → expires 8 months from now
    // Format: "2025-12-31 23:59:59"

    // ─── STEP 5: Handle Missing Email ───
    // Cashfree REQUIRES an email. Generate a dummy one if customer has none.
    customerEmail = planDetail.email
    IF customerEmail IS EMPTY:
        customerEmail = randomString(8) + "@placeholder.com"

    // ─── STEP 6: Insert Local DB Record (before calling Cashfree) ───
    insertId = DB.insert("auto_debit_subscription", {
        id_scheme_account:  id_scheme_account,
        subscription_id:    subscriptionId,
        plan_id:            planDetail.sync_scheme_code,
        first_charge_delay: 0,
        expires_on:         expiresOn,
        status:             0,        // Inactive until Cashfree confirms
        created_on:         NOW(),
        added_by:           0         // 0=Web, 1=Mobile
    })

    // ─── STEP 7: Call Cashfree Create Subscription API ───
    apiPayload = {
        "subscriptionId":    subscriptionId,
        "planId":            planDetail.sync_scheme_code,
        "customerName":      planDetail.firstname + " " + planDetail.lastname,
        "customerEmail":     customerEmail,
        "customerPhone":     planDetail.mobile,
        "firstChargeDelay":  1,       // Delay first charge by 1 day (0 = charge immediately)
        "authAmount":        1,       // ₹1 authorization test charge (refundable)
        "expiresOn":         expiresOn,
        "returnUrl":         BASE_URL + "/autodebit/callback/" + id_scheme_account
    }

    response = HTTP_POST(
        url:     planDetail.api_url + "api/v2/subscriptions/",
        headers: {
            "X-Client-Id":     planDetail.client_id,
            "X-Client-Secret": planDetail.client_secret,
            "Content-Type":    "application/json"
        },
        body: JSON.encode(apiPayload)
    )

    // ─── STEP 8: Handle API Response ───
    IF response.status == "OK":
        // Cashfree returned: subReferenceId, authLink, subStatus, message
        
        DB.update("auto_debit_subscription", WHERE id = insertId, {
            sub_reference_id: response.subReferenceId,
            auth_status:      MAP_STATUS(response.subStatus),
            auth_link:        response.authLink,
            message:          response.message,
            status:           1,       // Mark as active record
            last_update:      NOW()
        })

        DB.update("scheme_account", WHERE id_scheme_account = id_scheme_account, {
            auto_debit_status: MAP_STATUS(response.subStatus),
            date_upd:          NOW()
        })

        // Store subscriptionId in session (for web callback verification)
        SESSION.set("CF_subscriptionId", subscriptionId)

        RETURN success(
            "Subscription created. Please authorize by clicking the Authorize button.",
            redirect: "/scheme/account/report/" + id_scheme_account
        )

    ELSE IF response.status == "ERROR":
        RETURN error("Cashfree error: " + response.message)

    ELSE:
        RETURN error("Unexpected response from payment gateway")
```

---

## Phase 2 — Customer Authorization (UI)

### This is Cashfree's hosted page — you don't build this

After Phase 1, your **scheme account details page** should show different UI based on `auto_debit_status`:

```
FUNCTION renderAutoDebitUI(schemeAccount):

    // Only show if: global auto_debit is ON and scheme supports it
    IF settings.auto_debit != 1 OR scheme.auto_debit_plan_type != 1:
        RETURN  // Don't show anything

    SWITCH schemeAccount.auto_debit_status:

        CASE 0 OR 5:  // Not subscribed OR Previously cancelled
            IF schemeAccount.paid_installments < scheme.total_installments:
                RENDER:
                  <p>Subscribe to automate your monthly payments!</p>
                  <button onClick="confirmThen(POST /subscription/create/{id})">
                    Subscribe
                  </button>

        CASE 1:  // INITIALIZED — awaiting customer authorization
            IF schemeAccount.auth_link IS NOT NULL:
                RENDER:
                  <p>Subscription created. Please authorize your bank.</p>
                  <a href="{auth_link}">Click to Authorize</a>

        CASE 2:  // BANK_APPROVAL_PENDING
            RENDER:
              <p style="color:orange">⏳ Bank Authorization Pending</p>

        CASE 3:  // ACTIVE — auto-debit is live
            RENDER:
              <p style="color:green">
                ✅ Auto-Debit Active. Expires: {expires_on}
              </p>
              <button onClick="confirmThen(POST /subscription/cancel/{id})">
                Unsubscribe
              </button>

        CASE 4:  // ON HOLD
            RENDER:
              <p style="color:orange">⚠️ Subscription On Hold</p>

        CASE 6:  // COMPLETED
            RENDER:
              <p style="color:blue">✔️ Subscription Completed</p>
```

### Confirmation Modals

Always use confirmation dialogs for destructive/important actions:

- **Subscribe**: "Are you sure you want to enable auto-debit for this scheme?"
- **Unsubscribe**: "Are you sure you want to cancel auto-debit? Monthly payments will stop."
- **Retry Payment**: "Retry the failed payment? Amount ₹{X} will be debited."

---

## Phase 3 — Authorization Callback (Return URL)

### Route
```
POST /autodebit/callback/{id_scheme_account}
```

Cashfree POSTs to this URL after the customer completes (or fails) authorization.

### Cashfree POST Data — Success Example
```json
{
    "cf_authAmount": "1.00",
    "cf_message": "Subscription Activated successfully",
    "cf_orderId": "SUB_162281687260ba38682436f_AUTH_46715",
    "cf_referenceId": "907582",
    "cf_status": "ACTIVE",
    "cf_subReferenceId": "43225",
    "cf_subscriptionId": "162281687260ba38682436f",
    "signature": "VfPBjwwokjls4Vh9CpQsC5vq0KWB0FCT1fAvw05PjoY="
}
```

### Cashfree POST Data — Already Authorized Example
```json
{
    "cf_authAmount": "1.00",
    "cf_message": "Subscription has already been authorized",
    "cf_status": "ACTIVE",
    "cf_subReferenceId": "43225",
    "cf_subscriptionId": "162281687260ba38682436f",
    "signature": "3KhvbKkyIu1dwajoPULpvgoRcW6cg+2IfxTbjKtV8IM="
}
```

### Algorithm

```
FUNCTION authorizationCallback(id_scheme_account, POST_DATA):

    // ─── STEP 1: Log Everything ───
    logToFile("cf_subscription/" + DATE + ".txt",
        DATETIME + " POST: " + JSON(POST_DATA) + " ID: " + id_scheme_account
    )

    // ─── STEP 2: Validate Input ───
    IF POST_DATA IS EMPTY OR POST_DATA.cf_subscriptionId IS EMPTY:
        logToFile("cf_subscription/" + DATE + ".txt", "Empty POST data")
        RETURN

    // ─── STEP 3: Idempotency — Don't re-process ───
    currentStatus = DB.queryScalar("""
        SELECT auth_status FROM auto_debit_subscription 
        WHERE subscription_id = ?
    """, POST_DATA.cf_subscriptionId)

    IF currentStatus > 2:
        // Already ACTIVE or beyond — this is a duplicate callback
        RETURN response("Status already processed: " + POST_DATA.cf_message)

    // ─── STEP 4: Map Cashfree Status String → Integer ───
    newStatus = MAP_STATUS(POST_DATA.cf_status)

    // ─── STEP 5: Update Subscription Record ───
    DB.update("auto_debit_subscription", 
        WHERE subscription_id = POST_DATA.cf_subscriptionId, 
        SET {
            message:     POST_DATA.cf_message,
            auth_status: newStatus,
            status:      1,
            last_update: NOW()
        }
    )

    // ─── STEP 6: Update Scheme Account Status ───
    DB.update("scheme_account",
        WHERE id_scheme_account = id_scheme_account,
        SET {
            auto_debit_status: newStatus,
            date_upd:          NOW()
        }
    )

    // ─── STEP 7: Redirect Based on Platform ───
    IF isMobileBrowser():
        // Mobile in-app browser — show simple HTML page
        IF POST_DATA.cf_status == "ACTIVE":
            color = "green"
            appRedirectUrl = BASE_URL + "/autodebit/redirect/success"
        ELSE:
            color = "red"
            statusFlag = (POST_DATA.cf_status == "INITIALIZED") ? 1 : 2
            appRedirectUrl = BASE_URL + "/autodebit/redirect/pending/" + statusFlag

        RENDER HTML:
          <h3 style="color:{color}">{POST_DATA.cf_message}</h3>
          <a href="{appRedirectUrl}">Back To App</a>

    ELSE:  // Desktop web browser
        IF userIsLoggedIn():
            SESSION.unset("CF_subscriptionId")
            REDIRECT to "/scheme/account/report/" + id_scheme_account
        ELSE:
            RENDER subscription result template page

    // ─── STEP 8: Log Result ───
    logToFile("cf_subscription/" + DATE + ".txt",
        DATETIME + " Result: " + JSON(result)
    )
```

---

## Phase 4 — Webhook: Monthly Auto-Payment

### Route (Public endpoint — Cashfree sends POST requests here)
```
POST /autodebit/webhook
```

### Webhook POST Data — `SUBSCRIPTION_NEW_PAYMENT`
```json
{
    "cf_event": "SUBSCRIPTION_NEW_PAYMENT",
    "cf_merchantId": "37030",
    "cf_subReferenceId": "44075",
    "cf_paymentId": "1",
    "cf_amount": "1000",
    "cf_eventTime": "2021-06-23 10:38:47",
    "cf_referenceId": "123",
    "signature": "UqqNmlTzh+gMOXWS2zm+3YCF713YKTZspZscJ4Fstow="
}
```

### Main Webhook Router

```
FUNCTION webhookHandler(POST_DATA):

    // Log ALL incoming webhook data
    logToFile("cf_hook/" + DATE + "_postData.txt",
        DATETIME + " POST: " + JSON(POST_DATA) + " GET: " + JSON(GET_DATA)
    )

    IF POST_DATA IS EMPTY:
        logToFile("cf_hook/" + DATE + "_empty_post.txt", DATETIME + " Empty POST")
        RETURN error("Empty POST data")

    // Route to appropriate handler
    SWITCH POST_DATA.cf_event:
        CASE "SUBSCRIPTION_NEW_PAYMENT":
            result = handleNewPayment(POST_DATA)
            logToFile("cf_hook/" + DATE + "_new_payment.txt", ...)

        CASE "SUBSCRIPTION_PAYMENT_DECLINED":
            result = handleDeclinedPayment(POST_DATA)
            logToFile("cf_hook/" + DATE + "_pay_declined.txt", ...)

        CASE "SUBSCRIPTION_STATUS_CHANGE":
            result = handleStatusChange(POST_DATA)
            logToFile("cf_hook/" + DATE + "_status_change.txt", ...)

    RETURN JSON(result)
```

### New Payment Handler (Success)

```
FUNCTION handleNewPayment(POST_DATA):

    // ─── STEP 1: Find Subscription + Related Data ───
    planDetail = DB.query("""
        SELECT 
            ad.sub_reference_id, ad.id_auto_debit_subscription,
            sa.id_scheme_account, sa.id_branch, sa.scheme_acc_number, sa.firstPayment_amt,
            s.id_metal, s.scheme_type, s.flexible_sch_type, s.id_scheme, 
            s.sync_scheme_code, s.code,
            cs.allow_referral, cs.receipt_no_set, cs.scheme_wise_receipt, 
            cs.schemeacc_no_set, cs.firstPayamt_payable, cs.firstPayamt_as_payamt,
            g.param_1, g.param_3, g.api_url, g.id_pg,
            c.email, c.firstname, c.lastname, c.mobile
        FROM auto_debit_subscription ad
            LEFT JOIN scheme_account sa ON sa.id_scheme_account = ad.id_scheme_account
            LEFT JOIN customer c ON c.id_customer = sa.id_customer
            LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
            LEFT JOIN gateway g ON g.id_branch = sa.id_branch 
                AND g.is_default = 1 AND g.pg_code = 4
            JOIN chit_settings cs
        WHERE ad.status = 1 
          AND ad.sub_reference_id = ?
    """, POST_DATA.cf_subReferenceId)

    IF planDetail IS EMPTY:
        RETURN {status: FALSE, message: "sub_reference_id not found"}

    // ─── STEP 2: Duplicate Payment Check (CRITICAL) ───
    isDuplicate = DB.queryScalar("""
        SELECT COUNT(*) FROM payment 
        WHERE payment_ref_number = ?
    """, POST_DATA.cf_paymentId)

    IF isDuplicate > 0:
        RETURN {status: FALSE, message: "Payment already exists: " + POST_DATA.cf_paymentId}

    // ─── STEP 3: Get Current Metal Rate ───
    metalRate = DB.queryScalar("""
        SELECT goldrate_22ct FROM metal_rates 
        WHERE id_branch = ? OR id_branch IS NULL
        ORDER BY id_metalrates DESC LIMIT 1
    """, planDetail.id_branch)

    // ─── STEP 4: Calculate Metal Weight (for weight-based schemes) ───
    IF planDetail.scheme_type == 2 
       OR (planDetail.scheme_type == 4 AND planDetail.flexible_sch_type IN (2, 3)):
        metalWeight = POST_DATA.cf_amount / metalRate
    ELSE:
        metalWeight = NULL

    // ─── STEP 5: Calculate Due Month/Year ───
    {month, year, dueType} = calculateDueMonthYear(planDetail.id_scheme_account)
    // (See full algorithm in dedicated section below)

    // ─── STEP 6: Determine Payment Status ───
    IF config.auto_pay_approval IN (1, 2):
        paymentStatus = 1   // SUCCESS — auto-approved
    ELSE:
        paymentStatus = 2   // AWAITING — needs admin approval

    // ─── STEP 7: Build Payment Record ───
    paymentData = {
        id_scheme_account:  planDetail.id_scheme_account,
        payment_amount:     POST_DATA.cf_amount,
        payment_type:       "Cash Free",
        payment_mode:       "Subscription",
        gst:                0,
        gst_type:           0,
        no_of_dues:         1,
        act_amount:         POST_DATA.cf_amount,
        actual_trans_amt:   POST_DATA.cf_amount,
        date_payment:       NOW(),
        metal_rate:         (metalRate > 0) ? metalRate : NULL,
        metal_weight:       metalWeight,
        payment_ref_number: POST_DATA.cf_paymentId,
        remark:             "Payment done through cashfree subscription",
        added_by:           4,          // 4 = Cashfree Subscription
        add_charges:        0,
        payment_status:     paymentStatus,
        id_payGateway:      planDetail.id_pg,
        id_branch:          planDetail.id_branch,
        due_type:           dueType,
        due_month:          month,
        due_year:           year
    }

    // ─── STEP 8: Insert Payment + Post-Processing ───
    insertId = DB.insert("payment", paymentData)

    IF insertId > 0:
        postPaymentProcessing(insertId, planDetail)
        RETURN {status: TRUE, message: "Payment inserted successfully"}
    ELSE:
        RETURN {status: FALSE, message: "Unable to create payment"}
```

---

## Phase 5 — Webhook: Payment Declined

### Webhook POST Data — `SUBSCRIPTION_PAYMENT_DECLINED`
```json
{
    "cf_event": "SUBSCRIPTION_PAYMENT_DECLINED",
    "cf_subReferenceId": "1",
    "cf_paymentId": "1",
    "cf_amount": "10",
    "cf_reasons": "Insufficient amount",
    "cf_eventTime": "2021-06-23 15:43:27",
    "signature": "E7/7Wc7FMfdqBPxmyVgdbsjbmmI7P94c0u1ZO5ilgyY="
}
```

### Algorithm

```
FUNCTION handleDeclinedPayment(POST_DATA):

    planDetail = getSubscriptionBySubRefId(POST_DATA.cf_subReferenceId)
    IF planDetail IS EMPTY:
        RETURN {status: FALSE, message: "sub_reference_id not found"}

    // Duplicate check
    IF paymentExistsByRef(POST_DATA.cf_paymentId):
        RETURN {status: FALSE, message: "Payment already recorded"}

    {month, year, dueType} = calculateDueMonthYear(planDetail.id_scheme_account)

    // Insert FAILED payment record
    paymentData = {
        id_scheme_account:  planDetail.id_scheme_account,
        payment_amount:     POST_DATA.cf_amount,
        payment_type:       "Cash Free",
        payment_mode:       "Subscription",
        gst:                0,
        gst_type:           0,
        no_of_dues:         1,
        act_amount:         POST_DATA.cf_amount,
        actual_trans_amt:   POST_DATA.cf_amount,
        date_payment:       NOW(),
        metal_rate:         NULL,          // No rate for failed payments
        metal_weight:       NULL,          // No weight for failed payments
        payment_ref_number: POST_DATA.cf_paymentId,
        remark:             POST_DATA.cf_reasons,  // e.g., "Insufficient amount"
        added_by:           4,
        add_charges:        0,
        payment_status:     3,             // 3 = FAILURE
        id_payGateway:      planDetail.id_pg,
        id_branch:          planDetail.id_branch,
        due_type:           dueType,
        due_month:          month,
        due_year:           year
    }

    insertId = DB.insert("payment", paymentData)

    IF insertId > 0:
        // Send failure notification SMS to customer
        sendPaymentSMS(insertId)  // SMS template should mention failure reason
        RETURN {status: TRUE, message: "Failed payment recorded"}
    ELSE:
        RETURN {status: FALSE, message: "Unable to create payment record"}
```

---

## Phase 6 — Webhook: Status Change

### Webhook POST Data — `SUBSCRIPTION_STATUS_CHANGE`
```json
{
    "cf_event": "SUBSCRIPTION_STATUS_CHANGE",
    "cf_merchantId": "37030",
    "cf_subReferenceId": "1",
    "cf_status": "COMPLETED",
    "cf_lastStatus": "ACTIVE",
    "cf_eventTime": "2021-06-09 17:14:53",
    "signature": "TzXitELQq77QIYSVciDRnJrgHx6xMHwnqee0i3SLc20="
}
```

### Algorithm

```
FUNCTION handleStatusChange(POST_DATA):

    planDetail = getSubscriptionBySubRefId(POST_DATA.cf_subReferenceId)
    IF planDetail IS EMPTY:
        RETURN {status: FALSE, message: "sub_reference_id not found"}

    newStatus = MAP_STATUS(POST_DATA.cf_status)

    // Idempotency: don't update if already at this status
    IF planDetail.auth_status == newStatus:
        RETURN {status: FALSE, message: "Status already updated"}

    // Update subscription table
    DB.update("auto_debit_subscription",
        WHERE sub_reference_id = POST_DATA.cf_subReferenceId,
        SET {
            auth_status: newStatus,
            status:      1,
            last_update: NOW()
        }
    )

    // Update scheme account
    DB.update("scheme_account",
        WHERE id_scheme_account = planDetail.id_scheme_account,
        SET {
            auto_debit_status: newStatus,
            date_upd:          NOW()
        }
    )

    RETURN {status: TRUE, message: "Status changed to " + POST_DATA.cf_status}
```

---

## Phase 7 — Unsubscribe (Cancel)

### Trigger
Customer clicks **"Unsubscribe"** button (visible when status = ACTIVE).

### Route
```
POST /subscription/cancel/{id_scheme_account}
```

### Algorithm

```
FUNCTION cancelSubscription(id_scheme_account):

    // ─── STEP 1: Get Active Subscription Data ───
    subData = DB.query("""
        SELECT 
            ad.sub_reference_id, ad.id_auto_debit_subscription,
            g.param_1 as client_secret, g.param_3 as client_id, g.api_url
        FROM auto_debit_subscription ad
            JOIN scheme_account sa ON sa.id_scheme_account = ad.id_scheme_account
            LEFT JOIN gateway g ON g.id_branch = sa.id_branch 
                AND g.is_default = 1 AND g.pg_code = 4
        WHERE ad.status = 1 AND ad.id_scheme_account = ?
    """, id_scheme_account)

    IF subData IS EMPTY:
        RETURN error("No active subscription found")

    // ─── STEP 2: Call Cashfree Cancel API ───
    response = HTTP_POST(
        url:     subData.api_url + "api/v2/subscriptions/" 
                 + subData.sub_reference_id + "/cancel",
        headers: {
            "X-Client-Id":     subData.client_id,
            "X-Client-Secret": subData.client_secret,
            "Content-Type":    "application/json"
        },
        body: JSON({ "subReferenceId": subData.sub_reference_id })
    )

    // ─── STEP 3: Update Local Records ───
    IF response.status == "OK":
        DB.update("auto_debit_subscription", subData.id_auto_debit_subscription, {
            message:     response.message,
            auth_status: 5,        // CANCELLED
            status:      0,        // Mark record as inactive
            last_update: NOW()
        })

        DB.update("scheme_account", id_scheme_account, {
            auto_debit_status: 5,    // CANCELLED
            date_upd:          NOW()
        })

        RETURN success("Unsubscribed from auto-debit successfully")
    ELSE:
        RETURN error("Cancellation failed: " + response.message)
```

---

## Phase 8 — Retry Failed Payment

### Trigger
Customer clicks **"Retry Payment"** button (shown on scheme list when a payment has failed).

### Route
```
POST /subscription/retry/{id_scheme_account}
```

### Algorithm

```
FUNCTION retryPayment(id_scheme_account):

    subData = getSubscriptionBySchemeAccount(id_scheme_account)
    IF subData IS EMPTY:
        RETURN error("No subscription found")

    // ─── STEP 1: Call Cashfree Retry API ───
    response = HTTP_POST(
        url:     subData.api_url + "api/v2/subscriptions/" 
                 + subData.sub_reference_id + "/charge-retry",
        headers: cashfreeHeaders(subData),
        body: JSON({ "subReferenceId": subData.sub_reference_id })
    )

    /* 
    Success Response Example:
    {
        "status": "OK",
        "subStatus": "ACTIVE",
        "payment": {
            "paymentId": 123456,
            "amount": 2,
            "status": "SUCCESS",
            "addedOn": "2021-02-26 13:35:12",
            "retryAttempts": 1
        }
    }
    */

    IF response.status == "OK":
        // ─── STEP 2: Update Subscription Status ───
        DB.update("auto_debit_subscription", subData.id, {
            auth_status: MAP_STATUS(response.subStatus),
            last_update: NOW()
        })
        DB.update("scheme_account", id_scheme_account, {
            auto_debit_status: MAP_STATUS(response.subStatus),
            date_upd:          NOW()
        })

        // ─── STEP 3: Insert Payment (if not duplicate) ───
        IF NOT paymentExistsByRef(response.payment.paymentId):
            // Use same logic as handleNewPayment — calculate metal rate,
            // due month/year, insert payment, run post-processing
            insertPaymentFromRetry(response.payment, subData)
            RETURN success("Retry successful, payment recorded")
        ELSE:
            RETURN success("Retry successful, payment already recorded")
    ELSE:
        RETURN error("Retry failed: " + response.message)
```

---

## Payment Blocking Logic

When auto-debit is active, prevent manual payments to avoid double-charging. **Apply this check in your existing manual payment flow.**

```
FUNCTION isManualPaymentAllowed(schemeAccount, chitSettings):

    // No subscription at all → allow
    IF schemeAccount.auto_debit_status == 0:
        RETURN TRUE

    // A subscription exists (status 1-6)
    SWITCH chitSettings.auto_debit_allow_app_pay:

        CASE 0:
            // STRICT: Block ALL manual payments when ANY subscription exists
            RETURN FALSE

        CASE 1:
            // PERMISSIVE: Always allow manual payments alongside auto-debit
            RETURN TRUE

        CASE 2:
            // BALANCED: Block only when subscription is actively charging
            IF schemeAccount.auto_debit_status == 3:  // ACTIVE
                RETURN FALSE
            ELSE:
                RETURN TRUE   // Allow when INITIALIZED/PENDING/ON_HOLD/CANCELLED/COMPLETED

    // Default: allow
    RETURN TRUE
```

### Where to Apply
Insert this check at the start of your manual payment processing function:

```
FUNCTION processManualPayment(id_scheme_account, paymentData):
    
    schemeAccount = getSchemeAccount(id_scheme_account)
    chitSettings  = getChitSettings()
    
    IF NOT isManualPaymentAllowed(schemeAccount, chitSettings):
        RETURN error("Manual payment blocked. Auto-debit is active for this account.")

    // ... continue with normal payment processing
```

---

## Due Month/Year Calculation Algorithm

Determines which month/year a payment should be attributed to, avoiding duplicates.

```
FUNCTION calculateDueMonthYear(id_scheme_account):

    currentMonth = MONTH(NOW())     // 1-12
    currentYear  = YEAR(NOW())      // e.g., 2025

    // ─── Get Last Paid Month ───
    lastPaid = DB.queryRow("""
        SELECT due_month, due_year 
        FROM payment 
        WHERE id_scheme_account = ? 
          AND (payment_status = 1 OR payment_status = 2)
          AND due_month IS NOT NULL
        ORDER BY due_year DESC, due_month DESC 
        LIMIT 1
    """, id_scheme_account)

    // ─── Get Current Month's Payment ───
    currentMonthPay = DB.queryRow("""
        SELECT due_type 
        FROM payment 
        WHERE id_scheme_account = ? 
          AND YEAR(date_payment) = ? AND MONTH(date_payment) = ?
          AND (payment_status = 1 OR payment_status = 2)
        ORDER BY id_payment DESC LIMIT 1
    """, id_scheme_account, currentYear, currentMonth)

    // ─── Calculate Month & Year ───
    IF lastPaid IS NOT EMPTY AND lastPaid.due_month IS NOT EMPTY:
        IF lastPaid.due_year == currentYear:
            IF lastPaid.due_month == currentMonth:
                // This month already has a payment → go to next month
                month = currentMonth + 1
                year  = currentYear
            ELSE:
                // There's a gap — fill the next month after last paid
                month = lastPaid.due_month + 1
                year  = currentYear
        ELSE:
            // Different year entirely
            month = lastPaid.due_month + 1
            year  = lastPaid.due_year

        // Handle December → January rollover
        IF month == 13:
            month = 1
            year  = year + 1
    ELSE:
        // No previous payments → use current month
        month = currentMonth
        year  = currentYear

    // ─── Determine Due Type ───
    IF currentMonthPay IS NOT EMPTY:
        IF currentMonthPay.due_type == 'ND' OR currentMonthPay.due_type == 'AD':
            dueType = 'AD'     // Advance Due (additional payment this month)
        ELSE:
            dueType = 'ND'     // Normal Due
    ELSE:
        dueType = 'ND'         // No payment this month → Normal Due

    RETURN { month: month, year: year, dueType: dueType }
```

### Due Type Reference

| Code | Name | When Used |
|---|---|---|
| `ND` | Normal Due | First/regular payment for a month |
| `AD` | Advance Due | Additional payment when current month is already paid |
| `PD` | Pending Due | Payment for a previously missed month |
| `AN` | Advance + Normal | Multiple months being paid at once |

---

## Metal Weight Conversion

For "Amount to Weight" type schemes, convert payment amount to gold weight.

```
FUNCTION calculateMetalWeight(amount, schemeType, flexibleSchType, branchId):

    // Scheme types that need weight conversion:
    //   Type 2 = "Amount to Weight"
    //   Type 4 + Flexible Type 2 or 3 = Flexible amount-to-weight variants
    
    needsConversion = (schemeType == 2) 
                   OR (schemeType == 4 AND flexibleSchType IN (2, 3))

    IF needsConversion:
        metalRate = getCurrentGoldRate(branchId)
        
        IF metalRate > 0:
            RETURN ROUND(amount / metalRate, 3)   // Weight in grams (3 decimal places)
        ELSE:
            RETURN NULL   // No rate available
    ELSE:
        RETURN NULL       // Not a weight-based scheme


FUNCTION getCurrentGoldRate(branchId):
    // Get latest 22ct gold rate, optionally filtered by branch
    RETURN DB.queryScalar("""
        SELECT goldrate_22ct FROM metal_rates 
        ORDER BY id_metalrates DESC LIMIT 1
    """)
```

---

## Post-Payment Processing Checklist

Run this after inserting **every** successful auto-debit payment.

```
FUNCTION postPaymentProcessing(paymentInsertId, planDetail):

    approvalType = CONFIG.auto_pay_approval

    IF approvalType IN (1, 2, 3):  // Auto-approved payments only

        // ── 1. REFERRAL CREDIT ──
        IF planDetail.allow_referral == 1:
            referralData = getReferralData(planDetail.id_scheme_account)
            IF referralData.referal_code IS NOT EMPTY:
                // Check if referral benefit should be credited
                // (depends on config: credit once vs. every installment)
                creditReferralBenefit(planDetail.id_scheme_account, referralData)

        // ── 2. RECEIPT NUMBER GENERATION ──
        IF planDetail.receipt_no_set == 1:
            IF planDetail.scheme_wise_receipt == 1:
                receiptNo = generateReceiptNo(planDetail.id_scheme)  // Per-scheme numbering
            ELSE:
                receiptNo = generateReceiptNo()  // Global numbering
            
            // Format: {COMPANY_CODE}{PADDED_NUMBER} → e.g., "MJ000042"
            DB.update("payment", paymentInsertId, { receipt_no: receiptNo })

        // ── 3. ACCOUNT NUMBER GENERATION (First payment only) ──
        IF planDetail.schemeacc_no_set == 0:
            IF planDetail.scheme_acc_number IS NULL OR planDetail.scheme_acc_number == "":
                newAccNo = generateAccountNumber(planDetail.id_scheme)
                // Format: 5-digit zero-padded → "00001", "00042"
                DB.update("scheme_account", planDetail.id_scheme_account, {
                    scheme_acc_number: newAccNo
                })

        // ── 4. FIRST PAYMENT AMOUNT RECORDING ──
        IF (planDetail.firstPayamt_payable == 1 OR planDetail.firstPayamt_as_payamt == 1)
           AND (planDetail.firstPayment_amt IS NULL OR planDetail.firstPayment_amt == ""):
            DB.update("scheme_account", planDetail.id_scheme_account, {
                firstPayment_amt: POST_DATA.cf_amount
            })

        // ── 5. ERP / INTERMEDIATE TABLE SYNC ──
        IF approvalType IN (2, 3):
            syncPaymentToIntermediateTable(paymentInsertId)
            // Inserts into sync tables for offline POS/ERP integration

    // ── 6. SMS NOTIFICATION (Always, regardless of approval) ──
    smsService = checkService(SERVICE_ID_PAYMENT_SMS)  // Service ID = 7
    IF smsService.sms == 1:
        smsData = getSMSData(SERVICE_ID_PAYMENT_SMS, paymentInsertId)
        sendSMS(smsData.mobile, smsData.message)

    // ── 7. EMAIL NOTIFICATION (Optional) ──
    IF smsService.email == 1 AND planDetail.email IS NOT EMPTY:
        sendPaymentEmail(paymentInsertId, planDetail.email)
```

---

## SMS / Notification Triggers

| Event | SMS | Email | Who |
|---|---|---|---|
| Monthly auto-debit succeeds | ✅ Send | Optional | Customer |
| Monthly auto-debit fails | ✅ Send (include reason) | Optional | Customer |
| Subscription created | ❌ (shown in UI) | Optional | Customer |
| Subscription authorized/activated | ❌ (shown in UI) | Optional | Customer |
| Subscription cancelled | ❌ (shown in UI) | Optional | Customer |
| Status changed to COMPLETED | ❌ (shown in UI) | Optional | Customer |

---

## Logging Strategy

### Log Directory Structure
```
log/
├── cf_subscription/              # Return URL callback logs
│   ├── 2025-06-19.txt
│   └── 2025-06-20.txt
└── cf_hook/                      # Webhook logs
    ├── 2025-06-19_postData.txt         # ALL incoming webhooks (raw)
    ├── 2025-06-19_new_payment.txt      # Successful payment events
    ├── 2025-06-19_pay_declined.txt     # Failed payment events
    ├── 2025-06-19_status_change.txt    # Status change events
    └── 2025-06-19_empty_post.txt       # Empty/invalid requests
```

### Log Entry Format
```
{dd-mm-YYYY HH:mm:ss}
 POST : {json_encoded_post_data}
 Result : {json_encoded_processing_result}
```

### Best Practice
- Log **BEFORE** processing (raw input) and **AFTER** processing (result)
- Use daily file rotation (date in filename)
- Create log directories automatically if they don't exist (`mkdir -p` equivalent)
- Never log sensitive gateway credentials

---

## Security Considerations

### 1. Webhook Signature Verification (MUST IMPLEMENT)
```
// Cashfree sends a 'signature' field with every webhook/callback
// Verify using HMAC-SHA256 with your Client Secret

FUNCTION verifySignature(postData, clientSecret):
    // 1. Sort POST keys alphabetically
    // 2. Concatenate key-value pairs (excluding 'signature' key)
    // 3. Generate HMAC-SHA256 hash using Client Secret
    // 4. Compare with received signature
    
    sortedKeys = SORT(postData.keys().filter(k => k != "signature"))
    dataString = JOIN(sortedKeys.map(k => k + postData[k]))
    expectedSig = HMAC_SHA256(dataString, clientSecret)
    
    RETURN expectedSig == postData.signature
```

### 2. Idempotency
- **Always** check `isPaymentAlreadyExist(paymentRefNumber)` before inserting
- **Always** check `auth_status` before updating subscription status
- Webhooks CAN be sent multiple times — your code must handle this gracefully

### 3. Input Validation
- Webhook endpoints are **public** — validate all input fields
- Check that `cf_subReferenceId` exists in your database
- Validate `cf_amount` is a positive number

### 4. HTTPS
- All callback URLs (returnUrl, webhook URL) **MUST** be HTTPS in production
- Cashfree will reject HTTP URLs in production mode

### 5. Credential Storage
- Store Client ID and Client Secret in the database (gateway table), not in code
- Use different credentials for test vs. production environments
- Support per-branch gateway credentials for multi-branch setups

---

## Mobile App Integration

### Return URL Handling for In-App Browsers

When the mobile app opens the Cashfree authorization page in an in-app browser (WebView), the return URL needs special handling:

```
// After authorization, the return URL callback detects mobile browser
IF isMobileBrowser():

    // Render a simple HTML page (not your app's full UI)
    IF cf_status == "ACTIVE":
        RENDER:
          <h3 style="color:green; text-align:center">
            Subscription Activated Successfully ✅
          </h3>
          <a href="{BASE_URL}/autodebit/redirect/success" 
             style="display:block; text-align:center">
            Back To App
          </a>

    ELSE IF cf_status IN ("INITIALIZED", "BANK_APPROVAL_PENDING"):
        statusFlag = (cf_status == "INITIALIZED") ? 1 : 2
        RENDER:
          <h3 style="color:red; text-align:center">
            {cf_message}
          </h3>
          <a href="{BASE_URL}/autodebit/redirect/pending/{statusFlag}"
             style="display:block; text-align:center">
            Back To App
          </a>

// The /autodebit/redirect/{status} endpoint just shows:
//   "Please wait redirecting..."
// The mobile app intercepts this URL pattern to:
//   1. Close the WebView
//   2. Refresh the scheme account page
//   3. Show the updated subscription status
```

### Mobile API — Subscribe/Unsubscribe

The mobile app should have separate API endpoints that mirror the web flow:

```
// Mobile Subscribe
POST /api/subscription/create
Body: { id_scheme_account: 123, platform: "mobile" }
// Set added_by = 1 (Mobile) instead of 0 (Web)

// Mobile Unsubscribe  
POST /api/subscription/cancel
Body: { id_scheme_account: 123 }

// Mobile Retry
POST /api/subscription/retry
Body: { id_scheme_account: 123 }
```

---

## Status Code Reference

### Subscription Status (`auth_status` / `auto_debit_status`)

| Code | Cashfree String | Meaning | Customer Can See | Action Available |
|---|---|---|---|---|
| `0` | *(none)* | Not subscribed | — | Subscribe |
| `1` | `INITIALIZED` | Created, awaiting bank auth | "Authorize" button | Click auth_link |
| `2` | `BANK_APPROVAL_PENDING` | Bank is reviewing | "Pending" message | Wait |
| `3` | `ACTIVE` | Live — monthly debits happening | "Active ✅" + expiry | Unsubscribe |
| `4` | `ON_HOLD` | Temporarily paused | "On Hold ⚠️" | Contact support |
| `5` | `CANCELLED` | Cancelled by customer/admin | — | Re-subscribe |
| `6` | `COMPLETED` | All installments done | "Completed ✔️" | — |

### Status Mapping Function

```
FUNCTION MAP_STATUS(cashfreeStatusString):
    mapping = {
        "INITIALIZED":           1,
        "BANK_APPROVAL_PENDING": 2,
        "PENDING":               2,     // Alternative for BANK_APPROVAL_PENDING
        "ACTIVE":                3,
        "ON_HOLD":               4,
        "CANCELLED":             5,
        "COMPLETED":             6
    }
    RETURN mapping[cashfreeStatusString] OR 1  // Default to INITIALIZED
```

### Payment Status

| Code | Status | Used When |
|---|---|---|
| `1` | Success | Auto-approved payment |
| `2` | Awaiting | Needs admin approval |
| `3` | Failure | Payment declined by bank |
| `4` | Cancelled | *(not used in auto-debit)* |
| `7` | Pending | *(not used in auto-debit)* |

### Payment `added_by` Values

| Code | Source |
|---|---|
| `0` | Web App (manual payment) |
| `1` | Admin Panel |
| `2` | Mobile App (manual payment) |
| `4` | Cashfree Subscription (auto-debit) |

---

## API Reference — Cashfree Endpoints

### Base URLs

| Environment | URL |
|---|---|
| Sandbox/Test | `https://test.cashfree.com/` |
| Production | `https://api.cashfree.com/` |

### Authentication Headers (Required for ALL API calls)

```
X-Client-Id: {your_client_id}
X-Client-Secret: {your_client_secret}
Content-Type: application/json
```

### API Endpoints

#### 1. Create Subscription
```
POST {base_url}api/v2/subscriptions/

Request Body:
{
    "subscriptionId":    "unique_id_you_generate",
    "planId":            "GOLD_1000_MONTHLY",
    "customerName":      "John Doe",
    "customerEmail":     "john@example.com",
    "customerPhone":     "9876543210",
    "firstChargeDelay":  1,
    "authAmount":        1,
    "expiresOn":         "2025-12-31 23:59:59",
    "returnUrl":         "https://yourdomain.com/autodebit/callback/123"
}

Success Response:
{
    "status": "OK",
    "subReferenceId": "43225",
    "subStatus": "INITIALIZED",
    "authLink": "https://cashfree.com/auth/...",
    "message": "Subscription created"
}
```

#### 2. Cancel Subscription
```
POST {base_url}api/v2/subscriptions/{subReferenceId}/cancel

Request Body:
{
    "subReferenceId": "43225"
}

Success Response:
{
    "status": "OK",
    "message": "Subscription cancelled"
}
```

#### 3. Retry Failed Charge
```
POST {base_url}api/v2/subscriptions/{subReferenceId}/charge-retry

Request Body:
{
    "subReferenceId": "43225"
}

Success Response:
{
    "status": "OK",
    "subStatus": "ACTIVE",
    "payment": {
        "paymentId": 123456,
        "amount": 1000,
        "status": "SUCCESS",
        "addedOn": "2025-06-19 13:35:12",
        "retryAttempts": 1
    }
}
```

### Webhook Events (Cashfree → Your Server)

| Event | When | Key Fields |
|---|---|---|
| `SUBSCRIPTION_NEW_PAYMENT` | Monthly charge succeeds | `cf_subReferenceId`, `cf_paymentId`, `cf_amount` |
| `SUBSCRIPTION_PAYMENT_DECLINED` | Monthly charge fails | `cf_subReferenceId`, `cf_paymentId`, `cf_amount`, `cf_reasons` |
| `SUBSCRIPTION_STATUS_CHANGE` | Lifecycle change | `cf_subReferenceId`, `cf_status`, `cf_lastStatus` |

### HTTP Client Wrapper

```
FUNCTION cashfreeApiCall(endpoint, postData, gatewayCredentials):

    url = gatewayCredentials.api_url + "api/v2/subscriptions/" + endpoint

    response = HTTP_POST(
        url:     url,
        headers: {
            "Cache-Control":   "no-cache",
            "Content-Type":    "application/json",
            "X-Client-Id":     gatewayCredentials.client_id,
            "X-Client-Secret": gatewayCredentials.client_secret
        },
        body:    JSON.encode(postData),
        timeout: 30 seconds,
        maxRedirects: 10
    )

    IF httpError:
        RETURN { status: FALSE, result: httpErrorMessage }
    ELSE:
        RETURN { status: TRUE, result: JSON.decode(response) }
```

> **⚠️ NOTE**: Cashfree's v2 Subscriptions API may be superseded by newer versions. Always check [Cashfree's official documentation](https://docs.cashfree.com/) for the latest API version before starting implementation.

---

## Implementation Checklist

### Database Setup
- [ ] Create `auto_debit_subscription` table
- [ ] Add `auto_debit_status` column to `scheme_account` table
- [ ] Add `auto_debit_plan_type` and `sync_scheme_code` columns to `scheme` table
- [ ] Add `auto_debit` and `auto_debit_allow_app_pay` to `chit_settings` table
- [ ] Insert Cashfree gateway record (pg_code=4) in `gateway` table
- [ ] Ensure `payment` table has: `due_type`, `due_month`, `due_year`, `added_by` columns

### Cashfree Dashboard
- [ ] Create Cashfree merchant account
- [ ] Get Client ID and Client Secret
- [ ] Create Plans for each eligible scheme (amount, interval=month)
- [ ] Map Plan IDs to `sync_scheme_code` in scheme table
- [ ] Configure webhook URL pointing to your server
- [ ] Test in sandbox mode first

### Backend — Routes/Controllers
- [ ] `POST /subscription/create/{id}` — Create new subscription
- [ ] `POST /autodebit/callback/{id}` — Handle return URL after authorization
- [ ] `POST /autodebit/webhook` — Handle all 3 webhook events
- [ ] `POST /subscription/cancel/{id}` — Cancel/unsubscribe
- [ ] `POST /subscription/retry/{id}` — Retry failed payment
- [ ] `GET  /autodebit/redirect/{status}` — Mobile app redirect handler

### Backend — Core Functions
- [ ] `MAP_STATUS()` — Convert Cashfree status string to integer code
- [ ] `calculateDueMonthYear()` — Due month/year + due type logic
- [ ] `calculateMetalWeight()` — Amount-to-weight conversion for eligible schemes
- [ ] `postPaymentProcessing()` — Receipt, referral, account number, sync, SMS
- [ ] `isPaymentAlreadyExist()` — Duplicate payment prevention
- [ ] `isManualPaymentAllowed()` — Payment blocking logic
- [ ] `cashfreeApiCall()` — HTTP client wrapper with error handling
- [ ] `verifySignature()` — Webhook signature verification (HMAC-SHA256)
- [ ] `isMobileBrowser()` — User-agent detection for mobile redirect
- [ ] `generateUniqueId()` — Unique subscription ID generator

### Frontend — UI Elements
- [ ] **Subscribe** button (when status = 0 or 5)
- [ ] **Authorize** button/link (when status = 1 and auth_link exists)
- [ ] **Active** status display with expiry date (when status = 3)
- [ ] **Unsubscribe** button (when status = 3)
- [ ] **Pending/On Hold/Completed** status messages (status 2, 4, 6)
- [ ] **Retry Payment** button (for failed auto-debit payments in payment history)
- [ ] Confirmation modals for Subscribe / Unsubscribe / Retry actions
- [ ] Auto-debit status column in scheme list/table view

### Logging & Monitoring
- [ ] Webhook log files with daily rotation
- [ ] Return URL callback logs
- [ ] API call request/response logs
- [ ] Auto-create log directories if missing

### Security
- [ ] Implement webhook signature verification
- [ ] Use HTTPS for all callback/webhook URLs
- [ ] Validate all webhook input data
- [ ] Add CSRF protection on user-facing subscribe/unsubscribe actions
- [ ] Separate test vs. production gateway credentials

### Testing
- [ ] Test subscription creation (sandbox)
- [ ] Test authorization flow end-to-end
- [ ] Test webhook: new payment
- [ ] Test webhook: payment declined
- [ ] Test webhook: status change (ACTIVE → COMPLETED)
- [ ] Test unsubscribe flow
- [ ] Test retry failed payment
- [ ] Test duplicate webhook handling (idempotency)
- [ ] Test payment blocking with different `auto_debit_allow_app_pay` settings
- [ ] Test mobile in-app browser authorization flow
- [ ] Test with multiple branches (different gateway credentials)
- [ ] Switch to production credentials and verify

---

*End of Document*
