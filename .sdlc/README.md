# SDLC Pipeline Engine v1.1.0
> AI-powered, role-locked development workflow for Logimax ERP projects

---

## 🚀 New Developer Setup (2 minutes)

You got this file because you pulled the repo. The pipeline engine is already here. You just need to activate it.

### Step 1: Install the global skill (ONE TIME per machine)

```bash
cd c:\xampp\htdocs\your_project
python .sdlc\setup.py onboard
```

This installs a tiny instruction file to `%USERPROFILE%\.gemini\config\skills\sdlc-pipeline\SKILL.md` that tells the AI agent "hey, this project has a pipeline — follow its rules."

### Step 2: Verify

```bash
python .sdlc\pipeline_state.py --version
# → sdlc v1.1.0

python .sdlc\pipeline_state.py show
# → Shows IDLE state, no active task

python .sdlc\setup.py check
# → Shows ✅ for all files
```

### Step 3: Start working

Open any conversation in the project. The AI agent auto-detects `.sdlc/` and enters role mode. **That's it.**

> **⚠️ If you switch machines**, run `python .sdlc\setup.py onboard` again on the new machine. The repo files are already there from git — only the global skill needs reinstalling.

---

## What Is This?

The SDLC Pipeline is a **role-locked workflow engine** that controls how the AI agent works. Instead of letting the AI immediately start editing code when you say "fix this bug", the pipeline forces it through structured phases — just like a real development team where the analyst, architect, developer, and tester are different roles.

### Without Pipeline
```
You: "Fix the discount bug"
AI: *immediately edits code* → misses edge cases, breaks something else, no tests
```

### With Pipeline
```
You: "Fix the discount bug"
AI: → Asks tough questions (DISCUSS)
    → Writes requirements with acceptance criteria (REQUIREMENT)
    → Designs implementation plan — you approve it (PLANNING)
    → Writes tests FIRST that fail (TEST_DESIGN — TDD red)
    → Implements the fix, makes tests pass (CODING — TDD green)
    → Runs full test suite (TEST_EXECUTION)
    → Reviews its own code for security/quality (REVIEW)
    → Commits with validation hook (COMMIT)
```

**Each phase has different:**
- 🎭 **Persona** — the AI literally changes personality
- 📋 **Instructions** — what it should focus on
- 🚫 **Constraints** — what it CANNOT do
- 📁 **File permissions** — what files it can modify (ENFORCED, not suggested)

---

## How It Works — Under The Hood

### Files in This Directory

```
.sdlc/                          ← THIS DIRECTORY
│
│  COMMITTED (you get these from git pull):
├── pipeline_state.py           ← The engine — 2000+ lines, all CLI commands
├── roles.json                  ← Role definitions for all 10 phases
├── validate_write.py           ← File permission enforcer
├── pipeline.schema.json        ← State template
├── dashboard.html              ← Visual dashboard (browser)
├── api.php                     ← Dashboard backend
├── setup.py                    ← Setup + onboarding script
├── sdlc.cmd                    ← CLI shortcut (type 'sdlc' instead of 'python .sdlc/pipeline_state.py')
├── CHANGELOG.md                ← Version history
├── README.md                   ← This file
├── .gitignore                  ← Protects your local state from being committed
│
│  GITIGNORED (your local working files — NOT shared):
├── pipeline.json               ← YOUR current task state
├── handoff.md                  ← Pause/resume context (auto-generated)
├── tasks/                      ← Sub-task AND multi-task state files
└── history/                    ← Archived completed tasks
```

### What Happens When You Open a Conversation

```
1. AI agent starts
2. Checks: does .sdlc/roles.json exist?  → YES
3. Runs: python .sdlc/pipeline_state.py get-prompt
4. Gets ~40 focused lines telling it:
   - What role it is (e.g., "You are a Business Analyst")
   - What to do (e.g., "Write acceptance criteria")
   - What NOT to do (e.g., "DO NOT write code")
   - What files it can touch (e.g., "artifacts only")
   - What knowledge brains to read
   - Decisions already made (so it doesn't re-ask)
5. Shows role banner: 📋 [Business Analyst] Phase: REQUIREMENT | Task: BIL-042
6. Follows this role for the entire conversation
```

---

## Three Modes — Pick the Right One

### 1. Full Pipeline (for important work)

Use when: Anything touching money, data, security, or 3+ files.

```bash
python .sdlc/pipeline_state.py start-task -t fix -m billing -s "Discount rounding bug" --ticket BIL-042
```

Goes through all phases: DISCUSS → REQUIREMENT → PLANNING → TEST_DESIGN → CODING → TEST_EXECUTION → REVIEW → COMMIT → PR

### 2. Fast-Track (for trivial fixes)

Use when: Typo fix, one-line change, formatting.

```bash
python .sdlc/pipeline_state.py fast-track typo billing "Fix function name typo"
```

Shortened pipeline: CODING → REVIEW → COMMIT (max 3 files)

### 3. VIBE Mode (for exploration)

Use when: Investigating, brainstorming, ad-hoc work.

```bash
python .sdlc/pipeline_state.py set-phase VIBE
```

AI auto-selects from 8 expert roles based on what you say:

| You Say | AI Becomes | Can Modify |
|---|---|---|
| "Why is this broken?" | 🔍 Investigator | Nothing (read-only) |
| "Fix this bug" | 🐛 Bug Fixer | Source code |
| "Add a new feature" | 🏗️ Builder | Source code |
| "How should we design this?" | 📐 Architect | Nothing (read-only) |
| "Review this code" | 🔬 Code Reviewer | Nothing (read-only) |
| "What's in this table?" | 🗄️ Database Analyst | Nothing (SELECT only) |
| "Document this module" | 📝 Documenter | .md files only |
| "Write tests for this" | 🧪 Tester | Test files only |

---

## Full Pipeline Walkthrough (Example)

**Scenario:** "Discount calculation shows ₹99.999 instead of ₹100.00"

### Phase 1: DISCUSS → 🗣️ Technical Advisor
- Asks: "Is this all discount types or just percentage?" "UI issue or DB issue?"
- Records decisions: `sdlc add-decision bug_location "JS calculation" --reason "DB stores correct value"`
- **Can:** Read code, grep, search, query DB
- **Cannot:** Modify ANY file

### Phase 2: REQUIREMENT → 📋 Business Analyst
- Writes user story + acceptance criteria
- Previously resolved decisions carry forward (not re-asked)
- **Can:** Write analysis artifacts
- **Cannot:** Write code

### Phase 3: PLANNING → 📐 Solution Architect
- Traces code paths, identifies files to change, estimates complexity
- **You must approve** the plan before proceeding
- **Can:** Write implementation plans
- **Cannot:** Write code

### Phase 4: TEST_DESIGN → 🧪 Test Engineer (TDD Red Phase)
- Writes tests based on acceptance criteria
- Tests MUST FAIL (they verify behavior that doesn't exist yet)
- **Can:** Write test files only
- **Cannot:** Modify source code

### Phase 5: CODING → 💻 Senior Developer (TDD Green Phase)
- Implements exactly what the plan specified
- Goal: make all tests from Phase 4 pass
- **Can:** Modify source code
- **Cannot:** Modify test files

### Phase 6: TEST_EXECUTION → ✅ QA Lead
- Runs full test suite + regression + exploratory
- **Can:** Run tests only (read-only)
- **Cannot:** Modify any files

### Phase 7: REVIEW → 🔍 Code Reviewer
- Reviews every changed line for security, performance, business logic
- Verdict: PASS / FAIL
- **Can:** Analyze only (read-only)
- **Cannot:** Modify any files

### Phase 8: COMMIT → 📦 Release Engineer
- Pre-commit hook validates: correct phase, planned files, no debug code
- **Can:** Git operations only

---

## Decision Tracking

When the AI resolves a question during discussion, it records it:

```bash
python .sdlc/pipeline_state.py add-decision rounding_approach "toFixed(2) on final amount" --reason "prevents drift"
python .sdlc/pipeline_state.py add-decision affected_modules "billing only" --reason "estimation uses different function"
```

These decisions **automatically show up** in every subsequent phase's instructions:
```
RESOLVED DECISIONS (DO NOT re-ask):
  [DISCUSS] rounding_approach = toFixed(2) on final amount
  [DISCUSS] affected_modules = billing only
```

**Why this matters:** Without this, the AI in PLANNING phase would re-ask "which modules are affected?" — wasting your time. Decisions persist across phases AND across conversations.

---

## Multi-Conversation Workflow

When a conversation gets long (AI warns at 60% context):

### Pause (end of current session)
```bash
python .sdlc/pipeline_state.py pause --reason "Context getting long"
```

Creates `.sdlc/handoff.md` with: current phase, all decisions, progress, blockers, recent activity.

### Resume (start of next session)
The AI automatically detects the handoff file and shows:
```
⚠️ HANDOFF FILE DETECTED: .sdlc/handoff.md
→ Read it for context from the previous session
```

After reading:
```bash
python .sdlc/pipeline_state.py clear-handoff
```

---

## Sub-Tasks (for big changes)

Split a large task across multiple conversations:

```bash
python .sdlc/pipeline_state.py add-subtask model -s "Fix model calculation"
python .sdlc/pipeline_state.py add-subtask js -s "Fix JS display"
python .sdlc/pipeline_state.py add-subtask test -s "Write test coverage"

python .sdlc/pipeline_state.py subtasks
# Shows: model (pending), js (pending), test (pending)

python .sdlc/pipeline_state.py activate-subtask model
# Now working on model subtask

python .sdlc/pipeline_state.py complete-subtask model
# Mark done, move to next
```

---

## Multi-Task Parallel Work (v1.1)

Multiple developers (or one developer in multiple conversations) can run independent tasks simultaneously:

### Start Multiple Tasks
```bash
# Chat A starts a billing fix
python .sdlc/pipeline_state.py start-task -t fix -m billing -s "Discount rounding" --ticket BIL-042

# Chat B starts a report feature (doesn't touch BIL-042)
python .sdlc/pipeline_state.py start-task -t feature -m reports -s "GST export" --ticket RPT-018
```

### See All Active Tasks
```bash
python .sdlc/pipeline_state.py show
# Shows registry table:
#   ╔══════════════════════════════════════╗
#   ║ *BIL-042  💻 CODING    billing      ║
#   ║  RPT-018  📋 REQUIRE   reports      ║
#   ╚══════════════════════════════════════╝

python .sdlc/pipeline_state.py --task BIL-042 show     # Detail view for BIL-042
python .sdlc/pipeline_state.py --task RPT-018 get-prompt # Prompt for RPT-018
```

### How It Works
- Each task is a separate file: `tasks/BIL-042.json`, `tasks/RPT-018.json`
- `pipeline.json` is now a lightweight **registry** listing all active tasks
- When only 1 task exists, it auto-selects (identical to v1.0)
- When 2+ tasks exist, you must specify `--task <ID>` for commands that modify state
- Dashboard shows clickable task cards for quick switching

### Reset
```bash
python .sdlc/pipeline_state.py --task BIL-042 reset   # Archive just BIL-042
python .sdlc/pipeline_state.py reset --all             # Archive everything
```

## Dashboard

If your project runs on XAMPP:
```
http://localhost/your_project/.sdlc/dashboard.html
```
Auto-refreshes every 10 seconds. Shows current phase, task, timeline, sub-tasks.

---

## CLI Quick Reference

```bash
# ────── Info ──────
sdlc --version                                    # Check version
sdlc show                                         # Status (auto: single or multi)
sdlc list-tasks                                   # All active tasks
sdlc suggest-next                                 # What should I do next?
sdlc metrics                                      # Phase duration stats

# ────── Task Lifecycle ──────
sdlc start-task -t fix -m billing -s "desc" --ticket BIL-042
sdlc --task BIL-042 set-phase CODING              # Move to phase (with --task)
sdlc fast-track hotfix billing "one-line fix"     # Shortened pipeline
sdlc reset                                        # Archive + clear current task
sdlc reset --all                                  # Archive ALL tasks

# ────── Role Intelligence ──────
sdlc get-prompt                                   # Current role instructions
sdlc --task BIL-042 get-prompt                    # Prompt for specific task
sdlc get-prompt --sub-role bug_fixer              # VIBE sub-role

# ────── Decision Tracking ──────
sdlc add-decision <key> <value> --reason <why>    # Record decision
sdlc decisions                                    # List all decisions

# ────── Context Management ──────
sdlc pause --reason "reason"                      # Create handoff
sdlc resume                                       # View handoff
sdlc clear-handoff                                # Clean up

# ────── Sub-Tasks ──────
sdlc add-subtask model -s "Fix model layer"
sdlc subtasks
sdlc activate-subtask model
sdlc complete-subtask model
```

> **Shortcut:** Instead of `python .sdlc\pipeline_state.py`, you can use `.sdlc\sdlc.cmd` if you add `.sdlc/` to your PATH.

---

## When to Use What

| Situation | Use This |
|---|---|
| Bug touching money (discounts, taxes, totals) | **Full pipeline** |
| Cross-module change (billing + estimation + GST) | **Full pipeline** |
| Database schema change | **Full pipeline** |
| Security-sensitive code (auth, input handling) | **Full pipeline** |
| 3+ files changing | **Full pipeline** |
| One-line typo or formatting fix | **Fast-track** |
| Quick investigation or question | **VIBE mode** or just ask (no task needed) |
| Brainstorming / architecture discussion | **VIBE mode** (Architect sub-role) |

---

## Troubleshooting

### "The AI isn't following the pipeline"
```bash
python .sdlc/setup.py check       # Verify all files exist
python .sdlc/setup.py onboard     # Reinstall global skill
```

### "I want to skip a phase"
Tell the AI: "Skip to CODING phase." It will log the skip with a warning, but it's allowed.

### "I accidentally reset my task"
Check `history/` — completed tasks are archived there.

### "The dashboard shows nothing"
Make sure `api.php` can read `pipeline.json`. Both need to be in `.sdlc/`.

---

## Version History

See [CHANGELOG.md](CHANGELOG.md) for full release notes and the v2.0 roadmap.
