# MCP Server Configuration

The SDLC pipeline uses MCP (Model Context Protocol) servers for database queries and GitHub integration.

## Required Servers

### 1. `mysql-local` — Local Development Database

Used by: test_design, verify steps (SELECT-only queries for verification)

```json
{
  "mysql-local": {
    "command": "npx",
    "args": ["-y", "@benborla29/mcp-server-mysql"],
    "env": {
      "MYSQL_HOST": "127.0.0.1",
      "MYSQL_PORT": "3307",
      "MYSQL_USER": "retaillmx_admin",
      "MYSQL_PASSWORD": "<your_password>",
      "MYSQL_DATABASE": "retail_dev"
    }
  }
}
```

### 2. `github` — GitHub Integration

Used by: recipe push (enrich step), code search

```json
{
  "github": {
    "command": "npx",
    "args": ["-y", "@modelcontextprotocol/server-github"],
    "env": {
      "GITHUB_PERSONAL_ACCESS_TOKEN": "<your_token>"
    }
  }
}
```

## Where to Configure

Add these to: `~/.gemini/config/mcp_config.json`

**Full example:**
```json
{
  "mcpServers": {
    "mysql-local": {
      "command": "npx",
      "args": ["-y", "@benborla29/mcp-server-mysql"],
      "env": {
        "MYSQL_HOST": "127.0.0.1",
        "MYSQL_PORT": "3307",
        "MYSQL_USER": "retaillmx_admin",
        "MYSQL_PASSWORD": "YOUR_PASSWORD_HERE",
        "MYSQL_DATABASE": "retail_dev"
      }
    },
    "github": {
      "command": "npx",
      "args": ["-y", "@modelcontextprotocol/server-github"],
      "env": {
        "GITHUB_PERSONAL_ACCESS_TOKEN": "YOUR_TOKEN_HERE"
      }
    }
  }
}
```

## Optional Servers

### `logimax-devtools` — LCA Code Analyzer (Recommended)

Provides fast code analysis queries (function lookup, caller tracing, impact analysis) via MCP instead of slow CLI subprocess calls. Agents use `lca_investigate`, `lca_search`, `lca_analyze`, `lca_impact`, and `lca_manage` tools.

```json
{
  "logimax-devtools": {
    "command": "python",
    "args": ["-m", "lca.mcp_server"],
    "cwd": "<project_root>",
    "env": {
      "PYTHONPATH": "<project_root>/logimax-devtools/backend/lca_core",
      "LCA_INDEX_PATH": "<project_root>/.lca/index.json"
    }
  }
}
```

Replace `<project_root>` with your actual project path, e.g.:
- Windows: `C:/xampp/htdocs/etail_v3`
- Linux: `/var/www/etail_v3`

**Verify**: After configuring, check that the LCA MCP tools appear in your IDE's tool list. Test with a simple query:
```
lca_search → action: "functions", query: "get_cash_book"
```

> **Note**: LCA CLI (`lca` command via pip) and LCA MCP are different entry points to the same codebase. MCP is faster (keeps index in memory) and is the preferred path for AI agents.

## Verification

After configuring, restart the IDE and run:
```
python .sdlc/engine/cli.py health
```

The health check validates MCP connectivity as part of the subsystem check.

## Safety Rules

- **Never use PowerShell `Set-Content -Encoding UTF8` on JSON files** — adds BOM that breaks parsers.
- **Always verify JSON after editing:** `python -c "import json; json.load(open(r'path'))"`
- **Never define the same server in both `mcp_config.json` and `settings.json`** — causes conflicts.

