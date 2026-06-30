# SDLC Pipeline Engine — Changelog

## v1.0.0 (2026-06-12)

**First stable release.** Role-locked, TDD-first SDLC pipeline with 10 phases, context intelligence, and cross-framework best practices.

### Core Engine
- 10-phase pipeline: IDLE → DISCUSS → VIBE → REQUIREMENT → PLANNING → TEST_DESIGN → CODING → TEST_EXECUTION → REVIEW → COMMIT → PR → MERGED
- VIBE mode with 8 auto-routing sub-roles (investigator, bug_fixer, builder, architect, code_reviewer, database_analyst, documenter, tester)
- State persistence in `pipeline.json` with auto-validation on load
- Timeline tracking with auto-prune at 200 entries
- Sub-task management for multi-conversation work
- Task archival to `history/` on reset or completion

### Integrity (Sprint 1)
- **Hard file permission enforcement** via `validate_write.py` — blocks writes that violate phase constraints
- **Phase-skip detection** — logs warnings when phases are skipped (e.g., IDLE → CODING)
- **State validation on load** — auto-repairs missing sections, invalid phases
- **Timeline pruning** — prevents `pipeline.json` from growing unbounded

### Metrics & Workflow (Sprint 2)
- **Phase duration metrics** (`sdlc metrics`) — avg time per phase, bottleneck detection, completion rate
- **Transition condition checks** (`can_transition`) — verifies preconditions before phase changes
- **CLI quick commands** — `sdlc quick`, `sdlc undo`, `sdlc.cmd` shortcut

### Fast-Track & Hooks (Sprint 3)
- **Fast-track mode** (`sdlc fast-track`) — shortened pipeline for trivial fixes (max 3 files, skips TEST_DESIGN)
- **Pre-commit validation** (`sdlc validate-commit`) — blocks commits from wrong phase, detects unplanned files

### Role Intelligence (Sprint 4)
- **Focused role prompts** (`sdlc get-prompt`) — extracts ~40 focused lines from 917-line roles.json
- **Suggest-next** (`sdlc suggest-next`) — context-aware "what should I do next?" (inspired by BMad's bmad-help)
- **Skill activation map** — links each phase/sub-role to specific skills, workflows, and knowledge brains
- **Phase transition re-read** — agent reloads role instructions on every phase change

### Context Intelligence (Sprint 5)
- **Decision tracker** (`sdlc add-decision`) — records resolved decisions, prevents downstream re-asking
- **Pause/resume handoff** (`sdlc pause` / `sdlc resume`) — generates `.sdlc/handoff.md` for cross-conversation context (inspired by GSD's gsd-pause-work)
- **Enhanced DISCUSS** — active Socratic questioning mode, identifies gray areas (inspired by GSD's gsd-explore)
- **Handoff auto-detection** — `get-prompt` flags existing handoff files with ⚠️ warning

### SKILL.md Rules
- RULE 1: Role Banner
- RULE 2: File Permission Guard (hard enforcement)
- RULE 3: Override Protocol
- RULE 4: Phase Boundaries
- RULE 5: Context Health
- RULE 6: VIBE Mode Auto-Routing
- RULE 7: Skill Activation (Approach D Hybrid)
- RULE 8: Decision Tracking
- RULE 9: Pause/Resume Handoff

### CLI Commands (26 total)
```
show, phase, set-phase, start-task, reset, export, log, set, get,
add-subtask, subtasks, activate-subtask, complete-subtask, context,
transition, history, restore, check-transition, fast-track,
validate-commit, metrics, quick, undo, get-prompt, suggest-next,
add-decision, decisions, clear-decisions, pause, resume, clear-handoff
```

### Files
```
.sdlc/
├── pipeline_state.py       ← 2031 lines — state engine
├── roles.json              ← 1030 lines — role definitions + skill map
├── validate_write.py       ← ~100 lines — file permission validator
├── pipeline.schema.json    ← state template
├── dashboard.html          ← visual dashboard
├── api.php                 ← dashboard API
├── setup.py                ← multi-project deployment
├── sdlc.cmd                ← CLI shortcut
├── .gitignore              ← ignores local state
├── pipeline.json           ← (gitignored) active state
├── handoff.md              ← (gitignored) pause/resume context
├── tasks/                  ← (gitignored) sub-task state
└── history/                ← (gitignored) archived tasks
```

### Cross-Framework Inspirations
| Source | What We Borrowed |
|---|---|
| BMad Method (49K★) | Per-role focused prompts, bmad-help → suggest-next |
| GSD (Get Shit Done) | Decision tracking, pause/resume handoff, Socratic exploration |
| LangGraph | State validation patterns (validated our approach) |
| AutoGen/AG2 | Role specialization concepts (validated our approach) |

---

## v1.1.0 (2026-06-12)

**Multi-task parallel conversation support.** Enables multiple developers (or one developer in multiple conversations) to run independent tasks simultaneously without state collisions.

### Architecture Change
- `pipeline.json` is now a **lightweight registry** (v2.0 format) listing active tasks
- Each task's full state lives in `tasks/{id}.json` (gitignored, per-developer)
- Auto-migration from v1.0 format on first run — no manual intervention needed

### New CLI
- `--task <ID>` global flag on all commands — targets a specific task
- `sdlc list-tasks` — shows all active tasks in a table
- `sdlc reset --all` — archives and resets all active tasks at once
- `sdlc show` without `--task` — shows multi-task registry when 2+ tasks active

### Dashboard
- Multi-task card grid appears above pipeline progress when 2+ tasks active
- Clickable cards switch the detail view to show that task's pipeline, timeline, and permissions
- API v1.1: `?task=ID` parameter for single-task queries, `task_states` for all states

### Backward Compatibility
- When only 1 task is active, auto-selects (identical UX to v1.0)
- All existing CLI commands work unchanged for single-task usage
- `validate_write.py` reads phase from per-task files, falls back gracefully

---

## v2.0.0 (Planned)

### Resilience
- [ ] **Forensics / Post-mortem** (`sdlc forensics`) — analyze stuck workflows, timeline patterns, phase ping-pong detection
- [ ] **Auto-chain transitions** (`sdlc auto-chain`) — auto-advance phases when conditions are met (for fast-track)

### Quality
- [ ] **Debug code scanner** — pre-commit scan for `console.log`, `var_dump`, `dd()`, raw `$_POST`
- [ ] **Portable dashboard** — Python HTTP server (no XAMPP dependency)

### Reliability
- [ ] **Backup + restore** — safe reset with auto-backup, `sdlc restore <file>`
- [ ] **File locking** — OS-level lock on `pipeline.json` for concurrent access

### Integration
- [ ] **Conversation-phase binding** — auto-link conversation ID to active sub-task
- [ ] **Multi-user awareness** — user-scoped state files (`pipeline.{user}.json`)
- [ ] **Project Hub integration** — sync phase transitions to PM tool
- [ ] **AI training data extraction** — export VIBE role selections for classifier training
