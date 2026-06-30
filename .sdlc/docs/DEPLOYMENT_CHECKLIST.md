# SDLC Deployment Checklist — Rolling Out to Other Devs

## Current State of Deployment Tooling

You already have solid infrastructure:

| Tool | Purpose | Status |
|---|---|---|
| `setup.py init` | Copies engine files to new project | ✅ Works |
| `setup.py onboard` | Installs global SKILL.md + enforcement rules | ✅ Works |
| `setup.py check` | Validates installation | ✅ Works |
| `setup.py update` | Updates engine files without touching state | ✅ Works |
| `package.py` | Builds distributable zip (LCA index + ChromaDB + model) | ✅ Works |
| `install-package.py` | Interactive installer from zip | ✅ Works |
| `INSTALLATION.md` | Step-by-step guide | ✅ Written |
| `MCP_SETUP.md` | MCP server config | ✅ Written |
| `QUICK_START.md` | Fast onboarding | ✅ Written |
| `USER_MANUAL.md` | Full reference | ✅ Written |

## What's NOT Covered (Will Break for Other Devs)

### 🔴 Issue 1: `ENGINE_FILES` list is incomplete

`setup.py` only copies these root-level files:
```python
ENGINE_FILES = [
    'pipeline_state.py', 'validate_write.py', 'roles.json',
    'pipeline.schema.json', 'dashboard.html', 'api.php',
    'setup.py', 'sdlc.cmd', 'CHANGELOG.md', 'README.md', '.gitignore'
]
```

**Missing from ENGINE_FILES:**
```
engine/cli.py              ← THE MAIN CLI (226 KB) — not copied!
engine/validate_write.py   ← Write validation
engine/rag_server.py       ← RAG server
engine/build_docs.py       ← Doc builder
prompts/                   ← ALL 7 prompt files (code.md, verify.md, etc.)
templates/                 ← Task templates
roles/                     ← Role definitions (if separate from roles.json)
docs/                      ← Installation docs
quality/                   ← Quality metrics
memory/                    ← Anti-pattern memory
SKILL.md                   ← Skill definition
config.json                ← Project config (template needed)
steps.json                 ← Pipeline step definitions
intake_questions.json      ← Bug intake questions
```

**Impact**: `setup.py init` copies pipeline_state.py but NOT the actual CLI engine. Devs will get a broken setup.

**Fix**: Add these directories to a `ENGINE_DIRS` list and copy recursively.

---

### 🔴 Issue 2: `config.json` has your hardcoded php_path

```json
"environment": {
    "php_path": "C:/xampp/php/php.exe",  ← YOUR machine
}
```

Other devs may have PHP at:
- `C:/xampp/php/php.exe` (Windows XAMPP)
- `D:/xampp/php/php.exe` (D: drive)
- `php` (in system PATH)
- `/usr/bin/php` (Linux)

**Fix**: 
- Option A: Add auto-detect to `setup.py init` that finds PHP
- Option B: Create `config.template.json` without environment section; `setup.py init` prompts dev to set it
- Option C: Default to `php` (works if PHP is in PATH) and only need config for non-standard installs

---

### 🟡 Issue 3: `logimax-devtools` MCP not in deployment

`MCP_SETUP.md` documents `mysql-local` and `github` MCP servers but NOT the `logimax-devtools` MCP server — which provides LCA queries to subagents.

**Impact**: New devs won't have `lca_search`/`lca_investigate` MCP tools. Code prompts reference them but agents can't call them.

**Fix**: Add `logimax-devtools` MCP config to `MCP_SETUP.md` with startup command.

---

### 🟡 Issue 4: No `config.json` auto-generation

`setup.py init` copies engine files but doesn't create `config.json`. Dev must manually create it.

**Fix**: Add config generator to `setup.py init` that:
1. Auto-detects `php_path` (search common locations)
2. Auto-detects `base_url` from project directory name
3. Sets `project_name` from the directory name
4. Prompts for GitHub repo owner/name

---

### 🟡 Issue 5: Knowledge Brain not in package

`package.py` copies LCA index and ChromaDB but NOT `knowledge_brain/`.

**Impact**: Discovery quality drops — brain-based checks show "no brain available."

**Fix**: Add `knowledge_brain/` to `package.py`.

---

## Deployment Steps

### Pre-Deployment (do once, on YOUR machine)

```
Step 1: Fix setup.py ENGINE_FILES list
Step 2: Add config.json template generator  
Step 3: Update MCP_SETUP.md with logimax-devtools
Step 4: Add knowledge_brain to package.py
Step 5: Run: python .sdlc/package.py → builds distributable
Step 6: Upload zip to shared location
```

### Per-Developer Installation

```
Step 1: Download the package zip
Step 2: Double-click install.bat
Step 3: Edit .sdlc/config.json → set php_path for their machine
Step 4: Configure MCP servers (mysql-local, github, logimax-devtools)
Step 5: Verify: python .sdlc/engine/cli.py banner
```

### Post-Installation Verification

```bash
python .sdlc/engine/cli.py banner           # Shows SDLC.5 banner
lca --version                               # Shows version
python .sdlc/engine/cli.py start -t fix -m reports -s "Test"
python .sdlc/engine/cli.py discover "Test"
python .sdlc/engine/cli.py done             # Clean up
```

## Quick Fix: Update ENGINE_FILES Now

```python
ENGINE_FILES = [
    'pipeline_state.py', 'validate_write.py', 'roles.json',
    'pipeline.schema.json', 'dashboard.html', 'api.php',
    'setup.py', 'sdlc.cmd', 'CHANGELOG.md', 'README.md',
    '.gitignore', 'SKILL.md', 'steps.json',
    'intake_questions.json', 'config.json',
]

ENGINE_DIRS = [
    'engine', 'prompts', 'templates', 'docs', 'quality', 'memory',
]
```
