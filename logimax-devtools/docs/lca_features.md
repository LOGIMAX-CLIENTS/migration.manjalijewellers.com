# LCA (Logimax Code Analyzer) - Feature Documentation

> **Version**: 2.0.0 (Unified Engine)  
> **Backend**: `logimax-devtools/backend/lca_core`  
> **Base URL**: `http://127.0.0.1:8800/api/lca`

---

## 1. Codebase Indexing

Build a searchable call graph from your codebase.

| Endpoint                 | Method | Description                                  |
| ------------------------ | ------ | -------------------------------------------- |
| `/index`                 | POST   | Start indexing a directory (background task) |
| `/indexes`               | GET    | List all available indexes                   |
| `/status/{name}`         | GET    | Get index statistics                         |
| `/index/progress/{name}` | GET    | Real-time indexing progress                  |
| `/index/{name}`          | DELETE | Delete an index                              |

**Supported Languages:**

- JavaScript (`.js`, `.jsx`, `.mjs`)
- PHP (`.php`)
- Python (`.py`)

**Example Request:**

```json
POST /api/lca/index
{
  "path": "C:/project/src",
  "name": "my_project",
  "exclude_patterns": ["node_modules", "vendor"]
}
```

---

## 2. Function Search

Find functions by name pattern across indexed codebases.

| Endpoint                                  | Method | Description                |
| ----------------------------------------- | ------ | -------------------------- |
| `/search/{index_name}?q={query}&limit=20` | GET    | Search functions           |
| `/function/{index_name}/{function_name}`  | GET    | Get detailed function info |

**Function Details Include:**

- Source code snippet
- File path and line numbers
- Class name (if applicable)
- List of callers and callees

---

## 3. Call Graph Analysis

Understand function dependencies and relationships.

| Endpoint                                 | Method | Description                                   |
| ---------------------------------------- | ------ | --------------------------------------------- |
| `/callers/{index_name}/{function}`       | GET    | Get all functions that **call** this function |
| `/callees/{index_name}/{function}`       | GET    | Get all functions **called by** this function |
| `/graph/{index_name}/{function}?depth=2` | GET    | Get subgraph for visualization                |

**Graph Response:**

```json
{
  "nodes": [{ "id": "fn_name", "label": "fn_name", "group": "php" }],
  "edges": [{ "from": "caller", "to": "callee", "arrows": "to" }]
}
```

---

## 4. Impact Analysis

Assess the **blast radius** of changing a function.

| Endpoint                                  | Method | Description           |
| ----------------------------------------- | ------ | --------------------- |
| `/impact/{index_name}/{function}?depth=2` | GET    | Analyze change impact |

**Response:**

```json
{
  "function": "UserController::login",
  "risk_level": "MEDIUM",
  "total_impact": 12,
  "calls": [...],
  "level2": {...}
}
```

**Risk Levels:**

- `LOW`: 0-5 affected functions
- `MEDIUM`: 6-10 affected functions
- `HIGH`: 11-20 affected functions
- `CRITICAL`: 20+ affected functions

---

## 5. AI-Powered Explanation

Get natural language explanations of function behavior.

| Endpoint                           | Method | Description             |
| ---------------------------------- | ------ | ----------------------- |
| `/explain/{index_name}/{function}` | GET    | Generate AI explanation |

**Response:**

```json
{
  "function": "processPayment",
  "explanation": "This function handles payment processing...",
  "is_mock": true
}
```

---

## 6. Workflow / Change Request Management

Track and manage code changes through a structured workflow.

| Endpoint                           | Method | Description                     |
| ---------------------------------- | ------ | ------------------------------- |
| `/workflow/create`                 | POST   | Create a new change request     |
| `/workflow/{cr_id}/analyze`        | POST   | Run impact analysis on CR       |
| `/workflow/{cr_id}/plan`           | POST   | Generate AI implementation plan |
| `/workflow/{cr_id}/approve`        | POST   | Approve plan                    |
| `/workflow/{cr_id}`                | GET    | Get CR details                  |
| `/workflow`                        | GET    | List all CRs                    |
| `/workflow/{cr_id}`                | DELETE | Delete a CR                     |
| `/workflow/{cr_id}/task/{task_id}` | PATCH  | Update subtask status           |

**Workflow States:**

1. `draft` → Created
2. `analyzing` → Impact analysis running
3. `planned` → AI plan generated
4. `approved` → Plan approved
5. `in_progress` → Implementation started
6. `completed` → All tasks done

---

## 7. Quality Gate

Evaluate code changes against quality thresholds before merge.

| Endpoint        | Method | Description      |
| --------------- | ------ | ---------------- |
| `/quality/gate` | POST   | Evaluate changes |

**Request:**

```json
{
  "type": "BUGFIX",
  "files": ["app/Controllers/UserController.php"],
  "index_name": "default"
}
```

**Change Types:**

- `BUGFIX`: Bug fixes (strict thresholds)
- `CR`: Change Requests (balanced thresholds)
- `NR`: New Requirements (relaxed thresholds)

**Response:**

```json
{
  "type": "BUGFIX",
  "files_analyzed": 1,
  "metrics": {
    "impact_score": 5,
    "complexity_avg": 3,
    "has_tests": true
  },
  "verdict": "PASS"
}
```

---

## 8. Test Generation

Auto-generate PHPUnit test skeletons for controllers.

| Endpoint    | Method | Description            |
| ----------- | ------ | ---------------------- |
| `/gen-test` | POST   | Generate test skeleton |

**Request:**

```json
{
  "file_path": "C:/project/app/Controllers/UserController.php",
  "qualified_name": "UserController"
}
```

---

## 9. Internal Tools

Advanced analysis tools available via the engine.

### Reaper (Dead Code Detection)

Identifies functions that are never called.

```python
from lca_core.lca.tools.reaper import Reaper
reaper = Reaper("path/to/index.json")
zombies = reaper.scan()
```

### ModuleMapper (Module Dependencies)

Analyzes module-level dependency structure.

```python
from lca_core.lca.tools.module_mapper import ModuleMapper
mapper = ModuleMapper("index.json", "project_root")
deps = mapper.analyze()
```

### Cockpit Server (Local UI)

Visual web interface for code exploration.

```bash
python -m lca_core.lca.cockpit.server --index index.json --port 8000
```

---

## Architecture Overview

```
logimax-devtools/backend/
├── lca_core/                 # Unified LCA Engine
│   └── lca/
│       ├── core/             # Indexer, ImpactAnalyzer, Interfaces
│       ├── parsers/          # JS, PHP, Python parsers (tree-sitter)
│       ├── storage/          # SQLite, JSON storage
│       ├── tools/            # Reaper, ModuleMapper
│       └── cockpit/          # Local web UI
└── app/plugins/lca/          # FastAPI Plugin
    ├── router.py             # API endpoints
    ├── services/             # Business logic
    └── schemas.py            # Pydantic models
```

---

## MCP Integration

LCA exposes tools for AI agent integration via MCP:

| Tool          | Description           |
| ------------- | --------------------- |
| `lca_index`   | Index a codebase      |
| `lca_search`  | Search for functions  |
| `lca_callers` | Get function callers  |
| `lca_callees` | Get function callees  |
| `lca_impact`  | Analyze change impact |

---

_Last Updated: 2026-02-03_
