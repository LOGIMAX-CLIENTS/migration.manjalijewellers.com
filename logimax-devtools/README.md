# Logimax DevTools

Developer tools dashboard for eTail v3 development.

## Quick Start

### Prerequisites

- Node.js 18+
- Python 3.10+
- [uv](https://github.com/astral-sh/uv) (Python package manager)

### Installation

```bash
# Frontend
cd frontend
npm install

# Backend
cd backend
uv sync
```

### Development

```bash
# Start both servers
.\run_devtools.bat

# Or start individually
.\run_devtools.bat frontend
.\run_devtools.bat backend
```

- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8800
- **API Docs**: http://localhost:8800/docs

## Architecture

```
logimax-devtools/
├── frontend/          # React + TypeScript + Vite
│   ├── src/
│   │   ├── app/       # App shell & routing
│   │   ├── core/      # Plugin system
│   │   ├── design-system/  # Design tokens
│   │   └── plugins/   # Feature plugins
│
└── backend/           # FastAPI (Python)
    └── app/
        ├── core/      # Plugin system
        └── plugins/   # Backend plugins
```

## Plugins

| Plugin          | Purpose                                     |
| --------------- | ------------------------------------------- |
| **LCA Cockpit** | Code analysis, call graphs, impact analysis |
| **Test Runner** | Run PHPUnit, Jest, pytest tests             |
| **Log Viewer**  | Real-time debug log monitoring              |

## Design System

Design tokens are defined in `frontend/src/design-system/tokens/`:

- `colors.ts` - Color palette with light/dark themes
- `typography.ts` - Font settings
- `spacing.ts` - Spacing scale (4px grid)
- `motion.ts` - Animation timings

## Adding a New Plugin

1. Create plugin folder: `frontend/src/plugins/newplugin/`
2. Implement `DevToolsPlugin` interface
3. Register in `frontend/src/plugins/index.ts`
4. Create backend plugin in `backend/app/plugins/newplugin/`
5. Register in `backend/app/main.py`

## License

Internal use only.
