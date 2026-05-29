# ⚡ Workflow Cheatsheet

> Full details: `WORKFLOW_WALKTHROUGH_ESTIMATION.md` | Templates: `knowledge_brain/_TEMPLATE/`

---

## Workflow Sequence

```
Setup → Brain (100%) → Audit → Triage → Fix → Test → Learn → Report
```

| Phase | Command | One-liner |
|---|---|---|
| Setup | `/setup-new-client` | New project — creates dirs, config, GitHub labels |
| | `/setup-existing-client` | Existing client — copies brain, diffs for divergence |
| Build | `/build-module-brain` | Reads all code → 8–9 brain docs. **Run until COVERAGE_TRACKER = 100%** |
| | `/manage-bug-patterns` | Init / scan / add pattern entries |
| Find | `/module-bug-audit` | 6 rounds: Controller → Schema → Model → JS → Calcs → AJAX |
| Classify | `/bug-intake-triage` | Severity + Track + Category + Sprint + GitHub Issue |
| Fix | `/fix-single-bug` | **Entry point for ALL fixes.** Routes to ↓ based on track/complexity |
| | `/fix-architecture-bug` | Track A (system) — Antigravity leads, human approves diff |
| | `/fix-business-bug` | Track B (business) — human validates rule before code changes |
| Verify | `/test-and-verify` | Syntax check → category tests → smoke test (auto-called by fix workflows) |
| Close | `/learn-and-improve` | Updates brain + patterns + GitHub + ACTIVE_BUGS + velocity |
| Report | `/sprint-status` | Sprint progress from GitHub + ACTIVE_BUGS |
| | `/system-health` | Cross-module risk heatmap 🔴/🟡/🟢 |
| | `/overall-bug-dashboard` | Full report + developer attribution + velocity trends |
| Maintain | `/validate-workflows` | Checks files exist, cross-refs valid, no hardcoded paths |

---

## ✅ DOs

1. **Brain before audit** — no brain = missed bugs
2. **Triage before fix** — every bug needs severity, track, sprint, GitHub issue
3. **Always enter via `/fix-single-bug`** — it has guards, routing, and tracking
4. **Trace full data flow** for money bugs: Form → JS → AJAX → PHP → DB
5. **Check Module Brain first** before reading source files
6. **Run `/learn-and-improve` after every fix** — not optional
7. **Document rollback BEFORE applying fix** → `ROLLBACK_REGISTRY.md`
8. **Check for duplicates** before creating bug entries (local + GitHub)
9. **Syntax check after every PHP change**: `& "{PHP_PATH}" -l {file}`
10. **Keep ACTIVE_BUGS.md ↔ GitHub in sync**
11. **Run brain building until COVERAGE_TRACKER = 100%** — one pass is never enough

## ❌ DON'Ts

1. **Don't audit without brain** — you'll miss context-dependent bugs
2. **Don't skip triage** — no tracking = chaos
3. **Don't fix without root cause** — symptom patches create new bugs
4. **Don't call `/fix-architecture-bug` or `/fix-business-bug` directly** — go through `/fix-single-bug`
5. **Don't apply fixes without human approval** on Track A/B bugs
6. **Don't skip `/learn-and-improve`** — it's how the system learns
7. **Don't duplicate functions** — extend existing ones with conditions
8. **Don't run destructive SQL** without approval
9. **Don't hardcode paths** — use `{VARIABLE}` from `.agent/config.md`
10. **Don't fix one layer only** — if JS is wrong, check PHP too

---

## 📐 Templates (`knowledge_brain/_TEMPLATE/`)

| Template | Purpose | When |
|---|---|---|
| `COVERAGE_TRACKER.md` | Brain completeness per metric — **must reach 100%** | After every brain round |
| `FORENSIC_TEMPLATE.md` | 6-layer investigation: Symptom → Reproduce → JS → PHP → DB → Root Cause | When diagnosing any bug |
| `INVARIANT_MATRIX.md` | Variant × behavior grids (e.g., Scheme Type × GST Type) | Config-driven modules + business bug testing |

---

## 🏷️ Classification Quick Ref

| Severity | Criteria | Sprint | Track | Meaning | Who Leads |
|---|---|---|---|---|---|
| P0 | Money wrong, data lost, security | Sprint 1 | A (System) | Code defect | Antigravity leads |
| P1 | Wrong info, blocks workflow | Sprint 2 | B (Business) | Wrong business result | Human validates rule |
| P2 | Works but wrong behavior | Sprint 3 | | | |
| P3 | Cosmetic, edge case | Sprint 4 | | | |

**Categories** (pick ONE): Logic · Data Validation · Database · Exception Handling · Security · Performance · Integration · Concurrency

---

## ⏱️ Human Checkpoints

| Workflow | What You Do |
|---|---|
| `/fix-architecture-bug` Step 5 | Approve proposed fix approach |
| `/fix-architecture-bug` Step 10 | Review final diff |
| `/fix-business-bug` Step 5 | Confirm the correct business rule |
| `/fix-business-bug` Step 10 | Smoke test with real data |
| `/fix-single-bug` Step 7 | Approve diff + test results |

---

## 🗂️ Key Files

| File | Purpose |
|---|---|
| `.agent/config.md` | Central config — all workflows read from this |
| `.agent/GEMINI.md` | Project rules — enforced every conversation |
| `bug_report_AI/ACTIVE_BUGS.md` | Live bug tracker |
| `bug_report_AI/ROLLBACK_REGISTRY.md` | Emergency rollback index |
| `bug_report_AI/COMMON_BUG_PATTERNS.md` | Pattern library + detection rules |
| `knowledge_brain/{Module}/MODULE_BRAIN.md` | Module architecture + risks |
