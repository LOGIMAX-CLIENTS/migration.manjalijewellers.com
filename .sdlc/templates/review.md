# Review: {task_id}

**Reviewer**: AI (SDLC REVIEW phase)
**Date**: <!-- auto-fill -->
**Verdict**: PENDING

## Acceptance Criteria Verification

<!-- Copy from requirement.md, verify each -->

| # | Criterion | Status | Evidence |
|---|---|---|---|
| 1 | <!-- from requirement.md --> | PASS / FAIL | <!-- how verified --> |
| 2 | <!-- from requirement.md --> | PASS / FAIL | <!-- how verified --> |
| 3 | <!-- from requirement.md --> | PASS / FAIL | <!-- how verified --> |

## LCA Health Delta

| Metric | Before | After | Delta |
|---|---|---|---|
| Health Score | <!-- from health_before.json --> | <!-- from health_after.json --> | <!-- +/- --> |
| Functions Changed | — | <!-- count --> | — |
| Complexity Delta | — | <!-- from diff-health --> | — |

## Quality Checklist (per modified file)

- [ ] No hardcoded magic values
- [ ] No `SELECT *`
- [ ] Error handling present
- [ ] XSS safe
- [ ] SQL injection safe
- [ ] CSRF tokens intact
- [ ] Financial calcs: parseFloat + toFixed + NaN guard
- [ ] Business logic in models
- [ ] No N+1 queries
- [ ] Consistent with module patterns

## Findings

### Critical (blocks merge)
<!-- None / list -->

### Warnings (should fix)
<!-- None / list -->

### Advisories (nice to fix)
<!-- None / list -->

## Files Reviewed

<!-- FROM lca changeset -->

| File | Lines Changed | Notes |
|---|---|---|
| <!-- path --> | <!-- +/- lines --> | <!-- observations --> |

## Verdict: PENDING

<!-- Change to: PASS / PASS_WITH_ADVISORIES / FAIL -->
<!-- If FAIL: list specific issues that must be fixed -->
