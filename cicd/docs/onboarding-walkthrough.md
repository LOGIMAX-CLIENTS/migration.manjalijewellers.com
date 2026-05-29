# Mono-Repo Client Onboarding — Walkthrough

> How the onboarding workflow and setup script work together to deploy a new client in ~1 hour.

---

## Architecture Overview

```
LOCAL (Developer Machine)              SERVER (Client EC2)
┌─────────────────────────┐            ┌────────────────────────────┐
│ retail_v5/              │            │ /var/www/{client}/         │
│ ├── config/clients/     │  ──SSH──►  │ ├── staging/      (git pull)│
│ │   └── {client}.json   │            │ └── prod/                 │
│ ├── clients/{client}/   │            │     ├── current → release │
│ └── .github/workflows/  │            │     ├── releases/         │
│     └── deploy-mono-    │            │     └── shared/           │
│         client.yml      │            │         ├── config/       │
└─────────────────────────┘            │         ├── data/ (17 dirs)│
                                       │         └── logs/         │
GITHUB                                 └────────────────────────────┘
┌─────────────────────────┐
│ Actions Workflow         │
│ ├── Detect clients from │──webhook──► webhooks/deploy.php
│ │   config/clients/*.json│            │ staging: git pull
│ └── curl webhook per     │            │ prod: deploy-mono.sh
│     client               │
└─────────────────────────┘
```

---

## How the Workflow Works (`/onboard-mono-client`)

The workflow has **7 phases**. Here's what happens at each stage and why:

### Phase 1: Local Setup (Developer Machine)

**What**: Create two files in the `retail_v5` repo.

1. **`config/clients/{client}.json`** — The workflow reads this to discover clients. Contains:
   - Client name
   - Webhook URLs (where to send deploy triggers)
   - SSL verification flag
   - Branch mapping (which git branch = which environment)

2. **`clients/{client}/assets/.gitkeep`** — Creates the override directory structure. Any client-specific view, CSS, or config overrides go here.

**Why**: The GitHub Actions workflow **dynamically scans** `config/clients/*.json` on every push. No workflow YAML changes needed per client. The `.json` file IS the registration.

### Phase 2: SSH Deploy Key (Server)

**What**: Generate an Ed25519 SSH key on the server and add it to GitHub as a deploy key.

**Why**: The server needs to `git clone` and `git pull` from the private GitHub repo. Deploy keys are scoped to a single repo (more secure than personal tokens). One key per server, shared across all clients on that server.

### Phase 3: Database Migration (RDS)

**What**: Create databases on RDS and import the source SQL dump.

**Why**: Each client gets their own databases (`{client}_prod` and `{client}_staging`). The dump is stripped of DEFINER clauses because RDS doesn't allow SUPER privilege.

### Phase 4: Server Setup (`setup-mono-client.sh`)

**What**: One command creates everything on the server.

**Why**: This was manual before (took hours). The script automates 12 discrete tasks:

```
setup-mono-client.sh {client} {repo_url} --domain {domain} --staging-domain {staging}
```

Here's what it does, in order:

```
Step 1:  Create /var/www/{client}/prod/shared/ directory tree
         ├── config/           (database.php, global_configs.php)
         ├── data/             (17 dirs: img, uploads, assets_*, qrcodes, etc.)
         └── logs/             (app_log, tagging_log)

Step 2:  Clone production (shallow, single branch)
         → /var/www/{client}/prod/releases/{timestamp}/

Step 3:  Clone staging (full clone for git pull)
         → /var/www/{client}/staging/
         + Create gitignored dirs (admin/img, admin/log, etc.)

Step 4:  Generate config templates
         → global_configs.php (Globals:: class with placeholders)
         → database.php (references Globals::$hostname, etc.)

Step 5:  Create symlinks in production release (21 total)
         See "Server Directory Structure" below for full tree.

Step 6:  Auto-uncomment index.php
         Enables require_once global_configs.php (commented for local dev)

Step 7:  Create webhook handlers
         → {release}/webhooks/deploy.php (production)
         → staging/webhooks/deploy.php (staging)

Step 8:  Create atomic 'current' symlink
         → /var/www/{CLIENT_ID}/prod/current → releases/{timestamp}/

Step 9:  Copy maintenance.html to shared/

Step 10: Generate Apache vhost config
         → /tmp/{CLIENT_ID}-vhost.conf
         Includes: staging + production blocks, maintenance rewrite rules,
                   SetEnv CLIENT_ID, +FollowSymLinks

Step 11: Set permissions (ubuntu:www-data, 775)
```

---

## Server Directory Structure (Symlink Map)

After `setup-mono-client.sh` runs, the server looks like this.
`→` = symlink pointing to shared directory. All others are real files/dirs.

```
/var/www/{CLIENT_ID}/
│
├── staging/                                             ← git pull deploys here
│   ├── admin/
│   │   ├── index.php                                    (require_once global_configs.php uncommented)
│   │   ├── img/                                         (local dir, gitignored)
│   │   ├── log/                                         (local dir, gitignored)
│   │   ├── tagging_log/                                 (local dir, gitignored)
│   │   ├── uploads/                                     (local dir, gitignored)
│   │   ├── uploads_root/                                (local dir, gitignored)
│   │   ├── vendor_ack/                                  (local dir, gitignored)
│   │   ├── estimation/                                  (local dir, gitignored)
│   │   ├── export/                                      (local dir, gitignored)
│   │   ├── adm_app_apk/                                 (local dir, gitignored)
│   │   ├── bill_qrcode/                                 (local dir, gitignored)
│   │   ├── esti_qrcode/                                 (local dir, gitignored)
│   │   ├── other_qrcode/                                (local dir, gitignored)
│   │   ├── other_inventory_qrcode/                      (local dir, gitignored)
│   │   ├── other_product_qrcode/                        (local dir, gitignored)
│   │   ├── assets/
│   │   │   ├── kyc/                                     (local dir, gitignored)
│   │   │   └── img/                                     (local dir, gitignored)
│   │   └── application/config/
│   │       ├── database.php                             (generated — uses Globals::)
│   │       └── global_configs.php                       (manually configured)
│   ├── global_configs.php                               (manually configured)
│   └── webhooks/deploy.php                              (webhook handler)
│
└── prod/
    ├── current → releases/{timestamp}/                  ← atomic symlink (deploy swaps this)
    ├── .maintenance_active                              ← create to enable maintenance page
    │
    ├── releases/
    │   └── {timestamp}/                                 ← each deploy creates a new release
    │       ├── admin/
    │       │   ├── index.php                            (require_once uncommented)
    │       │   │
    │       │   │   ── Config Symlinks (2) ──
    │       │   ├── application/config/
    │       │   │   └── database.php                   → shared/config/database.php
    │       │   │
    │       │   │   ── Data Symlinks (14) ──
    │       │   ├── img                                → shared/data/img
    │       │   ├── uploads                            → shared/data/uploads
    │       │   ├── uploads_root                       → shared/data/uploads_root
    │       │   ├── vendor_ack                         → shared/data/vendor_ack
    │       │   ├── estimation                         → shared/data/estimation
    │       │   ├── export                             → shared/data/export
    │       │   ├── adm_app_apk                        → shared/data/adm_app_apk
    │       │   ├── bill_qrcode                        → shared/data/bill_qrcode
    │       │   ├── esti_qrcode                        → shared/data/esti_qrcode
    │       │   ├── other_qrcode                       → shared/data/other_qrcode
    │       │   ├── other_inventory_qrcode             → shared/data/other_inventory_qrcode
    │       │   ├── other_product_qrcode               → shared/data/other_product_qrcode
    │       │   ├── assets/
    │       │   │   ├── kyc                            → shared/data/kyc
    │       │   │   │
    │       │   │   │   ── Asset Symlinks (3) ──
    │       │   │   │   (git-tracked files copied to shared FIRST, then symlinked)
    │       │   │   ├── img                            → shared/data/assets_img
    │       │   │   ├── dist                           → shared/data/assets_dist
    │       │   │   └── plugins                        → shared/data/assets_plugins
    │       │   │
    │       │   │   ── Log Symlinks (2) ──
    │       │   ├── log                                → shared/logs/app_log
    │       │   └── tagging_log                        → shared/logs/tagging_log
    │       │
    │       ├── global_configs.php                     → shared/config/global_configs.php
    │       ├── rate.txt                               → shared/data/rate.txt
    │       ├── maintenance.html                         (copied from repo)
    │       └── webhooks/deploy.php                      (webhook handler)
    │
    └── shared/                                          ← PERSISTS across all releases
        ├── config/
        │   ├── database.php                             (generated — uses Globals::)
        │   └── global_configs.php                       (manually configured with RDS creds)
        │
        ├── data/
        │   ├── img/                                     (client photos, barcodes)
        │   ├── kyc/                                     (KYC documents)
        │   ├── uploads/                                 (general uploads)
        │   ├── uploads_root/                            (root-level uploads)
        │   ├── vendor_ack/                              (vendor acknowledgements)
        │   ├── estimation/                              (estimation PDFs)
        │   ├── export/                                  (data exports)
        │   ├── adm_app_apk/                             (mobile app APKs)
        │   ├── bill_qrcode/                             (billing QR codes)
        │   ├── esti_qrcode/                             (estimation QR codes)
        │   ├── other_qrcode/                            (other QR codes)
        │   ├── other_inventory_qrcode/                  (inventory QR codes)
        │   ├── other_product_qrcode/                    (product QR codes)
        │   ├── assets_img/                              (CSS images, icons — partially tracked)
        │   ├── assets_dist/                             (compiled JS/CSS — partially tracked)
        │   ├── assets_plugins/                          (jQuery plugins — partially tracked)
        │   └── rate.txt                                 (gold/silver rates)
        │
        └── logs/
            ├── app_log/                                 (CI3 application logs)
            └── tagging_log/                             (barcode tagging logs)
```

**Total: 21 symlinks** (2 config + 14 data + 3 assets + 2 logs)

### Phase 5: DNS + SSL (Senior)

**What**: Point domain to server, add to ACM certificate.

**Why**: Can't be automated without AWS API access (planned for Phase 2).

### Phase 6: DevOps Tool Registration

**What**: `POST /api/clients` to register the client in the DevOps Tool.

**Why**: Enables deploy tracking, status dashboard, and future approval flows.

### Phase 7: Verification

**What**: Test access, maintenance page, webhook, and full GitHub Actions E2E.

**Why**: Catch misconfigurations before handing off to the client.

---

## How Deployments Work After Setup

### Staging Deploy (Push to `support`)

```
Developer pushes to support
    → GitHub Actions detects push
    → Scans config/clients/*.json
    → For each client: reads webhook_url from JSON
    → curl POST → webhooks/deploy.php
    → deploy.php: touch .maintenance_active
    → deploy.php: git fetch + git reset --hard
    → deploy.php: rm .maintenance_active
```

### Production Deploy (Push to `Production`)

```
Developer pushes to Production
    → GitHub Actions detects push
    → curl POST → webhooks/deploy.php
    → deploy.php: runs deploy-mono.sh
    → deploy-mono.sh:
        1. Touch .maintenance_active
        2. Clone new release → releases/{timestamp}/
        3. Create all symlinks (config, data, assets, logs)
        4. Atomic symlink swap: current → new release
        5. Set permissions
        6. Remove .maintenance_active
        7. Clean up old releases (keep last 5)
```

---

## Key Design Decisions

| Decision | Why |
|----------|-----|
| Hybrid layout (staging=git pull, prod=symlink) | Staging is fast (git pull), prod is safe (atomic swap, instant rollback) |
| Shared data via symlinks | Assets, uploads, logs persist across releases |
| Webhook URLs in JSON, not GitHub secrets | Zero workflow changes per client |
| Single MONO_WEBHOOK_SECRET | Simpler than per-client secrets, same team manages all |
| `ssl_verify` flag in JSON | Fix SSL by changing config, not workflow code |
| Assets copied before symlink | Preserves git-tracked files that are partially gitignored |
