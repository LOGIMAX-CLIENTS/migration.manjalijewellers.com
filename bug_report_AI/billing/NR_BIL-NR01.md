## BIL-NR01 — Multi-Provider POS Integration (PhonePe DQR / IEDC + Architecture Refactor)

| Field         | Value                                     |
| ------------- | ----------------------------------------- |
| Type          | NR (New Requirement)                      |
| Priority      | P1 — High                                 |
| Track         | A (System/Architecture)                   |
| Category      | Integration                               |
| Sprint        | Sprint 2                                  |
| Pattern Match | None — Novel                              |
| Module Brain  | ✅ Ready (Billing)                        |
| Reporter      | Internal — Dev Team                       |
| Source        | Client requirement (PhonePe integration)  |
| GitHub        | #1157                                     |

### Description

Implement a future-proof multi-provider POS payment integration system for ARIA ERP Billing module. Currently only Pine Labs (Plutus) is supported with hardcoded controller functions. Need to:
1. Refactor to a generic provider-agnostic architecture
2. Add PhonePe DQR (Dynamic QR) as first new provider
3. Add PhonePe IEDC (Integrated EDC) as second provider
4. Build admin master pages for providers, devices, and transaction logs
5. Address 7 identified gaps (duplicate protection, amount validation, auto-polling, etc.)

### Current State
- Pine Labs Plutus code exists (copied from Aria staging)
- UAT tested successfully in Postman and from app
- PhonePe official API docs received from PhonePe team
- PhonePe UAT credentials needed (reply to Nayan's email)

### Identified Gaps
1. Payment ↔ Bill not linked (no FK)
2. No duplicate payment protection
3. Amount mismatch risk (POS vs bill)
4. Network failure handling (no callback for Pine Labs)
5. No auto-status polling
6. No refund flow
7. No settlement reconciliation

### Key Files
- **Controller**: `admin/application/controllers/admin_ret_billing.php` (L12231+)
- **Model**: `admin/application/models/ret_billing_model.php` (L11734+)
- **JS**: `admin/assets/js/ret_billing.js` (L44880+)
- **View**: `admin/application/views/billing/form.php` (L2493+)
- **Config**: `admin/application/config/config.php` (L170-174)

### Implementation Plan
- Phase 0: Fix gaps (0.5 day)
- Phase 1: Architecture refactor — DB tables + generic controller (2 days)
- Phase 2: Provider Master + Device Master UI (2 days)
- Phase 3: PhonePe DQR integration (3 days)
- Phase 4: PhonePe IEDC integration (2 days)
- Phase 5: Transaction Log viewer (1 day)
- Phase 6: Refund + future providers (ongoing)
- **Total: ~11 working days**
