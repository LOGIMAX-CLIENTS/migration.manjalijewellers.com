# Quick Start — Your First Task in 5 Minutes

This guide walks you through fixing a bug using the SDLC pipeline. No prior experience needed.

## Prerequisites

- [Installation](INSTALLATION.md) completed
- LCA index built (`lca index .`)
- A bug to fix

---

## Step 1: Start a Task (30 seconds)

```bash
python .sdlc/engine/cli.py start \
  -t fix \
  -m reports \
  -s "Cashbook not showing opening balance" \
  --url "admin_ret_reports/cash_book_details"
```

| Flag | Meaning | Required? |
|---|---|---|
| `-t fix` | Task type: fix, feature, cr, hotfix, refactor, perf | ✅ |
| `-m reports` | Module name | ✅ |
| `-s "..."` | One-line summary | ✅ |
| `--url "..."` | Page URL (controller/method) | Optional |
| `--ticket BIL-042` | Bug tracker reference | Optional |
| `-p critical` | Priority: critical/high/medium/low | Optional |

**Output**: Creates task folder at `.sdlc/active/{TASK_ID}/`

## Step 2: Run Discovery (60 seconds)

```bash
python .sdlc/engine/cli.py discover
```

This single command:
1. Resolves the URL to controller → model
2. Searches codebase via LCA (call chains, impact analysis)
3. Searches semantically via RAG (if available)
4. Checks bug recipe library for known fixes
5. Pulls relevant Knowledge Brain context
6. Extracts inline code snippets (L1 → L2 → L3 depth)
7. Lists all database tables in the call chain
8. Grades discovery quality (0-100%)

**Output**: `discovery.md` — everything the AI needs to understand the bug.

## Step 3: Follow the Pipeline

After discovery, the pipeline guides you through structured phases:

```bash
# See what to do next
python .sdlc/engine/cli.py what-next

# When the current step is done, advance
python .sdlc/engine/cli.py step-done --note "Created requirement from discovery"
```

### Typical Phase Flow

```
REQUIREMENT  →  Write requirement.md from discovery findings
PLANNING     →  Create design.md with implementation plan
CODING       →  Implement the fix
TEST_VERIFY  →  Run tests, verify fix works
REVIEW       →  Auto-review via git diff + requirement check
COMMIT       →  Commit, create PR, archive task
```

## Step 4: Check Status Anytime

```bash
# Quick status
python .sdlc/engine/cli.py show

# Detailed banner (shows in agent responses)
python .sdlc/engine/cli.py banner
```

## Step 5: Complete the Task

After PR is merged:

```bash
python .sdlc/engine/cli.py done
```

This moves task specs from `active/` to `done/` for audit trail.

---

## Common Workflows

### Bug Fix (most common)
```bash
sdlc start -t fix -m billing -s "Tax calculation wrong for exempt items"
sdlc discover
# ... AI works through phases ...
sdlc done
```

### Change Request
```bash
sdlc start -t cr -m reports -s "Add branch filter to stock report"
sdlc discover "stock report branch filter"
# ... AI works through phases ...
sdlc done
```

### Queue Multiple Tasks
```bash
# Add to backlog (doesn't start immediately)
sdlc queue -t fix -m billing -s "Invoice total mismatch" --ticket BIL-042
sdlc queue -t fix -m reports -s "GST report missing entries" --ticket RPT-018

# Pick one to work on
sdlc pick
```

### Shorthand
You can use `sdlc` instead of `python .sdlc/engine/cli.py` if you create an alias:

**Windows (PowerShell profile)**:
```powershell
function sdlc { python .sdlc/engine/cli.py @args }
```

**Linux (.bashrc)**:
```bash
alias sdlc='python .sdlc/engine/cli.py'
```

---

## What Happens If I Get Stuck?

```bash
# See exactly what to do
sdlc what-next

# See full task state
sdlc show

# Check phase
sdlc phase
```

The pipeline is designed to prevent getting lost. Each phase has specific instructions, and `what-next` always tells you the next action.
