# Installation Guide

## Prerequisites

| Requirement | Version | Check Command |
|---|---|---|
| Python | 3.10+ | `python --version` |
| Git | 2.30+ | `git --version` |
| pip | Latest | `pip --version` |

## Step 1: Install LCA (Logimax Code Analyzer)

LCA is the core analysis engine. It must be installed before using the pipeline.

### Windows (PowerShell)
```powershell
cd logimax-devtools\backend\lca_core
pip install -e .
lca --version   # Should show version
```

### Linux (Bash)
```bash
cd logimax-devtools/backend/lca_core
pip install -e .
lca --version
```

### Verify Installation
```bash
lca --help
# Should list commands: init, index, impact, search, etc.
```

## Step 2: Index Your Codebase

LCA needs to index your PHP/JS files before the pipeline can analyze them.

### Windows
```powershell
cd C:\xampp\htdocs\{project}
lca init .
lca index .
```

### Linux
```bash
cd /var/www/{project}
lca init .
lca index .
```

This creates a `.lca/` directory with indexed function data. Takes 1-5 minutes depending on codebase size.

### Verify Index
```bash
lca search . "get_cash_book"
# Should return matching functions
```

## Step 3: Initialize SDLC Pipeline

If the project doesn't have `.sdlc/` yet:

```bash
python .sdlc/engine/cli.py init
```

If `.sdlc/` already exists (e.g., from a repo clone), no init needed — it's ready to use.

### Verify Pipeline
```bash
python .sdlc/engine/cli.py banner
# Should show: SDLC.5 | Phase: IDLE | No active task
```

## Step 4: Configure Project Settings

Edit `.sdlc/config.json`:

```json
{
  "project_name": "your_project_name",
  "repo": {
    "owner": "Logimax-Technologies",
    "name": "your_repo_name"
  },
  "modules": {
    "reports": {
      "controller": "admin_ret_reports",
      "model": "ret_reports_model",
      "js": "ret_reports"
    }
  }
}
```

Key fields:
- `project_name`: Used in file paths and discovery
- `repo`: GitHub repo for PR creation
- `modules`: Maps module names to their controller/model/JS files

## Step 5: (Optional) Setup RAG for Semantic Search

RAG enables semantic code search using AI embeddings. It's optional — the pipeline works without it, using LCA's deterministic analysis.

### Install Dependencies
```bash
pip install chromadb sentence-transformers
```

### Build RAG Store
```bash
# This indexes your codebase for semantic search
# Takes 5-20 minutes and requires ~2GB RAM
lca init . --rag
```

### (Optional) Start RAG Server for Fast Queries

The RAG server keeps the embedding model warm in memory for ~200ms queries instead of 40-second cold starts:

```bash
# Start in a separate terminal
python .sdlc/engine/rag_server.py

# Verify
curl http://localhost:9876/health
```

> **Note**: RAG requires ~680MB RAM for the BGE-M3 model. If your machine is memory-constrained, skip this — the pipeline will work fine with LCA-only analysis.

## Step 6: Setup Knowledge Brain (Optional)

The Knowledge Brain stores documented business rules and module-specific knowledge that helps the AI make better decisions.

```
knowledge_brain/
├── _SYSTEM/           # Cross-module system docs
│   ├── SHARED_TABLES.md
│   ├── MODULE_DEPENDENCIES.md
│   └── ...
├── Reports/           # Module-specific brain
│   └── MODULE_BRAIN.md
├── Billing/
│   └── MODULE_BRAIN.md
└── ...
```

If no brain exists, the pipeline works but scores lower on the Discovery Quality metric.

## Verification Checklist

Run these commands to verify everything is working:

```bash
# 1. LCA
lca --version                    # ✅ Shows version
lca search . "get_cash_book"     # ✅ Returns results

# 2. Pipeline
python .sdlc/engine/cli.py banner    # ✅ Shows status
python .sdlc/engine/cli.py show      # ✅ Shows task state

# 3. Discovery (full test)
python .sdlc/engine/cli.py start -t fix -m reports -s "Test discovery"
python .sdlc/engine/cli.py discover "Test query"
# ✅ Creates discovery.md in .sdlc/active/{TASK_ID}/
# Clean up: python .sdlc/engine/cli.py done
```

## Troubleshooting

| Issue | Cause | Fix |
|---|---|---|
| `lca: command not found` | LCA not installed | `pip install -e logimax-devtools/backend/lca_core` |
| `ModuleNotFoundError: chromadb` | RAG dependencies missing | `pip install chromadb sentence-transformers` (optional) |
| `FileNotFoundError: .lca/` | Codebase not indexed | `lca init . && lca index .` |
| Discovery shows `RAG: n/a` | No RAG store or model | Normal — LCA works without RAG |
| `MemoryError` during discovery | Not enough RAM for BGE-M3 | Close other apps or skip RAG |
| `python` not recognized | Python not in PATH | Windows: Install from python.org, check "Add to PATH" |

## Platform-Specific Notes

### Windows
- Use PowerShell (not cmd.exe) — the pipeline uses PowerShell-compatible commands
- Python path: typically `C:\Python3xx\python.exe`
- Project path: typically `C:\xampp\htdocs\{project}`
- Line endings: Git handles CRLF conversion automatically

### Linux
- Use bash or zsh
- Python path: typically `/usr/bin/python3`
- Project path: typically `/var/www/{project}`
- Permissions: ensure `.sdlc/engine/cli.py` is executable (`chmod +x`)

## Updating the Pipeline

When a new version is released:

```bash
# Option A: Sync from source (preserves your task data)
python .sdlc/engine/cli.py sync

# Option B: Manual update from tagged release
git fetch origin --tags
git checkout sdlc-v3.8.1 -- .sdlc/engine/cli.py .sdlc/engine/rag_server.py .sdlc/CHANGELOG.md
```
