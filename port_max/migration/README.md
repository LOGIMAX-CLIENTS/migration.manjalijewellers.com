# Port_Max Migration Tool

SvelteKit-based frontend for the Port_Max data migration tool.

## Prerequisites

- [Node.js](https://nodejs.org/) v20+ (for local development)
- npm (comes with Node.js)

## Setup

```sh
# Install dependencies
npm install

# Start development server
npm run dev

# or open in browser automatically
npm run dev -- --open
```

## Building

```sh
# Create production build (outputs to dist/)
npm run build

# Preview production build
npm run preview
```

## Deployment (Mono-Repo)

The frontend is **automatically built** by the GitHub Actions CI pipeline (`deploy-portmax.yml`).

- CI runs `npm ci` → `npm run build` → commits `dist/` to git
- Server runs `git pull` and gets the pre-built `dist/` — no Node.js needed on server
- `composer install` runs automatically on server for the API (`port_max/api/`)

## Configuration

- **API URL**: Auto-detected from browser domain at runtime (`src/lib/config.js`)
- **Database**: Uses root-level `global_configs.php` (shared with admin)

## Tech Stack

- [SvelteKit](https://svelte.dev/) v2 with Svelte 5
- [Vite](https://vite.dev/) v7
- [@sveltejs/adapter-static](https://svelte.dev/docs/kit/adapter-static) — outputs to `dist/`
- Base path: `/port_max`

# auto deploy test