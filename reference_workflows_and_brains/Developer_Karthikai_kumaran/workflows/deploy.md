---
description: "Sprint deployment — deploy approved fixes to staging/production."
---

# /deploy — Phase 5: Deployment & Monitoring

> **Source:** FINAL_SOP_BUG_REMEDIATION.md § 8, MASTER_BUG_REMEDIATION_PROCEDURE.md § 12

## Pre-Deployment Checklist [👤 HUMAN]
```
[ ] All targeted fixes pass sign-off criteria
[ ] All unit tests pass
[ ] All smoke tests pass
[ ] Full database backup completed
[ ] Full codebase backup / Git commit tagged
[ ] Schema changes tested on staging first
[ ] Stakeholders notified of maintenance window
```

## Deployment Steps [👤 HUMAN]
1. Pull approved code changes from branch
2. Execute pending schema migrations in order
3. Clear PHP opcode cache (if applicable)
4. Instruct users to hard-refresh browsers (JS cache)
5. Run production smoke test:
   - [ ] Primary CREATE/SAVE works
   - [ ] Primary EDIT/UPDATE works
   - [ ] Primary DELETE works
   - [ ] SEARCH works
   - [ ] PRINT/EXPORT works

## Git Tagging
- Sprint: `release-{MODULE}-{date}-sprint{N}`
- Hotfix: `hotfix-{BUG_ID}-{date}`

## Post-Deployment Monitoring (24h) [👤 HUMAN]
```
[ ] No new PHP errors in error logs
[ ] No MySQL warnings in slow query log
[ ] Core module functions work correctly
[ ] No user-reported regressions
[ ] Database integrity checks pass (no new orphan records)
```

## Rollback Procedure
1. Identify Bug ID that caused regression
2. Refer to rollback plan in execution plan
3. Revert specific code change or restore backup
4. For schema: execute documented rollback SQL
5. Verify pre-fix state restored
6. Document reason and re-assess

## Communication Protocol
| Event | Notify | Template |
|-------|--------|----------|
| Deployed to staging | Reporter + QA | "Bug {ID} fixed. Please verify on staging." |
| Deployed to production | Reporter + stakeholders | "Bug {ID} fixed and deployed." |
| Rollback | Reporter + management | "Bug {ID} rolled back. Reason: {reason}." |
