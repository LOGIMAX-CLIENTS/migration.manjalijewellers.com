# AI Postmortem Log — Failures and Lessons Learned

> **Purpose**: Record every time AI gave a wrong diagnosis or fix. Checked BEFORE diagnosing new bugs.
> **Used by**: `/fix-single-bug` Step 1 — check BEFORE starting diagnosis
> **Last Updated**: 2026-03-16

---

## How to Use This File

1. Before diagnosing a new bug, scan this log for similar symptoms
2. If a match is found → avoid the "Wrong Approach" and go straight to "What Was Actually Wrong"
3. After EVERY bug where AI was wrong → add a new entry here

---

## POST-001: Missing Chit Payment — Webhook Race Condition

| Field | Value |
|---|---|
| **Date** | 2026-03 (approximate) |
| **Module** | Billing (Chit/Scheme Payments) |
| **Symptom** | 3 payments on gateway, only 2 in system. Webhook triggered twice |
| **What AI Suggested (WRONG)** | Update the database directly. Fix the display query |
| **What Was Actually Wrong** | Webhook handler race condition — 2 webhooks arrived simultaneously, one was lost due to concurrency/timeout |
| **Why AI Was Wrong** | Treated the SYMPTOM (missing data) instead of the ROOT CAUSE (pipeline failure). Thought at DB level instead of system level |
| **Correct Approach** | Trace: Gateway → Webhook → PHP Handler → DB. Find where the webhook was lost. Check for concurrent request handling |
| **Lesson** | **"Payment missing" → NEVER touch DB. ALWAYS trace the webhook pipeline first** |
| **Added to Playbook** | ✅ RULE-DX-001 |
| **Added to Danger Zone** | ✅ DZ-001, DZ-025 |

---

## POST-002: Sales Report NaN — Column Fix Without Checking Totals

| Field | Value |
|---|---|
| **Date** | 2026-03-16 |
| **Module** | Reports |
| **Symptom** | Report column showed NaN instead of 0.00 |
| **What AI Suggested** | Fixed the one column value |
| **What Was Missing** | Did not check: subtotal, grand total, footer callback, print view, export — all of which use the same column value |
| **Why AI Was Wrong** | Fixed the point of failure instead of checking the ripple effect. A junior developer would know NaN cascades to all totals |
| **Correct Approach** | Fix the column → then verify ALL downstream: subtotal, grand total, footer, print, export |
| **Lesson** | **"NaN in one column" → ALWAYS check all totals that sum/use that column** |
| **Added to Playbook** | ✅ RULE-DX-004 |
| **Added to Danger Zone** | ✅ DZ-006 |

---

## Template for New Entries

```markdown
## POST-{NNN}: {Short Title}

| Field | Value |
|---|---|
| **Date** | {date} |
| **Module** | {module} |
| **Symptom** | {what was reported} |
| **What AI Suggested (WRONG)** | {what AI said to do} |
| **What Was Actually Wrong** | {real root cause} |
| **Why AI Was Wrong** | {what thinking pattern was wrong} |
| **Correct Approach** | {what should have been done} |
| **Lesson** | {one-sentence rule — bold} |
| **Added to Playbook** | ✅ RULE-DX-{NNN} / ❌ Not yet |
| **Added to Danger Zone** | ✅ DZ-{NNN} / ❌ Not yet |
```

---

## Statistics

| Metric | Value |
|---|---|
| Total postmortems | 2 |
| Most common AI mistake | Fixing symptom instead of root cause |
| Most dangerous area | Payment / webhook integration |
| Target: reduce repeat failures by | 90% (once logged, should never happen again) |
