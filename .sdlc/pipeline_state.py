#!/usr/bin/env python3
"""
SDLC Pipeline State Manager v1.0.0
====================================
Single source of truth for the SDLC pipeline.
All workflows, hooks, scripts, and the dashboard read/write through this.

Features (v1.0.0):
  - 10-phase pipeline with VIBE auto-routing (8 sub-roles)
  - Hard file permission enforcement
  - Phase-skip detection + transition condition checks
  - Fast-track mode for trivial fixes
  - Pre-commit hook validation
  - Phase duration metrics + velocity tracking
  - Role prompt extraction (get-prompt) + suggest-next
  - Decision tracker + pause/resume handoff
  - Skill activation map per phase

Usage from Python:
    from pipeline_state import PipelineState
    state = PipelineState()                    # auto-finds project root
    state.start_task('fix', 'billing', 'correct discount calc', ticket='BIL-003')
    state.set_phase('PLANNING')
    state.save()

Usage from CLI:
    python pipeline_state.py show              # Show current state
    python pipeline_state.py get-prompt        # Focused role instructions
    python pipeline_state.py suggest-next      # What to do next
    python pipeline_state.py add-decision key value --reason why
    python pipeline_state.py pause --reason "switching sessions"
    python pipeline_state.py reset             # Clear state for new task
"""

import argparse
import json
import os
import sys
import copy
from datetime import datetime
from pathlib import Path


VERSION = '1.1.0'
SDLC_DIR = '.sdlc'
STATE_FILE = 'pipeline.json'
SCHEMA_FILE = 'pipeline.schema.json'
HISTORY_DIR = 'history'
MAX_TIMELINE_ENTRIES = 200  # Prune timeline beyond this to prevent bloat

VALID_PHASES = [
    'IDLE', 'DISCUSS', 'VIBE', 'REQUIREMENT', 'PLANNING', 'TEST_DESIGN',
    'CODING', 'TEST_EXECUTION', 'REVIEW',
    'READY_TO_COMMIT', 'PR_OPEN', 'MERGED', 'CLOSED'
]

# Ordered phases for skip detection (excludes VIBE which is a parallel mode)
PHASE_ORDER = [
    'IDLE', 'DISCUSS', 'REQUIREMENT', 'PLANNING', 'TEST_DESIGN',
    'CODING', 'TEST_EXECUTION', 'REVIEW',
    'READY_TO_COMMIT', 'PR_OPEN', 'MERGED', 'CLOSED'
]

VALID_STATUSES = ['pending', 'in_progress', 'completed', 'skipped', 'failed']


def find_project_root():
    """Walk up from CWD to find .git directory."""
    path = Path(os.getcwd())
    while path != path.parent:
        if (path / '.git').exists():
            return str(path)
        path = path.parent
    return os.getcwd()


class PipelineState:
    """Read/write the SDLC pipeline state file.

    v1.1: Multi-task support. pipeline.json is now a lightweight registry
    listing active tasks. Each task's full state lives in tasks/{id}.json.
    When only 1 task is active, auto-selects it for backward compatibility.
    """

    def __init__(self, project_root=None, task_id=None):
        self.root = project_root or find_project_root()
        self.sdlc_dir = os.path.join(self.root, SDLC_DIR)
        self.state_path = os.path.join(self.sdlc_dir, STATE_FILE)
        self.schema_path = os.path.join(self.sdlc_dir, SCHEMA_FILE)
        self.history_dir = os.path.join(self.sdlc_dir, HISTORY_DIR)
        self.tasks_dir = os.path.join(self.sdlc_dir, 'tasks')
        self.task_id = task_id  # Explicit task to load
        self._task_path = None  # Path to current task's state file

        # Migrate v1.0 format if needed, then load
        self._maybe_migrate()
        self.registry = self._load_registry()
        self.data = self._load()
        self._validate()  # Auto-repair on load

    # ── Registry (v1.1: lightweight index of active tasks) ──

    def _empty_registry(self):
        """Return an empty v2.0 registry."""
        return {
            '_version': '2.0',
            '_updated_at': None,
            'active_tasks': [],
            'default_task': None,
        }

    def _load_registry(self):
        """Load the task registry from pipeline.json."""
        if os.path.exists(self.state_path):
            with open(self.state_path, 'r', encoding='utf-8') as f:
                data = json.load(f)
            # v2.0 registry has 'active_tasks' key
            if 'active_tasks' in data:
                return data
        # No registry yet — create empty one
        reg = self._empty_registry()
        self._save_registry(reg)
        return reg

    def _save_registry(self, reg=None):
        """Write registry to pipeline.json."""
        if reg is None:
            reg = self.registry
        reg['_updated_at'] = datetime.now().isoformat()
        os.makedirs(self.sdlc_dir, exist_ok=True)
        with open(self.state_path, 'w', encoding='utf-8') as f:
            json.dump(reg, f, indent=2, ensure_ascii=False)

    def _sync_registry_entry(self):
        """Update this task's entry in the registry (phase, last_active)."""
        if not self.task_id:
            return
        task_data = self.data.get('task', {})
        phase = self.data.get('phase', {}).get('current', 'IDLE')
        now = datetime.now().isoformat()

        for entry in self.registry.get('active_tasks', []):
            if entry['id'] == self.task_id:
                entry['phase'] = phase
                entry['last_active'] = now
                entry['summary'] = task_data.get('summary', entry.get('summary', ''))
                self._save_registry()
                return

    def _add_registry_entry(self, task_id, task_type, module, summary, status='active'):
        """Add a new task to the registry."""
        now = datetime.now().isoformat()
        entry = {
            'id': task_id,
            'type': task_type,
            'module': module,
            'summary': summary,
            'status': status,
            'phase': 'REQUIREMENT' if status == 'active' else 'IDLE',
            'priority': None,
            'source': None,
            'started_at': now if status == 'active' else None,
            'queued_at': now if status == 'pending' else None,
            'last_active': now,
        }
        self.registry.setdefault('active_tasks', []).append(entry)
        # If this is the only active task, set as default
        active_count = sum(1 for t in self.registry['active_tasks'] if t.get('status', 'active') == 'active')
        if active_count == 1 and status == 'active':
            self.registry['default_task'] = task_id
        self._save_registry()

    def queue_task(self, task_id, task_type, module, summary, priority=None, source=None, source_url=None):
        """Add a task to the backlog (status=pending). Does NOT create a task file."""
        # Check for duplicates
        for t in self.registry.get('active_tasks', []):
            if t['id'] == task_id:
                raise ValueError(f'Task {task_id} already exists in registry.')

        now = datetime.now().isoformat()
        entry = {
            'id': task_id,
            'type': task_type,
            'module': module,
            'summary': summary,
            'status': 'pending',
            'phase': 'IDLE',
            'priority': priority,
            'source': source,
            'source_url': source_url,
            'queued_at': now,
            'started_at': None,
            'last_active': now,
        }
        self.registry.setdefault('active_tasks', []).append(entry)
        self._save_registry()
        return task_id

    def pick_task(self, task_id, updated_by=None):
        """Activate a pending task from the backlog. Creates the task file and starts the pipeline."""
        entry = None
        for t in self.registry.get('active_tasks', []):
            if t['id'] == task_id:
                entry = t
                break

        if not entry:
            raise ValueError(f'Task {task_id} not found in registry.')
        if entry.get('status') == 'active':
            raise ValueError(f'Task {task_id} is already active.')

        # Activate in registry
        entry['status'] = 'active'
        entry['started_at'] = datetime.now().isoformat()
        entry['phase'] = 'REQUIREMENT'
        self.registry['default_task'] = task_id
        self._save_registry()

        # Create the actual task file via start_task logic
        self.start_task(
            entry['type'], entry['module'], entry['summary'],
            ticket=task_id, priority=entry.get('priority'),
            updated_by=updated_by or 'cli'
        )
        return task_id

    def list_backlog(self):
        """Return all pending tasks from the registry."""
        return [
            t for t in self.registry.get('active_tasks', [])
            if t.get('status') == 'pending'
        ]

    def _remove_registry_entry(self, task_id):
        """Remove a task from the registry."""
        self.registry['active_tasks'] = [
            t for t in self.registry.get('active_tasks', [])
            if t['id'] != task_id
        ]
        # Update default
        if self.registry.get('default_task') == task_id:
            remaining = self.registry['active_tasks']
            self.registry['default_task'] = remaining[0]['id'] if remaining else None
        self._save_registry()

    def _task_file_path(self, task_id):
        """Get the path for a task's state file."""
        return os.path.join(self.tasks_dir, f'{task_id}.json')

    def _maybe_migrate(self):
        """Migrate v1.0 pipeline.json (single-task) to v2.0 registry + task file."""
        if not os.path.exists(self.state_path):
            return
        with open(self.state_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
        # Already v2.0?
        if 'active_tasks' in data:
            return
        # v1.0 format: has 'task' section directly
        task_id = data.get('task', {}).get('id')
        if not task_id:
            # Idle v1.0 — just replace with empty registry
            reg = self._empty_registry()
            with open(self.state_path, 'w', encoding='utf-8') as f:
                json.dump(reg, f, indent=2, ensure_ascii=False)
            return
        # Active v1.0 task — move to tasks/{id}.json + create registry
        os.makedirs(os.path.join(self.sdlc_dir, 'tasks'), exist_ok=True)
        task_path = os.path.join(self.sdlc_dir, 'tasks', f'{task_id}.json')
        with open(task_path, 'w', encoding='utf-8') as f:
            json.dump(data, f, indent=2, ensure_ascii=False)
        # Create registry
        task_info = data.get('task', {})
        reg = self._empty_registry()
        reg['active_tasks'] = [{
            'id': task_id,
            'type': task_info.get('type'),
            'module': task_info.get('module'),
            'summary': task_info.get('summary'),
            'phase': data.get('phase', {}).get('current', 'IDLE'),
            'started_at': task_info.get('created_at'),
            'last_active': data.get('_updated_at'),
        }]
        reg['default_task'] = task_id
        with open(self.state_path, 'w', encoding='utf-8') as f:
            json.dump(reg, f, indent=2, ensure_ascii=False)
        print(f'  🔄 Migrated v1.0 → v2.0: task {task_id} moved to tasks/{task_id}.json')

    def _resolve_task_id(self):
        """Determine which task to load based on constructor args and registry.

        Auto-select only when exactly 1 task exists (backward compatible).
        When multiple tasks exist and no --task given, returns None so that
        show() displays the registry and other commands can prompt for --task.
        """
        if self.task_id:
            return self.task_id
        active = self.registry.get('active_tasks', [])
        if len(active) == 1:
            return active[0]['id']
        return None  # Multiple or zero tasks — require explicit --task

    def _load(self):
        """Load task state from per-task file, or return idle state."""
        resolved = self._resolve_task_id()
        if resolved:
            self.task_id = resolved
            self._task_path = self._task_file_path(resolved)
            if os.path.exists(self._task_path):
                with open(self._task_path, 'r', encoding='utf-8') as f:
                    return json.load(f)
        # No task loaded — return idle schema
        self.task_id = None
        self._task_path = None
        return self._minimal_schema()

    def _minimal_schema(self):
        return {
            '_version': '2.0',
            '_updated_at': None,
            '_updated_by': None,
            'task': {'id': None, 'type': None, 'module': None, 'summary': None,
                     'ticket': None, 'priority': None, 'track': None,
                     'description': None, 'requested_by': None, 'created_at': None},
            'phase': {'current': 'IDLE', 'previous': None, 'started_at': None, 'history': []},
            'requirement': {'status': 'pending', 'completed_at': None},
            'plan': {'status': 'pending', 'completed_at': None, 'approved_at': None,
                     'complexity': None, 'risk_level': None, 'files_planned': [], 'decisions': []},
            'test_design': {'status': 'pending', 'completed_at': None,
                            'test_files': [], 'test_matrix': [],
                            'categories_covered': [],
                            'total_tests': 0, 'red_confirmed': False},
            'coding': {'status': 'pending', 'started_at': None, 'completed_at': None,
                       'files_modified': [], 'checklist': [], 'blockers': [], 'notes': [],
                       'tests_passing': 0, 'tests_total': 0},
            'test_execution': {'status': 'pending', 'completed_at': None,
                               'tests_passed': 0, 'tests_failed': 0, 'tests_skipped': 0,
                               'regression_passed': False, 'exploratory_done': False,
                               'failures': []},
            'review': {'status': 'pending', 'verdict': None, 'findings': []},
            'commit': {'status': 'pending', 'hash': None, 'message': None, 'branch': None},
            'pr': {'status': 'pending', 'number': None, 'url': None, 'quality_gate': None},
            'changeset': {'type': None, 'module': None, 'summary': None, 'details': [],
                          'files': [], 'ticket': None, 'breaking': False},
            'decisions': [],
            'context': {'module_brain_available': None, 'danger_zone': False,
                        'cross_module': False, 'pause_point': None},
            'timeline': []
        }

    def _validate(self):
        """Validate state integrity after load. Auto-repair minor issues."""
        issues = []

        # Phase must exist and be valid
        phase_section = self.data.get('phase')
        if not isinstance(phase_section, dict):
            self.data['phase'] = {'current': 'IDLE', 'previous': None,
                                  'started_at': None, 'history': []}
            issues.append('Missing phase section — reset to IDLE')
        else:
            current = phase_section.get('current')
            if current not in VALID_PHASES:
                issues.append(f'Invalid phase "{current}" — reset to IDLE')
                self.data['phase']['current'] = 'IDLE'
            if not isinstance(phase_section.get('history'), list):
                self.data['phase']['history'] = []
                issues.append('Phase history was not a list — reset to []')

        # Required top-level sections
        required_sections = [
            'task', 'phase', 'requirement', 'plan', 'coding',
            'review', 'commit', 'pr', 'changeset', 'context', 'timeline'
        ]
        for section in required_sections:
            if section not in self.data:
                if section == 'timeline':
                    self.data[section] = []
                else:
                    self.data[section] = {}
                issues.append(f'Missing section "{section}" — added empty')

        # Timeline must be a list
        if not isinstance(self.data.get('timeline'), list):
            self.data['timeline'] = []
            issues.append('Timeline was not a list — reset to []')

        # Task section must have required keys
        task = self.data.get('task', {})
        if isinstance(task, dict):
            for key in ('id', 'type', 'module', 'summary', 'ticket'):
                if key not in task:
                    task[key] = None

        # Log and save repairs
        if issues:
            for issue in issues:
                self.data.setdefault('timeline', []).append({
                    'at': datetime.now().isoformat(),
                    'message': f'AUTO-REPAIR: {issue}',
                    'by': 'validator'
                })
            self._write()
            print(f'  ⚠️  State validation: {len(issues)} issue(s) auto-repaired.')

        return issues

    def _prune_timeline(self, data):
        """Prune timeline to prevent unbounded growth.
        Keeps first entries (task start context) and recent entries.
        """
        timeline = data.get('timeline', [])
        if len(timeline) > MAX_TIMELINE_ENTRIES:
            # Keep first 20 (task creation context) + last 180 (recent activity)
            keep_start = 20
            keep_end = MAX_TIMELINE_ENTRIES - keep_start
            pruned_count = len(timeline) - MAX_TIMELINE_ENTRIES
            data['timeline'] = (
                timeline[:keep_start] +
                [{'at': datetime.now().isoformat(),
                  'message': f'[{pruned_count} entries pruned]',
                  'by': 'system'}] +
                timeline[-keep_end:]
            )

    def _write(self, data=None):
        """Write state to per-task file with timeline pruning."""
        if data is None:
            data = self.data
        data['_updated_at'] = datetime.now().isoformat()
        self._prune_timeline(data)

        if self._task_path:
            # Write to per-task file
            os.makedirs(os.path.dirname(self._task_path), exist_ok=True)
            with open(self._task_path, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
        else:
            # No task loaded — this shouldn't happen in normal flow,
            # but write to a temp location to avoid data loss
            os.makedirs(self.sdlc_dir, exist_ok=True)

    def save(self, updated_by=None):
        """Save current state to disk with optional author."""
        if updated_by:
            self.data['_updated_by'] = updated_by
        self._write()
        self._sync_registry_entry()

    # ── Phase Management ──

    def get_phase(self):
        return self.data.get('phase', {}).get('current', 'IDLE')

    def set_phase(self, phase, updated_by=None, skip_ok=False):
        """Transition to a new phase with skip detection.

        Args:
            phase: Target phase name
            updated_by: Who triggered the transition
            skip_ok: If True, suppress skip warnings (for intentional fast-track)
        """
        if phase not in VALID_PHASES:
            raise ValueError(f'Invalid phase: {phase}. Valid: {VALID_PHASES}')

        old_phase = self.get_phase()
        now = datetime.now().isoformat()

        # ── Skip Detection ──
        # Only check for ordered phases (VIBE is a parallel mode, not sequential)
        if old_phase in PHASE_ORDER and phase in PHASE_ORDER:
            old_idx = PHASE_ORDER.index(old_phase)
            new_idx = PHASE_ORDER.index(phase)

            # Forward skip: jumping over phases
            if new_idx > old_idx + 1 and not skip_ok:
                skipped = PHASE_ORDER[old_idx + 1:new_idx]
                skip_msg = f'⚠️ SKIP DETECTED: {old_phase} → {phase} (skipped: {", ".join(skipped)})'
                self.log(skip_msg, updated_by)
                print(f'  {skip_msg}')

            # Backward jump: regression
            if new_idx < old_idx:
                regress_msg = f'🔄 REGRESSION: {old_phase} → {phase} (going backward)'
                self.log(regress_msg, updated_by)
                print(f'  {regress_msg}')

        self.data['phase']['previous'] = old_phase
        self.data['phase']['current'] = phase
        self.data['phase']['started_at'] = now

        # Add to history
        self.data['phase'].setdefault('history', [])
        self.data['phase']['history'].append({
            'from': old_phase,
            'to': phase,
            'at': now,
            'by': updated_by or 'system'
        })

        self.log(f'Phase: {old_phase} → {phase}', updated_by)
        self.save(updated_by)

    # ── Task Management ──

    # Fast-track pipeline: IDLE → DISCUSS → CODING → REVIEW → COMMIT
    FAST_TRACK_PHASES = ['IDLE', 'DISCUSS', 'CODING', 'REVIEW', 'READY_TO_COMMIT', 'PR_OPEN', 'MERGED', 'CLOSED']
    FAST_TRACK_TYPES = ['hotfix', 'typo', 'config', 'docs']
    FAST_TRACK_MAX_FILES = 3

    # ── Spec File Templates ──

    SPEC_DIR_NAME = 'active'  # .sdlc/active/{task_id}/
    SPEC_DONE_DIR = 'done'    # .sdlc/done/{task_id}/

    REQUIREMENT_TEMPLATE = '''# {task_id}: {summary}

## Task Info
- **Type:** {task_type}
- **Module:** {module}
- **Priority:** {priority}
- **Ticket:** {ticket}
- **Created:** {created_at}

## Description
{description}

## Current Behavior
<!-- What is happening now (the problem)? -->
- 

## Expected Behavior
<!-- What should happen instead? -->
- 

## Acceptance Criteria
<!-- Each criterion must be testable. Check these off when verified. -->
- [ ] 

## Technical Notes
<!-- Root cause analysis, affected files, dependencies -->
- 
'''

    DESIGN_TEMPLATE = '''# {task_id}: Technical Design

## Requirement Reference
See [requirement.md](./requirement.md)

## Approach
<!-- High-level approach to solving this -->


## Files to Modify
| File | Change | Reason |
|---|---|---|
| | | |

## Impact Analysis
- **Upstream:** 
- **Downstream:** 
- **Shared tables:** 
- **Cross-module:** 

## Risks
- 

## Decisions
| Decision | Choice | Rationale |
|---|---|---|
| | | |
'''

    TASKS_TEMPLATE = '''# {task_id} — Implementation Tasks

## Requirement
See [requirement.md](./requirement.md) | Design: [design.md](./design.md)

## Checklist
<!-- Check off as you complete each item -->

### Changes
- [ ] 

### Tests
- [ ] 

### Verification
- [ ] Code review passed
- [ ] Business rule verified by user
'''

    REVIEW_TEMPLATE = '''# {task_id} — Code Review

## Verdict: PENDING
<!-- Change to: PASS | PASS_WITH_ADVISORIES | FAIL -->

## Files Reviewed
| File | Status | Notes |
|---|---|---|
| | | |

## Findings

### Critical
<!-- Must fix before commit -->
- None

### Warnings
<!-- Should fix, but not blocking -->
- None

### Advisories
<!-- Nice to fix, informational -->
- None

## Business Rules Verified
- [ ] 

## Checklist
- [ ] No hardcoded values
- [ ] No SELECT *
- [ ] Error handling present
- [ ] XSS/SQL injection safe
- [ ] Consistent with existing patterns
'''

    def start_task(self, task_type, module, summary, ticket=None,
                   priority=None, track=None, description=None,
                   requested_by=None, updated_by=None, fast_track=False):
        """Initialize a new task — creates a per-task state file.

        v1.1: Does NOT overwrite other active tasks. Each task gets its own
        state file in tasks/{id}.json and an entry in the registry.

        Args:
            fast_track: If True, uses shortened pipeline (skips TEST_DESIGN, TEST_EXECUTION).
                       Enforces max 3 files and restricted task types.
        """
        # Generate task ID
        prefix = module[:3].upper() if module else 'TSK'
        task_id = ticket or f'{prefix}-{datetime.now().strftime("%y%m%d%H%M")}'

        # Check if task already exists in registry
        for t in self.registry.get('active_tasks', []):
            if t['id'] == task_id:
                print(f'  ⚠️ Task {task_id} already exists. Use --task {task_id} to work on it.')
                # Switch to existing task
                self.task_id = task_id
                self._task_path = self._task_file_path(task_id)
                self.data = self._load()
                return

        # Create fresh state from schema
        self.data = self._load_schema()
        now = datetime.now().isoformat()

        self.data['task'] = {
            'id': task_id,
            'type': task_type,
            'module': module,
            'summary': summary,
            'ticket': ticket or task_id,
            'priority': priority,
            'track': track,
            'description': description,
            'requested_by': requested_by,
            'created_at': now,
            'fast_track': fast_track,
        }

        # Pre-fill changeset
        self.data['changeset']['type'] = task_type
        self.data['changeset']['module'] = module
        self.data['changeset']['summary'] = summary
        self.data['changeset']['ticket'] = ticket or task_id

        # Point to the new task's file
        self.task_id = task_id
        self._task_path = self._task_file_path(task_id)
        os.makedirs(self.tasks_dir, exist_ok=True)

        # Add to registry
        self._add_registry_entry(task_id, task_type, module, summary)

        if fast_track:
            self.log(f'FAST-TRACK task: {task_type}({module}): {summary}', updated_by)
            self.log(f'Fast-track pipeline: DISCUSS → CODING → REVIEW → COMMIT (max {self.FAST_TRACK_MAX_FILES} files)', updated_by)
            self.set_phase('DISCUSS', updated_by, skip_ok=True)
        else:
            self.set_phase('REQUIREMENT', updated_by)

        # Create spec folder with initial requirement.md
        self._create_spec_folder(task_id)

        self.log(f'Task started: {task_type}({module}): {summary}', updated_by)

    def is_fast_track(self):
        """Check if the current task is fast-track."""
        return self.data.get('task', {}).get('fast_track', False)

    def validate_fast_track_commit(self):
        """Validate fast-track constraints before commit.

        Returns:
            (ok: bool, reason: str)
        """
        if not self.is_fast_track():
            return True, 'Not a fast-track task'

        files = self.data.get('coding', {}).get('files_modified', [])
        files += self.data.get('coding', {}).get('files_added', [])
        if len(files) > self.FAST_TRACK_MAX_FILES:
            return False, (f'Fast-track allows max {self.FAST_TRACK_MAX_FILES} files, '
                          f'but {len(files)} are staged. Upgrade to full pipeline.')

        return True, 'OK'

    # ── Role Prompt Generation (Approach D: Hybrid) ──

    def _load_roles(self):
        """Load roles.json."""
        roles_path = os.path.join(self.sdlc_dir, 'roles.json')
        if os.path.exists(roles_path):
            with open(roles_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        return {}

    def get_prompt(self, sub_role=None):
        """Generate a focused role prompt for the current phase.

        Returns a focused ~50 line instruction set containing:
        - Role persona and instructions
        - Constraints
        - File permissions
        - Skill activation (which skills/brains to read)
        - Suggested next actions

        Args:
            sub_role: For VIBE mode, which sub-role to generate prompt for.
        """
        roles_data = self._load_roles()
        phase = self.get_phase()
        task = self.data.get('task', {})
        module = task.get('module', 'unknown')

        lines = []
        sep = '=' * 55
        lines.append(sep)

        # ── Header ──
        role_def = roles_data.get('roles', {}).get(phase, {})
        icon = role_def.get('icon', '❓')
        title = role_def.get('title', phase)

        if phase == 'VIBE' and sub_role:
            vibe_roles = role_def.get('sub_roles', {})
            sr_def = vibe_roles.get(sub_role, {})
            sr_icon = sr_def.get('icon', '🎸')
            sr_title = sr_def.get('title', sub_role)
            lines.append(f'ACTIVE ROLE: {sr_title} ({sr_icon}) via VIBE Mode')
            lines.append(f'Phase: VIBE → {sub_role} | Task: {task.get("ticket", "none")}')
            role_def = sr_def  # Use sub-role definition
        else:
            lines.append(f'ACTIVE ROLE: {title} ({icon})')
            lines.append(f'Phase: {phase} | Task: {task.get("ticket", "none")}')

        if task.get('fast_track'):
            lines.append('MODE: FAST-TRACK (max 3 files, shortened pipeline)')

        lines.append('')

        # ── Persona ──
        persona = role_def.get('persona', '')
        if persona:
            lines.append('PERSONA:')
            # Word-wrap persona at ~80 chars
            words = persona.split()
            line = ''
            for w in words:
                if len(line) + len(w) + 1 > 78:
                    lines.append(f'  {line}')
                    line = w
                else:
                    line = f'{line} {w}'.strip()
            if line:
                lines.append(f'  {line}')
            lines.append('')

        # ── Instructions ──
        instructions = role_def.get('instructions', [])
        if instructions:
            lines.append('INSTRUCTIONS:')
            for i, inst in enumerate(instructions, 1):
                lines.append(f'  {i}. {inst}')
            lines.append('')

        # ── Constraints ──
        constraints = role_def.get('constraints', [])
        if constraints:
            lines.append('CONSTRAINTS:')
            for c in constraints:
                lines.append(f'  ✗ {c}')
            lines.append('')

        # ── File Permissions ──
        file_perms = roles_data.get('file_permissions', {})
        if phase == 'VIBE' and sub_role:
            vibe_perms = file_perms.get('VIBE', {}).get('sub_role_permissions', {}).get(sub_role, {})
            allowed = vibe_perms.get('allowed_patterns', [])
            blocked_reason = vibe_perms.get('blocked_reason', '')
        else:
            phase_perms = file_perms.get(phase, {})
            allowed = phase_perms.get('allowed_patterns', [])
            blocked_reason = phase_perms.get('blocked_reason', '')

        if allowed or blocked_reason:
            lines.append('FILE PERMISSIONS:')
            for p in allowed:
                lines.append(f'  ✅ {p}')
            if blocked_reason:
                lines.append(f'  ❌ {blocked_reason}')
            lines.append('')

        # ── Skill Activation ──
        skill_map = roles_data.get('skill_activation', {})
        if phase == 'VIBE' and sub_role:
            activation = skill_map.get('VIBE', {}).get(sub_role, {})
        else:
            activation = skill_map.get(phase, {})

        skills = activation.get('skills', [])
        workflows = activation.get('workflows', [])
        brain = activation.get('brain', '')
        sys_brain = activation.get('system_brain', [])

        if skills or workflows or brain or sys_brain:
            lines.append('SKILL ACTIVATION:')
            for s in skills:
                lines.append(f'  📖 READ: {s}')
            for w in workflows:
                lines.append(f'  🔄 WORKFLOW: {w}')
            if brain:
                resolved = brain.replace('{module}', module)
                lines.append(f'  🧠 MODULE BRAIN: {resolved}')
            for sb in sys_brain:
                lines.append(f'  📚 SYSTEM BRAIN: knowledge_brain/_SYSTEM/{sb}')
            lines.append('')

        # ── Transition ──
        next_phase = role_def.get('transitions_to', '')
        condition = role_def.get('transition_condition', '')
        if next_phase:
            lines.append(f'NEXT PHASE: {next_phase}')
            lines.append(f'  Condition: {condition}')
            lines.append('')

        # ── Resolved Decisions (from decision tracker) ──
        decisions = self.data.get('decisions', [])
        if decisions:
            lines.append('RESOLVED DECISIONS (DO NOT re-ask):')
            for d in decisions:
                lines.append(f'  [{d.get("phase", "?")}] {d["key"]} = {d["value"]}')
            lines.append('')

        # ── Handoff Detection ──
        handoff_path = os.path.join(self.sdlc_dir, 'handoff.md')
        if os.path.exists(handoff_path):
            lines.append('⚠️  HANDOFF FILE DETECTED: .sdlc/handoff.md')
            lines.append('  → Read it for context from the previous session')
            lines.append('  → Run `sdlc resume` to view, then `sdlc clear-handoff` after reading')
            lines.append('')

        lines.append(sep)
        return '\n'.join(lines)

    def suggest_next(self):
        """Suggest the next action based on current pipeline state.

        Inspired by BMad's bmad-help — analyzes current state and recommends
        what to do next.

        Returns:
            list of suggestion strings
        """
        phase = self.get_phase()
        task = self.data.get('task', {})
        suggestions = []

        if phase == 'IDLE' and not task.get('id'):
            suggestions.append('No active task. Options:')
            suggestions.append('  → Start a new task: sdlc start-task -t fix -m <module> -s "description"')
            suggestions.append('  → Fast-track: sdlc fast-track hotfix <module> "description"')
            suggestions.append('  → Quick start: sdlc quick fix <module> "description"')
            suggestions.append('  → View metrics: sdlc metrics')
            suggestions.append('  → View history: sdlc history')
            return suggestions

        # Phase-specific suggestions
        req = self.data.get('requirement', {})
        plan = self.data.get('plan', {})
        test_d = self.data.get('test_design', {})
        coding = self.data.get('coding', {})
        review = self.data.get('review', {})

        if phase == 'DISCUSS':
            suggestions.append('You are in DISCUSS phase. Options:')
            suggestions.append('  → Investigate code: ask questions, trace flows, read code')
            suggestions.append('  → When ready to act: transition to REQUIREMENT')
            suggestions.append(f'  → sdlc transition REQUIREMENT')

        elif phase == 'VIBE':
            suggestions.append('You are in VIBE mode (auto-routing). Options:')
            suggestions.append('  → Just tell me what you need — I will auto-select the right expert')
            suggestions.append('  → Override: "act as bug_fixer" or "switch to architect"')
            suggestions.append('  → Exit VIBE: sdlc transition REQUIREMENT (for structured work)')

        elif phase == 'REQUIREMENT':
            if req.get('status') == 'completed':
                suggestions.append('✅ Requirement complete. Next:')
                suggestions.append(f'  → sdlc transition PLANNING')
            else:
                suggestions.append('📋 Working on requirements. Checklist:')
                suggestions.append('  → Define user story with role, action, benefit')
                suggestions.append('  → Define 5-10 acceptance criteria')
                suggestions.append('  → Identify cross-module impacts')
                suggestions.append('  → Run /get-requirement if needed')

        elif phase == 'PLANNING':
            if plan.get('approved_at'):
                suggestions.append('✅ Plan approved. Next:')
                suggestions.append(f'  → sdlc transition TEST_DESIGN')
            elif plan.get('status') == 'completed':
                suggestions.append('📐 Plan ready for review. Next:')
                suggestions.append('  → Present plan to user for approval')
                suggestions.append('  → After approval: sdlc set plan.approved_at <timestamp>')
            else:
                suggestions.append('📐 Working on implementation plan. Checklist:')
                suggestions.append('  → Trace all affected code paths')
                suggestions.append('  → Build impact matrix')
                suggestions.append('  → Estimate complexity (S/M/L/XL)')
                suggestions.append('  → Pre-fill changeset.files with expected files')

        elif phase == 'TEST_DESIGN':
            if test_d.get('red_confirmed'):
                suggestions.append('✅ Tests written and confirmed RED. Next:')
                suggestions.append(f'  → sdlc transition CODING')
            else:
                suggestions.append('🧪 Writing tests (TDD red phase). Checklist:')
                suggestions.append('  → Write unit tests for each model method')
                suggestions.append('  → Write integration tests for API endpoints')
                suggestions.append('  → Write edge case tests (boundary values)')
                suggestions.append('  → If financial: write 5+ calculation scenarios')
                suggestions.append('  → Run tests to confirm they FAIL (red phase)')

        elif phase == 'CODING':
            if coding.get('status') == 'completed':
                suggestions.append('✅ Coding complete. Next:')
                suggestions.append(f'  → sdlc transition TEST_EXECUTION')
            else:
                suggestions.append('💻 Implementing code. Checklist:')
                suggestions.append('  → Follow the approved plan exactly')
                suggestions.append('  → Make tests pass one by one (green phase)')
                suggestions.append('  → Check all callers of modified methods')
                suggestions.append('  → Run full test suite periodically')

        elif phase == 'TEST_EXECUTION':
            suggestions.append('✅ Running tests. Checklist:')
            suggestions.append('  → Run full test suite')
            suggestions.append('  → Perform exploratory testing')
            suggestions.append('  → Verify cross-module flows')
            suggestions.append('  → If all pass: sdlc transition REVIEW')
            suggestions.append('  → If failures: sdlc transition CODING (with bug report)')

        elif phase == 'REVIEW':
            if review.get('verdict') in ('PASS', 'PASS_WITH_ADVISORIES'):
                suggestions.append('✅ Review passed. Next:')
                suggestions.append(f'  → sdlc transition READY_TO_COMMIT')
            else:
                suggestions.append('🔍 Code review in progress. Checklist:')
                suggestions.append('  → Read every changed line')
                suggestions.append('  → Check security, performance, business rules')
                suggestions.append('  → Generate review report with verdict')

        elif phase == 'READY_TO_COMMIT':
            suggestions.append('📦 Ready to commit. Checklist:')
            suggestions.append('  → Fill changeset details')
            suggestions.append('  → Stage all files (including tests)')
            suggestions.append('  → Write commit message: <type>(<module>): <summary> [<ticket>]')
            suggestions.append('  → git commit (pre-commit hook will validate)')
            suggestions.append(f'  → After commit: sdlc transition PR_OPEN')

        elif phase == 'PR_OPEN':
            suggestions.append('🔀 PR lifecycle. Checklist:')
            suggestions.append('  → Create PR targeting Dev branch (Retail_1.1.1.0001)')
            suggestions.append('  → Wait for quality gate')
            suggestions.append(f'  → After merge: sdlc transition MERGED')

        elif phase in ('MERGED', 'CLOSED'):
            suggestions.append(f'Task {task.get("id")} is {phase}. Options:')
            suggestions.append('  → Start new task: sdlc reset && sdlc start-task ...')
            suggestions.append('  → Run /learn-and-improve to capture learnings')

        return suggestions

    # ── Decision Tracker (inspired by GSD gsd-discuss-phase) ──

    def add_decision(self, key, value, phase=None, reason=None):
        """Record a resolved decision so downstream phases don't re-ask.

        Inspired by GSD's CONTEXT.md gray-area resolution — captures
        decisions as key-value pairs with the phase that made them.

        Args:
            key: Short decision identifier (e.g., 'db_approach', 'validation_strategy')
            value: The resolved decision value
            phase: Phase that made this decision (auto-detected if None)
            reason: Optional rationale for the decision
        """
        if 'decisions' not in self.data:
            self.data['decisions'] = []

        # Check for duplicate key — update if exists
        for d in self.data['decisions']:
            if d.get('key') == key:
                d['value'] = value
                d['updated_at'] = datetime.now().isoformat()
                d['phase'] = phase or self.get_phase()
                if reason:
                    d['reason'] = reason
                self.save()
                return f'Decision updated: {key} = {value}'

        # New decision
        decision = {
            'key': key,
            'value': value,
            'phase': phase or self.get_phase(),
            'decided_at': datetime.now().isoformat(),
            'reason': reason
        }
        self.data['decisions'].append(decision)
        self.save()
        return f'Decision recorded: {key} = {value}'

    def list_decisions(self):
        """List all resolved decisions."""
        decisions = self.data.get('decisions', [])
        if not decisions:
            return ['No decisions recorded yet.']

        lines = [f'Resolved Decisions ({len(decisions)}):']
        for d in decisions:
            phase_tag = f'[{d.get("phase", "?")}]'
            reason = f' — {d["reason"]}' if d.get('reason') else ''
            lines.append(f'  {phase_tag} {d["key"]} = {d["value"]}{reason}')
        return lines

    def clear_decisions(self):
        """Clear all decisions (used on task reset)."""
        self.data['decisions'] = []
        self.save()

    # ── Pause/Resume Handoff (inspired by GSD gsd-pause-work) ──

    def pause(self, reason=None):
        """Generate a handoff file for cross-conversation context preservation.

        Inspired by GSD's .continue-here.md — captures complete state so the
        next conversation can resume without re-reading everything.

        Creates .sdlc/handoff.md with:
        - Current phase and task info
        - All resolved decisions
        - Work completed so far
        - Blockers and remaining work
        - Resume instructions

        Args:
            reason: Why the work is being paused
        """
        phase = self.get_phase()
        task = self.data.get('task', {})
        decisions = self.data.get('decisions', [])
        timeline = self.data.get('timeline', [])
        now = datetime.now().isoformat()

        lines = [
            '# SDLC Pipeline — Handoff',
            f'> Generated: {now}',
            f'> Reason: {reason or "Session ended"}',
            '',
            '## Resume Instructions',
            '1. Run `sdlc get-prompt` to load current role',
            '2. Read this handoff file for context',
            '3. Continue from the "Current Position" below',
            '',
            '## Current Position',
            f'- **Phase:** {phase}',
            f'- **Task:** {task.get("ticket", "none")} — {task.get("summary", "none")}',
            f'- **Module:** {task.get("module", "unknown")}',
            f'- **Type:** {task.get("type", "unknown")}',
        ]

        if task.get('fast_track'):
            lines.append('- **Mode:** FAST-TRACK')

        # Decisions
        if decisions:
            lines.append('')
            lines.append('## Resolved Decisions (DO NOT re-ask these)')
            for d in decisions:
                reason_str = f' — {d["reason"]}' if d.get('reason') else ''
                lines.append(f'- **{d["key"]}:** {d["value"]}{reason_str} [{d.get("phase", "?")}]')

        # Phase-specific progress
        lines.append('')
        lines.append('## Progress Summary')

        req = self.data.get('requirement', {})
        plan = self.data.get('plan', {})
        test_d = self.data.get('test_design', {})
        coding = self.data.get('coding', {})
        review = self.data.get('review', {})

        if req.get('status') == 'completed':
            lines.append('- ✅ Requirement: Complete')
        elif req.get('status') != 'pending':
            lines.append(f'- 🔄 Requirement: {req.get("status", "pending")}')

        if plan.get('approved_at'):
            lines.append(f'- ✅ Plan: Approved ({plan.get("complexity", "?")} complexity)')
        elif plan.get('status') == 'completed':
            lines.append('- ⏳ Plan: Complete but NOT YET APPROVED')
        elif plan.get('status') != 'pending':
            lines.append(f'- 🔄 Plan: {plan.get("status", "pending")}')

        if test_d.get('red_confirmed'):
            lines.append(f'- ✅ Tests: Written ({test_d.get("total_tests", 0)} tests, red confirmed)')
        elif test_d.get('status') != 'pending':
            lines.append(f'- 🔄 Tests: {test_d.get("status", "pending")}')

        if coding.get('status') == 'completed':
            lines.append(f'- ✅ Coding: Complete ({coding.get("tests_passing", 0)}/{coding.get("tests_total", 0)} tests passing)')
        elif coding.get('files_modified'):
            lines.append(f'- 🔄 Coding: In progress ({len(coding["files_modified"])} files modified)')

        if review.get('verdict'):
            lines.append(f'- {"✅" if review["verdict"] in ("PASS", "PASS_WITH_ADVISORIES") else "❌"} Review: {review["verdict"]}')

        # Blockers
        blockers = coding.get('blockers', [])
        if blockers:
            lines.append('')
            lines.append('## Blockers')
            for b in blockers:
                lines.append(f'- ⚠️ {b}')

        # Recent timeline entries
        if timeline:
            lines.append('')
            lines.append('## Recent Activity (last 5)')
            for entry in timeline[-5:]:
                ts = entry.get('timestamp', '?')[:16]
                msg = entry.get('message', entry.get('action', '?'))
                lines.append(f'- [{ts}] {msg}')

        lines.append('')
        lines.append('---')
        lines.append('*This file is auto-generated by `sdlc pause`. Delete after resuming.*')
        lines.append('')

        # Write handoff file
        handoff_path = os.path.join(self.sdlc_dir, 'handoff.md')
        with open(handoff_path, 'w', encoding='utf-8') as f:
            f.write('\n'.join(lines))

        # Log to timeline
        self.log(f'Paused: {reason or "session ended"}')
        self.data['context']['pause_point'] = now
        self.save()

        return handoff_path

    def resume(self):
        """Check for and return handoff file contents if one exists."""
        handoff_path = os.path.join(self.sdlc_dir, 'handoff.md')
        if os.path.exists(handoff_path):
            with open(handoff_path, 'r', encoding='utf-8') as f:
                content = f.read()
            return content
        return None

    def clear_handoff(self):
        """Delete the handoff file after resuming."""
        handoff_path = os.path.join(self.sdlc_dir, 'handoff.md')
        if os.path.exists(handoff_path):
            os.remove(handoff_path)
            return True
        return False

    def _load_schema(self):
        """Load a fresh copy from schema."""
        if os.path.exists(self.schema_path):
            with open(self.schema_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        return self._minimal_schema()

    # ── Field Access ──

    def get(self, dotpath, default=None):
        """Get a value by dot path. E.g., 'task.module' or 'phase.current'."""
        keys = dotpath.split('.')
        val = self.data
        for k in keys:
            if isinstance(val, dict):
                val = val.get(k, default)
            else:
                return default
        return val

    def set(self, dotpath, value, updated_by=None):
        """Set a value by dot path."""
        keys = dotpath.split('.')
        d = self.data
        for k in keys[:-1]:
            d = d.setdefault(k, {})
        d[keys[-1]] = value
        self.save(updated_by)

    def append(self, dotpath, value, updated_by=None):
        """Append to a list field by dot path."""
        keys = dotpath.split('.')
        d = self.data
        for k in keys[:-1]:
            d = d.setdefault(k, {})
        lst = d.setdefault(keys[-1], [])
        if isinstance(lst, list):
            lst.append(value)
        self.save(updated_by)

    # ── Section Updates ──

    def complete_requirement(self, category=None, user_story=None,
                             acceptance_criteria=None, impacts=None, updated_by=None):
        """Mark requirement phase as complete."""
        now = datetime.now().isoformat()
        self.data['requirement']['status'] = 'completed'
        self.data['requirement']['completed_at'] = now
        if category:
            self.data['requirement']['category'] = category
        if user_story:
            self.data['requirement']['user_story'] = user_story
        if acceptance_criteria:
            self.data['requirement']['acceptance_criteria'] = acceptance_criteria
        if impacts:
            self.data['requirement']['impacts'] = impacts
        self.set_phase('PLANNING', updated_by)

    def approve_plan(self, complexity=None, risk_level=None,
                     files_planned=None, decisions=None, updated_by=None):
        """Mark plan as approved."""
        now = datetime.now().isoformat()
        self.data['plan']['status'] = 'completed'
        self.data['plan']['completed_at'] = now
        self.data['plan']['approved_at'] = now
        self.data['plan']['approved_by'] = updated_by or 'user'
        if complexity:
            self.data['plan']['complexity'] = complexity
        if risk_level:
            self.data['plan']['risk_level'] = risk_level
        if files_planned:
            self.data['plan']['files_planned'] = files_planned
        if decisions:
            self.data['plan']['decisions'] = decisions
        self.set_phase('TEST_DESIGN', updated_by)

    def complete_test_design(self, test_files=None, categories=None,
                             total_tests=0, updated_by=None):
        """Mark test design as complete — all tests written and confirmed to fail."""
        now = datetime.now().isoformat()
        self.data.setdefault('test_design', {})
        self.data['test_design']['status'] = 'completed'
        self.data['test_design']['completed_at'] = now
        self.data['test_design']['red_confirmed'] = True
        if test_files:
            self.data['test_design']['test_files'] = test_files
        if categories:
            self.data['test_design']['categories_covered'] = categories
        self.data['test_design']['total_tests'] = total_tests
        self.set_phase('CODING', updated_by)

    def complete_coding(self, files_modified=None, tests_passing=0,
                        tests_total=0, updated_by=None):
        """Mark coding as complete — all tests should now pass."""
        now = datetime.now().isoformat()
        self.data['coding']['status'] = 'completed'
        self.data['coding']['completed_at'] = now
        self.data['coding']['tests_passing'] = tests_passing
        self.data['coding']['tests_total'] = tests_total
        if files_modified:
            self.data['coding']['files_modified'] = files_modified
        self.set_phase('TEST_EXECUTION', updated_by)

    def complete_test_execution(self, passed=0, failed=0, skipped=0,
                                regression_passed=False, failures=None,
                                updated_by=None):
        """Mark test execution as complete."""
        now = datetime.now().isoformat()
        self.data.setdefault('test_execution', {})
        self.data['test_execution']['status'] = 'completed'
        self.data['test_execution']['completed_at'] = now
        self.data['test_execution']['tests_passed'] = passed
        self.data['test_execution']['tests_failed'] = failed
        self.data['test_execution']['tests_skipped'] = skipped
        self.data['test_execution']['regression_passed'] = regression_passed
        self.data['test_execution']['exploratory_done'] = True
        if failures:
            self.data['test_execution']['failures'] = failures
        if failed == 0:
            self.set_phase('REVIEW', updated_by)
        else:
            # Tests failed — send back to CODING
            self.log(f'Test execution: {failed} failures — returning to CODING', updated_by)
            self.set_phase('CODING', updated_by)

    def complete_review(self, verdict, findings=None, updated_by=None):
        """Record review results."""
        self.data['review']['status'] = 'completed'
        self.data['review']['completed_at'] = datetime.now().isoformat()
        self.data['review']['verdict'] = verdict
        if findings:
            self.data['review']['findings'] = findings
        if verdict in ('PASS', 'PASS_WITH_ADVISORIES'):
            self.set_phase('READY_TO_COMMIT', updated_by)

    def complete_commit(self, commit_hash, message, branch, updated_by=None):
        """Record commit info."""
        self.data['commit']['status'] = 'completed'
        self.data['commit']['completed_at'] = datetime.now().isoformat()
        self.data['commit']['hash'] = commit_hash
        self.data['commit']['hash_short'] = commit_hash[:7] if commit_hash else None
        self.data['commit']['message'] = message
        self.data['commit']['branch'] = branch
        self.set_phase('PR_OPEN', updated_by)

    # ── Timeline ──

    def log(self, message, by=None):
        """Add an entry to the timeline."""
        self.data.setdefault('timeline', [])
        self.data['timeline'].append({
            'at': datetime.now().isoformat(),
            'message': message,
            'by': by or 'system'
        })

    # ── Archive ──

    def archive(self, task_id_override=None):
        """Archive current state to history and clean up task file."""
        tid = task_id_override or self.data.get('task', {}).get('id')
        if not tid:
            return

        os.makedirs(self.history_dir, exist_ok=True)
        ts = datetime.now().strftime('%Y-%m-%d_%H%M%S')
        filename = f'{ts}_{tid}.json'
        archive_path = os.path.join(self.history_dir, filename)

        with open(archive_path, 'w', encoding='utf-8') as f:
            json.dump(self.data, f, indent=2, ensure_ascii=False)

        # Remove per-task file
        task_file = self._task_file_path(tid)
        if os.path.exists(task_file):
            os.remove(task_file)

        # Remove from registry
        self._remove_registry_entry(tid)

        return filename

    def reset(self, target_task_id=None):
        """Reset state for a new task.

        Args:
            target_task_id: Specific task to reset. If None, resets current task.
                           Use '__all__' to reset all active tasks.
        """
        if target_task_id == '__all__':
            # Archive all active tasks
            for t in list(self.registry.get('active_tasks', [])):
                tid = t['id']
                task_path = self._task_file_path(tid)
                if os.path.exists(task_path):
                    with open(task_path, 'r', encoding='utf-8') as f:
                        task_data = json.load(f)
                    self.data = task_data
                    self.archive(tid)
            self.data = self._minimal_schema()
            self.task_id = None
            self._task_path = None
            return

        tid = target_task_id or self.task_id
        if tid and self.data.get('task', {}).get('id'):
            self.archive(tid)

        # Clear current state
        self.data = self._minimal_schema()
        self.task_id = None
        self._task_path = None

    # ── Transition Condition Checks ──

    def _get_spec_dir(self, task_id=None):
        """Get the spec folder path for a task."""
        tid = task_id or self.task_id
        if not tid:
            return None
        return os.path.join(self.sdlc_dir, self.SPEC_DIR_NAME, tid)

    def _create_spec_folder(self, task_id=None):
        """Create the active/{task_id}/ folder with initial requirement.md template."""
        spec_dir = self._get_spec_dir(task_id)
        if not spec_dir:
            return None

        os.makedirs(spec_dir, exist_ok=True)

        task = self.data.get('task', {})
        template_vars = {
            'task_id': task.get('id', task_id or '?'),
            'summary': task.get('summary', ''),
            'task_type': task.get('type', '?'),
            'module': task.get('module', '?'),
            'priority': task.get('priority') or 'Not set',
            'ticket': task.get('ticket', task_id or '?'),
            'created_at': task.get('created_at', datetime.now().isoformat()),
            'description': task.get('description') or '<!-- Describe the task in detail -->',
        }

        # Create requirement.md (always created on task start)
        req_path = os.path.join(spec_dir, 'requirement.md')
        if not os.path.exists(req_path):
            with open(req_path, 'w', encoding='utf-8') as f:
                f.write(self.REQUIREMENT_TEMPLATE.format(**template_vars))

        return spec_dir

    def create_spec_file(self, spec_type, task_id=None):
        """Create a specific spec file (design, tasks, review) in the task's spec folder.

        Args:
            spec_type: 'design', 'tasks', or 'review'
            task_id: Optional task ID (defaults to current task)
        """
        spec_dir = self._get_spec_dir(task_id)
        if not spec_dir:
            raise ValueError('No active task. Start one first.')

        os.makedirs(spec_dir, exist_ok=True)

        task = self.data.get('task', {})
        template_vars = {
            'task_id': task.get('id', task_id or '?'),
            'summary': task.get('summary', ''),
            'task_type': task.get('type', '?'),
            'module': task.get('module', '?'),
            'priority': task.get('priority') or 'Not set',
            'ticket': task.get('ticket', '?'),
            'created_at': task.get('created_at', ''),
            'description': task.get('description') or '',
        }

        templates = {
            'design': ('design.md', self.DESIGN_TEMPLATE),
            'tasks': ('tasks.md', self.TASKS_TEMPLATE),
            'review': ('review.md', self.REVIEW_TEMPLATE),
        }

        if spec_type not in templates:
            raise ValueError(f'Unknown spec type: {spec_type}. Use: {list(templates.keys())}')

        filename, template = templates[spec_type]
        filepath = os.path.join(spec_dir, filename)

        if not os.path.exists(filepath):
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(template.format(**template_vars))

        return filepath

    def check_spec_file(self, filename, task_id=None):
        """Check if a spec file exists and has meaningful content.

        Returns:
            (exists: bool, has_content: bool, path: str)
        """
        spec_dir = self._get_spec_dir(task_id)
        if not spec_dir:
            return False, False, None

        filepath = os.path.join(spec_dir, filename)
        if not os.path.exists(filepath):
            return False, False, filepath

        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # Check for meaningful content (not just template placeholders)
        has_content = True
        if filename == 'requirement.md':
            # Must have at least one acceptance criterion with real text
            lines_with_checkbox = [l.strip() for l in content.split('\n')
                                   if l.strip().startswith('- [') and len(l.strip()) > 6]
            has_content = len(lines_with_checkbox) >= 1
        elif filename == 'review.md':
            # Must have a verdict line that says PASS (not PENDING)
            has_content = False
            for line in content.split('\n'):
                if 'Verdict:' in line:
                    verdict_text = line.split('Verdict:')[1].strip()
                    if verdict_text in ('PASS', 'PASS_WITH_ADVISORIES'):
                        has_content = True
                    break

        return True, has_content, filepath

    def complete_spec(self, task_id=None, updated_by=None):
        """Move a task's spec folder from active/ to done/."""
        tid = task_id or self.task_id
        if not tid:
            raise ValueError('No task ID specified.')

        src = os.path.join(self.sdlc_dir, self.SPEC_DIR_NAME, tid)
        dst = os.path.join(self.sdlc_dir, self.SPEC_DONE_DIR, tid)

        if os.path.exists(src):
            os.makedirs(os.path.dirname(dst), exist_ok=True)
            import shutil
            shutil.move(src, dst)
            self.log(f'Specs moved to done/{tid}/', updated_by)
            return dst
        return None

    def can_transition(self, target_phase):
        """Check if preconditions are met to transition to target_phase.

        Checks two layers:
        1. JSON state preconditions (existing)
        2. Spec file existence gates (v2.0)

        Returns:
            (ok: bool, reason: str)
        """
        # ── Layer 1: Spec file gates ──
        spec_gates = {
            'PLANNING': ('requirement.md', 'requirement.md must exist with acceptance criteria'),
            'CODING': ('tasks.md', 'tasks.md must exist (create during PLANNING)'),
            'REVIEW': (None, None),  # Check tasks.md completion instead
            'READY_TO_COMMIT': ('review.md', 'review.md must exist with verdict'),
        }

        gate = spec_gates.get(target_phase)
        if gate and gate[0]:
            exists, has_content, path = self.check_spec_file(gate[0])
            if not exists:
                return False, f'{gate[1]} — file not found at {path}'
            if not has_content:
                return False, f'{gate[1]} — file exists but needs content (not just template)'

        # ── Layer 2: JSON state preconditions ──
        conditions = {
            'PLANNING': lambda: (
                bool(self.data.get('requirement', {}).get('acceptance_criteria'))
                or self.data.get('requirement', {}).get('status') == 'completed'
                or True,  # Spec file gate already checked above
                'Requirement phase should be completed with acceptance criteria'
            ),
            'TEST_DESIGN': lambda: (
                self.data.get('plan', {}).get('approved_at') is not None
                or True,  # Allow if design.md exists
                'Plan must be approved before writing tests'
            ),
            'CODING': lambda: (
                self.data.get('test_design', {}).get('status') == 'completed'
                or self.data.get('test_design', {}).get('red_confirmed', False)
                or True,  # Spec file gate already checked tasks.md
                'Test design should be completed (red phase confirmed)'
            ),
            'TEST_EXECUTION': lambda: (
                self.data.get('coding', {}).get('status') == 'completed',
                'Coding should be completed before test execution'
            ),
            'REVIEW': lambda: (
                self.data.get('test_execution', {}).get('tests_failed', 1) == 0
                or True,  # Allow manual review
                'All tests must pass before review (tests_failed must be 0)'
            ),
            'READY_TO_COMMIT': lambda: (
                self.data.get('review', {}).get('verdict') in ('PASS', 'PASS_WITH_ADVISORIES')
                or True,  # Spec file gate checks review.md
                'Review verdict must be PASS or PASS_WITH_ADVISORIES'
            ),
        }

        check = conditions.get(target_phase)
        if check is None:
            return True, 'No preconditions for this phase'

        ok, reason = check()
        return ok, reason

    def transition_with_check(self, phase, updated_by=None, force=False):
        """Transition with precondition verification.

        If preconditions are not met, logs a warning but still allows the
        transition if force=True or if the user confirms.
        """
        ok, reason = self.can_transition(phase)
        if not ok:
            msg = f'⚠️ PRECONDITION NOT MET for {phase}: {reason}'
            self.log(msg, updated_by)
            print(f'  {msg}')
            if not force:
                print(f'  Use --force to override.')
                return False
        self.set_phase(phase, updated_by)
        return True

    # ── Undo Phase ──

    def undo_phase(self, updated_by=None):
        """Revert to the previous phase.

        Returns:
            (old_phase, new_phase) tuple, or None if no previous phase.
        """
        previous = self.data.get('phase', {}).get('previous')
        if not previous:
            return None

        current = self.get_phase()
        # Use set_phase with skip_ok to suppress skip warnings on undo
        self.set_phase(previous, updated_by, skip_ok=True)
        self.log(f'UNDO: {current} → {previous} (reverted)', updated_by)
        return (current, previous)

    # ── Metrics ──

    def compute_metrics(self):
        """Analyze archived tasks to compute pipeline metrics.

        Returns dict with:
            - total_tasks: count of archived tasks
            - by_type: {fix: N, feature: N, ...}
            - by_module: {billing: N, estimation: N, ...}
            - phase_durations: {REQUIREMENT: avg_seconds, PLANNING: avg_seconds, ...}
            - completion_rate: % of tasks that reached MERGED/CLOSED
            - most_skipped_phases: list of commonly skipped phases
            - avg_total_duration: average task total time in seconds
        """
        if not os.path.isdir(self.history_dir):
            return {'total_tasks': 0, 'error': 'No history directory found'}

        import glob
        files = glob.glob(os.path.join(self.history_dir, '*.json'))
        if not files:
            return {'total_tasks': 0, 'error': 'No archived tasks found'}

        tasks = []
        for f in files:
            try:
                with open(f, 'r', encoding='utf-8') as fh:
                    tasks.append(json.load(fh))
            except (json.JSONDecodeError, IOError):
                continue

        if not tasks:
            return {'total_tasks': 0, 'error': 'No valid archived tasks'}

        # Count by type and module
        by_type = {}
        by_module = {}
        final_phases = {}
        phase_durations = {}  # phase -> [duration_seconds, ...]
        total_durations = []

        for task_data in tasks:
            task_info = task_data.get('task', {})
            t_type = task_info.get('type', 'unknown')
            t_module = task_info.get('module', 'unknown')
            by_type[t_type] = by_type.get(t_type, 0) + 1
            by_module[t_module] = by_module.get(t_module, 0) + 1

            # Final phase reached
            final = task_data.get('phase', {}).get('current', 'UNKNOWN')
            final_phases[final] = final_phases.get(final, 0) + 1

            # Phase duration from history
            history = task_data.get('phase', {}).get('history', [])
            for i, entry in enumerate(history):
                phase_name = entry.get('to', '')
                phase_start = entry.get('at', '')

                # Find when this phase ended (next transition)
                if i + 1 < len(history):
                    phase_end = history[i + 1].get('at', '')
                else:
                    # Last phase — use file update time
                    phase_end = task_data.get('_updated_at', '')

                if phase_start and phase_end:
                    try:
                        start_dt = datetime.fromisoformat(phase_start)
                        end_dt = datetime.fromisoformat(phase_end)
                        duration = (end_dt - start_dt).total_seconds()
                        if duration >= 0:
                            phase_durations.setdefault(phase_name, []).append(duration)
                    except (ValueError, TypeError):
                        pass

            # Total task duration
            created = task_info.get('created_at', '')
            updated = task_data.get('_updated_at', '')
            if created and updated:
                try:
                    total_dur = (datetime.fromisoformat(updated) - datetime.fromisoformat(created)).total_seconds()
                    if total_dur >= 0:
                        total_durations.append(total_dur)
                except (ValueError, TypeError):
                    pass

        # Compute averages
        avg_durations = {}
        for phase, durations in phase_durations.items():
            if durations:
                avg_durations[phase] = {
                    'avg_seconds': round(sum(durations) / len(durations), 1),
                    'count': len(durations),
                    'min_seconds': round(min(durations), 1),
                    'max_seconds': round(max(durations), 1),
                }

        # Completion rate
        completed = sum(1 for p in final_phases if p in ('MERGED', 'CLOSED'))
        completion_rate = round((completed / len(tasks)) * 100) if tasks else 0

        # Most skipped phases (phases that rarely appear in history)
        all_phases_seen = set()
        for phase, info in avg_durations.items():
            all_phases_seen.add(phase)
        expected = {'DISCUSS', 'REQUIREMENT', 'PLANNING', 'TEST_DESIGN',
                    'CODING', 'TEST_EXECUTION', 'REVIEW'}
        skipped = expected - all_phases_seen

        return {
            'total_tasks': len(tasks),
            'by_type': by_type,
            'by_module': by_module,
            'final_phases': final_phases,
            'phase_durations': avg_durations,
            'completion_rate': completion_rate,
            'most_skipped_phases': sorted(skipped),
            'avg_total_duration': round(sum(total_durations) / len(total_durations), 1) if total_durations else 0,
        }

    def show_metrics(self):
        """Display pipeline metrics in a formatted view."""
        m = self.compute_metrics()

        if m.get('error'):
            print(f'  No metrics available: {m["error"]}')
            return

        def fmt_duration(seconds):
            if seconds < 60:
                return f'{seconds:.0f}s'
            elif seconds < 3600:
                return f'{seconds / 60:.1f}m'
            else:
                return f'{seconds / 3600:.1f}h'

        print()
        print(f'  Pipeline Metrics ({m["total_tasks"]} archived tasks)')
        print(f'  {"=" * 50}')
        print()

        # By type
        print(f'  Task Types:')
        for t, count in sorted(m['by_type'].items(), key=lambda x: -x[1]):
            bar = '#' * count
            print(f'    {t:12} {bar} ({count})')
        print()

        # By module
        print(f'  Modules:')
        for mod, count in sorted(m['by_module'].items(), key=lambda x: -x[1]):
            bar = '#' * count
            print(f'    {mod:12} {bar} ({count})')
        print()

        # Phase durations
        if m['phase_durations']:
            print(f'  Phase Durations (avg):')
            ordered_phases = ['DISCUSS', 'REQUIREMENT', 'PLANNING', 'TEST_DESIGN',
                              'CODING', 'TEST_EXECUTION', 'REVIEW', 'READY_TO_COMMIT']
            for phase in ordered_phases:
                info = m['phase_durations'].get(phase)
                if info:
                    avg = fmt_duration(info['avg_seconds'])
                    print(f'    {phase:18} {avg:>8} (n={info["count"]}, '
                          f'min={fmt_duration(info["min_seconds"])}, '
                          f'max={fmt_duration(info["max_seconds"])})')
                else:
                    print(f'    {phase:18}      --  (never reached)')
            print()

        # Final phases
        print(f'  Final Phase Reached:')
        for phase, count in sorted(m['final_phases'].items(), key=lambda x: -x[1]):
            print(f'    {phase:18} {count}')
        print()

        # Completion rate
        print(f'  Completion Rate: {m["completion_rate"]}%')
        if m['avg_total_duration']:
            print(f'  Avg Task Duration: {fmt_duration(m["avg_total_duration"])}')
        print()

        # Skipped phases
        if m['most_skipped_phases']:
            print(f'  Commonly Skipped Phases:')
            for phase in m['most_skipped_phases']:
                print(f'    - {phase}')
            print()

    # ── Restore from History ──

    def restore(self, archive_filename):
        """Restore state from an archived history file."""
        archive_path = os.path.join(self.history_dir, archive_filename)
        if not os.path.exists(archive_path):
            # Try without directory
            if os.path.exists(archive_filename):
                archive_path = archive_filename
            else:
                raise FileNotFoundError(f'Archive not found: {archive_filename}')

        # Backup current state first
        if self.data.get('task', {}).get('id'):
            self.archive()

        with open(archive_path, 'r', encoding='utf-8') as f:
            self.data = json.load(f)

        self.log('RESTORED from archive: ' + os.path.basename(archive_path), 'cli')
        self.save('cli')
        return archive_path

    def list_history(self, limit=10):
        """List archived tasks."""
        if not os.path.isdir(self.history_dir):
            return []

        import glob
        files = sorted(glob.glob(os.path.join(self.history_dir, '*.json')), reverse=True)
        result = []
        for f in files[:limit]:
            try:
                with open(f, 'r', encoding='utf-8') as fh:
                    data = json.load(fh)
                result.append({
                    'filename': os.path.basename(f),
                    'task_id': data.get('task', {}).get('id', '?'),
                    'type': data.get('task', {}).get('type', '?'),
                    'module': data.get('task', {}).get('module', '?'),
                    'summary': data.get('task', {}).get('summary', ''),
                    'final_phase': data.get('phase', {}).get('current', '?'),
                    'updated_at': data.get('_updated_at', ''),
                })
            except (json.JSONDecodeError, IOError):
                continue
        return result

    # ── Display ──

    def show_registry(self):
        """Print a summary of all tasks grouped by status."""
        all_tasks = self.registry.get('active_tasks', [])
        print()
        if not all_tasks:
            print('  No tasks. Options:')
            print('    sdlc start-task -t fix -m billing -s "description"   # start immediately')
            print('    sdlc queue -t fix -m billing -s "description"        # add to backlog')
            print()
            return

        phase_icons = {
            'IDLE': '⬜', 'DISCUSS': '🗣️', 'REQUIREMENT': '📋',
            'PLANNING': '📐', 'TEST_DESIGN': '🧪', 'CODING': '💻',
            'TEST_EXECUTION': '✅', 'REVIEW': '🔍', 'READY_TO_COMMIT': '📦',
            'PR_OPEN': '🔀', 'MERGED': '✅', 'CLOSED': '🏁', 'VIBE': '🎯'
        }
        priority_icons = {'critical': '🔴', 'high': '🟠', 'medium': '🟡', 'low': '🟢'}

        active = [t for t in all_tasks if t.get('status', 'active') == 'active']
        pending = [t for t in all_tasks if t.get('status') == 'pending']
        paused = [t for t in all_tasks if t.get('status') == 'paused']

        if active:
            print(f'  📌 Active ({len(active)}):')
            for t in active:
                tid = (t.get('id') or '?')[:13]
                phase = t.get('phase', 'IDLE')
                icon = phase_icons.get(phase, '❓')
                mod = (t.get('module') or '?')[:9]
                summary = (t.get('summary') or '')[:30]
                is_default = '*' if t.get('id') == self.registry.get('default_task') else ' '
                print(f'  {is_default} {tid:<13} {icon} {phase:<15} {mod:<10} {summary}')
            print()

        if pending:
            # Sort by priority
            prio_order = {'critical': 0, 'high': 1, 'medium': 2, 'low': 3, None: 4}
            pending.sort(key=lambda t: prio_order.get(t.get('priority'), 4))
            print(f'  📋 Backlog ({len(pending)}):')
            for i, t in enumerate(pending, 1):
                tid = (t.get('id') or '?')[:13]
                mod = (t.get('module') or '?')[:9]
                summary = (t.get('summary') or '')[:30]
                prio = t.get('priority')
                picon = priority_icons.get(prio, '  ') if prio else '  '
                src = f' [{t["source"]}]' if t.get('source') else ''
                print(f'    {i}. {picon} {tid:<13} {t.get("type","?"):<8} {mod:<10} {summary}{src}')
            print()

        if paused:
            print(f'  ⏸️  Paused ({len(paused)}):')
            for t in paused:
                tid = (t.get('id') or '?')[:13]
                phase = t.get('phase', 'IDLE')
                summary = (t.get('summary') or '')[:30]
                print(f'    {tid:<13} {phase:<15} {summary}')
            print()

        print(f'  Commands:')
        if pending:
            print(f'    sdlc pick <ID>              # activate a backlog task')
        print(f'    sdlc show --task <ID>        # show task details')
        print(f'    sdlc queue -t fix -m mod -s "desc"  # add to backlog')
        if active:
            print(f'    * = default task')
        print()

    def show(self):
        """Print a compact status display.

        If no task is loaded (multiple tasks or zero), shows registry summary.
        If a task is loaded, shows the single-task detail view.
        """
        # If no task loaded, show the multi-task registry
        if not self.task_id:
            self.show_registry()
            return

        task = self.data.get('task', {})
        phase = self.data.get('phase', {})
        cs = self.data.get('changeset', {})
        commit = self.data.get('commit', {})

        phase_icons = {
            'IDLE': '⬜', 'DISCUSS': '🗣️', 'REQUIREMENT': '📋',
            'PLANNING': '📐', 'TEST_DESIGN': '🧪', 'CODING': '💻',
            'TEST_EXECUTION': '✅', 'REVIEW': '🔍', 'READY_TO_COMMIT': '📦',
            'PR_OPEN': '🔀', 'MERGED': '✅', 'CLOSED': '🏁'
        }

        current = phase.get('current', 'IDLE')
        icon = phase_icons.get(current, '❓')

        print()
        print(f'  ╔══════════════════════════════════════════╗')
        print(f'  ║  {icon} Pipeline: {current:<28}  ║')
        print(f'  ╠══════════════════════════════════════════╣')

        if task.get('id'):
            print(f'  ║  Task:     {task.get("id", "?"):<30}║')
            t = task.get('type', '?')
            m = task.get('module', '?')
            s = (task.get('summary', '') or '')[:28]
            print(f'  ║  {t}({m}): {s:<24}║')
            if task.get('priority'):
                print(f'  ║  Priority: {task["priority"]:<30}║')
        else:
            print(f'  ║  No active task                         ║')

        print(f'  ╠══════════════════════════════════════════╣')

        # Phase progress
        stages = ['DISCUSS', 'REQUIREMENT', 'PLANNING', 'TEST_DESIGN', 'CODING', 'TEST_EXECUTION', 'REVIEW', 'READY_TO_COMMIT', 'MERGED']
        stage_names = ['Talk', 'Req', 'Plan', 'Test', 'Code', 'Verify', 'Review', 'Commit', 'Ship']
        current_idx = stages.index(current) if current in stages else -1

        progress = '  ║  '
        for i, name in enumerate(stage_names):
            if i < current_idx:
                progress += f'✅'
            elif i == current_idx:
                progress += f'🔵'
            else:
                progress += f'⬜'
        progress += '                          ║'
        print(progress)

        labels = '  ║  '
        for name in stage_names:
            labels += f'{name[:3]:4}'
        labels += '                      ║'
        print(labels)

        print(f'  ╠══════════════════════════════════════════╣')

        if commit.get('hash'):
            print(f'  ║  Commit:  {commit["hash_short"]:<31}║')
        if cs.get('ticket'):
            print(f'  ║  Ticket:  {cs["ticket"]:<31}║')

        # Timeline (last 3 entries)
        timeline = self.data.get('timeline', [])
        if timeline:
            print(f'  ╠══════════════════════════════════════════╣')
            for entry in timeline[-3:]:
                msg = entry['message'][:38]
                print(f'  ║  • {msg:<38}║')

        print(f'  ╚══════════════════════════════════════════╝')
        print()


    # ── Sub-task Management ──

    def create_subtask(self, subtask_id, summary, phase_scope=None, updated_by=None):
        """Create a sub-task under the current parent task.

        Args:
            subtask_id: Short identifier (e.g., '1', 'model', 'js')
            summary: What this sub-task covers
            phase_scope: Which phases this sub-task covers (e.g., ['CODING', 'TEST_EXECUTION'])
        """
        parent_id = self.data.get('task', {}).get('id')
        if not parent_id:
            raise ValueError('No active parent task. Run start-task first.')

        # Ensure tasks directory
        tasks_dir = os.path.join(self.sdlc_dir, 'tasks', parent_id)
        os.makedirs(tasks_dir, exist_ok=True)

        # Save parent reference in main state
        self.data.setdefault('subtasks', [])
        full_id = f'{parent_id}-{subtask_id}'

        subtask_entry = {
            'id': full_id,
            'summary': summary,
            'status': 'pending',
            'phase_scope': phase_scope or list(VALID_PHASES),
            'conversation_id': None,
            'created_at': datetime.now().isoformat(),
            'completed_at': None,
        }

        # Check for duplicate
        existing_ids = [s['id'] for s in self.data['subtasks']]
        if full_id in existing_ids:
            raise ValueError(f'Sub-task {full_id} already exists.')

        self.data['subtasks'].append(subtask_entry)

        # Create sub-task state file
        subtask_state = self._load_schema()
        subtask_state['task'] = {
            'id': full_id,
            'parent_id': parent_id,
            'type': self.data['task'].get('type'),
            'module': self.data['task'].get('module'),
            'summary': summary,
            'ticket': self.data['task'].get('ticket'),
            'priority': self.data['task'].get('priority'),
            'track': self.data['task'].get('track'),
            'description': None,
            'requested_by': None,
            'created_at': datetime.now().isoformat(),
        }
        subtask_state['changeset'] = copy.deepcopy(self.data.get('changeset', {}))
        subtask_state['changeset']['summary'] = summary

        subtask_path = os.path.join(tasks_dir, f'{full_id}.json')
        with open(subtask_path, 'w', encoding='utf-8') as f:
            json.dump(subtask_state, f, indent=2, ensure_ascii=False)

        self.log(f'Sub-task created: {full_id} — {summary}', updated_by)
        self.save(updated_by)
        return full_id

    def list_subtasks(self):
        """List all sub-tasks for the current parent task."""
        return self.data.get('subtasks', [])

    def get_subtask_state(self, subtask_id):
        """Load a sub-task's pipeline state."""
        parent_id = self.data.get('task', {}).get('id')
        if not parent_id:
            return None

        tasks_dir = os.path.join(self.sdlc_dir, 'tasks', parent_id)
        full_id = subtask_id if parent_id in subtask_id else f'{parent_id}-{subtask_id}'
        subtask_path = os.path.join(tasks_dir, f'{full_id}.json')

        if os.path.exists(subtask_path):
            with open(subtask_path, 'r', encoding='utf-8') as f:
                return json.load(f)
        return None

    def activate_subtask(self, subtask_id, conversation_id=None, updated_by=None):
        """Set a sub-task as the active work item.
        Copies sub-task state into pipeline.json so all workflows operate on it.
        """
        parent_id = self.data.get('task', {}).get('id')
        if not parent_id:
            raise ValueError('No active parent task.')

        full_id = subtask_id if parent_id in subtask_id else f'{parent_id}-{subtask_id}'

        # Load sub-task state
        sub_state = self.get_subtask_state(full_id)
        if not sub_state:
            raise ValueError(f'Sub-task {full_id} not found.')

        # Mark as active
        for st in self.data.get('subtasks', []):
            if st['id'] == full_id:
                st['status'] = 'in_progress'
                st['conversation_id'] = conversation_id
                break

        # Set active subtask reference
        self.data['active_subtask'] = full_id
        self.log(f'Activated sub-task: {full_id}', updated_by)
        self.save(updated_by)

    def complete_subtask(self, subtask_id, updated_by=None):
        """Mark a sub-task as completed."""
        parent_id = self.data.get('task', {}).get('id')
        if not parent_id:
            return

        full_id = subtask_id if parent_id in subtask_id else f'{parent_id}-{subtask_id}'

        for st in self.data.get('subtasks', []):
            if st['id'] == full_id:
                st['status'] = 'completed'
                st['completed_at'] = datetime.now().isoformat()
                break

        # Check if all subtasks are done
        all_done = all(
            s.get('status') == 'completed'
            for s in self.data.get('subtasks', [])
        )

        if all_done and self.data.get('subtasks'):
            self.log(f'All sub-tasks completed! Parent task ready for final review.', updated_by)

        self.data['active_subtask'] = None
        self.log(f'Sub-task completed: {full_id}', updated_by)
        self.save(updated_by)

    def get_parent_progress(self):
        """Get parent task progress based on sub-task completion."""
        subtasks = self.data.get('subtasks', [])
        if not subtasks:
            return {'total': 0, 'completed': 0, 'in_progress': 0, 'pending': 0, 'percent': 0}

        completed = sum(1 for s in subtasks if s.get('status') == 'completed')
        in_progress = sum(1 for s in subtasks if s.get('status') == 'in_progress')
        pending = sum(1 for s in subtasks if s.get('status') == 'pending')
        total = len(subtasks)
        percent = int((completed / total) * 100) if total > 0 else 0

        return {
            'total': total,
            'completed': completed,
            'in_progress': in_progress,
            'pending': pending,
            'percent': percent
        }

    # ── Context Health ──

    @staticmethod
    def estimate_context_health(conversation_id=None):
        """Estimate context window usage from conversation transcript.

        Returns a dict with:
            - estimated_tokens: rough token count
            - percent_used: estimated % of context window used
            - status: 'healthy' / 'warning' / 'critical'
            - recommendation: what to do
        """
        # Gemini 2.5 Pro context window ≈ 1M tokens
        MAX_TOKENS = 1_000_000
        WARN_THRESHOLD = 0.60   # 60% — start thinking about new session
        CRITICAL_THRESHOLD = 0.80  # 80% — definitely start new session

        transcript_size = 0
        message_count = 0
        tool_call_count = 0

        # Try to find transcript
        home = os.path.expanduser('~')
        brain_dir = os.path.join(home, '.gemini', 'antigravity-ide', 'brain')

        if conversation_id:
            transcript = os.path.join(
                brain_dir, conversation_id,
                '.system_generated', 'logs', 'transcript.jsonl'
            )
            if os.path.exists(transcript):
                transcript_size = os.path.getsize(transcript)
                try:
                    with open(transcript, 'r', encoding='utf-8', errors='ignore') as f:
                        for line in f:
                            message_count += 1
                            if '"tool_calls"' in line:
                                tool_call_count += 1
                except Exception:
                    pass
        else:
            # Try to find the most recent/largest conversation
            if os.path.exists(brain_dir):
                largest_size = 0
                for conv_dir in os.listdir(brain_dir):
                    t = os.path.join(
                        brain_dir, conv_dir,
                        '.system_generated', 'logs', 'transcript.jsonl'
                    )
                    if os.path.exists(t):
                        size = os.path.getsize(t)
                        if size > largest_size:
                            largest_size = size
                            transcript_size = size
                            conversation_id = conv_dir

        # Estimate tokens (1 token ≈ 4 chars, JSON overhead ≈ 40% of transcript)
        estimated_tokens = int((transcript_size * 0.6) / 4)

        # Context includes more than just transcript (system prompt, rules, etc.)
        # Add ~50K baseline for system context
        estimated_tokens += 50_000

        percent_used = estimated_tokens / MAX_TOKENS

        if percent_used >= CRITICAL_THRESHOLD:
            status = 'critical'
            recommendation = (
                'Context window is nearly full. Start a new session NOW.\n'
                'Run /pause-work to save state, then /resume-work in new session.'
            )
        elif percent_used >= WARN_THRESHOLD:
            status = 'warning'
            recommendation = (
                'Context window is filling up. Consider starting a new session soon.\n'
                'Run /pause-work to save state before context degrades.'
            )
        else:
            status = 'healthy'
            recommendation = 'Context window is healthy. Continue working.'

        return {
            'conversation_id': conversation_id,
            'transcript_bytes': transcript_size,
            'message_count': message_count,
            'tool_call_count': tool_call_count,
            'estimated_tokens': estimated_tokens,
            'max_tokens': MAX_TOKENS,
            'percent_used': round(percent_used * 100, 1),
            'status': status,
            'recommendation': recommendation,
        }

    def show_context_health(self, conversation_id=None):
        """Display context health as a visual bar."""
        health = self.estimate_context_health(conversation_id)

        status_colors = {
            'healthy': '🟢',
            'warning': '🟡',
            'critical': '🔴',
        }
        icon = status_colors.get(health['status'], '❓')
        pct = health['percent_used']
        bar_len = 30
        filled = int(bar_len * min(pct, 100) / 100)
        bar = '█' * filled + '░' * (bar_len - filled)

        print()
        print(f'  {icon} Context Health: {health["status"].upper()}')
        print(f'  [{bar}] {pct}%')
        print(f'  Tokens: ~{health["estimated_tokens"]:,} / {health["max_tokens"]:,}')
        print(f'  Messages: {health["message_count"]} | Tool calls: {health["tool_call_count"]}')
        print(f'  {health["recommendation"]}')
        print()


# ══════════════════════════════════════════════════════════════
# CLI Interface
# ══════════════════════════════════════════════════════════════

def main():
    parser = argparse.ArgumentParser(description=f'SDLC Pipeline State Manager v{VERSION}')
    parser.add_argument('--version', '-v', action='version', version=f'sdlc v{VERSION}')
    parser.add_argument('--task', default=None, help='Task ID to operate on (required when multiple tasks active)')
    sub = parser.add_subparsers(dest='command')

    # show
    sub.add_parser('show', help='Show current state (or all tasks if no --task)')

    # list-tasks
    sub.add_parser('list-tasks', help='List all tasks (active + backlog)')

    # queue — add task to backlog
    p_queue = sub.add_parser('queue', help='Add a task to the backlog (pending)')
    p_queue.add_argument('--type', '-t', required=True)
    p_queue.add_argument('--module', '-m', required=True)
    p_queue.add_argument('--summary', '-s', required=True)
    p_queue.add_argument('--priority', '-p', default=None, choices=['critical', 'high', 'medium', 'low'])
    p_queue.add_argument('--ticket', default=None)
    p_queue.add_argument('--source', default=None, help='Source: github, gitea, manual, pm')
    p_queue.add_argument('--source-url', default=None)

    # pick — activate a pending task
    p_pick = sub.add_parser('pick', help='Activate a backlog task (pending → active)')
    p_pick.add_argument('task_id', help='Task ID to activate')

    # backlog — show only pending tasks
    sub.add_parser('backlog', help='Show backlog (pending tasks only)')

    # spec commands
    p_cs = sub.add_parser('create-spec', help='Create a spec file (design, tasks, review)')
    p_cs.add_argument('spec_type', choices=['design', 'tasks', 'review'])

    sub.add_parser('spec-status', help='Show spec file status for current task')

    p_done = sub.add_parser('done', help='Complete task — move specs from active/ to done/')

    # phase
    sub.add_parser('phase', help='Show current phase')

    # set-phase
    p_sp = sub.add_parser('set-phase', help='Set current phase')
    p_sp.add_argument('phase', choices=VALID_PHASES)

    # start-task
    p_st = sub.add_parser('start-task', help='Start a new task')
    p_st.add_argument('--type', '-t', required=True)
    p_st.add_argument('--module', '-m', required=True)
    p_st.add_argument('--summary', '-s', required=True)
    p_st.add_argument('--ticket', default=None)
    p_st.add_argument('--priority', default=None)

    # log
    p_log = sub.add_parser('log', help='Add timeline entry')
    p_log.add_argument('message')

    # set
    p_set = sub.add_parser('set', help='Set a field by dot path')
    p_set.add_argument('path', help='Dot path, e.g., task.module')
    p_set.add_argument('value', help='Value to set')

    # get
    p_get = sub.add_parser('get', help='Get a field by dot path')
    p_get.add_argument('path', help='Dot path, e.g., task.module')

    # reset
    p_reset = sub.add_parser('reset', help='Reset state for new task')
    p_reset.add_argument('--all', action='store_true', help='Reset all active tasks')

    # export
    sub.add_parser('export', help='Export full state as JSON')

    # sub-task commands
    p_sub = sub.add_parser('add-subtask', help='Create a sub-task')
    p_sub.add_argument('subtask_id', help='Short ID (e.g., 1, model, js)')
    p_sub.add_argument('--summary', '-s', required=True)

    sub.add_parser('subtasks', help='List all sub-tasks')

    p_activate = sub.add_parser('activate-subtask', help='Set active sub-task')
    p_activate.add_argument('subtask_id')
    p_activate.add_argument('--conversation', default=None)

    p_complete = sub.add_parser('complete-subtask', help='Mark sub-task done')
    p_complete.add_argument('subtask_id')

    # context health
    p_ctx = sub.add_parser('context', help='Show context health')
    p_ctx.add_argument('--conversation', default=None, help='Conversation ID')

    # metrics
    p_metrics = sub.add_parser('metrics', help='Show pipeline metrics from history')
    p_metrics.add_argument('--json', action='store_true', help='Output as JSON')

    # undo
    sub.add_parser('undo', help='Revert to previous phase')

    # quick — one-command task start for common scenarios
    p_quick = sub.add_parser('quick', help='Quick start: create task + set VIBE mode')
    p_quick.add_argument('task_type', choices=['fix', 'feature', 'hotfix', 'refactor'])
    p_quick.add_argument('module')
    p_quick.add_argument('summary')
    p_quick.add_argument('--ticket', default=None)
    p_quick.add_argument('--priority', default=None)

    # transition — set phase with precondition check
    p_trans = sub.add_parser('transition', help='Set phase with precondition check')
    p_trans.add_argument('phase', choices=VALID_PHASES)
    p_trans.add_argument('--force', action='store_true', help='Force transition even if preconditions fail')

    # history — list archived tasks
    p_hist = sub.add_parser('history', help='List archived tasks')
    p_hist.add_argument('--limit', '-n', type=int, default=10)

    # restore — restore from archive
    p_restore = sub.add_parser('restore', help='Restore state from history archive')
    p_restore.add_argument('filename', help='Archive filename from history/')

    # check-transition — dry-run check if transition is allowed
    p_check = sub.add_parser('check-transition', help='Check if transition preconditions are met')
    p_check.add_argument('phase', choices=VALID_PHASES)

    # fast-track — create a fast-track task (shortened pipeline)
    p_fast = sub.add_parser('fast-track', help='Start a fast-track task (max 3 files, no tests)')
    p_fast.add_argument('task_type', choices=['hotfix', 'typo', 'config', 'docs'])
    p_fast.add_argument('module')
    p_fast.add_argument('summary')
    p_fast.add_argument('--ticket', default=None)

    # validate-commit — pre-commit hook integration
    p_vc = sub.add_parser('validate-commit', help='Validate pipeline state for commit (used by pre-commit hook)')
    p_vc.add_argument('--staged-files', nargs='*', default=[], help='Files staged for commit')

    # get-prompt — generate focused role prompt for current phase
    p_gp = sub.add_parser('get-prompt', help='Generate focused role instructions for current phase')
    p_gp.add_argument('--sub-role', default=None,
                       choices=['investigator', 'bug_fixer', 'builder', 'architect',
                                'code_reviewer', 'database_analyst', 'documenter', 'tester'],
                       help='VIBE sub-role to generate prompt for')

    # suggest-next — recommend next action (BMad-style help)
    sub.add_parser('suggest-next', help='Suggest what to do next based on current state')

    # add-decision — record a resolved decision
    p_dec = sub.add_parser('add-decision', help='Record a resolved decision (prevents re-asking)')
    p_dec.add_argument('key', help='Decision identifier (e.g., db_approach, validation_strategy)')
    p_dec.add_argument('value', help='The resolved decision value')
    p_dec.add_argument('--reason', default=None, help='Rationale for the decision')

    # decisions — list all decisions
    sub.add_parser('decisions', help='List all resolved decisions')

    # clear-decisions — clear all decisions
    sub.add_parser('clear-decisions', help='Clear all resolved decisions')

    # pause — create handoff file for cross-conversation resume
    p_pause = sub.add_parser('pause', help='Create handoff file for session pause')
    p_pause.add_argument('--reason', '-r', default=None, help='Why work is being paused')

    # resume — show handoff file contents
    sub.add_parser('resume', help='Show handoff file from previous session')

    # clear-handoff — delete handoff file after resuming
    sub.add_parser('clear-handoff', help='Delete handoff file after resuming')

    args = parser.parse_args()

    # Create state with task_id from --task flag
    task_id = getattr(args, 'task', None)
    state = PipelineState(task_id=task_id)

    if args.command == 'show':
        state.show()
    elif args.command == 'list-tasks':
        state.show_registry()
    elif args.command == 'queue':
        tid = args.ticket or f'{args.module[:3].upper()}-{datetime.now().strftime("%H%M%S")}'
        state.queue_task(tid, args.type, args.module, args.summary,
                         priority=args.priority, source=args.source,
                         source_url=getattr(args, 'source_url', None))
        print(f'Queued: [{tid}] {args.type}({args.module}): {args.summary}')
        if args.priority:
            print(f'Priority: {args.priority}')
        print(f'Activate with: sdlc pick {tid}')
    elif args.command == 'pick':
        state.pick_task(args.task_id, updated_by='cli')
        print(f'Activated: {args.task_id}')
        state.show()
    elif args.command == 'backlog':
        pending = state.list_backlog()
        if not pending:
            print('Backlog is empty. Add tasks with: sdlc queue -t fix -m billing -s "desc"')
        else:
            prio_icons = {'critical': '\U0001f534', 'high': '\U0001f7e0', 'medium': '\U0001f7e1', 'low': '\U0001f7e2'}
            print(f'\n  Backlog ({len(pending)} pending):\n')
            for i, t in enumerate(pending, 1):
                picon = prio_icons.get(t.get('priority'), '  ')
                src = f' [{t["source"]}]' if t.get('source') else ''
                print(f'    {i}. {picon} [{t["id"]}] {t["type"]}({t["module"]}): {t["summary"]}{src}')
            print(f'\n  Activate: sdlc pick <ID>\n')
    elif args.command == 'create-spec':
        path = state.create_spec_file(args.spec_type)
        print(f'Created: {path}')
    elif args.command == 'spec-status':
        tid = state.task_id or '(none)'
        spec_dir = state._get_spec_dir()
        print(f'\n  Spec Status for: {tid}\n')
        if not spec_dir or not os.path.isdir(spec_dir):
            print(f'  No spec folder found at .sdlc/active/{tid}/')
            print(f'  Start a task first: sdlc start-task ...')
        else:
            spec_files = ['requirement.md', 'design.md', 'tasks.md', 'review.md']
            for sf in spec_files:
                exists, has_content, path = state.check_spec_file(sf)
                if exists and has_content:
                    icon = '\u2705'
                elif exists:
                    icon = '\ud83d\udfe1'  # exists but template-only
                else:
                    icon = '\u2b1c'
                status = 'ready' if has_content else ('template' if exists else 'missing')
                print(f'    {icon} {sf:<20} {status}')
        print()
    elif args.command == 'done':
        tid = state.task_id
        if not tid:
            print('No active task to complete.')
        else:
            result = state.complete_spec(tid, updated_by='cli')
            if result:
                print(f'Task {tid} completed. Specs moved to: {result}')
                state.reset(tid)
            else:
                print(f'No spec folder found for {tid}.')
    elif args.command == 'phase':
        print(state.get_phase())
    elif args.command == 'set-phase':
        state.set_phase(args.phase, 'cli')
        print(f'Phase set to: {args.phase}')
    elif args.command == 'start-task':
        state.start_task(args.type, args.module, args.summary,
                         ticket=args.ticket, priority=args.priority,
                         updated_by='cli')
        print(f'Task started: {args.type}({args.module}): {args.summary}')
    elif args.command == 'log':
        state.log(args.message, 'cli')
        state.save('cli')
        print(f'Logged: {args.message}')
    elif args.command == 'set':
        try:
            val = json.loads(args.value)
        except (json.JSONDecodeError, ValueError):
            val = args.value
        state.set(args.path, val, 'cli')
        print(f'Set {args.path} = {val}')
    elif args.command == 'get':
        val = state.get(args.path)
        if isinstance(val, (dict, list)):
            print(json.dumps(val, indent=2))
        else:
            print(val)
    elif args.command == 'reset':
        if getattr(args, 'all', False):
            state.reset('__all__')
            print('All tasks archived and reset.')
        else:
            tid = state.task_id
            state.reset(tid)
            print(f'Task {tid or "current"} archived and reset.')
    elif args.command == 'export':
        print(json.dumps(state.data, indent=2, ensure_ascii=False))
    elif args.command == 'add-subtask':
        full_id = state.create_subtask(args.subtask_id, args.summary, updated_by='cli')
        print(f'Sub-task created: {full_id}')
    elif args.command == 'subtasks':
        subtasks = state.list_subtasks()
        progress = state.get_parent_progress()
        if not subtasks:
            print('No sub-tasks. Use add-subtask to create one.')
        else:
            print(f'\nParent: {state.data.get("task", {}).get("id")} — '
                  f'{progress["completed"]}/{progress["total"]} done ({progress["percent"]}%)\n')
            status_icons = {'pending': '⬜', 'in_progress': '🔄', 'completed': '✅'}
            for st in subtasks:
                icon = status_icons.get(st['status'], '❓')
                conv = f' [conv: {st["conversation_id"][:8]}...]' if st.get('conversation_id') else ''
                print(f'  {icon} {st["id"]}: {st["summary"]}{conv}')
            print()
    elif args.command == 'activate-subtask':
        state.activate_subtask(args.subtask_id,
                                conversation_id=args.conversation,
                                updated_by='cli')
        print(f'Activated sub-task: {args.subtask_id}')
    elif args.command == 'complete-subtask':
        state.complete_subtask(args.subtask_id, updated_by='cli')
        print(f'Completed sub-task: {args.subtask_id}')
    elif args.command == 'context':
        state.show_context_health(args.conversation if hasattr(args, 'conversation') else None)
    elif args.command == 'metrics':
        if hasattr(args, 'json') and args.json:
            print(json.dumps(state.compute_metrics(), indent=2))
        else:
            state.show_metrics()
    elif args.command == 'undo':
        result = state.undo_phase('cli')
        if result:
            print(f'Undone: {result[0]} -> {result[1]}')
        else:
            print('Nothing to undo — no previous phase recorded.')
    elif args.command == 'quick':
        state.start_task(args.task_type, args.module, args.summary,
                         ticket=args.ticket, priority=args.priority,
                         updated_by='cli')
        state.set_phase('VIBE', 'cli')
        print(f'Quick start: {args.task_type}({args.module}): {args.summary}')
        print(f'Phase set to: VIBE (auto-routing active)')
    elif args.command == 'transition':
        force = getattr(args, 'force', False)
        ok = state.transition_with_check(args.phase, 'cli', force=force)
        if ok:
            print(f'Transitioned to: {args.phase}')
    elif args.command == 'history':
        items = state.list_history(args.limit)
        if not items:
            print('No archived tasks found.')
        else:
            print(f'\n  Archived Tasks (latest {len(items)}):\n')
            for item in items:
                phase_icon = {'MERGED': 'done', 'CLOSED': 'done'}.get(item['final_phase'], item['final_phase'])
                summary = (item['summary'] or '')[:40]
                print(f'  {item["task_id"]:12} {item["type"]:8} {item["module"]:12} '
                      f'{phase_icon:18} {summary}')
            print(f'\n  Restore with: python .sdlc/pipeline_state.py restore <filename>')
            print()
    elif args.command == 'restore':
        try:
            path = state.restore(args.filename)
            print(f'Restored from: {os.path.basename(path)}')
            state.show()
        except FileNotFoundError as e:
            print(f'Error: {e}')
    elif args.command == 'check-transition':
        ok, reason = state.can_transition(args.phase)
        if ok:
            print(f'Ready to transition to {args.phase}')
        else:
            print(f'NOT READY for {args.phase}: {reason}')
        sys.exit(0 if ok else 1)
    elif args.command == 'fast-track':
        state.start_task(args.task_type, args.module, args.summary,
                         ticket=args.ticket, updated_by='cli',
                         fast_track=True)
        print(f'Fast-track started: {args.task_type}({args.module}): {args.summary}')
        print(f'Pipeline: DISCUSS -> CODING -> REVIEW -> COMMIT (max 3 files)')
    elif args.command == 'validate-commit':
        # Used by pre-commit hook to check SDLC pipeline state
        phase = state.get_phase()
        task = state.data.get('task', {})
        errors = []
        warnings = []

        # 1. Check if there's an active task
        if not task.get('id') and phase == 'IDLE':
            warnings.append('No active SDLC task. Consider starting one for traceability.')

        # 2. Check if we're in a commit-ready phase
        commit_ok_phases = ['READY_TO_COMMIT', 'PR_OPEN', 'MERGED', 'IDLE', 'VIBE', 'CODING']
        if task.get('id') and phase not in commit_ok_phases:
            warnings.append(f'Pipeline is in {phase} phase. Expected READY_TO_COMMIT for formal commits.')

        # 3. Compare staged files to planned changeset
        staged = getattr(args, 'staged_files', []) or []
        planned = state.data.get('changeset', {}).get('files', [])
        if staged and planned:
            staged_set = set(staged)
            planned_set = set(planned)
            unplanned = staged_set - planned_set
            if unplanned:
                warnings.append(f'Unplanned files in commit: {", ".join(sorted(unplanned)[:5])}')

        # 4. Fast-track file count check
        if state.is_fast_track() and staged:
            if len(staged) > state.FAST_TRACK_MAX_FILES:
                errors.append(f'Fast-track max {state.FAST_TRACK_MAX_FILES} files, but {len(staged)} staged. '
                             f'Upgrade to full pipeline.')

        # Output for pre-commit hook consumption
        result = {
            'phase': phase,
            'task_id': task.get('id'),
            'fast_track': state.is_fast_track(),
            'errors': errors,
            'warnings': warnings,
            'ok': len(errors) == 0,
        }
        print(json.dumps(result))
        sys.exit(0 if not errors else 1)
    elif args.command == 'get-prompt':
        prompt = state.get_prompt(sub_role=args.sub_role)
        print(prompt)
    elif args.command == 'suggest-next':
        suggestions = state.suggest_next()
        print()
        for s in suggestions:
            print(f'  {s}')
        print()
    elif args.command == 'add-decision':
        result = state.add_decision(args.key, args.value, reason=args.reason)
        print(result)
    elif args.command == 'decisions':
        lines = state.list_decisions()
        print()
        for line in lines:
            print(f'  {line}')
        print()
    elif args.command == 'clear-decisions':
        state.clear_decisions()
        print('All decisions cleared.')
    elif args.command == 'pause':
        path = state.pause(reason=args.reason)
        print(f'Handoff file created: {path}')
        print('Share this file with the next conversation for context.')
    elif args.command == 'resume':
        content = state.resume()
        if content:
            print(content)
        else:
            print('No handoff file found. Nothing to resume.')
    elif args.command == 'clear-handoff':
        if state.clear_handoff():
            print('Handoff file deleted.')
        else:
            print('No handoff file to delete.')
    else:
        parser.print_help()


if __name__ == '__main__':
    # Fix Windows encoding: box-drawing chars + emojis crash on cp1252
    if sys.platform == 'win32' and hasattr(sys.stdout, 'reconfigure'):
        try:
            sys.stdout.reconfigure(encoding='utf-8', errors='replace')
            sys.stderr.reconfigure(encoding='utf-8', errors='replace')
        except Exception:
            pass
    main()

