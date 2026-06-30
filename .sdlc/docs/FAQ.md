# FAQ — Frequently Asked Questions

## General

### What is the SDLC Pipeline?
It's a structured workflow engine that guides AI coding assistants through phases (discover → plan → code → test → review → commit) instead of letting them jump straight to coding. Think of it as "guardrails for AI developers."

### Do I need to learn all the commands?
No. For daily use, you need 4 commands:
```bash
sdlc start -t fix -m {module} -s "{summary}"   # Start
sdlc discover                                     # Analyze
sdlc step-done                                    # Advance
sdlc done                                         # Finish
```

### Does this replace manual coding?
No. The pipeline guides the AI agent — a human developer still reviews and approves all changes. The pipeline just ensures the AI follows a structured process.

### Can I use this without an AI agent?
Technically yes — you can use `discover` for codebase analysis and `step-done` for checklist tracking. But it's designed for AI-assisted workflows.

---

## Installation

### Q: "lca: command not found"
LCA is not installed or not in PATH.
```bash
cd logimax-devtools/backend/lca_core
pip install -e .
# Verify:
lca --version
```

### Q: "ModuleNotFoundError: No module named 'chromadb'"
RAG dependencies are missing. RAG is **optional** — you can skip it:
```bash
# To install (optional):
pip install chromadb sentence-transformers
```

### Q: "FileNotFoundError: .lca/reports.json"
Your codebase isn't indexed yet:
```bash
lca init .
lca index .
```

### Q: Python version issues
The pipeline requires Python 3.10+. Check your version:
```bash
python --version
# If you have multiple Pythons:
python3 --version
py -3 --version    # Windows
```

### Q: Can I use this on Linux?
Yes. All commands work on both Windows (PowerShell) and Linux (bash). The only difference is file paths (`C:\xampp\htdocs\` vs `/var/www/`).

---

## Discovery

### Q: What does "RAG: n/a" mean in the quality score?
RAG (semantic search) is not configured on your machine. This is **normal and not a problem**. The pipeline works fine with LCA-only analysis. Your quality score is calculated proportionally — you won't be penalized.

### Q: Discovery quality score is LOW. What do I do?
Check which signals are missing:
- **LCA: 0** → Run `lca index .` to rebuild the index
- **Brain: 0** → Module brain doesn't exist yet (build with `/build-module-brain`)
- **Code snippets: 0** → Controller/model file not found (check config.json modules section)
- **Tables: 0** → LCA index might be outdated

### Q: Discovery takes 40+ seconds
If RAG is loading the BGE-M3 model from cold start:
```bash
# Option A: Start the RAG server (keeps model warm)
python .sdlc/engine/rag_server.py
# Subsequent discovers will be ~200ms

# Option B: Just wait — first discover is slow, it's normal
```

### Q: Can I run discover without starting a task first?
No — `discover` writes results to the active task's directory. Start a task first:
```bash
sdlc start -t fix -m reports -s "Quick investigation"
sdlc discover "your query"
sdlc done    # Clean up if you just wanted the analysis
```

### Q: Discovery found irrelevant results
The RAG semantic search can return "weak" matches (similarity >0.40). These are shown for completeness but are low-confidence. Focus on the **LCA call chain** results — those are deterministic and precise.

---

## Pipeline Flow

### Q: I'm stuck — what do I do?
```bash
# See your current step and what's expected
sdlc what-next

# See full task state
sdlc show

# See spec file status
sdlc spec-status
```

### Q: "step-done" won't advance — says criteria not met
Each step has `done_when` criteria. Check what's missing:
```bash
sdlc what-next
# Look at "Done when:" section — it tells you what file or condition is required
```

Common causes:
- Spec file not created yet (e.g., `requirement.md` needed but not written)
- Tasks.md has unchecked items
- Review verdict is not PASS

### Q: I need to skip a phase — the bug is obvious
Use the EXPRESS track to reduce steps:
```bash
sdlc start -t fix -m reports -s "Simple typo fix" --track express
# Only 4 steps instead of 8
```

Or escalate if already started:
```bash
# If current track is too restrictive, you can't de-escalate
# But you can override with agent confirmation
```

### Q: The AI went off-track and is doing something else
Run `what-next` — it will show the AI exactly what it should be doing. The pipeline's anti-drift system resets the AI's focus.

### Q: Can I work on multiple tasks simultaneously?
No — one active task at a time. But you can:
```bash
# Park current task in backlog
sdlc start -t fix -m billing -s "Urgent bug" --force
# This moves the current task to backlog and starts the new one

# Later, pick the old task back
sdlc done    # Finish urgent bug
sdlc pick    # Shows backlog, pick the parked task
```

---

## Git & PR

### Q: PR shows extra files I didn't change
Your branch has diverged from the target. Use the Clean Branch Guard:
```bash
# Create fresh branch off target
git checkout -b hotfix/{TASK_ID}-clean origin/PRODUCTION

# Cherry-pick only your files
git checkout {working_branch} -- {your_files}

# Commit and PR from clean branch
git commit -m "fix: ..."
git push -u origin hotfix/{TASK_ID}-clean
```

### Q: Which branch should I PR to?
Follow the promotion order:
```
Feature branch → Dev (Retail_1.1.1.0001) → QA → support → PRODUCTION
```
**Exception**: `hotfix/*` branches can PR directly to PRODUCTION.

### Q: Can I commit directly to PRODUCTION?
No. Never push directly to environment branches. Always create a PR.

---

## Configuration

### Q: How do I add a new module?
Edit `.sdlc/config.json`:
```json
{
  "modules": {
    "new_module": {
      "controller": "admin_new_module",
      "model": "new_module_model",
      "js": "new_module"
    }
  }
}
```

### Q: How do I update the pipeline to a new version?
```bash
# Option A: Sync command
sdlc sync

# Option B: Manual from tag
git fetch origin --tags
git checkout sdlc-v3.8.1 -- .sdlc/engine/cli.py .sdlc/engine/rag_server.py
```

### Q: What files should NOT be committed?
```
.sdlc/pipeline.json    # Task state (gitignored)
.sdlc/active/           # In-progress specs (gitignored)
.sdlc/backlog/           # Queued tasks (gitignored)
```

### Q: What files SHOULD be committed?
```
.sdlc/engine/           # Pipeline code
.sdlc/roles/            # Phase instructions
.sdlc/config.json       # Project settings
.sdlc/steps.json        # Track definitions
.sdlc/CHANGELOG.md      # Version history
.sdlc/done/             # Completed task archive
```

---

## Troubleshooting

### Q: "No active task" error
Start a task first:
```bash
sdlc start -t fix -m reports -s "Description"
```

### Q: Memory errors during discovery
The BGE-M3 embedding model needs ~2GB RAM. Options:
1. Close other applications
2. Use the RAG server (loads model once): `python .sdlc/engine/rag_server.py`
3. Skip RAG — discovery works without it (uses LCA only)

### Q: Discovery shows "0 results" for everything
1. Check LCA index exists: `ls .lca/`
2. Rebuild if needed: `lca index .`
3. Check the module name matches config.json
4. Try a different search query

### Q: "Permission denied" on Linux
```bash
chmod +x .sdlc/engine/cli.py
# Or run with python explicitly:
python3 .sdlc/engine/cli.py banner
```

### Q: Pipeline.json got corrupted
Delete and restart:
```bash
rm .sdlc/pipeline.json
sdlc banner    # Creates fresh state
```

No task data is lost — specs are in `active/` and `done/` directories.
