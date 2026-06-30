# Pipeline Monitoring & Learning System

> **Status**: PLANNED — Not yet implemented. Created 2026-06-18.
> **Priority**: Implement before multi-developer rollout.

## Problem

When multiple developers use the SDLC pipeline, there's **no way to know**:
- Is the discover step actually helping, or are developers ignoring it?
- Which types of bugs does the pipeline struggle with?
- Are fixes correct first-time, or do they get reverted?
- How much time does each phase actually take?
- What patterns should become recipes?

Today, learning only happens through **manual audit** (DA-002 through DA-014) which costs ~1 hour each and doesn't scale.

## Proposed Solution: 3-Tier Monitoring

### Tier 1: Passive Metrics (Zero Effort)
Automatically log key metrics into `metrics.jsonl` per task — no developer input needed.

### Tier 2: Post-Task Survey (5 Seconds)
After `done`, ask the developer 3 quick questions about fix quality.

### Tier 3: Analytics Dashboard (`sdlc report`)
Aggregate metrics across all tasks to show trends and actionable insights.

---

## Tier 1: Passive Metrics Collection

### What to log (automatically, on every phase transition)

**Discover phase example:**
```jsonl
{
  "task_id": "CAS-2606180657",
  "event": "phase_complete",
  "phase": "DISCOVER",
  "timestamp": "2026-06-18T12:30:00Z",
  "duration_sec": 45,
  "developer": "admin",
  "module": "opening_master",
  "task_type": "fix",
  "discover": {
    "quality_score": 75,
    "rag_hits": 8,
    "rag_strong": 2,
    "lca_modules": 2,
    "recipe_found": false,
    "code_snippets": true,
    "call_tree_depth": 3,
    "tables_found": 12,
    "brain_chunks_matched": 4,
    "track": "code-first"
  }
}
```

**Commit phase example:**
```jsonl
{
  "task_id": "CAS-2606180657",
  "event": "phase_complete",
  "phase": "COMMIT",
  "timestamp": "2026-06-18T14:00:00Z",
  "duration_sec": 5400,
  "files_changed": 3,
  "lines_added": 45,
  "lines_removed": 12,
  "total_phases_duration_sec": 7200
}
```

### Where to hook in cli.py
- `cmd_discover()` — log discover quality metrics after writing discovery.md
- `cmd_done()` / phase transitions — log phase completion with timing
- `_save_state()` — timestamp every state change

### Storage
`.sdlc/metrics/metrics.jsonl` (append-only, one JSON per line, **gitignored**)

---

## Tier 2: Post-Task Quality Survey

After task completes via `sdlc done`, print 3 quick survey questions:

```
✅ Task CAS-2606180657 moved to done/

Quick quality check (press Enter to skip):
  1. Fix correct first try? [Y/n]: Y
  2. Discovery useful? [1-5, 5=very]: 4  
  3. What would have helped? (optional): _
```

Logged to same `metrics.jsonl`:
```jsonl
{
  "task_id": "CAS-2606180657",
  "event": "quality_survey",
  "fix_correct_first_try": true,
  "discovery_usefulness": 4,
  "feedback": ""
}
```

> **Rule**: Survey must be **optional** (Enter to skip all). Developers will stop using the tool if it nags them.

---

## Tier 3: Analytics Dashboard

### `sdlc report` command

```bash
sdlc report                    # Last 7 days summary
sdlc report --since 2026-06-01 # Custom range
sdlc report --developer ravi   # Per-developer
sdlc report --module billing   # Per-module
sdlc report --json             # Machine-readable
```

### Output example
```
📊 SDLC Pipeline Report (Jun 11 - Jun 18, 2026)
═══════════════════════════════════════════════

Tasks:     12 completed, 3 in progress
Avg time:  2.1 hours per fix (down from 3.4h last week)
Fix rate:  83% correct first try

Discovery Quality:
  Avg score: 72/100
  RAG useful: 8/12 tasks (67%)
  Recipes applied: 2/12 tasks (17%)
  Call tree helped: 10/12 tasks (83%)

Module Hotspots:
  billing:     5 fixes (41%)  ← most fixes
  reports:     3 fixes (25%)
  estimation:  2 fixes (17%)

Improvement Opportunities:
  ⚠ 3 tasks had discovery score < 50 — review for pattern gaps
  ⚠ billing module: 0% recipe hit rate — consider building recipes
  ⚠ Avg 6 post-discover greps in reports module — brain may be stale
```

---

## Tier 3b: Learning Loop (Auto-Improvements)

### `sdlc learn` command

Analyzes completed tasks to identify:

1. **Missed recipes**: Tasks where the fix pattern appeared 3+ times → auto-suggest recipe creation
2. **Stale brains**: Modules where discovery score is consistently low → flag for brain rebuild
3. **RAG gaps**: Queries that returned 0 hits → suggest what to add to ChromaDB
4. **Common mistakes**: Fixes that got reverted → analyze root cause patterns

```bash
sdlc learn                     # Analyze all completed tasks
sdlc learn --auto-recipe       # Auto-generate recipe suggestions
```

---

## Open Design Questions

1. **Metrics storage for multi-developer teams**:
   - Option A: Local `metrics.jsonl` per developer (simple, gitignored)
   - Option B: Shared git-tracked file (merge conflicts risk)
   - Option C: Central endpoint/webhook (most scalable, needs server)
   - *Recommended*: **A** for now, with `sdlc report --export` to share

2. **Survey format**:
   - Interactive terminal prompt (quick, won't work in CI)
   - File-based `review.md` template
   - *Recommended*: Interactive with file fallback

3. **Implementation priority**:
   - Tier 1 first (passive metrics) → zero effort, most value
   - Then Tier 3 (dashboard) → visibility
   - Then Tier 2 (survey) → depends on developer discipline
