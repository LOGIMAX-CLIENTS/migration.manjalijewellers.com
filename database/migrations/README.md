# Database Migrations

Schema migration files for the retail ERP application. These migrations run automatically during deployments via `scripts/run-migrations.sh`.

## Naming Convention

```
YYYYMMDD_HHMMSS_description.sql
```

Example: `20260318_120000_add_customer_gst_field.sql`

## File Format

```sql
-- UP
ALTER TABLE ret_customer ADD COLUMN gst_number VARCHAR(20) DEFAULT NULL AFTER mobile;
CREATE INDEX idx_customer_gst ON ret_customer(gst_number);

-- DOWN
DROP INDEX idx_customer_gst ON ret_customer;
ALTER TABLE ret_customer DROP COLUMN gst_number;
```

- `-- UP` section is **required** — runs during deploy
- `-- DOWN` section is **optional** — used for manual rollback
- Files are applied in **lexicographic order** (timestamp prefix ensures correct ordering)
- Each file should be a **single logical change** (one feature/fix per file)
- Standard SQL comments (`--`) for documentation

## Rules

1. **Never modify an applied migration** — create a new one instead
2. **Keep migrations small** — one table change per file
3. **Favor additive changes** — `ADD COLUMN` is safe, `DROP COLUMN` is not
4. **Test on staging first** — staging deploys run migrations before production
5. **No data migrations in schema files** — use separate data migration scripts for bulk data changes

## Tracking

Applied migrations are tracked in the `schema_migrations` table in each client database:

```sql
CREATE TABLE schema_migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL UNIQUE,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    environment VARCHAR(20) DEFAULT 'production'
);
```

## Deploy Integration

- **Production**: `deploy-mono.sh` runs pending migrations BEFORE symlink swap. If migration fails, deploy aborts (old code stays live).
- **Staging**: `webhook-deploy.php` runs pending migrations AFTER git merge.
