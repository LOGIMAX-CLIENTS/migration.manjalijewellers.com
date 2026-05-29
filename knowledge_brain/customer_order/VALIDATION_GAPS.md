# Validation Gaps: Customer Order

> Client-side vs Server-side validation analysis. Gaps = attack vectors and data integrity risks.

---

## Customer Order Form (`form.php` + `ret_reports.js`)

### Fields with Client-Side ONLY Validation (No Server Check)

| Field | DOM ID | Client Validation | Server Validation | Gap Risk |
|---|---|---|---|---|
| Customer Selection | `#cus_id` | Select2 required | ❌ None — `order_to` can be NULL | HIGH — orders without customer |
| Product Selection | `#id_product` | JS checks non-empty | ❌ None — inserted as-is | HIGH — invalid product IDs |
| Weight | `input[name*=weight]` | HTML `type=number` | ❌ None — no range check | MEDIUM — zero/negative weights |
| Pieces | `input[name*=totalitems]` | HTML `type=number` | ❌ None — no range check | MEDIUM — zero/negative pieces |
| Order Rate | `input[name*=order_rate]` | JS calculation | ❌ None — passed through | HIGH — manipulated rates |
| Making Charge | `input[name*=mc]` | JS calculation | ❌ None — passed through | HIGH — manipulated charges |
| Wastage % | `input[name*=wast_percent]` | JS calculation | ❌ None — passed through | MEDIUM — inflated wastage |
| Stone Amount | `input[name*=stn_amt]` | JS total from modal | ❌ None — passed through | HIGH — manipulated stone costs |
| Charge Value | `input[name*=value_charge]` | JS total from modal | ❌ None — passed through | HIGH — manipulated charges |
| Tax (SGST/CGST/IGST) | Hidden fields | JS calculation | ❌ None — passed through | **CRITICAL** — tax evasion |
| Order Date | `#order_date` | Datepicker | Day-closing check only | MEDIUM — backdated orders |

### Fields with Server-Side Validation

| Field | Validation Type | Location |
|---|---|---|
| `order_from` (Branch) | Existence check | `generateOrderNo()` L144 |
| `order_type` | Used in SQL, no range check | `order('save')` L~130 |
| Financial Year | DB lookup, fails if no active year | `get_FinancialYear()` |
| Day Closing Date | Date comparison | `getBranchDayClosingData()` L1456 |

### Fields with NO Validation (Client or Server)

| Field | Name Attribute | Impact |
|---|---|---|
| Rate Type | `order[rate_type]` | Controls fixed vs delivery pricing |
| Balance Type | `order[balance_type]` | Controls metal vs cash balance |
| Work At | `order[work_at]` | Controls in-house vs outsource |
| Is EDA | `order[is_eda]` | Always hardcoded to 1, but can be tampered |
| Is EDA Tax Calc | `order[is_eda_tax_calc]` | Tax calculation toggle |
| Employee | `order[order_taken_by]` | Can assign to any employee ID |
| Description | `description` | No length limit, no XSS sanitization |
| Image Data | `order_img` (base64) | No file type validation, no size limit |

---

## Repair Order Form

### Additional Gaps (beyond Customer Order)

| Field | Validation | Gap |
|---|---|---|
| Repair Type (`id_repair_master`) | JS dropdown | ❌ No server check — invalid repair type ID accepted |
| Tag ID (`tag_id`) | JS scan lookup | ❌ No server ownership check — can link any tag |
| Pure Weight (`purewt`) | JS input | ❌ No server validation — can be manipulated |
| Customer Due Days | Datepicker | ❌ Only date format conversion, no business rule check |

---

## Cancel / OTP Flow

### OTP Validation Gaps

| Check Point | Validation | Gap |
|---|---|---|
| OTP Generation | 6-digit random | ✅ Stored in `otp` table |
| OTP Verification | Match check against DB | ⚠️ No expiry time enforcement |
| OTP Resend | Button timer (client) | ❌ No server-side rate limiting |
| Cancel Remarks | Required (client) | ❌ No server required check — can be empty |

---

## Summary: Validation Coverage Matrix

| Category | Total Fields | Client Only | Server Only | Both | None |
|---|---|---|---|---|---|
| Order Header | 12 | 2 | 2 | 1 | 7 |
| Order Items | 11 | 8 | 0 | 0 | 3 |
| Financial/Tax | 4 | 4 | 0 | 0 | 0 |
| Repair-Specific | 4 | 2 | 0 | 0 | 2 |
| OTP/Cancel | 4 | 2 | 1 | 1 | 0 |
| **Total** | **35** | **18 (51%)** | **3 (9%)** | **2 (6%)** | **12 (34%)** |

> **⚠️ 34% of fields have NO validation at all. 51% have client-side only validation (bypassable). Only 6% have proper dual validation.**
