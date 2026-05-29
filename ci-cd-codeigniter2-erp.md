
# CI/CD Design for Legacy CodeIgniter 2 ERP (Staging & Production)

This document captures the **complete CI/CD design discussion** for a legacy **Jewellery ERP Web Admin** built with **PHP / CodeIgniter 2**, deployed on **AWS and cPanel servers with root access**.

The content is structured to be **LLM-friendly (anti-gravity / ingestion-ready)**:
- Clear headings
- Deterministic rules
- Minimal ambiguity
- Explicit contracts
- No conversational noise

---

## 1. Problem Statement

Existing setup:
- Single-branch auto deployment
- No environment separation
- No database migration management
- No production safety mechanisms

Goal:
- Introduce **staging** and **production** pipelines
- Add **safe database migrations**
- Preserve legacy CI2 compatibility
- Avoid framework upgrades
- Keep deployment logic auditable and deterministic

---

## 2. Confirmed Git Workflow

```
feature/*  →  staging  →  production
```

Roles:
- **Developer** pushes to `staging`
- **Approver** merges `staging → production`
- **CI/CD** handles deployment automatically

---

## 3. Environment Model

| Environment | Branch | Deploy Type | DB Backup | Approval |
|------------|--------|------------|-----------|----------|
| Staging | `staging` | Auto | ❌ | ❌ |
| Production | `production` | Manual | ✅ | ✅ |

Environment behavior is controlled **only via CI/CD variables**, never hardcoded.

---

## 4. Core Design Principle

> **Schema evolution is a deployment concern, not an application concern**

Because:
- CodeIgniter 2 has no modern migration system
- Controllers/models must never mutate schema
- CI/CD must apply schema changes *before* runtime

---

## 5. Database Migration Strategy (CI2-safe)

### Folder Structure

```
database/
  migrations/
    001_init.sql
    002_add_column.sql
    003_alter_index.sql
```

### Migration Tracking Table

```sql
CREATE TABLE schema_migrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  migration VARCHAR(255) UNIQUE,
  executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Rules

- Never modify existing migrations
- Always add new numbered SQL files
- Same migrations run in staging & production
- Execution is idempotent

---

## 6. Migration Execution Guarantees

- Filenames are **sorted** → deterministic order
- `schema_migrations` table → prevents re-execution
- Stops on first failure
- Production runs include DB backup

Mnemonic:
**O.S.O**
- Order (sorted files)
- Skip (tracked migrations)
- Once (idempotent)

---

## 7. Migration Runner Script Location

```
project-root/
  scripts/
    run-migrations.sh
```

Why inside repo:
- Versioned with code
- Environment-agnostic
- Portable across servers
- Single source of truth

---

## 8. Environment Variables (Deployment Contract)

Defined **only in CI/CD pipelines**, never in code:

```
ENVIRONMENT=staging|production
RUN_MIGRATIONS=true|false
DB_BACKUP=true|false
```

Why:
- Prevents accidental prod deploys
- Enables reuse of same scripts
- Eliminates environment guessing

---

## 9. Webhook-Based Deployment Model

Deployment is triggered via **GitHub Actions → Webhook → Server**.

CI sends deployment context as JSON:

```json
"deploy": {
  "environment": "staging",
  "run_migrations": true,
  "db_backup": false
}
```

---

## 10. Webhook (`deploy.php`) Responsibilities

- Validate signature & IP
- Parse webhook payload
- Extract deployment config
- Export env vars dynamically using `putenv()`
- Trigger `deploy.sh`

Key rule:
> Environment variables are set **per deployment**, not globally.

---

## 11. Dynamic Environment Injection (PHP)

Minimal, safe addition:

```php
putenv("ENVIRONMENT=staging");
putenv("RUN_MIGRATIONS=true");
putenv("DB_BACKUP=false");
```

Values are read from webhook payload.
Defaults to staging if missing.

---

## 12. Deployment Script Behavior

`deploy.sh` remains generic and reusable:

```
git fetch
git reset --hard
(optional) DB backup
run migrations
clear cache
done
```

No branching logic inside script.
Behavior is driven entirely by env vars.

---

## 13. Production Safety Guarantees

- Manual approval before merge
- Database backup before migrations
- Lock file prevents concurrent deploys
- Server-side approval enforcement possible

---

## 14. Advanced Features (Planned)

### Rollback Support
- Explicit rollback SQL files
- Reverse execution order
- Manual in production

### Schema Drift Detection
- Detects DB changes outside CI/CD
- Fails deployment if drift found
- Prevents silent ERP corruption

---

## 15. Final Architecture Summary

```
Developer
  ↓ push
staging branch
  ↓ auto
CI/CD
  ↓ webhook
Server
  ├─ deploy.sh
  ├─ run-migrations.sh
  └─ logs

Approver
  ↓ merge
production branch
  ↓ approval
CI/CD
  ↓ webhook
Server
  ├─ DB backup
  ├─ deploy.sh
  ├─ run-migrations.sh
  └─ logs
```

---

## 16. Key Takeaways

- Legacy systems can have **enterprise-grade CI/CD**
- Database migrations must be deterministic
- Environment behavior must live in CI/CD, not code
- Webhooks + env injection scale cleanly
- Safety > speed for ERP systems

---

**End of document**
