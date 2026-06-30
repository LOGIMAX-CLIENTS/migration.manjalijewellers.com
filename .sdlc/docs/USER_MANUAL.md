# User Manual — SDLC Pipeline Engine v3.8

## Table of Contents

1. [Overview](#overview)
2. [Concepts](#concepts)
3. [Command Reference](#command-reference)
4. [Discovery Engine](#discovery-engine)
5. [Phase System](#phase-system)
6. [Track System](#track-system)
7. [Spec Files](#spec-files)
8. [Configuration](#configuration)
9. [Integration with AI Agents](#integration-with-ai-agents)
10. [Advanced Usage](#advanced-usage)

---

## Overview

The SDLC Pipeline Engine enforces a structured workflow for AI-assisted development. Instead of letting the AI jump straight to coding, it guides through phases:

```
IDLE → REQUIREMENT → PLANNING → CODING → TEST_VERIFY → REVIEW → COMMIT
```

Each phase has:
- **Role file** — instructions the AI reads to know what to do
- **Spec file** — output artifact the AI creates (e.g., requirement.md, design.md)
- **Gate check** — conditions that must be met before advancing

---

## Concepts

### Tasks
A task is a unit of work — a bug fix, feature, change request, or refactor. Each task gets:
- A unique ID (e.g., `REP-2606181045`)
- A directory under `.sdlc/active/{TASK_ID}/`
- Spec files created during each phase

### Phases
Phases are sequential stages a task goes through. The pipeline prevents phase skipping — you can't code before planning.

### Tracks
Tracks determine how many steps a task requires:

| Track | Steps | When Used |
|---|---|---|
| EXPRESS | 4 steps | Simple, isolated bugs with clear fix |
| STANDARD | 8 steps | Most bugs and CRs |
| COMPLEX | 12 steps | Multi-module, architectural changes |

Track is auto-detected by the `discover` command based on bug complexity.

### Specs
Spec files are markdown documents created during each phase:
- `discovery.md` — Auto-generated codebase analysis
- `requirement.md` — What needs to change and why
- `design.md` — How to implement the change
- `tasks.md` — Implementation checklist
- `review.md` — Auto-generated code review

---

## Command Reference

### Task Management

#### `start` — Start a new task
```bash
python .sdlc/engine/cli.py start \
  -t fix \           # Type: fix|feature|hotfix|cr|refactor|perf
  -m reports \       # Module name
  -s "Summary" \     # One-line description
  --url "ctrl/method" \  # Optional: page URL
  --ticket BIL-042 \    # Optional: tracker ID
  -p high \             # Optional: priority
  --track standard \    # Optional: override auto-detection
  --auto \              # Optional: AI auto-generates all specs
  --force               # Optional: move active task to backlog
```

#### `queue` — Add task to backlog
```bash
python .sdlc/engine/cli.py queue \
  -t fix -m billing -s "Invoice mismatch" --ticket BIL-042
```

#### `pick` — Activate a backlog task
```bash
python .sdlc/engine/cli.py pick
# Shows numbered list of backlog tasks, select one
```

#### `show` — Show current task status
```bash
python .sdlc/engine/cli.py show
# Output: Task ID, type, module, summary, current phase, spec status
```

#### `done` — Complete and archive task
```bash
python .sdlc/engine/cli.py done
# Moves active/{TASK_ID}/ → done/{TASK_ID}/
```

#### `history` — Show completed tasks
```bash
python .sdlc/engine/cli.py history
```

### Pipeline Navigation

#### `what-next` — Show current step instruction
```bash
python .sdlc/engine/cli.py what-next
# Shows: step number, instruction, allowed actions, done-when criteria
```

This is the **core anti-drift command**. When the AI is confused or going off-track, `what-next` brings it back.

#### `step-done` — Mark current step complete
```bash
python .sdlc/engine/cli.py step-done
python .sdlc/engine/cli.py step-done --note "Created requirement from discovery"
```

Validates the step's `done_when` criteria before advancing. Fails if criteria not met.

#### `phase` — Show current phase
```bash
python .sdlc/engine/cli.py phase
```

#### `transition` — Move to next phase (with gate check)
```bash
python .sdlc/engine/cli.py transition
```

#### `banner` — Show role banner
```bash
python .sdlc/engine/cli.py banner
# Output: Phase badge + task summary (shown at start of AI responses)
```

#### `escalate` — Upgrade EXPRESS → STANDARD track
```bash
python .sdlc/engine/cli.py escalate
# Use when an "easy" bug turns out to be complex
```

### Discovery & Analysis

#### `discover` — One-command codebase analysis
```bash
python .sdlc/engine/cli.py discover
python .sdlc/engine/cli.py discover "custom search query"
```

Generates `discovery.md` with:
- URL resolution (controller → model mapping)
- RAG semantic search (if available)
- LCA impact analysis (call chains, related functions)
- Bug recipe check (known fix patterns)
- Knowledge Brain context (business rules)
- Inline code snippets (L1 → L2 → L3 depth)
- Tables in call chain
- Discovery quality score

#### `search` — Semantic search only
```bash
python .sdlc/engine/cli.py search "opening balance calculation"
```

#### `context` — Generate context summary
```bash
python .sdlc/engine/cli.py context
```

### Review & Quality

#### `review` — Auto-generate code review
```bash
python .sdlc/engine/cli.py review
# Compares git diff against requirement.md, generates review.md
```

#### `metrics` — Show pipeline metrics
```bash
python .sdlc/engine/cli.py metrics
# Shows: tasks completed, average phase times, discovery scores
```

### Recipes

#### `enrich` — Auto-generate bug recipe from completed task
```bash
python .sdlc/engine/cli.py enrich
# Creates recipe from task artifacts, pushes to bug-recipes repo
```

### Administration

#### `init` — Initialize pipeline in new project
```bash
python .sdlc/engine/cli.py init
```

#### `sync` — Update framework from source
```bash
python .sdlc/engine/cli.py sync
```

---

## Discovery Engine

The `discover` command is the pipeline's intelligence layer. It answers: *"What code is involved in this bug?"*

### How It Works

```
Input: Bug description + optional URL
  ↓
Step 1: URL Resolution
  "admin_ret_reports/cash_book_details"
  → Controller: admin_ret_reports.php
  → Method: cash_book_details()
  ↓
Step 2: RAG Semantic Search (if available)
  → Finds files/functions semantically related to the description
  → Results scored: strong (<0.30), good (0.30-0.40), weak (>0.40)
  ↓
Step 3: LCA Analysis
  → Function search (keyword matching)
  → Impact analysis (call chain from entry point)
  → Related function search
  ↓
Step 4: Code Extraction
  → L1: Direct model methods from call chain (full code)
  → L2: Cross-module methods called by L1 (full code)
  → L3: Sub-calls within L2 matching keywords (full code)
  → Smart excerpting for functions >100 lines
  ↓
Step 5: Context Enrichment
  → Brain context (business rules from Knowledge Brain)
  → Tables in call chain (with keyword highlighting)
  → JS AJAX stub extraction
  → Class constants and properties
  ↓
Step 6: Quality Score
  → 8 signals scored out of available maximum
  → Grade: HIGH (≥80%), MEDIUM (≥50%), LOW (<50%)
```

### Discovery Quality Score

| Signal | Max Points | What It Measures |
|---|---|---|
| RAG results | 20 | Semantic search quality (only if RAG available) |
| LCA analysis | 15 | Call chain and impact depth |
| Code snippets | 20 | Inline code extracted |
| L2 methods | 10 | Cross-module resolution |
| L3 depth | 10 | Sub-call tracing |
| Brain context | 10 | Knowledge Brain matches |
| Tables | 5 | DB tables identified |
| Recipe | 10 | Known fix pattern found (only if recipes repo exists) |

Score is proportional to available infrastructure — clients without RAG or recipes aren't penalized.

---

## Phase System

### Phase Details

| Phase | Role File | Creates | Gate Check |
|---|---|---|---|
| IDLE | IDLE.md | — | — |
| REQUIREMENT | REQUIREMENT.md | requirement.md | Requirement spec exists |
| PLANNING | PLANNING.md | design.md, tasks.md | Design spec exists |
| CODING | CODING.md | Source code changes | All tasks.md items checked |
| TEST_VERIFY | TEST_DESIGN.md | Test results | Tests pass |
| REVIEW | REVIEW.md | review.md | Review verdict: PASS |
| COMMIT | COMMIT.md | Git commit + PR | PR created |

### Phase Enforcement

The pipeline prevents out-of-phase actions:

| Action | Allowed In | Blocked In |
|---|---|---|
| Create source files | CODING | REQUIREMENT, PLANNING |
| Run tests | TEST_VERIFY | REQUIREMENT |
| Create PR | COMMIT | All others |
| Read source files | All phases | — |
| Create spec files | Matching phase | — |

---

## Track System

### EXPRESS Track (4 steps)
For simple, isolated bugs with obvious fixes.
```
1. Read discovery.md
2. Create requirement.md
3. Code the fix
4. Review + commit
```

### STANDARD Track (8 steps)
For typical bugs and change requests.
```
1. Run discovery
2. Analyze discovery results
3. Create requirement.md
4. Create design.md + tasks.md
5. Code the fix
6. Test the fix
7. Review code
8. Commit + PR
```

### COMPLEX Track (12 steps)
For multi-module, architectural changes.
```
1-2.  Discovery + deep analysis
3-4.  Requirement + impact assessment
5-6.  Design + architecture review
7-8.  Code implementation
9-10. Testing + regression
11-12. Review + staged commit
```

### Track Auto-Detection

The `discover` command auto-selects track based on:
- Number of modules affected
- Call chain depth
- Presence of cross-module dependencies
- Complexity of the bug description

Override with `--track express|standard|complex`.

---

## Spec Files

All spec files live in `.sdlc/active/{TASK_ID}/`:

| File | Created By | Purpose |
|---|---|---|
| `metadata.txt` | `start` command | Task metadata |
| `discovery.md` | `discover` command | Codebase analysis |
| `requirement.md` | AI (REQUIREMENT phase) | What to fix and why |
| `design.md` | AI (PLANNING phase) | How to fix it |
| `tasks.md` | AI (PLANNING phase) | Implementation checklist |
| `review.md` | `review` command | Code review results |

### Check Spec Status
```bash
python .sdlc/engine/cli.py spec-status
```

### Create Spec Manually
```bash
python .sdlc/engine/cli.py create-spec requirement
```

---

## Configuration

### config.json

Located at `.sdlc/config.json`:

```json
{
  "project_name": "etail_v3",
  "repo": {
    "owner": "Logimax-Technologies",
    "name": "etail_development_src"
  },
  "lca": {
    "devtools_path": "logimax-devtools",
    "rag_store_path": ".rag_store"
  },
  "brain_path": "knowledge_brain",
  "recipe_repo": "LOGIMAX-CLIENTS/bug-recipes",
  "modules": {
    "reports": {
      "controller": "admin_ret_reports",
      "model": "ret_reports_model",
      "js": "ret_reports"
    }
  }
}
```

### steps.json

Defines the step sequences for each track (EXPRESS/STANDARD/COMPLEX). Typically not edited by users.

### .gitignore

Pipeline state files that should NOT be committed:
```
pipeline.json       # Task state (changes every step-done)
active/             # In-progress task specs
backlog/            # Queued tasks  
```

Files that SHOULD be committed:
```
engine/             # Pipeline code
roles/              # Phase instructions
config.json         # Project settings
CHANGELOG.md        # Version history
```

---

## Integration with AI Agents

### How the AI Uses the Pipeline

1. **Bootstrap**: At conversation start, AI reads `SKILL.md` → runs `banner`
2. **Phase routing**: AI reads the role file for the current phase
3. **Anti-drift**: If AI goes off-track, `what-next` refocuses it
4. **Spec creation**: AI creates spec files during each phase
5. **Advancement**: AI calls `step-done` when criteria met

### What the AI Sees

```
╔══════════════════════════════════════════════════════╗
║  SDLC.5 │ 🔧 CODING │ Task: REP-2606181045     ║
║  Fix: Cashbook not showing opening balance           ║
║  Step 5/8: Implement the fix per design.md           ║
╚══════════════════════════════════════════════════════╝
```

### Override Protocol

If you need the AI to do something outside its current phase:
1. AI will state the conflict
2. AI asks: "Override phase lock?"
3. You confirm → AI proceeds
4. Returns to pipeline after override

---

## Advanced Usage

### RAG Server (Optional)

For fast semantic search, run the RAG embedding server:

```bash
# Terminal 1: Start server (keeps model warm)
python .sdlc/engine/rag_server.py

# Terminal 2: Discovery now uses server (~200ms vs 40s)
python .sdlc/engine/cli.py discover "query"
```

Server endpoints:
- `POST /query` — Full RAG search
- `POST /embed` — Embedding only
- `GET /health` — Status check

### Backlog Management

```bash
# Add multiple tasks
sdlc queue -t fix -m billing -s "Bug 1" -p high
sdlc queue -t fix -m reports -s "Bug 2" -p medium
sdlc queue -t cr  -m estimation -s "Change 3" -p low

# View backlog
cat .sdlc/backlog.md

# Pick highest priority
sdlc pick
```

### Clean Branch Guard

Before creating a PR, the COMMIT phase checks if your branch has accumulated drift:

```bash
git diff --stat origin/PRODUCTION..HEAD
```

If extra files appear (not from your task), the pipeline instructs you to create a clean branch:

```bash
git checkout -b hotfix/{TASK_ID}-clean origin/PRODUCTION
git checkout {working_branch} -- {your_files_only}
git commit && git push
```

### Multi-Project Deployment

To deploy the pipeline to a new project:

```bash
# Copy framework files
cp -r .sdlc/engine/ /path/to/new-project/.sdlc/engine/
cp -r .sdlc/roles/ /path/to/new-project/.sdlc/roles/
cp .sdlc/steps.json /path/to/new-project/.sdlc/

# Initialize
cd /path/to/new-project
python .sdlc/engine/cli.py init

# Configure
# Edit .sdlc/config.json for the new project
```
