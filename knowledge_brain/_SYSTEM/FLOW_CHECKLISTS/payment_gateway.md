# Flow Checklist: Payment Gateway Config

> **Last Updated:** 2026-03-27
> **Controller:** `admin_settings.php` → `gateway_settings()`
> **Tables:** `payment_gateway`

---

## GATEWAY SAVE / UPDATE

| # | Item | Status |
|---|---|---|
| 1 | Store API keys and secrets | ⬜ |
| 2 | Set demo/production mode | ⬜ |
| 3 | Set as default | Single default per type | ⬜ |

## Default Toggle Bug

| Scenario | Expected | Actual |
|---|---|---|
| Set Cashfree demo as default | Cashfree pro → non-default | ✅ Reciprocal toggle |
| Set HDFC demo as default | HDFC pro → non-default | ❌ **No toggle** — MST-BUG-026 |

## Known Bugs

| Bug ID | Description | Severity |
|---|---|---|
| MST-BUG-026 | `is_default` not toggled for non-Cashfree gateways — dual active gateways | 🟡 MED |
| GW-001 | API credentials stored in source code (hardcoded) — MST-BUG-040 | 🔴 HIGH |
