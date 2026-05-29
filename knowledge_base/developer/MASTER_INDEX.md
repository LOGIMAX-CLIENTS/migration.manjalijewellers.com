# 👨‍💻 Developer Knowledge Base

> Technical documentation for eTail v3 development.

---

## Documentation Index

### Getting Started

| Document                      | Description                                   |
| ----------------------------- | --------------------------------------------- |
| [Setup Guide](SETUP_GUIDE.md) | Complete environment setup for new developers |

### Development Tools

| Document                                            | Description                                   |
| --------------------------------------------------- | --------------------------------------------- |
| [LCA Code Analyzer](LCA_CODE_ANALYZER.md)           | Static analysis, call graphs, impact analysis |
| [Testing Infrastructure](TESTING_INFRASTRUCTURE.md) | Unit testing, coverage, mutation testing      |

### Architecture

| Document          | Description |
| ----------------- | ----------- |
| Code Architecture | Coming soon |
| Database Schema   | Coming soon |
| API Reference     | Coming soon |

### Workflows

| Document            | Description |
| ------------------- | ----------- |
| Development Setup   | Coming soon |
| Code Review Process | Coming soon |
| Deployment Guide    | Coming soon |

---

## Quick Links

### Run Tests

```bash
# From project root
run_tests.bat              # All tests
run_tests.bat coverage     # With coverage
run_tests.bat watch        # Watch mode
```

### Start LCA Cockpit

```bash
cd logimax-code-analyzer
uv run lca ui -i master_index.json
```

### MCP Tools (Antigravity)

```python
mcp_lca_load_index(...)      # Load index
mcp_lca_search_functions(...) # Search
mcp_lca_get_impact(...)      # Impact analysis
```

---

## Access Level

This folder is accessible to:

- ✅ SUPERADMIN
- ✅ ADMIN (Developer role)

---

_Last Updated: January 31, 2026_
