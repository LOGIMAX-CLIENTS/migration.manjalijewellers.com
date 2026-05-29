# Billing Module — Invariant Matrix

> **Round 1** | Config-driven behavior grids for the Billing module

---

## Dimension 1: `bill_type` (Primary Variant)

| bill_type | Name                            | Search Sections                 | Payment Sections                | Writes To                                                          | Special Logic                           |
| --------- | ------------------------------- | ------------------------------- | ------------------------------- | ------------------------------------------------------------------ | --------------------------------------- |
| 1         | Normal Sale                     | esti + tag + order              | Full (cash/card/chq/NB/chit/GV) | ret_billing + ret_bill_details + ret_billing_payment + tag updates | Standard sale flow                      |
| 2         | Exchange Sale                   | esti + tag + order              | Full                            | + purchase (old metal) details                                     | Old metal exchange with net calculation |
| 3         | Combined (Sale+Return+Purchase) | All (esti + tag + order + bill) | Full                            | All tables                                                         | Most complex — handles all item types   |
| 4         | Purchase Only                   | esti only                       | purchase_details only           | Purchase items only                                                | No sale items                           |
| 5         | Order Advance                   | esti + order                    | order_adv_details               | Advance records                                                    | No items — just advance payment         |
| 6         | Other                           | None                            | None                            | —                                                                  | Hidden sections — purpose unclear       |
| 7         | Sales Return                    | bill search                     | return_details + total_summary  | Return adjustments                                                 | Searches by bill number for returns     |
| 9         | EDA variant                     | esti + tag + order              | Cash only + eda_tax_calc        | + EDA tax flag                                                     | Same as 1 but with EDA tax calc         |
| 10        | (variant)                       | —                               | —                               | —                                                                  | Needs Round 2 investigation             |
| 15        | Suspense Stock                  | esti + tag + order              | Full + eda_tax_calc             | + `issuspensestock = 1` on tag log                                 | Tag log marked as suspense              |

### Controlling Fields

- **DB**: `ret_billing.bill_type` (INT)
- **PHP**: `$addData['bill_type']` from POST
- **JS**: `$(".bill_type_sales:checked").val()`

---

## Dimension 2: `is_eda` (Normal vs EDA/No2)

| is_eda | Name        | Payment Options                         | Tax Calc              | UI Indicator                    | Toggle     |
| ------ | ----------- | --------------------------------------- | --------------------- | ------------------------------- | ---------- |
| 1      | Normal Sale | Full (Cash, Card, Cheque, NB, Chit, GV) | Standard              | No background color             | Default    |
| 2      | EDA (No2)   | Cash ONLY                               | Optional eda_tax_calc | Red background on customer name | Ctrl+Enter |

### Controlling Fields

- **DB**: `ret_billing.is_eda` (INT)
- **PHP**: `$addData['is_eda']`
- **JS**: `$('#is_eda').val()`

### Behavior Matrix: `bill_type` × `is_eda`

|                | is_eda = 1 (Normal)                       | is_eda = 2 (EDA)                        |
| -------------- | ----------------------------------------- | --------------------------------------- |
| bill_type = 1  | Standard sale, all payment modes          | Cash only, red UI, eda_tax_calc visible |
| bill_type = 2  | Exchange with all payments                | Exchange with cash only                 |
| bill_type = 3  | Combined with all payments                | Combined with cash only                 |
| bill_type = 5  | Order advance, all payments               | ⚠️ Page reloads if toggled (L444-448)   |
| bill_type = 9  | EDA sale with all payments + eda_tax_calc | EDA sale with cash + eda_tax_calc       |
| bill_type = 15 | Suspense with all payments + eda_tax_calc | Suspense with cash + eda_tax_calc       |

---

## Dimension 3: `billing_for` (Individual vs Company)

| billing_for | Name       | Customer Type      | Additional Fields                            |
| ----------- | ---------- | ------------------ | -------------------------------------------- |
| 1           | Individual | Regular customer   | PAN, Aadhaar, DL, Passport                   |
| 2           | Company    | Corporate customer | Company user search, company purchase amount |

### Constraint

- **EDA (is_eda=2) + Company (billing_for=2)** → ⚠️ **Not allowed** — shows "B2B Bills Not allowed" error (JS L461-468)

---

## Dimension 4: `is_credit` (Cash vs Credit)

| is_credit | Name        | Payment Behavior      | OTP Required                   | credit_status |
| --------- | ----------- | --------------------- | ------------------------------ | ------------- |
| 0         | Cash Sale   | Full payment required | No                             | 1 (paid)      |
| 1         | Credit Sale | Partial/no payment    | If `credit_sales_otp_req == 1` | 2 (pending)   |

---

## Dimension 5: `allow_bill_type` (Profile Setting)

| allow_bill_type | Name               | Behavior                  |
| --------------- | ------------------ | ------------------------- |
| 1               | Normal Only        | Fixed is_eda=1, no toggle |
| 2               | EDA Only           | Fixed is_eda=2, no toggle |
| 3               | All (Normal + EDA) | Ctrl+Enter toggle enabled |

---

## Dimension 6: `bill_disc_approval_type` (Discount Approval)

| bill_disc_approval_type | Name       | Flow                                        |
| ----------------------- | ---------- | ------------------------------------------- |
| 1                       | OTP        | Send OTP → verify → apply discount          |
| 2                       | Mobile App | Push notification → admin approves from app |

---

## Dimension 7: `credit_sales_approval_type`

| credit_sales_approval_type | Name       | Flow                               |
| -------------------------- | ---------- | ---------------------------------- |
| 1                          | OTP        | Send credit OTP → verify           |
| 2                          | Mobile App | Push notification → admin approves |
