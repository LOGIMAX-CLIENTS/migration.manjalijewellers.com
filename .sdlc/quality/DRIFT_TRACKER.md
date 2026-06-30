# SDLC — Pipeline Drift Tracker

> **Version**: 3.0  
> **Root Cause Analysis**: [pipeline_drift_analysis.md](drift-audits/pipeline_drift_analysis.md)  
> **Location**: `.sdlc/quality/`  
> **Purpose**: Track every drift audit per conversation. One row per incident. Detailed analysis in `drift-audits/`.

---

## Scoring Scale

| Grade | Criteria |
|---|---|
| 🟢 A | 0 violations |
| 🟡 B | 1–3 minor violations, no process bypass |
| 🟠 C | 4–7 violations or ≤2 process bypasses |
| 🔴 D | 8+ violations or ≥3 process bypasses |
| ⛔ F | Critical: source code modified outside CODING phase, or pipeline never entered |

---

## Audit Log

| # | Date | Version | Conversation | Key Finding | Violations | Grade | Audit Doc |
|---|---|---|---|---|---|---|---|
| DA-001 | Jun 17 | v2.6.0 | a60a8308 | Step Zero skipped, 8/11 workflow steps ignored | 13 (5🔴 + 8🟡) | 🔴 D | [DA-001](drift-audits/DA-001_v1.0.md) |
| DA-002 | Jun 17 | v3.0.0 | (this conv) | Pipeline never entered — agent followed GEMINI.md instead | Critical | ⛔ F | [DA-002](drift-audits/DA-002_v3.0.0.md) |
| DA-003 | Jun 17 | v3.0.1 | (this conv) | Pipeline entered ✅, but 6 parallel investigations during discover | 6 | 🟠 C | [DA-003](drift-audits/DA-003_v3.0.1.md) |
| DA-004 | Jun 17 | v3.0.2 | c48b3630 | **Zero parallel drift** ✅, but discovery.md race condition | 1 🟢 | 🟡 **B** | [DA-004](drift-audits/DA-004_v3.0.2.md) |
| DA-005 | Jun 17 | v3.0.2 | 49dc584c | Agent killed discover (RAG slow) → bypassed pipeline entirely | 3🔴 + 2🟡 | 🔴 D | [DA-005](drift-audits/DA-005_v3.0.2.md) |
| DA-006 | Jun 17 | v3.0.3 | a60a8308 | Pipeline followed ✅, but banner redundant + DB leak to Step 3 | 2🟢 + 2🟡 | 🟡 **B** | [DA-006](drift-audits/DA-006_v3.0.3.md) |
| DA-007 | Jun 17 | v3.0.4 | c9f8f9fe | **Full pipeline loop ✅** — 4 steps, zero bypasses, DB in correct step | 1🟢 + 2🟡 | 🟢 **A-** | [DA-007](drift-audits/DA-007_v3.0.4.md) |
| DA-008 | Jun 17 | v3.0.4 | b58d063c | Pipeline followed ✅, minor thinking mentions old Step Zero habits | 1🟢 + 2🟡 | 🟢 **A-** | — (short session, 26 steps) |
| DA-009 | Jun 17 | v3.0.5 | 84875f21 | RAG timed out + LCA called wrong → zero file targets → agent used run_command as grep workaround | 1🟢 + 1🟡 | 🟡 **B+** | — (agent was right, discover was broken) |
| DA-010 | Jun 17 | v3.0.5 | cc13736d | **4 steps completed ✅** — RAG+LCA worked, DB in Step 3 only, human checkpoint at Step 4. 9 greps in Step 2 (justified: discover missed report controller + JS) | 0🔴 + 1⚠️ | 🟢 **A-** | — (best run, 90 steps) |
| DA-011 | Jun 17 | v3.6.0 | 01e7d955 | **Intelligence upgrade test** — discover now produces 15.5KB self-contained output. 0 follow-up view_file/grep needed. Call chain via level2 dict, formulas+magic values+RAG inline code all working. | 0 violations | 🟢 **A** | — (infrastructure, not bug-fix session) |
| DA-012 | Jun 18 | v3.6.0 | 073a190d + 3cda0914 | **Real bug test ×2** — Pipeline 100% compliant. Discover worked but agent still did 6 view_file + 3-4 grep after discover. Root cause: L2 method body (`get_cash_in_hand`) not inline, parent function body missing. | 0 drift, 6 view+3 grep | 🟡 **B+** | [DA-012](drift-audits/DA-012_v3.6.0.md) |

---

## Violation Heatmap

| Violation Type | DA-001 | DA-002 | DA-003 | DA-004 | DA-005 | Total |
|---|---|---|---|---|---|---|
| Step Zero skip (System Brain) | ✗ | | | | | 1 |
| Step Zero skip (Module Brain) | ✗ | | | | ✗ | 2 |
| Step Zero skip (Recipe search) | ✗ | | | | | 1 |
| Step Zero skip (Pattern search) | ✗ | | | | | 1 |
| Bootstrap skip | ✗ | ✗ | | | | 2 |
| Pipeline never entered | | ✗ | | | | 1 |
| Parallel investigation during discover | | | ✗✗✗✗✗✗ | | | 6 |
| Polled task status (unnecessary) | | | | ✗ | | 1 |
| Killed CLI command | | | | | ✗ | 1 |
| Manual investigation without approval | | | | | ✗ | 1 |
| Never called step-done | | | | | ✗ | 1 |
| Never called what-next again | | | | | ✗ | 1 |
| Brain read outside pipeline | | | | | ✗ | 1 |
| Scope creep (next step's work) | ✗ | | | | | 1 |
| Read file outside discovery hits | ✗ | | | | | 1 |
| Skipped required LCA commands | ✗ | | | | | 1 |
| Wrong artifact name | ✗ | | | | | 1 |
| Missing confidence tag | ✗ | | | | | 1 |
| Incomplete artifact sections | ✗ | | | | | 1 |
| Summarized instead of evidence | ✗ | | | | | 1 |
| Missing RequestFeedback flag | ✗ | | | | | 1 |

---

## Trend Metrics

| Metric | Value |
|---|---|
| Total audits | **12** |
| Average grade | 🟡 B+ (D, F, C, B, D, B, A-, A-, B+, A-, A, B+) |
| Best grade achieved | 🟢 **A** (DA-011) |
| Pipeline entry rate | **92%** (11/12 — DA-002 failed) |
| Bootstrap compliance | **100%** since v3.0.1 (10/10 tests) |
| Parallel investigation drift | **Fixed** — 0 since v3.0.2 |
| Step boundary compliance | **Fixed** — correct since v3.0.4 |
| Banner/show redundancy | **Fixed** — eliminated in v3.0.4 |
| Post-discover view_file | **6** (target: 0) — L2 method body gap |
| Post-discover grep | **3-4** (target: 0) — constant/table lookup gap |

---

## Top Recurring Patterns

| # | Pattern | Count | Root Cause | Fix Applied | Status |
|---|---|---|---|---|---|
| 1 | Step Zero skipped | 4 | Reading ≠ Following | SKILL.md Rule 7: defers to pipeline | ✅ Fixed (v3.0.1) |
| 2 | Bootstrap not read | 2 | No re-entry points | GEMINI.md + SKILL.md alignment | ✅ Fixed (v3.0.1) |
| 3 | Parallel investigation during discover | 6 | Competing objectives | steps.json: forbid parallel work | ✅ Fixed (v3.0.2) |
| 4 | Killed slow CLI command | 1 | No rule against it | SKILL.md Rule 8: never kill CLI | ✅ Fixed (v3.0.3) |
| 5 | Bypass without user approval | 1 | No escalation protocol | SKILL.md Rule 9 + step instruction | ✅ Fixed (v3.0.3) |
| 6 | RAG causes discover to hang | 1 | Model download on first run | RAG 20s timeout in thread | ✅ Fixed (v3.0.3) |
| 7 | Discover output requires follow-up reads | 9+ | Missing inline code, formulas, callers | level2 dict + RAG inline + formulas | ⚠️ Partial (v3.6.0) — reduced from 12 to 6 |
| 8 | L2 method body missing from discover | 6 | Discover shows L2 name but not SQL body | Need to include L2 source code inline | 🔲 Open (v3.7.0) |

---

## Process Improvement Backlog

| Improvement | Source | Status | Impact |
|---|---|---|---|
| Flatten pipeline to 8 steps | DA-001 | ✅ Implemented (v3.0.0) | High |
| Validated step-done | DA-001 | ✅ Implemented (v3.0.0) | High |
| Add Express track | DA-001 | ✅ Implemented (v2.6) | High |
| `what-next` step-at-a-time delivery | DA-001 | ✅ Implemented (v2.6) | High |
| GEMINI.md defers to pipeline | DA-002 | ✅ Implemented (v3.0.1) | Critical |
| Gut competing workflows | DA-003 | ✅ Implemented (v3.0.1) | High |
| Forbid parallel investigation | DA-003 | ✅ Implemented (v3.0.2) | High |
| Remove redundant banner | DA-003 | ✅ Implemented (v3.0.2) | Medium |
| RAG timeout (20s thread) | DA-005 | ✅ Implemented (v3.0.3) | Critical |
| File write fsync + verify | DA-004 | ✅ Implemented (v3.0.3) | Medium |
| Escalation protocol for failures | DA-005 | ✅ Implemented (v3.0.3) | Critical |
| Never-kill-CLI rule | DA-005 | ✅ Implemented (v3.0.3) | Critical |
| LCA index direct JSON parse | DA-009 | ✅ Implemented (v3.1.0) | High |
| Mandatory test generation | User directive | ✅ Implemented (v3.2.0) | High |
| level2 dict for call chain | DA-010 | ✅ Implemented (v3.6.0) | High |
| SQL formulas in discovery | DA-010 | ✅ Implemented (v3.6.0) | Medium |
| Magic numbers decoded | DA-010 | ✅ Implemented (v3.6.0) | Medium |
| Reverse callers (called_by) | DA-010 | ✅ Implemented (v3.6.0) | High |
| RAG inline code snippets | DA-010 | ✅ Implemented (v3.6.0) | High |
| Convert workflows to checklists | DA-001 | ⬜ Planned | Medium |
| Automated drift audit script | Planned | ⬜ Planned | Medium |
| Orchestrator pattern (subagents) | Fallback | ⬜ If needed | High |
