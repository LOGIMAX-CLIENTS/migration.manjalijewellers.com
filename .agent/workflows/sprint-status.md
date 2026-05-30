---
description: Query GitHub milestones and generate sprint progress report with bug counts by status
version: 1.0
last_updated: 2026-02-19
---

# Sprint Status Workflow

> **Purpose**: Generate a real-time sprint progress report by querying GitHub Issues and local data.
> **When to use**: Sprint standup, status reporting, or anytime you need "where are we?"

## Prerequisites
- GitHub tracking set up via `/github-bug-tracking`
- `.agent/config.md` exists with `{REPO_OWNER}` and `{REPO_NAME}`

## Steps

### Step 1: Load Config [Antigravity]
// turbo
Read `.agent/config.md` → extract `{REPO_OWNER}`, `{REPO_NAME}`

### Step 2: Query GitHub Issues by Milestone [Antigravity]

For each sprint milestone (1–4), use platform dispatch → `List issues` (see `/github-bug-tracking`):

```
Owner: {REPO_OWNER}
Repo: {REPO_NAME}
Labels: ["P0-critical"] → for Sprint 1
State: OPEN / CLOSED
```

Count by status:
- **Open + `status:triaged`** = Queued
- **Open + `status:in-progress`** = In Progress
- **Open + `status:blocked`** = Blocked
- **Open + `status:testing`** = Testing
- **Closed** = Done

### Step 3: Read Local Active Bugs [Antigravity]
// turbo
Read `bug_report_AI/ACTIVE_BUGS.md` → count in-progress and blocked bugs.

### Step 4: Generate Sprint Report [Antigravity]

```markdown
# Sprint Status Report — {DATE}

## Sprint Overview

| Sprint | Milestone | Total | Done | In Progress | Blocked | Testing | Queued | % Complete |
|---|---|---|---|---|---|---|---|---|
| Sprint 1 | P0 Critical | {N} | {N} | {N} | {N} | {N} | {N} | {N}% |
| Sprint 2 | P1 High | {N} | {N} | {N} | {N} | {N} | {N} | {N}% |
| Sprint 3 | P2 Medium | {N} | {N} | {N} | {N} | {N} | {N} | {N}% |
| Sprint 4 | P3 Low | {N} | {N} | {N} | {N} | {N} | {N} | {N}% |
| **Total** | | **{N}** | **{N}** | **{N}** | **{N}** | **{N}** | **{N}** | **{N}%** |

## Blockers
| Bug ID | Module | Blocked At | Reason | Days Blocked |
|---|---|---|---|---|
| {from ACTIVE_BUGS.md} | | | | |

## Active Work
| Bug ID | Module | Developer | Current Step | Started |
|---|---|---|---|---|
| {from ACTIVE_BUGS.md} | | | | |

## Velocity (if data available)
- Average fix time: {N} minutes
- Bugs fixed today: {N}
- Bugs fixed this week: {N}
- Projected sprint completion: {date}
```

### Step 5: Present Report [Antigravity → Human]
Display the report. Optionally save to `bug_report_AI/SPRINT_REPORTS/sprint_status_{DATE}.md`.
