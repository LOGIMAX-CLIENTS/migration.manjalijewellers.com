# SDLC Pipeline — Stabilization Tracker

> **For Management Review**
> Project: Logimax ERP — AI-Assisted Bug Remediation Pipeline
> Period: June 15–18, 2026
> Status: **🟢 Stabilized + Intelligence Phase — 12 audits, Grade trend: D→F→C→B→D→B→A-→A-→B+→A-→A→B+, L2 body gap identified**

---

## Executive Summary

We are building an AI-assisted software development lifecycle (SDLC) pipeline that guides the AI agent through structured steps when fixing bugs. The goal: prevent the AI from skipping quality checks, producing incomplete fixes, or working on the wrong things.

**Phase 1 (Stabilization)**: The AI consistently "drifted" — it reads the process rules, then ignores them. Seven versions and 10 audits fixed this. Grade improved from F to A-.

**Phase 2 (Intelligence — Current)**: With drift under control, we upgraded the `discover` command to produce self-contained intelligence from LCA index + RAG. Result: **98% reduction in tool calls** (42 → 1), **87% faster** (~8 min → ~55 sec). The agent now diagnoses bugs from a single command output.

---

## Version History

| Version | Date | Key Change | Test Result | Audit |
|---|---|---|---|---|
| **v2.5.0** | Jun 15 | Role-based phases, 10-phase pipeline | Heavy drift — agent skipped phases entirely | Pre-audit (informal) |
| **v2.6.0** | Jun 16 | Added ALLOWED/FORBIDDEN/DONE_WHEN tool constraints per step | 13 violations, Grade 🔴 D | [DA-001](drift-audits/DA-001_v1.0.md) |
| **v3.0.0** | Jun 17 | Flattened to 8 steps, validated step-done (blocks if deliverable missing) | Agent never entered pipeline — Grade ⛔ F | [DA-002](drift-audits/DA-002_v3.0.0.md) |
| **v3.0.1** | Jun 17 | Fix: IDLE handling, SKILL.md explicit start instruction, gut competing workflows | Pipeline entered ✅, but parallel investigation drift — Grade 🟠 C | [DA-003](drift-audits/DA-003_v3.0.1.md) |
| **v3.0.2** | Jun 17 | Fix: default track STANDARD, forbid parallel investigation, remove redundant banner | **Zero parallel drift** ✅ — Grade 🟡 **B** (best yet) | [DA-004](drift-audits/DA-004_v3.0.2.md) |
| **v3.0.2** | Jun 17 | Same version, second test (different agent session) | Agent killed slow discover → bypassed pipeline — Grade 🔴 D | [DA-005](drift-audits/DA-005_v3.0.2.md) |
| **v3.0.3** | Jun 17 | Fix: RAG 20s timeout, fsync file write, escalation protocol, never-kill rule | Pipeline followed ✅, minor step boundary violations — Grade 🟡 **B** | [DA-006](drift-audits/DA-006_v3.0.3.md) |
| **v3.0.4** | Jun 17 | Fix: no-banner rule, stay-in-step rule, Step 2 DB prohibition | **Full pipeline loop ✅** — 4 steps, zero bypasses — Grade 🟢 **A-** 🎉 | [DA-007](drift-audits/DA-007_v3.0.4.md) |
| **v3.0.5** | Jun 17 | Fix: LCA `--index` flag, RAG 60s timeout, grep fallback in Step 2, progress markers | ✅ Both RAG (10 hits) + LCA (search+impact) working. Discovery.md has file targets. | DA-009 (pre-fix: B+) |
| **v3.0.6** | Jun 17 | Fix: LCA 3/3 broader keyword search (finds report controllers + view layer) | ✅ Better coverage, all 3 LCA phases complete | DA-010 |
| **v3.1.0** | Jun 17 | LCA index integration — direct JSON parse replaces CLI subprocess for call chain | ✅ Call chain L1+L2 resolved from `.lca/reports.json` | — (infrastructure) |
| **v3.2.0** | Jun 17 | Mandatory test generation + EXPRESS track threshold tuning | ✅ Pipeline enforces Playwright test cases | — (infrastructure) |
| **v3.6.0** | Jun 17 | **Intelligence upgrade** — level2 dict, formulas, magic values, called_by, RAG inline code | ✅ 15.5KB discovery with all sections populated | DA-011 |
| **v3.6.0** | Jun 18 | Same version, **real bug test ×2** (different agent sessions) | Pipeline 100% compliant. But 6 view_file + 3-4 grep after discover — L2 method body gap | [DA-012](quality/drift-audits/DA-012_v3.6.0.md) |

---

## Root Cause Analysis

### Why does the AI drift?

| Cause | Evidence | Solution Applied | Status |
|---|---|---|---|
| **Reading ≠ Following** | DA-001: Read workflow, skipped 8 of 11 steps | v3.0.0: Simplified to 8 steps | ✅ |
| **Text rules are advisory** | DA-001: 13 violations despite detailed constraint boxes | v3.0.0: Replaced with mechanical file validation | ✅ |
| **Competing instructions** | DA-002: Agent followed GEMINI.md instead of pipeline | v3.0.1: GEMINI.md defers to pipeline | ✅ |
| **IDLE gap** | DA-002: Agent saw "IDLE" and started investigating | v3.0.1: Explicit "start task first" instruction | ✅ |
| **Parallel work during async** | DA-003: 6 parallel investigations while discover ran | v3.0.2: Forbid parallel work in step instruction | ✅ |
| **CLI bugs mask as agent drift** | DA-003: Empty track field caused what-next to fail | v3.0.2: Default track to STANDARD | ✅ |
| **Kills slow commands** | DA-005: Agent killed discover after 60s, bypassed pipeline | v3.0.3: Rule 8 (never kill), RAG timeout, escalation protocol | ✅ |
| **No escalation path** | DA-005: Agent worked around failure instead of asking user | v3.0.3: Rule 9 (escalate, don't work around) + step instruction | ✅ |

### The fundamental insight

> "The agent drifts from text rules. It does NOT drift from running CLI commands."

All enforcement that works is **mechanical** (Python code that blocks advancement). All enforcement that fails is **text-based** (rules the agent is supposed to read and follow).

**Corollary** (from DA-005): When mechanical enforcement fails (slow CLI), the agent falls back to text-rule behavior (improvising). Solution: make CLI resilient so it never fails.

---

## What's Been Built

### Infrastructure

| Component | Status | Purpose |
|---|---|---|
| CLI Engine (`cli.py`) | ✅ 2,900+ lines | Task management, step tracking, discovery, metrics |
| Steps Definition (`steps.json`) | ✅ v3.0.3 | 8 flat steps with deliverable validation |
| RAG Integration | ✅ 61,465 chunks | Semantic search + **inline code snippets** (v3.6) |
| LCA Integration | ✅ **level2 dict** | Call graph, formulas, magic numbers, called_by (v3.6) |
| Recipe System | ✅ | Central bug fix recipe repo via GitHub MCP |
| Knowledge Brain | ✅ | 45 module brains + system-level cross-module docs |
| Drift Audit Framework | ✅ 10 audits | Post-conversation violation scoring |
| Multi-chat Handoff | ✅ | `.continue-here/` directory for cross-conversation context |

### Validated Step-Done (v3.0.0)

Each step requires a specific deliverable. `step-done` checks if it exists before advancing:

```
Step 1 (discover)    → discovery.md must exist
Step 2 (investigate) → context.md must have ## Hypothesis section
Step 3 (test_design) → test_cases.md must exist
Step 4 (plan)        → requirement_review.md artifact must exist
Step 5 (approval)    → requirement.md must exist (human approved)
Step 6 (code)        → git diff must show modified files
Step 7 (verify)      → context.md must have ## Verification section
Step 8 (commit)      → git log must contain task ID
```

### Resilient Discovery (v3.0.3)

Discover handles infrastructure failures gracefully:
- **RAG timeout**: 20s thread timeout → skips RAG, still produces discovery.md
- **File write**: `os.fsync()` + existence verification
- **Agent escalation**: If anything fails → STOP, report to user, wait for approval

### Intelligent Discovery (v3.6.0 — New)

Discover now produces self-contained intelligence that eliminates follow-up tool calls:

| Data Source | v3.0 | v3.6 |
|---|---|---|
| RAG results | File path + summary | File + summary + **inline code** (top 3) |
| Call chain | O(7,848) raw edge scan | **O(1) level2 dict** (20x faster) |
| Reverse callers | Not available | **called_by** with cap=5 |
| SQL formulas | Not available | **3,111 formula entries** searchable |
| Magic numbers | Not available | **687 status codes** decoded |
| Tables touched | Listed | Listed (same) |
| Model code L1/L2 | Auto-traced | Auto-traced (same) |

**Before (manual investigation)**:
```
42 tool calls → 12 view_file + 9 grep + 9 run_command + 8 MCP
82 model turns, ~8 minutes
```

**After (single discover)**:
```
1 command, ~55 seconds
15.5 KB discovery.md with everything inline
```

---

## Stabilization Roadmap

| Phase | Description | Impact | Status |
|---|---|---|---|
| **1. Flatten + Validate** | 8-step pipeline + validated step-done | Reduces drift surface by 64% | ✅ Delivered |
| **2. Discover absorbs Step Zero** | Brain/recipe/playbook auto-reads inside discover | Eliminates 31% of violations | ✅ Delivered |
| **3. Resilient CLI** | RAG timeout, file fsync, escalation protocol | Prevents workaround drift | ✅ Delivered |
| **4. LCA Index Integration** | Direct JSON parse of `.lca/reports.json` for call chain | Replaces CLI subprocess calls | ✅ Delivered (v3.1) |
| **5. Test Enforcement** | Mandatory Playwright test generation before step-done | Prevents untested fixes | ✅ Delivered (v3.2) |
| **6. Discover Intelligence** | level2 dict, formulas, magic values, RAG inline | 98% fewer tool calls post-discover | ✅ Delivered (v3.6) |
| **7. Checklist instructions** | Literal checkbox instructions per step | Eliminates artifact errors | 🔲 Next |
| **8. Orchestrator pattern** | Subagent per step (structural drift prevention) | Near-zero drift theoretically | 🔲 If needed |
| **9. Automated drift audit** | `sdlc audit` command for post-conversation scoring | Trend monitoring | 🔲 Planned |

---

## Decisions Made

| # | Decision | Rationale | Date |
|---|---|---|---|
| D-01 | Drop ALLOWED/FORBIDDEN/DONE_WHEN text constraints | Proven ineffective across 3 iterations | Jun 17 |
| D-02 | Replace 3 tracks × 11 steps with 1 track × 8 steps | Fewer steps = less drift surface | Jun 17 |
| D-03 | Put enforcement in CLI code, not text rules | Agent reliably runs CLI; unreliably follows text | Jun 17 |
| D-04 | Archive role files, don't delete | Useful for Phase 5 (orchestrator pattern) | Jun 17 |
| D-05 | Add test_design step before plan step | Test cases from impact areas guide the fix plan | Jun 17 |
| D-06 | Use `.continue-here/` directory not single file | Multiple parallel tasks need separate handoff files | Jun 17 |
| D-07 | Gut competing `.agent/workflows/` files | Old workflow files compete with SKILL.md | Jun 17 |
| D-08 | Archive old `.sdlc/workflows/` phase files | Replaced by flat steps | Jun 17 |
| D-09 | Remove redundant banner call | Saves ~3s per conversation start | Jun 17 |
| D-10 | RAG timeout at 20s, not infinite | Prevents discover from hanging on model download | Jun 17 |
| D-11 | Escalate on failure, don't work around | Agent should ask user, not silently bypass pipeline | Jun 17 |
| D-12 | Use LCA level2 dict instead of raw calls array | O(1) vs O(7,848) — 20x faster call chain | Jun 17 |
| D-13 | Include SQL formulas in discovery | Agent sees `SUM(p.payment_amount)` without reading code | Jun 17 |
| D-14 | Include called_by (reverse callers) | Agent knows regression scope before fixing | Jun 17 |
| D-15 | Cap called_by at 5 entries | Utility methods (40+ callers) create noise | Jun 17 |
| D-16 | Decode magic numbers in discovery | `report_type == 7` is meaningless without context | Jun 17 |
| D-17 | Inline RAG code (top 3, [Code] only) | Eliminates 2-3 view_file calls per bug | Jun 17 |
| D-18 | Stem matching for formula/magic lookup | `cash_book_details` vs `get_cash_book_detailed` must match | Jun 17 |

---

## Risk Register

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| Phases 1-4 still insufficient — agent finds new ways to drift | Medium | High | Phase 5 (orchestrator pattern) is the fallback |
| Pipeline overhead slows down simple bug fixes | Low | Medium | Express track for recipe-matched fixes |
| Management expects zero drift — unrealistic for current AI | High | Medium | Target B grade (≤3 minor), not A |
| Context window limits cause late-step drift | Medium | Medium | Periodic re-check planned |
| New agent models behave differently | Medium | Medium | Audit framework catches regressions |

---

## Metrics

### Drift Audit Grades

| Metric | DA-001 | DA-002 | DA-003 | DA-004 | DA-005 | DA-006 | DA-007 | DA-008 | DA-009 | DA-010 | DA-011 | DA-012 | Target |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Grade | 🔴 D | ⛔ F | 🟠 C | 🟡 B | 🔴 D | 🟡 B | 🟢 A- | 🟢 A- | 🟡 B+ | 🟢 A- | 🟢 A | 🟡 B+ | 🟡 B |
| Pipeline entered? | N/A | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | 100% |
| Steps completed | 0 | 0 | 1 | 1 | 0 | 2 | **4** | 2 | 2 | **4** | N/A | **3** | All |
| Violations | 13 | Crit | 6 | 1 | 5 | 4 | 3 | 3 | 2 | 1 | 0 | 0 | ≤3 |

### Discovery Intelligence Metrics (v3.6.0)

| Metric | Manual (pre-v3.0) | v3.0 | v3.6 (lab) | v3.6 (real test) | Target |
|---|---|---|---|---|---|
| Tool calls to diagnose | 42 | 1 + ~10 follow-up | **1** (lab) | 1 + **9 follow-up** | <5 |
| view_file calls | 12 | 0 + ~5 follow-up | **0** (lab) | **6** | 0 |
| grep_search calls | 9 | 0 + ~4 follow-up | **0** (lab) | **3-4** | 0 |
| Model turns | 82 | 1 + ~15 follow-up | **1** (lab) | 1 + ~15 follow-up | <5 |
| Time to diagnose | ~8 min | ~55s + ~5 min manual | **~55s** (lab) | ~55s + ~4 min | <2 min |
| Discovery file size | 0 bytes | 12.6 KB | **15.5 KB** | 15.9 KB | N/A |
| Data sources in output | 0 | 5 | **11** | 11 | 11 |

> **Lab vs Real**: DA-011 tested discover output quality (everything present). DA-012 tested whether the agent **actually uses** the inline data instead of re-reading files. Result: agent still reads L2 method bodies that aren't in discover. Fix target: v3.7.0.

---

## Files Reference

| File | Path | Purpose |
|---|---|---|
| CLI Engine | `.sdlc/engine/cli.py` | Core pipeline engine (2,900+ lines) |
| Steps Definition | `.sdlc/steps.json` | Step instructions + deliverables |
| Agent Instructions | `.sdlc/SKILL.md` | What the agent reads at conversation start |
| Changelog | `.sdlc/CHANGELOG.md` | Version history with metrics |
| LCA Index | `.lca/reports.json` | 7,848 call edges, 777 level2 entries, 3,111 formulas |
| RAG Store | `~/.logimax/rag_store/` | 61,465 chunks, 6,816 files indexed |
| Drift Tracker | `.sdlc/quality/DRIFT_TRACKER.md` | Violation trends |
| Stabilization Tracker | `.sdlc/quality/STABILIZATION_TRACKER.md` | This file — management view |
| DA-001 to DA-007 | `.sdlc/quality/drift-audits/DA-00*.md` | Individual audit reports |
| Deep Analysis | `.sdlc/quality/drift-audits/pipeline_drift_analysis.md` | Root cause analysis |
