#!/usr/bin/env python3
"""
SDLC File Permission Validator
================================
Hard enforcement layer for file permissions per pipeline phase.
Call this BEFORE any file write operation.

Usage:
    python .sdlc/validate_write.py <file_path>
    python .sdlc/validate_write.py <file_path> --phase CODING
    python .sdlc/validate_write.py <file_path> --sub-role bug_fixer
    python .sdlc/validate_write.py --check-all <file1> <file2> ...

Exit codes:
    0 = ALLOWED
    1 = BLOCKED
    2 = ERROR (couldn't determine)

Examples:
    python .sdlc/validate_write.py application/controllers/billing.php
    # → Checks against current phase in pipeline.json

    python .sdlc/validate_write.py application/controllers/billing.php --phase DISCUSS
    # → BLOCKED: DISCUSS phase cannot modify controllers.

    python .sdlc/validate_write.py application/tests/TestBilling.php --phase TEST_DESIGN
    # → ALLOWED: application/tests/** matches TEST_DESIGN permissions.
"""

import argparse
import json
import os
import sys
from fnmatch import fnmatch
from pathlib import Path


SDLC_DIR = '.sdlc'
STATE_FILE = 'pipeline.json'
ROLES_FILE = 'roles.json'

# Patterns that are always allowed (pipeline state, artifacts)
ALWAYS_ALLOWED = [
    '.sdlc/pipeline.json',
]

# Patterns that indicate "artifacts only" — these are write-safe non-source files
ARTIFACT_PATTERNS = [
    '*.md',
    '.gemini/**',
    'knowledge_brain/**',
    '.changes/**',
]

# File type descriptions for clear error messages
FILE_TYPE_MAP = {
    'application/controllers/': 'controller',
    'application/models/': 'model',
    'application/views/': 'view',
    'application/libraries/': 'library',
    'application/helpers/': 'helper',
    'application/config/': 'config',
    'application/tests/': 'test',
    'assets/js/': 'JavaScript',
    'assets/css/': 'CSS/stylesheet',
    '.agent/': 'agent config',
    '.sdlc/': 'SDLC engine',
}


def find_project_root():
    """Walk up from CWD to find .git directory."""
    path = Path(os.getcwd())
    while path != path.parent:
        if (path / '.git').exists():
            return str(path)
        path = path.parent
    return os.getcwd()


def normalize_path(filepath, project_root):
    """Normalize a file path to be relative to project root with forward slashes."""
    filepath = os.path.abspath(filepath)
    project_root = os.path.abspath(project_root)

    # Make relative to project root
    try:
        rel = os.path.relpath(filepath, project_root)
    except ValueError:
        # Different drive on Windows
        rel = filepath

    # Normalize to forward slashes
    rel = rel.replace('\\', '/')

    # Remove leading ./
    if rel.startswith('./'):
        rel = rel[2:]

    return rel


def get_file_type(rel_path):
    """Get a human-readable file type description."""
    for prefix, desc in FILE_TYPE_MAP.items():
        if rel_path.startswith(prefix):
            return desc

    ext = os.path.splitext(rel_path)[1].lower()
    ext_map = {
        '.php': 'PHP source',
        '.js': 'JavaScript',
        '.css': 'stylesheet',
        '.html': 'HTML',
        '.sql': 'SQL',
        '.json': 'JSON config',
        '.md': 'documentation',
    }
    return ext_map.get(ext, 'file')


def is_artifact_path(rel_path):
    """Check if a path is an artifact (non-source documentation/notes)."""
    for pattern in ARTIFACT_PATTERNS:
        if fnmatch(rel_path, pattern):
            return True
    return False


def matches_pattern(rel_path, pattern):
    """Check if a relative path matches an allowed pattern.

    Handles:
        - Exact match: '.sdlc/pipeline.json'
        - Glob with **: 'application/controllers/**'
        - Glob with *: '*.md'
        - Descriptive strings like 'artifacts only' (treated as artifact check)
    """
    # Handle descriptive pseudo-patterns
    if 'artifacts only' in pattern.lower():
        return is_artifact_path(rel_path)

    # Normalize pattern separators
    pattern = pattern.replace('\\', '/')

    # Exact match
    if rel_path == pattern:
        return True

    # fnmatch-style matching
    if fnmatch(rel_path, pattern):
        return True

    # Handle ** recursive matching
    # 'application/controllers/**' should match 'application/controllers/billing.php'
    # and 'application/controllers/admin/billing.php'
    if '**' in pattern:
        base = pattern.replace('/**', '').replace('**/', '').replace('**', '')
        if rel_path.startswith(base):
            return True

    return False


def load_json(path):
    """Load a JSON file."""
    if not os.path.exists(path):
        return None
    with open(path, 'r', encoding='utf-8') as f:
        return json.load(f)


def get_phase_permissions(roles_data, phase, sub_role=None):
    """Get the allowed patterns for a phase (or sub-role within VIBE)."""
    file_perms = roles_data.get('file_permissions', {})

    if phase == 'VIBE' and sub_role:
        vibe_perms = file_perms.get('VIBE', {})
        sub_perms = vibe_perms.get('sub_role_permissions', {})
        role_perms = sub_perms.get(sub_role, {})
        return {
            'allowed_patterns': role_perms.get('allowed_patterns', []),
            'blocked_reason': role_perms.get('blocked_reason', f'VIBE:{sub_role} has no defined permissions.'),
        }

    phase_perms = file_perms.get(phase, {})
    if isinstance(phase_perms, dict) and phase_perms:
        return {
            'allowed_patterns': phase_perms.get('allowed_patterns', []),
            'blocked_reason': phase_perms.get('blocked_reason', f'{phase} has restricted permissions.'),
        }

    # Phase not defined — block by default (fail closed)
    return {
        'allowed_patterns': [],
        'blocked_reason': f'Phase {phase} has no defined permissions. Blocked by default.',
    }


def validate_write(filepath, phase=None, sub_role=None, project_root=None, quiet=False):
    """Validate whether a file write is allowed in the current pipeline phase.

    Returns:
        (allowed: bool, message: str)
    """
    if project_root is None:
        project_root = find_project_root()

    sdlc_dir = os.path.join(project_root, SDLC_DIR)

    # Load current state if phase not specified
    if phase is None:
        # Try to find current phase from task-specific file or registry
        registry = load_json(os.path.join(sdlc_dir, STATE_FILE))
        if registry and 'active_tasks' in registry:
            # v2.0 registry — find task state
            active = registry.get('active_tasks', [])
            if len(active) == 1:
                task_state_path = os.path.join(sdlc_dir, 'tasks', f'{active[0]["id"]}.json')
                state = load_json(task_state_path)
            elif len(active) > 1:
                # Multiple tasks — can't auto-select, use most restrictive
                phase = 'DISCUSS'
            else:
                return True, 'No active tasks — no restrictions.'
        else:
            # v1.0 format or missing
            state = registry

        if state is None and phase is None:
            return True, 'No pipeline state found — no restrictions active.'
        if phase is None:
            phase = state.get('phase', {}).get('current', 'IDLE')

        # Check for VIBE sub-role from state
        if phase == 'VIBE' and sub_role is None:
            # Try to get active sub-role from state timeline or active_subtask
            # Default to investigator (safest — read-only)
            sub_role = 'investigator'

    # Load roles
    roles = load_json(os.path.join(sdlc_dir, ROLES_FILE))
    if roles is None:
        return True, 'No roles.json found — no restrictions active.'

    # Normalize the file path
    rel_path = normalize_path(filepath, project_root)

    # Always-allowed paths
    for pattern in ALWAYS_ALLOWED:
        if matches_pattern(rel_path, pattern):
            return True, f'ALLOWED: {rel_path} is always writable.'

    # Get phase permissions
    perms = get_phase_permissions(roles, phase, sub_role)
    allowed_patterns = perms['allowed_patterns']
    blocked_reason = perms['blocked_reason']

    # Empty allowed list = nothing allowed
    if not allowed_patterns:
        file_type = get_file_type(rel_path)
        return False, (
            f'BLOCKED: {phase} phase cannot modify {file_type} files.\n'
            f'  File: {rel_path}\n'
            f'  Reason: {blocked_reason}\n'
            f'  Action: Transition to the correct phase first.'
        )

    # Check against each allowed pattern
    for pattern in allowed_patterns:
        if matches_pattern(rel_path, pattern):
            phase_label = f'{phase}:{sub_role}' if sub_role else phase
            return True, f'ALLOWED: {rel_path} matches pattern "{pattern}" in {phase_label} phase.'

    # Not matched — blocked
    file_type = get_file_type(rel_path)

    # Suggest the right phase
    suggested_phase = suggest_phase(roles, rel_path)

    return False, (
        f'BLOCKED: {phase} phase cannot modify {file_type} files.\n'
        f'  File: {rel_path}\n'
        f'  Reason: {blocked_reason}\n'
        f'  Suggestion: Transition to {suggested_phase} phase first.'
    )


def suggest_phase(roles, rel_path):
    """Suggest which phase should handle this file type."""
    file_perms = roles.get('file_permissions', {})

    for phase_name, perms in file_perms.items():
        # Skip metadata keys and special phases
        if phase_name.startswith('_') or phase_name in ('IDLE', 'VIBE'):
            continue
        # Skip non-dict entries (e.g., _description is a string)
        if not isinstance(perms, dict):
            continue
        for pattern in perms.get('allowed_patterns', []):
            if matches_pattern(rel_path, pattern):
                return phase_name

    return 'CODING'


def main():
    parser = argparse.ArgumentParser(
        description='SDLC File Permission Validator',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python .sdlc/validate_write.py application/controllers/billing.php
  python .sdlc/validate_write.py application/tests/TestBilling.php --phase TEST_DESIGN
  python .sdlc/validate_write.py assets/js/billing.js --sub-role bug_fixer
  python .sdlc/validate_write.py --check-all file1.php file2.js file3.md
        """
    )

    parser.add_argument('files', nargs='*', help='File path(s) to validate')
    parser.add_argument('--phase', default=None, help='Override phase (default: read from pipeline state)')
    parser.add_argument('--task', default=None, help='Task ID (for multi-task lookup)')
    parser.add_argument('--sub-role', default=None, help='VIBE sub-role (for VIBE phase)')
    parser.add_argument('--check-all', action='store_true', help='Check multiple files, report all results')
    parser.add_argument('--json', action='store_true', help='Output as JSON')
    parser.add_argument('--quiet', '-q', action='store_true', help='Only exit code, no message')

    args = parser.parse_args()

    if not args.files:
        parser.print_help()
        sys.exit(2)

    project_root = find_project_root()
    all_allowed = True
    results = []

    for filepath in args.files:
        allowed, message = validate_write(
            filepath,
            phase=args.phase,
            sub_role=args.sub_role,
            project_root=project_root,
            quiet=args.quiet
        )

        results.append({
            'file': filepath,
            'allowed': allowed,
            'message': message,
        })

        if not allowed:
            all_allowed = False

    # Output
    if args.json:
        print(json.dumps(results, indent=2))
    elif not args.quiet:
        for r in results:
            icon = '✅' if r['allowed'] else '❌'
            print(f'{icon} {r["message"]}')

    sys.exit(0 if all_allowed else 1)


if __name__ == '__main__':
    # Fix Windows encoding
    if sys.platform == 'win32' and hasattr(sys.stdout, 'reconfigure'):
        try:
            sys.stdout.reconfigure(encoding='utf-8', errors='replace')
            sys.stderr.reconfigure(encoding='utf-8', errors='replace')
        except Exception:
            pass
    main()
