# SDLC Pipeline v2.0 — Final Rewrite Spec

> **Strategy: Parallel build.** `.sdlc/` (v1.1) stays untouched. Build v2.0 in `.sdlc-v2/`. Swap when proven.

## v2.0 Structure

```
.sdlc-v2/
├── engine/
│   ├── cli.py                # 10 commands (~200 lines)
│   ├── validate_write.py     # Cherry-pick from v1.1 (as-is)
│   └── file_permissions.json # Per-phase file rules (extracted from v1.1 roles.json)
│
├── roles/                    # Per-phase markdown (AI reads natively)
│   ├── IDLE.md
│   ├── DISCUSS.md
│   ├── REQUIREMENT.md
│   ├── PLANNING.md
│   ├── CODING.md
│   ├── REVIEW.md
│   └── COMMIT.md
│
├── templates/                # Spec file templates
│   ├── requirement.md
│   ├── design.md
│   ├── tasks.md
│   └── review.md
│
├── active/                   # Live task specs (committed)
├── done/                     # Completed specs (committed)
├── backlog.md                # Human-readable task list (committed)
├── pipeline.json             # Minimal state (gitignored)
├── dashboard.html            # Cherry-pick from v1.1, simplify
├── setup.py                  # Onboarding (rewritten for v2.0)
├── .gitignore
└── README.md
```

## Cherry-Pick Map

| v1.1 Source | v2.0 Destination | Action |
|---|---|---|
| `.sdlc/validate_write.py` | `engine/validate_write.py` | Copy as-is |
| `.sdlc/roles.json` → `file_permissions` sections | `engine/file_permissions.json` | Extract only the file rules |
| `.sdlc/pipeline_state.py` → spec templates | `templates/*.md` | Copy the TEMPLATE constants |
| `.sdlc/pipeline_state.py` → `can_transition()` | `engine/cli.py` → gate checks | Simplify, copy logic |
| `.sdlc/pipeline_state.py` → `start_task()` | `engine/cli.py` | Simplify |
| `.sdlc/dashboard.html` | `dashboard.html` | Copy, update API |
| `.sdlc/setup.py` | `setup.py` | Rewrite for v2.0 |
| `.sdlc/roles.json` → persona/instructions | `roles/*.md` | Rewrite as markdown |
| `.sdlc/sdlc.cmd` | `sdlc.cmd` | Update path |

## What v2.0 Does NOT Have

- ❌ No 2700-line monolith — split into focused files
- ❌ No roles.json — replaced by roles/*.md
- ❌ No get-prompt — AI reads markdown directly
- ❌ No sub-task management — use separate conversations
- ❌ No metrics/context health — premature optimization
- ❌ No 35 CLI commands — only 10

## CLI Commands (10 total)

```
sdlc show                              # Current task + phase + spec status
sdlc start -t fix -m billing -s "..."  # Create task → active/{ID}/
sdlc queue -t fix -m billing -s "..."  # Add to backlog.md
sdlc pick <ID>                         # Move from backlog → active
sdlc phase                             # Show current phase
sdlc transition <PHASE>                # Move phase (with gate check)
sdlc spec-status                       # Show spec file completeness
sdlc create-spec <type>                # Create design/tasks/review
sdlc done                              # Move active/ → done/
sdlc history                           # List done/ folders
```

## Transition Gates

| To Phase | File Gate |
|---|---|
| → PLANNING | `requirement.md` exists with ≥1 real acceptance criterion |
| → CODING | `tasks.md` exists with checkboxes |
| → REVIEW | All `tasks.md` checkboxes checked |
| → COMMIT | `review.md` verdict = PASS or PASS_WITH_ADVISORIES |

## pipeline.json (Minimal — gitignored)

```json
{
  "active_task": {
    "id": "BIL-042",
    "type": "fix", 
    "module": "billing",
    "summary": "Discount rounding",
    "phase": "CODING",
    "started_at": "2026-06-12T10:00:00"
  },
  "backlog": [
    {"id": "EST-057", "type": "fix", "module": "estimation", 
     "summary": "Rate not saving", "priority": "medium"}
  ]
}
```

No per-phase state sections. Spec files track everything.

## Build Order

### Step 1: Roles (30 min)
Create `roles/*.md` — extract persona/instructions from v1.1 `roles.json`, rewrite as markdown.

### Step 2: Templates (15 min)
Create `templates/*.md` — copy from v1.1 `pipeline_state.py` TEMPLATE constants.

### Step 3: Engine (1 hour)
Create `engine/cli.py`:
- Task lifecycle (start, queue, pick, done)
- Phase management (show, transition)
- Gate checks (spec file validation)
- `pipeline.json` read/write

Copy `engine/validate_write.py` from v1.1.
Extract `engine/file_permissions.json` from v1.1 `roles.json`.

### Step 4: Master SKILL.md (30 min)
Update global skill to point to `.sdlc-v2/` structure:
- Read `engine/cli.py show` output
- Read `roles/{PHASE}.md`
- Follow instructions

### Step 5: Dashboard (30 min)
Copy from v1.1, update to read `active/*/` folders directly.

### Step 6: Setup + Package (30 min)
- Rewrite `setup.py` for v2.0 structure
- Create `sdlc-pipeline-v2.0.0.zip`
- Test onboarding

### Step 7: E2E Test (30 min)
Full walkthrough: start → requirement → planning → coding → review → commit → done.

---

## Acceptance Criteria

- [ ] v1.1 (`.sdlc/`) completely untouched
- [ ] v2.0 (`.sdlc-v2/`) works independently
- [ ] Total Python code ≤ 400 lines (cli.py + validate_write.py)
- [ ] 7 role markdown files, each ≤ 40 lines
- [ ] 4 spec templates
- [ ] All 4 transition gates work
- [ ] Dashboard renders from active/ folders
- [ ] E2E test passes
- [ ] Packaged as v2.0.0.zip

## Next Conversation Prompt

```
Read `.sdlc/V2_REWRITE_SPEC.md` and execute the v2.0 rewrite.
Build in `.sdlc-v2/` — do NOT touch `.sdlc/` (v1.1 reference).
Start with Step 1 (roles/*.md).
```
