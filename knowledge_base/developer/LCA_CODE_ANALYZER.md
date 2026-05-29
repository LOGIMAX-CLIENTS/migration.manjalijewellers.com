# Logimax Code Analyzer (LCA)

> Static analysis tool for PHP/JavaScript codebases with call graph visualization and impact analysis.

---

## Quick Start

```bash
cd logimax-code-analyzer

# Index a codebase
uv run lca index ../admin/application -o admin_index.json

# Search for functions
uv run lca search "saveEstimation" -i admin_index.json

# Analyze impact of changes
uv run lca impact "calculateTotal" -i admin_index.json

# Start web UI
uv run lca ui -i admin_index.json
```

---

## CLI Commands

| Command                   | Description                              |
| ------------------------- | ---------------------------------------- |
| `lca index <path>`        | Index a file or directory                |
| `lca search <query>`      | Search for functions by name             |
| `lca impact <function>`   | Analyze change impact (callers/callees)  |
| `lca ui`                  | Launch interactive web cockpit           |
| `lca crossref <search>`   | Cross-language reference search (JS→PHP) |
| `lca reaper`              | Find dead (uncalled) code                |
| `lca modules`             | Analyze module dependencies              |
| `lca watch <path>`        | Watch for file changes and auto-reindex  |
| `lca gen-test <function>` | Generate test scaffolding                |
| `lca serve`               | Start MCP server for AI integration      |

---

## MCP Integration (Antigravity)

LCA provides MCP tools for AI-assisted development:

### Available Tools

| Tool                       | Purpose                                |
| -------------------------- | -------------------------------------- |
| `mcp_lca_load_index`       | Load an index file                     |
| `mcp_lca_index_module`     | Index a module on-demand               |
| `mcp_lca_search_functions` | Search for functions                   |
| `mcp_lca_get_callers`      | Get all callers of a function          |
| `mcp_lca_get_callees`      | Get all functions called by a function |
| `mcp_lca_get_impact`       | Full impact analysis                   |

### Usage Example

```python
# Load the master index
mcp_lca_load_index(index_path="logimax-code-analyzer/master_index.json")

# Search for a function
mcp_lca_search_functions(query="saveEstimation")

# Find what calls this function
mcp_lca_get_callers(function_name="saveEstimation")

# Find what this function calls
mcp_lca_get_callees(function_name="saveEstimation")

# Full impact analysis
mcp_lca_get_impact(function_name="saveEstimation", depth=2)
```

---

## LCA Cockpit (Web UI)

Interactive browser-based interface for code visualization.

### Starting the Cockpit

```bash
uv run lca ui -i master_index.json
# Opens at http://localhost:8765
```

### Features

| Feature                 | Description                                 |
| ----------------------- | ------------------------------------------- |
| **Network Graph**       | Interactive force-directed call graph       |
| **Mind Map View**       | Hierarchical function relationship view     |
| **Impact Analysis**     | Visual caller/callee tree with risk levels  |
| **Dead Code Detection** | List of unused functions                    |
| **Search**              | Real-time function search with autocomplete |

### Keyboard Shortcuts

| Key      | Action       |
| -------- | ------------ |
| `/`      | Focus search |
| `Escape` | Close panels |
| `+`/`-`  | Zoom in/out  |
| `R`      | Reset view   |

---

## Index Management

### Creating Indexes

```bash
# Full codebase index
uv run lca index ../admin/application -o admin_index.json --recursive

# Specific module
uv run lca index ../admin/application/controllers -o controllers_index.json

# Combined master index
uv run lca index ../admin -o master_index.json
```

### Index Structure

```json
{
  "module": "admin",
  "indexed_at": "2026-01-31T12:00:00Z",
  "version": "1.0",
  "functions": [
    {
      "name": "saveEstimation",
      "file": "controllers/estimation.php",
      "line": 42,
      "language": "php",
      "calls": ["validate", "insert_data"]
    }
  ],
  "calls": [
    {
      "source": "saveEstimation",
      "target": "validate",
      "line": 45,
      "file": "controllers/estimation.php"
    }
  ]
}
```

---

## Impact Analysis

### Understanding Risk Levels

| Risk       | Callers | Description                              |
| ---------- | ------- | ---------------------------------------- |
| **High**   | 10+     | Core function, changes affect many areas |
| **Medium** | 3-9     | Moderate impact, careful testing needed  |
| **Low**    | 0-2     | Isolated function, safe to modify        |

### Command Line

```bash
# Basic impact
uv run lca impact "saveEstimation" -i master_index.json

# With depth (how many levels of callers/callees)
uv run lca impact "saveEstimation" -i master_index.json --depth 3
```

### Output Example

```
Function: saveEstimation
File: admin/application/controllers/estimation.php:42
Risk Level: HIGH (12 callers)

Called By (12):
  ├── estimation.php:save_draft (line 78)
  ├── estimation.php:convert_to_bill (line 112)
  ├── api/estimation_api.php:post_save (line 45)
  └── ... and 9 more

Calls (5):
  ├── estimation_model.php:insert_estimation (line 50)
  ├── estimation_model.php:update_estimation (line 52)
  └── ... and 3 more
```

---

## Cross-Language Analysis

LCA can track calls between JavaScript and PHP (e.g., AJAX calls).

```bash
# Find all JS functions that call PHP endpoints
uv run lca crossref "estimation" -i master_index.json
```

### How It Works

1. Parses JavaScript `fetch()`, `$.ajax()`, `axios` calls
2. Extracts endpoint URLs
3. Maps to PHP controller methods
4. Creates cross-language call relationships

---

## Dead Code Reaper

Find unused functions that can be safely removed.

```bash
uv run lca reaper -i master_index.json
```

### Output

```
Dead Code Report
================
Found 23 potentially unused functions:

controllers/deprecated.php:
  - oldSaveMethod (line 45)
  - legacyExport (line 89)

models/unused_model.php:
  - entire file appears unused (0 references)

Estimated lines removable: ~450
```

---

## Watch Mode

Auto-reindex on file changes during development.

```bash
# Watch entire application
uv run lca watch ../admin/application -i admin_index.json

# Watch specific directory
uv run lca watch ../admin/application/controllers
```

---

## Test Generation

Generate test scaffolding based on function signatures and dependencies.

```bash
uv run lca gen-test "saveEstimation" -i master_index.json -o tests/
```

### Output

Creates a test file with:

- Mock setup for all dependencies (callees)
- Test method stubs for happy path and error cases
- PHPUnit/Jest/pytest format based on language

---

## Configuration

### Environment Variables

| Variable         | Default             | Description        |
| ---------------- | ------------------- | ------------------ |
| `LCA_INDEX_PATH` | `master_index.json` | Default index file |
| `LCA_PORT`       | `8765`              | Web UI port        |
| `LCA_HOST`       | `localhost`         | Web UI host        |

---

## Troubleshooting

### "Module not found"

```bash
# Ensure virtual environment is active
cd logimax-code-analyzer
uv sync
```

### "Index file not found"

```bash
# Create the index first
uv run lca index ../admin/application -o master_index.json
```

### MCP server not responding

```bash
# Check if server is running
uv run lca serve --help

# Restart MCP configuration in Antigravity settings
```

---

## File Structure

```
logimax-code-analyzer/
├── pyproject.toml          # Dependencies
├── pytest.ini              # Test config
├── master_index.json       # Primary index
├── lca/
│   ├── cli.py              # Command-line interface
│   ├── mcp_server.py       # MCP integration
│   ├── core/
│   │   ├── indexer.py      # File indexing
│   │   ├── analyzer.py     # Call graph analysis
│   │   └── parser.py       # Language parsers
│   └── cockpit/
│       ├── server.py       # Web server
│       └── templates/      # UI templates
└── tests/
    ├── conftest.py         # Shared fixtures
    ├── factories.py        # Test factories
    └── test_*.py           # Test files
```

---

_Last Updated: January 31, 2026_
